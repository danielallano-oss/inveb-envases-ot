@extends('layouts.index')

@section('content')
<h1 class="page-title">Órdenes de trabajo
	@if(Auth()->user()->isVendedor() || Auth()->user()->isJefeVenta() || Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isVendedorExterno() || Auth()->user()->isJefeDiseño() || Auth()->user()->isDiseñador())
	<a href="{{route('selectOt')}}" class="btn btn-primary rounded-pill ml-3 px-5">Crear OT</a>
	@endif
	<a href="{{route('notificacionesOT')}}" class="btn btn-primary rounded-pill ml-3 px-5">Notificaciones OT <sup>
			<div style="font-size: 1em;border-radius: 10rem;margin: 0 auto;" class="badge badge-danger">{{auth()->user()->totalNotificacionesActivas->count()}}</div>
		</sup></a>

	@if(auth()->user()->isVendedor())
		<a href="{{route('cotizarMultiplesOt')}}" class="btn btn-primary rounded-pill ml-3 px-5">Cotizar multiples OT </a>
	@endif
</h1>

<form id="filtros" class="py-3" action="{{ route('home') }}" method="get" enctype="multipart/form-data">
	<div class="form-row ">
		<div class="col-1">
			<div class="form-group">
				<label>Desde</label>
				<input class="form-control form-control-sm datepicker" id="datePicker1" name="date_desde" value="{{ (is_null(app('request')->input('date_desde')))? null : app('request')->input('date_desde') }}" autocomplete="off">
			</div>
		</div>
		<div class="col-1">
			<div class="form-group">
				<label>Hasta</label>
				<input class="form-control form-control-sm datepicker" id="datePicker2" name="date_hasta" value="{{ (is_null(app('request')->input('date_hasta')))? null : app('request')->input('date_hasta') }}" autocomplete="off">
			</div>
		</div>
		<div class="col-1">
			<div class="form-group">
				<label>ID OT</label>
				<input class="form-control form-control-sm " type="text" name="id" id="id" value="{{ (is_null(app('request')->input('id')))? '' : app('request')->input('id') }}">
			</div>
		</div>
		<div class="col-1">
			<div class="form-group">
				<label>Material</label>
				<input class="form-control form-control-sm " type="text" name="material" id="material" value="{{ (is_null(app('request')->input('material')))? '' : app('request')->input('material') }}">
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Creador</label>
				<select name="vendedor_id[]" id="vendedor_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultiple($vendedores,'vendedor_id',['nombre','apellido'],' ') !!}
				</select>
				<input id="user_id" type="text" hidden value="{{(is_null(app('request')->input('vendedor_id')))? auth()->user()->id : null}}">
			</div>
		</div>
		@if(Auth()->user()->isIngeniero() || Auth()->user()->isDiseñador() || Auth()->user()->isCatalogador())
			<div class="col-2">
				<div class="form-group">
					<label>Asignado</label>
					<select name="asignado[]" id="asignado" class="form-control form-control-sm" data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
						{!! optionsSelectArrayfilterMultiple(['SI'=>'SI','NO'=>'NO'],'asignado') !!}
					</select>
				</div>
			</div>
			<div class="col-2">
				<div class="form-group">
					<label>Responsable</label>
					<select name="responsable_id[]" id="responsable_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
						{!! optionsSelectObjetfilterMultiple($responsables,'responsable_id',['nombre','apellido'],' ') !!}
					</select>
					<input id="responsable" type="text" hidden value="{{(is_null(app('request')->input('responsable_id')))? auth()->user()->id : null}}">
				</div>
			</div>
		@endif
		@if(Auth()->user()->isJefeVenta() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeMuestras() || Auth()->user()->isJefeDiseño() || Auth()->user()->isJefeCatalogador()|| Auth()->user()->isJefePrecatalogador())
			<div class="col-2">
				<div class="form-group">
					<label>Asignado</label>
					<select name="asignado_id[]" id="asignado_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
						{!! optionsSelectObjetfilterMultipleNew($asignados,'asignado_id',['nombre','apellido'],' ') !!}

					</select>
				</div>
			</div>
		@endif
		<div class="col-2">
			<div class="form-group">
				<label>Cinta</label>
				<select name="cinta_id[]" id="cinta_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectArrayfilterMultipleNew([1=>'SI',0=>'NO'],'cinta_id') !!}
				</select>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Canal</label>
				<select name="canal_id[]" id="canal_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultiple($canals,'canal_id',['nombre','apellido'],' ') !!}
				</select>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Estado</label>
				<select name="estado_id[]" id="estado_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultiple($estados,'estado_id',['nombre'],' ') !!}
				</select>
			</div>
		</div>
		@if(Auth()->user()->isIngeniero() || Auth()->user()->isDiseñador() || Auth()->user()->isCatalogador())
		@else

		@endif
		<div class="col-2">
			<div class="form-group">
				<label>Área</label>
				<select name="area_id[]" id="area_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultiple($areas,'area_id',['nombre'],' ') !!}
				</select>
				<input id="area_actual_id" type="text" hidden value="{{(is_null(app('request')->input('area_id')))? 6 : null}}">
				<input id="area_user" name="area_user" type="hidden"  value="{{$area_user}}">
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Cliente</label>
				<select name="client_id[]" id="client_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultiple($clients,'client_id',['nombre','apellido'],' ') !!}
				</select>
			</div>
		</div>
		<div class="col-1">
			<div class="form-group">
				<label>CAD</label>
				<input class="form-control form-control-sm " type="text" name="cad" id="cad" value="{{ (is_null(app('request')->input('cad')))? '' : app('request')->input('cad') }}">
			</div>
		</div>
		<div class="col-1">
			<div class="form-group">
				<label>Cartón</label>
				<input class="form-control form-control-sm " type="text" name="carton" id="carton" value="{{ (is_null(app('request')->input('carton')))? '' : app('request')->input('carton') }}">
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Descripción</label>
				<input class="form-control form-control-sm " type="text" name="descripcion" id="descripcion" value="{{ (is_null(app('request')->input('descripcion')))? '' : app('request')->input('descripcion') }}">
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>FSC</label>
				<select name="fsc_codigo[]" id="fsc_codigo" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultipleNew($fsc_s,'fsc_codigo',['descripcion'],' ') !!}
				</select>
			</div>
		</div>
		<div class="col-1">
			<div class="form-group">
				<label>Planta</label>
				<select name="planta_id[]" id="planta_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultipleNew($plantas,'planta_id',['nombre'],' ') !!}
				</select>
			</div>
		</div>
		<div class="col-1">
			<div class="form-group">
				<label>Complejidad</label>
				<select name="complejidad_id[]" id="complejidad_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectArrayfilterMultipleNew(['Baja'=>'Baja','Media'=>'Media','Alta'=>'Alta'],'complejidad_id') !!}
				</select>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Impresión</label>
				<select name="impresion_id[]" id="impresion_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultipleNew($impresiones,'impresion_id',['descripcion'],' ') !!}
				</select>
			</div>
		</div>

		<div class="col-2">
			<div class="form-group">
				<label>Diseñador Estructural</label>
				<select name="disenador_estructural_id[]" id="disenador_estructural_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultipleNew($disenadores_estructurales,'disenador_estructural_id',['nombre','apellido'],' ') !!}
				</select>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Diseñador Gráfico</label>
				<select name="disenador_grafico_id[]" id="disenador_grafico_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultipleNew($disenadores_graficos,'disenador_grafico_id',['nombre','apellido'],' ') !!}
				</select>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Proceso</label>
				<select name="proceso_id[]" id="proceso_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultipleNew($procesos,'proceso_id',['descripcion'],' ') !!}
				</select>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Tipo Solicitud</label>
				<select name="solicitud_id[]" id="solicitud_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectArrayfilterMultipleNew([1 => "Desarrollo Completo", 3 => "Muestra con CAD", 7 => "OT Proyectos Innovación",  5 => "Arte con Material", 6 => "Otras Solicitudes Desarrollo"],'solicitud_id') !!}
				</select>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Jerarquía 2</label>
				<select name="subhierarchy_id[]" id="subhierarchy_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultipleNew($subhierarchys,'subhierarchy_id',['descripcion'],' ') !!}
				</select>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Estilo</label>
				<select name="style_id[]" id="style_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-deselect-text-format="count > 1">
					{!! optionsSelectObjetfilterMultipleNew($styles,'style_id',['glosa'],' ') !!}
				</select>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Recubrimiento Interno</label>
				<select name="coverage_interno_id[]" id="coverage_interno_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultipleNew($coverage_internos,'coverage_interno_id',['descripcion'],' ') !!}
				</select>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Recubrimiento Externo</label>
				<select name="coverage_externo_id[]" id="coverage_externo_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultipleNew($coverage_externos,'coverage_externo_id',['descripcion'],' ') !!}
				</select>
			</div>
		</div>
		@if(auth()->user()->isDiseñador() || auth()->user()->isJefeDiseño() || auth()->user()->isSuperAdministrador())
			<div class="col-2">
				<div class="form-group">
					<label>Diseñador Externo</label>
					<select name="proveedor_id[]" id="proveedor_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
						{!! optionsSelectObjetfilterMultipleNew($proveedores,'proveedor_id',['name'],' ') !!}
					</select>
				</div>
			</div>
		@endif
	</div>
	<div class="text-right">
		<button class="ml-auto btn btn-primary">Filtrar</button>
	</div>
</form>

<div class="container-table mt-3 bg-white border">
	<table class="table table-status table-hover">
		<thead>
			<tr>
				<th width="65"><center>OT</center></th>
				<th width="60">Creación</th>
				<!-- <th width="60">Estado</th> -->
				<!--<th>Creador</th>-->
				<th width="60">Cliente</th>
				<th width="75">Descripción</th>
				<th width="45">Canal</th>
				<th width="45">Item</th>
				<th width="45">Estado</th>
				<th width="50" class="border text-center" title='Tiempo Total' data-toggle='tooltip'>
					<div class="material-icons md-14">query_builder</div>
				</th>
				<th width="105" class="border text-center">Ventas</th>
				<th width="105" class="border text-center">Dis. Estructural</th>
				<th width="105" class="border text-center">Muestra</th>
				<th width="105" class="border text-center">Dis. Gráfico</th>
				<th width="105" class="border text-center">Dis. Externo</th>
				<th width="105" class="border text-center">Calc. Paletizado</th>
				<th width="105" class="border text-center">Catalogación</th>
				<th width="60" class="text-center">Acciones</th>
			</tr>
		</thead>
		<tbody>

			@foreach($ots as $ot)
			<!-- Destacamos la fila si fue Rechazado o Entregado al area del usuario o si tiene una consulta a su area como ultima gestion -->
			@if((auth()->user()->isDiseñador() || auth()->user()->isJefeDiseño() || auth()->user()->isSuperAdministrador()) && (in_array($ot->id,$ots_envio_dg_externo_array)))
				<tr style="background-color: #cdfaf2;">
			@else
				<tr @if(isset(auth()->user()->role->area) && ($ot->ultimoCambioEstado->state_id == 12 && $ot->current_area_id == auth()->user()->role->area->id )
					|| (isset(auth()->user()->role->area) && ($ot->ultimoCambioEstado->state_id == 13 && $ot->current_area_id == auth()->user()->role->area->id))
					|| ((isset($ot->gestiones) && isset($ot->gestiones[0]->consulted_work_space_id) && isset(auth()->user()->role->area) && $ot->gestiones[0]->consulted_work_space_id == auth()->user()->role->area->id)) && $ot->gestiones[0]->respuesta == null
					|| ((isset($ot->muestrasPrioritarias) && count($ot->muestrasPrioritarias)> 0) && (auth()->user()->isJefeMuestras() ||auth()->user()->isTecnicoMuestras()) ))
					style="background-color: #d1f3d1;" @endif>
			@endif
			@if(in_array($ot->creador_id,$vendedores_externos))
				<td><div class="material-icons md-14" data-toggle="tooltip" title="Vendedor Externo">person</div> <span title="Creador: {{$ot->creador->fullname}}" data-toggle="tooltip">{{$ot->id}}</span></td>
			@else
				@if($ot->tipo_solicitud==6)
					@if($ot->ajuste_area_desarrollo==1)
						<td><div class="material-icons md-14" data-toggle="tooltip" title="Licitación">request_page</div> <span title="Creador: {{$ot->creador->fullname}}" data-toggle="tooltip">{{$ot->id}}</span></td>
					@else
						@if($ot->ajuste_area_desarrollo==2)
							<td><div class="material-icons md-14" data-toggle="tooltip" title="Ficha Técnica">list_alt</div> <span title="Creador: {{$ot->creador->fullname}}" data-toggle="tooltip">{{$ot->id}}</span></td>
						@else
							<td><div class="material-icons md-14" data-toggle="tooltip" title="Estudio Benchmarking">book</div> <span title="Creador: {{$ot->creador->fullname}}" data-toggle="tooltip">{{$ot->id}}</span></td>
						@endif
					@endif

				@else
					@if($ot->tipo_solicitud==7)
						<td><div class="material-icons md-14" data-toggle="tooltip" title="Proyecto Innovación">info</div> <span title="Creador: {{$ot->creador->fullname}}" data-toggle="tooltip">{{$ot->id}}</span></td>
					@else
						<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span title="Creador: {{$ot->creador->fullname}}" data-toggle="tooltip">{{$ot->id}}</span></td>
					@endif
				@endif


			@endif
				<td><span title="{{$ot->created_at->diffForHumans()}}" data-toggle="tooltip">{{$ot->created_at->format('d/m/y')}}</span></td>
				<!-- <td>@if($ot->active)Activo @else Inactivo @endif</td> -->
				<!--<td class="text-truncate" title="{{$ot->creador->fullname}}" data-toggle="tooltip">{{$ot->creador->fullname}}</td>-->
				<td class="text-truncate" title="{{$ot->client->nombreSap}}" data-toggle="tooltip">{{$ot->client->nombreSap}}</td>
				<td class="text-truncate" title="{{$ot->descripcion}}" data-toggle="tooltip">{{$ot->descripcion}}</td>
				<td class="text-center" title="{{$ot->canal ? $ot->canal->nombre : null}}" data-toggle="tooltip">{{$ot->canal ? substr($ot->canal->nombre, 0 , 1) : null}}</td>
				<td>{{$ot->productType ? $ot->productType->descripcion : null}}</td>
				<td title="{{isset($ot->ultimoCambioEstado) ? $ot->ultimoCambioEstado->state->nombre : 'Proceso de Ventas'}}" data-toggle="tooltip">{{isset($ot->ultimoCambioEstado) ? $ot->ultimoCambioEstado->state->abreviatura : "PV"}}</td>
				<td class="border">
					<!-- <div class='pill-status' title='' data-toggle='tooltip'>
						<div style='font-size: 1em;border-radius: 10rem;margin: 0 auto;' class='badge badge-success'>5</div>
					</div> -->

					{!! $ot->present()->tiempoTotal() !!}
				</td>
				<td class="border">
					{!! $ot->present()->tiempoVenta() !!}
				</td>
				<td class="border">
					{!!$ot->present()->tiempoDesarrollo()!!}
				</td>
				<td class="border">
					{!!$ot->present()->tiempoMuestra()!!}
				</td>
				<td class="border">
					{!!$ot->present()->tiempoDiseño()!!}
				</td>
				<td class="border">
					{!!$ot->present()->tiempoDisenadorExterno()!!}
				</td>
				<td class="border">
					{!!$ot->present()->tiempoPrecatalogacion()!!}
				</td>
				<td class="border">
					{!!$ot->present()->tiempoCatalogacion()!!}
				</td>
				<td class="text-center" @if(isset($ot->oc) && $ot->oc == 1) style="background-color: rgba(255,0,0,0.12);" @endif>
					<!-- <a href="{{route('editOt', $ot->id)}}">
						<div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
					</a> -->
					<a href="{{route('gestionarOt', $ot->id)}}">
						<div class="material-icons md-14" data-toggle="tooltip" title="Gestionar">search</div>
					</a>
					<!-- Se muestra el detalle del log, solo si la OT tiene cambios guardados en el log -->
					@if(in_array($ot->id, $check_bitacora) && Auth()->user()->isSuperAdministrador())
						<a href="{{route('detalleLogOt', $ot->id)}}">
							<div class="material-icons md-14 ml-1" data-toggle="tooltip" title="Ver Detalle Log" style="">description</div>
						</a>
					@endif
					@if((auth()->user()->isDiseñador() || auth()->user()->isJefeDiseño() || auth()->user()->isSuperAdministrador()) && (in_array($ot->id,$ots_envio_dg_externo_array)))
						<div class="material-icons md-14 ml-1" data-toggle="tooltip" title="Envio Diseñador Externo: {{$ots_envio_dg_externo_array_proveedor[$ot->id]}}" style="">outgoing_mail</div>
					@endif
				</td>
			</tr>
			@endforeach

		</tbody>
	</table>
</div>
<!-- Paginacion -->
<nav class="mt-3">
	{!! $ots->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>
@endsection
@section('myjsfile')
@if(is_null(request()->input('estado_id')))
	@if(auth()->user()->isSuperAdministrador())
		<script>
			$(function() {
				$("#estado_id").val([1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21,22]).selectpicker("refresh")
			});
		</script>
	@else
		<script>
			$(function() {
				$("#estado_id").val([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22]).selectpicker("refresh")
			});
		</script>
	@endif
@endif

@if(auth()->user()->isVendedor() || auth()->user()->isVendedorExterno())
	<script>
		$(function() {
			const user_id = $("#user_id").val();
			console.log("user_id", user_id);
			if (user_id) {
				$("#vendedor_id").val(user_id).selectpicker("refresh")
			}
		});
	</script>
@elseif(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras())
	<script>
		$(function() {
			const area_actual_id = $("#area_actual_id").val();
			console.log("area_actual_id", area_actual_id);
			if (area_actual_id) {
				$("#area_id").val(area_actual_id).selectpicker("refresh")
			}

		});
	</script>
@elseif(Auth()->user()->isIngeniero() || Auth()->user()->isDiseñador() || Auth()->user()->isCatalogador())
	<script>
		$(function() {
			const responsable_id = $("#responsable").val();
			console.log("responsable_id", responsable_id);
			if (responsable_id) {
				$("#responsable_id").val(responsable_id).selectpicker("refresh")
				$("#asignado").val("SI").selectpicker("refresh")
			}
		});
	</script>
@endif
@if(Auth()->user()->isDiseñador() || Auth()->user()->isJefeDiseño())
	<script>
		$(function() {
			const area_user = $("#area_user").val();
			if(area_user){
				$("#area_id").val(area_user).selectpicker("refresh");
			}
			$("#estado_id").val([1, 2, 3, 4, 5, 6, 7, 10, 12, 14, 15, 16, 17, 18, 20, 21, 22]).selectpicker("refresh")
		});
	</script>
@endif

@if(auth()->user()->isVendedor() || auth()->user()->isJefeVenta())
	<script>
		$(function() {
			$("#estado_id").val([1, 2, 3, 4, 5, 6, 7, 10, 12, 13, 14, 15, 16, 17, 18, 20, 21, 22]).selectpicker("refresh")
		});
	</script>
@endif

@if(Auth()->user()->isCatalogador() 	|| 	Auth()->user()->isJefeCatalogador()	||
	Auth()->user()->isPrecatalogador() 	||	Auth()->user()->isJefePrecatalogador())
	<script>
		$(function() {
			$("#area_id").val([4,5]).selectpicker("refresh");
			$("#estado_id").val([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22]).selectpicker("refresh")
		});
	</script>
@endif
@if(Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo())
	<script>
		$(function() {
			$("#estado_id").val([1, 2, 3, 4, 5, 6, 7, 10, 12, 14, 15, 16, 17, 18, 20, 21, 22]).selectpicker("refresh")
		});
	</script>
@endif
@endsection
