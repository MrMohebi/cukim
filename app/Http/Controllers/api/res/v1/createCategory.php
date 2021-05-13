<?php

namespace App\Http\Controllers\api\res\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class createCategory extends Controller
{
    function createCategory(Request $request)
    {

        $validatedData = Validator::make($request, [
            "catPersianName" => 'min:2|required',
            "catEnglishName" => 'min:2|required',
        ]);


        if (!$validatedData->fails()) {


            $catPersianName = $request->input('catPersianName');
            $catEnglishName = $request->catEnglishName;
            $logo = $request->logo;
            $rank = $request->rank;
            $type = $request->type;
            $resEnglishName = $request->resEnglishName;
            $averageColor = $request->averageColor;


            if (
                DB::table('food_groups')->where('persian_name', $catPersianName)->exists() &&
                DB::table('food_groups')->where('english_name', $catEnglishName)->exists()
            ) {
                return response(["message" => "some of info are duplicate", 'statusCode' => 402], 402);
            }

            $insertCreateCat = [
                "persian_name" => $catPersianName,
                "english_name" => $catEnglishName,
                "logo" => $logo,
                "status" => "active",
                "type" => $type,
                "res_english_name" => $resEnglishName,
                "average_color" => $averageColor
            ];


            if (DB::table('food_groups')->insert($insertCreateCat)) {
                return response(['statusCode' => 200], 200);

            } else {
                return response(["message" => "some thing went wrong during creating new category on server", 'statusCode' => 500], 500);
            }

        } else {
            return response(['message' => "wrong inputs", 'statusCode' => 400], 400);
        }
    }

}
