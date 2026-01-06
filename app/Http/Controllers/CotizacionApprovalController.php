<?php

namespace App\Http\Controllers;

use App\Client;
use App\Cotizacion;
use App\CotizacionApproval;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificarCotizacionAprobadaMgBrutoNegativo;

class CotizacionApprovalController extends Controller
{

    public function gestionarAprobacionCotizacion($id)
    {
        

        $cotizacion = Cotizacion::find($id);
        $accion = null;
        $role = auth()->user()->role_id;
        // Segun el nivel de aprobacion de la cotizacion validamos la accion
        // 1 = solo jefe ventas
        if ($cotizacion->nivel_aprobacion == 1) {

            if (request("estado") == "aprobado") {

                $cotizacion->estado_id = 3;
                $accion = "Aprobación Total";
            } else {
                $cotizacion->estado_id = 6;
                $accion = "Rechazo";
            }
        } elseif ($cotizacion->nivel_aprobacion == 2) {
            // 2 = jefe venta y gerente comercial
            // Si es jefe de ventas la aprobacion es parcial y procede al gerente comercial
            if ($role == 3) {
                if (request("estado") == "aprobado") {

                    $accion = "Aprobación Parcial";
                    $cotizacion->role_can_show = 15; //gerente comercial
                } else {
                    $cotizacion->estado_id = 6;
                    $accion = "Rechazo";
                }
            } else {
                if (request("estado") == "aprobado") {

                    $accion = "Aprobación Total";
                    $cotizacion->estado_id = 3;
                } else {
                    $cotizacion->estado_id = 6;
                    $accion = "Rechazo";
                }
            }
        } elseif ($cotizacion->nivel_aprobacion == 3) {
            // 3 = jefe venta y gerentes comercial y general

            // Si es jefe de ventas la aprobacion es parcial y procede al gerente comercial
            if ($role == 3) {
                if (request("estado") == "aprobado") {

                    $accion = "Aprobación Parcial";
                    $cotizacion->role_can_show = 15; //gerente comercial
                } else {
                    $cotizacion->estado_id = 6;
                    $accion = "Rechazo";
                }
            } else if ($role == 15) {
                if (request("estado") == "aprobado") {

                    $accion = "Aprobación Parcial";
                    $cotizacion->role_can_show = 2; //gerente general
                } else {
                    $cotizacion->estado_id = 6;
                    $accion = "Rechazo";
                }
            } else {
                if (request("estado") == "aprobado") {

                    $accion = "Aprobación Total";
                    $cotizacion->estado_id = 3;
                } else {
                    $cotizacion->estado_id = 6;
                    $accion = "Rechazo";
                }
            }
        }
        $cotizacion->save();

        //Se envia correo de Aprobacion de Cotizacion con Mg Bruto Negativo
        if(($accion=="Aprobación Total" || $accion=="Aprobación Parcial") && $cotizacion->enviar_a_comite==1){
            Mail::to('maria.botella@cmpc.com')->send(new NotificarCotizacionAprobadaMgBrutoNegativo($cotizacion->id));
        }

        $aprobacion = new CotizacionApproval;
        $aprobacion->motivo = request("observacion");
        $aprobacion->role_do_action = auth()->user()->role_id;
        $aprobacion->action_made = $accion;
        $aprobacion->user_id = auth()->user()->id;
        $aprobacion->cotizacion_id = $id;
        $aprobacion->save();

   


        return redirect()->route('cotizador.aprobaciones')->with('success', 'Cotización gestionada con Exito');
    }

    public function gestionarAprobacionCotizacionExterno($id)
    {
        //dd($id, request()->all());

        $cotizacion = Cotizacion::find($id);
        $accion = null;
        $role = auth()->user()->role_id;
        // Segun el nivel de aprobacion de la cotizacion validamos la accion
        // 1 = solo jefe ventas
        //if ($cotizacion->nivel_aprobacion == 1) {

            if (request("estado") == "aprobado") {

                $cotizacion->estado_id = 3;
                $accion = "Aprobación Total";
            } else {
                $cotizacion->estado_id = 6;
                $accion = "Rechazo";
            }
        /*} elseif ($cotizacion->nivel_aprobacion == 2) {
            // 2 = jefe venta y gerente comercial
            // Si es jefe de ventas la aprobacion es parcial y procede al gerente comercial
            if ($role == 3) {
                if (request("estado") == "aprobado") {

                    $accion = "Aprobación Parcial";
                    $cotizacion->role_can_show = 15; //gerente comercial
                } else {
                    $cotizacion->estado_id = 6;
                    $accion = "Rechazo";
                }
            } else {
                if (request("estado") == "aprobado") {

                    $accion = "Aprobación Total";
                    $cotizacion->estado_id = 3;
                } else {
                    $cotizacion->estado_id = 6;
                    $accion = "Rechazo";
                }
            }
        } elseif ($cotizacion->nivel_aprobacion == 3) {
            // 3 = jefe venta y gerentes comercial y general

            // Si es jefe de ventas la aprobacion es parcial y procede al gerente comercial
            if ($role == 3) {
                if (request("estado") == "aprobado") {

                    $accion = "Aprobación Parcial";
                    $cotizacion->role_can_show = 15; //gerente comercial
                } else {
                    $cotizacion->estado_id = 6;
                    $accion = "Rechazo";
                }
            } else if ($role == 15) {
                if (request("estado") == "aprobado") {

                    $accion = "Aprobación Parcial";
                    $cotizacion->role_can_show = 2; //gerente general
                } else {
                    $cotizacion->estado_id = 6;
                    $accion = "Rechazo";
                }
            } else {
                if (request("estado") == "aprobado") {

                    $accion = "Aprobación Total";
                    $cotizacion->estado_id = 3;
                } else {
                    $cotizacion->estado_id = 6;
                    $accion = "Rechazo";
                }
            }
        }*/
        $cotizacion->save();

        $aprobacion = new CotizacionApproval;
        $aprobacion->motivo = request("observacion");
        $aprobacion->role_do_action = auth()->user()->role_id;
        $aprobacion->action_made = $accion;
        $aprobacion->user_id = auth()->user()->id;
        $aprobacion->cotizacion_id = $id;
        $aprobacion->save();

        return redirect()->route('cotizador.index_cotizacion')->with('success', 'Cotización gestionada con Exito');
    }


    public function aprobaciones()
    {
        // $detalles = DetalleCotizacion::all()->toArray();
        // dd($detalles);

        //filtros:
        $creadores = User::where('active', 1)->whereIn('role_id', [3, 4])->get();
        $creadores->map(function ($creador) {
            $creador->creador_id = $creador->id;
        });

        $clients = Client::whereHas('ots')->where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->get();
        $clients->map(function ($client) {
            $client->client_id = $client->id;
        });

        //filters:
        $query = Cotizacion::with('detalles');
        if(Auth()->user()->isJefeVenta()){
            $query = $query->whereHas('user', function ($query) {
                return $query->where("jefe_id", Auth()->user()->id);
            });
        }
        
        $query = $query->where("role_can_show", auth()->user()->role_id)->where("estado_id", 2)->where("active", 1);
        
        
        if (!is_null(request()->query('id'))) {
            $query = $query->whereIn('user_id', request()->query('id'));
        }
        // Filtro por id de cotizacion
        if (!is_null(request()->input('cotizacion_id'))) {
            $query->where('id', request()->input('cotizacion_id'));
        }
        // filtro por cliente
        if (!is_null(request()->query('client_id'))) {
            $query = $query->whereIn('client_id', request()->query('client_id'));
        }

        $cotizaciones = $query->orderBy('id', 'desc')->paginate(20);
        //dd($query,$cotizaciones);

        $updatedItems = $cotizaciones->getCollection();

        // data manipulation
        $updatedItems->map(function ($cotizacion) {
            $cotizacion->monto_total = $cotizacion->detalles->map(function ($detalle) {
                // dd($detalle->precios->precio_total["usd_caja"]);
                return $detalle->precios->precio_total["usd_caja"] * $detalle->cantidad;
            });
            // dd($cotizacion->monto_total[0]/1000);
            return $cotizacion;
        });
        // ...

        $cotizaciones->setCollection($updatedItems);
        return view('cotizador.aprobaciones.index', compact('cotizaciones', 'creadores', 'clients'));
    }
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
     * @param  \App\CotizacionApproval  $cotizacionApproval
     * @return \Illuminate\Http\Response
     */
    public function show(CotizacionApproval $cotizacionApproval)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CotizacionApproval  $cotizacionApproval
     * @return \Illuminate\Http\Response
     */
    public function edit(CotizacionApproval $cotizacionApproval)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CotizacionApproval  $cotizacionApproval
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CotizacionApproval $cotizacionApproval)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CotizacionApproval  $cotizacionApproval
     * @return \Illuminate\Http\Response
     */
    public function destroy(CotizacionApproval $cotizacionApproval)
    {
        //
    }
}
