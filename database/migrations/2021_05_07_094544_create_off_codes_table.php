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
            $table->bigInteger('from')->nullable();
            $table->bigInteger('to')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('off_codes');
    }
}
