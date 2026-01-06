<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTarifarioMargensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarifario_margens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('rubro_id');
            $table->unsignedInteger('indice_complejidad');
            $table->string('tipo_cliente');
            $table->unsignedBigInteger('volumen_negociacion_minimo_2');
            $table->unsignedBigInteger('volumen_negociacion_maximo_2');
            $table->unsignedInteger("margen_minimo_usd_mm2");
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
        Schema::dropIfExists('tarifario_margens');
    }
}
