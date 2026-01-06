<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditTypeFechaTableMuestras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE muestras MODIFY fecha_corte_vendedor datetime');
        DB::statement('ALTER TABLE muestras MODIFY fecha_corte_diseñador datetime');
        DB::statement('ALTER TABLE muestras MODIFY fecha_corte_laboratorio datetime');
        DB::statement('ALTER TABLE muestras MODIFY fecha_corte_1 datetime');
        DB::statement('ALTER TABLE muestras MODIFY fecha_corte_2 datetime');
        DB::statement('ALTER TABLE muestras MODIFY fecha_corte_3 datetime');
        DB::statement('ALTER TABLE muestras MODIFY fecha_corte_4 datetime');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE muestras MODIFY fecha_corte_vendedor date');
        DB::statement('ALTER TABLE muestras MODIFY fecha_corte_diseñador date');
        DB::statement('ALTER TABLE muestras MODIFY fecha_corte_laboratorio date');
        DB::statement('ALTER TABLE muestras MODIFY fecha_corte_1 date');
        DB::statement('ALTER TABLE muestras MODIFY fecha_corte_2 date');
        DB::statement('ALTER TABLE muestras MODIFY fecha_corte_3 date');
        DB::statement('ALTER TABLE muestras MODIFY fecha_corte_4 date');

    }
}
