@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Adhesivo</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<!-- formulario: -->
		<form method="POST" action="{{ route('mantenedores.adhesivos.update', $adhesivo->id) }}">
			@method('PUT')
			@csrf
			@include('adhesivos.form', ['tipo' => "edit",'class' => 'disabled'])
		</form>
	</div>
</div>
@endsection