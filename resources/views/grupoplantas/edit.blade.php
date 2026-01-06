@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Grupo Plantas</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<form method="POST" action="{{ route('mantenedores.grupo-plantas.update', $grupoplanta->id) }}">
			@method('PUT')
			@csrf
			@include('grupoplantas.form', ['tipo' => "edit",'class' => 'disabled'])
		</form>
	</div>
</div>
@endsection
