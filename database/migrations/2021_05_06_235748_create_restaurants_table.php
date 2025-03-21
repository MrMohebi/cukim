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
            $table->smallInteger('res_code')->nullable();
            $table->string('token');
            $table->string('owner_id');
            $table->string('owner_name');
            $table->json('permissions')->nullable();
            $table->json('permissions_disable')->nullable();
            $table->json('disable_permissions')->nullable();
            $table->string('payment_key');
            $table->string('db_name');
            $table->string('ipg_name')->nullable();
            $table->string('ipg_token')->nullable();
            $table->string('ipg_data')->nullable();
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
        Schema::dropIfExists('restaurants');
    }
}
