<?php

namespace App\Http\Controllers\api\resOwner\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\CustomFunctions\CusStFunc;

class res extends Controller
{
    public function createNewRes(Request $request){

        $validator = Validator::make($request->all(),[
            'username'=>"required|min:3",
            'password'=>"required|min:8",
            'persianName'=>"required",
            'englishName'=>"required",
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],"400");


        // check duplication
        if(
            DB::table("restaurants")->where("username",$request->input("username"))->exists() ||
            DB::table("restaurants")->where("english_name",$request->input("englishName"))->exists()
        ){
            return response(["massage"=>'username or englishName are duplicated', "statusCode"=>400],"400");
        }

        $ownerInfo = DB::table("res_owners")->where("token",$request->input("token"))->first();

        $hashed_password = password_hash($request->input("password"), PASSWORD_DEFAULT);
        $dbName = $request->input("englishName") . "_res";
        $paymentKey = self::generatePaymentKey($request->input("englishName"));

        $insertNewResParams = array(
            "username"=>$request->input("username"),
            "password"=>$hashed_password,
            "persian_name"=>$request->input("persianName"),
            "english_name"=>$request->input("englishName"),
            "db_name"=>$dbName,
            'token'=>CusStFunc::randomStringLower(64),
            "payment_key"=>$paymentKey,
            "position"=>"admin",
            "owner_id"=>$ownerInfo["id"],
            "owner_name"=>$ownerInfo["name"],
            "res_code"=>DB::table("restaurants")->get()->last()->id + 10,
        );
        if(!DB::table("restaurants")->insert($insertNewResParams)){
            return response(["massage"=>"something went wrong during create restaurant","statusCode"=>500],500);
        }


        // create database and tables
        if(!DB::statement(DB::raw('CREATE DATABASE ' . $dbName))){
            return response(["massage"=>"something went wrong during create restaurant","statusCode"=>500],500);
        }
        Config::set('database.connections.resConn.database', $dbName);
        if(!Artisan::call("migrate --database=resConn --path=database/migrations/res")){
            return response(["massage"=>"something went wrong during create restaurant","statusCode"=>500],500);
        }

        
        // add res info row
        DB::connection("resConn")->table("info")->insert(['english_name'=>$request->input("englishName"), "persian_name"=>$request->input("persianName")]);


        return response(["statusCode"=>200]);
    }

    protected static function generatePaymentKey($englishName):string{
        $paymentKey = substr($englishName, 0,2);
        while (DB::table("restaurants")->where('payment_key',$paymentKey)->exists()){
            $paymentKey = CusStFunc::randomAlphabetStringLower(2);
        }
        return $paymentKey;
    }
}
