@if(isset($cotizacion) && ( $cotizacion->estado_id == 2 || $cotizacion->estado_id > 2 && count($cotizacion->aprobaciones) > 0 ) && (!Auth()->user()->isJefeVenta() || (Auth()->user()->isJefeVenta() && $cotizacion->user->jefe_id == Auth()->user()->id)))
<h1 class="page-title">Gestiones de Aprobación</h1>
<!-- Tabla / Listado aprobacion de cotizaciones -->
<div class="container-table mt-3 bg-white border px-2">
    <table class="table table-status table-hover ">
        <thead>
            <tr>
                <th>Acción</th>               
                <th>Motivo </th>               
                <th>Usuario</th>
                <th>Fecha </th>
                <th>Estado </th>
            </tr>
        </thead>
        <tbody>
            @foreach($cotizacion->aprobaciones as $aprobacion)
            <tr>
                <td>{{ $aprobacion->action_made }}</td>
                <td>{{ $aprobacion->motivo}}</td>                
                <td>{{ $aprobacion->user->fullname }}</td>
                <td>{{ $aprobacion->created_at }}</td>
                <td>@if($aprobacion->action_made == "Rechazo")
                    Cotización Rechazada
                    @elseif($aprobacion->action_made == "Rechazo Automático")
                    Cotización Rechazada de forma Automática
                    @elseif($aprobacion->action_made == "Aprobación Total")
                    Cotización Aprobada
                    @elseif($aprobacion->action_made == "Aprobación Parcial")

                    @if($aprobacion->role_do_action == 3)
                    Por Aprobar Gerente Comercial
                    @elseif($aprobacion->role_do_action == 15)
                    Por Aprobar Gerente General
                    @endif
                    @endif
                </td>
            </tr>
            @endforeach
            @if($cotizacion->aprobaciones->count() < 1) <tr>
                <td colspan="4"> No hay gestiones actualmente</td>
                </tr>
                @endif
        </tbody>
    </table>
</div>
@endif

@if(isset($cotizacion) && auth()->user()->id != $cotizacion->user_id && $cotizacion->estado_id == 2 && $cotizacion->role_can_show == Auth()->user()->role_id && (!Auth()->user()->isJefeVenta() || (Auth()->user()->isJefeVenta() && $cotizacion->user->jefe_id == Auth()->user()->id)))


@if(isset($cotizacion) && ( $cotizacion->enviar_a_comite == 1 ))
    <div class="container-table mt-3 bg-white border px-2">
        <table class="table table-status table-hover ">
            <thead>
                <tr>
                    <th style="color: red;">
                        <h5>
                            Se recomienda rechazar debido a que tiene margen bruto negativo (MG BRUTO= PRECIO - CV - CIF - GVV - Mano de Obra - GF Planta). En caso de querer aprobarlo, llevar el caso al "Comité de Pricing". 
                        </h5>
                    </th>               
                    
                </tr>
            </thead>
            
          
        </table>
    </div>
@endif
<br>
<div class="" style="display: flex;justify-content:space-around">

    <button class="btn btn-lg btn-success" type='button' data-toggle="collapse" data-target="#aprobar-form">Aprobar</button>
    <button class="btn btn-lg btn-danger" type='button' data-toggle="collapse" data-target="#rechazo-form">Rechazar</button>
</div>
<div id="aprobar-form" class="collapse">
    <div class="card mt-3">
        <div class="card-body">
            <form id="aprobacion-cotizacion" method="POST" action="{{ route('cotizador.gestionar-cotizacion', $cotizacion->id) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="estado" value="aprobado">
                <div class="form-group form-row">
                    <label for="observacion" class="col-auto col-form-label text-right">Comentario (Opcional)</label>
                    <div class="col">
                        <textarea class="form-control" name="observacion" id="observacion" cols="30" rows="2"></textarea>
                    </div>
                </div>
                <div class="row mt-3 ">
                    <div class="col-12 ml-auto text-center">
                        <a href="#" data-toggle="modal" data-target="#modal-aprobar-cotizacion" class="btn btn-success btn-lg px-5">Aprobar Cotización</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="rechazo-form" class="collapse">
    <div class="card mt-3">
        <div class="card-body">
            <form id="rechazo-cotizacion" method="POST" action="{{ route('cotizador.gestionar-cotizacion', $cotizacion->id) }}" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="estado" value="rechazado">
                <div class="form-group form-row">
                    <label for="observacion" class="col-auto col-form-label text-right">Observación</label>
                    <div class="col">
                        <textarea class="form-control" name="observacion" id="observacion" cols="30" rows="2" required></textarea>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 ml-auto text-center">
                        <button type="submit" form="rechazo-cotizacion" class="btn btn-danger btn-lg px-5">Rechazar Cotización</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>


<!-- MODAL APROBACION -->
<div class="modal fade" id="modal-aprobar-cotizacion">
    <div class="modal-dialog modal-lg " style="width:60%">
        <div class="modal-content modal-confirmacion">
            <div class="modal-header text-center">
                <div class="title">Confirmar Aprobación de Cotización </div>
            </div>
            <div class="modal-body">
                <h5>Cotización N°: {{$cotizacion->id}}</h5>
                <h5>Monto Total (MUSD): <span id="monto-total-modal"></span></h5>
                <br>
                <h6>Una vez confirmado se aprobara la cotización correspondiente.
                </h6>
                <div class=" mt-4 text-center">
                    <button class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" form="aprobacion-cotizacion" data-id="" class="btn btn-success mx-2">Continuar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif