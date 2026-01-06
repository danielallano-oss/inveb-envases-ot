<?php

namespace App;

use App\Presenters\WorkOrderPresenter;
use Illuminate\Database\Eloquent\Model;
use App\SystemVariable;
use OwenIt\Auditing\Contracts\Auditable;

class WorkOrder extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];
    protected $appends = ['area_hm', 'area_hc', 'vendedor'];
    protected $dates = [
        'ultimo_cambio_area'
    ];
    private $CONSUMO_TINTA = 5;
    private $CONSUMO_ADHESIVO = 4;
    private $CONSUMO_CERA = 28;
    private $CONSUMO_HIDROPELENTE = 25;
    private $CONSUMO_BARNIZ_UV = 20;

    public function subsubhierarchy()
    {
        return $this->belongsTo(Subsubhierarchy::class);
    }

    public function cad_asignado()
    {
        return $this->belongsTo(Cad::class, "cad_id");
    }
    public function material()
    {
        return $this->belongsTo(Material::class, "material_id");
    }
    public function material_referencia()
    {
        return $this->belongsTo(Material::class, "reference_id");
    }



    public function canal()
    {
        return $this->belongsTo(Canal::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function installation()
    {
        return $this->hasOne(Installation::class,'id','instalacion_cliente');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_work_orders');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, "creador_id");
    }
    public function carton()
    {
        return $this->belongsTo(Carton::class);
    }
    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }
    public function styleType()
    {
        return $this->belongsTo(Style::class, "style_id");
    }

    public function envase()
    {
        return $this->belongsTo(Envase::class);
    }
    public function armado()
    {
        return $this->belongsTo(Armado::class);
    }
    public function proceso()
    {
        return $this->belongsTo(Process::class, "process_id");
    }
    public function style()
    {
        return $this->belongsTo(Style::class);
    }

    public function area()
    {
        return $this->belongsTo(WorkSpace::class, 'current_area_id');
    }

    public function muestras()
    {
        return $this->hasMany(Muestra::class)->orderBy('id', 'desc');
    }
    public function muestrasPrioritarias()
    {
        return $this->hasMany(Muestra::class)->where('prioritaria', 1);
    }
    public function notificaciones()
    {
        return $this->hasMany(Notification::class)->orderBy('id', 'desc');
    }
    public function gestiones()
    {
        return $this->hasMany(Management::class)->orderBy('id', 'desc')->where('management_type_id', '!=', 7)->where('mostrar', '=', 1);
    }
    public function gestiones_report()
    {
        return $this->hasMany(Management::class)->orderBy('id', 'desc')->where('management_type_id', '!=', 7);
    }
    public function ultimoCambioEstado()
    {
        return $this->hasOne(Management::class)->where('management_type_id', 1)->latest();
    }
    public function ultimoEstado()
    {
        return $this->ultimoCambioEstado();
        // $ultimaGestion = $this->hasOne(Management::class, "work_order_id")->where('management_type_id', 1)->latest();
        // if ($ultimaGestion) {
        //     return $ultimaGestion;
        // }
        // return 1;
    }
    public function scopeUltimoEstado($query, $estados)
    {
        return $query->whereHas('ultimoCambioEstado', function ($query) use ($estados) {
            $query->whereIn('state_id', $estados);
        });
    }
    public function files()
    {
        return $this->hasManyThrough(File::class, Management::class);
    }

    public function asignaciones()
    {
        return $this->hasMany(UserWorkOrder::class, 'work_order_id');
    }

    public function vendedorAsignado()
    {
        return $this->hasOne(UserWorkOrder::class, 'work_order_id')->where('area_id', 1);
    }
    public function ingenieroAsignado()
    {
        return $this->hasOne(UserWorkOrder::class, 'work_order_id')->where('area_id', 2);
    }
    public function diseñadorAsignado()
    {
        return $this->hasOne(UserWorkOrder::class, 'work_order_id')->where('area_id', 3);
    }
    public function catalogadorAsignado()
    {
        return $this->hasOne(UserWorkOrder::class, 'work_order_id')->where('area_id', 4);
    }
    public function tecnicoMuestrasAsignado()
    {
        return $this->hasOne(UserWorkOrder::class, 'work_order_id')->where('area_id', 6);
    }

    public function detalleCotizacion()
    {
        return $this->hasMany(DetalleCotizacion::class, "work_order_id");
    }


    // Colores

    public function color_1()
    {
        return $this->belongsTo(Color::class, 'color_1_id');
    }

    public function color_2()
    {
        return $this->belongsTo(Color::class, 'color_2_id');
    }

    public function color_3()
    {
        return $this->belongsTo(Color::class, 'color_3_id');
    }

    public function color_4()
    {
        return $this->belongsTo(Color::class, 'color_4_id');
    }

    public function color_5()
    {
        return $this->belongsTo(Color::class, 'color_5_id');
    }

    public function color_6()
    {
        return $this->belongsTo(Color::class, 'color_6_id');
    }

    public function color_7()
    {
        return $this->belongsTo(Color::class, 'barniz_uv');
    }

    public function color_interno_detalle()
    {
        return $this->belongsTo(Color::class, 'color_interno');
    }

    // asignaciones
    public function getVendedorAttribute()
    {
        $ot = WorkOrder::with('users')->whereHas('users', function ($q) {
            $q->where('area_id', 1)->where('user_work_orders.active', 1);
        })->find(1);
        if (empty($ot->users)) {
            return null;
        }
        return $ot->users[0]->id;
    }

    public function getIngenieroAttribute()
    {
        $ot = WorkOrder::with('users')->whereHas('users', function ($q) {
            $q->where('area_id', 2)->where('active', 1);
        })->find(1);
        if (empty($ot->users)) {
            return null;
        }
        return $ot->users[0]->id;
    }

    public function getDiseñadorAttribute()
    {
        $ot = WorkOrder::with('users')->whereHas('users', function ($q) {
            $q->where('area_id', 3)->where('active', 1);
        })->find(1);
        if (empty($ot->users)) {
            return null;
        }
        return $ot->users[0]->id;
    }
    public function getPrecatalogadorAttribute()
    {
        $ot = WorkOrder::with('users')->whereHas('users', function ($q) {
            $q->where('area_id', 4)->where('active', 1);
        })->find(1);
        if (empty($ot->users)) {
            return null;
        }
        return $ot->users[0]->id;
    }

    public function getCatalogadorAttribute()
    {
        $ot = WorkOrder::with('users')->whereHas('users', function ($q) {
            $q->where('area_id', 5)->where('active', 1);
        })->find(1);
        if (empty($ot->users)) {
            return null;
        }
        return $ot->users[0]->id;
    }

    // DATOS OT Excel
    public function tipo_pallet()
    {
        return $this->belongsTo(PalletType::class, "pallet_type_id");
    }
    public function cajas_por_paquete()
    {
        return $this->belongsTo(PalletBoxQuantity::class, "pallet_box_quantity_id");
    }
    public function patron_pallet()
    {
        return $this->belongsTo(PalletPatron::class, "pallet_patron_id");
    }
    public function proteccion_pallet()
    {
        return $this->belongsTo(PalletProtection::class, "pallet_protection_id");
    }
    public function formato_etiqueta_pallet()
    {
        return $this->belongsTo(PalletTagFormat::class, "formato_etiqueta");
    }
    public function qa()
    {
        return $this->belongsTo(PalletQa::class, "pallet_qa_id");
    }
    public function prepicado()
    {
        return $this->belongsTo(PrecutType::class, "precut_type_id");
    }


    public function secuencia_principal()
    {
        return $this->belongsTo(SecuenciaOperacional::class, 'so_planta_original');
    }

    public function secuencia_alt1()
    {
        return $this->belongsTo(SecuenciaOperacional::class, 'so_planta_alt1');
    }

    public function secuencia_alt2()
    {
        return $this->belongsTo(SecuenciaOperacional::class, 'so_planta_alt2');
    }


    public function tipos_cintas()
    {
        return $this->belongsTo(TipoCinta::class, 'tipo_cinta');
    }

    public function matrices()
    {
        return $this->belongsTo(Matriz::class, 'matriz_id');
    }

    public function matrices_2()
    {
        return $this->belongsTo(Matriz::class, 'matriz_id_2');
    }

    public function matrices_3()
    {
        return $this->belongsTo(Matriz::class, 'matriz_id_3');
    }



    // FORMULAS
    // areaHm
    public function getAreaHMAttribute()
    {
        return ($this->largura_hm && $this->anchura_hm) ? (($this->largura_hm * $this->anchura_hm) / 1000000) : "N/A";
    }
    // areaHc
    public function getAreaHCAttribute()
    {
        // Largura HC * Anchura HC / 1000000/(Golpes al largo * Golpes al ancho)
        if (empty($this->larguraHc) || $this->larguraHc == "N/A" || empty($this->anchuraHc) || $this->anchuraHc == "N/A" || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }
        return ((($this->larguraHc * $this->anchuraHc) / 1000000) / ($this->golpes_largo * $this->golpes_ancho));
    }

    public function getAreaHcSemielaboradoAttribute()
    {
        if (empty($this->larguraHc) || $this->larguraHc == "N/A" || empty($this->anchuraHc) || $this->anchuraHc == "N/A" || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }
        return ((($this->larguraHc * $this->anchuraHc) / 1000000));
    }

    // areaEsquinero
    public function getAreaEsquineroAttribute()
    {
        return ($this->largura_hm && $this->anchura_hm) ? (($this->largura_hm * $this->anchura_hm) / 1000000) : "N/A";
    }
    // larguraHc
    public function getLarguraHCAttribute()
    {   //   (Largura HM* Golpes al largo)+ ( si; Proceso(D33) contiene Rotary y es carton simple; +20;Proceso(D33) contiene Rotary y es carton doble; +25; si no +10)+(separacion Largura HM)*((Golpes Largo-1))
        // Si falta algun campo no podemos realizar calculos asi q retornamos N/A

        $separacion_golpes_largo = empty($this->separacion_golpes_largo) ? 0 : $this->separacion_golpes_largo;



        if (empty($this->largura_hm) || empty($this->golpes_largo) || empty($this->carton_id) || empty($this->process_id)) {
            return "N/A";
        }
        $suma = 10;
        $tipoCarton =  $this->carton->tipo;
        $proceso =  $this->proceso->descripcion;
        // Power Play=Simple
        // Monotapa=Simple
        // Monotapa Doble=Doble
        // Sumatoria simple
        if (($proceso == "DIECUTTER-C/PEGADO" || $proceso == "DIECUTTER" || $proceso == "FLEXO/MATRIZ COMPLET") && ($tipoCarton == "SIMPLES" || $tipoCarton == "POWER PLY" || $tipoCarton == "MONOTAPA" || $tipoCarton == 'SIMPLE EMPLACADO')) {
           $suma = 20;
        }

        if(($proceso == "OFFSET" || $proceso == "OFFSET-C/PEGADO") && ($tipoCarton == "SIMPLES" || $tipoCarton == "POWER PLY" || $tipoCarton == "MONOTAPA" || $tipoCarton == 'SIMPLE EMPLACADO')){
            $suma = 24;
        }

        // sumatoria doble
        if (($proceso == "DIECUTTER-C/PEGADO" || $proceso == "DIECUTTER" || $proceso == "FLEXO/MATRIZ COMPLET") && ($tipoCarton == "DOBLES" || $tipoCarton == "DOBLE MONOTAPA")) {
            $suma = 25;
        }

        if(($proceso == "OFFSET" || $proceso == "OFFSET-C/PEGADO") && ($tipoCarton == "DOBLES" || $tipoCarton == "DOBLE MONOTAPA")){
            $suma = 24;
        }

        $separacionLargura = empty($this->separacion_largura_hm) ? 0 : $this->separacion_largura_hm;
        // $larguraHc = ($this->largura_hm * $this->golpes_largo) + $suma + $separacionLargura * ($this->golpes_largo - 1);

        //Nueva formula, se cambia la separacion largura por separacion golpes al largo que es un nuevo campo (si este campo es vacio su valor por defecto es 0)
        $larguraHc = ($this->largura_hm * $this->golpes_largo) + (($this->golpes_largo - 1) * $separacion_golpes_largo ) + $suma;

        //SI EL PROCESO ES CORRUGADO O PROCESS_ID = 13 larguraHC = largura_hm
        if($proceso == 'CORRUGADO'){

            $larguraHc = $this->largura_hm;
        }

        // Cuando se seleccione SIN PROCESO en el select PROCESO los siguientes items deben tener lo siguiente:
        // Largura HC= Largura HM x Golpes al Largo
        if ($proceso == "S/PROCESO") {
            if ($this->id == 12219) return   1228;

            return $this->largura_hm * $this->golpes_largo;
        }


        //Buscamos el valor especifico a retornar de la OT a solicitud del cliente
        $largura_hc=SystemVariable::where('name','LarguraHC')
                                  ->where('deleted',0)
                                  ->first();
        $largura_hc=explode(',',$largura_hc->contents);
        for($i=0;$i<count($largura_hc);$i++){
            $largura_hc_aux=explode(':',$largura_hc[$i]);

            if($largura_hc_aux[0]==$this->id){
                $larguraHc=$largura_hc_aux[1];
                $i=count($largura_hc);
            }
        }
       //dd($larguraHc);
        return $larguraHc;
    }
    // anchuraHc
    public function getAnchuraHCAttribute()
    { //   (Anchura HM* Golpes al Ancho)+ ( si Proceso(D33) contiene Rotary y es carton simple; +20; si Proceso(D33) contiene Rotary y es carton doble; +25; si no +0)+(separacion anchura HM)*((Golpes Ancho-1))
        // Si falta algun campo no podemos realizar calculos asi q retornamos N/A
        $separacion_golpes_ancho = empty($this->separacion_golpes_ancho) ? 0 : $this->separacion_golpes_ancho;

        if (empty($this->anchura_hm) || empty($this->golpes_ancho) || empty($this->carton_id) || empty($this->process_id)) {
            return "N/A";
        }
        $suma = 0;
        $tipoCarton =  $this->carton->tipo;
        $proceso =  $this->proceso->descripcion;
        // Power Play=Simple
        // Monotapa=Simple
        // Monotapa Doble=Doble
        // Sumatoria simple
        if (($proceso == "DIECUTTER-C/PEGADO" || $proceso == "DIECUTTER" || $proceso == "FLEXO/MATRIZ COMPLET") && ($tipoCarton == "SIMPLES" || $tipoCarton == "POWER PLY" || $tipoCarton == "MONOTAPA" || $tipoCarton == 'SIMPLE EMPLACADO')) {
            $suma = 20;
        }

        if(($proceso == "OFFSET" || $proceso == "OFFSET-C/PEGADO") && ($tipoCarton == "SIMPLES" || $tipoCarton == "POWER PLY" || $tipoCarton == "MONOTAPA" || $tipoCarton == 'SIMPLE EMPLACADO')){
            $suma = 24;
        }
        // sumatoria doble
        if (($proceso == "DIECUTTER-C/PEGADO" || $proceso == "DIECUTTER" || $proceso == "FLEXO/MATRIZ COMPLET") && ($tipoCarton == "DOBLES" || $tipoCarton == "DOBLE MONOTAPA")) {
            $suma = 25;
        }

        if(($proceso == "OFFSET" || $proceso == "OFFSET-C/PEGADO") && ($tipoCarton == "DOBLES" || $tipoCarton == "DOBLE MONOTAPA")){
            $suma = 24;
        }

        $separacionAnchura = empty($this->separacion_anchura_hm) ? 0 : $this->separacion_anchura_hm;
        // $anchuraHc = ($this->anchura_hm * $this->golpes_ancho) + $suma + $separacionAnchura * ($this->golpes_ancho - 1);

        //Nueva formula, se cambia la separacion largura por separacion golpes al largo que es un nuevo campo (si este campo es vacio su valor por defecto es 0)
        $anchuraHc = ($this->anchura_hm * $this->golpes_ancho) + (($this->golpes_ancho - 1) * $separacion_golpes_ancho ) + $suma;

        // Cuando se seleccione SIN PROCESO en el select PROCESO los siguientes items deben tener lo siguiente:
        // Anchura HC= Anchura HM x Golpes al Ancho

        //SI EL PROCESO ES CORRUGADO O PROCESS_ID = 13 ANCHURAHC = ANCHURA_HM
        if($proceso == 'CORRUGADO'){

            $anchuraHc = $this->anchura_hm;
        }

        if ($proceso == "S/PROCESO") {
            if ($this->id == 12219) return   890;

            return $this->anchura_hm * $this->golpes_ancho;
        }

        //Buscamos el valor especifico a retornar de la OT a solicitud del cliente
        $anchura_hc=SystemVariable::where('name','AnchuraHC')
                                  ->where('deleted',0)
                                  ->first();
        $anchura_hc=explode(',',$anchura_hc->contents);
        for($i=0;$i<count($anchura_hc);$i++){
            $anchura_hc_aux=explode(':',$anchura_hc[$i]);

            if($anchura_hc_aux[0]==$this->id){
                $anchuraHc=$anchura_hc_aux[1];
                $i=count($anchura_hc);
            }
        }

        return $anchuraHc;
        // return number_format((float) $anchuraHc, 3, '.', '');
    }
    // recorteCaracteristico
    public function getRecorteCaracteristicoAttribute()
    {
        // Cambio a recorte caracteristico
        // 7 de julio 2021  nueva formula
        // Recorte Característico=( ( Largura HM x anchura HM)- Área producto- Área Agujero)

        if (empty($this->largura_hm) || $this->largura_hm == "N/A"  || empty($this->anchura_hm) || $this->anchura_hm == "N/A"  || (empty($this->area_producto_calculo) || $this->area_producto_calculo == "N/A") || (empty($this->recorte_adicional))) {
            return "N/A";
        }
        // Cuando se seleccione SIN PROCESO en el select PROCESO los siguientes items deben tener lo siguiente:
        // Recorte Característico= 0
        if (isset($this->proceso)  && $this->proceso->descripcion == "S/PROCESO") {
            return number_format(0);
        }
        $recorteAdicional = str_replace(',', '.', str_replace('.', ' ', str_replace('.', ',', $this->recorte_adicional)));
        // dd($recorteAdicional, $this->recorte_adicional, $this->area_producto_calculo, $this->area_producto);
        return number_format(((($this->largura_hm * $this->anchura_hm) / 1000000) - $this->area_producto_calculo - $recorteAdicional), 7);
        // $recorteCaracteristico = (($this->areaHm - $this->area_producto_calculo));
        // return number_format((float) $recorteCaracteristico, 2, '.', '');
    }
    // public function getRecorteAdicionalAttribute()
    // {
    //     // PRIMERA FORMULA DEPRECADA(Área HC - Área HM) / (Golpes al largo * Golpes al ancho)
    //     // NUEVA FORMULA 18-05 Recorte Adicional=Área HC Unitario - Área HM
    //     // formula actual (Área HC - (Área HM* Gopes al largo * Golpes al ancho)) / (Golpes al largo * Golpes al ancho)
    //     if (empty($this->areaHm) || $this->areaHm == "N/A" || empty($this->areaHc) || $this->areaHc == "N/A") {
    //         return "N/A";
    //     }
    //     // Cuando se seleccione SIN PROCESO en el select PROCESO los siguientes items deben tener lo siguiente:
    //     // Recorte Adicional= 0
    //     if (isset($this->proceso)  && $this->proceso->descripcion == "S/PROCESO") {
    //         return number_format(0);
    //     }
    //     return ($this->areaHc - $this->areaHm);
    // }
    // pesoBruto
    public function getPesoBrutoAttribute()
    {
        // Área HC unitario * Gramaje /1000
        if (empty($this->areaHc) || $this->areaHc == "N/A" || empty($this->carton_id)) {
            return "N/A";
        }
        return ($this->areaHc * $this->carton->peso / 1000);
    }
    // pesoNeto
    public function getPesoNetoAttribute()
    {
        // Área producto * Gramaje /1000
        if (empty($this->area_producto_calculo) || $this->area_producto_calculo == "N/A"  || empty($this->carton_id)) {
            return "N/A";
        }
        return ($this->area_producto_calculo * $this->carton->peso / 1000);
    }

    // pesoEsquinero
    public function getPesoEsquineroAttribute()
    {
        // Área HC unitario * Gramaje /1000
        if (empty($this->largura_hm) || $this->largura_hm == "N/A" || empty($this->carton_id)) {
            return "N/A";
        }
        return ($this->largura_hm * $this->carton->peso / 1000000);
    }
    // volumenUnitario
    public function getVolumenUnitarioAttribute()
    {
        // Volumen unitario= (Largura HC*Anchura HC*Grosor Cartón/(Golpes al Largo*Golpes al Ancho))/1000
        if (empty($this->larguraHc) || $this->larguraHc == "N/A" || empty($this->anchuraHc) || $this->anchuraHc == "N/A"  || empty($this->carton_id) || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }
        return (($this->larguraHc * $this->anchuraHc  * $this->carton->espesor / ($this->golpes_largo * $this->golpes_ancho)) / 1000);
    }

    public function getVolumenUnitarioAttributeReportExcel()
    {
        // Volumen unitario= (Largura HC*Anchura HC*Grosor Cartón/(Golpes al Largo*Golpes al Ancho))/1000
        if (empty($this->larguraHc) || $this->larguraHc == "N/A" || empty($this->anchuraHc) || $this->anchuraHc == "N/A"  || empty($this->carton_id) || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }
        return (($this->larguraHc * $this->anchuraHc  * $this->carton->espesor) / 1000);
    }

    // umaArea
    public function getUmaAreaAttribute()
    {
        // Área HC unitario * 1000
        if (empty($this->areaHc) || $this->areaHc == "N/A") {
            return "N/A";
        }
        return ($this->areaHc * 1000);
    }
    // umaPeso
    public function getUmaPesoAttribute()
    {
        // Peso bruto * 1000
        if (empty($this->pesoBruto) || $this->pesoBruto == "N/A") {
            return "N/A";
        }
        return ($this->pesoBruto * 1000);
    }
    // consumo1
    public function getConsumo1Attribute()
    {
        // Área HM * % impresión * consumo tinta / 100
        if (empty($this->areaHm) || $this->areaHm == "N/A" || empty($this->impresion_1) || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
        //if (empty($this->anchura_hm) || empty($this->largura_hm) || empty($this->impresion_1) || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }

        //return ($this->areaHm * $this->impresion_1 * $this->CONSUMO_TINTA / 100);///***Formula Anterior al evolutivo 23-1*/
        return (($this->areaHm * $this->impresion_1 * $this->CONSUMO_TINTA / 100) * ($this->golpes_largo * $this->golpes_ancho));
        //return ((($this->anchura_hm/1000)*($this->largura_hm/1000)* $this->impresion_1 * ($this->CONSUMO_TINTA/100)) * ($this->golpes_largo * $this->golpes_ancho));
        //areaHm=($this->largura_hm * $this->anchura_hm) / 1000000
        //impresion_1= BD
        //CONSUMO_TINTA=5
    }
    // consumo2
    public function getConsumo2Attribute()
    {
        // Área HM * % impresión * consumo tinta / 100
        if (empty($this->areaHm) || $this->areaHm == "N/A" || empty($this->impresion_2) || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }
        return (($this->areaHm * $this->impresion_2 * $this->CONSUMO_TINTA / 100) * ($this->golpes_largo * $this->golpes_ancho));
    }
    // consumo3
    public function getConsumo3Attribute()
    {
        // Área HM * % impresión * consumo tinta / 100
        if (empty($this->areaHm) || $this->areaHm == "N/A" || empty($this->impresion_3) || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }
        return (($this->areaHm * $this->impresion_3 * $this->CONSUMO_TINTA / 100) * ($this->golpes_largo * $this->golpes_ancho));
    }
    // consumo4
    public function getConsumo4Attribute()
    {
        // Área HM * % impresión * consumo tinta / 100
        if (empty($this->areaHm) || $this->areaHm == "N/A" || empty($this->impresion_4) || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }
        return (($this->areaHm * $this->impresion_4 * $this->CONSUMO_TINTA / 100) * ($this->golpes_largo * $this->golpes_ancho));
    }
    // consumo5
    public function getConsumo5Attribute()
    {
        // Área HM * % impresión * consumo tinta / 100
        if (empty($this->areaHm) || $this->areaHm == "N/A" || empty($this->impresion_5) || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }
        return (($this->areaHm * $this->impresion_5 * $this->CONSUMO_TINTA / 100) * ($this->golpes_largo * $this->golpes_ancho));
    }
    // consumo6
    public function getConsumo6Attribute()
    {
        // Área HM * % impresión * consumo tinta / 100
        if (empty($this->areaHm) || $this->areaHm == "N/A" || empty($this->impresion_6) || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }
        return (($this->areaHm * $this->impresion_6 * $this->CONSUMO_TINTA / 100) * ($this->golpes_largo * $this->golpes_ancho));
    }
    // consumo7
    public function getConsumo7Attribute()
    {
        // Área HM * % impresión * consumo tinta / 100
        if (empty($this->areaHm) || $this->areaHm == "N/A" || empty($this->impresion_7) || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }
        return (($this->areaHm * $this->impresion_7 * $this->CONSUMO_TINTA / 100) * ($this->golpes_largo * $this->golpes_ancho));
    }
    // consumoBarnizUV
    public function getConsumoBarnizUVAttribute()
    {
        // Área HM * % impresión * consumo tinta / 100
        if (empty($this->areaHm) || $this->areaHm == "N/A" || empty($this->porcentanje_barniz_uv) || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }
        return (($this->areaHm * $this->porcentanje_barniz_uv * $this->CONSUMO_BARNIZ_UV / 100) * ($this->golpes_largo * $this->golpes_ancho));

    }
    // consumocolorInterno
    public function getConsumoColorInternoAttribute()
    {
        // Área HM * % impresión * consumo tinta / 100
        if (empty($this->areaHm) || $this->areaHm == "N/A" || empty($this->impresion_color_interno)) {
            return "N/A";
        }
        return ($this->areaHm * $this->impresion_color_interno * $this->CONSUMO_TINTA / 100);
    }
    // consumoPegado
    public function getConsumoPegadoAttribute()
    {
        // Longitud del pegado /1000 * Consumo adhesivo * Golpes al largo * Golpes al ancho
        if (empty($this->longitud_pegado) || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }
        return ($this->longitud_pegado / 1000 *  $this->CONSUMO_ADHESIVO * $this->golpes_largo * $this->golpes_ancho);
    }
    // consumoCeraInterior
    public function getConsumoCeraInteriorAttribute()
    {
        // Área HM * % cera interior * consumo cera / 100
        if (empty($this->areaHm) || $this->areaHm == "N/A" || empty($this->percentage_coverage_internal)  || $this->coverage_internal_id!=2 || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }
        return (($this->areaHm * $this->percentage_coverage_internal *  $this->CONSUMO_CERA / 100) * ($this->golpes_largo * $this->golpes_ancho));
    }
    // consumoCeraExterior
    public function getConsumoCeraExteriorAttribute()
    {
        // Área HM * % cera exterior * consumo cera / 100
        if (empty($this->areaHm) || $this->areaHm == "N/A" || empty($this->percentage_coverage_external)  || $this->coverage_external_id!=2 || empty($this->golpes_largo) || empty($this->golpes_ancho)) {
            return "N/A";
        }
        return (($this->areaHm * $this->percentage_coverage_external *  $this->CONSUMO_CERA / 100) * ($this->golpes_largo * $this->golpes_ancho));
    }
    // consumoBarniz
    public function getConsumoBarnizAttribute()
    {
        // Área HM * % barniz interior * consumo hidropelente / 100
        if (empty($this->areaHm) || $this->areaHm == "N/A" || empty($this->porcentaje_barniz_interior)) {
            return "N/A";
        }
        return ($this->areaHm * $this->porcentaje_barniz_interior *  $this->CONSUMO_HIDROPELENTE / 100);
    }

    // gramosAdhesivo
    public function getGramosAdhesivoAttribute()
    {
        // Área HM * % impresión * consumo tinta / 100
        if (empty($this->longitud_pegado) || empty($this->golpes_largo) || empty($this->golpes_ancho)){
            return "N/A";
        }
        //return (($this->longitud_pegado/1000) * ($this->CONSUMO_ADHESIVO / 100) * ($this->golpes_largo * $this->golpes_ancho));

        //Modificacion segun evolutivo 23-6 Base de Datos Clientes
        return (($this->longitud_pegado/1000) * ($this->CONSUMO_ADHESIVO ) * ($this->golpes_largo * $this->golpes_ancho));
    }

    // ACCESORS (transforma la data al consultarla de la bd)
    public function getAreaProductoAttribute($value)
    {
        return str_replace('.', ',', $value);
    }

    public function getGramajeAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getPesoAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getFctAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getEctAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getCobbInteriorAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getCobbExteriorAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getSeparacionGolpesLargoAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getSeparacionGolpesAnchoAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getIncisionRayadoLongitudinalAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getIncisionRayadoVerticalAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getInternoLargoAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getInternoAnchoAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getInternoAltoAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getExternoLargoAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getExternoAnchoAttribute($value)
    {
        return str_replace('.', ',', $value);
    }
    public function getExternoAltoAttribute($value)
    {
        return str_replace('.', ',', $value);
    }

    public function getAreaProductoCalculoAttribute()
    {
        if (empty($this->area_producto)) {
            return "N/A";
        }
        return str_replace(',', '.', str_replace('.', ' ', $this->area_producto));
    }

    // pesoBrutoSemielaborado
    public function getPesoBrutoSemielaboradoAttribute()
    {
        /*// Área HC unitario * Gramaje /1000
        if (empty($this->areaHcSemielaborado) || $this->areaHcSemielaborado == "N/A" || empty($this->carton_id)) {
            return "N/A";
        }
        return ($this->areaHcSemielaborado * $this->carton->peso / 1000);*/

        if (empty($this->larguraHc) || $this->anchuraHc == "N/A"  || empty($this->carton_id)) {
            return "";
        }

        return (($this->areaHcSemielaborado * $this->carton->peso) / 1000);
    }

    // pesoNetoSemielaborado
    public function getPesoNetoSemielaboradoAttribute()
    {
        if (empty($this->larguraHc) || $this->anchuraHc == "N/A"  || empty($this->carton_id)) {
            return "";
        }

        return (($this->areaHcSemielaborado  * $this->carton->peso) / 1000);
    }

    // volumenUnitario
    public function getVolumenUnitarioSemielaboradoAttribute()
    {

        // Volumen unitario= (Largura HC*Anchura HC*Grosor Cartón/(Golpes al Largo*Golpes al Ancho))/1000
        if (empty($this->larguraHc) || $this->larguraHc == "N/A" || empty($this->anchuraHc) || $this->anchuraHc == "N/A"  || empty($this->carton_id)) {
            return "";
        }

        return (($this->larguraHc * $this->anchuraHc * $this->carton->espesor ) / 1000);
    }

     // umaArea
     public function getUmaAreaSemielaboradoAttribute()
     {
         // Área HC unitario * 1000
         if (empty($this->areaHcSemielaborado) || $this->areaHcSemielaborado == "N/A") {
             return "N/A";
         }
         return ($this->areaHcSemielaborado * 1000);
     }
     // umaPeso
     public function getUmaPesoSemielaboradoAttribute()
     {
         // Peso bruto * 1000
         if (empty($this->pesoBrutoSemielaborado) || $this->pesoBrutoSemielaborado == "N/A") {
             return "N/A";
         }
         return ($this->pesoBrutoSemielaborado * 1000);
     }


    // public function getRecorteAdicionalAttribute($value)
    // {
    //     return str_replace('.', ',', $value);
    // }
    // public function getRecorteAdicionalCalculoAttribute()
    // {
    //     if (empty($this->recorte_adicional)) {
    //         return "N/A";
    //     }
    //     return str_replace(',', '.', str_replace('.', ' ', $this->recorte_adicional));
    // }


    //  PRESENTADOR
    public function present()
    {
        return new WorkOrderPresenter($this);
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class, "pais_id");
    }

    public function planta()
    {
        return $this->belongsTo(Planta::class, "planta_id");
    }

    public function tamano_pallet()
    {
        return $this->belongsTo(PalletType::class, "tamano_pallet_type_id");
    }

    public function fsc_detalle()
    {
        return $this->belongsTo(Fsc::class, "fsc", "codigo"); //De la tabla work_orders es el fsc , pero la relacion es con el codigo de la tabla fsc
    }

    public function reference_type_detalle()
    {
        return $this->belongsTo(ReferenceType::class, "reference_type", "codigo"); //De la tabla work_orders es el reference_type , pero la relacion es con el codigo de la tabla reference_types
    }

    public function recubrimiento_detalle()
    {
        return $this->belongsTo(RecubrimientoType::class, "recubrimiento", "codigo");
    }

    public function pallet_status()
    {
        return $this->belongsTo(PalletStatusType::class, "pallet_status_type_id");
    }

    public function protection()
    {
        return $this->belongsTo(ProtectionType::class, "protection_type_id");
    }

    public function rayado()
    {
        return $this->belongsTo(Rayado::class, "rayado_type_id");
    }

    public function characteristics()
    {
        return $this->belongsTo(AdditionalCharacteristicsType::class, "additional_characteristics_type_id");
    }

    public function maquila_detalle()
    {
        return $this->belongsTo(MaquilaServicio::class, "maquila_servicio_id");
    }

    public function design_type()
    {
        return $this->belongsTo(DesignType::class, "design_type_id");
    }

    public function coverage_internal()
    {
        return $this->belongsTo(CoverageInternal::class, "coverage_internal_id");
    }

    public function coverage_external()
    {
        return $this->belongsTo(CoverageExternal::class, "coverage_external_id");
    }

    public function impresion_detalle()
    {
        return $this->belongsTo(Impresion::class, "impresion");
    }

    public function trazabilidad_detalle()
    {
        return $this->belongsTo(Trazabilidad::class, "trazabilidad");
    }
    public function product_type_developing()
    {
        return $this->belongsTo(ProductTypeDeveloping::class, "product_type_developing_id");
    }

    public function food_type()
    {
        return $this->belongsTo(FoodType::class, "food_type_id");
    }

    public function expected_use()
    {
        return $this->belongsTo(ExpectedUse::class, "expected_use_id");
    }

    public function recycled_use()
    {
        return $this->belongsTo(RecycledUse::class, "recycled_use_id");
    }

    public function class_substance_packed()
    {
        return $this->belongsTo(ClassSubstancePacked::class, "class_substance_packed_id");
    }

    public function transportation_way()
    {
        return $this->belongsTo(TransportationWay::class, "transportation_way_id");
    }

    public function target_market()
    {
        return $this->belongsTo(TargetMarket::class, "target_market_id");
    }

    public function productTypeEstudio()
    {
        return $this->belongsTo(ProductType::class,"tipo_producto_estudio");
    }

}
