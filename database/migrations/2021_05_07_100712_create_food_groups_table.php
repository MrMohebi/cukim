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
            $table->string("persian_name");
            $table->string("english_name");
            $table->string("logo")->nullable();
            $table->string("status")->default("active");
            $table->integer("rank")->default(0);
            $table->string("type")->default("restaurant");
            $table->string("average_color")->nullable();
            $table->string("res_english_name")->default("general");
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
        Schema::dropIfExists('food_groups');
    }
}
