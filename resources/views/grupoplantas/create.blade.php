@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Grupo Planta</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.grupo-plantas.store') }}">
      @csrf
      @include('grupoplantas.form', ['tipo' => "create",'grupoplanta' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection
