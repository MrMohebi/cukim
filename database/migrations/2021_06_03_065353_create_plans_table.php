<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('persian_name');
            $table->string('english_name');
            $table->json("items")->nullable();
            $table->string('details')->nullable();
            $table->integer("price")->default(999999999);
            $table->smallInteger("discount_percentage")->default(0);
            $table->integer("discount_amount")->default(0);
            $table->bigInteger("buy_times")->default(0);
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
        Schema::dropIfExists('plans');
    }
}
