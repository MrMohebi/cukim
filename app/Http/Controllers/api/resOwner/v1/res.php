<?php

namespace App\Http\Controllers\api\resOwner\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\CustomFunctions\CusStFunc;
use App\DatabaseNames\DN;

class res extends Controller
{
    public function createNewRes(Request $request){

        $validator = Validator::make($request->all(),[
            'username'=>"required|min:3",
            'password'=>"required|min:8",
            'persianName'=>"required",
            'englishName'=>"required",
            "planId"=>"required"
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],"400");


        $resEName = str_replace('/\s+/', "_", $request->input("englishName"));

        // check english name is correct
        if(preg_match('/[^A-Za-z0-9_]/', $resEName))
            return response(["massage"=>"english name should be english letter or number", "statusCode"=>400],"400");

        // check duplication
        if(
            DB::table("restaurants")->where("username",$request->input("username"))->exists() ||
            DB::table("restaurants")->where("english_name",$resEName)->exists()
        ){
            return response(["massage"=>'username or englishName are duplicated', "statusCode"=>400],"400");
        }

        $ownerInfo = DB::table("res_owners")->where("token",$request->input("token"))->first();

        $hashed_password = password_hash($request->input("password"), PASSWORD_DEFAULT);
        $dbName = 'cuki_'.$resEName . "_res";
        $paymentKey = self::generatePaymentKey($resEName);

        $plan = DB::table(DN::tables["PLANS"])->find($request->input("planId"));
        $permissions = array_map(function($eItem){return $eItem["englishName"];},json_decode($plan->items,true));


        $insertNewResParams = array(
            "username"=>$request->input("username"),
            "password"=>$hashed_password,
            "persian_name"=>$request->input("persianName"),
            "english_name"=>$resEName,
            "db_name"=>$dbName,
            'token'=>CusStFunc::randomStringLower(64),
            "payment_key"=>$paymentKey,
            "position"=>"admin",
            "owner_id"=>$ownerInfo->id,
            "owner_name"=>$ownerInfo->name,
            DN::CA => time(),
            DN::UA=> time(),
        );
        if(!DB::table("restaurants")->insert($insertNewResParams)){
            return response(["massage"=>"something went wrong during create restaurant","statusCode"=>500],500);
        }


        // create database and tables
        if(!DB::statement(DB::raw('CREATE DATABASE ' . $dbName))){
            return response(["massage"=>"something went wrong during create restaurant","statusCode"=>500],500);
        }
        Config::set('database.connections.resConn.database', $dbName);
        Artisan::call("migrate --database=resConn --path=database/migrations/res");


        // add res code
        DB::table("restaurants")
            ->where('db_name',$dbName)
            ->update([
                DN::RESTAURANTS["code"]=>DB::table("restaurants")->get()->last()->id + 10,
                DN::RESTAURANTS["permissions"]=>json_encode($permissions)
            ]);


        // add res info row
        DB::connection("resConn")
            ->table("info")
            ->insert([
                    'english_name'=> $request->input("englishName"),
                    "persian_name"=>$request->input("persianName"),
                    "open_time"=>'[[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]]',
                    DN::CA => time(),
                    DN::UA=> time(),
                ]);


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
