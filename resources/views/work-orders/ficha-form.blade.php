<div class="form-row">



	<!-- Solo si es creacion por ingeniero -->
	@if((auth()->user()->role_id == 5 || auth()->user()->role_id == 6 || auth()->user()->role_id == 7 || auth()->user()->role_id == 8) && ( $tipo == "create" || $tipo == "duplicate"))
	<div id="" class="col-12 mb-2">
		<div class="card py-2">
			<div class="col-4">
				{!! armarSelectArrayCreateEditOT($vendedores, 'vendedor_id', 'Seleccione Vendedor' , $errors, $vendedores ,'form-control form-element',true,true) !!}
			</div>
		</div>
	</div>
	@endif

	<div id="ot-tipo-solicitud" class="col-12 mb-2">
		<div class="card">
			<div class="card-header">2.- Datos comerciales</div>
			<div class="card-body">
				<div class="row">
					@if($errors->any())
					@endif
					@if(Auth()->user()->isVendedorExterno())
						<!-- Solo se permite seleccionar al crearlo, luego no puede ser modificado -->
						@if($tipo == "create" || $tipo == "duplicate")

							<div class="col-4">

								{!! armarSelectArrayCreateEditOT($clients, 'client_id', 'Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
							</div>

						<!-- Validacion para que pueda editar el super administrador -->
						@elseif(Auth()->user()->isSuperAdministrador())
							<div class="col-4">
								<!-- Cliente -->
								{!! armarSelectArrayCreateEditOT($clients, 'client_id', 'Cliente' , $errors, $ot ,'form-control',true,true) !!}
							</div>
						@else
							<div class="col-4">
								<!-- Cliente -->
								{!! inputReadOnly('Cliente',$ot->client->nombre) !!}
							</div>
						@endif
						<div class="col-4">
							<!-- Descripción -->
							{!! armarInputCreateEditOT('dato_sub_cliente', 'Datos Cliente Edipac:', 'text',$errors, $ot, 'form-control', '', '') !!}
						</div>
						<div class="col-4">
							<!-- Código Producto -->
							{!! armarInputCreateEditOT('codigo_producto', 'Código Producto:', 'text',$errors, $ot, 'form-control', '', '') !!}
						</div>
						<div class="col-4">
							<!-- Solo se permite seleccionar al crearlo, luego no puede ser modificado -->
							@if($tipo == "create" || $tipo == "duplicate")
								<!-- Tipo de Solicitud -->
								{!! armarSelectArrayCreateEditOT($tipos_solicitud, 'tipo_solicitud', 'Tipo de solicitud:' , $errors, $ot ,'form-control',true,false) !!}
								{!! armarSelectArrayCreateEditOT([], 'instalacion_cliente', 'Instalación Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
								<!-- Contactos Cliente -->
								<!-- //style="display:none" -->
								{!! armarSelectArrayCreateEditOT([], 'contactos_cliente', 'Contactos Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
							@elseif(Auth()->user()->isSuperAdministrador())
								<!-- Tipo de Solicitud -->
								{!! armarSelectArrayCreateEditOT($tipos_solicitud, 'tipo_solicitud', 'Tipo de solicitud:' , $errors, $ot ,'form-control',true,false) !!}
								<!-- Contactos Cliente -->
								<!-- //style="display:none" -->
								{!! armarSelectArrayCreateEditOT([], 'instalacion_cliente', 'Instalación Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
								{!! armarSelectArrayCreateEditOT([], 'contactos_cliente', 'Contactos Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
							@else
								<!-- Tipo de Solicitud -->
								{!! inputReadOnly('Tipo de Solicitud', [1 => "Desarrollo Completo", 4 => "Cotiza sin CAD", 2 => "Cotiza con CAD", 3 => "Muestra con CAD", 7 => "Proyecto Innovacion", 5 => "Arte con Material",6 => "Otras Solicitudes Desarrollo"][$ot->tipo_solicitud]) !!}
								<input type="hidden" id="tipo_solicitud_2" value="{{$ot->tipo_solicitud}}">
								@if (is_null($ot->instalacion_cliente))
									{!! inputReadOnly('Instalacion Cliente','N/A') !!}
								@else
									{!! inputReadOnly('Instalacion Cliente',$ot->installation->nombre) !!}
								@endif
							@endif


							<!-- Nombre Contacto -->
							{!! armarInputCreateEditOT('nombre_contacto', 'Nombre Contacto:', 'text', $errors, $ot, 'form-control', '', '') !!}
							<!-- Email Contacto -->
							{!! armarInputCreateEditOT('email_contacto', 'Email Contacto:', 'email', $errors, $ot, 'form-control', '', '') !!}
							<!-- Teléfono Contacto -->
							{!! armarInputCreateEditOT('telefono_contacto', 'Teléfono Contacto:', 'text', $errors, $ot, 'form-control', '', '') !!}
							@if(auth()->user()->role_id == 5 || auth()->user()->role_id == 6 || auth()->user()->role_id == 7 || auth()->user()->role_id == 8 || auth()->user()->role_id == 9 || auth()->user()->role_id == 10 || auth()->user()->role_id == 11 || auth()->user()->role_id == 12)
								@if($tipo == "create")
									<div id="seccion_indicaciones_especiales" style="display: none;">
										<br>
										<div class="form-group form-row" >
											<div class="col">
												<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-indicaciones-especiales">
													<b>Indicaciones Especiales Cliente</b>
												</button>
											</div>
										</div>
									</div>
								@else
									@if(count($indicaciones_especiales)>0)
										<div id="seccion_indicaciones_especiales">
											<br>
											<div class="form-group form-row" >
												<div class="col">
													<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-indicaciones-especiales-edit" data-editar={{$ot->client_id}}>
														<b>Indicaciones Especiales Cliente</b>
													</button>
												</div>
											</div>
										</div>
									@endif
								@endif
							@endif
						</div>
						<div class="col-4">
							<!-- Descripción -->
							{!! armarInputCreateEditOT('descripcion', 'Descripción:', 'text',$errors, $ot, 'form-control', 'maxlength="40"', '') !!}

							<div class="form-group form-row">

								<div class="col-6">
									<!-- Volumen vta. anual:-->
									{!! armarInputCreateEditOT('volumen_venta_anual', 'Vol vta anual:', 'text',$errors, $ot, 'form-control', '', '') !!}

								</div>
								<div class="col-6">
									<!-- USD -->
									{!! armarInputCreateEditOT('usd', 'USD:', 'text',$errors, $ot, 'form-control', '', '') !!}

								</div>
							</div>
							<!-- Organizacion de Ventas -->
							{!! armarSelectArrayCreateEditOT($org_ventas, 'org_venta_id', 'Org. Venta' , $errors, $ot ,'form-control',true,false) !!}
							<!-- Canal -->
							{!! armarSelectArrayCreateEditOT($canals, 'canal_id', 'Canal' , $errors, $ot ,'form-control',true,false) !!}
							<!-- OC -->
							{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'oc', 'OC' , $errors, $ot ,'form-control',true,false) !!}
							<br>
							<div class="form-group form-row">
								<div class="col-12">
									<div id="subida_archivo_oc" class="form-group form-row" style="display:none;">
										<label class="col-auto col-form-label text-right">Archivo OC: </label>
										<input type="file" class="input" id="oc_file" name="oc_file">
										@if ((!old('_token') && $tipo=='edit' && $ot->oc_file != ''))
											@if($ot->oc == 1)
												<span data-attribute="titulo" data-toggle="tooltip" title="{{str_replace('/files/','',$ot->oc_file)}}"><a data-attribute="link" href="{{$ot->oc_file}}" download title="{{str_replace('/files/','',$ot->oc_file)}}"><i class="fa fa-download" aria-hidden="true" style="color: #38c172;"></i></a></span>&nbsp;
											@endif
										@endif
									</div>
									@if ((!old('_token') && $tipo=='edit' && $ot->oc_file != ''))
										@if($ot->oc == 1)
											<input type="hidden" id="oc_file_exist" value="1">
										@else
											<input type="hidden" id="oc_file_exist" value="0">
										@endif
									@else
										<input type="hidden" id="oc_file_exist" value="0">
									@endif
								</div>
							</div>
						</div>
						<div class="col-4">

							{!! armarSelectArrayCreateEditOT($hierarchies, 'hierarchy_id', 'Jerarquía 1' , $errors, $ot ,'form-control',true,true) !!}
							<!-- Jerarquía 2-->
							{!! armarSelectArrayCreateEditOT($subhierarchies, 'subhierarchy_id', 'Jerarquía 2' , $errors, $ot ,'form-control',true,true) !!}
							<!-- Jerarquía 3-->
							{!! armarSelectArrayCreateEditOT($subsubhierarchies, 'subsubhierarchy_id', 'Jerarquía 3' , $errors, $ot ,'form-control',true,true) !!}


						</div>
					@else
						@if(!is_null($ot) && $ot->ot_vendedor_externo==1)
							<!-- Solo se permite seleccionar al crearlo, luego no puede ser modificado -->
							@if($tipo == "create" || $tipo == "duplicate")

								<div class="col-4">

									{!! armarSelectArrayCreateEditOT($clients, 'client_id', 'Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
								</div>

							<!-- Validacion para que pueda editar el super administrador -->
							@elseif(Auth()->user()->isSuperAdministrador())
								<div class="col-4">
									<!-- Cliente -->
									{!! armarSelectArrayCreateEditOT($clients, 'client_id', 'Cliente' , $errors, $ot ,'form-control',true,true) !!}
								</div>
							@else
								<div class="col-4">
									<!-- Cliente -->
									{!! inputReadOnly('Cliente',$ot->client->nombre) !!}
								</div>
							@endif
							<div class="col-4">
								<!-- Descripción -->
								{!! armarInputCreateEditOT('dato_sub_cliente', 'Datos Cliente Edipac:', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
							<div class="col-4">
								<!-- Código Producto -->
								{!! armarInputCreateEditOT('codigo_producto', 'Código Producto:', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
							<div class="col-4">
								<!-- Solo se permite seleccionar al crearlo, luego no puede ser modificado -->
								@if($tipo == "create" || $tipo == "duplicate")
									<!-- Tipo de Solicitud -->
									{!! armarSelectArrayCreateEditOT($tipos_solicitud, 'tipo_solicitud', 'Tipo de solicitud:' , $errors, $ot ,'form-control',true,false) !!}
									{!! armarSelectArrayCreateEditOT([], 'instalacion_cliente', 'Instalación Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
									<!-- Contactos Cliente -->
									<!-- //style="display:none" -->
									{!! armarSelectArrayCreateEditOT([], 'contactos_cliente', 'Contactos Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
								@elseif(Auth()->user()->isSuperAdministrador())
									<!-- Tipo de Solicitud -->
									{!! armarSelectArrayCreateEditOT($tipos_solicitud, 'tipo_solicitud', 'Tipo de solicitud:' , $errors, $ot ,'form-control',true,false) !!}
									<!-- Contactos Cliente -->
									<!-- //style="display:none" -->
									{!! armarSelectArrayCreateEditOT([], 'instalacion_cliente', 'Instalación Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
									{!! armarSelectArrayCreateEditOT([], 'contactos_cliente', 'Contactos Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
								@else
									<!-- Tipo de Solicitud -->
									{!! inputReadOnly('Tipo de Solicitud', [1 => "Desarrollo Completo", 4 => "Cotiza sin CAD", 2 => "Cotiza con CAD", 3 => "Muestra con CAD", 7 => "Proyecto Innovacion", 5 => "Arte con Material",6 => "Otras Solicitudes Desarrollo"][$ot->tipo_solicitud]) !!}
									<input type="hidden" id="tipo_solicitud_2" value="{{$ot->tipo_solicitud}}">
									@if (is_null($ot->instalacion_cliente))
										{!! inputReadOnly('Instalacion Cliente','N/A') !!}
									@else
										{!! inputReadOnly('Instalacion Cliente',$ot->installation->nombre) !!}
									@endif
								@endif


								<!-- Nombre Contacto -->
								{!! armarInputCreateEditOT('nombre_contacto', 'Nombre Contacto:', 'text', $errors, $ot, 'form-control', '', '') !!}
								<!-- Email Contacto -->
								{!! armarInputCreateEditOT('email_contacto', 'Email Contacto:', 'email', $errors, $ot, 'form-control', '', '') !!}
								<!-- Teléfono Contacto -->
								{!! armarInputCreateEditOT('telefono_contacto', 'Teléfono Contacto:', 'text', $errors, $ot, 'form-control', '', '') !!}
								@if(auth()->user()->role_id == 5 || auth()->user()->role_id == 6 || auth()->user()->role_id == 7 || auth()->user()->role_id == 8 || auth()->user()->role_id == 9 || auth()->user()->role_id == 10 || auth()->user()->role_id == 11 || auth()->user()->role_id == 12)
									@if($tipo == "create")
										<div id="seccion_indicaciones_especiales" style="display: none;">
											<br>
											<div class="form-group form-row" >
												<div class="col">
													<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-indicaciones-especiales">
														<b>Indicaciones Especiales Cliente</b>
													</button>
												</div>
											</div>
										</div>
									@else
										@if(count($indicaciones_especiales)>0)
											<div id="seccion_indicaciones_especiales">
												<br>
												<div class="form-group form-row" >
													<div class="col">
														<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-indicaciones-especiales-edit" data-editar={{$ot->client_id}}>
															<b>Indicaciones Especiales Cliente</b>
														</button>
													</div>
												</div>
											</div>
										@endif
									@endif
								@endif
							</div>
							<div class="col-4">
								<!-- Descripción -->
								{!! armarInputCreateEditOT('descripcion', 'Descripción:', 'text',$errors, $ot, 'form-control', 'maxlength="40"', '') !!}

								<div class="form-group form-row">

									<div class="col-6">
										<!-- Volumen vta. anual:-->
										{!! armarInputCreateEditOT('volumen_venta_anual', 'Vol vta anual:', 'text',$errors, $ot, 'form-control', '', '') !!}

									</div>
									<div class="col-6">
										<!-- USD -->
										{!! armarInputCreateEditOT('usd', 'USD:', 'text',$errors, $ot, 'form-control', '', '') !!}

									</div>
								</div>
								<!-- Organizacion de Ventas -->
								{!! armarSelectArrayCreateEditOT($org_ventas, 'org_venta_id', 'Org. Venta' , $errors, $ot ,'form-control',true,false) !!}
								<!-- Canal -->
								{!! armarSelectArrayCreateEditOT($canals, 'canal_id', 'Canal' , $errors, $ot ,'form-control',true,false) !!}
								<!-- OC -->
								{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'oc', 'OC' , $errors, $ot ,'form-control',true,false) !!}
								<br>
								<div class="form-group form-row">
									<div class="col-12">
										<div id="subida_archivo_oc" class="form-group form-row" style="display:none;">
											<label class="col-auto col-form-label text-right">Archivo OC: </label>
											<input type="file" class="input" id="oc_file" name="oc_file">
											@if ((!old('_token') && $tipo=='edit' && $ot->oc_file != ''))
												@if($ot->oc == 1)
													<span data-attribute="titulo" data-toggle="tooltip" title="{{str_replace('/files/','',$ot->oc_file)}}"><a data-attribute="link" href="{{$ot->oc_file}}" download title="{{str_replace('/files/','',$ot->oc_file)}}"><i class="fa fa-download" aria-hidden="true" style="color: #38c172;"></i></a></span>
												@endif
											@endif
										</div>
										@if ((!old('_token') && $tipo=='edit' && $ot->oc_file != ''))
											@if($ot->oc == 1)
												<input type="hidden" id="oc_file_exist" value="1">
											@else
												<input type="hidden" id="oc_file_exist" value="0">
											@endif
										@else
											<input type="hidden" id="oc_file_exist" value="0">
										@endif
									</div>
								</div>

							</div>
							<div class="col-4">

								{!! armarSelectArrayCreateEditOT($hierarchies, 'hierarchy_id', 'Jerarquía 1' , $errors, $ot ,'form-control',true,true) !!}
								<!-- Jerarquía 2-->
								{!! armarSelectArrayCreateEditOT($subhierarchies, 'subhierarchy_id', 'Jerarquía 2' , $errors, $ot ,'form-control',true,true) !!}
								<!-- Jerarquía 3-->
								{!! armarSelectArrayCreateEditOT($subsubhierarchies, 'subsubhierarchy_id', 'Jerarquía 3' , $errors, $ot ,'form-control',true,true) !!}


							</div>
						@else
							<!-- Solo se permite seleccionar al crearlo, luego no puede ser modificado -->
							@if($tipo == "create" || $tipo == "duplicate")

								<div class="col-4">

									{!! armarSelectArrayCreateEditOT($clients, 'client_id', 'Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
								</div>

							<!-- Validacion para que pueda editar el super administrador -->
							@elseif(Auth()->user()->isSuperAdministrador())
								<div class="col-4">
									<!-- Cliente -->
									{!! armarSelectArrayCreateEditOT($clients, 'client_id', 'Cliente' , $errors, $ot ,'form-control',true,true) !!}
								</div>
							@else
								<div class="col-4">
									<!-- Cliente -->
									{!! inputReadOnly('Cliente',$ot->client->nombre) !!}
								</div>
							@endif

							<div class="col-4">
								<!-- Descripción -->
								{!! armarInputCreateEditOT('descripcion', 'Descripción:', 'text',$errors, $ot, 'form-control', 'maxlength="40"', '') !!}
							</div>
							<div class="col-4">
								<!-- Código Producto -->
								{!! armarInputCreateEditOT('codigo_producto', 'Código Producto:', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>

							<div class="col-4">
								<!-- Solo se permite seleccionar al crearlo, luego no puede ser modificado -->
								@if($tipo == "create" || $tipo == "duplicate")
									<!-- Tipo de Solicitud -->
									{!! armarSelectArrayCreateEditOT($tipos_solicitud, 'tipo_solicitud', 'Tipo de solicitud:' , $errors, $ot ,'form-control',true,false) !!}
									{!! armarSelectArrayCreateEditOT([], 'instalacion_cliente', 'Instalación Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
									<!-- Contactos Cliente -->
									<!-- //style="display:none" -->
									{!! armarSelectArrayCreateEditOT([], 'contactos_cliente', 'Contactos Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
								@elseif(Auth()->user()->isSuperAdministrador())
									<!-- Tipo de Solicitud -->
									{!! armarSelectArrayCreateEditOT($tipos_solicitud, 'tipo_solicitud', 'Tipo de solicitud:' , $errors, $ot ,'form-control',true,false) !!}
									<!-- Contactos Cliente -->
									<!-- //style="display:none" -->
									{!! armarSelectArrayCreateEditOT([], 'instalacion_cliente', 'Instalación Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
									{!! armarSelectArrayCreateEditOT([], 'contactos_cliente', 'Contactos Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
								@else
									<!-- Tipo de Solicitud -->
									{!! inputReadOnly('Tipo de Solicitud', [1 => "Desarrollo Completo", 4 => "Cotiza sin CAD", 2 => "Cotiza con CAD", 3 => "Muestra con CAD", 7 => "Proyecto Innovacion", 5 => "Arte con Material", 6 => "Otras Solicitudes Desarrollo"][$ot->tipo_solicitud]) !!}
									<input type="hidden" id="tipo_solicitud_2" value="{{$ot->tipo_solicitud}}">
									@if (is_null($ot->instalacion_cliente))
										{!! inputReadOnly('Instalacion Cliente','N/A') !!}
									@else
										{!! inputReadOnly('Instalacion Cliente',$ot->installation->nombre) !!}
									@endif
								@endif


								<!-- Nombre Contacto -->
								{!! armarInputCreateEditOT('nombre_contacto', 'Nombre Contacto:', 'text', $errors, $ot, 'form-control', '', '') !!}
								<!-- Email Contacto -->
								{!! armarInputCreateEditOT('email_contacto', 'Email Contacto:', 'email', $errors, $ot, 'form-control', '', '') !!}
								<!-- Teléfono Contacto -->
								{!! armarInputCreateEditOT('telefono_contacto', 'Teléfono Contacto:', 'text', $errors, $ot, 'form-control', '', '') !!}
								@if(auth()->user()->role_id == 5 || auth()->user()->role_id == 6 || auth()->user()->role_id == 7 || auth()->user()->role_id == 8 || auth()->user()->role_id == 9 || auth()->user()->role_id == 10 || auth()->user()->role_id == 11 || auth()->user()->role_id == 12)
									@if($tipo == "create")
										<div id="seccion_indicaciones_especiales" style="display: none;">
											<br>
											<div class="form-group form-row" >
												<div class="col">
													<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-indicaciones-especiales">
														<b>Indicaciones Especiales Cliente</b>
													</button>
												</div>
											</div>
										</div>
									@else
										@if(count($indicaciones_especiales)>0)
											<div id="seccion_indicaciones_especiales">
												<br>
												<div class="form-group form-row" >
													<div class="col">
														<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-indicaciones-especiales-edit" data-editar={{$ot->client_id}}>
															<b>Indicaciones Especiales Cliente</b>
														</button>
													</div>
												</div>
											</div>
										@endif
									@endif
								@endif
							</div>
							<div class="col-4">
								<div class="form-group form-row">
									<div class="col-6">
										<!-- Volumen vta. anual:-->
										{!! armarInputCreateEditOT('volumen_venta_anual', 'Vol vta anual:', 'text',$errors, $ot, 'form-control', '', '') !!}

									</div>
									<div class="col-6">
										<!-- USD -->
										{!! armarInputCreateEditOT('usd', 'USD:', 'text',$errors, $ot, 'form-control', '', '') !!}

									</div>
								</div>
								<!-- Organizacion de Ventas -->
								{!! armarSelectArrayCreateEditOT($org_ventas, 'org_venta_id', 'Org. Venta' , $errors, $ot ,'form-control',true,false) !!}
								<!-- Canal -->
								{!! armarSelectArrayCreateEditOT($canals, 'canal_id', 'Canal' , $errors, $ot ,'form-control',true,false) !!}
								<!-- OC -->
								{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'oc', 'OC' , $errors, $ot ,'form-control',true,false) !!}
								<br>
								<div class="form-group form-row">
									<div class="col-12">
										<div id="subida_archivo_oc" class="form-group form-row" style="display:none;">
											<label class="col-auto col-form-label text-right">Archivo OC: </label>
											<input type="file" class="input" id="oc_file" name="oc_file">
											@if ((!old('_token') && $tipo=='edit' && $ot->oc_file != ''))
												@if($ot->oc == 1)
													<span data-attribute="titulo" data-toggle="tooltip" title="{{str_replace('/files/','',$ot->oc_file)}}"><a data-attribute="link" href="{{$ot->oc_file}}" download title="{{str_replace('/files/','',$ot->oc_file)}}"><i class="fa fa-download" aria-hidden="true" style="color: #38c172;"></i></a></span>
												@endif
											@endif
										</div>
										@if ((!old('_token') && $tipo=='edit' && $ot->oc_file != ''))
											@if($ot->oc == 1)
												<input type="hidden" id="oc_file_exist" value="1">
											@else
												<input type="hidden" id="oc_file_exist" value="0">
											@endif
										@else
											<input type="hidden" id="oc_file_exist" value="0">
										@endif
									</div>
								</div>
							</div>
							<div class="col-4">

								{!! armarSelectArrayCreateEditOT($hierarchies, 'hierarchy_id', 'Jerarquía 1' , $errors, $ot ,'form-control',true,true) !!}
								<!-- Jerarquía 2-->
								{!! armarSelectArrayCreateEditOT($subhierarchies, 'subhierarchy_id', 'Jerarquía 2' , $errors, $ot ,'form-control',true,true) !!}
								<!-- Jerarquía 3-->
								{!! armarSelectArrayCreateEditOT($subsubhierarchies, 'subsubhierarchy_id', 'Jerarquía 3' , $errors, $ot ,'form-control',true,true) !!}


							</div>
						@endif
					@endif
				</div>
			</div>
		</div>
	</div>

	<div id="ot-antecedentes" class="col-5 mb-2">
		<div class="card h-100">
			<div class="card-header">3.- Antecedentes Desarrollo</div>
			<div div class="card-body">
				<div class="row" style="height: 25px">
					<div class="col-12">
						<div class="form-group form-row">
							<label class="col-auto col-form-label">Documentos:</label>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-4">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" value="check_correo_cliente" id="check_correo_cliente" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->ant_des_correo_cliente == 1) || (old('check_correo_cliente'))) checked @endif>
							<label class="custom-control-label" for="check_correo_cliente">Correo Cliente</label>
						</div>
						@if ((!old('_token') && $tipo=='edit' && $ot->ant_des_correo_cliente_file != ''))
							@if($ot->ant_des_correo_cliente == 1)
								<div id="upload_file_correo" class="custom-control custom-checkbox">
							@else
								<div id="upload_file_correo" class="custom-control custom-checkbox" style="display:none;">
							@endif
								<label for="file_check_correo_cliente"><span data-attribute="titulo" id="file_chosen_correo" data-toggle="tooltip" title="{{str_replace('/files/','',$ot->ant_des_correo_cliente_file)}}"><i class="fa fa-paperclip"></i></span></label>&nbsp;
								<span data-attribute="titulo" data-toggle="tooltip" title="Descargar"><a data-attribute="link" href="{{$ot->ant_des_correo_cliente_file}}" download title="Descargar"><i class="fa fa-download" aria-hidden="true" style="color: #38c172;"></i></a></span>&nbsp;
								<input type="file" id="file_check_correo_cliente" class="input" name="file_check_correo_cliente" hidden/>
							</div>
						@else
							<div id="upload_file_correo"  class="custom-control custom-checkbox" style="display:none;">
								<label for="file_check_correo_cliente" ><span data-attribute="titulo" id="file_chosen_correo" data-toggle="tooltip" title=""><i class="fa fa-paperclip"></i></span></label>&nbsp;
								<input type="file" id="file_check_correo_cliente" class="input" name="file_check_correo_cliente" hidden>
							</div>

						@endif
					</div>
					<div class="col-4">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" value="check_plano_actual" id="check_plano_actual" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->ant_des_plano_actual == 1) || (old('check_plano_actual'))) checked @endif>
							<label class="custom-control-label" for="check_plano_actual">Plano Actual</label>
						</div>
						@if ((!old('_token') && $tipo=='edit' && $ot->ant_des_plano_actual_file != ''))
							@if($ot->ant_des_plano_actual == 1)
								<div id="upload_file_plano" class="custom-control custom-checkbox">
							@else
								<div id="upload_file_plano" class="custom-control custom-checkbox" style="display:none;">
							@endif
								<label for="file_check_plano_actual"><span data-attribute="titulo" data-toggle="tooltip" title="{{str_replace('/files/','',$ot->ant_des_plano_actual_file)}}"><i class="fa fa-paperclip"></i></span></label>&nbsp;
								<span data-attribute="titulo" data-toggle="tooltip" title="Descargar"><a data-attribute="link" href="{{$ot->ant_des_plano_actual_file}}" download title="Descargar"><i class="fa fa-download" aria-hidden="true" style="color: #38c172;"></i></a></span>&nbsp;
								<input type="file" id="file_check_plano_actual" class="input" name="file_check_plano_actual" hidden/>
							</div>
						@else
							<div id="upload_file_plano" class="custom-control custom-checkbox" style="display:none;">
								<label for="file_check_plano_actual" ><span class="fa fa-paperclip"><span data-attribute="titulo" id="file_chosen_plano" data-toggle="tooltip" title=""></span></label>&nbsp;
								<input type="file" id="file_check_plano_actual" class="input" name="file_check_plano_actual" hidden>

							</div>
						@endif
					</div>
					<div class="col-4">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" value="check_boceto_actual" id="check_boceto_actual" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->ant_des_boceto_actual == 1) || (old('check_boceto_actual'))) checked @endif>
							<label class="custom-control-label" for="check_boceto_actual">Boceto Actual</label>
						</div>
						@if ((!old('_token') && $tipo=='edit' && $ot->ant_des_boceto_actual_file != ''))
							@if($ot->ant_des_boceto_actual == 1)
								<div id="upload_file_boceto" class="custom-control custom-checkbox">
							@else
								<div id="upload_file_boceto" class="custom-control custom-checkbox" style="display:none;">
							@endif
								<label for="file_check_boceto_actual"><span data-attribute="titulo" id="file_chosen_boceto" data-toggle="tooltip" title="{{str_replace('/files/','',$ot->ant_des_boceto_actual_file)}}"><i class="fa fa-paperclip"></i></span></label>&nbsp;
								<span data-attribute="titulo" data-toggle="tooltip" title="Descargar"><a data-attribute="link" href="{{$ot->ant_des_boceto_actual_file}}" download title="Descargar"><i class="fa fa-download" aria-hidden="true" style="color: #38c172;"></i></a></span>&nbsp;
								<input type="file" id="file_check_boceto_actual" class="input" name="file_check_boceto_actual" hidden/>
							</div>
						@else
							<div id="upload_file_boceto" class="custom-control custom-checkbox" style="display:none;">
								<label for="file_check_boceto_actual" ><span data-attribute="titulo" data-toggle="tooltip" id="file_chosen_boceto"><i class="fa fa-paperclip"></i></span></label>&nbsp;
								<input type="file" id="file_check_boceto_actual" class="input" name="file_check_boceto_actual" hidden>
							</div>
						@endif
					</div>
					<div class="col-4">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" value="check_speed" id="check_speed" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->ant_des_speed == 1) || (old('check_speed'))) checked @endif>
							<label class="custom-control-label" for="check_speed">Spec</label>
						</div>
						@if ((!old('_token') && $tipo=='edit' && $ot->ant_des_speed_file != ''))
							@if($ot->ant_des_speed == 1)
								<div id="upload_file_speed" class="custom-control custom-checkbox">
							@else
								<div id="upload_file_speed" class="custom-control custom-checkbox" style="display:none;">
							@endif
								<label for="file_check_speed"><span data-attribute="titulo" data-toggle="tooltip" id="file_chosen_speed" title="{{str_replace('/files/','',$ot->ant_des_speed_file)}}"><i class="fa fa-paperclip"></i></span></label>&nbsp;
								<span data-attribute="titulo" data-toggle="tooltip" title="Descargar"><a data-attribute="link" href="{{$ot->ant_des_speed_file}}" download title="Descargar"><i class="fa fa-download" aria-hidden="true" style="color: #38c172;"></i></a></span>&nbsp;
								<input type="file" id="file_check_speed" class="input" name="file_check_speed" hidden/>
							</div>
						@else
							<div id="upload_file_speed" class="custom-control custom-checkbox" style="display:none;">
								<label for="file_check_speed" ><span data-attribute="titulo" data-toggle="tooltip" id="file_chosen_speed"><i class="fa fa-paperclip"></i></span></label>&nbsp;
								<input type="file" id="file_check_speed" class="input" name="file_check_speed" hidden>
							</div>
						@endif
					</div>


					<div class="col-4">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" value="check_otro" id="check_otro" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->ant_des_otro == 1) || (old('check_otro'))) checked @endif>
							<label class="custom-control-label" for="check_otro">Otro</label>
						</div>
						@if ((!old('_token') && $tipo=='edit' && $ot->ant_des_otro_file != ''))
							@if($ot->ant_des_otro == 1)
								<div id="upload_file_otro" class="custom-control custom-checkbox">
							@else
								<div id="upload_file_otro" class="custom-control custom-checkbox" style="display:none;">
							@endif
								<label for="file_check_otro"><span data-attribute="titulo" id="file_chosen_otro" data-toggle="tooltip" title="{{str_replace('/files/','',$ot->ant_des_otro_file)}}"><i class="fa fa-paperclip"></i></span></label>&nbsp;
								<span data-attribute="titulo" data-toggle="tooltip" title="Descargar"><a data-attribute="link" href="{{$ot->ant_des_otro_file}}" download title="Descargar"><i class="fa fa-download" aria-hidden="true" style="color: #38c172;"></i></a></span>&nbsp;
								<input type="file" id="file_check_otro" class="input" name="file_check_otro" hidden/>
							</div>
						@else
							<div id="upload_file_otro" class="custom-control custom-checkbox" style="display:none;">
								<label for="file_check_otro"><span data-attribute="titulo" data-toggle="tooltip" id="file_chosen_otro"><i class="fas fa-paperclip"></i></span></label>&nbsp;
								<input type="file" id="file_check_otro" class="input" name="file_check_otro" hidden/>
							</div>
						@endif
					</div>
				</div>
				<hr style="margin-top: 1%; margin-bottom:1%">
				<div class="row" style="height: 25px">
					<div class="col-12">
						<div class="form-group form-row">
							<label class="col-auto col-form-label">Muestra Competencia:</label>
						</div>
					</div>
				</div>
				<div class="row" style="height: 25px">
					<div class="col-4">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" value="check_referencia_de" id="check_referencia_de" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->ant_des_cj_referencia_de == 1) || (old('check_referencia_de'))) checked @endif>
							<label class="custom-control-label" for="check_referencia_de">CJ Referencia DE</label>
						</div>
					</div>
					<div class="col-4">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" value="check_referencia_dg" id="check_referencia_dg" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->ant_des_cj_referencia_dg == 1) || (old('check_referencia_dg'))) checked @endif>
							<label class="custom-control-label" for="check_referencia_dg">CJ Referencia DG</label>
						</div>
					</div>
					<div class="col-4">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" value="check_envase_primario" id="check_envase_primario" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->ant_des_envase_primario == 1) || (old('check_envase_primario'))) checked @endif>
							<label class="custom-control-label" for="check_envase_primario">Envase Primario</label>
						</div>
					</div>
				</div>
				<hr style="margin-top: 1%; margin-bottom:1%">
				<div class="row" style="height: 25px">
					<div class="col-4">
						<div class="form-group form-row">
							<label class="col-auto col-form-label">Conservar Muestra:</label>
						</div>
					</div>
					<div class="col-1">
						<div class="custom-control custom-checkbox mb-1">
							<input type="checkbox" class="custom-control-input" value="check_conservar_si" id="check_conservar_si" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->ant_des_conservar_muestra == 1) || (old('check_conservar_si'))) checked @endif>
							<label class="custom-control-label" for="check_conservar_si">SI</label>
						</div>
					</div>
					<div class="col-1">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" value="check_conservar_no" id="check_conservar_no" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->ant_des_conservar_muestra == 0) || (old('check_conservar_no'))) checked @endif>
							<label class="custom-control-label" for="check_conservar_no">NO</label>
						</div>
					</div>
				</div>
				<hr style="margin-top: 1%; margin-bottom:1%">
				<div class="row">
                    <div class="col-12">
                        <div class="row" >
							<div class="col-7">
								<!--Armado Automático -->
								{!! armarSelectArrayCreateEditOT(['1' => "Si", '0'=>"No"], 'armado_automatico', 'Armado Automático' , $errors, $ot ,'form-control',true,false) !!}
							{{-- <div class="col-4">
									<div class="form-group form-row" id="div_armado_automatico">
										<label class="col-auto col-form-label">Armado Automático:</label>
									</div>
								</div>
								<div class="col-1">
									<div class="custom-control custom-checkbox mb-1">
										<input type="checkbox" class="custom-control-input"  id="check_armado_automatico_si" name="check_armado_automatico_si" @if ((!old('_token') && $tipo=='edit' && $ot->armado_automatico == 1) || (old('check_armado_automatico_si'))) checked @endif>
										<label class="custom-control-label" for="check_armado_automatico_si">SI</label>
									</div>
								</div>
								<div class="col-1">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input"  id="check_armado_automatico_no" name="check_armado_automatico_no" @if ((!old('_token') && $tipo=='edit' && $ot->armado_automatico == 0) || (old('check_armado_automatico_no'))) checked @endif>
										<label class="custom-control-label" for="check_armado_automatico_no">NO</label>
									</div>
								</div>--}}
							</div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>

	<div id="ot-solicita" class="col-2 mb-2">
		<div class="card h-100">
			<div class="card-header">4.- Solicita</div>
			<div class="card-body">

				<div id="checkbox-card" class="row pt-2" style="display: flex;justify-content: space-evenly;">
					<div class="checkboxCol">
						<div class="custom-control custom-checkbox mb-1">
							<input type="checkbox" class="custom-control-input" value="analisis" id="analisis" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->analisis == 1) || (old('analisis'))) checked @endif>
							<label class="custom-control-label" for="analisis">Análisis</label>
						</div>
						<div class="custom-control custom-checkbox mb-1">
							<input type="checkbox" class="custom-control-input" value="prueba_industrial" id="prueba_industrial" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->prueba_industrial == 1) || (old('prueba_industrial'))) checked @endif>
							<label class="custom-control-label" for="prueba_industrial">Prueba Industrial</label>
						</div>
						<div class="custom-control custom-checkbox mb-1" style="    display: flex;justify-content: space-between;">
							<input type="checkbox" class="custom-control-input" value="muestra" id="muestra" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->muestra == 1) || (old('muestra'))) checked @endif>
							<label class="custom-control-label" for="muestra">Muestra</label>
							<span class="marcas-aprobaciones">
								@if($tipo=='edit' && $ot->tipo_solicitud == 3)
								{!!$ot->present()->iconosAprobacionVenta()!!}
								{!!$ot->present()->iconosAprobacionDesarrollo()!!}
								@endif
							</span>

							<!-- <div class="material-icons md-14" data-toggle="tooltip" title="Gestionar">search</div> -->
						</div>
					</div>
				</div>
				<br>
				<div style="width:140px">
					<div style="width:140px;display:none" id="container-numero-muetras">
						@if($tipo=='duplicate')
							{!! armarInputCreateEditOT('numero_muestras', 'N° Muestras:', 'number',$errors, null, 'form-control', 'min="1"', '') !!}
						@else
							{!! armarInputCreateEditOT('numero_muestras', 'N° Muestras:', 'number',$errors, $ot, 'form-control', 'min="1"', '') !!}
						@endif
					</div>
				</div>

				@if($errors->has('analisis')||$errors->has('muestra')||$errors->has('prueba_industrial'))
				<div class="error text-center p-3">
					<h6 style="color:red">* Debes seleccionar al menos una opción</h5>
				</div>
				@endif
			</div>
		</div>

	</div>

	<div id="ot-datos-cliente" class="col-5 mb-2">
		<div class="card">
			<div class="card-header">5.- Referencia Material</div>
			<div class="card-body">
				<div class="row">
					<div class="col-6">
						<!-- Tipo Referencia -->
						{!! armarSelectArrayCreateEditOTSeparado($reference_type, 'reference_type', 'Tipo Referencia' , $errors, $ot ,'form-control',true,false) !!}

					</div>
					<div class="col-6">
						<!-- Referencia -->
						{!! armarSelectArrayCreateEditOTSeparado($materials, 'reference_id', 'Referencia' , $errors, $ot ,'form-control',true,true) !!}
						<!--                                Cambiar valores -->

					</div>
					<div class="col-6">
						<!-- Bloqueo Referencia -->
						{!! armarSelectArrayCreateEditOTSeparado([1 => "Si", 0=>"No"], 'bloqueo_referencia', 'Bloqueo Referencia' , $errors, $ot ,'form-control',true,false) !!}

					</div>
					<div class="col-6">
						<!-- Indicador Facturación Diseño Estructural-->
						<!-- 6=>'Offest', -->
						<!-- 12=>'Impresión', -->
						{!! armarSelectArrayCreateEditOTSeparado([
							1=>'RRP',
							2=>'E-Commerce',
							3=>'Esquineros',
							4=>'Geometría',
							5=>'Participación nuevo Mercado',
							7=>'Innovación',
							8=>'Sustentabilidad',
							9=>'Automatización',
							10=>'No Aplica',
							11=>'Ahorro'], 'indicador_facturacion', 'Indicador Facturación D.E.' , $errors, $ot ,'form-control',true,false) !!}
					</div>
				</div>
			</div>
		</div>
        <div style="margin-top: 2%" class="card">
            <div class="card-header">Archivo Visto Bueno</div>
            <div class="card-body">
                <div class="row">
 {{-- VB MUESTRA --}}
 <div class="col-4">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" disabled value="check_vb_muestra" id="check_vb_muestra" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->ant_des_vb_muestra == 1) || (old('check_vb_muestra'))) checked @endif>
        <label class="custom-control-label" style="font-weight: bold; color:#44ae3f" for="check_vb_muestra">VB Muestra</label>
    </div>
    @if ((!old('_token') && $tipo=='edit' && $ot->ant_des_vb_muestra != ''))
        @if($ot->ant_des_vb_muestra == 1)
            <div id="upload_file_vb_muestra" class="custom-control custom-checkbox">
        @else
            <div id="upload_file_vb_muestra" class="custom-control custom-checkbox" style="display:none;">
        @endif
            <label for="file_check_vb_muestra"><span data-attribute="titulo" id="file_chosen_vb_muestra" data-toggle="tooltip" title="{{str_replace('/files/','',$ot->ant_des_vb_muestra_file)}}"><i class="fa fa-paperclip"></i></span></label>&nbsp;
            <span data-attribute="titulo" data-toggle="tooltip" title="Descargar"><a data-attribute="link" href="{{$ot->ant_des_vb_muestra_file}}" download title="Descargar"><i class="fa fa-download" aria-hidden="true" style="color: #38c172;"></i></a></span>&nbsp;
            <input type="file" id="file_check_vb_muestra" class="input" name="file_check_vb_muestra" hidden/>
        </div>
    @else
        <div id="upload_file_vb_muestra" class="custom-control custom-checkbox" style="display:none;">
            <label for="file_check_vb_muestra"><span data-attribute="titulo" data-toggle="tooltip" id="file_chosen_vb_muestra"><i class="fas fa-paperclip"></i></span></label>&nbsp;
            <input type="file" id="file_check_vb_muestra" class="input" name="file_check_vb_muestra" hidden/>
        </div>
    @endif

</div>
{{-- VB BOCE --}}
<div class="col-4">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" disabled value="check_vb_boce" id="check_vb_boce" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->ant_des_vb_boce == 1) || (old('check_vb_boce'))) checked @endif>
        <label class="custom-control-label" style="font-weight: bold; color:#44ae3f" for="check_vb_boce">VB Boceto</label>
    </div>
    @if ((!old('_token') && $tipo=='edit' && $ot->ant_des_vb_boce != ''))
        @if($ot->ant_des_vb_boce == 1)
            <div id="upload_file_vb_boce" class="custom-control custom-checkbox">
        @else
            <div id="upload_file_vb_boce" class="custom-control custom-checkbox" style="display:none;">
        @endif
            <label for="file_check_vb_boce"><span data-attribute="titulo" id="file_chosen_vb_boce" data-toggle="tooltip" title="{{str_replace('/files/','',$ot->ant_des_vb_boce_file)}}"><i class="fa fa-paperclip"></i></span></label>&nbsp;
            <span data-attribute="titulo" data-toggle="tooltip" title="Descargar"><a data-attribute="link" href="{{$ot->ant_des_vb_boce_file}}" download title="Descargar"><i class="fa fa-download" aria-hidden="true" style="color: #38c172;"></i></a></span>&nbsp;
            <input type="file" id="file_check_vb_boce" class="input" name="file_check_vb_boce" hidden/>
        </div>
    @else
        <div id="upload_file_vb_boce" class="custom-control custom-checkbox" style="display:none;">
            <label for="file_check_vb_boce"><span data-attribute="titulo" data-toggle="tooltip" id="file_chosen_vb_boce"><i class="fas fa-paperclip"></i></span></label>&nbsp;
            <input type="file" id="file_check_vb_boce" class="input" name="file_check_vb_boce" hidden/>
        </div>
    @endif

</div>
                </div>
            </div>
        </div>
	</div>

	<div id="ot-ingresos-principales" class="col-12 mb-2" style="background: #3aaa35;">
		<div class="card h-100">
			<div class="card-header">6.- Asistente Para: Ingresos Principales</div>
			<div class="card-body">
				<div class="row">
					<div class="col-4">
						<!-- TIPO ITEM -->
						{!! armarSelectArrayCreateEditOT($productTypes, 'product_type_id', 'Tipo item' , $errors, $ot ,'form-control',true,true) !!}

						<!-- Impresión -->
						{!! armarSelectArrayCreateEditOT($impresion, 'impresion', 'Impresión', $errors, $ot ,'form-control form-element',true,true) !!}

						<!-- FSC -->
						{!! armarSelectArrayCreateEditOT($fsc, 'fsc', 'FSC' , $errors, $ot ,'form-control form-element',true,true) !!}
					</div>
					<div class="col-4">
						<!-- Cinta-->
						{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'cinta', 'Cinta' , $errors, $ot ,'form-control',true,false) !!}

						<!-- Recubrimiento Interno -->
						{!! armarSelectArrayCreateEditOT($coverageInternal, 'coverage_internal_id', 'Recubrimiento Interno' , $errors, $ot ,'form-control',true,false) !!}

						<!-- Recubrimiento Externo -->
						{!! armarSelectArrayCreateEditOT($coverageExternal, 'coverage_external_id', 'Recubrimiento Externo' , $errors, $ot ,'form-control',true,false) !!}
					</div>
					<div class="col-4">
						<!-- PLANTA OBJETIVO -->
						{!! armarSelectArrayCreateEditOT($plantaObjetivo, 'planta_id', 'Planta Objetivo' , $errors, $ot ,'form-control',true,true) !!}

						<!-- Color Cartón-->
						{!! armarSelectArrayCreateEditOT([1=>"Café",2=>"Blanco"], 'carton_color', 'Color Cartón' , $errors, $ot ,'form-control',true,true) !!}

						<!-- Cartón-->
						{!! armarSelectArrayCreateEditOT($cartons, 'carton_id', 'Cartón' , $errors, $ot ,'form-control',true,true) !!}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="ot-caracteristicas" class="col-12 mb-2">
		<div class="card h-100">

			<div class="card-header">7.- Características </div>
			<input hidden class="" id="cad_asignado" value="{{ isset($ot->cad_id) ? $ot->cad_id : null}}" style="display:none;"></input>
			<input hidden class="" id="material_asignado" value="{{isset($ot->material_id) ? $ot->material_id : null}}" style="display:none;"></input>
			<input hidden class="" id="ot_id" value="{{isset($ot) ? $ot->id : null}}" style="display:none;"></input>

			<div class="card-body">
				<div class="row">
					<div class="col-4">

                        <div class="form-group form-row">
							<div class="col-11">
                                <div class="" id="cad_input_container">
                                    <!-- CAD:-->
                                    {!! armarInputCreateEditOT('cad', 'CAD:', 'text',$errors, $ot, 'form-control', '', '') !!}
                                </div>
                                <div class="" id="cad_select_container" style="display:none">
                                    <!-- CAD Select -->
                                    {!! armarSelectArrayCreateEditOT($cads, 'cad_id', 'CAD' , $errors, $ot ,'form-control form-element',true,true) !!}
                                    {{-- {!! armarSelectArrayCreateEditOT('', 'matriz_id', 'Matriz' , $errors, $ot ,'form-control',true,true) !!} --}}
                                </div>
                            </div>
                            <div class="col-1">
                                <a herf="#" data-toggle="modal" onclick="searchMatrizCad()" data-target="#modal-search-cad">
                                    <div class="material-icons md-50" data-toggle="tooltip" title="Buscar">search</div>
                                </a>
                            </div>
						</div>

						{{-- <div class="" id="cad_input_container">
							{!! armarInputCreateEditOT('cad', 'CAD:', 'text',$errors, $ot, 'form-control', '', '') !!}
						</div>
						<div class="" id="cad_select_container" style="display:none">
							{!! armarSelectArrayCreateEditOT($cads, 'cad_id', 'CAD' , $errors, $ot ,'form-control form-element',true,true) !!}
						</div> --}}

						<div class="matriz_select_container">
							{!! armarSelectArrayCreateEditOT($matriz, 'matriz_id', 'Matriz ',$errors, $ot, 'form-control form-element', true,true) !!}
						</div>

						{!! armarInputCreateEditOT('tipo_matriz_text', 'Tipo Matriz:', 'text',$errors, $ot, 'form-control', '', '') !!}

						<!-- TIPO ITEM -->
						{!! armarInputCreateEditOT('product_type_id_text', 'Tipo item:', 'text',$errors, $ot, 'form-control', '', '') !!}

						<!-- Tipo Tabique -->
						{{--
						{!! armarSelectArrayCreateEditOT(['Recto' => "Recto", 'Especial'=>"Especial"], 'tipo_tabique', 'Tipo Tabique' , $errors, $ot ,'form-control',true,false) !!}

						<!-- Rayado Desfasado -->
						{!! armarSelectArrayCreateEditOT(['SI' => "Si", 'NO'=>"No"], 'rayado_desfasado', 'Rayado Desfasado' , $errors, $ot ,'form-control',true,false) !!}
						--}}
						<div class="form-group form-row">
							<div class="col-6">
								<!-- Items del set -->
								{!! armarInputCreateEditOT('items_set', 'Items del set:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
							</div>
							<div class="col-6">
								<!-- Veces Item -->
								{!! armarInputCreateEditOT('veces_item', 'Veces Item:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
							</div>
						</div>

						<!-- Color Cartón-->
						{!! armarInputCreateEditOT('carton_color_text', 'Color Cartón:', 'text',$errors, $ot, 'form-control', '', '') !!}

						<!-- Cartón-->
						{!! armarInputCreateEditOT('carton_id_text', 'Cartón:', 'text',$errors, $ot, 'form-control', '', '') !!}

						<!-- Cinta-->
						{!! armarInputCreateEditOT('cinta_text', 'Cinta:', 'text',$errors, $ot, 'form-control', '', '') !!}

						<!-- FSC -->
						{!! armarInputCreateEditOT('fsc_text', 'FSC:', 'text',$errors, $ot, 'form-control', '', '') !!}

						<!-- <div class="row mt-2">
							<div class="col" style="flex-grow:3">
							</div>
							<div class="col" style="flex-grow:13"> -->
								<!-- FSC Observación -->
								<!-- {!! armarInputCreateEditOT('fsc_observacion', 'Observación FSC :', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div> -->

						<!-- CERTIFICADO CALIDAD -->
						{!! armarSelectArrayCreateEditOT($palletQa, 'pallet_qa_id', 'Certificado Calidad' , $errors, $ot ,'form-control',true,false) !!}

						<!-- PAÍS REFERENCIA ahora llamado PAÍS/MERCADO DESTINO -->
						{!! armarSelectArrayCreateEditOT($paisReferencia, 'pais_id', 'PAÍS/MERCADO DESTINO' , $errors, $ot ,'form-control',true,true) !!}
						<!-- PLANTA OBJETIVO -->
						{!! armarInputCreateEditOT('planta_id_text', 'Planta Objetivo:', 'text',$errors, $ot, 'form-control', '', '') !!}

						<!-- RESTRICCIÓN PALLET -->
						{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'restriccion_pallet', 'Restricción Paletizado' , $errors, $ot ,'form-control',true,false) !!}

						<!-- TAMAÑO PALLET -->
						{!! armarSelectArrayCreateEditOT($palletTypes, 'tamano_pallet_type_id', 'Tamaño Pallet' , $errors, $ot ,'form-control',true,true) !!}

						<!-- ALTURA PALLET -->
						{!! armarInputCreateEditOT('altura_pallet', 'Altura Pallet:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

						<!-- PERMITE SOBRESALIR CARGA -->
						{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'permite_sobresalir_carga', 'Permite Sobresalir Carga' , $errors, $ot ,'form-control',true,false) !!}

					</div>
					<div class="col-4">

						<!-- Estilo-->
						{!! armarSelectArrayCreateEditOT($styles, 'style_id', 'Estilo' , $errors, $ot ,'form-control',true,true) !!}

						<!--Caracteristicas Estilo-->
						<div class="form-group form-row">
							<div class="col-11">
								{!! armarInputCreateEditOT('caracteristicas_adicionales', 'Caracteristica Estilo:', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
							@if((auth()->user()->role_id == 5 || auth()->user()->role_id == 6 || auth()->user()->role_id == 18))
								<div class="col-1">
									<a herf="#" data-toggle="modal" data-target="#modal-carac-adicional">
										<div class="material-icons md-50" data-toggle="tooltip" title="Agregar">edit_note</div>
									</a>
								</div>
							@endif
						</div>

						<div class="form-group form-row">
							<div class="col-6">
								<!-- Largura HM -->
								{!! armarInputCreateEditOT('largura_hm', 'Largura HM:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
							</div>
							<div class="col-6">
								<!-- Anchura HM -->
								{!! armarInputCreateEditOT('anchura_hm', 'Anchura HM:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
							</div>
						</div>

						<!-- Área producto (m2) -->
						{!! armarInputCreateEditOT('area_producto', 'Área Producto (m2):', 'text',$errors, $ot, 'form-control', '', '') !!}

						<label id="area-error" style="color:red;margin-bottom:5px;font-size:0.9em;font-weight:normal;text-transform:none;padding-left:1.25rem"></label>

						<!-- Recorte Adicional / Area Agujero -->
						{!! armarInputCreateEditOT('recorte_adicional', 'Recorte Adicional / Area Agujero (m2):', 'text',$errors, $ot, 'form-control', '', '') !!}

						<label id="recorte-error" style="color:red;margin-bottom:5px;font-size:0.9em;font-weight:normal;text-transform:none;padding-left:1.25rem"></label>

						<!-- Liner Exterior -->
						<!-- <div class="form-group form-row">
							<label class="col-auto col-form-label" for="">Liner Externo:</label>
							<div class="col">
								<input id="liner_exterior" type="text" class="form-control-plaintext" value="" readonly="" title="" data-toggle="tooltip" data-original-title="">
							</div>
						</div> -->
						<!-- Longitud Pegado -->
						{!! armarInputCreateEditOT('longitud_pegado', 'Longitud Pegado (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

						<!-- Recubrimiento-->
						<!-- {!! armarSelectArrayCreateEditOT($recubrimiento_type, 'recubrimiento', 'Recubrimiento' , $errors, $ot ,'form-control',true,false) !!} -->
						<!-- [1 => "Cera", 0=>"No"] -->

						<div class="form-group form-row">
							<div class="col-6">
								<!-- Golpes al largo -->
								{!! armarInputCreateEditOT('golpes_largo', 'Golpes al largo:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
							</div>
							<div class="col-6">
								<!-- Golpes al ancho -->
								{!! armarInputCreateEditOT('golpes_ancho', 'Golpes al ancho:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Separación Golpes al Largo -->
								{!! armarInputCreateEditOT('separacion_golpes_largo', 'Separación Golpes al Largo (mm):', 'text',$errors, $ot, 'form-control', '', '') !!}
								<!-- {!! armarInputCreateEditOT('separacion_golpes_largo', 'Separación Golpes al Largo (mm):', 'text',$errors, $ot, 'form-control', 'min="0"', '') !!} -->
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Separación Golpes Ancho -->
								{!! armarInputCreateEditOT('separacion_golpes_ancho', 'Separación Golpes al Ancho (mm):', 'text',$errors, $ot, 'form-control', '', '') !!}
								<!-- {!! armarInputCreateEditOT('separacion_golpes_ancho', 'Separación Golpes al Ancho (mm):', 'text',$errors, $ot, 'form-control', 'min="0"', '') !!} -->
							</div>
						</div>
                        <div class="form-group form-row">
							<div class="col-12">
								<!-- Cuchillas -->
								 {!! armarInputCreateEditOT('cuchillas', 'Cuchillas (ml):', 'text',$errors, $ot, 'form-control', 'min="0.00"', '') !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-6">
								<!-- Rayado C1/R1 (mm) -->
								{!! armarInputCreateEditOT('rayado_c1r1', 'Rayado C1/R1 (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
							</div>
							<div class="col-6">
								<!-- Rayado R1/R2 (mm) -->
								{!! armarInputCreateEditOT('rayado_r1_r2', 'Rayado R1/R2 (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
							</div>

						</div>
						<div class="form-group form-row ">
							<div class="col-6">
								<!-- Rayado R2/C2 (mm) -->
								{!! armarInputCreateEditOT('rayado_r2_c2', 'Rayado R2/C2 (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
							</div>
							<div class="col-6">
							</div>
							<label id="rayado-error" style="color:red;margin-bottom:5px;font-size:0.9em;font-weight:normal;text-transform:none"></label>
						</div>
						<div class="form-group form-row ">
							<div class="col-12">
								<!-- Bulto Zunchado al Pallet -->
								{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'bulto_zunchado', 'Bulto Zunchado al Pallet' , $errors, $ot ,'form-control',true,false) !!}

							</div>
							<div class="col-12">
								<!-- Formato Etiqueta Pallet -->
								{!! armarSelectArrayCreateEditOT($palletTagFormat, 'formato_etiqueta', 'Formato Etiqueta Pallet' , $errors, $ot ,'form-control',true,false) !!}
							</div>
							<div class="col-12">
								<!-- N° Etiquetas por Pallet -->
								{!! armarInputCreateEditOT('etiquetas_pallet', 'N° Etiquetas por Pallet', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
							</div>
							<div class="col-12">
								<!-- Termocontraible -->
								{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'termocontraible', 'Termocontraible' , $errors, $ot ,'form-control',true,false) !!}

							</div>
						</div>

					</div>
					<div class="col-4">

						<div class="form-group form-row">
							<div class="col-6">
								<!-- BCT MIN (LB)-->
								{!! armarInputCreateEditOT('bct_min_lb', 'BCT MIN (LB):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
							</div>
							<div class="col-6">
								<!-- BCT MIN (KG)-->
								<div class="form-group form-row">
									<label class="col-auto col-form-label" for="">BCT MIN (KG):</label>
									<div class="col">
										<input id="bct_min_kg" name="bct_min_kg" type="text" class="form-control" readonly="" value="{{isset($ot) ? $ot->bct_min_kg : null}}">
									</div>
								</div>
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-6">
								<!-- BCT HUMEDO (LB)-->
								{!! armarInputCreateEditOT('bct_humedo_lb', 'BCT HUMEDO (LB):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
							</div>
							<div class="col-6">
								<!-- ECT (lb/pulg) -->
								{!! armarInputCreateEditOT('ect', 'ECT MIN (lb/pulg):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-6">
								<!-- Gramaje (g/m2) -->
								{!! armarInputCreateEditOT('gramaje', 'Gramaje (g/m2):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
							<div class="col-6">
								<!-- MULLEN (LB/PULG2)-->
								{!! armarInputCreateEditOT('mullen', 'Mullen (LB/PULG2):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
							</div>
						</div>
						<div class="form-group form-row ">
							<div class="col-6">
								<!-- FCT (lb/pulg2)  -->
								{!! armarInputCreateEditOT('fct', 'FCT (lb/pulg2):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
							<div class="col-6">
								<!-- Espesor (mm)  -->
								{!! armarInputCreateEditOT('dst', 'DST (BPI):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div>
						<div class="form-group form-row ">
							<div class="col-6">
								<!-- FCT (lb/pulg2)  -->
								{!! armarInputCreateEditOT('espesor_placa', 'Espesor Placa (mm):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
							<div class="col-6">
								<!-- Espesor (mm)  -->
								{!! armarInputCreateEditOT('espesor_caja', 'Espesor Caja (mm):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div>
						<div class="form-group form-row ">
							<div class="col-6">
								<!-- Cobb Interior (g/m2)  -->
								{!! armarInputCreateEditOT('cobb_interior', 'Cobb Interior (g/m2) :', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
							<div class="col-6">
								<!-- Cobb Exterior (g/m2)  -->
								{!! armarInputCreateEditOT('cobb_exterior', 'Cobb Exterior (g/m2) :', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-6">
								<!-- Flexion de aleta (%) -->

								{!! armarInputCreateEditOT('flexion_aleta', 'Flexion de aleta (N):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
							<div class="col-6">
								<!-- Peso (g) -->
								{!! armarInputCreateEditOT('peso', 'Peso Cliente(g):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Incision Rayado Longitudinal -->
								{!! armarInputCreateEditOT('incision_rayado_longitudinal', 'Incisión Rayado Longitudinal (N):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Incision Rayado Vertical -->
								{!! armarInputCreateEditOT('incision_rayado_vertical', 'Incisión Rayado Transversal (N):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-6">
								<!-- Flexion de aleta (%) -->

								{!! armarInputCreateEditOT('porosidad', 'Porosidad (SEG):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
							<div class="col-6">
								<!-- Flexion de aleta (%) -->

								{!! armarInputCreateEditOT('brillo', 'Brillo (%):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div>
						<div class="form-group form-row">

							<div class="col-12">
								<!-- Flexion de aleta (%) -->

								{!! armarInputCreateEditOT('rigidez_4_ptos_long', 'Rigidez 4 Puntos Longitudinal (N/mm):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div>
						<div class="form-group form-row">

							<div class="col-12">
								<!-- Peso (g) -->

								{!! armarInputCreateEditOT('rigidez_4_ptos_transv', 'Rigidez 4 Puntos Transversal (N/mm):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Incision Rayado Vertical -->
								{!! armarInputCreateEditOT('angulo_deslizamiento_tapa_exterior', 'Angulo de Deslizamiento-Tapa Exterior (°):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Incision Rayado Vertical -->
								{!! armarInputCreateEditOT('angulo_deslizamiento_tapa_interior', 'Angulo de Deslizamiento-Tapa Interior (°):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Incision Rayado Vertical -->
								{!! armarInputCreateEditOT('resistencia_frote', 'Resistencia al Frote:', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Incision Rayado Vertical -->
								{!! armarInputCreateEditOT('contenido_reciclado', 'Contenido Reciclado (%):', 'text',$errors, $ot, 'form-control', '', '') !!}
							</div>
						</div>
						@if(auth()->user()->role_id == 5 || auth()->user()->role_id == 6)
							<hr class="dashed">
							<div class="form-group form-row">
								@if($check_mckee)
									<div class="col-5">
										<div class="form-group form-row">
											<label class="col-auto col-form-label" for="">FORMULA MCKEE</label>
											<button class="btn btn-success btn-sm" type="button" data-toggle="collapse" data-target="#seccion_formula_mckee" id="button_formula_mckee">Calcular</button>
										</div>
									</div>
									<div class="col-1">
										<div class="form-group form-row">
											<div class="material-icons" herf="#" data-toggle="modal" data-target="#modal-log-mckee" title="Log Datos Mckee" style="">description</div>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group form-row">
											<label class="col-auto col-form-label" for="">Análisis Anchura</label>
											<button class="btn btn-success btn-sm" type="button" data-toggle="collapse" data-target="#seccion_combinabilidad" id="button_formula_combinabilidad">Calcular</button>
										</div>
									</div>
								@else
									<div class="col-6">
										<div class="form-group form-row">
											<label class="col-auto col-form-label" for="">FORMULA MCKEE</label>
											<button class="btn btn-success btn-sm" type="button" data-toggle="collapse" data-target="#seccion_formula_mckee" id="button_formula_mckee">Calcular</button>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group form-row">
											<label class="col-auto col-form-label" for="">Análisis Anchura</label>
											<button class="btn btn-success btn-sm" type="button" data-toggle="collapse" data-target="#seccion_combinabilidad" id="button_formula_combinabilidad">Calcular</button>
										</div>
									</div>
								@endif
							</div>
							<div class="collapse" id="seccion_formula_mckee">
								<div class="form-group form-row">
									<div class="col-4">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('largo_mckee', 'Largo:', 'number',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
									<div class="col-4">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('ancho_mckee', 'Ancho:', 'number',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
									<div class="col-4">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('alto_mckee', 'Alto:', 'number',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
								</div>
								<div class="form-group form-row">
									<div class="col-7">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('perimetro_mckee', 'Perimetro Resistente:', 'number',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
									<div class="col-5">
										<div class="form-group form-row">
											{!! armarSelectArrayCreateEditOT($cartons, 'carton_id_mckee', 'Cartón' , $errors, $ot ,'form-control',true,true) !!}
										</div>
									</div>
								</div>
								<div class="form-group form-row">
									<div class="col-6">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('ect_mckee', 'Ect:', 'number',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
									<div class="col-6">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('espesor_mckee', 'Espesor:', 'number',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
								</div>
								<div class="form-group form-row">
									<div class="col-6">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('bct_lib_mckee', 'BCT LB Min:', 'number',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
									<div class="col-6">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('bct_kilos_mckee', 'BCT Kilos Min:', 'number',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
								</div>
								<div class="form-group form-row">
									<div class="col-10">
										&nbsp;
									</div>
									<div class="col-2">
										<div class="form-group form-row">
											<button class="btn btn-success btn-sm hidden" type="button"  id="button_aplicar_mckee">Aplicar</button>
										</div>
									</div>
								</div>
							</div>
							<div class="collapse" id="seccion_combinabilidad">
								<div class="form-group form-row">
									<div class="col-8">
										<div class="form-group form-row" id="carton_combinabilidad_select">
											{!! armarSelectArrayCreateEditOT($cartons, 'carton_id_combinabilidad', 'Denominación:' , $errors, $ot ,'form-control',true,true) !!}
										</div>
									</div>
									<div class="col-4">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('hc_combinabilidad', 'HC:', 'number',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
								</div>
								<div class="form-group form-row">
									<div class="col-6">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('formato_optimo', 'Formato Optimo:', 'number',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
									<div class="col-6">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('numero_cortes', 'Numero Cortes:', 'number',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
								</div>
								<div class="form-group form-row">
									<div class="col-6">

										<div class="form-group form-row">
											{!! armarInputCreateEditOT('perdida_minima', 'Perdida Minima:', 'text',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
									<div class="col-6">

										<div class="form-group form-row">
											{!! armarInputCreateEditOT('perdida_minima_mm', 'Perdida Minima mm:', 'number',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
								</div>

								<div class="form-group form-row">
									<div class="col-4">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('1750_combinabilidad', '1750:', 'text',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
									<div class="col-4">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('1830_combinabilidad', '1830:', 'text',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
									<div class="col-4">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('1900_combinabilidad', '1900:', 'text',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
								</div>
								<div class="form-group form-row">
									<div class="col-4">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('1950_combinabilidad', '1950:', 'text',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
									<div class="col-4">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('2040_combinabilidad', '2040:', 'text',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
									<div class="col-4">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('2180_combinabilidad', '2180:', 'text',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
								</div>
								<div class="form-group form-row">
									<div class="col-4">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('2250_combinabilidad', '2250:', 'text',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
									<div class="col-4">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('2350_combinabilidad', '2350:', 'text',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
									<div class="col-4">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('2450_combinabilidad', '2450:', 'text',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
								</div>
								<div class="form-group form-row">
									<div class="col-4">
										<div class="form-group form-row">
											{!! armarInputCreateEditOT('2500_combinabilidad', '2500:', 'text',$errors, $ot, 'form-control', '', '') !!}
										</div>
									</div>
								</div>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="ot-distancia-cinta" class="col-12 mb-2" style="display:none">
		<div class="card h-100">

			<div class="card-header">Distancia Cinta </div>

			<div class="card-body">
				<div class="row">
					<div class="col-4">
						<!-- Distancia Corte 1 a Cinta 1 -->
						{!! armarInputCreateEditOT('distancia_cinta_1', 'Distancia Corte 1 a Cinta 1 (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
						<!-- Distancia Corte 1 a Cinta 2 -->
						{!! armarInputCreateEditOT('distancia_cinta_2', 'Distancia Corte 1 a Cinta 2 (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
						<!-- Distancia Corte 1 a Cinta 3 -->
						{!! armarInputCreateEditOT('distancia_cinta_3', 'Distancia Corte 1 a Cinta 3 (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
					</div>
					<div class="col-4">
						<!-- Distancia Corte 1 a Cinta 4 -->
						{!! armarInputCreateEditOT('distancia_cinta_4', 'Distancia Corte 1 a Cinta 4 (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
						<!-- Distancia Corte 1 a Cinta 5 -->
						{!! armarInputCreateEditOT('distancia_cinta_5', 'Distancia Corte 1 a Cinta 5 (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
						<!-- Distancia Corte 1 a Cinta 6 -->
						{!! armarInputCreateEditOT('distancia_cinta_6', 'Distancia Corte 1 a Cinta 6 (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
					</div>
					<div class="col-4">
						<!-- Corte de Liner:-->
						{!! armarSelectArrayCreateEditOT([1 => "SI", 0=>"NO"], 'corte_liner', 'Corte de Liner:' , $errors, $ot ,'form-control',true,false) !!}
						<!-- Tipo de Cinta-->
						{{-- {!! armarSelectArrayCreateEditOT([1 => "Corte", 2=>"Resistencia"], 'tipo_cinta', 'Tipo de Cinta' , $errors, $ot ,'form-control',true,false) !!} --}}
						{!! armarSelectArrayCreateEditOT($tipoCinta, 'tipo_cinta', 'Tipo de Cinta' , $errors, $ot ,'form-control',true,false) !!}

						{!! armarInputCreateEditOT('cintas_x_caja', 'Cantidad Cintas por Caja:', 'number',$errors, $ot, 'form-control', 'min="0" max="10"', '') !!}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="ot-colores" class="col-12 mb-2" >
		<div class="card h-100">
			<div class="card-header">8.- Color-Cera-Barniz</div>
			<!-- @if($tipo == "edit" && Auth()->user()->isVendedor())
				<div class="alert-warning" style="padding:5px;margin:10px;border-radius:7px;">
					NOTA: Para editar, primero se deben seleccionar los campos de RECUBRIMIENTO INTERNO y RECUBRIMIENTO EXTERNO.
				</div>
			@endif -->
			<div class="card-body">
				<div class="row">

					<div class="col-3">
						<!-- Impresión -->
						{!! armarInputCreateEditOT('impresion_text', 'Impresión:', 'text',$errors, $ot, 'form-control', '', '') !!}

                        <!-- TRAZABILIDAD -->
						{!! armarSelectArrayCreateEditOT($trazabilidad, 'trazabilidad', 'Trazabilidad', $errors, $ot ,'form-control form-element',true,true) !!}

						<!-- TIPO Diseno -->
						{!! armarSelectArrayCreateEditOT($designTypes, 'design_type_id', 'Tipo Diseño', $errors, $ot ,'form-control',true,false) !!}

                      	<!-- Complejidad -->
						{!! armarInputCreateEditOT('complejidad', 'Complejidad', 'text',$errors, $ot, 'form-control', '', '') !!}

						<!-- Numero Colores-->
						{!! armarSelectArrayCreateEditOT([0,1,2,3,4,5,6,7], 'numero_colores', 'Número Colores' , $errors, $ot ,'form-control',true,true) !!}

						<!-- Recubrimiento Interno -->
						{!! armarInputCreateEditOT('coverage_internal_id_text', 'Recubrimiento Interno:', 'text',$errors, $ot, 'form-control', '', '') !!}

						<!-- % Recubrimiento Interno -->
						{!! armarInputCreateEditOT('percentage_coverage_internal', '% Recubrimiento Interno:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

						<!-- Recubrimiento Externo -->
						{!! armarInputCreateEditOT('coverage_external_id_text', 'Recubrimiento Externo:', 'text',$errors, $ot, 'form-control', '', '') !!}

						<!-- % Recubrimiento Externo -->
						{!! armarInputCreateEditOT('percentage_coverage_external', '% Recubrimiento Externo:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

					</div>
					<div class="col-3">

						<!-- Color 1-->
						{!! armarSelectArrayCreateEditOT($colors, 'color_1_id', 'Color 1 (INTERIOR TyR)' , $errors, $ot ,'form-control',true,true,"") !!}
						<!-- % Impresión 1 -->
						{!! armarInputCreateEditOT('impresion_1', '% Impresión 1:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

                        <!-- % Clisse Cm2 1 -->
						{!! armarInputCreateEditOT('cm2_clisse_color_1', 'Clisse cm2 1:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
						<br>
						<!-- Color 2-->
						{!! armarSelectArrayCreateEditOT($colors, 'color_2_id', 'Color 2' , $errors, $ot ,'form-control',true,true,"") !!}
						<!-- % Impresión 2 -->
						{!! armarInputCreateEditOT('impresion_2', '% Impresión 2:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

                        <!-- % Clisse Cm2 2 -->
						{!! armarInputCreateEditOT('cm2_clisse_color_2', 'Clisse cm2 2:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
						<br>
						<!-- Color 3-->
						{!! armarSelectArrayCreateEditOT($colors, 'color_3_id', 'Color 3' , $errors, $ot ,'form-control',true,true,"") !!}
						<!-- % Impresión 3 -->
						{!! armarInputCreateEditOT('impresion_3', '% Impresión 3:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

                        <!-- % Clisse Cm2 3 -->
						{!! armarInputCreateEditOT('cm2_clisse_color_3', 'Clisse cm2 3:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
						<br>
						<!-- Color 4-->
						{!! armarSelectArrayCreateEditOT($colors, 'color_4_id', 'Color 4' , $errors, $ot ,'form-control',true,true,"") !!}
						<!-- % Impresión 4 -->
						{!! armarInputCreateEditOT('impresion_4', '% Impresión 4:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

                        <!-- % Clisse Cm2 4 -->
						{!! armarInputCreateEditOT('cm2_clisse_color_4', 'Clisse cm2 4:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

                    </div>
					<div class="col-3">
						<!-- Color 5-->
						{!! armarSelectArrayCreateEditOT($colors, 'color_5_id', 'Color 5' , $errors, $ot ,'form-control',true,true,"") !!}
						<!-- % Impresión 5 -->
						{!! armarInputCreateEditOT('impresion_5', '% Impresión 5:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

                        <!-- % Clisse Cm2 5 -->
						{!! armarInputCreateEditOT('cm2_clisse_color_5', 'Clisse cm2 5:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
						<br>
						<!-- Color 6-->
						{!! armarSelectArrayCreateEditOT($colors, 'color_6_id', 'Color 6' , $errors, $ot ,'form-control',true,true,"") !!}
						<!-- % Impresión 6 -->
						{!! armarInputCreateEditOT('impresion_6', '% Impresión 6:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

                        <!-- % Clisse Cm2 6 -->
						{!! armarInputCreateEditOT('cm2_clisse_color_6', 'Clisse cm2 6:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
						<br>
						<!-- Barniz UV -->
						{!! armarSelectArrayCreateEditOT($colors_barniz, 'barniz_uv', 'Color 7 (Barniz UV)' , $errors, $ot ,'form-control',true,true,"") !!}
						<!-- % Barniz UV -->
						{!! armarInputCreateEditOT('porcentanje_barniz_uv', '% Impresión 7 UV:', 'number',$errors, $ot, 'form-control', '', '') !!}

                        <!-- % Clisse Cm2 7 -->
						{!! armarInputCreateEditOT('cm2_clisse_color_7', 'Clisse cm2 7:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

					</div>
					<div class="col-3">

						<!-- Indicador Facturación Diseño Gráfico -->
						{!! armarInputCreateEditOT('indicador_facturacion_diseno_grafico', 'Indicador Facturación D.G.', 'text',$errors, $ot, 'form-control', '', '') !!}

						<!-- Prueba de Color -->
						{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'prueba_color', 'Prueba de Color' , $errors, $ot ,'form-control',true,false) !!}
						{{--Ajuste Evolutivo 24-06 de fecha 03-04-2024 correo del cliente
						<!-- Complejidad Impresion -->
						{!! armarSelectArrayCreateEditOT(['Baja' => "Baja", 'Media'=>"Media", 'Alta'=>"Alta"], 'complejidad_impresion', 'Complejidad de Impresión' , $errors, $ot ,'form-control',true,false) !!}
						--}}
						<!--Impresion de Borde -->
						{!! armarSelectArrayCreateEditOT(['SI' => "Si", 'NO'=>"No"], 'impresion_borde', 'Impresión de Borde' , $errors, $ot ,'form-control',true,false) !!}

						<!--Impresion sobre Rayado -->
						{!! armarSelectArrayCreateEditOT(['SI' => "Si", 'NO'=>"No"], 'impresion_sobre_rayado', 'Impresión Sobre Rayado' , $errors, $ot ,'form-control',true,false) !!}

						<!-- % Total Clisse Cm2  -->
						{!! armarInputCreateEditOT('total_cm2_clisse', 'Total clisse cm2:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Anterior Respaldo -->
		<!-- Pegado -->
		<!-- {!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'pegado', 'Pegado' , $errors, $ot ,'form-control',true,false) !!}	-->

		<!-- Cera Exterior -->
		<!-- {!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'cera_exterior', 'Cera Exterior' , $errors, $ot ,'form-control',true,false) !!} -->
		<!-- % Cera Exterior -->
		<!-- {!! armarInputCreateEditOT('porcentaje_cera_exterior', '% Cera Exterior:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!} -->

		<!-- Cera Interior -->
		<!-- {!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'cera_interior', 'Cera Interior' , $errors, $ot ,'form-control',true,false) !!} -->
		<!-- % Cera Interior -->
		<!-- {!! armarInputCreateEditOT('porcentaje_cera_interior', '% Cera Interior:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!} -->

		<!-- Barniz Interior -->
		<!-- {!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'barniz_interior', 'Barniz Externo' , $errors, $ot ,'form-control',true,false) !!} -->
		<!-- % Barniz Interior -->
		<!-- {!! armarInputCreateEditOT('porcentaje_barniz_interior', '% Barniz Externo:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!} -->

	<!-- Fin Anterior Respaldo-->

	<div id="ot-medidas-interiores" class="col-4 mb-2" >
		<div class="card h-100">
			<div class="card-header">9.- Medidas Interiores</div>
			<div class="card-body form-row">
				<div class="col-4">
					<!-- Largo (mm) -->
					{!! armarInputCreateEditOT_2('interno_largo', 'Largo (mm):', 'text',$errors, $ot, 'form-control', 'min="0"', '') !!}
				</div>
				<div class="col-4">
					<!-- Ancho (mm) -->
					{!! armarInputCreateEditOT('interno_ancho', 'Ancho (mm):', 'text',$errors, $ot, 'form-control', 'min="0"', '') !!}

				</div>
				<div class="col-4">
					<!-- Alto (mm) -->
					{!! armarInputCreateEditOT('interno_alto', 'Alto (mm):', 'text',$errors, $ot, 'form-control', 'min="0"', '') !!}

				</div>
			</div>
			<label id="medida-interior-error" style="color:red;margin-bottom:5px;font-size:0.9em;font-weight:normal;text-transform:none;padding-left:1.25rem"></label>

		</div>
	</div>

	<div id="ot-medidas-exteriores" class="col-4 mb-2" >
		<div class="card h-100">
			<div class="card-header">10.- Medidas Exteriores</div>
			<div class="card-body form-row">

				<div class="col-4">
					<!-- Largo (mm) -->
					{!! armarInputCreateEditOT('externo_largo', 'Largo (mm):', 'text',$errors, $ot, 'form-control', 'min="0"', '') !!}
				</div>
				<div class="col-4">
					<!-- Ancho (mm) -->
					{!! armarInputCreateEditOT('externo_ancho', 'Ancho (mm):', 'text',$errors, $ot, 'form-control', 'min="0"', '') !!}

				</div>
				<div class="col-4">
					<!-- Alto (mm) -->
					{!! armarInputCreateEditOT('externo_alto', 'Alto (mm):', 'text',$errors, $ot, 'form-control', 'min="0"', '') !!}

				</div>
			</div>
		</div>
	</div>

	<div id="ot-terminaciones" class="col-4 mb-2" >
		<div class="card h-100">
			<div class="card-header">11.- Terminaciones</div>
			<div class="card-body form-row">
				<div class="col-4">
					<!-- Proceso -->
					{!! armarSelectArrayCreateEditOT($procesos, 'process_id', 'Proceso' , $errors, $ot ,'form-control',true,false) !!}
				</div>
				<div class="col-4">
					<!-- Pegado -->
					{!! armarSelectArrayCreateEditOT([
						0=>"No Aplica",
						2=>"Pegado Interno",
						3=>"Pegado Externo",
						4=>"Pegado 3 Puntos",
						5=>"Pegado 4 Puntos"], 'pegado_terminacion', 'Tipo Pegado' , $errors, $ot ,'form-control',true,false,"118px") !!}
				</div>
				<div class="col-4">
					<!-- Armado -->
					{!! armarSelectArrayCreateEditOT($armados, 'armado_id', 'Armado' , $errors, $ot ,'form-control',true,false) !!}
				</div>
				<div class="col-12 mt-2">
					<!-- Sentido Arm. -->
					{!! armarSelectArrayCreateEditOT($sentidos_armado, 'sentido_armado', 'Sentido de Armado &nbsp;' , $errors, $ot ,'form-control',true,false) !!}

				</div>
				<div class="col-12 mt-2">
					<!-- Maquila-->
					{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'maquila', 'Maquila' , $errors, $ot ,'form-control',true,false) !!}
				</div>
				<div class="col-12 mt-2">
					<!-- Servicios Maquila-->
					{!! armarSelectArrayCreateEditOT($maquila_servicios, 'maquila_servicio_id', 'Servicios Maquila' , $errors, $ot ,'form-control',true,true) !!}
				</div>
			</div>
		</div>
	</div>
	<?php /*
	<div id="ot-sentido-onda" class="col-4 mb-2">
		<div class="card h-100">
			<div class="card-header">Sentido de onda</div>
			<div class="card-body">
				<!-- Tipo Sentido de Onda -->
				{!! armarSelectArrayCreateEditOT(["Vertical" => "Vertical", "Horizontal"=>"Horizontal"], 'tipo_sentido_onda', 'Tipo Sentido de Onda' , $errors, $ot ,'form-control',true,false) !!}
			</div>
		</div>
	</div>
	 */ ?>
	<!-- <div id="ot-sentido-onda" class="col-4 mb-2"> -->
		<!-- <div class="card h-100">
			<div class="card-header">Impresión</div>
			<div class="card-body">
			</div>
		</div> -->
	<!-- </div>  -->
	<div id="ot-secuencia operacional" class="col-12 mb-2" >
		<div class="card h-100">
			<div class="card-header">12.- Secuencia Operacional</div>
			<div class="card-body">
				<div class="form-row">
					<div id="ot-secuencia-planta-original" class="col-12 mb-2" >
						<div class="card h-200" id="fila_planta_original">
                            <div class="form-row" style="margin-left: 0px;margin-right: 0px; margin-top: 5px;">
								<div class="col-4">
									{!! armarInputCreateEditOT('planta_original_sec_ope', 'Planta:', 'text',$errors, $ot, 'form-control', '', '') !!}
								</div>
								<div class="col-3">
									&nbsp;
								</div>
								{{-- <div class="col-5">
									<button type="button" class="btn btn-outline-primary" id="agregar_fila_planta_original" name="agregar_fila_planta_original">
										Agregar Operación
									</button>
								</div> --}}
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuencia($secuenciaOperacional, 'sec_ope_ppal_planta_ori_1', 'Original' , $errors, $ot ,'form-control',true,true,'-12') !!}
								</div>
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuencia($secuenciaOperacional, 'sec_ope_atl_1_planta_ori_1', 'Alternativa 1' , $errors, $ot ,'form-control',true,true,'-12') !!}
								</div>
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuencia($secuenciaOperacional, 'sec_ope_atl_2_planta_ori_1', 'Alternativa 2' , $errors, $ot ,'form-control',true,true,'-12') !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuencia($secuenciaOperacional, 'sec_ope_atl_3_planta_ori_1', 'Alternativa 3' , $errors, $ot ,'form-control',true,true,'-12') !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuencia($secuenciaOperacional, 'sec_ope_atl_4_planta_ori_1', 'Alternativa 4' , $errors, $ot ,'form-control',true,true,'-12') !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuencia($secuenciaOperacional, 'sec_ope_atl_5_planta_ori_1', 'Alternativa 5' , $errors, $ot ,'form-control',true,true,'-12') !!}
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-12">
									&nbsp;
								</div>
							</div>
							<div class="form-row"style="margin-left: 0px;margin-right: 0px;">
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_ppal_planta_ori_2', $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_1_planta_ori_2', $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_2_planta_ori_2', $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_3_planta_ori_2', $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_4_planta_ori_2', $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_5_planta_ori_2', $errors, $ot ,'form-control',true,true) !!}
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-12">
									&nbsp;
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_ppal_planta_ori_3' , $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_1_planta_ori_3' , $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_2_planta_ori_3' , $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_3_planta_ori_3' , $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_4_planta_ori_3' , $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_5_planta_ori_3' , $errors, $ot ,'form-control',true,true) !!}
								</div>
							</div>
                            <div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-12">
									&nbsp;
								</div>
							</div>
                            <div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_ppal_planta_ori_4' , $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_1_planta_ori_4' , $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_2_planta_ori_4' , $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_3_planta_ori_4' , $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_4_planta_ori_4' , $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_5_planta_ori_4' , $errors, $ot ,'form-control',true,true) !!}
								</div>
							</div>
                            <div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-12">
									&nbsp;
								</div>
							</div>
                            <div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_ppal_planta_ori_5' , $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_1_planta_ori_5' , $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_2_planta_ori_5' , $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_3_planta_ori_5' , $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_4_planta_ori_5' , $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_5_planta_ori_5' , $errors, $ot ,'form-control',true,true) !!}
								</div>
							</div>
                            <div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-12">
									&nbsp;
								</div>
							</div>
                            <div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_ppal_planta_ori_6' , $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_1_planta_ori_6' , $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_2_planta_ori_6' , $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_3_planta_ori_6' , $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_4_planta_ori_6' , $errors, $ot ,'form-control',true,true) !!}
								</div>
                                <div class="col-2">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_5_planta_ori_6' , $errors, $ot ,'form-control',true,true) !!}
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-12">
									&nbsp;
								</div>
							</div>
						</div>
					</div>
					{{-- <div id="ot-secuencia-planta-auxiliar-1" class="col-4 mb-2" >
						<div class="card h-200" id="fila_planta_aux_1">
							<div class="form-row" style="margin-left: 0px;margin-right: 0px; margin-top: 5px;">
								<div class="col-4">
									{!! armarInputCreateEditOT('planta_aux_1_sec_ope', 'Planta:', 'text',$errors, $ot, 'form-control', '', '') !!}
								</div>
								<div class="col-1">
									<input type="checkbox" id="check_planta_aux_1" name="check_planta_aux_1" title="Habilitar Planta">
								</div>
								<div class="col-2">
									&nbsp;
								</div>
								<div class="col-5">
									<button type="button" class="btn btn-outline-primary" id="agregar_fila_planta_auxiliar_1" name="agregar_fila_planta_auxiliar_1">
										Agregar Operación
									</button>
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOT($secuenciaOperacional, 'sec_ope_ppal_planta_aux_1_1', 'Original' , $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOT($secuenciaOperacional, 'sec_ope_atl_1_planta_aux_1_1', 'Alternativa 1' , $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOT($secuenciaOperacional, 'sec_ope_atl_2_planta_aux_1_1', 'Alternativa 2' , $errors, $ot ,'form-control',true,true) !!}
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-12">
									&nbsp;
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_ppal_planta_aux_1_2', $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-4" >
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_1_planta_aux_1_2', $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_2_planta_aux_1_2', $errors, $ot ,'form-control',true,true) !!}
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-12">
									&nbsp;
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_ppal_planta_aux_1_3',$errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_1_planta_aux_1_3',$errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_2_planta_aux_1_3',$errors, $ot ,'form-control',true,true) !!}
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-12">
									&nbsp;
								</div>
							</div>
						</div>
					</div>
					<div id="ot-secuencia-planta-auxiliar-2" class="col-4 mb-2" >
						<div class="card h-200" id="fila_planta_aux_2">
							<div class="form-row" style="margin-left: 0px;margin-right: 0px; margin-top: 5px;">
								<div class="col-4">
									{!! armarInputCreateEditOT('planta_aux_2_sec_ope', 'Planta:', 'text',$errors, $ot, 'form-control', '', '') !!}
								</div>
								<div class="col-1">
									<input type="checkbox" id="check_planta_aux_2" name="check_planta_aux_2" title="Habilitar Planta">
								</div>
								<div class="col-2">
									&nbsp;
								</div>
								<div class="col-5">
									<button type="button" class="btn btn-outline-primary" id="agregar_fila_planta_auxiliar_2" name="agregar_fila_planta_auxiliar_2">
										Agregar Operación
									</button>
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOT($secuenciaOperacional, 'sec_ope_ppal_planta_aux_2_1', 'Original' , $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOT($secuenciaOperacional, 'sec_ope_atl_1_planta_aux_2_1', 'Alternativa 1' , $errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOT($secuenciaOperacional, 'sec_ope_atl_2_planta_aux_2_1', 'Alternativa 2' , $errors, $ot ,'form-control',true,true) !!}
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-12">
									&nbsp;
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_ppal_planta_aux_2_2',$errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_1_planta_aux_2_2',$errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_2_planta_aux_2_2',$errors, $ot ,'form-control',true,true) !!}
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-12">
									&nbsp;
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_ppal_planta_aux_2_3',$errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_1_planta_aux_2_3',$errors, $ot ,'form-control',true,true) !!}
								</div>
								<div class="col-4">
									<!-- Secuencia Operacional -->
									{!! armarSelectArrayCreateEditOTSecuenciaOperacional($secuenciaOperacional, 'sec_ope_atl_2_planta_aux_2_3',$errors, $ot ,'form-control',true,true) !!}
								</div>
							</div>
							<div class="form-row" style="margin-left: 0px;margin-right: 0px;">
								<div class="col-12">
									&nbsp;
								</div>
							</div>
						</div>
					</div> --}}
				</div>
			</div>
		</div>
	</div>

	<div id="ot-material" class="col-12 mb-2" >
		<div class="card h-100">
			<div class="card-header">13.- Material Asignado</div>
			<div class="card-body">
				<div class="form-group form-row ">
					<div class="col-4">
						<!-- Material Asignado -->
						{!! inputReadOnly('Material Asignado', isset($ot->material) ? $ot->material->codigo : null, 'material_asignado') !!}
					</div>
					<div class="col-8">
						<!-- Descripción -->
						@if(Auth()->user()->isSuperAdministrador())
							{!! inputEditDescripcion('Descripción', isset($ot->material) ? $ot->material->descripcion : null, 'descripcion_material') !!}
						@else
							{!! inputReadOnly('Descripción', isset($ot->material) ? $ot->material->descripcion : null, 'descripcion_material') !!}
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="ot-desarrollo" class="col-7 mb-2" >
		<div class="card h-100">
			<div class="card-header">13.- Datos para desarrollo</div>
			<div class="card-body">
				<div class="form-row">
					<div class="col-6">
						<!-- Tipo Producto -->
						{!! armarSelectArrayCreateEditOT($productTypeDeveloping, 'product_type_developing_id', 'Tipo Producto:' , $errors, $ot ,'form-control',true,false) !!}

						<!-- Tipo Alimento -->
						{!! armarSelectArrayCreateEditOT($foodType, 'food_type_id', 'Tipo Alimento:' , $errors, $ot ,'form-control',true,false) !!}

						<!-- Uso Previsto -->
						{!! armarSelectArrayCreateEditOT($expectedUse, 'expected_use_id', 'Uso Previsto:' , $errors, $ot ,'form-control',true,false) !!}

						<!-- Uso Reciclado -->
						{!! armarSelectArrayCreateEditOT($recycledUse,'recycled_use_id', 'Uso Reciclado:', $errors, $ot, 'form-control',true,false) !!}

						<!-- Clase Sustancia a Embalar -->
						{!! armarSelectArrayCreateEditOT($classSubstancePacked, 'class_substance_packed_id', 'Clase Sustancia a Embalar:' , $errors, $ot ,'form-control',true,false) !!}

						<!-- Medio de Transporte -->
						{!! armarSelectArrayCreateEditOT($transportationWay, 'transportation_way_id', 'Medio de Transporte:' , $errors, $ot ,'form-control',true,false) !!}

					</div>
					<div class="col-6">
						{!! armarInputCreateEditOT('peso_contenido_caja', 'Peso Contenido caja (Kg):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

						<!-- Autosoportante -->
						{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'autosoportante', 'Autosoportante' , $errors, $ot ,'form-control',true,false) !!}

						<!-- Envase Primario -->
						{!! armarSelectArrayCreateEditOT($envases, 'envase_id', 'Envase Primario' , $errors, $ot ,'form-control',true,true) !!}
						<!-- 							CAMBIAR DATOS -->

						<!-- Cuantas cajas apilan en altura -->
						{!! armarInputCreateEditOT('cajas_altura', 'Cantidad Cajas Apiladas:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

						<!-- Pallet Sobre pallet -->
						{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'pallet_sobre_pallet', 'Pallet Sobre pallet' , $errors, $ot ,'form-control',true,false) !!}

						<!-- Cantidad -->
						{!! armarInputCreateEditOT('cantidad', 'Cantidad:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

						<!-- Mercado Destino -->
						{!! armarSelectArrayCreateEditOT($targetMarket, 'target_market_id', 'Mercado Destino:' , $errors, $ot ,'form-control',true,false)  !!}

					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="ot-sentido-onda" class="col-5 mb-2" >
		<div class="card h-100">
			<div class="card-header">14.- Observación del trabajo a realizar</div>
			<div class="card-body">
				<div class="form-group form-row">
					<div class="col-12">
						<textarea class="{{$errors->has('observacion') ? 'error' : ''}}" style="resize: none;border-color:#3aaa35" name="observacion" id="observacion" cols="70" rows="12">@if(old('observacion')) {{old('observacion')}} @elseif(isset($ot->observacion) && !old('_token') && $tipo=='edit') {{ $ot->observacion}} @endif </textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-indicaciones-especiales" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
			  <h4 class="page-title">Indicaciones Especiales Cliente</h4>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			<div class="modal-body">
				<table id="client_indicaciones_view" name="client_indicaciones_view">
					<tbody></tbody>
				</table>
			</div>
			<div class="modal-footer">
			  <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
			</div>
		  </div>
	</div>
</div>

<div class="modal fade" id="modal-indicaciones-especiales-edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
			  <h4 class="page-title">Indicaciones Especiales Cliente</h4>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			<div class="modal-body">
				<table id="client_indicaciones_view" name="client_indicaciones_view">
					<tbody></tbody>
				</table>
			</div>
			<div class="modal-footer">
			  <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
			</div>
		  </div>
	</div>
</div>


<div class="modal fade" id="modal-search-cad" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
			  <h4 class="page-title">Buscar Matriz</h4>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			<div class="modal-body">

                {{-- <div class="row">
                    <div class="col-4">
                        <div class="form-group form-row">
                            <label class="col-auto col-form-label">Ingrese CAD</label>
                            <div class="col">
                                <input class="form-control" type="text" id="search_plano_cad" name="search_plano_cad">
                            </div>
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="btn btn-success" onclick="searchMatrizCad()">Buscar</div>
                    </div>
                </div>
                <hr> --}}
                <div class="row">
                    <div class="col-12">

                        <div class="container-table mt-3 bg-white border px-2">
                        <table class="table table-status table-hover actions states" id="matriz_table_view" name="matriz_table_view">
                            <thead>
                                <tr>
                                  <th>Plano CAD</th>
                                  <th>Material</th>
                                  <th>Texto Breve Material</th>
                                  <th>Largo Matriz</th>
                                  <th>Ancho Matriz</th>
                                  <th>Cant. al largo en Matriz</th>
                                  <th>Cant. al ancho en Matriz</th>
                                  <th>Separacion largo Matriz</th>
                                  <th>Separacion ancho Matriz</th>
                                  <th>Tipo Matriz</th>
                                  <th>Total Golpes</th>
                                  <th>Máquina</th>
                                  <th>Estado</th>
                                </tr>
                              </thead>
                            <tbody id="body_matriz_table_view"></tbody>
                        </table>
                        </div>
                    </div>
                </div>

			</div>
			<div class="modal-footer">
			  <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
			</div>
		  </div>
	</div>
</div>

<div class="modal fade" id="modal-carac-adicional">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-center">
				<div class="title"><h3>Caracteristicas Estilo</h3></div>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-7">
						<label class="form-label"><b>Característica</b></label>
					</div>
					<div class="col-2">
						<center><label class="form-label"><b>Letra</b></label></center>
					</div>
					<div class="col-3">
						<center><label class="form-label"><b>Seleccionar</b></label></center>
					</div>
				</div>

				<div class="row">
					<div class="col-7">
						<label class="form-label">Traba Anclaje</label>
					</div>
					<div class="col-2">
						<center><label class="form-label">A</label></center>
					</div>
					<div class="col-3">
						<center><input type="checkbox" id="check_a" name="check_a" ></center>
					</div>
				</div>

				<div class="row">
					<div class="col-7">
						<label class="form-label">Ceja Pegado Extendida</label>
					</div>
					<div class="col-2">
						<center><label class="form-label">C</label></center>
					</div>
					<div class="col-3">
						<center><input type="checkbox" id="check_c" name="check_c" ></center>
					</div>
				</div>
				<div class="row">
					<div class="col-7">
						<label class="form-label">Pegado Exterior</label>
					</div>
					<div class="col-2">
						<center><label class="form-label">E</label></center>
					</div>
					<div class="col-3">
						<center><input type="checkbox" id="check_e" name="check_e"></center>
					</div>
				</div>
				<div class="row">
					<div class="col-7">
						<label class="form-label">Hibrida</label>
					</div>
					<div class="col-2">
						<center><label class="form-label">H</label></center>
					</div>
					<div class="col-3">
						<center><input type="checkbox" id="check_h" name="check_h"></center>
					</div>
				</div>
				<div class="row">
					<div class="col-7">
						<label class="form-label">Cabezal o lateral inclinado</label>
					</div>
					<div class="col-2">
						<center><label class="form-label">I</label></center>
					</div>
					<div class="col-3">
						<center><input type="checkbox" id="check_i" name="check_i"></center>
					</div>
				</div>
				<div class="row">
					<div class="col-7">
						<label class="form-label">Cajas doble Lateral</label>
					</div>
					<div class="col-2">
						<center><label class="form-label">L</label></center>
					</div>
					<div class="col-3">
						<center><input type="checkbox" id="check_l" name="check_l"></center>
					</div>
				</div>
				<div class="row">
					<div class="col-7">
						<label class="form-label">Nervio refuerso pegado o autoarmable</label>
					</div>
					<div class="col-2">
						<center><label class="form-label">N</label></center>
					</div>
					<div class="col-3">
						<center><input type="checkbox" id="check_n" name="check_n"></center>
					</div>
				</div>
				<div class="row">
					<div class="col-7">
						<label class="form-label">Esquinero</label>
					</div>
					<div class="col-2">
						<center><label class="form-label">Q</label></center>
					</div>
					<div class="col-3">
						<center><input type="checkbox" id="check_q" name="check_q"></center>
					</div>
				</div>
                <div class="row">
					<div class="col-7">
						<label class="form-label">Prepicado y/o corte sobre rayado</label>
					</div>
					<div class="col-2">
						<center><label class="form-label">P</label></center>
					</div>
					<div class="col-3">
						<center><input type="checkbox" id="check_p" name="check_p"></center>
					</div>
				</div>
				<div class="row">
					<div class="col-7">
						<label class="form-label">RRP</label>
					</div>
					<div class="col-2">
						<center><label class="form-label">R</label></center>
					</div>
					<div class="col-3">
						<center><input type="checkbox" id="check_r" name="check_r"></center>
					</div>
				</div>
				<div class="row">
					<div class="col-7">
						<label class="form-label">Troquel adicional</label>
					</div>
					<div class="col-2">
						<center><label class="form-label">T</label></center>
					</div>
					<div class="col-3">
						<center><input type="checkbox" id="check_t" name="check_t"></center>
					</div>
				</div>
				<div class="row">
					<div class="col-7">
						<label class="form-label">Rayados desplazados</label>
					</div>
					<div class="col-2">
						<center><label class="form-label">Y</label></center>
					</div>
					<div class="col-3">
						<center><input type="checkbox" id="check_y" name="check_y"></center>
					</div>
				</div>
				<div class="row">
					<div class="col-7">
						<label class="form-label">Pieza adicional</label>
					</div>
					<div class="col-2">
						<center><label class="form-label">X</label></center>
					</div>
					<div class="col-3">
						<center><input type="checkbox" id="check_x" name="check_x"></center>
					</div>
				</div>
				<div class="row">
					<div class="col-7">
						<label class="form-label">N/A</label>
					</div>
					<div class="col-2">
						<center><label class="form-label">N/A</label></center>
					</div>
					<div class="col-3">
						<center><input type="checkbox" id="check_na" name="check_na" ></center>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<div class="text-center">
				<button class="btn btn-light" data-dismiss="modal" id="button_cerrar_caracteristica">Cerrar</button>
				<button class="btn btn-success" type="button" id="button_aplicar_caracteristica">Aplicar</button>
				</div>
			</div>

		</div>
	</div>
</div>

<input type="hidden" id="role_id" name="role_id" value="{{auth()->user()->role_id}}">
<input type="hidden" id="state_id" name="state_id" value="{{isset($ot->ultimoCambioEstado) ? $ot->ultimoCambioEstado->state_id : ''}}">
<input type="hidden" id="detalle_id" name="detalle_id" value="">
<input type="hidden" id="muestra_id" name="muestra_id" value="">
<input type="hidden" id="validacion_campos" name="validacion_campos" value="{{$validacion_campos}}">
<input type="hidden" id="prueba_required" name="prueba_required" value="">
<input type="hidden" id="largo_mckee_value" name="largo_mckee_value" value="">
<input type="hidden" id="ancho_mckee_value"  name="ancho_mckee_value"value="">
<input type="hidden" id="alto_mckee_value"  name="alto_mckee_value"value="">
<input type="hidden" id="perimetro_mckee_value"  name="perimetro_mckee_value" value="">
<input type="hidden" id="carton_id_mckee_value"  name="carton_id_mckee_value" value="">
<input type="hidden" id="ect_mckee_value"  name="ect_mckee_value" value="">
<input type="hidden" id="espesor_mckee_value" name="espesor_mckee_value" value="">
<input type="hidden" id="bct_lib_mckee_value" name="bct_lib_mckee_value" value="">
<input type="hidden" id="bct_kilos_mckee_value" name="bct_kilos_mckee_value" value="">
<input type="hidden" id="fecha_mckee_value" name="fecha_mckee_value" value="">
<input type="hidden" id="aplicar_mckee_value" name="aplicar_mckee_value" value="">
<input type="hidden" id="fsc_instalation" name="fsc_instalation" value="">
<input type="hidden" id="sec_ope_planta_orig_id" name="sec_ope_planta_orig_id" value="">
<input type="hidden" id="sec_ope_planta_aux_1_id" name="sec_ope_planta_aux_1_id" value="">
<input type="hidden" id="sec_ope_planta_aux_2_id" name="sec_ope_planta_aux_2_id" value="">
<input type="hidden" id="sec_ope_planta_orig_filas" name="sec_ope_planta_orig_filas" value="6">
<input type="hidden" id="sec_ope_planta_aux_1_filas" name="sec_ope_planta_aux_1_filas" value="3">
<input type="hidden" id="sec_ope_planta_aux_2_filas" name="sec_ope_planta_aux_2_filas" value="3">
<input type="hidden" id="cm2_clisse_color_1_value" name="cm2_clisse_color_1_value" value="{{isset($ot->cm2_clisse_color_1) ? $ot->cm2_clisse_color_1 : 0}}">
<input type="hidden" id="cm2_clisse_color_2_value" name="cm2_clisse_color_2_value" value="{{isset($ot->cm2_clisse_color_2) ? $ot->cm2_clisse_color_2 : 0}}">
<input type="hidden" id="cm2_clisse_color_3_value" name="cm2_clisse_color_3_value" value="{{isset($ot->cm2_clisse_color_3) ? $ot->cm2_clisse_color_3 : 0}}">
<input type="hidden" id="cm2_clisse_color_4_value" name="cm2_clisse_color_4_value" value="{{isset($ot->cm2_clisse_color_4) ? $ot->cm2_clisse_color_4 : 0}}">
<input type="hidden" id="cm2_clisse_color_5_value" name="cm2_clisse_color_5_value" value="{{isset($ot->cm2_clisse_color_5) ? $ot->cm2_clisse_color_5 : 0}}">
<input type="hidden" id="cm2_clisse_color_6_value" name="cm2_clisse_color_6_value" value="{{isset($ot->cm2_clisse_color_6) ? $ot->cm2_clisse_color_6 : 0}}">
<input type="hidden" id="cm2_clisse_color_7_value" name="cm2_clisse_color_7_value" value="{{isset($ot->cm2_clisse_color_7) ? $ot->cm2_clisse_color_7 : 0}}">
<input type="hidden" id="total_cm2_clisse_value" name="total_cm2_clisse_value" value="{{isset($ot->total_cm2_clisse) ? $ot->total_cm2_clisse : 0}}">
