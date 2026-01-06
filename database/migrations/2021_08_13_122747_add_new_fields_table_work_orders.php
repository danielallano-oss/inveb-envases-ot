<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsTableWorkOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_orders', function ($table) {
            $table->integer('bct_humedo_lb')->nullable();
            $table->decimal('mullen', 12, 2)->nullable();
            $table->unsignedInteger('pais_id')->nullable();
            $table->unsignedInteger('planta_id')->nullable();
            $table->tinyInteger('restriccion_pallet')->nullable();
            $table->unsignedInteger('tamano_pallet_type_id')->nullable();
            $table->integer('altura_pallet')->nullable();
            $table->tinyInteger('permite_sobresalir_carga')->nullable();
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(
                [
                    'bct_humedo_lb', 
                    'mullen', 
                    'pais_id', 
                    'planta_id', 
                    'restriccion_pallet', 
                    'tamano_pallet_type_id', 
                    'altura_pallet', 
                    'permite_sobresalir_carga', 
                ]
            );
        });
    }
}
