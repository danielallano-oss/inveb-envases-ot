@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Material</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<!-- formulario: -->
		<form method="POST" action="{{ route('mantenedores.materials.update', $material->id) }}">
			@method('PUT')
			@csrf
			@include('materials.form', [
			'tipo' => "edit",
			'class' => 'disabled',
			'cartons' =>$cartons,
			'productTypes' =>$productTypes,
			'styles' =>$styles,
			'rayados' =>$rayados,
			'clients' =>$clients
			])
		</form>
	</div>
</div>
@endsection