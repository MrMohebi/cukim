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
            "resEnglishName"=>"required|min:3"
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],400);

        if(!(self::isResOpen() && self::isResActive()))
            return response(["massage"=>"restaurant is closed", "statusCode"=>403],403);


        $randomNum = rand(11111111,99999999);
//        $offcodeUsed = false;

        $englishName =  $request->input("resEnglishName");
        $token =  $request->input("token");
        $items =  str_replace("\\","",$request->input("items"));
        $details =  $request->input("details");
        $deliveryAt =  $request->input("deliveryAt") ?? Carbon::now()->timestamp;
        $deliveryPrice  =  $request->input("deliveryPrice");
        $address =  json_decode(str_replace("\\","",$request->input("address")),true);
        $table  =  $request->input("table");

        $user = DB::table(DN::tables["USERS"])->where(DN::USERS["token"], $token);

        if(is_array($address) && count($address) > 1)
            $address["addressText"] = self::getAddressText($address["coordinates"][0], $address["coordinates"][1]);

        $items_array = json_decode($items, true); // [{id: 6, number: 2}, {id: 42, number: 6}, ....]
        $ordersFullInfo = self::getFoodInfo($items_array);
        $orderPrice = self::TotalPriceWithDiscount($ordersFullInfo);

        // change it when off codes added
        $totalPrice = $orderPrice;

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



    private static function sendSMSToCounter($customerPhone, $orderList, $finalPrice):bool{
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
            return true;
        }
        return false;
    }

    private static function TotalPriceWithDiscount($OrderedFoodsInfo){
        $totalPrice = 0;
        foreach ($OrderedFoodsInfo as $eachFood){
            $totalPrice += $eachFood['priceAfterDiscount'] * $eachFood['number'];
        }
        return $totalPrice;
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
