@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Grupo Imputación Material y Familia</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<form method="POST" action="{{ route('mantenedores.grupo-imputacion-material.update', $grupoimputacionmaterial->id) }}">
			@method('PUT')
			@csrf
			@include('grupoimputacionmateriales.form', ['tipo' => "edit",'class' => 'disabled'])
		</form>
	</div>
</div>
@endsection
