<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-4 offset-4">
            <!-- Tipo de Cálculo-->
            {!! armarSelectArrayCreateEditOT([1=>"Cálculo HC y Cartón",2=>"Cálculo HC",3=>"Cartón"], 'tipo_calculo', 'Tipo de Cálculo' , $errors, $areahc ,'form-control',false,true) !!}
          </div>
        </div>
        <div class="row">

          <div class="col-4 calculo_inputs">
            <!-- Largo (mm) -->
            {!! armarInputCreateEditOT('interno_largo', 'Largo (mm):', 'number',$errors, $areahc, 'form-control calculo-hc', '', '') !!}
            <!-- Ancho (mm) -->
            {!! armarInputCreateEditOT('interno_ancho', 'Ancho (mm):', 'number',$errors, $areahc, 'form-control calculo-hc', '', '') !!}
            <!-- Alto (mm) -->
            {!! armarInputCreateEditOT('interno_alto', 'Alto (mm):', 'number',$errors, $areahc, 'form-control calculo-hc', '', '') !!}
            <!-- Estilo-->
            {!! armarSelectArrayCreateEditOT($styles, 'style_id', 'Estilo' , $errors, $areahc ,'form-control calculo-hc',true,true) !!}
            <!-- Traslape o Gap -->
            {!! armarInputCreateEditOT('traslape', 'Traslape [mm] (Estilos 202, 221) o Gap [mm] (Estilos 216, 223):', 'number',$errors, $areahc, 'form-control calculo-hc', '', '') !!}
            <!-- TIPO ITEM -->
            {!! armarSelectArrayCreateEditOT($productTypes, 'areahc_product_type_id', 'Tipo item' , $errors, $areahc ,'form-control calculo-hc',true,true) !!}
            <!-- prepicado_ventilacion-->
            {!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'prepicado_ventilacion', 'Prepicado y/o Ventilacion' , $errors, $areahc ,'form-control',true,false) !!}
            <!-- Tipo de Onda -->
            {!! armarSelectArrayCreateEditOT($ondas, 'onda_id', 'Tipo de Onda' , $errors, $areahc ,'form-control  calculo-hc  calculo-carton',true,false) !!}
            <!-- Proceso -->
            {!! armarSelectArrayCreateEditOT($procesos, 'process_id', 'Proceso' , $errors, $areahc ,'form-control  calculo-hc',true,false) !!}
            <!-- Rubro-->
            {!! armarSelectArrayCreateEditOT($rubros, 'rubro_id', 'Rubro' , $errors, $areahc ,'form-control  calculo-carton',true,true) !!}


          </div>
          <div class="col-4 calculo_inputs">
            <!-- Envase Primario -->
            {!! armarSelectArrayCreateEditOT($envases, 'envase_id', 'Envase Primario' , $errors, $areahc ,'form-control',true,true) !!}
            <!-- contenido_caja (Kg) -->
            {!! armarInputCreateEditOT('contenido_caja', 'Contenido Caja (Kg):', 'number',$errors, $areahc, 'form-control', '', '') !!}

            <!-- N° Palets apilados -->
            {!! armarInputCreateEditOT('areahc_pallets_apilados', 'N° Palets apilados:', 'number',$errors, $areahc, 'form-control', '', '') !!}

            <!-- N° Cajas Apiladas por Palet -->
            {!! armarInputCreateEditOT('cajas_apiladas_por_pallet', 'N° Cajas Apiladas por Palet:', 'number',$errors, $areahc, 'form-control', '', '') !!}
            <!-- N° Filas columnares por Palet -->
            {!! armarInputCreateEditOT('filas_columnares_por_pallet', 'N° Filas columnares por Palet:', 'number',$errors, $areahc, 'form-control', '', '') !!}
            <!-- Color Cartón-->
            {!! armarSelectArrayCreateEditOT([1=>"Café",2=>"Blanco"], 'carton_color', 'Color Cartón' , $errors, $areahc ,'form-control calculo-carton',true,true) !!}
            <!-- Numero Colores-->
            {!! armarSelectArrayCreateEditOT([0,1,2,3,4,5], 'numero_colores', 'Número Colores' , $errors, $areahc ,'form-control',true,true) !!}
            <!-- RMT (Lb)-->
            {!! armarInputCreateEditOT('rmt', 'RMT (Lb):', 'number',$errors, $areahc, 'form-control calculo-carton', '', '') !!}

            <div id="ect_input_container" class="form-group form-row " style="display:none;">
              <label class="col-auto col-form-label">ECT min (lbf):</label>
              <div class="col">
                <input type="number" id="ect_min_ingresado" name="ect_min_ingresado" value="" class="form-control">

              </div>
            </div>
          </div>
          <div class="col-4">
            <div id="resultados" class="">
              <h3>Resultados</h3>
              <input type="hidden" name="codigo_carton_id" id="codigo_carton_id">
              {!! inputReadOnly(' Largo Exterior (mm) ', '','externo_largo') !!}
              {!! inputReadOnly(' Ancho Exterior (mm) ', '','externo_ancho') !!}
              {!! inputReadOnly(' Alto Exterior (mm)', '','externo_alto') !!}
              {!! inputReadOnly(' Area HC (m2) ', '','areahc') !!}
              {!! inputReadOnly(' RMT (lb) ', '','rmt_resultado') !!}
              {!! inputReadOnly(' ECT min (lbf) ', '','ect_min') !!}
              <h3>Cartón Seleccionado</h3>
              {!! inputReadOnly(' Codigo Cartón ', '','codigo_carton') !!}
              {!! inputReadOnly(' ECT min (lbf) Cartón ', '','ect_min_carton') !!}

            </div>
            <button id="sincronizarAreaHC" class="btn btn-success float-right " style="display: none;">Llevar a Detalle</button></button>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a data-dismiss="modal" class="btn btn-light">Cancelar</a>
  <!-- <button type="submit" class="btn btn-success">{{ isset($areahc->id) ? __('Actualizar') : __('Guardar') }}</button> -->
  <button id="guardarAreaHC" type="submit" class="btn btn-success float-right">{{ isset($areahc->id) ? __('Actualizar') : __('Calcular') }}</button>
  <button id="limpiarAreaHC" class="btn btn-light">Limpiar</button>
</div>