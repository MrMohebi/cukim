<?php

namespace App\Http\Middleware\tokenCheck;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DatabaseNames\DN;

class cukiTokenCheck
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
        if(DB::table(DN::tables["USERS"])->where(DN::USERS["token"],$request->input("token") )->exists()){
            return $next($request);
        }else{
            return response(["massage"=>"token is not valid", "statusCode"=>401],401);
        }
    }
}
