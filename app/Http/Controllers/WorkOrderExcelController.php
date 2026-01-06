<?php

namespace App\Http\Controllers;

use App\AdditionalCharacteristicsType;
use App\BitacoraWorkOrder;
use App\Canal;
use App\Carton;
use App\Client;
use App\Color;
use App\Hierarchy;
use App\Material;
use App\Management;
use App\PalletBoxQuantity;
use App\PalletPatron;
use App\PalletProtection;
use App\PalletQa;
use App\PalletTagFormat;
use App\PalletType;
use App\PrecutType;
use App\PalletStatusType;
use App\ProtectionType;
use App\Rayado;
use App\ReferenceType;
use App\Style;
use App\Subhierarchy;
use App\Subsubhierarchy;
use App\UserWorkOrder;
use App\WorkOrder;
use App\WorkSpace;
use App\SecuenciaOperacional;
use App\Matriz;
use App\GrupoPlanta;
use App\OrganizacionVenta;
use App\Almacen;
use App\GrupoImputacionMaterial;
use App\Sector;
use App\GrupoMateriales1;
use App\GrupoMateriales2;
use App\RechazoConjunto;
use App\TiempoTratamiento;
use App\Adhesivo;
use App\CeBe;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Excel;
use App\Muestra;
use App\Process;

class WorkOrderExcelController extends Controller
{

    public function create($id)
    {

        $ot = WorkOrder::find($id);
        $cad = "";
        // dd($ot->cad_asignado);
        // dd($ot->recorte_caracteristico);
        if ($ot->cad) {
            $cad = $ot->cad;
        } elseif ($ot->cad_asignado) {
            $cad = $ot->cad_asignado->cad;
        }
        $palletTypes = PalletType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $palletPatron = PalletPatron::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $palletProtection = PalletProtection::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $cajasPorPaquete = PalletBoxQuantity::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $palletTagFormat = PalletTagFormat::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $palletQa = PalletQa::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $precutType = PrecutType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $formulario = isset($ot->product_type_id) && $ot->product_type_id == 21 ? "esquinero" : "carton";

        $reference_type = [0 => "No", 1 => "Si"]; //Se deja el arreglo para poder mostrar el No y el SI a las OT antiguas
        $reference_type = array_merge($reference_type, ReferenceType::where('active', 1)->pluck('descripcion', 'codigo')->toArray());

        $palletStatusTypes = PalletStatusType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $protectionType = ProtectionType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $rayadoType = Rayado::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $CharacteristicsType = AdditionalCharacteristicsType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $secuenciaOperacional = SecuenciaOperacional::where('active', 1)->pluck('descripcion', 'id')->toArray();
        if (is_null($ot->cad)) {
            $matriz = array();
        } else {
            $matriz = Matriz::where('active', 1)->where('plano_cad', $ot->cad)->pluck('material', 'id')->toArray();
        }

        $clisse = (is_null($ot->material_id)) ? 'N/A' : 'ENC' . $ot->material->codigo;

        // Número de Material
        $numero_material = '';
        if (!is_null($ot->material_asignado) && !is_null($ot->material_id)) {
            $numero_material = 'GE1' . $ot->material_code;
        } else {
            $numero_material = '';
        }

        // Número de Semielaborado
        $semielaborado = '';
        if (!is_null($ot->material_asignado) && !is_null($ot->material_id)) {
            if ($ot->product_type_id == 16 || $ot->product_type_id == 21) {
                $semielaborado = '';
            } else {
                $semielaborado = 'GE2' . $ot->material_code;
            }
        } else {
            $semielaborado = '';
        }

        //Numero de Pieza Interior
        $numero_material_pieza = '';
        if (!is_null($ot->material_asignado) && !is_null($ot->material_id)) {
            if ($ot->product_type_id == 16 || $ot->product_type_id == 21) {
                $numero_material_pieza = '';
            } else {
                if ($ot->proceso_id != 15) {
                    $numero_material_pieza = '';
                } else {
                    $numero_material_pieza = 'GE3' . $ot->material_code;
                }
            }
        } else {
            $numero_material_pieza = '';
        }

        //dd($ot->consumo3);
        return view('work-orders.form-excel.create', compact(
            'ot',
            'formulario',
            "cad",
            'palletTypes',
            "palletPatron",
            "palletProtection",
            "cajasPorPaquete",
            "palletTagFormat",
            "palletQa",
            "precutType",
            "reference_type",
            "palletStatusTypes",
            "protectionType",
            "rayadoType",
            "CharacteristicsType",
            "secuenciaOperacional",
            "matriz",
            "clisse",
            "numero_material",
            "semielaborado",
            "numero_material_pieza"
        ));
    }

    public function store(Request $request, $id)
    {

        if (auth()->user()->isIngeniero() || auth()->user()->isJefeDesarrollo()) {
            $request->validate([]);
        }
        if (auth()->user()->isCatalogador() || auth()->user()->isJefeCatalogador() || auth()->user()->isSuperAdministrador()) {
            $request->validate([]);
        }
        // dd($request->all());
        $ot = WorkOrder::find($id);
        $ot->bct_min                   = (trim($request->input('bct_min')) != '') ? $request->input('bct_min') : $ot->bct_min;
        $ot->separacion_largura_hm     = (trim($request->input('separacion_largura_hm')) != '') ? $request->input('separacion_largura_hm') : $ot->separacion_largura_hm;
        $ot->separacion_anchura_hm     = (trim($request->input('separacion_anchura_hm')) != '') ? $request->input('separacion_anchura_hm') : $ot->separacion_anchura_hm;
        $ot->pallet_type_id            = (trim($request->input('pallet_type_id')) != '') ? $request->input('pallet_type_id') : $ot->pallet_type_id;
        $ot->pallet_treatment          = (trim($request->input('pallet_treatment')) != '') ? $request->input('pallet_treatment') : $ot->pallet_treatment;
        $ot->cajas_por_pallet          = (trim($request->input('cajas_por_pallet')) != '') ? $request->input('cajas_por_pallet') : $ot->cajas_por_pallet;
        $ot->placas_por_pallet         = (trim($request->input('placas_por_pallet')) != '') ? $request->input('placas_por_pallet') : $ot->placas_por_pallet;
        $ot->pallet_patron_id          = (trim($request->input('pallet_patron_id')) != '') ? $request->input('pallet_patron_id') : $ot->pallet_patron_id;
        $ot->patron_zuncho             = (trim($request->input('patron_zuncho')) != '') ? $request->input('patron_zuncho') : $ot->patron_zuncho;
        $ot->pallet_protection_id      = (trim($request->input('pallet_protection_id')) != '') ? $request->input('pallet_protection_id') : $ot->pallet_protection_id;
        $ot->pallet_box_quantity_id    = (trim($request->input('pallet_box_quantity_id')) != '') ? $request->input('pallet_box_quantity_id') : $ot->pallet_box_quantity_id;
        $ot->patron_zuncho_paquete     = (trim($request->input('patron_zuncho_paquete')) != '') ? $request->input('patron_zuncho_paquete') : $ot->patron_zuncho_paquete;
        $ot->patron_zuncho_bulto       = (trim($request->input('patron_zuncho_bulto')) != '') ? $request->input('patron_zuncho_bulto') : $ot->patron_zuncho_bulto;
        $ot->paquetes_por_unitizado    = (trim($request->input('paquetes_por_unitizado')) != '') ? $request->input('paquetes_por_unitizado') : $ot->paquetes_por_unitizado;
        $ot->unitizado_por_pallet      = (trim($request->input('unitizado_por_pallet')) != '') ? $request->input('unitizado_por_pallet') : $ot->unitizado_por_pallet;
        //$ot->pallet_tag_format_id      = (trim($request->input('pallet_tag_format_id')) != '') ? $request->input('pallet_tag_format_id') : $ot->pallet_tag_format_id;
        $ot->formato_etiqueta          = (trim($request->input('formato_etiqueta')) != '') ? $request->input('formato_etiqueta') : $ot->formato_etiqueta;
        $ot->numero_etiquetas          = (trim($request->input('numero_etiquetas')) != '') ? $request->input('numero_etiquetas') : $ot->numero_etiquetas;
        $ot->pallet_qa_id              = (trim($request->input('pallet_qa_id')) != '') ? $request->input('pallet_qa_id') : $ot->pallet_qa_id;
        $ot->unidad_medida_bct         = (trim($request->input('unidad_medida_bct')) != '') ? $request->input('unidad_medida_bct') : $ot->unidad_medida_bct;
        $ot->tipo_camion               = (trim($request->input('tipo_camion')) != '') ? $request->input('tipo_camion') : $ot->tipo_camion;
        $ot->restriccion_especial      = (trim($request->input('restriccion_especial')) != '') ? $request->input('restriccion_especial') : $ot->restriccion_especial;
        $ot->horario_recepcion         = (trim($request->input('horario_recepcion')) != '') ? $request->input('horario_recepcion') : $ot->horario_recepcion;
        $ot->codigo_producto_cliente   = (trim($request->input('codigo_producto_cliente')) != '') ? $request->input('codigo_producto_cliente') : $ot->codigo_producto_cliente;
        //Solicitud de correccion en Evolutivo 24-09
        $ot->codigo_producto           = (trim($request->input('codigo_producto_cliente')) != '') ? $request->input('codigo_producto_cliente') : $ot->codigo_producto;
        //
        $ot->uso_programa_z            = (trim($request->input('uso_programa_z')) != '') ? $request->input('uso_programa_z') : $ot->uso_programa_z;
        $ot->etiquetas_dsc             = (trim($request->input('etiquetas_dsc')) != '') ? $request->input('etiquetas_dsc') : $ot->etiquetas_dsc;
        $ot->orientacion_placa         = (trim($request->input('orientacion_placa')) != '') ? $request->input('orientacion_placa') : $ot->orientacion_placa;
        $ot->precut_type_id            = (trim($request->input('precut_type_id')) != '') ? $request->input('precut_type_id') : $ot->precut_type_id;
        $ot->rayado_type_id            = (trim($request->input('rayado_type_id')) != '') ? $request->input('rayado_type_id') : $ot->rayado_type_id;
        $ot->protection_type_id        = (trim($request->input('protection_type_id')) != '') ? $request->input('protection_type_id') : $ot->protection_type_id;
        $ot->pallet_status_type_id     = (trim($request->input('pallet_status_type_id')) != '') ? $request->input('pallet_status_type_id') : $ot->pallet_status_type_id;
        $ot->additional_characteristics_type_id  = (trim($request->input('additional_characteristics_type_id')) != '') ? $request->input('additional_characteristics_type_id') : $ot->additional_characteristics_type_id;
        $ot->termocontraible          = (trim($request->input('termocontraible')) != '') ? $request->input('termocontraible') : $ot->termocontraible;
        $ot->matriz_id                  = (trim($request->input('matriz_id')) != '') ? $request->input('matriz_id') : $ot->matriz_id;
        $ot->matriz_id_2                  = (trim($request->input('matriz_id_2')) != '') ? $request->input('matriz_id_2') : $ot->matriz_id_2;
        $ot->matriz_id_3                  = (trim($request->input('matriz_id_3')) != '') ? $request->input('matriz_id_3') : $ot->matriz_id_3;

        ////Nuevo Evolutivo 24-09 - Inicio

        ///Planta Original - Inicio
        $ot->so_planta_original = (trim($request->input('sec_ope_planta_orig_id')) != '') ? $request->input('sec_ope_planta_orig_id') : null;

        //// Valores secuencias operacionales Planta Original
        $array_planta_select_values = array();
        $valores_fila = false;

        $sec_ope_planta_orig_filas = $request->input('sec_ope_planta_orig_filas');

        for ($i = 1; $i <= $sec_ope_planta_orig_filas; $i++) {
            $array_planta_fila_select_values = array();
            $valores_fila = false;
            if (!is_null($request->input('sec_ope_ppal_planta_ori_' . $i))) {
                $array_planta_fila_select_values['org'] = $request->input('sec_ope_ppal_planta_ori_' . $i);
                $valores_fila = true;
            }
            if (!is_null($request->input('sec_ope_atl_1_planta_ori_' . $i))) {
                $array_planta_fila_select_values['alt1'] = $request->input('sec_ope_atl_1_planta_ori_' . $i);
                $valores_fila = true;
            }
            if (!is_null($request->input('sec_ope_atl_2_planta_ori_' . $i))) {
                $array_planta_fila_select_values['alt2'] = $request->input('sec_ope_atl_2_planta_ori_' . $i);
                $valores_fila = true;
            }
            if (!is_null($request->input('sec_ope_atl_3_planta_ori_' . $i))) {
                $array_planta_fila_select_values['alt3'] = $request->input('sec_ope_atl_3_planta_ori_' . $i);
                $valores_fila = true;
            }
            if (!is_null($request->input('sec_ope_atl_4_planta_ori_' . $i))) {
                $array_planta_fila_select_values['alt4'] = $request->input('sec_ope_atl_4_planta_ori_' . $i);
                $valores_fila = true;
            }
            if (!is_null($request->input('sec_ope_atl_5_planta_ori_' . $i))) {
                $array_planta_fila_select_values['alt5'] = $request->input('sec_ope_atl_5_planta_ori_' . $i);
                $valores_fila = true;
            }
            if ($valores_fila) {
                $array_planta_select_values['fila_' . $i] = $array_planta_fila_select_values;
            }
        }

        $ot->so_planta_original_select_values = json_encode($array_planta_select_values, JSON_UNESCAPED_UNICODE);

        ///Planta Original - Fin

        ///Planta Alternativa 1 - Inicio
        $ot->so_planta_alt1 = (trim($request->input('sec_ope_planta_aux_1_id')) != '') ? $request->input('sec_ope_planta_aux_1_id') : null;

        if (is_null($request->input('check_planta_aux_1'))) {
            $ot->check_planta_alt1 = 0;
        } else {

            $ot->check_planta_alt1 = 1;

            $sec_ope_planta_aux_1_filas = $request->input('sec_ope_planta_aux_1_filas');

            // Valores secuencias operacionales Planta  Alternativa 1
            $array_planta_select_values = array();
            $valores_fila = false;

            for ($i = 1; $i <= $sec_ope_planta_aux_1_filas; $i++) {
                $array_planta_fila_select_values = array();
                $valores_fila = false;
                if (!is_null($request->input('sec_ope_ppal_planta_aux_1_' . $i))) {
                    $array_planta_fila_select_values['org'] = $request->input('sec_ope_ppal_planta_aux_1_' . $i);
                    $valores_fila = true;
                }
                if (!is_null($request->input('sec_ope_atl_1_planta_aux_1_' . $i))) {
                    $array_planta_fila_select_values['alt1'] = $request->input('sec_ope_atl_1_planta_aux_1_' . $i);
                    $valores_fila = true;
                }
                if (!is_null($request->input('sec_ope_atl_2_planta_aux_1_' . $i))) {
                    $array_planta_fila_select_values['alt2'] = $request->input('sec_ope_atl_2_planta_aux_1_' . $i);
                    $valores_fila = true;
                }
                if ($valores_fila) {
                    $array_planta_select_values['fila_' . $i] = $array_planta_fila_select_values;
                }
            }

            $ot->so_planta_alt1_select_values = json_encode($array_planta_select_values, JSON_UNESCAPED_UNICODE);
        }

        ///Planta Alternativa 1 - Fin

        ///Planta Alternativa 2 - Inicio
        $ot->so_planta_alt2 = (trim($request->input('sec_ope_planta_aux_2_id')) != '') ? $request->input('sec_ope_planta_aux_2_id') : null;

        if (is_null($request->input('check_planta_aux_2'))) {
            $ot->check_planta_alt2 = 0;
        } else {
            $ot->check_planta_alt2 = 1;

            $sec_ope_planta_aux_2_filas = $request->input('sec_ope_planta_aux_2_filas');

            // Valores secuencias operacionales Planta  Alternativa 2
            $array_planta_select_values = array();
            $valores_fila = false;

            for ($i = 1; $i <= $sec_ope_planta_aux_2_filas; $i++) {
                $array_planta_fila_select_values = array();
                $valores_fila = false;
                if (!is_null($request->input('sec_ope_ppal_planta_aux_2_' . $i))) {
                    $array_planta_fila_select_values['org'] = $request->input('sec_ope_ppal_planta_aux_2_' . $i);
                    $valores_fila = true;
                }
                if (!is_null($request->input('sec_ope_atl_1_planta_aux_2_' . $i))) {
                    $array_planta_fila_select_values['alt1'] = $request->input('sec_ope_atl_1_planta_aux_2_' . $i);
                    $valores_fila = true;
                }
                if (!is_null($request->input('sec_ope_atl_2_planta_aux_2_' . $i))) {
                    $array_planta_fila_select_values['alt2'] = $request->input('sec_ope_atl_2_planta_aux_2_' . $i);
                    $valores_fila = true;
                }
                if ($valores_fila) {
                    $array_planta_select_values['fila_' . $i] = $array_planta_fila_select_values;
                }
            }
            $ot->so_planta_alt2_select_values = json_encode($array_planta_select_values, JSON_UNESCAPED_UNICODE);
        }
        ///Planta Alternativa 2 - Fin
        ////Nuevo Evolutivo 24-09 - Fin

        $ot->save();


        // Si ya la ot tiene un material creado anteriormente se debe actualizar la informacion correspondiente
        // Editar Material
        if (isset($ot->material_id)) {
            $material = Material::find($ot->material_id);
            // dd($material);
            $material->pallet_type_id = $ot->pallet_type_id;
            $material->pallet_box_quantity = $ot->cajas_por_pallet;
            $material->placas_por_pallet = $ot->placas_por_pallet;
            $material->pallet_patron_id = $ot->pallet_patron_id;
            $material->patron_zuncho_pallet = $ot->patron_zuncho;
            $material->pallet_protection_id = $ot->pallet_protection_id;
            $material->boxes_per_package = $ot->pallet_box_quantity_id;
            $material->patron_zuncho_paquete = $ot->patron_zuncho_paquete;
            $material->patron_zuncho_bulto = $ot->patron_zuncho_bulto;
            $material->paquetes_por_unitizado = $ot->paquetes_por_unitizado;
            $material->unitizado_por_pallet = $ot->unitizado_por_pallet;
            $material->pallet_tag_format_id = $ot->formato_etiqueta;
            $material->pallet_qa_id = $ot->pallet_qa_id;
            $material->numero_etiquetas = $ot->numero_etiquetas;
            // $material->rmt = $ot->rmt;
            // $material->unidad_medida_bct = $ot->unidad_medida_bct;
            $material->pallet_treatment = $ot->pallet_treatment;
            $material->tipo_camion = $ot->tipo_camion;
            $material->restriccion_especial = $ot->restriccion_especial;
            $material->horario_recepcion = $ot->horario_recepcion;
            $material->codigo_producto_cliente = $ot->codigo_producto_cliente;
            $material->etiquetas_dsc = $ot->etiquetas_dsc;
            $material->orientacion_placa = $ot->orientacion_placa;
            $material->recubrimiento = $ot->recubrimiento;
            // dd($material);
            $material->save();
        }

        // if(Auth()->user()->isSuperAdministrador()){

        //     //Se crea una observación automatica cuando se modifica la OT
        //     $gestion = new Management();
        //     $gestion->observacion = "Modificación en los datos del Formulario Excel Cartón";
        //     $gestion->management_type_id = 5; //Tipo modificacion
        //     $gestion->user_id = Auth()->user()->id;
        //     $gestion->work_order_id = $ot->id;
        //     $gestion->work_space_id =  7; //Area de super administrador
        //     $gestion->duracion_segundos = 0;
        //     $gestion->state_id = 19; //Estado modificacion
        //     $gestion->save();

        //     //Se guarda registro en la tabla de bitacora
        //     $bitacora = new BitacoraWorkOrder();
        //     $bitacora->observacion = "Modificación en los datos del Formulario Excel Cartón";
        //     $bitacora->operacion = 'Modificación'; //Tipo modificacion
        //     $bitacora->work_order_id = $ot->id;
        //     $bitacora->user_id = Auth()->user()->id;
        //     $bitacora->save();

        // }

        return redirect()->route('gestionarOt', $id)->with('success', 'Excel Cartones actualizado correctamente.');
    }

    public function descargarReporteExcel($id)
    {
        $ot = WorkOrder::with(
            'armado',
            'canal',
            'client',
            'creador',
            'productType',
            "users",
            "material",
            "material_referencia",
            "subsubhierarchy",
            "tipo_pallet",
            "cajas_por_paquete",
            "patron_pallet",
            "proteccion_pallet",
            "formato_etiqueta_pallet",
            "qa",
            "prepicado",
            "carton",
            "style",
            "rayado",
            "pallet_status",
            "protection",
            "characteristics",
        )->where('id', $id)->first();

        $item_array = self::array_data_excel($ot);
        $titulo = 'Reporte de Catalogación';

        Excel::create($titulo . Carbon::now(), function ($excel) use ($item_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet($titulo, function ($sheet) use ($item_array) {
                // Se aplica foreach porque la informacion tiene ser mostrada de forma vertical
                // solo utilizando las celdas A y B
                $i = 1;
                foreach ($item_array as $key => $fila) {
                    $sheet->setCellValue('A' . $i, $key);
                    $sheet->setCellValue('B' . $i, $fila);

                    $i++;
                }
            });
        })->download('xlsx');
    }

    public function array_data_excel($ot)
    {
        //Validamos los campos para que siempre imprima 4 decimales, ya sea agregar con cero o recortar los datos de la BD
        $recorte_caracteristico = number_format_unlimited_precision($ot->recorteCaracteristico, ",", ".", 7);
        if (isset($recorte_caracteristico) && $recorte_caracteristico != 'N/A') {
            if ($recorte_caracteristico === '0') {
                $detalle_recorte_caracteristico = $recorte_caracteristico;
            } else {
                $recorte = number_format_unlimited_precision($ot->recorteCaracteristico, ",", ".", 7);
                $decimal = explode(',', $recorte);
                $truncate_decimal = substr($decimal[1], 0, 4);
                $pad = str_pad($truncate_decimal, 4, "0");
                $detalle_recorte_caracteristico = $decimal[0] . ',' . $pad;
            }
        } else {
            $detalle_recorte_caracteristico = 'N/A';
        }

        if ($ot->recorte_adicional > 0) {
            $recorte_adicional = number_format_unlimited_precision($ot->recorte_adicional);
            $decimal_adicional = explode(',', $recorte_adicional);
            $truncate_decimal_adicional = substr($decimal_adicional[1], 0, 4);
            $pad_adicional = str_pad($truncate_decimal_adicional, 4, "0");
            $detalle_recorte_adicional = $decimal_adicional[0] . ',' . $pad_adicional;
        } else {
            $detalle_recorte_adicional = '0,0000';
        }

        if ($ot->area_producto_calculo > 0) {
            $recorte_producto = number_format_unlimited_precision($ot->area_producto_calculo);
            $decimal_producto = explode(',', $recorte_producto);
            // $truncate_decimal_producto = substr($decimal_producto[1], 0, 4);
            // $pad_producto = str_pad($truncate_decimal_producto, 4, "0");
            $concaterna_producto = $decimal_producto[0] . '.' . $decimal_producto[1];
            $detalle_area_producto = $concaterna_producto * 1000000;
        } else {
            $detalle_area_producto = 0;
        }

        //Validacion si la OT fue creada por un vendedor externo
        // if($ot->ot_vendedor_externo==1){


        // $numero_material = '';
        // $numero_material_semi = '';
        // $numero_material_pieza = '';
        // if($ot->material){

        //     $numero_material = 'GE1'.$ot->material->codigo;
        //     $numero_material_semi = 'GE2';
        //     $numero_material_pieza = 'GE3';

        // }

        // Número de Material
        $numero_material = '';
        if (!is_null($ot->material_asignado) && !is_null($ot->material_id)) {
            $numero_material = 'GE1' . $ot->material_code;
        } else {
            $numero_material = '';
        }

        // Número de Semielaborado
        $semielaborado = '';
        if (!is_null($ot->material_asignado) && !is_null($ot->material_id)) {
            if ($ot->product_type_id == 16 || $ot->product_type_id == 21) {
                $semielaborado = '';
            } else {
                $semielaborado = 'GE2' . $ot->material_code;
            }
        } else {
            $semielaborado = '';
        }

        //Numero de Pieza Interior
        $numero_material_pieza = '';
        if (!is_null($ot->material_asignado) && !is_null($ot->material_id)) {
            if ($ot->product_type_id == 16 || $ot->product_type_id == 21) {
                $numero_material_pieza = '';
            } else {
                if ($ot->proceso_id != 15) {
                    $numero_material_pieza = '';
                } else {
                    $numero_material_pieza = 'GE3' . $ot->material_code;
                }
            }
        } else {
            $numero_material_pieza = '';
        }

        //Secuencia Operacional
        //Planta Buin
        $maquina_proceso_1 = null;
        $maquina_proceso_1_id = null;
        $maquina_proceso_2 = null;
        $maquina_proceso_3 = null;
        $maquina_proceso_4 = null;
        $maquina_proceso_5 = null;
        $maquina_proceso_6 = null;

        if ($ot->so_planta_original == 1) { //Planta Original
            if (!is_null($ot->so_planta_original_select_values)) {
                $secuencia_productiva_planta_original = json_decode($ot->so_planta_original_select_values, true);
                for ($i = 1; $i <= count($secuencia_productiva_planta_original); $i++) {
                    if (isset($secuencia_productiva_planta_original['fila_' . $i])) {
                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['org'])) {
                            $secuencia_original = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->codigo;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1 = $secuencia_original;
                                    $maquina_proceso_1_id = $secuencia_productiva_planta_original['fila_' . $i]['org'];
                                    break;
                                case 2:
                                    $maquina_proceso_2 = $secuencia_original;
                                    break;
                                case 3:
                                    $maquina_proceso_3 = $secuencia_original;
                                    break;
                                case 4:
                                    $maquina_proceso_4 = $secuencia_original;
                                    break;
                                case 5:
                                    $maquina_proceso_5 = $secuencia_original;
                                    break;
                                case 6:
                                    $maquina_proceso_6 = $secuencia_original;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }
                    }
                }
            }
        }

        //Planta Original - Osorno
        if ($ot->so_planta_original == 3) {
            if (!is_null($ot->so_planta_original_select_values)) {
                $secuencia_productiva_planta_original = json_decode($ot->so_planta_original_select_values, true);
                for ($i = 1; $i <= count($secuencia_productiva_planta_original); $i++) {
                    if (isset($secuencia_productiva_planta_original['fila_' . $i])) {
                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['org'])) {
                            $secuencia_original = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->codigo;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1 = $secuencia_original;
                                    $maquina_proceso_1_id = $secuencia_productiva_planta_original['fila_' . $i]['org'];
                                    break;
                                case 2:
                                    $maquina_proceso_2 = $secuencia_original;
                                    break;
                                case 3:
                                    $maquina_proceso_3 = $secuencia_original;
                                    break;
                                case 4:
                                    $maquina_proceso_4 = $secuencia_original;
                                    break;
                                case 5:
                                    $maquina_proceso_5 = $secuencia_original;
                                    break;
                                case 6:
                                    $maquina_proceso_6 = $secuencia_original;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }
                    }
                }
            }
        }

        //Planta TilTil
        if ($ot->so_planta_original == 2) { //Planta Original
            if (!is_null($ot->so_planta_original_select_values)) {
                $secuencia_productiva_planta_original = json_decode($ot->so_planta_original_select_values, true);
                for ($i = 1; $i <= count($secuencia_productiva_planta_original); $i++) {
                    if (isset($secuencia_productiva_planta_original['fila_' . $i])) {
                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['org'])) {
                            $secuencia_original = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->codigo;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1 = $secuencia_original;
                                    $maquina_proceso_1_id = $secuencia_productiva_planta_original['fila_' . $i]['org'];
                                    break;
                                case 2:
                                    $maquina_proceso_2 = $secuencia_original;
                                    break;
                                case 3:
                                    $maquina_proceso_3 = $secuencia_original;
                                    break;
                                case 4:
                                    $maquina_proceso_4 = $secuencia_original;
                                    break;
                                case 5:
                                    $maquina_proceso_5 = $secuencia_original;
                                    break;
                                case 6:
                                    $maquina_proceso_6 = $secuencia_original;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }
                    }
                }
            }
        }

        //Consumo_cinta
        if (is_null($ot->cintas_x_caja) || $ot->cintas_x_caja == 0 || $ot->cintas_x_caja == '') {
            $consumo_cinta = '';
        } else {
            if ($ot->largura_hm == 0 || $ot->largura_hm == '') {
                $consumo_cinta = '';
            } else {
                $consumo_cinta = $ot->largura_hm * $ot->cintas_x_caja;
            }
        }

        $array_data = array(
            "OT" => $ot->id,
            "Número de Material" => $ot->material ? $ot->material->codigo : null,
            //"Número de Material" => $numero_material,
            //"Número de Semielaborado" => $semielaborado,
            //"Número de Pieza Interior" => $numero_material_pieza,
            "Descripción Comercial" => $ot->material ? $ot->material->descripcion : null,
            // Cliente
            "Cliente" => $ot->client->codigo,
            //"Datos Cliente Edipac" => $ot->dato_sub_cliente,
            "Vendedor" => isset($ot->creador->nombre_sap) ? $ot->creador->nombre_sap : $ot->creador->fullname,
            // "Vendedor" => $ot->creador->fullname,
            "Largo Interior (MM)" => $ot->interno_largo,
            "Ancho Interior (MM)" => $ot->interno_ancho,
            "Alto Interior (MM)" => $ot->interno_alto,
            "Largura HM (MM)" => $ot->largura_hm,
            "Anchura HM (MM)" => $ot->anchura_hm,
            "Largo Exterior (MM)" => $ot->externo_largo,
            "Ancho Exterior (MM)" => $ot->externo_ancho,
            "Alto Exterior (MM)" => $ot->externo_alto,

            // Carton
            "Cartón" => $ot->carton ? $ot->carton->codigo : null,
            "Tipo de Producto (Tipo Item)" => isset($ot->productType) ? $ot->productType->descripcion : null,
            "Estilo de Producto" => isset($ot->style) ? $ot->style->glosa : null,
            "Caracteristicas Estilo" =>   $ot->caracteristicas_adicionales ? $ot->caracteristicas_adicionales : null,
            "Rayado C1/R1 (MM)" => isset($ot->rayado_c1r1) ? $ot->rayado_c1r1 : null,
            "Rayado R1/R2 (MM)" => isset($ot->rayado_r1_r2) ? $ot->rayado_r1_r2 : null,
            "Rayado R2/C2 (MM)" => isset($ot->rayado_r2_c2) ? $ot->rayado_r2_c2 : null,
            "Tipo de Rayado" => isset($ot->rayado_type_id) ? $ot->rayado->descripcion : null,
            "Tipo Impresión" => $ot->impresion ? $ot->impresion_detalle->descripcion : null,
            "Número de Colores" => $ot->numero_colores,
            "Prueba de Color" => isset($ot->prueba_color) ? [1 => "Si", 0 => "No"][$ot->prueba_color] : null,
            "Recorte Característico (M2)" => $detalle_recorte_caracteristico,
            "Recorte Adicional (M2)" => $detalle_recorte_adicional,
            "Plano CAD" => isset($ot->cad) ? $ot->cad : null,
            "Area Producto (M2)" => $detalle_area_producto,
            "Estado de Palletizado" => isset($ot->pallet_status_type_id) ? $ot->pallet_status->descripcion : null,
            "Tipo de Pallet" => $ot->tipo_pallet ? $ot->tipo_pallet->descripcion : null,
            "Tratamiento de Pallet" => isset($ot->pallet_treatment) ? [1 => "Si", 0 => "No"][$ot->pallet_treatment] : null,
            "Nro Cajas por Pallet" => $ot->cajas_por_pallet ? $ot->cajas_por_pallet : null,
            "Nro Placas por Pallet" => $ot->placas_por_pallet ? $ot->placas_por_pallet : null,
            "Patron Carga Pallet" => $ot->patron_pallet ? $ot->patron_pallet->descripcion : null,
            "Patron Zuncho Bulto" => $ot->patron_zuncho_bulto ? [1 => "2x0", 2 => "2x1", 3 => "2x2"][$ot->patron_zuncho_bulto] : null,
            "Proteccion" => isset($ot->protection_type_id) ? $ot->protection->descripcion : null,
            "Patron Zuncho Pallet" => $ot->patron_zuncho_bulto ? [1 => "2x0", 2 => "2x1", 3 => "2x2"][$ot->patron_zuncho_bulto] : null,
            "Protección Pallet" => $ot->proteccion_pallet ? $ot->proteccion_pallet->descripcion : null,
            "Nro Cajas por Paquete" => $ot->cajas_por_paquete ? $ot->cajas_por_paquete->descripcion : null,
            "Patron Zuncho Paquete" => $ot->patron_zuncho_paquete ? $ot->patron_zuncho_paquete : null,
            "Termocontraible" => isset($ot->termocontraible) ? [1 => "Si", 0 => "No"][$ot->termocontraible] : null,
            "Nro Cajas por Unitizados" => $ot->paquetes_por_unitizado ? $ot->paquetes_por_unitizado : null,
            "Nro Unitizados por Pallet" => $ot->unitizado_por_pallet ? $ot->unitizado_por_pallet : null,
            "Tipo Formato Etiqueta Pallet" => $ot->formato_etiqueta_pallet ? $ot->formato_etiqueta_pallet->descripcion : null,
            "Nro Etiqueta Pallet" => $ot->numero_etiquetas ? [0, 1, 2, 3, 4][$ot->numero_etiquetas] : null,
            "Certificado Calidad" => $ot->qa ? $ot->qa->descripcion : null,
            "BCT MIN (LB)" => $ot->bct_min_lb,
            "Unidad Medida BCT" => 'Libras F',
            "Tipo Camión" => $ot->tipo_camion ? $ot->tipo_camion : null,
            "Restricciones Especiales" => $ot->restriccion_especial ? $ot->restriccion_especial : null,
            "Horario Recepcion" => $ot->horario_recepcion ? $ot->horario_recepcion : null,
            "Destinatario" => "",
            "Jerarquia" => $ot->subsubhierarchy ? $ot->subsubhierarchy->jerarquia_sap : null,
            "Código Producto Cliente" => $ot->codigo_producto_cliente ? $ot->codigo_producto_cliente : null,
            "Para uso de Programa Z" => $ot->uso_programa_z ? $ot->uso_programa_z : null,
            "Etiqueta FSC" => isset($ot->fsc) ? [0 => "NO", 2 => "NO", 5 => "No", 3 => "FACTURACION Y LOGO", 4 => "FACTURACION Y LOGO", 6 => "SOLO FACTURACION"][$ot->fsc] : null,
            "Orientación Placa" => isset($ot->orientacion_placa) ? [0, 90][$ot->orientacion_placa] : null,
            "Características Adicionales" => isset($ot->prepicado) ? $ot->prepicado->descripcion  : null,
            "Indicador Facturación Diseño Estructural" => $ot->indicador_facturacion ? [1 => 'RRP', 2 => 'E-Commerce', 3 => 'Esquineros', 4 => 'Geometría', 5 => 'Participación nuevo Mercado', 6 => '', 7 => 'Innovación', 8 => 'Sustentabilidad', 9 => 'Automatización', 10 => 'No Aplica', 11 => 'Ahorro', 12 => ''][$ot->indicador_facturacion] : null,
            "Indicador Facturación Diseño Gráfico" => $ot->indicador_facturacion_diseno_grafico,
            "Tipo de Pegado" => isset($ot->pegado_terminacion) ? [0 => "No Aplica", 2 => "Pegado Interno", 3 => "Pegado Externo", 4 => "Pegado 3 Puntos", 5 => "Pegado 4 Puntos"][$ot->pegado_terminacion] : null,
            "Armado" => $ot->armado ? $ot->armado->descripcion  : null,
            "Sentido Armado" =>  $ot->sentido_armado ? [1 => "No aplica", 2 => "Ancho a la Derecha", 3 => "Ancho a la Izquierda", 4 => "Largo a la Izquierda",  5 => "Largo a la Derecha"][$ot->sentido_armado] : null,
            "Gramaje (G/m2)" => isset($ot->gramaje) ? $ot->gramaje : null,
            "Peso (G)" => isset($ot->peso) ? $ot->peso : null,
            "Espesor Caja (mm)" => isset($ot->espesor_caja) ? $ot->espesor_caja : null,
            //"Espesor (MM)" => isset($ot->espesor) ? number_format_unlimited_precision(str_replace(',', '.',$ot->espesor)) : null,
            "ECT Minimo (LB/PULG2)" => isset($ot->ect) ? number_format_unlimited_precision(str_replace(',', '.', $ot->ect)) : null,
            "FCT Minimo (LB/PULG22)" => isset($ot->fct) ? number_format_unlimited_precision(str_replace(',', '.', $ot->fct)) : null,
            "Cobb INT. ( 2 Min.) Max." => isset($ot->cobb_interior) ? $ot->cobb_interior : null,
            "Cobb EXT. ( 2 Min.) Max." => isset($ot->cobb_exterior) ? $ot->cobb_exterior : null,
            "Flexion de Aleta (N)" => isset($ot->flexion_aleta) ? $ot->flexion_aleta : null,
            "Mullen (LB/PULG2)" => isset($ot->mullen) ? number_format_unlimited_precision(str_replace(',', '.', $ot->mullen)) : null,
            "Resistencia mínima (Humeda)" => "",
            "Incisión Rayado Long.[N]" => isset($ot->incision_rayado_longitudinal) ? $ot->incision_rayado_longitudinal : null,
            "Incisión Rayado Transv.[N]" => isset($ot->incision_rayado_vertical) ? $ot->incision_rayado_vertical : null,
            "DST (BPI)" => isset($ot->dst) ? $ot->dst : null,
            "Espesor Placa (mm)" => isset($ot->espesor_placa) ? $ot->espesor_placa : null,
            "Porosidad (SEG)" => isset($ot->porosidad) ? $ot->porosidad : null,
            "Brillo (%)" => isset($ot->brillo) ? $ot->brillo : null,
            "Rigidez 4 Puntos Longitudinal (N/MM)" => isset($ot->rigidez_4_ptos_long) ? $ot->rigidez_4_ptos_long : null,
            "Rigidez 4 Puntos Transversal (N/MM)" => isset($ot->rigidez_4_ptos_transv) ? $ot->rigidez_4_ptos_transv : null,
            "Angulo de Deslizamiento-Tapa Exterior (°)" => isset($ot->angulo_deslizamiento_tapa_exterior) ? $ot->angulo_deslizamiento_tapa_exterior : null,
            "Angulo de Deslizamiento-Tapa Interior (°)" => isset($ot->angulo_deslizamiento_tapa_interior) ? $ot->angulo_deslizamiento_tapa_interior : null,
            "Resistencia al Frote" => isset($ot->resistencia_frote) ? $ot->resistencia_frote : null,
            "Contenido Reciclado (%)" => isset($ot->contenido_reciclado) ? $ot->contenido_reciclado : null,
            "Observaciones" => $ot->observacion,
            "Referencia Material" => isset($ot->material_referencia) ? $ot->material_referencia->codigo : null,
            "Bloqueo Referencia" =>  isset($ot->bloqueo_referencia) && $ot->bloqueo_referencia == 1 ? "SI" : "NO",
            // Conversión Lista de Materiales
            "Golpes al Largo" => $ot->golpes_largo,
            "Golpes al Ancho" => $ot->golpes_ancho,
            "Largura HC (MM)" => $ot->larguraHc,
            "Anchura HC (MM)" => $ot->anchuraHc,
            "Nombre Color 1 (INTERIOR TyR)" => $ot->color_1 ? $ot->color_1->descripcion : null,
            "Código Color 1 (INTERIOR TyR)" => $ot->color_1 ? $ot->color_1->codigo : null,
            "Clisse Cm2 1 (INTERIOR TyR)" => $ot->cm2_clisse_color_1 ? $ot->cm2_clisse_color_1 : null,
            "Gramos Color 1 (INTERIOR TyR)" => $ot->consumo1,
            "Nombre Color 2" => $ot->color_2 ? $ot->color_2->descripcion : null,
            "Código Color 2" => $ot->color_2 ? $ot->color_2->codigo : null,
            "Clisse Cm2 2" => $ot->cm2_clisse_color_2 ? $ot->cm2_clisse_color_2 : null,
            "Gramos Color 2" => $ot->consumo2,
            "Nombre Color 3" => $ot->color_3 ? $ot->color_3->descripcion : null,
            "Código Color 3" => $ot->color_3 ? $ot->color_3->codigo : null,
            "Clisse Cm2 3" => $ot->cm2_clisse_color_3 ? $ot->cm2_clisse_color_3 : null,
            "Gramos Color 3" => $ot->consumo3,
            "Nombre Color 4" => $ot->color_4 ? $ot->color_4->descripcion : null,
            "Código Color 4" => $ot->color_4 ? $ot->color_4->codigo : null,
            "Clisse Cm2 4" => $ot->cm2_clisse_color_4 ? $ot->cm2_clisse_color_4 : null,
            "Gramos Color 4" => $ot->consumo4,
            "Nombre Color 5" => $ot->color_5 ? $ot->color_5->descripcion : null,
            "Código Color 5" => $ot->color_5 ? $ot->color_5->codigo : null,
            "Clisse Cm2 5" => $ot->cm2_clisse_color_5 ? $ot->cm2_clisse_color_5 : null,
            "Gramos Color 5" => $ot->consumo5,
            "Nombre Color 6" => $ot->color_6 ? $ot->color_6->descripcion : null,
            "Código Color 6" => $ot->color_6 ? $ot->color_6->codigo : null,
            "Clisse Cm2 6" => $ot->cm2_clisse_color_6 ? $ot->cm2_clisse_color_6 : null,
            "Gramos Color 6" => $ot->consumo6,
            "Nombre Color 7" => $ot->color_7 ? $ot->color_7->descripcion : null,
            "Código Color 7" => $ot->color_7 ? $ot->color_7->codigo : null,
            "Clisse Cm2 7" => $ot->cm2_clisse_color_7 ? $ot->cm2_clisse_color_7 : null,
            "Gramos Color 7" => $ot->consumo7,
            "Barniz UV" => $ot->consumoBarnizUV,
            "Consumo Adhesivo PVA" => $ot->gramosAdhesivo,
            "Total clisse cm2" => $ot->total_cm2_clisse ? $ot->total_cm2_clisse : null,
            // "Veces del Item en el Set" => isset($ot->veces_item) ? $ot->veces_item : null,
            // "Nombre Color Interno" => $ot->color_interno ? $ot->color_interno_detalle->descripcion : null,
            // "Código Color Interno" => $ot->color_interno ? $ot->color_interno_detalle->codigo : null,
            //"Gramos Color Interno" => $ot->consumoColorInterno,
            // "Consumo Pegado" => $ot->consumoPegado,
            //"Gramos de Adhesivo" => $ot->gramosAdhesivo,
            //"Recubrimiento Cera Interior (GR)" => $ot->consumoCeraInterior,
            //"Recubrimiento Cera Exterior (GR)" => $ot->consumoCeraExterior,
            // // "Barniz Interior" => $ot->consumoBarniz,
            // "Barniz Interior" => $ot->porcentaje_barniz_interior,
            // "Barniz Exterior" => "",

            //"Armado" => isset($ot->armado) ? $ot->armado->descripcion : null,

            // Carton
            "Gramaje" => $ot->carton ? number_format($ot->carton->peso, 0, ',', '.') : null,
            //"Espesor (MM)" => $ot->carton ? number_format($ot->carton->espesor, 2, ',', '.') : null,
            "Onda" => $ot->carton ? $ot->carton->onda : null,
            "Proceso" => $ot->process_id ? $ot->proceso->descripcion : null,
            "Color del Cartón" => isset($ot->carton_color) ? [1 => "Café", 2 => "Blanco"][$ot->carton_color] : null,
            //"Tipo de Tabique" => $ot->tipo_tabique,
            //"Complejidad de Impresión" => $ot->complejidad_impresion, //Ajuste Evolutivo 24-06 de fecha 03-04-2024 correo del cliente
            "Impresión de Borde" => $ot->impresion_borde,
            "Impresión Sobre Rayado" => $ot->impresion_sobre_rayado,
            //"Rayado Desfasado" => $ot->rayado_desfasado,
            "Maquila" => isset($ot->maquila) ? [1 => "Si", 0 => "No"][$ot->maquila] : null,
            "Servicio de Maquila" => isset($ot->maquila_servicio_id) ? $ot->maquila_detalle->servicio : null,
            //"Termocontraible" => isset($ot->termocontraible) ? [1 => "Si", 0 => "No"][$ot->termocontraible] : null,

            "Recubrimiento Interno" => $ot->coverage_internal_id ? $ot->coverage_internal->descripcion : null,
            "Recubrimiento Externo" => $ot->coverage_external_id ? $ot->coverage_external->descripcion : null,
            //Nuevos Campos Evolutivo 24-09
            "Clisse1" => (is_null($ot->material_id)) ? null : 'ENC' . $ot->material->codigo,
            "Matriz 1" => is_null($ot->matriz_id) ? null : $ot->matrices->material,
            "Matriz 2" => is_null($ot->matriz_id_2) ? null : $ot->matrices_2->material,
            "Matriz 3" => is_null($ot->matriz_id_3) ? null : $ot->matrices_3->material,
            /*"Secuencia productiva planta Buin 1" => $secuencia_productiva_buin_1,
                "Secuencia productiva planta Buin 2" => $secuencia_productiva_buin_2,
                "Secuencia productiva planta Buin 3" => $secuencia_productiva_buin_3,
                "Secuencia productiva planta Buin 4" => $secuencia_productiva_buin_4,
                "Secuencia productiva planta Osorno 1" => $secuencia_productiva_osorno_1,
                "Secuencia productiva planta Osorno 2" => $secuencia_productiva_osorno_2,
                "Secuencia productiva planta Osorno 3" => $secuencia_productiva_osorno_3,
                "Secuencia productiva planta Osorno 4" => $secuencia_productiva_osorno_4,
                "Secuencia productiva planta Tiltil 1" => $secuencia_productiva_tiltil_1,
                "Secuencia productiva planta Tiltil 2" => $secuencia_productiva_tiltil_2,
                "Secuencia productiva planta Tiltil 3" => $secuencia_productiva_tiltil_3,
                "Secuencia productiva planta Tiltil 4" => $secuencia_productiva_tiltil_4,*/
            "Maquina Proceso 1" => $maquina_proceso_1,
            "Maquina Proceso 2" => $maquina_proceso_2,
            "Maquina Proceso 3" => $maquina_proceso_3,
            "Maquina Proceso 4" => $maquina_proceso_4,
            "Maquina Proceso 5" => $maquina_proceso_5,
            "Maquina Proceso 6" => $maquina_proceso_6,
            "Distancia corte 1 a cinta 1" => isset($ot->distancia_cinta_1) ? $ot->distancia_cinta_1 : null,
            "Distancia corte 1 a cinta 2" => isset($ot->distancia_cinta_2) ? $ot->distancia_cinta_2 : null,
            "Distancia corte 1 a cinta 3" => isset($ot->distancia_cinta_3) ? $ot->distancia_cinta_3 : null,
            "Distancia corte 1 a cinta 4" => isset($ot->distancia_cinta_4) ? $ot->distancia_cinta_4 : null,
            "Distancia corte 1 a cinta 5" => isset($ot->distancia_cinta_5) ? $ot->distancia_cinta_5 : null,
            "Distancia corte 1 a cinta 6" => isset($ot->distancia_cinta_6) ? $ot->distancia_cinta_6 : null,
            "Tipo de Cinta" => isset($ot->tipo_cinta) ? $ot->tipos_cintas->descripcion : null,
            "Cantidad Cinta por CAJA" => $ot->cintas_x_caja,
            "Consumo cinta" => $consumo_cinta,
        );

        /*}else{

            $array_data = array(
                "OT" => $ot->id,
                "Número de Material" => $ot->material ? $ot->material->codigo : null,
                "Descripción Comercial" => $ot->material ? $ot->material->descripcion : null,

                // Cliente
                "Cliente" => $ot->client->codigo,
                "Vendedor" => isset($ot->creador->nombre_sap) ? $ot->creador->nombre_sap : $ot->creador->fullname,
                // "Vendedor" => $ot->creador->fullname,
                "Largo Interior (MM)" => $ot->interno_largo,
                "Ancho Interior (MM)" => $ot->interno_ancho,
                "Alto Interior (MM)" => $ot->interno_alto,
                "Largura HM (MM)" => $ot->largura_hm,
                "Anchura HM (MM)" => $ot->anchura_hm,
                "Largo Exterior (MM)" => $ot->externo_largo,
                "Ancho Exterior (MM)" => $ot->externo_ancho,
                "Alto Exterior (MM)" => $ot->externo_alto,

                // Carton
                "Cartón" => $ot->carton ? $ot->carton->codigo : null,
                "Tipo de Producto (Tipo Item)" => isset($ot->productType) ? $ot->productType->descripcion : null,
                "Estilo de Producto" => isset($ot->style) ? $ot->style->glosa : null,
                "Caracteristicas Estilo" =>  $ot->caracteristicas_adicionales ? $ot->caracteristicas_adicionales : null,
                "Rayado C1/R1 (MM)" => isset($ot->rayado_c1r1) ? $ot->rayado_c1r1 : null,
                "Rayado R1/R2 (MM)" => isset($ot->rayado_r1_r2) ? $ot->rayado_r1_r2 : null,
                "Rayado R2/C2 (MM)" => isset($ot->rayado_r2_c2) ? $ot->rayado_r2_c2 : null,
                "Tipo de Rayado" => isset($ot->rayado_type_id) ? $ot->rayado->descripcion : null,
                "Tipo Impresión" => $ot->impresion ? $ot->impresion_detalle->descripcion : null,
                "Número de Colores" => $ot->numero_colores,
                "Prueba de Color" => isset($ot->prueba_color) ? [1 => "Si", 0 => "No"][$ot->prueba_color] : null,
                "Recorte Característico (M2)" => $detalle_recorte_caracteristico,
                "Recorte Adicional (M2)" => $detalle_recorte_adicional,
                "Plano CAD" => isset($ot->cad) ? $ot->cad : null,
                "Area Producto (M2)" => $detalle_area_producto,
                "Estado de Palletizado" => isset($ot->pallet_status_type_id) ? $ot->pallet_status->descripcion : null,
                "Tipo de Pallet" => $ot->tipo_pallet ? $ot->tipo_pallet->descripcion : null,
                "Tratamiento de Pallet" => isset($ot->pallet_treatment) ? [1 => "Si", 0 => "No"][$ot->pallet_treatment] : null,
                "Nro Cajas por Pallet" => $ot->cajas_por_pallet ? $ot->cajas_por_pallet : null,
                "Nro Placas por Pallet" => $ot->placas_por_pallet ? $ot->placas_por_pallet : null,
                "Patron Carga Pallet" => $ot->patron_pallet ? $ot->patron_pallet->descripcion : null,
                "Patron Zuncho Bulto" => $ot->patron_zuncho_bulto ? [1 => "2x0", 2 => "2x1", 3 => "2x2"][$ot->patron_zuncho_bulto] : null,
                "Proteccion" => isset($ot->protection_type_id) ? $ot->protection->descripcion : null,
                "Patron Zuncho Pallet" => $ot->patron_zuncho_bulto ? [1 => "2x0", 2 => "2x1", 3 => "2x2"][$ot->patron_zuncho_bulto] : null,
                "Protección Pallet" => $ot->proteccion_pallet ? $ot->proteccion_pallet->descripcion : null,
                "Nro Cajas por Paquete" => $ot->cajas_por_paquete ? $ot->cajas_por_paquete->descripcion : null,
                "Patron Zuncho Paquete" => $ot->patron_zuncho_paquete ? $ot->patron_zuncho_paquete : null,
                "Termocontraible" => isset($ot->termocontraible) ? [1 => "Si", 0 => "No"][$ot->termocontraible] : null,
                "Nro Cajas por Unitizados" => $ot->paquetes_por_unitizado ? $ot->paquetes_por_unitizado : null,
                "Nro Unitizados por Pallet" => $ot->unitizado_por_pallet ? $ot->unitizado_por_pallet : null,
                "Tipo Formato Etiqueta Pallet" => $ot->formato_etiqueta_pallet ? $ot->formato_etiqueta_pallet->descripcion : null,
                "Nro Etiqueta Pallet" => $ot->numero_etiquetas ? [0, 1, 2, 3, 4][$ot->numero_etiquetas] : null,
                "Certificado Calidad" => $ot->qa ? $ot->qa->descripcion : null,
                "BCT MIN (LB)" => $ot->bct_min_lb,
                "Unidad Medida BCT" => 'Libras F',
                "Tipo Camión" => $ot->tipo_camion ? $ot->tipo_camion : null,
                "Restricciones Especiales" => $ot->restriccion_especial ? $ot->restriccion_especial : null,
                "Horario Recepcion" => $ot->horario_recepcion ? $ot->horario_recepcion : null,
                "Destinatario" => "",
                "Jerarquia" => $ot->subsubhierarchy ? $ot->subsubhierarchy->jerarquia_sap : null,
                "Código Producto Cliente" => $ot->codigo_producto_cliente ? $ot->codigo_producto_cliente : null,
                "Para uso de Programa Z" => $ot->uso_programa_z ? $ot->uso_programa_z : null,
                "Etiqueta FSC" => isset($ot->fsc) ? [0 => "NO", 2 => "NO", 5 => "No", 3 => "FACTURACION Y LOGO", 4 => "FACTURACION Y LOGO" , 6 => "SOLO FACTURACION"][$ot->fsc] : null,
                "Orientación Placa" => isset($ot->orientacion_placa) ? [0, 90][$ot->orientacion_placa] : null,
                "Características Adicionales" => isset($ot->prepicado) ? $ot->prepicado->descripcion  : null,
                "Indicador Facturación Diseño Estructural" => $ot->indicador_facturacion ? [1=>'RRP',2=>'E-Commerce',3=>'Esquineros',4=>'Geometría',5=>'Participación nuevo Mercado',6=>'',7=>'Innovación',8=>'Sustentabilidad',9=>'Automatización',10=>'No Aplica',11=>'Ahorro',12=>''][$ot->indicador_facturacion] : null,
                "Indicador Facturación Diseño Gráfico" => $ot->indicador_facturacion_diseno_grafico,
                "Tipo de Pegado" => isset($ot->pegado_terminacion) ? [0=>"No Aplica", 2=>"Pegado Interno", 3=>"Pegado Externo", 4=>"Pegado 3 Puntos", 5=>"Pegado 4 Puntos"][$ot->pegado_terminacion] : null,
                "Armado" => $ot->armado ? $ot->armado->descripcion  : null,
                "Sentido Armado" =>  $ot->sentido_armado ? [1 => "No aplica", 2 => "Ancho a la Derecha", 3 => "Ancho a la Izquierda", 4 => "Largo a la Izquierda",  5 => "Largo a la Derecha"][$ot->sentido_armado] : null,
                "Gramaje (G/m2)" => isset($ot->gramaje) ? $ot->gramaje : null,
                "Peso (G)" => isset($ot->peso) ? $ot->peso : null,
                "Espesor Caja (mm)" => isset($ot->espesor_caja) ? $ot->espesor_caja : null,
                //"Espesor (MM)" => isset($ot->espesor) ? number_format_unlimited_precision(str_replace(',', '.',$ot->espesor)) : null,
                "ECT Minimo (LB/PULG2)" => isset($ot->ect) ? number_format_unlimited_precision(str_replace(',', '.',$ot->ect)) : null,
                "FCT Minimo (LB/PULG22)" => isset($ot->fct) ? number_format_unlimited_precision(str_replace(',', '.',$ot->fct)) : null,
                "Cobb INT. ( 2 Min.) Max." => isset($ot->cobb_interior) ? $ot->cobb_interior : null,
                "Cobb EXT. ( 2 Min.) Max." => isset($ot->cobb_exterior) ? $ot->cobb_exterior : null,
                "Flexion de Aleta (N)" => isset($ot->flexion_aleta) ? $ot->flexion_aleta : null,
                "Mullen (LB/PULG2)" => isset($ot->mullen) ? number_format_unlimited_precision(str_replace(',', '.',$ot->mullen)) : null,
                "Resistencia mínima (Humeda)" => "",
                "Incisión Rayado Long.[N]" => isset($ot->incision_rayado_longitudinal) ? $ot->incision_rayado_longitudinal : null,
                "Incisión Rayado Transv.[N]" => isset($ot->incision_rayado_vertical) ? $ot->incision_rayado_vertical : null,
                "DST (BPI)" => isset($ot->dst) ? $ot->dst : null,
                "Espesor Placa (mm)" => isset($ot->espesor_placa) ? $ot->espesor_placa : null,
                "Porosidad (SEG)" => isset($ot->porosidad) ? $ot->porosidad : null,
                "Brillo (%)" => isset($ot->brillo) ? $ot->brillo : null,
                "Rigidez 4 Puntos Longitudinal (N/MM)" => isset($ot->rigidez_4_ptos_long) ? $ot->rigidez_4_ptos_long : null,
                "Rigidez 4 Puntos Transversal (N/MM)" => isset($ot->rigidez_4_ptos_transv) ? $ot->rigidez_4_ptos_transv : null,
                "Angulo de Deslizamiento-Tapa Exterior (°)" => isset($ot->angulo_deslizamiento_tapa_exterior) ? $ot->angulo_deslizamiento_tapa_exterior : null,
                "Angulo de Deslizamiento-Tapa Interior (°)" => isset($ot->angulo_deslizamiento_tapa_interior) ? $ot->angulo_deslizamiento_tapa_interior : null,
                "Resistencia al Frote" => isset($ot->resistencia_frote) ? $ot->resistencia_frote : null,
                "Contenido Reciclado (%)" => isset($ot->contenido_reciclado) ? $ot->contenido_reciclado : null,
                "Observaciones" => $ot->observacion,
                "Referencia Material" => isset($ot->material_referencia) ? $ot->material_referencia->codigo : null,
                "Bloqueo Referencia" =>  isset($ot->bloqueo_referencia) && $ot->bloqueo_referencia == 1 ? "SI" : "NO",
                // Conversión Lista de Materiales
                "Golpes al Largo" => $ot->golpes_largo,
                "Golpes al Ancho" => $ot->golpes_ancho,
                "Largura HC (MM)" => $ot->larguraHc,
                "Anchura HC (MM)" => $ot->anchuraHc,
                "Nombre Color 1 (INTERIOR TyR)" => $ot->color_1 ? $ot->color_1->descripcion : null,
                "Código Color 1 (INTERIOR TyR)" => $ot->color_1 ? $ot->color_1->codigo : null,
                "Gramos Color 1 (INTERIOR TyR)" => $ot->consumo1,
                "Nombre Color 2" => $ot->color_2 ? $ot->color_2->descripcion : null,
                "Código Color 2" => $ot->color_2 ? $ot->color_2->codigo : null,
                "Gramos Color 2" => $ot->consumo2,
                "Nombre Color 3" => $ot->color_3 ? $ot->color_3->descripcion : null,
                "Código Color 3" => $ot->color_3 ? $ot->color_3->codigo : null,
                "Gramos Color 3" => $ot->consumo3,
                "Nombre Color 4" => $ot->color_4 ? $ot->color_4->descripcion : null,
                "Código Color 4" => $ot->color_4 ? $ot->color_4->codigo : null,
                "Gramos Color 4" => $ot->consumo4,
                "Nombre Color 5" => $ot->color_5 ? $ot->color_5->descripcion : null,
                "Código Color 5" => $ot->color_5 ? $ot->color_5->codigo : null,
                "Gramos Color 5" => $ot->consumo5,
                "Nombre Color 6" => $ot->color_6 ? $ot->color_6->descripcion : null,
                "Código Color 6" => $ot->color_6 ? $ot->color_6->codigo : null,
                "Gramos Color 6" => $ot->consumo6,
                //CAMBIO A COLOR
                "Nombre Color 7" => $ot->consumoBarnizUV,
                "Código Color 7" => $ot->gramosAdhesivo,
                "Gramos Color 7" => isset($ot->veces_item) ? $ot->veces_item : null,

                // "Barniz UV" => $ot->consumoBarnizUV,
                // "Consumo Adhesivo PVA" => $ot->gramosAdhesivo,
                // "Veces del Item en el Set" => isset($ot->veces_item) ? $ot->veces_item : null,

                //FIN CAMBIO A COLOR

               // "Nombre Color Interno" => $ot->color_interno ? $ot->color_interno_detalle->descripcion : null,
               // "Código Color Interno" => $ot->color_interno ? $ot->color_interno_detalle->codigo : null,
                //"Gramos Color Interno" => $ot->consumoColorInterno,
                // "Consumo Pegado" => $ot->consumoPegado,
                //"Gramos de Adhesivo" => $ot->gramosAdhesivo,
                //"Recubrimiento Cera Interior (GR)" => $ot->consumoCeraInterior,
                //"Recubrimiento Cera Exterior (GR)" => $ot->consumoCeraExterior,
                // // "Barniz Interior" => $ot->consumoBarniz,
                // "Barniz Interior" => $ot->porcentaje_barniz_interior,
                // "Barniz Exterior" => "",

                //"Armado" => isset($ot->armado) ? $ot->armado->descripcion : null,

                // Carton
                "Gramaje" => $ot->carton ? number_format($ot->carton->peso, 0, ',', '.') : null,
                //"Espesor (MM)" => $ot->carton ? number_format($ot->carton->espesor, 2, ',', '.') : null,
                "Onda" => $ot->carton ? $ot->carton->onda : null,
                "Proceso" => $ot->process_id ? $ot->proceso->descripcion : null,
                "Color del Cartón" => isset($ot->carton_color) ? [1 => "Café", 2 => "Blanco"][$ot->carton_color] : null,
                //"Termocontraible" => isset($ot->termocontraible) ? [1 => "Si", 0 => "No"][$ot->termocontraible] : null,
                //"Tipo de Tabique" => $ot->tipo_tabique,
                //"Complejidad de Impresión" => $ot->complejidad_impresion, //Ajuste Evolutivo 24-06 de fecha 03-04-2024 correo del cliente
                "Impresión de Borde" => $ot->impresion_borde,
                "Impresión Sobre Rayado" => $ot->impresion_sobre_rayado,
                //"Rayado Desfasado" => $ot->rayado_desfasado,
                "Maquila" => isset($ot->maquila) ? [1 => "Si", 0 => "No"][$ot->maquila] : null,
                "Servicio de Maquila" => isset($ot->maquila_servicio_id) ? $ot->maquila_detalle->servicio : null,
                "Recubrimiento Interno" => $ot->coverage_internal_id ? $ot->coverage_internal->descripcion : null,
                "Recubrimiento Externo" => $ot->coverage_external_id ? $ot->coverage_external->descripcion : null,

            );
        }*/




        return $item_array = $array_data;
    }

    //Evolutivo 24-09
    public function descargarExcelSap($id)
    {
        $ot = WorkOrder::with(
            'armado',
            'canal',
            'client',
            'creador',
            'productType',
            "users",
            "material",
            "material_referencia",
            "subsubhierarchy",
            "tipo_pallet",
            "cajas_por_paquete",
            "patron_pallet",
            "proteccion_pallet",
            "formato_etiqueta_pallet",
            "qa",
            "prepicado",
            "carton",
            "style",
            "rayado",
            "pallet_status",
            "protection",
            "characteristics",
        )->where('id', $id)->first();

        $item_array = self::array_data_excel_sap($ot);
        $titulo = 'Excel SAP OT'. $id . ' ';

        Excel::create($titulo . Carbon::now(), function ($excel) use ($item_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet($titulo, function ($sheet) use ($item_array) {
                // Se aplica foreach porque la informacion tiene ser mostrada de forma vertical
                // solo utilizando las celdas A y B
                $i = 1;
                foreach ($item_array as $key => $fila) {
                    $sheet->setCellValue('A' . $i, $key);
                    $sheet->setCellValue('B' . $i, $fila[0]);
                    $sheet->setCellValue('C' . $i, $fila[1]);
                    $i++;
                }
            });
        })->download('xlsx');
    }

    public function array_data_excel_sap($ot)
    {
        //Validamos los campos para que siempre imprima 4 decimales, ya sea agregar con cero o recortar los datos de la BD
        $recorte_caracteristico = number_format_unlimited_precision($ot->recorteCaracteristico, ",", ".", 7);
        if (isset($recorte_caracteristico) && $recorte_caracteristico != 'N/A') {
            if ($recorte_caracteristico === '0') {
                $detalle_recorte_caracteristico = $recorte_caracteristico;
            } else {
                $recorte = number_format_unlimited_precision($ot->recorteCaracteristico, ",", ".", 7);
                $decimal = explode(',', $recorte);
                $truncate_decimal = substr($decimal[1], 0, 4);
                $pad = str_pad($truncate_decimal, 4, "0");
                $detalle_recorte_caracteristico = $decimal[0] . ',' . $pad;
            }
        } else {
            $detalle_recorte_caracteristico = 'N/A';
        }

        //recorte adicional
        if ($ot->recorte_adicional > 0) {
            $recorte_adicional = number_format_unlimited_precision($ot->recorte_adicional);
            $decimal_adicional = explode(',', $recorte_adicional);
            $truncate_decimal_adicional = substr($decimal_adicional[1], 0, 4);
            $pad_adicional = str_pad($truncate_decimal_adicional, 4, "0");
            $detalle_recorte_adicional = $decimal_adicional[0] . ',' . $pad_adicional;
        } else {
            $detalle_recorte_adicional = '0,0000';
        }

        if ($ot->area_producto_calculo > 0) {
            $recorte_producto = number_format_unlimited_precision($ot->area_producto_calculo);
            $decimal_producto = explode(',', $recorte_producto);
            // $truncate_decimal_producto = substr($decimal_producto[1], 0, 4);
            // $pad_producto = str_pad($truncate_decimal_producto, 4, "0");
            $concaterna_producto = $decimal_producto[0] . '.' . $decimal_producto[1];
            $detalle_area_producto = $concaterna_producto * 1000000;
        } else {
            $detalle_area_producto = 0;
        }

        // Número de Material
        $numero_material = '';
        if (!is_null($ot->material_asignado) && !is_null($ot->material_id)) {
            $numero_material = 'GE1' . $ot->material_code;
        } else {
            $numero_material = '';
        }

        // Número de Semielaborado
        $semielaborado = '';
        if (!is_null($ot->material_asignado) && !is_null($ot->material_id)) {
            if ($ot->product_type_id == 16 || $ot->product_type_id == 21) {
                $semielaborado = '';
            } else {
                $semielaborado = 'GE2' . $ot->material_code;
            }
        } else {
            $semielaborado = '';
        }

        //Numero de Pieza Interior
        $numero_material_pieza = '';
        if (!is_null($ot->material_asignado) && !is_null($ot->material_id)) {
            if ($ot->product_type_id == 16 || $ot->product_type_id == 21) {
                $numero_material_pieza = '';
            } else {
                if ($ot->proceso_id != 15) {
                    $numero_material_pieza = '';
                } else {
                    $numero_material_pieza = 'GE3' . $ot->material_code;
                }
            }
        } else {
            $numero_material_pieza = '';
        }

        //Secuencia Operacional
        //Planta Buin

        //ORIGINAL
        $maquina_proceso_1_id = null;
        $maquina_proceso_1 = null;
        $maquina_proceso_2 = null;
        $maquina_proceso_3 = null;
        $maquina_proceso_4 = null;
        $maquina_proceso_5 = null;
        $maquina_proceso_6 = null;


        $maquina_proceso_1_descripcion = '';
        $maquina_proceso_2_descripcion = '';
        $maquina_proceso_3_descripcion = '';
        $maquina_proceso_4_descripcion = '';
        $maquina_proceso_5_descripcion = '';
        $maquina_proceso_6_descripcion = '';

        $maquina_proceso_1_descripcion_corto = '';
        $maquina_proceso_2_descripcion_corto = '';


        //ALT1
        $maquina_proceso_1_1 = null;
        $maquina_proceso_2_1 = null;
        $maquina_proceso_3_1 = null;
        $maquina_proceso_4_1 = null;
        $maquina_proceso_5_1 = null;
        $maquina_proceso_6_1 = null;

        $maquina_proceso_1_1_descripcion = '';
        $maquina_proceso_2_1_descripcion = '';
        $maquina_proceso_3_1_descripcion = '';
        $maquina_proceso_4_1_descripcion = '';
        $maquina_proceso_5_1_descripcion = '';
        $maquina_proceso_6_1_descripcion = '';

        // ALT2


        $maquina_proceso_1_2 = null;
        $maquina_proceso_2_2 = null;
        $maquina_proceso_3_2 = null;
        $maquina_proceso_4_2 = null;
        $maquina_proceso_5_2 = null;
        $maquina_proceso_6_2 = null;

        $maquina_proceso_1_2_descripcion = '';
        $maquina_proceso_2_2_descripcion = '';
        $maquina_proceso_3_2_descripcion = '';
        $maquina_proceso_4_2_descripcion = '';
        $maquina_proceso_5_2_descripcion = '';
        $maquina_proceso_6_2_descripcion = '';

        // ALT3

        $maquina_proceso_1_3 = null;
        $maquina_proceso_2_3 = null;
        $maquina_proceso_3_3 = null;
        $maquina_proceso_4_3 = null;
        $maquina_proceso_5_3 = null;
        $maquina_proceso_6_3 = null;

        $maquina_proceso_1_3_descripcion = '';
        $maquina_proceso_2_3_descripcion = '';
        $maquina_proceso_3_3_descripcion = '';
        $maquina_proceso_4_3_descripcion = '';
        $maquina_proceso_5_3_descripcion = '';
        $maquina_proceso_6_3_descripcion = '';

        // ALT4

        $maquina_proceso_1_4 = null;
        $maquina_proceso_2_4 = null;
        $maquina_proceso_3_4 = null;
        $maquina_proceso_4_4 = null;
        $maquina_proceso_5_4 = null;
        $maquina_proceso_6_4 = null;

        $maquina_proceso_1_4_descripcion = '';
        $maquina_proceso_2_4_descripcion = '';
        $maquina_proceso_3_4_descripcion = '';
        $maquina_proceso_4_4_descripcion = '';
        $maquina_proceso_5_4_descripcion = '';
        $maquina_proceso_6_4_descripcion = '';

        //ALT5

        $maquina_proceso_1_5 = null;
        $maquina_proceso_2_5 = null;
        $maquina_proceso_3_5 = null;
        $maquina_proceso_4_5 = null;
        $maquina_proceso_5_5 = null;
        $maquina_proceso_6_5 = null;

        $maquina_proceso_1_5_descripcion = '';
        $maquina_proceso_2_5_descripcion = '';
        $maquina_proceso_3_5_descripcion = '';
        $maquina_proceso_4_5_descripcion = '';
        $maquina_proceso_5_5_descripcion = '';
        $maquina_proceso_6_5_descripcion = '';


        if ($ot->so_planta_original == 1) { //Planta Original
            if (!is_null($ot->so_planta_original_select_values)) {
                $secuencia_productiva_planta_original = json_decode($ot->so_planta_original_select_values, true);
                for ($i = 1; $i <= count($secuencia_productiva_planta_original); $i++) {
                    if (isset($secuencia_productiva_planta_original['fila_' . $i])) {
                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['org'])) {
                            $secuencia_original = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->codigo;
                            $secuencia_original_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->descripcion;
                            $secuencia_original_descripcion_corto = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->nombre_corto;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1 = $secuencia_original;
                                    $maquina_proceso_1_descripcion = $secuencia_original_descripcion;
                                    $maquina_proceso_1_id = $secuencia_productiva_planta_original['fila_' . $i]['org'];
                                    $maquina_proceso_1_descripcion_corto = $secuencia_original_descripcion_corto;

                                    break;
                                case 2:
                                    $maquina_proceso_2 = $secuencia_original;
                                    $maquina_proceso_2_descripcion = $secuencia_original_descripcion;
                                    $maquina_proceso_2_descripcion_corto = $secuencia_original_descripcion_corto;

                                    break;
                                case 3:
                                    $maquina_proceso_3 = $secuencia_original;
                                    $maquina_proceso_3_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4 = $secuencia_original;
                                    $maquina_proceso_4_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5 = $secuencia_original;
                                    $maquina_proceso_5_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6 = $secuencia_original;
                                    $maquina_proceso_6_descripcion = $secuencia_original_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt1'])) {
                            $secuencia_alt1 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt1'])->codigo;
                            $secuencia_alt1_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt1'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_1 = $secuencia_alt1;
                                    $maquina_proceso_1_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_1 = $secuencia_alt1;
                                    $maquina_proceso_2_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_1 = $secuencia_alt1;
                                    $maquina_proceso_3_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_1 = $secuencia_alt1;
                                    $maquina_proceso_4_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_1 = $secuencia_alt1;
                                    $maquina_proceso_5_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_1 = $secuencia_alt1;
                                    $maquina_proceso_6_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt2'])) {
                            $secuencia_alt2 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt2'])->codigo;
                            $secuencia_alt2_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt2'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_2 = $secuencia_alt2;
                                    $maquina_proceso_1_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_2 = $secuencia_alt2;
                                    $maquina_proceso_2_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_2 = $secuencia_alt2;
                                    $maquina_proceso_3_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_2 = $secuencia_alt2;
                                    $maquina_proceso_4_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_2 = $secuencia_alt2;
                                    $maquina_proceso_5_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_2 = $secuencia_alt2;
                                    $maquina_proceso_6_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt3'])) {
                            $secuencia_alt3 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt3'])->codigo;
                            $secuencia_alt3_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt3'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_3 = $secuencia_alt3;
                                    $maquina_proceso_1_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_3 = $secuencia_alt3;
                                    $maquina_proceso_2_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_3 = $secuencia_alt3;
                                    $maquina_proceso_3_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_3 = $secuencia_alt3;
                                    $maquina_proceso_4_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_3 = $secuencia_alt3;
                                    $maquina_proceso_5_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_3 = $secuencia_alt3;
                                    $maquina_proceso_6_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt4'])) {
                            $secuencia_alt4 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt4'])->codigo;
                            $secuencia_alt4_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt4'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_4 = $secuencia_alt4;
                                    $maquina_proceso_1_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_4 = $secuencia_alt4;
                                    $maquina_proceso_2_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_4 = $secuencia_alt4;
                                    $maquina_proceso_3_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_4 = $secuencia_alt4;
                                    $maquina_proceso_4_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_4 = $secuencia_alt4;
                                    $maquina_proceso_5_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_4 = $secuencia_alt4;
                                    $maquina_proceso_6_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt5'])) {
                            $secuencia_alt5 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt5'])->codigo;
                            $secuencia_alt5_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt5'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_5 = $secuencia_alt5;
                                    $maquina_proceso_1_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_5 = $secuencia_alt5;
                                    $maquina_proceso_2_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_5 = $secuencia_alt4;
                                    $maquina_proceso_3_5_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_5 = $secuencia_alt5;
                                    $maquina_proceso_4_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_5 = $secuencia_alt5;
                                    $maquina_proceso_5_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_5 = $secuencia_alt5;
                                    $maquina_proceso_6_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }
                    }
                }
            }
        }

        //Planta Original - Osorno
        if ($ot->so_planta_original == 3) {
            if (!is_null($ot->so_planta_original_select_values)) {
                $secuencia_productiva_planta_original = json_decode($ot->so_planta_original_select_values, true);
                for ($i = 1; $i <= count($secuencia_productiva_planta_original); $i++) {
                    if (isset($secuencia_productiva_planta_original['fila_' . $i])) {
                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['org'])) {
                            $secuencia_original = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->codigo;
                            $secuencia_original_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->descripcion;
                            $secuencia_original_descripcion_corto = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->nombre_corto;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1 = $secuencia_original;
                                    $maquina_proceso_1_descripcion = $secuencia_original_descripcion;
                                    $maquina_proceso_1_id = $secuencia_productiva_planta_original['fila_' . $i]['org'];
                                    $maquina_proceso_1_descripcion_corto = $secuencia_original_descripcion_corto;

                                    break;
                                case 2:
                                    $maquina_proceso_2 = $secuencia_original;
                                    $maquina_proceso_2_descripcion = $secuencia_original_descripcion;
                                    $maquina_proceso_2_descripcion_corto = $secuencia_original_descripcion_corto;
                                    break;
                                case 3:
                                    $maquina_proceso_3 = $secuencia_original;
                                    $maquina_proceso_3_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4 = $secuencia_original;
                                    $maquina_proceso_4_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5 = $secuencia_original;
                                    $maquina_proceso_5_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6 = $secuencia_original;
                                    $maquina_proceso_6_descripcion = $secuencia_original_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt1'])) {
                            $secuencia_alt1 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt1'])->codigo;
                            $secuencia_alt1_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt1'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_1 = $secuencia_alt1;
                                    $maquina_proceso_1_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_1 = $secuencia_alt1;
                                    $maquina_proceso_2_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_1 = $secuencia_alt1;
                                    $maquina_proceso_3_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_1 = $secuencia_alt1;
                                    $maquina_proceso_4_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_1 = $secuencia_alt1;
                                    $maquina_proceso_5_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_1 = $secuencia_alt1;
                                    $maquina_proceso_6_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt2'])) {
                            $secuencia_alt2 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt2'])->codigo;
                            $secuencia_alt2_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt2'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_2 = $secuencia_alt2;
                                    $maquina_proceso_1_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_2 = $secuencia_alt2;
                                    $maquina_proceso_2_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_2 = $secuencia_alt2;
                                    $maquina_proceso_3_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_2 = $secuencia_alt2;
                                    $maquina_proceso_4_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_2 = $secuencia_alt2;
                                    $maquina_proceso_5_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_2 = $secuencia_alt2;
                                    $maquina_proceso_6_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt3'])) {
                            $secuencia_alt3 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt3'])->codigo;
                            $secuencia_alt3_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt3'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_3 = $secuencia_alt3;
                                    $maquina_proceso_1_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_3 = $secuencia_alt3;
                                    $maquina_proceso_2_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_3 = $secuencia_alt3;
                                    $maquina_proceso_3_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_3 = $secuencia_alt3;
                                    $maquina_proceso_4_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_3 = $secuencia_alt3;
                                    $maquina_proceso_5_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_3 = $secuencia_alt3;
                                    $maquina_proceso_6_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt4'])) {
                            $secuencia_alt4 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt4'])->codigo;
                            $secuencia_alt4_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt4'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_4 = $secuencia_alt4;
                                    $maquina_proceso_1_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_4 = $secuencia_alt4;
                                    $maquina_proceso_2_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_4 = $secuencia_alt4;
                                    $maquina_proceso_3_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_4 = $secuencia_alt4;
                                    $maquina_proceso_4_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_4 = $secuencia_alt4;
                                    $maquina_proceso_5_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_4 = $secuencia_alt4;
                                    $maquina_proceso_6_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt5'])) {
                            $secuencia_alt5 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt5'])->codigo;
                            $secuencia_alt5_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt5'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_5 = $secuencia_alt5;
                                    $maquina_proceso_1_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_5 = $secuencia_alt5;
                                    $maquina_proceso_2_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_5 = $secuencia_alt4;
                                    $maquina_proceso_3_5_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_5 = $secuencia_alt5;
                                    $maquina_proceso_4_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_5 = $secuencia_alt5;
                                    $maquina_proceso_5_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_5 = $secuencia_alt5;
                                    $maquina_proceso_6_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }
                        /*
                            if(isset($secuencia_productiva_planta_original['fila_'.$i]['alt1'])){
                                $secuencia_alt1 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_'.$i]['alt1'])->codigo;
                                switch  ($i) {
                                    case 1:
                                        $secuencia_productiva_osorno_1=$secuencia_productiva_osorno_1.' - '.$secuencia_alt1;
                                        break;
                                    case 2:
                                        $secuencia_productiva_osorno_2=$secuencia_productiva_osorno_2.' - '.$secuencia_alt1;
                                        break;
                                    case 3:
                                        $secuencia_productiva_osorno_3=$secuencia_productiva_osorno_3.' - '.$secuencia_alt1;
                                        break;
                                    case 4:
                                        $secuencia_productiva_osorno_4=$secuencia_productiva_osorno_4.' - '.$secuencia_alt1;
                                        break;
                                    default:
                                        # code...
                                        break;
                                }

                            }
                            if(isset($secuencia_productiva_planta_original['fila_'.$i]['alt2'])){
                                $secuencia_alt2 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_'.$i]['alt2'])->codigo;
                                switch  ($i) {
                                    case 1:
                                        $secuencia_productiva_osorno_1=$secuencia_productiva_osorno_1.' - '.$secuencia_alt2;
                                        break;
                                    case 2:
                                        $secuencia_productiva_osorno_2=$secuencia_productiva_osorno_2.' - '.$secuencia_alt2;
                                        break;
                                    case 3:
                                        $secuencia_productiva_osorno_3=$secuencia_productiva_osorno_3.' - '.$secuencia_alt2;
                                        break;
                                    case 4:
                                        $secuencia_productiva_osorno_4=$secuencia_productiva_osorno_4.' - '.$secuencia_alt2;
                                        break;
                                    default:
                                        # code...
                                        break;
                                }
                            }  */
                    }
                }
            }
        }

        //Planta TilTil
        if ($ot->so_planta_original == 2) { //Planta Original
            if (!is_null($ot->so_planta_original_select_values)) {
                $secuencia_productiva_planta_original = json_decode($ot->so_planta_original_select_values, true);
                for ($i = 1; $i <= count($secuencia_productiva_planta_original); $i++) {
                    if (isset($secuencia_productiva_planta_original['fila_' . $i])) {
                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['org'])) {
                            $secuencia_original = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->codigo;
                            $secuencia_original_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->descripcion;
                            $secuencia_original_descripcion_corto = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->nombre_corto;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1 = $secuencia_original;
                                    $maquina_proceso_1_descripcion = $secuencia_original_descripcion;
                                    $maquina_proceso_1_id = $secuencia_productiva_planta_original['fila_' . $i]['org'];
                                    $maquina_proceso_1_descripcion_corto = $secuencia_original_descripcion_corto;

                                    break;
                                case 2:
                                    $maquina_proceso_2 = $secuencia_original;
                                    $maquina_proceso_2_descripcion = $secuencia_original_descripcion;
                                    $maquina_proceso_2_descripcion_corto = $secuencia_original_descripcion_corto;

                                    break;
                                case 3:
                                    $maquina_proceso_3 = $secuencia_original;
                                    $maquina_proceso_3_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4 = $secuencia_original;
                                    $maquina_proceso_4_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5 = $secuencia_original;
                                    $maquina_proceso_5_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6 = $secuencia_original;
                                    $maquina_proceso_6_descripcion = $secuencia_original_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt1'])) {
                            $secuencia_alt1 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt1'])->codigo;
                            $secuencia_alt1_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt1'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_1 = $secuencia_alt1;
                                    $maquina_proceso_1_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_1 = $secuencia_alt1;
                                    $maquina_proceso_2_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_1 = $secuencia_alt1;
                                    $maquina_proceso_3_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_1 = $secuencia_alt1;
                                    $maquina_proceso_4_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_1 = $secuencia_alt1;
                                    $maquina_proceso_5_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_1 = $secuencia_alt1;
                                    $maquina_proceso_6_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt2'])) {
                            $secuencia_alt2 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt2'])->codigo;
                            $secuencia_alt2_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt2'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_2 = $secuencia_alt2;
                                    $maquina_proceso_1_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_2 = $secuencia_alt2;
                                    $maquina_proceso_2_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_2 = $secuencia_alt2;
                                    $maquina_proceso_3_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_2 = $secuencia_alt2;
                                    $maquina_proceso_4_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_2 = $secuencia_alt2;
                                    $maquina_proceso_5_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_2 = $secuencia_alt2;
                                    $maquina_proceso_6_2_descripcion = $secuencia_alt2_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt3'])) {
                            $secuencia_alt3 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt3'])->codigo;
                            $secuencia_alt3_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt3'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_3 = $secuencia_alt3;
                                    $maquina_proceso_1_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_3 = $secuencia_alt3;
                                    $maquina_proceso_2_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_3 = $secuencia_alt3;
                                    $maquina_proceso_3_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_3 = $secuencia_alt3;
                                    $maquina_proceso_4_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_3 = $secuencia_alt3;
                                    $maquina_proceso_5_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_3 = $secuencia_alt3;
                                    $maquina_proceso_6_3_descripcion = $secuencia_alt3_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt4'])) {
                            $secuencia_alt4 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt4'])->codigo;
                            $secuencia_alt4_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt4'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_4 = $secuencia_alt4;
                                    $maquina_proceso_1_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_4 = $secuencia_alt4;
                                    $maquina_proceso_2_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_4 = $secuencia_alt4;
                                    $maquina_proceso_3_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_4 = $secuencia_alt4;
                                    $maquina_proceso_4_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_4 = $secuencia_alt4;
                                    $maquina_proceso_5_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_4 = $secuencia_alt4;
                                    $maquina_proceso_6_4_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt5'])) {
                            $secuencia_alt5 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt5'])->codigo;
                            $secuencia_alt5_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt5'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_5 = $secuencia_alt5;
                                    $maquina_proceso_1_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_5 = $secuencia_alt5;
                                    $maquina_proceso_2_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_5 = $secuencia_alt4;
                                    $maquina_proceso_3_5_descripcion = $secuencia_alt4_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_5 = $secuencia_alt5;
                                    $maquina_proceso_4_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_5 = $secuencia_alt5;
                                    $maquina_proceso_5_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_5 = $secuencia_alt5;
                                    $maquina_proceso_6_5_descripcion = $secuencia_alt5_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }
                    }
                }
            }
        }

        //Centro / Numero de Almacen /CeBe
        if (is_null($ot->planta_id)) {
            $centro = '';
            $num_almacen = '';
            $cebe = '';
            $grupo_gastos_generales = '';
        } else {
            $grupo_planta = GrupoPlanta::where('planta_id', $ot->planta_id)->where('active', 1)->first();
            if ($grupo_planta) {
                $centro = $grupo_planta->centro;
                $num_almacen = $grupo_planta->num_almacen;

                //para obter cebe buscamos subhierearchie
                // dd($ot->subhierarchy);
                $searchsubsubhierearchie = Subsubhierarchy::find($ot->subsubhierarchy->id);
                // dd($searchsubhierearchie);
                if ($searchsubsubhierearchie) {

                    $searchsubhierearchie = Subhierarchy::find($searchsubsubhierearchie->subhierarchy_id);

                    if ($searchsubhierearchie) {
                        $hierearchie_id = $searchsubhierearchie->hierarchy_id;

                        // dd($$ot->planta_id);
                        $searchcebe = CeBe::where('hierearchie_id', $hierearchie_id)->where('planta_id', $ot->planta_id)->where('active', 1)->first();
                        if ($searchcebe) {
                            $cebe = $searchcebe->cebe;
                            $grupo_gastos_generales = $searchcebe->grupo_gastos_generales;
                        } else {
                            $cebe = '';
                            $grupo_gastos_generales = '';
                        }
                    } else {
                        $cebe = '';
                        $grupo_gastos_generales = '';
                    }
                } else {
                    $cebe = '';
                    $grupo_gastos_generales = '';
                }
            } else {
                $centro = '';
                $num_almacen = '';
                $cebe = '';
                $grupo_gastos_generales = '';
            }
        }

        //Almacen
        if ($centro == '' || is_null($centro)) {
            $almacen = '';
        } else {
            $codigo_almacen = Almacen::where('centro', $centro)->first();
            if ($codigo_almacen) {
                $almacen = $codigo_almacen->codigo;
            } else {
                $almacen = '';
            }
        }

        //Organizacion de Ventas
        if (is_null($ot->org_venta_id)) {

            $org_ventas = '';
        } else {
            $codigo_org_ventas = OrganizacionVenta::where('id', $ot->org_venta_id)->where('active', 1)->first();
            if ($codigo_org_ventas) {
                $org_ventas = $codigo_org_ventas->codigo;
            } else {
                $org_ventas = '';
            }
        }
        // dd($org_ventas);

        //Canal
        if (is_null($ot->canal_id)) {
            $canal = '';
        } else {
            $codigo_canal = Canal::where('id', $ot->canal_id)->first();
            if ($codigo_canal) {
                $canal = $codigo_canal->codigo;
            } else {
                $canal = '';
            }
        }

        //Sector
        if (is_null($ot->product_type_id)) {
            $sector = '';
        } else {
            $codigo_sector = Sector::where('product_type_id', $ot->product_type_id)->where('active', 1)->first();
            if ($codigo_sector) {
                $sector = $codigo_sector->codigo;
            } else {
                $sector = '';
            }
        }

        //Grupo Imputacion Material
        if (is_null($ot->process_id)) {
            $grupo_imp_material = '';
            $familia = '';
            $modelo_material = '';
        } else {
            $searchprocesss = Process::find($ot->process_id);

            if ($searchprocesss) {
                $desc_proceso = $searchprocesss->descripcion;

                $codigo_grupo_imp_material = GrupoImputacionMaterial::where('proceso', $desc_proceso)->where('active', 1)->first();
                if ($codigo_grupo_imp_material) {
                    $grupo_imp_material = $codigo_grupo_imp_material->codigo;
                    $familia = $codigo_grupo_imp_material->familia;
                    $modelo_material = $codigo_grupo_imp_material->material_modelo;
                } else {
                    $grupo_imp_material = '';
                    $familia = '';
                    $modelo_material = '';
                }
            } else {
                $grupo_imp_material = '';
                $familia = '';
                $modelo_material = '';
            }
        }

        //Grupo Materiales 1
        if (is_null($ot->armado_id)) {
            $grupo_materiales_1 = '';
        } else {
            $codigo_grupo_materiales_1 = GrupoMateriales1::where('armado_id', $ot->armado_id)->where('active', 1)->first();
            if ($codigo_grupo_materiales_1) {
                $grupo_materiales_1 = $codigo_grupo_materiales_1->codigo;
            } else {
                $grupo_materiales_1 = '';
            }
        }

        //Grupo Materiales 2
        if (is_null($ot->product_type_id)) {
            $grupo_materiales_2 = '';
        } else {
            $codigo_grupo_materiales_2 = GrupoMateriales2::where('pruduct_type_id', $ot->product_type_id)->where('active', 1)->first();
            if ($codigo_grupo_materiales_2) {
                $grupo_materiales_2 = $codigo_grupo_materiales_2->codigo;
            } else {
                $grupo_materiales_2 = '';
            }
        }

        //Rechazo Conjunto
        if (is_null($ot->process_id)) {
            $rechazo_conjunto = 0;
        } else {
            $porcentaje_rechazo_conjunto = RechazoConjunto::where('proceso_id', $ot->process_id)->where('active', 1)->first();
            if ($porcentaje_rechazo_conjunto) {
                $rechazo_conjunto = $porcentaje_rechazo_conjunto->porcentaje_proceso_solo + $porcentaje_rechazo_conjunto->porcentaje_proceso_barniz + $porcentaje_rechazo_conjunto->porcentaje_proceso_maquila;
            } else {
                $rechazo_conjunto = 0;
            }
        }

        //Cantidad Base
        $cantidad_base = 1000; //Fijo segun cliente

        //Tiempo tratamiento
        $tiempo_tratamiento = 0;
        if (is_null($ot->process_id)) {
            $tiempo_tratamiento = 0;
        } else {
            $data_tiempo_tratamiento = TiempoTratamiento::where('proceso_id', $ot->process_id)->where('active', 1)->first();
            if ($data_tiempo_tratamiento) {
                if (is_null($ot->planta_id)) {
                    $tiempo_tratamiento = 0;
                } else {
                    if ($ot->planta_id == 1) { //Buin
                        $tiempo_tratamiento = $data_tiempo_tratamiento->tiempo_buin;
                    } else if ($ot->planta_id == 2) { //Tiltil
                        $tiempo_tratamiento = $data_tiempo_tratamiento->tiempo_tiltil;
                    } else if ($ot->planta_id == 3) { //Osorno
                        $tiempo_tratamiento = $data_tiempo_tratamiento->tiempo_osorno;
                    }
                }
            } else {
                $tiempo_tratamiento = 0;
            }
        }

        //Consumo_cinta
        if (is_null($ot->cintas_x_caja) || $ot->cintas_x_caja == 0 || $ot->cintas_x_caja == '') {
            $consumo_cinta = '';
        } else {
            if ($ot->largura_hm == 0 || $ot->largura_hm == '') {
                $consumo_cinta = '';
            } else {
                $consumo_cinta = $ot->largura_hm * $ot->cintas_x_caja;
            }
        }

        //Total golpes
        if (is_null($ot->golpes_ancho) || $ot->golpes_ancho == 0 || $ot->golpes_ancho == '') {
            $total_golpes = 0;
        } else {
            if (is_null($ot->golpes_largo) || $ot->golpes_largo == 0 || $ot->golpes_largo == '') {
                $total_golpes = 0;
            } else {
                $total_golpes = $ot->golpes_ancho * $ot->golpes_largo;
            }
        }

        //Grupo Materiales 3
        if (is_null($ot->style_id)) {
            $grupo_materiales_3 = '';
        } else {
            $codigo_grupo_materiales_3 = Style::where('id', $ot->style_id)->where('active', 1)->first();
            if ($codigo_grupo_materiales_3) {
                $grupo_materiales_3 = $codigo_grupo_materiales_3->grupo_materiales;
                $grupo_materiales_3 = str_pad($grupo_materiales_3, 29, " ");
            } else {
                $grupo_materiales_3 = '';
            }


        }

        //Adhesivo
        if (is_null($ot->planta_id)) {
            $adhesivo = '';
            $consumo_adhesivo = '';
        } else {
            if (is_null($ot->process_id) || $ot->process_id == '') {
                $adhesivo = '';
                $consumo_adhesivo = '';
            } else {
                if (in_array($ot->process_id, [2, 3, 11, 12, 13, 14, 15])) {
                    $adhesivo = '';
                    $consumo_adhesivo = '';
                } else {
                    if (in_array($ot->process_id, [1, 5, 10])) { //Procesos Flexo
                        if (is_null($maquina_proceso_2) || $maquina_proceso_2 == '') {
                            $adhesivo = '';
                            $consumo_adhesivo = '';
                        } else {
                            $codigo_adhesivo = Adhesivo::where('planta_id', $ot->planta_id)->where('maquina', $maquina_proceso_2)->where('active', 1)->first();
                            if ($codigo_adhesivo) {
                                $adhesivo = $codigo_adhesivo->codigo;
                                $consumo_adhesivo = $codigo_adhesivo->consumo;
                            } else {
                                $adhesivo = '';
                                $consumo_adhesivo = '';
                            }
                        }
                    } else {
                        if ($ot->process_id == 4) { // Proceso Diecutter con Pegado
                            if (is_null($maquina_proceso_3) || $maquina_proceso_3 == '') {
                                $adhesivo = '';
                                $consumo_adhesivo = '';
                            } else {
                                $codigo_adhesivo = Adhesivo::where('planta_id', $ot->planta_id)->where('maquina', $maquina_proceso_3)->where('active', 1)->first();
                                if ($codigo_adhesivo) {
                                    $adhesivo = $codigo_adhesivo->codigo;
                                    $consumo_adhesivo = $codigo_adhesivo->consumo;
                                } else {
                                    $adhesivo = '';
                                    $consumo_adhesivo = '';
                                }
                            }
                        } else {
                            $adhesivo = '';
                            $consumo_adhesivo = '';
                        }
                    }
                }
            }
        }

        //Gramos Adhesivo
        $longitud_pegado    = (is_null($ot->longitud_pegado) || $ot->longitud_pegado == '') ? 0 : $ot->longitud_pegado;
        $golpes_largo       = (is_null($ot->golpes_largo) || $ot->golpes_largo == '') ? 0 : $ot->golpes_largo;
        $golpes_ancho       = (is_null($ot->golpes_ancho) || $ot->golpes_ancho == '') ? 0 : $ot->golpes_ancho;
        $consumo_adhesivo_aux = (is_null($consumo_adhesivo) || $consumo_adhesivo == '') ? 0 : $consumo_adhesivo;

        $gramos_Adhesivo = '';
        if($consumo_cinta != ''){
            $gramos_Adhesivo = (($longitud_pegado / 1000) * ($consumo_adhesivo_aux) * ($golpes_largo * $golpes_ancho));
        }

        $caracteristicas_adicionales = '';

        if($ot->caracteristicas_adicionales){

            if($ot->caracteristicas_adicionales == 'N/A'){
                $caracteristicas_adicionales = 'No Aplica';
            }else{
                $caracteristicas_adicionales = $ot->caracteristicas_adicionales;
            }
        }

        $array_data = array(
            "STDPD"         => ['Mat. Configurable', $familia],
            "MATNR"         => ['Número de Material', $numero_material],
            "EN_PLANCHA_SEMI" => ['Número de Semielaborado', $semielaborado],
            "RMMG1_REF-MATNR" => ['Material Modelo', $modelo_material],
            "MAKTX"         => ['Descripción Comercial', $ot->material ? $ot->material->descripcion : null],
            "WERKS"         => ['centro', $centro],
            // "LGNUM"         => ['Almacen', $almacen],
            "LGORT"         => ['Almacen', '001'],
            "VKORG"         => ['Organiz. Ventas', $org_ventas],
            "VTWEG"         => ['Canal distrib.', $canal],
            "LGNUM"         => ['Numero Almacen', $num_almacen],
            "MARA-BISMT"    => ['N° Material antiguo', $ot->id],
            "SPART"         => ['Sector', $sector],
            "PRDHA"         => ['Jerarquia', $ot->subsubhierarchy ? $ot->subsubhierarchy->jerarquia_sap : null],
            "BRGEW"         => ['Peso bruto ( G1)', number_format_unlimited_precision(str_replace(',', '.', $ot->pesoBruto))],
            "NTGEW"         => ['Peso neto(G1)', number_format_unlimited_precision(str_replace(',', '.', $ot->pesoNeto))],
            "VOLUM"         => ['Volumen(G1)', is_numeric($ot->volumenUnitario) ? intval(round($ot->volumenUnitario)) : null], //number_format_unlimited_precision_sap($ot->volumenUnitario)],
            "NORMT"         => ['Denom.estándar', $ot->codigo_producto_cliente ? $ot->codigo_producto_cliente : null], //['Denom.estándar',$ot->material ? $ot->material->descripcion : null],
            "UMREN"         => ['UMA Área (m2)', (intval($ot->umaArea))],
            "UMREN01"         => ['UMA Peso (kg):', (intval($ot->umaPeso))],
            "KONDM"         => ['Gr.imputación mat.', $grupo_imp_material],
            "PRODH"         => ['Jerarquía productos', $ot->subsubhierarchy ? $ot->subsubhierarchy->jerarquia_sap : null],
            "MVGR1"         => ['Grupo materiales 1', $grupo_materiales_1],
            "MVGR2"         => ['Grupo de material 2', $grupo_materiales_2],
            "MVKE-MVGR3"    => ['Grupo de material 3', $grupo_materiales_3],
            "MARC-PRCTR"    => ['CeBe', $cebe],
            "MARC-AUSSS"    => ['Rechazo conjunto (%)', $rechazo_conjunto],
            "MARC-WEBAZ"    => ['Tmpo.tratamiento EM', $tiempo_tratamiento],
            "MARC-BASMG"    => ['Cantidad base', $cantidad_base],
            "MBEW-KOSGR"    => ['Grupo gastos gral.', $grupo_gastos_generales],
            "EN_OT"         => ['Numero OT', $ot->id],
            "EN_CODCLI"     => ['cliente', $ot->client->codigo],
            "EN_CODVEN"     => ['vendedor', isset($ot->creador->nombre_sap) ? $ot->creador->nombre_sap : $ot->creador->fullname],
            "EN_LARGO"      => ['Largo Interior (MM)', $ot->interno_largo],
            "EN_ANCHO"      => ['Ancho Interior (MM)', $ot->interno_ancho],
            "EN_ALTO"       => ['Alto Interior (MM)', $ot->interno_alto],
            "EN_LARGURA"    => ['Largura HM (MM)', $ot->largura_hm],
            "EN_ANCHURA"    => ['Anchura HM (MM)', $ot->anchura_hm],
            "EN_LARGOEXT"   => ['Largo Exterior (MM)', $ot->externo_largo],
            "EN_ANCHOEXT"   => ['Ancho Exterior (MM)', $ot->externo_ancho],
            "EN_ALTOEXT"    => ['Alto Exterior (MM)', $ot->externo_alto],
            "EN_CARTON"     => ['Cartón', $ot->carton ? $ot->carton->codigo : null],
            "EN_TIPO"       => ['Tipo de Producto (Tipo Item)', isset($ot->productType) ? $ot->productType->descripcion : null],
            "EN_ESTILO"     => ['Estilo de Producto', isset($ot->style) ? $ot->style->glosa : null],
            "EN_CARCTRIST_ESTILO" => ['Caracteristicas Adicionales', $ot->caracteristicas_adicionales ? $caracteristicas_adicionales : 'No Aplica'],
            "EN_C1_R1"      => ['Rayado C1/R1 (MM)', isset($ot->rayado_c1r1) ? $ot->rayado_c1r1 : null],
            "EN_R1_R2"      => ['Rayado R1/R2 (MM)', isset($ot->rayado_r1_r2) ? $ot->rayado_r1_r2 : null],
            "EN_R2_C2"      => ['Rayado R2/C2 (MM)', isset($ot->rayado_r2_c2) ? $ot->rayado_r2_c2 : null],
            "EN_RAYADO"     => ['Tipo de Rayado', isset($ot->rayado_type_id) ? $ot->rayado->descripcion : null],
            "EN_TIPOIMPRESION" => ['Tipo Impresión', $ot->impresion ? $ot->impresion_detalle->descripcion : null],
            "EN_COLORES"    => ['Número de Colores', $ot->numero_colores],
            "EN_PRUEBACOLOR" => ['Prueba de Color', isset($ot->prueba_color) ? [1 => "Si", 0 => "No"][$ot->prueba_color] : null],
            "EN_CARACTERISTICO" => ['Recorte Característico (M2)', number_format_unlimited_precision(str_replace(',', '.', $detalle_recorte_caracteristico))],
            "EN_ADICIONAL"  => ['Recorte Adicional (M2)', number_format_unlimited_precision(str_replace(',', '.', $detalle_recorte_adicional))],
            "EN_CAD"        => ['Plano CAD', isset($ot->cad) ? $ot->cad : null],
            "EN_AREA_INTERIOR_PERIMETRO" => ['Area Producto (M2)', $detalle_area_producto],
            // "EN_AREA_INTERIOR_PERIMETRO" => ['Area Producto (M2)', $ot->larguraHc*$ot->anchuraHc],
            "EN_ESTADO_PALETIZAD0" => ['Estado de Palletizado', isset($ot->pallet_status_type_id) ? $ot->pallet_status->descripcion : null],
            "EN_TIPO_PALET_GE" => ['Tipo de Pallet', $ot->tipo_pallet ? $ot->tipo_pallet->codigo : null],
            "EN_TRATAMIENTO_PALET" => ['Tratamiento de Pallet', isset($ot->pallet_treatment) ? [1 => "SI", 0 => "NO"][$ot->pallet_treatment] : null],
            "EN_CAJAS_POR_PALET" => ['Nro Cajas por Pallet', $ot->cajas_por_pallet ? $ot->cajas_por_pallet : null],
            "EN_PLACAS_POR_PALET" => ['Nro Placas por Pallet', $ot->placas_por_pallet ? $ot->placas_por_pallet : null],
            "EN_PATRON_CARGA_PALET" => ['Patron Carga Pallet', $ot->patron_pallet ? $ot->patron_pallet->descripcion : null],
            "EN_PATRON_ZUNCHO_BULTO" => ['Patron Zuncho Bulto', $ot->patron_zuncho_bulto ? [1 => "2x0", 2 => "2x1", 3 => "2x2"][$ot->patron_zuncho_bulto] : null],
            "EN_PROTECCION" => ['Proteccion', isset($ot->protection_type_id) ? $ot->protection->descripcion : null],
            "EN_PATRON_ZUNCHO_PALET_GE" => ['Patron Zuncho Pallet', $ot->patron_zuncho_bulto ? $ot->patron_zuncho_bulto : null],
            "EN_PROTECCION_PALET" => ['Protección Pallet', $ot->proteccion_pallet ? $ot->proteccion_pallet->descripcion : null],
            "EN_CAJAS_POR_PAQUETE" => ['Nro Cajas por Paquete', $ot->cajas_por_paquete ? $ot->cajas_por_paquete->descripcion : null],
            "EN_PATRON_ZUNCHO_PAQUETE_GE" => ['Patron Zuncho Paquete', $ot->patron_zuncho_paquete ? $ot->patron_zuncho_paquete : null],
            "EN_TERMOCONTRAIBLE" => ['Termocontraible', isset($ot->termocontraible) ? [1 => "SI", 0 => "NO"][$ot->termocontraible] : null],
            "EN_TERMOCONTRAIBLE_CANT" => ['Cantidad Consumo Termocontra', isset($ot->termocontraible) ? [1 => "0,60", 0 => ""][$ot->termocontraible] : null],
            // "EN_PAQUETES_POR_UNITIZADO" => ['Nro Cajas por Unitizados', isset($ot->termocontraible) ? [1 => "0.6", 0 => ""][$ot->termocontraible] : null],
            "EN_PAQUETES_POR_UNITIZADO" => ['Nro Cajas por Unitizados', $ot->paquetes_por_unitizado ? $ot->paquetes_por_unitizado : null],
            "EN_UNITIZADOS_POR_PALET" => ['Nro Unitizados por Pallet', $ot->unitizado_por_pallet ? $ot->unitizado_por_pallet : null],
            "EN_FORMATO_ETIQUETA_PALET" => ['Tipo Formato Etiqueta Pallet', $ot->formato_etiqueta_pallet ? $ot->formato_etiqueta_pallet->descripcion : null],
            "EN_ETIQUETAS_POR_PALET" => ['Nro Etiqueta Pallet', $ot->numero_etiquetas ? [0, 1, 2, 3, 4][$ot->numero_etiquetas] : null],
            "EN_CERTIFICADO" => ['Certificado Calidad', $ot->qa ? $ot->qa->descripcion : null],
            "EN_RESISTENCIA_MT" => ['BCT MIN (LB)', ($ot->bct_min_lb)],
            "EN_JERARQUIA" => ['Jerarquia', $ot->subsubhierarchy ? $ot->subsubhierarchy->jerarquia_sap : null],
            "EN_COD_PT_CLI" => ['Código Producto Cliente', $ot->codigo_producto_cliente ? $ot->codigo_producto_cliente : null], //['Código Producto Cliente',$ot->codigo_producto_cliente ? $ot->codigo_producto_cliente : null],
            "EN_C1_CINTA1" => ['Distancia corte 1 a cinta 1', isset($ot->distancia_cinta_1) ? $ot->distancia_cinta_1 : null],
            "EN_C1_CINTA2" => ['Distancia corte 1 a cinta 2', isset($ot->distancia_cinta_2) ? $ot->distancia_cinta_2 : null],
            "EN_C1_CINTA3" => ['Distancia corte 1 a cinta 3', isset($ot->distancia_cinta_3) ? $ot->distancia_cinta_3 : null],
            "EN_C1_CINTA4" => ['Distancia corte 1 a cinta 4', isset($ot->distancia_cinta_4) ? $ot->distancia_cinta_4 : null],
            "EN_C1_CINTA5" => ['Distancia corte 1 a cinta 5', isset($ot->distancia_cinta_5) ? $ot->distancia_cinta_5 : null],
            "EN_C1_CINTA6" => ['Distancia corte 1 a cinta 6', isset($ot->distancia_cinta_6) ? $ot->distancia_cinta_6 : null],
            "EN_TIPO_CINTA" => ['TIPO DE CINTA', isset($ot->tipo_cinta) ? $ot->tipos_cintas->descripcion : null],
            "EN_CORTE_LINER" => ['Cantidad Cinta por CAJA', $ot->cintas_x_caja],
            "EN_CINTA" => ['Codigo Cinta', isset($ot->tipo_cinta) ? $ot->tipos_cintas->codigo : null],
            "EN_CONSUMO_CINTA" => ['Consumo cinta', $consumo_cinta],
            "EN_SELLO" => ['Etiqueta FSC/PRODUCTO FSC', isset($ot->fsc) ? [2 => "NO", 5 => "No", 3 => "FACTURACION Y LOGO", 4 => "FACTURACION Y LOGO", 6 => "SOLO FACTURACION"][$ot->fsc] : null],
            "EN_ORIENTACION" => ['Orientación Placa', isset($ot->orientacion_placa) ? [0, 90][$ot->orientacion_placa] : null],
            "EN_CARACT_ADICION" => ['Características Adicionales', isset($ot->prepicado) ? $ot->prepicado->descripcion  : null],
            "EN_FACTURACION" => ['Indicador Facturación Diseño Estructural', $ot->indicador_facturacion ? [1 => 'RRP', 2 => 'E-Commerce', 3 => 'Esquineros', 4 => 'Geometría', 5 => 'Participación nuevo Mercado', 6 => '', 7 => 'Innovación', 8 => 'Sustentabilidad', 9 => 'Automatización', 10 => 'No Aplica', 11 => 'Ahorro', 12 => ''][$ot->indicador_facturacion] : null],
            "EN_FACTURACION_DG" => ['Indicador Facturación Diseño Gráfico', $ot->indicador_facturacion_diseno_grafico],
            "EN_TIPO_CIERRE" => ['Tipo de Pegado', isset($ot->pegado_terminacion) ? [0 => "No Aplica", 2 => "Pegado Interno", 3 => "Pegado Externo", 4 => "Pegado 3 Puntos", 5 => "Pegado 4 Puntos"][$ot->pegado_terminacion] : null],
            "EN_ARMADO" => ['Armado', $ot->armado ? $ot->armado->descripcion  : null],
            "EN_SENTIDO_DE_ARMADO" => ['Sentido Armado', $ot->sentido_armado ? [1 => "No aplica", 2 => "Ancho a la Derecha", 3 => "Ancho a la Izquierda", 4 => "Largo a la Izquierda",  5 => "Largo a la Derecha"][$ot->sentido_armado] : null],
            "EN_GRAMAJE_G_M2" => ['Gramaje (G/m2)', isset($ot->gramaje) ? $ot->gramaje : null],
            "EN_PESO_G" => ['Peso (G)', isset($ot->peso) ? $ot->peso : null],
            "EN_ESPESOR_MM" => ['Espesor Caja (mm)', isset($ot->espesor_caja) ? $ot->espesor_caja : null],
            "EN_ECT_MINIMO" => ['ECT Minimo (LB/PULG2)', isset($ot->ect) ? number_format_unlimited_precision(str_replace(',', '.', $ot->ect)) : null],
            "EN_FCT_MINIMO" => ['FCT Minimo (LB/PULG22)', isset($ot->fct) ? number_format_unlimited_precision(str_replace(',', '.', $ot->fct)) : null],
            "EN_COBB_INT_MAX" => ['Cobb INT. ( 2 Min.) Max.', isset($ot->cobb_interior) ? $ot->cobb_interior : null],
            "EN_COBB_EXT_MAX" => ['Cobb EXT. ( 2 Min.) Max.', isset($ot->cobb_exterior) ? $ot->cobb_exterior : null],
            "EN_FLEXION_ALETA" => ['Flexion de Aleta (N)', isset($ot->flexion_aleta) ? $ot->flexion_aleta : null],
            "EN_MULLEN" => ['Mullen (LB/PULG2)', isset($ot->mullen) ? number_format_unlimited_precision(str_replace(',', '.', $ot->mullen)) : null],
            "EN_RESISTENCIA_STD" => ['Resistencia mínima (Humeda)', $ot->bct_humedo_lb],
            "EN_INCISION_RAYADO_LONG" => ['Incisión Rayado Long.[N]', isset($ot->incision_rayado_longitudinal) ? $ot->incision_rayado_longitudinal : null],
            "EN_INCISION_RAYADO_TRANS" => ['Incisión Rayado Transv.[N]', isset($ot->incision_rayado_vertical) ? $ot->incision_rayado_vertical : null],
            "EN_DST_BPI" => ['DST (BPI)', isset($ot->dst) ? $ot->dst : null],
            "EN_ESPESOR_PL" => ['Espesor Placa (mm)', isset($ot->espesor_placa) ? $ot->espesor_placa : null],
            "EN_POROSIDAD" => ['Porosidad (SEG)', isset($ot->porosidad) ? $ot->porosidad : null],
            "EN_BRILLO" => ['Brillo (%)', isset($ot->brillo) ? $ot->brillo : null],
            "EN_RIGID_4_PUN_LONGITUDAL" => ['Rigidez 4 Puntos Longitudinal (N/MM)', isset($ot->rigidez_4_ptos_long) ? $ot->rigidez_4_ptos_long : null],
            "EN_RIGID_4_PUN_TRANSVERSAL" => ['Rigidez 4 Puntos Transversal (N/MM)', isset($ot->rigidez_4_ptos_transv) ? $ot->rigidez_4_ptos_transv : null],
            "EN_ANGUL_DESLIZ_TP_EXT" => ['Angulo de Deslizamiento-Tapa Exterior (°)', isset($ot->angulo_deslizamiento_tapa_exterior) ? $ot->angulo_deslizamiento_tapa_exterior : null],
            "EN_ANGUL_DESLIZ_TP_INT" => ['Angulo de Deslizamiento-Tapa Interior (°)', isset($ot->angulo_deslizamiento_tapa_interior) ? $ot->angulo_deslizamiento_tapa_interior : null],
            "EN_FROTE" => ['Resistencia al Frote', isset($ot->resistencia_frote) ? $ot->resistencia_frote : null],
            "EN_IMPRESION_BORDE" => ['Impresión de Borde', $ot->impresion_borde],
            "EN_IMPRESION_SOBRE_RAYADO" => ['Impresión Sobre Rayado', $ot->impresion_sobre_rayado],
            "EN_CONTEN_RECICLADO" => ['Contenido Reciclado (%)', isset($ot->contenido_reciclado) ? $ot->contenido_reciclado : null],
            "EN_CANT_LARGO" => ['Golpes al Largo', $ot->golpes_largo],
            "EN_CANT_ANCHO" => ['Golpes al Ancho', $ot->golpes_ancho],
            "EN_TOTAL_GOL_MATRIZ" => ['Golpes Total', $total_golpes],
            "EN_LARGURA_HC" => ['Largura HC (MM)', $ot->larguraHc],
            "EN_ANCHURA_HC" => ['Anchura HC (MM)', $ot->anchuraHc],
            "EN_COLOR_COMP_1" => ['Código Color 1 (INTERIOR TyR)', $ot->color_1 ? $ot->color_1->codigo : null],
            "EN_CONSUMO_1" => ['Gramos Color 1 (INTERIOR TyR)', number_format_unlimited_precision_sap(str_replace(',', '.', $ot->consumo1))],
            "EN_COLOR_COMP_2" => ['Código Color 2', $ot->color_2 ? $ot->color_2->codigo : null],
            "EN_CONSUMO_2" => ['Gramos Color 2', number_format_unlimited_precision_sap(str_replace(',', '.', $ot->consumo2))],
            "EN_COLOR_COMP_3" => ['Código Color 3', $ot->color_3 ? $ot->color_3->codigo : null],
            "EN_CONSUMO_3" => ['Gramos Color 3', number_format_unlimited_precision_sap(str_replace(',', '.', $ot->consumo3))],
            "EN_COLOR_COMP_4" => ['Código Color 4', $ot->color_4 ? $ot->color_4->codigo : null],
            "EN_CONSUMO_4" => ['Gramos Color 4', number_format_unlimited_precision_sap(str_replace(',', '.', $ot->consumo4))],
            "EN_COLOR_COMP_5" => ['Código Color 5', $ot->color_5 ? $ot->color_5->codigo : null],
            "EN_CONSUMO_5" => ['Gramos Color 5', number_format_unlimited_precision_sap(str_replace(',', '.', $ot->consumo5))],
            "EN_COLOR_COMP_6" => ['Código Color 6', $ot->color_6 ? $ot->color_6->codigo : null],
            "EN_CONSUMO_6" => ['Gramos Color 6', number_format_unlimited_precision_sap(str_replace(',', '.', $ot->consumo6))],
            "EN_COLOR_COMP_7" => ['Código Color 7', $ot->color_7 ? $ot->color_7->codigo : null],
            "EN_CONSUMO_7" => ['Gramos Color 7', number_format_unlimited_precision_sap(str_replace(',', '.', $ot->consumo7))],
            "EN_ADHESIVO" => ['Codigo Adhesivo acorde a maquina', $adhesivo],
            "EN_CONSUMO_ADH" => ['Consumo Adhesivo PVA', number_format_unlimited_precision(str_replace(',', '.', $ot->gramosAdhesivo))],
            "EN_MAQUINA_PROCESO_1" => ['Maquina Proceso 1', $maquina_proceso_1],
            "EN_MAQUINA_PROCESO_1_1" => ['Maquina Proceso 1.1 corrugado', $maquina_proceso_1_1],
            "EN_DESCRIPCION_MAQ_PROCESO_1" => ['Descripción Maquina Proceso 1', $maquina_proceso_1_descripcion_corto],
            "EN_DESCRIPCION_MAQ_PROCESO_1_1" => ['Descripción Maquina Proceso 1.1', $maquina_proceso_1_1_descripcion],
            "EN_MAQUINA_PROCESO_2" => ['Maquina Proceso 2', $maquina_proceso_2],
            "EN_MAQUINA_PROCESO_2_1" => ['Maquina Proceso 2.1 Flexo', $maquina_proceso_2_1],
            "EN_MAQUINA_PROCESO_2_2" => ['Maquina Proceso 2.2 Flexo', $maquina_proceso_2_2],
            "EN_MAQUINA_PROCESO_2_3" => ['Maquina Proceso 2.3 Flexo', $maquina_proceso_2_3],
            "EN_MAQUINA_PROCESO_2_4" => ['Maquina Proceso 2.4 Flexo', $maquina_proceso_2_4],
            "EN_MAQUINA_PROCESO_2_5" => ['Maquina Proceso 2.5 Flexo', $maquina_proceso_2_5],
            "EN_DESCRIPCION_MAQ_PROCESO_2" => ['Descripción Maquina Proceso 2', $maquina_proceso_2_descripcion_corto],
            "EN_DESCRIPCION_MAQ_PROCESO_2_1" => ['Descripción Maquina Proceso 2.1', $maquina_proceso_2_1_descripcion],
            "EN_DESCRIPCION_MAQ_PROCESO_2_2" => ['Descripción Maquina Proceso 2.2', $maquina_proceso_2_2_descripcion],
            "EN_DESCRIPCION_MAQ_PROCESO_2_3" => ['Descripción Maquina Proceso 2.3', $maquina_proceso_2_3_descripcion],
            "EN_DESCRIPCION_MAQ_PROCESO_2_4" => ['Descripción Maquina Proceso 2.4', $maquina_proceso_2_4_descripcion],
            "EN_DESCRIPCION_MAQ_PROCESO_2_5" => ['Descripción Maquina Proceso 2.5', $maquina_proceso_2_5_descripcion],
            "EN_MAQUINA_PROCESO_3" => ['Maquina Proceso 3', $maquina_proceso_3],
            "EN_MAQUINA_PROCESO_3_1" => ['Maquina Proceso 3.1 ', $maquina_proceso_3_1],
            "EN_MAQUINA_PROCESO_3_2" => ['Maquina Proceso 3.2 ', $maquina_proceso_3_2],
            "EN_MAQUINA_PROCESO_3_3" => ['Maquina Proceso 3.3 ', $maquina_proceso_3_3],
            "EN_DESCRIPCION_MAQ_PROCESO_3" => ['Descripción Maquina Proceso 3', $maquina_proceso_3_descripcion],
            "EN_DESCRIPCION_MAQ_PROCESO_3_1" => ['Descripción Maquina Proceso 3.1', $maquina_proceso_3_1_descripcion],
            "EN_DESCRIPCION_MAQ_PROCESO_3_2" => ['Descripción Maquina Proceso 3.2', $maquina_proceso_3_2_descripcion],
            "EN_DESCRIPCION_MAQ_PROCESO_3_3" => ['Descripción Maquina Proceso 3.3', $maquina_proceso_3_3_descripcion],
            "EN_MAQUINA_PROCESO_4" => ['Maquina Proceso 4', $maquina_proceso_4],
            "EN_MAQUINA_PROCESO_4" => ['Maquina Proceso 4', $maquina_proceso_4],
            "EN_MAQUINA_PROCESO_4_1" => ['Maquina Proceso 4.1 ', $maquina_proceso_4_1],
            "EN_MAQUINA_PROCESO_4_2" => ['Maquina Proceso 4.2 ', $maquina_proceso_4_2],
            "EN_DESCRIPCION_MAQ_PROCESO_4" => ['Descripción Maquina Proceso 4', $maquina_proceso_4_descripcion],
            "EN_DESCRIPCION_MAQ_PROCESO_4_1" => ['Descripción Maquina Proceso 4.1', $maquina_proceso_4_1_descripcion],
            "EN_DESCRIPCION_MAQ_PROCESO_4_2" => ['Descripción Maquina Proceso 4.2', $maquina_proceso_4_2_descripcion],
            "EN_MAQUINA_PROCESO_5" => ['Maquina Proceso 5', $maquina_proceso_5],
            "EN_MAQUINA_PROCESO_5_1" => ['Maquina Proceso 5.1', $maquina_proceso_5_1],
            "EN_DESCRIPCION_MAQ_PROCESO_5" => ['Descripción Maquina Proceso 5', $maquina_proceso_5_descripcion],
            "EN_DESCRIPCION_MAQ_PROCESO_5_1" => ['Descripción Maquina Proceso 5.1', $maquina_proceso_5_1_descripcion],
            "EN_MAQUINA_PROCESO_6" => ['Maquina Proceso 6', $maquina_proceso_6],
            "EN_CLISSE_1" => ['Clisse 1', (is_null($ot->material_id)) ? null : 'ENC' . $ot->material->codigo],
            "EN_CLISSE_2" => ['Clisse 2', ''],
            "EN_MATRIZ_1" => ['Matriz 1', is_null($ot->matriz_id) ? null : $ot->matrices->material],
            "EN_MATRIZ_2" => ['Matriz 2', is_null($ot->matriz_id_2) ? null : $ot->matrices_2->material],
            "EN_MATRIZ_3" => ['Matriz 3', is_null($ot->matriz_id_3) ? null : $ot->matrices_3->material],
            "MAKT-MAKTX" => ['URL FT', "\\\\pro-eeii05\\ftecnica\\" . $numero_material . "_FT.pdf"],

        );


        return $item_array = $array_data;
    }

    public function descargarExcelSapSemielaborado($id)
    {
        $ot = WorkOrder::with(
            'armado',
            'canal',
            'client',
            'creador',
            'productType',
            "users",
            "material",
            "material_referencia",
            "subsubhierarchy",
            "tipo_pallet",
            "cajas_por_paquete",
            "patron_pallet",
            "proteccion_pallet",
            "formato_etiqueta_pallet",
            "qa",
            "prepicado",
            "carton",
            "style",
            "rayado",
            "pallet_status",
            "protection",
            "characteristics",
        )->where('id', $id)->first();

        $item_array = self::array_data_excel_sap_semielaborado($ot);
        $titulo = 'Excel SAPSemielaborado OT'. $id.' ';

        Excel::create($titulo . Carbon::now(), function ($excel) use ($item_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet($titulo, function ($sheet) use ($item_array) {
                // Se aplica foreach porque la informacion tiene ser mostrada de forma vertical
                // solo utilizando las celdas A y B
                $i = 1;
                foreach ($item_array as $key => $fila) {
                    $sheet->setCellValue('A' . $i, $key);
                    $sheet->setCellValue('B' . $i, $fila[0]);
                    $sheet->setCellValue('C' . $i, $fila[1]);
                    $i++;
                }
            });
        })->download('xlsx');
    }

    public function array_data_excel_sap_semielaborado($ot)
    {
        //Validamos los campos para que siempre imprima 4 decimales, ya sea agregar con cero o recortar los datos de la BD
        $recorte_caracteristico = number_format_unlimited_precision($ot->recorteCaracteristico, ",", ".", 7);
        if (isset($recorte_caracteristico) && $recorte_caracteristico != 'N/A') {
            if ($recorte_caracteristico === '0') {
                $detalle_recorte_caracteristico = $recorte_caracteristico;
            } else {
                $recorte = number_format_unlimited_precision($ot->recorteCaracteristico, ",", ".", 7);
                $decimal = explode(',', $recorte);
                $truncate_decimal = substr($decimal[1], 0, 4);
                $pad = str_pad($truncate_decimal, 4, "0");
                $detalle_recorte_caracteristico = $decimal[0] . ',' . $pad;
            }
        } else {
            $detalle_recorte_caracteristico = 'N/A';
        }

        //recorte adicional
        if ($ot->recorte_adicional > 0) {
            $recorte_adicional = number_format_unlimited_precision($ot->recorte_adicional);
            $decimal_adicional = explode(',', $recorte_adicional);
            $truncate_decimal_adicional = substr($decimal_adicional[1], 0, 4);
            $pad_adicional = str_pad($truncate_decimal_adicional, 4, "0");
            $detalle_recorte_adicional = $decimal_adicional[0] . ',' . $pad_adicional;
        } else {
            $detalle_recorte_adicional = '0,0000';
        }

        if ($ot->area_producto_calculo > 0) {
            $recorte_producto = number_format_unlimited_precision($ot->area_producto_calculo);
            $decimal_producto = explode(',', $recorte_producto);
            // $truncate_decimal_producto = substr($decimal_producto[1], 0, 4);
            // $pad_producto = str_pad($truncate_decimal_producto, 4, "0");
            $concaterna_producto = $decimal_producto[0] . '.' . $decimal_producto[1];
            $detalle_area_producto = $concaterna_producto * 1000000;
        } else {
            $detalle_area_producto = 0;
        }

        // Número de Material
        $numero_material = '';
        if (!is_null($ot->material_asignado) && !is_null($ot->material_id)) {
            $numero_material = 'GE1' . $ot->material_code;
        } else {
            $numero_material = '';
        }

        // Número de Semielaborado
        $semielaborado = '';
        if (!is_null($ot->material_asignado) && !is_null($ot->material_id)) {
            if ($ot->product_type_id == 16 || $ot->product_type_id == 21) {
                $semielaborado = '';
            } else {
                $semielaborado = 'GE2' . $ot->material_code;
            }
        } else {
            $semielaborado = '';
        }

        //Numero de Pieza Interior
        $numero_material_pieza = '';
        if (!is_null($ot->material_asignado) && !is_null($ot->material_id)) {
            if ($ot->product_type_id == 16 || $ot->product_type_id == 21) {
                $numero_material_pieza = '';
            } else {
                if ($ot->proceso_id != 15) {
                    $numero_material_pieza = '';
                } else {
                    $numero_material_pieza = 'GE3' . $ot->material_code;
                }
            }
        } else {
            $numero_material_pieza = '';
        }

        //Secuencia Operacional - Maquinas
        $maquina_proceso_1 = null;
        $maquina_proceso_2 = null;
        $maquina_proceso_3 = null;
        $maquina_proceso_4 = null;
        $maquina_proceso_5 = null;
        $maquina_proceso_6 = null;


        $maquina_proceso_1_descripcion = '';
        $maquina_proceso_2_descripcion = '';
        $maquina_proceso_3_descripcion = '';
        $maquina_proceso_4_descripcion = '';
        $maquina_proceso_5_descripcion = '';
        $maquina_proceso_6_descripcion = '';

        $maquina_proceso_1_1 = null;
        $maquina_proceso_2_1 = null;
        $maquina_proceso_3_1 = null;
        $maquina_proceso_4_1 = null;
        $maquina_proceso_5_1 = null;
        $maquina_proceso_6_1 = null;

        $maquina_proceso_1_1_descripcion = '';
        $maquina_proceso_2_1_descripcion = '';
        $maquina_proceso_3_1_descripcion = '';
        $maquina_proceso_4_1_descripcion = '';
        $maquina_proceso_5_1_descripcion = '';
        $maquina_proceso_6_1_descripcion = '';

        //Planta Buin
        if ($ot->so_planta_original == 1) { //Planta Original
            if (!is_null($ot->so_planta_original_select_values)) {
                $secuencia_productiva_planta_original = json_decode($ot->so_planta_original_select_values, true);
                for ($i = 1; $i <= count($secuencia_productiva_planta_original); $i++) {
                    if (isset($secuencia_productiva_planta_original['fila_' . $i])) {
                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['org'])) {
                            $secuencia_original = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->codigo;
                            $secuencia_original_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1 = $secuencia_original;
                                    $maquina_proceso_1_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2 = $secuencia_original;
                                    $maquina_proceso_2_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3 = $secuencia_original;
                                    $maquina_proceso_3_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4 = $secuencia_original;
                                    $maquina_proceso_4_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5 = $secuencia_original;
                                    $maquina_proceso_5_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6 = $secuencia_original;
                                    $maquina_proceso_6_descripcion = $secuencia_original_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt1'])) {
                            $secuencia_alt1 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt1'])->codigo;
                            $secuencia_alt1_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt1'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_1 = $secuencia_alt1;
                                    $maquina_proceso_1_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_1 = $secuencia_alt1;
                                    $maquina_proceso_2_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_1 = $secuencia_alt1;
                                    $maquina_proceso_3_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_1 = $secuencia_alt1;
                                    $maquina_proceso_4_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_1 = $secuencia_alt1;
                                    $maquina_proceso_5_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_1 = $secuencia_alt1;
                                    $maquina_proceso_6_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }
                    }
                }
            }
        }

        //Planta Origonal - Osorno
        if ($ot->so_planta_original == 3) {
            if (!is_null($ot->so_planta_original_select_values)) {
                $secuencia_productiva_planta_original = json_decode($ot->so_planta_original_select_values, true);
                for ($i = 1; $i <= count($secuencia_productiva_planta_original); $i++) {
                    if (isset($secuencia_productiva_planta_original['fila_' . $i])) {
                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['org'])) {
                            $secuencia_original = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->codigo;
                            $secuencia_original_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1 = $secuencia_original;
                                    $maquina_proceso_1_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2 = $secuencia_original;
                                    $maquina_proceso_2_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3 = $secuencia_original;
                                    $maquina_proceso_3_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4 = $secuencia_original;
                                    $maquina_proceso_4_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5 = $secuencia_original;
                                    $maquina_proceso_5_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6 = $secuencia_original;
                                    $maquina_proceso_6_descripcion = $secuencia_original_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt1'])) {
                            $secuencia_alt1 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt1'])->codigo;
                            $secuencia_alt1_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt1'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_1 = $secuencia_alt1;
                                    $maquina_proceso_1_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_1 = $secuencia_alt1;
                                    $maquina_proceso_2_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_1 = $secuencia_alt1;
                                    $maquina_proceso_3_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_1 = $secuencia_alt1;
                                    $maquina_proceso_4_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_1 = $secuencia_alt1;
                                    $maquina_proceso_5_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_1 = $secuencia_alt1;
                                    $maquina_proceso_6_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }
                    }
                }
            }
        }

        //Planta TilTil
        if ($ot->so_planta_original == 2) { //Planta Original
            if (!is_null($ot->so_planta_original_select_values)) {
                $secuencia_productiva_planta_original = json_decode($ot->so_planta_original_select_values, true);
                for ($i = 1; $i <= count($secuencia_productiva_planta_original); $i++) {
                    if (isset($secuencia_productiva_planta_original['fila_' . $i])) {
                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['org'])) {
                            $secuencia_original = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->codigo;
                            $secuencia_original_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['org'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1 = $secuencia_original;
                                    $maquina_proceso_1_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2 = $secuencia_original;
                                    $maquina_proceso_2_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3 = $secuencia_original;
                                    $maquina_proceso_3_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4 = $secuencia_original;
                                    $maquina_proceso_4_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5 = $secuencia_original;
                                    $maquina_proceso_5_descripcion = $secuencia_original_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6 = $secuencia_original;
                                    $maquina_proceso_6_descripcion = $secuencia_original_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }

                        if (isset($secuencia_productiva_planta_original['fila_' . $i]['alt1'])) {
                            $secuencia_alt1 = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt1'])->codigo;
                            $secuencia_alt1_descripcion = SecuenciaOperacional::find($secuencia_productiva_planta_original['fila_' . $i]['alt1'])->descripcion;
                            switch ($i) {
                                case 1:
                                    $maquina_proceso_1_1 = $secuencia_alt1;
                                    $maquina_proceso_1_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 2:
                                    $maquina_proceso_2_1 = $secuencia_alt1;
                                    $maquina_proceso_2_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 3:
                                    $maquina_proceso_3_1 = $secuencia_alt1;
                                    $maquina_proceso_3_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 4:
                                    $maquina_proceso_4_1 = $secuencia_alt1;
                                    $maquina_proceso_4_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 5:
                                    $maquina_proceso_5_1 = $secuencia_alt1;
                                    $maquina_proceso_5_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                case 6:
                                    $maquina_proceso_6_1 = $secuencia_alt1;
                                    $maquina_proceso_6_1_descripcion = $secuencia_alt1_descripcion;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }
                    }
                }
            }
        }

        //Centro / Numero de Almacen /CeBe // grupo gastos generales
        if (is_null($ot->planta_id)) {
            $centro = '';
            $num_almacen = '';
            $cebe = '';
            $grupo_gastos_generales = '';
        } else {
            // $grupo_planta = GrupoPlanta::where('planta_id', $ot->planta_id)->where('active', 1)->first();
            // if ($grupo_planta) {
            //     $centro = $grupo_planta->centro;
            //     $num_almacen = $grupo_planta->num_almacen;
            //     $cebe = $grupo_planta->cebe;

            //     //obtener grupo gastos generales
            //     $searchgrupogastosgenerales = CeBe::where('cebe', $grupo_planta->cebe)->where('active', 1)->first();

            //     if ($searchgrupogastosgenerales) {
            //         $grupo_gastos_generales = $searchgrupogastosgenerales->grupo_gastos_generales;
            //     } else {
            //         $grupo_gastos_generales = '';
            //     }
            // } else {
            //     $centro = '';
            //     $num_almacen = '';
            //     $cebe = '';
            //     $grupo_gastos_generales = '';
            // }

            $grupo_planta = GrupoPlanta::where('planta_id', $ot->planta_id)->where('active', 1)->first();
            if ($grupo_planta) {
                $centro = $grupo_planta->centro;
                $num_almacen = $grupo_planta->num_almacen;

                //para obter cebe buscamos subhierearchie
                // dd($ot->subhierarchy);
                $searchsubsubhierearchie = Subsubhierarchy::find($ot->subsubhierarchy->id);
                // dd($searchsubhierearchie);
                if ($searchsubsubhierearchie) {

                    $searchsubhierearchie = Subhierarchy::find($searchsubsubhierearchie->subhierarchy_id);

                    if ($searchsubhierearchie) {
                        $hierearchie_id = $searchsubhierearchie->hierarchy_id;

                        // dd($hierearchie_id,$ot->planta_id);
                        // dd($$ot->planta_id);

                        $searchcebe = CeBe::where('hierearchie_id',NULL)->where('planta_id', $ot->planta_id)->where('active', 1)->first();

                        if ($searchcebe) {
                            $cebe = $searchcebe->cebe;
                            $grupo_gastos_generales = $searchcebe->grupo_gastos_generales;
                        } else {
                            $cebe = '';
                            $grupo_gastos_generales = '';
                        }
                    } else {
                        $cebe = '';
                        $grupo_gastos_generales = '';
                    }
                } else {
                    $cebe = '';
                    $grupo_gastos_generales = '';
                }
            } else {
                $centro = '';
                $num_almacen = '';
                $cebe = '';
                $grupo_gastos_generales = '';
            }
        }

        //Almacen
        if ($centro == '' || is_null($centro)) {
            $almacen = '';
        } else {
            $codigo_almacen = Almacen::where('centro', $centro)->first();
            if ($codigo_almacen) {
                $almacen = $codigo_almacen->codigo;
            } else {
                $almacen = '';
            }
        }

        //Organizacion de Ventas
        if (is_null($ot->org_venta_id)) {
            $org_ventas = '';
        } else {
            $codigo_org_ventas = OrganizacionVenta::where('id', $ot->org_venta_id)->where('active', 1)->first();
            if ($codigo_org_ventas) {
                $org_ventas = $codigo_org_ventas->codigo;
            } else {
                $org_ventas = '';
            }
        }

        //Canal
        if (is_null($ot->canal_id)) {
            $canal = '';
        } else {
            $codigo_canal = Canal::where('id', $ot->canal_id)->first();
            if ($codigo_canal) {
                $canal = $codigo_canal->codigo;
            } else {
                $canal = '';
            }
        }

        //Sector
        if (is_null($ot->product_type_id)) {
            $sector = '';
        } else {
            $codigo_sector = Sector::where('product_type_id', $ot->product_type_id)->where('active', 1)->first();
            if ($codigo_sector) {
                $sector = $codigo_sector->codigo;
            } else {
                $sector = '';
            }
        }

        //Grupo Imputacion Material
        if (is_null($ot->process_id)) {
            $grupo_imp_material = '';
            $familia = '';
            $modelo_material = '';
        } else {
            // $codigo_grupo_imp_material = GrupoImputacionMaterial::where('proceso_id', $ot->process_id)->where('active', 1)->first();
            // if ($codigo_grupo_imp_material) {
            //     $grupo_imp_material = $codigo_grupo_imp_material->codigo;
            //     $familia = $codigo_grupo_imp_material->familia;
            //     $modelo_material = $codigo_grupo_imp_material->material_modelo;
            // } else {
            //     $grupo_imp_material = '';
            //     $familia = '';
            //     $modelo_material = '';
            // }

            $searchprocesss = Process::find($ot->process_id);

            if ($searchprocesss) {
                $desc_proceso = $searchprocesss->descripcion;

                $codigo_grupo_imp_material = GrupoImputacionMaterial::where('proceso', $desc_proceso)->where('active', 1)->first();
                if ($codigo_grupo_imp_material) {
                    $grupo_imp_material = $codigo_grupo_imp_material->codigo;
                    $familia = $codigo_grupo_imp_material->familia;
                    $modelo_material = $codigo_grupo_imp_material->material_modelo;
                } else {
                    $grupo_imp_material = '';
                    $familia = '';
                    $modelo_material = '';
                }
            } else {
                $grupo_imp_material = '';
                $familia = '';
                $modelo_material = '';
            }
        }

        //Grupo Materiales 1
        if (is_null($ot->armado_id)) {
            $grupo_materiales_1 = '';
        } else {
            $codigo_grupo_materiales_1 = GrupoMateriales1::where('armado_id', $ot->armado_id)->where('active', 1)->first();
            if ($codigo_grupo_materiales_1) {
                $grupo_materiales_1 = $codigo_grupo_materiales_1->codigo;
            } else {
                $grupo_materiales_1 = '';
            }
        }

        //Grupo Materiales 2
        if (is_null($ot->product_type_id)) {
            $grupo_materiales_2 = '';
        } else {
            $codigo_grupo_materiales_2 = GrupoMateriales2::where('pruduct_type_id', $ot->product_type_id)->where('active', 1)->first();
            if ($codigo_grupo_materiales_2) {
                $grupo_materiales_2 = $codigo_grupo_materiales_2->codigo;
            } else {
                $grupo_materiales_2 = '';
            }
        }

        //Rechazo Conjunto
        if (is_null($ot->process_id)) {
            $rechazo_conjunto = 0;
        } else {
            $porcentaje_rechazo_conjunto = RechazoConjunto::where('proceso_id', $ot->process_id)->where('active', 1)->first();
            if ($porcentaje_rechazo_conjunto) {
                $rechazo_conjunto = $porcentaje_rechazo_conjunto->porcentaje_proceso_solo + $porcentaje_rechazo_conjunto->porcentaje_proceso_barniz + $porcentaje_rechazo_conjunto->porcentaje_proceso_maquila;
            } else {
                $rechazo_conjunto = 0;
            }
        }

        //Cantidad Base
        $cantidad_base = 1000; //Fijo segun cliente

        //Tiempo tratamiento
        $tiempo_tratamiento = 0;
        if (is_null($ot->process_id)) {
            $tiempo_tratamiento = 0;
        } else {
            $data_tiempo_tratamiento = TiempoTratamiento::where('proceso_id', $ot->process_id)->where('active', 1)->first();
            if ($data_tiempo_tratamiento) {
                if (is_null($ot->planta_id)) {
                    $tiempo_tratamiento = 0;
                } else {
                    if ($ot->planta_id == 1) { //Buin
                        $tiempo_tratamiento = $data_tiempo_tratamiento->tiempo_buin;
                    } else if ($ot->planta_id == 2) { //Tiltil
                        $tiempo_tratamiento = $data_tiempo_tratamiento->tiempo_tiltil;
                    } else if ($ot->planta_id == 3) { //Osorno
                        $tiempo_tratamiento = $data_tiempo_tratamiento->tiempo_osorno;
                    }
                }
            } else {
                $tiempo_tratamiento = 0;
            }
        }

        //Consumo_cinta
        if (is_null($ot->cintas_x_caja) || $ot->cintas_x_caja == 0 || $ot->cintas_x_caja == '') {
            $consumo_cinta = 0;
        } else {
            if ($ot->largura_hm == 0 || $ot->largura_hm == '') {
                $consumo_cinta = 0;
            } else {
                $consumo_cinta = $ot->largura_hm * $ot->cintas_x_caja;
            }
        }

        //Total golpes
        if (is_null($ot->golpes_ancho) || $ot->golpes_ancho == 0 || $ot->golpes_ancho == '') {
            $total_golpes = 0;
        } else {
            if (is_null($ot->golpes_largo) || $ot->golpes_largo == 0 || $ot->golpes_largo == '') {
                $total_golpes = 0;
            } else {
                $total_golpes = $ot->golpes_ancho * $ot->golpes_largo;
            }
        }

        //Grupo Materiales 3
        if (is_null($ot->style_id)) {
            $grupo_materiales_3 = '';
        } else {
            $codigo_grupo_materiales_3 = Style::where('id', $ot->style_id)->where('active', 1)->first();
            if ($codigo_grupo_materiales_3) {
                $grupo_materiales_3 = $codigo_grupo_materiales_3->grupo_materiales;
            } else {
                $grupo_materiales_3 = '';
            }
        }

        //Adhesivo
        if (is_null($ot->planta_id)) {
            $adhesivo = '';
            $consumo_adhesivo = '';
        } else {
            if (is_null($ot->process_id) || $ot->process_id == '') {
                $adhesivo = '';
                $consumo_adhesivo = '';
            } else {
                if (in_array($ot->process_id, [2, 3, 11, 12, 13, 14, 15])) {
                    $adhesivo = '';
                    $consumo_adhesivo = '';
                } else {
                    if (in_array($ot->process_id, [1, 5, 10])) { //Procesos Flexo
                        if (is_null($maquina_proceso_2) || $maquina_proceso_2 == '') {
                            $adhesivo = '';
                            $consumo_adhesivo = '';
                        } else {
                            $codigo_adhesivo = Adhesivo::where('planta_id', $ot->planta_id)->where('maquina', $maquina_proceso_2)->where('active', 1)->first();
                            if ($codigo_adhesivo) {
                                $adhesivo = $codigo_adhesivo->codigo;
                                $consumo_adhesivo = $codigo_adhesivo->consumo;
                            } else {
                                $adhesivo = '';
                                $consumo_adhesivo = '';
                            }
                        }
                    } else {
                        if ($ot->process_id == 4) { // Proceso Diecutter con Pegado
                            if (is_null($maquina_proceso_3) || $maquina_proceso_3 == '') {
                                $adhesivo = '';
                                $consumo_adhesivo = '';
                            } else {
                                $codigo_adhesivo = Adhesivo::where('planta_id', $ot->planta_id)->where('maquina', $maquina_proceso_3)->where('active', 1)->first();
                                if ($codigo_adhesivo) {
                                    $adhesivo = $codigo_adhesivo->codigo;
                                    $consumo_adhesivo = $codigo_adhesivo->consumo;
                                } else {
                                    $adhesivo = '';
                                    $consumo_adhesivo = '';
                                }
                            }
                        } else {
                            $adhesivo = '';
                            $consumo_adhesivo = '';
                        }
                    }
                }
            }
        }

        //Gramos Adhesivo
        $longitud_pegado    = (is_null($ot->longitud_pegado) || $ot->longitud_pegado == '') ? 0 : $ot->longitud_pegado;
        $golpes_largo       = (is_null($ot->golpes_largo) || $ot->golpes_largo == '') ? 0 : $ot->golpes_largo;
        $golpes_ancho       = (is_null($ot->golpes_ancho) || $ot->golpes_ancho == '') ? 0 : $ot->golpes_ancho;
        $consumo_adhesivo_aux = (is_null($consumo_adhesivo) || $consumo_adhesivo == '') ? 0 : $consumo_adhesivo;

        $gramos_Adhesivo = (($longitud_pegado / 1000) * ($consumo_adhesivo_aux) * ($golpes_largo * $golpes_ancho));

        $array_data = array(
            "STDPD"         => ['Mat. Configurable', 'EM-SEMI003'], //['Mat. Configurable',$familia],
            "MATNR"         => ['Número de Material', $semielaborado],
            "EN_PLANCHA_SEMI" => ['Número de Semielaborado', $semielaborado],
            // "RMMG1_REF-MATNR" => ['Material Modelo', $modelo_material],
            "RMMG1_REF-MATNR" => ['Material Modelo', 'GE2EM-SEMI003'],

            "MAKTX"         => ['Descripción Comercial', $ot->material ? 'SE PL/'.$ot->material->descripcion : null],
            "WERKS"         => ['centro', $centro],
            //   "LGORT"         => ['Almacen',$almacen],
            //"VKORG"         => ['Organiz. Ventas',$org_ventas],
            //"VTWEG"         => ['Canal distrib.',$canal],
            "LGORT"         => ['Almacen', "020"],
            "MARA-BISMT"    => ['N° Material antiguo',$ot->id],
            // "SPART"         => ['Sector', $sector],
            "SPART"         => ['Sector', '07'],
            "PRDHA"         => ['Jerarquia', $ot->subsubhierarchy ? $ot->subsubhierarchy->jerarquia_sap : null],
            "BRGEW"         => ['Peso bruto(G2)', number_format_unlimited_precision(str_replace(',', '.', $ot->pesoBrutoSemielaborado))],
            "NTGEW"         => ['Peso neto(G2)', number_format_unlimited_precision(str_replace(',', '.', $ot->pesoNetoSemielaborado))],
            "VOLUM"         => ['Volumen(G2)', is_numeric($ot->volumenUnitarioSemielaborado) ? intval(round($ot->volumenUnitarioSemielaborado)) : null],
            "UMREN"         => ['UMA Área (m2)', (intval($ot->umaAreaSemielaborado))],
            "UMREN01"         => ['UMA Peso (kg):', (intval($ot->umaPesoSemielaborado))],
            //"NORMT"         => ['Denom.estándar',$ot->material ? $ot->material->descripcion : null],
            //"KONDM"         => ['Gr.imputación mat.',$grupo_imp_material],
            //"PRODH"         => ['Jerarquía productos',$ot->subsubhierarchy ? $ot->subsubhierarchy->jerarquia_sap : null],
            //"MVGR1"         => ['Grupo materiales 1',$grupo_materiales_1],
            //"MVGR2"         => ['Grupo de material 2',$grupo_materiales_2],
            //"MVKE-MVGR3"    => ['Grupo de material 3',$grupo_materiales_3],
            "MARC-PRCTR"    => ['CeBe', $cebe],
            "MARC-AUSSS"    => ['Rechazo conjunto (%)', $rechazo_conjunto],
            "MARC-WEBAZ"    => ['Tmpo.tratamiento EM', $tiempo_tratamiento],
            "MARC-BASMG"    => ['Cantidad base', $cantidad_base],
            "MBEW-KOSGR"    => ['Grupo gastos gral.', $grupo_gastos_generales],
            "EN_OT"         => ['Numero OT',$ot->id],
            "EN_CODCLI"     => ['cliente', $ot->client->codigo],
            //"EN_CODVEN"     => ['vendedor',isset($ot->creador->nombre_sap) ? $ot->creador->nombre_sap : $ot->creador->fullname],

            //"EN_CODCLI"     => ['Fecha',$ot->fecha],
            //"EN_LARGO"      => ['Largo Interior (MM)',$ot->interno_largo],
            //"EN_ANCHO"      => ['Ancho Interior (MM)',$ot->interno_ancho],
            //"EN_ALTO"       => ['Alto Interior (MM)',$ot->interno_alto],
            //"EN_LARGURA"    => ['Largura HM (MM)',$ot->largura_hm],
            //"EN_ANCHURA"    => ['Anchura HM (MM)',$ot->anchura_hm],
            //"EN_LARGOEXT"   => ['Largo Exterior (MM)',$ot->externo_largo],
            //"EN_ANCHOEXT"   => ['Ancho Exterior (MM)',$ot->externo_ancho],
            //"EN_ALTOEXT"    => ['Alto Exterior (MM)',$ot->externo_alto],
            "EN_CARTON"     => ['Cartón', $ot->carton ? $ot->carton->codigo : null],
            //"EN_TIPO"       => ['Tipo de Producto (Tipo Item)',isset($ot->productType) ? $ot->productType->codigo_sap : null],
            //"EN_ESTILO"     => ['Estilo de Producto',isset($ot->style) ? $ot->style->glosa : null],
            //"EN_CARCTRIST-ESTILO" => ['Caracteristicas Adicionales',$ot->caracteristicas_adicionales ? $ot->caracteristicas_adicionales : null],
            "EN_C1_R1"      => ['Rayado C1/R1 (MM)', isset($ot->rayado_c1r1) ? $ot->rayado_c1r1 : null],
            "EN_R1_R2"      => ['Rayado R1/R2 (MM)', isset($ot->rayado_r1_r2) ? $ot->rayado_r1_r2 : null],
            "EN_R2_C2"      => ['Rayado R2/C2 (MM)', isset($ot->rayado_r2_c2) ? $ot->rayado_r2_c2 : null],
            "EN_RAYADO"     => ['Tipo de Rayado', isset($ot->rayado_type_id) ? $ot->rayado->descripcion : null],
            //"EN_TIPOIMPRESION" => ['Tipo Impresión',$ot->impresion ? $ot->impresion_detalle->descripcion : null],
            //"EN_COLORES"    => ['Número de Colores',$ot->numero_colores],
            //"EN_PRUEBACOLOR" => ['Prueba de Color', isset($ot->prueba_color) ? [1 => "Si", 0 => "No"][$ot->prueba_color] : null],
            //"EN_CARACTERISTICO" => ['Recorte Característico (M2)', number_format_unlimited_precision(str_replace(',', '.',$detalle_recorte_caracteristico))],
            //"EN_ADICIONAL"  => ['Recorte Adicional (M2)', number_format_unlimited_precision(str_replace(',', '.',$detalle_recorte_adicional))],
            "EN_CAD"        => ['Plano CAD', isset($ot->cad) ? $ot->cad : null],
            // "EN_AREA_INTERIOR_PERIMETRO" => ['Area Producto (M2)', $detalle_area_producto],
            // "EN_AREA_INTERIOR_PERIMETRO" => ['Area Interior Perímetro', $detalle_area_producto],
            "EN_AREA_INTERIOR_PERIMETRO" => ['Area Producto (M2)', is_numeric($ot->larguraHc) && is_numeric($ot->anchuraHc) ? $ot->larguraHc * $ot->anchuraHc : null],

            //"EN_ESTADO_PALETIZAD0" => ['Estado de Palletizado', isset($ot->pallet_status_type_id) ? $ot->pallet_status->descripcion : null],
            "EN_TIPO_PALET_GE" => ['Tipo de Pallet', ''],
            // "EN_TIPO_PALET_GE" => ['Tipo de Pallet', $ot->tipo_pallet ? $ot->tipo_pallet->codigo : null],
            //"EN_TRATAMIENTO_PALET" => ['Tratamiento de Pallet', isset($ot->pallet_treatment) ? [1 => "Si", 0 => "No"][$ot->pallet_treatment] : null],
            //"EN_CAJAS_POR_PALET" => ['Nro Cajas por Pallet', $ot->cajas_por_pallet ? $ot->cajas_por_pallet : null],
            "EN_PLACAS_POR_PALET" => ['Nro Placas por Pallet', $ot->placas_por_pallet ? $ot->placas_por_pallet : null],
            //"EN_PATRON_CARGA_PALET" => ['Patron Carga Pallet', $ot->patron_pallet ? $ot->patron_pallet->descripcion : null],
            //"EN_PATRON_ZUNCHO_BULTO" => ['Patron Zuncho Bulto', $ot->patron_zuncho_bulto ? [1 => "2x0", 2 => "2x1", 3 => "2x2"][$ot->patron_zuncho_bulto] : null],
            //"EN_PROTECCION" => ['Proteccion',isset($ot->protection_type_id) ? $ot->protection->descripcion : null],
            //"EN_PATRON_ZUNCHO_PALET" => ['Patron Zuncho Pallet',$ot->patron_zuncho_bulto ? [1 => "2x0", 2 => "2x1", 3 => "2x2"][$ot->patron_zuncho_bulto] : null],
            //"EN_PROTECCION_PALET" => ['Protección Pallet',$ot->proteccion_pallet ? $ot->proteccion_pallet->descripcion : null],
            //"EN_CAJAS_POR_PAQUETE" => ['Nro Cajas por Paquete',$ot->cajas_por_paquete ? $ot->cajas_por_paquete->descripcion : null],
            //"EN_PATRON_ZUNCHO_PAQUETE" => ['Patron Zuncho Paquete',$ot->patron_zuncho_paquete ? $ot->patron_zuncho_paquete : null],
            //"EN_TERMOCONTRAIBLE" => ['Termocontraible',isset($ot->termocontraible) ? [1 => "Si", 0 => "No"][$ot->termocontraible] : null],
            //"EN_PAQUETES_POR_UNITIZADO" => ['Nro Cajas por Unitizados',$ot->paquetes_por_unitizado ? $ot->paquetes_por_unitizado : null],
            //"EN_UNITIZADOS_POR_PALET" => ['Nro Unitizados por Pallet',$ot->unitizado_por_pallet ? $ot->unitizado_por_pallet : null],
            "EN_FORMATO_ETIQUETA_PALET" => ['Tipo Formato Etiqueta Pallet', $ot->formato_etiqueta_pallet ? $ot->formato_etiqueta_pallet->descripcion : null],
            "EN_ETIQUETAS_POR_PALET" => ['Nro Etiqueta Pallet', $ot->numero_etiquetas ? [0, 1, 2, 3, 4][$ot->numero_etiquetas] : null],
            //"EN_CERTIFICADO" => ['Certificado Calidad',$ot->qa ? $ot->qa->descripcion : null],
            //"EN_RESISTENCIA_MT" => ['BCT MIN (LB)',number_format_unlimited_precision($ot->bct_min_lb)],
            "EN_JERARQUIA" => ['Jerarquia', $ot->subsubhierarchy ? $ot->subsubhierarchy->jerarquia_sap : null],
            // "EN_COD_PT_CLI" => ['Código Producto Cliente', $ot->codigo_producto_cliente ? $ot->codigo_producto_cliente : null],
            "EN_C1_CINTA1" => ['Distancia corte 1 a cinta 1', isset($ot->distancia_cinta_1) ? $ot->distancia_cinta_1 : null],
            "EN_C1_CINTA2" => ['Distancia corte 1 a cinta 2', isset($ot->distancia_cinta_2) ? $ot->distancia_cinta_2 : null],
            "EN_C1_CINTA3" => ['Distancia corte 1 a cinta 3', isset($ot->distancia_cinta_3) ? $ot->distancia_cinta_3 : null],
            "EN_C1_CINTA4" => ['Distancia corte 1 a cinta 4', isset($ot->distancia_cinta_4) ? $ot->distancia_cinta_4 : null],
            "EN_C1_CINTA5" => ['Distancia corte 1 a cinta 5', isset($ot->distancia_cinta_5) ? $ot->distancia_cinta_5 : null],
            "EN_C1_CINTA6" => ['Distancia corte 1 a cinta 6', isset($ot->distancia_cinta_6) ? $ot->distancia_cinta_6 : null],
            "EN_TIPO_CINTA" => ['TIPO DE CINTA', isset($ot->tipo_cinta) ? $ot->tipos_cintas->descripcion : null],
            "EN_CORTE_LINER" => ['Corte de Liner', isset($ot->corte_liner) ? [1 => "Si", 0 => "No"][$ot->corte_liner] : null],
            "EN_CINTA" => ['Caracteristica de Cinta', isset($ot->tipo_cinta) ? $ot->tipos_cintas->codigo : null],

            //"EN_CORTE_LINER" => ['Cantidad Cinta por CAJA',$ot->cintas_x_caja],
            "EN_CONSUMO_CINTA" => ['Consumo cinta', $consumo_cinta],
            "EN_SELLO" => ['Etiqueta FSC/PRODUCTO FSC', isset($ot->fsc) ? [2 => "NO", 5 => "No", 3 => "FACTURACION Y LOGO", 4 => "FACTURACION Y LOGO", 6 => "SOLO FACTURACION"][$ot->fsc] : null],
            "EN_ORIENTACION" => ['Orientación Placa', isset($ot->orientacion_placa) ? [0, 90][$ot->orientacion_placa] : null],
            //"EN_CARACT_ADICION" => ['Características Adicionales',isset($ot->prepicado) ? $ot->prepicado->descripcion  : null],
            //"EN_FACTURACION" => ['Indicador Facturación Diseño Estructural',$ot->indicador_facturacion ? [1=>'RRP',2=>'E-Commerce',3=>'Esquineros',4=>'Geometría',5=>'Participación nuevo Mercado',6=>'',7=>'Innovación',8=>'Sustentabilidad',9=>'Automatización',10=>'No Aplica',11=>'Ahorro',12=>''][$ot->indicador_facturacion] : null],
            //"EN_FACTURACION_DG" => ['Indicador Facturación Diseño Gráfico',$ot->indicador_facturacion_diseno_grafico],
            //"EN_TIPO_CIERRE" => ['Tipo de Pegado',isset($ot->pegado_terminacion) ? [0=>"No Aplica", 2=>"Pegado Interno", 3=>"Pegado Externo", 4=>"Pegado 3 Puntos", 5=>"Pegado 4 Puntos"][$ot->pegado_terminacion] : null],
            //"ARMADO" => ['Armado',$ot->armado ? $ot->armado->descripcion  : null],
            //"SENTIDO_DE_ARMADO" => ['Sentido Armado',$ot->sentido_armado ? [1 => "No aplica", 2 => "Ancho a la Derecha", 3 => "Ancho a la Izquierda", 4 => "Largo a la Izquierda",  5 => "Largo a la Derecha"][$ot->sentido_armado] : null],
            //"EN_GRAMAJE_G_M2" => ['Gramaje (G/m2)',isset($ot->gramaje) ? $ot->gramaje : null],
            //"EN_PESO_G" => ['Peso (G)',isset($ot->peso) ? $ot->peso : null],
            //"EN_ESPESOR_MM" => ['Espesor Caja (mm)',isset($ot->espesor_caja) ? $ot->espesor_caja : null],
            //"EN_ECT_MINIMO" => ['ECT Minimo (LB/PULG2)',isset($ot->ect) ? number_format_unlimited_precision(str_replace(',', '.',$ot->ect)) : null],
            //"EN_FCT_MINIMO" => ['FCT Minimo (LB/PULG22)',isset($ot->fct) ? number_format_unlimited_precision(str_replace(',', '.',$ot->fct)) : null],
            //"EN_COBB_INT_MAX" => ['Cobb INT. ( 2 Min.) Max.',isset($ot->cobb_interior) ? $ot->cobb_interior : null],
            //"EN_COBB_EXT_MAX" => ['Cobb EXT. ( 2 Min.) Max.',isset($ot->cobb_exterior) ? $ot->cobb_exterior : null],
            //"EN_FLEXION_ALETA" => ['Flexion de Aleta (N)',isset($ot->flexion_aleta) ? $ot->flexion_aleta : null],
            //"EN_MULLEN" => ['Mullen (LB/PULG2)',isset($ot->mullen) ? number_format_unlimited_precision(str_replace(',', '.',$ot->mullen)) : null],
            //"EN_RESISTENCIA_STD" =>['Resistencia mínima (Humeda)',$ot->bct_humedo_lb],
            //"INCISION_RAYADO_LONG" => ['Incisión Rayado Long.[N]',isset($ot->incision_rayado_longitudinal) ? $ot->incision_rayado_longitudinal : null],
            //"INCISION_RAYADO_TRANS" => ['Incisión Rayado Transv.[N]',isset($ot->incision_rayado_vertical) ? $ot->incision_rayado_vertical : null],
            //"EN_DST_BPI" => ['DST (BPI)',isset($ot->dst) ? $ot->dst : null],
            //"ESPESOR_PL" => ['Espesor Placa (mm)',isset($ot->espesor_placa) ? $ot->espesor_placa : null],
            //"EN_POROSIDAD" => ['Porosidad (SEG)',isset($ot->porosidad) ? $ot->porosidad : null],
            //"EN_BRILLO" => ['Brillo (%)',isset($ot->brillo) ? $ot->brillo : null],
            //"EN_RIGID_4_PUN_LONGITUDAL" => ['Rigidez 4 Puntos Longitudinal (N/MM)',isset($ot->rigidez_4_ptos_long) ? $ot->rigidez_4_ptos_long : null],
            //"EN_RIGID_4_PUN_TRANSVERSAL" => ['Rigidez 4 Puntos Transversal (N/MM)',isset($ot->rigidez_4_ptos_transv) ? $ot->rigidez_4_ptos_transv : null],
            //"EN_ANGUL_DESLIZ_TP_EXT" => ['Angulo de Deslizamiento-Tapa Exterior (°)',isset($ot->angulo_deslizamiento_tapa_exterior) ? $ot->angulo_deslizamiento_tapa_exterior : null],
            //"EN_ANGUL_DESLIZ_TP_INT" => ['Angulo de Deslizamiento-Tapa Interior (°)',isset($ot->angulo_deslizamiento_tapa_interior) ? $ot->angulo_deslizamiento_tapa_interior : null],
            //"EN_FROTE" => ['Resistencia al Frote',isset($ot->resistencia_frote) ? $ot->resistencia_frote : null],
            //"EN_IMPRESION_BORDE" => ['Impresión de Borde',$ot->impresion_borde],
            //"EN_IMPRESION_SOBRE_RAYADO" => ['Impresión Sobre Rayado',$ot->impresion_sobre_rayado],
            // "EN_CONTEN_RECICLADO" => ['Contenido Reciclado (%)', isset($ot->contenido_reciclado) ? $ot->contenido_reciclado : null],
            //"EN_CANT_LARGO" => ['Golpes al Largo',$ot->golpes_largo],
            //"EN_CANT_ANCHO" => ['Golpes al Ancho',$ot->golpes_ancho],
            "EN_TOTAL_GOL_MATRIZ" => ['Golpes Total', '1'],
            "EN_LARGURA_HC" => ['Largura HC (MM)', $ot->larguraHc],
            "EN_ANCHURA_HC" => ['Anchura HC (MM)', $ot->anchuraHc],
            //"EN_COLOR_COMP_1" => ['Código Color 1 (INTERIOR TyR)',$ot->color_1 ? $ot->color_1->codigo : null],
            //"EN_CONSUMO_1" => ['Gramos Color 1 (INTERIOR TyR)',number_format_unlimited_precision_sap(str_replace(',', '.',$ot->consumo1))],
            //"EN_COLOR_COMP_2" => ['Código Color 2',$ot->color_2 ? $ot->color_2->codigo : null],
            //"EN_CONSUMO_2" => ['Gramos Color 2',number_format_unlimited_precision_sap(str_replace(',', '.',$ot->consumo2))],
            //"EN_COLOR_COMP_3" => ['Código Color 3',$ot->color_3 ? $ot->color_3->codigo : null],
            //"EN_CONSUMO_3" => ['Gramos Color 3',number_format_unlimited_precision_sap(str_replace(',', '.',$ot->consumo3))],
            //"EN_COLOR_COMP_4" => ['Código Color 4',$ot->color_4 ? $ot->color_4->codigo : null],
            //"EN_CONSUMO_4" => ['Gramos Color 4',number_format_unlimited_precision_sap(str_replace(',', '.',$ot->consumo4))],
            //"EN_COLOR_COMP_5" => ['Código Color 5',$ot->color_5 ? $ot->color_5->codigo : null],
            //"EN_CONSUMO_5" => ['Gramos Color 5',number_format_unlimited_precision_sap(str_replace(',', '.',$ot->consumo5))],
            //"EN_COLOR_COMP_6" => ['Código Color 6',$ot->color_6 ? $ot->color_6->codigo : null],
            //"EN_CONSUMO_6" => ['Gramos Color 6',number_format_unlimited_precision_sap(str_replace(',', '.',$ot->consumo6))],
            //"EN_COLOR_COMP_7" => ['Código Color 7',$ot->color_7 ? $ot->color_7->codigo : null],
            //"EN_CONSUMO_7" => ['Gramos Color 7',number_format_unlimited_precision_sap(str_replace(',', '.',$ot->consumo7))],
            //"EN_ADHESIVO" => ['Codigo Adhesivo acorde a maquina',$adhesivo],
            //"EN_CONSUMO_ADH" => ['Consumo Adhesivo PVA',$gramos_Adhesivo],
            "EN_MAQUINA_PROCESO_1" => ['Maquina Proceso 1', $maquina_proceso_1],
            "EN_MAQUINA_PROCESO_1_1" => ['Maquina Proceso 1.1 corrugado', $maquina_proceso_1_1],
            "EN_DESCRIPCION_MAQ_PROCESO_1" => ['Descripción Maquina Proceso 1', $maquina_proceso_1_descripcion],
            "EN_DESCRIPCION_MAQ_PROCESO_1_1" => ['Descripción Maquina Proceso 1.1', $maquina_proceso_1_1_descripcion],
            // "EN_MAQUINA_PROCESO_2" => ['Maquina Proceso 2', $maquina_proceso_2],
            "EN_MAQUINA_PROCESO_2" => ['Maquina Proceso 2', ''],
            //"EN_MAQUINA_PROCESO_3" =>['Maquina Proceso 3',$maquina_proceso_3],
            //"EN_MAQUINA_PROCESO_4" =>['Maquina Proceso 4',$maquina_proceso_4],
            //"EN_MAQUINA_PROCESO_5" =>['Maquina Proceso 5',$maquina_proceso_5],
            //"EN_MAQUINA_PROCESO_6" =>['Maquina Proceso 6',$maquina_proceso_6],
            //"EN_CLISSE_1" => ['Clisse 1',(is_null($ot->material_id)) ? null : 'ENC'.$ot->material->codigo],
            //"EN_MATRIZ_1" => ['Matriz 1',is_null($ot->matriz_id) ? null : $ot->matrices->material],
            //"EN_MATRIZ_2" => ['Matriz 2',is_null($ot->matriz_id_2) ? null : $ot->matrices_2->material],
            //"EN_MATRIZ_3" => ['Matriz 3',is_null($ot->matriz_id_3) ? null : $ot->matrices_3->material],
            // "EN_CINTA" => ['Caracteristica de Cinta', isset($ot->tipo_cinta) ? $ot->tipos_cintas->descripcion : null],
            // "EN_CORTE_LINER" => ['Corte de Liner', isset($ot->corte_liner) ? [1 => "Si", 0 => "No"][$ot->corte_liner] : null],
        );

        return $item_array = $array_data;
    }
}
