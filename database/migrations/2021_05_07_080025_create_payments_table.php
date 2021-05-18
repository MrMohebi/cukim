<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string("tracking_id");
            $table->bigInteger("amount");
            $table->string("status")->nullable();
            $table->json("item")->nullable();
            $table->string("item_type")->nullable();
            $table->string("payment_id");
            $table->string("payment_key");
            $table->string("payment_code")->nullable();
            $table->string("payment_group");
            $table->smallInteger("payment_num");
            $table->string("ipg")->nullable();
            $table->string("details")->nullable();
            $table->string("payer_phone")->nullable();
            $table->string("payer_name")->nullable();
            $table->string("payer_card")->nullable();
            $table->string("payer_card_hash")->nullable();
            $table->string("payping_code")->nullable();
            $table->bigInteger("paid_at")->nullable();
            $table->bigInteger("verified_at")->nullable();
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
        Schema::dropIfExists('payments');
    }
}
