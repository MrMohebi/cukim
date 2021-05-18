<?php

namespace App\Http\Controllers\api\res\v1;

use App\CustomFunctions\CusStFunc;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\DatabaseNames\DN;

class createNewFood extends Controller
{
    //
    function createNewFood(Request $request){

        $validatedData = Validator::make($request->all(), [
            "persianName" => "required",
            "group" => "required",
        ]);

        if ($validatedData->fails())
            return response(array( 'message' => "wrong inputs!",'statusCode' => 400),400);


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
            ])
        ) {
            return response(['statusCode' => 200]);
        } else {
            return response(["message" => "some thing went wrong during creating new food on server",'statusCode' => 500],500);
        }
    }
}
