<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreahcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areahcs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('interno_largo')->nullable();
            $table->integer('interno_ancho')->nullable();
            $table->integer('interno_alto')->nullable();
            $table->integer('traslape')->nullable();
            $table->tinyInteger('biselado')->default(0);
            $table->integer('biselado_horizontal')->nullable();
            $table->integer('biselado_vertical')->nullable();
            $table->unsignedInteger('product_type_id')->nullable();
            $table->unsignedInteger('style_id')->nullable();
            $table->unsignedInteger('onda_id')->nullable();
            $table->unsignedInteger('process_id')->nullable();
            $table->unsignedInteger('envase_id')->nullable();
            $table->integer('contenido_caja')->nullable();
            $table->integer('pallets_apilados')->nullable();
            $table->integer('cajas_apiladas_por_pallet')->nullable();
            $table->integer('filas_columnares_por_pallet')->nullable();
            $table->integer('numero_colores')->nullable();
            $table->unsignedInteger('carton_color')->nullable();
            $table->integer('rmt')->nullable();
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
        Schema::dropIfExists('areahcs');
    }
}
