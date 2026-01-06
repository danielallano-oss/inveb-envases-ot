@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Color</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.colors.store') }}">
      @csrf
      @include('colors.form', ['tipo' => "create",'color' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection