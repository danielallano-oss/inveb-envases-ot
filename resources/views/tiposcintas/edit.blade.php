@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Tipo Cinta</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<form method="POST" action="{{ route('mantenedores.tipos-cintas.update', $tipocinta->id) }}">
			@method('PUT')
			@csrf
			@include('tiposcintas.form', ['tipo' => "edit",'class' => 'disabled'])
		</form>
	</div>
</div>
@endsection