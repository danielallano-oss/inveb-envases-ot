<?php

namespace App\Http\Controllers;

use App\Areahc;
use App\Envase;
use App\Hierarchy;
use App\Process;
use App\ProductType;
use App\Rubro;
use App\Style;
use App\TipoOnda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use stdClass;

class AreahcController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Solo los 8 estilos asignados para calculo estilos 200-201-202-203-216-221-222-223
        $styles = Style::where('active', 1)->whereIn('id', [1, 2, 3, 4, 12, 14, 15, 16])->pluck('glosa', 'id')->toArray();
        // Solo "Caja" (caja = "una pieza") "tapa" o "fondo"
        $productTypes = ProductType::where('active', 1)->whereIn('id', [3, 4, 5])->pluck('descripcion', 'id')->toArray();
        // Todos los procesos menos offset y sin proceso
        $procesos = Process::where('active', 1)->whereNotIn('id', [3, 4, 5, 6, 7, 8, 9])->orderBy("descripcion")->pluck('descripcion', 'id')->toArray();
        // Todas excepto "Exportaciones"
        $hierarchies = Hierarchy::whereIn('id', [3, 5])->where('active', 1)->pluck('descripcion', 'id')->toArray();
        // Solo alimentos otros vinos aseo y deshidratados para calculo de area
        $rubros = Rubro::whereIn('id', [12, 13, 14, 18, 19])->pluck('descripcion', 'id')->toArray();
        $ondas = TipoOnda::pluck('onda', 'id')->toArray();
        $envases = Envase::where('active', 1)->whereIn('id', [1, 3, 4, 5, 7, 8, 9])->pluck('descripcion', 'id')->toArray();
        return view('cotizador.areas-hc.create', compact('styles', 'productTypes', 'procesos', 'ondas', 'envases', 'hierarchies', 'rubros'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            // Datos Comerciales
            'tipo_calculo' => 'required',
            // 'interno_largo' => 'required',
            // 'interno_ancho' => 'required',
            // 'interno_alto' => 'required',
            // 'style_id' => 'required',
            // 'onda_id' => 'required',
        ]);

        $tipo_calculo = request('tipo_calculo');


        $interno_largo = request('interno_largo');
        $interno_ancho = request('interno_ancho');
        $interno_alto = request('interno_alto');
        $onda_id = request('onda_id');
        $style_id = request('style_id');
        $product_type_id = request('areahc_product_type_id');
        $process_id = request('process_id');
        $rubro_id = request('rubro_id');
        $envase_id = request('envase_id');
        $rmt_ingresado = request('rmt');
        $contenido_caja = request('contenido_caja');
        $filas_columnares_por_pallet = request('filas_columnares_por_pallet');
        $pallets_apilados = request('areahc_pallets_apilados');
        $cajas_apiladas_por_pallet = request('cajas_apiladas_por_pallet');
        $carton_color = request('carton_color');
        $ect_min_ingresado = request('ect_min_ingresado');
        $prepicado_ventilacion = request('prepicado_ventilacion');

        // Traslape se usara solo para los siguientes estilos
        if (in_array($style_id, [3, 14, 12, 16])) {
            $traslape = request('traslape');
        } else {
            $traslape = 0;
        }


        $resultado = new stdClass();
        // Calculo
        if ($tipo_calculo == 1) {

            $resultado->externo_largo = externo_largo($interno_largo, $style_id, $onda_id);
            $resultado->externo_ancho = externo_ancho($interno_ancho, $style_id, $onda_id);
            $resultado->externo_alto = externo_alto($interno_alto, $style_id, $onda_id);
            $resultado->areahc = areaHC($style_id, $onda_id, $interno_largo, $interno_ancho, $interno_alto, $process_id, $traslape);

            $resultado->rmt = rmt($rmt_ingresado, $rubro_id, $style_id, $onda_id, $envase_id, $interno_largo, $interno_ancho, $interno_alto,  $traslape, $product_type_id, $filas_columnares_por_pallet, $contenido_caja, $cajas_apiladas_por_pallet, $pallets_apilados);
            $rmt_calculado = $resultado->rmt;
            $resultado->ect_min = ect_min($rmt_ingresado, $prepicado_ventilacion, $rmt_calculado, $onda_id, $interno_largo, $interno_ancho);
            $resultado->carton_seleccionado = calcular_carton($rubro_id, $resultado->ect_min, $onda_id, $carton_color);
        } else if ($tipo_calculo == 2) { //Area HC
            $resultado->externo_largo = externo_largo($interno_largo, $style_id, $onda_id);
            $resultado->externo_ancho = externo_ancho($interno_ancho, $style_id, $onda_id);
            $resultado->externo_alto = externo_alto($interno_alto, $style_id, $onda_id);
            $resultado->areahc = areaHC($style_id, $onda_id, $interno_largo, $interno_ancho, $interno_alto, $process_id, $traslape);
        } else if ($tipo_calculo == 3) {
            //carton
            $resultado->carton_seleccionado = calcular_carton($rubro_id, $ect_min_ingresado, $onda_id, $carton_color);
        }

        return response()->json($resultado);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FactoresDesarrollo  $factoresDesarrollo
     * @return \Illuminate\Http\Response
     */
    public function show(FactoresDesarrollo $factoresDesarrollo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FactoresDesarrollo  $factoresDesarrollo
     * @return \Illuminate\Http\Response
     */
    public function edit(FactoresDesarrollo $factoresDesarrollo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FactoresDesarrollo  $factoresDesarrollo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FactoresDesarrollo $factoresDesarrollo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FactoresDesarrollo  $factoresDesarrollo
     * @return \Illuminate\Http\Response
     */
    public function destroy(FactoresDesarrollo $factoresDesarrollo)
    {
        //
    }
}
