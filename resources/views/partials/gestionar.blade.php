<section id="gestion" class="py-4" data-container="gestion">
	@if(!Auth()->user()->isJefeMuestras() && ! Auth()->user()->isJefeDesarrollo()&& ! Auth()->user()->isSuperAdministrador())
		<button class="btn btn-lg btn-primary rounded-pill" data-toggle="collapse" data-target="#gestion-form" id="gestion_actividad">Gestionar actividad</button>
	@endif
    {{-- @php
        var_dump($ot->ajuste_area_desarrollo);
    @endphp --}}
	@if($ot->tipo_solicitud!=6 || ($ot->tipo_solicitud ==6 && $ot->ajuste_area_desarrollo == 1))
		@if((isset(auth()->user()->role->area) && $usuarioAsignado) && (Auth()->user()->isIngeniero() || auth()->user()->isJefeVenta()||auth()->user()->isVendedor() || auth()->user()->isTecnicoMuestras()) || Auth()->user()->isJefeMuestras() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isSuperAdministrador())
			<button class="btn btn-lg btn-primary rounded-pill" data-toggle="collapse" data-target="#muestras-form" id="gestion_muestra">Gestionar Muestras</button>
			@else
				@if( auth()->user()->isTecnicoMuestras()|| Auth()->user()->isJefeMuestras() || Auth()->user()->isSuperAdministrador())
					<button	button class="btn btn-lg btn-primary rounded-pill" data-toggle="collapse" data-target="#muestras-form" id="gestion_muestra">Gestionar Muestras</button>
				@endif
		@endif
	@endif

	<div id="gestion-form" class="collapse">
		<!-- class="collapse"> -->
		<div class="card mt-3" data-component="form">
			<div class="card-body">

				<form id="crear-gestion" method="POST" action="{{ route('crear-gestion', $ot->id) }}" enctype="multipart/form-data">
					@csrf
					@if($pendiente_recepcion_externo)
						<div id="nota" class="form-group form-row">
							<div class="col-12">
								<span style="background: cornsilk;color: chocolate;"><b>NOTA: No se puede cambiar de estado a Visto Bueno Cliente ya que falta registrar recepción de diseñador externo</b></span>
							</div>
						</div>
					@endif
					<div class="form-group form-row">
						<div class="col-4">
							<!-- Tipo de gestion -->
							{!! armarSelectArrayCreateEditOT($managementTypes, 'management_type_id', 'Tipo De Gestion' , $errors, $ot ,'form-control form-element',true,false) !!}

						</div>
						<div class="col-4">
							<!-- Estado -->
							{!! armarSelectArrayCreateEditOT($states, 'state_id', 'Estado' , $errors, $ot ,'form-control form-element',true,false) !!}


						</div>
						<div class="col-1">
							<span data-attribute="titulo" data-toggle="tooltip" title="Informacion de Estado" id="info_state">
								<i class="fa fa-question-circle" aria-hidden="true"></i>
							</span>
						</div>
						<div class="col-3">
							<!-- Area -->
							{!! armarSelectArrayCreateEditOT($workSpaces, 'work_space_id', 'Area' , $errors, $ot ,'form-control form-element',true,false) !!}

						</div>

					</div>
					<!-- Titulo -->
					<!-- { ! ! -->
					<!-- armarInputCreateEditOT('titulo', 'Titulo:', 'text',$errors, $ot, 'form-control', '', '') -->
					<!-- ! !} -->
					<div id="motivo" class="form-group form-row d-none">
						<div class="col-4">
							<!-- Motivo -->
							{!! armarSelectArrayCreateEditOT($motivos, 'motive_id', 'Motivo' , $errors, $ot ,'form-control form-element',true,false) !!}
						</div>
					</div>
					<div id="proveedor" class="form-group form-row d-none">
						<div class="col-4">
							<!-- Motivo -->
							{!! armarSelectArrayCreateEditOT($proveedores, 'proveedor_id', 'Proveedor' , $errors, $ot ,'form-control form-element',true,false) !!}
						</div>
					</div>
					@if(($ot->current_area_id==6 || $ot->current_area_id==2)  && (Auth()->user()->isTecnicoMuestras() || Auth()->user()->isJefeMuestras() || Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo()))
						<div id="muestra" class="form-group form-row d-none">
							<div class="col-4">
								<!-- Motivo -->
								{!! armarSelectArrayCreateEditOT($muestras, 'muestra_consulta_id', 'ID Muestra' , $errors, $ot ,'form-control form-element',true,false) !!}
								<input type="hidden" id="id_muestra_consulta" name="id_muestra_consulta" value="">
							</div>
						</div>
					@endif
					<div class="form-group form-row">
						<label for="observacion" class="col-auto col-form-label text-right">Observación</label>
						<div class="col">
							<textarea class="form-control" name="observacion" id="observacion" cols="30" rows="5"></textarea>
						</div>
					</div>
					<div id="generar_archivo" class="form-group form-row" style="display:none;">
						<div class="form-group form-row">

							{{----}}
							<label  for="click_diseño_pdf" class="col-auto col-form-label text-right">Generar Diseño PDF</label>
							<a style="" id="link_pdf_muestra_envio" target="_blank" href="{{ route('generar_diseño_pdf',['ot'=>$ot->id]) }}">
								<div class="material-icons md-16 ml-1" data-toggle="tooltip" title="Generar Diseño PDF" style="color:#3aaa35;width: 30px;"><i class="fa fa-download"></i></div>
							</a>
							<input type="checkbox" value="" id="click_diseño_pdf" name="click_diseño_pdf" disabled>

						</div>
					</div>
					<div id="subida_archivo" class="form-group form-row" style="display:none;">
						<label class="col-auto col-form-label text-right">Archivos adjuntos</label>
						<input type="file" class="input" id="files" name="files[]" multiple>
					</div>
					<div id="subida_archivo_pdf" class="form-group form-row" style="display:none;">
						@if(auth()->user()->role->area->id == 2)
							<label class="col-auto col-form-label text-right">Archivo Adjunto</label>
							<input type="file" class="input" id="file_pdf" name="file_pdf" accept="application/pdf">
						@endif
					</div>
					<div id="subida_archivo_diseño_pdf" class="form-group form-row" style="display:none;">
						<label class="col-auto col-form-label text-right">Subir archivo Diseño</label>
						<input type="file" class="input" id="files" name="files[]" multiple>
					</div>
					<div id="subida_archivo_boceto_pdf" class="form-group form-row" style="display:none;">
						@if(auth()->user()->role->area->id == 3)
							<label class="col-auto col-form-label text-right">Archivo Adjunto</label>
							<input type="file" class="input" id="file_boceto_pdf" name="file_boceto_pdf" accept="application/pdf">
						@endif
					</div>
					<div class="row mt-3">
						<div class="col-10 ml-auto">
							<button type="submit" class="btn btn-primary px-5" >Guardar Gestión</button>
						</div>
					</div>
					<input type="hidden" id="tipo_solicitud_ot" name="tipo_solicitud_ot" value="{{$ot->tipo_solicitud}}">
				</form>
			</div>
		</div>

	</div>
</section>

<!-- Modal para mostrar los datos del PDF -->

<!-- style="display: block; padding-right: 17px;" style="display: none;" -->


<div class="modal fade" id="modal-datos-pdf">
	<div class="modal-dialog modal-lg " style="width:80%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="title">Datos PDF</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- <div class="alert-warning" style="padding:10px;border-radius: 5px;">
					NOTA: Los campos con asterisco (*) , no se pudieron leer del PDF.
				</div> -->
				<form id="form-datos-pdf" method="POST" action="{{ route('guardar-pdf', $ot->id) }}" class="form-row form-cad-material" enctype="multipart/form-data">
				<input type="hidden" id="otID" name="otID" value="{{$ot->id}}">
				@csrf
					<div class="card-body">
						<div class="row">
							<div class="col-6">
								<!-- OT -->
								<!-- {!! inputReadOnly('OT',$ot->descripcion ? $ot->descripcion : null) !!} -->
								{!! armarInputCreateEditOT('ot_id', 'OT:', 'text',$errors, $ot, 'form-control', '', '') !!}
								<!-- Carton -->
								{!! armarInputCreateEditOT('carton', 'Cartón:', 'text',$errors, $ot, 'form-control', '', '') !!}
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
								<!-- Recorte Adicional / Area Agujero -->
								{!! armarInputCreateEditOT('recorte_adicional', 'Recorte Adicional / Area Agujero (m2):', 'text',$errors, $ot, 'form-control', '', '') !!}
								<div class="form-group form-row">
									<!-- <div style="display:flex;flex-direction:row;align-items:baseline;"> -->
										<!-- <span style="color:red;margin-right: -12px">*</span> -->
										<div class="col-6" >
											<!-- Golpes al largo -->
											{!! armarInputCreateEditOT('golpes_largo', 'Golpes largo:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
										</div>
										<!-- <span style="color:red;margin-right: -12px">*</span> -->
										<div class="col-6">
											<!-- Golpes al ancho -->
											{!! armarInputCreateEditOT('golpes_ancho', 'Golpes ancho:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
										</div>
									<!-- </div> -->
								</div>
							</div>
							<div class="col-6">
								<div class="card-header">Medidas Interiores</div>
								<div class="card-body form-row" style="margin-right: 20px !important;">
									<div class="col-4">
										<!-- Largo (mm) -->
										{!! armarInputCreateEditOT('interno_largo', 'Largo (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
									</div>
									<div class="col-4">
										<!-- Ancho (mm) -->
										{!! armarInputCreateEditOT('interno_ancho', 'Ancho (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
									</div>
									<div class="col-4">
										<!-- Alto (mm) -->
										{!! armarInputCreateEditOT('interno_alto', 'Alto (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

									</div>
								</div>
								<div class="card-header">Medidas Exteriores</div>
								<div class="card-body form-row" style="margin-right: 20px !important;">
									<div class="col-4">
										<!-- Largo (mm) -->
										{!! armarInputCreateEditOT('externo_largo', 'Largo (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
									</div>
									<div class="col-4">
										<!-- Ancho (mm) -->
										{!! armarInputCreateEditOT('externo_ancho', 'Ancho (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}
									</div>
									<div class="col-4">
										<!-- Alto (mm) -->
										{!! armarInputCreateEditOT('externo_alto', 'Alto (mm):', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}

									</div>
								</div>
							</div>
							<div class="col-6">
								<div style="margin-bottom: 10px;color: #7f7f7f;background-color: #fff;font-size: 12px;text-transform: uppercase;font-weight: 700;">
									Terminaciones</div>
								<div class="form-group form-row">
									<!-- <div class="col-12" style="display:flex;flex-direction:row;align-items:baseline;"> -->
										<!-- <span style="color:red;margin-right: -12px">*</span> -->
										<div class="col-12">
											<!-- Proceso -->
											<!-- {!! armarSelectArrayCreateEditOT($procesos, 'process_id', 'Proceso' , $errors, $ot ,'form-control',true,false) !!} -->
											{!! armarInputCreateEditOT('process', 'Proceso:', 'text', $errors, $ot, 'form-control', '', '') !!}


										</div>
									<!-- </div>
									<div class="col-12" style="display:flex;flex-direction:row;align-items:baseline;"> -->
										<!-- <span style="color:red;margin-right: -12px">*</span> -->
										<div class="col-12">
											<!-- Maquila-->
											<!-- {!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'maquila', 'Maquila' , $errors, $ot ,'form-control',true,false) !!}	 -->
											{!! armarInputCreateEditOT('maquila', 'Maquila:', 'text', $errors, $ot, 'form-control', '', '') !!}

										</div>
									<!-- </div> -->
								</div>
							</div>
						</div>
					</div>
					<div class="mt-3 text-right pull-right" style="width: 100%;">
						<a data-dismiss="modal" class="btn btn-light" style="cursor: pointer;">Cancelar</a>
						<button type="submit" id="guardarDatosPDF" class="btn btn-primary">Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-datos-boceto-pdf">
	<div class="modal-dialog modal-lg " style="width:80%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="title">Datos Boceto PDF - OT {{$ot->id}}</div>
                <input type="hidden" id="cant_colores_ot" data-val="{{$ot->numero_colores}}" value="">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- <div class="alert-warning" style="padding:10px;border-radius: 5px;">
					NOTA: Los campos con asterisco (*) , no se pudieron leer del PDF.
				</div> -->
				<div id="loading" style="display:none">
					<div id="modal-loader" class="loader">Loading...</div>
				</div>
                <input type="hidden" id="colores_ot">
                <input type="hidden" id="cant_colores_pdf" value="">

				<div id="form-datos" style="display:none">
					<form id="form-datos-pdf" method="POST" action="{{ route('guardar-boceto-pdf', $ot->id) }}" class="form-row form-cad-material" enctype="multipart/form-data">
						<input type="hidden" id="otID" name="otID" value="{{$ot->id}}">
						<input type="hidden" id="color_1_value" name="color_1_value" value="">
						<input type="hidden" id="color_2_value" name="color_2_value" value="">
						<input type="hidden" id="color_3_value" name="color_3_value" value="">
						<input type="hidden" id="color_4_value" name="color_4_value" value="">
						<input type="hidden" id="color_5_value" name="color_5_value" value="">
						<input type="hidden" id="color_6_value" name="color_6_value" value="">
						<input type="hidden" id="color_7_value" name="color_7_value" value="">

                        <input type="hidden" id="impresion_1_value" name="impresion_1_value" value="">
						<input type="hidden" id="impresion_2_value" name="impresion_2_value" value="">
						<input type="hidden" id="impresion_3_value" name="impresion_3_value" value="">
						<input type="hidden" id="impresion_4_value" name="impresion_4_value" value="">
						<input type="hidden" id="impresion_5_value" name="impresion_5_value" value="">
						<input type="hidden" id="impresion_6_value" name="impresion_6_value" value="">
						<input type="hidden" id="impresion_7_value" name="impresion_7_value" value="">

						<input type="hidden" id="cm2_clisse_color_1_value" name="cm2_clisse_color_1_value" value="">
						<input type="hidden" id="cm2_clisse_color_2_value" name="cm2_clisse_color_2_value" value="">
						<input type="hidden" id="cm2_clisse_color_3_value" name="cm2_clisse_color_3_value" value="">
						<input type="hidden" id="cm2_clisse_color_4_value" name="cm2_clisse_color_4_value" value="">
						<input type="hidden" id="cm2_clisse_color_5_value" name="cm2_clisse_color_5_value" value="">
						<input type="hidden" id="cm2_clisse_color_6_value" name="cm2_clisse_color_6_value" value="">
						<input type="hidden" id="cm2_clisse_color_7_value" name="cm2_clisse_color_7_value" value="">
						<input type="hidden" id="total_cm2_clisse_value" name="total_cm2_clisse_value" value="">
					@csrf
						<div class="card-body">
							<div class="row">
								<div class="col-4">
									<!--  Color 1-->
									{!! armarInputCreateEditOT('color_1', 'Color 1:', 'text',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Color 2-->
									{!! armarInputCreateEditOT('color_2', 'Color 2:', 'text',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Color 3-->
									{!! armarInputCreateEditOT('color_3', 'Color 3:', 'text',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Color 4-->
									{!! armarInputCreateEditOT('color_4', 'Color 4:', 'text',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Color 5-->
									{!! armarInputCreateEditOT('color_5', 'Color 5:', 'text',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Color 6-->
									{!! armarInputCreateEditOT('color_6', 'Color 6:', 'text',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Color 7-->
									{!! armarInputCreateEditOT('color_7', 'Color 7:', 'text',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}

								</div>

                                <div class="col-4">
									<!--  Clisse cm2 1-->
									{!! armarInputCreateEditOT('impresion_1', '% Impresión 1:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Clisse cm2 2-->
									{!! armarInputCreateEditOT('impresion_2', '% Impresión 2:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Clisse cm2 3-->
									{!! armarInputCreateEditOT('impresion_3', '% Impresión 3:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Clisse cm2 4-->
									{!! armarInputCreateEditOT('impresion_4', '% Impresión 4:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Clisse cm2 5-->
									{!! armarInputCreateEditOT('impresion_5', '% Impresión 5:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Clisse cm2 6-->
									{!! armarInputCreateEditOT('impresion_6', '% Impresión 6:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Clisse cm2 7-->
									{!! armarInputCreateEditOT('impresion_7', '% Impresión 7:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Total Clisse cm2 -->
									{{-- {!! armarInputCreateEditOT('total_cm2_clisse', 'Total clisse cm2:', 'number',$errors, '', 'form-control', 'min="0"', '') !!} --}}
								</div>
								<div class="col-4">
									<!--  Clisse cm2 1-->
									{!! armarInputCreateEditOT('cm2_clisse_color_1', 'Clisse cm2 1:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Clisse cm2 2-->
									{!! armarInputCreateEditOT('cm2_clisse_color_2', 'Clisse cm2 2:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Clisse cm2 3-->
									{!! armarInputCreateEditOT('cm2_clisse_color_3', 'Clisse cm2 3:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Clisse cm2 4-->
									{!! armarInputCreateEditOT('cm2_clisse_color_4', 'Clisse cm2 4:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Clisse cm2 5-->
									{!! armarInputCreateEditOT('cm2_clisse_color_5', 'Clisse cm2 5:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Clisse cm2 6-->
									{!! armarInputCreateEditOT('cm2_clisse_color_6', 'Clisse cm2 6:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Clisse cm2 7-->
									{!! armarInputCreateEditOT('cm2_clisse_color_7', 'Clisse cm2 7:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
									<!--  Total Clisse cm2 -->
									{!! armarInputCreateEditOT('total_cm2_clisse', 'Total clisse cm2:', 'number',$errors, '', 'form-control', 'min="0.00" step="0.01"', '') !!}
								</div>
							</div>
						</div>
						<div class="mt-3 text-right pull-right" style="width: 100%;">
							<a data-dismiss="modal" class="btn btn-light" style="cursor: pointer;">Cancelar</a>
							<button type="submit" id="guardarDatosBocetoPDF" class="btn btn-primary">Guardar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Loading  -->
<div id="loading" style="display:none">
  <div id="modal-loader" class="loader">Loading...</div>
</div>
