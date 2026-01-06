<?php

namespace App\Http\Controllers;

use App\Armado;
use App\BitacoraCamposModificados;
use App\BitacoraWorkOrder;
use App\Cad;
use App\Canal;
use App\Carton;
use App\CiudadesFlete;
use App\Client;
use App\CodigoMaterial;
use App\Color;
use App\Cotizacion;
use App\CoverageExternal;
use App\CoverageInternal;
use App\DesignType;
use App\DetalleCotizacion;
use App\Envase;
use App\Fsc;
use App\Hierarchy;
use App\Management;
use App\Material;
use App\MaterialsCode;
use App\Muestra;
use App\MaquilaServicio;
use App\Pais;
use App\PalletType;
use App\Planta;
use App\Process;
use App\ProductType;
use App\ReferenceType;
use App\RecubrimientoType;
use App\States;
use App\Style;
use App\Subhierarchy;
use App\Subsubhierarchy;
use App\User;
use App\UserWorkOrder;
use App\WorkOrder;
use App\WorkSpace;
use App\ClassSubstancePacked;
use App\ExpectedUse;
use App\FoodType;
use App\ProductTypeDeveloping;
use App\RecycledUse;
use App\TargetMarket;
use App\TransportationWay;
use App\SalaCorte;
use App\PalletQa;
use App\PalletTagFormat;
use App\IndicacionEspecial;
use App\Matriz;
use App\SecuenciaOperacional;
use App\TipoCinta;
use App\Trazabilidad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkOrderOldController extends Controller
{
    public function filtro($query)
    {
        // Filtro por id ot
        if (!is_null(request()->input('id'))) {
            $query->where('work_orders.id', request()->input('id'));
        }
        // Filtro por material
        if (!is_null(request()->input('material'))) {
            $query = $query->whereHas('material', function ($q) {
                $q->where('materials.codigo', 'like', '%' . request()->input('material') . '%');
            });
        }
        // Filtro por cad
        if (!is_null(request()->input('cad'))) {
            $query = $query->whereHas('cad_asignado', function ($q) {
                $q->where('cads.cad', 'like', '%' . request()->input('cad') . '%');
            });
        }
        // Filtro por carton
        if (!is_null(request()->input('carton'))) {
            $query = $query->whereHas('carton', function ($q) {
                $q->where('cartons.codigo', 'like', '%' . request()->input('carton') . '%');
            });
        }
        // Filtro por descripcion
        if (!is_null(request()->input('descripcion'))) {
            $query = $query->where('descripcion', 'like', '%' . request()->input('descripcion') . '%');
        }
        if (!is_null(request()->query('vendedor_id'))) {
            $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
        } else if (Auth()->user()->isVendedor()) {
            $query = $query->where('creador_id', auth()->user()->id);
        }
        if (!is_null(request()->query('client_id'))) {
            $query = $query->whereIn('client_id', request()->query('client_id'));
        }
        if (!is_null(request()->query('canal_id'))) {
            $query = $query->whereIn('canal_id', request()->query('canal_id'));
        }
        if (!is_null(request()->query('area_id'))) {
            $query = $query->whereIn('current_area_id', request()->query('area_id'));
        }

        if (!is_null(request()->query('active'))) {
            $query = $query->whereIn('active', request()->query('active'));
        }
        // Filtros por rol-area
        if (Auth()->user()->isIngeniero() ||  Auth()->user()->isDiseñador() || Auth()->user()->isCatalogador()) {
            if (!is_null(request()->query('asignado')) && request()->query('asignado')[0] == "NO") {
                $query = $query->with('asignaciones')->whereDoesntHave("asignaciones", function ($q) {
                    $q->where("area_id", auth()->user()->role->area->id);
                });
            } else {
                if (!is_null(request()->query('responsable_id'))) {
                    $query = $query->with('asignaciones')->whereHas("asignaciones", function ($q) {
                        $q->whereIn('user_id', request()->query('responsable_id'));
                    });
                } else {
                    $query = $query->with('asignaciones')->whereHas("asignaciones", function ($q) {
                        $q->where('user_id', auth()->user()->id);
                    });
                }
            }
        }

        // Calculo total de tiempo en area de venta
        $query = $query->withCount([
            'gestiones AS tiempo_venta' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 1)->where('mostrar', 1);
            }
        ]);
        // Calculo total de tiempo en area de desarrollo
        $query = $query->withCount([
            'gestiones AS tiempo_desarrollo' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 2)->where('mostrar', 1);
            }
        ]);
        // Calculo total de tiempo en area de diseño
        $query = $query->withCount([
            'gestiones AS tiempo_diseño' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 3)->where('mostrar', 1);
            }
        ]);
        // Calculo total de tiempo en area de catalogacion
        $query = $query->withCount([
            'gestiones AS tiempo_catalogacion' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 4)->where('mostrar', 1);
            }
        ]);
        // Calculo total de tiempo en area de precatalogacion
        $query = $query->withCount([
            'gestiones AS tiempo_precatalogacion' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 5)->where('mostrar', 1);
            }
        ]);
        // Calculo total de tiempo en area de sala muestra
        $query = $query->withCount([
            'gestiones AS tiempo_muestra' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 6)->where('mostrar', 1);
            }
        ]);
        // Calculo total de tiempo
        $query = $query->withCount([
            'gestiones AS tiempo_total' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('mostrar', 1);
            }
        ]);
        // Por defecto filtra por todos los estados activos
        if (is_null(request()->input('estado_id'))) {
            $estados_activos = [1, 2, 3, 4, 5, 6, 7, 10, 12, 14, 15, 16, 17, 18];
            if (auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras()) $estados_activos = [17];
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
        } else {
            $estados = request()->query('estado_id');
            $query = $query->join('managements', 'work_orders.id', 'managements.work_order_id')
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

        return $query;
    }
    public function index()
    {
        //filters:
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
        $query = $this->filtro($query);
        // Filtro por fechas
        // Sin fechas
        if (is_null(request()->input('date_desde')) && is_null(request()->input('date_hasta'))) {
            // Si el usuario es del sala de muestra se ordena por tiempo en sala muestra
            if ((Auth()->user()->isJefeMuestras() || Auth()->user()->isTecnicoMuestras())) {
                $ots = $query->get()->map(function ($ot) {
                    $ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaSalaMuestra($ot->tiempo_muestra, 6), 1);
                    return $ot;
                })->sortByDesc("dias_trabajados_muestra")->paginate(20);
            } else {
                $ots = $query->orderBy("id", "desc")->paginate(20);
            }
            // dd($ots);
        }
        // Solo viene la fecha hasta
        else if (is_null(request()->input('date_desde')) && !is_null(request()->input('date_hasta'))) {

            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
            // Si el usuario es del sala de muestra se ordena por tiempo en sala muestra
            if ((Auth()->user()->isJefeMuestras() || Auth()->user()->isTecnicoMuestras())) {
                $ots = $query->whereDate('work_orders.created_at', '<=', $toDate)->get()->map(function ($ot) {
                    $ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaSalaMuestra($ot->tiempo_muestra, 6), 1);
                    return $ot;
                })->sortByDesc("dias_trabajados_muestra")->paginate(20);
            } else {
                // $ots = $query->orderBy("id", "desc")->paginate(20);
                $ots = $query->whereDate('work_orders.created_at', '<=', $toDate)->orderBy("id", "desc")->paginate(20);
            }
        } // Solo viene la fecha desde
        else if (!is_null(request()->input('date_desde')) && is_null(request()->input('date_hasta'))) {

            $fromDate = Carbon::createFromFormat('d/m/Y', request()->input('date_desde'))->toDateString();
            if ((Auth()->user()->isJefeMuestras() || Auth()->user()->isTecnicoMuestras())) {
                $ots = $query->get()->map(function ($ot) {
                    $ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaSalaMuestra($ot->tiempo_muestra, 6), 1);
                    return $ot;
                })->sortByDesc("dias_trabajados_muestra")->paginate(20);;
            } else {
                // $ots = $query->orderBy("id", "desc")->paginate(20);
                $ots = $query->whereDate('work_orders.created_at', '>=', $fromDate)->orderBy("id", "desc")->paginate(20);
            }
        } // vienen ambas fechas
        else {
            $fromDate = Carbon::createFromFormat('d/m/Y', request()->input('date_desde'))->toDateString();
            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
            if ((Auth()->user()->isJefeMuestras() || Auth()->user()->isTecnicoMuestras())) {
                $ots = $query->get()->map(function ($ot) {
                    $ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaSalaMuestra($ot->tiempo_muestra, 6), 1);
                    return $ot;
                })->sortByDesc("dias_trabajados_muestra")->paginate(20);
            } else {
                // $ots = $query->orderBy("id", "desc")->paginate(20);
                $ots = $query->whereBetween('work_orders.created_at', [$fromDate, $toDate])->orderBy("id", "desc")->paginate(20);
            }
        }
        // dd($ots);
        $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4])->get();
        $vendedores->map(function ($vendedor) {
            $vendedor->vendedor_id = $vendedor->id;
        });
        $responsables = [];

        if (Auth()->user()->isIngeniero() ||  Auth()->user()->isDiseñador() || Auth()->user()->isCatalogador()) {
            $responsables = User::where('active', 1)->where('role_id', auth()->user()->role_id)->get();
            $responsables->map(function ($responsable) {
                $responsable->responsable_id = $responsable->id;
            });
        }

        //Validacion para que solo listes las OT activas y asi las (Anuladas, Perdidas y terminadas) no aparezcan en el listado para que no se modifiquen
        // por el super administrador
        if (Auth()->user()->isSuperAdministrador()) {
            $estados_activos = [1, 2, 3, 4, 5, 6, 7, 10, 12, 14, 15, 16, 17, 18];
            $estados = States::whereIn('id', $estados_activos)->get();
        } else {
            $estados = States::where('status', '=', 'active')->get();
        }
        $estados->map(function ($estado) {
            $estado->estado_id = $estado->id;
        });

        $areas = WorkSpace::where('status', '=', 'active')->get();
        $areas->map(function ($area) {
            $area->area_id = $area->id;
        });

        $clients = Client::whereHas('ots')->where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->get();
        $clients->map(function ($client) {
            $client->client_id = $client->id;
        });

        $canals = Canal::all();
        $canals->map(function ($canal) {
            $canal->canal_id = $canal->id;
        });

        // Se muestra el detalle del log, solo si la OT tiene cambios guardados en el log
        $check_bitacora = [];
        foreach ($ots as $ot) {
            $bitacora_ot = BitacoraWorkOrder::where('work_order_id', $ot->id)->get()->count();
            if ($bitacora_ot) {
                $check_bitacora[] = $ot->id;
            }
        }

        // dd($ots[0]->vendedorAsignado->user);
        // $ots = WorkOrder::with("ultimoCambioEstado")->get();
        // return response()->json($ots);
        // $ots
        return view('work-orders-old.index', compact('ots', 'vendedores', 'responsables', 'clients', 'canals', 'estados', 'areas', 'check_bitacora'));
    }

    public function create()
    {

        $validacion_campos = 0;
        if (Auth()->user()->isVendedorExterno()) {
            $clients = Client::where('active', 1)->where('id', 8)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->pluck('nombre', 'id')->toArray();
        } else {
            $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->pluck('nombre', 'id')->toArray();
        }
        // COALESCE(`affiliate_name`,''),'-',COALESCE(`model`,'')
        // dd($clients);
        $cads = Cad::where('active', 1)->pluck('cad', 'id')->toArray();
        // $canals = Canal::all()->pluck('nombre', 'id')->toArray();
        $canals = Canal::where('active', 1)->pluck('nombre', 'id')->toArray();
        $cartons = Carton::where('active', 1)->pluck('codigo', 'id')->toArray();
        $cartons_muestra = Carton::where('carton_muestra', 1)->where('active', 1)->pluck('codigo', 'id')->toArray();
        $styles = Style::where('active', 1)->pluck('glosa', 'id')->toArray();
        $colors = Color::select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"), 'id')->where('active', 1)->pluck('descripcion', 'id')->toArray();
        $colors_barniz = Color::select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"), 'id')->where('active', 1)->whereIn('codigo', ['1350710', '1350711'])->pluck('descripcion', 'id')->toArray();
        $envases = Envase::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $hierarchies = Hierarchy::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $productTypes = ProductType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        //$materials = Material::where('active', 1)->where('cad_id', '!=', 0)->pluck('codigo', 'id')->toArray();
        /// Actualizacion Selector de material listando solo los materiales que tienen cartones activos4
        // Segun correo del cliente asunto "Urgente OT 19617" de Fecha 26-04-2024
        $cartons_active_id = Carton::where('active', 1)->pluck('id')->toArray();
        $materials = Material::where('active', 1)->whereIN('carton_id', $cartons_active_id)->where('cad_id', '!=', 0)->where('status', 1)->pluck('codigo', 'id')->toArray();
        ///
        $materials2 = Material::whereIn('active', [1])->whereIN('carton_id', $cartons_active_id)->where('cad_id', '!=', 0)->where('status', 1)->pluck('codigo', 'id')->toArray();


        //$procesos = Process::where('active', 1)->orderBy("descripcion")->pluck('descripcion', 'id')->toArray();
        $procesos = Process::where('active', 1)->where('type', 'EV')->orderBy('orden', 'ASC')->pluck('descripcion', 'id')->toArray();

        $armados = Armado::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $sentidos_armado = [1 => "No aplica", 2 => "Ancho a la Derecha", 3 => "Ancho a la Izquierda", 4 => "Largo a la Izquierda",  5 => "Largo a la Derecha"];
        $subhierarchies = [];
        $subsubhierarchies = [];
        // $tipos_solicitud = [1 => "Desarrollo Completo", 4 => "Cotiza sin CAD", 2 => "Cotiza con CAD", 3 => "Muestra con CAD",  5 => "Arte con Material"];// Mantener los ID por si tienen que volver agregar el cotiza con CAD y sin CAD
        $tipos_solicitud = [1 => "Desarrollo Completo", 3 => "Muestra con CAD",  5 => "Arte con Material", 6 => "Otras Solicitudes Desarrollo"];
        // Verificar que area no sea null antes de acceder a su id
        if (auth()->user()->role->area && auth()->user()->role->area->id == 2) {
            $tipos_solicitud = [1 => "Desarrollo Completo", 3 => "Muestra con CAD", 6 => "Otras Solicitudes Desarrollo"];
        }
        $org_ventas = [1 => "Nacional", 2 => "Exportación"];
        $vendedores = User::whereIn('role_id', [4])->where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " ", COALESCE(apellido,"")) AS nombre'))->pluck('nombre', 'id')->toArray();
        // dd($vendedores);
        $comunas = CiudadesFlete::pluck('ciudad', 'id')->toArray();
        $paisReferencia = Pais::where('active', 1)->pluck('name', 'id')->toArray();
        $plantaObjetivo = Planta::pluck('nombre', 'id')->toArray();
        $palletTypes = PalletType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $recubrimiento_type = RecubrimientoType::where('active', 1)->pluck('descripcion', 'codigo')->toArray();
        $reference_type = ReferenceType::where('active', 1)->pluck('descripcion', 'codigo')->toArray();
        $fsc = Fsc::where('active', 1)->pluck('descripcion', 'codigo')->toArray();
        $designTypes = DesignType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $maquila_servicios = MaquilaServicio::where('active', 1)->pluck('servicio', 'id')->toArray();
        $secuenciaOperacional = SecuenciaOperacional::where('active', 1)->pluck('descripcion', 'id')->toArray();

        $trazabilidad = Trazabilidad::where('status', 1)->pluck('descripcion', 'id')->toArray();

        $coverageExternal = CoverageExternal::where('status', 1)->pluck('descripcion', 'id')->toArray();
        $coverageInternal = CoverageInternal::where('status', 1)->pluck('descripcion', 'id')->toArray();
        $tipoCinta = TipoCinta::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $matrices = Matriz::where('active', 1)->pluck('material', 'id')->toArray();;

        //Nuevos Campos Seccion Datas para el Desarrollo
        $classSubstancePacked = ClassSubstancePacked::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $expectedUse = ExpectedUse::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $foodType = FoodType::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $productTypeDeveloping = ProductTypeDeveloping::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $recycledUse = RecycledUse::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $targetMarket = TargetMarket::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $transportationWay = TransportationWay::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $salas_cortes = SalaCorte::where('deleted', 0)->pluck('nombre', 'id')->toArray();
        $palletQa = PalletQa::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $palletTagFormat = PalletTagFormat::where('active', 1)->pluck('descripcion', 'id')->toArray();

        return view('work-orders-old.create', compact(
            'comunas',
            'clients',
            'cads',
            'canals',
            'cartons_muestra',
            'cartons',
            'styles',
            'colors',
            'envases',
            'armados',
            'sentidos_armado',
            'procesos',
            'materials',
            'materials2',
            'productTypes',
            'hierarchies',
            'subhierarchies',
            'subsubhierarchies',
            'tipos_solicitud',
            'org_ventas',
            'vendedores',
            'paisReferencia',
            'plantaObjetivo',
            'palletTypes',
            'reference_type',
            'fsc',
            'recubrimiento_type',
            'designTypes',
            'maquila_servicios',
            'validacion_campos',
            'coverageExternal',
            'coverageInternal',
            'classSubstancePacked',
            'expectedUse',
            'foodType',
            'productTypeDeveloping',
            'recycledUse',
            'targetMarket',
            'transportationWay',
            'colors_barniz',
            'salas_cortes',
            'palletQa',
            'palletTagFormat',
            'secuenciaOperacional',
            'tipoCinta',
            'matrices',
            'trazabilidad',
        ));
    }

    public function duplicate(Request $request, $id)
    {
        $request->validate([
            // Datos Comerciales
            'tipo_solicitud' =>  ['required', Rule::in(['1', '2', '3', '4', '5'])],
        ]);
        $validacion_campos = 0;

        // dd(request('tipo_solicitud'), $id);
        $ot = WorkOrder::with('subsubhierarchy.subhierarchy.hierarchy')->find($id);
        $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->pluck('nombre', 'id')->toArray();
        // COALESCE(`affiliate_name`,''),'-',COALESCE(`model`,'')
        // dd($clients);
        $cads = Cad::where('active', 1)->pluck('cad', 'id')->toArray();
        // $canals = Canal::all()->pluck('nombre', 'id')->toArray();
        $canals = Canal::where('active', 1)->pluck('nombre', 'id')->toArray();
        $cartons = Carton::where('active', 1)->pluck('codigo', 'id')->toArray();
        $styles = Style::where('active', 1)->pluck('glosa', 'id')->toArray();
        $colors = Color::select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"), 'id')->where('active', 1)->pluck('descripcion', 'id')->toArray();
        $colors_barniz = Color::select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"), 'id')->where('active', 1)->whereIn('codigo', ['1350710', '1350711'])->pluck('descripcion', 'id')->toArray();
        $envases = Envase::where('active', 1)->pluck('descripcion', 'id')->toArray();
        //$hierarchies = Hierarchy::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $hierarchies = Hierarchy::where('active', 1)->orWhere('id', $ot->subsubhierarchy->subhierarchy->hierarchy->id)->pluck('descripcion', 'id')->toArray();
        $productTypes = ProductType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        //$materials = Material::where('active', 1)->where('cad_id', '!=', 0)->pluck('codigo', 'id')->toArray();
        /// Actualizacion Selector de material listando solo los materiales que tienen cartones activos4
        // Segun correo del cliente asunto "Urgente OT 19617" de Fecha 26-04-2024
        $cartons_active_id = Carton::where('active', 1)->pluck('id')->toArray();
        // $materials = Material::where('active', 1)->whereIN('carton_id', $cartons_active_id)->where('cad_id', '!=', 0)->where('status', 1)->pluck('codigo', 'id')->toArray();
        // $materials2 = Material::whereIn('active', [1])->whereIN('carton_id', $cartons_active_id)->where('cad_id', '!=', 0)->where('status', 1)->pluck('codigo', 'id')->toArray();
        ///

        $materials2 = Material::where('active', 1)->where('cad_id', '!=', 0)->where('status', 1)->pluck('codigo', 'id')->toArray();
        // $materials2 = Material::whereIn('active', [1])->where('cad_id', '!=', 0)->where('status',1)->pluck('codigo', 'id')->toArray();

        // Agregar el reference_id aunque no cumpla condiciones
        $materialsReferencia = [];

        if (!is_null($ot->reference_id) && !array_key_exists($ot->reference_id, $materials2)) {
            $materialsReferencia = Material::where('id', $ot->reference_id)
                ->pluck('codigo', 'id')
                ->toArray();
        }

        // Combinar ambos
        $materials = $materialsReferencia + $materials2;

        $trazabilidad = Trazabilidad::where('status', 1)->pluck('descripcion', 'id')->toArray();

        //$procesos = Process::where('active', 1)->orderBy("descripcion")->pluck('descripcion', 'id')->toArray();
        $procesos = Process::where('active', 1)->where('type', 'EV')->orderBy('orden', 'ASC')->pluck('descripcion', 'id')->toArray();

        $armados = Armado::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $sentidos_armado = [1 => "No aplica", 2 => "Ancho a la Derecha", 3 => "Ancho a la Izquierda", 4 => "Largo a la Izquierda",  5 => "Largo a la Derecha"];
        $subhierarchies = [];
        $subsubhierarchies = [];
        $tipos_solicitud = [1 => "Desarrollo Completo", 4 => "Cotiza sin CAD", 2 => "Cotiza con CAD", 3 => "Muestra con CAD",  5 => "Arte con Material", 6 => "Otras Solicitudes Desarrollo"];
        // Verificar que area no sea null antes de acceder a su id
        if (auth()->user()->role->area && auth()->user()->role->area->id == 2) {
            $tipos_solicitud = [1 => "Desarrollo Completo", 4 => "Cotiza sin CAD", 2 => "Cotiza con CAD", 3 => "Muestra con CAD", 6 => "Otras Solicitudes Desarrollo"];
        }
        $org_ventas = [1 => "Nacional", 2 => "Exportación"];
        $vendedores = User::whereIn('role_id', [4])->where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " ", COALESCE(apellido,"")) AS nombre'))->pluck('nombre', 'id')->toArray();
        // dd($vendedores);
        $tipo_solicitud_ot = request("tipo_solicitud");
        $cartons_muestra = Carton::where('carton_muestra', 1)->where('active', 1)->pluck('codigo', 'id')->toArray();
        $comunas = CiudadesFlete::pluck('ciudad', 'id')->toArray();
        $paisReferencia = Pais::where('active', 1)->pluck('name', 'id')->toArray();
        $plantaObjetivo = Planta::pluck('nombre', 'id')->toArray();
        $palletTypes = PalletType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $reference_type = ReferenceType::where('active', 1)->pluck('descripcion', 'codigo')->toArray();
        $fsc = Fsc::where('active', 1)->pluck('descripcion', 'codigo')->toArray();
        $recubrimiento_type = RecubrimientoType::where('active', 1)->pluck('descripcion', 'codigo')->toArray();
        $designTypes = DesignType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $maquila_servicios = MaquilaServicio::where('active', 1)->pluck('servicio', 'id')->toArray();

        $coverageExternal = CoverageExternal::where('status', 1)->pluck('descripcion', 'id')->toArray();
        $coverageInternal = CoverageInternal::where('status', 1)->pluck('descripcion', 'id')->toArray();

        //Nuevos Campos Seccion Datas para el Desarrollo
        $classSubstancePacked = ClassSubstancePacked::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $expectedUse = ExpectedUse::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $foodType = FoodType::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $productTypeDeveloping = ProductTypeDeveloping::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $recycledUse = RecycledUse::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $targetMarket = TargetMarket::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $transportationWay = TransportationWay::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $palletQa = PalletQa::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $palletTagFormat = PalletTagFormat::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $indicaciones_especiales = IndicacionEspecial::where('client_id', $ot->client_id)->where('deleted', 0)->get();
        $tipoCinta = TipoCinta::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $secuenciaOperacional = SecuenciaOperacional::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $matriz = Matriz::where('active', 1)->pluck('material', 'id')->toArray();
        //dd($indicaciones_especiales);
        return view('work-orders-old.create-old', compact(
            'comunas',
            'ot',
            'tipo_solicitud_ot',
            'clients',
            'cads',
            'canals',
            'cartons',
            'styles',
            'colors',
            'envases',
            'armados',
            'procesos',
            'materials',
            'materials2',
            'productTypes',
            'hierarchies',
            'subhierarchies',
            'subsubhierarchies',
            'tipos_solicitud',
            'org_ventas',
            'sentidos_armado',
            'vendedores',
            'cartons_muestra',
            'paisReferencia',
            'plantaObjetivo',
            'palletTypes',
            'reference_type',
            'fsc',
            'recubrimiento_type',
            'designTypes',
            'maquila_servicios',
            'validacion_campos',
            'coverageExternal',
            'coverageInternal',
            'classSubstancePacked',
            'expectedUse',
            'foodType',
            'productTypeDeveloping',
            'recycledUse',
            'targetMarket',
            'transportationWay',
            'colors_barniz',
            'palletQa',
            'palletTagFormat',
            'indicaciones_especiales',
            'tipoCinta',
            'secuenciaOperacional',
            'matriz',
            'trazabilidad',
        ));
    }

    public function detalleAOt(Request $request)
    {

        // dd(request()->all());
        $request->validate([
            // Datos Comerciales
            'tipo_solicitud' =>  ['required', Rule::in(['1', '2', '3', '4', '5'])],
        ]);
        $validacion_campos = 0;
        // dd(request('tipo_solicitud'), $id);
        $detalleCotizacion = DetalleCotizacion::withAll()->find(request("detalle_id"));
        $cotizacion = Cotizacion::find($detalleCotizacion->cotizacion_id);
        // dd($detalleCotizacion);
        $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->pluck('nombre', 'id')->toArray();
        // COALESCE(`affiliate_name`,''),'-',COALESCE(`model`,'')
        // dd($clients);
        $cads = Cad::where('active', 1)->pluck('cad', 'id')->toArray();
        $canals = Canal::all()->pluck('nombre', 'id')->toArray();
        $cartons = Carton::where('active', 1)->pluck('codigo', 'id')->toArray();
        $styles = Style::where('active', 1)->pluck('glosa', 'id')->toArray();
        $colors = Color::select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"), 'id')->where('active', 1)->pluck('descripcion', 'id')->toArray();
        $envases = Envase::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $hierarchies = Hierarchy::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $productTypes = ProductType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $trazabilidad = Trazabilidad::where('status', 1)->pluck('descripcion', 'id')->toArray();

        $materials = Material::where('active', 1)->where('cad_id', '!=', 0)->where('status', 1)->pluck('codigo', 'id')->toArray();
        $materials2 = Material::whereIn('active', [1])->where('cad_id', '!=', 0)->where('status', 1)->pluck('codigo', 'id')->toArray();

        // $procesos = Process::where('active', 1)->orderBy("descripcion")->pluck('descripcion', 'id')->toArray();
        $procesos = Process::where('active', 1)->where('type', 'EV')->orderBy('orden', 'ASC')->pluck('descripcion', 'id')->toArray();

        $armados = Armado::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $sentidos_armado = [1 => "No aplica", 2 => "Ancho a la Derecha", 3 => "Ancho a la Izquierda", 4 => "Largo a la Izquierda",  5 => "Largo a la Derecha"];
        $subhierarchies = [];
        $subsubhierarchies = [];
        $tipos_solicitud = [1 => "Desarrollo Completo", 4 => "Cotiza sin CAD", 2 => "Cotiza con CAD", 3 => "Muestra con CAD",  5 => "Arte con Material", 6 => "Otras Solicitudes Desarrollo"];
        // Verificar que area no sea null antes de acceder a su id
        if (auth()->user()->role->area && auth()->user()->role->area->id == 2) {
            $tipos_solicitud = [1 => "Desarrollo Completo", 4 => "Cotiza sin CAD", 2 => "Cotiza con CAD", 3 => "Muestra con CAD", 6 => "Otras Solicitudes Desarrollo"];
        }
        $org_ventas = [1 => "Nacional", 2 => "Exportación"];
        $vendedores = User::whereIn('role_id', [4])->where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " ", COALESCE(apellido,"")) AS nombre'))->pluck('nombre', 'id')->toArray();
        // dd($vendedores);
        $tipo_solicitud_ot = request("tipo_solicitud");

        $comunas = CiudadesFlete::pluck('ciudad', 'id')->toArray();
        $paisReferencia = Pais::where('active', 1)->pluck('name', 'id')->toArray();
        $plantaObjetivo = Planta::pluck('nombre', 'id')->toArray();
        $palletTypes = PalletType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $cartons_muestra = Carton::where('carton_muestra', 1)->where('active', 1)->pluck('codigo', 'id')->toArray();
        $reference_type = [0 => "No", 1 => "Si"];
        $reference_type = array_merge($reference_type, ReferenceType::where('active', 1)->pluck('descripcion', 'codigo')->toArray());
        $fsc = [0 => "No", 1 => "Si"];
        $fsc = array_merge($fsc, Fsc::where('active', 1)->pluck('descripcion', 'codigo')->toArray());
        $designTypes = DesignType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $recubrimiento_type = RecubrimientoType::where('active', 1)->pluck('descripcion', 'codigo')->toArray();
        $maquila_servicios = MaquilaServicio::where('active', 1)->pluck('servicio', 'id')->toArray();

        $coverageExternal = CoverageExternal::where('status', 1)->pluck('descripcion', 'id')->toArray();
        $coverageInternal = CoverageInternal::where('status', 1)->pluck('descripcion', 'id')->toArray();

        return view('work-orders-old.create', compact(
            'comunas',
            'cartons_muestra',
            'detalleCotizacion',
            'cotizacion',
            'tipo_solicitud_ot',
            'clients',
            'cads',
            'canals',
            'cartons',
            'styles',
            'colors',
            'envases',
            'armados',
            'procesos',
            'materials',
            'materials2',
            'productTypes',
            'hierarchies',
            'subhierarchies',
            'subsubhierarchies',
            'tipos_solicitud',
            'org_ventas',
            'sentidos_armado',
            'vendedores',
            'fsc',
            'paisReferencia',
            'plantaObjetivo',
            'palletTypes',
            'reference_type',
            'recubrimiento_type',
            'designTypes',
            'maquila_servicios',
            'validacion_campos',
            'coverageExternal',
            'coverageInternal',
            'trazabilidad',
        ));
    }

    public function store(Request $request)
    {
        // dd(request()->all());
        if (auth()->user()->isVendedor() || auth()->user()->isJefeVenta()) {
            $request->validate([
                // Datos Comerciales
                'client_id' => 'required',
                'descripcion' => 'required|max:40',
                'tipo_solicitud' => 'required',
                'canal_id' => 'required',
                //'hierarchy_id' => 'required',
                'subhierarchy_id' => 'required',
                'subsubhierarchy_id' => 'required',
                'checkboxes' => 'required',
                'fsc' => 'required',
                // 'numero_colores' => 'required_if:tipo_solicitud,3',
                'cinta' => 'required_if:tipo_solicitud,1,5',
                'impresion' => 'required_if:tipo_solicitud,3',
                'product_type_id' => 'required_if:tipo_solicitud,1,4',
                // 'armado_id' => 'required_if:tipo_solicitud,1,4',
                'peso_contenido_caja' => 'required_if:tipo_solicitud,1,4',
                'autosoportante' => 'required_if:tipo_solicitud,1,4',
                'envase_id' => 'required_if:tipo_solicitud,1,4',
                'cajas_altura' => 'required_if:tipo_solicitud,1,4',
                'pallet_sobre_pallet' => 'required_if:tipo_solicitud,1,4',

            ]);
        }
        $datos_insertados = array();
        $ot = new WorkOrder();

        // DATOS COMERCIALES
        $ot->client_id = (trim($request->input('client_id')) != '') ? $request->input('client_id') : $ot->client_id;
        if (trim($request->input('client_id')) != '') {
            $datos_insertados['client_id'] = [
                'texto' => 'Cliente',
                'valor' => ['descripcion' => $request->input('client_id')]
            ];
        }

        $ot->instalacion_cliente  = (trim($request->input('instalacion_cliente')) != '') ? $request->input('instalacion_cliente') : $ot->instalacion_cliente;
        if (trim($request->input('instalacion_cliente')) != '') {
            $datos_insertados['instalacion_cliente'] = [
                'texto' => 'Instalacion Cliente',
                'valor' => ['descripcion' => $request->input('instalacion_cliente')]
            ];
        }

        $ot->descripcion = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $ot->descripcion;
        if (trim($request->input('descripcion')) != '') {
            $datos_insertados['descripcion'] = [
                'texto' => 'Descripción',
                'valor' => ['descripcion' => $request->input('descripcion')]
            ];
        }

        $ot->dato_sub_cliente = (trim($request->input('dato_sub_cliente')) != '') ? $request->input('dato_sub_cliente') : $ot->dato_sub_cliente;
        if (trim($request->input('dato_sub_cliente')) != '') {
            $datos_insertados['dato_sub_cliente'] = [
                'texto' => 'Datos Cliente Edipac ',
                'valor' => ['dato_sub_cliente' => $request->input('dato_sub_cliente')]
            ];
        }

        $ot->codigo_producto = (trim($request->input('codigo_producto')) != '') ? $request->input('codigo_producto') : $ot->codigo_producto;
        if (trim($request->input('codigo_producto')) != '') {
            $datos_insertados['codigo_producto'] = [
                'texto' => 'Codigo Producto',
                'valor' => ['descripcion' => $request->input('codigo_producto')]
            ];
        }
        //Solicitud correccion Evolutivo 24-09
        $ot->codigo_producto_cliente = (trim($request->input('codigo_producto')) != '') ? $request->input('codigo_producto') : $ot->codigo_producto_cliente;

        $ot->tipo_solicitud = (trim($request->input('tipo_solicitud')) != '') ? $request->input('tipo_solicitud') : $ot->tipo_solicitud;
        if (trim($request->input('tipo_solicitud')) != '') {
            $datos_insertados['tipo_solicitud'] = [
                'texto' => 'Tipo Solicitud',
                'valor' => ['descripcion' => $request->input('tipo_solicitud')]
            ];
        }

        $ot->nombre_contacto = (trim($request->input('nombre_contacto')) != '') ? $request->input('nombre_contacto') : $ot->nombre_contacto;
        if (trim($request->input('nombre_contacto')) != '') {
            $datos_insertados['nombre_contacto'] = [
                'texto' => 'Nombre Contacto',
                'valor' => ['descripcion' => $request->input('nombre_contacto')]
            ];
        }

        $ot->email_contacto = (trim($request->input('email_contacto')) != '') ? $request->input('email_contacto') : $ot->email_contacto;
        if (trim($request->input('email_contacto')) != '') {
            $datos_insertados['email_contacto'] = [
                'texto' => 'Email Contacto',
                'valor' => ['descripcion' => $request->input('email_contacto')]
            ];
        }

        $ot->telefono_contacto = (trim($request->input('telefono_contacto')) != '') ? str_replace(' ', '', $request->input('telefono_contacto')) : $ot->telefono_contacto;
        if (trim($request->input('telefono_contacto')) != '') {
            $datos_insertados['telefono_contacto'] = [
                'texto' => 'Telefono Contacto',
                'valor' => ['descripcion' => $request->input('telefono_contacto')]
            ];
        }

        $ot->volumen_venta_anual = (trim($request->input('volumen_venta_anual')) != '') ? str_replace('.', '', $request->input('volumen_venta_anual')) : $ot->volumen_venta_anual;
        if (trim($request->input('volumen_venta_anual')) != '') {
            $datos_insertados['volumen_venta_anual'] = [
                'texto' => 'Volumen Venta Anual',
                'valor' => ['descripcion' => $request->input('volumen_venta_anual')]
            ];
        }

        $ot->usd = (trim($request->input('usd')) != '') ? str_replace('.', '', $request->input('usd')) : $ot->usd;
        if (trim($request->input('usd')) != '') {
            $datos_insertados['usd'] = [
                'texto' => 'Usd',
                'valor' => ['descripcion' => $request->input('usd')]
            ];
        }

        $ot->org_venta_id = (trim($request->input('org_venta_id')) != '') ? $request->input('org_venta_id') : $ot->org_venta_id;
        if (trim($request->input('org_venta_id')) != '') {
            $datos_insertados['org_venta_id'] = [
                'texto' => 'Org. Venta',
                'valor' => ['descripcion' => $request->input('org_venta_id')]
            ];
        }

        $ot->oc = (trim($request->input('oc')) != '') ? $request->input('oc') : $ot->oc;
        if (trim($request->input('oc')) != '') {
            $datos_insertados['oc'] = [
                'texto' => 'OC.',
                'valor' => ['descripcion' => $request->input('oc')]
            ];
        }

        $ot->canal_id = (trim($request->input('canal_id')) != '') ? $request->input('canal_id') : $ot->canal_id;
        if (trim($request->input('canal_id')) != '') {
            $datos_insertados['canal_id'] = [
                'texto' => 'Canal.',
                'valor' => ['descripcion' => $request->input('canal_id')]
            ];
        }

        $ot->subsubhierarchy_id  = (trim($request->input('subsubhierarchy_id')) != '') ? $request->input('subsubhierarchy_id') : $ot->subsubhierarchy_id;
        if (trim($request->input('subsubhierarchy_id')) != '') {
            $datos_insertados['subsubhierarchy_id'] = [
                'texto' => 'Subsubhierarchy',
                'valor' => ['descripcion' => $request->input('subsubhierarchy_id')]
            ];
        }

        // Solicita
        if (in_array('analisis', $request->input('checkboxes'))) {
            $ot->analisis = 1;
            $datos_insertados['analisis'] = [
                'texto' => 'Analisis',
                'valor' => ['descripcion' => 'Si']
            ];
        }

        if (in_array('plano', $request->input('checkboxes'))) {
            $ot->plano = 1;
            $datos_insertados['plano'] = [
                'texto' => 'Plano',
                'valor' => ['descripcion' => 'Si']
            ];
        }

        if (in_array('datos_cotizar', $request->input('checkboxes'))) {
            $ot->datos_cotizar = 1;
            $datos_insertados['datos_cotizar'] = [
                'texto' => 'Datos Cotizar',
                'valor' => ['descripcion' => 'Si']
            ];
        }

        if (in_array('boceto', $request->input('checkboxes'))) {
            $ot->boceto = 1;
            $datos_insertados['boceto'] = [
                'texto' => 'Boceto',
                'valor' => ['descripcion' => 'Si']
            ];
        }

        if (in_array('nuevo_material', $request->input('checkboxes'))) {
            $ot->nuevo_material = 1;
            $datos_insertados['nuevo_material'] = [
                'texto' => 'Nuevo Material',
                'valor' => ['descripcion' => 'Si']
            ];
        }

        if (in_array('prueba_industrial', $request->input('checkboxes'))) {
            $ot->prueba_industrial = 1;
            $datos_insertados['prueba_industrial'] = [
                'texto' => 'Prueba Insdustrial',
                'valor' => ['descripcion' => 'Si']
            ];
        }

        if (in_array('muestra', $request->input('checkboxes'))) {
            $ot->muestra = 1;
            $ot->numero_muestras = (trim($request->input('numero_muestras')) != '') ? $request->input('numero_muestras') : $ot->numero_muestras;
            $datos_insertados['muestra'] = [
                'texto' => 'Muestra',
                'valor' => ['descripcion' => 'Si']
            ];
            if (trim($request->input('numero_muestras')) != '') {
                $datos_insertados['numero_muestras'] = [
                    'texto' => 'Numero Muestras',
                    'valor' => ['descripcion' => $ot->numero_muestras]
                ];
            }
        }
        // Referencia
        $ot->reference_type = (trim($request->input('reference_type')) != '') ? $request->input('reference_type') : $ot->reference_type;
        if (trim($request->input('reference_type')) != '') {
            $datos_insertados['reference_type'] = [
                'texto' => 'Tipo de Referencia',
                'valor' => ['descripcion' => $request->input('reference_type')]
            ];
        }

        $ot->reference_id           = (trim($request->input('reference_id')) != '') ? $request->input('reference_id') : $ot->reference_id;
        if (trim($request->input('reference_id')) != '') {
            $datos_insertados['reference_id'] = [
                'texto' => 'Id Referencia',
                'valor' => ['descripcion' => $request->input('reference_id')]
            ];
        }

        $ot->bloqueo_referencia = (trim($request->input('bloqueo_referencia')) != '') ? $request->input('bloqueo_referencia') : $ot->bloqueo_referencia;
        if (trim($request->input('bloqueo_referencia')) != '') {
            $datos_insertados['bloqueo_referencia'] = [
                'texto' => 'Bloqueo Referencia',
                'valor' => ['descripcion' => $request->input('bloqueo_referencia')]
            ];
        }

        $ot->indicador_facturacion = (trim($request->input('indicador_facturacion')) != '') ? $request->input('indicador_facturacion') : $ot->indicador_facturacion;
        if (trim($request->input('indicador_facturacion')) != '') {
            $datos_insertados['indicador_facturacion'] = [
                'texto' => 'Indicador Facturacion',
                'valor' => ['descripcion' => $request->input('indicador_facturacion')]
            ];
        }
        // Caracteristicas
        // El cad puede ser seleccionado de un listado o ingresado libremente por eso permite guardar ambas opciones
        $ot->cad_id = (trim($request->input('cad_id')) != '') ? $request->input('cad_id') : $ot->cad_id;
        if (trim($request->input('cad_id')) != '') {
            $datos_insertados['cad_id'] = [
                'texto' => 'Id Cad',
                'valor' => ['descripcion' => $request->input('cad_id')]
            ];
        }

        $ot->cad = (trim($request->input('cad')) != '') ? $request->input('cad') : $ot->cad;
        if (trim($request->input('cad')) != '') {
            $datos_insertados['cad'] = [
                'texto' => 'Cad',
                'valor' => ['descripcion' => $request->input('cad')]
            ];
        }

        $ot->product_type_id = (trim($request->input('product_type_id')) != '') ? $request->input('product_type_id') : $ot->product_type_id;
        if (trim($request->input('product_type_id')) != '') {
            $datos_insertados['product_type_id'] = [
                'texto' => 'Id Tipo de Producto',
                'valor' => ['descripcion' => $request->input('product_type_id')]
            ];
        }

        $ot->items_set = (trim($request->input('items_set')) != '') ? $request->input('items_set') : $ot->items_set;
        if (trim($request->input('items_set')) != '') {
            $datos_insertados['items_set'] = [
                'texto' => 'Items Set',
                'valor' => ['descripcion' => $request->input('items_set')]
            ];
        }

        $ot->veces_item = (trim($request->input('veces_item')) != '') ? $request->input('veces_item') : $ot->veces_item;
        if (trim($request->input('veces_item')) != '') {
            $datos_insertados['veces_item'] = [
                'texto' => 'Veces Item',
                'valor' => ['descripcion' => $request->input('veces_item')]
            ];
        }

        $ot->carton_id = (trim($request->input('carton_id')) != '') ? $request->input('carton_id') : $ot->carton_id;
        if (trim($request->input('carton_id')) != '') {
            $datos_insertados['carton_id'] = [
                'texto' => 'Id Carton',
                'valor' => ['descripcion' => $request->input('carton_id')]
            ];
        }

        $ot->carton_color = (trim($request->input('carton_color')) != '') ? $request->input('carton_color') : $ot->carton_color;
        if (trim($request->input('carton_color')) != '') {
            $datos_insertados['carton_color'] = [
                'texto' => 'Color Carton',
                'valor' => ['descripcion' => $request->input('carton_color')]
            ];
        }

        $ot->style_id = (trim($request->input('style_id')) != '') ? $request->input('style_id') : $ot->style_id;
        if (trim($request->input('style_id')) != '') {
            $datos_insertados['style_id'] = [
                'texto' => 'Id Style',
                'valor' => ['descripcion' => $request->input('style_id')]
            ];
        }

        $ot->matriz_id = (trim($request->input('matriz_id')) != '') ? $request->input('matriz_id') : $ot->matriz_id;
        if (trim($request->input('matriz_id')) != '') {
            $datos_insertados['matriz_id'] = [
                'texto' => 'Id Matriz',
                'valor' => ['descripcion' => $request->input('matriz_id')]
            ];
        }
        $ot->recubrimiento = (trim($request->input('recubrimiento')) != '') ? $request->input('recubrimiento') : $ot->recubrimiento;
        if (trim($request->input('recubrimiento')) != '') {
            $datos_insertados['recubrimiento'] = [
                'texto' => 'Recubrimiento',
                'valor' => ['descripcion' => $request->input('recubrimiento')]
            ];
        }

        $ot->separacion_golpes_ancho = (trim($request->input('separacion_golpes_ancho')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('separacion_golpes_ancho'))) : null;
        if (trim($request->input('separacion_golpes_ancho')) != '') {
            $datos_insertados['separacion_golpes_ancho'] = [
                'texto' => 'Separacion Golpes Ancho',
                'valor' => ['descripcion' => $request->input('separacion_golpes_ancho')]
            ];
        }

        $ot->separacion_golpes_largo = (trim($request->input('separacion_golpes_largo')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('separacion_golpes_largo'))) : null;
        if (trim($request->input('separacion_golpes_largo')) != '') {
            $datos_insertados['separacion_golpes_largo'] = [
                'texto' => 'Separacion Golpes Largo',
                'valor' => ['descripcion' => $request->input('separacion_golpes_largo')]
            ];
        }

        $ot->cuchillas = (trim($request->input('cuchillas')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('cuchillas'))) : null;
        if (trim($request->input('cuchillas')) != '') {
            $datos_insertados['cuchillas'] = [
                'texto' => 'Cuchillas',
                'valor' => ['descripcion' => $request->input('cuchillas')]
            ];
        }

        $ot->golpes_largo = (trim($request->input('golpes_largo')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('golpes_largo'))) : null;
        if (trim($request->input('golpes_largo')) != '') {
            $datos_insertados['golpes_largo'] = [
                'texto' => 'Golpes Largo',
                'valor' => ['descripcion' => $request->input('golpes_largo')]
            ];
        }
        $ot->golpes_ancho = (trim($request->input('golpes_ancho')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('golpes_ancho'))) : null;
        if (trim($request->input('golpes_ancho')) != '') {
            $datos_insertados['golpes_ancho'] = [
                'texto' => 'Golpes Ancho',
                'valor' => ['descripcion' => $request->input('golpes_ancho')]
            ];
        }

        // Cargadas por CAD
        $ot->largura_hm = (trim($request->input('largura_hm')) != '') ? $request->input('largura_hm') : $ot->largura_hm;
        if (trim($request->input('largura_hm')) != '') {
            $datos_insertados['largura_hm'] = [
                'texto' => 'Largura Hm',
                'valor' => ['descripcion' => $request->input('largura_hm')]
            ];
        }

        $ot->anchura_hm = (trim($request->input('anchura_hm')) != '') ? $request->input('anchura_hm') : $ot->anchura_hm;
        if (trim($request->input('anchura_hm')) != '') {
            $datos_insertados['anchura_hm'] = [
                'texto' => 'Anchura Hm',
                'valor' => ['descripcion' => $request->input('anchura_hm')]
            ];
        }

        $ot->area_producto = (trim($request->input('area_producto')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('area_producto'))) : null;
        if (trim($request->input('area_producto')) != '') {
            $datos_insertados['area_producto'] = [
                'texto' => 'Area Producto',
                'valor' => ['descripcion' => $request->input('area_producto')]
            ];
        }

        $ot->recorte_adicional = (trim($request->input('recorte_adicional')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('recorte_adicional'))) : null;
        if (trim($request->input('recorte_adicional')) != '') {
            $datos_insertados['recorte_adicional'] = [
                'texto' => 'Recorte Adicional',
                'valor' => ['descripcion' => $request->input('recorte_adicional')]
            ];
        }

        $ot->longitud_pegado = (trim($request->input('longitud_pegado')) != '') ? $request->input('longitud_pegado') : null;
        if (trim($request->input('longitud_pegado')) != '') {
            $datos_insertados['longitud_pegado'] = [
                'texto' => 'Longitud Pegado',
                'valor' => ['descripcion' => $request->input('longitud_pegado')]
            ];
        }

        $ot->rayado_c1r1 = (trim($request->input('rayado_c1r1')) != '') ? $request->input('rayado_c1r1') : $ot->rayado_c1r1;
        if (trim($request->input('rayado_c1r1')) != '') {
            $datos_insertados['rayado_c1r1'] = [
                'texto' => 'Rayado_c1r1',
                'valor' => ['descripcion' => $request->input('rayado_c1r1')]
            ];
        }

        $ot->rayado_r1_r2 = (trim($request->input('rayado_r1_r2')) != '') ? $request->input('rayado_r1_r2') : $ot->rayado_r1_r2;
        if (trim($request->input('rayado_r1_r2')) != '') {
            $datos_insertados['rayado_r1_r2'] = [
                'texto' => 'Rayado_r1_r2',
                'valor' => ['descripcion' => $request->input('rayado_r1_r2')]
            ];
        }

        $ot->rayado_r2_c2 = (trim($request->input('rayado_r2_c2')) != '') ? $request->input('rayado_r2_c2') : $ot->rayado_r2_c2;
        if (trim($request->input('rayado_r2_c2')) != '') {
            $datos_insertados['rayado_r2_c2'] = [
                'texto' => 'Rayado_r2_c2',
                'valor' => ['descripcion' => $request->input('rayado_r2_c2')]
            ];
        }

        $ot->bct_humedo_lb = (trim($request->input('bct_humedo_lb')) != '') ? $request->input('bct_humedo_lb') : null;
        if (trim($request->input('bct_humedo_lb')) != '') {
            $datos_insertados['bct_humedo_lb'] = [
                'texto' => 'Bct Humedo Lb',
                'valor' => ['descripcion' => $request->input('bct_humedo_lb')]
            ];
        }

        $ot->mullen = (trim($request->input('mullen')) != '') ? $request->input('mullen') : null;
        if (trim($request->input('mullen')) != '') {
            $datos_insertados['mullen'] = [
                'texto' => 'Mullen',
                'valor' => ['descripcion' => $request->input('mullen')]
            ];
        }

        $ot->gramaje = (trim($request->input('gramaje')) != '') ? $request->input('gramaje') : null;
        if (trim($request->input('gramaje')) != '') {
            $datos_insertados['gramaje'] = [
                'texto' => 'Gramaje',
                'valor' => ['descripcion' => $request->input('gramaje')]
            ];
        }

        $ot->ect = (trim($request->input('ect')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('ect'))) : null;
        if (trim($request->input('ect')) != '') {
            $datos_insertados['ect'] = [
                'texto' => 'Ect',
                'valor' => ['descripcion' => $request->input('ect')]
            ];
        }

        $ot->flexion_aleta = (trim($request->input('flexion_aleta')) != '') ? $request->input('flexion_aleta') : null;
        if (trim($request->input('flexion_aleta')) != '') {
            $datos_insertados['flexion_aleta'] = [
                'texto' => 'Flexion Aleta',
                'valor' => ['descripcion' => $request->input('flexion_aleta')]
            ];
        }

        $ot->peso = (trim($request->input('peso')) != '') ? $request->input('peso') : null;
        if (trim($request->input('peso')) != '') {
            $datos_insertados['peso'] = [
                'texto' => 'Peso',
                'valor' => ['descripcion' => $request->input('peso')]
            ];
        }

        $ot->incision_rayado_longitudinal = (trim($request->input('incision_rayado_longitudinal')) != '') ? $request->input('incision_rayado_longitudinal') : null;
        if (trim($request->input('incision_rayado_longitudinal')) != '') {
            $datos_insertados['incision_rayado_longitudinal'] = [
                'texto' => 'Incision Rayado Longitudinal',
                'valor' => ['descripcion' => $request->input('incision_rayado_longitudinal')]
            ];
        }

        $ot->incision_rayado_vertical = (trim($request->input('incision_rayado_vertical')) != '') ? $request->input('incision_rayado_vertical') : null;
        if (trim($request->input('incision_rayado_vertical')) != '') {
            $datos_insertados['incision_rayado_vertical'] = [
                'texto' => 'Incision Rayado Vertical',
                'valor' => ['descripcion' => $request->input('incision_rayado_vertical')]
            ];
        }

        $ot->fct = (trim($request->input('fct')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('fct'))) : null;
        if (trim($request->input('fct')) != '') {
            $datos_insertados['fct'] = [
                'texto' => 'Fct',
                'valor' => ['descripcion' => $request->input('fct')]
            ];
        }

        $ot->cobb_interior = (trim($request->input('cobb_interior')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('cobb_interior'))) : null;
        if (trim($request->input('cobb_interior')) != '') {
            $datos_insertados['cobb_interior'] = [
                'texto' => 'Cobb Interior',
                'valor' => ['descripcion' => $request->input('cobb_interior')]
            ];
        }

        $ot->cobb_exterior = (trim($request->input('cobb_exterior')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('cobb_exterior'))) : null;
        if (trim($request->input('cobb_exterior')) != '') {
            $datos_insertados['cobb_exterior'] = [
                'texto' => 'Cobb Exterior',
                'valor' => ['descripcion' => $request->input('cobb_exterior')]
            ];
        }

        $ot->espesor = (trim($request->input('espesor')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('espesor'))) : null;
        if (trim($request->input('espesor')) != '') {
            $datos_insertados['espesor'] = [
                'texto' => 'Espesor',
                'valor' => ['descripcion' => $request->input('espesor')]
            ];
        }

        $ot->cinta = (trim($request->input('cinta')) != '') ? $request->input('cinta') : null;
        if (trim($request->input('cinta')) != '') {
            $datos_insertados['cinta'] = [
                'texto' => 'Cinta',
                'valor' => ['descripcion' => $request->input('cinta')]
            ];
        }

        $ot->dst = (trim($request->input('dst')) != '') ? $request->input('dst') : null;
        if (trim($request->input('dst')) != '') {
            $datos_insertados['dst'] = [
                'texto' => 'Dst ',
                'valor' => ['descripcion' => $request->input('dst')]
            ];
        }

        $ot->espesor_placa = (trim($request->input('espesor_placa')) != '') ? $request->input('espesor_placa') : null;
        if (trim($request->input('espesor_placa')) != '') {
            $datos_insertados['espesor_placa'] = [
                'texto' => 'Espesor Placa ',
                'valor' => ['descripcion' => $request->input('espesor_placa')]
            ];
        }

        $ot->espesor_caja = (trim($request->input('espesor_caja')) != '') ? $request->input('espesor_caja') : null;
        if (trim($request->input('espesor_caja')) != '') {
            $datos_insertados['espesor_caja'] = [
                'texto' => 'Espesor Caja ',
                'valor' => ['descripcion' => $request->input('espesor_caja')]
            ];
        }

        $ot->porosidad = (trim($request->input('porosidad')) != '') ? $request->input('porosidad') : null;
        if (trim($request->input('porosidad')) != '') {
            $datos_insertados['porosidad'] = [
                'texto' => 'Porosidad ',
                'valor' => ['descripcion' => $request->input('porosidad')]
            ];
        }

        $ot->brillo = (trim($request->input('brillo')) != '') ? $request->input('brillo') : null;
        if (trim($request->input('brillo')) != '') {
            $datos_insertados['brillo'] = [
                'texto' => 'Brillo ',
                'valor' => ['descripcion' => $request->input('brillo')]
            ];
        }

        $ot->rigidez_4_ptos_long = (trim($request->input('rigidez_4_ptos_long')) != '') ? $request->input('rigidez_4_ptos_long') : null;
        if (trim($request->input('rigidez_4_ptos_long')) != '') {
            $datos_insertados['rigidez_4_ptos_long'] = [
                'texto' => 'Rigidez 4 Ptos Long ',
                'valor' => ['descripcion' => $request->input('rigidez_4_ptos_long')]
            ];
        }

        $ot->rigidez_4_ptos_transv = (trim($request->input('rigidez_4_ptos_transv')) != '') ? $request->input('rigidez_4_ptos_transv') : null;
        if (trim($request->input('rigidez_4_ptos_transv')) != '') {
            $datos_insertados['rigidez_4_ptos_transv'] = [
                'texto' => 'Rigidez 4 Ptos Transv ',
                'valor' => ['descripcion' => $request->input('rigidez_4_ptos_transv')]
            ];
        }

        $ot->angulo_deslizamiento_tapa_exterior = (trim($request->input('angulo_deslizamiento_tapa_exterior')) != '') ? $request->input('angulo_deslizamiento_tapa_exterior') : null;
        if (trim($request->input('angulo_deslizamiento_tapa_exterior')) != '') {
            $datos_insertados['angulo_deslizamiento_tapa_exterior'] = [
                'texto' => 'Angulo Deslizamiento Tapa Exterior ',
                'valor' => ['descripcion' => $request->input('angulo_deslizamiento_tapa_exterior')]
            ];
        }

        $ot->angulo_deslizamiento_tapa_interior = (trim($request->input('angulo_deslizamiento_tapa_interior')) != '') ? $request->input('angulo_deslizamiento_tapa_interior') : null;
        if (trim($request->input('angulo_deslizamiento_tapa_interior')) != '') {
            $datos_insertados['angulo_deslizamiento_tapa_interior'] = [
                'texto' => 'Angulo Deslizamiento Tapa Interior ',
                'valor' => ['descripcion' => $request->input('angulo_deslizamiento_tapa_interior')]
            ];
        }

        $ot->resistencia_frote = (trim($request->input('resistencia_frote')) != '') ? $request->input('resistencia_frote') : null;
        if (trim($request->input('resistencia_frote')) != '') {
            $datos_insertados['resistencia_frote'] = [
                'texto' => 'Resistencia Frote',
                'valor' => ['descripcion' => $request->input('resistencia_frote')]
            ];
        }

        $ot->contenido_reciclado = (trim($request->input('contenido_reciclado')) != '') ? $request->input('contenido_reciclado') : null;
        if (trim($request->input('contenido_reciclado')) != '') {
            $datos_insertados['contenido_reciclado'] = [
                'texto' => 'Contenido Reciclado',
                'valor' => ['descripcion' => $request->input('contenido_reciclado')]
            ];
        }
        // campos de Distancia cinta
        // Solo guardar el valor si no es vacio y si cinta = "SI"
        $ot->corte_liner = (trim($request->input('corte_liner')) != '' && $ot->cinta == 1) ? $request->input('corte_liner') : null;
        if (trim($request->input('corte_liner')) != '') {
            $datos_insertados['corte_liner'] = [
                'Texto' => 'Corte Liner',
                'valor' => ['descripcion' => $request->input('corte_liner')]
            ];
        }

        $ot->tipo_cinta = (trim($request->input('tipo_cinta')) != '' && $ot->cinta == 1) ? $request->input('tipo_cinta') : null;
        if (trim($request->input('tipo_cinta')) != '') {
            $datos_insertados['tipo_cinta'] = [
                'texto' => 'Tipo Cinta',
                'valor' => ['descripcion' => $request->input('tipo_cinta')]
            ];
        }

        $ot->cintas_x_caja = (trim($request->input('cintas_x_caja')) != '' && $ot->cinta == 1) ? $request->input('cintas_x_caja') : null;
        if (trim($request->input('cintas_x_caja')) != '') {
            $datos_insertados['cintas_x_caja'] = [
                'texto' => 'Tipo Cinta',
                'valor' => ['descripcion' => $request->input('cintas_x_caja')]
            ];
        }
        $ot->distancia_cinta_1 = (trim($request->input('distancia_cinta_1')) != '' && $ot->cinta == 1) ? $request->input('distancia_cinta_1') : null;
        if (trim($request->input('distancia_cinta_1')) != '') {
            $datos_insertados['distancia_cinta_1'] = [
                'texto' => 'Distancia Cinta 1',
                'valor' => ['descripcion' => $request->input('distancia_cinta_1')]
            ];
        }

        $ot->distancia_cinta_2 = (trim($request->input('distancia_cinta_2')) != '' && $ot->cinta == 1) ? $request->input('distancia_cinta_2') : null;
        if (trim($request->input('distancia_cinta_2')) != '') {
            $datos_insertados['distancia_cinta_2'] = [
                'texto' => 'Distancia Cinta 2',
                'valor' => ['descripcion' => $request->input('distancia_cinta_2')]
            ];
        }

        $ot->distancia_cinta_3 = (trim($request->input('distancia_cinta_3')) != '' && $ot->cinta == 1) ? $request->input('distancia_cinta_3') : null;
        if (trim($request->input('distancia_cinta_3')) != '') {
            $datos_insertados['distancia_cinta_3'] = [
                'texto' => 'Distancia Cinta 3',
                'valor' => ['descripcion' => $request->input('distancia_cinta_3')]
            ];
        }

        $ot->distancia_cinta_4 = (trim($request->input('distancia_cinta_4')) != '' && $ot->cinta == 1) ? $request->input('distancia_cinta_4') : null;
        if (trim($request->input('distancia_cinta_4')) != '') {
            $datos_insertados['distancia_cinta_4'] = [
                'texto' => 'Distancia Cinta 4',
                'valor' => ['descripcion' => $request->input('distancia_cinta_4')]
            ];
        }

        $ot->distancia_cinta_5 = (trim($request->input('distancia_cinta_5')) != '' && $ot->cinta == 1) ? $request->input('distancia_cinta_5') : null;
        if (trim($request->input('distancia_cinta_5')) != '') {
            $datos_insertados['distancia_cinta_5'] = [
                'texto' => 'Distancia Cinta 5',
                'valor' => ['descripcion' => $request->input('distancia_cinta_5')]
            ];
        }

        $ot->distancia_cinta_6 = (trim($request->input('distancia_cinta_6')) != '' && $ot->cinta == 1) ? $request->input('distancia_cinta_6') : null;
        if (trim($request->input('distancia_cinta_6')) != '') {
            $datos_insertados['distancia_cinta_6'] = [
                'texto' => 'Distancia Cinta 6',
                'valor' => ['descripcion' => $request->input('distancia_cinta_6')]
            ];
        }

        // Color barniz cera
        $ot->impresion = (trim($request->input('impresion')) != '') ? $request->input('impresion') : null;
        if (trim($request->input('impresion')) != '') {
            $datos_insertados['impresion'] = [
                'texto' => 'Impresion',
                'valor' => ['descripcion' => $request->input('impresion')]
            ];
        }

        $ot->trazabilidad = (trim($request->input('trazabilidad')) != '') ? $request->input('trazabilidad') : null;
        if (trim($request->input('trazabilidad')) != '') {
            $datos_insertados['trazabilidad'] = [
                'texto' => 'Trazabilidad',
                'valor' => ['descripcion' => $request->input('trazabilidad')]
            ];
        }

        $ot->design_type_id = (trim($request->input('design_type_id')) != '') ? $request->input('design_type_id') : null;
        if (trim($request->input('design_type_id')) != '') {
            $datos_insertados['design_type_id'] = [
                'texto' => 'Tipo Diseño Id',
                'valor' => ['descripcion' => $request->input('design_type_id')]
            ];
        }

        $ot->complejidad = (trim($request->input('complejidad')) != '') ? $request->input('complejidad') : $ot->complejidad;
        if (trim($request->input('complejidad')) != '') {
            $datos_insertados['complejidad'] = [
                'texto' => 'Complejidad',
                'valor' => ['descripcion' => $request->input('complejidad')]
            ];
        }

        $ot->coverage_internal_id = (trim($request->input('coverage_internal_id')) != '') ? $request->input('coverage_internal_id') : null;
        if (trim($request->input('coverage_internal_id')) != '') {
            $datos_insertados['coverage_internal_id'] = [
                'texto' => 'Coverage Internal Id',
                'valor' => ['descripcion' => $request->input('coverage_internal_id')]
            ];
        }

        $ot->percentage_coverage_internal  = (trim($request->input('percentage_coverage_internal')) != '') ? $request->input('percentage_coverage_internal') : null;
        if (trim($request->input('percentage_coverage_internal')) != '') {
            $datos_insertados['percentage_coverage_internal'] = [
                'texto' => 'Percentage Coverage Internal',
                'valor' => ['descripcion' => $request->input('percentage_coverage_internal')]
            ];
        }

        $ot->coverage_external_id = (trim($request->input('coverage_external_id')) != '') ? $request->input('coverage_external_id') : null;
        if (trim($request->input('coverage_external_id')) != '') {
            $datos_insertados['coverage_external_id'] = [
                'texto' => 'Coverage External Id',
                'valor' => ['descripcion' => $request->input('coverage_external_id')]
            ];
        }

        $ot->percentage_coverage_external = (trim($request->input('percentage_coverage_external')) != '') ? $request->input('percentage_coverage_external') : null;
        if (trim($request->input('percentage_coverage_external')) != '') {
            $datos_insertados['percentage_coverage_external'] = [
                'texto' => 'Percentage Coverage External',
                'valor' => ['descripcion' => $request->input('percentage_coverage_external')]
            ];
        }

        $ot->numero_colores = (trim($request->input('numero_colores')) != '') ? $request->input('numero_colores') : null;
        if (trim($request->input('numero_colores')) != '') {
            $datos_insertados['numero_colores'] = [
                'texto' => 'Numero Colores',
                'valor' => ['descripcion' => $request->input('numero_colores')]
            ];
        }

        $ot->color_1_id  = (trim($request->input('color_1_id')) != '') ? $request->input('color_1_id') : null;
        if (trim($request->input('color_1_id')) != '') {
            $datos_insertados['color_1_id'] = [
                'texto' => 'Color 1 Id',
                'valor' => ['descripcion' => $request->input('color_1_id')]
            ];
        }

        $ot->impresion_1 = (trim($request->input('impresion_1')) != '') ? $request->input('impresion_1') : null;
        if (trim($request->input('impresion_1')) != '') {
            $datos_insertados['impresion_1'] = [
                'texto' => 'Impresion 1',
                'valor' => ['descripcion' => $request->input('impresion_1')]
            ];
        }

        $ot->color_2_id = (trim($request->input('color_2_id')) != '') ? $request->input('color_2_id') : null;
        if (trim($request->input('color_2_id')) != '') {
            $datos_insertados['color_2_id'] = [
                'texto' => 'Color 2 Id',
                'valor' => ['descripcion' => $request->input('color_2_id')]
            ];
        }

        $ot->impresion_2 = (trim($request->input('impresion_2')) != '') ? $request->input('impresion_2') : null;
        if (trim($request->input('impresion_2')) != '') {
            $datos_insertados['impresion_2'] = [
                'texto' => 'Impresion 2',
                'valor' => ['descripcion' => $request->input('impresion_2')]
            ];
        }

        $ot->color_3_id = (trim($request->input('color_3_id')) != '') ? $request->input('color_3_id') : null;
        if (trim($request->input('color_3_id')) != '') {
            $datos_insertados['color_3_id'] = [
                'texto' => 'Color 3 Id',
                'valor' => ['descripcion' => $request->input('color_3_id')]
            ];
        }

        $ot->impresion_3 = (trim($request->input('impresion_3')) != '') ? $request->input('impresion_3') : null;
        if (trim($request->input('impresion_3')) != '') {
            $datos_insertados['impresion_3'] = [
                'texto' => 'Impresion 3',
                'valor' => ['descripcion' => $request->input('impresion_3')]
            ];
        }

        $ot->color_4_id = (trim($request->input('color_4_id')) != '') ? $request->input('color_4_id') : null;
        if (trim($request->input('color_4_id')) != '') {
            $datos_insertados['color_4_id'] = [
                'texto' => 'Color 4 Id',
                'valor' => ['descripcion' => $request->input('color_4_id')]
            ];
        }

        $ot->impresion_4 = (trim($request->input('impresion_4')) != '') ? $request->input('impresion_4') : null;
        if (trim($request->input('impresion_4')) != '') {
            $datos_insertados['impresion_4'] = [
                'texto' => 'Impresion 4',
                'valor' => ['descripcion' => $request->input('impresion_4')]
            ];
        }

        $ot->color_5_id = (trim($request->input('color_5_id')) != '') ? $request->input('color_5_id') : null;
        if (trim($request->input('color_5_id')) != '') {
            $datos_insertados['color_5_id'] = [
                'texto' => 'Color 5 Id',
                'valor' => ['descripcion' => $request->input('color_5_id')]
            ];
        }

        $ot->impresion_5 = (trim($request->input('impresion_5')) != '') ? $request->input('impresion_5') : null;
        if (trim($request->input('impresion_5')) != '') {
            $datos_insertados['impresion_5'] = [
                'texto' => 'Impresion 5',
                'valor' => ['descripcion' => $request->input('impresion_5')]
            ];
        }

        $ot->color_6_id = (trim($request->input('color_6_id')) != '') ? $request->input('color_6_id') : null;
        if (trim($request->input('color_6_id')) != '') {
            $datos_insertados['color_6_id'] = [
                'texto' => 'Color 6 Id',
                'valor' => ['descripcion' => $request->input('color_6_id')]
            ];
        }

        $ot->impresion_6 = (trim($request->input('impresion_6')) != '') ? $request->input('impresion_6') : null;
        if (trim($request->input('impresion_6')) != '') {
            $datos_insertados['impresion_6'] = [
                'texto' => 'Impresion 6',
                'valor' => ['descripcion' => $request->input('impresion_6')]
            ];
        }
        //Se Desabilita a solicitud de correccion del Evolutivo 72 (Eliminar Barniz UV y % Impresión B. UV)
        //Utilizando los datos para este campo de los que vengan del input coverage_external_id y percentage_coverage_external
        $ot->barniz_uv = (trim($request->input('barniz_uv')) != '') ? $request->input('barniz_uv') : null;
        if (trim($request->input('barniz_uv')) != '') {
            $datos_insertados['barniz_uv'] = [
                'texto' => 'Barniz Uv',
                'valor' => ['descripcion' => $request->input('barniz_uv')]
            ];
        }

        $ot->porcentanje_barniz_uv = (trim($request->input('porcentanje_barniz_uv')) != '') ? $request->input('porcentanje_barniz_uv') : null;
        if (trim($request->input('porcentanje_barniz_uv')) != '') {
            $datos_insertados['porcentanje_barniz_uv'] = [
                'texto' => 'Porcentanje Barniz Uv',
                'valor' => ['descripcion' => $request->input('porcentanje_barniz_uv')]
            ];
        }
        //$ot->barniz_uv               = (trim($request->input('coverage_external_id')) == 4) ? 1 : null;
        //$ot->porcentanje_barniz_uv   = (trim($request->input('coverage_external_id')) == 4 && trim($request->input('percentage_coverage_external')) != '') ? $request->input('percentage_coverage_external') : null;

        $ot->color_interno = (trim($request->input('color_1_id')) != '') ? $request->input('color_1_id') : null;
        if (trim($request->input('color_1_id')) != '') {
            $datos_insertados['color_interno'] = [
                'texto' => 'Color Interno',
                'valor' => ['descripcion' => $request->input('color_1_id')]
            ];
        }

        $ot->impresion_color_interno = (trim($request->input('impresion_1')) != '') ? $request->input('impresion_1') : null;
        if (trim($request->input('impresion_1')) != '') {
            $datos_insertados['impresion_color_interno'] = [
                'texto' => 'Impresion Color Interno',
                'valor' => ['descripcion' => $request->input('impresion_1')]
            ];
        }

        $ot->indicador_facturacion_diseno_grafico = (trim($request->input('indicador_facturacion_diseno_grafico')) != '') ? $request->input('indicador_facturacion_diseno_grafico') : null;
        if (trim($request->input('indicador_facturacion_diseno_grafico')) != '') {
            $datos_insertados['indicador_facturacion_diseno_grafico'] = [
                'texto' => 'Indicador Facturacion Diseno Grafico',
                'valor' => ['descripcion' => $request->input('indicador_facturacion_diseno_grafico')]
            ];
        }

        $ot->prueba_color = (trim($request->input('prueba_color')) != '') ? $request->input('prueba_color') : null;
        if (trim($request->input('prueba_color')) != '') {
            $datos_insertados['prueba_color'] = [
                'texto' => 'Prueba de Color',
                'valor' => ['descripcion' => $request->input('prueba_color')]
            ];
        }

        $ot->pegado = (trim($request->input('pegado')) != '') ? $request->input('pegado') : null;
        if (trim($request->input('pegado')) != '') {
            $datos_insertados['pegado'] = [
                'texto' => 'Pegado',
                'valor' => ['descripcion' => $request->input('pegado')]
            ];
        }

        $ot->cera_exterior = (trim($request->input('cera_exterior')) != '') ? $request->input('cera_exterior') : null;
        if (trim($request->input('cera_exterior')) != '') {
            $datos_insertados['cera_exterior'] = [
                'texto' => 'Cera Exterior',
                'valor' => ['descripcion' => $request->input('cera_exterior')]
            ];
        }

        $ot->cera_interior = (trim($request->input('cera_interior')) != '') ? $request->input('cera_interior') : null;
        if (trim($request->input('cera_interior')) != '') {
            $datos_insertados['cera_interior'] = [
                'texto' => 'Cera Interior',
                'valor' => ['descripcion' => $request->input('cera_interior')]
            ];
        }

        $ot->barniz_interior = (trim($request->input('barniz_interior')) != '') ? $request->input('barniz_interior') : null;
        if (trim($request->input('barniz_interior')) != '') {
            $datos_insertados['barniz_interior'] = [
                'texto' => 'Barniz Interior',
                'valor' => ['descripcion' => $request->input('barniz_interior')]
            ];
        }

        $ot->fsc = (trim($request->input('fsc')) != '') ? $request->input('fsc') : null;
        if (trim($request->input('fsc')) != '') {
            $datos_insertados['fsc'] = [
                'texto' => 'Fsc',
                'valor' => ['descripcion' => $request->input('fsc')]
            ];
        }

        $ot->fsc_observacion = (trim($request->input('fsc_observacion')) != '') ? $request->input('fsc_observacion') : null;
        if (trim($request->input('fsc_observacion')) != '') {
            $datos_insertados['fsc_observacion'] = [
                'texto' => 'Fsc Observacion',
                'valor' => ['descripcion' => $request->input('fsc_observacion')]
            ];
        }

        $ot->pais_id = (trim($request->input('pais_id')) != '') ? $request->input('pais_id') : null;
        if (trim($request->input('pais_id')) != '') {
            $datos_insertados['pais_id'] = [
                'texto' => 'Pais Id',
                'valor' => ['descripcion' => $request->input('pais_id')]
            ];
        }

        $ot->planta_id = (trim($request->input('planta_id')) != '') ? $request->input('planta_id') : null;
        if (trim($request->input('planta_id')) != '') {
            $datos_insertados['planta_id'] = [
                'texto' => 'Planta Id',
                'valor' => ['descripcion' => $request->input('planta_id')]
            ];
        }

        $ot->restriccion_pallet = (trim($request->input('restriccion_pallet')) != '') ? $request->input('restriccion_pallet') : null;
        if (trim($request->input('restriccion_pallet')) != '') {
            $datos_insertados['restriccion_pallet'] = [
                'texto' => 'Restriccion Pallet',
                'valor' => ['descripcion' => $request->input('restriccion_pallet')]
            ];
        }

        $ot->tamano_pallet_type_id = (trim($request->input('tamano_pallet_type_id')) != '') ? $request->input('tamano_pallet_type_id') : null;
        if (trim($request->input('tamano_pallet_type_id')) != '') {
            $datos_insertados['tamano_pallet_type_id'] = [
                'texto' => 'Tamano Pallet Type Id',
                'valor' => ['descripcion' => $request->input('tamano_pallet_type_id')]
            ];
        }

        $ot->altura_pallet = (trim($request->input('altura_pallet')) != '') ? $request->input('altura_pallet') : null;
        if (trim($request->input('altura_pallet')) != '') {
            $datos_insertados['altura_pallet'] = [
                'texto' => 'Altura Pallet',
                'valor' => ['descripcion' => $request->input('altura_pallet')]
            ];
        }

        $ot->permite_sobresalir_carga = (trim($request->input('permite_sobresalir_carga')) != '') ? $request->input('permite_sobresalir_carga') : null;
        if (trim($request->input('permite_sobresalir_carga')) != '') {
            $datos_insertados['permite_sobresalir_carga'] = [
                'texto' => 'Permite Sobresalir Carga',
                'valor' => ['descripcion' => $request->input('permite_sobresalir_carga')]
            ];
        }
        // Medidas Interiores
        $ot->interno_largo = (trim($request->input('interno_largo')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('interno_largo'))) : null;
        if (trim($request->input('interno_largo')) != '') {
            $datos_insertados['interno_largo'] = [
                'texto' => 'Interno Largo',
                'valor' => ['descripcion' => $request->input('interno_largo')]
            ];
        }

        $ot->interno_ancho = (trim($request->input('interno_ancho')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('interno_ancho'))) : null;
        if (trim($request->input('interno_ancho')) != '') {
            $datos_insertados['interno_ancho'] = [
                'texto' => 'Interno Ancho',
                'valor' => ['descripcion' => $request->input('interno_ancho')]
            ];
        }

        $ot->interno_alto = (trim($request->input('interno_alto')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('interno_alto'))) : null;
        if (trim($request->input('interno_alto')) != '') {
            $datos_insertados['interno_alto'] = [
                'texto' => 'Interno Alto',
                'valor' => ['descripcion' => $request->input('interno_alto')]
            ];
        }

        // Medidas Exteriores
        $ot->externo_largo = (trim($request->input('externo_largo')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('externo_largo'))) : null;
        if (trim($request->input('externo_largo')) != '') {
            $datos_insertados['externo_largo'] = [
                'texto' => 'Externo Largo',
                'valor' => ['descripcion' => $request->input('externo_largo')]
            ];
        }

        $ot->externo_ancho = (trim($request->input('externo_ancho')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('externo_ancho'))) : null;
        if (trim($request->input('externo_ancho')) != '') {
            $datos_insertados['externo_ancho'] = [
                'texto' => 'Externo Ancho',
                'valor' => ['descripcion' => $request->input('externo_ancho')]
            ];
        }

        $ot->externo_alto = (trim($request->input('externo_alto')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('externo_alto'))) : null;
        if (trim($request->input('externo_alto')) != '') {
            $datos_insertados['externo_alto'] = [
                'texto' => 'Externo Alto',
                'valor' => ['descripcion' => $request->input('externo_alto')]
            ];
        }

        // Terminaciones
        $ot->process_id = (trim($request->input('process_id')) != '') ? $request->input('process_id') : $ot->process_id;
        if (trim($request->input('process_id')) != '') {
            $datos_insertados['process_id'] = [
                'texto' => 'Process Id',
                'valor' => ['descripcion' => $request->input('process_id')]
            ];
        }

        $ot->pegado_terminacion = (trim($request->input('pegado_terminacion')) != '') ? $request->input('pegado_terminacion') : $ot->pegado_terminacion;
        if (trim($request->input('pegado_terminacion')) != '') {
            $datos_insertados['pegado_terminacion'] = [
                'texto' => 'Pegado Terminacion',
                'valor' => ['descripcion' => $request->input('pegado_terminacion')]
            ];
        }

        $ot->armado_id = (trim($request->input('armado_id')) != '') ? $request->input('armado_id') : $ot->armado_id;
        if (trim($request->input('armado_id')) != '') {
            $datos_insertados['armado_id'] = [
                'texto' => 'Armado Id',
                'valor' => ['descripcion' => $request->input('armado_id')]
            ];
        }

        $ot->sentido_armado = (trim($request->input('sentido_armado')) != '') ? $request->input('sentido_armado') : $ot->sentido_armado;
        if (trim($request->input('sentido_armado')) != '') {
            $datos_insertados['sentido_armado'] = [
                'texto' => 'Sentido Armado',
                'valor' => ['descripcion' => $request->input('sentido_armado')]
            ];
        }

        // material asignado descripcion
        // $ot->descripcion_material = (trim($request->input('descripcion_material')) != '') ? $request->input('descripcion_material') : $ot->descripcion_material;

        // Datos para desarrollo
        $ot->peso_contenido_caja = (trim($request->input('peso_contenido_caja')) != '') ? $request->input('peso_contenido_caja') : $ot->peso_contenido_caja;
        if (trim($request->input('peso_contenido_caja')) != '') {
            $datos_insertados['peso_contenido_caja'] = [
                'texto' => 'Peso Contenido Caja',
                'valor' => ['descripcion' => $request->input('peso_contenido_caja')]
            ];
        }

        $ot->autosoportante = (trim($request->input('autosoportante')) != '') ? $request->input('autosoportante') : $ot->autosoportante;
        if (trim($request->input('autosoportante')) != '') {
            $datos_insertados['autosoportante'] = [
                'texto' => 'Autosoportante',
                'valor' => ['descripcion' => $request->input('autosoportante')]
            ];
        }

        $ot->envase_id = (trim($request->input('envase_id')) != '') ? $request->input('envase_id') : $ot->envase_id;
        if (trim($request->input('envase_id')) != '') {
            $datos_insertados['envase_id'] = [
                'texto' => 'Envase Id',
                'valor' => ['descripcion' => $request->input('envase_id')]
            ];
        }

        $ot->cajas_altura = (trim($request->input('cajas_altura')) != '') ? $request->input('cajas_altura') : $ot->cajas_altura;
        if (trim($request->input('cajas_altura')) != '') {
            $datos_insertados['cajas_altura'] = [
                'texto' => 'Cajas Altura',
                'valor' => ['descripcion' => $request->input('cajas_altura')]
            ];
        }

        $ot->pallet_sobre_pallet = (trim($request->input('pallet_sobre_pallet')) != '') ? $request->input('pallet_sobre_pallet') : $ot->pallet_sobre_pallet;
        if (trim($request->input('pallet_sobre_pallet')) != '') {
            $datos_insertados['pallet_sobre_pallet'] = [
                'texto' => 'Pallet Sobre Pallet',
                'valor' => ['descripcion' => $request->input('pallet_sobre_pallet')]
            ];
        }

        $ot->cantidad = (trim($request->input('cantidad')) != '') ? $request->input('cantidad') : $ot->cantidad;
        if (trim($request->input('cantidad')) != '') {
            $datos_insertados['cantidad'] = [
                'texto' => 'Cantidad',
                'valor' => ['descripcion' => $request->input('cantidad')]
            ];
        }

        // Observacion
        $ot->observacion = (trim($request->input('observacion')) != '') ? $request->input('observacion') : $ot->observacion;
        if (trim($request->input('observacion')) != '') {
            $datos_insertados['observacion'] = [
                'texto' => 'Observacion',
                'valor' => ['descripcion' => $request->input('observacion')]
            ];
        }

        $ot->creador_id = (trim($request->input('vendedor_id')) != '') ? $request->input('vendedor_id') : auth()->user()->id;
        if (trim($request->input('vendedor_id')) != '') {
            $datos_insertados['vendedor_id'] = [
                'texto' => 'Vendedor Id',
                'valor' => ['descripcion' => $request->input('vendedor_id')]
            ];
        }

        $ot->detalle_id = (trim($request->input('detalle_id')) != '') ? $request->input('detalle_id') : null;
        if (trim($request->input('detalle_id')) != '') {
            $datos_insertados['detalle_id'] = [
                'texto' => 'Detalle Id',
                'valor' => ['descripcion' => $request->input('detalle_id')]
            ];
        }

        $ot->current_area_id = 1;
        $ot->ultimo_cambio_area = Carbon::now();

        //Maquila
        $ot->maquila = (trim($request->input('maquila')) != '') ? $request->input('maquila') :  null;
        if (trim($request->input('maquila')) != '') {
            $datos_insertados['maquila'] = [
                'texto' => 'Maquila',
                'valor' => ['descripcion' => $request->input('maquila')]
            ];
        }

        $ot->maquila_servicio_id = (trim($request->input('maquila_servicio_id')) != '') ? $request->input('maquila_servicio_id') : null;
        if (trim($request->input('maquila_servicio_id')) != '') {
            $datos_insertados['maquila_servicio_id'] = [
                'texto' => 'Maquila Servicio Id',
                'valor' => ['descripcion' => $request->input('maquila_servicio_id')]
            ];
        }
        $ot->so_planta_original = (trim($request->input('sec_operacional_principal')) != '') ? $request->input('sec_operacional_principal') : null;
        if (trim($request->input('sec_operacional_principal')) != '') {
            $datos_insertados['so_planta_original'] = [
                'texto' => 'Planta Original',
                'valor' => ['descripcion' => $request->input('sec_operacional_principal')]
            ];
        }
        $ot->so_planta_original = (trim($request->input('sec_operacional_1')) != '') ? $request->input('sec_operacional_1') : null;
        if (trim($request->input('sec_operacional_1')) != '') {
            $datos_insertados['so_planta_alt1'] = [
                'texto' => 'Planta Alt1',
                'valor' => ['descripcion' => $request->input('sec_operacional_1')]
            ];
        }
        $ot->so_planta_original = (trim($request->input('sec_operacional_2')) != '') ? $request->input('sec_operacional_2') : null;
        if (trim($request->input('sec_operacional_2')) != '') {
            $datos_insertados['so_planta_alt2'] = [
                'texto' => 'Planta Alt2',
                'valor' => ['descripcion' => $request->input('sec_operacional_2')]
            ];
        }
        // Aprobacion de jefes
        // La regla de autorización es la siguiente:

        // Si son 2 o menos sin autorización
        // 3 o hasta 5 pasa por el jefe de ventas
        // De 6 al infinito, primero jefe de ventas y después jefe desarrollo
        if ($ot->tipo_solicitud == 3 &&  $ot->numero_muestras >= 6) {
            $ot->aprobacion_jefe_desarrollo = 1;
            $ot->aprobacion_jefe_venta = 1;
        }
        if ($ot->tipo_solicitud == 3 &&  $ot->numero_muestras > 2  && $ot->numero_muestras < 6) {
            $ot->aprobacion_jefe_venta = 1;
        }
        //Se realiza un proceso de creacion y asignacion de codigo para la pre-catalogacion del material
        //de los tipo de solicitud Desarrollo Completa y arte con Material (1 y 5)
        if ($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 5 || $ot->tipo_solicitud == 7) {

            //El cliente solicito no tomar en cuenta el codigo 700000 ya que esta utilizado en su
            //proceso interno de catalogacion y asi evitar que este codigo se repita
            $max_id = MaterialsCode::orderBy('id', 'desc')->first();
            if ($max_id->id == '699999') {
                $codigo_material = new MaterialsCode();
                $codigo_material->id = '700001';
                $codigo_material->save();
                $ot->material_code = $codigo_material->id;
            } else {
                $codigo_material = new MaterialsCode();
                $codigo_material->save();
                $ot->material_code = $codigo_material->id;
            }
        }

        // Antecdentes Desarrollo
        if (in_array('check_correo_cliente', $request->input('checkboxes'))) {
            $ot->ant_des_correo_cliente = 1;
            $datos_insertados['ant_des_correo_cliente'] = [
                'texto' => 'Ant. Des. Correo Cliente',
                'valor' => ['descripcion' => 'Si']
            ];
        }

        if (in_array('check_plano_actual', $request->input('checkboxes'))) {
            $ot->ant_des_plano_actual = 1;
            $datos_insertados['ant_des_plano_actual'] = [
                'texto' => 'Ant. Des. Plano Actual',
                'valor' => ['descripcion' => 'Si']
            ];
        }

        if (in_array('check_boceto_actual', $request->input('checkboxes'))) {
            $ot->ant_des_boceto_actual = 1;
            $datos_insertados['ant_des_boceto_actual'] = [
                'texto' => 'Ant. Des. Boceto Actual',
                'valor' => ['descripcion' => 'Si']
            ];
        }

        if (in_array('check_speed', $request->input('checkboxes'))) {
            $ot->ant_des_speed = 1;
            $datos_insertados['ant_des_speed'] = [
                'texto' => 'Ant. Des. Speck',
                'valor' => ['descripcion' => 'Si']
            ];
        }

        if (in_array('check_otro', $request->input('checkboxes'))) {
            $ot->ant_des_otro = 1;
            $datos_insertados['ant_des_otro'] = [
                'texto' => 'Ant. Des. Otro',
                'valor' => ['descripcion' => 'Si']
            ];
        }


        if ($request->hasFile('file_check_vb_muestra')) {
            $ot->ant_des_vb_muestra = 1;
            $datos_insertados['ant_des_vb_muestra'] = [
                'texto' => 'Ant. Des. VB Muestra',
                'valor' => ['descripcion' => 'Si']
            ];
        }

        if ($request->hasFile('file_check_vb_boce')) {
            $ot->ant_des_vb_boce = 1;
            $datos_insertados['ant_des_vb_boce'] = [
                'texto' => 'Ant. Des. VB Boce',
                'valor' => ['descripcion' => 'Si']
            ];
        }


        // if (in_array('check_vb_muestra', $request->input('checkboxes'))) {
        //     $ot->ant_des_vb_muestra = 1;
        //     $datos_insertados['ant_des_vb_muestra'] = [
        //         'texto' => 'Ant. Des. VB Muestra',
        //         'valor' => ['descripcion' => 'Si']
        //     ];
        // }

        // if (in_array('check_vb_boce', $request->input('checkboxes'))) {
        //     $ot->ant_des_vb_boce = 1;
        //     $datos_insertados['ant_des_vb_boce'] = [
        //         'texto' => 'Ant. Des. VB Boce',
        //         'valor' => ['descripcion' => 'Si']
        //     ];
        // }

        if (in_array('check_referencia_de', $request->input('checkboxes'))) {
            $ot->ant_des_cj_referencia_de = 1;
            $datos_insertados['ant_des_cj_referencia_de'] = [
                'texto' => 'Ant. Des. Cj Referenca Diseño Estructural',
                'valor' => ['descripcion' => 'Si']
            ];
        }

        if (in_array('check_referencia_dg', $request->input('checkboxes'))) {
            $ot->ant_des_cj_referencia_dg = 1;
            $datos_insertados['ant_des_cj_referencia_dg'] = [
                'texto' => 'Ant. Des. Referenca Diseño Grafico',
                'valor' => ['descripcion' => 'Si']
            ];
        }
        if (in_array('check_envase_primario', $request->input('checkboxes'))) {
            $ot->ant_des_envase_primario = 1;
            $datos_insertados['ant_des_envase_primario'] = [
                'texto' => 'Ant. Des. Envase Primario',
                'valor' => ['descripcion' => 'Si']
            ];
        }
        if (in_array('check_conservar_si', $request->input('checkboxes'))) {
            $ot->ant_des_conservar_muestra = 1;
            $datos_insertados['ant_des_conservar_muestra'] = [
                'texto' => 'Ant. Des. Conservar Muestra',
                'valor' => ['descripcion' => 'Si']
            ];
        }

        if (in_array('check_conservar_no', $request->input('checkboxes'))) {
            $ot->ant_des_conservar_muestra = 0;
            $datos_insertados['ant_des_conservar_muestra'] = [
                'texto' => 'Ant. Des. Conservar Muestra',
                'valor' => ['descripcion' => 'Si']
            ];
        }

        if (in_array('check_armado_automatico_si', $request->input('checkboxes'))) {
            $ot->armado_automatico = 1;
            $datos_insertados['armado_automatico'] = [
                'texto' => 'Armado automatico',
                'valor' => ['descripcion' => 'Si']
            ];
        }
        if (in_array('check_armado_automatico_no', $request->input('checkboxes'))) {
            $ot->armado_automatico = 0;
            $datos_insertados['armado_automatico'] = [
                'texto' => 'Armado automatico',
                'valor' => ['descripcion' => 'Si']
            ];
        }
        //Registro si viene Archivo Adjunto de correo de Cliente
        if ($request->hasfile('file_check_correo_cliente')) {
            $archivo = $request->file('file_check_correo_cliente');
            $file = new File();

            $filename = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $filename;

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
            $ot->ant_des_correo_cliente_file = '/files/' . $name . '.' . $extension;
        }
        //Registro si viene Archivo Adjunto de plano actual
        if ($request->hasfile('file_check_plano_actual')) {
            $archivo = $request->file('file_check_plano_actual');
            $file = new File();

            $filename = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $filename;

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
            $ot->ant_des_plano_actual_file = '/files/' . $name . '.' . $extension;
        }
        //Registro si viene Archivo Adjunto de boceto actual
        if ($request->hasfile('file_check_boceto_actual')) {
            $archivo = $request->file('file_check_boceto_actual');
            $file = new File();

            $filename = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $filename;

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
            $ot->ant_des_boceto_actual_file = '/files/' . $name . '.' . $extension;
        }
        //Registro si viene Archivo Adjunto de Speed
        if ($request->hasfile('file_check_speed')) {
            $archivo = $request->file('file_check_speed');
            $file = new File();

            $filename = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $filename;

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
            $ot->ant_des_speed_file = '/files/' . $name . '.' . $extension;
        }
        //Registro si viene Archivo Adjunto de boceto actual
        if ($request->hasfile('file_check_otro')) {
            $archivo = $request->file('file_check_otro');
            $file = new File();

            $filename = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $filename;

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
            $ot->ant_des_otro_file = '/files/' . $name . '.' . $extension;
        }

        //Registro si viene Archivo Adjunto de vb muestra actual
        if ($request->hasfile('file_check_vb_muestra')) {
            $archivo = $request->file('file_check_vb_muestra');
            $file = new File();

            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $filename;

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
            $ot->ant_des_vb_muestra_file = '/files/' . $name . '.' . $extension;
            $ot->ant_des_vb_muestra_file_date = Carbon::now();
        }

        //Registro si viene Archivo Adjunto de vb boce actual
        if ($request->hasfile('file_check_vb_boce')) {
            $archivo = $request->file('file_check_vb_boce');
            $file = new File();

            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $filename;

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
            $ot->ant_des_vb_boce_file = '/files/' . $name . '.' . $extension;
            $ot->ant_des_vb_boce_file_date = Carbon::now();
        }


        //Nuevos Campos Datos para desarrollo
        $ot->product_type_developing_id = (trim($request->input('product_type_developing_id')) != '') ? $request->input('product_type_developing_id') : null;
        if (trim($request->input('product_type_developing_id')) != '') {
            $datos_insertados['product_type_developing_id'] = [
                'texto' => 'Product Type Developing Id',
                'valor' => ['descripcion' => $request->input('product_type_developing_id')]
            ];
        }

        $ot->food_type_id = (trim($request->input('food_type_id')) != '') ? $request->input('food_type_id') : null;
        if (trim($request->input('food_type_id')) != '') {
            $datos_insertados['food_type_id'] = [
                'texto' => 'Food Type Id',
                'valor' => ['descripcion' => $request->input('food_type_id')]
            ];
        }

        $ot->expected_use_id = (trim($request->input('expected_use_id')) != '') ? $request->input('expected_use_id') : null;
        if (trim($request->input('expected_use_id')) != '') {
            $datos_insertados['expected_use_id'] = [
                'texto' => 'Expected Use Id',
                'valor' => ['descripcion' => $request->input('expected_use_id')]
            ];
        }

        $ot->recycled_use_id = (trim($request->input('recycled_use_id')) != '') ? $request->input('recycled_use_id') : null;
        if (trim($request->input('recycled_use_id')) != '') {
            $datos_insertados['recycled_use_id'] = [
                'texto' => 'Recycled Use Id',
                'valor' => ['descripcion' => $request->input('recycled_use_id')]
            ];
        }

        $ot->class_substance_packed_id = (trim($request->input('class_substance_packed_id')) != '') ? $request->input('class_substance_packed_id') : null;
        if (trim($request->input('class_substance_packed_id')) != '') {
            $datos_insertados['class_substance_packed_id'] = [
                'texto' => 'Class Substance Packed Id',
                'valor' => ['descripcion' => $request->input('class_substance_packed_id')]
            ];
        }

        $ot->transportation_way_id = (trim($request->input('transportation_way_id')) != '') ? $request->input('transportation_way_id') : null;
        if (trim($request->input('transportation_way_id')) != '') {
            $datos_insertados['transportation_way_id'] = [
                'texto' => 'Transportation Way Id',
                'valor' => ['descripcion' => $request->input('transportation_way_id')]
            ];
        }

        $ot->target_market_id = (trim($request->input('target_market_id')) != '') ? $request->input('target_market_id') : null;
        if (trim($request->input('target_market_id')) != '') {
            $datos_insertados['target_market_id'] = [
                'texto' => 'Target Market Id',
                'valor' => ['descripcion' => $request->input('target_market_id')]
            ];
        }

        $ot->pallet_qa_id = (trim($request->input('pallet_qa_id')) != '') ? $request->input('pallet_qa_id') : null;
        if (trim($request->input('pallet_qa_id')) != '') {
            $datos_insertados['pallet_qa_id'] = [
                'texto' => 'Certificado Calidad',
                'valor' => ['descripcion' => $request->input('pallet_qa_id')]
            ];
        }

        $ot->bulto_zunchado = (trim($request->input('bulto_zunchado')) != '') ? $request->input('bulto_zunchado') : null;
        if (trim($request->input('bulto_zunchado')) != '') {
            $datos_insertados['bulto_zunchado'] = [
                'texto' => 'Bulto Zunchado',
                'valor' => ['descripcion' => $request->input('bulto_zunchado')]
            ];
        }

        $ot->formato_etiqueta = (trim($request->input('formato_etiqueta')) != '') ? $request->input('formato_etiqueta') : null;
        if (trim($request->input('formato_etiqueta')) != '') {
            $datos_insertados['formato_etiqueta'] = [
                'texto' => 'Formato Etiqueta',
                'valor' => ['descripcion' => $request->input('formato_etiqueta')]
            ];
        }

        $ot->etiquetas_pallet = (trim($request->input('etiquetas_pallet')) != '') ? $request->input('etiquetas_pallet') : null;
        if (trim($request->input('etiquetas_pallet')) != '') {
            $datos_insertados['etiquetas_pallet'] = [
                'texto' => 'N° Etiquetas Pallet',
                'valor' => ['descripcion' => $request->input('etiquetas_pallet')]
            ];
        }

        $ot->termocontraible = (trim($request->input('termocontraible')) != '') ? $request->input('termocontraible') : null;
        if (trim($request->input('termocontraible')) != '') {
            $datos_insertados['termocontraible'] = [
                'texto' => 'N° Termocontraible',
                'valor' => ['descripcion' => $request->input('termocontraible')]
            ];
        }

        if (Auth()->user()->isVendedorExterno()) {
            $ot->ot_vendedor_externo = 1;
        }

        $ot->save();

        // Si el usuario que crea es un desarrollador selecciona un vendedor que asignaremos de lo contrario asignamos al vendedor que esta creando la solicitud
        $user_id = (trim($request->input('vendedor_id')) != '') ? $request->input('vendedor_id') : auth()->user()->id;
        $user = User::find($user_id);

        $asignacion = new UserWorkOrder();
        $asignacion->work_order_id = $ot->id;
        $asignacion->user_id = $user->id;
        $asignacion->area_id = $user->role->area->id;
        $asignacion->tiempo_inicial = 0;
        $asignacion->save();

        $gestion = new Management();
        // $gestion->titulo = request('titulo');
        $gestion->observacion = "Creación de Órden de Trabajo";
        $gestion->management_type_id = 1;
        $gestion->user_id = $user->id;
        $gestion->work_order_id = $ot->id;
        $gestion->work_space_id =  $ot->current_area_id;
        $gestion->duracion_segundos = 0;
        $gestion->state_id = 1;
        $gestion->save();

        // Si el usuario creador es del area de diseño estructural
        // Enviamos directamente la Ot a desarrollo y se la asignamos al usuario  le asignamos la ot
        if (auth()->user()->role->area->id == 2) {
            $gestion = new Management();
            // $gestion->titulo = request('titulo');
            $gestion->observacion = "Envio a " . auth()->user()->role->area->nombre;
            $gestion->management_type_id = 1;
            $gestion->user_id = $user->id;
            $gestion->work_order_id = $ot->id;
            $gestion->work_space_id =  $ot->current_area_id;
            $gestion->duracion_segundos = 0;
            $gestion->state_id = 2;
            $gestion->save();

            $asignacion = new UserWorkOrder();
            $asignacion->work_order_id = $ot->id;
            $asignacion->user_id = auth()->user()->id;
            $asignacion->area_id = 2;
            $asignacion->tiempo_inicial = 0;
            $asignacion->save();

            $ot->current_area_id = 2;
            $ot->ultimo_cambio_area = Carbon::now();
            $ot->save();
        }

        if (request("muestra_id")) {
            // si viene muestra id es un arrego con todas las muestras q debemos relacionar a dicha ot
            $cadAux = ($ot->cad_id && !$ot->cad) ? $ot->cad_asignado->cad : $ot->cad;
            Muestra::whereIn('id', json_decode(request("muestra_id")))->update(['work_order_id' => $ot->id, 'carton_id' => $ot->carton_id, 'cad_id' => $ot->cad_id, 'cad' => $cadAux]);
        }

        if (count($datos_insertados) > 0) { //Verificamos si se cambio algun valor para guardar

            //Se guarda registro en la tabla de bitacora
            $bitacora = new BitacoraWorkOrder();
            $user_auth = Auth()->user();
            $bitacora->observacion = "Insercion de datos de OT";
            $bitacora->operacion = 'Insercion'; //Tipo modificacion
            $bitacora->work_order_id = $ot->id;
            $bitacora->user_id = $user_auth->id;
            $user_data = array(
                'nombre' => $user_auth->nombre,
                'apellido' => $user_auth->apellido,
                'rut' => $user_auth->rut,
                'role_id' => $user_auth->role_id,
            );
            $bitacora->user_data = json_encode($user_data, JSON_UNESCAPED_UNICODE); //Se agrega JSON_UNESCAPED_UNICODE para mantener el formato de las palabras con acentos
            $bitacora->datos_modificados = json_encode($datos_insertados, JSON_UNESCAPED_UNICODE);
            $bitacora->ip_solicitud = \Request::getClientIp(true);
            $bitacora->url = url()->full();
            $bitacora->save();
            //se guardan los nombre de los campos que tiene la OT
            //BitacoraCamposModificados::insert($campos);

        }

        if (request("aplicar_mckee_value") == '1') {
            $datos_mckee = array();
            if (request("carton_id_mckee_value") != '') {
                $carton = Carton::where('active', 1)->find(request("carton_id_mckee_value"));

                $datos_mckee['carton'] = [
                    'texto' => 'Carton',
                    'valor' => ['descripcion' =>  $carton->codigo]
                ];
            }
            if (request("largo_mckee_value") != '') {
                $datos_mckee['largo'] = [
                    'texto' => 'Largo',
                    'valor' => ['descripcion' => request("largo_mckee_value")]
                ];
            }
            if (request("ancho_mckee_value") != '') {
                $datos_mckee['ancho'] = [
                    'texto' => 'Ancho',
                    'valor' => ['descripcion' => request("ancho_mckee_value")]
                ];
            }
            if (request("alto_mckee_value") != '') {
                $datos_mckee['alto'] = [
                    'texto' => 'Alto',
                    'valor' => ['descripcion' => request("alto_mckee_value")]
                ];
            }
            if (request("perimetro_mckee_value") != '') {
                $datos_mckee['perimetro'] = [
                    'texto' => 'Perimetro Persistente',
                    'valor' => ['descripcion' => request("perimetro_mckee_value")]
                ];
            }
            if (request("espesor_mckee_value") != '') {
                $datos_mckee['espesor'] = [
                    'texto' => 'Espesor',
                    'valor' => ['descripcion' => request("espesor_mckee_value")]
                ];
            }
            if (request("ect_mckee_value") != '') {
                $datos_mckee['ect'] = [
                    'texto' => 'Ect',
                    'valor' => ['descripcion' => request("ect_mckee_value")]
                ];
            }
            if (request("bct_lib_mckee_value") != '') {
                $datos_mckee['bct_lb'] = [
                    'texto' => 'Bct_lb',
                    'valor' => ['descripcion' => request("bct_lib_mckee_value")]
                ];
            }
            if (request("bct_kilos_mckee_value") != '') {
                // DATOS COMERCIALES
                $datos_mckee['bct_kilos'] = [
                    'texto' => 'Bct_kilos',
                    'valor' => ['descripcion' => request("bct_kilos_mckee_value")]
                ];
            }
            if (request("fecha_mckee_value") != '') {
                // DATOS COMERCIALES
                $datos_mckee['fecha'] = [
                    'texto' => 'Fecha',
                    'valor' => ['descripcion' => request("fecha_mckee_value")]
                ];
            }

            if (count($datos_mckee) > 0) { //Verificamos si se cambio algun valor para guardar

                //Se guarda registro en la tabla de bitacora
                $bitacora = new BitacoraWorkOrder();
                $user_auth = Auth()->user();
                $bitacora->observacion = "Aplicacion Formula Mckee";
                $bitacora->operacion = 'Mckee'; //Tipo modificacion
                $bitacora->work_order_id = $ot->id;
                $bitacora->user_id = $user_auth->id;
                $user_data = array(
                    'nombre' => $user_auth->nombre,
                    'apellido' => $user_auth->apellido,
                    'rut' => $user_auth->rut,
                    'role_id' => $user_auth->role_id,
                );
                $bitacora->user_data = json_encode($user_data, JSON_UNESCAPED_UNICODE); //Se agrega JSON_UNESCAPED_UNICODE para mantener el formato de las palabras con acentos
                $bitacora->datos_modificados = json_encode($datos_mckee, JSON_UNESCAPED_UNICODE);
                $bitacora->ip_solicitud = \Request::getClientIp(true);
                $bitacora->url = url()->full();
                $bitacora->save();
                //se guardan los nombre de los campos que tiene la OT
                //BitacoraCamposModificados::insert($campos);

            }
        }

        return redirect()->route('gestionarOt', $ot->id)->with('success', 'Órden de Trabajo creada correctamente.');
    }

    public function edit($id)
    {
        if (!empty($_GET['validacion_campos'])) {
            $validacion_campos = $_GET['validacion_campos'];
        } else {
            $validacion_campos = 0;
        }

        $ot = WorkOrder::with('subsubhierarchy.subhierarchy.hierarchy')->find($id);
        // dd($ot->area_producto_calculo);
        if (Auth()->user()->isVendedorExterno()) {
            $clients = Client::where('active', 1)->where('id', 8)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->pluck('nombre', 'id')->toArray();
        } else {
            $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->pluck('nombre', 'id')->toArray();
        }
        $cads = Cad::where('active', 1)->pluck('cad', 'id')->toArray();
        $canals = Canal::all()->pluck('nombre', 'id')->toArray();
        $cartons = Carton::where('active', 1)->pluck('codigo', 'id')->toArray();
        $cartons_muestra = Carton::where('carton_muestra', 1)->where('active', 1)->pluck('codigo', 'id')->toArray();
        $styles = Style::where('active', 1)->pluck('glosa', 'id')->toArray();
        $colors = Color::select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"), 'id')->where('active', 1)->pluck('descripcion', 'id')->toArray();
        $colors_barniz = Color::select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"), 'id')->where('active', 1)->whereIn('codigo', ['1350710', '1350711'])->pluck('descripcion', 'id')->toArray();
        $envases = Envase::where('active', 1)->pluck('descripcion', 'id')->toArray();

        // $hierarchies = Hierarchy::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $hierarchyId = optional(optional(optional($ot->subsubhierarchy)->subhierarchy)->hierarchy)->id;
        $hierarchies = Hierarchy::where('active', 1)->when($hierarchyId, function($q) use ($hierarchyId) {
            return $q->orWhere('id', $hierarchyId);
        })->pluck('descripcion', 'id')->toArray();

        $productTypes = ProductType::where('active', 1)->pluck('descripcion', 'id')->toArray();

        $materials2 = Material::where('active', 1)->where('cad_id', '!=', 0)->where('status', 1)->pluck('codigo', 'id')->toArray();
        // $materials2 = Material::whereIn('active', [1])->where('cad_id', '!=', 0)->where('status',1)->pluck('codigo', 'id')->toArray();

        // Agregar el reference_id aunque no cumpla condiciones
        $materialsReferencia = [];

        if (!is_null($ot->reference_id) && !array_key_exists($ot->reference_id, $materials2)) {
            $materialsReferencia = Material::where('id', $ot->reference_id)
                ->pluck('codigo', 'id')
                ->toArray();
        }

        // Combinar ambos
        $materials = $materialsReferencia + $materials2;

        //$procesos = Process::where('active', 1)->orderBy("descripcion")->pluck('descripcion', 'id')->toArray();
        $procesos = Process::where('active', 1)->where('type', 'EV')->orderBy('orden', 'ASC')->pluck('descripcion', 'id')->toArray();


        $trazabilidad = Trazabilidad::where('status', 1)->pluck('descripcion', 'id')->toArray();


        $armados = Armado::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $sentidos_armado = [1 => "No aplica", 2 => "Ancho a la Derecha", 3 => "Ancho a la Izquierda", 4 => "Largo a la Izquierda",  5 => "Largo a la Derecha"];
        $subhierarchies = [];
        $subsubhierarchies = [];
        $tipos_solicitud = [1 => "Desarrollo Completo", 4 => "Cotiza sin CAD", 2 => "Cotiza con CAD", 3 => "Muestra con CAD",  5 => "Arte con Material", 6 => "Otras Solicitudes Desarrollo"];
        $org_ventas = [1 => "Nacional", 2 => "Exportación"];
        $paisReferencia = Pais::where('active', 1)->pluck('name', 'id')->toArray();
        $plantaObjetivo = Planta::pluck('nombre', 'id')->toArray();
        $palletTypes = PalletType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $recubrimiento_type = RecubrimientoType::pluck('descripcion', 'codigo')->toArray();
        $reference_type = [0 => "No", 1 => "Si"]; //Se deja el arreglo para poder mostrar el No y el SI a las OT antiguas
        $reference_type = array_merge($reference_type, ReferenceType::where('active', 1)->pluck('descripcion', 'codigo')->toArray());
        $fsc = [0 => "No", 1 => "Si"]; //Se deja el arreglo para poder mostrar el No y el SI a las OT antiguas
        $fsc = array_merge($fsc, Fsc::where('active', 1)->pluck('descripcion', 'codigo')->toArray());
        $designTypes = DesignType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $secuenciaOperacional = SecuenciaOperacional::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $maquila_servicios = MaquilaServicio::where('active', 1)->pluck('servicio', 'id')->toArray();

        $coverageExternal = CoverageExternal::where('status', 1)->pluck('descripcion', 'id')->toArray();
        $coverageInternal = CoverageInternal::where('status', 1)->pluck('descripcion', 'id')->toArray();
        $tipoCinta = TipoCinta::where('active', 1)->pluck('descripcion', 'id')->toArray();

        //Nuevos Campos Seccion Datas para el Desarrollo
        $classSubstancePacked = ClassSubstancePacked::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $expectedUse = ExpectedUse::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $foodType = FoodType::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $productTypeDeveloping = ProductTypeDeveloping::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $recycledUse = RecycledUse::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $targetMarket = TargetMarket::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
        $transportationWay = TransportationWay::where('deleted', 0)->pluck('descripcion', 'id')->toArray();

        $check_mckee = false;

        $mckee_ot = BitacoraWorkOrder::where('work_order_id', $ot->id)->where('operacion', 'Mckee')->get()->count();
        if ($mckee_ot) {
            $check_mckee = true;
        }
        $palletQa = PalletQa::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $palletTagFormat = PalletTagFormat::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $indicaciones_especiales = IndicacionEspecial::where('client_id', $ot->client_id)->where('deleted', 0)->get();
        $matrices = Matriz::where('active', 1)->pluck('material', 'id')->toArray();

        return view('work-orders-old.edit', compact(
            'ot',
            'clients',
            'cads',
            'canals',
            'cartons_muestra',
            'cartons',
            'styles',
            'colors',
            'envases',
            'materials',
            'materials2',
            'armados',
            'sentidos_armado',
            'procesos',
            'productTypes',
            'hierarchies',
            'subhierarchies',
            'subsubhierarchies',
            'tipos_solicitud',
            'org_ventas',
            'paisReferencia',
            'plantaObjetivo',
            'palletTypes',
            'reference_type',
            'fsc',
            'recubrimiento_type',
            'designTypes',
            'maquila_servicios',
            'validacion_campos',
            'coverageExternal',
            'coverageInternal',
            'classSubstancePacked',
            'expectedUse',
            'foodType',
            'productTypeDeveloping',
            'recycledUse',
            'targetMarket',
            'transportationWay',
            'colors_barniz',
            'check_mckee',
            'palletQa',
            'palletTagFormat',
            'indicaciones_especiales',
            'secuenciaOperacional',
            'tipoCinta',
            'matrices',
            'trazabilidad'
        ));
    }

    public function update(Request $request, $id)
    {
        // dd(request()->all());
        $ot =  WorkOrder::find($id);

        //Buscamos primero la informacion correcta del CAD
        if ($request->input('cad_id') != '') {
            $search_cad = Cad::where('id', $request->input('cad_id'))->select('cad')->pluck('cad')->first();
        } else {
            $search_cad = '';
        }

        //Buscamos primero todos los campos de la OT de la tabla
        $campos_modificados = BitacoraCamposModificados::all()->pluck('descripcion')->toArray();

        // Busqueda de datos
        $canals_antiguo = Canal::where('id', $ot->canal_id)->select('nombre')->pluck('nombre')->first();
        $canals_nuevo = Canal::where('id', $request->input('canal_id'))->select('nombre')->pluck('nombre')->first();

        $subhierarchy_antiguo = Subsubhierarchy::where('id', $ot->subsubhierarchy_id)->select('descripcion')->pluck('descripcion')->first();
        $subhierarchy_nuevo = Subsubhierarchy::where('id', $request->input('subsubhierarchy_id'))->select('descripcion')->pluck('descripcion')->first();

        $reference_type_antiguo = ReferenceType::where('codigo', $ot->reference_type)->select('descripcion')->pluck('descripcion')->first();
        $reference_type_nuevo = ReferenceType::where('codigo', $request->input('reference_type'))->select('descripcion')->pluck('descripcion')->first();

        $material_antiguo = Material::where('id', $ot->reference_id)->select('codigo')->pluck('codigo')->first();
        $material_nuevo = Material::where('id', $request->input('reference_id'))->select('codigo')->pluck('codigo')->first();

        //  6=>'Offest' -- 12=>'Impresión'
        $indicador_valor = [1 => 'RRP', 2 => 'E-Commerce', 3 => 'Esquineros', 4 => 'Geometría', 5 => 'Participación nuevo Mercado', 6 => '', 7 => 'Innovación', 8 => 'Sustentabilidad', 9 => 'Automatización', 10 => 'No Aplica', 11 => 'Ahorro', 12 => ''];
        $indicador_nuevo = $request->input('indicador_facturacion') && array_key_exists($request->input('indicador_facturacion'), $indicador_valor) ? $indicador_valor[$request->input('indicador_facturacion')] : null;
        $indicador_antiguo = $ot->indicador_facturacion && array_key_exists($ot->indicador_facturacion, $indicador_valor) ? $indicador_valor[$ot->indicador_facturacion] : null;

        $cad_antiguo = Cad::where('id', $ot->cad_id)->select('cad')->pluck('cad')->first();
        $cad_nuevo = Cad::where('id', $request->input('cad_id'))->select('cad')->pluck('cad')->first();

        $product_type_antiguo = ProductType::where('id', $ot->product_type_id)->select('descripcion')->pluck('descripcion')->first();
        $product_type_nuevo = ProductType::where('id', $request->input('product_type_id'))->select('descripcion')->pluck('descripcion')->first();

        $carton_antiguo = Carton::where('id', $ot->carton_id)->select('codigo')->pluck('codigo')->first();
        $carton_nuevo = Carton::where('id', $request->input('carton_id'))->select('codigo')->pluck('codigo')->first();

        $fsc_antiguo = Fsc::where('id', $ot->fsc)->select('descripcion')->pluck('descripcion')->first();
        $fsc_nuevo = Fsc::where('id', $request->input('fsc'))->select('descripcion')->pluck('descripcion')->first();

        $pais_antiguo = Pais::where('id', $ot->pais_id)->select('name')->pluck('name')->first();
        $pais_nuevo = Pais::where('id', $request->input('pais_id'))->select('name')->pluck('name')->first();

        $planta_antiguo = Planta::where('id', $ot->planta_id)->select('nombre')->pluck('nombre')->first();
        $planta_nuevo = Planta::where('id', $request->input('planta_id'))->select('nombre')->pluck('nombre')->first();

        $tamano_pallet_antiguo = PalletType::where('id', $ot->tamano_pallet_type_id)->select('descripcion')->pluck('descripcion')->first();
        $tamano_pallet_nuevo = PalletType::where('id', $request->input('tamano_pallet_type_id'))->select('descripcion')->pluck('descripcion')->first();

        $style_antiguo = Style::where('id', $ot->style_id)->select('glosa')->pluck('glosa')->first();
        $style_nuevo = Style::where('id', $request->input('style_id'))->select('glosa')->pluck('glosa')->first();

        $matriz_antiguo = Matriz::where('id', $ot->matriz_id)->select('material')->pluck('material')->first();
        $matriz_nuevo = Matriz::where('id', $request->input('matriz_id'))->select('material')->pluck('material')->first();
        $recubrimiento_antiguo = RecubrimientoType::where('codigo', $ot->recubrimiento)->select('descripcion')->pluck('descripcion')->first();
        $recubrimiento_nuevo = RecubrimientoType::where('codigo', $request->input('recubrimiento'))->select('descripcion')->pluck('descripcion')->first();

        // $impresion_valor = [1 => 'Offset', 2 => 'Flexografía',3 => 'Flexografía Alta Gráfica', 4 => 'Flexografía Tiro y Retiro',  5 => 'Sin Impresión', 6 => 'Sin Impresión (Sólo OF)', 7 => 'Sin Impresión (Trazabilidad Completa)'];
        $impresion_valor = [1 => 'Offset', 2 => 'Flexografía', 3 => 'Flexografía Alta Gráfica', 4 => 'Flexografía Tiro y Retiro',  5 => 'Sin Impresión'];
        $impresion_nuevo = $request->input('impresion') && array_key_exists($request->input('impresion'), $impresion_valor) ? $impresion_valor[$request->input('impresion')] : null;
        $impresion_antiguo = $ot->impresion && array_key_exists($ot->impresion, $impresion_valor) ? $impresion_valor[$ot->impresion] : null;

        $trazabilidad_antiguo = Trazabilidad::where('id', $ot->trazabilidad)->select('descripcion')->pluck('descripcion')->first();
        $trazabilidad_nuevo = Trazabilidad::where('id', $request->input('trazabilidad'))->select('descripcion')->pluck('descripcion')->first();


        $design_type_antiguo = DesignType::where('id', $ot->design_type_id)->select('descripcion')->pluck('descripcion')->first();
        $design_type_nuevo = DesignType::where('id', $request->input('design_type_id'))->select('descripcion')->pluck('descripcion')->first();

        $coverage_internal_antiguo = CoverageInternal::where('id', $ot->coverage_internal_id)->select('descripcion')->pluck('descripcion')->first();
        $coverage_internal_nuevo = CoverageInternal::where('id', $request->input('coverage_internal_id'))->select('descripcion')->pluck('descripcion')->first();

        $coverage_external_antiguo = CoverageExternal::where('id', $ot->coverage_external_id)->select('descripcion')->pluck('descripcion')->first();
        $coverage_external_nuevo = CoverageExternal::where('id', $request->input('coverage_external_id'))->select('descripcion')->pluck('descripcion')->first();

        $color_1_antiguo = Color::where('id', $ot->color_1_id)->select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"))->pluck('descripcion')->first();
        $color_1_nuevo = Color::where('id', $request->input('color_1_id'))->select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"))->pluck('descripcion')->first();

        $color_2_antiguo = Color::where('id', $ot->color_2_id)->select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"))->pluck('descripcion')->first();
        $color_2_nuevo = Color::where('id', $request->input('color_2_id'))->select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"))->pluck('descripcion')->first();

        $color_3_antiguo = Color::where('id', $ot->color_3_id)->select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"))->pluck('descripcion')->first();
        $color_3_nuevo = Color::where('id', $request->input('color_3_id'))->select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"))->pluck('descripcion')->first();

        $color_4_antiguo = Color::where('id', $ot->color_4_id)->select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"))->pluck('descripcion')->first();
        $color_4_nuevo = Color::where('id', $request->input('color_4_id'))->select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"))->pluck('descripcion')->first();

        $color_5_antiguo = Color::where('id', $ot->color_5_id)->select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"))->pluck('descripcion')->first();
        $color_5_nuevo = Color::where('id', $request->input('color_5_id'))->select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"))->pluck('descripcion')->first();

        $color_6_antiguo = Color::where('id', $ot->color_6_id)->select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"))->pluck('descripcion')->first();
        $color_6_nuevo = Color::where('id', $request->input('color_6_id'))->select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"))->pluck('descripcion')->first();

        $color_interno_antiguo = Color::where('id', $ot->color_interno)->select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"))->pluck('descripcion')->first();
        $color_interno_nuevo = Color::where('id', $request->input('color_1_id'))->select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"))->pluck('descripcion')->first();

        $pegado_terminacion_valor = [0 => 'No Aplica', 2 => 'Pegado Interno', 3 => 'Pegado Externo', 4 => 'Pegado 3 Puntos', 5 => 'Pegado 4 Puntos'];
        $pegado_terminacion_nuevo = $request->input('pegado_terminacion') && array_key_exists($request->input('pegado_terminacion'), $pegado_terminacion_valor) ? $pegado_terminacion_valor[$request->input('pegado_terminacion')] : null;
        $pegado_terminacion_antiguo = $ot->pegado_terminacion && array_key_exists($ot->pegado_terminacion, $pegado_terminacion_valor) ? $pegado_terminacion_valor[$ot->pegado_terminacion] : null;

        $proceso_antiguo = Process::where('id', $ot->process_id)->select('descripcion')->pluck('descripcion')->first();
        $proceso_nuevo = Process::where('id', $request->input('process_id'))->select('descripcion')->pluck('descripcion')->first();

        $armado_antiguo = Armado::where('id', $ot->armado_id)->select('descripcion')->pluck('descripcion')->first();
        $armado_nuevo = Armado::where('id', $request->input('armado_id'))->select('descripcion')->pluck('descripcion')->first();

        $sentido_armado_valor = [1 => "No aplica", 2 => "Ancho a la Derecha", 3 => "Ancho a la Izquierda", 4 => "Largo a la Izquierda",  5 => "Largo a la Derecha"];
        $sentido_armado_nuevo = $request->input('sentido_armado') && array_key_exists($request->input('sentido_armado'), $sentido_armado_valor) ? $sentido_armado_valor[$request->input('sentido_armado')] : null;
        $sentido_armado_antiguo = $ot->sentido_armado && array_key_exists($ot->sentido_armado, $sentido_armado_valor) ? $sentido_armado_valor[$ot->sentido_armado] : null;

        $maquila_servicio_antiguo = MaquilaServicio::where('id', $ot->maquila_servicio_id)->select('servicio')->pluck('servicio')->first();
        $maquila_servicio_nuevo = MaquilaServicio::where('id', $request->input('maquila_servicio_id'))->select('servicio')->pluck('servicio')->first();

        $envase_antiguo = Envase::where('id', $ot->envase_id)->select('descripcion')->pluck('descripcion')->first();
        $envase_nuevo = Envase::where('id', $request->input('envase_id'))->select('descripcion')->pluck('descripcion')->first();

        $classSubstancePacked_antiguo = ClassSubstancePacked::where('id', $ot->class_substance_packed_id)->select('descripcion')->pluck('descripcion')->first();
        $classSubstancePacked_nuevo = ClassSubstancePacked::where('id', $request->input('class_substance_packed_id'))->select('descripcion')->pluck('descripcion')->first();

        $expectedUse_antiguo = ExpectedUse::where('id', $ot->expected_use_id)->select('descripcion')->pluck('descripcion')->first();
        $expectedUse_nuevo = ExpectedUse::where('id', $request->input('expected_use_id'))->select('descripcion')->pluck('descripcion')->first();

        $foodType_antiguo = FoodType::where('id', $ot->food_type_id)->select('descripcion')->pluck('descripcion')->first();
        $foodType_nuevo = FoodType::where('id', $request->input('food_type_id'))->select('descripcion')->pluck('descripcion')->first();

        $productTypeDeveloping_antiguo = ProductTypeDeveloping::where('id', $ot->product_type_developing_id)->select('descripcion')->pluck('descripcion')->first();
        $productTypeDeveloping_nuevo = ProductTypeDeveloping::where('id', $request->input('product_type_developing_id'))->select('descripcion')->pluck('descripcion')->first();

        $recycledUse_antiguo = RecycledUse::where('id', $ot->recycled_use_id)->select('descripcion')->pluck('descripcion')->first();
        $recycledUse_nuevo = RecycledUse::where('id', $request->input('recycled_use_id'))->select('descripcion')->pluck('descripcion')->first();

        $targetMarket_antiguo = TargetMarket::where('id', $ot->target_market_id)->select('descripcion')->pluck('descripcion')->first();
        $targetMarket_nuevo = TargetMarket::where('id', $request->input('target_market_id'))->select('descripcion')->pluck('descripcion')->first();

        $transportationWay_antiguo = TransportationWay::where('id', $ot->transportation_way_id)->select('descripcion')->pluck('descripcion')->first();
        $transportationWay_nuevo = TransportationWay::where('id', $request->input('transportation_way_id'))->select('descripcion')->pluck('descripcion')->first();


        $so_planta_original_antiguo = SecuenciaOperacional::where('id', $ot->so_planta_original)->select('descripcion')->pluck('descripcion')->first();
        $so_planta_original_nuevo = SecuenciaOperacional::where('id', $request->input('sec_operacional_principal'))->select('descripcion')->pluck('descripcion')->first();
        $so_planta_alt1_antiguo = SecuenciaOperacional::where('id', $ot->so_planta_alt1)->select('descripcion')->pluck('descripcion')->first();
        $so_planta_alt1_nuevo = SecuenciaOperacional::where('id', $request->input('sec_operacional_1'))->select('descripcion')->pluck('descripcion')->first();
        $so_planta_alt2_antiguo = SecuenciaOperacional::where('id', $ot->so_planta_alt1)->select('descripcion')->pluck('descripcion')->first();
        $so_planta_alt2_nuevo = SecuenciaOperacional::where('id', $request->input('sec_operacional_2'))->select('descripcion')->pluck('descripcion')->first();
        // ------- INICIO ------- Compara datos para la bitacora ( antes de que se guarden )
        $campos = array();
        $datos_modificados = array();

        // -------------------------------------------------------------------------- DATOS COMERCIALES -------------------------------------------------------------
        if ($ot->descripcion != $request->input('descripcion')) {
            $datos_modificados['descripcion'] = [
                'texto' => 'Descripción',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->descripcion],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('descripcion')]
            ];
        }
        if (!in_array('Descripción', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Descripción'];
        }

        if ($ot->dato_sub_cliente != $request->input('dato_sub_cliente')) {
            $datos_modificados['dato_sub_cliente'] = [
                'texto' => 'Datos Cliente Edipac',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->dato_sub_cliente],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('dato_sub_cliente')]
            ];
        }
        if (!in_array('Datos Cliente Edipac', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Datos Cliente Edipac'];
        }

        if ($ot->codigo_producto != $request->input('codigo_producto')) {
            $datos_modificados['codigo_producto'] = [
                'texto' => 'Código producto',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->codigo_producto],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('codigo_producto')]
            ];
        }
        if (!in_array('Código producto', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Código producto'];
        }

        if ($ot->nombre_contacto != $request->input('nombre_contacto')) {
            $datos_modificados['nombre_contacto'] = [
                'texto' => 'Nombre contacto',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->nombre_contacto],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('nombre_contacto')]
            ];
        }
        if (!in_array('Nombre contacto', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Nombre contacto'];
        }

        if ($ot->email_contacto != $request->input('email_contacto')) {
            $datos_modificados['email_contacto'] = [
                'texto' => 'Email contacto',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->email_contacto],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('email_contacto')]
            ];
        }
        if (!in_array('Email contacto', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Email contacto'];
        }

        if ($ot->telefono_contacto != str_replace(' ', '', $request->input('telefono_contacto'))) {
            $datos_modificados['telefono_contacto'] = [
                'texto' => 'Teléfono contacto',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->telefono_contacto],
                'nuevo_valor' => ['id' => null, 'descripcion' => str_replace(' ', '', $request->input('telefono_contacto'))]
            ];
        }
        if (!in_array('Teléfono contacto', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Teléfono contacto'];
        }

        if ($ot->volumen_venta_anual != str_replace('.', '', $request->input('volumen_venta_anual'))) {
            $datos_modificados['volumen_venta_anual'] = [
                'texto' => 'VOL VTA ANUAL',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->volumen_venta_anual],
                'nuevo_valor' => ['id' => null, 'descripcion' => str_replace('.', '', $request->input('volumen_venta_anual'))]
            ];
        }
        if (!in_array('VOL VTA ANUAL', $campos_modificados)) {
            $campos[] = ['descripcion' => 'VOL VTA ANUAL'];
        }

        if ($ot->usd != str_replace('.', '', $request->input('usd'))) {
            $datos_modificados['usd'] = [
                'texto' => 'USD',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->usd],
                'nuevo_valor' => ['id' => null, 'descripcion' => str_replace('.', '', $request->input('usd'))]
            ];
        }
        if (!in_array('USD', $campos_modificados)) {
            $campos[] = ['descripcion' => 'USD'];
        }

        if ((string)$ot->org_venta_id !== (string)$request->input('org_venta_id')) {
            $datos_modificados['org_venta_id'] = [
                'texto' => 'ORG. VENTA',
                'antiguo_valor' => ['id' => $ot->org_venta_id, 'descripcion' => $ot->org_venta_id == 1 ? 'Nacional' : ($ot->org_venta_id == 2 ? 'Exportación' : null)],
                'nuevo_valor' => ['id' => $request->input('org_venta_id'), 'descripcion' => $request->input('org_venta_id') == 1 ? 'Nacional' : ($request->input('org_venta_id') == 2 ? 'Exportación' : null)]
            ];
        }
        if (!in_array('ORG. VENTA', $campos_modificados)) {
            $campos[] = ['descripcion' => 'ORG. VENTA'];
        }

        if ($ot->canal_id != $request->input('canal_id')) {
            $datos_modificados['canal_id'] = [
                'texto' => 'Canal',
                'antiguo_valor' => ['id' => $ot->canal_id, 'descripcion' => $canals_antiguo],
                'nuevo_valor' => ['id' => $request->input('canal_id'), 'descripcion' => $canals_nuevo]
            ];
        }
        if (!in_array('Canal', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Canal'];
        }

        if ((string)$ot->oc !== (string)$request->input('oc')) {
            $datos_modificados['oc'] = [
                'texto' => 'OC',
                'antiguo_valor' => ['id' => $ot->oc, 'descripcion' => $ot->oc == 1 ? 'Si' : ($ot->oc == '0' ? 'No' : null)],
                'nuevo_valor' => ['id' => $request->input('oc'), 'descripcion' => $request->input('oc') == 1 ? 'Si' : ($request->input('oc') == '0' ? 'No' :  null)]
            ];
        }
        if (!in_array('OC', $campos_modificados)) {
            $campos[] = ['descripcion' => 'OC'];
        }

        if ($ot->subsubhierarchy_id != $request->input('subsubhierarchy_id')) {
            $datos_modificados['subsubhierarchy_id'] = [
                'texto' => 'Jerarquía 3',
                'antiguo_valor' => ['id' => $ot->subsubhierarchy_id, 'descripcion' => $subhierarchy_antiguo],
                'nuevo_valor' => ['id' => $request->input('subsubhierarchy_id'), 'descripcion' => $subhierarchy_nuevo]
            ];
        }
        if (!in_array('Jerarquía 3', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Jerarquía 3'];
        }


        // ---------------------------------------------------------------------------- SOLICITA -------------------------------------------------------------
        $analisis = '';
        if (in_array('analisis', $request->input('checkboxes'))) {
            $analisis = 1;
        } else {
            $analisis = 0;
        }
        if ($ot->analisis != $analisis) {
            $datos_modificados['analisis'] = [
                'texto' => 'Análisis',
                'antiguo_valor' => ['id' => $ot->analisis, 'descripcion' => $ot->analisis == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $analisis, 'descripcion' => $analisis == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Análisis', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Análisis'];
        }

        $plano = '';
        if (in_array('plano', $request->input('checkboxes'))) {
            $plano = 1;
        } else {
            $plano = 0;
        }
        if ($ot->plano != $plano) {
            $datos_modificados['plano'] = [
                'texto' => 'Plano',
                'antiguo_valor' => ['id' => $ot->plano, 'descripcion' => $ot->plano == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $plano, 'descripcion' => $plano == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Plano', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Plano'];
        }

        $prueba_industrial = '';
        if (in_array('prueba_industrial', $request->input('checkboxes'))) {
            $prueba_industrial = 1;
        } else {
            $prueba_industrial = 0;
        }
        if ($ot->prueba_industrial != $prueba_industrial) {
            $datos_modificados['prueba_industrial'] = [
                'texto' => 'Prueba industrial',
                'antiguo_valor' => ['id' => $ot->prueba_industrial, 'descripcion' => $ot->prueba_industrial == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $prueba_industrial, 'descripcion' => $prueba_industrial == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Prueba industrial', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Prueba industrial'];
        }

        $datos_cotizar = '';
        if (in_array('datos_cotizar', $request->input('checkboxes'))) {
            $datos_cotizar = 1;
        } else {
            $datos_cotizar = 0;
        }
        if ($ot->datos_cotizar != $datos_cotizar) {
            $datos_modificados['datos_cotizar'] = [
                'texto' => 'Datos para Cotizar',
                'antiguo_valor' => ['id' => $ot->datos_cotizar, 'descripcion' => $ot->datos_cotizar == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $datos_cotizar, 'descripcion' => $datos_cotizar == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Datos para Cotizar', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Datos para Cotizar'];
        }

        $boceto = '';
        if (in_array('boceto', $request->input('checkboxes'))) {
            $boceto = 1;
        } else {
            $boceto = 0;
        }
        if ($ot->boceto != $boceto) {
            $datos_modificados['boceto'] = [
                'texto' => 'Boceto',
                'antiguo_valor' => ['id' => $ot->boceto, 'descripcion' => $ot->boceto == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $boceto, 'descripcion' => $boceto == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Boceto', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Boceto'];
        }

        $nuevo_material = '';
        if (in_array('nuevo_material', $request->input('checkboxes'))) {
            $nuevo_material = 1;
        } else {
            $nuevo_material = 0;
        }
        if ($ot->nuevo_material != $nuevo_material) {
            $datos_modificados['nuevo_material'] = [
                'texto' => 'Nuevo Material',
                'antiguo_valor' => ['id' => $ot->nuevo_material, 'descripcion' => $ot->nuevo_material == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $nuevo_material, 'descripcion' => $nuevo_material == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Nuevo Material', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Nuevo Material'];
        }

        $muestra = '';
        if (in_array('muestra', $request->input('checkboxes'))) {
            $muestra = 1;
        } else {
            $muestra = 0;
        }
        if ($ot->muestra != $muestra) {
            $datos_modificados['muestra'] = [
                'texto' => 'Muestra',
                'antiguo_valor' => ['id' => $ot->muestra, 'descripcion' => $ot->muestra == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $muestra, 'descripcion' => $muestra == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Muestra', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Muestra'];
        }

        if ($ot->numero_muestras != $request->input('numero_muestras')) {
            $datos_modificados['numero_muestras'] = [
                'texto' => 'N° Muestras',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->numero_muestras],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('numero_muestras')]
            ];
        }
        if (!in_array('N° Muestras', $campos_modificados)) {
            $campos[] = ['descripcion' => 'N° Muestras'];
        }

        // ---------------------------------------------------------------------------- REFERENCIA -------------------------------------------------------------
        if ($ot->reference_type != $request->input('reference_type')) {
            $datos_modificados['reference_type'] = [
                'texto' => 'Tipo referencia',
                'antiguo_valor' => ['id' => $ot->reference_type, 'descripcion' => $reference_type_antiguo],
                'nuevo_valor' => ['id' => $request->input('reference_type'), 'descripcion' => $reference_type_nuevo]
            ];
        }
        if (!in_array('Tipo referencia', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Tipo referencia'];
        }

        if ($ot->reference_id != $request->input('reference_id')) {
            $datos_modificados['reference_id'] = [
                'texto' => 'Referencia',
                'antiguo_valor' => ['id' => $ot->reference_id, 'descripcion' => $material_antiguo],
                'nuevo_valor' => ['id' => $request->input('reference_id'), 'descripcion' => $material_nuevo]
            ];
        }
        if (!in_array('Referencia', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Referencia'];
        }

        if ((string)$ot->bloqueo_referencia !== (string)$request->input('bloqueo_referencia')) {
            $datos_modificados['bloqueo_referencia'] = [
                'texto' => 'Bloqueo referencia',
                'antiguo_valor' => ['id' => $ot->bloqueo_referencia, 'descripcion' => $ot->bloqueo_referencia == 1 ? 'Si' : ($ot->bloqueo_referencia == '0' ? 'No' : null)],
                'nuevo_valor' => ['id' => $request->input('bloqueo_referencia'), 'descripcion' => $request->input('bloqueo_referencia') == 1 ? 'Si' : ($request->input('bloqueo_referencia') == '0' ? 'No' :  null)]
            ];
        }
        if (!in_array('Bloqueo referencia', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Bloqueo referencia'];
        }

        if ($ot->indicador_facturacion != $request->input('indicador_facturacion')) {
            $datos_modificados['indicador_facturacion'] = [
                'texto' => 'Indicador facturación D. Estructural',
                'antiguo_valor' => ['id' => $ot->indicador_facturacion, 'descripcion' => $indicador_antiguo],
                'nuevo_valor' => ['id' => $request->input('indicador_facturacion'), 'descripcion' => $indicador_nuevo]
            ];
        }
        if (!in_array('Indicador facturación D. Estructural', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Indicador facturación D. Estructural'];
        }


        // ---------------------------------------------------------------------------- CARACTERISTICA -------------------------------------------------------------
        if ($ot->cad_id != $request->input('cad_id')) {
            $datos_modificados['cad_id'] = [
                'texto' => 'CAD',
                'antiguo_valor' => ['id' => $ot->cad_id, 'descripcion' => $cad_antiguo],
                'nuevo_valor' => ['id' => $request->input('cad_id'), 'descripcion' => $cad_nuevo]
            ];
        }
        if (!in_array('CAD', $campos_modificados)) {
            $campos[] = ['descripcion' => 'CAD'];
        }

        if ($ot->product_type_id != $request->input('product_type_id')) {
            $datos_modificados['product_type_id'] = [
                'texto' => 'TIPO ITEM',
                'antiguo_valor' => ['id' => $ot->product_type_id, 'descripcion' => $product_type_antiguo],
                'nuevo_valor' => ['id' => $request->input('product_type_id'), 'descripcion' => $product_type_nuevo]
            ];
        }
        if (!in_array('TIPO ITEM', $campos_modificados)) {
            $campos[] = ['descripcion' => 'TIPO ITEM'];
        }

        if ((string)$ot->items_set !== (string)$request->input('items_set')) {
            $datos_modificados['items_set'] = [
                'texto' => 'ITEMS DEL SET',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->items_set],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('items_set')]
            ];
        }
        if (!in_array('ITEMS DEL SET', $campos_modificados)) {
            $campos[] = ['descripcion' => 'ITEMS DEL SET'];
        }

        if ((string)$ot->veces_item !== (string)$request->input('veces_item')) {
            $datos_modificados['veces_item'] = [
                'texto' => 'VECES ITEM',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->veces_item],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('veces_item')]
            ];
        }
        if (!in_array('VECES ITEM', $campos_modificados)) {
            $campos[] = ['descripcion' => 'VECES ITEM'];
        }

        if ((string)$ot->carton_color !== (string)$request->input('carton_color')) {
            $datos_modificados['carton_color'] = [
                'texto' => 'Color cartón',
                'antiguo_valor' => ['id' => $ot->carton_color, 'descripcion' => $ot->carton_color == 1 ? "Café" : ($ot->carton_color == 2 ? "Blanco" : null)],
                'nuevo_valor' => ['id' => $request->input('carton_color'), 'descripcion' => $request->input('carton_color') == 1 ? "Café" : ($request->input('carton_color') == 2 ? "Blanco" :  null)]
            ];
        }
        if (!in_array('Color cartón', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Color cartón'];
        }

        if ($ot->carton_id != $request->input('carton_id')) {
            $datos_modificados['carton_id'] = [
                'texto' => 'Cartón',
                'antiguo_valor' => ['id' => $ot->carton_id, 'descripcion' => $carton_antiguo],
                'nuevo_valor' => ['id' => $request->input('carton_id'), 'descripcion' => $carton_nuevo]
            ];
        }
        if (!in_array('Cartón', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Cartón'];
        }

        if ((string)$ot->cinta !== (string)$request->input('cinta')) {
            $datos_modificados['cinta'] = [
                'texto' => 'Cinta',
                'antiguo_valor' => ['id' => $ot->cinta, 'descripcion' => $ot->cinta == 1 ? 'Si' : ($ot->cinta == '0' ? 'No' : null)],
                'nuevo_valor' => ['id' => $request->input('cinta'), 'descripcion' => $request->input('cinta') == 1 ? 'Si' : ($request->input('cinta') == '0' ? 'No' :  null)]
            ];
        }
        if (!in_array('Cinta', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Cinta'];
        }

        if ($ot->fsc != $request->input('fsc')) {
            $datos_modificados['fsc'] = [
                'texto' => 'FSC',
                'antiguo_valor' => ['id' => $ot->fsc, 'descripcion' => $fsc_antiguo],
                'nuevo_valor' => ['id' => $request->input('fsc'), 'descripcion' => $fsc_nuevo]
            ];
        }
        if (!in_array('FSC', $campos_modificados)) {
            $campos[] = ['descripcion' => 'FSC'];
        }

        if ($ot->pais_id != $request->input('pais_id')) {
            $datos_modificados['pais_id'] = [
                'texto' => 'País referencia',
                'antiguo_valor' => ['id' => $ot->pais_id, 'descripcion' => $pais_antiguo],
                'nuevo_valor' => ['id' => $request->input('pais_id'), 'descripcion' => $pais_nuevo]
            ];
        }
        if (!in_array('País referencia', $campos_modificados)) {
            $campos[] = ['descripcion' => 'País referencia'];
        }

        if ($ot->planta_id != $request->input('planta_id')) {
            $datos_modificados['planta_id'] = [
                'texto' => 'Planta objetivo',
                'antiguo_valor' => ['id' => $ot->planta_id, 'descripcion' => $planta_antiguo],
                'nuevo_valor' => ['id' => $request->input('planta_id'), 'descripcion' => $planta_nuevo]
            ];
        }
        if (!in_array('Planta objetivo', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Planta objetivo'];
        }

        if ((string)$ot->restriccion_pallet !== (string)$request->input('restriccion_pallet')) {
            $datos_modificados['restriccion_pallet'] = [
                'texto' => 'Restricción paletizado',
                'antiguo_valor' => ['id' => $ot->restriccion_pallet, 'descripcion' => $ot->restriccion_pallet == 1 ? 'Si' : ($ot->restriccion_pallet == '0' ? 'No' : null)],
                'nuevo_valor' => ['id' => $request->input('restriccion_pallet'), 'descripcion' => $request->input('restriccion_pallet') == 1 ? 'Si' : ($request->input('restriccion_pallet') == '0' ? 'No' :  null)]
            ];
        }
        if (!in_array('Restricción paletizado', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Restricción paletizado'];
        }

        if ($ot->tamano_pallet_type_id != $request->input('tamano_pallet_type_id')) {
            $datos_modificados['tamano_pallet_type_id'] = [
                'texto' => 'Tamaño Pallet',
                'antiguo_valor' => ['id' => $ot->tamano_pallet_type_id, 'descripcion' => $tamano_pallet_antiguo],
                'nuevo_valor' => ['id' => $request->input('tamano_pallet_type_id'), 'descripcion' => $tamano_pallet_nuevo]
            ];
        }
        if (!in_array('Tamaño Pallet', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Tamaño Pallet'];
        }

        if ((string)$ot->altura_pallet !== (string)$request->input('altura_pallet')) {
            $datos_modificados['altura_pallet'] = [
                'texto' => 'Altura Pallet',
                'antiguo_valor' => ['id' => $ot->altura_pallet, 'descripcion' => $ot->altura_pallet],
                'nuevo_valor' => ['id' => $request->input('altura_pallet'), 'descripcion' => $request->input('altura_pallet')]
            ];
        }
        if (!in_array('Altura Pallet', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Altura Pallet'];
        }

        if ((string)$ot->permite_sobresalir_carga !== (string)$request->input('permite_sobresalir_carga')) {
            $datos_modificados['permite_sobresalir_carga'] = [
                'texto' => 'Permite Sobresalir Carga',
                'antiguo_valor' => ['id' => $ot->permite_sobresalir_carga, 'descripcion' => $ot->permite_sobresalir_carga == 1 ? 'Si' : ($ot->permite_sobresalir_carga == '0' ? 'No' : null)],
                'nuevo_valor' => ['id' => $request->input('permite_sobresalir_carga'), 'descripcion' => $request->input('permite_sobresalir_carga') == 1 ? 'Si' : ($request->input('permite_sobresalir_carga') == '0' ? 'No' :  null)]
            ];
        }
        if (!in_array('Permite Sobresalir Carga', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Permite Sobresalir Carga'];
        }

        if ($ot->style_id != $request->input('style_id')) {
            $datos_modificados['style_id'] = [
                'texto' => 'Estilo',
                'antiguo_valor' => ['id' => $ot->style_id, 'descripcion' => $style_antiguo],
                'nuevo_valor' => ['id' => $request->input('style_id'), 'descripcion' => $style_nuevo]
            ];
        }
        if ($ot->matriz_id != $request->input('matriz_id')) {
            $datos_modificados['matriz_id'] = [
                'texto' => 'Matriz',
                'antiguo_valor' => ['id' => $ot->matriz_id, 'descripcion' => $matriz_antiguo],
                'nuevo_valor' => ['id' => $request->input('matriz_id'), 'descripcion' => $matriz_nuevo]
            ];
        }
        if (!in_array('Estilo', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Estilo'];
        }

        if ((string)$ot->largura_hm !== (string)$request->input('largura_hm')) {
            $datos_modificados['largura_hm'] = [
                'texto' => 'Largura HM',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->largura_hm],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('largura_hm')]
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

        if ($ot->recorte_adicional != str_replace(",", ".", str_replace('.', '', $request->input('recorte_adicional')))) {
            $datos_modificados['recorte_adicional'] = [
                'texto' => 'Recorte Adicional / Area Agujero (m2)',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->recorte_adicional],
                'nuevo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('recorte_adicional')))]
            ];
        }
        if (!in_array('Recorte Adicional / Area Agujero (m2)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Recorte Adicional / Area Agujero (m2)'];
        }

        if ($ot->longitud_pegado != $request->input('longitud_pegado')) {
            $datos_modificados['longitud_pegado'] = [
                'texto' => 'Longitud Pegado (mm)',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->longitud_pegado],
                'nuevo_valor' => ['id' => null, 'descripcion' => round($request->input('longitud_pegado'))]
            ];
        }
        if (!in_array('Longitud Pegado (mm)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Longitud Pegado (mm)'];
        }

        if ($ot->recubrimiento != $request->input('recubrimiento')) {
            $datos_modificados['recubrimiento'] = [
                'texto' => 'Recubrimiento',
                'antiguo_valor' => ['id' => $ot->recubrimiento, 'descripcion' => $recubrimiento_antiguo],
                'nuevo_valor' => ['id' => $request->input('recubrimiento'), 'descripcion' => $recubrimiento_nuevo]
            ];
        }
        if (!in_array('Recubrimiento', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Recubrimiento'];
        }

        if ((string)$ot->golpes_largo !== (string)$request->input('golpes_largo')) {
            $datos_modificados['golpes_largo'] = [
                'texto' => 'Golpes al largo',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->golpes_largo],
                'nuevo_valor' => ['id' => null, 'descripcion' => round($request->input('golpes_largo'))]
            ];
        }
        if (!in_array('Golpes al largo', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Golpes al largo'];
        }

        if ((string)$ot->golpes_ancho !== (string)$request->input('golpes_ancho')) {
            $datos_modificados['golpes_ancho'] = [
                'texto' => 'Golpes al ancho',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->golpes_ancho],
                'nuevo_valor' => ['id' => null, 'descripcion' => round($request->input('golpes_ancho'))]
            ];
        }
        if (!in_array('Golpes al ancho', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Golpes al ancho'];
        }

        if (str_replace(",", ".", str_replace('.', '', $ot->separacion_golpes_largo)) != str_replace(",", ".", str_replace('.', '', $request->input('separacion_golpes_largo')))) {
            $datos_modificados['separacion_golpes_largo'] = [
                'texto' => 'Separación Golpes al Largo (mm)',
                'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $ot->separacion_golpes_largo))],
                'nuevo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('separacion_golpes_largo')))]
            ];
        }
        if (!in_array('Separación Golpes al Largo (mm)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Separación Golpes al Largo (mm)'];
        }

        if (str_replace(",", ".", str_replace('.', '', $ot->separacion_golpes_ancho)) != str_replace(",", ".", str_replace('.', '', $request->input('separacion_golpes_ancho')))) {
            $datos_modificados['separacion_golpes_ancho'] = [
                'texto' => 'Separación Golpes al Ancho (mm)',
                'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $ot->separacion_golpes_ancho))],
                'nuevo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('separacion_golpes_ancho')))]
            ];
        }
        if (!in_array('Separación Golpes al Ancho (mm)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Separación Golpes al Ancho (mm)'];
        }

        if (str_replace(",", ".", str_replace('.', '', $ot->cuchillas)) != str_replace(",", ".", str_replace('.', '', $request->input('cuchillas')))) {
            $datos_modificados['cuchillas'] = [
                'texto' => 'Cuchillas (ml)',
                'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $ot->cuchillas))],
                'nuevo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('cuchillas')))]
            ];
        }
        if (!in_array('Cuchillas (ml)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Cuchillas (ml)'];
        }

        if ((string)$ot->rayado_c1r1 !== (string)$request->input('rayado_c1r1')) {
            $datos_modificados['rayado_c1r1'] = [
                'texto' => 'Rayado C1/R1 (mm)',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->rayado_c1r1],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('rayado_c1r1')]
            ];
        }
        if (!in_array('Rayado C1/R1 (mm)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Rayado C1/R1 (mm)'];
        }

        if ((string)$ot->rayado_r1_r2 !== (string)$request->input('rayado_r1_r2')) {
            $datos_modificados['rayado_r1_r2'] = [
                'texto' => 'Rayado R1/R2 (mm)',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->rayado_r1_r2],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('rayado_r1_r2')]
            ];
        }
        if (!in_array('Rayado R1/R2 (mm)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Rayado R1/R2 (mm)'];
        }

        if ((string)$ot->rayado_r2_c2 !== (string)$request->input('rayado_r2_c2')) {
            $datos_modificados['rayado_r2_c2'] = [
                'texto' => 'Rayado R2/C2 (mm)',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->rayado_r2_c2],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('rayado_r2_c2')]
            ];
        }
        if (!in_array('Rayado R2/C2 (mm)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Rayado R2/C2 (mm)'];
        }


        if ((string)$ot->bct_min_lb !== (string)$request->input('bct_min_lb')) {
            $datos_modificados['bct_min_lb'] = [
                'texto' => 'BCT MIN (LB)',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->bct_min_lb],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('bct_min_lb')]
            ];
        }
        if (!in_array('BCT MIN (LB)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'BCT MIN (LB)'];
        }

        if ((string)$ot->bct_min_kg !== (string)$request->input('bct_min_kg')) {
            $datos_modificados['bct_min_kg'] = [
                'texto' => 'BCT MIN (KG)',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->bct_min_kg],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('bct_min_kg')]
            ];
        }
        if (!in_array('BCT MIN (KG)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'BCT MIN (KG)'];
        }

        if ((string)$ot->bct_humedo_lb !== (string)$request->input('bct_humedo_lb')) {
            $datos_modificados['bct_humedo_lb'] = [
                'texto' => 'BCT HUMEDO (LB)',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->bct_humedo_lb],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('bct_humedo_lb')]
            ];
        }
        if (!in_array('BCT HUMEDO (LB)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'BCT HUMEDO (LB)'];
        }

        if (str_replace(",", ".", str_replace('.', '', $ot->ect))  != str_replace(",", ".", str_replace('.', '', $request->input('ect')))) {
            $datos_modificados['ect'] = [
                'texto' => 'ECT (lb/pulg)',
                'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $ot->ect))],
                'nuevo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('ect')))]
            ];
        }
        if (!in_array('ECT (lb/pulg)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'ECT (lb/pulg)'];
        }

        if ($ot->gramaje != $request->input('gramaje')) {
            $datos_modificados['gramaje'] = [
                'texto' => 'Gramaje (g/m2)',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->gramaje],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('gramaje')]
            ];
        }
        if (!in_array('Gramaje (g/m2)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Gramaje (g/m2)'];
        }

        if ((string)$ot->mullen !== (string)$request->input('mullen')) {
            $datos_modificados['mullen'] = [
                'texto' => 'Mullen (LB/PULG2)',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->mullen],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('mullen')]
            ];
        }
        if (!in_array('Mullen (LB/PULG2)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Mullen (LB/PULG2)'];
        }

        if (str_replace(",", ".", str_replace('.', '', $ot->fct)) != str_replace(",", ".", str_replace('.', '', $request->input('fct')))) {
            $datos_modificados['fct'] = [
                'texto' => 'FCT (lb/pulg2)',
                'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $ot->fct))],
                'nuevo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('fct')))]
            ];
        }
        if (!in_array('FCT (lb/pulg2)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'FCT (lb/pulg2)'];
        }

        if (str_replace(",", ".", str_replace('.', '', $ot->espesor)) !=  str_replace(",", ".", str_replace('.', '', $request->input('espesor')))) {
            $datos_modificados['espesor'] = [
                'texto' => 'Espesor (mm)',
                'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $ot->espesor))],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('espesor')))]
            ];
        }
        if (!in_array('Espesor (mm)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Espesor (mm)'];
        }

        if (str_replace(",", ".", str_replace('.', '', $ot->cobb_interior)) !=  str_replace(",", ".", str_replace('.', '', $request->input('cobb_interior')))) {
            $datos_modificados['cobb_interior'] = [
                'texto' => 'Cobb Interior (g/m2)',
                'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $ot->cobb_interior))],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('cobb_interior')))]
            ];
        }
        if (!in_array('Cobb Interior (g/m2)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Cobb Interior (g/m2)'];
        }

        if (str_replace(",", ".", str_replace('.', '', $ot->cobb_exterior)) !=  str_replace(",", ".", str_replace('.', '', $request->input('cobb_exterior')))) {
            $datos_modificados['cobb_exterior'] = [
                'texto' => 'Cobb Exterior (g/m2)',
                'antiguo_valor' => ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $ot->cobb_exterior))],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => str_replace(",", ".", str_replace('.', '', $request->input('cobb_exterior')))]
            ];
        }
        if (!in_array('Cobb Exterior (g/m2)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Cobb Exterior (g/m2)'];
        }

        if ($ot->flexion_aleta !=  $request->input('flexion_aleta')) {
            $datos_modificados['flexion_aleta'] = [
                'texto' => 'Flexion de aleta (%)',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->flexion_aleta],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('flexion_aleta')]
            ];
        }
        if (!in_array('Flexion de aleta (%)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Flexion de aleta (%)'];
        }

        if ($ot->peso !=  $request->input('peso')) {
            $datos_modificados['peso'] = [
                'texto' => 'Peso (g)',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->peso],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('peso')]
            ];
        }
        if (!in_array('Peso (g)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Peso (g)'];
        }

        if ($ot->incision_rayado_longitudinal !=  $request->input('incision_rayado_longitudinal')) {
            $datos_modificados['incision_rayado_longitudinal'] = [
                'texto' => 'Incisión Rayado Longitudinal (N)',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->incision_rayado_longitudinal],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('incision_rayado_longitudinal')]
            ];
        }
        if (!in_array('Incisión Rayado Longitud [N]', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Incisión Rayado Longitudinal (N)'];
        }

        if ($ot->incision_rayado_vertical !=   $request->input('incision_rayado_vertical')) {
            $datos_modificados['incision_rayado_vertical'] = [
                'texto' => 'Incisión Rayado Transversal (N)',
                'antiguo_valor' => ['id' => null, 'descripcion' =>  $ot->incision_rayado_vertical],
                'nuevo_valor' =>  ['id' => null, 'descripcion' =>  $request->input('incision_rayado_vertical')]
            ];
        }
        if (!in_array('Incisión Rayado Vertical [N]', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Incisión Rayado Transversal (N)'];
        }

        if ($ot->dst !=  $request->input('dst')) {
            $datos_modificados['dst'] = [
                'texto' => 'DST BPI',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->dst],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('dst')]
            ];
        }
        if (!in_array('DST BPI', $campos_modificados)) {
            $campos[] = ['descripcion' => 'DST BPI'];
        }

        if ($ot->espesor_placa !=  $request->input('espesor_placa')) {
            $datos_modificados['espesor_placa'] = [
                'texto' => 'Espesor Placa',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->espesor_placa],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('espesor_placa')]
            ];
        }
        if (!in_array('Espesor Placa', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Espesor Placa'];
        }

        if ($ot->espesor_caja !=  $request->input('espesor_caja')) {
            $datos_modificados['espesor_caja'] = [
                'texto' => 'Espesor Caja',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->espesor_caja],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('espesor_caja')]
            ];
        }
        if (!in_array('Espesor Caja', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Espesor Caja'];
        }

        if ($ot->porosidad !=  $request->input('porosidad')) {
            $datos_modificados['porosidad'] = [
                'texto' => 'Porosidad',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->porosidad],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('porosidad')]
            ];
        }
        if (!in_array('Porosidad', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Porosidad'];
        }

        if ($ot->brillo !=  $request->input('brillo')) {
            $datos_modificados['brillo'] = [
                'texto' => 'Brillo',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->brillo],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('brillo')]
            ];
        }
        if (!in_array('Brillo', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Brillo'];
        }

        if ($ot->rigidez_4_ptos_long !=  $request->input('rigidez_4_ptos_long')) {
            $datos_modificados['rigidez_4_ptos_long'] = [
                'texto' => 'Rigidez 4 Ptos Long.',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->rigidez_4_ptos_long],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('rigidez_4_ptos_long')]
            ];
        }
        if (!in_array('Rigidez 4 Ptos Long.', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Rigidez 4 Ptos Long.'];
        }

        if ($ot->rigidez_4_ptos_transv !=  $request->input('rigidez_4_ptos_transv')) {
            $datos_modificados['rigidez_4_ptos_transv'] = [
                'texto' => 'Rigidez 4 Ptos Transv.',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->rigidez_4_ptos_transv],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('rigidez_4_ptos_transv')]
            ];
        }
        if (!in_array('Rigidez 4 Ptos Transv.', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Rigidez 4 Ptos Transv.'];
        }

        if ($ot->angulo_deslizamiento_tapa_exterior !=  $request->input('angulo_deslizamiento_tapa_exterior')) {
            $datos_modificados['angulo_deslizamiento_tapa_exterior'] = [
                'texto' => 'Angulo Deslizamiento Tapa Exterior.',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->angulo_deslizamiento_tapa_exterior],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('angulo_deslizamiento_tapa_exterior')]
            ];
        }
        if (!in_array('Angulo Deslizamiento Tapa Exterior.', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Angulo Deslizamiento Tapa Exterior.'];
        }

        if ($ot->angulo_deslizamiento_tapa_interior !=  $request->input('angulo_deslizamiento_tapa_interior')) {
            $datos_modificados['angulo_deslizamiento_tapa_interior'] = [
                'texto' => 'Angulo Deslizamiento Tapa Interior.',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->angulo_deslizamiento_tapa_interior],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('angulo_deslizamiento_tapa_interior')]
            ];
        }
        if (!in_array('Angulo Deslizamiento Tapa Interior.', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Angulo Deslizamiento Tapa Interior.'];
        }

        if ($ot->resistencia_frote !=  $request->input('resistencia_frote')) {
            $datos_modificados['resistencia_frote'] = [
                'texto' => 'Resistencia Frote.',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->resistencia_frote],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('resistencia_frote')]
            ];
        }
        if (!in_array('Resistencia Frote.', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Resistencia Frote.'];
        }

        if ($ot->contenido_reciclado !=  $request->input('contenido_reciclado')) {
            $datos_modificados['contenido_reciclado'] = [
                'texto' => 'Contenido Reciclado.',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->contenido_reciclado],
                'nuevo_valor' =>  ['id' => null, 'descripcion' => $request->input('contenido_reciclado')]
            ];
        }
        if (!in_array('Contenido Reciclado.', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Contenido Reciclado.'];
        }


        // ---------------------------------------------------------------------------- DATOS CINTA -------------------------------------------------------------

        if ($request->input('cinta') != '' && $request->input('cinta') == 1) {

            if ((string)$ot->distancia_cinta_1 !== (string)$request->input('distancia_cinta_1')) {
                $datos_modificados['distancia_cinta_1'] = [
                    'texto' => 'Distancia Corte 1 a Cinta 1 (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->distancia_cinta_1],
                    'nuevo_valor' => ['id' => null, 'descripcion' => round($request->input('distancia_cinta_1'))]
                ];
            }
            if (!in_array('Distancia Corte 1 a Cinta 1 (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Distancia Corte 1 a Cinta 1 (mm)'];
            }

            if ((string)$ot->distancia_cinta_2 !== (string)$request->input('distancia_cinta_2')) {
                $datos_modificados['distancia_cinta_2'] = [
                    'texto' => 'Distancia Corte 1 a Cinta 2 (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->distancia_cinta_2],
                    'nuevo_valor' => ['id' => null, 'descripcion' => round($request->input('distancia_cinta_2'))]
                ];
            }
            if (!in_array('Distancia Corte 1 a Cinta 2 (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Distancia Corte 1 a Cinta 2 (mm)'];
            }

            if ((string)$ot->distancia_cinta_3 !== (string)$request->input('distancia_cinta_3')) {
                $datos_modificados['distancia_cinta_3'] = [
                    'texto' => 'Distancia Corte 1 a Cinta 3 (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->distancia_cinta_3],
                    'nuevo_valor' => ['id' => null, 'descripcion' => round($request->input('distancia_cinta_3'))]
                ];
            }
            if (!in_array('Distancia Corte 1 a Cinta 3 (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Distancia Corte 1 a Cinta 3 (mm)'];
            }

            if ((string)$ot->distancia_cinta_4 !== (string)$request->input('distancia_cinta_4')) {
                $datos_modificados['distancia_cinta_4'] = [
                    'texto' => 'Distancia Corte 1 a Cinta 4 (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->distancia_cinta_4],
                    'nuevo_valor' => ['id' => null, 'descripcion' => round($request->input('distancia_cinta_4'))]
                ];
            }
            if (!in_array('Distancia Corte 1 a Cinta 4 (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Distancia Corte 1 a Cinta 4 (mm)'];
            }

            if ((string)$ot->distancia_cinta_5 !== (string)$request->input('distancia_cinta_5')) {
                $datos_modificados['distancia_cinta_5'] = [
                    'texto' => 'Distancia Corte 1 a Cinta 5 (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->distancia_cinta_5],
                    'nuevo_valor' => ['id' => null, 'descripcion' => round($request->input('distancia_cinta_5'))]
                ];
            }
            if (!in_array('Distancia Corte 1 a Cinta 5 (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Distancia Corte 1 a Cinta 5 (mm)'];
            }

            if ((string)$ot->distancia_cinta_6 !== (string)$request->input('distancia_cinta_6')) {
                $datos_modificados['distancia_cinta_6'] = [
                    'texto' => 'Distancia Corte 1 a Cinta 6 (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->distancia_cinta_6],
                    'nuevo_valor' => ['id' => null, 'descripcion' => round($request->input('distancia_cinta_6'))]
                ];
            }
            if (!in_array('Distancia Corte 1 a Cinta 6 (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Distancia Corte 1 a Cinta 6 (mm)'];
            }

            if ((string)$ot->corte_liner !== (string)$request->input('corte_liner')) {
                $datos_modificados['corte_liner'] = [
                    'texto' => 'Corte de Liner',
                    'antiguo_valor' => ['id' => $ot->corte_liner, 'descripcion' => $ot->corte_liner == 1 ? 'Si' : ($ot->corte_liner == '0' ? 'No' : null)],
                    'nuevo_valor' => ['id' => $request->input('corte_liner'), 'descripcion' => $request->input('corte_liner') == 1 ? 'Si' : ($request->input('corte_liner') == '0' ? 'No' :  null)]
                ];
            }
            if (!in_array('Corte de Liner', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Corte de Liner'];
            }

            if ((string)$ot->tipo_cinta !== (string)$request->input('tipo_cinta')) {
                $datos_modificados['tipo_cinta'] = [
                    'texto' => 'Tipo de Cinta',
                    'antiguo_valor' => ['id' => $ot->tipo_cinta, 'descripcion' => $ot->tipo_cinta == 1 ? 'Corte' : ($ot->tipo_cinta == 2 ? 'Resistencia' : null)],
                    'nuevo_valor' => ['id' => $request->input('tipo_cinta'), 'descripcion' => $request->input('tipo_cinta') == 1 ? 'Corte' : ($request->input('tipo_cinta') == 2 ? 'Resistencia' :  null)]
                ];
            }
            if (!in_array('Tipo de Cinta', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Tipo de Cinta'];
            }


            if ((string)$ot->cintas_x_caja !== (string)$request->input('cintas_x_caja')) {
                $datos_modificados['cintas_x_caja'] = [
                    'texto' => 'Cintas por caja',
                    'antiguo_valor' => ['id' => $ot->cintas_x_caja, 'descripcion' => $ot->cintas_x_caja == 1 ? 'Corte' : ($ot->cintas_x_caja == 2 ? 'Resistencia' : null)],
                    'nuevo_valor' => ['id' => $request->input('cintas_x_caja'), 'descripcion' => $request->input('cintas_x_caja') == 1 ? 'Corte' : ($request->input('cintas_x_caja') == 2 ? 'Resistencia' :  null)]
                ];
            }
            if (!in_array('Cintas por caja', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Cintas por caja'];
            }
        } else if ($request->input('cinta') != '' && $request->input('cinta') == 0) {

            if ($ot->distancia_cinta_1 != null) {
                $datos_modificados['distancia_cinta_1'] = [
                    'texto' => 'Distancia Corte 1 a Cinta 1 (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->distancia_cinta_1],
                    'nuevo_valor' => ['id' => null, 'descripcion' => null]
                ];
            }
            if (!in_array('Distancia Corte 1 a Cinta 1 (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Distancia Corte 1 a Cinta 1 (mm)'];
            }

            if ($ot->distancia_cinta_2 != null) {
                $datos_modificados['distancia_cinta_2'] = [
                    'texto' => 'Distancia Corte 1 a Cinta 2 (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->distancia_cinta_2],
                    'nuevo_valor' => ['id' => null, 'descripcion' => null]
                ];
            }
            if (!in_array('Distancia Corte 1 a Cinta 2 (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Distancia Corte 1 a Cinta 2 (mm)'];
            }

            if ($ot->distancia_cinta_3 != null) {
                $datos_modificados['distancia_cinta_3'] = [
                    'texto' => 'Distancia Corte 1 a Cinta 3 (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->distancia_cinta_3],
                    'nuevo_valor' => ['id' => null, 'descripcion' => null]
                ];
            }
            if (!in_array('Distancia Corte 1 a Cinta 3 (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Distancia Corte 1 a Cinta 3 (mm)'];
            }

            if ($ot->distancia_cinta_4 != null) {
                $datos_modificados['distancia_cinta_4'] = [
                    'texto' => 'Distancia Corte 1 a Cinta 4 (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->distancia_cinta_4],
                    'nuevo_valor' => ['id' => null, 'descripcion' => null]
                ];
            }
            if (!in_array('Distancia Corte 1 a Cinta 4 (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Distancia Corte 1 a Cinta 4 (mm)'];
            }

            if ($ot->distancia_cinta_5 != null) {
                $datos_modificados['distancia_cinta_5'] = [
                    'texto' => 'Distancia Corte 1 a Cinta 5 (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->distancia_cinta_5],
                    'nuevo_valor' => ['id' => null, 'descripcion' => null]
                ];
            }
            if (!in_array('Distancia Corte 1 a Cinta 5 (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Distancia Corte 1 a Cinta 5 (mm)'];
            }

            if ($ot->distancia_cinta_6 != null) {
                $datos_modificados['distancia_cinta_6'] = [
                    'texto' => 'Distancia Corte 1 a Cinta 6 (mm)',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->distancia_cinta_6],
                    'nuevo_valor' => ['id' => null, 'descripcion' => null]
                ];
            }
            if (!in_array('Distancia Corte 1 a Cinta 6 (mm)', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Distancia Corte 1 a Cinta 6 (mm)'];
            }


            if ($ot->corte_liner != null) {
                $datos_modificados['corte_liner'] = [
                    'texto' => 'Corte de Liner',
                    'antiguo_valor' => ['id' => $ot->corte_liner, 'descripcion' => $ot->corte_liner == 1 ? 'Si' : ($ot->corte_liner == '0' ? 'No' : null)],
                    'nuevo_valor' => ['id' => null, 'descripcion' => null]
                ];
            }
            if (!in_array('Corte de Liner', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Corte de Liner'];
            }

            if ($ot->tipo_cinta != null) {
                $datos_modificados['tipo_cinta'] = [
                    'texto' => 'Tipo de Cinta',
                    'antiguo_valor' => ['id' => $ot->tipo_cinta, 'descripcion' => $ot->tipo_cinta == 1 ? 'Corte' : ($ot->tipo_cinta == 2 ? 'Resistencia' : null)],
                    'nuevo_valor' => ['id' => null, 'descripcion' => null]
                ];
            }
            if (!in_array('Tipo de Cinta', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Tipo de Cinta'];
            }

            if ($ot->tipo_cinta != null) {
                $datos_modificados['tipo_cinta'] = [
                    'texto' => 'Tipo de Cinta',
                    'antiguo_valor' => ['id' => $ot->tipo_cinta, 'descripcion' => $ot->tipo_cinta == 1 ? 'Corte' : ($ot->tipo_cinta == 2 ? 'Resistencia' : null)],
                    'nuevo_valor' => ['id' => null, 'descripcion' => null]
                ];
            }
            if (!in_array('Tipo de Cinta', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Tipo de Cinta'];
            }
        }

        // ---------------------------------------------------------------------------- COLOR - CERA - BRANIZ -------------------------------------------------------------
        if ($ot->impresion != $request->input('impresion')) {
            $datos_modificados['impresion'] = [
                'texto' => 'Impresión',
                'antiguo_valor' => ['id' => $ot->impresion, 'descripcion' => $impresion_antiguo],
                'nuevo_valor' => ['id' => $request->input('impresion'), 'descripcion' => $impresion_nuevo]
            ];
        }
        if (!in_array('Impresión', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Impresión'];
        }



        if ($ot->trazabilidad != $request->input('trazabilidad')) {
            $datos_modificados['trazabilidad'] = [
                'texto' => 'Trazabilidad',
                'antiguo_valor' => ['id' => $ot->trazabilidad, 'descripcion' => $trazabilidad_antiguo],
                'nuevo_valor' => ['id' => $request->input('trazabilidad'), 'descripcion' => $trazabilidad_nuevo]
            ];
        }
        if (!in_array('Trazabilidad', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Trazabilidad'];
        }

        if ($ot->design_type_id != $request->input('design_type_id')) {
            $datos_modificados['design_type_id'] = [
                'texto' => 'Tipo Diseño',
                'antiguo_valor' => ['id' => $ot->design_type_id, 'descripcion' => $design_type_antiguo],
                'nuevo_valor' => ['id' => $request->input('design_type_id'), 'descripcion' => $design_type_nuevo]
            ];
        }
        if (!in_array('Tipo Diseño', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Tipo Diseño'];
        }

        if ($ot->complejidad != $request->input('complejidad')) {
            $datos_modificados['complejidad'] = [
                'texto' => 'Complejidad',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->complejidad],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('complejidad')]
            ];
        }
        if (!in_array('Complejidad', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Complejidad'];
        }

        if ($ot->numero_colores != $request->input('numero_colores')) {
            $datos_modificados['numero_colores'] = [
                'texto' => 'Número Colores',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->numero_colores],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('numero_colores')]
            ];
        }
        if (!in_array('Número Colores', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Número Colores'];
        }

        if ($ot->coverage_internal_id != $request->input('coverage_internal_id')) {
            $datos_modificados['coverage_internal_id'] = [
                'texto' => 'Recubrimiento Interno',
                'antiguo_valor' => ['id' => $ot->coverage_internal_id, 'descripcion' => $coverage_internal_antiguo],
                'nuevo_valor' => ['id' => $request->input('coverage_internal_id'), 'descripcion' => $coverage_internal_nuevo]
            ];
        }
        if (!in_array('Recubrimiento Interno', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Recubrimiento Interno'];
        }

        if ($ot->percentage_coverage_internal != $request->input('percentage_coverage_internal')) {
            $datos_modificados['percentage_coverage_internal'] = [
                'texto' => '% Recubrimiento Interno',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->percentage_coverage_internal],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('percentage_coverage_internal') >= 0 && $request->input('percentage_coverage_internal') != '' ? round($request->input('percentage_coverage_internal')) : null]
            ];
        }
        if (!in_array('% Recubrimiento Interno', $campos_modificados)) {
            $campos[] = ['descripcion' => '% Recubrimiento Interno'];
        }

        if ($ot->coverage_external_id != $request->input('coverage_external_id')) {
            $datos_modificados['coverage_external_id'] = [
                'texto' => 'Recubrimiento Externo',
                'antiguo_valor' => ['id' => $ot->coverage_external_id, 'descripcion' => $coverage_external_antiguo],
                'nuevo_valor' => ['id' => $request->input('coverage_external_id'), 'descripcion' => $coverage_external_nuevo]
            ];
        }
        if (!in_array('Recubrimiento Externo', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Recubrimiento Externo'];
        }

        if ($ot->percentage_coverage_external != $request->input('percentage_coverage_external')) {
            $datos_modificados['percentage_coverage_external'] = [
                'texto' => '% Recubrimiento Externo',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->percentage_coverage_external],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('percentage_coverage_external') >= 0 && $request->input('percentage_coverage_external') != '' ? round($request->input('percentage_coverage_external')) : null]
            ];
        }
        if (!in_array('% Recubrimiento Externo', $campos_modificados)) {
            $campos[] = ['descripcion' => '% Recubrimiento Externo'];
        }

        if ($ot->color_1_id != $request->input('color_1_id')) {
            $datos_modificados['color_1_id'] = [
                'texto' => 'Color 1',
                'antiguo_valor' => ['id' => $ot->color_1_id, 'descripcion' => $color_1_antiguo],
                'nuevo_valor' => ['id' => $request->input('color_1_id'), 'descripcion' => $color_1_nuevo]
            ];
        }
        if (!in_array('Color 1', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Color 1'];
        }

        if ($ot->impresion_1 != $request->input('impresion_1')) {
            $datos_modificados['impresion_1'] = [
                'texto' => '% Impresión 1',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->impresion_1],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('impresion_1') >= 0 && $request->input('impresion_1') != '' ? round($request->input('impresion_1')) : null]
            ];
        }
        if (!in_array('% Impresión 1', $campos_modificados)) {
            $campos[] = ['descripcion' => '% Impresión 1'];
        }


        if ($ot->color_2_id != $request->input('color_2_id')) {
            $datos_modificados['color_2_id'] = [
                'texto' => 'Color 2',
                'antiguo_valor' => ['id' => $ot->color_2_id, 'descripcion' => $color_2_antiguo],
                'nuevo_valor' => ['id' => $request->input('color_2_id'), 'descripcion' => $color_2_nuevo]
            ];
        }
        if (!in_array('Color 2', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Color 2'];
        }

        if ($ot->impresion_2 != $request->input('impresion_2')) {
            $datos_modificados['impresion_2'] = [
                'texto' => '% Impresión 2',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->impresion_2],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('impresion_2') >= 0 && $request->input('impresion_2') != '' ? round($request->input('impresion_2')) : null]
            ];
        }
        if (!in_array('% Impresión 2', $campos_modificados)) {
            $campos[] = ['descripcion' => '% Impresión 2'];
        }


        if ($ot->color_3_id != $request->input('color_3_id')) {
            $datos_modificados['color_3_id'] = [
                'texto' => 'Color 3',
                'antiguo_valor' => ['id' => $ot->color_3_id, 'descripcion' => $color_3_antiguo],
                'nuevo_valor' => ['id' => $request->input('color_3_id'), 'descripcion' => $color_3_nuevo]
            ];
        }
        if (!in_array('Color 3', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Color 3'];
        }

        if ($ot->impresion_3 != $request->input('impresion_3')) {
            $datos_modificados['impresion_3'] = [
                'texto' => '% Impresión 3',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->impresion_3],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('impresion_3') >= 0 && $request->input('impresion_3') != '' ? round($request->input('impresion_3')) : null]
            ];
        }
        if (!in_array('% Impresión 3', $campos_modificados)) {
            $campos[] = ['descripcion' => '% Impresión 3'];
        }


        if ($ot->color_4_id != $request->input('color_4_id')) {
            $datos_modificados['color_4_id'] = [
                'texto' => 'Color 4',
                'antiguo_valor' => ['id' => $ot->color_4_id, 'descripcion' => $color_4_antiguo],
                'nuevo_valor' => ['id' => $request->input('color_4_id'), 'descripcion' => $color_4_nuevo]
            ];
        }
        if (!in_array('Color 4', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Color 4'];
        }

        if ($ot->impresion_4 != $request->input('impresion_4')) {
            $datos_modificados['impresion_4'] = [
                'texto' => '% Impresión 4',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->impresion_4],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('impresion_4') >= 0 && $request->input('impresion_4') != '' ? round($request->input('impresion_4')) : null]
            ];
        }
        if (!in_array('% Impresión 4', $campos_modificados)) {
            $campos[] = ['descripcion' => '% Impresión 4'];
        }


        if ($ot->color_5_id != $request->input('color_5_id')) {
            $datos_modificados['color_5_id'] = [
                'texto' => 'Color 5',
                'antiguo_valor' => ['id' => $ot->color_5_id, 'descripcion' => $color_5_antiguo],
                'nuevo_valor' => ['id' => $request->input('color_5_id'), 'descripcion' => $color_5_nuevo]
            ];
        }
        if (!in_array('Color 5', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Color 5'];
        }

        if ($ot->impresion_5 != $request->input('impresion_5')) {
            $datos_modificados['impresion_5'] = [
                'texto' => '% Impresión 5',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->impresion_5],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('impresion_5') >= 0 && $request->input('impresion_5') != '' ? round($request->input('impresion_5')) : null]
            ];
        }
        if (!in_array('% Impresión 5', $campos_modificados)) {
            $campos[] = ['descripcion' => '% Impresión 5'];
        }

        if ($ot->color_6_id != $request->input('color_6_id')) {
            $datos_modificados['color_6_id'] = [
                'texto' => 'Color 6',
                'antiguo_valor' => ['id' => $ot->color_6_id, 'descripcion' => $color_6_antiguo],
                'nuevo_valor' => ['id' => $request->input('color_6_id'), 'descripcion' => $color_6_nuevo]
            ];
        }
        if (!in_array('Color 6', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Color 6'];
        }

        if ($ot->impresion_6 != $request->input('impresion_6')) {
            $datos_modificados['impresion_6'] = [
                'texto' => '% Impresión 6',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->impresion_6],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('impresion_6') >= 0 && $request->input('impresion_6') != '' ? round($request->input('impresion_6')) : null]
            ];
        }
        if (!in_array('% Impresión 6', $campos_modificados)) {
            $campos[] = ['descripcion' => '% Impresión 6'];
        }
        /*Se Desabilita a solicitud de correccion del Evolutivo 72 (Eliminar Barniz UV y % Impresión B. UV)
          Utilizando los datos para este campo de los que vengan del input coverage_external_id y percentage_coverage_external
        if ((string)$ot->barniz_uv !== (string)$request->input('barniz_uv')) {
            $datos_modificados['barniz_uv'] = [
                'texto' => 'Barniz UV',
                'antiguo_valor' => ['id' => $ot->barniz_uv, 'descripcion' => $ot->barniz_uv == 1 ? 'Si' : ($ot->barniz_uv == '0' ? 'No' : null)],
                'nuevo_valor' => ['id' => $request->input('barniz_uv'), 'descripcion' => $request->input('barniz_uv') == 1 ? 'Si' : ($request->input('barniz_uv') == '0' ? 'No' :  null)]
            ];}
        if(!in_array('Barniz UV', $campos_modificados)){
            $campos[] = ['descripcion' => 'Barniz UV'];
        }

        if ((string)$ot->porcentanje_barniz_uv !== (string)$request->input('porcentanje_barniz_uv')) {
            $datos_modificados['porcentanje_barniz_uv'] = [
                'texto' => '% Impresión Barniz UV',
                'antiguo_valor' => ['id' => null , 'descripcion' => $ot->porcentanje_barniz_uv],
                'nuevo_valor' => ['id' => null , 'descripcion' => $request->input('porcentanje_barniz_uv') >= 0 && $request->input('porcentanje_barniz_uv') != '' ? round($request->input('porcentanje_barniz_uv')) : ($request->input('porcentanje_barniz_uv') == '0' ? '0' : null)]
            ];}
        if(!in_array('% Impresión Barniz UV', $campos_modificados)){
            $campos[] = ['descripcion' => '% Impresión Barniz UV'];
        }
        */

        if ($ot->color_interno != $request->input('color_1_id')) {
            $datos_modificados['color_interno'] = [
                'texto' => 'Color Interno',
                'antiguo_valor' => ['id' => $ot->color_interno, 'descripcion' => $color_interno_antiguo],
                'nuevo_valor' => ['id' => $request->input('color_1_id'), 'descripcion' => $color_interno_nuevo]
            ];
        }
        if (!in_array('Color Interno', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Color Interno'];
        }

        if ($ot->impresion_color_interno != $request->input('impresion_1')) {
            $datos_modificados['impresion_color_interno'] = [
                'texto' => '% Impresión Color Interno',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->impresion_color_interno],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('impresion_1') >= 0 && $request->input('impresion_1') != '' ? round($request->input('impresion_1')) : null]
            ];
        }
        if (!in_array('% Impresión Color Interno', $campos_modificados)) {
            $campos[] = ['descripcion' => '% Impresión Color Interno'];
        }

        if ($ot->indicador_facturacion_diseno_grafico != $request->input('indicador_facturacion_diseno_grafico')) {
            $datos_modificados['indicador_facturacion_diseno_grafico'] = [
                'texto' => 'Indicador Facturación D. Gráfico',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->indicador_facturacion_diseno_grafico],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('indicador_facturacion_diseno_grafico')]
            ];
        }
        if (!in_array('Indicador Facturación D. Gráfico', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Indicador Facturación D. Gráfico'];
        }

        if ((string)$ot->prueba_color !== (string)$request->input('prueba_color')) {
            $datos_modificados['prueba_color'] = [
                'texto' => 'Prueba de Color',
                'antiguo_valor' => ['id' => $ot->prueba_color, 'descripcion' => $ot->prueba_color == 1 ? 'Si' : ($ot->prueba_color == '0' ? 'No' : null)],
                'nuevo_valor' => ['id' => $request->input('prueba_color'), 'descripcion' => $request->input('prueba_color') == 1 ? 'Si' : ($request->input('prueba_color') == '0' ? 'No' :  null)]
            ];
        }
        if (!in_array('Prueba de Color', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Prueba de Color'];
        }
        //------ DATOS ANTIGUOS-------------------------------

        // if ((string)$ot->pegado !== (string)$request->input('pegado')) {
        //     $datos_modificados['pegado'] = [
        //         'texto' => 'Pegado',
        //         'antiguo_valor' => ['id' => $ot->pegado, 'descripcion' => $ot->pegado == 1 ? 'Si' : ($ot->pegado == '0' ? 'No' : null)],
        //         'nuevo_valor' => ['id' => $request->input('pegado'), 'descripcion' => $request->input('pegado') == 1 ? 'Si' : ($request->input('pegado') == '0' ? 'No' :  null)]
        //     ];}
        // if(!in_array('Pegado', $campos_modificados)){
        //     $campos[] = ['descripcion' => 'Pegado'];
        // }


        // if ((string)$ot->cera_exterior !== (string)$request->input('cera_exterior')) {
        //     $datos_modificados['cera_exterior'] = [
        //         'texto' => 'Cera Exterior',
        //         'antiguo_valor' => ['id' => $ot->cera_exterior, 'descripcion' => $ot->cera_exterior == 1 ? 'Si' : ($ot->cera_exterior == '0' ? 'No' : null)],
        //         'nuevo_valor' => ['id' => $request->input('cera_exterior'), 'descripcion' => $request->input('cera_exterior') == 1 ? 'Si' : ($request->input('cera_exterior') == '0' ? 'No' :  null)]
        //     ];}
        // if(!in_array('Cera Exterior', $campos_modificados)){
        //     $campos[] = ['descripcion' => 'Cera Exterior'];
        // }

        // if ($ot->porcentaje_cera_exterior != $request->input('porcentaje_cera_exterior')) {
        //     $datos_modificados['porcentaje_cera_exterior'] = [
        //         'texto' => '% Cera Exterior',
        //         'antiguo_valor' => ['id' => null , 'descripcion' => $ot->porcentaje_cera_exterior],
        //         'nuevo_valor' => ['id' => null , 'descripcion' => $request->input('porcentaje_cera_exterior') >= 0 && $request->input('porcentaje_cera_exterior') != '' ? round($request->input('porcentaje_cera_exterior')) : null]
        //     ];}
        // if(!in_array('% Cera Exterior', $campos_modificados)){
        //     $campos[] = ['descripcion' => '% Cera Exterior'];
        // }


        // if ((string)$ot->cera_interior !== (string)$request->input('cera_interior')) {
        //     $datos_modificados['cera_interior'] = [
        //         'texto' => 'Cera Interior',
        //         'antiguo_valor' => ['id' => $ot->cera_interior, 'descripcion' => $ot->cera_interior == 1 ? 'Si' : ($ot->cera_interior == '0' ? 'No' : null)],
        //         'nuevo_valor' => ['id' => $request->input('cera_interior'), 'descripcion' => $request->input('cera_interior') == 1 ? 'Si' : ($request->input('cera_interior') == '0' ? 'No' :  null)]
        //     ];}
        // if(!in_array('Cera Interior', $campos_modificados)){
        //     $campos[] = ['descripcion' => 'Cera Interior'];
        // }

        // if ($ot->porcentaje_cera_interior != $request->input('porcentaje_cera_interior')) {
        //     $datos_modificados['porcentaje_cera_interior'] = [
        //         'texto' => '% Cera Interior',
        //         'antiguo_valor' => ['id' => null , 'descripcion' => $ot->porcentaje_cera_interior],
        //         'nuevo_valor' => ['id' => null , 'descripcion' => $request->input('porcentaje_cera_interior') >= 0 && $request->input('porcentaje_cera_interior') != '' ? round($request->input('porcentaje_cera_interior')) : null]
        //     ];}
        // if(!in_array('% Cera Interior', $campos_modificados)){
        //     $campos[] = ['descripcion' => '% Cera Interior'];
        // }


        // if ((string)$ot->barniz_interior !== (string)$request->input('barniz_interior')) {
        //     $datos_modificados['barniz_interior'] = [
        //         'texto' => 'Barniz Externo',
        //         'antiguo_valor' => ['id' => $ot->barniz_interior, 'descripcion' => $ot->barniz_interior == 1 ? 'Si' : ($ot->barniz_interior == '0' ? 'No' : null)],
        //         'nuevo_valor' => ['id' => $request->input('barniz_interior'), 'descripcion' => $request->input('barniz_interior') == 1 ? 'Si' : ($request->input('barniz_interior') == '0' ? 'No' :  null)]
        //     ];}
        // if(!in_array('% Cera Interior', $campos_modificados)){
        //     $campos[] = ['descripcion' => '% Cera Interior'];
        // }

        // if ($ot->porcentaje_barniz_interior != $request->input('porcentaje_barniz_interior')) {
        //     $datos_modificados['porcentaje_barniz_interior'] = [
        //         'texto' => '% Barniz Externo',
        //         'antiguo_valor' => ['id' => null , 'descripcion' => $ot->porcentaje_barniz_interior],
        //         'nuevo_valor' => ['id' => null , 'descripcion' => $request->input('porcentaje_barniz_interior') >= 0 && $request->input('porcentaje_barniz_interior') != '' ? round($request->input('porcentaje_barniz_interior')) : null]
        //     ];}
        // if(!in_array('% Barniz Externo', $campos_modificados)){
        //     $campos[] = ['descripcion' => '% Barniz Externo'];
        // }


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

        // ---------------------------------------------------------------------------- TERMINACIONES -------------------------------------------------------------

        if ($ot->process_id != $request->input('process_id')) {
            $datos_modificados['process_id'] = [
                'texto' => 'Proceso',
                'antiguo_valor' => ['id' => $ot->process_id, 'descripcion' => $proceso_antiguo],
                'nuevo_valor' => ['id' => $request->input('process_id'), 'descripcion' => $proceso_nuevo]
            ];
        }
        if (!in_array('Proceso', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Proceso'];
        }

        if ($ot->pegado_terminacion != $request->input('pegado_terminacion')) {
            $datos_modificados['pegado_terminacion'] = [
                'texto' => 'Tipo Pegado',
                'antiguo_valor' => ['id' => $ot->pegado_terminacion, 'descripcion' => $pegado_terminacion_antiguo],
                'nuevo_valor' => ['id' => $request->input('pegado_terminacion'), 'descripcion' => $pegado_terminacion_nuevo]
            ];
        }
        if (!in_array('Tipo Pegado', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Tipo Pegado'];
        }

        if ($ot->armado_id != $request->input('armado_id')) {
            $datos_modificados['armado_id'] = [
                'texto' => 'Armado',
                'antiguo_valor' => ['id' => $ot->armado_id, 'descripcion' => $armado_antiguo],
                'nuevo_valor' => ['id' => $request->input('armado_id'), 'descripcion' => $armado_nuevo]
            ];
        }
        if (!in_array('Armado', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Armado'];
        }

        if ($ot->sentido_armado != $request->input('sentido_armado')) {
            $datos_modificados['sentido_armado'] = [
                'texto' => 'Sentido de Armado',
                'antiguo_valor' => ['id' => $ot->sentido_armado, 'descripcion' => $sentido_armado_antiguo],
                'nuevo_valor' => ['id' => $request->input('sentido_armado'), 'descripcion' => $sentido_armado_nuevo]
            ];
        }
        if (!in_array('Sentido de Armado', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Sentido de Armado'];
        }

        if ((string)$ot->maquila !== (string)$request->input('maquila')) {
            $datos_modificados['maquila'] = [
                'texto' => 'Maquila',
                'antiguo_valor' => ['id' => $ot->maquila, 'descripcion' => $ot->maquila == 1 ? 'Si' : ($ot->maquila == '0' ? 'No' : null)],
                'nuevo_valor' => ['id' => $request->input('maquila'), 'descripcion' => $request->input('maquila') == 1 ? 'Si' : ($request->input('maquila') == '0' ? 'No' :  null)]
            ];
        }
        if (!in_array('Maquila', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Maquila'];
        }

        if ($ot->maquila_servicio_id != $request->input('maquila_servicio_id')) {
            $datos_modificados['maquila_servicio_id'] = [
                'texto' => 'Servicios Maquila',
                'antiguo_valor' => ['id' => $ot->maquila_servicio_id, 'descripcion' => $maquila_servicio_antiguo],
                'nuevo_valor' => ['id' => $request->input('maquila_servicio_id'), 'descripcion' => $maquila_servicio_nuevo]
            ];
        }
        if (!in_array('Servicios Maquila', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Servicios Maquila'];
        }

        // ---------------------------------------------------------------------------- MATERIAL ASIGNADO -------------------------------------------------------------
        //Se comenta ya que el material asigando, hasta los momentos, no se puede editar, llega siempre vacio por se campo de tipo ReadOnly
        // if ($ot->material_asignado != $request->input('material_asignado')) {
        //     \Log::info('se edita el matrial');
        //     $datos_modificados['material_asignado'] = [
        //         'texto' => 'Material Asignado',
        //         'antiguo_valor' => ['id' => null, 'descripcion' => $ot->material_asignado],
        //         'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('material_asignado')]
        //     ];}
        // if(!in_array('Material Asignado', $campos_modificados)){
        //     $campos[] = ['descripcion' => 'Material Asignado'];
        // }

        // if ($ot->descripcion_material != $request->input('descripcion_material')) {
        //     $datos_modificados['descripcion_material'] = [
        //         'texto' => 'Material Asignado Descripción',
        //         'antiguo_valor' => ['id' => null, 'descripcion' => $ot->descripcion_material],
        //         'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('descripcion_material')]
        //     ];}
        // if(!in_array('Material Asignado Descripción', $campos_modificados)){
        //     $campos[] = ['descripcion' => 'Material Asignado Descripción'];
        // }


        // ---------------------------------------------------------------------------- DATOS PARA EL DESARROLLO -------------------------------------------------------------

        if ((string)$ot->peso_contenido_caja !== (string)$request->input('peso_contenido_caja')) {
            $datos_modificados['peso_contenido_caja'] = [
                'texto' => 'Peso que contiene la caja (Kg)',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->peso_contenido_caja],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('peso_contenido_caja')]
            ];
        }
        if (!in_array('Peso que contiene la caja (Kg)', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Peso que contiene la caja (Kg)'];
        }


        if ((string)$ot->autosoportante !== (string)$request->input('autosoportante')) {
            $datos_modificados['autosoportante'] = [
                'texto' => 'Autosoportante',
                'antiguo_valor' => ['id' => $ot->autosoportante, 'descripcion' => $ot->autosoportante == 1 ? 'Si' : ($ot->autosoportante == '0' ? 'No' : null)],
                'nuevo_valor' => ['id' => $request->input('autosoportante'), 'descripcion' => $request->input('autosoportante') == 1 ? 'Si' : ($request->input('autosoportante') == '0' ? 'No' :  null)]
            ];
        }
        if (!in_array('Autosoportante', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Autosoportante'];
        }

        if ($ot->envase_id != $request->input('envase_id')) {
            $datos_modificados['envase_id'] = [
                'texto' => 'Envase Primario',
                'antiguo_valor' => ['id' => $ot->envase_id, 'descripcion' => $envase_antiguo],
                'nuevo_valor' => ['id' => $request->input('envase_id'), 'descripcion' => $envase_nuevo]
            ];
        }
        if (!in_array('Envase Primario', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Envase Primario'];
        }

        if ((string)$ot->cajas_altura !== (string)$request->input('cajas_altura')) {
            $datos_modificados['cajas_altura'] = [
                'texto' => 'Cantidad Cajas Apiladas',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->cajas_altura],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('cajas_altura')]
            ];
        }
        if (!in_array('Cantidad Cajas Apiladas', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Cantidad Cajas Apiladas'];
        }


        if ((string)$ot->pallet_sobre_pallet !== (string)$request->input('pallet_sobre_pallet')) {
            $datos_modificados['pallet_sobre_pallet'] = [
                'texto' => 'Pallet Sobre pallet',
                'antiguo_valor' => ['id' => $ot->pallet_sobre_pallet, 'descripcion' => $ot->pallet_sobre_pallet == 1 ? 'Si' : ($ot->pallet_sobre_pallet == '0' ? 'No' : null)],
                'nuevo_valor' => ['id' => $request->input('pallet_sobre_pallet'), 'descripcion' => $request->input('pallet_sobre_pallet') == 1 ? 'Si' : ($request->input('pallet_sobre_pallet') == '0' ? 'No' :  null)]
            ];
        }
        if (!in_array('Pallet Sobre pallet', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Pallet Sobre pallet'];
        }

        if ($ot->cantidad != $request->input('cantidad')) {
            $datos_modificados['cantidad'] = [
                'texto' => 'Cantidad',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->cantidad],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('cantidad')]
            ];
        }
        if (!in_array('Cantidad', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Cantidad'];
        }

        if ($ot->product_type_developing_id != $request->input('product_type_developing_id')) {
            $datos_modificados['product_type_developing_id'] = [
                'texto' => 'Tipo Producto',
                'antiguo_valor' => ['id' => $ot->product_type_developing_id, 'descripcion' => $productTypeDeveloping_antiguo],
                'nuevo_valor' => ['id' => $request->input('product_type_developing_id'), 'descripcion' => $productTypeDeveloping_nuevo]
            ];
        }
        if (!in_array('Tipo Producto', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Tipo Producto'];
        }

        if ($ot->food_type_id != $request->input('food_type_id')) {
            $datos_modificados['food_type_id'] = [
                'texto' => 'Tipo Alimento',
                'antiguo_valor' => ['id' => $ot->food_type_id, 'descripcion' => $foodType_antiguo],
                'nuevo_valor' => ['id' => $request->input('food_type_id'), 'descripcion' => $foodType_nuevo]
            ];
        }
        if (!in_array('Tipo Alimento', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Tipo Alimento'];
        }

        if ($ot->expected_use_id != $request->input('expected_use_id')) {
            $datos_modificados['expected_use_id'] = [
                'texto' => 'Uso Previsto',
                'antiguo_valor' => ['id' => $ot->expected_use_id, 'descripcion' => $foodType_antiguo],
                'nuevo_valor' => ['id' => $request->input('expected_use_id'), 'descripcion' => $foodType_nuevo]
            ];
        }
        if (!in_array('Uso Previsto', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Uso Previsto'];
        }

        if ($ot->recycled_use_id != $request->input('recycled_use_id')) {
            $datos_modificados['recycled_use_id'] = [
                'texto' => 'Uso Reciclado',
                'antiguo_valor' => ['id' => $ot->recycled_use_id, 'descripcion' => $recycledUse_antiguo],
                'nuevo_valor' => ['id' => $request->input('recycled_use_id'), 'descripcion' => $recycledUse_nuevo]
            ];
        }
        if (!in_array('Uso Reciclado', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Uso Reciclado'];
        }

        if ($ot->class_substance_packed_id != $request->input('class_substance_packed_id')) {
            $datos_modificados['class_substance_packed_id'] = [
                'texto' => 'Clase Sustancia a Embalar',
                'antiguo_valor' => ['id' => $ot->class_substance_packed_id, 'descripcion' => $classSubstancePacked_antiguo],
                'nuevo_valor' => ['id' => $request->input('class_substance_packed_id'), 'descripcion' => $classSubstancePacked_nuevo]
            ];
        }
        if (!in_array('Clase Sustancia a Embalar', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Clase Sustancia a Embalar'];
        }

        if ($ot->transportation_way_id != $request->input('transportation_way_id')) {
            $datos_modificados['transportation_way_id'] = [
                'texto' => 'Medio de Transporte',
                'antiguo_valor' => ['id' => $ot->transportation_way_id, 'descripcion' => $transportationWay_antiguo],
                'nuevo_valor' => ['id' => $request->input('transportation_way_id'), 'descripcion' => $transportationWay_nuevo]
            ];
        }
        if (!in_array('Medio de Transporte', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Medio de Transporte'];
        }

        if ($ot->target_market_id != $request->input('target_market_id')) {
            $datos_modificados['target_market_id'] = [
                'texto' => 'Mercado Destino',
                'antiguo_valor' => ['id' => $ot->target_market_id, 'descripcion' => $targetMarket_antiguo],
                'nuevo_valor' => ['id' => $request->input('target_market_id'), 'descripcion' => $targetMarket_nuevo]
            ];
        }
        if (!in_array('Mercado Destino', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Mercado Destino'];
        }

        // ---------------------------------------------------------------------------- OBSERVACIÓN -------------------------------------------------------------

        if ($ot->observacion != $request->input('observacion')) {
            $datos_modificados['observacion'] = [
                'texto' => 'Observación',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->observacion],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('observacion')]
            ];
        }
        if (!in_array('Observación', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Observación'];
        }

        // ---------------------------------------------------------------------------- Antecedentes Desarrollo -------------------------------------------------------------
        $correo_cliente = '';
        if (in_array('check_correo_cliente', $request->input('checkboxes'))) {
            $correo_cliente = 1;
        } else {
            $correo_cliente = 0;
        }
        if ($ot->ant_des_correo_cliente != $correo_cliente) {
            $datos_modificados['correo_cliente'] = [
                'texto' => 'Correo Cliente',
                'antiguo_valor' => ['id' => $ot->ant_des_correo_cliente, 'descripcion' => $ot->ant_des_correo_cliente == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $correo_cliente, 'descripcion' => $correo_cliente == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Correo Cliente', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Correo Cliente'];
        }

        $plano_actual = '';
        if (in_array('check_plano_actual', $request->input('checkboxes'))) {
            $plano_actual = 1;
        } else {
            $plano_actual = 0;
        }
        if ($ot->ant_des_plano_actual != $plano_actual) {
            $datos_modificados['plano_actual'] = [
                'texto' => 'Plano Actual',
                'antiguo_valor' => ['id' => $ot->ant_des_plano_actual, 'descripcion' => $ot->ant_des_plano_actual == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $plano_actual, 'descripcion' => $plano_actual == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Plano Actual', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Plano Actual'];
        }

        $boceto_actual = '';
        if (in_array('check_boceto_actual', $request->input('checkboxes'))) {
            $boceto_actual = 1;
        } else {
            $boceto_actual = 0;
        }
        if ($ot->ant_des_boceto_actual != $boceto_actual) {
            $datos_modificados['boceto_actual'] = [
                'texto' => 'Boceto Actual',
                'antiguo_valor' => ['id' => $ot->ant_des_boceto_actual, 'descripcion' => $ot->ant_des_boceto_actual == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $boceto_actual, 'descripcion' => $boceto_actual == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Boceto Actual', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Boceto Actual'];
        }

        $speed = '';
        if (in_array('check_speed', $request->input('checkboxes'))) {
            $speed = 1;
        } else {
            $speed = 0;
        }
        if ($ot->ant_des_speed != $speed) {
            $datos_modificados['speed'] = [
                'texto' => 'Spec',
                'antiguo_valor' => ['id' => $ot->ant_des_speed, 'descripcion' => $ot->ant_des_speed == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $speed, 'descripcion' => $speed == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Speed', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Speed'];
        }

        $otro = '';
        if (in_array('check_otro', $request->input('checkboxes'))) {
            $otro = 1;
        } else {
            $otro = 0;
        }
        if ($ot->ant_des_otro != $otro) {
            $datos_modificados['otro'] = [
                'texto' => 'Otro',
                'antiguo_valor' => ['id' => $ot->ant_des_otro, 'descripcion' => $ot->ant_des_otro == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $otro, 'descripcion' => $otro == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Otro', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Otro'];
        }


        $vb_muestra = '';
        if (in_array('check_vb_muestra', $request->input('checkboxes'))) {
            $vb_muestra = 1;
        } else {
            $vb_muestra = 0;
        }
        if ($ot->ant_des_vb_muestra != $vb_muestra) {
            $datos_modificados['vb_muestra'] = [
                'texto' => 'VB Muestra',
                'antiguo_valor' => ['id' => $ot->ant_des_vb_muestra, 'descripcion' => $ot->ant_des_vb_muestra == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $vb_muestra, 'descripcion' => $vb_muestra == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('VB Muestra', $campos_modificados)) {
            $campos[] = ['descripcion' => 'VB Muestra'];
        }

        $vb_boce = '';
        if (in_array('check_vb_boce', $request->input('checkboxes'))) {
            $vb_boce = 1;
        } else {
            $vb_boce = 0;
        }
        if ($ot->ant_des_vb_boce != $vb_boce) {
            $datos_modificados['vb_boce'] = [
                'texto' => 'VB Boce',
                'antiguo_valor' => ['id' => $ot->ant_des_vb_boce, 'descripcion' => $ot->ant_des_vb_boce == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $vb_boce, 'descripcion' => $vb_boce == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('VB Boce', $campos_modificados)) {
            $campos[] = ['descripcion' => 'VB Boce'];
        }

        $referencia_de = '';
        if (in_array('check_referencia_de', $request->input('checkboxes'))) {
            $referencia_de = 1;
        } else {
            $referencia_de = 0;
        }
        if ($ot->ant_des_cj_referencia_de != $referencia_de) {
            $datos_modificados['referencia_de'] = [
                'texto' => 'CJ Referencia DE',
                'antiguo_valor' => ['id' => $ot->ant_des_cj_referencia_de, 'descripcion' => $ot->ant_des_cj_referencia_de == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $referencia_de, 'descripcion' => $referencia_de == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('CJ Referencia DE', $campos_modificados)) {
            $campos[] = ['descripcion' => 'CJ Referencia DE'];
        }

        $referencia_dg = '';
        if (in_array('check_referencia_dg', $request->input('checkboxes'))) {
            $referencia_dg = 1;
        } else {
            $referencia_dg = 0;
        }
        if ($ot->ant_des_cj_referencia_dg != $referencia_dg) {
            $datos_modificados['referencia_dg'] = [
                'texto' => 'CJ Referencia DG',
                'antiguo_valor' => ['id' => $ot->ant_des_cj_referencia_dg, 'descripcion' => $ot->ant_des_cj_referencia_dg == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $referencia_dg, 'descripcion' => $referencia_dg == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('CJ Referencia DG', $campos_modificados)) {
            $campos[] = ['descripcion' => 'CJ Referencia DG'];
        }

        $envase_primario = '';
        if (in_array('check_envase_primario', $request->input('checkboxes'))) {
            $envase_primario = 1;
        } else {
            $envase_primario = 0;
        }
        if ($ot->ant_des_envase_primario != $envase_primario) {
            $datos_modificados['envase_primario'] = [
                'texto' => 'Envase Primario',
                'antiguo_valor' => ['id' => $ot->ant_des_envase_primario, 'descripcion' => $ot->ant_des_envase_primario == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $envase_primario, 'descripcion' => $envase_primario == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Envase Primario', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Envase Primario'];
        }

        $conservar_muestra = '';
        if (in_array('check_conservar_si', $request->input('checkboxes'))) {
            $conservar_muestra = 1;
        } else {
            if (in_array('check_conservar_no', $request->input('checkboxes'))) {
                $conservar_muestra = 0;
            }
        }
        if ($ot->ant_des_conservar_muestra != $conservar_muestra) {
            $datos_modificados['conservar_muestra'] = [
                'texto' => 'Conservar Muestra',
                'antiguo_valor' => ['id' => $ot->ant_des_conservar_muestra, 'descripcion' => $ot->ant_des_conservar_muestra == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $conservar_muestra, 'descripcion' => $conservar_muestra == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Conservar Muestra', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Conservar Muestra'];
        }
        $armado_automatico = '';
        if (in_array('check_armado_automatico_si', $request->input('checkboxes'))) {
            $armado_automatico = 1;
        } else {
            if (in_array('check_armado_automatico_no', $request->input('checkboxes'))) {
                $armado_automatico = 0;
            }
        }
        if ($ot->armado_automatico != $armado_automatico) {
            $datos_modificados['armado_automatico'] = [
                'texto' => 'Armado Automático',
                'antiguo_valor' => ['id' => $ot->armado_automatico, 'descripcion' => $ot->armado_automatico == 1 ? 'Si' : 'No'],
                'nuevo_valor' => ['id' => $armado_automatico, 'descripcion' => $armado_automatico == 1 ? 'Si' : 'No']
            ];
        }
        if (!in_array('Armado Automático', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Armado Automático'];
        }

        $file_check_correo_cliente = '';
        $file_check_correo_cliente_change = false;
        if ($request->hasfile('file_check_correo_cliente')) {
            $archivo = $request->file('file_check_correo_cliente');
            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $destinationFolder = public_path('/files/');
            $file_check_correo_cliente = $destinationFolder . $filename . '.' . $extension;
        }
        if ($ot->ant_des_correo_cliente_file != $file_check_correo_cliente) {
            $datos_modificados['file_check_correo_cliente'] = [
                'texto' => 'Correo Cliente Adjunto',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->ant_des_correo_cliente_file],
                'nuevo_valor' => ['id' => null, 'descripcion' => $file_check_correo_cliente]
            ];
            $file_check_correo_cliente_change = true;
        }
        if (!in_array('Correo Cliente Adjunto', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Correo Cliente Adjunto'];
        }

        $file_check_plano_actual = '';
        $file_check_plano_actual_change = false;
        if ($request->hasfile('file_check_plano_actual')) {
            $archivo = $request->file('file_check_plano_actual');
            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $destinationFolder = public_path('/files/');
            $file_check_plano_actual = $destinationFolder . $filename . '.' . $extension;
        }
        if ($ot->ant_des_plano_actual_file != $file_check_plano_actual) {
            $datos_modificados['file_check_plano_actual'] = [
                'texto' => 'Plano Actual Adjunto',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->ant_des_plano_actual_file],
                'nuevo_valor' => ['id' => null, 'descripcion' => $file_check_plano_actual]
            ];
            $file_check_plano_actual_change = true;
        }
        if (!in_array('Plano Actual Adjunto', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Plano Actual Adjunto'];
        }

        $file_check_boceto_actual = '';
        $file_check_boceto_actual_change = false;
        if ($request->hasfile('file_check_boceto_actual')) {
            $archivo = $request->file('file_check_boceto_actual');
            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $destinationFolder = public_path('/files/');
            $file_check_boceto_actual = $destinationFolder . $filename . '.' . $extension;
        }
        if ($ot->ant_des_boceto_actual_file != $file_check_boceto_actual) {
            $datos_modificados['file_check_boceto_actual'] = [
                'texto' => 'Boceto Actual Adjunto',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->ant_des_boceto_actual_file],
                'nuevo_valor' => ['id' => null, 'descripcion' => $file_check_boceto_actual]
            ];
            $file_check_boceto_actual_change = true;
        }
        if (!in_array('Boceto Actual Adjunto', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Boceto Actual Adjunto'];
        }

        $file_check_speed = '';
        $file_check_speed_change = false;
        if ($request->hasfile('file_check_speed')) {
            $archivo = $request->file('file_check_speed');
            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $destinationFolder = public_path('/files/');
            $file_check_speed = $destinationFolder . $filename . '.' . $extension;
        }
        if ($ot->ant_des_speed_file != $file_check_speed) {
            $datos_modificados['file_check_speed'] = [
                'texto' => 'Speed Adjunto',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->ant_des_speed_file],
                'nuevo_valor' => ['id' => null, 'descripcion' => $file_check_speed]
            ];
            $file_check_speed_change = true;
        }
        if (!in_array('Speed Adjunto', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Speed Adjunto'];
        }

        $file_check_otro = '';
        $file_check_otro_change = false;
        if ($request->hasfile('file_check_otro')) {
            $archivo = $request->file('file_check_otro');
            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $destinationFolder = public_path('/files/');
            $file_check_otro = $destinationFolder . $filename . '.' . $extension;
        }
        if ($ot->ant_des_otro_file != $file_check_otro) {
            $datos_modificados['file_check_otro'] = [
                'texto' => 'Otro Adjunto',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->ant_des_otro_file],
                'nuevo_valor' => ['id' => null, 'descripcion' => $file_check_otro]
            ];
            $file_check_otro_change = true;
        }
        if (!in_array('Otro Adjunto', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Otro Adjunto'];
        }


        $file_check_vb_muestra = '';
        $file_check_vb_muestra_change = false;
        if ($request->hasfile('file_check_vb_muestra')) {
            $archivo = $request->file('file_check_vb_muestra');
            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $destinationFolder = public_path('/files/');
            $file_check_vb_muestra = $destinationFolder . $filename . '.' . $extension;
        }
        if ($ot->ant_des_vb_muestra_file != $file_check_vb_muestra) {
            $datos_modificados['file_check_vb_muestra'] = [
                'texto' => 'VB Muestra Adjunto',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->ant_des_vb_muestra_file],
                'nuevo_valor' => ['id' => null, 'descripcion' => $file_check_vb_muestra]
            ];
            $file_check_vb_muestra_change = true;
        }
        if (!in_array('VB Muestra Adjunto', $campos_modificados)) {
            $campos[] = ['descripcion' => 'VB Muestra Adjunto'];
        }

        $file_check_vb_boce = '';
        $file_check_vb_boce_change = false;
        if ($request->hasfile('file_check_vb_boce')) {
            $archivo = $request->file('file_check_vb_boce');
            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $destinationFolder = public_path('/files/');
            $file_check_vb_boce = $destinationFolder . $filename . '.' . $extension;
        }
        if ($ot->ant_des_vb_boce_file != $file_check_vb_boce) {
            $datos_modificados['file_check_vb_boce'] = [
                'texto' => 'VB Muestra Adjunto',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->ant_des_vb_boce_file],
                'nuevo_valor' => ['id' => null, 'descripcion' => $file_check_vb_boce]
            ];
            $file_check_vb_boce_change = true;
        }
        if (!in_array('VB Muestra Adjunto', $campos_modificados)) {
            $campos[] = ['descripcion' => 'VB Muestra Adjunto'];
        }


        if ($ot->descripcion_material != $request->input('descripcion_material')) {
            $datos_modificados['descripcion_material'] = [
                'texto' => 'Descripción Material',
                'antiguo_valor' => ['id' => null, 'descripcion' => $ot->descripcion_material],
                'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('descripcion_material')]
            ];
        }
        //SECUENCISA OPERACIONALES
        if ($ot->so_planta_original != $request->input('sec_operacional_principal')) {
            $datos_modificados['so_planta_original'] = [
                'texto' => 'Secuencia Operacional Original',
                'antiguo_valor' => ['id' => $ot->so_planta_original, 'descripcion' => $so_planta_original_antiguo],
                'nuevo_valor' => ['id' => $request->input('sec_operacional_principal'), 'descripcion' => $so_planta_original_nuevo]
            ];
        }
        if ($ot->so_planta_alt1_antiguo != $request->input('sec_operacional_1')) {
            $datos_modificados['so_planta_alt1'] = [
                'texto' => 'Secuencia Operacional ALT1',
                'antiguo_valor' => ['id' => $ot->so_planta_alt1_antiguo, 'descripcion' => $so_planta_alt1_antiguo],
                'nuevo_valor' => ['id' => $request->input('sec_operacional_1'), 'descripcion' => $so_planta_alt1_nuevo]
            ];
        }
        if ($ot->so_planta_alt2_antiguo != $request->input('sec_operacional_2')) {
            $datos_modificados['so_planta_alt2'] = [
                'texto' => 'Secuencia Operacional ALT2',
                'antiguo_valor' => ['id' => $ot->so_planta_alt2_antiguo, 'descripcion' => $so_planta_alt2_antiguo],
                'nuevo_valor' => ['id' => $request->input('sec_operacional_2'), 'descripcion' => $so_planta_alt2_nuevo]
            ];
        }
        //SIN SECUENCIAS OPERACIONALES
        if (!in_array('Descripción Material', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Descripción Material'];
        }

        // ------- FIN ------- Compara datos para la bitacora ( antes de que se guarden )
        $numero_muestras = $ot->numero_muestras;
        // DATOS COMERCIALES
        // $ot->client_id             = (trim($request->input('client_id')) != '') ? $request->input('client_id') : $ot->client_id;
        // $ot->tipo_solicitud             = (trim($request->input('tipo_solicitud')) != '') ? $request->input('tipo_solicitud') : $ot->tipo_solicitud;
        $ot->descripcion         = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $ot->descripcion;
        $ot->dato_sub_cliente    = (trim($request->input('dato_sub_cliente')) != '') ? $request->input('dato_sub_cliente') : $ot->dato_sub_cliente;
        $ot->codigo_producto     = (trim($request->input('codigo_producto')) != '') ? $request->input('codigo_producto') : null;
        //Solicitud correccion Evolutivo 24-09
        $ot->codigo_producto_cliente = (trim($request->input('codigo_producto')) != '') ? $request->input('codigo_producto') : null;
        $ot->nombre_contacto     = (trim($request->input('nombre_contacto')) != '') ? $request->input('nombre_contacto') : $ot->nombre_contacto;
        $ot->email_contacto      = (trim($request->input('email_contacto')) != '') ? $request->input('email_contacto') : $ot->email_contacto;
        $ot->telefono_contacto   = (trim($request->input('telefono_contacto')) != '') ?  str_replace(' ', '', $request->input('telefono_contacto')) : $ot->telefono_contacto;
        $ot->volumen_venta_anual = (trim($request->input('volumen_venta_anual')) != '') ? str_replace('.', '', $request->input('volumen_venta_anual'))  : $ot->volumen_venta_anual;
        $ot->usd                 = (trim($request->input('usd')) != '') ? str_replace('.', '', $request->input('usd')) : $ot->usd;
        $ot->org_venta_id        = (trim($request->input('org_venta_id')) != '') ? $request->input('org_venta_id') : null;
        $ot->oc                  = (trim($request->input('oc')) != '') ? $request->input('oc') : null;
        $ot->canal_id            = (trim($request->input('canal_id')) != '') ? $request->input('canal_id') : $ot->canal_id;
        $ot->subsubhierarchy_id  = (trim($request->input('subsubhierarchy_id')) != '') ? $request->input('subsubhierarchy_id') : $ot->subsubhierarchy_id;
        // Solicita
        if (in_array('analisis', $request->input('checkboxes'))) $ot->analisis = 1;
        else $ot->analisis = 0;
        if (in_array('plano', $request->input('checkboxes'))) $ot->plano = 1;
        else $ot->plano = 0;
        if (in_array('datos_cotizar', $request->input('checkboxes'))) $ot->datos_cotizar = 1;
        else $ot->datos_cotizar = 0;
        if (in_array('boceto', $request->input('checkboxes'))) $ot->boceto = 1;
        else $ot->boceto = 0;
        if (in_array('nuevo_material', $request->input('checkboxes'))) $ot->nuevo_material = 1;
        else $ot->nuevo_material = 0;
        if (in_array('prueba_industrial', $request->input('checkboxes'))) $ot->prueba_industrial = 1;
        else $ot->prueba_industrial = 0;
        if (in_array('muestra', $request->input('checkboxes'))) {
            $ot->muestra = 1;
            $ot->numero_muestras = (trim($request->input('numero_muestras')) != '') ? $request->input('numero_muestras') : $ot->numero_muestras;
        } else {
            $ot->muestra = 0;
            $ot->numero_muestras = 0;
        }
        // Referencia
        $ot->reference_type        = (trim($request->input('reference_type')) != '') ? $request->input('reference_type') : null;
        $ot->reference_id          = (trim($request->input('reference_id')) != '') ? $request->input('reference_id') : null;
        $ot->bloqueo_referencia    = (trim($request->input('bloqueo_referencia')) != '') ? $request->input('bloqueo_referencia') : null;
        $ot->indicador_facturacion = (trim($request->input('indicador_facturacion')) != '') ? $request->input('indicador_facturacion') : null;
        // Caracteristicas
        // El cad puede ser seleccionado de un listado o ingresado libremente por eso permite guardar ambas opciones
        $ot->cad_id          = (trim($request->input('cad_id')) != '') ? $request->input('cad_id') : null;
        $ot->cad             = (trim($request->input('cad')) != $search_cad) ? $search_cad : $request->input('cad');
        $ot->product_type_id = (trim($request->input('product_type_id')) != '') ? $request->input('product_type_id') : $ot->product_type_id;
        $ot->items_set       = (trim($request->input('items_set')) != '') ? $request->input('items_set') : null;
        $ot->veces_item      = (trim($request->input('veces_item')) != '') ? $request->input('veces_item') : null;
        // $ot->carton_id             = (trim($request->input('carton_id')) != '') ? $request->input('carton_id') : $ot->carton_id;
        // $ot->carton_color = (trim($request->input('carton_color')) != '') ? $request->input('carton_color') : $ot->carton_color;
        $ot->carton_id          = $request->input('carton_id');
        $ot->carton_color       = $request->input('carton_color');
        $ot->style_id           = (trim($request->input('style_id')) != '') ? $request->input('style_id') : null;
        $ot->matriz_id           = (trim($request->input('matriz_id')) != '') ? $request->input('matriz_id') : null;
        $ot->recubrimiento      = (trim($request->input('recubrimiento')) != '') ? $request->input('recubrimiento') : null;
        $ot->largura_hm         = (trim($request->input('largura_hm')) != '') ? $request->input('largura_hm') : null;
        $ot->anchura_hm         = (trim($request->input('anchura_hm')) != '') ? $request->input('anchura_hm') : null;
        $ot->area_producto      = (trim($request->input('area_producto')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('area_producto'))) : null;
        $ot->recorte_adicional  = (trim($request->input('recorte_adicional')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('recorte_adicional'))) : null;
        $ot->longitud_pegado   = (trim($request->input('longitud_pegado')) != '') ? $request->input('longitud_pegado') : null;
        $ot->bct_min_lb         = (trim($request->input('bct_min_lb')) != '') ? $request->input('bct_min_lb') : null;
        $ot->bct_min_kg         = (trim($request->input('bct_min_kg')) != '') ? $request->input('bct_min_kg') : null;
        $ot->bct_humedo_lb      = (trim($request->input('bct_humedo_lb')) != '') ? $request->input('bct_humedo_lb') : null;
        $ot->golpes_largo       = (trim($request->input('golpes_largo')) != '') ? $request->input('golpes_largo') : null;
        $ot->golpes_ancho       = (trim($request->input('golpes_ancho')) != '') ? $request->input('golpes_ancho') : null;
        $ot->separacion_golpes_ancho = (trim($request->input('separacion_golpes_ancho')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('separacion_golpes_ancho'))) : null;
        $ot->separacion_golpes_largo = (trim($request->input('separacion_golpes_largo')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('separacion_golpes_largo'))) : null;
        $ot->cuchillas = (trim($request->input('cuchillas')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('cuchillas'))) : null;
        $ot->rayado_c1r1        = (trim($request->input('rayado_c1r1')) != '') ? $request->input('rayado_c1r1') : null;
        $ot->rayado_r1_r2       = (trim($request->input('rayado_r1_r2')) != '') ? $request->input('rayado_r1_r2') : null;
        $ot->rayado_r2_c2       = (trim($request->input('rayado_r2_c2')) != '') ? $request->input('rayado_r2_c2') : null;

        $ot->gramaje                        = (trim($request->input('gramaje')) != '') ? $request->input('gramaje') : null;
        $ot->mullen                         = (trim($request->input('mullen')) != '') ? $request->input('mullen') : null;
        $ot->ect                            = (trim($request->input('ect')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('ect'))) : null;
        $ot->flexion_aleta                  = (trim($request->input('flexion_aleta')) != '') ? $request->input('flexion_aleta') : null;
        $ot->peso                           = (trim($request->input('peso')) != '') ? $request->input('peso') : null;
        $ot->incision_rayado_longitudinal   = (trim($request->input('incision_rayado_longitudinal')) != '') ? $request->input('incision_rayado_longitudinal') : null;
        $ot->incision_rayado_vertical       = (trim($request->input('incision_rayado_vertical')) != '') ? $request->input('incision_rayado_vertical') : null;
        $ot->fct                            = (trim($request->input('fct')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('fct'))) : null;
        $ot->cobb_interior                  = (trim($request->input('cobb_interior')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('cobb_interior'))) : null;
        $ot->cobb_exterior                  = (trim($request->input('cobb_exterior')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('cobb_exterior'))) : null;
        $ot->espesor                        = (trim($request->input('espesor')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('espesor'))) : null;
        $ot->fsc                            = (trim($request->input('fsc')) != '') ? $request->input('fsc') : null;
        $ot->fsc_observacion                = (trim($request->input('fsc_observacion')) != '') ? $request->input('fsc_observacion') : null;
        $ot->pais_id                        = (trim($request->input('pais_id')) != '') ? $request->input('pais_id') : null;
        $ot->planta_id                      = (trim($request->input('planta_id')) != '') ? $request->input('planta_id') : null;
        $ot->restriccion_pallet             = (trim($request->input('restriccion_pallet')) != '') ? $request->input('restriccion_pallet') : null;
        $ot->tamano_pallet_type_id          = (trim($request->input('tamano_pallet_type_id')) != '') ? $request->input('tamano_pallet_type_id') : null;
        $ot->altura_pallet                  = (trim($request->input('altura_pallet')) != '') ? $request->input('altura_pallet') : null;
        $ot->permite_sobresalir_carga       = (trim($request->input('permite_sobresalir_carga')) != '') ? $request->input('permite_sobresalir_carga') : null;
        $ot->dst                            = (trim($request->input('dst')) != '') ? $request->input('dst') : null;
        $ot->espesor_placa                  = (trim($request->input('espesor_placa')) != '') ? $request->input('espesor_placa') : null;
        $ot->espesor_caja                   = (trim($request->input('espesor_caja')) != '') ? $request->input('espesor_caja') : null;
        $ot->porosidad                      = (trim($request->input('porosidad')) != '') ? $request->input('porosidad') : null;
        $ot->brillo                         = (trim($request->input('brillo')) != '') ? $request->input('brillo') : null;
        $ot->rigidez_4_ptos_long            = (trim($request->input('rigidez_4_ptos_long')) != '') ? $request->input('rigidez_4_ptos_long') : null;
        $ot->rigidez_4_ptos_transv          = (trim($request->input('rigidez_4_ptos_transv')) != '') ? $request->input('rigidez_4_ptos_transv') : null;
        $ot->angulo_deslizamiento_tapa_exterior = (trim($request->input('angulo_deslizamiento_tapa_exterior')) != '') ? $request->input('angulo_deslizamiento_tapa_exterior') : null;
        $ot->angulo_deslizamiento_tapa_interior = (trim($request->input('angulo_deslizamiento_tapa_interior')) != '') ? $request->input('angulo_deslizamiento_tapa_interior') : null;
        $ot->resistencia_frote              = (trim($request->input('resistencia_frote')) != '') ? $request->input('resistencia_frote') : null;
        $ot->contenido_reciclado            = (trim($request->input('contenido_reciclado')) != '') ? $request->input('contenido_reciclado') : null;

        $ot->cinta   = (trim($request->input('cinta')) != '') ? $request->input('cinta') : null;
        // campos de Distancia cinta
        // Solo guardar el valor si no es vacio y si cinta = "SI"
        $ot->corte_liner        = (trim($request->input('corte_liner')) != '' && $ot->cinta == 1) ? $request->input('corte_liner') : null;
        $ot->tipo_cinta         = (trim($request->input('tipo_cinta')) != '' && $ot->cinta == 1) ? $request->input('tipo_cinta') : null;
        $ot->cintas_x_caja         = (trim($request->input('cintas_x_caja')) != '' && $ot->cinta == 1) ? $request->input('cintas_x_caja') : null;
        $ot->distancia_cinta_1  = (trim($request->input('distancia_cinta_1')) != '' && $ot->cinta == 1) ? $request->input('distancia_cinta_1') : null;
        $ot->distancia_cinta_2  = (trim($request->input('distancia_cinta_2')) != '' && $ot->cinta == 1) ? $request->input('distancia_cinta_2') : null;
        $ot->distancia_cinta_3  = (trim($request->input('distancia_cinta_3')) != '' && $ot->cinta == 1) ? $request->input('distancia_cinta_3') : null;
        $ot->distancia_cinta_4  = (trim($request->input('distancia_cinta_4')) != '' && $ot->cinta == 1) ? $request->input('distancia_cinta_4') : null;
        $ot->distancia_cinta_5  = (trim($request->input('distancia_cinta_5')) != '' && $ot->cinta == 1) ? $request->input('distancia_cinta_5') : null;
        $ot->distancia_cinta_6  = (trim($request->input('distancia_cinta_6')) != '' && $ot->cinta == 1) ? $request->input('distancia_cinta_6') : null;

        //Campos de tipo de diseño
        $ot->design_type_id = (trim($request->input('design_type_id')) != '') ? $request->input('design_type_id') : null;
        $ot->complejidad = (trim($request->input('complejidad')) != '') ? $request->input('complejidad') : null;

        // Color barniz cera
        $ot->impresion = (trim($request->input('impresion')) != '') ? $request->input('impresion') : null;
        $ot->trazabilidad = (trim($request->input('trazabilidad')) != '') ? $request->input('trazabilidad') : null;
        $ot->design_type_id = (trim($request->input('design_type_id')) != '') ? $request->input('design_type_id') : null;
        $ot->complejidad = (trim($request->input('complejidad')) != '') ? $request->input('complejidad') : $ot->complejidad;

        $ot->coverage_internal_id = (trim($request->input('coverage_internal_id')) != '') ? $request->input('coverage_internal_id') : null;
        $ot->percentage_coverage_internal  = (trim($request->input('percentage_coverage_internal')) != '') ? $request->input('percentage_coverage_internal') : null;
        $ot->coverage_external_id = (trim($request->input('coverage_external_id')) != '') ? $request->input('coverage_external_id') : null;
        $ot->percentage_coverage_external  = (trim($request->input('percentage_coverage_external')) != '') ? $request->input('percentage_coverage_external') : null;

        $ot->numero_colores = (trim($request->input('numero_colores')) != '') ? $request->input('numero_colores') : null;
        $ot->color_1_id  = (trim($request->input('color_1_id')) != '') ? $request->input('color_1_id') : null;
        $ot->impresion_1 = (trim($request->input('impresion_1')) != '') ? $request->input('impresion_1') : null;
        $ot->color_2_id  = (trim($request->input('color_2_id')) != '') ? $request->input('color_2_id') : null;
        $ot->impresion_2 = (trim($request->input('impresion_2')) != '') ? $request->input('impresion_2') : null;
        $ot->color_3_id  = (trim($request->input('color_3_id')) != '') ? $request->input('color_3_id') : null;
        $ot->impresion_3 = (trim($request->input('impresion_3')) != '') ? $request->input('impresion_3') : null;
        $ot->color_4_id  = (trim($request->input('color_4_id')) != '') ? $request->input('color_4_id') : null;
        $ot->impresion_4 = (trim($request->input('impresion_4')) != '') ? $request->input('impresion_4') : null;
        $ot->color_5_id  = (trim($request->input('color_5_id')) != '') ? $request->input('color_5_id') : null;
        $ot->impresion_5 = (trim($request->input('impresion_5')) != '') ? $request->input('impresion_5') : null;
        $ot->color_6_id  = (trim($request->input('color_6_id')) != '') ? $request->input('color_6_id') : null;
        $ot->impresion_6 = (trim($request->input('impresion_6')) != '') ? $request->input('impresion_6') : null;
        //Se Desabilita a solicitud de correccion del Evolutivo 72 (Eliminar Barniz UV y % Impresión B. UV)
        //Utilizando los datos para este campo de los que vengan del input coverage_external_id y percentage_coverage_external
        $ot->barniz_uv               = (trim($request->input('barniz_uv')) != '') ? $request->input('barniz_uv') : null;
        $ot->porcentanje_barniz_uv   = (trim($request->input('porcentanje_barniz_uv')) != '') ? $request->input('porcentanje_barniz_uv') : null;
        //$ot->barniz_uv               = (trim($request->input('coverage_external_id')) == 4) ? 1 : null;
        //$ot->porcentanje_barniz_uv   = (trim($request->input('coverage_external_id')) == 4 && trim($request->input('percentage_coverage_external')) != '') ? $request->input('percentage_coverage_external') : null;

        $ot->color_interno           = (trim($request->input('color_1_id')) != '') ? $request->input('color_1_id') : null;
        $ot->impresion_color_interno = (trim($request->input('impresion_1')) != '') ? $request->input('impresion_1') : null;

        $ot->indicador_facturacion_diseno_grafico = (trim($request->input('indicador_facturacion_diseno_grafico')) != '') ? $request->input('indicador_facturacion_diseno_grafico') : null;

        $ot->prueba_color = (trim($request->input('prueba_color')) != '') ? $request->input('prueba_color') : null;

        $ot->pegado = (trim($request->input('pegado')) != '') ? $request->input('pegado') : null;
        $ot->cera_exterior = (trim($request->input('cera_exterior')) != '') ? $request->input('cera_exterior') : null;
        $ot->cera_interior = (trim($request->input('cera_interior')) != '') ? $request->input('cera_interior') : null;
        $ot->barniz_interior = (trim($request->input('barniz_interior')) != '') ? $request->input('barniz_interior') : null;

        // Medidas Interiores
        $ot->interno_largo = (trim($request->input('interno_largo')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('interno_largo'))) : null;
        $ot->interno_ancho = (trim($request->input('interno_ancho')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('interno_ancho'))) : null;
        $ot->interno_alto  = (trim($request->input('interno_alto')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('interno_alto'))) : null;

        // Medidas Exteriores
        $ot->externo_largo = (trim($request->input('externo_largo')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('externo_largo'))) : null;
        $ot->externo_ancho = (trim($request->input('externo_ancho')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('externo_ancho'))) : null;
        $ot->externo_alto  = (trim($request->input('externo_alto')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('externo_alto'))) : null;

        // Terminaciones
        $ot->process_id         = (trim($request->input('process_id')) != '') ? $request->input('process_id') : null;
        $ot->pegado_terminacion = (trim($request->input('pegado_terminacion')) != '') ? $request->input('pegado_terminacion') : null;
        $ot->armado_id          = (trim($request->input('armado_id')) != '') ? $request->input('armado_id') : null;
        $ot->sentido_armado = (trim($request->input('sentido_armado')) != '') ? $request->input('sentido_armado') : null;


        // Sentido de onda
        $ot->tipo_sentido_onda  = (trim($request->input('tipo_sentido_onda')) != '') ? $request->input('tipo_sentido_onda') : $ot->tipo_sentido_onda;

        // material asignado descripcion
        $ot->material_asignado    = (trim($request->input('material_asignado')) != '') ? $request->input('material_asignado') : $ot->material_asignado;
        $ot->descripcion_material = (trim($request->input('descripcion_material')) != '') ? $request->input('descripcion_material') : $ot->descripcion_material;

        // Datos para desarrollo
        $ot->peso_contenido_caja  = (trim($request->input('peso_contenido_caja')) != '') ? $request->input('peso_contenido_caja') : null;
        $ot->autosoportante       = (trim($request->input('autosoportante')) != '') ? $request->input('autosoportante') : null;
        $ot->envase_id            = (trim($request->input('envase_id')) != '') ? $request->input('envase_id') : $ot->envase_id;
        $ot->cajas_altura         = (trim($request->input('cajas_altura')) != '') ? $request->input('cajas_altura') : null;
        $ot->impresion            = (trim($request->input('impresion')) != '') ? $request->input('impresion') : $ot->impresion;
        $ot->trazabilidad            = (trim($request->input('trazabilidad')) != '') ? $request->input('trazabilidad') : $ot->trazabilidad;
        $ot->pallet_sobre_pallet  = (trim($request->input('pallet_sobre_pallet')) != '') ? $request->input('pallet_sobre_pallet') : null;
        if ($request->input('pallet_sobre_pallet') == 0 || $request->input('pallet_sobre_pallet') == null) {
            $ot->cantidad = null;
        } else {
            $ot->cantidad  = (trim($request->input('cantidad')) != '') ? $request->input('cantidad') : $ot->cantidad;
        }
        $ot->product_type_developing_id = (trim($request->input('product_type_developing_id')) != '') ? $request->input('product_type_developing_id') : null;
        $ot->food_type_id               = (trim($request->input('food_type_id')) != '') ? $request->input('food_type_id') : null;
        $ot->expected_use_id            = (trim($request->input('expected_use_id')) != '') ? $request->input('expected_use_id') : null;
        $ot->recycled_use_id            = (trim($request->input('recycled_use_id')) != '') ? $request->input('recycled_use_id') : null;
        $ot->class_substance_packed_id  = (trim($request->input('class_substance_packed_id')) != '') ? $request->input('class_substance_packed_id') : null;
        $ot->transportation_way_id      = (trim($request->input('transportation_way_id')) != '') ? $request->input('transportation_way_id') : null;
        $ot->target_market_id           = (trim($request->input('target_market_id')) != '') ? $request->input('target_market_id') : null;

        //secuencias operacionales
        $ot->so_planta_original           = (trim($request->input('sec_operacional_principal')) != '') ? $request->input('sec_operacional_principal') : null;
        $ot->so_planta_alt1           = (trim($request->input('sec_operacional_1')) != '') ? $request->input('sec_operacional_1') : null;
        $ot->so_planta_alt2           = (trim($request->input('sec_operacional_2')) != '') ? $request->input('sec_operacional_2') : null;

        //Maquila
        $ot->maquila = (trim($request->input('maquila')) != '') ? $request->input('maquila') :  null;
        $ot->maquila_servicio_id = (trim($request->input('maquila_servicio_id')) != '') ? $request->input('maquila_servicio_id') : null;

        // Observacion
        $ot->observacion = (trim($request->input('observacion')) != '') ? $request->input('observacion') : $ot->observacion;


        // Aprobacion de jefes
        // La regla de autorización es la siguiente:

        // Si son 2 o menos sin autorización
        // 3 o hasta 5 pasa por el jefe de ventas
        // De 6 al infinito, primero jefe de ventas y después jefe desarrollo
        // Al editar solo cambiamos si el numero de muestra es superior al actual
        if ($request->input('numero_muestras') != null && $numero_muestras != $request->input('numero_muestras')) {

            if ($ot->tipo_solicitud == 3 &&  $ot->numero_muestras >= 6) {
                $ot->aprobacion_jefe_desarrollo = 1;
                $ot->aprobacion_jefe_venta = 1;
            } else if ($ot->tipo_solicitud == 3 &&  $ot->numero_muestras > 2  && $ot->numero_muestras < 6) {
                $ot->aprobacion_jefe_desarrollo = 0;
                $ot->aprobacion_jefe_venta = 1;
            } else {

                $ot->aprobacion_jefe_desarrollo = 0;
                $ot->aprobacion_jefe_venta = 0;
            }
        }

        // Antecedentes Desarrollo
        if (in_array('check_correo_cliente', $request->input('checkboxes'))) $ot->ant_des_correo_cliente = 1;
        else $ot->ant_des_correo_cliente = 0;
        if (in_array('check_plano_actual', $request->input('checkboxes'))) $ot->ant_des_plano_actual = 1;
        else $ot->ant_des_plano_actual = 0;
        if (in_array('check_boceto_actual', $request->input('checkboxes'))) $ot->ant_des_boceto_actual = 1;
        else $ot->ant_des_boceto_actual = 0;
        if (in_array('check_speed', $request->input('checkboxes'))) $ot->ant_des_speed = 1;
        else $ot->ant_des_speed = 0;
        if (in_array('check_otro', $request->input('checkboxes'))) $ot->ant_des_otro = 1;
        else $ot->ant_des_otro = 0;


        if (in_array('check_vb_muestra', $request->input('checkboxes'))) $ot->ant_des_vb_muestra = 1;
        else $ot->ant_des_vb_muestra = 0;

        if (in_array('check_vb_boce', $request->input('checkboxes'))) $ot->ant_des_vb_boce = 1;
        else $ot->ant_des_vb_boce = 0;

        if (in_array('check_referencia_de', $request->input('checkboxes'))) $ot->ant_des_cj_referencia_de = 1;
        else $ot->ant_des_cj_referencia_de = 0;
        if (in_array('check_referencia_dg', $request->input('checkboxes'))) $ot->ant_des_cj_referencia_dg = 1;
        else $ot->ant_des_cj_referencia_dg = 0;
        if (in_array('check_envase_primario', $request->input('checkboxes'))) $ot->ant_des_envase_primario = 1;
        else $ot->ant_des_envase_primario = 0;
        if (in_array('check_conservar_si', $request->input('checkboxes'))) $ot->ant_des_conservar_muestra = 1;
        if (in_array('check_conservar_no', $request->input('checkboxes'))) $ot->ant_des_conservar_muestra = 0;

        if (in_array('check_armado_automatico_si', $request->input('checkboxes'))) $ot->armado_automatico = 1;
        if (in_array('check_armado_automatico_no', $request->input('checkboxes'))) $ot->armado_automatico = 0;
        //Registro si viene Archivo Adjunto de correo de Cliente Editar
        if ($request->hasfile('file_check_correo_cliente')) {
            $archivo = $request->file('file_check_correo_cliente');
            $file = new File();

            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $filename;

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
            $ot->ant_des_correo_cliente_file = '/files/' . $name . '.' . $extension;
            if ($file_check_correo_cliente_change) {
                $ot->ant_des_correo_cliente_file_date = Carbon::now();
            }
        }
        //Registro si viene Archivo Adjunto de plano actual
        if ($request->hasfile('file_check_plano_actual')) {
            $archivo = $request->file('file_check_plano_actual');
            $file = new File();

            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $filename;

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
            $ot->ant_des_plano_actual_file = '/files/' . $name . '.' . $extension;
            if ($file_check_plano_actual_change) {
                $ot->ant_des_plano_actual_file_date = Carbon::now();
            }
        }
        //Registro si viene Archivo Adjunto de boceto actual
        if ($request->hasfile('file_check_boceto_actual')) {
            $archivo = $request->file('file_check_boceto_actual');
            $file = new File();

            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $filename;

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
            $ot->ant_des_boceto_actual_file = '/files/' . $name . '.' . $extension;
            if ($file_check_boceto_actual_change) {
                $ot->ant_des_boceto_actual_file_date = Carbon::now();
            }
        }
        //Registro si viene Archivo Adjunto de Speed
        if ($request->hasfile('file_check_speed')) {
            $archivo = $request->file('file_check_speed');
            $file = new File();

            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $filename;

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
            $ot->ant_des_speed_file = '/files/' . $name . '.' . $extension;
            if ($file_check_speed_change) {
                $ot->ant_des_speed_file_date = Carbon::now();
            }
        }
        //Registro si viene Archivo Adjunto de boceto actual
        if ($request->hasfile('file_check_otro')) {
            $archivo = $request->file('file_check_otro');
            $file = new File();

            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $filename;

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
            $ot->ant_des_otro_file = '/files/' . $name . '.' . $extension;
            if ($file_check_otro_change) {
                $ot->ant_des_otro_file_date = Carbon::now();
            }
        }

        if ($request->hasfile('file_check_vb_muestra')) {
            $archivo = $request->file('file_check_vb_muestra');
            $file = new File();

            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $filename;

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
            $ot->ant_des_vb_muestra_file = '/files/' . $name . '.' . $extension;
            if ($file_check_vb_muestra_change) {
                $ot->ant_des_vb_muestra_file_date = Carbon::now();
            }
        }

        if ($request->hasfile('file_check_vb_boce')) {
            $archivo = $request->file('file_check_vb_boce');
            $file = new File();

            $filename = str_replace('%', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = pathinfo($archivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $filename;

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
            $ot->ant_des_vb_boce_file = '/files/' . $name . '.' . $extension;
            if ($file_check_vb_boce_change) {
                $ot->ant_des_vb_boce_file_date = Carbon::now();
            }
        }


        if ((string)$ot->pallet_qa_id !== (string)$request->input('pallet_qa_id')) {
            $datos_modificados['pallet_qa_id'] = [
                'texto' => 'Certificado Calidad',
                'antiguo_valor' => ['id' => $ot->pallet_qa_id, 'descripcion' => $ot->pallet_qa_id],
                'nuevo_valor' => ['id' => $request->input('pallet_qa_id'), 'descripcion' => $request->input('pallet_qa_id')]
            ];
        }
        if (!in_array('Certificado Calidad', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Certificado Calidad'];
        }

        if ((string)$ot->bulto_zunchado !== (string)$request->input('bulto_zunchado')) {
            $datos_modificados['bulto_zunchado'] = [
                'texto' => 'Bulto Zunchado',
                'antiguo_valor' => ['id' => $ot->bulto_zunchado, 'descripcion' => $ot->bulto_zunchado == 1 ? 'Si' : ($ot->bulto_zunchado == '0' ? 'No' : null)],
                'nuevo_valor' => ['id' => $request->input('bulto_zunchado'), 'descripcion' => $request->input('bulto_zunchado') == 1 ? 'Si' : ($request->input('bulto_zunchado') == '0' ? 'No' :  null)]
            ];
        }
        if (!in_array('Bulto Zunchado', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Bulto Zunchado'];
        }

        if ($ot->formato_etiqueta != $request->input('formato_etiqueta')) {
            $datos_modificados['formato_etiqueta'] = [
                'texto' => 'Formato Etiqueta',
                'antiguo_valor' => ['id' => $ot->formato_etiqueta, 'descripcion' => $ot->formato_etiqueta],
                'nuevo_valor' => ['id' => $request->input('formato_etiqueta'), 'descripcion' => $request->input('formato_etiqueta')]
            ];
        }
        if (!in_array('Formato Etiqueta', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Formato Etiqueta'];
        }

        if ($ot->etiquetas_pallet != $request->input('etiquetas_pallet')) {
            $datos_modificados['etiquetas_pallet'] = [
                'texto' => 'Etiquetas Pallet',
                'antiguo_valor' => ['id' => $ot->etiquetas_pallet, 'descripcion' => $ot->etiquetas_pallet],
                'nuevo_valor' => ['id' => $request->input('etiquetas_pallet'), 'descripcion' => $request->input('etiquetas_pallet')]
            ];
        }
        if (!in_array('Etiquetas Pallet', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Etiquetas Pallet'];
        }

        if ((string)$ot->termocontraible !== (string)$request->input('termocontraible')) {
            $datos_modificados['termocontraible'] = [
                'texto' => 'Termocontraible',
                'antiguo_valor' => ['id' => $ot->termocontraible, 'descripcion' => $ot->termocontraible == 1 ? 'Si' : ($ot->termocontraible == '0' ? 'No' : null)],
                'nuevo_valor' => ['id' => $request->input('termocontraible'), 'descripcion' => $request->input('termocontraible') == 1 ? 'Si' : ($request->input('termocontraible') == '0' ? 'No' :  null)]
            ];
        }
        if (!in_array('Termocontraible', $campos_modificados)) {
            $campos[] = ['descripcion' => 'Termocontraible'];
        }

        $ot->pallet_qa_id = (trim($request->input('pallet_qa_id')) != '') ? $request->input('pallet_qa_id') : null;
        $ot->bulto_zunchado = (trim($request->input('bulto_zunchado')) != '') ? $request->input('bulto_zunchado') : null;
        $ot->formato_etiqueta = (trim($request->input('formato_etiqueta')) != '') ? $request->input('formato_etiqueta') : null;
        $ot->etiquetas_pallet = (trim($request->input('etiquetas_pallet')) != '') ? $request->input('etiquetas_pallet') : null;
        $ot->termocontraible = (trim($request->input('termocontraible')) != '') ? $request->input('termocontraible') : null;

        // else{

        // $ot->aprobacion_jefe_desarrollo = 1;
        // $ot->aprobacion_jefe_venta = 1;
        // }
        $ot->save();

        // Si ya la ot tiene un material creado anteriormente se debe actualizar la informacion correspondiente
        // Editar Material
        // dd($ot->material_id);
        if (isset($ot->material_id)) {
            $material = Material::find($ot->material_id);
            // datos de cintas
            $material->cinta = $ot->cinta;
            $material->corte_liner = $ot->corte_liner;
            $material->tipo_cinta = $ot->tipo_cinta;
            $material->distancia_cinta_1 = $ot->distancia_cinta_1;
            $material->distancia_cinta_2 = $ot->distancia_cinta_2;
            $material->distancia_cinta_3 = $ot->distancia_cinta_3;
            $material->distancia_cinta_4 = $ot->distancia_cinta_4;
            $material->distancia_cinta_5 = $ot->distancia_cinta_5;
            $material->distancia_cinta_6 = $ot->distancia_cinta_6;
            $material->numero_colores = $ot->numero_colores;
            // caracteristicas
            $material->gramaje = $ot->gramaje != "" ? $ot->gramaje : null;
            $material->ect = $ot->ect != "" ? str_replace(',', '.', $ot->ect) : null;
            $material->flexion_aleta = $ot->flexion_aleta != "" ? $ot->flexion_aleta : null;
            $material->peso = $ot->peso != "" ?  $ot->peso : null;
            $material->fct = $ot->fct != "" ?  str_replace(',', '.', $ot->fct) : null;
            $material->cobb_interior = $ot->cobb_interior != "" ?  str_replace(',', '.', $ot->cobb_interior) : null;
            $material->cobb_exterior = $ot->cobb_exterior != "" ?  str_replace(',', '.', $ot->cobb_exterior) : null;
            $material->espesor = $ot->espesor != "" ?  str_replace(',', '.', $ot->espesor) : null;
            if (Auth()->user()->isSuperAdministrador()) {
                $material->descripcion = (trim($request->input('descripcion_material')) != '') ? $request->input('descripcion_material') : $material->descripcion;
            }
            // dd($ot->gramaje, $material);
            $material->save();
        }
        // Solo se mostrara el recordatorio cuando un diseñador guarde y fsc sea = SI
        $recordatorio_fsc = ($ot->fsc == 1 && (auth()->user()->role_id == 7 || auth()->user()->role_id == 8));

        // Creamos un estado y guarda un registro en la Management cuando edita una OT el super administrador
        if (Auth()->user()->isSuperAdministrador()) {

            //Se crea una observación automatica cuando se modifica la OT
            $gestion = new Management();
            $gestion->observacion = "Modificación en los datos de la OT";
            $gestion->management_type_id = 5; //Tipo modificacion
            $gestion->user_id = Auth()->user()->id;
            $gestion->work_order_id = $ot->id;
            $gestion->work_space_id =  7; //Area de super administrador
            $gestion->duracion_segundos = 0;
            $gestion->state_id = 19; //Estado modificacion
            $gestion->save();
        }

        if (count($datos_modificados) > 0) { //Verificamos si se cambio algun valor para guardar

            //Se guarda registro en la tabla de bitacora
            $bitacora = new BitacoraWorkOrder();
            $user_auth = Auth()->user();
            $bitacora->observacion = "Modificación de datos de OT";
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

        if (request("aplicar_mckee_value") == '1') {
            $datos_mckee = array();
            if (request("carton_id_mckee_value") != '') {
                $carton = Carton::where('active', 1)->find(request("carton_id_mckee_value"));

                $datos_mckee['carton'] = [
                    'texto' => 'Carton',
                    'valor' => ['descripcion' =>  $carton->codigo]
                ];
            }
            if (request("largo_mckee_value") != '') {
                $datos_mckee['largo'] = [
                    'texto' => 'Largo',
                    'valor' => ['descripcion' => request("largo_mckee_value")]
                ];
            }
            if (request("ancho_mckee_value") != '') {
                $datos_mckee['ancho'] = [
                    'texto' => 'Ancho',
                    'valor' => ['descripcion' => request("ancho_mckee_value")]
                ];
            }
            if (request("alto_mckee_value") != '') {
                $datos_mckee['alto'] = [
                    'texto' => 'Alto',
                    'valor' => ['descripcion' => request("alto_mckee_value")]
                ];
            }
            if (request("perimetro_mckee_value") != '') {
                $datos_mckee['perimetro'] = [
                    'texto' => 'Perimetro Persistente',
                    'valor' => ['descripcion' => request("perimetro_mckee_value")]
                ];
            }
            if (request("espesor_mckee_value") != '') {
                $datos_mckee['espesor'] = [
                    'texto' => 'Espesor',
                    'valor' => ['descripcion' => request("espesor_mckee_value")]
                ];
            }
            if (request("ect_mckee_value") != '') {
                $datos_mckee['ect'] = [
                    'texto' => 'Ect',
                    'valor' => ['descripcion' => request("ect_mckee_value")]
                ];
            }
            if (request("bct_lib_mckee_value") != '') {
                $datos_mckee['bct_lb'] = [
                    'texto' => 'Bct_lb',
                    'valor' => ['descripcion' => request("bct_lib_mckee_value")]
                ];
            }
            if (request("bct_kilos_mckee_value") != '') {
                // DATOS COMERCIALES
                $datos_mckee['bct_kilos'] = [
                    'texto' => 'Bct_kilos',
                    'valor' => ['descripcion' => request("bct_kilos_mckee_value")]
                ];
            }
            if (request("fecha_mckee_value") != '') {
                // DATOS COMERCIALES
                $datos_mckee['fecha'] = [
                    'texto' => 'Fecha',
                    'valor' => ['descripcion' => request("fecha_mckee_value")]
                ];
            }

            if (count($datos_mckee) > 0) { //Verificamos si se cambio algun valor para guardar

                //Se guarda registro en la tabla de bitacora
                $bitacora = new BitacoraWorkOrder();
                $user_auth = Auth()->user();
                $bitacora->observacion = "Aplicacion Formula Mckee";
                $bitacora->operacion = 'Mckee'; //Tipo modificacion
                $bitacora->work_order_id = $ot->id;
                $bitacora->user_id = $user_auth->id;
                $user_data = array(
                    'nombre' => $user_auth->nombre,
                    'apellido' => $user_auth->apellido,
                    'rut' => $user_auth->rut,
                    'role_id' => $user_auth->role_id,
                );
                $bitacora->user_data = json_encode($user_data, JSON_UNESCAPED_UNICODE); //Se agrega JSON_UNESCAPED_UNICODE para mantener el formato de las palabras con acentos
                $bitacora->datos_modificados = json_encode($datos_mckee, JSON_UNESCAPED_UNICODE);
                $bitacora->ip_solicitud = \Request::getClientIp(true);
                $bitacora->url = url()->full();
                $bitacora->save();
                //se guardan los nombre de los campos que tiene la OT
                //BitacoraCamposModificados::insert($campos);

            }
        }

        return redirect()->route('gestionarOt', $id)->with('success', 'Orden de Trabajo actualizada correctamente.')->with("recordatorio_fsc", $recordatorio_fsc);
    }

    public function editDescriptionOt($id, $type_edit)
    {
        $validacion_campos = 0;
        $type_edit = $type_edit;
        $ot = WorkOrder::with('subsubhierarchy.subhierarchy.hierarchy')->find($id);
        // dd($ot->area_producto_calculo);
        $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->pluck('nombre', 'id')->toArray();
        $cads = Cad::where('active', 1)->pluck('cad', 'id')->toArray();
        $canals = Canal::all()->pluck('nombre', 'id')->toArray();
        $cartons = Carton::where('active', 1)->pluck('codigo', 'id')->toArray();
        $cartons_muestra = Carton::where('carton_muestra', 1)->where('active', 1)->pluck('codigo', 'id')->toArray();
        $styles = Style::where('active', 1)->pluck('glosa', 'id')->toArray();
        $colors = Color::select(DB::raw("CONCAT(codigo,' ',descripcion) AS descripcion"), 'id')->where('active', 1)->pluck('descripcion', 'id')->toArray();
        $envases = Envase::where('active', 1)->pluck('descripcion', 'id')->toArray();
        //$hierarchies = Hierarchy::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $hierarchies = Hierarchy::where('active', 1)->orWhere('id', $ot->subsubhierarchy->subhierarchy->hierarchy->id)->pluck('descripcion', 'id')->toArray();
        $productTypes = ProductType::where('active', 1)->pluck('descripcion', 'id')->toArray();

        // $materials = Material::where('active', 1)->where('cad_id', '!=', 0)->where('status', 1)->pluck('codigo', 'id')->toArray();
        // $materials2 = Material::whereIn('active', [1])->where('cad_id', '!=', 0)->where('status', 1)->pluck('codigo', 'id')->toArray();

        $materials2 = Material::where('active', 1)->where('cad_id', '!=', 0)->where('status', 1)->pluck('codigo', 'id')->toArray();
        // $materials2 = Material::whereIn('active', [1])->where('cad_id', '!=', 0)->where('status',1)->pluck('codigo', 'id')->toArray();

        // Agregar el reference_id aunque no cumpla condiciones
        $materialsReferencia = [];

        if (!is_null($ot->reference_id) && !array_key_exists($ot->reference_id, $materials2)) {
            $materialsReferencia = Material::where('id', $ot->reference_id)
                ->pluck('codigo', 'id')
                ->toArray();
        }

        // Combinar ambos
        $materials = $materialsReferencia + $materials2;

        //$procesos = Process::where('active', 1)->orderBy("descripcion")->pluck('descripcion', 'id')->toArray();
        $procesos = Process::where('active', 1)->where('type', 'EV')->orderBy('orden', 'ASC')->pluck('descripcion', 'id')->toArray();

        $armados = Armado::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $sentidos_armado = [1 => "No aplica", 2 => "Ancho a la Derecha", 3 => "Ancho a la Izquierda", 4 => "Largo a la Izquierda",  5 => "Largo a la Derecha"];
        $subhierarchies = [];
        $subsubhierarchies = [];
        $tipos_solicitud = [1 => "Desarrollo Completo", 4 => "Cotiza sin CAD", 2 => "Cotiza con CAD", 3 => "Muestra con CAD",  5 => "Arte con Material", 6 => "Otras Solicitudes Desarrollo"];
        $org_ventas = [1 => "Nacional", 2 => "Exportación"];
        $paisReferencia = Pais::where('active', 1)->pluck('name', 'id')->toArray();
        $plantaObjetivo = Planta::pluck('nombre', 'id')->toArray();
        $palletTypes = PalletType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $recubrimiento_type = RecubrimientoType::pluck('descripcion', 'codigo')->toArray();
        $reference_type = [0 => "No", 1 => "Si"]; //Se deja el arreglo para poder mostrar el No y el SI a las OT antiguas
        $reference_type = array_merge($reference_type, ReferenceType::where('active', 1)->pluck('descripcion', 'codigo')->toArray());
        $fsc = [0 => "No", 1 => "Si"]; //Se deja el arreglo para poder mostrar el No y el SI a las OT antiguas
        $fsc = array_merge($fsc, Fsc::where('active', 1)->pluck('descripcion', 'codigo')->toArray());
        $designTypes = DesignType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $maquila_servicios = MaquilaServicio::where('active', 1)->pluck('servicio', 'id')->toArray();

        $coverageExternal = CoverageExternal::where('status', 1)->pluck('descripcion', 'id')->toArray();
        $coverageInternal = CoverageInternal::where('status', 1)->pluck('descripcion', 'id')->toArray();
        $palletQa = PalletQa::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $palletTagFormat = PalletTagFormat::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $indicaciones_especiales = IndicacionEspecial::where('client_id', $ot->client_id)->where('deleted', 0)->get();

        return view('work-orders-old.edit-description', compact(
            'ot',
            'clients',
            'cads',
            'canals',
            'cartons_muestra',
            'cartons',
            'styles',
            'colors',
            'envases',
            'materials',
            'armados',
            'sentidos_armado',
            'procesos',
            'productTypes',
            'hierarchies',
            'subhierarchies',
            'subsubhierarchies',
            'tipos_solicitud',
            'org_ventas',
            'paisReferencia',
            'plantaObjetivo',
            'palletTypes',
            'reference_type',
            'fsc',
            'recubrimiento_type',
            'type_edit',
            'designTypes',
            'maquila_servicios',
            'validacion_campos',
            'coverageExternal',
            'coverageInternal',
            'palletQa',
            'palletTagFormat',
            'indicaciones_especiales'
        ));
    }

    public function updateDescripcion(Request $request, $id)
    {
        // Se actualiza la descripcion de la OT
        $ot =  WorkOrder::find($id);

        //Buscamos primero todos los campos de la OT de la tabla
        $campos_modificados = BitacoraCamposModificados::all()->pluck('descripcion')->toArray();

        // ------- INICIO ------- Compara datos para la bitacora ( antes de que se guarden )
        $campos = array();
        $datos_modificados = array();

        if ($request->type_edit == 'description') { //Guarda descripcion

            //--- Dato de descripcion para la bitacora
            if ($ot->descripcion != $request->input('descripcion')) {
                $datos_modificados['descripcion'] = [
                    'texto' => 'Descripción',
                    'antiguo_valor' => ['id' => null, 'descripcion' => $ot->descripcion],
                    'nuevo_valor' => ['id' => null, 'descripcion' => $request->input('descripcion')]
                ];
            }
            if (!in_array('Descripción', $campos_modificados)) {
                $campos[] = ['descripcion' => 'Descripción'];
            }

            if ($ot->cad == '' && $ot->material_id == '') {
                // Si es relacionar a un cad ya creado
                $cad = Cad::where('active', 1)->find($ot->cad_id);

                //Si no tiene cad ni material asigandao todavia , Solo actualiza la descripcion de la OT
                $ot =  WorkOrder::find($id);
                $ot->descripcion = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $ot->descripcion;
                $ot->save();


                //   // Si es relacionar a un cad ya creado
                //   $cad = Cad::where('active', 1)->find($ot->cad_id);

                //   // Creacion Material
                //   $material = new Material();
                //   $material->codigo = $ot->material_code;
                //   $material->descripcion = $request->input('descripcion');
                //   $material->client_id = $ot->client_id;
                //   $material->vendedor_id = $ot->creador_id;
                //   $material->carton_id = $ot->carton_id;
                //   $material->product_type_id = $ot->product_type_id;
                //   $material->style_id = $ot->style_id;
                //   $material->numero_colores = $ot->numero_colores;
                //   $material->cad_id = $cad->id;
                //   $material->pallet_type_id = $ot->pallet_type_id;
                //   $material->pallet_box_quantity = $ot->cajas_por_pallet;
                //   $material->placas_por_pallet = $ot->placas_por_pallet;
                //   $material->pallet_patron_id = $ot->pallet_patron_id;
                //   $material->patron_zuncho_pallet = $ot->patron_zuncho;
                //   $material->pallet_protection_id = $ot->pallet_protection_id;
                //   $material->boxes_per_package = $ot->pallet_box_quantity_id;
                //   $material->patron_zuncho_paquete = $ot->patron_zuncho_paquete;
                //   $material->paquetes_por_unitizado = $ot->paquetes_por_unitizado;
                //   $material->unitizado_por_pallet = $ot->unitizado_por_pallet;
                //   $material->pallet_tag_format_id = $ot->pallet_tag_format_id;
                //   $material->fecha_creacion = Carbon::now();
                //   $material->creador_id = Auth()->user()->id;
                //   $material->pallet_qa_id = $ot->pallet_qa_id;
                //   $material->numero_etiquetas = $ot->numero_etiquetas;
                //   $material->bct_min_lb = $ot->bct_min_lb;
                //   $material->bct_min_kg = $ot->bct_min_kg;
                //   $material->pallet_treatment = $ot->pallet_treatment;
                //   $material->sap_hiearchy_id = $ot->subsubhiearchy_id ? $ot->subsubhierarchy->jerarquia_sap : null;
                //   $material->tipo_camion = $ot->tipo_camion;
                //   $material->restriccion_especial = $ot->restriccion_especial;
                //   $material->horario_recepcion = $ot->horario_recepcion;
                //   $material->codigo_producto_cliente = $ot->codigo_producto_cliente;
                //   $material->etiquetas_dsc = $ot->etiquetas_dsc;
                //   $material->orientacion_placa = $ot->orientacion_placa;
                //   $material->recubrimiento = $ot->recubrimiento;
                //   $material->work_order_id = $ot->id;
                //   // datos de cintas
                //   $material->cinta = $ot->cinta;
                //   $material->corte_liner = $ot->corte_liner;
                //   $material->tipo_cinta = $ot->tipo_cinta;
                //   $material->distancia_cinta_1 = $ot->distancia_cinta_1;
                //   $material->distancia_cinta_2 = $ot->distancia_cinta_2;
                //   $material->distancia_cinta_3 = $ot->distancia_cinta_3;
                //   $material->distancia_cinta_4 = $ot->distancia_cinta_4;
                //   $material->distancia_cinta_5 = $ot->distancia_cinta_5;
                //   $material->distancia_cinta_6 = $ot->distancia_cinta_6;

                //   // Datos caracteristicas
                //   $material->gramaje = $ot->gramaje != "" ? str_replace(',', '.', $ot->gramaje) : null;
                //   $material->ect = $ot->ect != "" ? str_replace(',', '.', $ot->ect) : null;
                //   $material->flexion_aleta = $ot->flexion_aleta != "" ?  str_replace(',', '.', $ot->flexion_aleta) : null;
                //   $material->peso = $ot->peso != "" ?  str_replace(',', '.', $ot->peso) : null;
                //   $material->incision_rayado_longitudinal = $ot->incision_rayado_longitudinal  != "" ?  str_replace(',', '.', $ot->incision_rayado_longitudinal) : null;
                //   $material->incision_rayado_vertical = $ot->incision_rayado_vertical  != "" ?  str_replace(',', '.', $ot->incision_rayado_vertical) : null;
                //   $material->fct = $ot->fct != "" ?  str_replace(',', '.', $ot->fct) : null;
                //   $material->cobb_interior = $ot->cobb_interior != "" ?  str_replace(',', '.', $ot->cobb_interior) : null;
                //   $material->cobb_exterior = $ot->cobb_exterior != "" ?  str_replace(',', '.', $ot->cobb_exterior) : null;
                //   $material->espesor = $ot->espesor != "" ?  str_replace(',', '.', $ot->espesor) : null;

                //   $material->save();

                //   // Asignar cad y material a la OT
                //   $ot->cad = $cad->cad;
                //   $ot->cad_id = $cad->id;
                //   $ot->material_id = $material->id;
                //   $ot->material_asignado = $ot->material_code;
                //   $ot->descripcion_material = $request->input('descripcion');
                //   $ot->descripcion = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $ot->descripcion;

                //   $ot->save();
            } else {
                //Si ya esta el material y el cad registrado, se actualiza la descripcion la descripcion en ambos
                $ot =  WorkOrder::find($id);
                $ot->descripcion = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $ot->descripcion;
                $ot->descripcion_material = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $ot->descripcion;
                $ot->save();

                $material =  Material::find($ot->material_id);
                $material->descripcion = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $material->descripcion;
                $material->save();
            }
        } else { //Guarda OC

            //--- Dato de OC para la bitacora
            if ((string)$ot->oc !== (string)$request->input('oc')) {
                $datos_modificados['oc'] = [
                    'texto' => 'OC',
                    'antiguo_valor' => ['id' => $ot->oc, 'descripcion' => $ot->oc == 1 ? 'Si' : ($ot->oc == '0' ? 'No' : null)],
                    'nuevo_valor' => ['id' => $request->input('oc'), 'descripcion' => $request->input('oc') == 1 ? 'Si' : ($request->input('oc') == '0' ? 'No' :  null)]
                ];
            }
            if (!in_array('OC', $campos_modificados)) {
                $campos[] = ['descripcion' => 'OC'];
            }

            $ot =  WorkOrder::find($id);
            $ot->oc = $request->input('oc');
            $ot->save();

            //Se crea una observación automatica cuando se modifica la OC
            $gestion = new Management();
            $gestion->observacion = "Modificación de orden de compra";
            $gestion->management_type_id = 5; //Tipo modificacion OC
            $gestion->user_id = Auth()->user()->id;
            $gestion->work_order_id = $ot->id;
            $gestion->work_space_id =  1;
            $gestion->duracion_segundos = 0;
            $gestion->state_id = 19; //Estado modificacion OC
            $gestion->save();
        }

        // Solo se mostrara el recordatorio cuando un diseñador guarde y fsc sea = SI
        $recordatorio_fsc = ($ot->fsc == 1 && (auth()->user()->role_id == 7 || auth()->user()->role_id == 8));

        if (count($datos_modificados) > 0) { //Verificamos si se cambio algun valor para guardar

            //Se guarda registro en la tabla de bitacora
            $bitacora = new BitacoraWorkOrder();
            $user_auth = Auth()->user();
            $bitacora->observacion = "Modificación de datos de OT";
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

        if (request("aplicar_mckee_value") == '1') {
            $datos_mckee = array();
            if (request("carton_id_mckee_value") != '') {
                $carton = Carton::where('active', 1)->find(request("carton_id_mckee_value"));

                $datos_mckee['carton'] = [
                    'texto' => 'Carton',
                    'valor' => ['descripcion' =>  $carton->codigo]
                ];
            }
            if (request("largo_mckee_value") != '') {
                $datos_mckee['largo'] = [
                    'texto' => 'Largo',
                    'valor' => ['descripcion' => request("largo_mckee_value")]
                ];
            }
            if (request("ancho_mckee_value") != '') {
                $datos_mckee['ancho'] = [
                    'texto' => 'Ancho',
                    'valor' => ['descripcion' => request("ancho_mckee_value")]
                ];
            }
            if (request("alto_mckee_value") != '') {
                $datos_mckee['alto'] = [
                    'texto' => 'Alto',
                    'valor' => ['descripcion' => request("alto_mckee_value")]
                ];
            }
            if (request("perimetro_mckee_value") != '') {
                $datos_mckee['perimetro'] = [
                    'texto' => 'Perimetro Persistente',
                    'valor' => ['descripcion' => request("perimetro_mckee_value")]
                ];
            }
            if (request("espesor_mckee_value") != '') {
                $datos_mckee['espesor'] = [
                    'texto' => 'Espesor',
                    'valor' => ['descripcion' => request("espesor_mckee_value")]
                ];
            }
            if (request("ect_mckee_value") != '') {
                $datos_mckee['ect'] = [
                    'texto' => 'Ect',
                    'valor' => ['descripcion' => request("ect_mckee_value")]
                ];
            }
            if (request("bct_lib_mckee_value") != '') {
                $datos_mckee['bct_lb'] = [
                    'texto' => 'Bct_lb',
                    'valor' => ['descripcion' => request("bct_lib_mckee_value")]
                ];
            }
            if (request("bct_kilos_mckee_value") != '') {
                // DATOS COMERCIALES
                $datos_mckee['bct_kilos'] = [
                    'texto' => 'Bct_kilos',
                    'valor' => ['descripcion' => request("bct_kilos_mckee_value")]
                ];
            }
            if (request("fecha_mckee_value") != '') {
                // DATOS COMERCIALES
                $datos_mckee['fecha'] = [
                    'texto' => 'Fecha',
                    'valor' => ['descripcion' => request("fecha_mckee_value")]
                ];
            }

            if (count($datos_mckee) > 0) { //Verificamos si se cambio algun valor para guardar

                //Se guarda registro en la tabla de bitacora
                $bitacora = new BitacoraWorkOrder();
                $user_auth = Auth()->user();
                $bitacora->observacion = "Aplicacion Formula Mckee";
                $bitacora->operacion = 'Mckee'; //Tipo modificacion
                $bitacora->work_order_id = $ot->id;
                $bitacora->user_id = $user_auth->id;
                $user_data = array(
                    'nombre' => $user_auth->nombre,
                    'apellido' => $user_auth->apellido,
                    'rut' => $user_auth->rut,
                    'role_id' => $user_auth->role_id,
                );
                $bitacora->user_data = json_encode($user_data, JSON_UNESCAPED_UNICODE); //Se agrega JSON_UNESCAPED_UNICODE para mantener el formato de las palabras con acentos
                $bitacora->datos_modificados = json_encode($datos_mckee, JSON_UNESCAPED_UNICODE);
                $bitacora->ip_solicitud = \Request::getClientIp(true);
                $bitacora->url = url()->full();
                $bitacora->save();
                //se guardan los nombre de los campos que tiene la OT
                //BitacoraCamposModificados::insert($campos);

            }
        }

        return redirect()->route('gestionarOt', $id)->with('success', 'Orden de Trabajo actualizada correctamente.')->with("recordatorio_fsc", $recordatorio_fsc);
    }
    public function getCad()
    {
        // dd(request()->all());
        if (!empty($_GET['cad_id'])) {
            // return $equipo_id;
            $cad = Cad::where('active', 1)->find($_GET['cad_id']);
            $cad->area_producto = number_format_unlimited_precision($cad->area_producto);
            $cad->recorte_adicional = number_format_unlimited_precision($cad->recorte_adicional, ',', '.', 4);

            $cad->interno_largo = number_format_unlimited_precision(str_replace(',', '.', $cad->interno_largo));
            $cad->interno_ancho = number_format_unlimited_precision(str_replace(',', '.', $cad->interno_ancho));
            $cad->interno_alto = number_format_unlimited_precision(str_replace(',', '.', $cad->interno_alto));

            $cad->externo_largo = number_format_unlimited_precision(str_replace(',', '.', $cad->externo_largo));
            $cad->externo_ancho = number_format_unlimited_precision(str_replace(',', '.', $cad->externo_ancho));
            $cad->externo_alto = number_format_unlimited_precision(str_replace(',', '.', $cad->externo_alto));
            return $cad;
        }
        return "";
    }

    public function getCadByMaterial()
    {
        // dd(request()->all());
        if (!empty($_GET['material_id'])) {

            $material = Material::find($_GET['material_id']);
            return $material;
        }
        return "";
    }

    public function getCartonColor()
    {
        //Buscamos los cartones dependiendo del color
        if (!empty($_GET['carton_color'])) {

            $color = $_GET['carton_color'] == 1 ? 'CAFE' : 'BLANCO';

            $carton_calor = Carton::where('active', 1)->where('color_tapa_exterior', $color)->pluck('codigo', 'id')->toArray();
            $html = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimple($carton_calor, 'carton_id');

            return $html;
        }

        //Sino mostramos todos
        $carton_calor = Carton::where('active', 1)->pluck('codigo', 'id')->toArray();
        $html = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimple($carton_calor, 'carton_id');

        return $html;
    }

    public function getCarton()
    {
        // dd(request()->all());
        if (!empty($_GET['carton_id'])) {
            // return $equipo_id;
            $carton = Carton::where('active', 1)->find($_GET['carton_id']);
            return $carton;
        }
        return "";
    }

    public function getMaquilaServicio()
    {
        if (!empty($_GET['maquila_servicio_id'])) {

            $maquila_servicio = MaquilaServicio::where('active', 1)->find($_GET['maquila_servicio_id']);

            return $maquila_servicio;
        }
        return "";
    }

    public function getDesignType()
    {
        if (!empty($_GET['design_type_id'])) {
            $design_type = DesignType::where('active', 1)->find($_GET['design_type_id']);

            return $design_type;
        }
        return "";
    }

    public function getPlantaCarton()
    {
        //Si la OT ya tiene una planta seleccionanda se envia ese ID para que se mantega seleccionada en el select
        if (empty($_GET['select_planta_id'])) {
            $valor_planta_id = '';
        } else {
            $valor_planta_id = $_GET['select_planta_id'];
        }

        if (!empty($_GET['planta_id'])) {

            $plantaId = Planta::whereIn('id', explode(',', $_GET['planta_id']))->pluck('nombre', 'id')->toArray();

            $html = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimplePlanta($plantaId, 'planta_id', '', $valor_planta_id);

            return $html;
        }
        return "";
    }
    // Crear Cad y material para ot
    public function createCadMaterial(Request $request, $idOt)
    {
        // dd(request()->all());
        $request->validate([
            'cad' => 'required_without:cad_id',
            'cad_id' => 'required_without:cad',
            'descripcion' => 'required',
            'material' => 'required',
        ]);

        $ot = WorkOrder::find($idOt);

        $errores = [];
        // \Log::info($ot);

        if (($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 5 || $ot->tipo_solicitud == 7) && (!empty($ot->process_id))) {
            if ($ot->process_id == 1 || $ot->process_id == 5) { //Validacion para cuando el proceso sea Flexo o Flexo Con matriz parcial
                if ($ot->anchura_hm != '') {
                    $suma_rayado =  $ot->rayado_c1r1 + $ot->rayado_r1_r2 + $ot->rayado_r2_c2;

                    if ($ot->anchura_hm != $suma_rayado) {
                        $errores[] = 1;
                    }
                }
            }
        }

        if (($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 5 || $ot->tipo_solicitud == 7) && ($ot->interno_largo > $ot->externo_largo) || ($ot->interno_ancho > $ot->externo_ancho) || ($ot->interno_alto  > $ot->externo_alto)) {
            if (empty($ot->cad_id)) {
                $errores[] = 2;
            }
        }

        if (($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 5 || $ot->tipo_solicitud == 7) && (($ot->area_hc < $ot->area_hm) || ($ot->area_hm < $ot->area_producto))) {
            $errores[] = 3;
        }

        if (($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 5 || $ot->tipo_solicitud == 7) && ($ot->recorteCaracteristico != "N/A")) {

            $recorte_caracteristico = str_replace(",", ".", str_replace('.', '', number_format_unlimited_precision($ot->recorteCaracteristico, ",", ".", 7)));

            if (($recorte_caracteristico > $ot->area_hm) || ($ot->recorte_adicional > $ot->area_hm)) {
                $errores[] = 4;
            }
        }

        if (!empty($errores)) {
            //redirecciono a editar la OT con los errores encontrados
            return redirect()->route('editOt', array($idOt, 'validacion_campos' => implode(',', $errores)));
        } else {

            // Si es creacion de cad
            if (request('cad')) {
                // Creacion CAD
                $cad = new Cad();
                $cad->cad = request('cad');
                $cad->externo_largo = $ot->externo_largo;
                $cad->externo_ancho = $ot->externo_ancho;
                $cad->externo_alto = $ot->externo_alto;
                $cad->interno_largo = $ot->interno_largo;
                $cad->interno_ancho = $ot->interno_ancho;
                $cad->interno_alto = $ot->interno_alto;
                $cad->area_producto = ($ot->area_producto_calculo != "N/A") ? str_replace(",", ".", str_replace('.', '', number_format_unlimited_precision($ot->area_producto_calculo))) : null;
                $cad->recorte_adicional = ($ot->recorte_adicional) ? str_replace(",", ".", str_replace('.', '', number_format_unlimited_precision($ot->recorte_adicional, ',', '.', '4'))) : null;
                // dd($cad->recorte_adicional, $ot->recorte_adicional);
                $cad->largura_hm = $ot->largura_hm;
                $cad->anchura_hm = $ot->anchura_hm;
                $cad->largura_hc = ($ot->larguraHc != "N/A") ? $ot->larguraHc : null;
                $cad->anchura_hc = ($ot->anchuraHc != "N/A") ? $ot->anchuraHc : null;
                // CORREGIR
                $cad->area_hm = ($ot->areaHm != "N/A") ? str_replace(",", ".", str_replace('.', '', number_format_unlimited_precision($ot->areaHm, ",", ".", 6))) : null;
                // CORREGIR
                $cad->rayado_c1r1 =  trim($ot->rayado_c1r1 != "") ? str_replace(",", ".", str_replace('.', '', ($ot->rayado_c1r1))) : null;
                $cad->rayado_r1_r2 =  trim($ot->rayado_r1_r2 != "") ? str_replace(",", ".", str_replace('.', '', ($ot->rayado_r1_r2))) : null;
                $cad->rayado_r2_c2 = trim($ot->rayado_r2_c2 != "") ? str_replace(",", ".", str_replace('.', '', ($ot->rayado_r2_c2))) : null;
                // CORREGIR
                $cad->recorte_caracteristico = ($ot->recorteCaracteristico != "N/A") ? str_replace(",", ".", str_replace('.', '', number_format_unlimited_precision($ot->recorteCaracteristico, ",", ".", 7))) : null;
                // CORREGIR
                // $cad->recorte_adicional = ($ot->recorteAdicional != "N/A") ? str_replace(",", ".", str_replace('.', '', number_format_unlimited_precision($ot->recorteAdicional))) : null;
                $cad->veces_item = $ot->veces_item;
                // dd($cad->recorte_caracteristico);
                // dd($cad,$cad->recorte_caracteristico);
                $cad->save();
            } else if ((request('cad_id'))) {

                // Si es relacionar a un cad ya creado
                $cad = Cad::where('active', 1)->find(request('cad_id'));
                // dd($cad);
            }
            // dd(request()->all());


            // $proximoCodigoMaterial = CodigoMaterial::where("active", 1)->first();
            // $proximoCodigoMaterial->fecha_uso = now();
            // $proximoCodigoMaterial->active = 0;
            // $proximoCodigoMaterial->save();
            // Creacion Material
            $material = new Material();
            $material->codigo = request('material');
            $material->descripcion = request('descripcion');
            $material->client_id = $ot->client_id;
            $material->vendedor_id = $ot->creador_id;
            $material->carton_id = $ot->carton_id;
            $material->product_type_id = $ot->product_type_id;
            $material->style_id = $ot->style_id;
            $material->numero_colores = $ot->numero_colores;
            $material->cad_id = $cad->id;
            $material->pallet_type_id = $ot->pallet_type_id;
            $material->pallet_box_quantity = $ot->cajas_por_pallet;
            $material->placas_por_pallet = $ot->placas_por_pallet;
            $material->pallet_patron_id = $ot->pallet_patron_id;
            $material->patron_zuncho_pallet = $ot->patron_zuncho;
            $material->pallet_protection_id = $ot->pallet_protection_id;
            $material->boxes_per_package = $ot->pallet_box_quantity_id;
            $material->patron_zuncho_paquete = $ot->patron_zuncho_paquete;
            $material->paquetes_por_unitizado = $ot->paquetes_por_unitizado;
            $material->unitizado_por_pallet = $ot->unitizado_por_pallet;
            $material->pallet_tag_format_id = $ot->pallet_tag_format_id;
            $material->fecha_creacion = Carbon::now();
            $material->creador_id = Auth()->user()->id;
            $material->pallet_qa_id = $ot->pallet_qa_id;
            $material->numero_etiquetas = $ot->numero_etiquetas;
            $material->bct_min_lb = $ot->bct_min_lb;
            $material->bct_min_kg = $ot->bct_min_kg;
            $material->pallet_treatment = $ot->pallet_treatment;
            $material->sap_hiearchy_id = $ot->subsubhiearchy_id ? $ot->subsubhierarchy->jerarquia_sap : null;
            $material->tipo_camion = $ot->tipo_camion;
            $material->restriccion_especial = $ot->restriccion_especial;
            $material->horario_recepcion = $ot->horario_recepcion;
            $material->codigo_producto_cliente = $ot->codigo_producto_cliente;
            $material->etiquetas_dsc = $ot->etiquetas_dsc;
            $material->orientacion_placa = $ot->orientacion_placa;
            $material->recubrimiento = $ot->recubrimiento;
            $material->work_order_id = $ot->id;
            // datos de cintas
            $material->cinta = $ot->cinta;
            $material->corte_liner = $ot->corte_liner;
            $material->tipo_cinta = $ot->tipo_cinta;
            $material->distancia_cinta_1 = $ot->distancia_cinta_1;
            $material->distancia_cinta_2 = $ot->distancia_cinta_2;
            $material->distancia_cinta_3 = $ot->distancia_cinta_3;
            $material->distancia_cinta_4 = $ot->distancia_cinta_4;
            $material->distancia_cinta_5 = $ot->distancia_cinta_5;
            $material->distancia_cinta_6 = $ot->distancia_cinta_6;

            // Datos caracteristicas
            $material->gramaje = $ot->gramaje != "" ? $ot->gramaje : null;
            $material->ect = $ot->ect != "" ? str_replace(',', '.', $ot->ect) : null;
            $material->flexion_aleta = $ot->flexion_aleta != "" ?  $ot->flexion_aleta : null;
            $material->peso = $ot->peso != "" ?  $ot->peso : null;
            $material->incision_rayado_longitudinal = $ot->incision_rayado_longitudinal  != "" ?  $ot->incision_rayado_longitudinal : null;
            $material->incision_rayado_vertical = $ot->incision_rayado_vertical  != "" ?  $ot->incision_rayado_vertical : null;
            $material->fct = $ot->fct != "" ?  str_replace(',', '.', $ot->fct) : null;
            $material->cobb_interior = $ot->cobb_interior != "" ?  str_replace(',', '.', $ot->cobb_interior) : null;
            $material->cobb_exterior = $ot->cobb_exterior != "" ?  str_replace(',', '.', $ot->cobb_exterior) : null;
            $material->espesor = $ot->espesor != "" ?  str_replace(',', '.', $ot->espesor) : null;

            $material->active = 2;
            // dd($material);
            $material->save();

            // Asignar cad y material a la OT
            $ot->cad = $cad->cad;
            $ot->cad_id = $cad->id;
            $ot->material_id = $material->id;
            $ot->material_asignado = request('material');
            $ot->descripcion_material = request('descripcion');
            // dd($ot);
            $ot->save();
            return redirect()->route('gestionarOt', $idOt)->with('success', 'CAD y Material creados correctamente.');
        }
    }

    // Crear Cad y material para ot
    public function createCodigoMaterial(Request $request, $idOt)
    {
        // dd(request()->all());
        $request->validate([
            'codigo_material' => 'required',
            'sufijo_id' => 'required',
            'prefijo_ot' => 'required',
            // 'descripcion' => 'required',
        ]);

        // dd(request()->all());

        $ot = WorkOrder::find($idOt);
        $ot->material->codigo = request()->prefijo_ot . request()->codigo_material . "-" . request()->sufijo_id;

        if ($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 7) {
            $ot->material->descripcion = $ot->descripcion_material;
        } elseif ($ot->tipo_solicitud == 5) {
            $ot->material->descripcion = request()->descripcion;
            $ot->descripcion_material = request()->descripcion;
        }
        $ot->material_asignado = request()->prefijo_ot . request()->codigo_material . "-" . request()->sufijo_id;
        $ot->codigo_sap_final = 1;
        $ot->material->save();
        $ot->save();

        // Por cada prefijo debemos clonar el material principal y cambiar codigo
        if (isset(request()->prefijo)) {

            foreach (request()->prefijo as $prefijo) {
                $codigo = $prefijo . request()->codigo_material . "-" . request()->sufijo_id;
                $newMaterial = $ot->material->replicate();
                $newMaterial->codigo = $codigo;
                if ($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 7) {
                    $newMaterial->descripcion = $ot->descripcion_material;
                } elseif ($ot->tipo_solicitud == 5) {

                    $newMaterial->descripcion = request()->descripcion;
                }
                $newMaterial->save();
            }
        }
        return redirect()->route('gestionarOt', $idOt)->with('success', 'Codigo de Material editado correctamente.');
    }

    public function listadoAprobacion()
    {
        $query = WorkOrder::with(
            'canal',
            'client',
            'creador',
            'productType',
            "ultimoCambioEstado.area",
            "vendedorAsignado.user",
            "ingenieroAsignado.user",
            "diseñadorAsignado.user",
            "catalogadorAsignado.user",
            "users"
        );
        if (auth()->user()->isJefeVenta()) {
            $query =  $query->where('aprobacion_jefe_venta', 1);
        } elseif (auth()->user()->isJefeDesarrollo()) {
            $query =  $query->where('aprobacion_jefe_venta', 2)->where('aprobacion_jefe_desarrollo', 1);
        } else {
            $query = $query->where('id', '>=', 1);
        }

        $ots = $query->paginate(20);
        return view('work-orders-old.listado-aprobacion', compact('ots'));
    }

    public function aprobarOt($id)
    {
        if (auth()->user()->isJefeVenta()) {
            WorkOrder::findOrFail($id)->update(['aprobacion_jefe_venta' => 2]);
        } else if (auth()->user()->isJefeDesarrollo()) {
            WorkOrder::findOrFail($id)->update(['aprobacion_jefe_desarrollo' => 2]);
        }
        return redirect()->route('listadoAprobacion')->with('success', 'Órden de Trabajo Aprobada correctamente.');
    }

    public function rechazarOt($id)
    {
        if (auth()->user()->isJefeVenta()) {
            WorkOrder::findOrFail($id)->update(['aprobacion_jefe_venta' => 3]);
        } else if (auth()->user()->isJefeDesarrollo()) {
            WorkOrder::findOrFail($id)->update(['aprobacion_jefe_desarrollo' => 3]);
        }
        return redirect()->route('listadoAprobacion')->with('success', 'Órden de Trabajo Rechazada correctamente.');
    }


    public function modalOT(Request $request)
    {
        // dd(request()->all());
        if (!is_null(request()->input('ot_id'))) {
            $ot = WorkOrder::with('subsubhierarchy.subhierarchy.hierarchy')->find(request()->input('ot_id'));
            $tipo = "edit";
            return view('work-orders-old.modal-ot', compact('ot', "tipo"));
        } else return "Problema al encontrar contrato";
    }

    //-----------------------Cotizar multiples OT

    public function filtroMultiplesOt($query)
    {

        if (!is_null(request()->query('vendedor_id'))) {
            $query = $query->whereIn('creador_id', request()->query('vendedor_id'));
        } else if (Auth()->user()->isVendedor()) {
            $query = $query->where('creador_id', auth()->user()->id);
        }

        // Calculo total de tiempo en area de venta
        $query = $query->withCount([
            'gestiones AS tiempo_venta' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 1)->where('mostrar', 1);
            }
        ]);
        // Calculo total de tiempo en area de desarrollo
        $query = $query->withCount([
            'gestiones AS tiempo_desarrollo' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 2)->where('mostrar', 1);
            }
        ]);
        // Calculo total de tiempo en area de diseño
        $query = $query->withCount([
            'gestiones AS tiempo_diseño' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 3)->where('mostrar', 1);
            }
        ]);
        // Calculo total de tiempo en area de catalogacion
        $query = $query->withCount([
            'gestiones AS tiempo_catalogacion' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 4)->where('mostrar', 1);
            }
        ]);
        // Calculo total de tiempo en area de precatalogacion
        $query = $query->withCount([
            'gestiones AS tiempo_precatalogacion' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 5)->where('mostrar', 1);
            }
        ]);
        // Calculo total de tiempo en area de sala muestra
        $query = $query->withCount([
            'gestiones AS tiempo_muestra' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('work_space_id', 6)->where('mostrar', 1);
            }
        ]);
        // Calculo total de tiempo
        $query = $query->withCount([
            'gestiones AS tiempo_total' => function ($q) {
                $q->select(DB::raw("SUM(duracion_segundos) as tiempo_total"))->where('management_type_id', 1)->where('mostrar', 1);
            }
        ]);
        // Por defecto filtra por todos los estados activos
        if (is_null(request()->input('estado_id'))) {
            $estados_activos = [1, 2, 3, 4, 5, 6, 7, 10, 12, 14, 15, 16, 17, 18];
            if (auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras()) $estados_activos = [17];
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
        } else {
            $estados = request()->query('estado_id');
            $query = $query->join('managements', 'work_orders.id', 'managements.work_order_id')
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


        return $query;
    }
    public function cotizarMultiplesOt()
    {
        //filters:
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
        $query = $this->filtro($query);
        // Filtro por fechas
        // Sin fechas
        if (is_null(request()->input('date_desde')) && is_null(request()->input('date_hasta'))) {
            // Si el usuario es del sala de muestra se ordena por tiempo en sala muestra
            if ((Auth()->user()->isJefeMuestras() || Auth()->user()->isTecnicoMuestras())) {
                $ots = $query->get()->map(function ($ot) {
                    $ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaSalaMuestra($ot->tiempo_muestra, 6), 1);
                    return $ot;
                })->sortByDesc("dias_trabajados_muestra")->paginate(20);
            } else {
                $ots = $query->orderBy("id", "desc")->paginate(20);
            }
            // dd($ots);
        }
        // Solo viene la fecha hasta
        else if (is_null(request()->input('date_desde')) && !is_null(request()->input('date_hasta'))) {

            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
            // Si el usuario es del sala de muestra se ordena por tiempo en sala muestra
            if ((Auth()->user()->isJefeMuestras() || Auth()->user()->isTecnicoMuestras())) {
                $ots = $query->whereDate('work_orders.created_at', '<=', $toDate)->get()->map(function ($ot) {
                    $ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaSalaMuestra($ot->tiempo_muestra, 6), 1);
                    return $ot;
                })->sortByDesc("dias_trabajados_muestra")->paginate(20);
            } else {
                // $ots = $query->orderBy("id", "desc")->paginate(20);
                $ots = $query->whereDate('work_orders.created_at', '<=', $toDate)->orderBy("id", "desc")->paginate(20);
            }
        } // Solo viene la fecha desde
        else if (!is_null(request()->input('date_desde')) && is_null(request()->input('date_hasta'))) {

            $fromDate = Carbon::createFromFormat('d/m/Y', request()->input('date_desde'))->toDateString();
            if ((Auth()->user()->isJefeMuestras() || Auth()->user()->isTecnicoMuestras())) {
                $ots = $query->get()->map(function ($ot) {
                    $ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaSalaMuestra($ot->tiempo_muestra, 6), 1);
                    return $ot;
                })->sortByDesc("dias_trabajados_muestra")->paginate(20);;
            } else {
                // $ots = $query->orderBy("id", "desc")->paginate(20);
                $ots = $query->whereDate('work_orders.created_at', '>=', $fromDate)->orderBy("id", "desc")->paginate(20);
            }
        } // vienen ambas fechas
        else {
            $fromDate = Carbon::createFromFormat('d/m/Y', request()->input('date_desde'))->toDateString();
            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
            if ((Auth()->user()->isJefeMuestras() || Auth()->user()->isTecnicoMuestras())) {
                $ots = $query->get()->map(function ($ot) {
                    $ot->dias_trabajados_muestra = round($ot->present()->diasTrabajadosPorAreaSalaMuestra($ot->tiempo_muestra, 6), 1);
                    return $ot;
                })->sortByDesc("dias_trabajados_muestra")->paginate(20);
            } else {
                // $ots = $query->orderBy("id", "desc")->paginate(20);
                $ots = $query->whereBetween('work_orders.created_at', [$fromDate, $toDate])->orderBy("id", "desc")->paginate(20);
            }
        }
        // dd($ots);

        $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4])->get();
        $vendedores->map(function ($vendedor) {
            $vendedor->vendedor_id = $vendedor->id;
        });
        $responsables = [];

        if (Auth()->user()->isIngeniero() ||  Auth()->user()->isDiseñador() || Auth()->user()->isCatalogador()) {
            $responsables = User::where('active', 1)->where('role_id', auth()->user()->role_id)->get();
            $responsables->map(function ($responsable) {
                $responsable->responsable_id = $responsable->id;
            });
        }

        $estados = States::all();
        $estados->map(function ($estado) {
            $estado->estado_id = $estado->id;
        });

        $areas = WorkSpace::all();
        $areas->map(function ($area) {
            $area->area_id = $area->id;
        });

        $clients = Client::whereHas('ots')->where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->get();
        $clients->map(function ($client) {
            $client->client_id = $client->id;
        });

        $canals = Canal::all();
        $canals->map(function ($canal) {
            $canal->canal_id = $canal->id;
        });


        // dd($ots[0]->vendedorAsignado->user);
        // $ots = WorkOrder::with("ultimoCambioEstado")->get();
        // return response()->json($ots);
        // $ots

        return view('work-orders-old.cotizar-multiples-ot', compact('ots', 'vendedores', 'responsables', 'clients', 'canals', 'estados', 'areas'));
    }

    public function getAplicarMckee()
    {
        //dd(request()->all());

        // return $equipo_id;



        $datos_insertados = array();
        if (!empty($_GET['carton'])) {
            $carton = Carton::where('active', 1)->find($_GET['carton']);
            // DATOS COMERCIALES
            $datos_insertados['carton'] = [
                'texto' => 'Carton',
                'valor' => $carton->codigo
            ];
        }
        if (!empty($_GET['largo'])) {
            // DATOS COMERCIALES
            $datos_insertados['largo'] = [
                'texto' => 'Largo',
                'valor' => $_GET['largo']
            ];
        }
        if (!empty($_GET['ancho'])) {
            // DATOS COMERCIALES
            $datos_insertados['ancho'] = [
                'texto' => 'Ancho',
                'valor' => $_GET['ancho']
            ];
        }
        if (!empty($_GET['alto'])) {
            // DATOS COMERCIALES
            $datos_insertados['alto'] = [
                'texto' => 'Alto',
                'valor' => $_GET['alto']
            ];
        }
        if (!empty($_GET['perimetro'])) {
            // DATOS COMERCIALES
            $datos_insertados['perimetro'] = [
                'texto' => 'Perimetro',
                'valor' => $_GET['perimetro']
            ];
        }
        if (!empty($_GET['espesor'])) {
            // DATOS COMERCIALES
            $datos_insertados['espesor'] = [
                'texto' => 'Espesor',
                'valor' => $_GET['espesor']
            ];
        }
        if (!empty($_GET['ect'])) {
            // DATOS COMERCIALES
            $datos_insertados['ect'] = [
                'texto' => 'Ect',
                'valor' => $_GET['ect']
            ];
        }
        if (!empty($_GET['bct_lb'])) {
            // DATOS COMERCIALES
            $datos_insertados['bct_lb'] = [
                'texto' => 'Bct_lb',
                'valor' => $_GET['bct_lb']
            ];
        }
        if (!empty($_GET['bct_kilos'])) {
            // DATOS COMERCIALES
            $datos_insertados['bct_kilos'] = [
                'texto' => 'Bct_kilos',
                'valor' => $_GET['bct_kilos']
            ];
        }
        //dd($datos_insertados);
        if (count($datos_insertados) > 0) { //Verificamos si se cambio algun valor para guardar

            //Se guarda registro en la tabla de bitacora
            $bitacora = new BitacoraWorkOrder();
            $user_auth = Auth()->user();
            $bitacora->observacion = "Aplicacion Formula Mckee";
            $bitacora->operacion = 'Mckee'; //Tipo modificacion
            $bitacora->work_order_id = 909999;
            $bitacora->user_id = $user_auth->id;
            $user_data = array(
                'nombre' => $user_auth->nombre,
                'apellido' => $user_auth->apellido,
                'rut' => $user_auth->rut,
                'role_id' => $user_auth->role_id,
            );
            $bitacora->user_data = json_encode($user_data, JSON_UNESCAPED_UNICODE); //Se agrega JSON_UNESCAPED_UNICODE para mantener el formato de las palabras con acentos
            $bitacora->datos_modificados = json_encode($datos_insertados, JSON_UNESCAPED_UNICODE);
            $bitacora->ip_solicitud = \Request::getClientIp(true);
            $bitacora->url = url()->full();
            $bitacora->save();
            //se guardan los nombre de los campos que tiene la OT
            //BitacoraCamposModificados::insert($campos);

        }

        return "";
    }
    public function getSecuenciasOperacionales()
    {
        if (!empty($_GET['planta_id'])) {
            $planta_id = $_GET['planta_id'];
            $plantas = Planta::whereNotIn('planta_id', [$planta_id])->get();
            $secuenciaOperacionalPrincipal = SecuenciaOperacional::where('active', 1)->where('planta_id', $planta_id)->pluck('descripcion', 'id')->toArray();
            $html = '<div class="col-4">' . armarSelectArrayCreateEditOT($secuenciaOperacionalPrincipal, 'sec_operacional_principal', 'Planta Original', '', '', 'form-control', true, true) . '</div>';
            $cont = 1;
            foreach ($plantas as $key => $planta) {
                $secuenciasSecundarias = SecuenciaOperacional::where('planta_id', $planta->id)->pluck('descripcion', 'id')->toArray();
                $cont++;
                $html .= '<div class="col-4">' . armarSelectArrayCreateEditOT($secuenciasSecundarias, 'sec_operacional_' . $cont . '', 'Planta Alt ' . $cont . '', '', '', 'form-control', true, true) . '</div>';
            }
            return $html;
        }
        return "";
        // $secuencia_principal = SecuenciaOperacional::where('planta_id', $planta_id)->get();
    }
}
