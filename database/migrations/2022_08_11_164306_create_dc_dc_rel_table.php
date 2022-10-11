<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcDcRelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc_sp_rel', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('rel_sp_id');
            $table->foreign('rel_sp_id')
                  ->references('id')->on('dc_special_list')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('rel_dc_id');
            $table->foreign('rel_dc_id')
                  ->references('id')->on('dc_documents')
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
        Schema::dropIfExists('dc_dc_rel');
    }
}
