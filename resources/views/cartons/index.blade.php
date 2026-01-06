@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Mantenedor de Cartones
  <a href="{{ route('mantenedores.cartons.create') }}" class="btn btn-primary rounded-pill ml-3 px-5">Crear</a>
</h1>

<!-- Filtros -->
<form id="filtros" class="py-3" action="{{ route('mantenedores.cartons.list') }}" method="get" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <div class="col-2">
      <div class="form-group">
        <label>Cartones</label>
        <select name="id[]" id="id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($cartons_filter,'id',['codigo','descripcion'],'') !!}
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
        <th>Onda</th>
        <th id="peso">Peso Bruto (g)</th>
        <th id="volumen">Volumen (cms 3)</th>
        <th>Espesor (mm)</th>
        <th>Color</th>
        <th>Tipo de Cartón</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($cartons as $carton)
      <tr class="{{ $carton->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $carton->codigo }}</td>
        <td>{{ $carton->onda }}</td>
        <td>{{ number_format($carton->peso ,0,'', '.')}}</td>
        <td>{{ number_format($carton->volumen ,0,'', '.')}}</td>
        <td>{{ number_format($carton->espesor ,2,',', '.') }}</td>
        <td>{{ $carton->color }}</td>
        <td>{{ $carton->tipo }}</td>
        <td>
          @if($carton->active == 1) Activo
          @else Inactivo
          @endif
        </td>
        <td>
          <a href="{{route('mantenedores.cartons.edit', $carton->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
          </a>
          @if($carton->active == 0 )
          <form method="POST" action="{{ route('mantenedores.cartons.active', $carton->id) }}" carton="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit" data-toggle="tooltip" title="Activar">
              <div class="material-icons md-14">check_circle</div>
            </button>
          </form>
          @else
          <form method="POST" id="form_{{ $carton->id }}" action="{{ route('mantenedores.cartons.inactive', $carton->id) }}" carton="display: inline;">
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
  {!! $cartons->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>

@endsection

@section('myjsfile')
<script src="{{ asset('js/cartons.js') }}"></script>
@endsection