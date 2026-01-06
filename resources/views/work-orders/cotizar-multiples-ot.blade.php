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
<h1 class="page-title">Lista de OT para Cotizar</h1>

<form id="filtros" class="py-3" action="{{ route('cotizarMultiplesOt') }}" method="get" enctype="multipart/form-data">
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
				<label>OT</label>
				<input class="form-control form-control-sm " type="text" name="id" id="id" value="{{ (is_null(app('request')->input('id')))? '' : app('request')->input('id') }}">
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
		<div class="col-2">
			<div class="form-group">
				<label>Descripción</label>
				<input class="form-control form-control-sm " type="text" name="descripcion" id="descripcion" value="{{ (is_null(app('request')->input('descripcion')))? '' : app('request')->input('descripcion') }}">
			</div>
		</div>

	</div>
	<div class="text-right">
		<button class="ml-auto btn btn-primary">Filtrar</button>
        <a href="#"
            class="ml-auto btn btn-light disabled"
            style="background-color:#ccc"
            id="cotizar">Cotizar
        </a>
	</div>
</form>

<div class="container-table mt-3 bg-white border">
	<table class="table table-status table-hover">
		<thead>
			<tr>
				<th>OT</th>
				<th>Creación</th>
				<th>Creador</th>
				<th>Cliente</th>
				<th>Descripción</th>
				<th>CAD</th>
				<th>Item</th>
				<!-- <th class="text-center">Todos<br><input type="checkbox" id="selectall"></th> -->
				<th class="text-center">Tipo Corrugado<br><input type="checkbox" name="tipo_detalle" id="tipo_detalle_corrugado" value="1"></th>
				<th class="text-center">Tipo Esquinero<br><input type="checkbox" name="tipo_detalle" id="tipo_detalle_esquinero" value="2"></th>
			</tr>
		</thead>
		<tbody>
			@foreach($ots as $ot)
                <!-- Verificacion de campos para cotizar  -->
                @if(isset($ot->area_hc) && isset($ot->golpes_largo) && isset($ot->golpes_ancho) && isset($ot->largura_hm) && isset($ot->anchura_hm) && isset($ot->process_id) && isset($ot->carton_id))

                <!-- Destacamos la fila si fue Rechazado o Entregado al area del usuario o si tiene una consulta a su area como ultima gestion -->
                <tr @if(isset(auth()->user()->role->area) && ($ot->ultimoCambioEstado->state_id == 12 && $ot->current_area_id == auth()->user()->role->area->id )
                    || (isset(auth()->user()->role->area) && ($ot->ultimoCambioEstado->state_id == 13 && $ot->current_area_id == auth()->user()->role->area->id))
                    || ((isset($ot->gestiones) && isset($ot->gestiones[0]->consulted_work_space_id) && isset(auth()->user()->role->area) && $ot->gestiones[0]->consulted_work_space_id == auth()->user()->role->area->id)) && $ot->gestiones[0]->respuesta == null
                    || ((isset($ot->muestrasPrioritarias) && count($ot->muestrasPrioritarias)> 0) && (auth()->user()->isJefeMuestras() ||auth()->user()->isTecnicoMuestras()) ))
                    style="background-color: #d1f3d1;" @endif>
                    <td>{{$ot->id}}</td>
                    <td><span title="{{$ot->created_at->diffForHumans()}}" data-toggle="tooltip">{{$ot->created_at->format('d/m/y')}}</span></td>
                    <td class="text-truncate" title="{{$ot->creador->fullname}}" data-toggle="tooltip">{{$ot->creador->fullname}}</td>
                    <td class="text-truncate" title="{{$ot->client->nombreSap}}" data-toggle="tooltip">{{$ot->client->nombreSap}}</td>
                    <td class="text-truncate" title="{{$ot->descripcion}}" data-toggle="tooltip">{{$ot->descripcion}}</td>
                    <td class="text-truncate" title="{{$ot->cad}}" data-toggle="tooltip">{{$ot->cad}}</td>
                    <td>{{$ot->productType ? $ot->productType->descripcion : null}}</td>
                    <!-- <td class="text-center" @if(isset($ot->oc) && $ot->oc == 1) style="background-color: rgba(255,0,0,0.12);" @endif>
                        <input type="checkbox" class="checkbox_list" name="ot_lista[]" value="{{$ot->id}}">
                    </td> -->
                    <td class="text-center">
                        <input type="checkbox" class="checkbox_corrugado" name="tipo_detalle_list-{{$ot->id}}" value="1"> <!-- corrugado -->
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="checkbox_esquinero" name="tipo_detalle_list-{{$ot->id}}" value="2"> <!-- esquinero -->
                    </td>
                </tr>
                @endif
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
<script src="{{ asset('js/cotizar-multiples-ot-validation.js') }}"></script>

<script>
    $(document).ready(function() {
		$("#tipo_detalle_id").change(() => {
            // Si esta vacio
            if (jQuery.isEmptyObject($("#tipo_detalle_id").val())) {
                $("#enviarOT").prop("disabled", true);
            } else {
                $("#enviarOT").prop("disabled", false);
            }
        })
        .triggerHandler("change");
	});

	$(function() {
		$("#estado_id").val([1, 2, 3, 4, 5, 6, 7, 10, 12, 14, 15, 16, 17, 18]).selectpicker("refresh")
	});

</script>
@endif

@if(auth()->user()->isVendedor())
<script>
	$(function() {
		const user_id = $("#user_id").val();
		// console.log("user_id", user_id);
		if (user_id) {
			$("#vendedor_id").val(user_id).selectpicker("refresh")
		}

	});
</script>
@elseif(auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras())
<script>
	$(function() {
		const area_actual_id = $("#area_actual_id").val();
		// console.log("area_actual_id", area_actual_id);
		if (area_actual_id) {
			$("#area_id").val(area_actual_id).selectpicker("refresh")
		}

	});
</script>
@elseif(Auth()->user()->isIngeniero() || Auth()->user()->isDiseñador() || Auth()->user()->isCatalogador())
<script>
	$(function() {
		const responsable_id = $("#responsable").val();
		// console.log("responsable_id", responsable_id);
		if (responsable_id) {
			$("#responsable_id").val(responsable_id).selectpicker("refresh")
			$("#asignado").val("SI").selectpicker("refresh")
		}
	});
</script>
@endif
@endsection
