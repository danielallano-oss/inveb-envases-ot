<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdjustFieldsBarnizTablePlantas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plantas', function($table) {
            $table->dropColumn('costo_barniz_merma_usd_mm2');
            $table->dropColumn('costo_cera_merma_usd_ton');
            $table->dropColumn('costo_cera_merma_usd_caja');
            $table->decimal('costo_barniz_usd_gr', 7, 6)->nullable();
            $table->unsignedInteger('consumo_barniz_gr_x_Mm2')->nullable();
         });

        
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       
        Schema::table('plantas', function($table) {
            $table->unsignedInteger('costo_barniz_merma_usd_mm2')->nullable();
            $table->unsignedInteger('costo_cera_merma_usd_ton')->nullable();
            $table->decimal('costo_cera_merma_usd_caja', 7, 5)->nullable();
            $table->dropColumn('costo_barniz_usd_gr');
            $table->dropColumn('consumo_barniz_gr_x_Mm2');
        });

    }
}
