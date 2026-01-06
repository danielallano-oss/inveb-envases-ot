<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTarifarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarifario', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('mercado');
            $table->string('tipo_cliente');
            $table->tinyInteger('carton_frecuente');
            $table->string('planta');
            $table->string('estacionalidad');
            $table->string("porcentaje_margen");
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
        Schema::dropIfExists('tarifario');
    }
}
