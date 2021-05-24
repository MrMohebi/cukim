<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\resOwner\v1\res;
use App\Http\Controllers\api\resOwner\v1\signup;
use App\Http\Controllers\api\resOwner\v1\signin;
use App\Http\Controllers\api\resOwner\v1\smsRO;
use App\Http\Controllers\api\admin\v1\login;
use App\Http\Controllers\api\cuki\v1\resData;
use App\Http\Controllers\api\cuki\v1\user;
use App\Http\Controllers\api\cuki\v1\order;
use App\Http\Controllers\api\cuki\v1\getIpInfo;
use App\Http\Controllers\api\cuki\v1\pager;
use App\Http\Controllers\api\cuki\v1\comment;
use App\Http\Controllers\api\cuki\v1\payment;
use App\Http\Controllers\api\res\v1\resLogin;
use App\Http\Controllers\api\res\v1\resOrder;
use App\Http\Controllers\api\res\v1\food;
use App\Http\Controllers\api\res\v1\category;

Route::prefix("/cuki")->group(function (){
    Route::group(["middleware"=>["cukiToken"]], function () {
        Route::post("/setUserInfo",[user::class,"setUserInfo"]);
        Route::post("/getUserInfo",[user::class,"getUserInfo"]);
        Route::post("/sendOrder",[order::class,"sendOrder"]);
        Route::post("/getOpenOrders",[order::class,"getOpenOrders"]);
        Route::post("/getOrderByTrackingId",[order::class,"getOrderByTrackingId"]);
        Route::post("/getCommentsByFoodId",[comment::class,"getCommentsByFoodId"]);
        Route::post("/sendComment",[comment::class,"sendComment"]);
        Route::post("/getPaymentByTrackingId",[payment::class,"getPaymentByTrackingId"]);
    });

    Route::post("/callPager",[pager::class,"callPager"]);
    Route::post("/getTempToken",[user::class,"getTempToken"]);
    Route::post("/sendVCode",[user::class,"sendVCode"]);
    Route::post("/verifyVCode",[user::class,"verifyVCode"]);
    Route::post("/getResData",[resData::class,"getResData"]);
    Route::post("/getResParts",[resData::class,"getResParts"]);
    Route::post("/getResENameByCode",[resData::class,"getResENameByCode"]);
    Route::get("/getIpInfo",[getIpInfo::class,"getIpInfo"]);

});



Route::prefix("/res")->group(function (){
    Route::group(["middleware"=>["resToken"]], function (){
        Route::post('createCategory',[category::class,'createCategory']);
        Route::post('createFood',[food::class,'createNewFood']);
        Route::post('changeFoodInfo',[food::class,'changeFoodInfo']);
        Route::post('changeOrderStatus',[resOrder::class,'changeOrderStatus']);
    });

    Route::post('login',[resLogin::class,'login']);
});



Route::prefix("/resOwner")->group(function (){
    Route::group(["middleware"=>['resOwnerToken']], function (){
        Route::post("/createNewRes",[res::class,"createNewRes"]);
        Route::post("/sendVCode",[smsRO::class,"sendVCode"]);
        Route::post("/verifyVCode",[smsRO::class,"verifyVCode"]);
    });
    Route::post("/signin",[signin::class,"signin"]);
    Route::post("/signup",[signup::class,"signup"]);
});



Route::prefix("admin")->group(function (){
    Route::post('login',[login::class,'login']);
});



Route::prefix("/qr")->group(function (){

});



Route::prefix("/pay")->group(function (){

});
