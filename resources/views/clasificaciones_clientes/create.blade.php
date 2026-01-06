@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Clasificacion de Cliente</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.clasificaciones_clientes.store') }}">
      @csrf
      @include('clasificaciones_clientes.form', ['tipo' => "create",'clasificacion_cliente' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection