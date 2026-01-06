<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndicadorFacturacionDisenoGraficoTableDesignTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_types', function ($table) {
            $table->string('indicador_facturacion_diseno_grafico')->nullable()->after('complejidad');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_types', function (Blueprint $table) {
            $table->dropColumn('indicador_facturacion_diseno_grafico');
        });
    }
}
