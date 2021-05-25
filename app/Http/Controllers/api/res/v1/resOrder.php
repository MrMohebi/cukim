<?php

namespace App\Http\Controllers\api\res\v1;

use App\CustomFunctions\CusStFunc;
use App\DatabaseNames\DN;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class resOrder extends Controller
{
    public function changeOrderStatus(Request $request){
        $validator = Validator::make($request->all(), [
            "trackingId" => "required|min:8",
            "status"=> "required|min:3",
        ]);

        if ($validator->fails())
            return response(array( 'message' => $validator->errors()->all(),'statusCode' => 400),400);

        $trackingId = $request->input("trackingId");
        $newOrderStatus = $request->input("status");
        $deleteReason = ($newOrderStatus == "deleted") ? $request->input("deleteReason") : "";
//        $deliveryId = ($newOrderStatus == "delivered") ? $request->input("deliveryId") : "";


        // get order info and user phone
        $order = DB::connection("resConn")->table(DN::resTables["resORDERS"])->where(DN::resORDERS["trackingId"], $trackingId);

        if(!$order->exists())
            return response(array( 'message' => "tracking id is incorrect",'statusCode' => 404),404);


        if($order->value(DN::resORDERS["status"]) != $newOrderStatus) {
            $sqlOrderInfoUpdateParams = array(
                DN::resORDERS["status"]=>$newOrderStatus,
                DN::resORDERS["deleteReason"]=>$deleteReason,
                DN::UA=>time(),
            );

            if ($order->update($sqlOrderInfoUpdateParams)) {

                // create new customer if it doesn't exist
                $customer = DB::connection("resConn")->table(DN::resTables["resCUSTOMERS"])->where(DN::resCUSTOMERS["phone"], $order->value(DN::resORDERS["userPhone"]));
                if(!$customer->exists()){
                    self::createCustomer($order->value(DN::resORDERS["userPhone"]));
                    $customer = DB::connection("resConn")->table(DN::resTables["resCUSTOMERS"])->where(DN::resCUSTOMERS["phone"], $order->value(DN::resORDERS["userPhone"]));
                }

                // add tracking id to customer history if it doesn't exist
                $orderList = json_decode($customer->value(DN::resCUSTOMERS["orderList"])) ?? [];
                if(!in_array($trackingId, $orderList)){
                    $orderList[] = $trackingId;
                    $customer->update([DN::resCUSTOMERS["orderList"]=>$orderList]);
                }

                // add order info to customer info
                if($newOrderStatus == "done"){
                    self::addOrderCustomer($order, $customer);
                    self::increaseOrderTimesOfFoods($order);
                }elseif (in_array($newOrderStatus, ["inLine", "deleted"])){
                    self::removeOrderCustomer($order,$customer);
                }
                return response(array('data' => ["newStatus"=>$newOrderStatus, "trackingId"=>$trackingId],'statusCode' => 200));


            } else {
                return response(array( 'message' => "something went wrong during changing order status",'statusCode' => 500),500);
            }
        }else{
            return response(array( 'message' => "new order status is like its previous one",'statusCode' => 400),400);
        }
    }

    public function getOrderList(Request $request){
        $validator = Validator::make($request->all(), [
            "startDate" => "required|numeric",
        ]);

        if ($validator->fails())
            return response(array( 'message' => $validator->errors()->all(),'statusCode' => 400),400);

        $startDate = $request->input("startDate");
        $endDate = $request->input("endDate") ?? time();

        // check dates are correct
        if($startDate > $endDate)
            return response(array( 'message' => "input dates are incorrect",'statusCode' => 400),400);


        $ordersList = DB::connection("resConn")->table(DN::resTables["resORDERS"])->whereBetween(DN::CA, [$startDate, $endDate]);

        return response(array('statusCode'=>200, 'data'=>$ordersList ? CusStFunc::arrayKeysToCamel(json_decode(json_encode($ordersList->get()),true)) : array()));
    }


    static protected function createCustomer($phone):bool{
        return DB::connection("resConn")
            ->table(DN::resTables["resCUSTOMERS"])
            ->insert([
                DN::resCUSTOMERS["phone"]=>$phone,
                DN::CA=>time(),
                DN::UA=>time(),
            ]);
    }

    static protected function removeOrderCustomer($order, $customer):bool{
        return $customer->update([
            DN::resCUSTOMERS["orderTimes"] => DB::raw( DN::resCUSTOMERS["orderTimes"].' - ' . 1 ),
            DN::resCUSTOMERS["score"] => DB::raw( DN::resCUSTOMERS["score"].' - ' . $order->value(DN::resORDERS["tPrice"])/1000 ),
            DN::resCUSTOMERS["tOrderedPrice"] => DB::raw( DN::resCUSTOMERS["tOrderedPrice"].' - ' . $order->value(DN::resORDERS["tPrice"])),
            DN::UA => time()
        ]);
    }

    static protected function addOrderCustomer($order, $customer):bool{
        return $customer->update([
            DN::resCUSTOMERS["orderTimes"] => DB::raw( DN::resCUSTOMERS["orderTimes"].' + ' . 1 ),
            DN::resCUSTOMERS["score"] => DB::raw( DN::resCUSTOMERS["score"].' + ' . $order->value(DN::resORDERS["tPrice"])/1000 ),
            DN::resCUSTOMERS["tOrderedPrice"] => DB::raw( DN::resCUSTOMERS["tOrderedPrice"].' + ' . $order->value(DN::resORDERS["tPrice"])),
            DN::UA => time()
        ]);
    }

    static protected function increaseOrderTimesOfFoods($order):bool{
        $orderList = json_decode($order->value(DN::resORDERS["items"]), true);
        foreach ($orderList as $eachFood){
            DB::connection('resConn')
                ->table(DN::resTables["resFOODS"])
                ->where("id",$eachFood["id"])
                ->increment(DN::resFOODS["orderTimes"],$eachFood["number"]);
        }
        return true;
    }

}
