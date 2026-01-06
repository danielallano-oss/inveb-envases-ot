@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Rechazo Conjunto</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.rechazo-conjunto.store') }}">
      @csrf
      @include('rechazoconjunto.form', ['tipo' => "create",'rechazoconjunto' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection