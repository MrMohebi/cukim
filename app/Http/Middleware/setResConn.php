<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\DatabaseNames\DN;

class setResConn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $resDatabaseName ="";
        if($request->input("token")){
            $resDatabaseName = DB::table(DN::tables["RESTAURANTS"])
                ->where(DN::RESTAURANTS["token"], $request->input("token"))
                ->value(DN::RESTAURANTS["DBName"]);
        }
        if($request->input("resEnglishName")){
            $resDatabaseName = DB::table(DN::tables["RESTAURANTS"])
                ->where(DN::RESTAURANTS["eName"], $request->input("resEnglishName"))
                ->value(DN::RESTAURANTS["DBName"]);
        }
        if(strlen($resDatabaseName) > 2){
            Config::set('database.connections.resConn.database', $resDatabaseName);
        }
        return $next($request);
    }
}
