<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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

            config(['database.connections.resConn' => [
                'driver'    => 'mysql',
                'host'      => env('DB_HOST', '127.0.0.1'),
                'database'  => $resDatabaseName,
                'username'  => env('DB_USERNAME', 'root'),
                'password'  => env('DB_PASSWORD', ''),
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
                'strict'    => false,
            ]]);
        }
        return $next($request);
    }
}
