<?php

namespace App\Http\Controllers\api\cuki\v1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class getIpInfo extends Controller
{
    public function getIpInfo(){
        $ip = $_SERVER['REMOTE_ADDR'];

//        $result = Http::get("https://ipinfo.io/$ip/json");
        $result = Http::withHeaders(["Accept"=>'application/json', "Content-Type"=>'application/json'])->get("http://ip-api.com/json/$ip");

        return response(array(
            'ip'=>$ip,
            'city'=>$result['city'],
            'country'=>$result['country'],
            'location'=>$result['regionName'],
            'timezone'=>$result['timezone']
        ));
    }
}
