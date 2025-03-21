<?php


namespace App\CustomClasses\Ipg;
use App\CustomClasses\AbstractClasses\Ipg;
use App\DatabaseNames\DN;
use App\Http\Controllers\api\resOwner\v1\res;
use Ghasedak\GhasedakApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\CustomFunctions\CusStFunc;

class Payping extends Ipg{

    protected static $URLCreatePayment = "https://api.payping.ir/v2/pay";
    protected static $URLPaymentLinkBase = "https://api.payping.ir/v2/pay/gotoipg/";
    protected static $URLVerifyPayment = "https://api.payping.ir/v2/pay/verify";
    protected static $URLReturnIPG = "https://api.cukim.ir/api/v1/pay/verify";
    protected static $URLPaymentResult = "http://paystatus.cukim.ir/";


    static public function createOurPaymentLink(string $itemType, array $items, string $userToken, string $trackingId = null):array{
        $userInfo = DB::table(DN::tables["RES_OWNERS"])->where("token", $userToken);
        $userPhone = $userInfo->value(DN::RES_OWNERS["phone"]);
        $userName = $userInfo->value(DN::RES_OWNERS["name"]);
        if(strlen($userPhone) != 11)
            return array('statusCode' => 401, "massage" => "user is not valid");
        if(!$trackingId){
            $trackingId = rand(11111111,99999999);
        }
        $amount = 1000;

        if($itemType == "plan"){
            $plan = DB::table(DN::tables["PLANS"])->where("id", $items[0]);
            if(!$plan->exists())
                return array('statusCode'=>404, "massage" => "plan not found!");
            $items = [
                "persianName"=>$plan->value(DN::PLANS["pName"]),
                "englishName"=>$plan->value(DN::PLANS["eName"]),
                "price"=>$plan->value(DN::PLANS["price"]),
                "items"=> json_decode($plan->value(DN::PLANS["items"])),
                "planId"=>$items[0]
            ];
            $planPrice = $plan->value(DN::PLANS["price"]);
            $amount = ($planPrice - ($plan->value(DN::PLANS["disPercentage"])/100 * $planPrice)) - $plan->value(DN::PLANS["disAmount"]);
        }


        $info_params = array(
            "amount" => $amount,
            "payerIdentity" => $userPhone,
            "payerName" => $userName,
            "description"=>"",
            "clientRefId"=>$trackingId,
            "returnUrl" => self::$URLReturnIPG,
        );

        $api_key = env("PAYPING_KEY");

        $result = Http::withToken($api_key)->post(self::$URLCreatePayment,$info_params)->json();
        $payPingCode = $result['code'] ?? "";

        $sqlInsert_createPaymentParams = array(
            'ipg'=>"payping",
            'tracking_id'=>$trackingId,
            'payment_id'=>$trackingId,
            'payment_group'=>"",
            'payment_num'=>1,
            'payment_key'=>"cuki",
            'item_type'=>$itemType,
            'item'=>json_encode($items),
            'payer_phone'=>$userPhone,
            'payer_name'=>$userName,
            'amount'=>$amount,
            'status'=>'0',
            DN::CA =>time(),
            DN::UA =>time(),
        );


        if(DB::table(DN::tables["PAYMENTS"])->insert($sqlInsert_createPaymentParams)){
            if(strlen($payPingCode) > 2){
                if(DB::table(DN::tables["PAYMENTS"])->where(DN::PAYMENTS["trackingId"],$trackingId)->update(array(DN::PAYMENTS["paypingCode"]=>$payPingCode,DN::UA =>time()))){
                    return array(
                        'statusCode'=>200,
                        "data"=>array(
                            "url"=>self::$URLPaymentLinkBase.$payPingCode,
                            "amount"=>$amount,
                            "paymentId"=>$trackingId,
                        ));
                }else{
                    return array('statusCode'=>500);
                }
            }else{
                return array('statusCode'=>408, "massage" => "something went wrong during getting payment link");
            }
        }else{
            return array('statusCode'=>500, "massage" => "something went wrong during saving payment in our database");
        }

    }

    static public function verifyOurPayment(string $code, string $refid, string $clientrefid):array{

        // get payment info
        $payment = DB::table(DN::tables["PAYMENTS"])->where(DN::PAYMENTS["paymentId"],$clientrefid);


        // was verified before
        if($payment->value(DN::PAYMENTS["verifiedAt"]) > 100){
            return array('statusCode'=>409,"url"=>self::$URLPaymentResult."?".
                "statusCode=409".
                "&amount=".$payment->value(DN::PAYMENTS["amount"]).
                "&paymentId=".$clientrefid.
                "&trackingId=".$payment->value(DN::PAYMENTS["trackingId"]).
                "&itemType=".$payment->value(DN::PAYMENTS["itemType"]).
                "&item=".$payment->value(DN::PAYMENTS["item"])
            );
        }


        $api_key = env("PAYPING_KEY");

        $info_params = array(
            "refId" => $refid,
            'amount'=> $payment->value(DN::PAYMENTS["amount"]),
        );

        $result = Http::withToken($api_key)->post(self::$URLVerifyPayment,$info_params)->json();


        $verifyCardNumber = $result['cardNumber'] ?? null;
        $verifyCardHash = $result['cardHashPan'] ?? null;
        $verifyAmount = $result['amount'] ?? null;


        //check payment is valid and its not duplicate
        if(strlen($verifyCardHash) > 1 && ($payment->value(DN::PAYMENTS["verifiedAt"]) < 1000) && ($payment->value(DN::PAYMENTS["amount"]) == $verifyAmount)){
            $sqlUpdate_paymentPaidParams = array(
                DN::PAYMENTS["verifiedAt"]=>time(),
                DN::PAYMENTS["payerCard"]=>$verifyCardNumber,
                DN::PAYMENTS["payerCardHash"]=>$verifyCardHash,
            );
            if($payment->update($sqlUpdate_paymentPaidParams)){
                if($payment->value(DN::PAYMENTS["itemType"]) == "plan"){
                    $resOwner = DB::table(DN::tables["RES_OWNERS"])->where(DN::RES_OWNERS["phone"], $payment->value(DN::PAYMENTS["payerPhone"]));
                    $newPlanesList = json_decode($resOwner->value(DN::RES_OWNERS["plans"])) ?? [];
                    // create restaurant
                    if(count($newPlanesList) == 0){
                        $tempResName = DB::table(DN::tables["TEMP_RES_NAMES"])->where(DN::TEMP_RES_NAMES["resOwnerId"], $resOwner->value("id"))->latest();
                        $newResRequest = new Request();
                        $newResRequest->setMethod("POST");
                        $newResRequest->request->add([
                            "token"=>$resOwner->value("token"),
                            "username"=>$tempResName->value(DN::TEMP_RES_NAMES["eName"]),
                            "password"=>$clientrefid,
                            "persianName"=>$tempResName->value(DN::TEMP_RES_NAMES["pName"]),
                            "englishName"=>$tempResName->value(DN::TEMP_RES_NAMES["eName"]),
                            "planId"=>json_decode($payment->value(DN::PAYMENTS["item"]),true)["planId"]
                        ]);
                        app(res::class)->createNewRes($newResRequest);
                        $tempResName->delete();
                        $smsApi = new GhasedakApi(env('GHASEDAKAPI_KEY'));
                        $smsApi->Verify($resOwner->value(DN::RES_OWNERS["phone"]),1,'newResOwnerBuyPlan', $tempResName->value(DN::TEMP_RES_NAMES["eName"]), $clientrefid);
                    }


                    $newPlanesList[] = json_decode($payment->value(DN::PAYMENTS["item"]));
                    $resOwner->update([DN::RES_OWNERS["plans"]=>json_encode($newPlanesList)]);
                    DB::table(DN::tables["PLANS"])->where(DN::PLANS["eName"], json_decode($payment->value(DN::PAYMENTS["item"]), true)["englishName"])->increment(DN::PLANS["buyTimes"]);
                }

                return array('statusCode'=>200,"url"=>self::$URLPaymentResult."?".
                    "statusCode=200".
                    "&amount=".$payment->value(DN::PAYMENTS["amount"]).
                    "&paymentId=".$clientrefid.
                    "&trackingId=".$clientrefid.
                    "&itemType=".$payment->value(DN::PAYMENTS["itemType"]).
                    "&item=".$payment->value(DN::PAYMENTS["item"])
                );

            }else{
                return array('statusCode'=>500,"url"=>self::$URLPaymentResult."?".
                    "statusCode=500".
                    "&details=payment couldn't be saved on our server".
                    "&amount=".$payment->value(DN::PAYMENTS["amount"]).
                    "&paymentId=".$clientrefid.
                    "&trackingId=".$clientrefid.
                    "&itemType=".$payment->value(DN::PAYMENTS["itemType"]).
                    "&item=".$payment->value(DN::PAYMENTS["item"])
                );
            }
        }else{
            return array('statusCode'=>402,"url"=>self::$URLPaymentResult."?".
                "statusCode=402".
                "&details=payment is not valid or it's duplicate or was canceled".
                "&amount=".$payment->value(DN::PAYMENTS["amount"]).
                "&paymentId=".$clientrefid.
                "&trackingId=".$clientrefid.
                "&itemType=".$payment->value(DN::PAYMENTS["itemType"]).
                "&item=".$payment->value(DN::PAYMENTS["item"])
            );
        }

    }





    static public function createPaymentLink(string $resEnglishName, string $userToken, string $trackingId, int $amount, string $itemType, array $items):array{
        $resData = DB::table("restaurants")
            ->where('english_name', $resEnglishName);


        // check user is valid and get it's phone and name
        $userInfo = DB::table("users")->where("token", $userToken);
        $costumer_phone = $userInfo->value(DN::USERS["phone"]);
        $costumer_name = $userInfo->value(DN::USERS["name"]);
        if(strlen($costumer_phone) != 11)
            return array('statusCode' => 401, "massage" => "user is not valid");


        // check restaurant is correct
        if (strlen($resData->value(DN::RESTAURANTS["paymentKey"])) < 2)
            return array('statusCode' => 400, "massage" => "restaurant wasn't found");


        $paymentIdType = "x";
        if($itemType == "food"){
            $paymentIdType = "f";
            $foodsFullInfo = self::getFoodInfo($items);
            $items = $foodsFullInfo;
        }else if($itemType == "plan"){
            $paymentIdType = "p";
        }



        $previousPaidInfo = self::getPreviousPaidInfo($trackingId);


        if($previousPaidInfo['wasPaidTotal'])
            return array('statusCode'=>409, "massage" => "all ware paid");

        $paymentLastNum = $previousPaidInfo['paymentLastNum'];

        $paymentNum = (($paymentLastNum > 0) ? ($paymentLastNum+1) : 1 );
        $paymentBaseId = (strlen($previousPaidInfo['paymentBaseId']) > 5) ?
            $previousPaidInfo['paymentBaseId'] :
            ("cuki".$paymentIdType."-".$resData->value(DN::RESTAURANTS["paymentKey"]) ."-".CusStFunc::randomStringLower(4));

        $paymentId = $paymentBaseId ."-". $paymentNum;

        $api_key = env("PAYPING_KEY");


        // for test:
        if($resData->value(DN::RESTAURANTS["eName"]) == "cuki"){
            $amount = 1000;
            $api_key = $resData->value(DN::RESTAURANTS["ipgToken"]);
        }



        $info_params = array(
            "amount" => $amount,
            "payerIdentity" => $costumer_phone,
            "payerName" => $costumer_name,
            "description"=>"",
            "clientRefId"=>$paymentId,
            "returnUrl" => self::$URLReturnIPG,
        );


        $result = Http::withToken($api_key)->post(self::$URLCreatePayment,$info_params)->json();
        $payPingCode = $result['code'] ?? "";

//        return array("statusCode"=>200,"test"=>"x");


        $sqlInsert_createPaymentParams = array(
            'ipg'=>"payping",
            'tracking_id'=>$trackingId,
            'payment_id'=>$paymentId,
            'payment_group'=>$paymentBaseId,
            'payment_num'=>$paymentNum,
            'payment_key'=>$resData->value(DN::RESTAURANTS["paymentKey"]),
            'item_type'=>$itemType,
            'item'=>json_encode($items),
            'payer_phone'=>$costumer_phone,
            'payer_name'=>$costumer_name,
            'amount'=>$amount,
            'status'=>'0',
            DN::CA =>time(),
            DN::UA =>time(),
        );


        if(DB::table(DN::tables["PAYMENTS"])->insert($sqlInsert_createPaymentParams)){
            if(strlen($payPingCode) > 2){
                if(DB::table(DN::tables["PAYMENTS"])->where(DN::PAYMENTS["paymentId"],$paymentId)->update(array(DN::PAYMENTS["paypingCode"]=>$payPingCode,DN::UA =>time()))){
                    return array(
                        'statusCode'=>200,
                        "data"=>array(
                            "url"=>self::$URLPaymentLinkBase.$payPingCode,
                            "amount"=>$amount,
                            "paymentId"=>$paymentId,
                            "totalPaid"=>$previousPaidInfo['paidSum'],
                            "totalPrice"=>$previousPaidInfo['totalPrice'],
                        ));
                }else{
                    return array('statusCode'=>500);
                }
            }else{
                return array('statusCode'=>408, "massage" => "something went wrong during getting payment link");
            }
        }else{
            return array('statusCode'=>500, "massage" => "something went wrong during saving payment in our database");
        }
    }

    public static function verifyPayment(string $code, string $refid, string $clientrefid, ?string $cardnumber, ?string $cardhashpan):array{

        $paymentKey = explode("-",$clientrefid)[1];

        $resData = DB::table(DN::tables["RESTAURANTS"])
            ->where([[DN::RESTAURANTS["paymentKey"],$paymentKey],[DN::RESTAURANTS["position"], 'admin']]);

        $resDatabaseName = $resData->value(DN::RESTAURANTS["DBName"]);

        Config::set('database.connections.resConn.database', $resDatabaseName);


        // get payment info
        $payment = DB::table(DN::tables["PAYMENTS"])->where(DN::PAYMENTS["paymentId"],$clientrefid);


        // check if foods r not paid
        // if its paid before, dont verify payment and give back money (it will be done after about 10 min)
        if(($payment->value(DN::PAYMENTS["itemType"]) == "food") && self::isFoodsPaid(json_decode(json_encode($payment->first()),true), $payment->value(DN::PAYMENTS["paymentGroup"]), $payment->value(DN::PAYMENTS["trackingId"]))){
            return array('statusCode'=>409,"url"=>self::$URLPaymentResult."?".
                "statusCode=409".
                "&amount=".$payment->value(DN::PAYMENTS["amount"]).
                "&paymentId=".$clientrefid.
                "&trackingId=".$payment->value(DN::PAYMENTS["trackingId"]).
                "&itemType=".$payment->value(DN::PAYMENTS["itemType"]).
                "&item=".$payment->value(DN::PAYMENTS["item"])
            );
        }


        $api_key = $resData->value(DN::RESTAURANTS["ipgToken"]);
        if(strlen($api_key) < 10){
            $api_key = env("PAYPING_KEY");
        }

        $info_params = array(
            "refId" => $refid,
            'amount'=> $payment->value(DN::PAYMENTS["amount"]),
        );

        $result = Http::withToken($api_key)->post(self::$URLVerifyPayment,$info_params)->json();


        $verifyCardNumber = $result['cardNumber'] ?? null;
        $verifyCardHash = $result['cardHashPan'] ?? null;
        $verifyAmount = $result['amount'] ?? null;


        //check payment is valid and its not duplicate
        if(strlen($verifyCardHash) > 1 && ($payment->value(DN::PAYMENTS["verifiedAt"]) < 1000) && ($payment->value(DN::PAYMENTS["amount"]) == $verifyAmount)){
            $sqlUpdate_paymentPaidParams = array(
                DN::PAYMENTS["verifiedAt"]=>time(),
                DN::PAYMENTS["payerCard"]=>$verifyCardNumber,
                DN::PAYMENTS["payerCardHash"]=>$verifyCardHash,
            );
            if($payment->update($sqlUpdate_paymentPaidParams)){
                if($payment->value(DN::PAYMENTS["itemType"]) == "food"){
                    if(self::foodPaid($payment)){
                        return array('statusCode'=>200,"url"=>self::$URLPaymentResult."?".
                            "statusCode=200".
                            "&amount=".$payment->value(DN::PAYMENTS["amount"]).
                            "&paymentId=".$clientrefid.
                            "&trackingId=".$payment->value(DN::PAYMENTS["trackingId"]).
                            "&itemType=".$payment->value(DN::PAYMENTS["itemType"]).
                            "&item=".$payment->value(DN::PAYMENTS["item"])
                        );
                    }else{
                        return array('statusCode'=>500,"url"=>self::$URLPaymentResult."?".
                            "statusCode=500".
                            "&details=item couldn't be saved as paid in restaurant".
                            "&amount=".$payment->value(DN::PAYMENTS["amount"]).
                            "&paymentId=".$clientrefid.
                            "&trackingId=".$payment->value(DN::PAYMENTS["trackingId"]).
                            "&itemType=".$payment->value(DN::PAYMENTS["itemType"]).
                            "&item=".$payment->value(DN::PAYMENTS["item"])
                        );
                    }
                }else{
                    return array('statusCode'=>200,"url"=>self::$URLPaymentResult."?".
                        "statusCode=200".
                        "&amount=".$payment->value(DN::PAYMENTS["amount"]).
                        "&paymentId=".$clientrefid.
                        "&trackingId=".$payment->value(DN::PAYMENTS["trackingId"]).
                        "&itemType=".$payment->value(DN::PAYMENTS["itemType"]).
                        "&item=".$payment->value(DN::PAYMENTS["item"])
                    );
                }
            }else{
                return array('statusCode'=>500,"url"=>self::$URLPaymentResult."?".
                    "statusCode=500".
                    "&details=payment couldn't be saved on our server".
                    "&amount=".$payment->value(DN::PAYMENTS["amount"]).
                    "&paymentId=".$clientrefid.
                    "&trackingId=".$payment->value(DN::PAYMENTS["trackingId"]).
                    "&itemType=".$payment->value(DN::PAYMENTS["itemType"]).
                    "&item=".$payment->value(DN::PAYMENTS["item"])
                );
            }
        }else{
            return array('statusCode'=>402,"url"=>self::$URLPaymentResult."?".
                "statusCode=402".
                "&details=payment is not valid or it's duplicate or was canceled".
                "&amount=".$payment->value(DN::PAYMENTS["amount"]).
                "&paymentId=".$clientrefid.
                "&trackingId=".$payment->value(DN::PAYMENTS["trackingId"]).
                "&itemType=".$payment->value(DN::PAYMENTS["itemType"]).
                "&item=".$payment->value(DN::PAYMENTS["item"])
            );
        }

    }



    public static function foodPaid($payment):bool{
        $trackingId = $payment->value(DN::PAYMENTS["trackingId"]);

        // get order info
        $order = DB::connection("resConn")->table("orders")->where('tracking_id',$trackingId);

        $paymentIdsArr = json_decode($order->value(DN::resORDERS["paymentIds"]),true) ?? array();
        array_push($paymentIdsArr, $payment->value(DN::PAYMENTS["paymentId"]));

        $paidFoods =  $order->value(DN::resORDERS["paidFoods"]) ?? "[]";
        $newPaidFoodsArr = array_merge(json_decode($paidFoods,true), json_decode($payment->value(DN::PAYMENTS["item"]),true));

        $paidAmount = $order->value(DN::resORDERS["paidAmount"]) ?? 0;
        $newPaidAmount = $paidAmount + $payment->value(DN::PAYMENTS["amount"]);

        $updatedOrder = array(
            'payment_ids'=>$paymentIdsArr,
            'paid_foods'=>$newPaidFoodsArr,
            'paid_amount'=>$newPaidAmount,
        );

        if($order->update($updatedOrder)){
            return true;
        }else{
            return false;
        }

    }



    protected static function isFoodsPaid (array $currentPaymentInfo, $paymentGroupKey, $trackingId):bool{
        $isItemOverPaid = false;

        $order = DB::connection("resConn")->table(DN::resTables["resORDERS"])->where(DN::resORDERS["trackingId"], $trackingId);

        // add current payment to payments then calculate
        // it means imagine this payment is confirmed then what would happen? would items over paid?
        $previousPayments = json_decode(json_encode(DB::table("payments")->where([[DN::PAYMENTS["paymentGroup"],$paymentGroupKey],[DN::PAYMENTS["verifiedAt"],">", 1000]])->get()),true);
        $allPaidPaymentsInGroup = array_merge(
            array($currentPaymentInfo),
            ($previousPayments ?? array()));

        $orderedFoodsList = json_decode($order->value(DN::resORDERS["items"]),true);
        // check if number of payed food will be more than ordered ones
        foreach ($allPaidPaymentsInGroup as $ePPayment){
            $ePPayment['item'] = json_decode($ePPayment['item'],true);
            foreach ($ePPayment[DN::PAYMENTS["item"]] as $ePPFood){
                for($i = 0; $i < count($orderedFoodsList); $i++){
                    if($orderedFoodsList[$i]["id"] == $ePPFood["id"]){
                        $orderedFoodsList[$i]['number'] = $orderedFoodsList[$i]['number'] -  $ePPFood['number'];
                    }
                    // check if its over paid
                    if($orderedFoodsList[$i]['number'] < 0){
                        $isItemOverPaid = true;
                        break;
                    }
                }
            }
        }

        return $isItemOverPaid;
    }


    protected static function getFoodInfo($foods_list):array{
        $orderedFood = array();

        $all_foods = json_decode(json_encode(DB::connection("resConn")->table(DN::resTables["resFOODS"])->get()),true);

        foreach ($foods_list as $eachOrderedFood){
            foreach ($all_foods as $eachFood){
                if ($eachOrderedFood['id'] == $eachFood['id']) {
                    $priceAfterDiscount = $eachFood[DN::resFOODS["price"]] * ((100 - $eachFood[DN::resFOODS["discount"]])/100);
                    $eachOrderedFood_newArray = array(
                        'id'=>$eachOrderedFood['id'],
                        'persianName'=>$eachFood[DN::resFOODS["pName"]],
                        'number'=>$eachOrderedFood['number'],
                        'price'=>$eachFood[DN::resFOODS["price"]],
                        'discount'=>$eachFood[DN::resFOODS["discount"]],
                        'priceAfterDiscount'=>$priceAfterDiscount
                    );
                    array_push($orderedFood, $eachOrderedFood_newArray);
                }
            }
        }
        return $orderedFood;
    }

    protected static function getPreviousPaidInfo($trackingId):array{
        $payments = DB::table("payments")->where(DN::PAYMENTS["trackingId"],$trackingId);
        // get order info
        $orderInfo = DB::connection("resConn")
            ->table(DN::resTables["resORDERS"])
            ->where(DN::resORDERS["trackingId"], $trackingId);

        if(!$payments->exists()){
            return array(
                "wasPaidTotal"=>false,
                "paidSum"=>0,
                "totalPrice"=>$orderInfo->value(DN::resORDERS["tPrice"]) ?? 999999999,
                'paymentBaseId'=>"",
                'paymentLastNum'=>0
            );
        }

        $lastPaymentNum = $payments->max(DN::PAYMENTS["paymentNum"]);
        $paymentBaseId = $payments->value(DN::PAYMENTS["paymentGroup"]);
        $paidSum = $payments->where(DN::PAYMENTS["verifiedAt"], ">", "100")->sum(DN::PAYMENTS["amount"]);


        // check if order was paid dont open new payment
        $wasPaidTotal = false;
        if($orderInfo->value(DN::resORDERS["tPrice"]) <= $paidSum && $paidSum > 999)
            $wasPaidTotal = true;

        return array(
            "wasPaidTotal"=>$wasPaidTotal,
            "paidSum"=>$paidSum,
            "totalPrice"=>$orderInfo->value(DN::resORDERS["tPrice"]),
            'paymentBaseId'=>$paymentBaseId,
            'paymentLastNum'=>$lastPaymentNum
        );
    }


}
