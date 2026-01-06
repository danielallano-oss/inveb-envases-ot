@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Mantenedor de Cantidad Base
  <a href="{{ route('mantenedores.cantidad-base.create') }}" class="btn btn-primary rounded-pill ml-3 px-5">Crear</a>
</h1>

<!-- Filtros -->
<form id="filtros" class="py-3" action="{{ route('mantenedores.cantidad-base.list') }}" method="get" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    
    <div class="col-sm-12 col-md-3 col-lg-3">
      <div class="form-group">
        <label>Proceso</label>
        <select name="proceso_id[]" id="proceso_id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($procesos_filter,'id',['descripcion','type'],'') !!}
        </select>
      </div>
    </div>
    <div class="col-2">
      <div class="form-group">
        <label for="">Estado</label>
        <select name="active[]" id="active" class="form-control form-control-sm" multiple data-live-search="false" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectArrayfilterMultiple([1=>'Activo', 0=>'Inactivo'],'active') !!}
        </select>
      </div>
    </div>
  </div>
  <div class="text-right">
    <button class="ml-auto btn btn-primary">Filtrar</button>
  </div>
</form>

<!-- Tabla / Listado -->
<div class="container-table mt-3 bg-white border px-2">
  <table class="table table-status table-hover actions states">
    <thead>
      <tr>
        <th> {!! order_column('Cantidad Buin','cantidad_buin','ASC') !!} </th>
        <th> {!! order_column('Cantidad Tiltil','cantidad_tiltil','ASC') !!} </th>
        <th> {!! order_column('Cantidad Osorno','cantidad_osorno','ASC') !!} </th>
        <th> {!! order_column('Proceso','proceso_id','ASC') !!} </th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($cantidadbase as $cantidad)
      <tr class="{{ $cantidad->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $cantidad->cantidad_buin }}</td>
        <td>{{ $cantidad->cantidad_tiltil}}</td>
        <td>{{ $cantidad->cantidad_osorno}}</td>
        <td>{{ $cantidad->proceso->descripcion}}</td>
        <td>
          @if($cantidad->active == 1) Activo
          @else Inactivo
          @endif
        </td>
        <td>
          <a href="{{route('mantenedores.cantidad-base.edit', $cantidad->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
          </a>
          @if($cantidad->active == 0 )
          <form method="POST" action="{{ route('mantenedores.cantidad-base.active', $cantidad->id) }}" style="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit" data-toggle="tooltip" title="Activar">
              <div class="material-icons md-14">check_circle</div>
            </button>
          </form>
          @else
          <form method="POST" id="form_{{ $cantidad->id }}" action="{{ route('mantenedores.cantidad-base.inactive', $cantidad->id) }}" style="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit" data-toggle="tooltip" title="Inactivar">
              <div class="material-icons md-14">remove_circle</div>
            </button>
          </form>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<!-- Paginacion -->
<nav class="mt-3">
  {!! $cantidadbase->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>
@endsection