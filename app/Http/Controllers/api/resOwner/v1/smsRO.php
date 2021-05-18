<?php

namespace App\Http\Controllers\api\resOwner\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Ghasedak\GhasedakApi;
use App\DatabaseNames\DN;

class smsRO extends Controller
{
    public function sendVCode(Request $request){
        $validator = Validator::make($request->all(),[
            "token"=>"required|min:10",
            "phone"=>"required|size:11",
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],"400");


        $phone = $request->input("phone");
        $token = $request->input("token");
        $smsApi = new GhasedakApi(env('GHASEDAKAPI_KEY'));

        $code = rand(1000,9999);

        if(DB::table(DN::tables['RES_OWNERS'])->where(DN::RES_OWNERS["token"], $token)->value(DN::RES_OWNERS['vCodeTries']) == -1)
            return response(["massage"=>"phone is verified", "statusCode"=>409],"409");


        DB::table(DN::tables['RES_OWNERS'])
            ->where(DN::RES_OWNERS["token"], $token)
            ->update([
                DN::RES_OWNERS["vCode"]=> $code,
                DN::RES_OWNERS["phone"]=> $phone,
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
            "token"=>"required|min:10",
            "vCode"=>"required|size:4"
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],"400");

        $token = $request->input("token");

        if((DB::table(DN::tables['RES_OWNERS'])->where(DN::RES_OWNERS["token"], $token)->value(DN::RES_OWNERS['vCodeTries'])) == -1)
            return response(["massage"=>"phone is verified", "statusCode"=>409],"409");

        $code = DB::table(DN::tables["RES_OWNERS"])->where(DN::RES_OWNERS["token"], $token)->value(DN::RES_OWNERS["vCode"]);
        if($request->input("vCode") == $code){
            DB::table(DN::tables["RES_OWNERS"])->where(DN::RES_OWNERS["token"], $token)->update([DN::RES_OWNERS["vCode"]=>-1, DN::RES_OWNERS["vCodeTries"]=>-1]);
            return response(["statusCode"=>200]);
        }else{
            DB::table(DN::tables["RES_OWNERS"])->where(DN::RES_OWNERS["token"], $token)->increment(DN::RES_OWNERS["vCodeTries"]);
            $tries = DB::table(DN::tables["RES_OWNERS"])->where(DN::RES_OWNERS["token"], $token)->value(DN::RES_OWNERS["vCodeTries"]);
            if($tries > 15){
                DB::table(DN::tables["RES_OWNERS"])->where(DN::RES_OWNERS["token"], $token)->update([DN::RES_OWNERS["vCode"]=>null, DN::RES_OWNERS["vCodeTries"]=>0]);
                return response(["massage"=>"too many tries, please request new code", "statusCode"=>429],"429");
            }
            return response(["massage"=>"code is not correct", "statusCode"=>401],"401");
        }

    }

}
