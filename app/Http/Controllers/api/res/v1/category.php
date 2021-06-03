<?php

namespace App\Http\Controllers\api\res\v1;

use App\CustomFunctions\CusStFunc;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\DatabaseNames\DN;

class category extends Controller
{
    function createCategory(Request $request){
        $validator = Validator::make($request->all(), [
            "catPersianName" => 'min:2|required',
            "catEnglishName" => 'min:2|required',
        ]);

        if ($validator->fails())
            return response(array( 'message' => $validator->errors()->all(),'statusCode' => 400),400);


        $catPersianName = $request->input('catPersianName');
        $catEnglishName = $request->input('catEnglishName');
        $logo = $request->input('logo') ?? "";
        $rank = $request->input('rank') ?? "";
        $type = $request->input('type') ?? "restaurant";
        $averageColor = $request->input('averageColor') ?? "";

        if (
            DB::table(DN::tables["FOOD_GROUPS"])->where(DN::FOOD_GROUPS['pName'], $catPersianName)->exists() &&
            DB::table(DN::tables["FOOD_GROUPS"])->where(DN::FOOD_GROUPS['eName'], $catEnglishName)->exists()
        ) {
            return response(["message" => "some of info are duplicate", 'statusCode' => 402], 402);
        }

        $res = DB::table(DN::tables["RESTAURANTS"])->where(DN::RESTAURANTS["token"], $request->input("token"));

        $insertCreateCat = [
            DN::FOOD_GROUPS['pName'] => $catPersianName,
            DN::FOOD_GROUPS['eName'] => $catEnglishName,
            DN::FOOD_GROUPS['logo'] => $logo,
            DN::FOOD_GROUPS['status'] => "active",
            DN::FOOD_GROUPS['type'] => $type,
            DN::FOOD_GROUPS['resEName'] => $res->value(DN::RESTAURANTS["eName"]),
            DN::FOOD_GROUPS['averageColor'] => $averageColor,
            DN::FOOD_GROUPS["rank"]=>$rank,
            DN::CA=> Carbon::now()->timestamp,
        ];


        if (DB::table(DN::tables["FOOD_GROUPS"])->insert($insertCreateCat)) {
            return response(['statusCode' => 200]);

        } else {
            return response(["message" => "some thing went wrong during creating new category on server", 'statusCode' => 500], 500);
        }

    }

    public function getCategoryList(Request $request){
        $resEnglishName = DB::table(DN::tables["RESTAURANTS"])->where(DN::RESTAURANTS["token"], $request->input("token"))->value(DN::RESTAURANTS["eName"]);

        $catsList = DB::table(DN::tables["FOOD_GROUPS"])->where(DN::FOOD_GROUPS['resEName'], $resEnglishName)->orWhere(DN::FOOD_GROUPS['resEName'], 'general');

        return response(array('statusCode'=>200, 'data'=>$catsList ? CusStFunc::arrayKeysToCamel(json_decode(json_encode($catsList->get()),true)) : array()));
    }

}
