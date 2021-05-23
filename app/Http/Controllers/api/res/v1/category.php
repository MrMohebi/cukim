<?php

namespace App\Http\Controllers\api\res\v1;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\DatabaseNames\DN;

class category extends Controller
{
    function createCategory(Request $request)
    {

        $validatedData = Validator::make($request->all(), [
            "catPersianName" => 'min:2|required',
            "catEnglishName" => 'min:2|required',
        ]);


        if (!$validatedData->fails()) {


            $catPersianName = $request->input('catPersianName');
            $catEnglishName = $request->input('catEnglishName');
            $logo = $request->input('logo') ?? "";
            $rank = $request->input('type') ?? "";
            $type = $request->input('type') ?? "";
            $resEnglishName = $request->input('resEnglishName') ?? "";
            $averageColor = $request->input('averageColor') ?? "";


            if (
                DB::table(DN::tables["FOOD_GROUPS"])->where(DN::FOOD_GROUPS['pName'], $catPersianName)->exists() &&
                DB::table(DN::tables["FOOD_GROUPS"])->where(DN::FOOD_GROUPS['eName'], $catEnglishName)->exists()
            ) {
                return response(["message" => "some of info are duplicate", 'statusCode' => 402], 402);
            }

            $insertCreateCat = [
                DN::FOOD_GROUPS['pName'] => $catPersianName,
                DN::FOOD_GROUPS['eName'] => $catEnglishName,
                DN::FOOD_GROUPS['logo'] => $logo,
                DN::FOOD_GROUPS['status'] => "active",
                DN::FOOD_GROUPS['type'] => $type,
                DN::FOOD_GROUPS['resEName'] => $resEnglishName,
                DN::FOOD_GROUPS['averageColor'] => $averageColor,
                DN::CA=> Carbon::now()->timestamp,
            ];


            if (DB::table(DN::tables["FOOD_GROUPS"])->insert($insertCreateCat)) {
                return response(['statusCode' => 200]);

            } else {
                return response(["message" => "some thing went wrong during creating new category on server", 'statusCode' => 500], 500);
            }

        } else {
            return response(['message' => "wrong inputs", 'statusCode' => 400], 400);
        }
    }

}
