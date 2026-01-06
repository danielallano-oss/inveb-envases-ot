<?php

namespace App\Http\Controllers;

use App\Canal;
use App\Client;
use App\Constants;
use App\Notification;
use App\States;
use App\User;
use App\user_work_order;
use App\UserWorkOrder;
use App\WorkOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserWorkOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(auth()->user()->role->area->id);
        //filters:
        $query = WorkOrder::with('canal', 'client', 'creador', 'subsubhierarchy.subhierarchy.hierarchy')
            ->select('work_orders.*');

        // if (!is_null(request()->query('client_id'))) {
        //     $query = $query->whereIn('client_id', request()->query('client_id'));
        // }
        // Filtro por id ot
        if (!is_null(request()->input('id'))) {
            $query->where('work_orders.id', request()->input('id'));
        }
        if (!is_null(request()->query('vendedor_id'))) {
            $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
        } else if (Auth()->user()->isVendedor()) {
            $query = $query->where('creador_id', auth()->user()->id);
        }
        if (!is_null(request()->query('tipo_solicitud'))) {
            $query = $query->whereIn('tipo_solicitud', request()->query('tipo_solicitud'));
        }
        if (!is_null(request()->query('canal_id'))) {
            $query = $query->whereIn('canal_id', request()->query('canal_id'));
        }

        // Filtros por rol-area
        if (Auth()->user()->isIngeniero() ||  Auth()->user()->isDiseñador() ||  Auth()->user()->isTecnicoMuestras()) {

            $query = $query->with('asignaciones')->whereDoesntHave("asignaciones", function ($q) {
                $q->where("area_id", auth()->user()->role->area->id);
            })->where('current_area_id', auth()->user()->role->area->id);
        }
        // Catalogador funciona distinto puesto que maneja area 4 y 5
        if (Auth()->user()->isCatalogador()) {

            $query = $query->with('asignaciones')->whereDoesntHave("asignaciones", function ($q) {
                $q->where("area_id", auth()->user()->role->area->id);
            })->whereIn('current_area_id', [4, 5]);
        }
        // relacionar asignacion con tipo de area
        if (Auth()->user()->isJefeVenta()) {
            $query = $query->with('vendedorAsignado.user');
        }
        if (Auth()->user()->isJefeDesarrollo()) {
            $query = $query->with('ingenieroAsignado.user');
        }
        if (Auth()->user()->isJefeDiseño()) {
            $query = $query->with('diseñadorAsignado.user');
        }
        if (Auth()->user()->isJefeCatalogador()) {
            $query = $query->with('catalogadorAsignado.user');
        }
        if (Auth()->user()->isJefeMuestras()) {
            $query = $query->with('tecnicoMuestrasAsignado.user');
        }
        $asignado = false;
        if (Auth()->user()->isJefeVenta() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño() || Auth()->user()->isJefeMuestras()) {
            if (!is_null(request()->query('asignado')) && request()->query('asignado')[0] == "SI") {
                $asignado = true;
                $query = $query->with('asignaciones')->whereHas("asignaciones", function ($q) {
                    $q->where("area_id", auth()->user()->role->area->id);
                });
            } else {
                $query = $query->with('asignaciones')->whereDoesntHave("asignaciones", function ($q) {
                    $q->where("area_id", auth()->user()->role->area->id);
                })->where('current_area_id', auth()->user()->role->area->id);
            }
        }
        if (Auth()->user()->isJefeCatalogador()) {
            if (!is_null(request()->query('asignado')) && request()->query('asignado')[0] == "SI") {
                $asignado = true;
                $query = $query->with('asignaciones')->whereHas("asignaciones", function ($q) {
                    $q->where("area_id", auth()->user()->role->area->id);
                });
            } else {
                $query = $query->with('asignaciones')->whereDoesntHave("asignaciones", function ($q) {
                    $q->where("area_id", auth()->user()->role->area->id);
                })->whereIn('current_area_id', [4, 5]);
            }
        }

        // Por defecto filtra por todos los estados activos
        if (is_null(request()->input('estado_id'))) {
            $estados_activos = [1, 2, 3, 4, 5, 6, 7, 10, 12, 14, 15, 16, 17, 18];
            $query = $query->leftjoin('managements', 'work_orders.id', 'managements.work_order_id')
                ->where('managements.management_type_id', 1)
                ->whereIn("managements.state_id", $estados_activos)
                ->where('managements.id', function ($q) {
                    $q->select('id')
                        ->from('managements')
                        ->whereColumn('work_order_id', 'work_orders.id')
                        ->where('managements.management_type_id', 1)
                        ->latest()
                        ->limit(1);
                });
        } else {
            $estados = request()->query('estado_id');
            $query = $query->leftjoin('managements', 'work_orders.id', 'managements.work_order_id')
                ->where('managements.management_type_id', 1)
                ->whereIn("managements.state_id", $estados)
                ->where('managements.id', function ($q) {
                    $q->select('id')
                        ->from('managements')
                        ->whereColumn('work_order_id', 'work_orders.id')
                        ->where('managements.management_type_id', 1)
                        ->latest()
                        ->limit(1);
                });
        }
        // Filtro por fechas
        // Sin fechas
        if (is_null(request()->input('date_desde')) && is_null(request()->input('date_hasta'))) {
            $ots = $query->paginate(20);
        }
        // Solo viene la fecha hasta
        else if (is_null(request()->input('date_desde')) && !is_null(request()->input('date_hasta'))) {

            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
            $ots = $query->whereDate('work_orders.created_at', '<=', $toDate)->paginate(20);
        } // Solo viene la fecha desde
        else if (!is_null(request()->input('date_desde')) && is_null(request()->input('date_hasta'))) {

            $fromDate = Carbon::createFromFormat('d/m/Y', request()->input('date_desde'))->toDateString();
            // dd($fromDate);
            $ots = $query->whereDate('work_orders.created_at', '>=', $fromDate)->paginate(20);
        } // vienen ambas fechas
        else {
            $fromDate = Carbon::createFromFormat('d/m/Y', request()->input('date_desde'))->toDateString();
            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
            $ots = $query->whereBetween('work_orders.created_at', [$fromDate, $toDate])->paginate(20);
        }

        // $ots = $query->whereBetween('created_at', [$fromDate, $toDate])->paginate(20);

        $tipo_solicitudes = [1 => "Desarrollo Completo", 4 => "Cotiza sin CAD", 2 => "Cotiza con CAD", 3 => "Muestra con CAD", 7 => "OT Proyectos Innovación",  5 => "Arte con Material", 6 => "Otras Solicitudes Desarrollo"];

        $clients = Client::where('active', 1)->get();
        $clients->map(function ($client) {
            $client->client_id = $client->id;
        });

        $canals = Canal::all();
        $canals->map(function ($canal) {
            $canal->canal_id = $canal->id;
        });

        $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4, 19])->get();
        $vendedores->map(function ($vendedor) {
            $vendedor->vendedor_id = $vendedor->id;
        });
        $estados = States::where('status', '=', 'active')->get();
        $estados->map(function ($estado) {
            $estado->estado_id = $estado->id;
        });
        // dd($ots);
        return view('asignations.index', compact('ots', 'clients', 'canals', 'tipo_solicitudes', 'asignado', 'vendedores', 'estados'));
    }

    public function modalAsignacion(Request $request)
    {
        // dd(request()->all());
        if (!is_null(request('ot_id')) && !is_null(request('role_id'))) {
            $ot_id = request('ot_id');
            $role_id = request('role_id');
            $asignarDirecto = false;
            // Perfiles no Jefes
            if (in_array($role_id, [Constants::Ingeniero, Constants::Diseñador, Constants::Catalogador, Constants::TecnicoMuestras])) {
                $asignarDirecto = true;
                return view('asignations.modalAsignacion', compact('asignarDirecto', 'ot_id'));
            }
            // Perfiles Jefes
            $asignacionOT = UserWorkOrder::where('work_order_id', $ot_id)->where('area_id', auth()->user()->role->area->id)->first();
            if ($asignacionOT) {

                $profesionalActual = User::find($asignacionOT->user_id);
                // Selecciona usuarios no jefes del area, menos el profesional actual y agregando al jefe logeado
                $profesionales = User::where('role_id', (auth()->user()->role_id + 1))->where('id', '!=', $profesionalActual->id)->where("active", 1)->orwhere('id', auth()->user()->id)->get();
            } else {
                $profesionalActual = null;
                // Selecciona usuarios no jefes del area y agregando al jefe logeado
                $profesionales = User::where('role_id', (auth()->user()->role_id + 1))->where("active", 1)->orwhere('id', auth()->user()->id)->get();
            }
            // dd($profesionales);
            return view('asignations.modalAsignacion', compact('asignarDirecto', 'ot_id', 'profesionalActual', 'profesionales'));
            // return view('asignations.modalAsignacion', compact('ejecutivos', 'ejecutivoActual', 'ejecutivoSugerido', 'ejecutivosPorContrato', 'contrato_id'));
        } else return "Por favor intentar nuevamente";
    }

    public function asignarOT(Request $request)
    {
        // dd($request->all());
        if (!empty($request->all())) {
            try {
                // Consultamos por alguna asignacion para OT y Area definida
                $asignacionOT = UserWorkOrder::where('work_order_id', request('id'))->where('area_id', auth()->user()->role->area->id)->first();
                // dd($asignacionOT);

                // De haber asignacion reasignamos de lo contrario la creamos
                if ($asignacionOT) {
                    $asignacionOT->user_id = request('asignado_id');
                    $asignacionOT->save();
                    $motivo = "Reasignado";
                } else {
                    $ot = WorkOrder::find(request('id'));

                    $date = Carbon::parse($ot->ultimo_cambio_area);
                    $now = Carbon::now();
                    $diff = $date->diffInSeconds($now);

                    $asignacion = new UserWorkOrder();
                    $asignacion->work_order_id = request('id');
                    $asignacion->user_id = request('asignado_id');
                    $asignacion->area_id = auth()->user()->role->area->id;
                    $asignacion->tiempo_inicial = $diff;
                    $asignacion->save();

                    $motivo = "Asignado";
                }
                // Solo se notifica cuando es asignada por un jefe a un usuario distinto a si mismo
                if ((Auth()->user()->isJefeVenta() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño() || Auth()->user()->isJefeCatalogador() || Auth()->user()->isJefeMuestras()) && (auth()->user()->id != request('asignado_id'))) {

                    // Crear notificacion de asignacion
                    $notificacion = new Notification();
                    $notificacion->work_order_id = request('id');
                    $notificacion->user_id = request('asignado_id');
                    $notificacion->generador_id = auth()->user()->id;
                    $notificacion->motivo = $motivo;
                    $notificacion->observacion = '';
                    $notificacion->save();
                }

                return 200;
            } catch (\Exception $e) {
                // do task when error
                Log::info("error asignacion: " . $e->getMessage());
                echo $e->getMessage();   // insert query

            }
        }
        return "Error al actualizar datos";
    }

    public function asignacionesConMensaje()
    {
        return redirect()->to('asignaciones')->with('success', 'Asignacion realizada correctamente.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\user_work_order  $user_work_order
     * @return \Illuminate\Http\Response
     */
    public function show(user_work_order $user_work_order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\user_work_order  $user_work_order
     * @return \Illuminate\Http\Response
     */
    public function edit(user_work_order $user_work_order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\user_work_order  $user_work_order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, user_work_order $user_work_order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\user_work_order  $user_work_order
     * @return \Illuminate\Http\Response
     */
    public function destroy(user_work_order $user_work_order)
    {
        //
    }
}
