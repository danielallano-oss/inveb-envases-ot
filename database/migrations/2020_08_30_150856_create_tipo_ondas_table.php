<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoOndasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_ondas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('onda', 200);
            $table->double('espesor_promedio');
            $table->double('espesor_maximo');
            $table->double('espesor_minimo');
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
        Schema::dropIfExists('tipo_ondas');
    }
}
