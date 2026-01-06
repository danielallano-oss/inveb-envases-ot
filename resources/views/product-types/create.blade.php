@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Tipo de Producto</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.product-types.store') }}">
      @csrf
      @include('product-types.form', ['tipo' => "create",'productType' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection