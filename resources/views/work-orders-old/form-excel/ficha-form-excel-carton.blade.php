<div class="form-row">
	<div id="ot-tipo-solicitud" class="col-12 mb-2">
		<div class="card">
			<div class="card-header">Datos del Material</div>
			<div class="card-body">
				<div class="row">
					<div class="col-4">
						<!-- Cliente -->
						{!! inputReadOnly('Cliente',$ot->client->nombre) !!}
					</div>
					<div class="col-8">
						<!-- Descripción -->
						{!! inputReadOnly('Descripción Material',isset($ot->material) ? $ot->material->descripcion : null) !!}
					</div>

					<div class="col-4">

						<!-- Material Asignado -->
						{!! inputReadOnly('Material Asignado', isset($ot->material) ? $ot->material->codigo : null) !!}
						<!-- Código Cliente -->
						{!! inputReadOnly('Código Cliente',$ot->client->codigo) !!}
						<!-- Items del Set -->
						{!! inputReadOnly('Items del Set',$ot->items_set) !!}
						<!-- Tipo de Ítem -->
						{!! inputReadOnly('Tipo de Ítem',$ot->tipo_item_id) !!}
					</div>
					<div class="col-4">
						<!-- Veces del Ítem en el Set -->
						{!! inputReadOnly('Veces del Ítem en el Set',$ot->veces_item) !!}
						<!-- Cartón -->
						{!! inputReadOnly('Cartón',$ot->carton ? $ot->carton->codigo : "N/A") !!}
						<!-- CAD -->
						{!! inputReadOnly('CAD',$cad) !!}
						<!-- Estilo -->
						{!! inputReadOnly('Estilo',$ot->style ? $ot->style->glosa: null) !!}

					</div>
					<div class="col-4">
						<!-- Armado -->
						{!! inputReadOnly('Armado',$ot->armado ? 'Si' : 'No' ) !!}
						<div class="form-group form-row">
							<div class="col-6">
								<!-- BCT MIN (LB)  -->
								{!! inputReadOnly('BCT MIN (LB) ',$ot->bct_min_lb ) !!}
							</div>
							<div class="col-6">
								<!-- BCT MIN (KG)  -->
								{!! inputReadOnly('BCT MIN (KG) ',$ot->bct_min_kg ) !!}
							</div>
						</div>
						<!-- Solo el catalogador y desarrollador pueden editar bct min humedo -->
						@if(Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isIngeniero())
						<!-- BCT Min (Humedo) Lb -->
						{!! armarInputCreateEditOT('bct_min', 'BCT Min (Humedo) Lb:', 'number', $errors, $ot, 'form-control', 'min="0"', '') !!}

						@else
						<!-- BCT Min (Humedo) Lb: -->
						{!! inputReadOnly('BCT Min (Humedo) Lb', $ot->bct_min ) !!}

						@endif

						<!-- Cod. Jerarquia SAP -->
						{!! inputReadOnly('Cod. Jerarquia SAP', $ot->subsubhierarchy ? $ot->subsubhierarchy->jerarquia_sap : "N/A" ) !!}

					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 mb-2">
		<div class="card">
			<div class="card-header">Observación</div>
			<div class="card-body">
				<div class="row">
					<div class="col-12">
						<!-- Observación -->
						{!! inputReadOnly('Observación',$ot->observacion) !!}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 mb-2">
		<div class="card">
			<div class="card-header">REFERENCIA MATERIAL</div>
			<div class="card-body">
				<div class="row">
					<div class="col-3">
						<!-- Tipo Referencia -->
						{!! inputReadOnly('Tipo Referencia',isset($ot->reference_type) ? $ot->reference_type_detalle->descripcion : null) !!}
					</div>
					<div class="col-3">
						<!-- Referencia -->
						{!! inputReadOnly('Referencia',($ot->reference_id) ? $ot->material_referencia->codigo : '' ) !!}
					</div>
					<div class="col-3">
						<!-- Bloqueo Referencia -->
						{!! inputReadOnly('Bloqueo Referencia',($ot->bloqueo_referencia == 1) ? 'Si' : (($ot->bloqueo_referencia === 0 )? 'No' :'') ) !!}
					</div>
					<div class="col-3">
						<!-- Indicador Facturación Diseño Estructural-->
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
	</div>
	<div class="col-12 mb-2">
		<div class="card">
			<div class="card-header">Datos del Cartón</div>
			<div class="card-body">
				<div class="row">
					<div class="col-4">
						<!-- Cartón -->
						{!! inputReadOnly('Cartón',$ot->carton ? $ot->carton->codigo : "N/A") !!}
					</div>
					<div class="col-4">
						<!-- Espesor -->
						{!! inputReadOnly('Espesor',$ot->carton ? number_format($ot->carton->espesor ,2,',', '.') : "N/A") !!}
					</div>
					<div class="col-4">
						<!-- Proceso -->
						{!! inputReadOnly('Proceso',isset($ot->proceso) ? $ot->proceso->descripcion : "") !!}
					</div>
					<div class="col-4">
						<!-- Gramaje -->
						{!! inputReadOnly('Gramaje',$ot->carton ? number_format($ot->carton->peso ,0,',', '.') : "N/A") !!}
					</div>
					<div class="col-4">
						<!-- Onda -->
						{!! inputReadOnly('Onda',$ot->carton ? $ot->carton->onda : "N/A") !!}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 mb-2">
		<div class="card">
			<div class="card-header">Datos Material</div>
			<div class="card-body">
				<div class="row medidasSection">
					<div class="col">
						<!-- Largo Interior (mm) -->
						{!! inputReadOnly('Largo Interior (mm)',$ot->interno_largo) !!}

						<!-- Ancho Interior (mm) -->
						{!! inputReadOnly('Ancho Interior (mm)',$ot->interno_ancho) !!}

						<!-- Alto Interior (mm) -->
						{!! inputReadOnly('Alto Interior (mm)',$ot->interno_alto) !!}


						<!-- Largura HC (mm) -->
						{!! inputReadOnly('Largura HC (mm)',$ot->larguraHc) !!}

						<!-- Largura HM (mm) -->
						<div class="form-group form-row">
							<label style="" class="col-5 col-form-label" for="">Largura HM (mm):</label>
							<div class="col-4">
								<input type="text" class="form-control-plaintext" value="{{$ot->largura_hm}}" readonly>
							</div>
							<div class="col-3">
								<input type="number" class="form-control" value="{{$ot->separacion_largura_hm}}" id="separacion_largura_hm" name="separacion_largura_hm" data-toggle="tooltip" title="Separacion" min='0'>
							</div>
						</div>
						<!-- Golpes al Largo (un) -->
						{!! inputReadOnly('Golpes al Largo (un)', $ot->golpes_largo) !!}

					</div>
					<div class="col">

						<!-- Largo Exterior (mm) -->
						{!! inputReadOnly('Largo Exterior (mm)',$ot->externo_largo) !!}

						<!-- Ancho Exterior (mm) -->
						{!! inputReadOnly('Ancho Exterior (mm)',$ot->externo_ancho) !!}

						<!-- Alto Exterior (mm) -->
						{!! inputReadOnly('Alto Exterior (mm)',$ot->externo_alto) !!}

						<!-- Anchura HC (mm) -->
						{!! inputReadOnly('Anchura HC (mm)',$ot->anchuraHc) !!}
						<!-- Anchura HM (mm) -->
						<div class="form-group form-row">
							<label style="" class="col-5 col-form-label" for="">Anchura HM (mm):</label>
							<div class="col-4">
								<input type="text" class="form-control-plaintext" value="{{$ot->anchura_hm}}" readonly>
							</div>
							<div class="col-3">
								<input type="number" class="form-control" value="{{$ot->separacion_anchura_hm}}" id="separacion_anchura_hm" name="separacion_anchura_hm" data-toggle="tooltip" title="Separacion" min='0'>
								<!-- <div class="material-icons md-14" >report</div> -->
							</div>
						</div>

						<!-- Golpes al Ancho (un) -->
						{!! inputReadOnly('Golpes al Ancho (un)', $ot->golpes_ancho) !!}
					</div>
					<div class="col">

						<!-- Rayado C1/R1 (mm) -->
						{!! inputReadOnly('Rayado C1/R1 (mm)', $ot->rayado_c1r1) !!}

						<!-- Rayado R1/R2 (mm) -->
						{!! inputReadOnly('Rayado R1/R2 (mm)', $ot->rayado_r1_r2) !!}

						<!-- Rayado R2/C2 (mm) -->
						{!! inputReadOnly('Rayado R2/C2 (mm)', $ot->rayado_r2_c2) !!}

						<!-- Solo el catalogador puede editar campos -->
						@if(Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador())
							<!-- Tipo Rayado -->
							{!! armarSelectArrayCreateEditOT($rayadoType, 'rayado_type_id', 'Tipo Rayado' , $errors, $ot ,'form-control',true,true) !!}
						@else
							<!-- Tipo Rayado -->
							{!! inputReadOnly('Tipo Rayado', isset($ot->rayado_type_id) ? $ot->rayado->descripcion : null) !!}
						@endif

						<!-- Gramaje (g/m2) -->
						{!! inputReadOnly('Gramaje (g/m2)',isset($ot->gramaje) ? $ot->gramaje : null) !!}

					</div>
					<div class="col">


						<!-- Área HM (m2) -->
						{!! inputReadOnly('Área HM (m2)', number_format_unlimited_precision($ot->areaHm, ",", ".", 6)) !!}

						<!-- Área HC Unitario (m2) -->
						{!! inputReadOnly('Área HC Unitario (m2)', number_format_unlimited_precision($ot->areaHc)) !!}

						<!-- Recorte Característico (m2) -->
						{!! inputReadOnly('REC. CARACT. UNIT (M2)', number_format_unlimited_precision($ot->recorteCaracteristico,",",".",7)) !!}

						<!-- Recorte Adicional (m2) -->
						{!! inputReadOnly('REC. ADIC / Agujero (M2)',$ot->recorte_adicional ? number_format_unlimited_precision($ot->recorte_adicional,",",".",4) : "N/A") !!}

						<!-- Área Producto (m2) -->
						{!! inputReadOnly('Área Producto (m2)', number_format_unlimited_precision($ot->area_producto_calculo)) !!}
						<!-- Peso (g) -->
						{!! inputReadOnly('Peso (g)',isset($ot->peso) ? $ot->peso : null) !!}

					</div>
					<!-- style="flex: 1 0 3%;" -->
					<div class="col">
						<!-- Cobb Interior (g/m2)  -->
						{!! inputReadOnly(' Cobb Interior (g/m2)',isset($ot->cobb_interior) ? $ot->cobb_interior : null) !!}

						<!-- Cobb Exterior (g/m2)  -->
						{!! inputReadOnly(' Cobb Exterior (g/m2)',isset($ot->cobb_exterior) ? $ot->cobb_exterior : null) !!}

						<!-- Flexion de aleta (%) -->
						{!! inputReadOnly('Flexion de aleta (N)',isset($ot->flexion_aleta) ? $ot->flexion_aleta : null) !!}

						<!-- ECT (lb/pulg) -->
						{!! inputReadOnly('ECT MIN (lb/pulg)',isset($ot->ect) ? number_format_unlimited_precision(str_replace(',', '.',$ot->ect)) : null) !!}

						<!-- FCT (lb/pulg2)  -->
						{!! inputReadOnly(' FCT (lb/pulg2)',isset($ot->fct) ? number_format_unlimited_precision(str_replace(',', '.',$ot->fct)) : null) !!}

						<!-- Espesor (mm)  -->
						{!! inputReadOnly('Espesor (mm)',isset($ot->espesor) ? number_format_unlimited_precision(str_replace(',', '.',$ot->espesor)) : null) !!}

					</div>
					<div class="col">

						<!-- Peso Bruto (kg) -->
						{!! inputReadOnly('Peso Bruto (kg)', number_format_unlimited_precision($ot->pesoBruto)) !!}

						<!-- Peso Neto (kg) -->
						{!! inputReadOnly('Peso Neto (kg)', number_format_unlimited_precision($ot->pesoNeto)) !!}

						<!-- Volumen Unitario (cm3) -->
						{!! inputReadOnly('Vol Unit (cm3)', number_format_unlimited_precision($ot->volumenUnitario)) !!}

						<!-- UMA Área (m2) -->
						{!! inputReadOnly('UMA Área (m2)', number_format_unlimited_precision($ot->umaArea)) !!}

						<!-- UMA Peso (kg) -->
						{!! inputReadOnly('UMA Peso (kg)', number_format_unlimited_precision($ot->umaPeso)) !!}

						<!-- Cinta-->
						{{-- {!! inputReadOnly('Cinta',isset($ot->tipo_cinta) ? [1 => "Si", 0=>"No"][$ot->cinta] : null) !!} --}}
						{!! inputReadOnly('Tipo de Cinta',isset($ot->tipo_cinta) ? $ot->tipos_cintas->descripcion : null) !!}
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


	<div class="col-12 mb-2">
		<div class="card">
			<div class="card-header">Color-Cera-Barniz</div>
			<div class="card-body">
				<div class="row">
					<div class="col">
						<!--Número Colores-->
						{!! inputReadOnly('Número Colores',$ot->numero_colores) !!}

						<div style="margin-top:130px">
							<!--Color 1-->
							{!! inputReadOnly('Color 1',isset($ot->color_1) ? $ot->color_1->descripcion : null) !!}
							<!--% Impresión 1 -->
							{!! inputReadOnly('% Impresión 1',$ot->impresion_1) !!}
							<!--Consumo por Cantidad Base 1 -->
							<div class="form-group form-row">
								<label class="col-8 col-form-label" for="">Consumo por Cantidad Base 1 (gr):</label>
								<div class="col-4">
									<input type="text" class="form-control-plaintext" value="{{number_format_unlimited_precision($ot->consumo1)}}" readonly>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<!-- Color 2 -->
						{!! inputReadOnly('Color 2',isset($ot->color_2) ? $ot->color_2->descripcion : null) !!}
						<!--% Impresión 2 -->
						{!! inputReadOnly('% Impresión 2',$ot->impresion_2) !!}
						<!--Consumo por Cantidad Base 2 -->
						<div class="form-group form-row">
							<label class="col-8 col-form-label" for="">Consumo por Cantidad Base 2 (gr):</label>
							<div class="col-4">
								<input type="text" class="form-control-plaintext" value="{{number_format_unlimited_precision($ot->consumo2)}}" readonly>
							</div>
						</div>
						<div class="mt-5">
							<!--Color 3-->
							{!! inputReadOnly('Color 3',isset($ot->color_3) ? $ot->color_3->descripcion : null) !!}
							<!--% Impresión 3 -->
							{!! inputReadOnly('% Impresión 3',$ot->impresion_3) !!}
							<!--Consumo por Cantidad Base 4 -->
							<div class="form-group form-row">
								<label class="col-8 col-form-label" for="">Consumo por Cantidad Base 3 (gr):</label>
								<div class="col-4">
									<input type="text" class="form-control-plaintext" value="{{number_format_unlimited_precision($ot->consumo3)}}" readonly>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<!-- Color 4 -->
						{!! inputReadOnly('Color 4',isset($ot->color_4) ? $ot->color_4->descripcion : null) !!}
						<!--% Impresión 4 -->
						{!! inputReadOnly('% Impresión 4',$ot->impresion_4) !!}
						<!--Consumo por Cantidad Base 4 -->
						<div class="form-group form-row">
							<label class="col-8 col-form-label" for="">Consumo por Cantidad Base 4 (gr):</label>
							<div class="col-4">
								<input type="text" class="form-control-plaintext" value="{{number_format_unlimited_precision($ot->consumo4)}}" readonly>
							</div>
						</div>
						<div class="mt-5">
							<!--Color 5-->
							{!! inputReadOnly('Color 5',isset($ot->color_5) ? $ot->color_5->descripcion : null) !!}
							<!--% Impresión 5 -->
							{!! inputReadOnly('% Impresión 5',$ot->impresion_5) !!}
							<!--Consumo por Cantidad Base 5 -->
							<div class="form-group form-row">
								<label class="col-8 col-form-label" for="">Consumo por Cantidad Base 5 (gr):</label>
								<div class="col-4">
									<input type="text" class="form-control-plaintext" value="{{number_format_unlimited_precision($ot->consumo5)}}" readonly>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<!-- Color 6 -->
						{!! inputReadOnly('Color 6',isset($ot->color_6) ? $ot->color_6->descripcion : null) !!}
						<!--% Impresión 6 -->
						{!! inputReadOnly('% Impresión 6',$ot->impresion_6) !!}
						<!--Consumo por Cantidad Base 6 -->
						<div class="form-group form-row">
							<label class="col-8 col-form-label" for="">Consumo por Cantidad Base 6 (gr):</label>
							<div class="col-4">
								<input type="text" class="form-control-plaintext" value="{{number_format_unlimited_precision($ot->consumo6)}}" readonly>
							</div>
						</div>
						<div class="mt-5">
							<!--Barniz UV-->
							{!! inputReadOnly('Barniz UV',isset($ot->barniz_uv) ? [1 => "Si", 0=>"No"][$ot->barniz_uv] : null) !!}
							<!-- % Barniz UV -->
							{!! inputReadOnly('% Impresión B. UV',$ot->porcentanje_barniz_uv) !!}
							<!--Consumo por Cantidad Barniz UV -->
							<div class="form-group form-row">
								<label class="col-8 col-form-label" for="">Consumo por Cantidad Base Barniz UV (gr):</label>
								<div class="col-4">
									<input type="text" class="form-control-plaintext" value="{{number_format_unlimited_precision($ot->consumoBarnizUV)}}" readonly>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<!-- Color Interno -->
						{!! inputReadOnly('Color Interno',isset($ot->color_interno) ? $ot->color_interno_detalle->descripcion : null) !!}
						<!--% Impresión Color Interno -->
						{!! inputReadOnly('% Impresión Color Interno',$ot->impresion_color_interno) !!}
						<!--Consumo por Cantidad Base Color Interno -->
						<div class="form-group form-row">
							<label class="col-8 col-form-label" for="">Consumo por Cantidad Base Color Interno (gr):</label>
							<div class="col-4">
								<input type="text" class="form-control-plaintext" value="{{number_format_unlimited_precision($ot->consumoColorInterno)}}" readonly>
							</div>
						</div>
						<div class="col mt-5">
							<!-- Indicador Facturación Diseño Gráfico -->
							{!! inputReadOnly('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Indicador Facturación D.G.',$ot->indicador_facturacion_diseno_grafico) !!}
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Datos Antiguos -->
	<!-- <div class="col"> -->
		<!-- Pegado -->
		<!-- {!! inputReadOnly('Pegado',isset($ot->pegado) ? [1 => "Si", 0=>"No"][$ot->pegado] : null) !!} -->
		<!-- Longitud Pegado -->
		<!-- {!! inputReadOnly('Longitud Pegado (mm)',$ot->longitud_pegado) !!} -->
		<!--Consumo por Cantidad Base Pegado -->
		<!-- <div class="form-group form-row">
			<label class="col-8 col-form-label" for="">Consumo por Cantidad Base Pegado (gr):</label>
			<div class="col-4">
				<input type="text" class="form-control-plaintext" value="{{number_format_unlimited_precision($ot->consumoPegado)}}" readonly>
			</div>
		</div>
		<div class="mt-5"> -->
			<!-- Cera Exterior -->
			<!-- {!! inputReadOnly('Cera Exterior',$ot->cera_exterior ? 'Si' : 'No') !!} -->
			<!-- % Cera Exterior -->
			<!-- {!! inputReadOnly('% Cera Exterior',$ot->porcentaje_cera_exterior) !!} -->
			<!--Consumo por Cantidad Base Cera Exterior -->
			<!-- <div class="form-group form-row">
				<label class="col-8 col-form-label" for="">Consumo por Cantidad Base Cera (gr):</label>
				<div class="col-4">
					<input type="text" class="form-control-plaintext" value="{{number_format_unlimited_precision($ot->consumoCeraExterior)}}" readonly>
				</div>
			</div> -->
		<!-- </div>
	</div>
	<div class="col"> -->
		<!-- Cera Interior -->
		<!-- {!! inputReadOnly('Cera Interior',$ot->cera_interior ? 'Si' : 'No') !!} -->
		<!-- % Cera Interior -->
		<!-- {!! inputReadOnly('% Cera Interior',$ot->porcentaje_cera_interior) !!} -->
		<!--Consumo por Cantidad Base Cera Interior -->
		<!-- <div class="form-group form-row">
			<label class="col-8 col-form-label" for="">Consumo por Cantidad Base Cera (gr):</label>
			<div class="col-4">
				<input type="text" class="form-control-plaintext" value="{{number_format_unlimited_precision($ot->consumoCeraInterior)}}" readonly>
			</div>
		</div> -->

		<!-- <div class="mt-5"> -->
			<!-- Barniz Interior -->
			<!-- {!! inputReadOnly('Barniz Externo',$ot->barniz_interior ? 'Si' : 'No') !!} -->
			<!-- % Barniz Interior -->
			<!-- {!! inputReadOnly('% Barniz Externo',$ot->porcentaje_barniz_interior) !!} -->
			<!--Consumo por Cantidad Base Barniz Interior -->
			<!-- <div class="form-group form-row">
				<label class="col-8 col-form-label" for="">Consumo por Cantidad Base Barniz (gr):</label>
				<div class="col-4">
					<input type="text" class="form-control-plaintext" value="{{number_format_unlimited_precision($ot->consumoBarniz)}}" readonly>
				</div>
			</div>
		</div>
	</div> -->


	<div class="col-12 mb-2">

		@include('partials/formulario-excel-catalogador',['ot' => $ot])

	</div>

</div>

<input type="hidden" id="role_id" name="role_id" value="{{auth()->user()->role_id}}">
