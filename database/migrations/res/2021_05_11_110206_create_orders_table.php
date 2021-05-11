<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string("tracking_id");
            $table->string("user_phone");
            $table->string("order_status")->default("inLine");
            $table->json("items");
            $table->integer("delivery_price");
            $table->json("address")->nullable();
            $table->string("table")->nullable();
            $table->json("details")->nullable();
            $table->integer("total_price");
            $table->timestamp("delivered_at")->nullable();
            $table->timestamp("delivery_at")->nullable();
            $table->string("delete_reason")->nullable();
            $table->string("offcode")->nullable();
            $table->string("how_to_serve")->nullable();
            $table->string("payment_status")->default("notPaid");
            $table->json("paid_foods")->nullable();
            $table->integer("paid_amount");
            $table->json("payment_ids")->nullable();
            $table->string("counter_app_status")->default("0");
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
        Schema::dropIfExists('orders');
    }
}
