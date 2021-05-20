<?php

namespace App\Http\Controllers\api\cuki\v1;

use App\CustomFunctions\CusStFunc;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ghasedak\GhasedakApi;
use App\DatabaseNames\DN;


class user extends Controller
{
    public function sendVCode(Request $request){
        $validator = Validator::make($request->all(),[
            'phone'=>"required|size:11|starts_with:09",
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],"400");

        $phone = $request->input("phone");
        $smsApi = new GhasedakApi(env('GHASEDAKAPI_KEY'));

        $code = rand(1000,9999);

        $user = DB::table(DN::tables["USERS"])
            ->where(DN::USERS["phone"], $phone);

        // create new user if it doesnt exist
        if(!$user->exists()){
            $insertNewUserParams = [
                DN::USERS["phone"]=> $phone,
                DN::CA=> Carbon::now()->timestamp,
            ];
            DB::table(DN::tables["USERS"])->insert($insertNewUserParams);
        }

        $user->update([
           DN::USERS["vCode"]=>$code,
           DN::USERS["vCodeTries"]=>0,
           DN::UA=> Carbon::now()->timestamp,
        ]);


        if($smsApi->Verify($phone,1,'loginfoodusers',$code)){
            return response([
                "data"=> [
                    "phone"=> $phone
                ],
                "statusCode"=> 200]);
        }
        return response(["massage"=>"server error", "statusCode"=>500],"500");
    }

    public function verifyVCode(Request $request){
        $validator = Validator::make($request->all(),[
            'phone'=>"required|size:11|starts_with:09",
            "vCode"=>'required|size:4',
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],400);

        $phone = $request->input("phone");
        $vCode = (int)$request->input("vCode");

        $user = DB::table(DN::tables["USERS"])
            ->where(DN::USERS["phone"], $phone);

        // dont let attacker to find whom use ur service
        if(!$user->exists())
            return response(["massage"=>"code is not correct", "statusCode"=>401],401);

        if($user->value(DN::USERS["vCode"]) === $vCode){
            $token = CusStFunc::randomStringLower(32);
            $user->update([
                DN::USERS["vCode"]=>null,
                DN::USERS["vCodeTries"]=>0,
                DN::USERS["token"]=>$token,
                DN::UA=> Carbon::now()->timestamp,
            ]);
            return response(["data"=>[
                "phone"=>$phone,
                "token"=>$token,
                "isInfoComplete"=> strlen($user->value("name")) > 2,
            ],'statusCode'=>200]);
        }else{
            $user->increment(DN::USERS["vCodeTries"]);
            if($user->value(DN::USERS["vCodeTries"]) > 15)
                return response(["massage"=>"too many tries, please request new code", "statusCode"=>429],"429");

            return response(["massage"=>"code is not correct", "statusCode"=>401],"401");
        }

    }

}
