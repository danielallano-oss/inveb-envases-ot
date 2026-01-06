<!-- MODAL DETALLE DE COTIZACION -->
<div class="modal fade" id="modal-carga-material">
	<div class="modal-dialog modal-xl " style="width:100%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title page-title">
					<h1 id="titulo-form-detalle" class="page-title">Cargar Material</h1>

				</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="    background-color: #F2F4FD">
				<div id="" class="col-12 mb-2">
					<form method="POST" id="form-carga-material" enctype="multipart/form-data">
						@csrf
						<!-- <input type="hidden" id="detalle_cotizacion_id" name="detalle_cotizacion_id" value=""> -->
						<div class="row">
							<div class="col-12 mb-2">
								<div class="card">
									<div class="card-body">
										<div class="row">
											<div class="col-2">
												<!-- codigo_material -->
												{!! armarInputCreateEditOT('codigo_material', 'Codigo:', 'text',$errors, $cotizacion, 'form-control', 'min="1"', '') !!}
											</div>
											<div class="col-5">
												<!-- descripcion_material -->
												{!! armarInputCreateEditOT('descripcion_material', 'Descripción:', 'text',$errors, $cotizacion, 'form-control', 'min="1"', '') !!}
											</div>
											<div class="col-2">
												<!-- cad -->
												{!! armarInputCreateEditOT('cad', 'CAD:', 'text',$errors, $cotizacion, 'form-control', 'min="1"', '') !!}
											</div>
											<div class="col-3">
												<!-- Estilo-->
												{!! armarSelectArrayCreateEditOT($styles, 'style_id', 'Estilo' , $errors, $cotizacion ,'form-control',true,true) !!}

											</div>
										</div>
										<div class="mt-3 text-right">
											<button type="submit" class="btn btn-success float-right creacion">{{ isset($cotizacion->id) ? __('Buscar Materiales') : __('Buscar Materiales') }}</button>
										</div>
										<!-- MUESTRA RESULTADOS DE CARGA -->
										<div id="resultados-carga-materiales" class="row" style="margin-top:50px">
											<div class="col-12 mb-5">
												<div class="infoBox">
													<div class="title">Materiales Encontrados
														<div id="total-materiales-carga" class="badge infoInverse right">{{count([0])}}</div>
													</div>
													<div class="content height200">
														<table id="materiales-carga" class="table table-status table-hover table-bordered ">
															<thead>
																<tr>
																	<th>Descripción</th>
																	<th width="100px">Código</th>
																	<!-- <th>Tipo Producto</th> -->
																	<th width="100px">Cartón</th>
																	<th width="100px">CAD</th>
																	<th>Estilo</th>
																	<th width="100px">Item</th>
																	<th width="100px">Acciones</th>
																	<!-- <th>Proceso</th> -->
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td colspan="7"></td>
																</tr>
															</tbody>
														</table>
														<!-- <button id="sincronizarMaterial" class="btn btn-success float-right creacion mt-2">Sincronizar Material</button> -->
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
								<div class="mt-3 text-right">
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