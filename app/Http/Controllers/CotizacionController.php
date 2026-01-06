<?php

namespace App\Http\Controllers;

use App\Cad;
use App\Cardboard;
use App\Carton;
use App\CartonEsquinero;
use App\CiudadesFlete;
use App\Client;
use App\Constants;
use App\ConsumoAdhesivo;
use App\Cotizacion;
use App\CotizacionEstado;
use App\CoverageType;
use App\DetalleCotizacion;
use App\DetallePrecioPalletizado;
use App\Envase;
use App\FactoresDesarrollo;
use App\FactoresOnda;
use App\Flete;
use App\Hierarchy;
use App\InsumosPalletizado;
use App\InkType;
use App\MaquilaServicio;
use App\Material;
use App\MermaConvertidora;
use App\MermaCorrugadora;
use App\Planta;
use App\Process;
use App\PrintType;
use App\ProductType;
use App\Role;
use App\Rubro;
use App\Style;
use App\TarifarioMargen;
use App\TipoOnda;
use App\User;
use App\VariablesCotizador;
use App\WorkOrder;
use App\PalletType;
use App\PrintingMachine;
use App\Pegado;
use App\PalletHeight;
use App\TipoBarniz;
use App\Zuncho;
use App\Pallet;
use App\CotizacionApproval;
use App\ClasificacionCliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;
use PDF;

class CotizacionController extends Controller
{
    public function index()
    {
        // Filtro por fechas
        if (!is_null(request()->input('date_desde')) and !is_null(request()->input('date_hasta'))) {
            $fromDate = Carbon::createFromFormat(
                'd/m/Y',
                request()->input('date_desde')
            )->toDateString();
            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
        } else {
            $fromDate = Carbon::now()->startOfMonth()->subMonth()->toDateString();
            $toDate = Carbon::tomorrow()->toDateString();
        }
        // $detalles = DetalleCotizacion::all()->toArray();
        // dd($detalles);

        //filtros:
        $creadores = User::where('active', 1)->whereIn('role_id', [3, 4])->get();
        if (Auth()->user()->isJefeVenta()) {
            $creadores = User::where('active', 1)->whereIn('role_id', [4])->where('jefe_id', auth()->user()->id)->orWhere('id', auth()->user()->id)->get();
        }
        $creadores->map(function ($creador) {
            $creador->creador_id = $creador->id;
        });

        $estados = CotizacionEstado::all();
        $estados->map(function ($estado) {
            $estado->estado_id = $estado->id;
        });

        $clients = Client::whereHas('cotizaciones')->where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->get();
        $clients->map(function ($client) {
            $client->client_id = $client->id;
        });

        $ots = WorkOrder::whereHas('detalleCotizacion')->where('active', 1)->select('id')->get();
        $ots->map(function ($ot) {
            $ot->work_order_id = $ot->id;
        });
        
        $cads = Cad::whereHas('detalleCotizacion')->where('active', 1)->select('id', 'cad')->get();
        $cads->map(function ($cad) {
            $cad->cad_material_id = $cad->id;
        });

        //filters:
        $query = Cotizacion::with('detalles', "estado", "user", "client", "detalles_ganados", "detalles_perdidos")->select(
            "id",
            "client_id",
            "nombre_contacto",
            "email_contacto",
            "telefono_contacto",
            "moneda_id",
            "dias_pago",
            "comision",
            "observacion_interna",
            "observacion_cliente",
            "user_id",
            "estado_id",
            "role_can_show",
            "nivel_aprobacion",
            "previous_version_id",
            "original_version_id",
            "version_number",
            "active",
            "created_at",
            "updated_at",
            "check_nombre_contacto"
        );
        if (!is_null(request()->query('estado_id'))) {
            // dd(request()->query('estado_id'));
            $query = $query->whereIn('estado_id', request()->query('estado_id'));
        }
        // Filtro por id de cotizacion
        if (!is_null(request()->input('cotizacion_id'))) {
            $query->where('id', request()->input('cotizacion_id'));
        }
        // filtro por cliente
        if (!is_null(request()->query('client_id'))) {
            $query = $query->whereIn('client_id', request()->query('client_id'));
        }
        // filtro por OT
        if (!is_null(request()->query('work_order_id'))) {
            $query = $query->whereHas('detalles', function ($q) {
                return  $q->whereIn('work_order_id', request()->input('work_order_id'));
            });
        }
        // filtro por CAD
        if (!is_null(request()->query('cad_material_id'))) {
            $query = $query->whereHas('detalles', function ($q) {
                return  $q->whereIn('cad_material_id', request()->input('cad_material_id'));
            });
        }
        
        if(!Auth()->user()->isAdmin()){//Se deja ahorita en duro para que todos los Administradores puedan revisar todas las cotizaciones
           
            if (Auth()->user()->isJefeCotizador()) {

                if (!is_null(request()->query('creador_id'))) {

                    // dd(request()->query('creador_id'));
                    $query = $query->whereIn("user_id", request()->query('creador_id'));
                    
                } else {

                    if (Auth()->user()->isJefeVenta()) {

                        $query = $query->whereHas('user', function ($query) {
                            return $query->where("jefe_id", Auth()->user()->id)->orWhere('id', auth()->user()->id);
                        });
                    }
                }
                
            } else {
                
                $vendedores_externos=User::where('responsable_id',Auth()->user()->id)->where("active", 1)->select('id')->get();
                if(count($vendedores_externos)>0){
                    $array_users=[];
                    $array_users[]=Auth()->user()->id;
                    foreach($vendedores_externos as $vendedor_externo){
                        $array_users[]=$vendedor_externo->id;
                    }
                    $query = $query->whereIn("user_id", $array_users);
                }else{
                    $query = $query->where("user_id", Auth()->user()->id);
                }
                
            }
        }
        $query = $query->whereBetween('created_at', [$fromDate, $toDate]);
        
        $cotizaciones = $query->where("active", 1)->orderBy('id', 'desc')->paginate(20);
        // Dates Format
        //dd($fromDate, $toDate,$cotizaciones);
        $fromDate = Carbon::now()->startOfMonth()->subMonth()->format('d/m/Y');
        $toDate = Carbon::now()->format('d/m/Y');
        return view('cotizador.cotizaciones.index', compact('cotizaciones', 'creadores', "estados", 'clients', 'fromDate', 'toDate', 'ots', 'cads'));
    }

    public function index_externo()
    {
        // Filtro por fechas
        if (!is_null(request()->input('date_desde')) and !is_null(request()->input('date_hasta'))) {
            $fromDate = Carbon::createFromFormat(
                'd/m/Y',
                request()->input('date_desde')
            )->toDateString();
            $toDate = Carbon::createFromFormat('d/m/Y', request()->input('date_hasta'))->addDay(1)->toDateString();
        } else {
            $fromDate = Carbon::now()->startOfMonth()->subMonth()->toDateString();
            $toDate = Carbon::tomorrow()->toDateString();
        }
        // $detalles = DetalleCotizacion::all()->toArray();
        // dd($detalles);

        //filtros:
        $creadores = User::where('active', 1)->whereIn('role_id', [3, 4])->get();
        if (Auth()->user()->isJefeVenta()) {
            $creadores = User::where('active', 1)->whereIn('role_id', [4])->where('jefe_id', auth()->user()->id)->orWhere('id', auth()->user()->id)->get();
        }
        $creadores->map(function ($creador) {
            $creador->creador_id = $creador->id;
        });

        $estados = CotizacionEstado::all();
        $estados->map(function ($estado) {
            $estado->estado_id = $estado->id;
        });

        $clients = Client::whereHas('cotizaciones')->where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->get();
        $clients->map(function ($client) {
            $client->client_id = $client->id;
        });

        $ots = WorkOrder::whereHas('detalleCotizacion')->where('active', 1)->select('id')->get();
        $ots->map(function ($ot) {
            $ot->work_order_id = $ot->id;
        });
        
        $cads = Cad::whereHas('detalleCotizacion')->where('active', 1)->select('id', 'cad')->get();
        $cads->map(function ($cad) {
            $cad->cad_material_id = $cad->id;
        });

        //filters:
        $query = Cotizacion::with('detalles', "estado", "user", "client", "detalles_ganados", "detalles_perdidos")->select(
            "id",
            "client_id",
            "nombre_contacto",
            "email_contacto",
            "telefono_contacto",
            "moneda_id",
            "dias_pago",
            "comision",
            "observacion_interna",
            "observacion_cliente",
            "user_id",
            "estado_id",
            "role_can_show",
            "nivel_aprobacion",
            "previous_version_id",
            "original_version_id",
            "version_number",
            "active",
            "created_at",
            "updated_at",
            "check_nombre_contacto"
        );
        if (!is_null(request()->query('estado_id'))) {
            // dd(request()->query('estado_id'));
            $query = $query->whereIn('estado_id', request()->query('estado_id'));
        }
        // Filtro por id de cotizacion
        if (!is_null(request()->input('cotizacion_id'))) {
            $query->where('id', request()->input('cotizacion_id'));
        }
        // filtro por cliente
        if (!is_null(request()->query('client_id'))) {
            $query = $query->whereIn('client_id', request()->query('client_id'));
        }
        // filtro por OT
        if (!is_null(request()->query('work_order_id'))) {
            $query = $query->whereHas('detalles', function ($q) {
                return  $q->whereIn('work_order_id', request()->input('work_order_id'));
            });
        }
        // filtro por CAD
        if (!is_null(request()->query('cad_material_id'))) {
            $query = $query->whereHas('detalles', function ($q) {
                return  $q->whereIn('cad_material_id', request()->input('cad_material_id'));
            });
        }

        if(!Auth()->user()->isAdmin()){//Se deja ahorita en duro para que todos los Administradores puedan revisar todas las cotizaciones
            if (Auth()->user()->isJefeCotizador()) {

                if (!is_null(request()->query('creador_id'))) {

                    // dd(request()->query('creador_id'));
                    $query = $query->whereIn("user_id", request()->query('creador_id'));
                } else {

                    if (Auth()->user()->isJefeVenta()) {

                        $query = $query->whereHas('user', function ($query) {
                            return $query->where("jefe_id", Auth()->user()->id)->orWhere('id', auth()->user()->id);
                        });
                    }
                }
                
            } else {

                $query = $query->where("user_id", Auth()->user()->id);
            }
        }
        $query = $query->whereBetween('created_at', [$fromDate, $toDate]);

        $cotizaciones = $query->where("active", 1)->orderBy('id', 'desc')->paginate(20);
        // Dates Format
        $fromDate = Carbon::now()->startOfMonth()->subMonth()->format('d/m/Y');
        $toDate = Carbon::now()->format('d/m/Y');
        return view('cotizador.cotizaciones.index_externo', compact('cotizaciones', 'creadores', "estados", 'clients', 'fromDate', 'toDate', 'ots', 'cads'));
    }

    public function create($id = 0)
    {
        
        // $detalle = DetalleCotizacion::withAll()->find([43, 44]);
        // $detalle1 = DetalleCotizacion::withAll()->find(1);
        // var_dump($detalle);
        // var_dump($detalle1);
        // Si el id esta asignado es una edicion de la cotizacion de lo contrario es creacion
        if ($id) {
            
            $cotizacion = Cotizacion::withAll()->find($id);
            // Validar si el vendedor no es el creador de la cotizacion redireccionamos 

            if (auth()->user()->isVendedor() && $cotizacion->user_id != auth()->user()->id) {
                $vendedor_externo=User::where('id',$cotizacion->user_id)->where('responsable_id',auth()->user()->id)->where("active", 1)->get();
                if(count($vendedor_externo)<1){
                    return redirect('/cotizador/index');                
                }
            }

            // dd($cotizacion->detalles[0]);
        } else {
            $cotizacion = null;
        }
        $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->pluck('nombre', 'id')->toArray();

        // Solo los 8 estilos asignados para calculo estilos 200-201-202-203-216-221-222-223
        $styles = Style::where('active', 1)->whereIn('id', [1, 2, 3, 4, 12, 14, 15, 16])->pluck('glosa', 'id')->toArray();
        $productTypes = ProductType::where('cotiza',1)->where('active', 1)->pluck('descripcion', 'id')->toArray(); // // Solo "Caja" (caja = "una pieza") "tapa" o "fondo"->whereIn('id', [3, 4, 5])
        // Todos los procesos menos offset 
        $procesos = Process::where('active', 1)->whereNotIn('id', [6, 8, 7, 9])->orderBy("descripcion")->pluck('descripcion', 'id')->toArray();
        // Todas excepto "Exportaciones"
        $hierarchies = Hierarchy::whereIn('id', [2, 3, 4, 5])->where('active', 1)->pluck('descripcion', 'id')->toArray();
        // Todos los rubros excepto esquinero
        $rubros = Rubro::where('id', "!=", 5)->pluck('descripcion', 'id')->toArray();
        $ondas = TipoOnda::pluck('onda', 'id')->toArray();
        $envases = Envase::where('active', 1)->whereIn('id', [1, 3, 4, 5, 7, 8, 9])->pluck('descripcion', 'id')->toArray();

        $styles = Style::where('active', 1)->whereIn('id', [1, 2, 3, 4, 12, 14, 15, 16])->pluck('glosa', 'id')->toArray();
        $cartons = Carton::where('tipo', '!=', 'ESQUINEROS')->where('active', 1)->where('provisional', 0)->pluck('codigo', 'id')->toArray();
        $cartones_offset = Carton::whereIn("tipo", ["DOBLE MONOTAPA", "SIMPLE EMPLACADO"])->where("active", 1)->pluck('id')->toArray();
        $cartonesEsquinero = CartonEsquinero::where('active', 1)->pluck('codigo', 'id')->toArray();
        //$cartones_alta_grafica = Carton::where('active', 1)->where('tipo_proceso',['ALTA_GRAFICA'])->pluck('id')->toArray();
        $cartones_alta_grafica = Carton::where('active', 1)->where('alta_grafica',1)->pluck('id')->toArray();
        // dd($cartones_alta_grafica);
        $flete = CiudadesFlete::pluck('ciudad', 'id')->toArray();
        $maquila_servicios = MaquilaServicio::where('active', 1)->pluck('servicio', 'id')->toArray();
        //Tipos de cobertura
        $coverageTypes = CoverageType::where('status', 1)->orderBy('order','asc')->pluck('descripcion', 'id')->toArray();
        $printTypes = PrintType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $inkTypes = InkType::where('status', 1)->pluck('descripcion', 'id')->toArray();
        $palletTypes = PalletType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        //Obtenemos el valor maximo de dias de financiamiento
        $max_dias_financiamiento=VariablesCotizador::pluck('dias_financiamiento_credito')->toArray();
        //armamos el arreglo de dias de 30 en 30
        $dias_financiamiento=[];
        $max_dias = isset($max_dias_financiamiento[0]) ? $max_dias_financiamiento[0] : 360;
        for($i=0;$i<=$max_dias;$i+=30){
            $dias_financiamiento[$i]=$i;
        }

        ///Nuevos campos Evolutivo 24-01 - Inicio
            //Maquinas Impresoras
            $printingMachines = PrintingMachine::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
            //Pegados
            $pegados = Pegado::where('active', 1)->where('codigo','<>', 0)->pluck('descripcion', 'id')->toArray();
            //Altura Pallets
            $alturaPallets = PalletHeight::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
            //tipos Barniz
            $tiposBarniz = TipoBarniz::where('active', 1)->pluck('descripcion', 'id')->toArray();
            //Zunchos
            $zunchos = Zuncho::where('active', 1)->pluck('descripcion', 'id')->toArray();
            //Pallet
            $pallets = Pallet::where('active', 1)->pluck('descripcion', 'id')->toArray();
        ///Nuevos campos Evolutivo 24-01 - Fin
        ///Nuevo campo Evolutivo 24-02 - Inicio
            //Clasificacion Cliente
            $clasificacion_clientes = ClasificacionCliente::where('active', 1)->pluck('name', 'id')->toArray();
        ///Nuevo campo Evolutivo 24-02 - Fin
            
        $es_provisional=0;
        $carton_original_codigo=''; 
        $carton_original_id='';
       
        //dd($array_dias);
        return view('cotizador.cotizaciones.create',
         compact(
            'cotizacion', 
            'clients', 
            'styles', 
            'cartons', 
            'cartones_offset', 
            'cartonesEsquinero', 
            'flete', 
            'styles', 
            'productTypes', 
            'procesos', 
            'ondas', 
            'envases', 
            'hierarchies', 
            'rubros', 
            'maquila_servicios', 
            'coverageTypes', 
            'printTypes',
            'inkTypes',
            'cartones_alta_grafica',
            'dias_financiamiento',
            'palletTypes',
            'es_provisional',
            'carton_original_codigo',
            'carton_original_id',
            'printingMachines',
            'pegados',
            'alturaPallets',
            'tiposBarniz',
            'zunchos',
            'pallets',
            'clasificacion_clientes'
        ));
    }

    public function create_externo($id = 0)
    {
        
        // $detalle = DetalleCotizacion::withAll()->find([43, 44]);
        // $detalle1 = DetalleCotizacion::withAll()->find(1);
        // var_dump($detalle);
        // var_dump($detalle1);
        // Si el id esta asignado es una edicion de la cotizacion de lo contrario es creacion
        if ($id) {
            
            $cotizacion = Cotizacion::withAll()->find($id);
            // Validar si el vendedor no es el creador de la cotizacion redireccionamos 

            if (auth()->user()->isVendedor() && $cotizacion->user_id != auth()->user()->id) {
                return redirect('/cotizador/index_externo');
            }

            // dd($cotizacion->detalles[0]);
        } else {
            $cotizacion = null;
        }
        $clients    = Client::where('active', 1)->where('id',auth()->user()->cliente_id)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->pluck('nombre', 'id')->toArray();
        $id_client  =   auth()->user()->cliente_id;
        // Solo los 8 estilos asignados para calculo estilos 200-201-202-203-216-221-222-223
        $styles = Style::where('active', 1)->whereIn('id', [1, 2, 3, 4, 12, 14, 15, 16])->pluck('glosa', 'id')->toArray();
        $productTypes = ProductType::where('cotiza',1)->where('active', 1)->pluck('descripcion', 'id')->toArray(); // // Solo "Caja" (caja = "una pieza") "tapa" o "fondo"->whereIn('id', [3, 4, 5])
        // Todos los procesos menos offset 
        $procesos = Process::where('active', 1)->whereNotIn('id', [6, 8])->orderBy("descripcion")->pluck('descripcion', 'id')->toArray();
        // Todas excepto "Exportaciones"
        $hierarchies = Hierarchy::whereIn('id', [2, 3, 4, 5])->where('active', 1)->pluck('descripcion', 'id')->toArray();
        // Todos los rubros excepto esquinero
        $rubros = Rubro::where('id', "!=", 5)->pluck('descripcion', 'id')->toArray();
        $ondas = TipoOnda::pluck('onda', 'id')->toArray();
        $envases = Envase::where('active', 1)->whereIn('id', [1, 3, 4, 5, 7, 8, 9])->pluck('descripcion', 'id')->toArray();

        $styles = Style::where('active', 1)->whereIn('id', [1, 2, 3, 4, 12, 14, 15, 16])->pluck('glosa', 'id')->toArray();
        $cartons = Carton::where('tipo', '!=', 'ESQUINEROS')->where('active', 1)->where('provisional', 0)->pluck('codigo', 'id')->toArray();
        $cartones_offset = Carton::whereIn("tipo", ["DOBLE MONOTAPA", "SIMPLE EMPLACADO"])->where("active", 1)->pluck('id')->toArray();
        $cartonesEsquinero = CartonEsquinero::where('active', 1)->pluck('codigo', 'id')->toArray();
        //$cartones_alta_grafica = Carton::where('active', 1)->where('tipo_proceso',['ALTA_GRAFICA'])->pluck('id')->toArray();
        $cartones_alta_grafica = Carton::where('active', 1)->where('alta_grafica',1)->pluck('id')->toArray();
        // dd($cartones_alta_grafica);
        $flete = CiudadesFlete::pluck('ciudad', 'id')->toArray();
        $maquila_servicios = MaquilaServicio::where('active', 1)->pluck('servicio', 'id')->toArray();
        //Tipos de cobertura
        $coverageTypes = CoverageType::where('status', 1)->orderBy('order','asc')->pluck('descripcion', 'id')->toArray();
        $printTypes = PrintType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $inkTypes = InkType::where('status', 1)->pluck('descripcion', 'id')->toArray();
        $palletTypes = PalletType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        //Obtenemos el valor maximo de dias de financiamiento
        $max_dias_financiamiento=VariablesCotizador::pluck('dias_financiamiento_credito')->toArray();
        //armamos el arreglo de dias de 30 en 30
        $dias_financiamiento=[];
        $max_dias = isset($max_dias_financiamiento[0]) ? $max_dias_financiamiento[0] : 360;
        for($i=0;$i<=$max_dias;$i+=30){
            $dias_financiamiento[$i]=$i;
        }

        $es_provisional=0;
        $carton_original_codigo=''; 
        $carton_original_id='';
        
        //Pegados
        //$pegados = Pegado::where('active', 1)->where('codigo','<>', 0)->pluck('descripcion', 'id')->toArray();
        ///Nuevos campos Evolutivo 24-01 - Inicio
            //Maquinas Impresoras
            $printingMachines = PrintingMachine::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
            //Pegados
            $pegados = Pegado::where('active', 1)->where('codigo','<>', 0)->pluck('descripcion', 'id')->toArray();
            //Altura Pallets
            $alturaPallets = PalletHeight::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
            //tipos Barniz
            $tiposBarniz = TipoBarniz::where('active', 1)->pluck('descripcion', 'id')->toArray();
            //Zunchos
            $zunchos = Zuncho::where('active', 1)->pluck('descripcion', 'id')->toArray();
            //Pallet
            $pallets = Pallet::where('active', 1)->pluck('descripcion', 'id')->toArray();
        ///Nuevos campos Evolutivo 24-01 - Fin
        
        ///Nuevo campo Evolutivo 24-02 - Inicio
            //Clasificacion Cliente
            $clasificacion_clientes = ClasificacionCliente::where('active', 1)->pluck('name', 'id')->toArray();
        ///Nuevo campo Evolutivo 24-02 - Fin

        return view('cotizador.cotizaciones.create_externo',
         compact(
            'cotizacion', 
            'clients', 
            'styles', 
            'cartons', 
            'cartones_offset', 
            'cartonesEsquinero', 
            'flete', 
            'styles', 
            'productTypes', 
            'procesos', 
            'ondas', 
            'envases', 
            'hierarchies', 
            'rubros', 
            'maquila_servicios', 
            'coverageTypes', 
            'printTypes',
            'inkTypes',
            'cartones_alta_grafica',
            'dias_financiamiento',
            'palletTypes',
            'es_provisional',
            'carton_original_codigo',
            'carton_original_id',
            'id_client',
            'pegados',
            'printingMachines',
            'alturaPallets',
            'tiposBarniz',
            'zunchos',
            'pallets',
            'clasificacion_clientes'
        ));
    }

    public function create_externo_aprobacion($id = 0)
    {
        
        // $detalle = DetalleCotizacion::withAll()->find([43, 44]);
        // $detalle1 = DetalleCotizacion::withAll()->find(1);
        // var_dump($detalle);
        // var_dump($detalle1);
        // Si el id esta asignado es una edicion de la cotizacion de lo contrario es creacion
        if ($id) {
            
            $cotizacion = Cotizacion::withAll()->find($id);
            // Validar si el vendedor no es el creador de la cotizacion redireccionamos 

            if (auth()->user()->isVendedor() && $cotizacion->user_id != auth()->user()->id) {
                $vendedor_externo=User::where('id',$cotizacion->user_id)->where('responsable_id',auth()->user()->id)->where("active", 1)->get();
                if(count($vendedor_externo)<1){
                    return redirect('/cotizador/index');                
                }
            }

            // dd($cotizacion->detalles[0]);
        } else {
            $cotizacion = null;
        }
        $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->pluck('nombre', 'id')->toArray();

        // Solo los 8 estilos asignados para calculo estilos 200-201-202-203-216-221-222-223
        $styles = Style::where('active', 1)->whereIn('id', [1, 2, 3, 4, 12, 14, 15, 16])->pluck('glosa', 'id')->toArray();
        $productTypes = ProductType::where('cotiza',1)->where('active', 1)->pluck('descripcion', 'id')->toArray(); // // Solo "Caja" (caja = "una pieza") "tapa" o "fondo"->whereIn('id', [3, 4, 5])
        // Todos los procesos menos offset 
        $procesos = Process::where('active', 1)->whereNotIn('id', [6, 8])->orderBy("descripcion")->pluck('descripcion', 'id')->toArray();
        // Todas excepto "Exportaciones"
        $hierarchies = Hierarchy::whereIn('id', [2, 3, 4, 5])->where('active', 1)->pluck('descripcion', 'id')->toArray();
        // Todos los rubros excepto esquinero
        $rubros = Rubro::where('id', "!=", 5)->pluck('descripcion', 'id')->toArray();
        $ondas = TipoOnda::pluck('onda', 'id')->toArray();
        $envases = Envase::where('active', 1)->whereIn('id', [1, 3, 4, 5, 7, 8, 9])->pluck('descripcion', 'id')->toArray();

        $styles = Style::where('active', 1)->whereIn('id', [1, 2, 3, 4, 12, 14, 15, 16])->pluck('glosa', 'id')->toArray();
        $cartons = Carton::where('tipo', '!=', 'ESQUINEROS')->where('active', 1)->where('provisional', 0)->pluck('codigo', 'id')->toArray();
        $cartones_offset = Carton::whereIn("tipo", ["DOBLE MONOTAPA", "SIMPLE EMPLACADO"])->where("active", 1)->pluck('id')->toArray();
        $cartonesEsquinero = CartonEsquinero::where('active', 1)->pluck('codigo', 'id')->toArray();
        //$cartones_alta_grafica = Carton::where('active', 1)->where('tipo_proceso',['ALTA_GRAFICA'])->pluck('id')->toArray();
        $cartones_alta_grafica = Carton::where('active', 1)->where('alta_grafica',1)->pluck('id')->toArray();
        // dd($cartones_alta_grafica);
        $flete = CiudadesFlete::pluck('ciudad', 'id')->toArray();
        $maquila_servicios = MaquilaServicio::where('active', 1)->pluck('servicio', 'id')->toArray();
        //Tipos de cobertura
        $coverageTypes = CoverageType::where('status', 1)->orderBy('order','asc')->pluck('descripcion', 'id')->toArray();
        $printTypes = PrintType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $inkTypes = InkType::where('status', 1)->pluck('descripcion', 'id')->toArray();
        $palletTypes = PalletType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        //Obtenemos el valor maximo de dias de financiamiento
        $max_dias_financiamiento=VariablesCotizador::pluck('dias_financiamiento_credito')->toArray();
        //armamos el arreglo de dias de 30 en 30
        $dias_financiamiento=[];
        $max_dias = isset($max_dias_financiamiento[0]) ? $max_dias_financiamiento[0] : 360;
        for($i=0;$i<=$max_dias;$i+=30){
            $dias_financiamiento[$i]=$i;
        }

        $es_provisional=0;
        $carton_original_codigo=''; 
        $carton_original_id='';
                
        //Pegados
        //$pegados = Pegado::where('active', 1)->where('codigo','<>', 0)->pluck('descripcion', 'id')->toArray();
        ///Nuevos campos Evolutivo 24-01 - Inicio
            //Maquinas Impresoras
            $printingMachines = PrintingMachine::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
            //Pegados
            $pegados = Pegado::where('active', 1)->where('codigo','<>', 0)->pluck('descripcion', 'id')->toArray();
            //Altura Pallets
            $alturaPallets = PalletHeight::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
            //tipos Barniz
            $tiposBarniz = TipoBarniz::where('active', 1)->pluck('descripcion', 'id')->toArray();
            //Zunchos
            $zunchos = Zuncho::where('active', 1)->pluck('descripcion', 'id')->toArray();
            //Pallet
            $pallets = Pallet::where('active', 1)->pluck('descripcion', 'id')->toArray();
        ///Nuevos campos Evolutivo 24-01 - Fin
        
        ///Nuevo campo Evolutivo 24-02 - Inicio
            //Clasificacion Cliente
            $clasificacion_clientes = ClasificacionCliente::where('active', 1)->pluck('name', 'id')->toArray();
        ///Nuevo campo Evolutivo 24-02 - Fin

        return view('cotizador.cotizaciones.create_externo_aprobacion',
         compact(
            'cotizacion', 
            'clients', 
            'styles', 
            'cartons', 
            'cartones_offset', 
            'cartonesEsquinero', 
            'flete', 
            'styles', 
            'productTypes', 
            'procesos', 
            'ondas', 
            'envases', 
            'hierarchies', 
            'rubros', 
            'maquila_servicios', 
            'coverageTypes', 
            'printTypes',
            'inkTypes',
            'cartones_alta_grafica',
            'dias_financiamiento',
            'palletTypes',
            'es_provisional',
            'carton_original_codigo',
            'carton_original_id',
            'pegados',
            'printingMachines',
            'alturaPallets',
            'tiposBarniz',
            'zunchos',
            'pallets',
            'clasificacion_clientes'
        ));
    }

    public function aprobar_externo($id = 0)
    {
        
        // $detalle = DetalleCotizacion::withAll()->find([43, 44]);
        // $detalle1 = DetalleCotizacion::withAll()->find(1);
        // var_dump($detalle);
        // var_dump($detalle1);
        // Si el id esta asignado es una edicion de la cotizacion de lo contrario es creacion
        if ($id) {
            
            $cotizacion = Cotizacion::withAll()->find($id);
            // Validar si el vendedor no es el creador de la cotizacion redireccionamos 

            if (auth()->user()->isVendedor() && $cotizacion->user_id != auth()->user()->id) {
                $vendedor_externo=User::where('id',$cotizacion->user_id)->where('responsable_id',auth()->user()->id)->where("active", 1)->get();
                if(count($vendedor_externo)<1){
                    return redirect('/cotizador/index');                
                }
            }

            // dd($cotizacion->detalles[0]);
        } else {
            $cotizacion = null;
        }
        $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->pluck('nombre', 'id')->toArray();

        // Solo los 8 estilos asignados para calculo estilos 200-201-202-203-216-221-222-223
        $styles = Style::where('active', 1)->whereIn('id', [1, 2, 3, 4, 12, 14, 15, 16])->pluck('glosa', 'id')->toArray();
        $productTypes = ProductType::where('cotiza',1)->where('active', 1)->pluck('descripcion', 'id')->toArray(); // // Solo "Caja" (caja = "una pieza") "tapa" o "fondo"->whereIn('id', [3, 4, 5])
        // Todos los procesos menos offset 
        $procesos = Process::where('active', 1)->whereNotIn('id', [6, 8])->orderBy("descripcion")->pluck('descripcion', 'id')->toArray();
        // Todas excepto "Exportaciones"
        $hierarchies = Hierarchy::whereIn('id', [2, 3, 4, 5])->where('active', 1)->pluck('descripcion', 'id')->toArray();
        // Todos los rubros excepto esquinero
        $rubros = Rubro::where('id', "!=", 5)->pluck('descripcion', 'id')->toArray();
        $ondas = TipoOnda::pluck('onda', 'id')->toArray();
        $envases = Envase::where('active', 1)->whereIn('id', [1, 3, 4, 5, 7, 8, 9])->pluck('descripcion', 'id')->toArray();

        $styles = Style::where('active', 1)->whereIn('id', [1, 2, 3, 4, 12, 14, 15, 16])->pluck('glosa', 'id')->toArray();
        $cartons = Carton::where('tipo', '!=', 'ESQUINEROS')->where('active', 1)->where('provisional', 0)->pluck('codigo', 'id')->toArray();
        $cartones_offset = Carton::whereIn("tipo", ["DOBLE MONOTAPA", "SIMPLE EMPLACADO"])->where("active", 1)->pluck('id')->toArray();
        $cartonesEsquinero = CartonEsquinero::where('active', 1)->pluck('codigo', 'id')->toArray();
        //$cartones_alta_grafica = Carton::where('active', 1)->where('tipo_proceso',['ALTA_GRAFICA'])->pluck('id')->toArray();
        $cartones_alta_grafica = Carton::where('active', 1)->where('alta_grafica',1)->pluck('id')->toArray();
        // dd($cartones_alta_grafica);
        $flete = CiudadesFlete::pluck('ciudad', 'id')->toArray();
        $maquila_servicios = MaquilaServicio::where('active', 1)->pluck('servicio', 'id')->toArray();
        //Tipos de cobertura
        $coverageTypes = CoverageType::where('status', 1)->orderBy('order','asc')->pluck('descripcion', 'id')->toArray();
        $printTypes = PrintType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $inkTypes = InkType::where('status', 1)->pluck('descripcion', 'id')->toArray();
        $palletTypes = PalletType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        //Obtenemos el valor maximo de dias de financiamiento
        $max_dias_financiamiento=VariablesCotizador::pluck('dias_financiamiento_credito')->toArray();
        //armamos el arreglo de dias de 30 en 30
        $dias_financiamiento=[];
        $max_dias = isset($max_dias_financiamiento[0]) ? $max_dias_financiamiento[0] : 360;
        for($i=0;$i<=$max_dias;$i+=30){
            $dias_financiamiento[$i]=$i;
        }

        $es_provisional=0;
        $carton_original_codigo=''; 
        $carton_original_id='';
       
        //dd($array_dias);
        return view('cotizador.cotizaciones.aprobar_externo',
         compact(
            'cotizacion', 
            'clients', 
            'styles', 
            'cartons', 
            'cartones_offset', 
            'cartonesEsquinero', 
            'flete', 
            'styles', 
            'productTypes', 
            'procesos', 
            'ondas', 
            'envases', 
            'hierarchies', 
            'rubros', 
            'maquila_servicios', 
            'coverageTypes', 
            'printTypes',
            'inkTypes',
            'cartones_alta_grafica',
            'dias_financiamiento',
            'palletTypes',
            'es_provisional',
            'carton_original_codigo',
            'carton_original_id'
        ));
    }

    public function cotizarOt($id)
    {
        $ot =  WorkOrder::with(
            'subsubhierarchy.subhierarchy.hierarchy',
            'canal',
            'client',
            'creador',
            'productType',
            "users",
            "material",
            "cad_asignado"
        )->find($id);
        // dd($ot);
        $cotizacion = null;
        $clients = Client::where('active', 1)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " - ", COALESCE(codigo,"")) AS nombre'))->pluck('nombre', 'id')->toArray();

        // Solo los 8 estilos asignados para calculo estilos 200-201-202-203-216-221-222-223
        $styles = Style::where('active', 1)->whereIn('id', [1, 2, 3, 4, 12, 14, 15, 16])->pluck('glosa', 'id')->toArray();
        $productTypes = ProductType::where('cotiza',1)->where('active', 1)->pluck('descripcion', 'id')->toArray(); // // Solo "Caja" (caja = "una pieza") "tapa" o "fondo"->whereIn('id', [3, 4, 5])
        // Todos los procesos menos offset 
        $procesos = Process::where('active', 1)->whereNotIn('id', [6, 8])->orderBy("descripcion")->pluck('descripcion', 'id')->toArray();
        // Todas excepto "Exportaciones"
        $hierarchies = Hierarchy::whereIn('id', [2, 3, 4, 5])->where('active', 1)->pluck('descripcion', 'id')->toArray();
        // Todos los rubros excepto esquinero
        $rubros = Rubro::where('id', "!=", 5)->pluck('descripcion', 'id')->toArray();
        $ondas = TipoOnda::pluck('onda', 'id')->toArray();
        $envases = Envase::where('active', 1)->whereIn('id', [1, 3, 4, 5, 7, 8, 9])->pluck('descripcion', 'id')->toArray();

        $styles = Style::where('active', 1)->whereIn('id', [1, 2, 3, 4, 12, 14, 15, 16])->pluck('glosa', 'id')->toArray();
        $cartons = Carton::where('tipo', '!=', 'ESQUINEROS')->where('active', 1)->where('provisional', 0)->pluck('codigo', 'id')->toArray();
        $cartones_offset = Carton::whereIn("tipo", ["DOBLE MONOTAPA", "SIMPLE EMPLACADO"])->where("active", 1)->pluck('id')->toArray();
        $cartonesEsquinero = CartonEsquinero::where('active', 1)->pluck('codigo', 'id')->toArray();
        //$cartones_alta_grafica = Carton::where('active', 1)->where('tipo_proceso',['ALTA_GRAFICA'])->pluck('id')->toArray();
        $cartones_alta_grafica = Carton::where('active', 1)->where('alta_grafica',1)->pluck('id')->toArray();
        // dd($cartones_offset);

        $flete = CiudadesFlete::pluck('ciudad', 'id')->toArray();
        $impresion = max($ot->impresion_1, $ot->impresion_2, $ot->impresion_3, $ot->impresion_4, $ot->impresion_5);
        $maquila_servicios = MaquilaServicio::where('active', 1)->pluck('servicio', 'id')->toArray();
        $coverageTypes = CoverageType::where('status', 1)->orderBy('order','asc')->pluck('descripcion', 'id')->toArray();
        $printTypes = PrintType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $inkTypes = InkType::where('status', 1)->pluck('descripcion', 'id')->toArray();
        //Obtenemos el valor maximo de dias de financiamiento
        $max_dias_financiamiento=VariablesCotizador::pluck('dias_financiamiento_credito')->toArray();
        //armamos el arreglo de dias de 30 en 30
        $dias_financiamiento=[];
        $max_dias = isset($max_dias_financiamiento[0]) ? $max_dias_financiamiento[0] : 360;
        for($i=0;$i<=$max_dias;$i+=30){
            $dias_financiamiento[$i]=$i;
        }
        $palletTypes = PalletType::where('active', 1)->pluck('descripcion', 'id')->toArray();

        $carton_provisional=Carton::where('id',$ot->carton_id)->where('active', 1)->where('provisional', 1)->first();

        if($carton_provisional){
            $es_provisional=1;
            $carton_original_codigo=$carton_provisional->carton_original;
            $carton_original=Carton::where('codigo',$carton_original_codigo)->where('active', 1)->where('provisional', 0)->first();
            $carton_original_id=$carton_original->id;

        }else{
            $es_provisional=0;
            $carton_original_codigo=''; 
            $carton_original_id='';
        }
       
        //Pegados
        $pegados = Pegado::where('active', 1)->where('codigo','<>', 0)->pluck('descripcion', 'id')->toArray();
        ///Nuevos campos Evolutivo 24-01 - Inicio
            //Maquinas Impresoras
            $printingMachines = PrintingMachine::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
            //Pegados
            $pegados = Pegado::where('active', 1)->where('codigo','<>', 0)->pluck('descripcion', 'id')->toArray();
            //Altura Pallets
            $alturaPallets = PalletHeight::where('deleted', 0)->pluck('descripcion', 'id')->toArray();
            //tipos Barniz
            $tiposBarniz = TipoBarniz::where('active', 1)->pluck('descripcion', 'id')->toArray();
            //Zunchos
            $zunchos = Zuncho::where('active', 1)->pluck('descripcion', 'id')->toArray();
            //Pallet
            $pallets = Pallet::where('active', 1)->pluck('descripcion', 'id')->toArray();
        ///Nuevos campos Evolutivo 24-01 - Fin

       ///Nuevo campo Evolutivo 24-02 - Inicio
            //Clasificacion Cliente
            $clasificacion_clientes = ClasificacionCliente::where('active', 1)->pluck('name', 'id')->toArray();
        ///Nuevo campo Evolutivo 24-02 - Fin

        return view('cotizador.cotizaciones.create', compact(
            'impresion', 
            'ot', 
            'cotizacion', 
            'clients', 
            'styles', 
            'cartons', 
            'cartones_offset', 
            'cartonesEsquinero', 
            'flete', 
            'styles', 
            'productTypes', 
            'procesos', 
            'ondas', 
            'envases', 
            'hierarchies', 
            'rubros',
            'maquila_servicios', 
            'coverageTypes', 
            'printTypes',
            'cartones_alta_grafica',
            'inkTypes',
            'dias_financiamiento',
            'palletTypes',
            'es_provisional',
            'carton_original_codigo',
            'carton_original_id',
            'printingMachines',
            'pegados',
            'alturaPallets',
            'tiposBarniz',
            'zunchos',
            'pallets',
            'clasificacion_clientes'));
    }

    public function calcularDetalleCotizacion(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            // Datos Comerciales
            // 'tipo_detalle_id' => 'required',
        ]);


        // return response()->json($resultado);
    }

    public function generarPrecotizacion($id,$client_id)
    {
        $detalles = json_decode(request("detalles"));
       
        if ($id) {
            $operacion = "actualizar";
            $cotizacion = Cotizacion::find($id);
            $cliente_antiguo = $cotizacion->client_id;
        } else {
            $operacion = "crear";
            $cotizacion = new Cotizacion();
            $cotizacion->version_number = 1;
            $cliente_antiguo = 0;
        }

       
        // if($cotizacion->user_id == auth()->user()->id)
        if (auth()->user()->role_id != 2  && auth()->user()->role_id != 15) {

            $cotizacion->client_id = request('client_id');
            $cotizacion->nombre_contacto = request('nombre_contacto');
            $cotizacion->instalacion_cliente = request('instalacion_cliente');
            $cotizacion->check_nombre_contacto = (trim(request('check_nombre_contacto')) == 'on') ?  1 : 0;;
            $cotizacion->email_contacto = request('email_contacto');
            $cotizacion->telefono_contacto = request('telefono_contacto');
            $cotizacion->moneda_id = request('moneda_id');
            $cotizacion->dias_pago = request('dias_pago');
            $cotizacion->comision = request('comision');
            $cotizacion->observacion_interna = request('observacion_interna');
            $cotizacion->observacion_cliente = request('observacion_cliente');
            $cotizacion->user_id = auth()->user()->id;
            $cotizacion->clasificacion_cliente = request('clasificacion_cliente');
            $cotizacion->save();
        }
               
        if ($detalles) {
            // dd($detalles);
            // $detalles_id = [];
            foreach ($detalles as $detalle) {

                // $detalles_id[] = $detalle->id;
                // Si no tienen margen lo calculamos (excepto rubro esquineros)
                $DetalleCotizacion = DetalleCotizacion::with('rubro', 'carton', 'cotizacion.client')->find($detalle);
                $DetalleCotizacion->cotizacion_id = $cotizacion->id;
                $DetalleCotizacion->setRelation('cotizacion', $cotizacion);
                if ($DetalleCotizacion->rubro_id != 5) { 
                    $role_user_id=User::where('id',$cotizacion->user_id)->where('active',1)->first();
                    
                    if($role_user_id->role_id==19){
                        $cliente=Client::where('id',$cotizacion->client_id)->first();
                        if($operacion == "crear"){
                            $DetalleCotizacion->margen_sugerido = $cliente->margen_minimo_vendedor_externo;
                        }
                       
                    }else{
                        //$DetalleCotizacion->margen_sugerido = obtenerMargenSugerido($DetalleCotizacion);
                        if($operacion == "crear"){
                            $DetalleCotizacion->margen_sugerido = obtenerMargenSugeridoNew($DetalleCotizacion);
                            $DetalleCotizacion->margen_papeles = $this->obtenerMargenPapeles($DetalleCotizacion);
                        }else{
                           
                            if($cliente_antiguo != $client_id){
                               // dd(obtenerMargenSugeridoNew($DetalleCotizacion));
                                $DetalleCotizacion->margen_sugerido = obtenerMargenSugeridoNew($DetalleCotizacion);
                                $DetalleCotizacion->margen_papeles = $this->obtenerMargenPapeles($DetalleCotizacion);
                            }else{
                                if($DetalleCotizacion->margen_sugerido == 0 ){
                                    $DetalleCotizacion->margen_sugerido = obtenerMargenSugeridoNew($DetalleCotizacion);
                                    $DetalleCotizacion->margen_papeles = $this->obtenerMargenPapeles($DetalleCotizacion); 
                                }
                            }
                        }
                    }
                    
                   // $DetalleCotizacion->margen_sugerido = calcularMargenSugerido($DetalleCotizacion);
                    // dd($DetalleCotizacion->margen_sugerido);
                }
                $DetalleCotizacion->save();
            }
            // DetalleCotizacion::whereIn('id', $detalles_id)->update(array('cotizacion_id' => $cotizacion->id));
        }

        $cotizacion = Cotizacion::withAll()->find($cotizacion->id);
        // $cotizacion = Cotizacion::withAll()->find($cotizacion->id);

        // dd($cotizacion);
        return response()->json($cotizacion);
    }

    public function generarPrecotizacionExterno($id,$client_id)
    {
        $detalles = json_decode(request("detalles"));
        // $detalles = request("detalles");
        //dd($detalles);

        if ($id) {
            $operacion = "actualizar";
            $cotizacion = Cotizacion::find($id);
            $cliente_antiguo = $cotizacion->client_id;
        } else {
            $operacion = "crear";
            $cotizacion = new Cotizacion();
            $cotizacion->version_number = 1;
            $cliente_antiguo = 0;
        }

        // if($cotizacion->user_id == auth()->user()->id)
        if (auth()->user()->role_id != 2  && auth()->user()->role_id != 15) {

            $cotizacion->client_id = request('client_id');
            $cotizacion->instalacion_cliente = request('instalacion_cliente');
            $cotizacion->nombre_contacto = request('nombre_contacto');
            $cotizacion->check_nombre_contacto = (trim(request('check_nombre_contacto')) == 'on') ?  1 : 0;;
            $cotizacion->email_contacto = request('email_contacto');
            $cotizacion->telefono_contacto = request('telefono_contacto');
            $cotizacion->moneda_id = request('moneda_id');
            $cotizacion->dias_pago = request('dias_pago');
            $cotizacion->comision = request('comision');
            $cotizacion->observacion_interna = request('observacion_interna');
            $cotizacion->observacion_cliente = request('observacion_cliente');
            $cotizacion->clasificacion_cliente = request('clasificacion_cliente');
            //$cotizacion->user_id = auth()->user()->id;
            $cotizacion->save();
        }
        if ($detalles) {
            // dd($detalles);
            // $detalles_id = [];
            foreach ($detalles as $detalle) {

                // $detalles_id[] = $detalle->id;
                // Si no tienen margen lo calculamos (excepto rubro esquineros)
                $DetalleCotizacion = DetalleCotizacion::with('rubro', 'carton', 'cotizacion.client')->find($detalle);
                $DetalleCotizacion->cotizacion_id = $cotizacion->id;
                $DetalleCotizacion->setRelation('cotizacion', $cotizacion);
                if ($DetalleCotizacion->rubro_id != 5) { 

                    $role_user_id=User::where('id',$cotizacion->user_id)->where('active',1)->first();
                    
                    if($role_user_id->role_id==19){
                        $cliente=Client::where('id',$cotizacion->client_id)->first();
                        $DetalleCotizacion->margen_sugerido = $cliente->margen_minimo_vendedor_externo;
                    }else{
                        //$DetalleCotizacion->margen_sugerido = obtenerMargenSugerido($DetalleCotizacion);
                        if($operacion == "crear"){
                            $DetalleCotizacion->margen_sugerido = obtenerMargenSugeridoNew($DetalleCotizacion);
                            $DetalleCotizacion->margen_papeles  = $this->obtenerMargenPapeles($DetalleCotizacion);
                        }else{
                            if($cliente_antiguo != $client_id){
                                // dd(obtenerMargenSugeridoNew($DetalleCotizacion));
                                 $DetalleCotizacion->margen_sugerido = obtenerMargenSugeridoNew($DetalleCotizacion);
                                 $DetalleCotizacion->margen_papeles = $this->obtenerMargenPapeles($DetalleCotizacion);
                            }else{
                                if($DetalleCotizacion->margen_sugerido == 0 ){
                                    $DetalleCotizacion->margen_sugerido = obtenerMargenSugeridoNew($DetalleCotizacion);
                                    $DetalleCotizacion->margen_papeles = $this->obtenerMargenPapeles($DetalleCotizacion); 
                                }
                            }
                         }
                    }                 
                }                  
               
                $DetalleCotizacion->save();
            }
            // DetalleCotizacion::whereIn('id', $detalles_id)->update(array('cotizacion_id' => $cotizacion->id));
        }

        $cotizacion = Cotizacion::withAll()->find($cotizacion->id);
        // $cotizacion = Cotizacion::withAll()->find($cotizacion->id);

        // dd($cotizacion);
        return response()->json($cotizacion);
    }

    public function solicitarAprobacion($id)
    {   
       
        $cotizacion = Cotizacion::withAll()->find($id);
        
        // Almacenar por detalle los resultados y calculos de precios de este
        foreach ($cotizacion->detalles as $detalle) {
            // dd(json_encode((array)$detalle->precios),$detalle->precios);
            $detalle->historial_resultados = $detalle->precios;
            // $detalle->historial_resultados = json_encode ((array)$detalle->precios);
            $detalle->save();
        }

        // Iterar sobre los detalles de la cotizacion para encontrar margenes maximos
        $margen = $this->calcular_margen($cotizacion);
        // dd($margen);
        // Role jefe de ventas siempre es el primero en aprobar de requerir aprobacion
        $por_aprobar = null;
        $nivel_aprobacion = null;
        $estado = 2;
        // Si el margen esta dentro del lo sugerido la cotizacion es aprobada inmediatamente
        // Si es jefe de venta el margen para aprobar inmediatamente sube a 10
        if ($margen <= 0 || (auth()->user()->isJefeVenta() && $margen < 10)) {

            $estado = 3;
        }
        // Si 
        elseif ($margen < 10) {
            $por_aprobar = 3;
            $nivel_aprobacion = 1;
        } elseif ($margen < 25) {

            $por_aprobar = auth()->user()->isJefeVenta() ? 15 : 3;
            $nivel_aprobacion = 2;
        } else {

            $por_aprobar = auth()->user()->isJefeVenta() ? 15 : 3;
            $nivel_aprobacion = 3;
        }
        $cotizacion->role_can_show = $por_aprobar;
        $cotizacion->nivel_aprobacion = $nivel_aprobacion;
        $cotizacion->estado_id = $estado;
        $cotizacion->save();


        if(Auth()->user()->isVendedorExterno()){
            //return redirect()->route('cotizador.index_cotizacion_externo');
            return response()->json(["url" => route('cotizador.index_cotizacion_externo'), "margen" => $margen]);
        }else{
            return response()->json(["url" => route('cotizador.index_cotizacion'), "margen" => $margen]);
        }
        
        // return response()->json($cotizacion);
    }

    public function solicitarAprobacionExterno($id)
    {

        $cotizacion = Cotizacion::withAll()->find($id);

        // Almacenar por detalle los resultados y calculos de precios de este
        foreach ($cotizacion->detalles as $detalle) {
            // dd(json_encode((array)$detalle->precios),$detalle->precios);
            $detalle->historial_resultados = $detalle->precios;
            // $detalle->historial_resultados = json_encode ((array)$detalle->precios);
            $detalle->save();
        }

        // Iterar sobre los detalles de la cotizacion para encontrar margenes maximos
        //$margen = $this->calcular_margen($cotizacion);
        // dd($margen);
        // Role jefe de ventas siempre es el primero en aprobar de requerir aprobacion
        $por_aprobar = 4;
        $nivel_aprobacion = 1;
        $estado = 2;
        
        // Si el margen esta dentro del lo sugerido la cotizacion es aprobada inmediatamente
        // Si es jefe de venta el margen para aprobar inmediatamente sube a 10
        /*if ($margen <= 0 || (auth()->user()->isJefeVenta() && $margen < 10)) {

            $estado = 3;
        }
        // Si 
        elseif ($margen < 10) {
            $por_aprobar = 3;
            $nivel_aprobacion = 1;
        } elseif ($margen < 25) {

            $por_aprobar = auth()->user()->isJefeVenta() ? 15 : 3;
            $nivel_aprobacion = 2;
        } else {

            $por_aprobar = auth()->user()->isJefeVenta() ? 15 : 3;
            $nivel_aprobacion = 3;
        }*/
        $cotizacion->role_can_show = $por_aprobar;
        $cotizacion->nivel_aprobacion = $nivel_aprobacion;
        $cotizacion->estado_id = $estado;
        $cotizacion->save();


        if(Auth()->user()->isVendedorExterno()){
            //return redirect()->route('cotizador.index_cotizacion_externo');
            return response()->json(["url" => route('cotizador.index_cotizacion_externo')]);
        }else{
            return response()->json(["url" => route('cotizador.index_cotizacion')]);
        }
        
        // return response()->json($cotizacion);
    }

    public function calcular_margen($cotizacion)
    {
        $margen_total = $cotizacion->detalles->sum("margen");
        $margen_sugerido_total = $cotizacion->detalles->sum("margen_sugerido");

        // dd($margen_total,$margen_sugerido_total,$margen_total > $margen_sugerido_total,$margen_total  == "0.0");
        // Si el margen esta dentro del margen sugerido retornamos 0
        if ($margen_total >= $margen_sugerido_total) {
            $margen = 0;
        } else {
            if ($margen_sugerido_total == 0) {
                return  0;
            }
            // de lo contrario calculamos el porcentaje absoluto de la diferencia 
            $margen = abs((1 - $margen_total / $margen_sugerido_total) * 100);
        }
        return $margen;
    }

    public function versionarCotizacion($id)
    {
        // dd($id);
        $cotizacion = Cotizacion::find($id);
        // dd($cotizacion);
        $newCotizacion = $cotizacion->replicate();
        $newCotizacion->previous_version_id = $cotizacion->id;
        $newCotizacion->original_version_id = ($cotizacion->original_version_id) ? $cotizacion->original_version_id : $cotizacion->id;
        $newCotizacion->version_number = $cotizacion->version_number + 1;
        $newCotizacion->estado_id = 1;
        $newCotizacion->active = 1;
        $newCotizacion->push();

        foreach ($cotizacion->detalles as $detalle) {

            $newDetalle = $detalle->replicate();
            $newDetalle->cotizacion_id = $newCotizacion->id;
            $newDetalle->historial_resultados = null;
            $newDetalle->push();
        }

        $cotizacion->active = 0;
        $cotizacion->save();

        return redirect()->route('cotizador.editar_cotizacion', $newCotizacion->id)->with('success', 'Cotización versionada exitosamente.');
    }

    public function duplicarCotizacion($id)
    {
        // dd($id);
        $cotizacion = Cotizacion::find($id);
        // dd($cotizacion);
        $newCotizacion = $cotizacion->replicate();
        $newCotizacion->previous_version_id = null;
        $newCotizacion->original_version_id = null;
        $newCotizacion->version_number = 1;
        $newCotizacion->estado_id = 1;
        $newCotizacion->role_can_show = null;
        $newCotizacion->nivel_aprobacion = null;
        $newCotizacion->active = 1;
        $newCotizacion->user_id = auth()->user()->id;
        $newCotizacion->push();

        foreach ($cotizacion->detalles as $detalle) {

            $newDetalle = $detalle->replicate();
           // $newDetalle->margen = 0;
            $newDetalle->margen_sugerido= null;
            $newDetalle->margen_papeles= null;

            $newDetalle->cotizacion_id = $newCotizacion->id;
            $newDetalle->historial_resultados = null;
            $newDetalle->push();
        }


        return redirect()->route('cotizador.editar_cotizacion', $newCotizacion->id)->with('success', 'Cotización duplicada exitosamente.');
    }

    public function retomarCotizacion($id)
    {
        // dd($id);
        $cotizacion = Cotizacion::find($id);
        // dd($cotizacion);
        $cotizacion->estado_id = 1;
        $cotizacion->role_can_show = null;
        $cotizacion->nivel_aprobacion = null;
        $cotizacion->save();


        return redirect()->route('cotizador.editar_cotizacion', $cotizacion->id)->with('success', 'Cotización retomada exitosamente.');
    }

    public function retomarCotizacionExterno($id)
    {
        // dd($id);
        $cotizacion = Cotizacion::find($id);
        // dd($cotizacion);
        $cotizacion->estado_id = 1;
        $cotizacion->role_can_show = null;
        $cotizacion->nivel_aprobacion = null;
        $cotizacion->save();


        return redirect()->route('cotizador.editar_cotizacion_externo', $cotizacion->id)->with('success', 'Cotización retomada exitosamente.');
    }

    public function editarCotizacionExterno($id)
    {
        // dd($id);
        $cotizacion = Cotizacion::find($id);
        // dd($cotizacion);
        $cotizacion->estado_id = 1;
        $cotizacion->role_can_show = null;
        $cotizacion->nivel_aprobacion = null;
        $cotizacion->save();


        return redirect()->route('cotizador.editar_cotizacion_externo_aprobacion', $cotizacion->id)->with('success', 'Cotización retomada exitosamente.');
    }

    public function cargaMateriales(Request $request)
    {
        // dd(request()->all());

        $query = Material::with('cad', 'client', 'carton', 'product_type', 'style');

        if (!is_null(request()->input('codigo_material'))) {
            $query = $query->where('codigo', 'like', '%' . request()->input('codigo_material') . '%');
        }
        if (!is_null(request()->input('descripcion_material'))) {
            $query = $query->where('descripcion', 'like', '%' . request()->input('descripcion_material') . '%');
        }
        if (!is_null(request()->input('style_id'))) {
            $query = $query->where('style_id', request()->input('style_id'));
        }
        if (!is_null(request()->input('cad'))) {
            $query = $query->whereHas('cad', function ($q) {
                $q->where('cad', 'like', '%' . request()->input('cad') . '%');
            });
        }
        // dd($query);
        $materiales = $query->limit(100)->get();
        return response()->json([
            'mensaje' => "Materiales encontrados Exitosamente",
            'materiales' => $materiales,

        ], 200);
    }

    public function generar_pdf(Request $request)
    {
        // return view('cotizador.cotizaciones.pdf.cotizacion');
        $cotizacion = Cotizacion::withAll()->find(request("id"));
        // dd($cotizacion);
        view()->share('cotizacion', $cotizacion);
        if ($request->has('download')) {
            $pdf = PDF::setOptions(['isRemoteEnabled' => true, 'enable_remote' => true])->loadView('cotizador.cotizaciones.pdf.cotizacion');
            return $pdf->stream('Cotizacion N° ' . request("id") . ' ' . Carbon::now() . ' .pdf');
        }
        return view('cotizador.cotizaciones.pdf.cotizacion');
    }

    public function enviar_pdf(Request $request)
    {
        // dd(request()->all());

        // return view('cotizador.cotizaciones.pdf.cotizacion');
        $cotizacion = Cotizacion::withAll()->find(request("pdf_cotizacion_id"));
        // dd($cotizacion);
        view()->share('cotizacion', $cotizacion);
        // if ($request->has('download')) {
        $pdf = PDF::setOptions(['isRemoteEnabled' => true, 'enable_remote' => true])->loadView('cotizador.cotizaciones.pdf.cotizacion');
        // return $pdf->stream('Cotizacion N°' . request("id") . '-' . Carbon::now() . ' .pdf');
        // }
        // return view('cotizador.cotizaciones.pdf.cotizacion');

        //Feedback mail to client
        $pdf = PDF::loadView('cotizador.cotizaciones.pdf.cotizacion');
        $to = request('email');
        $data = ["vendedor" => auth()->user(), "nombre" => request("nombre")];
        Mail::send('email.pdf', ['data' => $data], function ($message) use ($to, $pdf) {
            $message->from('no-reply@invebchile.cl', 'CMPC');
            $message->to($to);
            $message->subject('Cotizacion CMPC');
            //Attach PDF doc
            $message->attachData($pdf->output(), 'Cotizacion N° ' . request("pdf_cotizacion_id") . ' ' . Carbon::now() . ' .pdf');
        });

        return response()->json([
            'mensaje' => "Correo enviado exitosamente",
        ], 200);
    }

    public function detalle_costos(Request $request)
    {
        $titulo = "Detalle Cotizacion N° " . request("id");
        $cotizacion = Cotizacion::withAll()->find(request("id"));
        // dd(number_format_unlimited_precision(str_replace(',', '.', number_format($cotizacion->detalles[0]->precios->costo_tinta_esquinero["usd_caja"], 10)), ",", ".", 10));
        // $precio_dolar = $cotizacion->detalles ? "" : $cotizacion->detalles[0]->precio_dolar;

        $detalles_esquineros_array[] = array(
            // "Planta", "Cód. Fiscal (Rut)", "Cliente",  "Cartón", "Descripción Material (Código)", "Cantidad (UN)", "Largo o Medida", "Número Colores", "Funda", "Strech Film",    "Clisse",    "Matriz",    "Royalty",    "Armado", "Maquila", "Servicios Maquila",    "CAD",    "Destino",    "Pallets Apilados",    "Moneda", "Diás Pago", "% Comisión", "Valor Dolar", "	Gramaje Cartón (gr/m2)", "Precio  (USD/Mm2)", "Margen (USD/Mm2)", "Precio  (USD/UN)", "Precio  (CLP/UN)", "Costo Directo (USD/Mm2)",    "Costo Indirecto (USD/Mm2)",    "GVV (USD/Mm2)",    "Recargo Financiero (USD/Mm2)", "FOB (USD/CAJA)",   "Papeles (USD/Mm2)",    "Desperdicio Papel (%)",    "Merma Corrugadora (%)",    "Merma Convertidora (%)",    "Merma Ceresinado (%)",    "Adhesivos (UUSD/Mm2)", "	Cartulina (USD/Mm2)",    "Adhesivos Cartulina (USD/Mm2)",    "Tintas (USD/Mm2)",    "Cera (USD/Mm2)",    "Cinta Desgarro (USD/Mm2)",    "Adhesivo Pegado (USD/Mm2)",    "Clisses (USD/Mm2)",    "Matriz (USD/Mm2)",    "Pallet (USD/Mm2)",    "Zuncho (USD/Mm2)",    "Funda (USD/Mm2)",    "Stretch Film (USD/Mm2)",    "Energía Eléctrica (USD/Mm2)",    "Gas Caldera (USD/Mm2)",    "Gas Grúa (USD/Mm2)",    "Flete (USD/Mm2)",     "Royalty (USD/Mm2)",    "Comisión Agente (USD/Mm2)"
            "Planta",
            "Cód. Fiscal (Rut)",
            "Cliente",
            "Cartón",
            "Descripción Material (Código)",
            "Cantidad (UN)",
            "Largo o Medida",
            "Número Colores",
            "Funda",
            "Clisse",
            "Maquila",
            "CAD",
            "Destino",
            "Moneda",
            "Diás Pago",
            "% Comisión",
            "Valor Dolar",
            "Precio  (USD/Mm2)",
            "Margen (USD/Mm2)",
            "Precio  (USD/UN)",
            "Precio  (CLP/UN)",
            "Precio  (USD/Ton)",
            "Costo Directo (USD/UN)",
            "Costo Indirecto (USD/UN)",
            "GVV (USD/UN)",
            "Recargo Financiero (USD/UN)",
            "Papeles (USD/UN)",
            "Adhesivos (USD/UN)",
            "Tintas (USD/UN)",
            "Clisses (USD/UN)",
            "Materiales de Embalaje (USD/UN)",
            "Energía Eléctrica (USD/UN)",
            "Flete (USD/UN)",
            "Comisión Agente (USD/Mm2)"

        );

        $detalles_corrugados_array[] = array(
            "Planta", 
            "Cód. Fiscal (Rut)", 
            "Cliente", 
            "Rubro", 
            "Tipo Item", 
            "Cartón",   
            "Descripción Material (Código)", 
            "Cantidad (UN)", 
            "Área HC (M2)",    
            "Ancho HM (mm)", 
            "Largo HM (mm)", 
            "Golpes Largo", 
            "Golpes Ancho",
            "Maquina Impresora",
            "Tipo de Impresión", 
            "Número Colores",    
            //"% Impresión",
            "Clissé por un golpe (cm2)",
            "Cobertura Color (%)",
            "Barniz",
            "Tipo Barniz",
            "Cobertura Barniz (cm2)",
            "Proceso", 
            "Tipo de Pegado",
            "Cinta Desgarro",
            'Pallet',
            "Altura Pallet", 
            "Zuncho", 
            "Funda", 
            "Strech Film",    
            "Clisse",    
            "Matriz",    
            "Royalty",    
            "Armado", 
            "Maquila", 
            "Servicios Maquila",    
            "CAD",    
            "Destino",    
            "Pallets Apilados",    
            "Moneda", 
            "Diás Pago",
            "% Comisión", 
            "Valor Dolar", 
            "Gramaje Cartón (gr/m2)", 
            "Precio  (USD/Mm2)", 
            "Margen (USD/Mm2)", 
            "Precio  (USD/UN)",
            "Precio  (CLP/UN)", 
            "Costo Directo (USD/Mm2)",    
            "Costo Indirecto (USD/Mm2)",    
            "GVV (USD/Mm2)",    
            "Recargo Financiero (USD/Mm2)", 
            "FOB (USD/CAJA)",   
            "Papeles (USD/Mm2)",    
            "Desperdicio Papel (%)",    
            "Merma Corrugadora (%)",    
            "Merma Convertidora (%)",    
           // "Merma Ceresinado (%)",    
            "Adhesivos (UUSD/Mm2)", 
           // "Cartulina (USD/Mm2)",    
           // "Adhesivos Cartulina (USD/Mm2)",    
            "Tintas (USD/Mm2)",    
            //"Cera (USD/Mm2)", 
            "Barniz (USD/Mm2)",   
            "Cinta Desgarro (USD/Mm2)",    
            "Adhesivo Pegado (USD/Mm2)",    
            "Clisses (USD/Mm2)",    
            "Matriz (USD/Mm2)",    
            "Pallet (USD/Mm2)",    
            "Zuncho (USD/Mm2)",    
            "Funda (USD/Mm2)",    
            "Stretch Film (USD/Mm2)",    
            "Energía Eléctrica (USD/Mm2)",    
            "Gas Caldera (USD/Mm2)",    
            "Gas Grúa (USD/Mm2)",    
            "Flete (USD/Mm2)",   
            "Maquila (USD/Mm2)",  
            "Royalty (USD/Mm2)",    
            "Comisión Agente (USD/Mm2)", 
            "Perdida de Productividad (USD/Mm2)",
            "Costo Fijo Planta (USD/Mm2)",
            "Costo de Servir (USD/Mm2)",
            "Costo fijo Administrativo (USD/UN)",
        );


        // dd($detalles);
        foreach ($cotizacion->detalles as $detalle) {
            if($detalle->pallet > 1){
                $pallet='SI';
            }else{
                if($detalle->pallet == 0){
                    $pallet='NO';
                }else{
                    if($detalle->pallet==1){
                        if($detalle->created_at > '2021-07-01 00:00:00'){
                            $pallet='NO';
                        }else{
                            $pallet='SI';
                        }
                    }else{
                        $pallet='NO';
                    }
                }                
            }

            if ($detalle->tipo_detalle_id == 2) {
                
                $detalles_esquineros_array[] = array(
                    "Planta" => $detalle->planta->nombre,
                    "Cód. Fiscal (Rut)" => $detalle->cotizacion->client->rut,
                    "Cliente" => $detalle->cotizacion->client->nombre,
                    "Cartón" => $detalle->carton_esquinero->codigo,
                    "Descripción Material (Código)" => $detalle->codigo_material_detalle,
                    "Cantidad (UN)" => $detalle->cantidad,
                    "Largo o Medida" => $detalle->largo_esquinero,
                    "Número Colores" => $detalle->numero_colores,
                    "Funda" => $detalle->funda_esquinero != null ? [0 => "NO", 1 => "SI"][$detalle->funda_esquinero] : "NO",
                    "Clisse" => $detalle->clisse != null ? [0 => "NO", 1 => "SI"][$detalle->clisse] : "NO",
                    "Maquila" => $detalle->maquila != null ? [0 => "NO", 1 => "SI"][$detalle->maquila] : "NO",
                    "CAD" => $detalle->cad_material_detalle,
                    "Destino" => $detalle->flete->ciudad,
                    "Moneda" => $cotizacion->moneda_id == 1 ? "USD" : "CLP",
                    "Diás Pago" => $cotizacion->dias_pago,
                    "% Comisión" => $cotizacion->comision,
                    "Valor Dolar" => $detalle->detalle_valor_dolar,
                    "Precio  (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->precio_total["usd_mm2"]), ",", ".", 1),
                    "Margen (USD/Mm2)" =>  number_format_unlimited_precision(str_replace(',', '.', $detalle->margen)),
                    "Precio  (USD/UN)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->precio_total["usd_caja"])),
                    "Precio  (CLP/UN)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->precio_total["clp_caja"]),  ",", ".", 1),
                    "Precio  (USD/Ton)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->precio_total["usd_ton"]), ",", ".", 1),
                    "Costo Directo (USD/UN)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_directo["usd_caja"])),
                    "Costo Indirecto (USD/UN)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_indirecto["usd_caja"])),
                    "GVV (USD/UN)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_gvv["usd_caja"])),
                    "Recargo Financiero (USD/UN)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_financiamiento["usd_caja"])),
                    "Papeles (USD/UN)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_papel_esquinero["usd_caja"])),
                    "Adhesivos (USD/UN)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_adhesivo_esquinero["usd_caja"])),
                    "Tintas (USD/UN)" => number_format_unlimited_precision(str_replace(',', '.', number_format($detalle->precios->costo_tinta_esquinero["usd_caja"], 10)), ",", ".", 10),
                    "Clisses (USD/UN)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_clisses_esquinero["usd_caja"])),
                    // "Funda (USD/UN)" =>number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_adhesivo_esquinero["usd_mm2"])),
                    "Materiales de Embalaje (USD/UN)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_embalaje_esquinero["usd_caja"])),
                    "Energía Eléctrica (USD/UN)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_energia_esquinero["usd_caja"])),
                    "Flete (USD/UN)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_flete_esquinero["usd_caja"])),
                    "Comisión Agente (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_comision_esquinero["usd_mm2"])),


                );

            } else {

                $detalles_corrugados_array[] = array(
                    "Planta" => $detalle->planta->nombre,
                    "Cód. Fiscal (Rut)" => $detalle->cotizacion->client->rut,
                    "Cliente" => $detalle->cotizacion->client->nombre,
                    "Rubro" => $detalle->rubro->descripcion,
                    "Tipo Item" => $detalle->productType->descripcion,
                    "Cartón" => $detalle->carton->codigo,
                    "Descripción Material (Código)" => $detalle->codigo_material_detalle,
                    "Cantidad (UN)" => $detalle->cantidad,
                    "Área HC (M2)" => $detalle->area_hc,
                    "Ancho HM (mm)" => $detalle->anchura,
                    "Largo HM (mm)" => $detalle->largura,
                    "Golpes Largo" => $detalle->golpes_largo,
                    "Golpes Ancho" => $detalle->golpes_ancho,
                    //"% Cera Interior" => $detalle->porcentaje_cera_interno != null ? $detalle->porcentaje_cera_interno : "0",
                    //"% Cera Exterior" => $detalle->porcentaje_cera_externo != null ? $detalle->porcentaje_cera_externo : "0",
                    "Maquina Impresora" => $detalle->printing_machine_id != null ? $detalle->maquinaImpresora->descripcion : null,
                    "Tipo de Impresión" => $detalle->print_type_id != null ? $detalle->printType->descripcion : null,
                    "Número Colores" => $detalle->numero_colores,
                    //"% Impresión" => $detalle->impresion != null ? $detalle->impresion : "0",
                    "Clisse por un golpe (cm2)" => $detalle->cobertura_color_cm2,
                    "Cobertura Color (%)" => $detalle->cobertura_color_percent,
                    "Barniz" => $detalle->barniz != null ? [0 => "NO", 1 => "SI"][$detalle->barniz] : "NO",
                    "Tipo Barniz" => $detalle->barniz_type_id != null ? $detalle->barnizType->descripcion : null,
                    "Cobertura Barniz (cm2)" => $detalle->cobertura_barniz_cm2,
                    "Proceso" => ($detalle->proceso) ? $detalle->proceso->descripcion : "",
                    "Tipo de Pegado" => $detalle->pegado_id != null ? $detalle->pegadoDescripcion->descripcion : null,
                    "Cinta Desgarro" => $detalle->cinta_desgarro != null ? [0 => "NO", 1 => "SI"][$detalle->cinta_desgarro] : "NO",
                    "Pallet" => $pallet,//$detalle->pallet != null ? [0 => "NO", 1 => "SI", 2=> "SI"][$detalle->pallet] : "NO",
                    "Altura Pallet (mm)" => $detalle->pallet_height_id != null ? $detalle->palletHeight->descripcion : null,
                    "Zuncho" =>  $detalle->zuncho != null ? $detalle->zunchoDescripcion->descripcion : null,
                    "Funda" => $detalle->funda != null ? [0 => "NO", 1 => "SI"][$detalle->funda] : "NO",
                    "Strech Film" => $detalle->stretch_film != null ? [0 => "NO", 1 => "SI"][$detalle->stretch_film] : "NO",
                    "Clisse" => $detalle->clisse != null ? [0 => "NO", 1 => "SI"][$detalle->clisse] : "NO",
                    "Matriz" => $detalle->matriz != null ? [0 => "NO", 1 => "SI"][$detalle->matriz] : "NO",
                    "Royalty" => $detalle->royalty != null ? [0 => "NO", 1 => "SI"][$detalle->royalty] : "NO",
                    "Armado" => $detalle->armado_automatico != null ? [0 => "NO", 1 => "SI"][$detalle->armado_automatico] : "NO",
                    "Maquila" => $detalle->maquila != null ? [0 => "NO", 1 => "SI"][$detalle->maquila] : "NO",
                    "Servicios Maquila" => $detalle->maquila == 1 && $detalle->servicio_maquila ? $detalle->servicio_maquila->servicio : null,
                    "CAD" => $detalle->cad_material_detalle,
                    "Destino" => $detalle->flete->ciudad,
                    "Pallets Apilados" => $detalle->pallets_apilados,
                    "Moneda" => $cotizacion->moneda_id == 1 ? "USD" : "CLP",
                    "Diás Pago" => $cotizacion->dias_pago,
                    "% Comisión" => $cotizacion->comision,
                    "Valor Dolar" => $detalle->detalle_valor_dolar,
                    "Gramaje Cartón (gr/m2)" => $detalle->gramajeCarton,
                    "Precio  (USD/Mm2)" => (isset($detalle->precios->precio_final))?number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->precio_final["usd_mm2"]), ",", ".", 1):number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->precio_total["usd_mm2"]), ",", ".", 1),
                    "Margen (USD/Mm2)" =>  number_format_unlimited_precision(str_replace(',', '.', $detalle->margen)),
                    "Precio  (USD/UN)" => (isset($detalle->precios->precio_final))?number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->precio_final["usd_caja"])):number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->precio_total["usd_caja"])),
                    "Precio  (CLP/UN)" => (isset($detalle->precios->precio_final))?number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->precio_final["clp_caja"]),  ",", ".", 1):number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->precio_total["clp_caja"]),  ",", ".", 1),
                    "Costo Directo (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_directo["usd_mm2"])),
                    "Costo Indirecto (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_indirecto["usd_mm2"])),
                    "GVV (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_gvv["usd_mm2"])),
                    "Recargo Financiero (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_financiamiento["usd_mm2"])),
                    "FOB (USD/CAJA)" => number_format_unlimited_precision(str_replace(',', '.', ($detalle->precios->costo_materia_prima["usd_caja"] + $detalle->precios->costo_materiales_operacion["usd_caja"] + $detalle->precios->costo_materiales_embalaje["usd_caja"] + $detalle->precios->costo_fabricacion["usd_caja"] + $detalle->precios->costo_flete["usd_caja"]))),
                    "Papeles (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_papel["usd_mm2"])),
                    "Desperdicio Papel (%)" => $detalle->desperdicio_papel * 100,
                    "Merma Corrugadora (%)" => $detalle->merma_corrugadora,
                    "Merma Convertidora (%)" => $detalle->merma_convertidora,
                    //"Merma Ceresinado (%)" => (($detalle->porcentaje_cera_interno + $detalle->porcentaje_cera_externo) > 0) ? $detalle->planta->merma_cera : "0",
                    "Adhesivos (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_adhesivo["usd_mm2"])),
                    //"Cartulina (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_cartulina["usd_mm2"])),
                    //"Adhesivos Cartulina (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_adhesivo_cartulina["usd_mm2"])),
                    //"Tintas (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_tinta["usd_mm2"])),
                    "Tintas (USD/Mm2)" => (isset($detalle->precios->costo_tinta_new))?number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_tinta_new["usd_mm2"])):number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_tinta["usd_mm2"])),
                    //"Cera (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_cera["usd_mm2"])),
                    //"Barniz (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_barniz["usd_mm2"])),
                    "Barniz (USD/Mm2)" => (isset($detalle->precios->costo_barniz_new))?number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_barniz_new["usd_mm2"])):number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_barniz["usd_mm2"])),
                    "Cinta Desgarro (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_cinta["usd_mm2"])),
                    "Adhesivo Pegado (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_adhesivo_pegado["usd_mm2"])),
                    "Clisses (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_clisses["usd_mm2"])),
                    "Matriz (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_matriz["usd_mm2"])),
                    "Pallet (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_pallet["usd_mm2"])),
                    "Zuncho (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_zuncho["usd_mm2"])),
                    "Funda (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_funda["usd_mm2"])),
                    "Stretch Film (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_stretch_film["usd_mm2"])),
                    "Energía Eléctrica (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_energia["usd_mm2"])),
                    "Gas Caldera (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_gas_caldera["usd_mm2"])),
                    "Gas Grúa (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_gas_gruas["usd_mm2"])),
                    "Flete (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_flete["usd_mm2"])),
                    // "Recargo Financiero (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_cartulina["usd_mm2"])),
                    "Maquila (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_maquila["usd_mm2"])),
                    "Royalty (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_royalty["usd_mm2"])),
                    "Comisión Agente (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_comision["usd_mm2"])),
                    "Perdida de Productividad (USD/Mm2)" => number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_perdida_productividad["usd_mm2"])),
                    "Costo Fijo Planta (USD/Mm2)" => (isset($detalle->precios->costo_fijo_total))?number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_fijo_total["usd_mm2"])):0,
                    "Costo de Servir (USD/Mm2)" => (isset($detalle->precios->costo_servir_sin_flete))?number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_servir_sin_flete["usd_mm2"])):0,
                    "Costo fijo Administrativo (USD/UN)" => (isset($detalle->precios->costo_administrativos))?number_format_unlimited_precision(str_replace(',', '.', $detalle->precios->costo_administrativos["usd_mm2"])):0,
                );

            }
        }
        
        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($detalles_corrugados_array, $detalles_esquineros_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet("Corrugados", function ($sheet) use ($detalles_corrugados_array) {
                $sheet->fromArray($detalles_corrugados_array, null, 'A1', true, false);
            });
            $excel->sheet("Esquineros", function ($sheet) use ($detalles_esquineros_array) {
                $sheet->fromArray($detalles_esquineros_array, null, 'A1', true, false);
            });
        })->download('xlsx');
    }

    public function detalles_corrugados(Request $request)
    {
        $titulo = "Corrugados Cotizacion N° " . request("id");
        $cotizacion = Cotizacion::withAll()->find(request("id"));

        $detalles_array[] = array(
            'Planta', 'Rubro', 'Cartón', 'Item', 'Anchura (mm)', 'Largura  (mm)', 'Area (M2)', 'Proceso', 'Un x golpe al Largo', 'Un x golpe al Ancho', 'Colores', '% Cobertura', '% Cera Exterior', '% Cera Interior', 'Cinta Desgarro', 'Pallet', 'Zuncho', 'Funda', 'Stretch Film', 'Matriz', 'Royalty', 'Clichés', 'Margen', 'Maquila', 'Valor Armado', 'Cod_Interno_Cliente', 'Ancho Pliego Cartulina (mm)', 'Largo Pliego Cartulina ($/UN)', 'Precio Pliego Cartulina ($/UN)', 'Precio Impresión Pliego ($/UN)', 'GP Emplacado (UN/GP)', 'Descripción', 'Codigo Material', 'CAD', 'Largo', 'Ancho', 'Alto', 'BCT MIN (LB)', 'BCT MIN (KG)', 'Lugar Destino', 'Cantidad'
        );

        $cotizacion->detalles = $cotizacion->detalles->filter(function ($detalle) {
            return $detalle->tipo_detalle_id == 1;
        });
        // dd($cotizacion->detalles);
        foreach ($cotizacion->detalles as $detalle) {
            $detalles_array[] = array(
                'Planta' => $detalle->planta->nombre,
                'Rubro' => $detalle->rubro->descripcion,
                'Cartón' => $detalle->carton->codigo,
                'Item' => $detalle->productType->descripcion,
                'Anchura (mm)' => $detalle->anchura,
                'Largura  (mm)' => $detalle->largura,
                'Area (M2)' => $detalle->area_hc,
                'Proceso' => $detalle->proceso->descripcion,
                'Un x golpe al Largo' => $detalle->golpes_largo,
                'Un x golpe al Ancho' => $detalle->golpes_ancho,
                'Colores' => $detalle->numero_colores,
                '% Cobertura' => $detalle->impresion != null ? $detalle->impresion : "0",
                '% Cera Exterior' => $detalle->porcentaje_cera_interno != null ? $detalle->porcentaje_cera_interno : "0",
                '% Cera Interior' => $detalle->porcentaje_cera_externo != null ? $detalle->porcentaje_cera_externo : "0",
                'Cinta Desgarro' => $detalle->cinta_desgarro != null ? [0 => "NO", 1 => "SI"][$detalle->cinta_desgarro] : "NO",
                'Pallet' => $detalle->pallet != null ? [0 => "NO", 1 => "NO", 2 => "Madera"][$detalle->pallet] : "NO",
                'Zuncho' => $detalle->zuncho != null ? [0 => "NO", 1 => "NO", 2 => "Al Bulto", 3 => "Al Pallet (Se cobra maquila)"][$detalle->zuncho] : "NO",
                'Funda' => $detalle->funda != null ? [0 => "NO", 1 => "SI"][$detalle->funda] : "NO",
                'Stretch Film' => $detalle->stretch_film != null ? [0 => "NO", 1 => "SI"][$detalle->stretch_film] : "NO",
                'Matriz' => $detalle->matriz != null ? [0 => "NO", 1 => "SI"][$detalle->matriz] : "NO",
                'Royalty' =>  $detalle->royalty != null ? [0 => "NO", 1 => "SI"][$detalle->royalty] : "NO",
                'Clichés' =>  $detalle->clisse != null ? [0 => "NO", 1 => "SI"][$detalle->clisse] : "NO",
                'Margen' => $detalle->margen,
                'Maquila' => $detalle->maquila == 1 && $detalle->servicio_maquila ? $detalle->servicio_maquila->servicio : null,
                'Valor Armado' => isset($detalle->armado_usd_caja) && is_numeric($detalle->armado_usd_caja) ? $detalle->armado_usd_caja : null,
                'Cod_Interno_Cliente' => $detalle->codigo_cliente,
                'Ancho Pliego Cartulina (mm)' => (isset($detalle->ancho_pliego_cartulina) && ($detalle->proceso->id == 7 || $detalle->proceso->id == 9)) ? $detalle->ancho_pliego_cartulina : null,
                'Largo Pliego Cartulina ($/UN)' => (isset($detalle->largo_pliego_cartulina) && ($detalle->proceso->id == 7 || $detalle->proceso->id == 9)) ? $detalle->largo_pliego_cartulina : null,
                'Precio Pliego Cartulina ($/UN)' => (isset($detalle->precio_pliego_cartulina) && ($detalle->proceso->id == 7 || $detalle->proceso->id == 9)) ? $detalle->precio_pliego_cartulina : null,
                'Precio Impresión Pliego ($/UN)' => (isset($detalle->precio_impresion_pliego) && ($detalle->proceso->id == 7 || $detalle->proceso->id == 9)) ? $detalle->precio_impresion_pliego : null,
                'GP Emplacado (UN/GP)' => (isset($detalle->gp_emplacado) && ($detalle->proceso->id == 7 || $detalle->proceso->id == 9)) ? $detalle->gp_emplacado : null,
                'Descripción' => $detalle->descripcion_material_detalle,
                'Codigo Material' => $detalle->codigo_material_detalle,
                'CAD' => $detalle->cad_material_detalle,
                'Largo' => $detalle->largo,
                'Ancho' => $detalle->ancho,
                'Alto' => $detalle->alto,
                'BCT MIN (LB)' => $detalle->bct_min_lb,
                'BCT MIN (KG)' => $detalle->bct_min_kg,
                'Lugar Destino' => $detalle->flete->ciudad,
                'Cantidad' => $detalle->cantidad,

            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($detalles_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('CORRUGADO', function ($sheet) use ($detalles_array) {
                $sheet->fromArray($detalles_array, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    public function detalles_esquineros(Request $request)
    {
        $titulo = "Esquineros Cotizacion N° " . request("id");
        $cotizacion = Cotizacion::withAll()->find(request("id"));

        $detalles_array[] = array(
            'Planta',  'Cartón', 'Destino', 'Transporte', 'Largo o Medida', 'Incluye Funda', 'Colores', 'Margen', 'Maquila', 'Clisse', 'Descripción', 'Material', 'CAD', 'Lugar Destino', 'Cantidad'

        );
        // "Planta", "Tipo de Producto", "Cód. Fiscal (Rut)", "Cliente", "Rubro", "Tipo Item", "Cartón",   "Descripción Material (Código)", "Cantidad (UN)", "Área HC (M2)",    "Ancho HM (mm)", "Largo HM (mm)", "Golpes Largo", "Golpes Ancho", "% Cera Interior", "% Cera Exterior", "Número Colores",    "% Impresión", "Proceso",    "Zunchos", "Funda", "Strech Film", "Pallet",    "Clisse",    "Matriz",    "Royalty",    "Armado", "Maquila", "Servicios Maquila",    "CAD",    "Destino",    "Pallets Apilados"

        $cotizacion->detalles = $cotizacion->detalles->filter(function ($detalle) {
            return $detalle->tipo_detalle_id == 2;
        });
        // dd($cotizacion->detalles);
        foreach ($cotizacion->detalles as $detalle) {
            $detalles_array[] = array(
                'Planta' => $detalle->planta->nombre,
                'Cartón' => $detalle->carton_esquinero->codigo,
                'Destino' => isset($detalle->tipo_destino_esquinero) ? [1 => "Tarima Nacional", 2 => "Empaque Exportación (Granel)", 3 =>  "Tarima de Exportación"][$detalle->tipo_destino_esquinero] : null,
                'Transporte' => isset($detalle->tipo_camion_esquinero) ? [1 => "Camión 7x2,6mts", 2 => "Camión 12x2,6mts"][$detalle->tipo_camion_esquinero] : null,
                'Largo o Medida' => $detalle->largo_esquinero,
                'Incluye Funda' => $detalle->funda_esquinero != null ? [0 => "NO", 1 => "SI"][$detalle->funda_esquinero] : "NO",
                'Colores' => $detalle->numero_colores,
                'Margen' => $detalle->margen,
                'Maquila' => $detalle->maquila != null ? [0 => "NO", 1 => "SI"][$detalle->maquila] : "NO",
                'Clisse' => $detalle->clisse != null ? [0 => "NO", 1 => "SI"][$detalle->clisse] : "NO",
                'Descripción' => $detalle->descripcion_material_detalle,
                'Codigo Material' => $detalle->codigo_material_detalle,
                'CAD' => $detalle->cad_material_detalle,
                'Lugar Destino' => $detalle->flete->ciudad,
                'Cantidad' => $detalle->cantidad,
            );
        }

        Excel::create($titulo . ' ' . Carbon::now(), function ($excel) use ($detalles_array, $titulo) {
            $excel->setTitle($titulo);
            $excel->sheet('ESQUINERO', function ($sheet) use ($detalles_array) {
                $sheet->fromArray($detalles_array, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    public function ayuda()
    {
        return view('cotizador.cotizaciones.ayuda');
    }

    public function obtenerMargenPapeles($detalle)
    {

        $margen_papeles=0;

        $carton = $detalle->carton;
        $onda_1=$carton["onda_1"];
        $planta= $detalle->planta;
        $desperdicio_papel= $detalle->desperdicio_papel;
        $merma_corrugadora = $detalle->merma_corrugadora;
        $merma_convertidora = $detalle->merma_convertidora;

        
        $factor_onda_1=FactoresOnda::where('planta_id',$planta->id)
                                    ->where('onda',$onda_1)
                                    ->first();
        
       // dd($factor_onda["factor_onda"]);
        
        //dd(isset($carton["onda_powerplay"]));
        $mc_papel_tapa_interior = (isset($carton["tapa_interior"]))? $carton["tapa_interior"]["mc_usd_ton"]  : 0 ;
        $mc_papel_primera_onda  = (isset($carton["primera_onda"]))? $carton["primera_onda"]["mc_usd_ton"]  : 0;
        $mc_papel_powerplay     = (isset($carton["onda_powerplay"]))? $carton["onda_powerplay"]["mc_usd_ton"] : 0;
        $mc_papel_tapa_media    = (isset($carton["tapa_media"]))? $carton["tapa_media"]["mc_usd_ton"] : 0 ;
        $mc_papel_segunda_onda  = (isset($carton["segunda_onda"]))? $carton["segunda_onda"]["mc_usd_ton"] : 0;
        $mc_papel_tapa_exterior = (isset($carton["tapa_exterior"]))? $carton["tapa_exterior"]["mc_usd_ton"] : 0;
        
        $gramaje_tapa_interior = (isset($carton["tapa_interior"]))? $carton["tapa_interior"]["gramaje"] : 0;
        $gramaje_primera_onda  = (isset($carton["tapa_interior"]))? $factor_onda_1->factor_onda * $carton["primera_onda"]["gramaje"] : 0;
        $gramaje_powerplay     = (isset($carton["onda_powerplay"]))? $carton["onda_powerplay"]["gramaje"] : 0;
        $gramaje_tapa_media    = (isset($carton["tapa_media"]))? $carton["tapa_media"]["gramaje"] : 0;
        $gramaje_segunda_onda  = (isset($carton["segunda_onda"]))? $carton["segunda_onda"]["gramaje"] : 0;
        $gramaje_tapa_exterior = (isset($carton["tapa_exterior"]))? $carton["tapa_exterior"]["gramaje"] : 0;
        
        

        $mc_usd_ton = ( ($mc_papel_tapa_interior * $gramaje_tapa_interior)  +
                        ($mc_papel_primera_onda *  $gramaje_primera_onda)   +
                        ($mc_papel_powerplay *  $gramaje_powerplay)   +
                        ($mc_papel_tapa_media *  $gramaje_tapa_media)   +
                        ($mc_papel_segunda_onda *  $gramaje_segunda_onda)   +
                        ($mc_papel_tapa_exterior *  $gramaje_tapa_exterior)
                        )/1000;
        
        $margen_papeles= ($mc_usd_ton) /((1-$desperdicio_papel)*(1-$merma_corrugadora)*(1-$merma_convertidora));
        
        return $margen_papeles;
    }

    public function solicitarAprobacionNew($id)
    {   
        $cotizacion = Cotizacion::withAll()->find($id);
        
        // Almacenar por detalle los resultados y calculos de precios de este
        foreach ($cotizacion->detalles as $detalle) {
            // dd(json_encode((array)$detalle->precios),$detalle->precios);
            $detalle->historial_resultados = $detalle->precios;
            // $detalle->historial_resultados = json_encode ((array)$detalle->precios);
            $detalle->save();
        }

        // Iterar sobre los detalles de la cotizacion para encontrar margenes maximos
        $margen = $this->calcular_margen($cotizacion);

        //Rechazo Automatico
        if($cotizacion->client->clasificacion == 4 && $margen != 0){

            $aprobacion = new CotizacionApproval;
            $aprobacion->motivo = "Presencia Rentable y no se pone un margen igual o mayor al minimo";
            $aprobacion->role_do_action = 1;
            $aprobacion->action_made = "Rechazo Automático";
            $aprobacion->user_id = 1;
            $aprobacion->cotizacion_id = $cotizacion->id;
            $aprobacion->save();


            $cotizacion->estado_id = 6;
            $cotizacion->save();
            return response()->json(["url" => route('cotizador.index_cotizacion'), "margen" => 0]);
        }
        
        // Role jefe de ventas siempre es el primero en aprobar de requerir aprobacion
        $por_aprobar = null;
        $nivel_aprobacion = null;
        $mc_bruto_negativo = false;
        $estado = 2;
        // Si el margen esta dentro del lo sugerido la cotizacion es aprobada inmediatamente
        // Si es jefe de venta el margen para aprobar inmediatamente sube a 10
        
        if($margen <= 0){ //La cotizacion es liberada
            $estado = 3;
            $mc_bruto_negativo = false;
        }elseif ($margen < 4) {// Aprueba el Jefe de Area
            $por_aprobar = 3;
            $nivel_aprobacion = 1;
            $mc_bruto_negativo = false;
            
        }else{// Aprueba el Gerente Comercial
            $por_aprobar = 15;
            $nivel_aprobacion = 1; 
            $mc_bruto_negativo = $this->calcular_mc_bruto($cotizacion);
        }            
        
        $cotizacion->role_can_show = $por_aprobar;
        $cotizacion->nivel_aprobacion = $nivel_aprobacion;
        $cotizacion->estado_id = $estado;
        if($mc_bruto_negativo){
            $cotizacion->enviar_a_comite = 1;
        }else{
            $cotizacion->enviar_a_comite = 0;
        }
        $cotizacion->save();


        if(Auth()->user()->isVendedorExterno()){
            //return redirect()->route('cotizador.index_cotizacion_externo');
            return response()->json(["url" => route('cotizador.index_cotizacion_externo'), "margen" => 0]);
        }else{
            return response()->json(["url" => route('cotizador.index_cotizacion'), "margen" => 0]);
        }
        
    }

    public function calcular_mc_bruto($cotizacion)
    {   
        $mc_bruto_negativo = false;
        $margen_total=0;
        $costo_administratito_total =0;
        $costo_de_servir_total =0;

        foreach ($cotizacion->detalles as $detalle) {
            $mc_bruto_result = $detalle->margen + $detalle->precios->costo_administrativos["usd_mm2"] + $detalle->precios->costo_servir_sin_flete["usd_mm2"];
            if($mc_bruto_result<0){
                $mc_bruto_negativo = true;
            }else{
                $mc_bruto_negativo = false;
            }
        }       

        return $mc_bruto_negativo;
    }
}
