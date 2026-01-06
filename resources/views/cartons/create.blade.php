@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Cartón</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.cartons.store') }}">
      @csrf
      @include('cartons.form', ['tipo' => "create",'carton' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection
@section('myjsfile')
<script src="{{ asset('js/cartons.js') }}"></script>
@endsection