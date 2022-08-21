<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc_documents', function (Blueprint $table) {
            $table->id();
            $table->enum('dc_item_status', [0, 1]);
            $table->integer('dc_number');
            $table->string('dc_subject');
            $table->text('dc_raw_content');
            $table->text('dc_content');
            $table->string('dc_who_send');
            $table->string('dc_who_receiver');
            $table->integer('dc_date');
            
            $table->unsignedBigInteger('dc_cat_id'); 
            $table->foreign('dc_cat_id')
                  ->references('id')->on('dc_category');
                //   ->onDelete('cascade');
            
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
        Schema::dropIfExists('dc_documents');
    }
}
