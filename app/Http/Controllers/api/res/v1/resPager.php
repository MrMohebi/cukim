<?php

namespace App\Http\Controllers\api\res\v1;

use App\CustomFunctions\CusStFunc;
use App\DatabaseNames\DN;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class resPager extends Controller
{
    public function getPagers(){
        $pagerList = DB::connection("resConn")
            ->table(DN::resTables["resPAGERS"])
            ->where(DN::CA, ">", (time() - 14400))
            ->orderByDesc("id")
            ->limit(150);

        return response(array('statusCode'=>200, 'data'=>$pagerList ? CusStFunc::arrayKeysToCamel(json_decode(json_encode($pagerList->get()),true)) : array()));
    }
}
