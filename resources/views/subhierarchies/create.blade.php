@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Jerarquía 2</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.subhierarchies.store') }}">
      @csrf
      @include('subhierarchies.form', ['tipo' => "create",'subhierarchy' => null,'class' => '','hierarchies_id' =>$hierarchies_id])
    </form>
  </div>
</div>
@endsection
@section('myjsfile')
<script>
  $(document).ready(function() {

    if ($('#hierarchies_id').is(':disabled')) {
      $('#hierarchies_id-multiselect .multiselect-selected-text').text('Sin Jerarquía para Asignar');
    }

  });
</script>
@endsection