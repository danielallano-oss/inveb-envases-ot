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
						{!! inputReadOnly('Tipo de Solicitud', [1 => "Desarrollo Completo", 2 => "Cotiza con CAD", 3 => "Muestra con CAD", 4 => "Cotiza sin CAD", 5 => "Arte con Material"][$ot->tipo_solicitud]) !!}
						<input type="hidden" id="tipo_solicitud" value="{{$ot->tipo_solicitud}}">
						<!-- Nombre Contacto -->
						{!! inputReadOnly('Nombre Contacto',$ot->nombre_contacto) !!}
						<!-- Email Contacto -->
						{!! inputReadOnly('Email Contacto',$ot->email_contacto) !!}
						<!-- Teléfono Contacto -->
						{!! inputReadOnly('Teléfono Contacto',$ot->telefono_contacto) !!}
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
						{!! inputReadOnly('OC',isset($ot->oc) ? [1 => "Si", 0=>"No"][$ot->oc] : null) !!}
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
						{!! inputReadOnly('MATRIZ',isset($ot->matrices) ? $ot->matrices->material : null) !!}

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
								{!! inputReadOnly('FCT (lb/pulg2)',isset($ot->fct) ? number_format_unlimited_precision(str_replace(',', '.',$ot->fct)) : null) !!}
							</div>
							<div class="col-6">
								<!-- Espesor (mm)  -->
								{!! inputReadOnly('DST (BPI)',isset($ot->dst) ? $ot->dst : null) !!}
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
								{!! inputReadOnly('Cobb Interior (g/m2)',isset($ot->cobb_interior) ? $ot->cobb_interior : null) !!}

							</div>
							<div class="col-6">
								<!-- Cobb Exterior (g/m2)  -->
								{!! inputReadOnly('Cobb Exterior (g/m2)',isset($ot->cobb_exterior) ? $ot->cobb_exterior : null) !!}
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
						<div class="form-group form-row">
							<div class="col-6">
								<!-- Flexion de aleta (%) -->
								{!! inputReadOnly('Porosidad (SEG)',isset($ot->porosidad) ? $ot->porosidad : null) !!}
							</div>
							<div class="col-6">
								<!-- Flexion de aleta (%) -->
								{!! inputReadOnly('Brillo (%)',isset($ot->brillo) ? $ot->brillo : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Flexion de aleta (%) -->
								{!! inputReadOnly('Rigidez 4 Puntos Longitudinal (N/MM)',isset($ot->rigidez_4_ptos_long) ? $ot->rigidez_4_ptos_long : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Peso (g) -->
								{!! inputReadOnly('Rigidez 4 Puntos Transversal (N/MM)',isset($ot->rigidez_4_ptos_transv) ? $ot->rigidez_4_ptos_transv : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Incision Rayado Vertical-->
								{!! inputReadOnly('Angulo de Deslizamiento-Tapa Exterior (°)',isset($ot->angulo_deslizamiento_tapa_exterior) ? $ot->angulo_deslizamiento_tapa_exterior : null) !!}
							</div>
						</div>
						<div class="form-group form-row">
							<div class="col-12">
								<!-- Incision Rayado Vertical-->
								{!! inputReadOnly('Angulo de Deslizamiento-Tapa Interior (°)',isset($ot->angulo_deslizamiento_tapa_interior) ? $ot->angulo_deslizamiento_tapa_interior : null) !!}
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
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Si el tipo de solicitud es desarrollo o arte y cintas = SI -->
	@if(($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 5) && $ot->cinta == 1)
		<div id="ot-distancia-cinta" class="col-12 mb-2">
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
							{!! inputReadOnly('Tipo de Cinta',isset($ot->tipo_cinta) ? [1 => "Corte", 2=>"Resistencia"][$ot->tipo_cinta] : null) !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	@endif

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
						{!! inputReadOnly('Color 1',isset($ot->color_1) ? $ot->color_1->color : null) !!}
						<!--  % Impresión 1-->
						{!! inputReadOnly('% Impresión 1',isset($ot->impresion_1) ? $ot->impresion_1 : null) !!}

						<!--  Color 2-->
						{!! inputReadOnly('Color 2',isset($ot->color_2) ? $ot->color_2->color : null) !!}
						<!--  % Impresión 2-->
						{!! inputReadOnly('% Impresión 2',isset($ot->impresion_2) ? $ot->impresion_2 : null) !!}

						<!--  Color 3-->
						{!! inputReadOnly('Color 3',isset($ot->color_3) ? $ot->color_3->color : null) !!}
						<!--  % Impresión 3-->
						{!! inputReadOnly('% Impresión 3',isset($ot->impresion_3) ? $ot->impresion_3 : null) !!}

					</div>
					<div class="col-3">

						<!--  Color 4-->
						{!! inputReadOnly('Color 4',isset($ot->color_4) ? $ot->color_4->color : null) !!}
						<!--  % Impresión 4-->
						{!! inputReadOnly('% Impresión 4',isset($ot->impresion_4) ? $ot->impresion_1 : null) !!}

						<!--  Color 5-->
						{!! inputReadOnly('Color 5',isset($ot->color_5) ? $ot->color_5->color : null) !!}
						<!--  % Impresión 5-->
						{!! inputReadOnly('% Impresión 5',isset($ot->impresion_5) ? $ot->impresion_5 : null) !!}

						<!--  Color 6-->
						{!! inputReadOnly('Color 6',isset($ot->color_6) ? $ot->color_6->color : null) !!}
						<!--  % Impresión 6-->
						{!! inputReadOnly('% Impresión 6',isset($ot->impresion_6) ? $ot->impresion_6 : null) !!}

					</div>
					<div class="col-3">
						{{--Se Desabilita a solicitud de correccion del Evolutivo 72 (Eliminar Barniz UV y % Impresión B. UV)
        					Utilizando los datos para este campo de los que vengan del input coverage_external_id y percentage_coverage_external
        				<!-- Barniz UV -->
						{!! inputReadOnly('Barniz UV',isset($ot->barniz_uv) ? [1 => "Si", 0=>"No"][$ot->barniz_uv] : null) !!}
						<!-- % Barniz UV -->
						{!! inputReadOnly('% Impresión B. UV',isset($ot->porcentanje_barniz_uv) ? $ot->porcentanje_barniz_uv : null) !!}

						<!-- Color Interno -->
						{!! inputReadOnly('Color Interno',isset($ot->color_interno) ? $ot->color_interno_detalle->color : null) !!}
						<!--  % Impresión Color Interno-->
						{!! inputReadOnly('% Impresión C. I.',isset($ot->impresion_color_interno) ? $ot->impresion_color_interno : null) !!}
						<!-- Indicador Facturación Diseño Gráfico -->--}}
						{!! inputReadOnly('Indicador Facturación D.G.',isset($ot->indicador_facturacion_diseno_grafico) ? $ot->indicador_facturacion_diseno_grafico : null) !!}

						{!! inputReadOnly('Prueba de Color',isset($ot->prueba_color) ? [1 => "Si", 0=>"No"][$ot->prueba_color] : null) !!}

					</div>

				</div>

			</div>
		</div>
	</div>

	<!-- -------- Datos Antiguos -------------------- -->
	<!-- <div class="mb-5"> -->
		<!--  Pegado-->
		<!-- {!! inputReadOnly('Pegado',isset($ot->pegado) ? [1 => "Si", 0=>"No"][$ot->pegado] : null) !!}			 -->
	<!-- </div> -->

	<!--  Cera Exterior-->
	<!-- {!! inputReadOnly('Cera Exterior',isset($ot->cera_exterior) ? [1 => "Si", 0=>"No"][$ot->cera_exterior] : null) !!} -->
	<!-- % Cera Exterior-->
	<!-- {!! inputReadOnly('% Cera Exterior',isset($ot->porcentaje_cera_exterior) ? $ot->porcentaje_cera_exterior : null) !!} -->

	<!--   Cera Interior-->
	<!-- {!! inputReadOnly('Cera Interior',isset($ot->cera_interior) ? [1 => "Si", 0=>"No"][$ot->cera_interior] : null) !!} -->
	<!--  % Cera Interior-->
	<!-- {!! inputReadOnly(' % Cera Interior',isset($ot->porcentaje_cera_interior) ? $ot->porcentaje_cera_interior : null) !!} -->
	<!-- Barniz Interior-->
	<!-- {!! inputReadOnly('Barniz Externo',isset($ot->barniz_interior) ? [1 => "Si", 0=>"No"][$ot->barniz_interior] : null) !!} -->
	<!--  % Barniz Interior-->
	<!-- {!! inputReadOnly('% Barniz Externo',isset($ot->porcentaje_barniz_interior) ? $ot->porcentaje_barniz_interior : null) !!} -->


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
		</div>
	</div>
	<div id="ot-medidas-exteriores" class="col-4 mb-2">
		<div class="card h-100">
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

	<div id="ot-desarrollo" class="col-7 mb-2">
		<div class="card h-100">
			<div class="card-header">Datos para desarrollo</div>
			<div class="card-body">
				<div class="form-row">
					<div class="col-6">
						<!-- Tipo Producto -->
						{!! inputReadOnly('Tipo Producto:',isset($ot->product_type_developing_id) ? $ot->product_type_developing->descripcion : null) !!}

						<!-- Tipo Alimento -->
						{!! inputReadOnly('Tipo Alimento:',isset($ot->food_type_id) ? $ot->food_type->descripcion : null) !!}

						<!-- Uso Previsto -->
						{!! inputReadOnly('Uso Previsto:',isset($ot->expected_use_id) ? $ot->expected_use->descripcion : null) !!}

						<!-- Uso Reciclado -->
						{!! inputReadOnly('Uso Reciclado:',isset($ot->recycled_use_id) ? $ot->recycled_use->descripcion : null) !!}

						<!-- Clase Sustancia a Embalar -->
						{!! inputReadOnly('Clase Sustancia a Embalar:',isset($ot->class_substance_packed_id) ? $ot->class_substance_packed->descripcion : null) !!}

						<!-- Medio de Transporte -->
						{!! inputReadOnly('Medio de Transporte:',isset($ot->transportation_way_id) ? $ot->transportation_way->descripcion : null) !!}

					</div>
					<div class="col-6">
						<!--  Peso que contiene la caja (Kg)-->
						{!! inputReadOnly('Peso que contiene la caja (Kg)',isset($ot->peso_contenido_caja) ? $ot->peso_contenido_caja : null) !!}
						<!--  Autosoportante-->
						{!! inputReadOnly('Autosoportante',isset($ot->autosoportante) ? [1 => "Si", 0=>"No"][$ot->autosoportante] : null) !!}
						<!--  Envase Primario-->
						{!! inputReadOnly('Envase Primario',isset($ot->envase) ? $ot->envase->descripcion : null) !!}

						<!--  Cuantas cajas apilan en altura-->
						{!! inputReadOnly('Cuantas cajas apilan en altura',isset($ot->cajas_altura) ? $ot->cajas_altura : null) !!}

						<!--  Pallet Sobre pallet-->
						{!! inputReadOnly('Pallet Sobre pallet',isset($ot->pallet_sobre_pallet) ? [1 => "Si", 0=>"No"][$ot->pallet_sobre_pallet] : null) !!}

						<!--  Cantidad-->
						{!! inputReadOnly('Cantidad',isset($ot->cantidad) ? $ot->cantidad : null) !!}

						<!-- Mercado Destino -->
						{!! inputReadOnly('Mercado Destino',isset($ot->target_market_id) ? $ot->target_market->descripcion : null)  !!}

					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="ot-sentido-onda" class="col-5 mb-2">
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

<input type="hidden" id="role_id" name="role_id" value="{{auth()->user()->role_id}}">
