@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Areá HC</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form id="formAreaHC" method="POST" action="{{ route('cotizador.crear_areahc') }}">
      @csrf
      @include('cotizador.areas-hc.form', ['tipo' => "create",'areahc' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection
@section('myjsfile')
<script src="{{ asset('js/cotizador/areaHC.js') }}"></script>
<script src="{{ asset('js/cotizador/areahc-validation.js') }}"></script>
@endsection