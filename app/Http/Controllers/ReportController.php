<?php

namespace App\Http\Controllers;

use App\Canal;
use App\WorkOrder;
use App\Client;
use App\Management;
use App\Muestra;
use App\Carton;
use App\States;
use App\User;
use App\WorkSpace;
use App\UserWorkOrder;
use App\SystemVariable;
use App\ReporteDesm;
use App\Pegado;
use App\CiudadesFlete;
use App\Proveedor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use DateTime;


class ReportController extends Controller
{

    // funcion para devolver datos para generar reportes de Gestion de Carga de OT por mes:
    public function reportGestionLoadOtMonth()
    {

        $nombreMeses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        // Carbon::setLocale('es');
        // // Filtro por fechas
        if (!is_null(request()->input('mes')) and !is_null(request()->input('year'))) {
            $mes = request()->input('mes');
            $year = request()->input('year')[0];
        } else {
            $mes = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
        }


        if (!is_null(request()->input('tipo_vendedor'))) {
            $tipo_vendedor = request()->input('tipo_vendedor');
        } else {
            $tipo_vendedor = 1;
        }

        $fromDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth(4)->startOfMonth();
        $toDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->endOfMonth();
        $mesSeleccionado = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->format('Y-m');
        $meses = [];
        $nombreMesesSeleccionados = [];
        $solicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];
        //$cotizaSinCadSolicitudesUltimosMeses = [0, 0, 0, 0, 0];
        //$cotizaConCadSolicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];
        $muestraSolicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];
        $desarrolloCompletoSolicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];
        $arteSolicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];
        $otrasDesarrolloSolicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];
        $proyectoInnovacionSolicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];

        // Si se solicito exportar
        if (!is_null(request()->input('exportar'))) {
            $this->descargaReporteOT($fromDate, $toDate, "Gestión de Carga OT ");
        } else {

            for ($i = 4; $i >= 0; $i--) {
                $meses[] = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('Y-m');
                $nombreMesesSeleccionados[] = $nombreMeses[Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('m') - 1];
            }

            // SECCION SUPERIOR REPORTE " CANTIDADES "
            // Numero de ots creadas entre las fechas seleccionadas por tipo de solicitud
            $query = WorkOrder::with(
                "gestiones"
            )->select(DB::raw('count(id) as `total_solicitudes`'), DB::raw("DATE_FORMAT(created_at, '%Y-%m') new_date"), 'tipo_solicitud');

            if ($tipo_vendedor == 1) {
                if (!is_null(request()->query('vendedor_id'))) {
                    $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
                }
            } elseif ($tipo_vendedor == 4) {
                $vendedores_array = User::where('active', 1)->whereIN('role_id', [3, 4])->pluck('id')->toArray();
                //dd(request()->query('vendedor_id'));
                if (!is_null(request()->query('vendedor_id'))) {
                    $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
                } else {
                    $query = $query->whereIn('creador_id', $vendedores_array);
                }
            } elseif ($tipo_vendedor == 19) {
                $vendedores_array = User::where('active', 1)->whereIN('role_id', [19])->pluck('id')->toArray();
                if (!is_null(request()->query('vendedor_id'))) {
                    $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
                } else {
                    $query = $query->whereIn('creador_id', $vendedores_array);
                }
            }

            $ots = $query->whereBetween('created_at', [$fromDate, $toDate])->groupBy('new_date', 'tipo_solicitud')->orderBy('new_date')->get();
            // Numero de ots creadas entre las fechas seleccionadas todas las solicitudes
            // $ots = WorkOrder::select(DB::raw('count(id) as `total_solicitudes`'), DB::raw("DATE_FORMAT(created_at, '%Y-%m') new_date"))->whereBetween('created_at', [$fromDate, $toDate])
            //     ->groupBy('new_date')->orderBy('new_date')->get();
            // usamos "&$solicitudesTotalesUltimosMeses" para poder editar el original y no la referencia
            $otsCantidad = $ots->map(function ($ot) use ($meses, &$solicitudesTotalesUltimosMeses, &$muestraSolicitudesTotalesUltimosMeses, &$desarrolloCompletoSolicitudesTotalesUltimosMeses, &$arteSolicitudesTotalesUltimosMeses, &$otrasDesarrolloSolicitudesTotalesUltimosMeses, &$proyectoInnovacionSolicitudesTotalesUltimosMeses) {
                // sumamos el total del mes al arrego de solicitud totales
                // primero conseguimos la llave del mes para sumarle la cantidad
                $key = array_search($ot->new_date, $meses);
                if ($key !== false) {
                    $solicitudesTotalesUltimosMeses[$key] +=  $ot->total_solicitudes;
                    // sumamos solicitudes por tipo
                    switch ($ot->tipo_solicitud) {
                        case '1':
                            $desarrolloCompletoSolicitudesTotalesUltimosMeses[$key] += $ot->total_solicitudes;
                            break;
                        /*case '2':
                            $cotizaConCadSolicitudesTotalesUltimosMeses[$key] += $ot->total_solicitudes;
                            break;*/
                        case '3':
                            $muestraSolicitudesTotalesUltimosMeses[$key] += $ot->total_solicitudes;
                            break;
                        /*case '4':
                            $cotizaSinCadSolicitudesUltimosMeses[$key] += $ot->total_solicitudes;
                            break;*/
                        case '5':
                            $arteSolicitudesTotalesUltimosMeses[$key] += $ot->total_solicitudes;
                            break;
                        case '6':
                            $otrasDesarrolloSolicitudesTotalesUltimosMeses[$key] += $ot->total_solicitudes;
                            break;
                        case '7':
                            $proyectoInnovacionSolicitudesTotalesUltimosMeses[$key] += $ot->total_solicitudes;
                            break;
                        default:
                            break;
                    }
                }
                return $ot;
            });

            // FIN SECCION SUPERIOR REPORTE " CANTIDADES "



            // SECCION INFERIOR REPORTE " DIAS "
            // ------- dias
            // $query =  WorkOrder::select(DB::raw('count(id) as `total_solicitudes`'), DB::raw("DATE_FORMAT(created_at, '%Y-%m') new_date"), 'tipo_solicitud');
            // // Calculo total de tiempo
            // $query = $query->withCount([
            //     'gestiones AS tiempo_total' => function ($q) {
            //         $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
            //     }
            // ]);
            // if (!is_null(request()->query('vendedor_id'))) {
            //     $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
            // }
            // $topOts = $query->whereBetween('work_orders.created_at', [$fromDate, $toDate])->get();
            // $otsDias = $query->whereBetween('created_at', [$fromDate, $toDate])->groupBy('new_date', 'tipo_solicitud')->orderBy('new_date')->get();
            //
            $query =  WorkOrder::with(
                "gestiones",
                "ultimoCambioEstado.area"
            )->select('*', DB::raw("DATE_FORMAT(created_at, '%Y-%m') new_date"))->withCount([
                'gestiones AS tiempo_total' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
                }
            ]);

            if ($tipo_vendedor == 1) {
                if (!is_null(request()->query('vendedor_id'))) {
                    $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
                }
            } elseif ($tipo_vendedor == 4) {
                $vendedores_array = User::where('active', 1)->whereIN('role_id', [3, 4])->pluck('id')->toArray();
                //dd(request()->query('vendedor_id'));
                if (!is_null(request()->query('vendedor_id'))) {
                    $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
                } else {
                    $query = $query->whereIn('creador_id', $vendedores_array);
                }
            } elseif ($tipo_vendedor == 19) {
                $vendedores_array = User::where('active', 1)->whereIN('role_id', [19])->pluck('id')->toArray();
                if (!is_null(request()->query('vendedor_id'))) {
                    $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
                } else {
                    $query = $query->whereIn('creador_id', $vendedores_array);
                }
            }

            $otsDias = $query->whereBetween('created_at', [$fromDate, $toDate])->get();

            $otsDias = $otsDias->map(function ($ot) {
                $ot->dias_trabajados = round($ot->present()->diasTrabajados($ot->tiempo_total), 1);
                return $ot;
            });
            $otsDias = $otsDias->groupBy('new_date')->transform(function ($item, $k) {
                return $item->groupBy('tipo_solicitud');
            });

            $diasPorSolicitudUltimosMeses = [0, 0, 0, 0, 0];
            $numeroPorSolicitudUltimosMeses = [0, 0, 0, 0, 0];

            //$cotizaSinCadPromedioDiasUltimosMeses = [0, 0, 0, 0, 0];
            //$cotizaSinCadPromedioDiasUltimosMesesCount = [0, 0, 0, 0, 0];

            //$cotizaConCadPromedioDiasTotalesUltimosMeses = [0, 0, 0, 0, 0];
            //$cotizaConCadPromedioDiasTotalesUltimosMesesCount = [0, 0, 0, 0, 0];

            $muestraPromedioDiasTotalesUltimosMeses = [0, 0, 0, 0, 0];
            $muestraPromedioDiasTotalesUltimosMesesCount = [0, 0, 0, 0, 0];

            $desarrolloCompletoPromedioDiasTotalesUltimosMeses = [0, 0, 0, 0, 0];
            $desarrolloCompletoPromedioDiasTotalesUltimosMesesCount = [0, 0, 0, 0, 0];

            $artePromedioDiasTotalesUltimosMeses = [0, 0, 0, 0, 0];
            $artePromedioDiasTotalesUltimosMesesCount = [0, 0, 0, 0, 0];

            $otrasDesarrolloPromedioDiasTotalesUltimosMeses = [0, 0, 0, 0, 0];
            $otrasDesarrolloPromedioDiasTotalesUltimosMesesCount = [0, 0, 0, 0, 0];

            $proyectoInnovacionPromedioDiasTotalesUltimosMeses = [0, 0, 0, 0, 0];
            $proyectoInnovacionPromedioDiasTotalesUltimosMesesCount = [0, 0, 0, 0, 0];

            $otsDias = $otsDias->map(function ($ots_por_tipo_solicitud, $new_date) use ($meses, &$diasPorSolicitudUltimosMeses, &$numeroPorSolicitudUltimosMeses, &$muestraPromedioDiasTotalesUltimosMeses, &$muestraPromedioDiasTotalesUltimosMesesCount, &$desarrolloCompletoPromedioDiasTotalesUltimosMeses, &$desarrolloCompletoPromedioDiasTotalesUltimosMesesCount, &$artePromedioDiasTotalesUltimosMeses, &$artePromedioDiasTotalesUltimosMesesCount, &$otrasDesarrolloPromedioDiasTotalesUltimosMeses, &$otrasDesarrolloPromedioDiasTotalesUltimosMesesCount, &$proyectoInnovacionPromedioDiasTotalesUltimosMeses, &$proyectoInnovacionPromedioDiasTotalesUltimosMesesCount) {
                // sumamos el total del mes al arrego de solicitud totales
                // primero conseguimos la llave del mes para sumarle la cantidad
                $key = array_search($new_date, $meses);
                if ($key !== false) {
                    $ots_por_tipo_solicitud->map(function ($ots, $tipo_solicitud) use ($key, $meses, &$diasPorSolicitudUltimosMeses, &$numeroPorSolicitudUltimosMeses, &$muestraPromedioDiasTotalesUltimosMeses, &$muestraPromedioDiasTotalesUltimosMesesCount, &$desarrolloCompletoPromedioDiasTotalesUltimosMeses, &$desarrolloCompletoPromedioDiasTotalesUltimosMesesCount, &$artePromedioDiasTotalesUltimosMeses, &$artePromedioDiasTotalesUltimosMesesCount, &$otrasDesarrolloPromedioDiasTotalesUltimosMeses, &$otrasDesarrolloPromedioDiasTotalesUltimosMesesCount, &$proyectoInnovacionPromedioDiasTotalesUltimosMeses, &$proyectoInnovacionPromedioDiasTotalesUltimosMesesCount) {
                        $diasTrabajados = round($ots->sum("dias_trabajados"), 1);
                        $numeroOts = $ots->count();
                        $diasPorSolicitudUltimosMeses[$key] += $diasTrabajados;
                        $numeroPorSolicitudUltimosMeses[$key] += $numeroOts;
                        switch ($tipo_solicitud) {
                            case '1':
                                $desarrolloCompletoPromedioDiasTotalesUltimosMeses[$key] += $diasTrabajados;
                                $desarrolloCompletoPromedioDiasTotalesUltimosMesesCount[$key] += $numeroOts;
                                break;
                            /*case '2':
                                $cotizaConCadPromedioDiasTotalesUltimosMeses[$key] += $diasTrabajados;
                                $cotizaConCadPromedioDiasTotalesUltimosMesesCount[$key] += $numeroOts;
                                break;*/
                            case '3':
                                $muestraPromedioDiasTotalesUltimosMeses[$key] += $diasTrabajados;
                                $muestraPromedioDiasTotalesUltimosMesesCount[$key] += $numeroOts;
                                break;
                            /*case '4':
                                $cotizaSinCadPromedioDiasUltimosMeses[$key] += $diasTrabajados;
                                $cotizaSinCadPromedioDiasUltimosMesesCount[$key] += $numeroOts;
                                break;*/
                            case '5':
                                $artePromedioDiasTotalesUltimosMeses[$key] += $diasTrabajados;
                                $artePromedioDiasTotalesUltimosMesesCount[$key] += $numeroOts;
                                break;
                            case '6':
                                $otrasDesarrolloPromedioDiasTotalesUltimosMeses[$key] += $diasTrabajados;
                                $otrasDesarrolloPromedioDiasTotalesUltimosMesesCount[$key] += $numeroOts;
                                break;
                            case '7':
                                $proyectoInnovacionPromedioDiasTotalesUltimosMeses[$key] += $diasTrabajados;
                                $proyectoInnovacionPromedioDiasTotalesUltimosMesesCount[$key] += $numeroOts;
                                break;
                            default:
                                break;
                        }
                    });
                    // $diasTrabajados = round($ot->present()->diasTrabajados($ot->tiempo_total), 1);
                    // $diasTrabajados = round(($ot->tiempo_total / 3600) / 9.5, 1);
                    // sumamos promedio de dias por tipo solicitud

                }
                return $ots_por_tipo_solicitud;
            });

            foreach ($diasPorSolicitudUltimosMeses as $key => $val) {
                if ($val > 0) {
                    $diasPorSolicitudUltimosMeses[$key] =  round($val / $numeroPorSolicitudUltimosMeses[$key], 1);
                }
            }
            foreach ($desarrolloCompletoPromedioDiasTotalesUltimosMeses as $key => $val) {
                if ($val > 0) {
                    $desarrolloCompletoPromedioDiasTotalesUltimosMeses[$key] =  round($val / $desarrolloCompletoPromedioDiasTotalesUltimosMesesCount[$key], 1);
                }
            }
            /*foreach ($cotizaConCadPromedioDiasTotalesUltimosMeses as $key => $val) {
                if ($val > 0) {
                    $cotizaConCadPromedioDiasTotalesUltimosMeses[$key] =  round($val / $cotizaConCadPromedioDiasTotalesUltimosMesesCount[$key], 1);
                }
            }*/
            foreach ($muestraPromedioDiasTotalesUltimosMeses as $key => $val) {
                if ($val > 0) {
                    $muestraPromedioDiasTotalesUltimosMeses[$key] =  round($val / $muestraPromedioDiasTotalesUltimosMesesCount[$key], 1);
                }
            }
            /*foreach ($cotizaSinCadPromedioDiasUltimosMeses as $key => $val) {
                if ($val > 0) {
                    $cotizaSinCadPromedioDiasUltimosMeses[$key] =  round($val / $cotizaSinCadPromedioDiasUltimosMesesCount[$key], 1);
                }
            }*/
            foreach ($artePromedioDiasTotalesUltimosMeses as $key => $val) {
                if ($val > 0) {
                    $artePromedioDiasTotalesUltimosMeses[$key] =  round($val / $artePromedioDiasTotalesUltimosMesesCount[$key], 1);
                }
            }

            foreach ($otrasDesarrolloPromedioDiasTotalesUltimosMeses as $key => $val) {
                if ($val > 0) {
                    $otrasDesarrolloPromedioDiasTotalesUltimosMeses[$key] =  round($val / $otrasDesarrolloPromedioDiasTotalesUltimosMesesCount[$key], 1);
                }
            }

            foreach ($proyectoInnovacionPromedioDiasTotalesUltimosMeses as $key => $val) {
                if ($val > 0) {
                    $proyectoInnovacionPromedioDiasTotalesUltimosMeses[$key] =  round($val / $proyectoInnovacionPromedioDiasTotalesUltimosMesesCount[$key], 1);
                }
            }

            // FIN SECCION INFERIOR REPORTE " DIAS "
            // vendedores o creadores:
            if ($tipo_vendedor == 1) {
                $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4, 19])->get();
                $vendedores->map(function ($vendedor) {
                    $vendedor->vendedor_id = $vendedor->id;
                });
            } elseif ($tipo_vendedor == 4) {
                $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4])->get();
                $vendedores->map(function ($vendedor) {
                    $vendedor->vendedor_id = $vendedor->id;
                });
            } elseif ($tipo_vendedor == 19) {
                $vendedores = User::where('active', 1)->whereIn('role_id', [19])->get();
                $vendedores->map(function ($vendedor) {
                    $vendedor->vendedor_id = $vendedor->id;
                });
            }

            // clientes:
            $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->get();
            $clients->map(function ($client) {
                $client->client_id = $client->id;
            });

            // estados:
            $estados = States::where('status', '=', 'active')->get();
            $estados->map(function ($estado) {
                $estado->estado_id = $estado->id;
            });

            // mes y año:
            // $mes  = date('m');
            $year_ini = date('Y') + 0;
            $year_fin = 2020;
            $years = [];
            for ($i = $year_ini; $i >= $year_fin; $i--) {
                $years[] = $i;
            }
        }

        return view('reports.reportGestionLoadOtMonth', compact(
            'vendedores',
            'clients',
            'estados',
            'mes',
            'year',
            'years',
            'meses',
            'solicitudesTotalesUltimosMeses',
            "nombreMeses",
            "nombreMesesSeleccionados",
            "muestraSolicitudesTotalesUltimosMeses",
            "desarrolloCompletoSolicitudesTotalesUltimosMeses",
            "arteSolicitudesTotalesUltimosMeses",
            "otrasDesarrolloSolicitudesTotalesUltimosMeses",
            "proyectoInnovacionSolicitudesTotalesUltimosMeses",
            "diasPorSolicitudUltimosMeses",
            "desarrolloCompletoPromedioDiasTotalesUltimosMeses",
            "muestraPromedioDiasTotalesUltimosMeses",
            "artePromedioDiasTotalesUltimosMeses",
            "otrasDesarrolloPromedioDiasTotalesUltimosMeses",
            "proyectoInnovacionPromedioDiasTotalesUltimosMeses",
            "tipo_vendedor"
        ));
    }

    // REPORTE CONVERSION OTS CREADAS Y OTS TERMINADAS
    public function reportCompletedOt()
    {
        $nombreMeses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        // Carbon::setLocale('es');
        // // Filtro por fechas
        if (!is_null(request()->input('mes')) and !is_null(request()->input('year'))) {
            $mes = request()->input('mes');
            $year = request()->input('year')[0];
        } else {
            $mes = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
        }

        if (!is_null(request()->input('tipo_vendedor'))) {
            $tipo_vendedor = request()->input('tipo_vendedor');
        } else {
            $tipo_vendedor = 1;
        }

        $fromDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth(4)->startOfMonth();
        $toDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->endOfMonth();
        $mesSeleccionado = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->format('Y-m');
        $meses = [];
        $nombreMesesSeleccionados = [];
        // desarrollo
        $solicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];
        $desarrolloCompletoSolicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];
        $desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje = [0, 0, 0, 0, 0];
        //Total materiales creados/asignados por mes
        $totalMaterialesCreadosUltimosMeses = [0, 0, 0, 0, 0];
        $totalMaterialesCreadosUltimosMesesPorcentaje = [0, 0, 0, 0, 0];
        // Arte con Material
        $artesCreadosUltimosMeses = [0, 0, 0, 0, 0];
        $artesTerminadosUltimosMeses = [0, 0, 0, 0, 0];
        $artesTerminadosUltimosMesesPorcentaje = [0, 0, 0, 0, 0];
        for ($i = 4; $i >= 0; $i--) {
            $meses[] = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('Y-m');
            $nombreMesesSeleccionados[] = $nombreMeses[Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('m') - 1];
        }


        // SECCION SUPERIOR REPORTE " CANTIDADES "
        // Numero de ots creadas entre las fechas seleccionadas por tipo de solicitud
        $query = WorkOrder::with(
            "gestiones",
            "ultimoCambioEstado.area"
        )->select('*', DB::raw("DATE_FORMAT(created_at, '%Y-%m') new_date"));

        if ($tipo_vendedor == 1) {
            if (!is_null(request()->query('vendedor_id'))) {
                $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
            }
        } elseif ($tipo_vendedor == 4) {
            $vendedores_array = User::where('active', 1)->whereIN('role_id', [3, 4])->pluck('id')->toArray();
            //dd(request()->query('vendedor_id'));
            if (!is_null(request()->query('vendedor_id'))) {
                $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
            } else {
                $query = $query->whereIn('creador_id', $vendedores_array);
            }
        } elseif ($tipo_vendedor == 19) {
            $vendedores_array = User::where('active', 1)->whereIN('role_id', [19])->pluck('id')->toArray();
            if (!is_null(request()->query('vendedor_id'))) {
                $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
            } else {
                $query = $query->whereIn('creador_id', $vendedores_array);
            }
        }

        // Desarrollo y arte con material;
        $ots = $query->whereIn('tipo_solicitud', [1, 5])->whereBetween('created_at', [$fromDate, $toDate])->get();
        $ots = $ots->groupBy('new_date');
        // usamos "&$solicitudesTotalesUltimosMeses" para poder editar el original y no la referencia
        $otsCantidad = $ots->map(function ($ots, $new_date) use ($meses, &$solicitudesTotalesUltimosMeses, &$desarrolloCompletoSolicitudesTotalesUltimosMeses, &$artesCreadosUltimosMeses, &$artesTerminadosUltimosMeses, &$totalMaterialesCreadosUltimosMeses) {
            $key = array_search($new_date, $meses);
            if ($key !== false) {
                $ots->map(function ($ot) use ($key, &$solicitudesTotalesUltimosMeses, &$desarrolloCompletoSolicitudesTotalesUltimosMeses, &$artesCreadosUltimosMeses, &$artesTerminadosUltimosMeses, &$totalMaterialesCreadosUltimosMeses) {

                    // desarrollo
                    if ($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 7) {

                        $solicitudesTotalesUltimosMeses[$key]++;
                        // Solo acumulamos si es ot terminada
                        if (isset($ot->ultimoCambioEstado) && $ot->ultimoCambioEstado->state_id == 8) {
                            $desarrolloCompletoSolicitudesTotalesUltimosMeses[$key]++;
                        }
                        if (!is_null($ot->material_id)) {
                            $totalMaterialesCreadosUltimosMeses[$key]++;
                        }
                    } elseif ($ot->tipo_solicitud == 5) { //Arte con material
                        $artesCreadosUltimosMeses[$key]++;
                        // Solo acumulamos si es ot terminada
                        if (isset($ot->ultimoCambioEstado) && $ot->ultimoCambioEstado->state_id == 8) {
                            $artesTerminadosUltimosMeses[$key]++;
                        }
                        if (!is_null($ot->material_id)) {
                            $totalMaterialesCreadosUltimosMeses[$key]++;
                        }
                    }


                    return $ot;
                });
            }
            return $ots;
        });

        foreach ($desarrolloCompletoSolicitudesTotalesUltimosMeses as $key => $val) {
            if ($val > 0) {
                $desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje[$key] =  round($val / $solicitudesTotalesUltimosMeses[$key] * 100, 1);
            }
        }
        foreach ($artesTerminadosUltimosMeses as $key => $val) {
            if ($val > 0) {
                $artesTerminadosUltimosMesesPorcentaje[$key] =  round($val / $artesCreadosUltimosMeses[$key] * 100, 1);
            }
        }
        foreach ($totalMaterialesCreadosUltimosMeses as $key => $val) {
            // var_dump($val);
            if ($val > 0) {
                // $totalMaterialesCreadosUltimosMesesPorcentaje[$key] =  round(($artesTerminadosUltimosMeses[$key] + $desarrolloCompletoSolicitudesTotalesUltimosMeses [$key]) / $val * 100, 1);
                $totalMaterialesCreadosUltimosMesesPorcentaje[$key] =  round(((($artesTerminadosUltimosMeses[$key] + $desarrolloCompletoSolicitudesTotalesUltimosMeses[$key]) * 100) / ($solicitudesTotalesUltimosMeses[$key] + $artesCreadosUltimosMeses[$key])), 1);
            }
        }

        // vendedores o creadores:
        if ($tipo_vendedor == 1) {
            $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4, 19])->get();
            $vendedores->map(function ($vendedor) {
                $vendedor->vendedor_id = $vendedor->id;
            });
        } elseif ($tipo_vendedor == 4) {
            $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4])->get();
            $vendedores->map(function ($vendedor) {
                $vendedor->vendedor_id = $vendedor->id;
            });
        } elseif ($tipo_vendedor == 19) {
            $vendedores = User::where('active', 1)->whereIn('role_id', [19])->get();
            $vendedores->map(function ($vendedor) {
                $vendedor->vendedor_id = $vendedor->id;
            });
        }

        // clientes:
        $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->get();
        $clients->map(function ($client) {
            $client->client_id = $client->id;
        });

        // estados:
        $estados = States::where('status', '=', 'active')->get();
        $estados->map(function ($estado) {
            $estado->estado_id = $estado->id;
        });

        // mes y año:
        // $mes  = date('m');
        $year_ini = date('Y') + 0;
        $year_fin = 2020;
        $years = [];
        for ($i = $year_ini; $i >= $year_fin; $i--) {
            $years[] = $i;
        }


        // Si se solicito exportar
        if (!is_null(request()->input('exportar'))) {
            $query = WorkOrder::with(
                'canal',
                'client',
                'creador',
                'productType',
                "area",
                "ultimoCambioEstado.area",
                "vendedorAsignado.user",
                "ingenieroAsignado.user",
                "diseñadorAsignado.user",
                "catalogadorAsignado.user",
                "tecnicoMuestrasAsignado.user",
                "users",
                "gestiones.respuesta",
                "material"
            );
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
            // Calculo total de tiempo
            $query = $query->withCount([
                'gestiones AS tiempo_total' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
                }
            ]);

            if ($tipo_vendedor == 1) {
                if (!is_null(request()->query('vendedor_id'))) {
                    $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
                }
            } elseif ($tipo_vendedor == 4) {
                $vendedores_array = User::where('active', 1)->whereIN('role_id', [3, 4])->pluck('id')->toArray();
                //dd(request()->query('vendedor_id'));
                if (!is_null(request()->query('vendedor_id'))) {
                    $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
                } else {
                    $query = $query->whereIn('creador_id', $vendedores_array);
                }
            } elseif ($tipo_vendedor == 19) {
                $vendedores_array = User::where('active', 1)->whereIN('role_id', [19])->pluck('id')->toArray();
                if (!is_null(request()->query('vendedor_id'))) {
                    $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
                } else {
                    $query = $query->whereIn('creador_id', $vendedores_array);
                }
            }

            // Desarrollo y arte con material;
            $query = $query->whereIn('tipo_solicitud', [1, 5]);
            $query = $query->join('managements', 'work_orders.id', 'managements.work_order_id')
                ->where('managements.management_type_id', 1)
                ->whereIn("managements.state_id", [8]) // 8 = Terminados
                ->where('managements.id', function ($q) {
                    $q->select('id')
                        ->from('managements')
                        ->whereColumn('work_order_id', 'work_orders.id')
                        ->where('managements.management_type_id', 1)
                        ->latest()
                        ->limit(1);
                });
            $otsTerminadas = $query->whereBetween('work_orders.created_at', [$fromDate, $toDate])->get();
            $this->descargaReporteOT(null, null, "OTs Terminadas ", $otsTerminadas);
        }

        $creadores = User::select("id", "nombre", "apellido")->where("role_id", 4)->with([
            "desarrollosCompletados" => function ($q) use ($year, $mes) {
                $q = $q->whereYear('work_orders.created_at', '=', $year)->whereMonth('work_orders.created_at', '=', $mes);
            }
        ])->withCount([
            'otsCreadas' => function ($q) use ($year, $mes) {
                $q = $q->whereIn('tipo_solicitud', [1, 5]);
                $q = $q->whereYear('work_orders.created_at', '=', $year)->whereMonth('work_orders.created_at', '=', $mes);
            }
        ])->get()->filter(function ($creador) {
            return $creador->ots_creadas_count > 0;
        });

        $creadores = $creadores->map(function ($creador) {
            $creador->desarrollosCreados = $creador->ots_creadas_count;
            $creador->desarrollosTerminados = count($creador->desarrollosCompletados);
            $creador->ratio_conversion = ($creador->desarrollosCreados > 0 && $creador->desarrollosTerminados > 0) ? round($creador->desarrollosTerminados / $creador->desarrollosCreados * 100, 1) : 0;
            return $creador;
        });
        $creadoresPositivos = $creadores->sortByDesc('desarrollosCreados')->sortByDesc('ratio_conversion')->take(5);
        $creadoresNegativos = $creadores->sortByDesc('desarrollosCreados')->sortBy('ratio_conversion')->take(5);

        $clientes = Client::select("nombre", "id")->whereHas("ots")->with([
            "desarrollosCompletados" => function ($q) use ($year, $mes) {
                $q = $q->whereYear('work_orders.created_at', '=', $year)->whereMonth('work_orders.created_at', '=', $mes);
            }
        ])->withCount([
            'ots' => function ($q) use ($year, $mes) {
                $q = $q->whereIn('tipo_solicitud', [1, 5]);
                $q = $q->whereYear('work_orders.created_at', '=', $year)->whereMonth('work_orders.created_at', '=', $mes);
            }
        ])->get()->filter(function ($client) {
            return $client->ots_count > 0;
        });

        $clientesMayorRatio = $clientes->map(function ($cliente) {
            $cliente->desarrollosCreados = $cliente->ots_count;
            $cliente->desarrollosTerminados = count($cliente->desarrollosCompletados);
            $cliente->ratio_conversion = ($cliente->desarrollosCreados > 0 && $cliente->desarrollosTerminados > 0) ? round($cliente->desarrollosTerminados / $cliente->desarrollosCreados * 100, 1) : 0;
            return $cliente;
            // if ($cliente->desarrollosCreados > 0) {
            // }
        })->sort(function ($a, $b) {
            if ($a->ratio_conversion === $b->ratio_conversion) {
                if ($a->desarrollosCreados === $b->desarrollosCreados) {
                    return 0;
                }
                return $a->desarrollosCreados < $b->desarrollosCreados ? 1 : -1;
            }
            return $a->ratio_conversion < $b->ratio_conversion ? 1 : -1;
        })->take(10);

        // ->sortByDesc('desarrollosCreados')->sortByDesc('ratio_conversion')->take(10);

        $clientesMayorDesarrollos = $clientes->sortByDesc('desarrollosCreados')->take(10);
        return view(
            'reports.reportCompletedOt',
            compact(
                'vendedores',
                'clients',
                'estados',
                'mes',
                'years',
                'meses',
                'solicitudesTotalesUltimosMeses',
                "nombreMeses",
                "nombreMesesSeleccionados",
                "desarrolloCompletoSolicitudesTotalesUltimosMeses",
                "desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje",
                "artesCreadosUltimosMeses",
                "artesTerminadosUltimosMeses",
                "artesTerminadosUltimosMesesPorcentaje",
                'creadoresPositivos',
                'creadoresNegativos',
                "clientesMayorRatio",
                "clientesMayorDesarrollos",
                "tipo_vendedor",
                "totalMaterialesCreadosUltimosMeses",
                "totalMaterialesCreadosUltimosMesesPorcentaje",
            )
        );
    }

    // REPORTE CONVERSION ENTRE FECHAS OTS CREADAS Y OTS TERMINADAS
    public function reportCompletedOtEntreFechas()
    {
        // Filtro por fechas
        if (!is_null(request()->input('date_desde')) and !is_null(request()->input('date_hasta'))) {
            $fromDate = Carbon::createFromFormat(
                'd/m/Y',
                request()->input('date_desde')
            )->toDateString();
            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
        } else {
            $fromDate = Carbon::now()->startOfMonth()->toDateString();
            $toDate = Carbon::tomorrow()->toDateString();
        }



        // Numero de ots creadas entre las fechas seleccionadas por tipo de solicitud
        $query = WorkOrder::with(
            "gestiones",
            "ultimoCambioEstado.area"
        )->select('*');
        if (!is_null(request()->query('vendedor_id'))) {
            $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
        }
        // desarrollo y arte con material
        $ots = $query->whereIn("tipo_solicitud", [1, 5]);
        $ots = $query->whereBetween('work_orders.created_at', [$fromDate, $toDate])->get();

        $solicitudesTotalesUltimosMeses = 0;
        $desarrolloCompletoSolicitudesTotalesUltimosMeses = 0;
        $desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje = 0;


        // Arte con Material
        $artesCreadosUltimosMeses = 0;
        $artesTerminadosUltimosMeses = 0;
        $artesTerminadosUltimosMesesPorcentaje = 0;

        // usamos "&$solicitudesTotalesUltimosMeses" para poder editar el original y no la referencia
        $otsCantidad = $ots->map(function ($ot) use (&$solicitudesTotalesUltimosMeses, &$desarrolloCompletoSolicitudesTotalesUltimosMeses, &$artesCreadosUltimosMeses, &$artesTerminadosUltimosMeses) {
            // desarrollo
            if ($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 7) {

                $solicitudesTotalesUltimosMeses++;
                // Solo acumulamos si es ot terminada
                if (isset($ot->ultimoCambioEstado) && $ot->ultimoCambioEstado->state_id == 8) {
                    $desarrolloCompletoSolicitudesTotalesUltimosMeses++;
                }
            } elseif ($ot->tipo_solicitud == 5) { //Arte con material
                $artesCreadosUltimosMeses++;
                // Solo acumulamos si es ot terminada
                if (isset($ot->ultimoCambioEstado) && $ot->ultimoCambioEstado->state_id == 8) {
                    $artesTerminadosUltimosMeses++;
                }
            }

            return $ot;
        });

        if ($desarrolloCompletoSolicitudesTotalesUltimosMeses > 0) {
            $desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje =  round($desarrolloCompletoSolicitudesTotalesUltimosMeses / $solicitudesTotalesUltimosMeses * 100, 1);
        }
        if ($artesTerminadosUltimosMeses > 0) {
            $artesTerminadosUltimosMesesPorcentaje =  round($artesTerminadosUltimosMeses / $artesCreadosUltimosMeses * 100, 1);
        }

        // vendedores o creadores:
        $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4, 19])->get();
        $vendedores->map(function ($vendedor) {
            $vendedor->vendedor_id = $vendedor->id;
        });

        // clintes:
        $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->get();
        $clients->map(function ($client) {
            $client->client_id = $client->id;
        });

        // estados:
        $estados = States::where('status', '=', 'active')->get();
        $estados->map(function ($estado) {
            $estado->estado_id = $estado->id;
        });


        // Si se solicito exportar
        if (!is_null(request()->input('exportar'))) {
            $query = WorkOrder::with(
                'canal',
                'client',
                'creador',
                'productType',
                "area",
                "ultimoCambioEstado.area",
                "vendedorAsignado.user",
                "ingenieroAsignado.user",
                "diseñadorAsignado.user",
                "catalogadorAsignado.user",
                "tecnicoMuestrasAsignado.user",
                "users",
                "gestiones.respuesta",
                "material"
            );
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
            // Calculo total de tiempo
            $query = $query->withCount([
                'gestiones AS tiempo_total' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
                }
            ]);
            if (!is_null(request()->query('vendedor_id'))) {
                $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
            }
            $query = $query->whereIn('tipo_solicitud', [1, 5]);
            $query = $query->join('managements', 'work_orders.id', 'managements.work_order_id')
                ->where('managements.management_type_id', 1)
                ->whereIn("managements.state_id", [8]) // 8 = Terminados
                ->where('managements.id', function ($q) {
                    $q->select('id')
                        ->from('managements')
                        ->whereColumn('work_order_id', 'work_orders.id')
                        ->where('managements.management_type_id', 1)
                        ->latest()
                        ->limit(1);
                });
            // Filtro por fechas
            $otsTerminadas = $query->whereBetween('work_orders.created_at', [$fromDate, $toDate])->get();
            $this->descargaReporteOT(null, null, "OTs Terminadas ", $otsTerminadas);
        }

        $creadores = User::select("id", "nombre", "apellido")->where("role_id", 4)->with([
            "desarrollosCompletados" => function ($q) use ($toDate, $fromDate) {
                // Filtro por fechas
                $q = $q->whereBetween('work_orders.created_at', [$fromDate, $toDate]);
                // $q = $q->whereBetween('work_orders.created_at', [$fromDate, $toDate]);

            }
        ])->withCount([
            'otsCreadas' => function ($q) use ($toDate, $fromDate) {
                $q = $q->whereIn('tipo_solicitud', [1, 5]);
                // Filtro por fechas
                $q = $q->whereBetween('work_orders.created_at', [$fromDate, $toDate]);
            }
        ])->get()->filter(function ($creador) {
            return $creador->ots_creadas_count > 0;
        });

        $creadores = $creadores->map(function ($creador) {
            $creador->desarrollosCreados = $creador->ots_creadas_count;
            $creador->desarrollosTerminados = count($creador->desarrollosCompletados);
            $creador->ratio_conversion = ($creador->desarrollosCreados > 0 && $creador->desarrollosTerminados > 0) ? round($creador->desarrollosTerminados / $creador->desarrollosCreados * 100, 1) : 0;
            return $creador;
        });
        $creadoresPositivos = $creadores->sortByDesc('desarrollosCreados')->sortByDesc('ratio_conversion')->take(5);
        $creadoresNegativos = $creadores->sortByDesc('desarrollosCreados')->sortBy('ratio_conversion')->take(5);

        $clientes = Client::select("nombre", "id")->whereHas("ots")->with([
            "desarrollosCompletados" => function ($q)  use ($toDate, $fromDate) {
                // Filtro por fechas
                $q = $q->whereBetween('work_orders.created_at', [$fromDate, $toDate]);
            }
        ])->withCount([
            'ots' => function ($q) use ($toDate, $fromDate) {
                $q = $q->whereIn('tipo_solicitud', [1, 5]);
                // Filtro por fechas
                $q = $q->whereBetween('work_orders.created_at', [$fromDate, $toDate]);
            }
        ])->get()->filter(function ($client) {
            return $client->ots_count > 0;
        });

        $clientesMayorRatio = $clientes->map(function ($cliente) {
            $cliente->desarrollosCreados = $cliente->ots_count;
            $cliente->desarrollosTerminados = count($cliente->desarrollosCompletados);
            $cliente->ratio_conversion = ($cliente->desarrollosCreados > 0 && $cliente->desarrollosTerminados > 0) ? round($cliente->desarrollosTerminados / $cliente->desarrollosCreados * 100, 1) : 0;
            return $cliente;
            // if ($cliente->desarrollosCreados > 0) {
            // }
        })->sort(function ($a, $b) {
            if ($a->ratio_conversion === $b->ratio_conversion) {
                if ($a->desarrollosCreados === $b->desarrollosCreados) {
                    return 0;
                }
                return $a->desarrollosCreados < $b->desarrollosCreados ? 1 : -1;
            }
            return $a->ratio_conversion < $b->ratio_conversion ? 1 : -1;
        })->take(10);


        $clientesMayorDesarrollos = $clientes->sortByDesc('desarrollosCreados')->take(10);

        // Dates Format
        $fromDate = Carbon::now()->startOfMonth()->format('d/m/Y');
        $toDate = Carbon::now()->format('d/m/Y');

        return view(
            'reports.reportCompletedOtEntreFechas',
            compact(
                'fromDate',
                'toDate',
                'vendedores',
                'clients',
                'estados',
                'solicitudesTotalesUltimosMeses',
                "desarrolloCompletoSolicitudesTotalesUltimosMeses",
                "desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje",
                "artesCreadosUltimosMeses",
                "artesTerminadosUltimosMeses",
                "artesTerminadosUltimosMesesPorcentaje",
                'creadoresPositivos',
                'creadoresNegativos',
                "clientesMayorRatio",
                "clientesMayorDesarrollos"
            )
        );
    }

    // funcion para devolver datos para generar reportes de Tiempos Por Area de OT por mes:
    public function reportTimeByAreaOtMonth()
    {
        $nombreMeses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        // Carbon::setLocale('es');
        // // Filtro por fechas
        if (!is_null(request()->input('mes')) and !is_null(request()->input('year'))) {
            $mes = request()->input('mes');
            $year = request()->input('year')[0];
        } else {
            $mes = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
        }

        $fromDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth(4)->startOfMonth();
        $toDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->endOfMonth();
        $mesSeleccionado = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->format('Y-m');
        $meses = [];
        $nombreMesesSeleccionados = [];
        $solicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];

        for ($i = 4; $i >= 0; $i--) {
            $meses[] = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('Y-m');
            $nombreMesesSeleccionados[] = $nombreMeses[Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('m') - 1];
        }

        // SECCION SUPERIOR REPORTE " CANTIDADES "
        // Numero de ots creadas entre las fechas seleccionadas por tipo de solicitud
        $query = WorkOrder::select(DB::raw('count(id) as `total_solicitudes`'), DB::raw("DATE_FORMAT(created_at, '%Y-%m') new_date"), 'tipo_solicitud');
        if (!is_null(request()->query('vendedor_id'))) {
            $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
        }
        $ots = $query->whereBetween('created_at', [$fromDate, $toDate])->groupBy('new_date', 'tipo_solicitud')->orderBy('new_date')->get();
        // Numero de ots creadas entre las fechas seleccionadas todas las solicitudes

        // usamos "&$solicitudesTotalesUltimosMeses" para poder editar el original y no la referencia
        $otsCantidad = $ots->map(function ($ot) use ($meses, &$solicitudesTotalesUltimosMeses) {
            // sumamos el total del mes al arrego de solicitud totales
            // primero conseguimos la llave del mes para sumarle la cantidad
            $key = array_search($ot->new_date, $meses);
            if ($key !== false) {
                $solicitudesTotalesUltimosMeses[$key] +=  $ot->total_solicitudes;
            }
            return $ot;
        });

        // FIN SECCION SUPERIOR REPORTE " CANTIDADES "


        $query =  WorkOrder::with("ultimoCambioEstado")->select('*', DB::raw("DATE_FORMAT(created_at, '%Y-%m') new_date"));
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
        // Calculo total de tiempo en area de muestras
        $query = $query->withCount([
            'gestiones AS tiempo_muestra' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 6);
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
        // Calculo total de tiempo
        $query = $query->withCount([
            'gestiones AS tiempo_total' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
            }
        ]);
        if (!is_null(request()->query('vendedor_id'))) {
            $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
        }
        $otsDias = $query->whereBetween('created_at', [$fromDate, $toDate])->get();
        $otsDias = $otsDias->map(function ($ot) {
            $ot->dias_trabajados = round($ot->present()->diasTrabajados($ot->tiempo_total), 1);
            $ot->dias_trabajados_venta = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_venta, 1), 1);
            $ot->dias_trabajados_desarrollo = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_desarrollo, 2), 1);
            $ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaSalaMuestra($ot->tiempo_muestra, 6), 1);
            $ot->dias_trabajados_diseño = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_diseño, 3), 1);
            $ot->dias_trabajados_catalogacion = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_catalogacion, 4), 1);
            $ot->dias_trabajados_precatalogacion = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_precatalogacion, 5), 1);
            return $ot;
        });
        // gerardoCode
        // De todas las ots solo seleccionamos las que sean del mes actual antes de
        // $otsMesActual = $otsDias->filter(function ($ot) use ($mesSeleccionado) {
        //     return $ot->created_at->format('Y-m') == $mesSeleccionado;
        // });

        $otsDias = $otsDias->groupBy('new_date');

        $diasPorSolicitudUltimosMeses = [0, 0, 0, 0, 0];
        $numeroPorSolicitudUltimosMeses = [0, 0, 0, 0, 0];

        $ventaPromedioDiasUltimosMeses = [0, 0, 0, 0, 0];
        $ventaPromedioDiasUltimosMesesCount = [0, 0, 0, 0, 0];

        $desarrolloPromedioDiasTotalesUltimosMeses = [0, 0, 0, 0, 0];
        $desarrolloPromedioDiasTotalesUltimosMesesCount = [0, 0, 0, 0, 0];


        $muestrasPromedioDiasTotalesUltimosMeses = [0, 0, 0, 0, 0];
        $muestrasPromedioDiasTotalesUltimosMesesCount = [0, 0, 0, 0, 0];

        $diseñoPromedioDiasTotalesUltimosMeses = [0, 0, 0, 0, 0];
        $diseñoPromedioDiasTotalesUltimosMesesCount = [0, 0, 0, 0, 0];

        $catalogacionPromedioDiasTotalesUltimosMeses = [0, 0, 0, 0, 0];
        $catalogacionPromedioDiasTotalesUltimosMesesCount = [0, 0, 0, 0, 0];

        $precatalogacionPromedioDiasTotalesUltimosMeses = [0, 0, 0, 0, 0];
        $precatalogacionPromedioDiasTotalesUltimosMesesCount = [0, 0, 0, 0, 0];

        $otsDias = $otsDias->map(function ($ots, $new_date) use ($meses, &$diasPorSolicitudUltimosMeses, &$numeroPorSolicitudUltimosMeses, &$ventaPromedioDiasUltimosMeses, &$ventaPromedioDiasUltimosMesesCount, &$desarrolloPromedioDiasTotalesUltimosMeses, &$desarrolloPromedioDiasTotalesUltimosMesesCount, &$muestrasPromedioDiasTotalesUltimosMeses, &$muestrasPromedioDiasTotalesUltimosMesesCount, &$diseñoPromedioDiasTotalesUltimosMeses, &$diseñoPromedioDiasTotalesUltimosMesesCount, &$catalogacionPromedioDiasTotalesUltimosMeses, &$catalogacionPromedioDiasTotalesUltimosMesesCount, &$precatalogacionPromedioDiasTotalesUltimosMeses, &$precatalogacionPromedioDiasTotalesUltimosMesesCount) {
            // sumamos el total del mes al arrego de solicitud totales
            // primero conseguimos la llave del mes para sumarle la cantidad
            $key = array_search($new_date, $meses);
            if ($key !== false) {
                $ots->map(function ($ot) use ($key, &$diasPorSolicitudUltimosMeses, &$numeroPorSolicitudUltimosMeses, &$ventaPromedioDiasUltimosMeses, &$ventaPromedioDiasUltimosMesesCount, &$desarrolloPromedioDiasTotalesUltimosMeses, &$desarrolloPromedioDiasTotalesUltimosMesesCount, &$muestrasPromedioDiasTotalesUltimosMeses, &$muestrasPromedioDiasTotalesUltimosMesesCount, &$diseñoPromedioDiasTotalesUltimosMeses, &$diseñoPromedioDiasTotalesUltimosMesesCount, &$catalogacionPromedioDiasTotalesUltimosMeses, &$catalogacionPromedioDiasTotalesUltimosMesesCount, &$precatalogacionPromedioDiasTotalesUltimosMeses, &$precatalogacionPromedioDiasTotalesUltimosMesesCount) {
                    $diasTrabajados = round($ot->dias_trabajados, 1);
                    $diasPorSolicitudUltimosMeses[$key] += $diasTrabajados;
                    $numeroPorSolicitudUltimosMeses[$key] += 1;

                    $ventaPromedioDiasUltimosMeses[$key] += $ot->dias_trabajados_venta;
                    $ventaPromedioDiasUltimosMesesCount[$key] += 1;
                    // Solo considerar desarrollo si
                    if ($ot->dias_trabajados_desarrollo != 0) {
                        $desarrolloPromedioDiasTotalesUltimosMeses[$key] += $ot->dias_trabajados_desarrollo;
                        $desarrolloPromedioDiasTotalesUltimosMesesCount[$key] += 1;
                    }

                    if ($ot->dias_trabajados_muestra != 0) {
                        $muestrasPromedioDiasTotalesUltimosMeses[$key] += $ot->dias_trabajados_muestra;
                        $muestrasPromedioDiasTotalesUltimosMesesCount[$key] += 1;
                    }

                    if ($ot->dias_trabajados_diseño != 0) {
                        $diseñoPromedioDiasTotalesUltimosMeses[$key] += $ot->dias_trabajados_diseño;
                        $diseñoPromedioDiasTotalesUltimosMesesCount[$key] += 1;
                    }

                    // Precat y Cat Solo se consideran cuando el tipo de solicitud sea desarrolo = 1 o arte con material = 5
                    if ($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 5 || $ot->tipo_solicitud == 7) {

                        if ($ot->dias_trabajados_catalogacion != 0) {
                            $catalogacionPromedioDiasTotalesUltimosMeses[$key] += $ot->dias_trabajados_catalogacion;
                            $catalogacionPromedioDiasTotalesUltimosMesesCount[$key] += 1;
                        }

                        if ($ot->dias_trabajados_precatalogacion != 0) {
                            $precatalogacionPromedioDiasTotalesUltimosMeses[$key] += $ot->dias_trabajados_precatalogacion;
                            $precatalogacionPromedioDiasTotalesUltimosMesesCount[$key] += 1;
                        }
                    }
                    return $ot;
                });
            }
            return $ots;
        });

        foreach ($diasPorSolicitudUltimosMeses as $key => $val) {
            if ($val > 0) {
                $diasPorSolicitudUltimosMeses[$key] =  round($val / $numeroPorSolicitudUltimosMeses[$key], 1);
            }
        }
        foreach ($catalogacionPromedioDiasTotalesUltimosMeses as $key => $val) {
            if ($val > 0) {
                $catalogacionPromedioDiasTotalesUltimosMeses[$key] =  round($val / $catalogacionPromedioDiasTotalesUltimosMesesCount[$key], 1);
            }
        }
        foreach ($desarrolloPromedioDiasTotalesUltimosMeses as $key => $val) {
            if ($val > 0) {
                $desarrolloPromedioDiasTotalesUltimosMeses[$key] =  round($val / $desarrolloPromedioDiasTotalesUltimosMesesCount[$key], 1);
            }
        }
        foreach ($muestrasPromedioDiasTotalesUltimosMeses as $key => $val) {
            if ($val > 0) {
                $muestrasPromedioDiasTotalesUltimosMeses[$key] =  round($val / $muestrasPromedioDiasTotalesUltimosMesesCount[$key], 1);
            }
        }
        foreach ($diseñoPromedioDiasTotalesUltimosMeses as $key => $val) {
            if ($val > 0) {
                $diseñoPromedioDiasTotalesUltimosMeses[$key] =  round($val / $diseñoPromedioDiasTotalesUltimosMesesCount[$key], 1);
            }
        }
        foreach ($ventaPromedioDiasUltimosMeses as $key => $val) {
            if ($val > 0) {
                $ventaPromedioDiasUltimosMeses[$key] =  round($val / $ventaPromedioDiasUltimosMesesCount[$key], 1);
            }
        }
        foreach ($precatalogacionPromedioDiasTotalesUltimosMeses as $key => $val) {
            if ($val > 0) {
                $precatalogacionPromedioDiasTotalesUltimosMeses[$key] =  round($val / $precatalogacionPromedioDiasTotalesUltimosMesesCount[$key], 1);
            }
        }

        // FIN SECCION INFERIOR REPORTE " DIAS "


        // Reporte top vendedores y clientes
        // $creadores = User::select("id", "nombre", "apellido")->whereIn("role_id", [4])->get()->map(function ($creador) {
        //     $creador->ots = 0;
        //     $creador->tiempo = 0;
        //     return $creador;
        // })->keyBy('id');

        // $otsMesActual->map(function ($ot) {
        //     return $ot;
        // });

        $creadores = User::select("id", "nombre", "apellido")->whereIn("role_id", [3, 4, 19])
            ->whereHas('otsCreadas', function ($q) use ($year, $mes) {
                $q = $q->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $mes);
            })->with(['otsCreadas' => function ($q) use ($year, $mes) {
                $q = $q->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $mes);
                // Calculo total de tiempo
                $q = $q->withCount([
                    'gestiones AS tiempo_total' => function ($q) {
                        $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
                    }
                ]);
            }])->get();

        $creadores = $creadores->map(function ($creador) {
            $creador->total_ots = count($creador->otsCreadas);
            $creador->tiempo_total = $creador->otsCreadas->sum('tiempo_total') / 3600 / 9.5;
            $creador->tiempo_promedio = ($creador->total_ots > 0 && $creador->tiempo_total > 0) ? $creador->tiempo_total / $creador->total_ots : 0;
            return $creador;
        })->sortByDesc("tiempo_promedio")->take(10);

        $clientes = Client::select("id", "nombre")
            ->whereHas('ots', function ($q) use ($year, $mes) {
                $q = $q->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $mes);
            })->with(['ots' => function ($q) use ($year, $mes) {
                $q = $q->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $mes);
                // Calculo total de tiempo
                $q = $q->withCount([
                    'gestiones AS tiempo_total' => function ($q) {
                        $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
                    }
                ]);
            }])->get();

        $clientes = $clientes->map(function ($cliente) {
            $cliente->total_ots = count($cliente->ots);
            $cliente->tiempo_total = $cliente->ots->sum('tiempo_total') / 3600 / 9.5;
            $cliente->tiempo_promedio = ($cliente->total_ots > 0 && $cliente->tiempo_total > 0) ? $cliente->tiempo_total / $cliente->total_ots : 0;
            return $cliente;
        })->sortByDesc("tiempo_promedio")->take(10);

        // Fin REPORTE TOP
        // vendedores o creadores:
        $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4, 19])->get();
        $vendedores->map(function ($vendedor) {
            $vendedor->vendedor_id = $vendedor->id;
        });

        // clintes:
        $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->get();
        $clients->map(function ($client) {
            $client->client_id = $client->id;
        });

        // estados:
        $estados = States::where('status', '=', 'active')->get();
        $estados->map(function ($estado) {
            $estado->estado_id = $estado->id;
        });

        // mes y año:
        // $mes  = date('m');
        $year_ini = date('Y') + 0;
        $year_fin = 2020;
        $years = [];
        for ($i = $year_ini; $i >= $year_fin; $i--) {
            $years[] = $i;
        }


        // Si se solicito exportar
        if (!is_null(request()->input('exportar'))) {
            $this->descargaReporteOT($fromDate, $toDate, "Tiempos OT por Área ");
        }

        return view(
            'reports.reportTimeByAreaOtMonth',
            compact(
                'vendedores',
                'clients',
                'mes',
                'years',
                'meses',
                'solicitudesTotalesUltimosMeses',
                "nombreMeses",
                "nombreMesesSeleccionados",
                "diasPorSolicitudUltimosMeses",
                "catalogacionPromedioDiasTotalesUltimosMeses",
                "desarrolloPromedioDiasTotalesUltimosMeses",
                "muestrasPromedioDiasTotalesUltimosMeses",
                "diseñoPromedioDiasTotalesUltimosMeses",
                "ventaPromedioDiasUltimosMeses",
                "precatalogacionPromedioDiasTotalesUltimosMeses",
                "creadores",
                "clientes"
            )
        );
    }

    // funcion para devolver datos para generar reportes de Gestion de OT activas:
    public function reportGestionOtActives()
    {
        $query =  WorkOrder::select('work_orders.*')->with(
            'canal',
            'client',
            'creador',
            'productType',
            "area",
            'gestiones',
            'ultimoCambioEstado.area',
            'ultimoCambioEstado.state',
            "vendedorAsignado.user",
            "ingenieroAsignado.user",
            "diseñadorAsignado.user",
            "catalogadorAsignado.user",
            "tecnicoMuestrasAsignado.user",
            "users",
            "gestiones"
        );
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
        // Calculo total de tiempo en area de muestras
        $query = $query->withCount([
            'gestiones AS tiempo_muestra' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 6);
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
        // Calculo total de tiempo
        $query = $query->withCount([
            'gestiones AS tiempo_total' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
            }
        ]);
        if (!is_null(request()->query('vendedor_id'))) {
            $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
        }
        if (!is_null(request()->query('canal_id'))) {
            $query = $query->whereIn('canal_id', request()->query('canal_id'));
        }

        $query = $query->whereNotIn('tipo_solicitud', [6, 7]);


        $query = $query->join('managements', 'work_orders.id', 'managements.work_order_id')
            ->where('managements.management_type_id', 1)
            ->whereIn("managements.state_id", [1, 2, 3, 4, 5, 6, 7, 10, 12, 14, 15, 16, 17, 18])
            ->where('managements.id', function ($q) {
                $q->select('id')
                    ->from('managements')
                    ->whereColumn('work_order_id', 'work_orders.id')
                    ->where('managements.management_type_id', 1)
                    ->latest()
                    ->limit(1);
            });
        // Filtro por fechas
        // Sin fechas
        //dd($query);
        if (is_null(request()->input('date_desde')) && is_null(request()->input('date_hasta'))) {
            $otsDias = $query->get();
        }
        // Solo viene la fecha hasta
        else if (is_null(request()->input('date_desde')) && !is_null(request()->input('date_hasta'))) {

            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
            $otsDias = $query->whereDate('work_orders.created_at', '<=', $toDate)->get();
        } // Solo viene la fecha desde
        else if (!is_null(request()->input('date_desde')) && is_null(request()->input('date_hasta'))) {

            $fromDate = Carbon::createFromFormat('d/m/Y', request()->input('date_desde'))->toDateString();
            $otsDias = $query->whereDate('work_orders.created_at', '>=', $fromDate)->get();
        } // vienen ambas fechas
        else {
            $fromDate = Carbon::createFromFormat('d/m/Y', request()->input('date_desde'))->toDateString();
            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
            $otsDias = $query->whereBetween('work_orders.created_at', [$fromDate, $toDate])->get();
        }
        // $otsDias = $query->whereBetween('created_at', [$fromDate, $toDate])->get();
        $solicitudesTotales = $otsDias->count();
        // Los indices de los siguientes arreglos representan las areas
        //  1 = venta, 2 desarrollo, 3 diseño, 4 cata, 5 precat, 6 muestras
        $solicitudesPorArea = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
        $porcentajeSolicitudesPorArea = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];

        $totalDesarrolloPorArea = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
        $totalArtePorArea = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
        $totalCotizaConCadPorArea = [1 => 0, 2 => 0, 3 => 0, 6 => 0];
        $totalCotizaSinCadPorArea = [1 => 0, 2 => 0, 3 => 0, 6 => 0];
        $totalMuestraConCadPorArea = [1 => 0, 2 => 0, 3 => 0, 6 => 0];

        $desarrollosPorArea = [1 => [0, 0, 0], 2 => [0, 0, 0], 3 => [0, 0, 0], 4 => [0, 0, 0], 5 => [0, 0, 0], 6 => [0, 0, 0]];
        $desarrollosPorAreaDias = [1 => [0, 0, 0], 2 => [0, 0, 0], 3 => [0, 0, 0], 4 => [0, 0, 0], 5 => [0, 0, 0], 6 => [0, 0, 0]];

        $artesPorArea = [1 => [0, 0, 0], 2 => [0, 0, 0], 3 => [0, 0, 0], 4 => [0, 0, 0], 5 => [0, 0, 0], 6 => [0, 0, 0]];
        $artesPorAreaDias = [1 => [0, 0, 0], 2 => [0, 0, 0], 3 => [0, 0, 0], 4 => [0, 0, 0], 5 => [0, 0, 0], 6 => [0, 0, 0]];


        $cotizaConCadPorArea = [1 => [0, 0, 0], 2 => [0, 0, 0], 3 => [0, 0, 0], 6 => [0, 0, 0]];
        $cotizaConCadPorAreaDias = [1 => [0, 0, 0], 2 => [0, 0, 0], 3 => [0, 0, 0], 6 => [0, 0, 0]];

        $cotizaSinCadPorArea = [1 => [0, 0, 0], 2 => [0, 0, 0], 3 => [0, 0, 0], 6 => [0, 0, 0]];
        $cotizaSinCadPorAreaDias = [1 => [0, 0, 0], 2 => [0, 0, 0], 3 => [0, 0, 0], 6 => [0, 0, 0]];

        $muestraConCadPorArea = [1 => [0, 0, 0], 2 => [0, 0, 0], 3 => [0, 0, 0], 6 => [0, 0, 0]];
        $muestraConCadPorAreaDias = [1 => [0, 0, 0], 2 => [0, 0, 0], 3 => [0, 0, 0], 6 => [0, 0, 0]];

        // cantidad y tiempos de ultimos estados de venta

        $estados = [1 => "Proceso de Ventas", 10 => "Consulta Cliente", 12 => "Rechazada", 14 => "Espera de OC", 15 => "Falta definición del Cliente", 16 => "Visto Bueno Cliente"];
        $cantidadPorEstado = [1 => 0, 10 => 0, 12 => 0, 14 => 0, 15 => 0, 16 => 0];
        $tiempoPorEstado = [1 => 0, 10 => 0, 12 => 0, 14 => 0, 15 => 0, 16 => 0];

        // Si no viene ningun filtro tomamos todos los vendedores de lo contrario usamos los que vengan del filtro
        $responsables = request()->query('vendedor_id') !== null ? User::select("id", "nombre", "apellido", "role_id")->where("active", 1)->whereIn("id", request()->query('vendedor_id'))->get() : User::select("id", "nombre", "apellido", "role_id")->where("active", 1)->whereIn("role_id", [3, 4, 19])->get();

        $responsables = $responsables->map(function ($responsable) {
            $responsable->estados = [1 => 0, 10 => 0, 12 => 0, 14 => 0, 15 => 0, 16 => 0];
            $responsable->total_ots = 0;
            $responsable->tiempo_ventas = 0;
            $responsable->tiempo_total = 0;
            return $responsable;
        })->keyBy('id');

        $otsDias = $otsDias->map(function ($ot) use (&$solicitudesPorArea, &$totalDesarrolloPorArea, &$desarrollosPorArea, &$desarrollosPorAreaDias, &$totalArtePorArea, &$artesPorArea, &$artesPorAreaDias, &$totalCotizaConCadPorArea, &$totalCotizaSinCadPorArea, &$totalMuestraConCadPorArea, &$cotizaConCadPorArea, &$cotizaConCadPorAreaDias, &$cotizaSinCadPorArea, &$cotizaSinCadPorAreaDias, &$muestraConCadPorArea, &$muestraConCadPorAreaDias, $estados, &$cantidadPorEstado, &$tiempoPorEstado, &$responsables) {

            $ot->dias_trabajados = round($ot->present()->diasTrabajados($ot->tiempo_total), 1);
            $ot->dias_trabajados_venta = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_venta, 1), 1);
            $ot->dias_trabajados_desarrollo = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_desarrollo, 2), 1);
            $ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaSalaMuestra($ot->tiempo_muestra, 6), 1);
            $ot->dias_trabajados_diseño = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_diseño, 3), 1);
            $ot->dias_trabajados_catalogacion = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_catalogacion, 4), 1);
            $ot->dias_trabajados_precatalogacion = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_precatalogacion, 5), 1);
            $solicitudesPorArea[$ot->current_area_id]++;

            // 1 => "Desarrollo Completo",2 => "Cotiza con CAD",  3 => "Muestra con CAD", 4 => "Cotiza sin CAD"
            switch ($ot->tipo_solicitud) {
                // Desarrollo Completo
                case 1:
                    $totalDesarrolloPorArea[$ot->current_area_id]++;
                    $this->calcularSemaforo($ot, $desarrollosPorArea, $desarrollosPorAreaDias);
                    break;
                // "Cotiza con CAD"
                case 2:
                    // 4 y 5 son area de precat y cat que solo se contemplan para desarrollos completos
                    if ($ot->current_area_id == 4 || $ot->current_area_id == 5) {
                        break;
                    }
                    $totalCotizaConCadPorArea[$ot->current_area_id]++;
                    $this->calcularSemaforo($ot, $cotizaConCadPorArea, $cotizaConCadPorAreaDias);
                    break;
                // 3 => "Muestra con CAD"
                case 3:
                    // 4 y 5 son area de precat y cat que solo se contemplan para desarrollos completos
                    if ($ot->current_area_id == 4 || $ot->current_area_id == 5) {
                        break;
                    }
                    $totalMuestraConCadPorArea[$ot->current_area_id]++;
                    $this->calcularSemaforo($ot, $muestraConCadPorArea, $muestraConCadPorAreaDias);
                    break;
                // 4 => "Cotiza sin CAD"
                case 4:
                    // 4 y 5 son area de precat y cat que solo se contemplan para desarrollos completos
                    if ($ot->current_area_id == 4 || $ot->current_area_id == 5) {
                        break;
                    }
                    $totalCotizaSinCadPorArea[$ot->current_area_id]++;
                    $this->calcularSemaforo($ot, $cotizaSinCadPorArea, $cotizaSinCadPorAreaDias);
                    break;
                // Arte con Material
                case 5:
                    $totalArtePorArea[$ot->current_area_id]++;
                    $this->calcularSemaforo($ot, $artesPorArea, $artesPorAreaDias);
                    break;
                default:
                    # code...
                    break;
            }
            // si esta en ventas y el estado de la ot se encuentra en los especificados
            if ($ot->current_area_id == 1 && array_key_exists($ot->ultimoCambioEstado->state->id, $estados) && $ot->vendedorAsignado->user->active == 1) {

                $cantidadPorEstado[$ot->ultimoCambioEstado->state->id]++;
                $tiempoPorEstado[$ot->ultimoCambioEstado->state->id] += get_working_hours($ot->ultimoCambioEstado->created_at, Carbon::now());

                $estados = $responsables[$ot->vendedorAsignado->user_id]->estados;
                $estados[$ot->ultimoCambioEstado->state->id]++;
                $responsables[$ot->vendedorAsignado->user_id]->estados = $estados;
                $responsables[$ot->vendedorAsignado->user_id]->total_ots++;
                $responsables[$ot->vendedorAsignado->user_id]->tiempo_venta += get_working_hours($ot->ultimoCambioEstado->created_at, Carbon::now());
            }
            if ($ot->vendedorAsignado->user->active == 1) {
                $responsables[$ot->vendedorAsignado->user_id]->tiempo_total += $ot->dias_trabajados;
            }

            return $ot;
        });

        $responsables = $responsables->map(function ($responsable) {
            $responsable->estados = array_values($responsable->estados);
            if ($responsable->total_ots > 0) {
                // para el tiempo de venta se debe pasar de horas a dias dividiendo entre 9.5 Horas/dia
                $responsable->tiempo_promedio_venta =  round(($responsable->tiempo_venta / 9.5) / $responsable->total_ots, 1);
                $responsable->tiempo_promedio_total =  round($responsable->tiempo_total  / $responsable->total_ots, 1);
            } else {
                $responsable->tiempo_promedio_venta = 0;
                $responsable->tiempo_promedio_total = 0;
            }
            return $responsable;
        })->sortByDesc('total_ots');

        $top_responsables = $responsables->sortByDesc('total_ots');
        foreach ($solicitudesPorArea as $key => $solicitudPorArea) {
            $porcentajeSolicitudesPorArea[$key] = ($solicitudPorArea != 0) ? number_format_unlimited_precision(round($solicitudPorArea * 100 / $solicitudesTotales, 1)) . '%' : '0%';
        }
        $cantidadPorEstado = array_values($cantidadPorEstado);
        $tiempoPorEstado = array_values($tiempoPorEstado);
        $tiempoPromedioPorEstado = [0, 0, 0, 0, 0, 0];
        foreach ($tiempoPromedioPorEstado as $key => $val) {
            if ($tiempoPorEstado[$key] > 0) {
                $tiempoPromedioPorEstado[$key] =  round(($tiempoPorEstado[$key] / 9.5) / $cantidadPorEstado[$key], 1);
            }
        }

        // arreglo asociativo de tipos de solicitud y area
        //////////////////////////////////// "Desarrollo completo" = 1 ["areaVenta" = 1,"desarrollo" = 2]
        $solicitudesPorTipoSolicitudPorArea = [1 => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0], 2 => [1 => 0, 2 => 0, 3 => 0, 6 => 0], 3 => [1 => 0, 2 => 0, 3 => 0, 6 => 0], 4 => [1 => 0, 2 => 0, 3 => 0, 6 => 0], 5 => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0]];
        $solicitudesPorTipoSolicitudPorAreaDias = [1 => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0], 2 => [1 => 0, 2 => 0, 3 => 0, 6 => 0], 3 => [1 => 0, 2 => 0, 3 => 0, 6 => 0], 4 => [1 => 0, 2 => 0, 3 => 0, 6 => 0], 5 => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0]];

        // DESARROLLO
        foreach ($desarrollosPorArea as $key => $desarrolloPorArea) {
            $sumDias = 0;
            foreach ($desarrolloPorArea as $key2 => $totalDias) {
                if ($totalDias != 0) {
                    $solicitudesPorTipoSolicitudPorArea[1][$key] += $desarrollosPorArea[$key][$key2];
                    $sumDias += $desarrollosPorAreaDias[$key][$key2];
                    if ($desarrollosPorAreaDias[$key][$key2] != 0) {;
                        $desarrollosPorAreaDias[$key][$key2] = number_format_unlimited_precision(round($desarrollosPorAreaDias[$key][$key2] / $totalDias, 1));
                    }
                }
            }
            $solicitudesPorTipoSolicitudPorAreaDias[1][$key] += ($sumDias > 0 && $solicitudesPorTipoSolicitudPorArea[1][$key] > 0) ? round($sumDias / $solicitudesPorTipoSolicitudPorArea[1][$key], 1) : 0;
        }

        // Arte con Material
        foreach ($artesPorArea as $key => $artePorArea) {
            $sumDias = 0;
            foreach ($artePorArea as $key2 => $totalDias) {
                if ($totalDias != 0) {
                    $solicitudesPorTipoSolicitudPorArea[5][$key] += $artesPorArea[$key][$key2];
                    $sumDias += $artesPorAreaDias[$key][$key2];
                    if ($artesPorAreaDias[$key][$key2] != 0) {;
                        $artesPorAreaDias[$key][$key2] = number_format_unlimited_precision(round($artesPorAreaDias[$key][$key2] / $totalDias, 1));
                    }
                }
            }
            $solicitudesPorTipoSolicitudPorAreaDias[5][$key] += ($sumDias > 0 && $solicitudesPorTipoSolicitudPorArea[5][$key] > 0) ? round($sumDias / $solicitudesPorTipoSolicitudPorArea[5][$key], 1) : 0;
        }

        // Cotiza con cad
        foreach ($cotizaConCadPorArea as $key => $cotizacadPorArea) {
            $sumDias = 0;
            foreach ($cotizacadPorArea as $key2 => $totalDias) {
                if ($totalDias != 0) {
                    $solicitudesPorTipoSolicitudPorArea[2][$key] += $cotizaConCadPorArea[$key][$key2];
                    $sumDias += $cotizaConCadPorAreaDias[$key][$key2];
                    $cotizaConCadPorAreaDias[$key][$key2] = number_format_unlimited_precision(round($cotizaConCadPorAreaDias[$key][$key2] / $totalDias, 1));
                }
            }
            $solicitudesPorTipoSolicitudPorAreaDias[2][$key] += ($sumDias > 0 && $solicitudesPorTipoSolicitudPorArea[2][$key] > 0) ? round($sumDias / $solicitudesPorTipoSolicitudPorArea[2][$key], 1) : 0;
        }

        foreach ($cotizaSinCadPorArea as $key => $cotizacadPorArea) {
            $sumDias = 0;
            foreach ($cotizacadPorArea as $key2 => $totalDias) {
                if ($totalDias != 0) {
                    $solicitudesPorTipoSolicitudPorArea[4][$key] += $cotizaSinCadPorArea[$key][$key2];
                    $sumDias += $cotizaSinCadPorAreaDias[$key][$key2];
                    $cotizaSinCadPorAreaDias[$key][$key2] = number_format_unlimited_precision(round($cotizaSinCadPorAreaDias[$key][$key2] / $totalDias, 1));
                }
            }
            $solicitudesPorTipoSolicitudPorAreaDias[4][$key] += ($sumDias > 0 && $solicitudesPorTipoSolicitudPorArea[4][$key] > 0) ? round($sumDias / $solicitudesPorTipoSolicitudPorArea[4][$key], 1) : 0;
        }

        foreach ($muestraConCadPorArea as $key => $muestracadPorArea) {
            $sumDias = 0;
            foreach ($muestracadPorArea as $key2 => $totalDias) {
                if ($totalDias != 0) {
                    $solicitudesPorTipoSolicitudPorArea[3][$key] += $muestraConCadPorArea[$key][$key2];
                    $sumDias += $muestraConCadPorAreaDias[$key][$key2];
                    $muestraConCadPorAreaDias[$key][$key2] = number_format_unlimited_precision(round($muestraConCadPorAreaDias[$key][$key2] / $totalDias, 1));
                }
            }
            $solicitudesPorTipoSolicitudPorAreaDias[3][$key] += ($sumDias > 0 && $solicitudesPorTipoSolicitudPorArea[3][$key] > 0) ? round($sumDias / $solicitudesPorTipoSolicitudPorArea[3][$key], 1) : 0;
        }

        //  Reportes secundarios de cantidad y tiempo promedio de estados de venta

        // vendedores o creadores:
        $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4, 19])->get();
        $vendedores->map(function ($vendedor) {
            $vendedor->vendedor_id = $vendedor->id;
        });
        // clientes:
        // $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->get();
        // $clients->map(function ($client) {
        //     $client->client_id = $client->id;
        // });
        // canales:
        $canals = Canal::all();
        $canals->map(function ($canal) {
            $canal->canal_id = $canal->id;
        });


        // Si se solicito exportar
        if (!is_null(request()->input('exportar'))) {
            $this->descargaReporteOT(null, null, "Gestión de OT Activas ", $otsDias);
        }
        return view('reports.reportGestionOtActives', compact('top_responsables', 'vendedores', 'canals', 'solicitudesTotales', 'solicitudesPorArea', 'porcentajeSolicitudesPorArea', 'totalDesarrolloPorArea', 'desarrollosPorArea', 'desarrollosPorAreaDias', 'totalArtePorArea', 'artesPorArea', 'artesPorAreaDias', 'totalCotizaConCadPorArea', 'totalCotizaSinCadPorArea', 'totalMuestraConCadPorArea', 'cotizaConCadPorArea', 'cotizaConCadPorAreaDias', 'cotizaSinCadPorArea', 'cotizaSinCadPorAreaDias', 'muestraConCadPorArea', 'muestraConCadPorAreaDias', 'solicitudesPorTipoSolicitudPorArea', 'solicitudesPorTipoSolicitudPorAreaDias', 'estados', "cantidadPorEstado", "tiempoPromedioPorEstado", "responsables"));
    }

    public function calcularSemaforo($ot, &$tipoSolicitudPorArea, &$tipoSolicitudPorAreaDias)
    {
        switch ($ot->current_area_id) {
            case 1:
                // AREA VENTA
                if ($ot->dias_trabajados_venta <= 1) {
                    $tipoSolicitudPorArea[$ot->current_area_id][0]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][0] += $ot->dias_trabajados_venta;
                } elseif ($ot->dias_trabajados_venta > 1 && $ot->dias_trabajados_venta <= 2) {
                    $tipoSolicitudPorArea[$ot->current_area_id][1]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][1] += $ot->dias_trabajados_venta;
                } else {
                    $tipoSolicitudPorArea[$ot->current_area_id][2]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][2] += $ot->dias_trabajados_venta;
                }
                break;
            case 2:
                $maximoVerde = 1;
                $maximoAmarillo = 2;
                // Si esta marcado el checkbox de muestra o analisis se deben agregar dias por cada color
                if ($ot->muestra == 1 && $ot->id < 4000) {
                    $maximoVerde += 1;
                    $maximoAmarillo += 2;
                }
                if ($ot->analisis == 1) {
                    $maximoVerde += 3;
                    $maximoAmarillo += 5;
                }
                // Area Desarrollo
                if ($ot->dias_trabajados_desarrollo <= $maximoVerde) {
                    $tipoSolicitudPorArea[$ot->current_area_id][0]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][0] += $ot->dias_trabajados_desarrollo;
                } elseif ($ot->dias_trabajados_desarrollo > $maximoVerde && $ot->dias_trabajados_desarrollo <=  $maximoAmarillo) {
                    $tipoSolicitudPorArea[$ot->current_area_id][1]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][1] += $ot->dias_trabajados_desarrollo;
                } else {
                    $tipoSolicitudPorArea[$ot->current_area_id][2]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][2] += $ot->dias_trabajados_desarrollo;
                }
                break;
            case 3:
                // Area Diseño
                if ($ot->dias_trabajados_diseño <= 2) {
                    $tipoSolicitudPorArea[$ot->current_area_id][0]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][0] += $ot->dias_trabajados_diseño;
                } elseif ($ot->dias_trabajados_diseño > 2 && $ot->dias_trabajados_diseño <= 3) {
                    $tipoSolicitudPorArea[$ot->current_area_id][1]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][1] += $ot->dias_trabajados_diseño;
                } else {
                    $tipoSolicitudPorArea[$ot->current_area_id][2]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][2] += $ot->dias_trabajados_diseño;
                }
                break;
            case 4:
                // Area Catalogacion
                if ($ot->dias_trabajados_catalogacion <= 2) {
                    $tipoSolicitudPorArea[$ot->current_area_id][0]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][0] += $ot->dias_trabajados_catalogacion;
                } elseif ($ot->dias_trabajados_catalogacion > 2 && $ot->dias_trabajados_catalogacion <= 3) {
                    $tipoSolicitudPorArea[$ot->current_area_id][1]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][1] += $ot->dias_trabajados_catalogacion;
                } else {
                    $tipoSolicitudPorArea[$ot->current_area_id][2]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][2] += $ot->dias_trabajados_catalogacion;
                }
                break;
            case 5:
                // Area Precatalogacion
                if ($ot->dias_trabajados_precatalogacion <= 1) {
                    $tipoSolicitudPorArea[$ot->current_area_id][0]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][0] += $ot->dias_trabajados_precatalogacion;
                } elseif ($ot->dias_trabajados_precatalogacion > 1 && $ot->dias_trabajados_precatalogacion <= 2) {
                    $tipoSolicitudPorArea[$ot->current_area_id][1]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][1] += $ot->dias_trabajados_precatalogacion;
                } else {
                    $tipoSolicitudPorArea[$ot->current_area_id][2]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][2] += $ot->dias_trabajados_precatalogacion;
                }
                break;
            case 6:
                // Area Muestras
                if ($ot->dias_trabajados_muestra <= 3) {
                    $tipoSolicitudPorArea[$ot->current_area_id][0]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][0] += $ot->dias_trabajados_muestra;
                } elseif ($ot->dias_trabajados_muestra > 3 && $ot->dias_trabajados_muestra <= 5) {
                    $tipoSolicitudPorArea[$ot->current_area_id][1]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][1] += $ot->dias_trabajados_muestra;
                } else {
                    $tipoSolicitudPorArea[$ot->current_area_id][2]++;
                    $tipoSolicitudPorAreaDias[$ot->current_area_id][2] += $ot->dias_trabajados_muestra;
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
                if ($ot->muestra == 1) {
                    $maximoVerde += 1;
                    $maximoAmarillo += 2;
                }
                if ($ot->analisis == 1) {
                    $maximoVerde += 3;
                    $maximoAmarillo += 5;
                }
                // total
                if ($ot->dias_trabajados <= $maximoVerde) {
                    return "success";
                } elseif ($ot->dias_trabajados > $maximoVerde && $ot->dias_trabajados <=  $maximoAmarillo) {
                    return "warning";
                } else {
                    return "danger";
                }
                break;

            default:
                break;
        }
    }

    // funcion para devolver datos para generar reportes de Motivos de Rechazos por mes:
    public function reportReasonsRejectionMonth()
    {
        // Filtro por fechas
        if (!is_null(request()->input('date_desde')) and !is_null(request()->input('date_hasta'))) {
            $fromDate = Carbon::createFromFormat('d/m/Y', request()->input('date_desde'))->toDateString();
            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
        } else {
            $fromDate = Carbon::now()->startOfMonth()->toDateString();
            $toDate = Carbon::tomorrow()->toDateString();
        }


        // $mesSeleccionado = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->format('Y-m');
        $query = Management::with(
            'user',
            "ot.vendedorAsignado.user",
            "ot.ingenieroAsignado.user",
            "ot.diseñadorAsignado.user",
            "ot.catalogadorAsignado.user",
            "ot.tecnicoMuestrasAsignado.user",
            "ot.ultimoCambioEstado",
            "ot.creador",
            "ot.gestiones"
        )->where("state_id", 12);
        $query = $query->whereHas('ot', function ($q) {
            // Filtro por fechas
            // $q = $q->whereBetween('work_orders.created_at', [$fromDate, $toDate]);

            // Calculo total de tiempo en area de venta
            $q = $q->withCount([
                'gestiones AS tiempo_venta' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 1);
                }
            ]);
            // Calculo total de tiempo en area de desarrollo
            $q = $q->withCount([
                'gestiones AS tiempo_desarrollo' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 2);
                }
            ]);
            // Calculo total de tiempo en area de diseño
            $q = $q->withCount([
                'gestiones AS tiempo_diseño' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 3);
                }
            ]);
            // Calculo total de tiempo en area de catalogacion
            $q = $q->withCount([
                'gestiones AS tiempo_catalogacion' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 4);
                }
            ]);
            // Calculo total de tiempo en area de precatalogacion
            $q = $q->withCount([
                'gestiones AS tiempo_precatalogacion' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 5);
                }
            ]);
            // Calculo total de tiempo en area sala de muestras
            $q = $q->withCount([
                'gestiones AS tiempo_muestra' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 6);
                }
            ]);
            // Calculo total de tiempo
            $q = $q->withCount([
                'gestiones AS tiempo_total' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
                }
            ]);
        });


        if (!is_null(request()->query('vendedor_id'))) {
            $query = $query->whereHas('ot', function ($q) {
                $q->whereIn('creador_id', request()->query('vendedor_id'));
            });
        }
        $rechazos = $query->whereBetween('created_at', [$fromDate, $toDate])->get();
        $motivos = [1 => "Falta Información", 2 => "Información Erronea", 3 => "Falta Muestra Física", 4 => "Formato Imagen Inadecuado", 5 => "Medida Erronea", 6 => "Descripción de Producto", 7 => "Plano mal Acotado", 8 => "Error de Digitación", 9 => "Error tipo Sustrato", 10 => "No viable por restricciones", 11 => "Falta CAD para corte", 12 => "Falta OT Chileexpress", 13 => "Falta OT Laboratorio"];
        $motivosCompletos = [1 => 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $motivosIngenieriaAVentas = [1 => 0, 2 => 0, 3 => 0, 10 => 0];
        $motivosIngenieriaAMuestras = [1 => 0, 2 => 0];
        $motivosMuestrasAIngenieria = [1 => 0, 2 => 0, 11 => 0, 12 => 0, 13 => 0];
        $motivosDiseñoAVentas = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 10 => 0];
        $motivosDiseñoAIngenieria = [2 => 0, 5 => 0, 10 => 0];
        $motivosCatalogacionAVentas = [1 => 0, 2 => 0, 6 => 0];
        $motivosCatalogacionAIngenieria = [1 => 0, 5 => 0, 7 => 0, 8 => 0];
        $motivosCatalogacionADiseño = [2 => 0, 9 => 0];
        $motivosPrecatalogacionAVentas = [1 => 0, 2 => 0, 6 => 0];
        $motivosPrecatalogacionAIngenieria = [1 => 0, 5 => 0, 7 => 0, 8 => 0];
        $motivosPrecatalogacionADiseño = [2 => 0, 9 => 0];


        $responsables = User::select("id", "nombre", "apellido", "role_id")->whereIn("role_id", [3, 4, 5, 6, 7, 8, 19])->get()->map(function ($responsable) {
            $responsable->rechazos = 0;
            $responsable->ots = [];
            $responsable->tiempo = 0;
            return $responsable;
        })->keyBy('id');

        $rechazos = $rechazos->map(function ($rechazo, $key) use (
            &$motivosCompletos,
            &$motivosIngenieriaAVentas,
            &$motivosIngenieriaAMuestras,
            &$motivosMuestrasAIngenieria,
            &$motivosDiseñoAVentas,
            &$motivosDiseñoAIngenieria,
            &$motivosCatalogacionAVentas,
            &$motivosCatalogacionAIngenieria,
            &$motivosCatalogacionADiseño,
            &$motivosPrecatalogacionAVentas,
            &$motivosPrecatalogacionAIngenieria,
            &$motivosPrecatalogacionADiseño,
            &$responsables
        ) {
            $motivosCompletos[$rechazo->motive_id]++;
            if ($rechazo->work_space_id == 2) {
                // rechazo de diseño a ventas
                if ($rechazo->consulted_work_space_id == 1) {

                    $motivosIngenieriaAVentas[$rechazo->motive_id]++;
                }
                // rechazo de diseño a muestras
                if ($rechazo->consulted_work_space_id == 6) {

                    $motivosIngenieriaAMuestras[$rechazo->motive_id]++;
                }
            }
            if ($rechazo->work_space_id == 3) {
                // rechazo de diseño a ventas
                if ($rechazo->consulted_work_space_id == 1) {

                    $motivosDiseñoAVentas[$rechazo->motive_id]++;
                }
                // rechazo de diseño a desarrollo
                if ($rechazo->consulted_work_space_id == 2) {
                    $motivosDiseñoAIngenieria[$rechazo->motive_id]++;
                }
            }
            if ($rechazo->work_space_id == 4) {
                // rechazo de catalogacion a ventas
                if ($rechazo->consulted_work_space_id == 1) {

                    $motivosCatalogacionAVentas[$rechazo->motive_id]++;
                }
                // rechazo de catalogacion a desarrollo
                if ($rechazo->consulted_work_space_id == 2) {
                    $motivosCatalogacionAIngenieria[$rechazo->motive_id]++;
                }
                // rechazo de catalogacion a diseño
                if ($rechazo->consulted_work_space_id == 3) {
                    $motivosCatalogacionADiseño[$rechazo->motive_id]++;
                }
            }
            if ($rechazo->work_space_id == 5) {
                // rechazo de catalogacion a ventas
                if ($rechazo->consulted_work_space_id == 1) {

                    $motivosPrecatalogacionAVentas[$rechazo->motive_id]++;
                }
                // rechazo de Precatalogacion a desarrollo
                if ($rechazo->consulted_work_space_id == 2) {
                    $motivosPrecatalogacionAIngenieria[$rechazo->motive_id]++;
                }
                // rechazo de Precatalogacion a diseño
                if ($rechazo->consulted_work_space_id == 3) {
                    $motivosPrecatalogacionADiseño[$rechazo->motive_id]++;
                }
            }
            if ($rechazo->work_space_id == 6) {
                // rechazo de muestras a desarrollo
                if ($rechazo->consulted_work_space_id == 2) {
                    $motivosMuestrasAIngenieria[$rechazo->motive_id]++;
                }
            }

            // Acumular rechazos para reportes de TOP rechazos por usuario
            switch ($rechazo->consulted_work_space_id) {
                case 1: //Venta
                    // Añadir rechazo a total
                    $responsables[$rechazo->ot->vendedorAsignado->user_id]->rechazos++;

                    // Solo agregar ot si no se encuentra en el arreglo
                    if (!in_array($rechazo->ot->id, $responsables[$rechazo->ot->vendedorAsignado->user_id]->ots)) {
                        $otsArray = $responsables[$rechazo->ot->vendedorAsignado->user_id]->ots;
                        $responsables[$rechazo->ot->vendedorAsignado->user_id]->ots = array_merge($otsArray, [$rechazo->ot->id]);
                    }

                    // Calcular tiempo q tomo el rechazo
                    $rechazo->ot->gestiones = $rechazo->ot->gestiones->sortBy('id');
                    $siguienteGestion = false;
                    $tiempoRechazo = 0;
                    foreach ($rechazo->ot->gestiones as $gestion) {
                        if ($siguienteGestion) {
                            $tiempoRechazo = $gestion->duracion_segundos;
                            break;
                        }
                        // Si el rechazo es la ultima gestion calculamos el tiempo q ha pasado desde q se creo esa gestion hasta ahora
                        if ($rechazo->ot->gestiones->last() == $rechazo) {
                            $tiempoRechazo = get_working_hours($this->ot->ultimo_cambio_area, Carbon::now()) * 3600;
                            break;
                        }
                        // de lo contrario seteamos variable auxiliar para obtener tiempo de siguiente gestion
                        if ($rechazo->id == $gestion->id) {
                            $siguienteGestion = true;
                        }
                    }
                    // llevar tiempo de segundos a dias
                    $responsables[$rechazo->ot->vendedorAsignado->user_id]->tiempo += $tiempoRechazo / 3600 / 9.5;
                    break;
                case 2: //Dibujo estructural
                    $responsables[$rechazo->ot->ingenieroAsignado->user_id]->rechazos++;
                    if (!in_array($rechazo->ot->id, $responsables[$rechazo->ot->ingenieroAsignado->user_id]->ots)) {
                        $otsArray = $responsables[$rechazo->ot->ingenieroAsignado->user_id]->ots;
                        $responsables[$rechazo->ot->ingenieroAsignado->user_id]->ots = array_merge($otsArray, [$rechazo->ot->id]);
                    }


                    // Calcular tiempo q tomo el rechazo
                    $rechazo->ot->gestiones = $rechazo->ot->gestiones->sortBy('id');
                    $siguienteGestion = false;
                    $tiempoRechazo = 0;
                    foreach ($rechazo->ot->gestiones as $gestion) {
                        if ($siguienteGestion) {
                            $tiempoRechazo = $gestion->duracion_segundos;
                            break;
                        }
                        // Si el rechazo es la ultima gestion calculamos el tiempo q ha pasado desde q se creo esa gestion hasta ahora
                        if ($rechazo->ot->gestiones->last() == $rechazo) {
                            $tiempoRechazo = get_working_hours($this->ot->ultimo_cambio_area, Carbon::now()) * 3600;
                            break;
                        }
                        // de lo contrario seteamos variable auxiliar para obtener tiempo de siguiente gestion
                        if ($rechazo->id == $gestion->id) {
                            $siguienteGestion = true;
                        }
                    }
                    // llevar tiempo de segundos a dias
                    $responsables[$rechazo->ot->ingenieroAsignado->user_id]->tiempo += $tiempoRechazo / 3600 / 9.5;
                    break;
                case 3: //dibujo grafico
                    $responsables[$rechazo->ot->diseñadorAsignado->user_id]->rechazos++;
                    if (!in_array($rechazo->ot->id, $responsables[$rechazo->ot->diseñadorAsignado->user_id]->ots)) {
                        $otsArray = $responsables[$rechazo->ot->diseñadorAsignado->user_id]->ots;
                        $responsables[$rechazo->ot->diseñadorAsignado->user_id]->ots = array_merge($otsArray, [$rechazo->ot->id]);
                    }

                    // Calcular tiempo q tomo el rechazo
                    $rechazo->ot->gestiones = $rechazo->ot->gestiones->sortBy('id');
                    $siguienteGestion = false;
                    $tiempoRechazo = 0;
                    foreach ($rechazo->ot->gestiones as $gestion) {
                        if ($siguienteGestion) {
                            $tiempoRechazo = $gestion->duracion_segundos;
                            break;
                        }
                        // Si el rechazo es la ultima gestion calculamos el tiempo q ha pasado desde q se creo esa gestion hasta ahora
                        if ($rechazo->ot->gestiones->last() == $rechazo) {
                            $tiempoRechazo = get_working_hours($this->ot->ultimo_cambio_area, Carbon::now()) * 3600;
                            break;
                        }
                        // de lo contrario seteamos variable auxiliar para obtener tiempo de siguiente gestion
                        if ($rechazo->id == $gestion->id) {
                            $siguienteGestion = true;
                        }
                    }
                    // llevar tiempo de segundos a dias
                    $responsables[$rechazo->ot->diseñadorAsignado->user_id]->tiempo += $tiempoRechazo / 3600 / 9.5;
                    break;
                    break;
                default:
                    # code...
                    break;
            }

            return $rechazo;
        });
        $responsables = $responsables->sortByDesc("rechazos");
        $responsablesVentas = $responsables->filter(function ($responsable) {
            return ($responsable->role_id == 3 || $responsable->role_id == 4 || $responsable->role_id == 19) && $responsable->rechazos > 0;
        })->take(5);
        $responsablesDesarrollo = $responsables->filter(function ($responsable) {
            return ($responsable->role_id == 5 || $responsable->role_id == 6) && $responsable->rechazos > 0;
        })->take(5);
        $responsablesDiseño = $responsables->filter(function ($responsable) {
            return ($responsable->role_id == 7 || $responsable->role_id == 8) && $responsable->rechazos > 0;
        })->take(5);
        //     $responsablesVentas,
        //     $responsablesDesarrollo,
        //     $responsablesDiseño
        // );
        // TRANSFORMAR A SIGUIENTE ORDEN DE MAQUETA
        //Descripción de producto ,Error de digitación, Error tipo Sustrato, Falta Informacion , Falta Muestra Fisica, Formato Imagen Inadecuado, Informacion Erronea, Medida Erronea, No viable por Restricciones, Plano mal acotado

        $motivosCompletos = $this->cambiarOrdenMotivos($motivosCompletos);

        $motivosIngenieriaAVentas = array_values($motivosIngenieriaAVentas);
        $motivosIngenieriaAMuestras = array_values($motivosIngenieriaAMuestras);
        $motivosMuestrasAIngenieria = array_values($motivosMuestrasAIngenieria);
        $motivosDiseñoAVentas = array_values($motivosDiseñoAVentas);
        $motivosDiseñoAIngenieria = array_values($motivosDiseñoAIngenieria);
        $motivosCatalogacionAVentas = array_values($motivosCatalogacionAVentas);
        $motivosCatalogacionAIngenieria = array_values($motivosCatalogacionAIngenieria);
        $motivosCatalogacionADiseño = array_values($motivosCatalogacionADiseño);
        $motivosPrecatalogacionAVentas = array_values($motivosPrecatalogacionAVentas);
        $motivosPrecatalogacionAIngenieria = array_values($motivosPrecatalogacionAIngenieria);
        $motivosPrecatalogacionADiseño = array_values($motivosPrecatalogacionADiseño);
        // vendedores o creadores:
        $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4, 19])->get();
        $vendedores->map(function ($vendedor) {
            $vendedor->vendedor_id = $vendedor->id;
        });
        // // clientes:
        $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->get();
        $clients->map(function ($client) {
            $client->client_id = $client->id;
        });


        // $creadores = $creadores->map(function ($creador) {

        //     $creador->desarrollosCreados = $creador->ots_creadas_count;
        //     $creador->desarrollosTerminados = count($creador->desarrollosCompletados);
        //     $creador->ratio_conversion = ($creador->desarrollosCreados > 0 && $creador->desarrollosTerminados > 0) ? round($creador->desarrollosTerminados / $creador->desarrollosCreados * 100, 1) : 0;
        //     return $creador;
        // });
        // $creadoresPositivos = $creadores->sortByDesc('desarrollosCreados')->sortByDesc('ratio_conversion')->take(5);

        // Si se solicito exportar
        if (!is_null(request()->input('exportar'))) {
            // $productions_data = $productions_excel;
            $rechazos_array[] = array(
                "ID Rechazo",
                "Nº OT ",
                "Estado OT",
                "Fecha Creación OT",
                "Días OT",
                "Creador OT",
                "Fecha Rechazo",
                "Tipo Rechazo",
                "Generador Rechazo",
                "Área Generadora",
                "Área Receptora",
                "Observación"
            );
            foreach ($rechazos as $rechazo) {
                $rechazos_array[] = array(
                    'ID Rechazo'  => $rechazo->id,
                    'Nº OT ' => $rechazo->ot->id,
                    'Estado OT' => $rechazo->ot->ultimoCambioEstado->state->nombre,
                    'Fecha Creación OT' => $rechazo->ot->created_at->format('d-m-Y H:i'),
                    'Días OT' => $rechazo->ot->dias_trabajados,
                    'Creador OT' => $rechazo->ot->creador->fullname,
                    'Fecha Rechazo' => $rechazo->created_at->format('d-m-Y H:i'),
                    'Tipo Rechazo' => [1 => "Falta Información", 2 => "Información Erronea", 3 => "Falta Muestra Física", 4 => "Formato Imagen Inadecuado", 5 => "Medida Erronea", 6 => "Descripción de Producto", 7 => "Plano mal Acotado", 8 => "Error de Digitación", 9 => "Error tipo Sustrato", 10 => "No viable por restricciones", 11 => "Falta CAD para corte", 12 => "Falta OT Chileexpress", 13 => "Falta OT Laboratorio"][$rechazo->motive_id],
                    'Generador Rechazo' => $rechazo->user->fullname,
                    'Área Generadora' => $rechazo->area->nombre,
                    'Área Receptora' => $rechazo->area_consultada->nombre,
                    'Observación' => $rechazo->observacion,
                );
            }
            Excel::create('Arbol de Rechazos ' . Carbon::now(), function ($excel) use ($rechazos_array) {
                $excel->setTitle('Arbol de Rechazos');
                $excel->sheet('Arbol de Rechazos', function ($sheet) use ($rechazos_array) {
                    $sheet->fromArray($rechazos_array, null, 'A1', false, false);
                });
            })->download('xlsx');
        }

        // Dates Format
        $fromDate = Carbon::now()->startOfMonth()->format('d/m/Y');
        $toDate = Carbon::now()->format('d/m/Y');


        return view('reports.reportReasonsRejectionMonth', compact(
            'fromDate',
            'toDate',
            'vendedores',
            'clients',
            "motivosCompletos",
            "motivosIngenieriaAVentas",
            "motivosIngenieriaAMuestras",
            "motivosMuestrasAIngenieria",
            "motivosDiseñoAVentas",
            "motivosDiseñoAIngenieria",
            "motivosCatalogacionAVentas",
            "motivosCatalogacionAIngenieria",
            "motivosCatalogacionADiseño",
            "motivosPrecatalogacionAVentas",
            "motivosPrecatalogacionAIngenieria",
            "motivosPrecatalogacionADiseño",
            "responsablesVentas",
            "responsablesDesarrollo",
            "responsablesDiseño"
        ));
    }

    // REPORTE CONVERSION OTS CREADAS Y OTS TERMINADAS
    public function reportRechazosPorMes()
    {
        $nombreMeses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        // Carbon::setLocale('es');
        // // Filtro por fechas
        if (!is_null(request()->input('mes')) and !is_null(request()->input('year'))) {
            $mes = request()->input('mes');
            $year = request()->input('year')[0];
        } else {
            $mes = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
        }

        $fromDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth(5)->startOfMonth();
        $toDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->endOfMonth();
        $mesSeleccionado = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->format('Y-m');
        $meses = [];
        $nombreMesesSeleccionados = [];
        for ($i = 5; $i >= 0; $i--) {
            $meses[] = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('Y-m');
            $nombreMesesSeleccionados[] = $nombreMeses[Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('m') - 1];
        }
        // SECCION SUPERIOR REPORTE " CANTIDADES "
        // Numero de ots creadas entre las fechas seleccionadas por tipo de solicitud
        $query = WorkOrder::with(
            "gestiones",
            "ultimoCambioEstado.area"
        )->select('*', DB::raw("DATE_FORMAT(created_at, '%Y-%m') new_date"));
        if (!is_null(request()->query('vendedor_id'))) {
            $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
        }
        $ots = $query->where("tipo_solicitud", 1)->whereBetween('created_at', [$fromDate, $toDate])->get();
        $ots = $ots->groupBy('new_date');

        $query = Management::with(
            'user',
            "ot.ultimoCambioEstado",
            "ot.creador",
            "ot.gestiones"
        )->where("state_id", 12)->select('*', DB::raw("DATE_FORMAT(created_at, '%Y-%m') new_date"));
        $query = $query->whereHas('ot', function ($q) {
            // Filtro por fechas
            // $q = $q->whereBetween('work_orders.created_at', [$fromDate, $toDate]);

            // Calculo total de tiempo en area de venta
            $q = $q->withCount([
                'gestiones AS tiempo_venta' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 1);
                }
            ]);
            // Calculo total de tiempo en area de desarrollo
            $q = $q->withCount([
                'gestiones AS tiempo_desarrollo' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 2);
                }
            ]);
            // Calculo total de tiempo en area de diseño
            $q = $q->withCount([
                'gestiones AS tiempo_diseño' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 3);
                }
            ]);
            // Calculo total de tiempo en area de catalogacion
            $q = $q->withCount([
                'gestiones AS tiempo_catalogacion' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 4);
                }
            ]);
            // Calculo total de tiempo en area de precatalogacion
            $q = $q->withCount([
                'gestiones AS tiempo_precatalogacion' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 5);
                }
            ]);
            // Calculo total de tiempo en area sala de muestras
            $q = $q->withCount([
                'gestiones AS tiempo_muestra' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 6);
                }
            ]);
            // Calculo total de tiempo
            $q = $q->withCount([
                'gestiones AS tiempo_total' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
                }
            ]);
        });


        if (!is_null(request()->query('vendedor_id'))) {
            $query = $query->whereHas('ot', function ($q) {
                $q->whereIn('creador_id', request()->query('vendedor_id'));
            });
        }
        $rechazos = $query->whereBetween('created_at', [$fromDate, $toDate])->get();
        $rechazos = $rechazos->groupBy('new_date');

        $solicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];
        $desarrolloCompletoSolicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];
        $desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje = [0, 0, 0, 0, 0];
        // ARREGLOS DE MOTIVOS POR MES
        $motivos = [1 => "Falta Información", 2 => "Información Erronea", 3 => "Falta Muestra Física", 4 => "Formato Imagen Inadecuado", 5 => "Medida Erronea", 6 => "Descripción de Producto", 7 => "Plano mal Acotado", 8 => "Error de Digitación", 9 => "Error tipo Sustrato", 10 => "No viable por restricciones", 11 => "Falta CAD para corte", 12 => "Falta OT Chileexpress", 13 => "Falta OT Laboratorio"];
        $faltaInformacion = [0, 0, 0, 0, 0, 0];
        $informacionErronea = [0, 0, 0, 0, 0, 0];
        $faltaMuestraFisica = [0, 0, 0, 0, 0, 0];
        $formatoImagenInadecuado = [0, 0, 0, 0, 0, 0];
        $medidaErronea = [0, 0, 0, 0, 0, 0];
        $descripcionDeProducto = [0, 0, 0, 0, 0, 0];
        $planoMalAcotado = [0, 0, 0, 0, 0, 0];
        $errorDeDigitacion = [0, 0, 0, 0, 0, 0];
        $errorTipoSustrato = [0, 0, 0, 0, 0, 0];
        $noViablePorRestricciones = [0, 0, 0, 0, 0, 0];
        $faltaCadParaCorte = [0, 0, 0, 0, 0, 0];
        $faltaOTChileexpress = [0, 0, 0, 0, 0, 0];
        $faltaOTLaboratorio = [0, 0, 0, 0, 0, 0];


        // usamos "&$faltaInformacion editar el original y no la referencia
        $rechazos = $rechazos->map(function ($rechazos, $new_date) use ($meses, &$faltaInformacion, &$informacionErronea, &$faltaMuestraFisica, &$formatoImagenInadecuado, &$medidaErronea, &$descripcionDeProducto, &$planoMalAcotado, &$errorDeDigitacion, &$errorTipoSustrato, &$noViablePorRestricciones, &$faltaCadParaCorte, &$faltaOTChileexpress, &$faltaOTLaboratorio) {
            $key = array_search($new_date, $meses);
            if ($key !== false) {
                $rechazos->map(function ($rechazo) use ($new_date, $key, &$faltaInformacion, &$informacionErronea, &$faltaMuestraFisica, &$formatoImagenInadecuado, &$medidaErronea, &$descripcionDeProducto, &$planoMalAcotado, &$errorDeDigitacion, &$errorTipoSustrato, &$noViablePorRestricciones, &$faltaCadParaCorte, &$faltaOTChileexpress, &$faltaOTLaboratorio) {
                    switch ($rechazo->motive_id) {
                        case '1':
                            // Falta Informacion
                            $faltaInformacion[$key]++;
                            break;
                        case '2':
                            // Informacion Erronea
                            $informacionErronea[$key]++;
                            break;
                        case '3':
                            // Falta Muestra Física
                            $faltaMuestraFisica[$key]++;
                            break;
                        case '4':
                            // Formato Imagen Inadecuado
                            $formatoImagenInadecuado[$key]++;
                            break;
                        case '5':
                            // Medida Erronea
                            $medidaErronea[$key]++;
                            break;
                        case '6':
                            // Descripción de Producto
                            $descripcionDeProducto[$key]++;
                            break;
                        case '7':
                            // Plano mal Acotado
                            $planoMalAcotado[$key]++;
                            break;
                        case '8':
                            // Error de Digitación
                            $errorDeDigitacion[$key]++;
                            break;
                        case '9':
                            // Error tipo Sustrato
                            $errorTipoSustrato[$key]++;
                            break;
                        case '10':
                            // No viable por restricciones
                            $noViablePorRestricciones[$key]++;
                            break;
                        case '11':
                            // faltaCadParaCorte
                            $faltaCadParaCorte[$key]++;
                            break;

                        case '12':
                            // faltaOTChileexpress
                            $faltaOTChileexpress[$key]++;
                            break;
                        case '13':
                            // faltaOTLaboratorio
                            $faltaOTLaboratorio[$key]++;
                            break;

                        default:
                            # code...
                            break;
                    }
                    return $rechazo;
                });
            }
            return $rechazos;
        });


        // vendedores o creadores:
        $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4, 19])->get();
        $vendedores->map(function ($vendedor) {
            $vendedor->vendedor_id = $vendedor->id;
        });



        // mes y año:
        // $mes  = date('m');
        $year_ini = date('Y') + 0;
        $year_fin = 2020;
        $years = [];
        for ($i = $year_ini; $i >= $year_fin; $i--) {
            $years[] = $i;
        }


        // Si se solicito exportar
        if (!is_null(request()->input('exportar'))) {
            $query = Management::with(
                'user',
                "ot.ultimoCambioEstado",
                "ot.creador",
                "ot.gestiones"
            )->where("state_id", 12)->select('*', DB::raw("DATE_FORMAT(created_at, '%Y-%m') new_date"));
            $query = $query->whereHas('ot', function ($q) {
                // Filtro por fechas
                // $q = $q->whereBetween('work_orders.created_at', [$fromDate, $toDate]);

                // Calculo total de tiempo en area de venta
                $q = $q->withCount([
                    'gestiones AS tiempo_venta' => function ($q) {
                        $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 1);
                    }
                ]);
                // Calculo total de tiempo en area de desarrollo
                $q = $q->withCount([
                    'gestiones AS tiempo_desarrollo' => function ($q) {
                        $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 2);
                    }
                ]);
                // Calculo total de tiempo en area de diseño
                $q = $q->withCount([
                    'gestiones AS tiempo_diseño' => function ($q) {
                        $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 3);
                    }
                ]);
                // Calculo total de tiempo en area de catalogacion
                $q = $q->withCount([
                    'gestiones AS tiempo_catalogacion' => function ($q) {
                        $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 4);
                    }
                ]);
                // Calculo total de tiempo en area de precatalogacion
                $q = $q->withCount([
                    'gestiones AS tiempo_precatalogacion' => function ($q) {
                        $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 5);
                    }
                ]);
                // Calculo total de tiempo
                $q = $q->withCount([
                    'gestiones AS tiempo_total' => function ($q) {
                        $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
                    }
                ]);
            });


            if (!is_null(request()->query('vendedor_id'))) {
                $query = $query->whereHas('ot', function ($q) {
                    $q->whereIn('creador_id', request()->query('vendedor_id'));
                });
            }
            $rechazos = $query->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $mes)->get();
            // $productions_data = $productions_excel;
            $rechazos_array[] = array(
                "ID Rechazo",
                "Nº OT ",
                "Estado OT",
                "Fecha Creación OT",
                "Días OT",
                "Creador OT",
                "Fecha Rechazo",
                "Tipo Rechazo",
                "Generador Rechazo",
                "Área Generadora",
                "Área Receptora",
                "Observación"
            );
            foreach ($rechazos as $rechazo) {
                $rechazos_array[] = array(
                    'ID Rechazo'  => $rechazo->id,
                    'Nº OT ' => $rechazo->ot->id,
                    'Estado OT' => $rechazo->ot->ultimoCambioEstado->state->nombre,
                    'Fecha Creación OT' => $rechazo->ot->created_at->format('d-m-Y H:i'),
                    'Días OT' => $rechazo->ot->dias_trabajados,
                    'Creador OT' => $rechazo->ot->creador->fullname,
                    'Fecha Rechazo' => $rechazo->created_at->format('d-m-Y H:i'),
                    'Tipo Rechazo' => [1 => "Falta Información", 2 => "Información Erronea", 3 => "Falta Muestra Física", 4 => "Formato Imagen Inadecuado", 5 => "Medida Erronea", 6 => "Descripción de Producto", 7 => "Plano mal Acotado", 8 => "Error de Digitación", 9 => "Error tipo Sustrato", 10 => "No viable por restricciones", 11 => "Falta CAD para corte", 12 => "Falta OT Chileexpress", 13 => "Falta OT Laboratorio"][$rechazo->motive_id],
                    'Generador Rechazo' => $rechazo->user->fullname,
                    'Área Generadora' => $rechazo->area->nombre,
                    'Área Receptora' => $rechazo->area_consultada->nombre,
                    'Observación' => $rechazo->observacion,
                );
            }
            Excel::create('Arbol de Rechazos ' . Carbon::now(), function ($excel) use ($rechazos_array) {
                $excel->setTitle('Arbol de Rechazos');
                $excel->sheet('Arbol de Rechazos', function ($sheet) use ($rechazos_array) {
                    $sheet->fromArray($rechazos_array, null, 'A1', false, false);
                });
            })->download('xlsx');
        }

        return view('reports.reportRechazosPorMes', compact('vendedores', 'mes', 'years', 'meses', 'solicitudesTotalesUltimosMeses', "nombreMeses", "nombreMesesSeleccionados", "faltaInformacion", "informacionErronea", "faltaMuestraFisica", "formatoImagenInadecuado", "medidaErronea", "descripcionDeProducto", "planoMalAcotado", "errorDeDigitacion", "errorTipoSustrato", "noViablePorRestricciones", "faltaCadParaCorte", "faltaOTChileexpress", "faltaOTLaboratorio"));
    }

    public function index()
    {
        // $ot
        return view('reports.index');
    }

    public function reporte1()
    {
        // Filtro por fechas
        if (!is_null(request()->input('date_desde')) and !is_null(request()->input('date_hasta'))) {
            $fromDate = Carbon::createFromFormat('d/m/Y', request()->input('date_desde'))->toDateString();
            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
        } else {
            $fromDate = Carbon::now()->subMonth(1)->toDateString();
            $toDate = Carbon::now()->toDateString();
        }
        //filters:
        $query = WorkOrder::with('client', 'creador', 'productType', "ultimoCambioEstado.area", "area");
        $query = $query->join('managements', 'work_orders.id', 'managements.work_order_id')
            ->where('managements.management_type_id', 1)
            // ->whereIn("managements.state_id", [1, 2, 3, 4, 5, 6, 7, 8, 10, 12, 13])
            ->where('managements.id', function ($q) {
                $q->select('id')
                    ->from('managements')
                    ->whereColumn('work_order_id', 'work_orders.id')
                    ->where('managements.management_type_id', 1)
                    ->latest()
                    ->limit(1);
            });
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
        $topOts = $query->whereBetween('work_orders.created_at', [$fromDate, $toDate])->get();
        $topDesarrolloCompleto = $topOts->filter(function ($ot) {
            return $ot->tipo_solicitud == 1;
        });
        $topOtrosDesarrollos = $topOts->filter(function ($ot) {
            return $ot->tipo_solicitud != 1;
        });

        $topOt = $this->topFiveOt($topOts);
        $topDesarrolloCompleto = $this->topFiveOt($topDesarrolloCompleto);
        $topOtrosDesarrollos = $this->topFiveOt($topOtrosDesarrollos);
        // $topOt = [0, 1, 2, 3, 4];

        // Dates Format
        $fromDate = Carbon::now()->subMonth(1)->format('d/m/Y');
        $toDate = Carbon::now()->format('d/m/Y');
        return view('reports.reporte1', compact('fromDate', 'toDate', "topOt", "topDesarrolloCompleto", "topOtrosDesarrollos"));
    }

    public function topFiveOt($collection)
    {
        return $collection->map(function ($ot, $key) {
            // para calcular el tiempo total debemos sumar todos los tiempos por area, si esta en un estado contrario a los siguientes significa q
            // debemos sumar el tiempo que ha transcurrido desde el ultimo cambio de estado hasta ahora
            $sum = 0;
            if (isset($ot->ultimoCambioEstado) && !in_array($ot->ultimoCambioEstado->state_id, [8, 9, 11, 13])) {
                $sum = ((Carbon::parse($ot->ultimoCambioEstado->created_at))->diffInSeconds(Carbon::now()));
            }
            $ot->tiempoTotalSegundos = $sum + $ot->tiempo_venta + $ot->tiempo_desarrollo + $ot->tiempo_diseño + $ot->tiempo_catalogacion + $ot->tiempo_precatalogacion;
            // cambiamos de segundos a dias
            $ot->tiempoTotal = Carbon::now()->diffInDays(Carbon::now()->addSeconds($ot->tiempoTotalSegundos));
            return $ot;
        })->sortByDesc('tiempoTotal')->slice(0, 5);
    }

    public function cambiarOrdenMotivos($arr)
    {
        // orden actual
        // 1 => "Falta Información", 2 => "Información Erronea", 3 => "Falta Muestra Física", 4 => "Formato Imagen Inadecuado", 5 => "Medida Erronea"
        // , 6 => "Descripción de Producto", 7 => "Plano mal Acotado", 8 => "Error de Digitación", 9 => "Error tipo Sustrato", 10 => "No viable por restricciones",11 => "Falta CAD para corte", 12 => "Falta OT Chileexpress", 13 => "Falta OT Laboratorio"
        // nuevo orden
        //Descripción de producto ,Error de digitación, Error tipo Sustrato, Falta Informacion , Falta Muestra Fisica, Formato Imagen Inadecuado, Informacion Erronea, Medida Erronea, No viable por Restricciones, Plano mal acotado, 11 => "Falta CAD para corte", 12 => "Falta OT Chileexpress", 13 => "Falta OT Laboratorio"
        $new_arr_order = [$arr[6], $arr[8], $arr[9], $arr[1], $arr[3], $arr[4], $arr[2], $arr[5], $arr[10], $arr[7], $arr[11], $arr[12], $arr[13]];
        return $new_arr_order;
    }

    public function descargaReporteOT($fromDate, $toDate, $titulo, $ordenes = null)
    {
        // $productions_data = $productions_excel;
        if ($ordenes) {
            $ots = $ordenes;
        } else {
            $query = WorkOrder::with(
                'canal',
                'client',
                'creador',
                'productType',
                "area",
                "ultimoCambioEstado.area",
                "ultimoCambioEstado.state",
                "vendedorAsignado.user",
                "ingenieroAsignado.user",
                "diseñadorAsignado.user",
                "catalogadorAsignado.user",
                "tecnicoMuestrasAsignado.user",
                "users",
                "gestiones.respuesta",
                "material",
                "subsubhierarchy.subhierarchy.hierarchy"
            );
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
            // Calculo total de tiempo
            $query = $query->withCount([
                'gestiones AS tiempo_total' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
                }
            ]);
            // Calculo total de tiempo en area sala de muestras
            $query = $query->withCount([
                'gestiones AS tiempo_muestra' => function ($q) {
                    $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 6);
                }
            ]);
            if (!is_null(request()->query('vendedor_id'))) {
                $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
            }
            $ots = $query->whereBetween('created_at', [$fromDate, $toDate])->get();
        }
        $ots_array[] = array(

            // "OT", "FECHA CREACIÓN", "CREADOR", "CLIENTE", "  ESTADO", "  OC", "  TIPO SOLICITUD", "  SOL. ANÁLISIS", "   SOL. PLANO", "  SOL. PRUEBA INDUST.", " SOL. DATOS COTIZAR", "  SOL. BOCETO", " SOL. NUEVO MATERIAL", " SOL. MUESTRA", "    TIPO ITEM", "   DESCRIPCIÓN", " CANAL", "   JERARQUÍA 1", " JERARQUÍA 2", " JERARQUÍA 3", " ÁREA ACTUAL", " ÚLTIMO CAMBIO ÁREA", "  Tº Total", "    Vendedor", "    Tº VENTAS", "   Dibujante Técnico ", "   Tº DISEÑO ESTRUCTURAL", "   Diseñador Gráfico   ", "Tº DISEÑO GRÁFICO", "   Pre Catalogador ", "Tº PRE CATALOGACIÓN ", "Catalogador", " Tº CATALOGACIÓN"
            "OT",
            "FECHA CREACIÓN",
            "CREADOR",
            "CLIENTE",
            "ESTADO",
            "MATERIAL",
            "OC",
            "TIPO SOLICITUD",
            "SOL. ANÁLISIS",
            //"SOL. PLANO", :::Requerimiento del Cliente Fecha 03-01-2023:::
            "SOL. PRUEBA INDUST.",
            //"SOL. DATOS COTIZAR", :::Requerimiento del Cliente Fecha 03-01-2023:::
            //"SOL. BOCETO", :::Requerimiento del Cliente Fecha 03-01-2023:::
            //"SOL. NUEVO MATERIAL", :::Requerimiento del Cliente Fecha 03-01-2023:::
            "SOL. MUESTRA",
            "TIPO ITEM",
            "DESCRIPCIÓN",
            "CANAL",
            "JERARQUÍA 1",
            "JERARQUÍA 2",
            "JERARQUÍA 3",
            "ÁREA ACTUAL",
            "ÚLTIMO CAMBIO ÁREA",
            "Tº Total",
            "Vendedor",
            "Tº VENTAS",
            "Dibujante Técnico",
            "Tº DISEÑO ESTRUCTURAL",
            "Técnico de Muestras",
            "Tº SALA DE MUESTRAS",
            "Diseñador Gráfico",
            "Tº DISEÑO GRÁFICO",
            "Complejidad Diseño Gráfico",
            "Pre Catalogador",
            "Tº PRE CATALOGACIÓN",
            "Catalogador",
            "Tº CATALOGACIÓN",
            "Cantidad de Muestras",
            "Indicador Facturación D.E.",
            "Código Carton",
            "Indicador Facturación D.G.",
        );
        foreach ($ots as $ot) {
            $ot->dias_trabajados = round($ot->present()->diasTrabajados($ot->tiempo_total), 1);
            $ot->dias_trabajados_venta = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_venta, 1), 1);
            $ot->dias_trabajados_desarrollo = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_desarrollo, 2), 1);
            $ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaSalaMuestra($ot->tiempo_muestra, 6), 1);
            $ot->dias_trabajados_diseño = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_diseño, 3), 1);
            $ot->dias_trabajados_catalogacion = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_catalogacion, 4), 1);
            $ot->dias_trabajados_precatalogacion = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_precatalogacion, 5), 1);

            // Buscamos todas las muestras que sean distintas a estado 4 (Anuladas)
            $muestras = Muestra::where("work_order_id", $ot->id)
                ->where("estado", "!=", "4")
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->get();
            $cantidad_muestra = self::cantidad_muestras_por_ot($muestras);
            $cantidades_muestras = $cantidad_muestra['cantidad'];

            // Obtenemos el codigo del Carton de la OT
            $carton = Carton::where("id", $ot->carton_id)->first();
            $carton_codigo = ($carton) ? $carton->codigo : '';

            $ots_array[] = array(
                "OT" => $ot->id,
                "FECHA CREACIÓN" => $ot->created_at->format('d-m-Y H:i'),
                "CREADOR" => $ot->creador->fullname,
                "CLIENTE" => $ot->client->nombre,
                "ESTADO" => $ot->ultimoCambioEstado->state->nombre,
                "MATERIAL" => isset($ot->material) ? $ot->material->codigo : '',
                "OC" => isset($ot->oc) ? [1 => "Si", 0 => "No"][$ot->oc] : "",
                "TIPO SOLICITUD" => [1 => "Desarrollo Completo", 2 => "Cotiza con CAD", 3 => "Muestra con CAD", 4 => "Cotiza sin CAD", 5 => "Arte con Material", 6 => "Otras Solicitudes Desarrollo", 7 => "OT Proyectos Innovación"][$ot->tipo_solicitud],
                "SOL. ANÁLISIS" => isset($ot->analisis) ? [1 => "Si", 0 => "No"][$ot->analisis] : "No",
                //"SOL. PLANO" => isset($ot->plano) ? [1 => "Si", 0 => "No"][$ot->plano] : "No", :::Requerimiento del Cliente Fecha 03-01-2023:::
                "SOL. PRUEBA INDUST." => isset($ot->prueba_industrial) ? [1 => "Si", 0 => "No"][$ot->prueba_industrial] : "No",
                //"SOL. DATOS COTIZAR" => isset($ot->datos_cotizar) ? [1 => "Si", 0 => "No"][$ot->datos_cotizar] : "No", :::Requerimiento del Cliente Fecha 03-01-2023:::
                //"SOL. BOCETO" => isset($ot->boceto) ? [1 => "Si", 0 => "No"][$ot->boceto] : "No", :::Requerimiento del Cliente Fecha 03-01-2023:::
                //"SOL. NUEVO MATERIAL" => isset($ot->nuevo_material) ? [1 => "Si", 0 => "No"][$ot->nuevo_material] : "No", :::Requerimiento del Cliente Fecha 03-01-2023:::
                "SOL. MUESTRA" => (isset($ot->muestra) && $ot->muestra == 1) ? "Si" : "No",
                // "SOL. MUESTRA" => (isset($ot->muestra) && $ot->muestra == 1) ? $ot->numero_muestras : "No",
                "TIPO ITEM" => isset($ot->productType) ? $ot->productType->descripcion : '',
                "DESCRIPCIÓN" => $ot->descripcion,
                "CANAL" => isset($ot->canal) ? $ot->canal->nombre : '',
                "JERARQUÍA 1" => $ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->hierarchy->descripcion : "N/A",
                "JERARQUÍA 2" => $ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->descripcion : "N/A",
                "JERARQUÍA 3" => $ot->subsubhierarchy ? $ot->subsubhierarchy->descripcion : "N/A",
                "ÁREA ACTUAL" => isset($ot->area) ? $ot->area->nombre : '',
                "ÚLTIMO CAMBIO ÁREA" => $ot->ultimo_cambio_area->format('d-m-Y H:i'),
                "Tº Total" => $ot->dias_trabajados,
                "Vendedor" => isset($ot->vendedorAsignado) ? $ot->vendedorAsignado->user->fullname : "",
                "Tº VENTAS" => $ot->dias_trabajados_venta,
                " Dibujante Técnico" => isset($ot->ingenieroAsignado) ? $ot->ingenieroAsignado->user->fullname : "",
                "Tº DISEÑO ESTRUCTURAL" => $ot->dias_trabajados_desarrollo,
                "Técnico de Muestras" => isset($ot->tecnicoMuestrasAsignado) ? $ot->tecnicoMuestrasAsignado->user->fullname : "",
                "Tº SALA DE MUESTRAS" => $ot->dias_trabajados_muestra,
                "Diseñador Gráfico  " => isset($ot->diseñadorAsignado) ? $ot->diseñadorAsignado->user->fullname : "",
                "Tº DISEÑO GRÁFICO" => $ot->dias_trabajados_diseño,
                "Complejidad Diseño Gráfico" => is_null($ot->complejidad) ? '' : $ot->complejidad,
                // La asignacion de precatalogacion y catalogacion es la misma
                "Pre Catalogador    " => isset($ot->catalogadorAsignado) ? $ot->catalogadorAsignado->user->fullname : "",
                "Tº PRE CATALOGACIÓN    " => $ot->dias_trabajados_precatalogacion,
                "Catalogador" => isset($ot->catalogadorAsignado) ? $ot->catalogadorAsignado->user->fullname : "",
                "Tº CATALOGACIÓN" => $ot->dias_trabajados_catalogacion,
                "Cantidad de Muestras" => $cantidades_muestras,
                "Indicador Facturación D.E." => isset($ot->indicador_facturacion) ? [1 => 'RRP', 2 => 'E-Commerce', 3 => 'Esquineros', 4 => 'Geometría', 5 => 'Participación nuevo Mercado', 6 => '', 7 => 'Innovación', 8 => 'Sustentabilidad', 9 => 'Automatización', 10 => 'No Aplica', 11 => 'Ahorro', 12 => ''][$ot->indicador_facturacion] : null,
                "Código Carton" => $carton_codigo,
                "Indicador Facturación D.G." => is_null($ot->indicador_facturacion_diseno_grafico) ? '' : $ot->indicador_facturacion_diseno_grafico,
            );
        }

        Excel::create($titulo . Carbon::now(), function ($excel) use ($ots_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet($titulo, function ($sheet) use ($ots_array) {
                $sheet->fromArray($ots_array, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    // funcion para devolver datos para generar reportes de Gestion de Carga de OT activas por Area:
    public function reportActiveOtsPerArea()
    {
        $areas = WorkSpace::where('status', '=', 'active')->where('id', '<>', 6)->get();
        $areas->map(function ($area) {
            $area->area_id = $area->id;
        });
        $users = User::whereIn("role_id", [5, 6])->get();
        $users->map(function ($user) {
            $user->user_id = $user->id;
        });



        // El area actual se toma del filtro o por defecto "dibujo estructural" = 2
        $area_actual = request("area_id") ? request("area_id")[0] : 2;
        $users_actual = request("user_id") ? request("user_id") : User::where('active', 1)->whereIn('role_id', [5, 6])->pluck('id')->toArray();

        // Query OTS activas
        $query =  WorkOrder::select('work_orders.*')->with(
            'client',
            "area",
            'gestiones',
            'ultimoCambioEstado.area',
            "vendedorAsignado.user",
            "ingenieroAsignado.user",
            "diseñadorAsignado.user",
            "catalogadorAsignado.user",
            "tecnicoMuestrasAsignado.user",
            "users",
            "gestiones"
        );
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
        // Calculo total de tiempo en area sala de muestras
        $query = $query->withCount([
            'gestiones AS tiempo_muestra' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 6);
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
        // Calculo total de tiempo
        $query = $query->withCount([
            'gestiones AS tiempo_total' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1);
            }
        ]);
        if (!is_null(request()->query('vendedor_id'))) {
            $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
        }
        if (!is_null(request()->query('canal_id'))) {
            $query = $query->whereIn('canal_id', request()->query('canal_id'));
        }

        $query = $query->join('managements', 'work_orders.id', 'managements.work_order_id')
            ->where('managements.management_type_id', 1)
            ->whereIn("managements.state_id", [1, 2, 3, 4, 5, 6, 7, 10, 12, 14, 15, 16, 17, 18])
            ->where('managements.id', function ($q) {
                $q->select('id')
                    ->from('managements')
                    ->whereColumn('work_order_id', 'work_orders.id')
                    ->where('managements.management_type_id', 1)
                    ->latest()
                    ->limit(1);
            });
        // Sin fechas
        $otsDias = $query->get();

        $responsablesArea = User::whereIn("id", $users_actual)->get()->map(function ($responsable) {
            $responsable->ots_asignadas = 0;
            $responsable->ots_asignadas_en_area = 0;

            $responsable->tiempo_ots_asignadas = 0;
            $responsable->tiempo_ots_asignadas_en_area = 0;

            $responsable->tiempo_promedio_ots_asignadas = 0;
            $responsable->tiempo_promedio_ots_asignadas_en_area = 0;
            return $responsable;
        })->keyBy('id');
        $totalSolicitudesAsignadasAlArea = 0;
        $totalSolicitudesEnArea = 0;
        $tiempoSolicitudesAsignadasAlArea = 0;
        $tiempoSolicitudesEnArea = 0;
        $tiempoPromedioSolicitudesAsignadasAlArea = 0;
        $tiempoPromedioSolicitudesEnArea = 0;
        $otsDias = $otsDias->map(
            function ($ot) use (
                $area_actual,
                &$responsablesArea,
                &$totalSolicitudesAsignadasAlArea,
                &$totalSolicitudesEnArea,
                &$tiempoSolicitudesAsignadasAlArea,
                &$tiempoSolicitudesEnArea
            ) {
                // $ot->dias_trabajados = round($ot->present()->diasTrabajados($ot->tiempo_total), 1);
                $ot->dias_trabajados_venta = $ot->present()->diasTrabajadosPorArea($ot->tiempo_venta, 1);
                $ot->dias_trabajados_desarrollo = $ot->present()->diasTrabajadosPorArea($ot->tiempo_desarrollo, 2);
                $ot->dias_trabajados_muestra = $ot->present()->diasTrabajadosPorAreaSalaMuestra($ot->tiempo_muestra, 6);
                $ot->dias_trabajados_diseño = $ot->present()->diasTrabajadosPorArea($ot->tiempo_diseño, 3);
                $ot->dias_trabajados_catalogacion = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_catalogacion, 4), 1);
                $ot->dias_trabajados_precatalogacion = round($ot->present()->diasTrabajadosPorArea($ot->tiempo_precatalogacion, 5), 1);
                // contabilizar cada ot que tenga un responsable de dicha area o que la OT este en esa area
                switch ($area_actual) {
                    case 1:
                        if (isset($ot->vendedorAsignado) || $ot->current_area_id == 1) {
                            $totalSolicitudesAsignadasAlArea++;
                            $tiempoSolicitudesAsignadasAlArea += $ot->dias_trabajados_venta;
                        }
                        if ($ot->current_area_id == 1) {
                            $totalSolicitudesEnArea++;
                            $tiempoSolicitudesEnArea += $ot->dias_trabajados_venta;
                        }
                        // Si tiene un vendedor asignado y se encuentra en los usuarios responsables seleccionados le sumamos la ot asignada
                        if (isset($ot->vendedorAsignado) && $responsablesArea->contains('id', $ot->vendedorAsignado->user->id)) {
                            $responsablesArea[$ot->vendedorAsignado->user->id]->ots_asignadas++;
                            $responsablesArea[$ot->vendedorAsignado->user->id]->tiempo_ots_asignadas += $ot->dias_trabajados_venta;
                            // Si ademas esta en el area actual lo añadimos al total ots en area
                            if ($ot->current_area_id == 1) {
                                $responsablesArea[$ot->vendedorAsignado->user->id]->ots_asignadas_en_area++;
                                $responsablesArea[$ot->vendedorAsignado->user->id]->tiempo_ots_asignadas_en_area += $ot->dias_trabajados_venta;
                            }
                        }
                        break;
                    case 2:
                        if (isset($ot->ingenieroAsignado) || $ot->current_area_id == 2) {
                            $totalSolicitudesAsignadasAlArea++;
                            $tiempoSolicitudesAsignadasAlArea += $ot->dias_trabajados_desarrollo;
                        }
                        if ($ot->current_area_id == 2) {
                            $totalSolicitudesEnArea++;
                            $tiempoSolicitudesEnArea += $ot->dias_trabajados_desarrollo;
                        }
                        // Si tiene un ingeniero asignado  y se encuentra en los usuarios responsables seleccionados le sumamos la ot asignada
                        if (isset($ot->ingenieroAsignado)  && $responsablesArea->contains('id', $ot->ingenieroAsignado->user->id)) {
                            $responsablesArea[$ot->ingenieroAsignado->user->id]->ots_asignadas++;
                            $responsablesArea[$ot->ingenieroAsignado->user->id]->tiempo_ots_asignadas += $ot->dias_trabajados_desarrollo;

                            // Si ademas esta en el area actual lo añadimos al total ots en area
                            if ($ot->current_area_id == 2) {
                                $responsablesArea[$ot->ingenieroAsignado->user->id]->ots_asignadas_en_area++;
                                $responsablesArea[$ot->ingenieroAsignado->user->id]->tiempo_ots_asignadas_en_area += $ot->dias_trabajados_desarrollo;
                            }
                        }
                        break;
                    case 3:
                        if (isset($ot->diseñadorAsignado) || $ot->current_area_id == 3) {
                            $totalSolicitudesAsignadasAlArea++;
                            $tiempoSolicitudesAsignadasAlArea += $ot->dias_trabajados_diseño;
                        }
                        if ($ot->current_area_id == 3) {
                            $totalSolicitudesEnArea++;
                            $tiempoSolicitudesEnArea += $ot->dias_trabajados_diseño;
                        } // Si tiene un diseñador asignado  y se encuentra en los usuarios responsables seleccionados le sumamos la ot asignada
                        if (isset($ot->diseñadorAsignado)  && $responsablesArea->contains('id', $ot->diseñadorAsignado->user->id)) {
                            $responsablesArea[$ot->diseñadorAsignado->user->id]->ots_asignadas++;
                            $responsablesArea[$ot->diseñadorAsignado->user->id]->tiempo_ots_asignadas += $ot->dias_trabajados_diseño;

                            // Si ademas esta en el area actual lo añadimos al total ots en area
                            if ($ot->current_area_id == 3) {
                                $responsablesArea[$ot->diseñadorAsignado->user->id]->ots_asignadas_en_area++;
                                $responsablesArea[$ot->diseñadorAsignado->user->id]->tiempo_ots_asignadas_en_area += $ot->dias_trabajados_diseño;
                            }
                        }
                        break;
                    case 4:
                        if (isset($ot->catalogadorAsignado) || $ot->current_area_id == 4) {
                            $totalSolicitudesAsignadasAlArea++;
                            $tiempoSolicitudesAsignadasAlArea += $ot->dias_trabajados_catalogacion;
                        }
                        if ($ot->current_area_id == 4) {
                            $totalSolicitudesEnArea++;
                            $tiempoSolicitudesEnArea += $ot->dias_trabajados_catalogacion;
                        } // Si tiene un diseñador asignado  y se encuentra en los usuarios responsables seleccionados le sumamos la ot asignada
                        if (isset($ot->catalogadorAsignado)  && $responsablesArea->contains('id', $ot->catalogadorAsignado->user->id)) {
                            $responsablesArea[$ot->catalogadorAsignado->user->id]->ots_asignadas++;
                            $responsablesArea[$ot->catalogadorAsignado->user->id]->tiempo_ots_asignadas += $ot->dias_trabajados_catalogacion;

                            // Si ademas esta en el area actual lo añadimos al total ots en area
                            if ($ot->current_area_id == 4) {
                                $responsablesArea[$ot->catalogadorAsignado->user->id]->ots_asignadas_en_area++;
                                $responsablesArea[$ot->catalogadorAsignado->user->id]->tiempo_ots_asignadas_en_area += $ot->dias_trabajados_catalogacion;
                            }
                        }
                        break;
                    case 5:
                        if (isset($ot->catalogadorAsignado) || $ot->current_area_id == 5) {
                            $totalSolicitudesAsignadasAlArea++;
                            $tiempoSolicitudesAsignadasAlArea += $ot->dias_trabajados_precatalogacion;
                        }
                        if ($ot->current_area_id == 5) {
                            $totalSolicitudesEnArea++;
                            $tiempoSolicitudesEnArea += $ot->dias_trabajados_precatalogacion;
                        } // Si tiene un diseñador asignado  y se encuentra en los usuarios responsables seleccionados le sumamos la ot asignada
                        if (isset($ot->catalogadorAsignado)  && $responsablesArea->contains('id', $ot->catalogadorAsignado->user->id)) {
                            $responsablesArea[$ot->catalogadorAsignado->user->id]->ots_asignadas++;
                            $responsablesArea[$ot->catalogadorAsignado->user->id]->tiempo_ots_asignadas += $ot->dias_trabajados_precatalogacion;

                            // Si ademas esta en el area actual lo añadimos al total ots en area
                            if ($ot->current_area_id == 5) {
                                $responsablesArea[$ot->catalogadorAsignado->user->id]->ots_asignadas_en_area++;
                                $responsablesArea[$ot->catalogadorAsignado->user->id]->tiempo_ots_asignadas_en_area += $ot->dias_trabajados_precatalogacion;
                            }
                        }
                        break;
                    case 6:
                        // SALA DE MUESTRAS
                        if (isset($ot->tecnicoMuestrasAsignado) || $ot->current_area_id == 6) {
                            $totalSolicitudesAsignadasAlArea++;
                            $tiempoSolicitudesAsignadasAlArea += $ot->dias_trabajados_muestra;
                        }
                        if ($ot->current_area_id == 6) {
                            $totalSolicitudesEnArea++;
                            $tiempoSolicitudesEnArea += $ot->dias_trabajados_muestra;
                        }
                        // Si tiene un tecnico de muestras asignado  y se encuentra en los usuarios responsables seleccionados le sumamos la ot asignada
                        if (isset($ot->tecnicoMuestrasAsignado)  && $responsablesArea->contains('id', $ot->tecnicoMuestrasAsignado->user->id)) {
                            $responsablesArea[$ot->tecnicoMuestrasAsignado->user->id]->ots_asignadas++;
                            $responsablesArea[$ot->tecnicoMuestrasAsignado->user->id]->tiempo_ots_asignadas += $ot->dias_trabajados_muestra;

                            // Si ademas esta en el area actual lo añadimos al total ots en area
                            if ($ot->current_area_id == 6) {
                                $responsablesArea[$ot->tecnicoMuestrasAsignado->user->id]->ots_asignadas_en_area++;
                                $responsablesArea[$ot->tecnicoMuestrasAsignado->user->id]->tiempo_ots_asignadas_en_area += $ot->dias_trabajados_muestra;
                            }
                        }
                        break;
                    default:
                        # code...
                        break;
                }


                return $ot;
            }
        );
        $responsablesArea = $responsablesArea->map(function ($responsable) {;
            $responsable->tiempo_ots_asignadas = round($responsable->tiempo_ots_asignadas);
            $responsable->tiempo_ots_asignadas_en_area = round($responsable->tiempo_ots_asignadas_en_area);
            $responsable->tiempo_promedio_ots_asignadas = ($responsable->ots_asignadas > 0 &&  $responsable->tiempo_ots_asignadas > 0) ? round($responsable->tiempo_ots_asignadas /  $responsable->ots_asignadas) : 0;
            $responsable->tiempo_promedio_ots_asignadas_en_area = ($responsable->ots_asignadas_en_area > 0 &&  $responsable->tiempo_ots_asignadas_en_area > 0) ? round($responsable->tiempo_ots_asignadas_en_area /  $responsable->ots_asignadas_en_area) : 0;

            return $responsable;
        });

        $tiempoSolicitudesAsignadasAlArea =  round($tiempoSolicitudesAsignadasAlArea);
        $tiempoSolicitudesEnArea =  round($tiempoSolicitudesEnArea);
        $tiempoPromedioSolicitudesAsignadasAlArea = ($totalSolicitudesAsignadasAlArea > 0 &&  $tiempoSolicitudesAsignadasAlArea > 0) ? round($tiempoSolicitudesAsignadasAlArea /  $totalSolicitudesAsignadasAlArea) : 0;
        $tiempoPromedioSolicitudesEnArea = ($totalSolicitudesEnArea > 0 &&  $tiempoSolicitudesEnArea > 0) ? round($tiempoSolicitudesEnArea /  $totalSolicitudesEnArea) : 0;


        // Si se solicito exportar
        if (!is_null(request()->input('exportar'))) {
            // $this->descargaReporteExcel(null, null, "Excel Cartones", null);
            $this->descargaReporteOT(null, null, "Gestión de OTs Activas por Area", $otsDias);
        }


        $colores = ["#806939", "#73e2e6", "#5DA5DA", "#DECF3F", "#FAA43A", "#6e6e6e", "#F17CB0", "#a668f2", "#60BD68", "#F15854"];
        $count = 0;
        return view(
            'reports.reportActiveOtsPerArea',
            compact(
                "area_actual",
                "users_actual",
                "areas",
                "users",
                "colores",
                "count",
                "totalSolicitudesAsignadasAlArea",
                "totalSolicitudesEnArea",
                "responsablesArea",
                "tiempoSolicitudesAsignadasAlArea",
                "tiempoSolicitudesEnArea",
                "tiempoPromedioSolicitudesAsignadasAlArea",
                "tiempoPromedioSolicitudesEnArea"
            )
        );
    }

    public function descargaReporteExcel($fromDate, $toDate, $titulo, $ordenes = null)
    {
        // $productions_data = $productions_excel;
        if ($ordenes) {
            $ots = $ordenes;
        } else {
            $start = microtime(true);
            // Execute the query
            $query = WorkOrder::with(
                'canal',
                'client',
                'creador',
                'productType',
                "users",
                "material",
                "subsubhierarchy",
                "tipo_pallet",
                "cajas_por_paquete",
                "patron_pallet",
                "proteccion_pallet",
                "formato_etiqueta_pallet",
                "qa",
                "prepicado",
                "carton",
                "style"
            );
            if (!is_null(request()->query('vendedor_id'))) {
                $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
            }
            $ots = $query->get();
            // $ots = $query->whereBetween('created_at', [$fromDate, $toDate])->get();
        }
        $ots_array[] = array(

            // "OT", "FECHA CREACIÓN", "CREADOR", "CLIENTE", "  ESTADO", "  OC", "  TIPO SOLICITUD", "  SOL. ANÁLISIS", "   SOL. PLANO", "  SOL. PRUEBA INDUST.", " SOL. DATOS COTIZAR", "  SOL. BOCETO", " SOL. NUEVO MATERIAL", " SOL. MUESTRA", "    TIPO ITEM", "   DESCRIPCIÓN", " CANAL", "   JERARQUÍA 1", " JERARQUÍA 2", " JERARQUÍA 3", " ÁREA ACTUAL", " ÚLTIMO CAMBIO ÁREA", "  Tº Total", "    Vendedor", "    Tº VENTAS", "   Dibujante Técnico", "   Tº DISEÑO ESTRUCTURAL", "   Diseñador Gráfico   ", "Tº DISEÑO GRÁFICO", "   Pre Catalogador ", "Tº PRE CATALOGACIÓN ", "Catalogador", " Tº CATALOGACIÓN"
            "OT",
            "Número de Material",
            "Descripción Comercial",
            "Cliente",
            "Vendedor",
            "Largo Interior (MM)",
            "Ancho Interior (MM)",
            "Alto Interior (MM)",
            "Largura HM (MM)",
            "Anchura HM (MM)",
            "Largo Exterior (MM)",
            "Ancho Exterior (MM)",
            "Alto Exterior (MM)",
            "Cartón",
            "Tipo de Producto (Tipo Item)",
            "Estilo de Producto",
            "Rayado C1/R1 (MM)",
            "Rayado R1/R2 (MM)",
            "Rayado R2/C2 (MM)",
            "Tipo de Rayado",
            "Número de Colores",
            "Recorte Característico (M2)",
            "Recorte Adicional (M2)",
            "Plano CAD",
            "Area Producto (M2)",
            "Estado de Palletizado",
            "Tipo de Pallet",
            "Tratamiento de Pallet",
            "Nro Cajas por Pallet",
            "Nro Placas por Pallet",
            "Patron Carga Pallet",
            "Patron Zuncho Bulto",
            "Proteccion",
            "Patron Zuncho Pallet",
            "Protección Pallet",
            "Nro Cajas por Paquete",
            "Patron Zuncho Paquete",
            "Nro Cajas por Unitizados",
            "Nro Unitizados por Pallet",
            "Tipo Formato Etiqueta Pallet",
            "Nro Etiqueta Pallet",
            "Certificado Calidad",
            "BCT MIN (LB)",
            "BCT MIN (KG)",
            "Tipo Camión",
            "Restricciones Especiales",
            "Horario Recepcion",
            "Destinatario",
            "Jerarquia",
            "Código Producto Cliente",
            "Para uso de Programa Z",
            "Etiqueta FSC",
            "Orientación Placa",
            "Tipo Prepicado",
            "Caracteristicas Adicionales",
            "Observaciones",
            "Referencia Material",
            "Golpes al Largo",
            "Golpes al Ancho",
            "Largura HC (MM)",
            "Anchura HC (MM)",
            "Nombre Color 1",
            "Código Color 1",
            "Gramos Color 1",
            "Nombre Color 2",
            "Código Color 2",
            "Gramos Color 2",
            "Nombre Color 3",
            "Código Color 3",
            "Gramos Color 3",
            "Nombre Color 4",
            "Código Color 4",
            "Gramos Color 4",
            "Nombre Color 5",
            "Código Color 5",
            "Gramos Color 5",
            "Consumo Pegado",
            "Cera Interior",
            "Cera Exterior",
            "Barniz Interior",
            "Barniz Exterior",
            "Veces del Item en el Set ",
            "Armado",
            "Gramaje",
            "Espesor (MM)",
            "Onda",
            "Proceso",
            "Color del Cartón"
        );
        foreach ($ots as $ot) {
            $ots_array[] = array(
                "OT" => $ot->id,
                "Número de Material" => $ot->material ? $ot->material->codigo : null,
                "Descripción Comercial" => $ot->material ? $ot->material->descripcion : null,
                // Cliente
                "Cliente" => $ot->client->codigo,
                "Vendedor" => $ot->creador->fullname,
                "Largo Interior (MM)" => $ot->interno_largo,
                "Ancho Interior (MM)" => $ot->interno_ancho,
                "Alto Interior (MM)" => $ot->interno_alto,
                "Largura HM (MM)" => $ot->largura_hm,
                "Anchura HM (MM)" => $ot->anchura_hm,
                "Largo Exterior (MM)" => $ot->externo_largo,
                "Ancho Exterior (MM)" => $ot->externo_ancho,
                "Alto Exterior (MM)" => $ot->externo_alto,
                // Carton
                "Cartón" => $ot->carton ? $ot->carton->codigo : null,
                "Tipo de Producto (Tipo Item)" => isset($ot->productType) ? $ot->productType->descripcion : null,
                "Estilo de Producto" => isset($ot->style) ? $ot->style->glosa : null,
                "Rayado C1/R1 (MM)" => isset($ot->rayado_c1r1) ? $ot->rayado_c1r1 : null,
                "Rayado R1/R2 (MM)" => isset($ot->rayado_r1_r2) ? $ot->rayado_r1_r2 : null,
                "Rayado R2/C2 (MM)" => isset($ot->rayado_r2_c2) ? $ot->rayado_r2_c2 : null,
                "Tipo de Rayado" => "",
                "Número de Colores" => $ot->numero_colores,
                "Recorte Característico (M2)" => number_format_unlimited_precision($ot->recorteCaracteristico, ",", ".", 7),
                "Recorte Adicional (M2)" => number_format_unlimited_precision($ot->recorteAdicional),
                "Plano CAD" => isset($ot->cad) ? $ot->cad : null,
                "Area Producto (M2)" => number_format_unlimited_precision($ot->area_producto_calculo),
                "Estado de Palletizado" => "",
                "Tipo de Pallet" => $ot->tipo_pallet ? $ot->tipo_pallet->descripcion : null,
                "Tratamiento de Pallet" => isset($ot->pallet_treatment) ? [1 => "Si", 0 => "No"][$ot->pallet_treatment] : null,
                "Nro Cajas por Pallet" => $ot->cajas_por_pallet ? $ot->cajas_por_pallet : null,
                "Nro Placas por Pallet" => $ot->placas_por_pallet ? $ot->placas_por_pallet : null,
                "Patron Carga Pallet" => $ot->patron_pallet ? $ot->patron_pallet->descripcion : null,
                "Patron Zuncho Bulto" => $ot->patron_zuncho_bulto ? [1 => "2x0", 2 => "2x1", 3 => "2x2"][$ot->patron_zuncho_bulto] : null,
                "Protección" => "",
                "Patron Zuncho Pallet" => $ot->patron_zuncho_bulto ? [1 => "2x0", 2 => "2x1", 3 => "2x2"][$ot->patron_zuncho_bulto] : null,
                "Protección Pallet" => $ot->proteccion_pallet ? $ot->proteccion_pallet->descripcion : null,
                "Nro Cajas por Paquete" => $ot->cajas_por_paquete ? $ot->cajas_por_paquete->descripcion : null,
                "Patron Zuncho Paquete" => $ot->patron_zuncho_paquete ? $ot->patron_zuncho_paquete : null,
                "Nro Cajas por Unitizados" => $ot->paquetes_por_unitizado ? $ot->paquetes_por_unitizado : null,
                "Nro Unitizados por Pallet" => $ot->unitizado_por_pallet ? $ot->unitizado_por_pallet : null,
                "Tipo Formato Etiqueta Pallet" => $ot->formato_etiqueta_pallet ? $ot->formato_etiqueta_pallet->descripcion : null,
                "Nro Etiqueta Pallet" => $ot->numero_etiquetas ? [0, 1, 2, 3, 4][$ot->numero_etiquetas] : null,
                "Certificado Calidad" => $ot->qa ? $ot->qa->descripcion : null,
                "BCT MIN (LB)" => $ot->bct_min_lb,
                "BCT MIN (KG)" => $ot->bct_min_kg,
                "Tipo Camión" => $ot->tipo_camion ? $ot->tipo_camion : null,
                "Restricciones Especiales" => $ot->restriccion_especial ? $ot->restriccion_especial : null,
                "Horario Recepcion" => $ot->horario_recepcion ? $ot->horario_recepcion : null,
                "Destinatario" => "",
                "Jerarquia" => $ot->subsubhierarchy ? $ot->subsubhierarchy->jerarquia_sap : null,
                "Código Producto Cliente" => $ot->codigo_producto_cliente ? $ot->codigo_producto_cliente : null,
                "Para uso de Programa Z" => $ot->uso_programa_z ? $ot->uso_programa_z : null,
                "Etiqueta FSC" => isset($ot->etiquetas_dsc) ? [1 => "Si", 0 => "No"][$ot->etiquetas_dsc] : null,
                "Orientación Placa" => isset($ot->orientacion_placa) ? [0, 90][$ot->orientacion_placa] : null,
                "Tipo Prepicado" => $ot->prepicado ? $ot->prepicado->descripcion : null,
                "Caracteristicas Adicionales" => "",
                "Observaciones" => $ot->observacion,
                "Referencia Material" => $ot->bloqueo_referencia ? $ot->material_referencia->codigo : "NO",

                // Conversión Lista de Materiales Cabecera
                "Golpes al Largo" => $ot->golpes_largo,
                "Golpes al Ancho" => $ot->golpes_ancho,
                "Largura HC (MM)" => $ot->larguraHc,
                "Anchura HC (MM)" => $ot->anchuraHc,
                "Nombre Color 1 (INTERIOR TyR)" => $ot->color_1 ? $ot->color_1->descripcion : null,
                "Código Color 1 (INTERIOR TyR)" => $ot->color_1 ? $ot->color_1->codigo : null,
                "Gramos Color 1 (INTERIOR TyR)" => $ot->consumo1,
                "Nombre Color 2" => $ot->color_2 ? $ot->color_2->descripcion : null,
                "Código Color 2" => $ot->color_2 ? $ot->color_2->codigo : null,
                "Gramos Color 2" => $ot->consumo2,
                "Nombre Color 3" => $ot->color_3 ? $ot->color_3->descripcion : null,
                "Código Color 3" => $ot->color_3 ? $ot->color_3->codigo : null,
                "Gramos Color 3" => $ot->consumo3,
                "Nombre Color 4" => $ot->color_4 ? $ot->color_4->descripcion : null,
                "Código Color 4" => $ot->color_4 ? $ot->color_4->codigo : null,
                "Gramos Color 4" => $ot->consumo4,
                "Nombre Color 5" => $ot->color_5 ? $ot->color_5->descripcion : null,
                "Código Color 5" => $ot->color_5 ? $ot->color_5->codigo : null,
                "Gramos Color 5" => $ot->consumo5,
                "Consumo Pegado" => $ot->consumoPegado,
                "Cera Interior" => $ot->consumoCeraInterior,
                "Cera Exterior" => $ot->consumoCeraExterior,
                "Barniz Interior" => $ot->consumoBarniz,
                "Barniz Exterior" => "",
                "Veces del Item en el Set" => isset($ot->veces_item) ? $ot->veces_item : null,
                "Armado" => isset($ot->armado) ? $ot->armado->descripcion : null,

                // Carton
                "Gramaje" => $ot->carton ? number_format($ot->carton->peso, 0, ',', '.') : null,
                "Espesor (MM)" => $ot->carton ? number_format($ot->carton->espesor, 2, ',', '.') : null,
                "Onda" => $ot->carton ? $ot->carton->onda : null,
                "Proceso" => $ot->carton ? number_format($ot->carton->peso, 0, ',', '.') : null,
                "Color del Cartón" => isset($ot->carton_color) ? [1 => "Café", 2 => "Blanco"][$ot->carton_color] : null,


            );
        }

        Excel::create($titulo . Carbon::now(), function ($excel) use ($ots_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet($titulo, function ($sheet) use ($ots_array) {
                $sheet->fromArray($ots_array, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    // REPORTE DE ANULACIONES (DESCARGABLES CON OTS QUE TENGAN MATERIAL REFERIDO)
    public function reportAnulaciones()
    {
        // Filtro por fechas
        if (!is_null(request()->input('date_desde')) and !is_null(request()->input('date_hasta'))) {
            $fromDate = Carbon::createFromFormat(
                'd/m/Y',
                request()->input('date_desde')
            )->toDateString();
            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
        } else {
            $fromDate = Carbon::now()->startOfMonth()->toDateString();
            $toDate = Carbon::tomorrow()->toDateString();
        }

        // Si se solicito exportar
        if (!is_null(request()->input('exportar'))) {
            $query = WorkOrder::with(
                'creador',
                "material",
                "material_referencia"
            );
            // if (!is_null(request()->query('vendedor_id'))) {
            //     $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
            // }
            // Filtro por fechas
            $otsAnulaciones = $query->whereBetween('work_orders.created_at', [$fromDate, $toDate])->get();
            $ots_array[] = array(
                "ID OT",
                "Código Material",
                "Código Referencia",
                "Bloqueo Referencia",
                "Vendedor"
            );
            foreach ($otsAnulaciones as $ot) {
                $ots_array[] = array(
                    "OT" => $ot->id,
                    "Código Material" => isset($ot->material) ? $ot->material->codigo : null,
                    "Código Referencia" => isset($ot->material_referencia) ? $ot->material_referencia->codigo : null,
                    "Bloqueo Referencia" =>  isset($ot->bloqueo_referencia) && $ot->bloqueo_referencia == 1 ? "SI" : "NO",
                    "Vendedor" => $ot->creador->fullname,

                );
            }
            Excel::create("OTs Anulaciones " . Carbon::now(), function ($excel) use ($ots_array) {
                $excel->setTitle("OTs Anulaciones ");
                $excel->sheet("OTs Anulaciones ", function ($sheet) use ($ots_array) {
                    $sheet->fromArray($ots_array, null, 'A1', false, false);
                });
            })->download('xlsx');
        }
        $fromDate = Carbon::now()->startOfMonth()->format('d/m/Y');
        $toDate = Carbon::now()->format('d/m/Y');

        return view(
            'reports.reportAnulaciones',
            compact(
                'fromDate',
                'toDate'
            )
        );
    }

    // REPORTE DE MUESTRAS PENDIENTES
    public function reportMuestras()
    {
        // Filtro por fechas
        if (!is_null(request()->input('date_desde')) and !is_null(request()->input('date_hasta'))) {
            $fromDate = Carbon::createFromFormat(
                'd/m/Y',
                request()->input('date_desde')
            )->toDateString();
            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
        } else {
            $fromDate = Carbon::now()->startOfMonth()->toDateString();
            $toDate = Carbon::tomorrow()->toDateString();
        }

        // Si se solicito exportar
        if (!is_null(request()->input('exportar'))) {

            // }}}}}}}}}
            $titulo = "Listado Muestras Pendientes";
            $muestras = Muestra::where("work_order_id", "!=", "0")->where("estado", "1")->whereBetween('created_at', [$fromDate, $toDate])->get();
            $muestras_array[] = array(
                'ID OT',
                'ID Muestra',
                'CAD',
                'Carton',
                'Carton Muestra',
                'Tipo de Pegado',
                'Cantidad',
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
                    // [1 => "Retira Ventas", 2 => "Envio a Diseñador", 3 => "Laboratorio", 4 => "Envío a Clientes"][$muestra->destinatarios_id[0]],
                    $cantidad,
                    // ($muestra->prioritaria == 1) ? "SI" : "NO",
                    "En Proceso",


                );
            }

            Excel::create($titulo, function ($excel) use ($muestras_array, $titulo) {
                $excel->setTitle($titulo);
                $excel->sheet('Muestras', function ($sheet) use ($muestras_array) {
                    $sheet->fromArray($muestras_array, null, 'A1', true, false);
                });
            })->download('xlsx');

            // }
        }
        $fromDate = Carbon::now()->startOfMonth()->format('d/m/Y');
        $toDate = Carbon::now()->format('d/m/Y');

        return view(
            'reports.reportMuestras',
            compact(
                'fromDate',
                'toDate'
            )
        );
    }

    public function reportIndicadorSalaMuestra()
    {
        // mes y año:
        $year_ini = date('Y') + 0;
        $year_fin = 2020;
        $years = [];
        for ($i = $year_ini; $i >= $year_fin; $i--) {
            $years[] = $i;
        }

        //---- Se busca los meses anteriores al seleccionado en la vista para realizar la busqueda
        $nombreMes = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];

        // // Filtro por fechas
        if (!is_null(request()->input('mes')) and !is_null(request()->input('year'))) {
            $mes_grafica = request()->input('mes');
            $year_grafica = request()->input('year')[0];
        } else {
            $mes_grafica = Carbon::now()->format('m');
            $year_grafica = Carbon::now()->format('Y');
        }

        $mesSeleccionado = Carbon::createFromFormat('Y-m-d', $year_grafica . '-' . $mes_grafica . '-' . '1')->format('Y-m');
        $nombreMesesSeleccionados = [];
        $numero_mes = [];
        $numero_anio = [];
        $fechas_iniciales_mes = [];
        $fechas_finales_mes = [];
        for ($i = 11; $i >= 0; $i--) {
            $anio = Carbon::createFromFormat('Y-m-d', $year_grafica . '-' . $mes_grafica . '-' . '1')->subMonth($i)->format('Y');
            $nombreMesesSeleccionados[] = $nombreMes[Carbon::createFromFormat('Y-m-d', $year_grafica . '-' . $mes_grafica . '-' . '1')->subMonth($i)->format('m') - 1] . ' ' . $anio;
            $numero_mes[] = Carbon::createFromFormat('Y-m-d', $year_grafica . '-' . $mes_grafica . '-' . '1')->subMonth($i)->format('m') - 1;
            $numero_anio[] = Carbon::createFromFormat('Y-m-d', $year_grafica . '-' . $mes_grafica . '-' . '1')->subMonth($i)->format('Y');
            $fechas_iniciales_mes[] = Carbon::createFromFormat('Y-m-d', $year_grafica . '-' . $mes_grafica . '-' . '1')->subMonth($i)->startOfMonth()->toDateTimeString();
            $fechas_finales_mes[] = Carbon::createFromFormat('Y-m-d', $year_grafica . '-' . $mes_grafica . '-' . '1')->subMonth($i)->endOfMonth()->toDateTimeString();
        }

        //---- Se envia el nombre del mes seleccionado a la vista
        $nombreMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        // // Filtro por fechas
        if (!is_null(request()->input('mes')) and !is_null(request()->input('year'))) {
            $mes = request()->input('mes');
            $year = request()->input('year')[0];

            //El mes y el año que se envia desde la vista
            $mesSeleccionado = $nombreMeses[request()->input('mes') - 1];
            $yearSeleccionado = request()->input('year')[0];
        } else {
            $mes = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');

            //Se selecciona por defecto el mes y el año actual
            $mesSeleccionado = $nombreMeses[Carbon::now()->format('m') - 1];
            $yearSeleccionado = $year;
        }

        $fromDate_grafica = Carbon::createFromFormat('Y-m-d', $numero_anio[0] . '-' . $mes[0] . '-' . '1')->startOfMonth();
        $fromDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->startOfMonth();
        $toDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->endOfMonth();

        //************Muestras sin agrupar por OT************

        //************Primero consulto todas las muestras************

        //Consulta de muestras en estado proceso -> 1
        $muestrasEstadoProceso = Muestra::where("work_order_id", "!=", "0")
            ->where("estado", "1")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();

        //Consulta de muestras en estado terminado -> 3
        $muestrasEstadoTerminado = Muestra::where("work_order_id", "!=", "0")
            ->where("estado", "3")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();

        //************Fin de consulta de muestra************

        //------ Calculo de muestras pendientes corte ( solo las que estan con estado de 'proceso')
        $verificar_muestras_fecha_NULL = self::verificar_muestras_fecha_NULL($muestrasEstadoProceso);
        $muestrasPendientesCorte = $verificar_muestras_fecha_NULL['cantidad'];

        //------ Calculo de muestras pendientes termino ( solo las que estan con estado de proceso y con fecha corte )
        // NOTA: Lo que diferencia una muestra pendiente de corte con una muestra pendiente de termino, es que la de termino ya tiene una fecha de "termino" dependiendo de su destinatario
        $verificar_muestras_fecha_con_dato = self::verificar_muestras_fecha_con_dato($muestrasEstadoProceso);
        $muestrasPendientesTermino = $verificar_muestras_fecha_con_dato['cantidad'];

        //------ Calculo de muestras pendientes terminadas ( solo las que estan con estado de 'terminadas')
        $verificar_muestras_sin_fecha = self::verificar_muestras_sin_fecha($muestrasEstadoTerminado);
        $muestrasTerminadas = $verificar_muestras_sin_fecha['cantidad'];


        //************Muestras agrupadas por OT************

        //Calculo de muestras pendientes corte agrupadas por OT ( solo las que estan con estado de 'proceso')
        $muestrasPendientesCortePorOt = self::muestras_pendientes_corte_por_ot($verificar_muestras_fecha_NULL, $fromDate, $toDate);

        //Calculo de muestras pendientes termino agrupadas por OT ( solo las que estan con estado de 'proceso' y con fecha corte)
        $muestrasPendientesTerminoPorOt = self::muestras_pendientes_termino_por_ot($verificar_muestras_fecha_con_dato, $fromDate, $toDate);

        //Calculo de muestras pendientes terminadas agrupadas por OT ( solo las que estan con estado de 'terminadas')
        $muestrasTerminadasPorOt = self::muestras_terminadas_por_ot($fromDate, $toDate);

        //************Consulta de muestras para la grafica************
        $muestrasTerminadasGrafica = self::arreglo_muestras_para_grafica($fechas_iniciales_mes, $fechas_finales_mes);

        return view(
            'reports.reportIndicadorSalaMuestra',
            compact(
                'mes',
                'years',
                'mesSeleccionado',
                'yearSeleccionado',
                'nombreMesesSeleccionados',
                'muestrasPendientesCorte',
                'muestrasPendientesTermino',
                'muestrasTerminadas',
                'muestrasPendientesCortePorOt',
                'muestrasPendientesTerminoPorOt',
                'muestrasTerminadasPorOt',
                'muestrasTerminadasGrafica'
            )
        );
    }

    public function verificar_muestras_fecha_NULL($muestras)
    {
        $cantidadMuestras = 0;
        $destinatarios = array();

        foreach ($muestras as $muestra) {
            if (!in_array($muestra->destinatarios_id[0], $destinatarios)) {
                $destinatarios[] = $muestra->destinatarios_id[0];
            }

            if ($muestra->destinatarios_id[0] == 1 && $muestra->check_fecha_corte_vendedor == NULL && $muestra->fecha_corte_vendedor == NULL) {
                $cantidadMuestras += $muestra->cantidad_vendedor;
            } elseif ($muestra->destinatarios_id[0] == 2 && $muestra->check_fecha_corte_diseñador == NULL && $muestra->fecha_corte_diseñador == NULL) {
                $cantidadMuestras += $muestra->cantidad_diseñador;
            } elseif ($muestra->destinatarios_id[0] == 3 &&  $muestra->check_fecha_corte_laboratorio == NULL && $muestra->fecha_corte_laboratorio == NULL) {
                $cantidadMuestras += $muestra->cantidad_laboratorio;
            } elseif ($muestra->destinatarios_id[0] == 4 && ($muestra->check_fecha_corte_1 == NULL || $muestra->check_fecha_corte_2 == NULL || $muestra->check_fecha_corte_3 == NULL || $muestra->check_fecha_corte_4 == NULL) && $muestra->fecha_corte_1 == NULL) {
                $cantidadMuestras += $muestra->cantidad_1;
            } elseif ($muestra->destinatarios_id[0] == 5 && $muestra->check_fecha_corte_diseñador_revision == NULL && $muestra->fecha_corte_diseñador_revision == NULL) {
                $cantidadMuestras += $muestra->cantidad_diseñador_revision;
            }
        }

        return array(
            'cantidad' => $cantidadMuestras,
            'columnas_destinatarios' => self::traduce_destinatario_id_a_campo($destinatarios),
        );
    }

    public function verificar_muestras_fecha_con_dato($muestras)
    {
        $cantidadMuestras = 0;
        $destinatarios = array();

        foreach ($muestras as $muestra) {
            if (!in_array($muestra->destinatarios_id[0], $destinatarios)) {
                $destinatarios[] = $muestra->destinatarios_id[0];
            }

            if ($muestra->destinatarios_id[0] == 1 && $muestra->check_fecha_corte_vendedor != NULL && $muestra->fecha_corte_vendedor != NULL) {
                $cantidadMuestras += $muestra->cantidad_vendedor;
            } elseif ($muestra->destinatarios_id[0] == 2 && $muestra->check_fecha_corte_diseñador != NULL && $muestra->fecha_corte_diseñador != NULL) {
                $cantidadMuestras += $muestra->cantidad_diseñador;
            } elseif ($muestra->destinatarios_id[0] == 3 &&  $muestra->check_fecha_corte_laboratorio != NULL && $muestra->fecha_corte_laboratorio != NULL) {
                $cantidadMuestras += $muestra->cantidad_laboratorio;
            } elseif ($muestra->destinatarios_id[0] == 4 && ($muestra->check_fecha_corte_1 != NULL || $muestra->check_fecha_corte_2 != NULL || $muestra->check_fecha_corte_3 != NULL || $muestra->check_fecha_corte_4 != NULL) && $muestra->fecha_corte_1 != NULL) {
                $cantidadMuestras += $muestra->cantidad_1;
            } elseif ($muestra->destinatarios_id[0] == 5 && $muestra->check_fecha_corte_diseñador_revision != NULL && $muestra->fecha_corte_diseñador_revision != NULL) {
                $cantidadMuestras += $muestra->cantidad_diseñador_revision;
            }
        }

        return array(
            'cantidad' => $cantidadMuestras,
            'columnas_destinatarios' => self::traduce_destinatario_id_a_campo($destinatarios),
        );
    }

    public function verificar_muestras_sin_fecha($muestras)
    {
        $cantidadMuestras = 0;
        $destinatarios = array();

        foreach ($muestras as $muestra) {
            if (!in_array($muestra->destinatarios_id[0], $destinatarios)) {
                $destinatarios[] = $muestra->destinatarios_id[0];
            }

            if ($muestra->destinatarios_id[0] == 1) {
                $cantidadMuestras += $muestra->cantidad_vendedor;
            } elseif ($muestra->destinatarios_id[0] == 2) {
                $cantidadMuestras += $muestra->cantidad_diseñador;
            } elseif ($muestra->destinatarios_id[0] == 3) {
                $cantidadMuestras += $muestra->cantidad_laboratorio;
            } elseif ($muestra->destinatarios_id[0] == 4) {
                $cantidadMuestras += $muestra->cantidad_1;
            } elseif ($muestra->destinatarios_id[0] == 5) {
                $cantidadMuestras += $muestra->cantidad_diseñador_revision;
            }
        }

        return array(
            'cantidad' => $cantidadMuestras,
            'columnas_destinatarios' => self::traduce_destinatario_id_a_campo($destinatarios),
        );
    }

    public function traduce_destinatario_id_a_campo($destinatarios)
    {
        $columnas = array(
            '1' => [
                // 'check_fecha_corte_vendedor',
                'fecha_corte_vendedor',
            ],
            '2' => [
                // 'check_fecha_corte_diseñador',
                'fecha_corte_diseñador',
            ],
            '3' => [
                // 'check_fecha_corte_laboratorio',
                'fecha_corte_laboratorio',
            ],
            '4' => [
                // 'check_fecha_corte_1',
                // 'check_fecha_corte_2',
                // 'check_fecha_corte_3',
                // 'check_fecha_corte_4',
                'fecha_corte_1',
            ],
            '5' => [
                // 'check_fecha_corte_1',
                // 'check_fecha_corte_2',
                // 'check_fecha_corte_3',
                // 'check_fecha_corte_4',
                'fecha_corte_diseñador_revision',
            ],
        );

        $columnas_traduccion = array();
        foreach ($destinatarios as $destinatario) {
            $columnas_traduccion[] = $columnas[strval($destinatario)];
        }

        return array_merge(...$columnas_traduccion);
    }

    public function muestras_pendientes_corte_por_ot($verificar_muestras_fecha_NULL, $fromDate, $toDate)
    {

        $muestras = Muestra::select(
            DB::raw("COUNT(DISTINCT work_order_id) AS cantidad_ot")
        )->where(function ($query) use ($verificar_muestras_fecha_NULL) {
            foreach ($verificar_muestras_fecha_NULL['columnas_destinatarios'] as $indice => $columna) {
                // if ($indice == 0) {
                $query->where(strval($columna), NULL);
                // } else {
                // $query->orWhere(strval($columna), NULL);
                // }
            }
        })
            ->where("work_order_id", "!=", "0")
            ->where("estado", "1")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->pluck('cantidad_ot')
            ->first();

        return $muestras;
    }

    public function muestras_pendientes_termino_por_ot($verificar_muestras_fecha_con_dato, $fromDate, $toDate)
    {

        $muestras = Muestra::select(
            DB::raw("COUNT(DISTINCT work_order_id) AS cantidad_ot")
        )->where(function ($query) use ($verificar_muestras_fecha_con_dato) {
            foreach ($verificar_muestras_fecha_con_dato['columnas_destinatarios'] as $indice => $columna) {
                if ($indice == 0) {
                    $query->where(strval($columna), "!=",  NULL);
                } else {
                    $query->orWhere(strval($columna), "!=", NULL);
                }
            }
        })
            ->where("work_order_id", "!=", "0")
            ->where("estado", "1")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->pluck('cantidad_ot')
            ->first();

        return $muestras;
    }

    public function muestras_terminadas_por_ot($fromDate, $toDate)
    {

        $query = "
            SELECT COUNT(*) AS total FROM(
                SELECT
                    ot.id AS ot_id,
                    COALESCE(total_muestras.cantidad, 0) AS total_muestras,
                    COALESCE(total_terminada.cantidad, 0) AS total_terminada,
                    if(COALESCE(total_muestras.cantidad, 0) = COALESCE(total_terminada.cantidad, 0), TRUE,FALSE) AS condicion
                FROM work_orders AS ot
                INNER JOIN (
                    SELECT muestras.work_order_id, COUNT(*) as cantidad
                    FROM muestras
                    WHERE
                        muestras.work_order_id != 0
                        AND muestras.created_at between  '" . $fromDate . "' AND  '" . $toDate . "'
                    GROUP BY muestras.work_order_id
                ) AS total_muestras ON total_muestras.work_order_id = ot.id
                LEFT OUTER JOIN (
                    SELECT muestras.work_order_id, COUNT(*) as cantidad
                    FROM muestras
                    WHERE
                        muestras.work_order_id != 0
                        AND muestras.estado = 3
                        AND muestras.created_at between  '" . $fromDate . "' AND  '" . $toDate . "'
                    GROUP BY muestras.work_order_id
                ) AS total_terminada ON total_terminada.work_order_id = ot.id
                WHERE
                    ot.created_at between  '" . $fromDate . "' AND  '" . $toDate . "'
                    ) as data where data.condicion = 1
            ";

        $muestras = DB::select($query);

        return $muestras[0]->total;
    }

    public function arreglo_muestras_para_grafica($fechas_iniciales_mes, $fechas_finales_mes)
    {

        $fechas = array();

        foreach ($fechas_iniciales_mes as $posicion => $fecha) {
            $fechas[] = array(
                'fecha_inicial' => $fecha,
                'fecha_fin' => $fechas_finales_mes[$posicion],
                'data' => []
            );
        }

        $data = [
            'muestrasTerminadas' => [],
            'muestrasTerminadasPorOt' => []
        ];

        foreach ($fechas as $key => $value) {

            $muestras_terminadas_por_fecha = Muestra::where("work_order_id", "!=", "0")
                ->where("estado", "3")
                ->whereBetween('created_at', [$value['fecha_inicial'], $value['fecha_fin']])
                ->get();

            $muestras_terminadas = self::verificar_muestras_fecha_con_dato($muestras_terminadas_por_fecha);
            $muestras_terminadas_por_ot = self::muestras_terminadas_por_ot($value['fecha_inicial'], $value['fecha_fin']);

            $data['muestrasTerminadas'][] = $muestras_terminadas['cantidad'];
            $data['muestrasTerminadasPorOt'][] = $muestras_terminadas_por_ot;
        }

        return $data;
    }

    public function cantidad_muestras_por_ot($muestras)
    {

        $cantidadMuestras = 0;
        $destinatarios = array();

        foreach ($muestras as $muestra) {
            if (!in_array($muestra->destinatarios_id[0], $destinatarios)) {
                $destinatarios[] = $muestra->destinatarios_id[0];
            }

            if ($muestra->destinatarios_id[0] == 1) {
                $cantidadMuestras += $muestra->cantidad_vendedor;
            } elseif ($muestra->destinatarios_id[0] == 2) {
                $cantidadMuestras += $muestra->cantidad_diseñador;
            } elseif ($muestra->destinatarios_id[0] == 3) {
                $cantidadMuestras += $muestra->cantidad_laboratorio;
            } elseif ($muestra->destinatarios_id[0] == 4) {
                $cantidadMuestras += $muestra->cantidad_1;
            } elseif ($muestra->destinatarios_id[0] == 5) {
                $cantidadMuestras += $muestra->cantidad_diseñador_revision;
            }
        }
        return array(
            'cantidad' => $cantidadMuestras,
            'columnas_destinatarios' => self::traduce_destinatario_id_a_campo($destinatarios),
        );
    }

    public function reportDisenoEstructuralySalaMuestra()
    {
        // ------------------- FILTRO DE BUSQUEDA
        //año:
        $year_ini = date('Y') + 0;
        $year_fin = 2020;
        $years = [];
        for ($i = $year_ini; $i >= $year_fin; $i--) {
            $years[] = $i;
        }

        //---- Se busca los meses anteriores al seleccionado en la vista para realizar la busqueda
        $nombreMeses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        // Carbon::setLocale('es');
        // // Filtro por fechas
        if (!is_null(request()->input('mes')) and !is_null(request()->input('year'))) {
            $mes = request()->input('mes');
            $year = request()->input('year')[0];
        } else {
            $mes = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
        }

        $fromDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->startOfMonth();
        $toDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->endOfMonth();
        $mesSeleccionado = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->format('Y-m');
        $meses = [];
        $nombreMesesSeleccionados = [];
        $solicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];


        for ($i = 4; $i >= 0; $i--) {
            $meses[] = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('Y-m');
            $nombreMesesSeleccionados[] = $nombreMeses[Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('m') - 1];
        }

        // vendedores o creadores:
        $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4, 19])->get(); ///Se dejan solo los activos para uso del filtro
        $vendedores->map(function ($vendedor) {
            $vendedor->vendedor_id = $vendedor->id;
        });


        if (!is_null(request()->query('vendedor_id'))) {
            $vendedor_id = request()->query('vendedor_id');
        } else {
            $vendedor_id = array();
            foreach ($vendedores as $vendedor) {
                $vendedor_id[] = $vendedor->vendedor_id;
            }
        }

        // Área
        $areas = WorkSpace::where('status', '=', 'active')->where('id', '!=', 1)->get();
        $areas->map(function ($area) {
            $area->area_id = $area->id;
        });

        if (!is_null(request()->query('area_id'))) {
            $area_id = request()->query('area_id');
        } else {
            $area_id = array();
            foreach ($areas as $area) {
                $area_id[] = $area->area_id;
            }
        }
        // ------------------- FIN FILTRO DE BUSQUEDA

        //Se Actualizan los registros de las gestiones con cambios de mes
        $array_ot_diseño_estructural = self::actualiza_registros_managements($fromDate, $toDate, 2, 'Actualiza Registros Área de Diseño Estructural');
        $array_ot_diseño_grafico    = self::actualiza_registros_managements($fromDate, $toDate, 3, 'Actualiza Registros Área de Diseño Gráfico');
        $array_ot_sala_muestra      = self::actualiza_registros_managements($fromDate, $toDate, 6, 'Actualiza Registros Área de Muestras');
        $array_ot_pre_catalogacion  = self::actualiza_registros_managements($fromDate, $toDate, 5, 'Actualiza Registros Área de Precatalogación');
        $array_ot_catalogacion      = self::actualiza_registros_managements($fromDate, $toDate, 4, 'Actualiza Registros Área de Catalogación');

        $ot_activa_area_actual_diseño_estructural   = self::actualiza_registros_ot_activa_area_actual(2, 'Actualiza Registros Ot activas en mes actual Área de Diseño Estructural');
        $ot_activa_area_actual_diseño_grafico       = self::actualiza_registros_ot_activa_area_actual(3, 'Actualiza Registros Ot activas en mes actual Área de Diseño Gráfico');
        $ot_activa_area_actual_sala_muestra         = self::actualiza_registros_ot_activa_area_actual(6, 'Actualiza Registros Ot activas en mes actual Área de Muestras');
        $ot_activa_area_actual_pre_catalogacion     = self::actualiza_registros_ot_activa_area_actual(5, 'Actualiza Registros Ot activas en mes actual Área de Precatalogación');
        $ot_activa_area_actual_catalogacion         = self::actualiza_registros_ot_activa_area_actual(4, 'Actualiza Registros Ot activas en mes actual Área de Catalogación');

        //Promedios de tiempos de OT mes actual ( GRAFICA -> Tiempos OT )
        $tiempo_promedio_mes_actual_ot = self::tiempo_promedio_mes_actual_ot($area_id, $vendedor_id, $fromDate, $toDate);


        $promedio_mes_actual_titulo = $tiempo_promedio_mes_actual_ot['titulo']; //.'Gest.+Activas';
        $promedio_mes_actual_desarrollo = $tiempo_promedio_mes_actual_ot['promedio_mes_actual_desarrollo'];
        $promedio_mes_actual_muestra = $tiempo_promedio_mes_actual_ot['promedio_mes_actual_muestra'];
        $promedio_mes_actual_diseno = $tiempo_promedio_mes_actual_ot['promedio_mes_actual_diseno'];
        $promedio_mes_actual_catalogacion = $tiempo_promedio_mes_actual_ot['promedio_mes_actual_catalogacion'];
        $promedio_mes_actual_precatalogacion = $tiempo_promedio_mes_actual_ot['promedio_mes_actual_precatalogacion'];

        $count_mes_actual_desarrollo = $tiempo_promedio_mes_actual_ot['count_dias_trabajados_desarrollo'];
        $count_mes_actual_muestra = $tiempo_promedio_mes_actual_ot['count_dias_trabajados_muestra'];
        $count_mes_actual_diseno = $tiempo_promedio_mes_actual_ot['count_dias_trabajados_diseno'];
        $count_mes_actual_catalogacion = $tiempo_promedio_mes_actual_ot['count_dias_trabajados_catalogacion'];
        $count_mes_actual_precatalogacion = $tiempo_promedio_mes_actual_ot['count_dias_trabajados_precatalogacion'];

        $suma_mes_actual_desarrollo = $tiempo_promedio_mes_actual_ot['suma_dias_trabajados_desarrollo'];
        $suma_mes_actual_muestra = $tiempo_promedio_mes_actual_ot['suma_dias_trabajados_muestra'];
        $suma_mes_actual_diseno = $tiempo_promedio_mes_actual_ot['suma_dias_trabajados_diseno'];
        $suma_mes_actual_catalogacion = $tiempo_promedio_mes_actual_ot['suma_dias_trabajados_catalogacion'];
        $suma_mes_actual_precatalogacion = $tiempo_promedio_mes_actual_ot['suma_dias_trabajados_precatalogacion'];

        //Promedios de tiempos de OT año actual ( GRAFICA -> Tiempos OT )
        $tiempo_promedio_anio_actual_ot = self::tiempo_promedio_anio_actual_ot($area_id, $vendedor_id, $fromDate, $toDate, $count_mes_actual_desarrollo, $count_mes_actual_muestra, $count_mes_actual_diseno, $count_mes_actual_catalogacion, $count_mes_actual_precatalogacion, $suma_mes_actual_desarrollo, $suma_mes_actual_muestra, $suma_mes_actual_diseno, $suma_mes_actual_catalogacion, $suma_mes_actual_precatalogacion);

        $promedio_anio_actual_titulo = $tiempo_promedio_anio_actual_ot['titulo'];
        $promedio_anio_actual_desarrollo = $tiempo_promedio_anio_actual_ot['promedio_anio_actual_desarrollo'];
        $promedio_anio_actual_muestra = $tiempo_promedio_anio_actual_ot['promedio_anio_actual_muestra'];
        $promedio_anio_actual_diseno = $tiempo_promedio_anio_actual_ot['promedio_anio_actual_diseno'];
        $promedio_anio_actual_catalogacion = $tiempo_promedio_anio_actual_ot['promedio_anio_actual_catalogacion'];
        $promedio_anio_actual_precatalogacion = $tiempo_promedio_anio_actual_ot['promedio_anio_actual_precatalogacion'];


        // //Promedios de tiempos de OT mes anterior al actual ( GRAFICA -> Tiempos OT )
        $tiempo_promedio_mes_anterior_al_actual_ot = self::tiempo_promedio_mes_anterior_al_actual_ot($area_id, $vendedor_id, $fromDate, $toDate);

        $promedio_mes_anterior_al_actual_titulo = $tiempo_promedio_mes_anterior_al_actual_ot['titulo'];
        $promedio_mes_anterior_al_actual_desarrollo = $tiempo_promedio_mes_anterior_al_actual_ot['promedio_mes_anterior_al_actual_desarrollo'];
        $promedio_mes_anterior_al_actual_muestra = $tiempo_promedio_mes_anterior_al_actual_ot['promedio_mes_anterior_al_actual_muestra'];
        $promedio_mes_anterior_al_actual_diseno = $tiempo_promedio_mes_anterior_al_actual_ot['promedio_mes_anterior_al_actual_diseno'];
        $promedio_mes_anterior_al_actual_catalogacion = $tiempo_promedio_mes_anterior_al_actual_ot['promedio_mes_anterior_al_actual_catalogacion'];
        $promedio_mes_anterior_al_actual_precatalogacion = $tiempo_promedio_mes_anterior_al_actual_ot['promedio_mes_anterior_al_actual_precatalogacion'];


        //Cantidad de OT por Area mes Actual ( GRAFICA -> OT que estan en cada area mes actual )
        $cantidad_ot_por_area = self::cantidad_ot_por_area_mes_actual($area_id, $vendedor_id);
        $array_cantidad_ot_por_area = $cantidad_ot_por_area['array_cantidad_ot_por_area'];
        $array_keys_ot_por_area = $cantidad_ot_por_area['array_keys_ot_por_area'];

        //Promedios de tiempos de OT Año anterior ( GRAFICA -> Tiempos OT )

        $tiempo_promedio_anio_anterior_ot = self::tiempo_promedio_anio_anterior_ot($area_id, $vendedor_id, $fromDate, $toDate); //entrada

        $promedio_anio_anterior_titulo = $tiempo_promedio_anio_anterior_ot['titulo'];
        $promedio_anio_anterior_desarrollo = $tiempo_promedio_anio_anterior_ot['promedio_anio_anterior_desarrollo'];
        $promedio_anio_anterior_muestra = $tiempo_promedio_anio_anterior_ot['promedio_anio_anterior_muestra'];
        $promedio_anio_anterior_diseno = $tiempo_promedio_anio_anterior_ot['promedio_anio_anterior_diseno'];
        $promedio_anio_anterior_catalogacion = $tiempo_promedio_anio_anterior_ot['promedio_anio_anterior_catalogacion'];
        $promedio_anio_anterior_precatalogacion = $tiempo_promedio_anio_anterior_ot['promedio_anio_anterior_precatalogacion'];

        // // //Promedios de tiempos de OT mes actual del año anterior ( GRAFICA -> Tiempos OT )
        $tiempo_promedio_mes_actual_anio_anterior_ot = self::tiempo_promedio_mes_actual_anio_anterior_ot($area_id, $vendedor_id, $fromDate, $toDate);

        $promedio_mes_actual_anio_anterior_titulo = $tiempo_promedio_mes_actual_anio_anterior_ot['titulo'];
        $promedio_mes_actual_anio_anterior_desarrollo = $tiempo_promedio_mes_actual_anio_anterior_ot['promedio_mes_actual_anio_anterior_desarrollo'];
        $promedio_mes_actual_anio_anterior_muestra = $tiempo_promedio_mes_actual_anio_anterior_ot['promedio_mes_actual_anio_anterior_muestra'];
        $promedio_mes_actual_anio_anterior_diseno = $tiempo_promedio_mes_actual_anio_anterior_ot['promedio_mes_actual_anio_anterior_diseno'];
        $promedio_mes_actual_anio_anterior_catalogacion = $tiempo_promedio_mes_actual_anio_anterior_ot['promedio_mes_actual_anio_anterior_catalogacion'];
        $promedio_mes_actual_anio_anterior_precatalogacion = $tiempo_promedio_mes_actual_anio_anterior_ot['promedio_mes_actual_anio_anterior_precatalogacion'];



        // dd($promedio_mes_actual_desarrollo,$array_ot_de);



        //Nº OT QUE HAN PASADO POR CADA ÁREA del mes actual ( GRAFICA TRES)
        $cantidad_historico_ot_por_area = self::cantidad_historico_ot_por_area($area_id, $vendedor_id, $fromDate, $toDate);


        $array_cantidad_historico_ot_por_area = $cantidad_historico_ot_por_area['array_cantidad_historico_ot_por_area'];
        $array_keys_historico_ot_por_area = $cantidad_historico_ot_por_area['array_keys_historico_ot_por_area'];

        //Datos del Diseñador estructural
        $disenador_estructural = self::datos_disenador_estructural($mes, $year);


        //Datos del Diseñador grafico
        $disenador_grafico = self::datos_disenador_grafico($mes, $year);


        //OT CON MUESTRAS PENDIENTES DE CORTE (Grafico de números) -> son las OT
        /*$ot_con_muestras_pendientes_corte = self::ot_con_muestras_pendientes_corte($fromDate,$toDate);


        //ID MUESTRAS PENDIENTES DE CORTE (Grafico de números) --> son los registros
        $id_muestras_pendientes_corte = self::id_muestras_pendientes_corte($fromDate,$toDate);


        //MUESTRAS PENDIENTES DE CORTE (Grafico de números) --> son las cantidades

        $muestras_pendientes_corte = self::muestras_pendientes_corte($fromDate,$toDate);



        //Promedio de OT con muestras del año anterior ( ------------primera grafica de la ultima fila )
        /*$promedio_ot_con_muestras_anio_anterior = self::promedio_ot_con_muestras_anio_anterior($fromDate, $toDate);
        $promedio_ot_con_muestras_anio_anterior_titulo = $promedio_ot_con_muestras_anio_anterior['titulo'];
        $promedio_ot_con_muestras_anio_anterior = $promedio_ot_con_muestras_anio_anterior['promedio'];

        //Cantidad de OT con muestras mes y año actual ( ------------primera grafica de la ultima fila )
        $cantidad_ot_con_muestras_mes_anio_actual = self::cantidad_ot_con_muestras_mes_anio_actual($fromDate, $toDate);
        $ot_con_muestras_mes_anio_actual_titulo = $cantidad_ot_con_muestras_mes_anio_actual['titulo'];
        $ot_con_muestras_mes_anio_actual_cantidad = $cantidad_ot_con_muestras_mes_anio_actual['cantidad'];

        //Cantidad de OT con muestras mes actual y año anterior ( ------------primera grafica de la ultima fila )
        $cantidad_ot_con_muestras_mes_actual_anio_anterior = self::cantidad_ot_con_muestras_mes_actual_anio_anterior($fromDate, $toDate);
        $ot_con_muestras_mes_actual_anio_anterior_titulo = $cantidad_ot_con_muestras_mes_actual_anio_anterior['titulo'];
        $ot_con_muestras_mes_actual_anio_anterior_cantidad = $cantidad_ot_con_muestras_mes_actual_anio_anterior['cantidad'];

        //Promedio de OT con muestras del año actual ( ------------primera grafica de la ultima fila )
        $promedio_ot_con_muestras_anio_actual = self::promedio_ot_con_muestras_anio_actual($fromDate, $toDate);
        $promedio_ot_con_muestras_anio_actual_titulo = $promedio_ot_con_muestras_anio_actual['titulo'];
        $promedio_ot_con_muestras_anio_actual = $promedio_ot_con_muestras_anio_actual['promedio'];


        //Promedio de OT con muestras pendientes termino (con fecha corte) del año anterior (---------segunda grafica de la ultima fila )
        $promedio_ot_con_muestras_cortadas_anio_anterior = self::promedio_ot_con_muestras_cortadas_anio_anterior($fromDate, $toDate);
        $promedio_ot_con_muestras_cortadas_anio_anterior_titulo = $promedio_ot_con_muestras_cortadas_anio_anterior['titulo'];
        $promedio_ot_con_muestras_cortadas_anio_anterior = $promedio_ot_con_muestras_cortadas_anio_anterior['promedio'];

        //Cantidad de OT con muestras pendientes termino mes actual y año anterior (---------segunda grafica de la ultima fila )
        $cantidad_ot_con_muestras_cortadas_mes_actual_anio_anterior = self::cantidad_ot_con_muestras_cortadas_mes_actual_anio_anterior($fromDate, $toDate);
        $ot_con_muestras_cortadas_mes_actual_anio_anterior_titulo = $cantidad_ot_con_muestras_cortadas_mes_actual_anio_anterior['titulo'];
        $ot_con_muestras_cortadas_mes_actual_anio_anterior_cantidad = $cantidad_ot_con_muestras_cortadas_mes_actual_anio_anterior['cantidad'];

        //Cantidad de OT con muestras pendientes termino mes y año actual (---------segunda grafica de la ultima fila )
        $cantidad_ot_con_muestras_cortadas_mes_anio_actual = self::cantidad_ot_con_muestras_cortadas_mes_anio_actual($fromDate, $toDate);
        $ot_con_muestras_cortadas_mes_anio_actual_titulo = $cantidad_ot_con_muestras_cortadas_mes_anio_actual['titulo'];
        $ot_con_muestras_cortadas_mes_anio_actual_cantidad = $cantidad_ot_con_muestras_cortadas_mes_anio_actual['cantidad'];

        //Promedio de OT con muestras pendientes termino (con fecha corte) del año actual (---------segunda grafica de la ultima fila )
        $promedio_ot_con_muestras_cortadas_anio_actual = self::promedio_ot_con_muestras_cortadas_anio_actual($fromDate, $toDate);
        $promedio_ot_con_muestras_cortadas_anio_actual_titulo = $promedio_ot_con_muestras_cortadas_anio_actual['titulo'];
        $promedio_ot_con_muestras_cortadas_anio_actual = $promedio_ot_con_muestras_cortadas_anio_actual['promedio'];


        //Promedio de ID con muestras del año anterior (---------tercera grafica de la ultima fila )
        $promedio_id_con_muestras_anio_anterior = self::promedio_id_con_muestras_anio_anterior($fromDate, $toDate);
        $promedio_id_con_muestras_anio_anterior_titulo = $promedio_id_con_muestras_anio_anterior['titulo'];
        $promedio_id_con_muestras_anio_anterior = $promedio_id_con_muestras_anio_anterior['promedio'];

        //Cantidad de ID con muestras mes actual y año anterior (---------tercera grafica de la ultima fila )
        $cantidad_id_con_muestras_mes_actual_anio_anterior = self::cantidad_id_con_muestras_pendientes_mes_actual_anio_anterior($fromDate, $toDate);
        $id_con_muestras_mes_actual_anio_anterior_titulo = $cantidad_id_con_muestras_mes_actual_anio_anterior['titulo'];
        $id_con_muestras_mes_actual_anio_anterior_cantidad = $cantidad_id_con_muestras_mes_actual_anio_anterior['cantidad'];

        //Cantidad de ID con muestras mes y año actual (---------tercera grafica de la ultima fila )
        $cantidad_id_con_muestras_mes_anio_actual = self::cantidad_id_con_muestras_pendientes_mes_anio_actual($fromDate, $toDate);
        $id_con_muestras_mes_anio_actual_titulo = $cantidad_id_con_muestras_mes_anio_actual['titulo'];
        $id_con_muestras_mes_anio_actual_cantidad = $cantidad_id_con_muestras_mes_anio_actual['cantidad'];

        //Promedio de ID con muestras del año actual (---------tercera grafica de la ultima fila )
        $promedio_id_con_muestras_anio_actual = self::promedio_id_con_muestras_anio_actual($fromDate, $toDate);
        $promedio_id_con_muestras_anio_actual_titulo = $promedio_id_con_muestras_anio_actual['titulo'];
        $promedio_id_con_muestras_anio_actual = $promedio_id_con_muestras_anio_actual['promedio'];


        //Promedio de ID con muestras pendientes termino (con fecha corte) del año anterior (---------cuarta grafica de la ultima fila )
        $promedio_id_con_muestras_cortadas_anio_anterior = self::promedio_id_con_muestras_cortadas_anio_anterior($fromDate, $toDate);
        $promedio_id_con_muestras_cortadas_anio_anterior_titulo = $promedio_id_con_muestras_cortadas_anio_anterior['titulo'];
        $promedio_id_con_muestras_cortadas_anio_anterior = $promedio_id_con_muestras_cortadas_anio_anterior['promedio'];

        //Cantidad de ID con muestras pendientes termino (con fecha corte) mes actual y año anterior (---------cuarta grafica de la ultima fila )
        $cantidad_id_con_muestras_cortadas_mes_actual_anio_anterior = self::cantidad_id_con_muestras_cortadas_mes_actual_anio_anterior($fromDate, $toDate);
        $id_con_muestras_cortadas_mes_actual_anio_anterior_titulo = $cantidad_id_con_muestras_cortadas_mes_actual_anio_anterior['titulo'];
        $id_con_muestras_cortadas_mes_actual_anio_anterior_cantidad = $cantidad_id_con_muestras_cortadas_mes_actual_anio_anterior['cantidad'];

        //Cantidad de ID con muestras pendientes termino (con fecha corte) mes y año anterior (---------cuarta grafica de la ultima fila )
        $cantidad_id_con_muestras_cortadas_mes_anio_actual = self::cantidad_id_con_muestras_cortadas_mes_anio_actual($fromDate, $toDate);
        $id_con_muestras_cortadas_mes_anio_actual_titulo = $cantidad_id_con_muestras_cortadas_mes_anio_actual['titulo'];
        $id_con_muestras_cortadas_mes_anio_actual_cantidad = $cantidad_id_con_muestras_cortadas_mes_anio_actual['cantidad'];

        //Promedio de ID con muestras pendientes termino (con fecha corte) del año actual (---------cuarta grafica de la ultima fila )
        $promedio_id_con_muestras_cortadas_anio_actual = self::promedio_id_con_muestras_cortadas_anio_actual($fromDate, $toDate);
        $promedio_id_con_muestras_cortadas_anio_actual_titulo = $promedio_id_con_muestras_cortadas_anio_actual['titulo'];
        $promedio_id_con_muestras_cortadas_anio_actual = $promedio_id_con_muestras_cortadas_anio_actual['promedio'];


        //Promedio de Muestras pendientes del año anterior (---------quinta grafica de la ultima fila )
        $promedio_muestras_anio_anterior = self::promedio_muestras_anio_anterior($fromDate, $toDate);
        $promedio_muestras_anio_anterior_titulo = $promedio_muestras_anio_anterior['titulo'];
        $promedio_muestras_anio_anterior = $promedio_muestras_anio_anterior['promedio'];

        //Cantidad de Muestras pendientes mes actual y año anterior (---------quinta grafica de la ultima fila )
        $cantidad_muestras_mes_actual_anio_anterior = self::cantidad_muestras_mes_actual_anio_anterior($fromDate, $toDate);
        $muestras_mes_actual_anio_anterior_titulo = $cantidad_muestras_mes_actual_anio_anterior['titulo'];
        $muestras_mes_actual_anio_anterior_cantidad = $cantidad_muestras_mes_actual_anio_anterior['cantidad'];

        //Cantidad de Muestras pendientes mes y año actual (---------quinta grafica de la ultima fila )
        $cantidad_muestras_mes_anio_actual = self::cantidad_muestras_mes_anio_actual($fromDate, $toDate);
        $muestras_mes_anio_actual_titulo = $cantidad_muestras_mes_anio_actual['titulo'];
        $muestras_mes_anio_actual_cantidad = $cantidad_muestras_mes_anio_actual['cantidad'];

        //Promedio de Muestras pendientes del año actual (---------quinta grafica de la ultima fila )
        $promedio_muestras_anio_actual = self::promedio_muestras_anio_actual($fromDate, $toDate);
        $promedio_muestras_anio_actual_titulo = $promedio_muestras_anio_actual['titulo'];
        $promedio_muestras_anio_actual = $promedio_muestras_anio_actual['promedio'];


        //Promedio de Muestras pendientes de termino (cortadas) del año anterior (---------sexta grafica de la ultima fila )
        $promedio_muestras_cortadas_anio_anterior = self::promedio_muestras_cortadas_anio_anterior($fromDate, $toDate);
        $promedio_muestras_cortadas_anio_anterior_titulo = $promedio_muestras_cortadas_anio_anterior['titulo'];
        $promedio_muestras_cortadas_anio_anterior = $promedio_muestras_cortadas_anio_anterior['promedio'];

        //Cantidad de Muestras pendientes de termino (cortadas) mes actual y año anterior (---------sexta grafica de la ultima fila )
        $cantidad_muestras_cortadas_mes_actual_anio_anterior = self::cantidad_muestras_cortadas_mes_actual_anio_anterior($fromDate, $toDate);
        $muestras_cortadas_mes_actual_anio_anterior_titulo = $cantidad_muestras_cortadas_mes_actual_anio_anterior['titulo'];
        $muestras_cortadas_mes_actual_anio_anterior_cantidad = $cantidad_muestras_cortadas_mes_actual_anio_anterior['cantidad'];

        //Cantidad de Muestras pendientes de termino (cortadas) mes y año actual (---------sexta grafica de la ultima fila )
        $cantidad_muestras_cortadas_mes_anio_actual = self::cantidad_muestras_cortadas_mes_anio_actual($fromDate, $toDate);
        $muestras_cortadas_mes_anio_actual_titulo = $cantidad_muestras_cortadas_mes_anio_actual['titulo'];
        $muestras_cortadas_mes_anio_actual_cantidad = $cantidad_muestras_cortadas_mes_anio_actual['cantidad'];

        //Promedio de Muestras pendientes de termino (cortadas) del año actual (---------sexta grafica de la ultima fila )
        $promedio_muestras_cortadas_anio_actual = self::promedio_muestras_cortadas_anio_actual($fromDate, $toDate);
        $promedio_muestras_cortadas_anio_actual_titulo = $promedio_muestras_cortadas_anio_actual['titulo'];
        $promedio_muestras_cortadas_anio_actual = $promedio_muestras_cortadas_anio_actual['promedio'];
        */
        return view(
            'reports.reportDisenoEstructuralySalaMuestra',
            compact(
                'mes',
                'years',
                'year',
                'mesSeleccionado',
                'nombreMesesSeleccionados',
                'vendedores',
                'areas',
                'array_cantidad_ot_por_area',
                'array_keys_ot_por_area',
                'promedio_anio_anterior_titulo',
                'promedio_anio_anterior_desarrollo',
                'promedio_anio_anterior_muestra',
                'promedio_anio_anterior_diseno',
                'promedio_anio_anterior_catalogacion',
                'promedio_anio_anterior_precatalogacion',
                'promedio_mes_actual_anio_anterior_titulo',
                'promedio_mes_actual_anio_anterior_desarrollo',
                'promedio_mes_actual_anio_anterior_muestra',
                'promedio_mes_actual_anio_anterior_diseno',
                'promedio_mes_actual_anio_anterior_catalogacion',
                'promedio_mes_actual_anio_anterior_precatalogacion',
                'promedio_mes_anterior_al_actual_titulo',
                'promedio_mes_anterior_al_actual_desarrollo',
                'promedio_mes_anterior_al_actual_muestra',
                'promedio_mes_anterior_al_actual_diseno',
                'promedio_mes_anterior_al_actual_catalogacion',
                'promedio_mes_anterior_al_actual_precatalogacion',
                'promedio_mes_actual_titulo',
                'promedio_mes_actual_desarrollo',
                'promedio_mes_actual_muestra',
                'promedio_mes_actual_diseno',
                'promedio_mes_actual_catalogacion',
                'promedio_mes_actual_precatalogacion',
                'promedio_anio_actual_titulo',
                'promedio_anio_actual_desarrollo',
                'promedio_anio_actual_muestra',
                'promedio_anio_actual_diseno',
                'promedio_anio_actual_catalogacion',
                'promedio_anio_actual_precatalogacion',
                'array_cantidad_historico_ot_por_area',
                'array_keys_historico_ot_por_area',
                'disenador_estructural',
                'disenador_grafico'
                /*'ot_con_muestras_pendientes_corte',
            'id_muestras_pendientes_corte',
            'muestras_pendientes_corte',
            'ot_con_muestras_mes_anio_actual_titulo',
            'ot_con_muestras_mes_anio_actual_cantidad',
            'ot_con_muestras_mes_actual_anio_anterior_titulo',
            'ot_con_muestras_mes_actual_anio_anterior_cantidad',
            'promedio_ot_con_muestras_anio_anterior_titulo',
            'promedio_ot_con_muestras_anio_anterior',
            'promedio_ot_con_muestras_anio_actual_titulo',
            'promedio_ot_con_muestras_anio_actual',
            'promedio_ot_con_muestras_cortadas_anio_anterior_titulo',
            'promedio_ot_con_muestras_cortadas_anio_anterior',
            'ot_con_muestras_cortadas_mes_actual_anio_anterior_titulo',
            'ot_con_muestras_cortadas_mes_actual_anio_anterior_cantidad',
            'ot_con_muestras_cortadas_mes_anio_actual_titulo',
            'ot_con_muestras_cortadas_mes_anio_actual_cantidad',
            'promedio_ot_con_muestras_cortadas_anio_actual_titulo',
            'promedio_ot_con_muestras_cortadas_anio_actual',
            'promedio_id_con_muestras_anio_anterior_titulo',
            'promedio_id_con_muestras_anio_anterior',
            'id_con_muestras_mes_actual_anio_anterior_titulo',
            'id_con_muestras_mes_actual_anio_anterior_cantidad',
            'id_con_muestras_mes_anio_actual_titulo',
            'id_con_muestras_mes_anio_actual_cantidad',
            'promedio_id_con_muestras_anio_actual_titulo',
            'promedio_id_con_muestras_anio_actual',
            'promedio_id_con_muestras_cortadas_anio_anterior_titulo',
            'promedio_id_con_muestras_cortadas_anio_anterior',
            'id_con_muestras_cortadas_mes_actual_anio_anterior_titulo',
            'id_con_muestras_cortadas_mes_actual_anio_anterior_cantidad',
            'id_con_muestras_cortadas_mes_anio_actual_titulo',
            'id_con_muestras_cortadas_mes_anio_actual_cantidad',
            'promedio_id_con_muestras_cortadas_anio_actual_titulo',
            'promedio_id_con_muestras_cortadas_anio_actual',
            'promedio_muestras_anio_anterior_titulo',
            'promedio_muestras_anio_anterior',
            'muestras_mes_actual_anio_anterior_titulo',
            'muestras_mes_actual_anio_anterior_cantidad',
            'muestras_mes_anio_actual_titulo',
            'muestras_mes_anio_actual_cantidad',
            'promedio_muestras_anio_actual_titulo',
            'promedio_muestras_anio_actual',
            'promedio_muestras_cortadas_anio_anterior_titulo',
            'promedio_muestras_cortadas_anio_anterior',
            'muestras_cortadas_mes_actual_anio_anterior_titulo',
            'muestras_cortadas_mes_actual_anio_anterior_cantidad',
            'muestras_cortadas_mes_anio_actual_titulo',
            'muestras_cortadas_mes_anio_actual_cantidad',
            'promedio_muestras_cortadas_anio_actual_titulo',
            'promedio_muestras_cortadas_anio_actual'*/
            )
        );
    }

    public function reportSalaMuestra()
    {
        // ------------------- FILTRO DE BUSQUEDA
        //año:
        $year_ini = date('Y') + 0;
        $year_fin = 2020;
        $years = [];
        for ($i = $year_ini; $i >= $year_fin; $i--) {
            $years[] = $i;
        }

        //---- Se busca los meses anteriores al seleccionado en la vista para realizar la busqueda
        $nombreMeses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        // Carbon::setLocale('es');
        // // Filtro por fechas
        if (!is_null(request()->input('mes')) and !is_null(request()->input('year'))) {
            $mes = request()->input('mes');
            $year = request()->input('year')[0];
            $yearSeleccionado = request()->input('year')[0];
        } else {
            $mes = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
            $yearSeleccionado = Carbon::now()->format('Y');
        }

        $fromDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->startOfMonth();
        $toDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->endOfMonth();
        $mesSeleccionado = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->format('Y-m');
        $meses = [];
        $nombreMesesSeleccionados = [];
        $solicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];

        for ($i = 4; $i >= 0; $i--) {
            $meses[] = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('Y-m');
            $nombreMesesSeleccionados[] = $nombreMeses[Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('m') - 1];
        }

        // Si se solicito exportar
        if (!is_null(request()->input('exportar'))) {

            $nombreMesesCompletos = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            $titulo = 'Muestras_' . $nombreMesesCompletos[(int)$mes - 1] . '_' . $year;

            $this->descargaReporteSalaMuestra($fromDate, $toDate, $titulo);
        }
        // ------------------- FIN FILTRO DE BUSQUEDA
        $ot_en_sala_muestras = WorkOrder::where('current_area_id', 6)->pluck('id')->toArray();

        ////MUESTRAS EN PROCESO
        $muestrasEstadoProceso = Muestra::whereIN("work_order_id", $ot_en_sala_muestras)
            ->where("estado", "1")
            ->where('fecha_corte_vendedor', NULL)
            ->where('fecha_corte_diseñador', NULL)
            ->where('fecha_corte_laboratorio', NULL)
            ->where('fecha_corte_diseñador_revision', NULL)
            ->where('fecha_corte_1', NULL)
            ->where('fecha_corte_2', NULL)
            ->where('fecha_corte_3', NULL)
            ->where('fecha_corte_4', NULL)
            //->whereIn("work_order_id", $ot_id)
            ->get();
        ////
        ////MUESTRAS PENDIENTES DE ENTREGA
        $cantidad_ot = [];
        $cantidad_id = [];
        $cantidad_muestras = 0;
        $muestrasPendienteEntregaVendedor = Muestra::whereIN("work_order_id", $ot_en_sala_muestras)
            ->whereNotIn("estado", [2, 3, 4])
            ->whereNotNull('fecha_corte_vendedor')
            //->whereIn("work_order_id", $ot_id)
            ->get();

        foreach ($muestrasPendienteEntregaVendedor as $muestra) {
            $ultimo_estado_ot = Management::where('work_order_id', $muestra->work_order_id)
                ->where('management_type_id', 1)
                ->orderBy('created_at', 'Desc')
                ->first();
            if (in_array($ultimo_estado_ot->state_id, [1, 2, 3, 4, 5, 6, 7, 10, 15, 16, 17, 19])) {

                if ($muestra->destinatarios_id[0] == 1 && $muestra->check_fecha_corte_vendedor != NULL && $muestra->fecha_corte_vendedor != NULL) {
                    if (!in_array($muestra->work_order_id, $cantidad_ot)) {
                        $cantidad_ot[] = $muestra->work_order_id;
                    }

                    if (!in_array($muestra->id, $cantidad_id)) {
                        $cantidad_id[] = $muestra->id;
                    }

                    $cantidad_muestras += $muestra->cantidad_vendedor;
                }
            }
        }

        $muestrasPendienteEntregaDiseñador = Muestra::whereIN("work_order_id", $ot_en_sala_muestras)
            ->whereNotIn("estado", [2, 3, 4])
            ->whereNotNull('fecha_corte_diseñador')
            //->whereIn("work_order_id", $ot_id)
            ->get();

        foreach ($muestrasPendienteEntregaDiseñador as $muestra) {
            $ultimo_estado_ot = Management::where('work_order_id', $muestra->work_order_id)
                ->where('management_type_id', 1)
                ->orderBy('created_at', 'Desc')
                ->first();
            if (in_array($ultimo_estado_ot->state_id, [1, 2, 3, 4, 5, 6, 7, 10, 15, 16, 17, 19])) {

                if ($muestra->destinatarios_id[0] == 2 && $muestra->check_fecha_corte_diseñador != NULL && $muestra->fecha_corte_diseñador != NULL) {
                    if (!in_array($muestra->work_order_id, $cantidad_ot)) {
                        $cantidad_ot[] = $muestra->work_order_id;
                    }

                    if (!in_array($muestra->id, $cantidad_id)) {
                        $cantidad_id[] = $muestra->id;
                    }

                    $cantidad_muestras += $muestra->cantidad_diseñador;
                }
            }
        }

        $muestrasPendienteEntregaLaboratorio = Muestra::whereIN("work_order_id", $ot_en_sala_muestras)
            ->whereNotIn("estado", [2, 3, 4])
            ->whereNotNull('fecha_corte_laboratorio')
            //->whereIn("work_order_id", $ot_id)
            ->get();

        foreach ($muestrasPendienteEntregaLaboratorio as $muestra) {
            $ultimo_estado_ot = Management::where('work_order_id', $muestra->work_order_id)
                ->where('management_type_id', 1)
                ->orderBy('created_at', 'Desc')
                ->first();
            if (in_array($ultimo_estado_ot->state_id, [1, 2, 3, 4, 5, 6, 7, 10, 15, 16, 17, 19])) {

                if ($muestra->destinatarios_id[0] == 3 && $muestra->check_fecha_corte_laboratorio != NULL && $muestra->fecha_corte_laboratorio != NULL) {
                    if (!in_array($muestra->work_order_id, $cantidad_ot)) {
                        $cantidad_ot[] = $muestra->work_order_id;
                    }

                    if (!in_array($muestra->id, $cantidad_id)) {
                        $cantidad_id[] = $muestra->id;
                    }

                    $cantidad_muestras += $muestra->cantidad_laboratorio;
                }
            }
        }

        $muestrasPendienteEntregaCliente_1  = Muestra::whereIN("work_order_id", $ot_en_sala_muestras)
            ->whereNotIn("estado", [2, 3, 4])
            ->whereNotNull('fecha_corte_1')
            //->whereIn("work_order_id", $ot_id)
            ->get();

        foreach ($muestrasPendienteEntregaCliente_1 as $muestra) {
            $ultimo_estado_ot = Management::where('work_order_id', $muestra->work_order_id)
                ->where('management_type_id', 1)
                ->orderBy('created_at', 'Desc')
                ->first();
            if (in_array($ultimo_estado_ot->state_id, [1, 2, 3, 4, 5, 6, 7, 10, 15, 16, 17, 19])) {

                if ($muestra->destinatarios_id[0] == 4 && $muestra->check_fecha_corte_1 != NULL && $muestra->fecha_corte_1 != NULL) {
                    if (!in_array($muestra->work_order_id, $cantidad_ot)) {
                        $cantidad_ot[] = $muestra->work_order_id;
                    }

                    if (!in_array($muestra->id, $cantidad_id)) {
                        $cantidad_id[] = $muestra->id;
                    }

                    $cantidad_muestras += $muestra->cantidad_1;
                }
            }
        }

        $muestrasPendienteEntregaCliente_2  = Muestra::whereIN("work_order_id", $ot_en_sala_muestras)
            ->whereNotIn("estado", [2, 3, 4])
            ->whereNotNull('fecha_corte_2')
            //->whereIn("work_order_id", $ot_id)
            ->get();

        foreach ($muestrasPendienteEntregaCliente_2 as $muestra) {
            $ultimo_estado_ot = Management::where('work_order_id', $muestra->work_order_id)
                ->where('management_type_id', 1)
                ->orderBy('created_at', 'Desc')
                ->first();
            if (in_array($ultimo_estado_ot->state_id, [1, 2, 3, 4, 5, 6, 7, 10, 15, 16, 17, 19])) {

                if ($muestra->destinatarios_id[0] == 4 && $muestra->check_fecha_corte_2 != NULL && $muestra->fecha_corte_2 != NULL) {
                    if (!in_array($muestra->work_order_id, $cantidad_ot)) {
                        $cantidad_ot[] = $muestra->work_order_id;
                    }

                    if (!in_array($muestra->id, $cantidad_id)) {
                        $cantidad_id[] = $muestra->id;
                    }

                    $cantidad_muestras += $muestra->cantidad_2;
                }
            }
        }

        $muestrasPendienteEntregaCliente_3  = Muestra::whereIN("work_order_id", $ot_en_sala_muestras)
            ->whereNotIn("estado", [2, 3, 4])
            ->whereNotNull('fecha_corte_3')
            //->whereIn("work_order_id", $ot_id)
            ->get();

        foreach ($muestrasPendienteEntregaCliente_3 as $muestra) {
            $ultimo_estado_ot = Management::where('work_order_id', $muestra->work_order_id)
                ->where('management_type_id', 1)
                ->orderBy('created_at', 'Desc')
                ->first();
            if (in_array($ultimo_estado_ot->state_id, [1, 2, 3, 4, 5, 6, 7, 10, 15, 16, 17, 19])) {

                if ($muestra->destinatarios_id[0] == 4 && $muestra->check_fecha_corte_3 != NULL && $muestra->fecha_corte_3 != NULL) {
                    if (!in_array($muestra->work_order_id, $cantidad_ot)) {
                        $cantidad_ot[] = $muestra->work_order_id;
                    }

                    if (!in_array($muestra->id, $cantidad_id)) {
                        $cantidad_id[] = $muestra->id;
                    }

                    $cantidad_muestras += $muestra->cantidad_3;
                }
            }
        }

        $muestrasPendienteEntregaCliente_4  = Muestra::whereIN("work_order_id", $ot_en_sala_muestras)
            ->whereNotIn("estado", [2, 3, 4])
            ->whereNotNull('fecha_corte_4')
            //->whereIn("work_order_id", $ot_id)
            ->get();

        foreach ($muestrasPendienteEntregaCliente_4 as $muestra) {
            $ultimo_estado_ot = Management::where('work_order_id', $muestra->work_order_id)
                ->where('management_type_id', 1)
                ->orderBy('created_at', 'Desc')
                ->first();
            if (in_array($ultimo_estado_ot->state_id, [1, 2, 3, 4, 5, 6, 7, 10, 15, 16, 17, 19])) {

                if ($muestra->destinatarios_id[0] == 4 && $muestra->check_fecha_corte_4 != NULL && $muestra->fecha_corte_4 != NULL) {
                    if (!in_array($muestra->work_order_id, $cantidad_ot)) {
                        $cantidad_ot[] = $muestra->work_order_id;
                    }

                    if (!in_array($muestra->id, $cantidad_id)) {
                        $cantidad_id[] = $muestra->id;
                    }

                    $cantidad_muestras += $muestra->cantidad_4;
                }
            }
        }

        $ot_con_muestras_pendientes_entrega =  count(array_unique($cantidad_ot));

        $id_muestras_pendientes_entrega =   count(array_unique($cantidad_id));

        $muestras_pendientes_entrega =  $cantidad_muestras;

        ////
        //dd($ot_con_muestras_pendientes_entrega,$id_muestras_pendientes_entrega,$muestras_pendientes_entrega );

        /*
        $ot_muestras_listas=Management::where('management_type_id',1)
        ->where('state_id',18)
        ->whereBetween('created_at',[$fromDate, $toDate])
        ->pluck('work_order_id')
        ->toArray();*/
        ////MUESTRAS OSORNO
        $cantidad_muestras_osorno_cortadas = 0;
        $cantidad_muestras_osorno = 0;
        $id_ot_osorno = array();
        $cantidad_ot_osorno = 0;

        ///Muestras Vendedor
        $muestrasOsornoVendedor = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_vendedor", 1)
            ->get();

        if (count($muestrasOsornoVendedor) > 0) {
            foreach ($muestrasOsornoVendedor as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_osorno += $muestra->cantidad_vendedor;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_vendedor)) {
                    $cantidad_muestras_osorno_cortadas += $muestra->cantidad_vendedor;
                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_osorno[] = $muestra->work_order_id;
                        $cantidad_ot_osorno++;
                    }
                }
            }
        }
        ///

        ///Muestras Diseñador
        $muestrasOsornoDiseñador = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_diseñador", 1)
            ->get();

        if (count($muestrasOsornoDiseñador) > 0) {
            foreach ($muestrasOsornoDiseñador as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_osorno += $muestra->cantidad_diseñador;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_diseñador)) {
                    $cantidad_muestras_osorno_cortadas += $muestra->cantidad_diseñador;
                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_osorno[] = $muestra->work_order_id;
                        $cantidad_ot_osorno++;
                    }
                }
            }
        }
        ///

        ///Muestras Diseñador Revision
        $muestrasOsornoDiseñadorRevision = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_diseñador_revision", 1)
            ->get();

        if (count($muestrasOsornoDiseñadorRevision) > 0) {
            foreach ($muestrasOsornoDiseñadorRevision as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_osorno += $muestra->cantidad_diseñador_revision;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_diseñador_revision)) {
                    $cantidad_muestras_osorno_cortadas += $muestra->cantidad_diseñador_revision;
                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_osorno[] = $muestra->work_order_id;
                        $cantidad_ot_osorno++;
                    }
                }
            }
        }
        ///

        ///Muestras Laboratorio
        $muestrasOsornolaboratorio = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_laboratorio", 1)
            ->get();

        if (count($muestrasOsornolaboratorio) > 0) {
            foreach ($muestrasOsornolaboratorio as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_osorno += $muestra->cantidad_laboratorio;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_laboratorio)) {
                    $cantidad_muestras_osorno_cortadas += $muestra->cantidad_laboratorio;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_osorno[] = $muestra->work_order_id;
                        $cantidad_ot_osorno++;
                    }
                }
            }
        }
        ///

        ///Muestras Clientes1
        $muestrasOsornoCliente1 = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_1", 1)
            ->get();

        if (count($muestrasOsornoCliente1) > 0) {
            foreach ($muestrasOsornoCliente1 as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_osorno += $muestra->cantidad_1;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_1)) {
                    $cantidad_muestras_osorno_cortadas += $muestra->cantidad_1;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_osorno[] = $muestra->work_order_id;
                        $cantidad_ot_osorno++;
                    }
                }
            }
        }
        ///

        ///Muestras Clientes2
        $muestrasOsornoCliente2 = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_2", 1)
            ->get();

        if (count($muestrasOsornoCliente2) > 0) {
            foreach ($muestrasOsornoCliente2 as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_osorno += $muestra->cantidad_2;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_2)) {
                    $cantidad_muestras_osorno_cortadas += $muestra->cantidad_2;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_osorno[] = $muestra->work_order_id;
                        $cantidad_ot_osorno++;
                    }
                }
            }
        }
        ///

        ///Muestras Clientes3
        $muestrasOsornoCliente3 = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_3", 1)
            ->get();

        if (count($muestrasOsornoCliente3) > 0) {
            foreach ($muestrasOsornoCliente3 as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_osorno += $muestra->cantidad_3;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_3)) {
                    $cantidad_muestras_osorno_cortadas += $muestra->cantidad_3;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_osorno[] = $muestra->work_order_id;
                        $cantidad_ot_osorno++;
                    }
                }
            }
        }
        ///

        ///Muestras Clientes4
        $muestrasOsornoCliente4 = Muestra::where("work_order_id", "!=", "0")
            // ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_4", 1)
            ->get();

        if (count($muestrasOsornoCliente4) > 0) {
            foreach ($muestrasOsornoCliente4 as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_osorno += $muestra->cantidad_4;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_4)) {
                    $cantidad_muestras_osorno_cortadas += $muestra->cantidad_4;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_osorno[] = $muestra->work_order_id;
                        $cantidad_ot_osorno++;
                    }
                }
            }
        }
        ///
        ////

        //// MUESTRAS PUENTE ALTO
        $cantidad_muestras_puente_alto = 0;
        $cantidad_muestras_cortadas_puente_alto = 0;
        $id_ot_puente_alto = array();
        $cantidad_ot_puente_alto = 0;

        ///Muestras Vendedor
        $muestrasPuenteAltoVendedor = Muestra::where("work_order_id", "!=", "0")
            // ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_vendedor", 2)
            ->get();

        if (count($muestrasPuenteAltoVendedor) > 0) {
            foreach ($muestrasPuenteAltoVendedor as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_puente_alto += $muestra->cantidad_vendedor;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_vendedor)) {
                    $cantidad_muestras_cortadas_puente_alto += $muestra->cantidad_vendedor;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_puente_alto[] = $muestra->work_order_id;
                        $cantidad_ot_puente_alto++;
                    }
                }
            }
        }
        ///

        ///Muestras Diseñador
        $muestrasPuenteAltoDiseñador = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_diseñador", 2)
            ->get();

        if (count($muestrasPuenteAltoDiseñador) > 0) {
            foreach ($muestrasPuenteAltoDiseñador as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_puente_alto += $muestra->cantidad_diseñador;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_diseñador)) {
                    $cantidad_muestras_cortadas_puente_alto += $muestra->cantidad_diseñador;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_puente_alto[] = $muestra->work_order_id;
                        $cantidad_ot_puente_alto++;
                    }
                }
            }
        }
        ///

        ///Muestras Diseñador Revision
        $muestrasPuenteAltoDiseñadorRevision = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_diseñador_revision", 2)
            ->get();

        if (count($muestrasPuenteAltoDiseñadorRevision) > 0) {
            foreach ($muestrasPuenteAltoDiseñadorRevision as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_puente_alto += $muestra->cantidad_diseñador_revision;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_diseñador_revision)) {
                    $cantidad_muestras_cortadas_puente_alto += $muestra->cantidad_diseñador_revision;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_puente_alto[] = $muestra->work_order_id;
                        $cantidad_ot_puente_alto++;
                    }
                }
            }
        }
        ///

        ///Muestras Laboratorio
        $muestrasPuenteAltolaboratorio = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_laboratorio", 2)
            ->get();

        if (count($muestrasPuenteAltolaboratorio) > 0) {
            foreach ($muestrasPuenteAltolaboratorio as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_puente_alto += $muestra->cantidad_laboratorio;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_laboratorio)) {
                    $cantidad_muestras_cortadas_puente_alto += $muestra->cantidad_laboratorio;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_puente_alto[] = $muestra->work_order_id;
                        $cantidad_ot_puente_alto++;
                    }
                }
            }
        }
        ///

        ///Muestras Cliente1
        $muestrasPuenteAltoCliente1 = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_1", 2)
            ->get();

        if (count($muestrasPuenteAltoCliente1) > 0) {
            foreach ($muestrasPuenteAltoCliente1 as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_puente_alto += $muestra->cantidad_1;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_1)) {
                    $cantidad_muestras_cortadas_puente_alto += $muestra->cantidad_1;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_puente_alto[] = $muestra->work_order_id;
                        $cantidad_ot_puente_alto++;
                    }
                }
            }
        }
        ///

        ///Muestras Cliente2
        $muestrasPuenteAltoCliente2 = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_2", 2)
            ->get();

        if (count($muestrasPuenteAltoCliente2) > 0) {
            foreach ($muestrasPuenteAltoCliente2 as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_puente_alto += $muestra->cantidad_2;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_2)) {
                    $cantidad_muestras_cortadas_puente_alto += $muestra->cantidad_2;
                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_puente_alto) && ! in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_puente_alto[] = $muestra->work_order_id;
                        $cantidad_ot_puente_alto++;
                    }
                }
            }
        }
        ///

        ///Muestras Cliente3
        $muestrasPuenteAltoCliente3 = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_3", 2)
            ->get();

        if (count($muestrasPuenteAltoCliente3) > 0) {
            foreach ($muestrasPuenteAltoCliente3 as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_puente_alto += $muestra->cantidad_3;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_3)) {
                    $cantidad_muestras_cortadas_puente_alto += $muestra->cantidad_3;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_puente_alto[] = $muestra->work_order_id;
                        $cantidad_ot_puente_alto++;
                    }
                }
            }
        }
        ///

        ///Muestras Cliente4
        $muestrasPuenteAltoCliente4 = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_4", 2)
            ->get();

        if (count($muestrasPuenteAltoCliente4) > 0) {
            foreach ($muestrasPuenteAltoCliente4 as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_puente_alto += $muestra->cantidad_4;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_4)) {
                    $cantidad_muestras_cortadas_puente_alto += $muestra->cantidad_4;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_puente_alto[] = $muestra->work_order_id;
                        $cantidad_ot_puente_alto++;
                    }
                }
            }
        }
        ///
        ////

        //// MUESTRAS OTRO
        $cantidad_muestras_otro = 0;
        $cantidad_muestras_cortadas_otro = 0;
        $id_ot_otro = array();
        $cantidad_ot_otro = 0;

        ///Muestras Vendedor
        $muestrasOtroVendedor = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_vendedor", 3)
            ->get();

        if (count($muestrasOtroVendedor) > 0) {
            foreach ($muestrasOtroVendedor as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_otro += $muestra->cantidad_vendedor;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_vendedor)) {
                    $cantidad_muestras_cortadas_otro += $muestra->cantidad_vendedor;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_otro) && !in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_otro[] = $muestra->work_order_id;
                        $cantidad_ot_otro++;
                    }
                }
            }
        }
        ///

        ///Muestras Diseñador
        $muestrasOtroDiseñador = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_diseñador", 3)
            ->get();

        if (count($muestrasOtroDiseñador) > 0) {
            foreach ($muestrasOtroDiseñador as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_otro += $muestra->cantidad_diseñador;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_diseñador)) {
                    $cantidad_muestras_cortadas_otro += $muestra->cantidad_diseñador;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_otro) && !in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_otro[] = $muestra->work_order_id;
                        $cantidad_ot_otro++;
                    }
                }
            }
        }
        ///

        ///Muestras Diseñador Revision
        $muestrasOtroDiseñadorRevision = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_diseñador_revision", 3)
            ->get();

        if (count($muestrasOtroDiseñadorRevision) > 0) {
            foreach ($muestrasOtroDiseñadorRevision as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_otro += $muestra->cantidad_diseñador_revision;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_diseñador_revision)) {
                    $cantidad_muestras_cortadas_otro += $muestra->cantidad_diseñador_revision;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_otro) && !in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_otro[] = $muestra->work_order_id;
                        $cantidad_ot_otro++;
                    }
                }
            }
        }
        ///

        ///Muestras Laboratorio
        $muestrasOtrolaboratorio = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_laboratorio", 3)
            ->get();

        if (count($muestrasOtrolaboratorio) > 0) {
            foreach ($muestrasOtrolaboratorio as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_otro += $muestra->cantidad_laboratorio;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_laboratorio)) {
                    $cantidad_muestras_cortadas_otro += $muestra->cantidad_laboratorio;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_otro) && !in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_otro[] = $muestra->work_order_id;
                        $cantidad_ot_otro++;
                    }
                }
            }
        }
        ///

        ///Muestras Cliente1
        $muestrasOtroCliente1 = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_1", 3)
            ->get();

        if (count($muestrasOtroCliente1) > 0) {
            foreach ($muestrasOtroCliente1 as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_otro += $muestra->cantidad_1;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_1)) {
                    $cantidad_muestras_cortadas_otro += $muestra->cantidad_1;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_otro) && !in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_otro[] = $muestra->work_order_id;
                        $cantidad_ot_otro++;
                    }
                }
            }
        }
        ///

        ///Muestras Cliente2
        $muestrasOtroCliente2 = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_2", 3)
            ->get();

        if (count($muestrasOtroCliente2) > 0) {
            foreach ($muestrasOtroCliente2 as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_otro += $muestra->cantidad_2;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_2)) {
                    $cantidad_muestras_cortadas_otro += $muestra->cantidad_2;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_otro) && !in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_otro[] = $muestra->work_order_id;
                        $cantidad_ot_otro++;
                    }
                }
            }
        }
        ///

        ///Muestras Cliente3
        $muestrasOtroCliente3 = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_3", 3)
            ->get();

        if (count($muestrasOtroCliente3) > 0) {
            foreach ($muestrasOtroCliente3 as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_otro += $muestra->cantidad_3;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_3)) {
                    $cantidad_muestras_cortadas_otro += $muestra->cantidad_3;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_otro) && !in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_otro[] = $muestra->work_order_id;
                        $cantidad_ot_otro++;
                    }
                }
            }
        }
        ///

        ///Muestras Cliente4
        $muestrasOtroCliente4 = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->where("sala_corte_4", 3)
            ->get();

        if (count($muestrasOtroCliente4) > 0) {
            foreach ($muestrasOtroCliente4 as $muestra) {
                //Muestras Solicitadas
                $cantidad_muestras_otro += $muestra->cantidad_4;
                //Muestras Cortadas
                if (!is_null($muestra->fecha_corte_4)) {
                    $cantidad_muestras_cortadas_otro += $muestra->cantidad_4;

                    //Cantidad de OT
                    if (!in_array($muestra->work_order_id, $id_ot_otro) && !in_array($muestra->work_order_id, $id_ot_puente_alto) && !in_array($muestra->work_order_id, $id_ot_osorno)) {
                        $id_ot_otro[] = $muestra->work_order_id;
                        $cantidad_ot_otro++;
                    }
                }
            }
        }
        ///
        ////


        //// OT CON MUESTRAS PENDIENTES DE CORTE (Grafico de números) -> son las OT
        //$ot_con_muestras_pendientes_corte = self::ot_con_muestras_pendientes_corte($fromDate,$toDate);
        $ot_con_muestras_pendientes_corte = self::cantidad_ot_fecha_NULL($muestrasEstadoProceso);
        ////

        //// ID MUESTRAS PENDIENTES DE CORTE (Grafico de números) --> son los registros
        //$id_muestras_pendientes_corte = self::id_muestras_pendientes_corte($fromDate,$toDate);
        $id_muestras_pendientes_corte = self::cantidad_id_fecha_NULL($muestrasEstadoProceso);
        ////

        //// MUESTRAS PENDIENTES DE CORTE (Grafico de números) --> son las cantidades
        //$muestras_pendientes_corte = self::muestras_pendientes_corte($fromDate,$toDate);
        $muestras_pendientes_corte = self::cantidad_muestras_fecha_NULL($muestrasEstadoProceso);
        ////

        //// GRAFICO OT CON MUESTRAS CORTADAS ////
        //Promedio de OT con muestras pendientes termino (con fecha corte) del año anterior (---------segunda grafica de la ultima fila )
        $promedio_ot_con_muestras_cortadas_anio_anterior = self::promedio_ot_con_muestras_cortadas_anio_anterior($fromDate, $toDate);
        $promedio_ot_con_muestras_cortadas_anio_anterior_titulo = $promedio_ot_con_muestras_cortadas_anio_anterior['titulo'];
        $promedio_ot_con_muestras_cortadas_anio_anterior = $promedio_ot_con_muestras_cortadas_anio_anterior['promedio'];
        //

        //Cantidad de OT con muestras pendientes termino mes actual y año anterior (---------segunda grafica de la ultima fila )
        $cantidad_ot_con_muestras_cortadas_mes_actual_anio_anterior = self::cantidad_ot_con_muestras_cortadas_mes_actual_anio_anterior($fromDate, $toDate);
        $ot_con_muestras_cortadas_mes_actual_anio_anterior_titulo = $cantidad_ot_con_muestras_cortadas_mes_actual_anio_anterior['titulo'];
        $ot_con_muestras_cortadas_mes_actual_anio_anterior_cantidad = $cantidad_ot_con_muestras_cortadas_mes_actual_anio_anterior['cantidad'];
        //

        //Cantidad de OT con muestras pendientes termino mes y año actual (---------segunda grafica de la ultima fila )
        $cantidad_ot_con_muestras_cortadas_mes_anio_actual = self::cantidad_ot_con_muestras_cortadas_mes_anio_actual($fromDate, $toDate);
        $ot_con_muestras_cortadas_mes_anio_actual_titulo = $cantidad_ot_con_muestras_cortadas_mes_anio_actual['titulo'];
        $ot_con_muestras_cortadas_mes_anio_actual_cantidad = $cantidad_ot_con_muestras_cortadas_mes_anio_actual['cantidad'];
        //

        //Promedio de OT con muestras pendientes termino (con fecha corte) del año actual (---------segunda grafica de la ultima fila )
        $promedio_ot_con_muestras_cortadas_anio_actual = self::promedio_ot_con_muestras_cortadas_anio_actual($fromDate, $toDate);
        $promedio_ot_con_muestras_cortadas_anio_actual_titulo = $promedio_ot_con_muestras_cortadas_anio_actual['titulo'];
        $promedio_ot_con_muestras_cortadas_anio_actual = $promedio_ot_con_muestras_cortadas_anio_actual['promedio'];
        //
        ////

        //// GRAFICO ID CORTADAS ////

        //Promedio de ID con muestras pendientes termino (con fecha corte) del año anterior (---------cuarta grafica de la ultima fila )
        $promedio_id_con_muestras_cortadas_anio_anterior = self::promedio_id_con_muestras_cortadas_anio_anterior($fromDate, $toDate);
        $promedio_id_con_muestras_cortadas_anio_anterior_titulo = $promedio_id_con_muestras_cortadas_anio_anterior['titulo'];
        $promedio_id_con_muestras_cortadas_anio_anterior = $promedio_id_con_muestras_cortadas_anio_anterior['promedio'];
        //

        //Cantidad de ID con muestras pendientes termino (con fecha corte) mes actual y año anterior (---------cuarta grafica de la ultima fila )
        $cantidad_id_con_muestras_cortadas_mes_actual_anio_anterior = self::cantidad_id_con_muestras_cortadas_mes_actual_anio_anterior($fromDate, $toDate);
        $id_con_muestras_cortadas_mes_actual_anio_anterior_titulo = $cantidad_id_con_muestras_cortadas_mes_actual_anio_anterior['titulo'];
        $id_con_muestras_cortadas_mes_actual_anio_anterior_cantidad = $cantidad_id_con_muestras_cortadas_mes_actual_anio_anterior['cantidad'];
        //

        //Cantidad de ID con muestras pendientes termino (con fecha corte) mes y año anterior (---------cuarta grafica de la ultima fila )
        $cantidad_id_con_muestras_cortadas_mes_anio_actual = self::cantidad_id_con_muestras_cortadas_mes_anio_actual($fromDate, $toDate);
        $id_con_muestras_cortadas_mes_anio_actual_titulo = $cantidad_id_con_muestras_cortadas_mes_anio_actual['titulo'];
        $id_con_muestras_cortadas_mes_anio_actual_cantidad = $cantidad_id_con_muestras_cortadas_mes_anio_actual['cantidad'];
        //

        //Promedio de ID con muestras pendientes termino (con fecha corte) del año actual (---------cuarta grafica de la ultima fila )
        $promedio_id_con_muestras_cortadas_anio_actual = self::promedio_id_con_muestras_cortadas_anio_actual($fromDate, $toDate);
        $promedio_id_con_muestras_cortadas_anio_actual_titulo = $promedio_id_con_muestras_cortadas_anio_actual['titulo'];
        $promedio_id_con_muestras_cortadas_anio_actual = $promedio_id_con_muestras_cortadas_anio_actual['promedio'];
        //
        ////

        //// GRAFICO MUESTRAS CORTADAS ////
        //Promedio de Muestras pendientes de termino (cortadas) del año anterior (---------sexta grafica de la ultima fila )
        $promedio_muestras_cortadas_anio_anterior = self::promedio_muestras_cortadas_anio_anterior($fromDate, $toDate);
        $promedio_muestras_cortadas_anio_anterior_titulo = $promedio_muestras_cortadas_anio_anterior['titulo'];
        $promedio_muestras_cortadas_anio_anterior = $promedio_muestras_cortadas_anio_anterior['promedio'];
        //

        //Cantidad de Muestras pendientes de termino (cortadas) mes actual y año anterior (---------sexta grafica de la ultima fila )
        $cantidad_muestras_cortadas_mes_actual_anio_anterior = self::cantidad_muestras_cortadas_mes_actual_anio_anterior($fromDate, $toDate);
        $muestras_cortadas_mes_actual_anio_anterior_titulo = $cantidad_muestras_cortadas_mes_actual_anio_anterior['titulo'];
        $muestras_cortadas_mes_actual_anio_anterior_cantidad = $cantidad_muestras_cortadas_mes_actual_anio_anterior['cantidad'];
        //

        //Cantidad de Muestras pendientes de termino (cortadas) mes y año actual (---------sexta grafica de la ultima fila )
        $cantidad_muestras_cortadas_mes_anio_actual = self::cantidad_muestras_cortadas_mes_anio_actual($fromDate, $toDate);
        $muestras_cortadas_mes_anio_actual_titulo = $cantidad_muestras_cortadas_mes_anio_actual['titulo'];
        $muestras_cortadas_mes_anio_actual_cantidad = $cantidad_muestras_cortadas_mes_anio_actual['cantidad'];

        //

        //Promedio de Muestras pendientes de termino (cortadas) del año actual (---------sexta grafica de la ultima fila )
        $promedio_muestras_cortadas_anio_actual = self::promedio_muestras_cortadas_anio_actual($fromDate, $toDate);
        $promedio_muestras_cortadas_anio_actual_titulo = $promedio_muestras_cortadas_anio_actual['titulo'];
        $promedio_muestras_cortadas_anio_actual = $promedio_muestras_cortadas_anio_actual['promedio'];
        //
        ////

        return view(
            'reports.reportSalaMuestra',
            compact(
                'mes',
                'years',
                'mesSeleccionado',
                'yearSeleccionado',
                'nombreMesesSeleccionados',
                'ot_con_muestras_pendientes_corte',
                'id_muestras_pendientes_corte',
                'muestras_pendientes_corte',
                'ot_con_muestras_pendientes_entrega',
                'id_muestras_pendientes_entrega',
                'muestras_pendientes_entrega',
                'promedio_ot_con_muestras_cortadas_anio_anterior_titulo',
                'promedio_ot_con_muestras_cortadas_anio_anterior',
                'ot_con_muestras_cortadas_mes_actual_anio_anterior_titulo',
                'ot_con_muestras_cortadas_mes_actual_anio_anterior_cantidad',
                'ot_con_muestras_cortadas_mes_anio_actual_titulo',
                'ot_con_muestras_cortadas_mes_anio_actual_cantidad',
                'promedio_ot_con_muestras_cortadas_anio_actual_titulo',
                'promedio_ot_con_muestras_cortadas_anio_actual',
                'promedio_id_con_muestras_cortadas_anio_anterior_titulo',
                'promedio_id_con_muestras_cortadas_anio_anterior',
                'id_con_muestras_cortadas_mes_actual_anio_anterior_titulo',
                'id_con_muestras_cortadas_mes_actual_anio_anterior_cantidad',
                'id_con_muestras_cortadas_mes_anio_actual_titulo',
                'id_con_muestras_cortadas_mes_anio_actual_cantidad',
                'promedio_id_con_muestras_cortadas_anio_actual_titulo',
                'promedio_id_con_muestras_cortadas_anio_actual',
                'promedio_muestras_cortadas_anio_anterior_titulo',
                'promedio_muestras_cortadas_anio_anterior',
                'muestras_cortadas_mes_actual_anio_anterior_titulo',
                'muestras_cortadas_mes_actual_anio_anterior_cantidad',
                'muestras_cortadas_mes_anio_actual_titulo',
                'muestras_cortadas_mes_anio_actual_cantidad',
                'promedio_muestras_cortadas_anio_actual_titulo',
                'promedio_muestras_cortadas_anio_actual',
                'cantidad_muestras_puente_alto',
                'cantidad_muestras_osorno',
                'cantidad_muestras_cortadas_puente_alto',
                'cantidad_muestras_osorno_cortadas',
                'cantidad_ot_osorno',
                'cantidad_ot_puente_alto',
                'cantidad_muestras_otro',
                'cantidad_muestras_cortadas_otro',
                'cantidad_ot_otro'
            )
        );
    }

    public function reportTiempoPrimeraMuestra()
    {
        // ------------------- FILTRO DE BUSQUEDA
        //año:
        $year_ini = date('Y') + 0;
        $year_fin = 2020;
        $years = [];
        for ($i = $year_ini; $i >= $year_fin; $i--) {
            $years[] = $i;
        }

        //---- Se busca los meses anteriores al seleccionado en la vista para realizar la busqueda
        $nombreMeses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        // Carbon::setLocale('es');
        // // Filtro por fechas
        if (!is_null(request()->input('mes')) and !is_null(request()->input('year'))) {
            $mes = request()->input('mes');
            $year = request()->input('year')[0];
            $yearSeleccionado = request()->input('year')[0];
        } else {
            $mes = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
            $yearSeleccionado = Carbon::now()->format('Y');
        }
        //$hoy=Carbon::now()->format('Y-m-d');
        $fromDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->startOfMonth();
        $toDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->endOfMonth();
        $mesSeleccionado = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->format('Y-m');
        //dd($mes,$yearSeleccionado);
        $meses = [];
        $nombreMesesSeleccionados = [];
        $solicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];

        for ($i = 4; $i >= 0; $i--) {
            $meses[] = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('Y-m');
            $nombreMesesSeleccionados[] = $nombreMeses[Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('m') - 1];
        }

        // Si se solicito exportar
        if (!is_null(request()->input('exportar'))) {

            $nombreMesesCompletos = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            $titulo = 'TiempoPrimeraMuestra_' . $nombreMesesCompletos[(int)$mes - 1] . '_' . $year;

            $this->descargaReporteTiempoPrimeraMuestra($fromDate, $toDate, $titulo);
        }

        $prom_tiempo_creacion_ene = 0;
        $prom_tiempo_de_ene = 0;
        $prom_tiempo_creacion_feb = 0;
        $prom_tiempo_de_feb = 0;
        $prom_tiempo_creacion_mar = 0;
        $prom_tiempo_de_mar = 0;
        $prom_tiempo_creacion_abr = 0;
        $prom_tiempo_de_abr = 0;
        $prom_tiempo_creacion_may = 0;
        $prom_tiempo_de_may = 0;
        $prom_tiempo_creacion_jun = 0;
        $prom_tiempo_de_jun = 0;
        $prom_tiempo_creacion_jul = 0;
        $prom_tiempo_de_jul = 0;
        $prom_tiempo_creacion_ago = 0;
        $prom_tiempo_de_ago = 0;
        $prom_tiempo_creacion_sep = 0;
        $prom_tiempo_de_sep = 0;
        $prom_tiempo_creacion_oct = 0;
        $prom_tiempo_de_oct = 0;
        $prom_tiempo_creacion_nov = 0;
        $prom_tiempo_de_nov = 0;
        $prom_tiempo_creacion_dic = 0;
        $prom_tiempo_de_dic = 0;
        $prom_tiempo_creacion_ano = 0;
        $prom_tiempo_DE_ano = 0;
        $prom_tiempo_creacion = 0;
        $prom_tiempo_DE = 0;


        for ($i = 1; $i <= $mes; $i++) {

            if ($i < 10) {
                $fromDateAux = Carbon::createFromFormat('Y-m-d', $year . '-0' . $i . '-' . '1')->startOfMonth();
                $toDateAux = Carbon::createFromFormat('Y-m-d', $year . '-0' . $i . '-' . '1')->endOfMonth();
            } else {
                $fromDateAux = Carbon::createFromFormat('Y-m-d', $year . '-' . $i . '-' . '1')->startOfMonth();
                $toDateAux = Carbon::createFromFormat('Y-m-d', $year . '-' . $i . '-' . '1')->endOfMonth();
            }

            $ots_periodo_muestras_listas = Muestra::select('work_order_id')
                ->where('estado', 3)
                ->whereIN('destinatarios_id', ['["1"]', '["2"]', '["4"]'])
                ->whereBetween('ultimo_cambio_estado', [$fromDateAux, $toDateAux])
                ->groupBy("work_order_id")
                ->orderBy("work_order_id", "Asc")
                ->get();

            $cantidad_ot = 0; // Cantidad total de OTS
            $sum_tiempo_creacion_mes = 0; // Suma Total Tiempos de Creacion por Mes
            $sum_tiempo_DE_mes = 0; // Suma Total Tiempos de DE por Mes
            $indicador_result = array(); // Arreglo con datos detalle de las Ot a mostrar
            $sum_tiempo_creacion = 0; //
            $sum_tiempo_DE = 0;

            foreach ($ots_periodo_muestras_listas as $ot_muestra) {

                /* $ots_estado_muestra_lista=Management::where("work_order_id",$ot_muestra->work_order_id)
                                                    ->where('state_id',18)
                                                    ->orderBy("created_at","Asc")
                                                    ->first();*/
                $fecha_termino_primera_muestra = Muestra::where("work_order_id", $ot_muestra->work_order_id)
                    ->where('estado', 3)
                    ->whereIN('destinatarios_id', ['["1"]', '["2"]', '["4"]'])
                    //->whereBetween('created_at', [$fromDateAux, $toDateAux])
                    ->orderBy("ultimo_cambio_estado", "Asc")
                    ->first();


                if ($fecha_termino_primera_muestra && $fromDateAux <= $fecha_termino_primera_muestra->ultimo_cambio_estado && $fecha_termino_primera_muestra->ultimo_cambio_estado <= $toDateAux) {

                    //Verifica Fecha de termino de muestra envio vendedor o cliente


                    //  if($fecha_termino_primera_muestra){

                    //Fecha Creacion OT
                    $fecha_creacion_ot = Management::where("work_order_id", $ot_muestra->work_order_id)
                        ->where("management_type_id", 1)
                        ->where("state_id", 1)
                        ->orderBy("created_at", "Asc")
                        ->first();

                    $tiempo_desde_creación = get_working_hours_muestra($fecha_creacion_ot->created_at, $fecha_termino_primera_muestra->ultimo_cambio_estado) / 11.5;
                    $sum_tiempo_creacion_mes += $tiempo_desde_creación;

                    //Fecha Entra en Area de Diseño Estructurtal
                    $fecha_ingreso_de_ot = Management::where("work_order_id", $ot_muestra->work_order_id)
                        ->where("management_type_id", 1)
                        ->where("state_id", 2)
                        ->orderBy("created_at", "Asc")
                        ->first();

                    $tiempo_desde_DE = get_working_hours_muestra($fecha_ingreso_de_ot->created_at, $fecha_termino_primera_muestra->ultimo_cambio_estado) / 11.5;
                    $sum_tiempo_DE_mes += $tiempo_desde_DE;


                    if ($fromDate == $fromDateAux && $toDate == $toDateAux) {

                        $date_muestra = date_create($fecha_termino_primera_muestra->ultimo_cambio_estado);
                        $fecha_muestra = date_format($date_muestra, 'd/m/Y H:i:s');

                        $date_creacion = date_create($fecha_creacion_ot->created_at);
                        $fecha_creacion = date_format($date_creacion, 'd/m/Y H:i:s');

                        $date_de = date_create($fecha_ingreso_de_ot->created_at);
                        $fecha_de = date_format($date_de, 'd/m/Y H:i:s');
                        $sum_tiempo_creacion += $tiempo_desde_creación;
                        $sum_tiempo_DE += $tiempo_desde_DE;
                        $indicador_result[$cantidad_ot] = $ot_muestra->work_order_id . '*' . $fecha_creacion . '*' . $fecha_de . '*' . $fecha_muestra . '*' . $tiempo_desde_creación . '*' . $tiempo_desde_DE;
                    }
                    $cantidad_ot++;
                    // }
                }
            }

            if ($cantidad_ot > 0) {
                $prom_tiempo_creacion_ano += $sum_tiempo_creacion_mes / $cantidad_ot;
                $prom_tiempo_DE_ano += $sum_tiempo_DE_mes / $cantidad_ot;
            } else {
                $prom_tiempo_creacion_ano += $sum_tiempo_creacion_mes / 1;
                $prom_tiempo_DE_ano += $sum_tiempo_DE_mes / 1;
            }

            switch ($i) {
                case 1:
                    if ($cantidad_ot > 0) {
                        $prom_tiempo_creacion_ene = round($sum_tiempo_creacion_mes / $cantidad_ot, 2);
                        $prom_tiempo_de_ene = round($sum_tiempo_DE_mes / $cantidad_ot, 2);
                    } else {
                        $prom_tiempo_creacion_ene = round($sum_tiempo_creacion_mes / 1, 2);
                        $prom_tiempo_de_ene = round($sum_tiempo_DE_mes / 1, 2);
                    }
                    break;
                case 2:
                    if ($cantidad_ot > 0) {
                        $prom_tiempo_creacion_feb = round($sum_tiempo_creacion_mes / $cantidad_ot, 2);
                        $prom_tiempo_de_feb = round($sum_tiempo_DE_mes / $cantidad_ot, 2);
                    } else {
                        $prom_tiempo_creacion_feb = round($sum_tiempo_creacion_mes / 1, 2);
                        $prom_tiempo_de_feb = round($sum_tiempo_DE_mes / 1, 2);
                    }
                    break;
                case 3:
                    if ($cantidad_ot > 0) {
                        $prom_tiempo_creacion_mar = round($sum_tiempo_creacion_mes / $cantidad_ot, 2);
                        $prom_tiempo_de_mar = round($sum_tiempo_DE_mes / $cantidad_ot, 2);
                    } else {
                        $prom_tiempo_creacion_mar = round($sum_tiempo_creacion_mes / 1, 2);
                        $prom_tiempo_de_mar = round($sum_tiempo_DE_mes / 1, 2);
                    }
                    break;
                case 4:
                    if ($cantidad_ot > 0) {
                        $prom_tiempo_creacion_abr = round($sum_tiempo_creacion_mes / $cantidad_ot, 2);
                        $prom_tiempo_de_abr = round($sum_tiempo_DE_mes / $cantidad_ot, 2);
                    } else {
                        $prom_tiempo_creacion_abr = round($sum_tiempo_creacion_mes / 1, 2);
                        $prom_tiempo_de_abr = round($sum_tiempo_DE_mes / 1, 2);
                    }
                    break;
                case 5:
                    if ($cantidad_ot > 0) {
                        $prom_tiempo_creacion_may = round($sum_tiempo_creacion_mes / $cantidad_ot, 2);
                        $prom_tiempo_de_may = round($sum_tiempo_DE_mes / $cantidad_ot, 2);
                    } else {
                        $prom_tiempo_creacion_may = round($sum_tiempo_creacion_mes / 1, 2);
                        $prom_tiempo_de_may = round($sum_tiempo_DE_mes / 1, 2);
                    }
                    break;
                case 6:
                    if ($cantidad_ot > 0) {
                        $prom_tiempo_creacion_jun = round($sum_tiempo_creacion_mes / $cantidad_ot, 2);
                        $prom_tiempo_de_jun = round($sum_tiempo_DE_mes / $cantidad_ot, 2);
                    } else {
                        $prom_tiempo_creacion_jun = round($sum_tiempo_creacion_mes / 1, 2);
                        $prom_tiempo_de_jun = round($sum_tiempo_DE_mes / 1, 2);
                    }

                    break;
                case 7:
                    if ($cantidad_ot > 0) {
                        $prom_tiempo_creacion_jul = round($sum_tiempo_creacion_mes / $cantidad_ot, 2);
                        $prom_tiempo_de_jul = round($sum_tiempo_DE_mes / $cantidad_ot, 2);
                    } else {
                        $prom_tiempo_creacion_jul = round($sum_tiempo_creacion_mes / 1, 2);
                        $prom_tiempo_de_jul = round($sum_tiempo_DE_mes / 1, 2);
                    }

                    break;
                case 8:
                    if ($cantidad_ot > 0) {
                        $prom_tiempo_creacion_ago = round($sum_tiempo_creacion_mes / $cantidad_ot, 2);
                        $prom_tiempo_de_ago = round($sum_tiempo_DE_mes / $cantidad_ot, 2);
                    } else {
                        $prom_tiempo_creacion_ago = round($sum_tiempo_creacion_mes / 1, 2);
                        $prom_tiempo_de_ago = round($sum_tiempo_DE_mes / 1, 2);
                    }
                    break;
                case 9:
                    if ($cantidad_ot > 0) {
                        $prom_tiempo_creacion_sep = round($sum_tiempo_creacion_mes / $cantidad_ot, 2);
                        $prom_tiempo_de_sep = round($sum_tiempo_DE_mes / $cantidad_ot, 2);
                    } else {
                        $prom_tiempo_creacion_sep = round($sum_tiempo_creacion_mes / 1, 2);
                        $prom_tiempo_de_sep = round($sum_tiempo_DE_mes / 1, 2);
                    }
                    break;
                case 10:
                    if ($cantidad_ot > 0) {
                        $prom_tiempo_creacion_oct = round($sum_tiempo_creacion_mes / $cantidad_ot, 2);
                        $prom_tiempo_de_oct = round($sum_tiempo_DE_mes / $cantidad_ot, 2);
                    } else {
                        $prom_tiempo_creacion_oct = round($sum_tiempo_creacion_mes / 1, 2);
                        $prom_tiempo_de_oct = round($sum_tiempo_DE_mes / 1, 2);
                    }
                    break;
                case 11:
                    if ($cantidad_ot > 0) {
                        $prom_tiempo_creacion_nov = round($sum_tiempo_creacion_mes / $cantidad_ot, 2);
                        $prom_tiempo_de_nov = round($sum_tiempo_DE_mes / $cantidad_ot, 2);
                    } else {
                        $prom_tiempo_creacion_nov = round($sum_tiempo_creacion_mes / 1, 2);
                        $prom_tiempo_de_nov = round($sum_tiempo_DE_mes / 1, 2);
                    }
                    break;
                default:
                    if ($cantidad_ot > 0) {
                        $prom_tiempo_creacion_dic = round($sum_tiempo_creacion_mes / $cantidad_ot, 2);
                        $prom_tiempo_de_dic = round($sum_tiempo_DE_mes / $cantidad_ot, 2);
                    } else {
                        $prom_tiempo_creacion_dic = round($sum_tiempo_creacion_mes / 1, 2);
                        $prom_tiempo_de_dic = round($sum_tiempo_DE_mes / 1, 2);
                    }
                    break;
            }
        }
        //dd($sum_tiempo_creacion,$sum_tiempo_DE,$cantidad_ot,$prom_tiempo_creacion_ano/$mes,);
        if ($cantidad_ot > 0) {
            $prom_tiempo_creacion = str_replace(".", ",", round($sum_tiempo_creacion / $cantidad_ot, 2));
            $prom_tiempo_creacion_grafico = round($sum_tiempo_creacion / $cantidad_ot, 2);
            $prom_tiempo_DE = str_replace(".", ",", round($sum_tiempo_DE / $cantidad_ot, 2));
            $prom_tiempo_DE_grafico = round($sum_tiempo_DE / $cantidad_ot, 2);

            $prom_tiempo_creacion_ano = $prom_tiempo_creacion_ano;
            $prom_tiempo_DE_ano = $prom_tiempo_DE_ano;
            // $prom_tiempo_creacion_ano_grafico=round($prom_tiempo_creacion_ano/$cantidad_ot, 2);
            $prom_tiempo_creacion_ano_grafico = round($prom_tiempo_creacion_ano / $mes, 2);
            // $prom_tiempo_DE_ano_grafico=round($prom_tiempo_DE_ano/$cantidad_ot, 2);
            $prom_tiempo_DE_ano_grafico = round($prom_tiempo_DE_ano / $mes, 2);
            //dd($prom_tiempo_creacion_ano_grafico,$prom_tiempo_DE_ano_grafico);
        } else {
            $prom_tiempo_creacion = str_replace(".", ",", round($sum_tiempo_creacion / 1, 2));
            $prom_tiempo_creacion_grafico = round($sum_tiempo_creacion / 1, 2);
            $prom_tiempo_DE = str_replace(".", ",", round($sum_tiempo_DE / 1, 2));
            $prom_tiempo_DE_grafico = round($sum_tiempo_DE / 1, 2);
            $prom_tiempo_creacion_ano += $sum_tiempo_creacion / 1;
            $prom_tiempo_DE_ano += $sum_tiempo_DE / 1;
            $prom_tiempo_creacion_ano_grafico = round($prom_tiempo_creacion_ano / $mes, 2);
            $prom_tiempo_DE_ano_grafico = round($prom_tiempo_DE_ano / $mes, 2);
        }

        return view(
            'reports.reportTiempoPrimeraMuestra',
            compact(
                'mes',
                'years',
                'mesSeleccionado',
                'yearSeleccionado',
                'nombreMesesSeleccionados',
                'indicador_result',
                'prom_tiempo_creacion',
                'prom_tiempo_DE',
                'cantidad_ot',
                'prom_tiempo_creacion_grafico',
                'prom_tiempo_DE_grafico',
                'prom_tiempo_creacion_ene',
                'prom_tiempo_de_ene',
                'prom_tiempo_creacion_feb',
                'prom_tiempo_de_feb',
                'prom_tiempo_creacion_mar',
                'prom_tiempo_de_mar',
                'prom_tiempo_creacion_abr',
                'prom_tiempo_de_abr',
                'prom_tiempo_creacion_may',
                'prom_tiempo_de_may',
                'prom_tiempo_creacion_jun',
                'prom_tiempo_de_jun',
                'prom_tiempo_creacion_jul',
                'prom_tiempo_de_jul',
                'prom_tiempo_creacion_ago',
                'prom_tiempo_de_ago',
                'prom_tiempo_creacion_sep',
                'prom_tiempo_de_sep',
                'prom_tiempo_creacion_oct',
                'prom_tiempo_de_oct',
                'prom_tiempo_creacion_nov',
                'prom_tiempo_de_nov',
                'prom_tiempo_creacion_dic',
                'prom_tiempo_de_dic',
                'prom_tiempo_creacion_ano_grafico',
                'prom_tiempo_DE_ano_grafico'
            )
        );
    }

    public function reportTiempoDisenadorExterno()
    {
        // ------------------- FILTRO DE BUSQUEDA
        //año:
        $year_ini = date('Y') + 0;
        $year_fin = 2020;
        $years = [];
        for ($i = $year_ini; $i >= $year_fin; $i--) {
            $years[] = $i;
        }

        //---- Se busca los meses anteriores al seleccionado en la vista para realizar la busqueda
        $nombreMeses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        // Carbon::setLocale('es');
        // // Filtro por fechas
        if (!is_null(request()->input('mes')) and !is_null(request()->input('year'))) {
            $mes = request()->input('mes');
            $year = request()->input('year')[0];
            $yearSeleccionado = request()->input('year')[0];
        } else {
            $mes = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
            $yearSeleccionado = Carbon::now()->format('Y');
        }
        //$hoy=Carbon::now()->format('Y-m-d');
        $fromDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->startOfMonth();
        $toDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->endOfMonth();
        $mesSeleccionado = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->format('Y-m');
        //dd($mes,$yearSeleccionado);
        $meses = [];
        $nombreMesesSeleccionados = [];
        $solicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];

        for ($i = 4; $i >= 0; $i--) {
            $meses[] = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('Y-m');
            $nombreMesesSeleccionados[] = $nombreMeses[Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('m') - 1];
        }

        // Si se solicito exportar
        $cantidad_envio_prinflex = 0;
        $cantidad_envio_graphicbox = 0;
        $cantidad_envio_flexoclean = 0;
        $cantidad_envio_artfactory = 0;

        $cantidad_enviadas_prinflex = 0;
        $cantidad_enviadas_graphicbox = 0;
        $cantidad_enviadas_flexoclean = 0;
        $cantidad_enviadas_artfactory = 0;

        $cantidad_pendiente_prinflex = 0;
        $cantidad_pendiente_graphicbox = 0;
        $cantidad_pendiente_flexoclean = 0;
        $cantidad_pendiente_artfactory = 0;

        $tiempo_duracion_prinflex = 0;
        $tiempo_duracion_graphicbox = 0;
        $tiempo_duracion_flexoclean = 0;
        $tiempo_duracion_artfactory = 0;

        $prom_tiempo_duracion_prinflex = 0;
        $prom_tiempo_duracion_graphicbox = 0;
        $prom_tiempo_duracion_flexoclean = 0;
        $prom_tiempo_duracion_artfactory = 0;

        $cantidad_recepcion_prinflex = 0;
        $cantidad_recepcion_graphicbox = 0;
        $cantidad_recepcion_flexoclean = 0;
        $cantidad_recepcion_artfactory = 0;

        $array_ot_procesadas = array();

        $now = Carbon::now();
        if ($now >= $toDate) {
            $fecha_final = $toDate;
        } else {
            $fecha_final = $now;
        }

        $cantidad_ot = 0;

        $indicador_result = array();

        $ot_array = array();

        $result_array[] = array(
            'Ot',
            'Diseñador CMPC',
            'Fecha Ingreso Diseño Grafico',
            'Diseñador Externo',
            'Fecha Envio Diseñador Externo',
            'Fecha Entrega Diseñador Externo',
            'Fecha que pasa Diseño a precatalogar',
            'Tiempo respuesta Diseñador Externo',
            'Tiempo respuesta Diseñador Grafico'

        );

        $ots_proveedor_periodo = Management::select('work_order_id')
            ->where('state_id', 6)
            ->where('management_type_id', 1)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->groupBy('work_order_id')
            ->orderBy('work_order_id', 'Asc')
            ->get();

        foreach ($ots_proveedor_periodo as $ot) {

            $ingreso_precatalogacion = Management::where('work_order_id', $ot->work_order_id)
                ->where('management_type_id', 1)
                ->where('state_id', 6)
                ->orderBy('created_at', 'Asc')
                ->first();
            if ($ingreso_precatalogacion->created_at >= $fromDate && $ingreso_precatalogacion->created_at <= $toDate) {
                $ot_array[] = $ot->work_order_id;
            }
        }

        $ots_proveedor = Management::where('management_type_id', 9)
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->whereIN('work_order_id', $ot_array)
            ->orderBy('work_order_id', 'Asc')
            ->get();

        foreach ($ots_proveedor as $ot) {
            if (!in_array($ot->work_order_id, $array_ot_procesadas)) {

                $fecha_envio_diseñador_externo      = '';
                $fecha_entrega_diseñador_externo    = '';
                $fecha_ingreso_diseno_grafico       = '';
                $fecha_ingreso_precatalogacion      = '';
                $tiempo_respuesta_diseño_externo    = 0;
                $tiempo_respuesta_diseño_grafico    = 0;

                $id_diseñador_cmpc = UserWorkOrder::where('work_order_id', $ot->work_order_id)
                    ->where('area_id', 3)
                    ->first();

                $diseñador_cmpc = User::where('id', $id_diseñador_cmpc->user_id)->first();

                $ingreso_diseño_grafico = Management::where('work_order_id', $ot->work_order_id)
                    ->where('management_type_id', 1)
                    ->where('state_id', 5)
                    ->orderBy('created_at', 'Asc')
                    ->first();
                if ($ingreso_diseño_grafico) {

                    $fecha_ingreso_diseno_grafico   = date_format(date_create($ingreso_diseño_grafico->created_at), 'd/m/Y H:i:s');
                    $tiempo_diseno_grafico = Management::select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                        ->where('work_order_id', $ot->work_order_id)
                        ->where('management_type_id', 1)
                        ->where('work_space_id', 3)
                        // ->where('mostrar', 1)
                        ->get();

                    $tiempo_respuesta_diseño_grafico = round((($tiempo_diseno_grafico[0]->tiempo_total / 3600)) / 9.5, 1);
                }

                $proveedor = Proveedor::where('deleted', 0)->where('id', $ot->proveedor_id)->first();


                $ingreso_precatalogacion = Management::where('work_order_id', $ot->work_order_id)
                    ->where('management_type_id', 1)
                    ->where('state_id', 6)
                    ->orderBy('created_at', 'Asc')
                    ->first();

                if ($ingreso_precatalogacion) {

                    if ($ingreso_diseño_grafico) {

                        //$tiempo_respuesta_diseño_grafico = round((get_working_hours($ingreso_diseño_grafico->created_at, $ingreso_precatalogacion->created_at)/ 9.5),2);

                        $fecha_ingreso_precatalogacion  = date_format(date_create($ingreso_precatalogacion->created_at), 'd/m/Y H:i:s');
                    }
                }

                $fecha_envio_diseñador_externo   = date_format(date_create($ot->created_at), 'd/m/Y H:i:s');

                if ($ot->proveedor_id == 1) { //Proveedor Prinflex

                    if ($ot->recibido_diseño_externo == 0) {

                        // $cantidad_pendiente_prinflex++;
                        $tiempo_respuesta_diseño_externo = get_working_hours($ot->created_at, $fecha_final) / 9.5;
                        $tiempo_duracion_prinflex += $tiempo_respuesta_diseño_externo;
                    } else {
                        $recepcion_proveedor_prinflex = Management::where('proveedor_id', 1)
                            ->where('management_type_id', 10)
                            ->where('gestion_id', $ot->id)
                            ->first();

                        $tiempo_respuesta_diseño_externo = get_working_hours($ot->created_at, $recepcion_proveedor_prinflex->created_at) / 9.5;

                        $tiempo_duracion_prinflex += $tiempo_respuesta_diseño_externo;

                        $fecha_entrega_diseñador_externo = date_format(date_create($recepcion_proveedor_prinflex->created_at), 'd/m/Y H:i:s');
                    }

                    $indicador_result[$cantidad_ot] = $ot->work_order_id . '*' .
                        $diseñador_cmpc->nombre . ' ' . $diseñador_cmpc->apellido . '*' .
                        $fecha_ingreso_diseno_grafico . '*' .
                        $proveedor->name . '*' .
                        $fecha_envio_diseñador_externo . '*' .
                        $fecha_entrega_diseñador_externo . '*' .
                        $fecha_ingreso_precatalogacion . '*' .
                        $tiempo_respuesta_diseño_externo . '*' .
                        $tiempo_respuesta_diseño_grafico;

                    $cantidad_envio_prinflex++;

                    $result_array[] = array(
                        'Ot'                                    => $ot->work_order_id,
                        'Diseñador CMPC'                        => $diseñador_cmpc->nombre . ' ' . $diseñador_cmpc->apellido,
                        'Fecha Ingreso Diseño Grafico'          => ($fecha_ingreso_diseno_grafico) ? $fecha_ingreso_diseno_grafico : '',
                        'Diseñador Externo'                     => $proveedor->name,
                        'Fecha Envio Diseñador Externo'         => $fecha_envio_diseñador_externo,
                        'Fecha Entrega Diseñador Externo'       => $fecha_entrega_diseñador_externo,
                        'Fecha que pasa Diseño a precatalogar'  => ($fecha_ingreso_precatalogacion) ? $fecha_ingreso_precatalogacion : '',
                        'Tiempo respuesta Diseñador Externo'    => round($tiempo_respuesta_diseño_externo, 2),
                        'Tiempo respuesta Diseñador Grafico'    => $tiempo_respuesta_diseño_grafico
                    );
                } elseif ($ot->proveedor_id == 2) { //Proveedor Graphicbox

                    if ($ot->recibido_diseño_externo == 0) {
                        //  $cantidad_pendiente_graphicbox++;
                        $tiempo_respuesta_diseño_externo = get_working_hours($ot->created_at, $fecha_final) / 9.5;
                        $tiempo_duracion_graphicbox += $tiempo_respuesta_diseño_externo;
                        //$tiempo_duracion_graphicbox += get_working_hours($ot->created_at, $fecha_final)/ 9.5;

                    } else {
                        $recepcion_proveedor_graphicbox = Management::where('proveedor_id', 2)
                            ->where('management_type_id', 10)
                            ->where('gestion_id', $ot->id)
                            ->first();
                        $tiempo_respuesta_diseño_externo  = get_working_hours($ot->created_at, $recepcion_proveedor_graphicbox->created_at) / 9.5;

                        $tiempo_duracion_graphicbox += $tiempo_respuesta_diseño_externo;

                        $fecha_entrega_diseñador_externo = date_format(date_create($recepcion_proveedor_graphicbox->created_at), 'd/m/Y H:i:s');
                    }

                    $indicador_result[$cantidad_ot] = $ot->work_order_id . '*' .
                        $diseñador_cmpc->nombre . ' ' . $diseñador_cmpc->apellido . '*' .
                        $fecha_ingreso_diseno_grafico . '*' .
                        $proveedor->name . '*' .
                        $fecha_envio_diseñador_externo . '*' .
                        $fecha_entrega_diseñador_externo . '*' .
                        $fecha_ingreso_precatalogacion . '*' .
                        $tiempo_respuesta_diseño_externo . '*' .
                        $tiempo_respuesta_diseño_grafico;

                    $cantidad_envio_graphicbox++;

                    $result_array[] = array(
                        'Ot'                                    => $ot->work_order_id,
                        'Diseñador CMPC'                        => $diseñador_cmpc->nombre . ' ' . $diseñador_cmpc->apellido,
                        'Fecha Ingreso Diseño Grafico'          => ($fecha_ingreso_diseno_grafico) ? $fecha_ingreso_diseno_grafico : '',
                        'Diseñador Externo'                     => $proveedor->name,
                        'Fecha Envio Diseñador Externo'         => $fecha_envio_diseñador_externo,
                        'Fecha Entrega Diseñador Externo'       => $fecha_entrega_diseñador_externo,
                        'Fecha que pasa Diseño a precatalogar'  => ($fecha_ingreso_precatalogacion) ? $fecha_ingreso_precatalogacion : '',
                        'Tiempo respuesta Diseñador Externo'    => round($tiempo_respuesta_diseño_externo, 2),
                        'Tiempo respuesta Diseñador Grafico'    => $tiempo_respuesta_diseño_grafico
                    );
                } elseif ($ot->proveedor_id == 3) { //Proveedor Flexoclean

                    if ($ot->recibido_diseño_externo == 0) {
                        //   $cantidad_pendiente_flexoclean++;
                        $tiempo_respuesta_diseño_externo = get_working_hours($ot->created_at, $fecha_final) / 9.5;
                        $tiempo_duracion_flexoclean += $tiempo_respuesta_diseño_externo;
                        //$tiempo_duracion_flexoclean += get_working_hours($ot->created_at, $fecha_final)/ 9.5;

                    } else {
                        $recepcion_proveedor_flexoclean = Management::where('proveedor_id', 3)
                            ->where('management_type_id', 10)
                            ->where('gestion_id', $ot->id)
                            ->first();

                        $tiempo_respuesta_diseño_externo = get_working_hours($ot->created_at, $recepcion_proveedor_flexoclean->created_at) / 9.5;

                        $tiempo_duracion_flexoclean += $tiempo_respuesta_diseño_externo;

                        $fecha_entrega_diseñador_externo = date_format(date_create($recepcion_proveedor_flexoclean->created_at), 'd/m/Y H:i:s');
                    }

                    $indicador_result[$cantidad_ot] = $ot->work_order_id . '*' .
                        $diseñador_cmpc->nombre . ' ' . $diseñador_cmpc->apellido . '*' .
                        $fecha_ingreso_diseno_grafico . '*' .
                        $proveedor->name . '*' .
                        $fecha_envio_diseñador_externo . '*' .
                        $fecha_entrega_diseñador_externo . '*' .
                        $fecha_ingreso_precatalogacion . '*' .
                        $tiempo_respuesta_diseño_externo . '*' .
                        $tiempo_respuesta_diseño_grafico;

                    $cantidad_envio_flexoclean++;

                    $result_array[] = array(
                        'Ot'                                    => $ot->work_order_id,
                        'Diseñador CMPC'                        => $diseñador_cmpc->nombre . ' ' . $diseñador_cmpc->apellido,
                        'Fecha Ingreso Diseño Grafico'          => ($fecha_ingreso_diseno_grafico) ? $fecha_ingreso_diseno_grafico : '',
                        'Diseñador Externo'                     => $proveedor->name,
                        'Fecha Envio Diseñador Externo'         => $fecha_envio_diseñador_externo,
                        'Fecha Entrega Diseñador Externo'       => $fecha_entrega_diseñador_externo,
                        'Fecha que pasa Diseño a precatalogar'  => ($fecha_ingreso_precatalogacion) ? $fecha_ingreso_precatalogacion : '',
                        'Tiempo respuesta Diseñador Externo'    => round($tiempo_respuesta_diseño_externo, 2),
                        'Tiempo respuesta Diseñador Grafico'    => $tiempo_respuesta_diseño_grafico
                    );
                } elseif ($ot->proveedor_id == 4) { //Proveedor Artfactory

                    if ($ot->recibido_diseño_externo == 0) {
                        //  $cantidad_pendiente_artfactory++;
                        $tiempo_respuesta_diseño_externo = get_working_hours($ot->created_at, $fecha_final) / 9.5;
                        $tiempo_duracion_artfactory += $tiempo_respuesta_diseño_externo;
                        //$tiempo_duracion_artfactory += get_working_hours($ot->created_at, $fecha_final)/ 9.5;

                    } else {
                        $recepcion_proveedor_artfactory = Management::where('proveedor_id', 4)
                            ->where('management_type_id', 10)
                            ->where('gestion_id', $ot->id)
                            ->first();

                        $tiempo_respuesta_diseño_externo = get_working_hours($ot->created_at, $recepcion_proveedor_artfactory->created_at) / 9.5;

                        $tiempo_duracion_artfactory += $tiempo_respuesta_diseño_externo;

                        $fecha_entrega_diseñador_externo = date_format(date_create($recepcion_proveedor_artfactory->created_at), 'd/m/Y H:i:s');
                    }

                    $indicador_result[$cantidad_ot] = $ot->work_order_id . '*' .
                        $diseñador_cmpc->nombre . ' ' . $diseñador_cmpc->apellido . '*' .
                        $fecha_ingreso_diseno_grafico . '*' .
                        $proveedor->name . '*' .
                        $fecha_envio_diseñador_externo . '*' .
                        $fecha_entrega_diseñador_externo . '*' .
                        $fecha_ingreso_precatalogacion . '*' .
                        $tiempo_respuesta_diseño_externo . '*' .
                        $tiempo_respuesta_diseño_grafico;

                    $cantidad_envio_artfactory++;

                    $result_array[] = array(
                        'Ot'                                    => $ot->work_order_id,
                        'Diseñador CMPC'                        => $diseñador_cmpc->nombre . ' ' . $diseñador_cmpc->apellido,
                        'Fecha Ingreso Diseño Grafico'          => ($fecha_ingreso_diseno_grafico) ? $fecha_ingreso_diseno_grafico : '',
                        'Diseñador Externo'                     => $proveedor->name,
                        'Fecha Envio Diseñador Externo'         => $fecha_envio_diseñador_externo,
                        'Fecha Entrega Diseñador Externo'       => $fecha_entrega_diseñador_externo,
                        'Fecha que pasa Diseño a precatalogar'  => ($fecha_ingreso_precatalogacion) ? $fecha_ingreso_precatalogacion : '',
                        'Tiempo respuesta Diseñador Externo'    => round($tiempo_respuesta_diseño_externo, 2),
                        'Tiempo respuesta Diseñador Grafico'    => $tiempo_respuesta_diseño_grafico
                    );
                }
                $cantidad_ot++;

                $array_ot_procesadas[] = $ot->work_order_id;
            }
        }

        if (!is_null(request()->input('exportar'))) {

            $nombreMesesCompletos = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            $titulo = 'TiempoDiseñadorExterno_' . $nombreMesesCompletos[(int)$mes - 1] . '_' . $year;

            Excel::create($titulo, function ($excel) use ($result_array, $titulo) {
                $excel->setTitle($titulo);
                $excel->sheet($titulo, function ($sheet) use ($result_array) {
                    $sheet->fromArray($result_array, null, 'A1', true, false);
                });
            })->download('xlsx');

            //$this->descargaReporteTiempoDisenoGrafico($fromDate, $toDate, $titulo);
        }

        //dd($array_ot_procesadas);

        //Promedio de duracion para cada proveedor
        $prom_tiempo_duracion_prinflex      = round($tiempo_duracion_prinflex / (($cantidad_envio_prinflex > 0) ? $cantidad_envio_prinflex : 1), 2);
        $prom_tiempo_duracion_graphicbox    = round($tiempo_duracion_graphicbox / (($cantidad_envio_graphicbox > 0) ? $cantidad_envio_graphicbox : 1), 2);
        $prom_tiempo_duracion_flexoclean    = round($tiempo_duracion_flexoclean / (($cantidad_envio_flexoclean > 0) ? $cantidad_envio_flexoclean : 1), 2);
        $prom_tiempo_duracion_artfactory    = round($tiempo_duracion_artfactory / (($cantidad_envio_artfactory > 0) ? $cantidad_envio_artfactory : 1), 2);
        //dd($tiempo_duracion_prinflex,$cantidad_envio_prinflex,$prom_tiempo_duracion_prinflex);
        //Nuevo Ajuste Indicador Evolutivo 24-02

        //Total OT enviadas a Diseñador externo mes en curso
        $ots_enviadas_diseño_periodo = Management::where('management_type_id', 9)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('work_order_id', 'Asc')
            ->get();

        $array_ot_procesadas_enviadas = array();

        foreach ($ots_enviadas_diseño_periodo as $ot_enviada) {
            if (!in_array($ot_enviada->work_order_id, $array_ot_procesadas_enviadas)) {

                if ($ot_enviada->proveedor_id == 1) {
                    $cantidad_enviadas_prinflex++;
                } elseif ($ot_enviada->proveedor_id == 2) {
                    $cantidad_enviadas_graphicbox++;
                } elseif ($ot_enviada->proveedor_id == 3) {
                    $cantidad_enviadas_flexoclean++;
                } elseif ($ot_enviada->proveedor_id == 4) {
                    $cantidad_enviadas_artfactory++;
                }

                $array_ot_procesadas_enviadas[] = $ot_enviada->work_order_id;
            }
        }

        //Total OT Pendientes a Diseñador externo a la fecha
        $ots_enviadas_diseño = Management::where('management_type_id', 9)
            ->where('created_at', '>=', '2024-01-01 00:00:00')
            ->orderBy('work_order_id', 'Asc')
            ->get();

        $array_ot_procesadas_pendientes = array();

        foreach ($ots_enviadas_diseño as $ot_enviada) {

            if (!in_array($ot_enviada->work_order_id, $array_ot_procesadas_pendientes)) {
                if ($ot_enviada->proveedor_id == 1) {
                    if ($ot_enviada->recibido_diseño_externo == 0) {
                        $cantidad_pendiente_prinflex++;
                    }
                } elseif ($ot_enviada->proveedor_id == 2) {
                    if ($ot_enviada->recibido_diseño_externo == 0) {
                        $cantidad_pendiente_graphicbox++;
                    }
                } elseif ($ot_enviada->proveedor_id == 3) {
                    if ($ot_enviada->recibido_diseño_externo == 0) {
                        $cantidad_pendiente_flexoclean++;
                    }
                } elseif ($ot_enviada->proveedor_id == 4) {
                    if ($ot_enviada->recibido_diseño_externo == 0) {
                        $cantidad_pendiente_artfactory++;
                    }
                }

                $array_ot_procesadas_pendientes[] = $ot_enviada->work_order_id;
            }
        }


        //Total OT Recepcionada por Diseñador externo mes en curso
        $ots_recepcionadas_diseño_periodo = Management::where('management_type_id', 10)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('work_order_id', 'Asc')
            ->get();

        $array_ot_procesadas_recepcionadas = array();

        foreach ($ots_recepcionadas_diseño_periodo as $ot_recepcionada) {
            if (!in_array($ot_recepcionada->work_order_id, $array_ot_procesadas_recepcionadas)) {

                if ($ot_recepcionada->proveedor_id == 1) {
                    $cantidad_recepcion_prinflex++;
                } elseif ($ot_recepcionada->proveedor_id == 2) {
                    $cantidad_recepcion_graphicbox++;
                } elseif ($ot_recepcionada->proveedor_id == 3) {
                    $cantidad_recepcion_flexoclean++;
                } elseif ($ot_recepcionada->proveedor_id == 4) {
                    $cantidad_recepcion_artfactory++;
                }

                $array_ot_procesadas_recepcionadas[] = $ot_recepcionada->work_order_id;
            }
        }

        //dd($cantidad_envio_prinflex,$indicador_result,$result_array);
        return view(
            'reports.reportTiempoDisenadorExterno',
            compact(
                'mes',
                'years',
                'mesSeleccionado',
                'yearSeleccionado',
                'nombreMesesSeleccionados',
                'cantidad_envio_prinflex',
                'cantidad_envio_graphicbox',
                'cantidad_envio_flexoclean',
                'cantidad_envio_artfactory',
                'cantidad_pendiente_prinflex',
                'cantidad_pendiente_graphicbox',
                'cantidad_pendiente_flexoclean',
                'cantidad_pendiente_artfactory',
                'prom_tiempo_duracion_prinflex',
                'prom_tiempo_duracion_graphicbox',
                'prom_tiempo_duracion_flexoclean',
                'prom_tiempo_duracion_artfactory',
                'indicador_result',
                'cantidad_enviadas_prinflex',
                'cantidad_enviadas_graphicbox',
                'cantidad_enviadas_flexoclean',
                'cantidad_enviadas_artfactory',
                'cantidad_recepcion_prinflex',
                'cantidad_recepcion_graphicbox',
                'cantidad_recepcion_flexoclean',
                'cantidad_recepcion_artfactory'
            )
        );
    }

    //Consultas complementarias *********************************
    public function query_ot_area_sala_muestra()
    {
        // vendedores o creadores:
        $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4, 19])->get();
        $vendedores->map(function ($vendedor) {
            $vendedor->vendedor_id = $vendedor->id;
        });

        $vendedor_id = array();
        foreach ($vendedores as $vendedor) {
            $vendedor_id[] = $vendedor->vendedor_id;
        }

        $query = WorkOrder::select("work_orders.*")->with(
            'canal',
            'client',
            'creador',
            'productType',
            "ultimoCambioEstado.area",
            "vendedorAsignado.user",
            "ingenieroAsignado.user",
            "diseñadorAsignado.user",
            "catalogadorAsignado.user",
            "users",
            "gestiones.respuesta",
            "material",
            "muestrasPrioritarias"
        );

        $query = $query->join('work_spaces', 'work_spaces.id', '=', 'work_orders.current_area_id')
            ->where('work_orders.current_area_id', '!=', 1)
            ->select(
                'work_orders.id as work_order_id_',
                'current_area_id',
                'work_spaces.nombre as area_nombre',
                DB::raw('(CASE
                                    WHEN work_spaces.nombre = "Área de Ventas" THEN "V"
                                    WHEN work_spaces.nombre = "Área de Diseño Estructural" THEN "DE"
                                    WHEN work_spaces.nombre = "Área de Diseño Gráfico" THEN "DG"
                                    WHEN work_spaces.nombre = "Área de Catalogación" THEN "C"
                                    WHEN work_spaces.nombre = "Área de Precatalogación" THEN "PC"
                                    WHEN work_spaces.nombre = "Área de Muestras" THEN "SM"
                                    ELSE "Área"
                                    END)
                                    AS area_abreviatura_nombre'),
                DB::raw('count(*) as cantidad')
            );

        $query = $query->whereIn('creador_id', $vendedor_id);

        $query = $query->where('current_area_id', 6); //--->solo sala de muestra

        $estados_activos = [1, 2, 3, 4, 5, 6, 7, 10, 12, 14, 15, 16, 17, 18];
        $query = $query->join('managements', 'work_orders.id', 'managements.work_order_id')
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

        $ots = $query->groupBy(
            'work_order_id_',
            'area_nombre',
            'area_abreviatura_nombre',
            'current_area_id'
        )
            ->orderBy("work_orders.id", "desc")->get();

        $ot_array = [];
        foreach ($ots as $value) {
            $ot_array[] = $value->work_order_id_;
        }

        return $ot_array;
    }

    public function cantidad_ot_fecha_NULL($muestras)
    {
        $destinatarios = array();
        $cantidad_ot = [];

        foreach ($muestras as $muestra) {
            $ultimo_estado_ot = Management::where('work_order_id', $muestra->work_order_id)
                ->where('management_type_id', 1)
                ->orderBy('created_at', 'Desc')
                ->first();
            if (in_array($ultimo_estado_ot->state_id, [1, 2, 3, 4, 5, 6, 7, 10, 15, 16, 17, 19])) {

                if (!in_array($muestra->destinatarios_id[0], $destinatarios)) {
                    $destinatarios[] = $muestra->destinatarios_id[0];
                }

                if ($muestra->destinatarios_id[0] == 1 && $muestra->check_fecha_corte_vendedor == NULL && $muestra->fecha_corte_vendedor == NULL) {
                    $cantidad_ot[] = $muestra->work_order_id;
                } elseif ($muestra->destinatarios_id[0] == 2 && $muestra->check_fecha_corte_diseñador == NULL && $muestra->fecha_corte_diseñador == NULL) {
                    $cantidad_ot[] = $muestra->work_order_id;
                } elseif ($muestra->destinatarios_id[0] == 3 &&  $muestra->check_fecha_corte_laboratorio == NULL && $muestra->fecha_corte_laboratorio == NULL) {
                    $cantidad_ot[] = $muestra->work_order_id;
                } elseif ($muestra->destinatarios_id[0] == 4 && ($muestra->check_fecha_corte_1 == NULL || $muestra->check_fecha_corte_2 == NULL || $muestra->check_fecha_corte_3 == NULL || $muestra->check_fecha_corte_4 == NULL) && $muestra->fecha_corte_1 == NULL) {
                    $cantidad_ot[] = $muestra->work_order_id;
                } elseif ($muestra->destinatarios_id[0] == 5 && $muestra->check_fecha_corte_diseñador_revision == NULL && $muestra->fecha_corte_diseñador_revision == NULL) {
                    $cantidad_ot[] = $muestra->work_order_id;
                }
            }
        }

        $ots = count(array_unique($cantidad_ot));

        return $ots;
    }

    public function cantidad_id_fecha_NULL($muestras)
    {
        $destinatarios = array();
        $cantidad_id = [];

        foreach ($muestras as $muestra) {

            $ultimo_estado_ot = Management::where('work_order_id', $muestra->work_order_id)
                ->where('management_type_id', 1)
                ->orderBy('created_at', 'Desc')
                ->first();
            if (in_array($ultimo_estado_ot->state_id, [1, 2, 3, 4, 5, 6, 7, 10, 15, 16, 17, 19])) {
                if (!in_array($muestra->destinatarios_id[0], $destinatarios)) {
                    $destinatarios[] = $muestra->destinatarios_id[0];
                }

                if ($muestra->destinatarios_id[0] == 1 && $muestra->check_fecha_corte_vendedor == NULL && $muestra->fecha_corte_vendedor == NULL) {
                    $cantidad_id[] = $muestra->id;
                } elseif ($muestra->destinatarios_id[0] == 2 && $muestra->check_fecha_corte_diseñador == NULL && $muestra->fecha_corte_diseñador == NULL) {
                    $cantidad_id[] = $muestra->id;
                } elseif ($muestra->destinatarios_id[0] == 3 &&  $muestra->check_fecha_corte_laboratorio == NULL && $muestra->fecha_corte_laboratorio == NULL) {
                    $cantidad_id[] = $muestra->id;
                } elseif ($muestra->destinatarios_id[0] == 4 && ($muestra->check_fecha_corte_1 == NULL || $muestra->check_fecha_corte_2 == NULL || $muestra->check_fecha_corte_3 == NULL || $muestra->check_fecha_corte_4 == NULL) && $muestra->fecha_corte_1 == NULL) {
                    $cantidad_id[] = $muestra->id;
                } elseif ($muestra->destinatarios_id[0] == 5 && $muestra->check_fecha_corte_diseñador_revision == NULL && $muestra->fecha_corte_diseñador_revision == NULL) {
                    $cantidad_id[] = $muestra->id;
                }
            }
        }

        $ids = count(array_unique($cantidad_id));

        return $ids;
    }

    public function cantidad_muestras_fecha_NULL($muestras)
    {
        $destinatarios = array();
        $cantidad_muestras = 0;

        foreach ($muestras as $muestra) {

            $ultimo_estado_ot = Management::where('work_order_id', $muestra->work_order_id)
                ->where('management_type_id', 1)
                ->orderBy('created_at', 'Desc')
                ->first();
            if (in_array($ultimo_estado_ot->state_id, [1, 2, 3, 4, 5, 6, 7, 10, 15, 16, 17, 19])) {
                if (!in_array($muestra->destinatarios_id[0], $destinatarios)) {
                    $destinatarios[] = $muestra->destinatarios_id[0];
                }

                if ($muestra->destinatarios_id[0] == 1 && $muestra->check_fecha_corte_vendedor == NULL && $muestra->fecha_corte_vendedor == NULL) {
                    $cantidad_muestras += $muestra->cantidad_vendedor;
                } elseif ($muestra->destinatarios_id[0] == 2 && $muestra->check_fecha_corte_diseñador == NULL && $muestra->fecha_corte_diseñador == NULL) {
                    $cantidad_muestras += $muestra->cantidad_diseñador;
                } elseif ($muestra->destinatarios_id[0] == 3 &&  $muestra->check_fecha_corte_laboratorio == NULL && $muestra->fecha_corte_laboratorio == NULL) {
                    $cantidad_muestras += $muestra->cantidad_laboratorio;
                } elseif ($muestra->destinatarios_id[0] == 4 && ($muestra->check_fecha_corte_1 == NULL || $muestra->check_fecha_corte_2 == NULL || $muestra->check_fecha_corte_3 == NULL || $muestra->check_fecha_corte_4 == NULL) && $muestra->fecha_corte_1 == NULL) {
                    $cantidad_muestras += $muestra->cantidad_1;
                } elseif ($muestra->destinatarios_id[0] == 5 && $muestra->check_fecha_corte_diseñador_revision == NULL && $muestra->fecha_corte_diseñador_revision == NULL) {
                    $cantidad_muestras += $muestra->cantidad_diseñador_revision;
                }
            }
        }

        return $cantidad_muestras;
    }

    public function cantidad_ot_cortadas_fecha_con_DATO($fromDate, $toDate)
    {
        //Consulta de muestras en estado proceso -> 1 y tengan datos de fecha de corte
        //dd($fromDate, $toDate);
        /*$ot_sala_muestra = Muestra::select(DB::raw("COUNT(DISTINCT work_order_id) AS cantidad_ot"))
                                    ->where("work_order_id", "!=", "0")
                                    //->whereBetween('created_at', [$fromDate, $toDate])
                                    ->whereBetween('created_at', [$fromDate, $toDate])
                                    /*->whereNotNull('fecha_corte_vendedor')
                                    ->orWhereNotNull('fecha_corte_diseñador')
                                    ->orWhereNotNull('fecha_corte_laboratorio')
                                    ->orWhereNotNull('fecha_corte_1')
                                    ->whereBetween('fecha_corte_vendedor', [$fromDate, $toDate])
                                    ->orWhereBetween('fecha_corte_diseñador', [$fromDate, $toDate])
                                    ->orWhereBetween('fecha_corte_laboratorio', [$fromDate, $toDate])
                                    ->orWhereBetween('fecha_corte_1', [$fromDate, $toDate])
                                    ->pluck('cantidad_ot')
                                    ->first();*/

        $muestras = Muestra::where("work_order_id", "!=", "0")
            //->whereIn("estado", ["1", "3"])
            ->whereBetween('created_at', [$fromDate, $toDate])
            /*->whereBetween('fecha_corte_vendedor', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_diseñador', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_laboratorio', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_1', [$fromDate, $toDate])*/
            /*->where(function($query) use ($fromDate, $toDate){
            $query->whereBetween('fecha_corte_vendedor', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_diseñador', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_laboratorio', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_1', [$fromDate, $toDate]);

            })*/
            ->get();

        $destinatarios = array();
        $cantidad_ot = [];

        foreach ($muestras as $muestra) {
            if (!in_array($muestra->destinatarios_id[0], $destinatarios)) {
                $destinatarios[] = $muestra->destinatarios_id[0];
            }

            if ($muestra->destinatarios_id[0] == 1 && $muestra->check_fecha_corte_vendedor != NULL && $muestra->fecha_corte_vendedor != NULL) {
                $cantidad_ot[] = $muestra->work_order_id;
            } elseif ($muestra->destinatarios_id[0] == 2 && $muestra->check_fecha_corte_diseñador != NULL && $muestra->fecha_corte_diseñador != NULL) {
                $cantidad_ot[] = $muestra->work_order_id;
            } elseif ($muestra->destinatarios_id[0] == 3 &&  $muestra->check_fecha_corte_laboratorio != NULL && $muestra->fecha_corte_laboratorio != NULL) {
                $cantidad_ot[] = $muestra->work_order_id;
            } elseif ($muestra->destinatarios_id[0] == 4 && ($muestra->check_fecha_corte_1 != NULL || $muestra->check_fecha_corte_2 != NULL || $muestra->check_fecha_corte_3 != NULL || $muestra->check_fecha_corte_4 != NULL) && $muestra->fecha_corte_1 != NULL) {
                $cantidad_ot[] = $muestra->work_order_id;
            } elseif ($muestra->destinatarios_id[0] == 5 && $muestra->check_fecha_corte_diseñador_revision != NULL && $muestra->fecha_corte_diseñador_revision != NULL) {
                $cantidad_ot[] = $muestra->work_order_id;
            }
        }

        $ots = count(array_unique($cantidad_ot));

        return array(
            'cantidad' =>  $ots,
        );
    }

    public function cantidad_id_cortadas_fecha_con_DATO($fromDate, $toDate)
    {
        //Consulta de muestras en estado proceso -> 1 y tengan datos de fecha de corte
        $muestras = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('created_at', [$fromDate, $toDate])
            //->whereIn("estado", ["1", "3"])
            /*->where(function($query) use ($fromDate, $toDate){
            $query->whereBetween('fecha_corte_vendedor', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_diseñador', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_laboratorio', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_1', [$fromDate, $toDate])
            })*/
            ->get();

        $destinatarios = array();
        $cantidad_id = [];

        foreach ($muestras as $muestra) {
            if (!in_array($muestra->destinatarios_id[0], $destinatarios)) {
                $destinatarios[] = $muestra->destinatarios_id[0];
            }

            if ($muestra->destinatarios_id[0] == 1 && $muestra->check_fecha_corte_vendedor != NULL && $muestra->fecha_corte_vendedor != NULL) {
                $cantidad_id[] = $muestra->id;
            } elseif ($muestra->destinatarios_id[0] == 2 && $muestra->check_fecha_corte_diseñador != NULL && $muestra->fecha_corte_diseñador != NULL) {
                $cantidad_id[] = $muestra->id;
            } elseif ($muestra->destinatarios_id[0] == 3 &&  $muestra->check_fecha_corte_laboratorio != NULL && $muestra->fecha_corte_laboratorio != NULL) {
                $cantidad_id[] = $muestra->id;
            } elseif ($muestra->destinatarios_id[0] == 4 && ($muestra->check_fecha_corte_1 != NULL || $muestra->check_fecha_corte_2 != NULL || $muestra->check_fecha_corte_3 != NULL || $muestra->check_fecha_corte_4 != NULL) && $muestra->fecha_corte_1 != NULL) {
                $cantidad_id[] = $muestra->id;
            } elseif ($muestra->destinatarios_id[0] == 5 && $muestra->check_fecha_corte_diseñador_revision != NULL && $muestra->fecha_corte_diseñador_revision != NULL) {
                $cantidad_id[] = $muestra->id;
            }
        }

        $ids = count(array_unique($cantidad_id));

        return array(
            'cantidad' =>  $ids,
        );
    }

    public function cantidad_muestras_cortadas_fecha_con_DATO($fromDate, $toDate)
    {


        $cantidad_muestras = 0;
        //Consulta de muestras en estado proceso -> 1 y tengan datos de fecha de corte
        $muestras_vendedor = Muestra::select(DB::raw("SUM(cantidad_vendedor) as cantidad_vendedor"))
            ->where("work_order_id", "!=", "0")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_vendedor')
            ->get();
        $cantidad_muestras += $muestras_vendedor[0]->cantidad_vendedor;

        $muestras_diseñador = Muestra::select(DB::raw("SUM(cantidad_diseñador) as cantidad_diseñador"))
            ->where("work_order_id", "!=", "0")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_diseñador')
            ->get();
        $cantidad_muestras += $muestras_diseñador[0]->cantidad_diseñador;

        $muestras_diseñador_revision = Muestra::select(DB::raw("SUM(cantidad_diseñador_revision) as cantidad_diseñador_revision"))
            ->where("work_order_id", "!=", "0")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_diseñador_revision')
            ->get();
        $cantidad_muestras += $muestras_diseñador_revision[0]->cantidad_diseñador_revision;

        $muestras_laboratorio = Muestra::select(DB::raw("SUM(cantidad_laboratorio) as cantidad_laboratorio"))
            ->where("work_order_id", "!=", "0")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_laboratorio')
            ->get();
        $cantidad_muestras += $muestras_laboratorio[0]->cantidad_laboratorio;

        $muestras_cliente_1 = Muestra::select(DB::raw("SUM(cantidad_1) as cantidad_1"))
            ->where("work_order_id", "!=", "0")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_1')
            ->get();
        $cantidad_muestras += $muestras_cliente_1[0]->cantidad_1;

        $muestras_cliente_2 = Muestra::select(DB::raw("SUM(cantidad_2) as cantidad_2"))
            ->where("work_order_id", "!=", "0")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_2')
            ->get();
        $cantidad_muestras += $muestras_cliente_2[0]->cantidad_2;

        $muestras_cliente_3 = Muestra::select(DB::raw("SUM(cantidad_3) as cantidad_3"))
            ->where("work_order_id", "!=", "0")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_3')
            ->get();
        $cantidad_muestras += $muestras_cliente_3[0]->cantidad_3;

        $muestras_cliente_4 = Muestra::select(DB::raw("SUM(cantidad_4) as cantidad_4"))
            ->where("work_order_id", "!=", "0")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_4')
            ->get();
        $cantidad_muestras += $muestras_cliente_4[0]->cantidad_4;
        //->whereIn("estado", ["1", "3"])
        /*->where(function($query) use ($fromDate, $toDate){
            $query->whereBetween('fecha_corte_vendedor', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_diseñador', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_laboratorio', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_1', [$fromDate, $toDate]);
            })*/


        //$destinatarios = array();


        /*foreach ($muestras as $muestra) {
            /*if (!in_array($muestra->destinatarios_id[0], $destinatarios)) {
                $destinatarios[] = $muestra->destinatarios_id[0];
            }

            if ($muestra->destinatarios_id[0] == 1 && $muestra->check_fecha_corte_vendedor != NULL && $muestra->fecha_corte_vendedor != NULL) {
                $cantidad_muestras += $muestra->cantidad_vendedor;
            } elseif ($muestra->destinatarios_id[0] == 2 && $muestra->check_fecha_corte_diseñador != NULL && $muestra->fecha_corte_diseñador != NULL) {
                $cantidad_muestras += $muestra->cantidad_diseñador;
            } elseif ($muestra->destinatarios_id[0] == 3 &&  $muestra->check_fecha_corte_laboratorio != NULL && $muestra->fecha_corte_laboratorio != NULL) {
                $cantidad_muestras += $muestra->cantidad_laboratorio;
            } elseif ($muestra->destinatarios_id[0] == 4 && ($muestra->check_fecha_corte_1 != NULL || $muestra->check_fecha_corte_2 != NULL || $muestra->check_fecha_corte_3 != NULL || $muestra->check_fecha_corte_4 != NULL) && $muestra->fecha_corte_1 != NULL) {
                $cantidad_muestras += $muestra->cantidad_1;
            }
            if(!is_null($muestra->fecha_corte_vendedor)){
                $cantidad_muestras += $muestra->cantidad_vendedor;
            }
            if(!is_null($muestra->fecha_corte_diseñador)){
                $cantidad_muestras += $muestra->cantidad_diseñador;
            }
            if(!is_null($muestra->fecha_corte_laboratorio)){
                $cantidad_muestras += $muestra->cantidad_laboratorio;
            }
            if(!is_null($muestra->fecha_corte_1)){
                $cantidad_muestras += $muestra->cantidad_1;
            }
            if(!is_null($muestra->fecha_corte_2)){
                $cantidad_muestras += $muestra->cantidad_2;
            }
            if(!is_null($muestra->fecha_corte_3)){
                $cantidad_muestras += $muestra->cantidad_3;
            }
            if(!is_null($muestra->fecha_corte_4)){
                $cantidad_muestras += $muestra->cantidad_4;
            }
        }*/
        /*
        if($fromDate=='2023-10-01 00:00:00' && $toDate== '2023-10-31 23:59:59'){
            dd( $cantidad_muestras,$muestras_vendedor[0]->cantidad_vendedor,
                $muestras_diseñador[0]->cantidad_diseñador,$muestras_laboratorio[0]->cantidad_laboratorio,
                $muestras_cliente_1[0]->cantidad_1,$muestras_cliente_2[0]->cantidad_2,
                $muestras_cliente_3[0]->cantidad_3,$muestras_cliente_4[0]->cantidad_4);
        }*/

        return $cantidad_muestras;
    }

    public function query_cantidad_ot_sala_muestra_pendiente_corte($fromDate, $toDate)
    {
        //Consulta de muestras en estado proceso -> 1 y que hayan pasado durante el rango de fecha de búsqueda a Sala de muestra (estado 17)
        $ot_sala_muestra = Management::select(\DB::raw('distinct managements.id, managements.*'))
            ->join('muestras', 'muestras.work_order_id', '=', 'managements.work_order_id')
            ->where("muestras.work_order_id", "!=", "0")
            ->whereIn("muestras.estado", ["1", "3"])
            ->where("managements.state_id", "17")
            ->whereBetween('managements.created_at', [$fromDate, $toDate])
            // ->where(function($query){
            //     $query->whereNull('muestras.fecha_corte_vendedor')
            //         ->whereNull('muestras.fecha_corte_diseñador')
            //         ->whereNull('muestras.fecha_corte_laboratorio')
            //         ->whereNull('muestras.fecha_corte_1');
            //     })
            ->get();
        $cantidad_ot = [];
        foreach ($ot_sala_muestra as $value) {
            $cantidad_ot[] = $value->work_order_id;
        }

        $cantidad = count($cantidad_ot);

        return $cantidad;
    }

    public function cantidad_id_pendiente_corte($fromDate, $toDate)
    {
        //Consulta de muestras en estado proceso -> 1 y con fechas NULL
        $muestras = Muestra::select(\DB::raw("DISTINCT muestras.id as muestra_id, work_orders.id as work_order_id"))
            ->whereIn("estado", ["1", "3"])
            ->join('work_orders', 'work_orders.id', 'muestras.work_order_id')
            ->join('managements', 'work_orders.id', 'managements.work_order_id')
            ->where('managements.state_id', '17')
            ->whereBetween('managements.created_at', [$fromDate, $toDate])
            ->get();

        return $muestras->count();
    }

    public function cantidad_muestras_pendiente_corte($fromDate, $toDate)
    {
        //Consulta de muestras en estado proceso -> 1 y con fechas NULL
        $muestras = Muestra::select(\DB::raw(
            "
            DISTINCT
                muestras.id as muestra_id,
                work_orders.id as work_order_id,
                COALESCE(muestras.cantidad_vendedor, 0)  AS cantidad_vendedor,
                COALESCE(muestras.cantidad_diseñador, 0) AS cantidad_disenador,
                COALESCE(muestras.cantidad_laboratorio, 0) AS cantidad_lab,
                COALESCE(muestras.cantidad_1, 0) AS cantidad_1"
        ))
            ->whereIn("estado", ["1", "3"])
            ->join('work_orders', 'work_orders.id', 'muestras.work_order_id')
            ->join('managements', 'work_orders.id', 'managements.work_order_id')
            ->where('managements.state_id', '17')
            ->whereBetween('managements.created_at', [$fromDate, $toDate])
            ->get();

        $cantidad_muestras = 0;

        foreach ($muestras as $muestra) {
            $cantidad_muestras += $muestra->cantidad_vendedor + $muestra->cantidad_disenador + $muestra->cantidad_lab + $muestra->cantidad_1;
        }

        return $cantidad_muestras;
    }
    //Fin consultas complementarias *********************************


    public function cantidad_ot_por_area_mes_actual($area_id, $vendedor_id)
    {
        $query = WorkOrder::select("work_orders.*")->with(
            'canal',
            'client',
            'creador',
            'productType',
            "ultimoCambioEstado.area",
            "vendedorAsignado.user",
            "ingenieroAsignado.user",
            "diseñadorAsignado.user",
            "catalogadorAsignado.user",
            "users",
            "gestiones.respuesta",
            "material",
            "muestrasPrioritarias"
        );

        $query = $query->join('work_spaces', 'work_spaces.id', '=', 'work_orders.current_area_id')
            ->where('work_orders.current_area_id', '!=', 1)
            ->select(
                'current_area_id',
                'work_spaces.nombre as area_nombre',
                DB::raw('(CASE
                                    WHEN work_spaces.nombre = "Área de Ventas" THEN "V"
                                    WHEN work_spaces.nombre = "Área de Diseño Estructural" THEN "DE"
                                    WHEN work_spaces.nombre = "Área de Diseño Gráfico" THEN "DG"
                                    WHEN work_spaces.nombre = "Área de Catalogación" THEN "C"
                                    WHEN work_spaces.nombre = "Área de Precatalogación" THEN "PC"
                                    WHEN work_spaces.nombre = "Área de Muestras" THEN "SM"
                                    ELSE "Área"
                                    END)
                                    AS area_abreviatura_nombre'),
                DB::raw('count(*) as cantidad')
            );

        $query = $query->whereIn('creador_id', $vendedor_id);

        $query = $query->whereIn('current_area_id', $area_id);

        $estados_activos = [1, 2, 3, 4, 5, 6, 7, 10, 12, 14, 15, 16, 17, 18];
        $query = $query->join('managements', 'work_orders.id', 'managements.work_order_id')
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

        $ots = $query->groupBy(
            'area_nombre',
            'area_abreviatura_nombre',
            'current_area_id'
        )->orderBy("work_orders.id", "desc")
            ->get();

        //Se establece primero el arreglo que quiero mostrar en la grafica
        $array_key_definition = ['DE' => 0, 'SM' => 0, 'DG' => 0, 'PC' => 0, 'C' => 0];
        //Recorremos los datos y asignamos el valor que consiga en la consulta, sino se envia 0
        foreach ($ots as $value) {
            $array_key_definition[$value->area_abreviatura_nombre] = $value->cantidad;
        }
        $array_cantidad_ot_por_area = array_values($array_key_definition); //Asigna el valor
        $array_keys_ot_por_area = array_keys($array_key_definition); //Asigna el key

        return array(
            'array_cantidad_ot_por_area' => $array_cantidad_ot_por_area,
            'array_keys_ot_por_area' => $array_keys_ot_por_area
        );
    }

    public function tiempo_promedio_anio_anterior_ot($area_id, $vendedor_id, $fromDate, $toDate)
    {

        $fullDate = Carbon::instance($fromDate)->subYear('1');
        $anioAnterior = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $fromDate = $anioAnterior . "-01-01 00:00:00";
        $toDate = $anioAnterior . "-12-31 23:59:59";

        $titulo = 'Prom' . ' ' . $anio_digit;
        // dd($fromDate,$toDate);
        //Funcion que tiene la query, solo le pasamos el rango de fecha que deseamos buscar
        $count_ot_anio_actual_desarrollo        = 0;
        $count_ot_anio_actual_muestra           = 0;
        $count_ot_anio_actual_diseno            = 0;
        $count_ot_anio_actual_catalogacion      = 0;
        $count_ot_anio_actual_precatalogacion   = 0;
        $suma_ot_anio_actual_desarrollo         = 0;
        $suma_ot_anio_actual_muestra            = 0;
        $suma_ot_anio_actual_diseno             = 0;
        $suma_ot_anio_actual_catalogacion       = 0;
        $suma_ot_anio_actual_precatalogacion    = 0;

        $fecha_aux_ini = $fromDate;

        for ($i = 0; $i <= 12; $i++) {

            $fecha_aux_fin = Carbon::createFromFormat('Y-m-d', date('Y', strtotime($fecha_aux_ini)) . '-' . date('m', strtotime($fecha_aux_ini)) . '-' . '1')->endOfMonth();
            $fecha_aux_ini = Carbon::createFromFormat('Y-m-d', date('Y', strtotime($fecha_aux_ini)) . '-' . date('m', strtotime($fecha_aux_ini)) . '-' . '1')->startOfMonth();

            //Area Diseño Estructural
            $reporte_de = ReporteDesm::where('mes', date('m', strtotime($fecha_aux_ini)))
                ->where('anio', date('Y', strtotime($fecha_aux_ini)))
                ->where('area', 2)
                ->where('deleted', 0)
                ->first();
            if ($reporte_de) {
                $count_ot_anio_actual_desarrollo    += $reporte_de->count_ot;
                $suma_ot_anio_actual_desarrollo     += $reporte_de->sum_ot;
            } else {

                $ot_promedio_anio_actual = self::query_tiempos_area_aux($fecha_aux_ini, $fecha_aux_fin, 2, $vendedor_id);
                $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fecha_aux_ini, $fecha_aux_fin, $vendedor_id, 2);
                $suma_ot_anio_actual_desarrollo += $promedio['suma_ot_trabajados'];
                $count_ot_anio_actual_desarrollo += $promedio['count_ot_trabajados'];

                $reporte_insert             = new ReporteDesm();
                $reporte_insert->mes        = date('m', strtotime($fecha_aux_ini));
                $reporte_insert->anio       = date('Y', strtotime($fecha_aux_ini));
                $reporte_insert->area       = 2;
                $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                $reporte_insert->deleted    = 0;
                $reporte_insert->save();
            }

            //Area de Diseño Grafico
            $reporte_dg = ReporteDesm::where('mes', date('m', strtotime($fecha_aux_ini)))
                ->where('anio', date('Y', strtotime($fecha_aux_ini)))
                ->where('area', 3)
                ->where('deleted', 0)
                ->first();
            if ($reporte_dg) {
                $count_ot_anio_actual_diseno    += $reporte_dg->count_ot;
                $suma_ot_anio_actual_diseno     += $reporte_dg->sum_ot;
            } else {
                $ot_promedio_anio_actual = self::query_tiempos_area_aux($fecha_aux_ini, $fecha_aux_fin, 3, $vendedor_id);
                $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fecha_aux_ini, $fecha_aux_fin, $vendedor_id, 3);
                $suma_ot_anio_actual_diseno     += $promedio['suma_ot_trabajados'];
                $count_ot_anio_actual_diseno    += $promedio['count_ot_trabajados'];

                $reporte_insert             = new ReporteDesm();
                $reporte_insert->mes        = date('m', strtotime($fecha_aux_ini));
                $reporte_insert->anio       = date('Y', strtotime($fecha_aux_ini));
                $reporte_insert->area       = 3;
                $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                $reporte_insert->deleted    = 0;
                $reporte_insert->save();
            }
            //Area de Sala de Muestra
            $reporte_sm = ReporteDesm::where('mes', date('m', strtotime($fecha_aux_ini)))
                ->where('anio', date('Y', strtotime($fecha_aux_ini)))
                ->where('area', 6)
                ->where('deleted', 0)
                ->first();
            if ($reporte_sm) {
                $count_ot_anio_actual_muestra    += $reporte_sm->count_ot;
                $suma_ot_anio_actual_muestra     += $reporte_sm->sum_ot;
            } else {
                $ot_promedio_anio_actual = self::query_tiempos_area_aux($fecha_aux_ini, $fecha_aux_fin, 6, $vendedor_id);
                $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fecha_aux_ini, $fecha_aux_fin, $vendedor_id, 6);
                $suma_ot_anio_actual_muestra     += $promedio['suma_ot_trabajados'];
                $count_ot_anio_actual_muestra    += $promedio['count_ot_trabajados'];

                $reporte_insert             = new ReporteDesm();
                $reporte_insert->mes        = date('m', strtotime($fecha_aux_ini));
                $reporte_insert->anio       = date('Y', strtotime($fecha_aux_ini));
                $reporte_insert->area       = 6;
                $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                $reporte_insert->deleted    = 0;
                $reporte_insert->save();
            }
            //Area de Pre-Catalogacion
            $reporte_pc = ReporteDesm::where('mes', date('m', strtotime($fecha_aux_ini)))
                ->where('anio', date('Y', strtotime($fecha_aux_ini)))
                ->where('area', 5)
                ->where('deleted', 0)
                ->first();
            if ($reporte_pc) {
                $count_ot_anio_actual_precatalogacion    += $reporte_pc->count_ot;
                $suma_ot_anio_actual_precatalogacion     += $reporte_pc->sum_ot;
            } else {
                $ot_promedio_anio_actual = self::query_tiempos_area_aux($fecha_aux_ini, $fecha_aux_fin, 5, $vendedor_id);
                $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fecha_aux_ini, $fecha_aux_fin, $vendedor_id, 5);
                $suma_ot_anio_actual_precatalogacion     += $promedio['suma_ot_trabajados'];
                $count_ot_anio_actual_precatalogacion    += $promedio['count_ot_trabajados'];

                $reporte_insert             = new ReporteDesm();
                $reporte_insert->mes        = date('m', strtotime($fecha_aux_ini));
                $reporte_insert->anio       = date('Y', strtotime($fecha_aux_ini));
                $reporte_insert->area       = 5;
                $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                $reporte_insert->deleted    = 0;
                $reporte_insert->save();
            }

            //Area de Catalogacion
            $reporte_ca = ReporteDesm::where('mes', date('m', strtotime($fecha_aux_ini)))
                ->where('anio', date('Y', strtotime($fecha_aux_ini)))
                ->where('area', 4)
                ->where('deleted', 0)
                ->first();
            if ($reporte_ca) {
                $count_ot_anio_actual_catalogacion    += $reporte_ca->count_ot;
                $suma_ot_anio_actual_catalogacion     += $reporte_ca->sum_ot;
            } else {
                $ot_promedio_anio_actual = self::query_tiempos_area_aux($fecha_aux_ini, $fecha_aux_fin, 4, $vendedor_id);
                $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fecha_aux_ini, $fecha_aux_fin, $vendedor_id, 4);
                $suma_ot_anio_actual_catalogacion     += $promedio['suma_ot_trabajados'];
                $count_ot_anio_actual_catalogacion    += $promedio['count_ot_trabajados'];

                $reporte_insert             = new ReporteDesm();
                $reporte_insert->mes        = date('m', strtotime($fecha_aux_ini));
                $reporte_insert->anio       = date('Y', strtotime($fecha_aux_ini));
                $reporte_insert->area       = 4;
                $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                $reporte_insert->deleted    = 0;
                $reporte_insert->save();
            }


            /* var_dump("<br>");
                var_dump($fecha_aux_ini);
                var_dump("<br>");
                var_dump($fecha_aux_fin);
                var_dump("<br>");
                $promedio_anio_actual_desarrollo++;
                $promedio_anio_actual_muestra++;
                $promedio_anio_actual_diseno++;
                $promedio_anio_actual_catalogacion++;
                $promedio_anio_actual_precatalogacion++;*/

            /*$ot_promedio_anio_actual = self::query_tiempos_area($fecha_aux_ini,$fecha_aux_fin,$area_id,$vendedor_id);

            $promedio = self::calcula_el_promedio_anio_actual($ot_promedio_anio_actual,$fecha_aux_ini, $fecha_aux_fin,$vendedor_id,2);

            $count_ot_anio_actual_desarrollo += $promedio['count_ot_trabajados_desarrollo'];
            $count_ot_anio_actual_muestra += $promedio['count_ot_trabajados_muestra'];
            $count_ot_anio_actual_diseno += $promedio['count_ot_trabajados_diseno'];
            $count_ot_anio_actual_catalogacion += $promedio['count_ot_trabajados_catalogacion'];
            $count_ot_anio_actual_precatalogacion += $promedio['count_ot_trabajados_precatalogacion'];

            $suma_ot_anio_actual_desarrollo += $promedio['suma_ot_trabajados_desarrollo'];
            $suma_ot_anio_actual_muestra += $promedio['suma_ot_trabajados_muestra'];
            $suma_ot_anio_actual_diseno += $promedio['suma_ot_trabajados_diseno'];
            $suma_ot_anio_actual_catalogacion += $promedio['suma_ot_trabajados_catalogacion'];
            $suma_ot_anio_actual_precatalogacion += $promedio['suma_ot_trabajados_precatalogacion'];*/


            $fecha_aux_ini = date("Y-m-d", strtotime($fecha_aux_ini . "+ 1 month"));
            if ($toDate == $fecha_aux_fin) {
                $i = 13;
            }
        }

        /* $ot_promedio_anio_anterior = self::query_tiempos_area($fromDate, $toDate, $area_id, $vendedor_id);
        //Funcion que calcula el promedio, solo le pasamos el resultado de las ot

        $promedio = self::calcula_el_promedio($ot_promedio_anio_anterior,$fromDate, $toDate,$vendedor_id,1);
        $promedio_anio_anterior_desarrollo = $promedio['promedio_dias_trabajados_desarrollo'];
        $promedio_anio_anterior_muestra = $promedio['promedio_dias_trabajados_muestra'];
        $promedio_anio_anterior_diseno = $promedio['promedio_dias_trabajados_diseno'];
        $promedio_anio_anterior_catalogacion = $promedio['promedio_dias_trabajados_catalogacion'];
        $promedio_anio_anterior_precatalogacion = $promedio['promedio_dias_trabajados_precatalogacion'];*/

        $promedio_anio_anterior_desarrollo = ($count_ot_anio_actual_desarrollo > 0) ? round($suma_ot_anio_actual_desarrollo / $count_ot_anio_actual_desarrollo, 1) : 0;
        $promedio_anio_anterior_muestra = ($count_ot_anio_actual_muestra > 0) ? round($suma_ot_anio_actual_muestra / $count_ot_anio_actual_muestra, 1) : 0;
        $promedio_anio_anterior_diseno = ($count_ot_anio_actual_diseno > 0) ? round($suma_ot_anio_actual_diseno / $count_ot_anio_actual_diseno, 1) : 0;
        $promedio_anio_anterior_catalogacion = ($count_ot_anio_actual_catalogacion > 0) ? round($suma_ot_anio_actual_catalogacion / $count_ot_anio_actual_catalogacion, 1) : 0;
        $promedio_anio_anterior_precatalogacion = ($count_ot_anio_actual_precatalogacion > 0) ? round($suma_ot_anio_actual_precatalogacion / $count_ot_anio_actual_precatalogacion, 1) : 0;


        return array(
            'titulo' => $titulo,
            'promedio_anio_anterior_desarrollo' => $promedio_anio_anterior_desarrollo,
            'promedio_anio_anterior_muestra' => $promedio_anio_anterior_muestra,
            'promedio_anio_anterior_diseno' => $promedio_anio_anterior_diseno,
            'promedio_anio_anterior_catalogacion' => $promedio_anio_anterior_catalogacion,
            'promedio_anio_anterior_precatalogacion' => $promedio_anio_anterior_precatalogacion,
        );
    }

    public function tiempo_promedio_mes_actual_anio_anterior_ot($area_id, $vendedor_id, $fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate)->subYear('1');

        $anioAnterior = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $mes = $fullDate->format('m');
        $fromDate = $anioAnterior . "-" . $mes . "-01 00:00:00";
        $toDate = date("Y-m-t", strtotime($fromDate)) . ' 23:59:59';

        //Funcion para obtener el nombre del mes en español
        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
        $mes_nombre =  $meses[$mes - 1];

        $titulo = ucwords($mes_nombre) . ' ' . $anio_digit;

        $count_ot_desarrollo        = 0;
        $count_ot_muestra           = 0;
        $count_ot_diseno            = 0;
        $count_ot_catalogacion      = 0;
        $count_ot_precatalogacion   = 0;
        $suma_ot_desarrollo         = 0;
        $suma_ot_muestra            = 0;
        $suma_ot_diseno             = 0;
        $suma_ot_catalogacion       = 0;
        $suma_ot_precatalogacion    = 0;

        $reporte_de = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
            ->where('anio', date('Y', strtotime($fromDate)))
            ->where('area', 2)
            ->where('deleted', 0)
            ->first();

        if ($reporte_de) {
            $count_ot_desarrollo    += $reporte_de->count_ot;
            $suma_ot_desarrollo     += $reporte_de->sum_ot;
        } else {

            $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 2, $vendedor_id);
            $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 2);
            $suma_ot_desarrollo += $promedio['suma_ot_trabajados'];
            $count_ot_desarrollo += $promedio['count_ot_trabajados'];

            $reporte_insert             = new ReporteDesm();
            $reporte_insert->mes        = date('m', strtotime($fromDate));
            $reporte_insert->anio       = date('Y', strtotime($fromDate));
            $reporte_insert->area       = 2;
            $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
            $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
            $reporte_insert->deleted    = 0;
            $reporte_insert->save();
        }

        $reporte_dg = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
            ->where('anio', date('Y', strtotime($fromDate)))
            ->where('area', 3)
            ->where('deleted', 0)
            ->first();

        if ($reporte_dg) {
            $count_ot_diseno    += $reporte_dg->count_ot;
            $suma_ot_diseno     += $reporte_dg->sum_ot;
        } else {

            $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 3, $vendedor_id);
            $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 3);
            $suma_ot_diseno += $promedio['suma_ot_trabajados'];
            $count_ot_diseno += $promedio['count_ot_trabajados'];

            $reporte_insert             = new ReporteDesm();
            $reporte_insert->mes        = date('m', strtotime($fromDate));
            $reporte_insert->anio       = date('Y', strtotime($fromDate));
            $reporte_insert->area       = 3;
            $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
            $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
            $reporte_insert->deleted    = 0;
            $reporte_insert->save();
        }

        $reporte_sm = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
            ->where('anio', date('Y', strtotime($fromDate)))
            ->where('area', 6)
            ->where('deleted', 0)
            ->first();
        if ($reporte_sm) {
            $count_ot_muestra    += $reporte_sm->count_ot;
            $suma_ot_muestra     += $reporte_sm->sum_ot;
        } else {

            $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 6, $vendedor_id);
            $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 6);
            $suma_ot_muestra += $promedio['suma_ot_trabajados'];
            $count_ot_muestra += $promedio['count_ot_trabajados'];

            $reporte_insert             = new ReporteDesm();
            $reporte_insert->mes        = date('m', strtotime($fromDate));
            $reporte_insert->anio       = date('Y', strtotime($fromDate));
            $reporte_insert->area       = 6;
            $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
            $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
            $reporte_insert->deleted    = 0;
            $reporte_insert->save();
        }

        $reporte_ca = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
            ->where('anio', date('Y', strtotime($fromDate)))
            ->where('area', 4)
            ->where('deleted', 0)
            ->first();
        if ($reporte_ca) {
            $count_ot_catalogacion    += $reporte_ca->count_ot;
            $suma_ot_catalogacion     += $reporte_ca->sum_ot;
        } else {

            $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 4, $vendedor_id);
            $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 4);
            $suma_ot_catalogacion += $promedio['suma_ot_trabajados'];
            $count_ot_catalogacion += $promedio['count_ot_trabajados'];

            $reporte_insert             = new ReporteDesm();
            $reporte_insert->mes        = date('m', strtotime($fromDate));
            $reporte_insert->anio       = date('Y', strtotime($fromDate));
            $reporte_insert->area       = 5;
            $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
            $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
            $reporte_insert->deleted    = 0;
            $reporte_insert->save();
        }

        $reporte_pc = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
            ->where('anio', date('Y', strtotime($fromDate)))
            ->where('area', 5)
            ->where('deleted', 0)
            ->first();

        if ($reporte_pc) {
            $count_ot_precatalogacion    += $reporte_pc->count_ot;
            $suma_ot_precatalogacion     += $reporte_pc->sum_ot;
        } else {

            $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 5, $vendedor_id);
            $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 5);
            $suma_ot_precatalogacion += $promedio['suma_ot_trabajados'];
            $count_ot_precatalogacion += $promedio['count_ot_trabajados'];

            $reporte_insert             = new ReporteDesm();
            $reporte_insert->mes        = date('m', strtotime($fromDate));
            $reporte_insert->anio       = date('Y', strtotime($fromDate));
            $reporte_insert->area       = 5;
            $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
            $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
            $reporte_insert->deleted    = 0;
            $reporte_insert->save();
        }

        $promedio_mes_actual_anio_anterior_desarrollo = ($count_ot_desarrollo > 0) ? round($suma_ot_desarrollo / $count_ot_desarrollo, 1) : 0;
        $promedio_mes_actual_anio_anterior_muestra = ($count_ot_muestra > 0) ? round($suma_ot_muestra / $count_ot_muestra, 1) : 0;
        $promedio_mes_actual_anio_anterior_diseno = ($count_ot_diseno > 0) ? round($suma_ot_diseno / $count_ot_diseno, 1) : 0;
        $promedio_mes_actual_anio_anterior_catalogacion = ($count_ot_catalogacion > 0) ? round($suma_ot_catalogacion / $count_ot_catalogacion, 1) : 0;
        $promedio_mes_actual_anio_anterior_precatalogacion = ($count_ot_precatalogacion > 0) ? round($suma_ot_precatalogacion / $count_ot_precatalogacion, 1) : 0;


        return array(
            'titulo' => $titulo,
            'promedio_mes_actual_anio_anterior_desarrollo' => $promedio_mes_actual_anio_anterior_desarrollo,
            'promedio_mes_actual_anio_anterior_muestra' => $promedio_mes_actual_anio_anterior_muestra,
            'promedio_mes_actual_anio_anterior_diseno' => $promedio_mes_actual_anio_anterior_diseno,
            'promedio_mes_actual_anio_anterior_catalogacion' => $promedio_mes_actual_anio_anterior_catalogacion,
            'promedio_mes_actual_anio_anterior_precatalogacion' => $promedio_mes_actual_anio_anterior_precatalogacion,
        );
    }

    public function tiempo_promedio_mes_anterior_al_actual_ot($area_id, $vendedor_id, $fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate);

        $mes = $fullDate->format('m');
        $mes_anterior = $fullDate->subMonth('1')->format('m');
        $anio = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');

        $fromDate = $anio . "-" . $mes_anterior . "-01 00:00:00";
        $toDate = date("Y-m-t", strtotime($fromDate)) . ' 23:59:59';

        //Funcion para obtener el nombre del mes en español
        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
        $mes_nombre =  $meses[$mes_anterior - 1];

        $titulo = $mes_nombre . ' ' . $anio_digit;

        $count_ot_desarrollo        = 0;
        $count_ot_muestra           = 0;
        $count_ot_diseno            = 0;
        $count_ot_catalogacion      = 0;
        $count_ot_precatalogacion   = 0;
        $suma_ot_desarrollo         = 0;
        $suma_ot_muestra            = 0;
        $suma_ot_diseno             = 0;
        $suma_ot_catalogacion       = 0;
        $suma_ot_precatalogacion    = 0;

        $reporte_de = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
            ->where('anio', date('Y', strtotime($fromDate)))
            ->where('area', 2)
            ->where('deleted', 0)
            ->first();

        if ($reporte_de) {
            $count_ot_desarrollo    += $reporte_de->count_ot;
            $suma_ot_desarrollo     += $reporte_de->sum_ot;
        } else {

            $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 2, $vendedor_id);
            $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 2);
            $suma_ot_desarrollo += $promedio['suma_ot_trabajados'];
            $count_ot_desarrollo += $promedio['count_ot_trabajados'];

            $reporte_insert             = new ReporteDesm();
            $reporte_insert->mes        = date('m', strtotime($fromDate));
            $reporte_insert->anio       = date('Y', strtotime($fromDate));
            $reporte_insert->area       = 2;
            $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
            $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
            $reporte_insert->deleted    = 0;
            $reporte_insert->save();
        }

        $reporte_dg = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
            ->where('anio', date('Y', strtotime($fromDate)))
            ->where('area', 3)
            ->where('deleted', 0)
            ->first();

        if ($reporte_dg) {
            $count_ot_diseno    += $reporte_dg->count_ot;
            $suma_ot_diseno     += $reporte_dg->sum_ot;
        } else {

            $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 3, $vendedor_id);
            $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 3);
            $suma_ot_diseno += $promedio['suma_ot_trabajados'];
            $count_ot_diseno += $promedio['count_ot_trabajados'];

            $reporte_insert             = new ReporteDesm();
            $reporte_insert->mes        = date('m', strtotime($fromDate));
            $reporte_insert->anio       = date('Y', strtotime($fromDate));
            $reporte_insert->area       = 3;
            $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
            $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
            $reporte_insert->deleted    = 0;
            $reporte_insert->save();
        }

        $reporte_sm = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
            ->where('anio', date('Y', strtotime($fromDate)))
            ->where('area', 6)
            ->where('deleted', 0)
            ->first();
        if ($reporte_sm) {
            $count_ot_muestra    += $reporte_sm->count_ot;
            $suma_ot_muestra     += $reporte_sm->sum_ot;
        } else {

            $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 6, $vendedor_id);
            $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 6);
            $suma_ot_muestra += $promedio['suma_ot_trabajados'];
            $count_ot_muestra += $promedio['count_ot_trabajados'];

            $reporte_insert             = new ReporteDesm();
            $reporte_insert->mes        = date('m', strtotime($fromDate));
            $reporte_insert->anio       = date('Y', strtotime($fromDate));
            $reporte_insert->area       = 6;
            $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
            $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
            $reporte_insert->deleted    = 0;
            $reporte_insert->save();
        }

        $reporte_ca = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
            ->where('anio', date('Y', strtotime($fromDate)))
            ->where('area', 4)
            ->where('deleted', 0)
            ->first();
        if ($reporte_ca) {
            $count_ot_catalogacion    += $reporte_ca->count_ot;
            $suma_ot_catalogacion     += $reporte_ca->sum_ot;
        } else {

            $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 4, $vendedor_id);
            $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 4);
            $suma_ot_catalogacion += $promedio['suma_ot_trabajados'];
            $count_ot_catalogacion += $promedio['count_ot_trabajados'];

            $reporte_insert             = new ReporteDesm();
            $reporte_insert->mes        = date('m', strtotime($fromDate));
            $reporte_insert->anio       = date('Y', strtotime($fromDate));
            $reporte_insert->area       = 5;
            $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
            $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
            $reporte_insert->deleted    = 0;
            $reporte_insert->save();
        }

        $reporte_pc = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
            ->where('anio', date('Y', strtotime($fromDate)))
            ->where('area', 5)
            ->where('deleted', 0)
            ->first();

        if ($reporte_pc) {
            $count_ot_precatalogacion    += $reporte_pc->count_ot;
            $suma_ot_precatalogacion     += $reporte_pc->sum_ot;
        } else {

            $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 5, $vendedor_id);
            $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 5);
            $suma_ot_precatalogacion += $promedio['suma_ot_trabajados'];
            $count_ot_precatalogacion += $promedio['count_ot_trabajados'];

            $reporte_insert             = new ReporteDesm();
            $reporte_insert->mes        = date('m', strtotime($fromDate));
            $reporte_insert->anio       = date('Y', strtotime($fromDate));
            $reporte_insert->area       = 5;
            $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
            $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
            $reporte_insert->deleted    = 0;
            $reporte_insert->save();
        }

        /*
        $ot_promedio_mes_anterior_al_actual = self::query_tiempos_area($fromDate,$toDate,$area_id,$vendedor_id);

        $promedio = self::calcula_el_promedio($ot_promedio_mes_anterior_al_actual,$fromDate, $toDate,$vendedor_id,1);
        $promedio_mes_anterior_al_actual_desarrollo = $promedio['promedio_dias_trabajados_desarrollo'];
        $promedio_mes_anterior_al_actual_muestra = $promedio['promedio_dias_trabajados_muestra'];
        $promedio_mes_anterior_al_actual_diseno = $promedio['promedio_dias_trabajados_diseno'];
        $promedio_mes_anterior_al_actual_catalogacion = $promedio['promedio_dias_trabajados_catalogacion'];
        $promedio_mes_anterior_al_actual_precatalogacion = $promedio['promedio_dias_trabajados_precatalogacion'];*/

        $promedio_mes_anterior_al_actual_desarrollo = ($count_ot_desarrollo > 0) ? round($suma_ot_desarrollo / $count_ot_desarrollo, 1) : 0;
        $promedio_mes_anterior_al_actual_muestra = ($count_ot_muestra > 0) ? round($suma_ot_muestra / $count_ot_muestra, 1) : 0;
        $promedio_mes_anterior_al_actual_diseno = ($count_ot_diseno > 0) ? round($suma_ot_diseno / $count_ot_diseno, 1) : 0;
        $promedio_mes_anterior_al_actual_catalogacion = ($count_ot_catalogacion > 0) ? round($suma_ot_catalogacion / $count_ot_catalogacion, 1) : 0;
        $promedio_mes_anterior_al_actual_precatalogacion = ($count_ot_precatalogacion > 0) ? round($suma_ot_precatalogacion / $count_ot_precatalogacion, 1) : 0;


        return array(
            'titulo' => $titulo,
            'promedio_mes_anterior_al_actual_desarrollo' => $promedio_mes_anterior_al_actual_desarrollo,
            'promedio_mes_anterior_al_actual_muestra' => $promedio_mes_anterior_al_actual_muestra,
            'promedio_mes_anterior_al_actual_diseno' => $promedio_mes_anterior_al_actual_diseno,
            'promedio_mes_anterior_al_actual_catalogacion' => $promedio_mes_anterior_al_actual_catalogacion,
            'promedio_mes_anterior_al_actual_precatalogacion' => $promedio_mes_anterior_al_actual_precatalogacion,
        );
    }

    public function tiempo_promedio_mes_actual_ot($area_id, $vendedor_id, $fromDate, $toDate)
    {

        $fullDate = Carbon::instance($fromDate);

        $mes = $fullDate->format('m');
        $anio = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $dia = $fromDate->format('t');

        $fromDate = $anio . "-" . $mes . "-01 00:00:00";
        $toDate = $anio . "-" . $mes . "-" . $dia . " 23:59:59";
        $current_date = Carbon::now();

        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");

        $mes_nombre =  $meses[$mes - 1];
        $titulo = $mes_nombre . ' ' . $anio_digit;

        if ($current_date >= $fromDate && $current_date <= $toDate) {

            $ot_promedio_mes_actual = self::query_tiempos_area($fromDate, $toDate, $area_id, $vendedor_id);
            $promedio = self::calcula_el_promedio_mes_actual($ot_promedio_mes_actual, $fromDate, $toDate, $vendedor_id, 1);
            $promedio_mes_actual_desarrollo = $promedio['promedio_dias_trabajados_desarrollo'];
            $promedio_mes_actual_muestra = $promedio['promedio_dias_trabajados_muestra'];
            $promedio_mes_actual_diseno = $promedio['promedio_dias_trabajados_diseno'];
            $promedio_mes_actual_catalogacion = $promedio['promedio_dias_trabajados_catalogacion'];
            $promedio_mes_actual_precatalogacion = $promedio['promedio_dias_trabajados_precatalogacion'];

            $count_dias_trabajados_desarrollo = $promedio['count_dias_trabajados_desarrollo'];
            $count_dias_trabajados_muestra = $promedio['count_dias_trabajados_muestra'];
            $count_dias_trabajados_diseno = $promedio['count_dias_trabajados_diseno'];
            $count_dias_trabajados_catalogacion = $promedio['count_dias_trabajados_catalogacion'];
            $count_dias_trabajados_precatalogacion = $promedio['count_dias_trabajados_precatalogacion'];

            $suma_dias_trabajados_desarrollo = $promedio['suma_dias_trabajados_desarrollo'];
            $suma_dias_trabajados_muestra = $promedio['suma_dias_trabajados_muestra'];
            $suma_dias_trabajados_diseno = $promedio['suma_dias_trabajados_diseno'];
            $suma_dias_trabajados_catalogacion = $promedio['suma_dias_trabajados_catalogacion'];
            $suma_dias_trabajados_precatalogacion = $promedio['suma_dias_trabajados_precatalogacion'];
        } else {

            $count_ot_desarrollo        = 0;
            $count_ot_muestra           = 0;
            $count_ot_diseno            = 0;
            $count_ot_catalogacion      = 0;
            $count_ot_precatalogacion   = 0;
            $suma_ot_desarrollo         = 0;
            $suma_ot_muestra            = 0;
            $suma_ot_diseno             = 0;
            $suma_ot_catalogacion       = 0;
            $suma_ot_precatalogacion    = 0;

            $reporte_de = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
                ->where('anio', date('Y', strtotime($fromDate)))
                ->where('area', 2)
                ->where('deleted', 0)
                ->first();

            if ($reporte_de) {
                $count_ot_desarrollo    += $reporte_de->count_ot;
                $suma_ot_desarrollo     += $reporte_de->sum_ot;
            } else {

                $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 2, $vendedor_id);
                $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 2);
                $suma_ot_desarrollo += $promedio['suma_ot_trabajados'];
                $count_ot_desarrollo += $promedio['count_ot_trabajados'];

                $reporte_insert             = new ReporteDesm();
                $reporte_insert->mes        = date('m', strtotime($fromDate));
                $reporte_insert->anio       = date('Y', strtotime($fromDate));
                $reporte_insert->area       = 2;
                $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                $reporte_insert->deleted    = 0;
                $reporte_insert->save();
            }

            $reporte_dg = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
                ->where('anio', date('Y', strtotime($fromDate)))
                ->where('area', 3)
                ->where('deleted', 0)
                ->first();

            if ($reporte_dg) {
                $count_ot_diseno    += $reporte_dg->count_ot;
                $suma_ot_diseno     += $reporte_dg->sum_ot;
            } else {

                $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 3, $vendedor_id);
                $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 3);
                $suma_ot_diseno += $promedio['suma_ot_trabajados'];
                $count_ot_diseno += $promedio['count_ot_trabajados'];

                $reporte_insert             = new ReporteDesm();
                $reporte_insert->mes        = date('m', strtotime($fromDate));
                $reporte_insert->anio       = date('Y', strtotime($fromDate));
                $reporte_insert->area       = 3;
                $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                $reporte_insert->deleted    = 0;
                $reporte_insert->save();
            }

            $reporte_sm = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
                ->where('anio', date('Y', strtotime($fromDate)))
                ->where('area', 6)
                ->where('deleted', 0)
                ->first();
            if ($reporte_sm) {
                $count_ot_muestra    += $reporte_sm->count_ot;
                $suma_ot_muestra     += $reporte_sm->sum_ot;
            } else {

                $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 6, $vendedor_id);
                $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 6);
                $suma_ot_muestra += $promedio['suma_ot_trabajados'];
                $count_ot_muestra += $promedio['count_ot_trabajados'];

                $reporte_insert             = new ReporteDesm();
                $reporte_insert->mes        = date('m', strtotime($fromDate));
                $reporte_insert->anio       = date('Y', strtotime($fromDate));
                $reporte_insert->area       = 6;
                $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                $reporte_insert->deleted    = 0;
                $reporte_insert->save();
            }

            $reporte_ca = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
                ->where('anio', date('Y', strtotime($fromDate)))
                ->where('area', 4)
                ->where('deleted', 0)
                ->first();
            if ($reporte_ca) {
                $count_ot_catalogacion    += $reporte_ca->count_ot;
                $suma_ot_catalogacion     += $reporte_ca->sum_ot;
            } else {

                $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 4, $vendedor_id);
                $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 4);
                $suma_ot_catalogacion += $promedio['suma_ot_trabajados'];
                $count_ot_catalogacion += $promedio['count_ot_trabajados'];

                $reporte_insert             = new ReporteDesm();
                $reporte_insert->mes        = date('m', strtotime($fromDate));
                $reporte_insert->anio       = date('Y', strtotime($fromDate));
                $reporte_insert->area       = 5;
                $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                $reporte_insert->deleted    = 0;
                $reporte_insert->save();
            }

            $reporte_pc = ReporteDesm::where('mes', date('m', strtotime($fromDate)))
                ->where('anio', date('Y', strtotime($fromDate)))
                ->where('area', 5)
                ->where('deleted', 0)
                ->first();

            if ($reporte_pc) {
                $count_ot_precatalogacion    += $reporte_pc->count_ot;
                $suma_ot_precatalogacion     += $reporte_pc->sum_ot;
            } else {

                $ot_promedio_anio_actual = self::query_tiempos_area_aux($fromDate, $toDate, 5, $vendedor_id);
                $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fromDate, $toDate, $vendedor_id, 5);
                $suma_ot_precatalogacion += $promedio['suma_ot_trabajados'];
                $count_ot_precatalogacion += $promedio['count_ot_trabajados'];

                $reporte_insert             = new ReporteDesm();
                $reporte_insert->mes        = date('m', strtotime($fromDate));
                $reporte_insert->anio       = date('Y', strtotime($fromDate));
                $reporte_insert->area       = 5;
                $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                $reporte_insert->deleted    = 0;
                $reporte_insert->save();
            }

            $promedio_mes_actual_desarrollo = ($count_ot_desarrollo > 0) ? round($suma_ot_desarrollo / $count_ot_desarrollo, 1) : 0;
            $promedio_mes_actual_muestra = ($count_ot_muestra > 0) ? round($suma_ot_muestra / $count_ot_muestra, 1) : 0;
            $promedio_mes_actual_diseno = ($count_ot_diseno > 0) ? round($suma_ot_diseno / $count_ot_diseno, 1) : 0;
            $promedio_mes_actual_catalogacion = ($count_ot_catalogacion > 0) ? round($suma_ot_catalogacion / $count_ot_catalogacion, 1) : 0;
            $promedio_mes_actual_precatalogacion = ($count_ot_precatalogacion > 0) ? round($suma_ot_precatalogacion / $count_ot_precatalogacion, 1) : 0;

            $count_dias_trabajados_desarrollo = $count_ot_desarrollo;
            $count_dias_trabajados_muestra = $count_ot_muestra;
            $count_dias_trabajados_diseno = $count_ot_diseno;
            $count_dias_trabajados_catalogacion = $count_ot_catalogacion;
            $count_dias_trabajados_precatalogacion = $count_ot_precatalogacion;

            $suma_dias_trabajados_desarrollo = $suma_ot_desarrollo;
            $suma_dias_trabajados_muestra = $suma_ot_muestra;
            $suma_dias_trabajados_diseno = $suma_ot_diseno;
            $suma_dias_trabajados_catalogacion = $suma_ot_catalogacion;
            $suma_dias_trabajados_precatalogacion = $suma_ot_precatalogacion;
        }

        return array(
            'titulo' => $titulo,
            'promedio_mes_actual_desarrollo' => $promedio_mes_actual_desarrollo,
            'promedio_mes_actual_muestra' => $promedio_mes_actual_muestra,
            'promedio_mes_actual_diseno' => $promedio_mes_actual_diseno,
            'promedio_mes_actual_catalogacion' => $promedio_mes_actual_catalogacion,
            'promedio_mes_actual_precatalogacion' => $promedio_mes_actual_precatalogacion,
            'count_dias_trabajados_desarrollo' => $count_dias_trabajados_desarrollo,
            'count_dias_trabajados_muestra' => $count_dias_trabajados_muestra,
            'count_dias_trabajados_diseno' => $count_dias_trabajados_diseno,
            'count_dias_trabajados_catalogacion' => $count_dias_trabajados_catalogacion,
            'count_dias_trabajados_precatalogacion' => $count_dias_trabajados_precatalogacion,
            'suma_dias_trabajados_desarrollo' => $suma_dias_trabajados_desarrollo,
            'suma_dias_trabajados_muestra' => $suma_dias_trabajados_muestra,
            'suma_dias_trabajados_diseno' => $suma_dias_trabajados_diseno,
            'suma_dias_trabajados_catalogacion' => $suma_dias_trabajados_catalogacion,
            'suma_dias_trabajados_precatalogacion' => $suma_dias_trabajados_precatalogacion,
        );
    }

    public function tiempo_promedio_anio_actual_ot($area_id, $vendedor_id, $fromDate, $toDate, $count_mes_actual_desarrollo, $count_mes_actual_muestra, $count_mes_actual_diseno, $count_mes_actual_catalogacion, $count_mes_actual_precatalogacion, $suma_mes_actual_desarrollo, $suma_mes_actual_muestra, $suma_mes_actual_diseno, $suma_mes_actual_catalogacion, $suma_mes_actual_precatalogacion)
    {

        //dd($count_mes_actual_desarrollo,$count_mes_actual_muestra,$count_mes_actual_diseno,$count_mes_actual_catalogacion,$count_mes_actual_precatalogacion,$suma_mes_actual_desarrollo,$suma_mes_actual_muestra,$suma_mes_actual_diseno,$suma_mes_actual_catalogacion,$suma_mes_actual_precatalogacion);
        $fullDate = Carbon::instance($fromDate);

        $mes = $fullDate->format("m");
        $anio = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $dia = Carbon::instance($toDate)->format('d');
        $current_date = Carbon::now();

        //$calcular=false;
        $count_ot_anio_actual_desarrollo        = 0;
        $count_ot_anio_actual_muestra           = 0;
        $count_ot_anio_actual_diseno            = 0;
        $count_ot_anio_actual_catalogacion      = 0;
        $count_ot_anio_actual_precatalogacion   = 0;
        $suma_ot_anio_actual_desarrollo         = 0;
        $suma_ot_anio_actual_muestra            = 0;
        $suma_ot_anio_actual_diseno             = 0;
        $suma_ot_anio_actual_catalogacion       = 0;
        $suma_ot_anio_actual_precatalogacion    = 0;

        $fromDate = $anio . "-01-01 00:00:00";
        $toDate = $anio . "-" . $mes . "-" . $dia . " 23:59:59";
        //$toDate = "2023-12-31 23:59:59";
        //dd($fromDate,$toDate);
        $fecha_aux_ini = $fromDate;
        $titulo = 'Prom' . ' ' . $anio_digit;

        for ($i = 0; $i <= 12; $i++) {

            $fecha_aux_fin = Carbon::createFromFormat('Y-m-d', date('Y', strtotime($fecha_aux_ini)) . '-' . date('m', strtotime($fecha_aux_ini)) . '-' . '1')->endOfMonth();
            $fecha_aux_ini = Carbon::createFromFormat('Y-m-d', date('Y', strtotime($fecha_aux_ini)) . '-' . date('m', strtotime($fecha_aux_ini)) . '-' . '1')->startOfMonth();

            if ($toDate == $fecha_aux_fin) {
                break;
            } else {



                /*var_dump($fecha_aux_fin);
            var_dump('<br>');
            var_dump($fecha_aux_ini);
            var_dump('<br>');*/

                //Area Diseño Estructural
                $reporte_de = ReporteDesm::where('mes', date('m', strtotime($fecha_aux_ini)))
                    ->where('anio', date('Y', strtotime($fecha_aux_ini)))
                    ->where('area', 2)
                    ->where('deleted', 0)
                    ->first();
                if ($reporte_de) {
                    $count_ot_anio_actual_desarrollo    += $reporte_de->count_ot;
                    $suma_ot_anio_actual_desarrollo     += $reporte_de->sum_ot;
                } else {
                    if ($fecha_aux_fin < $current_date) {
                        $ot_promedio_anio_actual = self::query_tiempos_area_aux($fecha_aux_ini, $fecha_aux_fin, 2, $vendedor_id);
                        $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fecha_aux_ini, $fecha_aux_fin, $vendedor_id, 2);
                        $suma_ot_anio_actual_desarrollo += $promedio['suma_ot_trabajados'];
                        $count_ot_anio_actual_desarrollo += $promedio['count_ot_trabajados'];

                        $reporte_insert             = new ReporteDesm();
                        $reporte_insert->mes        = date('m', strtotime($fecha_aux_ini));
                        $reporte_insert->anio       = date('Y', strtotime($fecha_aux_ini));
                        $reporte_insert->area       = 2;
                        $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                        $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                        $reporte_insert->deleted    = 0;
                        $reporte_insert->save();
                    }
                }

                //Area de Diseño Grafico
                $reporte_dg = ReporteDesm::where('mes', date('m', strtotime($fecha_aux_ini)))
                    ->where('anio', date('Y', strtotime($fecha_aux_ini)))
                    ->where('area', 3)
                    ->where('deleted', 0)
                    ->first();
                if ($reporte_dg) {
                    $count_ot_anio_actual_diseno    += $reporte_dg->count_ot;
                    $suma_ot_anio_actual_diseno     += $reporte_dg->sum_ot;
                } else {

                    if ($fecha_aux_fin < $current_date) {

                        $ot_promedio_anio_actual = self::query_tiempos_area_aux($fecha_aux_ini, $fecha_aux_fin, 3, $vendedor_id);
                        $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fecha_aux_ini, $fecha_aux_fin, $vendedor_id, 3);
                        $suma_ot_anio_actual_diseno     += $promedio['suma_ot_trabajados'];
                        $count_ot_anio_actual_diseno    += $promedio['count_ot_trabajados'];

                        $reporte_insert             = new ReporteDesm();
                        $reporte_insert->mes        = date('m', strtotime($fecha_aux_ini));
                        $reporte_insert->anio       = date('Y', strtotime($fecha_aux_ini));
                        $reporte_insert->area       = 3;
                        $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                        $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                        $reporte_insert->deleted    = 0;
                        $reporte_insert->save();
                    }
                }
                //Area de Sala de Muestra
                $reporte_sm = ReporteDesm::where('mes', date('m', strtotime($fecha_aux_ini)))
                    ->where('anio', date('Y', strtotime($fecha_aux_ini)))
                    ->where('area', 6)
                    ->where('deleted', 0)
                    ->first();
                if ($reporte_sm) {
                    $count_ot_anio_actual_muestra    += $reporte_sm->count_ot;
                    $suma_ot_anio_actual_muestra     += $reporte_sm->sum_ot;
                } else {
                    if ($fecha_aux_fin < $current_date) {
                        $ot_promedio_anio_actual = self::query_tiempos_area_aux($fecha_aux_ini, $fecha_aux_fin, 6, $vendedor_id);
                        $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fecha_aux_ini, $fecha_aux_fin, $vendedor_id, 6);
                        $suma_ot_anio_actual_muestra     += $promedio['suma_ot_trabajados'];
                        $count_ot_anio_actual_muestra    += $promedio['count_ot_trabajados'];

                        $reporte_insert             = new ReporteDesm();
                        $reporte_insert->mes        = date('m', strtotime($fecha_aux_ini));
                        $reporte_insert->anio       = date('Y', strtotime($fecha_aux_ini));
                        $reporte_insert->area       = 6;
                        $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                        $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                        $reporte_insert->deleted    = 0;
                        $reporte_insert->save();
                    }
                }
                //Area de Pre-Catalogacion
                $reporte_pc = ReporteDesm::where('mes', date('m', strtotime($fecha_aux_ini)))
                    ->where('anio', date('Y', strtotime($fecha_aux_ini)))
                    ->where('area', 5)
                    ->where('deleted', 0)
                    ->first();
                if ($reporte_pc) {
                    $count_ot_anio_actual_precatalogacion    += $reporte_pc->count_ot;
                    $suma_ot_anio_actual_precatalogacion     += $reporte_pc->sum_ot;
                } else {
                    if ($fecha_aux_fin < $current_date) {
                        $ot_promedio_anio_actual = self::query_tiempos_area_aux($fecha_aux_ini, $fecha_aux_fin, 5, $vendedor_id);
                        $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fecha_aux_ini, $fecha_aux_fin, $vendedor_id, 5);
                        $suma_ot_anio_actual_precatalogacion     += $promedio['suma_ot_trabajados'];
                        $count_ot_anio_actual_precatalogacion    += $promedio['count_ot_trabajados'];

                        $reporte_insert             = new ReporteDesm();
                        $reporte_insert->mes        = date('m', strtotime($fecha_aux_ini));
                        $reporte_insert->anio       = date('Y', strtotime($fecha_aux_ini));
                        $reporte_insert->area       = 5;
                        $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                        $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                        $reporte_insert->deleted    = 0;
                        $reporte_insert->save();
                    }
                }

                //Area de Catalogacion
                $reporte_ca = ReporteDesm::where('mes', date('m', strtotime($fecha_aux_ini)))
                    ->where('anio', date('Y', strtotime($fecha_aux_ini)))
                    ->where('area', 4)
                    ->where('deleted', 0)
                    ->first();
                if ($reporte_ca) {
                    $count_ot_anio_actual_catalogacion    += $reporte_ca->count_ot;
                    $suma_ot_anio_actual_catalogacion     += $reporte_ca->sum_ot;
                } else {
                    if ($fecha_aux_fin < $current_date) {
                        $ot_promedio_anio_actual = self::query_tiempos_area_aux($fecha_aux_ini, $fecha_aux_fin, 4, $vendedor_id);
                        $promedio = self::calcula_el_promedio_anio_actual_aux($ot_promedio_anio_actual, $fecha_aux_ini, $fecha_aux_fin, $vendedor_id, 4);
                        $suma_ot_anio_actual_catalogacion     += $promedio['suma_ot_trabajados'];
                        $count_ot_anio_actual_catalogacion    += $promedio['count_ot_trabajados'];

                        $reporte_insert             = new ReporteDesm();
                        $reporte_insert->mes        = date('m', strtotime($fecha_aux_ini));
                        $reporte_insert->anio       = date('Y', strtotime($fecha_aux_ini));
                        $reporte_insert->area       = 4;
                        $reporte_insert->count_ot   = $promedio['count_ot_trabajados'];
                        $reporte_insert->sum_ot     = $promedio['suma_ot_trabajados'];
                        $reporte_insert->deleted    = 0;
                        $reporte_insert->save();
                    }
                }
            }

            /* var_dump("<br>");
                var_dump($fecha_aux_ini);
                var_dump("<br>");
                var_dump($fecha_aux_fin);
                var_dump("<br>");
                $promedio_anio_actual_desarrollo++;
                $promedio_anio_actual_muestra++;
                $promedio_anio_actual_diseno++;
                $promedio_anio_actual_catalogacion++;
                $promedio_anio_actual_precatalogacion++;*/

            /*$ot_promedio_anio_actual = self::query_tiempos_area($fecha_aux_ini,$fecha_aux_fin,$area_id,$vendedor_id);

                $promedio = self::calcula_el_promedio_anio_actual($ot_promedio_anio_actual,$fecha_aux_ini, $fecha_aux_fin,$vendedor_id,2);

                $count_ot_anio_actual_desarrollo += $promedio['count_ot_trabajados_desarrollo'];
                $count_ot_anio_actual_muestra += $promedio['count_ot_trabajados_muestra'];
                $count_ot_anio_actual_diseno += $promedio['count_ot_trabajados_diseno'];
                $count_ot_anio_actual_catalogacion += $promedio['count_ot_trabajados_catalogacion'];
                $count_ot_anio_actual_precatalogacion += $promedio['count_ot_trabajados_precatalogacion'];

                $suma_ot_anio_actual_desarrollo += $promedio['suma_ot_trabajados_desarrollo'];
                $suma_ot_anio_actual_muestra += $promedio['suma_ot_trabajados_muestra'];
                $suma_ot_anio_actual_diseno += $promedio['suma_ot_trabajados_diseno'];
                $suma_ot_anio_actual_catalogacion += $promedio['suma_ot_trabajados_catalogacion'];
                $suma_ot_anio_actual_precatalogacion += $promedio['suma_ot_trabajados_precatalogacion'];*/


            $fecha_aux_ini = date("Y-m-d H:i:s", strtotime($fecha_aux_ini . "+ 1 month"));
            //$fecha_aux_fin=Carbon::createFromFormat('Y-m-d', date('Y', strtotime($fecha_aux_ini)) . '-' . date('m', strtotime($fecha_aux_ini)) . '-' . '1')->endOfMonth();
            /*var_dump($fecha_aux_ini);
            var_dump('<br>');
            var_dump($fecha_aux_fin);
            var_dump('<br>');
            var_dump($toDate);
            var_dump('<br>');
            var_dump($toDate==$fecha_aux_fin);
            var_dump('<br>')*/
        }

        $count_ot_anio_actual_desarrollo += $count_mes_actual_desarrollo;
        $count_ot_anio_actual_muestra += $count_mes_actual_muestra;
        $count_ot_anio_actual_diseno += $count_mes_actual_diseno;
        $count_ot_anio_actual_catalogacion += $count_mes_actual_catalogacion;
        $count_ot_anio_actual_precatalogacion += $count_mes_actual_precatalogacion;

        $suma_ot_anio_actual_desarrollo += $suma_mes_actual_desarrollo;
        $suma_ot_anio_actual_muestra += $suma_mes_actual_muestra;
        $suma_ot_anio_actual_diseno += $suma_mes_actual_diseno;
        $suma_ot_anio_actual_catalogacion += $suma_mes_actual_catalogacion;
        $suma_ot_anio_actual_precatalogacion += $suma_mes_actual_precatalogacion;

        $promedio_anio_actual_desarrollo = ($count_ot_anio_actual_desarrollo > 0) ? round($suma_ot_anio_actual_desarrollo / $count_ot_anio_actual_desarrollo, 1) : 0;
        $promedio_anio_actual_muestra = ($count_ot_anio_actual_muestra > 0) ? round($suma_ot_anio_actual_muestra / $count_ot_anio_actual_muestra, 1) : 0;
        $promedio_anio_actual_diseno = ($count_ot_anio_actual_diseno > 0) ? round($suma_ot_anio_actual_diseno / $count_ot_anio_actual_diseno, 1) : 0;
        $promedio_anio_actual_catalogacion = ($count_ot_anio_actual_catalogacion > 0) ? round($suma_ot_anio_actual_catalogacion / $count_ot_anio_actual_catalogacion, 1) : 0;
        $promedio_anio_actual_precatalogacion = ($count_ot_anio_actual_precatalogacion > 0) ? round($suma_ot_anio_actual_precatalogacion / $count_ot_anio_actual_precatalogacion, 1) : 0;

        //dd($fromDate,$toDate,$promedio_anio_actual_desarrollo,$promedio_anio_actual_muestra,
        //  $promedio_anio_actual_diseno,$promedio_anio_actual_catalogacion,$promedio_anio_actual_precatalogacion);


        return array(
            'titulo' => $titulo,
            'promedio_anio_actual_desarrollo' => $promedio_anio_actual_desarrollo,
            'promedio_anio_actual_muestra' => $promedio_anio_actual_muestra,
            'promedio_anio_actual_diseno' => $promedio_anio_actual_diseno,
            'promedio_anio_actual_catalogacion' => $promedio_anio_actual_catalogacion,
            'promedio_anio_actual_precatalogacion' => $promedio_anio_actual_precatalogacion,
        );
    }

    public function query_tiempos_area($fromDate, $toDate, $area_id, $vendedor_id)
    {
        // dd($fromDate, $toDate,$area_id, $vendedor_id);
        //dd($fromDate,$toDate);
        //Obtenemos los direfentes ot con gestiones dureante el periodo consultado
        $ot_periodo = Management::whereBetween('created_at', [$fromDate, $toDate])
            ->where('management_type_id', 1)
            ->groupBy('work_order_id')
            ->pluck('work_order_id')
            ->toArray();

        //Se busca el estado dependiendo del área donde esta la OT ( por eso se reemplaza el id especifico por el relacionado con su area)
        // $area = implode(" ",$area_id);

        $query = WorkOrder::query();
        // Calculo total de tiempo en area de venta
        $query = $query->withCount([
            'gestiones_report AS tiempo_venta' => function ($q) use ($fromDate, $toDate) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('management_type_id', 1)
                    ->where('work_space_id', 1)
                    ->where('created_at', '>=', $fromDate)
                    ->where('created_at', '<=', $toDate);
            }
        ]);
        // Calculo total de tiempo en area de desarrollo
        $query = $query->withCount([
            'gestiones_report AS tiempo_desarrollo' => function ($q) use ($fromDate, $toDate) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('management_type_id', 1)
                    ->where('work_space_id', 2)
                    ->where('created_at', '>=', $fromDate)
                    ->where('created_at', '<=', $toDate);
            }
        ]);
        // Calculo total de tiempo en area de diseño
        $query = $query->withCount([
            'gestiones_report AS tiempo_diseño' => function ($q) use ($fromDate, $toDate) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('management_type_id', 1)
                    ->where('work_space_id', 3)
                    ->where('created_at', '>=', $fromDate)
                    ->where('created_at', '<=', $toDate);
            }
        ]);
        // Calculo total de tiempo en area de catalogacion
        $query = $query->withCount([
            'gestiones_report AS tiempo_catalogacion' => function ($q) use ($fromDate, $toDate) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('management_type_id', 1)
                    ->where('work_space_id', 4)
                    ->where('created_at', '>=', $fromDate)
                    ->where('created_at', '<=', $toDate);
                //->whereBetween('created_at', [$fromDate, $toDate]);

            }
        ]);
        // Calculo total de tiempo en area de precatalogacion
        $query = $query->withCount([
            'gestiones_report AS tiempo_precatalogacion' => function ($q) use ($fromDate, $toDate) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('management_type_id', 1)
                    ->where('work_space_id', 5)
                    ->where('created_at', '>=', $fromDate)
                    ->where('created_at', '<=', $toDate);
            }
        ]);
        // Calculo total de tiempo
        $query = $query->withCount([
            'gestiones_report AS tiempo_total' => function ($q) use ($fromDate, $toDate) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('management_type_id', 1)
                    ->where('created_at', '>=', $fromDate)
                    ->where('created_at', '<=', $toDate);
            }
        ]);
        // Calculo total de tiempo en area sala de muestras
        $query = $query->withCount([
            'gestiones_report AS tiempo_muestra' => function ($q) use ($fromDate, $toDate) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('management_type_id', 1)
                    ->where('work_space_id', 6)
                    ->where('created_at', '>=', $fromDate)
                    ->where('created_at', '<=', $toDate);
            }
        ]);
        $query = $query->whereIN('creador_id', $vendedor_id);



        $ots = $query->whereIN('work_orders.id', $ot_periodo)->get();

        return $ots;
    }

    public function query_tiempos_area_aux($fromDate, $toDate, $area, $vendedor_id)
    {
        // dd($fromDate, $toDate,$area_id, $vendedor_id);
        //dd($fromDate,$toDate);
        //Obtenemos los direfentes ot con gestiones dureante el periodo consultado
        $ot_periodo = Management::whereBetween('created_at', [$fromDate, $toDate])
            ->where('management_type_id', 1)
            ->groupBy('work_order_id')
            ->pluck('work_order_id')
            ->toArray();

        //Se busca el estado dependiendo del área donde esta la OT ( por eso se reemplaza el id especifico por el relacionado con su area)
        // $area = implode(" ",$area_id);

        $query = WorkOrder::query();
        // Calculo total de tiempo en area de venta
        $query = $query->withCount([
            'gestiones_report AS tiempo_duracion' => function ($q) use ($fromDate, $toDate, $area) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('management_type_id', 1)
                    ->where('work_space_id', $area)
                    ->where('created_at', '>=', $fromDate)
                    ->where('created_at', '<=', $toDate);
            }
        ]);
        /*// Calculo total de tiempo en area de desarrollo
        $query = $query->withCount([
            'gestiones_report AS tiempo_desarrollo' => function ($q) use ($fromDate,$toDate) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                        ->where('management_type_id', 1)
                        ->where('work_space_id', 2)
                        ->where('created_at','>=',$fromDate)
                        ->where('created_at','<=',$toDate);

            }
        ]);
        // Calculo total de tiempo en area de diseño
        $query = $query->withCount([
            'gestiones_report AS tiempo_diseño' => function ($q) use ($fromDate,$toDate) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('management_type_id', 1)
                    ->where('work_space_id', 3)
                    ->where('created_at','>=',$fromDate)
                    ->where('created_at','<=',$toDate);

            }
        ]);
        // Calculo total de tiempo en area de catalogacion
        $query = $query->withCount([
            'gestiones_report AS tiempo_catalogacion' => function ($q) use ($fromDate,$toDate) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('management_type_id', 1)
                    ->where('work_space_id', 4)
                    ->where('created_at','>=',$fromDate)
                    ->where('created_at','<=',$toDate);
                    //->whereBetween('created_at', [$fromDate, $toDate]);

            }
        ]);
        // Calculo total de tiempo en area de precatalogacion
        $query = $query->withCount([
            'gestiones_report AS tiempo_precatalogacion' => function ($q) use ($fromDate,$toDate) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('management_type_id', 1)
                    ->where('work_space_id', 5)
                    ->where('created_at','>=',$fromDate)
                    ->where('created_at','<=',$toDate);

            }
        ]);
        // Calculo total de tiempo
        $query = $query->withCount([
            'gestiones_report AS tiempo_total' => function ($q) use ($fromDate,$toDate) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('management_type_id', 1)
                    ->where('created_at','>=',$fromDate)
                    ->where('created_at','<=',$toDate);

            }
        ]);
         // Calculo total de tiempo en area sala de muestras
         $query = $query->withCount([
            'gestiones_report AS tiempo_muestra' => function ($q) use ($fromDate,$toDate) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('management_type_id', 1)
                    ->where('work_space_id', 6)
                    ->where('created_at','>=',$fromDate)
                    ->where('created_at','<=',$toDate);

            }
        ]);*/
        $query = $query->whereIN('creador_id', $vendedor_id);



        $ots = $query->whereIN('work_orders.id', $ot_periodo)->get();

        return $ots;
    }

    public static function conteo_de_elementos($array, $ignore = null)
    {
        $count = 0;
        if ($ignore === null) {
            return count($array);
        }

        foreach ($array as $element) {
            if ($element != $ignore) {
                $count += 1;
            }
        }

        return $count;
    }

    public function calcula_el_promedio($data, $fromDate, $toDate, $vendedorid, $promedio_anio)
    {

        $array_dias_trabajados_desarrollo = []; //2
        $array_dias_trabajados_muestra = []; //6
        $array_dias_trabajados_diseno = []; //3
        $array_dias_trabajados_catalogacion = []; //4
        $array_dias_trabajados_precatalogacion = []; //5

        $ot_ids = $data->pluck('id');



        /*$dic_ultimoCambioEstado_byWorkOrderId=DB::table('managements')
                                                    ->whereIn('work_order_id',$ot_ids)
                                                    ->where('management_type_id', 1)
                                                    ->whereNotNull('state_id')
                                                    //->whereBetween('created_at', [$fromDate, $toDate])
                                                    ->orderBy('created_at','asc')
                                                    ->select('state_id','work_order_id','created_at')
                                                    ->get()
                                                    ->pluck('state_id','work_order_id');*/

        $dic_ultimoCambioEstado_byWorkOrderId   = DB::table('managements')
            ->select(DB::raw('state_id,work_order_id,created_at'))
            ->where('management_type_id', 1)
            //->where('work_order_id',10309 )
            ->whereNotNull('state_id')
            ->where('created_at', '>=', $fromDate)
            //->where('created_at','<=',$toDate)
            ->orderBy('work_order_id', 'asc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->pluck('state_id', 'work_order_id');


        //dd($dic_ultimoCambioEstado_byWorkOrderId);

        //Ultimas gestiones por ot Area Diseño Estructural
        /*$ultima_gestion_de_ot_de   = Management::whereIn('work_order_id',$ot_ids)
                                                ->where('work_space_id',2)
                                                ->where('management_type_id', 1)
                                                ->orderBy('created_at','desc')
                                                ->latest()
                                                ->limit(1)
                                                ->pluck('created_at','work_order_id')
                                                ->toArray();

        //Ultimas gestiones por ot Area Diseño Grafico
        $ultima_gestion_de_ot_dg   = Management::whereIn('work_order_id',$ot_ids)
                                                ->where('work_space_id',3)
                                                ->where('management_type_id', 1)
                                                ->orderBy('created_at','desc')
                                                ->latest()
                                                ->limit(1)
                                                ->pluck('created_at','work_order_id')
                                                ->toArray();
        //Ultimas gestiones por ot Sala de Muestras
        $ultima_gestion_de_ot_sm   = Management::whereIn('work_order_id',$ot_ids)
                                                ->where('work_space_id',6)
                                                ->where('management_type_id', 1)
                                                ->orderBy('created_at','desc')
                                                ->latest()
                                                ->limit(1)
                                                ->pluck('created_at','work_order_id')
                                                ->toArray();

        //Ultimas gestiones por ot Pre-catalogacion
        $ultima_gestion_de_ot_pc   = Management::whereIn('work_order_id',$ot_ids)
                                                ->where('work_space_id',5)
                                                ->where('management_type_id', 1)
                                                ->orderBy('created_at','desc')
                                                ->latest()
                                                ->limit(1)
                                                ->pluck('created_at','work_order_id')
                                                ->toArray();
        //Ultimas gestiones por ot Catalogacion
        $ultima_gestion_de_ot_ct   = Management::whereIn('work_order_id',$ot_ids)
                                                ->where('work_space_id',4)
                                                ->where('management_type_id', 1)
                                                ->orderBy('created_at','desc')
                                                ->latest()
                                                ->limit(1)
                                                ->pluck('created_at','work_order_id')
                                                ->toArray();*/

        //Obtener horarios de trabajo
        $dic_variables = [];
        //Horario Lunes a Jueves
        $horario = SystemVariable::where('name', 'Horario')
            ->where('deleted', 0)
            ->first();
        $horario = explode(',', $horario->contents);
        $dic_variables['ini_time_lun_jue'] = explode(':', $horario[0]);
        $dic_variables['end_time_lun_jue'] = explode(':', $horario[1]);

        //Horario Dia Viernes
        $horario_viernes = SystemVariable::where('name', 'HorarioViernes')
            ->where('deleted', 0)
            ->first();
        $horario_viernes = explode(',', $horario_viernes->contents);
        $dic_variables['ini_time_viernes'] = explode(':', $horario_viernes[0]);
        $dic_variables['end_time_viernes'] = explode(':', $horario_viernes[1]);

        //Dias Feriados
        $feriados = SystemVariable::where('name', 'Feriados')
            ->where('deleted', 0)
            ->first();
        $dic_variables['skipdates'] = explode(',', $feriados->contents);

        $dic_variables['current_date'] = Carbon::now();
        //dd($data);
        //dd($fromDate, $toDate);
        foreach ($data as $ot) {

            //$ot->dias_trabajados = round($ot->present()->diasTrabajadosReportDESM($ot->tiempo_total,$dic_ultimoCambioEstado_byWorkOrderId, $dic_variables), 1);
            //$ot->dias_trabajados_venta = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_venta, 1,$dic_ultimoCambioEstado_byWorkOrderId, $dic_variables), 1);
            $ot->dias_trabajados_desarrollo = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_desarrollo, 2, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, $promedio_anio), 1); //,$ultima_gestion_de_ot_de
            $ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_muestra, 6, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, $promedio_anio), 1); //,$ultima_gestion_de_ot_sm
            $ot->dias_trabajados_diseño = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_diseño, 3, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, $promedio_anio), 1); //,$ultima_gestion_de_ot_dg
            $ot->dias_trabajados_catalogacion = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_catalogacion, 4, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, $promedio_anio), 1); //, $ultima_gestion_de_ot_ct
            $ot->dias_trabajados_precatalogacion = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_precatalogacion, 5, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, $promedio_anio), 1); //, $ultima_gestion_de_ot_pc

            //Se almacenan primero em un arreglo, para despues poder sumarlas
            $array_dias_trabajados_desarrollo[] = $ot->dias_trabajados_desarrollo; //2
            $array_dias_trabajados_muestra[] = $ot->dias_trabajados_muestra; //6
            $array_dias_trabajados_diseno[] = $ot->dias_trabajados_diseño; //3
            $array_dias_trabajados_catalogacion[] = $ot->dias_trabajados_catalogacion; //4
            $array_dias_trabajados_precatalogacion[] = $ot->dias_trabajados_precatalogacion; //5
        }

        //se cuenta cuantos registro hay
        $count_dias_trabajados_desarrollo = self::conteo_de_elementos($array_dias_trabajados_desarrollo, 0);
        $count_dias_trabajados_muestra = self::conteo_de_elementos($array_dias_trabajados_muestra, 0);
        $count_dias_trabajados_diseno = self::conteo_de_elementos($array_dias_trabajados_diseno, 0);
        $count_dias_trabajados_catalogacion = self::conteo_de_elementos($array_dias_trabajados_catalogacion, 0);
        $count_dias_trabajados_precatalogacion = self::conteo_de_elementos($array_dias_trabajados_precatalogacion, 0);

        //sumamos la cantidad de dias
        $suma_dias_trabajados_desarrollo = array_sum($array_dias_trabajados_desarrollo);
        $suma_dias_trabajados_muestra = array_sum($array_dias_trabajados_muestra);
        $suma_dias_trabajados_diseno = array_sum($array_dias_trabajados_diseno);
        $suma_dias_trabajados_catalogacion = array_sum($array_dias_trabajados_catalogacion);
        $suma_dias_trabajados_precatalogacion = array_sum($array_dias_trabajados_precatalogacion);
        //$suma_dias_trabajados_desarrollo_aux =self::actualiza_ot_area_de_actual_de($fromDate, $toDate);
        /*$resp_tiempo_ot_area_actual_desarrollo=self::tiempo_ot_area_actual($fromDate, $toDate, 2, $ot_ids);
        $suma_dias_trabajados_desarrollo+=$resp_tiempo_ot_area_actual_desarrollo['tiempo'];
        $count_dias_trabajados_desarrollo+=$resp_tiempo_ot_area_actual_desarrollo['cantidad'];

        $resp_tiempo_ot_area_actual_diseno=self::tiempo_ot_area_actual($fromDate, $toDate, 3, $ot_ids);
        $suma_dias_trabajados_diseno+=$resp_tiempo_ot_area_actual_diseno['tiempo'];
        $count_dias_trabajados_diseno+=$resp_tiempo_ot_area_actual_diseno['cantidad'];

        $resp_tiempo_ot_area_actual_muestra=self::tiempo_ot_area_actual($fromDate, $toDate, 6, $ot_ids);
        $suma_dias_trabajados_muestra+=$resp_tiempo_ot_area_actual_muestra['tiempo'];
        $count_dias_trabajados_muestra+=$resp_tiempo_ot_area_actual_muestra['cantidad'];

        $resp_tiempo_ot_area_actual_catalogacion=self::tiempo_ot_area_actual($fromDate, $toDate, 4, $ot_ids);
        $suma_dias_trabajados_catalogacion+=$resp_tiempo_ot_area_actual_catalogacion['tiempo'];
        $count_dias_trabajados_catalogacion+=$resp_tiempo_ot_area_actual_catalogacion['cantidad'];

        $resp_tiempo_ot_area_actual_precatalogacion=self::tiempo_ot_area_actual($fromDate, $toDate, 5, $ot_ids);
        $suma_dias_trabajados_precatalogacion+=$resp_tiempo_ot_area_actual_precatalogacion['tiempo'];
        $count_dias_trabajados_precatalogacion+=$resp_tiempo_ot_area_actual_precatalogacion['cantidad'];
        */

        //dd($suma_dias_trabajados_desarrollo,$suma_dias_trabajados_desarrollo_aux,($suma_dias_trabajados_desarrollo+$suma_dias_trabajados_desarrollo_aux),$count_dias_trabajados_desarrollo,$cantidad_ot_area_actual[0],($count_dias_trabajados_desarrollo+$cantidad_ot_area_actual[0]),($suma_dias_trabajados_desarrollo+$suma_dias_trabajados_desarrollo_aux/$count_dias_trabajados_desarrollo+$cantidad_ot_area_actual[0]),round(($suma_dias_trabajados_desarrollo+$suma_dias_trabajados_desarrollo_aux)/($count_dias_trabajados_desarrollo+$cantidad_ot_area_actual[0]) , 1));
        // Se calcula el promedio

        $promedio_dias_trabajados_desarrollo = $count_dias_trabajados_desarrollo ? round($suma_dias_trabajados_desarrollo / $count_dias_trabajados_desarrollo, 1) : 0;
        $promedio_dias_trabajados_muestra = $count_dias_trabajados_muestra ? round($suma_dias_trabajados_muestra / $count_dias_trabajados_muestra, 1) : 0;
        $promedio_dias_trabajados_diseno = $count_dias_trabajados_diseno ? round($suma_dias_trabajados_diseno / $count_dias_trabajados_diseno, 1) : 0;
        $promedio_dias_trabajados_catalogacion = $count_dias_trabajados_catalogacion ? round($suma_dias_trabajados_catalogacion / $count_dias_trabajados_catalogacion, 1) : 0;
        $promedio_dias_trabajados_precatalogacion = $count_dias_trabajados_precatalogacion ? round($suma_dias_trabajados_precatalogacion / $count_dias_trabajados_precatalogacion, 1) : 0;
        // CODIGO MERY

        return array(
            'promedio_dias_trabajados_desarrollo' => $promedio_dias_trabajados_desarrollo,
            'promedio_dias_trabajados_muestra' => $promedio_dias_trabajados_muestra,
            'promedio_dias_trabajados_diseno' => $promedio_dias_trabajados_diseno,
            'promedio_dias_trabajados_catalogacion' => $promedio_dias_trabajados_catalogacion,
            'promedio_dias_trabajados_precatalogacion' => $promedio_dias_trabajados_precatalogacion
        );
    }

    public function calcula_el_promedio_mes_actual($data, $fromDate, $toDate, $vendedorid, $promedio_anio)
    {

        $array_dias_trabajados_desarrollo = []; //2
        $array_dias_trabajados_muestra = []; //6
        $array_dias_trabajados_diseno = []; //3
        $array_dias_trabajados_catalogacion = []; //4
        $array_dias_trabajados_precatalogacion = []; //5

        $ot_ids = $data->pluck('id');



        /*$dic_ultimoCambioEstado_byWorkOrderId=DB::table('managements')
                                                    ->whereIn('work_order_id',$ot_ids)
                                                    ->where('management_type_id', 1)
                                                    ->whereNotNull('state_id')
                                                    //->whereBetween('created_at', [$fromDate, $toDate])
                                                    ->orderBy('created_at','asc')
                                                    ->select('state_id','work_order_id','created_at')
                                                    ->get()
                                                    ->pluck('state_id','work_order_id');*/

        $dic_ultimoCambioEstado_byWorkOrderId   = DB::table('managements')
            ->select(DB::raw('state_id,work_order_id,created_at'))
            ->where('management_type_id', 1)
            //->where('work_order_id',10309 )
            ->whereNotNull('state_id')
            ->where('created_at', '>=', $fromDate)
            //->where('created_at','<=',$toDate)
            ->orderBy('work_order_id', 'asc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->pluck('state_id', 'work_order_id');


        //dd($dic_ultimoCambioEstado_byWorkOrderId);

        //Ultimas gestiones por ot Area Diseño Estructural
        /*$ultima_gestion_de_ot_de   = Management::whereIn('work_order_id',$ot_ids)
                                                ->where('work_space_id',2)
                                                ->where('management_type_id', 1)
                                                ->orderBy('created_at','desc')
                                                ->latest()
                                                ->limit(1)
                                                ->pluck('created_at','work_order_id')
                                                ->toArray();

        //Ultimas gestiones por ot Area Diseño Grafico
        $ultima_gestion_de_ot_dg   = Management::whereIn('work_order_id',$ot_ids)
                                                ->where('work_space_id',3)
                                                ->where('management_type_id', 1)
                                                ->orderBy('created_at','desc')
                                                ->latest()
                                                ->limit(1)
                                                ->pluck('created_at','work_order_id')
                                                ->toArray();
        //Ultimas gestiones por ot Sala de Muestras
        $ultima_gestion_de_ot_sm   = Management::whereIn('work_order_id',$ot_ids)
                                                ->where('work_space_id',6)
                                                ->where('management_type_id', 1)
                                                ->orderBy('created_at','desc')
                                                ->latest()
                                                ->limit(1)
                                                ->pluck('created_at','work_order_id')
                                                ->toArray();

        //Ultimas gestiones por ot Pre-catalogacion
        $ultima_gestion_de_ot_pc   = Management::whereIn('work_order_id',$ot_ids)
                                                ->where('work_space_id',5)
                                                ->where('management_type_id', 1)
                                                ->orderBy('created_at','desc')
                                                ->latest()
                                                ->limit(1)
                                                ->pluck('created_at','work_order_id')
                                                ->toArray();
        //Ultimas gestiones por ot Catalogacion
        $ultima_gestion_de_ot_ct   = Management::whereIn('work_order_id',$ot_ids)
                                                ->where('work_space_id',4)
                                                ->where('management_type_id', 1)
                                                ->orderBy('created_at','desc')
                                                ->latest()
                                                ->limit(1)
                                                ->pluck('created_at','work_order_id')
                                                ->toArray();*/

        //Obtener horarios de trabajo
        $dic_variables = [];
        $dic_variables_muestra = [];
        //Horario Lunes a Jueves
        $horario = SystemVariable::where('name', 'Horario')
            ->where('deleted', 0)
            ->first();
        $horario = explode(',', $horario->contents);
        $dic_variables['ini_time_lun_jue'] = explode(':', $horario[0]);
        $dic_variables['end_time_lun_jue'] = explode(':', $horario[1]);

        //Horario Dia Viernes
        $horario_viernes = SystemVariable::where('name', 'HorarioViernes')
            ->where('deleted', 0)
            ->first();
        $horario_viernes = explode(',', $horario_viernes->contents);
        $dic_variables['ini_time_viernes'] = explode(':', $horario_viernes[0]);
        $dic_variables['end_time_viernes'] = explode(':', $horario_viernes[1]);

        //Horario Lunes a Jueves
        $horarioSalaMuestra = SystemVariable::where('name', 'HorarioSalaMuestras')
            ->where('deleted', 0)
            ->first();
        $horarioSalaMuestra = explode(',', $horarioSalaMuestra->contents);
        $dic_variables_muestra['ini_time_lun_jue'] = explode(':', $horarioSalaMuestra[0]);
        $dic_variables_muestra['end_time_lun_jue'] = explode(':', $horarioSalaMuestra[1]);
        $dic_variables_muestra['ini_time_viernes'] = explode(':', $horarioSalaMuestra[0]);
        $dic_variables_muestra['end_time_viernes'] = explode(':', $horarioSalaMuestra[1]);
        //Horario Dia Viernes


        //Dias Feriados
        $feriados = SystemVariable::where('name', 'Feriados')
            ->where('deleted', 0)
            ->first();
        $dic_variables['skipdates'] = explode(',', $feriados->contents);

        $dic_variables['current_date'] = Carbon::now();

        $dic_variables_muestra['skipdates'] = explode(',', $feriados->contents);

        $dic_variables_muestra['current_date'] = Carbon::now();
        //dd($data);
        foreach ($data as $ot) {

            //$ot->dias_trabajados = round($ot->present()->diasTrabajadosReportDESM($ot->tiempo_total,$dic_ultimoCambioEstado_byWorkOrderId, $dic_variables), 1);
            //$ot->dias_trabajados_venta = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_venta, 1,$dic_ultimoCambioEstado_byWorkOrderId, $dic_variables), 1);
            $ot->dias_trabajados_desarrollo = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_desarrollo, 2, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, $promedio_anio), 1); //,$ultima_gestion_de_ot_de
            $ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_muestra, 6, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables_muestra, $fromDate, $toDate, $promedio_anio), 1); //,$ultima_gestion_de_ot_sm
            $ot->dias_trabajados_diseño = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_diseño, 3, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, $promedio_anio), 1); //,$ultima_gestion_de_ot_dg
            $ot->dias_trabajados_catalogacion = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_catalogacion, 4, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, $promedio_anio), 1); //, $ultima_gestion_de_ot_ct
            $ot->dias_trabajados_precatalogacion = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_precatalogacion, 5, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, $promedio_anio), 1); //, $ultima_gestion_de_ot_pc

            //Se almacenan primero em un arreglo, para despues poder sumarlas
            $array_dias_trabajados_desarrollo[] = $ot->dias_trabajados_desarrollo; //2
            $array_dias_trabajados_muestra[] = $ot->dias_trabajados_muestra; //6
            $array_dias_trabajados_diseno[] = $ot->dias_trabajados_diseño; //3
            $array_dias_trabajados_catalogacion[] = $ot->dias_trabajados_catalogacion; //4
            $array_dias_trabajados_precatalogacion[] = $ot->dias_trabajados_precatalogacion; //5
        }

        //se cuenta cuantos registro hay
        $count_dias_trabajados_desarrollo = self::conteo_de_elementos($array_dias_trabajados_desarrollo, 0);
        $count_dias_trabajados_muestra = self::conteo_de_elementos($array_dias_trabajados_muestra, 0);
        $count_dias_trabajados_diseno = self::conteo_de_elementos($array_dias_trabajados_diseno, 0);
        $count_dias_trabajados_catalogacion = self::conteo_de_elementos($array_dias_trabajados_catalogacion, 0);
        $count_dias_trabajados_precatalogacion = self::conteo_de_elementos($array_dias_trabajados_precatalogacion, 0);

        //sumamos la cantidad de dias
        $suma_dias_trabajados_desarrollo = array_sum($array_dias_trabajados_desarrollo);
        $suma_dias_trabajados_muestra = array_sum($array_dias_trabajados_muestra);
        $suma_dias_trabajados_diseno = array_sum($array_dias_trabajados_diseno);
        $suma_dias_trabajados_catalogacion = array_sum($array_dias_trabajados_catalogacion);
        $suma_dias_trabajados_precatalogacion = array_sum($array_dias_trabajados_precatalogacion);
        //$suma_dias_trabajados_desarrollo_aux =self::actualiza_ot_area_de_actual_de($fromDate, $toDate);
        $resp_tiempo_ot_area_actual_desarrollo = self::tiempo_ot_area_actual($fromDate, $toDate, 2, $ot_ids);
        $suma_dias_trabajados_desarrollo += $resp_tiempo_ot_area_actual_desarrollo['tiempo'];
        $count_dias_trabajados_desarrollo += $resp_tiempo_ot_area_actual_desarrollo['cantidad'];

        $resp_tiempo_ot_area_actual_diseno = self::tiempo_ot_area_actual($fromDate, $toDate, 3, $ot_ids);
        $suma_dias_trabajados_diseno += $resp_tiempo_ot_area_actual_diseno['tiempo'];
        $count_dias_trabajados_diseno += $resp_tiempo_ot_area_actual_diseno['cantidad'];

        $resp_tiempo_ot_area_actual_muestra = self::tiempo_ot_area_actual($fromDate, $toDate, 6, $ot_ids);
        $suma_dias_trabajados_muestra += $resp_tiempo_ot_area_actual_muestra['tiempo'];
        $count_dias_trabajados_muestra += $resp_tiempo_ot_area_actual_muestra['cantidad'];

        $resp_tiempo_ot_area_actual_catalogacion = self::tiempo_ot_area_actual($fromDate, $toDate, 4, $ot_ids);
        $suma_dias_trabajados_catalogacion += $resp_tiempo_ot_area_actual_catalogacion['tiempo'];
        $count_dias_trabajados_catalogacion += $resp_tiempo_ot_area_actual_catalogacion['cantidad'];

        $resp_tiempo_ot_area_actual_precatalogacion = self::tiempo_ot_area_actual($fromDate, $toDate, 5, $ot_ids);
        $suma_dias_trabajados_precatalogacion += $resp_tiempo_ot_area_actual_precatalogacion['tiempo'];
        $count_dias_trabajados_precatalogacion += $resp_tiempo_ot_area_actual_precatalogacion['cantidad'];


        //dd($suma_dias_trabajados_desarrollo,$count_dias_trabajados_desarrollo);
        // Se calcula el promedio

        $promedio_dias_trabajados_desarrollo = $count_dias_trabajados_desarrollo ? round($suma_dias_trabajados_desarrollo / $count_dias_trabajados_desarrollo, 1) : 0;
        $promedio_dias_trabajados_muestra = $count_dias_trabajados_muestra ? round($suma_dias_trabajados_muestra / $count_dias_trabajados_muestra, 1) : 0;
        $promedio_dias_trabajados_diseno = $count_dias_trabajados_diseno ? round($suma_dias_trabajados_diseno / $count_dias_trabajados_diseno, 1) : 0;
        $promedio_dias_trabajados_catalogacion = $count_dias_trabajados_catalogacion ? round($suma_dias_trabajados_catalogacion / $count_dias_trabajados_catalogacion, 1) : 0;
        $promedio_dias_trabajados_precatalogacion = $count_dias_trabajados_precatalogacion ? round($suma_dias_trabajados_precatalogacion / $count_dias_trabajados_precatalogacion, 1) : 0;
        // CODIGO MERY
        //dd($suma_dias_trabajados_desarrollo,$count_dias_trabajados_desarrollo);
        return array(
            'promedio_dias_trabajados_desarrollo' => $promedio_dias_trabajados_desarrollo,
            'promedio_dias_trabajados_muestra' => $promedio_dias_trabajados_muestra,
            'promedio_dias_trabajados_diseno' => $promedio_dias_trabajados_diseno,
            'promedio_dias_trabajados_catalogacion' => $promedio_dias_trabajados_catalogacion,
            'promedio_dias_trabajados_precatalogacion' => $promedio_dias_trabajados_precatalogacion,
            'count_dias_trabajados_desarrollo' => $count_dias_trabajados_desarrollo,
            'count_dias_trabajados_muestra' => $count_dias_trabajados_muestra,
            'count_dias_trabajados_diseno' => $count_dias_trabajados_diseno,
            'count_dias_trabajados_catalogacion' => $count_dias_trabajados_catalogacion,
            'count_dias_trabajados_precatalogacion' => $count_dias_trabajados_precatalogacion,
            'suma_dias_trabajados_desarrollo' => $suma_dias_trabajados_desarrollo,
            'suma_dias_trabajados_muestra' => $suma_dias_trabajados_muestra,
            'suma_dias_trabajados_diseno' => $suma_dias_trabajados_diseno,
            'suma_dias_trabajados_catalogacion' => $suma_dias_trabajados_catalogacion,
            'suma_dias_trabajados_precatalogacion' => $suma_dias_trabajados_precatalogacion
        );
    }

    public function calcula_el_promedio_anio_actual($data, $fromDate, $toDate, $vendedorid, $promedio_anio)
    {

        $array_dias_trabajados_desarrollo = []; //2
        $array_dias_trabajados_muestra = []; //6
        $array_dias_trabajados_diseno = []; //3
        $array_dias_trabajados_catalogacion = []; //4
        $array_dias_trabajados_precatalogacion = []; //5

        $ot_ids = $data->pluck('id');

        $dic_ultimoCambioEstado_byWorkOrderId   = DB::table('managements')
            ->select(DB::raw('state_id,work_order_id,created_at'))
            ->where('management_type_id', 1)
            //->where('work_order_id',10309 )
            ->whereNotNull('state_id')
            ->where('created_at', '>=', $fromDate)
            //->where('created_at','<=',$toDate)
            ->orderBy('work_order_id', 'asc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->pluck('state_id', 'work_order_id');


        //Obtener horarios de trabajo
        $dic_variables = [];
        //Horario Lunes a Jueves
        $horario = SystemVariable::where('name', 'Horario')
            ->where('deleted', 0)
            ->first();
        $horario = explode(',', $horario->contents);
        $dic_variables['ini_time_lun_jue'] = explode(':', $horario[0]);
        $dic_variables['end_time_lun_jue'] = explode(':', $horario[1]);

        //Horario Dia Viernes
        $horario_viernes = SystemVariable::where('name', 'HorarioViernes')
            ->where('deleted', 0)
            ->first();
        $horario_viernes = explode(',', $horario_viernes->contents);
        $dic_variables['ini_time_viernes'] = explode(':', $horario_viernes[0]);
        $dic_variables['end_time_viernes'] = explode(':', $horario_viernes[1]);

        //Dias Feriados
        $feriados = SystemVariable::where('name', 'Feriados')
            ->where('deleted', 0)
            ->first();
        $dic_variables['skipdates'] = explode(',', $feriados->contents);

        $dic_variables['current_date'] = Carbon::now();
        //dd($data);
        // dd($fromDate, $toDate);
        foreach ($data as $ot) {

            //$ot->dias_trabajados = round($ot->present()->diasTrabajadosReportDESM($ot->tiempo_total,$dic_ultimoCambioEstado_byWorkOrderId, $dic_variables), 1);
            //$ot->dias_trabajados_venta = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_venta, 1,$dic_ultimoCambioEstado_byWorkOrderId, $dic_variables), 1);
            $ot->dias_trabajados_desarrollo = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_desarrollo, 2, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, $promedio_anio), 1); //,$ultima_gestion_de_ot_de
            $ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_muestra, 6, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, $promedio_anio), 1); //,$ultima_gestion_de_ot_sm
            $ot->dias_trabajados_diseño = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_diseño, 3, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, $promedio_anio), 1); //,$ultima_gestion_de_ot_dg
            $ot->dias_trabajados_catalogacion = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_catalogacion, 4, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, $promedio_anio), 1); //, $ultima_gestion_de_ot_ct
            $ot->dias_trabajados_precatalogacion = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_precatalogacion, 5, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, $promedio_anio), 1); //, $ultima_gestion_de_ot_pc

            //Se almacenan primero em un arreglo, para despues poder sumarlas
            $array_dias_trabajados_desarrollo[] = $ot->dias_trabajados_desarrollo; //2
            $array_dias_trabajados_muestra[] = $ot->dias_trabajados_muestra; //6
            $array_dias_trabajados_diseno[] = $ot->dias_trabajados_diseño; //3
            $array_dias_trabajados_catalogacion[] = $ot->dias_trabajados_catalogacion; //4
            $array_dias_trabajados_precatalogacion[] = $ot->dias_trabajados_precatalogacion; //5
        }

        //se cuenta cuantos registro hay
        $count_ot_trabajados_desarrollo = self::conteo_de_elementos($array_dias_trabajados_desarrollo, 0);
        $count_ot_trabajados_muestra = self::conteo_de_elementos($array_dias_trabajados_muestra, 0);
        $count_ot_trabajados_diseno = self::conteo_de_elementos($array_dias_trabajados_diseno, 0);
        $count_ot_trabajados_catalogacion = self::conteo_de_elementos($array_dias_trabajados_catalogacion, 0);
        $count_ot_trabajados_precatalogacion = self::conteo_de_elementos($array_dias_trabajados_precatalogacion, 0);

        //sumamos la cantidad de dias
        $suma_ot_trabajados_desarrollo = array_sum($array_dias_trabajados_desarrollo);
        $suma_ot_trabajados_muestra = array_sum($array_dias_trabajados_muestra);
        $suma_ot_trabajados_diseno = array_sum($array_dias_trabajados_diseno);
        $suma_ot_trabajados_catalogacion = array_sum($array_dias_trabajados_catalogacion);
        $suma_ot_trabajados_precatalogacion = array_sum($array_dias_trabajados_precatalogacion);

        return array(
            'count_ot_trabajados_desarrollo' => $count_ot_trabajados_desarrollo,
            'count_ot_trabajados_muestra' => $count_ot_trabajados_muestra,
            'count_ot_trabajados_diseno' => $count_ot_trabajados_diseno,
            'count_ot_trabajados_catalogacion' => $count_ot_trabajados_catalogacion,
            'count_ot_trabajados_precatalogacion' => $count_ot_trabajados_precatalogacion,
            'suma_ot_trabajados_desarrollo' => $suma_ot_trabajados_desarrollo,
            'suma_ot_trabajados_muestra' => $suma_ot_trabajados_muestra,
            'suma_ot_trabajados_diseno' => $suma_ot_trabajados_diseno,
            'suma_ot_trabajados_catalogacion' => $suma_ot_trabajados_catalogacion,
            'suma_ot_trabajados_precatalogacion' => $suma_ot_trabajados_precatalogacion
        );
    }

    public function calcula_el_promedio_anio_actual_aux($data, $fromDate, $toDate, $vendedorid, $area)
    {

        $array_dias_trabajados = []; //2


        $ot_ids = $data->pluck('id');

        $dic_ultimoCambioEstado_byWorkOrderId   = DB::table('managements')
            ->select(DB::raw('state_id,work_order_id,created_at'))
            ->where('management_type_id', 1)
            //->where('work_order_id',10309 )
            ->whereNotNull('state_id')
            ->where('created_at', '>=', $fromDate)
            //->where('created_at','<=',$toDate)
            ->orderBy('work_order_id', 'asc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->pluck('state_id', 'work_order_id');


        //Obtener horarios de trabajo
        $dic_variables = [];
        //Horario Lunes a Jueves
        $horario = SystemVariable::where('name', 'Horario')
            ->where('deleted', 0)
            ->first();
        $horario = explode(',', $horario->contents);
        $dic_variables['ini_time_lun_jue'] = explode(':', $horario[0]);
        $dic_variables['end_time_lun_jue'] = explode(':', $horario[1]);

        //Horario Dia Viernes
        $horario_viernes = SystemVariable::where('name', 'HorarioViernes')
            ->where('deleted', 0)
            ->first();
        $horario_viernes = explode(',', $horario_viernes->contents);
        $dic_variables['ini_time_viernes'] = explode(':', $horario_viernes[0]);
        $dic_variables['end_time_viernes'] = explode(':', $horario_viernes[1]);

        //Dias Feriados
        $feriados = SystemVariable::where('name', 'Feriados')
            ->where('deleted', 0)
            ->first();
        $dic_variables['skipdates'] = explode(',', $feriados->contents);

        $dic_variables['current_date'] = Carbon::now();
        //dd($data);
        // dd($fromDate, $toDate);
        foreach ($data as $ot) {

            //$ot->dias_trabajados = round($ot->present()->diasTrabajadosReportDESM($ot->tiempo_total,$dic_ultimoCambioEstado_byWorkOrderId, $dic_variables), 1);
            //$ot->dias_trabajados_venta = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_venta, 1,$dic_ultimoCambioEstado_byWorkOrderId, $dic_variables), 1);
            $ot->dias_trabajados = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_duracion, $area, $dic_ultimoCambioEstado_byWorkOrderId, $dic_variables, $fromDate, $toDate, 2), 1); //,$ultima_gestion_de_ot_de
            //$ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_muestra, 6,$dic_ultimoCambioEstado_byWorkOrderId, $dic_variables,$fromDate, $toDate,$promedio_anio), 1);//,$ultima_gestion_de_ot_sm
            //$ot->dias_trabajados_diseño = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_diseño, 3,$dic_ultimoCambioEstado_byWorkOrderId, $dic_variables,$fromDate, $toDate,$promedio_anio), 1);//,$ultima_gestion_de_ot_dg
            //$ot->dias_trabajados_catalogacion = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_catalogacion, 4,$dic_ultimoCambioEstado_byWorkOrderId, $dic_variables,$fromDate, $toDate,$promedio_anio), 1);//, $ultima_gestion_de_ot_ct
            //$ot->dias_trabajados_precatalogacion = round($ot->present()->diasTrabajadosPorAreaReportDESM($ot->tiempo_precatalogacion, 5,$dic_ultimoCambioEstado_byWorkOrderId, $dic_variables,$fromDate, $toDate,$promedio_anio), 1);//, $ultima_gestion_de_ot_pc

            //Se almacenan primero em un arreglo, para despues poder sumarlas
            $array_dias_trabajados[] = $ot->dias_trabajados; //2
            //$array_dias_trabajados_muestra[] = $ot->dias_trabajados_muestra; //6
            //$array_dias_trabajados_diseno[] = $ot->dias_trabajados_diseño; //3
            //$array_dias_trabajados_catalogacion[] = $ot->dias_trabajados_catalogacion; //4
            //$array_dias_trabajados_precatalogacion[] = $ot->dias_trabajados_precatalogacion; //5
        }

        //se cuenta cuantos registro hay
        $count_ot_trabajados = self::conteo_de_elementos($array_dias_trabajados, 0);
        //$count_ot_trabajados_muestra = self::conteo_de_elementos($array_dias_trabajados_muestra, 0);
        //$count_ot_trabajados_diseno = self::conteo_de_elementos($array_dias_trabajados_diseno, 0);
        //$count_ot_trabajados_catalogacion = self::conteo_de_elementos($array_dias_trabajados_catalogacion, 0);
        //$count_ot_trabajados_precatalogacion = self::conteo_de_elementos($array_dias_trabajados_precatalogacion, 0);

        //sumamos la cantidad de dias
        $suma_ot_trabajados = array_sum($array_dias_trabajados);
        //$suma_ot_trabajados_muestra = array_sum($array_dias_trabajados_muestra);
        //$suma_ot_trabajados_diseno = array_sum($array_dias_trabajados_diseno);
        //$suma_ot_trabajados_catalogacion = array_sum($array_dias_trabajados_catalogacion);
        //$suma_ot_trabajados_precatalogacion = array_sum($array_dias_trabajados_precatalogacion);

        return array(
            'count_ot_trabajados' => $count_ot_trabajados,
            'suma_ot_trabajados' => $suma_ot_trabajados
        );
    }

    public function actualiza_registros_managements($fromDate, $toDate, $area, $observacion)
    {

        // Se Obtienen las gestiones del area especifica en el periodo especifico
        $ots_management   = Management::where('work_space_id', $area)
            ->select('work_order_id')
            ->where('management_type_id', 1)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->groupBy('work_order_id')
            ->get();

        // Se recorren cada gestion anterior
        foreach ($ots_management as $ot) {

            $gestiones_ot = Management::where('work_space_id', $area)
                ->where('management_type_id', 1)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->where('work_order_id', $ot->work_order_id)
                //->groupBy('id')
                ->get();

            foreach ($gestiones_ot as $gestion_ot) {

                $gestion_ot_anterior   = Management::where('management_type_id', 1)
                    ->where('created_at', '<', $gestion_ot->created_at)
                    ->where('work_order_id', $ot->work_order_id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($gestion_ot_anterior && $gestion_ot_anterior->mostrar != 0) {

                    $diff_segundos_total = 0;
                    $diferencia         = date_diff($gestion_ot_anterior->created_at, $gestion_ot->created_at);
                    $differenceFormat   = '%a';
                    $diff_days          = $diferencia->format($differenceFormat);
                    $diff_meses         = $diferencia->m;
                    //dd($gestion_ot_anterior->created_at,$gestion_ot->created_at,$diff_meses,$diff_days);
                    if ($diff_days > 30) {

                        $mes_gestion_anterior       = date('m', strtotime($gestion_ot_anterior->created_at));
                        $anio_gestion_anterior      = date('Y', strtotime($gestion_ot_anterior->created_at));
                        $fecha_gestion_anterior_fin = Carbon::createFromFormat('Y-m-d', $anio_gestion_anterior . '-' . $mes_gestion_anterior . '-' . '1')->endOfMonth();
                        $fecha_gestion_anterior_ini = Carbon::createFromFormat('Y-m-d', $anio_gestion_anterior . '-' . $mes_gestion_anterior . '-' . '1')->startOfMonth();
                        if ($area == 6) {
                            $diff_mes_inicio    = get_working_hours_muestra($gestion_ot_anterior->created_at, $fecha_gestion_anterior_fin) * 3600;
                        } else {
                            $diff_mes_inicio    = get_working_hours($gestion_ot_anterior->created_at, $fecha_gestion_anterior_fin) * 3600;
                        }

                        //dd($ot->work_order_id,$diff_meses,$diferencia,$fecha_gestion_anterior_fin);
                        $gestion_anterior_insert                        = new Management();
                        $gestion_anterior_insert->observacion           = $observacion;
                        $gestion_anterior_insert->management_type_id    = 1;
                        $gestion_anterior_insert->user_id               = 1; //Usuario Admin Inveb
                        $gestion_anterior_insert->work_order_id         = $gestion_ot->work_order_id;
                        $gestion_anterior_insert->work_space_id         = $area;
                        $gestion_anterior_insert->duracion_segundos     = $diff_mes_inicio;
                        $gestion_anterior_insert->state_id              = $gestion_ot->state_id;
                        $gestion_anterior_insert->mostrar               = 0;
                        $gestion_anterior_insert->created_at            = $fecha_gestion_anterior_fin;
                        $gestion_anterior_insert->updated_at            = $fecha_gestion_anterior_fin;
                        $gestion_anterior_insert->save();

                        $fecha_aux  = $fecha_gestion_anterior_ini;
                        $diff_segundos_total += $diff_mes_inicio;

                        for ($i = 30; $diff_days > $i; $i += 30) {

                            $fecha_aux = date("Y-m-d", strtotime($fecha_aux . "+ 1 month"));

                            $fecha_aux_ini = Carbon::createFromFormat('Y-m-d', date('Y', strtotime($fecha_aux)) . '-' . date('m', strtotime($fecha_aux)) . '-' . '1')->startOfMonth();
                            $fecha_aux_fin = Carbon::createFromFormat('Y-m-d', date('Y', strtotime($fecha_aux)) . '-' . date('m', strtotime($fecha_aux)) . '-' . '1')->endOfMonth();
                            if ($area == 6) {
                                $diff_mes_aux    = get_working_hours_muestra($fecha_aux_ini, $fecha_aux_fin) * 3600;
                            } else {
                                $diff_mes_aux    = get_working_hours($fecha_aux_ini, $fecha_aux_fin) * 3600;
                            }


                            $gestion_anterior_insert                        = new Management();
                            $gestion_anterior_insert->observacion           = $observacion;
                            $gestion_anterior_insert->management_type_id    = 1;
                            $gestion_anterior_insert->user_id               = 1; //Usuario Admin Inveb
                            $gestion_anterior_insert->work_order_id         = $gestion_ot->work_order_id;
                            $gestion_anterior_insert->work_space_id         = $area;
                            $gestion_anterior_insert->duracion_segundos     = $diff_mes_aux;
                            $gestion_anterior_insert->state_id              = $gestion_ot->state_id;
                            $gestion_anterior_insert->mostrar               = 0;
                            $gestion_anterior_insert->created_at            = $fecha_aux_fin;
                            $gestion_anterior_insert->updated_at            = $fecha_aux_fin;
                            $gestion_anterior_insert->save();

                            $fecha_aux = $fecha_aux_ini;
                            $diff_segundos_total += $diff_mes_aux;
                        }


                        $mes_gestion_actual         = date('m', strtotime($gestion_ot->created_at));
                        $anio_gestion_actual_ini_mes = date('Y', strtotime($gestion_ot->created_at));
                        $fecha_gestion_actual_ini   = Carbon::createFromFormat('Y-m-d', $anio_gestion_actual_ini_mes . '-' . $mes_gestion_actual . '-' . '1')->startOfMonth();
                        if ($area == 6) {
                            $diff_mes_actual    = get_working_hours_muestra($fecha_gestion_actual_ini, $gestion_ot->created_at) * 3600;
                        } else {
                            $diff_mes_actual    = get_working_hours($fecha_gestion_actual_ini, $gestion_ot->created_at) * 3600;
                        }

                        $gestion_ot_actual_update   = Management::where('id', $gestion_ot->id)->update(['duracion_segundos' => $diff_mes_actual]);
                    } else {

                        $mes_gestion_actual     = date('m', strtotime($gestion_ot->created_at));
                        $mes_gestion_anterior   = date('m', strtotime($gestion_ot_anterior->created_at));

                        if ($mes_gestion_actual != $mes_gestion_anterior) {

                            //dd($gestion_ot->id,$gestion_ot->work_order_id,$diff_days,$mes_gestion_actual,$mes_gestion_anterior);
                            $anio_gestion_actual_ini_mes = date('Y', strtotime($gestion_ot->created_at));
                            $fecha_gestion_actual_ini = Carbon::createFromFormat('Y-m-d', $anio_gestion_actual_ini_mes . '-' . $mes_gestion_actual . '-' . '1')->startOfMonth();

                            $anio_gestion_anterior_fin_mes = date('Y', strtotime($gestion_ot_anterior->created_at));
                            $fecha_gestion_anterior_fin = Carbon::createFromFormat('Y-m-d', $anio_gestion_anterior_fin_mes . '-' . $mes_gestion_anterior . '-' . '1')->endOfMonth();
                            if ($area == 6) {
                                $diff_mes_actual    = get_working_hours_muestra($fecha_gestion_actual_ini, $gestion_ot->created_at) * 3600;
                                $diff_mes_anterior  = get_working_hours_muestra($gestion_ot_anterior->created_at, $fecha_gestion_anterior_fin) * 3600;
                            } else {
                                $diff_mes_actual    = get_working_hours($fecha_gestion_actual_ini, $gestion_ot->created_at) * 3600;
                                $diff_mes_anterior  = get_working_hours($gestion_ot_anterior->created_at, $fecha_gestion_anterior_fin) * 3600;
                            }


                            $gestion_ot_actual_update = Management::where('id', $gestion_ot->id)->update(['duracion_segundos' => $diff_mes_actual]);

                            $gestion_anterior_insert                        = new Management();
                            $gestion_anterior_insert->observacion           = $observacion;
                            $gestion_anterior_insert->management_type_id    = 1;
                            $gestion_anterior_insert->user_id               = 1; //Usuario Admin Inveb
                            $gestion_anterior_insert->work_order_id         = $gestion_ot->work_order_id;
                            $gestion_anterior_insert->work_space_id         = $area;
                            $gestion_anterior_insert->duracion_segundos     = $diff_mes_anterior;
                            $gestion_anterior_insert->state_id              = $gestion_ot->state_id;
                            $gestion_anterior_insert->mostrar               = 0;
                            $gestion_anterior_insert->created_at            = $fecha_gestion_anterior_fin;
                            $gestion_anterior_insert->updated_at            = $fecha_gestion_anterior_fin;
                            $gestion_anterior_insert->save();
                        }
                    }
                }
            }
        }
    }

    public function actualiza_registros_ot_activa_area_actual($area, $observacion)
    {

        //$fecha_actual=Carbon::now();
        //Obtener Ot d eun area actual vigentes y que no tengan gestion durante el periodo filtrado
        $ots_de   = WorkOrder::where('current_area_id', $area)
            ->where('active', 1)
            ->get();

        $mes_actual  = date('m', strtotime(Carbon::now()));
        $anio_actual = date('Y', strtotime(Carbon::now()));
        $fecha_ini_mes_actual = Carbon::createFromFormat('Y-m-d', $anio_actual . '-' . $mes_actual . '-' . '1')->startOfMonth();

        //$last_date = ($mes_consulta_actual==$mes_actual)? date('Y-m-d H:i:s'): $toDate;
        $current_date = Carbon::now();

        foreach ($ots_de as $ot) {

            $gestion_ot   = Management::where('work_order_id', $ot->id)
                ->where('management_type_id', 1)
                ->where('created_at', '<', $current_date)
                //->where('created_at', '<',$fromDate)
                ->orderBy('created_at', 'desc')
                ->first();



            if ($gestion_ot &&  !in_array($gestion_ot->state_id, [8, 9, 11, 13]) && $gestion_ot->mostrar != 0) {
                //$ots_def[$i]=$gestiones_de->work_order_id;
                $mes_ultima_gestion = date('m', strtotime($gestion_ot->created_at));
                if ($mes_ultima_gestion != $mes_actual) {

                    $diff_segundos_total = 0;
                    $diferencia         = date_diff($gestion_ot->created_at, $current_date);
                    $differenceFormat   = '%a';
                    $diff_days          = $diferencia->format($differenceFormat);

                    if ($diff_days > 30) {

                        $mes_gestion_anterior       = date('m', strtotime($gestion_ot->created_at));
                        $anio_gestion_anterior      = date('Y', strtotime($gestion_ot->created_at));
                        $fecha_gestion_anterior_fin = Carbon::createFromFormat('Y-m-d', $anio_gestion_anterior . '-' . $mes_gestion_anterior . '-' . '1')->endOfMonth();
                        $fecha_gestion_anterior_ini = Carbon::createFromFormat('Y-m-d', $anio_gestion_anterior . '-' . $mes_gestion_anterior . '-' . '1')->startOfMonth();
                        if ($area == 6) {
                            $diff_mes_inicio    = get_working_hours_muestra($gestion_ot->created_at, $fecha_gestion_anterior_fin) * 3600;
                        } else {
                            $diff_mes_inicio    = get_working_hours($gestion_ot->created_at, $fecha_gestion_anterior_fin) * 3600;
                        }


                        $gestion_anterior_insert                        = new Management();
                        $gestion_anterior_insert->observacion           = $observacion;
                        $gestion_anterior_insert->management_type_id    = 1;
                        $gestion_anterior_insert->user_id               = 1; //Usuario Admin Inveb
                        $gestion_anterior_insert->work_order_id         = $gestion_ot->work_order_id;
                        $gestion_anterior_insert->work_space_id         = $area;
                        $gestion_anterior_insert->duracion_segundos     = $diff_mes_inicio;
                        $gestion_anterior_insert->state_id              = $gestion_ot->state_id;
                        $gestion_anterior_insert->mostrar               = 0;
                        $gestion_anterior_insert->created_at            = $fecha_gestion_anterior_fin;
                        $gestion_anterior_insert->updated_at            = $fecha_gestion_anterior_fin;
                        $gestion_anterior_insert->save();

                        $fecha_aux  = $fecha_gestion_anterior_ini;
                        $diff_segundos_total += $diff_mes_inicio;

                        for ($i = 30; $diff_days > $i; $i += 30) {

                            $fecha_aux = date("Y-m-d", strtotime($fecha_aux . "+ 1 month"));

                            $fecha_aux_ini = Carbon::createFromFormat('Y-m-d', date('Y', strtotime($fecha_aux)) . '-' . date('m', strtotime($fecha_aux)) . '-' . '1')->startOfMonth();
                            $fecha_aux_fin = Carbon::createFromFormat('Y-m-d', date('Y', strtotime($fecha_aux)) . '-' . date('m', strtotime($fecha_aux)) . '-' . '1')->endOfMonth();

                            if ($fecha_aux_ini == $fecha_ini_mes_actual) {
                                $i += $diff_days;
                            } else {
                                if ($area == 6) {
                                    $diff_mes_aux    = get_working_hours_muestra($fecha_aux_ini, $fecha_aux_fin) * 3600;
                                } else {
                                    $diff_mes_aux    = get_working_hours($fecha_aux_ini, $fecha_aux_fin) * 3600;
                                }

                                $gestion_anterior_insert                        = new Management();
                                $gestion_anterior_insert->observacion           = $observacion;
                                $gestion_anterior_insert->management_type_id    = 1;
                                $gestion_anterior_insert->user_id               = 1; //Usuario Admin Inveb
                                $gestion_anterior_insert->work_order_id         = $gestion_ot->work_order_id;
                                $gestion_anterior_insert->work_space_id         = $area;
                                $gestion_anterior_insert->duracion_segundos     = $diff_mes_aux;
                                $gestion_anterior_insert->state_id              = $gestion_ot->state_id;
                                $gestion_anterior_insert->mostrar               = 0;
                                $gestion_anterior_insert->created_at            = $fecha_aux_fin;
                                $gestion_anterior_insert->updated_at            = $fecha_aux_fin;
                                $gestion_anterior_insert->save();

                                $fecha_aux = $fecha_aux_ini;
                                $diff_segundos_total += $diff_mes_aux;
                            }
                        }

                        //dd($gestiones_ot->work_order_id,$fecha_aux,$diff_days,$i);
                        /* $mes_gestion_actual         = date('m', strtotime($gestion_ot->created_at));
                        $anio_gestion_actual_ini_mes= date('Y', strtotime($gestion_ot->created_at));
                        $fecha_gestion_actual_ini   = Carbon::createFromFormat('Y-m-d', $anio_gestion_actual_ini_mes.'-'.$mes_gestion_actual.'-'. '1')->startOfMonth();
                        $diff_mes_actual            = get_working_hours($fecha_gestion_actual_ini, $gestion_ot->created_at) * 3600;
                        //$gestion_ot_actual_update   = Management::where('id',$gestion_ot->id)->update(['duracion_segundos' => $diff_mes_actual]); */
                    } else {

                        //$mes_gestion_actual     = date('m', strtotime($gestion_ot->created_at));
                        $mes_gestion_anterior   = date('m', strtotime($gestion_ot->created_at));

                        if ($mes_actual != $mes_gestion_anterior) {

                            //dd($gestion_ot->id,$gestion_ot->work_order_id,$diff_days,$mes_gestion_actual,$mes_gestion_anterior);
                            /*$anio_gestion_actual_ini_mes= date('Y', strtotime($gestion_ot->created_at));
                            $fecha_gestion_actual_ini=Carbon::createFromFormat('Y-m-d', $anio_gestion_actual_ini_mes.'-'.$mes_gestion_actual.'-'. '1')->startOfMonth();
                            */
                            $anio_gestion_anterior_fin_mes = date('Y', strtotime($gestion_ot->created_at));
                            $fecha_gestion_anterior_fin = Carbon::createFromFormat('Y-m-d', $anio_gestion_anterior_fin_mes . '-' . $mes_gestion_anterior . '-' . '1')->endOfMonth();

                            // $diff_mes_actual    = get_working_hours($fecha_gestion_actual_ini, $gestion_ot->created_at) * 3600;
                            if ($area == 6) {
                                $diff_mes_anterior  = get_working_hours_muestra($gestion_ot->created_at, $fecha_gestion_anterior_fin) * 3600;
                            } else {
                                $diff_mes_anterior  = get_working_hours($gestion_ot->created_at, $fecha_gestion_anterior_fin) * 3600;
                            }

                            //$gestion_ot_actual_update = Management::where('id',$gestion_ot->id)->update(['duracion_segundos' => $diff_mes_actual]);

                            $gestion_anterior_insert                        = new Management();
                            $gestion_anterior_insert->observacion           = $observacion;
                            $gestion_anterior_insert->management_type_id    = 1;
                            $gestion_anterior_insert->user_id               = 1; //Usuario Admin Inveb
                            $gestion_anterior_insert->work_order_id         = $gestion_ot->work_order_id;
                            $gestion_anterior_insert->work_space_id         = $area;
                            $gestion_anterior_insert->duracion_segundos     = $diff_mes_anterior;
                            $gestion_anterior_insert->state_id              = $gestion_ot->state_id;
                            $gestion_anterior_insert->mostrar               = 0;
                            $gestion_anterior_insert->created_at            = $fecha_gestion_anterior_fin;
                            $gestion_anterior_insert->updated_at            = $fecha_gestion_anterior_fin;
                            $gestion_anterior_insert->save();
                        }
                    }
                }
            } else {
                //dd($current_date);
                //$current_date = Carbon::createFromFormat('Y-m-d', '2023-03-1');
                //$mes_actual  = date('m', strtotime($current_date));
                if ($gestion_ot &&  !in_array($gestion_ot->state_id, [8, 9, 11, 13]) && $gestion_ot->mostrar == 0) {
                    $mes_ultima_gestion = date('m', strtotime($gestion_ot->created_at));
                    if ($mes_ultima_gestion != $mes_actual) {

                        $diferencia         = date_diff($gestion_ot->created_at, $current_date);
                        $differenceFormat   = '%a';
                        $diff_days          = $diferencia->format($differenceFormat);
                        $diff_days_compare = ($mes_ultima_gestion == '01' && $mes_actual == '03') ? 28 : 30;
                        //dd($diff_days,$gestion_ot->created_at,$current_date,$diff_days_compare);
                        if ($diff_days >= $diff_days_compare) {

                            $mes_gestion_anterior       = date('m', strtotime($gestion_ot->created_at));
                            $anio_gestion_anterior      = date('Y', strtotime($gestion_ot->created_at));
                            //$fecha_gestion_anterior_fin = Carbon::createFromFormat('Y-m-d', $anio_gestion_anterior . '-' . $mes_gestion_anterior . '-' . '1')->endOfMonth();
                            $fecha_gestion_anterior_ini = Carbon::createFromFormat('Y-m-d', $anio_gestion_anterior . '-' . $mes_gestion_anterior . '-' . '1')->startOfMonth();
                            $fecha_aux = date("Y-m-d", strtotime($fecha_gestion_anterior_ini . "+ 1 month"));


                            $fecha_ini_aux = Carbon::createFromFormat('Y-m-d', date('Y', strtotime($fecha_aux)) . '-' . date('m', strtotime($fecha_aux)) . '-' . '1')->startOfMonth();
                            $fecha_fin_aux = Carbon::createFromFormat('Y-m-d', date('Y', strtotime($fecha_aux)) . '-' . date('m', strtotime($fecha_aux)) . '-' . '1')->endOfMonth();

                            if ($area == 6) {
                                $diff_mes_inicio    = get_working_hours_muestra($fecha_ini_aux, $fecha_fin_aux) * 3600;
                            } else {
                                $diff_mes_inicio    = get_working_hours($fecha_ini_aux, $fecha_fin_aux) * 3600;
                            }


                            $gestion_anterior_insert                        = new Management();
                            $gestion_anterior_insert->observacion           = $observacion;
                            $gestion_anterior_insert->management_type_id    = 1;
                            $gestion_anterior_insert->user_id               = 1; //Usuario Admin Inveb
                            $gestion_anterior_insert->work_order_id         = $gestion_ot->work_order_id;
                            $gestion_anterior_insert->work_space_id         = $area;
                            $gestion_anterior_insert->duracion_segundos     = $diff_mes_inicio;
                            $gestion_anterior_insert->state_id              = $gestion_ot->state_id;
                            $gestion_anterior_insert->mostrar               = 0;
                            $gestion_anterior_insert->created_at            = $fecha_fin_aux;
                            $gestion_anterior_insert->updated_at            = $fecha_fin_aux;
                            $gestion_anterior_insert->save();
                        }
                    }
                }
            }
        }
    }

    public function tiempo_ot_area_actual($fromDate, $toDate, $area, $ot_ids)
    {

        //$fecha_actual=Carbon::now();
        //Obtener Ot d eun area actual vigentes y que no tengan gestion durante el periodo filtrado
        $ots_de   = WorkOrder::where('current_area_id', $area)
            ->whereNotIn('id', $ot_ids)
            ->where('active', 1)
            ->get();
        //$ots_def=array();
        $tiempo_total = 0;
        $i = 0;
        $response = array();
        $mes_consulta_actual = date('m', strtotime($fromDate));
        $mes_actual = date('m', strtotime(Carbon::now()));
        $last_date = ($mes_consulta_actual == $mes_actual) ? date('Y-m-d H:i:s') : $toDate;

        foreach ($ots_de as $ot) {

            $gestiones_de   = Management::where('work_order_id', $ot->id)
                ->where('management_type_id', 1)
                ->where('created_at', '<', $fromDate)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($gestiones_de &&  !in_array($gestiones_de->state_id, [8, 9, 11, 13])) {
                //$ots_def[$i]=$gestiones_de->work_order_id;
                //dd($gestiones_de);
                $mes_ultima_gestion = date('m', strtotime($gestiones_de->created_at));

                if ($mes_ultima_gestion != $mes_consulta_actual) {

                    $anio_consulta_actual = date('Y', strtotime($fromDate));
                    $fecha_consulta_actual_ini = Carbon::createFromFormat('Y-m-d', $anio_consulta_actual . '-' . $mes_consulta_actual . '-' . '1')->startOfMonth();
                    if ($area == 6) {
                        $diff_mes_actual    = get_working_hours_muestra($fecha_consulta_actual_ini, $last_date) * 3600;
                    } else {
                        $diff_mes_actual    = get_working_hours($fecha_consulta_actual_ini, $last_date) * 3600;
                    }

                    $tiempo_total += $diff_mes_actual;
                    $i++;
                }
            }
        }

        //dd((($tiempo_total / 3600) / 9.5),$i,round((($tiempo_total / 3600) / 9.5)/$i));
        $response['tiempo'] = ($tiempo_total / 3600) / 9.5;
        $response['cantidad'] = $i;

        return $response;
    }

    public function cantidad_historico_ot_por_area($area_id, $vendedor_id, $fromDate, $toDate)
    {
        /*$anio = date("Y");
        $mes = date("m");
        $dia = date('d');*/
        //dd($fromDate, $toDate);
        /* $fromDate = $anio."-".$mes."-01 00:00:00";
        $toDate = $anio."-".$mes."-".$dia." 23:59:59";*/
        //dd($fromDate, $toDate);
        $vendedor = implode(",", $vendedor_id);

        //Se busca el estado dependiendo del área donde esta la OT ( por eso se reemplaza el id especifico por el relacionado con su area)
        $area = implode(" ", $area_id);

        $where = "WHERE ";
        if ($area === (string)2) { //-------------Desarrollo // $state_id = 2;

            $where .= " managements.state_id = 2";
        } else if ($area === (string)3) { //-------------Diseño // $state_id = 5;

            $where .= " managements.state_id = 5";
        } else if ($area === (string)4) { //-------------Catalogación // $state_id = 7;

            $where .= " managements.state_id = 7";
        } else if ($area === (string)5) { //-------------Precatalogación // $state_id = 6;

            $where .= " managements.state_id = 6";
        } else if ($area === (string)6) { //-------------Muestras // $state_id = 17;

            $where .= " managements.state_id = 17";
        } else { // $state_id = '(2, 5, 7, 6, 17)';

            $where .= " managements.state_id IN (2, 5, 7, 6, 17)";
        }


        $query = "SELECT
                    state_id,
                    area_abreviatura_nombre,
                    COUNT(*) as cantidad
                FROM
                    (SELECT DISTINCT
                        managements.state_id,
                        managements.work_order_id,
                        CASE
                            WHEN states.nombre = 'Proceso de Diseño Estructural' THEN 'DE'
                            WHEN states.nombre = 'Proceso de Diseño Gráfico' THEN 'DG'
                            WHEN states.nombre = 'Proceso de Catalogación' THEN 'C'
                            WHEN states.nombre = 'Proceso de Precatalogación' THEN 'PC'
                            WHEN states.nombre = 'Sala de Muestras' THEN 'SM'
                            ELSE 'Área'
                        END AS area_abreviatura_nombre
                    FROM
                        managements
                    JOIN states ON states.id = managements.state_id
                    JOIN work_orders ON work_orders.id = managements.work_order_id
                    $where
                        AND work_orders.creador_id IN ($vendedor)
                        AND managements.created_at BETWEEN '$fromDate' AND '$toDate'
                    GROUP BY
                        managements.state_id,
                        managements.work_order_id,
                        area_abreviatura_nombre ) AS consulta
                GROUP BY
                   state_id,
                   area_abreviatura_nombre";


        $ots = DB::select($query);

        //Se establece primero el arreglo que quiero mostrar en la grafica
        $array_key_definition = ['DE' => 0, 'SM' => 0, 'DG' => 0, 'PC' => 0, 'C' => 0];
        //Recorremos los datos y asignamos el valor que consiga en la consulta, sino se envia 0
        foreach ($ots as $value) {
            $array_key_definition[$value->area_abreviatura_nombre] = $value->cantidad;
        }
        $array_cantidad_ot_por_area = array_values($array_key_definition); //Asigna el valor
        $array_keys_ot_por_area = array_keys($array_key_definition); //Asigna el key

        //  \Log::info($array_cantidad_ot_por_area);
        //  \Log::info($array_keys_ot_por_area);

        return array(
            'array_cantidad_historico_ot_por_area' => $array_cantidad_ot_por_area,
            'array_keys_historico_ot_por_area' => $array_keys_ot_por_area
        );
    }

    public function datos_disenador_estructural($mes, $year)
    {

        $disenador_estructural = User::select("users.id", "users.nombre", "users.apellido")
            ->where("users.role_id", 6)
            ->whereHas('ots', function ($q) use ($year, $mes) {
                $q = $q->whereYear('user_work_orders.created_at', '=', $year)
                    ->whereMonth('user_work_orders.created_at', '=', $mes);
            })
            ->with(['ots' => function ($q) use ($year, $mes) {
                $q = $q->whereYear('user_work_orders.created_at', '=', $year)
                    ->whereMonth('user_work_orders.created_at', '=', $mes);
                // Calculo total de tiempo
                $q = $q->withCount([
                    'gestiones AS tiempo_total' => function ($q) use ($year, $mes) {
                        $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                            ->where('management_type_id', 1)
                            ->where('work_space_id', 2)
                            ->whereYear('created_at', '=', $year)
                            ->whereMonth('created_at', '=', $mes);
                    }
                ]);
            }])
            ->get();

        if ($disenador_estructural->count() > 0) {
            $disenador_estructural = $disenador_estructural->map(function ($disenador) use ($mes, $year) {

                //Se buscan las Ot que tengan gestion dentro del mes pero que no estan asignadas al Diseñador
                $ots_in = [];
                foreach ($disenador->ots as $ot) {
                    $ots_in[] = $ot->id;
                }

                //se obtiene la fecha para obtener las ot y los tiempos antes del periodo
                $fecha_anterior = $year . '-' . $mes . '-01';

                //Se obtienen las OT que estan actualmente en el area de Diseño estructural y no creada durante el periodo
                $ots_dis_estruc = WorkOrder::where('current_area_id', 2)
                    ->whereNotIn('id', $ots_in)
                    ->where('created_at', '<', $fecha_anterior)
                    ->pluck('id')
                    ->toArray();

                //Se obtienen las ot anteriores al periodo y que le que pertenecen al diseñador estructural
                $ots_dis_estruc_ot = UserWorkOrder::where('user_id', $disenador->id)
                    ->whereIn('work_order_id', $ots_dis_estruc)
                    ->pluck('work_order_id')
                    ->toArray();

                //Se obtienen las gestiones donde participa el diseñador antes del periodo
                $ots_gestion = Management::select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('managements.management_type_id', 1)
                    ->where('managements.work_space_id', 2)
                    ->where('managements.user_id', $disenador->id)
                    ->whereIn('managements.work_order_id', $ots_dis_estruc_ot)
                    ->where('managements.created_at', '<', $fecha_anterior)
                    ->groupBy("work_order_id")
                    ->get();

                //Se obtienen las gestiones donde participa el diseñador del antes del periodo
                $ots_gestion_aux = Management::select("managements.work_order_id")
                    ->where('managements.management_type_id', 1)
                    ->where('managements.work_space_id', 2)
                    ->where('managements.user_id', $disenador->id)
                    ->whereNotIn('managements.work_order_id', $ots_in)
                    ->whereYear('created_at', '=', $year)
                    ->whereMonth('created_at', '=', $mes)
                    ->groupBy("work_order_id")
                    ->get();
                //Se obtienen las gestiones donde participa el diseñador del antes del periodo
                $ots_gestion_aux_tiempo = Management::select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('managements.management_type_id', 1)
                    ->where('managements.work_space_id', 2)
                    ->where('managements.user_id', $disenador->id)
                    ->whereNotIn('managements.work_order_id', $ots_in)
                    ->whereYear('created_at', '=', $year)
                    ->whereMonth('created_at', '=', $mes)
                    ->groupBy("work_order_id")
                    ->get();

                $disenador->total_ots = count($disenador->ots) + count($ots_dis_estruc_ot) + count($ots_gestion_aux);
                $disenador->tiempo_total = ($disenador->ots->sum('tiempo_total') + $ots_gestion->sum('tiempo_total') + $ots_gestion_aux_tiempo->sum('tiempo_total')) / 3600 / 9.5;
                $disenador->tiempo_promedio = ($disenador->total_ots > 0 && $disenador->tiempo_total > 0) ? $disenador->tiempo_total / $disenador->total_ots : 0;
                return $disenador;
            })->sortByDesc("tiempo_promedio"); //->take(10);
        } else {
            $disenador_estructural = User::select("users.id", "users.nombre", "users.apellido")
                ->where("users.role_id", 6)
                ->get();

            $disenador_estructural = $disenador_estructural->map(function ($disenador) use ($mes, $year) {


                //se obtiene la fecha para obtener las ot y los tiempos antes del periodo
                $fecha_anterior = $year . '-' . $mes . '-01';

                //Se obtienen las OT que estan actualmente en el area de Diseño estructural y no creada durante el periodo
                $ots_dis_estruc = WorkOrder::where('current_area_id', 2)
                    //->whereNotIn('id',$ots_in)
                    ->where('created_at', '<', $fecha_anterior)
                    ->pluck('id')
                    ->toArray();

                //Se obtienen las ot anteriores al periodo y que le que pertenecen al diseñador estructural
                $ots_dis_estruc_ot = UserWorkOrder::where('user_id', $disenador->id)
                    ->whereIn('work_order_id', $ots_dis_estruc)
                    ->pluck('work_order_id')
                    ->toArray();

                //Se obtienen las gestiones donde participa el diseñador antes del periodo
                $ots_gestion = Management::select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('managements.management_type_id', 1)
                    ->where('managements.work_space_id', 2)
                    ->where('managements.user_id', $disenador->id)
                    ->whereIn('managements.work_order_id', $ots_dis_estruc_ot)
                    ->where('managements.created_at', '<', $fecha_anterior)
                    ->groupBy("work_order_id")
                    ->get();

                //Se obtienen las gestiones donde participa el diseñador del antes del periodo
                $ots_gestion_aux = Management::select("managements.work_order_id")
                    ->where('managements.management_type_id', 1)
                    ->where('managements.work_space_id', 2)
                    ->where('managements.user_id', $disenador->id)
                    //->whereNotIn('managements.work_order_id',$ots_in)
                    ->whereYear('created_at', '=', $year)
                    ->whereMonth('created_at', '=', $mes)
                    ->groupBy("work_order_id")
                    ->get();
                //Se obtienen las gestiones donde participa el diseñador del antes del periodo
                $ots_gestion_aux_tiempo = Management::select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('managements.management_type_id', 1)
                    ->where('managements.work_space_id', 2)
                    ->where('managements.user_id', $disenador->id)
                    //->whereNotIn('managements.work_order_id',$ots_in)
                    ->whereYear('created_at', '=', $year)
                    ->whereMonth('created_at', '=', $mes)
                    ->groupBy("work_order_id")
                    ->get();



                $disenador->total_ots = count($disenador->ots) + count($ots_dis_estruc_ot) + count($ots_gestion_aux);
                $disenador->tiempo_total = ($disenador->ots->sum('tiempo_total') + $ots_gestion->sum('tiempo_total') + $ots_gestion_aux_tiempo->sum('tiempo_total')) / 3600 / 9.5;
                $disenador->tiempo_promedio = ($disenador->total_ots > 0 && $disenador->tiempo_total > 0) ? $disenador->tiempo_total / $disenador->total_ots : 0;
                return $disenador;
            })->sortByDesc("tiempo_promedio"); //->take(10);

        }

        return $disenador_estructural;
    }

    public function datos_disenador_grafico($mes, $year)
    {
        $mes = $mes;
        $year = $year;
        $disenador_grafico = User::select("users.id", "users.nombre", "users.apellido")
            ->where("users.role_id", 8)
            ->whereHas('ots', function ($q) use ($year, $mes) {
                $q = $q->whereYear('user_work_orders.created_at', '=', $year)
                    ->whereMonth('user_work_orders.created_at', '=', $mes);
            })
            ->with(['ots' => function ($q) use ($year, $mes) {
                $q = $q->whereYear('user_work_orders.created_at', '=', $year)
                    ->whereMonth('user_work_orders.created_at', '=', $mes);
                // Calculo total de tiempo
                $q = $q->withCount([
                    'gestiones AS tiempo_total' => function ($q) use ($year, $mes) {
                        $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                            ->where('management_type_id', 1)
                            ->where('work_space_id', 3)
                            ->whereYear('created_at', '=', $year)
                            ->whereMonth('created_at', '=', $mes);
                    }
                ]);
            }])
            ->get();
        if ($disenador_grafico->count() > 0) {
            $disenador_grafico = $disenador_grafico->map(function ($disenador) use ($mes, $year) {

                //Se buscan las Ot que tengan gestion dentro del mes pero que no estan asignadas al Diseñador
                $ots_in = [];
                foreach ($disenador->ots as $ot) {
                    $ots_in[] = $ot->id;
                }

                //se obtiene la fecha para obtener las ot y los tiempos antes del periodo
                $fecha_anterior = $year . '-' . $mes . '-01';

                //Se obtienen las OT que estan actualmente en el area de Diseño Grafico y no creada durante el periodo
                $ots_dis_graf = WorkOrder::where('current_area_id', 3)
                    ->whereNotIn('id', $ots_in)
                    ->where('created_at', '<', $fecha_anterior)
                    ->pluck('id')
                    ->toArray();

                //Se obtienen las ot anteriores al periodo y que le que pertenecen al diseñador estructural
                $ots_dis_graf_ot = UserWorkOrder::where('user_id', $disenador->id)
                    ->whereIn('work_order_id', $ots_dis_graf)
                    ->pluck('work_order_id')
                    ->toArray();

                //Se obtienen las gestiones donde participa el diseñador del periodo
                $ots_gestion = Management::select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('managements.management_type_id', 1)
                    ->where('managements.work_space_id', 3)
                    ->where('managements.user_id', $disenador->id)
                    ->whereIn('managements.work_order_id', $ots_dis_graf_ot)
                    ->where('managements.created_at', '<', $fecha_anterior)
                    ->groupBy("work_order_id")
                    ->get();

                //Se obtienen las gestiones donde participa el diseñador del antes del periodo
                $ots_gestion_aux = Management::select("managements.work_order_id")
                    ->where('managements.management_type_id', 1)
                    ->where('managements.work_space_id', 3)
                    ->where('managements.user_id', $disenador->id)
                    ->whereNotIn('managements.work_order_id', $ots_in)
                    ->whereYear('created_at', '=', $year)
                    ->whereMonth('created_at', '=', $mes)
                    ->groupBy("work_order_id")
                    ->get();
                //Se obtienen las gestiones donde participa el diseñador del antes del periodo
                $ots_gestion_aux_tiempo = Management::select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('managements.management_type_id', 1)
                    ->where('managements.work_space_id', 3)
                    ->where('managements.user_id', $disenador->id)
                    ->whereNotIn('managements.work_order_id', $ots_in)
                    ->whereYear('created_at', '=', $year)
                    ->whereMonth('created_at', '=', $mes)
                    ->groupBy("work_order_id")
                    ->get();

                $disenador->total_ots = count($disenador->ots) + count($ots_dis_graf_ot) + count($ots_gestion_aux);
                $disenador->tiempo_total = ($disenador->ots->sum('tiempo_total') + $ots_gestion->sum('tiempo_total') + $ots_gestion_aux_tiempo->sum('tiempo_total')) / 3600 / 9.5;
                $disenador->tiempo_promedio = ($disenador->total_ots > 0 && $disenador->tiempo_total > 0) ? $disenador->tiempo_total / $disenador->total_ots : 0;
                return $disenador;
            })->sortByDesc("tiempo_promedio"); //->take(10);
        } else {
            $disenador_grafico = User::select("users.id", "users.nombre", "users.apellido")
                ->where("users.role_id", 8)
                ->get();

            $disenador_grafico = $disenador_grafico->map(function ($disenador) use ($mes, $year) {


                //se obtiene la fecha para obtener las ot y los tiempos antes del periodo
                $fecha_anterior = $year . '-' . $mes . '-01';

                //Se obtienen las OT que estan actualmente en el area de Diseño grafico y no creada durante el periodo
                $ots_dis_grafico = WorkOrder::where('current_area_id', 3)
                    //->whereNotIn('id',$ots_in)
                    ->where('created_at', '<', $fecha_anterior)
                    ->pluck('id')
                    ->toArray();

                //Se obtienen las ot anteriores al periodo y que le que pertenecen al diseñador grafico
                $ots_dis_grafico_ot = UserWorkOrder::where('user_id', $disenador->id)
                    ->whereIn('work_order_id', $ots_dis_grafico)
                    ->pluck('work_order_id')
                    ->toArray();

                //Se obtienen las gestiones donde participa el diseñador antes del periodo
                $ots_gestion = Management::select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('managements.management_type_id', 1)
                    ->where('managements.work_space_id', 3)
                    ->where('managements.user_id', $disenador->id)
                    ->whereIn('managements.work_order_id', $ots_dis_grafico_ot)
                    ->where('managements.created_at', '<', $fecha_anterior)
                    ->groupBy("work_order_id")
                    ->get();

                //Se obtienen las gestiones donde participa el diseñador del antes del periodo
                $ots_gestion_aux = Management::select("managements.work_order_id")
                    ->where('managements.management_type_id', 1)
                    ->where('managements.work_space_id', 3)
                    ->where('managements.user_id', $disenador->id)
                    //->whereNotIn('managements.work_order_id',$ots_in)
                    ->whereYear('created_at', '=', $year)
                    ->whereMonth('created_at', '=', $mes)
                    ->groupBy("work_order_id")
                    ->get();
                //Se obtienen las gestiones donde participa el diseñador del antes del periodo
                $ots_gestion_aux_tiempo = Management::select(DB::raw("SUM(duracion_segundos) as tiempo_total"))
                    ->where('managements.management_type_id', 1)
                    ->where('managements.work_space_id', 3)
                    ->where('managements.user_id', $disenador->id)
                    //->whereNotIn('managements.work_order_id',$ots_in)
                    ->whereYear('created_at', '=', $year)
                    ->whereMonth('created_at', '=', $mes)
                    ->groupBy("work_order_id")
                    ->get();



                $disenador->total_ots = count($disenador->ots) + count($ots_dis_grafico_ot) + count($ots_gestion_aux);
                $disenador->tiempo_total = ($disenador->ots->sum('tiempo_total') + $ots_gestion->sum('tiempo_total') + $ots_gestion_aux_tiempo->sum('tiempo_total')) / 3600 / 9.5;
                $disenador->tiempo_promedio = ($disenador->total_ots > 0 && $disenador->tiempo_total > 0) ? $disenador->tiempo_total / $disenador->total_ots : 0;
                return $disenador;
            })->sortByDesc("tiempo_promedio"); //->take(10);
        }

        return $disenador_grafico;
    }

    public function ot_con_muestras_pendientes_corte($fromDate, $toDate)
    {
        //Consultamos las OT que estan en el dashboard solo en el área de sala de muestra
        // $ot_id = self::query_ot_area_sala_muestra();

        //Consulta de muestras en estado proceso -> 1
        $muestrasEstadoProceso = Muestra::where("work_order_id", "!=", "0")
            ->where("estado", "1")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('fecha_corte_vendedor', NULL)
            ->where('fecha_corte_diseñador', NULL)
            ->where('fecha_corte_laboratorio', NULL)
            ->where('fecha_corte_1', NULL)
            ->where('fecha_corte_2', NULL)
            ->where('fecha_corte_3', NULL)
            ->where('fecha_corte_4', NULL)
            //->whereIn("work_order_id", $ot_id)
            ->get();
        //------ Calculo de muestras pendientes corte ( solo las que estan con estado de 'proceso')
        $cantidad_ot_fecha_NULL = self::cantidad_ot_fecha_NULL($muestrasEstadoProceso);

        return $cantidad_ot_fecha_NULL;
    }

    public function id_muestras_pendientes_corte($fromDate, $toDate)
    {
        //Consultamos las OT que estan en el dashboard solo en el área de sala de muestra
        //$ot_id = self::query_ot_area_sala_muestra();
        //dd($fromDate,$toDate);
        //Consulta de muestras en estado proceso -> 1
        $muestrasEstadoProceso = Muestra::where("work_order_id", "!=", "0")
            ->where("estado", "1")
            //->whereIn("work_order_id", $ot_id)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('fecha_corte_vendedor', NULL)
            ->where('fecha_corte_diseñador', NULL)
            ->where('fecha_corte_laboratorio', NULL)
            ->where('fecha_corte_1', NULL)
            ->where('fecha_corte_2', NULL)
            ->where('fecha_corte_3', NULL)
            ->where('fecha_corte_4', NULL)
            ->get();

        //------ Calculo de muestras pendientes corte ( solo las que estan con estado de 'proceso')
        $cantidad_id_fecha_NULL = self::cantidad_id_fecha_NULL($muestrasEstadoProceso);

        return $cantidad_id_fecha_NULL;
    }

    public function muestras_pendientes_corte($fromDate, $toDate)
    {
        //Consultamos las OT que estan en el dashboard solo en el área de sala de muestra
        $ot_id = self::query_ot_area_sala_muestra();

        //Consulta de muestras en estado proceso -> 1
        $muestrasEstadoProceso = Muestra::where("work_order_id", "!=", 0)
            ->where("estado", 1)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('fecha_corte_vendedor', NULL)
            ->where('fecha_corte_diseñador', NULL)
            ->where('fecha_corte_laboratorio', NULL)
            ->where('fecha_corte_1', NULL)
            ->where('fecha_corte_2', NULL)
            ->where('fecha_corte_3', NULL)
            ->where('fecha_corte_4', NULL)
            //->whereIn("work_order_id", $ot_id)
            ->get();

        //------ Calculo de muestras pendientes corte ( solo las que estan con estado de 'proceso')
        $cantidad_muestras_fecha_NULL = self::cantidad_muestras_fecha_NULL($muestrasEstadoProceso);

        return $cantidad_muestras_fecha_NULL;
    }

    //Gráfica OT CON MUESTRAS
    public function promedio_ot_con_muestras_anio_anterior($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate)->subYear('1');

        $anioAnterior = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $fromDate = $anioAnterior . "-01-01 00:00:00";
        $toDate = $anioAnterior . "-12-31 23:59:59";

        $titulo = 'Prom' . ' ' . $anio_digit;

        $cantidad = self::query_cantidad_ot_con_muestras($fromDate, $toDate);

        //La formula que me indicaron fue la siguiente :
        //Promedio Año Pasado: se toma los totales de cada mes de año y lo divides por 12 meses.
        $promedio = $cantidad ? round($cantidad / 12, 1) : 0;

        return array(
            'titulo' => $titulo,
            'promedio' => $promedio,
        );
    }

    public function cantidad_ot_con_muestras_mes_actual_anio_anterior($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate)->subYear('1');
        //La diferencia de esta funcion con la funcion ot_con_muestras_pendientes_corte es que en esta, el mes no se cambia, siempre sera el
        //mes actual del año pasado
        $anioAnterior = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $mes = $fullDate->format('m');
        $fromDate = $anioAnterior . "-" . $mes . "-01 00:00:00";
        $toDate = date("Y-m-t 23:59:59", strtotime($fromDate));

        //Funcion para obtener el nombre del mes en español
        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
        $mes_nombre =  $meses[$mes - 1];

        $titulo = ucwords($mes_nombre) . ' ' . $anio_digit;

        $cantidad = self::query_cantidad_ot_con_muestras($fromDate, $toDate);

        return array(
            'titulo' => $titulo,
            'cantidad' => $cantidad,
        );
    }

    public function cantidad_ot_con_muestras_mes_anio_actual($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate);
        $fullToDate = Carbon::instance($toDate);

        //Siempre sera el mes actual
        $mes = $fullDate->format('m');
        $anio = $fullDate->format("Y");
        $anio_digit = $fullDate->format('y');
        $dia = $fullToDate->format('d');

        $fromDate = $anio . "-" . $mes . "-01 00:00:00";
        $toDate = $anio . "-" . $mes . "-" . $dia . " 23:59:59";

        //Funcion para obtener el nombre del mes en español
        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
        $mes_nombre =  $meses[$mes - 1];

        $titulo = ucwords($mes_nombre) . ' ' . $anio_digit;

        $cantidad = self::query_cantidad_ot_con_muestras($fromDate, $toDate);

        return array(
            'titulo' => $titulo,
            'cantidad' => $cantidad,
        );
    }

    public function promedio_ot_con_muestras_anio_actual($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($toDate);

        $mes = $fullDate->format("m");
        $mes_anterior = $fullDate->subMonth('1')->format('m');
        $anio = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $dia = $fullDate->format('d');

        $fromDateM = $anio . "-" . $mes . "-01"; //Inicio del mes actual
        $fromDate = $anio . "-01-01 00:00:00"; //Inicio de año actual
        $toDate = $anio . "-" . $mes . "-" . $dia . " 23:59:59";

        $titulo = 'Prom' . ' ' . $anio_digit;

        $cantidad = self::query_cantidad_ot_con_muestras($fromDate, $toDate);
        $promedio = $cantidad ? round($cantidad / $mes, 1) : 0;

        return array(
            'titulo' => $titulo,
            'promedio' => $promedio,
        );
    }
    //FIN Gráfica OT CON MUESTRAS

    //Gráfica OT CON MUESTRAS CORTADAS
    public function promedio_ot_con_muestras_cortadas_anio_anterior($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate)->subYear('1');

        $anioAnterior = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $fromDate = $anioAnterior . "-01-01 00:00:00";
        $toDate = $anioAnterior . "-12-31 23:59:59";

        $titulo = 'Prom' . ' ' . $anio_digit;

        $cantidad_id = self::cantidad_ot_cortadas_terminadas($fromDate, $toDate);
        $cantidad = implode($cantidad_id);

        //La formula que me indicaron fue la siguiente :
        //Año anterior: sumas todo y lo divides por 12
        $promedio = $cantidad ? round($cantidad / 12, 1) : 0;

        return array(
            'titulo' => $titulo,
            'promedio' => $promedio,
        );
    }

    public function cantidad_ot_con_muestras_cortadas_mes_actual_anio_anterior($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate)->subYear('1');

        $anioAnterior = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $mes = $fullDate->format("m");
        $fromDate = $anioAnterior . "-" . $mes . "-01 00:00:00";
        $toDate = date("Y-m-t 23:59:59", strtotime($fromDate));

        //Funcion para obtener el nombre del mes en español
        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
        $mes_nombre =  $meses[$mes - 1];

        $titulo = ucwords($mes_nombre) . ' ' . $anio_digit;

        $cantidad_id = self::cantidad_ot_cortadas_terminadas($fromDate, $toDate);
        $cantidad = implode($cantidad_id);

        return array(
            'titulo' => $titulo,
            'cantidad' => $cantidad,
        );
    }

    public function cantidad_ot_con_muestras_cortadas_mes_anio_actual($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($toDate);

        $mes = $fullDate->format("m");
        $anio = $fullDate->format("Y");
        $anio_digit = $fullDate->format('y');
        $dia = $fullDate->format('d');
        $fromDate = $anio . "-" . $mes . "-01 00:00:00";
        $toDate = $anio . "-" . $mes . "-" . $dia . " 23:59:59";

        //Funcion para obtener el nombre del mes en español
        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
        $mes_nombre =  $meses[$mes - 1];

        $titulo = ucwords($mes_nombre) . ' ' . $anio_digit;

        $cantidad_id = self::cantidad_ot_cortadas_terminadas($fromDate, $toDate);
        $cantidad = implode($cantidad_id);

        return array(
            'titulo' => $titulo,
            'cantidad' => $cantidad,
        );
    }

    public function promedio_ot_con_muestras_cortadas_anio_actual($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($toDate);

        $mes = $fullDate->format("m");
        // $mes_anterior = $fullDate->subMonth('1')->format('m');
        $anio = $fullDate->format("Y");
        $anio_digit = $fullDate->format('y');
        $dia = $fullDate->format('d');

        $fromDateM = $anio . "-" . $mes . "-01"; //Inicio del mes actual
        $fromDate = $anio . "-01-01 00:00:00"; //Inicio de año actual
        $toDate = $anio . "-" . $mes . "-" . $dia . " 23:59:59";

        // //Mes anterior que seria el ultimo del año actual cerrado
        // $fromDate_mes_anterior = $anio."-".$mes_anterior."-01";
        // $toDate_mes_anterior = date("Y-m-t", strtotime($fromDate_mes_anterior));

        $titulo = 'Prom' . ' ' . $anio_digit;

        $cantidad_id = self::cantidad_ot_cortadas_terminadas($fromDate, $toDate);
        $cantidad = implode($cantidad_id);

        $promedio = $cantidad ? round($cantidad / $mes, 1) : 0;


        return array(
            'titulo' => $titulo,
            'promedio' => $promedio,
        );
    }
    //Gráfica OT CON MUESTRAS CORTADAS

    //Gráfica ID SOLICITADAS
    public function promedio_id_con_muestras_anio_anterior($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate)->subYear(1);

        $anioAnterior = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $fromDate = $anioAnterior . "-01-01 00:00:00";
        $toDate = $anioAnterior . "-12-31 23:59:59";

        $titulo = 'Prom' . ' ' . $anio_digit;

        //Consulta de muestras en estado proceso -> 1 y con fechas NULL
        $cantidad = self::query_cantidad_id_solicitadas($fromDate, $toDate);

        //La formula que me indicaron fue la siguiente :
        //Promedio Año Pasado: se toma los totales de cada mes de año y lo divides por 12 meses.
        $promedio = $cantidad ? round($cantidad / 12, 1) : 0;

        return array(
            'titulo' => $titulo,
            'promedio' => $promedio,
        );
    }

    public function cantidad_id_con_muestras_pendientes_mes_actual_anio_anterior($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate)->subYear(1);

        $anioAnterior = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $mes = $fullDate->format("m");
        $fromDate = $anioAnterior . "-" . $mes . "-01 00:00:00";
        $toDate = date("Y-m-t 23:59:59", strtotime($fromDate));

        //Funcion para obtener el nombre del mes en español
        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
        $mes_nombre =  $meses[$mes - 1];

        $titulo = ucwords($mes_nombre) . ' ' . $anio_digit;

        //Consulta de muestras en estado proceso -> 1 y con fechas NULL
        $cantidad = self::query_cantidad_id_solicitadas($fromDate, $toDate);

        return array(
            'titulo' => $titulo,
            'cantidad' => $cantidad,
        );
    }

    public function cantidad_id_con_muestras_pendientes_mes_anio_actual($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($toDate);

        $mes = $fullDate->format("m");
        $anio = $fullDate->format("Y");
        $anio_digit = $fullDate->format('y');
        $dia = $fullDate->format('d');

        $fromDate = $anio . "-" . $mes . "-01 00:00:00";
        $toDate = $anio . "-" . $mes . "-" . $dia . " 23:59:59";

        //Funcion para obtener el nombre del mes en español
        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
        $mes_nombre =  $meses[$mes - 1];

        $titulo = ucwords($mes_nombre) . ' ' . $anio_digit;

        $cantidad = self::query_cantidad_id_solicitadas($fromDate, $toDate);

        return array(
            'titulo' => $titulo,
            'cantidad' => $cantidad,
        );
    }

    public function promedio_id_con_muestras_anio_actual($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($toDate);

        $mes = $fullDate->format("m");
        $anio = $fullDate->format("Y");
        $anio_digit = $fullDate->format('y');
        $dia = $fullDate->format('d');

        $fromDateM = $anio . "-" . $mes . "-01"; //Inicio del mes actual
        $fromDate = $anio . "-01-01 00:00:00"; //Inicio de año actual
        $toDate = $anio . "-" . $mes . "-" . $dia . " 23:59:59";

        //Mes anterior que seria el ultimo del año actual cerrado
        // $fromDate_mes_anterior = $anio."-".$mes_anterior."-01";
        // $toDate_mes_anterior = date("Y-m-t", strtotime($fromDate_mes_anterior));

        $titulo = 'Prom' . ' ' . $anio_digit;

        //Consulta de muestras en estado proceso -> 1 y con fechas NULL
        $cantidad = self::query_cantidad_id_solicitadas($fromDate, $toDate);
        $promedio = $cantidad ? round($cantidad / $mes, 1) : 0;

        return array(
            'titulo' => $titulo,
            'promedio' => $promedio,
        );
    }
    //FIN Gráfica ID SOLICITADAS

    //Gráfica ID CORTADAS
    public function promedio_id_con_muestras_cortadas_anio_anterior($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate)->subYear(1);

        $anioAnterior = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $fromDate = $anioAnterior . "-01-01 00:00:00";
        $toDate = $anioAnterior . "-12-31 23:59:59";

        $titulo = 'Prom' . ' ' . $anio_digit;

        $cantidad_id = self::cantidad_id_cortadas_terminadas($fromDate, $toDate);
        $cantidad = implode($cantidad_id);

        //La formula que me indicaron fue la siguiente :
        //Año anterior: sumas todo y lo divides por 12
        $promedio = $cantidad ? round($cantidad / 12, 1) : 0;

        return array(
            'titulo' => $titulo,
            'promedio' => $promedio,
        );
    }

    public function cantidad_id_con_muestras_cortadas_mes_actual_anio_anterior($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate)->subYear(1);

        $anioAnterior = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $mes = $fullDate->format("m");
        $fromDate = $anioAnterior . "-" . $mes . "-01 00:00:00";
        $toDate = date("Y-m-t 23:59:59", strtotime($fromDate));

        //Funcion para obtener el nombre del mes en español
        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
        $mes_nombre =  $meses[$mes - 1];

        $titulo = ucwords($mes_nombre) . ' ' . $anio_digit;

        //------ Calculo de muestras pendientes termino ( solo las que estan con estado de proceso y con fecha corte )
        // NOTA: Lo que diferencia una muestra pendiente de corte con una muestra pendiente de termino, es que la de termino ya tiene una fecha de "termino" dependiendo de su destinatario
        $cantidad_id = self::cantidad_id_cortadas_terminadas($fromDate, $toDate);
        $cantidad = implode($cantidad_id);

        return array(
            'titulo' => $titulo,
            'cantidad' => $cantidad,
        );
    }

    public function cantidad_id_con_muestras_cortadas_mes_anio_actual($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($toDate);

        $mes = $fullDate->format("m");
        $anio = $fullDate->format("Y");
        $anio_digit = $fullDate->format('y');
        $dia = $fullDate->format('d');
        $fromDate = $anio . "-" . $mes . "-01 00:00:00";
        $toDate = $anio . "-" . $mes . "-" . $dia . " 23:59:59";

        //Funcion para obtener el nombre del mes en español
        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
        $mes_nombre =  $meses[$mes - 1];

        $titulo = ucwords($mes_nombre) . ' ' . $anio_digit;

        //------ Calculo de muestras pendientes termino ( solo las que estan con estado de proceso y con fecha corte )
        // NOTA: Lo que diferencia una muestra pendiente de corte con una muestra pendiente de termino, es que la de termino ya tiene una fecha de "termino" dependiendo de su destinatario
        $cantidad_id = self::cantidad_id_cortadas_terminadas($fromDate, $toDate);
        $cantidad = implode($cantidad_id);

        return array(
            'titulo' => $titulo,
            'cantidad' => $cantidad,
        );
    }

    public function promedio_id_con_muestras_cortadas_anio_actual($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($toDate);

        $mes = $fullDate->format("m");
        $anio = $fullDate->format("Y");
        $anio_digit = $fullDate->format('y');
        $dia = $fullDate->format('d');

        $fromDateM = $anio . "-" . $mes . "-01"; //Inicio del mes actual
        $fromDate = $anio . "-01-01 00:00:00"; //Inicio de año actual
        $toDate = $anio . "-" . $mes . "-" . $dia . " 23:59:59";

        $titulo = 'Prom' . ' ' . $anio_digit;

        //------ Calculo de muestras pendientes termino ( solo las que estan con estado de proceso y con fecha corte )
        // NOTA: Lo que diferencia una muestra pendiente de corte con una muestra pendiente de termino, es que la de termino ya tiene una fecha de "termino" dependiendo de su destinatario
        $cantidad_id = self::cantidad_id_cortadas_terminadas($fromDate, $toDate);
        $cantidad = implode($cantidad_id);

        $promedio = $cantidad ? round($cantidad / $mes, 1) : 0;

        return array(
            'titulo' => $titulo,
            'promedio' => $promedio,
        );
    }
    //Fin Gráfica ID CORTADAS

    //Gráfica MUESTRAS SOLICITADAS
    public function promedio_muestras_anio_anterior($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate)->subYear('1');

        $anioAnterior = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $fromDate = $anioAnterior . "-01-01 00:00:00";
        $toDate = $anioAnterior . "-12-31 23:59:59";

        $titulo = 'Prom' . ' ' . $anio_digit;

        $cantidad = self::cantidad_muestras_solicitadas($fromDate, $toDate);
        $promedio = $cantidad ? round($cantidad / 12, 1) : 0;

        return array(
            'titulo' => $titulo,
            'promedio' => $promedio,
        );
    }

    public function cantidad_muestras_mes_actual_anio_anterior($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate)->subYear(1);

        $anioAnterior = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $mes = $fullDate->format("m");
        $fromDate = $anioAnterior . "-" . $mes . "-01 00:00:00";
        $toDate = date("Y-m-t 23:59:59", strtotime($fromDate));

        //Funcion para obtener el nombre del mes en español
        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
        $mes_nombre =  $meses[$mes - 1];

        $titulo = ucwords($mes_nombre) . ' ' . $anio_digit;

        $cantidad = self::cantidad_muestras_solicitadas($fromDate, $toDate);

        return array(
            'titulo' => $titulo,
            'cantidad' => $cantidad,
        );
    }

    public function cantidad_muestras_mes_anio_actual($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($toDate);

        $mes = $fullDate->format("m");
        $anio = $fullDate->format("Y");
        $anio_digit = $fullDate->format('y');
        $dia = $fullDate->format('d');

        $fromDate = $anio . "-" . $mes . "-01 00:00:00";
        $toDate = $anio . "-" . $mes . "-" . $dia . " 23:59:59";

        //Funcion para obtener el nombre del mes en español
        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
        $mes_nombre =  $meses[$mes - 1];

        $titulo = ucwords($mes_nombre) . ' ' . $anio_digit;

        $cantidad = self::cantidad_muestras_solicitadas($fromDate, $toDate);

        return array(
            'titulo' => $titulo,
            'cantidad' => $cantidad,
        );
    }

    public function promedio_muestras_anio_actual($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($toDate);

        $mes = $fullDate->format("m");
        $anio = $fullDate->format("Y");
        $anio_digit = $fullDate->format('y');
        $dia = $fullDate->format('d');

        $fromDateM = $anio . "-" . $mes . "-01"; //Inicio del mes actual
        $fromDate = $anio . "-01-01 00:00:00"; //Inicio de año actual
        $toDate = $anio . "-" . $mes . "-" . $dia . " 23:59:59";

        $titulo = 'Prom' . ' ' . $anio_digit;

        $cantidad = self::cantidad_muestras_solicitadas($fromDate, $toDate);
        $promedio = $cantidad ? round($cantidad / $mes, 1) : 0;

        return array(
            'titulo' => $titulo,
            'promedio' => $promedio,
        );
    }
    //FIN Gráfica MUESTRAS SOLICITADAS

    //Gráfica MUESTRAS CORTADAS
    public function promedio_muestras_cortadas_anio_anterior($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate)->subYear(1);

        $anioAnterior = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $fromDate = $anioAnterior . "-01-01 00:00:00";
        $toDate = $anioAnterior . "-12-31 23:59:59";

        $titulo = 'Prom' . ' ' . $anio_digit;

        $cantidad = self::cantidad_muestras_cortadas_terminadas($fromDate, $toDate);

        //La formula que me indicaron fue la siguiente :
        //Promedio Año Pasado: se toma los totales de cada mes de año y lo divides por 12 meses.
        $promedio = $cantidad ? round($cantidad / 12, 1) : 0;

        return array(
            'titulo' => $titulo,
            'promedio' => $promedio,
        );
    }

    public function cantidad_muestras_cortadas_mes_actual_anio_anterior($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($fromDate)->subYear(1);

        $anioAnterior = $fullDate->format('Y');
        $anio_digit = $fullDate->format('y');
        $mes = $fullDate->format("m");
        $fromDate = $anioAnterior . "-" . $mes . "-01 00:00:00";
        $toDate = date("Y-m-t 23:59:59", strtotime($fromDate));

        //Funcion para obtener el nombre del mes en español
        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
        $mes_nombre =  $meses[$mes - 1];

        $titulo = ucwords($mes_nombre) . ' ' . $anio_digit;

        $cantidad = self::cantidad_muestras_cortadas_terminadas($fromDate, $toDate);

        return array(
            'titulo' => $titulo,
            'cantidad' => $cantidad,
        );
    }

    public function cantidad_muestras_cortadas_mes_anio_actual($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($toDate);

        $mes = $fullDate->format("m");
        $anio = $fullDate->format("Y");
        $anio_digit = $fullDate->format('y');
        $dia = $fullDate->format('d');

        $fromDate = $anio . "-" . $mes . "-01 00:00:00";
        $toDate = $anio . "-" . $mes . "-" . $dia . " 23:59:59";

        //Funcion para obtener el nombre del mes en español
        $meses = array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
        $mes_nombre =  $meses[$mes - 1];

        $titulo = ucwords($mes_nombre) . ' ' . $anio_digit;

        $cantidad = self::cantidad_muestras_cortadas_terminadas($fromDate, $toDate);




        return array(
            'titulo' => $titulo,
            'cantidad' => $cantidad,
        );
    }

    public function promedio_muestras_cortadas_anio_actual($fromDate, $toDate)
    {
        $fullDate = Carbon::instance($toDate);

        $mes = $fullDate->format("m");
        $anio = $fullDate->format("Y");
        $anio_digit = $fullDate->format('y');
        $dia = $fullDate->format('d');

        $fromDateM = $anio . "-" . $mes . "-01"; //Inicio del mes actual
        $fromDate = $anio . "-01-01 00:00:00"; //Inicio de año actual
        $toDate = $anio . "-" . $mes . "-" . $dia . " 23:59:59";

        $titulo = 'Prom' . ' ' . $anio_digit;

        $cantidad = self::cantidad_muestras_cortadas_terminadas($fromDate, $toDate);
        $promedio = $cantidad ? round($cantidad / $mes, 1) : 0;

        return array(
            'titulo' => $titulo,
            'promedio' => $promedio,
        );
    }
    //FIN Gráfica MUESTRAS CORTADAS

    //--*************** Funciones extras que complementan las consultas de las funciones anteriores ***************--
    public function getDiasHabiles($fechainicio, $fechafin, $diasferiados = array())
    {
        // Convirtiendo en timestamp las fechas
        $fechainicio = strtotime($fechainicio);
        $fechafin = strtotime($fechafin);

        // Incremento en 1 dia
        $diainc = 24 * 60 * 60;

        // Arreglo de dias habiles, inicianlizacion
        $diashabiles = array();

        // Se recorre desde la fecha de inicio a la fecha fin, incrementando en 1 dia
        for ($midia = $fechainicio; $midia <= $fechafin; $midia += $diainc) {
            // Si el dia indicado, no es sabado o domingo es habil
            if (!in_array(date('N', $midia), array(6, 7))) { // DOC: http://www.php.net/manual/es/function.date.php
                // Si no es un dia feriado entonces es habil
                if (!in_array(date('Y-m-d', $midia), $diasferiados)) {
                    array_push($diashabiles, date('Y-m-d', $midia));
                }
            }
        }

        return $diashabiles;
    }

    public function ot_con_muestras_pendientes_de_termino($fromDate, $toDate)
    {
        //Consulta de muestras en estado proceso -> 1
        $muestrasEstadoProceso = Muestra::where("work_order_id", "!=", "0")
            ->where("estado", "1")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();

        //------ Calculo de muestras pendientes termino ( solo las que estan con estado de proceso y con fecha corte )
        $verificar_muestras_fecha_con_dato = self::verificar_muestras_fecha_con_dato($muestrasEstadoProceso);
        $muestrasPendientesTermino = $verificar_muestras_fecha_con_dato['cantidad'];

        //Calculo de muestras pendientes termino agrupadas por OT ( solo las que estan con estado de 'proceso' y con fecha corte)
        $muestrasPendientesTerminoPorOt = self::muestras_pendientes_termino_por_ot($verificar_muestras_fecha_con_dato, $fromDate, $toDate);

        return $muestrasPendientesTerminoPorOt;
    }

    public function id_muestras_pendientes_de_termino($fromDate, $toDate)
    {
        //Consulta de muestras en estado proceso -> 1
        $muestrasEstadoProceso = Muestra::where("work_order_id", "!=", "0")
            ->where("estado", "1")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();

        //------ Calculo de muestras pendientes termino ( solo las que estan con estado de proceso y con fecha corte )
        // NOTA: Lo que diferencia una muestra pendiente de corte con una muestra pendiente de termino, es que la de termino ya tiene una fecha de "termino" dependiendo de su destinatario
        $verificar_muestras_fecha_con_dato = self::verificar_muestras_fecha_con_dato($muestrasEstadoProceso);

        //Calculo de muestras pendientes corte agrupadas por su ID de registro ( solo las que estan con estado de 'proceso')
        $IdmuestrasPendientesDeTermino = Muestra::select(
            DB::raw("COUNT(DISTINCT id) AS cantidad_ot")
        )->where(function ($query) use ($verificar_muestras_fecha_con_dato) {
            foreach ($verificar_muestras_fecha_con_dato['columnas_destinatarios'] as $indice => $columna) {
                if ($indice == 0) {
                    $query->where(strval($columna), "!=",  NULL);
                } else {
                    $query->orWhere(strval($columna), "!=", NULL);
                }
            }
        })
            ->where("work_order_id", "!=", "0")
            ->where("estado", "1")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->pluck('cantidad_ot')
            ->first();

        return $IdmuestrasPendientesDeTermino;
    }

    public function muestras_pendientes_de_termino($fromDate, $toDate)
    {
        //Consulta de muestras en estado proceso -> 1
        $muestrasEstadoProceso = Muestra::where("work_order_id", "!=", "0")
            ->where("estado", "1")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();

        //------ Calculo de muestras pendientes de termino (cortadas) ( solo las que estan con estado de 'proceso' y tienen fecha de corte)
        $verificar_muestras_fecha_con_dato = self::verificar_muestras_fecha_con_dato($muestrasEstadoProceso);
        $muestrasPendientesDeTermino = $verificar_muestras_fecha_con_dato['cantidad'];

        return $muestrasPendientesDeTermino;
    }

    public function query_cantidad_ot_con_muestras($fromDate, $toDate)
    {


        //Consulta de muestras en estado proceso -> 1 y que hayan pasado durante el rango de fecha de búsqueda a Sala de muestra (estado 17)
        /*$ot_sala_muestra = Muestras::select(\DB::raw( 'distinct managements.id, managements.*'))
        ->join('muestras', 'muestras.work_order_id', '=', 'managements.work_order_id')
        ->where("muestras.work_order_id", "!=", "0")
        ->whereIn("muestras.estado", ["1", "3"])
        ->where("managements.state_id", "17")
        ->whereBetween('managements.created_at', [$fromDate, $toDate])
        // ->where(function($query){
        //     $query->whereNull('muestras.fecha_corte_vendedor')
        //         ->whereNull('muestras.fecha_corte_diseñador')
        //         ->whereNull('muestras.fecha_corte_laboratorio')
        //         ->whereNull('muestras.fecha_corte_1');
        //     })
        ->get(); */
        /*
        $ot_sala_muestra = Muestra::select(DB::raw("COUNT(DISTINCT work_order_id) AS cantidad_ot"))
                                    ->where("work_order_id", "!=", "0")
                                    ->whereBetween('created_at', [$fromDate, $toDate])
                                    ->pluck('cantidad_ot')
                                    ->first();*/
        $ot_numbers = [];
        $cantidad_ot = 0;
        //OT Con fechas de corte para vendedores dentro del periodo consultado
        $ot_sala_muestra_corte_vendedor = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_vendedor', [$fromDate, $toDate])
            //->pluck('cantidad_ot')
            ->get();
        foreach ($ot_sala_muestra_corte_vendedor as $ot_sala) {
            if (!in_array($ot_sala->work_order_id, $ot_numbers)) {
                $ot_numbers[] = $ot_sala->work_order_id;
                $cantidad_ot++;
            }
        }

        //OT Con fechas de corte para diseñador dentro del periodo consultado
        $ot_sala_muestra_corte_diseñador = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_diseñador', [$fromDate, $toDate])
            //->pluck('cantidad_ot')
            ->get();
        foreach ($ot_sala_muestra_corte_diseñador as $ot_sala) {
            if (!in_array($ot_sala->work_order_id, $ot_numbers)) {
                $ot_numbers[] = $ot_sala->work_order_id;
                $cantidad_ot++;
            }
        }

        //OT Con fechas de corte para laboratorio dentro del periodo consultado
        $ot_sala_muestra_corte_laboratorio = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_laboratorio', [$fromDate, $toDate])
            //->pluck('cantidad_ot')
            ->get();
        foreach ($ot_sala_muestra_corte_laboratorio as $ot_sala) {
            if (!in_array($ot_sala->work_order_id, $ot_numbers)) {
                $ot_numbers[] = $ot_sala->work_order_id;
                $cantidad_ot++;
            }
        }

        //OT Con fechas de corte para cliente 1 dentro del periodo consultado
        $ot_sala_muestra_corte_cliente_1 = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_1', [$fromDate, $toDate])
            //->pluck('cantidad_ot')
            ->get();
        foreach ($ot_sala_muestra_corte_cliente_1 as $ot_sala) {
            if (!in_array($ot_sala->work_order_id, $ot_numbers)) {
                $ot_numbers[] = $ot_sala->work_order_id;
                $cantidad_ot++;
            }
        }

        //OT Con fechas de corte para cliente 1 dentro del periodo consultado
        $ot_sala_muestra_corte_cliente_2 = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_2', [$fromDate, $toDate])
            //->pluck('cantidad_ot')
            ->get();
        foreach ($ot_sala_muestra_corte_cliente_2 as $ot_sala) {
            if (!in_array($ot_sala->work_order_id, $ot_numbers)) {
                $ot_numbers[] = $ot_sala->work_order_id;
                $cantidad_ot++;
            }
        }

        //OT Con fechas de corte para cliente 1 dentro del periodo consultado
        $ot_sala_muestra_corte_cliente_3 = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_3', [$fromDate, $toDate])
            //->pluck('cantidad_ot')
            ->get();
        foreach ($ot_sala_muestra_corte_cliente_3 as $ot_sala) {
            if (!in_array($ot_sala->work_order_id, $ot_numbers)) {
                $ot_numbers[] = $ot_sala->work_order_id;
                $cantidad_ot++;
            }
        }

        //OT Con fechas de corte para cliente 1 dentro del periodo consultado
        $ot_sala_muestra_corte_cliente_4 = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_4', [$fromDate, $toDate])
            //->pluck('cantidad_ot')
            ->get();
        foreach ($ot_sala_muestra_corte_cliente_4 as $ot_sala) {
            if (!in_array($ot_sala->work_order_id, $ot_numbers)) {
                $ot_numbers[] = $ot_sala->work_order_id;
                $cantidad_ot++;
            }
        }

        //dd($fromDate, $toDate,$cantidad_ot);

        /*dd($ot_sala_muestra);
        $cantidad_ot = [];
        foreach($ot_sala_muestra as $value){
            $cantidad_ot[] = $value->work_order_id;
        }*/

        $cantidad = $cantidad_ot;

        return $cantidad;
    }

    public function query_cantidad_id_solicitadas($fromDate, $toDate)
    {


        //Consulta de muestras en estado proceso -> 1 y que hayan pasado durante el rango de fecha de búsqueda a Sala de muestra (estado 17)
        /*$ot_sala_muestra = Muestras::select(\DB::raw( 'distinct managements.id, managements.*'))
        ->join('muestras', 'muestras.work_order_id', '=', 'managements.work_order_id')
        ->where("muestras.work_order_id", "!=", "0")
        ->whereIn("muestras.estado", ["1", "3"])
        ->where("managements.state_id", "17")
        ->whereBetween('managements.created_at', [$fromDate, $toDate])
        // ->where(function($query){
        //     $query->whereNull('muestras.fecha_corte_vendedor')
        //         ->whereNull('muestras.fecha_corte_diseñador')
        //         ->whereNull('muestras.fecha_corte_laboratorio')
        //         ->whereNull('muestras.fecha_corte_1');
        //     })
        ->get(); */

        /*$id_sala_muestra = Muestra::select(DB::raw("COUNT(DISTINCT id) AS cantidad_id"))
                                    ->where("work_order_id", "!=", "0")
                                    ->whereBetween('created_at', [$fromDate, $toDate])
                                    ->pluck('cantidad_id')
                                    ->first();*/
        /*dd($ot_sala_muestra);
        $cantidad_ot = [];
        foreach($ot_sala_muestra as $value){
            $cantidad_ot[] = $value->work_order_id;
        }*/
        //dd($id_sala_muestra);

        $id_numbers = [];
        $cantidad_id = 0;
        //OT Con fechas de corte para vendedores dentro del periodo consultado
        $ot_sala_muestra_corte_vendedor = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_vendedor', [$fromDate, $toDate])
            //->pluck('cantidad_ot')
            ->get();
        foreach ($ot_sala_muestra_corte_vendedor as $ot_sala) {
            if (!in_array($ot_sala->id, $id_numbers)) {
                $id_numbers[] = $ot_sala->id;
                $cantidad_id++;
            }
        }

        //OT Con fechas de corte para diseñador dentro del periodo consultado
        $ot_sala_muestra_corte_diseñador = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_diseñador', [$fromDate, $toDate])
            //->pluck('cantidad_id')
            ->get();
        foreach ($ot_sala_muestra_corte_diseñador as $ot_sala) {
            if (!in_array($ot_sala->id, $id_numbers)) {
                $id_numbers[] = $ot_sala->id;
                $cantidad_id++;
            }
        }

        //OT Con fechas de corte para laboratorio dentro del periodo consultado
        $ot_sala_muestra_corte_laboratorio = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_laboratorio', [$fromDate, $toDate])
            //->pluck('cantidad_id')
            ->get();
        foreach ($ot_sala_muestra_corte_laboratorio as $ot_sala) {
            if (!in_array($ot_sala->id, $id_numbers)) {
                $id_numbers[] = $ot_sala->id;
                $cantidad_id++;
            }
        }

        //OT Con fechas de corte para cliente 1 dentro del periodo consultado
        $ot_sala_muestra_corte_cliente_1 = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_1', [$fromDate, $toDate])
            //->pluck('cantidad_id')
            ->get();
        foreach ($ot_sala_muestra_corte_cliente_1 as $ot_sala) {
            if (!in_array($ot_sala->id, $id_numbers)) {
                $id_numbers[] = $ot_sala->id;
                $cantidad_id++;
            }
        }

        //OT Con fechas de corte para cliente 1 dentro del periodo consultado
        $ot_sala_muestra_corte_cliente_2 = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_2', [$fromDate, $toDate])
            //->pluck('cantidad_id')
            ->get();
        foreach ($ot_sala_muestra_corte_cliente_2 as $ot_sala) {
            if (!in_array($ot_sala->id, $id_numbers)) {
                $id_numbers[] = $ot_sala->id;
                $cantidad_id++;
            }
        }

        //OT Con fechas de corte para cliente 1 dentro del periodo consultado
        $ot_sala_muestra_corte_cliente_3 = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_3', [$fromDate, $toDate])
            //->pluck('cantidad_id')
            ->get();
        foreach ($ot_sala_muestra_corte_cliente_3 as $ot_sala) {
            if (!in_array($ot_sala->id, $id_numbers)) {
                $id_numbers[] = $ot_sala->id;
                $cantidad_id++;
            }
        }

        //OT Con fechas de corte para cliente 1 dentro del periodo consultado
        $ot_sala_muestra_corte_cliente_4 = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_4', [$fromDate, $toDate])
            //->pluck('cantidad_id')
            ->get();
        foreach ($ot_sala_muestra_corte_cliente_4 as $ot_sala) {
            if (!in_array($ot_sala->id, $id_numbers)) {
                $id_numbers[] = $ot_sala->id;
                $cantidad_id++;
            }
        }

        $cantidad = $cantidad_id;

        return $cantidad;
    }

    public function cantidad_muestras_solicitadas($fromDate, $toDate)
    {
        //Consulta de muestras en estado proceso -> 1 y con fechas NULL
        /*$muestras = Muestra::select(\DB::raw("
            DISTINCT
                muestras.id as muestra_id,
                COALESCE(muestras.cantidad_vendedor, 0)  AS cantidad_vendedor,
                COALESCE(muestras.cantidad_diseñador, 0) AS cantidad_disenador,
                COALESCE(muestras.cantidad_laboratorio, 0) AS cantidad_lab,
                COALESCE(muestras.cantidad_1, 0) AS cantidad_1"
            ))
            //->whereIn("estado", ["1", "3"])
            //->join('work_orders', 'work_orders.id', 'muestras.work_order_id')
            //->join('managements', 'work_orders.id', 'managements.work_order_id')
            //->where('managements.state_id', '17')
            //->whereBetween('managements.created_at', [$fromDate, $toDate])
            ->where("work_order_id", "!=", "0")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();


        $id_sala_muestra = Muestra::select(DB::raw("DISTINCT id) AS muestra_id"))
            ->where("work_order_id", "!=", "0")
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->pluck('cantidad_id')
            ->first();*/

        //$_numbers=[];
        $cantidad_muestras = 0;
        //OT Con fechas de corte para vendedores dentro del periodo consultado
        $ot_sala_muestra_corte_vendedor = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_vendedor', [$fromDate, $toDate])
            //->pluck('cantidad_ot')
            ->get();
        foreach ($ot_sala_muestra_corte_vendedor as $ot_sala) {
            // if(!in_array($ot_sala->id,$id_numbers)){
            //$id_numbers[]=$ot_sala->id;
            $cantidad_muestras += $ot_sala->cantidad_vendedor;
            // }
        }

        //OT Con fechas de corte para diseñador dentro del periodo consultado
        $ot_sala_muestra_corte_diseñador = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_diseñador', [$fromDate, $toDate])
            //->pluck('cantidad_id')
            ->get();
        foreach ($ot_sala_muestra_corte_diseñador as $ot_sala) {
            //if(!in_array($ot_sala->id,$id_numbers)){
            //  $id_numbers[]=$ot_sala->id;
            $cantidad_muestras += $ot_sala->cantidad_diseñador;
            //}
        }

        $ot_sala_muestra_corte_diseñador_revision = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_diseñador_revision', [$fromDate, $toDate])
            //->pluck('cantidad_id')
            ->get();
        foreach ($ot_sala_muestra_corte_diseñador_revision as $ot_sala) {
            //if(!in_array($ot_sala->id,$id_numbers)){
            //  $id_numbers[]=$ot_sala->id;
            $cantidad_muestras += $ot_sala->cantidad_diseñador_revision;
            //}
        }

        //OT Con fechas de corte para laboratorio dentro del periodo consultado
        $ot_sala_muestra_corte_laboratorio = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_laboratorio', [$fromDate, $toDate])
            //->pluck('cantidad_id')
            ->get();
        foreach ($ot_sala_muestra_corte_laboratorio as $ot_sala) {
            //if(!in_array($ot_sala->id,$id_numbers)){
            //  $id_numbers[]=$ot_sala->id;
            $cantidad_muestras += $ot_sala->cantidad_laboratorio;
            // }
        }

        //OT Con fechas de corte para cliente 1 dentro del periodo consultado
        $ot_sala_muestra_corte_cliente_1 = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_1', [$fromDate, $toDate])
            //->pluck('cantidad_id')
            ->get();
        foreach ($ot_sala_muestra_corte_cliente_1 as $ot_sala) {
            //if(!in_array($ot_sala->id,$id_numbers)){
            //  $id_numbers[]=$ot_sala->id;
            $cantidad_muestras += $ot_sala->cantidad_1;
            // }
        }

        //OT Con fechas de corte para cliente 1 dentro del periodo consultado
        $ot_sala_muestra_corte_cliente_2 = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_2', [$fromDate, $toDate])
            //->pluck('cantidad_id')
            ->get();
        foreach ($ot_sala_muestra_corte_cliente_2 as $ot_sala) {
            //if(!in_array($ot_sala->id,$id_numbers)){
            //  $id_numbers[]=$ot_sala->id;
            $cantidad_muestras += $ot_sala->cantidad_2;
            //}
        }

        //OT Con fechas de corte para cliente 1 dentro del periodo consultado
        $ot_sala_muestra_corte_cliente_3 = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_3', [$fromDate, $toDate])
            //->pluck('cantidad_id')
            ->get();
        foreach ($ot_sala_muestra_corte_cliente_3 as $ot_sala) {
            //if(!in_array($ot_sala->id,$id_numbers)){
            //  $id_numbers[]=$ot_sala->id;
            $cantidad_muestras += $ot_sala->cantidad_3;
            //}
        }

        //OT Con fechas de corte para cliente 1 dentro del periodo consultado
        $ot_sala_muestra_corte_cliente_4 = Muestra::where("work_order_id", "!=", "0")
            ->whereBetween('fecha_corte_4', [$fromDate, $toDate])
            //->pluck('cantidad_id')
            ->get();
        foreach ($ot_sala_muestra_corte_cliente_4 as $ot_sala) {
            // if(!in_array($ot_sala->id,$id_numbers)){
            //   $id_numbers[]=$ot_sala->id;
            $cantidad_muestras += $ot_sala->cantidad_4;
            //}
        }


        /*foreach ($muestras as $muestra) {
            $cantidad_muestras += $muestra->cantidad_vendedor + $muestra->cantidad_disenador + $muestra->cantidad_lab + $muestra->cantidad_1;
        }*/

        return $cantidad_muestras;
    }

    public function descargaReporteSalaMuestra($fromDate, $toDate, $titulo)
    {
        //Muestras Listas
        $ot_muestras_listas = Management::where('management_type_id', 1)
            ->where('state_id', 18)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->pluck('work_order_id')
            ->toArray();


        $muestras = DB::table('muestras')
            ->select(
                'id AS ID_MUESTRA',
                'work_order_id AS NUMERO_OT',
                'user_id AS ID_USUARIO',
                'cad AS CAD',
                'cad_id AS ID_CAD',
                'carton_id AS ID_CARTON',
                'pegado_id AS ID_PEGADO',
                'tiempo_unitario AS TIEMPO_UNITARIO',
                DB::raw("DATE_FORMAT(fecha_corte, '%d-%m-%Y %H:%i:%s ')AS FECHA_CORTE"),
                'destinatarios_id AS ID_DESTINATARIO',
                'cantidad_vendedor AS CANTIDAD_VENDEDOR',
                'comentario_vendedor AS COMENTARIO_VENDEDOR',
                'cantidad_diseñador AS CANTIDAD_DISEÑADOR',
                'comentario_diseñador AS COMENTARIO_DISEÑADOR',
                'cantidad_diseñador_revision AS CANTIDAD_DISEÑADOR_REVISION',
                'comentario_diseñador_revision AS COMENTARIO_DISEÑADOR_REVISION',
                'cantidad_laboratorio AS CANTIDAD_LABORATORIO',
                'comentario_laboratorio AS COMENTARIO_LABORATORIO',
                'destinatario_1 AS DESTINATARIO_CLIENTE_1',
                'comuna_1 AS COMUNA_CLIENTE_1',
                'direccion_1 AS DIRECCION_CLIENTE_1',
                'cantidad_1 AS CANTIDAD_CLIENTE_1',
                'comentario_1 AS COMENTARIO_CLIENTE_1',
                'destinatario_2 AS DESTINATARIO_CLIENTE_2',
                'comuna_2 AS COMUNA_CLIENTE_2',
                'direccion_2 AS DIRECCION_CLIENTE_2',
                'cantidad_2 AS CANTIDAD_CLIENTE_2',
                'comentario_2 AS COMENTARIO_CLIENTE_2',
                'destinatario_3 AS DESTINATARIO_CLIENTE_3',
                'comuna_3 AS COMUNA_CLIENTE_3',
                'direccion_3 AS DIRECCION_CLIENTE_3',
                'cantidad_3 AS CANTIDAD_CLIENTE_3',
                'comentario_3 AS COMENTARIO_CLIENTE_3',
                'destinatario_4 AS DESTINATARIO_CLIENTE_4',
                'comuna_4 AS COMUNA_CLIENTE_4',
                'direccion_4 AS DIRECCION_CLIENTE_4',
                'cantidad_4 AS CANTIDAD_CLIENTE_4',
                'comentario_4 AS COMENTARIO_CLIENTE_4',
                DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i:%s ')AS FECHA_CREACION"),
                DB::raw("DATE_FORMAT(updated_at, '%d-%m-%Y %H:%i:%s ')AS FECHA_ACTUALIZACION"),
                'numero_envio_1 AS NUMERO_ENVIO_CLIENTE_1',
                'numero_envio_2 AS NUMERO_ENVIO_CLIENTE_2',
                'numero_envio_3 AS NUMERO_ENVIO_CLIENTE_3',
                'numero_envio_4 AS NUMERO_ENVIO_CLIENTE_4',
                DB::raw("DATE_FORMAT(fecha_corte_vendedor, '%d-%m-%Y %H:%i:%s ')AS FECHA_CORTE_VENDEDOR"),
                DB::raw("DATE_FORMAT(fecha_corte_diseñador, '%d-%m-%Y %H:%i:%s ')AS FECHA_CORTE_DISEÑADOR"),
                DB::raw("DATE_FORMAT(fecha_corte_diseñador_revision, '%d-%m-%Y %H:%i:%s ')AS FECHA_CORTE_DISEÑADOR_REVISION"),
                DB::raw("DATE_FORMAT(fecha_corte_laboratorio, '%d-%m-%Y %H:%i:%s ')AS FECHA_CORTE_LABORATORIO"),
                DB::raw("DATE_FORMAT(fecha_corte_1, '%d-%m-%Y %H:%i:%s ')AS FECHA_CORTE_CLIENTE_1"),
                DB::raw("DATE_FORMAT(fecha_corte_2, '%d-%m-%Y %H:%i:%s ')AS FECHA_CORTE_CLIENTE_2"),
                DB::raw("DATE_FORMAT(fecha_corte_3, '%d-%m-%Y %H:%i:%s ')AS FECHA_CORTE_CLIENTE_3"),
                DB::raw("DATE_FORMAT(fecha_corte_4, '%d-%m-%Y %H:%i:%s ')AS FECHA_CORTE_CLIENTE_4"),
                'estado AS ESTADO',
                DB::raw("DATE_FORMAT(ultimo_cambio_estado, '%d-%m-%Y %H:%i:%s ')AS ULTIMO_CAMBIO_ESTADO"),
                'check_fecha_corte_1 AS CHECK_FECHA_CORTE_CLIENTE_1',
                'check_fecha_corte_2 AS CHECK_FECHA_CORTE_CLIENTE_2',
                'check_fecha_corte_3 AS CHECK_FECHA_CORTE_CLIENTE_3',
                'check_fecha_corte_4 AS CHECK_FECHA_CORTE_CLIENTE_4',
                'check_fecha_corte_vendedor AS CHECK_FECHA_CORTE_VENDEDOR',
                'check_fecha_corte_diseñador AS CHECK_FECHA_CORTE_DISEÑADOR',
                'check_fecha_corte_diseñador_revision AS CHECK_FECHA_CORTE_DISEÑADOR_REVISION',
                'check_fecha_corte_laboratorio AS CHECK_FECHA_CORTE_LABORATORIO',
                'prioritaria AS PRIORITARIA',
                'carton_muestra_id AS ID_CARTON_MUESTRA'
            )
            //->whereIn('work_order_id',$ot_muestras_listas)
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            //->whereBetween('created_at',[$fromDate, $toDate])
            ->get();

        $muestras_array[] = array(

            'ID_MUESTRA',
            'NUMERO_OT',
            'ID_USUARIO',
            'NOMBRE_USUARIO',
            'CAD',
            'ID_CAD',
            'ID_CARTON',
            'CARTON_CODIGO',
            'ID_PEGADO',
            'TIPO_PEGADO',
            'TIEMPO_UNITARIO',
            'FECHA_CORTE',
            'ID_DESTINATARIO',
            'CANTIDAD_VENDEDOR',
            'COMENTARIO_VENDEDOR',
            'CANTIDAD_DISEÑADOR',
            'COMENTARIO_DISEÑADOR',
            'CANTIDAD_DISEÑADOR_REVISION',
            'COMENTARIO_DISEÑADOR_REVISION',
            'CANTIDAD_LABORATORIO',
            'COMENTARIO_LABORATORIO',
            'DESTINATARIO_CLIENTE_1',
            'COMUNA_CLIENTE_1',
            'DIRECCION_CLIENTE_1',
            'CANTIDAD_CLIENTE_1',
            'COMENTARIO_CLIENTE_1',
            'DESTINATARIO_CLIENTE_2',
            'COMUNA_CLIENTE_2',
            'DIRECCION_CLIENTE_2',
            'CANTIDAD_CLIENTE_2',
            'COMENTARIO_CLIENTE_2',
            'DESTINATARIO_CLIENTE_3',
            'COMUNA_CLIENTE_3',
            'DIRECCION_CLIENTE_3',
            'CANTIDAD_CLIENTE_3',
            'COMENTARIO_CLIENTE_3',
            'DESTINATARIO_CLIENTE_4',
            'COMUNA_CLIENTE_4',
            'DIRECCION_CLIENTE_4',
            'CANTIDAD_CLIENTE_4',
            'COMENTARIO_CLIENTE_4',
            'FECHA_CREACION',
            'FECHA_ACTUALIZACION',
            'NUMERO_ENVIO_CLIENTE_1',
            'NUMERO_ENVIO_CLIENTE_2',
            'NUMERO_ENVIO_CLIENTE_3',
            'NUMERO_ENVIO_CLIENTE_4',
            'FECHA_CORTE_VENDEDOR',
            'FECHA_CORTE_DISEÑADOR',
            'FECHA_CORTE_DISEÑADOR_REVISION',
            'FECHA_CORTE_LABORATORIO',
            'FECHA_CORTE_CLIENTE_1',
            'FECHA_CORTE_CLIENTE_2',
            'FECHA_CORTE_CLIENTE_3',
            'FECHA_CORTE_CLIENTE_4',
            'ESTADO',
            'ULTIMO_CAMBIO_ESTADO',
            'CHECK_FECHA_CORTE_CLIENTE_1',
            'CHECK_FECHA_CORTE_CLIENTE_2',
            'CHECK_FECHA_CORTE_CLIENTE_3',
            'CHECK_FECHA_CORTE_CLIENTE_4',
            'CHECK_FECHA_CORTE_VENDEDOR',
            'CHECK_FECHA_CORTE_DISEÑADOR',
            'CHECK_FECHA_CORTE_DISEÑADOR_REVISION',
            'CHECK_FECHA_CORTE_LABORATORIO',
            'PRIORITARIA',
            'ID_CARTON_MUESTRA',
            'CARTON_MUESTRA'
        );

        $estados = ['Sin Asignar', 'En Proceso', 'Rechazada', 'Terminada', 'Eliminada/Anulada', 'Devuelta'];
        $cant_id = 0;
        $cant_id_cortadas = 0;
        $cant_m_cortadas_vendedor = 0;
        $cant_m_cortadas_diseñador = 0;
        $cant_m_cortadas_diseñador_revision = 0;
        $cant_m_cortadas_laboratorio = 0;
        $cant_m_cortadas_cliente = 0;
        $ot_muestras = array();

        foreach ($muestras as $muestra) {

            $usuario = User::where('id', $muestra->ID_USUARIO)->where('active', 1)->first();
            $nombre_usuario = ($usuario) ? $usuario->nombre . ' ' . $usuario->apellido : ' ';

            $carton = Carton::where('id', $muestra->ID_CARTON)->where('active', 1)->first();
            $carton_codigo = ($carton) ? $carton->codigo : ' ';

            $carton_muestra = Carton::where('id', $muestra->ID_CARTON_MUESTRA)->where('active', 1)->first();
            $carton_muestra_codigo = ($carton_muestra) ? $carton_muestra->codigo : ' ';

            $pegado = Pegado::where('id', $muestra->ID_PEGADO)->where('active', 1)->first();
            $tipo_pegado = ($pegado) ? $pegado->descripcion : ' ';

            $comuna_1 = CiudadesFlete::where('id', $muestra->COMUNA_CLIENTE_1)->where('active', 1)->first();
            $comuna_cliente_1 = ($comuna_1) ? $comuna_1->ciudad : ' ';

            $comuna_2 = CiudadesFlete::where('id', $muestra->COMUNA_CLIENTE_2)->where('active', 1)->first();
            $comuna_cliente_2 = ($comuna_2) ? $comuna_2->ciudad : ' ';

            $comuna_3 = CiudadesFlete::where('id', $muestra->COMUNA_CLIENTE_3)->where('active', 1)->first();
            $comuna_cliente_3 = ($comuna_3) ? $comuna_3->ciudad : ' ';

            $comuna_4 = CiudadesFlete::where('id', $muestra->COMUNA_CLIENTE_4)->where('active', 1)->first();
            $comuna_cliente_4 = ($comuna_4) ? $comuna_4->ciudad : ' ';

            $muestras_array[] = array(
                'ID_MUESTRA'                    => $muestra->ID_MUESTRA,
                'NUMERO_OT'                     => $muestra->NUMERO_OT,
                'ID_USUARIO'                    => $muestra->ID_USUARIO,
                'NOMBRE_USUARIO'                => $nombre_usuario,
                'CAD'                           => $muestra->CAD,
                'ID_CAD'                        => $muestra->ID_CAD,
                'ID_CARTON'                     => $muestra->ID_CARTON,
                'CARTON_CODIGO'                 => $carton_codigo,
                'ID_PEGADO'                     => $muestra->ID_PEGADO,
                'TIPO_PEGADO'                   => $tipo_pegado,
                'TIEMPO_UNITARIO'               => $muestra->TIEMPO_UNITARIO,
                'FECHA_CORTE'                   => $muestra->FECHA_CORTE,
                'ID_DESTINATARIO'               => $muestra->ID_DESTINATARIO,
                'CANTIDAD_VENDEDOR'             => $muestra->CANTIDAD_VENDEDOR,
                'COMENTARIO_VENDEDOR'           => $muestra->COMENTARIO_VENDEDOR,
                'CANTIDAD_DISEÑADOR'            => $muestra->CANTIDAD_DISEÑADOR,
                'COMENTARIO_DISEÑADOR'          => $muestra->COMENTARIO_DISEÑADOR,
                'CANTIDAD_DISEÑADOR_REVISION'   => $muestra->CANTIDAD_DISEÑADOR_REVISION,
                'COMENTARIO_DISEÑADOR_REVISION' => $muestra->COMENTARIO_DISEÑADOR_REVISION,
                'CANTIDAD_LABORATORIO'          => $muestra->CANTIDAD_LABORATORIO,
                'COMENTARIO_LABORATORIO'        => $muestra->COMENTARIO_LABORATORIO,
                'DESTINATARIO_CLIENTE_1'        => $muestra->DESTINATARIO_CLIENTE_1,
                'COMUNA_CLIENTE_1'              => $comuna_cliente_1,
                'DIRECCION_CLIENTE_1'           => $muestra->DIRECCION_CLIENTE_1,
                'CANTIDAD_CLIENTE_1'            => $muestra->CANTIDAD_CLIENTE_1,
                'COMENTARIO_CLIENTE_1'          => $muestra->COMENTARIO_CLIENTE_1,
                'DESTINATARIO_CLIENTE_2'        => $muestra->DESTINATARIO_CLIENTE_2,
                'COMUNA_CLIENTE_2'              => $comuna_cliente_2,
                'DIRECCION_CLIENTE_2'           => $muestra->DIRECCION_CLIENTE_2,
                'CANTIDAD_CLIENTE_2'            => $muestra->CANTIDAD_CLIENTE_2,
                'COMENTARIO_CLIENTE_2'          => $muestra->COMENTARIO_CLIENTE_2,
                'DESTINATARIO_CLIENTE_3'        => $muestra->DESTINATARIO_CLIENTE_3,
                'COMUNA_CLIENTE_3'              => $comuna_cliente_3,
                'DIRECCION_CLIENTE_3'           => $muestra->DIRECCION_CLIENTE_3,
                'CANTIDAD_CLIENTE_3'            => $muestra->CANTIDAD_CLIENTE_3,
                'COMENTARIO_CLIENTE_3'          => $muestra->COMENTARIO_CLIENTE_3,
                'DESTINATARIO_CLIENTE_4'        => $muestra->DESTINATARIO_CLIENTE_4,
                'COMUNA_CLIENTE_4'              => $comuna_cliente_4,
                'DIRECCION_CLIENTE_4'           => $muestra->DIRECCION_CLIENTE_4,
                'CANTIDAD_CLIENTE_4'            => $muestra->CANTIDAD_CLIENTE_4,
                'COMENTARIO_CLIENTE_4'          => $muestra->COMENTARIO_CLIENTE_4,
                'FECHA_CREACION'                => $muestra->FECHA_CREACION,
                'FECHA_ACTUALIZACION'           => $muestra->FECHA_ACTUALIZACION,
                'NUMERO_ENVIO_CLIENTE_1'        => $muestra->NUMERO_ENVIO_CLIENTE_1,
                'NUMERO_ENVIO_CLIENTE_2'        => $muestra->NUMERO_ENVIO_CLIENTE_2,
                'NUMERO_ENVIO_CLIENTE_3'        => $muestra->NUMERO_ENVIO_CLIENTE_3,
                'NUMERO_ENVIO_CLIENTE_4'        => $muestra->NUMERO_ENVIO_CLIENTE_4,
                'FECHA_CORTE_VENDEDOR'          => $muestra->FECHA_CORTE_VENDEDOR,
                'FECHA_CORTE_DISEÑADOR'         => $muestra->FECHA_CORTE_DISEÑADOR,
                'FECHA_CORTE_DISEÑADOR_REVISION' => $muestra->FECHA_CORTE_DISEÑADOR_REVISION,
                'FECHA_CORTE_LABORATORIO'       => $muestra->FECHA_CORTE_LABORATORIO,
                'FECHA_CORTE_CLIENTE_1'         => $muestra->FECHA_CORTE_CLIENTE_1,
                'FECHA_CORTE_CLIENTE_2'         => $muestra->FECHA_CORTE_CLIENTE_2,
                'FECHA_CORTE_CLIENTE_3'         => $muestra->FECHA_CORTE_CLIENTE_3,
                'FECHA_CORTE_CLIENTE_4'         => $muestra->FECHA_CORTE_CLIENTE_4,
                'ESTADO'                        => $estados[$muestra->ESTADO],
                'ULTIMO_CAMBIO_ESTADO'          => $muestra->ULTIMO_CAMBIO_ESTADO,
                'CHECK_FECHA_CORTE_CLIENTE_1'   => $muestra->CHECK_FECHA_CORTE_CLIENTE_1,
                'CHECK_FECHA_CORTE_CLIENTE_2'   => $muestra->CHECK_FECHA_CORTE_CLIENTE_2,
                'CHECK_FECHA_CORTE_CLIENTE_3'   => $muestra->CHECK_FECHA_CORTE_CLIENTE_3,
                'CHECK_FECHA_CORTE_CLIENTE_4'   => $muestra->CHECK_FECHA_CORTE_CLIENTE_4,
                'CHECK_FECHA_CORTE_VENDEDOR'    => $muestra->CHECK_FECHA_CORTE_VENDEDOR,
                'CHECK_FECHA_CORTE_DISEÑADOR'   => $muestra->CHECK_FECHA_CORTE_DISEÑADOR,
                'CHECK_FECHA_CORTE_DISEÑADOR_REVISION'  => $muestra->CHECK_FECHA_CORTE_DISEÑADOR_REVISION,
                'CHECK_FECHA_CORTE_LABORATORIO' => $muestra->CHECK_FECHA_CORTE_LABORATORIO,
                'PRIORITARIA'                   => $muestra->PRIORITARIA,
                'ID_CARTON_MUESTRA'             => $muestra->ID_CARTON_MUESTRA,
                'CARTON_MUESTRA'                => $carton_muestra_codigo
            );

            if (!is_null($muestra->FECHA_CORTE_VENDEDOR)) {
                $cant_m_cortadas_vendedor += (int)$muestra->CANTIDAD_VENDEDOR;
            }

            if (!is_null($muestra->FECHA_CORTE_DISEÑADOR)) {
                $cant_m_cortadas_diseñador += (int)$muestra->CANTIDAD_DISEÑADOR;
            }

            if (!is_null($muestra->FECHA_CORTE_DISEÑADOR_REVISION)) {
                $cant_m_cortadas_diseñador_revision += (int)$muestra->CANTIDAD_DISEÑADOR_REVISION;
            }

            if (!is_null($muestra->FECHA_CORTE_LABORATORIO)) {
                $cant_m_cortadas_laboratorio += (int)$muestra->CANTIDAD_LABORATORIO;
            }

            if (!is_null($muestra->FECHA_CORTE_CLIENTE_1)) {
                $cant_m_cortadas_cliente += (int)$muestra->CANTIDAD_CLIENTE_1;
            }

            if (!is_null($muestra->FECHA_CORTE_CLIENTE_2)) {
                $cant_m_cortadas_cliente += (int)$muestra->CANTIDAD_CLIENTE_2;
            }

            if (!is_null($muestra->FECHA_CORTE_CLIENTE_3)) {
                $cant_m_cortadas_cliente += (int)$muestra->CANTIDAD_CLIENTE_3;
            }

            if (!is_null($muestra->FECHA_CORTE_CLIENTE_4)) {
                $cant_m_cortadas_cliente += (int)$muestra->CANTIDAD_CLIENTE_4;
            }

            if ((!is_null($muestra->FECHA_CORTE_VENDEDOR)) ||
                (!is_null($muestra->FECHA_CORTE_DISEÑADOR)) ||
                (!is_null($muestra->FECHA_CORTE_DISEÑADOR_REVISION)) ||
                (!is_null($muestra->FECHA_CORTE_LABORATORIO)) ||
                (!is_null($muestra->FECHA_CORTE_CLIENTE_1)) ||
                (!is_null($muestra->FECHA_CORTE_CLIENTE_2)) ||
                (!is_null($muestra->FECHA_CORTE_CLIENTE_3)) ||
                (!is_null($muestra->FECHA_CORTE_CLIENTE_4))
            ) {

                $cant_id_cortadas++;
            }

            $cant_id++;

            if (!in_array($muestra->NUMERO_OT, $ot_muestras)) {
                $ot_muestras[] = $muestra->NUMERO_OT;
            }
        }

        $total_m_cortadas = $cant_m_cortadas_cliente + $cant_m_cortadas_laboratorio + $cant_m_cortadas_diseñador + $cant_m_cortadas_diseñador_revision + $cant_m_cortadas_vendedor;

        $resumen_muestras[] = array(
            'CANTIDAD ID',
            'CANTIDAD OT',
            'ID CORTADAS',
            'M. CORTADAS VENDEDOR',
            'M. CORTADAS DISEÑADOR',
            'M. CORTADAS DISEÑADOR REVISION',
            'M. CORTADAS LABORATORIO',
            'M. CORTADAS CLIENTES',
            'TOTAL MUESTRAS CORTADAS'
        );

        $resumen_muestras[] = array(
            'CANTIDAD ID'               => $cant_id,
            'CANTIDAD OT'               => count($ot_muestras),
            'ID CORTADAS'               => $cant_id_cortadas,
            'M. CORTADAS VENDEDOR'      => $cant_m_cortadas_vendedor,
            'M. CORTADAS DISEÑADOR'     => $cant_m_cortadas_diseñador,
            'M. CORTADAS DISEÑADOR REVISION' => $cant_m_cortadas_diseñador_revision,
            'M. CORTADAS LABORATORIO'   => $cant_m_cortadas_laboratorio,
            'M. CORTADAS CLIENTES'      => $cant_m_cortadas_cliente,
            'TOTAL MUESTRAS CORTADAS'   => $total_m_cortadas
        );

        Excel::create($titulo, function ($excel) use ($muestras_array, $resumen_muestras, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet($titulo, function ($sheet) use ($muestras_array, $resumen_muestras) {
                $sheet->mergeCells('A1:M1');
                $sheet->row(1, array('RESUMEN CANTIDADES MUESTRAS DEL MES'));
                $sheet->rows($resumen_muestras);
                $sheet->mergeCells('A4:BL4');
                $sheet->row(4, array(' '));
                $sheet->mergeCells('A5:BL5');
                $sheet->row(5, array('DETALLE INFORMACION MUESTRAS DEL MES'));
                $sheet->rows($muestras_array);

                $sheet->cell('A1', function ($cell) {
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(12);
                });

                $sheet->cell('A5', function ($cell) {
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(12);
                });

                $sheet->cells('A3:H3', function ($cells) {
                    $cells->setAlignment('center');
                });
            });
        })->download('xlsx');
    }

    public function descargaReporteTiempoPrimeraMuestra($fromDate, $toDate, $titulo)
    {

        $muestras_array[] = array(
            'Ot',
            'Fecha y hora  Creación OT',
            'Fecha y hora  Ingreso DE',
            'Fecha y hora  Termino Primera Muestra',
            'Duración Días desde Creación',
            'Duración Días desde Ingreso DE'
        );

        /*$ots_periodo_muestras_listas=Management::select('work_order_id')
                                                ->where('state_id',18)
                                                ->whereBetween('created_at', [$fromDate, $toDate])
                                                ->groupBy("work_order_id")
                                                ->orderBy("work_order_id","Asc")
                                                ->get();   */

        $ots_periodo_muestras_listas = Muestra::select('work_order_id')
            ->where('estado', 3)
            ->whereIN('destinatarios_id', ['["4"]', '["1"]', '["2"]'])
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->groupBy("work_order_id")
            ->orderBy("work_order_id", "Asc")
            ->get();

        foreach ($ots_periodo_muestras_listas as $ot_muestra) {



            $fecha_termino_primera_muestra = Muestra::where("work_order_id", $ot_muestra->work_order_id)
                ->where('estado', 3)
                ->whereIN('destinatarios_id', ['["4"]', '["1"]', '["2"]'])
                //->whereBetween('created_at', [$fromDateAux, $toDateAux])
                ->orderBy("ultimo_cambio_estado", "Asc")
                ->first();


            if ($fecha_termino_primera_muestra && $fromDate <= $fecha_termino_primera_muestra->ultimo_cambio_estado && $fecha_termino_primera_muestra->ultimo_cambio_estado <= $toDate) {

                /*$ots_estado_muestra_lista=Management::where("work_order_id",$ot_muestra->work_order_id)
                                                    ->where('state_id',18)
                                                    ->orderBy("created_at","Asc")
                                                    ->first();

                //if($fromDate<=$ots_estado_muestra_lista->created_at && $ots_estado_muestra_lista->created_at<=$toDate){

                //Verifica Fecha de termino de muestra envio vendedor o cliente
                $fecha_termino_primera_muestra=Muestra::where("work_order_id", $ot_muestra->work_order_id)
                                                    ->where('estado',3)
                                                    ->whereIN('destinatarios_id',['["4"]','["1"]'])
                                                    ->orderBy("ultimo_cambio_estado","Asc")
                                                    ->first();*/

                // if($fecha_termino_primera_muestra){

                //Fecha Creacion OT
                $fecha_creacion_ot = Management::where("work_order_id", $ot_muestra->work_order_id)
                    ->where("management_type_id", 1)
                    ->where("state_id", 1)
                    ->orderBy("created_at", "Asc")
                    ->first();
                $tiempo_desde_creación = get_working_hours_muestra($fecha_creacion_ot->created_at, $fecha_termino_primera_muestra->ultimo_cambio_estado) / 11.5;

                //Fecha Entra en Area de Diseño Estructurtal
                $fecha_ingreso_de_ot = Management::where("work_order_id", $ot_muestra->work_order_id)
                    ->where("management_type_id", 1)
                    ->where("state_id", 2)
                    ->orderBy("created_at", "Asc")
                    ->first();

                $tiempo_desde_DE = get_working_hours_muestra($fecha_ingreso_de_ot->created_at, $fecha_termino_primera_muestra->ultimo_cambio_estado) / 11.5;

                $date_muestra = date_create($fecha_termino_primera_muestra->ultimo_cambio_estado);
                $fecha_muestra = date_format($date_muestra, 'd/m/Y H:i:s');

                $date_creacion = date_create($fecha_creacion_ot->created_at);
                $fecha_creacion = date_format($date_creacion, 'd/m/Y H:i:s');

                $date_de = date_create($fecha_ingreso_de_ot->created_at);
                $fecha_de = date_format($date_de, 'd/m/Y H:i:s');

                $muestras_array[] = array(
                    'Ot'    => $ot_muestra->work_order_id,
                    'Fecha y hora  Creación OT' => $fecha_creacion,
                    'Fecha y hora  Ingreso DE'  => $fecha_de,
                    'Fecha y hora  Termino Primera Muestra' => $fecha_muestra,
                    'Duración Días desde Creación' =>   round($tiempo_desde_creación, 2),
                    'Duración Días desde Ingreso DE' =>  round($tiempo_desde_DE, 2)
                );
            }
        }

        //dd($muestras_array);

        Excel::create($titulo, function ($excel) use ($muestras_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet($titulo, function ($sheet) use ($muestras_array) {
                $sheet->fromArray($muestras_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    public function descargaReporteTiempoDisenoGrafico($fromDate, $toDate, $titulo)
    {

        $result_array[] = array(
            'Ot',
            'Diseñador CMPC',
            'Fecha Ingreso Diseño Grafico',
            'Diseñador Externo',
            'Fecha Envio Diseñador Externo',
            'Fecha Entrega Diseñador Externo',
            'Fecha que pasa Diseño a precatalogar',
            'Tiempo respuesta Diseñador Externo',
            'Tiempo respuesta Diseñador Grafico'

        );

        $array_ot_procesadas = array();
        $ot_array = array();

        $ots_proveedor_periodo = Management::select('work_order_id')
            ->where('state_id', 6)
            ->where('management_type_id', 1)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->groupBy('work_order_id')
            ->orderBy('work_order_id', 'Asc')
            ->get();

        foreach ($ots_proveedor_periodo as $ot) {

            $ingreso_precatalogacion = Management::where('work_order_id', $ot->work_order_id)
                ->where('management_type_id', 1)
                ->where('state_id', 6)
                ->orderBy('created_at', 'Asc')
                ->first();
            if ($ingreso_precatalogacion->created_at >= $fromDate && $ingreso_precatalogacion->created_at <= $toDate) {
                $ot_array[] = $ot->work_order_id;
            }
        }

        $ots_proveedor_periodo = Management::where('management_type_id', 9)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->whereIN('work_order_id', $ot_array)
            ->orderBy('work_order_id', 'Asc')
            ->get();

        foreach ($ots_proveedor_periodo as $ot_proveedor) {
            if (!in_array($ot_proveedor->work_order_id, $array_ot_procesadas)) {


                $fecha_entrega_diseñador_externo = '';
                $tiempo_respuesta_diseño_externo = '';
                $tiempo_respuesta_diseño_grafico = '';

                $id_diseñador_cmpc = UserWorkOrder::where('work_order_id', $ot_proveedor->work_order_id)
                    ->where('area_id', 3)
                    ->first();

                $diseñador_cmpc = User::where('id', $id_diseñador_cmpc->user_id)->first();

                $ingreso_diseño_grafico = Management::where('work_order_id', $ot_proveedor->work_order_id)
                    ->where('management_type_id', 1)
                    ->where('state_id', 5)
                    ->orderBy('created_at', 'Asc')
                    ->first();

                $proveedor = Proveedor::where('deleted', 0)->where('id', $ot_proveedor->proveedor_id)->first();

                if ($ot_proveedor->recibido_diseño_externo != 0) {

                    $recepcion_proveedor_prinflex = Management::where('management_type_id', 10)
                        ->where('gestion_id', $ot_proveedor->id)
                        ->first();

                    $tiempo_respuesta_diseño_externo = round((get_working_hours($ot_proveedor->created_at, $recepcion_proveedor_prinflex->created_at) / 9.5), 2);

                    $fecha_entrega_diseñador_externo = date_format(date_create($recepcion_proveedor_prinflex->created_at), 'd/m/Y H:i:s');
                }

                $ingreso_precatalogacion = Management::where('work_order_id', $ot_proveedor->work_order_id)
                    ->where('management_type_id', 1)
                    ->where('state_id', 6)
                    ->orderBy('created_at', 'Asc')
                    ->first();
                if ($ingreso_precatalogacion) {
                    if ($ingreso_diseño_grafico) {
                        $tiempo_respuesta_diseño_grafico = round((get_working_hours($ingreso_diseño_grafico->created_at, $ingreso_precatalogacion->created_at) / 9.5), 2);
                    }
                }

                $result_array[] = array(
                    'Ot'                                    => $ot_proveedor->work_order_id,
                    'Diseñador CMPC'                        => $diseñador_cmpc->nombre . ' ' . $diseñador_cmpc->apellido,
                    'Fecha Ingreso Diseño Grafico'          => ($ingreso_diseño_grafico) ? date_format(date_create($ingreso_diseño_grafico->created_at), 'd/m/Y H:i:s') : '',
                    'Diseñador Externo'                     => $proveedor->name,
                    'Fecha Envio Diseñador Externo'         => date_format(date_create($ot_proveedor->created_at), 'd/m/Y H:i:s'),
                    'Fecha Entrega Diseñador Externo'       => $fecha_entrega_diseñador_externo,
                    'Fecha que pasa Diseño a precatalogar'  => ($ingreso_precatalogacion) ? date_format(date_create($ingreso_precatalogacion->created_at), 'd/m/Y H:i:s') : '',
                    'Tiempo respuesta Diseñador Externo'    => $tiempo_respuesta_diseño_externo,
                    'Tiempo respuesta Diseñador Grafico'    => $tiempo_respuesta_diseño_grafico
                );

                $array_ot_procesadas[] = $ot_proveedor->work_order_id;
            }
        }





        Excel::create($titulo, function ($excel) use ($result_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet($titulo, function ($sheet) use ($result_array) {
                $sheet->fromArray($result_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    public function cantidad_ot_cortadas_terminadas($fromDate, $toDate)
    {
        //Consulta de muestras en estado proceso -> 1 y tengan datos de fecha de corte
        //dd($fromDate, $toDate);
        /*$ot_sala_muestra = Muestra::select(DB::raw("COUNT(DISTINCT work_order_id) AS cantidad_ot"))
                                    ->where("work_order_id", "!=", "0")
                                    //->whereBetween('created_at', [$fromDate, $toDate])
                                    ->whereBetween('created_at', [$fromDate, $toDate])
                                    /*->whereNotNull('fecha_corte_vendedor')
                                    ->orWhereNotNull('fecha_corte_diseñador')
                                    ->orWhereNotNull('fecha_corte_laboratorio')
                                    ->orWhereNotNull('fecha_corte_1')
                                    ->whereBetween('fecha_corte_vendedor', [$fromDate, $toDate])
                                    ->orWhereBetween('fecha_corte_diseñador', [$fromDate, $toDate])
                                    ->orWhereBetween('fecha_corte_laboratorio', [$fromDate, $toDate])
                                    ->orWhereBetween('fecha_corte_1', [$fromDate, $toDate])
                                    ->pluck('cantidad_ot')
                                    ->first();*/

        $muestras = Muestra::where("work_order_id", "!=", "0")
            //->whereIn("estado", ["1", "3"])
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where("estado", "3")
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            /*->whereBetween('fecha_corte_vendedor', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_diseñador', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_laboratorio', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_1', [$fromDate, $toDate])*/
            /*->where(function($query) use ($fromDate, $toDate){
            $query->whereBetween('fecha_corte_vendedor', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_diseñador', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_laboratorio', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_1', [$fromDate, $toDate]);

            })*/
            ->get();

        $destinatarios = array();
        $cantidad_ot = [];

        foreach ($muestras as $muestra) {
            if (!in_array($muestra->destinatarios_id[0], $destinatarios)) {
                $destinatarios[] = $muestra->destinatarios_id[0];
            }

            if ($muestra->destinatarios_id[0] == 1 && $muestra->check_fecha_corte_vendedor != NULL && $muestra->fecha_corte_vendedor != NULL) {
                $cantidad_ot[] = $muestra->work_order_id;
            } elseif ($muestra->destinatarios_id[0] == 2 && $muestra->check_fecha_corte_diseñador != NULL && $muestra->fecha_corte_diseñador != NULL) {
                $cantidad_ot[] = $muestra->work_order_id;
            } elseif ($muestra->destinatarios_id[0] == 3 &&  $muestra->check_fecha_corte_laboratorio != NULL && $muestra->fecha_corte_laboratorio != NULL) {
                $cantidad_ot[] = $muestra->work_order_id;
            } elseif ($muestra->destinatarios_id[0] == 4 && ($muestra->check_fecha_corte_1 != NULL || $muestra->check_fecha_corte_2 != NULL || $muestra->check_fecha_corte_3 != NULL || $muestra->check_fecha_corte_4 != NULL) && $muestra->fecha_corte_1 != NULL) {
                $cantidad_ot[] = $muestra->work_order_id;
            } elseif ($muestra->destinatarios_id[0] == 5 && $muestra->check_fecha_corte_diseñador_revision != NULL && $muestra->fecha_corte_diseñador_revision != NULL) {
                $cantidad_ot[] = $muestra->work_order_id;
            }
        }

        $ots = count(array_unique($cantidad_ot));

        return array(
            'cantidad' =>  $ots,
        );
    }

    public function cantidad_id_cortadas_terminadas($fromDate, $toDate)
    {
        //Consulta de muestras en estado proceso -> 1 y tengan datos de fecha de corte
        $muestras = Muestra::where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where("estado", "3")
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            /*->where(function($query) use ($fromDate, $toDate){
            $query->whereBetween('fecha_corte_vendedor', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_diseñador', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_laboratorio', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_1', [$fromDate, $toDate])
            })*/
            ->get();

        $destinatarios = array();
        $cantidad_id = [];

        foreach ($muestras as $muestra) {
            if (!in_array($muestra->destinatarios_id[0], $destinatarios)) {
                $destinatarios[] = $muestra->destinatarios_id[0];
            }

            if ($muestra->destinatarios_id[0] == 1 && $muestra->check_fecha_corte_vendedor != NULL && $muestra->fecha_corte_vendedor != NULL) {
                $cantidad_id[] = $muestra->id;
            } elseif ($muestra->destinatarios_id[0] == 2 && $muestra->check_fecha_corte_diseñador != NULL && $muestra->fecha_corte_diseñador != NULL) {
                $cantidad_id[] = $muestra->id;
            } elseif ($muestra->destinatarios_id[0] == 3 &&  $muestra->check_fecha_corte_laboratorio != NULL && $muestra->fecha_corte_laboratorio != NULL) {
                $cantidad_id[] = $muestra->id;
            } elseif ($muestra->destinatarios_id[0] == 4 && ($muestra->check_fecha_corte_1 != NULL || $muestra->check_fecha_corte_2 != NULL || $muestra->check_fecha_corte_3 != NULL || $muestra->check_fecha_corte_4 != NULL) && $muestra->fecha_corte_1 != NULL) {
                $cantidad_id[] = $muestra->id;
            } elseif ($muestra->destinatarios_id[0] == 5 && $muestra->check_fecha_corte_diseñador_revision != NULL && $muestra->fecha_corte_diseñador_revision != NULL) {
                $cantidad_id[] = $muestra->id;
            }
        }

        $ids = count(array_unique($cantidad_id));

        return array(
            'cantidad' =>  $ids,
        );
    }

    public function cantidad_muestras_cortadas_terminadas($fromDate, $toDate)
    {


        $cantidad_muestras = 0;
        //Consulta de muestras en estado proceso -> 1 y tengan datos de fecha de corte
        $muestras_vendedor = Muestra::select(DB::raw("SUM(cantidad_vendedor) as cantidad_vendedor"))
            ->where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_vendedor')
            ->get();
        $cantidad_muestras += $muestras_vendedor[0]->cantidad_vendedor;

        $muestras_diseñador = Muestra::select(DB::raw("SUM(cantidad_diseñador) as cantidad_diseñador"))
            ->where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_diseñador')
            ->get();
        $cantidad_muestras += $muestras_diseñador[0]->cantidad_diseñador;

        $muestras_diseñador_revision = Muestra::select(DB::raw("SUM(cantidad_diseñador_revision) as cantidad_diseñador_revision"))
            ->where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_diseñador_revision')
            ->get();
        $cantidad_muestras += $muestras_diseñador_revision[0]->cantidad_diseñador_revision;

        $muestras_laboratorio = Muestra::select(DB::raw("SUM(cantidad_laboratorio) as cantidad_laboratorio"))
            ->where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_laboratorio')
            ->get();
        $cantidad_muestras += $muestras_laboratorio[0]->cantidad_laboratorio;

        $muestras_cliente_1 = Muestra::select(DB::raw("SUM(cantidad_1) as cantidad_1"))
            ->where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_1')
            ->get();
        $cantidad_muestras += $muestras_cliente_1[0]->cantidad_1;

        $muestras_cliente_2 = Muestra::select(DB::raw("SUM(cantidad_2) as cantidad_2"))
            ->where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_2')
            ->get();
        $cantidad_muestras += $muestras_cliente_2[0]->cantidad_2;

        $muestras_cliente_3 = Muestra::select(DB::raw("SUM(cantidad_3) as cantidad_3"))
            ->where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_3')
            ->get();
        $cantidad_muestras += $muestras_cliente_3[0]->cantidad_3;

        $muestras_cliente_4 = Muestra::select(DB::raw("SUM(cantidad_4) as cantidad_4"))
            ->where("work_order_id", "!=", "0")
            //->whereBetween('created_at', [$fromDate, $toDate])
            ->where('estado', 3)
            ->whereBetween('ultimo_cambio_estado', [$fromDate, $toDate])
            ->WhereNotNull('fecha_corte_4')
            ->get();
        $cantidad_muestras += $muestras_cliente_4[0]->cantidad_4;
        //->whereIn("estado", ["1", "3"])
        /*->where(function($query) use ($fromDate, $toDate){
            $query->whereBetween('fecha_corte_vendedor', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_diseñador', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_laboratorio', [$fromDate, $toDate])
                ->orWhereBetween('fecha_corte_1', [$fromDate, $toDate]);
            })*/


        //$destinatarios = array();


        /*foreach ($muestras as $muestra) {
            /*if (!in_array($muestra->destinatarios_id[0], $destinatarios)) {
                $destinatarios[] = $muestra->destinatarios_id[0];
            }

            if ($muestra->destinatarios_id[0] == 1 && $muestra->check_fecha_corte_vendedor != NULL && $muestra->fecha_corte_vendedor != NULL) {
                $cantidad_muestras += $muestra->cantidad_vendedor;
            } elseif ($muestra->destinatarios_id[0] == 2 && $muestra->check_fecha_corte_diseñador != NULL && $muestra->fecha_corte_diseñador != NULL) {
                $cantidad_muestras += $muestra->cantidad_diseñador;
            } elseif ($muestra->destinatarios_id[0] == 3 &&  $muestra->check_fecha_corte_laboratorio != NULL && $muestra->fecha_corte_laboratorio != NULL) {
                $cantidad_muestras += $muestra->cantidad_laboratorio;
            } elseif ($muestra->destinatarios_id[0] == 4 && ($muestra->check_fecha_corte_1 != NULL || $muestra->check_fecha_corte_2 != NULL || $muestra->check_fecha_corte_3 != NULL || $muestra->check_fecha_corte_4 != NULL) && $muestra->fecha_corte_1 != NULL) {
                $cantidad_muestras += $muestra->cantidad_1;
            }
            if(!is_null($muestra->fecha_corte_vendedor)){
                $cantidad_muestras += $muestra->cantidad_vendedor;
            }
            if(!is_null($muestra->fecha_corte_diseñador)){
                $cantidad_muestras += $muestra->cantidad_diseñador;
            }
            if(!is_null($muestra->fecha_corte_laboratorio)){
                $cantidad_muestras += $muestra->cantidad_laboratorio;
            }
            if(!is_null($muestra->fecha_corte_1)){
                $cantidad_muestras += $muestra->cantidad_1;
            }
            if(!is_null($muestra->fecha_corte_2)){
                $cantidad_muestras += $muestra->cantidad_2;
            }
            if(!is_null($muestra->fecha_corte_3)){
                $cantidad_muestras += $muestra->cantidad_3;
            }
            if(!is_null($muestra->fecha_corte_4)){
                $cantidad_muestras += $muestra->cantidad_4;
            }
        }*/
        /*
        if($fromDate=='2023-10-01 00:00:00' && $toDate== '2023-10-31 23:59:59'){
            dd( $cantidad_muestras,$muestras_vendedor[0]->cantidad_vendedor,
                $muestras_diseñador[0]->cantidad_diseñador,$muestras_laboratorio[0]->cantidad_laboratorio,
                $muestras_cliente_1[0]->cantidad_1,$muestras_cliente_2[0]->cantidad_2,
                $muestras_cliente_3[0]->cantidad_3,$muestras_cliente_4[0]->cantidad_4);
        }*/

        return $cantidad_muestras;
    }

    public function reportTiempoDisenadorExternoAjuste()
    {
        // ------------------- FILTRO DE BUSQUEDA
        //año:
        $year_ini = date('Y') + 0;
        $year_fin = 2020;
        $years = [];
        for ($i = $year_ini; $i >= $year_fin; $i--) {
            $years[] = $i;
        }

        //---- Se busca los meses anteriores al seleccionado en la vista para realizar la busqueda
        $nombreMeses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        // Carbon::setLocale('es');
        // // Filtro por fechas
        if (!is_null(request()->input('mes')) and !is_null(request()->input('year'))) {
            $mes = request()->input('mes');
            $year = request()->input('year')[0];
            $yearSeleccionado = request()->input('year')[0];
        } else {
            $mes = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
            $yearSeleccionado = Carbon::now()->format('Y');
        }
        //$hoy=Carbon::now()->format('Y-m-d');
        $fromDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->startOfMonth();
        $toDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->endOfMonth();
        $mesSeleccionado = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->format('Y-m');
        //dd($mes,$yearSeleccionado);
        $meses = [];
        $nombreMesesSeleccionados = [];
        $solicitudesTotalesUltimosMeses = [0, 0, 0, 0, 0];

        for ($i = 4; $i >= 0; $i--) {
            $meses[] = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('Y-m');
            $nombreMesesSeleccionados[] = $nombreMeses[Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . '1')->subMonth($i)->format('m') - 1];
        }

        // Si se solicito exportar
        $cantidad_envio_prinflex = 0;
        $cantidad_envio_graphicbox = 0;
        $cantidad_envio_flexoclean = 0;
        $cantidad_envio_artfactory = 0;

        $cantidad_enviadas_prinflex = 0;
        $cantidad_enviadas_graphicbox = 0;
        $cantidad_enviadas_flexoclean = 0;
        $cantidad_enviadas_artfactory = 0;

        $cantidad_pendiente_prinflex = 0;
        $cantidad_pendiente_graphicbox = 0;
        $cantidad_pendiente_flexoclean = 0;
        $cantidad_pendiente_artfactory = 0;

        $tiempo_duracion_prinflex = 0;
        $tiempo_duracion_graphicbox = 0;
        $tiempo_duracion_flexoclean = 0;
        $tiempo_duracion_artfactory = 0;

        $prom_tiempo_duracion_prinflex = 0;
        $prom_tiempo_duracion_graphicbox = 0;
        $prom_tiempo_duracion_flexoclean = 0;
        $prom_tiempo_duracion_artfactory = 0;

        $cantidad_recepcion_prinflex = 0;
        $cantidad_recepcion_graphicbox = 0;
        $cantidad_recepcion_flexoclean = 0;
        $cantidad_recepcion_artfactory = 0;

        $array_ot_procesadas = array();

        $now = Carbon::now();
        if ($now >= $toDate) {
            $fecha_final = $toDate;
        } else {
            $fecha_final = $now;
        }

        $cantidad_ot = 0;

        $indicador_result = array();

        $ot_array = array();

        $result_array[] = array(
            'Ot',
            'Diseñador CMPC',
            'Diseñador Externo',
            'Fecha Envio Diseñador Externo',
            'Fecha Entrega Diseñador Externo',
            'Tiempo respuesta Diseñador Externo'
        );

        //Obtener OT con recepcion de proveedor durante el mes consultado
        $ots_proveedor = Management::where('management_type_id', 10)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('work_order_id', 'Asc')
            ->get();

        foreach ($ots_proveedor as $ot) {
            //if(!in_array($ot->work_order_id,$array_ot_procesadas)){

            $fecha_envio_diseñador_externo      = '';
            $fecha_recepcion_diseñador_externo    = '';
            $tiempo_respuesta_diseño_externo    = 0;

            $id_diseñador_cmpc = UserWorkOrder::where('work_order_id', $ot->work_order_id)
                ->where('area_id', 3)
                ->first();
            //Diseñador CMPC
            $diseñador_cmpc = User::where('id', $id_diseñador_cmpc->user_id)->first();

            //Diseñador Externo
            $proveedor = Proveedor::where('deleted', 0)->where('id', $ot->proveedor_id)->first();

            //Fecha Recepcion de Respuesta de Diseñador Externo
            $fecha_recepcion_diseñador_externo   = date_format(date_create($ot->created_at), 'd/m/Y H:i:s');

            //Fecha Envio a Diseñador Ecxterno
            $data_envio = Management::where('id', $ot->gestion_id)
                ->first();

            $fecha_envio_diseñador_externo = date_format(date_create($data_envio->created_at), 'd/m/Y H:i:s');

            $tiempo_respuesta_diseño_externo = round((get_working_hours($data_envio->created_at, $ot->created_at) / 9.5), 2);


            if ($ot->proveedor_id == 1) { //Proveedor Prinflex
                $tiempo_duracion_prinflex += $tiempo_respuesta_diseño_externo;
                $cantidad_envio_prinflex++;
            } elseif ($ot->proveedor_id == 2) { //Proveedor Graphicbox
                $tiempo_duracion_graphicbox += $tiempo_respuesta_diseño_externo;
                $cantidad_envio_graphicbox++;
            } elseif ($ot->proveedor_id == 3) { //Proveedor Flexoclean
                $tiempo_duracion_flexoclean += $tiempo_respuesta_diseño_externo;
                $cantidad_envio_flexoclean++;
            } elseif ($ot->proveedor_id == 4) { //Proveedor Artfactory
                $tiempo_duracion_artfactory += $tiempo_respuesta_diseño_externo;
                $cantidad_envio_artfactory++;
            }

            //Array para grafico de indicador
            $indicador_result[$cantidad_ot] = $ot->work_order_id . '*' .
                $diseñador_cmpc->nombre . ' ' . $diseñador_cmpc->apellido . '*' .
                $proveedor->name . '*' .
                $fecha_envio_diseñador_externo . '*' .
                $fecha_recepcion_diseñador_externo . '*' .
                $tiempo_respuesta_diseño_externo;

            //Array para exportar a excel
            $result_array[] = array(
                'Ot'                                    => $ot->work_order_id,
                'Diseñador CMPC'                        => $diseñador_cmpc->nombre . ' ' . $diseñador_cmpc->apellido,
                'Diseñador Externo'                     => $proveedor->name,
                'Fecha Envio Diseñador Externo'         => $fecha_envio_diseñador_externo,
                'Fecha Entrega Diseñador Externo'       => $fecha_recepcion_diseñador_externo,
                'Tiempo respuesta Diseñador Externo'    => round($tiempo_respuesta_diseño_externo, 2)
            );

            $cantidad_ot++;
        }

        if (!is_null(request()->input('exportar'))) {

            $nombreMesesCompletos = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            $titulo = 'TiempoDiseñadorExterno_' . $nombreMesesCompletos[(int)$mes - 1] . '_' . $year;

            Excel::create($titulo, function ($excel) use ($result_array, $titulo) {
                $excel->setTitle($titulo);
                $excel->sheet($titulo, function ($sheet) use ($result_array) {
                    $sheet->fromArray($result_array, null, 'A1', true, false);
                });
            })->download('xlsx');
        }

        //dd($array_ot_procesadas);

        //Promedio de duracion para cada proveedor
        $prom_tiempo_duracion_prinflex      = round($tiempo_duracion_prinflex / (($cantidad_envio_prinflex > 0) ? $cantidad_envio_prinflex : 1), 2);
        $prom_tiempo_duracion_graphicbox    = round($tiempo_duracion_graphicbox / (($cantidad_envio_graphicbox > 0) ? $cantidad_envio_graphicbox : 1), 2);
        $prom_tiempo_duracion_flexoclean    = round($tiempo_duracion_flexoclean / (($cantidad_envio_flexoclean > 0) ? $cantidad_envio_flexoclean : 1), 2);
        $prom_tiempo_duracion_artfactory    = round($tiempo_duracion_artfactory / (($cantidad_envio_artfactory > 0) ? $cantidad_envio_artfactory : 1), 2);
        //dd($tiempo_duracion_prinflex,$cantidad_envio_prinflex,$prom_tiempo_duracion_prinflex);
        //Nuevo Ajuste Indicador Evolutivo 24-02

        //Total OT enviadas a Diseñador externo mes en curso
        $ots_enviadas_diseño_periodo = Management::where('management_type_id', 9)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('work_order_id', 'Asc')
            ->get();

        $array_ot_procesadas_enviadas = array();

        foreach ($ots_enviadas_diseño_periodo as $ot_enviada) {
            if (!in_array($ot_enviada->work_order_id, $array_ot_procesadas_enviadas)) {
                if ($ot_enviada->proveedor_id == 1) {
                    $cantidad_enviadas_prinflex++;
                } elseif ($ot_enviada->proveedor_id == 2) {
                    $cantidad_enviadas_graphicbox++;
                } elseif ($ot_enviada->proveedor_id == 3) {
                    $cantidad_enviadas_flexoclean++;
                } elseif ($ot_enviada->proveedor_id == 4) {
                    $cantidad_enviadas_artfactory++;
                }

                $array_ot_procesadas_enviadas[] = $ot_enviada->work_order_id;
            }
        }

        //Total OT Pendientes a Diseñador externo a la fecha
        $ots_enviadas_diseño = Management::where('management_type_id', 9)
            ->where('created_at', '>=', '2024-01-01 00:00:00')
            ->orderBy('work_order_id', 'Asc')
            ->get();

        $array_ot_procesadas_pendientes = array();

        foreach ($ots_enviadas_diseño as $ot_enviada) {

            if (!in_array($ot_enviada->work_order_id, $array_ot_procesadas_pendientes)) {
                if ($ot_enviada->proveedor_id == 1) {
                    if ($ot_enviada->recibido_diseño_externo == 0) {
                        $cantidad_pendiente_prinflex++;
                    }
                } elseif ($ot_enviada->proveedor_id == 2) {
                    if ($ot_enviada->recibido_diseño_externo == 0) {
                        $cantidad_pendiente_graphicbox++;
                    }
                } elseif ($ot_enviada->proveedor_id == 3) {
                    if ($ot_enviada->recibido_diseño_externo == 0) {
                        $cantidad_pendiente_flexoclean++;
                    }
                } elseif ($ot_enviada->proveedor_id == 4) {
                    if ($ot_enviada->recibido_diseño_externo == 0) {
                        $cantidad_pendiente_artfactory++;
                    }
                }

                $array_ot_procesadas_pendientes[] = $ot_enviada->work_order_id;
            }
        }

        //Total OT Recepcionada por Diseñador externo mes en curso
        $ots_recepcionadas_diseño_periodo = Management::where('management_type_id', 10)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('work_order_id', 'Asc')
            ->get();

        $array_ot_procesadas_recepcionadas = array();

        foreach ($ots_recepcionadas_diseño_periodo as $ot_recepcionada) {
            if (!in_array($ot_recepcionada->work_order_id, $array_ot_procesadas_recepcionadas)) {

                if ($ot_recepcionada->proveedor_id == 1) {
                    $cantidad_recepcion_prinflex++;
                } elseif ($ot_recepcionada->proveedor_id == 2) {
                    $cantidad_recepcion_graphicbox++;
                } elseif ($ot_recepcionada->proveedor_id == 3) {
                    $cantidad_recepcion_flexoclean++;
                } elseif ($ot_recepcionada->proveedor_id == 4) {
                    $cantidad_recepcion_artfactory++;
                }

                $array_ot_procesadas_recepcionadas[] = $ot_recepcionada->work_order_id;
            }
        }

        //dd($cantidad_envio_prinflex,$indicador_result,$result_array);
        return view(
            'reports.reportTiempoDisenadorExternoAjuste',
            compact(
                'mes',
                'years',
                'mesSeleccionado',
                'yearSeleccionado',
                'nombreMesesSeleccionados',
                'cantidad_envio_prinflex',
                'cantidad_envio_graphicbox',
                'cantidad_envio_flexoclean',
                'cantidad_envio_artfactory',
                'cantidad_pendiente_prinflex',
                'cantidad_pendiente_graphicbox',
                'cantidad_pendiente_flexoclean',
                'cantidad_pendiente_artfactory',
                'prom_tiempo_duracion_prinflex',
                'prom_tiempo_duracion_graphicbox',
                'prom_tiempo_duracion_flexoclean',
                'prom_tiempo_duracion_artfactory',
                'indicador_result',
                'cantidad_enviadas_prinflex',
                'cantidad_enviadas_graphicbox',
                'cantidad_enviadas_flexoclean',
                'cantidad_enviadas_artfactory',
                'cantidad_recepcion_prinflex',
                'cantidad_recepcion_graphicbox',
                'cantidad_recepcion_flexoclean',
                'cantidad_recepcion_artfactory'
            )
        );
    }
}
