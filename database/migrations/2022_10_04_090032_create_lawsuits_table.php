<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLawsuitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lawsuits', function (Blueprint $table) {
            $table->id();
            $table->integer('law_order');
            $table->string('law_brief');

            $table->unsignedBigInteger('dc_id');
            $table->foreign('dc_id')
                  ->references('id')->on('dc_documents');

            $table->unsignedBigInteger('uns_id')->nullable();
            $table->foreign('uns_id')
                  ->references('id')->on('unions');

            $table->unsignedBigInteger('thr_id')->nullable();
            $table->foreign('thr_id')
                  ->references('id')->on('teachers');

            $table->unsignedBigInteger('law_id')->nullable();
            $table->foreign('law_id')
                  ->references('id')->on('lawsuits');

            $table->unsignedBigInteger('sub_id');
            $table->foreign('sub_id')
                  ->references('id')->on('subjects');

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
        Schema::dropIfExists('lawsuits');
    }
}