@extends('layouts.index')

@section('content')
<h1 class="page-title">Órdenes de trabajo por Aprobar
</h1>

<div class="container-table mt-3 bg-white border">
	<table class="table table-status table-hover">
		<thead>
			<tr>
				<th width="40">OT</th>
				<th width="80">Creación</th>
				<!-- <th width="60">Estado</th> -->
				<th>Creador</th>
				<th>Cliente</th>
				<th>Descripción</th>
				<th width="80">Canal</th>
				<th width="70">Item</th>
				<th width="50">Estado</th>
				<th width="80" class="text-center">Acciones</th>
			</tr>
		</thead>
		<tbody>

			@foreach($ots as $ot)
			<!-- Destacamos la fila si fue Rechazado o Entregado al area del usuario o si tiene una consulta a su area como ultima gestion -->
			<tr>
				<td>{{$ot->id}}</td>
				<td><span title="{{$ot->created_at->diffForHumans()}}" data-toggle="tooltip">{{$ot->created_at->format('d/m/y')}}</span></td>
				<!-- <td>@if($ot->active)Activo @else Inactivo @endif</td> -->
				<td class="text-truncate" title="{{$ot->creador->fullname}}" data-toggle="tooltip">{{$ot->creador->fullname}}</td>
				<td class="text-truncate" title="{{$ot->client->nombreSap}}" data-toggle="tooltip">{{$ot->client->nombreSap}}</td>
				<td class="text-truncate" title="{{$ot->descripcion}}" data-toggle="tooltip">{{$ot->descripcion}}</td>
				<td>{{$ot->canal ? $ot->canal->nombre : null}}</td>
				<td>{{$ot->productType ? $ot->productType->descripcion : null}}</td>
				<td title="{{isset($ot->ultimoCambioEstado) ? $ot->ultimoCambioEstado->state->nombre : 'Proceso de Ventas'}}" data-toggle="tooltip">{{isset($ot->ultimoCambioEstado) ? $ot->ultimoCambioEstado->state->abreviatura : "PV"}}</td>

				<td class="text-center">
					<a href="#" class=" modalVerOt" id="{{$ot->id}}" data-toggle="modal" data-target="#modal-ver-ot">
						<div class="material-icons md-14" data-toggle="tooltip" title="Visualizar">search</div>
					</a>
					<!-- <a href="{{route('gestionarOt', $ot->id)}}">
						<div class="material-icons md-14" data-toggle="tooltip" title="Aprobar">check</div>
					</a>
					<a href="{{route('gestionarOt', $ot->id)}}">
						<div class="material-icons md-14" data-toggle="tooltip" title="Rechazar">not_interested</div>
					</a> -->

					<!-- aprobar rechar -->
					<form method="POST" action="{{ route('aprobarOt', $ot->id) }}" style="display: inline;">
						@method('put')
						@csrf
						<button class="btn_link" type="submit">
							<div class="material-icons md-14" data-toggle="tooltip" title="Aprobar">check_circle</div>
						</button>
					</form>
					<form method="POST" action="{{ route('rechazarOt', $ot->id) }}" style="display: inline;">
						@method('put')
						@csrf
						<button class="btn_link" type="submit">
							<i class="material-icons md-14" data-toggle="tooltip" title="Rechazar">remove_circle</i>
						</button>
					</form>
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

<input type="hidden" id="role_id" name="role_id" value="{{auth()->user()->role->id}}">
<div class="modal fade" id="modal-ver-ot">
	<div class="modal-dialog modal-xl " style="width:100%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title page-title">
					<h1 class="page-title">Visualización OT <span class="text-primary" id="numero_ot"></span></h1>
				</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="    background-color: #F2F4FD">
				<div id="modal-loader" class="loader">Loading...</div>
				<div id="modal-ver-ot-content"></div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('myjsfile')
<script src="{{ asset('js/ot-old-vertion/modalOT.js') }}"></script>

@endsection