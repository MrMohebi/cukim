<?php

namespace App\Http\Controllers\api\resOwner\v1;

use App\CustomFunctions\CusStFunc;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
            'token'=>CusStFunc::randomStringLower(64)
        ];

        if(DB::table("res_owners")->insert($insertNewOwner)){
            return response(["statusCode"=>200],200);
        }
        return response(["massage"=>"something went wrong", "statsCode"=>200],200);
    }
}














