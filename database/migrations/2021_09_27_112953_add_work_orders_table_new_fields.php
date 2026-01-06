<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorkOrdersTableNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_orders', function ($table) {
            $table->unsignedInteger('pallet_status_type_id')->nullable();
            $table->unsignedInteger('protection_type_id')->nullable();
            $table->unsignedInteger('rayado_type_id')->nullable();
            $table->unsignedInteger('additional_characteristics_type_id')->nullable();
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
                    'pallet_status_type_id', 
                    'protection_type_id', 
                    'rayado_type_id', 
                    'additional_characteristics_type_id', 
                ]
            );
        });
    }
}
