<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBarnizAcuosoAndUvConsumoTablePlantas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plantas', function($table) {
            $table->decimal('costo_barniz_acuoso_usd_gr', 7, 6)->nullable();
            $table->unsignedInteger('consumo_barniz_acuoso_gr_x_Mm2')->nullable();
            $table->decimal('costo_barniz_uv_usd_gr', 7, 6)->nullable();
            $table->unsignedInteger('consumo_barniz_uv_gr_x_Mm2')->nullable();
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
            $table->dropColumn('costo_barniz_acuoso_usd_gr');
            $table->dropColumn('consumo_barniz_acuoso_gr_x_Mm2');
            $table->dropColumn('costo_barniz_uv_usd_gr');
            $table->dropColumn('consumo_barniz_uv_gr_x_Mm2');
        });
    }
}
