@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Almacen</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.almacenes.store') }}">
      @csrf
      @include('almacenes.form', ['tipo' => "create",'almacen' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection