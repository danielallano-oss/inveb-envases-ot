
<div class="row mb-3">
	<div class="col-12">
		<div class="form-row">
			
			@csrf
			@include('work-orders.ficha-form-estudio-bench', [
				'tipo' => "create",
				'ot' => null,
				'class' => '',
				'clients'=> $clients,
				'canals'=> $canals, 
				'hierarchies'=> $hierarchies,
				'subhierarchies'=> $subhierarchies,
				'subsubhierarchies'=> $subsubhierarchies,
				'tipos_solicitud'=> $tipos_solicitud,
				'productTypes'=> $productTypes,
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

<div class="modal fade" id="modal-carga-detalles">
	<div class="modal-dialog modal-xl " style="width:100%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title page-title">
					<h1 id="titulo-form-detalle" class="page-title">Cargar Detalles Estudio Benchmarking</h1>

				</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" id="boton_cerrar_cargar">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="    background-color: #F2F4FD">
				<div id="" class="col-12 mb-2">
					<form method="POST" id="form-carga-detalles">
						@csrf
						<!-- <input type="hidden" id="detalle_cotizacion_id" name="detalle_cotizacion_id" value=""> -->
						<div class="row">
							<div class="col-12 mb-2">
								<div class="card">
									<!-- <div class="card-header">Servicios</div> -->
									<div class="card-body">
										<!-- Formulario de Corrugado -->
										<div class="row ">
											<div class="col-4 row">
												<div class="col-12 text-center card-header mb-3">Descargar Formato Archivo Detalles</div>
												<div class="col-12 text-center">
													<a class="btn btn-success" data-attribute="link" href="/files/Detalle_Estudio_Benchmarking.xlsx" download title="Descargar">
														<div class="material-icons" data-toggle="tooltip" style="color:#ffffff;align-items: center;">download</div>
													</a>
												</div>												
											</div>
											<div class="col-8 text-center">
												<div class="col-12  card-header mb-3">Subir Archivo</div>
												<input type="file" class="file" name="archivo_detalles" id="archivo_detalles" required />
											</div>
											<!-- <div class="col-4">

											</div> -->


										</div>
										

									</div>
								</div>
								<div class="mt-3 text-right">
									<button id="guardarCargaMasiva" type="submit" class="btn btn-success float-right creacion">{{ isset($cotizacion->id) ? __('Confirmar Detalles') : __('Confirmar Detalles') }}</button>
									<button data-dismiss="modal" class="btn btn-light">Cancelar</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="{{ asset('js/ot-form-validation-estudio-bench.js') }}"></script>
<script src="{{ asset('js/ot-creation-estudio-bench.js') }}"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>