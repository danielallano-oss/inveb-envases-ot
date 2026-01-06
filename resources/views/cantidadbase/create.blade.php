@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Cantidad Base</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.cantidad-base.store') }}">
      @csrf
      @include('cantidadbase.form', ['tipo' => "create",'cantidadbase' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection