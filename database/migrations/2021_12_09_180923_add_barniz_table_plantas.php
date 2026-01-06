<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBarnizTablePlantas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plantas', function ($table) {
            $table->unsignedInteger('costo_barniz_merma_usd_mm2')->nullable();
            $table->unsignedInteger('costo_cera_merma_usd_ton')->nullable();
            $table->decimal('costo_cera_merma_usd_caja', 7, 5)->nullable();
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plantas', function (Blueprint $table) {
            $table->dropColumn(
                [
                    'costo_barniz_merma_usd_mm2', 
                    'costo_cera_merma_usd_ton',
                    'costo_cera_merma_usd_caja',
                ]
            );
        });
    }
}
