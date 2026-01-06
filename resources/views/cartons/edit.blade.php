@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Cartón</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<form method="POST" action="{{ route('mantenedores.cartons.update', $carton->id) }}">
			@method('PUT')
			@csrf
			@include('cartons.form', ['tipo' => "edit",'class' => 'disabled'])
		</form>
	</div>
</div>
@endsection
@section('myjsfile')
<script src="{{ asset('js/cartons.js') }}"></script>
@endsection