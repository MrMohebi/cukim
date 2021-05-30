<?php


namespace App\CustomClasses\Ipg;
use App\CustomClasses\AbstractClasses\Ipg;
use App\DatabaseNames\DN;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\CustomFunctions\CusStFunc;

class Payping extends Ipg{

    protected static $URLCreatePayment = "https://api.payping.ir/v2/pay";
    protected static $URLPaymentLinkBase = "https://api.payping.ir/v2/pay/gotoipg/";
    protected static $URLVerifyPayment = "https://api.payping.ir/v2/pay/verify";
    protected static $URLReturnIPG = "https://cukim.ir/api/v1/pay/NOT_SET_YET";
    protected static $URLPaymentResult = "https://cukim.ir/api/v1/pay/NOT_SET_YET";



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
        }



        $previousPaidInfo = self::getPreviousPaidInfo($trackingId);
        if($previousPaidInfo['wasPaidTotal'])
            return array('statusCode'=>600, "massage" => "all ware paid");

        $paymentLastNum = $previousPaidInfo['paymentLastNum'];

        $paymentNum = (($paymentLastNum > 0) ? ($paymentLastNum+1) : 1 );
        $paymentBaseId = (strlen($previousPaidInfo['paymentBaseId']) > 5) ?
            $previousPaidInfo['paymentBaseId'] :
            ("cuki".$paymentIdType."-".$resData->value(DN::RESTAURANTS["paymentKey"]) ."-".CusStFunc::randomStringLower(4));

        $paymentId = $paymentBaseId ."-". $paymentNum;

        $api_key = $resData->value(DN::RESTAURANTS["ipgToken"]);


        // for test:
        if($resData->value(DN::RESTAURANTS["eName"]) == "cuki"){
            $amount = 1000;
            $api_key = "06b31901f67541d49531245358d48a10ab083854933dde5e0c6da705fdf1c742";
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
        $payPingCode = $result['code'];

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
            'status'=>'0'
        );


        if(DB::table(DN::tables["PAYMENTS"])->insert($sqlInsert_createPaymentParams)){
            if(strlen($payPingCode) > 2){
                if(DB::table(DN::tables["PAYMENTS"])->where(DN::PAYMENTS["paymentId"],$paymentId)->update(array(DN::PAYMENTS["paypingCode"]=>$payPingCode))){
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

    public static function verifyPayment(string $code, string $refid, string $clientrefid, string $cardnumber, string $cardhashpan):array{

        $paymentKey = explode("-",$clientrefid)[1];
        $resEnglishName = DB::table("restaurants")->where(['payment_key',$paymentKey],['position', 'admin'])->first()->value('english_name');

        $resDatabaseName = DB::table("restaurants")
            ->where("english_name", $resEnglishName)
            ->first()
            ->value("db_name");

        Config::set('database.connections.resConn.database', $resDatabaseName);


        $resData = DB::table("restaurants")
            ->where('english_name', $resEnglishName)
            ->first();


        // get payment info
        $paymentInfo = DB::table("payments")->where('payment_id',$clientrefid)->first();


        // check if foods r not paid
        // if its paid before, dont verify payment and give back money (it will be done after about 10 min)
        if(($paymentInfo["item_type"] == "food") && self::isFoodsPaid($paymentInfo, $paymentInfo["payment_group"], $paymentInfo["tracking_id"])){
            return array('statusCode'=>409,"url"=>"location: ".self::$URLPaymentResult."?".
                "statusCode=409".
                "&amount=".$paymentInfo['amount'].
                "&paymentId=".$clientrefid.
                "&trackingId=".$paymentInfo['tracking_id'].
                "&itemType=".$paymentInfo['item_type'].
                "&item=".json_encode($paymentInfo['item'])
            );
        }


        $api_key = $resData['ipg_token'];

        $info_params = array(
            "refId" => $refid,
            'amount'=> $paymentInfo['amount'],
        );

        $result = Http::withToken($api_key)->post(self::$URLVerifyPayment,$info_params);

        $verifyCardNumber = $result['cardNumber'];
        $verifyCardHash = $result['cardHashPan'];
        $verifyAmount = $result['amount'];


        //check payment is valid and its not duplicate
        if(strlen($verifyCardHash) > 10 && ($paymentInfo["verified_at"] < 1000) && ($paymentInfo["amount"] == $verifyAmount)){
            $sqlUpdate_paymentPaidParams = array(
                'verified_at'=>time(),
                'payer_card'=>$verifyCardNumber,
                'payer_card_hash'=>$verifyCardHash,
            );

            if(DB::table("payments")->where('payment_id', $clientrefid)->update($sqlUpdate_paymentPaidParams)){
                if($paymentInfo['item_type'] == "food"){
                    if(self::foodPaid($paymentInfo)){
                        return array('statusCode'=>200,"url"=>"location: ".self::$URLPaymentResult."?".
                            "statusCode=200".
                            "&amount=".$paymentInfo['amount'].
                            "&paymentId=".$clientrefid.
                            "&trackingId=".$paymentInfo['tracking_id'].
                            "&itemType=".$paymentInfo['item_type'].
                            "&item=".json_encode(json_decode($paymentInfo['item']))
                        );
                    }else{
                        return array('statusCode'=>500,"url"=>"location: ".self::$URLPaymentResult."?".
                            "statusCode=500".
                            "&details=item couldn't be saved as paid in restaurant".
                            "&amount=".$paymentInfo['amount'].
                            "&paymentId=".$clientrefid.
                            "&trackingId=".$paymentInfo['tracking_id'].
                            "&itemType=".$paymentInfo['item_type'].
                            "&item=".json_decode($paymentInfo['item'])
                        );
                    }
                }else{
                    return array('statusCode'=>200,"url"=>"location: ".self::$URLPaymentResult."?".
                        "statusCode=200".
                        "&amount=".$paymentInfo['amount'].
                        "&paymentId=".$clientrefid.
                        "&trackingId=".$paymentInfo['tracking_id'].
                        "&itemType=".$paymentInfo['item_type'].
                        "&item=".json_decode($paymentInfo['item'])
                    );
                }
            }else{
                return array('statusCode'=>500,"url"=>"location: ".self::$URLPaymentResult."?".
                    "statusCode=500".
                    "&details=payment couldn't be saved on our server".
                    "&amount=".$paymentInfo['amount'].
                    "&paymentId=".$clientrefid.
                    "&trackingId=".$paymentInfo['tracking_id'].
                    "&itemType=".$paymentInfo['item_type'].
                    "&item=".json_decode($paymentInfo['item'])
                );
            }
        }else{
            return array('statusCode'=>402,"url"=>"location: ".self::$URLPaymentResult."?".
                "statusCode=402".
                "&details=payment is not valid or it's duplicate".
                "&amount=".$paymentInfo['amount'].
                "&paymentId=".$clientrefid.
                "&trackingId=".$paymentInfo['tracking_id'].
                "&itemType=".$paymentInfo['item_type'].
                "&item=".json_decode($paymentInfo['item'])
            );
        }

    }



    public static function foodPaid($paymentInfo):bool{
        $trackingId = $paymentInfo['tracking_id'];

        // get order info
        $orderInfo = DB::connection("resConn")->table("orders")->where('tracking_id',$trackingId);

        $paymentIdsArr = ($orderInfo['payment_ids'] != null) ? $orderInfo['payment_ids'] : array();
        array_push($paymentIdsArr, $paymentInfo['payment_id']);

        $paidFoods = ($orderInfo['paid_foods'] != null) ? $orderInfo['paid_foods'] : array();
        $newPaidFoodsArr = array_merge($paidFoods, $paymentInfo['item']);

        $paidAmount = ($orderInfo['paid_amount'] != null) ? $orderInfo['paid_amount'] : 0;
        $newPaidAmount = $paidAmount + $paymentInfo['amount'];

        $updatedOrder = array(
            'payment_ids'=>$paymentIdsArr,
            'paid_foods'=>$newPaidFoodsArr,
            'paid_amount'=>$newPaidAmount,
        );

        if(DB::connection("resConn")->table("orders")->where('tracking_id',$trackingId)->update($updatedOrder)){
            return true;
        }else{
            return false;
        }

    }



    protected static function isFoodsPaid ($currentPaymentInfo, $paymentGroupKey, $trackingId):bool{
        $isItemOverPaid = false;

        $orderInfo = DB::connection("resConn")->table("orders")->where('tracking_id', $trackingId)->first();


        // add current payment to payments then calculate
        // it means imagine this payment is confirmed then what would happen? would items over paid?
        $allPaidPaymentsInGroup = array_merge(
            array($currentPaymentInfo),
            DB::table("payments")->where(['payment_group',$paymentGroupKey],['verified_at',">", 1000]));

        $orderedFoodsList = $orderInfo['items'];

        // check if number of payed food will be more than ordered ones
        foreach ($allPaidPaymentsInGroup as $ePPayment){
            foreach ($ePPayment['item'] as $ePPFood){
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
        $paymentBaseId = $payments->first()->value(DN::PAYMENTS["paymentGroup"]);
        $paidSum = $payments->where(DN::PAYMENTS["verifiedAt"], ">", "100")->sum(DN::PAYMENTS["amount"]);


        // check if order was paid dont open new payment
        $wasPaidTotal = false;
        if($orderInfo->value(DN::resORDERS["tPrice"]) <= $paidSum)
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
