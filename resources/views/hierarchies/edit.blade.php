@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Jerarquía 1</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<!-- formulario: -->
		<form method="POST" action="{{ route('mantenedores.hierarchies.update', $hierarchy->id) }}">
			@method('PUT')
			@csrf
			@include('hierarchies.form', ['tipo' => "edit",'class' => 'disabled'])
		</form>
	</div>
</div>

@endsection