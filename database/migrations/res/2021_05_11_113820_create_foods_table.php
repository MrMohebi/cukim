<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string("counter_app_food_id")->nullable();
            $table->string("persian_name");
            $table->string("english_name");
            $table->string("group");
            $table->integer("group_id");
            $table->json("details");
            $table->integer("price");
            $table->string("status");
            $table->bigInteger("order_times");
            $table->tinyInteger("discount");
            $table->smallInteger("delivery_time");
            $table->string("thumbnail");
            $table->json("photos");
            $table->string("model3d");
            $table->string("related_main_persian_name")->nullable();
            $table->string("related_main_english_name")->nullable();
            $table->json("related_price_range")->nullable();
            $table->string("related_thumbnail")->nullable();
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
        Schema::dropIfExists('foods');
    }
}
