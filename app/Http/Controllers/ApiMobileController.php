<?php

namespace App\Http\Controllers;

use App\Answer;
use App\User;
use App\WorkOrder;
use App\Management;
use App\States;
use App\WorkSpace;
use App\Client;
use App\Material;
use App\Hierarchy;
use App\Subhierarchy;
use App\Subsubhierarchy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use stdClass;

class ApiMobileController extends Controller
{
    function __construct()
    {
        // $this->middleware(['roles:7']);
    }
    // GET DATA CALLS
    /*
    public function getOrdenesOt(Request $request)
    {
        // vendedor que esta consultando
        $id_vendedor      = Auth::user()->id;
        $query = WorkOrder::with('productType', 'client', 'area', "ultimoCambioEstado.area");

        // Calculo total de tiempo en area de venta
        $query = $query->withCount([
            'gestiones AS tiempo_venta' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 1);
            }
        ]);
        // Calculo total de tiempo en area de desarrollo
        $query = $query->withCount([
            'gestiones AS tiempo_desarrollo' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 2);
            }
        ]);
        // Calculo total de tiempo en area de diseño
        $query = $query->withCount([
            'gestiones AS tiempo_diseño' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 3);
            }
        ]);
        // Calculo total de tiempo en area de catalogacion
        $query = $query->withCount([
            'gestiones AS tiempo_catalogacion' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 4);
            }
        ]);
        // Calculo total de tiempo en area de precatalogacion
        $query = $query->withCount([
            'gestiones AS tiempo_precatalogacion' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 5);
            }
        ]);
        $ots = $query->where('creador_id', $id_vendedor)->where('active', 1)->get();
        // return response()->json($ots[0]);

        $listadoOrdenes = $ots->map(function ($ot, $key) {
            $newOt = new stdClass();
            $newOt->id = $ot->id;
            $newOt->item = $ot->productType ? $ot->productType->descripcion : null;
            $newOt->cliente_id = $ot->client->id;
            $newOt->cliente = $ot->client->nombre;
            $newOt->descripcion = $ot->descripcion;
            $newOt->area = $ot->area ? $ot->area->nombre : null;
            $newOt->area_abreviatura = $ot->area ? $ot->area->abreviatura : null;
            $newOt->estado = isset($ot->ultimoCambioEstado) ? $ot->ultimoCambioEstado->state : "PV";
            $newOt->dias_area_actual = $ot->present()->diasPorArea($ot->area->id);
            $newOt->created_at = $ot->created_at->format("d/m/Y");
            return $newOt;
        });
        $areas = WorkSpace::where('status', '=', 'active')->get();
        $areas = $areas->map(function ($area, $key) {
            $newArea = new stdClass();
            $newArea->id = $area->id;
            $newArea->nombre = $area->nombre;
            $newArea->abreviatura = $area->abreviatura;
            return $newArea;
        });
        //-Estados:
        $estados = States::where('status', '=', 'active')->get();
        $estados = $estados->map(function ($estado, $key) {
            $newestado = new stdClass();
            $newestado->id = $estado->id;
            $newestado->nombre = $estado->nombre;
            $newestado->abreviatura = $estado->abreviatura;
            return $newestado;
        });
        // DATOS TO SEND:
        $response['ots'] = $listadoOrdenes;
        $response['estados']    = $estados;
        $response['areas']      = $areas;
        return response()->json([
            'code' => '200',
            'message' => 'Exito, lista de Todos las OT para vendedor:' . $id_vendedor,
            'data' =>  $response,
        ], 200);
    }

    public function getDetailsOt(Request $request)
    {
        //get data:
        if (request("ot_id") == null) {
            return response()->json([
                'code' => '404',
                'message' => 'Campo ot_id es requerido',
            ], 404);
        }
        $id  = request("ot_id");
        if (!is_numeric($id)) {
            return response()->json([
                'code' => '404',
                'message' => 'Campo ot_id debe ser numerico',
            ], 404);
        }
        $ot = WorkOrder::with('gestiones.user.role', 'gestiones.respuesta.user', 'gestiones.state', 'gestiones.area', 'gestiones.area_consultada', 'gestiones.files', 'gestiones.type', 'files', 'users')->find($id);

        if (!$ot) {
            return response()->json([
                'code' => '404',
                'message' => 'Órden de trabajo no existe:' . $id,
            ], 404);
        }
        $newOt = new stdClass();
        $newOt->id = $ot->id;
        $newOt->cliente = $ot->client->nombre;
        $newOt->tipo_solicitud = [1 => "Desarrollo Completo", 4 => "Cotiza sin CAD", 2 => "Cotiza con CAD", 3 => "Muestra con CAD",  5 => "Arte con Material"][$ot->tipo_solicitud];
        $newOt->descripcion = $ot->descripcion;
        $newOt->volumen_venta_anual = $ot->volumen_venta_anual;
        $newOt->usd = $ot->usd;
        $newOt->canal = $ot->canal ? $ot->canal->nombre : null;
        $newOt->analisis = $ot->analisis;
        $newOt->plano = $ot->plano;
        $newOt->datos_cotizar = $ot->datos_cotizar;
        $newOt->boceto = $ot->boceto;
        $newOt->nuevo_material = $ot->nuevo_material;
        $newOt->prueba_industrial = $ot->prueba_industrial;
        $newOt->muestra = $ot->muestra;
        $newOt->numero_muestras = $ot->numero_muestras;
        $newOt->cad = $ot->cad;
        $newOt->tipo_item = $ot->productType ? $ot->productType->descripcion : null;
        $newOt->carton = $ot->carton ? $ot->carton->codigo : null;
        $newOt->estilo = $ot->style ? $ot->style->glosa : null;
        $newOt->recubrimiento = $ot->recubrimiento == "1" ? "Cera" : "No";
        $newOt->golpes_largo = $ot->golpes_largo;
        $newOt->golpes_ancho = $ot->golpes_ancho;
        $newOt->largura_hm = $ot->largura_hm;
        $newOt->anchura_hm = $ot->anchura_hm;
        $newOt->area_producto = number_format_unlimited_precision($ot->area_producto_calculo);
        $newOt->bct_minimo = $ot->rmt;
        $newOt->color_1 = $ot->color_1 ? $ot->color_1->descripcion : null;
        $newOt->color_2 = $ot->color_2 ? $ot->color_2->descripcion : null;
        $newOt->color_3 = $ot->color_3 ? $ot->color_3->descripcion : null;
        $newOt->color_4 = $ot->color_4 ? $ot->color_4->descripcion : null;
        $newOt->color_5 = $ot->color_5 ? $ot->color_5->descripcion : null;
        $newOt->impresion_1 = $ot->impresion_1;
        $newOt->impresion_2 = $ot->impresion_2;
        $newOt->impresion_3 = $ot->impresion_3;
        $newOt->impresion_4 = $ot->impresion_4;
        $newOt->impresion_5 = $ot->impresion_5;
        $newOt->pegado = isset($ot->pegado) ? [1 => "Si", 0 => "No"][$ot->pegado] : null;
        $newOt->longitud_pegado = $ot->longitud_pegado;
        $newOt->cera_exterior = $ot->cera_exterior ? 'Si' : 'No';
        $newOt->porcentaje_cera_exterior = $ot->porcentaje_cera_exterior;
        $newOt->cera_interior = $ot->cera_interior ? 'Si' : 'No';
        $newOt->porcentaje_cera_interior = $ot->porcentaje_cera_interior;
        $newOt->barniz_interior = $ot->barniz_interior ? 'Si' : 'No';
        $newOt->porcentaje_barniz_interior = $ot->porcentaje_barniz_interior;
        $newOt->interno_largo = $ot->interno_largo;
        $newOt->interno_ancho = $ot->interno_ancho;
        $newOt->interno_alto = $ot->interno_alto;
        $newOt->externo_largo = $ot->externo_largo;
        $newOt->externo_ancho = $ot->externo_ancho;
        $newOt->externo_alto = $ot->externo_alto;
        $newOt->proceso = $ot->proceso ? $ot->proceso->descripcion : null;
        $newOt->terminacion_pegado = isset($ot->pegado_terminacion)  && $ot->pegado_terminacion != 0 ? [1 => "Si", 0 => "No", 2 => "Interno", 3 => "Externo"][$ot->pegado_terminacion] : null;
        $newOt->armado = $ot->armado ? $ot->armado->descripcion : null;
        $newOt->impresion = isset($ot->impresion) && $ot->impresion != 0 ? [1 => "Offset", 2 => "Flexografía",3 => "Flexografía Alta Gráfica", 4 => "Flexografía Tiro y Retiro",  5 => "Sin Impresión", 6 => "Sin Impresión (Sólo OF)", 7 => "Sin Impresión (Trazabilidad Completa)"][$ot->impresion] : null;
        $newOt->peso_contenido_caja = isset($ot->peso_contenido_caja) ? $ot->peso_contenido_caja : null;
        $newOt->autosoportante = isset($ot->autosoportante) ? [1 => "Si", 0 => "No"][$ot->autosoportante] : null;
        $newOt->envase_primario = isset($ot->envase) ? $ot->envase->descripcion : null;
        $newOt->cajas_altura = isset($ot->cajas_altura) ? $ot->cajas_altura : null;
        $newOt->pallet_sobre_pallet = isset($ot->pallet_sobre_pallet) ? [1 => "Si", 0 => "No"][$ot->pallet_sobre_pallet] : null;
        $newOt->cantidad = isset($ot->cantidad) ? $ot->cantidad : null;
        $newOt->nombre_contacto = $ot->nombre_contacto;
        $newOt->email_contacto = $ot->email_contacto;
        $newOt->telefono_contacto = $ot->telefono_contacto;


        // $newOt->area = $ot->area ? $ot->area->nombre : null;
        // $newOt->area_abreviatura = $ot->area ? $ot->area->abreviatura : null;
        // $newOt->dias_area_actual = $ot->present()->diasPorArea($ot->area->id);
        $newOt->created_at = $ot->created_at->format("d/m/Y");
        // return $newOt;
        // return response()->json([$newOt, $ot]);
        //para test:
        //****return response()->json(['code' => '200', 'message' => 'test', 'data' =>  $data['area_id'], ],200);
        // DATOS TO SEND:
        $response =   $ot;
        return response()->json([
            'code' => '200',
            'message' => 'Detalle de OT:' . $id,
            'data' =>  $newOt,
        ], 200);
    }
    public function getHistoryOt(Request $request)
    {
        //get data:
        if (request("ot_id") == null) {
            return response()->json([
                'code' => '404',
                'message' => 'Campo ot_id es requerido',
            ], 404);
        }
        $id  = request("ot_id");
        if (!is_numeric($id)) {
            return response()->json([
                'code' => '404',
                'message' => 'Campo ot_id debe ser numerico',
            ], 404);
        }
        $ot = WorkOrder::with('gestiones.user.role', 'gestiones.respuesta.user', 'gestiones.state', 'gestiones.area', 'gestiones.area_consultada', 'gestiones.files', 'gestiones.type', 'files', 'users')->find($id);
        if (!$ot) {
            return response()->json([
                'code' => '404',
                'message' => 'Órden de trabajo no existe:' . $id,
            ], 404);
        }
        $puede_gestionar = false;
        if (!empty($ot->ultimoCambioEstado) && ($ot->ultimoCambioEstado->state_id != 9 && $ot->ultimoCambioEstado->state_id != 11) && $ot->current_area_id == 1) {
            $puede_gestionar = true;
        }
        // return response()->json([$ot->gestiones[0]]);

        $gestiones = $ot->gestiones->map(function ($gestion, $key) use ($ot) {
            $newGestion = new stdClass();
            $newGestion->id = $gestion->id;
            $newGestion->tipo_gestion = $gestion->type->nombre;
            $newGestion->observacion = $gestion->observacion;
            $newGestion->area = $gestion->area->nombre;
            $newGestion->usuario = $gestion->user->fullname;
            $newGestion->fecha = $gestion->created_at->format('d/m/Y H:i');
            $newGestion->archivos_subidos = count($gestion->files);

            switch ($gestion->type->id) {
                case 1:
                    // Cambio estado
                    $newGestion->color =  "#6f42c1";
                    $newGestion->nuevo_estado = isset($gestion->state) ? $gestion->state->nombre : null;
                    break;
                case 2:
                    // Consulta
                    $newGestion->color =  "#e83e8c";
                    $newGestion->area_consultada = $gestion->area_consultada->nombre;
                    if (empty($gestion->respuesta)) {
                        $newGestion->estado_consulta = "Por Responder";
                        if (isset($gestion->consulted_work_space_id) && $gestion->consulted_work_space_id == 1 && $ot->creador_id == auth()->user()->id) {
                            $newGestion->responder = true;
                        } else {
                            $newGestion->responder = false;
                        }
                    } else {
                        $newGestion->estado_consulta = "Respondida";
                        $newGestion->respuesta = $gestion->respuesta->respuesta;
                        $newGestion->usuario_respuesta = $gestion->respuesta->user->fullname;
                        $newGestion->fecha_respuesta = $gestion->respuesta->created_at->format('d/m/Y H:i');
                        $newGestion->responder = false;
                    }
                    break;
                case 3:
                    // Archivo
                    $newGestion->color =  "#20c997";
                    break;
                default:
                    $newGestion->color = "#6f42c1";
                    break;
            }

            return $newGestion;
        });

        $states_by_area = [];
        // Si es el primer cambio de estado solo puede derivar a diseño estructural
        $enviadoADesarrollo = false;
        $enviadoADiseño = false;
        foreach ($ot->gestiones as $gestion) {
            // si hay una gestion de cambio de estado echa para diseño entonces permitimos enviar a cualquier otra
            if ($gestion->management_type_id == 1 && $gestion->state_id == 2) {
                $enviadoADesarrollo = true;
            } // si esta en arte con Material hay una gestion de cambio de estado echa para diseño grafico entonces permitimos enviar a cualquier otra
            if ($gestion->management_type_id == 1 && $gestion->state_id == 5) {
                $enviadoADiseño = true;
            }
        }
        if ($enviadoADesarrollo) {
            $states_by_area = [2, 5, 6, 7, 9, 10, 11, 14, 15, 16];
        } else {
            $states_by_area = [2, 9, 10, 11, 14, 15, 16];
        }
        // Si es arte con Material y no ha sido enviado a diseño solo se puede enviar a diseño grafico
        if ($ot->tipo_solicitud == 5 && $enviadoADiseño) {
            $states_by_area = [2, 5, 6, 7, 9, 10, 11, 14, 15, 16];
        } elseif ($ot->tipo_solicitud == 5 && !$enviadoADiseño) {
            $states_by_area = [5, 9, 10, 11, 14, 15, 16];
        }
        // Si llega a estar en estado consulta cliente debemos agregar el estado de regreso a Venta 
        if (!empty($ot->ultimoCambioEstado) && $ot->ultimoCambioEstado->state_id == 10) {
            // array_splice($states_by_area, 5, 1);
            // Eliminar de los estados  consulta cliente = 10
            if (($key = array_search(10, $states_by_area)) !== false) {
                unset($states_by_area[$key]);
            }
            $states_by_area[] = 1;
        }
        // Si llega a estar en estado Espera de OC debemos agregar el estado de regreso a Venta 
        if (!empty($ot->ultimoCambioEstado) && $ot->ultimoCambioEstado->state_id == 14) {
            // dd($states_by_area);
            // Eliminar de los estados  Espera de OC = 14
            if (($key = array_search(14, $states_by_area)) !== false) {
                unset($states_by_area[$key]);
            }
            $states_by_area[] = 1;
        }
        // Si llega a estar en estado Falta definición del Cliente debemos agregar el estado de regreso a Venta 
        if (!empty($ot->ultimoCambioEstado) && $ot->ultimoCambioEstado->state_id == 15) {
            // Eliminar de los estados  Falta definición del Cliente = 15
            if (($key = array_search(15, $states_by_area)) !== false) {
                unset($states_by_area[$key]);
            }
            $states_by_area[] = 1;
        }

        // Si llega a estar en estado Visto bueno cliente debemos agregar el estado de regreso a Venta 
        if (!empty($ot->ultimoCambioEstado) && $ot->ultimoCambioEstado->state_id == 16) {
            // Eliminar de los estados  Visto bueno cliente = 16
            if (($key = array_search(16, $states_by_area)) !== false) {
                unset($states_by_area[$key]);
            }
            $states_by_area[] = 1;
        }

        $states = States::whereIn('id', $states_by_area)->pluck('nombre', 'id')->toArray();


        // DATOS TO SEND:
        $response["gestiones"]       =   $gestiones;
        $response["estados"]       =    $states;
        $response["puede_gestionar"]       =    $puede_gestionar;
        return response()->json([
            'code' => '200',
            'message' => 'Historico OT:' . $id,
            'data' =>  $response,
        ], 200);
    }
    // POST DATA CALLS:
    public function saveGestionOt(Request $request)
    {
        //get data:
        if (request("ot_id") == null || request("observacion") == null) {
            return response()->json([
                'code'    => '401',
                'message' => 'Campos ot_id,observacion son requeridos',
            ], 401);
        }
        if (request("state_id") == null && request("area_id") == null) {
            return response()->json([
                'code'    => '401',
                'message' => 'Falta campo state_id o area_id',
            ], 401);
        }
        $id  = request("ot_id");
        if (!is_numeric($id)) {
            return response()->json([
                'code'    => '401',
                'message' => 'Campo ot_id debe ser numerico',
            ], 401);
        }
        $ot = WorkOrder::with('gestiones.user.role', 'gestiones.respuesta.user', 'gestiones.state', 'gestiones.area', 'gestiones.area_consultada', 'gestiones.files', 'gestiones.type', 'files', 'users')->find($id);
        if (!$ot) {
            return response()->json([
                'code'    => '404',
                'message' => 'Órden de trabajo no existe:' . $id,
            ], 404);
        }

        // Validar si el usuario puede crear gestion actualmente
        $puede_gestionar = false;
        if (!empty($ot->ultimoCambioEstado) && ($ot->ultimoCambioEstado->state_id != 9 && $ot->ultimoCambioEstado->state_id != 11) && $ot->current_area_id == 1) {
            $puede_gestionar = true;
        }
        if (!$puede_gestionar && request("state_id") != null) {
            return response()->json([
                'code'    => '403',
                'message' => 'Usuario no puede gestionar OT actualmente',
            ], 403);
        }


        $gestion = new Management();
        $gestion->observacion = request('observacion');
        // Si viene state_id el tipo de gestion es 1 = cambio estado de lo contrario 2 = consulta
        $gestion->management_type_id = (request("state_id") != null) ? 1 : 2;
        $gestion->user_id = auth()->user()->id;
        $gestion->work_order_id = $id;
        $gestion->work_space_id =  1;

        // Cambio de estado
        if ($gestion->management_type_id == 1) {
            // Cambio de estado
            if (empty($ot->ultimoCambioEstado)) {
                $date = Carbon::parse($ot->ultimo_cambio_area);
            } else if ($ot->ultimo_cambio_area->gt($ot->ultimoCambioEstado->created_at)) {
                // ultimo_cambio_area at is newer than ultimoCambioEstado created at
                $date = Carbon::parse($ot->ultimo_cambio_area);
            } else {
                $date = Carbon::parse($ot->ultimoCambioEstado->created_at);
            }
            $now = Carbon::now();
            // $diff = $date->diffInSeconds($now);
            $diff = get_working_hours($date, $now) * 3600;

            // SI AL CAMBIAR ESTADO EL ESTADO ACTUAL ES UN ESTADO 
            // Terminado = 8
            // Perdido = 9
            // Anulado = 11
            // Entregado = 13
            // Se debe guardar como tiempo de ese proceso de reactivacion 0 segundos
            if (isset($ot->ultimoCambioEstado) && in_array($ot->ultimoCambioEstado->state_id, [8, 9, 11, 13])) {
                $diff = 0;
            }

            $gestion->duracion_segundos = $diff;
            $gestion->state_id = request('state_id');

            // return response()->json([$gestion]);
            // Verificar el estado para cambiar el area
            // Cambiar a Ventas
            if (request('state_id') == 1) {
                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 1;
                $ot->save();
            }
            // Cambiar a Desarrollo
            if (request('state_id') == 2) {
                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 2;
                $ot->save();
            }
            // Cambiar a Diseño
            if (request('state_id') == 5) {
                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 3;
                $ot->save();
            }
            // Cambiar a Catalogacion
            if (request('state_id') == 7) {
                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 4;
                $ot->save();
            }
            // Cambiar a Precatalogacion
            if (request('state_id') == 6) {
                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 5;
                $ot->save();
            }

            // OT PERDIDA
            if (request('state_id') == 9) {
                $ot->ultimo_cambio_area = $now;
                $ot->save();
            }
            // OT Anulada
            if (request('state_id') == 11) {
                $ot->ultimo_cambio_area = $now;
                $ot->save();
            }

            // Visto bueno cliente
            if (request('state_id') == 16) {
                // una vez q venta la envie a otra area debe contabilizar tiempo de nuevo
                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 1;
                $ot->save();
            }
        } // Consulta
        else if ($gestion->management_type_id == 2) {
            // todo
            $gestion->consulted_work_space_id =  request('area_id');

            // return response()->json([$gestion]);
        }
        // Si vienen archivos anexos, los almacenamos 1 a 1 
        // if ($request->hasfile('files')) {

        //     foreach ($request->file('files') as $archivo) {
        //         $file = new File();

        //         $filename = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
        //         $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
        //         $name = $filename . Carbon::now()->timestamp . '.' . $extension;
        //         $peso = $this->human_filesize($archivo->getSize());
        //         $tipo_archivo = $this->tipo_archivo($extension);


        //         $archivo->move(public_path() . '/files/', $name);
        //         $file->url = '/files/' . $name;
        //         $file->peso = round($peso[0]);
        //         $file->unidad = $peso[1];
        //         $file->tipo = $tipo_archivo;
        //         $file->management_id = $gestion->id;
        //         $file->save();
        //     }
        // }




        if (!$gestion->save()) {
            return response()->json([
                'code'    => '500',
                'message' => 'Error al guardar gestion',
            ], 500);
        }
        $newGestion = new stdClass();
        $newGestion->id = $gestion->id;
        $newGestion->tipo_gestion = $gestion->type->nombre;
        $newGestion->observacion = $gestion->observacion;
        $newGestion->area = $gestion->area->nombre;
        $newGestion->usuario = $gestion->user->fullname;
        $newGestion->fecha = $gestion->created_at->format('d/m/Y H:i');
        $newGestion->archivos_subidos = count($gestion->files);
        switch ($gestion->type->id) {
            case 1:
                // Cambio estado
                $newGestion->color =  "#6f42c1";
                $newGestion->nuevo_estado = isset($gestion->state) ? $gestion->state->nombre : null;
                break;
            case 2:
                // Consulta
                $newGestion->color =  "#e83e8c";
                $newGestion->area_consultada = $gestion->area_consultada->nombre;
                if (empty($gestion->respuesta)) {
                    $newGestion->estado_consulta = "Por Responder";
                    if (isset($gestion->consulted_work_space_id) && $gestion->consulted_work_space_id == 1 && $ot->creador_id == auth()->user()->id) {
                        $newGestion->responder = true;
                    } else {
                        $newGestion->responder = false;
                    }
                } else {
                    $newGestion->estado_consulta = "Respondida";
                    $newGestion->respuesta = $gestion->respuesta->respuesta;
                    $newGestion->usuario_respuesta = $gestion->respuesta->user->fullname;
                    $newGestion->fecha_respuesta = $gestion->respuesta->created_at->format('d/m/Y H:i');
                    $newGestion->responder = false;
                }
                break;
            default:
                $newGestion->color = "#6f42c1";
                break;
        }


        return response()->json([
            'code'    => '200',
            'message' => 'Gestion creada con exito',
            'data'    =>   $newGestion,
        ], 200);
    }

    public function saveAnswerOt(Request $request)
    {
        //get data:
        if (request("gestion_id") == null || request("observacion") == null) {
            return response()->json([
                'code'    => '401',
                'message' => 'Campos gestion_id,observacion son requeridos',
            ], 401);
        }
        $id  = request("gestion_id");
        if (!is_numeric($id)) {
            return response()->json([
                'code'    => '401',
                'message' => 'Campo gestion_id debe ser numerico',
            ], 401);
        }
        $gestion = Management::find($id);
        if (!$gestion) {
            return response()->json([
                'code'    => '404',
                'message' => 'Gestion no existe:' . $id,
            ], 404);
        }


        $respuesta = new Answer();
        $respuesta->respuesta = request('observacion');
        $respuesta->user_id = auth()->user()->id;
        $respuesta->management_id = $id;
        // $respuesta->save();


        if (!$respuesta->save()) {
            return response()->json([
                'code'    => '500',
                'message' => 'Error al guardar respuesta',
            ], 500);
        }
        // $newGestion = new stdClass();
        // $newGestion->id = $gestion->id;
        // $newGestion->tipo_gestion = $gestion->type->nombre;
        // $newGestion->observacion = $gestion->observacion;
        // $newGestion->area = $gestion->area->nombre;
        // $newGestion->usuario = $gestion->user->fullname;
        // $newGestion->fecha = $gestion->created_at->format('d/m/Y H:i');
        // $newGestion->archivos_subidos = count($gestion->files);



        return response()->json([
            'code'    => '200',
            'message' => 'Respuesta guardada con exito',
            // 'data'    =>   $newGestion,
        ], 200);
    }

    public function updateTokenNotificationSeller(Request $request)
    {
        //validar que existan los datos necesarios:
        if (!$request->has('token_push_mobile')) {
            return response()->json([
                'code' => '400',
                'message' => 'Bad Request, datos incompletos. Por favor verificar los datos enviados.',
                'data' =>  $request->all(),
            ], 400);
        } else if (empty($request->input('token_push_mobile'))) {
            return response()->json([
                'code' => '400',
                'message' => 'Bad Request, datos invalidos. Por favor verificar los datos enviados.',
                'data' =>  $request->all(),
            ], 400);
        } else if (is_null($request->input('token_push_mobile'))) {
            return response()->json([
                'code' => '400',
                'message' => 'Bad Request, datos invalidos o nulos. Por favor verificar los datos enviados.',
                'data' =>  $request->all(),
            ], 400);
        }
        //obtenemos el id del vendedor:
        $user_id_vendedor = Auth::user()->id;
        //obtenemos el user:
        $user = User::find($user_id_vendedor);
        $user->token_push_mobile = $request->input('token_push_mobile');
        if ($user->save()) { //save
            return response()->json([
                'code' => '200',
                'message' => 'Exito, Token de notificacion push ha sido guardado!'
            ], 200);
        } else {
            return response()->json([
                'code' => '400',
                'message' => 'Error al guardar token. Por favor verificar los datos enviados.',
                'data' =>  $request->all(),
            ], 400);
        }
    }

    public function postMaterialesCliente(Request $request)
    {
        $rut_clientes = $request->all();

        if (count($rut_clientes) == 0) {
            return response()->json([
                'code' => '404',
                'message' => 'Campo RUT es requerido',
            ], 404);
        }

        $clientes = Client::whereIn('rut', $rut_clientes)->get();

        $clientes_id = [];
        foreach($clientes as $cliente){
            $clientes_id[] = $cliente->id;
        }

        $materiales = Material::whereIn('client_id', $clientes_id)->get();
        return response()->json([
            'code' => '200',
            'data' =>  $materiales,
        ], 200);

    }

    public function postMaterialesJerarquia(Request $request)
    {
        $codigo_material = $request->all();

        if (count($codigo_material) == 0) {
            return response()->json([
                'code' => '404',
                'message' => 'El código es requerido',
            ], 404);
        }

        $material = Material::whereIn('codigo', $codigo_material)->get();

        $datos = [];
        foreach($material as $value){

            //Busca el Id de la jerarquia 2 mediante el id de la jerarquia 3 que esta guardada en la tabla de materiales
            $subhierarchy_id = Subsubhierarchy::where('id', $value->sap_hiearchy_id)->select('subhierarchy_id')->pluck('subhierarchy_id')->first();
            //Busca la descripcion de ls jerarquia 2
            $subhierarchy_descripcion = Subhierarchy::where('id', $subhierarchy_id)->select('descripcion')->pluck('descripcion')->first();

            $datos[$value->codigo] = [
                'material_descripcion' => $value->descripcion, 
                'material_jerarquia' => $subhierarchy_descripcion
            ];
        }

        // $material_detalle = Material::whereIn('client_id', $clientes_id)->get();
        return response()->json([
            'code' => '200',
            'data' =>  $datos,
        ], 200);

    }

    public function getJerarquias(Request $request)
    {
        //Jerarquia 1:
        //Lista las jerarquias 1 que estan activas
        $hierarchies = Hierarchy::where('active', 1)->get();
       
        $datos = [];
        foreach($hierarchies as $key => $value){
        
            //Se setea el key por el nombre de la descripcion
            $datos[$value->descripcion] = [];

            //Busca la jerarquia 2 mediante el id de la jerarquia 1
            $subhierarchies = Subhierarchy::where('hierarchy_id', $value->id)->get();

            foreach($subhierarchies as $kk => $sub) {
                $datos[$value->descripcion][] = $sub->descripcion;
            }
            
        }

        return response()->json([
            'code' => '200',
            'data' =>  $datos,
        ], 200);

    }*/
}
