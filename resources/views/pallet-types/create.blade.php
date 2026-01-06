@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Tipo Palet</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.pallet-types.store') }}">
      @csrf
      @include('pallet-types.form', ['tipo' => "create",'palletType' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection
