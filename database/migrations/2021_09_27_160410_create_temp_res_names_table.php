<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempResNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_res_names', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('res_owner_id');
            $table->foreign('res_owner_id')->references('id')->on('res_owners');
            $table->string("persianName");
            $table->string("englishName");
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
        Schema::dropIfExists('temp_res_names');
    }
}
