<?php

namespace App\Http\Controllers\api\pay\v1;

use App\DatabaseNames\DN;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CustomClasses\Ipg\Payping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class createLink extends Controller
{
    public function createLink(Request $request){
        $validator = Validator::make($request->all(), [
            "resEnglishName" => "required|min:3",
            "token" => "required",
            "trackingId" => "required|min:8",
            "amount" => "required|numeric",
            "itemType" => "required",
            "items" => "required",
        ]);

        if ($validator->fails())
            return response(array( 'message' => $validator->errors()->all(),'statusCode' => 400),400);

        $resEnglishName = $request->input("resEnglishName");
        $userToken = $request->input("token");
        $trackingId = $request->input("trackingId");
        $amount = $request->input("amount");
        $itemType = $request->input("itemType");
        $items = json_decode($request->input("items"),true);

        $res = DB::table(DN::tables["RESTAURANTS"])->where(DN::RESTAURANTS["eName"], $resEnglishName);

        $linkData = array( 'message' => "something went wrong",'statusCode' => 500);

        switch ($res->value(DN::RESTAURANTS["ipgName"])){
            case "payping":
                $linkData = Payping::createPaymentLink($resEnglishName, $userToken, $trackingId, $amount, $itemType, $items);
                break;
        }

        return response($linkData, $linkData["statusCode"]);
    }
}
