<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;
use App\PorcentajeMargen;
use stdClass;

class DetalleCotizacion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $appends = ['precios'];
    protected $guarded = [];

    protected $casts = [
        'historial_resultados' => 'array', // Will convert to (Array)
        'detalle_maquila_servicio_id' => 'array', // Will convert to (Array)
    ];

    // Funcion para cargar todas las relaciones en un call
    public function scopeWithAll($query)
    {
        $query->with(
            'rubro.mermas_convertidora',
            'subsubhierarchy.subhierarchy.hierarchy',
            'users',
            'carton.mermas_corrugadora',
            'carton.tapa_interior',
            'carton.tapa_media',
            'carton.tapa_exterior',
            'carton.primera_onda',
            'carton.segunda_onda',
            'carton_esquinero.papel_1',
            'carton_esquinero.papel_2',
            'carton_esquinero.papel_3',
            'carton_esquinero.papel_4',
            'carton_esquinero.papel_5',
            'productType',
            'proceso',
            'planta.factores_onda',
            'planta.consumos_adhesivo',
            'planta.consumos_energia',
            'cotizacion',
            'flete',
            'variables_cotizador',
            'detalles_hermanos',
            'pallet_height'
        );
    }

    public function detalles_hermanos()
    {
        return $this->hasMany(DetalleCotizacion::class, 'cotizacion_id', 'cotizacion_id')
            ->where('id', '!=', $this->id)->select(array('id', 'area_hc', 'process_id', 'carton_id', 'largura', 'anchura', 'cotizacion_id'));
    }

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class)->select(array('id', 'estado_id', 'user_id', 'client_id', 'comision', 'dias_pago','moneda_id'));
    }

    public function flete()
    {
        return $this->belongsTo(CiudadesFlete::class, 'ciudad_id');
    }

    public function rubro()
    {
        return $this->belongsTo(Rubro::class);
    }
    public function subsubhierarchy()
    {
        return $this->belongsTo(Subsubhierarchy::class);
    }
    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function carton()
    {
        return $this->belongsTo(Carton::class);
    }
    public function carton_esquinero()
    {
        return $this->belongsTo(CartonEsquinero::class, "carton_esquinero_id");
    }
    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }
    public function armado()
    {
        return $this->belongsTo(Armado::class);
    }
    public function servicio_maquila()
    {
        return $this->belongsTo(MaquilaServicio::class, "maquila_servicio_id");
    }
    public function proceso()
    {
        return $this->belongsTo(Process::class, "process_id");
    }
    public function planta()
    {
        return $this->belongsTo(Planta::class);
    }
    public function detalle_palletizado()
    {
        return $this->belongsTo(DetallePrecioPalletizado::class, "tipo_destino_esquinero");
    }

    public function variables_cotizador()
    {
        return $this->belongsTo(VariablesCotizador::class, "variable_cotizador_id");
    }

    public function getPrecioDolarAttribute()
    {
        return $this->variables_cotizador->precio_dolar;
    }

    public function coverageType()
    {
        return $this->belongsTo(CoverageType::class, 'coverage_type_id');
    }

    public function printType()
    {
        return $this->belongsTo(PrintType::class, 'print_type_id');
    }

    public function inkType()
    {
        return $this->belongsTo(InkType::class, 'ink_type_id');
    }

    public function pallet_height()
    {
        return $this->belongsTo(PalletHeight::class, "pallet_height_id");
    }

    public function getPalletHeighValuetAttribute()
    {
        return $this->pallet_height->descripcion;
    }

    public function zunchoDescripcion()
    {
        return $this->belongsTo(Zuncho::class, 'zuncho');
    }

    public function maquinaImpresora()
    {
        return $this->belongsTo(PrintingMachine::class, 'printing_machine_id');
    }

    public function barnizType()
    {
        return $this->belongsTo(TipoBarniz::class, 'barniz_type_id');
    }

    public function pegadoDescripcion()
    {
        return $this->belongsTo(Pegado::class, 'pegado_id');
    }

    public function palletHeight()
    {
        return $this->belongsTo(PalletHeight::class, 'pallet_height_id');
    }

    // FORMULAS 
    // Listado de variables segun unidades
    public function getPreciosAttribute()
    {
        $time_start = microtime(true);
        $precios = [];
        // dd($this->cotizacion);
        if ($this->cotizacion_id != 0 && isset($this->cotizacion->estado_id) && $this->cotizacion->estado_id != 1 && isset($this->historial_resultados)) {
            $precios = (object) $this->historial_resultados;
            // Display Script End time
            $time_end = microtime(true);
            $execution_time = ($time_end - $time_start);
            // dd($time_start, $time_end, $execution_time,$precios);
            // dd($precios->costo_papel["usd_caja"],($this->preciosCorrugados()->costo_papel["usd_caja"]));
            return $precios;
        }
        switch ($this->tipo_detalle_id) {
            case 1:
                $precios = $this->preciosCorrugados();
                break;
            case 2:
                $precios = $this->preciosEsquineros();
                break;
            default:
                # code...
                break;
        }
        return $precios;
    }

    public function preciosCorrugados()
    {
        //place this before any script you want to calculate time
        //$time_start = microtime(true);
        //usd_mm2 la unidad mm2 es "Mil metros cuadrados"
        $precios = new stdClass();

        ////Costos Directos - Inicio
            //Costo Papel
            $precios->costo_papel = ["usd_mm2" => $this->costo_papel("usd_mm2"), 
                                     "usd_ton" => $this->costo_papel("usd_ton"), 
                                     "usd_caja" => $this->costo_papel("usd_caja")];
            //Costo Adhesivo
            $precios->costo_adhesivo = ["usd_mm2" => $this->costo_adhesivo("usd_mm2"), 
                                        "usd_ton" => $this->costo_adhesivo("usd_ton"), 
                                        "usd_caja" => $this->costo_adhesivo("usd_caja")];
            //Costo Tinta
            $precios->costo_tinta = ["usd_mm2" => $this->costo_tinta_new("usd_mm2"), 
                                     "usd_ton" => $this->costo_tinta_new("usd_ton"), 
                                     "usd_caja" => $this->costo_tinta_new("usd_caja")];
            //Costo Barniz
            $precios->costo_barniz = ["usd_mm2" => $this->costo_barniz_new("usd_mm2"), 
                                      "usd_ton" => $this->costo_barniz_new("usd_ton"), 
                                      "usd_caja" => $this->costo_barniz_new("usd_caja")];
            //Costo Cinta
            $precios->costo_cinta = ["usd_mm2" => $this->costo_cinta("usd_mm2"), 
                                     "usd_ton" => $this->costo_cinta("usd_ton"), 
                                     "usd_caja" => $this->costo_cinta("usd_caja")];
            //Costo Adhesivo Pegado
            $precios->costo_adhesivo_pegado = ["usd_mm2" => $this->costo_adhesivo_pegado("usd_mm2"), 
                                               "usd_ton" => $this->costo_adhesivo_pegado("usd_ton"), 
                                               "usd_caja" => $this->costo_adhesivo_pegado("usd_caja")];

            //Total Costo Materia Prima
            $precios->costo_materia_prima = [
                "usd_mm2" =>    $precios->costo_papel["usd_mm2"] + 
                                $precios->costo_adhesivo["usd_mm2"] + 
                                $precios->costo_tinta["usd_mm2"] + 
                                $precios->costo_barniz["usd_mm2"] + 
                                $precios->costo_cinta["usd_mm2"] + 
                                $precios->costo_adhesivo_pegado["usd_mm2"],

                "usd_ton" =>    $precios->costo_papel["usd_ton"] + 
                                $precios->costo_adhesivo["usd_ton"] + 
                                $precios->costo_tinta["usd_ton"] + 
                                $precios->costo_barniz["usd_ton"] +
                                $precios->costo_cinta["usd_ton"] + 
                                $precios->costo_adhesivo_pegado["usd_ton"],

                "usd_caja" =>   $precios->costo_papel["usd_caja"] + 
                                $precios->costo_adhesivo["usd_caja"] + 
                                $precios->costo_tinta["usd_caja"] + 
                                $precios->costo_barniz["usd_caja"] +
                                $precios->costo_cinta["usd_caja"] + 
                                $precios->costo_adhesivo_pegado["usd_caja"],
            ];

            //Total Costo Directo
            $precios->costo_directo = [
                "usd_mm2" =>    $precios->costo_materia_prima["usd_mm2"],
                "usd_ton" =>    $precios->costo_materia_prima["usd_ton"],
                "usd_caja" =>   $precios->costo_materia_prima["usd_caja"],
            ];
        ////Costos Directos - Fin

        ////Costos Indirectos - Inicio
            //Costo Pallet
            $precios->costo_pallet = ["usd_mm2" => $this->costo_pallet("usd_mm2"), 
                                      "usd_ton" => $this->costo_pallet("usd_ton"), 
                                      "usd_caja" => $this->costo_pallet("usd_caja")];
            //Costo Zuncho
            $precios->costo_zuncho = ["usd_mm2" => $this->costo_zuncho_new("usd_mm2"), 
                                      "usd_ton" => $this->costo_zuncho_new("usd_ton"), 
                                      "usd_caja" => $this->costo_zuncho_new("usd_caja")];
            //Costo Funda
            $precios->costo_funda = ["usd_mm2" => $this->costo_funda("usd_mm2"), 
                                     "usd_ton" => $this->costo_funda("usd_ton"), 
                                     "usd_caja" => $this->costo_funda("usd_caja")];
            //Costo Stretch Film
            $precios->costo_stretch_film = ["usd_mm2" => $this->costo_stretch_film("usd_mm2"), 
                                            "usd_ton" => $this->costo_stretch_film("usd_ton"), 
                                            "usd_caja" => $this->costo_stretch_film("usd_caja")];

            //Costo Materiales de Embalaje
            $precios->costo_materiales_embalaje = [
                "usd_mm2" =>    $precios->costo_pallet["usd_mm2"] + 
                                $precios->costo_zuncho["usd_mm2"] + 
                                $precios->costo_funda["usd_mm2"] + 
                                $precios->costo_stretch_film["usd_mm2"],

                "usd_ton" =>    $precios->costo_pallet["usd_ton"] + 
                                $precios->costo_zuncho["usd_ton"] + 
                                $precios->costo_funda["usd_ton"] + 
                                $precios->costo_stretch_film["usd_ton"],

                "usd_caja" =>   $precios->costo_pallet["usd_caja"] + 
                                $precios->costo_zuncho["usd_caja"] + 
                                $precios->costo_funda["usd_caja"] + 
                                $precios->costo_stretch_film["usd_caja"],
            ]; 

            //Costo Energia
            $precios->costo_energia = ["usd_mm2" => $this->costo_energia_new("usd_mm2"), 
                                       "usd_ton" => $this->costo_energia_new("usd_ton"), 
                                       "usd_caja" => $this->costo_energia_new("usd_caja")];
            //Costo Gas Caldera
            $precios->costo_gas_caldera = ["usd_mm2" => $this->costo_gas_caldera_new("usd_mm2"), 
                                           "usd_ton" => $this->costo_gas_caldera_new("usd_ton"), 
                                           "usd_caja" => $this->costo_gas_caldera_new("usd_caja")];
            //Costo Gas Gruas
            $precios->costo_gas_gruas = ["usd_mm2" => $this->costo_gas_gruas("usd_mm2"), 
                                         "usd_ton" => $this->costo_gas_gruas("usd_ton"), 
                                         "usd_caja" => $this->costo_gas_gruas("usd_caja")];

            //Costo Fabricacion
            $precios->costo_fabricacion = [
                "usd_mm2" =>    $precios->costo_energia["usd_mm2"] + 
                                $precios->costo_gas_caldera["usd_mm2"] + 
                                $precios->costo_gas_gruas["usd_mm2"],
                                
                "usd_ton" =>    $precios->costo_energia["usd_ton"] + 
                                $precios->costo_gas_caldera["usd_ton"] + 
                                $precios->costo_gas_gruas["usd_ton"],

                "usd_caja" =>   $precios->costo_energia["usd_caja"] + 
                                $precios->costo_gas_caldera["usd_caja"] + 
                                $precios->costo_gas_gruas["usd_caja"],
            ];

            //Costo Clisses
            $precios->costo_clisses = ["usd_mm2" => $this->costo_clisses_new("usd_mm2"), 
                                       "usd_ton" => $this->costo_clisses_new("usd_ton"), 
                                       "usd_caja" => $this->costo_clisses_new("usd_caja")];
            //Costo Matriz
            $precios->costo_matriz = ["usd_mm2" => $this->costo_matriz_new("usd_mm2"), 
                                      "usd_ton" => $this->costo_matriz_new("usd_ton"), 
                                      "usd_caja" => $this->costo_matriz_new("usd_caja")];

            //Costo materiales de operacion
            $precios->costo_materiales_operacion = [
                "usd_mm2" =>    $precios->costo_clisses["usd_mm2"] + 
                                $precios->costo_matriz["usd_mm2"],

                "usd_ton" =>    $precios->costo_clisses["usd_ton"] + 
                                $precios->costo_matriz["usd_ton"],

                "usd_caja" =>   $precios->costo_clisses["usd_caja"] + 
                                $precios->costo_matriz["usd_caja"],
            ];

            //Total Costos Indirectos
            $precios->costo_indirecto = [
                "usd_mm2" =>    $precios->costo_materiales_operacion["usd_mm2"] + 
                                $precios->costo_materiales_embalaje["usd_mm2"] + 
                                $precios->costo_fabricacion["usd_mm2"],

                "usd_ton" =>    $precios->costo_materiales_operacion["usd_ton"] + 
                                $precios->costo_materiales_embalaje["usd_ton"] + 
                                $precios->costo_fabricacion["usd_ton"],

                "usd_caja" =>   $precios->costo_materiales_operacion["usd_caja"] + 
                                $precios->costo_materiales_embalaje["usd_caja"] + 
                                $precios->costo_fabricacion["usd_caja"]
            ];
        ////Costos Indirectos - Fin

        ////Costos Servicios y Otros - Inicio
            //Costo Armado
            $precios->costo_armado = ["usd_mm2" => $this->costo_armado("usd_mm2"), 
                                      "usd_ton" => $this->costo_armado("usd_ton"), 
                                      "usd_caja" => $this->costo_armado("usd_caja")];
            //Costo Maquila
            $precios->costo_maquila = ["usd_mm2" => $this->costo_maquila_new("usd_mm2"), 
                                       "usd_ton" => $this->costo_maquila_new("usd_ton"), 
                                       "usd_caja" => $this->costo_maquila_new("usd_caja")];

            //Total Costos Servicios y  otros
            $precios->costo_servicios = [
                "usd_mm2" => $precios->costo_armado["usd_mm2"] + 
                             $precios->costo_maquila["usd_mm2"],

                "usd_ton" => $precios->costo_armado["usd_ton"] + 
                             $precios->costo_maquila["usd_ton"],

                "usd_caja" => $precios->costo_armado["usd_caja"] + 
                              $precios->costo_maquila["usd_caja"] ,
            ];
        ////Costos Servicios y Otros - Fin
           // dd("costo_armado",$precios->costo_armado["usd_mm2"],"costo_maquila",$precios->costo_maquila["usd_mm2"]);
        ////Costos GVV - Inicio
            //Costo Flete
            $precios->costo_flete = ["usd_mm2" => $this->costo_flete("usd_mm2"), 
                                     "usd_ton" => $this->costo_flete("usd_ton"), 
                                     "usd_caja" => $this->costo_flete("usd_caja")];

            // FOB este es el costo que se utiliza para calcular el financiamiento, comision
            $costo_fob = $this->margen + 
                         $precios->costo_materia_prima["usd_mm2"] + 
                         $precios->costo_materiales_operacion["usd_mm2"] + 
                         $precios->costo_materiales_embalaje["usd_mm2"] + 
                         $precios->costo_fabricacion["usd_mm2"] + 
                         $precios->costo_servicios["usd_mm2"] + 
                         $precios->costo_flete["usd_mm2"];

            //Costo Financiamiento
            $precios->costo_financiamiento = ["usd_mm2" => $this->costo_financiamiento_new("usd_mm2", $costo_fob), 
                                              "usd_ton" => $this->costo_financiamiento_new("usd_ton", $costo_fob), 
                                              "usd_caja" => $this->costo_financiamiento_new("usd_caja", $costo_fob)];
            //Costo Comision
            $precios->costo_comision = ["usd_mm2" => $this->costo_comision("usd_mm2", $costo_fob), 
                                        "usd_ton" => $this->costo_comision("usd_ton", $costo_fob), 
                                        "usd_caja" => $this->costo_comision("usd_caja", $costo_fob)];
            //Total Costos GVV
            $precios->costo_gvv = [
                "usd_mm2" => $precios->costo_flete["usd_mm2"] + 
                             $precios->costo_financiamiento["usd_mm2"] + 
                             $precios->costo_comision["usd_mm2"],

                "usd_ton" => $precios->costo_flete["usd_ton"] + 
                             $precios->costo_financiamiento["usd_ton"]  + 
                             $precios->costo_comision["usd_ton"],

                "usd_caja" => $precios->costo_flete["usd_caja"] + 
                              $precios->costo_financiamiento["usd_caja"]  + 
                              $precios->costo_comision["usd_caja"]
            ];
        ////Costos GVV - Fin
        
        ////Costos Fijos - Inicio
            //Costo Mano de Obra            
            $precios->costo_mano_de_obra = ["usd_mm2" => $this->costo_mano_de_obra("usd_mm2"), 
                                            "usd_ton" => $this->costo_mano_de_obra("usd_ton"), 
                                            "usd_caja" => $this->costo_mano_de_obra("usd_caja")];
            //Costo Perdida Productividad
            $precios->costo_perdida_productividad = ["usd_mm2" => $this->costo_perdida_productividad_new("usd_mm2"), 
                                                     "usd_ton" => $this->costo_perdida_productividad_new("usd_ton"), 
                                                     "usd_caja" => $this->costo_perdida_productividad_new("usd_caja")];
            //Costo Perdida Productividad Pegado
            $precios->costo_perdida_productividad_pegado = ["usd_mm2" => $this->costo_perdida_productividad_pegado_new("usd_mm2"), 
                                                            "usd_ton" => $this->costo_perdida_productividad_pegado_new("usd_ton"), 
                                                            "usd_caja" => $this->costo_perdida_productividad_pegado_new("usd_caja")];
            //Costo Fijos planta
            $precios->costo_fijos_planta = ["usd_mm2" => $this->costo_fijos_planta("usd_mm2"), 
                                            "usd_ton" => $this->costo_fijos_planta("usd_ton"), 
                                            "usd_caja" => $this->costo_fijos_planta("usd_caja")];

            //Total Costos Fijos
            $precios->costo_fijo_total = [
                "usd_mm2" => $precios->costo_mano_de_obra["usd_mm2"] + 
                             $precios->costo_perdida_productividad["usd_mm2"] + 
                             $precios->costo_perdida_productividad_pegado["usd_mm2"] +
                             $precios->costo_fijos_planta["usd_mm2"],

                "usd_ton" => $precios->costo_mano_de_obra["usd_ton"] + 
                             $precios->costo_perdida_productividad["usd_ton"]  + 
                             $precios->costo_perdida_productividad_pegado["usd_ton"] +
                             $precios->costo_fijos_planta["usd_ton"],

                "usd_caja" => $precios->costo_mano_de_obra["usd_caja"] + 
                              $precios->costo_perdida_productividad["usd_caja"]  + 
                              $precios->costo_perdida_productividad_pegado["usd_caja"] +
                              $precios->costo_fijos_planta["usd_caja"]
            ];  
        ////Costos Fijos - Fin
       
        ////Costos de Servir - Inicio
            //Total Costos de Servir 
            $precios->costo_servir_sin_flete = ["usd_mm2" => $this->costo_servir_sin_flete("usd_mm2"), 
                                                "usd_ton" => $this->costo_servir_sin_flete("usd_ton"), 
                                                "usd_caja" => $this->costo_servir_sin_flete("usd_caja")];
        ////Costos de Servir - Fin

        ////Costo Administrativos - Inicio
            //Total Costo Administrativos
            $precios->costo_administrativos = ["usd_mm2" => $this->costo_administrativos("usd_mm2"), 
                                               "usd_ton" => $this->costo_administrativos("usd_ton"), 
                                               "usd_caja" => $this->costo_administrativos("usd_caja")];
        ////Costo Administrativos - Fin
       
        ////Costo Totales - Inicio
             // Total Costos
            $precios->costo_total = [
                "usd_mm2" => $precios->costo_directo["usd_mm2"] + 
                            $precios->costo_indirecto["usd_mm2"] + 
                            $precios->costo_gvv["usd_mm2"] + 
                            $precios->costo_servicios["usd_mm2"] +
                            $precios->costo_fijo_total["usd_mm2"] +
                            $precios->costo_servir_sin_flete["usd_mm2"] +
                            $precios->costo_administrativos["usd_mm2"],

                "usd_ton" => $precios->costo_directo["usd_ton"] + 
                            $precios->costo_indirecto["usd_ton"] + 
                            $precios->costo_gvv["usd_ton"] + 
                            $precios->costo_servicios["usd_ton"] +
                            $precios->costo_fijo_total["usd_ton"] +
                            $precios->costo_servir_sin_flete["usd_ton"] +
                            $precios->costo_administrativos["usd_ton"],

                "usd_caja" => $precios->costo_directo["usd_caja"] + 
                            $precios->costo_indirecto["usd_caja"] + 
                            $precios->costo_gvv["usd_caja"] + 
                            $precios->costo_servicios["usd_caja"] +
                            $precios->costo_fijo_total["usd_caja"] +
                            $precios->costo_servir_sin_flete["usd_caja"] +
                            $precios->costo_administrativos["usd_caja"],
            ];
        ////Costo Totales - Fin
            
        ////Margen - Inicio
            //Total Margen
            $precios->margen = [
                "usd_mm2" => $this->margen("usd_mm2"),
                "usd_ton" => $this->margen("usd_ton"),
                "usd_caja" => $this->margen("usd_caja"),
            ];
        ////Margen - Fin
            
        ////Mg Ebitda - Inicio
            $mg_ebitda = $this->mg_ebitda();
        ////Mg Ebitda - Fin
            
        ////Precio - Inicio
            //Total Precio
          //  if($precios->margen["usd_mm2"] == 0){
                
                $precios->precio_total = [
                    "usd_mm2" => ($precios->costo_total["usd_mm2"] / (1 - ($mg_ebitda/100))),

                    "usd_ton" => ($precios->costo_total["usd_ton"] / (1 - ($mg_ebitda/100))),

                    "usd_caja" => ($precios->costo_total["usd_caja"] / (1 - ($mg_ebitda/100))),
                ];
           /* }else{
                $precios->precio_total = [
                    "usd_mm2" => ($precios->costo_total["usd_mm2"] + $precios->margen["usd_mm2"]),

                    "usd_ton" => ($precios->costo_total["usd_ton"] + $precios->margen["usd_ton"]),

                    "usd_caja" => ($precios->costo_total["usd_caja"] + $precios->margen["usd_caja"]),
                ];
            }*/

        ////Precio - Fin

        ////Precio Final - Fin
            $precios->precio_final = [
                "usd_mm2" => ($precios->costo_total["usd_mm2"] + $precios->margen["usd_mm2"]),

                "usd_ton" => ($precios->costo_total["usd_ton"] + $precios->margen["usd_ton"]),

                "usd_caja" => ($precios->costo_total["usd_caja"] + $precios->margen["usd_caja"]),
            ];
        
       
        
        // dd("margen",$precios->margen["usd_mm2"],"mg_ebitda",$mg_ebitda,"costo total",$precios->costo_total["usd_mm2"],"precio_total",$precios->precio_total["usd_mm2"],"precio_final",$precios->precio_final["usd_mm2"]);

        ////Precios y Costo CLP (usd_caja * precio_dolar) - Inicio
            $precios->costo_total["clp_caja"] =  $precios->costo_total["usd_caja"] * $this->precio_dolar;
            $precios->precio_total["clp_caja"] = ($precios->costo_total["usd_caja"] / (1 - ($mg_ebitda/100))) * $this->precio_dolar;
            $precios->precio_final["clp_caja"] = ($precios->costo_total["usd_caja"] + $precios->margen["usd_caja"]) * $this->precio_dolar;
        ////Precios y Costo CLP (usd_caja * precio_dolar) - Fin
        
        ////Costos Adicionales Informativos - Inicio
            $precios->costo_royalty = ["usd_mm2" => $this->costo_royalty("usd_mm2"), "usd_ton" => $this->costo_royalty("usd_ton"), "usd_caja" => $this->costo_royalty("usd_caja")];
        ////Costos Adicionales Informativos - Fin
        
        //dd("costo_directo",$precios->costo_directo["usd_mm2"],"costo_indirecto",$precios->costo_indirecto["usd_mm2"] , "costo_gvv",$precios->costo_gvv["usd_mm2"] ,"costo_servicios",$precios->costo_servicios["usd_mm2"],"costo_fijo_total",$precios->costo_fijo_total["usd_mm2"],"costo_servir_sin_flete",$precios->costo_servir_sin_flete["usd_mm2"],"costo_administrativos",$precios->costo_administrativos["usd_mm2"],"margen",$precios->margen["usd_mm2"],"precio_total",$precios->precio_total["usd_mm2"],"precio_final",$precios->precio_final["usd_mm2"]);
 
        return $precios;
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    // CORRUGADOS
    // NOTA: se agrega cobertura de barniz, ahora como esta cobertura barniz y cera se deja el nombre de porcentaje_cera_interno y porcentaje_cera_externo
	// Pero su valor para los calculo de las formulas, se va a validar de si la cobertura es barniz o cera
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    // COSTOS DE PAPEL
    public function costo_papel($unidad)
    {
        $planta = $this->planta;
       
        
        // Costo de todos los papeles q componen el carton sin desperdicio
        $costo_carton_papeles = $this->costo_carton_papeles;
        
        $desperdicio_papel = $this->desperdicio_papel;
        //dd($desperdicio_papel);
        //obtenemos la merma ceresinado
        if(is_null($this->coverageType)){
            $merma_ceresinado = 0;
        }else{
            $coverageTypeId = $this->coverageType;
            if($coverageTypeId->id==2){
                if(($this->porcentaje_cera_externo + $this->porcentaje_cera_interno)==0){
                    $merma_ceresinado = 0;
                }else{
                    $merma_ceresinado = $planta["merma_cera"];
                }
            }else{
                $merma_ceresinado = 0;
            }
        }
           
        
       
         
        // $costo_papel_usd_mm2 = $costo_carton_papeles / ((1 - $desperdicio_papel) * (1 - ($this->merma_corrugadora)) * (1 - ($this->merma_convertidora)) * (1 - ($merma_ceresinado / 100)));
        //dd($desperdicio_papel,$planta["porcentaje_merma_corrugadora"],$this->merma_convertidora);
        $costo_papel_usd_mm2 = $costo_carton_papeles / ( (1 - $desperdicio_papel) * (1 - ($planta["porcentaje_merma_corrugadora"])) * (1 - ($this->merma_convertidora)) );
       
        switch ($unidad) {
            case 'usd_mm2':
                $costo_papel = $costo_papel_usd_mm2;
                break;
            case 'usd_ton':
                $costo_papel = ($costo_papel_usd_mm2 * 1000) / $this->gramaje_carton;
                break;
            case 'usd_caja':
                $costo_papel = $costo_papel_usd_mm2 * $this->area_hc / 1000;
                break;

            default:
                # code...
                break;
        }
        

        return $costo_papel;
    }

    public function getDesperdicioPapelAttribute()
    {
        $desperdicio_papel = 0;
        $anchoHC = $this->anchura * $this->golpes_ancho + $this->orilla_ancho();
        $planta = $this->planta;
        $array_default_carton = array("EN32B","EN58Q","EN34C","EN42C","EN48C","EN55C","EN57C","EN64C");

        $formatos_bobina = $planta["formatos_bobina_corrugadora"];
        if($anchoHC==0){
            $desperdicio_papel = 0;
            return $desperdicio_papel;
        }

        if(in_array($this->carton->codigo,$array_default_carton)){
            $desperdicio_papel = 0.015;
            return $desperdicio_papel;
        }
       /* if ($this->carton->excepcion) {
            // dd($this->carton);
            $desperdicio_papel = $this->carton->desperdicio / 100;
        } else {*/
        $numero_cortes = (int) (($planta["ancho_corrugadora"] - $planta["trim_corrugadora"]) / $anchoHC);
          //  dd($numero_cortes,$planta["ancho_corrugadora"],$planta["trim_corrugadora"],$anchoHC);
            foreach ($formatos_bobina["formatos"] as $formato) {
                // Si carton es excepcion calcular desperdicion desde bd

                // Recorrer formatos q soporta la planta 
                if (($formato - ($numero_cortes * $anchoHC)) > 30 && (($numero_cortes * $anchoHC) > 0)) {
                    $desperdicio_papel = ($formato - ($numero_cortes * $anchoHC)) / ($numero_cortes * $anchoHC);
                    break;
                }
            }
       // }
        //dd($desperdicio_papel);
        //Validar tope maximo de 7% por desperdicio de papel (Solicitado por el cliente en correo de fecha 16/01/2025)
        if($desperdicio_papel > 0.07){
            $desperdicio_papel = 0.07;
        }
        return $desperdicio_papel;
    }

    public function getMermaConvertidoraAttribute()
    {
        
        // encontramos la merma segun la planta   
        $merma_convertidora = $this->rubro->mermas_convertidora->first(function ($merma_convertidora) {
            return ($merma_convertidora->planta_id == $this->planta_id && $merma_convertidora->process_id == $this->process_id);
        });
       
        if(is_null($merma_convertidora)){
            $merma_convertidora = 0;

        }else{
            $merma_convertidora = $merma_convertidora->porcentaje_merma_convertidora;
        }
        
       
        if ($merma_convertidora <= 0) {
            switch ($this->planta_id) {
                case '1':
                    switch ($this->process_id) {
                        case '2':
                            $merma_convertidora = 0.0566303193306924;
                            break;
                        case '4':
                            $merma_convertidora = 0.0749950389855116;
                            break;
                        case '1':
                            $merma_convertidora = 0.0566303193306924;
                            break;
                        case '5':
                            $merma_convertidora = 0.0566303193306924;
                            break;
                        case '3':
                            $merma_convertidora = 0;
                            break;
                        case '7':
                            $merma_convertidora = 0.282405261875926;
                            break;
                        case '9':
                            $merma_convertidora = 0.221081680483711;
                            break;
                        case '10':
                            $merma_convertidora = 0.0566303193306924;
                            break;
                        case '11':
                            $merma_convertidora = 0.0566303193306924;
                            break;
                        case '12':
                            $merma_convertidora = 0.0749950389855116;
                            break;
                        default:
                            # code...
                            break;
                    }
                    break;
                case '2':
                    switch ($this->process_id) {
                        case '2':
                            $merma_convertidora = 0.0551099978808036;
                            break;
                        case '4':
                            $merma_convertidora = 0.0804161827386336;
                            break;
                        case '1':
                            $merma_convertidora = 0.0551099978808036;
                            break;
                        case '5':
                            $merma_convertidora = 0.0551099978808036;
                            break;
                        case '10':
                            $merma_convertidora = 0.0551099978808036;
                            break;
                        case '3':
                            $merma_convertidora = 0;
                            break;
                    }
                    break;
                case '3':
                    switch ($this->process_id) {
                        case '2':
                            $merma_convertidora = 0.0208859693873034;
                            break;
                        case '4':
                            $merma_convertidora = 0.0319021930421724;
                            break;
                        case '1':
                            $merma_convertidora = 0.0208859693873034;
                            break;
                        case '5':
                            $merma_convertidora = 0.0208859693873034;
                            break;
                        case '10':
                            $merma_convertidora = 0.0208859693873034;
                            break;
                        case '3':
                            $merma_convertidora = 0;
                            break;
                    }
                    break;
            }
        }
       
        $ensamblado = $this->ensamblado;

        if($ensamblado == 1){
            $valor_ensamblado = 0.0749950389855116 - 0.0566303193306924;
        }else{
            $valor_ensamblado = 0;
        }
       
        return $merma_convertidora + $valor_ensamblado;
    }

    public function getMermaCorrugadoraAttribute()
    {
        // encontramos la merma segun la planta
        $merma_corrugadora = $this->carton->mermas_corrugadora->first(function ($merma_corrugadora) {
            return $merma_corrugadora->planta_id == $this->planta_id;
        });
        // dd($this->carton);
        // $merma_corrugadora = MermaCorrugadora::where("carton_id", $this->carton_id)->where("planta_id", $this->planta_id)->first();
        if (!$merma_corrugadora) {
            switch ($this->planta_id) {
                case '1':
                    $merma_corrugadora = 0.037000000;
                    break;
                case '2':
                    $merma_corrugadora = 0.032000000;
                    break;
                case '3':
                    $merma_corrugadora = 0.025000000;
                    break;

                default:
                    # code...
                    break;
            }
        } else {

            $merma_corrugadora = $merma_corrugadora->porcentaje_merma_corrugadora;
        }
        // dd($merma_corrugadora);
        return $merma_corrugadora;
    }

    // Calcula en base a la planta seleccionada el precio de los papeles para armar carton
    public function getCostoCartonPapelesAttribute()
    {

        $carton = $this->carton;
        // dd($this->costo_tapa("interior"), $this->costo_onda_1(), $this->costo_tapa("media"), $this->costo_onda_2(), $this->costo_tapa("exterior"));
        $costo_tapa_interior = $this->costo_tapa("interior", $carton);

      //  dd($this->carton,$costo_tapa_interior,$this->planta_id);
        $costo_onda_1 = $this->costo_onda_1($carton);
        $costo_onda_1_2 = $this->costo_onda_1_2($carton);
        $costo_tapa_media = $this->costo_tapa("media", $carton);
        $costo_onda_2 = $this->costo_onda_2($carton);
        $costo_tapa_externa = $this->costo_tapa("exterior", $carton);
       //dd("costo_tapa_interior",$costo_tapa_interior,"costo_onda_1",$costo_onda_1,$costo_onda_1_2,$costo_tapa_media,$costo_onda_2,$costo_tapa_externa);
        $costo_carton_papeles = ($costo_tapa_interior + $costo_onda_1 + $costo_onda_1_2 + $costo_tapa_media + $costo_onda_2 + $costo_tapa_externa) / 1000;
       // dd($carton,$costo_carton_papeles);
        return $costo_carton_papeles;
       
          
    }

    public function costo_tapa($tipo_papel, $carton)
    {


        // si la planta es en osorno se debe agregar un adicional al precio de papeles 
        $diferencia_precio_papel = ($this->planta_id == 3) ? 35 : 0;
        //dd($carton["tapa_interior"]);
        switch ($tipo_papel) {
            case 'interior':
                if ($carton["tapa_interior"]) {
                  
                    return ($carton["tapa_interior"]["gramaje"] * ($carton["tapa_interior"]["precio"] + $diferencia_precio_papel));
                }
                break;
            case 'media':
                if ($carton["tapa_media"]) {
                    return ($carton["tapa_media"]["gramaje"] * ($carton["tapa_media"]["precio"] + $diferencia_precio_papel));
                }
                break;
            case 'exterior':
                if ($carton["tapa_exterior"]) {
                    //dd("gramaje",$carton["tapa_exterior"]["gramaje"],"percio",$carton["tapa_exterior"]["precio"], $diferencia_precio_papel);
                    return ($carton["tapa_exterior"]["gramaje"] * ($carton["tapa_exterior"]["precio"] + $diferencia_precio_papel));
                }
                break;

            default:
                # code...
                break;
        }

        // Si no hay papel el valor es 0
        return 0;
    }

    public function costo_onda_1($carton)
    {
        //dd($this->planta_id);
        // Si no hay onda el valor es 0
        if (!$carton["primera_onda"]) {
            return 0;
        }
        // si la planta es en osorno se debe agregar un adicional al precio de papeles 
        $diferencia_precio_papel = ($this->planta_id == 3) ? 35 : 0;

        // encontramos el factor de onda segun el tipo de onda 1
        $factor_onda = $this->planta->factores_onda->first(function ($factor) {
            //dd($this->carton["onda_1"]);
            return $factor->onda == $this->carton["onda_1"];
        });
       
        //dd($carton["primera_onda"]);
        //  FactoresOnda::where("onda", $carton["onda_1"])->where("planta_id", $this->planta_id)->first();
        //dd($this->planta->factores_onda$this->carton["onda_1"],$factor_onda["factor_onda"],$carton["primera_onda"]["gramaje"],$carton["primera_onda"]["precio"],$diferencia_precio_papel);
       
        $costo_onda = $factor_onda["factor_onda"] * $carton["primera_onda"]["gramaje"] * ($carton["primera_onda"]["precio"] + $diferencia_precio_papel);
       // dd($costo_onda);
        return ($costo_onda);
    }

    public function costo_onda_1_2($carton)
    {
       
        //dd($carton["onda_powerplay"]);
        // Si no hay onda el valor es 0
        if (!$carton["onda_powerplay"]) {
            return 0;
        }
        // si la planta es en osorno se debe agregar un adicional al precio de papeles 
        $diferencia_precio_papel = ($this->planta_id == 3) ? 35 : 0;
        // encontramos el factor de onda power ply siempre es C
        $factor_onda = $this->planta->factores_onda->first(function ($factor) {
            return $factor->onda == "C";
        });
      //  dd( $costo_onda = $factor_onda["factor_onda"], $carton["onda_powerplay"]["gramaje"] , $carton["onda_powerplay"]["precio"],$diferencia_precio_papel);
        $costo_onda = $factor_onda["factor_onda"] * $carton["onda_powerplay"]["gramaje"] * ($carton["onda_powerplay"]["precio"] + $diferencia_precio_papel);
        return ($costo_onda);
    }

    public function costo_onda_2($carton)
    {

        // Si no hay onda el valor es 0
        if (!$carton["segunda_onda"]) {
            return 0;
        }
        // si la planta es en osorno se debe agregar un adicional al precio de papeles 
        $diferencia_precio_papel = ($this->planta_id == 3) ? 35 : 0;

        // encontramos el factor de onda segun el tipo de onda 2
        $factor_onda = $this->planta->factores_onda->first(function ($factor) {
            
            return $factor->onda == $this->carton["onda_2"];
        });

        if(!$factor_onda){
            return 0;
        }
        $costo_onda = $factor_onda["factor_onda"] * $carton["segunda_onda"]["gramaje"] * ($carton["segunda_onda"]["precio"] + $diferencia_precio_papel);
       
        return ($costo_onda);
    }

    // TRIM CONVERTIDORA EXCEL
    public function orilla_ancho()
    {
        $tipoCarton = $this->carton->tipo;
        // Segun el tipo de carton ver q tipo de onda usa
        if ($tipoCarton == "SIMPLES" || $tipoCarton == "POWER PLY" || $tipoCarton == "MONOTAPA") {
            $tipo_onda_carton = "SIMPLE";
        } else {
            $tipo_onda_carton = "DOBLE";
        }

        // Si el proceso es diecutter o diecutter con proceso/pegado y DIECUTTER - ALTA GRÁFICA, DIECUTTER -C/PEGADO ALTA GRÁFICA
        if (in_array($this->process_id, [2, 4, 6, 10, 11, 12])) {
            if ($tipo_onda_carton == "SIMPLE") {
                return 20;
            }
            return 25;
        } elseif (in_array($this->process_id, [1, 5])) {
            // Si es ffg o flexo 
            return 0;
            // Si es felxo pero con troquelado dependiendo del carton debe ser 20 o 25
        } else {
            return 0;
        }
    }

    // TRIM CONVERTIDORA EXCEL
    public function orilla_largo()
    {
        $tipoCarton = $this->carton->tipo;
        // Segun el tipo de carton ver q tipo de onda usa
        if ($tipoCarton == "SIMPLES" || $tipoCarton == "POWER PLY" || $tipoCarton == "MONOTAPA") {
            $tipo_onda_carton = "SIMPLE";
        } else {
            $tipo_onda_carton = "DOBLE";
        }

        // Si el proceso es diecutter o diecutter con proceso/pegado y DIECUTTER - ALTA GRÁFICA, DIECUTTER -C/PEGADO ALTA GRÁFICA
        if (in_array($this->process_id, [2, 4, 6, 10, 11, 12])) {
            if ($tipo_onda_carton == "SIMPLE") {
                return 20;
            }
            return 25;
        } elseif (in_array($this->process_id, [1, 5])) {
            // Si es ffg o flexo 
            return 10;
            // Si es felxo pero con troquelado dependiendo del carton debe ser 20 o 25
        } else {
            return 0;
        }
    }

    // COSTOS ADHESIVOS
    public function costo_adhesivo($unidad)
    {

        $planta = $this->planta;
        $coverageTypeId = $this->coverageType;
        
        if (($this->porcentaje_cera_interno + $this->porcentaje_cera_externo) > 0) {


            if(is_null($coverageTypeId)){
                $merma_cera = 0;
            }else if($coverageTypeId->descripcion == 'Barniz'){
                $merma_cera = 0;
            }else if($coverageTypeId->descripcion == 'Cera'){
                $merma_cera = $planta["merma_cera"];
            } else {
                $merma_cera = 0;
            }
            
            // $merma_cera = $planta["merma_cera"];
        } else {
            $merma_cera = 0;
        }
        $costo_carton_adhesivo = $this->costo_carton_adhesivos;
        $costo_adhesivo_usd_mm2 = $costo_carton_adhesivo / ((1 - ($planta["porcentaje_merma_corrugadora"])) * (1 - ($this->merma_convertidora)));  //* (1 - ($merma_cera / 100)));
       
        switch ($unidad) {
            case 'usd_mm2':
                $costo_adhesivo = $costo_adhesivo_usd_mm2;
                break;
            case 'usd_ton':
                $costo_adhesivo = ($costo_adhesivo_usd_mm2 * 1000) / $this->gramaje_carton;
                break;
            case 'usd_caja':
                $costo_adhesivo = $costo_adhesivo_usd_mm2 * $this->area_hc / 1000;
                break;

            default:
                # code...
                break;
        }
        return $costo_adhesivo;
    }
    
    // Calcula en base a la planta seleccionada el precio de los adhesivos para armar carton
    public function getCostoCartonAdhesivosAttribute()
    {

        $carton = $this->carton;
        $planta = $this->planta;

        $costo_adhesivo_onda_1 = $this->costo_adhesivo_onda_1($carton, $planta);
        $costo_adhesivo_onda_1_2 = $this->costo_adhesivo_onda_1_2($carton, $planta);
        $costo_adhesivo_onda_2 = $this->costo_adhesivo_onda_2($carton, $planta);

        // Si el tipo de carton es power play se debe agregar la segunda onda como la 1.2
        $costo_carton_adhesivo = ($costo_adhesivo_onda_1 + $costo_adhesivo_onda_1_2 + $costo_adhesivo_onda_2) * 1000;
     
        return $costo_carton_adhesivo;
        // return $costo_carton_papeles;
    }

    public function costo_adhesivo_onda_1($carton, $planta)
    {
        // Si no hay onda el valor es 0
        if (!$carton["primera_onda"]) {
            return 0;
        }

        // encontramos el consumo_adhesivo segun el tipo de onda 1
        $consumo_adhesivo = $this->planta->consumos_adhesivo->first(function ($consumo_adhesivo) {
            return $consumo_adhesivo->onda == $this->carton->onda_1;
        });

        $costo_adhesivo_onda_1 = $consumo_adhesivo["adhesivo_corrugado"] * $planta["precio_adhesivo"];
       
        return $costo_adhesivo_onda_1;
    }
    public function costo_adhesivo_onda_1_2($carton, $planta)
    {
       
        // Si no hay onda el valor es 0
        if (!$this->carton->onda_powerplay) {
            return 0;
        }

        // encontramos el consumo_adhesivo powerply siempre es C
        $consumo_adhesivo = $this->planta->consumos_adhesivo->first(function ($consumo_adhesivo) {
            return $consumo_adhesivo->onda == "C";
        });
       // dd($consumo_adhesivo,$consumo_adhesivo["adhesivo_powerply"]);

        // Si es power ply se debe sumar la onda adicional 
        if ($this->carton->tipo == "POWER PLY") {
            
            $costo_adhesivo_onda_2 = $consumo_adhesivo["adhesivo_powerply"] * $planta["precio_adhesivo_powerply"];
        } else {
            //return 0;
            $costo_adhesivo_onda_2=0;
            //$costo_adhesivo_onda_2 = $consumo_adhesivo["adhesivo_corrugado"] * $planta["precio_adhesivo"];
        }
        // dd($costo_adhesivo_onda_2);
        return $costo_adhesivo_onda_2;
    }
    public function costo_adhesivo_onda_2($carton, $planta)
    {
        // Si no hay onda el valor es 0
        if (!$this->carton->segunda_onda) {
            return 0;
        }


        // encontramos el consumo_adhesivo segun el tipo de onda 2
        $consumo_adhesivo = $this->planta->consumos_adhesivo->first(function ($consumo_adhesivo) {
            return $consumo_adhesivo->onda == $this->carton->onda_2;
        });

        if(!$consumo_adhesivo){
            return 0;
        }
        // Si es power ply se debe sumar la onda adicional 
        // if (!$this->carton->tipo == "POWER PLY") {
        //     $costo_adhesivo_onda_2 = $consumo_adhesivo["adhesivo_powerplay"] * $planta["precio_adhesivo_powerply"];
        // } else {
        $costo_adhesivo_onda_2 = $consumo_adhesivo["adhesivo_corrugado"] * $planta["precio_adhesivo"];
        // }
        // dd($costo_adhesivo_onda_2);
        return $costo_adhesivo_onda_2;
    }

    // COSTOS Tintas
    public function costo_tinta($unidad)
    {
        $planta = $this->planta;
        
        $tintaAltaGrafica = $this->inkType;

        
        //obtenemos la merma ceresinado
        //obtenemos la merma ceresinado
        if(is_null($this->coverageType)){
            $merma_ceresinado = 0;
        }else{
            $coverageTypeId = $this->coverageType;
            if($coverageTypeId->id==2){
                if(($this->porcentaje_cera_externo + $this->porcentaje_cera_interno)==0){
                    $merma_ceresinado = 0;
                }else{
                    $merma_ceresinado = $planta["merma_cera"];
                }
            }else{
                $merma_ceresinado = 0;
            }
        }
        
        //Obtenemos el costo y consumo de la tinta segun el proceso
        if($this->process_id==11 || $this->process_id==12){
           
            if($tintaAltaGrafica->id == 2){//Alta gráfica: Especial
    
                $costo_tinta_usd_gr = $planta["costo_tinta_usd_gr_alta_grafica_especial"];
                $consumo_tinta_gr_x_Mm2 = $planta["consumo_tinta_usd_gr_alta_grafica_especial"];
    
            }else if($tintaAltaGrafica->id == 3){//Alta gráfica: Metalizada
    
                $costo_tinta_usd_gr = $planta["costo_tinta_usd_gr_alta_grafica_metalizado"];
                $consumo_tinta_gr_x_Mm2 = $planta["consumo_tinta_usd_gr_alta_grafica_metalizado"];
    
            }else if($tintaAltaGrafica->id == 4){//Alta gráfica: Otras
                
                $costo_tinta_usd_gr = $planta["costo_tinta_usd_gr_alta_grafica_otras"];
                $consumo_tinta_gr_x_Mm2 = $planta["consumo_tinta_usd_gr_alta_grafica_otras"];
    
            }else{
                $costo_tinta_usd_gr = $planta["costo_tinta_usd_gr"];
                $consumo_tinta_gr_x_Mm2 = $planta["consumo_tinta_gr_x_Mm2"];
            }
            
        }else{
            $costo_tinta_usd_gr = $planta["costo_tinta_usd_gr"];
            $consumo_tinta_gr_x_Mm2 = $planta["consumo_tinta_gr_x_Mm2"];
        }

        

        $cobertura = $this->impresion;
        $costo_tinta_usd_mm2 = (($cobertura / 100) * $costo_tinta_usd_gr * $consumo_tinta_gr_x_Mm2)
        / ((1 - ($this->merma_convertidora)) * (1 - ($merma_ceresinado / 100)));
       
        switch ($unidad) {
            case 'usd_mm2':
                $costo_tinta = $costo_tinta_usd_mm2;
                break;
            case 'usd_ton':
                $costo_tinta = ($costo_tinta_usd_mm2 * 1000) / $this->gramaje_carton;
                break;
            case 'usd_caja':
                $costo_tinta = $costo_tinta_usd_mm2 * $this->area_hc / 1000;
                break;

            default:
                # code...
                break;
        }
       
        return $costo_tinta;
    }

    public function costo_tinta_new($unidad)
    {

        $planta = $this->planta;
        $maquinaImpresora = $this->printing_machine_id;
        $tintaAltaGrafica = $this->inkType;
        $tipo_impresion=$this->print_type_id;

        if($maquinaImpresora==1 || is_null($maquinaImpresora) || $maquinaImpresora=='' || $this->cobertura_color_percent==0 || is_null($this->cobertura_color_percent)){//Sin Impresión o porcentaje cobertura color nulo o cero
            return 0;
        }elseif($maquinaImpresora==4){//Maquina Impresora Alta Grafica
            //Obtenemos el costo y consumo de la tinta
            if($tipo_impresion){//Alta Grafica
                ////Costos
                $costo_usd_gr=($planta["costo_tinta_usd_gr_alta_grafica_metalizada"]*0.2)+($planta["costo_tinta_usd_gr_alta_grafica_otras"]*0.8);

                ////Consumos
                $consumo_usd_gr=((($planta["consumo_tinta_usd_gr_alta_grafica_otras"]*4)+($planta["consumo_tinta_usd_gr_alta_grafica_metalizado"]*2))/6)/10000000;
            }else{//Normal
                ////Costos
                $costo_usd_gr=$planta["costo_tinta_usd_gr"];

                ////Consumos
                $consumo_usd_gr=$planta["consumo_tinta_gr_x_Mm2"]/10000000;
            }       

        }elseif($maquinaImpresora==2){//Maquina Impresora Normal
            
            ////Costos
            $costo_usd_gr=$planta["costo_tinta_usd_gr"];

            ////Consumos
            $consumo_usd_gr=$planta["consumo_tinta_gr_x_Mm2"]/10000000;

        }elseif($maquinaImpresora==3){//Maquina Impresora Interna
            
            return 0;

        }elseif($maquinaImpresora==5){//Maquina Dong Fang

            ////Costos
            $costo_usd_gr=$planta["costo_tinta_usd_gr"];

            ////Consumos
            $consumo_usd_gr=$planta["consumo_tinta_gr_x_Mm2"]/10000000;
        }
        //Se multiplica por  100 en ves de 10000 ya que de deben quitar los 00 del convercion de pòrcentaje del dato cobertura_color_percent 
        $cobertura = $this->cobertura_color_percent * $this->area_hc * 100;
        //Calculo Costo de Tinta
        $costo_tinta_usd_mm2 = ($cobertura * $costo_usd_gr * $consumo_usd_gr) / (1 - ($this->merma_convertidora));
           
        switch ($unidad) {
            case 'usd_mm2':
                $costo_tinta = $costo_tinta_usd_mm2 / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_tinta =  $costo_tinta_usd_mm2 / $this->area_hc * 1000 * 1000 / $this->gramaje_carton;
                break;
            case 'usd_caja':
                $costo_tinta = $costo_tinta_usd_mm2;
                break;

            default:
                break;
        }    
        
        return $costo_tinta;
    }

    // COSTOS Cera
    public function costo_cera($unidad)
    {   
        //Si el tipo de cobertura no es cera retorna 0
        if($this->coverage_type_id!=2){
            return 0;
        }

        $planta = $this->planta;

        if (($this->porcentaje_cera_interno + $this->porcentaje_cera_externo) > 0) {
            $merma_cera = $planta["merma_cera"];
        } else {
            $merma_cera = 0;
        }
        $cobertura_cera = $this->porcentaje_cera_interno + $this->porcentaje_cera_externo;
        $costo_cera_usd_mm2 = (($cobertura_cera / 100) * $planta["costo_cera_usd_gr"] * $planta["consumo_cera_gr_x_Mm2"]) / ((1 - ($merma_cera / 100)));
        //dd($costo_cera_usd_mm2);
        $costo_cera = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_cera = $costo_cera_usd_mm2;
                break;
            case 'usd_ton':
                $costo_cera = ($costo_cera_usd_mm2 * 1000) / $this->gramaje_carton;
                break;
            case 'usd_caja':
                $costo_cera = $costo_cera_usd_mm2 * $this->area_hc / 1000;
                break;

            default:
                # code...
                break;
        }
        return $costo_cera;
    }

    // COSTOS Barniz
    // NOTA: se agrega cobertura de barniz, ahora como esta cobertura barniz y cera se deja el nombre de porcentaje_cera_interno y porcentaje_cera_externo
	// Pero su valor para los calculo de las formulas, se va a validar de si la cobertura es barniz o cera
    public function costo_barniz($unidad)
    {

        //1::OBTENER DATOS PARA CALCULO
        $planta = $this->planta;
        $coverageType = $this->coverageType;

        //2::OBTENER PORCENTAJE DE COBERTURA
        if (in_array($this->process_id, [11, 12])) {
            $porcentaje_cobertura_barniz = 100/100;
        }else{
            $porcentaje_cobertura_barniz = ($this->porcentaje_cera_interno + $this->porcentaje_cera_externo)/100;
        }
       
        //3::OBTENER COSTO CLISSE EN USD
        $numero_golpes = $this->golpes_ancho * $this->golpes_largo;
        $costo_clisse_usd_cm2 = $planta["costo_clisse_clp_cm2"] / $this->precio_dolar;
        $costo_usd_clisses = $numero_golpes * $this->area_hc * $porcentaje_cobertura_barniz  *  $costo_clisse_usd_cm2 * 10000;

        //4::OBTENER CONSUMO DE GR  MM2 DE COBERTURA Y COSTO USD/GRAMO
        //dd($numero_golpes,$this->area_hc,$cobertura_barniz,$costo_clisse_usd_cm2,$costo_usd_clisses);     
        if(!$coverageType) {
            $consumo_barniz_gr_x_Mm2 = 0;
            $costo_barniz_usd_gr = 0;
        } else {
            if($coverageType->id == 1 || $coverageType->id == 2){//Barniz o Cera
            
                $consumo_barniz_gr_x_Mm2 = $planta["consumo_barniz_gr_x_Mm2"];
                $costo_barniz_usd_gr = $planta["costo_barniz_usd_gr"];
    
            }else if($coverageType->id == 4){//Barniz: Acuoso
    
                $consumo_barniz_gr_x_Mm2 = $planta["consumo_barniz_acuoso_gr_x_Mm2"];
                $costo_barniz_usd_gr = $planta["costo_barniz_acuoso_usd_gr"];
    
            }else if($coverageType->id == 5){//Barniz: UV Brillante
    
                $consumo_barniz_gr_x_Mm2 = $planta["consumo_barniz_uv_gr_x_Mm2"];
                $costo_barniz_usd_gr = $planta["costo_barniz_uv_usd_gr"];
    
            }else{//Sin cobertura
    
                $consumo_barniz_gr_x_Mm2 = 0;
                $costo_barniz_usd_gr = 0;
    
            } 
        }

        //5::OBTENER MERMA COMVERTIDORA
        $merma_convertidora=$this->merma_convertidora;
        /*switch ($this->planta_id) {
            case '1':
                switch ($this->process_id) {
                    case '2':
                        $merma_convertidora= 0.0566303193306924;
                        break;
                    case '4':
                        $merma_convertidora= 0.0749950389855116;
                        break;
                    case '1':
                        $merma_convertidora= 0.0566303193306924;
                        break;
                    case '5':
                        $merma_convertidora= 0.0566303193306924;
                        break;
                    case '3':
                        $merma_convertidora= 0;
                        break;
                    case '7':
                        $merma_convertidora= 0.282405261875926;
                        break;
                    case '9':
                        $merma_convertidora= 0.221081680483711;
                        break;
                    case '10':
                        $merma_convertidora= 0.0566303193306924;
                        break;
                    case '11':
                        $merma_convertidora= 0.0566303193306924;
                        break;
                    case '12':
                        $merma_convertidora= 0.0749950389855116;
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case '2':
                switch ($this->process_id) {
                    case '2':
                        $merma_convertidora= 0.0551099978808036;
                        break;
                    case '4':
                        $merma_convertidora= 0.0804161827386336;
                        break;
                    case '1':
                        $merma_convertidora= 0.0551099978808036;
                        break;
                    case '5':
                        $merma_convertidora= 0.0551099978808036;
                        break;
                    case '10':
                        $merma_convertidora= 0.0551099978808036;
                        break;
                    case '3':
                        $merma_convertidora= 0;
                        break;
                }
                break;
            case '3':
                switch ($this->process_id) {
                    case '2':
                        $merma_convertidora= 0.0208859693873034;
                        break;
                    case '4':
                        $merma_convertidora= 0.0319021930421724;
                        break;
                    case '1':
                        $merma_convertidora= 0.0208859693873034;
                        break;
                    case '5':
                        $merma_convertidora= 0.0208859693873034;
                        break;
                    case '10':
                        $merma_convertidora= 0.0208859693873034;
                        break;
                    case '3':
                        $merma_convertidora= 0;
                        break;
                }
                break;
        }*/
        
        //5::OBTENER COSTO DE BARNIZ TOTAL
        //$costo_barniz_usd_mm2 = $consumo_barniz_gr_x_Mm2*$costo_barniz_usd_gr*$porcentaje_cobertura_barniz/(1-($this->merma_convertidora))+(1000*($costo_usd_clisses/$this->cantidad))/$this->area_hc; 
        $costo_barniz_usd_mm2 = ($consumo_barniz_gr_x_Mm2*$costo_barniz_usd_gr*$porcentaje_cobertura_barniz)/(1-$merma_convertidora)
                                +(1000*($costo_usd_clisses/$this->cantidad))/$this->area_hc; 
       // dd($costo_barniz_usd_mm2);
        //dd($consumo_barniz_gr_x_Mm2,$costo_barniz_usd_gr,$porcentaje_cobertura_barniz,$merma_convertidora,$costo_usd_clisses,$this->cantidad,$this->area_hc);
        $costo_barniz = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_barniz = $costo_barniz_usd_mm2;
                break;
            case 'usd_ton':
                $costo_barniz = ($costo_barniz_usd_mm2 * 1000) / $this->gramaje_carton;
                break;
            case 'usd_caja':
                $costo_barniz = $costo_barniz_usd_mm2 * $this->area_hc / 1000;
                break;

            default:
                # code...
                break;
        }

        return $costo_barniz;
    }

    //Nueva Funcion Barniz Evo 24-01
    public function costo_barniz_new($unidad)
    {

        $planta = $this->planta;
        $maquinaImpresora = $this->printing_machine_id;
       // $tintaAltaGrafica = $this->inkType;
        $tipo_barniz=$this->barniz_type_id;
      //  dd($maquinaImpresora,$tipo_barniz);
        if($maquinaImpresora==1 || is_null($maquinaImpresora) || $maquinaImpresora=='' ){//Sin Impresión
            return 0;
        }elseif($maquinaImpresora==4){//Maquina Impresora Alta Grafica
            //Obtenemos el costo y consumo del barniz

            if($tipo_barniz==1){//UV
                ////Costos
                $costo_usd_gr= $planta["costo_barniz_uv_usd_gr"];

                ////Consumos
                $consumo_usd_gr= $planta["consumo_barniz_uv_gr_x_Mm2"]/10000000;

            }elseif ($tipo_barniz==2){//Acuoso

                ////Costos
                $costo_usd_gr= $planta["costo_barniz_acuoso_usd_gr"];

                ////Consumos
                $consumo_usd_gr= $planta["consumo_barniz_acuoso_gr_x_Mm2"]/10000000;

            }elseif ($tipo_barniz==3) {//Hidrorepelente

                ////Costos
                $costo_usd_gr= $planta["costo_barniz_usd_gr"];

                ////Consumos
                $consumo_usd_gr= $planta["consumo_barniz_gr_x_Mm2"]/10000000;
            }else{
                ////Costos
                $costo_usd_gr= 0;

                ////Consumos
                $consumo_usd_gr= 0;
            }

        }elseif($maquinaImpresora==2){//Maquina Impresora Normal
            
           ////Costos
           $costo_usd_gr= $planta["costo_barniz_usd_gr"];

           ////Consumos
           $consumo_usd_gr= $planta["consumo_barniz_gr_x_Mm2"]/10000000;

        }elseif($maquinaImpresora==3){//Maquina Impresora Interna
            
            return 0;

        }elseif($maquinaImpresora==5){//Maquina Dong Fang

            ////Costos
            $costo_usd_gr= $planta["costo_barniz_usd_gr"];

            ////Consumos
            $consumo_usd_gr= $planta["consumo_barniz_gr_x_Mm2"]/10000000;
        }
        
        $cobertura = $this->cobertura_barniz_cm2;

        //Calculo Costo de Barniz
        $costo_barniz_usd_mm2 = ($cobertura * $costo_usd_gr * $consumo_usd_gr) / (1 - ($this->merma_convertidora));
           
        switch ($unidad) {
            case 'usd_mm2':
                $costo_barniz = ($costo_barniz_usd_mm2)/$this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_barniz =  ((($costo_barniz_usd_mm2)/$this->area_hc * 1000)*1000)/$this->gramaje_carton;
                break;
            case 'usd_caja':
                $costo_barniz = $costo_barniz_usd_mm2;
                break;

            default:
                break;
        }    
        
   
        return $costo_barniz;
    }

    // COSTOS CINTA
    public function costo_cinta($unidad)
    {
        if (($this->cinta_desgarro) != 1) {
            return 0;
        }

        $planta = $this->planta;

        $costo_cinta_usd_caja = (($planta["precio_cinta_usd_mm"]) * $this->largura) / 1000 / (1 - ($this->merma_convertidora));
        //dd($costo_cinta_usd_caja);
        switch ($unidad) {
            case 'usd_mm2':
                $costo_cinta = $costo_cinta_usd_caja / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_cinta = ($costo_cinta_usd_caja / $this->area_hc * 1000) / $this->gramaje_carton * 1000;
                break;
            case 'usd_caja':
                $costo_cinta = $costo_cinta_usd_caja;
                break;

            default:
                # code...
                break;
        }
        return $costo_cinta;
    }

    // COSTOS ADHESIVO PEGADO 
    // Es el adhesivo que se utiliza para armar la caja cuando ya se tiene el carton hecho

    public function costo_adhesivo_pegado($unidad)
    {
        $consumo_adhesivo_pegado = $this->planta->consumos_adhesivo_pegado->first(function ($consumo_adhesivo_pegado) {
            return $consumo_adhesivo_pegado->process_id == $this->process_id;
        });
        $planta = $this->planta;
        //dd($this->planta->consumos_adhesivo_pegado->first(),$this->process_id);
        //dd($consumo_adhesivo_pegado);
       // dd($planta,$consumo_adhesivo_pegado->consumo_adhesivo_pegado_gr_caja,$planta["precio_adhesivo_powerply"]);
        $costo_adhesivo_pegado_usd_caja = (($consumo_adhesivo_pegado->consumo_adhesivo_pegado_gr_caja * $planta["precio_adhesivo_powerply"])) / (1 - ($planta["porcentaje_merma_convertidora_adhesivo_pegado"]));
        
        $costo_adhesivo_pegado = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_adhesivo_pegado = $costo_adhesivo_pegado_usd_caja / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_adhesivo_pegado = ($costo_adhesivo_pegado_usd_caja / $this->area_hc * 1000) / $this->gramaje_carton * 1000;
                break;
            case 'usd_caja':
                $costo_adhesivo_pegado = $costo_adhesivo_pegado_usd_caja;
                break;

            default:
                # code...
                break;
        }
        return $costo_adhesivo_pegado;
    }


    // COSTOS CARTULINA
    public function costo_cartulina($unidad)
    {

        $carton = $this->carton;
        $planta = $this->planta;
        $variables_cotizador = $this->variables_cotizador;


        // Si el proceso no es offset no lleva cartulina
        if ($this->process_id != 7 && $this->process_id != 9) {
            return 0;
        }
        // dd($variables_cotizador["consumo_adhesivo_emplacado_simple_gr_m2"]);
        $consumo_adhesivo_gr_m2 = 0;
        if ($carton["tipo"] == "DOBLE MONOTAPA") {
            $consumo_adhesivo_gr_m2 = $variables_cotizador["consumo_adhesivo_emplacado_doble_gr_m2"];
        } elseif ($carton["tipo"] == "SIMPLE EMPLACADO") {
            $consumo_adhesivo_gr_m2 = $variables_cotizador["consumo_adhesivo_emplacado_simple_gr_m2"];
        }

        $area_cartulina_m2 = $this->ancho_pliego_cartulina * $this->largo_pliego_cartulina / 1000000;

        $gramaje_cartulina_gr_m2 = $carton["tapa_exterior"]["gramaje"];
        $peso_cartulina_en_caja_kg_caja = $gramaje_cartulina_gr_m2 * $this->area_hc;

        $costo_pliego_cartulina_impresa_usd_caja = $this->precio_pliego_cartulina + $this->precio_impresion_pliego;
        $costo_pliego_cartulina_impresa_usd_m2 = $costo_pliego_cartulina_impresa_usd_caja / $area_cartulina_m2;

        $gramaje_carton_emplacado = $this->gramaje_carton + $gramaje_cartulina_gr_m2 + $consumo_adhesivo_gr_m2;
        $peso_carton_emplacado_kg = $gramaje_carton_emplacado * $this->area_hc / 1000;

        // Costo cartulina
        $costo_cartulina_usd_caja = ($costo_pliego_cartulina_impresa_usd_m2 * $this->area_hc * $this->gp_emplacado / $this->precio_dolar) / (1 - ($variables_cotizador["merma_emplacadora"] / 100));
        //dd($costo_cartulina_usd_caja);
        switch ($unidad) {
            case 'usd_mm2':
                $costo_cartulina = ($costo_cartulina_usd_caja / $peso_carton_emplacado_kg * 1000) * $gramaje_carton_emplacado / 1000;
                break;
            case 'usd_ton':
                $costo_cartulina = $costo_cartulina_usd_caja / $peso_carton_emplacado_kg * 1000;
                break;
            case 'usd_caja':
                $costo_cartulina = $costo_cartulina_usd_caja;
                break;

            default:
                # code...
                break;
        }
        return $costo_cartulina;
    }


    // COSTOS ADHESIVO CARTULINA
    public function costo_adhesivo_cartulina($unidad)
    {

        $carton = $this->carton;
        $planta = $this->planta;
        $variables_cotizador = $this->variables_cotizador;


        // Si el proceso no es offset no lleva cartulina
        if ($this->process_id != 7 && $this->process_id != 9) {
            return 0;
        }
        $consumo_adhesivo_gr_m2 = 0;
        if ($carton["tipo"] == "DOBLE MONOTAPA") {
            $consumo_adhesivo_gr_m2 = $variables_cotizador["consumo_adhesivo_emplacado_doble_gr_m2"];
        } elseif ($carton["tipo"] == "SIMPLE EMPLACADO") {
            $consumo_adhesivo_gr_m2 = $variables_cotizador["consumo_adhesivo_emplacado_simple_gr_m2"];
        }

        $area_cartulina_m2 = $this->ancho_pliego_cartulina * $this->largo_pliego_cartulina / 1000000;

        $gramaje_cartulina_gr_m2 = $carton["tapa_exterior"]["gramaje"];
        $peso_cartulina_en_caja_kg_caja = $gramaje_cartulina_gr_m2 * $this->area_hc;

        $costo_pliego_cartulina_impresa_usd_caja = $this->precio_pliego_cartulina + $this->precio_impresion_pliego;
        $costo_pliego_cartulina_impresa_usd_m2 = $costo_pliego_cartulina_impresa_usd_caja / $area_cartulina_m2;

        $gramaje_carton_emplacado = $this->gramaje_carton + $gramaje_cartulina_gr_m2 + $consumo_adhesivo_gr_m2;
        $peso_carton_emplacado_kg = $gramaje_carton_emplacado * $this->area_hc / 1000;

        // Costo cartulina
        $costo_adhesivo_cartulina_usd_mm2 = $consumo_adhesivo_gr_m2 * $planta["costo_adhesivo_pegado_usd_kg"] / (1 - ($variables_cotizador["merma_emplacadora"] / 100));
        //dd($costo_adhesivo_cartulina_usd_mm2);
        switch ($unidad) {
            case 'usd_mm2':
                $costo_adhesivo_cartulina = $costo_adhesivo_cartulina_usd_mm2;
                break;
            case 'usd_ton':
                $costo_adhesivo_cartulina = ($costo_adhesivo_cartulina_usd_mm2 * $this->area_hc / 1000) / $peso_carton_emplacado_kg * 1000;
                break;
            case 'usd_caja':
                $costo_adhesivo_cartulina = $costo_adhesivo_cartulina_usd_mm2 * $this->area_hc / 1000;
                break;

            default:
                # code...
                break;
        }
        return $costo_adhesivo_cartulina;
    }

    // Gramajes 
    // gramaje del carton
    public function getGramajeCartonAttribute()
    {
        $gramaje_carton = $this->gramaje_papeles() + $this->gramaje_adhesivos();
        //$gramaje_carton =1125;
        
        return $gramaje_carton;
    }
    // GRAMAJE DE PAPELES
    public function gramaje_papeles()
    {
        $carton = $this->carton;
        
        if(is_null($carton)){
            return 0;
        }

        $gramaje_papeles = 0;
       // dd($carton["tapa_interior"]["gramaje"],$carton["tapa_media"]["gramaje"],$carton["tapa_exterior"]["gramaje"],$this->carton->tipo);
        if (isset($carton["tapa_interior"])) {
            $gramaje_papeles += $carton["tapa_interior"]["gramaje"];
        }else{
            $gramaje_papeles += 0;
        }
        if (isset($carton["tapa_media"])) {
            $gramaje_papeles += $carton["tapa_media"]["gramaje"];
        }else{
            $gramaje_papeles += 0;
        }
        // Si el proceso es offset o offset con pegado debemos ignorar el gramaje de la tapa exterior

        if (isset($carton["tapa_exterior"]) && ($this->process_id != 7 && $this->process_id != 9)) {
            $gramaje_papeles += $carton["tapa_exterior"]["gramaje"];
        }else{
            $gramaje_papeles += 0;
        }

      
        // Formula para los cartones tipo POWER PLY se debe redondear
        if($this->carton->tipo == "POWER PLY"){

            if ($carton["primera_onda"]) {
                // encontramos el factor de onda segun el tipo de onda 1
                $factor_onda = $this->planta->factores_onda->first(function ($factor) {
                    return $factor->onda == $this->carton["onda_1"];
                });

                $gramaje_papeles += round($factor_onda["factor_onda"] * $carton["primera_onda"]["gramaje"], 0);
            }
    
            if ($carton["onda_powerplay"]) {
                // encontramos el factor de onda segun el tipo de onda 1_2
                $factor_onda = $this->planta->factores_onda->first(function ($factor) {
                    return $factor->onda == $this->carton["onda_1"];
                });
        
                $gramaje_papeles += $factor_onda["factor_onda"] * $carton["onda_powerplay"]["gramaje"];
                // $gramaje_papeles += 193.32; //En el archivo excel esta siempre este valor
            }
    
            if ($carton["segunda_onda"]) {
                // encontramos el factor de onda segun el tipo de onda 2
                $factor_onda = $this->planta->factores_onda->first(function ($factor) {
                    return $factor->onda == $this->carton["onda_2"];
                });
                $gramaje_papeles += round($factor_onda["factor_onda"] * $carton["segunda_onda"]["gramaje"], 0);
            }

        }else{

            //al no ser de tipo “POWER PLY” automaticamente el gramaje_onda_1_2 = 0
            $gramaje_papeles += 0;
            
            if ($carton["primera_onda"]) {
                // encontramos el factor de onda segun el tipo de onda 1
                $factor_onda = $this->planta->factores_onda->first(function ($factor) {
                    return $factor->onda == $this->carton["onda_1"];
                });
               
                $gramaje_papeles += $factor_onda["factor_onda"] * $carton["primera_onda"]["gramaje"];
            }
    
            if ($carton["segunda_onda"]) {
    
                // encontramos el factor de onda segun el tipo de onda 2

                $factor_onda = $this->planta->factores_onda->first(function ($factor) {
                    return $factor->onda == $this->carton["onda_2"];
                });

                if($factor_onda){
                    $gramaje_papeles += $factor_onda["factor_onda"] * $carton["segunda_onda"]["gramaje"];
                }
                
                //$gramaje_papeles += $factor_onda["factor_onda"] * $carton["segunda_onda"]["gramaje"];
            }

        }
        
        if($this->carton->tipo == "POWER PLY"){
            return ceil($gramaje_papeles);
        }else{    
            return $gramaje_papeles;
        }
    }


    // GRAMAJE DE ADHESIVOS
    public function gramaje_adhesivos()
    {        

        $carton = $this->carton;
        $gramaje_adhesivos = 0;
        if(is_null($carton)){
            return 0;
        }
        //Verificamos que tenga codigo valido para onda _1 (condigo_onda_1)
        if ($carton["primera_onda"]) {
            
            //1:: encontramos el consumo_adhesivo segun el tipo de onda 1
            $consumo_adhesivo = $this->planta->consumos_adhesivo->first(function ($consumo_adhesivo) {
                return $consumo_adhesivo->onda == $this->carton->onda_1;
            });

            // Si es power ply se debe sumar la onda adicional 
            // Esto es nuevo (Se agrego tambien para la primera Onda, ya que hay cartones que solo tiene una onda)
            if ($this->carton->tipo == "POWER PLY") {
                // dd($consumo_adhesivo);
                $gramaje_adhesivos += $consumo_adhesivo["adhesivo_powerply"];
            } else {
                $gramaje_adhesivos += $consumo_adhesivo["adhesivo_corrugado"];
            }
            
        }

        // la segunda onda de haberla si es power play se debe sumar el gramaje powerplay
        if ($carton["segunda_onda"]) {
            // encontramos el consumo_adhesivo segun el tipo de onda 2
            $consumo_adhesivo = $this->planta->consumos_adhesivo->first(function ($consumo_adhesivo) {
                return $consumo_adhesivo->onda == $this->carton->onda_2;
            });

            if($consumo_adhesivo){
               
                // Si es power ply se debe sumar la onda adicional 
                if ($this->carton->tipo == "POWER PLY") {
                    // dd($consumo_adhesivo);
                    $gramaje_adhesivos += $consumo_adhesivo["adhesivo_powerply"];
                } else {
                    $gramaje_adhesivos += $consumo_adhesivo["adhesivo_corrugado"];
                }
            }
        }
       
        return $gramaje_adhesivos;
    }


    ////////////////////////////////////////////////////////////////////////////
    // COSTOS INDIRECTOS 
    ////////////////////////////////////////////////////////////////////////////


    public function costo_clisses($unidad)
    {   
       
        $planta = $this->planta;
        
        // Si cliss es null o = "NO"
        if (!isset($this->clisse) || $this->clisse == 0) {
            return 0;
        }

        if ($this->numero_colores < 0 && $this->impresion > 0) {
            return 0;
        }
        
        $numero_colores= $this->obtener_numero_colores();
        
        $total_area_clisse = $numero_colores * $this->golpes_ancho * $this->golpes_largo * $this->area_hc * ($this->impresion / 100);
        //dd($total_area_clisse);
        //dd($total_area_clisse,$numero_colores,$this->golpes_ancho,$this->golpes_largo,$this->area_hc,($this->impresion / 100),$planta["costo_clisse_clp_cm2"],$this->precio_dolar);
        $costo_usd_clisses = $total_area_clisse * (($planta["costo_clisse_clp_cm2"] / $this->precio_dolar) * 10000);
        // Si varios detalles comparten un clisse entonces se divide el costo entre ellos
        $cantidad_detalles_multidestinos = $this->detalles_multidestino();
        $costo_usd_clisses = $costo_usd_clisses / ($cantidad_detalles_multidestinos + 1);

        $costo_clisses_usd_caja = $costo_usd_clisses / $this->cantidad;
         
        // dd($costo_clisses_usd_caja,$total_area_clisse * (($planta["costo_clisse_clp_cm2"] / $this->precio_dolar) * 10000), $costo_usd_clisses, $cantidad_detalles_multidestinos,$this->detalles_hermanos);
        $costo_clisses = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_clisses = ($costo_clisses_usd_caja) / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_clisses = (($costo_clisses_usd_caja) / $this->area_hc * 1000) / $this->gramaje_carton * 1000;
                break;
            case 'usd_caja':
                $costo_clisses = $costo_clisses_usd_caja;
                break;

            default:
                # code...
                break;
        } 
       // dd($costo_clisses,$numero_colores,$planta,$this->impresion)
        
        return $costo_clisses;
    }

    public function costo_clisses_new($unidad)
    {   
       
        $planta = $this->planta;
       // dd( $planta,$this->clisse);
        // Si cliss es null o = "NO"
        if (!isset($this->clisse) || $this->clisse == 0) {
            return 0;
        }
        
        /*if ($this->numero_colores < 0 && $this->impresion > 0) {
            return 0;
        }*/

       // $numero_colores= $this->obtener_numero_colores();
        
        //$total_area_clisse = $numero_colores * $this->golpes_ancho * $this->golpes_largo * $this->area_hc * ($this->impresion / 100);
        $total_area_clisse = ($this->cobertura_color_cm2 + $this->cobertura_barniz_cm2)*($this->golpes_ancho * $this->golpes_largo);
       
        //dd($total_area_clisse);
        //dd($total_area_clisse,$numero_colores,$this->golpes_ancho,$this->golpes_largo,$this->area_hc,($this->impresion / 100),$planta["costo_clisse_clp_cm2"],$this->precio_dolar);
        $costo_usd_clisses = $total_area_clisse * (($planta["costo_clisse_clp_cm2"] / $this->precio_dolar));
        // Si varios detalles comparten un clisse entonces se divide el costo entre ellos
       // $cantidad_detalles_multidestinos = $this->detalles_multidestino();
        //$costo_usd_clisses = $costo_usd_clisses / ($cantidad_detalles_multidestinos + 1);

        $costo_clisses_usd_caja = $costo_usd_clisses / $this->cantidad;
         
        // dd($costo_clisses_usd_caja,$total_area_clisse * (($planta["costo_clisse_clp_cm2"] / $this->precio_dolar) * 10000), $costo_usd_clisses, $cantidad_detalles_multidestinos,$this->detalles_hermanos);
        $costo_clisses = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_clisses = ($costo_clisses_usd_caja) / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_clisses = (($costo_clisses_usd_caja) / $this->area_hc * 1000) / $this->gramaje_carton * 1000;
                break;
            case 'usd_caja':
                $costo_clisses = $costo_clisses_usd_caja;
                break;

            default:
                # code...
                break;
        } 
       // dd($costo_clisses,$numero_colores,$planta,$this->impresion)
        
        return $costo_clisses;
    }


    // Esta funcion recibe todos los detalles que sean de una misma cotizacion 
    //y luego los compara para encontrar aquellos que compartan mismo material, clisses y matrices
    public function detalles_multidestino()
    {

        //place this before any script you want to calculate time
        // $time_start = microtime(true);
        // Display Script End time
        $detalles_multidestino = 0;
        // return $detalles_multidestino;
        // dd($this->detalles_hermanos, $this);
        foreach ($this->detalles_hermanos as $detalle) {
            if (($this->id != $detalle->id) && ($this->tipo_detalle_id == 1) && ($this->area_hc == $detalle->area_hc) && ($this->process_id == $detalle->process_id) && ($this->carton_id == $detalle->carton_id) && ($this->largura == $detalle->largura) && ($this->anchura == $detalle->anchura)
            ) {
                $detalles_multidestino = $detalles_multidestino + 1;

                // dd(
                //     $detalles_multidestino,
                //     2
                // );
            }
        }

        // $time_end = microtime(true);
        // $execution_time = ($time_end - $time_start);
        // dd($time_start, $time_end, $execution_time, $detalles_multidestino);
        // dd($detalles_multidestino);
        // return 3;
        return $detalles_multidestino;
    }
    public function costo_matriz($unidad)
    {
        // Si matriz es null o = "NO"
        if (!isset($this->matriz) || $this->matriz == 0) {
            return 0;
        }
        //1:::::: Obtenemos los metros de cuchillos de goma en base al proceso
        //Para los procesos de DIECUTTER, DICUTTER-C/PEGADO, FLEXO/MATRIZ COMPLET, DIECUTTER - ALTA GRÁFICA y DIECUTTER -C/PEGADO ALTA GRÁFICA
        if (in_array($this->process_id, [2, 4, 10, 11, 12])) {

            $cuchillos_mm = is_null($this->rubro->cuchillos_mm_matriz_completa)? 0:$this->rubro->cuchillos_mm_matriz_completa;
            $metros_cuchillos_gomas = (($this->anchura * $this->golpes_ancho * ($this->golpes_largo + 1)) 
                                      +($this->largura * $this->golpes_largo * ($this->golpes_ancho + 1)) 
                                      +($cuchillos_mm * ($this->golpes_ancho * $this->golpes_largo)))
                                      / 1000; 
         
        //Para los procesos de FLEXO/MATRIZ PARCIAL
        } elseif (in_array($this->process_id, [5])) {

            $cuchillos_mm = is_null($this->rubro->cuchillos_mm)? 0:$this->rubro->cuchillos_mm;//Cuchillos mm Matriz Parcial
            $metros_cuchillos_gomas = (($this->anchura * $this->golpes_ancho * $this->golpes_largo)/1000)+($cuchillos_mm/1000);
        
        //El resto de los procesos no lleva cuchillos
        } else {
            return 0;
        }

        //2:::::: calculo de los metros de cuchillos a dolares
        $planta = $this->planta;
        $cuchillos_gomas_usd = $metros_cuchillos_gomas * ($planta["valor_cuchillos_y_gomas_clp"] / $this->precio_dolar);

        //3:::::: Calculamos el costo total de la matriz
        $anchoHC = $this->anchura * $this->golpes_ancho + $this->orilla_ancho();
        // Se compara la anchura con el largo ya que aca se rota la caja 90 grados al ingresar a matriz
        $cantidad_tableros = ($anchoHC > $planta["largo_matriz_estandar"]) ? 2 : 1;
        $tablero_usd = $cantidad_tableros * ($planta["valor_tablero_clp"] / $this->precio_dolar);
        
        // Si varios detalles comparten una matriz entonces se divide el costo entre ellos
        $costo_matriz = $tablero_usd + $cuchillos_gomas_usd;
        $cantidad_detalles_multidestinos = $this->detalles_multidestino();
        $costo_matriz = $costo_matriz / ($cantidad_detalles_multidestinos + 1);

        //4:::::: Calculamos el costo total de la matriz por caja
        $costo_matriz_usd_caja = $costo_matriz / $this->cantidad;

        $costo_matriz = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_matriz = ($costo_matriz_usd_caja) / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_matriz = (($costo_matriz_usd_caja) / $this->area_hc * 1000) / $this->gramaje_carton * 1000;
                break;
            case 'usd_caja':
                $costo_matriz = $costo_matriz_usd_caja;
                break;

            default:
                # code...
                break;
        }
        //dd($costo_matriz);
        return $costo_matriz;
    }

    public function costo_matriz_new($unidad)
    {
        //dd($this->cuchillos_gomas);
        // Si matriz es null o = "NO"
        if (!isset($this->matriz) || $this->matriz == 0) {
            return 0;
        }

        $planta = $this->planta;

        $total_cuchillos_gomas_usd=$this->cuchillos_gomas*($planta["valor_cuchillos_y_gomas_clp"] / $this->precio_dolar);

        $anchoHC = $this->anchura * $this->golpes_ancho + $this->orilla_ancho();

        $cantidad_tableros = ($anchoHC > $planta["largo_matriz_estandar"]) ? 2 : 1;

        $tablero_usd = $cantidad_tableros * ($planta["valor_tablero_clp"] / $this->precio_dolar);

        $costo_total_matriz = $tablero_usd + $total_cuchillos_gomas_usd;
        
        $costo_matriz_usd_caja = $costo_total_matriz / $this->cantidad;

        $costo_matriz = 0;

        switch ($unidad) {
            case 'usd_mm2':
                $costo_matriz = ($costo_matriz_usd_caja) / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_matriz = (($costo_matriz_usd_caja) / $this->area_hc * 1000) / $this->gramaje_carton * 1000;
                break;
            case 'usd_caja':
                $costo_matriz = $costo_matriz_usd_caja;
                break;

            default:
                # code...
                break;
        }
        //dd($costo_matriz);
        return $costo_matriz;
    }

    //////////////////////// COSTOS Materiales de Embalaje///////////////////////////////

    public function costo_pallet($unidad)
    {
        // Si zuncho es null o = "NO"
        if (!isset($this->pallet) || $this->pallet == 0 || $this->pallet == 1) {
            return 0;
        }

        $carton = $this->carton;
        $planta = $this->planta;
        //Validacion para duplicacion o versionar cotizaciones antiguas antes del evolutivo
        //donde se maneja fija el valor de 1200 para altura de pallet y no mdiente un selector variables
        //Para estas antiguas se deja el valor fijo anterios a 1200 para calculo de cajas por pallet
        if(is_null($this->pallet_height_id) || $this->pallet_height_id == ''){
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * (1200 / ($carton["espesor"]*4));
        }else{
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            //$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*4));
        }

        //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
        //$cajas_por_pallet = round((floor((1.2 / ($this->area_hc/2)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*2));
        //$cajas_por_pallet = ((1.2 / $this->area_hc) * (1200 / $carton["espesor"])); Formula anterior
        $cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
        
        $costo_pallet_usd = $planta["costo_pallet_clp"] / $this->precio_dolar;
        //dd($costo_pallet_usd,$this->precio_dolar,$planta["costo_pallet_clp"]);
        
        $costo_pallet = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_pallet = (($costo_pallet_usd * $cantidad_pallets) / $this->cantidad) / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_pallet = ((($costo_pallet_usd * $cantidad_pallets) / $this->cantidad) / $this->area_hc * 1000) / $this->gramaje_carton * 1000;
                break;
            case 'usd_caja':
                $costo_pallet = ($costo_pallet_usd * $cantidad_pallets) / $this->cantidad;
                break;

            default:
                # code...
                break;
        }
        return $costo_pallet;
    }

    public function costo_pallet_new($unidad)
    {
      
        // Si pellet es null o = "NO"
        if (!isset($this->pallet) || $this->pallet == 0 || $this->pallet == 1 || is_null($this->pallet_height_id) || $this->pallet_height_id == '') {
            return 0;
        }
        $carton = $this->carton;
        $planta = $this->planta;
        $pallet=$this->pallet;

        if($pallet==2){// Pallet de Madera
            $costo_pallet_usd = $planta["costo_pallet_clp"] / $this->precio_dolar;
        }else{//Pallet de Carton
            $costo_pallet_usd = $planta["costo_pallet_carton_clp"] / $this->precio_dolar;
        }
        //Validacion para duplicacion o versionar cotizaciones antiguas antes del evolutivo
        //donde se maneja fija el valor de 1200 para altura de pallet y no mdiente un selector variables
        //Para estas antiguas se deja el valor fijo anterios a 1200 para calculo de cajas por pallet
        if(is_null($this->pallet_height_id) || $this->pallet_height_id == ''){
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * (1200 / ($carton["espesor"]*4));
        }else{
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            //$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*4));
        }
        //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
        //$cajas_por_pallet = round((floor((1.2 / ($this->area_hc/2)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*2));

        //$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
        //dd($cajas_por_pallet);
        // if($cajas_por_pallet<1){
        //   $cantidad_pallets = ceil($this->cantidad);
        //}else{
        $cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
        //}
        
        //dd($cajas_por_pallet,$cantidad_pallets,$this->cantidad);
        
       
        $costo_pallet = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_pallet = (($costo_pallet_usd * $cantidad_pallets) / $this->cantidad) / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_pallet = ((($costo_pallet_usd * $cantidad_pallets) / $this->cantidad) / $this->area_hc * 1000) / $this->gramaje_carton * 1000;
                break;
            case 'usd_caja':
                $costo_pallet = ($costo_pallet_usd * $cantidad_pallets) / $this->cantidad;
                break;

            default:
                # code...
                break;
        }
        return $costo_pallet;
    }

    public function costo_zuncho($unidad)
    {

        $carton = $this->carton;
        $planta = $this->planta;


        // Si zuncho es null o = "NO"
        if (!isset($this->zuncho) || $this->zuncho == 0 || $this->zuncho == 1 || is_null($this->pallet_height_id) || $this->pallet_height_id == '') {
            return 0;
        }

        //$cajas_por_pallet = ((1.2 / $this->area_hc) * (1200 / $carton["espesor"]));
        //Validacion para duplicacion o versionar cotizaciones antiguas antes del evolutivo
        //donde se maneja fija el valor de 1200 para altura de pallet y no mdiente un selector variables
        //Para estas antiguas se deja el valor fijo anterios a 1200 para calculo de cajas por pallet
        if(is_null($this->pallet_height_id) || $this->pallet_height_id == ''){
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * (1200 / ($carton["espesor"]*4));
        }else{
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            //$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*4));
        }
        //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
        //$cajas_por_pallet = round((floor((1.2 / ($this->area_hc/2)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*2));

        $cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
        //dd($cajas_por_pallet,$cantidad_pallets);
        // $costo_zuncho_usd  = $planta["zuncho_metros_por_pallet"] * ($planta["zuncho_precio_rollo_usd"] / $planta["zuncho_metros_por_rollo"]);
        $costo_zuncho_usd  = $planta["zuncho_precio_por_pallet_usd"];
        // dd($cajas_por_pallet, $cantidad_pallets, $costo_zuncho_usd);
        $costo_zuncho = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_zuncho = (($costo_zuncho_usd * $cantidad_pallets) / $this->cantidad) / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_zuncho = ((($costo_zuncho_usd * $cantidad_pallets) / $this->cantidad) / $this->area_hc * 1000) / $this->gramaje_carton * 1000;
                break;
            case 'usd_caja':
                $costo_zuncho = ($costo_zuncho_usd * $cantidad_pallets) / $this->cantidad;
                break;

            default:
                # code...
                break;
        }
        return $costo_zuncho;
    }

    public function costo_zuncho_new($unidad)
    {
       
        // Si zuncho es null o = "NO"
        if (!isset($this->zuncho) || $this->zuncho == 0 || $this->zuncho == 1 || is_null($this->pallet_height_id) || $this->pallet_height_id == '') {
            return 0;
        }
        
        $carton = $this->carton;
        $planta = $this->planta;
        $costo_zuncho_usd  = $planta["zuncho_precio_por_pallet_usd"];
       
        // $cajas_por_pallet = ((1.2 / $this->area_hc) * (1200 / $carton["espesor"]));
        //$cajas_por_pallet =  ((1.2 / $this->area_hc) * ($this->pallet_height->descripcion / $carton["espesor"]));
        /*
        $cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
        if($cajas_por_pallet<1){
            $cajas_por_pallet = (round((1.2 / $this->area_hc),1) * $this->pallet_height->descripcion / $carton["espesor"]);
        }*/
        //Validacion para duplicacion o versionar cotizaciones antiguas antes del evolutivo
        //donde se maneja fija el valor de 1200 para altura de pallet y no mdiente un selector variables
        //Para estas antiguas se deja el valor fijo anterios a 1200 para calculo de cajas por pallet
        if(is_null($this->pallet_height_id) || $this->pallet_height_id == ''){
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * (1200 / ($carton["espesor"]*4));
        }else{
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            //$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*4));
        }

        //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
        //$cajas_por_pallet = round((floor((1.2 / ($this->area_hc/2)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*2));

       
        $cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
        /*$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
        if($cajas_por_pallet<1){
            $cantidad_pallets = ceil($this->cantidad);
        }else{
            $cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
        }*/

        //$cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);

        if($this->zuncho==3){
            $conto_maquila_usd=$planta["zuncho_metros_por_pallet"] / $this->precio_dolar;
        }else{
            $conto_maquila_usd=0;
        }
        
        $costo_zuncho = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_zuncho = ((($costo_zuncho_usd +  $conto_maquila_usd) * $cantidad_pallets) / $this->cantidad) / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_zuncho = (((($costo_zuncho_usd +  $conto_maquila_usd) * $cantidad_pallets) / $this->cantidad) / $this->area_hc * 1000) / $this->gramaje_carton * 1000;
                break;
            case 'usd_caja':
                $costo_zuncho = (($costo_zuncho_usd +  $conto_maquila_usd) * $cantidad_pallets) / $this->cantidad;
                break;

            default:
                # code...
                break;
        }
        return $costo_zuncho;
    }

    public function costo_funda($unidad)
    {
        // Si funda es null o = "NO"
        if (!isset($this->funda) || $this->funda == 0) {
            return 0;
        }

        $carton = $this->carton;
        $planta = $this->planta;

       /* $cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
        if($cajas_por_pallet<1){
            $cantidad_pallets = ceil($this->cantidad);
        }else{
            $cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
        }*/
        //Validacion para duplicacion o versionar cotizaciones antiguas antes del evolutivo
        //donde se maneja fija el valor de 1200 para altura de pallet y no mdiente un selector variables
        //Para estas antiguas se deja el valor fijo anterios a 1200 para calculo de cajas por pallet
        if(is_null($this->pallet_height_id) || $this->pallet_height_id == ''){
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * (1200 / ($carton["espesor"]*4));
        }else{
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            //$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*4));
        }
        //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
        //$cajas_por_pallet = round((floor((1.2 / ($this->area_hc/2)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*2));

        $cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);

        // $costo_funda_usd  = $planta["funda_costo_clp_pallet"] / $this->precio_dolar;
        $costo_funda_usd  = $planta["funda_precio_por_pallet_usd"];
        // dd($costo_funda_usd);
        $costo_funda = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_funda = (($costo_funda_usd * $cantidad_pallets) / $this->cantidad) / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_funda = ((($costo_funda_usd * $cantidad_pallets) / $this->cantidad) / $this->area_hc * 1000) / $this->gramaje_carton * 1000;
                break;
            case 'usd_caja':
                $costo_funda = ($costo_funda_usd * $cantidad_pallets) / $this->cantidad;
                break;

            default:
                # code...
                break;
        }
        return $costo_funda;
    }

    public function costo_stretch_film($unidad)
    {

        // Si stretch_film es null o = "NO"
        if (!isset($this->stretch_film) || $this->stretch_film == 0 || is_null($this->pallet_height_id) || $this->pallet_height_id == '') {
            return 0;
        }

        $carton = $this->carton;
        $planta = $this->planta;

        //$cajas_por_pallet = ((1.2 / $this->area_hc) * (1200 / $carton["espesor"]));
       // $cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
       // $cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
        //$cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
        // $costo_stretch_film_usd  = ($planta["film_usd_kg"] / 1000) * $planta["film_gramos_pallet"];
        //if($cajas_por_pallet<1){
        //    $cantidad_pallets = ceil($this->cantidad);
        //}else{

        //Validacion para duplicacion o versionar cotizaciones antiguas antes del evolutivo
        //donde se maneja fija el valor de 1200 para altura de pallet y no mdiente un selector variables
        //Para estas antiguas se deja el valor fijo anterios a 1200 para calculo de cajas por pallet
        if(is_null($this->pallet_height_id) || $this->pallet_height_id == ''){
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * (1200 / ($carton["espesor"]*4));
        }else{
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            //$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*4));
        }
        //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
        //$cajas_por_pallet = round((floor((1.2 / ($this->area_hc/2)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*2));
        $cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
        //}
        $costo_stretch_film_usd  = $planta["film_precio_por_pallet_usd"];
        // dd($costo_stretch_film_usd);
        $costo_stretch_film = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_stretch_film = (($costo_stretch_film_usd * $cantidad_pallets) / $this->cantidad) / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_stretch_film = ((($costo_stretch_film_usd * $cantidad_pallets) / $this->cantidad) / $this->area_hc * 1000) / $this->gramaje_carton * 1000;
                break;
            case 'usd_caja':
                $costo_stretch_film = ($costo_stretch_film_usd * $cantidad_pallets) / $this->cantidad;
                break;

            default:
                # code...
                break;
        }
        return $costo_stretch_film;
    }

    public function obtener_numero_colores(){
        $numero_colores=0;
        $planta = $this->planta;
        $barniz_color=(isset($this->coverage_type_id) && $this->coverage_type_id==1)?1:0;

        if($this->print_type_id==3){//3 = Alta gráfica (0-6 colores) + Barniz
            $numero_colores=$this->numero_colores+1;
        }else{
            if($this->print_type_id==2){//2 = Delantera + Trasera
                if((($this->numero_colores+$barniz_color)<2)&&($planta->id==2)&&($this->process_id==2)){
                    $numero_colores=0;
                }else{
                    $numero_colores=$this->numero_colores;
                }
            }else{
                if($this->print_type_id==1){//1 = solo Delantera
                    if(($this->numero_colores+$barniz_color)<9){
                        $numero_colores=$this->numero_colores+$barniz_color;
                    }else{
                        $numero_colores=0;
                    }
                }
            }
        }

        return $numero_colores;
    }

    public function costo_energia($unidad)
    {
        $planta = $this->planta;
        $coverageTypeId = $this->coverageType;
        $printTypeId = $this->printType;

        // encontramos el consumo_energia segun el proceso
        $consumo_energia_clp_kwh = $this->planta->consumos_energia->first(function ($consumo_energia) {
            return $consumo_energia->process_id == $this->process_id;
        });
        // $consumo_energia_clp_kwh = 60;
        $numero_colores= $this->obtener_numero_colores();

        $factor_multiplicador=($numero_colores > 4)?2:1;
       // dd($this->merma_corrugadora,$this->merma_convertidora,$consumo_energia_clp_kwh->consumo_kwh_mm2);
        // dd($consumo_energia_clp_kwh->consumo_kwh_mm2, $this->planta->consumos_energia);
        $costo_energia_usd_mm2  = ($factor_multiplicador * $consumo_energia_clp_kwh->consumo_kwh_mm2  * $planta["precio_energia_clp_kwh"] / $this->precio_dolar)
                                  /((1 - ($this->merma_corrugadora)) * (1 - ($this->merma_convertidora)));

        $costo_energia = 0;
        switch ($unidad) { 
            case 'usd_mm2':
                $costo_energia = $costo_energia_usd_mm2;
                break;
            case 'usd_ton':
                $costo_energia = ($costo_energia_usd_mm2) / $this->gramaje_carton * 1000;
                break;
            case 'usd_caja':
                $costo_energia = ($costo_energia_usd_mm2 * $this->area_hc) / 1000;
                break;

            default:
                # code...
                break;
        }

        return $costo_energia;
    }

    public function costo_energia_new($unidad)
    {
        $planta = $this->planta;
       
        // encontramos el consumo_energia segun el proceso
        $consumo_energia_clp_kwh = $this->planta->consumos_energia->first(function ($consumo_energia) {
            return $consumo_energia->process_id == $this->process_id;
        });

        $ensamblado = $this->ensamblado;
        if($ensamblado == 1) {
            $costo_energia_ensamblado=15;
        }else{
            $costo_energia_ensamblado=0;
        }

       
        // dd($consumo_energia_clp_kwh->consumo_kwh_mm2, $this->planta->consumos_energia);
        $costo_energia_usd_mm2  = (($consumo_energia_clp_kwh->consumo_kwh_mm2 + $costo_energia_ensamblado) * $planta["precio_energia_clp_kwh"] / $this->precio_dolar)
                                  /((1 - ($this->merma_corrugadora)) * (1 - ($this->merma_convertidora)));
        
        $costo_energia = 0;
        switch ($unidad) { 
            case 'usd_mm2':
                $costo_energia = $costo_energia_usd_mm2;
                break;
            case 'usd_ton':
                $costo_energia = ($costo_energia_usd_mm2) / $this->gramaje_carton * 1000;
                break;
            case 'usd_caja':
                $costo_energia = ($costo_energia_usd_mm2 * $this->area_hc) / 1000;
                break;

            default:
                # code...
                break;
        }

        return $costo_energia;
    }


    public function costo_gas_caldera($unidad)
    {
        $planta = $this->planta;

        $costo_gas_caldera_usd_ton  = ($planta["consumo_gas_caldera_mmbtu_ton"]  * $planta["precio_gas_caldera_clp_mmbtu"] / $this->precio_dolar)
                                      / ((1 - ($this->merma_corrugadora)) * (1 - ($this->merma_convertidora)));

        dd($planta["consumo_gas_caldera_mmbtu_ton"],$planta["precio_gas_caldera_clp_mmbtu"]);
        $costo_gas_caldera = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_gas_caldera = $costo_gas_caldera_usd_ton  * $this->gramaje_carton / 1000;
                break;
            case 'usd_ton':
                $costo_gas_caldera = ($costo_gas_caldera_usd_ton);
                break;
            case 'usd_caja':
                $costo_gas_caldera = (($costo_gas_caldera_usd_ton  * $this->gramaje_carton / 1000) * $this->area_hc) / 1000;
                break;

            default:
                # code...
                break;
        }
        return $costo_gas_caldera;
    }

    public function costo_gas_caldera_new($unidad)
    {
        $planta = $this->planta;

        $costo_gas_caldera_usd_ton  = (($planta["consumo_gas_caldera_mmbtu_ton"]  * ($planta["precio_gas_caldera_usd_mmbtu"]*$this->precio_dolar)) / $this->precio_dolar)
                                      / ((1 - ($this->merma_corrugadora)) * (1 - ($this->merma_convertidora)));

        //dd($planta["consumo_gas_caldera_mmbtu_ton"],$planta["precio_gas_caldera_usd_mmbtu"],$costo_gas_caldera_usd_ton);
        $costo_gas_caldera = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_gas_caldera = $costo_gas_caldera_usd_ton  * $this->gramaje_carton / 1000;
                break;
            case 'usd_ton':
                $costo_gas_caldera = ($costo_gas_caldera_usd_ton);
                break;
            case 'usd_caja':
                $costo_gas_caldera = (($costo_gas_caldera_usd_ton  * $this->gramaje_carton / 1000) * $this->area_hc) / 1000;
                break;

            default:
                # code...
                break;
        }
        return $costo_gas_caldera;
    }

    public function costo_gas_gruas($unidad)
    {
        $planta = $this->planta;

        $costo_gas_gruas_usd_mm2  = ($planta["consumo_gas_gruas_mmbtu_mm2"] * $planta["precio_gas_gruas_clp_mmbtu"] / $this->precio_dolar);

        $costo_gas_gruas = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_gas_gruas = $costo_gas_gruas_usd_mm2;
                break;
            case 'usd_ton':
                $costo_gas_gruas = ($costo_gas_gruas_usd_mm2 / $this->gramaje_carton * 1000);
                break;
            case 'usd_caja':
                $costo_gas_gruas = ($costo_gas_gruas_usd_mm2  * $this->area_hc) / 1000;
                break;

            default:
                # code...
                break;
        }
        return $costo_gas_gruas;
    }

    public function costo_emplacadora($unidad)
    {
        // Si el proceso no es offset no lleva cartulina
        if ($this->process_id != 7 && $this->process_id != 9) {
            return 0;
        }
        $carton = $this->carton;
        $variables_cotizador = $this->variables_cotizador;


        $consumo_adhesivo_gr_m2 = 0;
        if ($carton["tipo"] == "DOBLE MONOTAPA") {
            $consumo_adhesivo_gr_m2 = $variables_cotizador["consumo_adhesivo_emplacado_doble_gr_m2"];
        } elseif ($carton["tipo"] == "SIMPLE EMPLACADO") {
            $consumo_adhesivo_gr_m2 = $variables_cotizador["consumo_adhesivo_emplacado_simple_gr_m2"];
        }

        $area_cartulina_m2 = $this->ancho_pliego_cartulina * $this->largo_pliego_cartulina / 1000000;

        $gramaje_cartulina_gr_m2 = $carton["tapa_exterior"]["gramaje"];
        $peso_cartulina_en_caja_kg_caja = $gramaje_cartulina_gr_m2 * $this->area_hc;

        $costo_pliego_cartulina_impresa_usd_caja = $this->precio_pliego_cartulina + $this->precio_impresion_pliego;
        $costo_pliego_cartulina_impresa_usd_m2 = $costo_pliego_cartulina_impresa_usd_caja / $area_cartulina_m2;

        $gramaje_carton_emplacado = $this->gramaje_carton + $gramaje_cartulina_gr_m2 + $consumo_adhesivo_gr_m2;
        $peso_carton_emplacado_kg = $gramaje_carton_emplacado * $this->area_hc / 1000;


        // Costo emplacadora
        $costo_emplacadora_usd_caja = ($variables_cotizador["costo_energia_emplacadora_usd_kw_hr"] * $variables_cotizador["consumo_energia_emplacadora_kw_hr"] / $variables_cotizador["productividad_media_emplacado_placas_hr"] / $this->gp_emplacado / $this->precio_dolar) / (1 - ($variables_cotizador["merma_emplacadora"] / 100));
        // dd($costo_emplacadora_usd_caja);
        $costo_emplacadora = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_emplacadora = ($costo_emplacadora_usd_caja / $peso_carton_emplacado_kg * 1000) * $gramaje_carton_emplacado / 1000;
                break;
            case 'usd_ton':
                $costo_emplacadora = $costo_emplacadora_usd_caja / $peso_carton_emplacado_kg * 1000;
                break;
            case 'usd_caja':
                $costo_emplacadora = $costo_emplacadora_usd_caja;
                break;

            default:
                # code...
                break;
        }
        return $costo_emplacadora;
    }


    public function costo_flete_aux($unidad)
    {
        $carton = $this->carton;
        $planta = $this->planta;
       
        if(is_null($this->flete)){
            return 0;
        }else{
            $destino= $this->flete->id;
        }
       
        
        //310 es el Id que corresponde a Retiro en Planta, retorna 0.0001 
        //(valor debe ser mayor a cero para poder continuar con cotizacion en el formulario)
        if($destino==310){
            return $this->flete->clp_pallet_buin;
        }

        // dd($this->cotizacion_id,        $this->planta_id);
        $pallets_por_pedido=1;
        if ($this->cotizacion_id) {
            // Segun el destino seleccionado buscar en listado de fletes el precio
            switch ($this->planta_id) {
                case 1:
                    
                    if(!is_null($destino)&&($destino+0)>0){
                        $expedicion_optima  = 0;//por defecto valor 0 segun excel de calculo
                        $ubicacion_destino  = ($this->flete->region!='RM')?'Region':'RM';
                        $conteo_expedicion  = 0;

                        //Validacion para duplicacion o versionar cotizaciones antiguas antes del evolutivo
                        //donde se maneja fija el valor de 1200 para altura de pallet y no mdiente un selector variables
                        //Para estas antiguas se deja el valor fijo anterios a 1200 para calculo de cajas por pallet
                        if(is_null($this->pallet_height_id) || $this->pallet_height_id == ''){
                            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
                            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * (1200 / ($carton["espesor"]*4));
                        }else{
                            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
                            //$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
                            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*4));
                        }
                        //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
                        //$cajas_por_pallet = round((floor((1.2 / ($this->area_hc/2)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*2));

                        // $cajas_por_pallet   = ((1.2 / $this->area_hc) * (1200 / $carton["espesor"]));
                        $pallets_por_pedido = ($cajas_por_pallet<=0)?0:ceil($this->cantidad / $cajas_por_pallet);
                        $tipo_expedicion=0;
                        $pallet_type=PalletType::where('id',$this->pallet_type_id)->where('active',1)->first();
                        //Calculo Conteo Expedicion
                        if($pallet_type){
                            $conteo_expedicion+= ($pallets_por_pedido<$pallet_type->cant_pallet_expedicion_26)?0:1;
                            $conteo_expedicion+= ($pallets_por_pedido<$pallet_type->cant_pallet_expedicion_27)?0:1;
                            $conteo_expedicion+= ($pallets_por_pedido<$pallet_type->cant_pallet_expedicion_28)?0:1;
                            $conteo_expedicion+= ($pallets_por_pedido<$pallet_type->cant_pallet_expedicion_29)?0:1;
                            $conteo_expedicion+= ($pallets_por_pedido<$pallet_type->cant_pallet_expedicion_30)?0:1;
                            $conteo_expedicion+= ($pallets_por_pedido<$pallet_type->cant_pallet_expedicion_36)?0:1;
                            $conteo_expedicion+= ($pallets_por_pedido<$pallet_type->cant_pallet_expedicion_40)?0:1;
                            $conteo_expedicion+= ($pallets_por_pedido<$pallet_type->cant_pallet_expedicion_41)?0:1;
                            $conteo_expedicion+= ($pallets_por_pedido<$pallet_type->cant_pallet_expedicion_42)?0:1;
                            $conteo_expedicion+= ($pallets_por_pedido<$pallet_type->cant_pallet_expedicion_43)?0:1;
                        }else{
                            $conteo_expedicion=0;
                        }

                        if($pallet_type){

                        
                            //calculo expedicion optima
                            $expedicion_optima=0;
                            if(($conteo_expedicion<6)&&($ubicacion_destino=='Region')){
                                $expedicion_optima=($pallet_type)?$pallet_type->cant_pallet_expedicion_40:0;
                                $tipo_expedicion=($pallet_type)?$pallet_type->size_pallet_expedicion_40:0;
                            }else{
                                if($conteo_expedicion>9){
                                    $expedicion_optima=($pallet_type)?$pallet_type->cant_pallet_expedicion_43:0;
                                    $tipo_expedicion=($pallet_type)?$pallet_type->size_pallet_expedicion_43:0;
                                }else{
                                    $nro_fila_pallet_expedicion_array = [5=>26,6=>27,7=>28,8=>29,9=>30,10=>36,11=>40,12=>41,13=>42,14=>43,15=>43];
                                    $nro_fila_pallet_expedicion=5+$conteo_expedicion;
                                    $aux_cant = 'cant_pallet_expedicion_'.$nro_fila_pallet_expedicion_array[$nro_fila_pallet_expedicion];
                                    $expedicion_optima=($pallet_type)?$pallet_type->$aux_cant:0;
                                    //Pensamos que el excel tiene un error en este calculo y se considero tomar dato nro_fila_pallet_expedicion y no el dato expedicion_optima
                                    //ya que pudiera fallar al obtener el valor de la BD
                                    $aux_size = 'size_pallet_expedicion_'.$nro_fila_pallet_expedicion_array[$nro_fila_pallet_expedicion];
                                    $tipo_expedicion=($pallet_type)?$pallet_type->$aux_size:0;
                                }
                            }

                            //pallets por expedicion
                            $pallets_por_expedicion=$expedicion_optima*(($this->pallets_apilados==2)?1:0.5);
                            
                            //obtener camiones necesarios
                            
                            $camiones_necesario=($pallets_por_expedicion==0)?0:ceil($pallets_por_pedido/$pallets_por_expedicion);
                            
                            $tarifa_tipo_expedicion=$this->flete->$tipo_expedicion;
                            
                            $pallets_sobrantes=$pallets_por_expedicion*$camiones_necesario-$pallets_por_pedido;
                            $espera_consolidacion=($pallets_sobrantes>2)?'Si':'No';
                            $factor_consolidacion=($espera_consolidacion=='Si')?$pallets_por_expedicion:$pallets_por_pedido;
                            $factor_consolidacion_2=($espera_consolidacion=='Si')?(1+$this->variables_cotizador->sobre_costo_consolidacion_buin):1;

                            //calcumos clp pallet del flete
                            $flete=$tarifa_tipo_expedicion*$camiones_necesario/$factor_consolidacion*$factor_consolidacion_2;
                            
                        }else{
                            $flete=0;
                        }
                      
                    }else{
                        //Segun formula si destino no existe o es null
                        $flete=0;
                    }
                    
                    //Calculamos el cosoto total Flete en USD
                    $flete_usd=$flete / $this->precio_dolar;
                    $costo_flete_usd_pallet=$flete_usd*$pallets_por_pedido;

                    //Calculamos Costo de Flete
                    break;
                case 2:
                    $flete = $this->flete->clp_pallet_tiltil;
                    // Si los pallets apilados son 1, entonces el costo del flete es el doble 
                    if ($this->pallets_apilados == 1) {
                        // dd($flete, $flete * 2);
                        $flete *= 2;
                    }

                    //Calculamos Costo de Flete

                    //$cajas_por_pallet = ((1.2 / $this->area_hc) * (1200 / $carton["espesor"]));
                    //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
                    //$cajas_por_pallet = round((floor((1.2 / ($this->area_hc/2)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*2));

                    //Validacion para duplicacion o versionar cotizaciones antiguas antes del evolutivo
                    //donde se maneja fija el valor de 1200 para altura de pallet y no mdiente un selector variables
                    //Para estas antiguas se deja el valor fijo anterios a 1200 para calculo de cajas por pallet
                    if(is_null($this->pallet_height_id) || $this->pallet_height_id == ''){
                        //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
                        $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * (1200 / ($carton["espesor"]*4));
                    }else{
                        //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
                        //$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
                        $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*4));
                    }

                    $cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
                    $costo_flete_usd_pallet  = $flete / $this->precio_dolar * $cantidad_pallets;
                    
                    break;
                case 3:
                    $flete = $this->flete->clp_pallet_osorno;

                    // Si los pallets apilados son 1, entonces el costo del flete es el doble 
                    if ($this->pallets_apilados == 1) {
                        // dd($flete, $flete * 2);
                        $flete *= 2;
                    }

                    //Calculamos Costo de Flete
                    //Validacion para duplicacion o versionar cotizaciones antiguas antes del evolutivo
                    //donde se maneja fija el valor de 1200 para altura de pallet y no mdiente un selector variables
                    //Para estas antiguas se deja el valor fijo anterios a 1200 para calculo de cajas por pallet
                    if(is_null($this->pallet_height_id) || $this->pallet_height_id == ''){
                        //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
                        $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * (1200 / ($carton["espesor"]*4));
                    }else{
                        //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
                        //$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
                        $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*4));
                    }
                    //$cajas_por_pallet = ((1.2 / $this->area_hc) * (1200 / $carton["espesor"]));
                    //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
                    //$cajas_por_pallet = round((floor((1.2 / ($this->area_hc/2)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*2));

                    $cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
                    $costo_flete_usd_pallet  = $flete / $this->precio_dolar * $cantidad_pallets;

                    break;

                default:
                    $flete = $this->flete->clp_pallet_buin;

                    // Si los pallets apilados son 1, entonces el costo del flete es el doble 
                    if ($this->pallets_apilados == 1) {
                        // dd($flete, $flete * 2);
                        $flete *= 2;
                    }

                    //Calculamos Costo de Flete
                    //Validacion para duplicacion o versionar cotizaciones antiguas antes del evolutivo
                    //donde se maneja fija el valor de 1200 para altura de pallet y no mdiente un selector variables
                    //Para estas antiguas se deja el valor fijo anterios a 1200 para calculo de cajas por pallet
                    if(is_null($this->pallet_height_id) || $this->pallet_height_id == ''){
                        //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
                        $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * (1200 / ($carton["espesor"]*4));
                    }else{
                        //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
                        //$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
                        $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*4));
                    }
                    //$cajas_por_pallet = ((1.2 / $this->area_hc) * (1200 / $carton["espesor"]));
                    //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
                    //$cajas_por_pallet = round((floor((1.2 / ($this->area_hc/2)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*2));

                    $cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
                    $costo_flete_usd_pallet  = $flete / $this->precio_dolar * $cantidad_pallets;

                    break;
            }
            //dd($flete);
        } else {
            // Si no hay flete el valor es 0
            return 0;
        }

          
        $costo_flete = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_flete = ($costo_flete_usd_pallet / $this->cantidad) / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_flete = ((($costo_flete_usd_pallet / $this->cantidad) / $this->area_hc * 1000) / $this->gramaje_carton * 1000);
                break;
            case 'usd_caja':
                $costo_flete = ($costo_flete_usd_pallet / $this->cantidad);
                break;

            default:
                # code...
                break; 
        }
       // dd($costo_flete);
        return $costo_flete;
    }

    public function costo_flete($unidad)
    {
        $carton = $this->carton;
        $planta = $this->planta;

        // dd($this->cotizacion_id,        $this->planta_id);
        if ($this->cotizacion_id) {
            // Segun el destino seleccionado buscar en listado de fletes el precio
            switch ($this->planta_id) {
                case 1:
                    $flete = $this->flete->clp_pallet_buin;
                    break;
                case 2:
                    $flete = $this->flete->clp_pallet_tiltil;
                    break;
                case 3:
                    $flete = $this->flete->clp_pallet_osorno;
                    break;

                default:
                    $flete = $this->flete->clp_pallet_buin;
                    break;
            }
            // dd($flete);
        } else {
            // Si no hay flete el valor es 0
            return 0;
        }
       
        // Si los pallets apilados son 1, entonces el costo del flete es el doble 
        if ($this->pallets_apilados == 1) {
            // dd($flete, $flete * 2);
            $flete *= 2;
        }
        //Validacion para duplicacion o versionar cotizaciones antiguas antes del evolutivo
        //donde se maneja fija el valor de 1200 para altura de pallet y no mdiente un selector variables
        //Para estas antiguas se deja el valor fijo anterios a 1200 para calculo de cajas por pallet
        if(is_null($this->pallet_height_id) || $this->pallet_height_id == ''){
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * (1200 / ($carton["espesor"]*4));
        }else{
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            //$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*4));
        }

        $cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
        $costo_flete_usd_pallet  = ($flete / $this->precio_dolar) * $cantidad_pallets;
        // dd($cantidad_pallets, $costo_flete_usd_pallet, $flete);

       
        $costo_flete = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_flete = ($costo_flete_usd_pallet / $this->cantidad) / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_flete = ((($costo_flete_usd_pallet / $this->cantidad) / $this->area_hc * 1000) / $this->gramaje_carton * 1000);
                break;
            case 'usd_caja':
                $costo_flete = ($costo_flete_usd_pallet / $this->cantidad);
                break;

            default:
                # code...
                break;
        }
        return $costo_flete;
    }

    public function costo_flete_new($unidad)
    {
        $carton = $this->carton;
        $planta = $this->planta;

        // dd($this->cotizacion_id,        $this->planta_id);
        if ($this->cotizacion_id) {
            // Segun el destino seleccionado buscar en listado de fletes el precio
            switch ($this->planta_id) {
                case 1:
                    $flete = $this->flete->clp_pallet_buin;
                    break;
                case 2:
                    $flete = $this->flete->clp_pallet_tiltil;
                    break;
                case 3:
                    $flete = $this->flete->clp_pallet_osorno;
                    break;

                default:
                    $flete = $this->flete->clp_pallet_buin;
                    break;
            }
            // dd($flete);
        } else {
            // Si no hay flete el valor es 0
            return 0;
        }

        if(is_null($this->pallet_height_id) || $this->pallet_height_id == ''){
            return 0;
        }

        // Si los pallets apilados son 1, entonces el costo del flete es el doble 
        if ($this->pallets_apilados == 1) {
            // dd($flete, $flete * 2);
            $flete *= 2;
        }

        //$cajas_por_pallet = ((1.2 / $this->area_hc) * (1200 / $carton["espesor"]));
        //$cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
        //$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
        //$cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
        //Validacion para duplicacion o versionar cotizaciones antiguas antes del evolutivo
        //donde se maneja fija el valor de 1200 para altura de pallet y no mdiente un selector variables
        //Para estas antiguas se deja el valor fijo anterios a 1200 para calculo de cajas por pallet
        if(is_null($this->pallet_height_id) || $this->pallet_height_id == ''){
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * (1200 / ($carton["espesor"]*4));
        }else{
            //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
            //$cajas_por_pallet = round((floor((1.2 / $this->area_hc)) * ($this->pallet_height->descripcion / $carton["espesor"])));
            $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/4)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*4));
        }
        //Cambio de Formula Cajas por Pallet segun solicitud de correo fecha 24/09/2024
       // $cajas_por_pallet = round((floor((1.2 / ($this->area_hc/2)))),0) * ($this->pallet_height->descripcion / ($carton["espesor"]*2));
        $cantidad_pallets = ceil($this->cantidad / $cajas_por_pallet);
        $costo_flete_usd_pallet  = $flete / $this->precio_dolar * $cantidad_pallets;
        // dd($cantidad_pallets, $costo_flete_usd_pallet, $flete);


        $costo_flete = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_flete = ($costo_flete_usd_pallet / $this->cantidad) / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_flete = ((($costo_flete_usd_pallet / $this->cantidad) / $this->area_hc * 1000) / $this->gramaje_carton * 1000);
                break;
            case 'usd_caja':
                $costo_flete = ($costo_flete_usd_pallet / $this->cantidad);
                break;

            default:
                # code...
                break;
        }
        return $costo_flete;
    }

    public function costo_financiamiento($unidad, $costo_fob)
    {
        // dd($unidad, $costo_fob, $this->cotizacion == null, $this->cotizacion->dias_pago);
        // Si no hay dias de financiamiento el valor es 0
        if ($this->cotizacion == null || $this->cotizacion->dias_pago < 1) {
            return 0;
        }
 
        // TODO
        $tasa_interes_mensual = $this->variables_cotizador->tasa_interes_mensual_financiamiento;
        $tasa_interes_diario = (pow((1 + $tasa_interes_mensual), (1 / 30))) - 1;
        //dd();
        // dd($costo_fob, $tasa_interes_diario, pow((1 + $tasa_interes_mensual), (1 / 30)));
        $costo_financiamiento_usd_caja  = ($costo_fob * (pow((1 + $tasa_interes_diario), $this->cotizacion->dias_pago))) - $costo_fob;
        //dd($costo_financiamiento_usd_caja);
        // dd($costo_fob, $tasa_interes_diario, $costo_financiamiento_usd_caja, (pow((1 + $tasa_interes_diario), $this->cotizacion->dias_pago)));
        $costo_financiamiento = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_financiamiento = $costo_financiamiento_usd_caja / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_financiamiento = (($costo_financiamiento_usd_caja / $this->area_hc * 1000) / $this->gramaje_carton * 1000);
                break;
            case 'usd_caja':
                $costo_financiamiento = $costo_financiamiento_usd_caja;
                break;

            default:
                # code...
                break;
        }
        //dd($costo_financiamiento);
        return $costo_financiamiento;
    }

    public function costo_financiamiento_new($unidad, $costo_fob)
    {
        // dd($unidad, $costo_fob, $this->cotizacion == null, $this->cotizacion->dias_pago);
        // Si no hay dias de financiamiento el valor es 0
        if ($this->cotizacion == null || $this->cotizacion->dias_pago < 1) {
            return 0;
        }
        
        if($this->cotizacion->moneda_id==1){
            $tasa_interes_mensual = $this->variables_cotizador->tasa_interes_mensual_financiamiento_usd;
        }else{
            $tasa_interes_mensual = $this->variables_cotizador->tasa_interes_mensual_financiamiento;
        }

        $tasa_interes_diario = (pow((1 + $tasa_interes_mensual), ($this->cotizacion->dias_pago / 30)));
        
        // dd($costo_fob, $tasa_interes_diario, pow((1 + $tasa_interes_mensual), (1 / 30)));
        $costo_financiamiento_usd_caja  = ($costo_fob * $tasa_interes_diario) - $costo_fob;
        //dd($costo_financiamiento_usd_caja);
        // dd($costo_fob, $tasa_interes_diario, $costo_financiamiento_usd_caja, (pow((1 + $tasa_interes_diario), $this->cotizacion->dias_pago)));
       
        $costo_financiamiento = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_financiamiento = $costo_financiamiento_usd_caja;
                break;
            case 'usd_ton':
                $costo_financiamiento = (($costo_financiamiento_usd_caja * 1000) / $this->gramaje_carton );
                break;
            case 'usd_caja':
                $costo_financiamiento = (($costo_financiamiento_usd_caja * 1000) / $this->gramaje_carton ) * $this->area_hc / 1000;
                break;
              
            default:
                # code...
                break;
        }
        //dd($costo_financiamiento);
        return $costo_financiamiento;
    }

    public function costo_royalty($unidad)
    {
        // Si no se selecciona royalty = 0
        if ($this->royalty != 1) {
            return 0;
        }
        // TODO valor de royalty por caja debe venir de base de datos
        $costo_royalty_usd_caja  = 0.01;

        $costo_royalty = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_royalty = $costo_royalty_usd_caja / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_royalty = (($costo_royalty_usd_caja / $this->area_hc * 1000) / $this->gramaje_carton * 1000);
                break;
            case 'usd_caja':
                $costo_royalty = $costo_royalty_usd_caja;
                break;

            default:
                # code...
                break;
        }
        return $costo_royalty;
    }

    public function costo_comision($unidad, $costo_fob)
    {
        // Si no se ingresa una comision o es 0
        if ($this->cotizacion == null || $this->cotizacion->comision < 1) {
            return 0;
        }
       
        $costo_comision_usd_caja  = $costo_fob * ($this->cotizacion->comision / 100);

        $costo_comision = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_comision = $costo_comision_usd_caja;//$costo_comision_usd_caja / ($this->area_hc * 1000);
                break;
            case 'usd_ton':
                $costo_comision = ($costo_comision_usd_caja / $this->gramaje_carton * 1000);
                break;
            case 'usd_caja':
                $costo_comision = ($costo_comision_usd_caja * $this->area_hc / 1000);
                break;

            default:
                # code... 
                break;
        }
        
        return $costo_comision;
    }

    public function costo_armado($unidad)
    {
        // Si no se selecciona armado = 0
        if ($this->armado_automatico != 1) {
            return 0;
        }

        $costo_armado_usd_caja  = $this->armado_usd_caja;

        $costo_armado = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_armado = $costo_armado_usd_caja / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_armado = (($costo_armado_usd_caja / $this->area_hc * 1000) / $this->gramaje_carton * 1000);
                break;
            case 'usd_caja':
                $costo_armado = $costo_armado_usd_caja;
                break;

            default:
                # code...
                break;
        }
       
        return $costo_armado;
    }

    public function ahorro($unidad)
    {
        // TODO
        // El ahorro debe provenir del carton actual
        $ahorro_usd_mm2  =$this->carton["ahorro_usd_mm2"];
        
        $ahorro = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $ahorro = $ahorro_usd_mm2;
                break;
            case 'usd_ton':
                $ahorro = ($ahorro_usd_mm2  * 1000 / $this->gramaje_carton);
                break;
            case 'usd_caja':
                $ahorro = $ahorro_usd_mm2 * $this->area_hc / 1000;
                break;

            default:
                # code...
                break;
        }
        return $ahorro;
    }

    public function costo_maquila($unidad)
    {
        // Si no se selecciona maquila = 0
        if ($this->maquila != 1) {
            return 0;
        }
        $servicio_maquila = $this->servicio_maquila;

        // dd($servicio_maquila);
        // Si existe el detalle calculamos el costo, de lo contrario tomamos el total
        if ($this->detalle_maquila_servicio_id) {
            // dd($this->detalle_maquila_servicio_id);
            $costo_maquila_clp_caja  = 0;
            if ($this->servicio_maquila->desgajado && in_array("Desgajado", $this->detalle_maquila_servicio_id)) $costo_maquila_clp_caja += $this->servicio_maquila->desgajado;
            if ($this->servicio_maquila->ensamblado  && in_array("Ensamblado", $this->detalle_maquila_servicio_id))  $costo_maquila_clp_caja += $this->servicio_maquila->ensamblado;
            if ($this->servicio_maquila->pegado  && in_array("Pegado", $this->detalle_maquila_servicio_id))  $costo_maquila_clp_caja += $this->servicio_maquila->pegado;
            if ($this->servicio_maquila->flejado  && in_array("Flejado", $this->detalle_maquila_servicio_id))  $costo_maquila_clp_caja += $this->servicio_maquila->flejado;
            if ($this->servicio_maquila->palletizado  && in_array("Palletizado", $this->detalle_maquila_servicio_id))  $costo_maquila_clp_caja += $this->servicio_maquila->palletizado;
            if ($this->servicio_maquila->empaquetado  && in_array("Empaquetado", $this->detalle_maquila_servicio_id))  $costo_maquila_clp_caja += $this->servicio_maquila->empaquetado;
            $costo_maquila_usd_caja  = $costo_maquila_clp_caja / $this->precio_dolar;
        } else {
            if(isset($servicio_maquila) && isset($servicio_maquila["precio_clp_caja"]) && !is_null($servicio_maquila["precio_clp_caja"])){
                $costo_maquila_usd_caja  = $servicio_maquila["precio_clp_caja"] / $this->precio_dolar;
            }else{
                $costo_maquila_usd_caja  = 0;
            }
           
        }

        $costo_maquila = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_maquila = $costo_maquila_usd_caja / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_maquila = (($costo_maquila_usd_caja / $this->area_hc * 1000) / $this->gramaje_carton * 1000);
                break;
            case 'usd_caja':
                $costo_maquila = $costo_maquila_usd_caja;
                break;

            default:
                # code...
                break;
        }
        return $costo_maquila;
    }

    public function costo_maquila_new($unidad)
    {
        
        // Si no se selecciona maquila = 0
        if ($this->maquila != 1) {
            return 0;
        }

        //$servicio_maquila = $this->servicio_maquila;
       
        // Si existe el detalle calculamos el costo, de lo contrario tomamos el total
        /*
        if(isset($servicio_maquila) && isset($servicio_maquila["precio_clp_caja"]) && !is_null($servicio_maquila["precio_clp_caja"])){
            $costo_maquila_usd_caja  = $servicio_maquila["precio_clp_caja"] / $this->precio_dolar;
        }else{
            $costo_maquila_usd_caja  = 0;
        }*/        
        
        $costo_maquila_usd_caja=$this->variables_cotizador->maquina_bins_usd * $this->precio_dolar;
        $costo_maquila = 0;
        //dd($costo_maquila_usd_caja /  $this->precio_dolar);
        switch ($unidad) {  
            case 'usd_mm2':
                $costo_maquila = 1000 * ($costo_maquila_usd_caja /  $this->precio_dolar) / $this->area_hc ;
                break;
            case 'usd_ton':
                $costo_maquila = ((1000 * ($costo_maquila_usd_caja /  $this->precio_dolar) / $this->area_hc) * 1000 / $this->gramaje_carton );
                break;
            case 'usd_caja':
                $costo_maquila = $costo_maquila_usd_caja /  $this->precio_dolar;
                break;

            default:
                # code...
                break;
        }
        return $costo_maquila;
    }

    public function costo_maquila_old($unidad)
    {
        // Si no se selecciona maquila = 0
        if ($this->maquila != 1) {
            return 0;
        }
        $servicio_maquila = $this->servicio_maquila;

        // dd($servicio_maquila);
        // Si existe el detalle calculamos el costo, de lo contrario tomamos el total
        if ($this->detalle_maquila_servicio_id) {
            // dd($this->detalle_maquila_servicio_id);
            $costo_maquila_clp_caja  = 0;
            if ($this->servicio_maquila->desgajado && in_array("Desgajado", $this->detalle_maquila_servicio_id)) $costo_maquila_clp_caja += $this->servicio_maquila->desgajado;
            if ($this->servicio_maquila->ensamblado  && in_array("Ensamblado", $this->detalle_maquila_servicio_id))  $costo_maquila_clp_caja += $this->servicio_maquila->ensamblado;
            if ($this->servicio_maquila->pegado  && in_array("Pegado", $this->detalle_maquila_servicio_id))  $costo_maquila_clp_caja += $this->servicio_maquila->pegado;
            if ($this->servicio_maquila->flejado  && in_array("Flejado", $this->detalle_maquila_servicio_id))  $costo_maquila_clp_caja += $this->servicio_maquila->flejado;
            if ($this->servicio_maquila->palletizado  && in_array("Palletizado", $this->detalle_maquila_servicio_id))  $costo_maquila_clp_caja += $this->servicio_maquila->palletizado;
            if ($this->servicio_maquila->empaquetado  && in_array("Empaquetado", $this->detalle_maquila_servicio_id))  $costo_maquila_clp_caja += $this->servicio_maquila->empaquetado;
            $costo_maquila_usd_caja  = $costo_maquila_clp_caja / $this->precio_dolar;
        } else {
            $costo_maquila_usd_caja  = $servicio_maquila["precio_clp_caja"] / $this->precio_dolar;
        }

        $costo_maquila = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_maquila = $costo_maquila_usd_caja / $this->area_hc * 1000;
                break;
            case 'usd_ton':
                $costo_maquila = (($costo_maquila_usd_caja / $this->area_hc * 1000) / $this->gramaje_carton * 1000);
                break;
            case 'usd_caja':
                $costo_maquila = $costo_maquila_usd_caja;
                break;

            default:
                # code...
                break;
        }
        return $costo_maquila;
    }

    public function margen($unidad)
    {
        $carton = $this->carton;
        $variables_cotizador = $this->variables_cotizador;


        $gramaje_carton = $this->gramaje_carton;

        $margen_usd_caja = $this->margen * $this->area_hc / 1000;

        // Si el proceso es offset el gramaje debe ser calculado con la cartulina
        if ($this->process_id == 7 || $this->process_id == 9) {
            $consumo_adhesivo_gr_m2 = 0;
            if ($carton["tipo"] == "DOBLE MONOTAPA") {
                $consumo_adhesivo_gr_m2 = $variables_cotizador["consumo_adhesivo_emplacado_doble_gr_m2"];
            } elseif ($carton["tipo"] == "SIMPLE EMPLACADO") {
                $consumo_adhesivo_gr_m2 = $variables_cotizador["consumo_adhesivo_emplacado_simple_gr_m2"];
            }

            $area_cartulina_m2 = $this->ancho_pliego_cartulina * $this->largo_pliego_cartulina / 1000000;

            $gramaje_cartulina_gr_m2 = $carton["tapa_exterior"]["gramaje"];
            $peso_cartulina_en_caja_kg_caja = $gramaje_cartulina_gr_m2 * $this->area_hc;

            $costo_pliego_cartulina_impresa_usd_caja = $this->precio_pliego_cartulina + $this->precio_impresion_pliego;
            $costo_pliego_cartulina_impresa_usd_m2 = $costo_pliego_cartulina_impresa_usd_caja / $area_cartulina_m2;

            $gramaje_carton_emplacado = $this->gramaje_carton + $gramaje_cartulina_gr_m2 + $consumo_adhesivo_gr_m2;
            $peso_carton_emplacado_kg = $gramaje_carton_emplacado * $this->area_hc / 1000;

            $gramaje_carton = $gramaje_carton_emplacado;
            $margen_usd_caja = (($this->margen * 1000) / $gramaje_carton) / 1000 * $peso_carton_emplacado_kg;
        }




        $margen = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $margen = $this->margen;
                break;
            case 'usd_ton':
                $margen = (($this->margen * 1000) / $gramaje_carton);
                break;
            case 'usd_caja':
                $margen = $margen_usd_caja;
                break;

            default:
                # code...
                break;
        }
        return $margen;
    }


    //PERDIDA DE PRODUCTIVIDAD TAMBIEN ES CONOCIDA COMO COSTO DE OPORTUNIDAD DE PRODUCIR PARTE (%) DE OTRO MATERIAL EN DC
    public function costo_perdida_productividad($unidad)
    {
        // Si no es proceso DIECUTTER ALTA GRÁFICA o DIECUTTER -C/PEGADO ALTA GRÁFICA
        if ($this->process_id != 11 && $this->process_id !=12) {
            return 0;
        }


        $costo_perdida_productividad_usd  = $this->variables_cotizador["perdida_productividad_ag"];
        /*
        // Obtener valor para DIECUTTER ALTA GRÁFICA
        if($this->process_id == 11){
            $costo_perdida_productividad_usd  = $this->variables_cotizador["perdida_productividad_mg_dc"]*$this->variables_cotizador["perdida_productividad_porcentaje_mayor_tiempo_dc"];
        // Obtener valor para DIECUTTER -C/PEGADO ALTA GRÁFICA
        }else{
            $costo_perdida_productividad_usd  = $this->variables_cotizador["perdida_productividad_mg_dc_pe"]*$this->variables_cotizador["perdida_productividad_porcentaje_mayor_tiempo_dc"];
        }
        */
        //dd($this->gramaje_carton);
        $costo_perdida_productividad = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_perdida_productividad = $costo_perdida_productividad_usd;
                break;
            case 'usd_ton':
                $costo_perdida_productividad = $costo_perdida_productividad_usd * 1000 / $this->gramaje_carton;
                break;
            case 'usd_caja':
                $costo_perdida_productividad = $costo_perdida_productividad_usd * $this->area_hc / 1000;
                break;

            default:
                # code...
                break;
        }
        //dd($costo_perdida_productividad);
        return $costo_perdida_productividad;
    }

    

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    // ESUINQEROS
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////


    public function preciosEsquineros()
    {
        // usd_mm2 la unidad mm2 es "Mil metros cuadrados"
        $precios = new stdClass();
        // Costos por item
        //  Costo directo
        // Materia Prima
        $precios->costo_papel_esquinero = ["usd_m" => $this->costo_papel_esquinero("usd_m"), "usd_mm2" => $this->costo_papel_esquinero("usd_mm2"), "usd_ton" => $this->costo_papel_esquinero("usd_ton"), "usd_caja" => $this->costo_papel_esquinero("usd_caja")];
        $precios->costo_adhesivo_esquinero = ["usd_m" => $this->costo_adhesivo_esquinero("usd_m"), "usd_mm2" => $this->costo_adhesivo_esquinero("usd_mm2"), "usd_ton" => $this->costo_adhesivo_esquinero("usd_ton"), "usd_caja" => $this->costo_adhesivo_esquinero("usd_caja")];
        $precios->costo_tinta_esquinero = ["usd_m" => $this->costo_tinta_esquinero("usd_m"), "usd_mm2" => $this->costo_tinta_esquinero("usd_mm2"), "usd_ton" => $this->costo_tinta_esquinero("usd_ton"), "usd_caja" => $this->costo_tinta_esquinero("usd_caja")];
        $precios->costo_offset_esquinero = ["usd_m" => $this->costo_offset_esquinero("usd_m"), "usd_mm2" => $this->costo_offset_esquinero("usd_mm2"), "usd_ton" => $this->costo_offset_esquinero("usd_ton"), "usd_caja" => $this->costo_offset_esquinero("usd_caja")];
       
        //  Costo Indirecto
        // Materiales de operacion
        $precios->costo_clisses_esquinero = ["usd_m" => $this->costo_clisses_esquinero("usd_m"), "usd_mm2" => $this->costo_clisses_esquinero("usd_mm2"), "usd_ton" => $this->costo_clisses_esquinero("usd_ton"), "usd_caja" => $this->costo_clisses_esquinero("usd_caja")];
        // Materiales de embalaje
        $precios->costo_embalaje_esquinero = ["usd_m" => $this->costo_embalaje_esquinero("usd_m"), "usd_mm2" => $this->costo_embalaje_esquinero("usd_mm2"), "usd_ton" => $this->costo_embalaje_esquinero("usd_ton"), "usd_caja" => $this->costo_embalaje_esquinero("usd_caja")];
        // Fabricacion
        $precios->costo_energia_esquinero = ["usd_m" => $this->costo_energia_esquinero("usd_m"), "usd_mm2" => $this->costo_energia_esquinero("usd_mm2"), "usd_ton" => $this->costo_energia_esquinero("usd_ton"), "usd_caja" => $this->costo_energia_esquinero("usd_caja")];
        // Servicios 
        $precios->costo_maquila_esquinero = ["usd_m" => $this->costo_maquila_esquinero("usd_m"), "usd_mm2" => $this->costo_maquila_esquinero("usd_mm2"), "usd_ton" => $this->costo_maquila_esquinero("usd_ton"), "usd_caja" => $this->costo_maquila_esquinero("usd_caja")];
        // GVV
        $precios->costo_flete_esquinero = ["usd_m" => $this->costo_flete_esquinero("usd_m"), "usd_mm2" => $this->costo_flete_esquinero("usd_mm2"), "usd_ton" => $this->costo_flete_esquinero("usd_ton"), "usd_caja" => $this->costo_flete_esquinero("usd_caja")];
        $precios->costo_comision_esquinero = ["usd_m" => $this->costo_comision_esquinero("usd_m", $precios), "usd_mm2" => $this->costo_comision_esquinero("usd_mm2", $precios), "usd_ton" => $this->costo_comision_esquinero("usd_ton", $precios), "usd_caja" => $this->costo_comision_esquinero("usd_caja", $precios)];
        $precios->costo_financiamiento_esquinero = ["usd_m" => $this->costo_financiamiento_esquinero("usd_m", $precios), "usd_mm2" => $this->costo_financiamiento_esquinero("usd_mm2", $precios), "usd_ton" => $this->costo_financiamiento_esquinero("usd_ton", $precios), "usd_caja" => $this->costo_financiamiento_esquinero("usd_caja", $precios)];


        $precios->costo_directo = [
            "usd_m" => $precios->costo_papel_esquinero["usd_m"] +  $precios->costo_adhesivo_esquinero["usd_m"] +  $precios->costo_tinta_esquinero["usd_m"] +  $precios->costo_offset_esquinero["usd_m"],
            "usd_mm2" => $precios->costo_papel_esquinero["usd_mm2"] +  $precios->costo_adhesivo_esquinero["usd_mm2"] +  $precios->costo_tinta_esquinero["usd_mm2"] +  $precios->costo_offset_esquinero["usd_mm2"],
            "usd_ton" => $precios->costo_papel_esquinero["usd_ton"] +  $precios->costo_adhesivo_esquinero["usd_ton"] +  $precios->costo_tinta_esquinero["usd_ton"] +  $precios->costo_offset_esquinero["usd_ton"],
            "usd_caja" => $precios->costo_papel_esquinero["usd_caja"] +  $precios->costo_adhesivo_esquinero["usd_caja"] +  $precios->costo_tinta_esquinero["usd_caja"] +  $precios->costo_offset_esquinero["usd_caja"],
        ];
        $precios->costo_indirecto = [
            "usd_m" => $precios->costo_clisses_esquinero["usd_m"] +  $precios->costo_embalaje_esquinero["usd_m"] +  $precios->costo_energia_esquinero["usd_m"],
            "usd_mm2" => $precios->costo_clisses_esquinero["usd_mm2"] +  $precios->costo_embalaje_esquinero["usd_mm2"] +  $precios->costo_energia_esquinero["usd_mm2"],
            "usd_ton" => $precios->costo_clisses_esquinero["usd_ton"] +  $precios->costo_embalaje_esquinero["usd_ton"] +  $precios->costo_energia_esquinero["usd_ton"],
            "usd_caja" => $precios->costo_clisses_esquinero["usd_caja"] +  $precios->costo_embalaje_esquinero["usd_caja"] +  $precios->costo_energia_esquinero["usd_caja"],
        ];
        $precios->costo_gvv = [
            "usd_m" => $precios->costo_flete_esquinero["usd_m"] +  $precios->costo_comision_esquinero["usd_m"] +  $precios->costo_financiamiento_esquinero["usd_m"],
            "usd_mm2" => $precios->costo_flete_esquinero["usd_mm2"] +  $precios->costo_comision_esquinero["usd_mm2"] +  $precios->costo_financiamiento_esquinero["usd_mm2"],
            "usd_ton" => $precios->costo_flete_esquinero["usd_ton"] +  $precios->costo_comision_esquinero["usd_ton"] +  $precios->costo_financiamiento_esquinero["usd_ton"],
            "usd_caja" => $precios->costo_flete_esquinero["usd_caja"] +  $precios->costo_comision_esquinero["usd_caja"] +  $precios->costo_financiamiento_esquinero["usd_caja"],
        ];
        $precios->costo_servicios = [
            "usd_m" => $precios->costo_maquila_esquinero["usd_m"],
            "usd_mm2" => $precios->costo_maquila_esquinero["usd_mm2"],
            "usd_ton" => $precios->costo_maquila_esquinero["usd_ton"],
            "usd_caja" => $precios->costo_maquila_esquinero["usd_caja"],
        ];

        ////Costos Fijos - Inicio
            //Costo Mano de Obra            
            $precios->costo_mano_de_obra = ["usd_mm2" => $this->costo_mano_de_obra("usd_mm2"), 
                                            "usd_ton" => $this->costo_mano_de_obra("usd_ton"), 
                                            "usd_caja" => $this->costo_mano_de_obra("usd_caja")];
            //Costo Perdida Productividad
            $precios->costo_perdida_productividad = ["usd_mm2" => $this->costo_perdida_productividad_new("usd_mm2"), 
                                                     "usd_ton" => $this->costo_perdida_productividad_new("usd_ton"), 
                                                     "usd_caja" => $this->costo_perdida_productividad_new("usd_caja")];
            //Costo Perdida Productividad Pegado
            $precios->costo_perdida_productividad_pegado = ["usd_mm2" => $this->costo_perdida_productividad_pegado_new("usd_mm2"), 
                                                            "usd_ton" => $this->costo_perdida_productividad_pegado_new("usd_ton"), 
                                                            "usd_caja" => $this->costo_perdida_productividad_pegado_new("usd_caja")];
            //Costo Fijos planta
            $precios->costo_fijos_planta = ["usd_mm2" => $this->costo_fijos_planta("usd_mm2"), 
                                            "usd_ton" => $this->costo_fijos_planta("usd_ton"), 
                                            "usd_caja" => $this->costo_fijos_planta("usd_caja")];

            //Total Costos Fijos
            $precios->costo_fijo_total = [
                "usd_mm2" => $precios->costo_mano_de_obra["usd_mm2"] + 
                             $precios->costo_perdida_productividad["usd_mm2"] + 
                             $precios->costo_perdida_productividad_pegado["usd_mm2"] +
                             $precios->costo_fijos_planta["usd_mm2"],

                "usd_ton" => $precios->costo_mano_de_obra["usd_ton"] + 
                             $precios->costo_perdida_productividad["usd_ton"]  + 
                             $precios->costo_perdida_productividad_pegado["usd_ton"] +
                             $precios->costo_fijos_planta["usd_ton"],

                "usd_caja" => $precios->costo_mano_de_obra["usd_caja"] + 
                              $precios->costo_perdida_productividad["usd_caja"]  + 
                              $precios->costo_perdida_productividad_pegado["usd_caja"] +
                              $precios->costo_fijos_planta["usd_caja"]
            ];  
        ////Costos Fijos - Fin

        ////Costos de Servir - Inicio
            //Total Costos de Servir 
                $precios->costo_servir_sin_flete = ["usd_mm2" => $this->costo_servir_sin_flete("usd_mm2"), 
                                                    "usd_ton" => $this->costo_servir_sin_flete("usd_ton"), 
                                                    "usd_caja" => $this->costo_servir_sin_flete("usd_caja")];
        ////Costos de Servir - Fin

         ////Costo Administrativos - Inicio
            //Total Costo Administrativos
            $precios->costo_administrativos = ["usd_mm2" => $this->costo_administrativos("usd_mm2"), 
                                               "usd_ton" => $this->costo_administrativos("usd_ton"), 
                                               "usd_caja" => $this->costo_administrativos("usd_caja")];
        ////Costo Administrativos - Fin

        // Total
        $precios->costo_total = [
            "usd_m" => $precios->costo_directo["usd_m"] +  $precios->costo_indirecto["usd_m"] +  $precios->costo_gvv["usd_m"] +  $precios->costo_servicios["usd_m"],
            "usd_mm2" => $precios->costo_directo["usd_mm2"] +  $precios->costo_indirecto["usd_mm2"] +  $precios->costo_gvv["usd_mm2"] +  $precios->costo_servicios["usd_mm2"] + $precios->costo_fijo_total["usd_mm2"] + $precios->costo_servir_sin_flete["usd_mm2"] + $precios->costo_administrativos["usd_mm2"],
            "usd_ton" => $precios->costo_directo["usd_ton"] +  $precios->costo_indirecto["usd_ton"] +  $precios->costo_gvv["usd_ton"] +  $precios->costo_servicios["usd_ton"] + $precios->costo_fijo_total["usd_ton"] + $precios->costo_servir_sin_flete["usd_ton"] + $precios->costo_administrativos["usd_ton"],
            "usd_caja" => $precios->costo_directo["usd_caja"] +  $precios->costo_indirecto["usd_caja"] +  $precios->costo_gvv["usd_caja"] +  $precios->costo_servicios["usd_caja"] + $precios->costo_fijo_total["usd_caja"] + $precios->costo_servir_sin_flete["usd_caja"] + $precios->costo_administrativos["usd_caja"],
        ];

        $precios->margen = [
            "usd_m" => $this->margen_esquinero("usd_m"),
            "usd_mm2" => $this->margen_esquinero("usd_mm2"),
            "usd_ton" => $this->margen_esquinero("usd_ton"),
            "usd_caja" => $this->margen_esquinero("usd_caja"),
        ];

        $precios->precio_total = [
            "usd_m" => $precios->costo_total["usd_m"] + $precios->margen["usd_m"],
            "usd_mm2" => $precios->costo_total["usd_mm2"] + $precios->margen["usd_mm2"],
            "usd_ton" => $precios->costo_total["usd_ton"] + $precios->margen["usd_ton"],
            "usd_caja" => $precios->costo_total["usd_caja"] + $precios->margen["usd_caja"],
        ];

        $precios->precio_final = [
            "usd_mm2" => ($precios->costo_total["usd_mm2"] + $precios->margen["usd_mm2"]),

            "usd_ton" => ($precios->costo_total["usd_ton"] + $precios->margen["usd_ton"]),

            "usd_caja" => ($precios->costo_total["usd_caja"] + $precios->margen["usd_caja"]),
        ];

        $precios->costo_total["clp_caja"] =  $precios->costo_total["usd_caja"] * $this->precio_dolar;
        $precios->precio_total["clp_caja"] = ($precios->costo_total["usd_caja"] + $precios->margen["usd_caja"]) * $this->precio_dolar;
        $precios->precio_final["clp_caja"] = ($precios->costo_total["usd_caja"] + $precios->margen["usd_caja"]) * $this->precio_dolar;
        // Formateo de valores para muestra de resultados
        $precios->costo_clisses = $precios->costo_clisses_esquinero;
        $precios->costo_maquila = $precios->costo_maquila_esquinero;
        $precios->costo_financiamiento = $precios->costo_financiamiento_esquinero;
        $precios->costo_flete = $precios->costo_flete_esquinero;


        return $precios;
    }


    // COSTOS DE PAPEL
    public function costo_papel_esquinero($unidad)
    {
        $costo_carton_papeles_esquinero = $this->costo_carton_papeles_esquinero($unidad);
        return $costo_carton_papeles_esquinero;
    }

    // Calcula en base a la planta seleccionada el precio de los papeles para armar carton
    public function costo_carton_papeles_esquinero($unidad)
    {
        $costo_papel_esquinero_1 = $this->costo_carton_papel_esquinero(1, $unidad);
        $costo_papel_esquinero_2 = $this->costo_carton_papel_esquinero(2, $unidad);
        $costo_papel_esquinero_3 = $this->costo_carton_papel_esquinero(3, $unidad);
        $costo_papel_esquinero_4 = $this->costo_carton_papel_esquinero(4, $unidad);
        $costo_papel_esquinero_5 = $this->costo_carton_papel_esquinero(5, $unidad);
        $costo_carton_papeles_esquinero = ($costo_papel_esquinero_1 + $costo_papel_esquinero_2 + $costo_papel_esquinero_3 + $costo_papel_esquinero_4 + $costo_papel_esquinero_5);
        return $costo_carton_papeles_esquinero;
    }

    public function costo_carton_papel_esquinero($tipo_papel, $unidad)
    {
        $carton_esquinero = $this->carton_esquinero;

        // dd($carton_esquinero);
        if ($carton_esquinero["papel_" . $tipo_papel]) {
            $gramaje_papel = $carton_esquinero["papel_" . $tipo_papel]["gramaje"] * ($carton_esquinero["ancho_" . $tipo_papel] / $carton_esquinero["ancho_esquinero"]);
            $perdida_papel =  $this->variables_cotizador["esq_perdida_papel"];//0.12;
            switch ($unidad) {
                case 'usd_m':
                    $costo_carton_papel_esquinero = $gramaje_papel * $carton_esquinero["ancho_" . $tipo_papel] / 100  * $carton_esquinero["papel_" . $tipo_papel]["precio"] / 1000000 / ($carton_esquinero["ancho_" . $tipo_papel] / $carton_esquinero["ancho_esquinero"]);
                    break;
                case 'usd_mm2':
                    $costo_carton_papel_esquinero = $carton_esquinero["papel_" . $tipo_papel]["precio"] / 1000 * $gramaje_papel;
                    break;
                case 'usd_ton':
                    // dd($this->gramaje_papeles_esquinero());
                    $costo_carton_papel_esquinero = $carton_esquinero["papel_" . $tipo_papel]["precio"] * $gramaje_papel / $this->gramaje_papeles_esquinero();
                    break;
                case 'usd_caja':
                    $costo_carton_papel_esquinero = ($gramaje_papel * $carton_esquinero["ancho_" . $tipo_papel] / 100  * $carton_esquinero["papel_" . $tipo_papel]["precio"] / 1000000 / ($carton_esquinero["ancho_" . $tipo_papel] / $carton_esquinero["ancho_esquinero"])) * $this->largo_esquinero;
                    break;

                default:
                    # code...
                    break;
            }
            $costo_carton_papel_esquinero = ($costo_carton_papel_esquinero * $carton_esquinero["capas_" . $tipo_papel]) / (1 - ($perdida_papel / 100));
            // dd($costo_carton_papel_esquinero);
            return $costo_carton_papel_esquinero;
        }

        // Si no hay papel el valor es 0
        return 0;
    }

    // GRAMAJE DE PAPELES SIN ADHESIVO
    public function gramaje_papeles_esquinero()
    {
        $carton_esquinero = $this->carton_esquinero;


        $gramaje_papeles_esquinero = 0;
        if ($carton_esquinero["papel_1"]) {
            $gramaje_papeles_esquinero += ($carton_esquinero["papel_1"]["gramaje"] * ($carton_esquinero["ancho_1"] / $carton_esquinero["ancho_esquinero"])) * $carton_esquinero["capas_1"];
        }
        if ($carton_esquinero["papel_2"]) {
            $gramaje_papeles_esquinero += ($carton_esquinero["papel_2"]["gramaje"] * ($carton_esquinero["ancho_2"] / $carton_esquinero["ancho_esquinero"])) * $carton_esquinero["capas_2"];
        }
        if ($carton_esquinero["papel_3"]) {
            $gramaje_papeles_esquinero += ($carton_esquinero["papel_3"]["gramaje"] * ($carton_esquinero["ancho_3"] / $carton_esquinero["ancho_esquinero"])) * $carton_esquinero["capas_3"];
        }
        if ($carton_esquinero["papel_4"]) {
            $gramaje_papeles_esquinero += ($carton_esquinero["papel_4"]["gramaje"] * ($carton_esquinero["ancho_4"] / $carton_esquinero["ancho_esquinero"])) * $carton_esquinero["capas_4"];
        }
        if ($carton_esquinero["papel_5"]) {
            $gramaje_papeles_esquinero += ($carton_esquinero["papel_5"]["gramaje"] * ($carton_esquinero["ancho_5"] / $carton_esquinero["ancho_esquinero"])) * $carton_esquinero["capas_5"];
        }


        // dd($gramaje_papeles_esquinero);
        return $gramaje_papeles_esquinero;
    }

    // COSTO ADHESIVO ESQUINERO
    public function costo_adhesivo_esquinero($unidad)
    {
        $carton_esquinero = $this->carton_esquinero;
        $variables_cotizador = $this->variables_cotizador;

        // dd($variables_cotizador);
        $gramos_adhesivo_esquinero = ($this->area_adhesivo_esquinero() / $carton_esquinero["ancho_esquinero"] * $variables_cotizador["esq_consumo_adhesivo"]);
        // dd($gramos_adhesivo_esquinero, $this->area_adhesivo_esquinero());
        $costo_adhesivo_esquinero = 0;
        switch ($unidad) {
            case 'usd_m':
                // gramaje: D20
                $costo_adhesivo_esquinero = $gramos_adhesivo_esquinero;
                // precio: D21
                $costo_adhesivo_esquinero = ($costo_adhesivo_esquinero  * $variables_cotizador["esq_precio_adhesivo"] / 1000000);
                // restandole la perdida: D22
                $costo_adhesivo_esquinero = $costo_adhesivo_esquinero * (1 / (1 - ($variables_cotizador["esq_perdida_adhesivo"] / 100)));
                break;
            case 'usd_mm2':
                // gramaje: E20
                $costo_adhesivo_esquinero = $gramos_adhesivo_esquinero / ($carton_esquinero["ancho_esquinero"] / 100);
                // precio: E21
                $costo_adhesivo_esquinero = ($costo_adhesivo_esquinero  * $variables_cotizador["esq_precio_adhesivo"] / 1000000);
                // restandole la perdida: E22
                $costo_adhesivo_esquinero = $costo_adhesivo_esquinero * (1 / (1 - ($variables_cotizador["esq_perdida_adhesivo"] / 100)));
                break;
            case 'usd_ton':
                // gramaje: F20
                $gramos_adhesivo_esquinero_aux  = $gramos_adhesivo_esquinero / ($carton_esquinero["ancho_esquinero"] / 100);
                $gramos_adhesivo_esquinero      = $gramos_adhesivo_esquinero_aux / $this->gramaje_papeles_esquinero() * 1000000;
                // precio: F21
                $costo_adhesivo_esquinero_aux   = ($gramos_adhesivo_esquinero_aux  * $variables_cotizador["esq_precio_adhesivo"] / 1000000); //E21
                $costo_adhesivo_esquinero       = $costo_adhesivo_esquinero_aux / $this->gramaje_papeles_esquinero() * 1000000; //F21 Final
                // restandole la perdida: F22
                $costo_adhesivo_esquinero       = $costo_adhesivo_esquinero * (1 / (1 - ($variables_cotizador["esq_perdida_adhesivo"] / 100)));

                break;
            case 'usd_caja':
                // precio: G21
                $costo_adhesivo_esquinero = $gramos_adhesivo_esquinero;// D20
                $costo_adhesivo_esquinero = ($costo_adhesivo_esquinero  * $variables_cotizador["esq_precio_adhesivo"] / 1000000); // D21
                $costo_adhesivo_esquinero = $costo_adhesivo_esquinero * (1 / (1 - ($variables_cotizador["esq_perdida_adhesivo"] / 100))); // D22
                
                // precio final:
                $costo_adhesivo_esquinero = ($costo_adhesivo_esquinero * $this->largo_esquinero);
                break;
            default:
                # code...
                break;
        }
        // solamente costo 45,49 * 1180 / 1000
        // $costo_adhesivo_esquinero = ($costo_adhesivo_esquinero  * $variables_cotizador["esq_precio_adhesivo"] / 1000000);
        // a porcentaje:
        // $costo_adhesivo_esquinero = $costo_adhesivo_esquinero * (1 / (1 - ($variables_cotizador["esq_perdida_adhesivo"] / 100)));
        // dd($costo_adhesivo_esquinero);
        return $costo_adhesivo_esquinero;
    }

    public function area_adhesivo_esquinero()
    {
        $carton_esquinero = $this->carton_esquinero;


        $area_adhesivo_esquinero = 0;
        if ($carton_esquinero["papel_1"]) {
            $area_adhesivo_esquinero += $carton_esquinero["ancho_1"] * $carton_esquinero["capas_1"];
        }
        if ($carton_esquinero["papel_2"]) {
            $area_adhesivo_esquinero += $carton_esquinero["ancho_2"] * $carton_esquinero["capas_2"];
        }
        if ($carton_esquinero["papel_3"]) {
            $area_adhesivo_esquinero += $carton_esquinero["ancho_3"] * $carton_esquinero["capas_3"];
        }
        if ($carton_esquinero["papel_4"]) {
            $area_adhesivo_esquinero += $carton_esquinero["ancho_4"] * $carton_esquinero["capas_4"];
        }
        if ($carton_esquinero["papel_5"]) {
            $area_adhesivo_esquinero += $carton_esquinero["ancho_5"] * $carton_esquinero["capas_5"];
        }


        return $area_adhesivo_esquinero;
    }

    // Gramajes con Esquinero + adhesivo
    public function gramaje_esquinero_con_adhesivo_gr_m()
    {
        $carton_esquinero = $this->carton_esquinero;
        $variables_cotizador = $this->variables_cotizador;

        $area_adhesivo_esquinero = 0;
        if ($carton_esquinero["papel_1"]) {
            $area_adhesivo_esquinero += $carton_esquinero["ancho_1"] * $carton_esquinero["capas_1"] * (($carton_esquinero["papel_1"]["gramaje"] * ($carton_esquinero["ancho_1"] / $carton_esquinero["ancho_esquinero"])));
        }
        if ($carton_esquinero["papel_2"]) {
            $area_adhesivo_esquinero += $carton_esquinero["ancho_2"] * $carton_esquinero["capas_2"] * (($carton_esquinero["papel_2"]["gramaje"] * ($carton_esquinero["ancho_2"] / $carton_esquinero["ancho_esquinero"])));
        }
        if ($carton_esquinero["papel_3"]) {
            $area_adhesivo_esquinero += $carton_esquinero["ancho_3"] * $carton_esquinero["capas_3"] * (($carton_esquinero["papel_3"]["gramaje"] * ($carton_esquinero["ancho_3"] / $carton_esquinero["ancho_esquinero"])));
        }
        if ($carton_esquinero["papel_4"]) {
            $area_adhesivo_esquinero += $carton_esquinero["ancho_4"] * $carton_esquinero["capas_4"] * (($carton_esquinero["papel_4"]["gramaje"] * ($carton_esquinero["ancho_4"] / $carton_esquinero["ancho_esquinero"])));
        }
        if ($carton_esquinero["papel_5"]) {
            $area_adhesivo_esquinero += $carton_esquinero["ancho_5"] * $carton_esquinero["capas_5"] * (($carton_esquinero["papel_5"]["gramaje"] * ($carton_esquinero["ancho_5"] / $carton_esquinero["ancho_esquinero"])));
        }

        $gramos_adhesivo_esquinero = ($this->area_adhesivo_esquinero() / $carton_esquinero["ancho_esquinero"] * $variables_cotizador["esq_consumo_adhesivo"]);
        // dd($gramos_adhesivo_esquinero);
        return ($area_adhesivo_esquinero / 100) + $gramos_adhesivo_esquinero;
    }

    public function gramaje_esquinero_con_adhesivo_gr_m2()
    {
        $carton_esquinero = $this->carton_esquinero;
        $variables_cotizador = $this->variables_cotizador;



        $gramos_adhesivo_esquinero = ($this->area_adhesivo_esquinero() / $carton_esquinero["ancho_esquinero"] * $variables_cotizador["esq_consumo_adhesivo"]);
        $gramos_adhesivo_esquinero_m2  = $gramos_adhesivo_esquinero / ($carton_esquinero["ancho_esquinero"] / 100);
        return $gramos_adhesivo_esquinero_m2 + $this->gramaje_papeles_esquinero();
    }

    // Gramajes  Esquinero + adhesivo + tinta
    public function gramaje_esquinero_adhesivo_tinta_gr_m()
    {

        $carton_esquinero = $this->carton_esquinero;
        $variables_cotizador = $this->variables_cotizador;


        $gramaje_esquinero_con_adhesivo_gr_m = $this->gramaje_esquinero_con_adhesivo_gr_m();
        if ($this->numero_colores > 0) {
            $gramos_tinta_esquinero = ($this->largo_esquinero * ($carton_esquinero["ancho_esquinero"] / 100) * $variables_cotizador["esq_consumo_tinta_g_m2"]);
        } else {
            $gramos_tinta_esquinero = 0;
        }

        $gramaje_esquinero_adhesivo_tinta_gr_m = $gramaje_esquinero_con_adhesivo_gr_m + ($gramos_tinta_esquinero * $carton_esquinero["ancho_esquinero"] / 1000);
        // dd($gramaje_esquinero_adhesivo_tinta_gr_m, $carton_esquinero["ancho_esquinero"] / 1000 *  $gramaje_esquinero_con_adhesivo_gr_m);
        return $gramaje_esquinero_adhesivo_tinta_gr_m;
    }

    public function gramaje_esquinero_adhesivo_tinta_gr_m2()
    {
        $carton_esquinero = $this->carton_esquinero;
        $variables_cotizador = $this->variables_cotizador;

        $gramaje_esquinero_con_adhesivo_gr_m2 = $this->gramaje_esquinero_con_adhesivo_gr_m2();

        if ($this->numero_colores > 0) {
            $gramos_tinta_esquinero = ($this->largo_esquinero * ($carton_esquinero["ancho_esquinero"] / 100) * $variables_cotizador["esq_consumo_tinta_g_m2"]);
        } else {
            $gramos_tinta_esquinero = 0;
        }
        $gramaje_esquinero_adhesivo_tinta_gr_m2 = $gramos_tinta_esquinero +  $gramaje_esquinero_con_adhesivo_gr_m2;
        return $gramaje_esquinero_adhesivo_tinta_gr_m2;
    }


    // 
    // COSTO TINTA ESQUINERO
    public function costo_tinta_esquinero($unidad)
    {
        // Si el numero de colores es menor a 1 el costo siempre sera 0
        if ($this->numero_colores < 1) {
            return 0;
        }

        $carton_esquinero = $this->carton_esquinero;
        $variables_cotizador = $this->variables_cotizador;



        $gramos_tinta_esquinero = ($this->largo_esquinero * ($carton_esquinero["ancho_esquinero"] / 100) * $variables_cotizador["esq_consumo_tinta_g_m2"]);
        $tinta_esquinero_usd = $gramos_tinta_esquinero / 1000 * $variables_cotizador["esq_precio_tinta_usd_kg"];
        $gramaje_esquinero_con_adhesivo_gr_m2 = $this->gramaje_esquinero_con_adhesivo_gr_m2();
        $costo_tinta_esquinero = 0;
        switch ($unidad) {
            case 'usd_m':
                $costo_tinta_esquinero = $tinta_esquinero_usd / $this->cantidad;
                break;
            case 'usd_mm2':
                $costo_tinta_esquinero = ($tinta_esquinero_usd / $this->cantidad) / ($carton_esquinero["ancho_esquinero"] / 100);
                break;
            case 'usd_ton':
                $costo_tinta_esquinero = (($tinta_esquinero_usd / $this->cantidad) / ($carton_esquinero["ancho_esquinero"] / 100)) / $gramaje_esquinero_con_adhesivo_gr_m2 * 1000000;
                break;
            case 'usd_caja':
                $costo_tinta_esquinero = ($tinta_esquinero_usd / $this->cantidad) * $this->largo_esquinero;
                break;
            default:
                # code...
                break;
        }
        return $costo_tinta_esquinero;
    }

    // COSTO offset ESQUINERO
    public function costo_offset_esquinero($unidad)
    {
        $carton_esquinero = $this->carton_esquinero;
        $variables_cotizador = $this->variables_cotizador;


        // Si el carton seleccionado no tiene ALTA GRAFICA no lleva impresión por lo tanto el costo es 0
        if ($carton_esquinero["alta_grafica"] == 0) {
            return 0;
        }

        $gramaje_esquinero_con_adhesivo_gr_m2 = $this->gramaje_esquinero_con_adhesivo_gr_m2();

        $costo_offset_esquinero = 0;
        switch ($unidad) {
            case 'usd_m':
                $costo_offset_esquinero = $variables_cotizador["esq_costo_impresion_offset"] * 1;
                break;
            case 'usd_mm2':
                $costo_offset_esquinero = ($variables_cotizador["esq_costo_impresion_offset"]) / ($carton_esquinero["ancho_esquinero"] / 100) * 1000;
                break;
            case 'usd_ton':
                $costo_offset_esquinero = (($variables_cotizador["esq_costo_impresion_offset"]) / ($carton_esquinero["ancho_esquinero"] / 100)) / $gramaje_esquinero_con_adhesivo_gr_m2 * 1000000;
                break;
            case 'usd_caja':
                $costo_offset_esquinero = $variables_cotizador["esq_costo_impresion_offset"] * $this->largo_esquinero;
                break;
            default:
                # code...
                break;
        }
        return $costo_offset_esquinero;
    }

    // Costo clisses esquinero
    public function costo_clisses_esquinero($unidad)
    {
        // Si cliss es null o = "NO"
        if (!isset($this->clisse) || $this->clisse == 0) {
            return 0;
        }

        $carton_esquinero = $this->carton_esquinero;
        $variables_cotizador = $this->variables_cotizador;


        $total_area_clisse = 30 * 15 / 10000;
        $costo_usd_clisses = $total_area_clisse * $variables_cotizador["esq_precio_clisses_clp_cm2"] / $this->precio_dolar * 10000;
        $cantidad_detalles_multidestinos = $this->detalles_multidestino();
        $costo_usd_clisses = $costo_usd_clisses / ($cantidad_detalles_multidestinos + 1);
        $costo_clisses_usd_caja = $costo_usd_clisses / $this->cantidad; // Si varios detalles comparten un clisse entonces se divide el costo entre ellos
        $costo_clisses = 0;
        switch ($unidad) {
            case 'usd_m':
                $costo_clisses = ($costo_clisses_usd_caja) / $this->largo_esquinero;
                break;
            case 'usd_mm2':
                $costo_clisses = ($costo_clisses_usd_caja) / $this->largo_esquinero / ($carton_esquinero["ancho_esquinero"] / 100) * 1000;
                break;
            case 'usd_ton':
                $costo_clisses = ($costo_clisses_usd_caja) / $this->largo_esquinero / ($carton_esquinero["ancho_esquinero"] / 100) * 1000 / $this->gramaje_esquinero_adhesivo_tinta_gr_m2() * 1000;
                break;
            case 'usd_caja':
                $costo_clisses = $costo_clisses_usd_caja;
                break;

            default:
                # code...
                break;
        }
        return $costo_clisses;
    }

    // Costo maquila esquinero
    public function costo_maquila_esquinero($unidad)
    {
        // Si cliss es null o = "NO"
        if (!isset($this->maquila) || $this->maquila == 0) {
            return 0;
        }

        $carton_esquinero = $this->carton_esquinero;
        $variables_cotizador = $this->variables_cotizador;


        $costo_maquila_usd_caja  = $variables_cotizador["esq_precio_maquila_clp_caja"] / $this->precio_dolar;

        $costo_maquila = 0;
        switch ($unidad) {
            case 'usd_m':
                $costo_maquila = $costo_maquila_usd_caja / $this->largo_esquinero;
                break;
            case 'usd_mm2':
                $costo_maquila = ($costo_maquila_usd_caja / $this->largo_esquinero) / ($carton_esquinero["ancho_esquinero"] / 100) * 1000;
                break;
            case 'usd_ton':
                $costo_maquila = ($costo_maquila_usd_caja / $this->largo_esquinero) / ($carton_esquinero["ancho_esquinero"] / 100) * 1000 / $this->gramaje_esquinero_con_adhesivo_gr_m2() * 1000;
                break;
            case 'usd_caja':
                $costo_maquila = $costo_maquila_usd_caja;
                break;

            default:
                # code...
                break;
        }
        return $costo_maquila;
    }

    // Costo embalaje esquinero
    public function costo_embalaje_esquinero($unidad)
    {
        $carton_esquinero = $this->carton_esquinero;
        $variables_cotizador = $this->variables_cotizador;
        $palletizadoPorInsumo = $this->detalle_palletizado;
        $precioInsumos = InsumosPalletizado::pluck("precio", "insumo");


        // dd($palletizadoPorInsumo, $precioInsumos);
        // $palletizadoPorInsumo = DetallePrecioPalletizado::find($this->tipo_destino_esquinero);

        $costo_embalaje = 0;
        $costo_embalaje += ($palletizadoPorInsumo["tarima_nacional"] * $precioInsumos["tarima_nacional"]);
        $costo_embalaje += ($palletizadoPorInsumo["tarima_exportacion"] * $precioInsumos["tarima_exportacion"]);
        $costo_embalaje += ($palletizadoPorInsumo["liston_nacional"] * $precioInsumos["liston_nacional"]);
        $costo_embalaje += ($palletizadoPorInsumo["liston_exportacion"] * $precioInsumos["liston_exportacion"]);
        $costo_embalaje += ($palletizadoPorInsumo["tabla_tarima"] * $precioInsumos["tabla_tarima"]);
        $costo_embalaje += ($palletizadoPorInsumo["stretch_film"] * $precioInsumos["stretch_film"]);
        $costo_embalaje += ($palletizadoPorInsumo["sellos"] * $precioInsumos["sellos"]);
        $costo_embalaje += ($palletizadoPorInsumo["zunchos"] * $precioInsumos["zunchos"]);
        $costo_embalaje += ($palletizadoPorInsumo["cordel_y_clavos"] * $precioInsumos["cordel_y_clavos"]);
        $costo_embalaje += ($palletizadoPorInsumo["maquila"] * $precioInsumos["maquila"]);

        // Si lleva funda se suma al costo
        if ($this->funda_esquinero == 1) {
            $costo_embalaje += ($palletizadoPorInsumo["fundas"] * $precioInsumos["fundas"]);
        }

        $costo_embalaje_usd_m = $costo_embalaje / ($variables_cotizador["esq_esquineros_por_pallet"] * 2.05 * $this->precio_dolar);
        // dd($costo_embalaje, $costo_embalaje_usd_m, $variable_cotizador->esq_esquineros_por_pallet, $this->precio_dolar, ($variable_cotizador->esq_esquineros_por_pallet * 2.05 * $this->precio_dolar));
        // dd($this->gramaje_esquinero_con_adhesivo_gr_m2());
        switch ($unidad) {
            case 'usd_m':
                $costo_embalaje = $costo_embalaje_usd_m;
                break;
            case 'usd_mm2':
                $costo_embalaje = $costo_embalaje_usd_m / ($carton_esquinero["ancho_esquinero"] / 100) * 1000;
                break;
            case 'usd_ton':
                $costo_embalaje = ($costo_embalaje_usd_m / ($carton_esquinero["ancho_esquinero"] / 100) * 1000) / $this->gramaje_esquinero_con_adhesivo_gr_m2() * 1000;
                break;
            case 'usd_caja':
                $costo_embalaje = $costo_embalaje_usd_m / $this->largo_esquinero;
                break;

            default:
                # code...
                break;
        }
        return $costo_embalaje;
    }

    // Costo energia esquinero
    public function costo_energia_esquinero($unidad)
    {
        $carton_esquinero = $this->carton_esquinero;
        $variables_cotizador = $this->variables_cotizador;



        $consumo_motor = $variables_cotizador["esq_energia_consumo_m4"] + $variables_cotizador["esq_energia_consumo_m5"];
        $consumo_iluminacion = $variables_cotizador["esq_energia_iluminacion_m4"] + $variables_cotizador["esq_energia_iluminacion_m5"];
        $precio_kw_hr = $variables_cotizador["esq_energia_precio_kw_hr"];
        $costo_clp_hr = $precio_kw_hr * ($consumo_iluminacion + $consumo_motor);
        $costo_energia_usd_hr = $costo_clp_hr / $this->precio_dolar;
        // dd($consumo_motor, $consumo_iluminacion, $precio_kw_hr, $costo_clp_hr, $costo_energia_usd_hr);
        $costo_energia_usd_m = $costo_energia_usd_hr / $variables_cotizador["esq_produccion_m_hr"] * (($variables_cotizador["esq_energia_asignacion_m4"] + $variables_cotizador["esq_energia_asignacion_m5"]) / 2 / 100);
        switch ($unidad) {
            case 'usd_m':
                $costo_energia = $costo_energia_usd_m;
                break;
            case 'usd_mm2':
                $costo_energia = $costo_energia_usd_m / ($carton_esquinero["ancho_esquinero"] / 100) * 1000;
                break;
            case 'usd_ton':
                $costo_energia = ($costo_energia_usd_m / ($carton_esquinero["ancho_esquinero"] / 100) * 1000) / $this->gramaje_esquinero_con_adhesivo_gr_m2() * 1000;
                break;
            case 'usd_caja':
                $costo_energia = $costo_energia_usd_hr / $variables_cotizador["esq_produccion_m_hr"] * (($variables_cotizador["esq_energia_asignacion_m4"] + $variables_cotizador["esq_energia_asignacion_m5"]) / 2 / 100);
                break;

            default:
                # code...
                break;
        }
        return $costo_energia;
    }

    // Costo flete esquinero
    public function costo_flete_esquinero($unidad)
    {
        $carton_esquinero = $this->carton_esquinero;
        $variables_cotizador = $this->variables_cotizador;


        if ($this->cotizacion_id) {
            // Segun el destino seleccionado buscar en listado de fletes el precio
            $flete = $this->flete->valor_usd_camion;
            // dd($flete);
        } else {
            // Si no hay flete el valor es 0
            return 0;
            // $flete = 312463.6;
        }


        $largo_camion = ($this->tipo_camion_esquinero == 1) ? 7 : 12;
        $ancho_camion = 2.6;
        $medida_pallet = 0.9;
        $pallet_a_lo_largo = floor($largo_camion / $this->largo_esquinero);
        $pallet_extras_a_lo_ancho = ((($largo_camion - $pallet_a_lo_largo) * $this->largo_esquinero) > $medida_pallet) ? 1 : 0;
        $total_pallets = 5;
        $unidades_por_pallet = ($this->largo_esquinero <= 1) ? 3000 : $variables_cotizador["esq_esquineros_por_pallet"];
        $cantidad_de_pallet = ceil($this->cantidad / $unidades_por_pallet);
        $unidades_por_camion = $total_pallets * $unidades_por_pallet;
        // $costo_flete_usd_m = ($flete / ($unidades_por_camion * $this->largo_esquinero)) / $this->precio_dolar;
        $costo_flete_usd_m = ($flete * ($cantidad_de_pallet * ($unidades_por_pallet / $unidades_por_camion)) / ($this->largo_esquinero * $this->cantidad)) / $this->precio_dolar;
        // dd($this->largo_esquinero, ($this->largo_esquinero >= 1), $costo_flete_usd_m, $cantidad_de_pallet, $total_pallets, $flete, $unidades_por_pallet, $unidades_por_camion);
        switch ($unidad) {
            case 'usd_m':
                $costo_flete = $costo_flete_usd_m;
                break;
            case 'usd_mm2':
                $costo_flete = $costo_flete_usd_m / ($carton_esquinero["ancho_esquinero"] / 100) * 1000;
                break;
            case 'usd_ton':
                $costo_flete = ($costo_flete_usd_m / ($carton_esquinero["ancho_esquinero"] / 100) * 1000) / $this->gramaje_esquinero_con_adhesivo_gr_m2() * 1000;
                break;
            case 'usd_caja':
                $costo_flete = $costo_flete_usd_m * $this->largo_esquinero;
                break;

            default:
                # code...
                break;
        }
        return $costo_flete;
    }

    // Costo comision esquinero
    public function costo_comision_esquinero($unidad, $precios)
    {
        if (!$this->cotizacion) return 0;

        $precio = $precios->costo_papel_esquinero[$unidad] + $precios->costo_adhesivo_esquinero[$unidad] + $precios->costo_tinta_esquinero[$unidad] + $precios->costo_offset_esquinero[$unidad] + $precios->costo_embalaje_esquinero[$unidad] + $precios->costo_energia_esquinero[$unidad];
        $comision = $this->cotizacion->comision / 100;
        switch ($unidad) {
            case 'usd_m':
                $costo_comision = $comision * ($precio - $precios->costo_flete_esquinero[$unidad]);
                break;
            case 'usd_mm2':
                $costo_comision = $comision * (($precio + $this->cotizacion->margen) - $precios->costo_flete_esquinero[$unidad]);
                break;
            case 'usd_ton':
                $costo_comision = $comision * (($precio + ($this->cotizacion->margen / $this->gramaje_esquinero_adhesivo_tinta_gr_m2() * 1000)) - $precios->costo_flete_esquinero[$unidad]);
                break;
            case 'usd_caja':
                $costo_comision = $comision * ($precio - $precios->costo_flete_esquinero[$unidad]);
                break;

            default:
                # code...
                break;
        }
        return $costo_comision;
    }


    // Costo financiamiento esquinero
    public function costo_financiamiento_esquinero($unidad, $precios)
    {
        if (!$this->cotizacion || $this->cotizacion->dias_pago < 1) return 0;

        $variables_cotizador = $this->variables_cotizador;


        $precio = $precios->costo_papel_esquinero[$unidad] + $precios->costo_adhesivo_esquinero[$unidad] + $precios->costo_tinta_esquinero[$unidad] + $precios->costo_offset_esquinero[$unidad] + $precios->costo_embalaje_esquinero[$unidad] + $precios->costo_energia_esquinero[$unidad];
        $financiamiento = (pow((1 + ($variables_cotizador["tasa_mensual_credito"] / 100) / 30), ($this->cotizacion->dias_pago)) - 1) * 1 / 100;
        // dd($financiamiento, $variable_cotizador->tasa_mensual_credito, $this->cotizacion->dias_pago, $this->cotizacion->margen);
        switch ($unidad) {
            case 'usd_m':
                $costo_financiamiento = $financiamiento * ($precio - $precios->costo_flete_esquinero[$unidad]);
                break;
            case 'usd_mm2':
                $costo_financiamiento = $financiamiento * (($precio + $this->cotizacion->margen) - $precios->costo_flete_esquinero[$unidad]);
                break;
            case 'usd_ton':
                $costo_financiamiento = $financiamiento * (($precio + ($this->cotizacion->margen / $this->gramaje_esquinero_adhesivo_tinta_gr_m2() * 1000)) - $precios->costo_flete_esquinero[$unidad]);
                break;
            case 'usd_caja':
                $costo_financiamiento = $financiamiento * ($precio - $precios->costo_flete_esquinero[$unidad]);
                break;

            default:
                # code...
                break;
        }
        return $costo_financiamiento;
    }

    public function margen_esquinero($unidad)
    {
        // Si no se selecciona maquila = 0
        // if ($this->margen < 1) {
        //     return 0;
        // }
        // dd($this->gramaje_esquinero_adhesivo_tinta_gr_m(), (($this->margen) / $this->gramaje_esquinero_adhesivo_tinta_gr_m2() * 1000) * $this->gramaje_esquinero_adhesivo_tinta_gr_m());
        $margen = 0;
        switch ($unidad) {
            case 'usd_m':
                $margen = (($this->margen) / $this->gramaje_esquinero_adhesivo_tinta_gr_m2() * 1000) * $this->gramaje_esquinero_adhesivo_tinta_gr_m() / 1000000;
                break;
            case 'usd_mm2':
                $margen = $this->margen;
                break;
            case 'usd_ton':
                $margen = (($this->margen) / $this->gramaje_esquinero_adhesivo_tinta_gr_m2() * 1000);
                break;
            case 'usd_caja':
                $margen = ((($this->margen) / $this->gramaje_esquinero_adhesivo_tinta_gr_m2() * 1000) * $this->gramaje_esquinero_adhesivo_tinta_gr_m() / 1000000) * $this->largo_esquinero;
                break;

            default:
                # code...
                break;
        }
        return $margen;
    }

    public function costo_perdida_productividad_new($unidad)
    {
       
        // Si no es proceso DIECUTTER ALTA GRÁFICA o DIECUTTER -C/PEGADO ALTA GRÁFICA
        if ($this->process_id != 11 && $this->process_id !=12) {
            return 0;
        }else{

        }

        $costo_perdida_productividad_usd  = $this->variables_cotizador["perdida_productividad_ag"];
        /*
        // Obtener valor para DIECUTTER ALTA GRÁFICA
        if($this->process_id == 11){
            $costo_perdida_productividad_usd  = $this->variables_cotizador["perdida_productividad_mg_dc"];
        // Obtener valor para DIECUTTER -C/PEGADO ALTA GRÁFICA
        }else{
            $costo_perdida_productividad_usd  = $this->variables_cotizador["perdida_productividad_mg_dc_pe"];
        }
        */
        $costo_perdida_productividad = 0;
        switch ($unidad) {
            case 'usd_mm2':
                $costo_perdida_productividad = $costo_perdida_productividad_usd;
                break;
            case 'usd_ton':
                $costo_perdida_productividad = $costo_perdida_productividad_usd * 1000 / $this->gramaje_carton;
                break;
            case 'usd_caja':
                $costo_perdida_productividad = $costo_perdida_productividad_usd * $this->area_hc / 1000;
                break;

            default:
                # code...
                break;
        }
        //dd($costo_perdida_productividad);
        return $costo_perdida_productividad;
    }

    public function costo_perdida_productividad_pegado_new($unidad)
    {
        // Si no es proceso DIECUTTER ALTA GRÁFICA o DIECUTTER -C/PEGADO ALTA GRÁFICA

        if ($this->process_id != 4 && $this->process_id !=12) {
            return 0;    
        }
    
        //Obtener valor perdida productividad pegado 3 y 4 puntos
        $costo_perdida_productividad_pegado_usd=$this->variables_cotizador["perdida_productividad_pegado_3_y_4_puntos"];

        //$costo_perdida_productividad_pegado = 0;
    
        switch ($unidad) {

            case 'usd_mm2':
                $costo_perdida_productividad_pegado = $costo_perdida_productividad_pegado_usd;
                break;

            case 'usd_ton':
                $costo_perdida_productividad_pegado = $costo_perdida_productividad_pegado_usd * 1000 / $this->gramaje_carton;
                break;

            case 'usd_caja':
                $costo_perdida_productividad_pegado = $costo_perdida_productividad_pegado_usd * $this->area_hc / 1000;
                break;

            default:
                # code...
                break;
        }
    
        return $costo_perdida_productividad_pegado;
        
    }

    public function costo_mano_de_obra($unidad)
    {
       
        
        if(!isset($this->carton['onda']) || !isset($this->process_id)){
            return 0;
        }  

        $planta_id = $this->planta['id'];
        $onda= $this->carton['onda'];
        $proceso_id= $this->process_id;
        $product_type_id= $this->product_type_id;
        $ensamblado = $this->ensamblado;
        $desgajado_cabezal = $this->desgajado_cabezal;

        //dd($product_type_id,$ensamblado,$desgajado_cabezal);
        
        $mano_obra_mantencion = ManoObraMantencion::where('onda', $onda)
                                                    ->where('proceso_id',$proceso_id)
                                                    ->where('active',1)
                                                    ->where('deleted',0)    
                                                    ->first();
       
        if($mano_obra_mantencion){
            if($planta_id==1){//Planta Buin
                $costo_mano_de_obra_clp = (is_null($mano_obra_mantencion->costo_buin))?0:$mano_obra_mantencion->costo_buin; 

                if($product_type_id==18){//Producto Tipo Set Tabiques
                    $factor_divisor_set_tabiques=2;
                }else{
                    $factor_divisor_set_tabiques=1;
                }

                if($ensamblado==1){//Ensamblado
                    $usd_unid_ensamblado=$this->variables_cotizador->ensamblado_usd_unid;
                    $factor_divisor_ensamblado= 1000 * $usd_unid_ensamblado / $this->area_hc;

                }else{
                    $factor_divisor_ensamblado=0;
                }

                if($desgajado_cabezal==1){//Ensamblado
                    $usd_unid_desgajado=$this->variables_cotizador->desgajado_usd_unid;
                    $factor_divisor_desgajado= 1000 * $usd_unid_desgajado / $this->area_hc;

                }else{
                    $factor_divisor_desgajado=0;
                }
                $costo_mano_de_obra_usd = $costo_mano_de_obra_clp / $this->precio_dolar / $factor_divisor_set_tabiques + $factor_divisor_ensamblado + $factor_divisor_desgajado; 


            }elseif ($planta_id==2) {//Planta TilTil
                $costo_mano_de_obra_clp = (is_null($mano_obra_mantencion->costo_tiltil))?0:$mano_obra_mantencion->costo_tiltil;
                $costo_mano_de_obra_usd = $costo_mano_de_obra_clp / $this->precio_dolar;
            }elseif ($planta_id==3) {//Planta Osorno
                $costo_mano_de_obra_clp = (is_null($mano_obra_mantencion->costo_osorno))?0:$mano_obra_mantencion->costo_osorno;
                $costo_mano_de_obra_usd = $costo_mano_de_obra_clp / $this->precio_dolar;
            }else{
                $costo_mano_de_obra_clp = 0;
                $costo_mano_de_obra_usd = $costo_mano_de_obra_clp / $this->precio_dolar;
            }            
        }else{
            $costo_mano_de_obra_clp = 0;
            $costo_mano_de_obra_usd = $costo_mano_de_obra_clp / $this->precio_dolar;
        }
       
        /*
        // Si no se selecciona armado = 0

        if ($this->process_id!=4 && $this->process_id!=12) {
            return 0;
        }

        $costo_mano_de_obra = 0;

        //Preceso de Pegado
        if($this->process_id==4){//DIECUTTER-C/PEGADO
            $costo_mano_de_obra= $planta['mano_de_obra_pegado_usd_x_Mm2'];
        }else{//DIECUTTER -C/PEGADO ALTA GRÁFICA
            $costo_mano_de_obra= $planta['mano_de_obra_pegado_ag_usdx_Mm2'];
        }*/

        

        switch ($unidad) {

            case 'usd_mm2':
                $costo_mano_de_obra = $costo_mano_de_obra_usd;
                break;

            case 'usd_ton':
                $costo_mano_de_obra = (($costo_mano_de_obra_usd * 1000) / $this->gramaje_carton);
                break;

            case 'usd_caja':
                $costo_mano_de_obra = (($costo_mano_de_obra_usd * $this->area_hc) / 1000);
                break;

            default:
                # code...
                break;
        }

        return $costo_mano_de_obra;

    }

    public function costo_fijos_planta($unidad)
    {

        $planta = $this->planta;
        $costo_fijo_clp_ton = $planta["costo_fijo_clp_ton"];
                      
        //Calculo Costo de fijo usd por tonelada
        $costo_fijo_usd_ton = $costo_fijo_clp_ton / $this->precio_dolar;
        
        switch ($unidad) {
            case 'usd_mm2':
                $costo_fijo =  $costo_fijo_usd_ton * $this->gramaje_carton / 1000;
                break;
            case 'usd_ton':
                $costo_fijo =  $costo_fijo_usd_ton; 
                break;
            case 'usd_caja':
                $costo_fijo =  $this->area_hc *  ($costo_fijo_usd_ton * $this->gramaje_carton / 1000) / 1000;
                break;

            default:
                break;
        }    
        
        return $costo_fijo;
    }

    public function costo_servir_sin_flete($unidad)
    {   
        //validar si es null o vacio la clasificacion del cliente
        //dd($this->cotizacion_id);

        if(is_null($this->cotizacion_id) || is_null($this->gramaje_carton) || $this->gramaje_carton==0 || !isset($this->cotizacion->client)){
            return 0;
        }

        //Asignamos el costo de la clasificacion actual que tiene el cliente
        $costo_servir_sin_flete_usd=$this->cotizacion->client->ClasificacionCliente->costo;
              
        switch ($unidad) {
            case 'usd_mm2':
                $costo_servir_sin_flete =  $costo_servir_sin_flete_usd;//$costo_servir_sin_flete_usd_ton * $this->gramaje_carton / 1000;
                break;
            case 'usd_ton':
                $costo_servir_sin_flete =  $costo_servir_sin_flete_usd * 1000 / $this->gramaje_carton; 
                break;
            case 'usd_caja':
                $costo_servir_sin_flete =  $this->area_hc * $costo_servir_sin_flete_usd / 1000;
                break;

            default:
                break;
        }    
        
        return $costo_servir_sin_flete;
    }

    public function costo_administrativos($unidad)
    {   

        if(is_null($this->cotizacion_id) || is_null($this->gramaje_carton) || $this->gramaje_carton==0){
            return 0;
        }

        //Valor variable costo fijo administrativo usd_mm2
        $costo_administrativo_usd_mm2 = $this->variables_cotizador->costo_fijo_administrativo;
                      
        switch ($unidad) {
            case 'usd_mm2':
                $costo_administrativo =  $costo_administrativo_usd_mm2;
                break;
            case 'usd_ton':
                $costo_administrativo =  $costo_administrativo_usd_mm2 * 1000 / $this->gramaje_carton; 
                break;
            case 'usd_caja':
                $costo_administrativo =  $this->area_hc * $costo_administrativo_usd_mm2 / 1000;
                break;
            default:
                break;
        }    
        
        return $costo_administrativo;
    }

    public function mg_ebitda()
    {   
        //validar si es null o vacio la clasificacion del cliente
        //dd($this->cotizacion_id);

        if(is_null($this->cotizacion_id) || !isset($this->cotizacion->client)){
            return 0;
        }

        $clasificacion_cliente = $this->cotizacion->client->clasificacion;
        $rubro = $this->rubro_id;

        $mg_ebitda = PorcentajeMargen::where('clasificacion_cliente_id', $clasificacion_cliente)
                                    ->where('rubro_id', $rubro)
                                    ->where('active', 1)
                                    ->first();
        if($mg_ebitda){
            return $mg_ebitda->ebitda_esperado;
        }else{
            return 0;
        }                          

        
    }
}
