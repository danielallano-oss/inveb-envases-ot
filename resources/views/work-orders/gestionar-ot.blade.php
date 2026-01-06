@extends('layouts.index')

@section('content')
<a href="javascript: history.go(-1)"  style="font-size: 20px" class="btn btn-link px-0">&lsaquo; Volver</a>
<!-- <a href="{{route('nuevaOtExcel',$ot->id)}}" class="btn btn-link px-0">&lsaquo; Formulario Excel</a> -->
<div class="row">
	<div class="col-10">
		<h1 class="page-title">OT: <span class="text-primary">{{$ot->id}}</span></h1>
	</div>
	@if($ot->tipo_solicitud!=6)
		@if(auth()->user()->isVendedor() && isset($ot->area_hc) && isset($ot->golpes_largo)&& isset($ot->golpes_ancho)&& isset($ot->largura_hm)&& isset($ot->anchura_hm)&& isset($ot->process_id)&& isset($ot->carton_id))
		<div class="col-1 p-0">
			<a href="{{route('cotizador.cotizar-ot', $ot->id)}}" class="btn btn-outline-primary" data-id="{{$ot->id}}">Cotizar OT </a>
		</div>
		@else
		<div class="col-1 p-0" style="cursor: not-allowed;" data-html="true" data-toggle="tooltip" title="Para poder Cotizar una OT se requieren los siguientes datos:
			<br>-Area HC
			<br>-Golpes al Largo
			<br>-Golpes al Ancho
			<br>-Largura HM
			<br>-Anchura HM
			<br>-Proceso
			<br>-Cartón
		">
			<a href="#" class="btn btn-outline-primary disabled">Cotizar OT </a>
		</div>
		@endif
		@if(auth()->user()->isVendedor() || auth()->user()->isJefeVenta())
		<div class="col-1 p-0">
			<a href="#" class="btn btn-outline-primary modalDuplicarOt" id="{{$ot->id}}" data-toggle="modal" data-target="#modal-duplicar-ot">Duplicar OT </a>
		</div>
		@endif
	@endif
</div>

<div class="row mb-3">
	<div class="col-12">
		<!-- resumen de ot / encabezado -->
		@include('partials/ficha-resumen', ['ot' => $ot,'proximoCodigoMaterial'=>$proximoCodigoMaterial,'usuarioAsignado'=>$usuarioAsignado,'sufijos'=>$sufijos, 'validation_edition'=>$validation_edition])
	</div>
</div>
@if(empty($ot->ultimoCambioEstado) || (!empty($ot->ultimoCambioEstado) && ($ot->ultimoCambioEstado->state_id != 9 && $ot->ultimoCambioEstado->state_id != 11)))
<!-- // Para gerente y admin -->
@if((isset(auth()->user()->role->area) && (!auth()->user()->isVendedor() && !auth()->user()->isJefeVenta()) && $usuarioAsignado && $ot->current_area_id == auth()->user()->role->area->id) || ((auth()->user()->isVendedor() || auth()->user()->isJefeVenta())) || (isset(auth()->user()->role->area) && auth()->user()->role->area->id == 4 && $usuarioAsignado && ($ot->current_area_id == 4 || $ot->current_area_id == 5)))
<h2 class="page-title">Gestionar</h2>
@endif
@endif
<div class="row">
	<div class="col-9">
		<!-- modulo para realizar gestiones -->
		<!-- Si la ot esta perdida o anulada no se puede gestionar solo recuperar por el vendedor -->
		<!-- Si la ot no tiene ultima cambio estado o si el ultimo cambio es distinto a Terminado, Perdido y anulado -->
		@if(is_null($ot->ultimoCambioEstado) || (!empty($ot->ultimoCambioEstado) && ($ot->ultimoCambioEstado->state_id != 9 && $ot->ultimoCambioEstado->state_id != 11)))

		<!-- // (isset(auth()->user()->role->area) bloquea gerente y admin ya que no tienen area -->
		<!-- if((isset(auth()->user()->role->area) && (!auth()->user()->isVendedor() && !auth()->user()->isJefeVenta()) && $usuarioAsignado && $ot->current_area_id == auth()->user()->role->area->id) || ((auth()->user()->isVendedor() || auth()->user()->isJefeVenta())) || (isset(auth()->user()->role->area) && auth()->user()->role->area->id == 4 && $usuarioAsignado && ($ot->current_area_id == 4 || $ot->current_area_id == 5)) || ) -->
			@if((isset(auth()->user()->role->area) && $usuarioAsignado) || (Auth()->user()->isJefeMuestras() || Auth()->user()->isJefeDesarrollo()) || ((auth()->user()->isTecnicoMuestras() || Auth()->user()->isJefeMuestras() || Auth()->user()->isSuperAdministrador())))
			@include('partials/gestionar',['ot' => $ot,'states' =>$states, 'workSpaces' => $workSpaces,'managementTypes'=>$managementTypes , 'procesos'=>$procesos])
			@endif

		<!-- Gestion de muestras, diseñador crea y edita, tecnico muestra solo edita comentario y vendedor solo Visualiza -->
		@if((isset(auth()->user()->role->area) && $usuarioAsignado) || (Auth()->user()->isIngeniero() || auth()->user()->isJefeVenta()||auth()->user()->isVendedor() || auth()->user()->isTecnicoMuestras()) || (Auth()->user()->isJefeMuestras() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isSuperAdministrador()))
			@include('partials/muestras-ot',['ot' => $ot,'cartons' => $cartons,'cartons_muestra' => $cartons_muestra,'cads' => $cads,'comunas' => $comunas,'salas_cortes' => $salas_cortes])
		@else
			@if($ot->current_area_id==6 && ((auth()->user()->isTecnicoMuestras()) || (Auth()->user()->isJefeMuestras())))
			@include('partials/muestras-ot',['ot' => $ot,'cartons' => $cartons,'cartons_muestra' => $cartons_muestra,'cads' => $cads,'comunas' => $comunas,'salas_cortes' => $salas_cortes])
			@endif
		@endif



		<!-- Si esta anulada o perdida permite recuperarla -->
		<!-- Validacion antigua para las anuladas  || $ot->ultimoCambioEstado->state_id == 11 -->
		@elseif(!empty($ot->ultimoCambioEstado) && ($ot->ultimoCambioEstado->state_id == 9) && auth()->user()->role->area->id == 1)
		<a class="btn btn-outline-primary mb-4" href="{{route('reactivarOt', $ot->id)}}" style="width:100%">
			Reactivar OT <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
		</a>
		@endif

		@include('partials/historial',['ot' => $ot,'motivos'=> $motivos])
	</div>
	<div class="col-3">
		@include('partials/adjuntos-vacio',['files' => $ot->files])
	</div>
</div>

<input type="hidden" id="role_id" name="role_id" value="{{auth()->user()->role->id}}">
<input type="hidden" id="envio_disenador_externo" name="envio_disenador_externo" value="{{$envio_disenador_externo}}">
<!-- MODAL VER OT -->
<div class="modal fade" id="modal-ver-ot">
	<div class="modal-dialog modal-xl " style="width:100%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title page-title">
					<h1 class="page-title">Visualización OT: <span class="text-primary">{{$ot->id}}</span></h1>
				</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="    background-color: #F2F4FD">
				{{-- <div id="modal-loader" class="loader">Loading...</div> --}}
				<div id="modal-ver-ot-content"></div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-ver-ot-estudio">
	<div class="modal-dialog modal-xl " style="width:100%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title page-title">
					<h1 class="page-title">Visualización OT: <span class="text-primary">{{$ot->id}}</span></h1>
				</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="    background-color: #F2F4FD">
				<div id="modal-loader-estudio" class="loader">Loading...</div>
				<div id="modal-ver-ot-estudio-content"></div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-ver-ot-licitacion">
	<div class="modal-dialog modal-xl " style="width:100%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title page-title">
					<h1 class="page-title">Visualización OT: <span class="text-primary">{{$ot->id}}</span></h1>
				</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="    background-color: #F2F4FD">
				<div id="modal-loader-licitacion" class="loader">Loading...</div>
				<div id="modal-ver-ot-licitacion-content"></div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-ver-ot-ficha-tecnica">
	<div class="modal-dialog modal-xl " style="width:100%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title page-title">
					<h1 class="page-title">Visualización OT: <span class="text-primary">{{$ot->id}}</span></h1>
				</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="    background-color: #F2F4FD">
				<div id="modal-loader-ficha-tecnica" class="loader">Loading...</div>
				<div id="modal-ver-ot-ficha-tecnica-content"></div>
			</div>
		</div>
	</div>
</div>

<!-- MODAL DUPLICACION DE OT -->
<div class="modal fade" id="modal-duplicar-ot">
	<div class="modal-dialog modal-md " style="width:100%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title page-title">
					<h1 class="page-title">Duplicar OT: <span class="text-primary">{{$ot->id}}</span></h1>
				</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="hidden" id="duplicate_new" value="{{ route('duplicateOt',$ot->id) }}">
        		<input type="hidden" id="duplicate_old" value="{{ route('duplicateOtOld',$ot->id) }}">
				<form method="GET" id="form-codigo-material" name="form-codigo-material" action="" class="form-row form-codigo-material">
					@csrf
					<div class="container-cad" style="width:100%">
						<div class="item-cad" style="justify-content: space-between;
    align-content: start;">
							<input type="text" hidden id="ot" name="ot" value="{{$ot->id}}">
							<!-- Tipo de Solicitud -->
							{!! armarSelectArrayCreateEditOT([1 => "Desarrollo Completo", 3 => "Muestra con CAD", 5 => "Arte con Material"], 'tipo_solicitud', 'Tipo de solicitud:' , $errors, null ,'form-control',true,false) !!}


						</div>

					</div>
					<input type="hidden" id="ot_tipo_solicitud" name="ot_tipo_solicitud" value="{{$ot->tipo_solicitud}}">
					<div class="mt-3 text-right pull-right" style="width: 100%;">
						<a data-dismiss="modal" class="btn btn-light" style="cursor: pointer;">Cancelar</a>
						<button type="submit" id="duplicarOT" class="btn btn-success mx-2" disabled>Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@if(isset($recordatorio_fsc) || Session::get('recordatorio_fsc'))
<div class="modal fade" id="modal-recordatorio-fsc-prompt">
	<div class="modal-dialog modal-lg " style="width:60%">
		<div class="modal-content modal-confirmacion">
			<div class="modal-header text-center">
				<div class="title">Recordatorio FSC</div>
			</div>
			<div class="modal-body">
				<h6>Recuerda solicitar la autorización del ente certificador y adjuntar la misma a la OT</h6>
				<div class=" mt-4 text-center">
					<button class="btn btn-success mx-2" data-dismiss="modal">Confirmar</button>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" id="recordatorio_fsc" value="{{Session::get('recordatorio_fsc')}}">
@endif
@endsection
@section('myjsfile')
<script src="{{ asset('js/ot-managements.js') }}"></script>
<script>
	ot = @json(isset($ot) ? $ot : null) || null;
	$(document).ready(function() {
		//////////////////////////////////////////////
		// MASCARAS NUMERICAS
		//const volumenMask = IMask(volumen_venta_anual, thousandsOptions);
		//const usdMask = IMask(usd, thousandsOptions);

		$("#tipo_solicitud").change(() => {
				// Si esta vacio
				if (jQuery.isEmptyObject($("#tipo_solicitud").val())) {
					$("#duplicarOT").prop("disabled", true);
				} else {
					$("#duplicarOT").prop("disabled", false);
				}
				//alert($("#tipo_solicitud").val());
				if($("#tipo_solicitud").val()==1 || $("#tipo_solicitud").val()==7){
					//alert($('#duplicate_new').val());
					$('#modal-duplicar-ot #form-codigo-material').prop('action',$('#duplicate_new').val());
					$("#modal-duplicar-ot #form-codigo-material input[name=_method]").val('GET');
				}else{
					$('#modal-duplicar-ot #form-codigo-material').prop('action',$('#duplicate_old').val());
					$("#modal-duplicar-ot #form-codigo-material input[name=_method]").val('GET');
				}
			})
			.triggerHandler("change");

			if ($("#recordatorio_fsc").val()) {
				$("#modal-recordatorio-fsc-prompt").modal('show');
			}


			$('.collapse').on('show.bs.collapse', function() {
				console.log("asd");
				$('.collapse.show').each(function() {
					$(this).collapse('hide');
				});
			});
		});

		$("#modal-duplicar-ot").on("show.bs.modal", function (event) {

			if($("#ot_tipo_solicitud").val()==1){
				$("#tipo_solicitud")
					.html(`<option value="1">Desarrollo Completo</option>`)
					.selectpicker("refresh");

			}else{
				if($("#ot_tipo_solicitud").val()==3){
					$("#tipo_solicitud")
						.html(`<option value="3">Muestra con CAD</option>`)
						.selectpicker("refresh");
				}else{
					$("#tipo_solicitud")
						.html(`<option value="5">Arte con Material</option>`)
						.selectpicker("refresh");
				}
			}

			$("#tipo_solicitud")
					.val($("#ot_tipo_solicitud").val())
					.selectpicker("refresh")
					.triggerHandler("change");
		});
	/*$(document).on('submit','form-codigo-material', function() {

		alert($("#tipo_solicitud").val());
		//method="GET" action="{{ route('duplicateOt',$ot->id) }}"
		/*var select = this.value;

		$('#loading').show();
		var urlajax='/crear-ot'; //select == 1
		if(select==3 || select==5){urlajax='/crear-ot-old';}

		$.ajax({
			type: 	'get',
			url: 	urlajax,
			data: 	{},
			success: function (data) {

				//obtenemos la vista con las datos del formulario
				$('#ot-view').html(data);

				//refrescar los selectores
				$("#ot-view .selectpicker")
					.prop("disabled", false)
					.prop("readonly", false)
					.val("")
					.selectpicker("refresh");

				//Se oculta el icono de loading
				$('#loading').hide();

				//seteamos el tipo de solicitud seleccionado e inhaibilitamos
				$("#ot-view #tipo_solicitud").val($("#tipo_solicitud_select_ppal").val()).trigger("change");
				$("#ot-view #tipo_solicitud")
					.prop("disabled", true)
					.prop("readonly", true)
					.selectpicker("refresh");

				//inhaibilitamos selector principal
				$("#tipo_solicitud_select_ppal")
					.prop("disabled", true)
					.prop("readonly", true)
					.val(select)
					.selectpicker("refresh");

			},
			error: function(e) {
				console.log(e.responseText);
				$('#loading').hide();
			},
			async:true
		});

	});*/
	/*$(document).on('click', '#duplicarOT', function(e) {
		e.preventDefault();
		//alert($("#tipo_solicitud").val());
		//alert($("#ot").val());

		var urlajax='/duplicar/'+$("#ot").val(); //select == 1
		if($("#tipo_solicitud").val()==3 || $("#tipo_solicitud").val()==5){urlajax='/duplicar-old/'+$("#ot").val();}


		$('#form-codigo-material').submit();

	});*/
</script>
<!-- Script para el archivo ficha-resumen (modal creacion CAD y MATERIAL) -->
<script src="{{ asset('js/modalCodigoMaterial.js') }}"></script>
<script src="{{ asset('js/modalCadMaterial.js') }}"></script>
<script src="{{ asset('js/modalOT.js') }}"></script>

<!-- ////////////////SCRIPT DE MUESTRAS Y GESTIONES DE MUESTRAS -->
<script src="{{ asset('js/ot-muestras-validation.js') }}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<!-- NO uses async, y si usas defer respeta el orden -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
	// $(function() { //Propiedades del DatePicker
	// 	$('.datepickerH').datepicker({
	// 		format: 'LT'
	// 	});
	// });
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
