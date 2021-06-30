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
            $table->string('name')->nullable();
            $table->string('phone');
            $table->bigInteger('birthday')->nullable();
            $table->string('status')->default("active");
            $table->string('job')->nullable();
            $table->bigInteger('amount')->default(0);
            $table->json('off_codes')->nullable();
            $table->string('token')->nullable();
            $table->text('info')->nullable();
            $table->string('type')->nullable();
            $table->json('favorite_places')->nullable();
            $table->mediumInteger('verification_code')->nullable();
            $table->tinyInteger('verification_code_tries')->nullable();
            $table->json('payments')->nullable();
            $table->json('orders')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
