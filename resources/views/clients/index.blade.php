@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Mantenedor de Clientes
  @if(!Auth()->user()->isIngeniero() && !Auth()->user()->isJefeCatalogador() && !Auth()->user()->isCatalogador() && !Auth()->user()->isJefeDesarrollo() && !Auth()->user()->isJefeDiseño() && !Auth()->user()->isDiseñador()  && !Auth()->user()->isJefePrecatalogador() && !Auth()->user()->isPrecatalogador())
    <a href="{{ route('mantenedores.clients.create') }}" class="btn btn-primary rounded-pill ml-3 px-5">Crear</a>
  @endif
</h1>

<!-- Filtros -->
<form id="filtros" class="py-3" class="filter-tool" action="{{ route('mantenedores.clients.list') }}" method="get" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <div class="col-2">
      <div class="form-group">
        <label>Cliente</label>
        <select name="id[]" id="id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($clients_filter,'id',['rut','nombre'],'') !!}
        </select>
      </div>
    </div>
    <div class="col-2">
      <div class="form-group">
        <label>Clasificación</label>
        <select name="clasificacion[]" id="clasificacion" class="form-control form-control-sm" multiple data-live-search="false" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectArrayfilterMultiple($clasificaciones,'clasificacion') !!}
        </select>
      </div>
    </div>
    <div class="col-2">
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
        <th>RUT</th>
        <th>{!! order_column('Nombre','name','ASC') !!}</th>
        <th>Dirección</th>
        <th><center>N° instalaciones</center></th>
        <th>Clasificación</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($clients as $client)
      <tr class="{{ $client->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $client->rut }}</td>
        <td>{{ $client->nombre}}</td>
        <td>{{ $client->direccion }}</td>
        <td align="center">{{ $client->instalaciones }}</td>
        @if(is_null($client->clasificacion) || $client->clasificacion == 0 || $client->clasificacion == '')
          <td>-</td>
        @else
          <td>{{ $client->ClasificacionCliente->name }}</td>
        @endif
        <td>
          @if($client->active == 1) Activo
          @else Inactivo
          @endif
        </td>
        <td>
          <a href="{{route('mantenedores.clients.edit', $client->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
          </a>

          @if(Auth()->user()->isAdmin())
          @if($client->active == 0 )
          <form method="POST" action="{{ route('mantenedores.clients.active', $client->id) }}" style="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit" data-toggle="tooltip" title="Activar">
              <div class="material-icons md-14">check_circle</div>
            </button>
          </form>
          @else
          <form method="POST" id="form_{{ $client->id }}" action="{{ route('mantenedores.clients.inactive', $client->id) }}" style="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit" data-toggle="tooltip" title="Inactivar">
              <div class="material-icons md-14">remove_circle</div>
            </button>
          </form>
          @endif
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<!-- Paginacion -->
<nav class="mt-3">
  {!! $clients->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>

@endsection
