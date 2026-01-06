@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Almacen</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<form method="POST" action="{{ route('mantenedores.almacenes.update', $almacen->id) }}">
			@method('PUT')
			@csrf
			@include('almacenes.form', ['tipo' => "edit",'class' => 'disabled'])
		</form>
	</div>
</div>
@endsection