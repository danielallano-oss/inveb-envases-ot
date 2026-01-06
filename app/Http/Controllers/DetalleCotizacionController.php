<?php

namespace App\Http\Controllers;

use App\Cardboard;
use App\Carton;
use App\CartonEsquinero;
use App\CiudadesFlete;
use App\Client;
use App\DetalleCotizacion;
use App\MaquilaServicio;
use App\MermaCorrugadora;
use App\Planta;
use App\Process;
use App\ProductType;
use App\Rubro;
use App\Tarifario;
use App\TarifarioMargen;
use App\WorkOrder;
use App\VariablesCotizador;
use App\PrintType;
use App\PrintingMachine;
use App\Pegado;
use App\PalletHeight;
use App\TipoBarniz;
use App\Zuncho;
use App\Pallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Excel;
use stdClass;
use Auth;

class DetalleCotizacionController extends Controller
{
    public function store(Request $request, $id)
    {
       
        $request->validate([
            // Datos Comerciales
            'tipo_detalle_id' => 'required',
        ]);
        $tipo_detalle_id = request('tipo_detalle_id');

        if ($id) {
            $detalle = DetalleCotizacion::find($id);
        } else {

            $detalle = new DetalleCotizacion();
            $detalle->margen = 0;
        }

        $detalle->tipo_detalle_id = $tipo_detalle_id;

        //Buscamos el precio del dolar que esta registrado al momento de crear la cotizacion
        $precio_dolar_actual = VariablesCotizador::select('precio_dolar')->pluck('precio_dolar')->first();

        // Si viene con un id de ot es por q se esta creando la cotizacion en base a esa ot
        if (request('work_order_id')) $detalle->work_order_id = request('work_order_id');

        $detalle->ciudad_id = request('ciudad_id');

        // Si al crearlo se busca por material campos de material y cad se llenan
        $detalle->codigo_material_detalle = request('codigo_material_detalle');
        $detalle->descripcion_material_detalle = request('descripcion_material_detalle');
        $detalle->cad_material_detalle = request('cad_material_detalle');
        $detalle->cad_material_id = request('cad_material_id');
        $detalle->material_id = request('material_id');
        $detalle->codigo_cliente = request('codigo_cliente');
        $detalle->detalle_valor_dolar = $precio_dolar_actual;

        // Corrugado
        if ($tipo_detalle_id == 1) {
            $detalle->pallets_apilados = request('pallets_apilados');
            $detalle->cantidad = request('cantidad');
            $detalle->area_hc = request('area_hc');
            $detalle->anchura = request('anchura');
            $detalle->largura = request('largura');
            $detalle->product_type_id = request('product_type_id');
            $detalle->carton_id = request('carton_id');
            $detalle->print_type_id = request('print_type_id');
            $detalle->numero_colores = request('numero_colores');
            $detalle->impresion = request('impresion');
            $detalle->golpes_largo = request('golpes_largo');
            $detalle->golpes_ancho = request('golpes_ancho');
            $detalle->process_id = request('process_id');
            $detalle->ink_type_id = request('ink_type_id');
            $detalle->coverage = request('coverage');
            $detalle->coverage_type_id = request('coverage_type_id');
            // $detalle->pegado_terminacion = request('pegado_terminacion');
            $detalle->porcentaje_cera_interno = request('porcentaje_cera_interno');
            $detalle->porcentaje_cera_externo = request('porcentaje_cera_externo');
            $detalle->rubro_id = request('rubro_id');
            $detalle->matriz = request('matriz');
            $detalle->clisse = request('clisse');
            $detalle->royalty = request('royalty');
            // Si el tipo de producto es = 8 = CABEZAL maquila siempre es SI
            $detalle->maquila = request('product_type_id') == 8 ? 1 : request('maquila');
            $detalle->maquila_servicio_id = request('maquila_servicio_id');
            $detalle->detalle_maquila_servicio_id = request('detalle_maquila_servicio_id');
            $detalle->armado_automatico = request('armado_automatico');
            $detalle->armado_usd_caja = (trim($request->input('armado_usd_caja')) != '') ? $request->input('armado_usd_caja') : null;
            $detalle->pallet = request('pallet');
            //$detalle->pallet_type_id = request('pallet_type_id');
            $detalle->zuncho = request('zuncho');
            $detalle->funda = request('funda');
            $detalle->stretch_film = request('stretch_film');
            // Campos opcionales
            $detalle->subsubhierarchy_id = request('subsubhierarchy_id');
            $detalle->devolucion_pallets = request('devolucion_pallets');
            $detalle->ajuste_precios = request('ajuste_precios');
            $detalle->tipo_medida = request('tipo_medida');
            $detalle->largo = request('largo');
            $detalle->ancho = request('ancho');
            $detalle->alto = request('alto');
            $detalle->bct_min_lb = request('bct_min_lb');
            $detalle->bct_min_kg = request('bct_min_kg');
            // $detalle->unidad_medida_bct = request('unidad_medida_bct');



            // Inputs offset
            $detalle->ancho_pliego_cartulina = (trim($request->input('ancho_pliego_cartulina')) != '') ? $request->input('ancho_pliego_cartulina') : null;
            $detalle->largo_pliego_cartulina = (trim($request->input('largo_pliego_cartulina')) != '') ? $request->input('largo_pliego_cartulina') : null;
            $detalle->precio_pliego_cartulina = (trim($request->input('precio_pliego_cartulina')) != '') ? $request->input('precio_pliego_cartulina') : null;
            $detalle->precio_impresion_pliego = (trim($request->input('precio_impresion_pliego')) != '') ? $request->input('precio_impresion_pliego') : null;
            $detalle->gp_emplacado = (trim($request->input('gp_emplacado')) != '') ? $request->input('gp_emplacado') : null;

            $indice_complejidad = 3;

            //Maquila
            $detalle->maquila = (trim($request->input('maquila')) != '') ? $request->input('maquila') :  null;
            //$detalle->maquila_servicio_id = (trim($request->input('maquila_servicio_id')) != '') ? $request->input('maquila_servicio_id') : null;


            // $detalle->cotizacion_id = request('cotizacion_id');
            // $detalle->precio = precio();

            // Si tiene cinta de desgarro planta = tiltil
            $detalle->cinta_desgarro = request('cinta_desgarro');
            if ($detalle->cinta_desgarro == 1) {
                $detalle->planta_id = 2;
            }

            //Solo la impresion trasera esta disponible para la planta tiltil
            if ($detalle->print_type_id == 2) {
                $detalle->planta_id = 2;
            }

            //Si tiene tipo de tinta y el proceso es DIECUTTER - ALTA GRÁFICA o DIECUTTER -C/PEGADO ALTA GRÁFICA solo aplica para planta Buin
            if ($detalle->ink_type_id > 0 && ($detalle->process_id == 12 || $detalle->process_id == 13)) {
                $detalle->planta_id = 1;
            }
            
            //Nuevo campo evolutivo 24-01
            $detalle->pegado_id = (trim($request->input('pegado_id')) != '') ? $request->input('pegado_id') : null;

            ///Nuevos Campos evolutivo 24-01
                $detalle->printing_machine_id = (trim($request->input('printing_machine_id')) != '') ? $request->input('printing_machine_id') : null;
                $detalle->pegado_id = (trim($request->input('pegado_id')) != '') ? $request->input('pegado_id') : null;
                $detalle->pallet_height_id = (trim($request->input('pallet_height_id')) != '') ? $request->input('pallet_height_id') : null;
                $detalle->barniz = (trim($request->input('barniz')) != '') ? $request->input('barniz') : null;
                $detalle->barniz_type_id = (trim($request->input('barniz_type_id')) != '') ? $request->input('barniz_type_id') : null;
                $detalle->cobertura_color_cm2 = (trim($request->input('cobertura_color_cm2')) != '') ? $request->input('cobertura_color_cm2') : null;
                $detalle->cobertura_barniz_cm2 = (trim($request->input('cobertura_barniz_cm2')) != '') ? $request->input('cobertura_barniz_cm2') : null;
                $detalle->cuchillos_gomas = (trim($request->input('cuchillos_gomas')) != '') ? $request->input('cuchillos_gomas') : null;
                $detalle->cobertura_color_percent = (trim($request->input('cobertura_color_percent')) != '') ? $request->input('cobertura_color_percent') : null;
            ///

            ///Nuevos Campos evolutivo 25-01
                $detalle->ensamblado        = (trim($request->input('ensamblado')) != '') ? $request->input('ensamblado') : null;
                $detalle->desgajado_cabezal = (trim($request->input('desgajado_cabezal')) != '') ? $request->input('desgajado_cabezal') : null;
            ///


        } else if ($tipo_detalle_id == 2) { //Esquinero
            $detalle->product_type_id = 21;
            $detalle->largo_esquinero = request('largo_esquinero');
            $detalle->carton_esquinero_id = request('carton_esquinero_id');
            $detalle->cantidad = request('cantidad_esquinero');
            $detalle->numero_colores = request('numero_colores_esquinero');
            $detalle->funda_esquinero = request('funda_esquinero');
            $detalle->tipo_destino_esquinero = request('tipo_destino_esquinero');
            $detalle->tipo_camion_esquinero = request('tipo_camion_esquinero');
            $detalle->clisse = request('clisse_esquinero');
            $detalle->maquila = request('maquila_esquinero');

            $detalle->rubro_id = 5;
            $indice_complejidad = 3;
        }
        // else if ($tipo_detalle_id == 3) { //Offset
        // } else if ($tipo_detalle_id == 4) { //Pulpa
        // }

        $volumen = $detalle->area_hc * $detalle->cantidad;
        //$margen_sugerido = calcularMargenSugerido($detalle);
        ///Se Valida si es vendedor externo para obtener el margen sugerido
        if(Auth()->user()->isVendedorExterno()){
            $cliente=Client::where('id',Auth()->user()->cliente_id)->first();
            $margen_sugerido = $cliente->margen_minimo_vendedor_externo;
            
        }else{
            //$margen_sugerido = obtenerMargenSugerido($detalle);
            $margen_sugerido = obtenerMargenSugeridoNew($detalle);
           
        }
        $detalle->margen_sugerido = $margen_sugerido;
        $detalle->indice_complejidad = $indice_complejidad;
        $detalle->save();

        // si es multidetalle debemos retornas todos los detalles agregados
        $multidetalle = [];
        // Validar ciudades adicionales para clonar detalle
        foreach (request()->all() as $key => $input) {
            // dd($key);
            if (strpos($key, "ciudad_id") === 0 && $key != "ciudad_id") {
                $id  = str_replace("ciudad_id", "", $key);

                $newDetalle = $detalle->replicate();
                $newDetalle->ciudad_id = $input;
                $newDetalle->pallets_apilados = request("pallets_apilados" . $id);
                $newDetalle->cantidad = request("cantidad" . $id);
                $newDetalle->margen = 0;

                //$margen_sugerido = calcularMargenSugerido($detalle);
                ///Se Valida si es vendedor externo para obtener el margen sugerido
                if(Auth()->user()->isVendedorExterno()){
                    $cliente=Client::where('id',Auth()->user()->cliente_id)->first();
                    $margen_sugerido = $cliente->margen_minimo_vendedor_externo;
                    
                }else{
                    //$margen_sugerido = obtenerMargenSugerido($detalle);
                    $margen_sugerido = obtenerMargenSugeridoNew($detalle);
                    
                }

                //$margen_sugerido = obtenerMargenSugerido($detalle);
                $newDetalle->margen_sugerido = $margen_sugerido;
                $newDetalle->indice_complejidad = 3;
                $newDetalle->push();
                $multidetalle[] = $newDetalle->id;
            }
        }
        // dd($ciudades);

        // si es multidetalle debemos retornas todos los detalles agregados
        if (!empty($multidetalle)) {
            $multidetalle[] = $detalle->id;
            // dd($multidetalle);
            $detalles = DetalleCotizacion::withAll()->find($multidetalle);
            // dd($detalles);
            return response()->json($detalles);
        } else {
            $detalle = DetalleCotizacion::withAll()->find($detalle->id);
            // dd($detalle);

            return response()->json($detalle);
        }
    }

    public function update(Request $request)
    {
       
        $request->validate([
            // Datos Comerciales
            'detalle_id' => 'required',
        ]);
        $detalle_id = request('detalle_id');
        $planta_id = request('planta_id');
        $margen = request('margen');
        $margen = str_replace(',', '.', str_replace('.', '', $margen));

        $detalle = DetalleCotizacion::find($detalle_id);
        
        if ($planta_id) {
            $detalle->planta_id = $planta_id;

            //$margen_sugerido = calcularMargenSugerido($detalle);
            ///Se Valida si es vendedor externo para obtener el margen sugerido
            /*if(Auth()->user()->isVendedorExterno()){
                $cliente=Client::where('id',Auth()->user()->cliente_id)->first();
                $margen_sugerido = $cliente->margen_minimo_vendedor_externo;
                
            }else{
                //$margen_sugerido = obtenerMargenSugerido($detalle);
                $margen_sugerido = obtenerMargenSugeridoNew($detalle);
                
            }

            $detalle->margen_sugerido = $margen_sugerido;*/
            // // Si la planta es osorno el margen sugerido aumenta en 35 dolares
            // $indice_complejidad = ($detalle->tipo_detalle_id == 1) ? $this->calcularIndiceComplejidad($detalle) : 3;
            // $volumen = $detalle->area_hc * $detalle->cantidad;
            // $tarifario = TarifarioMargen::where("rubro_id", $detalle->rubro_id)->where("tipo_cliente", "A")->where("indice_complejidad", $indice_complejidad)
            //     ->where('volumen_negociacion_minimo_2', '<=', $volumen)->where('volumen_negociacion_maximo_2', '>=', $volumen)->first();
            // // dd($tarifario);
            // if ($planta_id == 3) {
            //     $detalle->margen_sugerido = $tarifario ? $tarifario->margen_minimo_usd_mm2 + 35 : 0;
            // } else {

            //     $detalle->margen_sugerido = $tarifario ? $tarifario->margen_minimo_usd_mm2 : 0;
            // }
        }
        if (($margen || $margen == "0") && $margen < 9999) {
            $detalle->margen = $margen;

        }
       
        $detalle->save();
        

        $detalle = DetalleCotizacion::withAll()->find($detalle->id);


        return response()->json($detalle);
    }

    public function editarMargenCotizacion(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            // Datos Comerciales
            'detalle_id' => 'required',
            'monto' => 'required',
            'variable' => 'required',
        ]);
        $detalle_id = request('detalle_id');
        $monto = request('monto');
        $variable = request('variable');
        $margen = 0;

        $detalle = DetalleCotizacion::find($detalle_id);
        $margen_calculo = $detalle->margen;
        $monto = str_replace(',', '.', str_replace('.', '', $monto));
        // dd($monto);
        switch ($variable) {
            case 'usd_mm2':
                // dd($detalle);
                // dd($monto - $detalle->precios->costo_total["usd_mm2"], $monto, $detalle->precios->costo_total["usd_mm2"]);
                $margen = $monto - $detalle->precios->costo_total["usd_mm2"];
                break;
            case 'usd_caja':
                // dd($monto, $detalle->area_hc, $variable, $monto * $detalle->area_hc / 1000, $monto / $detalle->largo_esquinero);
                if ($detalle->tipo_detalle_id == 1) {
                    $margen = ($monto - $detalle->precios->costo_total["usd_caja"]) * 1000 / $detalle->area_hc;
                   // $monto = $monto / $detalle->area_hc * 1000;
                } elseif ($detalle->tipo_detalle_id == 2) {
                    $monto = $monto / $detalle->largo_esquinero / ($detalle->carton_esquinero["ancho_esquinero"] / 100) * 1000;
                    $margen = $monto - $detalle->precios->costo_total["usd_mm2"];
                }
                // dd($monto, $monto - $detalle->precios->costo_total["usd_mm2"]);
                
                break;
            case 'clp_caja':
                // dd($monto);
                if ($detalle->tipo_detalle_id == 1) {
                    
                    $margen=($monto / $detalle->precio_dolar - $detalle->precios->costo_total["usd_caja"]) *1000 / $detalle->area_hc;
                                      
                } elseif ($detalle->tipo_detalle_id == 2) {
                    $monto = $monto / $detalle->largo_esquinero / ($detalle->carton_esquinero["ancho_esquinero"] / 100) * 1000;
                    $monto = ($monto / $detalle->precio_dolar);
                    $margen = $monto - $detalle->precios->costo_total["usd_mm2"];
                }
               // echo($detalle->precios->costo_total["usd_caja"]);
               // echo($detalle->area_hc);
             
                break;

            default:
                # code...
                break;
        }

        if ($monto && $monto < 9999) {
            // dd($monto);
            $detalle->margen = $margen;
        }

        $detalle->save();

        $detalle = DetalleCotizacion::withAll()->find($detalle->id);


        return response()->json($detalle);
    }

    public function delete(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            // Datos Comerciales
            'detalle_id' => 'required',
        ]);
        $detalle_id = request('detalle_id');

        DetalleCotizacion::find($detalle_id)->delete();

        return response()->json($detalle_id);
    }

    public function detalleCotizacionGanado(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            // Datos Comerciales
            'detalle_id' => 'required',
        ]);
        $detalle_id = request('detalle_id');

        DetalleCotizacion::findOrFail($detalle_id)->update(['estado' => 1]);

        return response()->json($detalle_id);
    }

    public function detalleCotizacionPerdido(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            // Datos Comerciales
            'detalle_id' => 'required',
        ]);
        $detalle_id = request('detalle_id');

        DetalleCotizacion::findOrFail($detalle_id)->update(['estado' => 2]);

        return response()->json($detalle_id);
    }

    public function getServiciosMaquila()
    {
        // dd(request()->all());
        if (!empty($_GET['tipo_producto_id'])) {

            $product_type = $_GET['tipo_producto_id'];

            // Producto : Plancha
            if ($product_type == 16) {

                //Servicio : Paletizado Placas
                $servicios = MaquilaServicio::where('active', 1)->where('id', '18')->pluck('servicio', 'id')->toArray();

            // Producto :  Caja - Fondo - Tapa - Panel perimetral
            } else if ($product_type == 3 || $product_type == 4 || $product_type == 5  || $product_type == 15) {

                //Servicio : PM CJ Chica  entre 0 y  30 Cm - PM CJ Grande  entre 70 y  100 Cm - PM CJ Mediana  entre 30 y  70 Cm
                $servicios = MaquilaServicio::where('active', 1)->whereIN('id', ['15','16','17'])->pluck('servicio', 'id')->toArray();

            // Producto : Caja Bipartida -
            }else if ($product_type == 31) {

                //Servicio : Pegado Especial
                $servicios = MaquilaServicio::where('active', 1)->where('id', '21')->pluck('servicio', 'id')->toArray();

            // Producto : Set de tabique - Tabique- Tabique Corto- Tabique Largo
            }else if ($product_type == 18 || $product_type == 33 || $product_type == 20 || $product_type == 19) {

                //Servicio : Armado y Paletizado Tabiques  Doble - Armado y Paletizado Tabiques Simple
                $servicios = MaquilaServicio::where('active', 1)->whereIN('id', ['19','20'])->pluck('servicio', 'id')->toArray();
            
            // Producto : Cuerpo - Bandeja - Interconector - Tapa Pallet 
            }else if ($product_type == 6 || $product_type == 28 || $product_type == 10 || $product_type == 34) {

                //Servicio : Desgaje Unitario
                $servicios = MaquilaServicio::where('active', 1)->where('id', '14')->pluck('servicio', 'id')->toArray();
            
            // Producto : Cabezal -
            }else if ($product_type == 8) {

                //Servicio : Desgaje Cabezal Par - Desgaje Unitario
                $servicios = MaquilaServicio::where('active', 1)->whereIN('id', ['13','14'])->pluck('servicio', 'id')->toArray();
        
            // Producto : Hoja Sencilla - Hoja Rayada - Hoja  Troquelada - Hoja Die-Cutter
            }else if ($product_type == 13 || $product_type == 12 || $product_type == 32  || $product_type == 11) {

                //Servicio : Paletizado Placas - Desgaje Unitario
                $servicios = MaquilaServicio::where('active', 1)->whereIN('id', ['14', '18'])->pluck('servicio', 'id')->toArray();
        
            } else {
                return "";
            }


            $html = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimple($servicios, 'id');
            return $html;
            
        }
        return "";

    }

    public function cargaMasivaDetalles(Request $request)
    {
        // $request->validate(
        //     [
        //         'archivo'      => $request->archivo,
        //         'extension' => strtolower($request->archivo->getClientOriginalExtension()),
        //     ],
        //     [
        //         'archivo'          => 'required',
        //         'extension'      => 'required|in:xlsx,xls,csv',
        //     ]
        // );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::selectSheetsByIndex(0)->load($path, false, 'ISO-8859-1');
        // dd($data, $data->get());
        $titulo = $data->getSheetNames()[0];
        $data = $data->get();
        if ($titulo != "CORRUGADO" && $titulo != "ESQUINERO") {
            return response()->json([
                'mensaje' => "Archivo no contiene Hoja de Corrugado o Esquinero"
            ], 404);
        }

        $plantas = Planta::pluck('id', 'nombre')->toArray();
        $ciudades = CiudadesFlete::pluck('id', 'ciudad')->toArray();
        $rubros = Rubro::where('id', "!=", 5)->pluck('id', 'descripcion')->toArray();
        $productTypes = ProductType::where('cotiza',1)->where('active', 1)->pluck('id', 'descripcion')->toArray();
        $maquilaServicios = MaquilaServicio::where('active', 1)->pluck('id', 'servicio')->toArray();
        $procesos = Process::where('active', 1)->whereNotIn('id', [6, 8])->orderBy("descripcion")->pluck('id', 'descripcion')->toArray();
        $printTypes = PrintType::where('active', 1)->orderBy("descripcion")->pluck('id', 'descripcion')->toArray();
        $cartons = Carton::where('active', 1)->pluck('id', 'codigo')->toArray();
        $cartones_offset = Carton::whereIn("tipo", ["DOBLE MONOTAPA", "SIMPLE EMPLACADO"])->where("active", 1)->pluck('id', 'codigo')->toArray();
        $cartonesEsquinero = CartonEsquinero::where('active', 1)->pluck('id', 'codigo')->toArray();
        $tipo_destino = ["Tarima Nacional" => 1, "Empaque Exportación (Granel)" => 2,  "Tarima de Exportación" => 2];
        $tipo_camion = ["Camión 7x2,6mts" => 1, "Camión 12x2,6mts" => 2];
       
        $precio_dolar_actual = VariablesCotizador::select('precio_dolar')->pluck('precio_dolar')->first();

        ///Nuevos Campos evolutivo 24-01 - Inicio
            //Maquinas Impresoras
            $printingMachines = PrintingMachine::where('deleted', 0)->pluck('id', 'descripcion')->toArray();
            //Pegados
            $pegados = Pegado::where('active', 1)->where('codigo','<>', 0)->pluck('id', 'descripcion')->toArray();
            //Altura Pallets
            $alturaPallets = PalletHeight::where('deleted', 0)->pluck('id', 'descripcion')->toArray();
            //tipos Barniz
            $tiposBarniz = TipoBarniz::where('active', 1)->pluck('id', 'descripcion')->toArray();
            //Zunchos
            $zunchos = Zuncho::where('active', 1)->pluck('id', 'descripcion')->toArray();
            //Pallet
            $pallets = Pallet::where('active', 1)->pluck('id', 'descripcion')->toArray();
        ///Nuevos Campos evolutivo 24-01 - Fin


        $detallesInvalidos = [];
        $detallesID = [];
        $detallesAux = [];
        $indice_aux=0;
        $codigo_carga=Auth::user()->id.date('ymdhis');
       
        if ($data->count()) {
            $tipo_detalle_id = ($titulo == "CORRUGADO") ? 1 : 2;
            foreach ($data as $key => $row) {
                // Validar filas vacias
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
               
                $motivos = [];

                // Validaciones generales
                $planta = array_key_exists($row->planta, $plantas) ? $plantas[$row->planta] : false;
                $cantidad = is_numeric($row->cantidad) ? $row->cantidad : false;
                $numero_colores = is_numeric($row->colores) ? $row->colores : false;

                $margen = is_numeric($row->margen) ? $row->margen : false;
                $destino = array_key_exists($row->lugar_destino, $ciudades) ? $ciudades[$row->lugar_destino] : false;

                if (!$planta) $motivos[] = " Planta";
                if (!$destino) $motivos[] = " Ciudad Destino";
                if (!$cantidad) $motivos[] = " Cantidad";
                if (!$numero_colores && $numero_colores != 0) $motivos[] = " Colores";
                if (!$margen) $motivos[] = " Margen";

                // Validaciones especificas CORRUGADO 
                if ($tipo_detalle_id == 1) {

                    $rubro = array_key_exists($row->rubro, $rubros) ? $rubros[$row->rubro] : false;
                    $proceso = array_key_exists($row->proceso, $procesos) ? $procesos[$row->proceso] : false;
                    $print_type = array_key_exists($row->impresion, $printTypes) ? $printTypes[$row->impresion] : false;
                    $proceso_desc =$row->proceso;
                   
                    $tipo_producto = array_key_exists($row->item, $productTypes) ? $productTypes[$row->item] : false;
                    $tipo_producto_desc =$row->item;
                    // validar si proceso es offset solo carton de offset
                    /*  
                    if ($proceso && ($proceso == 7 || $proceso == 9)) {
                        $carton = array_key_exists($row->carton, $cartones_offset) ? $cartones_offset[$row->carton] : false;

                        // Si el proceso es offset validar datos de cartulina/offset
                        $ancho_pliego_cartulina_mm = is_numeric($row->ancho_pliego_cartulina_mm) ? $row->ancho_pliego_cartulina_mm : false;
                        $largo_pliego_cartulina_un = is_numeric($row->largo_pliego_cartulina_un) ? $row->largo_pliego_cartulina_un : false;
                        $precio_pliego_cartulina_un = is_numeric($row->precio_pliego_cartulina_un) ? $row->precio_pliego_cartulina_un : false;
                        $precio_impresion_pliego_un = is_numeric($row->precio_impresion_pliego_un) ? $row->precio_impresion_pliego_un : false;
                        $gp_emplacado_ungp = is_numeric($row->gp_emplacado_ungp) ? $row->gp_emplacado_ungp : false;
                    } else {
                    */
                    $carton = array_key_exists($row->carton, $cartons) ? $cartons[$row->carton] : false;
                    // }
                    $carton_desc=$row->carton;

                    $anchura = is_numeric($row->anchura_mm) ? $row->anchura_mm : false;
                    $largura = is_numeric($row->largura_mm) ? $row->largura_mm : false;
                    $area_hc = is_numeric($row->area_m2) ? $row->area_m2 : false;
                    $un_x_golpe_al_largo = is_numeric($row->un_x_golpe_al_largo) ? $row->un_x_golpe_al_largo : false;
                    $un_x_golpe_al_ancho = is_numeric($row->un_x_golpe_al_ancho) ? $row->un_x_golpe_al_ancho : false;
                    //$cobertura = is_numeric($row->cobertura) ? $row->cobertura : false;
                    $cera_exterior = is_numeric($row->cera_exterior) ? $row->cera_exterior : false;
                    $cera_interior = is_numeric($row->cera_interior) ? $row->cera_interior : false;

                    $valor_armado = is_numeric($row->valor_armado) ? $row->valor_armado : false;

                    ///Nuevos Campos evolutivo 24-01 - Inicio
                       // dd($row->maquina_impresora,$printingMachines,array_key_exists(trim($row->maquina_impresora), $printingMachines));
                        $printing_machine_id = array_key_exists($row->maquina_impresora, $printingMachines) ? $printingMachines[$row->maquina_impresora] : false;
                        $cobertura_color_percent = is_numeric($row->cobertura_color) ? $row->cobertura_color : false;
                        $cobertura_barniz_cm2 = is_numeric($row->cobertura_barniz_cm2) ? $row->cobertura_barniz_cm2 : false;
                        $barniz_type_id = array_key_exists($row->tipo_barniz, $tiposBarniz) ? $tiposBarniz[$row->tipo_barniz] : false;
                        $pegado_id = array_key_exists($row->tipo_pegado, $pegados) ? $pegados[$row->tipo_pegado] : false;
                        $pallet_height_id = array_key_exists(intval($row->altura_pallet), $alturaPallets) ? $alturaPallets[intval($row->altura_pallet)] : false;
                        $cuchillos_gomas = is_numeric($row->cuchillos_y_gomas_m) ? $row->cuchillos_y_gomas_m : false;
                        $pallet = array_key_exists($row->pallet, $pallets) ? $pallets[$row->pallet] : false;
                        $zuncho = array_key_exists($row->zuncho, $zunchos) ? $zunchos[$row->zuncho] : false;
                        $cobertura_color_cm2 = is_numeric($row->clisse_por_golpe_cm2) ? $row->clisse_por_golpe_cm2 : false;
                    ///Nuevos Campos evolutivo 24-01 - Fin
                   // dd($row->tipo_barniz,$tiposBarniz,$tiposBarniz[$row->tipo_barniz]);
                    if (!$tipo_producto) $motivos[] = " Item";
                    if (!$rubro) $motivos[] = " Rubro";
                    if (!$proceso) $motivos[] = " Proceso";
                    if (!$carton) $motivos[] = " Cartón";
                    if (!$printing_machine_id) $motivos[] = " Maquina Impresora";
                    if (!$anchura) $motivos[] = " Anchura";
                    if (!$largura) $motivos[] = " Largura";
                    if (!$area_hc) $motivos[] = " Area";
                    if (!$un_x_golpe_al_largo) $motivos[] = " Golpe al Largo";
                    if (!$un_x_golpe_al_ancho) $motivos[] = " Golpe al Ancho";
                    //if (!$cobertura && $cobertura != 0) $motivos[] = " Cobertura";
                    if (!$cera_exterior && $cera_exterior != 0) $motivos[] = " Cera Exterior";
                    if (!$cera_interior && $cera_interior != 0) $motivos[] = " Cera Interior";
                    if (!$valor_armado && $valor_armado != 0) $motivos[] = " Valor Armado";
                    if (!$cobertura_color_cm2 && $cobertura_color_cm2 != 0) $motivos[] = " Cobertura Color %";
                    if (!$cobertura_barniz_cm2 && $cobertura_barniz_cm2 != 0) $motivos[] = " Cobertura Barniz CM2";
                    if (!$cuchillos_gomas && $cuchillos_gomas != 0) $motivos[] = " Cuchillos y Gomas M";
                    if (!$cobertura_color_percent && $cobertura_color_percent != 0) $motivos[] = " Clisse por un golpe CM2";
                    // validar si proceso es offset solo carton de offset
                   /* if ($proceso && ($proceso == 7 || $proceso == 9)) {
                        if (!$ancho_pliego_cartulina_mm) $motivos[] = " Ancho Pliego Cartulina";
                        if (!$largo_pliego_cartulina_un) $motivos[] = " Largo Pliego Cartulina";
                        if (!$precio_pliego_cartulina_un) $motivos[] = " Precio Pliego Cartulina";
                        if (!$precio_impresion_pliego_un) $motivos[] = " Precio Impresion Pliego";
                        if (!$gp_emplacado_ungp) $motivos[] = " GP Emplagado";
                    }*/
                }

                // Validaciones especificas Esquinero
                elseif ($tipo_detalle_id == 2) {
                    // dd($row, $row->carton, $cartonesEsquinero);
                    $carton = array_key_exists($row->carton, $cartonesEsquinero) ? $cartonesEsquinero[$row->carton] : false;
                    $tipo_destino_palletizado = array_key_exists($row->destino, $tipo_destino) ? $tipo_destino[$row->destino] : false;
                    $camion = array_key_exists($row->transporte, $tipo_camion) ? $tipo_camion[$row->transporte] : false;


                    $largo_esquinero = is_numeric($row->largo_o_medida) ? $row->largo_o_medida : false;


                    if (!$carton) $motivos[] = " Cartón";
                    if (!$tipo_destino_palletizado) $motivos[] = "Tipo Destino";
                    if (!$camion) $motivos[] = " Transporte";
                    if (!$largo_esquinero) $motivos[] = " Largo o Medida";
                }
                //dd($motivos);
                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $detalleErroneo = new stdClass();
                    $detalleErroneo->linea = $key + 2;
                    $detalleErroneo->motivos = $motivos;
                    $detallesInvalidos[] = $detalleErroneo;
                    continue;
                }
                // Creacion de Corrugado
                if ($tipo_detalle_id == 1) {

                    // if (isset($row->unidad_bct)) {
                    //     if (
                    //         strtolower(trim("lb")) == strtolower(trim($row->unidad_bct)) ||
                    //         strtolower(trim("libras")) == strtolower(trim($row->unidad_bct)) ||
                    //         strtolower(trim("lbs")) == strtolower(trim($row->unidad_bct)) ||
                    //         strtolower(trim("lib")) == strtolower(trim($row->unidad_bct))
                    //     ) {

                    //         $unidadBCT =  0;
                    //     } else if (
                    //         strtolower(trim("kg")) == strtolower(trim($row->unidad_bct)) ||
                    //         strtolower(trim("kgs")) == strtolower(trim($row->unidad_bct)) ||
                    //         strtolower(trim("kilog")) == strtolower(trim($row->unidad_bct)) ||
                    //         strtolower(trim("kilogramo")) == strtolower(trim($row->unidad_bct)) ||
                    //         strtolower(trim("kilogramos")) == strtolower(trim($row->unidad_bct))
                    //     ) {

                    //         $unidadBCT =  1;
                    //     }
                    // }
                    /*$detalle = new DetalleCotizacion([
                        'tipo_detalle_id' => $tipo_detalle_id,
                        'planta_id' => $planta,
                        'cantidad' => $cantidad,
                        'ciudad_id' => $destino,
                        "numero_colores" => $numero_colores,
                        'product_type_id' => $tipo_producto,
                        'process_id' => $proceso,
                        'carton_id' => $carton,
                        'area_hc' => $area_hc,
                        'anchura' => $anchura,
                        'largura' => $largura,
                        'golpes_largo' => $un_x_golpe_al_largo,
                        'golpes_ancho' => $un_x_golpe_al_ancho,
                        "impresion" => isset($cobertura) ? $cobertura : 0,
                        "cinta_desgarro" => isset($row->cinta_desgarro) &&  strtolower(trim("SI")) == strtolower(trim($row->cinta_desgarro)) ? 1 : 0,
                        "codigo_cliente" => isset($row->cod_interno_cliente) ? $row->cod_interno_cliente : null,
                        "porcentaje_cera_interno" => isset($cera_interior) ? $cera_interior : 0,
                        "porcentaje_cera_externo" => isset($cera_exterior) ? $cera_exterior : 0,
                        "rubro_id" => $rubro,
                        "matriz" => isset($row->matriz) &&  strtolower(trim("SI")) == strtolower(trim($row->matriz)) ? 1 : 0,
                        "clisse" => isset($row->cliches) &&  strtolower(trim("SI")) == strtolower(trim($row->cliches)) ? 1 : 0,
                        "royalty" => isset($row->royalty) &&  strtolower(trim("SI")) == strtolower(trim($row->royalty)) ? 1 : 0,
                        "maquila" => isset($row->maquila) && $row->maquila != 0 &&  array_key_exists($row->maquila, $maquilaServicios) ? 1 : 0,
                        "maquila_servicio_id" => isset($row->maquila) && $row->maquila != 0 && array_key_exists($row->maquila, $maquilaServicios) ? $maquilaServicios[$row->maquila] : 0,
                        "armado_automatico" => isset($row->valor_armado) &&  strtolower(trim("no")) != strtolower(trim($row->valor_armado)) && is_numeric($row->valor_armado) && $row->valor_armado > 0  ? 1 : 0,
                        "armado_usd_caja" => isset($row->valor_armado) && is_numeric($row->valor_armado) ? $row->valor_armado : 0,
                        "pallet" => isset($row->pallet) &&  strtolower(trim("SI")) == strtolower(trim($row->pallet)) ? 1 : 0,
                        "zuncho" => isset($row->zuncho) &&  strtolower(trim("SI")) == strtolower(trim($row->zuncho)) ? 1 : 0,
                        "funda" => isset($row->funda) &&  strtolower(trim("SI")) == strtolower(trim($row->funda)) ? 1 : 0,
                        "stretch_film" => isset($row->stretch_film) &&  strtolower(trim("SI")) == strtolower(trim($row->stretch_film)) ? 1 : 0,
                        "ancho_pliego_cartulina" => isset($ancho_pliego_cartulina_mm) ? $ancho_pliego_cartulina_mm  : 0,
                        "largo_pliego_cartulina" => isset($largo_pliego_cartulina_un) ? $largo_pliego_cartulina_un  : 0,
                        "precio_pliego_cartulina" => isset($precio_pliego_cartulina_un) ? $precio_pliego_cartulina_un  : 0,
                        "precio_impresion_pliego" => isset($precio_impresion_pliego_un) ? $precio_impresion_pliego_un  : 0,
                        "gp_emplacado" => isset($gp_emplacado_ungp) ? $gp_emplacado_ungp  : 0,
                        "margen" => $margen,
                        "cotizacion_id" => 0,
                        "variable_cotizador_id" => 1,
                        "codigo_material_detalle" => isset($row->codigo_material) ? $row->codigo_material  : null,
                        "descripcion_material_detalle" => isset($row->descripcion) ? $row->descripcion  : null,
                        "cad_material_detalle" => isset($row->cad) ? $row->cad  : null,
                        "largo" => isset($row->largo) && is_numeric($row->largo) ? $row->largo  : null,
                        "ancho" => isset($row->ancho) && is_numeric($row->ancho) ? $row->ancho  : null,
                        "alto" => isset($row->alto) && is_numeric($row->alto) ? $row->alto  : null,
                        "bct_min_lb" => isset($row->bct_min_lb) && is_numeric($row->bct_min_lb) ? $row->bct_min_lb  : null,
                        "bct_min_kg" => isset($row->bct_min_lb) && is_numeric($row->bct_min_lb) ? (int) ($row->bct_min_lb * 0.4535)  : null,
                        // "unidad_medida_bct" => isset($unidadBCT) ? $unidadBCT  : null,



                        // id, tipo_detalle_id, cantidad, product_type_id, numero_colores, area_hc, anchura, largura, carton_id, impresion, golpes_largo, golpes_ancho, process_id, cinta_desgarro, pegado_terminacion, codigo_cliente, porcentaje_cera_interno, porcentaje_cera_externo, rubro_id, subsubhierarchy_id, matriz, clisse, royalty, maquila, maquila_servicio_id, armado_automatico, armado_usd_caja, zuncho, funda, stretch_film, ancho_pliego_cartulina, largo_pliego_cartulina, precio_pliego_cartulina, precio_impresion_pliego, gp_emplacado, largo_esquinero, carton_esquinero_id, funda_esquinero, tipo_destino_esquinero, tipo_camion_esquinero, margen, planta_id, cotizacion_id, variable_cotizador_id, created_at, updated_at
                    ]);*/
                    //dd($row->maquila,isset($row->maquila),($row->maquila != '0'), array_key_exists($row->maquila, $maquilaServicios));
                    $detallesInsert[$indice_aux]= [
                        'tipo_detalle_id' => $tipo_detalle_id,
                        'planta_id' => $planta,
                        'cantidad' => $cantidad,
                        'ciudad_id' => $destino,
                        "numero_colores" => $numero_colores,
                        'product_type_id' => $tipo_producto,
                        //'product_type_desc' => $tipo_producto_desc,
                        'process_id' => $proceso,
                        'print_type_id'=>$print_type,
                        //'process_desc' => $proceso_desc,
                        'carton_id' => $carton,
                        //'carton_desc' => $carton_desc,
                        'area_hc' => $area_hc,
                        'anchura' => $anchura,
                        'largura' => $largura,
                        'golpes_largo' => $un_x_golpe_al_largo,
                        'golpes_ancho' => $un_x_golpe_al_ancho,
                        "impresion" => isset($cobertura) ? $cobertura : 0,
                        "cinta_desgarro" => isset($row->cinta_desgarro) &&  strtolower(trim("SI")) == strtolower(trim($row->cinta_desgarro)) ? 1 : 0,
                        "codigo_cliente" => isset($row->cod_interno_cliente) ? $row->cod_interno_cliente : null,
                       // "porcentaje_cera_interno" => isset($cera_interior) ? $cera_interior : 0,
                       // "porcentaje_cera_externo" => isset($cera_exterior) ? $cera_exterior : 0,
                        "rubro_id" => $rubro,
                        "matriz" => isset($row->matriz) &&  strtolower(trim("SI")) == strtolower(trim($row->matriz)) ? 1 : 0,
                        "clisse" => isset($row->clisse) &&  strtolower(trim("SI")) == strtolower(trim($row->clisse)) ? 1 : 0,
                        "royalty" => isset($row->royalty) &&  strtolower(trim("SI")) == strtolower(trim($row->royalty)) ? 1 : 0, 
                        "maquila" => (isset($row->maquila) && trim($row->maquila) != '' && $row->maquila != 'Sin Maquila')? 1 : 0, //&&  array_key_exists($row->maquila, $maquilaServicios) ? 1 : 0,
                        "maquila_servicio_id" => isset($row->maquila) && trim($row->maquila != '') && $row->maquila != 'Sin Maquila' && array_key_exists($row->maquila, $maquilaServicios) ? $maquilaServicios[$row->maquila] : 0,
                        "armado_automatico" => isset($row->valor_armado) &&  strtolower(trim("no")) != strtolower(trim($row->valor_armado)) && is_numeric($row->valor_armado) && $row->valor_armado > 0  ? 1 : 0,
                        "armado_usd_caja" => isset($row->valor_armado) && is_numeric($row->valor_armado) ? $row->valor_armado : 0,
                        //"pallet" => isset($row->pallet) &&  strtolower(trim("SI")) == strtolower(trim($row->pallet)) ? 1 : 0,
                        //"zuncho" => isset($row->zuncho) &&  strtolower(trim("SI")) == strtolower(trim($row->zuncho)) ? 1 : 0,
                        "funda" => isset($row->funda) &&  strtolower(trim("SI")) == strtolower(trim($row->funda)) ? 1 : 0,
                        "stretch_film" => isset($row->stretch_film) &&  strtolower(trim("SI")) == strtolower(trim($row->stretch_film)) ? 1 : 0,
                       // "ancho_pliego_cartulina" => isset($ancho_pliego_cartulina_mm) ? $ancho_pliego_cartulina_mm  : 0,
                       // "largo_pliego_cartulina" => isset($largo_pliego_cartulina_un) ? $largo_pliego_cartulina_un  : 0,
                       // "precio_pliego_cartulina" => isset($precio_pliego_cartulina_un) ? $precio_pliego_cartulina_un  : 0,
                       // "precio_impresion_pliego" => isset($precio_impresion_pliego_un) ? $precio_impresion_pliego_un  : 0,
                       // "gp_emplacado" => isset($gp_emplacado_ungp) ? $gp_emplacado_ungp  : 0,
                        "margen" => $margen,
                        "cotizacion_id" => $codigo_carga,
                        "variable_cotizador_id" => 1,
                        "codigo_material_detalle" => isset($row->codigo_material) ? $row->codigo_material  : null,
                        "descripcion_material_detalle" => isset($row->descripcion) ? $row->descripcion  : null,
                        "cad_material_detalle" => isset($row->cad) ? $row->cad  : null,
                        "largo" => isset($row->largo) && is_numeric($row->largo) ? $row->largo  : null,
                        "ancho" => isset($row->ancho) && is_numeric($row->ancho) ? $row->ancho  : null,
                        "alto" => isset($row->alto) && is_numeric($row->alto) ? $row->alto  : null,
                        "bct_min_lb" => isset($row->bct_min_lb) && is_numeric($row->bct_min_lb) ? $row->bct_min_lb  : null,
                        "bct_min_kg" => isset($row->bct_min_lb) && is_numeric($row->bct_min_lb) ? (int) ($row->bct_min_lb * 0.4535)  : null,
                        "detalle_valor_dolar"=>$precio_dolar_actual,                       
                        // "unidad_medida_bct" => isset($unidadBCT) ? $unidadBCT  : null,
                        // id, tipo_detalle_id, cantidad, product_type_id, numero_colores, area_hc, anchura, largura, carton_id, impresion, golpes_largo, golpes_ancho, process_id, cinta_desgarro, pegado_terminacion, codigo_cliente, porcentaje_cera_interno, porcentaje_cera_externo, rubro_id, subsubhierarchy_id, matriz, clisse, royalty, maquila, maquila_servicio_id, armado_automatico, armado_usd_caja, zuncho, funda, stretch_film, ancho_pliego_cartulina, largo_pliego_cartulina, precio_pliego_cartulina, precio_impresion_pliego, gp_emplacado, largo_esquinero, carton_esquinero_id, funda_esquinero, tipo_destino_esquinero, tipo_camion_esquinero, margen, planta_id, cotizacion_id, variable_cotizador_id, created_at, updated_at
                      
                        ///Nuevos Campos evolutivo 24-01 - Inicio
                            "cobertura_color_cm2"   => isset($cobertura_color_cm2) ? $cobertura_color_cm2 : 0,
                            "cobertura_barniz_cm2"  => isset($cobertura_barniz_cm2) ? $cobertura_barniz_cm2 : 0,
                            "cuchillos_gomas"       => isset($cuchillos_gomas) ? $cuchillos_gomas : 0,
                            "printing_machine_id"   => $printing_machine_id,
                            "barniz_type_id"        => $barniz_type_id,
                            "barniz"                => ($barniz_type_id)? 1 : 0,
                            "pegado_id"             => $pegado_id,
                            "pallet_height_id"      => $pallet_height_id,
                            "pallet"                => $pallet,
                            "zuncho"                => $zuncho,
                            "cobertura_color_percent"   => isset($cobertura_color_percent) ? $cobertura_color_percent : 0,
                            "pallets_apilados" => ($pallet_height_id == 1) ? 2 : 1,
                        ///Nuevos Campos evolutivo 24-01 - Fin
                    ];
                   // dd($detallesInsert);
                    /*$detallesAux [$indice_aux]= [
                        'tipo_detalle_id' => $tipo_detalle_id,
                        'planta_id' => $planta,
                        'cantidad' => $cantidad,
                        'ciudad_id' => $destino,
                        "numero_colores" => $numero_colores,
                        'product_type_id' => $tipo_producto,
                        'product_type_desc' => $tipo_producto_desc,
                        'process_id' => $proceso,
                        'process_desc' => $proceso_desc,
                        'carton_id' => $carton,
                        'carton_desc' => $carton_desc,
                        'area_hc' => $area_hc,
                        'anchura' => $anchura,
                        'largura' => $largura,
                        'golpes_largo' => $un_x_golpe_al_largo,
                        'golpes_ancho' => $un_x_golpe_al_ancho,
                        "impresion" => isset($cobertura) ? $cobertura : 0,
                        "cinta_desgarro" => isset($row->cinta_desgarro) &&  strtolower(trim("SI")) == strtolower(trim($row->cinta_desgarro)) ? 1 : 0,
                        "codigo_cliente" => isset($row->cod_interno_cliente) ? $row->cod_interno_cliente : null,
                        "porcentaje_cera_interno" => isset($cera_interior) ? $cera_interior : 0,
                        "porcentaje_cera_externo" => isset($cera_exterior) ? $cera_exterior : 0,
                        "rubro_id" => $rubro,
                        "matriz" => isset($row->matriz) &&  strtolower(trim("SI")) == strtolower(trim($row->matriz)) ? 1 : 0,
                        "clisse" => isset($row->cliches) &&  strtolower(trim("SI")) == strtolower(trim($row->cliches)) ? 1 : 0,
                        "royalty" => isset($row->royalty) &&  strtolower(trim("SI")) == strtolower(trim($row->royalty)) ? 1 : 0,
                        "maquila" => isset($row->maquila) && $row->maquila != 0 &&  array_key_exists($row->maquila, $maquilaServicios) ? 1 : 0,
                        "maquila_servicio_id" => isset($row->maquila) && $row->maquila != 0 && array_key_exists($row->maquila, $maquilaServicios) ? $maquilaServicios[$row->maquila] : 0,
                        "armado_automatico" => isset($row->valor_armado) &&  strtolower(trim("no")) != strtolower(trim($row->valor_armado)) && is_numeric($row->valor_armado) && $row->valor_armado > 0  ? 1 : 0,
                        "armado_usd_caja" => isset($row->valor_armado) && is_numeric($row->valor_armado) ? $row->valor_armado : 0,
                        "pallet" => isset($row->pallet) &&  strtolower(trim("SI")) == strtolower(trim($row->pallet)) ? 1 : 0,
                        "zuncho" => isset($row->zuncho) &&  strtolower(trim("SI")) == strtolower(trim($row->zuncho)) ? 1 : 0,
                        "funda" => isset($row->funda) &&  strtolower(trim("SI")) == strtolower(trim($row->funda)) ? 1 : 0,
                        "stretch_film" => isset($row->stretch_film) &&  strtolower(trim("SI")) == strtolower(trim($row->stretch_film)) ? 1 : 0,
                        "ancho_pliego_cartulina" => isset($ancho_pliego_cartulina_mm) ? $ancho_pliego_cartulina_mm  : 0,
                        "largo_pliego_cartulina" => isset($largo_pliego_cartulina_un) ? $largo_pliego_cartulina_un  : 0,
                        "precio_pliego_cartulina" => isset($precio_pliego_cartulina_un) ? $precio_pliego_cartulina_un  : 0,
                        "precio_impresion_pliego" => isset($precio_impresion_pliego_un) ? $precio_impresion_pliego_un  : 0,
                        "gp_emplacado" => isset($gp_emplacado_ungp) ? $gp_emplacado_ungp  : 0,
                        "margen" => $margen,
                        "cotizacion_id" => 0,
                        "variable_cotizador_id" => 1,
                        "codigo_material_detalle" => isset($row->codigo_material) ? $row->codigo_material  : null,
                        "descripcion_material_detalle" => isset($row->descripcion) ? $row->descripcion  : null,
                        "cad_material_detalle" => isset($row->cad) ? $row->cad  : null,
                        "largo" => isset($row->largo) && is_numeric($row->largo) ? $row->largo  : null,
                        "ancho" => isset($row->ancho) && is_numeric($row->ancho) ? $row->ancho  : null,
                        "alto" => isset($row->alto) && is_numeric($row->alto) ? $row->alto  : null,
                        "bct_min_lb" => isset($row->bct_min_lb) && is_numeric($row->bct_min_lb) ? $row->bct_min_lb  : null,
                        "bct_min_kg" => isset($row->bct_min_lb) && is_numeric($row->bct_min_lb) ? (int) ($row->bct_min_lb * 0.4535)  : null,
                        // "unidad_medida_bct" => isset($unidadBCT) ? $unidadBCT  : null,
                        // id, tipo_detalle_id, cantidad, product_type_id, numero_colores, area_hc, anchura, largura, carton_id, impresion, golpes_largo, golpes_ancho, process_id, cinta_desgarro, pegado_terminacion, codigo_cliente, porcentaje_cera_interno, porcentaje_cera_externo, rubro_id, subsubhierarchy_id, matriz, clisse, royalty, maquila, maquila_servicio_id, armado_automatico, armado_usd_caja, zuncho, funda, stretch_film, ancho_pliego_cartulina, largo_pliego_cartulina, precio_pliego_cartulina, precio_impresion_pliego, gp_emplacado, largo_esquinero, carton_esquinero_id, funda_esquinero, tipo_destino_esquinero, tipo_camion_esquinero, margen, planta_id, cotizacion_id, variable_cotizador_id, created_at, updated_at
                    ];*/
                }
                // Creacion de Esquinero
                elseif ($tipo_detalle_id == 2) {
                    $detalle = new DetalleCotizacion([
                        'tipo_detalle_id' => $tipo_detalle_id,
                        'planta_id' => $planta,
                        'cantidad' => $cantidad,
                        'ciudad_id' => $destino,
                        "numero_colores" => $numero_colores,
                        'product_type_id' => 21,
                        'rubro_id' => 5,
                        "largo_esquinero" => $largo_esquinero,
                        "carton_esquinero_id" => $carton,
                        "funda_esquinero" => isset($row->incluye_funda) &&  strtolower(trim("SI")) == strtolower(trim($row->incluye_funda)) ? 1 : 0,
                        "tipo_destino_esquinero" => $tipo_destino_palletizado,
                        "tipo_camion_esquinero" => $camion,
                        "clisse" => isset($row->clisse) &&  strtolower(trim("SI")) == strtolower(trim($row->clisse)) ? 1 : 0,
                        "maquila" => isset($row->maquila) &&  strtolower(trim("SI")) == strtolower(trim($row->maquila)) ? 1 : 0,
                        "margen" => $margen,
                        "cotizacion_id" => 0,
                        "variable_cotizador_id" => 1,

                        "codigo_material_detalle" => isset($row->codigo_material) ? $row->codigo_material  : null,
                        "descripcion_material_detalle" => isset($row->descripcion) ? $row->descripcion  : null,
                        "cad_material_detalle" => isset($row->cad) ? $row->cad  : null,
                        "detalle_valor_dolar"=>$precio_dolar_actual,
                    ]);
                }

              
                $indice_complejidad = ($tipo_detalle_id == 1) ? $this->calcularIndiceComplejidad($detallesInsert[$indice_aux]) : 3;
                
                $volumen = $detallesInsert[$indice_aux]["area_hc"] * $detallesInsert[$indice_aux]["cantidad"];
                
                $tarifario = TarifarioMargen::where("rubro_id", $detallesInsert[$indice_aux]["rubro_id"])->where("tipo_cliente", "A")->where("indice_complejidad", $indice_complejidad)
                    ->where('volumen_negociacion_minimo_2', '<=', $volumen)->where('volumen_negociacion_maximo_2', '>=', $volumen)->first();
                // dd($volumen,$detalle->rubro_id, $indice_complejidad, $tarifario);
               
                //$detallesInsert[$indice_aux]["margen_sugerido"] = $tarifario ? $tarifario->margen_minimo_usd_mm2 : 0;
                //$detallesAux[$indice_aux]["indice_complejidad"] = $indice_complejidad;
               // $detallesInsert[$indice_aux]["margen_sugerido"] = $tarifario ? $tarifario->margen_minimo_usd_mm2 : 0;
                $detallesInsert[$indice_aux]["indice_complejidad"] = $indice_complejidad;
                //$detalle->save();
                //$detallesAux[$indice_aux]["linea"] = $key + 2;
                $indice_aux++;
                

                // dd($row);
            }
        }

        //dd($detallesID);
        $detalle_insert = DetalleCotizacion::insert($detallesInsert);  
        $detalles = DetalleCotizacion::withAll()->where('cotizacion_id',$codigo_carga)->get();
       //dd($detalles);
        //$detalles = DetalleCotizacion::withAll()->whereIn('id', $detallesID)->get();

        // return $detalles; 
        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'detalles' => $detalles,
            'detallesInvalidos' => $detallesInvalidos,

        ], 200);
 

        // $exito = null;
        // $failure = null;
        // $error = null;
        // $detalles_ingresados = [];
        // $detalles_error = [];

        // if (isset($detalles)) {
        //     $exito = 'Se ingresaron los siguientes detalles:';
        //     $detalles_ingresados = $detalles;
        // }
        // if (isset($detallesInvalidos)) {
        //     $detalles_error = $detallesInvalidos;
        //     $error = 'Los siguientes detalles presentan un problema';
        // }
    }

    public function calcularIndiceComplejidad($detalle)
    {
        $indice_complejidad = 3;
        //dd($detalle[0]["porcentaje_cera_interno"]); 
        // Si solo es tabique y sin proceso  o maquila el indice es 2
        if (in_array($detalle["product_type_id"], [33, 20, 19]) && $detalle["process_id"] == 3 && $detalle["maquila"] == 0) {
            $indice_complejidad = 2;
        }

        // Si es solo maquila sin proceso ni tabique indice = 2
        if ($detalle["maquila"] == 1 && !in_array($detalle["product_type_id"], [33, 20, 19]) && $detalle["process_id"] == 3) {
            $indice_complejidad = 2;
        }
        // Si es proceso rdc sin maquila ni tabique ni cera y numero de colores es menor a 3
       // if ($detalle["process_id"] == 2 && $detalle["maquila"] == 0 && !in_array($detalle["product_type_id"], [33, 20, 19]) && ($detalle["porcentaje_cera_interno"] < 1 && $detalle["porcentaje_cera_externo"] < 1) && $detalle["numero_colores"] < 3) {
        if ($detalle["process_id"] == 2 && $detalle["maquila"] == 0 && !in_array($detalle["product_type_id"], [33, 20, 19]) && $detalle["numero_colores"] < 3) {
            if ($detalle["numero_colores"] < 2) {
                $indice_complejidad = 1;
            } else {
                $indice_complejidad = 2;
            }
        }

        // Si es proceso flexo sin maquila ni tabique ni cera y numero de colores es menor a 3
       // if ($detalle["process_id"] == 1 && $detalle["maquila"] == 0 && !in_array($detalle["product_type_id"], [33, 20, 19]) && ($detalle["porcentaje_cera_interno"] < 1 && $detalle["porcentaje_cera_externo"] < 1) && $detalle["numero_colores"] < 3) {
        if ($detalle["process_id"] == 1 && $detalle["maquila"] == 0 && !in_array($detalle["product_type_id"], [33, 20, 19]) &&  $detalle["numero_colores"] < 3) {
            $indice_complejidad = 2;
        }

        // dd($detalle);
        return $indice_complejidad;
    }

    public function guardarMultiplesOt(Request $request)
    {
        $data = $request->all();
        $detalle_cotizacion = [];

        foreach($data as $key_id => $tipo_detalle)
        {

            $ot =  WorkOrder::with(
                'subsubhierarchy.subhierarchy.hierarchy',
                'canal',
                'client',
                'creador',
                'productType',
                "users",
                "material",
                "cad_asignado",
                "proceso"
            )->find($key_id);

            $client = Client::find($ot->client_id);;

            $detalle = new DetalleCotizacion();
            $detalle->margen = 0;

            $detalle->tipo_detalle_id = $tipo_detalle;
            $detalle->work_order_id = $key_id;

             // Si al crearlo se busca por material campos de material y cad se llenan
            $detalle->codigo_material_detalle = $ot->material_code;
            $detalle->descripcion_material_detalle = $ot->descripcion_material;
            $detalle->cad_material_detalle =$ot->cad;
            $detalle->cad_material_id = $ot->cad_id;
            $detalle->material_id =  $ot->material_id;
            $detalle->codigo_cliente = $client->codigo;

            // Corrugado
            if ($tipo_detalle == 1) {
                $detalle->cantidad = 1000; //Se debe modificar
                $detalle->area_hc = str_replace(',', '.', $ot->area_hc);
                $detalle->anchura = $ot->anchura_hm;
                $detalle->largura = $ot->largura_hm;
                $detalle->product_type_id = $ot->product_type_id;
                $detalle->carton_id = $ot->carton_id;
                $detalle->numero_colores = $ot->numero_colores;
                $detalle->impresion = $ot->impresion_1;
                $detalle->golpes_largo = $ot->golpes_largo;
                $detalle->golpes_ancho = $ot->golpes_ancho;
                $detalle->process_id = $ot->process_id;
                $detalle->pegado_terminacion = $ot->pegado_terminacion;
                $detalle->porcentaje_cera_interno = $ot->cera_interior;
                $detalle->porcentaje_cera_externo = $ot->cera_exterior;
                // Si el tipo de producto es = 8 = CABEZAL maquila siempre es SI
                $detalle->maquila = $ot->maquila;
                $detalle->maquila_servicio_id = $ot->maquila_servicio_id;
                $detalle->pallet = $ot->pallet_sobre_pallet;
                //$detalle->pallet = $ot->pallet_type_id;
                // // Campos opcionales
                $detalle->subsubhierarchy_id = $ot->subsubhierarchy_id;
                $detalle->rubro_id = $ot->subsubhierarchy->rubro_id;
                $detalle->tipo_medida = 1;
                $detalle->largo = $ot->interno_largo;
                $detalle->ancho = $ot->interno_ancho;
                $detalle->alto = $ot->interno_alto;
                $detalle->bct_min_lb = $ot->bct_min_lb;
                $detalle->bct_min_kg = $ot->bct_min_kg;
                // // $detalle->unidad_medida_bct = request('unidad_medida_bct');
                

                // Inputs offset
                // $detalle->ancho_pliego_cartulina = (trim($request->input('ancho_pliego_cartulina')) != '') ? $request->input('ancho_pliego_cartulina') : null;
                // $detalle->largo_pliego_cartulina = (trim($request->input('largo_pliego_cartulina')) != '') ? $request->input('largo_pliego_cartulina') : null;
                // $detalle->precio_pliego_cartulina = (trim($request->input('precio_pliego_cartulina')) != '') ? $request->input('precio_pliego_cartulina') : null;
                // $detalle->precio_impresion_pliego = (trim($request->input('precio_impresion_pliego')) != '') ? $request->input('precio_impresion_pliego') : null;
                // $detalle->gp_emplacado = (trim($request->input('gp_emplacado')) != '') ? $request->input('gp_emplacado') : null;

                $indice_complejidad = 3;
                // $detalle->cotizacion_id = request('cotizacion_id');
                // $detalle->precio = precio();

                // Si tiene cinta de desgarro planta = tiltil

                // $detalle->cinta_desgarro = request('cinta_desgarro');
                // if ($detalle->cinta_desgarro == 1) {
                    $detalle->planta_id = 1;
                // }
            } else if ($tipo_detalle == 2) { //Esquinero
                $detalle->product_type_id = 21;
                // $detalle->largo_esquinero = request('largo_esquinero');
                $detalle->carton_esquinero_id = 38; //Carton esquinero de prueba
                $detalle->cantidad = 1000; //Se debe modificar
                $detalle->numero_colores = $ot->numero_colores;
                // $detalle->funda_esquinero = request('funda_esquinero');
                // $detalle->tipo_destino_esquinero = request('tipo_destino_esquinero');
                // $detalle->tipo_camion_esquinero = request('tipo_camion_esquinero');
                // $detalle->clisse = request('clisse_esquinero');
                $detalle->maquila = $ot->maquila;

                $detalle->rubro_id = 5;
                $indice_complejidad = 3;
            }
    
            $volumen = $detalle->area_hc * $detalle->cantidad;
            //$margen_sugerido = calcularMargenSugerido($detalle);
            ///Se Valida si es vendedor externo para obtener el margen sugerido
            if(Auth()->user()->isVendedorExterno()){
                $cliente=Client::where('id',Auth()->user()->cliente_id)->first();
                $margen_sugerido = $cliente->margen_minimo_vendedor_externo;
                
            }else{
                //$margen_sugerido = obtenerMargenSugerido($detalle);
                $margen_sugerido = obtenerMargenSugeridoNew($detalle);
                 
                
            } 

            //$margen_sugerido = obtenerMargenSugerido($detalle);
            $detalle->margen_sugerido = $margen_sugerido;
            $detalle->indice_complejidad = $indice_complejidad;
            $detalle->save();

            // $detalle = DetalleCotizacion::withAll()->find($detalle->id);

            $detalle_cotizacion[] = $detalle->id;
            
        }

        return response()->json($detalle_cotizacion);
    }

    public function obtieneDatos(Request $request) {
        $ids = explode(',', request('ids'));

        $datos = [];
        foreach($ids as $id) {
            $detalle = DetalleCotizacion::withAll()->find($id);
            $datos[] = $detalle;
        }
      
        return response()->json(array('datos' => $datos));
    }

    //Devuelve solo los cartones de Alta grafica que pertenecen a los procesos DIECUTTER - ALTA GRÁFICA y DIECUTTER -C/PEGADO ALTA GRÁFICA
    public function cartonAltaGrafica(){

        //$cartones_alta_grafica = Carton::where('active', 1)->where('tipo_proceso',['ALTA_GRAFICA'])->pluck('codigo','id')->toArray();
        $cartones_alta_grafica = Carton::where('active', 1)->where('alta_grafica',1)->pluck('codigo','id')->toArray();

        $html = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimple($cartones_alta_grafica, 'id');
        return $html;
    }

    //Devuelve todos los cartones para los otros procesos
    public function cartonGenerico(){

        $cartones = Carton::where('active', 1)->where('tipo', '!=', 'ESQUINEROS')->pluck('codigo','id')->toArray();

        $html = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimple($cartones, 'id');
        return $html;
    }
}
