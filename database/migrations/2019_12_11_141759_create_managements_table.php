<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('managements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('duracion_segundos')->nullable();
            $table->string('titulo');
            $table->string('observacion');
            $table->unsignedInteger('consulted_work_space_id')->nullable();
            $table->unsignedInteger('answer_id')->nullable();
            $table->unsignedInteger('management_type_id');
            $table->unsignedInteger('work_order_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('state_id')->nullable();
            $table->unsignedInteger('work_space_id');
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
        Schema::dropIfExists('managements');
    }
}
