@extends('layouts.index', ['dontnotify' => true])

@section('content')
<a href="javascript: history.go(-1)" style="font-size: 20px" class="btn btn-link px-0">&lsaquo; Volver</a>
<h1 class="page-title">Editar Orden de Trabajo # <span class="text-primary">{{$ot->id}}</span></h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<section id="ficha" class="py-3">
			<!-- formulario: -->

			<form id="form-ot" method="POST" action="{{ route('updateOt', $ot->id) }}">
				@method('PUT')
				@csrf
				@include('work-orders/ficha-form-estudio-bench', [
				'tipo' => "edit",
				'ot' => $ot,
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

				<input type="hidden" id="jerarquia3" value="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->id : null}}">
				<input type="hidden" id="jerarquia2" value="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->id : null}}">
				<input type="hidden" id="jerarquia1" value="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->hierarchy->id : null}}">

				<div class="mt-3 text-right">
					<a href="{{ route('gestionarOt', $ot->id) }}" class="btn btn-light">Cancelar</a>
					<button id="guardarOt" type="submit" class="btn btn-success">{{ isset($ot->id) ? __('Actualizar') : __('Guardar OT') }}</button>
				</div>
			</form>
		</section>
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

<script src="{{ asset('js/ot-form-validation-estudio-bench.js') }}"></script>
<script src="{{ asset('js/ot-edition-estudio-bench.js') }}"></script>
<script>
	window.ot = @json($ot);

	window.onload = function() {
		document.getElementById("loading").style.display = "none"
	}
</script>
<!-- Si ya fue creado el material y cad entonces desabilitamos el boton de actualizar  -->

@endsection
