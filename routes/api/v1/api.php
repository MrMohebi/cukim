<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\resOwner\v1\res;

Route::prefix("/cuki")->group(function (){

});

Route::prefix("/res")->group(function (){

});

Route::prefix("/resOwner")->group(function (){
    Route::post("/createNewRes",[res::class,"createNewRes"]);
});

Route::prefix("admin")->group(function (){

});

Route::prefix("/qr")->group(function (){

});

Route::prefix("/pay")->group(function (){

});
