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
						<!-- Cartón -->
						{!! inputReadOnly('Cartón',$ot->carton ? $ot->carton->codigo : "N/A") !!}
						<!-- CAD -->
						{!! inputReadOnly('CAD',$cad) !!}
					</div>
					<div class="col-4">
						<!-- Estilo -->
						{!! inputReadOnly('Estilo',$ot->style ? $ot->style->glosa: null) !!}
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
						{!! inputReadOnly('Tipo Referencia',($ot->reference_type == 1) ? 'Si' : 'No' ) !!}
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
						<!-- Indicador Facturación -->
						{!! armarSelectArrayCreateEditOTSeparado([1=>'RRP','E-Commerce','Esquineros','Geometría','Participación nuevo Mercado','Offset','Innovación','Sustentabilidad','Automatización','No Aplica','Ahorro','Impresión'], 'indicador_facturacion', 'Indicador Facturación' , $errors, $ot ,'form-control',true,false) !!}

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
						{!! inputReadOnly('Espesor',$ot->carton ? number_format_unlimited_precision($ot->carton->espesor) : "N/A") !!}
					</div>
					<div class="col-4">
						<!-- Proceso -->
						{!! inputReadOnly('Proceso',$ot->proceso ? $ot->proceso->descripcion : "") !!}
					</div>
					<div class="col-4">
						<!-- Gramaje -->
						{!! inputReadOnly('Gramaje',$ot->carton ? number_format_unlimited_precision($ot->carton->peso) : "N/A") !!}
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
			<div class="card-header">Medidas</div>
			<div class="card-body">
				<div class="row">
					<div class="col-3">
						<!-- Largo Interior (mm) -->
						{!! inputReadOnly('Largo Interior (mm)',$ot->interno_largo) !!}

						<!-- Ancho Interior (mm) -->
						{!! inputReadOnly('Ancho Interior (mm)',$ot->interno_ancho) !!}

						<!-- Largura HM (mm) -->
						{!! inputReadOnly('Largura HM (mm)',$ot->largura_hm) !!}

						<!-- Anchura HM (mm) -->
						{!! inputReadOnly('Anchura HM (mm)',$ot->anchura_hm) !!}
					</div>
					<div class="col-3">
						<!-- Área HM (m2) -->
						{!! inputReadOnly('Área HM (m2)', number_format_unlimited_precision($ot->area_hm)) !!}
						<!-- Área HC Unitario (m2) -->
						{!! inputReadOnly('Área HC Unitario (m2)', number_format_unlimited_precision($ot->areaEsquinero)) !!}

						<!-- Peso Bruto (kg) -->
						{!! inputReadOnly('Peso Bruto (KGxMTL)', number_format_unlimited_precision($ot->pesoEsquinero)) !!}

						<!-- Peso Neto (kg) -->
						{!! inputReadOnly('Peso Neto (KGxMTL)', number_format_unlimited_precision($ot->pesoEsquinero)) !!}
						<!-- Gramaje (g/m2) -->
						{!! inputReadOnly('Gramaje (g/m2)',isset($ot->gramaje) ? $ot->gramaje : null) !!}

					</div>
					<div class="col-3">

						<!-- Volumen Unitario (cm3) -->
						{!! inputReadOnly('Vol Unit (cm3)', number_format_unlimited_precision($ot->volumenUnitario)) !!}

						<!-- UMA Área (m2) -->
						{!! inputReadOnly('UMA Área (m2)', number_format_unlimited_precision($ot->umaArea)) !!}

						<!-- UMA Peso (kg) -->
						{!! inputReadOnly('UMA Peso (kg)', number_format_unlimited_precision($ot->umaPeso)) !!}
						<!-- Peso (g) -->
						{!! inputReadOnly('Peso (g)',isset($ot->peso) ? $ot->peso : null) !!}

						<!-- Espesor (mm)  -->
						{!! inputReadOnly('Espesor (mm)',isset($ot->espesor) ? number_format_unlimited_precision(str_replace(',', '.',$ot->espesor)) : null) !!}

					</div>
					<div class="col-3">
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


					</div>
				</div>
			</div>
		</div>
	</div>


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
							{!! inputReadOnly('Color 1',$ot->color_1 ? $ot->color_1->descripcion : null) !!}

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
						{!! inputReadOnly('Color 2',$ot->color_2 ? $ot->color_2->descripcion : null) !!}
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
							{!! inputReadOnly('Color 3',$ot->color_3 ? $ot->color_3->descripcion : null) !!}

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
						{!! inputReadOnly('Color 4',$ot->color_4 ? $ot->color_4->descripcion : null) !!}
						<!--% Impresión 4 -->
						{!! inputReadOnly('% Impresión 4',$ot->impresion_4) !!}
						<!--Consumo por Cantidad Base 4 -->
						<div class="form-group form-row">
							<label class="col-8 col-form-label" for="">Consumo por Cantidad Base 4 (gr):</label>
							<div class="col-4">
								<input type="text" class="form-control-plaintext" value="{{number_format_unlimited_precision($ot->consumo4)}}" readonly>
							</div>
						</div>
						<?php /* 
						<div class="mt-5">
							<!--Color 5-->
							{!! inputReadOnly('Color 5',$ot->color_5 ? $ot->color_5->descripcion : null) !!}

							<!--% Impresión 5 -->
							{!! inputReadOnly('% Impresión 5',$ot->impresion_5) !!}

							<!--Consumo por Cantidad Base 5 -->
							<div class="form-group form-row">
								<label class="col-8 col-form-label" for="">Consumo por Cantidad Base 5 (gr):</label>
								<div class="col-4">
									<input type="text" class="form-control-plaintext" value="{{$ot->consumo5}}" readonly>
								</div>
							</div>
						</div>
						*/ ?>
					</div>
					<div class="col"></div>
					<div class="col"></div>
				</div>
			</div>
		</div>
	</div>


	<div class="col-12 mb-2">
		@include('partials/formulario-excel-catalogador',['ot' => $ot])
	</div>

</div>

<input type="hidden" id="role_id" name="role_id" value="{{auth()->user()->role_id}}">