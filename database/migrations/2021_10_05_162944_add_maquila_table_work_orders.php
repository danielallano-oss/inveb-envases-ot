<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaquilaTableWorkOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_orders', function ($table) {
            $table->unsignedInteger('maquila')->nullable();
            $table->unsignedInteger('maquila_servicio_id')->nullable();
         });

         DB::table('maquila_servicios')->insert([
            ['servicio' => 'Desgaje Cabezal Par' , 'precio_clp_caja' => '7' , 'product_type_id' => '0' ,'created_at' => NOW(), 'updated_at' => NOW()],
            ['servicio' => 'Desgaje Unitario' ,'precio_clp_caja' => '3' , 'product_type_id' => '0' ,'created_at' => NOW(), 'updated_at' => NOW()],
            ['servicio' => 'PM CJ Chica entre 0 y 30 Cm' , 'precio_clp_caja' => '33' , 'product_type_id' => '0' ,'created_at' => NOW(), 'updated_at' => NOW()],
            ['servicio' => 'PM CJ Grande entre 70 y 100 Cm' , 'precio_clp_caja' => '84' , 'product_type_id' => '0' ,'created_at' => NOW(), 'updated_at' => NOW()],
            ['servicio' => 'PM CJ Mediana entre 30 y 70 Cm' , 'precio_clp_caja' => '41' , 'product_type_id' => '0' ,'created_at' => NOW(), 'updated_at' => NOW()],
            ['servicio' => 'Paletizado Placas' , 'precio_clp_caja' => '15' , 'product_type_id' => '0' ,'created_at' => NOW(), 'updated_at' => NOW()],
            ['servicio' => 'Armado y Paletizado Tabiques Doble' , 'precio_clp_caja' => '55' , 'product_type_id' => '0' ,'created_at' => NOW(), 'updated_at' => NOW()],
            ['servicio' => 'Armado y Paletizado Tabiques Simple' , 'precio_clp_caja' => '42' , 'product_type_id' => '0' ,'created_at' => NOW(), 'updated_at' => NOW()],
        ]);
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
                    'maquila', 
                    'maquila_servicio_id', 
                ]
            );
        });
    }
}
