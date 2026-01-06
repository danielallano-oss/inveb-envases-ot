@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Secuencia Operacional</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.secuencias-operacionales.store') }}">
      @csrf
      @include('secuenciasoperacionales.form', ['tipo' => "create",'secuenciaoperacional' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection