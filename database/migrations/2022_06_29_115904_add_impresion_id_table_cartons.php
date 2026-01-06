<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImpresionIdTableCartons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //este campo "impresion_id" es el mismo "impresion" que esta en la tabla de work_orders
        Schema::table('cartons', function ($table) {
            $table->string('impresion_id')->nullable()->after('planta_id');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cartons', function (Blueprint $table) {
            $table->dropColumn('impresion_id');
        });
    }
}
