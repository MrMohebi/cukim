<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('info', function (Blueprint $table) {
            $table->id();
            $table->string("status")->default("open");
            $table->string("persian_name");
            $table->string("english_name");
            $table->string("counter_phone")->nullable();
            $table->json("phones")->nullable();
            $table->string("address")->nullable();
            $table->string("address_link")->nullable();
            $table->string("owner")->nullable();
            $table->json("employers")->nullable();
            $table->json("social_links")->nullable();
            $table->json("open_time")->default([[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]]);
            $table->json("type")->nullable();
            $table->decimal("rate")->nullable();
            $table->string("logo_link")->default("https://dl.cuki.ir/resimg/cuki/logo/logoX256.png");
            $table->string("favicon_link")->default("https://dl.cuki.ir/resimg/cuki/favicon/faviconX64.png");
            $table->integer("medium_order_price");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('info');
    }
}
