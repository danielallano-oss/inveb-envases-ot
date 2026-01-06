<section id="ficha" class="py-3">
    <div class="form-row">
        <div id="ot-datos-comerciales" class="col-8 mb-2">
            <div class="card h-100">
                <div class="card-header">Datos comerciales</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <!-- Vendedor -->
                            {!! inputReadOnly('Vendedor', $ot->creador->fullname) !!}

                            <!-- Tipo de Solicitud -->
                            {!! inputReadOnly(
                                'Tipo de Solicitud',
                                [
                                    1 => 'Desarrollo Completo',
                                    2 => 'Cotiza con CAD',
                                    3 => 'Muestra con CAD',
                                    4 => 'Cotiza sin CAD',
                                    5 => 'Arte con Material',
                                    6 => 'Otras Solicitudes Desarrollo',
                                    7 => 'Proyecto Innovacion',
                                ][$ot->tipo_solicitud],
                            ) !!}
                            @if ($ot->tipo_solicitud != 6)
                                <div class="form-group row">
                                    <div class="col-6">
                                        <!-- Vol Vta. Anual -->
                                        {!! inputReadOnly('Vol Vta. Anual', $ot->volumen_venta_anual, 'volumen_venta_anual') !!}

                                    </div>
                                    <div class="col-6">
                                        <!-- USD -->
                                        {!! inputReadOnly('USD', $ot->usd, 'usd') !!}

                                    </div>
                                </div>

                                <!-- OC -->
                                {!! inputReadOnly('OC', !is_null($ot->oc) ? [1 => 'Si', 0 => 'No'][$ot->oc] : null) !!}
                            @else
                                <!-- Tipo de Solicitud -->
                                {!! inputReadOnly(
                                    'Tipo de Ajuste de Desarrollo',
                                    [1 => 'Licitacion', 2 => 'Ficha Tecnica', 3 => 'Estudio Benchmarking'][$ot->ajuste_area_desarrollo],
                                ) !!}
                            @endif

                        </div>

                        <div class="col-6">
                            <!-- Descripcion -->
                            {!! inputReadOnly('Descripción', $ot->descripcion ? $ot->descripcion : null) !!}
                            @if ($ot->tipo_solicitud != 6)
                                <!-- Material -->
                                {!! inputReadOnly('Material', isset($ot->material) ? $ot->material->codigo : null) !!}
                                <!-- CAD -->
                                {!! inputReadOnly('CAD', $ot->cad ? $ot->cad : null) !!}
                                <!-- Carton -->
                                {!! inputReadOnly('Cartón', $ot->carton ? $ot->carton->codigo : null) !!}
                            @else
                                @if ($ot->ajuste_area_desarrollo == 1)
                                    <!-- CAD -->
                                    {!! inputReadOnly('Cantidad Items', $ot->cantidad_item_licitacion ? $ot->cantidad_item_licitacion : null) !!}
                                    <!-- CAD -->
                                    {!! inputReadOnly(
                                        'Fecha Maxima Entrega',
                                        $ot->fecha_maxima_entrega_licitacion ? date('d/m/Y', strtotime($ot->fecha_maxima_entrega_licitacion)) : null,
                                    ) !!}
                                @endif
                                @if ($ot->ajuste_area_desarrollo == 2)

                                    @if ($ot->check_ficha_simple == 1)
                                        {!! inputReadOnly('Tipo Ficha', 'Simple') !!}
                                    @else
                                        @if ($ot->check_ficha_doble == 1)
                                            {!! inputReadOnly('Tipo Ficha', 'Completa') !!}
                                        @else
                                            {!! inputReadOnly('Tipo Ficha', null) !!}
                                        @endif
                                    @endif

                                    {!! inputReadOnly(
                                        'Fecha Maxima Entrega',
                                        $ot->fecha_maxima_entrega_ficha ? date('d/m/Y', strtotime($ot->fecha_maxima_entrega_ficha)) : null,
                                    ) !!}
                                @endif
                                @if ($ot->ajuste_area_desarrollo == 3)
                                    <!-- CAD -->
                                    {!! inputReadOnly('Cantidad', $ot->cantidad_estudio_bench ? $ot->cantidad_estudio_bench : null) !!}
                                    <!-- CAD -->
                                    {!! inputReadOnly(
                                        'Fecha Maxima Entrega',
                                        $ot->fecha_maxima_entrega_estudio ? date('d/m/Y', strtotime($ot->fecha_maxima_entrega_estudio)) : null,
                                    ) !!}
                                @endif
                            @endif
                        </div>

                        <!-- <div class="col-6"> -->
                        <!-- Canal -->
                        <!-- {!! inputReadOnly('Canal', $ot->canal ? $ot->canal->nombre : 'N/A') !!} -->

                        <!-- Jerarquia 1 -->
                        <!-- {!! inputReadOnly(
                            'Jerarquia 1',
                            $ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->hierarchy->descripcion : 'N/A',
                        ) !!} -->
                        <!-- Jerarquia 2 -->
                        <!-- {!! inputReadOnly('Jerarquia 2', $ot->subsubhierarchy ? $ot->subsubhierarchy->subhierarchy->descripcion : 'N/A') !!} -->
                        <!-- Jerarquia 3 -->
                        <!-- {!! inputReadOnly('Jerarquia 3', $ot->subsubhierarchy ? $ot->subsubhierarchy->descripcion : 'N/A') !!} -->
                        <!-- </div> -->

                    </div>
                </div>
            </div>
        </div>

        <div id="ot-datos-cliente" class="col-4 mb-2">
            <div class="card h-100">
                <div class="card-header">Contacto cliente&nbsp;&nbsp;&nbsp;&nbsp;
                    @if (Auth()->user()->isJefeDesarrollo() ||
                            Auth()->user()->isIngeniero() ||
                            Auth()->user()->isJefeCatalogador() ||
                            Auth()->user()->isCatalogador() ||
                            Auth()->user()->isJefeDiseño() ||
                            Auth()->user()->isDiseñador() ||
                            Auth()->user()->isJefePrecatalogador() ||
                            Auth()->user()->isPrecatalogador())
                        @if (count($indicaciones_especiales) > 0)
                            <button type="button" class="btn btn-outline-primary" data-toggle="modal"
                                data-target="#modal-indicaciones-especiales" data-editar={{ $ot->client_id }}>
                                Indicaciones Especiales Cliente
                            </button>
                        @endif
                    @endif
                </div>
                <div class="card-body">

                    <!-- Cliente -->
                    {!! inputReadOnly('Cliente', $ot->client->nombreSap) !!}

                    <!-- Nombre Contacto -->
                    {!! inputReadOnly('Nombre Contacto', $ot->nombre_contacto) !!}


                    <!-- Email Contacto -->
                    {!! inputReadOnly('Email Contacto', $ot->email_contacto) !!}

                    <!-- Teléfono Contacto -->
                    {!! inputReadOnly('Teléfono Contacto', $ot->telefono_contacto) !!}
                    @if (Auth()->user()->isVendedorExterno())
                        {!! inputReadOnly('Datos Cliente Edipac', $ot->dato_sub_cliente ? $ot->dato_sub_cliente : null) !!}
                    @endif

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!--para  Estado visto bueno cliente los diseñadores o diseñadores estructurales asignados pueden retomar la OT automaticamente  -->
        <!-- segundo caso es para ots que esten en ventas y ya tengan codigo sap final pueden ser retomadas por catalogadores al area de catalogacion -->
        <div class="col-3">
            @if (
                (isset(auth()->user()->role->area) &&
                    $usuarioAsignado &&
                    (auth()->user()->isIngeniero() || auth()->user()->isDiseñador()) &&
                    $ot->ultimoCambioEstado->state_id == 16) ||
                    (isset(auth()->user()->role->area) &&
                        $usuarioAsignado &&
                        (auth()->user()->isCatalogador() || auth()->user()->isJefeCatalogador()) &&
                        $ot->current_area_id == 1 &&
                        $ot->codigo_sap_final == 1 &&
                        ($ot->ultimoCambioEstado->state_id != 8 &&
                            $ot->ultimoCambioEstado->state_id != 9 &&
                            $ot->ultimoCambioEstado->state_id != 11 &&
                            $ot->ultimoCambioEstado->state_id != 12)))
                <a class="btn btn-outline-primary mb-4" href="{{ route('retomarOt', $ot->id) }}" style="width:100%">
                    Retomar OT
                </a>
            @endif
        </div>
        <div class="col-9 text-right">
            @if (($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 7) && isset($ot->material_id) && isset($ot->cad_id))
                <h6 class="title" style="display: inline-block; margin-right:20px">Creación exitosa de Material y Cad
                </h6>
            @endif
            <!-- Si Esta en catalogacion, es desarrollo completo o arte con Material y tiene material creado -->
            @if (
                ($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 7 || $ot->tipo_solicitud == 5) &&
                    $ot->current_area_id == 5 &&
                    $ot->material_id != null &&
                    $usuarioAsignado &&
                    (Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador()))
                <a href="#" id="{{ $ot->id }}" data-toggle="modal" data-target="#modal-codigo-material"
                    class="btn btn-outline-primary modalCad">Código SAP Final</a>
            @endif
            <!-- Si tipo de solicitud = 1 , el usuario es Dibujante Técnico y esta en el area de desarrollo puede crear material y cad -->
            @if (
                ($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 7) &&
                    (Auth()->user()->isJefeDesarrollo() || Auth()->user()->isIngeniero()) &&
                    $ot->current_area_id == 2 &&
                    $ot->material_id == null &&
                    $usuarioAsignado)
                <a href="#" id="{{ $ot->id }}" data-toggle="modal" data-target="#modal-cad"
                    class="btn btn-outline-primary modalCad"> Crear CAD y Material</a>
            @endif

            @if (!auth()->user()->isVendedorExterno())

                @if (auth()->user()->isSuperAdministrador() ||
                        auth()->user()->isCatalogador() ||
                        auth()->user()->isPrecatalogador() ||
                        auth()->user()->isJefeCatalogador() ||
                        auth()->user()->isJefePrecatalogador())
                    <a href="{{ route('descargarExcelSap', $ot->id) }}" class="btn btn-outline-primary">Excel SAP</a>
                    <a href="{{ route('descargarExcelSapSemielaborado', $ot->id) }}"
                        class="btn btn-outline-primary">Excel SAP Semielaborado</a>
                @endif
                <!--Si el producto es "Cajas" = 3-->
                @if (isset($ot->product_type_id) &&
                        $ot->product_type_id != 21 &&
                        ($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 5 || $ot->tipo_solicitud == 7))
                    <a href="{{ route('nuevaOtExcel', $ot->id) }}" class="btn btn-outline-primary"> Formulario Excel
                        Cartón</a>

                    <!--Si el producto es "Esquineros" = 21-->
                @elseif(isset($ot->product_type_id) &&
                        $ot->product_type_id == 21 &&
                        ($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 5 || $ot->tipo_solicitud == 7))
                    <a href="{{ route('nuevaOtExcel', $ot->id) }}" class="btn btn-outline-primary"> Formulario Excel
                        Esquinero</a>
                @endif
            @endif
            <!-- Boton para que solo pueda editar el super admistrador -->
            @if (Auth()->user()->isSuperAdministrador())
                <a class="btn btn-outline-primary" href="{{ route('editOt', $ot->id) }}">
                    Editar OT <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
                </a>
            @endif

            <!-- // Para gerente y admin -->


            <!-- Solo mostrar boton de editar para usuarios q no sean admin/gerente, que esten asignados a la OT y que pertenescan al area asignada a la ot actualmente -->
            @if (
                ((isset(auth()->user()->role->area) &&
                    !auth()->user()->isVendedor() &&
                    !auth()->user()->isJefeVenta() &&
                    !auth()->user()->isVendedorExterno() &&
                    $usuarioAsignado &&
                    $ot->current_area_id == auth()->user()->role->area->id) ||
                    ((auth()->user()->isVendedor() || auth()->user()->isJefeVenta() || auth()->user()->isVendedorExterno()) &&
                        count($ot->users) < 2) ||
                    (isset(auth()->user()->role->area) &&
                        auth()->user()->role->area->id == 4 &&
                        $usuarioAsignado &&
                        ($ot->current_area_id == 4 || $ot->current_area_id == 5) &&
                        ($ot->ultimoCambioEstado->state_id != 9 && $ot->ultimoCambioEstado->state_id != 11)) ||
                    auth()->user()->isJefeDiseño()) &&
                    !auth()->user()->isPrecatalogador() &&
                    !auth()->user()->isCatalogador())


                @if ($ot->tipo_solicitud != 6)

                    <a class="btn btn-outline-primary" href="{{ route('editOt', $ot->id) }}">
                        Ver/Editar OT <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
                    </a>

                    @if (!auth()->user()->isVendedor() && !auth()->user()->isJefeVenta())
                        @if (
                            ($ot->ultimoCambioEstado->state->nombre == 'Proceso de Ventas' ||
                                $ot->ultimoCambioEstado->state->nombre == 'Visto Bueno Cliente') &&
                                ($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 7 || $ot->tipo_solicitud == 5))
                            <a class="btn btn-outline-primary"
                                href="{{ route('editDescriptionOt', [$ot->id, 'description']) }}">
                                Ver/Editar Descripción <div class="material-icons md-14" data-toggle="tooltip"
                                    title="Editar">edit</div>
                            </a>
                        @endif

                    @endif
                @else
                    @if ($ot->ajuste_area_desarrollo == 1)
                        <a class="btn btn-outline-primary" href="{{ route('editOtLicitacion', $ot->id) }}">
                            Ver/Editar OT <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit
                            </div>
                        </a>
                       
                    @endif
                    @if ($ot->ajuste_area_desarrollo == 2)
                        <a class="btn btn-outline-primary" href="{{ route('editOtFicha', $ot->id) }}">
                            Ver/Editar OT <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit
                            </div>
                        </a>
                    @endif
                    @if ($ot->ajuste_area_desarrollo == 3)
                        <a class="btn btn-outline-primary" href="{{ route('editOtEstudioBench', $ot->id) }}">
                            Ver/Editar OT <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit
                            </div>
                        </a>
                        @if (auth()->user()->isIngeniero() || auth()->user()->isJefeDesarrollo())
                            <a class="btn btn-outline-primary" href="{{ route('generarPdfEstudioBench', $ot->id) }}"
                                target="_blank">
                                PDF Estudio <div class="material-icons md-14" data-toggle="tooltip" title="Editar">print
                                </div>
                            </a>
                        @endif
                    @endif
                @endif
                <!-- Solo puede editar la descripcion el jefe de venta y el vendedor al cual esta asignada la ot y mientras el estado sea Proceso de Ventas  -->
            @elseif(
                ((auth()->user()->isVendedor() || auth()->user()->isVendedorExterno()) &&
                    $ot->vendedorAsignado->user->id == auth()->user()->id) ||
                    (auth()->user()->isJefeVenta() && $ot->current_area_id == auth()->user()->role->area->id))
                @if ($ot->tipo_solicitud != 6)
                    @if (
                        ($ot->ultimoCambioEstado->state->nombre == 'Proceso de Ventas' ||
                            $ot->ultimoCambioEstado->state->nombre == 'Visto Bueno Cliente') &&
                            ($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 7 || $ot->tipo_solicitud == 5))
                        <a class="btn btn-outline-primary"
                            href="{{ route('editDescriptionOt', [$ot->id, 'description']) }}">
                            Ver/Editar Descripción <div class="material-icons md-14" data-toggle="tooltip"
                                title="Editar">edit</div>
                        </a>
                    @endif

                    <!-- Solo puede editar la orden de compra el jefe de venta y el vendedor al cual esta asignada la ot si no ha pasado por pre catalogacion  -->
                    @if ($validation_edition == false)
                        <a class="btn btn-outline-primary"
                            href="{{ route('editDescriptionOt', [$ot->id, 'orden_compra']) }}">
                            Ver/Editar Orden Compra <div class="material-icons md-14" data-toggle="tooltip"
                                title="Editar">edit</div>
                        </a>
                    @endif

                    {{-- @if (($ot->ultimoCambioEstado->state->nombre == 'Proceso de Ventas' || $ot->ultimoCambioEstado->state->nombre == 'Visto Bueno Cliente') && ($ot->tipo_solicitud == 1 || $ot->tipo_solicitud == 7 || $ot->tipo_solicitud == 5))
						<a class="btn btn-outline-primary" href="{{route('editDescriptionOt',array($ot->id,'description'))}}">
							Ver/Editar Descripción <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
						</a>
					@endif --}}
                @else
                    @if ($ot->ajuste_area_desarrollo == 1)
                        @if ((auth()->user()->isVendedor() || auth()->user()->isJefeVenta()) && $ot->current_area_id == 1)
                            <a class="btn btn-outline-primary" href="{{ route('editOtLicitacion', $ot->id) }}">
                                Ver/Editar OT <div class="material-icons md-14" data-toggle="tooltip" title="Editar">
                                    edit</div>
                            </a>
                        @else
                            <a href="#" class="btn btn-outline-primary modalVerOtLicitacion"
                                id="{{ $ot->id }}" data-toggle="modal"
                                data-target="#modal-ver-ot-licitacion">Ver OT</a>
                        @endif
                    @endif
                    @if ($ot->ajuste_area_desarrollo == 2)
                        @if ((auth()->user()->isVendedor() || auth()->user()->isJefeVenta()) && $ot->current_area_id == 1)
                            <a class="btn btn-outline-primary" href="{{ route('editOtFicha', $ot->id) }}">
                                Ver/Editar OT <div class="material-icons md-14" data-toggle="tooltip" title="Editar">
                                    edit</div>
                            </a>
                        @else
                            <a href="#" class="btn btn-outline-primary modalVerOtFichaTecnica"
                                id="{{ $ot->id }}" data-toggle="modal"
                                data-target="#modal-ver-ot-ficha-tecnica">Ver OT</a>
                        @endif
                    @endif
                    @if ($ot->ajuste_area_desarrollo == 3)
                        @if ((auth()->user()->isVendedor() || auth()->user()->isJefeVenta()) && $ot->current_area_id == 1)
                            <a class="btn btn-outline-primary" href="{{ route('editOtEstudioBench', $ot->id) }}">
                                Ver/Editar OT <div class="material-icons md-14" data-toggle="tooltip" title="Editar">
                                    edit</div>
                            </a>
                        @else
                            <a href="#" class="btn btn-outline-primary modalVerOtEstudio"
                                id="{{ $ot->id }}" data-toggle="modal" data-target="#modal-ver-ot-estudio">Ver
                                OT</a>
                        @endif
                    @endif
                @endif
            @else
                @if ($ot->tipo_solicitud != 6)
                    <a href="#" class="btn btn-outline-primary modalVerOt" id="{{ $ot->id }}"
                        data-toggle="modal" data-target="#modal-ver-ot">Ver OT </a>
                    @php
                    @endphp
                @else
                    @if ($ot->ajuste_area_desarrollo == 1)
                        <a href="#" class="btn btn-outline-primary modalVerOtLicitacion"
                            id="{{ $ot->id }}" data-toggle="modal" data-target="#modal-ver-ot-licitacion">Ver
                            OT</a>
                    @endif
                    @if ($ot->ajuste_area_desarrollo == 2)
                        <a href="#" class="btn btn-outline-primary modalVerOtFichaTecnica"
                            id="{{ $ot->id }}" data-toggle="modal"
                            data-target="#modal-ver-ot-ficha-tecnica">Ver OT</a>
                    @endif
                    @if ($ot->ajuste_area_desarrollo == 3)
                        <a href="#" class="btn btn-outline-primary modalVerOtEstudio" id="{{ $ot->id }}"
                            data-toggle="modal" data-target="#modal-ver-ot-estudio">Ver OT</a>
                    @endif
                @endif
            @endif
        </div>
    </div>
</section>
<!-- MODA CREACION CAD Y MATERIAL -->

<div class="modal fade" id="modal-cad">
    <div class="modal-dialog modal-lg " style="width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <div class="title">Creación de CAD y Material</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-cad-material" method="POST" action="{{ route('createCadMaterial', $ot->id) }}"
                    class="form-row form-cad-material">
                    @method('PUT')
                    @csrf
                    <div class="container-cad" style="width:100%">
                        @if ($ot->cad_id == null)
                            <div class="item-cad" id="cad_input_container">
                                <!-- CAD:-->
                                {!! armarInputCreateEditOT('cad', 'CAD:', 'text', $errors, $ot, 'form-control', '', '') !!}
                            </div>
                        @else
                            <div class="" id="cad_select_container">
                                <!-- CAD Select -->
                                {!! inputReadOnly('CAD', $ot->cad_asignado->cad) !!}
                                <input type="text" hidden id="cad_id" name="cad_id"
                                    value="{{ $ot->cad_id }}">
                            </div>
                        @endif

                        @if ($ot->material_code == null)
                            <div class="item-cad">

                                <!-- Material:-->
                                {!! armarInputCreateEditOT('material', 'Material:', 'text', $errors, $ot, 'form-control', '', '') !!}

                            </div>
                        @else
                            <div class="item-cad">
                                <!-- Material  -->
                                {!! inputReadOnly('Material', $ot->material_code) !!}
                                <input type="text" hidden id="material" name="material"
                                    value="{{ $ot->material_code }}">
                            </div>
                        @endif
                        <div class="item-cad" id="alargar_descripcion">
                            <!-- Descripción Material:-->
                            {!! armarInputCreateEditOT(
                                'descripcion',
                                'Descripción Material:',
                                'text',
                                $errors,
                                null,
                                'form-control',
                                '',
                                '',
                            ) !!}

                        </div>
                        <div class="item-cad" id="maquila">
                            <!-- Descripción Material:-->
                            {!! armarSelectArrayCreateEditOT(
                                [1 => 'Si', 0 => 'No'],
                                'maquila',
                                'Maquila',
                                $errors,
                                $ot,
                                'form-control',
                                true,
                                false,
                            ) !!}

                        </div>

                    </div>
                    <div class="mt-3 text-right pull-right" style="width: 100%;">
                        <a data-dismiss="modal" class="btn btn-light" style="cursor: pointer;">Cancelar</a>
                        <a href="#" data-toggle="modal" data-target="#modal-cad-prompt"
                            class="btn btn-success">Guardar</a>

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-cad-prompt">
    <div class="modal-dialog modal-lg " style="width:60%">
        <div class="modal-content modal-confirmacion">
            <div class="modal-header text-center">
                <div class="title">Confirmar Creación</div>
            </div>
            <div class="modal-body">
                <h6>Una vez confirmado se creara el CAD y Material correspondiente,
                    esta opción es definitiva y bloquea la edición de la orden de trabajo.
                </h6>
                <div class=" mt-4 text-center">
                    <button class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" form="form-cad-material" id="crearCadMaterial"
                        class="btn btn-success mx-2">Continuar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODA CREACION CODIGO MATERIAL -->

<div class="modal fade" id="modal-codigo-material">
    <div class="modal-dialog modal-xl " style="width:100%">
        <div class="modal-content">
            <div class="modal-header">
                <div class="title">

                    @if ($ot->codigo_sap_final == 0)
                        Creación de Código SAP Final
                    @else
                        Código SAP Final
                    @endif
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if ($ot->codigo_sap_final == 0)
                    <form id="form-codigo-material" method="POST"
                        action="{{ route('createCodigoMaterial', $ot->id) }}" class="form-row form-codigo-material">
                        @method('PUT')
                        @csrf
                        <div class="container-cad" style="width:100%">
                            <div class="item-cad" style="justify-content: space-between;
    align-content: start;">
                                <!-- Material  -->
                                <input type="text" hidden id="codigo_material" name="codigo_material"
                                    value="{{ $ot->material_code }}">
                                <div class="form-group form-row">
                                    <label class="col-auto col-form-label" for="">Codigo Material:</label>
                                    <div class="col" style="width: 100px;">
                                        <input type="text" class="form-control" value="{{ $ot->material_code }}"
                                            readonly="">
                                    </div>
                                </div>
                                {!! armarSelectArrayCreateEditOT(
                                    $sufijos,
                                    'sufijo_id',
                                    'Código SAP Ítem',
                                    $errors,
                                    $sufijos,
                                    'form-control form-element',
                                    true,
                                    true,
                                ) !!}

                                <div class="">
                                    {!! armarSelectArrayCreateEditOT(
                                        $prefijosSimple,
                                        'prefijo_ot',
                                        'Código SAP Planta',
                                        $errors,
                                        $prefijosSimple,
                                        'form-control form-element',
                                        true,
                                        true,
                                    ) !!}
                                    <p style="margin-top: -12px;font-size: 12px;text-align: end;"><em>Asociado a la
                                            OT</em></p>
                                </div>
                                <div class="form-group  form-row">
                                    <label class="col-auto col-form-label">Plantas Adicionales</label>
                                    <div class="col">
                                        <select name="prefijo[]" id="prefijo" class="form-control form-control-sm"
                                            multiple data-live-search="true" title="Seleccionar..."
                                            data-selected-text-format="count > 3">
                                            {!! optionsSelectObjetfilterMultiple($prefijos, 'codigo', ['codigo'], '') !!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @if ($ot->tipo_solicitud == 5)
                                <div class="item-cad" id="alargar_descripcion">
                                    <!-- Descripción Material:-->
                                    {!! armarInputCreateEditOT('descripcion', 'Descripción Material:', 'text', $errors, $ot, 'form-control', '', '') !!}

                                </div>
                            @endif
                        </div>
                        <div class="mt-3 text-right pull-right" style="width: 100%;">
                            <a data-dismiss="modal" class="btn btn-light" style="cursor: pointer;">Cancelar</a>
                            <button type="submit" id="crearCodigoMaterial"
                                class="btn btn-success mx-2">Guardar</button>
                        </div>
                    </form>
                @elseif(isset($ot->material))
                    <div class="row" style="padding:10px 20px">
                        <div class="col-4">
                            <!-- Material Asociado -->
                            {!! inputReadOnly('Material Asociado', $ot->material->codigo) !!}
                        </div>
                        <div class="col-8">
                            <!-- Descripción -->
                            {!! inputReadOnly('Descripción', $ot->descripcion_material) !!}
                        </div>

                        @if (isset($ot->materiales_adicionales) && count($ot->materiales_adicionales) > 0)
                            @foreach ($ot->materiales_adicionales as $material)
                                <div class="col-12">
                                    {!! inputReadOnly('Código Material', $material->codigo) !!}
                                </div>
                            @endforeach

                        @endif
                    </div>
                    <div class="mt-3 text-right">
                        <a href="#" class="btn btn-light" data-dismiss="modal">Cerrar</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-indicaciones-especiales" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="page-title">Indicaciones Especiales Cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="client_indicaciones_view" name="client_indicaciones_view">
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
