@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear CeBe</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.cebes.store') }}">
      @csrf
      @include('cebes.form', ['tipo' => "create",'cebe' => null,'class' => '',])
    </form>
  </div>
</div>
@endsection
