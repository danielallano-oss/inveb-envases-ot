@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Mantenedor de Adhesivos
  <a href="{{ route('mantenedores.adhesivos.create') }}" class="btn btn-primary rounded-pill ml-3 px-5">Crear</a>
</h1>

<!-- Filtros -->
<form id="filtros" class="py-3" action="{{ route('mantenedores.adhesivos.list') }}" method="get" enctype="multipart/form-data">
  @csrf

  <div class="form-row">
    <div class="col-2">
      <div class="form-group">
        <label>Adhesivo</label>
        <select name="id[]" id="id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($adhesivos_filter,'id',['codigo','maquina'],'') !!}
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
        <th> {!! order_column('Planta','planta_id','ASC') !!} </th>
        <th>Maquina</th>
        <th>Código SAP</th>
        <th>Consumo gr. Ml</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($adhesivos as $adhesivo)
      <tr class="{{ $adhesivo->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $adhesivo->Planta->nombre}}</td>
        <td>{{ $adhesivo->maquina}}</td>
        <td>{{ $adhesivo->codigo}}</td>
        <td>{{ $adhesivo->consumo}}</td>
        <td>
          @if($adhesivo->active == 1) Activo
          @else Inactivo
          @endif
        </td>
        <td>
          <a href="{{route('mantenedores.adhesivos.edit', $adhesivo->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
          </a>
          @if($adhesivo->active == 0 )
            <form method="POST" action="{{ route('mantenedores.adhesivos.active', $adhesivo->id) }}" style="display: inline;">
              @method('put')
              @csrf
              <button class="btn_link" type="submit" data-toggle="tooltip" title="Activar">
                <div class="material-icons md-14">check_circle</div>
              </button>
            </form>
          @else
            <form method="POST" id="form_{{ $adhesivo->id }}" action="{{ route('mantenedores.adhesivos.inactive', $adhesivo->id) }}" style="display: inline;">
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
  {!! $adhesivos->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>

@endsection