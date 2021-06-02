<?php

namespace App\Http\Controllers\api\cuki\v1;

use App\DatabaseNames\DN;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class payment extends Controller
{
    public function getPaymentByTrackingId(Request $request){
        $validator = Validator::make($request->all(),[
            "trackingId"=>"required|min:8"
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],400);

        $trackingId =  $request->input("trackingId");

        $user = DB::table(DN::tables["USERS"])->where(DN::USERS["token"], $request->input("token"));


        $paymentsInfo = json_decode(json_encode(DB::table(DN::tables["PAYMENTS"])
            ->where([
                [DN::PAYMENTS["trackingId"],$trackingId],
                [DN::PAYMENTS["payerPhone"], $user->value(DN::USERS["phone"])]
                ])
            ->get()), true);

        if($paymentsInfo){
            if(isset($paymentsInfo["id"]))
                $paymentsInfo = array($paymentsInfo);
            $result = array();
            foreach ($paymentsInfo as $eachPay){
                array_push($result,array(
                    "trackingId"=>$eachPay[DN::PAYMENTS["trackingId"]],
                    'paymentId'=>$eachPay[DN::PAYMENTS["paymentId"]],
                    "payerPhone"=>$eachPay[DN::PAYMENTS["payerPhone"]],
                    "paidDate"=>$eachPay[DN::PAYMENTS["verifiedAt"]],
                    "isPaid"=> $eachPay[DN::PAYMENTS["verifiedAt"]] > 1000,
                    "amount"=>$eachPay[DN::PAYMENTS["amount"]],
                    "itemType"=>$eachPay[DN::PAYMENTS["itemType"]],
                    "item"=>$eachPay[DN::PAYMENTS["item"]],
                    "status"=>$eachPay[DN::PAYMENTS["status"]],
                ));
            }
            return response(array('statusCode'=>200, "data"=>$result));

        }else{
            return response(["massage"=>"no payment was found!", "statusCode"=>404],200);
        }

    }
}
