<style>
  .tooltip {
    pointer-events: none;
  }

  .table td,
  .table th {
    text-align: center;
  }

  .table tr {
    transition: all 0.3s;
  }

  .actualizado {
    background-color: #e0ffe0;
  }

  [data-notify="message"] {
    font-size: 120%;
  }

  button[disabled]:hover {
    cursor: not-allowed
  }

  .error-costeo {
    background-color: #fdcece;
  }

  #cotizacion-datos-comerciales .form-group.form-row label.col-auto.col-form-label {
    padding: 0px 5px;
    margin-bottom: 3px;
  }

  #cotizacion-datos-comerciales label {
    padding: 0px 5px;
    margin-bottom: 0px;
  }

  #loading {
    width: 100%;
    height: 100%;
    top: 0px;
    left: 0px;
    position: fixed;
    display: block;
    z-index: 1199;
    background-color: rgba(0, 0, 0, 0.15);
  }

  .loader {
    position: absolute;
    top: 40%;
    left: 45%;
    z-index: 1100;
  }
</style>
<input type="hidden" name="cotizacion_id" id="cotizacion_id" value="{{isset($cotizacion) ? $cotizacion->id : ''}}">
<input type="hidden" name="instalacion_cliente_id" id="instalacion_cliente_id" value="{{isset($cotizacion) ? $cotizacion->instalacion_cliente : ''}}">
<div class="form-row">
  <div id="cotizacion-datos-comerciales" class="col-12 mb-5 componente-pasos">
    <div class="card" style="position:relative">
      <div class="text-center pasos-creacion-cotizacion" style="position: absolute;top: -32%;left: 86%;z-index: 1;display: flex;/* align-content: center; */align-items: center;">
        <p style="font-weight: bold;font-size: 16px;white-space: nowrap;margin-right: 5px; margin: 0px 5px 0px 0px;">Seleccionar Cliente</p>
        <img id="paso_uno" style="height: 30px;width: 30px;background-color: #fff;border-radius: 46px;" src="https://envases-ot.inveb.cl/img/uno.png">
      </div>
      <div class="card-header">Datos comerciales</div>
      <div class="card-body">
        <div class="row">
          <div class="col-3" style="">
            <!-- Clientes -->
            {!! armarSelectArrayCreateEditOTSeparado($clients, 'client_id', 'Cliente' , $errors, $cotizacion ,'form-control form-element',true,true) !!}

          </div>
          <div class="col-3" style="">
            <!-- Clientes -->
            {!! armarSelectArrayCreateEditOTSeparado([], 'instalacion_cliente', 'Instalación' , $errors, $cotizacion ,'form-control form-element',true,true) !!}

          </div>
          <div class="col-3" style="">

            <!-- Contactos Cliente -->
            <!-- //style="display:none" -->
            {!! armarSelectArrayCreateEditOTSeparado([], 'contactos_cliente', 'Contactos' , $errors, $cotizacion ,'form-control form-element',false,false) !!}

          </div>


          <div class="col-3 row">
            <div class="col-11" style="padding-right: 2px;padding-left:2px">
              <!-- Nombre Contacto -->
              {!! armarInputCreateEditCotiza('nombre_contacto', 'Nombre:', 'text', $errors, $cotizacion, 'form-control', '', '',null,"separado") !!}
            </div>
            <div class="col-1 " style="margin:0px;padding:0px">
              <div class="custom-control custom-checkbox mb-1 form-group" style="display: inline-block;">
                <input type="checkbox" class="custom-control-input" name="check_nombre_contacto" id="check_nombre_contacto" @if ((isset($cotizacion) && $cotizacion->check_nombre_contacto == 1) || !$cotizacion) checked @endif>
                <label class="custom-control-label" for="check_nombre_contacto">

                </label>
              </div>
              <div class="material-icons md-18 ml-1" data-toggle="tooltip" title="Al estar marcada esta opción, se adjuntara el nombre del contacto al encabezado del PDF de la cotización" style="color:#218838;align-items: center;    margin-left: -2px !important;">help_outline</div>
            </div>

          </div>
          <div class="col-3">

            <!-- Email Contacto -->
            {!! armarInputCreateEditCotiza('email_contacto', 'Email:', 'email', $errors, $cotizacion, 'form-control ', '', '',null,"unido") !!}
          </div>

          <div class="col-3">
            <!-- Teléfono Contacto -->
            {!! armarInputCreateEditCotiza('telefono_contacto', 'Teléfono:', 'text', $errors, $cotizacion, 'form-control', '', '',null,"unido") !!}
          </div>
          <div class="col-3">
            <!-- Clasificacion Cliente -->
            {!! armarSelectArrayCreateEditOTSeparado($clasificacion_clientes, 'clasificacion_cliente', 'Clasificacion' , $errors, $cotizacion ,'form-control form-element',true,true) !!}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="cotizacion-precotizaciones" class="col-12 mb-5 componente-pasos">
    <div class="card" style="position: relative;">
      <div class="text-center pasos-creacion-cotizacion" style="position: absolute;top: -18.5%;left: 87.5%;z-index: 1;display: flex;/* align-content: center; */align-items: center;">
        <p style="font-weight: bold;font-size: 16px;white-space: nowrap;margin-right: 5px; margin: 0px 5px 0px 0px;">Agregar Detalles</p>
        <img id="paso_dos" style="height: 30px;width: 30px;background-color: #fff;border-radius: 46px;" src="https://envases-ot.inveb.cl/img/dos.png">
      </div>
      <div class="card-header">Detalles </div>
      @if(!$cotizacion || ( $cotizacion->estado_id == 1 && auth()->user()->id == $cotizacion->user_id))
      <div class="" style="display: flex;    justify-content: flex-end;    margin-right: 15px;margin-top: 10px;">
        <a href="#" id="carga-masiva-detalles" class="btn btn-light float-right mr-3" style="display:flex;align-items:center;border-color: #28a745;" data-toggle="modal" data-target="#modal-carga-masiva-detalles">Carga Masiva <div class="material-icons md-14 ml-1" data-toggle="tooltip" title="Agregar Detalles Masivos" style="color:#218838;align-items: center;">insert_drive_file
          </div></a>
        <a href="#" id="crear_precotizacion" class="btn btn-success float-right" style="display:flex;align-items:center" data-toggle="modal" data-target="#modal-detalle-cotizacion">Crear Detalle <div class="material-icons md-14 ml-1" data-toggle="tooltip" title="Agregar Detalle" style="color:white;align-items: center;">add</div></a>
      </div>
      @endif
      <div class="card-body">
        <div class="row">
          <div class="col-12">
            <table id="listado-detalles" class="table table-status table-hover table-bordered ">
              <thead>
                <th width="40px">N°</th>
                <th>Descrip.</th>
                <th>CAD</th>
                <th>Tipo Producto</th>
                <th>Cantidad</th>
                <th style="width:50px">Área</th>
                <th>Cartón</th>
                <th>Item</th>
                <th>Proceso</th>
                <!-- <th>Pegado</th> -->
                <!-- <th>Golpes Ancho</th>
                <th>Golpes Largo</th> -->
                <th style="width:55px">Colores</th>
                <th style="width:55px">% Impr.</th>
                <th style="width:65px">%Cob.<div class="material-icons md-18 ml-1" data-toggle="tooltip" title="Valor obtenido de la suma del % interior mas el % exterior" style="color:#218838;align-items: center;    margin-left: -2px !important;">help_outline</div></th>
                <th style="width:50px">Matriz</th>
                <th style="width:50px">Clisse</th>
                <th style="width:60px">Royalty</th>
                <th style="width:60px">Maquila</th>
                <th style="width:60px">Armado</th>
                <th style="width:50px">OT</th>
                <th style="width:50px">
                  <div class="material-icons md-14" data-toggle="tooltip" title="Etiqueta">flag</div>
                </th>
                <th>Acciones</th>
              </thead>
              <tbody>

                <tr>
                  <td colspan="20"></td>
                </tr>
              </tbody>
            </table>

          </div>
          @if($cotizacion)
          <div class="col-12 mt-2 text-right">
            <div class=" text-right">
              <a class="btn btn-success" href="{{ route('cotizador.detalles_corrugados',['id'=>$cotizacion->id]) }}" download title="Descargar Detalles Corrugados">Descargar Detalles Corrugados
                <div class="material-icons md-14" data-toggle="tooltip" title="Descargar Detalles Corrugados" style="color:white;">insert_drive_file</div> </a>
              </a>
              <a class="btn btn-success" href="{{ route('cotizador.detalles_esquineros',['id'=>$cotizacion->id]) }}" download title="Descargar Detalles Esquineros">Descargar Detalles Esquineros
                <div class="material-icons md-14" data-toggle="tooltip" title="Descargar Detalles Esquineros" style="color:white;">insert_drive_file</div> </a>
              </a>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>




  <?php  /* 
  <div id="" class="col-12 mb-2">
    <div class="card">
      <div class="card-header">Destino</div>
      <div class="card-body">
        <div class="row">
          <div class="col-3">
            <!-- Lugar de Destino -->
            {!! armarSelectArrayCreateEditOT($flete, 'ciudad_id', 'Lugar de Destino' , $errors, $cotizacion ,'form-control form-element',true,true) !!}
          </div>
          <div class="col-3">
            <!-- Pallets Apilados -->
            {!! armarSelectArrayCreateEditOT([1=>1,2=>2], 'pallets_apilados', 'Pallets Apilados' , $errors, $cotizacion ,'form-control form-element',true,true) !!}
          </div>

          <div class="col-3">
            <!-- Comuna -->
            {!! armarSelectArrayCreateEditOT($flete, 'comuna_id', 'Comuna' , $errors, $cotizacion ,'form-control form-element',true,true) !!}

          </div>
        </div>
      </div>
    </div>
  </div>
*/ ?>
  <div id="" class="col-12 mb-2">
    <div class="card" style="position: relative;">
      <div class="text-center pasos-creacion-cotizacion" style="position: absolute;top: -30%;left: 84.5%;z-index: 1;display: flex;/* align-content: center; */align-items: center;">
        <p style="font-weight: bold;font-size: 16px;white-space: nowrap;margin-right: 5px; margin: 0px 5px 0px 0px;">Completar Cotización</p>
        <img id="paso_tres" style="height: 30px;width: 30px;background-color: #fff;border-radius: 46px;" src="https://envases-ot.inveb.cl/img/tres.png">
      </div>
      <div class="card-header">Moneda - Días Pago - % Comisión</div>
      <div class="card-body">
        <div class="row">
          <div class="col-3">
            <!-- Moneda -->
            {!! armarSelectArrayCreateEditOT([1=>"USD",2=>"CLP"], 'moneda_id', 'Moneda' , $errors, $cotizacion ,'form-control form-element',true,true) !!}
          </div>

          <div class="col-3">
            <!-- Días Pago -->
            {!! armarSelectArrayCreateEditOT($dias_financiamiento, 'dias_pago', 'Días Pago' , $errors, $cotizacion ,'form-control form-element',true,true) !!}
          </div>

          <div class="col-3">
            <!-- Comision -->
            {!! armarInputCreateEditOT('comision', 'Comision %:', 'number', $errors, $cotizacion, 'form-control autofill-value', 'min="0" max="99"', '') !!}
            <div style="font-weight: bold;margin-top: -15px;font-size: 12px;text-align: center;">*Solo para Exportacion</div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <div id="" class="col-6 mb-2">
    <div class="card h-100">
      <div class="card-header">Observación Interna</div>
      <div class="card-body">
        <div class="form-group form-row">
          <div class="col">
            <textarea class="{{$errors->has('observacion_interna') ? 'error' : ''}}" style="resize: none;border-color:#3aaa35;width:100%" name="observacion_interna" id="observacion_interna" rows="2">{{isset($cotizacion) ? $cotizacion->observacion_interna : null}}</textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="" class="col-6 mb-2">
    <div class="card h-100">
      <div class="card-header" style="background-color: #fbfbd5;">Observación Cliente</div>
      <div class="card-body" style="background-color: #fbfbd5;">
        <div class="form-group form-row">
          <div class="col">
            <textarea class="{{$errors->has('observacion_cliente') ? 'error' : ''}}" style="resize: none;border-color:#3aaa35;width:100%" name="observacion_cliente" id="observacion_cliente" rows="2">{{ isset($cotizacion) ?$cotizacion->observacion_cliente: null}}</textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 ">
    <h6 style="display: none;font-weight:bold;color:red" id="mensajeCalculoAHC">Recuerde que esta cotizacion contiene un Área Hoja Corrugada estimada, favor validar este dato con desarrollo</h6>
    <h6 style="display: none;font-weight:bold;color:red" id="mensajeCalculoCarton">Recuerde que esta cotizacion contiene un Cartón estimado, favor validar este dato con desarrollo</h6>
  </div>


</div>
@if((!$cotizacion || ( $cotizacion->estado_id == 1 && auth()->user()->id == $cotizacion->user_id || $cotizacion == null)))
<div style="position: relative;">
  <div class="text-center pasos-creacion-cotizacion" style="position: absolute;top: -30%;left: 86%;z-index: 1;display: flex;/* align-content: center; */align-items: center;">
    <p style="font-weight: bold;font-size: 16px;white-space: nowrap;margin-right: 5px; margin: 0px 5px 0px 0px;">Guardar Cotización</p>
    <img id="paso_cuatro" style="height: 30px;width: 30px;background-color: #fff;border-radius: 46px;" src="https://envases-ot.inveb.cl/img/cuatro.png">
  </div>
</div>
<div class=" mt-5 text-right componente-pasos-boton">
  <a href="{{ route('cotizador.index_cotizacion') }}" class="btn btn-light">Cancelar</a>
  <!-- <button type="submit" class="btn btn-success">{{ isset($areahc->id) ? __('Actualizar') : __('Guardar') }}</button> -->
  <button data-toggle="tooltip" title="Si ha editado o agregado un detalle recuerde Actualizar resultados" disabled id="generarPrecotizacion" type="submit" class="btn btn-success float-right">{{ isset($cotizacion->id) ? __('Actualizar Pre-Cotización') : __('Generar Pre-Cotización') }}</button>
  <!-- <button id="limpiarAreaHC" class="btn btn-light">Limpiar</button> -->
</div>

@endif
<div id="contenedor-resultados" style="display: none;">


  <h1 class="page-title">Resumen de Costos por producto</h1>
  <div class="row" id="resultados">

    <!-- RESULTADOS -->

    <div id="resultados-detalle" class="col-12 mb-2">
      <div class="card">
        <div class="card-header" style="background-color: #28a745;color: white;padding-bottom: 0.5rem">Parametros Por Producto
          <!-- <a href="#" id="crear_precotizacion" class="btn btn-success float-right" data-toggle="modal" data-target="#modal-detalle-cotizacion">Crear Detalle</a> -->
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <table id="listado-resultados-detalle" class="table table-status table-hover table-bordered ">
                <thead>
                  <th width="40px">N°</th>
                  <th>Descripción</th>
                  <th>CAD</th>
                  <th>Planta</th>
                  <th>Tipo Producto</th>
                  <th>Item</th>
                  <th>Cartón</th>
                  <th>Flete</th>
                  <th>Margen Papeles (USD/Mm2)</th>
                  <th>Margen (USD/Mm2)</th>
                  <th>Margen MÍNIMO (USD/Mm2)</th>
                  <th>Precio (USD/Mm2)</th>
                  <th>Precio (USD/Ton)</th>
                  <th>Precio (USD/UN)</th>
                  <th>Precio ($/UN)</th>
                  <th>Cantidad</th>
                  <th>Precio Total (MUSD)</th>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="15"></td>
                  </tr>
                </tbody>
              </table>
              <span style="color: #025902;">* En verde: Promedios ponderados por columna</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="" class="col-12 mb-2">
      <div class="card">
        <div class="card-header" style="background-color: #28a745;color: white;padding-bottom: 0.5rem">Nuevos Detalles Cotizacion
          <!-- <a href="#" id="crear_precotizacion" class="btn btn-success float-right" data-toggle="modal" data-target="#modal-detalle-cotizacion">Crear Detalle</a> -->
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <table id="listado-nuevos-detalle" class="table table-status table-hover table-bordered ">
                @if(auth()->user()->role_id == 3 || auth()->user()->role_id == 4)
                  <thead>
                    <th width="40px">N°</th>
                    <th>Descripción</th>
                    <th>CAD</th>
                    <th>Tipo Producto</th>
                    <th>Item</th>
                    <th>Cartón</th>
                    <th>MC (USD/Mm2)</th>
                    <th>Margen bruto sin flete (USD/Mm2)</th>
                    <th>Margen de servir (USD/Mm2)</th>
                    <th>Mg EBITDA (%)</th>
                  </thead>
                @else
                  <thead>
                    <th width="40px">N°</th>
                    <th>Descripción</th>
                    <th>CAD</th>
                    <th>Tipo Producto</th>
                    <th>Item</th>
                    <th>Cartón</th>
                    <th>Diferencia Margen Vendedor y Minimo</th>    
                    <th>MC (USD/Mm2)</th>
                    <th>Margen Bruto sin flete (USD/Mm2)</th>
                    <th>Margen bruto sin flete (%)</th>
                    <th>Margen de servir (USD/Mm2)</th>
                    <th>Margen de servir (%)</th>
                    <th>EBITDA (USD/Mm2)</th>
                    <th>Mg EBITDA (%)</th>
                  </thead>
                @endif
                <tbody>
                  <tr>
                    <td colspan="12"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="" class="col-12 mb-2">
      <div class="card">
        <div class="card-header" style="background-color: #28a745;color: white;padding-bottom: 0.5rem">Costos Productos (USD/MM2)
          <!-- <a href="#" id="crear_precotizacion" class="btn btn-success float-right" data-toggle="modal" data-target="#modal-detalle-cotizacion">Crear Detalle</a> -->
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <table id="listado-costos-detalle" class="table table-status table-hover table-bordered ">
                <thead>
                  <th width="40px">N°</th>
                  <th>Descripción</th>
                  <th>CAD</th>
                  <th>Tipo Producto</th>
                  <th>Item</th>
                  <th>Cartón</th>
                  <th>Costo Directo</th>
                  <th>Costo Indirecto</th>
                  <th>GVV</th>
                  <th>Costo Fijo</th>
                  <th>Costo Total</th>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="11"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="" class="col-12 mb-2">
      <div class="card">
        <div class="card-header" style="background-color: #28a745;color: white;padding-bottom: 0.5rem">Costos Servicios (USD/MM2)
          <!-- <a href="#" id="crear_precotizacion" class="btn btn-success float-right" data-toggle="modal" data-target="#modal-detalle-cotizacion">Crear Detalle</a> -->
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <table id="listado-costos-servicios-detalle" class="table table-status table-hover table-bordered ">
                <thead>
                  <th width="40px">N°</th>
                  <th>Descripción</th>
                  <th>CAD</th>
                  <th>Tipo Producto</th>
                  <th>Item</th>
                  <th>Cartón</th>
                  <th>Maquila</th>
                  <th>Armado</th>
                  <th>Clisses</th>
                  <th>Matriz</th>
                  <th>Mano de Obra</th>
                  <th>Flete</th>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="12"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

 

  </div>

  @if(isset($cotizacion) && auth()->user()->id == $cotizacion->user_id && (($cotizacion->estado_id == 1 ))|| empty($cotizacion))
  <div style="position: relative;">
    <div class="text-center pasos-creacion-cotizacion" style="position: absolute;top: -30%;left: 86%;z-index: 1;display: flex;/* align-content: center; */align-items: center;">
      <p style="font-weight: bold;font-size: 16px;white-space: nowrap;margin-right: 5px; margin: 0px 5px 0px 0px;">Finalizar Cotización</p>
      <img style="height: 30px;width: 30px;background-color: #fff;border-radius: 46px;" src="https://envases-ot.inveb.cl/img/cinco.png">
    </div>
  </div>
  <div class="text-right mt-5 componente-pasos-boton">
    <button id="solicitarAprobacion" class="btn btn-lg btn-success">Solicitar Aprobación</button>
  </div>
  @elseif( $cotizacion->estado_id == 2 && auth()->user()->id == $cotizacion->user_id)
  <div class="text-center">
    <h5>En espera de Aprobación</h5>

    <a href="{{ route('cotizador.retomarCotizacion', $cotizacion->id) }}" onclick="event.preventDefault();
                        $('#retomarCotizacionForm').submit();" class="btn btn-success">Retomar Cotización</a>
  </div>
  @elseif( auth()->user()->id == $cotizacion->user_id && $cotizacion->active == 1 & ($cotizacion->estado_id == 3||$cotizacion->estado_id == 4||$cotizacion->estado_id == 5 ))


  <div class="row">

    <div class="col-12 text-right">
      <a style="" class="btn btn-success" href="{{ route('cotizador.detalle_costos',['id'=>$cotizacion->id]) }}">
        Descargar Detalle de Costos
        <div class="material-icons md-14 ml-1" data-toggle="tooltip" title="Descargar Detalle Costos" style="color:white;">description</div>
      </a>
      <a style="" class="btn btn-success" target="_blank" href="{{ route('cotizador.generar_pdf',['download'=>'pdf','id'=>$cotizacion->id]) }}">
        Descargar Cotización PDF
        <div class="material-icons md-14 ml-1" data-toggle="tooltip" title="Agregar Detalles Masivos" style="color:white;">insert_drive_file</div>
      </a>
      <a href="#" class="btn btn-success" data-toggle="modal" data-target="#modal-enviar-pdf">
        Enviar PDF
        <div class="material-icons md-14" data-toggle="tooltip" title="Enviar PDF de cotizacion" style="color:white;">insert_drive_file</div> </a>
    </div>
  </div>

  @endif
</div>


<!-- Loading  -->
<div id="loading" style="display:none">
  <div id="modal-loader" class="loader">Loading...</div>
</div>