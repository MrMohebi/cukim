<?php

namespace App\Http\Controllers\api\cuki\v1;

use App\DatabaseNames\DN;
use App\Http\Controllers\Controller;
use Ghasedak\GhasedakApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\CustomFunctions\CusStFunc;
use Carbon\Carbon;

class order extends Controller
{
    public function sendOrder(Request $request){
        $validator = Validator::make($request->all(),[
            'items'=>"required|json",
            'details'=>"json",
            'deliveryAt'=>"numeric",
            'deliveryPrice'=>"numeric",
            "resEnglishName"=>"required|min:3",
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],400);

        if(!(self::isResOpen() && self::isResActive()))
            return response(["massage"=>"restaurant is closed", "statusCode"=>403],403);


        $randomNum = rand(11111111,99999999);

        $englishName =  $request->input("resEnglishName");
        $token =  $request->input("token");
        $items =  str_replace("\\","",$request->input("items"));
        $details =  $request->input("details");
        $deliveryAt =  $request->input("deliveryAt") ?? Carbon::now()->timestamp;
        $deliveryPrice  =  $request->input("deliveryPrice");
        $address =  json_decode(str_replace("\\","",$request->input("address")),true);
        $table  =  $request->input("table");
        $offCode  =  $request->input("offCode");

        $user = DB::table(DN::tables["USERS"])->where(DN::USERS["token"], $token);

        if(is_array($address) && count($address) > 1)
            $address["addressText"] = self::getAddressText($address["coordinates"][0], $address["coordinates"][1]);

        $items_array = json_decode($items, true); // [{id: 6, number: 2}, {id: 42, number: 6}, ....]
        $ordersFullInfo = self::getFoodInfo($items_array);
        $orderPrice = self::TotalPriceWithDiscount($ordersFullInfo);

        $totalPrice = $orderPrice;
        if (strlen($offCode) > 2){
            $tOffCode = self::useOffCode($offCode, $orderPrice, $user->value(DN::USERS["phone"]), $englishName, $ordersFullInfo);
            $ordersFullInfo = $tOffCode["orderItems"];
            $offCode = $tOffCode["code"];
            $totalPrice = $tOffCode["finalPrice"];
        }


        if($orderPrice < 900){
            return response(["massage"=>"order list is empty", "statusCode"=>405],405);
        }

        $ordersFullInfo_jsonStr = CusStFunc::fixPersianUnicode(json_encode($ordersFullInfo));


        $orderParams = array(
            DN::resORDERS["trackingId"] => $randomNum,
            DN::resORDERS["userPhone"] => $user->value(DN::USERS["phone"]),
            DN::resORDERS["items"] => $ordersFullInfo_jsonStr,
            DN::resORDERS["deliveryPrice"] => $deliveryPrice,
            DN::resORDERS["address"] => CusStFunc::fixPersianUnicode(json_encode($address)),
            DN::resORDERS["table"] => $table,
            DN::resORDERS["details"] => $details,
            DN::resORDERS["tPrice"] => $totalPrice,
            DN::resORDERS["deliveryAt"] => $deliveryAt,
            DN::resORDERS["paidAmount"] => 0,
            DN::resORDERS["offcode"] => $offCode,
            DN::UA => Carbon::now()->timestamp,
            DN::CA => Carbon::now()->timestamp,
        );

        if (DB::connection("resConn")->table(DN::resTables["resORDERS"])->insert($orderParams)) {
            $trackingIdForUserData = $englishName . "@" . $randomNum;
            $userOrders = json_decode($user->value("orders"));
            $userOrders[] = $trackingIdForUserData;
            if ($user->update([DN::USERS["orders"]=>$userOrders,DN::UA => Carbon::now()->timestamp])) {
                self::sendSMSToCounter($user->value(DN::USERS["phone"]), $ordersFullInfo, $totalPrice);
                return response(array(
                    'statusCode' => 200,
                    "data" => array(
                        'trackingId' => $randomNum,
                        'totalPrice' => $totalPrice,
                        "deliveryAt" => $deliveryAt,
                        "isOffCodeUsed"=> strlen($offCode) > 2,
                    )
                ));
            } else {
                return response(["massage"=>"order didnt saved in user history but saved for restaurant", "statusCode"=>500],500);
            }
        } else {
            return response(["massage"=>"couldn't save order", "statusCode"=>500],500);

        }


    }

    public function getOpenOrders(Request $request){
        $validator = Validator::make($request->all(),[
            "resEnglishName"=>"required|min:3"
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],400);

        $user = DB::table(DN::tables["USERS"])->where(DN::USERS["token"], $request->input("token"));

        $openOrdersList = json_decode(json_encode(DB::connection("resConn")
            ->table(DN::resTables["resORDERS"])
            ->where([
                [DN::resORDERS["userPhone"], $user->value(DN::USERS["phone"])],
                [DN::resORDERS["status"], "!=", "deleted"],
                [DN::resORDERS["status"], "!=", "done"]
            ])
            ->get()), true);


        if(sizeof($openOrdersList) > 0){
            if (isset($openOrdersList['id']))
                $openOrdersList = array($openOrdersList);
            return response(array('statusCode'=>200, 'data'=>CusStFunc::arrayKeysToCamel($openOrdersList)));
        }else{
            return response(["massage"=>"nothing found", "statusCode"=>404],404);
        }

    }

    public function getOrderByTrackingId(Request $request){
        $validator = Validator::make($request->all(),[
            "resEnglishName"=>"required|min:3",
            "trackingId"=>"required|min:8"
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],400);

        $trackingIds =  json_decode($request->input("trackingId"));
        $trackingIds = is_array($trackingIds) ? $trackingIds : array($trackingIds);

        $user = DB::table(DN::tables["USERS"])->where(DN::USERS["token"], $request->input("token"));

        $ordersInfo = json_decode(json_encode(DB::connection("resConn")
            ->table(DN::resTables["resORDERS"])
            ->where(DN::resORDERS["userPhone"], $user->value(DN::USERS["phone"]))
            ->whereIn(DN::resORDERS["trackingId"], $trackingIds)
            ->get()),true);


        if (sizeof($ordersInfo) == 1) {
            return response(array('statusCode'=>200, 'data'=>CusStFunc::arrayKeysToCamel($ordersInfo[0])));
        }else if(sizeof($ordersInfo) > 1){
            return response(array('statusCode'=>200, 'data'=>CusStFunc::arrayKeysToCamel($ordersInfo)));
        }else{
            return response(["massage"=>"nothing found", "statusCode"=>404],404);
        }

    }




    private static function getFoodInfo(array $foods_list):array{
        $orderedFood = array();
        $all_foods = DB::connection("resConn")->table(DN::resTables["resFOODS"])->get();

        foreach ($foods_list as $eachOrderedFood){
            foreach ($all_foods as $eachFood){
                if ($eachOrderedFood['id'] == $eachFood->id) {
                    $priceAfterDiscount = $eachFood->{DN::resFOODS["price"]} * ((100 - $eachFood->{DN::resFOODS["discount"]})/100);
                    $orderedFood[] = array(
                        "id"=>$eachOrderedFood["id"],
                        'persianName'=>$eachFood->{DN::resFOODS["pName"]},
                        'englishName'=>$eachFood->{DN::resFOODS["eName"]},
                        'group'=>$eachFood->{DN::resFOODS["group"]},
                        'number'=>$eachOrderedFood['number'],
                        'price'=>$eachFood->{DN::resFOODS["price"]},
                        'discount'=>$eachFood->{DN::resFOODS["discount"]},
                        'priceAfterDiscount'=>$priceAfterDiscount,
                        'counterAppFoodId'=>$eachFood->{DN::resFOODS["counterAppFoodId"]},
                    );
                }
            }
        }
        return $orderedFood;
    }


    private static function getAddressText ($lat, $lon){
        $result = Http::withHeaders([
            "Accept"=>'application/json',
            "Content-Type"=>'application/json',
            'x-api-key'=>env('MAPAPI_KEY')
        ])->get('https://map.ir/fast-reverse',[
            "lat"=>$lat,
            "lon"=>$lon,
        ]);

        return $result['address_compact'];
    }



    private static function sendSMSToCounter($customerPhone, $orderList, $finalPrice): void{
        $smsApi = new GhasedakApi(env('GHASEDAKAPI_KEY'));

        $carbon = new Carbon();
        $carbon->setTimezone('Asia/Tehran');
        $dateTimeText = $carbon->format('Y-m-d H:i');

        $orderListText = "";
        foreach ($orderList as $eOrder)
            $orderListText .= $eOrder['persianName'] . " ==> " . $eOrder['number'] . "\n";
        $finalPrice = number_format($finalPrice);
        $massageTemplate = "از: $customerPhone". "\n".
            "سفارشات: ". "\n".
            $orderListText. "\n".
            "مجموع: $finalPrice"."\n".
            "زمان: $dateTimeText";
        $counterPhone = DB::connection("resConn")->table(DN::resTables['resINFO'])->latest()->first()->{DN::resINFO["counterPhone"]};
        if(strlen($counterPhone) == 11){
            $smsApi->SendSimple($counterPhone, $massageTemplate, 50001212124276);
        }
    }

    private static function TotalPriceWithDiscount($OrderedFoodsInfo){
        $totalPrice = 0;
        foreach ($OrderedFoodsInfo as $eachFood){
            $totalPrice += $eachFood['priceAfterDiscount'] * $eachFood['number'];
        }
        return $totalPrice;
    }

    private static function useOffCode($offCodeCode, $price, $userPhone, $resEnglishName, $orderItems):array{
        $finalPrice = $price;
        $offCode = DB::table(DN::tables["OFF_CODES"])
            ->where([
                [DN::OFF_CODES["code"], "=" , $offCodeCode],
                [DN::OFF_CODES["target"], "=" , $userPhone],
                [DN::OFF_CODES["status"], "=" , "active"],
                [DN::OFF_CODES["from"], "<=", time()],
                [DN::OFF_CODES["to"], ">=", time()],
            ])
            ->where(function ($query) use ($price) {
                $query->where([[DN::OFF_CODES["maxAmount"], "=", 0], [DN::OFF_CODES["minAmount"], "<=", $price]])
                    ->orWhere([[DN::OFF_CODES["minAmount"], "=", 0], [DN::OFF_CODES["maxAmount"], ">=", $price]])
                    ->orWhere([[DN::OFF_CODES["minAmount"], "=", 0], [DN::OFF_CODES["maxAmount"], "=", 0]])
                    ->orWhere([[DN::OFF_CODES["maxAmount"], ">=", $price], [DN::OFF_CODES["minAmount"], "<=", $price]]);
            })
            ->where(function ($query) use ($resEnglishName) {
                $query->where(DN::OFF_CODES["creator"], $resEnglishName)
                    ->orWhere(DN::OFF_CODES["creator"], 'system');
            })
            ->whereColumn(DN::OFF_CODES["used"], "<", DN::OFF_CODES["times"]);

        if($offCode->exists()){
            $code = $offCode->value(DN::OFF_CODES["code"]);
            if($offCode->value(DN::OFF_CODES["disAmount"]) > 100){
                $finalPrice = $price - $offCode->value(DN::OFF_CODES["disAmount"]);
                $disPercentage = (($price - $finalPrice) / $price * 100);
            }else{
                $disPercentage = $offCode->value(DN::OFF_CODES["disPercentage"]);
                $finalPrice = ceil((100-$disPercentage) / 100 * $price);
            }
            for ($i=0; $i < count($orderItems); $i++){
                $orderItems[$i]["priceAfterDiscount"] =  ceil(((100-$disPercentage) / 100 * $orderItems[$i]["priceAfterDiscount"])/1000)*1000;
            }
            $offCode->increment(DN::OFF_CODES["used"]);
            $offCode->update([DN::UA=>time()]);
            return ["statusCode"=>200,"code"=>$code, "finalPrice"=>$finalPrice, "orderItems"=>$orderItems];
        }else{
            return ["statusCode"=>404, "code"=>"", "massage"=>"offCode is not valid", "finalPrice"=>$finalPrice, "orderItems"=>$orderItems];
        }


    }

    private static function isResActive():bool{
        $resStatus = DB::connection("resConn")->table(DN::resTables["resINFO"])->latest()->first()->{DN::resINFO["status"]};
        return $resStatus == "open";
    }

    private static function isResOpen():bool{
        date_default_timezone_set("Asia/Tehran");
        $currentHour = date("H");
        $dayOfWeek = date("w");
        $openTimes = json_decode(DB::connection("resConn")->table(DN::resTables["resINFO"])->latest()->first()->{DN::resINFO["openTime"]});
        return in_array($currentHour, $openTimes[$dayOfWeek]);
    }

}
