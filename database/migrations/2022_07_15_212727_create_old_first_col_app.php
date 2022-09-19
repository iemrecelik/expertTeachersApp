<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOldFirstColApp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* Schema::create('old_first_col_app', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ofc_app_tc_num');
            $table->integer('ofc_app_nu');
            $table->integer('ofc_app_order_of_success');
            $table->boolean('ofc_app_success');
            $table->string('ofc_app_name');
            $table->string('ofc_app_surname');
            $table->string('ofc_app_ref_type');
            $table->string('ofc_app_institution');
            $table->string('ofc_app_com_area_eva');
            $table->string('ofc_app_area');
            $table->string('ofc_app_province');
            $table->float('ofc_app_seniority');
            $table->smallInteger('ofc_app_education');
            $table->smallInteger('ofc_app_activity');
            $table->float('ofc_app_record');
            $table->double('ofc_app_example_puan');
            $table->double('ofc_app_main_puan');
            $table->integer('ofc_app_quota');
            $table->double('ofc_app_min_puan');
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
        Schema::dropIfExists('old_first_col_app');
    }
}
