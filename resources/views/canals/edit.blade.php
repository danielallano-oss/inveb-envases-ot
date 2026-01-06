@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Canal</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<!-- formulario: -->
		<form method="POST" action="{{ route('mantenedores.canals.update', $canal->id) }}">
			@method('PUT')
			@csrf
			@include('canals.form', ['tipo' => "edit",'class' => 'disabled'])
		</form>
	</div>
</div>
@endsection