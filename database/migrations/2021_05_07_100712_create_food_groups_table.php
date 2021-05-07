<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_groups', function (Blueprint $table) {
            $table->id();
            $table->tinyText("persian_name");
            $table->tinyText("english_name");
            $table->tinyText("logo")->nullable();
            $table->tinyText("status")->default("active");
            $table->integer("rank")->default(0);
            $table->tinyText("type")->default("restaurant");
            $table->tinyText("average_color")->nullable();
            $table->tinyText("res_english_name")->default("general");
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
        Schema::dropIfExists('food_groups');
    }
}
