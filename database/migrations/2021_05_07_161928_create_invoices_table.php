<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->tinyText("res_english_name");
            $table->tinyText("payment_key");
            $table->text("details");
            $table->bigInteger("online_till_now");
            $table->bigInteger("offline_till_now");
            $table->bigInteger("online_from_previous");
            $table->bigInteger("offline_from_previous");
            $table->bigInteger("to_pay");
            $table->string("status")->default("created-notPaid");
            $table->timestamp("paid_at");
            $table->bigInteger("paid_amount");
            $table->tinyText("res_card_number");
            $table->tinyText("our_card_number");
            $table->tinyText("bank_tracking_id");
            $table->tinyText("creator_support_name");
            $table->tinyText("creator_support_id");
            $table->tinyText("payer_support_name");
            $table->tinyText("payer_support_id");
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
        Schema::dropIfExists('invoices');
    }
}
