<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLawSubTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('law_sub', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('law_id');
            $table->foreign('law_id')
                  ->references('id')->on('lawsuits')
                  ->onDelete('cascade');
                  
            $table->unsignedBigInteger('sub_id');
            $table->foreign('sub_id')
                  ->references('id')->on('subjects')
                  ->onDelete('cascade');

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
        Schema::dropIfExists('law_sub');
    }
}
