@extends('layouts.index', ['dontnotify' => true])


@section('content')
<a href="{{url()->previous()}}" class="btn btn-link px-0">&lsaquo; Volver</a>
<!-- solo si hay versiones anteriores -->
@if($cotizacion && $cotizacion->previous_version_id && auth()->user()->id == $cotizacion->user_id)
<div class="row">
  <div class="col">
    <h1 class="page-title" id="titulo-cotizacion">{{ isset($cotizacion->id) ? __('Cotización N° '.$cotizacion->id) : __('Ingreso Cotización') }}</h1>

  </div>
  @if($cotizacion)
  
  @endif
  @if($cotizacion && $cotizacion->estado_id >= 3 && auth()->user()->id == $cotizacion->user_id && $cotizacion->active == 1)
  <div class="col-2">
      <a href="{{ route('cotizador.versionarCotizacion', $cotizacion->id) }}" class="btn btn-success btn-block" onclick="event.preventDefault();
                        $('#versionarCotizacionForm').submit();">Versionar Cotización</a>
    </div>
  @endif
  <div class="col-2">
    <button class="btn btn-xs btn-success btn-block" style="background-color: #7942a0	;border-color:#4B0082" type='button' data-toggle="collapse" data-target="#versiones">Ver Versiones Anteriores
    </button>
  </div>

</div>

<!-- Tabla / Listado -->
<div id="versiones" class="container-table mt-3 bg-white border collapse">
  <table class="table table-status table-hover">
    <thead style="background-color: #c4f9c4;">
      <tr>
        <th>Cotización N°</th>
        <th>Fecha Creación </th>
        <th>Creador </th>
        <th>Estado</th>
        <th>N° Version</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>

      @foreach($cotizacion->versiones as $oldcotizacion)
      <tr>
        <td>{{ $oldcotizacion->id }}</td>
        <td>{{ $oldcotizacion->created_at }}</td>
        <td class="text-truncate" title="{{$oldcotizacion->user->fullname}}" data-toggle="tooltip">{{$oldcotizacion->user->fullname}}</td>
        <td>{{$oldcotizacion->estado->nombre}}</td>
        <td>{{$oldcotizacion->version_number}}
        </td>
        <td>
          <a target="_blank" href="{{route('cotizador.editar_cotizacion', $oldcotizacion->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Ver">search</div>
          </a>
        </td>
      </tr>
      @endforeach
      <tr>
        <td>{{ $cotizacion->parent->id }}</td>
        <td>{{ $cotizacion->parent->created_at }}</td>
        <td class="text-truncate" title="{{$cotizacion->parent->user->fullname}}" data-toggle="tooltip">{{$cotizacion->parent->user->fullname}}</td>
        <td>{{$cotizacion->parent->estado->nombre}}</td>
        <td>{{$cotizacion->parent->version_number}}
        </td>
        <td>
          <a target="_blank" href="{{route('cotizador.editar_cotizacion', $cotizacion->parent->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Ver">search</div>
          </a>
        </td>
      </tr>
    </tbody>
  </table>
</div>
@elseif($cotizacion && (Auth()->user()->isVendedor() || Auth()->user()->isJefeVenta()))
<div class="row">
  <div class="col">
    <h1 class="page-title" id="titulo-cotizacion">{{ isset($cotizacion->id) ? __('Cotización N° '.$cotizacion->id) : __('Ingreso Cotización') }}</h1>

  </div>
  
  @if($cotizacion && $cotizacion->estado_id >= 3  && auth()->user()->id == $cotizacion->user_id && $cotizacion->active == 1)
  <div class="col-2">
      <a href="{{ route('cotizador.versionarCotizacion', $cotizacion->id) }}" class="btn btn-success btn-block" onclick="event.preventDefault();
                        $('#versionarCotizacionForm').submit();">Versionar Cotización</a>
    </div>
  @endif
</div> 
@else
<h1 class="page-title" id="titulo-cotizacion">{{ isset($cotizacion->id) ? __('Cotización N° '.$cotizacion->id) : __('Ingreso Cotización') }}</h1>
@endif
<div class="row mb-3">
  <div class="col-12 p-2">
    <form id="formCotizacion" method="POST" action="{{ route('cotizador.crear_areahc') }}">
      @csrf
      @include('cotizador.cotizaciones.form_aprobar_externo', ['tipo' => "create",'cotizacion' => $cotizacion,'class' => '',])
    </form> 
    <!-- Modulo de aprobacion  -->
    @include('partials/aprobacion-cotizaciones-externo', ['cotizacion'=>$cotizacion])
    <!-- MODAL DETALLE DE COTIZACION -->
    @include('partials/modal-detalle-cotizacion', ['cotizacion'=>$cotizacion ])
    @include('partials/modal-carga-masiva-detalles', ['cotizacion'=>$cotizacion])
    @include('partials/modal-carga-material', ['cotizacion'=>$cotizacion])
    @include('partials/modal-calculo-hc', ['cotizacion'=>$cotizacion,'areahc'=>null])
  </div>
  <input type="hidden" id="es_provisional" name="es_provisional" value="{{$es_provisional}}">
  <input type="hidden" id="carton_original_codigo" name="carton_original_codigo" value="{{$carton_original_codigo}}">
  <input type="hidden" id="carton_original_id" name="carton_original_id" value="{{$carton_original_id}}">
</div>

<!-- MODAL ELIMNAR DETALLE -->
<div class="modal fade" id="modal-eliminar-detalle">
  <div class="modal-dialog modal-lg " style="width:60%">
    <div class="modal-content modal-confirmacion">
      <div class="modal-header text-center">
        <div class="title">Confirmar Eliminación de Detalle</div>
      </div>
      <div class="modal-body">
        <h6>Una vez confirmado se eliminara el detalle correspondiente, esta opción es definitiva.
        </h6>
        <div class=" mt-4 text-center">
          <button class="btn btn-light" data-dismiss="modal">Cancelar</button>
          <button type="submit" form="form-cad-material" id="botonEliminarDetalle" data-id="" class="btn btn-success mx-2">Continuar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL OBSERVACION CLIENTE -->
<div class="modal fade" id="modal-observacion-cliente">
  <div class="modal-dialog modal-lg " style="width:60%">
    <div class="modal-content modal-confirmacion">
      <div class="modal-header text-center">
        <div class="title">Confirmar Observacion</div>
      </div>
      <div class="modal-body">
        <h6 class="text-center">Esta observacion podra ser visualizada por el cliente <br>
          ¿Confirma el texto ingresado?
        </h6>
        <p class="text-center" id="observacion-cliente-modal" style="font-weight: bold;"></p>
        <div class=" mt-4 text-center">
          <button id="eliminarObservacion" class="btn btn-light">Cancelar</button>
          <button id="confirmarObservacion" class="btn btn-success mx-2">Confirmo</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- MODAL CONFIRMACION CALCULO HC -->
<div class="modal fade" id="modal-confirmacion-calculo-hc">
  <div class="modal-dialog modal-lg " style="width:60%">
    <div class="modal-content modal-confirmacion">
      <!-- <div class="modal-header text-center">
        <div class="title">Confirmar Observacion</div>
      </div> -->
      <div class="modal-body">
        <h6 class="text-center" id="texto-calculo-hc">Área Hoja Corrugada estimada, favor validar este dato con desarrollo</h6>
        <div class=" mt-4 text-center">
          <button data-dismiss="modal" class="btn btn-light">Cancelar</button>
          <button id="confirmarCalculoHC" class="btn btn-success mx-2">Confirmo</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- MODAL duplicar cotizacion -->
<div class="modal fade" id="modal-duplicar-cotizacion">
  <div class="modal-dialog modal-lg " style="width:60%">
    <div class="modal-content modal-confirmacion">
      <div class="modal-header text-center">
        <div class="title">Duplicar Cotización</div>
      </div>
      <div class="modal-body">
        <h6 class="text-center">Duplicar una cotización no genera una nueva version de esta, si no una nueva cotizacion en base a la misma información
        </h6>
        <div class=" mt-4 text-center">
          <button data-dismiss="modal" class="btn btn-light">Cancelar</button>
          <button id="confirmarDuplicarCotizacion" class="btn btn-success mx-2">Confirmo</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL ENVIAR DETALLE A OT -->
<div class="modal fade" id="modal-detalle-a-ot">
  <div class="modal-dialog modal-md " style="width:100%">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title page-title">
          <h1 class="page-title">Enviar Detalle a Órden de Trabajo</h1>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form-codigo-material" method="POST" action="{{ route('cotizador.detalleAOt') }}" class="form-row form-codigo-material">
          @csrf
          <div class="container-cad" style="width:100%">
            <div class="item-cad" style="justify-content: space-between;
    align-content: start;">
              <input type="text" hidden id="detalle_id" name="detalle_id" value="">
              <!-- Tipo de Solicitud -->
              {!! armarSelectArrayCreateEditOT([1 => "Desarrollo Completo", 5 => "Arte con Material"], 'tipo_solicitud', 'Tipo de solicitud:' , $errors, null ,'form-control',true,false) !!}

            </div>

          </div>
          <div class="mt-3 text-right pull-right" style="width: 100%;">
            <a data-dismiss="modal" class="btn btn-light" style="cursor: pointer;">Cancelar</a>
            <button type="submit" id="duplicarOT" class="btn btn-success mx-2" disabled>Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- MODAL ENVIAR PDF VIA EMAIL -->
<div class="modal fade" id="modal-enviar-pdf">
  <div class="modal-dialog modal-md" style="width:100%">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title page-title">
          <h1 class="page-title">Enviar PDF de Cotización</h1>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form-envio-pdf" method="POST" action="{{ route('cotizador.enviar_pdf') }}" class="form-row ">
          @csrf
          <input type="text" hidden id="pdf_cotizacion_id" name="pdf_cotizacion_id" value="{{isset($cotizacion) ? $cotizacion->id : null}}">
          <div class="col-12">
            <!-- Correo Electrónico -->
            {!! armarInputCreateEditOT('email', 'Correo Electrónico:', 'email',$errors, null, 'form-control ', 'required', '') !!}
          </div>
          <div class="col-12">
            <!-- Nombre -->
            {!! armarInputCreateEditOT('nombre', 'Nombre:', 'text',$errors, null, 'form-control ', 'required', '') !!}
          </div>
          <div class="mt-3 text-right pull-right" style="width: 100%;">
            <a data-dismiss="modal" class="btn btn-light" style="cursor: pointer;">Cancelar</a>
            <button type="submit" id="enviar-pdf" class="btn btn-success mx-2">Enviar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Codigo para poder versionar cotizacion -->
@if($cotizacion)
<form id="versionarCotizacionForm" action="{{ route('cotizador.versionarCotizacion', $cotizacion->id) }}" method="POST" style="display: none;">
  @csrf
</form>
<form id="duplicarCotizacionForm" action="{{ route('cotizador.duplicarCotizacion', $cotizacion->id) }}" method="POST" style="display: none;">
  @csrf
</form>
<form id="retomarCotizacionForm" action="{{ route('cotizador.retomarCotizacion', $cotizacion->id) }}" method="POST" style="display: none;">
  @csrf
</form>
<form id="editarCotizacionForm" action="{{ route('cotizador.editarCotizacionExterno', $cotizacion->id) }}" method="POST" style="display: none;">
  @csrf
</form>
@endif
@endsection
@section('myjsfile')

<script>
  window.detalles_cotizaciones = @json(isset($cotizacion) ? $cotizacion->detalles : []) || []
  window.ot = @json(isset($ot) ? $ot : null) || null;
  window.impresion = @json(isset($impresion) ? $impresion : null) || null;
  cartones_offset = @json(isset($cartones_offset) ? $cartones_offset : []) || []
  cartones_alta_grafica = @json(isset($cartones_alta_grafica) ? $cartones_alta_grafica : []) || []
  window.user = {!!auth()->user() !!}
  window.maquilaServicios =  @json(isset($maquilaServicios) ? $maquilaServicios : null) || null;
  console.log(window.detalles_cotizaciones);
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  if (window.detalles_cotizaciones.length > 0) {

    $(".componente-pasos").removeClass("mb-5").addClass("mb-2");
    $(".componente-pasos-boton").removeClass("mt-5").addClass("mt-2");
    $(".pasos-creacion-cotizacion").hide();
  }

  $('#modal-eliminar-detalle').on('show.bs.modal', function(e) {
    $('#botonEliminarDetalle').data('id', $(e.relatedTarget).data('id'));
  });

  $('#botonEliminarDetalle').on("click", function(e) {
    let detalle_id = $(this).data('id');
    return $.ajax({
      type: "POST",
      url: "/cotizador/eliminarDetalleCotizacion",
      data: "detalle_id=" + detalle_id,
      success: function(data) {
        console.log(data);
        $('#modal-eliminar-detalle').modal('hide');
        notify("Detalle Eliminado Exitosamente", "success");
        var indice_detalle = window.detalles_cotizaciones.findIndex(
          (detalle) => detalle.id === detalle_id
        );
        if (indice_detalle > -1) {
          window.detalles_cotizaciones.splice(indice_detalle, 1);
        }
        if (window.detalles_cotizaciones.length < 1) {
          botonGenerarPrecotizacion.prop("disabled", true);
          contenedorResultados.hide();
        }
        renderTable();
      },
    });
  })
  $(document).on('hidden.bs.modal', '.modal', function() {
    $('.modal:visible').length && $(document.body).addClass('modal-open');
  });

  $("#tipo_solicitud").change(() => {
      // Si esta vacio
      if (jQuery.isEmptyObject($("#tipo_solicitud").val())) {
        $("#duplicarOT").prop("disabled", true);
      } else {
        $("#duplicarOT").prop("disabled", false);
      }
    })
    .triggerHandler("change");

  $("#modal-detalle-a-ot").on("show.bs.modal", function(e) {
    let btn = $(e.relatedTarget); // e.related here is the element that opened the modal, specifically the row button
    let detalle_cotizacion_id = btn.data("id"); // this is how you get the of any `data` attribute of an element
    $("#detalle_id").val(detalle_cotizacion_id);
    console.log(detalle_cotizacion_id);
  });
</script>
<script>
  const url_location = window.location
  var url = new URL(url_location);
  const dids = url.searchParams.get("dids");
  if (!!dids) {
    
    $("#generarPrecotizacion").prop("disabled", false);
    $.ajax({
        type: "POST",
        url: "/cotizador/obtieneDatos",
        dataType: "json",
        data: {'ids': dids},
        success: function (data) {
          window.detalles_cotizaciones = data.datos;
          window.detalles_cotizaciones.forEach((element) => {
            element.cantidad = '';
          });
          renderTable();
        },
    });
  }
</script>
<script src="{{ asset('js/cotizador/detalle-cotizacion-validation.js') }}"></script>
<script src="{{ asset('js/cotizador/detalle-cotizacion.js?'.date('ymdhis'))}}"></script>
<script src="{{ asset('js/cotizador/cotizacion-validation.js') }}"></script>
<script src="{{ asset('js/cotizador/cotizacion.js') }}"></script>
<script src="{{ asset('js/cotizador/carga-masiva-detalles.js') }}"></script>
<script src="{{ asset('js/cotizador/carga-material-detalles.js') }}"></script>
<script src="{{ asset('js/cotizador/cotizacion-from-ot.js') }}"></script>
<script src="{{ asset('js/cotizador/areaHC.js') }}"></script>
<script src="{{ asset('js/cotizador/areahc-validation.js') }}"></script>

@endsection