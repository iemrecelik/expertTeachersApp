<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOldSecondColApp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* Schema::create('old_second_col_app', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('osc_app_tc_num');
            $table->string('osc_app_name');
            $table->string('osc_app_surname');
            $table->string('osc_app_task area');
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
        Schema::dropIfExists('old_second_col_app');
    }
}
