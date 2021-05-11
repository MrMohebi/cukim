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
            $table->string("res_english_name");
            $table->string("payment_key");
            $table->string("details");
            $table->bigInteger("online_till_now");
            $table->bigInteger("offline_till_now");
            $table->bigInteger("online_from_previous");
            $table->bigInteger("offline_from_previous");
            $table->bigInteger("to_pay");
            $table->string("status")->default("created-notPaid");
            $table->timestamp("paid_at");
            $table->bigInteger("paid_amount");
            $table->string("res_card_number");
            $table->string("our_card_number");
            $table->string("bank_tracking_id");
            $table->string("creator_support_name");
            $table->string("creator_support_id");
            $table->string("payer_support_name");
            $table->string("payer_support_id");
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
