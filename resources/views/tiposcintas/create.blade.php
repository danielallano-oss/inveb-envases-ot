@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Tipo Cinta</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.tipos-cintas.store') }}">
      @csrf
      @include('tiposcintas.form', ['tipo' => "create",'tipocinta' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection