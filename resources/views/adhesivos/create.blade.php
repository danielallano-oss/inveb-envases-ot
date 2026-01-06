@extends('layouts.index', ['dontnotify' => true])


@section('content')

<h1 class="page-title">Crear Adhesivo</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.adhesivos.store') }}">
      @csrf
      @include('adhesivos.form', ['tipo' => "create",'adhesivo' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection