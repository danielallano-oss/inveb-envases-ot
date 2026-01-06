<div class="form-row">
	<!-- Solo si es creacion por ingeniero -->
	
	<div id="ot-datos-comerciales" class="col-12 mb-2">
		<div class="card">
			<div class="card-header">
				Datos comerciales
			</div>
			<div class="card-body">
				<div class="row">
					@if($errors->any())
					@endif

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
							{!! armarSelectArrayCreateEditOT($ajustes_area_desarrollo, 'ajuste_area_desarrollo', 'Tipo de Ajuste Area Desarrollo:' , $errors, $ot ,'form-control',true,false) !!}
							{!! armarSelectArrayCreateEditOT([], 'instalacion_cliente', 'Instalación Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
							<!-- Contactos Cliente -->
							<!-- //style="display:none" -->
							{!! armarSelectArrayCreateEditOT([], 'contactos_cliente', 'Contactos Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
						@elseif(Auth()->user()->isSuperAdministrador())
							<!-- Tipo de Solicitud -->
							{!! armarSelectArrayCreateEditOT($tipos_solicitud, 'tipo_solicitud', 'Tipo de solicitud:' , $errors, $ot ,'form-control',true,false) !!}
							{!! armarSelectArrayCreateEditOT($ajustes_area_desarrollo, 'ajuste_area_desarrollo', 'Tipo de Ajuste Area Desarrollo:' , $errors, $ot ,'form-control',true,false) !!}
							<!-- Contactos Cliente -->
							<!-- //style="display:none" -->
							{!! armarSelectArrayCreateEditOT([], 'instalacion_cliente', 'Instalación Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
							{!! armarSelectArrayCreateEditOT([], 'contactos_cliente', 'Contactos Cliente' , $errors, $ot ,'form-control form-element',true,true) !!}
						@else 
							<!-- Tipo de Solicitud -->
							{!! inputReadOnly('Tipo de Solicitud', [1 => "Desarrollo Completo", 4 => "Cotiza sin CAD", 2 => "Cotiza con CAD", 3 => "Muestra con CAD", 5 => "Arte con Material", 6 => "Otras Solicitudes Desarrollo"][$ot->tipo_solicitud]) !!}
							{!! inputReadOnly('Tipo de Ajuste Area Desarrollo', [1 => "Licitación", 2 => "Ficha Técnica",  3 => "Estudio Benchmarking"][$ot->ajuste_area_desarrollo]) !!}
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
						
					</div>
					<div class="col-4">
						<!-- Canal -->
						{!! armarSelectArrayCreateEditOT($canals, 'canal_id', 'Canal' , $errors, $ot ,'form-control',true,false) !!}
					</div>
					<div class="col-4">			
							
						{!! armarSelectArrayCreateEditOT($hierarchies, 'hierarchy_id', 'Jerarquía 1' , $errors, $ot ,'form-control',true,true) !!}
						<!-- Jerarquía 2-->
						{!! armarSelectArrayCreateEditOT($subhierarchies, 'subhierarchy_id', 'Jerarquía 2' , $errors, $ot ,'form-control',true,true) !!}
						<!-- Jerarquía 3-->
						{!! armarSelectArrayCreateEditOT($subsubhierarchies, 'subsubhierarchy_id', 'Jerarquía 3' , $errors, $ot ,'form-control',true,true) !!}

						
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="ot-estudio-benchmarking" class="col-8 mb-2">
		<div class="card">
			<div class="card-header">
				Datos de Estudio Benchmarking
			</div>
			<div class="card-body">
				<div class="form-group form-row">	
					<div class="col-3">
						<!-- Cantidad de Items -->
						{!! armarInputCreateEditOT('cantidad_estudio_bench', 'Cantidad:', 'number',$errors, $ot, 'form-control','min="1"', '') !!}
					</div>	
								
					<div class="col-1">
						<!-- Cantidad de Items -->
						&nbsp;
					</div>
										
					<div class="col-4">
						<!-- Fecha Maxima Entrega -->
						{!! armarInputCreateEditOT('fecha_maxima_entrega_estudio', 'Fecha Maxima Entrega:', 'date',$errors, $ot, 'form-control','min="1"', '') !!}
					</div>	
					<div class="col-4">
						@if($tipo=='edit')
							@if(Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo())
								&nbsp;
							@else
								<div class="" style="display: flex;    justify-content: flex-end;    margin-right: 15px;">
									<a href="#" id="carga-detalles" class="btn btn-light float-right mr-3" style="display:flex;align-items:center;border-color: #28a745;" data-toggle="modal" data-target="#modal-carga-detalles">Carga Archivo Detalles <div class="material-icons md-14 ml-1" data-toggle="tooltip" title="Agregar Detalles Masivos" style="color:#218838;align-items: center;">insert_drive_file
									</div></a>
								</div>
							@endif
						@else
							<div class="" style="display: flex;    justify-content: flex-end;    margin-right: 15px;">
								<a href="#" id="carga-detalles" class="btn btn-light float-right mr-3" style="display:flex;align-items:center;border-color: #28a745;" data-toggle="modal" data-target="#modal-carga-detalles">Carga Archivo Detalles <div class="material-icons md-14 ml-1" data-toggle="tooltip" title="Agregar Detalles Masivos" style="color:#218838;align-items: center;">insert_drive_file
								</div></a>
							</div>
						@endif
					</div>			
				</div>
				<br>
				
				<div class="form-group form-row">
					<div class="col-12">
						<div id="detalles_estudio_benchmarking" class="form-group">
						</div>
					</div>
				</div>
				@if(Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo())
					<br>
					<div class="form-group form-row">
						<div class="col-12">
							<div id="detalle_estudio">
								<div class="form-group form-row" id="div_check_ensayos">
									<label class="col-auto col-form-label">Ensayos Caja</label>
								</div>
								<br>
								<div class="form-group form-row">
									<div class="col-2">		
										<div class="custom-control custom-checkbox mb-1">								
											<input type="checkbox" class="custom-control-input" value="check_estudio_bct" id="check_estudio_bct" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_bct == 1) || (old('check_estudio_bct'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_bct">BCT (lbf)</label>
										</div>
									</div>
									<div class="col-2">
										
										&nbsp;
									
									</div>	
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_ect" id="check_estudio_ect" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_ect == 1) || (old('check_estudio_ect'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_ect">ECT (lb/in)</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>	
									<div class="col-3">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_bct_humedo" id="check_estudio_bct_humedo" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_bct_humedo == 1) || (old('check_estudio_bct_humedo'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_bct_humedo">BCT en Humedo (lbf)</label>
										</div>
									</div>
									</div>
								<br>
								<div class="form-group form-row">
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_flat" id="check_estudio_flat" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_flat == 1) || (old('check_estudio_flat'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_flat">Flat Crush (lb/in)</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>	
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_humedad" id="check_estudio_humedad" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_humedad == 1) || (old('check_estudio_humedad'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_humedad">Humedad (%)</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>
									<div class="col-3">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_porosidad_ext" id="check_estudio_porosidad_ext" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_porosidad_ext == 1) || (old('check_estudio_porosidad_ext'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_porosidad_ext">Porosidad Exterior Gurley</label>
										</div>
									</div>															
								</div>
								<br>
								<div class="form-group form-row">
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_espesor" id="check_estudio_espesor" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_espesor == 1) || (old('check_estudio_espesor'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_espesor">Espesor (mm)</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_cera" id="check_estudio_cera" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_cera == 1) || (old('check_estudio_cera'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_cera">Cera</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>
									<div class="col-3">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_porosidad_int" id="check_estudio_porosidad_int" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_porosidad_int == 1) || (old('check_estudio_porosidad_int'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_porosidad_int">Porosidad Interior Gurley</label>
										</div>
									</div>							
								</div>
								<br>
								<div class="form-group form-row">
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_flexion_fondo" id="check_estudio_flexion_fondo" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_flexion_fondo == 1) || (old('check_estudio_flexion_fondo'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_flexion_fondo">Flexión de Fondo</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>								
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_gramaje" id="check_estudio_gramaje" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_gramaje == 1) || (old('check_estudio_gramaje'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_gramaje">Gramaje (gr/mt2)</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>
									<div class="col-3">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_composicion_papeles" id="check_estudio_composicion_papeles" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_composicion_papeles == 1) || (old('check_estudio_composicion_papeles'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_composicion_papeles">Composición Papeles</label>
										</div>
									</div>							
								</div>
								<br>
								<div class="form-group form-row">
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_cobb_interno" id="check_estudio_cobb_interno" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_cobb_interno == 1) || (old('check_estudio_cobb_interno'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_cobb_interno">Cobb Interno</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_cobb_externo" id="check_estudio_cobb_externo" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_cobb_externo == 1) || (old('check_estudio_cobb_externo'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_cobb_externo">Cobb Externo</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>
									<div class="col-3">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_flexion_4_puntos" id="check_estudio_flexion_4_puntos" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_flexion_4_puntos == 1) || (old('check_estudio_flexion_4_puntos'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_flexion_4_puntos">Flexión 4 Puntos</label>
										</div>
									</div>							
								</div>
								<br>
								<div class="form-group form-row">
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_medidas" id="check_estudio_medidas" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_medidas == 1) || (old('check_estudio_medidas'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_medidas">Medidas</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_impresion" id="check_estudio_impresion" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_impresion == 1) || (old('check_estudio_impresion'))) checked @endif>
											<label class="custom-control-label" for="check_estudio_impresion">Impresión</label>
										</div>
									</div>						
									<div class="col-4">
										&nbsp;
									</div>
								</div>	
							</div>
						</div>
					</div>
				@endif
			</div>			
		</div>
	</div>
	
	<div id="ot-sentido-onda" class="col-4 mb-2" >
		<div class="card">
			<div class="card-header">
				Observaciones
			</div>
			<div class="card-body">
				<div class="form-group form-row">
					<div class="col-12">
						<textarea class="{{$errors->has('observacion') ? 'error' : ''}}" style="resize: none;border-color:#3aaa35" name="observacion" id="observacion" cols="47" rows="6">@if(old('observacion')) {{old('observacion')}} @elseif(isset($ot->observacion) && !old('_token') && $tipo=='edit') {{ $ot->observacion}} @endif </textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<input type="hidden" id="role_id" name="role_id" value="{{auth()->user()->role_id}}">
<input type="hidden" id="detalle_id" name="detalle_id" value="">
<input type="hidden" id="cant_aux" name="cant_aux" value="0">
<input type="hidden" id="detalle_estudio_bench" name="detalle_estudio_bench" @if(isset($ot)) value="{{$ot->detalle_estudio_bench}}" @else value="" @endif>
<input type="hidden" id="tipo" name="tipo" value="{{$tipo}}">
<input type="hidden" id="archivo_estudio" name="archivo_estudio" value="">




