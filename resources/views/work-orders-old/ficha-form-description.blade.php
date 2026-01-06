<div class="form-row">
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
						@if($type_edit == "description")	<!-- Valida que se pueda editar la descripcion -->
                        	{!! armarInputCreateEditOT('descripcion', 'Descripción:', 'text',$errors, $ot, 'form-control', 'maxlength="40"', '') !!}
						@else
							{!! inputReadOnly('Descripción',$ot->descripcion) !!}
						@endif
					</div>
					<div class="col-4">
						<!-- Código Producto -->
						{!! inputReadOnly('Código Producto',$ot->codigo_producto) !!}
					</div>

					<div class="col-4">
						<!-- Tipo de Solicitud -->
						{!! inputReadOnly('Tipo de Solicitud', [1 => "Desarrollo Completo", 2 => "Cotiza con CAD", 3 => "Muestra con CAD",7 => "OT Proyectos Innovación", 4 => "Cotiza sin CAD", 5 => "Arte con Material", 6 => "Otras Solicitudes Desarrollo"][$ot->tipo_solicitud]) !!}
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
						@if(auth()->user()->role_id == 5 || auth()->user()->role_id == 6 || auth()->user()->role_id == 7 || auth()->user()->role_id == 8 || auth()->user()->role_id == 9 || auth()->user()->role_id == 10 || auth()->user()->role_id == 11 || auth()->user()->role_id == 12)
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
					</div>
					<div class="col-4">
						<div class="form-group row">
							<div class="col-6">
								<!-- Vol Vta. Anual -->
								{!! inputReadOnly('Vol Vta. Anual',number_format_unlimited_precision($ot->volumen_venta_anual)) !!}

							</div>
							<div class="col-6">
								<!-- USD -->
								{!! inputReadOnly('USD',number_format_unlimited_precision($ot->usd)) !!}

							</div>
						</div>
						<!-- Organizacion de Ventas -->
						{!! inputReadOnly('Organizacion de Ventas', isset($ot->org_venta_id) ? [1 => "Nacional", 2 => "Exportación"][$ot->org_venta_id] : null) !!}

						<!-- Canal -->
						{!! inputReadOnly('Canal', isset($ot->canal) ? $ot->canal->nombre : null) !!}

						<!-- Oc -->
						@if($type_edit == "orden_compra")	<!-- Valida que se pueda editar la Orden de Compra -->
							{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'oc', 'OC' , $errors, $ot ,'form-control',true,false) !!}
						@else
							{!! inputReadOnly('OC',isset($ot->oc) ? [1 => "Si", 0=>"No"][$ot->oc] : null) !!}
						@endif

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

	<div id="ot-solicita" class="col-5 mb-2">
		<div class="card h-100">
			<div class="card-header">Solicita</div>
			<div class="card-body">
				<div id="checkbox-card" class="row pt-2" style="display: flex;justify-content: space-evenly;">
					<div class="checkboxCol">
						<div class="custom-control custom-checkbox mb-1">
							<input disabled type="checkbox" class="custom-control-input" value="analisis" id="analisis" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->analisis == 1) || (old('analisis'))) checked @endif>
							<label class="custom-control-label" for="analisis">Análisis</label>
						</div>
						<div class="custom-control custom-checkbox">
							<input disabled type="checkbox" class="custom-control-input" value="plano" id="plano" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->plano == 1) || (old('plano'))) checked @endif>
							<label class="custom-control-label" for="plano">Plano</label>
						</div>
					</div>
					<div class="checkboxCol">
						<div class="custom-control custom-checkbox mb-1">
							<input disabled type="checkbox" class="custom-control-input" value="prueba_industrial" id="prueba_industrial" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->prueba_industrial == 1) || (old('prueba_industrial'))) checked @endif>
							<label class="custom-control-label" for="prueba_industrial">Prueba Industrial</label>
						</div>

						<div class="custom-control custom-checkbox">
							<input disabled type="checkbox" class="custom-control-input" value="datos_cotizar" id="datos_cotizar" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->datos_cotizar == 1) || (old('datos_cotizar'))) checked @endif>
							<label class="custom-control-label" for="datos_cotizar">Datos para Cotizar</label>
						</div>
					</div>
					<div class="checkboxCol">
						<div class="custom-control custom-checkbox mb-1">
							<input disabled type="checkbox" class="custom-control-input" value="boceto" id="boceto" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->boceto == 1) || (old('boceto'))) checked @endif>
							<label class="custom-control-label" for="boceto">Boceto</label>
						</div>
						<div class="custom-control custom-checkbox">
							<input disabled type="checkbox" class="custom-control-input" value="nuevo_material" id="nuevo_material" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->nuevo_material == 1) || (old('nuevo_material'))) checked @endif>
							<label class="custom-control-label" for="nuevo_material">Nuevo Material</label>
						</div>
					</div>
					<div>
						<div class="custom-control custom-checkbox mb-1" style="display: flex;justify-content: space-between;">
							<input disabled type="checkbox" class="custom-control-input" value="muestra" id="muestra" name="checkboxes[]" @if ((!old('_token') && $tipo=='edit' && $ot->muestra == 1) || (old('muestra'))) checked @endif>
							<label class="custom-control-label" for="muestra">Muestra</label>
							<span class="marcas-aprobaciones">
								@if($tipo=='edit' && $ot->tipo_solicitud == 3)
								{!!$ot->present()->iconosAprobacionVenta()!!}
								{!!$ot->present()->iconosAprobacionDesarrollo()!!}
								@endif
							</span>
						</div>
						<div style="width:140px">
							@if($ot->muestra == 1)
							<div style="width:140px;" id="container-numero-muetras">
								<!-- Numero de Muetras -->
								{!! inputReadOnly('N° Muestras',isset($ot->numero_muestras) ? $ot->numero_muestras : null) !!}
							</div>
							@endif
						</div>

					</div>
				</div>
				@if($errors->has('analisis')||$errors->has('plano')||$errors->has('muestra')||$errors->has('datos_cotizar')
				||$errors->has('boceto')||$errors->has('nuevo_material')||$errors->has('prueba_industrial'))
				<div class="error text-center p-3">
					<h6 style="color:red">* Debes seleccionar al menos una opción</h5>
				</div>
				@endif
			</div>
		</div>
	</div>

	<div id="ot-datos-cliente" class="col-7 mb-2">
		<div class="card h-100">
			<div class="card-header">Referencia Material</div>
			<div class="card-body">
                <div class="row">
					<div class="col-6">
						<!-- Tipo Referencia -->
						{!! inputReadOnly('Tipo Ref.',isset($ot->reference_type) ? $ot->reference_type_detalle->descripcion : null) !!}

					</div>
					<div class="col-6">
						<!-- Referencia -->
						{!! inputReadOnly('Referencia',isset($ot->reference_id) ? $ot->material_referencia->codigo : null) !!}

					</div>
					<div class="col-6">
						<!-- Bloqueo Ref. -->
						{!! inputReadOnly('Bloqueo Ref.',isset($ot->bloqueo_referencia) ? [1 => "Si", 0=>"No"][$ot->bloqueo_referencia] : null) !!}

					</div>
					<div class="col-6">
						<!-- Indicador Facturación Diseño Estructural -->
						{!! inputReadOnly('Indicador Facturación D.E.',isset($ot->indicador_facturacion) ? [
							1=>'RRP',
							2=>'E-Commerce',
							3=>'Esquineros',
							4=>'Geometría',
							5=>'Participación nuevo Mercado',
							6=>'',
							7=>'Innovación',
							8=>'Sustentabilidad',
							9=>'Automatización',
							10=>'No Aplica',
							11=>'Ahorro',
							12=>''][$ot->indicador_facturacion] : null) !!}

					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="ot-caracteristicas" class="col-12 mb-2">
		<div class="card h-100">

			<div class="card-header">Características </div>
			<input hidden class="" id="cad_asignado" value="{{ isset($ot->cad_id) ? $ot->cad_id : null}}" style="display:none;"></input>
			<input hidden class="" id="material_asignado" value="{{isset($ot->material_id) ? $ot->material_id : null}}" style="display:none;"></input>
			<input hidden class="" id="ot_id" value="{{isset($ot) ? $ot->id : null}}" style="display:none;"></input>

			<div class="card-body">
                <div class="row">
					<div class="col-4">
						<div class="" id="cad_input_container">
							<!-- Si hay cad_id mostramos cad relacionado de lo contrario si hay un cad mostrarlo -->
							@if(isset($ot->cad_id))
							<!-- CAD -->
							{!! inputReadOnly('CAD',(isset($ot->cad_id)) ? $ot->cad_asignado->cad : null) !!}
							@else
							<!-- CAD -->
							{!! inputReadOnly('CAD',(isset($ot->cad)) ? $ot->cad : null) !!}
							@endif

						</div>

						{!! inputReadOnly('Matriz',isset($ot->matrices) ? $ot->matrices->material : null) !!}

						{!! inputReadOnly('Tipo Matriz',isset($ot->matrices) ? $ot->matrices->tipo_matriz : null) !!}


						<!-- TIPO ITEM -->
						{!! inputReadOnly('TIPO ITEM',isset($ot->productType) ? $ot->productType->descripcion : null) !!}

						<div class="form-group form-row">
							<div class="col-6">
								<!-- Items del set -->
								{!! inputReadOnly('Items del set',isset($ot->items_set) ? $ot->items_set : null) !!}
							</div>
							<div class="col-6">
								<!-- Veces Item -->
								{!! inputReadOnly('Veces Item',isset($ot->veces_item) ? $ot->veces_item : null) !!}
							</div>
						</div>

						<!--  Color Cartón -->
						{!! inputReadOnly(' Color Cartón',isset($ot->carton_color) ? [1=>"Café",2=>"Blanco"][$ot->carton_color] : null) !!}
						<!-- Cartón -->
						{!! inputReadOnly('Cartón',isset($ot->carton) ? $ot->carton->codigo : null) !!}
						<!-- Cinta-->
						{!! inputReadOnly('Cinta',isset($ot->cinta) ? [1 => "Si", 0=>"No"][$ot->cinta] : null) !!}
						<!--   FSC-->
						{!! inputReadOnly('FSC',isset($ot->fsc) ? $ot->fsc_detalle->descripcion : null) !!}
						<!-- <div class="row mt-2">
							<div class="col" style="flex-grow:3">
							</div>
							<div class="col" style="flex-grow:13">
								Observación FSC
								{!! inputReadOnly('Observación FSC',isset($ot->fsc_observacion) ? $ot->fsc_observacion : null) !!}
							</div>
						</div> -->
						{!! inputReadOnly('Certificado Calidad',isset($ot->pallet_qa_id) ? $ot->pallet_qa_id->descripcion : null) !!}

						<!-- PAÍS REFERENCIA -->
						{!! inputReadOnly('País referencia',isset($ot->pais) ? $ot->pais->name : null) !!}

						<!-- PLANTA OBJETIVO -->
						{!! inputReadOnly('Planta Objetivo',isset($ot->planta) ? $ot->planta->nombre : null) !!}

						<!-- RESTRICCIÓN PALLET -->
						{!! inputReadOnly('Restricción Paletizado',isset($ot->restriccion_pallet) ? [1 => "Si", 0=>"No"][$ot->restriccion_pallet] : null) !!}

						<!-- TAMAÑO PALLET -->
						{!! inputReadOnly('Tamaño Pallet',isset($ot->tamano_pallet) ? $ot->tamano_pallet->descripcion : null) !!}

						<!-- ALTURA PALLET -->
						{!! inputReadOnly('Altura Pallet',(isset($ot->altura_pallet)) ? $ot->altura_pallet : null) !!}

						<!-- PERMITE SOBRESALIR CARGA -->
						{!! inputReadOnly('Permite Sobresalir Carga',isset($ot->permite_sobresalir_carga) ? [1 => "Si", 0=>"No"][$ot->permite_sobresalir_carga] : null) !!}

					</div>
					<div class="col-4">

						<!--  Estilo -->
						{!! inputReadOnly(' Estilo',isset($ot->style) ? $ot->style->glosa : null) !!}

						<div class="form-group form-row">
							<div class="col-6">
								<!--  Largura HM -->
								{!! inputReadOnly(' Largura HM',isset($ot->largura_hm) ? $ot->largura_hm : null) !!}

							</div>
							<div class="col-6">
								<!--  Anchura HM -->
								{!! inputReadOnly(' Anchura HM',isset($ot->anchura_hm) ? $ot->anchura_hm : null) !!}
							</div>
						</div>
						<!--  Área producto (m2) -->
						{!! inputReadOnly(' Área producto (m2)',isset($ot->area_producto) ? $ot->area_producto : null) !!}

						<!--  Recorte Adicional / Area Agujero (m2) -->
						{!! inputReadOnly(' Recorte Adicional / Area Agujero (m2)',$ot->recorte_adicional ? number_format_unlimited_precision($ot->recorte_adicional,",",".",4) : null) !!}

						<!-- Liner Externo -->
						<!-- {!! inputReadOnly('Liner Externo',isset($ot->carton) ? $ot->carton->liner_exterior : null) !!} -->

						<!--  Longitud Pegado-->
						{!! inputReadOnly('Longitud Pegado (mm)',isset($ot->longitud_pegado) ? $ot->longitud_pegado : null) !!}

						<!--  Recubrimiento -->
						<!-- {!! inputReadOnly(' Recubrimiento',isset($ot->recubrimiento) ? $ot->recubrimiento_detalle->descripcion : null) !!} -->

						<div class="form-group form-row">
							<div class="col-6">
								<!--  Golpes al largo -->
								{!! inputReadOnly(' Golpes al largo',isset($ot->golpes_largo) ? $ot->golpes_largo : null) !!}
							</div>
							<div class="col-6">
								<!--  Golpes al ancho  -->
								{!! inputReadOnly(' Golpes al ancho ',isset($ot->golpes_ancho) ? $ot->golpes_ancho : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Separación Golpes al Largo -->
								{!! inputReadOnly('Separación Golpes al Largo (mm)',isset($ot->separacion_golpes_largo) ? $ot->separacion_golpes_largo : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Separación Golpes Ancho -->
								{!! inputReadOnly('Separación Golpes al Ancho (mm)',isset($ot->separacion_golpes_ancho) ? $ot->separacion_golpes_ancho : null) !!}
							</div>
						</div>

                        <div class="form-group form-row">
							<div class="col-12">
								<!-- Cuchillas -->
								{!! inputReadOnly('Cuchillas (ml)',isset($ot->cuchillas) ? $ot->cuchillas : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-6">
								<!--  Rayado C1/R1 (mm) -->
								{!! inputReadOnly(' Rayado C1/R1 (mm)',isset($ot->rayado_c1r1) ? $ot->rayado_c1r1 : null) !!}
							</div>
							<div class="col-6">
								<!-- Rayado R1/R2 (mm) -->
								{!! inputReadOnly('Rayado R1/R2 (mm)',isset($ot->rayado_r1_r2) ? $ot->rayado_r1_r2 : null) !!}
							</div>
						</div>
						<div class="form-group form-row ">
							<div class="col-6">
								<!--  Rayado R2/C2 (mm) -->
								{!! inputReadOnly(' Rayado R2/C2 (mm)',isset($ot->rayado_r2_c2) ? $ot->rayado_r2_c2 : null) !!}
							</div>
						</div>
						<div class="form-group form-row ">
							<div class="col-12">
								<!--  Bulto Zunchado -->
								{!! inputReadOnly('Bulto Zunchado',isset($ot->bulto_zunchado) ? [1 => "Si", 0=>"No"][$ot->bulto_zunchado] : null) !!}
							</div>
							<div class="col-12">
								<!--  Formato Etiqueta Pallet -->
								{!! inputReadOnly('Formato Etiqueta Pallet',isset($ot->formato_etiqueta) ? $ot->formato_etiqueta_pallet->descripcion : null) !!}
							</div>
							<div class="col-12">
								<!--  N° Etiquetas por Pallet -->
								{!! inputReadOnly('N° Etiquetas por Pallet',isset($ot->etiquetas_pallet) ? $ot->etiquetas_pallet: null) !!}
							</div>
							<div class="col-12">
								<!--  Termocontraible -->
								{!! inputReadOnly('Termocontraible',isset($ot->termocontraible) ? [1 => "Si", 0=>"No"][$ot->termocontraible]: null) !!}
							</div>
						</div>
					</div>
					<div class="col-4">
						<div class="form-group form-row">
							<div class="col-6">
								<!--  BCT MIN (LB) -->
								{!! inputReadOnly(' BCT MIN (LB)',isset($ot->bct_min_lb) ? $ot->bct_min_lb : null) !!}
							</div>
							<div class="col-6">
								<!--  BCT MIN (KG) -->
								{!! inputReadOnly(' BCT MIN (KG)',isset($ot->bct_min_kg) ? $ot->bct_min_kg : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-6">
								<!-- BCT HUMEDO (LB)-->
								{!! inputReadOnly(' BCT HUMEDO (LB)',isset($ot->bct_humedo_lb) ? $ot->bct_humedo_lb : null) !!}
							</div>
							<div class="col-6">
								<!-- ECT (lb/pulg) -->
								{!! inputReadOnly('ECT MIN (lb/pulg)',isset($ot->ect) ? number_format_unlimited_precision(str_replace(',', '.',$ot->ect)) : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-6">
								<!-- Gramaje (g/m2) -->
								{!! inputReadOnly('Gramaje (g/m2)',isset($ot->gramaje) ? $ot->gramaje : null) !!}
							</div>
							<div class="col-6">
								<!-- MULLEN (LB/PULG2)-->
								{!! inputReadOnly('Mullen (LB/PULG2)',isset($ot->mullen) ? $ot->mullen : null) !!}
							</div>
						</div>
						<div class="form-group form-row ">
							<div class="col-6">
								<!-- FCT (lb/pulg2)  -->
								{!! inputReadOnly(' FCT (lb/pulg2)',isset($ot->fct) ? number_format_unlimited_precision(str_replace(',', '.',$ot->fct)) : null) !!}
							</div>
							<div class="col-6">
								<!-- Espesor (mm)  -->
								{!! inputReadOnly('Dst (BPI)',isset($ot->dst) ? $ot->dst : null) !!}
							</div>
						</div>
						<div class="form-group form-row ">
							<div class="col-6">
								<!-- Cobb Interior (g/m2)  -->
								{!! inputReadOnly('Espesor Placa (mm)',isset($ot->espesor_placa) ? $ot->espesor_placa : null) !!}

							</div>
							<div class="col-6">
								<!-- Cobb Exterior (g/m2)  -->
								{!! inputReadOnly('Espesor Caja (mm)',isset($ot->espesor_caja) ? $ot->espesor_caja : null) !!}
							</div>
						</div>
						<div class="form-group form-row ">
							<div class="col-6">
								<!-- Cobb Interior (g/m2)  -->
								{!! inputReadOnly(' Cobb Interior (g/m2)',isset($ot->cobb_interior) ? $ot->cobb_interior : null) !!}

							</div>
							<div class="col-6">
								<!-- Cobb Exterior (g/m2)  -->
								{!! inputReadOnly(' Cobb Exterior (g/m2)',isset($ot->cobb_exterior) ? $ot->cobb_exterior : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-6">
								<!-- Flexion de aleta (%) -->
								{!! inputReadOnly('Flexion de aleta (N)',isset($ot->flexion_aleta) ? $ot->flexion_aleta : null) !!}
							</div>
							<div class="col-6">
								<!-- Peso (g) -->
								{!! inputReadOnly('Peso Cliente (g)',isset($ot->peso) ? $ot->peso : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Incision Rayado Longitudinal-->
								{!! inputReadOnly('Incisión Rayado Longitudinal (N)',isset($ot->incision_rayado_longitudinal) ? $ot->incision_rayado_longitudinal : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Incision Rayado Vertical-->
								{!! inputReadOnly('Incisión Rayado Transversal (N)',isset($ot->incision_rayado_vertical) ? $ot->incision_rayado_vertical : null) !!}
							</div>
						</div>
						<div class="form-group form-row ">
							<div class="col-6">
								<!-- Cobb Interior (g/m2)  -->
								{!! inputReadOnly('Porosidad (seg)',isset($ot->porosidad) ? $ot->porosidad : null) !!}

							</div>
							<div class="col-6">
								<!-- Cobb Interior (g/m2)  -->
								{!! inputReadOnly('Brillo (%)',isset($ot->brillo) ? $ot->brillo : null) !!}

							</div>
						</div>
						<div class="form-group form-row ">

							<div class="col-12">
								<!-- Cobb Interior (g/m2)  -->
								{!! inputReadOnly('Rigidez 4 puntos longitudinal (N/MM)',isset($ot->rigidez_4_ptos_long) ? $ot->rigidez_4_ptos_long : null) !!}

							</div>
						</div>
						<div class="form-group form-row ">

							<div class="col-12">
								<!-- Cobb Exterior (g/m2)  -->
								{!! inputReadOnly('Rigidez 4 puntos transversal (N/MM)',isset($ot->rigidez_4_ptos_transv) ? $ot->rigidez_4_ptos_transv : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Incision Rayado Vertical-->
								{!! inputReadOnly('Angulo de deslizamiento-Tapa Exterior (°)',isset($ot->angulo_deslizamiento_tapa_exterior) ? $ot->angulo_deslizamiento_tapa_exterior : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Incision Rayado Vertical-->
								{!! inputReadOnly('Angulo de deslizamiento-Tapa Interior (°)',isset($ot->angulo_deslizamiento_tapa_interior) ? $ot->angulo_deslizamiento_tapa_interior : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Incision Rayado Vertical-->
								{!! inputReadOnly('Resistencia al Frote',isset($ot->resistencia_frote) ? $ot->resistencia_frote : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Incision Rayado Vertical-->
								{!! inputReadOnly('Contenido Reciclado (%)',isset($ot->contenido_reciclado) ? $ot->contenido_reciclado : null) !!}
							</div>
						</div>

						@if(auth()->user()->role_id == 5 || auth()->user()->role_id == 6)
							<div class="form-group form-row">
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
							</div>
							<div class="collapse" id="seccion_formula_mckee" >
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
						{!! inputReadOnly('Distancia Corte 1 a Cinta 1 (mm)',isset($ot->distancia_cinta_1) ? $ot->distancia_cinta_1 : null) !!}
						<!-- Distancia Corte 1 a Cinta 2 -->
						{!! inputReadOnly('Distancia Corte 1 a Cinta 2 (mm)',isset($ot->distancia_cinta_2) ? $ot->distancia_cinta_2 : null) !!}
						<!-- Distancia Corte 1 a Cinta 3 -->
						{!! inputReadOnly('Distancia Corte 1 a Cinta 3 (mm)',isset($ot->distancia_cinta_3) ? $ot->distancia_cinta_3 : null) !!}
					</div>
					<div class="col-4">
						<!-- Distancia Corte 1 a Cinta 4 -->
						{!! inputReadOnly('Distancia Corte 1 a Cinta 4 (mm)',isset($ot->distancia_cinta_4) ? $ot->distancia_cinta_4 : null) !!}
						<!-- Distancia Corte 1 a Cinta 5 -->
						{!! inputReadOnly('Distancia Corte 1 a Cinta 5 (mm)',isset($ot->distancia_cinta_5) ? $ot->distancia_cinta_5 : null) !!}
						<!-- Distancia Corte 1 a Cinta 6 -->
						{!! inputReadOnly('Distancia Corte 1 a Cinta 6 (mm)',isset($ot->distancia_cinta_6) ? $ot->distancia_cinta_6 : null) !!}
					</div>
					<div class="col-4">
						<!-- Corte de Liner:-->
						{!! inputReadOnly('Corte de Liner',isset($ot->corte_liner) ? [1 => "SI", 0=>"NO"][$ot->corte_liner] : null) !!}
						<!-- Tipo de Cinta-->
						{{-- {!! inputReadOnly('Tipo de Cinta',isset($ot->tipo_cinta) ? [1 => "Corte", 2=>"Resistencia"][$ot->tipo_cinta] : null) !!} --}}

						{!! inputReadOnly('Tipo de Cinta',isset($ot->tipo_cinta) ? $ot->tipos_cintas->descripcion : null) !!}

						{!! inputReadOnly('Cintas por Caja',isset($ot->cintas_x_caja) ? $ot->cintas_x_caja : null) !!}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="ot-colores" class="col-12 mb-2">
		<div class="card h-100">
			<div class="card-header">Color-Cera-Barniz</div>
			<div class="card-body">
                <div class="row">
					<div class="col-3">
						<!-- Impresión -->
						{!! inputReadOnly('Impresión',isset($ot->impresion) && $ot->impresion != 0 ? [2 => "Flexografía",3 => "Flexografía Alta Gráfica", 4 => "Flexografía Tiro y Retiro",  5 => "Sin Impresión", 6 => "Sin Impresión (Sólo OF)", 7 => "Sin Impresión (Trazabilidad Completa)"][$ot->impresion] : null) !!}

                        {!! inputReadOnly('Trazabilidad',isset($ot->trazabilidad) && $ot->trazabilidad != 0 ? [1 => " Sin Trazabilidad (Solo Placas)",2 => " Trazabilidad Solo OF", 3 => "Trazabilidad Completa"][$ot->trazabilidad] : null) !!}
						<!-- TIPO Diseño -->
						{!! inputReadOnly('Tipo Diseño',isset($ot->design_type_id) ? $ot->design_type->descripcion : null) !!}


						<!-- Complejidad -->
						{!! inputReadOnly('Complejidad',isset($ot->complejidad) ? $ot->complejidad : null) !!}

						<!--  Número Colores-->
						{!! inputReadOnly('Número Colores',isset($ot->numero_colores) ? $ot->numero_colores : null) !!}

						<!-- Recubrimiento Interno -->
						{!! inputReadOnly('Recubrimiento Interno',isset($ot->coverage_internal_id) ? $ot->coverage_internal->descripcion : null) !!}

						<!-- % Recubrimiento Interno -->
						{!! inputReadOnly('% Recubrimiento Interno',isset($ot->percentage_coverage_internal) ? $ot->percentage_coverage_internal : null) !!}

						<!-- Recubrimiento Externo -->
						{!! inputReadOnly('Recubrimiento Externo',isset($ot->coverage_external_id) ? $ot->coverage_external->descripcion : null) !!}

						<!-- % Recubrimiento Externo -->
						{!! inputReadOnly('% Recubrimiento Externo',isset($ot->percentage_coverage_external) ? $ot->percentage_coverage_external : null) !!}

					</div>
					<div class="col-3">

						<!--  Color 1-->
						{!! inputReadOnly('Color 1 (INTERIOR TyR)',isset($ot->color_1) ? $ot->color_1->color : null) !!}
						<!--  % Impresión 1-->
						{!! inputReadOnly('% Impresión 1',isset($ot->impresion_1) ? $ot->impresion_1 : null) !!}

                        <!--  Clisse cm2 1-->
						{!! inputReadOnly('Clisse cm2 1',isset($ot->cm2_clisse_color_1) ? $ot->cm2_clisse_color_1 : null) !!}
						<br>
						<!--  Color 2-->
						{!! inputReadOnly('Color 2',isset($ot->color_2) ? $ot->color_2->color : null) !!}
						<!--  % Impresión 2-->
						{!! inputReadOnly('% Impresión 2',isset($ot->impresion_2) ? $ot->impresion_2 : null) !!}

                        <!--  Clisse cm2 2-->
						{!! inputReadOnly('Clisse cm2 2',isset($ot->cm2_clisse_color_2) ? $ot->cm2_clisse_color_2 : null) !!}
						<br>
						<!--  Color 3-->
						{!! inputReadOnly('Color 3',isset($ot->color_3) ? $ot->color_3->color : null) !!}
						<!--  % Impresión 3-->
						{!! inputReadOnly('% Impresión 3',isset($ot->impresion_3) ? $ot->impresion_3 : null) !!}

                        <!--  Clisse cm2 3-->
						{!! inputReadOnly('Clisse cm2 3',isset($ot->cm2_clisse_color_3) ? $ot->cm2_clisse_color_3 : null) !!}
						<br>
					</div>
					<div class="col-3">

						<!--  Color 4-->
						{!! inputReadOnly('Color 4',isset($ot->color_4) ? $ot->color_4->color : null) !!}
						<!--  % Impresión 4-->
						{!! inputReadOnly('% Impresión 4',isset($ot->impresion_4) ? $ot->impresion_4 : null) !!}

                        <!--  Clisse cm2 4-->
						{!! inputReadOnly('Clisse cm2 4',isset($ot->cm2_clisse_color_4) ? $ot->cm2_clisse_color_4 : null) !!}
						<br>
						<!--  Color 5-->
						{!! inputReadOnly('Color 5',isset($ot->color_5) ? $ot->color_5->color : null) !!}
						<!--  % Impresión 5-->
						{!! inputReadOnly('% Impresión 5',isset($ot->impresion_5) ? $ot->impresion_5 : null) !!}

                        <!--  Clisse cm2 5-->
						{!! inputReadOnly('Clisse cm2 5',isset($ot->cm2_clisse_color_5) ? $ot->cm2_clisse_color_5 : null) !!}
						<br>
						<!--  Color 6-->
						{!! inputReadOnly('Color 6',isset($ot->color_6) ? $ot->color_6->color : null) !!}
						<!--  % Impresión 6-->
						{!! inputReadOnly('% Impresión 6',isset($ot->impresion_6) ? $ot->impresion_6 : null) !!}

                        <!--  Clisse cm2 6-->
						{!! inputReadOnly('Clisse cm2 6',isset($ot->cm2_clisse_color_6) ? $ot->cm2_clisse_color_6 : null) !!}

					</div>
					<div class="col-3">
					<div class="col-3">
						{{--Se Desabilita a solicitud de correccion del Evolutivo 72 (Eliminar Barniz UV y % Impresión B. UV)
        					Utilizando los datos para este campo de los que vengan del input coverage_external_id y percentage_coverage_external
       					<!-- Barniz UV -->
						{!! inputReadOnly('Barniz UV',isset($ot->barniz_uv) ? [1 => "Si", 0=>"No"][$ot->barniz_uv] : null) !!}
						<!-- % Barniz UV -->
						{!! inputReadOnly('% Impresión B. UV',isset($ot->porcentanje_barniz_uv) ? $ot->porcentanje_barniz_uv : null) !!}
						--}}
						<!-- Color Interno -->
						{!! inputReadOnly('Color Interno',isset($ot->color_interno) ? $ot->color_interno_detalle->color : null) !!}
						<!--  % Impresión Color Interno-->
						{!! inputReadOnly('% Impresión C. I.',isset($ot->impresion_color_interno) ? $ot->impresion_color_interno : null) !!}
						<!-- Indicador Facturación Diseño Gráfico -->
						{!! inputReadOnly('Indicador Facturación D.G.',isset($ot->indicador_facturacion_diseno_grafico) ? $ot->indicador_facturacion_diseno_grafico : null) !!}

						{!! inputReadOnly('Prueba de Color',isset($ot->prueba_color) ? [1 => "Si", 0=>"No"][$ot->prueba_color]: null) !!}

						<!-- % Total Clisse Cm2  -->
						{!! armarInputCreateEditOT('total_cm2_clisse', 'Total clisse cm2:', 'number',$errors, $ot, 'form-control', 'min="0"', '') !!}


					</div>
				</div>

			</div>
		</div>
	</div>

	<div id="ot-medidas-interiores" class="col-4 mb-2">
		<div class="card h-100">
			<div class="card-header">Medidas Interiores</div>
			<div class="card-body form-row">
				<div class="col-4">
					<!--  Largo (mm)-->
					{!! inputReadOnly('Largo (mm)',isset($ot->interno_largo) ? number_format_unlimited_precision(str_replace(',', '.',$ot->interno_largo)) : null) !!}

				</div>
				<div class="col-4">
					<!--  Ancho (mm)-->
					{!! inputReadOnly('Ancho (mm)',isset($ot->interno_ancho) ? number_format_unlimited_precision(str_replace(',', '.',$ot->interno_ancho)) : null) !!}

				</div>
				<div class="col-4">
					<!--  Alto (mm)-->
					{!! inputReadOnly('Alto (mm)',isset($ot->interno_alto) ? number_format_unlimited_precision(str_replace(',', '.',$ot->interno_alto)) : null) !!}

				</div>
			</div>
			<div class="card-header">Medidas Exteriores</div>
			<div class="card-body form-row">

				<div class="col-4">
					<!--  Largo (mm)-->
					{!! inputReadOnly('Largo (mm)',isset($ot->externo_largo) ? number_format_unlimited_precision(str_replace(',', '.',$ot->externo_largo)) : null) !!}
				</div>
				<div class="col-4">
					<!--  Ancho (mm)-->
					{!! inputReadOnly('Ancho (mm)',isset($ot->externo_ancho) ? number_format_unlimited_precision(str_replace(',', '.',$ot->externo_ancho)) : null) !!}

				</div>
				<div class="col-4">
					<!--  Alto (mm)-->
					{!! inputReadOnly('Alto (mm)',isset($ot->externo_alto) ? number_format_unlimited_precision(str_replace(',', '.',$ot->externo_alto)) : null) !!}

				</div>
			</div>
		</div>
	</div>
	{{-- <div id="ot-medidas-exteriores" class="col-4 mb-2">
		<div class="card h-100">
			<div class="card-header">Medidas Exteriores</div>
			<div class="card-body form-row">

				<div class="col-4">
					{!! inputReadOnly('Largo (mm)',isset($ot->externo_largo) ? number_format_unlimited_precision(str_replace(',', '.',$ot->externo_largo)) : null) !!}
				</div>
				<div class="col-4">
					{!! inputReadOnly('Ancho (mm)',isset($ot->externo_ancho) ? number_format_unlimited_precision(str_replace(',', '.',$ot->externo_ancho)) : null) !!}

				</div>
				<div class="col-4">
					{!! inputReadOnly('Alto (mm)',isset($ot->externo_alto) ? number_format_unlimited_precision(str_replace(',', '.',$ot->externo_alto)) : null) !!}

				</div>
			</div>
		</div>
	</div> --}}

	<div id="ot-secuencias-operacionales" class="col-4 mb-2">
		<div class="card h-100">
		<div class="card-header">Secuencias Operacionales</div>
			<div class="card-body form-row">

				<div class="col-12">
					{!! inputReadOnly('Planta Original',isset($ot->so_planta_original) ? $ot->secuencia_principal->descripcion : null) !!}

				</div>
				<div class="col-12">
					{!! inputReadOnly('Planta Alt. 1',isset($ot->so_planta_alt1) ?  $ot->secuencia_alt1->descripcion : null) !!}


				</div>
				<div class="col-12">
					{!! inputReadOnly('Planta Alt. 2',isset($ot->so_planta_alt2) ? $ot->secuencia_alt2->descripcion : null) !!}


				</div>
			</div>
		</div>
	</div>

	<div id="ot-terminaciones" class="col-4 mb-2">
		<div class="card h-100">
			<div class="card-header">Terminaciones</div>
			<div class="card-body form-row">
				<div class="col-4">
					<!--  Proceso-->
					{!! inputReadOnly('Proceso',isset($ot->proceso) ? $ot->proceso->descripcion : null) !!}
				</div>
				<div class="col-4">
					<!--  Pegado-->
					{!! inputReadOnly('Tipo Pegado',isset($ot->pegado_terminacion) ? [1 => "Si", 0=>"No Aplica",2=>"Pegado Interno",3=>"Pegado Externo",4=>"Pegado 3 Puntos",5=>"Pegado 4 Puntos"][$ot->pegado_terminacion] : null) !!}
				</div>
				<div class="col-4">
					<!--  Armado-->
					{!! inputReadOnly('Armado',isset($ot->armado) ? $ot->armado->descripcion : null) !!}
				</div>
				<div class="col-12 mt-2">
					<!-- Sentido Arm. -->
					{!! inputReadOnly('Sentido de Armado &nbsp;',isset($ot->sentido_armado) ? [1 => "No aplica", 2 => "Ancho a la Derecha", 3 => "Ancho a la Izquierda", 4 => "Largo a la Izquierda", 5 => "Largo a la Derecha"][$ot->sentido_armado] : null) !!}

				</div>
				<div class="col-12 mt-2">
					<!-- Maquila-->
					{!! inputReadOnly('Maquila',isset($ot->maquila) ? [1 => "Si", 0=>"No"][$ot->maquila] : null) !!}
				</div>
				<div class="col-12 mt-2">
					<!-- Servicios Maquila-->
					{!! inputReadOnly('Servicios Maquila',isset($ot->maquila_servicio_id) ? $ot->maquila_detalle->servicio : null) !!}
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
	<!-- <div id="ot-sentido-onda" class="col-4 mb-2">
		<div class="card h-100">
			<div class="card-header">Impresión</div>
			<div class="card-body">
			</div>
		</div>
	</div> -->
	<div id="ot-material" class="col-12 mb-2">
		<div class="card h-100">
			<div class="card-header">Material Asignado</div>
			<div class="card-body">
				<div class="form-group form-row ">
					<div class="col-4">
						<!-- Material Asignado -->
						{!! inputReadOnly('Material Asignado', isset($ot->material) ? $ot->material->codigo : null) !!}
					</div>
					<div class="col-8">
						<!-- Descripción -->
						{!! inputReadOnly('Descripción', isset($ot->material) ? $ot->material->descripcion : null) !!}
					</div>
				</div>
			</div>
		</div>
	</div>


	<div id="ot-desarrollo" class="col-4 mb-2">
		<div class="card h-100">
			<div class="card-header">Datos para desarrollo</div>
			<div class="card-body">

				<!--  Peso que contiene la caja (Kg)-->
				{!! inputReadOnly('Peso que contiene la caja (Kg)',isset($ot->peso_contenido_caja) ? $ot->peso_contenido_caja : null) !!}
				<!--  Autosoportante-->
				{!! inputReadOnly('Autosoportante',isset($ot->autosoportante) ? [1 => "Si", 0=>"No"][$ot->autosoportante] : null) !!}
				<!--  Envase Primario-->
				{!! inputReadOnly('Envase Primario',isset($ot->envase) ? $ot->envase->descripcion : null) !!}
				<div class="form-row">
					<div class="col-12">
						<!--  Cuantas cajas apilan en altura-->
						{!! inputReadOnly('Cuantas cajas apilan en altura',isset($ot->cajas_altura) ? $ot->cajas_altura : null) !!}
					</div>
					<div class="col-8">
						<!--  Pallet Sobre pallet-->
						{!! inputReadOnly('Pallet Sobre pallet',isset($ot->pallet_sobre_pallet) ? [1 => "Si", 0=>"No"][$ot->pallet_sobre_pallet] : null) !!}

					</div>
					<div class="col-4">
						<!--  Cantidad-->
						{!! inputReadOnly('Cantidad',isset($ot->cantidad) ? $ot->cantidad : null) !!}

					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="ot-sentido-onda" class="col-8 mb-2">
		<div class="card h-100">
			<div class="card-header">Observación del trabajo a realizar</div>
			<div class="card-body">
				<div class="form-group form-row">
					<div class="col">
						<textarea readonly class="{{$errors->has('observacion') ? 'error' : ''}}" style="width:100%;resize: none;border-color:#3aaa35; background-color:rgb(248, 248, 248)" name="observacion" id="observacion" rows="10">@if(old('observacion')) {{old('observacion')}} @elseif(isset($ot->observacion) && !old('_token') && $tipo=='edit') {{ $ot->observacion}} @endif </textarea>
					</div>
				</div>
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

<input type="hidden" id="type_edit" name="type_edit" value="{{$type_edit}}">
<input type="hidden" id="role_id" name="role_id" value="{{auth()->user()->role_id}}">
<input type="hidden" id="detalle_id" name="detalle_id" value="">
<input type="hidden" id="muestra_id" name="muestra_id" value="">
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
