<?php

namespace App\Http\Controllers\api\admin\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\DatabaseNames\DN;


class invoice extends Controller{
    private const commission = 1 - (6.42 / 100); // in percentage


    public function createInvoice(Request $request){
        $validator = Validator::make($request->all(), [
            'resEnglishName' => "required|min:3",
        ]);

        if ($validator->fails())
            return response(["message" => $validator->errors()->all(), "statusCode" => 400], 400);

        $details = $request->input("details");
        $resEnglishName = $request->input("resEnglishName");

        $admin = DB::table(DN::tables["ADMINS"])->where(DN::ADMINS["token"], $request->input("token"))->first();
        $paymentKey = DB::table(DN::tables["RESTAURANTS"])->where(DN::RESTAURANTS["eName"], $resEnglishName)->value(DN::RESTAURANTS["paymentKey"]);

        if (strlen($paymentKey) < 2)
            return response(["message" => "restaurant wasn't found", "statusCode" => 404]);

        $lastResPaymentRecord = DB::table(DN::tables["INVOICES"])->where(DN::INVOICES["paymentKey"], $paymentKey)->orderBy("id", "desc")->first();
        $lastResPaymentRecord = isset($lastResPaymentRecord->id) ? $lastResPaymentRecord : self::createLastResPaymentRecordStdClassEmpty();
        $isThereOpenReceipt = ($lastResPaymentRecord->{DN::INVOICES["status"]} ?? "") == "created-notPaid";

        // check if there is no receipt
        if ($isThereOpenReceipt)
            return response([
                "message" => "there is an open restaurant receipt. First pay it!",
                "data" => [
                    'receiptId' => $lastResPaymentRecord->id,
                    'resEnglishName' => $resEnglishName,
                    'toPay' => $lastResPaymentRecord->{DN::INVOICES["toPay"]},
                    'status' => $lastResPaymentRecord->{DN::INVOICES["status"]},
                    'totalOnlineIncomeAllTime' => $lastResPaymentRecord->{DN::INVOICES["onlineTillNow"]},
                    'totalCashIncomeAllTime' => $lastResPaymentRecord->{DN::INVOICES["offlineTillNow"]},
                    'totalOnlineIncomeFromLastInvoiceTillNow' => $lastResPaymentRecord->{DN::INVOICES["onlineFromPrevious"]},
                    'totalCashIncomeFromLastInvoiceTillNow' => $lastResPaymentRecord->{DN::INVOICES["offlineFromPrevious"]},
                ],
                "statusCode" => 402]);


        $totalOnlineIncomeAllTime = self::calculateTOnlineIncomeTillNow($lastResPaymentRecord);
        $totalCashIncomeAllTime = self::calculateTOrdersIncomeTillNow()-$totalOnlineIncomeAllTime;
        $totalOnlineIncomeFromLastSettlement = self::calculateTOnlineIncomeFromLastSettlement($lastResPaymentRecord)+0;
        $totalIncomeFromLastSettlement = self::calculateTOrdersIncomeFromLastSettlement($lastResPaymentRecord);
        $totalCashIncomeFromLastSettlement = $totalIncomeFromLastSettlement-$totalOnlineIncomeFromLastSettlement;


        // check if receipt online(not pay) is not zero
        if ($totalIncomeFromLastSettlement <= 100)
            return response([
                "message" => "there is no unpaid bill",
                "data" => [
                    'resEnglishName' => $resEnglishName,
                    'totalOnlineIncomeAllTime' => $totalOnlineIncomeAllTime,
                    'totalCashIncomeAllTime' => $totalCashIncomeAllTime,
                    'totalOnlineIncomeFromLastSettlementTillNow' => $totalOnlineIncomeFromLastSettlement,
                    'totalCashIncomeFromLastSettlementTillNow' => $totalCashIncomeFromLastSettlement,
                ],
                "statusCode" => 408]);


        $status = "created-notPaid";
        $creatorSupportName = $admin->{DN::ADMINS["name"]};
        $creatorSupportId = $admin->id;
        $toPayAmount = ceil($totalIncomeFromLastSettlement * (self::commission));


        $saveNewInvoice = array(
            DN::INVOICES["resEName"]=>$resEnglishName,
            DN::INVOICES["paymentKey"]=>$paymentKey,
            DN::INVOICES["details"]=>$details,
            DN::INVOICES["toPay"]=>$toPayAmount,
            DN::INVOICES["onlineTillNow"]=>$totalOnlineIncomeAllTime,
            DN::INVOICES["offlineTillNow"]=>$totalCashIncomeAllTime,
            DN::INVOICES["onlineFromPrevious"]=>$totalOnlineIncomeFromLastSettlement,
            DN::INVOICES["offlineFromPrevious"]=>$totalCashIncomeFromLastSettlement,
            DN::INVOICES["status"]=>$status,
            DN::INVOICES["creatorSupportName"]=>$creatorSupportName,
            DN::INVOICES["creatorSupportId"]=>$creatorSupportId,
            DN::CA=>time(),
            DN::UA=>time(),
        );

        if(DB::table(DN::tables["INVOICES"])->insert($saveNewInvoice)){
            return response([
                "data" => [
                    'resEnglishName' => $resEnglishName,
                    'status' => $status,
                    'toPay' => $toPayAmount,
                    'totalOnlineIncomeAllTime' => $totalOnlineIncomeAllTime,
                    'totalCashIncomeAllTime' => $totalCashIncomeAllTime,
                    'totalOnlineIncomeFromLastSettlementTillNow' => $totalOnlineIncomeFromLastSettlement,
                    'totalCashIncomeFromLastSettlementTillNow' => $totalCashIncomeFromLastSettlement,
                    ],
                "statusCode" => 200]);
        }else{
            return response([
                "message" => "some thing went wrong during saving record",
                "statusCode" => 500]);
        }
    }

    private static function createLastResPaymentRecordStdClassEmpty(): \stdClass{
        $emptyStd = new \stdClass();
        $emptyStd->{DN::CA} = 0;
        $emptyStd->{DN::INVOICES["status"]} = "";
        $emptyStd->{DN::INVOICES["onlineTillNow"]} = 0;
        return $emptyStd;
    }

    private static function calculateTOnlineIncomeTillNow($lastRPRecord){
        $lastRDate = $lastRPRecord->{DN::CA} > 100 ? $lastRPRecord->{DN::CA} : 0;
        $sum = DB::table(DN::tables["PAYMENTS"])->where(DN::PAYMENTS["verifiedAt"], ">=", $lastRDate)->sum(DN::PAYMENTS["amount"]);
        return $lastRPRecord->{DN::INVOICES["onlineTillNow"]} + $sum;
    }

    function calculateTOrdersIncomeTillNow(){
        return DB::connection("resConn")->table(DN::resTables["resORDERS"])->where(DN::resORDERS["status"],"!=","deleted")->sum(DN::resORDERS["tPrice"]);
    }

    function calculateTOnlineIncomeFromLastSettlement($lastRPRecord){
        $lastRDate = $lastRPRecord->{DN::CA} > 100 ? $lastRPRecord->{DN::CA} : 0;
        return DB::table(DN::tables["PAYMENTS"])->where(DN::PAYMENTS["verifiedAt"], ">=", $lastRDate)->sum(DN::PAYMENTS["amount"]);
    }

    function calculateTOrdersIncomeFromLastSettlement($lastRPRecord){
        $lastRDate = $lastRPRecord->{DN::CA} > 100 ? $lastRPRecord->{DN::CA} : 0;
        return DB::connection("resConn")->table(DN::resTables["resORDERS"])->where([[DN::CA, ">=",$lastRDate],[DN::resORDERS["status"],"!=","deleted"]])->sum(DN::resORDERS["tPrice"]);
    }

}
