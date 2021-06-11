<?php

namespace App\Http\Controllers\api\resOwner\v1;

use App\CustomClasses\Ipg\Payping;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class resOwnerBuy extends Controller
{
    public function buyPlan(Request $request){

        $validator = Validator::make($request->all(), [
            'planId' => "required",
        ]);

        if ($validator->fails())
            return response(["massage" => $validator->errors()->all(), "statusCode" => 400], "400");

        $trackingId = rand(11111111,99999999);
        $paymentData = Payping::createOurPaymentLink("plan", [$request->input("planId")], $request->input("token"), $trackingId);

        if(isset($paymentData["statusCode"]) && $paymentData["statusCode"] == 200){
            return response([
                "data"=>[
                    "url"=>$paymentData["data"]["url"],
                    "amount"=>$paymentData["data"]["amount"],
                    "trackingId"=>$trackingId,
                ],
                "statusCode"=>200
            ]);
        }
        return response(["massage"=>"something went wrong", "statsCode"=>500]);

    }
}
