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
            $table->string("code");
            $table->string("creator");
            $table->string("target");
            $table->string("place")->nullable();
            $table->smallInteger("times")->default(0);
            $table->smallInteger("used")->default(0);
            $table->mediumInteger("max_amount")->default(0);
            $table->mediumInteger("min_amount")->default(0);
            $table->smallInteger("discount_percentage")->default(0);
            $table->mediumInteger("discount_amount")->default(0);
            $table->string("name")->nullable();
            $table->string("body")->nullable();
            $table->timestamp('from');
            $table->timestamp('to');
            $table->string('status')->nullable();
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
