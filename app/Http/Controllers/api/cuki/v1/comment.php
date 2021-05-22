<?php

namespace App\Http\Controllers\api\cuki\v1;

use App\CustomFunctions\CusStFunc;
use App\DatabaseNames\DN;
use App\Http\Controllers\Controller;
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
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],"400");

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
                $eComment = CusStFunc::arrayKeysToCamel(json_decode(json_encode($eComment)));
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
            return response(["massage"=>"nothing found", "statusCode"=>404],"404");
        }
    }

    protected static function getOrdersAndLastTrackingIdBaseOnFoodId($userPhone, $foodId, $startTime, $endTime):array{
        $ordersList = CusStFunc::arrayKeysToCamel(json_decode(json_encode(DB::connection("resConn")
            ->table(DN::resTables["resORDERS"])
            ->where(DN::resORDERS['userPhone'], "=", "$userPhone")
            ->whereBetween(DN::CA, [$startTime, $endTime])->orderBy(DN::CA,"desc")
            ->get())));

        if(!($ordersList))
            return array(0,array(),array());

        if(isset($ordersList[DN::resORDERS["trackingId"]]))
            $ordersList = array($ordersList);

        $trackingId = 0;
        $selectedOrder = array();
        foreach ($ordersList as $eOrder){
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
