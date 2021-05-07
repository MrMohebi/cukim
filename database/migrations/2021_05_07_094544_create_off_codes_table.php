<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('off_codes', function (Blueprint $table) {
            $table->id();
            $table->tinyText("code");
            $table->tinyText("creator");
            $table->tinyText("target");
            $table->tinyText("place")->nullable();
            $table->smallInteger("times")->default(0);
            $table->smallInteger("used")->default(0);
            $table->mediumInteger("max_amount")->default(0);
            $table->mediumInteger("min_amount")->default(0);
            $table->smallInteger("discount_percentage")->default(0);
            $table->mediumInteger("discount_amount")->default(0);
            $table->tinyText("name")->nullable();
            $table->tinyText("body")->nullable();
            $table->timestamp('from')->default(0);
            $table->timestamp('to')->default(0);
            $table->tinyText('status')->nullable();
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
        Schema::dropIfExists('off_codes');
    }
}
