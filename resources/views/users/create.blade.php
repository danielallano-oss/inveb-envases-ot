@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear usuario</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form method="POST" action="{{ route('mantenedores.users.store') }}">
      @csrf
      @include('users.form', ['tipo' => "create",'user' => null,'class' => '','profiles'=> $profiles,'jefesVenta'=>$jefesVenta])
    </form>
  </div>
</div>
@endsection
@section('myjsfile')

<script type="text/javascript" src="{{ asset('/js/functions_user.js') }}"></script>

@endsection