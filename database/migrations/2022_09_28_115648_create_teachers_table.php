<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->enum('thr_gender', [0, 1])->nullable(false);
            $table->string('thr_tc_no', 11)->unique();
            $table->string('thr_name')->nullable(false);
            $table->string('thr_surname')->nullable(false);
            $table->string('thr_province')->nullable(true);
            $table->string('thr_town')->nullable(true);
            $table->string('thr_email')->nullable(true);
            // $table->string('thr_career_ladder')->nullable(false);
            $table->enum('thr_career_ladder', [-1, 0, 1, 2])->nullable(false);
            $table->string('thr_degree')->nullable(true);
            $table->string('thr_task')->nullable(true);
            $table->string('thr_education_st')->nullable(true);
            $table->string('thr_mobile_no')->nullable(true);
            $table->string('thr_place_of_task')->nullable(true);
            $table->string('thr_photo')->nullable(true);
            $table->integer('thr_birth_day')->nullable(true);

            $table->unsignedBigInteger('inst_id'); 
            $table->foreign('inst_id')
                  ->references('id')->on('institutions');
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
        Schema::dropIfExists('teachers');
    }
}
