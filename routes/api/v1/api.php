<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\resOwner\v1\res;
use App\Http\Controllers\api\resOwner\v1\signup;
use App\Http\Controllers\createCategory;

Route::prefix("/cuki")->group(function (){

});

Route::prefix("/res")->group(function (){
    Route::post('createCategory',[createCategory::class,['createCategory']]);
    Route::post('createFood',[createCategory::class,['createCategory']]);
});

Route::prefix("/resOwner")->group(function (){
    Route::group(["middleware"=>['resOwnerToken']], function (){
        Route::post("/createNewRes",[res::class,"createNewRes"]);
    });

    Route::post("/signup",[signup::class,"signup"]);
});

Route::prefix("admin")->group(function (){
 Route::post('login',[login::class,'login']);
});

Route::prefix("/qr")->group(function (){

});

Route::prefix("/pay")->group(function (){

});
