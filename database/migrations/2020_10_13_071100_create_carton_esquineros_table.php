<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartonEsquinerosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carton_esquineros', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo', 200);
            $table->unsignedInteger('codigo_papel_1');
            $table->decimal('ancho_1', 5, 1);
            $table->unsignedInteger('capas_1');
            $table->unsignedInteger('codigo_papel_2');
            $table->decimal('ancho_2', 5, 1);
            $table->unsignedInteger('capas_2');
            $table->unsignedInteger('codigo_papel_3');
            $table->decimal('ancho_3', 5, 1);
            $table->unsignedInteger('capas_3');
            $table->unsignedInteger('codigo_papel_4');
            $table->decimal('ancho_4', 5, 1);
            $table->unsignedInteger('capas_4');
            $table->unsignedInteger('codigo_papel_5');
            $table->decimal('ancho_5', 5, 1);
            $table->unsignedInteger('capas_5');
            $table->unsignedInteger('resistencia');
            $table->decimal('espesor', 5, 1);
            $table->tinyInteger('alta_grafica')->default(1);
            $table->decimal('ancho_esquinero', 5, 1);
            $table->tinyInteger('active')->default(1);
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
        Schema::dropIfExists('carton_esquineros');
    }
}
