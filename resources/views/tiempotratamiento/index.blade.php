@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Mantenedor de Tiempo Tratamiento
  <a href="{{ route('mantenedores.tiempo-tratamiento.create') }}" class="btn btn-primary rounded-pill ml-3 px-5">Crear</a>
</h1>

<!-- Filtros -->
<form id="filtros" class="py-3" action="{{ route('mantenedores.tiempo-tratamiento.list') }}" method="get" enctype="multipart/form-data">
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
        <th> {!! order_column('Buin','tiempo_buin','ASC') !!} </th>
        <th> {!! order_column('Tiltil','tiempo_tiltil','ASC') !!} </th>
        <th> {!! order_column('Osorno','tiempo_osorno','ASC') !!} </th>
        <th> {!! order_column('Buin Powerply','tiempo_buin_powerply','ASC') !!} </th>
        <th> {!! order_column('Buin CC Doble','tiempo_buin_cc_doble','ASC') !!} </th>
        <th> {!! order_column('Proceso','proceso_id','ASC') !!} </th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($tiempotratamiento as $tiempo)
      <tr class="{{ $tiempo->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $tiempo->tiempo_buin }}</td>
        <td>{{ $tiempo->tiempo_tiltil}}</td>
        <td>{{ $tiempo->tiempo_osorno}}</td>
        <td>{{ $tiempo->tiempo_buin_powerply}}</td>
        <td>{{ $tiempo->tiempo_buin_cc_doble}}</td>
        <td>{{ $tiempo->proceso->descripcion}}</td>
        <td>
          @if($tiempo->active == 1) Activo
          @else Inactivo
          @endif
        </td>
        <td>
          <a href="{{route('mantenedores.tiempo-tratamiento.edit', $tiempo->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
          </a>
          @if($tiempo->active == 0 )
          <form method="POST" action="{{ route('mantenedores.tiempo-tratamiento.active', $tiempo->id) }}" style="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit" data-toggle="tooltip" title="Activar">
              <div class="material-icons md-14">check_circle</div>
            </button>
          </form>
          @else
          <form method="POST" id="form_{{ $tiempo->id }}" action="{{ route('mantenedores.tiempo-tratamiento.inactive', $tiempo->id) }}" style="display: inline;">
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
  {!! $tiempotratamiento->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>
@endsection