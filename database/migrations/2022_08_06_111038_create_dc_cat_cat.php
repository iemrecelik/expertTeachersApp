<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcCatCat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* Schema::create('dc_cat_cat', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('dc_cat_id'); 
            $table->foreign('dc_cat_id')
                  ->references('id')->on('dc_category')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('dc_cat_child_id');
            $table->foreign('dc_cat_child_id')
                  ->references('id')->on('dc_category')
                  ->onDelete('cascade');

            $table->timestamps();
        }); */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dc_cat_cat');
    }
}
