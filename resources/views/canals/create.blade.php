@extends('layouts.index', ['dontnotify' => true])


@section('content')

<h1 class="page-title">Crear Canal</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.canals.store') }}">
      @csrf
      @include('canals.form', ['tipo' => "create",'canal' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection