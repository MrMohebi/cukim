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
            $table->string("theme")->default("default");
            $table->json("employers")->nullable();
            $table->json("social_links")->nullable();
            $table->json("open_time")->nullable();
            $table->json("type")->nullable();
            $table->decimal("rate")->nullable();
            $table->string("logo_link")->default("https://dl.cuki.ir/resimg/cuki/logo/logoX256.png");
            $table->string("favicon_link")->default("https://dl.cuki.ir/resimg/cuki/favicon/faviconX64.png");
            $table->integer("minimum_order_price")->nullable();
            $table->bigInteger("created_at")->nullable();
            $table->bigInteger("deleted_at")->nullable();
            $table->bigInteger("updated_at")->nullable();
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
