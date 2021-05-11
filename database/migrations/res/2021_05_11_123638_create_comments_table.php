<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string("phone");
            $table->string("name");
            $table->string("tracking_id");
            $table->integer("food_id");
            $table->string("title")->nullable();
            $table->string("body");
            $table->tinyInteger("rate")->nullable();
            $table->string("order_type")->nullable();
            $table->json("pros_cons")->nullable();
            $table->string("status")->default("notConfirmed");
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
        Schema::dropIfExists('comments');
    }
}
