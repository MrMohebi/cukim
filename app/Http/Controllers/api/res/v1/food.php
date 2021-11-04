<?php

namespace App\Http\Controllers\api\res\v1;

use App\CustomFunctions\CusStFunc;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\DatabaseNames\DN;
use Intervention\Image\Facades\Image;


class food extends Controller{

    const resIMGPath = '/var/www/dl.cuki.ir/resimg/';
    const dlURL = "https://dl.cuki.ir/";



    function createNewFood(Request $request){
        $validator = Validator::make($request->all(), [
            "persianName" => "required",
            "group" => "required",
        ]);

        if ($validator->fails())
            return response(array( 'message' => $validator->errors()->all(),'statusCode' => 400),400);


        $persianName = trim($request->input('persianName') ?? "");
        $englishName = trim($request->input('englishName') ?? "");
        $group = trim($request->input('group') ?? "");
        $details = $request->input('details') ?? "";
        $price = $request->input('price') ?? 1000000;
        $status = $request->input('status') ?? 'outOfStock';
        $delivery_time = $request->input('deliveryTime') ?? 0;
        $thumbnail = $request->input('thumbnail') ?? "https://dl.cuki.ir/sampleAssets/sampleThumbnail_96x96.png";


        $details_array = array_values(array_filter(array_map('trim', explode("+", str_replace(array("\n", "\r"), '', $details)))));
        $details_array_str = CusStFunc::fixPersianUnicode(json_encode($details_array));


        $groupId = DB::table(DN::tables["FOOD_GROUPS"])->where(DN::FOOD_GROUPS["eName"], $group)->value("id");
        if (!($groupId > 0)){
            return response(["message" => "group name is not available",'statusCode' => 400],400);
        }

        if (DB::connection("resConn")->table(DN::resTables["resFOODS"])->where(DN::resFOODS["pName"], $persianName)->exists()) {
            return response(["details" => "food is duplicate", 'statusCode' => 402], 402);
        }

        if (
            DB::connection("resConn")->table(DN::resTables["resFOODS"])->insert([
                DN::resFOODS["pName"] => $persianName,
                DN::resFOODS["eName"] => $englishName,
                DN::resFOODS["group"] => $group,
                DN::resFOODS["groupId"] => $groupId,
                DN::resFOODS["details"] => $details_array_str,
                DN::resFOODS["price"] => $price,
                DN::resFOODS["status"] => $status,
                DN::resFOODS["orderTimes"] => 0,
                DN::resFOODS["discount"] => 0,
                DN::resFOODS["deliveryTime"] => $delivery_time,
                DN::resFOODS["thumbnail"] => $thumbnail,
                DN::CA => time(),
                DN::UA => time(),
            ])
        ) {
            return response(['statusCode' => 200]);
        } else {
            return response(["message" => "some thing went wrong during creating new food on server",'statusCode' => 500],500);
        }
    }

    public function changeFoodInfo(Request $request){
        $validator = Validator::make($request->all(), [
            "foodId" => "required",
            'foodThumbnail' => 'image|mimes:jpg,jpeg,png,svg,gif|max:2048'
        ]);

        if ($validator->fails())
            return response(array( 'message' => $validator->errors()->all(),'statusCode' => 400),400);

        $foodId = $request->input("foodId");
        $persianName = $request->input("persianName");
        $englishName = $request->input("englishName");
        $group = trim($request->input("group"));
        $details = $request->input("details");
        $price = $request->input("price");
        $status = $request->input("status");
        $discount = $request->input("discount");
        $deliveryTime = $request->input("deliveryTime");
        $counterAppFoodId = $request->input("counterAppFoodId");


        // validate and translate inputs
        if(strlen($group) > 2){
            $foodGroup = DB::table(DN::tables["FOOD_GROUPS"])->where(DN::FOOD_GROUPS["eName"], $group);
            if(!$foodGroup->exists())
                return response(["massage"=>"group name is not available", "statusCode"=>400],400);
        }

        if(strlen($details) > 2){
            $details = array_values(array_filter(array_map('trim', explode("+", str_replace(array("\n", "\r"), '', $details)))));
        }else{
            $details = array();
        }






        $previousFoodInfo = DB::connection("resConn")->table(DN::resTables["resFOODS"])->where("id",$foodId);
        if(!$previousFoodInfo->exists())
            return response(["massage"=>"couldn't find food, foodId maybe incorrect", "statusCode"=>404],404);
        $previousFoodInfo = json_decode(json_encode($previousFoodInfo->first()),true);


        $sqlUpdateFoodInfoParams = array(
            DN::resFOODS["pName"] => strlen($persianName) > 2 ? $persianName : $previousFoodInfo[DN::resFOODS["pName"]],
            DN::resFOODS["eName"] => strlen($englishName) > 2 ? $englishName : $previousFoodInfo[DN::resFOODS["eName"]],
            DN::resFOODS["group"] => strlen($group) > 2 ? $group : $previousFoodInfo[DN::resFOODS["group"]],
            DN::resFOODS["details"] => count($details) > 0 ? json_encode($details,JSON_UNESCAPED_UNICODE) : $previousFoodInfo[DN::resFOODS["details"]],
            DN::resFOODS["price"] => $price > 999 ? $price : $previousFoodInfo[DN::resFOODS["price"]],
            DN::resFOODS["status"] => in_array($status, array("inStock", "outOfStock", "deleted")) ? $status : $previousFoodInfo[DN::resFOODS["status"]],
            DN::resFOODS["discount"] => $discount > 0 ? $discount : $previousFoodInfo[DN::resFOODS["discount"]],
            DN::resFOODS["counterAppFoodId"] => $counterAppFoodId > 0 ? $counterAppFoodId : $previousFoodInfo[DN::resFOODS["counterAppFoodId"]],
            DN::resFOODS["deliveryTime"] => $deliveryTime > 0 ? $deliveryTime : $previousFoodInfo[DN::resFOODS["deliveryTime"]],
            DN::UA=>time(),
        );

        $changedFields = [];

        if(($request->file('foodThumbnail')!= null)){
            if(!self::changeFoodThumbnail($request->input("token"), $foodId, $request->file('foodThumbnail')))
                return response(["massage"=>"couldn't upload thumbnail", "statusCode"=>500],500);
            $changedFields[] = 'foodThumbnail';
        }

        foreach (["foodId", "persianName", "englishName", "group", "details", "price", "status", "discount", "deliveryTime", "counterAppFoodId"] as $eField){
            if(strlen($request->input($eField)) >1)
                $changedFields[] = $eField;
        }

        if(DB::connection("resConn")->table(DN::resTables["resFOODS"])->where("id",$foodId)->update($sqlUpdateFoodInfoParams)){
            return response(array('statusCode'=>200, "data"=>["changedFields"=>join(" ,",$changedFields)]));
        }else{
            return response(["massage"=>"something went wrong during change food info on server", "statusCode"=>500],500);
        }
    }

    public function uploadFoodImage(Request $request){
        $validator = Validator::make($request->all(), [
            "foodId" => "required",
            'foodImage' => 'image|mimes:jpg,jpeg,png,svg|required'
        ]);

        if ($validator->fails())
            return response(array('message' => $validator->errors()->all(), 'statusCode' => 400), 400);


        $foodId = $request->input("foodId");
        $token = $request->input("token");
        $imgFile = $request->file('foodImage');

        $resEnglishName = DB::table(DN::tables["RESTAURANTS"])->where(DN::RESTAURANTS["token"], $token)->value(DN::RESTAURANTS["eName"]);
        $restaurantFolder = preg_replace('/ /', "_", $resEnglishName);
        $filePath = self::resIMGPath.$restaurantFolder."/foodsImg";

        self::createPath($filePath);

        $img = Image::make($imgFile->path());

        // check image is landscape and  4:3
        if(($img->width() / $img->height()) !== (4/3)){
            if(($img->width() / $img->height()) === (3/4)){
                return response(["massage"=>"image must be landscape", "statusCode"=>400]);
            }else{
                return response(["massage"=>"image aspect ratio must be 4:3", "statusCode"=>400]);
            }
        }

        if($img->width() < 1080){
            return response(["massage"=>"please select a bigger picture, min width is 1080", "statusCode"=>400]);
        }


        $imageInfo = [];
        $qualityList = [1080,720,540];
        $currentTime = time();
        foreach ($qualityList as $eQuality){
            $newFileName = strtolower($foodId."_$currentTime"."_foodImg_$eQuality".".". $imgFile->extension());
            $img->resize($eQuality, null, function ($const) {$const->aspectRatio();})->save($filePath.'/'.$newFileName);
            $imgUrlPath = "resimg/".$restaurantFolder."/foodsImg/".$newFileName;
            $imageInfo[$eQuality] = $imgUrlPath;
        }

        $previousImageList = json_decode(DB::connection("resConn")->table(DN::resTables["resFOODS"])->where("id",$foodId)->value(DN::resFOODS["photos"]) ?? "[]");
        $previousImageList[] = $imageInfo;
        if(DB::connection("resConn")->table(DN::resTables["resFOODS"])->where("id",$foodId)
            ->update([
                DN::resFOODS["photos"]=>$previousImageList
            ])
        ){
            return response(array('statusCode'=>200, "data"=>["photoList"=>$previousImageList]));
        }else{
            return response(["massage"=>"something went wrong during uploading image on server", "statusCode"=>500],500);
        }
    }

    public function getFoodList (){
        $foodsList = DB::connection("resConn")->table(DN::resTables["resFOODS"]);
        return response(array('statusCode'=>200, 'data'=>$foodsList ? CusStFunc::arrayKeysToCamel(json_decode(json_encode($foodsList->get()),true)) : array()));

    }


    static protected function changeFoodThumbnail($token,$foodId, $thumbnail):bool{
        $resEnglishName = DB::table(DN::tables["RESTAURANTS"])->where(DN::RESTAURANTS["token"], $token)->value(DN::RESTAURANTS["eName"]);
        $restaurantFolder = preg_replace('/ /', "_", $resEnglishName);
        $filePath = self::resIMGPath.$restaurantFolder."/foodThumbnail";

        self::createPath($filePath);

        $newFileName = strtolower($foodId.'_'.time().'_foodThumbnail.'. $thumbnail->extension());
        $img = Image::make($thumbnail->path());
        $img->resize(125, 150, function ($const) {
            $const->aspectRatio();
        })->save($filePath.'/'.$newFileName);

        $thumbnailUrl = self::dlURL."resimg/".$restaurantFolder."/foodThumbnail/".$newFileName;


        return DB::connection("resConn")->table(DN::resTables["resFOODS"])->where("id",$foodId)->update([DN::resFOODS["thumbnail"]=>$thumbnailUrl]);
    }


    static protected function createPath($path):bool {
        if (is_dir($path)) return true;
        $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
        $return = self::createPath($prev_path);
        return $return && is_writable($prev_path) && mkdir($path);
    }

}
