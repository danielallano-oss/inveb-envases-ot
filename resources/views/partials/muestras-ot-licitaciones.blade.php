@if($ot)
<section id="muestras" class="py-4" data-container="muestras">
	<div id="muestras-form" class="collapse">
		<!-- class="collapse"> -->
		<div class="card mt-3" data-component="form">
			<div class="card-body">

				<!-- Tabla / Listado -->
				<div class="container-table ">
					<h1 class="text-center" style="color: #7f7f7f;
    font-size: 24px;">Muestras
						@if((isset(auth()->user()->role->area) && $usuarioAsignado) && ( Auth()->user()->isIngeniero() || (((Auth()->user()->isVendedor() || Auth()->user()->isJefeVenta()) && $ot->current_area_id == 1))))
						<a href="#" id="crear_muestra" class="btn btn-primary rounded-pill ml-3 px-5" data-toggle="modal" data-target="#modal-muestras">Crear Muestra</a>
						@endif
					</h1>
					@if(count($ot->muestras) > 0)

					<table class="table table-status table-hover actions states">
						<thead>
							<tr>
								<th width="50px">ID </th>
								<th width="70px">CAD</th>
								<th width="70px">Cartón </th>
								<th>Tipo de Pegado </th>
								<th>Planta Corte </th>
								<th>Destinatario</th>
								<th width="80px">N° Muestras</th>
								<th width="80px">Fecha Corte</th>
								<th width="50px">Estado</th>
								<th width="40px">Acciones</th>
							</tr>
						</thead>
						<tbody>
							@foreach($ot->muestras as $muestra)
							<input type="hidden" id="cambio_rechazo_devolucion" name="cambio_rechazo_devolucion" value="{{$muestra->cambio_rechazo_devolucion}}">
							@if($muestra->prioritaria == 1)

							<tr style="background-color: #d1f3d1;">
								@else
							<tr>
								@endif
								<td width="50px">{{ $muestra->id }}</td>
								<td width="70px">{{ isset( $muestra->cad_asignado )? $muestra->cad_asignado->cad : $muestra->cad }}</td>
								<td>{{ isset( $muestra->carton )?$muestra->carton->codigo:null }}</td>
								<td>{{isset($muestra->pegado_id) ? [1 => "Sin Pegar", 2=>"Pegado Flexo Interior",3=>"Pegado Flexo Exterior",4=>"Pegado Diecutter",5=>"Pegado Cajas Fruta",6=>"Pegado con Cinta",7=>"Sin Pegar con Cinta"][$muestra->pegado_id] : null}}</td>
								@if($muestra->destinatarios_id[0] == 1)
									<td>{{ is_null( $muestra->planta_corte_vendedor ) ? '--' : $muestra->planta_corte_vendedor->nombre }}</td>
								@endif
								@if($muestra->destinatarios_id[0] == 2)
									<td>{{ is_null( $muestra->planta_corte_diseñador ) ? '--' : $muestra->planta_corte_diseñador->nombre }}</td>
								@endif
								@if($muestra->destinatarios_id[0] == 3)
									<td>{{ is_null( $muestra->planta_corte_laboratorio ) ? '--' : $muestra->planta_corte_laboratorio->nombre }}</td>
								@endif
								@if($muestra->destinatarios_id[0] == 4)
									<td>{{is_null($muestra->planta_corte_1)? '--' : $muestra->planta_corte_1->nombre}}</td>
								@endif
								@if($muestra->destinatarios_id[0] == 5)
									<td>{{ is_null( $muestra->planta_corte_diseñador_revision ) ? '--' : $muestra->planta_corte_diseñador_revision->nombre }}</td>
								@endif
								<td>{{[2=>"Retira Diseñador VB",1=>"Retira Ventas VB",4=>"Envío Cliente VB",5=>"Retira Diseñador Revisión",3=>"Envío Laboratorio"][$muestra->destinatarios_id[0]]}}</td>

								@if($muestra->destinatarios_id[0] == 1)
									<td>{{$muestra->cantidad_vendedor}}</td>
									<td>
										@if($muestra->check_fecha_corte_vendedor == 1)
										{{$muestra->fecha_corte_vendedor}}
										@else --
										@endif
									</td>
								@elseif($muestra->destinatarios_id[0] == 2)
									<td>{{$muestra->cantidad_diseñador}}</td>
									<td>
										@if($muestra->check_fecha_corte_diseñador == 1)
										{{$muestra->fecha_corte_diseñador}}
										@else --
										@endif
									</td>
								@elseif($muestra->destinatarios_id[0] == 3)
									<td>{{$muestra->cantidad_laboratorio}}</td>
									<td>
										@if($muestra->check_fecha_corte_laboratorio == 1)
										{{$muestra->fecha_corte_laboratorio}}
										@else --
										@endif
									</td>
								@elseif($muestra->destinatarios_id[0] == 4)

									<td>{{$muestra->cantidad_1 }}</td>
									<td>
										@if($muestra->check_fecha_corte_1 == 1 || $muestra->check_fecha_corte_2 == 1 || $muestra->check_fecha_corte_3 == 1 || $muestra->check_fecha_corte_4 == 1)
										{{$muestra->fecha_corte_1}}
										@else --
										@endif
									</td>
								@elseif($muestra->destinatarios_id[0] == 5)
									<td>{{$muestra->cantidad_diseñador_revision}}</td>
									<td>
										@if($muestra->check_fecha_corte_diseñador_revision == 1)
										{{$muestra->fecha_corte_diseñador_revision}}
										@else --
										@endif
									</td>
								@endif
								<td><span id="state_{{$muestra->id}}">{{["Sin Asignar","En Proceso","Rechazada","Terminada","Eliminada","Devuelta"][$muestra->estado]}}</span></td>
								<td>
									@if($ot->current_area_id==6 && (Auth()->user()->isTecnicoMuestras() || Auth()->user()->isJefeMuestras()))
										@if(Auth()->user()->sala_corte_id==$muestra->sala_corte_vendedor || Auth()->user()->sala_corte_id==$muestra->sala_corte_diseñador || Auth()->user()->sala_corte_id==$muestra->sala_corte_laboratorio || Auth()->user()->sala_corte_id==$muestra->sala_corte_1 || Auth()->user()->sala_corte_id==$muestra->sala_corte_2 || Auth()->user()->sala_corte_id==$muestra->sala_corte_3 || Auth()->user()->sala_corte_id==$muestra->sala_corte_4 || Auth()->user()->sala_corte_id==$muestra->sala_corte_diseñador_revision || auth()->user()->isJefeMuestras() ||Auth()->user()->isJefeDesarrollo())
											<div id="acciones_{{$muestra->id}}" class="form-group">
												@if(auth()->user()->isJefeMuestras() ||Auth()->user()->isJefeDesarrollo())
													@if($muestra->prioritaria == 0)
														<form method="POST" action="{{ route('muestraPrioritaria', $muestra->id) }}" style="display: inline;">
															@method('put')
															@csrf
															<button class="btn_link" type="submit">
																<div class="material-icons md-14" data-toggle="tooltip" title="Marcar Prioritaria">check_circle</div>
															</button>
														</form>
													@else
														<form method="POST" action="{{ route('muestraNoPrioritaria', $muestra->id) }}" style="display: inline;">
															@method('put')
															@csrf
															<button class="btn_link" type="submit">
																<i class="material-icons md-14" data-toggle="tooltip" title="Marcar No Prioritaria">remove_circle</i>
															</button>
														</form>
													@endif
												@else

													{{--@if($ot->current_area_id==2 && (Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo()))--}}
													&nbsp;
													<a href="#" class=" modalVerDetalle" data-id="{{$muestra->id}}" data-id-excel="{{$muestra->ot_id_excel}}" data-toggle="modal" data-target="#modal-muestras">

														@if($muestra->estado == 4 && !(Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo()))

														@elseif(Auth()->user()->isVendedor() || Auth()->user()->isJefeVenta())
															@if($ot->current_area_id==1)
																<div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
															@else
																<div class="material-icons md-14" data-toggle="tooltip" title="Ver">search</div>
															@endif

														@else
															@if((Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo())&&($ot->current_area_id==2))
																<div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
															@else
																@if((Auth()->user()->isTecnicoMuestras() || Auth()->user()->isJefeMuestras())&&($ot->current_area_id==6))
																	<div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
																@else
																@endif
															@endif

														@endif

													</a>


													@if($muestra->estado != 2 && $muestra->estado!=3 && !(Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo()) )
														{{--<a href="#" class="" onclick="event.preventDefault();
														$('#rechazarMuestraID').val({{$muestra->id}});$('#rechazarMuestraForm').submit();">--}}
														&nbsp;&nbsp;
														<a href="#" id="rechazar_muestra" class="" onclick="event.preventDefault();rechazarMuestra({{$muestra->id}})">
															<div class="material-icons md-14" data-toggle="tooltip" title="Rechazar Muestra">close</div>
														</a>
														&nbsp;
													@endif
													@if($muestra->estado != 5 && $muestra->estado != 4 && $muestra->estado!=3  && $muestra->estado!=2 && (Auth()->user()->isTecnicoMuestras() || Auth()->user()->isJefeMuestras()))
														&nbsp;&nbsp;
														<a href="#" class="" onclick="event.preventDefault();devolverMuestra({{$muestra->id}})">
															<div class="material-icons md-14" data-toggle="tooltip" title="Devolver Muestra">undo</div>
														</a>

													@endif
													{{--@endif--}}


												@if( auth()->user()->isTecnicoMuestras())
													<?php
														$terminarMuestra = false;
														switch ($muestra->destinatarios_id[0]) {
															case "1":
																if ($muestra->check_fecha_corte_vendedor) {
																	$terminarMuestra = true;
																}
																break;
															case "2":
																if ($muestra->check_fecha_corte_diseñador) {
																	$terminarMuestra = true;
																}
																break;
															case "3":
																if ($muestra->check_fecha_corte_laboratorio) {
																	$terminarMuestra = true;
																}
																break;
															case "4":
																if (
																	$muestra->check_fecha_corte_1 ||
																	$muestra->check_fecha_corte_2 ||
																	$muestra->check_fecha_corte_3 ||
																	$muestra->check_fecha_corte_4
																) {
																	$terminarMuestra = true;
																}
																break;
															case "5":
																if ($muestra->check_fecha_corte_diseñador_revision) {
																	$terminarMuestra = true;
																}
																break;
															default:
																break;
														}
														// dd($terminarMuestra);
													?>
													@if($terminarMuestra && ($muestra->estado == 1 || $muestra->estado == 5) && $ot->current_area_id == 6)
														<br><br>
														<a href="#" class="" onclick="event.preventDefault();$('#terminarMuestraEnListadoID').val({{$muestra->id}});$('#terminarMuestraEnListadoForm').submit();">
															<div class="material-icons md-14" data-toggle="tooltip" title="Terminar Muestra">check_circle</div>
														</a>
													@endif
													@endif

													@if((isset(auth()->user()->role->area) && $usuarioAsignado) && (Auth()->user()->isIngeniero() && Auth()->user()->isVendedor() ) )
														&nbsp;&nbsp;
														<a id="eliminar-muestra" data-toggle="modal" data-target="#modal-eliminar-muestra" href="#" class=" modalVerDetalle" data-id-excel="{{$muestra->ot_id_excel}}"  data-id="{{$muestra->id}} ">
															<div class="material-icons md-14" data-toggle="tooltip" title="Eliminar">delete</div>
														</a>
													@endif

													@endif
													@if( ($muestra->estado != 3 && $muestra->estado != 2) &&(auth()->user()->isJefeDesarrollo() ||auth()->user()->isIngeniero()) && $ot->current_area_id == 2)
														&nbsp;&nbsp;
														<a href="#" class="" onclick="event.preventDefault();$('#anularMuestraID').val({{$muestra->id}});$('#anularMuestraForm').submit();">
															<div class="material-icons md-14" data-toggle="tooltip" title="Anular Muestra">block</div>
														</a>
													@endif
													@if( ($muestra->estado == 3) &&(auth()->user()->isTecnicoMuestras() ||auth()->user()->isJefeMuestras() || auth()->user()->isJefeDesarrollo() ||auth()->user()->isIngeniero()))
														&nbsp;&nbsp;
														<a href="#" class=" modalVerDetalle" data-id="{{$muestra->id}}" data-id-excel="{{$muestra->ot_id_excel}}"  data-toggle="modal" data-target="#modal-muestras">
															<div class="material-icons md-14" data-toggle="tooltip" title="Ver">search</div>
														</a>
													@endif
																								</div>
										@endif
									@else
										@if(Auth()->user()->isSuperAdministrador())
											<div id="acciones_{{$muestra->id}}" class="form-group">
												<a href="#" class=" modalVerDetalle" data-id="{{$muestra->id}}" data-id-excel="{{$muestra->ot_id_excel}}"  data-toggle="modal" data-target="#modal-muestras">
													<div class="material-icons md-14" data-toggle="tooltip" title="Ver">search</div>
												</a>
											</div>
										@else
											<div id="acciones_{{$muestra->id}}" class="form-group">
												@if(auth()->user()->isJefeMuestras() ||Auth()->user()->isJefeDesarrollo())
													@if($muestra->prioritaria == 0)
														<form method="POST" action="{{ route('muestraPrioritaria', $muestra->id) }}" style="display: inline;">
															@method('put')
															@csrf
															<button class="btn_link" type="submit">
																<div class="material-icons md-14" data-toggle="tooltip" title="Marcar Prioritaria">check_circle</div>
															</button>
														</form>
													@else
														<form method="POST" action="{{ route('muestraNoPrioritaria', $muestra->id) }}" style="display: inline;">
															@method('put')
															@csrf
															<button class="btn_link" type="submit">
																<i class="material-icons md-14" data-toggle="tooltip" title="Marcar No Prioritaria">remove_circle</i>
															</button>
														</form>
													@endif
												@else

													{{--@if($ot->current_area_id==2 && (Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo()))--}}
													&nbsp;
													<a href="#" class=" modalVerDetalle" data-id="{{$muestra->id}}" data-id-excel="{{$muestra->ot_id_excel}}"  data-toggle="modal" data-target="#modal-muestras">

														@if($muestra->estado == 2 && !(Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo()))

														@elseif(Auth()->user()->isVendedor() || Auth()->user()->isJefeVenta())
															@if($ot->current_area_id==1)
																<div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
															@else
																<div class="material-icons md-14" data-toggle="tooltip" title="Ver">search</div>
															@endif

														@else
															@if((Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo())&&($ot->current_area_id==2))
																<div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
															@else
																@if((Auth()->user()->isTecnicoMuestras() || Auth()->user()->isJefeMuestras())&&($ot->current_area_id==6) && ($muestra->estado != 4 && $muestra->estado!=3))
																	<div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
																@else
																@endif
															@endif

														@endif

													</a>
													@if($muestra->estado != 2 && $muestra->estado!=3 && !(Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo()) )
														{{--<a href="#" class="" onclick="event.preventDefault();
															$('#rechazarMuestraID').val({{$muestra->id}});$('#rechazarMuestraForm').submit();">--}}
														&nbsp;&nbsp;
														<a href="#" id="rechazar_muestra" class="" onclick="event.preventDefault();rechazarMuestra({{$muestra->id}})">
															<div class="material-icons md-14" data-toggle="tooltip" title="Rechazar Muestra">close</div>
														</a>

													@endif
													@if($muestra->estado != 5 && $muestra->estado != 4 && $muestra->estado!=3 && $muestra->estado != 2 && (Auth()->user()->isTecnicoMuestras() || Auth()->user()->isJefeMuestras()))
														&nbsp;&nbsp;
														<a href="#" class="" onclick="event.preventDefault();devolverMuestra({{$muestra->id}})">
															<div class="material-icons md-14" data-toggle="tooltip" title="Devolver Muestra">undo</div>
														</a>
													@endif
													{{--@endif--}}


													@if( auth()->user()->isTecnicoMuestras())
														<?php
														$terminarMuestra = false;
														switch ($muestra->destinatarios_id[0]) {
															case "1":
																if ($muestra->check_fecha_corte_vendedor) {
																	$terminarMuestra = true;
																}
																break;
															case "2":
																if ($muestra->check_fecha_corte_diseñador) {
																	$terminarMuestra = true;
																}
																break;
															case "3":
																if ($muestra->check_fecha_corte_laboratorio) {
																	$terminarMuestra = true;
																}
																break;
															case "4":
																if (
																	$muestra->check_fecha_corte_1 ||
																	$muestra->check_fecha_corte_2 ||
																	$muestra->check_fecha_corte_3 ||
																	$muestra->check_fecha_corte_4
																) {
																	$terminarMuestra = true;
																}
																break;
															case "5":
																if ($muestra->check_fecha_corte_diseñador_revision) {
																	$terminarMuestra = true;
																}
																break;
															default:
																break;
														}
														// dd($terminarMuestra);
														?>
														@if($terminarMuestra && ($muestra->estado == 1 || $muestra->estado == 5) && $ot->current_area_id == 6)
															<br><br>
															<a href="#" class="" onclick="event.preventDefault();$('#terminarMuestraEnListadoID').val({{$muestra->id}});$('#terminarMuestraEnListadoForm').submit();">
																<div class="material-icons md-14" data-toggle="tooltip" title="Terminar Muestra">check_circle</div>
															</a>
														@endif
													@endif

														@if((isset(auth()->user()->role->area) && $usuarioAsignado) && (Auth()->user()->isIngeniero() && Auth()->user()->isVendedor() ) )
															&nbsp;&nbsp;
															<a id="eliminar-muestra" data-toggle="modal" data-target="#modal-eliminar-muestra" href="#" class=" modalVerDetalle" data-id-excel="{{$muestra->ot_id_excel}}"  data-id="{{$muestra->id}}">
																<div class="material-icons md-14" data-toggle="tooltip" title="Eliminar">delete</div>
															</a>
														@endif

												@endif
												@if( ($muestra->estado != 3 && $muestra->estado != 2 && $muestra->estado != 4) &&(auth()->user()->isJefeDesarrollo() ||auth()->user()->isIngeniero()) && $ot->current_area_id == 2)
													&nbsp;&nbsp;
													<a href="#" class="" onclick="event.preventDefault();$('#anularMuestraID').val({{$muestra->id}});$('#anularMuestraForm').submit();">
														<div class="material-icons md-14" data-toggle="tooltip" title="Anular Muestra">block</div>
													</a>
												@endif
												@if( ($muestra->estado == 3) &&(auth()->user()->isTecnicoMuestras() ||auth()->user()->isJefeMuestras() || auth()->user()->isJefeDesarrollo() ||auth()->user()->isIngeniero()))
													&nbsp;&nbsp;
													<a href="#" class=" modalVerDetalle" data-id-excel="{{$muestra->ot_id_excel}}"  data-id="{{$muestra->id}}" data-toggle="modal" data-target="#modal-muestras">
														<div class="material-icons md-14" data-toggle="tooltip" title="Ver">search</div>
													</a>
												@endif
												@if( ($muestra->estado == 2 || $muestra->estado == 5) &&(auth()->user()->isTecnicoMuestras()))
													&nbsp;&nbsp;
													<a href="#" class=" modalVerDetalle" data-id-excel="{{$muestra->ot_id_excel}}"  data-id="{{$muestra->id}}" data-toggle="modal" data-target="#modal-muestras">
														<div class="material-icons md-14" data-toggle="tooltip" title="Ver">search</div>
													</a>
												@endif
											</div>
										@endif
									@endif

								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					@else
					<br>
					<h5 class="text-center">Aún no se han registrados muestras</h5>
					@endif
				</div>
			</div>
		</div>

	</div>
</section>
@else
<a style="display: none;" href="#" id="crear_muestra" data-toggle="modal" data-target="#modal-muestras">Crear Muestra</a>
@endif
<style>
	.datepicker {
		z-index: 1151 !important;
	}
</style>




<!-- MODAL MUESTRAS -->
<div class="modal fade" id="modal-muestras">
	<div class="modal-dialog modal-xl " style="width:100%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title page-title">
					<h1 id="titulo-form-muestra" class="page-title">Crear Muestra</h1>
				</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="    background-color: #F2F4FD">
				<div id="" class="col-12 mb-2">
					<form onsubmit="$(this).find('input,select,textarea').prop('disabled', false)" id="form-muestra" action="{{ route('crear-muestra', isset($ot) ? $ot->id : 0) }}" method="POST">
						@csrf
						<input type="hidden" id="ot_id" name="ot_id" value="{{isset($ot) ? $ot->id : 0}}">
						<input type="hidden" id="muestra_id" name="muestra_id" value="">
						<input type="hidden" id="current_area_id" name="current_area_id" value="{{isset($ot) ?$ot->current_area_id: 0}}">
						<div class="row">
							<div class="col-12 mb-2">
								<div class="card">
									<div class="card-body inputs_muestra">
										<h1 class="page-title mb-3">Caracteristicas de Muestra</h1>
										<div class="row ">
											<div class="col-4">
												<div class="" id="cad_input_container_muestra" style="display:none">
													<!-- CAD:-->
													{!! armarInputCreateEditOT('cad', 'CAD:', 'text',$errors, $ot, 'form-control', '', '') !!}
												</div>
												<div class="" id="cad_select_container_muestra">
													<!-- CAD Select -->
													{!! armarSelectArrayCreateEditOT($cads, 'cad_id', 'CAD' , $errors, $ot ,'form-control form-element',true,true) !!}
												</div>

											</div>
											<div class="col-4">
												<!-- Cartón-->
												{!! armarSelectArrayCreateEditOT($cartons, 'carton_id', 'Cartón' , $errors, null ,'form-control',true,true) !!}
											</div>
											<div class="col-4">
												<!-- Tipo de Pegado-->
												{!! armarSelectArrayCreateEditOT([1 => "Sin Pegar", 2=>"Pegado Flexo Interior",3=>"Pegado Flexo Exterior",4=>"Pegado Diecutter",5=>"Pegado Cajas Fruta",6=>"Pegado con Cinta",7=>"Sin Pegar con Cinta"], 'pegado_id', 'Tipo de Pegado' , $errors, null ,'form-control',true,false) !!}
											</div>
											<!-- <div class="col-4"> -->
											<!-- Tiempo Unitario -->

											<!-- armarInputCreateEditOT('tiempo_unitario', 'Tiempo Unitario:', 'text', $errors, $ot, 'form-control', '', '') -->

											<!-- </div> -->
											<div class="col-4">
												<!-- Tiempo Unitario -->
												<div class="form-group form-row ">
													<label class="col-auto col-form-label">Tiempo Unitario</label>
													<div class="col">
														<input class="form-control form-control-sm datepickerH" id="tiempo_unitario" name="tiempo_unitario" autocomplete="off">
													</div>
												</div>
											</div>
											<div class="col-4">
												<!-- Cartón-->
												{!! armarSelectArrayCreateEditOT($cartons_muestra, 'carton_muestra_id', 'Cartón Muestra' , $errors, null ,'form-control',true,true) !!}
											</div>
										</div>


										<h1 class="page-title my-3">Destinos</h1>
										@if(auth()->user()->isJefeVenta()||auth()->user()->isVendedor() ||Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo() ||auth()->user()->isVendedorExterno())
										<div class="row">
											@else
											<div class="row" style="display: none;">
												@endif
												<div class="col-4">
													<div class="form-group form-row ">
														<label class="col-auto col-form-label">Enviar Muestras a</label>

														<div class="col">
															<select name="destinatarios_id[]" id="destinatarios_id" class="form-control form-control-sm" multiple title="Selecciona..." data-selected-text-format="count > 1" data-actions-box="false">
																{!! optionsSelectArrayfilterMultiple([2=>"Retira Diseñador VB",1=>"Retira Ventas VB",5=>"Retira Diseñador Revisión",3=>"Envío Laboratorio"],'destinatarios_id') !!}
															</select>
														</div>
													</div>
												</div>
											</div>

											<h4 class="text-center" style="color: #7f7f7f;">Datos de Muestra</h4>
											<div class="row muestra-destinatario muestra-vendedor" style="display:none">
												<div class="col-12">
													<h6 style=" font-size:20px;    text-align: center; color: #3aaa35; margin-top: 5px">
														Retira Ventas VB
													</h6>
												</div>
												<div class="col-2">
													<!-- Cantidad -->
													{!! armarInputCreateEditOT('cantidad_vendedor', 'Cantidad:', 'number', $errors, $ot, 'form-control', '', '') !!}
												</div>

												<div class="col-5">
													<!-- comentario_vendedor -->
													{!! armarInputCreateEditOT('comentario_vendedor', 'Forma de Entrega:', 'text', $errors, $ot, 'form-control comentario', '', '') !!}
												</div>
												@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras())
												<div class="col-2">
													<div class="custom-control custom-checkbox mb-1 form-group">
														<input type="checkbox" class="custom-control-input" name="check_fecha_corte_vendedor" id="check_fecha_corte_vendedor">
														<label class="custom-control-label" for="check_fecha_corte_vendedor">Fecha de Corte</label>
													</div>
													<!-- Fecha de Corte -->
													<!-- <div class="form-group form-row ">
														<label class="col-auto col-form-label">Fecha de Corte</label>
														<div class="col">
															<input class="form-control form-control-sm datepicker fecha_corte" id="fecha_corte_vendedor" name="fecha_corte_vendedor" autocomplete="off">
														</div>
													</div> -->
												</div>
												@endif
												@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras() || auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo())
													<div class="col-3">
														{!! armarSelectArrayCreateEditOT($salas_cortes, 'sala_corte_vendedor', 'Planta de Corte' , $errors, null ,'form-control',true,false) !!}
													</div>
												@endif
												 {{-- <div class="pdf-muestra col-2" style="display: none;">
													<a style="" class="link_pdf_muestra" target="_blank" href="{{ route('generar_etiqueta_muestra_pdf',['download'=>'pdf']) }}">
														<div class="material-icons md-16 ml-1" data-toggle="tooltip" title="Descargar Etiqueta Producto" style="color:#3aaa35;">file_download</div>
													</a>
												</div> --}}



											</div>
											<div class="row muestra-destinatario muestra-diseñador" style="display:none">
												<div class="col-12">
													<h6 style="font-size:20px;       text-align: center;color: #3aaa35; margin-top: 5px">
														Retira Diseñador VB
													</h6>
												</div>
												<div class="col-2">
													<!-- Cantidad -->
													{!! armarInputCreateEditOT('cantidad_diseñador', 'Cantidad:', 'number', $errors, $ot, 'form-control', '', '') !!}
												</div>
												<div class="col-5">
													<!-- Forma de Envío 1 -->
													{!! armarInputCreateEditOT('comentario_diseñador', 'Forma de Entrega:', 'text', $errors, $ot, 'form-control comentario', '', '') !!}
												</div>
												@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras())
												<div class="col-2">
													<div class="custom-control custom-checkbox mb-1 form-group">
														<input type="checkbox" class="custom-control-input" name="check_fecha_corte_diseñador" id="check_fecha_corte_diseñador">
														<label class="custom-control-label" for="check_fecha_corte_diseñador">Fecha de Corte</label>
													</div>
													<!-- Fecha de Corte -->
													<!-- <div class="form-group form-row ">
														<label class="col-auto col-form-label">Fecha de Corte</label>
														<div class="col">
															<input class="form-control form-control-sm datepicker fecha_corte" id="fecha_corte_diseñador" name="fecha_corte_diseñador" autocomplete="off">
														</div>
													</div> -->
												</div>
												@endif
												@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras() || auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo())
													<div class="col-3">
														{!! armarSelectArrayCreateEditOT($salas_cortes, 'sala_corte_diseñador', 'Planta de Corte' , $errors, null ,'form-control',true,false) !!}
													</div>
												@endif

											</div>
											<div class="row muestra-destinatario muestra-laboratorio" style="display:none">
												<div class="col-12">
													<h6 style=" font-size:20px;    text-align: center; color: #3aaa35;  margin-top: 5px">
														Envío Laboratorio
													</h6>
												</div>
												<div class="col-2">
													<!-- Cantidad -->
													{!! armarInputCreateEditOT('cantidad_laboratorio', 'Cantidad:', 'number', $errors, $ot, 'form-control', '', '') !!}
												</div>

												<div class="col-5">
													<!-- Forma de Envío 1 -->
													{!! armarInputCreateEditOT('comentario_laboratorio', 'Forma de Envío:', 'text', $errors, $ot, 'form-control comentario', '', '') !!}
												</div>
												@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras())
												<div class="col-2">
													<div class="custom-control custom-checkbox mb-1 form-group">
														<input type="checkbox" class="custom-control-input" name="check_fecha_corte_laboratorio" id="check_fecha_corte_laboratorio">
														<label class="custom-control-label" for="check_fecha_corte_laboratorio">Fecha de Corte</label>
													</div>
													<!-- Fecha de Corte -->
													<!-- <div class="form-group form-row ">
														<label class="col-auto col-form-label">Fecha de Corte</label>
														<div class="col">
															<input class="form-control form-control-sm datepicker fecha_corte" id="fecha_corte_laboratorio" name="fecha_corte_laboratorio" autocomplete="off">
														</div>
													</div> -->
												</div>
												@endif
												@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras() || auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo())
													<div class="col-3">
														{!! armarSelectArrayCreateEditOT($salas_cortes, 'sala_corte_laboratorio', 'Planta de Corte' , $errors, null ,'form-control',true,false) !!}
													</div>
												@endif
												<!-- <div class="pdf-muestra col-2" style="display: none;">
													<a style="" class="link_pdf_muestra" target="_blank" href="{{ route('generar_etiqueta_muestra_pdf',['download'=>'pdf']) }}">
														<div class="material-icons md-16 ml-1" data-toggle="tooltip" title="Descargar Etiqueta Producto" style="color:#3aaa35;">file_download</div>
													</a>
												</div> -->

											</div>
											<div class="muestra-destinatario muestra-clientes" style="display:none">
												<hr>
												<h6 style=" font-size:20px;    text-align: center; color: #3aaa35;  margin-top: 5px"> Envío Cliente VB</h6>
												<div class="row my-2 select-contactos-clientes">
													<!-- Contactos de Cliente 1 -->
													{!! armarSelectArrayCreateEdit([],"col-3", 'contactos_cliente_1', 'Contactos Cliente' , $errors, $ot ,'form-control form-element') !!}
												</div>
												<div class="row my-2">
													<div class="col-4">
														<!-- Destinatario 1 -->
														{!! armarInputCreateEditOT('destinatario_1', 'Destinatario 1:', 'text', $errors, $ot, 'form-control cliente_1', '', '') !!}
													</div>
													<div class="col-4">
														<!-- Comuna 1-->
														{!! armarSelectArrayCreateEditOT($comunas, 'comuna_1', 'Comuna 1' , $errors, null ,'form-control cliente_1',true,true) !!}
													</div>
													<div class="col-4">
														<!-- Dirección 1 -->
														{!! armarInputCreateEditOT('direccion_1', 'Dirección 1:', 'text', $errors, $ot, 'form-control cliente_1', '', '') !!}
													</div>
													<div class="col-3">
														<!-- Cantidad -->
														{!! armarInputCreateEditOT('cantidad_1', 'Cantidad 1:', 'number', $errors, $ot, 'form-control cliente_1', '', '') !!}
													</div>

													<div class="col-3">
														<!-- Forma de Envío 1 -->
														{!! armarSelectArrayCreateEditOT(["Chile Express","Auto Correo","Camión"], 'comentario_1', 'Forma de Envío 1' , $errors, $ot ,'form-control form-element cliente_1',true,true) !!}
													</div>
													<div class="col-3" id="contenedorNumeroEnvio1" style="display:none">
														<!-- N° Envío 1-->
														{!! armarInputCreateEditOT('numero_envio_1', 'N° Envío 1:', 'text', $errors, $ot, 'form-control', '', '') !!}
													</div>
													@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras())
													<div class="col-2">
														<div class="custom-control custom-checkbox mb-1 form-group">
															<input type="checkbox" class="custom-control-input" name="check_fecha_corte_1" id="check_fecha_corte_1">
															<label class="custom-control-label" for="check_fecha_corte_1">Fecha de Corte</label>
														</div>
														<!-- Fecha de Corte -->
														<!-- <div class="form-group form-row ">
															<label class="col-auto col-form-label">Fecha de Corte</label>
															<div class="col">
																<input class="form-control form-control-sm datepicker fecha_corte" id="fecha_corte_1" name="fecha_corte_1" autocomplete="off">
															</div>
														</div> -->
													</div>
													@endif
													@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras() || auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo())
														<div class="col-3">
															{!! armarSelectArrayCreateEditOT($salas_cortes, 'sala_corte_1', 'Planta de Corte' , $errors, null ,'form-control',true,false) !!}
														</div>
													@endif
													<!-- <div class="pdf-muestra col-1" style="display: none;">
														<a style="" class="link_pdf_muestra" target="_blank" href="{{ route('generar_etiqueta_muestra_pdf',['download'=>'pdf']) }}">
															<div class="material-icons md-16 ml-1" data-toggle="tooltip" title="Descargar Etiqueta Producto" style="color:#3aaa35;">file_download</div>
														</a>
													</div> -->
													<!-- <div class="pdf-muestra col-1" style="display: none;">
														<a style="" class="link_pdf_muestra" target="_blank" href="{{ route('generar_etiqueta_muestra_pdf',['download'=>'pdf']) }}">
															<div class="material-icons md-16 ml-1" data-toggle="tooltip" title="Etiqueta Dirección Envío Cliente" style="color:#3aaa35;">badge</div>
														</a>
													</div> -->

												</div>
												<hr class="contactos-clientes">
												<div class="row my-2 select-contactos-clientes">
													<!-- Contactos de Cliente 2 -->
													{!! armarSelectArrayCreateEdit([],"col-3", 'contactos_cliente_2', 'Contactos Cliente' , $errors, $ot ,'form-control form-element') !!}
												</div>
												<div class="row mb-2 contactos-clientes">
													<div class="col-4">
														<!-- Destinatario 2 -->
														{!! armarInputCreateEditOT('destinatario_2', 'Destinatario 2:', 'text', $errors, $ot, 'form-control cliente_2', '', '') !!}
													</div>
													<div class="col-4">
														<!-- Comuna 2-->
														{!! armarSelectArrayCreateEditOT($comunas, 'comuna_2', 'Comuna 2' , $errors, null ,'form-control cliente_2',true,true) !!}
													</div>
													<div class="col-4">
														<!-- Dirección 2 -->
														{!! armarInputCreateEditOT('direccion_2', 'Dirección 2:', 'text', $errors, $ot, 'form-control cliente_2', '', '') !!}
													</div>
													<div class="col-3">
														<!-- Cantidad 2-->
														{!! armarInputCreateEditOT('cantidad_2', 'Cantidad 2:', 'number', $errors, $ot, 'form-control cliente_2', '', '') !!}
													</div>
													<div class="col-3">
														<!-- Forma de Envío 1 -->
														{!! armarSelectArrayCreateEditOT(["Chile Express","Auto Correo","Camión"], 'comentario_2', 'Forma de Envío 2' , $errors, $ot ,'form-control form-element cliente_2',true,true) !!}
													</div>
													<div class="col-3" id="contenedorNumeroEnvio2" style="display:none">
														<!-- N° Envío 2-->
														{!! armarInputCreateEditOT('numero_envio_2', 'N° Envío 2:', 'text', $errors, $ot, 'form-control', '', '') !!}
													</div>
													@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras())
													<div class="col-2">
														<div class="custom-control custom-checkbox mb-1 form-group">
															<input type="checkbox" class="custom-control-input" name="check_fecha_corte_2" id="check_fecha_corte_2">
															<label class="custom-control-label" for="check_fecha_corte_2">Fecha de Corte</label>
														</div>
														<!-- Fecha de Corte -->
														<!-- <div class="form-group form-row ">
															<label class="col-auto col-form-label">Fecha de Corte</label>
															<div class="col">
																<input class="form-control form-control-sm datepicker fecha_corte" id="fecha_corte_2" name="fecha_corte_2" autocomplete="off">
															</div>
														</div> -->
													</div>
													<!-- <div class="pdf-muestra col-1" style="display: none;">
														<a style="" class="link_pdf_muestra" target="_blank" href="{{ route('generar_etiqueta_muestra_pdf',['download'=>'pdf']) }}">
															<div class="material-icons md-16 ml-1" data-toggle="tooltip" title="Descargar Etiqueta Producto" style="color:#3aaa35;">file_download</div>
														</a>
													</div> -->
													@endif
													@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras() || auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo())
														<div class="col-3">
															{!! armarSelectArrayCreateEditOT($salas_cortes, 'sala_corte_2', 'Planta de Corte' , $errors, null ,'form-control',true,false) !!}
														</div>
													@endif


												</div>
												<hr class="contactos-clientes">
												<div class="row my-2 select-contactos-clientes" style="display:none">
													<!-- Contactos de Cliente 3 -->
													{!! armarSelectArrayCreateEdit([],"col-3", 'contactos_cliente_3', 'Contactos Cliente' , $errors, $ot ,'form-control form-element') !!}
												</div>
												<div class="row mb-2 contactos-clientes">
													<div class="col-4">
														<!-- Destinatario 3 -->
														{!! armarInputCreateEditOT('destinatario_3', 'Destinatario 3:', 'text', $errors, $ot, 'form-control cliente_3', '', '') !!}
													</div>
													<div class="col-4">
														<!-- Comuna 3-->
														{!! armarSelectArrayCreateEditOT($comunas, 'comuna_3', 'Comuna 3' , $errors, null ,'form-control cliente_3',true,true) !!}
													</div>
													<div class="col-4">
														<!-- Dirección 3 -->
														{!! armarInputCreateEditOT('direccion_3', 'Dirección 3:', 'text', $errors, $ot, 'form-control cliente_3', '', '') !!}
													</div>
													<div class="col-3">
														<!-- Cantidad -->
														{!! armarInputCreateEditOT('cantidad_3', 'Cantidad 3:', 'number', $errors, $ot, 'form-control cliente_3', '', '') !!}
													</div>
													<div class="col-3">
														<!-- Forma de Envío 3 -->
														{!! armarSelectArrayCreateEditOT(["Chile Express","Auto Correo","Camión"], 'comentario_3', 'Forma de Envío 3' , $errors, $ot ,'form-control form-element cliente_3',true,true) !!}
													</div>
													<div class="col-3" id="contenedorNumeroEnvio3" style="display:none">
														<!-- N° Envío 3-->
														{!! armarInputCreateEditOT('numero_envio_3', 'N° Envío 3:', 'text', $errors, $ot, 'form-control', '', '') !!}
													</div>
													@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras())
													<div class="col-3">
														<div class="custom-control custom-checkbox mb-1 form-group">
															<input type="checkbox" class="custom-control-input" name="check_fecha_corte_3" id="check_fecha_corte_3">
															<label class="custom-control-label" for="check_fecha_corte_3">Fecha de Corte</label>
														</div>
														<!-- Fecha de Corte -->
														<!-- <div class="form-group form-row ">
															<label class="col-auto col-form-label">Fecha de Corte</label>
															<div class="col">
																<input class="form-control form-control-sm datepicker fecha_corte" id="fecha_corte_3" name="fecha_corte_3" autocomplete="off">
															</div>
														</div> -->
													</div>
													<!-- <div class="pdf-muestra col-1" style="display: none;">
														<a style="" class="link_pdf_muestra" target="_blank" href="{{ route('generar_etiqueta_muestra_pdf',['download'=>'pdf']) }}">
															<div class="material-icons md-16 ml-1" data-toggle="tooltip" title="Descargar Etiqueta Producto" style="color:#3aaa35;">file_download</div>
														</a>
													</div> -->
													@endif
													@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras() || auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo())
														<div class="col-3">
															{!! armarSelectArrayCreateEditOT($salas_cortes, 'sala_corte_3', 'Planta de Corte' , $errors, null ,'form-control',true,false) !!}
														</div>
													@endif
												</div>
												<hr class="contactos-clientes">
												<div class="row my-2 select-contactos-clientes" style="display:none">
													<!-- Contactos de Cliente 4 -->
													{!! armarSelectArrayCreateEdit([],"col-3", 'contactos_cliente_4', 'Contactos Cliente' , $errors, $ot ,'form-control form-element') !!}
												</div>
												<div class="row contactos-clientes">
													<div class="col-4">
														<!-- Destinatario 4 -->
														{!! armarInputCreateEditOT('destinatario_4', 'Destinatario 4:', 'text', $errors, $ot, 'form-control cliente_4', '', '') !!}
													</div>
													<div class="col-4">
														<!-- Comuna 4-->
														{!! armarSelectArrayCreateEditOT($comunas, 'comuna_4', 'Comuna 4' , $errors, null ,'form-control cliente_4',true,true) !!}
													</div>
													<div class="col-4">
														<!-- Dirección 4 -->
														{!! armarInputCreateEditOT('direccion_4', 'Dirección 4:', 'text', $errors, $ot, 'form-control cliente_4', '', '') !!}
													</div>
													<div class="col-3">
														<!-- Cantidad -->
														{!! armarInputCreateEditOT('cantidad_4', 'Cantidad 4:', 'number', $errors, $ot, 'form-control cliente_4', '', '') !!}
													</div>
													<div class="col-3">
														<!-- Forma de Envío 4 -->
														{!! armarSelectArrayCreateEditOT(["Chile Express","Auto Correo","Camión"], 'comentario_4', 'Forma de Envío 4' , $errors, $ot ,'form-control form-element cliente_4',true,true) !!}
													</div>
													<div class="col-3" id="contenedorNumeroEnvio4" style="display:none">
														<!-- N° Envío -->
														{!! armarInputCreateEditOT('numero_envio_4', 'N° Envío 4:', 'text', $errors, $ot, 'form-control', '', '') !!}
													</div>
													@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras())
													<div class="col-3">
														<div class="custom-control custom-checkbox mb-1 form-group">
															<input type="checkbox" class="custom-control-input" name="check_fecha_corte_4" id="check_fecha_corte_4">
															<label class="custom-control-label" for="check_fecha_corte_4">Fecha de Corte</label>
														</div>
														<!-- Fecha de Corte -->
														<!-- <div class="form-group form-row ">
															<label class="col-auto col-form-label">Fecha de Corte</label>
															<div class="col">
																<input class="form-control form-control-sm datepicker fecha_corte" id="fecha_corte_4" name="fecha_corte_4" autocomplete="off">
															</div>
														</div> -->
													</div>
													<!-- <div class="pdf-muestra col-1" style="display: none;">
														<a style="" class="link_pdf_muestra" target="_blank" href="{{ route('generar_etiqueta_muestra_pdf',['download'=>'pdf']) }}">
															<div class="material-icons md-16 ml-1" data-toggle="tooltip" title="Descargar Etiqueta Producto" style="color:#3aaa35;">file_download</div>
														</a>
													</div> -->
													@endif
													@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras() || auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo())
														<div class="col-3">
															{!! armarSelectArrayCreateEditOT($salas_cortes, 'sala_corte_4', 'Planta de Corte' , $errors, null ,'form-control',true,false) !!}
														</div>
													@endif


												</div>
											</div>

											<div class="row muestra-destinatario muestra-diseñador-revision" style="display:none">
												<div class="col-12">
													<h6 style="font-size:20px;       text-align: center;color: #3aaa35; margin-top: 5px">
														Retira Diseñador Revisión
													</h6>
												</div>
												<div class="col-2">
													<!-- Cantidad -->
													{!! armarInputCreateEditOT('cantidad_diseñador_revision', 'Cantidad:', 'number', $errors, $ot, 'form-control', '', '') !!}
												</div>
												<div class="col-5">
													<!-- Forma de Envío 1 -->
													{!! armarInputCreateEditOT('comentario_diseñador_revision', 'Forma de Entrega:', 'text', $errors, $ot, 'form-control comentario', '', '') !!}
												</div>
												@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras())
												<div class="col-2">
													<div class="custom-control custom-checkbox mb-1 form-group">
														<input type="checkbox" class="custom-control-input" name="check_fecha_corte_diseñador_revision" id="check_fecha_corte_diseñador_revision">
														<label class="custom-control-label" for="check_fecha_corte_diseñador_revision">Fecha de Corte</label>
													</div>
													<!-- Fecha de Corte -->
													<!-- <div class="form-group form-row ">
														<label class="col-auto col-form-label">Fecha de Corte</label>
														<div class="col">
															<input class="form-control form-control-sm datepicker fecha_corte" id="fecha_corte_diseñador" name="fecha_corte_diseñador" autocomplete="off">
														</div>
													</div> -->
												</div>
												@endif
												@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras() || auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo())
													<div class="col-3">
														{!! armarSelectArrayCreateEditOT($salas_cortes, 'sala_corte_diseñador_revision', 'Planta de Corte' , $errors, null ,'form-control',true,false) !!}
													</div>
												@endif

											</div>

											@if(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras())
											<hr>
											<h6 style=" font-size:20px;    text-align: center; color: #3aaa35;  margin-top: 5px"> Etiquetas PDF</h6>
											<div class="row">
												<div class="pdf-muestra col text-center form-group " style="display:flex;align-content:center;justify-content:center">
													<label class="col-form-label">Producto</label>
													<a style="" id="link_pdf_muestra" target="_blank" href="{{ route('generar_etiqueta_muestra_pdf',['download'=>'pdf']) }}">
														<div class="material-icons md-16 ml-1" data-toggle="tooltip" title="Descargar Etiqueta Producto" style="color:#3aaa35;">file_download</div>
													</a>
												</div>
												<div class="pdf-muestra direccion-envio-cliente col text-center form-group " style="display:flex;align-content:center;justify-content:center">
													<label class="col-form-label">Direccion Envio Cliente</label>

													<a style="" id="link_pdf_cliente" target="_blank" href="{{ route('generar_etiqueta_muestra_pdf',['download'=>'pdf']) }}">
														<div class="material-icons md-16 ml-1" data-toggle="tooltip" title="Etiqueta Dirección Envío Cliente" style="color:#3aaa35;">badge</div>
													</a>
												</div>
											</div>
											@endif


										</div>
									</div>
								</div>


							</div>
							<div class="row">
								@if((auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras()) && $ot->current_area_id == 6)

								<div class="col-8 row text-center mt-3">
									<div class="col-6">
										<!--<button id="rechazar_muestra" data-id="" >Rechazar Muestra</button> -->
										{{--<a id="rechazarMuestra" href="{{ route('rechazarMuestra') }}" class="btn btn-danger btn-block" onclick="event.preventDefault();
                        $('#rechazarMuestraForm').submit();">
						<a href="#" id="rechazar_muestra">Rechazar Muestra</a>--}}
									</div>
									<div class="col-6">
										<!-- <button id="terminarMuestra" data-id="" type="submit" class="btn btn-success float-right creacion" style="display: none;">Muestra Terminada</button> -->
										<a style="display: none;" id="terminarMuestra" href="{{ route('terminarMuestra') }}" class="btn btn-success btn-block" onclick="event.preventDefault();
                        $('#terminarMuestraForm').submit();">Muestra Terminada</a>
									</div>
								</div>
								@endif
								<div class="col mt-3 text-right">
									@if(Auth()->user()->isSuperAdministrador())
										<button id="limpiarMuestra" class="btn btn-light" data-dismiss="modal">Cerrar</button>
									@else
										@if((isset(auth()->user()->role->area) && isset($usuarioAsignado)) && ( Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeMuestras() || Auth()->user()->isTecnicoMuestras()|| auth()->user()->isJefeVenta()||auth()->user()->isVendedor()))
											<button id="guardarMuestra" type="submit" class="btn btn-success float-right creacion">Guardar Muestra</button>
										@else
											<button id="guardarMuestraVendedor" type="submit" class="btn btn-success float-right creacion">Guardar Muestra</button>
										@endif
										<button id="limpiarMuestra" class="btn btn-light" data-dismiss="modal">Cancelar</button>
									@endif
								</div>
							</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- FIN MODAL MUESTRAS -->
<!-- MODAL ELIMNAR MUESTRA -->
<div class="modal fade" id="modal-eliminar-muestra">
	<div class="modal-dialog modal-lg " style="width:60%">
		<div class="modal-content modal-confirmacion">
			<div class="modal-header text-center">
				<div class="title">Confirmar Eliminación de Muestra</div>
			</div>
			<div class="modal-body">
				<h6>Una vez confirmado se eliminara la Muestra correspondiente, esta opción es definitiva.
				</h6>
				<div class=" mt-4 text-center">
					<button class="btn btn-light" data-dismiss="modal">Cancelar</button>
					<button type="submit" id="botonEliminarMuestra" data-id="" class="btn btn-success mx-2">Continuar</button>
				</div>
			</div>
		</div>
	</div>
</div>

{{--<form id="rechazarMuestraForm" action="{{route('rechazarMuestra') }}" method="POST" style="display: none;">
	<input type="hidden" id="rechazarMuestraID" name="rechazarMuestraID" value="">
	@csrf
</form>-->--}}

<form id="terminarMuestraForm" action="{{ route('terminarMuestra') }}" method="POST" style="display: none;">
	<input type="hidden" id="terminarMuestraID" name="terminarMuestraID" value="">
	@csrf
</form>
<form id="terminarMuestraEnListadoForm" action="{{ route('terminarMuestra') }}" method="POST" style="display: none;">
	<input type="hidden" id="terminarMuestraEnListadoID" name="terminarMuestraEnListadoID" value="">
	@csrf
</form>
<form id="anularMuestraForm" action="{{ route('anularMuestra') }}" method="POST" style="display: none;">
	<input type="hidden" id="anularMuestraID" name="anularMuestraID" value="">
	@csrf
</form>
<form id="devolverMuestraForm" action="{{ route('devolverMuestra') }}" method="POST" style="display: none;">
	<input type="hidden" id="devolverMuestraID" name="devolverMuestraID" value="">
	@csrf
</form>


<!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<script>


document.querySelector('input[type="file"]').addEventListener('change', function () {
    // Restaurar valores predeterminados al cambiar archivo
    $('#validacion_excel').val('');
    // $('#btnGuardarOT').prop('disabled', true);

    // (Opcional) Limpiar mensajes anteriores si tienes algún label visible
    // $('#lbl_validacion').text('');
});

    function validarArchivo() {
        const input = document.querySelector('input[type="file"]');
        const archivo = input.files[0];

        if (!archivo) {
            toastr.warning("Debe seleccionar un archivo para validar.");
            return;
        }

        const formData = new FormData();
        formData.append('archivo', archivo);

        fetch('{{ route('validarExcel') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.validado) {
                    toastr.success("Archivo válido. Puedes guardar la OT.");
                    // $('#lbl_validacion').text('Archivo Validado');
                    $('#validacion_excel').val('Validado');
                } else {
                    data.errores.forEach(error => {
                        toastr.error(error.mensaje);
                    });
                    $('#validacion_excel').val('');
                }
            })
            .catch(err => {
                console.error(err);
                toastr.error("Error al validar el archivo.");
            });
    }
</script>
