@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Mantenedor de CeBe
  <a href="{{ route('mantenedores.cebes.create') }}" class="btn btn-primary rounded-pill ml-3 px-5">Crear</a>
</h1>

<!-- Filtros -->
<form id="filtros" class="py-3" action="{{ route('mantenedores.cebes.list') }}" method="get" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <div class="col-sm-12 col-md-3 col-lg-3">
      <div class="form-group">
        <label>CeBe</label>
        <select name="id[]" id="id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($cebes_filter,'id',['cebe'],'') !!}
        </select>
      </div>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-3">
      <div class="form-group">
        <label>Planta</label>
        <select name="planta_id[]" id="planta_id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($planta_filter,'id',['nombre'],'') !!}
        </select>
      </div>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-3">
        <div class="form-group">
          <label>Mercado</label>
          <select name="hierearchie_id[]" id="hierearchie_id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
            {!! optionsSelectObjetfilterMultiple($mercado_filter,'id',['descripcion'],'') !!}
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
        <th> {!! order_column('Planta','planta_id','ASC') !!} </th>
        <th> {!! order_column('Tipo','tipo','ASC') !!} </th>
        <th> {!! order_column('Mercado','hierearchie_id','ASC') !!} </th>
        <th> {!! order_column('CeBe','cebe','ASC') !!} </th>
        <th> {!! order_column('Nombre CeBe','nombre_cebe','ASC') !!} </th>
        <th> {!! order_column('Grupo Gastos Generales','grupo_gastos_generales','ASC') !!} </th>
        {{-- <th> {!! order_column('CeBe','cebe','ASC') !!} </th> --}}
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($cebes as $cebe)
      <tr class="{{ $cebe->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $cebe->planta->nombre }}</td>
        <td>{{ $cebe->tipo }}</td>
        {{-- <td>{{ $cebe->hierearchie_id =! null && $cebe->hierearchie_id =! 0 ?  $cebe->mercados : '' }}</td> --}}
        <td>{{ ($cebe->hierearchie_id == null || $cebe->hierearchie_id == 0) ? '':$cebe->mercados->descripcion  }}</td>
        <td>{{ $cebe->cebe }}</td>
        <td>{{ $cebe->nombre_cebe }}</td>
        <td>{{ $cebe->grupo_gastos_generales }}</td>
        {{-- <td>{{ $cebe->cebe }}</td> --}}
        <td>
          @if($cebe->active == 1) Activo
          @else Inactivo
          @endif
        </td>
        <td>
          <a href="{{route('mantenedores.cebes.edit', $cebe->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
          </a>
          @if($cebe->active == 0 )
          <form method="POST" action="{{ route('mantenedores.cebes.active', $cebe->id) }}" style="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit" data-toggle="tooltip" title="Activar">
              <div class="material-icons md-14">check_circle</div>
            </button>
          </form>
          @else
          <form method="POST" id="form_{{ $cebe->id }}" action="{{ route('mantenedores.cebes.inactive', $cebe->id) }}" style="display: inline;">
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
  {!! $cebes->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>
@endsection
