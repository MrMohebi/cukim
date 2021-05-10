<?php


namespace App\CustomClasses\Ipg;
use App\CustomClasses\AbstractClasses\Ipg;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\CustomFunctions\CusStFunc;

class Payping extends Ipg{

    protected static $URLCreatePayment = "https://api.payping.ir/v2/pay";
    protected static $URLPaymentLinkBase = "https://api.payping.ir/v2/pay/gotoipg/";
    protected static $URLVerifyPayment = "https://api.payping.ir/v2/pay/verify";
    protected static $URLReturnIPG = "https://cukim.ir/api/v1/pay/NOT_SET_YET";


    static public function createPaymentLink(string $resEnglishName, string $userToken, string $trackingId, int $amount, string $itemType, array $items):array{
        $resData = DB::table("restaurants")
            ->where('english_name', $resEnglishName)
            ->first();


        // check user is valid and get it's phone and name
        $userInfo = DB::table("users")->where("token", $userToken)->first();
        $costumer_phone = $userInfo['phone'];
        $costumer_name = $userInfo['name'];
        if(strlen($costumer_phone) != 11)
            return array('statusCode' => 401, "massage" => "user is not valid");


        // check restaurant is correct
        if (strlen($resData['payment_key']) < 2)
            return array('statusCode' => 400, "massage" => "restaurant wasn't found");


        $paymentIdType = "x";
        if($itemType == "food"){
            $paymentIdType = "f";
            $foodsFullInfo = self::getFoodInfo($items);
            $items = $foodsFullInfo;
        }



        $previousPaidInfo = self::getPreviousPaidInfo($trackingId);
        if($previousPaidInfo['wasPaidTotal'])
            return array('statusCode'=>600, "massage" => "all ware paid");

        $paymentLastNum = $previousPaidInfo['paymentLastNum'];

        $paymentNum = (($paymentLastNum > 0) ? ($paymentLastNum+1) : 1 );
        $paymentBaseId = (strlen($previousPaidInfo['paymentBaseId']) > 5) ? $previousPaidInfo['paymentBaseId'] : ("cuki".$paymentIdType."-".$resData["payment_key"] ."-".CusStFunc::randomStringLower(4));

        $paymentId = $paymentBaseId ."-". $paymentNum;

        // for test:
        if($resData["english_name"] == "cuki"){
            $amount = 1000;
        }


        $api_key = $resData['ipg_token'];

        $info_params = array(
            "amount" => $amount,
            "payerIdentity" => $costumer_phone,
            "payerName" => $costumer_name,
            "description"=>"",
            "clientRefId"=>$paymentId,
            "returnUrl" => self::$URLReturnIPG,
        );


        $result = Http::withToken($api_key)->post(self::$URLCreatePayment,$info_params);

        $payPingCode = $result['code'];

        $sqlInsert_createPaymentParams = array(
            'ipg'=>"payping",
            'tracking_id'=>$trackingId,
            'payment_id'=>$paymentId,
            'payment_group'=>$paymentBaseId,
            'payment_num'=>$paymentNum,
            'payment_key'=>$resData["payment_key"],
            'item_type'=>$itemType,
            'item'=>$items,
            'payer_phone'=>$costumer_phone,
            'payer_name'=>$costumer_name,
            'amount'=>$amount,
            'status'=>'0',
        );


        if(DB::table("payments")->insert($sqlInsert_createPaymentParams)){
            if(strlen($payPingCode) > 2){
                if(DB::table("payments")->where("payment_id",$paymentId)->update(array('payping_code'=>$payPingCode))){
                    return array(
                        'statusCode'=>200,
                        "data"=>array(
                            "url"=>self::$URLPaymentLinkBase.$payPingCode,
                            "amount"=>$amount,
                            "paymentId"=>$paymentId,
                            "totalPaid"=>$previousPaidInfo['paidSum'],
                            "totalPrice"=>$previousPaidInfo['totalPrice'],
                        ));
                }else{
                    return array('statusCode'=>500);
                }
            }else{
                return array('statusCode'=>408, "massage" => "something went wrong during getting payment link");
            }
        }else{
            return array('statusCode'=>500, "massage" => "something went wrong during saving payment in our database");
        }
    }


    protected static function getFoodInfo($foods_list):array{
        $orderedFood = array();

        $all_foods = DB::connection("resConn")->table("foods")->get();

        foreach ($foods_list as $eachOrderedFood){
            foreach ($all_foods as $eachFood){
                if ($eachOrderedFood['id'] == $eachFood['foods_id']) {
                    $priceAfterDiscount = $eachFood['price'] * ((100 - $eachFood['discount'])/100);
                    $eachOrderedFood_newArray = array(
                        'id'=>$eachOrderedFood['id'],
                        'name'=>$eachFood['name'],
                        'number'=>$eachOrderedFood['number'],
                        'price'=>$eachFood['price'],
                        'discount'=>$eachFood['discount'],
                        'priceAfterDiscount'=>$priceAfterDiscount
                    );
                    array_push($orderedFood, $eachOrderedFood_newArray);
                }
            }
        }
        return $orderedFood;
    }

    protected static function getPreviousPaidInfo($trackingId):array{
        $payments = DB::table("payments")->where('tracking_id',$trackingId);
        $lastPaymentNum = $payments->max('payment_num');
        $paymentBaseId = $payments->first()->value('payment_group');
        $paidSum = $payments->where('verified_date', ">", "100")->sum('amount');


        // get order info
        $orderInfo = DB::connection("resConn")
            ->table("orders")
            ->where('tracking_id', $trackingId)
            ->first();

        // check if order was paid dont open new payment
        $wasPaidTotal = false;
        if($orderInfo['total_price'] <= $paidSum)
            $wasPaidTotal = true;

        return array(
            "wasPaidTotal"=>$wasPaidTotal,
            "paidSum"=>$paidSum,
            "totalPrice"=>$orderInfo['total_price'],
            'paymentBaseId'=>$paymentBaseId,
            'paymentLastNum'=>$lastPaymentNum
        );
    }



}
