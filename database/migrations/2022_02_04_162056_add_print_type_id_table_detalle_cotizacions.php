<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrintTypeIdTableDetalleCotizacions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detalle_cotizacions', function ($table) {
            $table->unsignedInteger('print_type_id')->nullable()->after('product_type_id');
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
            $table->dropColumn('print_type_id');
        });
    }
}
