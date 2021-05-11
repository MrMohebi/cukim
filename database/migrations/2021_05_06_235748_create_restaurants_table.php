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
            $table->string('username');
            $table->string("password");
            $table->string('position');
            $table->string('persian_name');
            $table->string('english_name');
            $table->smallInteger('res_code');
            $table->string('token');
            $table->json('phones');
            $table->json('permissions');
            $table->string('payment_key');
            $table->string('db_name');
            $table->string('ipg_name');
            $table->string('ipg_token');
            $table->string('ipg_data');
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
