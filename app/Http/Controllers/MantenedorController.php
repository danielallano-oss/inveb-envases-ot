<?php

namespace App\Http\Controllers;

use App\Carton;
use App\CartonEsquinero;
use App\CiudadesFlete;
use App\ConsumoAdhesivo;
use App\ConsumoAdhesivoPegado;
use App\ConsumoEnergia;
use App\DetallePrecioPalletizado;
use App\Envase;
use App\FactoresDesarrollo;
use App\FactoresOnda;
use App\FactoresSeguridad;
use App\InsumosPalletizado;
use App\MaquilaServicio;
use App\Material;
use App\MermaConvertidora;
use App\MermaCorrugadora;
use App\Muestra;
use App\Paper;
use App\Planta;
use App\Process;
use App\Rubro;
use App\Tarifario;
use App\TipoOnda;
use App\VariablesCotizador;
use App\MargenMinimo;
use App\Hierarchy;
use App\Client;
use App\Matriz;
use App\PorcentajeMargen;
use App\ClasificacionCliente;
use App\ManoObraMantencion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Excel;
use stdClass;
use DB;
use PHPExcel_CachedObjectStorageFactory;
use PHPExcel_Settings;

class MantenedorController extends Controller
{

    //////////////CARTONES CORRUGADOS//////////////////
    ////////////////////////////////////////
    //////////////CARTONES CORRUGADOS/////////////////
    ////////////////////////////////////////
    //////////////CARTONES CORRUGADOS//////////////////
    public function cargaCartonsForm()
    {
        $cartones = Carton::where("tipo", "!=", "ESQUINEROS")->orderByRaw('ISNULL(orden), orden ASC')->get();
        return view('mantenedores.cartons-masive', compact("cartones"));
    }


    public function importCartons(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $proceso = request("proceso");
        $papeles = Paper::pluck('id', 'codigo')->toArray();
        // $papeles = TipoOnda::pluck('id', 'codigo')->toArray();

        // añadimos 0 al listado de papeles ya que por base de dato este papel es nulo pero lo necesitamos para la comparacion
        $papeles[0] = 0;
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END

                // dd("fin");

                // Algoritmo especifico de cartones}

                $motivos = [];

                // Validaciones generales
                // dd(array_key_exists((int) $row->codigo_onda_1_2, $papeles), (int) $row->codigo_onda_1_2, $papeles);
                $ondas_validas = ["C", "CB", "CE", "B", "BE", "E", "P", "P-BC", "EB", "EC","BC"];
                // dd(is_numeric($row->tolerancia_gramaje_real), $row->tolerancia_gramaje_real, in_array($row->onda, $ondas_validas));
                $codigo = (trim($row->codigo) != "") ? $row->codigo : false;
                $onda = in_array($row->onda, $ondas_validas) ? $row->onda : false;
                $color_tapa_exterior = in_array(strtolower($row->color_tapa_exterior), ["blanco", "cafe"]) ? $row->color_tapa_exterior : false;
                $tipo = in_array(strtoupper($row->tipo), ["SIMPLES", "DOBLES", "DOBLE MONOTAPA", "POWER PLY", "SIMPLE EMPLACADO"]) ? $row->tipo : false;
                $codigo_tapa_interior = array_key_exists((string)$row->codigo_tapa_interior, $papeles) ? (string)$row->codigo_tapa_interior : false;
                $codigo_onda_1 = array_key_exists((string)$row->codigo_onda_1, $papeles) ? (string)$row->codigo_onda_1 : false;
                $codigo_onda_1_2 = array_key_exists((string)$row->codigo_onda_1_2, $papeles) ? (string)$row->codigo_onda_1_2 : false;
                $codigo_tapa_media = array_key_exists((string)$row->codigo_tapa_media, $papeles) ? (string)$row->codigo_tapa_media : false;
                $codigo_onda_2 = array_key_exists((string)$row->codigo_onda_2, $papeles) ? (string)$row->codigo_onda_2 : false;
                $codigo_tapa_exterior = array_key_exists((string)$row->codigo_tapa_exterior, $papeles) ? (string)$row->codigo_tapa_exterior : false;
                $ect_min = is_numeric($row->ect_min) ? $row->ect_min : false;
                $espesor = is_numeric($row->espesor) ? $row->espesor : false;
                $peso = is_numeric($row->peso) ? $row->peso : false;
                $peso_total = is_numeric($row->peso_total) ? $row->peso_total : false;
                $tolerancia_gramaje_real = is_numeric($row->tolerancia_gramaje_real) ? $row->tolerancia_gramaje_real : false;
                $contenido_cordillera = is_numeric($row->contenido_cordillera) ? $row->contenido_cordillera : false;
                $contenido_reciclado = is_numeric($row->contenido_reciclado) ? $row->contenido_reciclado : false;
                $porocidad_gurley = is_numeric($row->porocidad_gurley) ? $row->porocidad_gurley : false;
                $cobb_int = is_numeric($row->cobb_int) ? $row->cobb_int : false;
                $cobb_ext = is_numeric($row->cobb_ext) ? $row->cobb_ext : false;

                // $margen = is_numeric($row->margen) ? $row->margen : false;
                // $destino = array_key_exists($row->lugar_destino, $ciudades) ? $ciudades[$row->lugar_destino] : false;

                if (!$codigo) $motivos[] = " Codigo";
                if (!$onda) $motivos[] = " Onda";
                if (!$color_tapa_exterior) $motivos[] = " Color Tapa Exterior";
                if (!$tipo) $motivos[] = " Tipo";
                if (!$codigo_tapa_interior && $codigo_tapa_interior != '0') $motivos[] = " Codigo Tapa Interior No existe";
                if (!$codigo_onda_1 && $codigo_onda_1 != '0') $motivos[] = " Codigo Onda 1 No existe";
                if (!$codigo_onda_1_2 && $codigo_onda_1_2 != '0') $motivos[] = " Codigo Onda 1.2 No existe";
                if (!$codigo_tapa_media && $codigo_tapa_media != '0') $motivos[] = " Codigo Tapa Media No existe";
                if (!$codigo_onda_2 && $codigo_onda_2 != '0') $motivos[] = " Codigo Onda 2 No existe";
                if (!$codigo_tapa_exterior && $codigo_tapa_exterior != '0') $motivos[] = " Codigo Tapa Exterior No existe";
                // if (!$cantidad) $motivos[] = " Cantidad";
                // if (!$numero_colores && $numero_colores != 0) $motivos[] = " Colores";
                if (!$ect_min && $ect_min !== 0) $motivos[] = " ECT Min";
                if (!$espesor && $espesor !== 0) $motivos[] = " Espesor";
                if (!$peso && $peso !== 0) $motivos[] = " Peso";
                if (!$peso_total && $peso_total !== 0) $motivos[] = " Peso Total";
                if (!$tolerancia_gramaje_real && $tolerancia_gramaje_real != 0) $motivos[] = " Tolerancia Gramaje Real";
                if (!$contenido_cordillera && $contenido_cordillera != 0) $motivos[] = " Contenido Cordillera";
                if (!$contenido_reciclado && $contenido_reciclado != 0) $motivos[] = " Contenido Reciclado";
                if (!$porocidad_gurley && $porocidad_gurley !== 0) $motivos[] = " Porocidad Gurley";
                if (!$cobb_int && $cobb_int !== 0) $motivos[] = " Cobb Interior";
                if (!$cobb_ext && $cobb_ext !== 0) $motivos[] = " Cobb Exterior";

                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $cartonErroneo = new stdClass();
                    $cartonErroneo->linea = $key + 2;
                    $cartonErroneo->motivos = $motivos;
                    // $cartonesInvalidos[] = $key + 2;
                    $cartonesInvalidos[] = $cartonErroneo;
                    continue;
                }

                $carton = Carton::where('codigo', $row->codigo)->first();
                // dd($user);
                if ($carton) {

                    $buin = $row->buin != '' || $row->buin != 0 ? 1 : '';
                    $tiltil = $row->tiltil != '' || $row->tiltil != 0 ? 2 : '';
                    $osorno = $row->osorno != '' || $row->osorno != 0 ? 3 : '';

                    $array_planta = [];

                    // Agregamos los valores solo si no están vacíos
                    if ($row->buin != '') {
                        $array_planta[] = $buin;
                    }

                    if ($row->tiltil != '') {
                        $array_planta[] = $tiltil;
                    }

                    if ($row->osorno != '') {
                        $array_planta[] = $osorno;
                    }
                    sort($array_planta);
                    $string_planta = implode(',', $array_planta);




                    $carton->codigo = trim($row->codigo);
                    $carton->onda = trim($row->onda);
                    $carton->color_tapa_exterior = trim($row->color_tapa_exterior);
                    $carton->tipo = trim($row->tipo);
                    $carton->ect_min = trim($row->ect_min);
                    $carton->espesor = trim($row->espesor);
                    $carton->peso = trim($row->peso);
                    $carton->peso_total = trim($row->peso_total);
                    $carton->tolerancia_gramaje_real = trim($row->tolerancia_gramaje_real);
                    $carton->contenido_cordillera = trim($row->contenido_cordillera);
                    $carton->contenido_reciclado = trim($row->contenido_reciclado);
                    $carton->porocidad_gurley = trim($row->porocidad_gurley);
                    $carton->cobb_int = trim($row->cobb_int);
                    $carton->cobb_ext = trim($row->cobb_ext);
                    $carton->recubrimiento = trim($row->recubrimiento);
                    $carton->codigo_tapa_interior = trim($row->codigo_tapa_interior);
                    $carton->codigo_onda_1 = trim($row->codigo_onda_1);
                    $carton->codigo_onda_1_2 = trim($row->codigo_onda_1_2);
                    $carton->codigo_tapa_media = trim($row->codigo_tapa_media);
                    $carton->codigo_onda_2 = trim($row->codigo_onda_2);
                    $carton->codigo_tapa_exterior = trim($row->codigo_tapa_exterior);
                    $carton->desperdicio = (trim($row->desperdicio) != '') ? $row->desperdicio : null;
                    $carton->excepcion = (trim($row->excepcion) != '') ? $row->excepcion : null;
                    $carton->carton_muestra = trim($row->carton_muestra);
                    $carton->alta_grafica = trim($row->alta_grafica);
                    $carton->provisional = trim($row->provisional);
                    $carton->carton_original = trim($row->carton_original);
                    $carton->planta_id = trim($string_planta);

                    $carton_original = Carton::where('codigo', $row->carton_original)->first();

                    $carton->carton_original_id = ($carton_original) ? $carton_original->id : null;

                    $carton->active = trim($row->active);

                    // dd($carton, $carton->isDirty(), $carton->getChanges(), $carton->isDirty("desperdicio"));
                    if ($carton->isDirty()) {
                        $carton->orden = $key + 2;
                        // para marcar un carton como inactivado debe ser originalmente activo
                        if (($carton->getOriginal("active") == 1 && $carton->active == 0)) {
                            $cartonesInactivados[] = $carton;
                        } else {
                            $cartonesActualizados[] = $carton;
                        }
                        // dd($carton->getDirty(), $carton);

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($carton, $row, "UPDATE", $codigo_operacion);
                            $carton->save();
                            continue;
                        }
                    } else {
                        // Si no hay cambios lo unico q actualizamos es el orden segun archivo
                        $carton->update(["orden" => $key + 2]);
                    }




                    // $carton->linea = $key + 2;
                } else {
                    $carton = new carton();
                    // $carton->id = trim($row->id);
                    $carton->codigo = trim($row->codigo);
                    $carton->onda = trim($row->onda);
                    $carton->color_tapa_exterior = trim($row->color_tapa_exterior);
                    $carton->tipo = trim($row->tipo);
                    $carton->ect_min = trim($row->ect_min);
                    $carton->espesor = trim($row->espesor);
                    $carton->peso = trim($row->peso);
                    $carton->peso_total = trim($row->peso_total);
                    $carton->tolerancia_gramaje_real = trim($row->tolerancia_gramaje_real);
                    $carton->contenido_cordillera = trim($row->contenido_cordillera);
                    $carton->contenido_reciclado = trim($row->contenido_reciclado);
                    $carton->porocidad_gurley = trim($row->porocidad_gurley);
                    $carton->cobb_int = trim($row->cobb_int);
                    $carton->cobb_ext = trim($row->cobb_ext);
                    $carton->recubrimiento = trim($row->recubrimiento);
                    $carton->codigo_tapa_interior = trim($row->codigo_tapa_interior);
                    $carton->codigo_onda_1 = trim($row->codigo_onda_1);
                    $carton->codigo_onda_1_2 = trim($row->codigo_onda_1_2);
                    $carton->codigo_tapa_media = trim($row->codigo_tapa_media);
                    $carton->codigo_onda_2 = trim($row->codigo_onda_2);
                    $carton->codigo_tapa_exterior = trim($row->codigo_tapa_exterior);
                    $carton->desperdicio = (trim($row->desperdicio) != '') ? $row->desperdicio : null;
                    $carton->excepcion = (trim($row->excepcion) != '') ? $row->excepcion : null;
                    $carton->carton_muestra = trim($row->carton_muestra);
                    $carton->alta_grafica = trim($row->alta_grafica);
                    $carton->provisional = trim($row->provisional);
                    $carton->carton_original = trim($row->carton_original);

                    $carton_original = Carton::where('codigo', $row->carton_original)->first();
                    $carton->carton_original_id = ($carton_original) ? $carton_original->id : null;

                    $carton->active = trim($row->active);
                    $carton->orden = $key + 2;
                    // Solo si el proceso ya es de carga guardamos y continuamos
                    if ($proceso == "cargaCompleta") {
                        $changelog = changelog($carton, $row, "INSERT", $codigo_operacion);
                        $carton->save();
                        $changelog->update(['item_id' => $carton->id]);
                        continue;
                    }


                    $carton->linea = $key + 2;
                    $cartones[] = $carton;
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.cartons.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $cartones_ingresados = [];
        $cartones_actualizados = [];
        $cartones_inactivados = [];
        $cartones_error = [];

        if (isset($cartones)) {
            $exito = 'Se ingresaron los siguientes cartones';
            $cartones_ingresados = $cartones;
            // $plantas_ingresadas = Planta::whereIn('id', $ids)->with('status')->paginate(10);
        }
        if (isset($cartonesActualizados)) {
            $updated = 'Los siguientes cartones fueron actualizados:';
            $cartones_actualizados = $cartonesActualizados;
        }
        if (isset($cartonesInactivados)) {
            $updated = 'Los siguientes cartones fueron actualizados:';
            $cartones_inactivados = $cartonesInactivados;
        }
        if (isset($cartonesInvalidos)) {
            $error = 'Los siguientes cartones tienen 1 o mas errores';
            $cartones_error = $cartonesInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'cartones' => $cartones_ingresados,
            'cartones_actualizados' => $cartones_actualizados,
            'cartones_inactivados' => $cartones_inactivados,
            'cartones_error' => $cartones_error

        ], 200);
    }

    public function descargar_excel_cartones_corrugados(Request $request)
    {
        $titulo = "Listado Cartones Corrugados";
        $cartones = Carton::where("tipo", "!=", "ESQUINEROS")->orderByRaw('ISNULL(orden), orden ASC')->get();
        // dd($cartones);
        $cartones_array[] = array(
            'ID',
            'codigo',
            'onda',
            'color_tapa_exterior',
            'tipo',
            'ect_min',
            'espesor',
            'peso',
            'peso_total',
            'tolerancia_gramaje_real',
            'contenido_cordillera',
            'contenido_reciclado',
            'porocidad_gurley',
            'cobb_int',
            'cobb_ext',
            'recubrimiento',
            'codigo_tapa_interior',
            'codigo_onda_1',
            'codigo_onda_1_2',
            'codigo_tapa_media',
            'codigo_onda_2',
            'codigo_tapa_exterior',
            'desperdicio',
            'excepcion',
            'carton_muestra',
            'alta_grafica',
            'Buin',
            'Osorno',
            'Tiltil',
            'provisional',
            'carton_original',
            'active'

        );

        foreach ($cartones as $carton) {

            //obtener planta por separadas segun $carton->planta_id

            $buin = 0;
            $tiltil = 0;
            $osorno = 0;

            if ($carton->planta_id != '') {

                $plantas = explode(",", $carton->planta_id);

                foreach ($plantas as $planta) {

                    if ($planta == 1) {
                        $buin = 1;
                    }

                    if ($planta == 2) {
                        $tiltil = 1;
                    }

                    if ($planta == 3) {
                        $osorno = 1;
                    }
                }
            }



            $cartones_array[] = array(
                $carton->id,
                $carton->codigo,
                $carton->onda,
                $carton->color_tapa_exterior,
                $carton->tipo,
                $carton->ect_min,
                $carton->espesor,
                $carton->peso,
                $carton->peso_total,
                $carton->tolerancia_gramaje_real,
                $carton->contenido_cordillera,
                $carton->contenido_reciclado,
                $carton->porocidad_gurley,
                $carton->cobb_int,
                $carton->cobb_ext,
                $carton->recubrimiento,
                $carton->codigo_tapa_interior,
                $carton->codigo_onda_1,
                $carton->codigo_onda_1_2,
                $carton->codigo_tapa_media,
                $carton->codigo_onda_2,
                $carton->codigo_tapa_exterior,
                $carton->desperdicio,
                $carton->excepcion,
                $carton->carton_muestra,
                $carton->alta_grafica,
                $buin,
                $osorno,
                $tiltil,
                $carton->provisional,
                $carton->carton_original,
                $carton->active,

            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($cartones_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Cartones', function ($sheet) use ($cartones_array) {
                $sheet->fromArray($cartones_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    //////////////CARTONES ESQUINEROS//////////////////
    ////////////////////////////////////////
    //////////////CARTONES ESQUINEROS/////////////////
    ////////////////////////////////////////
    //////////////CARTONES ESQUINEROS//////////////////
    public function cargaCartonesEsquinerosForm()
    {
        $cartones = CartonEsquinero::orderByRaw('ISNULL(orden), orden ASC')->get();
        // $cartones = CartonEsquinero::where("tipo", "==", "ESQUINEROS")->orderByRaw('ISNULL(orden), orden ASC')->get();
        return view('mantenedores.cartones-esquineros-masive', compact("cartones"));
    }


    public function importCartonesEsquineros(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $proceso = request("proceso");
        $papeles = Paper::pluck('id', 'codigo')->toArray();
        // $papeles = TipoOnda::pluck('id', 'codigo')->toArray();

        // añadimos 0 al listado de papeles ya que por base de dato este papel es nulo pero lo necesitamos para la comparacion
        $papeles[0] = 0;
        $codigo_operacion = Carbon::now()->timestamp . '-' . Auth()->user()->id;
        // carton base para duplicar datos al guardar esquinero en cartones generales
        // EN9HB
        $carton_esquinero_base = Carton::where("codigo", "EN9HB")->first();
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END

                // dd("fin");

                // Algoritmo especifico de cartones}

                $motivos = [];

                // Validaciones generales
                // dd(is_numeric($row->tolerancia_gramaje_real), $row->tolerancia_gramaje_real, in_array($row->onda, $ondas_validas));
                $codigo = (trim($row->codigo) != "") ? $row->codigo : false;
                $codigo_papel_1 = array_key_exists((string)$row->codigo_papel_1, $papeles) ? (string)$row->codigo_papel_1 : false;
                $ancho_1 = is_numeric($row->ancho_1) ? $row->ancho_1 : false;
                $capas_1 = is_numeric($row->capas_1) ? $row->capas_1 : false;
                $codigo_papel_2 = array_key_exists((string)$row->codigo_papel_2, $papeles) ? (string)$row->codigo_papel_2 : false;
                $ancho_2 = is_numeric($row->ancho_2) ? $row->ancho_2 : false;
                $capas_2 = is_numeric($row->capas_2) ? $row->capas_2 : false;
                $codigo_papel_3 = array_key_exists((string)$row->codigo_papel_3, $papeles) ? (string)$row->codigo_papel_3 : false;
                $ancho_3 = is_numeric($row->ancho_3) ? $row->ancho_3 : false;
                $capas_3 = is_numeric($row->capas_3) ? $row->capas_3 : false;
                $codigo_papel_4 = array_key_exists((string)$row->codigo_papel_4, $papeles) ? (string)$row->codigo_papel_4 : false;
                $ancho_4 = is_numeric($row->ancho_4) ? $row->ancho_4 : false;
                $capas_4 = is_numeric($row->capas_4) ? $row->capas_4 : false;
                $codigo_papel_5 = array_key_exists((string)$row->codigo_papel_5, $papeles) ? (string)$row->codigo_papel_5 : false;
                $ancho_5 = is_numeric($row->ancho_5) ? $row->ancho_5 : false;
                $capas_5 = is_numeric($row->capas_5) ? $row->capas_5 : false;

                $espesor = is_numeric($row->espesor) ? $row->espesor : false;
                $alta_grafica = is_numeric($row->alta_grafica) ? (int) $row->alta_grafica : false;
                $ancho_esquinero = is_numeric($row->ancho_esquinero) ? $row->ancho_esquinero : false;


                if (!$codigo) $motivos[] = " Codigo";
                if (!$codigo_papel_1 && $codigo_papel_1 != 0) $motivos[] = " Codigo Papel 1";
                if (!$ancho_1 && $ancho_1 != 0) $motivos[] = " Ancho Papel 1";
                if (!$capas_1 && $capas_1 != 0) $motivos[] = " Capas Papel 1";
                if (!$codigo_papel_2 && $codigo_papel_2 != 0) $motivos[] = " Codigo Papel 2";
                if (!$ancho_2 && $ancho_2 != 0) $motivos[] = " Ancho Papel 2";
                if (!$capas_2 && $capas_2 != 0) $motivos[] = " Capas Papel 2";
                if (!$codigo_papel_3 && $codigo_papel_3 != 0) $motivos[] = " Codigo Papel 3";
                if (!$ancho_3 && $ancho_3 != 0) $motivos[] = " Ancho Papel 3";
                if (!$capas_3 && $capas_3 != 0) $motivos[] = " Capas Papel 3";
                if (!$codigo_papel_4 && $codigo_papel_4 != 0) $motivos[] = " Codigo Papel 4";
                if (!$ancho_4 && $ancho_4 != 0) $motivos[] = " Ancho Papel 4";
                if (!$capas_4 && $capas_4 != 0) $motivos[] = " Capas Papel 4";
                if (!$codigo_papel_5 && $codigo_papel_5 != 0) $motivos[] = " Codigo Papel 5";
                if (!$ancho_5 && $ancho_5 != 0) $motivos[] = " Ancho Papel 5";
                if (!$capas_5 && $capas_5 != 0) $motivos[] = " Capas Papel 5";
                if (!$espesor && $espesor !== 0) $motivos[] = " Espesor";
                if (!$alta_grafica && $alta_grafica !== 0) $motivos[] = " Alta Grafica";
                if (!$ancho_esquinero && $ancho_esquinero !== 0) $motivos[] = " Ancho Esquinero";



                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $cartonErroneo = new stdClass();
                    $cartonErroneo->linea = $key + 2;
                    $cartonErroneo->motivos = $motivos;
                    // $cartonesInvalidos[] = $key + 2;
                    $cartonesInvalidos[] = $cartonErroneo;
                    continue;
                }

                $carton = CartonEsquinero::where('codigo', $row->codigo)->first();
                // dd($user);
                if ($carton) {
                    $carton->codigo = trim($row->codigo);
                    $carton->active = trim($row->active);
                    $carton->codigo_papel_1 = trim($row->codigo_papel_1);
                    // dd($carton, $carton->isDirty(), $carton->getChanges(), $carton->isDirty("desperdicio"));
                    if ($carton->isDirty()) {

                        $carton->codigo = trim($row->codigo);
                        $carton->codigo_papel_1 = trim($row->codigo_papel_1);
                        $carton->ancho_1 = trim($row->ancho_1);
                        $carton->capas_1 = trim($row->capas_1);
                        $carton->codigo_papel_2 = trim($row->codigo_papel_2);
                        $carton->ancho_2 = trim($row->ancho_2);
                        $carton->capas_2 = trim($row->capas_2);
                        $carton->codigo_papel_3 = trim($row->codigo_papel_3);
                        $carton->ancho_3 = trim($row->ancho_3);
                        $carton->capas_3 = trim($row->capas_3);
                        $carton->codigo_papel_4 = trim($row->codigo_papel_4);
                        $carton->ancho_4 = trim($row->ancho_4);
                        $carton->capas_4 = trim($row->capas_4);
                        $carton->codigo_papel_5 = trim($row->codigo_papel_5);
                        $carton->ancho_5 = trim($row->ancho_5);
                        $carton->capas_5 = trim($row->capas_5);
                        $carton->resistencia = trim($row->resistencia);
                        $carton->espesor = trim($row->espesor);
                        $carton->alta_grafica = trim($row->alta_grafica);
                        $carton->ancho_esquinero = trim($row->ancho_esquinero);
                        $carton->active = trim($row->active);
                        $carton->orden = $key + 2;
                        // para marcar un carton como inactivado debe ser originalmente activo
                        if (($carton->getOriginal("active") == 1 && $carton->active == 0)) {
                            $cartonesInactivados[] = $carton;
                        } else {
                            $cartonesActualizados[] = $carton;
                        }
                        // dd($carton->getDirty(), $carton);

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($carton, $row, "UPDATE", $codigo_operacion);
                            $carton->save();
                            // En la tabla comun de cartones mantenemos sincronia con los esquineros
                            // ya que de esta forma los mostramos en un solo select en envases
                            // aunque se administren los 2 mantenedores de forma indepentiente
                            $carton_aux = Carton::where('codigo', $carton->codigo)->first();
                            $carton_aux->active = $carton->active;
                            $carton_aux->save();
                            continue;
                        }
                    } else {
                        // Si no hay cambios lo unico q actualizamos es el orden segun archivo
                        $carton->update(["orden" => $key + 2]);
                    }




                    // $carton->linea = $key + 2;
                } else {
                    $carton = new CartonEsquinero();
                    // $carton->id = trim($row->id);
                    $carton->codigo = trim($row->codigo);
                    $carton->codigo_papel_1 = trim($row->codigo_papel_1);
                    $carton->ancho_1 = trim($row->ancho_1);
                    $carton->capas_1 = trim($row->capas_1);
                    $carton->codigo_papel_2 = trim($row->codigo_papel_2);
                    $carton->ancho_2 = trim($row->ancho_2);
                    $carton->capas_2 = trim($row->capas_2);
                    $carton->codigo_papel_3 = trim($row->codigo_papel_3);
                    $carton->ancho_3 = trim($row->ancho_3);
                    $carton->capas_3 = trim($row->capas_3);
                    $carton->codigo_papel_4 = trim($row->codigo_papel_4);
                    $carton->ancho_4 = trim($row->ancho_4);
                    $carton->capas_4 = trim($row->capas_4);
                    $carton->codigo_papel_5 = trim($row->codigo_papel_5);
                    $carton->ancho_5 = trim($row->ancho_5);
                    $carton->capas_5 = trim($row->capas_5);
                    $carton->resistencia = trim($row->resistencia);
                    $carton->espesor = trim($row->espesor);
                    $carton->alta_grafica = trim($row->alta_grafica);
                    $carton->ancho_esquinero = trim($row->ancho_esquinero);
                    $carton->active = trim($row->active);

                    $carton->active = trim($row->active);
                    $carton->orden = $key + 2;
                    // Solo si el proceso ya es de carga guardamos y continuamos
                    if ($proceso == "cargaCompleta") {
                        $changelog = changelog($carton, $row, "INSERT", $codigo_operacion);
                        $carton->save();
                        // Registrar el nuevo carton en log historico de cambios
                        $changelog->update(['item_id' => $carton->id]);

                        //Guardar nuevo carton En la tabla comun de cartones, mantenemos sincronia con los esquineros
                        // ya que de esta forma los mostramos en un solo select en envases
                        // aunque se administren los 2 mantenedores de forma indepentiente
                        $carton_aux = $carton_esquinero_base->replicate();
                        $carton_aux->codigo = $carton->codigo;
                        $carton_aux->active = $carton->active;
                        $carton_aux->save();
                        continue;
                    }


                    $carton->linea = $key + 2;
                    $cartones[] = $carton;
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.cartons.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $cartones_ingresados = [];
        $cartones_actualizados = [];
        $cartones_inactivados = [];
        $cartones_error = [];

        if (isset($cartones)) {
            $exito = 'Se ingresaron los siguientes cartones';
            $cartones_ingresados = $cartones;
            // $plantas_ingresadas = Planta::whereIn('id', $ids)->with('status')->paginate(10);
        }
        if (isset($cartonesActualizados)) {
            $updated = 'Los siguientes cartones fueron actualizados:';
            $cartones_actualizados = $cartonesActualizados;
        }
        if (isset($cartonesInactivados)) {
            $updated = 'Los siguientes cartones fueron actualizados:';
            $cartones_inactivados = $cartonesInactivados;
        }
        if (isset($cartonesInvalidos)) {
            $error = 'Los siguientes cartones tienen 1 o mas errores';
            $cartones_error = $cartonesInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'cartones' => $cartones_ingresados,
            'cartones_actualizados' => $cartones_actualizados,
            'cartones_inactivados' => $cartones_inactivados,
            'cartones_error' => $cartones_error

        ], 200);
    }

    public function descargar_excel_cartones_esquineros(Request $request)
    {
        $titulo = "Listado Cartones Esquineros";
        $cartones = CartonEsquinero::orderByRaw('ISNULL(orden), orden ASC')->get();
        // dd($cartones);
        $cartones_array[] = array(
            'ID',
            'codigo',
            'codigo_papel_1',
            'ancho_1',
            'capas_1',
            'codigo_papel_2',
            'ancho_2',
            'capas_2',
            'codigo_papel_3',
            'ancho_3',
            'capas_3',
            'codigo_papel_4',
            'ancho_4',
            'capas_4',
            'codigo_papel_5',
            'ancho_5',
            'capas_5',
            'resistencia',
            'espesor',
            'alta_grafica',
            'ancho_esquinero',
            'active'

        );

        foreach ($cartones as $carton) {
            $cartones_array[] = array(
                $carton->id,
                $carton->codigo,
                $carton->codigo_papel_1,
                $carton->ancho_1,
                $carton->capas_1,
                $carton->codigo_papel_2,
                $carton->ancho_2,
                $carton->capas_2,
                $carton->codigo_papel_3,
                $carton->ancho_3,
                $carton->capas_3,
                $carton->codigo_papel_4,
                $carton->ancho_4,
                $carton->capas_4,
                $carton->codigo_papel_5,
                $carton->ancho_5,
                $carton->capas_5,
                $carton->resistencia,
                $carton->espesor,
                $carton->alta_grafica,
                $carton->ancho_esquinero,
                $carton->active,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($cartones_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Cartones', function ($sheet) use ($cartones_array) {
                $sheet->fromArray($cartones_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    //////////////PAPELES//////////////////
    ////////////////////////////////////////
    //////////////PAPELES/////////////////
    ////////////////////////////////////////
    //////////////PAPELES//////////////////
    public function cargaPapelesForm()
    {
        $papeles = Paper::all();
        return view('mantenedores.papeles-masive', compact("papeles"));
    }


    public function importPapeles(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();

        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}

                $motivos = [];
                // Validaciones generales
                // dd(is_numeric($row->tolerancia_gramaje_real), $row->tolerancia_gramaje_real, in_array($row->onda, $ondas_validas));
                $codigo = (trim($row->codigo) != "") ? $row->codigo : false;
                $gramaje = is_numeric($row->gramaje) ? $row->gramaje : false;
                $precio = is_numeric($row->precio) ? $row->precio : false;
                $mc_usd_ton = is_numeric($row->mc_usd_ton) ? $row->mc_usd_ton : false;

                if (!$codigo) $motivos[] = " Codigo";
                if (!$gramaje && $gramaje !== 0.0) $motivos[] = " Gramaje";
                if (!$precio && $precio !== 0.0) $motivos[] = " Precio";
                if (!$mc_usd_ton && $mc_usd_ton !== 0.0) $motivos[] = " Precio";

                if (count($motivos) >= 1) {
                    $papelErroneo = new stdClass();
                    $papelErroneo->linea = $key + 2;
                    $papelErroneo->motivos = $motivos;
                    $papelesInvalidos[] = $papelErroneo;
                    continue;
                }

                $papel = Paper::where('codigo', $row->codigo)->first();
                // dd($user);
                if ($papel) {
                    // Validar si la diferencia del cambio de precio es significativa
                    // si es mayor al 10% del precio original indicamos que no se hace el cambio
                    // if (abs($papel->precio - $row->precio) > $papel->precio * 0.1) {

                    //     $papelErroneo = new stdClass();
                    //     $papelErroneo->linea = $key + 2;
                    //     $papelErroneo->motivos = "Diferencia de precios de papel excede cambio permitido";
                    //     $papelesInvalidos[] = $papelErroneo;
                    //     continue;
                    // }
                    $papel->codigo = trim($row->codigo);
                    $papel->gramaje = trim($row->gramaje);
                    $papel->precio = trim($row->precio);
                    $papel->mc_usd_ton = trim($row->mc_usd_ton);
                    $papel->active = trim($row->active);
                    // dd($papel, $papel->isDirty(), $papel->getChanges(), $papel->isDirty("desperdicio"));
                    if ($papel->isDirty()) {
                        $papel->orden = $key + 2;
                        // para marcar un papel como inactivado debe ser originalmente activo
                        if (($papel->getOriginal("active") == 1 && $papel->active == 0)) {
                            $papelesInactivados[] = $papel;
                        } else {
                            $papelesActualizados[] = $papel;
                        }
                        // dd($papel->getDirty(), $papel);

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($papel, $row, "UPDATE", $codigo_operacion);
                            $papel->save();
                            continue;
                        }
                    } else {
                        // Si no hay cambios lo unico q actualizamos es el orden segun archivo
                        $papel->update(["orden" => $key + 2]);
                    }
                } else {
                    $papel = new Paper();
                    $papel->codigo = trim($row->codigo);
                    $papel->gramaje = trim($row->gramaje);
                    $papel->precio = trim($row->precio);
                    $papel->mc_usd_ton = trim($row->mc_usd_ton);
                    $papel->active = trim($row->active);

                    $papel->orden = $key + 2;
                    // Solo si el proceso ya es de carga guardamos y continuamos
                    if ($proceso == "cargaCompleta") {
                        $changelog = changelog($papel, $row, "INSERT", $codigo_operacion);
                        $papel->save();
                        $changelog->update(['item_id' => $papel->id]);
                        continue;
                    }

                    $papel->linea = $key + 2;
                    $papeles[] = $papel;
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $papeles_ingresados = [];
        $papeles_actualizados = [];
        $papeles_inactivados = [];
        $papeles_error = [];

        if (isset($papeles)) {
            $exito = 'Se ingresaron los siguientes papeles';
            $papeles_ingresados = $papeles;
            // $plantas_ingresadas = Planta::whereIn('id', $ids)->with('status')->paginate(10);
        }
        if (isset($papelesActualizados)) {
            $updated = 'Los siguientes papeles fueron actualizados:';
            $papeles_actualizados = $papelesActualizados;
        }
        if (isset($papelesInactivados)) {
            $updated = 'Los siguientes papeles fueron actualizados:';
            $papeles_inactivados = $papelesInactivados;
        }
        if (isset($papelesInvalidos)) {
            $error = 'Los siguientes papeles tienen 1 o mas errores';
            $papeles_error = $papelesInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'papeles' => $papeles_ingresados,
            'papeles_actualizados' => $papeles_actualizados,
            'papeles_inactivados' => $papeles_inactivados,
            'papeles_error' => $papeles_error

        ], 200);
    }

    public function descargar_excel_papeles(Request $request)
    {
        $titulo = "Listado Papeles";
        $papeles = Paper::all();
        // dd($papeles);
        $papeles_array[] = array(
            'ID',
            'codigo',
            'gramaje',
            'precio',
            'mc_usd_ton',
            'active'

        );

        foreach ($papeles as $papel) {
            $papeles_array[] = array(
                $papel->id,
                $papel->codigo,
                $papel->gramaje,
                $papel->precio,
                $papel->mc_usd_ton,
                $papel->active,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($papeles_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Papeles', function ($sheet) use ($papeles_array) {
                $sheet->fromArray($papeles_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    //////////////FLETES//////////////////
    ////////////////////////////////////////
    //////////////FLETES/////////////////
    ////////////////////////////////////////
    //////////////FLETES//////////////////
    public function cargaFletesForm()
    {
        $fletes = CiudadesFlete::all();
        return view('mantenedores.fletes-masive', compact("fletes"));
    }


    public function importFletes(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);
        // procesamiento inicial o confirmar guardado de datos
        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}


                $motivos = [];
                // Validaciones generales
                // dd(is_numeric($row->tolerancia_gramaje_real), $row->tolerancia_gramaje_real, in_array($row->onda, $ondas_validas));
                $ciudad = (trim($row->ciudad) != "") ? $row->ciudad : false;
                $clp_camion = is_numeric($row->clp_camion) ? $row->clp_camion : false;
                $clp_pallet_osorno = is_numeric($row->clp_pallet_osorno) ? $row->clp_pallet_osorno : false;
                $clp_pallet_tiltil = is_numeric($row->clp_pallet_tiltil) ? $row->clp_pallet_tiltil : false;
                $clp_pallet_buin = is_numeric($row->clp_pallet_buin) ? $row->clp_pallet_buin : false;
                // dd($row->clp_camion, !$clp_camion, $row->clp_camion != 0, is_numeric($row->clp_camion));
                if (!$ciudad) $motivos[] = " Ciudad";
                if ($clp_camion === false) $motivos[] = " CLP/Camion";
                if ($clp_pallet_osorno === false) $motivos[] = " CLP/Pallet Osorno";
                if ($clp_pallet_tiltil === false) $motivos[] = " CLP/Pallet TilTil";
                if ($clp_pallet_buin === false) $motivos[] = " CLP/Pallet Buin";

                if (count($motivos) >= 1) {
                    $fleteErroneo = new stdClass();
                    $fleteErroneo->linea = $key + 2;
                    $fleteErroneo->motivos = $motivos;
                    $fletesInvalidos[] = $fleteErroneo;
                    continue;
                }

                $flete = CiudadesFlete::where('id', $row->id)->first();
                // dd($user);
                if ($flete) {
                    // dd(trim($row->clp_pallet_osorno), $row, $flete);
                    $flete->ciudad = trim($row->ciudad);
                    $flete->valor_usd_camion = trim($row->clp_camion);
                    $flete->clp_pallet_osorno = trim($row->clp_pallet_osorno);
                    $flete->clp_pallet_tiltil = trim($row->clp_pallet_tiltil);
                    $flete->clp_pallet_buin = trim($row->clp_pallet_buin);
                    $flete->active = trim($row->active);
                    // dd($flete, $flete->isDirty(), $flete->getChanges(), $flete->isDirty("desperdicio"));
                    if ($flete->isDirty()) {
                        $flete->orden = $key + 2;
                        // para marcar un flete como inactivado debe ser originalmente activo
                        if (($flete->getOriginal("active") == 1 && $flete->active == 0)) {
                            $fletesInactivados[] = $flete;
                        } else {
                            $fletesActualizados[] = $flete;

                            // dd($fletesActualizados, $flete->isDirty(), $flete);
                        }
                        // dd($papel->getDirty(), $papel);

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($flete, $row, "UPDATE", $codigo_operacion);
                            $flete->save();
                            continue;
                        }
                    } else {
                        // Si no hay cambios lo unico q actualizamos es el orden segun archivo
                        $flete->update(["orden" => $key + 2]);
                    }
                } else {
                    $flete = new CiudadesFlete();
                    $flete->ciudad = trim($row->ciudad);
                    $flete->valor_usd_camion = trim($row->clp_camion);
                    $flete->clp_pallet_osorno = trim($row->clp_pallet_osorno);
                    $flete->clp_pallet_tiltil = trim($row->clp_pallet_tiltil);
                    $flete->clp_pallet_buin = trim($row->clp_pallet_buin);
                    $flete->active = trim($row->active);

                    $flete->orden = $key + 2;
                    // Solo si el proceso ya es de carga guardamos y continuamos
                    if ($proceso == "cargaCompleta") {
                        $changelog = changelog($flete, $row, "INSERT", $codigo_operacion);
                        $flete->save();
                        $changelog->update(['item_id' => $flete->id]);
                        continue;
                    }

                    $flete->linea = $key + 2;
                    $fletes[] = $flete;
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $fletes_ingresados = [];
        $fletes_actualizados = [];
        $fletes_inactivados = [];
        $fletes_error = [];

        if (isset($fletes)) {
            $exito = 'Se ingresaron los siguientes flete';
            $fletes_ingresados = $fletes;
            // $plantas_ingresadas = Planta::whereIn('id', $ids)->with('status')->paginate(10);
        }
        if (isset($fletesActualizados)) {
            $updated = 'Los siguientes flete fueron actualizados:';
            $fletes_actualizados = $fletesActualizados;
        }
        if (isset($fletesInactivados)) {
            $updated = 'Los siguientes flete fueron actualizados:';
            $fletes_inactivados = $fletesInactivados;
        }
        if (isset($fletesInvalidos)) {
            $error = 'Los siguientes flete tienen 1 o mas errores';
            $fletes_error = $fletesInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'fletes' => $fletes_ingresados,
            'fletes_actualizados' => $fletes_actualizados,
            'fletes_inactivados' => $fletes_inactivados,
            'fletes_error' => $fletes_error

        ], 200);
    }

    public function descargar_excel_fletes(Request $request)
    {
        $titulo = "Listado Fletes";
        $fletes = CiudadesFlete::all();
        // dd($fletes);
        $fletes_array[] = array(
            'ID',
            'ciudad',
            'clp_camion',
            'clp_pallet_osorno',
            'clp_pallet_tiltil',
            'clp_pallet_buin',
            'active'
            // Valor Camion USD (Esquineros)
        );

        foreach ($fletes as $flete) {
            $fletes_array[] = array(
                $flete->id,
                $flete->ciudad,
                $flete->valor_usd_camion,
                $flete->clp_pallet_osorno,
                $flete->clp_pallet_tiltil,
                $flete->clp_pallet_buin,
                $flete->active,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($fletes_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Fletes', function ($sheet) use ($fletes_array) {
                $sheet->fromArray($fletes_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }


    //////////////MERMAS CORRUGADORAS//////////////////
    ////////////////////////////////////////
    //////////////MERMAS CORRUGADORAS/////////////////
    ////////////////////////////////////////
    //////////////MERMAS CORRUGADORAS//////////////////
    public function cargaMermasCorrugadorasForm()
    {
        $mermasCorrugadoras = MermaCorrugadora::with('carton', 'planta')->get();
        return view('mantenedores.mermas-corrugadoras-masive', compact("mermasCorrugadoras"));
    }


    public function importMermasCorrugadoras(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        $plantas = Planta::pluck('id', 'nombre')->toArray();
        $cartons = Carton::where('tipo', "!=", "Esquinero")->pluck('id', 'codigo')->toArray();
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}

                $motivos = [];
                $porcentaje_merma_corrugadora = is_numeric($row->porcentaje_merma_corrugadora) ? $row->porcentaje_merma_corrugadora : false;
                $planta = array_key_exists($row->planta, $plantas) ? $plantas[$row->planta] : false;
                $carton = array_key_exists($row->carton, $cartons) ? $cartons[$row->carton] : false;

                if ($porcentaje_merma_corrugadora === false) $motivos[] = " Porcentaje Merma";
                if (!$planta) $motivos[] = " Planta";
                if (!$carton) $motivos[] = " Cartón";

                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $mermasCorrugadoraErronea = new stdClass();
                    $mermasCorrugadoraErronea->linea = $key + 2;
                    $mermasCorrugadoraErronea->motivos = $motivos;
                    // $mermasCorrugadorasInvalidos[] = $key + 2;
                    $mermasCorrugadorasInvalidas[] = $mermasCorrugadoraErronea;
                    continue;
                }

                $mermasCorrugadora = MermaCorrugadora::where('id', $row->id)->first();
                // dd($user);
                if ($mermasCorrugadora) {
                    $mermasCorrugadora->porcentaje_merma_corrugadora = trim($row->porcentaje_merma_corrugadora);
                    $mermasCorrugadora->planta_id = $planta;
                    $mermasCorrugadora->carton_id = $carton;
                    // dd($mermasCorrugadora, $mermasCorrugadora->isDirty(), $mermasCorrugadora->getChanges(), $mermasCorrugadora->isDirty("desperdicio"));
                    if ($mermasCorrugadora->isDirty()) {
                        // $mermasCorrugadora->orden = $key + 2;
                        // para marcar un mermasCorrugadora como inactivado debe ser originalmente activo
                        if (($mermasCorrugadora->getOriginal("active") == 1 && $mermasCorrugadora->active == 0)) {
                            $mermasCorrugadorasInactivadas[] = $mermasCorrugadora;
                        } else {
                            $mermasCorrugadorasActualizadas[] = $mermasCorrugadora;
                        }
                        // dd($mermasCorrugadora->getDirty(), $mermasCorrugadora);

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($mermasCorrugadora, $row, "UPDATE", $codigo_operacion);
                            $mermasCorrugadora->save();
                            continue;
                        }
                    } else {
                        // Si no hay cambios lo unico q actualizamos es el orden segun archivo
                        // $mermasCorrugadora->update(["orden" => $key + 2]);
                    }
                } else {
                    $mermasCorrugadora = new MermaCorrugadora();
                    $mermasCorrugadora->porcentaje_merma_corrugadora = trim($row->porcentaje_merma_corrugadora);
                    $mermasCorrugadora->planta_id = $planta;
                    $mermasCorrugadora->carton_id = $carton;
                    // $mermasCorrugadora->orden = $key + 2;
                    // Solo si el proceso ya es de carga guardamos y continuamos
                    if ($proceso == "cargaCompleta") {
                        $changelog = changelog($mermasCorrugadora, $row, "INSERT", $codigo_operacion);
                        $mermasCorrugadora->save();
                        $changelog->update(['item_id' => $mermasCorrugadora->id]);
                        continue;
                    }

                    $mermasCorrugadora->linea = $key + 2;
                    $mermasCorrugadoras[] = $mermasCorrugadora;
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $mermasCorrugadora_ingresadas = [];
        $mermasCorrugadora_actualizadas = [];
        $mermasCorrugadora_inactivadas = [];
        $mermasCorrugadora_error = [];

        if (isset($mermasCorrugadoras)) {
            $exito = 'Se ingresaron los siguientes mermasCorrugadoras';
            $mermasCorrugadoras_ingresadas = $mermasCorrugadoras;
            // $plantas_ingresadas = Planta::whereIn('id', $ids)->with('status')->paginate(10);
        }
        if (isset($mermasCorrugadorasActualizadas)) {
            $updated = 'Los siguientes mermasCorrugadora fueron actualizadas:';
            $mermasCorrugadora_actualizadas = $mermasCorrugadorasActualizadas;
        }
        if (isset($mermasCorrugadorasInactivadas)) {
            $updated = 'Los siguientes mermasCorrugadora fueron actualizadas:';
            $mermasCorrugadora_inactivadas = $mermasCorrugadorasInactivadas;
        }
        if (isset($mermasCorrugadorasInvalidas)) {
            $error = 'Los siguientes mermasCorrugadora tienen 1 o mas errores';
            $mermasCorrugadora_error = $mermasCorrugadorasInvalidas;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'mermasCorrugadoras' => $mermasCorrugadora_ingresadas,
            'mermasCorrugadoras_actualizados' => $mermasCorrugadora_actualizadas,
            'mermasCorrugadoras_inactivados' => $mermasCorrugadora_inactivadas,
            'mermasCorrugadoras_error' => $mermasCorrugadora_error

        ], 200);
    }

    public function descargar_excel_mermas_corrugadoras(Request $request)
    {
        $titulo = "Listado Mermas Corrugadoras";
        $mermas_corrugadoras = MermaCorrugadora::with('carton', 'planta')->get();
        // dd($mermas_corrugadoras);
        $mermas_corrugadoras_array[] = array(
            'ID',
            "porcentaje_merma_corrugadora",
            "planta",
            "carton"

        );

        foreach ($mermas_corrugadoras as $merma) {
            $mermas_corrugadoras_array[] = array(
                $merma->id,
                $merma->porcentaje_merma_corrugadora,
                $merma->planta->nombre,
                $merma->carton->codigo,
                // $merma->active,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($mermas_corrugadoras_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Mermas Corrugadoras', function ($sheet) use ($mermas_corrugadoras_array) {
                $sheet->fromArray($mermas_corrugadoras_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }


    //////////////MERMAS CONVERTIDORAS//////////////////
    ////////////////////////////////////////
    //////////////MERMAS CONVERTIDORAS/////////////////
    ////////////////////////////////////////
    //////////////MERMAS CONVERTIDORAS//////////////////
    public function cargaMermasConvertidorasForm()
    {
        $mermasConvertidoras = MermaConvertidora::with('rubro', 'proceso', 'planta')->get();
        return view('mantenedores.mermas-convertidoras-masive', compact("mermasConvertidoras"));
    }


    public function importMermasConvertidoras(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $plantas = Planta::pluck('id', 'nombre')->toArray();
        $rubros = Rubro::where('id', "!=", 5)->pluck('id', 'descripcion')->toArray();
        $procesos = Process::where('active', 1)->whereNotIn('id', [6, 8])->orderBy("descripcion")->pluck('id', 'descripcion')->toArray();

        // proceso de carga de archivo
        $proceso_carga = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}

                $motivos = [];
                $porcentaje_merma_convertidora = is_numeric($row->porcentaje_merma_convertidora) ? $row->porcentaje_merma_convertidora : false;
                $planta = array_key_exists($row->planta, $plantas) ? $plantas[$row->planta] : false;
                $proceso = array_key_exists($row->proceso, $procesos) ? $procesos[$row->proceso] : false;
                $rubro = array_key_exists($row->rubro, $rubros) ? $rubros[$row->rubro] : false;

                if ($porcentaje_merma_convertidora === false) $motivos[] = " Porcentaje Merma";
                if (!$planta) $motivos[] = " Planta";
                if (!$rubro) $motivos[] = " Rubro";
                if (!$proceso) $motivos[] = " Proceso";

                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $mermasConvertidoraErronea = new stdClass();
                    $mermasConvertidoraErronea->linea = $key + 2;
                    $mermasConvertidoraErronea->motivos = $motivos;
                    // $mermasConvertidorasInvalidos[] = $key + 2;
                    $mermasConvertidorasInvalidas[] = $mermasConvertidoraErronea;
                    continue;
                }

                $mermasConvertidora = MermaConvertidora::where('id', $row->id)->first();
                // dd($user);
                if ($mermasConvertidora) {

                    $mermasConvertidora->porcentaje_merma_convertidora = trim($row->porcentaje_merma_convertidora);
                    $mermasConvertidora->planta_id = $planta;
                    $mermasConvertidora->process_id = $proceso;
                    $mermasConvertidora->rubro_id = $rubro;
                    // $mermasConvertidora->active = trim($row->active);
                    // dd($mermasConvertidora, $mermasConvertidora->isDirty(), $mermasConvertidora->getChanges(), $mermasConvertidora->isDirty("desperdicio"));
                    if ($mermasConvertidora->isDirty()) {
                        $mermasConvertidorasActualizadas[] = $mermasConvertidora;

                        // dd($mermasConvertidora->getDirty(), $mermasConvertidora);

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso_carga == "cargaCompleta") {
                            changelog($mermasConvertidora, $row, "UPDATE", $codigo_operacion);
                            $mermasConvertidora->save();
                            continue;
                        }
                    } else {
                        // Si no hay cambios lo unico q actualizamos es el orden segun archivo
                        // $mermasConvertidora->update(["orden" => $key + 2]);
                    }
                } else {
                    $mermasConvertidora = new MermaConvertidora();

                    $mermasConvertidora->porcentaje_merma_corrugadora = trim($row->porcentaje_merma_convertidora);
                    $mermasConvertidora->planta_id = $planta;
                    $mermasConvertidora->proccess_id = $proceso;
                    $mermasConvertidora->rubro_id = $rubro;
                    // $mermasConvertidora->active = trim($row->active);

                    // $mermasConvertidora->orden = $key + 2;
                    // Solo si el proceso ya es de carga guardamos y continuamos
                    if ($proceso_carga == "cargaCompleta") {
                        $changelog = changelog($mermasConvertidora, $row, "INSERT", $codigo_operacion);
                        $mermasConvertidora->save();
                        $changelog->update(['item_id' => $mermasConvertidora->id]);
                        continue;
                    }

                    $mermasConvertidora->linea = $key + 2;
                    $mermasConvertidoras[] = $mermasConvertidora;
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso_carga == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $mermasConvertidoras_ingresadas = [];
        $mermasConvertidoras_actualizadas = [];
        $mermasConvertidoras_inactivadas = [];
        $mermasConvertidoras_error = [];

        if (isset($mermasConvertidoras)) {
            $exito = 'Se ingresaron los siguientes mermas Convertidoras';
            $mermasConvertidoras_ingresadas = $mermasConvertidoras;
            // $plantas_ingresadas = Planta::whereIn('id', $ids)->with('status')->paginate(10);
        }
        if (isset($mermasConvertidorasActualizadas)) {
            $updated = 'Los siguientes mermas Convertidoras fueron actualizadas:';
            $mermasConvertidoras_actualizadas = $mermasConvertidorasActualizadas;
        }
        if (isset($mermasConvertidorasInactivadas)) {
            $updated = 'Los siguientes mermas Convertidoras fueron actualizadas:';
            $mermasConvertidoras_inactivadas = $mermasConvertidorasInactivadas;
        }
        if (isset($mermasConvertidorasInvalidas)) {
            $error = 'Los siguientes mermas Convertidoras tienen 1 o mas errores';
            $mermasConvertidoras_error = $mermasConvertidorasInvalidas;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'mermasConvertidoras' => $mermasConvertidoras_ingresadas,
            'mermasConvertidoras_actualizados' => $mermasConvertidoras_actualizadas,
            'mermasConvertidoras_inactivados' => $mermasConvertidoras_inactivadas,
            'mermasConvertidoras_error' => $mermasConvertidoras_error

        ], 200);
    }

    public function descargar_excel_mermas_convertidoras(Request $request)
    {
        $titulo = "Listado Mermas Convertidoras";
        $mermas_convertidoras = MermaConvertidora::with('rubro', 'proceso', 'planta')->get();
        // dd($mermas_convertidoras);
        $mermas_convertidoras_array[] = array(
            'ID',
            "porcentaje_merma_convertidora",
            "planta",
            "proceso",
            "rubro"

        );

        foreach ($mermas_convertidoras as $merma) {
            $mermas_convertidoras_array[] = array(
                $merma->id,
                $merma->porcentaje_merma_convertidora,
                $merma->planta->nombre,
                $merma->proceso->descripcion,
                $merma->rubro->descripcion,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($mermas_convertidoras_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Mermas Convertidoras', function ($sheet) use ($mermas_convertidoras_array) {
                $sheet->fromArray($mermas_convertidoras_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    //////////////PALETIZADO//////////////////
    ////////////////////////////////////////
    //////////////PALETIZADO/////////////////
    ////////////////////////////////////////
    //////////////PALETIZADO//////////////////
    public function cargaPaletizadosForm()
    {
        $paletizados = DetallePrecioPalletizado::all();
        return view('mantenedores.detalles-paletizados-masive', compact("paletizados"));
    }


    public function importPaletizados(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}

                $motivos = [];
                $tipo_palletizado = (trim($row->tipo_palletizado) != "") ? $row->tipo_palletizado : false;
                $tarima_nacional = is_numeric($row->tarima_nacional) ? (int) $row->tarima_nacional : false;
                $tarima_exportacion = is_numeric($row->tarima_exportacion) ? (int) $row->tarima_exportacion : false;
                $liston_nacional = is_numeric($row->liston_nacional) ? (int) $row->liston_nacional : false;
                $liston_exportacion = is_numeric($row->liston_exportacion) ? (int) $row->liston_exportacion : false;
                $tabla_tarima = is_numeric($row->tabla_tarima) ? (int) $row->tabla_tarima : false;
                $stretch_film = is_numeric($row->stretch_film) ? (int) $row->stretch_film : false;
                $sellos = is_numeric($row->sellos) ? (int) $row->sellos : false;
                $zunchos = is_numeric($row->zunchos) ? (int) $row->zunchos : false;
                $fundas = is_numeric($row->fundas) ? (int) $row->fundas : false;
                $cordel_y_clavos = is_numeric($row->cordel_y_clavos) ? (int) $row->cordel_y_clavos : false;
                $maquila = is_numeric($row->maquila) ? (int) $row->maquila : false;

                if (!$tipo_palletizado) $motivos[] = " Tipo Palletizado";
                if ($tarima_nacional === false) $motivos[] = " tarima_nacional";
                if ($tarima_exportacion === false) $motivos[] = " tarima_exportacion";
                if ($liston_nacional === false) $motivos[] = " liston_nacional";
                if ($liston_exportacion === false) $motivos[] = " liston_exportacion";
                if ($tabla_tarima === false) $motivos[] = " tabla_tarima";
                if ($stretch_film === false) $motivos[] = " stretch_film";
                if ($sellos === false) $motivos[] = " sellos";
                if ($zunchos === false) $motivos[] = " zunchos";
                if ($fundas === false) $motivos[] = " fundas";
                if ($cordel_y_clavos === false) $motivos[] = " cordel_y_clavos";
                if ($maquila === false) $motivos[] = " maquila";


                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $paletizadoErroneo = new stdClass();
                    $paletizadoErroneo->linea = $key + 2;
                    $paletizadoErroneo->motivos = $motivos;
                    $paletizadosInvalidos[] = $paletizadoErroneo;
                    continue;
                }

                $paletizados = DetallePrecioPalletizado::where('id', $row->id)->first();
                // dd($user);
                if ($paletizados) {
                    $paletizados->tipo_palletizado  = trim($row->tipo_palletizado);
                    $paletizados->tarima_nacional  = (int) ($row->tarima_nacional);
                    $paletizados->tarima_exportacion  = (int) ($row->tarima_exportacion);
                    $paletizados->liston_nacional  = (int) ($row->liston_nacional);
                    $paletizados->liston_exportacion  = (int) ($row->liston_exportacion);
                    $paletizados->tabla_tarima  = (int) ($row->tabla_tarima);
                    $paletizados->stretch_film  = (int) ($row->stretch_film);
                    $paletizados->sellos  = (int) ($row->sellos);
                    $paletizados->zunchos  = (int) ($row->zunchos);
                    $paletizados->fundas  = (int) ($row->fundas);
                    $paletizados->cordel_y_clavos  = (int) ($row->cordel_y_clavos);
                    $paletizados->maquila  = (int) ($row->maquila);
                    if ($paletizados->isDirty()) {
                        $paletizadosActualizados[] = $paletizados;
                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($paletizados, $row, "UPDATE", $codigo_operacion);
                            $paletizados->save();
                            continue;
                        }
                    }
                }
            }
        }
        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");
        $paletizados_actualizados = [];
        $paletizados_error = [];

        if (isset($paletizadosActualizados)) {
            $paletizados_actualizados = $paletizadosActualizados;
        }
        if (isset($paletizadosInvalidos)) {
            $paletizados_error = $paletizadosInvalidos;
        }
        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'paletizados_actualizados' => $paletizados_actualizados,
            'paletizados_error' => $paletizados_error

        ], 200);
    }

    public function descargar_excel_paletizados(Request $request)
    {
        $titulo = "Listado Paletizados";
        $paletizados = DetallePrecioPalletizado::all();
        // dd($paletizados);
        $paletizados_array[] = array(
            'ID',
            'Tipo Palletizado',
            'Tarima Nacional',
            'Tarima Exportacion',
            'Liston Nacional',
            'Liston Exportacion',
            'Tabla Tarima',
            'Stretch Film',
            'Sellos',
            'Zunchos',
            'Fundas',
            'Cordel y Clavos',
            'Maquila',
        );

        foreach ($paletizados as $paletizado) {
            $paletizados_array[] = array(
                $paletizado->id,
                $paletizado->tipo_palletizado,
                $paletizado->tarima_nacional,
                $paletizado->tarima_exportacion,
                $paletizado->liston_nacional,
                $paletizado->liston_exportacion,
                $paletizado->tabla_tarima,
                $paletizado->stretch_film,
                $paletizado->sellos,
                $paletizado->zunchos,
                $paletizado->fundas,
                $paletizado->cordel_y_clavos,
                $paletizado->maquila,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($paletizados_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Paletizados', function ($sheet) use ($paletizados_array) {
                $sheet->fromArray($paletizados_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }



    //////////////INSUMOS PALETIZADO//////////////////
    ////////////////////////////////////////
    //////////////INSUMOS PALETIZADO/////////////////
    ////////////////////////////////////////
    //////////////INSUMOS PALETIZADO//////////////////
    public function cargaInsumosPaletizadosForm()
    {
        $insumosPalletizados = InsumosPalletizado::all();
        return view('mantenedores.insumos-paletizados-masive', compact("insumosPalletizados"));
    }


    public function importInsumosPaletizados(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}

                $motivos = [];

                // Validaciones generales
                $precio = is_numeric($row->precio) ? $row->precio : false;
                if (!$precio && $precio !== 0) $motivos[] = " Precio Invalido";

                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $insumoPaletizadoErroneo = new stdClass();
                    $insumoPaletizadoErroneo->linea = $key + 2;
                    $insumoPaletizadoErroneo->motivos = $motivos;
                    // $InsumoPaletizadoesInvalidos[] = $key + 2;
                    $insumoPalletizadosInvalidos[] = $insumoPaletizadoErroneo;
                    continue;
                }

                $insumoPalletizado = InsumosPalletizado::where('id', $row->id)->first();
                // dd($user);
                if ($insumoPalletizado) {
                    // $insumoPalletizado->insumo = trim($row->insumo);
                    $insumoPalletizado->precio = trim($row->precio);


                    if ($insumoPalletizado->isDirty()) {

                        $insumoPalletizadosActualizados[] = $insumoPalletizado;

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($insumoPalletizado, $row, "UPDATE", $codigo_operacion);
                            $insumoPalletizado->save();
                            continue;
                        }
                    }
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $insumoPaletizados_actualizados = [];
        $insumoPaletizados_error = [];

        if (isset($insumoPalletizadosActualizados)) {
            $updated = 'Los siguientes insumoPalletizado fueron actualizados:';
            $insumoPaletizados_actualizados = $insumoPalletizadosActualizados;
        }
        if (isset($insumoPalletizadosInvalidos)) {
            $error = 'Los siguientes insumoPalletizado tienen 1 o mas errores';
            $insumoPaletizados_error = $insumoPalletizadosInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'insumoPaletizados_actualizados' => $insumoPaletizados_actualizados,
            'insumoPaletizados_error' => $insumoPaletizados_error

        ], 200);
    }

    public function descargar_excel_insumos_paletizados(Request $request)
    {
        $titulo = "Listado Insumos Paletizados";
        $insumos_paletizados = InsumosPalletizado::all();
        // dd($paletizados);
        $insumos_paletizados_array[] = array(
            'ID',
            'insumo',
            'precio'

        );

        foreach ($insumos_paletizados as $insumo) {
            $insumos_paletizados_array[] = array(
                $insumo->id,
                $insumo->insumo,
                $insumo->precio,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($insumos_paletizados_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Insumos Paletizados', function ($sheet) use ($insumos_paletizados_array) {
                $sheet->fromArray($insumos_paletizados_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    //////////////TARIFARIO DE MARGENES//////////////////
    ////////////////////////////////////////
    //////////////TARIFARIO DE MARGENES/////////////////
    ////////////////////////////////////////
    //////////////TARIFARIO DE MARGENES//////////////////
    public function cargaTarifariosForm()
    {
        $tarifarios = Tarifario::all();
        return view('mantenedores.tarifarios-masive', compact("tarifarios"));
    }


    public function importTarifarios(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}

                $motivos = [];

                // Validaciones generales
                // dd(array_key_exists((int) $row->codigo_onda_1_2, $papeles), (int) $row->codigo_onda_1_2, $papeles);
                // $codigo_tapa_interior = array_key_exists((int) $row->codigo_tapa_interior, $papeles) ? (int) $row->codigo_tapa_interior : false;
                // if (!$codigo_tapa_interior && $codigo_tapa_interior !== 0) $motivos[] = "Codigo Tapa Interior No existe";

                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $tarifarioErroneo = new stdClass();
                    $tarifarioErroneo->linea = $key + 2;
                    $tarifarioErroneo->motivos = $motivos;
                    // $tarifarioesInvalidos[] = $key + 2;
                    $tarifariosInvalidos[] = $tarifarioErroneo;
                    continue;
                }

                $tarifario = Tarifario::where('codigo', $row->codigo)->first();
                // dd($user);
                if ($tarifario) {
                    $tarifario->active = trim($row->active);
                    // dd($tarifario, $tarifario->isDirty(), $tarifario->getChanges(), $tarifario->isDirty("desperdicio"));
                    if ($tarifario->isDirty()) {
                        $tarifario->orden = $key + 2;
                        // para marcar un tarifario como inactivado debe ser originalmente activo
                        if (($tarifario->getOriginal("active") == 1 && $tarifario->active == 0)) {
                            $tarifariosInactivados[] = $tarifario;
                        } else {
                            $tarifariosActualizados[] = $tarifario;
                        }
                        // dd($tarifario->getDirty(), $tarifario);

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        changelog($tarifario, $row, "UPDATE", $codigo_operacion);
                        if ($proceso == "cargaCompleta") {
                            $tarifario->save();
                            continue;
                        }
                    } else {
                        // Si no hay cambios lo unico q actualizamos es el orden segun archivo
                        $tarifario->update(["orden" => $key + 2]);
                    }
                } else {
                    $tarifario = new Paper();
                    $tarifario->active = trim($row->active);

                    $tarifario->orden = $key + 2;
                    // Solo si el proceso ya es de carga guardamos y continuamos
                    $changelog = changelog($tarifario, $row, "INSERT", $codigo_operacion);
                    if ($proceso == "cargaCompleta") {
                        $tarifario->save();
                        $changelog->update(['item_id' => $tarifario->id]);
                        continue;
                    }

                    $tarifario->linea = $key + 2;
                    $tarifarios[] = $tarifario;
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $tarifarios_ingresados = [];
        $tarifarios_actualizados = [];
        $tarifarios_inactivados = [];
        $tarifarios_error = [];

        if (isset($tarifarios)) {
            $exito = 'Se ingresaron los siguientes tarifarios';
            $tarifarios_ingresados = $tarifarios;
            // $plantas_ingresadas = Planta::whereIn('id', $ids)->with('status')->paginate(10);
        }
        if (isset($tarifariosActualizados)) {
            $updated = 'Los siguientes tarifarios fueron actualizados:';
            $tarifarios_actualizados = $tarifariosActualizados;
        }
        if (isset($tarifariosInactivados)) {
            $updated = 'Los siguientes tarifarios fueron actualizados:';
            $tarifarios_inactivados = $tarifariosInactivados;
        }
        if (isset($tarifariosInvalidos)) {
            $error = 'Los siguientes tarifarios tienen 1 o mas errores';
            $tarifarios_error = $tarifariosInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'tarifarios' => $tarifarios_ingresados,
            'tarifarios_actualizados' => $tarifarios_actualizados,
            'tarifarios_inactivados' => $tarifarios_inactivados,
            'tarifarios_error' => $tarifarios_error

        ], 200);
    }

    public function descargar_excel_tarifarios(Request $request)
    {
        $titulo = "Listado Tarifarios";
        $tarifarios = Tarifario::all();
        // dd($papeles);
        $tarifarios_array[] = array(
            'ID',
            'active'

        );

        foreach ($tarifarios as $tarifario) {
            $tarifarios_array[] = array(
                $tarifario->id,
                $tarifario->active,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($tarifarios_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Papeles', function ($sheet) use ($tarifarios_array) {
                $sheet->fromArray($tarifarios_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }



    //////////////CONSUMO ADHESIVO//////////////////
    ////////////////////////////////////////
    //////////////CONSUMO ADHESIVO/////////////////
    ////////////////////////////////////////
    //////////////CONSUMO ADHESIVO//////////////////
    public function cargaConsumoAdhesivoForm()
    {
        $consumoAdhesivos = ConsumoAdhesivo::all();
        return view('mantenedores.consumos-adhesivos-masive', compact("consumoAdhesivos"));
    }


    public function importConsumoAdhesivo(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $plantas = Planta::pluck('id', 'nombre')->toArray();
        $proceso_carga = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}
                $ondas_validas = ["C", "B",  "E"];
                $motivos = [];

                $adhesivo_corrugado = is_numeric($row->adhesivo_corrugado) ? (int) $row->adhesivo_corrugado : false;
                $adhesivo_powerply = is_numeric($row->adhesivo_powerply) ? (int) $row->adhesivo_powerply : false;
                $planta = array_key_exists($row->planta, $plantas) ? $plantas[$row->planta] : false;
                $onda = in_array($row->onda, $ondas_validas) ? $row->onda : false;
                if ($adhesivo_corrugado === false) $motivos[] = " Adhesivo Corrugado";
                if ($adhesivo_powerply === false) $motivos[] = " Adhesivo Powerply";
                if (!$planta) $motivos[] = " Planta";
                if (!$onda) $motivos[] = " Onda";

                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $consumosAdhesivo = new stdClass();
                    $consumosAdhesivo->linea = $key + 2;
                    $consumosAdhesivo->motivos = $motivos;
                    $consumosAdhesivosInvalidos[] = $consumosAdhesivo;
                    continue;
                }


                $consumoAdhesivo = ConsumoAdhesivo::where('id', $row->id)->first();
                // dd($user);
                if ($consumoAdhesivo) {

                    $consumoAdhesivo->onda = trim($row->onda);
                    $consumoAdhesivo->adhesivo_corrugado = trim($row->adhesivo_corrugado);
                    $consumoAdhesivo->adhesivo_powerply = trim($row->adhesivo_powerply);
                    $consumoAdhesivo->planta_id = $planta;
                    // dd($consumoAdhesivo, $consumoAdhesivo->isDirty(), $consumoAdhesivo->getChanges(), $consumoAdhesivo->isDirty("desperdicio"));
                    if ($consumoAdhesivo->isDirty()) {
                        // $consumoAdhesivo->orden = $key + 2;
                        // para marcar un consumoAdhesivo como inactivado debe ser originalmente activo
                        if (($consumoAdhesivo->getOriginal("active") == 1 && $consumoAdhesivo->active == 0)) {
                            $consumosAdhesivosInactivados[] = $consumoAdhesivo;
                        } else {
                            $consumosAdhesivosActualizados[] = $consumoAdhesivo;
                        }
                        // dd($consumoAdhesivo->getDirty(), $consumoAdhesivo);

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso_carga == "cargaCompleta") {
                            changelog($consumoAdhesivo, $row, "UPDATE", $codigo_operacion);
                            $consumoAdhesivo->save();
                            continue;
                        }
                    }
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso_carga == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $consumosAdhesivos_ingresados = [];
        $consumosAdhesivos_actualizados = [];
        $consumosAdhesivos_inactivados = [];
        $consumosAdhesivos_error = [];

        if (isset($consumosAdhesivos)) {
            $exito = 'Se ingresaron los siguientes Consumos Adhesivos';
            $consumosAdhesivos_ingresados = [];
            // $plantas_ingresadas = Planta::whereIn('id', $ids)->with('status')->paginate(10);
        }
        if (isset($consumosAdhesivosActualizados)) {
            $updated = 'Los siguientes Consumos Adhesivos fueron actualizados:';
            $consumosAdhesivos_actualizados = $consumosAdhesivosActualizados;
        }
        if (isset($consumosAdhesivosInactivados)) {
            $updated = 'Los siguientes Consumos Adhesivos fueron actualizados:';
            $consumosAdhesivos_inactivados = $consumosAdhesivosInactivados;
        }
        if (isset($consumosAdhesivosInvalidos)) {
            $error = 'Los siguientes Consumos Adhesivos tienen 1 o mas errores';
            $consumosAdhesivos_error = $consumosAdhesivosInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'consumo_adhesivos' => $consumosAdhesivos_ingresados,
            'consumo_adhesivos_actualizados' => $consumosAdhesivos_actualizados,
            'consumo_adhesivos_inactivados' => $consumosAdhesivos_inactivados,
            'consumo_adhesivos_error' => $consumosAdhesivos_error

        ], 200);
    }

    public function descargar_excel_consumo_adhesivos(Request $request)
    {
        $titulo = "Listado Consumo Adhesivo";
        $consumo_adhesivos = ConsumoAdhesivo::all();
        // dd($consumo_adhesivo);
        $consumo_adhesivo_array[] = array(
            'ID',
            'planta',
            'onda',
            'adhesivo_corrugado',
            'adhesivo_powerply'
        );

        foreach ($consumo_adhesivos as $consumo_adhesivo) {
            $consumo_adhesivo_array[] = array(
                $consumo_adhesivo->id,
                $consumo_adhesivo->planta->nombre,
                $consumo_adhesivo->onda,
                $consumo_adhesivo->adhesivo_corrugado,
                $consumo_adhesivo->adhesivo_powerply,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($consumo_adhesivo_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Consumo Adhesivo', function ($sheet) use ($consumo_adhesivo_array) {
                $sheet->fromArray($consumo_adhesivo_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }


    //////////////CONSUMO ADHESIVO PEGADO//////////////////
    ////////////////////////////////////////
    //////////////CONSUMO ADHESIVO PEGADO/////////////////
    ////////////////////////////////////////
    //////////////CONSUMO ADHESIVO PEGADO//////////////////
    public function cargaConsumoAdhesivoPegadoForm()
    {
        $consumoAdhesivosPegados = ConsumoAdhesivoPegado::all();
        return view('mantenedores.consumos-adhesivos-pegados-masive', compact("consumoAdhesivosPegados"));
    }


    public function importConsumoAdhesivoPegado(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $plantas = Planta::pluck('id', 'nombre')->toArray();
        $procesos = Process::where('active', 1)->whereNotIn('id', [6, 8])->orderBy("descripcion")->pluck('id', 'descripcion')->toArray();
        $proceso_carga = request("proceso");

        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}
                $motivos = [];

                $planta = isset($row->planta) && trim($row->planta) != "" && array_key_exists((string) ($row->planta), $plantas) ? $plantas[$row->planta] : false;
                $proceso = isset($row->proceso) && trim($row->proceso) != ""  && array_key_exists((string) ($row->proceso), $procesos) ? $procesos[$row->proceso] : false;
                $consumo_adhesivo_pegado = is_numeric($row->consumo_adhesivo_pegado) ? (int) $row->consumo_adhesivo_pegado : false;

                if ($consumo_adhesivo_pegado === false) $motivos[] = " Consumo Adhesivo Pegado";
                if (!$planta) $motivos[] = " Planta";
                if (!$proceso) $motivos[] = " Proceso";

                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $consumosAdhesivoPegado = new stdClass();
                    $consumosAdhesivoPegado->linea = $key + 2;
                    $consumosAdhesivoPegado->motivos = $motivos;
                    $consumosAdhesivosPegadosInvalidos[] = $consumosAdhesivoPegado;
                    continue;
                }


                $ConsumoAdhesivoPegado = ConsumoAdhesivoPegado::where('id', $row->id)->first();
                // dd($user);
                if ($ConsumoAdhesivoPegado) {

                    $ConsumoAdhesivoPegado->consumo_adhesivo_pegado_gr_caja = trim($consumo_adhesivo_pegado);
                    $ConsumoAdhesivoPegado->planta_id = $planta;
                    $ConsumoAdhesivoPegado->process_id = $proceso;

                    // dd($ConsumoAdhesivoPegado, $ConsumoAdhesivoPegado->isDirty(), $ConsumoAdhesivoPegado->getChanges(), $ConsumoAdhesivoPegado->isDirty("desperdicio"));
                    if ($ConsumoAdhesivoPegado->isDirty()) {
                        // $ConsumoAdhesivoPegado->orden = $key + 2;
                        // para marcar un ConsumoAdhesivoPegado como inactivado debe ser originalmente activo
                        if (($ConsumoAdhesivoPegado->getOriginal("active") == 1 && $ConsumoAdhesivoPegado->active == 0)) {
                            $consumosAdhesivosPegadosInactivados[] = $ConsumoAdhesivoPegado;
                        } else {
                            $consumosAdhesivosPegadosActualizados[] = $ConsumoAdhesivoPegado;
                        }
                        // dd($ConsumoAdhesivoPegado->getDirty(), $ConsumoAdhesivoPegado);

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        changelog($ConsumoAdhesivoPegado, $row, "UPDATE", $codigo_operacion);
                        if ($proceso_carga == "cargaCompleta") {
                            $ConsumoAdhesivoPegado->save();
                            continue;
                        }
                    } else {
                        // Si no hay cambios lo unico q actualizamos es el orden segun archivo
                        // $ConsumoAdhesivoPegado->update(["orden" => $key + 2]);
                    }
                } else {
                    // PARA ESTE MANTENEDOR DESHABILITAMOS LA ADICION DE CONSUMOS
                    $ConsumoAdhesivoPegado = new ConsumoAdhesivoPegado();

                    $ConsumoAdhesivoPegado->onda = trim($row->onda);
                    $ConsumoAdhesivoPegado->adhesivo_corrugado = trim($row->adhesivo_corrugado);
                    $ConsumoAdhesivoPegado->adhesivo_powerply = trim($row->adhesivo_powerply);
                    $ConsumoAdhesivoPegado->planta_id = $planta;
                    // Solo si el proceso ya es de carga guardamos y continuamos
                    $changelog = changelog($ConsumoAdhesivoPegado, $row, "INSERT", $codigo_operacion);
                    if ($proceso == "cargaCompleta") {
                        $ConsumoAdhesivoPegado->save();
                        $changelog->update(['item_id' => $ConsumoAdhesivoPegado->id]);
                        continue;
                    }

                    $ConsumoAdhesivoPegado->linea = $key + 2;
                    $consumosAdhesivosPegados[] = $ConsumoAdhesivoPegado;
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso_carga == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $consumosAdhesivosPegados_ingresados = [];
        $consumosAdhesivosPegados_actualizados = [];
        $consumosAdhesivosPegados_inactivados = [];
        $consumosAdhesivosPegados_error = [];

        if (isset($consumosAdhesivosPegados)) {
            $exito = 'Se ingresaron los siguientes Consumos Adhesivos Pegado';
            $consumosAdhesivosPegados_ingresados = $consumosAdhesivosPegados;
            // $plantas_ingresadas = Planta::whereIn('id', $ids)->with('status')->paginate(10);
        }
        if (isset($consumosAdhesivosPegadosActualizados)) {
            $updated = 'Los siguientes Consumos Adhesivos Pegado fueron actualizados:';
            $consumosAdhesivosPegados_actualizados = $consumosAdhesivosPegadosActualizados;
        }
        if (isset($consumosAdhesivosPegadosInactivados)) {
            $updated = 'Los siguientes Consumos Adhesivos Pegado fueron actualizados:';
            $consumosAdhesivosPegados_inactivados = $consumosAdhesivosPegadosInactivados;
        }
        if (isset($consumosAdhesivosPegadosInvalidos)) {
            $error = 'Los siguientes Consumos Adhesivos Pegado tienen 1 o mas errores';
            $consumosAdhesivosPegados_error = $consumosAdhesivosPegadosInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'consumo_adhesivos_pegados' => $consumosAdhesivosPegados_ingresados,
            'consumo_adhesivos_pegados_actualizados' => $consumosAdhesivosPegados_actualizados,
            'consumo_adhesivos_pegados_inactivados' => $consumosAdhesivosPegados_inactivados,
            'consumo_adhesivos_pegados_error' => $consumosAdhesivosPegados_error

        ], 200);
    }

    public function descargar_excel_consumo_adhesivos_pegados(Request $request)
    {
        $titulo = "Listado Consumo Adhesivo Pegado";
        $consumo_adhesivos_pegados = ConsumoAdhesivoPegado::all();
        // dd($consumo_adhesivo);
        $consumo_adhesivo_pegado_array[] = array(
            'ID',
            'planta',
            'proceso',
            'consumo_adhesivo_pegado',
        );

        foreach ($consumo_adhesivos_pegados as $consumo_adhesivo) {
            $consumo_adhesivo_pegado_array[] = array(
                $consumo_adhesivo->id,
                $consumo_adhesivo->planta->nombre,
                $consumo_adhesivo->proceso->descripcion,
                $consumo_adhesivo->consumo_adhesivo_pegado_gr_caja,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($consumo_adhesivo_pegado_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Consumo Adhesivo Pegado', function ($sheet) use ($consumo_adhesivo_pegado_array) {
                $sheet->fromArray($consumo_adhesivo_pegado_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }


    //////////////CONSUMO ENERGIA//////////////////
    ////////////////////////////////////////
    //////////////CONSUMO ENERGIA/////////////////
    ////////////////////////////////////////
    //////////////CONSUMO ENERGIA//////////////////
    public function cargaConsumoEnergiaForm()
    {
        $consumoEnergias = ConsumoEnergia::all();
        return view('mantenedores.consumos-energias-masive', compact("consumoEnergias"));
    }


    public function importConsumoEnergia(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $plantas = Planta::pluck('id', 'nombre')->toArray();
        $procesos = Process::where('active', 1)->whereNotIn('id', [6, 8])->orderBy("descripcion")->pluck('id', 'descripcion')->toArray();
        $proceso_carga = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}
                $motivos = [];

                $consumo_kwh_mm2 = is_numeric($row->consumo_kwh_mm2) ? (int) $row->consumo_kwh_mm2 : false;
                $planta = array_key_exists($row->planta, $plantas) ? $plantas[$row->planta] : false;
                $proceso = array_key_exists($row->proceso, $procesos) ? $procesos[$row->proceso] : false;
                if ($consumo_kwh_mm2 === false) $motivos[] = " Consumo Energia";
                if (!$planta) $motivos[] = " Planta";
                if (!$proceso) $motivos[] = " Proceso";

                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $consumosEnergia = new stdClass();
                    $consumosEnergia->linea = $key + 2;
                    $consumosEnergia->motivos = $motivos;
                    $consumosEnergiasInvalidos[] = $consumosEnergia;
                    continue;
                }


                $consumoEnergia = ConsumoEnergia::where('id', $row->id)->first();
                // dd($user);
                if ($consumoEnergia) {

                    $consumoEnergia->consumo_kwh_mm2 = trim($row->consumo_kwh_mm2);
                    $consumoEnergia->planta_id = $planta;
                    $consumoEnergia->process_id = $proceso;
                    // dd($consumoEnergia, $consumoEnergia->isDirty(), $consumoEnergia->getChanges(), $consumoEnergia->isDirty("desperdicio"));
                    if ($consumoEnergia->isDirty()) {
                        // $consumoEnergia->orden = $key + 2;
                        // para marcar un consumoEnergia como inactivado debe ser originalmente activo
                        if (($consumoEnergia->getOriginal("active") == 1 && $consumoEnergia->active == 0)) {
                            $consumosEnergiasInactivados[] = $consumoEnergia;
                        } else {
                            $consumosEnergiasActualizados[] = $consumoEnergia;
                        }
                        // dd($consumoEnergia->getDirty(), $consumoEnergia);

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso_carga == "cargaCompleta") {
                            changelog($consumoEnergia, $row, "UPDATE", $codigo_operacion);
                            $consumoEnergia->save();
                            continue;
                        }
                    } else {
                        // Si no hay cambios lo unico q actualizamos es el orden segun archivo
                        // $consumoEnergia->update(["orden" => $key + 2]);
                    }
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso_carga == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $consumosEnergias_ingresados = [];
        $consumosEnergias_actualizados = [];
        $consumosEnergias_inactivados = [];
        $consumosEnergias_error = [];

        if (isset($consumosEnergiasActualizados)) {
            $updated = 'Los siguientes Consumos Energias fueron actualizados:';
            $consumosEnergias_actualizados = $consumosEnergiasActualizados;
        }
        if (isset($consumosEnergiasInvalidos)) {
            $error = 'Los siguientes Consumos Energias tienen 1 o mas errores';
            $consumosEnergias_error = $consumosEnergiasInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'consumo_energia' => $consumosEnergias_ingresados,
            'consumo_energia_actualizados' => $consumosEnergias_actualizados,
            'consumo_energia_inactivados' => $consumosEnergias_inactivados,
            'consumo_energia_error' => $consumosEnergias_error

        ], 200);
    }

    public function descargar_excel_consumo_energia(Request $request)
    {
        $titulo = "Listado Consumo Energia";
        $consumo_energias = ConsumoEnergia::all();
        $consumo_energia_array[] = array(
            'ID',
            'planta',
            'proceso',
            'consumo_kwh_mm2'
        );

        foreach ($consumo_energias as $consumo_energia) {
            $consumo_energia_array[] = array(
                $consumo_energia->id,
                $consumo_energia->planta->nombre,
                $consumo_energia->proceso->descripcion,
                $consumo_energia->consumo_kwh_mm2,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($consumo_energia_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Consumo Energia', function ($sheet) use ($consumo_energia_array) {
                $sheet->fromArray($consumo_energia_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    //MATRICES

    public function cargaMatricesForm()
    {
        $matrices = Matriz::get();
        return view('mantenedores.matrices-masive', compact("matrices"));
    }
    public function importMatrices(Request $request)
    {
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]
        );
        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de matrices
                $motivos = [];
                // Validaciones generales
                $plano_cad = (trim($row->plano_cad) != "") ? $row->plano_cad : false;
                $material = (trim($row->material) != "") ? $row->material : false;
                $texto_breve_material = (trim($row->texto_breve_material) != "") ? $row->texto_breve_material : false;
                $largo_matriz = is_numeric($row->largo_matriz) ? true : false;
                $ancho_matriz = is_numeric($row->ancho_matriz) ? true : false;
                $cantidad_largo_matriz = is_numeric($row->cantidad_largo_matriz) ? true : false;
                $cantidad_ancho_matriz = is_numeric($row->cantidad_ancho_matriz) ? true : false;
                $separacion_largo_matriz = is_numeric($row->separacion_largo_matriz) ? true : false;
                $separacion_ancho_matriz = is_numeric($row->separacion_ancho_matriz) ? true : false;
                $cuchillas = is_numeric($row->cuchillas) ? true : false;
                $tipo_matriz = trim($row->tipo_matriz) ? $row->tipo_matriz : false;
                $total_golpes = is_numeric($row->total_golpes) ? true : false;
                //$maquina = (trim($row->maquina)) ? $row->maquina : false;
                if (!$plano_cad) $motivos[] = " Plano Cad";
                if (!$material) $motivos[] = " Material";
                if (!$texto_breve_material) $motivos[] = " Texto breve material";
                if (!$largo_matriz) $motivos[] = " Largo Matriz";
                if (!$ancho_matriz) $motivos[] = " Ancho Matriz";
                if (!$cantidad_largo_matriz) $motivos[] = " Cant. Largo Matriz";
                if (!$cantidad_ancho_matriz) $motivos[] = " Cant. Ancho Matriz";
                if (!$separacion_largo_matriz) $motivos[] = " Separacion largo matriz";
                if (!$separacion_ancho_matriz) $motivos[] = " Separacion ancho matriz";
                if (!$cuchillas) $motivos[] = " Cuchillas";
                if (!$tipo_matriz && $tipo_matriz !== 0.0) $motivos[] = " Tipo Matriz";
                if (!$total_golpes) $motivos[] = " Total golpes";
                //  if (!$maquina) $motivos[] = " Maquina";

                //  dd(count($motivos),$motivos,is_numeric($row->separacion_largo_matriz),$row->separacion_largo_matriz,!$separacion_largo_matriz);
                if (count($motivos) >= 1) {
                    $matrizErroneo = new stdClass();
                    $matrizErroneo->linea = $key + 2;
                    $matrizErroneo->motivos = $motivos;
                    $matricesInvalidos[] = $matrizErroneo;
                    continue;
                }
                $matriz = Matriz::where('plano_cad', $row->plano_cad)->where('material', $row->material)->first();
                // dd($user);
                if ($matriz) {
                    $matriz->plano_cad = ($row->plano_cad);
                    $matriz->material = ($row->material);
                    $matriz->texto_breve_material = ($row->texto_breve_material);
                    $matriz->largo_matriz = ($row->largo_matriz);
                    $matriz->ancho_matriz = ($row->ancho_matriz);
                    $matriz->cantidad_largo_matriz = ($row->cantidad_largo_matriz);
                    $matriz->cantidad_ancho_matriz = ($row->cantidad_ancho_matriz);
                    $matriz->separacion_largo_matriz = ($row->separacion_largo_matriz);
                    $matriz->separacion_ancho_matriz = ($row->separacion_ancho_matriz);
                    $matriz->cuchillas = ($row->cuchillas);
                    $matriz->tipo_matriz = ($row->tipo_matriz);
                    $matriz->total_golpes = ($row->total_golpes);
                    $matriz->maquina = ($row->maquina);
                    $matriz->active = ($row->active);
                    if ($matriz->isDirty()) {
                        $matriz->orden = $key + 2;
                        // para marcar una matriz como inactivado debe ser originalmente activo
                        if (($matriz->getOriginal("active") == 1 && $matriz->active == 0)) {
                            $matricesInactivados[] = $matriz;
                        } else {
                            $matricesActualizados[] = $matriz;
                        }
                        // dd($papel->getDirty(), $papel);
                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($matriz, $row, "UPDATE", $codigo_operacion);
                            $matriz->save();
                            continue;
                        }
                    } else {
                        // Si no hay cambios lo unico q actualizamos es el orden segun archivo
                        // $matriz->update(["orden" => $key + 2]);
                        $matriz->orden = $key + 2;
                        $matriz->save();
                    }
                } else {
                    $matriz = new Matriz();
                    $matriz->plano_cad = ($row->plano_cad);
                    $matriz->material = ($row->material);
                    $matriz->texto_breve_material = ($row->texto_breve_material);
                    $matriz->largo_matriz = ($row->largo_matriz);
                    $matriz->ancho_matriz = ($row->ancho_matriz);
                    $matriz->cantidad_largo_matriz = ($row->cantidad_largo_matriz);
                    $matriz->cantidad_ancho_matriz = ($row->cantidad_ancho_matriz);
                    $matriz->separacion_largo_matriz = ($row->separacion_largo_matriz);
                    $matriz->separacion_ancho_matriz = ($row->separacion_ancho_matriz);
                    $matriz->cuchillas = ($row->cuchillas);
                    $matriz->tipo_matriz = ($row->tipo_matriz);
                    $matriz->total_golpes = ($row->total_golpes);
                    $matriz->maquina = ($row->maquina);
                    $matriz->active = ($row->active);
                    $matriz->orden = $key + 2;
                    // Solo si el proceso ya es de carga guardamos y continuamos
                    if ($proceso == "cargaCompleta") {
                        $changelog = changelog($matriz, $row, "INSERT", $codigo_operacion);
                        $matriz->save();
                        $changelog->update(['item_id' => $matriz->id]);
                        continue;
                    }
                    $matriz->linea = $key + 2;
                    $matrices[] = $matriz;
                }
            }
        }
        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");
        $exito = null;
        $updated = null;
        $error = null;
        $matrices_ingresados = [];
        $matrices_actualizados = [];
        $matrices_inactivados = [];
        $matrices_error = [];
        if (isset($matrices)) {
            $exito = 'Se ingresaron las siguientes matrices';
            $matrices_ingresados = $matrices;
            // $plantas_ingresadas = Planta::whereIn('id', $ids)->with('status')->paginate(10);
        }
        if (isset($matricesActualizados)) {
            $updated = 'Las siguientes matrices fueron actualizados:';
            $matrices_actualizados = $matricesActualizados;
        }
        if (isset($matricesInactivados)) {
            $updated = 'Las siguientes matrices fueron actualizados:';
            $matrices_inactivados = $matricesInactivados;
        }
        if (isset($matricesInvalidos)) {
            $error = 'Las siguientes matrices tienen 1 o mas errores';
            $matrices_error = $matricesInvalidos;
        }
        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'matrices' => $matrices_ingresados,
            'matrices_actualizados' => $matrices_actualizados,
            'matrices_inactivados' => $matrices_inactivados,
            'matrices_error' => $matrices_error
        ], 200);
    }
    public function descargar_excel_matrices(Request $request)
    {
        $titulo = "Listado Matrices";
        $matrices = Matriz::all();
        $matrices_array = [];
        $matrices_array[] = [
            // 'ID',
            'plano_cad',
            'material',
            'texto_breve_material',
            'largo_matriz',
            'ancho_matriz',
            'cantidad_largo_matriz',
            'cantidad_ancho_matriz',
            'separacion_largo_matriz',
            'separacion_ancho_matriz',
            'tipo_matriz',
            'total_golpes',
            'cuchillas',
            'maquina',
            'active'
        ];
        foreach ($matrices as $matriz) {
            $matrices_array[] = [
                // strval($matriz->id),
                strval($matriz->plano_cad),
                strval($matriz->material),
                strval($matriz->texto_breve_material),
                strval($matriz->largo_matriz),
                strval($matriz->ancho_matriz),
                strval($matriz->cantidad_largo_matriz),
                strval($matriz->cantidad_ancho_matriz),
                strval($matriz->separacion_largo_matriz),
                strval($matriz->separacion_ancho_matriz),
                strval($matriz->tipo_matriz),
                strval($matriz->total_golpes),
                strval($matriz->cuchillas),
                strval($matriz->maquina),
                strval($matriz->active)
            ];
        }
        // $matrices_array[] = array(
        //     'ID',
        //     'plano_cad',
        //     'material',
        //     'texto_breve_material',
        //     'largo_matriz',
        //     'ancho_matriz',
        //     'cantidad_largo_matriz',
        //     'cantidad_ancho_matriz',
        //     'separacion_largo_matriz',
        //     'separacion_ancho_matriz',
        //     'tipo_matriz',
        //     'total_golpes',
        //     'active'
        // );
        // foreach ($matrices as $matriz) {
        //     $matrices_array[] = array(
        //         $matriz->id,
        //         $matriz->plano_cad,
        //         $matriz->material,
        //         $matriz->texto_breve_material,
        //         $matriz->largo_matriz,
        //         $matriz->ancho_matriz,
        //         $matriz->cantidad_largo_matriz,
        //         $matriz->cantidad_ancho_matriz,
        //         $matriz->separacion_largo_matriz,
        //         $matriz->separacion_ancho_matriz,
        //         $matriz->tipo_matriz,
        //         $matriz->total_golpes,
        //         $matriz->active
        //     );
        // }
        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($matrices_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Matrices', function ($sheet) use ($matrices_array) {
                $sheet->fromArray($matrices_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }


    //MATERIALES


    public function cargaMaterialesForm()
    {
        $materiales = Material::get();
        return view('mantenedores.materiales-masive', compact("materiales"));
    }
    public function importMateriales(Request $request)
    {
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]
        );
        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de matrices
                $motivos = [];
                // Validaciones generales
                // $codigo = (trim($row->codigo) != "") ? $row->codigo : false;
                $descripcion = (trim($row->descripcion) != "") ? $row->descripcion : false;
                // $numero_colores = $row->numero_colores;
                // $pallet_box_quantity = is_numeric($row->pallet_box_quantity) ? true : false;
                // $patron_zuncho_pallet = is_numeric($row->patron_zuncho_pallet) ? true : false;
                // $boxes_per_package = is_numeric($row->boxes_per_package) ? true : false;
                // $patron_zuncho_paquete = is_numeric($row->patron_zuncho_paquete) ? true : false;
                // $patron_zuncho_bulto = is_numeric($row->patron_zuncho_bulto) ? true : false;
                // $paquetes_por_unitizado = is_numeric($row->paquetes_por_unitizado) ? true : false;
                // $unitizado_por_pallet = is_numeric($row->unitizado_por_pallet) ? true : false;
                // $numero_etiquetas = is_numeric($row->numero_etiquetas) ? true : false;
                // $rmt = is_numeric($row->rmt) ? true : false;
                // $unidad_medida_bct = is_numeric($row->unidad_medida_bct) ? true : false;;
                // $pallet_treatment = is_numeric($row->pallet_treatment) ? true : false;
                // $tipo_camion = trim($row->tipo_camion) != "" ? $row->tipo_camion : false;
                // $restriccion_especial = trim($row->restriccion_especial) != "" ? $row->restriccion_especial : false;
                // $horario_recepcion = trim($row->horario_recepcion) != "" ? $row->horario_recepcion : false;
                // $codigo_producto_cliente = trim($row->codigo_producto_cliente) != "" ? $row->codigo_producto_cliente : false;
                // $etiquetas_dsc = is_numeric($row->etiquetas_dsc) ? true : false;
                // $orientacion_placa = is_numeric($row->orientacion_placa) ? true : false;
                // $recubrimiento = is_numeric($row->recubrimiento) ? true : false;
                // $cinta = is_numeric($row->cinta) ? true : false;
                // $tipo_cinta = is_numeric($row->tipo_cinta) ? true : false;
                // $corte_liner = is_numeric($row->corte_liner) ? true : false;
                // $distancia_cinta_1 = is_numeric($row->distancia_cinta_1) ? true : false;
                // $distancia_cinta_2 = is_numeric($row->distancia_cinta_2) ? true : false;
                // $distancia_cinta_3 = is_numeric($row->distancia_cinta_3) ? true : false;
                // $distancia_cinta_4 = is_numeric($row->distancia_cinta_4) ? true : false;
                // $distancia_cinta_5 = is_numeric($row->distancia_cinta_5) ? true : false;
                // $distancia_cinta_6 = is_numeric($row->distancia_cinta_6) ? true : false;
                // $gramaje = is_numeric($row->gramaje) ? true : false;
                // $ect = is_numeric($row->ect) ? true : false;
                // $flexion_aleta = trim($row->flexion_aleta) != "" ? $row->flexion_aleta : false;
                // $peso = trim($row->peso) != "" ? $row->peso : false;
                // $fct = is_numeric($row->fct) ? true : false;
                // $cobb_interior = trim($row->cobb_interior) != "" ? $row->cobb_interior : false;
                // $cobb_exterior = trim($row->cobb_exterior) != "" ? $row->cobb_exterior : false;
                // $espesor = is_numeric($row->espesor) ? true : false;
                // $golpes_largo = is_numeric($row->golpes_largo) ? true : false;
                // $golpes_ancho = is_numeric($row->golpes_ancho) ? true : false;
                // $area_hc = is_numeric($row->area_hc) ? true : false;
                // $bct_min_lb = is_numeric($row->bct_min_lb) ? true : false;
                // $bct_min_kg = is_numeric($row->bct_min_kg) ? true : false;
                // $incision_rayado_longitudinal = is_numeric($row->incision_rayado_longitudinal) ? true : false;
                // $incision_rayado_vertical = is_numeric($row->incision_rayado_vertical) ? true : false;


                // if (!$codigo) $motivos[] = " Codigo";
                if (!$descripcion) $motivos[] = " Descripcion";
                // if (!$numero_colores) $motivos[] = " Numero de Colores";
                // if (!$pallet_box_quantity) $motivos[] = " Pallet Box Quantity";
                // if (!$patron_zuncho_pallet) $motivos[] = " Patron Zuncho Pallet";
                // if (!$boxes_per_package) $motivos[] = " Boxes Per Package";
                // if (!$patron_zuncho_paquete) $motivos[] = " Patron Zuncho Paquete";
                // if (!$patron_zuncho_bulto) $motivos[] = " Patron Zuncho Bulto";
                // if (!$paquetes_por_unitizado) $motivos[] = " Paquetes Por Unitizado";
                // if (!$unitizado_por_pallet) $motivos[] = " Unitizado Por Pallet";
                // if (!$numero_etiquetas) $motivos[] = " Numero Etiquetas";
                // if (!$rmt) $motivos[] = " Rmt";
                // if (!$unidad_medida_bct) $motivos[] = " Unidad Medida Bct";
                // if (!$pallet_treatment) $motivos[] = " Pallet Treatment";
                // if (!$tipo_camion) $motivos[] = " Tipo Camion";
                // if (!$restriccion_especial) $motivos[] = " Restriccion Especial";
                // if (!$horario_recepcion) $motivos[] = " Horario Recepcion";
                // if (!$codigo_producto_cliente) $motivos[] = " Codigo Producto Cliente";
                // if (!$etiquetas_dsc) $motivos[] = " Etiquetas DSC";
                // if (!$orientacion_placa) $motivos[] = " Orientacion Placa";
                // if (!$recubrimiento) $motivos[] = " Recubrimiento";
                // if (!$cinta) $motivos[] = " Cinta";
                // if (!$tipo_cinta) $motivos[] = " Tipo Cinta";
                // if (!$corte_liner) $motivos[] = " Corte Liner";
                // if (!$distancia_cinta_1) $motivos[] = " Distancia Cinta 1";
                // if (!$distancia_cinta_2) $motivos[] = " Distancia Cinta 2";
                // if (!$distancia_cinta_3) $motivos[] = " Distancia Cinta 3";
                // if (!$distancia_cinta_4) $motivos[] = " Distancia Cinta 4";
                // if (!$distancia_cinta_5) $motivos[] = " Distancia Cinta 5";
                // if (!$distancia_cinta_6) $motivos[] = " Distancia Cinta 6";
                // if (!$gramaje) $motivos[] = " Gramaje";
                // if (!$ect) $motivos[] = " Ect";
                // if (!$flexion_aleta) $motivos[] = " Flexion Aleta";
                // if (!$peso) $motivos[] = " Peso";
                // if (!$fct) $motivos[] = " Fct";
                // if (!$cobb_interior) $motivos[] = " Cobb Interior";
                // if (!$cobb_exterior) $motivos[] = " Cobb Exterior";
                // if (!$espesor) $motivos[] = " Espesor";
                // if (!$golpes_largo) $motivos[] = " Golpes Largo";
                // if (!$golpes_ancho) $motivos[] = " Golpes Ancho";
                // if (!$area_hc) $motivos[] = " Area Hc";
                // if (!$bct_min_lb) $motivos[] = " Bct Min Lb";
                // if (!$bct_min_kg) $motivos[] = " Bct Min Kg";
                // if (!$incision_rayado_longitudinal) $motivos[] = " Incision Rayado Longitudinal";
                // if (!$incision_rayado_vertical) $motivos[] = " Incision Rayado Vertical";




                //  dd(count($motivos),$motivos,is_numeric($row->separacion_largo_matriz),$row->separacion_largo_matriz,!$separacion_largo_matriz);
                if (count($motivos) >= 1) {
                    $materialErroneo = new stdClass();
                    $materialErroneo->linea = $key + 2;
                    $materialErroneo->motivos = $motivos;
                    $materialesInvalidos[] = $materialErroneo;
                    continue;
                }

                // dd($materialErroneo);
                $material = Material::where('codigo', $row->codigo)->first();
                // dd($user);
                if ($material) {

                    $material->codigo = ($row->codigo);
                    $material->descripcion = ($row->descripcion);
                    $material->numero_colores = ($row->numero_colores);
                    $material->pallet_box_quantity = ($row->pallet_box_quantity);
                    $material->patron_zuncho_pallet = ($row->patron_zuncho_pallet);
                    $material->boxes_per_package = ($row->boxes_per_package);
                    $material->patron_zuncho_paquete = ($row->patron_zuncho_paquete);
                    $material->patron_zuncho_bulto = ($row->patron_zuncho_bulto);
                    $material->paquetes_por_unitizado = ($row->paquetes_por_unitizado);
                    $material->unitizado_por_pallet = ($row->unitizado_por_pallet);
                    $material->numero_etiquetas = ($row->numero_etiquetas);
                    $material->rmt = ($row->rmt);
                    $material->unidad_medida_bct = ($row->unidad_medida_bct);
                    $material->pallet_treatment = ($row->pallet_treatment);
                    $material->tipo_camion = ($row->tipo_camion);
                    $material->restriccion_especial = ($row->restriccion_especial);
                    $material->horario_recepcion = ($row->horario_recepcion);
                    $material->codigo_producto_cliente = ($row->codigo_producto_cliente);
                    $material->etiquetas_dsc = ($row->etiquetas_dsc);
                    $material->orientacion_placa = ($row->orientacion_placa);
                    $material->recubrimiento = ($row->recubrimiento);
                    $material->cinta = ($row->cinta);
                    $material->tipo_cinta = ($row->tipo_cinta);
                    $material->corte_liner = ($row->corte_liner);
                    $material->distancia_cinta_1 = ($row->distancia_cinta_1);
                    $material->distancia_cinta_2 = ($row->distancia_cinta_2);
                    $material->distancia_cinta_3 = ($row->distancia_cinta_3);
                    $material->distancia_cinta_4 = ($row->distancia_cinta_4);
                    $material->distancia_cinta_5 = ($row->distancia_cinta_5);
                    $material->distancia_cinta_6 = ($row->distancia_cinta_6);
                    $material->gramaje = ($row->gramaje);
                    $material->ect = ($row->ect);
                    $material->flexion_aleta = ($row->flexion_aleta);
                    $material->peso = ($row->peso);
                    $material->fct = ($row->fct);
                    $material->cobb_interior = ($row->cobb_interior);
                    $material->cobb_exterior = ($row->cobb_exterior);
                    $material->espesor = ($row->espesor);
                    $material->golpes_largo = ($row->golpes_largo);
                    $material->golpes_ancho = ($row->golpes_ancho);
                    $material->area_hc = ($row->area_hc);
                    $material->bct_min_lb = ($row->bct_min_lb);
                    $material->bct_min_kg = ($row->bct_min_kg);
                    // $material->incision_rayado_longitudinal = ($row->incision_rayado_longitudinal);
                    // $material->incision_rayado_vertical = ($row->incision_rayado_vertical);


                    $material->active = ($row->active);
                    if ($material->isDirty()) {
                        // $material->orden = $key + 2;
                        // para marcar un material como inactivado debe ser originalmente activo
                        if (($material->getOriginal("active") == 1 && $material->active == 0)) {
                            $materialesInactivados[] = $material;
                        } else {
                            $materialesActualizados[] = $material;
                        }
                        // dd($papel->getDirty(), $papel);
                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($material, $row, "UPDATE", $codigo_operacion);
                            $material->save();
                            continue;
                        }
                    } else {
                        // Si no hay cambios lo unico q actualizamos es el orden segun archivo
                        // $matriz->update(["orden" => $key + 2]);
                        // $matriz->orden = $key + 2;
                        // $material->save();
                    }
                } else {
                    // $matriz = new Matriz();
                    // $matriz->plano_cad = ($row->plano_cad);
                    // $matriz->material = ($row->material);
                    // $matriz->texto_breve_material = ($row->texto_breve_material);
                    // $matriz->largo_matriz = ($row->largo_matriz);
                    // $matriz->ancho_matriz = ($row->ancho_matriz);
                    // $matriz->cantidad_largo_matriz = ($row->cantidad_largo_matriz);
                    // $matriz->cantidad_ancho_matriz = ($row->cantidad_ancho_matriz);
                    // $matriz->separacion_largo_matriz = ($row->separacion_largo_matriz);
                    // $matriz->separacion_ancho_matriz = ($row->separacion_ancho_matriz);
                    // $matriz->tipo_matriz = ($row->tipo_matriz);
                    // $matriz->total_golpes = ($row->total_golpes);
                    // $matriz->maquina = ($row->maquina);
                    // $matriz->active = ($row->active);
                    // $matriz->orden = $key + 2;
                    // // Solo si el proceso ya es de carga guardamos y continuamos
                    // if ($proceso == "cargaCompleta") {
                    //     $changelog = changelog($matriz, $row, "INSERT", $codigo_operacion);
                    //     $matriz->save();
                    //     $changelog->update(['item_id' => $matriz->id]);
                    //     continue;
                    // }
                    // $matriz->linea = $key + 2;
                    // $matrices[] = $matriz;
                }
            }
        }
        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");
        $exito = null;
        $updated = null;
        $error = null;
        $materiales_ingresados = [];
        $materiales_actualizados = [];
        $materiales_inactivados = [];
        $materiales_error = [];
        // if (isset($materiales)) {
        //     $exito = 'Se ingresaron las siguientes materiales';
        //     $materiales_ingresados = $materiales;
        //     // $plantas_ingresadas = Planta::whereIn('id', $ids)->with('status')->paginate(10);
        // }
        if (isset($materialesActualizados)) {
            $updated = 'Las siguientes materiales fueron actualizados:';
            $materiales_actualizados = $materialesActualizados;
        }
        if (isset($materialesInactivados)) {
            $updated = 'Las siguientes materiales fueron actualizados:';
            $materiales_inactivados = $materialesInactivados;
        }
        if (isset($materialesInvalidos)) {
            $error = 'Las siguientes materiales tienen 1 o mas errores';
            $materiales_error = $materialesInvalidos;
        }
        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            // 'materiales' => $materiales_ingresados,
            'materiales_actualizados' => $materiales_actualizados,
            'materiales_inactivados' => $materiales_inactivados,
            'materiales_error' => $materiales_error
        ], 200);
    }
    // public function descargar_excel_materiales(Request $request)
    // {
    //     $titulo = "Listado Materiales";
    //     // $materiales = Material::whereIn('id',[1,2,3])->get();
    //     $materiales = Material::get();
    //     $materiales_array = [];
    //     $materiales_array[] = [
    //         // 'ID',
    //         'codigo',
    //         'descripcion',
    //         'numero_colores',
    //         'pallet_box_quantity',
    //         'patron_zuncho_pallet',
    //         'boxes_per_package',
    //         'patron_zuncho_paquete',
    //         'patron_zuncho_bulto',
    //         'paquetes_por_unitizado',
    //         'unitizado_por_pallet',
    //         'numero_etiquetas',
    //         'rmt',
    //         'unidad_medida_bct',
    //         'pallet_treatment',
    //         'tipo_camion',
    //         'restriccion_especial',
    //         'horario_recepcion',
    //         'codigo_producto_cliente',
    //         'etiquetas_dsc',
    //         'orientacion_placa',
    //         'recubrimiento',
    //         'cinta',
    //         'tipo_cinta',
    //         'corte_liner',
    //         'distancia_cinta_1',
    //         'distancia_cinta_2',
    //         'distancia_cinta_3',
    //         'distancia_cinta_4',
    //         'distancia_cinta_5',
    //         'distancia_cinta_6',
    //         'gramaje',
    //         'ect',
    //         'flexion_aleta',
    //         'peso',
    //         'fct',
    //         'cobb_interior',
    //         'cobb_exterior',
    //         'espesor',
    //         'golpes_largo',
    //         'golpes_ancho',
    //         'area_hc',
    //         'bct_min_lb',
    //         'bct_min_kg',
    //         // 'incision_rayado_longitudinal',
    //         // 'incision_rayado_vertical',
    //         'active'
    //     ];
    //     foreach ($materiales as $material) {
    //         $materiales_array[] = [
    //             // strval($matriz->id),
    //             strval($material->codigo),
    //             strval($material->descripcion),
    //             strval($material->numero_colores),
    //             strval($material->pallet_box_quantity),
    //             strval($material->patron_zuncho_pallet),
    //             strval($material->boxes_per_package),
    //             strval($material->patron_zuncho_paquete),
    //             strval($material->patron_zuncho_bulto),
    //             strval($material->paquetes_por_unitizado),
    //             strval($material->unitizado_por_pallet),
    //             strval($material->numero_etiquetas),
    //             strval($material->rmt),
    //             strval($material->unidad_medida_bct),
    //             strval($material->pallet_treatment),
    //             strval($material->tipo_camion),
    //             strval($material->restriccion_especial),
    //             strval($material->horario_recepcion),
    //             strval($material->codigo_producto_cliente),
    //             strval($material->etiquetas_dsc),
    //             strval($material->orientacion_placa),
    //             strval($material->recubrimiento),
    //             strval($material->cinta),
    //             strval($material->tipo_cinta),
    //             strval($material->corte_liner),
    //             strval($material->distancia_cinta_1),
    //             strval($material->distancia_cinta_2),
    //             strval($material->distancia_cinta_3),
    //             strval($material->distancia_cinta_4),
    //             strval($material->distancia_cinta_5),
    //             strval($material->distancia_cinta_6),
    //             strval($material->gramaje),
    //             strval($material->ect),
    //             strval($material->flexion_aleta),
    //             strval($material->peso),
    //             strval($material->fct),
    //             strval($material->cobb_interior),
    //             strval($material->cobb_exterior),
    //             strval($material->espesor),
    //             strval($material->golpes_largo),
    //             strval($material->golpes_ancho),
    //             strval($material->area_hc),
    //             strval($material->bct_min_lb),
    //             strval($material->bct_min_kg),
    //             // strval($material->incision_rayado_longitudinal),
    //             // strval($material->incision_rayado_vertical),
    //             strval($material->active),
    //         ];
    //     }

    //     Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($materiales_array, $titulo) {
    //         $excel->setTitle($titulo);
    //         $excel->sheet('Materiales', function ($sheet) use ($materiales_array) {
    //             $sheet->fromArray($materiales_array, null, 'A1', true, false);
    //         });
    //     })->download('xlsx');
    // }

      public function descargar_excel_materiales(Request $request)
    {
        // --- Validación de parámetro requerido ---
        $activeSel = $request->query('active');

        // viene vacío o no presente → error y NO filtrar
        if ($activeSel === null || $activeSel === '') {
            return back()->withInput()->with('error', 'Debe seleccionar un estado.');
        }

        // valor inválido → error
        if (!in_array((string)$activeSel, ['0', '1', '2'], true)) {
            return back()->withInput()->with('error', 'Valor de estado inválido.');
        }

        // --- Cache PHPExcel para bajar RAM ---
        ini_set('memory_limit', '2048M');
        set_time_limit(0);
        $cacheDir = storage_path('app/phpexcel-cache');
        if (!is_dir($cacheDir)) @mkdir($cacheDir, 0775, true);
        PHPExcel_Settings::setCacheStorageMethod(
            PHPExcel_CachedObjectStorageFactory::cache_to_discISAM,
            ['dir' => $cacheDir]
        );

        // --- Query según selección ---
        $query = Material::query();
        if ($activeSel === '0' || $activeSel === '1' || $activeSel === '2') {
            $query->where('active', (int)$activeSel);
        }
        // si es '2' => TODOS (sin filtro)

        $titulo  = 'Listado Materiales';
        $nombre  = $titulo . ' ' . Carbon::now()->format('Y-m-d_His');

        $headers = [
            'codigo',
            'descripcion',
            'numero_colores',
            'pallet_box_quantity',
            'patron_zuncho_pallet',
            'boxes_per_package',
            'patron_zuncho_paquete',
            'patron_zuncho_bulto',
            'paquetes_por_unitizado',
            'unitizado_por_pallet',
            'numero_etiquetas',
            'rmt',
            'unidad_medida_bct',
            'pallet_treatment',
            'tipo_camion',
            'restriccion_especial',
            'horario_recepcion',
            'codigo_producto_cliente',
            'etiquetas_dsc',
            'orientacion_placa',
            'recubrimiento',
            'cinta',
            'tipo_cinta',
            'corte_liner',
            'distancia_cinta_1',
            'distancia_cinta_2',
            'distancia_cinta_3',
            'distancia_cinta_4',
            'distancia_cinta_5',
            'distancia_cinta_6',
            'gramaje',
            'ect',
            'flexion_aleta',
            'peso',
            'fct',
            'cobb_interior',
            'cobb_exterior',
            'espesor',
            'golpes_largo',
            'golpes_ancho',
            'area_hc',
            'bct_min_lb',
            'bct_min_kg',
            'active',
        ];

        return Excel::create($nombre, function ($excel) use ($headers, $titulo, $query) {
            $excel->setTitle($titulo);
            $excel->sheet('Materiales', function ($sheet) use ($headers, $query) {
                $sheet->row(1, $headers);
                $sheet->freezeFirstRow();

                $row = 2;
                $query->orderBy('id')->chunk(1000, function ($chunk) use ($sheet, &$row) {
                    foreach ($chunk as $m) {
                        $sheet->row($row++, [
                            (string)$m->codigo,
                            (string)$m->descripcion,
                            (string)$m->numero_colores,
                            (string)$m->pallet_box_quantity,
                            (string)$m->patron_zuncho_pallet,
                            (string)$m->boxes_per_package,
                            (string)$m->patron_zuncho_paquete,
                            (string)$m->patron_zuncho_bulto,
                            (string)$m->paquetes_por_unitizado,
                            (string)$m->unitizado_por_pallet,
                            (string)$m->numero_etiquetas,
                            (string)$m->rmt,
                            (string)$m->unidad_medida_bct,
                            (string)$m->pallet_treatment,
                            (string)$m->tipo_camion,
                            (string)$m->restriccion_especial,
                            (string)$m->horario_recepcion,
                            (string)$m->codigo_producto_cliente,
                            (string)$m->etiquetas_dsc,
                            (string)$m->orientacion_placa,
                            (string)$m->recubrimiento,
                            (string)$m->cinta,
                            (string)$m->tipo_cinta,
                            (string)$m->corte_liner,
                            (string)$m->distancia_cinta_1,
                            (string)$m->distancia_cinta_2,
                            (string)$m->distancia_cinta_3,
                            (string)$m->distancia_cinta_4,
                            (string)$m->distancia_cinta_5,
                            (string)$m->distancia_cinta_6,
                            (string)$m->gramaje,
                            (string)$m->ect,
                            (string)$m->flexion_aleta,
                            (string)$m->peso,
                            (string)$m->fct,
                            (string)$m->cobb_interior,
                            (string)$m->cobb_exterior,
                            (string)$m->espesor,
                            (string)$m->golpes_largo,
                            (string)$m->golpes_ancho,
                            (string)$m->area_hc,
                            (string)$m->bct_min_lb,
                            (string)$m->bct_min_kg,
                            (string)$m->active,
                        ]);
                    }
                });
            });
        })->download('xlsx');
    }


    //////////////FACTORES DESARROLLO//////////////////
    ////////////////////////////////////////
    //////////////FACTORES DESARROLLO/////////////////
    ////////////////////////////////////////
    //////////////FACTORES DESARROLLO//////////////////
    public function cargaFactoresDesarrolloForm()
    {
        $desarrollos = FactoresDesarrollo::all();
        return view('mantenedores.desarrollos-masive', compact("desarrollos"));
    }


    public function importFactoresDesarrollo(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}
                $motivos = [];

                $ondas_validas = ["C", "B",  "E", "CB", "CE", "BE"];
                $caja_entera_valida = ["SI", "NO"];
                $caja_entera_valida_values = ["SI" => 1, "NO" => 0];
                // dd(in_array(strtoupper($row->caja_entera), $caja_entera_valida));
                $tipo_onda = in_array(strtoupper($row->tipo_onda), $ondas_validas) ? strtoupper($row->tipo_onda) : false;
                $caja_entera = in_array(strtoupper($row->caja_entera), $caja_entera_valida) ? $caja_entera_valida_values[strtoupper($row->caja_entera)] : false;
                $externo_largo = is_numeric($row->externo_largo) ? (int) $row->externo_largo : false;
                $externo_ancho = is_numeric($row->externo_ancho) ? (int) $row->externo_ancho : false;
                $externo_alto = is_numeric($row->externo_alto) ? (int) $row->externo_alto : false;
                $d1 = is_numeric($row->d1) ? (int) $row->d1 : false;
                $d2 = is_numeric($row->d2) ? (int) $row->d2 : false;
                $dh = is_numeric($row->dh) ? (int) $row->dh : false;

                if (!$tipo_onda) $motivos[] = " Onda";
                if (!$caja_entera && $caja_entera !== 0) $motivos[] = " caja_entera";
                if (!$externo_largo) $motivos[] = " externo_largo";
                if (!$externo_ancho) $motivos[] = " externo_ancho";
                if (!$externo_alto) $motivos[] = " externo_alto";
                if (!$d1) $motivos[] = " d1";
                if (!$d2) $motivos[] = " d2";
                if (!$dh) $motivos[] = " dh";

                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $desarrollos = new stdClass();
                    $desarrollos->linea = $key + 2;
                    $desarrollos->motivos = $motivos;
                    $desarrollosInvalidos[] = $desarrollos;
                    continue;
                }


                $desarrollo = FactoresDesarrollo::where('id', $row->id)->first();
                // dd($user);
                if ($desarrollo) {

                    $desarrollo->tipo_onda = $tipo_onda;
                    $desarrollo->caja_entera = $caja_entera;
                    $desarrollo->externo_largo = $externo_largo;
                    $desarrollo->externo_ancho = $externo_ancho;
                    $desarrollo->externo_alto = $externo_alto;
                    $desarrollo->d1 = $d1;
                    $desarrollo->d2 = $d2;
                    $desarrollo->dh = $dh;
                    // $desarrollo->factor_desarrollo = $factor_desarrollo;
                    // $desarrollo->rubro_id = $rubro;
                    // $desarrollo->envase_id = $envase;
                    // dd($desarrollo, $desarrollo->isDirty(), $desarrollo->getChanges(), $desarrollo->isDirty("desperdicio"));
                    if ($desarrollo->isDirty()) {

                        $desarrollosActualizados[] = $desarrollo;

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($desarrollo, $row, "UPDATE", $codigo_operacion);
                            $desarrollo->save();
                            continue;
                        }
                    }
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $desarrollos_ingresados = [];
        $desarrollos_actualizados = [];
        $desarrollos_inactivados = [];
        $desarrollos_error = [];

        if (isset($desarrollosActualizados)) {
            $updated = 'Los siguientes Factores de desarrollo fueron actualizados:';
            $desarrollos_actualizados = $desarrollosActualizados;
        }
        if (isset($desarrollosInvalidos)) {
            $error = 'Los siguientes Factores de desarrollo tienen 1 o mas errores';
            $desarrollos_error = $desarrollosInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'desarrollos' => $desarrollos_ingresados,
            'desarrollos_actualizados' => $desarrollos_actualizados,
            'desarrollos_inactivados' => $desarrollos_inactivados,
            'desarrollos_error' => $desarrollos_error

        ], 200);
    }

    public function descargar_excel_factores_desarrollo(Request $request)
    {
        $titulo = "Listado Factores Desarrollo";
        $desarrollos = FactoresDesarrollo::all();
        $desarrollos_array[] = array(
            'ID',
            'Tipo Onda',
            'Caja Entera',
            'externo_largo',
            'externo_ancho',
            'externo_alto',
            'd1',
            'd2',
            'dh'
        );

        foreach ($desarrollos as $desarrollo) {
            $desarrollos_array[] = array(
                $desarrollo->id,
                $desarrollo->tipo_onda,
                ($desarrollo->caja_entera == 1) ? "SI" : "NO",
                $desarrollo->externo_largo,
                $desarrollo->externo_ancho,
                $desarrollo->externo_alto,
                $desarrollo->d1,
                $desarrollo->d2,
                $desarrollo->dh,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($desarrollos_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Listado Factores Desarrollo', function ($sheet) use ($desarrollos_array) {
                $sheet->fromArray($desarrollos_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }


    //////////////FACTORES SEGURIDAD//////////////////
    ////////////////////////////////////////
    //////////////FACTORES SEGURIDAD/////////////////
    ////////////////////////////////////////
    //////////////FACTORES SEGURIDAD//////////////////
    public function cargaFactoresSeguridadForm()
    {
        $seguridades = FactoresSeguridad::all();
        return view('mantenedores.seguridades-masive', compact("seguridades"));
    }


    public function importFactoresSeguridad(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $rubros = Rubro::pluck('id', 'descripcion')->toArray();
        $envases = Envase::pluck('id', 'descripcion')->toArray();
        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}
                $motivos = [];

                $factor_seguridad = is_numeric($row->factor_seguridad) ? (int) $row->factor_seguridad : false;
                $rubro = array_key_exists($row->rubro, $rubros) ? $rubros[$row->rubro] : false;
                $envase = array_key_exists($row->envase, $envases) ? $envases[$row->envase] : false;
                if ($factor_seguridad === false) $motivos[] = " Factor de Seguridad";
                if (!$rubro) $motivos[] = " Rubro";
                if (!$envase) $motivos[] = " Envase";

                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $seguridades = new stdClass();
                    $seguridades->linea = $key + 2;
                    $seguridades->motivos = $motivos;
                    $seguridadesInvalidos[] = $seguridades;
                    continue;
                }


                $seguridad = FactoresSeguridad::where('id', $row->id)->first();
                // dd($user);
                if ($seguridad) {

                    $seguridad->factor_seguridad = $factor_seguridad;
                    $seguridad->rubro_id = $rubro;
                    $seguridad->envase_id = $envase;
                    // dd($seguridad, $seguridad->isDirty(), $seguridad->getChanges(), $seguridad->isDirty("desperdicio"));
                    if ($seguridad->isDirty()) {
                        // $seguridad->orden = $key + 2;

                        $seguridadesActualizados[] = $seguridad;

                        // dd($seguridad->getDirty(), $seguridad);

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($seguridad, $row, "UPDATE", $codigo_operacion);
                            $seguridad->save();
                            continue;
                        }
                    }
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $seguridades_ingresados = [];
        $seguridades_actualizados = [];
        $seguridades_inactivados = [];
        $seguridades_error = [];

        if (isset($seguridadesActualizados)) {
            $updated = 'Los siguientes Factores de seguridad fueron actualizados:';
            $seguridades_actualizados = $seguridadesActualizados;
        }
        if (isset($seguridadesInvalidos)) {
            $error = 'Los siguientes Factores de seguridad tienen 1 o mas errores';
            $seguridades_error = $seguridadesInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'seguridades' => $seguridades_ingresados,
            'seguridades_actualizados' => $seguridades_actualizados,
            'seguridades_inactivados' => $seguridades_inactivados,
            'seguridades_error' => $seguridades_error

        ], 200);
    }

    public function descargar_excel_factores_seguridad(Request $request)
    {
        $titulo = "Listado Factores Seguridad";
        $seguridades = FactoresSeguridad::all();
        $seguridades_array[] = array(
            'ID',
            'rubro',
            'envase',
            'factor_seguridad'
        );

        foreach ($seguridades as $seguridad) {
            $seguridades_array[] = array(
                $seguridad->id,
                $seguridad->rubro->descripcion,
                $seguridad->envase->descripcion,
                $seguridad->factor_seguridad,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($seguridades_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Listado Factores Seguridad', function ($sheet) use ($seguridades_array) {
                $sheet->fromArray($seguridades_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }


    //////////////FACTORES ONDA//////////////////
    ////////////////////////////////////////
    //////////////FACTORES ONDA/////////////////
    ////////////////////////////////////////
    //////////////FACTORES ONDA//////////////////
    public function cargaFactoresOndaForm()
    {
        $ondas = FactoresOnda::all();
        return view('mantenedores.ondas-masive', compact("ondas"));
    }


    public function importFactoresOnda(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $plantas = Planta::pluck('id', 'nombre')->toArray();
        $ondas_validas = ["C", "B",  "E"];
        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}
                $motivos = [];

                $planta = array_key_exists($row->planta, $plantas) ? $plantas[$row->planta] : false;
                $tipo_onda = in_array(strtoupper($row->onda), $ondas_validas) ? strtoupper($row->onda) : false;
                $factor_onda = is_numeric($row->factor_onda) ? $row->factor_onda : false;

                if (!$planta) $motivos[] = " Planta";
                if (!$tipo_onda) $motivos[] = " Onda";
                if ($factor_onda === false) $motivos[] = " Factor Onda";

                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $ondas = new stdClass();
                    $ondas->linea = $key + 2;
                    $ondas->motivos = $motivos;
                    $ondasInvalidos[] = $ondas;
                    continue;
                }


                $onda = FactoresOnda::where('id', $row->id)->first();
                // dd($onda);
                if ($onda) {
                    $onda->planta_id = $planta;
                    $onda->onda = $tipo_onda;
                    $onda->factor_onda = $factor_onda;
                    // dd($onda);
                    // dd($onda, $onda->isDirty(), $onda->getChanges(), $onda->isDirty("desperdicio"));
                    if ($onda->isDirty()) {
                        $ondasActualizados[] = $onda;

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($onda, $row, "UPDATE", $codigo_operacion);
                            $onda->save();
                            continue;
                        }
                    }
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }

        $ondas_ingresados = [];
        $ondas_actualizados = [];
        $ondas_error = [];

        if (isset($ondasActualizados)) {
            $updated = 'Los siguientes Factores de onda fueron actualizados:';
            $ondas_actualizados = $ondasActualizados;
        }
        if (isset($ondasInvalidos)) {
            $error = 'Los siguientes Factores de onda tienen 1 o mas errores';
            $ondas_error = $ondasInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'ondas' => $ondas_ingresados,
            'ondas_actualizados' => $ondas_actualizados,
            'ondas_error' => $ondas_error

        ], 200);
    }

    public function descargar_excel_factores_onda(Request $request)
    {
        $titulo = "Listado Factores Onda";
        $ondas = FactoresOnda::all();
        $ondas_array[] = array(
            'ID',
            'planta',
            'onda',
            'factor_onda'
        );

        foreach ($ondas as $onda) {
            $ondas_array[] = array(
                $onda->id,
                $onda->planta->nombre,
                $onda->onda,
                $onda->factor_onda,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($ondas_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Listado Factores Onda', function ($sheet) use ($ondas_array) {
                $sheet->fromArray($ondas_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }




    //////////////MAQUILAS//////////////////
    ////////////////////////////////////////
    //////////////MAQUILAS/////////////////
    ////////////////////////////////////////
    //////////////MAQUILAS//////////////////
    public function cargaMaquilasForm()
    {
        $maquilas = MaquilaServicio::all();
        return view('mantenedores.maquilas-masive', compact("maquilas"));
    }


    public function importMaquilas(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $plantas = Planta::pluck('id', 'nombre')->toArray();
        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}
                $motivos = [];
                // dd($row);
                $servicio = (trim($row->servicio) != "") ? $row->servicio : false;
                $desgajado = is_numeric($row->desgajado) || $row->desgajado == null ? $row->desgajado : false;
                $ensamblado = is_numeric($row->ensamblado) || $row->ensamblado == null ? $row->ensamblado : false;
                $pegado = is_numeric($row->pegado) || $row->pegado == null ? $row->pegado : false;
                $flejado = is_numeric($row->flejado) || $row->flejado == null ? $row->flejado : false;
                $paletizado = is_numeric($row->paletizado) || $row->paletizado == null ? $row->paletizado : false;
                $empaquetado = is_numeric($row->empaquetado) || $row->empaquetado == null ? $row->empaquetado : false;

                if ($servicio === false) $motivos[] = " Servicio";
                if ($desgajado === false) $motivos[] = " desgajado";
                if ($ensamblado === false) $motivos[] = " ensamblado";
                if ($pegado === false) $motivos[] = " pegado";
                if ($flejado === false) $motivos[] = " flejado";
                if ($paletizado === false) $motivos[] = " paletizado";
                if ($empaquetado === false) $motivos[] = " empaquetado";

                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $maquilas = new stdClass();
                    $maquilas->linea = $key + 2;
                    $maquilas->motivos = $motivos;
                    $maquilasInvalidos[] = $maquilas;
                    continue;
                }


                $maquila = MaquilaServicio::where('id', $row->id)->first();
                // dd($maquila);
                if ($maquila) {
                    $maquila->servicio = $servicio;
                    $maquila->desgajado = $desgajado;
                    $maquila->ensamblado = $ensamblado;
                    $maquila->pegado = $pegado;
                    $maquila->flejado = $flejado;
                    $maquila->palletizado = $paletizado;
                    $maquila->empaquetado = $empaquetado;
                    // dd($maquila);
                    // dd($maquila, $maquila->isDirty(), $maquila->getChanges(), $maquila->isDirty("desperdicio"));
                    if ($maquila->isDirty()) {
                        $maquilasActualizados[] = $maquila;

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($maquila, $row, "UPDATE", $codigo_operacion);
                            $maquila->save();
                            continue;
                        }
                    }
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }

        $maquilas_ingresados = [];
        $maquilas_actualizados = [];
        $maquilas_error = [];

        if (isset($maquilasActualizados)) {
            $updated = 'Los siguientes Servicios de Maquila fueron actualizados:';
            $maquilas_actualizados = $maquilasActualizados;
        }
        if (isset($maquilasInvalidos)) {
            $error = 'Los siguientes Servicios de Maquila tienen 1 o mas errores';
            $maquilas_error = $maquilasInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'maquilas' => $maquilas_ingresados,
            'maquilas_actualizados' => $maquilas_actualizados,
            'maquilas_error' => $maquilas_error

        ], 200);
    }


    public function descargar_excel_maquilas(Request $request)
    {
        $titulo = "Listado Maquilas";
        $maquilas = MaquilaServicio::all();
        $maquilas_array[] = array(
            'ID',
            'Servicio',
            'Desgajado',
            'Ensamblado',
            'Pegado',
            'Flejado',
            'Paletizado',
            'Empaquetado'
        );

        foreach ($maquilas as $maquila) {
            $maquilas_array[] = array(
                $maquila->id,
                $maquila->servicio,
                // $maquila->productType->descripcion,
                $maquila->desgajado,
                $maquila->ensamblado,
                $maquila->pegado,
                $maquila->flejado,
                $maquila->palletizado,
                $maquila->empaquetado
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($maquilas_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Servicios Maquila', function ($sheet) use ($maquilas_array) {
                $sheet->fromArray($maquilas_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }


    //////////////ONDAS//////////////////
    ////////////////////////////////////////
    //////////////ONDAS/////////////////
    ////////////////////////////////////////
    //////////////ONDAS//////////////////
    public function cargaOndasForm()
    {
        $ondas = TipoOnda::all();
        return view('mantenedores.tipo-ondas-masive', compact("ondas"));
    }



    public function importOndas(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}
                $motivos = [];
                // dd($row);
                $espesor_promedio = is_numeric($row->espesor_promedio) || $row->espesor_promedio == null ? $row->espesor_promedio : false;
                $espesor_maximo = is_numeric($row->espesor_maximo) || $row->espesor_maximo == null ? $row->espesor_maximo : false;
                $espesor_minimo = is_numeric($row->espesor_minimo) || $row->espesor_minimo == null ? $row->espesor_minimo : false;

                if ($espesor_promedio === false) $motivos[] = " espesor_promedio";
                if ($espesor_maximo === false) $motivos[] = " espesor_maximo";
                if ($espesor_minimo === false) $motivos[] = " espesor_minimo";

                if (count($motivos) >= 1) {

                    // dd($motivos);
                    $ondas = new stdClass();
                    $ondas->linea = $key + 2;
                    $ondas->motivos = $motivos;
                    $ondasInvalidos[] = $ondas;
                    continue;
                }


                $onda = TipoOnda::where('id', $row->id)->first();
                // dd($onda);
                if ($onda) {
                    $onda->espesor_promedio = $espesor_promedio;
                    $onda->espesor_maximo = $espesor_maximo;
                    $onda->espesor_minimo = $espesor_minimo;
                    // dd($onda);
                    // dd($onda, $onda->isDirty(), $onda->getChanges(), $onda->isDirty("desperdicio"));
                    if ($onda->isDirty()) {
                        $ondasActualizados[] = $onda;

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($onda, $row, "UPDATE", $codigo_operacion);
                            $onda->save();
                            continue;
                        }
                    }
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }

        $ondas_ingresados = [];
        $ondas_actualizados = [];
        $ondas_error = [];

        if (isset($ondasActualizados)) {
            $updated = 'Los siguientes Servicios de Maquila fueron actualizados:';
            $ondas_actualizados = $ondasActualizados;
        }
        if (isset($ondasInvalidos)) {
            $error = 'Los siguientes Servicios de Maquila tienen 1 o mas errores';
            $ondas_error = $ondasInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'ondas' => $ondas_ingresados,
            'ondas_actualizados' => $ondas_actualizados,
            'ondas_error' => $ondas_error

        ], 200);
    }


    public function descargar_excel_ondas(Request $request)
    {
        $titulo = "Listado Tipos de Ondas";
        $ondas = TipoOnda::all();
        $ondas_array[] = array(
            'ID',
            'Onda',
            'espesor_promedio',
            'espesor_maximo',
            'espesor_minimo'
        );

        foreach ($ondas as $onda) {
            $ondas_array[] = array(
                $onda->id,
                $onda->onda,
                $onda->espesor_promedio,
                $onda->espesor_maximo,
                $onda->espesor_minimo,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($ondas_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Listado Ondas', function ($sheet) use ($ondas_array) {
                $sheet->fromArray($ondas_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }


    //////////////PLANTAS//////////////////
    ////////////////////////////////////////
    //////////////PLANTAS/////////////////
    ////////////////////////////////////////
    //////////////PLANTAS//////////////////
    public function cargaPlantasForm()
    {
        $plantas = Planta::all();
        return view('mantenedores.plantas-masive', compact("plantas"));
    }


    public function importPlantas(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}

                $motivos = [];
                // Validaciones generales
                // dd(is_numeric($row->tolerancia_gramaje_real), $row->tolerancia_gramaje_real, in_array($row->onda, $ondas_validas));
                $nombre = (trim($row->nombre) != "") ? $row->nombre : false;
                $ancho_corrugadora = is_numeric($row->ancho_corrugadora) ? $row->ancho_corrugadora : false;
                $trim_corrugadora = is_numeric($row->trim_corrugadora) ? $row->trim_corrugadora : false;
                $merma_pegado = is_numeric($row->merma_pegado) ? $row->merma_pegado : false;
                $merma_cera = is_numeric($row->merma_cera) || trim($row->merma_cera) == ""  ? $row->merma_cera : false;
                // dd(!$merma_cera, $merma_cera !== 0, $merma_cera != null);
                $precio_adhesivo = is_numeric($row->precio_adhesivo) ? $row->precio_adhesivo : false;
                $precio_adhesivo_powerply = is_numeric($row->precio_adhesivo_powerply) ? $row->precio_adhesivo_powerply : false;
                $costo_tinta_usd_gr = is_numeric($row->costo_tinta_usd_gr) ? $row->costo_tinta_usd_gr : false;
                $consumo_tinta_gr_x_mm2 = is_numeric($row->consumo_tinta_gr_x_mm2) ? $row->consumo_tinta_gr_x_mm2 : false;
                $costo_cera_usd_gr = is_numeric($row->costo_cera_usd_gr) || trim($row->costo_cera_usd_gr) == "" ? $row->costo_cera_usd_gr : false;
                $consumo_cera_gr_x_mm2 = is_numeric($row->consumo_cera_gr_x_mm2) || trim($row->consumo_cera_gr_x_mm2) == "" ? $row->consumo_cera_gr_x_mm2 : false;
                $costo_clisse_clp_cm2 = is_numeric($row->costo_clisse_clp_cm2) ? $row->costo_clisse_clp_cm2 : false;
                $valor_tablero_clp = is_numeric($row->valor_tablero_clp) ? $row->valor_tablero_clp : false;
                $ancho_matriz_estandar = is_numeric($row->ancho_matriz_estandar) ? $row->ancho_matriz_estandar : false;
                $largo_matriz_estandar = is_numeric($row->largo_matriz_estandar) ? $row->largo_matriz_estandar : false;
                $valor_cuchillos_y_gomas_clp = is_numeric($row->valor_cuchillos_y_gomas_clp) ? $row->valor_cuchillos_y_gomas_clp : false;
                $costo_pallet_clp = is_numeric($row->costo_pallet_clp) ? $row->costo_pallet_clp : false;
                $zuncho_metros_por_rollo = is_numeric($row->zuncho_metros_por_rollo) ? $row->zuncho_metros_por_rollo : false;
                $zuncho_precio_rollo_usd = is_numeric($row->zuncho_precio_rollo_usd) ? $row->zuncho_precio_rollo_usd : false;
                $zuncho_metros_por_pallet = is_numeric($row->zuncho_metros_por_pallet) ? $row->zuncho_metros_por_pallet : false;
                $funda_costo_clp_pallet = is_numeric($row->funda_costo_clp_pallet) ? $row->funda_costo_clp_pallet : false;
                $film_gramos_pallet = is_numeric($row->film_gramos_pallet) ? $row->film_gramos_pallet : false;
                $film_usd_kg = is_numeric($row->film_usd_kg) ? $row->film_usd_kg : false;
                $precio_cinta_clp_m = is_numeric($row->precio_cinta_clp_m) ? $row->precio_cinta_clp_m : false;
                $precio_cinta_usd_mm = is_numeric($row->precio_cinta_usd_mm) ? $row->precio_cinta_usd_mm : false;
                $costo_adhesivo_pegado_usd_kg = is_numeric($row->costo_adhesivo_pegado_usd_kg) ? $row->costo_adhesivo_pegado_usd_kg : false;
                $precio_energia_clp_kwh = is_numeric($row->precio_energia_clp_kwh) ? $row->precio_energia_clp_kwh : false;
                $consumo_gas_caldera_mmbtu_ton = is_numeric($row->consumo_gas_caldera_mmbtu_ton) ? $row->consumo_gas_caldera_mmbtu_ton : false;
                $precio_gas_caldera_clp_mmbtu = is_numeric($row->precio_gas_caldera_clp_mmbtu) ? $row->precio_gas_caldera_clp_mmbtu : false;
                $consumo_gas_gruas_mmbtu_mm2 = is_numeric($row->consumo_gas_gruas_mmbtu_mm2) ? $row->consumo_gas_gruas_mmbtu_mm2 : false;
                $precio_gas_gruas_clp_mmbtu = is_numeric($row->precio_gas_gruas_clp_mmbtu) ? $row->precio_gas_gruas_clp_mmbtu : false;
                $zuncho_precio_por_pallet_usd = is_numeric($row->zuncho_precio_por_pallet_usd) ? $row->zuncho_precio_por_pallet_usd : false;
                $funda_precio_por_pallet_usd = is_numeric($row->funda_precio_por_pallet_usd) ? $row->funda_precio_por_pallet_usd : false;
                $film_precio_por_pallet_usd = is_numeric($row->film_precio_por_pallet_usd) ? $row->film_precio_por_pallet_usd : false;

                $costo_barniz_usd_gr = is_numeric($row->costo_barniz_usd_gr) ? $row->costo_barniz_usd_gr : false;
                $consumo_barniz_gr_x_Mm2 = is_numeric($row->consumo_barniz_gr_x_mm2) ? $row->consumo_barniz_gr_x_mm2 : false;
                $costo_tinta_usd_gr_alta_grafica_especial = is_numeric($row->costo_tinta_usd_gr_alta_grafica_especial) ? $row->costo_tinta_usd_gr_alta_grafica_especial : false;
                $consumo_tinta_usd_gr_alta_grafica_especial = is_numeric($row->consumo_tinta_usd_gr_alta_grafica_especial) ? $row->consumo_tinta_usd_gr_alta_grafica_especial : false;
                $costo_tinta_usd_gr_alta_grafica_metalizada = is_numeric($row->costo_tinta_usd_gr_alta_grafica_metalizada) ? $row->costo_tinta_usd_gr_alta_grafica_metalizada : false;
                $consumo_tinta_usd_gr_alta_grafica_metalizado = is_numeric($row->consumo_tinta_usd_gr_alta_grafica_metalizado) ? $row->consumo_tinta_usd_gr_alta_grafica_metalizado : false;
                $costo_tinta_usd_gr_alta_grafica_otras = is_numeric($row->costo_tinta_usd_gr_alta_grafica_otras) ? $row->costo_tinta_usd_gr_alta_grafica_otras : false;
                $consumo_tinta_usd_gr_alta_grafica_otras = is_numeric($row->consumo_tinta_usd_gr_alta_grafica_otras) ? $row->consumo_tinta_usd_gr_alta_grafica_otras : false;
                $costo_barniz_acuoso_usd_gr = is_numeric($row->costo_barniz_acuoso_usd_gr) ? $row->costo_barniz_acuoso_usd_gr : false;
                $consumo_barniz_acuoso_gr_x_Mm2 = is_numeric($row->consumo_barniz_acuoso_gr_x_mm2) ? $row->consumo_barniz_acuoso_gr_x_mm2 : false;
                $costo_barniz_uv_usd_gr = is_numeric($row->costo_barniz_uv_usd_gr) ? $row->costo_barniz_uv_usd_gr : false;
                $consumo_barniz_uv_gr_x_Mm2 = is_numeric($row->consumo_barniz_uv_gr_x_mm2) ? $row->consumo_barniz_uv_gr_x_mm2 : false;
                $mano_de_obra_pegado_usd_x_Mm2 = is_numeric($row->mano_de_obra_pegado_usd_x_mm2) ? $row->mano_de_obra_pegado_usd_x_mm2 : false;
                $mano_de_obra_pegado_ag_usdx_Mm2 = is_numeric($row->mano_de_obra_pegado_ag_usdx_mm2) ? $row->mano_de_obra_pegado_ag_usdx_mm2 : false;
                //if($row->nombre=='TIL TIL'){ dd(is_numeric($row->consumo_tinta_usd_gr_alta_grafica_especial),!$consumo_tinta_usd_gr_alta_grafica_especial,$consumo_tinta_usd_gr_alta_grafica_especial,$row->consumo_tinta_usd_gr_alta_grafica_especial);}
                if (!$nombre) $motivos[] = " Nombre";
                if (!$ancho_corrugadora && $ancho_corrugadora !== 0) $motivos[] = " Ancho Corrugadora";
                if (!$trim_corrugadora && $trim_corrugadora !== 0) $motivos[] = " Trim Corrugadora";

                if (!$merma_pegado && $merma_pegado !== 0 && $merma_pegado !== 0.0) $motivos[] = " merma_pegado";
                if (!$merma_cera && $merma_cera !== 0 && $merma_cera !== 0.0 && $merma_cera != null) $motivos[] = " merma_cera";
                if (!$precio_adhesivo && $precio_adhesivo !== 0 && $precio_adhesivo !== 0.0) $motivos[] = " precio_adhesivo";
                if (!$precio_adhesivo_powerply && $precio_adhesivo_powerply !== 0 && $precio_adhesivo_powerply !== 0.0) $motivos[] = " precio_adhesivo_powerply";
                if (!$costo_tinta_usd_gr && $costo_tinta_usd_gr !== 0 && $costo_tinta_usd_gr !== 0.0) $motivos[] = " costo_tinta_usd_gr";
                if (!$consumo_tinta_gr_x_mm2 && $consumo_tinta_gr_x_mm2 !== 0 && $consumo_tinta_gr_x_mm2 !== 0.0) $motivos[] = " consumo_tinta_gr_x_mm2";
                if (!$costo_cera_usd_gr && $costo_cera_usd_gr !== 0 && $costo_cera_usd_gr !== 0.0 && $costo_cera_usd_gr != null) $motivos[] = " costo_cera_usd_gr";
                if (!$consumo_cera_gr_x_mm2 && $consumo_cera_gr_x_mm2 !== 0 && $consumo_cera_gr_x_mm2 !== 0.0 && $consumo_cera_gr_x_mm2 != null) $motivos[] = " consumo_cera_gr_x_mm2";
                if (!$costo_clisse_clp_cm2 && $costo_clisse_clp_cm2 !== 0 && $costo_clisse_clp_cm2 !== 0.0) $motivos[] = " costo_clisse_clp_cm2";
                if (!$valor_tablero_clp && $valor_tablero_clp !== 0 && $valor_tablero_clp !== 0.0) $motivos[] = " valor_tablero_clp";
                if (!$ancho_matriz_estandar && $ancho_matriz_estandar !== 0 && $ancho_matriz_estandar !== 0.0) $motivos[] = " ancho_matriz_estandar";
                if (!$largo_matriz_estandar && $largo_matriz_estandar !== 0 && $largo_matriz_estandar !== 0.0) $motivos[] = " largo_matriz_estandar";
                if (!$valor_cuchillos_y_gomas_clp && $valor_cuchillos_y_gomas_clp !== 0 && $valor_cuchillos_y_gomas_clp !== 0.0) $motivos[] = " valor_cuchillos_y_gomas_clp";
                if (!$costo_pallet_clp && $costo_pallet_clp !== 0 && $costo_pallet_clp !== 0.0) $motivos[] = " costo_pallet_clp";
                if (!$zuncho_metros_por_rollo && $zuncho_metros_por_rollo !== 0 && $zuncho_metros_por_rollo !== 0.0) $motivos[] = " zuncho_metros_por_rollo";
                if (!$zuncho_precio_rollo_usd && $zuncho_precio_rollo_usd !== 0 && $zuncho_precio_rollo_usd !== 0.0) $motivos[] = " zuncho_precio_rollo_usd";
                if (!$zuncho_metros_por_pallet && $zuncho_metros_por_pallet !== 0 && $zuncho_metros_por_pallet !== 0.0) $motivos[] = " zuncho_metros_por_pallet";
                if (!$funda_costo_clp_pallet && $funda_costo_clp_pallet !== 0 && $funda_costo_clp_pallet !== 0.0) $motivos[] = " funda_costo_clp_pallet";
                if (!$film_gramos_pallet && $film_gramos_pallet !== 0 && $film_gramos_pallet !== 0.0) $motivos[] = " film_gramos_pallet";
                if (!$film_usd_kg && $film_usd_kg !== 0 && $film_usd_kg !== 0.0) $motivos[] = " film_usd_kg";
                if (!$precio_cinta_clp_m && $precio_cinta_clp_m !== 0 && $precio_cinta_clp_m !== 0.0) $motivos[] = " precio_cinta_clp_m";
                if (!$precio_cinta_usd_mm && $precio_cinta_usd_mm !== 0 && $precio_cinta_usd_mm !== 0.0) $motivos[] = " precio_cinta_usd_mm";
                if (!$costo_adhesivo_pegado_usd_kg && $costo_adhesivo_pegado_usd_kg !== 0 && $costo_adhesivo_pegado_usd_kg !== 0.0) $motivos[] = " costo_adhesivo_pegado_usd_kg";
                if (!$precio_energia_clp_kwh && $precio_energia_clp_kwh !== 0 && $precio_energia_clp_kwh !== 0.0) $motivos[] = " precio_energia_clp_kwh";
                if (!$consumo_gas_caldera_mmbtu_ton && $consumo_gas_caldera_mmbtu_ton !== 0 && $consumo_gas_caldera_mmbtu_ton !== 0.0) $motivos[] = " consumo_gas_caldera_mmbtu_ton";
                if (!$precio_gas_caldera_clp_mmbtu && $precio_gas_caldera_clp_mmbtu !== 0 && $precio_gas_caldera_clp_mmbtu !== 0.0) $motivos[] = " precio_gas_caldera_clp_mmbtu";
                if (!$consumo_gas_gruas_mmbtu_mm2 && $consumo_gas_gruas_mmbtu_mm2 !== 0 && $consumo_gas_gruas_mmbtu_mm2 !== 0.0) $motivos[] = " consumo_gas_gruas_mmbtu_mm2";
                if (!$precio_gas_gruas_clp_mmbtu && $precio_gas_gruas_clp_mmbtu !== 0 && $precio_gas_gruas_clp_mmbtu !== 0.0) $motivos[] = " precio_gas_gruas_clp_mmbtu";
                if (!$zuncho_precio_por_pallet_usd && $zuncho_precio_por_pallet_usd !== 0 && $zuncho_precio_por_pallet_usd !== 0.0) $motivos[] = " zuncho_precio_por_pallet_usd";
                if (!$funda_precio_por_pallet_usd && $funda_precio_por_pallet_usd !== 0 && $funda_precio_por_pallet_usd !== 0.0) $motivos[] = " funda_precio_por_pallet_usd";
                if (!$film_precio_por_pallet_usd && $film_precio_por_pallet_usd !== 0 && $film_precio_por_pallet_usd !== 0.0) $motivos[] = " film_precio_por_pallet_usd";

                if (!$costo_barniz_usd_gr && $costo_barniz_usd_gr !== 0 && $costo_barniz_usd_gr !== 0.0) $motivos[] = " costo_barniz_usd_gr";
                if (!$consumo_barniz_gr_x_Mm2 && $consumo_barniz_gr_x_Mm2 !== 0 && $consumo_barniz_gr_x_Mm2 !== 0.0) $motivos[] = " consumo_barniz_gr_x_Mm2";
                if (!$costo_tinta_usd_gr_alta_grafica_especial && $costo_tinta_usd_gr_alta_grafica_especial !== 0 && $costo_tinta_usd_gr_alta_grafica_especial !== 0.0) $motivos[] = " costo_tinta_usd_gr_alta_grafica_especial";
                if (!$consumo_tinta_usd_gr_alta_grafica_especial && $consumo_tinta_usd_gr_alta_grafica_especial !== 0 &&  $consumo_tinta_usd_gr_alta_grafica_especial !== 0.0) $motivos[] = " consumo_tinta_usd_gr_alta_grafica_especial";
                if (!$costo_tinta_usd_gr_alta_grafica_metalizada && $costo_tinta_usd_gr_alta_grafica_metalizada !== 0 && $costo_tinta_usd_gr_alta_grafica_metalizada !== 0.0) $motivos[] = " costo_tinta_usd_gr_alta_grafica_metalizada";
                if (!$consumo_tinta_usd_gr_alta_grafica_metalizado && $consumo_tinta_usd_gr_alta_grafica_metalizado !== 0 && $consumo_tinta_usd_gr_alta_grafica_metalizado !== 0.0) $motivos[] = " consumo_tinta_usd_gr_alta_grafica_metalizado";
                if (!$costo_tinta_usd_gr_alta_grafica_otras && $costo_tinta_usd_gr_alta_grafica_otras !== 0 && $costo_tinta_usd_gr_alta_grafica_otras !== 0.0) $motivos[] = " costo_tinta_usd_gr_alta_grafica_otras";
                if (!$consumo_tinta_usd_gr_alta_grafica_otras && $consumo_tinta_usd_gr_alta_grafica_otras !== 0 && $consumo_tinta_usd_gr_alta_grafica_otras !== 0.0) $motivos[] = " consumo_tinta_usd_gr_alta_grafica_otras";
                if (!$costo_barniz_acuoso_usd_gr && $costo_barniz_acuoso_usd_gr !== 0 && $costo_barniz_acuoso_usd_gr !== 0.0) $motivos[] = " costo_barniz_acuoso_usd_gr";
                if (!$consumo_barniz_acuoso_gr_x_Mm2 && $consumo_barniz_acuoso_gr_x_Mm2 !== 0 && $consumo_barniz_acuoso_gr_x_Mm2 !== 0.0) $motivos[] = " consumo_barniz_acuoso_gr_x_Mm2";
                if (!$costo_barniz_uv_usd_gr && $costo_barniz_uv_usd_gr !== 0 && $costo_barniz_uv_usd_gr !== 0.0) $motivos[] = " costo_barniz_uv_usd_gr";
                if (!$consumo_barniz_uv_gr_x_Mm2 && $consumo_barniz_uv_gr_x_Mm2 !== 0 && $consumo_barniz_uv_gr_x_Mm2 !== 0.0) $motivos[] = " consumo_barniz_uv_gr_x_Mm2";
                if (!$mano_de_obra_pegado_usd_x_Mm2 && $mano_de_obra_pegado_usd_x_Mm2 !== 0 && $mano_de_obra_pegado_usd_x_Mm2 !== 0.0) $motivos[] = " mano_de_obra_pegado_usd_x_Mm2";
                if (!$mano_de_obra_pegado_ag_usdx_Mm2 && $mano_de_obra_pegado_ag_usdx_Mm2 !== 0 && $mano_de_obra_pegado_ag_usdx_Mm2 !== 0.0) $motivos[] = " mano_de_obra_pegado_ag_usdx_Mm2";

                if (count($motivos) >= 1) {
                    $plantaErroneo = new stdClass();
                    $plantaErroneo->linea = $key + 2;
                    $plantaErroneo->motivos = $motivos;
                    $plantasInvalidos[] = $plantaErroneo;
                    continue;
                }

                $planta = Planta::where('id', $row->id)->first();
                // dd($user);
                if ($planta) {

                    $planta->ancho_corrugadora = $ancho_corrugadora;
                    $planta->trim_corrugadora = $trim_corrugadora;
                    $planta->merma_pegado = $merma_pegado;
                    $planta->merma_cera = $merma_cera;
                    $planta->precio_adhesivo = $precio_adhesivo;
                    $planta->precio_adhesivo_powerply = $precio_adhesivo_powerply;
                    $planta->costo_tinta_usd_gr = $costo_tinta_usd_gr;
                    $planta->consumo_tinta_gr_x_Mm2 = $consumo_tinta_gr_x_mm2;
                    $planta->costo_cera_usd_gr = $costo_cera_usd_gr;
                    $planta->consumo_cera_gr_x_Mm2 = $consumo_cera_gr_x_mm2;
                    $planta->costo_clisse_clp_cm2 = $costo_clisse_clp_cm2;
                    $planta->valor_tablero_clp = $valor_tablero_clp;
                    $planta->ancho_matriz_estandar = $ancho_matriz_estandar;
                    $planta->largo_matriz_estandar = $largo_matriz_estandar;
                    $planta->valor_cuchillos_y_gomas_clp = $valor_cuchillos_y_gomas_clp;
                    $planta->costo_pallet_clp = $costo_pallet_clp;
                    $planta->zuncho_metros_por_rollo = $zuncho_metros_por_rollo;
                    $planta->zuncho_precio_rollo_usd = $zuncho_precio_rollo_usd;
                    $planta->zuncho_metros_por_pallet = $zuncho_metros_por_pallet;
                    $planta->funda_costo_clp_pallet = $funda_costo_clp_pallet;
                    $planta->film_gramos_pallet = $film_gramos_pallet;
                    $planta->film_usd_kg = $film_usd_kg;
                    $planta->precio_cinta_clp_m = $precio_cinta_clp_m;
                    $planta->precio_cinta_usd_mm = $precio_cinta_usd_mm;
                    $planta->costo_adhesivo_pegado_usd_kg = $costo_adhesivo_pegado_usd_kg;
                    $planta->precio_energia_clp_kwh = $precio_energia_clp_kwh;
                    $planta->consumo_gas_caldera_mmbtu_ton = $consumo_gas_caldera_mmbtu_ton;
                    $planta->precio_gas_caldera_clp_mmbtu = $precio_gas_caldera_clp_mmbtu;
                    $planta->consumo_gas_gruas_mmbtu_mm2 = $consumo_gas_gruas_mmbtu_mm2;
                    $planta->precio_gas_gruas_clp_mmbtu = $precio_gas_gruas_clp_mmbtu;
                    $planta->zuncho_precio_por_pallet_usd = $zuncho_precio_por_pallet_usd;
                    $planta->funda_precio_por_pallet_usd = $funda_precio_por_pallet_usd;
                    $planta->film_precio_por_pallet_usd = $film_precio_por_pallet_usd;
                    $planta->costo_barniz_usd_gr = $costo_barniz_usd_gr;
                    $planta->consumo_barniz_gr_x_Mm2 = $consumo_barniz_gr_x_Mm2;
                    $planta->costo_tinta_usd_gr_alta_grafica_especial = $costo_tinta_usd_gr_alta_grafica_especial;
                    $planta->consumo_tinta_usd_gr_alta_grafica_especial = $consumo_tinta_usd_gr_alta_grafica_especial;
                    $planta->costo_tinta_usd_gr_alta_grafica_metalizada = $costo_tinta_usd_gr_alta_grafica_metalizada;
                    $planta->consumo_tinta_usd_gr_alta_grafica_metalizado = $consumo_tinta_usd_gr_alta_grafica_metalizado;
                    $planta->costo_tinta_usd_gr_alta_grafica_otras = $costo_tinta_usd_gr_alta_grafica_otras;
                    $planta->consumo_tinta_usd_gr_alta_grafica_otras = $consumo_tinta_usd_gr_alta_grafica_otras;
                    $planta->costo_barniz_acuoso_usd_gr = $costo_barniz_acuoso_usd_gr;
                    $planta->consumo_barniz_acuoso_gr_x_Mm2 = $consumo_barniz_acuoso_gr_x_Mm2;
                    $planta->costo_barniz_uv_usd_gr = $costo_barniz_uv_usd_gr;
                    $planta->consumo_barniz_uv_gr_x_Mm2 = $consumo_barniz_uv_gr_x_Mm2;
                    $planta->mano_de_obra_pegado_usd_x_Mm2 = $mano_de_obra_pegado_usd_x_Mm2;
                    $planta->mano_de_obra_pegado_ag_usdx_Mm2 = $mano_de_obra_pegado_ag_usdx_Mm2;

                    if ($planta->isDirty()) {
                        $plantasActualizados[] = $planta;
                        // dd($planta->getDirty(), $planta);

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($planta, $row, "UPDATE", $codigo_operacion);
                            $planta->save();
                            continue;
                        }
                    }
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $plantas_ingresados = [];
        $plantas_actualizados = [];
        $plantas_inactivados = [];
        $plantas_error = [];

        if (isset($plantasActualizados)) {
            $updated = 'Los siguientes plantas fueron actualizados:';
            $plantas_actualizados = $plantasActualizados;
        }
        if (isset($plantasInvalidos)) {
            $error = 'Los siguientes plantas tienen 1 o mas errores';
            $plantas_error = $plantasInvalidos;
        }



        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'plantas_actualizados' => $plantas_actualizados,
            'plantas_error' => $plantas_error

        ], 200);
    }

    public function descargar_excel_plantas(Request $request)
    {
        $titulo = "Listado Plantas";
        $plantas = Planta::all();
        // dd($plantas);
        $plantas_array[] = array(
            'ID',
            'nombre',
            'ancho_corrugadora',
            'trim_corrugadora',
            'merma_pegado',
            'merma_cera',
            'precio_adhesivo',
            'precio_adhesivo_powerply',
            'costo_tinta_usd_gr',
            'consumo_tinta_gr_x_mm2',
            'costo_cera_usd_gr',
            'consumo_cera_gr_x_mm2',
            'costo_clisse_clp_cm2',
            'valor_tablero_clp',
            'ancho_matriz_estandar',
            'largo_matriz_estandar',
            'valor_cuchillos_y_gomas_clp',
            'costo_pallet_clp',
            'zuncho_metros_por_rollo',
            'zuncho_precio_rollo_usd',
            'zuncho_metros_por_pallet',
            'funda_costo_clp_pallet',
            'film_gramos_pallet',
            'film_usd_kg',
            'precio_cinta_clp_m',
            'precio_cinta_usd_mm',
            'costo_adhesivo_pegado_usd_kg',
            'precio_energia_clp_kwh',
            'consumo_gas_caldera_mmbtu_ton',
            'precio_gas_caldera_clp_mmbtu',
            'consumo_gas_gruas_mmbtu_mm2',
            'precio_gas_gruas_clp_mmbtu',
            'zuncho_precio_por_pallet_usd',
            'funda_precio_por_pallet_usd',
            'film_precio_por_pallet_usd',
            'costo_barniz_usd_gr',
            'consumo_barniz_gr_x_Mm2',
            'costo_tinta_usd_gr_alta_grafica_especial',
            'consumo_tinta_usd_gr_alta_grafica_especial',
            'costo_tinta_usd_gr_alta_grafica_metalizada',
            'consumo_tinta_usd_gr_alta_grafica_metalizado',
            'costo_tinta_usd_gr_alta_grafica_otras',
            'consumo_tinta_usd_gr_alta_grafica_otras',
            'costo_barniz_acuoso_usd_gr',
            'consumo_barniz_acuoso_gr_x_Mm2',
            'costo_barniz_uv_usd_gr',
            'consumo_barniz_uv_gr_x_Mm2',
            'mano_de_obra_pegado_usd_x_Mm2',
            'mano_de_obra_pegado_ag_usdx_Mm2'

        );

        foreach ($plantas as $planta) {
            $plantas_array[] = array(
                $planta->id,
                $planta->nombre,
                $planta->ancho_corrugadora,
                $planta->trim_corrugadora,
                $planta->merma_pegado,
                $planta->merma_cera,
                $planta->precio_adhesivo,
                $planta->precio_adhesivo_powerply,
                $planta->costo_tinta_usd_gr,
                $planta->consumo_tinta_gr_x_Mm2,
                $planta->costo_cera_usd_gr,
                $planta->consumo_cera_gr_x_Mm2,
                $planta->costo_clisse_clp_cm2,
                $planta->valor_tablero_clp,
                $planta->ancho_matriz_estandar,
                $planta->largo_matriz_estandar,
                $planta->valor_cuchillos_y_gomas_clp,
                $planta->costo_pallet_clp,
                $planta->zuncho_metros_por_rollo,
                $planta->zuncho_precio_rollo_usd,
                $planta->zuncho_metros_por_pallet,
                $planta->funda_costo_clp_pallet,
                $planta->film_gramos_pallet,
                $planta->film_usd_kg,
                $planta->precio_cinta_clp_m,
                $planta->precio_cinta_usd_mm,
                $planta->costo_adhesivo_pegado_usd_kg,
                $planta->precio_energia_clp_kwh,
                $planta->consumo_gas_caldera_mmbtu_ton,
                $planta->precio_gas_caldera_clp_mmbtu,
                $planta->consumo_gas_gruas_mmbtu_mm2,
                $planta->precio_gas_gruas_clp_mmbtu,
                $planta->zuncho_precio_por_pallet_usd,
                $planta->funda_precio_por_pallet_usd,
                $planta->film_precio_por_pallet_usd,
                $planta->costo_barniz_usd_gr,
                $planta->consumo_barniz_gr_x_Mm2,
                $planta->costo_tinta_usd_gr_alta_grafica_especial,
                $planta->consumo_tinta_usd_gr_alta_grafica_especial,
                $planta->costo_tinta_usd_gr_alta_grafica_metalizada,
                $planta->consumo_tinta_usd_gr_alta_grafica_metalizado,
                $planta->costo_tinta_usd_gr_alta_grafica_otras,
                $planta->consumo_tinta_usd_gr_alta_grafica_otras,
                $planta->costo_barniz_acuoso_usd_gr,
                $planta->consumo_barniz_acuoso_gr_x_Mm2,
                $planta->costo_barniz_uv_usd_gr,
                $planta->consumo_barniz_uv_gr_x_Mm2,
                $planta->mano_de_obra_pegado_usd_x_Mm2,
                $planta->mano_de_obra_pegado_ag_usdx_Mm2,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($plantas_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Plantas', function ($sheet) use ($plantas_array) {
                $sheet->fromArray($plantas_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    //////////////VARIABLES//////////////////
    ////////////////////////////////////////
    //////////////VARIABLES/////////////////
    ////////////////////////////////////////
    //////////////VARIABLES//////////////////
    public function cargaVariablesForm()
    {
        $variables = VariablesCotizador::all();
        // $variables->makeHidden(['created_at', 'updated_at']);
        // dd($variables);
        return view('mantenedores.variables-masive', compact("variables"));
    }


    public function importVariables(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        // dd($data);

        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}

                $motivos = [];
                // Validaciones generales
                $precio_dolar = is_numeric($row->precio_dolar) ? (int) $row->precio_dolar : false;
                $esq_perdida_papel = is_numeric($row->esq_perdida_papel) ? $row->esq_perdida_papel : false;
                $esq_perdida_adhesivo = is_numeric($row->esq_perdida_adhesivo) ? $row->esq_perdida_adhesivo : false;
                $esq_recorte_esquineros = is_numeric($row->esq_recorte_esquineros) ? $row->esq_recorte_esquineros : false;
                $esq_consumo_adhesivo = is_numeric($row->esq_consumo_adhesivo) ? $row->esq_consumo_adhesivo : false;
                $esq_costo_impresion_offset = is_numeric($row->esq_costo_impresion_offset) ? $row->esq_costo_impresion_offset : false;
                $esq_esquineros_por_pallet = is_numeric($row->esq_esquineros_por_pallet) ? $row->esq_esquineros_por_pallet : false;
                $esq_merma_costo_impresion = is_numeric($row->esq_merma_costo_impresion) ? $row->esq_merma_costo_impresion : false;
                $esq_precio_adhesivo = is_numeric($row->esq_precio_adhesivo) ? $row->esq_precio_adhesivo : false;
                $esq_precio_tinta_usd_kg = is_numeric($row->esq_precio_tinta_usd_kg) ? $row->esq_precio_tinta_usd_kg : false;
                $esq_consumo_tinta_g_m2 = is_numeric($row->esq_consumo_tinta_g_m2) ? $row->esq_consumo_tinta_g_m2 : false;
                $esq_precio_clisses_clp_cm2 = is_numeric($row->esq_precio_clisses_clp_cm2) ? $row->esq_precio_clisses_clp_cm2 : false;
                $esq_produccion_m_hr = is_numeric($row->esq_produccion_m_hr) ? $row->esq_produccion_m_hr : false;
                $esq_energia_consumo_m4 = is_numeric($row->esq_energia_consumo_m4) ? $row->esq_energia_consumo_m4 : false;
                $esq_energia_consumo_m5 = is_numeric($row->esq_energia_consumo_m5) ? $row->esq_energia_consumo_m5 : false;
                $esq_energia_iluminacion_m4 = is_numeric($row->esq_energia_iluminacion_m4) ? $row->esq_energia_iluminacion_m4 : false;
                $esq_energia_iluminacion_m5 = is_numeric($row->esq_energia_iluminacion_m5) ? $row->esq_energia_iluminacion_m5 : false;
                $esq_energia_eficiencia_m4 = is_numeric($row->esq_energia_eficiencia_m4) ? $row->esq_energia_eficiencia_m4 : false;
                $esq_energia_eficiencia_m5 = is_numeric($row->esq_energia_eficiencia_m5) ? $row->esq_energia_eficiencia_m5 : false;
                $esq_energia_asignacion_m4 = is_numeric($row->esq_energia_asignacion_m4) ? $row->esq_energia_asignacion_m4 : false;
                $esq_energia_asignacion_m5 = is_numeric($row->esq_energia_asignacion_m5) ? $row->esq_energia_asignacion_m5 : false;
                $esq_energia_precio_kw_hr = is_numeric($row->esq_energia_precio_kw_hr) ? $row->esq_energia_precio_kw_hr : false;
                $esq_energia_horas_por_turno = is_numeric($row->esq_energia_horas_por_turno) ? $row->esq_energia_horas_por_turno : false;
                $esq_precio_maquila_clp_caja = is_numeric($row->esq_precio_maquila_clp_caja) ? $row->esq_precio_maquila_clp_caja : false;
                $consumo_adhesivo_emplacado_simple_gr_m2 = is_numeric($row->consumo_adhesivo_emplacado_simple_gr_m2) ? $row->consumo_adhesivo_emplacado_simple_gr_m2 : false;
                $consumo_adhesivo_emplacado_doble_gr_m2 = is_numeric($row->consumo_adhesivo_emplacado_doble_gr_m2) ? $row->consumo_adhesivo_emplacado_doble_gr_m2 : false;
                $consumo_energia_emplacadora_kw_hr = is_numeric($row->consumo_energia_emplacadora_kw_hr) ? $row->consumo_energia_emplacadora_kw_hr : false;
                $costo_energia_emplacadora_usd_kw_hr = is_numeric($row->costo_energia_emplacadora_usd_kw_hr) ? $row->costo_energia_emplacadora_usd_kw_hr : false;
                $productividad_media_emplacado_placas_hr = is_numeric($row->productividad_media_emplacado_placas_hr) ? $row->productividad_media_emplacado_placas_hr : false;
                $merma_emplacadora = is_numeric($row->merma_emplacadora) ? $row->merma_emplacadora : false;
                $iva = is_numeric($row->iva) ? $row->iva : false;
                $tasa_mensual_credito = is_numeric($row->tasa_mensual_credito) ? $row->tasa_mensual_credito : false;
                $dias_financiamiento_credito = is_numeric($row->dias_financiamiento_credito) ? $row->dias_financiamiento_credito : false;
                $perdida_productividad_porcentaje_mayor_tiempo_dc = is_numeric($row->perdida_productividad_porcentaje_mayor_tiempo_dc) ? $row->perdida_productividad_porcentaje_mayor_tiempo_dc : false;
                $perdida_productividad_mg_dc = is_numeric($row->perdida_productividad_mg_dc) ? $row->perdida_productividad_mg_dc : false;
                $perdida_productividad_mg_dc_pe = is_numeric($row->perdida_productividad_mg_dc_pe) ? $row->perdida_productividad_mg_dc_pe : false;
                $sobre_costo_consolidacion_buin = is_numeric($row->sobre_costo_consolidacion_buin) ? $row->sobre_costo_consolidacion_buin : false;
                $tasa_interes_mensual_financiamiento = is_numeric($row->tasa_interes_mensual_financiamiento) ? $row->tasa_interes_mensual_financiamiento : false;
                $perdida_productividad_pegado_3_y_4_puntos = is_numeric($row->perdida_productividad_pegado_3_y_4_puntos) ? $row->perdida_productividad_pegado_3_y_4_puntos : false;
                $perdida_productividad_ag = is_numeric($row->perdida_productividad_ag) ? $row->perdida_productividad_ag : false;
                $costo_fijo_administrativo = is_numeric($row->costo_fijo_administrativo) ? $row->costo_fijo_administrativo : false;
                $ensamblado_usd_unid = is_numeric($row->ensamblado_usd_unid) ? $row->ensamblado_usd_unid : false;
                $desgajado_usd_unid = is_numeric($row->desgajado_usd_unid) ? $row->desgajado_usd_unid : false;
                $maquina_bins_usd = is_numeric($row->maquina_bins_usd) ? $row->maquina_bins_usd : false;

                if (!$precio_dolar && $precio_dolar !== 0) $motivos[] = " precio_dolar";
                if (!$esq_perdida_papel && $esq_perdida_papel !== 0) $motivos[] = " esq_perdida_papel";
                if (!$esq_perdida_adhesivo && $esq_perdida_adhesivo !== 0) $motivos[] = " esq_perdida_adhesivo";
                if (!$esq_recorte_esquineros && $esq_recorte_esquineros !== 0) $motivos[] = " esq_recorte_esquineros";
                if (!$esq_consumo_adhesivo && $esq_consumo_adhesivo !== 0) $motivos[] = " esq_consumo_adhesivo";
                if (!$esq_costo_impresion_offset && $esq_costo_impresion_offset !== 0) $motivos[] = " esq_costo_impresion_offset";
                if (!$esq_esquineros_por_pallet && $esq_esquineros_por_pallet !== 0) $motivos[] = " esq_esquineros_por_pallet";
                if (!$esq_merma_costo_impresion && $esq_merma_costo_impresion !== 0) $motivos[] = " esq_merma_costo_impresion";
                if (!$esq_precio_adhesivo && $esq_precio_adhesivo !== 0) $motivos[] = " esq_precio_adhesivo";
                if (!$esq_precio_tinta_usd_kg && $esq_precio_tinta_usd_kg !== 0) $motivos[] = " esq_precio_tinta_usd_kg";
                if (!$esq_consumo_tinta_g_m2 && $esq_consumo_tinta_g_m2 !== 0) $motivos[] = " esq_consumo_tinta_g_m2";
                if (!$esq_precio_clisses_clp_cm2 && $esq_precio_clisses_clp_cm2 !== 0) $motivos[] = " esq_precio_clisses_clp_cm2";
                if (!$esq_produccion_m_hr && $esq_produccion_m_hr !== 0) $motivos[] = " esq_produccion_m_hr";
                if (!$esq_energia_consumo_m4 && $esq_energia_consumo_m4 !== 0) $motivos[] = " esq_energia_consumo_m4";
                if (!$esq_energia_consumo_m5 && $esq_energia_consumo_m5 !== 0) $motivos[] = " esq_energia_consumo_m5";
                if (!$esq_energia_iluminacion_m4 && $esq_energia_iluminacion_m4 !== 0) $motivos[] = " esq_energia_iluminacion_m4";
                if (!$esq_energia_iluminacion_m5 && $esq_energia_iluminacion_m5 !== 0) $motivos[] = " esq_energia_iluminacion_m5";
                if (!$esq_energia_eficiencia_m4 && $esq_energia_eficiencia_m4 !== 0) $motivos[] = " esq_energia_eficiencia_m4";
                if (!$esq_energia_eficiencia_m5 && $esq_energia_eficiencia_m5 !== 0) $motivos[] = " esq_energia_eficiencia_m5";
                if (!$esq_energia_asignacion_m4 && $esq_energia_asignacion_m4 !== 0) $motivos[] = " esq_energia_asignacion_m4";
                if (!$esq_energia_asignacion_m5 && $esq_energia_asignacion_m5 !== 0) $motivos[] = " esq_energia_asignacion_m5";
                if (!$esq_energia_precio_kw_hr && $esq_energia_precio_kw_hr !== 0) $motivos[] = " esq_energia_precio_kw_hr";
                if (!$esq_energia_horas_por_turno && $esq_energia_horas_por_turno !== 0) $motivos[] = " esq_energia_horas_por_turno";
                if (!$esq_precio_maquila_clp_caja && $esq_precio_maquila_clp_caja !== 0) $motivos[] = " esq_precio_maquila_clp_caja";
                if (!$consumo_adhesivo_emplacado_simple_gr_m2 && $consumo_adhesivo_emplacado_simple_gr_m2 !== 0) $motivos[] = " consumo_adhesivo_emplacado_simple_gr_m2";
                if (!$consumo_adhesivo_emplacado_doble_gr_m2 && $consumo_adhesivo_emplacado_doble_gr_m2 !== 0) $motivos[] = " consumo_adhesivo_emplacado_doble_gr_m2";
                if (!$consumo_energia_emplacadora_kw_hr && $consumo_energia_emplacadora_kw_hr !== 0) $motivos[] = " consumo_energia_emplacadora_kw_hr";
                if (!$costo_energia_emplacadora_usd_kw_hr && $costo_energia_emplacadora_usd_kw_hr !== 0) $motivos[] = " costo_energia_emplacadora_usd_kw_hr";
                if (!$productividad_media_emplacado_placas_hr && $productividad_media_emplacado_placas_hr !== 0) $motivos[] = " productividad_media_emplacado_placas_hr";
                if (!$merma_emplacadora && $merma_emplacadora !== 0) $motivos[] = " merma_emplacadora";
                if (!$iva && $iva !== 0) $motivos[] = " iva";
                if (!$tasa_mensual_credito && $tasa_mensual_credito !== 0) $motivos[] = " tasa_mensual_credito";
                if (!$dias_financiamiento_credito && $dias_financiamiento_credito !== 0) $motivos[] = " dias_financiamiento_credito";
                if (!$perdida_productividad_porcentaje_mayor_tiempo_dc && $perdida_productividad_porcentaje_mayor_tiempo_dc !== 0) $motivos[] = " perdida_productividad_porcentaje_mayor_tiempo_dc";
                if (!$perdida_productividad_mg_dc && $perdida_productividad_mg_dc !== 0) $motivos[] = " perdida_productividad_mg_dc";
                if (!$perdida_productividad_mg_dc_pe && $perdida_productividad_mg_dc_pe !== 0) $motivos[] = " perdida_productividad_mg_dc_pe";
                if (!$sobre_costo_consolidacion_buin && $sobre_costo_consolidacion_buin !== 0) $motivos[] = " sobre_costo_consolidacion_buin";
                if (!$tasa_interes_mensual_financiamiento && $tasa_interes_mensual_financiamiento !== 0) $motivos[] = " tasa_interes_mensual_financiamiento";
                if (!$perdida_productividad_pegado_3_y_4_puntos && $perdida_productividad_pegado_3_y_4_puntos !== 0) $motivos[] = " perdida_productividad_pegado_3_y_4_puntos";
                if (!$perdida_productividad_ag && $perdida_productividad_ag !== 0) $motivos[] = " perdida_productividad_ag";
                if (!$costo_fijo_administrativo && $costo_fijo_administrativo !== 0) $motivos[] = " costo_fijo_administrativo";
                if (!$ensamblado_usd_unid && $ensamblado_usd_unid !== 0) $motivos[] = " ensamblado_usd_unid";
                if (!$desgajado_usd_unid && $desgajado_usd_unid !== 0) $motivos[] = " desgajado_usd_unid";
                if (!$maquina_bins_usd && $maquina_bins_usd !== 0) $motivos[] = " maquina_bins_usd";

                if (count($motivos) >= 1) {
                    $variableErroneo = new stdClass();
                    $variableErroneo->linea = $key + 2;
                    $variableErroneo->motivos = $motivos;
                    $variablesInvalidos[] = $variableErroneo;
                    continue;
                }

                $variable = VariablesCotizador::where('id', 1)->first();
                // dd($user);
                if ($variable) {

                    $variable->precio_dolar = $precio_dolar;
                    $variable->esq_perdida_papel = $esq_perdida_papel;
                    $variable->esq_perdida_adhesivo = $esq_perdida_adhesivo;
                    $variable->esq_recorte_esquineros = $esq_recorte_esquineros;
                    $variable->esq_consumo_adhesivo = $esq_consumo_adhesivo;
                    $variable->esq_costo_impresion_offset = $esq_costo_impresion_offset;
                    $variable->esq_esquineros_por_pallet = $esq_esquineros_por_pallet;
                    $variable->esq_merma_costo_impresion = $esq_merma_costo_impresion;
                    $variable->esq_precio_adhesivo = $esq_precio_adhesivo;
                    $variable->esq_precio_tinta_usd_kg = $esq_precio_tinta_usd_kg;
                    $variable->esq_consumo_tinta_g_m2 = $esq_consumo_tinta_g_m2;
                    $variable->esq_precio_clisses_clp_cm2 = $esq_precio_clisses_clp_cm2;
                    $variable->esq_produccion_m_hr = $esq_produccion_m_hr;
                    $variable->esq_energia_consumo_m4 = $esq_energia_consumo_m4;
                    $variable->esq_energia_consumo_m5 = $esq_energia_consumo_m5;
                    $variable->esq_energia_iluminacion_m4 = $esq_energia_iluminacion_m4;
                    $variable->esq_energia_iluminacion_m5 = $esq_energia_iluminacion_m5;
                    $variable->esq_energia_eficiencia_m4 = $esq_energia_eficiencia_m4;
                    $variable->esq_energia_eficiencia_m5 = $esq_energia_eficiencia_m5;
                    $variable->esq_energia_asignacion_m4 = $esq_energia_asignacion_m4;
                    $variable->esq_energia_asignacion_m5 = $esq_energia_asignacion_m5;
                    $variable->esq_energia_precio_kw_hr = $esq_energia_precio_kw_hr;
                    $variable->esq_energia_horas_por_turno = $esq_energia_horas_por_turno;
                    $variable->esq_precio_maquila_clp_caja = $esq_precio_maquila_clp_caja;
                    $variable->consumo_adhesivo_emplacado_simple_gr_m2 = $consumo_adhesivo_emplacado_simple_gr_m2;
                    $variable->consumo_adhesivo_emplacado_doble_gr_m2 = $consumo_adhesivo_emplacado_doble_gr_m2;
                    $variable->consumo_energia_emplacadora_kw_hr = $consumo_energia_emplacadora_kw_hr;
                    $variable->costo_energia_emplacadora_usd_kw_hr = $costo_energia_emplacadora_usd_kw_hr;
                    $variable->productividad_media_emplacado_placas_hr = $productividad_media_emplacado_placas_hr;
                    $variable->merma_emplacadora = $merma_emplacadora;
                    $variable->iva = $iva;
                    $variable->tasa_mensual_credito = $tasa_mensual_credito;
                    $variable->dias_financiamiento_credito = $dias_financiamiento_credito;
                    $variable->perdida_productividad_porcentaje_mayor_tiempo_dc = $perdida_productividad_porcentaje_mayor_tiempo_dc;
                    $variable->perdida_productividad_mg_dc = $perdida_productividad_mg_dc;
                    $variable->perdida_productividad_mg_dc_pe = $perdida_productividad_mg_dc_pe;
                    $variable->sobre_costo_consolidacion_buin = $sobre_costo_consolidacion_buin;
                    $variable->tasa_interes_mensual_financiamiento = $tasa_interes_mensual_financiamiento;
                    $variable->perdida_productividad_pegado_3_y_4_puntos = $perdida_productividad_pegado_3_y_4_puntos;
                    $variable->perdida_productividad_ag = $perdida_productividad_ag;
                    $variable->costo_fijo_administrativo = $costo_fijo_administrativo;
                    $variable->ensamblado_usd_unid = $ensamblado_usd_unid;
                    $variable->desgajado_usd_unid = $desgajado_usd_unid;
                    $variable->maquina_bins_usd = $maquina_bins_usd;

                    if ($variable->isDirty()) {
                        $variablesActualizados[] = $variable;
                        // dd($variable->getDirty(), $variable);

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($variable, $row, "UPDATE", $codigo_operacion);
                            $variable->save();
                            continue;
                        }
                    }
                }
            }
        }

        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $variables_actualizados = [];
        $variables_error = [];

        if (isset($variablesActualizados)) {
            $updated = 'Los siguientes variables fueron actualizados:';
            $variables_actualizados = $variablesActualizados;
        }
        if (isset($variablesInvalidos)) {
            $error = 'Los siguientes variables tienen 1 o mas errores';
            $variables_error = $variablesInvalidos;
        }

        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'variables_actualizados' => $variables_actualizados,
            'variables_error' => $variables_error

        ], 200);
    }

    public function descargar_excel_variables(Request $request)
    {
        $titulo = "Listado Variables";
        $variables = VariablesCotizador::all();
        // dd($variables);
        $variables_array[] = array(
            'ID',
            'precio_dolar',
            'esq_perdida_papel',
            'esq_perdida_adhesivo',
            'esq_recorte_esquineros',
            'esq_consumo_adhesivo',
            'esq_costo_impresion_offset',
            'esq_esquineros_por_pallet',
            'esq_merma_costo_impresion',
            'esq_precio_adhesivo',
            'esq_precio_tinta_usd_kg',
            'esq_consumo_tinta_g_m2',
            'esq_precio_clisses_clp_cm2',
            'esq_produccion_m_hr',
            'esq_energia_consumo_m4',
            'esq_energia_consumo_m5',
            'esq_energia_iluminacion_m4',
            'esq_energia_iluminacion_m5',
            'esq_energia_eficiencia_m4',
            'esq_energia_eficiencia_m5',
            'esq_energia_asignacion_m4',
            'esq_energia_asignacion_m5',
            'esq_energia_precio_kw_hr',
            'esq_energia_horas_por_turno',
            'esq_precio_maquila_clp_caja',
            'consumo_adhesivo_emplacado_simple_gr_m2',
            'consumo_adhesivo_emplacado_doble_gr_m2',
            'consumo_energia_emplacadora_kw_hr',
            'costo_energia_emplacadora_usd_kw_hr',
            'productividad_media_emplacado_placas_hr',
            'merma_emplacadora',
            'iva',
            'tasa_mensual_credito',
            'dias_financiamiento_credito',
            'perdida_productividad_porcentaje_mayor_tiempo_dc',
            'perdida_productividad_mg_dc',
            'perdida_productividad_mg_dc_pe',
            'sobre_costo_consolidacion_buin',
            'tasa_interes_mensual_financiamiento',
            'perdida_productividad_pegado_3_y_4_puntos',
            'perdida_productividad_ag',
            'costo_fijo_administrativo',
            'ensamblado_usd_unid',
            'desgajado_usd_unid',
            'maquina_bins_usd'
        );

        foreach ($variables as $variable) {
            $variables_array[] = array(
                $variable->id,
                $variable->precio_dolar,
                $variable->esq_perdida_papel,
                $variable->esq_perdida_adhesivo,
                $variable->esq_recorte_esquineros,
                $variable->esq_consumo_adhesivo,
                $variable->esq_costo_impresion_offset,
                $variable->esq_esquineros_por_pallet,
                $variable->esq_merma_costo_impresion,
                $variable->esq_precio_adhesivo,
                $variable->esq_precio_tinta_usd_kg,
                $variable->esq_consumo_tinta_g_m2,
                $variable->esq_precio_clisses_clp_cm2,
                $variable->esq_produccion_m_hr,
                $variable->esq_energia_consumo_m4,
                $variable->esq_energia_consumo_m5,
                $variable->esq_energia_iluminacion_m4,
                $variable->esq_energia_iluminacion_m5,
                $variable->esq_energia_eficiencia_m4,
                $variable->esq_energia_eficiencia_m5,
                $variable->esq_energia_asignacion_m4,
                $variable->esq_energia_asignacion_m5,
                $variable->esq_energia_precio_kw_hr,
                $variable->esq_energia_horas_por_turno,
                $variable->esq_precio_maquila_clp_caja,
                $variable->consumo_adhesivo_emplacado_simple_gr_m2,
                $variable->consumo_adhesivo_emplacado_doble_gr_m2,
                $variable->consumo_energia_emplacadora_kw_hr,
                $variable->costo_energia_emplacadora_usd_kw_hr,
                $variable->productividad_media_emplacado_placas_hr,
                $variable->merma_emplacadora,
                $variable->iva,
                $variable->tasa_mensual_credito,
                $variable->dias_financiamiento_credito,
                $variable->perdida_productividad_porcentaje_mayor_tiempo_dc,
                $variable->perdida_productividad_mg_dc,
                $variable->perdida_productividad_mg_dc_pe,
                $variable->sobre_costo_consolidacion_buin,
                $variable->tasa_interes_mensual_financiamiento,
                $variable->perdida_productividad_pegado_3_y_4_puntos,
                $variable->perdida_productividad_ag,
                $variable->costo_fijo_administrativo,
                $variable->ensamblado_usd_unid,
                $variable->desgajado_usd_unid,
                $variable->maquina_bins_usd
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($variables_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Variables', function ($sheet) use ($variables_array) {
                $sheet->fromArray($variables_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    // excel descargable de muestras

    public function excel_muestras_pendientes(Request $request)
    {
        $titulo = "Listado Muestras Pendientes";
        $muestras = Muestra::where("work_order_id", "!=", "0")->where("estado", "1")->get();
        // dd($muestras);
        $muestras_array[] = array(
            'ID OT',
            'ID Muestra',
            'CAD',
            'Carton',
            'Carton Muestra',
            'Tipo de Pegado',
            'Destinatario',
            'Cantidad',
            'Prioritaria',
            'Estado',

        );

        foreach ($muestras as $muestra) {
            if ($muestra->destinatarios_id[0] == 1) {
                $cantidad = $muestra->cantidad_vendedor;
            } elseif ($muestra->destinatarios_id[0] == 2) {
                $cantidad = $muestra->cantidad_diseñador;
            } elseif ($muestra->destinatarios_id[0] == 3) {
                $cantidad =  $muestra->cantidad_laboratorio;
            } elseif ($muestra->destinatarios_id[0] == 4) {
                $cantidad =  $muestra->cantidad_1;
            } elseif ($muestra->destinatarios_id[0] == 5) {
                $cantidad = $muestra->cantidad_diseñador_revision;
            }
            $muestras_array[] = array(
                $muestra->work_order_id,
                $muestra->id,
                (isset($muestra->cad_id)) ? $muestra->cad_asignado->cad : $muestra->cad,
                isset($muestra->carton) && isset($muestra->carton->codigo) ? $muestra->carton->codigo : "",
                isset($muestra->carton_muestra) && isset($muestra->carton_muestra->codigo) ? $muestra->carton_muestra->codigo : "",
                isset($muestra->pegado_id) ? [1 => "Sin Pegar", 2 => "Pegado Flexo Interior", 3 => "Pegado Flexo Exterior", 4 => "Pegado Diecutter", 5 => "Pegado Cajas Fruta", 6 => "Pegado con Cinta", 7 => "Sin Pegar con Cinta"][$muestra->pegado_id] : null,
                [1 => "Retira Ventas", 2 => "Envio a Diseñador", 3 => "Laboratorio", 4 => "Envío a Clientes"][$muestra->destinatarios_id[0]],
                $cantidad,
                ($muestra->prioritaria == 1) ? "SI" : "NO",
                "En Proceso",


            );
        }

        Excel::create($titulo, function ($excel) use ($muestras_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Muestras', function ($sheet) use ($muestras_array) {
                $sheet->fromArray($muestras_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    //////////////MARGENES MINIMOS//////////////////
    ////////////////////////////////////////
    //////////////MARGENES MINIMOS/////////////////
    ////////////////////////////////////////
    //////////////MARGENES MINIMOS//////////////////
    public function cargaMargenesMinimosForm()
    {
        $margenes = MargenMinimo::all();
        return view('mantenedores.margenes-minimos', compact("margenes"));
    }

    public function descargar_excel_margenes(Request $request)
    {
        $titulo = "Listado Margenes Minimos";
        $margenes = MargenMinimo::all();
        // dd($variables);
        $variables_array[] = array(
            'ID',
            'mercado',
            'rubro',
            'cluster',
            'mc_minimo'
        );

        foreach ($margenes as $margen) {
            $variables_array[] = array(
                $margen->id,
                $margen->mercado_descripcion,
                $margen->rubro_descripcion,
                $margen->cluster,
                $margen->minimo
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($variables_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Margenes', function ($sheet) use ($variables_array) {
                $sheet->fromArray($variables_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    public function importMargenesMinimos(Request $request)
    {
        // dd(request()->all());
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();


        $rubros = Rubro::pluck('id', 'descripcion')->toArray();
        $mercados = Hierarchy::pluck('id', 'descripcion')->toArray();

        $clusters = [];
        $clusters_aux = DB::table('clients')->distinct()->get(['tipo_cliente']);
        for ($i = 0; $i < $clusters_aux->count(); $i++) {
            $clusters[$i] = $clusters_aux[$i]->tipo_cliente;
        }
        //dd($mercados);

        $proceso = request("proceso");
        $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
        if ($data->count()) {
            foreach ($data as $key => $row) {

                //detect not empty row START
                $is_row_empty = true;
                foreach ($row as $cell) {
                    if ($cell !== '' &&  $cell !== NULL) {
                        $is_row_empty = false; //detect not empty row
                        break;
                    }
                }
                if ($is_row_empty) continue; // skip empty row
                //detect not empty row END
                // Algoritmo especifico de papeles}

                $motivos = [];
                // Validaciones generales
                // dd(is_numeric($row->tolerancia_gramaje_real), $row->tolerancia_gramaje_real, in_array($row->onda, $ondas_validas));
                /*$codigo = (trim($row->codigo) != "") ? $row->codigo : false;
                $gramaje = is_numeric($row->gramaje) ? $row->gramaje : false;
                $precio = is_numeric($row->precio) ? $row->precio : false;*/
                //dd(strtoupper($row->mercado));
                $rubro = array_key_exists(strtoupper($row->rubro), $rubros) ? $rubros[strtoupper($row->rubro)] : false;
                $mercado = array_key_exists(strtoupper($row->mercado), $mercados) ? $mercados[strtoupper($row->mercado)] : false;
                $cluster = in_array(strtoupper($row->cluster), $clusters) ? strtoupper($row->cluster) : false;
                $minimo = is_numeric($row->mc_minimo) ? (int) $row->mc_minimo : false;

                //dd($mercado);
                //dd($minimo);

                if (!$rubro) $motivos[] = "Rubro";
                if (!$mercado) $motivos[] = "Mercado";
                if (!$cluster) $motivos[] = "Cluster";
                if (!$minimo) $motivos[] = "Mc Minimo";

                if (count($motivos) >= 1) {
                    $margenErroneo = new stdClass();
                    $margenErroneo->linea = $key + 2;
                    $margenErroneo->motivos = $motivos;
                    $margenesInvalidos[] = $margenErroneo;
                    continue;
                }

                $margen = MargenMinimo::where('id', $row->id)->first();
                // dd($user);
                if ($margen) {
                    // Validar si la diferencia del cambio de precio es significativa
                    // si es mayor al 10% del precio original indicamos que no se hace el cambio
                    // if (abs($papel->precio - $row->precio) > $papel->precio * 0.1) {

                    //     $papelErroneo = new stdClass();
                    //     $papelErroneo->linea = $key + 2;
                    //     $papelErroneo->motivos = "Diferencia de precios de papel excede cambio permitido";
                    //     $papelesInvalidos[] = $papelErroneo;
                    //     continue;
                    // }
                    $margen->id                  = trim($row->id);
                    $margen->rubro_id            = $rubro;
                    $margen->rubro_descripcion   = trim($row->rubro);
                    $margen->mercado_id          = $mercado;
                    $margen->mercado_descripcion = trim($row->mercado);
                    $margen->cluster             = trim($row->cluster);
                    $margen->minimo              = trim($row->mc_minimo);
                    // dd($papel, $papel->isDirty(), $papel->getChanges(), $papel->isDirty("desperdicio"));
                    if ($margen->isDirty()) {
                        //$papel->orden = $key + 2;
                        // para marcar un papel como inactivado debe ser originalmente activo
                        /*if (($papel->getOriginal("active") == 1 && $papel->active == 0)) {
                            $papelesInactivados[] = $papel;
                        } else {*/
                        $margenesActualizados[] = $margen;
                        // }
                        // dd($papel->getDirty(), $papel);

                        // Solo si el proceso ya es de carga guardamos y continuamos
                        if ($proceso == "cargaCompleta") {
                            changelog($margen, $row, "UPDATE", $codigo_operacion);
                            $margen->save();
                            continue;
                        }
                    } else {
                        // Si no hay cambios lo unico q actualizamos es el orden segun archivo
                        //$papel->update(["orden" => $key + 2]);
                        continue;
                    }
                } else {
                    $margen                      = new MargenMinimo();
                    $margen->rubro_id            = $rubro;
                    $margen->rubro_descripcion   = trim($row->rubro);
                    $margen->mercado_id          = $mercado;
                    $margen->mercado_descripcion = trim($row->mercado);
                    $margen->cluster             = trim($row->cluster);
                    $margen->minimo              = trim($row->mc_minimo);

                    //$margen->orden = $key + 2;
                    // Solo si el proceso ya es de carga guardamos y continuamos
                    if ($proceso == "cargaCompleta") {
                        $changelog = changelog($margen, $row, "INSERT", $codigo_operacion);
                        $margen->save();
                        $changelog->update(['item_id' => $margen->id]);
                        continue;
                    }

                    $margen->linea = $key + 2;
                    $margenes[] = $margen;
                }
            }
        }


        // Solo si el proceso es de carga Completa retornamos la vista actualizada
        if ($proceso == "cargaCompleta") {
            // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
            return response()->json([
                'url' => "redirect",
            ], 200);
        }
        // dd("fin");

        $exito = null;
        $updated = null;
        $error = null;
        $margenes_ingresados = [];
        $margenes_actualizados = [];
        $margenes_error = [];

        if (isset($margenes)) {
            $exito = 'Se ingresaron los siguientes papeles';
            $margenes_ingresados = $margenes;
            // $plantas_ingresadas = Planta::whereIn('id', $ids)->with('status')->paginate(10);
        }
        if (isset($margenesActualizados)) {
            $updated = 'Los siguientes margenes fueron actualizados:';
            $margenes_actualizados = $margenesActualizados;
        }
        /*if (isset($margenesInactivados)) {
            $updated = 'Los siguientes papeles fueron actualizados:';
            $papeles_inactivados = $papelesInactivados;
        }*/
        if (isset($margenesInvalidos)) {
            $error = 'Los siguientes margenes tienen 1 o mas errores';
            $margenes_error = $margenesInvalidos;
        }

        return response()->json([
            'mensaje' => "Archivo cargado Exitosamente",
            'margenes' => $margenes_ingresados,
            'margenes_actualizados' => $margenes_actualizados,
            //'margenes_inactivados' => $margenes_inactivados,
            'margenes_error' => $margenes_error

        ], 200);
    }

    ////PORCENTAJES MARGENES MINIMOS - INICIO

    public function cargaPorcentajesMargenesMinimosForm()
    {
        $porcentajes_margenes = PorcentajeMargen::all();
        return view('mantenedores.porcentajes-margenes-minimos', compact("porcentajes_margenes"));
    }

    public function descargar_excel_porcentajes_margenes(Request $request)
    {
        $titulo = "Listado Porcentajes Margenes Minimos";
        $margenes = PorcentajeMargen::all();
        // dd($variables);
        $variables_array[] = array(
            'ID',
            'rubro',
            'clasificacion_cliente',
            'porcentaje_bruto_esperado',
            'porcentaje_servir_esperado',
            'porcentaje_ebitda_esperado'
        );

        foreach ($margenes as $margen) {
            $variables_array[] = array(
                $margen->id,
                $margen->rubro->descripcion,
                $margen->clasificacion->name,
                $margen->bruto_esperado,
                $margen->servir_esperado,
                $margen->ebitda_esperado
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($variables_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('Porcentajes Margenes', function ($sheet) use ($variables_array) {
                $sheet->fromArray($variables_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    public function importPorcentajesMargenesMinimos(Request $request)
        {
            // dd(request()->all());
            $validator = Validator::make(
                [
                    'archivo'      => $request->archivo,
                    'extension' => strtolower($request->archivo->getClientOriginalExtension()),
                ],
                [
                    'archivo'          => 'required',
                    'extension'      => 'required|in:xlsx,xls,csv',
                ]

            );

            $path = $request->file('archivo')->getRealPath();
            $data = Excel::load($path, false, 'ISO-8859-1')->get();


            $rubros = Rubro::pluck('id', 'descripcion')->toArray();
            $clasificaciones = ClasificacionCliente::pluck('id', 'name')->toArray();

            $proceso = request("proceso");
            $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
            if ($data->count()) {
                foreach ($data as $key => $row) {

                    //detect not empty row START
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
                    $rubro = array_key_exists(strtoupper($row->rubro), $rubros) ? $rubros[strtoupper($row->rubro)] : false;
                    $clasificacion = array_key_exists(strtoupper($row->clasificacion_cliente), $clasificaciones) ? $clasificaciones[strtoupper($row->clasificacion_cliente)] : false;
                    $bruto_esperado = is_numeric($row->porcentaje_bruto_esperado) ? $row->porcentaje_bruto_esperado : 'NO';
                    $servir_esperado = is_numeric($row->porcentaje_servir_esperado) ? $row->porcentaje_servir_esperado : 'NO';
                    $ebitda_esperado = is_numeric($row->porcentaje_ebitda_esperado) ? $row->porcentaje_ebitda_esperado : 'NO';

                    if (!$rubro) $motivos[] = "Rubro";
                    if (!$clasificacion) $motivos[] = "Clasificacion Cliente";
                    if ($bruto_esperado==='NO') $motivos[] = "Margen Bruto menos flete esperado";
                    if ($servir_esperado==='NO') $motivos[] = "Margen de Servir esperado";
                    if ($ebitda_esperado==='NO') $motivos[] = "Margen Ebitda esperado";

                    if (count($motivos) >= 1) {
                        $margenErroneo = new stdClass();
                        $margenErroneo->linea = $key + 2;
                        $margenErroneo->motivos = $motivos;
                        $porcentajesMargenesInvalidos[] = $margenErroneo;
                        continue;
                    }

                    $porcentaje_margen = PorcentajeMargen::where('id', $row->id)->first();
                    // dd($user);
                    if ($porcentaje_margen) {

                        $porcentaje_margen->id                  = trim($row->id);
                        $porcentaje_margen->rubro_id            = $rubro;
                        $porcentaje_margen->clasificacion_cliente_id = $clasificacion;
                        $porcentaje_margen->bruto_esperado  = trim($row->porcentaje_bruto_esperado);
                        $porcentaje_margen->servir_esperado = trim($row->porcentaje_servir_esperado);
                        $porcentaje_margen->ebitda_esperado = trim($row->porcentaje_ebitda_esperado);

                        if ($porcentaje_margen->isDirty()) {

                            $porcentajesMargenesActualizados[] = $porcentaje_margen;

                            if ($proceso == "cargaCompleta") {
                                changelog($porcentaje_margen, $row, "UPDATE", $codigo_operacion);
                                $porcentaje_margen->save();
                                continue;
                            }
                        } else {
                            // Si no hay cambios lo unico q actualizamos es el orden segun archivo

                            continue;
                        }
                    } else {

                        $porcentaje_margen                      = new PorcentajeMargen();
                        $porcentaje_margen->rubro_id            = $rubro;
                        $porcentaje_margen->clasificacion_cliente_id   = $clasificacion;
                        $porcentaje_margen->bruto_esperado  = trim($row->porcentaje_bruto_esperado);
                        $porcentaje_margen->servir_esperado = trim($row->porcentaje_servir_esperado);
                        $porcentaje_margen->ebitda_esperado = trim($row->porcentaje_ebitda_esperado);


                        if ($proceso == "cargaCompleta") {
                            $changelog = changelog($porcentaje_margen, $row, "INSERT", $codigo_operacion);
                            $porcentaje_margen->save();
                            $changelog->update(['item_id' => $porcentaje_margen->id]);
                            continue;
                        }

                        $porcentaje_margen->linea = $key + 2;
                        $porcentajesMargenes[] = $porcentaje_margen;
                    }
                }
            }

            // Solo si el proceso es de carga Completa retornamos la vista actualizada
            if ($proceso == "cargaCompleta") {
                // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
                return response()->json([
                    'url' => "redirect",
                ], 200);
            }
            // dd("fin");

            $exito = null;
            $updated = null;
            $error = null;
            $porcentajes_margenes_ingresados = [];
            $porcentajes_margenes_actualizados = [];
            $porcentajes_margenes_error = [];

            if (isset($porcentajesMargenes)) {
                $exito = 'Se ingresaron los siguientes porcentajes de margenes';
                $porcentajes_margenes_ingresados = $porcentajesMargenes;
                // $plantas_ingresadas = Planta::whereIn('id', $ids)->with('status')->paginate(10);
            }
            if (isset($porcentajesMargenesActualizados)) {
                $updated = 'Los siguientes porcentajes de margenes fueron actualizados:';
                $porcentajes_margenes_actualizados = $porcentajesMargenesActualizados;
            }

            if (isset($porcentajesMargenesInvalidos)) {
                $error = 'Los siguientes porcentajes de margenes tienen 1 o mas errores';
                $porcentajes_margenes_error = $porcentajesMargenesInvalidos;
            }

            return response()->json([
                'mensaje' => "Archivo cargado Exitosamente",
                'porcentajes_margenes_nuevos' => $porcentajes_margenes_ingresados,
                'porcentajes_margenes_actualizados' => $porcentajes_margenes_actualizados,
                'porcentajes_margenes_error' => $porcentajes_margenes_error

            ], 200);
        }
    ////PORCENTAJES MARGENES MINIMOS - FIN

    ////MANO OBRA MANTENCION - INICIO
        public function cargaManoObraMantencionForm()
        {
            $manoObraMantencion = ManoObraMantencion::all();
            return view('mantenedores.mano-obra-mantencion-masive', compact("manoObraMantencion"));
        }


        public function importManoObraMantencion(Request $request)
        {
            // dd(request()->all());
            $validator = Validator::make(
                [
                    'archivo'      => $request->archivo,
                    'extension' => strtolower($request->archivo->getClientOriginalExtension()),
                ],
                [
                    'archivo'          => 'required',
                    'extension'      => 'required|in:xlsx,xls,csv',
                ]

            );

            $path = $request->file('archivo')->getRealPath();
            $data = Excel::load($path, false, 'ISO-8859-1')->get();
            // dd($data);

            $procesos = Process::orderBy("descripcion")->pluck('id', 'descripcion')->toArray();
            $proceso_carga = request("proceso");
            $codigo_operacion = Carbon::now()->timestamp . Auth()->user()->id;
            if ($data->count()) {
                foreach ($data as $key => $row) {
                    // dd($row);
                    //detect not empty row START
                    $is_row_empty = true;
                    foreach ($row as $cell) {
                        if ($cell !== '' &&  $cell !== NULL) {
                            $is_row_empty = false; //detect not empty row
                            break;
                        }
                    }
                    if ($is_row_empty) continue; // skip empty row
                    //detect not empty row END
                    // Algoritmo especifico de papeles}
                    $motivos = [];

                  //  $consumo_kwh_mm2 = is_numeric($row->consumo_kwh_mm2) ? (int) $row->consumo_kwh_mm2 : false;
                    $proceso = array_key_exists($row->proceso, $procesos) ? $procesos[$row->proceso] : false;
                  //  if ($consumo_kwh_mm2 === false) $motivos[] = " Consumo Energia";
                  //  if (!$planta) $motivos[] = " Planta";
                    if (!$proceso) $motivos[] = " Proceso";

                    if (count($motivos) >= 1) {

                        // dd($motivos);
                        $manoObraMantencion = new stdClass();
                        $manoObraMantencion->linea = $key + 2;
                        $manoObraMantencion->motivos = $motivos;
                        $manoObraMantencionInvalidos[] = $manoObraMantencion;
                        continue;
                    }

                    $manoObraMantencion = ManoObraMantencion::where('id', $row->id)->first();
                    // dd($user);
                    if ($manoObraMantencion) {




                        // para marcar un manoObraMantencion como inactivado debe ser originalmente activo
                        if (($manoObraMantencion->active == 1 && $row->active == 0)) {

                            $manoObraMantencion->onda = trim($row->onda);
                            $manoObraMantencion->proceso_id = $proceso;
                            $manoObraMantencion->proceso = trim($row->proceso);
                            $manoObraMantencion->concatenacion = trim($row->concatenacion);
                            $manoObraMantencion->costo_buin = (is_null($row->costo_buin)||$row->costo_buin=="") ? 0 : trim($row->costo_buin);
                            $manoObraMantencion->costo_tiltil = (is_null($row->costo_tiltil)||$row->costo_tiltil=="") ? 0 : trim($row->costo_tiltil);
                            $manoObraMantencion->costo_osorno =(is_null($row->costo_osorno)||$row->costo_osorno=="") ? 0 : trim($row->costo_osorno);
                            $manoObraMantencion->active = $row->active;
                            $manoObraMantencion->save();

                            $manoObraMantencionInactivados[] = $manoObraMantencion;

                        } else {
                            if( $manoObraMantencion->onda != trim($row->onda)||
                                $manoObraMantencion->proceso_id != $proceso ||
                                $manoObraMantencion->concatenacion != trim($row->concatenacion) ||
                                $manoObraMantencion->costo_buin != trim($row->costo_buin) ||
                                $manoObraMantencion->costo_tiltil != trim($row->costo_tiltil) ||
                                $manoObraMantencion->costo_osorno != trim($row->costo_osorno) ||
                                $manoObraMantencion->active != $row->active
                            ){
                                $manoObraMantencion->onda = trim($row->onda);
                                $manoObraMantencion->proceso_id = $proceso;
                                $manoObraMantencion->proceso = trim($row->proceso);
                                $manoObraMantencion->concatenacion = trim($row->concatenacion);
                                $manoObraMantencion->costo_buin = (is_null($row->costo_buin)||$row->costo_buin=="") ? 0 : trim($row->costo_buin);
                                $manoObraMantencion->costo_tiltil = (is_null($row->costo_tiltil)||$row->costo_tiltil=="") ? 0 : trim($row->costo_tiltil);
                                $manoObraMantencion->costo_osorno =(is_null($row->costo_osorno)||$row->costo_osorno=="") ? 0 : trim($row->costo_osorno);
                                $manoObraMantencion->active = $row->active;
                                $manoObraMantencion->save();

                                $manoObraMantencionActualizados[] = $manoObraMantencion;
                            }
                        }
                    }else{
                        //Crear el nuevo registro
                        $manoObraMantencion = new ManoObraMantencion();
                        $manoObraMantencion->onda = trim($row->onda);
                        $manoObraMantencion->proceso_id = $proceso;
                        $manoObraMantencion->proceso = trim($row->proceso);
                        $manoObraMantencion->concatenacion = trim($row->concatenacion);
                        $manoObraMantencion->costo_buin = (is_null($row->costo_buin)||$row->costo_buin=="") ? 0 : trim($row->costo_buin);
                        $manoObraMantencion->costo_tiltil = (is_null($row->costo_tiltil)||$row->costo_tiltil=="") ? 0 : trim($row->costo_tiltil);;
                        $manoObraMantencion->costo_osorno =(is_null($row->costo_osorno)||$row->costo_osorno=="") ? 0 : trim($row->costo_osorno);;
                        $manoObraMantencion->save();
                        $manoObraMantencionIngresados[] = $manoObraMantencion;
                    }
                }
            }
            $exito = null;
            $updated = null;
            $error = null;
            $manoObraMantencion_ingresados = [];
            $manoObraMantencion_actualizados = [];
            $manoObraMantencion_inactivados = [];
            $manoObraMantencion_error = [];

            if (isset($manoObraMantencionActualizados)) {
                $updated = 'Los siguientes Mano Obra Mantencion fueron actualizados:';
                $manoObraMantencion_actualizados = $manoObraMantencionActualizados;
            }
            if (isset($manoObraMantencionInvalidos)) {
                $error = 'Los siguientes Mano Obra Mantencion tienen 1 o mas errores';
                $manoObraMantencion_error = $manoObraMantencionInvalidos;
            }
            if (isset($manoObraMantencionIngresados)) {
                $insertes = 'Los siguientes Mano Obra Mantencion fueron ingresados:';
                $manoObraMantencion_ingresados = $manoObraMantencionIngresados;
            }

            if (isset($manoObraMantencionInactivados)) {
                $insertes = 'Los siguientes Mano Obra Mantencion fueron inactivados:';
                $manoObraMantencion_inactivados = $manoObraMantencionInactivados;
            }
             // Solo si el proceso es de carga Completa retornamos la vista actualizada
            if ($proceso_carga == "cargaCompleta") {
                // return redirect()->route('mantenedores.cotizador.papels.masive')->with('success', 'Listado de Cartones Actualizado Exitosamente');
                return response()->json([
                    'url' => "redirect",
                ], 200);
            }

            return response()->json([
                'mensaje' => "Archivo cargado Exitosamente",
                'mano_obra_mantencion' => $manoObraMantencion_ingresados,
                'mano_obra_mantencion_actualizados' => $manoObraMantencion_actualizados,
                'mano_obra_mantencion_inactivados' => $manoObraMantencion_inactivados,
                'mano_obra_mantencion_error' => $manoObraMantencion_error

            ], 200);
        }

        public function descargar_excel_mano_obra_mantencion(Request $request)
        {
            $titulo = "Listado Mano Obra Mantención";
            $mano_obra_mantenciones = ManoObraMantencion::all();
            $mano_obra_mantencion_array[] = array(
                'ID',
                'onda',
                'proceso',
                'concatenacion',
                'costo_buin',
                'costo_tiltil',
                'costo_osorno',
                'active'
            );

            foreach ($mano_obra_mantenciones as $mano_obra_mantencion) {
                $mano_obra_mantencion_array[] = array(
                    $mano_obra_mantencion->id,
                    $mano_obra_mantencion->onda,
                    $mano_obra_mantencion->proceso,
                    $mano_obra_mantencion->concatenacion,
                    $mano_obra_mantencion->costo_buin,
                    $mano_obra_mantencion->costo_tiltil,
                    $mano_obra_mantencion->costo_osorno,
                    $mano_obra_mantencion->active
                );
            }

            Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($mano_obra_mantencion_array, $titulo) {
                $excel->setTitle($titulo);
                $excel->sheet('Mano Obra Mantencion', function ($sheet) use ($mano_obra_mantencion_array) {
                    $sheet->fromArray($mano_obra_mantencion_array, null, 'A1', true, false);
                });
            })->download('xlsx');
        }
    ////MANO OBRA MANTENCION - FIN
}
