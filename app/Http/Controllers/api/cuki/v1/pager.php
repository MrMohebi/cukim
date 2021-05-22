<?php

namespace App\Http\Controllers\api\cuki\v1;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\DatabaseNames\DN;

class pager extends Controller
{
    public function callPager(Request $request){
        $validator = Validator::make($request->all(),[
            'resEnglishName'=>"required|min:3",
            "table"=>"required"
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],"400");

        $table =  $request->input("table");
        $customerPhone = $request->input("customerPhone") ?? "notDefined";

        // check last open paging from this table in past 5 mins
        if(DB::connection("resConn")
            ->table(DN::resTables["resPAGERS"])
            ->where([
                [DN::resPAGERS["table"], "=" , $table],
                [DN::CA, ">", (time() - 150)]])
            ->exists())
            return response(["massage"=>"there is an open paging in last 5 min", "statusCode"=>401],401);


        $sql_newPagingParams = array(
            DN::resPAGERS["table"]=>$table,
            DN::resPAGERS["userPhone"]=>$customerPhone,
            DN::CA=> Carbon::now()->timestamp,
            DN::UA=> Carbon::now()->timestamp,
        );

        if(DB::connection("resConn")->table(DN::resTables["resPAGERS"])->insert($sql_newPagingParams)){
            return response(array('statusCode'=>200));
        }else{
            return response(["massage"=>"something went wrong during paging", "statusCode"=>500],500);
        }

    }
}
