<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string("username");
            $table->string("password");
            $table->string("email")->nullable();
            $table->string("name")->nullable();
            $table->string("position");
            $table->string("phone");
            $table->string("status")->default("active");
            $table->string("token");
            $table->bigInteger("last_login");
            $table->string("promoted_by");
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
        Schema::dropIfExists('admins');
    }
}
