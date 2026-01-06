<?php

namespace App\Http\Controllers;

use App\Management;
use App\Muestra;
use App\Carton;
use App\Notification;
use App\WorkOrder;
use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MuestraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function visualizar_muestra_html()
    {   
        // dd(request()->all());
        //$muestra = Muestra::find(request("id"));
        $muestra = Muestra::find(request("id"));
        //dd($muestra);
        $tipo = (request("tipo"));
      
        // dd($muestra);
        //view()->share('muestra', $muestra);
        //view()->share('tipo', $tipo);
        return view('pdf.etiqueta_muestra',compact('tipo','muestra'));
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
        $now =Carbon::now();
        $destinatarios = request("destinatarios_id");
        
        
        // dd($destinatarios);
        // unset($destinatarios[0]);
        // dd($destinatarios);
        // dd(request()->all());


        // Solo es requerido cuando se crea la muestra y esto solo lo puede hacer el usuario diseñador tecnico o jefe desarrollo
        if (auth()->user()->role_id == 5 || auth()->user()->role_id == 6) {
            $request->validate([
                // Datos Comerciales
                // 'cad_id' => 'required',
                // 'carton_id' => 'required',
                // 'pegado_id' => 'required',
                // 'destinatarios_id' => 'required|array',
                // 'ot_id' => 'required',
            ]);
        }
        $muestra_id = request('muestra_id');

        if ($muestra_id) {
            $muestra = Muestra::find($muestra_id);
        } else {
            $muestra = new Muestra();
        }
        // Si es tecnico de muestras solo edita los comentarios
        if (auth()->user()->role_id == 13 || auth()->user()->role_id == 14) {
            // dd(Carbon::parse(str_replace("/", "-", $request->input('fecha_corte')))->format('Y-m-d'));
            $muestra->tiempo_unitario = (trim($request->input('tiempo_unitario')) != '') ? "2021-01-01 " . $request->input('tiempo_unitario') . ":00" : $muestra->tiempo_unitario;
            // $muestra->fecha_corte = (trim($request->input('fecha_corte')) != '') ? Carbon::parse(str_replace("/", "-", $request->input('fecha_corte')))->format('Y-m-d') : $muestra->fecha_corte;
            if (trim($request->input('check_fecha_corte_vendedor')) == 'on') {
                if ($muestra->fecha_corte_vendedor == null) {
                    $muestra->fecha_corte_vendedor = $now;
                    /*$muestra->estado=3;
                    $muestra->fin_sala_corte=$now;
                    $diff = get_working_hours($muestra->inicio_sala_corte, $now) * 3600;
                    $muestra->duracion_sala_corte=$diff;*/
                }
                
            } else {
                $muestra->fecha_corte_vendedor = null;
            }
            if (trim($request->input('check_fecha_corte_diseñador')) == 'on') {
                if ($muestra->fecha_corte_diseñador == null) {
                    $muestra->fecha_corte_diseñador = $now;
                    /*$muestra->estado=3;
                    $muestra->fin_sala_corte=$now;
                    $diff = get_working_hours($muestra->inicio_sala_corte, $now) * 3600;
                    $muestra->duracion_sala_corte=$diff;*/
                }
                
            } else {
                $muestra->fecha_corte_diseñador = null;
            }
            if (trim($request->input('check_fecha_corte_laboratorio')) == 'on') {
                if ($muestra->fecha_corte_laboratorio == null) {
                    $muestra->fecha_corte_laboratorio = $now;
                    /*$muestra->estado=3;
                    $muestra->fin_sala_corte=$now;
                    $diff = get_working_hours($muestra->inicio_sala_corte, $now) * 3600;
                    $muestra->duracion_sala_corte=$diff;*/
                }
               // $muestra->estado=3;
            } else {
                $muestra->fecha_corte_laboratorio = null;
            }
            if (trim($request->input('check_fecha_corte_1')) == 'on') {
                if ($muestra->fecha_corte_1 == null) {
                    $muestra->fecha_corte_1 = $now;
                    /*$muestra->estado=3;
                    $muestra->fin_sala_corte=$now;
                    $diff = get_working_hours($muestra->inicio_sala_corte, $now) * 3600;
                    $muestra->duracion_sala_corte=$diff;*/
                }
                
            } else {
                $muestra->fecha_corte_1 = null;
            }
            if (trim($request->input('check_fecha_corte_2')) == 'on') {
                if ($muestra->fecha_corte_2 == null) {
                    $muestra->fecha_corte_2 = $now;
                   /* $muestra->estado=3;
                    $muestra->fin_sala_corte=$now;
                    $diff = get_working_hours($muestra->inicio_sala_corte, $now) * 3600;
                    $muestra->duracion_sala_corte=$diff;*/
                }
                
            } else {
                $muestra->fecha_corte_2 = null;
            }
            if (trim($request->input('check_fecha_corte_3')) == 'on') {
                if ($muestra->fecha_corte_3 == null) {
                    $muestra->fecha_corte_3 = $now;
                    /*$muestra->estado=3;
                    $muestra->fin_sala_corte=$now;
                    $diff = get_working_hours($muestra->inicio_sala_corte, $now) * 3600;
                    $muestra->duracion_sala_corte=$diff;*/
                }
                
            } else {
                $muestra->fecha_corte_3 = null;
            }
            if (trim($request->input('check_fecha_corte_4')) == 'on') {
                if ($muestra->fecha_corte_4 == null) {
                    $muestra->fecha_corte_4 = $now;
                   /* $muestra->estado=3;
                    $muestra->fin_sala_corte=$now;
                    $diff = get_working_hours($muestra->inicio_sala_corte, $now) * 3600;
                    $muestra->duracion_sala_corte=$diff;*/
                }
                
            } else {
                $muestra->fecha_corte_4 = null;
            }
            if (trim($request->input('check_fecha_corte_diseñador_revision')) == 'on') {
                if ($muestra->fecha_corte_diseñador_revision == null) {
                    $muestra->fecha_corte_diseñador_revision = $now;
                    /*$muestra->estado=3;
                    $muestra->fin_sala_corte=$now;
                    $diff = get_working_hours($muestra->inicio_sala_corte, $now) * 3600;
                    $muestra->duracion_sala_corte=$diff;*/
                }
                
            } else {
                $muestra->fecha_corte_diseñador_revision = null;
            }
            // $muestra->fecha_corte_diseñador = (trim($request->input('check_fecha_corte_diseñador')) == 'on') ? Carbon::now() : null;
            // $muestra->fecha_corte_laboratorio = (trim($request->input('check_fecha_corte_laboratorio')) == 'on') ? Carbon::now() : null;
            // $muestra->fecha_corte_1 = (trim($request->input('check_fecha_corte_1')) == 'on') ? Carbon::now() : null;
            // $muestra->fecha_corte_2 = (trim($request->input('check_fecha_corte_2')) == 'on') ? Carbon::now() : null;
            // $muestra->fecha_corte_3 = (trim($request->input('check_fecha_corte_3')) == 'on') ? Carbon::now() : null;
            // $muestra->fecha_corte_4 = (trim($request->input('check_fecha_corte_4')) == 'on') ? Carbon::now() : null;

            $muestra->check_fecha_corte_vendedor = (trim($request->input('check_fecha_corte_vendedor')) == 'on') ?  1 : 0;
            $muestra->check_fecha_corte_diseñador = (trim($request->input('check_fecha_corte_diseñador')) == 'on') ?  1 : 0;
            $muestra->check_fecha_corte_laboratorio = (trim($request->input('check_fecha_corte_laboratorio')) == 'on') ?  1 : 0;
            $muestra->check_fecha_corte_1 = (trim($request->input('check_fecha_corte_1')) == 'on') ?  1 : 0;
            $muestra->check_fecha_corte_2 = (trim($request->input('check_fecha_corte_2')) == 'on') ?  1 : 0;
            $muestra->check_fecha_corte_3 = (trim($request->input('check_fecha_corte_3')) == 'on') ?  1 : 0;
            $muestra->check_fecha_corte_4 = (trim($request->input('check_fecha_corte_4')) == 'on') ?  1 : 0;

            $muestra->check_fecha_corte_diseñador_revision = (trim($request->input('check_fecha_corte_diseñador_revision')) == 'on') ?  1 : 0;

            $muestra->pegado_id = request('pegado_id');

            $muestra->cantidad_vendedor = request('cantidad_vendedor');
            $muestra->cantidad_diseñador = request('cantidad_diseñador');
            $muestra->cantidad_laboratorio = request('cantidad_laboratorio');
            $muestra->cantidad_diseñador_revision = request('cantidad_diseñador_revision');

            $muestra->cantidad_1 = request('cantidad_1');
            $muestra->carton_muestra_id = (trim($request->input('carton_muestra_id')) != '') ? request('carton_muestra_id') : $muestra->carton_muestra_id;
        } else {

            $muestra->user_id = auth()->user()->id;
            $muestra->work_order_id = request('ot_id');

            //Asignación estado de muestra en progreso si se crea cuando la ot esta en sala de muestra
            if (request('ot_id') != null && request('ot_id') != 0 ){
                $ot = WorkOrder::find(request('ot_id'));
                if($ot->current_area_id==6){
                    $muestra->estado = 1;
                }
            }
           
            // El cad puede ser seleccionado de un listado o ingresado libremente por eso permite guardar ambas opciones
            $muestra->cad_id = (trim($request->input('cad_id')) != '' && $request->input('cad') == '') ? $request->input('cad_id') : $muestra->cad_id;
            $muestra->cad = (trim($request->input('cad')) != '') ? $request->input('cad') : $muestra->cad;
            $muestra->carton_id = request('carton_id');
            $muestra->carton_muestra_id = (trim($request->input('carton_muestra_id')) != '') ? request('carton_muestra_id') : $muestra->carton_muestra_id;
            $muestra->pegado_id = request('pegado_id');
            $muestra->destinatarios_id = isset($destinatarios) ?  array($destinatarios[0]) : $muestra->destinatarios_id;

            $muestra->cantidad_vendedor = request('cantidad_vendedor');
            $muestra->cantidad_diseñador = request('cantidad_diseñador');
            $muestra->cantidad_laboratorio = request('cantidad_laboratorio');

            $muestra->cantidad_diseñador_revision = request('cantidad_diseñador_revision');

            $muestra->comentario_vendedor = "Retira Vendedor";
            $muestra->comentario_diseñador = (trim($request->input('comentario_diseñador')) != '') ? $request->input('comentario_diseñador') : $muestra->comentario_diseñador;
            $muestra->comentario_laboratorio = (trim($request->input('comentario_laboratorio')) != '') ? $request->input('comentario_laboratorio') : $muestra->comentario_laboratorio;
            $muestra->comentario_1 = (trim($request->input('comentario_1')) != '') ? $request->input('comentario_1') : $muestra->comentario_1;
            $muestra->comentario_2 = (trim($request->input('comentario_2')) != '') ? $request->input('comentario_2') : $muestra->comentario_2;
            $muestra->comentario_3 = (trim($request->input('comentario_3')) != '') ? $request->input('comentario_3') : $muestra->comentario_3;
            $muestra->comentario_4 = (trim($request->input('comentario_4')) != '') ? $request->input('comentario_4') : $muestra->comentario_4;

            $muestra->comentario_diseñador_revision = (trim($request->input('comentario_diseñador_revision')) != '') ? $request->input('comentario_diseñador_revision') : $muestra->comentario_diseñador_revision;

            // destinos
            $muestra->destinatario_1 = request('destinatario_1');
            $muestra->comuna_1 = request('comuna_1');
            $muestra->direccion_1 = request('direccion_1');
            $muestra->cantidad_1 = request('cantidad_1');
            $muestra->numero_envio_1 = request('numero_envio_1');

            $muestra->destinatario_2 = request('destinatario_2');
            $muestra->comuna_2 = request('comuna_2');
            $muestra->direccion_2 = request('direccion_2');
            $muestra->cantidad_2 = request('cantidad_2');
            $muestra->numero_envio_2 = request('numero_envio_2');

            $muestra->destinatario_3 = request('destinatario_3');
            $muestra->comuna_3 = request('comuna_3');
            $muestra->direccion_3 = request('direccion_3');
            $muestra->cantidad_3 = request('cantidad_3');
            $muestra->numero_envio_3 = request('numero_envio_3');

            $muestra->destinatario_4 = request('destinatario_4');
            $muestra->comuna_4 = request('comuna_4');
            $muestra->direccion_4 = request('direccion_4');
            $muestra->cantidad_4 = request('cantidad_4');
            $muestra->numero_envio_4 = request('numero_envio_4');
            //dd(request('sala_corte_vendedor'),request('sala_corte_diseñador'),request('sala_corte_laboratorio'));
            $muestra->sala_corte_vendedor = (trim($request->input('sala_corte_vendedor')) != '') ? request('sala_corte_vendedor') : $muestra->sala_corte_vendedor;
            $muestra->sala_corte_diseñador = (trim($request->input('sala_corte_diseñador')) != '') ? request('sala_corte_diseñador') : $muestra->sala_corte_diseñador;
            $muestra->sala_corte_laboratorio = (trim($request->input('sala_corte_laboratorio')) != '') ? request('sala_corte_laboratorio') : $muestra->sala_corte_laboratorio;
            $muestra->sala_corte_diseñador_revision = (trim($request->input('sala_corte_diseñador_revision')) != '') ? request('sala_corte_diseñador_revision') : $muestra->sala_corte_diseñador_revision;
            $muestra->sala_corte_1 = request('sala_corte_1');
            $muestra->sala_corte_2 = request('sala_corte_2');
            $muestra->sala_corte_3 = request('sala_corte_3');
            $muestra->sala_corte_4 = request('sala_corte_4');
        }
        // si se crea desde el ajax este retorna el id de las muestras para luego relacionar con la ot
        $muestras_ids = array();
        // dd($muestra);
        $muestra->save();
        $muestras_ids[] = $muestra->id;
        
        if ((isset($destinatarios) && count($destinatarios) > 1) || ($muestra->destinatario_1 != "" && ($muestra->destinatario_2 != "" || $muestra->destinatario_3 != "" || $muestra->destinatario_4 != ""))) {
            // dd($destinatarios[0]);
            unset($destinatarios[0]);
            foreach ($destinatarios as $destinatario) {
                $newMuestra = $muestra->replicate();
                $newMuestra->destinatarios_id = array($destinatario);
                $newMuestra->destinatario_2 =  "";
                $newMuestra->destinatario_3 =  "";
                $newMuestra->destinatario_4 =  "";
                $newMuestra->push();
                $muestras_ids[] = $newMuestra->id;
            }

            if (($muestra->destinatario_2 != "")) {
                $newMuestra = $muestra->replicate();
                $newMuestra->destinatarios_id = array(4);
                $newMuestra->destinatario_1 = $muestra->destinatario_2;
                $newMuestra->comuna_1 = $muestra->comuna_2;
                $newMuestra->direccion_1 = $muestra->direccion_2;
                $newMuestra->cantidad_1 = $muestra->cantidad_2;
                $newMuestra->numero_envio_1 = $muestra->numero_envio_2;
                $newMuestra->comentario_1 =  $muestra->comentario_2;
                $newMuestra->comentario_1 =  $muestra->comentario_2;
                $newMuestra->destinatario_2 =  "";
                $newMuestra->destinatario_3 =  "";
                $newMuestra->destinatario_4 =  "";
                $newMuestra->push();
                $muestras_ids[] = $newMuestra->id;

                // limpiar en la muestra original para no duplicar datos luego
                $muestra->destinatario_2 = "";
                $muestra->save();
            }
            if (($muestra->destinatario_3 != "")) {
                $newMuestra = $muestra->replicate();
                $newMuestra->destinatarios_id = array(4);
                $newMuestra->destinatario_1 = $muestra->destinatario_3;
                $newMuestra->comuna_1 = $muestra->comuna_3;
                $newMuestra->direccion_1 = $muestra->direccion_3;
                $newMuestra->cantidad_1 = $muestra->cantidad_3;
                $newMuestra->numero_envio_1 = $muestra->numero_envio_3;
                $newMuestra->comentario_1 =  $muestra->comentario_3;
                $newMuestra->destinatario_2 =  "";
                $newMuestra->destinatario_3 =  "";
                $newMuestra->destinatario_4 =  "";
                $newMuestra->push();
                $muestras_ids[] = $newMuestra->id;

                // limpiar en la muestra original para no duplicar datos luego
                $muestra->destinatario_3 = "";
                $muestra->save();
            }
            if (($muestra->destinatario_4 != "")) {
                $newMuestra = $muestra->replicate();
                $newMuestra->destinatarios_id = array(4);
                $newMuestra->destinatario_1 = $muestra->destinatario_4;
                $newMuestra->comuna_1 = $muestra->comuna_4;
                $newMuestra->direccion_1 = $muestra->direccion_4;
                $newMuestra->cantidad_1 = $muestra->cantidad_4;
                $newMuestra->numero_envio_1 = $muestra->numero_envio_4;
                $newMuestra->comentario_1 =  $muestra->comentario_3;
                $newMuestra->destinatario_2 =  "";
                $newMuestra->destinatario_3 =  "";
                $newMuestra->destinatario_4 =  "";
                $newMuestra->push();
                $muestras_ids[] = $newMuestra->id;

                // limpiar en la muestra original para no duplicar datos luego
                $muestra->destinatario_4 = "";
                $muestra->save();
            }
        }

        // dd(request()->all());
        if (request('ot_id') != null && request('ot_id') != 0 && request('fecha_corte_vendedor') != null) {
            $ot = WorkOrder::find(request('ot_id'));
            // dd($ot->vendedorAsignado);
            $user_id = isset($ot->vendedorAsignado) ? $ot->vendedorAsignado->user->id : null;
            if ($user_id != null) {

                $notificacion = new Notification();
                $notificacion->work_order_id = request('ot_id');
                $notificacion->user_id = $user_id;
                $notificacion->generador_id = auth()->user()->id;
                $notificacion->motivo = "Muestra Lista";
                $notificacion->observacion = '';
                $notificacion->save();
            }
        }


        if (request("ot_id") == 0) {
            return response()->json([$muestra, $muestras_ids]);
        }

        return redirect()->back()->with('success', 'Muestra guardada Correctamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Muestra  $muestra
     * @return \Illuminate\Http\Response
     */
    public function show(Muestra $muestra)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Muestra  $muestra
     * @return \Illuminate\Http\Response
     */
    public function edit(Muestra $muestra)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Muestra  $muestra
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Muestra $muestra)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Muestra  $muestra
     * @return \Illuminate\Http\Response
     */
    public function destroy(Muestra $muestra)
    {
        //
    }

    public function getMuestra()
    {
        // dd(request()->all());
        if (!empty($_GET['muestra_id'])) {
            // return $equipo_id;
            $muestra = Muestra::find($_GET['muestra_id']);
            return $muestra;
        }
        return "";
    }

    public function getCartonMuestra()
    {
        //$result=true;
        // dd(request()->all());
        if (!empty($_GET['id'])) {
            // return $equipo_id;
            $carton = Carton::where('id',$_GET['id'])->where('carton_muestra',1)->get();
            $result =$carton->count();
            $carton = Carton::where('id',$_GET['id'])->first();
            $cartons_muestra = Carton::where('carton_muestra',1)->pluck('codigo', 'id')->toArray();
            $html = optionsSelectArrayfilterSimple($cartons_muestra, 'id');
            //$html = armarSelectArrayCreateEditOTAux($cartons_muestra, 'carton_muestra_id', 'Cartón Muestra' , null, null ,'form-control',true,true);
           
            return response()->json(['cantidad' => $result, 'codigo' => $carton->codigo,'html'=>$html,200]);
        }
        return "";
    }

    public function delete(Request $request, $id)
    {
        // dd(request()->all(), $id);

        Muestra::find($id)->delete();

        return redirect()->back()->with('success', 'Muestra eliminada Correctamente');
    }
    public function rechazarMuestra($ot)
    {
       
        //$id = request("rechazarMuestraID");
        $now = Carbon::now();
       // if ($id) {
           // $muestra = Muestra::find($id);
            
            
            /*
            $muestra->estado = 4;
            $muestra->ultimo_cambio_estado = $now;
            $muestra->fin_sala_corte = $now;
            $diff = get_working_hours($muestra->inicio_sala_corte, $now) * 3600;
            $muestra->duracion_sala_corte=$diff;
            $muestra->cambio_rechazo_devolucion=4; 
            $muestra->save();*/

            $muestras_pendientes= Muestra::where('work_order_id',$ot)
                                         ->whereNotIn('estado',[3,4,6])
                                        ->get();           
           
            foreach ($muestras_pendientes as $muestra_pendiente) {
                               
                $muestra_pendiente->estado = 2;
                $muestra_pendiente->ultimo_cambio_estado = $now;
                $muestra_pendiente->fin_sala_corte = $now;
                $diff = get_working_hours($muestra_pendiente->inicio_sala_corte, $now) * 3600;
                $muestra_pendiente->duracion_sala_corte=$diff;
                $muestra_pendiente->cambio_rechazo_devolucion=4;
                $muestra_pendiente->save();
                 
            }
            
            /*$gestion = new Management();
            // $gestion->titulo = request('titulo');
            $gestion->observacion = "Rechazo de Muestras";
            $gestion->management_type_id = 1;
            $gestion->user_id = auth()->user()->id;
            $gestion->work_order_id = $muestra->ot->id;
            $gestion->work_space_id =  $muestra->ot->current_area_id;

            // Cambio de estado
            if (empty($muestra->ot->ultimoCambioEstado)) {
                $date = Carbon::parse($muestra->ot->ultimo_cambio_area);
            } else if ($muestra->ot->ultimo_cambio_area->gt($muestra->ot->ultimoCambioEstado->created_at)) {
                // ultimo_cambio_area at is newer than ultimoCambioEstado created at
                $date = Carbon::parse($muestra->ot->ultimo_cambio_area);
            } else {
                $date = Carbon::parse($muestra->ot->ultimoCambioEstado->created_at);
            }
            $now = Carbon::now();
            // $diff = $date->diffInSeconds($now);
            $diff = get_working_hours($date, $now) * 3600;
            // dd($diff, $diffWorkingSeconds);

            // SI AL CAMBIAR ESTADO EL ESTADO ACTUAL ES UN ESTADO 
            // Terminado = 8
            // Perdido = 9
            // Anulado = 11
            // Entregado = 13
            // Se debe guardar como tiempo de ese proceso de reactivacion 0 segundos
            if (isset($muestra->ot->ultimoCambioEstado) && in_array($muestra->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13])) {
                $diff = 0;
            }

            $gestion->duracion_segundos = $diff;
            $gestion->state_id = 12;


            // Rechazo
            $muestra->ot->ultimo_cambio_area = $now;
            $muestra->ot->current_area_id = 2;
            $muestra->ot->save();

            $gestion->motive_id =  2;
            $gestion->consulted_work_space_id =  2;

            // if ($muestra->ot->current_area_id == 1) {
            //     push_notification("Rechazo", "Se rechazo la OT " . $muestra->ot->id, $muestra->ot->id, $muestra->ot->creador->token_push_mobile);
            // }
                
            // Crear notificacion de cambio de area
            $user_id = $this->usuarioAsignadoPorArea($muestra->ot, $gestion->consulted_work_space_id);
            $motivo = "Rechazo - " . [1 => "Falta Información", 2 => "Información Erronea", 3 => "Falta Muestra Física", 4 => "Formato Imagen Inadecuado", 5 => "Medida Erronea", 6 => "Descripción de Producto", 7 => "Plano mal Acotado", 8 => "Error de Digitación", 9 => "Error tipo Sustrato", 10 => "No viable por restricciones", 11 => "Falta CAD para corte", 12 => "Falta OT Chileexpress", 13 => "Falta OT Laboratorio"][$gestion->motive_id];
            $this->crearNotificacion($muestra->ot->id, $user_id, auth()->user()->id, $motivo, $gestion->observacion);
            $gestion->save();
            */
            $muestras=Muestra::where('work_order_id',$ot)->get(); 
            
            return $muestras;
        //}
        
    }

    public function getMuestrasOt($ot)
    {
        // dd(request()->all());
        $muestras=Muestra::where('work_order_id',$ot)->get(); 
            
        return $muestras;
    }
    
    /*public function terminarMuestra_old()
    {
        $id = (request("terminarMuestraID") != null) ? request("terminarMuestraID") : request("terminarMuestraEnListadoID");
        $now = Carbon::now();
        if ($id) {
            $muestra = Muestra::find($id);
            // dd($muestra->ot->muestras->count());
            
            $muestra->estado = 3;
            $muestra->ultimo_cambio_estado = $now;
            $muestra->fin_sala_corte = $now;
            $diff = get_working_hours($muestra->inicio_sala_corte, $now) * 3600;
            $muestra->duracion_sala_corte=$diff;
            $muestra->save();

            /*if ($muestra->destinatarios_id[0] == "1") {
                // Crear notificacion de muestra lista a vendedor
                $user_id = $this->usuarioAsignadoPorArea($muestra->ot, 1);
                $this->crearNotificacion($muestra->ot->id, $user_id, auth()->user()->id, "Muestra Lista", "");
            }

            foreach ($muestra->ot->muestras as $muestra) {
                // si esta anulado no considerarlo
                if ($muestra->estado == 4) continue;
                if ($muestra->estado != 3) {

                    return redirect()->route('gestionarOt', $muestra->ot->id)->with('success', 'Muestra Terminada Correctamente');
                }
            }

            $gestion = new Management();
            // $gestion->titulo = request('titulo');
            $gestion->observacion = "Muestras Listas";
            $gestion->management_type_id = 1;
            $gestion->user_id = auth()->user()->id;
            $gestion->work_order_id = $muestra->ot->id;
            $gestion->work_space_id =  $muestra->ot->current_area_id;

            // Cambio de estado
            if (empty($muestra->ot->ultimoCambioEstado)) {
                $date = Carbon::parse($muestra->ot->ultimo_cambio_area);
            } else if ($muestra->ot->ultimo_cambio_area->gt($muestra->ot->ultimoCambioEstado->created_at)) {
                // ultimo_cambio_area at is newer than ultimoCambioEstado created at
                $date = Carbon::parse($muestra->ot->ultimo_cambio_area);
            } else {
                $date = Carbon::parse($muestra->ot->ultimoCambioEstado->created_at);
            }
            $now = Carbon::now();
            // $diff = $date->diffInSeconds($now);
            $diff = get_working_hours($date, $now) * 3600;
            // dd($diff, $diffWorkingSeconds);

            // SI AL CAMBIAR ESTADO EL ESTADO ACTUAL ES UN ESTADO 
            // Terminado = 8
            // Perdido = 9
            // Anulado = 11
            // Entregado = 13
            // Se debe guardar como tiempo de ese proceso de reactivacion 0 segundos
            if (isset($muestra->ot->ultimoCambioEstado) && in_array($muestra->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13])) {
                $diff = 0;
            }
            $gestion->duracion_segundos = $diff;
            $gestion->state_id = 18;

            // Cambiar a Muestras listas = Desarrollo

            $muestra->ot->ultimo_cambio_area = $now;
            $muestra->ot->current_area_id = 2;
            $muestra->ot->save();
            // Crear notificacion de cambio de area
            $user_id = $this->usuarioAsignadoPorArea($muestra->ot, 2); // 2 = desarrollo
            $this->crearNotificacion($muestra->ot->id, $user_id, auth()->user()->id, "Muestras Listas", $gestion->observacion);


            // dd(request()->all());
            $gestion->save();



            return redirect()->route('gestionarOt', $muestra->ot->id)->with('success', 'Muestra Terminada Correctamente');


            // return redirect()->route('gestionarOt', $id)->with('success', 'Muestra Aprobada Correctamente');;
        }
        return "";
    }*/

    public function terminarMuestra()
    {
        $id = (request("terminarMuestraID") != null) ? request("terminarMuestraID") : request("terminarMuestraEnListadoID");
        $now = Carbon::now();
        if ($id) {
            $muestra = Muestra::find($id);
            // dd($muestra->ot->muestras->count());
            $muestra->estado = 3;
            $muestra->ultimo_cambio_estado = $now;
            $muestra->fin_sala_corte = $now;
            $diff = get_working_hours($muestra->inicio_sala_corte, $now) * 3600;
            $muestra->duracion_sala_corte=$diff;
            $muestra->save();

            if ($muestra->destinatarios_id[0] == "1") {
                // Crear notificacion de muestra lista a vendedor
                $user_id = $this->usuarioAsignadoPorArea($muestra->ot, 1);
                $this->crearNotificacion($muestra->ot->id, $user_id, auth()->user()->id, "Muestra Lista", "");
            }

            return redirect()->route('gestionarOt', $muestra->ot->id)->with('success', 'Muestra Terminada Correctamente');
        }
        /*
            foreach ($muestra->ot->muestras as $muestra) {
                // si esta anulado no considerarlo
                if ($muestra->estado == 4) continue;
                if ($muestra->estado != 3) {

                    return redirect()->route('gestionarOt', $muestra->ot->id)->with('success', 'Muestra Terminada Correctamente');
                }
            }

            

            $gestion = new Management();
            // $gestion->titulo = request('titulo');
            $gestion->observacion = "Muestras Listas";
            $gestion->management_type_id = 1;
            $gestion->user_id = auth()->user()->id;
            $gestion->work_order_id = $muestra->ot->id;
            $gestion->work_space_id =  $muestra->ot->current_area_id;

            // Cambio de estado
            if (empty($muestra->ot->ultimoCambioEstado)) {
                $date = Carbon::parse($muestra->ot->ultimo_cambio_area);
            } else if ($muestra->ot->ultimo_cambio_area->gt($muestra->ot->ultimoCambioEstado->created_at)) {
                // ultimo_cambio_area at is newer than ultimoCambioEstado created at
                $date = Carbon::parse($muestra->ot->ultimo_cambio_area);
            } else {
                $date = Carbon::parse($muestra->ot->ultimoCambioEstado->created_at);
            }
            //$now = Carbon::now();
            // $diff = $date->diffInSeconds($now);
            $diff = get_working_hours($date, $now) * 3600;
            // dd($diff, $diffWorkingSeconds);

            // SI AL CAMBIAR ESTADO EL ESTADO ACTUAL ES UN ESTADO 
            // Terminado = 8
            // Perdido = 9
            // Anulado = 11
            // Entregado = 13
            // Se debe guardar como tiempo de ese proceso de reactivacion 0 segundos
            if (isset($muestra->ot->ultimoCambioEstado) && in_array($muestra->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13])) {
                $diff = 0;
            }
            $gestion->duracion_segundos = $diff;
            $gestion->state_id = 18;
            $gestion->save();
            // Cambiar a Muestras listas = Desarrollo

            $muestra->ot->ultimo_cambio_area = $now;
            $muestra->ot->current_area_id = 2;
            $muestra->ot->save();
            // Crear notificacion de cambio de area
            $user_id = $this->usuarioAsignadoPorArea($muestra->ot, 2); // 2 = desarrollo
            $this->crearNotificacion($muestra->ot->id, $user_id, auth()->user()->id, "Muestras Listas", $gestion->observacion);


            // dd(request()->all());
            

            return redirect()->route('gestionarOt', $muestra->ot->id)->with('success', 'Muestras Terminadas Correctamente');


            // return redirect()->route('gestionarOt', $id)->with('success', 'Muestra Aprobada Correctamente');;
        }*/
        return "";
    }

    public function anularMuestra()
    {
        // dd(request()->all());
        $id = request("anularMuestraID");
        if ($id) {
            $muestra = Muestra::find($id);
            // dd($muestra->ot->muestras->count());
            $muestra->estado = 4;
            $muestra->ultimo_cambio_estado = Carbon::now();
            $muestra->save();

            return redirect()->route('gestionarOt', $muestra->ot->id)->with('success', 'Muestras Anulada Correctamente');
        }
        return "";
    }

    public function devolverMuestra()
    {
        // dd(request()->all());
        $now = Carbon::now();
        $id = request("devolverMuestraID");
        if ($id) {
            $muestra = Muestra::find($id);
            // dd($muestra->ot->muestras->count());
            $muestra->estado = 5;
            $muestra->ultimo_cambio_estado = $now;
            $muestra->fin_sala_corte = $now;
            $diff = get_working_hours($muestra->inicio_sala_corte, $now) * 3600;
            $muestra->duracion_sala_corte=$diff;
            $muestra->save();

            

            return redirect()->route('gestionarOt', $muestra->ot->id)->with('success', 'Muestras Devuelta Correctamente');
        }
        return "";
    }

    public function usuarioAsignadoPorArea($ot, $area)
    {
        switch ($area) {
            case '1':
                $user_id = isset($ot->vendedorAsignado) ? $ot->vendedorAsignado->user->id : null;
                break;
            case '2':
                $user_id = isset($ot->ingenieroAsignado) ? $ot->ingenieroAsignado->user->id : null;
                break;
            case '3':
                $user_id = isset($ot->diseñadorAsignado) ? $ot->diseñadorAsignado->user->id : null;
                break;
            case '4':
                $user_id = isset($ot->catalogadorAsignado) ? $ot->catalogadorAsignado->user->id : null;
                break;
            case '5':
                $user_id = isset($ot->catalogadorAsignado) ? $ot->catalogadorAsignado->user->id : null;
                break;
            case '6':
                $user_id = isset($ot->tecnicoMuestrasAsignado) ? $ot->tecnicoMuestrasAsignado->user->id : null;
                break;

            default:
                # code...
                break;
        }
        return $user_id;
    }

    public function crearNotificacion($idOt, $user_id, $generador_id, $motivo, $observacion)
    {
        // Si el user es null el area no esta asignada asi que no generamos notificacion
        if ($user_id == null) {
            return;
        }
        $notificacion = new Notification();
        $notificacion->work_order_id = $idOt;
        $notificacion->user_id = $user_id;
        $notificacion->generador_id = $generador_id;
        $notificacion->motivo = $motivo;
        $notificacion->observacion = strlen($observacion) > 180 ? mb_substr($observacion, 0, 180) . "..." : $observacion;
        return $notificacion->save();
    }

    public function muestraPrioritaria($id)
    {
        Muestra::findOrFail($id)->update(['prioritaria' => 1]);
        return redirect()->back()->with('success', 'Muestra marcada como prioritaria correctamente.');
    }

    public function muestraNoPrioritaria($id)
    {
        Muestra::findOrFail($id)->update(['prioritaria' => 0]);
        return redirect()->back()->with('success', 'Muestra marcada como no prioritaria correctamente.');
    }

    public function generar_etiqueta_muestra_pdf(Request $request)
    {
        // dd(request()->all());
        //$muestra = Muestra::find(request("id"));
        $muestra = Muestra::find(request("id"));
        //dd($muestra);
        $tipo = (request("tipo"));
        // dd($muestra);
        view()->share('muestra', $muestra);
        view()->share('tipo', $tipo);
        // return view('pdf.etiqueta_muestra');
        if ($request->has('download')) {
            
            $pdf = PDF::setOptions(['isPhpEnabled'=> true,'isRemoteEnabled' => true, 'enable_remote' => true,'isHtml5ParserEnabled' => true])->loadView('pdf.etiqueta_muestra');
            //$pdf->setWatermarkImage(public_path('img/logo-cmpc.gif'));
            return $pdf->stream('Etiqueta Info Producto N° ' . request("id") . ' ' . Carbon::now() . ' .pdf');
        }
        return view('pdf.etiqueta_muestra');
    }
}
