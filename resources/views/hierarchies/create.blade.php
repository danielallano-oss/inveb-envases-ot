@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Jerarquía 1</h1>
<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.hierarchies.store') }}">
      @csrf
      @include('hierarchies.form', ['tipo' => "create",'hierarchy' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection