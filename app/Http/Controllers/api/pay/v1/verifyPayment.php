<?php

namespace App\Http\Controllers\api\pay\v1;

use App\CustomClasses\Ipg\Payping;
use App\DatabaseNames\DN;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class verifyPayment extends Controller
{
    public function verify(Request $request){
        $validator = Validator::make($request->all(), [
            "code" => "required",
            "refid" => "required",
            "clientrefid" => "required|min:8",
            "cardnumber" => "",
            "cardhashpan" => "",
        ]);

        if ($validator->fails())
            return response(array( 'message' => $validator->errors()->all(),'statusCode' => 400),400);

        $code = $request->input("code");
        $refid = $request->input("refid");
        $clientrefid = $request->input("clientrefid");
        $cardnumber = $request->input("cardnumber");
        $cardhashpan = $request->input("cardhashpan");

        $paymentKey = explode("-",$clientrefid)[1];
        $res = DB::table(DN::tables["RESTAURANTS"])->where(DN::RESTAURANTS["paymentKey"], $paymentKey);

        $linkData = array( 'message' => "something went wrong",'statusCode' => 500);

        switch ($res->value(DN::RESTAURANTS["ipgName"])){
            case "payping":
                $linkData = Payping::verifyPayment($code, $refid, $clientrefid, $cardnumber, $cardhashpan);
                return redirect($linkData["url"]) ;
        }

        return response($linkData);
    }

}
