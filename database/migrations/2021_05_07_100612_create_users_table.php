<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->tinyText('name')->nullable();
            $table->tinyText('phone')->unique();
            $table->timestamp('birthday')->nullable();
            $table->tinyText('status')->default("active");
            $table->tinyText('job')->nullable();
            $table->bigInteger('amount')->default(0);
            $table->json('off_codes')->nullable();
            $table->tinyText('token')->nullable();
            $table->tinyText('info')->nullable();
            $table->tinyText('type')->nullable();
            $table->json('favorite_places')->nullable();
            $table->mediumInteger('verification_code')->nullable();
            $table->tinyInteger('verification_code_tries')->nullable();
            $table->json('payments')->nullable();
            $table->json('orders')->nullable();
            $table->text('password')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
