<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFiledsTableBitacoraWorkOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bitacora_work_orders', function ($table) {
            $table->text('user_data')->nullable()->after('user_id');
            $table->text('datos_modificados')->nullable()->after('work_order_id');
            $table->string('ip_solicitud')->nullable()->after('user_data');
            $table->string('url')->nullable()->after('ip_solicitud');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bitacora_work_orders', function (Blueprint $table) {
            $table->dropColumn(['ip_solicitud','url','user_data','datos_modificados']);
        });
    }
}
