@extends('layouts.index')

@section('content')
<!-- Titulo -->
<h1 class="page-title">Mantenedor de Usuarios
  <a href="{{ route('mantenedores.users.create') }}" class="btn btn-primary rounded-pill ml-3 px-5">Crear</a>
</h1>

<!-- Filtros -->
<form id="filtros" class="py-3" action="{{ route('mantenedores.users.list') }}" method="get" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <div class="col-2">
      <div class="form-group">
        <label for="">Usuario</label>
        <select name="id[]" id="id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectObjetfilterMultiple($users_filter,'id',['rut','nombre','apellido'],'') !!}
        </select>
      </div>
    </div>
    <div class="col-2">
      <div class="form-group">
        <label for="">Perfil</label>
        <select name="role_id[]" id="role_id" class="form-control form-control-sm" multiple data-live-search="true" title="Seleccionar..." data-selected-text-format="count > 1">
          {!! optionsSelectArrayfilterMultiple($profiles,'role_id') !!}
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
        <th> RUT </th>
        <th> {!! order_column('Nombre','name','ASC') !!} </th>
        <th> {!! order_column('Tipo de usuario','role','ASC') !!} </th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $user)
      <tr class="{{ $user->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $user->rut }}</td>
        <td>{{ $user->nombre.' '.$user->apellido }}</td>
        <td>{{ $user->role->nombre }}</td>
        <td>
          @if($user->active == 1) Activo
          @else Inactivo
          @endif
        </td>
        <td>
          <a href="{{route('mantenedores.users.edit', $user->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
          </a>
          @if($user->active == 0)
          <form method="POST" action="{{ route('mantenedores.users.active', $user->id) }}" style="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit">
              <div class="material-icons md-14" data-toggle="tooltip" title="Activar">check_circle</div>
            </button>
          </form>
          @else
          <form method="POST" id="form_{{ $user->id }}" action="{{ route('mantenedores.users.inactive', $user->id) }}" style="display: inline;">
            @method('put')
            @csrf
            <button class="btn_link" type="submit">
              <i class="material-icons md-14" data-toggle="tooltip" title="Inactivar">remove_circle</i>
            </button>
          </form>
          @endif
          @if(auth()->user()->id == 1 && $user->active == 1)
          <a href="{{route('logearUsuario', $user->id)}}">
            <div class="material-icons md-14" data-toggle="tooltip" title="Iniciar Sesión">login</div>
          </a>
          @endif
        </td>

      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<!-- Paginacion -->
<nav class="mt-3">
  {!! $users->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>

@endsection