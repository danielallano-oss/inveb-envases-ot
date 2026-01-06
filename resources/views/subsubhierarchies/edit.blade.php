@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Jerarquía 3</h1>


<div class="row mb-3">
	<div class="col-12 p-2">
		<form method="POST" action="{{ route('mantenedores.subsubhierarchies.update', $subsubhierarchy->id) }}">
			@method('PUT')
			@csrf
			@include('subsubhierarchies.form', ['tipo' => "edit",'class' => 'disabled','subhierarchies_id' => $subhierarchies_id])
		</form>
	</div>
</div>

@endsection
@section('myjsfile')
<script>
	$(document).ready(function() {

		if ($('#subhierarchies_id').is(':disabled')) {
			$('#subhierarchies_id-multiselect .multiselect-selected-text').text('Sin Jerarquía para Asignar');
		}

	});
</script>
@endsection