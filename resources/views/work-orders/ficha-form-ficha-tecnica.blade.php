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
					@if($tipo == "create" )

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
						@if($tipo == "create")
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

	<div id="ot-ficha-tecnica" class="col-8 mb-2">
		<div class="card">
			<div class="card-header">
				Datos de Ficha Técnica
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

					<div class="col-4">
						<!-- Fecha Maxima Entrega -->
						{!! armarInputCreateEditOT('fecha_maxima_entrega_ficha', 'Fecha Maxima Entrega:', 'date',$errors, $ot, 'form-control','min="1"', '') !!}
					</div>

					<div class="col-4">
						{!! armarInputCreateEditOT('cantidad_fichas_solicitadas', 'Solicitadas:', 'number',$errors, $ot, 'form-control','min="1"', '') !!}
						{{--
						{!! armarSelectArrayCreateEditOT([	1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',
															6=>'6',7=>'7',8=>'8',9=>'9',10=>'10',
															11=>'11',12=>'12',13=>'13',14=>'14',15=>'15',
															16=>'16',17=>'17',18=>'18',19=>'19',20=>'20',
															21=>'21',22=>'22',23=>'23',24=>'24',25=>'25'], 'cantidad_fichas_solicitadas', 'Solicitadas:' , $errors, $ot ,'form-control',true,true) !!}
						--}}
					</div>
				</div>
				@if($tipo=='create')
					<div class="form-group form-row">
						<div class="col-12">
							<div id="subida_archivo" class="form-group form-row">
								<label class="col-auto col-form-label text-right">Archivo: </label>
								<input type="file" class="input" id="ficha_tecnica_file" name="ficha_tecnica_file">
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
				Observación
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
<input type="hidden" id="detalle_fichas_solicitadas" name="detalle_fichas_solicitadas" @if(isset($ot)) value="{{$ot->detalle_fichas_solicitadas}}" @else value="" @endif>
<input type="hidden" id="tipo" name="tipo" value="{{$tipo}}">


