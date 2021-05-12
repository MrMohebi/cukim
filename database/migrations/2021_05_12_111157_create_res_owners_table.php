<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResOwnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_owners', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string("password");
            $table->string("name");
            $table->string('token');
            $table->json("restaurants_ids")->nullable();
            $table->json("payment_ids")->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->mediumInteger('verification_code')->nullable();
            $table->tinyInteger('verification_code_tries')->nullable();
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
        Schema::dropIfExists('res_users');
    }
}
