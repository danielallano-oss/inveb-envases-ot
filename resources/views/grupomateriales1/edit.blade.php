@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Grupo Materiales 1</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<form method="POST" action="{{ route('mantenedores.grupo-materiales-1.update', $grupomaterial->id) }}">
			@method('PUT')
			@csrf
			@include('grupomateriales1.form', ['tipo' => "edit",'class' => 'disabled'])
		</form>
	</div>
</div>
@endsection