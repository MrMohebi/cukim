<?php

namespace App\Http\Controllers\api\resOwner\v1;

use App\CustomFunctions\CusStFunc;
use App\DatabaseNames\DN;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\CustomClasses\Ipg\Payping;

class signup extends Controller
{
    public function signup(Request $request){
        $validator = Validator::make($request->all(),[
            "username"=>"required|min:3",
            "password"=>"required|min:8",
            "name"=>"required|min:3"
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],"400");

        // check duplication
        if(
            DB::table("res_owners")->where("username",$request->input("username"))->exists()
        ){
            return response(["massage"=>'username is duplicated', "statusCode"=>400],"400");
        }
        $hashed_password = password_hash($request->input("password"), PASSWORD_DEFAULT);

        $insertNewOwner = [
            'username'=>$request->input("username"),
            'password'=>$hashed_password,
            'name'=>$request->input("name"),
            'token'=>CusStFunc::randomStringLower(64),
            DN::CA=>time(),
        ];

        if(DB::table("res_owners")->insert($insertNewOwner)){
            return response(["statusCode"=>200],200);
        }
        return response(["massage"=>"something went wrong", "statsCode"=>500],500);
    }

    public function signupAndBuyPlan(Request $request){
        $validator = Validator::make($request->all(),[
            "username"=>"required|min:3",
            "planId"=>"required",
            "name"=>"required|min:3",
            "resPersianName"=>"required",
            "resEnglishName"=>"required",
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],"400");

        // check duplication
        if(
        DB::table("restaurants")->where("english_name",$request->input("resEnglishName"))->exists()
        ){
            return response(["massage"=>'username or resEnglishName is duplicated', "statusCode"=>400],"400");
        }

        // if user exist and doesn't have active res delete it
        if($tUser = DB::table("res_owners")->where("username",$request->input("username"))){
            if(!DB::table(DN::tables["RESTAURANTS"])->where(DN::RESTAURANTS["ownerId"], $tUser->value("id"))->exists()){
                DB::table(DN::tables["TEMP_RES_NAMES"])->where(DN::TEMP_RES_NAMES["resOwnerId"], $tUser->value("id"))->delete();
                $tUser->delete();
            }
        }


        $trackingId = rand(11111111,99999999);

        $hashed_password = password_hash($trackingId, PASSWORD_DEFAULT);
        $token = CusStFunc::randomStringLower(64);
        $insertNewOwner = [
            DN::RES_OWNERS["username"]=>$request->input("username"),
            DN::RES_OWNERS["password"]=>$hashed_password,
            DN::RES_OWNERS["phone"]=>$request->input("username"),
            DN::RES_OWNERS["name"]=>$request->input("name"),
            DN::RES_OWNERS["token"]=>$token,
            DN::CA=>time(),
        ];

        $userId = DB::table("res_owners")->insertGetId($insertNewOwner);

        DB::table(DN::tables["TEMP_RES_NAMES"])
            ->insert([
                DN::TEMP_RES_NAMES["resOwnerId"]=>$userId,
                DN::TEMP_RES_NAMES["pName"]=>$request->input("resPersianName"),
                DN::TEMP_RES_NAMES["eName"]=>$request->input("resEnglishName"),
            ]);

        $paymentData = Payping::createOurPaymentLink("plan", [$request->input("planId")], $token, $trackingId);
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
        DB::table("res_owners")->delete($userId);
        return response(["massage"=>"something went wrong","Test"=>$paymentData, "statsCode"=>500]);
    }
}














