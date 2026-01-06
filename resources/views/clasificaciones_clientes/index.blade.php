@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Mantenedor de Clasificación de Cliente
  <a href="{{ route('mantenedores.clasificaciones_clientes.create') }}" class="btn btn-primary rounded-pill ml-3 px-5">Crear</a>
</h1>

<!-- Filtros -->
<form id="filtros" class="py-3" action="{{ route('mantenedores.clasificaciones_clientes.list') }}" method="get" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <div class="col-sm-12 col-md-3 col-lg-3">
      <div class="form-group">
        <label>Descripción</label>
        <select name="id[]" id="id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($clasificaciones_clientes_filter,'id',['id','name'],'') !!}
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
        <th> {!! order_column('ID','id','ASC') !!} </th>
        <th> {!! order_column('Descripción','name','ASC') !!} </th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($clasificaciones_clientes as $clasificacion_cliente)
      <tr class="{{ $clasificacion_cliente->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $clasificacion_cliente->id }}</td>
        <td>{{ $clasificacion_cliente->name}}</td>
        <td>
          @if($clasificacion_cliente->active == 1) Activo
          @else Inactivo
          @endif
        </td>
        <td>
          <a href="{{route('mantenedores.clasificaciones_clientes.edit', $clasificacion_cliente->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
          </a>
          @if($clasificacion_cliente->active == 0 )
          <form method="POST" action="{{ route('mantenedores.clasificaciones_clientes.active', $clasificacion_cliente->id) }}" style="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit" data-toggle="tooltip" title="Activar">
              <div class="material-icons md-14">check_circle</div>
            </button>
          </form>
          @else
          <form method="POST" id="form_{{ $clasificacion_cliente->id }}" action="{{ route('mantenedores.clasificaciones_clientes.inactive', $clasificacion_cliente->id) }}" style="display: inline;">
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
  {!! $clasificaciones_clientes->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>
@endsection