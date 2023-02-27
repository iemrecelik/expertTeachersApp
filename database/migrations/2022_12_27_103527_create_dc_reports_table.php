<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('rp_date');
            $table->integer('rp_count');
            $table->enum('rp_item_status', [0, 1])->default(0);

            $table->unsignedBigInteger('user_id'); 
            $table->foreign('user_id')
                  ->references('id')->on('users');
                  
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
        Schema::dropIfExists('dc_reports');
    }
}
