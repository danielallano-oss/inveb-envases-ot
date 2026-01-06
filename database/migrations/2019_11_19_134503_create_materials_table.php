<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo', 200);
            $table->string('descripcion', 200);
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('vendedor_id');
            $table->unsignedInteger('carton_id');
            $table->unsignedInteger('product_type_id');
            $table->unsignedInteger('style_id');
            $table->integer('numero_colores')->nullable();
            $table->unsignedInteger('cad_id');
            $table->unsignedInteger('pallet_type_id')->nullable();
            $table->integer('pallet_box_quantity')->nullable();
            $table->integer('placas_por_pallet')->default(0);
            $table->unsignedInteger('pallet_patron_id')->nullable();
            $table->integer('patron_zuncho_pallet');
            $table->integer('patron_zuncho_bulto')->nullable();
            $table->unsignedInteger('pallet_protection_id');
            $table->unsignedInteger('boxes_per_package');
            $table->integer('patron_zuncho_paquete');
            $table->integer('paquetes_por_unitizado');
            $table->integer('unitizado_por_pallet');
            $table->unsignedInteger('pallet_tag_format_id');
            $table->date('fecha_creacion');
            $table->unsignedInteger('creador_id');
            $table->unsignedInteger('pallet_qa_id')->nullable();
            $table->integer('numero_etiquetas');
            $table->string('rmt', 200)->nullable();
            $table->integer('unidad_medida_bct')->nullable();
            $table->tinyInteger('pallet_treatment')->nullable();
            $table->unsignedInteger('sap_hiearchy_id');
            $table->string('tipo_camion', 200)->nullable();
            $table->string('restriccion_especial', 200)->nullable();
            $table->string('horario_recepcion', 200)->nullable();
            $table->string('codigo_producto_cliente', 200)->nullable();
            $table->tinyInteger('etiquetas_dsc')->nullable();
            $table->integer('orientacion_placa');
            $table->tinyInteger('recubrimiento')->default(0)->nullable();

            // datos distancias cinta
            $table->tinyInteger('cinta')->nullable();
            $table->integer('corte_liner')->nullable();
            $table->integer('tipo_cinta')->nullable();
            $table->integer('distancia_cinta_1')->nullable();
            $table->integer('distancia_cinta_2')->nullable();
            $table->integer('distancia_cinta_3')->nullable();
            $table->integer('distancia_cinta_4')->nullable();
            $table->integer('distancia_cinta_5')->nullable();
            $table->integer('distancia_cinta_6')->nullable();

            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('materials');
    }
}
