@extends('layouts.index', ['dontnotify' => true])


@section('content')

<h1 class="page-title">Crear Estilo</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.styles.store') }}">
      @csrf
      @include('styles.form', ['tipo' => "create",'style' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection