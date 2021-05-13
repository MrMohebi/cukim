<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class createNewFood extends Controller
{
    //
    function createNewFood(Request $request)
    {

        $validatedData = Validator::make($request->all(), [
            "name" => "required",
            "group" => "required",
            "details" => "required"
        ]);

        if (!$validatedData->fails()) {


            // is token valid and has access
//            if(!(
//                $oursAccess->isTokenValid($_POST['token'], "restaurants")&&
//                $oursAccess->hasTokenAccess($_POST['token'], "restaurants", array("admin"))
//            )){
//                exit(json_encode(array('statusCode'=>401, "details"=>"token is not valid or you dont have access in this action")));
//            }
//
//            $connRes = MysqlConfig::connRes($oursAccess->select('english_name','restaurants',"`token`='".$_POST['token']."'"));
//            $resAccess = new MysqldbAccess($connRes);


            $name = trim($request->name);
            $group = trim($request->group);
            $details = $request->details;
            $price = ($request->price > 900) ? $request->price : 100000;
            $status = (strlen($request->status) > 3) ? $request->status : 'out of stock';
            $delivery_time = ($request->delivery_time > 0) ? $request->delivery_time : 0;
            $thumbnail = (strlen($request->thumbnail) > 0) ? $request->thumbnail : 'https://dl.cuki.ir/sampleAssets/sampleThumbnail_96x96.png';


            $details_array = array_values(array_filter(array_map('trim', explode("+", str_replace(array("\n", "\r"), '', $details)))));
            $details_array_str = characterFixer(json_encode($details_array));


            $groupTableFullInfo = DB::select("* from food_group WHERE `english_name`='$group");

            if (count($groupTableFullInfo) < 2){
                exit(json_encode(array('statusCode' => 400, "details" => "group name is not available")));
            }

            if (
            DB::table("food_group")->where('english_name', $group)->exists()
            ) {
                return response(["details" => "food is duplicate", 'statusCode' => 402], 401);
            }

            if (
                DB::table('food_group')->insert([
                    "name" => $name,
                    "group" => $group,
                    "details" => $details_array_str,
                    "price" => $price,
                    "status" => $status,
                    "order_times" => 0,
                    "discount" => 0,
                    "delivery_time" => $delivery_time,
                    "thumbnail" => $thumbnail,
                    "modified_date" => time(),
                ])
            ) {
                return response(['statusCode' => 200],200);
            } else {
                return response(["message" => "some thing went wrong during creating new food on server",'statusCode' => 500],500);
            }

        } else {
            return response(array( 'message' => "wrong inputs!",'statusCode' => 400),400);
        }

        function characterFixer($str)
        {
            return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
                return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
            }, $str);
        }
    }
}
