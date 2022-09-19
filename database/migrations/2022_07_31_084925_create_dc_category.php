<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc_category', function (Blueprint $table) {
            $table->id();
            $table->string('dc_cat_name')->unique();
            $table->unsignedBigInteger('dc_cat_id')->nullable(); 
            $table->foreign('dc_cat_id')
                  ->references('id')->on('dc_category')
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
        Schema::dropIfExists('dc_category');
    }
}
