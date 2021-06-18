<?php

namespace App\Http\Controllers\api\resOwner\v1;

use App\CustomFunctions\CusStFunc;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class plans extends Controller
{
    public function getPlansInfo(){
        $plans = json_decode(json_encode(DB::table("plans")->get()),true);
        return response(["data"=>CusStFunc::arrayKeysToCamel($plans), "statusCode"=>200]);
    }
}
