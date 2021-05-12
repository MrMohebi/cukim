<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class createCategory extends Controller
{
    function createCategory (Request $request){
            $validatedData = Validator::make($request,[
                "catPersianName"=>'min:2|required',
                "catEnglishName"=>'min:2|required',
                "logo"=>'required|min:2'
            ]);


        if(!$validatedData->fails()){

//            $connOurs = MysqlConfig::connOurs();
//            $oursAccess = new MysqldbAccess($connOurs);

            // is token valid and has access
//            if(!(
//                $oursAccess->isTokenValid($_POST['token'], "restaurants")&&
//                $oursAccess->hasTokenAccess($_POST['token'], "restaurants", array("admin"))
//            )){
//                exit(json_encode(array('statusCode'=>401, "details"=>"token is not valid or you dont have access in this action")));
//            }

            $catPersianName = $request->catPersianName;
            $catEnglishName = $request->catEnglishName;
            $logo = $request->logo;
            $rank = $request->rank;
            $type = $request->type;
            $resEnglishName = $request->resEnglishName;
            $averageColor = $request->averageColor;
//
//            $flag_duplicate = $oursAccess->noDuplicate(array(
//                "persian_name"=>$catPersianName,
//                "english_name"=>$catEnglishName,
//            ), "food_group");

            if (DB::table('food_group')->where('persian_name',$catPersianName)->exists() &&DB::table('category')->where('english_name',$catEnglishName)->exists() )
                return response([ "details"=>"some of info are duplicate",'statusCode'=>402],402);


            if (DB::table('food_group')->insert([
                "persian_name"=>$catPersianName,
                "english_name"=>$catEnglishName,
                "logo"=>$logo,
                "status"=>"active",
                "type"=>$type,
                "res_english_name"=>$resEnglishName,
                "average_color"=>$averageColor
            ])){
                return response(['statusCode'=>200],200);

            }else{
                return response(["message"=>"some thing went wrong during creating new category on server",'statusCode'=>500],500);
            }

        }else{
            return response(['message'=>"wrong inputs",'statusCode'=>400],400);
        }
    }
}
