@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar usuario</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<form method="POST" action="{{ route('mantenedores.product-types.update', $productType->id) }}">
			@method('PUT')
			@csrf
			@include('product-types.form', ['tipo' => "edit",'class' => 'disabled'])
		</form>
	</div>
</div>
@endsection