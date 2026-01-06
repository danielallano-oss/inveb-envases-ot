<div class="modal-body">
    <div class="row">
        <!-- Seleccionar Asignacion -->
        <div class="col-12">
            @if(!$asignarDirecto)
            <div class="row">
                <div class="col-12 row">
                    @if($profesionalActual)
                    <div class="col-6">
                        <div class="form-group">
                            <label>Profesional Asignado Actualmente</label>
                            <div class="col-12"><span class="">{{$profesionalActual->fullname}}</span></div>
                        </div>
                    </div>
                    @endif
                    <div class="col-6">
                        <div class="form-group">
                            <label>Profesional a Asignar</label>
                            <select name="profesional_id[]" id="profesional_id" class="form-control form-control-sm" data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
                                {!! optionsSelectObjetfilterSimple($profesionales,'id',['nombre','apellido'],' ',false) !!}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-12 text-center form-group">
                    <label>Confirma la Asignación de Órden de Trabajo para continuar</label>
                </div>
            </div>
            <input hidden id="profesional_id" value="{{Auth()->user()->id}}">
            @endif

            <input hidden id="ot_id" value="{{$ot_id}}">

            <div class=" mt-4 text-center">
                <button id="asignacion" class="btn btn-success mx-2">Asignar</button>
                <button class="btn btn-light" data-dismiss="modal">Cancelar</button>
            </div>
        </div>

    </div>

</div>
</div>