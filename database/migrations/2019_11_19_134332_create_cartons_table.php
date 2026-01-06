<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cartons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo', 200);
            $table->string('onda', 200)->nullable();
            $table->string('color_tapa_exterior', 200);
            $table->string('tipo', 200)->nullable();
            $table->decimal('ect_min', 5, 2)->nullable();
            $table->decimal('espesor', 5, 2)->nullable();
            $table->unsignedInteger('peso')->nullable();
            $table->unsignedInteger('peso_total')->nullable();
            // $table->unsignedInteger('volumen')->nullable();
            // Mas o menos % 
            $table->unsignedInteger('tolerancia_gramaje_real')->nullable();
            $table->unsignedInteger('contenido_cordillera')->nullable();
            $table->unsignedInteger('contenido_reciclado')->nullable();

            // < menor a x valor
            $table->unsignedInteger('porocidad_gurley')->nullable();
            // > mayor a x valor
            $table->unsignedInteger('cobb_int')->nullable();
            $table->unsignedInteger('cobb_ext')->nullable();
            $table->string('recubrimiento', 200)->nullable();
            $table->unsignedInteger('codigo_tapa_interior');
            $table->unsignedInteger('codigo_onda_1');
            $table->unsignedInteger('codigo_onda_1_2');
            $table->unsignedInteger('codigo_tapa_media');
            $table->unsignedInteger('codigo_onda_2');
            $table->unsignedInteger('codigo_tapa_exterior');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        // Antiguo primer esquema envases OT
        // Schema::create('cartons', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('codigo', 200);
        //     $table->string('descripcion', 200);
        //     $table->string('onda', 200);
        //     $table->integer('peso');
        //     $table->integer('volumen');
        //     $table->double('espesor');
        //     $table->string('color', 200);
        //     $table->string('tipo', 200);
        //     $table->tinyInteger('active')->default(1);
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cartons');
    }
}
