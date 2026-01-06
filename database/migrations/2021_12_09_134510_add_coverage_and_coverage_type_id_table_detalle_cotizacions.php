<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCoverageAndCoverageTypeIdTableDetalleCotizacions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detalle_cotizacions', function ($table) {
            $table->unsignedInteger('coverage')->nullable();
            $table->unsignedInteger('coverage_type_id')->nullable();
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
            $table->dropColumn(
                [
                    'coverage', 
                    'coverage_type_id',
                ]
            );
        });
    }
}
