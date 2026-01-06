@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Color</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<form method="POST" action="{{ route('mantenedores.colors.update', $color->id) }}">
			@method('PUT')
			@csrf
			@include('colors.form', ['tipo' => "edit",'class' => 'disabled'])
		</form>
	</div>
</div>
@endsection