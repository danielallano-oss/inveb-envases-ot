<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDetalleValorDolarTableDetalleCotizacions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detalle_cotizacions', function ($table) {
            $table->string('detalle_valor_dolar')->nullable()->after('work_order_id');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detalle_cotizacions', function (Blueprint $table) {
            $table->dropColumn('detalle_valor_dolar');
        });
    }
}
