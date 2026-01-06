<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleCotizacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_cotizacions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('tipo_detalle_id');
            $table->unsignedInteger('cantidad');
            $table->unsignedInteger('product_type_id');
            $table->unsignedInteger('numero_colores')->nullable();


            // Campos para detalles Corrugados
            $table->decimal('area_hc', 5, 3)->nullable();
            $table->unsignedInteger('anchura')->nullable();
            $table->unsignedInteger('largura')->nullable();
            $table->unsignedInteger('carton_id')->nullable();
            $table->unsignedInteger('impresion')->nullable();
            $table->unsignedInteger('golpes_largo')->nullable();
            $table->unsignedInteger('golpes_ancho')->nullable();
            $table->unsignedInteger('process_id')->nullable();
            $table->tinyInteger('cinta_desgarro')->nullable();
            $table->unsignedInteger('pegado_terminacion')->nullable();
            $table->unsignedInteger('porcentaje_cera_interno')->nullable();
            $table->unsignedInteger('porcentaje_cera_externo')->nullable();
            $table->unsignedInteger('rubro_id')->nullable();
            // Campos opcionales
            $table->unsignedInteger('subsubhierarchy_id')->nullable();
            $table->unsignedInteger('largo')->nullable();
            $table->unsignedInteger('ancho')->nullable();
            $table->unsignedInteger('alto')->nullable();
            $table->integer('bct')->nullable();
            $table->integer('unidad_medida_bct')->nullable(); // 0 = Kilo , 1 = Libra
            $table->unsignedInteger('codigo_cliente')->nullable();

            // Campos al cargar de un material
            $table->string('codigo_material_detalle')->nullable();
            $table->string('descripcion_material_detalle')->nullable();
            $table->string('cad_material_detalle')->nullable();
            $table->unsignedInteger('cad_material_id')->nullable();
            $table->unsignedInteger('material_id')->nullable();

            // Servicios
            $table->tinyInteger('matriz')->nullable();
            $table->tinyInteger('clisse')->nullable();
            $table->tinyInteger('royalty')->nullable();
            $table->tinyInteger('maquila')->nullable();
            $table->tinyInteger('maquila_servicio_id')->nullable();
            $table->tinyInteger('armado_automatico')->default(0)->nullable();
            $table->decimal('armado_usd_caja', 5, 3)->default(0)->nullable();
            $table->tinyInteger('pallet')->nullable();
            $table->tinyInteger('zuncho')->nullable();
            $table->tinyInteger('funda')->nullable();
            $table->tinyInteger('stretch_film')->nullable();

            // Inputs de offset
            $table->unsignedInteger('ancho_pliego_cartulina')->nullable();
            $table->unsignedInteger('largo_pliego_cartulina')->nullable();
            $table->decimal('precio_pliego_cartulina', 5, 1)->default(0)->nullable();
            $table->decimal('precio_impresion_pliego', 5, 1)->default(0)->nullable();
            $table->unsignedInteger('gp_emplacado')->nullable();

            // // Campos para detalles Esquineros
            $table->unsignedInteger('largo_esquinero')->nullable();
            $table->unsignedInteger('carton_esquinero_id')->nullable();
            $table->unsignedInteger('funda_esquinero')->nullable();
            $table->unsignedInteger('tipo_destino_esquinero')->nullable();
            $table->unsignedInteger('tipo_camion_esquinero')->nullable();


            $table->unsignedInteger("ciudad_id")->nullable();
            $table->tinyInteger("pallets_apilados")->default(2)->nullable();
            $table->decimal("margen", 5, 1)->nullable();
            $table->unsignedInteger("margen_sugerido")->nullable();
            $table->unsignedInteger("indice_complejidad")->nullable();
            $table->unsignedInteger('planta_id')->default(1);
            $table->unsignedInteger('cotizacion_id')->nullable();
            $table->unsignedInteger('variable_cotizador_id')->nullable()->default(1);
            $table->unsignedInteger('work_order_id')->nullable();
            $table->text('historial_resultados')->nullable();
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
        Schema::dropIfExists('detalle_cotizacions');
    }
}
