<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVariablesCotizadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variables_cotizadors', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Calculos de costos esquineros
            $table->unsignedInteger('esq_perdida_papel');
            $table->unsignedInteger('esq_perdida_adhesivo');
            $table->unsignedInteger('esq_recorte_esquineros');
            $table->decimal('esq_consumo_adhesivo', 5, 1);
            $table->decimal('esq_costo_impresion_offset', 5, 3);
            $table->unsignedInteger('esq_esquineros_por_pallet');
            $table->unsignedInteger('esq_merma_costo_impresion');
            // 
            $table->unsignedInteger('iva');
            $table->unsignedInteger('tasa_mensual_credito');
            $table->unsignedInteger('dias_financiamiento_credito');
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
        Schema::dropIfExists('variables_cotizadors');
    }
}
