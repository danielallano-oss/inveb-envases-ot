<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsTableWorkOrders2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_orders', function ($table) {
            $table->unsignedInteger('color_6_id')->nullable()->after('impresion_5');
            $table->integer('impresion_6')->nullable()->after('color_6_id');
            $table->unsignedInteger('color_interno')->nullable()->after('impresion_6');
            $table->integer('impresion_color_interno')->nullable()->after('color_interno');
            $table->unsignedInteger('barniz_uv')->nullable()->after('impresion_color_interno');
            $table->integer('porcentanje_barniz_uv')->nullable()->after('barniz_uv');
            $table->unsignedInteger('coverage_internal_id')->nullable();
            $table->integer('percentage_coverage_internal')->nullable();
            $table->unsignedInteger('coverage_external_id')->nullable();
            $table->integer('percentage_coverage_external')->nullable();
            $table->string('indicador_facturacion_diseno_grafico')->nullable();
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
            $table->dropColumn(['
                color_6_id,
                impresion_6,
                color_interno,
                impresion_color_interno,
                barniz_uv,
                porcentanje_barniz_uv,
                coverage_internal_id,
                percentage_coverage_internal,
                coverage_external_id,
                percentage_coverage_external,
                indicador_facturacion_diseno_grafico
            ']);
        });
    }
}
