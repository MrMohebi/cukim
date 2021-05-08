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
            $table->string("status")->default("active");
            $table->integer("rank")->default(0);
            $table->string("type")->default("restaurant");
            $table->tinyText("average_color")->nullable();
            $table->string("res_english_name")->default("general");
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
