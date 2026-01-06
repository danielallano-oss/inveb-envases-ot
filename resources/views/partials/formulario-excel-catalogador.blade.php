<div class="card">
	<div class="card-header">Tarjetas-Etiquetas</div>
	<div class="card-body">
		<div class="row">
			<div class="col-4">
				<!-- Solo el catalogador puede editar campos -->
				@if(Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isSuperAdministrador())
					<!-- Tipo de Pallet -->
					{!! armarSelectArrayCreateEditOT($palletTypes, 'pallet_type_id', 'Tipo de Pallet' , $errors, $ot ,'form-control',true,true) !!}
					<!-- Tratamiento de Pallet -->
					{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'pallet_treatment', 'Tratamiento de Pallet' , $errors, $ot ,'form-control',true,true) !!}
					<!-- N Cajas por Pallet -->
					{!! armarInputCreateEditOT('cajas_por_pallet', 'N° Cajas Por Pallet:', 'number', $errors, $ot, 'form-control', 'min="0"', '') !!}
					<!-- N Placas Por Pallet -->
					{!! armarInputCreateEditOT('placas_por_pallet', 'N° Placas Por Pallet:', 'number', $errors, $ot, 'form-control', 'min="0"', '') !!}
					<!-- Patron Carga Pallet -->
					{!! armarSelectArrayCreateEditOT($palletPatron, 'pallet_patron_id', 'Patron Carga Pallet' , $errors, $ot ,'form-control',true,true) !!}

				@else
					<!-- Tipo de Pallet -->
					{!! inputReadOnly('Tipo de Pallet',isset($ot->tipo_pallet) ? $ot->tipo_pallet->descripcion : null) !!}
					<!-- Tratamiento de Pallet -->
					{!! inputReadOnly('Tratamiento de Pallet',isset($ot->pallet_treatment) ? [1 => "Si", 0=>"No"][$ot->pallet_treatment] : null) !!}
					<!-- N° Cajas Por Pallet -->
					{!! inputReadOnly('N° Cajas Por Pallet',$ot->cajas_por_pallet ? $ot->cajas_por_pallet : null) !!}
					<!-- N° Placas Por Pallet -->
					{!! inputReadOnly('N° Placas Por Pallet',$ot->placas_por_pallet ? $ot->placas_por_pallet : null) !!}
					<!-- Patron Carga Pallet -->
					{!! inputReadOnly('Patron Carga Pallet', isset($ot->patron_pallet) ? $ot->patron_pallet->descripcion : null) !!}

				@endif

			</div>
			<div class="col-4">

				<!-- Solo el catalogador puede editar campos -->
				@if(Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isSuperAdministrador())
					<!-- Patron Zuncho Pallet -->
					{!! armarInputCreateEditOT('patron_zuncho', 'Patron Zuncho Pallet:', 'number', $errors, $ot, 'form-control', 'min="0"', '') !!}
					<!-- Proteccion Pallet -->
					{!! armarSelectArrayCreateEditOT($palletProtection, 'pallet_protection_id', 'Proteccion Pallet' , $errors, $ot ,'form-control',true,true) !!}
					<!-- Patron Zuncho Bulto -->
					{!! armarSelectArrayCreateEditOT([1=>"2x0",2=>"2x1",3=>"2x2"], 'patron_zuncho_bulto', 'Patron Zuncho Bulto' , $errors, $ot ,'form-control',true,true) !!}
					<!-- N Cajas por Paquete -->
					{!! armarSelectArrayCreateEditOT($cajasPorPaquete, 'pallet_box_quantity_id', 'N° Cajas por Paquete' , $errors, $ot ,'form-control',true,true) !!}
					<!-- Patron Zuncho Paquete -->
					{!! armarInputCreateEditOT('patron_zuncho_paquete', 'Patron Zuncho Paquete:', 'number', $errors, $ot, 'form-control', 'min="0"', '') !!}
				@else				
					<!-- Patron Zuncho Pallet -->
					{!! inputReadOnly('Patron Zuncho Pallet',$ot->patron_zuncho ? $ot->patron_zuncho : null) !!}
					<!-- Proteccion Pallet -->
					{!! inputReadOnly('Proteccion Pallet',isset($ot->proteccion_pallet) ? $ot->proteccion_pallet->descripcion : null) !!}
					<!-- Patron Zuncho Bulto -->
					{!! inputReadOnly('Patron Zuncho Bulto',$ot->patron_zuncho_bulto ? [1=>"2x0",2=>"2x1",3=>"2x2"][$ot->patron_zuncho_bulto] : null) !!}
					<!-- N° Cajas por Paquete -->
					{!! inputReadOnly('N° Cajas por Paquete', isset($ot->cajas_por_paquete) ? $ot->cajas_por_paquete->descripcion : null) !!}
					<!-- Patron Zuncho Paquete -->
					{!! inputReadOnly('Patron Zuncho Paquete',$ot->patron_zuncho_paquete ? $ot->patron_zuncho_paquete : null) !!}
				@endif

			</div>
			<div class="col-4">

				<!-- Solo el catalogador puede editar campos -->
				@if(Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isSuperAdministrador())
					<!-- Termocontraible -->
					{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'termocontraible', 'Termocontraible' , $errors, $ot ,'form-control',true,true) !!}
					<!-- N Paquetes por Unidad -->
					{!! armarInputCreateEditOT('paquetes_por_unitizado', 'N° Paquetes por Unidad:', 'number', $errors, $ot, 'form-control', 'min="0"', '') !!}
					<!-- Proteccion -->
					{!! armarSelectArrayCreateEditOT($protectionType, 'protection_type_id', 'Protección' , $errors, $ot ,'form-control',true,true) !!}
				@else
					<!-- Termocontraible -->			
					{!! inputReadOnly('Termocontraible',($ot->termocontraible == 1) ? 'Si' : (($ot->termocontraible === 0 )? 'No' :'') ) !!}
					<!-- N° Paquetes por Unidad -->
					{!! inputReadOnly('N° Paquetes por Unidad',$ot->paquetes_por_unitizado ? $ot->paquetes_por_unitizado : null) !!}
					<!-- Proteccion -->
					{!! inputReadOnly('Protección', isset($ot->protection_type_id) ? $ot->protection->descripcion : null) !!}
				@endif
			</div>
		</div>
		<div class="col-10 offset-1">
			<hr>
		</div>
		<div class="row">
			<div class="col-4">
				<!-- Solo el catalogador puede editar campos -->
				@if(Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isSuperAdministrador())
				<!-- N Unitizados por Pallet -->
				{!! armarInputCreateEditOT('unitizado_por_pallet', 'N° Unitizados por Pallet:', 'number', $errors, $ot, 'form-control', 'min="0"', '') !!}
				<!-- Tipo Formato Etiqueta Pallet -->
				{!! armarSelectArrayCreateEditOT($palletTagFormat, 'formato_etiqueta', 'Tipo Formato Etiqueta Pallet' , $errors, $ot ,'form-control',true,true) !!}

				<!-- N Etiquetas Pallet -->
				{!! armarSelectArrayCreateEditOT([0,1,2,3,4], 'numero_etiquetas', 'N° Etiquetas Pallet' , $errors, $ot ,'form-control',true,true) !!}
				@else
				<!-- N° Unitizados por Pallet -->
				{!! inputReadOnly('N° Unitizados por Pallet', $ot->unitizado_por_pallet ? $ot->unitizado_por_pallet : null) !!}

				<!-- Tipo Formato Etiqueta Pallet -->
				{!! inputReadOnly('Tipo Formato Etiqueta Pallet',isset($ot->formato_etiqueta) ? $ot->formato_etiqueta_pallet->descripcion : null) !!}
				<!-- N° Etiquetas Pallet -->
				{!! inputReadOnly('N° Etiquetas Pallet',$ot->numero_etiquetas ? [0,1,2,3,4][$ot->numero_etiquetas] : null) !!}

				@endif

			</div>
			<div class="col-4">
				<!-- Solo el catalogador puede editar campos -->
				@if(Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isSuperAdministrador())
				<!-- Certificado de Calidad -->
				{!! armarSelectArrayCreateEditOT($palletQa, 'pallet_qa_id', 'Certificado de Calidad' , $errors, $ot ,'form-control',true,true) !!}

				<!-- BCT MIN (LB)  -->
				{!! inputReadOnly('BCT MIN (LB) ',$ot->bct_min_lb ) !!}
				<!-- BCT MIN (KG)  -->
				{!! inputReadOnly('BCT MIN (KG) ',$ot->bct_min_kg ) !!}
				@else
				<!-- Certificado de Calidad -->
				{!! inputReadOnly('Certificado de Calidad', isset($ot->qa) ? $ot->qa->descripcion : null) !!}
				<!-- BCT MIN (LB)  -->
				{!! inputReadOnly('BCT MIN (LB) ',$ot->bct_min_lb ) !!}
				<!-- BCT MIN (KG)  -->
				{!! inputReadOnly('BCT MIN (KG) ',$ot->bct_min_kg ) !!}
				@endif

			</div>
			<div class="col-4">
				<!-- Solo el catalogador puede editar campos -->
				@if(Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isSuperAdministrador())
				<!-- Tipo de Camión -->
				{!! armarInputCreateEditOT('tipo_camion', 'Tipo de Camión:', 'text', $errors, $ot, 'form-control', '', '') !!}

				<!-- Restricciones Especiales -->
				{!! armarInputCreateEditOT('restriccion_especial', 'Restricciones Especiales:', 'text', $errors, $ot, 'form-control', '', '') !!}

				<!-- Horario de Recepcion -->
				{!! armarInputCreateEditOT('horario_recepcion', 'Horario de Recepcion:', 'text', $errors, $ot, 'form-control', '', '') !!}
				@else
				<!-- Tipo de Camión -->
				{!! inputReadOnly('Tipo de Camión', $ot->tipo_camion ? $ot->tipo_camion : null) !!}
				<!-- Restricciones Especiales -->
				{!! inputReadOnly('Restricciones Especiales',$ot->restriccion_especial ? $ot->restriccion_especial : null) !!}
				<!-- Horario de Recepcion -->
				{!! inputReadOnly('Horario de Recepcion',$ot->horario_recepcion ? $ot->horario_recepcion : null) !!}

				@endif
			</div>
		</div>

		<div class="col-10 offset-1">
			<hr>
		</div>
		<div class="row">
			<div class="col-4">
				<!-- Solo el catalogador puede editar campos -->
				@if(Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isSuperAdministrador())
				<!-- Código Producto Cliente -->
				{!! armarInputCreateEditOT('codigo_producto_cliente', 'Código Producto Cliente:', 'text', $errors, $ot, 'form-control', '', '') !!}
				<!-- Para uso de programa Z -->
				{!! armarInputCreateEditOT('uso_programa_z', 'Para uso de programa Z:', 'text', $errors, $ot, 'form-control', '', '') !!}
				@else
				<!-- Código Producto Cliente -->
				{!! inputReadOnly('Código Producto Cliente', $ot->codigo_producto_cliente ? $ot->codigo_producto_cliente : null) !!}
				<!-- Para uso de programa Z -->
				{!! inputReadOnly('Para uso de programa Z',$ot->uso_programa_z ? $ot->uso_programa_z : null) !!}

				@endif
				<!-- Tipo de Tabique -->
				{{--
				{!! inputReadOnly('Tipo de Tabique', $ot->tipo_tabique) !!}

				<!-- Impresion sobre Rayado -->
				{!! inputReadOnly('Impresion sobre Rayado', $ot->impresion_sobre_rayado) !!}
				--}}
				<!-- Solo el catalogador puede editar campos -->
				{{--@if(Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isSuperAdministrador())
				<!-- Caracteristicas Adicionales -->
				{!! armarSelectArrayCreateEditOT($CharacteristicsType, 'additional_characteristics_type_id', 'Caracteristicas Adicionales' , $errors, $ot ,'form-control',true,true) !!}
				@else
				<!-- Caracteristicas Adicionales -->
				{!! inputReadOnly('Caracteristicas Adicionales', isset($ot->additional_characteristics_type_id) ? $ot->characteristics->descripcion : null) !!}
				@endif--}}
			</div>
			<div class="col-4">
				<!-- Solo el catalogador puede editar campos -->
				@if(Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isSuperAdministrador())
				<!-- ETIQUTA FSC -->
				{!! armarSelectArrayCreateEditOT([1 => "Si", 0=>"No"], 'etiquetas_dsc', 'Etiqueta FSC' , $errors, $ot ,'form-control',true,true) !!}
				<!-- Orientacion Placa -->
				{!! armarSelectArrayCreateEditOT([0,90], 'orientacion_placa', 'Orientacion Placa' , $errors, $ot ,'form-control',true,true) !!}
				@else
				<!-- ETIQUTA FSC -->
				{!! inputReadOnly('ETIQUTA FSC', isset($ot->etiquetas_dsc) ? [1 => "Si", 0=>"No"][$ot->etiquetas_dsc] : null) !!}
				<!-- Orientacion Placa -->
				{!! inputReadOnly('Orientacion Placa',isset($ot->orientacion_placa) ? [0,90][$ot->orientacion_placa] : null) !!}

				@endif
				{{--Ajuste Evolutivo 24-06 de fecha 03-04-2024 correo del cliente
				<!-- Complejidad de Impresion -->
				{!! inputReadOnly('Complejidad de Impresion', $ot->complejidad_impresion) !!}
				--}}
				<!-- Rayado Desfasado -->
				{{--
					{!! inputReadOnly('Rayado Desfasado', $ot->rayado_desfasado) !!}
				--}}
			</div>
			<div class="col-4">
				<!-- Solo el catalogador puede editar campos -->
				@if(Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isSuperAdministrador())
				<!-- Tipo de Prepicado -->
				{!! armarSelectArrayCreateEditOT($precutType, 'precut_type_id', 'Características Adicionales' , $errors, $ot ,'form-control',true,true) !!}
				@else
				<!-- Características Adicionales -->
				{!! inputReadOnly('Características Adicionales', isset($ot->prepicado) ? $ot->prepicado->descripcion : null) !!}

				@endif


				<!-- Solo el catalogador puede editar campos -->
				@if(Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isSuperAdministrador())
				<!-- Estado De Palletizado -->
				{!! armarSelectArrayCreateEditOT($palletStatusTypes, 'pallet_status_type_id', 'Estado De Palletizado' , $errors, $ot ,'form-control',true,true) !!}
				@else
				<!-- Estado De Palletizado -->
				{!! inputReadOnly('Estado De Palletizado', isset($ot->pallet_status_type_id) ? $ot->pallet_status->descripcion : null) !!}

				@endif

				<!-- Impresion de Borde -->
				{!! inputReadOnly('Impresion de Borde', $ot->impresion_borde) !!}
				
			</div>
		</div>
	</div>
</div>