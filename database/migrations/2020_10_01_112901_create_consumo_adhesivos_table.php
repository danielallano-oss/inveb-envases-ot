<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsumoAdhesivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumo_adhesivos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('planta_id');
            $table->string('onda');
            $table->unsignedInteger('adhesivo_corrugado');
            $table->unsignedInteger('adhesivo_powerply');
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
        Schema::dropIfExists('consumo_adhesivos');
    }
}
