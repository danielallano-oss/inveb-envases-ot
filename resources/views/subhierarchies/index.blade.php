@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Mantenedor de Jerarquía 2
  <a href="{{ route('mantenedores.subhierarchies.create') }}" class="btn btn-primary rounded-pill ml-3 px-5">Crear</a>
</h1>

<!-- Filtros -->
<form id="filtros" class="py-3" action="{{ route('mantenedores.subhierarchies.list') }}" method="get" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <div class="col-2">
      <div class="form-group">
        <label>Descripción</label>
        <select name="id[]" id="id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($subhierarchies_filter,'id',['descripcion'],'') !!}
        </select>
      </div>
    </div>
    <div class="col-2">
        <div class="form-group">
          <label>Jerarquía 1</label>
          <select name="hierarchy_id[]" id="id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
            {!! optionsSelectObjetfilterMultiple($hierarchies_filter,'id',['descripcion'],'') !!}
          </select>
        </div>
      </div>
    <div class="col-sm-12 col-md-3 col-lg-3">
      <div class="form-group">
        <label>Estado</label>
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
        <th> {!! order_column('Descripción','descripcion','ASC') !!} </th>
        <th>Jerarquía 1</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($subhierarchies as $subhierarchy)
      <tr class="{{ $subhierarchy->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $subhierarchy->descripcion}}</td>
        <td>{{ $subhierarchy->hierarchy->descripcion}}</td>
        <td>
          @if($subhierarchy->active == 1) Activo
          @else Inactivo
          @endif
        </td>
        <td>
          <a href="{{route('mantenedores.subhierarchies.edit', $subhierarchy->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
          </a>
          @if($subhierarchy->active == 0 )
          <form method="POST" action="{{ route('mantenedores.subhierarchies.active', $subhierarchy->id) }}" style="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit" data-toggle="tooltip" title="Activar">
              <div class="material-icons md-14">check_circle</div>
            </button>
          </form>
          @else
          <form method="POST" id="form_{{ $subhierarchy->id }}" action="{{ route('mantenedores.subhierarchies.inactive', $subhierarchy->id) }}" style="display: inline;">
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
  {!! $subhierarchies->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>
@endsection
