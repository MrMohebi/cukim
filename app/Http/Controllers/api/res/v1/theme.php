<?php

namespace App\Http\Controllers\api\res\v1;

use App\CustomFunctions\CusStFunc;
use App\DatabaseNames\DN;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class theme extends Controller
{
    public function getThemes(){
        $themes = DB::table(DN::tables["THEMES"]);
        return response(array('statusCode'=>200, 'data'=>$themes ? CusStFunc::arrayKeysToCamel(json_decode(json_encode($themes->get()),true)) : array()));
    }
}
