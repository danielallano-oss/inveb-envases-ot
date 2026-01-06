@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Ingreso Nueva Orden de Trabajo</h1>
<div class="row mb-3">
	<div class="col-12">
		<section id="ficha" class="py-3">
			<form onsubmit="$(this).find('#tipo_solicitud,#cad_id,#reference_type,#carton_id,#carton_color,#product_type_id,#style_id,#muestra,#numero_muestras,#design_type_id,#complejidad,#numero_colores,#indicador_facturacion_diseno_grafico,#matriz_id,#golpes_largo, #golpes_ancho, #separacion_golpes_largo,#separacion_golpes_ancho, #cuhillas').prop('disabled', false)" id="form-ot" method="POST" action="{{ route('storeOt') }}" enctype="multipart/form-data">
				@csrf
				@include('work-orders.ficha-form-duplicate', [
				'tipo' => "duplicate",
				'ot' => $ot,
				'class' => '',
				'clients'=> $clients,
				'cads'=> $cads,
				'cartons'=> $cartons,
				'styles'=> $styles,
				'colors'=> $colors,
				'envases'=> $envases,
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
				'reference_type'=> $reference_type,
				'fsc'=> $fsc,
				'maquila_servicios' => $maquila_servicios,
				'designTypes' => $designTypes,
				'validacion_campos' => $validacion_campos,
				'coverageExternal' => $coverageExternal,
				'coverageInternal' => $coverageInternal,
				'impresion' => $impresion,
				'colors_barniz' => $colors_barniz,
				'check_mckee' => false,
				'palletQa' => $palletQa,
				'palletTagFormat' => $palletTagFormat,
				'indicaciones_especiales' => $indicaciones_especiales
				])
				<!-- Valores que permiten llenar las jerarquias -->
				<!-- Solo hay ot si es una duplicacion y no creacion -->
				@if(isset($ot))
				<input type="hidden" id="jerarquia3" value="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->id : null}}">
				<input type="hidden" id="jerarquia2" value="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->id : null}}">
				<input type="hidden" id="jerarquia1" value="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->hierarchy->id : null}}">
				<input type="hidden" id="tipo_solicitud_ot" value="{{$tipo_solicitud_ot}}">
				<input type="hidden" id="instalacion" value="{{$ot->instalacion_cliente}}">
				@endif
				<div class="mt-3 text-right">
					<a href="{{ route('Ots') }}" class="btn btn-light">Cancelar</a>
					<button id="ot-submit" type="submit" class="btn btn-success">{{ __('Guardar OT') }}</button>
				</div>
			</form>
			@include('partials/muestras-ot',['ot' => null,'cartons' => $cartons,'cads' => $cads,'comunas' => $comunas])
	</div>
	</section>
</div>
</div>
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
<!-- Loading  -->
<div id="loading">
	<div id="modal-loader" class="loader">Loading...</div>
</div>
@endsection
@section('myjsfile')
<script src="{{ asset('js/ot-form-validation-duplication.js') }}"></script>
<script>
	window.onload = function() {
		document.getElementById("loading").style.display = "none"
	}
</script>
<!-- Solo hay ot si es una duplicacion y no creacion -->
@if(isset($ot))
<script>
	window.ot = @json($ot);
	window.tipo_solicitud = @json($tipo_solicitud_ot);
</script>
<script src="{{ asset('js/ot-duplication.js') }}"></script>
@elseif(isset($detalleCotizacion))

<script>
	window.detalleCotizacion = @json($detalleCotizacion);
	window.cotizacion = @json($cotizacion);
	window.tipo_solicitud = @json($tipo_solicitud_ot);
</script>
<script src="{{ asset('js/ot-detalleAOt.js') }}"></script>
@else
<script src="{{ asset('js/ot-creation.js') }}"></script>

@endif
<script>
$(document).ready(function () {
	// Impresion // 1 => "Offset", 2=>"Flexografía", 3=>"Sin Impresión", 4=>"Sin Impresión (Sólo OF)", 5=>"Sin Impresión (Trazabilidad Completa)"
    if (!window.ot || window.ot.impresion != 3) {
        // Si al editar no es "sin impresion" eliminamos la opcion ya que solo la mentenemos para ots  antiguas
        // $("#impresion option[value='3']").remove().selectpicker("refresh");
    }
});
</script>



<!-- ////////////////SCRIPT DE MUESTRAS Y GESTIONES DE MUESTRAS -->
<script src="{{ asset('js/ot-muestras-validation.js') }}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<script>
	// $(function() { //Propiedades del DatePicker
	// 	$('.datepickerH').datepicker({
	// 		format: 'LT'
	// 	});
	// });
	ot = @json(isset($ot) ? $ot : null) || null;
	$(function() { //Propiedades del DatePicker
		$('.datepickerH').datetimepicker({
			stepping: 1,
			icons: {
				close: 'glyphicon glyphicon-ok',
				clear: 'glyphicon glyphicon-remove'
			},
			format: 'HH:mm',
			useCurrent: true,
			showTodayButton: true,
			showClear: true,
			showClose: true,
			ignoreReadonly: true,
		});
	});
</script>
<script src="{{ asset('js/ot-muestras.js') }}"></script>
<!-- //////////////// FIN FIN FIN SCRIPT DE MUESTRAS Y GESTIONES DE MUESTRAS -->
@endsection
