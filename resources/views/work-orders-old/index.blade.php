@extends('layouts.index')

@section('content')
<h1 class="page-title">Órdenes de trabajo
	@if(Auth()->user()->isVendedor() || Auth()->user()->isJefeVenta() || Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo())
	<a href="{{route('nuevaOt')}}" class="btn btn-primary rounded-pill ml-3 px-5">Crear OT</a>
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
		<div class="col-2"></div>
		@endif

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
		<div class="col-2"></div>
		@endif
		<div class="col-2">
			<div class="form-group">
				<label>Área</label>
				<select name="area_id[]" id="area_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultiple($areas,'area_id',['nombre'],' ') !!}
				</select>
				<input id="area_actual_id" type="text" hidden value="{{(is_null(app('request')->input('area_id')))? 6 : null}}">

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

	</div>
	<div class="text-right">
		<button class="ml-auto btn btn-primary">Filtrar</button>
	</div>
</form>


<div class="container-table mt-3 bg-white border">
	<table class="table table-status table-hover">
		<thead>
			<tr>
				<th width="40">OT</th>
				<th width="60">Creación</th>
				<!-- <th width="60">Estado</th> -->
				<th>Creador</th>
				<th>Cliente</th>
				<th>Descripción</th>
				<th width="50">Canal</th>
				<th width="70">Item</th>
				<th width="50">Estado</th>
				<th width="50" class="border text-center" title='Tiempo Total' data-toggle='tooltip'>
					<div class="material-icons md-14">query_builder</div>
				</th>
				<th width="110" class="border text-center">Ventas</th>
				<th width="110" class="border text-center">Dis. Estructural</th>
				<th width="110" class="border text-center">Muestra</th>
				<th width="110" class="border text-center">Dis. Gráfico</th>
				<th width="110" class="border text-center">Pre-Cat.</th>
				<th width="110" class="border text-center">Catalogación</th>
				<th width="60" class="text-center">Acciones</th>
			</tr>
		</thead>
		<tbody>

			@foreach($ots as $ot)
			<!-- Destacamos la fila si fue Rechazado o Entregado al area del usuario o si tiene una consulta a su area como ultima gestion -->
			<tr @if(isset(auth()->user()->role->area) && ($ot->ultimoCambioEstado->state_id == 12 && $ot->current_area_id == auth()->user()->role->area->id )
				|| (isset(auth()->user()->role->area) && ($ot->ultimoCambioEstado->state_id == 13 && $ot->current_area_id == auth()->user()->role->area->id))
				|| ((isset($ot->gestiones) && isset($ot->gestiones[0]->consulted_work_space_id) && isset(auth()->user()->role->area) && $ot->gestiones[0]->consulted_work_space_id == auth()->user()->role->area->id)) && $ot->gestiones[0]->respuesta == null
				|| ((isset($ot->muestrasPrioritarias) && count($ot->muestrasPrioritarias)> 0) && (auth()->user()->isJefeMuestras() ||auth()->user()->isTecnicoMuestras()) ))
				style="background-color: #d1f3d1;" @endif>
				<td>{{$ot->id}}</td>
				<td><span title="{{$ot->created_at->diffForHumans()}}" data-toggle="tooltip">{{$ot->created_at->format('d/m/y')}}</span></td>
				<!-- <td>@if($ot->active)Activo @else Inactivo @endif</td> -->
				<td class="text-truncate" title="{{$ot->creador->fullname}}" data-toggle="tooltip">{{$ot->creador->fullname}}</td>
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
<script>
	$(function() {
		$("#estado_id").val([1, 2, 3, 4, 5, 6, 7, 10, 12, 14, 15, 16, 17, 18]).selectpicker("refresh")
	});
</script>
@endif

@if(auth()->user()->isVendedor())
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
@endsection