<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetallePrecioPalletizadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_precio_palletizados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tipo_palletizado', 200);
            $table->unsignedInteger('tarima_nacional');
            $table->unsignedInteger('tarima_exportacion');
            $table->unsignedInteger('liston_nacional');
            $table->unsignedInteger('liston_exportacion');
            $table->unsignedInteger('tabla_tarima');
            $table->unsignedInteger('stretch_film');
            $table->unsignedInteger('sellos');
            $table->unsignedInteger('zunchos');
            $table->unsignedInteger('fundas');
            $table->unsignedInteger('cordel_y_clavos');
            $table->unsignedInteger('maquila');
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
        Schema::dropIfExists('detalle_precio_palletizados');
    }
}
