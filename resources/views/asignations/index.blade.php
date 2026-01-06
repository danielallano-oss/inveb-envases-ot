@extends('layouts.index')

@section('content')
<h1 class="page-title">Asignación de Órdenes de Trabajo
	@if(Auth()->user()->isVendedor() || Auth()->user()->isJefeVenta() || Auth()->user()->isAdmin())
	<a href="{{route('nuevaOt')}}" class="btn btn-primary rounded-pill ml-3 px-5">Crear OT</a>
	@endif
</h1>

<form id="filtros" class="py-3" action="{{ route('asignaciones') }}" method="get" enctype="multipart/form-data">
	<div class="form-row">
		<div class="col-1">
			<div class="form-group">
				<label>Desde</label>
				<input class="form-control form-control-sm datepicker" id="datePicker1" name="date_desde" value="{{ (is_null(app('request')->input('date_desde')))? null: app('request')->input('date_desde') }}" autocomplete="off">
			</div>
		</div>
		<div class="col-1">
			<div class="form-group">
				<label>Hasta</label>
				<input class="form-control form-control-sm datepicker" id="datePicker2" name="date_hasta" value="{{ (is_null(app('request')->input('date_hasta')))? null : app('request')->input('date_hasta') }}" autocomplete="off">
			</div>
		</div>
		@if(Auth()->user()->isJefeVenta() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño() || Auth()->user()->isJefeCatalogador())
		<div class="col-2">
			<div class="form-group">
				<label>Asignado</label>
				<select name="asignado[]" id="asignado" class="form-control form-control-sm" data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectArrayfilterMultiple(['SI'=>'SI','NO'=>'NO'],'asignado') !!}
				</select>
			</div>
		</div>
		@endif
		<!-- <div class="col-2">
			<div class="form-group">
				<label>Cliente</label>
				<select name="client_id[]" id="client_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultiple($clients,'client_id',['nombre','apellido'],' ') !!}
				</select>
			</div>
		</div> -->
		<div class="col-2">
			<div class="form-group">
				<label>Tipo Solicitud</label>
				<select name="tipo_solicitud[]" id="tipo_solicitud" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectArrayfilterMultiple($tipo_solicitudes,'tipo_solicitud') !!}
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
				<label>Creador</label>
				<select name="vendedor_id[]" id="vendedor_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultiple($vendedores,'vendedor_id',['nombre','apellido'],' ') !!}
				</select>
				<input id="user_id" type="text" hidden value="{{(is_null(app('request')->input('vendedor_id')))? auth()->user()->id : null}}">
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
		<div class="col-1">
			<div class="form-group">
				<label>ID OT</label>
				<input class="form-control form-control-sm " type="text" name="id" id="id" value="{{ (is_null(app('request')->input('id')))? '' : app('request')->input('id') }}">
			</div>
		</div>
	</div>
	<div class="text-right">
		<button class="ml-auto btn btn-primary">Filtrar</button>
	</div>
	<input hidden id="role_id" value="{{Auth()->user()->role_id}}">
</form>


<div class="container-table mt-3 bg-white border">
	<table class="table table-status table-hover">
		<thead>
			<tr>
				<th width="80">OT</th>
				<th width="80">Creación</th>
				<th>Cliente</th>
				<th>Vendedor</th>
				<th width="125">Tipo Solicitud</th>
				<th width="70">Canal</th>
				<th width="125" class="border text-center">Jerarquía 1</th>
				<th width="125" class="border text-center">Jerarquía 2</th>
				<th width="125" class="border text-center">Jerarquía 3</th>
				<th width="80" class="border text-center">CAD</th>
				@if(Auth()->user()->isJefeVenta() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño() || Auth()->user()->isJefeCatalogador())
				<th width="125" class="border text-center">Asignado</th>
				@endif
				@if(!$asignado)
				<th width="50" class="border text-center">Días</th>
				@endif
				<th width="70" class="text-center">Acciones</th>
			</tr>
		</thead>
		<tbody>

			@foreach($ots as $ot)
			<tr @if(isset($ot->oc) && $ot->oc == 1) style="background-color: rgba(255,0,0,0.12);" @endif>
				<td>{{$ot->id}}</td>
				<td><span title="{{$ot->created_at->diffForHumans()}}" data-toggle="tooltip">{{$ot->created_at->format('d/m/y')}}</span></td>
				<td class="text-truncate" title="{{$ot->client->nombre}}" data-toggle="tooltip">{{$ot->client->nombre}}</td>
				<td class="text-truncate" title="{{$ot->creador->fullname}}" data-toggle="tooltip">{{$ot->creador->fullname}}</td>
				<td>{{$tipo_solicitudes[$ot->tipo_solicitud]}}</td>
				<td class="text-truncate" title="{{$ot->canal ? $ot->canal->nombre : null}}" data-toggle="tooltip">{{$ot->canal ? $ot->canal->nombre : null}}</td>
				<td class="text-truncate" title="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->hierarchy->descripcion : "N/A"}}" data-toggle="tooltip">{{$ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->hierarchy->descripcion : "N/A"}}</td>
				<td class="text-truncate" title="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->hierarchy->descripcion : "N/A"}}" data-toggle="tooltip">{{$ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->descripcion : "N/A"}}</td>
				<td class="text-truncate" title="{{$ot->subsubhierarchy ? $ot->subsubhierarchy->descripcion : "N/A"}}" data-toggle="tooltip">{{$ot->subsubhierarchy ? $ot->subsubhierarchy->descripcion : "N/A"}}</td>
				<td>{{$ot->cad}}</td>
				@if(Auth()->user()->isJefeVenta() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño() || Auth()->user()->isJefeCatalogador()|| Auth()->user()->isJefeMuestras())
				<td>
					{!!$ot->present()->profesionalAsignado()!!}
				</td>
				@endif
				@if(!$asignado)
				<td>{!! $ot->present()->tiempoAsignacion() !!}</td> 
				
				@endif
				<td class="text-center">
					<a id="{{$ot->id}}" data-toggle="modal" data-target="#modal-asignacion" class="modalAsignacion"><i class="fas fa-edit" data-toggle="tooltip" title="Asignar"></i></a>
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

<!-- MODA ASIGNACIONES -->

<div class="modal fade" id="modal-asignacion">
	<div class="modal-dialog modal-lg " style="width:80%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="title">Asignación de Órdenes de Trabajo</div>
			</div>
			<div class="modal-body">
				<div id="modal-loader-asignacion" class="loader">Loading...</div>
				<div id="modal-asignacion-content"></div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('myjsfile')
<script src="{{ asset('js/modalAsignacion.js') }}"></script>
@endsection