<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFactoresDesarrollosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factores_desarrollos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('externo_largo');
            $table->integer('externo_ancho');
            $table->integer('externo_alto');
            $table->integer('d1');
            $table->integer('d2');
            $table->integer('dh');
            $table->tinyInteger('caja_entera');
            $table->string('tipo_onda');
            $table->unsignedInteger('onda_id')->nullable();
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
        Schema::dropIfExists('factores_desarrollos');
    }
}
