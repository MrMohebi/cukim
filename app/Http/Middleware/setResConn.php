<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

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
        if($request->input("resEnglishName")){
            $resDatabaseName = DB::table("restaurants")
                ->where("english_name", $request->input("resEnglishName"))
                ->first()
                ->value("db_name");

            Config::set('database.connections.resConn.database', $resDatabaseName);
        }
        return $next($request);
    }
}
