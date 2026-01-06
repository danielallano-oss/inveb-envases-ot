@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Grupo Imputacion Material y Familia</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.grupo-imputacion-material.store') }}">
      @csrf
      @include('grupoimputacionmateriales.form', ['tipo' => "create",'grupoimputacionmaterial' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection
