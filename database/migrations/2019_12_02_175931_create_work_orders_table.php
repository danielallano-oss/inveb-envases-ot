<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            // DATOS COMERCIALES
            $table->integer('tipo_solicitud');
            $table->string('nombre_contacto')->nullable();
            $table->string('email_contacto', 191)->nullable();
            $table->string('telefono_contacto', 12)->nullable()->comment('formato: 12 digitos sin espacios');
            $table->bigInteger('volumen_venta_anual')->nullable();
            $table->bigInteger('usd')->nullable();
            $table->tinyInteger('oc')->nullable();
            $table->string('descripcion');
            // Relaciones
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('org_venta_id')->nullable();
            $table->unsignedInteger('canal_id');
            // $table->unsignedInteger('hierarchy_id');
            // $table->unsignedInteger('subhierarchy_id');
            $table->unsignedInteger('subsubhierarchy_id')->nullable();
            // FIN DATOS COMERCIALES

            // solicitante
            $table->tinyInteger('analisis')->default(0)->nullable();
            $table->tinyInteger('plano')->default(0)->nullable();
            $table->tinyInteger('muestra')->default(0)->nullable();
            $table->tinyInteger('datos_cotizar')->default(0)->nullable();
            $table->tinyInteger('boceto')->default(0)->nullable();
            $table->tinyInteger('nuevo_material')->default(0)->nullable();
            $table->tinyInteger('prueba_industrial')->default(0)->nullable();
            $table->integer('numero_muestras')->nullable();
            // Referencia
            $table->tinyInteger('reference_type')->nullable();
            $table->unsignedInteger('reference_id')->nullable();
            $table->tinyInteger('bloqueo_referencia')->nullable();
            $table->tinyInteger('indicador_facturacion')->nullable();

            // CARACTERISTICAS
            $table->unsignedInteger('cad_id')->nullable();
            $table->string('cad')->nullable();
            $table->unsignedInteger('product_type_id')->nullable();
            $table->integer('items_set')->nullable();
            $table->integer('veces_item')->nullable();
            $table->unsignedInteger('carton_id')->nullable();
            $table->unsignedInteger('carton_color')->nullable();
            $table->unsignedInteger('style_id')->nullable();
            $table->integer('largura_hm')->nullable();
            $table->integer('anchura_hm')->nullable();
            $table->integer('area_producto')->nullable();
            $table->tinyInteger('recubrimiento')->default(0)->nullable();
            $table->integer('rmt')->nullable();
            $table->integer('golpes_largo')->nullable();
            $table->integer('golpes_ancho')->nullable();
            $table->integer('rayado_c1r1')->nullable();
            $table->integer('rayado_r1_r2')->nullable();
            $table->integer('rayado_r2_c2')->nullable();


            $table->decimal('gramaje', 12, 1)->nullable();
            $table->decimal('ect', 12, 2)->nullable();
            $table->unsignedInteger('flexion_aleta')->nullable();
            $table->unsignedInteger('peso')->nullable();
            $table->decimal('fct', 12, 2)->nullable();
            $table->decimal('cobb_interior', 12, 1)->nullable();
            $table->decimal('cobb_exterior', 12, 1)->nullable();
            $table->unsignedInteger('espesor')->nullable();

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


            // Datos de diseño
            $table->integer('numero_colores')->nullable();
            $table->unsignedInteger('color_1_id')->nullable();
            $table->integer('impresion_1')->nullable();
            $table->unsignedInteger('color_2_id')->nullable();
            $table->integer('impresion_2')->nullable();
            $table->unsignedInteger('color_3_id')->nullable();
            $table->integer('impresion_3')->nullable();
            $table->unsignedInteger('color_4_id')->nullable();
            $table->integer('impresion_4')->nullable();
            $table->unsignedInteger('color_5_id')->nullable();
            $table->integer('impresion_5')->nullable();
            // $table->unsignedInteger('color_6_id')->nullable();
            // $table->integer('impresion_6')->nullable();
            $table->tinyInteger('pegado')->nullable();
            $table->integer('longitud_pegado')->nullable();
            $table->tinyInteger('cera_exterior')->nullable();
            $table->integer('porcentaje_cera_exterior')->nullable();
            $table->tinyInteger('cera_interior')->nullable();
            $table->integer('porcentaje_cera_interior')->nullable();
            $table->tinyInteger('barniz_interior')->nullable();
            $table->integer('porcentaje_barniz_interior')->nullable();

            // Medidas interiores
            $table->integer('interno_largo')->nullable();
            $table->integer('interno_ancho')->nullable();
            $table->integer('interno_alto')->nullable();

            // Medidas exteriores
            $table->integer('externo_largo')->nullable();
            $table->integer('externo_ancho')->nullable();
            $table->integer('externo_alto')->nullable();

            // Terminaciones
            $table->unsignedInteger('process_id')->nullable();
            $table->tinyInteger('pegado_terminacion')->nullable();
            $table->unsignedInteger('armado_id')->nullable();
            $table->unsignedInteger('sentido_armado')->nullable();

            $table->enum('tipo_sentido_onda', ['Horizontal', 'Vertical'])->nullable();

            // Material Asignado
            $table->string('material_id')->nullable();
            $table->string('material_asignado')->nullable();
            $table->string('descripcion_material')->nullable();
            $table->string('material_code')->nullable();
            $table->tinyInteger('codigo_sap_final')->default(0);

            // DATOS DESARROLLO
            $table->integer('peso_contenido_caja')->nullable();
            $table->tinyInteger('autosoportante')->nullable();
            $table->unsignedInteger('envase_id')->nullable();
            $table->integer('cajas_altura')->nullable();
            $table->tinyInteger('pallet_sobre_pallet')->nullable();
            $table->tinyInteger('impresion')->nullable();
            $table->integer('cantidad')->nullable();
            $table->string('observacion')->nullable();

            // Datos para carton excel
            $table->integer('bct_min')->nullable();
            $table->integer('separacion_largura_hm')->nullable();
            $table->integer('separacion_anchura_hm')->nullable();

            $table->unsignedInteger('pallet_type_id')->nullable();
            $table->tinyInteger('pallet_treatment')->nullable();
            $table->integer('cajas_por_pallet')->nullable();
            $table->integer('placas_por_pallet')->nullable();
            $table->unsignedInteger('pallet_patron_id')->nullable();
            $table->integer('patron_zuncho')->nullable();
            $table->unsignedInteger('pallet_protection_id')->nullable();
            $table->unsignedInteger('pallet_box_quantity_id')->nullable();
            $table->integer('patron_zuncho_paquete')->nullable();
            $table->integer('patron_zuncho_bulto')->nullable();
            $table->integer('paquetes_por_unitizado')->nullable();


            $table->integer('unitizado_por_pallet')->nullable();
            $table->unsignedInteger('pallet_tag_format_id')->nullable();
            $table->integer('numero_etiquetas')->nullable();
            $table->unsignedInteger('pallet_qa_id')->nullable();
            $table->integer('unidad_medida_bct')->nullable(); // 0 = Kilo , 1 = Libra
            $table->string('tipo_camion', 200)->nullable();
            $table->string('restriccion_especial', 200)->nullable();
            $table->string('horario_recepcion', 200)->nullable();

            $table->string('codigo_producto_cliente', 200)->nullable();
            $table->string('uso_programa_z', 200)->nullable();
            $table->tinyInteger('etiquetas_dsc')->nullable();
            $table->integer('orientacion_placa')->nullable();
            $table->unsignedInteger('precut_type_id')->nullable();




            $table->tinyInteger('aprobacion_jefe_venta')->default(0);
            $table->tinyInteger('aprobacion_jefe_desarrollo')->default(0);
            $table->unsignedInteger('creador_id');
            $table->unsignedInteger('current_area_id');
            $table->dateTime('ultimo_cambio_area');
            $table->tinyInteger('terminado')->default(0);
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
        Schema::dropIfExists('work_orders');
    }
}
