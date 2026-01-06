<?php

namespace App\Http\Controllers;

use App\Notification;
use App\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Notification::with(
            'ot.client',
            'ot.productType',
            "ot.ultimoCambioEstado.area",
            "ot.gestiones",
            "generador"
        );
        // Del usuario que consulta
        $query = $query->where("user_id", auth()->user()->id);
        // Solo notificaciones pendientes
        $query = $query->where("active", 1);

        $query = $query->with(['ot' => function ($q) {
            // Calculo total de tiempo
            $q = $q->withCount([
                'gestiones AS tiempo_total' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
                }
            ]);
        }]);

        $notificaciones = $query->paginate(20);
        return view('work-orders.notificaciones', compact('notificaciones'));
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
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        //
    }


    // public function desactivarNotificacion($id)
    // {


    //     $notificacion = Notification::find($id);

    //     if ($notificacion) {

    //         $notificacion->active = 0;
    //         $notificacion->save();
    //     }

    //     $query = Notification::with(
    //         'ot.client',
    //         'ot.productType',
    //         "ot.ultimoCambioEstado.area",
    //         "ot.gestiones",
    //         "generador"
    //     );
    //     // Del usuario que consulta
    //     $query = $query->where("user_id", auth()->user()->id);
    //     // Solo notificaciones pendientes
    //     $query = $query->where("active", 1);

    //     $query = $query->with(['ot' => function ($q) {
    //         // Calculo total de tiempo
    //         $q = $q->withCount([
    //             'gestiones AS tiempo_total' => function ($q) {
    //                 $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
    //             }
    //         ]);
    //     }]);

    //     $notificaciones = $query->paginate(20);
    //     return view('work-orders.notificaciones', compact('notificaciones'));
    // }

    public function inactivarNotificacion($id)
    {
        $notificacion = Notification::find($id);


        if ($notificacion) {

            $notificacion->active = 0;
            $notificacion->save();
        }
        return redirect()->back();
    }
}
