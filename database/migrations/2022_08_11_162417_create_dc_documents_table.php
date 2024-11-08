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
            $table->enum('dc_main_status', [0, 1])->default(1);
            $table->integer('dc_number');
            $table->string('dc_subject');
            $table->longText('dc_raw_content');
            $table->longText('dc_content');
            $table->longText('dc_show_content');
            $table->string('dc_who_send');
            $table->string('dc_who_receiver');
            $table->string('dc_base_number')->nullable()->default(null);
            $table->integer('dc_date');
            $table->enum('dc_manuel', [0, 1])->nullable()->default(0);
            
            /* $table->unsignedBigInteger('dc_cat_id'); 
            $table->foreign('dc_cat_id')
                  ->references('id')->on('dc_category'); */

            $table->unsignedBigInteger('user_id'); 
            $table->foreign('user_id')
                  ->references('id')->on('users');
                //   ->onDelete('cascade');

            $table->integer('created_by')->nullable();
            $table->string('created_by_name')->nullable();
            $table->integer('updated_by')->nullable();
            $table->string('updated_by_name')->nullable();
            
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
