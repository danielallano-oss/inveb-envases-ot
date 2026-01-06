@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Clasificación de Cliente</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<form method="POST" action="{{ route('mantenedores.clasificaciones_clientes.update', $clasificacion_cliente->id) }}">
			@method('PUT')
			@csrf
			@include('clasificaciones_clientes.form', ['tipo' => "edit",'class' => 'disabled'])
		</form>
	</div>
</div>
@endsection