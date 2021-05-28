<?php

namespace App\Http\Controllers\api\cuki\v1;

use App\CustomFunctions\CusStFunc;
use App\DatabaseNames\DN;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class comment extends Controller
{
    public function getCommentsByFoodId(Request $request){
        $validator = Validator::make($request->all(),[
            'resEnglishName'=>"required|min:3",
            "foodId"=>"required",
            "lastDate"=>"required|numeric",
            "number"=>"required|numeric",
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],400);

        $foodId =  $request->input("foodId");
        $lastDate = $request->input("lastDate");
        $number = $request->input("number");

        // get user phone and name
        $user = DB::table(DN::tables["USERS"])->where(DN::USERS["token"], $request->input("token"));


        $trackingIdAndOrders = self::getOrdersAndLastTrackingIdBaseOnFoodId($user->value(DN::USERS["phone"]), $foodId, time()-(8400*3), time());



        $commentsList = DB::connection("resConn")
            ->table(DN::resTables["resCOMMENTS"])
            ->where([
                [DN::resCOMMENTS["foodId"], "=", $foodId],
                [DN::resCOMMENTS["status"], "=", "confirmed"],
                [DN::CA, "<", $lastDate]
            ])
            ->orderBy(DN::CA, "desc")
            ->limit($number)
            ->get();

        if(isset($commentsList->id))
            $commentsList = array($commentsList);

        if(count($commentsList) > 0 && $commentsList){
            // remove privet info
            $finalCommentsList = array();
            foreach ($commentsList as $eComment){
                $eComment = CusStFunc::arrayKeysToCamel(json_decode(json_encode($eComment), true));
                $eComment[DN::resCOMMENTS["phone"]] = ":)";
                array_push($finalCommentsList, $eComment);
            }

            return response(array(
                'statusCode'=>200,
                'data'=>array(
                    'comments'=>$finalCommentsList,
                    'isAllowedLeaveComment'=> $trackingIdAndOrders[0] > 100
                )
            ));
        }else{
            return response(["massage"=>"nothing found", "data"=>['isAllowedLeaveComment'=> $trackingIdAndOrders[0] > 100], "statusCode"=>404],200);
        }
    }

    public function sendComment(Request $request){
        $validator = Validator::make($request->all(),[
            'resEnglishName'=>"required|min:3",
            "foodId"=>"required",
            "body"=>"required|min:3",
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],400);


        $foodId =  $request->input("foodId");
        $title =  $request->input("title");
        $body  =  $request->input("body");
        $rate  =  $request->input("rate");
        $prosCons  =  json_decode(str_replace("\\","",$request->input("prosCons")),true);
        if(!(isset($prosCons['pros']) && isset($prosCons['cons'])))
            $prosCons = array('pros'=>array(), 'cons'=>array());



        // get user phone and name
        $user = DB::table(DN::tables["USERS"])->where(DN::USERS["token"], $request->input("token"));
        $phone = $user->value(DN::USERS["phone"]);
        $name = $user->value(DN::USERS["name"]);

        $trackingIdAndOrders = self::getOrdersAndLastTrackingIdBaseOnFoodId($phone, $foodId, time()-(8400*3), time());

        if($trackingIdAndOrders[0] < 100)
            return response(["massage"=>"your not allowed to leave comment", "statusCode"=>403],403);

        $addCommentParams = array(
            DN::resCOMMENTS["phone"]=>$phone,
            DN::resCOMMENTS["name"]=>$name,
            DN::resCOMMENTS["trackingId"]=>$trackingIdAndOrders[0],
            DN::resCOMMENTS["foodId"]=>$foodId,
            DN::resCOMMENTS["title"]=>$title,
            DN::resCOMMENTS["body"]=>$body,
            DN::resCOMMENTS["rate"]=>$rate,
            DN::resCOMMENTS["orderType"]=>isset($trackingIdAndOrders[2][DN::resORDERS["table"]]) ? "inRes" : "outRes",
            DN::resCOMMENTS["prosCons"]=>json_encode($prosCons),
            DN::resCOMMENTS["status"]=>"notConfirmed",
            DN::UA=>Carbon::now()->timestamp,
            DN::CA=>Carbon::now()->timestamp,
        );

        if(DB::connection("resConn")->table(DN::resTables["resCOMMENTS"])->insert($addCommentParams)){
            return response(array('statusCode'=>200));
        }else{
            return response(["massage"=>"couldn't save comment", "statusCode"=>500],500);
        }
    }

    protected static function getOrdersAndLastTrackingIdBaseOnFoodId($userPhone, $foodId, $startTime, $endTime):array{
        $ordersList = json_decode(json_encode(DB::connection("resConn")
            ->table(DN::resTables["resORDERS"])
            ->where(DN::resORDERS['userPhone'], "=", "$userPhone")
            ->whereBetween(DN::CA, [$startTime, $endTime])->orderBy(DN::CA,"desc")
            ->get()), true);

        if(!($ordersList))
            return array(0,array(),array());

        if(isset($ordersList[DN::resORDERS["trackingId"]]))
            $ordersList = array($ordersList);

        $trackingId = 0;
        $selectedOrder = array();
        foreach ($ordersList as $eOrder){
            $eOrder = json_decode(json_encode($eOrder),true);
            $foodsList = json_decode($eOrder[DN::resORDERS["items"]], true);
            foreach ($foodsList as $eFood){
                if ($eFood['id'] == $foodId)
                    $trackingId = $eOrder[DN::resORDERS["trackingId"]];
                $selectedOrder = $eOrder;
            }
        }
        return array($trackingId, $selectedOrder, $ordersList);
    }
}
