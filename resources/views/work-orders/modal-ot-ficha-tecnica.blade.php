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
	<div id="ot-ficha-tecnica" class="col-9 mb-2">
		<div class="card">
			<div class="card-header">
				Datos de Ficha Tecnica
			</div>
			<div class="card-body">
				<div class="form-group form-row">	
					<div class="col-4">
						<div class="form-group form-row" id="div_check_ficha">
							<label class="col-auto col-form-label">tipo Ficha: &nbsp;&nbsp;</label>
						
					
							<div class="custom-control custom-checkbox mb-1">
								<input type="checkbox" class="custom-control-input" value="check_ficha_simple" id="check_ficha_simple" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_ficha_simple == 1) || (old('check_ficha_simple'))) checked @endif>
								<label class="custom-control-label" for="check_ficha_simple">Simple&nbsp;&nbsp;&nbsp;</label>
							</div>
						
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" value="check_ficha_doble" id="check_ficha_doble" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->check_ficha_doble == 1) || (old('check_ficha_doble'))) checked @endif>
								<label class="custom-control-label" for="check_ficha_doble">Completa</label>
							</div>
						</div>
					</div>
					
					<div class="col-5">
						<!-- Fecha Maxima Entrega -->
						{!! armarInputCreateEditOT('fecha_maxima_entrega_ficha', 'Fecha Maxima Entrega:', 'date',$errors, $ot, 'form-control','min="1"', '') !!}
					</div>
					
					<div class="col-3">
						{!! armarInputCreateEditOT('cantidad_fichas_solicitadas', 'Solicitadas:', 'number',$errors, $ot, 'form-control','min="1"', '') !!}
						
					</div>	
				</div>
				
			</div>			
		</div>
	</div>
	
	<div id="ot-observacion" class="col-3 mb-2" >
		<div class="card">
			<div class="card-header">
				Observaciones
			</div>
			<div class="card-body">
				<div class="form-group form-row">
					<div class="col-12">
						<textarea readonly class="{{$errors->has('observacion') ? 'error' : ''}}" style="resize: none;border-color:#3aaa35" name="observacion" id="observacion" cols="30" rows="10">@if(old('observacion')) {{old('observacion')}} @elseif(isset($ot->observacion) && !old('_token') && $tipo=='edit') {{ $ot->observacion}} @endif </textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>

<input type="hidden" id="role_id" name="role_id" value="{{auth()->user()->role_id}}">
<input type="hidden" id="detalle_estudio_bench" name="detalle_estudio_bench" @if(isset($ot)) value="{{$ot->detalle_estudio_bench}}" @else value="" @endif>
<input type="hidden" id="cantidad_estudio_bench" name="cantidad_estudio_bench" @if(isset($ot)) value="{{$ot->cantidad_estudio_bench}}" @else value="0" @endif>