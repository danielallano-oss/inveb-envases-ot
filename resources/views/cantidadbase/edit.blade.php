@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Cantidad Base</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<form method="POST" action="{{ route('mantenedores.cantidad-base.update', $cantidadbase->id) }}">
			@method('PUT')
			@csrf
			@include('cantidadbase.form', ['tipo' => "edit",'class' => 'disabled'])
		</form>
	</div>
</div>
@endsection