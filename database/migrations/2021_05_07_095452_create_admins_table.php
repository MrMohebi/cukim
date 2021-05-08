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
            $table->tinyText("username");
            $table->text("password");
            $table->tinyText("email")->nullable();
            $table->tinyText("name")->nullable();
            $table->tinyText("position");
            $table->tinyText("phone");
            $table->string("status")->default("active");
            $table->text("token");
            $table->timestamp("last_login");
            $table->tinyText("promoted_by");
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
        Schema::dropIfExists('admins');
    }
}
