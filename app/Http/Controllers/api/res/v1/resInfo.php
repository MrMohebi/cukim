<?php

namespace App\Http\Controllers\api\res\v1;

use App\CustomFunctions\CusStFunc;
use App\DatabaseNames\DN;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class resInfo extends Controller
{
    public function changeResInfo(Request $request){
        $persianName = trim($request->input("persianName"));
        $englishName = trim($request->input("englishName"));
        $status = $request->input("status");
        $counterPhone = $request->input("counterPhone");
        $phone = json_decode(str_replace("\\","",$request->input("phone")));
        $addressText = $request->input("addressText");
        $addressLink = $request->input("addressLink");
        $owner = $request->input("owner");
        $employers = json_decode(str_replace("\\","",$request->input("employers")));
        $socialLinks = json_decode(str_replace("\\","",$request->input("socialLinks")), true);
        $openTime = (array) json_decode(str_replace("\\","",$request->input("openTime")));
        $type = json_decode(str_replace("\\","",$request->input("type")));
        $minOrderPrice = $request->input("minOrderPrice");


        $resInfoLastId = DB::connection("resConn")->table(DN::resTables["resINFO"])->get()->last()->id;

        $resInfo = DB::connection("resConn")->table(DN::resTables["resINFO"])->where("id", $resInfoLastId);

        $sqlUpdateResInfoParams = array(
            DN::resINFO["pName"] => strlen($persianName) > 2 ? $persianName : $resInfo->value(DN::resINFO["pName"]),
            DN::resINFO["eName"] => strlen($englishName) > 2 ? $englishName : $resInfo->value(DN::resINFO["eName"]),
            DN::resINFO["status"] => strlen($status) > 2 ? $status : $resInfo->value(DN::resINFO["status"]),
            DN::resINFO["counterPhone"] => strlen($counterPhone) == 11 ? $counterPhone : $resInfo->value(DN::resINFO["counterPhone"]),
            DN::resINFO["phones"] => (is_array($phone)) ? json_encode($phone) : (strlen($phone) > 4 ? json_encode(array($phone)) : $resInfo->value(DN::resINFO["phones"])),
            DN::resINFO["address"] => strlen($addressText) > 3 ? $addressText : $resInfo->value(DN::resINFO["address"]),
            DN::resINFO["addressLink"] => strlen($addressLink) > 10 ? $addressLink : $resInfo->value(DN::resINFO["addressLink"]),
            DN::resINFO["owner"] => strlen($owner) > 2 ? $owner : $resInfo->value(DN::resINFO["owner"]),
            DN::resINFO["employers"] => is_array($employers) && count($employers) > 0 ? json_encode($employers) : $resInfo->value(DN::resINFO["employers"]),
            DN::resINFO["socialLinks"] => is_array($socialLinks) && count($socialLinks) > 0  ? json_encode($socialLinks) : $resInfo->value(DN::resINFO["socialLinks"]),
            DN::resINFO["openTime"] => is_array($openTime) && count($openTime) == 7  ? json_encode($openTime) : $resInfo->value(DN::resINFO["openTime"]),
            DN::resINFO["type"] => is_array($type) && count($type) > 0  ? json_encode($type) : $resInfo->value(DN::resINFO["type"]),
            DN::resINFO["minOrderPrice"]=> $minOrderPrice > 100  ? $minOrderPrice : $resInfo->value(DN::resINFO["minOrderPrice"]),
            DN::UA => time(),
        );


        $changedFields = [];
        foreach ( ["persianName", "englishName", "status", "counterPhone", "phone", "addressText", "addressLink", "owner", "employers", "socialLinks", "openTime","type","minOrderPrice"] as $eField){
            if(strlen($request->input($eField)) >1)
                $changedFields[] = $eField;
        }

        if($resInfo->update($sqlUpdateResInfoParams)){
            return response(array('statusCode'=>200, "data"=>["changedFields"=>join(" ,",$changedFields)], "test"=>$openTime));
        }else{
            return response(["massage"=>"something went wrong during change food info on server", "statusCode"=>500],500);
        }

    }

    public function getResInfo(){
        $resInfo = DB::connection("resConn")->table(DN::resTables["resINFO"]);

        return response(array('statusCode'=>200, 'data'=>$resInfo ? CusStFunc::arrayKeysToCamel(json_decode(json_encode($resInfo->first()),true)) : array()));
    }
}
