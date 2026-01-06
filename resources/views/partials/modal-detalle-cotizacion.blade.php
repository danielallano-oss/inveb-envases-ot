<!-- MODAL DETALLE DE COTIZACION -->
<div class="modal fade" id="modal-detalle-cotizacion">
	<div class="modal-dialog modal-xl " style="width:100%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title page-title">
					<h1 id="titulo-form-detalle" class="page-title">Crear Detalle</h1>
				</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="    background-color: #F2F4FD">
				<div id="" class="col-12 mb-2">
					<form action="" id="form-detalle-cotizacion">
						@csrf
						<input type="hidden" id="work_order_id" name="work_order_id" value="">
						<input type="hidden" id="detalle_cotizacion_id" name="detalle_cotizacion_id" value="">
						<input type="hidden" id="cad_material_id" name="cad_material_id" value="">
						<input type="hidden" id="material_id" name="material_id" value="">
						<input type="hidden" id="interno_largo_med" name="interno_largo_med" value="">
						<input type="hidden" id="interno_ancho_med" name="interno_ancho_med" value="">
						<input type="hidden" id="interno_alto_med" name="interno_alto_med" value="">
						<input type="hidden" id="externo_largo_med" name="externo_largo_med" value="">
						<input type="hidden" id="externo_ancho_med" name="externo_ancho_med" value="">
						<input type="hidden" id="externo_alto_med" name="externo_alto_med" value="">
						<div class="row">
							<div class="col-12 mb-2">
								<div class="card">
									<div class="card-header">SELECCIONAR TIPO DE PRODUCTO</div>
									<div class="card-body" style="    padding: 0.25px 1.25rem;">
										<div class="row mb-3">
											<div class="col-4 offset-4">
												<!-- Tipo de detalle  ,3=>"Offset",4=>"Pulpa" -->
												{!! armarSelectArrayCreateEditOT([1=>"Corrugado",2=>"Esquinero"], 'tipo_detalle_id', 'Tipo de Producto' , $errors, $cotizacion ,'form-control form-element',false,false) !!}
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-12 mb-2">
								<div class="card">
									<div id="encabezado_tipo_detalle" class="card-header" style="    text-align: center;color: black;font-size: 18px;">Corrugado</div>
									<div class="card-header">Caracteristicas</div>
									<div class="card-body">
										<div class="row">
											<div id="divCargaMaterial" class="col-4 input_detalle_cotizacion fragmento_formulario_corrugado">

												<a href="#" id="cargarMaterial" class="btn btn-success btn-block float-right text-center" style="display:flex;align-items:center" data-toggle="modal" data-target="#modal-carga-material">Buscar por Material <div class="material-icons md-14 ml-1" data-toggle="tooltip" title="Ver" style="color:white;align-items: center;">search</div></a>
											</div>
											<div class="col-4 offset-4">
											</div>

										</div>
										<br>
										<!-- Formulario de corrugado -->
										<div class="row input_detalle_cotizacion fragmento_formulario_corrugado">
											<div class="col-4">
												<div class="row">
													<div class="col-8 calculo-hc-div">
														<!-- Cartón-->
														{!! armarSelectArrayCreateEditOT($cartons, 'carton_id', 'Cartón' , $errors, $cotizacion ,'form-control',true,true) !!}

													</div>
													<div class="col-4 calculo-hc-boton" style="padding:0px">
														<a href="#" data-toggle="modal" data-target="#modal-calculo-hc" id="calculoHC" class="btn btn-success btn-block btn-sm">Estimación Cartón</a>
													</div>
												</div>
												<div class="row">
													<div class="col-8 calculo-hc-div">
														<!-- Area HC (m2) -->
														{!! armarInputCreateEditOT('area_hc', 'Area HC (m2):', 'number',$errors, $cotizacion, 'form-control', 'min="0" max="99"', '') !!}

													</div>
													<div class="col-4 calculo-hc-boton" style="padding:0px">
														<a href="#" data-toggle="modal" data-target="#modal-calculo-hc" id="calculoCarton" class="btn btn-success btn-block btn-sm">Cálculo AHC</a>
													</div>
												</div>
												<div class="row">
													<div class="col-11">
														<!-- Anchura (mm) -->
														{!! armarInputCreateEditOT('anchura', 'Ancho Hoja Madre (mm):', 'number',$errors, $cotizacion, 'form-control', '', '') !!}

													</div>
													<div class="col-1" style="padding:0px;margin-top: 6px;">
														<div class="material-icons md-18" data-html="true" title="<img src='https://envases-ot.inveb.cl/img/anchoHM.png' />" data-toggle="tooltip">help_outline</div>
													</div>
												</div>
												<div class="row">
													<div class="col-11">
														<!-- Largura (mm) -->
														{!! armarInputCreateEditOT('largura', 'Largo Hoja Madre (mm):', 'number',$errors, $cotizacion, 'form-control', '', '') !!}

													</div>
													<div class="col-1" style="padding:0px;margin-top: 6px;">
														<div class="material-icons md-18" data-html="true" title="<img src='https://envases-ot.inveb.cl/img/largoHM.png' />" data-toggle="tooltip">help_outline</div>
													</div>
												</div>

												<!-- TIPO ITEM -->
												{!! armarSelectArrayCreateEditOT($productTypes, 'product_type_id', 'Tipo item' , $errors, $cotizacion ,'form-control',true,true) !!}
												
												<!-- Maquina Impresora -->
												{!! armarSelectArrayCreateEditOT($printingMachines, 'printing_machine_id', 'Máquina Impresora' , $errors, $cotizacion ,'form-control',true,false) !!}
												
												<!-- Impresion / Tipo de maquina -->
												{!! armarSelectArrayCreateEditOT($printTypes, 'print_type_id', 'Impresión' , $errors, $cotizacion ,'form-control',true,false,'255px') !!}
													
												<!-- Numero Colores-->
												{!! armarSelectArrayCreateEditOT([0,1,2,3,4,5,6], 'numero_colores', 'Número Colores' , $errors, $cotizacion ,'form-control',true,true) !!}

												<div class="row">
													<div class="col-6">
														<!-- Golpes largo -->
														{!! armarInputCreateEditOT('golpes_largo', 'Golpes largo:', 'number',$errors, $cotizacion, 'form-control', 'min="0" max="20"', '',1) !!}
													</div>
													<div class="col-6">
														<!-- Golpes ancho -->
														{!! armarInputCreateEditOT('golpes_ancho', 'Golpes ancho:', 'number',$errors, $cotizacion, 'form-control', 'min="0" max="20"', '',1) !!}
													</div>
												</div>
											</div>

											<div class="col-4">
												<!-- Proceso -->
												{!! armarSelectArrayCreateEditOT($procesos, 'process_id', 'Proceso' , $errors, $cotizacion ,'form-control',true,false) !!}

												<!-- Pegados -->
												{!! armarSelectArrayCreateEditOT($pegados, 'pegado_id','Tipo de Pegado' , $errors, $cotizacion ,'form-control',true,false) !!}

												<div id="tipo_tinta" style="display: none;">
													<!-- Tipo Tinta -->
													{!! armarSelectArrayCreateEditOT($inkTypes, 'ink_type_id', 'Tipo Tinta' , $errors, $cotizacion ,'form-control',true,true) !!}
												</div>
												<!-- Cinta Desgarro-->
												{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'cinta_desgarro', 'Cinta Desgarro' , $errors, $cotizacion ,'form-control',true,false) !!}
												<!-- Cobertura (Barniz/Cera) -->
												{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'barniz', 'Barniz' , $errors, $cotizacion ,'form-control',true,false) !!}
												<!-- Tipo de Cobertura -->
												{!! armarSelectArrayCreateEditOT($tiposBarniz, 'barniz_type_id', 'Tipo de Barniz' , $errors, $cotizacion ,'form-control',true,false) !!}

												<!-- Pegado -->
												<!-- armarSelectArrayCreateEditOT([ 0=>"No",2=>"Interno",3=>"Externo"], 'pegado_terminacion', 'Pegado' , $errors, $cotizacion ,'form-control',true,false) -->
												
												<!-- NOTA: se agrega cobertura de barniz, ahora como esta cobertura barniz y cera se deja el nombre de porcentaje_cera_interno y porcentaje_cera_externo
												Pero su valor para los calculo de las formulas, se va a validar de si la cobertura es barniz o cera -->
												{{---
												<!-- % Interior -->
												{!! armarInputCreateEditOT('porcentaje_cera_interno', '% Interior:', 'number',$errors, $cotizacion, 'form-control autofill-value', 'min="0" max="100"', '',0) !!}
												<!-- % Exterior-->
												{!! armarInputCreateEditOT('porcentaje_cera_externo', '% Exterior:', 'number',$errors, $cotizacion, 'form-control autofill-value', 'min="0" max="100"', '',0) !!}
												
												<!-- % Impresión -->
												<!-- armarInputCreateEditOT('impresion', '% Impresión:', 'number',$errors, $cotizacion, 'form-control autofill-value', 'min="0" max="100"', '',0)  -->
												{!! armarSelectArrayCreateEditOT([0,25,50,75,100], 'impresion', '% Impresión:' , $errors, $cotizacion ,'form-control autofill-value', 'min="0" max="100"', '',0)!!}
												--}}
												<!-- Cobertura color (%) -->
												{!! armarInputCreateEditOT('cobertura_color_percent', 'Cobertura color (%):', 'number',$errors, $cotizacion, 'form-control autofill-value', 'min="0"', '',0) !!}
												<!-- Cobertura barniz (cm2)-->
												{!! armarInputCreateEditOT('cobertura_barniz_cm2', 'Cobertura barniz (cm2):', 'number',$errors, $cotizacion, 'form-control autofill-value', 'min="0"', '',0) !!}
												<!-- Clisse por un golpe (cm2) -->
												{!! armarInputCreateEditOT('cobertura_color_cm2', 'Clisse por un golpe (cm2):', 'number',$errors, $cotizacion, 'form-control autofill-value', 'min="0"', '',0) !!}
											</div>

											<div class="col-4">
												<!-- Pallet-->
												{!! armarSelectArrayCreateEditOT($pallets, 'pallet', 'Pallet' , $errors, $cotizacion ,'form-control',true,false) !!}

												<!-- Altura Pallet-->
												{!! armarSelectArrayCreateEditOT($alturaPallets, 'pallet_height_id', 'Altura Pallet' , $errors, $cotizacion ,'form-control',true,false) !!}
												{{--
												<div id="tipo_pallet" style="display: none;">
													<!-- Tipo de Pallet-->
													{!! armarSelectArrayCreateEditOT($palletTypes, 'pallet_type_id', 'Tipo Pallet' , $errors, $cotizacion ,'form-control',true,false) !!}
												</div>--}}
												<!-- Zunchos-->
												{!! armarSelectArrayCreateEditOT($zunchos, 'zuncho', 'Zunchos' , $errors, $cotizacion ,'form-control',true,false) !!}

												<!-- funda-->
												{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'funda', 'Funda' , $errors, $cotizacion ,'form-control',true,false) !!}

												<!-- stretch_film-->
												{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'stretch_film', 'Strech Film' , $errors, $cotizacion ,'form-control',true,false) !!}

												<!-- Ensamblado-->
												{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'ensamblado', 'Ensamblado' , $errors, $cotizacion ,'form-control',true,false) !!}

												<!-- Desgajado Cabezal-->
												{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'desgajado_cabezal', 'Desgajado Cabezal' , $errors, $cotizacion ,'form-control',true,false) !!}

												<!-- Rubro-->
												{!! armarSelectArrayCreateEditOT($rubros, 'rubro_id', 'Rubro' , $errors, $cotizacion ,'form-control',true,true) !!}

											</div>

										</div>

										<!-- Inputs de offset -->
										<div id="inputs_offset" class="row input_detalle_cotizacion " style="display: none;">
											<div class="col-12 text-center">
												<div class="card-header">Inputs OFFSET</div>
											</div>
											<div class="col-4">
												<!-- Ancho Pliego Cartulina (mm) -->
												{!! armarInputCreateEditOT('ancho_pliego_cartulina', 'Ancho Pliego Cartulina (mm):', 'number',$errors, $cotizacion, 'form-control', '', '') !!}
												<!-- Largo Pliego Cartulina (mm)-->
												{!! armarInputCreateEditOT('largo_pliego_cartulina', 'Largo Pliego Cartulina (mm):', 'number',$errors, $cotizacion, 'form-control', '', '') !!}
											</div>
											<div class="col-4">
												<!-- Precio Pliego Cartulina ($/Un) -->
												{!! armarInputCreateEditOT('precio_pliego_cartulina', 'Precio Pliego Cartulina ($/Un):', 'number',$errors, $cotizacion, 'form-control', '', '') !!}
												<!-- Precio Impresión Pliego ($/Un)-->
												{!! armarInputCreateEditOT('precio_impresion_pliego', 'Precio Impresión Pliego ($/Un):', 'number',$errors, $cotizacion, 'form-control', '', '') !!}
											</div>
											<div class="col-4">
												<!-- GP Emplacado (UN/GP)-->
												{!! armarInputCreateEditOT('gp_emplacado', 'GP Emplacado (UN/GP):', 'number',$errors, $cotizacion, 'form-control', '', '') !!}
											</div>
										</div>

										<!-- Formulario de Esquineros -->
										<div class="row input_detalle_cotizacion fragmento_formulario_esquinero" style="display: none;">
											<div class="col-4">
												<!-- Largo o medida (m) -->
												{!! armarInputCreateEditOT('largo_esquinero', 'Largo o medida (m):', 'number',$errors, $cotizacion, 'form-control', 'min="0" max="3"', '') !!}
												<!-- Cartón-->
												{!! armarSelectArrayCreateEditOT($cartonesEsquinero, 'carton_esquinero_id', 'Cartón' , $errors, $cotizacion ,'form-control',true,true) !!}
												<!-- Numero Colores-->
												{!! armarSelectArrayCreateEditOT([0,1,2], 'numero_colores_esquinero', 'Número Colores' , $errors, $cotizacion ,'form-control',true,true) !!}
												<!-- Incluye funda?-->
												{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'funda_esquinero', 'Incluye funda?' , $errors, $cotizacion ,'form-control',true,false) !!}

											</div>

										</div>
									</div>
								</div>
							</div>
							<div class="col-12 mb-2">
								<div class="row">
									<div class="col-8">
										<div class="card">
											<div class="card-header">CAMPOS OPCIONALES (DATOS PARA INCLUIR EN LA CARTA DE OFERTA)</div>
											<div class="card-body">
												<!-- Formulario de Corrugado -->
												<div class="row input_detalle_cotizacion ">
													<div class="col-6 fragmento_formulario_corrugado">
														<!--Medidas-->
														{!! armarSelectArrayCreateEditOT([1 => "Internas", 2=>"Externas"], 'tipo_medida', 'Medidas' , $errors, $cotizacion ,'form-control',false,false) !!}

														<!-- Largo (mm) -->
														{!! armarInputCreateEditOT('largo', 'Largo (mm):', 'number',$errors, $cotizacion, 'form-control', '', '') !!}
														<!-- Ancho (mm) -->
														{!! armarInputCreateEditOT('ancho', 'Ancho (mm):', 'number',$errors, $cotizacion, 'form-control', '', '') !!}
														<!-- Alto (mm) -->
														{!! armarInputCreateEditOT('alto', 'Alto (mm):', 'number',$errors, $cotizacion, 'form-control', '', '') !!}

														<div class="form-group form-row">
															<div class="col-6">
																<!-- BCT MIN (LB)-->
																{!! armarInputCreateEditOT('bct_min_lb', 'BCT MIN (LB):', 'number',$errors, $cotizacion, 'form-control', '', '') !!}
															</div>
															<div class="col-6">
																<!-- BCT MIN (KG)-->
																<div class="form-group form-row">
																	<label class="col-auto col-form-label" for="">BCT MIN (KG):</label>
																	<div class="col">
																		<input id="bct_min_kg" name="bct_min_kg" type="text" class="form-control" readonly="">
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="col-6">
														<!-- descripcion (material) -->
														{!! armarInputCreateEditOT('descripcion_material_detalle', 'Descripción (material):', 'text',$errors, $cotizacion, 'form-control', '', '') !!}
														<!-- CAD (material) -->
														{!! armarInputCreateEditOT('cad_material_detalle', 'CAD (material):', 'text',$errors, $cotizacion, 'form-control', '', '') !!}
														<!-- Cod. interno cliente -->
														{!! armarInputCreateEditOT('codigo_cliente', 'Cod. interno cliente:', 'number',$errors, $cotizacion, 'form-control', '', '') !!}
														<!-- Cláusula Devolución de Pallets-->
														{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'devolucion_pallets', 'Cláusula Devolución de Pallets' , $errors, $cotizacion ,'form-control',false,false) !!}
														<!-- Cláusula Ajuste de Precios-->
														{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'ajuste_precios', 'Cláusula Ajuste de Precios' , $errors, $cotizacion ,'form-control',false,false) !!}


													</div>


												</div>
												<!-- Formulario de Esquineros -->
												<div class="row input_detalle_cotizacion fragmento_formulario_esquinero" style="display: none;">
													<div class="col-4">

													</div>

												</div>
											</div>
										</div>
									</div>
									<div class="col-4" style="padding-left: 0px;">
										<div class="card" style="height: 100%;">
											<div class="card-header">CAMPOS OPCIONALES (SOLO EN CASO QUE SE QUIERA CONVERTIR COTIZACIÓN A OT)</div>
											<div class="card-body">
												<div class=" fragmento_formulario_corrugado">
													<!-- codigo (material) -->
													{!! armarInputCreateEditOT('codigo_material_detalle', 'Código (material):', 'text',$errors, $cotizacion, 'form-control', '', '') !!}
													<!-- Jerarquía 1-->
													{!! armarSelectArrayCreateEditOT($hierarchies, 'hierarchy_id', 'Jerarquía 1' , $errors, $cotizacion ,'form-control',true,true) !!}
													<!-- Jerarquía 2-->
													{!! armarSelectArrayCreateEditOT([], 'subhierarchy_id', 'Jerarquía 2' , $errors, $cotizacion ,'form-control',true,true) !!}
													<!-- Jerarquía 3-->
													{!! armarSelectArrayCreateEditOT([], 'subsubhierarchy_id', 'Jerarquía 3' , $errors, $cotizacion ,'form-control',true,true) !!}

												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-12 mb-2">
								<div class="card">
									<div class="card-header">Servicios</div>
									<div class="card-body">
										<!-- Formulario de Corrugado -->
										<div class="row input_detalle_cotizacion fragmento_formulario_corrugado">
											<div class="col-4">
												<!-- Matriz-->
												{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'matriz', 'Matriz' , $errors, $cotizacion ,'form-control',true,false) !!}
												<!-- Clisse-->
												{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'clisse', 'Clisse' , $errors, $cotizacion ,'form-control',true,false) !!}
												<!-- Royalty-->
												{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'royalty', 'Royalty' , $errors, $cotizacion ,'form-control',true,false) !!}

											</div>
											<div class="col-4">
												<!-- Maquila-->
												{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'maquila', 'Maquila' , $errors, $cotizacion ,'form-control',true,false) !!}
												<!-- Servicios Maquila-->
												{{--{!! armarSelectArrayCreateEditOT($maquila_servicios, 'maquila_servicio_id', 'Servicios Maquila' , $errors, $cotizacion ,'form-control',true,true) !!}--}}

												<!-- Cuchillos y gomas (m) -->
												{!! armarInputCreateEditOT('cuchillos_gomas', 'Cuchillos y gomas (m):', 'number',$errors, $cotizacion, 'form-control', '', '') !!}

												<!-- <div class="detalle-maquila"> -->
													<!-- Detalle Maquila-->
													<!-- <div class="form-group form-row ">
														<label class="col-auto col-form-label">Detalle Maquila</label>
														<div class="col">
															<select name="detalle_maquila_servicio_id[]" id="detalle_maquila_servicio_id" class="form-control form-control-sm" multiple title="Selecciona..." data-selected-text-format="count > 1" data-actions-box="false">
															</select>
														</div>
													</div>
												</div> -->

											</div>

											<div class="col-4">
												<!-- armado-->
												{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'armado_automatico', 'Armado' , $errors, $cotizacion ,'form-control',true,false) !!}

												<!-- Armado (US$/UN) -->
												{!! armarInputCreateEditOT('armado_usd_caja', 'Armado (US$/UN):', 'number',$errors, $cotizacion, 'form-control', '', '') !!}

											</div>
										</div>
										<!-- Formulario de Esquineros -->
										<div class="row input_detalle_cotizacion fragmento_formulario_esquinero" style="display: none;">
											<div class="col-4">
												<!-- Tipo Destino (palletizado)-->
												{!! armarSelectArrayCreateEditOT([1 => "Tarima Nacional", 2=>"Empaque Exportación (Granel)", 3=>"Tarima de Exportación"], 'tipo_destino_esquinero', 'Tipo Destino (palletizado)' , $errors, $cotizacion ,'form-control',true,false) !!}
												<!-- Tipo Camión-->
												{!! armarSelectArrayCreateEditOT([1 => "Camión 7x2,6mts"], 'tipo_camion_esquinero', 'Tipo Camión' , $errors, $cotizacion ,'form-control',true,false) !!}
												<!-- , 2=>"Camión 12x2,6mts" -->
												<!-- Maquila-->
												{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'maquila_esquinero', 'Maquila' , $errors, $cotizacion ,'form-control',true,false) !!}
												<!-- Clisse-->
												{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'clisse_esquinero', 'Clisse' , $errors, $cotizacion ,'form-control',true,false) !!}

											</div>

										</div>
									</div>
								</div>
							</div>
							<div id="" class="col-12 mb-2">
								<div class="card">
									<div class="card-header">Destino</div>
									<div class="card-body">
										<div class="row input_detalle_cotizacion">
											<div class="col-3">
												<!-- Lugar de Destino -->
												{!! armarSelectArrayCreateEditOT($flete, 'ciudad_id', 'Lugar de Destino' , $errors, $cotizacion ,'form-control form-element',true,true) !!}
											</div>
											<!-- Pallets apilados solo se despliega para corrugado -->
											<div class="col-3 fragmento_formulario_corrugado">
												<!-- Pallets Apilados -->
												{!! armarSelectArrayCreateEditOT([1=>1,2=>2], 'pallets_apilados', 'Pallets Apilados' , $errors, $cotizacion ,'form-control form-element',true,true) !!}
												<input type="hidden" id="pallets_apilados_val" value="">
											</div>
											<div class="col-3 fragmento_formulario_corrugado">
												<!-- Cantidad -->
												{!! armarInputCreateEditOT('cantidad', 'Cantidad (UN):', 'number',$errors, $cotizacion, 'form-control', 'min="1"', '') !!}

											</div>
											<div class="col-3 fragmento_formulario_esquinero">
												<!-- Cantidad -->
												{!! armarInputCreateEditOT('cantidad_esquinero', 'Cantidad (UN):', 'number',$errors, $cotizacion, 'form-control', 'min="1"', '') !!}

											</div>
											{{--
											<div class="col-3">
												<!-- Cantidad -->
												{!! armarInputCreateEditOT('agente_exportacion', 'Agente Exportación (%):', 'number',$errors, $cotizacion, 'form-control', 'min="1"', '') !!}

											</div>
											--}}
										</div>
										<div id="newRow">
										</div>
										<div class="row">
											<div class="col-12 text-center">

												<button id="agregarDestino" class="btn btn-success ">Agregar Destino</button>
											</div>
										</div>
									</div>
								</div>
							</div>


						</div>
						
						@if(Auth()->user()->isAdmin()) 
						<!-- Si es administrador no puede guardar ni limpiar los campos ( solo ver )  -->
						<div class="mt-3 text-right">
						</div>
						@elseif(!$cotizacion || $cotizacion->estado_id == 1)
						<div class="mt-3 text-right">
							<button id="guardarDetalleCotizacion" type="submit" class="btn btn-success float-right creacion">{{ isset($cotizacion->id) ? __('Guardar Detalle') : __('Guardar Detalle') }}</button>
							<button id="limpiarDetalleCotizacion" class="btn btn-light">Limpiar</button>
						</div>
						@endif
					</form>
				</div>
			</div>
		</div>
	</div>
</div>