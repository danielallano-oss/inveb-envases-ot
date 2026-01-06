@extends('layouts.index')

@section('content')
<h1 class="page-title">Órdenes de trabajo con Notificaciones
</h1>

<div class="container-table mt-3 bg-white border">
	<table class="table table-status table-hover">
		<thead>
			<tr>
				<th width="40">OT</th>
				<th width="50" class="border text-center" title='Tiempo Total' data-toggle='tooltip'>Días</th>
				<th>Cliente</th>
				<th>Descripción</th>
				<th width="70">Item</th>
				<th width="50">Estado</th>
				<th>Generador</th>
				<th>Motivo</th>
				<th>Observación</th>
				<th width="80" class="text-center">Accion</th>
			</tr>
		</thead>
		<tbody>

			@foreach($notificaciones as $notificacion)
			<tr>
                @if ($notificacion->work_order_id == 0)
                <td>N/A</td>
				<td class="border">
					N/A
				</td>
				<td class="text-truncate">N/A</td>
				<td class="text-truncate">N/A</td>
				<td>N/A</td>
				<td>N/A</td>
                @else
                <td>{{$notificacion->ot->id}}</td>
				<td class="border">
					{!! $notificacion->ot->present()->tiempoTotal() !!}
				</td>
				<td class="text-truncate" title="{{$notificacion->ot->client->nombreSap}}" data-toggle="tooltip">{{$notificacion->ot->client->nombreSap}}</td>
				<td class="text-truncate" title="{{$notificacion->ot->descripcion}}" data-toggle="tooltip">{{$notificacion->ot->descripcion}}</td>
				<td>{{$notificacion->ot->productType ? $notificacion->ot->productType->descripcion : null}}</td>
				<td title="{{isset($notificacion->ot->ultimoCambioEstado) ? $notificacion->ot->ultimoCambioEstado->state->nombre : 'Proceso de Ventas'}}" data-toggle="tooltip">{{isset($notificacion->ot->ultimoCambioEstado) ? $notificacion->ot->ultimoCambioEstado->state->abreviatura : "PV"}}</td>
                @endif

				<td>{{$notificacion->generador->fullname}}</td>
				<td>{{$notificacion->motivo}}</td>
				<td>{{$notificacion->observacion}}</td>
                @if ($notificacion->work_order_id != 0)
				<td class="text-center" @if(isset($notificacion->ot->oc) && $notificacion->ot->oc == 1) style="background-color: rgba(255,0,0,0.12);" @endif>
					<a href="{{route('gestionarOt', $notificacion->ot->id)}}">
						<div class="material-icons md-14" data-toggle="tooltip" title="Gestionar">search</div>
					</a>
				</td>
                @else
                <td class="text-center">
                <form method="POST" id="form_{{ $notificacion->id }}" action="{{ route('inactivarNotificacion', $notificacion->id) }}" style="display: inline;">
                    @method('put')
                    @csrf
                    <button class="btn_link" type="submit" data-toggle="tooltip" title="Marcar como leída">
                      <div class="material-icons md-14">check</div>
                    </button>
                  </form>
                </td>
                {{-- <td class="text-center">
					<a href="{{route('desactivarNotificacion', $notificacion->id)}}">
						<div class="material-icons md-14" data-toggle="tooltip" title="Marcar como leída">check</div>
					</a>
				</td> --}}
                @endif
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
<!-- Paginacion -->
<nav class="mt-3">
	{!! $notificaciones->appends(request()->query())->links('pagination::bootstrap-4') !!}
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
<script src="{{ asset('js/modalOT.js') }}"></script>

@endsection
