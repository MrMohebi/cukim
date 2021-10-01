<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQrReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qr_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qr_id');
            $table->foreign('qr_id')->references('id')->on('qrs');
            $table->string("source");
            $table->bigInteger("scan_at");
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
        Schema::dropIfExists('qr_reports');
    }
}
