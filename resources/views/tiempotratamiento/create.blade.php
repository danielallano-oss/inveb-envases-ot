@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Tiempo Tratamiento</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.tiempo-tratamiento.store') }}">
      @csrf
      @include('tiempotratamiento.form', ['tipo' => "create",'tiempotratamiento' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection