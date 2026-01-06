<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RelacionFiltroIngresosPrincipales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relacion_filtro_ingresos_principales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('filtro_1');
            $table->unsignedInteger('filtro_2');
            $table->string('planta_id');
            $table->string('referencia');
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
        Schema::dropIfExists('relacion_filtro_ingresos_principales');
    }
}
