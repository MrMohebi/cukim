<?php

namespace App\Http\Controllers\api\cuki\v1;

use App\CustomFunctions\CusStFunc;
use App\DatabaseNames\DN;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class offCode extends Controller{

    public function getOffCodes(Request $request){
        $validator = Validator::make($request->all(), [
            'resEnglishName' => "required",
        ]);

        if ($validator->fails())
            return response(["massage" => $validator->errors()->all(), "statusCode" => 400], 400);

        $user = DB::table(DN::tables["USERS"])->where(DN::USERS["token"] ,$request->input("token"));
        $offCodesList = json_decode(json_encode(DB::table(DN::tables["OFF_CODES"])
            ->where([
                [DN::OFF_CODES["target"], "=" , $user->value(DN::USERS["phone"])],
                [DN::OFF_CODES["status"], "=" , "active"]
                ])
            ->where(function ($query) use ($request) {
                $query->where(DN::OFF_CODES["creator"], $request->input("resEnglishName"))
                    ->orWhere(DN::OFF_CODES["creator"], 'system');
            })
            ->get()),true);

        return response(["data"=>CusStFunc::arrayKeysToCamel($offCodesList), "statusCode"=>200]);

    }
}
