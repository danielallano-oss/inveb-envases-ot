@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Grupo Materiales 1</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.grupo-materiales-1.store') }}">
      @csrf
      @include('grupomateriales1.form', ['tipo' => "create",'grupomaterial' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection