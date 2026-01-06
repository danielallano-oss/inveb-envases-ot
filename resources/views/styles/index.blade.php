@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Mantenedor de Estilos
  <a href="{{ route('mantenedores.styles.create') }}" class="btn btn-primary rounded-pill ml-3 px-5">Crear</a>
</h1>

<!-- Filtros -->
<form class="filter-tool" action="{{ route('mantenedores.styles.list') }}" method="get" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <div class="col-2">
      <div class="form-group">
        <label>Código</label>
        <select name="id[]" id="id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($styles_filter,'id',['codigo','glosa'],'') !!}
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
        <th> {!! order_column('Glosa','glosa','ASC') !!} </th>
        <th> {!! order_column('Codigo Estilo Armado','codigo_armado','ASC') !!} </th>
        <th> {!! order_column('Grupo Material','grupo_materiales','ASC') !!} </th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($styles as $style)
      <tr class="{{ $style->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $style->codigo }}</td>
        <td>{{ $style->glosa}}</td>
        <td>{{ $style->codigo_armado}}</td>
        <td>{{ $style->grupo_materiales}}</td>
        <td>
          @if($style->active == 1) Activo
          @else Inactivo
          @endif
        </td>
        <td>
          <a href="{{route('mantenedores.styles.edit', $style->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
          </a>
          @if($style->active == 0 )
          <form method="POST" action="{{ route('mantenedores.styles.active', $style->id) }}" style="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit" data-toggle="tooltip" title="Activar">
              <div class="material-icons md-14">check_circle</div>
            </button>
          </form>
          @else
          <form method="POST" id="form_{{ $style->id }}" action="{{ route('mantenedores.styles.inactive', $style->id) }}" style="display: inline;">
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
  {!! $styles->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>

@endsection