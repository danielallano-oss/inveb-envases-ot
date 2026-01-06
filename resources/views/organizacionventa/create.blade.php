@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Organización Venta</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.organizacion-venta.store') }}">
      @csrf
      @include('organizacionventa.form', ['tipo' => "create",'organizacionventa' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection