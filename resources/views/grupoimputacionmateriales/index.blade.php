@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Mantenedor de Grupo Imputación Material y Familia
  <a href="{{ route('mantenedores.grupo-imputacion-material.create') }}" class="btn btn-primary rounded-pill ml-3 px-5">Crear</a>
</h1>

<!-- Filtros -->
<form id="filtros" class="py-3" action="{{ route('mantenedores.grupo-imputacion-material.list') }}" method="get" enctype="multipart/form-data">
  @csrf
  <div class="form-row">

    <div class="col-sm-12 col-md-3 col-lg-3">
      <div class="form-group">
        <label>Código</label>
        <select name="id[]" id="id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($grupoimputacionmaterial_filter,'id',['codigo','familia'],'') !!}
        </select>
      </div>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-3">
      <div class="form-group">
        <label>Proceso</label>
        <select name="proceso_id[]" id="proceso_id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($procesos_filter,'descripcion',['descripcion'],'') !!}
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
        <th> {!! order_column('Código','codigo','ASC') !!} </th>
        <th> {!! order_column('Proceso','proceso','ASC') !!} </th>
        <th> {!! order_column('Familia','familia','ASC') !!} </th>
        <th> {!! order_column('Material Modelo','material_modelo','ASC') !!} </th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($grupoimputacionmateriales as $grupoimputacionmaterial)
      <tr class="{{ $grupoimputacionmaterial->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $grupoimputacionmaterial->codigo }}</td>
        <td>{{ $grupoimputacionmaterial->proceso}}</td>
        <td>{{ $grupoimputacionmaterial->familia}}</td>
        <td>{{ $grupoimputacionmaterial->material_modelo}}</td>
        <td>
          @if($grupoimputacionmaterial->active == 1) Activo
          @else Inactivo
          @endif
        </td>
        <td>
          <a href="{{route('mantenedores.grupo-imputacion-material.edit', $grupoimputacionmaterial->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
          </a>
          @if($grupoimputacionmaterial->active == 0 )
          <form method="POST" action="{{ route('mantenedores.grupo-imputacion-material.active', $grupoimputacionmaterial->id) }}" style="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit" data-toggle="tooltip" title="Activar">
              <div class="material-icons md-14">check_circle</div>
            </button>
          </form>
          @else
          <form method="POST" id="form_{{ $grupoimputacionmaterial->id }}" action="{{ route('mantenedores.grupo-imputacion-material.inactive', $grupoimputacionmaterial->id) }}" style="display: inline;">
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
  {!! $grupoimputacionmateriales->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>
@endsection
