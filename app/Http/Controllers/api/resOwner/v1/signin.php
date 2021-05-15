<?php

namespace App\Http\Controllers\api\resOwner\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\CustomFunctions\CusStFunc;

class signin extends Controller
{
    public function signin(Request $request){
        $validator = Validator::make($request->all(),[
            "username"=>"required|min:3",
            "password"=>"required|min:8",
        ]);
        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],"400");

        $resOwnerInfo = DB::table("res_owners")->where("username",$request->input("username"))->first();



        if(password_verify($request->input("password"),$resOwnerInfo->password)){
            unset($resOwnerInfo->password);
            unset($resOwnerInfo->verification_code);
            unset($resOwnerInfo->verification_code_tries);
            return response(["data"=>CusStFunc::arrayKeysToCamel(json_decode(json_encode($resOwnerInfo),true)), "statusCode"=>200],"200");
        }else{
            return response(["massage"=>"username or password are incorrect", "statusCode"=>401],"401");
        }
    }
}
