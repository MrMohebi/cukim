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
            $table->json("details")->nullable();
            $table->integer("price");
            $table->string("status")->default("outOfStock");
            $table->bigInteger("order_times")->default(0);
            $table->tinyInteger("discount")->default(0);
            $table->smallInteger("delivery_time")->default(0);
            $table->string("thumbnail")->default("https://dl.cuki.ir/sampleAssets/sampleThumbnailBurger2_96x96.png");
            $table->json("photos")->nullable();
            $table->string("model3d")->nullable();
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
