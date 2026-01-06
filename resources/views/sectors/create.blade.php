@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Sector</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.sectors.store') }}">
      @csrf
      @include('sectors.form', ['tipo' => "create",'sector' => null,'class' => '','product_types_id' =>$product_types_id])
    </form>
  </div>
</div>
@endsection
@section('myjsfile')
<script>
  $(document).ready(function() {

    if ($('#product_type_id').is(':disabled')) {
      $('#product_types_id-multiselect .multiselect-selected-text').text('Sin Tipos de Producto para Asignar');
    }

  });
</script>
@endsection