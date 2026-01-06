
<div class="row mb-3">
	<div class="col-12">
		<div class="form-row">
			
			@csrf
			@include('work-orders.ficha-form-ficha-tecnica', [
				'tipo' => "create",
				'ot' => null,
				'class' => '',
				'clients'=> $clients,
				'canals'=> $canals, 
				'hierarchies'=> $hierarchies,
				'subhierarchies'=> $subhierarchies,
				'subsubhierarchies'=> $subsubhierarchies,
				'tipos_solicitud'=> $tipos_solicitud,
				'ajustes_area_desarrollo'=>$ajustes_area_desarrollo
				
			])
			
			<!-- Valores que permiten llenar las jerarquias -->
			<!-- Solo hay ot si es una duplicacion y no creacion -->
			@if(isset($ot))
				<input type="hidden" id="jerarquia3" value="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->id : null}}">
				<input type="hidden" id="jerarquia2" value="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->id : null}}">
				<input type="hidden" id="jerarquia1" value="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->hierarchy->id : null}}">
			@endif
			<div class="mt-3 text-right">
				<a href="{{ route('Ots') }}" class="btn btn-light">Cancelar</a>
				<button id="ot-submit" type="submit" class="btn btn-success">{{ __('Guardar OT') }}</button>
			</div>		
			
		</div>
	</div> 
</div>
<script src="{{ asset('js/ot-form-validation-ficha-tecnica.js') }}"></script>
<script src="{{ asset('js/ot-creation-ficha-tecnica.js') }}"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>


