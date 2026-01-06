@extends('layouts.index', ['dontnotify' => true])

@section('content')
<a href="javascript: history.go(-1)" style="font-size: 20px" class="btn btn-link px-0">&lsaquo; Volver</a>
<h1 class="page-title">Editar {{$type_edit == 'orden_compra' ? __('OC') : __('Descripción') }} de Orden de Trabajo # <span class="text-primary">{{$ot->id}}</span></h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<section id="ficha" class="py-3">
			<!-- formulario: -->
			<form onsubmit="$(this).find('input,select,textarea').prop('disabled', false)" id="form-ot" method="POST" action="{{ route('updateDescriptionOt', $ot->id) }}">
				@method('PUT')
				@csrf
				@include('work-orders/ficha-form-description', [
				'tipo' => "edit",
				'ot' => $ot,
				'class' => '',
				'clients'=> $clients,
				'cads'=> $cads,
				'cartons'=> $cartons,
				'styles'=> $styles,
				'colors'=> $colors,
				'canals'=> $canals,
				'productTypes'=> $productTypes,
				'materials'=> $materials,
				'procesos'=> $procesos,
				'armados'=> $armados,
				'sentidos_armado'=> $sentidos_armado,
				'hierarchies'=> $hierarchies,
				'subhierarchies'=> $subhierarchies,
				'subsubhierarchies'=> $subsubhierarchies,
				'tipos_solicitud'=> $tipos_solicitud,
				'org_ventas'=> $org_ventas,
				'paisReferencia'=> $paisReferencia,
				'plantaObjetivo'=> $plantaObjetivo,
				'palletTypes'=> $palletTypes,
				'recubrimiento_type'=> $recubrimiento_type,
				'reference_type' => $reference_type,
				'fsc'=> $fsc,
				'type_edit'=> $type_edit,
				'designTypes'=> $designTypes,
				'maquila_servicios' => $maquila_servicios,
				'validacion_campos' => $validacion_campos,
				'palletQa' => $palletQa,
				'palletTagFormat' => $palletTagFormat,
				'indicaciones_especiales' => $indicaciones_especiales
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

<div class="modal fade" id="modal-cad-prompt">
	<div class="modal-dialog modal-lg " style="width:60%">
		<div class="modal-content modal-confirmacion">
			<div class="modal-header text-center">
				<div class="title">Confirmar Seleccion de CAD</div>
			</div>
			<div class="modal-body">
				<h6>Importante:
					<br><br>
					Al seleccionar un CAD aceptas sobreescribir todos los datos relacionados al CAD de esta Órden de Trabajo.<br>
					Este proceso no es reversible.
				</h6>
				<div class=" mt-4 text-center">
					<button class="btn btn-light" data-dismiss="modal">Cancelar</button>
					<button type="submit" form="form-cad-material" id="seleccionarCad" class="btn btn-success mx-2" data-dismiss="modal">Continuar</button>
				</div>
			</div>
		</div>
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
<?php /* <script src="{{ asset('js/ot-creation.js') }}"></script>
*/ ?>
<script src="{{ asset('js/ot-form-validation.js') }}"></script>
<script src="{{ asset('js/ot-edition.js') }}"></script>
<script>
	window.ot = @json($ot);

	window.onload = function() {
		document.getElementById("loading").style.display = "none"
	}


$(document).ready(function () {
	// Impresion // 1 => "Offset", 2=>"Flexografía", 3=>"Sin Impresión", 4=>"Sin Impresión (Sólo OF)", 5=>"Sin Impresión (Trazabilidad Completa)"
    if (!window.ot || window.ot.impresion != 3) {
        // Si al editar no es "sin impresion" eliminamos la opcion ya que solo la mentenemos para ots  antiguas
        // $("#impresion option[value='3']").remove().selectpicker("refresh");
    }
});
</script>
<!-- Si ya fue creado el material y cad entonces desabilitamos el boton de actualizar  -->
@if($ot->tipo_solicitud == 1 && isset($ot->material_id) && isset($ot->cad_id))
<script>
	// $(document).ready(function() {
	// 	$("#guardarOt").prop('disabled', true).css("cursor", "not-allowed");
	// });
</script>
@endif
@endsection
