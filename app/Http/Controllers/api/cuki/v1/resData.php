<?php

namespace App\Http\Controllers\api\cuki\v1;

use App\DatabaseNames\DN;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\CustomFunctions\CusStFunc;

class resData extends Controller
{
    public function getResData(Request $request){
        $validator = Validator::make($request->all(),[
            'resEnglishName'=>"required",
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],"400");

        return response(array('data'=>array(
                'foods'=>self::getFoodList(),
                'restaurantInfo'=>self::getResInfo()
            ),'statusCode'=>200));
    }

    public function getResParts(Request $request){
        $validator = Validator::make($request->all(),[
            'resEnglishName'=>"required",
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],"400");

        return response(['statusCode'=>200,"data"=>json_decode(self::getResInfo()[DN::resINFO["type"]])]);
    }

    public function getResENameByCode(Request $request){
        $validator = Validator::make($request->all(),[
            'resCode'=>"required",
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],400);

        $resCode =  $request->input("resCode");

        $resEnglishName = DB::table(DN::tables["RESTAURANTS"])->where(DN::RESTAURANTS["code"],$resCode)->value(DN::RESTAURANTS["eName"]);

        if(strlen($resEnglishName) > 2){
            return response(
                array(
                    'statusCode'=>200,
                    'data'=>array(
                        'resEnglishName'=> $resEnglishName
                    )
                )
            );
        }else{
            return response(array('massage'=>"restaurant not found",'statusCode'=>404), 404);
        }

    }



    static public function getResInfo():array{
        $resInfo = DB::connection("resConn")->table(DN::resTables["resINFO"])->get()->last();
        unset($resInfo->{DN::resINFO["counterPhone"]});
        return CusStFunc::arrayKeysToCamel(json_decode(json_encode($resInfo),true));
    }



    static public function getFoodList():array{
        $foodsList = DB::connection("resConn")
            ->table(DN::resTables["resFOODS"])
            ->where(DN::resFOODS["status"], "!=","deleted")
            ->get();

        $groupsInfo = DB::table(DN::tables["FOOD_GROUPS"])->get();


        for($i = 0; $i < count($foodsList) ; $i++){
            $group=array();
            foreach ($groupsInfo as $eGroup){
                if($eGroup->{DN::FOOD_GROUPS["eName"]} == $foodsList[$i]->{DN::resFOODS["group"]}){
                    $group = $eGroup;
                    break;
                }
            }

            $groupInfo = array(
                "englishName"=>$group->{DN::FOOD_GROUPS["eName"]},
                "persianName"=>$group->{DN::FOOD_GROUPS["pName"]},
                "logo"=>$group->{DN::FOOD_GROUPS["logo"]},
                "status"=>$group->{DN::FOOD_GROUPS["status"]},
                "rank"=>$group->{DN::FOOD_GROUPS["rank"]},
                "averageColor"=>$group->{DN::FOOD_GROUPS["averageColor"]},
                "type"=>$group->{DN::FOOD_GROUPS["type"]},
            );
            $foodsList[$i]->{DN::resFOODS["group"]} = $groupInfo;
        }
        return CusStFunc::arrayKeysToCamel(json_decode(json_encode($foodsList),true));
    }
}
