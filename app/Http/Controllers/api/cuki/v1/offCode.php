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
                [DN::OFF_CODES["status"], "=" , "active"],
                [DN::OFF_CODES["place"], "=" , $request->input("resEnglishName")]
                ])
            ->where(function ($query) use ($request) {
                $query->where(DN::OFF_CODES["creator"], $request->input("resEnglishName"))
                    ->orWhere(DN::OFF_CODES["creator"], 'system');
            })
            ->get()),true);

        return response(["data"=>CusStFunc::arrayKeysToCamel($offCodesList), "statusCode"=>200]);

    }

    public function validateOffCode(Request $request){
        $validator = Validator::make($request->all(), [
            'resEnglishName' => "required",
            "offCode" => "required",
            "amount"=> "required|numeric"
        ]);

        if ($validator->fails())
            return response(["massage" => $validator->errors()->all(), "statusCode" => 400], 400);


        $amount = $request->input("amount");
        $isOffCodeValid = false;

        $user = DB::table(DN::tables["USERS"])->where(DN::USERS["token"] ,$request->input("token"));
        $offCode = DB::table(DN::tables["OFF_CODES"])
            ->where([
                [DN::OFF_CODES["target"], "=" , $user->value(DN::USERS["phone"])],
                [DN::OFF_CODES["status"], "=" , "active"],
            ])
//            ->where(function ($query) use ($amount) {
//                $query->where([[DN::OFF_CODES["maxAmount"], "=", 0], [DN::OFF_CODES["minAmount"], "<=", $amount]])
//                    ->orWhere([[DN::OFF_CODES["minAmount"], "=", 0], [DN::OFF_CODES["maxAmount"], ">=", $amount]])
//                    ->orWhere([[DN::OFF_CODES["minAmount"], "=", 0], [DN::OFF_CODES["maxAmount"], "=", 0]])
//                    ->orWhere([[DN::OFF_CODES["maxAmount"], ">=", $amount], [DN::OFF_CODES["minAmount"], "<=", $amount]]);
//            })
            ->where(function ($query) use ($request) {
                $query->where(DN::OFF_CODES["creator"], $request->input("resEnglishName"))
                    ->orWhere(DN::OFF_CODES["creator"], 'system');
            })
            ->whereColumn(DN::OFF_CODES["used"], "<", DN::OFF_CODES["times"]);

            if( $offCode->exists() &&
                ($offCode->value(DN::OFF_CODES["from"]) <= time() && $offCode->value(DN::OFF_CODES["to"]) >= time())&&(
                ($offCode->value(DN::OFF_CODES["maxAmount"]) == 0 && $offCode->value(DN::OFF_CODES["minAmount"]) <= $amount)||
                ($offCode->value(DN::OFF_CODES["minAmount"]) == 0 && $offCode->value(DN::OFF_CODES["maxAmount"]) >= $amount)||
                ($offCode->value(DN::OFF_CODES["minAmount"]) == 0 && $offCode->value(DN::OFF_CODES["maxAmount"]) == 0)||
                ($offCode->value(DN::OFF_CODES["maxAmount"]) >= $amount && $offCode->value(DN::OFF_CODES["minAmount"]) <= $amount)
            )) {
                $isOffCodeValid = true;
            }

        return response(["data"=>["isOffCodeValid"=>$isOffCodeValid], "statusCode"=>200]);

    }
}
