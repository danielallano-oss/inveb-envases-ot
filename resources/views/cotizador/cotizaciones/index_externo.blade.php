@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Cotizaciones
  
  <a href="{{ route('cotizador.crear_cotizacion_externo') }}" class="btn btn-primary rounded-pill ml-3 px-5">Crear Cotización</a>
  
 
</h1>

<!-- Filtros -->
<form id="filtros" class="py-3" action="{{ route('cotizador.index_cotizacion') }}" method="get" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <div class="col-1">
      <div class="form-group">
        <label>Desde</label>
        <input class="form-control form-control-sm datepicker" id="datePicker1" name="date_desde" value="{{ (is_null(app('request')->input('date_desde')))? $fromDate : app('request')->input('date_desde') }}" autocomplete="off">
      </div>
    </div>
    <div class="col-1">
      <div class="form-group">
        <label>Hasta</label>
        <input class="form-control form-control-sm datepicker" id="datePicker2" name="date_hasta" value="{{ (is_null(app('request')->input('date_hasta')))? $toDate : app('request')->input('date_hasta') }}" autocomplete="off">
      </div>
    </div>
    <div class="col-2">
      <div class="form-group">
        <label>N° de Cotizacion</label>
        <input class="form-control form-control-sm " type="text" name="cotizacion_id" id="cotizacion_id" value="{{ (is_null(app('request')->input('cotizacion_id')))? '' : app('request')->input('cotizacion_id') }}">
      </div>
    </div>

    
    <div class="col-2">
      <div class="form-group">
        <label>Estado</label>
        <select name="estado_id[]" id="estado_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($estados,'estado_id',['nombre'],' ') !!}
        </select>
      </div>
    </div>
    
    <div class="col-2">
      <div class="form-group">
        <label>CAD</label>
        <select name="cad_material_id[]" id="cad_material_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($cads,'cad_material_id',['cad'],' ') !!}
        </select>
      </div>
    </div>
  </div>
  <div class="text-right">
    <button class="ml-auto btn btn-primary">Filtrar Cotización</button>
  </div>
</form>

<!-- Tabla / Listado -->
<div class="container-table mt-3 bg-white border px-2">
  <table class="table table-status table-hover text-center">
    <thead>
      <tr>
        <th width="100px">Cotizacion N°</th>
        <th width="130px">N° de Productos</th>
        <!-- <th width="100px"></th> -->
        <th>Creador </th>
        <th>Cliente</th>
        <th>Fecha Creación 1ra Ver.</th>
        <th>Fecha Creación Ult. Ver.</th>
        <th>Descrip.</th>
        <th>CAD</th>
        <th>OT</th>
        <th width="80px">N° Versión</th>
        <!-- <th width="60px">Observación</th> -->
        <th width="100px">Estado</th>
        <th width="80px">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($cotizaciones as $cotizacion)
        <tr class="{{ $cotizacion->active == 1 ? '' : 'text-muted' }}">
          <td>{{ $cotizacion->id }}</td>
          <!-- <td>{{count($cotizacion->detalles)}}</td> -->
          <td style="display: flex;align-items: center;justify-content: center;">
            <div style="color:grey" class="material-icons md-14" data-toggle="tooltip" title="Total Detalles">receipt_long</div><span style="padding: 2px;display: inline-block;">{{count($cotizacion->detalles)}}</span> &nbsp;&nbsp;<div style="@if($cotizacion->estado_id < 3) opacity:0.15;@endif " class="material-icons md-14" data-toggle="tooltip" title="Detalles Ganados">thumb_up</div><span style="padding: 2px;display: inline-block;@if($cotizacion->estado_id < 3) opacity:0.15;@endif ">{{count($cotizacion->detalles_ganados)}}</span> &nbsp;&nbsp; <div style="color:red;@if($cotizacion->estado_id < 3) opacity:0.15;@endif " class="material-icons md-14" data-toggle="tooltip" title="Detalles Perdidos">thumb_down</div><span style="padding: 2px;display: inline-block;@if($cotizacion->estado_id < 3) opacity:0.15;@endif ">{{count($cotizacion->detalles_perdidos)}}</span>
          </td>
          <td class="text-truncate" title="{{$cotizacion->user->fullname}}" data-toggle="tooltip">{{$cotizacion->user->fullname}}</td>
          <td class="text-truncate" title="{{$cotizacion->client->nombreSap}}" data-toggle="tooltip">{{$cotizacion->client->nombreSap}}</td>
          <td>{{ $cotizacion->parent ? $cotizacion->parent->created_at :  $cotizacion->created_at }}</td>
          <td>{{ $cotizacion->created_at }}</td>
          <!-- -------- Mostramos solo los datos del primer detalle de la cotizacion -------- -->
          <td>{{ count($cotizacion->detalles) > 0 ? $cotizacion->detalles[0]->descripcion_material_detalle : '-'}}</td>
          <td>{{ count($cotizacion->detalles) > 0 ? $cotizacion->detalles[0]->cad_material_detalle : '-'}}</td>
          <td>{{ count($cotizacion->detalles) > 0 ? $cotizacion->detalles[0]->work_order_id : '-'}}</td>
          <td>{{ $cotizacion->version_number }}</td>
          <!-- <td>
            @if(isset($cotizacion->observacion_interna) && $cotizacion->observacion_interna != '')
            <div class="material-icons md-14" title="{{isset($cotizacion->observacion_interna) && $cotizacion->observacion_interna != '' ? $cotizacion->observacion_interna : null}}" data-toggle="tooltip">assignment</div>
            @endif
          </td> -->

          <td>
            @if(($cotizacion->estado_id == 3) || ($cotizacion->estado_id == 4))
            <div class="badge badge-success" style="font-size: 100%">{{$cotizacion->estado->nombre}}</div>
            @elseif(($cotizacion->estado_id == 1) || ($cotizacion->estado_id == 2))
            <div class="badge badge-warning" style="font-size: 100%">{{$cotizacion->estado->nombre}}</div>
            @else
            <div class="badge badge-danger" style="font-size: 100%">{{$cotizacion->estado->nombre}}</div>
            @endif
          </td>
          <td>

            
            @if($cotizacion->estado_id == 3)
            <a href="{{route('cotizador.editar_cotizacion_externo', $cotizacion->id)}}">
              <div class="material-icons md-14" data-toggle="tooltip" title="Ver">search</div>
            </a>
            @else
            <a href="{{route('cotizador.editar_cotizacion_externo', $cotizacion->id)}}">
              <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
            </a>
            @endif
            @if($cotizacion->estado_id == 3)

            <a style="" class="" target="_blank" href="{{ route('cotizador.generar_pdf',['download'=>'pdf','id'=>$cotizacion->id]) }}">
              <div class="material-icons md-14 ml-1" data-toggle="tooltip" title="Descargar PDF Cotización" style="">insert_drive_file</div>
            </a>
            
            @endif
          </td>
        </tr>
      @endforeach

      @if($cotizaciones->count() < 1) <tr>
        <td colspan="12"> No hay cotizaciones actualmente</td>
        </tr>
        @endif
    </tbody>
  </table>
</div>

<!-- Paginacion -->
<nav class="mt-3">
  {!! $cotizaciones->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>

@endsection
@section('myjsfile')
<script>
  const notify = (msg = "Complete los campos faltantes", type = "danger") => {
    $.notify({
      message: `<p  class="text-center">${msg}</p> `,
    }, {
      type,
      animate: {
        enter: "animated bounceInDown",
        exit: "animated bounceOutUp",
      },
      // delay: 500000,
      placement: {
        from: "top",
        align: "center",
      },
      z_index: 999999,
    });
  };

  if (window.location.hash == '#aprobado') {
    notify("Cotizacion Liberada", "success");
  }
  if (window.location.hash == '#poraprobar') {
    notify("Cotizacion En Espera de Aprobación", "success");
  }
</script>

<!-- Funcion para vaciar select de OT y CAD -->
@if(is_null(request()->input('work_order_id')))
<script>
	$(function() {
		$("#work_order_id").val('').selectpicker("refresh")
	});
</script>
@endif

@if(is_null(request()->input('cad_material_id')))
<script>
	$(function() {
		$("#cad_material_id").val('').selectpicker("refresh")
	});
</script>
@endif


@endsection