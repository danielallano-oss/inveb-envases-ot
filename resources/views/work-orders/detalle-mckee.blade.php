@extends('layouts.index')

@section('content')
<style>
	.disabled {
        opacity: 0.5;
        pointer-events: none;
        cursor: default;
    }
</style>

<a href="javascript: history.go(-1)" style="font-size: 20px" class="btn btn-link px-0">&lsaquo; Volver</a>
<h1 class="page-title">Datos Formula Mckee OT: {{$ot_id}}</h1>

<form id="filtros" class="py-3" action="{{ route('detalleMckee', $ot_id) }}" method="get" enctype="multipart/form-data">
	@csrf
	<!--<div class="form-row ">
		<div class="col-1">
			<div class="form-group">
				<label>Desde</label>
				<input class="form-control form-control-sm datepicker" id="datePicker1" name="date_desde" value="{{ (is_null(app('request')->input('date_desde')))? $fromDate : app('request')->input('date_desde') }}" autocomplete="off">
			</div>
		</div>
		<div class="col-1">
			<div class="form-group">
				<label>Hasta</label>
				<input class="form-control form-control-sm datepicker" id="datePicker2" name="date_hasta" value="{{ (is_null(app('request')->input('date_hasta')))? $toDate : app('request')->input('date_hasta') }}" autocomplete="off">
			</div>
		</div>
		<div class="col-1">
			<div class="form-group">
				<label>ID Cambio</label>
				<select name="cambio_id[]" id="cambio_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultiple($id_cambios,'cambio_id',['cambio_id'],' ') !!}
				</select>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Descripción</label>
				<select name="descripcion[]" id="descripcion" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultiple($descripciones,'observacion',['observacion'],' ') !!}
				</select>
				@if($dato != null)
					<input id="descripcion_id" type="text" hidden value="{{(!is_null(app('request')->input('descripcion'))) ? $descripcion_filter : null}}">
				@endif
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Campos Modificados</label>
				<select name="campo_id[]" id="campo_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultiple($campos_modificados,'campo_id',['descripcion'],' ') !!}
				</select>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label>Usuario</label>
				<select name="user_id[]" id="user_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
					{!! optionsSelectObjetfilterMultiple($users,'user_id',['nombre','apellido'],' ') !!}
				</select>
			</div>
		</div>
	</div>-->
	<div class="text-left">
		<!--<button class="ml-auto btn btn-primary">Filtrar</button>
		 <a href="{{ route('descargarDetalleLogExcel', $ot_id) }}" target="_blank" class="btn btn-outline-primary" id="exportarSubmit">Exportar</a> -->
		<button id="exportarSubmit" class="btn btn-outline-primary">Exportar</button>
		<input hidden id="exportar" name="exportar" value="">
	</div>
</form>

<div class="container-table">
	<table class="table table-status table-hover" style="width:40%;">
		<thead>
			<tr>
				<th style="width:20%;">Campo</th>
				<th style="width:15%;">Valor</th>
			</tr>
		</thead>
		<tbody>
			@foreach($bitacora_ot as $bitacora)
				@foreach($bitacora->datos_modificados as $key => $value)
					<tr>
						<td>{{$value['texto']}}</td>
						<td>{{$value['valor']['descripcion']}}</td>
					</tr>
				@endforeach
			@endforeach
			<tr>
				<td>Usuario</td>
				<td>{{$bitacora->user_data['nombre']}} {{$bitacora->user_data['apellido']}}</td>
			</tr>
		</tbody>
	</table>
</div>

<!-- Paginacion -->
<nav class="mt-3">
	{!! $bitacora_ot->appends(request()->query())->links('pagination::bootstrap-4') !!}
</nav>
@endsection
@section('myjsfile')
<script>

	$(document).ready(function() {
		// Funcionabilidad para filtrar o exportar
		$(document).on('click', '#exportarSubmit', function(e) {
			e.preventDefault();
			document.getElementById('exportar').value = "Si";
			$('#filtros').submit();
		});

	});

	$(function() {
		const descripcion_id = $("#descripcion_id").val();
		if (descripcion_id) {
			$("#descripcion").val(descripcion_id).selectpicker("refresh")
		}

	});
</script>
@endsection
