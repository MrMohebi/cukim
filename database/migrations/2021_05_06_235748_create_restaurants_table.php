<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->tinyText('username');
            $table->text("password");
            $table->tinyText('position');
            $table->tinyText('persian_name');
            $table->tinyText('english_name');
            $table->smallInteger('res_code');
            $table->tinyText('token');
            $table->tinyText('phone');
            $table->tinyText('payment_key');
            $table->tinyText('db_name');
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
        Schema::dropIfExists('restaurants');
    }
}
