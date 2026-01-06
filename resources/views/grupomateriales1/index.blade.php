@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Mantenedor de Grupos Materiales 1
  <a href="{{ route('mantenedores.grupo-materiales-1.create') }}" class="btn btn-primary rounded-pill ml-3 px-5">Crear</a>
</h1>

<!-- Filtros -->
<form id="filtros" class="py-3" action="{{ route('mantenedores.grupo-materiales-1.list') }}" method="get" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <div class="col-sm-12 col-md-3 col-lg-3">
      <div class="form-group">
        <label>Código</label>
        <select name="id[]" id="id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($grupomateriales_filter,'id',['codigo','armado_desc'],'') !!}
        </select>
      </div>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-3">
      <div class="form-group">
        <label>Armado</label>
        <select name="armado_id[]" id="armado_id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($armado_filter,'id',['descripcion'],'') !!}
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
        <th> {!! order_column('Armado','armado_id','ASC') !!} </th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($grupomateriales as $grupomaterial)
      <tr class="{{ $grupomaterial->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $grupomaterial->codigo }}</td>
        <td>{{ $grupomaterial->armado->descripcion}}</td>
        <td>
          @if($grupomaterial->active == 1) Activo
          @else Inactivo
          @endif
        </td>
        <td>
          <a href="{{route('mantenedores.grupo-materiales-1.edit', $grupomaterial->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
          </a>
          @if($grupomaterial->active == 0 )
          <form method="POST" action="{{ route('mantenedores.grupo-materiales-1.active', $grupomaterial->id) }}" style="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit" data-toggle="tooltip" title="Activar">
              <div class="material-icons md-14">check_circle</div>
            </button>
          </form>
          @else
          <form method="POST" id="form_{{ $grupomaterial->id }}" action="{{ route('mantenedores.grupo-materiales-1.inactive', $grupomaterial->id) }}" style="display: inline;">
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
  {!! $grupomateriales->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>
@endsection