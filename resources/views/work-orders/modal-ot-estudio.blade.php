<div class="form-row py-3" id="ficha">
	<div id="ot-tipo-solicitud" class="col-12 mb-2">
		<div class="card">
			<div class="card-header">Datos comerciales</div>
			<div class="card-body">
				<div class="row">
					<div class="col-4">
						<!-- Cliente -->
						{!! inputReadOnly('Cliente',$ot->client->nombre) !!}
					</div>
					<div class="col-4">
						<!-- Descripción -->
						{!! inputReadOnly('Descripción',$ot->descripcion) !!}
					</div>
					<div class="col-4">
						<!-- Código Producto -->
						{!! inputReadOnly('Código Producto',$ot->codigo_producto) !!}
					</div>

					<div class="col-4">
						<!-- Tipo de Solicitud -->
						{!! inputReadOnly('Tipo de Solicitud', [1 => "Desarrollo Completo", 2 => "Cotiza con CAD", 3 => "Muestra con CAD",7 => "OT Proyectos Innovación", 4 => "Cotiza sin CAD", 5 => "Arte con Material", 6 => "Otras Solicitudes Desarrollo"][$ot->tipo_solicitud]) !!}
						{!! inputReadOnly('Tipo de Ajuste Area Desarrollo', [1 => "Licitación", 2 => "Ficha Técnica",  3 => "Estudio Benchmarking"][$ot->ajuste_area_desarrollo]) !!}
						<input type="hidden" id="tipo_solicitud" value="{{$ot->tipo_solicitud}}">
						@if (is_null($ot->instalacion_cliente)) 
							{!! inputReadOnly('Instalacion Cliente','N/A') !!}
						@else
							{!! inputReadOnly('Instalacion Cliente',$ot->installation->nombre) !!}
						@endif
						<!-- Nombre Contacto -->
						{!! inputReadOnly('Nombre Contacto',$ot->nombre_contacto) !!}
						<!-- Email Contacto -->
						{!! inputReadOnly('Email Contacto',$ot->email_contacto) !!}
						<!-- Teléfono Contacto -->
						{!! inputReadOnly('Teléfono Contacto',$ot->telefono_contacto) !!}
					</div>
					<div class="col-4">
						
						<!-- Canal -->
						{!! inputReadOnly('Canal', isset($ot->canal) ? $ot->canal->nombre : null) !!}

						
					</div>
					<div class="col-4">


						<!-- Jerarquia 1 -->
						{!! inputReadOnly('Jerarquia 1',$ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->hierarchy->descripcion : "N/A") !!}
						<!-- Jerarquia 2 -->
						{!! inputReadOnly('Jerarquia 2', $ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->descripcion : "N/A") !!}
						<!-- Jerarquia 3 -->
						{!! inputReadOnly('Jerarquia 3', $ot->subsubhierarchy ? $ot->subsubhierarchy->descripcion : "N/A") !!}
					</div>					
				</div>
			</div>
		</div>
	</div>
	<div id="ot-estudio-benchmarking" class="col-9 mb-2">
		<div class="card">
			<div class="card-header">
				Datos de Estudio Benchmarking
			</div>
			<div class="card-body">
				<div class="form-group form-row">	
					<div class="col-3">
						<!-- Cantidad de Items -->
						{!! inputReadOnly('Cantidad', $ot->cantidad_estudio_bench ? $ot->cantidad_estudio_bench : null) !!}
					</div>	
								
					<div class="col-1">
						<!-- Cantidad de Items -->
						&nbsp;
					</div>
										
					<div class="col-5">
						<!-- Fecha Maxima Entrega -->
						{!! inputReadOnly('Fecha Maxima Entrega', $ot->fecha_maxima_entrega_estudio ? date("d/m/Y", strtotime($ot->fecha_maxima_entrega_estudio)) : null) !!}
					</div>	
					<div class="col-3">
						&nbsp;
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
											<input type="checkbox" class="custom-control-input" value="check_estudio_bct" id="check_estudio_bct" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_bct == 1) || (old('check_estudio_bct'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_bct">BCT (lbf)</label>
										</div>
									</div>
									<div class="col-2">
										
										&nbsp;
									
									</div>	
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_ect" id="check_estudio_ect" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_ect == 1) || (old('check_estudio_ect'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_ect">ECT (lb/in)</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>	
									<div class="col-3">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_bct_humedo" id="check_estudio_bct_humedo" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_bct_humedo == 1) || (old('check_estudio_bct_humedo'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_bct_humedo">BCT en Humedo (lbf)</label>
										</div>
									</div>
									</div>
								<br>
								<div class="form-group form-row">
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_flat" id="check_estudio_flat" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_flat == 1) || (old('check_estudio_flat'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_flat">Flat Crush (lb/in)</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>	
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_humedad" id="check_estudio_humedad" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_humedad == 1) || (old('check_estudio_humedad'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_humedad">Humedad (%)</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>
									<div class="col-3">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_porosidad_ext" id="check_estudio_porosidad_ext" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_porosidad_ext == 1) || (old('check_estudio_porosidad_ext'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_porosidad_ext">Porosidad Exterior Gurley</label>
										</div>
									</div>															
								</div>
								<br>
								<div class="form-group form-row">
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_espesor" id="check_estudio_espesor" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_espesor == 1) || (old('check_estudio_espesor'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_espesor">Espesor (mm)</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_cera" id="check_estudio_cera" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_cera == 1) || (old('check_estudio_cera'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_cera">Cera</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>
									<div class="col-3">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_porosidad_int" id="check_estudio_porosidad_int" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_porosidad_int == 1) || (old('check_estudio_porosidad_int'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_porosidad_int">Porosidad Interior Gurley</label>
										</div>
									</div>							
								</div>
								<br>
								<div class="form-group form-row">
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_flexion_fondo" id="check_estudio_flexion_fondo" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_flexion_fondo == 1) || (old('check_estudio_flexion_fondo'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_flexion_fondo">Flexión de Fondo</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>								
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_gramaje" id="check_estudio_gramaje" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_gramaje == 1) || (old('check_estudio_gramaje'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_gramaje">Gramaje (gr/mt2)</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>
									<div class="col-3">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_composicion_papeles" id="check_estudio_composicion_papeles" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_composicion_papeles == 1) || (old('check_estudio_composicion_papeles'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_composicion_papeles">Composición Papeles</label>
										</div>
									</div>							
								</div>
								<br>
								<div class="form-group form-row">
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_cobb_interno" id="check_estudio_cobb_interno" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_cobb_interno == 1) || (old('check_estudio_cobb_interno'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_cobb_interno">Cobb Interno</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_cobb_externo" id="check_estudio_cobb_externo" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_cobb_externo == 1) || (old('check_estudio_cobb_externo'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_cobb_externo">Cobb Externo</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>
									<div class="col-3">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_flexion_4_puntos" id="check_estudio_flexion_4_puntos" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_flexion_4_puntos == 1) || (old('check_estudio_flexion_4_puntos'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_flexion_4_puntos">Flexión 4 Puntos</label>
										</div>
									</div>							
								</div>
								<br>
								<div class="form-group form-row">
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_medidas" id="check_estudio_medidas" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_medidas == 1) || (old('check_estudio_medidas'))) checked disabled @endif>
											<label class="custom-control-label" for="check_estudio_medidas">Medidas</label>
										</div>
									</div>
									<div class="col-2">
										&nbsp;								
									</div>
									<div class="col-2">
										<div class="custom-control custom-checkbox mb-1">
											<input type="checkbox" class="custom-control-input" value="check_estudio_impresion" id="check_estudio_impresion" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_estudio_impresion == 1) || (old('check_estudio_impresion'))) checked disabled @endif>
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
	
	<div id="ot-sentido-onda" class="col-3 mb-2" >
		<div class="card">
			<div class="card-header">
				Observaciones
			</div>
			<div class="card-body">
				<div class="form-group form-row">
					<div class="col-12">
						<textarea readonly class="{{$errors->has('observacion') ? 'error' : ''}}" style="resize: none;border-color:#3aaa35" name="observacion" id="observacion" cols="30" rows="20">@if(old('observacion')) {{old('observacion')}} @elseif(isset($ot->observacion) && !old('_token') && $tipo=='edit') {{ $ot->observacion}} @endif </textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>

<input type="hidden" id="role_id" name="role_id" value="{{auth()->user()->role_id}}">
<input type="hidden" id="detalle_estudio_bench" name="detalle_estudio_bench" @if(isset($ot)) value="{{$ot->detalle_estudio_bench}}" @else value="" @endif>
<input type="hidden" id="cantidad_estudio_bench" name="cantidad_estudio_bench" @if(isset($ot)) value="{{$ot->cantidad_estudio_bench}}" @else value="0" @endif>