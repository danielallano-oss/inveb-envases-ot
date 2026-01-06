<?php

namespace App\Http\Controllers;

use App\Carton;
use App\Material;
use App\Muestra;
use App\Paper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Excel;
use stdClass;

class CartonController extends Controller
{
    public function index()
    {
        //filtros:
        $cartons_filter = Carton::all();
        //filters:
        $query = Carton::query();
        if (!is_null(request()->query('id'))) {
            $query = $query->whereIn('id', request()->query('id'));
        }
        // if (!is_null(request()->query('role_id'))) {
        //     $query = $query->whereIn('role_id', request()->query('role_id'));
        // }
        if (!is_null(request()->query('active'))) {
            $query = $query->whereIn('active', request()->query('active'));
        }
        /*order columnns table*/
        $orderby = trim(request()->query('orderby'));
        $sorted  = trim(request()->query('sorted'));
        $orderby = in_array($orderby, ['codigo']) ? $orderby : 'codigo';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $cartons = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('cartons.index', compact('cartons', 'cartons_filter'));
    }
    public function create()
    {
        return view('cartons.create');
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'codigo' => 'required|unique:cartons,codigo',
            'descripcion' => 'required',
            'onda' => 'required',
            'peso' => 'required',
            // 'volumen' => 'required',
            'espesor' => 'required',
            'color' => 'required',
            'tipo' => 'required',
        ]);
        $carton = new Carton();
        $carton->codigo             = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $carton->codigo;
        $carton->descripcion             = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $carton->descripcion;
        $carton->onda             = (trim($request->input('onda')) != '') ? $request->input('onda') : $carton->onda;
        $carton->peso             = (trim($request->input('peso')) != '') ? str_replace('.', '', $request->input('peso'))  : $carton->peso;
        $carton->volumen             = (trim($request->input('volumen')) != '') ? str_replace('.', '', $request->input('volumen')) : null;
        $carton->espesor             = (trim($request->input('espesor')) != '') ? str_replace(',', '.', $request->input('espesor')) : $carton->espesor;
        $carton->color             = (trim($request->input('color')) != '') ? $request->input('color') : $carton->color;
        $carton->tipo             = (trim($request->input('tipo')) != '') ? $request->input('tipo') : $carton->tipo;
        $carton->save();
        return redirect()->route('mantenedores.cartons.list')->with('success', 'Cartón creado correctamente.');
    }
    public function edit($id)
    {
        $carton = Carton::find($id);
        $carton->espesor = str_replace('.', ',', $carton->espesor);
        return view('cartons.edit', compact('carton'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'codigo' => 'required|unique:cartons,codigo,' . $id,
            'descripcion' => 'required',
            'onda' => 'required',
            'peso' => 'required',
            // 'volumen' => 'required',
            'espesor' => 'required',
            'color' => 'required',
            'tipo' => 'required',
        ]);

        $carton = Carton::find($id);
        $carton->codigo             = (trim($request->input('codigo')));
        $carton->descripcion              = (trim($request->input('descripcion')));
        $carton->onda              = (trim($request->input('onda')));
        $carton->peso              = (trim($request->input('peso')) != '') ? str_replace('.', '', $request->input('peso'))  : $carton->peso;
        $carton->volumen             = (trim($request->input('volumen')) != '') ? str_replace('.', '', $request->input('volumen')) : null;
        $carton->espesor              = (trim($request->input('espesor')) != '') ? str_replace(',', '.', $request->input('espesor')) : $carton->espesor;
        $carton->color              = (trim($request->input('color')));
        $carton->tipo              = (trim($request->input('tipo')));
        $carton->save();
        return redirect()->route('mantenedores.cartons.list')->with('success', 'Cartón editado correctamente.');
    }

    public function active($id)
    {
        Carton::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.cartons.list')->with('success', 'Cartón activado correctamente.');
    }

    public function inactive($id)
    {
        Carton::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.cartons.list')->with('success', 'Cartón inactivado correctamente.');
    }


    public function cargaCartonsForm()
    {
        // dd("asd");
        //filters:
        $cartones = Carton::where("tipo", "!=", "ESQUINEROS")->orderByRaw('ISNULL(orden), orden ASC')->get();
        // ->paginate(20);
        return view('cartons.masive', compact("cartones"));
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
                $codigo_tapa_interior = array_key_exists((int) $row->codigo_tapa_interior, $papeles) ? (int) $row->codigo_tapa_interior : false;
                $codigo_onda_1 = array_key_exists((int) $row->codigo_onda_1, $papeles) ? (int) $row->codigo_onda_1 : false;
                $codigo_onda_1_2 = array_key_exists((int) $row->codigo_onda_1_2, $papeles) ? (int) $row->codigo_onda_1_2 : false;
                $codigo_tapa_media = array_key_exists((int) $row->codigo_tapa_media, $papeles) ? (int) $row->codigo_tapa_media : false;
                $codigo_onda_2 = array_key_exists((int) $row->codigo_onda_2, $papeles) ? (int) $row->codigo_onda_2 : false;
                $codigo_tapa_exterior = array_key_exists((int) $row->codigo_tapa_exterior, $papeles) ? (int) $row->codigo_tapa_exterior : false;
                // $cantidad = is_numeric($row->cantidad) ? $row->cantidad : false;
                // $numero_colores = is_numeric($row->colores) ? $row->colores : false;

                // $margen = is_numeric($row->margen) ? $row->margen : false;
                // $destino = array_key_exists($row->lugar_destino, $ciudades) ? $ciudades[$row->lugar_destino] : false;

                if (!$codigo_tapa_interior && $codigo_tapa_interior !== 0) $motivos[] = "Codigo Tapa Interior No existe";
                if (!$codigo_onda_1 && $codigo_onda_1 !== 0) $motivos[] = "Codigo Onda 1 No existe";
                if (!$codigo_onda_1_2 && $codigo_onda_1_2 !== 0) $motivos[] = "Codigo Onda 1.2 No existe";
                if (!$codigo_tapa_media && $codigo_tapa_media !== 0) $motivos[] = "Codigo Tapa Media No existe";
                if (!$codigo_onda_2 && $codigo_onda_2 !== 0) $motivos[] = "Codigo Onda 2 No existe";
                if (!$codigo_tapa_exterior && $codigo_tapa_exterior !== 0) $motivos[] = "Codigo Tapa Exterior No existe";
                // if (!$destino) $motivos[] = " Ciudad Destino";
                // if (!$cantidad) $motivos[] = " Cantidad";
                // if (!$numero_colores && $numero_colores != 0) $motivos[] = " Colores";
                // if (!$margen) $motivos[] = " Margen";

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
                        changelog($carton, $row, "UPDATE", $codigo_operacion);
                        if ($proceso == "cargaCompleta") {
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
                    $carton->active = trim($row->active);
                    $carton->orden = $key + 2;
                    // Solo si el proceso ya es de carga guardamos y continuamos
                    $changelog = changelog($carton, $row, "INSERT", $codigo_operacion);
                    if ($proceso == "cargaCompleta") {
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
        $cartones = Carton::where("tipo", "!=", "ESQUINEROS")->get();
        // dd($cartones);
        $cartones_array[] = array(
            'ID', 'codigo', 'onda', 'color_tapa_exterior', 'tipo', 'ect_min', 'espesor', 'peso', 'peso_total', 'tolerancia_gramaje_real', 'contenido_cordillera', 'contenido_reciclado', 'porocidad_gurley', 'cobb_int', 'cobb_ext', 'recubrimiento', 'codigo_tapa_interior',    'codigo_onda_1',    'codigo_onda_1_2',    'codigo_tapa_media',    'codigo_onda_2', 'codigo_tapa_exterior',    'desperdicio',    'excepcion',    'carton_muestra',    'active'

        );

        foreach ($cartones as $carton) {
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

    public function excel_muestras_pendientes(Request $request)
    {
        $titulo = "Listado Muestras Pendientes";
        $muestras = Muestra::where("work_order_id", "!=", "0")->where("estado", "1")->get();
        // dd($muestras);
        $muestras_array[] = array(
            'ID OT', 'ID Muestra', 'CAD', 'Carton', 'Carton Muestra', 'Tipo de Pegado', 'Destinatario', 'Cantidad', 'Prioritaria', 'Estado',

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
}
