<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditTypeFiltro2TableRelacionFiltroIngresosPrincipales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE relacion_filtro_ingresos_principales MODIFY filtro_2 VARCHAR(191)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE relacion_filtro_ingresos_principales MODIFY filtro_2 INT');
    }
}
