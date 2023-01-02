<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcThrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc_thr', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('dc_id');
            $table->foreign('dc_id')
                  ->references('id')->on('dc_documents')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('thr_id');
            $table->foreign('thr_id')
                  ->references('id')->on('teachers')
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
        Schema::dropIfExists('dc_thr');
    }
}
