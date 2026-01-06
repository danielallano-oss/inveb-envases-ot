<?php

use App\Carton;
use App\FactoresDesarrollo;
use App\FactoresSeguridad;
use App\Rubro;
use App\Style;
use App\TipoOnda;
use PhpParser\Builder\Function_;

// Funciones que calculan resultados 

// Calcular medidas externas en base al input interno, onda y estilo seleccionados
function externo_largo($interno_largo, $estilo, $onda)
{
    $caja_entera  = caja_entera($estilo);

    $factor =  FactoresDesarrollo::where('onda_id', $onda)->where('caja_entera', $caja_entera)->first();
    return $interno_largo + $factor->externo_largo;
}

// Calcular medidas externas en base al input interno, onda y estilo seleccionados
function externo_ancho($interno_ancho, $estilo, $onda)
{
    $caja_entera  = caja_entera($estilo);

    $factor =  FactoresDesarrollo::where('onda_id', $onda)->where('caja_entera', $caja_entera)->first();
    return $interno_ancho + $factor->externo_ancho;
}

// Calcular medidas externas en base al input interno, onda y estilo seleccionados
function externo_alto($interno_alto, $estilo, $onda)
{
    $caja_entera  = caja_entera($estilo);

    $factor =  FactoresDesarrollo::where('onda_id', $onda)->where('caja_entera', $caja_entera)->first();
    return $interno_alto + $factor->externo_alto;
}


// Calcular area de hoja corrugada
function areaHC($estilo, $onda, $interno_largo, $interno_ancho, $interno_alto, $proceso, $traslape)
{
    // AREA HC UNITARIA =SI('Param. Carton & AHC (IND)'!O36="OK",('Param. Carton & AHC (IND)'!O5*'Param. Carton & AHC (IND)'!O6)/('Param. Carton & AHC (IND)'!I5*'Param. Carton & AHC (IND)'!I6)/1000000,"-")

    // 'Param. Carton & AHC (IND)'!O36 = Comprobacion
    // 'Param. Carton & AHC (IND)'!O5 = Largo HC
    // 'Param. Carton & AHC (IND)'!O6 = Ancho HC
    // 'Param. Carton & AHC (IND)'!I5 = golpes al largo
    // 'Param. Carton & AHC (IND)'!I6 = golpes al ancho

    // (largo hc* ancho hc)/(golpes_largo*golpes_ancho)/1000000

    // Calculos al largo
    $largurahm = larguraHM($estilo, $onda, $interno_largo, $interno_ancho);
    $golpes_largo = golpes_largo($largurahm, $proceso);
    $orilla_largo = orilla_largo($proceso);
    $largohc = largoHC($largurahm, $golpes_largo, $orilla_largo);

    // Calculos al ancho
    $anchurahm = anchuraHM($estilo, $onda, $interno_ancho, $interno_alto, $traslape);
    $golpes_ancho = golpes_ancho($anchurahm, $proceso);
    $orilla_ancho = orilla_ancho($proceso);
    $anchohc = anchoHC($anchurahm, $golpes_ancho, $orilla_ancho);

    // dd($largurahm, $anchurahm);
    return (($largohc * $anchohc) / ($golpes_ancho * $golpes_largo)) / 1000000;
}





// FUNCIONES LOCALES :::::::::::::::::::::::::::::::::::

// Booleano para determinar tipo de caja (Entera O NO)
function caja_entera($estilo)
{
    if (in_array($estilo, [2, 3, 4, 12])) {
        $caja_entera = 1;
    } else {
        $caja_entera = 0;
    }

    return $caja_entera;
}


// Todo
function areaHM($largurahm, $anchurahm)
{
    return ($largurahm * $anchurahm) / 1000000;
}

// Calculos al largo::::::::::::::::
function largoHC($largurahm, $golpes_largo, $orilla_largo)
{
    // (larguraHM * golpes al largo) + separacion al largo + orilla largo
    return $largurahm * $golpes_largo + $orilla_largo;
}
function larguraHM($estilo, $onda, $interno_largo, $interno_ancho)
{
    // largura hm = =SI(O('Ppal. M. INDUSTRIAL'!$C$18="BE", 'Ppal. M. INDUSTRIAL'!$C$18="CE"),('Ppal. M. INDUSTRIAL'!$C$8+'Ppal. M. INDUSTRIAL'!$C$9)*2+($C$6+$C$5)*2+35,('Ppal. M. INDUSTRIAL'!$C$8+'Ppal. M. INDUSTRIAL'!$C$9)*2+($C$6+$C$5)*2+30)
    // ["BE","CE"]
    $d1 = d1($estilo, $onda);
    $d2 = d2($estilo, $onda);

    if (in_array($onda, [2, 5])) {
        $largurahm = ($interno_largo + $interno_ancho) * 2 + ($d1 + $d2) * 2 + 35;
    } else {
        $largurahm = ($interno_largo + $interno_ancho) * 2 + ($d1 + $d2) * 2 + 30;
    }

    return $largurahm;
}

function golpes_largo($largurahm, $proceso)
{
    // Valor constante en excel 800
    $largo_minimo = 800;
    // Si el proceso es diecutter o diecutter con proceso/pegado
    if (in_array($proceso, [2, 4, 6])) {
        // Redondear al entero proximo hacia arriba
        return ceil($largo_minimo / $largurahm);
    } else {
        return 1;
    }
}

function orilla_largo($proceso)
{
    // Si el proceso es diecutter o diecutter con proceso/pegado
    if (in_array($proceso, [2, 4, 6])) {
        return 25;
    } else {
        // Si es ffg o flexo 
        return 10;
    }
}

// Calculos al Ancho 

function anchoHC($anchurahm, $golpes_ancho, $orilla_ancho)
{
    // (anchuuraHM * golpes al ancho) + separacion al ancho + orilla ancho
    return $anchurahm * $golpes_ancho + $orilla_ancho;
}

// Calculo de anchura segun estilos
function anchuraHM($estilo, $onda, $interno_ancho, $interno_alto, $traslape)
{
    $d2 = d2($estilo, $onda);
    $dh = dh($estilo, $onda);
    // Aleta para estilos 200-201
    $aleta1 = (floor($interno_ancho + $d2) / 2);
    // Aleta para estilos 202-221
    $aleta2 = floor((($interno_ancho + $d2) / 2) + ($traslape / 2));
    // Aleta para estilos 203-222
    $aleta3 = $interno_ancho;
    // Aleta para estilos 216-223
    $aleta4 = $aleta2;
    switch ($estilo) {
        case '2':
            // Estilo 201
            // F12*2+(altura int + DH)
            $anchurahc = $aleta1 * 2 + ($interno_alto + $dh);
            break;

        case '1':
            // Estilo 200
            // F12+('Ppal. M. INDUSTRIAL'!$C$10+$C$7)
            $anchurahc =  $aleta1 + ($interno_alto + $dh);
            break;

        case '3':
            // Estilo 202
            // F13*2+('Ppal. M. INDUSTRIAL'!$C$10+$C$7)
            $anchurahc = $aleta2 * 2 + ($interno_alto + $dh);
            break;
        case '14':
            // Estilo 221
            // F13+('Ppal. M. INDUSTRIAL'!$C$10+$C$7)
            $anchurahc = $aleta2 + ($interno_alto + $dh);
            break;
        case '4':
            // Estilo 203
            // F14*2+('Ppal. M. INDUSTRIAL'!$C$10+$C$7)
            $anchurahc = $aleta3 * 2 + ($interno_alto + $dh);
            break;
        case '15':
            // Estilo 222
            // F14*2+('Ppal. M. INDUSTRIAL'!$C$10+$C$7)
            $anchurahc = $aleta3  + ($interno_alto + $dh);
            break;
        case '12':
            // Estilo 216
            // F15*2+('Ppal. M. INDUSTRIAL'!$C$10+$C$7)
            $anchurahc = $aleta4 * 2  + ($interno_alto + $dh);
            break;
        case '16':
            // Estilo 223
            // F15*2+('Ppal. M. INDUSTRIAL'!$C$10+$C$7)
            $anchurahc = $aleta4  + ($interno_alto + $dh);
            break;
        default:
            # code...
            break;
    }
    return $anchurahc;
}

function golpes_ancho($anchurahm, $proceso)
{
    // Valor constante en excel 500
    $ancho_minimo = 500;
    // Si el proceso es diecutter o diecutter con proceso/pegado
    if (in_array($proceso, [2, 4, 6])) {
        // Redondear al entero proximo hacia arriba
        return ceil($ancho_minimo / $anchurahm);
    } else {
        return 1;
    }
}

function orilla_ancho($proceso)
{
    // Si el proceso es diecutter o diecutter con proceso/pegado
    if (in_array($proceso, [2, 4, 6])) {
        return 25;
    } else {
        // Si es ffg o flexo 
        return 0;
    }
}

// auxiliares
function d1($estilo, $onda)
{
    $caja_entera  = caja_entera($estilo);
    $factor =  FactoresDesarrollo::where('onda_id', $onda)->where('caja_entera', $caja_entera)->first();
    return $factor->d1;
}

function d2($estilo, $onda)
{
    $caja_entera  = caja_entera($estilo);
    $factor =  FactoresDesarrollo::where('onda_id', $onda)->where('caja_entera', $caja_entera)->first();
    return $factor->d2;
}

function dh($estilo, $onda)
{
    $caja_entera  = caja_entera($estilo);
    $factor =  FactoresDesarrollo::where('onda_id', $onda)->where('caja_entera', $caja_entera)->first();
    return $factor->dh;
}

// ------------------------------------------------------------------------------------------------------------------
// :::::::::::::::::::::::::::::::::::::::::::
// :::::::::::::::::::::::::::::::::::::::::::
// CALCULOS PARA ESCOJER EL CARTON
// :::::::::::::::::::::::::::::::::::::::::::
// :::::::::::::::::::::::::::::::::::::::::::
function rmt($rmt, $rubro_id, $style_id, $onda_id, $envase_id, $interno_largo, $interno_ancho, $interno_alto,  $traslape, $product_type_id, $filas_columnares_por_pallet, $contenido_cajas, $cajas_apiladas_por_pallet, $pallets_apilados)
{
    //     RMT =SI(O('Ppal. M. INDUSTRIAL'!C20="Vinos",ESNUMERO('Ppal. M. INDUSTRIAL'!C28),'Param. Carton & AHC (IND)'!O36="NO"),"-",MAX('Param. Carton & AHC (IND)'!O27:O28))

    // c20 = rubro
    // if(rubro = vinos || rmt lb requerido || comprobacion = NO){
    // 	"-"
    // }else {
    // 	MAX('Param. Carton & AHC (IND)'!O27:O28) => max entre trabado y columnar de rmt
    // }

    // 18 = rubro de vinos
    if ($rubro_id == 18 || isset($rmt)) {
        return "-";
    }

    $peso_caja_estimado = peso_caja_estimado($rubro_id, $style_id, $onda_id, $interno_largo, $interno_ancho, $interno_alto,  $traslape);
    $factor_seguridad = factor_seguridad($rubro_id, $envase_id);
    $factor_ect = factor_ect($product_type_id);
    $externo_largo = externo_largo($interno_largo, $style_id, $onda_id);
    $externo_ancho = externo_ancho($interno_ancho, $style_id, $onda_id);

    $rmt_trabado = rmt_trabado($filas_columnares_por_pallet, $factor_ect, $factor_seguridad, $peso_caja_estimado, $contenido_cajas, $cajas_apiladas_por_pallet, $pallets_apilados, $externo_largo, $externo_ancho);
    $rmt_columnar = rmt_columnar($factor_seguridad, $peso_caja_estimado, $contenido_cajas, $cajas_apiladas_por_pallet, $pallets_apilados, $externo_largo, $externo_ancho);

    // el rmt es el valor mas alto entre rmt trabado y columnar
    $rmt = max($rmt_trabado, $rmt_columnar);
    return $rmt;
}


// Functiones Locales de calculo carton
function peso_caja_estimado($rubro_id, $style_id, $onda_id, $interno_largo, $interno_ancho, $interno_alto,  $traslape)
{


    $rubro = Rubro::find($rubro_id);
    $estilo = Style::find($style_id);

    $gramaje = $rubro->gramaje;
    $factor_peso = $estilo->factor_peso;

    $largurahm = larguraHM($style_id, $onda_id, $interno_largo, $interno_ancho);
    $anchurahm = anchuraHM($style_id, $onda_id, $interno_ancho, $interno_alto, $traslape);
    $areahm = areaHM($largurahm, $anchurahm);
    return $gramaje * $factor_peso * $areahm;
}

function factor_seguridad($rubro_id, $envase_id)
{
    //     FactorSeguridad (M . Industrial) =SI('Ppal. M. INDUSTRIAL'!C20="Deshidratados Gral.",4,SI('Ppal. M. INDUSTRIAL'!C20="Deshidratados Asia",5,INDICE('Factores Seguridad'!B3:H6,COINCIDIR('Ppal. M. INDUSTRIAL'!C20,'Factores Seguridad'!B3:B6,0),COINCIDIR('Ppal. M. INDUSTRIAL'!C21,'Factores Seguridad'!B3:H3,0))))

    // 'Ppal. M. INDUSTRIAL'!C20 = rubro
    // if(rubro = Deshidratados Gral){
    // 	fs = 4
    // }elseif(rubro = Deshidratados asia){
    // fs = 5
    // }else{
    // INDICE('Factores Seguridad'!B3:H6,COINCIDIR('Ppal. M. INDUSTRIAL'!C20,'Factores Seguridad'!B3:B6,0),COINCIDIR('Ppal. M. INDUSTRIAL'!C21,'Factores Seguridad'!B3:H3,0))
    // }

    // Si no es desidratados se busca en tabla de factores de seguridad segun el rubro y el tipo de envase

    // 19 = deshidratados
    if ($rubro_id == 19) {
        return 4; //factor constante para deshidratados gral
    } else {
        $factor_seguridad = FactoresSeguridad::where('rubro_id', $rubro_id)->where('envase_id', $envase_id)->first();
        return $factor_seguridad->factor_seguridad;
    }
}

function tipo_adhesivo($rubro_id)
{
    //     Tipo adhesivo =SI(O('Ppal. M. INDUSTRIAL'!$C$20=B26, 'Ppal. M. INDUSTRIAL'!$C$20=B27),"HIDRORESISTENTE", "CORRIENTE")
    // si el rubor es desidratado = hidroresistente de lo contrario es corriente
    // 19 = deshidratados
    if ($rubro_id == 19) {
        return "HIDRORESISTENTE";
    } else {

        return "CORRIENTE";
    }
}

function espesor($onda_id)
{
    //     Espesor =INDICE('Gramajes (rubros)'!B25:E32,COINCIDIR('Ppal. M. INDUSTRIAL'!$C$18,'Gramajes (rubros)'!B25:B32,0),2)
    // c18 = tipo de onda
    // Segun el tipo de onda se utiliza una constante de promedio de espesor
    $onda = TipoOnda::find($onda_id);
    return $onda->espesor_promedio;
}


function rmt_trabado($filas_columnares_por_pallet, $factor_ect, $factor_seguridad, $peso_caja_estimado, $contenido_cajas, $cajas_apiladas_por_pallet, $pallets_apilados, $externo_largo, $externo_ancho)
{
    //     RMT TRABADO =SI('Ppal. M. INDUSTRIAL'!C25=0, F17*O17*2.2*1/0.55*((O16+'Ppal. M. INDUSTRIAL'!C22)*('Ppal. M. INDUSTRIAL'!C24-'Ppal. M. INDUSTRIAL'!C25-1)*'Ppal. M. INDUSTRIAL'!C23+'Conversiones y palets'!M10*(('Param. Carton & AHC (IND)'!O12*'Param. Carton & AHC (IND)'!O13)/('Conversiones y palets'!K10))*('Ppal. M. INDUSTRIAL'!C23-1)),F17*O17*2.2*1/0.55*((O16+'Ppal. M. INDUSTRIAL'!C22)*('Ppal. M. INDUSTRIAL'!C24-'Ppal. M. INDUSTRIAL'!C25)*'Ppal. M. INDUSTRIAL'!C23+'Conversiones y palets'!M10*(('Param. Carton & AHC (IND)'!O12*'Param. Carton & AHC (IND)'!O13)/('Conversiones y palets'!K10))*('Ppal. M. INDUSTRIAL'!C23-1)))

    // C25 = N filas columnares por pallet
    // F17 = factor ECT
    // O17 = factor seguridad
    // O16 = Peso de caja estimado
    // C22 = Contenido cajas
    // C23 = N palets apilados
    // c24 = N cajas apiladas x pallet
    // Conversiones y palets'!M10 = constante peso pallet kg x CHEP = 24.22
    // O12 = Largo Exterior
    // O13 = Ancho exterior
    // k10 = constante area mm2 x CHEP = 1200000

    // SI('Ppal. M. INDUSTRIAL'!C25=0, ,

    // Si es cero asignamos 1 como lo indica la formula 
    if ($filas_columnares_por_pallet == 0) {
        $filas_columnares_por_pallet = 1;
    }
    $rmt_trabado = $factor_ect * $factor_seguridad * 2.2 * 1 / 0.55 * (($peso_caja_estimado + $contenido_cajas) * ($cajas_apiladas_por_pallet - $filas_columnares_por_pallet) * $pallets_apilados + 24.22 * (($externo_largo * $externo_ancho) / (1200000)) * ($pallets_apilados - 1));
    return $rmt_trabado;
}


function rmt_columnar($factor_seguridad, $peso_caja_estimado, $contenido_cajas, $cajas_apiladas_por_pallet, $pallets_apilados, $externo_largo, $externo_ancho)
{
    //     RMT COLUMNAR =O17*2.2*((O16+'Ppal. M. INDUSTRIAL'!C22)*('Ppal. M. INDUSTRIAL'!C24-1)*('Ppal. M. INDUSTRIAL'!C23)+'Conversiones y palets'!M10*(('Param. Carton & AHC (IND)'!$O$12*'Param. Carton & AHC (IND)'!$O$13)/('Conversiones y palets'!K10))*('Ppal. M. INDUSTRIAL'!C23-1))

    // O17 = factor seguridad
    // O16 = Peso de caja estimado
    // C22 = Contenido cajas
    // C23 = N palets apilados
    // c24 = N cajas apiladas x pallet
    // Conversiones y palets'!M10 = constante peso pallet kg x CHEP
    // O12 = Largo ExteriorO13 = Ancho exterior
    // k10 = constante area mm2 x CHEP
    // O13 = Ancho exterior
    $rmt_columnar = $factor_seguridad * 2.2 * (($peso_caja_estimado + $contenido_cajas) * ($cajas_apiladas_por_pallet - 1) * ($pallets_apilados) + 24.22 * (($externo_largo * $externo_ancho) / (1200000)) * ($pallets_apilados - 1));
    return $rmt_columnar;
}


function factor_ect($product_type_id)
{
    if ($product_type_id == 5) {
        // TAPA
        return (48 / (48 + 70));
    } elseif ($product_type_id == 4) {
        // FONDO
        return (70 / (48 + 70));
    } else {
        // CAJAS / UNA PIEZA
        return 1;
    }
}
function ect_min($rmt_ingresado, $prepicado_ventilacion, $rmt_calculado, $onda_id, $interno_largo, $interno_ancho)
{


    // ECTmin =SI(O('Ppal. M. INDUSTRIAL'!C17="Sí",'Ppal. M. INDUSTRIAL'!C20="Vinos",'Param. Carton & AHC (IND)'!O36="NO"), "-",SI(O('Ppal. M. INDUSTRIAL'!C28="No",'Ppal. M. INDUSTRIAL'!C28=""),(O29/(6.6*('Param. Carton & AHC (IND)'!O19*'Conversiones y palets'!D6)^0.4*(2*('Ppal. M. INDUSTRIAL'!C8*'Conversiones y palets'!D6+'Ppal. M. INDUSTRIAL'!C9*'Conversiones y palets'!D6))^0.3))^0.91, ('Ppal. M. INDUSTRIAL'!C28/(6.6*('Param. Carton & AHC (IND)'!O19*'Conversiones y palets'!D6)^0.4*(2*('Ppal. M. INDUSTRIAL'!C8*'Conversiones y palets'!D6+'Ppal. M. INDUSTRIAL'!C9*'Conversiones y palets'!D6))^0.3))^0.91))
    // c17 = prepicado ventilaciones
    // c28 = rmtlb
    // O29 = rmt calculado
    // O19 = espesor
    // D6 = constante conversion de mm a in
    // C8 = largo interior


    // if(rubro = vinos || comprobacion = NO){
    // 	"-"
    // }else{
    // 	if(rmtlb = 0 | ""){
    // (O29/(6.6*('Param. Carton & AHC (IND)'!O19*'Conversiones y palets'!D6)^0.4*(2*('Ppal. M. INDUSTRIAL'!C8*'Conversiones y palets'!D6+'Ppal. M. INDUSTRIAL'!C9*'Conversiones y palets'!D6))^0.3))^0.91
    // 	}
    // 	else{
    // ('Ppal. M. INDUSTRIAL'!C28/(6.6*('Param. Carton & AHC (IND)'!O19*'Conversiones y palets'!D6)^0.4*(2*('Ppal. M. INDUSTRIAL'!C8*'Conversiones y palets'!D6+'Ppal. M. INDUSTRIAL'!C9*'Conversiones y palets'!D6))^0.3))^0.91)
    // 	}
    // }

    $espesor = espesor($onda_id);
    $factor_conversion_mm_to_in = 0.0393701;
    // Si prepicado es SI
    if ($prepicado_ventilacion == 1) {
        $ect_min = "-";
    } elseif ($rmt_ingresado == null) {

        $ect_min = pow(($rmt_calculado / (6.6 * pow(($espesor * $factor_conversion_mm_to_in), 0.4) * pow((2 * ($interno_largo * $factor_conversion_mm_to_in + $interno_ancho * $factor_conversion_mm_to_in)), 0.3))), 0.91);
    } else {
        $ect_min = pow(($rmt_ingresado / (6.6 * pow(($espesor * $factor_conversion_mm_to_in), 0.4) * pow((2 * ($interno_largo * $factor_conversion_mm_to_in + $interno_ancho * $factor_conversion_mm_to_in)), 0.3))), 0.91);
    }
    // var_dump($rmt_calculado, pow(($espesor * $factor_conversion_mm_to_in), 0.4), pow((4.2 * 0.0393701), 0.4));
    return $ect_min;
}

// CALCULO DE CARTON SELECCIONADO
function calcular_carton($rubro_id, $ect_min, $onda_id, $carton_color)
{
    // Si hay prepicado_ventilacion el ect no se puede calcular por lo tanto no se puede seleccionar un carton valido
    if ($ect_min == "-") {
        return $ect_min;
    }

    $tipo_adhesivo = tipo_adhesivo($rubro_id);

    $cartones = Carton::where("active", 1)->get();
    // mayor ect_min en la base de datos
    $ect_max = Carton::max("ect_min");

    // para crear requerimiento necesitamos la onda no sirve solo id
    $onda = TipoOnda::find($onda_id);
    $onda = $onda->onda;
    $carton_seleccionado = false;
    if ($tipo_adhesivo == "HIDRORESISTENTE") {
        // If (curReq = Req And curECT >= ECT_Min) Then

        //                     x = x + 1
        //                     ECT_aux = curECT

        //                     If (ECT_aux < ECT) Then

        //                         ECT = ECT_aux
        //                         CART = curCart

        //                     End If

        //                 End If
        foreach ($cartones as $carton) {
            // La onda se conforma de la union de onda 1 + onda 2 si la hubiera
            // cuando es powerplay la onda 2 no se considera
            if ($carton->tipo == "POWERPLAY") {
                $onda_actual = $carton->onda_1;
            } else {
                if ($carton->onda_2 != 0) {

                    $onda_actual = $carton->onda_1 . $carton->onda_2;
                } else {

                    $onda_actual = $carton->onda_1;
                }
            }
            // $onda_actual = str_replace("Onda ", "", $carton->onda_1);
            // dd($onda_actual);
            // Si el color onda y adhesivo coinciden y el ect del carton es mayor o igual al ectmin calculado
            if ($carton->color_tapa_exterior == [1 => "CAFE", 2 => "BLANCO"][$carton_color] && $onda_actual == $onda && $carton->recubrimiento == $tipo_adhesivo && $carton->ect_min >= $ect_min) {
                if ($carton->ect_min < $ect_max) {
                    $ect_max = $carton->ect_min;
                    $carton_seleccionado = $carton;
                }
                // ECT_aux = curECT

                //                     If (ECT_aux < ECT) Then

                //                         ECT = ECT_aux
                //                         CART = curCart

                //                     End If
            }
        }
        return $carton_seleccionado;
    } elseif ($tipo_adhesivo == "CORRIENTE") {
        foreach ($cartones as $carton) {
            if ($carton->tipo == "POWERPLAY") {
                $onda_actual = $carton->onda_1;
            } else {
                if ($carton->onda_2 != 0) {

                    $onda_actual = $carton->onda_1 . $carton->onda_2;
                } else {

                    $onda_actual = $carton->onda_1;
                }
            }
            // $onda_actual = str_replace("Onda ", "", $carton->onda_1);
            // dd($onda_actual);
            // dd($carton->color, [1 => "CAFE", 2 => "BLANCO"][$carton_color], $carton_color);
            // Si el color onda y adhesivo coinciden y el ect del carton es mayor o igual al ectmin calculado
            if ($carton->color_tapa_exterior == [1 => "CAFE", 2 => "BLANCO"][$carton_color] && $onda_actual == $onda && $carton->ect_min >= $ect_min) {
                if ($carton->ect_min < $ect_max) {
                    $ect_max = $carton->ect_min;
                    $carton_seleccionado = $carton;
                }
            }
        }
        return $carton_seleccionado;
    }

    return "-";
    // Como se calcula el carton seleccionado
    // Si es corriente se calcula comparando ONDA_COLOR ej. C_BLANCO
    // si es hidroresistente se calcula comparando  ONDA_COLOR_ADHESIVO C_BLANCO_HIDRORESISTENTE

    // req = requerimiento

    //     Dim ECT As Double
    //         Rgect = Worksheets("Listado cartones").Range("D2:D88")
    //         ECT = Application.WorksheetFunction.Max(Rgect)
    //         ECT_Min = Worksheets("Ppal. M. INDUSTRIAL").Range("G13").Value

    //         Dim CART As String
    //         Dim Req As String

    //         Dim x As Integer
    //         Dim y As Integer
    //         Dim i As Integer
    //         Dim ECT_aux As Double


    //     'Si el adhesivo es Hidrorresistente, req debe incluir el tipo de adhesivo

    //         If Worksheets("Param. Carton & AHC (IND)").Range("O18") = "HIDRORESISTENTE" Then

    //             Req = Worksheets("Param. Carton & AHC (IND)").Range("O23").Value

    //             i = 2
    //             While Worksheets("Listado cartones").Cells(i, 2) <> ""

    //                 Set curReq = Worksheets("Listado cartones").Cells(i, 2)
    //                 Set curCart = Worksheets("Listado cartones").Cells(i, 3)
    //                 Set curECT = Worksheets("Listado cartones").Cells(i, 4)

    //                 If (curReq = Req And curECT >= ECT_Min) Then

    //                     x = x + 1
    //                     ECT_aux = curECT

    //                     If (ECT_aux < ECT) Then

    //                         ECT = ECT_aux
    //                         CART = curCart

    //                     End If

    //                 End If

    //                 i = i + 1
    //             Wend

    //             If (x <> 0) Then

    //                 Worksheets("Ppal. M. INDUSTRIAL").Range("G19") = CART
    //                 Worksheets("Ppal. M. INDUSTRIAL").Range("G20") = ECT

    //             Else
    //                 MsgBox "No hay cartón que cumpla requerimiento y que cumpla ECT min"

    //             End If


    //     'Si el adhesivo es coriente, req no incluye tipo adhesivo
    //         Else
    //             Req = Worksheets("Param. Carton & AHC (IND)").Range("N23").Value

    //             i = 2
    //             While Worksheets("Listado cartones").Cells(i, 1) <> ""

    //                 Set curReq = Worksheets("Listado cartones").Cells(i, 1)
    //                 Set curCart = Worksheets("Listado cartones").Cells(i, 3)
    //                 Set curECT = Worksheets("Listado cartones").Cells(i, 4)

    //                 If (curReq = Req And curECT >= ECT_Min) Then

    //                     x = x + 1
    //                     ECT_aux = curECT

    //                     If (ECT_aux < ECT) Then

    //                         ECT = ECT_aux
    //                         CART = curCart

    //                     End If

    //                 End If

    //                 i = i + 1
    //             Wend

    //             If (x <> 0) Then

    //                 Worksheets("Ppal. M. INDUSTRIAL").Range("G19") = CART
    //                 Worksheets("Ppal. M. INDUSTRIAL").Range("G20") = ECT

    //             Else
    //                 MsgBox "No hay cartón que cumpla requerimiento y que cumpla ECT min"

    //             End If


}
