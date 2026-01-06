@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Aprobaciones</h1>

<!-- Filtros -->
<form id="filtros" class="py-3" action="{{ route('cotizador.aprobaciones') }}" method="get" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <div class="col-2">
      <div class="form-group">
        <label for="">Creador</label>
        <select name="id[]" id="id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($creadores,'id',['rut','nombre','apellido'],'') !!}
        </select>
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
        <label>Cliente</label>
        <select name="client_id[]" id="client_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($clients,'client_id',['nombre','apellido'],' ') !!}
        </select>
      </div>
    </div>
  </div>
  <div class="text-right">
    <button class="ml-auto btn btn-primary">Filtrar Cotizacion</button>
  </div>
</form>

<!-- Tabla / Listado -->
<div class="container-table mt-3 bg-white border px-2">
  <table class="table table-status table-hover text-center">
    <thead>
      <tr>
        <th width="100px">Cotizacion N°</th>
        <th width="100px">N° de Productos</th>
        <th>Creador </th>
        <th>Cliente</th>
        <th>Monto Total (MUSD)</th>
        <th>Fecha Creación 1ra Ver.</th>
        <th>Fecha Creación Ult. Ver.</th>
        <th width="80px">N° Versión</th>
        <th width="100px">Estado</th>
        <th width="80px">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($cotizaciones as $cotizacion)
      <tr class="{{ $cotizacion->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $cotizacion->id }}</td>
        <td>{{count($cotizacion->detalles)}}</td>
        <td class="text-truncate" title="{{$cotizacion->user->fullname}}" data-toggle="tooltip">{{$cotizacion->user->fullname}}</td>
        <td class="text-truncate" title="{{$cotizacion->client->nombreSap}}" data-toggle="tooltip">{{$cotizacion->client->nombreSap}}</td>
        <td>{{ number_format_unlimited_precision(round($cotizacion->monto_total[0]/1000,3))}}</td>
        <td>{{ $cotizacion->parent ? $cotizacion->parent->created_at :  $cotizacion->created_at }}</td>
        <td>{{ $cotizacion->created_at }}</td>
        <td>{{ $cotizacion->version_number }}</td>
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
          <a href="{{route('cotizador.editar_cotizacion', $cotizacion->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Gestionar">edit</div>
          </a>
        </td>
      </tr>
      @endforeach

      @if($cotizaciones->count() < 1) <tr>
        <td colspan="7"> No hay cotizaciones para aprobar actualmente</td>
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