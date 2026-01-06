<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardboardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cardboards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo', 200);
            $table->string('tipo', 200);
            $table->string('color_tapa_exterior', 200);
            $table->decimal('ect_min', 5, 2);
            $table->decimal('ect_promedio_real', 20, 15);
            $table->decimal('espesor', 5, 2);
            $table->unsignedInteger('codigo_tapa_interior');
            $table->string('onda_1', 200);
            $table->unsignedInteger('codigo_onda_1');
            $table->unsignedInteger('codigo_tapa_media');
            $table->string('onda_2', 200);
            $table->unsignedInteger('codigo_onda_2');
            $table->unsignedInteger('codigo_tapa_exterior');
            $table->string('recubrimiento', 200);

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
        Schema::dropIfExists('cardboards');
    }
}
