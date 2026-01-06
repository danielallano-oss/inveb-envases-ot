@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Material</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.materials.store') }}">
      @csrf
      @include('materials.form', [
      'tipo' => "create",
      'material' => null,
      'class' => '',
      'cartons' =>$cartons,
      'productTypes' =>$productTypes,
      'styles' =>$styles,
      'rayados' =>$rayados,
      'clients' =>$clients
      ])
    </form>
  </div>
</div>
@endsection