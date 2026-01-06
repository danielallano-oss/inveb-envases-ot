<?php

namespace App\Http\Controllers;

use App\Answer;
use App\BitacoraCamposModificados;
use App\BitacoraWorkOrder;
use App\Cad;
use App\Carton;
use App\CiudadesFlete;
use App\CodigoMaterial;
use App\Constants;
use App\File;
use App\Management;
use App\ManagementType;
use App\Material;
use App\MaquilaServicio;
use App\Notification;
use App\PrefijoMaterial;
use App\Process;
use App\States;
use App\SufijoMaterial;
use App\User;
use App\UserWorkOrder;
use App\WorkOrder;
use App\WorkSpace;
use App\SalaCorte;
use App\Proveedor;
use App\Muestra;
use App\IndicacionEspecial;
use App\Color;
use Carbon\Carbon;
use Excel;
use PDF;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\DB;

class ManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function gestionarOt($id)
    {
        if (Auth()->user()->isAdmin()) {
            return redirect()->back();
        }
        $pendiente_recepcion_externo = false;
        $ot = WorkOrder::with('gestiones.user.role', 'gestiones.respuesta.user', 'gestiones.state', 'gestiones.area', 'gestiones.area_consultada', 'gestiones.files', 'gestiones.type', 'files', 'users')->find($id);
        // dd($ot->id, UserWorkOrder::where('work_order_id', $ot->id)->where("user_id", auth()->user()->id)->first());
        $usuarioAsignado = UserWorkOrder::where('work_order_id', $ot->id)->where("user_id", auth()->user()->id)->first() ? true : false;
        // dd($usuarioAsignado);
        //dd($ot);
        $files_by_area = DB::table('managements')
            ->join('files', 'managements.id', '=', 'files.management_id')
            ->join('users', 'managements.user_id', '=', 'users.id')
            ->where('managements.work_order_id', '=', $id)
            ->select(
                'files.*',
                'managements.work_space_id',
                'users.role_id'
            )
            ->get()->toArray();

        $states_by_area = [];
        if (isset(auth()->user()->role->area)) {

            // Estados segun area:
            if (auth()->user()->role->area->id == Constants::AreaVenta) {
                // dd($ot->gestiones);
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
                    if ($ot->tipo_solicitud == 6) {
                        $states_by_area = [2, 10, 11, 15, 8];
                    } else {
                        //Cambio 24-02: /03 Poner alguna alerta a Diseño Grafico,
                        //por ejemplo que no pueda pasar a VB de cliente si no hay registro de recepción de diseño externo.
                        //( Si hubo envío, debe haber recepción)

                        //Se valida si la OT tiene envio a diseñador externo sin recepcion
                        $disenador_externo_pendiente = Management::where('work_order_id', $ot->id)
                            ->where('management_type_id', 9)
                            ->where('recibido_diseño_externo', 0)
                            ->orderBy('id', 'DESC')
                            ->first();
                        if ($disenador_externo_pendiente) {
                            $pendiente_recepcion_externo = true;
                            $states_by_area = [2, 5, 6, 7, 9, 10, 11, 14, 15, 20, 21];
                        } else {
                            $states_by_area = [2, 5, 6, 7, 9, 10, 11, 14, 15, 16, 20, 21];
                        }
                    }
                } else {
                    if ($ot->tipo_solicitud == 6) {
                        $states_by_area = [2, 10, 11, 15, 8];
                    } else {
                        //Cambio 24-02: /03 Poner alguna alerta a Diseño Grafico,
                        //por ejemplo que no pueda pasar a VB de cliente si no hay registro de recepción de diseño externo.
                        //( Si hubo envío, debe haber recepción)

                        //Se valida si la OT tiene envio a diseñador externo sin recepcion
                        $disenador_externo_pendiente = Management::where('work_order_id', $ot->id)
                            ->where('management_type_id', 9)
                            ->where('recibido_diseño_externo', 0)
                            ->orderBy('id', 'DESC')
                            ->first();
                        if ($disenador_externo_pendiente) {
                            $pendiente_recepcion_externo = true;
                            $states_by_area = [2, 9, 10, 11, 14, 15, 20, 21];
                        } else {
                            $states_by_area = [2, 9, 10, 11, 14, 15, 16, 20, 21];
                        }
                    }
                }
                // Si es arte con Material y no ha sido enviado a diseño solo se puede enviar a ( ya no diseño grafico ) diseño estructural
                if ($ot->tipo_solicitud == 5 && $enviadoADiseño) {
                    //Cambio 24-02: /03 Poner alguna alerta a Diseño Grafico,
                    //por ejemplo que no pueda pasar a VB de cliente si no hay registro de recepción de diseño externo.
                    //( Si hubo envío, debe haber recepción)

                    //Se valida si la OT tiene envio a diseñador externo sin recepcion
                    $disenador_externo_pendiente = Management::where('work_order_id', $ot->id)
                        ->where('management_type_id', 9)
                        ->where('recibido_diseño_externo', 0)
                        ->orderBy('id', 'DESC')
                        ->first();
                    if ($disenador_externo_pendiente) {
                        $pendiente_recepcion_externo = true;
                        $states_by_area = [2, 5, 7, 9, 10, 11, 14, 15];
                    } else {
                        $states_by_area = [2, 5, 7, 9, 10, 11, 14, 15, 16];
                    }
                } elseif ($ot->tipo_solicitud == 5 && !$enviadoADiseño) {
                    //Cambio 24-02: /03 Poner alguna alerta a Diseño Grafico,
                    //por ejemplo que no pueda pasar a VB de cliente si no hay registro de recepción de diseño externo.
                    //( Si hubo envío, debe haber recepción)

                    //Se valida si la OT tiene envio a diseñador externo sin recepcion
                    $disenador_externo_pendiente = Management::where('work_order_id', $ot->id)
                        ->where('management_type_id', 9)
                        ->where('recibido_diseño_externo', 0)
                        ->orderBy('id', 'DESC')
                        ->first();
                    if ($disenador_externo_pendiente) {
                        $pendiente_recepcion_externo = true;
                        $states_by_area = [2, 9, 10, 11, 14, 15];
                    } else {
                        $states_by_area = [2, 9, 10, 11, 14, 15, 16];
                    }
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
            } else if (auth()->user()->role->area->id == Constants::AreaDesarrollo) {
                if ($ot->tipo_solicitud == 6) {
                    if ($ot->ajuste_area_desarrollo == 3) {
                        $states_by_area = [1, 3, 12, 13];
                    } else {
                        $states_by_area = [1, 3, 17, 12, 13];
                    }
                } else {
                    //Cambio 24-02: /03 Poner alguna alerta a Diseño Grafico,
                    //por ejemplo que no pueda pasar a VB de cliente si no hay registro de recepción de diseño externo.
                    //( Si hubo envío, debe haber recepción)

                    //Se valida si la OT tiene envio a diseñador externo sin recepcion
                    $disenador_externo_pendiente = Management::where('work_order_id', $ot->id)
                        ->where('management_type_id', 9)
                        ->where('recibido_diseño_externo', 0)
                        ->orderBy('id', 'DESC')
                        ->first();
                    if ($disenador_externo_pendiente) {
                        $pendiente_recepcion_externo = true;
                        $states_by_area = [1, 3, 5, 6, 7, 12, 17];
                    } else {
                        $states_by_area = [1, 3, 5, 6, 7, 12, 16, 17];
                    }

                    // Si llega a estar en estado muestra o laboratorio debemos agregar el estado de regresar a Desarrollo
                    if (!empty($ot->ultimoCambioEstado) && ($ot->ultimoCambioEstado->state_id == 3 || $ot->ultimoCambioEstado->state_id == 4)) {
                        if ($ot->ultimoCambioEstado->state_id == 3) {
                            array_splice($states_by_area, 1, 1);
                        } else if ($ot->ultimoCambioEstado->state_id == 4) {
                            array_splice($states_by_area, 2, 1);
                        }
                        $states_by_area[] = 2;
                    }

                    // si el tipo de solicitud de la OT es distinto a Desarrollo completo agregamos estado de "ENTREGADO" = 13
                    if ($ot->tipo_solicitud != 1) {
                        $states_by_area[] = 13;
                    }
                }

                // dd($states_by_area);
            } else if (auth()->user()->role->area->id == Constants::AreaDiseño) {

                //Cambio 24-02: /03 Poner alguna alerta a Diseño Grafico,
                //por ejemplo que no pueda pasar a VB de cliente si no hay registro de recepción de diseño externo.
                //( Si hubo envío, debe haber recepción)

                //Se valida si la OT tiene envio a diseñador externo sin recepcion
                $disenador_externo_pendiente = Management::where('work_order_id', $ot->id)
                    ->where('management_type_id', 9)
                    ->where('recibido_diseño_externo', 0)
                    ->orderBy('id', 'DESC')
                    ->first();
                if ($disenador_externo_pendiente) {
                    $pendiente_recepcion_externo = true;
                    $states_by_area = [1, 2, 7, 12];
                } else {
                    $states_by_area = [1, 2, 7, 12, 16];
                }

                // si el tipo de solicitud de la OT es distinto a Desarrollo completo agregamos estado de "ENTREGADO" = 13
                if ($ot->tipo_solicitud != 1) {
                    $states_by_area[] = 13;
                }
            } else if (auth()->user()->role->area->id == Constants::AreaCatalogacion) {
                // si el usuario es catalogador, ver si la ot esta en el area de Catalogacion o precatalogacion
                if ($ot->current_area_id == 4) {
                    $states_by_area = [1, 2, 5, 8, 12];
                } else if ($ot->current_area_id == 5) {
                    $states_by_area = [1, 2, 5, 7, 12];
                }
            } else if (auth()->user()->role->area->id == Constants::AreaMuestras) {
                //$muestras=Muestra::where('work_order_id',$ot->id)->where('estado',2)->get();

                //if($muestras->count()>0){
                $states_by_area = [12, 18, 22];
                /*}else{
                    $states_by_area = [2, 12, 18, 22];
                }*/
            }
            // dd($states_by_area);
        } else {
            // Para gerente y admin
            $states_by_area = [];
        }

        $motivos = [1 => "Falta Información", 2 => "Información Erronea", 3 => "Falta Muestra Física", 4 => "Formato Imagen Inadecuado", 5 => "Medida Erronea", 6 => "Descripción de Producto", 7 => "Plano mal Acotado", 8 => "Error de Digitación", 9 => "Error tipo Sustrato", 10 => "No viable por restricciones", 11 => "Falta CAD para corte", 12 => "Falta OT Chileexpress", 13 => "Falta OT Laboratorio"];
        $states = States::whereIn('id', $states_by_area)->pluck('nombre', 'id')->toArray();
        if (isset(auth()->user()->role->area)) {

            // $workSpaces = '';

            // dd($ot->current_area_id);

            if ($ot->tipo_solicitud == 6) {
                $workSpaces = WorkSpace::where('id', '!=', auth()->user()->role->area->id)->where('status', '=', 'active')->pluck('nombre', 'id')->toArray();

                if ($ot->current_area_id == 1) {

                    $workSpaces = WorkSpace::where('id', 2)->where('status', '=', 'active')->pluck('nombre', 'id')->toArray();
                    // dd(1);
                } elseif ($ot->current_area_id == 2) {
                    $workSpaces = WorkSpace::where('id', 1)->where('status', '=', 'active')->pluck('nombre', 'id')->toArray();
                    // dd(2);
                }
                //  else if ($ot->current_area_id == 6) {
                //     $workSpaces = WorkSpace::where('id', 6)->where('status', '=', 'active')->pluck('nombre', 'id')->toArray();
                //     // dd(3);
                // }
            } else {
                $workSpaces = WorkSpace::where('id', '!=', auth()->user()->role->area->id)->where('status', '=', 'active')->pluck('nombre', 'id')->toArray();
                if ($ot->current_area_id == 4) {
                    // dd(4);
                    $workSpaces = WorkSpace::where('id', '!=', 4)->where('status', '=', 'active')->pluck('nombre', 'id')->toArray();
                } else if ($ot->current_area_id == 5) {
                    $workSpaces = WorkSpace::where('id', '!=', 5)->where('status', '=', 'active')->pluck('nombre', 'id')->toArray();
                    // dd(5);
                }
            }







            if ($ot->current_area_id == auth()->user()->role->area->id || (auth()->user()->role->area->id == 4 && ($ot->current_area_id == 4 || $ot->current_area_id == 5))) {

                if ($ot->current_area_id == auth()->user()->role->area->id && $ot->current_area_id == 2) {
                    if ($ot->tipo_solicitud == 6) {
                        $managementTypes = ManagementType::whereIn('id', [1, 2, 3])->pluck('nombre', 'id')->toArray();
                    } else {
                        $managementTypes = ManagementType::whereIn('id', [1, 2, 3, 6])->pluck('nombre', 'id')->toArray();
                    }
                } else {

                    $muestraspendientes = Muestra::where('estado', 2)->where('work_order_id', $ot->id)->get();
                    if ($ot->current_area_id == 6 && $muestraspendientes->count() > 0) {
                        //Validar Si la OT realizo una gestion de Envio de Diseño Pdf a Proveedor
                        $gestiones = Management::where('work_order_id', $id)->where('management_type_id', 9)->where('recibido_diseño_externo', 0)->get();
                        if (count($gestiones) > 0) {
                            if (auth()->user()->isJefeDiseño() || auth()->user()->isDiseñador()) {
                                $managementTypes = ManagementType::whereIn('id', [1, 2, 3, 8, 10, 11])->pluck('nombre', 'id')->toArray();
                            } else {
                                $managementTypes = ManagementType::whereIn('id', [1, 2, 3, 8])->pluck('nombre', 'id')->toArray();
                            }
                        } else {
                            if (auth()->user()->isJefeDiseño() || auth()->user()->isDiseñador()) {
                                $managementTypes = ManagementType::whereIn('id', [1, 2, 3, 8, 9, 11])->pluck('nombre', 'id')->toArray();
                            } else {
                                $managementTypes = ManagementType::whereIn('id', [1, 2, 3, 8])->pluck('nombre', 'id')->toArray();
                            }
                        }
                    } else {
                        //Validar Si la OT realizo una gestion de Envio de Diseño Pdf a Proveedor
                        $gestiones = Management::where('work_order_id', $id)->where('management_type_id', 9)->where('recibido_diseño_externo', 0)->get();
                        if (count($gestiones) > 0) {
                            if (auth()->user()->isJefeDiseño() || auth()->user()->isDiseñador()) {
                                $managementTypes = ManagementType::whereIn('id', [1, 2, 3, 8, 10, 11])->pluck('nombre', 'id')->toArray();
                            } else {
                                $managementTypes = ManagementType::whereIn('id', [1, 2, 3, 8])->pluck('nombre', 'id')->toArray();
                            }
                        } else {
                            if (auth()->user()->isJefeDiseño() || auth()->user()->isDiseñador()) {
                                $managementTypes = ManagementType::whereIn('id', [1, 2, 3, 8, 9, 11])->pluck('nombre', 'id')->toArray();
                            } else {
                                if ($ot->tipo_solicitud == 6) {
                                    $managementTypes = ManagementType::whereIn('id', [1, 2, 3])->pluck('nombre', 'id')->toArray();
                                } else {
                                    $managementTypes = ManagementType::whereIn('id', [1, 2, 3, 8])->pluck('nombre', 'id')->toArray();
                                }
                            }
                        }
                    }
                }
                //Cuando sea area de venta puede y la OT este en estado de Hibernacion o cotizacion puede cambiar la OT a cualquier otro estado
            } else if (auth()->user()->role->area->id == 1 && ($ot->current_area_id == 8 || $ot->current_area_id == 9)) {
                $managementTypes = ManagementType::whereIn('id', [1, 2, 3])->pluck('nombre', 'id')->toArray();
            } else {

                $managementTypes = ManagementType::whereIn('id', [2, 3])->pluck('nombre', 'id')->toArray();
            }
        } else {
            // Para gerente y admin
            // dd(6);
            $workSpaces = [];
            $managementTypes = [];
        }

        // dd($workSpaces);
        // Detallar areas para usuario Catalogador segun area OT

        $proximoCodigoMaterial = CodigoMaterial::where("active", 1)->first();
        $cads = Cad::where('active', 1)->pluck('cad', 'id')->toArray();
        // dd($proximoCodigoMaterial);
        // dd($ot->gestiones);


        // TODAS LAS NOTIFICACIONES PENDIENTES DEL USUARIO DEBEN INACTIVARSE
        $notificaciones = Notification::where("user_id", auth()->user()->id)->where("active", 1)->where("work_order_id", $id);
        $notificaciones->update(array("active" => 0));
        // dd($notificaciones);
        $prefijosSimple = [];
        $prefijos = [];
        $sufijos = [];
        if (isset(auth()->user()->role->area->id) && auth()->user()->role->area->id == Constants::AreaCatalogacion) {
            $prefijos = PrefijoMaterial::get();
            $sufijos = SufijoMaterial::pluck('codigo', 'id')->toArray();
            $sufijos = array_combine($sufijos, $sufijos);
            $prefijosSimple = PrefijoMaterial::pluck('codigo', 'id')->toArray();
            $prefijosSimple = array_combine($prefijosSimple, $prefijosSimple);
        }

        // Si es desarrollo completo o arte con Material, esta en catalogacion y ya se creo codigo sap final enviar materiales adicionales
        if (($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 5 || $ot->tipo_solicitud == 7) && $ot->current_area_id == 4 && $ot->codigo_sap_final == 1) {
            $ot->materiales_adicionales = Material::where("work_order_id", $ot->id)->where("id", "!=", $ot->material->id)->get();
            // dd($ot->materiales_adicionales);
        }

        // DATOS PARA CREACION DE MUESTRAS

        $cartons = Carton::where('active', 1)->pluck('codigo', 'id')->toArray();
        $cartons_muestra = Carton::where('carton_muestra', 1)->where('active', 1)->pluck('codigo', 'id')->toArray();
        $comunas = CiudadesFlete::pluck('ciudad', 'id')->toArray();
        $procesos = Process::where('active', 1)->orderBy("descripcion")->pluck('descripcion', 'id')->toArray();

        //Verificamos si ya la OT ha pasado por proceso de precatalogacion (para verificar el editar)
        $validation_edition = self::validation_edition($ot);
        $extension = pathinfo($ot->ant_des_correo_cliente_file, PATHINFO_EXTENSION);
        //dd($extension);
        $files_develop_ventas = [];
        $files_ventas_correo = [];
        $files_ventas_plano = [];
        $files_ventas_boceto = [];
        $files_ventas_spec = [];
        $files_ventas_otro = [];
        $files_ventas_vb_muestra = [];
        $files_ventas_vb_boce = [];
        $files_ventas_oc = [];

        if (!is_null($ot->ant_des_correo_cliente_file) && $ot->ant_des_correo_cliente == 1) {
            if (file_exists(public_path($ot->ant_des_correo_cliente_file))) {
                $url = $ot->ant_des_correo_cliente_file;
                $ext = pathinfo($url, PATHINFO_EXTENSION);
                $size = $this->human_filesize(filesize(public_path($url)));
                $peso = $size[0];
                $unidad = $size[1];
                $tipo_archivo = $this->tipo_archivo($ext);
                //$files_ventas_correo=['url'=>$url,'ext'=>$ext,'tipo'=>$tipo_archivo,'peso'=>$peso,'unidad'=>$unidad,'created_at'=>$ot->created_at];
                if (!is_null($ot->ant_des_correo_cliente_file_date)) {
                    $files_ventas_correo = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->ant_des_correo_cliente_file_date];
                } else {
                    $files_ventas_correo = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->created_at];
                }
                $files_develop_ventas[] = $files_ventas_correo;
            }
        }

        if (!is_null($ot->ant_des_plano_actual_file) && $ot->ant_des_plano_actual == 1) {
            if (file_exists(public_path($ot->ant_des_plano_actual_file))) {
                $url = $ot->ant_des_plano_actual_file;
                $ext = pathinfo($url, PATHINFO_EXTENSION);
                $size = $this->human_filesize(filesize(public_path($url)));
                $peso = $size[0];
                $unidad = $size[1];
                $tipo_archivo = $this->tipo_archivo($ext);
                //$files_ventas_plano=['url'=>$url,'ext'=>$ext,'tipo'=>$tipo_archivo,'peso'=>$peso,'unidad'=>$unidad,'created_at'=>$ot->updated_at];
                if (!is_null($ot->ant_des_plano_actual_file_date)) {
                    $files_ventas_plano = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->ant_des_plano_actual_file_date];
                } else {
                    $files_ventas_plano = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->created_at];
                }
                $files_develop_ventas[] = $files_ventas_plano;
            }
        }

        if (!is_null($ot->ant_des_boceto_actual_file) && $ot->ant_des_boceto_actual == 1) {
            if (file_exists(public_path($ot->ant_des_boceto_actual_file))) {
                $url = $ot->ant_des_boceto_actual_file;
                $ext = pathinfo($url, PATHINFO_EXTENSION);
                $size = $this->human_filesize(filesize(public_path($url)));
                $peso = $size[0];
                $unidad = $size[1];
                $tipo_archivo = $this->tipo_archivo($ext);
                // $files_ventas_boceto=['url'=>$url,'ext'=>$ext,'tipo'=>$tipo_archivo,'peso'=>$peso,'unidad'=>$unidad,'created_at'=>$ot->updated_at];
                if (!is_null($ot->ant_des_boceto_actual_file_date)) {
                    $files_ventas_boceto = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->ant_des_boceto_actual_file_date];
                } else {
                    $files_ventas_boceto = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->created_at];
                }
                $files_develop_ventas[] = $files_ventas_boceto;
            }
        }

        if (!is_null($ot->ant_des_speed_file) && $ot->ant_des_speed == 1) {
            if (file_exists(public_path($ot->ant_des_speed_file))) {
                $url = $ot->ant_des_speed_file;
                $ext = pathinfo($url, PATHINFO_EXTENSION);
                $size = $this->human_filesize(filesize(public_path($url)));
                $peso = $size[0];
                $unidad = $size[1];
                $tipo_archivo = $this->tipo_archivo($ext);
                //$files_ventas_spec=['url'=>$url,'ext'=>$ext,'tipo'=>$tipo_archivo,'peso'=>$peso,'unidad'=>$unidad,'created_at'=>$ot->updated_at];
                if (!is_null($ot->ant_des_speed_file_date)) {
                    $files_ventas_spec = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->ant_des_speed_file_date];
                } else {
                    $files_ventas_spec = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->created_at];
                }
                $files_develop_ventas[] = $files_ventas_spec;
            }
        }

        if (!is_null($ot->ant_des_otro_file) && $ot->ant_des_otro == 1) {
            if (file_exists(public_path($ot->ant_des_otro_file))) {
                $url = $ot->ant_des_otro_file;
                $ext = pathinfo($url, PATHINFO_EXTENSION);
                $size = $this->human_filesize(filesize(public_path($url)));
                $peso = $size[0];
                $unidad = $size[1];
                $tipo_archivo = $this->tipo_archivo($ext);
                //$files_ventas_otro=['url'=>$url,'ext'=>$ext,'tipo'=>$tipo_archivo,'peso'=>$peso,'unidad'=>$unidad,'created_at'=>$ot->updated_at];
                if (!is_null($ot->ant_des_otro_file_date)) {
                    $files_ventas_otro = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->ant_des_otro_file_date];
                } else {
                    $files_ventas_otro = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->created_at];
                }
                $files_develop_ventas[] = $files_ventas_otro;
            }
        }

        ////Nuevo Evolutivo 24-07
        if (!is_null($ot->oc_file)) {
            if (file_exists(public_path($ot->oc_file))) {
                $url = $ot->oc_file;
                $ext = pathinfo($url, PATHINFO_EXTENSION);
                $size = $this->human_filesize(filesize(public_path($url)));
                $peso = $size[0];
                $unidad = $size[1];
                $tipo_archivo = $this->tipo_archivo($ext);
                //$files_ventas_otro=['url'=>$url,'ext'=>$ext,'tipo'=>$tipo_archivo,'peso'=>$peso,'unidad'=>$unidad,'created_at'=>$ot->updated_at];
                if (!is_null($ot->oc_file_date)) {
                    $files_ventas_oc = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->oc_file_date];
                } else {
                    $files_ventas_oc = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->created_at];
                }
                $files_develop_ventas[] = $files_ventas_oc;
            }
        }
        ////

        //EVOLUTIVO NOV-DICIEMBRE

        if (!is_null($ot->ant_des_vb_muestra_file) && $ot->ant_des_vb_muestra == 1) {
            if (file_exists(public_path($ot->ant_des_vb_muestra_file))) {
                $url = $ot->ant_des_vb_muestra_file;
                $ext = pathinfo($url, PATHINFO_EXTENSION);
                $size = $this->human_filesize(filesize(public_path($url)));
                $peso = $size[0];
                $unidad = $size[1];
                $tipo_archivo = $this->tipo_archivo($ext);
                if (!is_null($ot->ant_des_vb_muestra_file)) {
                    $files_ventas_vb_muestra = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->ant_des_vb_muestra_file_date];
                } else {
                    $files_ventas_vb_muestra = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->created_at];
                }
                $files_develop_ventas[] = $files_ventas_vb_muestra;
            }
        }

        if (!is_null($ot->ant_des_vb_boce_file) && $ot->ant_des_vb_boce == 1) {
            if (file_exists(public_path($ot->ant_des_vb_boce_file))) {
                $url = $ot->ant_des_vb_boce_file;
                $ext = pathinfo($url, PATHINFO_EXTENSION);
                $size = $this->human_filesize(filesize(public_path($url)));
                $peso = $size[0];
                $unidad = $size[1];
                $tipo_archivo = $this->tipo_archivo($ext);
                if (!is_null($ot->ant_des_vb_boce_file)) {
                    $files_ventas_vb_boce = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->ant_des_vb_boce_file_date];
                } else {
                    $files_ventas_vb_boce = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->created_at];
                }
                $files_develop_ventas[] = $files_ventas_vb_boce;
            }
        }


        //////


        if ($ot->tipo_solicitud == 6) {
            if ($ot->ajuste_area_desarrollo == 1) {
                if (!is_null($ot->licitacion_file)) {
                    if (file_exists(public_path($ot->licitacion_file))) {
                        $url = $ot->licitacion_file;
                        $ext = pathinfo($url, PATHINFO_EXTENSION);
                        $size = $this->human_filesize(filesize(public_path($url)));
                        $peso = $size[0];
                        $unidad = $size[1];
                        $tipo_archivo = $this->tipo_archivo($ext);
                        $files_ventas_correo = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->created_at];
                        $files_develop_ventas[] = $files_ventas_correo;
                    }
                }
            } elseif ($ot->ajuste_area_desarrollo == 2) {
                if (!is_null($ot->ficha_tecnica_file)) {
                    if (file_exists(public_path($ot->ficha_tecnica_file))) {
                        $url = $ot->ficha_tecnica_file;
                        $ext = pathinfo($url, PATHINFO_EXTENSION);
                        $size = $this->human_filesize(filesize(public_path($url)));
                        $peso = $size[0];
                        $unidad = $size[1];
                        $tipo_archivo = $this->tipo_archivo($ext);
                        $files_ventas_correo = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->created_at];
                        $files_develop_ventas[] = $files_ventas_correo;
                    }
                }
            } elseif ($ot->ajuste_area_desarrollo == 3) {
                if (!is_null($ot->estudio_file)) {
                    if (file_exists(public_path($ot->estudio_file))) {
                        $url = $ot->estudio_file;
                        $ext = pathinfo($url, PATHINFO_EXTENSION);
                        $size = $this->human_filesize(filesize(public_path($url)));
                        $peso = $size[0];
                        $unidad = $size[1];
                        $tipo_archivo = $this->tipo_archivo($ext);
                        $files_ventas_correo = ['url' => $url, 'ext' => $ext, 'tipo' => $tipo_archivo, 'peso' => $peso, 'unidad' => $unidad, 'created_at' => $ot->created_at];
                        $files_develop_ventas[] = $files_ventas_correo;
                    }
                }
            }
        }


        $salas_cortes = SalaCorte::where('deleted', 0)->pluck('nombre', 'id')->toArray();
        $muestras = Muestra::where('work_order_id', $ot->id)->pluck('id', 'id')->toArray();
        $proveedores = Proveedor::where('deleted', 0)->pluck('name', 'id')->toArray();
        $indicaciones_especiales = IndicacionEspecial::where('client_id', $ot->client_id)->where('deleted', 0)->get();

        //Verificacion si OT envio a Diseñador Externo
        $envio_disenador_externo = 'N';
        $gestion_envio_disenador_externo = Management::where('work_order_id', $ot->id)->where('management_type_id', 9)->get();
        $envio_disenador_externo = (count($gestion_envio_disenador_externo) > 0) ? 'S' : 'N';

        return view('work-orders.gestionar-ot', compact("comunas", 'cartons_muestra', "cartons", "prefijos", "sufijos", "prefijosSimple", 'ot', 'states', 'workSpaces', 'managementTypes', 'motivos', 'proximoCodigoMaterial', 'cads', 'usuarioAsignado', 'validation_edition', 'procesos', 'files_by_area', 'files_develop_ventas', 'salas_cortes', 'muestras', 'proveedores', 'indicaciones_especiales', 'envio_disenador_externo', 'pendiente_recepcion_externo'));
    }

    public function storeRespuesta(Request $request, $id)
    {
        // dd((request()->all()));
        $respuesta = new Answer();
        $respuesta->respuesta = request('respuesta');
        $respuesta->user_id = auth()->user()->id;
        $respuesta->management_id = $id;
        $respuesta->save();

        $management = Management::find($id);
        // Crear notificacion de respuesta
        $this->crearNotificacion($management->ot->id, $management->user_id, auth()->user()->id, "Respuesta a Consulta", strlen($respuesta->respuesta) > 180 ? mb_substr($respuesta->respuesta, 0, 180) . "..." : $respuesta->respuesta);

        return redirect()->back()->with('success', 'Respuesta enviada Correctamente');
    }

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

    public function reactivarOT(Request $request, $id)
    {
        // dd($id);
        $ot = WorkOrder::find($id);

        $gestion = new Management();
        $gestion->observacion = "Órden de Trabajo Reactivada";
        $gestion->management_type_id = 1;
        $gestion->user_id = auth()->user()->id;
        $gestion->work_order_id = $id;
        $gestion->work_space_id =  $ot->current_area_id;
        $gestion->duracion_segundos = 0;
        $gestion->state_id = 1;
        $gestion->save();

        $ot->ultimo_cambio_area = Carbon::now();
        $ot->save();

        return redirect()->back()->with('success', 'Órden de Trabajo Reactivada Correctamente');
    }

    public function retomarOT(Request $request, $id)
    {
        // dd($id);
        $ot = WorkOrder::find($id);

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
        $diff2 = get_working_hours2($date, $now) * 3600;

        // El estado y area depende del area del usuario que la este retomando
        switch (auth()->user()->role->area->id) {
            case '2':
                $estado = 2;
                break;
            case '3':
                $estado = 5;
                break;
            case '4':
                $estado = 7;
                break;

            default:
                $estado = 2;
                break;
        }


        $gestion = new Management();
        $gestion->observacion = "Órden de Trabajo Retomada por " . auth()->user()->role->area->nombre;
        $gestion->management_type_id = 1;
        $gestion->user_id = auth()->user()->id;
        $gestion->work_order_id = $id;
        $gestion->work_space_id =  1;
        $gestion->duracion_segundos = $diff;
        $gestion->duracion_segundos_24h = $diff2;
        $gestion->state_id = $estado;
        $gestion->save();

        $ot->ultimo_cambio_area = Carbon::now();
        $ot->current_area_id = auth()->user()->role->area->id;
        $ot->save();

        return redirect()->back()->with('success', 'Órden de Trabajo Retomada Correctamente');
    }

    public function store(Request $request, $id)
    {

        $ot = WorkOrder::find($id);
        $id_muestras = '';
        $muestras_sin_sala_corte = Muestra::where('work_order_id', $ot->id)
            ->whereIn('estado', [0, 1])
            ->whereNull('sala_corte_vendedor')
            ->whereNull('sala_corte_diseñador')
            ->whereNull('sala_corte_diseñador_revision')
            ->whereNull('sala_corte_laboratorio')
            ->whereNull('sala_corte_1')
            ->whereNull('sala_corte_2')
            ->whereNull('sala_corte_3')
            ->whereNull('sala_corte_4')
            ->get();


        if ($muestras_sin_sala_corte->count() > 0 && $ot->current_area_id == 2 && request('state_id') == 17) {

            foreach ($muestras_sin_sala_corte as $muestra) {
                $id_muestras .= ' ' . $muestra->id;
            }
            //dd($id_muestras);
            return redirect()->route('gestionarOt', $id)->with('danger', 'Muestras sin sala de corte:' . $id_muestras . ' .Favor gestionar la muestra');
        }

        // dd(request()->all());

        $gestion = new Management();
        // $gestion->titulo = request('titulo');
        if (request('management_type_id') == 6) {
            // Concatenamos la observacion que ingreso el cliente con el mensaje de que fue un archivo cargado desde el lector de PDF
            $gestion->observacion = "Datos OT cargados con lector PDF.  " . request('observacion');
        } else {
            if (is_null(request('observacion')) || request('observacion') == '') {

                $gestion->observacion = "sin observaciones por usuario";
            } else {
                $gestion->observacion = request('observacion');
            }
        }
        $gestion->management_type_id = request('management_type_id');
        $gestion->user_id = auth()->user()->id;
        $gestion->work_order_id = $id;
        $gestion->work_space_id =  $ot->current_area_id;


        //dd($ot->ultimoCambioEstado->state_id);
        // Cambio de estado
        if (request('management_type_id') == 1) {

            if (empty($ot->ultimoCambioEstado)) {
                $date = Carbon::parse($ot->ultimo_cambio_area);
            } else if ($ot->ultimo_cambio_area->gt($ot->ultimoCambioEstado->created_at)) {
                // ultimo_cambio_area at is newer than ultimoCambioEstado created at
                $date = Carbon::parse($ot->ultimo_cambio_area);
            } else {
                $date = Carbon::parse($ot->ultimoCambioEstado->created_at);
            }
            //Validacion para acumulacion de segundos para el estado de cotizacion
            if ($ot->ultimoCambioEstado->state_id == 21) {
                $date = Carbon::parse($ot->ultimo_cambio_area);
            }
            $now = Carbon::now();
            // $diff = $date->diffInSeconds($now);
            $diff = (request('state_id') == 21) ? 0 : get_working_hours($date, $now) * 3600;
            $diff2 = (request('state_id') == 21) ? 0 : get_working_hours2($date, $now) * 3600;
            // dd($diff, $diffWorkingSeconds);

            // SI AL CAMBIAR ESTADO EL ESTADO ACTUAL ES UN ESTADO
            // Terminado = 8
            // Perdido = 9
            // Anulado = 11
            // Entregado = 13
            // Hibernación = 20 Es un estado que solo puede modificar el vendedor
            // Se debe guardar como tiempo de ese proceso de reactivacion 0 segundos
            if (isset($ot->ultimoCambioEstado) && in_array($ot->ultimoCambioEstado->state_id, [8, 9, 11, 13, 20])) {
                $diff = 0;
            }
            //dd($diff);
            //$diff = (request('state_id')==21)?0;$
            $gestion->duracion_segundos = $diff;
            $gestion->duracion_segundos_24h = $diff2;
            $gestion->state_id = request('state_id');

            if (request('state_id') == 12) {
                $gestion->motive_id =  request('motive_id');
                $gestion->consulted_work_space_id =  request('work_space_id');
            }


            // Verificar el estado para cambiar el area
            // Cambiar a Ventas
            if (request('state_id') == 1) {
                $gestion->save();

                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 1;
                $ot->save();

                push_notification("Devolución", "Se le ha devuelto la OT " . $ot->id, $ot->id, $ot->creador->token_push_mobile);

                // Crear notificacion de cambio de area
                $user_id = $this->usuarioAsignadoPorArea($ot, 1); // 1= ventas
                $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, "Regreso a Venta", $gestion->observacion);
            }
            // Cambiar a Desarrollo
            if (request('state_id') == 2) {
                $gestion->save();

                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 2;
                $ot->save();
                // Crear notificacion de cambio de area
                $user_id = $this->usuarioAsignadoPorArea($ot, 2); // 2= diseño estructural
                $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, "Regreso a Diseño Estructural", $gestion->observacion);
            }
            // Cambiar a Diseño
            if (request('state_id') == 5) {
                $gestion->save();

                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 3;
                $ot->save();
                // Crear notificacion de cambio de area
                $user_id = $this->usuarioAsignadoPorArea($ot, 3); // 3= diseño grafico
                $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, "Regreso a Diseño Grafico", $gestion->observacion);

                // Si la solicitud es del tipo Arte con Material = 5 y es enviada por primera vez a diseño grafico se debe crear material
                if ($ot->tipo_solicitud == 5) {
                    // Si se envia a diseño y no se ha creado el material lo creamos
                    if (isset($ot->reference_id) && $ot->material_id == null) {
                        $material = Material::find($ot->reference_id);

                        $newMaterial = $material->replicate();
                        $newMaterial->client_id = $ot->client_id;
                        $newMaterial->vendedor_id = $ot->creador_id;
                        $newMaterial->codigo = $ot->material_code;
                        $newMaterial->work_order_id = $ot->id;
                        $newMaterial->fecha_creacion = Carbon::now()->format("Y-m-d");
                        $newMaterial->descripcion = $ot->descripcion;
                        $newMaterial->work_order_id = $ot->id;
                        $newMaterial->cinta = $ot->cinta;
                        $newMaterial->corte_liner = $ot->corte_liner;
                        $newMaterial->tipo_cinta = $ot->tipo_cinta;
                        $newMaterial->distancia_cinta_1 = $ot->distancia_cinta_1;
                        $newMaterial->distancia_cinta_2 = $ot->distancia_cinta_2;
                        $newMaterial->distancia_cinta_3 = $ot->distancia_cinta_3;
                        $newMaterial->distancia_cinta_4 = $ot->distancia_cinta_4;
                        $newMaterial->distancia_cinta_5 = $ot->distancia_cinta_5;
                        $newMaterial->distancia_cinta_6 = $ot->distancia_cinta_6;
                        $newMaterial->gramaje = $ot->gramaje != "" ? $ot->gramaje : null;
                        $newMaterial->ect = $ot->ect != "" ? str_replace(',', '.', $ot->ect) : null;
                        $newMaterial->flexion_aleta = $ot->flexion_aleta != "" ?  $ot->flexion_aleta : null;
                        $newMaterial->peso = $ot->peso != "" ?  $ot->peso : null;
                        $newMaterial->fct = $ot->fct != "" ?  str_replace(',', '.', $ot->fct) : null;
                        $newMaterial->cobb_interior = $ot->cobb_interior != "" ?  str_replace(',', '.', $ot->cobb_interior) : null;
                        $newMaterial->cobb_exterior = $ot->cobb_exterior != "" ?  str_replace(',', '.', $ot->cobb_exterior) : null;
                        $newMaterial->espesor = $ot->espesor != "" ?  str_replace(',', '.', $ot->espesor) : null;
                        $newMaterial->save();

                        $ot->cad = $ot->cad_asignado->cad;
                        $ot->material_id = $newMaterial->id;
                        $ot->material_asignado = $ot->material_code;
                        $ot->descripcion_material = $ot->descripcion;
                        $ot->save();
                        // dd($material, $newMaterial, $ot, $gestion);
                    }
                }
            }
            // Cambiar a Catalogacion
            if (request('state_id') == 7) {
                $gestion->save();
                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 4;
                $ot->save();
                // Crear notificacion de cambio de area
                $user_id = $this->usuarioAsignadoPorArea($ot, 4); // 4= Catalogacion
                $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, "Regreso a Catalogación", $gestion->observacion);
            }
            // Cambiar a Precatalogacion
            if (request('state_id') == 6) {
                $gestion->save();
                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 5;
                $ot->save();
                // Crear notificacion de cambio de area
                $user_id = $this->usuarioAsignadoPorArea($ot, 5); // 5= Precatalogación
                $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, "Regreso a Precatalogación", $gestion->observacion);
            }
            // Rechazo
            // Cambiar al area devuelta segun rechazo
            if (request('state_id') == 12) {
                if ($ot->current_area_id == 6) {
                    $now = Carbon::now();

                    $muestras_pendientes = Muestra::where('work_order_id', $ot->id)
                        ->whereNotIn('estado', [2, 3, 4])
                        ->get();

                    foreach ($muestras_pendientes as $muestra_pendiente) {

                        $muestra_pendiente->estado = 2;
                        $muestra_pendiente->ultimo_cambio_estado = $now;
                        $muestra_pendiente->fin_sala_corte = $now;
                        $diff = get_working_hours($muestra_pendiente->inicio_sala_corte, $now) * 3600;
                        $muestra_pendiente->duracion_sala_corte = $diff;
                        $muestra_pendiente->save();
                    }
                }
                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = request('work_space_id');
                $ot->save();

                if ($ot->current_area_id == 1) {
                    push_notification("Rechazo", "Se rechazo la OT " . $ot->id, $ot->id, $ot->creador->token_push_mobile);
                }

                // Crear notificacion de cambio de area
                $user_id = $this->usuarioAsignadoPorArea($ot, $gestion->consulted_work_space_id);
                $motivo = "Rechazo - " . [1 => "Falta Información", 2 => "Información Erronea", 3 => "Falta Muestra Física", 4 => "Formato Imagen Inadecuado", 5 => "Medida Erronea", 6 => "Descripción de Producto", 7 => "Plano mal Acotado", 8 => "Error de Digitación", 9 => "Error tipo Sustrato", 10 => "No viable por restricciones", 11 => "Falta CAD para corte", 12 => "Falta OT Chileexpress", 13 => "Falta OT Laboratorio"][$gestion->motive_id];
                $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, $motivo, $gestion->observacion);
            }

            // OT PERDIDA
            if (request('state_id') == 9) {
                $gestion->save();
                $ot->ultimo_cambio_area = $now;
                $ot->save();
            }
            // OT Anulada
            if (request('state_id') == 11) {
                $gestion->save();
                $ot->ultimo_cambio_area = $now;
                $ot->save();
            }

            // ENTREGADA
            if (request('state_id') == 13) {
                $gestion->save();
                // La ot solo es entregada por desarrollo o ingenieria y en ese momento debe pasar a ventas
                // una vez q venta la envie a otra area debe contabilizar tiempo de nuevo
                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 1;
                $ot->save();
                // Notificar a ventas
                $user_id = $this->usuarioAsignadoPorArea($ot, 1);
                $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, "Entregada", "");
            }

            // TERMINADA
            if (request('state_id') == 8) {
                $gestion->save();
                // Notificar a ventas
                $user_id = $this->usuarioAsignadoPorArea($ot, 1);
                $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, "Terminada", "");

                $material = Material::find($ot->material_id);
                if ($material) {
                    $material->active = 1;
                    $material->save();
                }
            }

            // Visto bueno cliente
            if (request('state_id') == 16) {
                $gestion->save();
                // una vez q venta la envie a otra area debe contabilizar tiempo de nuevo
                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 1;
                $ot->save();

                // Notificar a ventas si es de otra area
                if (auth()->user()->role->area->id != Constants::AreaVenta) {
                    $user_id = $this->usuarioAsignadoPorArea($ot, 1);
                    $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, "Visto Bueno Cliente", "");
                }
            }

            // Cambiar a Sala de Muestras
            if (request('state_id') == 17) {
                if (count($ot->muestras) < 1) {

                    return redirect()->back()->with('danger', 'Debes ingresar al menos una muestra para su gestión ');
                }
                $gestion->save();

                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 6;
                $ot->save();

                //Se Actualiza los estado de los id de muestra En Proceso
                $muestras_update = Muestra::where('work_order_id', $ot->id)
                    ->whereIn('estado', [0, 1])
                    ->update([
                        'estado' => 1,
                        'inicio_sala_corte' => Carbon::now()
                    ]);


                // Crear notificacion de cambio de area
                $user_id = $this->usuarioAsignadoPorArea($ot, 6); // 6= Sala de Muestras

                // Sala de muestra es un area especial donde hay un unico Tecnico de muestras
                // por lo tanto al enviar a sala de muestra se debe asignar la OT directamente al unico usuario TECNICO DE MUESTRa si es que ya no lo tiene asignado
                if (!$user_id) {
                    // para efectos de prueba usuario id =
                    // 88 = TESTING
                    // 85 = PRODUCCION
                    $ID_UNICO_TECNICO_MUESTRAS = 85;

                    $asignacion = new UserWorkOrder();
                    $asignacion->work_order_id = $ot->id;
                    $asignacion->user_id = $ID_UNICO_TECNICO_MUESTRAS;
                    $asignacion->area_id = 6;
                    $asignacion->tiempo_inicial = 0;
                    $asignacion->save();

                    $motivo = "Asignado";


                    // Crear notificacion de asignacion
                    $notificacion = new Notification();
                    $notificacion->work_order_id = $ot->id;
                    $notificacion->user_id = $ID_UNICO_TECNICO_MUESTRAS;
                    $notificacion->generador_id = auth()->user()->id;
                    $notificacion->motivo = $motivo;
                    $notificacion->observacion = '';
                    $notificacion->save();
                }
                $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, "Envio a Sala de Muestras", $gestion->observacion);
            }

            // Cambiar a Muestras listas = Desarrollo
            if (request('state_id') == 18) {
                foreach ($ot->muestras as $muestra) {
                    // if ($muestra->estado == 4) continue;
                    if ($muestra->estado == 1) {

                        return redirect()->back()->with('danger', 'Debes terminar todas las muestras para culminar la Gestión');
                    }
                }
                $gestion->save();

                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 2;
                $ot->save();
                // Crear notificacion de cambio de area
                $user_id = $this->usuarioAsignadoPorArea($ot, 2); // 2 = desarrollo
                $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, "Muestras Listas", $gestion->observacion);
            }

            // Cambiar a Muestras Devuelta = Desarrollo
            if (request('state_id') == 22) {

                if ($ot->current_area_id == 6) {
                    $now = Carbon::now();


                    $muestras_pendientes = Muestra::where('work_order_id', $ot->id)
                        ->whereNotIn('estado', [2, 3, 4])
                        ->get();

                    foreach ($muestras_pendientes as $muestra_pendiente) {

                        $muestra_pendiente->estado = 5;
                        $muestra_pendiente->ultimo_cambio_estado = $now;
                        $muestra_pendiente->fin_sala_corte = $now;
                        $diff = get_working_hours($muestra_pendiente->inicio_sala_corte, $now) * 3600;
                        $muestra_pendiente->duracion_sala_corte = $diff;
                        $muestra_pendiente->save();
                    }

                    /*
                    $muestra_pendiente= Muestra::find(request('id_muestra_consulta'));
                    $muestra_pendiente->estado = 5;
                    $muestra_pendiente->ultimo_cambio_estado = $now;
                    $muestra_pendiente->fin_sala_corte = $now;
                    $diff = get_working_hours($muestra_pendiente->inicio_sala_corte, $now) * 3600;
                    $muestra_pendiente->duracion_sala_corte=$diff;
                    $muestra_pendiente->save();*/
                }
                $gestion->save();
                $ot->ultimo_cambio_area = $now;
                $ot->current_area_id = 2;
                $ot->save();


                $gestion->consulted_work_space_id =  2;

                if ($ot->current_area_id == 1) {
                    push_notification("Muestras Devueltas", "Se Devuelve la OT " . $ot->id, $ot->id, $ot->creador->token_push_mobile);
                }

                // Crear notificacion de cambio de area
                $user_id = $this->usuarioAsignadoPorArea($ot, $gestion->consulted_work_space_id);

                $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, "Muestras Devueltas", $gestion->observacion);
            }
        } // Consulta
        else if (request('management_type_id') == 2) {
            // todo

            if ($ot->current_area_id == 6 || $ot->current_area_id == 2) {
                $gestion->consulted_work_space_id   =  request('work_space_id');
                $gestion->muestra_id   =  request('muestra_consulta_id');
                if (is_null(request('muestra_consulta_id'))) {
                    $muestra = "Consulta";
                } else {
                    $muestra = "Consulta ID Muestra - " . $gestion->muestra_id;
                }
                if ($gestion->consulted_work_space_id == 1) {
                    push_notification("Consulta", "Se le ha realizado una consulta en la OT " . $ot->id, $ot->id, $ot->creador->token_push_mobile);
                }
                // Crear notificacion de consulta
                $user_id = $this->usuarioAsignadoPorArea($ot, $gestion->consulted_work_space_id);
                $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, $muestra, $gestion->observacion);
            } else {
                $gestion->consulted_work_space_id =  request('work_space_id');
                $gestion->save();
                if ($gestion->consulted_work_space_id == 1) {
                    push_notification("Consulta", "Se le ha realizado una consulta en la OT " . $ot->id, $ot->id, $ot->creador->token_push_mobile);
                }
                // Crear notificacion de consulta
                $user_id = $this->usuarioAsignadoPorArea($ot, $gestion->consulted_work_space_id);
                $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, "Consulta", $gestion->observacion);
            }
        } else if (request('management_type_id') == 8) {


            $id_muestra = is_null(request('muestra_consulta_id')) ? request('id_muestra_consulta') : request('muestra_consulta_id');

            $now = Carbon::now();
            $muestra = Muestra::find($id_muestra);
            $muestra->estado = 5;
            $muestra->ultimo_cambio_estado = $now;
            $muestra->fin_sala_corte = $now;
            $diff = get_working_hours($muestra->inicio_sala_corte, $now) * 3600;
            $muestra->duracion_sala_corte = $diff;
            $muestra->save();

            $gestion->consulted_work_space_id   =  request('work_space_id');
            $gestion->muestra_id   =  $id_muestra;
            $gestion->save();

            $muestra = "Devolucion ID Muestra - " . $id_muestra;
            if ($gestion->consulted_work_space_id == 1) {
                push_notification("Devolución", "Se ha realizado una devolución en la OT " . $ot->id, $ot->id, $ot->creador->token_push_mobile);
            }


            // Crear notificacion de devolución
            $user_id = $this->usuarioAsignadoPorArea($ot, $gestion->consulted_work_space_id);
            $this->crearNotificacion($ot->id, $user_id, auth()->user()->id, $muestra, $gestion->observacion);
        } //Envio Diseñador Externo
        else if (request('management_type_id') == 9) {

            $gestion->proveedor_id = request('proveedor_id');
        } else if (request('management_type_id') == 10) {
            $gestion_envio = Management::where('work_order_id', $id)->where('management_type_id', 9)->where('recibido_diseño_externo', 0)->get();
            $gestion_envio_update = Management::where('id', $gestion_envio[0]->id)
                ->where('work_order_id', $id)
                ->where('management_type_id', 9)
                ->where('recibido_diseño_externo', 0)
                ->update(['recibido_diseño_externo' => 1]);
            $gestion->proveedor_id  = $gestion_envio[0]->proveedor_id;
            $gestion->gestion_id    = $gestion_envio[0]->id;
            $gestion->save();
        } else {
            $gestion->save();
        }
        // dd(request()->all());
        $gestion->save();
        // dd($gestion->id);
        // dd($gestion);

        // Si vienen archivos anexos, los almacenamos 1 a 1
        if ($request->hasfile('files')) {

            foreach ($request->file('files') as $archivo) {
                $file = new File();

                $filename = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
                $name = str_replace('%', '', $filename);
                $peso = $this->human_filesize($archivo->getSize());
                $tipo_archivo = $this->tipo_archivo($extension);
                $destinationFolder = public_path('/files/');

                // Validar si nombre de archivo existe
                $num = 1;
                // dd($archivo->getClientOriginalName());
                while (file_exists($destinationFolder . $name . '.' . $extension)) {
                    $name = $filename . '(' . $num . ')';
                    $num++;
                }
                // dd($name);
                $archivo->move($destinationFolder, $name . '.' . $extension);
                $file->url = '/files/' . $name . '.' . $extension;
                $file->peso = round($peso[0]);
                $file->unidad = $peso[1];
                $file->tipo = $tipo_archivo;
                $file->management_id = $gestion->id;
                // dd($gestion->id);
                $file->save();
            }
        }
        //Se Reliza el manejo del archio que se genera para el envio a diseñadro externo
        if (request('management_type_id') == 9) {

            $gestion_envio_disenador_externo = Management::where('work_order_id', $id)
                ->where('management_type_id', 9)
                ->where('created_at', '<', Carbon::now())
                ->get();

            if (count($gestion_envio_disenador_externo) < 1) {
                $destinationFolder = public_path('/files/');

                // Validar si nombre de archivo existe
                //$num = 1;
                // dd($archivo->getClientOriginalName());
                if (file_exists($destinationFolder . 'OT_' . $ot->id . '_temp.pdf')) {

                    if (file_exists($destinationFolder . 'OT_' . $ot->id . '.pdf')) {

                        $num = 1;
                        $filename = pathinfo($destinationFolder . 'OT_' . $ot->id . '.pdf', PATHINFO_FILENAME);
                        $extension = pathinfo($destinationFolder . 'OT_' . $ot->id . '.pdf', PATHINFO_EXTENSION);
                        $name = str_replace('%', '', $filename);

                        while (file_exists($destinationFolder . $name . '.' . $extension)) {
                            $name = $filename . '_' . $num;
                            $num++;
                        }
                        rename($destinationFolder . 'OT_' . $ot->id . '_temp.pdf', $destinationFolder . $name . '.pdf');
                        $filenameaux = pathinfo($destinationFolder . $name . '.pdf', PATHINFO_FILENAME);

                        $extensionaux = pathinfo($destinationFolder . $name . '.pdf', PATHINFO_EXTENSION);
                        $peso = $this->human_filesize(filesize($destinationFolder . $filenameaux . '.pdf'));
                        $tipo_archivo = $this->tipo_archivo($extension);

                        $file = new File();

                        // dd($gestion->id);

                        //$archivo->move($destinationFolder, $name . '.' . $extension);
                        $file->url = '/files/' . $name . '.' . $extension;
                        $file->peso = round($peso[0]);
                        $file->unidad = $peso[1];
                        $file->tipo = $tipo_archivo;
                        $file->management_id = $gestion->id;
                        $file->save();
                    } else {
                        rename($destinationFolder . 'OT_' . $ot->id . '_temp.pdf', $destinationFolder . 'OT_' . $ot->id . '.pdf');
                        $filename = pathinfo($destinationFolder . 'OT_' . $ot->id . '.pdf', PATHINFO_FILENAME);
                        $extension = pathinfo($destinationFolder . 'OT_' . $ot->id . '.pdf', PATHINFO_EXTENSION);
                        $peso = $this->human_filesize(filesize($destinationFolder . $filename . '.pdf'));
                        $tipo_archivo = $this->tipo_archivo($extension);
                        $name = str_replace('%', '', $filename);

                        $file = new File();

                        //$archivo->move($destinationFolder, $name . '.' . $extension);
                        $file->url = '/files/' . $name . '.' . $extension;
                        $file->peso = round($peso[0]);
                        $file->unidad = $peso[1];
                        $file->tipo = $tipo_archivo;
                        $file->management_id = $gestion->id;
                        $file->save();
                    }
                }
            }
        }

        return redirect()->route('gestionarOt', $id)->with('success', 'Gestion Creada Correctamente');
    }

    public function read_pdf(Request $request)
    {

        // self::store_pdf_read($request->file('file_pdf'));
        // echo 'Cargado!';
        // return;
        $parseador = new Parser();
        $documento = $parseador->parseFile($request->file('file_pdf'));
        $data = [];

        $texto = $documento->getText();
        // $cleanText = str_replace('/\t', '', $texto);
        // echo $texto;
        // exit();
        $cleanText = trim(preg_replace('/\t+/', '<>', $texto));
        $cleanText = trim(preg_replace('/\n+/', ' <<break>> ', $cleanText));
        $arrayText = explode(' <<break>> ', $cleanText);

        // Obtain values
        $data = self::searchValue('ot', 'OT', $arrayText, $data);
        $data = self::searchValue('carton', 'Carton', $arrayText, $data);
        $data = self::searchValue('medidas_interiores', 'Medidas Interiores', $arrayText, $data);
        $data = self::searchValue('medidas_exteriores', 'Medidas Exteriores', $arrayText, $data);
        $data = self::searchValue('largura', 'Largura HM', $arrayText, $data);
        $data = self::searchValue('anchura', 'Anchura HM', $arrayText, $data);
        $data = self::searchValue('golpes_largo', 'Golpes al Largo', $arrayText, $data);
        $data = self::searchValue('golpes_ancho', 'Golpes al Ancho', $arrayText, $data);
        $data = self::searchValue('area_producto', 'Area Producto', $arrayText, $data);
        $data = self::searchValue('area_agujeros', 'Area Agujeros', $arrayText, $data);
        $data = self::searchValue('proceso', 'PROCESO', $arrayText, $data);
        $data = self::searchValue('maquila', 'Maquila', $arrayText, $data);

        // Extra explode
        $ot = explode('<>', $data['ot']);
        $data['ot'] = $ot[0];

        // Clean values
        $data = self::sanitizeValues('ot', '<>', '', $data);
        $data = self::sanitizeValues('ot', 'OT', '', $data);
        $data = self::sanitizeValues('carton', array('Carton:', ' '), '', $data);
        $data = self::sanitizeValues('medidas_interiores', ':', '', $data, 'explode', 1);
        $data = self::sanitizeValues('medidas_interiores', array(' [mm] ', ' ', '[mm]'), '', $data);
        $data = self::sanitizeValues('medidas_interiores', 'x', '', $data, 'explode', -1);
        $data = self::sanitizeValues('medidas_exteriores', ':', '', $data, 'explode', 1);
        $data = self::sanitizeValues('medidas_exteriores', array(' [mm] ', ' ', '[mm]'), '', $data);
        $data = self::sanitizeValues('medidas_exteriores', 'x', '', $data, 'explode', -1);
        $data = self::sanitizeValues('largura', array('Largura HM ', '[mm]', ' '), '', $data);
        $data = self::sanitizeValues('anchura', array('Anchura HM:', '[mm]', ' '), '', $data);
        $data = self::sanitizeValues('golpes_largo', array('Golpes al Largo:'), '', $data);
        $data = self::sanitizeValues('golpes_ancho', array('Golpes al Ancho:'), '', $data);
        $data = self::sanitizeValues('area_producto', array('Area Producto', ' ', '[mm2]'), '', $data);
        $data = self::sanitizeValues('area_producto', array('Area Producto', ' ', '[m2]'), '', $data);
        $data = self::sanitizeValues('area_agujeros', array('Area Agujeros:', ' ', '[m²]'), '', $data);
        $data = self::sanitizeValues('proceso', array('PROCESO:', ' '), '', $data);
        $data = self::sanitizeValues('maquila', array('Maquila: ', '<>'), '', $data);


        // ------------------------------  Datos Antiguos  --------------------------------------
        // $data['ot'] = $arrayText[4];
        // $data = self::searchValue('areas', 'Area Producto', $arrayText, $data);
        // $data = self::searchValue('largura_anchura', 'Largura', $arrayText, $data);
        // $data = self::searchValue('carton_cad', 'Carton', $arrayText, $data);

        // $areas = explode('<>', $data['areas']);
        // $largura_anchura = explode('<>', $data['largura_anchura']);
        // $carton_cad = explode('<>', $data['carton_cad']);
        // $data['area_producto'] = $areas[0];
        // $data['area_agujeros'] = $areas[1];
        // $data['largura'] = $largura_anchura[0];
        // $data['anchura'] = $largura_anchura[1];
        // $data['carton'] = $carton_cad[0];
        // $data['cad'] = $carton_cad[1];

        // $data = self::sanitizeValues('area_producto', array('Area Producto ', ' [mm2]'), '', $data);
        // $data = self::sanitizeValues('area_agujeros', array('Area Agujeros:', '[m²]'), '', $data);
        // $data = self::sanitizeValues('largura', array('Largura HM ', ' [mm]'), '', $data);
        // $data = self::sanitizeValues('anchura', array('Anchura HM: ', ' [mm]'), '', $data);
        // $data = self::sanitizeValues('area_agujeros', array('Area Agujeros:', '[m²]'), '', $data);
        // $data = self::sanitizeValues('carton', array('Carton: '), '', $data);
        // $data = self::sanitizeValues('cad', array('CAD: '), '', $data);

        // unset($data['areas']);
        // unset($data['largura_anchura']);
        // unset($data['carton_cad']);

        return response()->json(['data' => $data]);
    }

    //Buscamos el carton del lector PDF primero, para saber si existe o si esta activo en la BD
    public function validar_carton(Request $request)
    {

        $carton = Carton::where('codigo', 'like', '%' . request()->input('codigo_carton') . '%')->first();
        $carton_data = $carton != '' ? $carton->codigo : null;

        return response()->json(['carton_data' => $carton_data]);
    }

    public function store_pdf_read($file_pdf)
    {
        $file = new File();

        $filename = pathinfo($file_pdf->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = pathinfo($file_pdf->getClientOriginalName(), PATHINFO_EXTENSION);
        $name = $filename;
        $peso = $this->human_filesize($file_pdf->getSize());
        $tipo_archivo = $this->tipo_archivo($extension);
        $destinationFolder = public_path('/files/');
        $file_pdf->move($destinationFolder, $name . '.' . $extension);
        $file->url = '/files/' . $name . '.' . $extension;
        $file->peso = round($peso[0]);
        $file->unidad = $peso[1];
        $file->tipo = $tipo_archivo;
        $file->management_id = 3;
        $file->save();
    }

    // Para buscar los posibles valores de lo que se genero en la lectura del PDF
    public function searchValue($key, $toSearch, $arrayText, $arrayValues)
    {
        foreach ($arrayText as $value) {
            if (str_contains($value, $toSearch)) {
                $arrayValues[$key] = $value;
            }
        }

        return $arrayValues;
    }

    //Limpia los valores del PDF para que se puedan mostrar bien en el modal
    public function sanitizeValues($key, $delimiter, $toReplace, $arrayValues, $action = 'replace', $positionExplode = 1)
    {
        if ($action == 'replace') {
            $arrayValues[$key] = str_replace($delimiter, $toReplace, $arrayValues[$key]);
        }

        if ($action == 'explode') {
            if ($positionExplode == -1) {
                $arrayValues[$key] = str_replace('  ', '', explode($delimiter, $arrayValues[$key]));
            } else {
                $arrayValues[$key] = str_replace('  ', '', explode($delimiter, $arrayValues[$key])[$positionExplode]);
            }
        }

        return $arrayValues;
    }

    public function store_pdf(Request $request)
    {
        $Ot_modificada = $request->input('otID'); // OT original
        $ot =  WorkOrder::find($Ot_modificada); //Buscamos los datos de la OT original
        $Ot_lector_pdf = $request->input('ot_id'); //OT ingresada en el input del lector PDF

        if ($Ot_modificada == $Ot_lector_pdf) {

            //Buscamos primero todos los campos de la OT de la tabla
            $campos_modificados = BitacoraCamposModificados::all()->pluck('descripcion')->toArray();

            // Busqueda de datos para la bitacora
            $carton_antiguo = Carton::where('id', $ot->carton_id)->select('codigo')->pluck('codigo')->first();

            $proceso_antiguo = Process::where('id', $ot->process_id)->select('descripcion')->pluck('descripcion')->first();

            $maquila_servicio_antiguo = MaquilaServicio::where('id', $ot->maquila_servicio_id)->select('servicio')->pluck('servicio')->first();

            //Buscamos el ID del carton
            $carton = Carton::where('codigo', 'like', '%' . request()->input('carton') . '%')->first();
            $carton_id = $carton != '' ? $carton->id : $ot->carton_id;
            $carton_nuevo = $carton != '' ? $carton->codigo : null;

            //Buscamos el ID del proceso
            if (request()->input('process') != '') {
                $proceso = Process::where('descripcion', 'like', '%' . request()->input('process') . '%')->first();
                $process_id = $proceso != '' ? $proceso->id : $ot->process_id;
                $proceso_nuevo = $proceso != '' ? $proceso->descripcion : null;
            } else {
                $process_id = null;
                $proceso_nuevo = null;
            }

            //Buscamos el ID del servicio de maquila
            if (request()->input('maquila') == 'No aplica' || request()->input('maquila') == 'No Aplica') {
                $maquila = '0'; //Se declara maquila NO (Automaticamente)
                $maquila_servicio_id = null; //Servicio maquila NULL
                $maquila_servicio_nuevo = null;
            } else if (request()->input('maquila') == '') {

                $maquila = null;
                $maquila_servicio_id = null; //Servicio maquila NULL
                $maquila_servicio_nuevo = null;
            } else {

                $maquila = 1; //Se declara maquila SI (Automaticamente)
                $maquila_servicio = MaquilaServicio::where('servicio', 'like', '%' . request()->input('maquila') . '%')->first(); //Se busca el ID del servecio indicado
                $maquila_servicio_id = $maquila_servicio != '' ? $maquila_servicio->id : $ot->maquila_servicio_id;
                $maquila_servicio_nuevo = $maquila_servicio != '' ? $maquila_servicio->servicio : null;
            }

            $campos = array();
            $datos_modificados = array();
            // Compare datos para la bitacora ( antes de que se guarden )
            if ($ot->carton_id != $carton_id && $request->input('carton') != '') {
                $datos_modificados['carton_id'] = [
                    'texto' => 'Cartón',
                    'antiguo_valor' => ['id' => $ot->carton_id, 'descripcion' => $carton_antiguo],
                    'nuevo_valor' => ['id' => $carton_id, 'descripcion' => $carton_nuevo]
                ];
            }
            if (!in_array('Cartón', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Cartón'];
            }

            if ((string)$ot->largura_hm !== (string)$request->input('largura_hm')) {
                $datos_modificados['largura_hm'] = [
                    'texto' => 'Largura HM',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->largura_hm],
                    'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('largura_hm')]
                ];
            }
            if (!in_array('Largura HM', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Largura HM'];
            }

            if ((string)$ot->anchura_hm !== (string)$request->input('anchura_hm')) {
                $datos_modificados['anchura_hm'] = [
                    'texto' => 'Anchura HM',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->anchura_hm],
                    'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('anchura_hm')]
                ];
            }
            if (!in_array('Anchura HM', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Anchura HM'];
            }

            if ($ot->area_producto != $request->input('area_producto')) {
                $datos_modificados['area_producto'] = [
                    'texto' => 'Área Producto (m2)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(',', '.', $ot->area_producto)],
                    'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('area_producto')]
                ];
            }
            if (!in_array('Área Producto (m2)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Área Producto (m2)'];
            }

            if ($ot->recorte_adicional != $request->input('recorte_adicional')) {
                $datos_modificados['recorte_adicional'] = [
                    'texto' => 'Recorte Adicional / Area Agujero (m2)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->recorte_adicional],
                    'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('recorte_adicional')]
                ];
            }
            if (!in_array('Recorte Adicional / Area Agujero (m2)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Recorte Adicional / Area Agujero (m2)'];
            }

            if ((string)$ot->golpes_largo !== (string)$request->input('golpes_largo')) {
                $datos_modificados['golpes_largo'] = [
                    'texto' => 'Golpes al largo',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->golpes_largo],
                    'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('golpes_largo')]
                ];
            }
            if (!in_array('Golpes al largo', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Golpes al largo'];
            }

            if ((string)$ot->golpes_ancho !== (string)$request->input('golpes_ancho')) {
                $datos_modificados['golpes_ancho'] = [
                    'texto' => 'Golpes al ancho',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->golpes_ancho],
                    'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('golpes_ancho')]
                ];
            }
            if (!in_array('Golpes al ancho', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Golpes al ancho'];
            }

            // ---------------------------------------------------------------------------- MEDIDAS INTERIORES -------------------------------------------------------------
            if (str_replace(",", ".", str_replace('.', '', $ot->interno_largo)) != str_replace(",", ".", str_replace('.', '', $request->input('interno_largo')))) {
                $datos_modificados['interno_largo'] = [
                    'texto' => 'Medida interior largo (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $ot->interno_largo))],
                    'nuevo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('interno_largo')))]
                ];
            }
            if (!in_array('Medida interior largo (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Medida interior largo (mm)'];
            }
            if (str_replace(",", ".", str_replace('.', '', $ot->interno_ancho)) != str_replace(",", ".", str_replace('.', '', $request->input('interno_ancho')))) {
                $datos_modificados['interno_ancho'] = [
                    'texto' => 'Medida interior ancho (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $ot->interno_ancho))],
                    'nuevo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('interno_ancho')))]
                ];
            }
            if (!in_array('Medida interior ancho (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Medida interior ancho (mm)'];
            }
            if (str_replace(",", ".", str_replace('.', '', $ot->interno_alto)) != str_replace(",", ".", str_replace('.', '', $request->input('interno_alto')))) {
                $datos_modificados['interno_alto'] = [
                    'texto' => 'Medida interior alto (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $ot->interno_alto))],
                    'nuevo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('interno_alto')))]
                ];
            }
            if (!in_array('Medida interior alto (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Medida interior alto (mm)'];
            }

            // ---------------------------------------------------------------------------- MEDIDAS EXTERIORES -------------------------------------------------------------

            if (str_replace(",", ".", str_replace('.', '', $ot->externo_largo)) != str_replace(",", ".", str_replace('.', '', $request->input('externo_largo')))) {
                $datos_modificados['externo_largo'] = [
                    'texto' => 'Medida exterior largo (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $ot->externo_largo))],
                    'nuevo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('externo_largo')))]
                ];
            }
            if (!in_array('Medida interior alto (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Medida interior alto (mm)'];
            }
            if (str_replace(",", ".", str_replace('.', '', $ot->externo_ancho)) != str_replace(",", ".", str_replace('.', '', $request->input('externo_ancho')))) {
                $datos_modificados['externo_ancho'] = [
                    'texto' => 'Medida exterior ancho (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $ot->externo_ancho))],
                    'nuevo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('externo_ancho')))]
                ];
            }
            if (!in_array('Medida interior alto (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Medida interior alto (mm)'];
            }
            if (str_replace(",", ".", str_replace('.', '', $ot->externo_alto)) != str_replace(",", ".", str_replace('.', '', $request->input('externo_alto')))) {
                $datos_modificados['externo_alto'] = [
                    'texto' => 'Medida exterior alto (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $ot->externo_alto))],
                    'nuevo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('externo_alto')))]
                ];
            }
            if (!in_array('Medida exterior alto (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Medida exterior alto (mm)'];
            }

            if ($ot->process_id != $process_id) {
                $datos_modificados['process_id'] = [
                    'texto' => 'Proceso',
                    'antiguo_valor' => ['id' => $ot->process_id, 'descripcion' => $proceso_antiguo],
                    'nuevo_valor' => ['id' => $process_id, 'descripcion' => $proceso_nuevo]
                ];
            }
            if (!in_array('Proceso', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Proceso'];
            }

            if ((string)$ot->maquila !== (string)$maquila) {
                $datos_modificados['maquila'] = [
                    'texto' => 'Maquila',
                    'antiguo_valor' => ['id' => $ot->maquila, 'descripcion' => $ot->maquila == 1 ? 'Si' : ($ot->maquila == '0' ? 'No' : null)],
                    'nuevo_valor' => ['id' => $maquila, 'descripcion' => $maquila == 1 ? 'Si' : ($maquila == '0' ? 'No' :  null)]
                ];
            }
            if (!in_array('Maquila', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Maquila'];
            }

            if ($ot->maquila_servicio_id != $maquila_servicio_id) {
                $datos_modificados['maquila_servicio_id'] = [
                    'texto' => 'Servicios Maquila',
                    'antiguo_valor' => ['id' => $ot->maquila_servicio_id, 'descripcion' => $maquila_servicio_antiguo],
                    'nuevo_valor' => ['id' => $maquila_servicio_id, 'descripcion' => $maquila_servicio_nuevo]
                ];
            }
            if (!in_array('Servicios Maquila', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Servicios Maquila'];
            }


            //Guardamos los input del formulario
            $ot->carton_id           = (trim($request->input('carton')) != '') ? $carton_id : null;
            $ot->largura_hm          = (trim($request->input('largura_hm')) != '') ? $request->input('largura_hm') : null;
            $ot->anchura_hm          = (trim($request->input('anchura_hm')) != '') ? $request->input('anchura_hm') : null;
            $ot->area_producto       = (trim($request->input('area_producto')) != '') ? str_replace(",", ".", str_replace('.', '', number_format_unlimited_precision($request->input('area_producto')))) : null;
            $ot->recorte_adicional   = (trim($request->input('recorte_adicional')) != '') ? str_replace(",", ".", str_replace('.', '', number_format_unlimited_precision($request->input('recorte_adicional'), ',', '.', '4'))) : null;
            $ot->golpes_largo        = (trim($request->input('golpes_largo')) != '') ? $request->input('golpes_largo') : null;
            $ot->golpes_ancho        = (trim($request->input('golpes_ancho')) != '') ? $request->input('golpes_ancho') : null;
            $ot->interno_largo       = (trim($request->input('interno_largo')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('interno_largo'))) : null;
            $ot->interno_ancho       = (trim($request->input('interno_ancho')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('interno_ancho'))) : null;
            $ot->interno_alto        = (trim($request->input('interno_alto')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('interno_alto'))) : null;
            $ot->externo_largo       = (trim($request->input('externo_largo')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('externo_largo'))) : null;
            $ot->externo_ancho       = (trim($request->input('externo_ancho')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('externo_ancho'))) : null;
            $ot->externo_alto        = (trim($request->input('externo_alto')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('externo_alto'))) : null;
            $ot->process_id          = (trim($request->input('process')) != '') ? $process_id : null;
            $ot->maquila             = (trim($request->input('maquila')) != '') ? $maquila : null;
            $ot->maquila_servicio_id = (trim($request->input('maquila')) != '') ? $maquila_servicio_id : null;
            $ot->save();

            //Guarda la gestion identificada como lector PDF
            $gestion = new Management();
            // $gestion->titulo = request('titulo');
            $gestion->observacion = 'Subido archivo con lector PDF';
            $gestion->management_type_id = 7;
            $gestion->user_id = auth()->user()->id;
            $gestion->work_order_id = $Ot_modificada;
            $gestion->work_space_id =  $ot->current_area_id;
            $gestion->save();


            //Guardamos el archivo que lee el PDF
            if ($request->hasfile('file_pdf')) {
                $file = new File();
                $file_pdf = $request->file('file_pdf');

                $filename = pathinfo($file_pdf->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = pathinfo($file_pdf->getClientOriginalName(), PATHINFO_EXTENSION);
                $name = $filename;
                $peso = $this->human_filesize($file_pdf->getSize());
                $tipo_archivo = $this->tipo_archivo($extension);
                $destinationFolder = public_path('/files/');
                $file_pdf->move($destinationFolder, $name . '.' . $extension);
                $file->url = '/files/' . $name . '.' . $extension;
                $file->peso = round($peso[0]);
                $file->unidad = $peso[1];
                $file->tipo = $tipo_archivo;
                $file->management_id = $gestion->id;
                $file->save();
            }

            if (count($datos_modificados) > 0) { //Verificamos si se cambio algun valor para guardar
                //Se guarda registro en la tabla de bitacora
                $bitacora = new BitacoraWorkOrder();
                $user_auth = Auth()->user();
                $bitacora->observacion = "Subida de Archivo PDF con lector";
                $bitacora->operacion = 'Modificación'; //Tipo modificacion
                $bitacora->work_order_id = $ot->id;
                $bitacora->user_id = $user_auth->id;
                $user_data = array(
                    'nombre' => $user_auth->nombre,
                    'apellido' => $user_auth->apellido,
                    'rut' => $user_auth->rut,
                    'role_id' => $user_auth->role_id,
                );
                $bitacora->user_data = json_encode($user_data, JSON_UNESCAPED_UNICODE); //Se agrega JSON_UNESCAPED_UNICODE para mantener el formato de las palabras con acentos
                $bitacora->datos_modificados = json_encode($datos_modificados, JSON_UNESCAPED_UNICODE);
                $bitacora->ip_solicitud = \Request::getClientIp(true);
                $bitacora->url = url()->full();
                $bitacora->save();

                //se guardan los nombre de los campos que tiene la OT
                BitacoraCamposModificados::insert($campos);
            }


            return response()->json(true);
        } else {
            return response()->json(false);
        }
    }

    function human_filesize($bytes, $dec = 2)
    {
        $size   = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);

        return [sprintf("%.{$dec}f", $bytes / pow(1024, $factor)), @$size[$factor]];
    }

    function tipo_archivo($ext)
    {
        if ($ext == 'pdf') {
            return "pdf";
        } else if ($ext == 'cad' || $ext == 'dwg' || $ext == 'dxf' || $ext == 'dgn') {
            return "cad";
        } else if ($ext == 'doc' || $ext == 'docx' || $ext == 'xlsx' || $ext == 'csv') {
            return "ofi";
        } else if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg') {
            return "img";
        }

        return "otr";
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Management  $management
     * @return \Illuminate\Http\Response
     */
    public function show(Management $management)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Management  $management
     * @return \Illuminate\Http\Response
     */
    public function edit(Management $management)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Management  $management
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Management $management)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Management  $management
     * @return \Illuminate\Http\Response
     */
    public function destroy(Management $management)
    {
        //
    }

    public function validation_edition($ot)
    {
        $search = Management::where('work_order_id', $ot->id)->get();

        $validation = false;
        foreach ($search as $value) { //Cuando se consiga un id 6 ( que es estado de Pre Catalogacion ) ya no va a poder editar mas el campo OC (orden de compra)

            if ($value->state_id == 6) {
                $validation = true;
            }
        }

        return $validation;
    }

    public function detalleLogOt($id)
    {
        $ot_id = $id;

        // Filtro por fechas
        if (!is_null(request()->input('date_desde')) and !is_null(request()->input('date_hasta'))) {
            $fromDate = Carbon::createFromFormat('d/m/Y', request()->input('date_desde'))->toDateString();
            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
        } else {
            $fromDate = Carbon::now()->startOfMonth()->subMonth()->toDateString();
            $toDate = Carbon::tomorrow()->toDateString();
        }

        // $bitacora_ot = BitacoraWorkOrder::where('work_order_id' , $id)->where('id', 287)->whereNotNull('datos_modificados')->orderBy("id", "desc")->get()->paginate(20);

        $query = BitacoraWorkOrder::where('work_order_id', $id)
            ->whereIn('operacion', array('Mckee', 'Modificación'))
            ->whereNotNull('datos_modificados')
            ->orderBy("id", "desc");

        // filtro por ID especifico
        if (!is_null(request()->query('cambio_id'))) {
            $query = $query->whereIn('id', request()->query('cambio_id'));
        }

        // filtro por Usuario que hizo la modificacion
        if (!is_null(request()->query('user_id'))) {
            $query = $query->whereIn('user_id', request()->query('user_id'));
        }

        // filtro por la descripcion del cambio
        if (!is_null(request()->query('descripcion'))) {
            $query = $query->whereIn('observacion', request()->query('descripcion'));

            if (count(request()->query('descripcion')) <= 1) {
                $descripcion_filter = BitacoraWorkOrder::where('observacion', request()->query('descripcion'))->select('observacion')->pluck('observacion')->first();
            } else {
                $descripcion_filter = null;
            }
        } else {
            $descripcion_filter = null;
        }

        // filtro por los campos asignados del cambio
        $dato = null;
        if (!is_null(request()->query('campo_id'))) {
            $campo = BitacoraCamposModificados::whereIn('id', request()->query('campo_id'))->get();
            $dato = [];

            foreach ($campo as $value) {
                $dato[] = $value->descripcion;
            }

            $query = $query->where(function ($query) use ($campo) {
                foreach ($campo as $pos => $value) {
                    if ($pos === 0) {
                        $query = $query->where('datos_modificados', 'like', '%"' . $value->descripcion . '"%');
                    } else {
                        $query = $query->orWhere('datos_modificados', 'like', '%"' . $value->descripcion . '"%');
                    }
                }
            });
        }

        $bitacora_ot = $query->whereBetween('created_at', [$fromDate, $toDate])->get()->paginate(20);

        $id_cambios = BitacoraWorkOrder::where('work_order_id', $id)->get();
        $id_cambios->map(function ($id_cambio) {
            $id_cambio->cambio_id = $id_cambio->id;
        });

        $descripciones = BitacoraWorkOrder::where('work_order_id', $id)->where('operacion', '<>', 'Inserción')->select('observacion')->distinct()->get();
        $descripciones->map(function ($descripcion) {
            $descripcion->descripcion_id = $descripcion->id;
        });

        $campos_modificados = DB::Table('bitacora_campos_modificados')->select(DB::RAW('MIN(id) as id ,descripcion as descripcion'))
            ->where('active', 1)
            ->groupBy('descripcion') // Agrupar por name
            ->get();
        $campos_modificados->map(function ($campo) {
            $campo->campo_id = $campo->id;
        });

        $users = User::select('users.id', 'users.nombre', 'users.apellido', 'bitacora_work_orders.user_id')
            ->join('bitacora_work_orders', 'users.id', '=', 'bitacora_work_orders.user_id')
            ->where('bitacora_work_orders.work_order_id', $id)
            ->distinct()
            ->get();

        $users->map(function ($user) {
            $user->user_id = $user->id;
        });
        // Si se solicito exportar
        if (!is_null(request()->input('exportar'))) {
            // dd($fromDate, $toDate);
            $this->descargarDetalleLogExcel($ot_id);
        }
        // Dates Format
        $fromDate = Carbon::now()->startOfMonth()->subMonth()->format('d/m/Y');
        $toDate = Carbon::now()->format('d/m/Y');

        return view('work-orders.detalle-log-ot', compact('ot_id', 'fromDate', 'toDate', 'bitacora_ot', 'id_cambios', 'descripciones', 'descripcion_filter', 'campos_modificados', 'users', 'dato'));
    }

    public function descargarDetalleLogExcel($id)
    {
        $ot_id = $id;

        // Filtro por fechas
        if (!is_null(request()->input('date_desde')) and !is_null(request()->input('date_hasta'))) {
            $fromDate = Carbon::createFromFormat('d/m/Y', request()->input('date_desde'))->toDateString();
            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
        } else {
            $fromDate = Carbon::now()->startOfMonth()->subMonth()->toDateString();
            $toDate = Carbon::tomorrow()->toDateString();
        }

        $query = BitacoraWorkOrder::where('work_order_id', $id)
            ->whereIn('operacion', array('Mckee', 'Modificación'))
            ->whereNotNull('datos_modificados')
            ->orderBy("id", "desc");

        // filtro por ID especifico
        if (!is_null(request()->query('cambio_id'))) {
            $query = $query->whereIn('id', request()->query('cambio_id'));
        }

        // filtro por Usuario que hizo la modificacion
        if (!is_null(request()->query('user_id'))) {
            $query = $query->whereIn('user_id', request()->query('user_id'));
        }


        // filtro por la descripcion del cambio
        if (!is_null(request()->query('descripcion'))) {
            $query = $query->whereIn('observacion', request()->query('descripcion'));
        }

        // filtro por los campos asignados del cambio
        $dato = null;
        if (!is_null(request()->query('campo_id'))) {
            $campo = BitacoraCamposModificados::whereIn('id', request()->query('campo_id'))->get();
            $dato = [];

            foreach ($campo as $value) {
                $dato[] = $value->descripcion;
            }

            $query = $query->where(function ($query) use ($campo) {
                foreach ($campo as $pos => $value) {
                    if ($pos === 0) {
                        $query = $query->where('datos_modificados', 'like', '%"' . $value->descripcion . '"%');
                    } else {
                        $query = $query->orWhere('datos_modificados', 'like', '%"' . $value->descripcion . '"%');
                    }
                }
            });
        }


        $bitacora_ot = $query->whereBetween('created_at', [$fromDate, $toDate])->get();

        // $data_array = [];
        $data_array[] = array(
            "OT",
            "ID Cambio",
            "Fecha",
            "Descripción",
            "Campo Modificado",
            "Valor Antiguo",
            "Valor Nuevo",
            "Usuario"
        );

        if ($dato == null) {

            foreach ($bitacora_ot as $bitacora) {
                foreach ($bitacora->datos_modificados as $key => $value) {
                    if ($bitacora->operacion == 'Modificación') {
                        $data_array[] = [
                            'OT ' => $bitacora->work_order_id,
                            'ID Cambio' => $bitacora->id,
                            'Fecha' => $bitacora->created_at,
                            'Descripción' => $bitacora->observacion,
                            'Campo Modificado' => $value['texto'],
                            'Valor Antiguo' => $value['antiguo_valor']['descripcion'] ? $value['antiguo_valor']['descripcion'] : 'Campo Vacío',
                            'Valor Nuevo' => $value['nuevo_valor']['descripcion']  ? $value['nuevo_valor']['descripcion'] : 'Campo Vacío',
                            'Usuario' => $bitacora->user_data['nombre'] . ' ' . $bitacora->user_data['apellido'],
                        ];
                    } else {
                        $data_array[] = [
                            'OT ' => $bitacora->work_order_id,
                            'ID Cambio' => $bitacora->id,
                            'Fecha' => $bitacora->created_at,
                            'Descripción' => $bitacora->observacion,
                            'Campo Modificado' => $value['texto'],
                            'Valor Antiguo' => 'N/A',
                            'Valor Nuevo' => $value['valor']['descripcion'],
                            'Usuario' => $bitacora->user_data['nombre'] . ' ' . $bitacora->user_data['apellido'],
                        ];
                    }
                }
            }
        } else {

            foreach ($bitacora_ot as $bitacora) {
                foreach ($bitacora->datos_modificados as $key => $value) {

                    if (in_array($value['texto'], $dato)) {
                        if ($bitacora->operacion == 'Modificación') {
                            $data_array[] = [
                                'OT ' => $bitacora->work_order_id,
                                'ID Cambio' => $bitacora->id,
                                'Fecha' => $bitacora->created_at,
                                'Descripción' => $bitacora->observacion,
                                'Campo Modificado' => $value['texto'],
                                'Valor Antiguo' => $value['antiguo_valor']['descripcion'] ? $value['antiguo_valor']['descripcion'] : 'Campo Vacío',
                                'Valor Nuevo' => $value['nuevo_valor']['descripcion']  ? $value['nuevo_valor']['descripcion'] : 'Campo Vacío',
                                'Usuario' => $bitacora->user_data['nombre'] . ' ' . $bitacora->user_data['apellido'],
                            ];
                        } else {
                            $data_array[] = [
                                'OT ' => $bitacora->work_order_id,
                                'ID Cambio' => $bitacora->id,
                                'Fecha' => $bitacora->created_at,
                                'Descripción' => $bitacora->observacion,
                                'Campo Modificado' => $value['texto'],
                                'Valor Antiguo' => 'N/A',
                                'Valor Nuevo' => $value['valor']['descripcion'],
                                'Usuario' => $bitacora->user_data['nombre'] . ' ' . $bitacora->user_data['apellido'],
                            ];
                        }
                    }
                }
            }
        }
        $titulo = 'Detalle Log Ot' . ' ' . $ot_id;

        Excel::create($titulo, function ($excel) use ($data_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet($titulo, function ($sheet) use ($data_array) {
                $sheet->fromArray($data_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    public function detalleMckee()
    {
        if (!isset($_GET['ot_id'])) {
            return response()->json(['error' => 'ot_id requerido'], 400);
        }
        $ot_id = $_GET['ot_id'];
        $html = '';

        // $bitacora_ot = BitacoraWorkOrder::where('work_order_id' , $id)->where('id', 287)->whereNotNull('datos_modificados')->orderBy("id", "desc")->get()->paginate(20);

        $query = BitacoraWorkOrder::where('work_order_id', $ot_id)
            ->where('operacion', 'Mckee')
            ->whereNotNull('datos_modificados')
            ->orderBy("id", "desc");

        $bitacora_ot = $query->get();
        $data_array = [];
        $i = 0;
        foreach ($bitacora_ot as $bitacora) {

            foreach ($bitacora->datos_modificados as $key => $value) {

                $data_array[$i][] = $value['valor']['descripcion'];
            }

            $data_array[$i][] = $bitacora->user_data['nombre'] . ' ' . $bitacora->user_data['apellido'];
            $i++;
        }

        //dd($data_array);
        //$html = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimple($subhierarchies, 'subhierarchy_id');

        return $data_array;
    }

    public function descargarDetalleLogExcelMckee($id)
    {
        $ot_id = $id;

        $query = BitacoraWorkOrder::where('work_order_id', $id)
            ->where('operacion', 'Mckee')
            ->whereNotNull('datos_modificados')
            ->orderBy("id", "desc");

        $bitacora_ot = $query->get();

        // $data_array = [];
        $data_array[] = array(
            "Campo",
            "Valor"
        );


        foreach ($bitacora_ot as $bitacora) {
            foreach ($bitacora->datos_modificados as $key => $value) {
                $data_array[] = [
                    'Campo' => $value['texto'],
                    'Valor' => $value['valor']['descripcion'],

                ];
            }
            $data_array[] = [
                'Campo' => 'Usuario',
                'Valor' => $bitacora->user_data['nombre'] . ' ' . $bitacora->user_data['apellido'],
            ];
        }
        //dd($data_array);
        $titulo = 'Detalle Datos Mckee' . ' ' . $ot_id;

        Excel::create($titulo, function ($excel) use ($data_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet($titulo, function ($sheet) use ($data_array) {
                $sheet->fromArray($data_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    public function generar_diseño_pdf(Request $request)
    {

        $ot = WorkOrder::find(request("ot"));
        $destinationFolder = public_path('/files/');
        //$fecha_actual=date("Ymd");
        //$hora_actual=date("hi");
        if (file_exists($destinationFolder . 'OT_' . request("ot") . '_temp.pdf')) {
            unlink($destinationFolder . 'OT_' . request("ot") . '_temp.pdf');
        }

        view()->share('ot', $ot);

        $pdf = PDF::loadView('pdf.diseño_pdf', $ot)->save($destinationFolder . 'OT_' . request("ot") . '_temp.pdf');
        return $pdf->stream('OT_' . request("ot"));

        return view('pdf.diseño_pdf');
    }

    //Funcion para buscar el proveedor de la gestion de envio de archivo de diseño
    public function obtenerProveedorExternoDiseño(Request $request)
    {

        $gestion_proveedor_envio = Management::where('work_order_id', request("ot"))
            ->where('management_type_id', 9)
            ->where('recibido_diseño_externo', 0)
            ->get();
        $proveedor_id = $gestion_proveedor_envio[0]->proveedor_id;

        return response()->json(['proveedor_id' => $proveedor_id]);
    }


    public function obtenerDatosPdf(Request $request)
    {
        $parseador = new Parser();
        $documento = $parseador->parseFile($request->file('file_boceto_pdf'));
        $texto = $documento->getText();
        $texto = str_replace("\t", "", $texto);

        $cleanText = trim(preg_replace('/\t+/', '<>', $texto));
        $cleanText = trim(preg_replace('/\n+/', ' <<break>> ', $cleanText));
        $arrayText = explode(' <<break>> ', $cleanText);

        // var_dump($arrayText);
        // Obtener colores activos

        $array_colores = Color::select('id', 'descripcion')->where('active', 1)->get();

        // Detectar colores en el PDF manteniendo el orden en el documento
        $arrayValues = [];
        $data_colores_id = [];
        $data_colores_desc = [];

        foreach ($arrayText as $index => $line) { // Recorrer en orden
            foreach ($array_colores as $color) {

                if (stripos($line, $color->descripcion) !== false) {
                    // Agregar sin verificar si ya existe
                    $data_colores_id[] = $color->id;
                    $data_colores_desc[] = $color->descripcion;
                }
            }
        }

        // foreach ($arrayText as $index => $line) { // Recorrer en orden
        //     foreach ($array_colores as $color) {
        //         if (stripos($line, $color->descripcion) !== false) {
        //             // Solo agregar si no ha sido agregado antes
        //             if (!in_array($color->descripcion, $arrayValues)) {
        //                 $arrayValues[] = $color->descripcion;
        //                 $data_colores_id[] = $color->id;
        //                 $data_colores_desc[] = $color->descripcion;
        //             }
        //         }
        //     }
        // }


        // Extraer porcentajes (ignorando los que contengan "CM2%")

        $data_porcentaje = [];
        foreach ($arrayText as $line) {
            // Ignorar cualquier línea que contenga "CM2%" completa
            if (stripos($line, 'CM2%') !== false) {
                continue;
            }

            // Buscar todos los números seguidos de % estén donde estén
            if (preg_match_all('/\d{1,3}(?:[.,]\d+)?%/', $line, $matches)) {
                foreach ($matches[0] as $match) {
                    // Convertir a formato numérico
                    $numero = str_replace(['%', ','], ['', '.'], $match);

                    if (is_numeric($numero)) {
                        $data_porcentaje[] = (float)$numero;
                    }
                }
            }
        }

        // $data_porcentaje = [];
        // foreach ($arrayText as $line) {
        //     if (stripos($line, 'CM2%') !== false) {
        //         continue; // Ignorar porcentajes que contengan "CM2%"
        //     }

        //     // Expresión regular mejorada para detectar números con ',' o '.'
        //     if (preg_match_all('/\b\d{1,3}(?:[.,]\d+)?%/', $line, $matches)) {
        //         foreach ($matches[0] as $match) {
        //             // Eliminar el símbolo de porcentaje
        //             $numero = str_replace('%', '', $match);

        //             // Normalizar formato (convertir comas en puntos si son decimales)
        //             $numero = str_replace(',', '.', $numero);

        //             if (is_numeric($numero)) {
        //                 $data_porcentaje[] = (float)$numero;
        //             }
        //         }
        //     }
        // }

        // Extraer valores de cm²
        $data_clisse_cm2 = [];
        foreach ($arrayText as $line) {
            if (preg_match_all('/\b\d{1,3}(?:[.,]\d{3})\b/', $line, $matches)) {
                foreach ($matches[0] as $match) {
                    $numero = str_replace(['.', ','], '', $match);
                    if (is_numeric($numero)) {
                        $data_clisse_cm2[] = (int)$numero;
                    }
                }
            }
        }

        return response()->json([
            'data_clisse_cm2' => $data_clisse_cm2,
            'data_porcentaje' => $data_porcentaje,
            'data_colores_id' => $data_colores_id,
            'data_colores_desc' => $data_colores_desc,
            'cant_colores' => count($data_colores_desc),
        ]);
    }




    public function store_boceto_pdf(Request $request)
    {

        $Ot_modificada = $request->input('otID'); // OT original
        $ot =  WorkOrder::find($Ot_modificada); //Buscamos los datos de la OT original


        if ($ot) {

            //Buscamos primero todos los campos de la OT de la tabla
            $campos_modificados = BitacoraCamposModificados::all()->pluck('descripcion')->toArray();

            $campos = array();
            $datos_modificados = array();
            // Compare datos para la bitacora ( antes de que se guarden )

            //Color 1
            if ($ot->color_1_id != $request->input('color_1_value')) {
                $datos_modificados['color_1_id'] = [
                    'texto' => 'Color 1',
                    'antiguo_valor' => ['id' => $ot->color_1_id, 'descripcion' => $ot->color_1_id],
                    'nuevo_valor' => ['id' => $ot->color_1_id, 'descripcion' => $request->input('color_1_value')]
                ];
            }
            if (!in_array('Color 1', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Color 1'];
            }

            //Color 2
            if ($ot->color_2_id != $request->input('color_2_value')) {
                $datos_modificados['color_2_id'] = [
                    'texto' => 'Color 2',
                    'antiguo_valor' => ['id' => $ot->color_2_id, 'descripcion' => $ot->color_2_id],
                    'nuevo_valor' => ['id' => $ot->color_2_id, 'descripcion' => $request->input('color_2_value')]
                ];
            }
            if (!in_array('Color 2', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Color 2'];
            }

            //Color 3
            if ($ot->color_3_id != $request->input('color_3_value')) {
                $datos_modificados['color_3_id'] = [
                    'texto' => 'Color 3',
                    'antiguo_valor' => ['id' => $ot->color_3_id, 'descripcion' => $ot->color_3_id],
                    'nuevo_valor' => ['id' => $ot->color_3_id, 'descripcion' => $request->input('color_3_value')]
                ];
            }
            if (!in_array('Color 3', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Color 3'];
            }

            //Color 4
            if ($ot->color_4_id != $request->input('color_4_value')) {
                $datos_modificados['color_4_id'] = [
                    'texto' => 'Color 4',
                    'antiguo_valor' => ['id' => $ot->color_4_id, 'descripcion' => $ot->color_4_id],
                    'nuevo_valor' => ['id' => $ot->color_4_id, 'descripcion' => $request->input('color_4_value')]
                ];
            }
            if (!in_array('Color 4', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Color 4'];
            }

            //Color 5
            if ($ot->color_5_id != $request->input('color_5_value')) {
                $datos_modificados['color_5_id'] = [
                    'texto' => 'Color 5',
                    'antiguo_valor' => ['id' => $ot->color_5_id, 'descripcion' => $ot->color_5_id],
                    'nuevo_valor' => ['id' => $ot->color_5_id, 'descripcion' => $request->input('color_5_value')]
                ];
            }
            if (!in_array('Color 5', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Color 5'];
            }

            //Color 6
            if ($ot->color_6_id != $request->input('color_6_value')) {
                $datos_modificados['color_6_id'] = [
                    'texto' => 'Color 6',
                    'antiguo_valor' => ['id' => $ot->color_6_id, 'descripcion' => $ot->color_6_id],
                    'nuevo_valor' => ['id' => $ot->color_6_id, 'descripcion' => $request->input('color_6_value')]
                ];
            }
            if (!in_array('Color 6', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Color 6'];
            }

            //Color 7
            if ($ot->color_7_id != $request->input('color_7_value')) {
                $datos_modificados['color_7_id'] = [
                    'texto' => 'Color 7',
                    'antiguo_valor' => ['id' => $ot->color_7_id, 'descripcion' => $ot->color_7_id],
                    'nuevo_valor' => ['id' => $ot->color_7_id, 'descripcion' => $request->input('color_7_value')]
                ];
            }
            if (!in_array('Color 7', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Color 7'];
            }

            //Clisse cm2 1
            if ($ot->cm2_clisse_color_1 != $request->input('cm2_clisse_color_1_value')) {
                $datos_modificados['cm2_clisse_color_1'] = [
                    'texto' => 'Clisse cm2 1',
                    'antiguo_valor' => ['id' => $ot->cm2_clisse_color_1, 'descripcion' => $ot->cm2_clisse_color_1],
                    'nuevo_valor' => ['id' => $ot->cm2_clisse_color_1, 'descripcion' => $request->input('cm2_clisse_color_1_value')]
                ];
            }
            if (!in_array('Clisse cm2 1', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Clisse cm2 1'];
            }

            //Clisse cm2 2
            if ($ot->cm2_clisse_color_2 != $request->input('cm2_clisse_color_2_value')) {
                $datos_modificados['cm2_clisse_color_2'] = [
                    'texto' => 'Clisse cm2 2',
                    'antiguo_valor' => ['id' => $ot->cm2_clisse_color_2, 'descripcion' => $ot->cm2_clisse_color_2],
                    'nuevo_valor' => ['id' => $ot->cm2_clisse_color_2, 'descripcion' => $request->input('cm2_clisse_color_2_value')]
                ];
            }
            if (!in_array('Clisse cm2 2', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Clisse cm2 2'];
            }

            //Clisse cm2 3
            if ($ot->cm2_clisse_color_3 != $request->input('cm2_clisse_color_3_value')) {
                $datos_modificados['cm2_clisse_color_3'] = [
                    'texto' => 'Clisse cm2 3',
                    'antiguo_valor' => ['id' => $ot->cm2_clisse_color_3, 'descripcion' => $ot->cm2_clisse_color_3],
                    'nuevo_valor' => ['id' => $ot->cm2_clisse_color_3, 'descripcion' => $request->input('cm2_clisse_color_3_value')]
                ];
            }
            if (!in_array('Clisse cm2 3', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Clisse cm2 3'];
            }

            //Clisse cm2 4
            if ($ot->cm2_clisse_color_4 != $request->input('cm2_clisse_color_4_value')) {
                $datos_modificados['cm2_clisse_color_4'] = [
                    'texto' => 'Clisse cm2 4',
                    'antiguo_valor' => ['id' => $ot->cm2_clisse_color_4, 'descripcion' => $ot->cm2_clisse_color_4],
                    'nuevo_valor' => ['id' => $ot->cm2_clisse_color_4, 'descripcion' => $request->input('cm2_clisse_color_4_value')]
                ];
            }
            if (!in_array('Clisse cm2 4', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Clisse cm2 4'];
            }

            //Clisse cm2 5
            if ($ot->cm2_clisse_color_5 != $request->input('cm2_clisse_color_5_value')) {
                $datos_modificados['cm2_clisse_color_5'] = [
                    'texto' => 'Clisse cm2 5',
                    'antiguo_valor' => ['id' => $ot->cm2_clisse_color_5, 'descripcion' => $ot->cm2_clisse_color_5],
                    'nuevo_valor' => ['id' => $ot->cm2_clisse_color_5, 'descripcion' => $request->input('cm2_clisse_color_5_value')]
                ];
            }
            if (!in_array('Clisse cm2 5', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Clisse cm2 5'];
            }

            //Clisse cm2 6
            if ($ot->cm2_clisse_color_6 != $request->input('cm2_clisse_color_6_value')) {
                $datos_modificados['cm2_clisse_color_6'] = [
                    'texto' => 'Clisse cm2 6',
                    'antiguo_valor' => ['id' => $ot->cm2_clisse_color_6, 'descripcion' => $ot->cm2_clisse_color_6],
                    'nuevo_valor' => ['id' => $ot->cm2_clisse_color_6, 'descripcion' => $request->input('cm2_clisse_color_6_value')]
                ];
            }
            if (!in_array('Clisse cm2 6', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Clisse cm2 6'];
            }

            //Clisse cm2 7
            if ($ot->cm2_clisse_color_7 != $request->input('cm2_clisse_color_7_value')) {
                $datos_modificados['cm2_clisse_color_7'] = [
                    'texto' => 'Clisse cm2 7',
                    'antiguo_valor' => ['id' => $ot->cm2_clisse_color_7, 'descripcion' => $ot->cm2_clisse_color_7],
                    'nuevo_valor' => ['id' => $ot->cm2_clisse_color_7, 'descripcion' => $request->input('cm2_clisse_color_7_value')]
                ];
            }
            if (!in_array('Clisse cm2 7', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Clisse cm2 7'];
            }



            //PORCENTAJE 1
            if ($ot->impresion_1 != $request->input('impresion_1_value')) {
                $datos_modificados['impresion_1'] = [
                    'texto' => 'Impresion 1',
                    'antiguo_valor' => ['id' => $ot->impresion_1, 'descripcion' => $ot->impresion_1],
                    'nuevo_valor' => ['id' => $ot->impresion_1, 'descripcion' => $request->input('impresion_1_value')]
                ];
            }
            if (!in_array('Impresion 1', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Impresion 1'];
            }

            //PORCENTAJ 2
            if ($ot->impresion_2 != $request->input('impresion_2_value')) {
                $datos_modificados['impresion_2'] = [
                    'texto' => 'Impresion 2',
                    'antiguo_valor' => ['id' => $ot->impresion_2, 'descripcion' => $ot->impresion_2],
                    'nuevo_valor' => ['id' => $ot->impresion_2, 'descripcion' => $request->input('impresion_2_value')]
                ];
            }
            if (!in_array('Impresion 2', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Impresion 2'];
            }

            //PORCENTAJ 3
            if ($ot->impresion_3 != $request->input('impresion_3_value')) {
                $datos_modificados['impresion_3'] = [
                    'texto' => 'Impresion 3',
                    'antiguo_valor' => ['id' => $ot->impresion_3, 'descripcion' => $ot->impresion_3],
                    'nuevo_valor' => ['id' => $ot->impresion_3, 'descripcion' => $request->input('impresion_3_value')]
                ];
            }
            if (!in_array('Impresion 2', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Impresion 2'];
            }

            //PORCENTAJ 4
            if ($ot->impresion_4 != $request->input('impresion_4_value')) {
                $datos_modificados['impresion_2'] = [
                    'texto' => 'Impresion 4',
                    'antiguo_valor' => ['id' => $ot->impresion_4, 'descripcion' => $ot->impresion_4],
                    'nuevo_valor' => ['id' => $ot->impresion_4, 'descripcion' => $request->input('impresion_4_value')]
                ];
            }
            if (!in_array('Impresion 4', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Impresion 4'];
            }

            //PORCENTAJ 5
            if ($ot->impresion_5 != $request->input('impresion_5_value')) {
                $datos_modificados['impresion_5'] = [
                    'texto' => 'Impresion 2',
                    'antiguo_valor' => ['id' => $ot->impresion_5, 'descripcion' => $ot->impresion_5],
                    'nuevo_valor' => ['id' => $ot->impresion_5, 'descripcion' => $request->input('impresion_5_value')]
                ];
            }
            if (!in_array('Impresion 5', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Impresion 5'];
            }

            //PORCENTAJ 6
            if ($ot->impresion_6 != $request->input('impresion_6_value')) {
                $datos_modificados['impresion_6'] = [
                    'texto' => 'Impresion 2',
                    'antiguo_valor' => ['id' => $ot->impresion_6, 'descripcion' => $ot->impresion_6],
                    'nuevo_valor' => ['id' => $ot->impresion_6, 'descripcion' => $request->input('impresion_6_value')]
                ];
            }
            if (!in_array('Impresion 6', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Impresion 6'];
            }

            //PORCENTAJ 7
            if ($ot->impresion_7 != $request->input('impresion_7_value')) {
                $datos_modificados['impresion_7'] = [
                    'texto' => 'Impresion 7',
                    'antiguo_valor' => ['id' => $ot->impresion_7, 'descripcion' => $ot->impresion_7],
                    'nuevo_valor' => ['id' => $ot->impresion_7, 'descripcion' => $request->input('impresion_7_value')]
                ];
            }
            if (!in_array('Impresion 7', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Impresion 7'];
            }


            //Total Clisse cm2
            if ($ot->total_cm2_clisse != $request->input('total_cm2_clisse_value')) {
                $datos_modificados['total_cm2_clisse'] = [
                    'texto' => 'Total Clisse cm2',
                    'antiguo_valor' => ['id' => $ot->total_cm2_clisse, 'descripcion' => $ot->total_cm2_clisse],
                    'nuevo_valor' => ['id' => $ot->total_cm2_clisse, 'descripcion' => $request->input('total_cm2_clisse_value')]
                ];
            }
            if (!in_array('Total Clisse cm2', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Total Clisse cm2'];
            }


            //Guardamos los input del formulario
            $ot->color_1_id = (trim($request->input('color_1_value')) != '') ? $request->input('color_1_value') : null;
            $ot->color_2_id = (trim($request->input('color_2_value')) != '') ? $request->input('color_2_value') : null;
            $ot->color_3_id = (trim($request->input('color_3_value')) != '') ? $request->input('color_3_value') : null;
            $ot->color_4_id = (trim($request->input('color_4_value')) != '') ? $request->input('color_4_value') : null;
            $ot->color_5_id = (trim($request->input('color_5_value')) != '') ? $request->input('color_5_value') : null;
            $ot->color_6_id = (trim($request->input('color_6_value')) != '') ? $request->input('color_6_value') : null;
            $ot->color_7_id = (trim($request->input('color_7_value')) != '') ? $request->input('color_7_value') : null;

            $ot->impresion_1 = (trim($request->input('impresion_1_value')) != '') ? $request->input('impresion_1_value') : null;
            $ot->impresion_2 = (trim($request->input('impresion_2_value')) != '') ? $request->input('impresion_2_value') : null;
            $ot->impresion_3 = (trim($request->input('impresion_3_value')) != '') ? $request->input('impresion_3_value') : null;
            $ot->impresion_4 = (trim($request->input('impresion_4_value')) != '') ? $request->input('impresion_4_value') : null;
            $ot->impresion_5 = (trim($request->input('impresion_5_value')) != '') ? $request->input('impresion_5_value') : null;
            $ot->impresion_6 = (trim($request->input('impresion_6_value')) != '') ? $request->input('impresion_6_value') : null;
            $ot->impresion_7 = (trim($request->input('impresion_7_value')) != '') ? $request->input('impresion_7_value') : null;

            $ot->cm2_clisse_color_1 = (trim($request->input('cm2_clisse_color_1_value')) != '') ? $request->input('cm2_clisse_color_1_value') : null;
            $ot->cm2_clisse_color_2 = (trim($request->input('cm2_clisse_color_2_value')) != '') ? $request->input('cm2_clisse_color_2_value') : null;
            $ot->cm2_clisse_color_3 = (trim($request->input('cm2_clisse_color_3_value')) != '') ? $request->input('cm2_clisse_color_3_value') : null;
            $ot->cm2_clisse_color_4 = (trim($request->input('cm2_clisse_color_4_value')) != '') ? $request->input('cm2_clisse_color_4_value') : null;
            $ot->cm2_clisse_color_5 = (trim($request->input('cm2_clisse_color_5_value')) != '') ? $request->input('cm2_clisse_color_5_value') : null;
            $ot->cm2_clisse_color_6 = (trim($request->input('cm2_clisse_color_6_value')) != '') ? $request->input('cm2_clisse_color_6_value') : null;
            $ot->cm2_clisse_color_7 = (trim($request->input('cm2_clisse_color_7_value')) != '') ? $request->input('cm2_clisse_color_7_value') : null;
            $ot->total_cm2_clisse   = (trim($request->input('total_cm2_clisse_value')) != '') ? $request->input('total_cm2_clisse_value') : null;

            $ot->save();

            //Guarda la gestion identificada como lector PDF
            $gestion = new Management();
            // $gestion->titulo = request('titulo');
            $gestion->observacion = 'Subido archivo con Boceto Clisse PDF';
            $gestion->management_type_id = 7;
            $gestion->user_id = auth()->user()->id;
            $gestion->work_order_id = $Ot_modificada;
            $gestion->work_space_id =  $ot->current_area_id;
            $gestion->save();


            //Guardamos el archivo que lee el PDF
            if ($request->hasfile('file_boceto_pdf')) {
                $file = new File();
                $file_pdf = $request->file('file_boceto_pdf');

                $filename = pathinfo($file_pdf->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = pathinfo($file_pdf->getClientOriginalName(), PATHINFO_EXTENSION);
                $name = $filename;
                $peso = $this->human_filesize($file_pdf->getSize());
                $tipo_archivo = $this->tipo_archivo($extension);
                $destinationFolder = public_path('/files/');
                $file_pdf->move($destinationFolder, $name . '.' . $extension);
                $file->url = '/files/' . $name . '.' . $extension;
                $file->peso = round($peso[0]);
                $file->unidad = $peso[1];
                $file->tipo = $tipo_archivo;
                $file->management_id = $gestion->id;
                $file->save();
            }

            if (count($datos_modificados) > 0) { //Verificamos si se cambio algun valor para guardar
                //Se guarda registro en la tabla de bitacora
                $bitacora = new BitacoraWorkOrder();
                $user_auth = Auth()->user();
                $bitacora->observacion = "Subida de Archivo PDF con boceto clisse PDF";
                $bitacora->operacion = 'Modificación'; //Tipo modificacion
                $bitacora->work_order_id = $ot->id;
                $bitacora->user_id = $user_auth->id;
                $user_data = array(
                    'nombre' => $user_auth->nombre,
                    'apellido' => $user_auth->apellido,
                    'rut' => $user_auth->rut,
                    'role_id' => $user_auth->role_id,
                );
                $bitacora->user_data = json_encode($user_data, JSON_UNESCAPED_UNICODE); //Se agrega JSON_UNESCAPED_UNICODE para mantener el formato de las palabras con acentos
                $bitacora->datos_modificados = json_encode($datos_modificados, JSON_UNESCAPED_UNICODE);
                $bitacora->ip_solicitud = \Request::getClientIp(true);
                $bitacora->url = url()->full();
                $bitacora->save();

                //se guardan los nombre de los campos que tiene la OT
                BitacoraCamposModificados::insert($campos);
            }
            return response()->json(true);
        } else {
            return response()->json(false);
        }
    }
}
