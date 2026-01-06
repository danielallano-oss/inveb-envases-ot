@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Mantenedor de Materiales
  <a href="{{ route('mantenedores.materials.create') }}" class="btn btn-primary rounded-pill ml-3 px-5">Crear</a>
</h1>

<!-- Filtros -->
<form id="filtros" class="py-3" action="{{ route('mantenedores.materials.list') }}" method="get" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <div class="col-2">
      <div class="form-group">
        <label>Código</label>
        <input type="text" name="codigo" id="codigo"  value="{{ is_null(app('request')->query('codigo')) ? '' : app('request')->query('codigo') }}" class="form-control form-control-sm" placeholder="">
        {{-- <select name="id[]" id="id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($materials_filter,'id',['codigo','descripcion'],'') !!}
        </select> --}}
      </div>
    </div>
    <div class="col-2">
        <div class="form-group">
          <label>Descripción</label>
          <input type="text" name="descripcion" id="descripcion" value="{{ is_null(app('request')->query('descripcion')) ? '' : app('request')->query('descripcion') }}" class="form-control form-control-sm" placeholder="">

        </div>
      </div>
    <div class="col-2">
      <div class="form-group">
        <label>Estado</label>
        <select name="active[]" id="active" class="form-control form-control-sm" multiple data-live-search="false" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectArrayfilterMultiple([1=>'Activo', 0=>'Inactivo',2=>'En Proceso'],'active') !!}
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
        <th>ID</th>
        <th>Código</th>
        <th>Descripcion</th>
        <th>N° Colores</th>
        <th>Gramaje</th>
        <th>ECT</th>
        <th>Peso</th>
        <th>Golpes Largo</th>
        <th>Golpes Ancho</th>
        <th>Area HC</th>
        <th>BCT MIN LB</th>
        <th>BCT MIN KG</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($materials as $material)
      <tr class="{{ $material->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $material->id }}</td>
        <td>{{ $material->codigo }}</td>
        <td>{{ $material->descripcion }}</td>
        <td>{{ $material->numero_colores }}</td>
        <td>{{ $material->gramaje }}</td>
        <td>{{ $material->ect }}</td>
        <td>{{ $material->peso }}</td>
        <td>{{ $material->golpes_largo }}</td>
        <td>{{ $material->golpes_ancho }}</td>
        <td>{{ $material->area_hc }}</td>
        <td>{{ $material->bct_min_lb }}</td>
        <td>{{ $material->bct_min_kg }}</td>
        <td>
          @if($material->active == 1) Activo
          @elseif ($material->active == 2) En Proceso
          @else Inactivo
          @endif
        </td>
        <td>
          <a href="{{route('mantenedores.materials.edit', $material->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
          </a>
          @if($material->active == 0 || $material->active == 2)
          <form method="POST" action="{{ route('mantenedores.materials.active', $material->id) }}" style="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit" data-toggle="tooltip" title="Activar">
              <div class="material-icons md-14">check_circle</div>
            </button>
          </form>
          @else
          <form method="POST" id="form_{{ $material->id }}" action="{{ route('mantenedores.materials.inactive', $material->id) }}" style="display: inline;">
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
  {!! $materials->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>

@endsection
