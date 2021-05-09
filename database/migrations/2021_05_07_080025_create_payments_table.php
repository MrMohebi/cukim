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
            $table->tinyText("tracking_id");
            $table->bigInteger("amount");
            $table->tinyText("status")->nullable();
            $table->json("item")->nullable();
            $table->tinyText("item_type")->nullable();
            $table->tinyText("payment_id");
            $table->tinyText("payment_key");
            $table->tinyText("payment_code");
            $table->tinyText("payment_group");
            $table->smallInteger("payment_num");
            $table->tinyText("ipg")->nullable();
            $table->tinyText("details")->nullable();
            $table->tinyText("payer_phone")->nullable();
            $table->tinyText("payer_name")->nullable();
            $table->tinyText("payer_card")->nullable();
            $table->text("payer_card_hash")->nullable();
            $table->tinyText("payping_code")->nullable();
            $table->timestamp("paid_at")->nullable();
            $table->timestamp("verified_at")->nullable();
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
        Schema::dropIfExists('payments');
    }
}
