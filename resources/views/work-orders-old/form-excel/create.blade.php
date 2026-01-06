@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Añadir Datos Excel @if($formulario == "carton") Cartones @else Esquinero @endif</h1>

<div class="row mb-3">
	<div class="col-12">
		<section id="ficha" class="py-3">
			<form id="form-excel" method="POST" action="{{ route('storeOtExcel',$ot->id) }}">
				@csrf
				@include('work-orders/form-excel/ficha-form-excel-'.$formulario, [
				'tipo' => "create",
				'ot' => $ot,
				'cad' => $cad,
				'palletTypes' => $palletTypes,
				'palletPatron' => $palletPatron,
				'palletProtection' => $palletProtection,
				'cajasPorPaquete' => $cajasPorPaquete,
				'palletTagFormat' => $palletTagFormat,
				'palletQa' => $palletQa,
				'reference_type' => $reference_type,
				'palletStatusTypes' => $palletStatusTypes,
				'protectionType' => $protectionType,
				'rayadoType' => $rayadoType,
				'CharacteristicsType' => $CharacteristicsType
				])
				<div class="mt-3 text-right">
					<a href="{{ route('gestionarOt', $ot->id)  }}" class="btn btn-light">Cancelar</a>
					@if(!($formulario == "esquinero" && (Auth()->user()->isJefeDesarrollo() || Auth()->user()->isIngeniero() )))
						<button id="guardarExcel" type="submit" class="btn btn-success">{{ isset($ot->id) ? __('Actualizar') : __('Guardar OT') }}</button>
					@endif
					@if(!($formulario == "esquinero"))
						@if((Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isSuperAdministrador()))
							<a href="{{ route('descargarReporte', $ot->id) }}" target="_blank" class="btn btn-outline-primary">{{ isset($ot->id) ? __('Exportar') : '' }}</a>
						@endif
					@endif
				</div>
			</form>
	</div>
	</section>
</div>
</div>
@endsection
@section('myjsfile')
<script src="{{ asset('js/ot-old-vertion/ot-excel-form.js') }}"></script>
@if($ot->tipo_solicitud == 1 && isset($ot->material_id) && isset($ot->cad_id))
<script>
	$(document).ready(function() {
		// $("#guardarExcel").prop('disabled', true).css("cursor", "not-allowed");
	});
</script>
@endif
@endsection