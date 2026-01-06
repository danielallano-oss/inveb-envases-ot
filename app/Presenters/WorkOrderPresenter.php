<?php

namespace App\Presenters;

use App\WorkOrder;
use App\Management;
use App\Proveedor;
use App\SystemVariable;
use Illuminate\Support\Carbon;

class WorkOrderPresenter
{
    protected $ot;

    function __construct(WorkOrder $ot)
    {
        $this->ot = $ot;
        //Obtenemos el Horario de Trabajo definido en la tabla de variables del sistema
        /*$this->horario=SystemVariable::where('name','Horario')
                                        ->where('deleted',0)
                                        ->first();
        $this->horario=explode(',',$this->horario->contents);

        $this->ini_time_array=explode(':',$this->horario[0]);
        $this->end_time_array=explode(':',$this->horario[1]);*/
    }

    public function client()
    {
        return $this->ot->client->id;
    }

    public function profesionalAsignado()
    {
        if (Auth()->user()->isJefeVenta()) {
            if (!empty($this->ot->vendedorAsignado)) {

                return $this->ot->vendedorAsignado->user->fullname;
            } else return "N/A";
        } elseif (Auth()->user()->isJefeDesarrollo()) {

            if (!empty($this->ot->ingenieroAsignado)) {

                return $this->ot->ingenieroAsignado->user->fullname;
            } else return "N/A";
        } elseif (Auth()->user()->isJefeDiseño()) {
            if (!empty($this->ot->diseñadorAsignado)) {
                return $this->ot->diseñadorAsignado->user->fullname;
            } else return "N/A";
        } elseif (Auth()->user()->isJefeCatalogador()) {
            if (!empty($this->ot->catalogadorAsignado)) {
                return $this->ot->catalogadorAsignado->user->fullname;
            } else return "N/A";
        } elseif (Auth()->user()->isJefeMuestras()) {
            if (!empty($this->ot->tecnicoMuestrasAsignado)) {
                return $this->ot->tecnicoMuestrasAsignado->user->fullname;
            } else return "N/A";
        }
    }

    // Tiempos para listado de ordenes de trabajo por area
    public function tiempoVenta()
    {
        $vendedorAsignado = isset($this->ot->vendedorAsignado) ? $this->ot->vendedorAsignado->user->fullname : "Sin Asignar";
        if ($this->ot->current_area_id == 1 || $this->ot->current_area_id == 21) {
            //dd($this->ot->tiempo_venta);
            // Si esta en el area y el estado no es alguno que pause los tiempos se calcula el tiempo transcurrido hasta $NOW
            if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13, 20])) {
                return $this->tiempoAreaActualDiasTrabajados($this->ot->tiempo_venta, $vendedorAsignado, 1);
            } else {

                // Si es area actual y esta  en estado pausado mostrar area con tiempo hasta hoy
                return $this->tiempoPausado($this->ot->tiempo_venta, $vendedorAsignado, 1);
            }
        }
        return $this->tiempoArea($this->ot->tiempo_venta, $vendedorAsignado, 1);
    }
    public function tiempoVentaValue()
    {
        $vendedorAsignado = isset($this->ot->vendedorAsignado) ? $this->ot->vendedorAsignado->user->fullname : "Sin Asignar";
        if ($this->ot->current_area_id == 1 || $this->ot->current_area_id == 21) {

            // Si esta en el area y el estado no es alguno que pause los tiempos se calcula el tiempo transcurrido hasta $NOW
            if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13, 20])) {
                return $this->tiempoAreaActualDiasTrabajadosValue($this->ot->tiempo_venta, $vendedorAsignado, 1);
            } else {

                // Si es area actual y esta en estado pausado mostrar area con tiempo hasta hoy
                return $this->tiempoPausadoValue($this->ot->tiempo_venta, $vendedorAsignado, 1);
            }
        }
        return $this->tiempoAreaValue($this->ot->tiempo_venta, $vendedorAsignado, 1);
    }
    public function tiempoDesarrollo()
    {

        $ingenieroAsignado = isset($this->ot->ingenieroAsignado) ? $this->ot->ingenieroAsignado->user->fullname : "Sin Asignar";
        if ($this->ot->current_area_id == 2) {
            // Si esta en el area y el estado no es alguno que pause los tiempos se calcula el tiempo transcurrido hasta $NOW
            if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13, 20, 21])) {
                return $this->tiempoAreaActualDiasTrabajados($this->ot->tiempo_desarrollo, $ingenieroAsignado, 2);
            } else {
                // Si es area actual y esta en estado pausado mostrar area con tiempo hasta hoy
                return $this->tiempoPausado($this->ot->tiempo_desarrollo, $ingenieroAsignado, 2);
            }
        }
        // var_dump($this->ot);
        return $this->tiempoArea($this->ot->tiempo_desarrollo, $ingenieroAsignado, 2);
    }
    public function tiempoDesarrolloValue()
    {

        $ingenieroAsignado = isset($this->ot->ingenieroAsignado) ? $this->ot->ingenieroAsignado->user->fullname : "Sin Asignar";
        if ($this->ot->current_area_id == 2) {
            // Si esta en el area y el estado no es alguno que pause los tiempos se calcula el tiempo transcurrido hasta $NOW
            if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13, 20, 21])) {
                return $this->tiempoAreaActualDiasTrabajadosValue($this->ot->tiempo_desarrollo, $ingenieroAsignado, 2);
            } else {
                // Si es area actual y esta en estado pausado mostrar area con tiempo hasta hoy
                return $this->tiempoPausadoValue($this->ot->tiempo_desarrollo, $ingenieroAsignado, 2);
            }
        }
        return $this->tiempoAreaValue($this->ot->tiempo_desarrollo, $ingenieroAsignado, 2);
    }
    public function tiempoDiseño()
    {
        $diseñadorAsignado = isset($this->ot->diseñadorAsignado) ? $this->ot->diseñadorAsignado->user->fullname : "Sin Asignar";
        if ($this->ot->current_area_id == 3) {
            // Si esta en el area y el estado no es alguno que pause los tiempos se calcula el tiempo transcurrido hasta $NOW
            if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13, 20, 21])) {
                return $this->tiempoAreaActualDiasTrabajados($this->ot->tiempo_diseño, $diseñadorAsignado, 3);
            } else {
                // Si es area actual y esta en estado pausado mostrar area con tiempo hasta hoy
                return $this->tiempoPausado($this->ot->tiempo_diseño, $diseñadorAsignado, 3);
            }
        }
        return $this->tiempoArea($this->ot->tiempo_diseño, $diseñadorAsignado, 3);
    }
    public function tiempoDiseñoValue()
    {
        $diseñadorAsignado = isset($this->ot->diseñadorAsignado) ? $this->ot->diseñadorAsignado->user->fullname : "Sin Asignar";
        if ($this->ot->current_area_id == 3) {
            // Si esta en el area y el estado no es alguno que pause los tiempos se calcula el tiempo transcurrido hasta $NOW
            if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13, 20, 21])) {
                return $this->tiempoAreaActualDiasTrabajadosValue($this->ot->tiempo_diseño, $diseñadorAsignado, 3);
            } else {
                // Si es area actual y esta en estado pausado mostrar area con tiempo hasta hoy
                return $this->tiempoPausadoValue($this->ot->tiempo_diseño, $diseñadorAsignado, 3);
            }
        }
        return $this->tiempoAreaValue($this->ot->tiempo_diseño, $diseñadorAsignado, 3);
    }
    public function tiempoCatalogacion()
    {
        $catalogadorAsignado = isset($this->ot->catalogadorAsignado) ? $this->ot->catalogadorAsignado->user->fullname : "Sin Asignar";
        if ($this->ot->current_area_id == 4) {
            // Si esta en el area y el estado no es alguno que pause los tiempos se calcula el tiempo transcurrido hasta $NOW
            if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13, 20, 21])) {
                return $this->tiempoAreaActualDiasTrabajados($this->ot->tiempo_catalogacion, $catalogadorAsignado, 4);
            } else {
                // Si es area actual y esta en estado pausado mostrar area con tiempo hasta hoy
                return $this->tiempoPausado($this->ot->tiempo_catalogacion, $catalogadorAsignado, 4);
            }
        }
        return $this->tiempoArea($this->ot->tiempo_catalogacion, $catalogadorAsignado, 4);
    }
    public function tiempoCatalogacionValue()
    {
        $catalogadorAsignado = isset($this->ot->catalogadorAsignado) ? $this->ot->catalogadorAsignado->user->fullname : "Sin Asignar";
        if ($this->ot->current_area_id == 4) {
            // Si esta en el area y el estado no es alguno que pause los tiempos se calcula el tiempo transcurrido hasta $NOW
            if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13, 20, 21])) {
                return $this->tiempoAreaActualDiasTrabajadosValue($this->ot->tiempo_catalogacion, $catalogadorAsignado, 4);
            } else {
                // Si es area actual y esta en estado pausado mostrar area con tiempo hasta hoy
                return $this->tiempoPausadoValue($this->ot->tiempo_catalogacion, $catalogadorAsignado, 4);
            }
        }
        return $this->tiempoAreaValue($this->ot->tiempo_catalogacion, $catalogadorAsignado, 4);
    }
    public function tiempoPrecatalogacion()
    {

        $precatalogadorAsignado = isset($this->ot->catalogadorAsignado) ? $this->ot->catalogadorAsignado->user->fullname : "Sin Asignar";
        if ($this->ot->current_area_id == 5) {

            // Si esta en el area y el estado no es alguno que pause los tiempos se calcula el tiempo transcurrido hasta $NOW
            if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13, 20, 21])) {
                return $this->tiempoAreaActualDiasTrabajados($this->ot->tiempo_precatalogacion, $precatalogadorAsignado, 5);
            } else {
                // Si es area actual y esta en estado pausado mostrar area con tiempo hasta hoy
                return $this->tiempoPausado($this->ot->tiempo_precatalogacion, $precatalogadorAsignado, 5);
            }
        }
        return $this->tiempoArea($this->ot->tiempo_precatalogacion, $precatalogadorAsignado, 5);
    }
    public function tiempoPrecatalogacionValue()
    {

        $precatalogadorAsignado = isset($this->ot->catalogadorAsignado) ? $this->ot->catalogadorAsignado->user->fullname : "Sin Asignar";
        if ($this->ot->current_area_id == 5) {

            // Si esta en el area y el estado no es alguno que pause los tiempos se calcula el tiempo transcurrido hasta $NOW
            if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13, 20, 21])) {
                return $this->tiempoAreaActualDiasTrabajadosValue($this->ot->tiempo_precatalogacion, $precatalogadorAsignado, 5);
            } else {
                // Si es area actual y esta en estado pausado mostrar area con tiempo hasta hoy
                return $this->tiempoPausadoValue($this->ot->tiempo_precatalogacion, $precatalogadorAsignado, 5);
            }
        }
        return $this->tiempoAreaValue($this->ot->tiempo_precatalogacion, $precatalogadorAsignado, 5);
    }
    public function tiempoMuestra()
    {

        $tecnicoMuestrasAsignado = isset($this->ot->tecnicoMuestrasAsignado) ? $this->ot->tecnicoMuestrasAsignado->user->fullname : "Sin Asignar";
        if ($this->ot->current_area_id == 6) {
            // Si esta en el area y el estado no es alguno que pause los tiempos se calcula el tiempo transcurrido hasta $NOW
            if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13, 20, 21])) {

                return $this->tiempoAreaActualDiasTrabajadosMuestra($this->ot->tiempo_muestra, $tecnicoMuestrasAsignado, 6);
            } else {
                // Si es area actual y esta en estado pausado mostrar area con tiempo hasta hoy
                return $this->tiempoPausado($this->ot->tiempo_muestra, $tecnicoMuestrasAsignado, 2);
            }
        }
        return $this->tiempoArea($this->ot->tiempo_muestra, $tecnicoMuestrasAsignado, 2);
    }
    public function tiempoMuestraValue()
    {

        $tecnicoMuestrasAsignado = isset($this->ot->tecnicoMuestrasAsignado) ? $this->ot->tecnicoMuestrasAsignado->user->fullname : "Sin Asignar";
        if ($this->ot->current_area_id == 6) {
            // Si esta en el area y el estado no es alguno que pause los tiempos se calcula el tiempo transcurrido hasta $NOW
            if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13, 20, 21])) {

                return $this->tiempoAreaActualDiasTrabajadosMuestraValue($this->ot->tiempo_muestra, $tecnicoMuestrasAsignado, 6);
            } else {
                // Si es area actual y esta en estado pausado mostrar area con tiempo hasta hoy
                return $this->tiempoPausadoValue($this->ot->tiempo_muestra, $tecnicoMuestrasAsignado, 2);
            }
        }
        return $this->tiempoAreaValue($this->ot->tiempo_muestra, $tecnicoMuestrasAsignado, 2);
    }

    // SUMATORIA TOTAL DE AREAS
    public function tiempoTotal()
    {

        $diasTrabajados = floor(floatval($this->tiempoMuestraValue()) + floatval($this->tiempoVentaValue()) + floatval($this->tiempoDesarrolloValue()) + floatval($this->tiempoDiseñoValue()) + floatval($this->tiempoCatalogacionValue()) + floatval($this->tiempoPrecatalogacionValue()));
        //Se comenta para el calculo del tiempo total debido al cambio de horario por departamentos
        /*if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13])) {

            $diasTrabajados = floor((($this->ot->tiempo_total / 3600) + get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 9.5);
        } else {
            $diasTrabajados = floor(($this->ot->tiempo_total / 3600) / 9.5);
        }*/
        /*if($this->ot->id==8612){
            dd($diasTrabajados);
        }*/
        // 99 codigo para tiempo total
        $badgeColor = $this->badgeColor(99, $diasTrabajados);
        return '<div class="pill-status" ><div style="font-size: 1em;border-radius: 10rem;margin: 0 auto;" class="badge badge-' . $badgeColor . '">' . $diasTrabajados . '</div></div>';
    }

    public function tiempoAsignacion()
    {
        $diasTrabajados =  floor((get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 9.5);

        return '<div class="pill-status" ><div style="font-size: 1em;border-radius: 10rem;margin: 0 auto;" >' . $diasTrabajados . '</div></div>';
    }

    public function tiempoAsignacion2()
    {
        $diasTrabajados =  floor((get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 24);

        return '<div class="pill-status" ><div style="font-size: 1em;border-radius: 10rem;margin: 0 auto;" >' . $diasTrabajados . '</div></div>';
    }

    public function tiempoAreaActualDiasTrabajados($tiempoArea, $usuarioAsignado, $area)
    {
        //$date = Carbon::now();
        //$date->addDays(1);
        //dd($tiempoArea / 3600);
        // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
        //Formula anterior es sin decimal
        $diasTrabajados_formula_anterior = floor((($tiempoArea / 3600) + get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 9.5);

        if ($diasTrabajados_formula_anterior < 100) {

            $diasTrabajados = round((($tiempoArea / 3600) + get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 9.5, 1);
        } else {
            $diasTrabajados = $diasTrabajados_formula_anterior;
        }
        $badgeColor = $this->badgeColor($area, $diasTrabajados);
        return '<div class="pill-status border-success" title="' . $usuarioAsignado . '" data-toggle="tooltip">' . $this->ot->ultimo_cambio_area->format('d/m/y') . '<div class="badge badge-' . $badgeColor . '">' . $diasTrabajados . '</div></div>';
    }

    // public function tiempoAreaActualDiasTrabajados2($tiempoArea, $usuarioAsignado, $area)
    // {
    //     //$date = Carbon::now();
    //     //$date->addDays(1);
    //     //dd($tiempoArea / 3600);
    //     // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
    //     //Formula anterior es sin decimal
    //     $diasTrabajados_formula_anterior = floor((($tiempoArea / 3600) + get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 24);

    //     if ($diasTrabajados_formula_anterior < 100) {

    //         $diasTrabajados = round((($tiempoArea / 3600) + get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 24, 1);
    //     } else {
    //         $diasTrabajados = $diasTrabajados_formula_anterior;
    //     }

    //     $badgeColor = $this->badgeColor($area, $diasTrabajados);
    //     return '<div class="pill-status border-success" title="' . $usuarioAsignado . '" data-toggle="tooltip">' . $this->ot->ultimo_cambio_area->format('d/m/y') . '<div class="badge badge-' . $badgeColor . '">' . $diasTrabajados . '</div></div>';
    // }

    public function tiempoAreaActualDiasTrabajadosValue($tiempoArea, $usuarioAsignado, $area)
    {
        // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
        //Formula anterior es sin decimal
        $diasTrabajados_formula_anterior = floor((($tiempoArea / 3600) + get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 9.5);

        if ($diasTrabajados_formula_anterior < 100) {

            $diasTrabajados = round((($tiempoArea / 3600) + get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 9.5, 1);
        } else {
            $diasTrabajados = $diasTrabajados_formula_anterior;
        }

        $badgeColor = $this->badgeColor($area, $diasTrabajados);
        return $diasTrabajados;
    }

    // public function tiempoAreaActualDiasTrabajadosValue2($tiempoArea, $usuarioAsignado, $area)
    // {
    //     // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
    //     //Formula anterior es sin decimal
    //     $diasTrabajados_formula_anterior = floor((($tiempoArea / 3600) + get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 24);

    //     if ($diasTrabajados_formula_anterior < 100) {

    //         $diasTrabajados = round((($tiempoArea / 3600) + get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 24, 1);
    //     } else {
    //         $diasTrabajados = $diasTrabajados_formula_anterior;
    //     }

    //     $badgeColor = $this->badgeColor($area, $diasTrabajados);
    //     return $diasTrabajados;
    // }

    public function tiempoAreaActualDiasTrabajadosMuestra($tiempoArea, $usuarioAsignado, $area)
    {
        // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
        //Formula anterior es sin decimal
        $diasTrabajados_formula_anterior = floor((($tiempoArea / 3600) + get_working_hours_muestra($this->ot->ultimo_cambio_area, Carbon::now())) / 11.5);

        if ($diasTrabajados_formula_anterior < 100) {

            $diasTrabajados = round((($tiempoArea / 3600) + get_working_hours_muestra($this->ot->ultimo_cambio_area, Carbon::now())) / 11.5, 1);
        } else {
            $diasTrabajados = $diasTrabajados_formula_anterior;
        }

        $badgeColor = $this->badgeColor($area, $diasTrabajados);
        return '<div class="pill-status border-success" title="' . $usuarioAsignado . '" data-toggle="tooltip">' . $this->ot->ultimo_cambio_area->format('d/m/y') . '<div class="badge badge-' . $badgeColor . '">' . $diasTrabajados . '</div></div>';
    }

    // public function tiempoAreaActualDiasTrabajadosMuestra2($tiempoArea, $usuarioAsignado, $area)
    // {
    //     // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
    //     //Formula anterior es sin decimal
    //     $diasTrabajados_formula_anterior = floor((($tiempoArea / 3600) + get_working_hours_muestra($this->ot->ultimo_cambio_area, Carbon::now())) / 24);

    //     if ($diasTrabajados_formula_anterior < 100) {

    //         $diasTrabajados = round((($tiempoArea / 3600) + get_working_hours_muestra($this->ot->ultimo_cambio_area, Carbon::now())) / 24, 1);
    //     } else {
    //         $diasTrabajados = $diasTrabajados_formula_anterior;
    //     }

    //     $badgeColor = $this->badgeColor($area, $diasTrabajados);
    //     return '<div class="pill-status border-success" title="' . $usuarioAsignado . '" data-toggle="tooltip">' . $this->ot->ultimo_cambio_area->format('d/m/y') . '<div class="badge badge-' . $badgeColor . '">' . $diasTrabajados . '</div></div>';
    // }

    public function tiempoAreaActualDiasTrabajadosMuestraValue($tiempoArea, $usuarioAsignado, $area)
    {
        // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
        //Formula anterior es sin decimal
        $diasTrabajados_formula_anterior = floor((($tiempoArea / 3600) + get_working_hours_muestra($this->ot->ultimo_cambio_area, Carbon::now())) / 11.5);

        if ($diasTrabajados_formula_anterior < 100) {

            $diasTrabajados = round((($tiempoArea / 3600) + get_working_hours_muestra($this->ot->ultimo_cambio_area, Carbon::now())) / 11.5, 1);
        } else {
            $diasTrabajados = $diasTrabajados_formula_anterior;
        }

        return $diasTrabajados;
    }

    // public function tiempoAreaActualDiasTrabajadosMuestraValue2($tiempoArea, $usuarioAsignado, $area)
    // {
    //     // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
    //     //Formula anterior es sin decimal
    //     $diasTrabajados_formula_anterior = floor((($tiempoArea / 3600) + get_working_hours_muestra($this->ot->ultimo_cambio_area, Carbon::now())) / 24);

    //     if ($diasTrabajados_formula_anterior < 100) {

    //         $diasTrabajados = round((($tiempoArea / 3600) + get_working_hours_muestra($this->ot->ultimo_cambio_area, Carbon::now())) / 24, 1);
    //     } else {
    //         $diasTrabajados = $diasTrabajados_formula_anterior;
    //     }

    //     return $diasTrabajados;
    // }

    public function tiempoArea($tiempoArea, $usuarioAsignado, $area)
    {

        // var_dump($tiempoArea);
        // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
        //Formula anterior es sin decimal
        $diasTrabajados_formula_anterior = floor(($tiempoArea / 3600) / 9.5);

        // var_dump($diasTrabajados_formula_anterior);
        if ($diasTrabajados_formula_anterior < 100) {
            $diasTrabajados = round(($tiempoArea / 3600) / 9.5, 1);
        } else {
            $diasTrabajados = $diasTrabajados_formula_anterior;
        }

        $badgeColor = $this->badgeColor($area, $diasTrabajados);
        return "<div class='pill-status'  title='" . $usuarioAsignado . "' data-toggle='tooltip'><div style='font-size: 1em;border-radius: 10rem;margin-left: auto;' class='badge badge-" . $badgeColor . "'>" . $diasTrabajados . "</div></div>";
    }

    // public function tiempoArea2($tiempoArea, $usuarioAsignado, $area)
    // {


    //     // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
    //     //Formula anterior es sin decimal
    //     $diasTrabajados_formula_anterior = floor(($tiempoArea / 3600) / 24);

    //     if ($diasTrabajados_formula_anterior < 100) {
    //         $diasTrabajados = round(($tiempoArea / 3600) / 24, 1);
    //     } else {
    //         $diasTrabajados = $diasTrabajados_formula_anterior;
    //     }

    //     $badgeColor = $this->badgeColor($area, $diasTrabajados);
    //     return "<div class='pill-status'  title='" . $usuarioAsignado . "' data-toggle='tooltip'><div style='font-size: 1em;border-radius: 10rem;margin-left: auto;' class='badge badge-" . $badgeColor . "'>" . $diasTrabajados . "</div></div>";
    // }

    public function tiempoAreaValue($tiempoArea, $usuarioAsignado, $area)
    {

        // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
        //Formula anterior es sin decimal
        $diasTrabajados_formula_anterior = floor(($tiempoArea / 3600) / 9.5);

        if ($diasTrabajados_formula_anterior < 100) {
            $diasTrabajados = round(($tiempoArea / 3600) / 9.5, 1);
        } else {
            $diasTrabajados = $diasTrabajados_formula_anterior;
        }

        return $diasTrabajados;
    }

    // public function tiempoAreaValue2($tiempoArea, $usuarioAsignado, $area)
    // {

    //     // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
    //     //Formula anterior es sin decimal
    //     $diasTrabajados_formula_anterior = floor(($tiempoArea / 3600) / 24);

    //     if ($diasTrabajados_formula_anterior < 100) {
    //         $diasTrabajados = round(($tiempoArea / 3600) / 24, 1);
    //     } else {
    //         $diasTrabajados = $diasTrabajados_formula_anterior;
    //     }

    //     return $diasTrabajados;
    // }

    public function tiempoPausado($tiempoArea, $usuarioAsignado, $area)
    {
        // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
        //Formula anterior es sin decimal
        $diasTrabajados_formula_anterior = floor(($tiempoArea / 3600) / 9.5);

        if ($diasTrabajados_formula_anterior < 100) {
            $diasTrabajados = round(($tiempoArea / 3600) / 9.5, 1);
        } else {
            $diasTrabajados = $diasTrabajados_formula_anterior;
        }

        $badgeColor = $this->badgeColor($area, $diasTrabajados);

        return '<div class="pill-status border-success"  title="' . $usuarioAsignado . '" data-toggle="tooltip">' . $this->ot->ultimo_cambio_area->format('d/m/y') . '<div class="badge badge-' . $badgeColor . '">' . $diasTrabajados . '</div></div>';
    }

    // public function tiempoPausado2($tiempoArea, $usuarioAsignado, $area)
    // {
    //     // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
    //     //Formula anterior es sin decimal
    //     $diasTrabajados_formula_anterior = floor(($tiempoArea / 3600) / 24);

    //     if ($diasTrabajados_formula_anterior < 100) {
    //         $diasTrabajados = round(($tiempoArea / 3600) / 24, 1);
    //     } else {
    //         $diasTrabajados = $diasTrabajados_formula_anterior;
    //     }

    //     $badgeColor = $this->badgeColor($area, $diasTrabajados);

    //     return '<div class="pill-status border-success"  title="' . $usuarioAsignado . '" data-toggle="tooltip">' . $this->ot->ultimo_cambio_area->format('d/m/y') . '<div class="badge badge-' . $badgeColor . '">' . $diasTrabajados . '</div></div>';
    // }

    public function tiempoPausadoValue($tiempoArea, $usuarioAsignado, $area)
    {
        // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
        //Formula anterior es sin decimal
        $diasTrabajados_formula_anterior = floor(($tiempoArea / 3600) / 9.5);

        if ($diasTrabajados_formula_anterior < 100) {
            $diasTrabajados = round(($tiempoArea / 3600) / 9.5, 1);
        } else {
            $diasTrabajados = $diasTrabajados_formula_anterior;
        }

        return $diasTrabajados;
    }

    // public function tiempoPausadoValue2($tiempoArea, $usuarioAsignado, $area)
    // {
    //     // Calculamos el tiempo guardado en segundos en bd + el tiempo transcurrido desde el ultimo cambio de area a hoy contando solo las horas trabajables / 9.5 horas por dia
    //     //Formula anterior es sin decimal
    //     $diasTrabajados_formula_anterior = floor(($tiempoArea / 3600) / 24);

    //     if ($diasTrabajados_formula_anterior < 100) {
    //         $diasTrabajados = round(($tiempoArea / 3600) / 24, 1);
    //     } else {
    //         $diasTrabajados = $diasTrabajados_formula_anterior;
    //     }

    //     return $diasTrabajados;
    // }

    public function badgeColor($area, $diasTrabajados)
    {
        // TODA LA LOGICA DE SEMAFOROS SE ENCUENTRA DOCUMENTANDA EN EXCEL COLOR CODING JMP
        switch ($area) {
            case 1:
                // AREA VENTA
                if ($diasTrabajados <= 1) {
                    return "success";
                } elseif ($diasTrabajados > 1 && $diasTrabajados <= 2) {
                    return "warning";
                } else {
                    return "danger";
                }
                break;
            case 2:
                $maximoVerde = 1;
                $maximoAmarillo = 2;
                // Si esta marcado el checkbox de muestra o analisis se deben agregar dias por cada color
                if ($this->ot->muestra == 1 && $this->ot->id < 4000) {
                    $maximoVerde += 1;
                    $maximoAmarillo += 2;
                }
                if ($this->ot->analisis == 1) {
                    $maximoVerde += 3;
                    $maximoAmarillo += 5;
                }
                // Area Desarrollo
                if ($diasTrabajados <= $maximoVerde) {
                    return "success";
                } elseif ($diasTrabajados > $maximoVerde && $diasTrabajados <=  $maximoAmarillo) {
                    return "warning";
                } else {
                    return "danger";
                }
                break;
            case 3:
                // Area Diseño
                if ($diasTrabajados <= 2) {
                    return "success";
                } elseif ($diasTrabajados > 2 && $diasTrabajados <= 3) {
                    return "warning";
                } else {
                    return "danger";
                }
                break;
            case 4:
                // Area Catalogacion
                if ($diasTrabajados <= 2) {
                    return "success";
                } elseif ($diasTrabajados > 2 && $diasTrabajados <= 3) {
                    return "warning";
                } else {
                    return "danger";
                }
                break;
            case 5:
                // Area Precatalogacion
                if ($diasTrabajados <= 1) {
                    return "success";
                } elseif ($diasTrabajados > 1 && $diasTrabajados <= 2) {
                    return "warning";
                } else {
                    return "danger";
                }
                break;
            case 6:
                // Area Sala muestras
                if ($diasTrabajados <= 3) {
                    return "success";
                } elseif ($diasTrabajados > 3 && $diasTrabajados <= 5) {
                    return "warning";
                } else {
                    return "danger";
                }
                break;
            case 99:
                // TIEMPO TOTAL
                // if ($diasTrabajados <= 10) {
                //     return "success";
                // } elseif ($diasTrabajados > 10 && $diasTrabajados <= 19) {
                //     return "warning";
                // } else {
                //     return "danger";
                // }
                // break;

                $maximoVerde = 13;
                $maximoAmarillo = 21;
                // Si esta marcado el checkbox de muestra o analisis se deben agregar dias por cada color
                if ($this->ot->muestra == 1 && $this->ot->id < 4000) {
                    $maximoVerde += 1;
                    $maximoAmarillo += 2;
                }
                if ($this->ot->analisis == 1) {
                    $maximoVerde += 3;
                    $maximoAmarillo += 5;
                }
                // Area Desarrollo
                if ($diasTrabajados <= $maximoVerde) {
                    return "success";
                } elseif ($diasTrabajados > $maximoVerde && $diasTrabajados <=  $maximoAmarillo) {
                    return "warning";
                } else {
                    return "danger";
                }
                break;

            default:
                if ($diasTrabajados <= 1) {
                    return "success";
                } elseif ($diasTrabajados > 1 && $diasTrabajados <= 2) {
                    return "warning";
                } else {
                    return "danger";
                }
                break;
        }
    }

    // dias trabajados sin redondeo y sin importar el area actual
    public function diasTrabajados($tiempo_total)
    {
        if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13])) {
            $diasTrabajados = (($tiempo_total / 3600) + get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 9.5;
        } else {
            $diasTrabajados = ($tiempo_total / 3600) / 9.5;
        }
        return $diasTrabajados;
    }

    // public function diasTrabajados2($tiempo_total)
    // {
    //     if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13])) {
    //         $diasTrabajados = (($tiempo_total / 3600) + get_working_hours2($this->ot->ultimo_cambio_area, Carbon::now())) / 24;
    //     } else {
    //         $diasTrabajados = ($tiempo_total / 3600) / 24;
    //     }
    //     return $diasTrabajados;
    // }
    // dias trabajados sin redondeo
    public function diasTrabajadosPorArea($tiempo_total, $area)
    {

        if ($this->ot->current_area_id == $area) {
            if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13])) {
                $diasTrabajados = (($tiempo_total / 3600) + get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 9.5;
            } else {
                $diasTrabajados = ($tiempo_total / 3600) / 9.5;
            }

            return $diasTrabajados;
        }
        return ($tiempo_total / 3600) / 9.5;
    }

    // public function diasTrabajados2PorArea($tiempo_total, $area)
    // {

    //     if ($this->ot->current_area_id == $area) {
    //         if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13])) {
    //             $diasTrabajados = (($tiempo_total / 3600) + get_working_hours2($this->ot->ultimo_cambio_area, Carbon::now())) / 24;
    //         } else {
    //             $diasTrabajados = ($tiempo_total / 3600) / 24;
    //         }

    //         return $diasTrabajados;
    //     }
    //     return ($tiempo_total / 3600) / 24;
    // }


    // dias trabajados sin redondeo
    public function diasTrabajadosPorAreaSalaMuestra($tiempo_total, $area)
    {

        if ($this->ot->current_area_id == $area) {
            if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13])) {
                $diasTrabajados = (($tiempo_total / 3600) + get_working_hours_muestra($this->ot->ultimo_cambio_area, Carbon::now())) / 11.5;
            } else {
                $diasTrabajados = ($tiempo_total / 3600) / 11.5;
            }

            return $diasTrabajados;
        }
        return ($tiempo_total / 3600) / 9.5;
    }

    //  public function diasTrabajadosPorArea2SalaMuestra($tiempo_total, $area)
    // {

    //     if ($this->ot->current_area_id == $area) {
    //         if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13])) {
    //             $diasTrabajados = (($tiempo_total / 3600) + get_working_hours_muestra($this->ot->ultimo_cambio_area, Carbon::now())) / 24;
    //         } else {
    //             $diasTrabajados = ($tiempo_total / 3600) / 24;
    //         }

    //         return $diasTrabajados;
    //     }
    //     return ($tiempo_total / 3600) / 24;
    // }

    // dias trabajados sin redondeo y sin importar el area actual
    public function diasTrabajadosReport($tiempo_total, $ultimoCambioEstado)
    {
        if (isset($ultimoCambioEstado[$this->ot->id]) && !in_array($ultimoCambioEstado[$this->ot->id], [8, 9, 11, 13])) {
            $diasTrabajados = (($tiempo_total / 3600) + get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 9.5;
        } else {
            $diasTrabajados =  ($tiempo_total / 3600) / 9.5;
        }
        return $diasTrabajados;
    }

    // public function diasTrabajados2Report($tiempo_total, $ultimoCambioEstado)
    // {
    //     if (isset($ultimoCambioEstado[$this->ot->id]) && !in_array($ultimoCambioEstado[$this->ot->id], [8, 9, 11, 13])) {
    //         $diasTrabajados = (($tiempo_total / 3600) + get_working_hours2($this->ot->ultimo_cambio_area, Carbon::now())) / 24;
    //     } else {
    //         $diasTrabajados =  ($tiempo_total / 3600) / 24;
    //     }
    //     return $diasTrabajados;
    // }
    // dias trabajados sin redondeo
    public function diasTrabajadosPorAreaReport($tiempo_total, $area, $ultimoCambioEstado)
    {

        if ($this->ot->current_area_id == $area) {
            if (isset($ultimoCambioEstado[$this->ot->id]) && !in_array($ultimoCambioEstado[$this->ot->id], [8, 9, 11, 13])) {
                $diasTrabajados = (($tiempo_total / 3600) + get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 9.5;
            } else {
                $diasTrabajados =  ($tiempo_total / 3600) / 9.5;
            }

            return $diasTrabajados;
        }
        return ($tiempo_total / 3600) / 9.5;
    }

    // public function diasTrabajados2PorAreaReport($tiempo_total, $area, $ultimoCambioEstado)
    // {

    //     if ($this->ot->current_area_id == $area) {
    //         if (isset($ultimoCambioEstado[$this->ot->id]) && !in_array($ultimoCambioEstado[$this->ot->id], [8, 9, 11, 13])) {
    //             $diasTrabajados = (($tiempo_total / 3600) + get_working_hours2($this->ot->ultimo_cambio_area, Carbon::now())) / 9.5;
    //         } else {
    //             $diasTrabajados =  ($tiempo_total / 3600) / 24;
    //         }

    //         return $diasTrabajados;
    //     }
    //     return ($tiempo_total / 3600) / 24;
    // }

    // dias trabajados sin redondeo y sin importar el area actual
    public function diasTrabajadosReportDESM($tiempo_total, $ultimoCambioEstado, $dic_variables)
    {
        if (isset($ultimoCambioEstado[$this->ot->id]) && !in_array($ultimoCambioEstado[$this->ot->id], [8, 9, 11, 13])) {
            $diasTrabajados = (($tiempo_total / 3600) + get_working_hours_DESM($this->ot->ultimo_cambio_area, $dic_variables)) / 9.5;
        } else {
            $diasTrabajados =  ($tiempo_total / 3600) / 9.5;
        }
        return $diasTrabajados;
    }

    // public function diasTrabajados2ReportDESM($tiempo_total, $ultimoCambioEstado, $dic_variables)
    // {
    //     if (isset($ultimoCambioEstado[$this->ot->id]) && !in_array($ultimoCambioEstado[$this->ot->id], [8, 9, 11, 13])) {
    //         $diasTrabajados = (($tiempo_total / 3600) + get_working_hours_DESM($this->ot->ultimo_cambio_area, $dic_variables)) / 24;
    //     } else {
    //         $diasTrabajados =  ($tiempo_total / 3600) / 24;
    //     }
    //     return $diasTrabajados;
    // }
    // dias trabajados sin redondeo
    public function diasTrabajadosPorAreaReportDESM($tiempo_total, $area, $ultimoCambioEstado, $dic_variables, $fromDate, $toDate, $promedio_anio) //,$ultima_gestion_area
    {
        /*$fecha_ultima_gestion   = Management::where('work_order_id',$this->ot->id)
                                            ->where('management_type_id', 1)
                                            ->where('work_space_id',$area)
                                            ->orderBy('created_at','desc')
                                            ->first();*/

        /* if(isset($ultima_gestion_area[$this->ot->id])){
            $mes_gestion_actual= date('m', strtotime($ultima_gestion_area[$this->ot->id]));

            $fecha_anterior_gestion = Management::where('work_order_id',$this->ot->id)
                                                ->where('management_type_id', 1)
                                                ->where('created_at','<',$ultima_gestion_area[$this->ot->id])
                                                ->orderBy('created_at','desc')
                                                ->first();
            if($fecha_anterior_gestion){

                $mes_gestion_anterior= date('m', strtotime($fecha_anterior_gestion->created_at));


                if($mes_gestion_actual!=$mes_gestion_anterior){
                    $anio_gestion_ini_mes= date('Y', strtotime($ultima_gestion_area[$this->ot->id]));
                    $fecha_gestion_ini_mes=$anio_gestion_ini_mes.'-'.$mes_gestion_actual.'-01 00:00:00';
                    $diff = get_working_hours($fecha_gestion_ini_mes, $ultima_gestion_area[$this->ot->id]) * 3600;
                    $tiempo_total=$diff;
                    //dd($fecha_ultima_gestion->created_at,$mes_gestion_actual,$fecha_anterior_gestion->created_at,$mes_gestion_anterior,$this->ot->ultimo_cambio_area,$this->ot->current_area_id,$tiempo_total,$diff);
                }
            }
        }*/

        if ($dic_variables['current_date'] >= $fromDate && $dic_variables['current_date'] <= $toDate && $promedio_anio != 2) {
            //dd($dic_variables['current_date'],$fromDate, $toDate,$this->ot->current_area_id);
            if ($this->ot->current_area_id == $area) {
                if (isset($ultimoCambioEstado[$this->ot->id]) && !in_array($ultimoCambioEstado[$this->ot->id], [8, 9, 11, 13])) {
                    $diasTrabajados = (($tiempo_total / 3600) + get_working_hours_DESM($this->ot->ultimo_cambio_area, $dic_variables)) / 9.5;
                } else {
                    $diasTrabajados =  ($tiempo_total / 3600) / 9.5;
                }

                return $diasTrabajados;
            }
            return ($tiempo_total / 3600) / 9.5;
        } else {
            //dd($tiempo_total);
            return ($tiempo_total / 3600) / 9.5;
        }
    }

    // public function diasTrabajados2PorAreaReportDESM($tiempo_total, $area, $ultimoCambioEstado, $dic_variables, $fromDate, $toDate, $promedio_anio) //,$ultima_gestion_area
    // {
    //     /*$fecha_ultima_gestion   = Management::where('work_order_id',$this->ot->id)
    //                                         ->where('management_type_id', 1)
    //                                         ->where('work_space_id',$area)
    //                                         ->orderBy('created_at','desc')
    //                                         ->first();*/

    //     /* if(isset($ultima_gestion_area[$this->ot->id])){
    //         $mes_gestion_actual= date('m', strtotime($ultima_gestion_area[$this->ot->id]));

    //         $fecha_anterior_gestion = Management::where('work_order_id',$this->ot->id)
    //                                             ->where('management_type_id', 1)
    //                                             ->where('created_at','<',$ultima_gestion_area[$this->ot->id])
    //                                             ->orderBy('created_at','desc')
    //                                             ->first();
    //         if($fecha_anterior_gestion){

    //             $mes_gestion_anterior= date('m', strtotime($fecha_anterior_gestion->created_at));


    //             if($mes_gestion_actual!=$mes_gestion_anterior){
    //                 $anio_gestion_ini_mes= date('Y', strtotime($ultima_gestion_area[$this->ot->id]));
    //                 $fecha_gestion_ini_mes=$anio_gestion_ini_mes.'-'.$mes_gestion_actual.'-01 00:00:00';
    //                 $diff = get_working_hours($fecha_gestion_ini_mes, $ultima_gestion_area[$this->ot->id]) * 3600;
    //                 $tiempo_total=$diff;
    //                 //dd($fecha_ultima_gestion->created_at,$mes_gestion_actual,$fecha_anterior_gestion->created_at,$mes_gestion_anterior,$this->ot->ultimo_cambio_area,$this->ot->current_area_id,$tiempo_total,$diff);
    //             }
    //         }
    //     }*/

    //     if ($dic_variables['current_date'] >= $fromDate && $dic_variables['current_date'] <= $toDate && $promedio_anio != 2) {
    //         //dd($dic_variables['current_date'],$fromDate, $toDate,$this->ot->current_area_id);
    //         if ($this->ot->current_area_id == $area) {
    //             if (isset($ultimoCambioEstado[$this->ot->id]) && !in_array($ultimoCambioEstado[$this->ot->id], [8, 9, 11, 13])) {
    //                 $diasTrabajados = (($tiempo_total / 3600) + get_working_hours_DESM2($this->ot->ultimo_cambio_area, $dic_variables)) / 24;
    //             } else {
    //                 $diasTrabajados =  ($tiempo_total / 3600) / 24;
    //             }

    //             return $diasTrabajados;
    //         }
    //         return ($tiempo_total / 3600) / 24;
    //     } else {
    //         //dd($tiempo_total);
    //         return ($tiempo_total / 3600) / 24;
    //     }
    // }


    public function diasPorArea($area_id)
    {
        $tiempo_area = 0;
        switch ($area_id) {
            case 1:
                $tiempo_area = $this->ot->tiempo_venta;
                break;
            case 2:
                $tiempo_area = $this->ot->tiempo_desarrollo;
                break;
            case 3:
                $tiempo_area = $this->ot->tiempo_diseño;
                break;
            case 4:
                $tiempo_area = $this->ot->tiempo_catalogacion;
                break;
            case 5:
                $tiempo_area = $this->ot->tiempo_precatalogacion;
                break;
            case 6:
                $tiempo_area = $this->ot->tiempo_muestra;
                break;

            default:
                # code...
                break;
        }
        if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13])) {
            return floor((($tiempo_area / 3600) + get_working_hours($this->ot->ultimo_cambio_area, Carbon::now())) / 9.5);
        } else {
            // Si es area actual y esta en estado pausado mostrar area con tiempo hasta hoy
            return floor(($tiempo_area / 3600) / 9.5);
        }
    }


    // public function diasPorArea2($area_id)
    // {
    //     $tiempo_area = 0;
    //     switch ($area_id) {
    //         case 1:
    //             $tiempo_area = $this->ot->tiempo_venta;
    //             break;
    //         case 2:
    //             $tiempo_area = $this->ot->tiempo_desarrollo;
    //             break;
    //         case 3:
    //             $tiempo_area = $this->ot->tiempo_diseño;
    //             break;
    //         case 4:
    //             $tiempo_area = $this->ot->tiempo_catalogacion;
    //             break;
    //         case 5:
    //             $tiempo_area = $this->ot->tiempo_precatalogacion;
    //             break;
    //         case 6:
    //             $tiempo_area = $this->ot->tiempo_muestra;
    //             break;

    //         default:
    //             # code...
    //             break;
    //     }
    //     if (isset($this->ot->ultimoCambioEstado) && !in_array($this->ot->ultimoCambioEstado->state_id, [8, 9, 11, 13])) {
    //         return floor((($tiempo_area / 3600) + get_working_hours2($this->ot->ultimo_cambio_area, Carbon::now())) / 24);
    //     } else {
    //         // Si es area actual y esta en estado pausado mostrar area con tiempo hasta hoy
    //         return floor(($tiempo_area / 3600) / 24);
    //     }
    // }

    public function iconosAprobacionVenta()
    {
        if ($this->ot->aprobacion_jefe_venta == 0) {
            return '';
        } elseif ($this->ot->aprobacion_jefe_venta == 1) {
            return '<div title="Por Aprobar Jefe Venta" data-toggle="tooltip" class="material-icons md-14">notification_important</div>';
        } elseif ($this->ot->aprobacion_jefe_venta == 2) {
            return '<div title="Aprobado Jefe Venta" data-toggle="tooltip" class="material-icons md-14">check_box</div>';
        } elseif ($this->ot->aprobacion_jefe_venta == 3) {
            return '<div title="Rechazado Jefe Venta" data-toggle="tooltip" class="material-icons md-14">not_interested</div>';
        } else return '';
    }

    public function iconosAprobacionDesarrollo()
    {
        if ($this->ot->aprobacion_jefe_desarrollo == 0) {
            return '';
        } elseif ($this->ot->aprobacion_jefe_desarrollo == 1) {
            return '<div title="Por Aprobar Jefe Diseño Estructural" data-toggle="tooltip" class="material-icons md-14">notification_important</div>';
        } elseif ($this->ot->aprobacion_jefe_desarrollo == 2) {
            return '<div title="Aprobado Jefe Diseño Estructural" data-toggle="tooltip" class="material-icons md-14">check_box</div>';
        } elseif ($this->ot->aprobacion_jefe_desarrollo == 3) {
            return '<div title="Rechazado Jefe Diseño Estructural" data-toggle="tooltip" class="material-icons md-14">not_interested</div>';
        } else return '';
    }

    public function tiempoDisenadorExterno()
    {
        $tiempo_total_diseño_externo = 0;
        $tiempo_respuesta_diseño_externo = 0;
        $fecha_ultimo_envio = "";
        $ultimo_diseñador_externo = "";
        //Buscamos si tiene envios a diseñador externo
        $ot_envios_disenador_externo = Management::where('work_order_id', $this->ot->id)
            ->where('management_type_id', 9)
            ->get();
        if ($ot_envios_disenador_externo->count() == 0) {
            $tiempo_total_diseño_externo = 0;
            $fecha_ultimo_envio = "";
        } else {
            //Recorrremos cada envio a diseñador externo
            foreach ($ot_envios_disenador_externo as $data_envio) {
                //Buscamos si el envio tiene recepcion  del diseñador externo
                $data_recepcion = Management::where('work_order_id', $this->ot->id)
                    ->where('management_type_id', 10)
                    ->where('gestion_id', '>=', $data_envio->id)
                    ->first();
                if ($data_recepcion) {
                    //Calculamos el tiempo de respuesta de diseñador externo
                    $tiempo_respuesta_diseño_externo += get_working_hours($data_envio->created_at, $data_recepcion->created_at);
                } else {
                    //Se calacula el tiempoa hasta hoy
                    $tiempo_respuesta_diseño_externo += get_working_hours($data_envio->created_at, Carbon::now());
                }
                // $fecha_ultimo_envio = $data_envio->created_at;
                $proveedor = Proveedor::where('deleted', 0)->where('id', $data_envio->proveedor_id)->first();
                $ultimo_diseñador_externo = $proveedor->name;
            }

            $tiempo_total_diseño_externo = round($tiempo_respuesta_diseño_externo / 9.5, 1);
        }

        // 🔍 Validar si el último management de la OT es del tipo diseñador externo (id 9)
        $ultimo_management = Management::where('work_order_id', $this->ot->id)
            ->orderByDesc('id')
            ->first();

        $mostrar_fecha = $ultimo_management && $ultimo_management->management_type_id == 9;

        if($mostrar_fecha){
            $fecha_ultimo_envio = $ultimo_management->created_at;
        }
        // var_dump($mostrar_fecha);

        // var_dump($fecha_ultimo_envio);
        $badgeColor = $this->badgeColor(3, $tiempo_total_diseño_externo);
        if ($fecha_ultimo_envio == "") {
            return "<div class='pill-status'   title='" . $ultimo_diseñador_externo . "' data-toggle='tooltip'><div style='font-size: 1em;border-radius: 10rem;margin-left: auto;' class='badge badge-" . $badgeColor . "'>" . $tiempo_total_diseño_externo . "</div></div>";
        } else {
            return "<div class='pill-status'   title='" . $ultimo_diseñador_externo . "' data-toggle='tooltip'>" . Carbon::parse($fecha_ultimo_envio)->format('d/m/y') . " <div style='font-size: 1em;border-radius: 10rem;margin-left: auto;' class='badge badge-" . $badgeColor . "'>" . $tiempo_total_diseño_externo . "</div></div>";
            // return "<div class='pill-status'   title='" . $ultimo_diseñador_externo . "' data-toggle='tooltip'> <div style='font-size: 1em;border-radius: 10rem;margin-left: auto;' class='badge badge-" . $badgeColor . "'>" . $tiempo_total_diseño_externo . "</div></div>";
        }
    }

    // public function tiempoDisenadorExterno2()
    // {
    //     $tiempo_total_diseño_externo = 0;
    //     $tiempo_respuesta_diseño_externo = 0;
    //     $fecha_ultimo_envio = "";
    //     $ultimo_diseñador_externo = "";
    //     //Buscamos si tiene envios a diseñador externo
    //     $ot_envios_disenador_externo = Management::where('work_order_id', $this->ot->id)
    //         ->where('management_type_id', 9)
    //         ->get();
    //     if ($ot_envios_disenador_externo->count() == 0) {
    //         $tiempo_total_diseño_externo = 0;
    //         $fecha_ultimo_envio = "";
    //     } else {
    //         //Recorrremos cada envio a diseñador externo
    //         foreach ($ot_envios_disenador_externo as $data_envio) {
    //             //Buscamos si el envio tiene recepcion  del diseñador externo
    //             $data_recepcion = Management::where('work_order_id', $this->ot->id)
    //                 ->where('management_type_id', 10)
    //                 ->where('gestion_id', '>=', $data_envio->id)
    //                 ->first();
    //             if ($data_recepcion) {
    //                 //Calculamos el tiempo de respuesta de diseñador externo
    //                 $tiempo_respuesta_diseño_externo += get_working_hours2($data_envio->created_at, $data_recepcion->created_at);
    //             } else {
    //                 //Se calacula el tiempoa hasta hoy
    //                 $tiempo_respuesta_diseño_externo += get_working_hours2($data_envio->created_at, Carbon::now());
    //             }
    //             $fecha_ultimo_envio = $data_envio->created_at;
    //             $proveedor = Proveedor::where('deleted', 0)->where('id', $data_envio->proveedor_id)->first();
    //             $ultimo_diseñador_externo = $proveedor->name;
    //         }

    //         $tiempo_total_diseño_externo = round($tiempo_respuesta_diseño_externo / 24, 1);
    //     }

    //     $badgeColor = $this->badgeColor(3, $tiempo_total_diseño_externo);
    //     if ($fecha_ultimo_envio == "") {
    //         return "<div class='pill-status'   title='" . $ultimo_diseñador_externo . "' data-toggle='tooltip'><div style='font-size: 1em;border-radius: 10rem;margin-left: auto;' class='badge badge-" . $badgeColor . "'>" . $tiempo_total_diseño_externo . "</div></div>";
    //     } else {
    //         // return "<div class='pill-status'   title='" . $ultimo_diseñador_externo . "' data-toggle='tooltip'>" . Carbon::parse($fecha_ultimo_envio)->format('d/m/y') . " <div style='font-size: 1em;border-radius: 10rem;margin-left: auto;' class='badge badge-" . $badgeColor . "'>" . $tiempo_total_diseño_externo . "</div></div>";
    //         return "<div class='pill-status'   title='" . $ultimo_diseñador_externo . "' data-toggle='tooltip'><div style='font-size: 1em;border-radius: 10rem;margin-left: auto;' class='badge badge-" . $badgeColor . "'>" . $tiempo_total_diseño_externo . "</div></div>";
    //     }
    // }
}
