<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc_lists', function (Blueprint $table) {
            $table->id();
            $table->string('dc_list_name')->unique();

            $table->unsignedBigInteger('user_id');
            /* $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade'); */

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
        Schema::dropIfExists('dc_lists');
    }
}
