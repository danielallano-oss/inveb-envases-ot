@extends('layouts.index', ['dontnotify' => true])

@section('content')
<a href="javascript: history.go(-1)" style="font-size: 20px" class="btn btn-link px-0">&lsaquo; Volver</a>
<h1 class="page-title">Editar Orden de Trabajo # <span class="text-primary">{{$ot->id}}</span></h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<section id="ficha" class="py-3">
			<!-- formulario: -->

			<form  id="form-ot" method="POST" action="{{ route('updateOt', $ot->id) }}" enctype="multipart/form-data">
				@method('PUT')
				@csrf
				@include('work-orders/ficha-form-ficha-tecnica', [
				'tipo' => "edit",
				'ot' => $ot,
				'class' => '',
				'clients'=> $clients,
				'canals'=> $canals,
				'hierarchies'=> $hierarchies,
				'subhierarchies'=> $subhierarchies,
				'subsubhierarchies'=> $subsubhierarchies,
				'tipos_solicitud'=> $tipos_solicitud,
				'ajustes_area_desarrollo'=>$ajustes_area_desarrollo

				])

				<input type="hidden" id="jerarquia3" value="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->id : null}}">
				<input type="hidden" id="jerarquia2" value="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->id : null}}">
				<input type="hidden" id="jerarquia1" value="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->hierarchy->id : null}}">
				<?php /*
				 <input type="hidden" id="jerarquia3" value="{{old('subsubhierarchy_id') ? old('subsubhierarchy_id') : $ot->subsubhierarchy->id}}">
				<input type="hidden" id="jerarquia2" value="{{old('subhierarchy_id') ? old('subhierarchy_id') : $ot->subsubhierarchy->subhierarchy->id}}">
				<input type="hidden" id="jerarquia1" value="{{old('hierarchy_id') ? old('hierarchy_id') : $ot->subsubhierarchy->subhierarchy->hierarchy->id}}">
				*/ ?>

				<div class="mt-3 text-right">
					<a href="{{ route('gestionarOt', $ot->id) }}" class="btn btn-light">Cancelar</a>
					<button id="guardarOt" type="submit" class="btn btn-success">{{ isset($ot->id) ? __('Actualizar') : __('Guardar OT') }}</button>
				</div>
			</form>
		</section>
	</div>
</div>

<!-- Loading  -->
<style>
	#loading {
		width: 100%;
		height: 100%;
		top: 0px;
		left: 0px;
		position: fixed;
		display: block;
		z-index: 99;
		background-color: rgba(0, 0, 0, 0.15);
	}

	.loader {
		position: absolute;
		top: 40%;
		left: 45%;
		z-index: 100;
	}
</style>
<div id="loading">
	<div id="modal-loader" class="loader">Loading...</div>
</div>
@endsection
@section('myjsfile')

<script src="{{ asset('js/ot-form-validation-ficha-tecnica.js') }}"></script>
<script src="{{ asset('js/ot-edition-ficha-tecnica.js') }}"></script>
<script>
	window.ot = @json($ot);

	window.onload = function() {
		document.getElementById("loading").style.display = "none"
	}
</script>
@endsection
