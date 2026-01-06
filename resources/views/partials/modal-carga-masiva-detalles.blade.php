<!-- MODAL DETALLE DE COTIZACION -->
<div class="modal fade" id="modal-carga-masiva-detalles">
	<div class="modal-dialog modal-xl " style="width:100%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title page-title">
					<h1 id="titulo-form-detalle" class="page-title">Cargar Detalles</h1>

				</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="    background-color: #F2F4FD">
				<div id="" class="col-12 mb-2">
					<form method="POST" id="form-carga-masiva" enctype="multipart/form-data">
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
												<div class="col-12 text-center card-header mb-3">Descargar Archivos de Ejemplo</div>
												<div class="col-6 text-center">
													<a class="btn btn-success" data-attribute="link" href="/files/Carga Masiva Corrugados.xlsx" download title="Descargar">Corrugados</a>
												</div>
												<div class="col-6 text-center">
													<a class="btn btn-success" data-attribute="link" href="/files/Carga Masiva Esquineros.xlsx" download title="Descargar">Esquineros</a>
												</div>
											</div>
											<div class="col-8 text-center">
												<div class="col-12  card-header mb-3">Carga Masiva</div>
												<input type="file" class="file" name="archivo" id="archivo" required />
											</div>
											<!-- <div class="col-4">

											</div> -->


										</div>
										<!-- MUESTRA RESULTADOS DE CARGA -->
										<div id="resultados-carga-masiva" class="row" style="margin-top:50px;display: none">
											<div class="col-12 mb-5">
												<div class="infoBox">
													<div class="title">Detalles Ingresados
														<div id="total-detalles-carga" class="badge infoInverse right">{{count([0])}}</div>
													</div>
													<div class="content height200">
														<table id="detalles-carga" class="table table-status table-hover table-bordered ">
															<thead>
																<tr>
																	<th>Tipo Producto</th>
																	<th>Cantidad</th>
																	<th style="width:50px">Área</th>
																	<th>Cartón</th>
																	<th>Item</th>
																	<th>Proceso</th>
																	<!-- <th>Pegado</th> -->
																	<th>Golpes Ancho</th>
																	<th>Golpes Largo</th>
																	<th style="width:55px">Colores</th>
																	<th>% impr.</th>
																	<th style="width:50px">Cera</th>
																	<th style="width:50px">Matriz</th>
																	<th style="width:50px">Clisse</th>
																	<th style="width:60px">Royalty</th>
																	<th style="width:60px">Maquila</th>
																	<th style="width:60px">Armado</th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td colspan="17"></td>

																</tr>
															</tbody>

														</table>
														<button id="sincronizarDetalles" class="btn btn-success float-right creacion mt-2">Sincronizar Detalles</button>
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="infoBox">
													<div class="title">Detalles Invalidos
														<div id="total-detalles-invalidos-carga" class="badge infoInverse right">{{count([0])}}</div>
													</div>
													<div class="content height200">
														<table id="detalles-invalidos-carga" class="table table-status table-hover table-bordered ">
															<thead>
																<tr>
																	<th style="width:80px">Linea Excel</th>
																	<th>Motivos</th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td>
																	</td>
																	<td>
																	</td>

																</tr>
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
								<div class="mt-3 text-right">
									<button id="guardarCargaMasiva" type="submit" class="btn btn-success float-right creacion">{{ isset($cotizacion->id) ? __('Cargar Detalles') : __('Cargar Detalles') }}</button>
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