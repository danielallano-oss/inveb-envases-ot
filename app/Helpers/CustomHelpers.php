<?php

/* ********************  FUNCIONES INTERNAS: *************************** */

use App\Changelog;
use App\Tarifario;
use App\MargenMinimo;
use App\Rubro;
use App\Hierarchy;
use App\Cotizacion;
use App\Client;
use App\SystemVariable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

function armar_text_select($element, $texts, $separador)
{
    $cant = count($texts);
    $text = '';
    for ($i = 0; $i < $cant; $i++) {
        $propiedad = $texts[$i];
        if ($i == 0) $text .= $element->$propiedad;
        else $text .= ' ' . $separador . ' ' . $element->$propiedad;
    }
    return $text;
}

/* ---------------------- COLUMNAS DE LAS TABLAS: ---------------------------- */

//armar links de orden ASC y DESC de las columnas de las tablas
function order_column($column_name, $field_data, $sorted)
{
    $down_up  = 'keyboard_arrow_down';
    if (!is_null(request('orderby'))) {
        if (request('orderby') == $field_data) {

            if (!is_null(request('sorted'))) {
                $sorted   = (request('sorted') == 'DESC') ? 'ASC' : 'DESC';
                $down_up  = (request('sorted') == 'ASC') ? 'keyboard_arrow_down' : 'keyboard_arrow_up';
            } else {
                $sorted   = 'ASC';
                $down_up  = 'keyboard_arrow_down';
            }
        }
    }

    return $column_name . "<a href=" . Request::fullUrlWithQuery(['orderby' => $field_data, 'sorted' => $sorted]) . "><div class='material-icons md-14'>" . $down_up . "</div></a>";
}

/* ---------------------- COMPONENTES DE LOS FORMS FILTROS: ---------------------------- */

//armar cualquier filtro Multiple de una vista con un objeto:
function optionsSelectObjetfilterMultiple($datos_filter, $value, $texts, $separador = '')
{
    $options_select = '';
    foreach ($datos_filter as $element) {

        if (!empty(request()->query($value))) {
            $selected = '';
            if (in_array($element->$value, request()->query($value))) {
                $selected = 'selected="selected"';
            }
            $options_select .= '<option value="' . $element->$value . '" ' . $selected . '> ' . armar_text_select($element, $texts, $separador) . '</option>';
        } else {
            $options_select .= '<option value="' . $element->$value . '" selected="selected"> ' . armar_text_select($element, $texts, $separador) . '</option>';
        }
    } //fin foreaach
    //var_dump(request()->query()); die();
    return $options_select;
}

function optionsSelectObjetfilterMultipleNew($datos_filter, $value, $texts, $separador = '')
{
    $options_select = '';
    foreach ($datos_filter as $element) {

        if (!empty(request()->query($value))) {
            $selected = '';
            if (in_array($element->$value, request()->query($value))) {
                $selected = 'selected="selected"';
            }
            $options_select .= '<option value="' . $element->$value . '" ' . $selected . '> ' . armar_text_select($element, $texts, $separador) . '</option>';
        } else {
            $options_select .= '<option value="' . $element->$value . '" > ' . armar_text_select($element, $texts, $separador) . '</option>';
        }
    } //fin foreaach
    //var_dump(request()->query()); die();
    return $options_select;
}

//armar cualquier filtro Multiple de una vista con un Array asociativo:
function optionsSelectArrayfilterMultiple($datos_filter, $value)
{
    $options_select = '';
    foreach ($datos_filter as $key => $valor) {
        if (!empty(request()->query($value))) {
            $selected = '';
            if (in_array($key, request()->query($value))) {
                $selected = 'selected="selected"';
            }
            $options_select .= '<option value="' . $key . '" ' . $selected . '> ' . $valor . '</option>';
        } else {
            $options_select .= '<option value="' . $key . '" selected="selected"> ' . $valor . '</option>';
        }
    } //fin foreaach

    return $options_select;
}

function optionsSelectArrayfilterMultipleSinSeleccion($datos_filter, $value)
{
    $options_select = '';
    $query_values = request()->query($value, []); // devuelve array o []

    foreach ($datos_filter as $key => $valor) {
        $selected = in_array($key, $query_values) ? 'selected="selected"' : '';
        $options_select .= '<option value="' . $key . '" ' . $selected . '>' . $valor . '</option>';
    }

    return $options_select;
}

function optionsSelectArrayfilterMultipleNew($datos_filter, $value)
{
    $options_select = '';
    foreach ($datos_filter as $key => $valor) {
        if (!empty(request()->query($value))) {
            $selected = '';
            if (in_array($key, request()->query($value))) {
                $selected = 'selected="selected"';
            }
            $options_select .= '<option value="' . $key . '" ' . $selected . '> ' . $valor . '</option>';
        } else {
            $options_select .= '<option value="' . $key . '" > ' . $valor . '</option>';
        }
    } //fin foreaach

    return $options_select;
}

//armar cualquier filtro Simple de una vista con un objeto:
function optionsSelectObjetfilterSimple($datos_filter, $value, $texts, $separador = '', $all_opc = false)
{
    $options_select = '';
    if ($all_opc)
        $options_select = '<option value="all" selected>Todos</option>';

    foreach ($datos_filter as $element) {
        if (!empty(request()->query($value))) {
            $selected = '';
            if ($element->$value == request()->query($value)) {
                $selected = 'selected="selected"';
            }
            $options_select .= '<option value="' . $element->$value . '" ' . $selected . '> ' . armar_text_select($element, $texts, $separador) . '</option>';
        } else {
            $options_select .= '<option value="' . $element->$value . '"> ' . armar_text_select($element, $texts, $separador) . '</option>';
        }
    } //fin foreaach

    return $options_select;
}

//armar cualquier filtro Simple de una vista con un Array asociativo:
function optionsSelectArrayfilterSimple($datos_filter, $value, $all_opc = false)
{
    $options_select = '';
    if ($all_opc)
        $options_select = '<option value="all" selected>Todos</option>';
    foreach ($datos_filter as $key => $valor) {
        if (!empty(request()->query($value))) {
            $selected = '';
            if ($key == request()->query($value)) {
                $selected = 'selected="selected"';
            }
            $options_select .= '<option value="' . $key . '" ' . $selected . '> ' . $valor . '</option>';
        } else {
            $options_select .= '<option value="' . $key . '"> ' . $valor . '</option>';
        }
    } //fin foreaach

    return $options_select;
}

// function optionsSelectArrayfilterSimpleSelected($datos_filter, $value, $all_opc = false)
// {
//     $options_select = '';
//     if ($all_opc)
//         $options_select = '<option value="all" selected>Todos</option>';
//     if (count($datos_filter) == 1) {

//         foreach ($datos_filter as $key => $valor) {
//             $selected = 'selected="selected"';
//             // var_dump($key);
//             // if (!empty(request()->query($value))) {
//             //     $selected = '';
//             //     if ($key == request()->query($value)) {
//             //         $selected = 'selected="selected"';
//             //     }
//             $options_select .= '<option value="' . $key . '" ' . $selected . '> ' . $valor . '</option>';
//             // } else {
//             // /    $options_select .= '<option value="' . $key . '"> ' . $valor . '</option>';
//             // }
//         }
//     } else {

//         foreach ($datos_filter as $key => $valor) {
//             if (!empty(request()->query($value))) {
//                 $selected = '';
//                 if ($key == request()->query($value)) {
//                     $selected = 'selected="selected"';
//                 }
//                 $options_select .= '<option value="' . $key . '" ' . $selected . '> ' . $valor . '</option>';
//             } else {
//                 $options_select .= '<option value="' . $key . '"> ' . $valor . '</option>';
//             }
//         }
//     }
//     //fin foreaach

//     return $options_select;
// }

function optionsSelectArrayfilterSimpleSelected($datos_filter, $value, $all_opc = false)
{
    $options_select = '';

    if ($all_opc) {
        $options_select .= '<option value="all" selected>Todos</option>';
    }

    $selected_value = request()->query($value);

    foreach ($datos_filter as $key => $valor) {
        $selected = '';

        // Si hay solo una opción, márcala como seleccionada automáticamente
        if (count($datos_filter) == 1 || (!empty($selected_value) && $key == $selected_value)) {
            $selected = 'selected="selected"';
        }

        $options_select .= '<option value="' . $key . '" ' . $selected . '>' . $valor . '</option>';
    }

    return $options_select;
}

//armar cualquier filtro Simple de una vista con un Array asociativo:
function optionsSelectArrayfilterSimplePlanta($datos_filter, $value, $all_opc = false, $valor_select = '')
{
    $options_select = '';
    if ($all_opc)
        $options_select = '<option value="all" selected>Todos</option>';
    foreach ($datos_filter as $key => $valor) {
        if (!empty(request()->query($value))) {
            $selected = '';
            if ($key == request()->query($value)) {
                $selected = 'selected="selected"';
            } elseif ($key == $valor_select) {
                $selected = 'selected="selected"';
            }
            $options_select .= '<option value="' . $key . '" ' . $selected . '> ' . $valor . '</option>';
        } else {
            $options_select .= '<option value="' . $key . '"> ' . $valor . '</option>';
        }
    } //fin foreaach

    return $options_select;
}


/* ---------------------- COMPONENTES DE LOS FORMS CREATE/EDIT: ---------------------------- */

function armarInputCreateEdit($formato, $key, $title, $type, $errors, $objeto, $class_input, $required, $placeholder)
{
    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = ($errors->has($key)) ? '<span class="bottom-description text-danger"><strong>* ' . $errors->first($key) . '</strong></span>' : '';
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);

    if ($type == 'readonly')
        return '
          <div class="col">
            <div class="form-group ">
              <div class="' . $formato . '">
                <div class="form-group">
                  <label class="col col-form-label">' . $title . '</label>
                  <span>' . $value . '</span>
                </div>
              </div>
            </div>
          </div>';
    else
        return '
          <div class="col">
            <div class="form-group ">
              <div class="' . $formato . '">
                <div class="form-group' . $class_error . '">
                  <label class="col col-form-label">' . $title . '</label>
                  <input type="' . $type . '" id="' . $key . '" name="' . $key . '" value="' . $value . '" class="' . $class_input . '" ' . $required . ' placeholder="' . $placeholder . '">
                  ' . $span_error . '
                </div>
              </div>
            </div>
          </div>';
}


function armarInputCreateEditCustomCol($formato, $key, $title, $type, $errors, $objeto, $class_input, $required, $placeholder, $col, $default_value)
{
    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = ($errors->has($key)) ? '<span class="bottom-description text-danger"><strong>* ' . $errors->first($key) . '</strong></span>' : '';
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);

    if ($type == 'readonly')
        return '
          <div class="col' . $col . '">
            <div class="form-group ">
              <div class="' . $formato . '">
                <div class="form-group">
                  <label class="col col-form-label">' . $title . '</label>
                  <span>' . $value . '</span>
                </div>
              </div>
            </div>
          </div>';
    else
        return '
          <div class="col' . $col . '">
            <div class="form-group ">
              <div class="' . $formato . '">
                <div class="form-group' . $class_error . '">
                  <label class="col col-form-label">' . $title . '</label>
                  <input type="' . $type . '" id="' . $key . '" name="' . $key . '" value="' . $value . '" class="' . $class_input . '" ' . $required . ' placeholder="' . $placeholder . '">
                  ' . $span_error . '
                </div>
              </div>
            </div>
          </div>';
}

function armarInputCreateEditCustomColValue($formato, $key, $title, $type, $errors, $objeto, $class_input, $required, $placeholder, $col, $default_value)
{
    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = ($errors->has($key)) ? '<span class="bottom-description text-danger"><strong>* ' . $errors->first($key) . '</strong></span>' : '';
    $value = old($key, isset($objeto->$key) ? $objeto->$key : $default_value);

    if ($type == 'readonly')
        return '
          <div class="col' . $col . '">
            <div class="form-group ">
              <div class="' . $formato . '">
                <div class="form-group">
                  <label class="col col-form-label">' . $title . '</label>
                  <span>' . $value . '</span>
                </div>
              </div>
            </div>
          </div>';
    else
        return '
          <div class="col' . $col . '">
            <div class="form-group ">
              <div class="' . $formato . '">
                <div class="form-group' . $class_error . '">
                  <label class="col col-form-label">' . $title . '</label>
                  <input type="' . $type . '" id="' . $key . '" name="' . $key . '" value="' . $value . '" class="' . $class_input . '" ' . $required . ' placeholder="' . $placeholder . '">
                  ' . $span_error . '
                </div>
              </div>
            </div>
          </div>';
}


//armar cualquier filtro de un formulario con un objeto:
function armarSelectObjectCreateEdit($options, $val, $texts, $separador, $formato, $key, $title, $errors, $objeto, $class_select)
{
    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = ($errors->has($key)) ? '<span class="bottom-description text-danger"><strong>* ' . $errors->first($key) . '</strong></span>' : '';
    $value = isset($objeto->$key) ? $objeto->$key : null;

    $optionsAux = '';
    foreach ($options as $element) {
        $selected = '';
        if ($element->$val == $value) {
            $selected = 'selected="selected"';
        }
        $optionsAux .= '<option value="' . $element->$val . '" ' . $selected . '> ' . armar_text_select($element, $texts, $separador) . '</option>';
    } //fin foreaach

    return '<div class="' . $formato . '">
            <div class="form-group' . $class_error . '">
              <label>' . $title . '</label>
              <select id="' . $key . '" name="' . $key . '" class="' . $class_select . '">
                 ' . $optionsAux . '
              </select>
              ' . $span_error . '
            </div>
          </div>';
}

//armar cualquier filtro de un formulario con un Array asociativo:
function armarSelectArrayCreateEdit($options, $formato, $key, $title, $errors, $objeto, $class_select)
{
    if ($options == 'readonly')
        return '<div class="col">
              <div class="form-group ">
                <div class="' . $formato . '">
                  <div class="form-group completed">
                    <label class="col col-form-label">' . $title . '</label>
                    <span>' . $objeto->$key . '</span>
                  </div>
                </div>
              </div>
            </div>';


    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = ($errors->has($key)) ? '<span class="bottom-description text-danger"><strong>* ' . $errors->first($key) . '</strong></span>' : '';
    $value = isset($objeto->$key) ? $objeto->$key : null;

    $optionsAux = '';
    foreach ($options as $clave => $val) {
        $selected = '';
        if ($clave == $value) {
            $selected = 'selected="selected"';
        }
        $optionsAux .= '<option value="' . $clave . '" ' . $selected . '> ' . $val . '</option>';
    } //fin foreaach

    return '<div class="col">
            <div class="form-group ">
              <div class="' . $formato . '">
                <div class="form-group' . $class_error . '">
                  <label class="col-auto col-form-label">' . $title . '</label>
                  <select id="' . $key . '" name="' . $key . '" class="' . $class_select . '">
                    ' . $optionsAux . '
                  </select>
                  ' . $span_error . '
                </div>
              </div>
            </div>
          </div>';
}
// FUNCION DE GEOLOCALIZACION
function determinar_longitud_latitud_direction_with_googleApi($address)
{
    $url_base   = 'https://maps.googleapis.com/maps/api/geocode/json';
    $key        = 'AIzaSyDaZURDIygZM1jTkjB0TXecap2XcLirrM0';
    $url_send   = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&key=' . $key;
    $googleApiGeolocalizacion = null;
    $client     = new \GuzzleHttp\Client();
    try {
        $googleApiGeolocalizacion = $client->request('GET', $url_send);
        $googleApiGeolocalizacion = json_decode($googleApiGeolocalizacion->getBody()->getContents(), true);
    } catch (RequestException $e) {
        $googleApiGeolocalizacion['status'] == 'FAIL';
    }
    if ($googleApiGeolocalizacion['status'] == 'OK') {
        $latitud    = $googleApiGeolocalizacion['results'][0]['geometry']['location']['lat'];
        $longitud   = $googleApiGeolocalizacion['results'][0]['geometry']['location']['lng'];
    } else {
        $latitud    = 0;
        $longitud   = 0;
    }
    return ['latitud' => $latitud, 'longitud' => $longitud];
}

// Calcular la distancia entre 2 coordenadas
function distanceCalculation($point1_lat, $point1_long, $point2_lat, $point2_long, $unit = 'km', $decimals = 2)
{
    // Cálculo de la distancia en grados
    $degrees = rad2deg(acos((sin(deg2rad($point1_lat)) * sin(deg2rad($point2_lat))) + (cos(deg2rad($point1_lat)) * cos(deg2rad($point2_lat)) * cos(deg2rad($point1_long - $point2_long)))));

    // Conversión de la distancia en grados a la unidad escogida (kilómetros, millas o millas naúticas)
    switch ($unit) {
        case 'km':
            $distance = $degrees * 111.13384; // 1 grado = 111.13384 km, basándose en el diametro promedio de la Tierra (12.735 km)
            break;
        case 'mi':
            $distance = $degrees * 69.05482; // 1 grado = 69.05482 millas, basándose en el diametro promedio de la Tierra (7.913,1 millas)
            break;
        case 'nmi':
            $distance =  $degrees * 59.97662; // 1 grado = 59.97662 millas naúticas, basándose en el diametro promedio de la Tierra (6,876.3 millas naúticas)
    }
    return round($distance, $decimals);
}


// MODIFICACIONES A LAS FUNCTIONES ANTERIORES QUE SE AJUSTAN AL PROYECTO
//
// -
// -
// -
// -
// -



function armarSelectArrayCreateEditOT($options, $key, $title, $errors, $objeto, $class_select, $display_empty_select, $search_option, $width = "100%")
{

    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = null;
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);

    $optionsAux = '';
    foreach ($options as $clave => $val) {
        $selected = '';
        if ($clave == $value && !is_null($value)) {
            $selected = 'selected="selected"';
        }
        $optionsAux .= '<option value="' . $clave . '" ' . $selected . '> ' . $val . '</option>';
    } //fin foreaach
    $first_option = $display_empty_select ? '<option value="">Seleccionar...</option>' : null;

    return '<div class="form-group form-row ' . $class_error . '">
            <label class="col-auto col-form-label">' . $title . '</label>
            <div class="col">

              <select id="' . $key . '" name="' . $key . '" class="' . $class_select . ' selectpicker" data-live-search="' . $search_option . '" title="Seleccionar..." data-selected-text-format="count > 1" data-width="' . $width . '">

              ' . $first_option . '
                ' . $optionsAux . '
              </select>
            ' . $span_error . '
            </div>
          </div>';
}

function armarSelectArrayCreateEditOTSecuencia($options, $key, $title, $errors, $objeto, $class_select, $display_empty_select, $search_option, $col)
{

    $width = "100%";
    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = null;
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);

    $optionsAux = '';
    foreach ($options as $clave => $val) {
        $selected = '';
        if ($clave == $value && !is_null($value)) {
            $selected = 'selected="selected"';
        }
        $optionsAux .= '<option value="' . $clave . '" ' . $selected . '> ' . $val . '</option>';
    } //fin foreaach
    $first_option = $display_empty_select ? '<option value="">Seleccionar...</option>' : null;

    return '<div class="form-group form-row ' . $class_error . '">
            <label class="col-auto col-form-label">' . $title . '</label>
            <div class="col' . $col . '">

              <select id="' . $key . '" name="' . $key . '" class="' . $class_select . ' selectpicker" data-live-search="' . $search_option . '" title="Seleccionar..." data-selected-text-format="count > 1" data-width="' . $width . '">

              ' . $first_option . '
                ' . $optionsAux . '
              </select>
            ' . $span_error . '
            </div>
          </div>';
}

function armarSelectArrayCreateEditOTSecuenciaOperacional($options, $key, $errors, $objeto, $class_select, $display_empty_select, $search_option, $width = "100%")
{

    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = null;
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);

    $optionsAux = '';
    foreach ($options as $clave => $val) {
        $selected = '';
        if ($clave == $value && !is_null($value)) {
            $selected = 'selected="selected"';
        }
        $optionsAux .= '<option value="' . $clave . '" ' . $selected . '> ' . $val . '</option>';
    } //fin foreaach
    $first_option = $display_empty_select ? '<option value="">Seleccionar...</option>' : null;

    return '<div class="form-group form-row ' . $class_error . '">

            <div class="col">

              <select id="' . $key . '" name="' . $key . '" class="' . $class_select . ' selectpicker" data-live-search="' . $search_option . '" title="Seleccionar..." data-selected-text-format="count > 1" data-width="' . $width . '">

              ' . $first_option . '
                ' . $optionsAux . '
              </select>
            ' . $span_error . '
            </div>
          </div>';
}

function armarSelectArrayCreateEditOT2($options, $key, $title, $objeto, $class_select, $display_empty_select, $search_option, $width = "100%")
{

    // $class_error = $errors->has($key) ? ' error' : '';
    $span_error = null;
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);

    $optionsAux = '';
    foreach ($options as $clave => $val) {
        $selected = '';
        if ($clave == $value && !is_null($value)) {
            $selected = 'selected="selected"';
        }
        $optionsAux .= '<option value="' . $clave . '" ' . $selected . '> ' . $val . '</option>';
    } //fin foreaach
    $first_option = $display_empty_select ? '<option value="">Seleccionar...</option>' : null;

    return '<div class="form-group form-row">
            <label class="col-auto col-form-label">' . $title . '</label>
            <div class="col">

              <select id="' . $key . '" name="' . $key . '" class="' . $class_select . ' selectpicker" data-live-search="' . $search_option . '" title="Seleccionar..." data-selected-text-format="count > 1" data-width="' . $width . '">

              ' . $first_option . '
                ' . $optionsAux . '
              </select>
            ' . $span_error . '
            </div>
          </div>';
}

function armarSelectArrayCreateEditOTOnChange($options, $key, $title, $errors, $objeto, $class_select, $display_empty_select, $search_option, $onchange, $width = "100%")
{

    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = null;
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);

    $optionsAux = '';
    foreach ($options as $clave => $val) {
        $selected = '';
        if ($clave == $value && !is_null($value)) {
            $selected = 'selected="selected"';
        }
        $optionsAux .= '<option value="' . $clave . '" ' . $selected . '> ' . $val . '</option>';
    } //fin foreaach
    $first_option = $display_empty_select ? '<option value="">Seleccionar...</option>' : null;

    return '<div class="form-group form-row ' . $class_error . '">
            <label class="col-auto col-form-label">' . $title . '</label>
            <div class="col">

              <select onchange="' . $onchange . '" id="' . $key . '" name="' . $key . '" class="' . $class_select . ' selectpicker" data-live-search="' . $search_option . '" title="Seleccionar..." data-selected-text-format="count > 1" data-width="' . $width . '">

              ' . $first_option . '
                ' . $optionsAux . '
              </select>
            ' . $span_error . '
            </div>
          </div>';
}

function armarSelectArrayCreateEditOTEdipac($options, $key, $title, $errors, $objeto, $class_select, $display_empty_select, $search_option, $width = "100%")
{

    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = null;
    $value = old($key, isset($objeto) ? $objeto : null);

    $optionsAux = '';
    foreach ($options as $clave => $val) {
        $selected = '';
        if ($clave == $value && !is_null($value)) {
            $selected = 'selected="selected"';
        }
        $optionsAux .= '<option value="' . $clave . '" ' . $selected . '> ' . $val . '</option>';
    } //fin foreaach
    $first_option = $display_empty_select ? '<option value="">Seleccionar...</option>' : null;

    return '<div class="form-group form-row ' . $class_error . '">
            <label class="col-auto col-form-label">' . $title . '</label>
            <div class="col">

              <select id="' . $key . '" name="' . $key . '" class="' . $class_select . ' selectpicker" data-live-search="' . $search_option . '" title="Seleccionar..." data-selected-text-format="count > 1" data-width="' . $width . '">

              ' . $first_option . '
                ' . $optionsAux . '
              </select>
            ' . $span_error . '
            </div>
          </div>';
}

function armarSelectArrayCreateEditComuna($options, $key, $title, $errors, $objeto, $class_select, $display_empty_select, $search_option, $width = "100%")
{

    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = null;
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);

    $optionsAux = '';
    foreach ($options as $clave => $val) {
        $selected = '';
        if ($clave == $value && !is_null($value)) {
            $selected = 'selected="selected"';
        }
        $optionsAux .= '<option value="' . $clave . '" ' . $selected . '> ' . $val . '</option>';
    } //fin foreaach
    $first_option = $display_empty_select ? '<option value="">Seleccionar...</option>' : null;

    return '<div class="form-group form-row ' . $class_error . '">
            <label class="col-auto col-form-label">' . $title . '</label>


              <select id="' . $key . '" name="' . $key . '" class="' . $class_select . ' selectpicker" data-live-search="' . $search_option . '" title="Seleccionar..." data-selected-text-format="count > 1" data-width="' . $width . '">

              ' . $first_option . '
                ' . $optionsAux . '
              </select>
            ' . $span_error . '

          </div>';
}

function armarSelectArrayCreateEditUser($options, $key, $title, $errors, $objeto, $class_select, $display_empty_select, $search_option, $width = "100%")
{

    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = null;
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);

    $optionsAux = '';
    foreach ($options as $clave => $val) {
        $selected = '';
        if ($clave == $value && !is_null($value)) {
            $selected = 'selected="selected"';
        }
        $optionsAux .= '<option value="' . $clave . '" ' . $selected . '> ' . $val . '</option>';
    } //fin foreaach
    $first_option = $display_empty_select ? '<option value="">Seleccionar...</option>' : null;

    return '<div class="col">
            <div class="form-group ">
              <div class="col">
                <div class="form-group ' . $class_error . '">
                  <label class="col-auto col-form-label">' . $title . '</label>


                    <select id="' . $key . '" name="' . $key . '" class="' . $class_select . ' selectpicker" data-live-search="' . $search_option . '" title="Seleccionar..." data-selected-text-format="count > 1" data-width="' . $width . '">

                    ' . $first_option . '
                      ' . $optionsAux . '
                    </select>
                  ' . $span_error . '
                </div>
              </div>
            </div>
          </div>';
}


function armarSelectArrayCreateEditCusmtomCol($options, $key, $title, $errors, $objeto, $class_select, $display_empty_select, $search_option, $col)
{

    $width = "100%";
    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = null;
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);

    $optionsAux = '';
    foreach ($options as $clave => $val) {
        $selected = '';
        if ($clave == $value && !is_null($value)) {
            $selected = 'selected="selected"';
        }
        $optionsAux .= '<option value="' . $clave . '" ' . $selected . '> ' . $val . '</option>';
    } //fin foreaach
    $first_option = $display_empty_select ? '<option value="">Seleccionar...</option>' : null;

    return '<div class="col' . $col . '">
            <div class="form-group ">
              <div class="col">
                <div class="form-group ' . $class_error . '">
                  <label class="col-auto col-form-label">' . $title . '</label>


                    <select id="' . $key . '" name="' . $key . '" class="' . $class_select . ' selectpicker" data-live-search="' . $search_option . '" title="Seleccionar..." data-selected-text-format="count > 1" data-width="' . $width . '">

                    ' . $first_option . '
                      ' . $optionsAux . '
                    </select>
                  ' . $span_error . '
                </div>
              </div>
            </div>
          </div>';
}

function armarSelectArrayCreateEditCusmtomColValue($options, $key, $title, $errors, $objeto, $class_select, $display_empty_select, $search_option, $col, $default_value)
{

    $width = "100%";
    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = null;
    $value = old($key, isset($objeto->$key) ? $objeto->$key : $default_value);

    $optionsAux = '';
    foreach ($options as $clave => $val) {
        $selected = '';
        if ($clave == $value && !is_null($value)) {
            $selected = 'selected="selected"';
        }
        $optionsAux .= '<option value="' . $clave . '" ' . $selected . '> ' . $val . '</option>';
    } //fin foreaach
    $first_option = $display_empty_select ? '<option value="">Seleccionar...</option>' : null;

    return '<div class="col' . $col . '">
            <div class="form-group ">
              <div class="col">
                <div class="form-group ' . $class_error . '">
                  <label class="col-auto col-form-label">' . $title . '</label>


                    <select id="' . $key . '" name="' . $key . '" class="' . $class_select . ' selectpicker" data-live-search="' . $search_option . '" title="Seleccionar..." data-selected-text-format="count > 1" data-width="' . $width . '">

                    ' . $first_option . '
                      ' . $optionsAux . '
                    </select>
                  ' . $span_error . '
                </div>
              </div>
            </div>
          </div>';
}

function armarInputCreateEditOT($key, $title, $type, $errors, $objeto, $class_input, $required, $placeholder, $valor = null, $posicion = "unido")
{
    //dd($objeto);
    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = null;
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);
    $class = $posicion == "unido" ? "form-group form-row" : "form-group";
    // Si se envia directamente un valor debe sobreescribir el valor actual si lo hubiera
    if (isset($valor)) {
        $value = $valor;
    }

    if ($type == 'readonly')
        return '<div class="' . $class . '">
              <label class="col-auto col-form-label">' . $title . '</label>
              <div class="col">
                <span>' . $value . '</span>
              </div>
            </div>';
    else
        return '<div class="' . $class . ' ' . $class_error . '">
              <label class="col-auto col-form-label">' . $title . '</label>
              <div class="col">
                <input type="' . $type . '" id="' . $key . '" name="' . $key . '" value="' . $value . '" class="' . $class_input . '" ' . $required . ' placeholder="' . $placeholder . '">
                ' . $span_error . '
              </div>
            </div>';
}

function armarInputCreateEditOT_2($key, $title, $type, $errors, $objeto, $class_input, $required, $placeholder, $valor = null, $posicion = "unido")
{
    //dd($objeto);
    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = null;
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);
    $class = $posicion == "unido" ? "form-group form-row" : "form-group";
    // Si se envia directamente un valor debe sobreescribir el valor actual si lo hubiera
    if (isset($valor)) {
        $value = $valor;
    }

    if ($type == 'readonly')
        return '<div class="' . $class . '">
              <label class="col-auto col-form-label">' . $title . '</label>
              <div class="col">
                <span>' . $value . '</span>
              </div>
            </div>';
    else
        return '<div class="' . $class . ' ' . $class_error . '">
              <label class="col-auto col-form-label">' . $title . '</label>
              <div class="col">
                <input type="' . $type . '" id="' . $key . '" name="' . $key . '" value="' . $value . '" class="' . $class_input . '" ' . $required . ' placeholder="' . $placeholder . '">
                ' . $span_error . '
              </div>
            </div>';
}

function armarInputCreateEditCotiza($key, $title, $type, $errors, $objeto, $class_input, $required, $placeholder, $valor = null, $posicion = "unido")
{
    //dd($objeto);
    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = null;
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);
    $class = $posicion == "unido" ? "form-group form-row" : "form-group";
    // Si se envia directamente un valor debe sobreescribir el valor actual si lo hubiera
    if (isset($valor)) {
        $value = $valor;
    }

    if ($type == 'readonly')
        return '<div class="' . $class . '">
              <label class="col-auto col-form-label">' . $title . '</label>

                <span>' . $value . '</span>

            </div>';
    else
        return '<div class="' . $class . ' ' . $class_error . '">
              <label class="col-auto col-form-label">' . $title . '</label>

                <input type="' . $type . '" id="' . $key . '" name="' . $key . '" value="' . $value . '" class="' . $class_input . '" ' . $required . ' placeholder="' . $placeholder . '">
                ' . $span_error . '

            </div>';
}


function armarSelectArrayCreateEditOTSeparado($options, $key, $title, $errors, $objeto, $class_select, $display_empty_select, $search_option, $width = "100%")
{
    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = null;
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);

    $optionsAux = '';
    foreach ($options as $clave => $val) {
        $selected = '';
        if ($clave == $value && !is_null($value)) {
            $selected = 'selected="selected"';
        }
        $optionsAux .= '<option value="' . $clave . '" ' . $selected . '> ' . $val . '</option>';
    } //fin foreaach
    $first_option = $display_empty_select ? '<option value="">Seleccionar...</option>' : null;

    return '<div class="form-group form-row ' . $class_error . '">
            <label class="col-auto col-form-label">' . $title . '</label>
            <select id="' . $key . '" name="' . $key . '" class="' . $class_select . ' selectpicker" data-live-search="' . $search_option . '" title="Seleccionar..." data-selected-text-format="count > 1" data-width="' . $width . '">

                ' . $first_option . '
                ' . $optionsAux . '
              </select>
            ' . $span_error . '
          </div>';
}

function armarInputCreateEdit_2($formato, $key, $title, $type, $errors, $objeto, $class_input, $required, $placeholder)
{
    $class_error = $errors->has($key) ? ' error' : '';
    $span_error = ($errors->has($key)) ? '<span class="bottom-description text-danger"><strong>* ' . $errors->first($key) . '</strong></span>' : '';
    $value = old($key, isset($objeto->$key) ? $objeto->$key : null);

    if ($type == 'readonly')
        return '

      <div class="form-group form-row">
        <div class="' . $formato . '">
          <div class="form-group form-row">
            <label class="col-auto col-form-label">' . $title . '</label>
            <span>' . $value . '</span>
          </div>
        </div>
      </div>';
    else
        return '

      <div class="form-group form-row">
        <div class="' . $formato . '">
          <div class="form-group form-row ' . $class_error . '">
            <label class="col-auto col-form-label">' . $title . '</label>
            <input type="' . $type . '" id="' . $key . '" name="' . $key . '" value="' . $value . '" class="' . $class_input . '" ' . $required . ' placeholder="' . $placeholder . '">
            ' . $span_error . '
          </div>
        </div>
      </div>';
}


function inputReadOnly($title, $value, $id = null)
{
    $id = $id ? "id=\"" . $id . "\"" : null;
    return '<div class="form-group form-row">
            <label class="col-auto col-form-label" for="">' . $title . ':</label>
            <div class="col">
              <input ' . $id . ' type="text" class="form-control-plaintext" value="' . $value . '" readonly title="' . $value . '" data-toggle="tooltip">
            </div>
          </div>';
}

function inputReadOnlySinLabel($value, $id = null)
{
    $id = $id ? "id=\"" . $id . "\"" : null;
    return '<div class="form-group form-row">
            <div class="col">
              <input ' . $id . ' type="text" class="form-control-plaintext" value="' . $value . '" readonly title="' . $value . '" data-toggle="tooltip">
            </div>
          </div>';
}

//armar cualquier filtro Multiple de una vista con un objeto:
function optionsSelectObjetfilterMultipleOT($datos_filter, $value, $texts, $separador = '')
{
    $options_select = '';
    foreach ($datos_filter as $element) {

        if (!empty(request()->query($value))) {
            $selected = '';
            if (in_array($element->$value, request()->query($value))) {
                $selected = 'selected="selected"';
            }
            $options_select .= '<option value="' . $element->$value . '" ' . $selected . '> ' . armar_text_select($element, $texts, $separador) . '</option>';
        } else {
            $options_select .= '<option value="' . $element->$value . '" selected="selected"> ' . armar_text_select($element, $texts, $separador) . '</option>';
        }
    } //fin foreaach
    //var_dump(request()->query()); die();
    return $options_select;
}
// Formatear numero 1,000.00 => 1.000,00
function number_format_unlimited_precision($number, $dec_point = ",", $thousands_sep = ".", $dec_digits = 3)
{
    // dd($number);
    if ($number == null) return "";
    if ($number == "N/A") return "N/A";
    $tmp = explode('.', $number);
    $out = number_format(str_replace(",", "", $tmp[0]), 0, $dec_point, $thousands_sep);
    if ($dec_digits === 0) {
        return $out;
    }
    if (isset($tmp[1])) $out .= $dec_point . substr($tmp[1], 0, $dec_digits);

    return $out;
}

// Formatear numero 1,000.00 => 1.000,00
function number_format_unlimited_precision_cotizacion($number, $dec_point = ",", $thousands_sep = ".", $dec_digits = 3)
{


    if ($number == null) return "";
    if ($number == "N/A") return "N/A";
    $tmp = explode('.', $number);

    $out = number_format(str_replace(",", "", $tmp[0]), 0, $dec_point, $thousands_sep);
    // dd($number,$tmp,$out);
    if ($dec_digits === 0) {
        $number_redondeo_2 = round($number, $dec_digits);
        //dd($number_redondeo_2,number_format($number_redondeo_2, 0, $dec_point, $thousands_sep));
        return number_format($number_redondeo_2, 0, $dec_point, $thousands_sep);
        //return ceil($out_redondeo);
    }
    if (isset($tmp[1])) $out .= $dec_point . substr($tmp[1], 0, $dec_digits);

    return $out;
}

function number_format_unlimited_precision_sap($number, $dec_point = ",", $thousands_sep = ".", $dec_digits = 3)
{
    // dd($number);
    if ($number == null) return "";
    if ($number == "N/A") return "";
    $tmp = explode('.', $number);
    $out = number_format(str_replace(",", "", $tmp[0]), 0, $dec_point, $thousands_sep);
    if ($dec_digits === 0) {
        return $out;
    }
    if (isset($tmp[1])) $out .= $dec_point . substr($tmp[1], 0, $dec_digits);

    return $out;
}


function number_format_unlimited_no_decimal($number, $dec_point = ",", $thousands_sep = ".", $dec_digits = 0)
{
    // dd($number);
    if ($number == null) return "";
    if ($number == "N/A") return "N/A";
    $tmp = explode('.', $number);
    $out = number_format(str_replace(",", "", $tmp[0]), 0, $dec_point, $thousands_sep);
    if ($dec_digits === 0) {
        return $out;
    }
    if (isset($tmp[1])) $out .= $dec_point . substr($tmp[1], 0, $dec_digits);

    return $out;
}



// function get_working_hours($ini_str, $end_str)
// {

//     //config
//     //$ini_time = [8, 15]; //hr, min
//     //$end_time = [17, 45]; //hr, min
//     $dia_ini = date('l', strtotime($ini_str));
//     $dia_end = date('l', strtotime($end_str));

//     //Horario Lunes a Jueves
//     $horario = SystemVariable::where('name', 'Horario')
//         ->where('deleted', 0)
//         ->first();
//     $horario = explode(',', $horario->contents);

//     $ini_time_lun_jue = explode(':', $horario[0]);
//     $end_time_lun_jue = explode(':', $horario[1]);

//     //Horario Dia Viernes
//     $horario_viernes = SystemVariable::where('name', 'HorarioViernes')
//         ->where('deleted', 0)
//         ->first();
//     $horario_viernes = explode(',', $horario_viernes->contents);

//     $ini_time_viernes = explode(':', $horario_viernes[0]);
//     $end_time_viernes = explode(':', $horario_viernes[1]);

//     $ini_time = $ini_time_lun_jue;

//   if($dia_end=="Friday"){
//     $end_time=$end_time_viernes;
//   }else{
//     $end_time=$end_time_lun_jue;
//   }

//   //dd($horario_entrada,$horario_salida,$ini_time,$end_time);
//   //date objects
//   $ini = date_create($ini_str);

//   $ini_wk = date_time_set(date_create($ini_str), $ini_time[0], $ini_time[1]);

//   $end = date_create($end_str);
//   $end_wk = date_time_set(date_create($end_str), $end_time[0], $end_time[1]);
//   //dd($ini_time,$end_time,$ini_wk,$end_wk);
//   //dd($ini, $ini_wk, $end, $end_wk);
//   $current_ini_time = [(int) $ini->format("H"), (int) $ini->format("i")];
//   $current_end_time = [(int) $end->format("H"), (int) $end->format("i")];
//   // dump("tiempo inicial: " . $ini->format("H") . $ini->format("i"), "tiempo final: " . $end->format("H") . $end->format("i"));
//   //days
//   $workdays_arr = get_workdays($ini, $end, $current_ini_time, $current_end_time);

//   $workdays_count = count($workdays_arr);
//   $viernes=0;

//   if($workdays_count>0){
//     foreach($workdays_arr as $workday){

//      // var_dump($workdays_arr[$i]);
//       if(date('l',strtotime($workday))=="Friday"){
//        // $array_aux=$workdays_arr[$i];
//         $viernes++;
//       }
//     }
//   }

//   //sdd($viernes);
//   $workday_seconds = (($end_time_lun_jue[0] * 60 + $end_time_lun_jue[1]) - ($ini_time_lun_jue[0] * 60 + $ini_time_lun_jue[1])) * 60;
//   $workday_seconds_viernes = (($end_time_viernes[0] * 60 + $end_time_viernes[1]) - ($ini_time_viernes[0] * 60 + $ini_time_viernes[1])) * 60;
//   //dd($workday_seconds_viernes / 3600);
//   //get time difference
//   $ini_seconds = 0;
//   $end_seconds = 0;
//   if (in_array($ini->format('Y-m-d'), $workdays_arr)) $ini_seconds = $ini->format('U') - $ini_wk->format('U');
//   if (in_array($end->format('Y-m-d'), $workdays_arr)) $end_seconds = $end_wk->format('U') - $end->format('U');
//   $seconds_dif = $ini_seconds > 0 ? $ini_seconds : 0;
//   if ($end_seconds > 0) $seconds_dif += $end_seconds;
//   //final calculations
//   //Fechas con excepcion de horario de salida - Inicio
//     $fecha_excepcion=SystemVariable::where('name','FechaExcepcion')
//                               ->where('deleted',0)
//                               ->first();
//     if($fecha_excepcion){

//       $fechas_excepciones=explode(',',$fecha_excepcion->contents);

//       $hora_excepcion=SystemVariable::where('name','HoraExcepcion')
//                                 ->where('deleted',0)
//                                 ->first();
//       $horas_excepciones=explode(',',$hora_excepcion->contents);
//     }else{
//       $fechas_excepciones=[];
//       $horas_excepciones=[];
//     }

//     //dd($horario_entrada,$horario_salida,$ini_time,$end_time);
//     //date objects
//     $ini = date_create($ini_str);

//     $ini_wk = date_time_set(date_create($ini_str), $ini_time[0], $ini_time[1]);

//     $end = date_create($end_str);
//     $end_wk = date_time_set(date_create($end_str), $end_time[0], $end_time[1]);
//     //dd($ini_time,$end_time,$ini_wk,$end_wk);
//     //dd($ini, $ini_wk, $end, $end_wk);
//     $current_ini_time = [(int) $ini->format("H"), (int) $ini->format("i")];
//     $current_end_time = [(int) $end->format("H"), (int) $end->format("i")];
//     // dump("tiempo inicial: " . $ini->format("H") . $ini->format("i"), "tiempo final: " . $end->format("H") . $end->format("i"));
//     //days
//     $workdays_arr = get_workdays($ini, $end, $current_ini_time, $current_end_time);

//     $workdays_count = count($workdays_arr);
//     $viernes = 0;

//     if ($workdays_count > 0) {
//         foreach ($workdays_arr as $workday) {

//             // var_dump($workdays_arr[$i]);
//             if (date('l', strtotime($workday)) == "Friday") {
//                 // $array_aux=$workdays_arr[$i];
//                 $viernes++;
//             }
//         }
//     }

//     //sdd($viernes);
//     $workday_seconds = (($end_time_lun_jue[0] * 60 + $end_time_lun_jue[1]) - ($ini_time_lun_jue[0] * 60 + $ini_time_lun_jue[1])) * 60;
//     $workday_seconds_viernes = (($end_time_viernes[0] * 60 + $end_time_viernes[1]) - ($ini_time_viernes[0] * 60 + $ini_time_viernes[1])) * 60;
//     //dd($workday_seconds_viernes / 3600);
//     //get time difference
//     $ini_seconds = 0;
//     $end_seconds = 0;
//     if (in_array($ini->format('Y-m-d'), $workdays_arr)) $ini_seconds = $ini->format('U') - $ini_wk->format('U');
//     if (in_array($end->format('Y-m-d'), $workdays_arr)) $end_seconds = $end_wk->format('U') - $end->format('U');
//     $seconds_dif = $ini_seconds > 0 ? $ini_seconds : 0;
//     if ($end_seconds > 0) $seconds_dif += $end_seconds;
//     //final calculations
//     //Fechas con excepcion de horario de salida - Inicio
//     $fecha_excepcion = SystemVariable::where('name', 'FechaExcepcion')
//         ->where('deleted', 0)
//         ->first();
//     if ($fecha_excepcion) {

//         $fechas_excepciones = explode(',', $fecha_excepcion->contents);

//         $hora_excepcion = SystemVariable::where('name', 'HoraExcepcion')
//             ->where('deleted', 0)
//             ->first();
//         $horas_excepciones = explode(',', $hora_excepcion->contents);
//     } else {
//         $fechas_excepciones = [];
//         $horas_excepciones = [];
//     }

//     $total_seconds_excepcions = 0;
//     //recorrer las fecha excepcion y validar si se encuenbtran dentro del arrey workdays_arr
//     for ($i = 0; $i < count($fechas_excepciones); $i++) {
//         if (in_array($fechas_excepciones[$i], $workdays_arr)) {
//             $end_time_excepcion = explode(':', $horas_excepciones[$i]);
//             $workday_seconds_excepcions = (($end_time_excepcion[0] * 60 + $end_time_excepcion[1]) - ($ini_time[0] * 60 + $ini_time[1])) * 60;
//             $total_seconds_excepcions += $workday_seconds_excepcions;
//         }
//     }
//     //Fechas con excepcion de horario de salida - Fin

//     $working_seconds = (($workdays_count - $viernes) * $workday_seconds) + ($viernes * $workday_seconds_viernes) - $seconds_dif - $total_seconds_excepcions;
//     // echo $ini_str . ' - ' . $end_str . '; Working Hours:' . ($working_seconds / 3600 / 9.5) . "<br>";
//     // return $working_seconds / 3600 / 9.5; //return Worked Days
//     if ($working_seconds < 0) {
//         $working_seconds = 0;
//     }
//     //dd($working_seconds/ 3600);
//     return $working_seconds / 3600; //return hrs
// }

function get_working_hours2($ini_str, $end_str)
{
    $ini = date_create($ini_str);
    $end = date_create($end_str);

    $diffInSeconds = $end->getTimestamp() - $ini->getTimestamp();

    if ($diffInSeconds < 0) {
        $diffInSeconds = 0;
    }

    return $diffInSeconds / 3600; // devuelve horas reales (24/7)
}

// function get_working_hours2($ini_str, $end_str)
// {

//     //config
//     //$ini_time = [8, 15]; //hr, min
//     //$end_time = [17, 45]; //hr, min
//     $dia_ini = date('l', strtotime($ini_str));
//     $dia_end = date('l', strtotime($end_str));

//     //Horario Lunes a Jueves
//     $horario = SystemVariable::where('name', 'SinHorarios')
//         ->where('deleted', 0)
//         ->first();
//     $horario = explode(',', $horario->contents);

//     $ini_time_lun_jue = explode(':', $horario[0]);
//     $end_time_lun_jue = explode(':', $horario[1]);

//     //Horario Dia Viernes
//     $horario_viernes = SystemVariable::where('name', 'SinHorarioViernes')
//         ->where('deleted', 0)
//         ->first();
//     $horario_viernes = explode(',', $horario_viernes->contents);

//     $ini_time_viernes = explode(':', $horario_viernes[0]);
//     $end_time_viernes = explode(':', $horario_viernes[1]);

//     $ini_time = $ini_time_lun_jue;

//     if ($dia_end == "Sunday") {
//         $end_time = $end_time_viernes;
//     } else {
//         $end_time = $end_time_lun_jue;
//     }

//     //dd($horario_entrada,$horario_salida,$ini_time,$end_time);
//     //date objects
//     $ini = date_create($ini_str);

//     $ini_wk = date_time_set(date_create($ini_str), $ini_time[0], $ini_time[1]);

//     $end = date_create($end_str);
//     $end_wk = date_time_set(date_create($end_str), $end_time[0], $end_time[1]);
//     //dd($ini_time,$end_time,$ini_wk,$end_wk);
//     //dd($ini, $ini_wk, $end, $end_wk);
//     $current_ini_time = [(int) $ini->format("H"), (int) $ini->format("i")];
//     $current_end_time = [(int) $end->format("H"), (int) $end->format("i")];
//     // dump("tiempo inicial: " . $ini->format("H") . $ini->format("i"), "tiempo final: " . $end->format("H") . $end->format("i"));
//     //days
//     $workdays_arr = get_workdays2($ini, $end, $current_ini_time, $current_end_time);

//     $workdays_count = count($workdays_arr);
//     $viernes = 0;

//     if ($workdays_count > 0) {
//         foreach ($workdays_arr as $workday) {

//             // var_dump($workdays_arr[$i]);
//             if (date('l', strtotime($workday)) == "Friday") {
//                 // $array_aux=$workdays_arr[$i];
//                 $viernes++;
//             }
//         }
//     }

//     //sdd($viernes);
//     $workday_seconds = (($end_time_lun_jue[0] * 60 + $end_time_lun_jue[1]) - ($ini_time_lun_jue[0] * 60 + $ini_time_lun_jue[1])) * 60;
//     $workday_seconds_viernes = (($end_time_viernes[0] * 60 + $end_time_viernes[1]) - ($ini_time_viernes[0] * 60 + $ini_time_viernes[1])) * 60;
//     //dd($workday_seconds_viernes / 3600);
//     //get time difference
//     $ini_seconds = 0;
//     $end_seconds = 0;
//     if (in_array($ini->format('Y-m-d'), $workdays_arr)) $ini_seconds = $ini->format('U') - $ini_wk->format('U');
//     if (in_array($end->format('Y-m-d'), $workdays_arr)) $end_seconds = $end_wk->format('U') - $end->format('U');
//     $seconds_dif = $ini_seconds > 0 ? $ini_seconds : 0;
//     if ($end_seconds > 0) $seconds_dif += $end_seconds;
//     //final calculations
//     $working_seconds = (($workdays_count - $viernes) * $workday_seconds) + ($viernes * $workday_seconds_viernes) - $seconds_dif;
//     // echo $ini_str . ' - ' . $end_str . '; Working Hours:' . ($working_seconds / 3600 / 9.5) . "<br>";
//     // return $working_seconds / 3600 / 9.5; //return Worked Days
//     if ($working_seconds < 0) {
//         $working_seconds = 0;
//     }
//     //dd($working_seconds/ 3600);
//     return $working_seconds / 3600; //return hrs
// }


function get_working_hours_DESM($ini_str, $dic_variables)
{

    //config
    $dia_ini = date('l', strtotime($ini_str));
    $dia_end = date('l', strtotime($dic_variables['current_date']));

    $ini_time = $dic_variables['ini_time_lun_jue'];

  if($dia_end=="Friday"){
    $end_time=$dic_variables['end_time_viernes'];
  }else{
    $end_time=$dic_variables['end_time_lun_jue'];
  }


  //date objects
  $ini = date_create($ini_str);

  $ini_wk = date_time_set(date_create($ini_str), $ini_time[0], $ini_time[1]);

  $end = date_create($dic_variables['current_date']);
  $end_wk = date_time_set(date_create($dic_variables['current_date']), $end_time[0], $end_time[1]);

  $current_ini_time = [(int) $ini->format("H"), (int) $ini->format("i")];
  $current_end_time = [(int) $end->format("H"), (int) $end->format("i")];

  //days
  $workdays_arr = get_workdays_DESM($ini, $end, $current_ini_time, $current_end_time, $dic_variables);

  $workdays_count = count($workdays_arr);
  $viernes=0;

  if($workdays_count>0){
    foreach($workdays_arr as $workday){


      if(date('l',strtotime($workday))=="Friday"){
       ;
        $viernes++;
      }
    }
  }

  $workday_seconds = (($dic_variables['end_time_lun_jue'][0] * 60 + $dic_variables['end_time_lun_jue'][1]) - ($dic_variables['ini_time_lun_jue'][0] * 60 + $dic_variables['ini_time_lun_jue'][1])) * 60;
  $workday_seconds_viernes = (($dic_variables['end_time_viernes'][0] * 60 + $dic_variables['end_time_viernes'][1]) - ($dic_variables['ini_time_viernes'][0] * 60 + $dic_variables['ini_time_viernes'][1])) * 60;

  //get time difference
  $ini_seconds = 0;
  $end_seconds = 0;
  if (in_array($ini->format('Y-m-d'), $workdays_arr)) $ini_seconds = $ini->format('U') - $ini_wk->format('U');
  if (in_array($end->format('Y-m-d'), $workdays_arr)) $end_seconds = $end_wk->format('U') - $end->format('U');
  $seconds_dif = $ini_seconds > 0 ? $ini_seconds : 0;
  if ($end_seconds > 0) $seconds_dif += $end_seconds;
  //final calculations
  //Fechas con excepcion de horario de salida - Inicio
    $fecha_excepcion=SystemVariable::where('name','FechaExcepcion')
                              ->where('deleted',0)
                              ->first();
    if($fecha_excepcion){

      $fechas_excepciones=explode(',',$fecha_excepcion->contents);

      $hora_excepcion=SystemVariable::where('name','HoraExcepcion')
                                ->where('deleted',0)
                                ->first();
      $horas_excepciones=explode(',',$hora_excepcion->contents);
    }else{
      $fechas_excepciones=[];
      $horas_excepciones=[];
    }


    //date objects
    $ini = date_create($ini_str);

    $ini_wk = date_time_set(date_create($ini_str), $ini_time[0], $ini_time[1]);

    $end = date_create($dic_variables['current_date']);
    $end_wk = date_time_set(date_create($dic_variables['current_date']), $end_time[0], $end_time[1]);

    $current_ini_time = [(int) $ini->format("H"), (int) $ini->format("i")];
    $current_end_time = [(int) $end->format("H"), (int) $end->format("i")];

    //days
    $workdays_arr = get_workdays_DESM($ini, $end, $current_ini_time, $current_end_time, $dic_variables);

    $workdays_count = count($workdays_arr);
    $viernes = 0;

    if ($workdays_count > 0) {
        foreach ($workdays_arr as $workday) {


            if (date('l', strtotime($workday)) == "Friday") {;
                $viernes++;
            }
        }
    }

    $workday_seconds = (($dic_variables['end_time_lun_jue'][0] * 60 + $dic_variables['end_time_lun_jue'][1]) - ($dic_variables['ini_time_lun_jue'][0] * 60 + $dic_variables['ini_time_lun_jue'][1])) * 60;
    $workday_seconds_viernes = (($dic_variables['end_time_viernes'][0] * 60 + $dic_variables['end_time_viernes'][1]) - ($dic_variables['ini_time_viernes'][0] * 60 + $dic_variables['ini_time_viernes'][1])) * 60;

    //get time difference
    $ini_seconds = 0;
    $end_seconds = 0;
    if (in_array($ini->format('Y-m-d'), $workdays_arr)) $ini_seconds = $ini->format('U') - $ini_wk->format('U');
    if (in_array($end->format('Y-m-d'), $workdays_arr)) $end_seconds = $end_wk->format('U') - $end->format('U');
    $seconds_dif = $ini_seconds > 0 ? $ini_seconds : 0;
    if ($end_seconds > 0) $seconds_dif += $end_seconds;
    //final calculations
    //Fechas con excepcion de horario de salida - Inicio
    $fecha_excepcion = SystemVariable::where('name', 'FechaExcepcion')
        ->where('deleted', 0)
        ->first();
    if ($fecha_excepcion) {

        $fechas_excepciones = explode(',', $fecha_excepcion->contents);

        $hora_excepcion = SystemVariable::where('name', 'HoraExcepcion')
            ->where('deleted', 0)
            ->first();
        $horas_excepciones = explode(',', $hora_excepcion->contents);
    } else {
        $fechas_excepciones = [];
        $horas_excepciones = [];
    }

    $total_seconds_excepcions = 0;
    //recorrer las fecha excepcion y validar si se encuenbtran dentro del arrey workdays_arr
    for ($i = 0; $i < count($fechas_excepciones); $i++) {
        if (in_array($fechas_excepciones[$i], $workdays_arr)) {
            $end_time_excepcion = explode(':', $horas_excepciones[$i]);
            $workday_seconds_excepcions = (($end_time_excepcion[0] * 60 + $end_time_excepcion[1]) - ($ini_time[0] * 60 + $ini_time[1])) * 60;
            $total_seconds_excepcions += $workday_seconds_excepcions;
        }
    }
    //Fechas con excepcion de horario de salida - Fin

    $working_seconds = (($workdays_count - $viernes) * $workday_seconds) + ($viernes * $workday_seconds_viernes) - $seconds_dif - $total_seconds_excepcions;

    // return $working_seconds / 3600 / 9.5; //return Worked Days
    if ($working_seconds < 0) {
        $working_seconds = 0;
    }

    return $working_seconds / 3600; //return hrs
}


// function get_working_hours_DESM2($ini_str, $dic_variables)
// {

//      $ini = date_create($ini_str);
//     $end = date_create($dic_variables['current_date']);

//     $diffInSeconds = $end->getTimestamp() - $ini->getTimestamp();

//     if ($diffInSeconds < 0) {
//         $diffInSeconds = 0;
//     }

//     return $diffInSeconds / 3600; // horas reales entre fechas, sin restricciones

// }


// function get_workdays($ini, $end, $current_ini_time, $current_end_time)
// {
//     //config
//     //Se obtienen los horarios dependiendo del dia de la fecha de inicio y la fecha fin
//     if ($ini->format('w') == 5) {
//         $horario = SystemVariable::where('name', 'HorarioViernes')
//             ->where('deleted', 0)
//             ->first();
//         $horario = explode(',', $horario->contents);
//         $ini_time = explode(':', $horario[0]); //[8, 15]; //hr, min
//         $end_time_ini = explode(':', $horario[1]);
//     } else {
//         $horario = SystemVariable::where('name', 'Horario')
//             ->where('deleted', 0)
//             ->first();
//         $horario = explode(',', $horario->contents);
//         $ini_time = explode(':', $horario[0]); //[8, 15]; //hr, min
//         $end_time_ini = explode(':', $horario[1]);
//     }

//     if ($end->format('w') == 5) {
//         $horario = SystemVariable::where('name', 'HorarioViernes')
//             ->where('deleted', 0)
//             ->first();
//         $horario = explode(',', $horario->contents);

//         $ini_time = explode(':', $horario[0]);
//         $end_time_end = explode(':', $horario[1]);
//     } else {
//         $horario = SystemVariable::where('name', 'Horario')
//             ->where('deleted', 0)
//             ->first();
//         $horario = explode(',', $horario->contents);
//         $ini_time = explode(':', $horario[0]); //[8, 15]; //hr, min
//         $end_time_end = explode(':', $horario[1]);
//     }

//     //dd($ini,$end, $current_ini_time, $current_end_time);
//     //Obtenemos el Horario de Trabajo definido en la tabla de variables del sistema
//     //[17, 45]; //hr, min

//     //config
//     $skipdays = [6, 0]; //saturday:6; sunday:0
//     //$skipdates = ['2020-12-25', '2021-01-01', '2022-09-16', '2022-09-19', '2022-10-10', '2022-10-31', '2022-11-01', '2022-12-08']; //eg: ['2020-05-01'];
//     $feriados = SystemVariable::where('name', 'Feriados')
//         ->where('deleted', 0)
//         ->first();
//     $skipdates = explode(',', $feriados->contents);

//     //vars
//     $current = clone $ini;
//     $current_disp = $current->format('Y-m-d');
//     $end_disp = $end->format('Y-m-d');
//     // Validar que el dia inicial sea valido
//     $days_arr = [];
//     //days range
//     while ($current_disp <= $end_disp) {
//         if (!in_array($current->format('w'), $skipdays) && !in_array($current_disp, $skipdates)) {
//             $days_arr[] = $current_disp;
//         }
//         $current->add(new DateInterval('P1D')); //adds one day
//         $current_disp = $current->format('Y-m-d');
//     }

//     // Si la hora del primer dia es mayor a la hora final del dia
//     if ($current_ini_time[0] > $end_time_ini[0] && !in_array($ini->format('w'), $skipdays)) {
//         // eliminamos el primer dia
//         unset($days_arr[0]);
//         // Si es la misma hora pero mayor minutos igual eliminamos
//     } else if ($current_ini_time[0] == $end_time_ini[0] && $current_ini_time[1] >= $end_time_ini[1] && !in_array($ini->format('w'), $skipdays)) {
//         // eliminamos el primer dia
//         unset($days_arr[0]);
//     }
//     if ($current_end_time[0] < $ini_time[0] && !in_array($end->format('w'), $skipdays)) {
//         // eliminamos el ultimo dia
//         array_pop($days_arr);
//         // Si es la misma hora pero menor minutos igual eliminamos
//     } else if ($current_end_time[0] == $ini_time[0] && $current_end_time[1] <= $ini_time[1] && !in_array($end->format('w'), $skipdays)) {
//         // eliminamos el ultimo dia
//         array_pop($days_arr);
//     }
//     //dd($days_arr);
//     return $days_arr;
// }

// <?php
// app/Support/helpers.php (sugerido)

// use App\SystemVariable; // <-- ajusta si tu modelo vive en otro namespace
// use DateTimeImmutable;

/**
 * Calcula HORAS hábiles entre $ini_str y $end_str (fin EXCLUSIVO).
 * - Lu–Jue: 'Horario' (ej. 08:15,17:45)
 * - Viernes: 'HorarioViernes' (ej. 08:15,15:00)
 * - Excluye fines de semana y feriados (SystemVariable:Feriados)
 * - Aplica excepciones (FechaExcepcion + HoraExcepcion) para hora de fin del día
 * Retorna float (horas), igual que tu función original.
 */
function get_working_hours($ini_str, $end_str)
{
    // Normalizar fechas
    $start = new DateTimeImmutable($ini_str);
    $end   = new DateTimeImmutable($end_str);

    if ($end <= $start) {
        return 0.0;
    }

    // === Cargar configuración desde SystemVariable (una vez por llamada) ===
    $vHorario         = SystemVariable::where('name', 'Horario')->where('deleted', 0)->first();
    $vHorarioViernes  = SystemVariable::where('name', 'HorarioViernes')->where('deleted', 0)->first();
    $vFeriados        = SystemVariable::where('name', 'Feriados')->where('deleted', 0)->first();
    $vFechaExcepcion  = SystemVariable::where('name', 'FechaExcepcion')->where('deleted', 0)->first();
    $vHoraExcepcion   = SystemVariable::where('name', 'HoraExcepcion')->where('deleted', 0)->first();

    // Horario L–J
    $horario = $vHorario ? (string) $vHorario->contents : '08:15,17:45';
    $parts = array_map('trim', explode(',', $horario));
    $ini_lj = isset($parts[0]) ? $parts[0] : '08:15';
    $fin_lj = isset($parts[1]) ? $parts[1] : '17:45';

    // Horario Viernes
    $horarioVie = $vHorarioViernes ? (string) $vHorarioViernes->contents : '08:15,15:00';
    $partsV = array_map('trim', explode(',', $horarioVie));
    $ini_v = isset($partsV[0]) ? $partsV[0] : '08:15';
    $fin_v = isset($partsV[1]) ? $partsV[1] : '15:00';

    // Feriados -> set para lookup O(1)
    $feriadosCsv  = $vFeriados ? (string) $vFeriados->contents : '';
    $feriadosList = array_filter(array_map('trim', explode(',', $feriadosCsv)));
    $feriados = array_fill_keys($feriadosList, true);

    // Excepciones (fecha paralela a hora)
    $fecExcCsv = $vFechaExcepcion ? (string) $vFechaExcepcion->contents : '';
    $horExcCsv = $vHoraExcepcion  ? (string) $vHoraExcepcion->contents  : '';
    $fecExcArr = array_filter(array_map('trim', explode(',', $fecExcCsv)));
    $horExcArr = array_filter(array_map('trim', explode(',', $horExcCsv)));
    $excepciones = [];
    foreach ($fecExcArr as $i => $f) {
        if (isset($horExcArr[$i]) && $horExcArr[$i] !== '') {
            $excepciones[$f] = $horExcArr[$i]; // reemplaza hora fin de ese día
        }
    }

    // === Acumulador en segundos, recortando por día con fin EXCLUSIVO ===
    $totalSeconds = 0;
    $day    = new DateTimeImmutable($start->format('Y-m-d')); // 00:00 del día de inicio
    $endDay = new DateTimeImmutable($end->format('Y-m-d'));   // 00:00 del día fin

    while ($day <= $endDay) {
        $dateStr = $day->format('Y-m-d');
        $w = (int) $day->format('w'); // 0=Dom ... 6=Sáb

        // Excluir fines de semana y feriados
        if ($w === 0 || $w === 6 || isset($feriados[$dateStr])) {
            $day = $day->modify('+1 day');
            continue;
        }

        // Elegir ventana laboral del día
        if ($w === 5) { // Viernes
            $ini_h = $ini_v; $fin_h = $fin_v;
        } else {
            $ini_h = $ini_lj; $fin_h = $fin_lj;
        }

        // Excepción de fin de jornada para este día
        if (isset($excepciones[$dateStr])) {
            $fin_h = $excepciones[$dateStr];
        }

        // Ventana del día
        $winStart = new DateTimeImmutable("$dateStr $ini_h");
        $winEnd   = new DateTimeImmutable("$dateStr $fin_h");

        // Ventana inválida -> saltar
        if ($winEnd <= $winStart) {
            $day = $day->modify('+1 day');
            continue;
        }

        // Intersección [start, end) con [winStart, winEnd)
        $fromTs = max($winStart->getTimestamp(), $start->getTimestamp());
        $toTs   = min($winEnd->getTimestamp(),   $end->getTimestamp());

        if ($toTs > $fromTs) {
            $totalSeconds += ($toTs - $fromTs);
        }

        $day = $day->modify('+1 day');
    }

    // Retornar HORAS (float), igual que tu firma original
    return $totalSeconds / 3600;
}

/**
 * Devuelve un array de fechas (Y-m-d) laborables entre $ini y $end (INCLUSIVO),
 * excluyendo fines de semana y feriados de SystemVariable.
 * Firma compatible con tu método original.
 */
function get_workdays($ini, $end, $current_ini_time = null, $current_end_time = null)
{
    // Normalizar entradas
    if ($ini instanceof DateTime) {
        $ini = new DateTimeImmutable($ini->format('Y-m-d H:i:s'));
    } elseif (!($ini instanceof DateTimeImmutable)) {
        $ini = new DateTimeImmutable((string)$ini);
    }
    if ($end instanceof DateTime) {
        $end = new DateTimeImmutable($end->format('Y-m-d H:i:s'));
    } elseif (!($end instanceof DateTimeImmutable)) {
        $end = new DateTimeImmutable((string)$end);
    }

    if ($end < $ini) return [];

    // Feriados
    $vFeriados = SystemVariable::where('name', 'Feriados')->where('deleted', 0)->first();
    $feriadosCsv  = $vFeriados ? (string) $vFeriados->contents : '';
    $feriadosList = array_filter(array_map('trim', explode(',', $feriadosCsv)));
    $feriados = array_fill_keys($feriadosList, true);

    $days = [];
    $day  = new DateTimeImmutable($ini->format('Y-m-d'));
    $last = new DateTimeImmutable($end->format('Y-m-d'));

    while ($day <= $last) {
        $w = (int)$day->format('w'); // 0=Dom..6=Sáb
        $dateStr = $day->format('Y-m-d');
        if ($w !== 0 && $w !== 6 && !isset($feriados[$dateStr])) {
            $days[] = $dateStr;
        }
        $day = $day->modify('+1 day');
    }

    return array_values($days);
}


function get_workdays2($ini, $end, $current_ini_time, $current_end_time)
{
    //config
    //Se obtienen los horarios dependiendo del dia de la fecha de inicio y la fecha fin
    if ($ini->format('w') == 5) {
        $horario = SystemVariable::where('name', 'SinHorarioViernes')
            ->where('deleted', 0)
            ->first();
        $horario = explode(',', $horario->contents);
        $ini_time = explode(':', $horario[0]); //[8, 15]; //hr, min
        $end_time_ini = explode(':', $horario[1]);
    } else {
        $horario = SystemVariable::where('name', 'SinHorarios')
            ->where('deleted', 0)
            ->first();
        $horario = explode(',', $horario->contents);
        $ini_time = explode(':', $horario[0]); //[8, 15]; //hr, min
        $end_time_ini = explode(':', $horario[1]);
    }

    if ($end->format('w') == 5) {
        $horario = SystemVariable::where('name', 'SinHorarioViernes')
            ->where('deleted', 0)
            ->first();
        $horario = explode(',', $horario->contents);

        $ini_time = explode(':', $horario[0]);
        $end_time_end = explode(':', $horario[1]);
    } else {
        $horario = SystemVariable::where('name', 'SinHorarios')
            ->where('deleted', 0)
            ->first();
        $horario = explode(',', $horario->contents);
        $ini_time = explode(':', $horario[0]); //[8, 15]; //hr, min
        $end_time_end = explode(':', $horario[1]);
    }

    //dd($ini,$end, $current_ini_time, $current_end_time);
    //Obtenemos el Horario de Trabajo definido en la tabla de variables del sistema
    //[17, 45]; //hr, min

    //config
    $skipdays = [6, 0]; //saturday:6; sunday:0
    //$skipdates = ['2020-12-25', '2021-01-01', '2022-09-16', '2022-09-19', '2022-10-10', '2022-10-31', '2022-11-01', '2022-12-08']; //eg: ['2020-05-01'];
    $feriados = SystemVariable::where('name', 'SinFeriados')
        ->where('deleted', 0)
        ->first();
    $skipdates = explode(',', $feriados->contents);

    //vars
    $current = clone $ini;
    $current_disp = $current->format('Y-m-d');
    $end_disp = $end->format('Y-m-d');
    // Validar que el dia inicial sea valido
    $days_arr = [];
    //days range
    while ($current_disp <= $end_disp) {
        if (!in_array($current->format('w'), $skipdays) && !in_array($current_disp, $skipdates)) {
            $days_arr[] = $current_disp;
        }
        $current->add(new DateInterval('P1D')); //adds one day
        $current_disp = $current->format('Y-m-d');
    }

    // Si la hora del primer dia es mayor a la hora final del dia
    if ($current_ini_time[0] > $end_time_ini[0] && !in_array($ini->format('w'), $skipdays)) {
        // eliminamos el primer dia
        unset($days_arr[0]);
        // Si es la misma hora pero mayor minutos igual eliminamos
    } else if ($current_ini_time[0] == $end_time_ini[0] && $current_ini_time[1] >= $end_time_ini[1] && !in_array($ini->format('w'), $skipdays)) {
        // eliminamos el primer dia
        unset($days_arr[0]);
    }
    if ($current_end_time[0] < $ini_time[0] && !in_array($end->format('w'), $skipdays)) {
        // eliminamos el ultimo dia
        array_pop($days_arr);
        // Si es la misma hora pero menor minutos igual eliminamos
    } else if ($current_end_time[0] == $ini_time[0] && $current_end_time[1] <= $ini_time[1] && !in_array($end->format('w'), $skipdays)) {
        // eliminamos el ultimo dia
        array_pop($days_arr);
    }
    //dd($days_arr);
    return $days_arr;
}

function get_workdays_DESM($ini, $end, $current_ini_time, $current_end_time, $dic_variables)
{
    //config
    //Se obtienen los horarios dependiendo del dia de la fecha de inicio y la fecha fin
    if ($ini->format('w') == 5) {

        $ini_time = $dic_variables['ini_time_viernes'];
        $end_time_ini = $dic_variables['end_time_viernes'];
    } else {

        $ini_time = $dic_variables['ini_time_lun_jue'];
        $end_time_ini = $dic_variables['end_time_lun_jue'];
    }

    if ($end->format('w') == 5) {


        $ini_time = $dic_variables['ini_time_viernes'];
        $end_time_ini = $dic_variables['end_time_viernes'];
    } else {

        $ini_time = $dic_variables['ini_time_lun_jue'];
        $end_time_ini = $dic_variables['end_time_lun_jue'];
    }
    //config
    $skipdays = [6, 0]; //saturday:6; sunday:0

    //vars
    $current = clone $ini;
    $current_disp = $current->format('Y-m-d');
    $end_disp = $end->format('Y-m-d');
    //dd($current_disp, $end_disp);
    // Validar que el dia inicial sea valido
    $days_arr = [];
    //days range
    while ($current_disp <= $end_disp) {
        if (!in_array($current->format('w'), $skipdays) && !in_array($current_disp, $dic_variables['skipdates'])) {
            $days_arr[] = $current_disp;
        }
        $current->add(new DateInterval('P1D')); //adds one day
        $current_disp = $current->format('Y-m-d');
    }

    // Si la hora del primer dia es mayor a la hora final del dia
    if ($current_ini_time[0] > $end_time_ini[0] && !in_array($ini->format('w'), $skipdays)) {
        // eliminamos el primer dia
        unset($days_arr[0]);
        // Si es la misma hora pero mayor minutos igual eliminamos
    } else if ($current_ini_time[0] == $end_time_ini[0] && $current_ini_time[1] >= $end_time_ini[1] && !in_array($ini->format('w'), $skipdays)) {
        // eliminamos el primer dia
        unset($days_arr[0]);
    }
    if ($current_end_time[0] < $ini_time[0] && !in_array($end->format('w'), $skipdays)) {
        // eliminamos el ultimo dia
        array_pop($days_arr);
        // Si es la misma hora pero menor minutos igual eliminamos
    } else if ($current_end_time[0] == $ini_time[0] && $current_end_time[1] <= $ini_time[1] && !in_array($end->format('w'), $skipdays)) {
        // eliminamos el ultimo dia
        array_pop($days_arr);
    }
    //dd($days_arr);
    return $days_arr;
}

function get_working_hours_muestra($ini_str, $end_str)
{

    //config
    //$ini_time = [8, 15]; //hr, min
    //$end_time = [17, 45]; //hr, min


    //Horario Lunes a Viernes Sala de Muestras
    $horario = SystemVariable::where('name', 'HorarioSalaMuestras')
        ->where('deleted', 0)
        ->first();
    $horario = explode(',', $horario->contents);

    $ini_time = explode(':', $horario[0]);
    $end_time = explode(':', $horario[1]);

  //dd($horario_entrada,$horario_salida,$ini_time,$end_time);
  //date objects
  $ini = date_create($ini_str);

  $ini_wk = date_time_set(date_create($ini_str), $ini_time[0], $ini_time[1]);

  $end = date_create($end_str);
  $end_wk = date_time_set(date_create($end_str), $end_time[0], $end_time[1]);


  //dd($ini_time,$end_time,$ini_wk,$end_wk);
  //dd($ini, $ini_wk, $end, $end_wk);
  $current_ini_time = [(int) $ini->format("H"), (int) $ini->format("i")];
  $current_end_time = [(int) $end->format("H"), (int) $end->format("i")];
  // dump("tiempo inicial: " . $ini->format("H") . $ini->format("i"), "tiempo final: " . $end->format("H") . $end->format("i"));
  //days

  $workdays_arr = get_workdays_muestra($ini, $end, $current_ini_time, $current_end_time);

  //if(count($workdays_arr)>1){
  //  $workdays_count = count($workdays_arr)-1;
//  }else{
    $workdays_count = count($workdays_arr);
 // }

//dd($workday_seconds_excepcions,$total_seconds_excepcions);


  /*$viernes=0;

  if($workdays_count>0){
    foreach($workdays_arr as $workday){

     // var_dump($workdays_arr[$i]);
      if(date('l',strtotime($workday))=="Friday"){
       // $array_aux=$workdays_arr[$i];
        $viernes++;
      }
    }
  }*/

  //sdd($viernes);

  $workday_seconds = (($end_time[0] * 60 + $end_time[1]) - ($ini_time[0] * 60 + $ini_time[1])) * 60;
 // dd($workday_seconds);
  //dd($workdays_count,$workday_seconds,$end_time[0],$end_time[1],$ini_time[0],$ini_time[1]);
  //$workday_seconds_viernes = (($end_time_viernes[0] * 60 + $end_time_viernes[1]) - ($ini_time_viernes[0] * 60 + $ini_time_viernes[1])) * 60;
  //dd($workday_seconds / 3600);
  //get time difference
  $ini_seconds = 0;
  $end_seconds = 0;

  if (in_array($ini->format('Y-m-d'), $workdays_arr)) $ini_seconds = $ini->format('U') - $ini_wk->format('U');
  if (in_array($end->format('Y-m-d'), $workdays_arr)) $end_seconds = $end_wk->format('U') - $end->format('U');
  $seconds_dif = $ini_seconds > 0 ? $ini_seconds : 0;
  if ($end_seconds > 0) $seconds_dif += $end_seconds;
 // dd("fecha inicio",$ini,"fecha inicio unix",$ini->format('U'),"fecha horario inicio",$ini_wk,"fecha horario inicio unix",$ini_wk->format('U'),"fecha fin",$end,"fecha fin unix",$end->format('U'),"fecha fin horario",$end_wk,"fecha fin horario unix",$end_wk->format('U'));
  //final calculations
 // dd($workdays_count,$workday_seconds,$seconds_dif);

    $workday_seconds = (($end_time[0] * 60 + $end_time[1]) - ($ini_time[0] * 60 + $ini_time[1])) * 60;
    // dd($workday_seconds);
    //dd($workdays_count,$workday_seconds,$end_time[0],$end_time[1],$ini_time[0],$ini_time[1]);
    //$workday_seconds_viernes = (($end_time_viernes[0] * 60 + $end_time_viernes[1]) - ($ini_time_viernes[0] * 60 + $ini_time_viernes[1])) * 60;
    //dd($workday_seconds / 3600);
    //get time difference
    $ini_seconds = 0;
    $end_seconds = 0;

    if (in_array($ini->format('Y-m-d'), $workdays_arr)) $ini_seconds = $ini->format('U') - $ini_wk->format('U');
    if (in_array($end->format('Y-m-d'), $workdays_arr)) $end_seconds = $end_wk->format('U') - $end->format('U');
    $seconds_dif = $ini_seconds > 0 ? $ini_seconds : 0;
    if ($end_seconds > 0) $seconds_dif += $end_seconds;
    // dd("fecha inicio",$ini,"fecha inicio unix",$ini->format('U'),"fecha horario inicio",$ini_wk,"fecha horario inicio unix",$ini_wk->format('U'),"fecha fin",$end,"fecha fin unix",$end->format('U'),"fecha fin horario",$end_wk,"fecha fin horario unix",$end_wk->format('U'));
    //final calculations
    // dd($workdays_count,$workday_seconds,$seconds_dif);

    //Fechas con excepcion de horario de salida - Inicio
    $fecha_excepcion = SystemVariable::where('name', 'FechaExcepcion')
        ->where('deleted', 0)
        ->first();
    if ($fecha_excepcion) {

        $fechas_excepciones = explode(',', $fecha_excepcion->contents);

        $hora_excepcion = SystemVariable::where('name', 'HoraExcepcion')
            ->where('deleted', 0)
            ->first();
        $horas_excepciones = explode(',', $hora_excepcion->contents);
    } else {
        $fechas_excepciones = [];
        $horas_excepciones = [];
    }

    $total_seconds_excepcions = 0;
    //recorrer las fecha excepcion y validar si se encuenbtran dentro del arrey workdays_arr
    for ($i = 0; $i < count($fechas_excepciones); $i++) {
        if (in_array($fechas_excepciones[$i], $workdays_arr)) {
            $end_time_excepcion = explode(':', $horas_excepciones[$i]);
            $workday_seconds_excepcions = (($end_time_excepcion[0] * 60 + $end_time_excepcion[1]) - ($ini_time[0] * 60 + $ini_time[1])) * 60;
            $total_seconds_excepcions += $workday_seconds_excepcions;
        }
    }
    //Fechas con excepcion de horario de salida - Fin

    $working_seconds = ($workdays_count * $workday_seconds) - $seconds_dif - $total_seconds_excepcions;
    // echo $ini_str . ' - ' . $end_str . '; Working Hours:' . ($working_seconds / 3600 / 9.5) . "<br>";
    // return $working_seconds / 3600 / 9.5; //return Worked Days
    // dd($working_seconds);
    if ($working_seconds < 0) {
        $working_seconds = 0;
    }
    //dd($working_seconds);
    //dd($working_seconds/ 3600);
    return $working_seconds / 3600; //return hrs
}

function get_workdays_muestra($ini, $end, $current_ini_time, $current_end_time)
{
    //config
    //Se obtienen los horarios dependiendo del dia de la fecha de inicio y la fecha fin

    $horario = SystemVariable::where('name', 'HorarioSalaMuestras')
        ->where('deleted', 0)
        ->first();
    $horario = explode(',', $horario->contents);
    $ini_time = explode(':', $horario[0]); //[8, 15]; //hr, min
    $end_time = explode(':', $horario[1]);


    //dd($ini,$end, $current_ini_time, $current_end_time);
    //Obtenemos el Horario de Trabajo definido en la tabla de variables del sistema
    //[17, 45]; //hr, min

    //config
    $skipdays = [6, 0]; //saturday:6; sunday:0
    //$skipdates = ['2020-12-25', '2021-01-01', '2022-09-16', '2022-09-19', '2022-10-10', '2022-10-31', '2022-11-01', '2022-12-08']; //eg: ['2020-05-01'];
    $feriados = SystemVariable::where('name', 'Feriados')
        ->where('deleted', 0)
        ->first();
    $skipdates = explode(',', $feriados->contents);

    //vars
    $current = clone $ini;
    $current_disp = $current->format('Y-m-d');
    $end_disp = $end->format('Y-m-d');
    // Validar que el dia inicial sea valido
    $days_arr = [];
    //days range
    while ($current_disp <= $end_disp) {
        if (!in_array($current->format('w'), $skipdays) && !in_array($current_disp, $skipdates)) {
            $days_arr[] = $current_disp;
        }
        $current->add(new DateInterval('P1D')); //adds one day
        $current_disp = $current->format('Y-m-d');
    }

    // Si la hora del primer dia es mayor a la hora final del dia
    if ($current_ini_time[0] > $end_time[0] && !in_array($ini->format('w'), $skipdays)) {
        // eliminamos el primer dia
        unset($days_arr[0]);
        // Si es la misma hora pero mayor minutos igual eliminamos
    } else if ($current_ini_time[0] == $end_time[0] && $current_ini_time[1] >= $end_time[1] && !in_array($ini->format('w'), $skipdays)) {
        // eliminamos el primer dia
        unset($days_arr[0]);
    }
    if ($current_end_time[0] < $ini_time[0] && !in_array($end->format('w'), $skipdays)) {
        // eliminamos el ultimo dia
        array_pop($days_arr);
        // Si es la misma hora pero menor minutos igual eliminamos
    } else if ($current_end_time[0] == $ini_time[0] && $current_end_time[1] <= $ini_time[1] && !in_array($end->format('w'), $skipdays)) {
        // eliminamos el ultimo dia
        array_pop($days_arr);
    }
    //dd($days_arr);
    return $days_arr;
}


// function get_working_hours_muestra2($ini_str, $end_str)
// {

//    $ini = date_create($ini_str);
//     $end = date_create($end_str);

//     $diffInSeconds = $end->getTimestamp() - $ini->getTimestamp();

//     if ($diffInSeconds < 0) {
//         $diffInSeconds = 0;
//     }

//     return $diffInSeconds / 3600; // horas reales (24/7)
// }

// function get_workdays_muestra2($ini, $end, $current_ini_time, $current_end_time)
// {
//   $days_arr = [];

//     $current = clone $ini;
//     $current_disp = $current->format('Y-m-d');
//     $end_disp = $end->format('Y-m-d');

//     while ($current_disp <= $end_disp) {
//         $days_arr[] = $current_disp;
//         $current->add(new DateInterval('P1D')); // Suma un día
//         $current_disp = $current->format('Y-m-d');
//     }

//     return $days_arr;
// }


function push_notification($titulo, $body, $data, $token)
{
    // Envio de notificacion push a tecnico
    //notif incluye la informacion que se va a desplegar en la notifiacion en pantalla
    $notif = array('title' => $titulo, 'body' => $body, 'icon' => 'myicon');
    $data = array('data' => $data);
    //data incluye la información que se requiere comunicar a la aplicacion
    // $data = array('punto' => '12345', 'sesion' => true);
    //token es el token que entrega la app para identificar el telefono
    // $token = "7CYU:APA91bF19MRGkSt8Nrb_Ov_h8boJG4do1eCJuKooLkrOSj4Nf39FIem3Emuj7tc";
    $token = $token;
    //key es el identificador de la aplicacion, este se le debe pedir al desarrollador android
    $key = 'Authorization:key=AAAAeM86l8E:APA91bHBnyZaQA5jsRCciPquoRzEQmOuLPLBy9cMEkYAAVw_9LnMTepdt6LE3IP_WQdnFRPwAFb0Uo-Y_Dy7EO-spll5jcYzlbooIEzXBAgnX3GhyPg5CncVXcJk8LtNvopi9sW6XwL7';
    //se crea recurso cURL y se asigna la url a la que se enviaran los datos
    $ch = curl_init("https://fcm.googleapis.com/fcm/send");
    //se configura tipo de envio
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //se configuran los datos a ser enviados en el POST, en 'to' iria el token del movil del que quieres conectarte, puede ir notif o data o ambos
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('notification' => $notif, 'to' => $token, 'data' => $data)));
    //se configura para que tmb muestre el resultado de la operación
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //se configura el header que incluye el authorization key que se extrae desde la consola de firebase
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $key));
    //se ejecuta el POST, $response almacena la respuesta desde el servidor
    $response = curl_exec($ch);
    //se cierra el recurso cURL
    curl_close($ch);
    return $response;
}

//No se esta usando y en sustitucion se usa obtenerMargenSugerido a partir 17-10-2022
//esta funcion calcula el margen minimo en base a la formula del excel madre del cotiza
function calcularMargenSugerido($detalle)
{
    $porcentaje_margen = 0;
    // Si al momento de guardar el detalle no esta asociado a una cotizacion no podemos calcular el margen
    if (!$detalle->cotizacion) {
        return 0;
    }
    // Si hay comision es por que es exportacion y siempre debe ser 45%
    if ($detalle->cotizacion->comision > 0) {
        // Para toda caja cuyo destino sea el exterioir de Chile, el MC corresponde al 45 % de la suma de todos los costos
        return $detalle->precios->costo_total["usd_mm2"] * 0.45;
    }
    switch ($detalle->rubro->mercado) {
        case 'Esquineros':
            return 0;
            break;
        case 'Salmones':
        case 'Vinos':
            $porcentaje_margen = Tarifario::where("mercado", $detalle->rubro->mercado)->where("tipo_cliente", $detalle->cotizacion->client->tipo_cliente)->where("carton_frecuente", $detalle->carton->excepcion != null ? 1 : 0)->first();
            break;
        case 'Industrial':
            // El margen se calcula segun si la planta es de santiago (tiltil y buin ) o osrno
            $planta = ($detalle->planta_id == 3) ? "osorno" : "santiago";
            $porcentaje_margen = Tarifario::where("mercado", $detalle->rubro->mercado)->where("tipo_cliente", $detalle->cotizacion->client->tipo_cliente)->where("carton_frecuente", $detalle->carton->excepcion != null ? 1 : 0)->where('planta', $planta)->first();
            break;
        case 'Hortofruticola':
            $estacionalidad = ($detalle->rubro->estacionalidad);
            $porcentaje_margen = Tarifario::where("mercado", $detalle->rubro->mercado)->where("tipo_cliente", $detalle->cotizacion->client->tipo_cliente)->where("carton_frecuente", $detalle->carton->excepcion != null ? 1 : 0)->where('estacionalidad', $estacionalidad)->first();

            if (($detalle->area_hc * $detalle->cantidad / 1000) > $detalle->rubro->volumen_minimo_descuento) {
                $porcentaje_margen->porcentaje_margen = $porcentaje_margen->porcentaje_margen - $detalle->rubro->descuento;
            }
            break;
        default:
            # code...
            break;
    }
    // dd($detalle->precios->costo_total["usd_mm2"] * $porcentaje_margen->porcentaje_margen);
    $margen = $detalle->precios->costo_total["usd_mm2"] * $porcentaje_margen->porcentaje_margen;
    return $margen;
}

//Obtiene de forma directa el margen minimo desde el mantenedor
function obtenerMargenSugerido($detalle)
{
    $porcentaje_margen = 0;
    // Si al momento de guardar el detalle no esta asociado a una cotizacion no podemos calcular el margen
    if (!$detalle->cotizacion) {
        return 0;
    }
    // Si hay comision es por que es exportacion y siempre debe ser 45%
    if ($detalle->cotizacion->comision > 0) {
        // Para toda caja cuyo destino sea el exterioir de Chile, el MC corresponde al 45 % de la suma de todos los costos
        return $detalle->precios->costo_total["usd_mm2"] * 0.45;
    }
    $rubro_mercado = Rubro::where('id', $detalle->rubro_id)->first();

    //$mercado=Hierarchy::where('descripcion',strtoupper($rubro_mercado->mercado))->first();
    $mercado = Hierarchy::where('id', $rubro_mercado->mercado_id)->first();
    //dd($mercado->id);
    $comision = Cotizacion::where('id', $detalle->cotizacion_id)->first();
    $client = Client::where('id', $comision->client_id)->first();
    $margen = MargenMinimo::where('rubro_id', $detalle->rubro_id)
        ->where('mercado_id', $mercado->id)
        ->where('cluster', $client->tipo_cliente)
        ->first();
    if ($margen) {
        $margen_valor = $margen->minimo;
    } else {
        $margen_valor = 0;
    }
    /*switch ($detalle->rubro->mercado) {
    case 'Esquineros':
      return 0;
      break;
    case 'Salmones':
    case 'Vinos':
      $porcentaje_margen = Tarifario::where("mercado", $detalle->rubro->mercado)->where("tipo_cliente", $detalle->cotizacion->client->tipo_cliente)->where("carton_frecuente", $detalle->carton->excepcion != null ? 1 : 0)->first();
      break;
    case 'Industrial':
      // El margen se calcula segun si la planta es de santiago (tiltil y buin ) o osrno
      $planta = ($detalle->planta_id == 3) ? "osorno" : "santiago";
      $porcentaje_margen = Tarifario::where("mercado", $detalle->rubro->mercado)->where("tipo_cliente", $detalle->cotizacion->client->tipo_cliente)->where("carton_frecuente", $detalle->carton->excepcion != null ? 1 : 0)->where('planta', $planta)->first();
      break;
    case 'Hortofruticola':
      $estacionalidad = ($detalle->rubro->estacionalidad);
      $porcentaje_margen = Tarifario::where("mercado", $detalle->rubro->mercado)->where("tipo_cliente", $detalle->cotizacion->client->tipo_cliente)->where("carton_frecuente", $detalle->carton->excepcion != null ? 1 : 0)->where('estacionalidad', $estacionalidad)->first();

      if (($detalle->area_hc * $detalle->cantidad / 1000) > $detalle->rubro->volumen_minimo_descuento) {
        $porcentaje_margen->porcentaje_margen = $porcentaje_margen->porcentaje_margen - $detalle->rubro->descuento;
      }
      break;
    default:
      # code...
      break;
  }*/
    // dd($detalle->precios->costo_total["usd_mm2"] * $porcentaje_margen->porcentaje_margen);
    //$margen = $detalle->precios->costo_total["usd_mm2"] * $porcentaje_margen->porcentaje_margen;
    return $margen->minimo;
}


function changelog($model, $excel_row, $operacion, $codigo_operacion)
{
    // var_dump($excel_row->toJson());
    // dd($model->getDirty(), $excel_row);
    if ($operacion == "INSERT") {
        $changelog = new Changelog();
        $changelog->column_name = null;
        $changelog->table_name = $model->getTable();
        $changelog->old_value = null;
        $changelog->new_value = null;
        $changelog->item_id = null;
        $changelog->user_id = Auth()->user()->id;
        $changelog->user = Auth()->user()->toArray();
        $changelog->codigo_operacion = $codigo_operacion;
        $changelog->tipo_operacion =  $operacion;
        $changelog->excel_row = $excel_row;
        $changelog->save();
        return $changelog;
    }

    foreach ($model->getDirty() as  $columna => $cambio) {
        if ($columna == "orden") {
            continue;
        }
        // dd(Auth()->user()->toArray());
        // dd($model->getOriginal($columna), $model->getTable(), $columna, $cambio, $model);
        $changelog = new Changelog();
        $changelog->column_name = $columna;
        $changelog->table_name = $model->getTable();
        $changelog->old_value = $model->getOriginal($columna);
        $changelog->new_value = $cambio;
        $changelog->item_id = $model->id;
        $changelog->user_id = Auth()->user()->id;
        $changelog->user = Auth()->user()->toArray();
        $changelog->codigo_operacion = $codigo_operacion;
        $changelog->tipo_operacion =  $operacion;
        $changelog->excel_row = $excel_row;
        $changelog->save();
    }
}

function get_working_hours_report($ini_str, $end_str, $ini_time, $end_time)
{
    //config
    //$ini_time = [8, 15]; //hr, min
    //$end_time = [17, 45]; //hr, min
    //Obtenemos el Horario de Trabajo definido en la tabla de variables del sistema
    /*$horario=SystemVariable::where('name','Horario')
                          ->where('deleted',0)
                          ->first();
  $horario=explode(',',$horario->contents);

  $ini_time=explode(':',$horario[0]);
  $end_time=explode(':',$horario[1]);*/
    //dd($ini_time,$end_time);
    //date objects
    $ini = date_create($ini_str);
    //dd($ini);
    $ini_wk = date_time_set(date_create($ini_str), $ini_time[0], $ini_time[1]);

  $end = date_create($end_str);
  $end_wk = date_time_set(date_create($end_str), $end_time[0], $end_time[1]);
  //dd($ini_time,$end_time,$ini_wk,$end_wk);
  // dd($ini, $ini_wk, $end, $end_wk);
  $current_ini_time = [(int) $ini->format("H"), (int) $ini->format("i")];
  $current_end_time = [(int) $end->format("H"), (int) $end->format("i")];
  // dump("tiempo inicial: " . $ini->format("H") . $ini->format("i"), "tiempo final: " . $end->format("H") . $end->format("i"));
  //days
  $workdays_arr = get_workdays($ini, $end, $current_ini_time, $current_end_time);
  $workdays_count = count($workdays_arr);
  $workday_seconds = (($end_time[0] * 60 + $end_time[1]) - ($ini_time[0] * 60 + $ini_time[1])) * 60;
  // dd($workday_seconds / 3600);
  //get time difference
  $ini_seconds = 0;
  $end_seconds = 0;
  if (in_array($ini->format('Y-m-d'), $workdays_arr)) $ini_seconds = $ini->format('U') - $ini_wk->format('U');
  if (in_array($end->format('Y-m-d'), $workdays_arr)) $end_seconds = $end_wk->format('U') - $end->format('U');
  $seconds_dif = $ini_seconds > 0 ? $ini_seconds : 0;
  if ($end_seconds > 0) $seconds_dif += $end_seconds;
  //final calculations
  //Fechas con excepciones de horario de salida - Inicio
    $fecha_excepcion=SystemVariable::where('name','FechaExcepcion')
                              ->where('deleted',0)
                              ->first();
    if($fecha_excepcion){

      $fechas_excepciones=explode(',',$fecha_excepcion->contents);

        $fechas_excepciones = explode(',', $fecha_excepcion->contents);

        $hora_excepcion = SystemVariable::where('name', 'HoraExcepcion')
            ->where('deleted', 0)
            ->first();
        $horas_excepciones = explode(',', $hora_excepcion->contents);
    } else {
        $fechas_excepciones = [];
        $horas_excepciones = [];
    }

    $total_seconds_excepcions = 0;
    //recorrer las fecha excepcion y validar si se encuenbtran dentro del arrey workdays_arr
    for ($i = 0; $i < count($fechas_excepciones); $i++) {
        if (in_array($fechas_excepciones[$i], $workdays_arr)) {
            $end_time_excepcion = explode(':', $horas_excepciones[$i]);
            $workday_seconds_excepcions = (($end_time_excepcion[0] * 60 + $end_time_excepcion[1]) - ($ini_time[0] * 60 + $ini_time[1])) * 60;
            $total_seconds_excepcions += $workday_seconds_excepcions;
        }
    }
    //Fechas con excepciones de horario de salida - Fin

    $working_seconds = ($workdays_count * $workday_seconds) - $seconds_dif - $total_seconds_excepcions;
    // echo $ini_str . ' - ' . $end_str . '; Working Hours:' . ($working_seconds / 3600 / 9.5) . "<br>";
    // return $working_seconds / 3600 / 9.5; //return Worked Days
    if ($working_seconds < 0) {
        $working_seconds = 0;
    }
    return $working_seconds / 3600; //return hrs
}

function inputEditDescripcion($title, $value, $id = null)
{
    $name = $id;
    $id = $id ? "id=\"" . $id . "\"" : null;

    return '<div class="form-group form-row">
            <label class="col-auto col-form-label" for="">' . $title . ':</label>
            <div class="col">
              <input ' . $id . ' type="text" class="form-control" value="' . $value . '" name="' . $name . '" title="' . $value . '" data-toggle="tooltip">
            </div>
          </div>';
}

function obtenerMargenSugeridoNew($detalle)
{
  // Si al momento de guardar el detalle no esta asociado a una cotizacion no podemos calcular el margen
  if (!$detalle->cotizacion) {
    return 0;
  }

  $margen = $detalle->precios->precio_total["usd_mm2"] - $detalle->precios->costo_total["usd_mm2"];

  // dd($detalle->precios->costo_total["usd_mm2"] * $porcentaje_margen->porcentaje_margen);
  //$margen = $detalle->precios->costo_total["usd_mm2"] * $porcentaje_margen->porcentaje_margen;
  return $margen;
}
