<div class="form-row">
    <!-- Solo si es creacion por ingeniero -->
    <div id="ot-datos-comerciales" class="col-12 mb-2">
        <div class="card">
            <div class="card-header">
                Datos comerciales
            </div>
            <div class="card-body">
                <div class="row">
                    @if ($errors->any())
                    @endif

                    <!-- Solo se permite seleccionar al crearlo, luego no puede ser modificado -->
                    @if ($tipo == 'create')
                        <div class="col-4">

                            {!! armarSelectArrayCreateEditOT(
                                $clients,
                                'client_id',
                                'Cliente',
                                $errors,
                                $ot,
                                'form-control form-element',
                                true,
                                true,
                            ) !!}
                        </div>

                        <!-- Validacion para que pueda editar el super administrador -->
                    @elseif(Auth()->user()->isSuperAdministrador())
                        <div class="col-4">
                            <!-- Cliente -->
                            {!! armarSelectArrayCreateEditOT($clients, 'client_id', 'Cliente', $errors, $ot, 'form-control', true, true) !!}
                        </div>
                    @else
                        <div class="col-4">
                            <!-- Cliente -->
                            {!! inputReadOnly('Cliente', $ot->client->nombre) !!}
                        </div>
                    @endif
                    <div class="col-4">
                        <!-- Descripción -->
                        {!! armarInputCreateEditOT(
                            'descripcion',
                            'Descripción:',
                            'text',
                            $errors,
                            $ot,
                            'form-control',
                            'maxlength="40"',
                            '',
                        ) !!}
                    </div>
                    <div class="col-4">
                        <!-- Código Producto -->
                        {!! armarInputCreateEditOT('codigo_producto', 'Código Producto:', 'text', $errors, $ot, 'form-control', '', '') !!}
                    </div>
                    <div class="col-4">
                        <!-- Solo se permite seleccionar al crearlo, luego no puede ser modificado -->
                        @if ($tipo == 'create')
                            <!-- Tipo de Solicitud -->
                            {!! armarSelectArrayCreateEditOT(
                                $tipos_solicitud,
                                'tipo_solicitud',
                                'Tipo de solicitud:',
                                $errors,
                                $ot,
                                'form-control',
                                true,
                                false,
                            ) !!}
                            {!! armarSelectArrayCreateEditOT(
                                $ajustes_area_desarrollo,
                                'ajuste_area_desarrollo',
                                'Tipo de Ajuste Area Desarrollo:',
                                $errors,
                                $ot,
                                'form-control',
                                true,
                                false,
                            ) !!}
                            {!! armarSelectArrayCreateEditOT(
                                [],
                                'instalacion_cliente',
                                'Instalación Cliente',
                                $errors,
                                $ot,
                                'form-control form-element',
                                true,
                                true,
                            ) !!}
                            <!-- Contactos Cliente -->
                            <!-- //style="display:none" -->
                            {!! armarSelectArrayCreateEditOT(
                                [],
                                'contactos_cliente',
                                'Contactos Cliente',
                                $errors,
                                $ot,
                                'form-control form-element',
                                true,
                                true,
                            ) !!}
                        @elseif(Auth()->user()->isSuperAdministrador())
                            <!-- Tipo de Solicitud -->
                            {!! armarSelectArrayCreateEditOT(
                                $tipos_solicitud,
                                'tipo_solicitud',
                                'Tipo de solicitud:',
                                $errors,
                                $ot,
                                'form-control',
                                true,
                                false,
                            ) !!}
                            {!! armarSelectArrayCreateEditOT(
                                $ajustes_area_desarrollo,
                                'ajuste_area_desarrollo',
                                'Tipo de Ajuste Area Desarrollo:',
                                $errors,
                                $ot,
                                'form-control',
                                true,
                                false,
                            ) !!}
                            <!-- Contactos Cliente -->
                            <!-- //style="display:none" -->
                            {!! armarSelectArrayCreateEditOT(
                                [],
                                'instalacion_cliente',
                                'Instalación Cliente',
                                $errors,
                                $ot,
                                'form-control form-element',
                                true,
                                true,
                            ) !!}
                            {!! armarSelectArrayCreateEditOT(
                                [],
                                'contactos_cliente',
                                'Contactos Cliente',
                                $errors,
                                $ot,
                                'form-control form-element',
                                true,
                                true,
                            ) !!}
                        @else
                            <!-- Tipo de Solicitud -->
                            {!! inputReadOnly(
                                'Tipo de Solicitud',
                                [
                                    1 => 'Desarrollo Completo',
                                    4 => 'Cotiza sin CAD',
                                    2 => 'Cotiza con CAD',
                                    3 => 'Muestra con CAD',
                                    5 => 'Arte con Material',
                                    6 => 'Otras Solicitudes Desarrollo',
                                ][$ot->tipo_solicitud],
                            ) !!}
                            {!! inputReadOnly(
                                'Tipo de Ajuste Area Desarrollo',
                                [1 => 'Licitación', 2 => 'Ficha Técnica', 3 => 'Estudio Benchmarking'][$ot->ajuste_area_desarrollo],
                            ) !!}
                            <input type="hidden" id="tipo_solicitud_2" value="{{ $ot->tipo_solicitud }}">
                            @if (is_null($ot->instalacion_cliente))
                                {!! inputReadOnly('Instalacion Cliente', 'N/A') !!}
                            @else
                                {!! inputReadOnly('Instalacion Cliente', $ot->installation->nombre) !!}
                            @endif
                        @endif
                        <!-- Nombre Contacto -->
                        {!! armarInputCreateEditOT('nombre_contacto', 'Nombre Contacto:', 'text', $errors, $ot, 'form-control', '', '') !!}
                        <!-- Email Contacto -->
                        {!! armarInputCreateEditOT('email_contacto', 'Email Contacto:', 'email', $errors, $ot, 'form-control', '', '') !!}
                        <!-- Teléfono Contacto -->
                        {!! armarInputCreateEditOT(
                            'telefono_contacto',
                            'Teléfono Contacto:',
                            'text',
                            $errors,
                            $ot,
                            'form-control',
                            '',
                            '',
                        ) !!}

                    </div>
                    <div class="col-4">
                        <!-- Canal -->
                        {!! armarSelectArrayCreateEditOT($canals, 'canal_id', 'Canal', $errors, $ot, 'form-control', true, false) !!}
                    </div>
                    <div class="col-4">

                        {!! armarSelectArrayCreateEditOT(
                            $hierarchies,
                            'hierarchy_id',
                            'Jerarquía 1',
                            $errors,
                            $ot,
                            'form-control',
                            true,
                            true,
                        ) !!}
                        <!-- Jerarquía 2-->
                        {!! armarSelectArrayCreateEditOT(
                            $subhierarchies,
                            'subhierarchy_id',
                            'Jerarquía 2',
                            $errors,
                            $ot,
                            'form-control',
                            true,
                            true,
                        ) !!}
                        <!-- Jerarquía 3-->
                        {!! armarSelectArrayCreateEditOT(
                            $subsubhierarchies,
                            'subsubhierarchy_id',
                            'Jerarquía 3',
                            $errors,
                            $ot,
                            'form-control',
                            true,
                            true,
                        ) !!}


                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="ot-licitacion" class="col-6 mb-2">
        <div class="card">
            <div class="card-header">
                Datos de Licitacion
            </div>
            <div class="card-body">
                <div class="form-group form-row">
                    <div class="col-5">
                        <!-- Cantidad de Items -->
                        {!! armarInputCreateEditOT(
                            'cantidad_item_licitacion',
                            'Cantidad Items:',
                            'number',
                            $errors,
                            $ot,
                            'form-control',
                            'min="1"',
                            '',
                        ) !!}
                    </div>
                    <div class="col-1">
                        &nbsp;
                    </div>
                    <div class="col-6">
                        <!-- Fecha Maxima Entrega -->
                        {!! armarInputCreateEditOT(
                            'fecha_maxima_entrega_licitacion',
                            'Fecha Maxima Entrega:',
                            'date',
                            $errors,
                            $ot,
                            'form-control',
                            'min="1"',
                            '',
                        ) !!}
                    </div>
                </div>
                @if (Auth()->user()->role_id == 6 && $tipo == 'edit')
                    <div class="form-group form-row">
                        <div class="col-5">
                            <div class="form-group form-row" id="div_cantidad_muestras_entregadas">
                                <label class="col-auto col-form-label">Cantidad Muestras Entregadas :</label>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="custom-control custom-checkbox mb-1">
                                <input disabled="disabled" type="checkbox" class="custom-control-input" value="check_entregadas_todas"
                                    id="check_entregadas_todas" name="checkboxes[]"
                                    @if ((!old('_token') && $tipo == 'edit' && $ot->check_entregadas_todas == 1) || old('check_entregadas_todas')) checked @endif>
                                <label class="custom-control-label" for="check_entregadas_todas">Todas</label>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="custom-control custom-checkbox">
                                <input disabled="disabled" type="checkbox" class="custom-control-input" value="check_entregadas_algunas"
                                    id="check_entregadas_algunas" name="checkboxes[]"
                                    @if ((!old('_token') && $tipo == 'edit' && $ot->check_entregadas_algunas == 1) || old('check_entregadas_algunas')) checked @endif>
                                <label class="custom-control-label" for="check_entregadas_algunas">Algunas</label>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group form-row" id="div_cantidad_entregadas_algunas">
                                <label class="col-auto col-form-label"> Cantidad:</label>
                                <input disabled="disabled" type="number" class="form-control" id="cantidad_entregadas_algunas"
                                    name="cantidad_entregadas_algunas" style="width: 70px;"
                                    @if (!old('_token') && $tipo == 'edit' && !is_null($ot->cantidad_entregadas_algunas)) value="{{ $ot->cantidad_entregadas_algunas }}" disabled="false" @else value="" disabled="true" @endif>
                            </div>
                        </div>
                    </div>
                @elseif(Auth()->user()->role_id != 6 && $tipo == 'create')

                <div class="form-group form-row">
                        <div class="col-5">
                            <div class="form-group form-row" id="div_cantidad_muestras_entregadas">
                                <label class="col-auto col-form-label">Cantidad Muestras Entregadas :</label>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="custom-control custom-checkbox mb-1">
                                <input type="checkbox" class="custom-control-input" value="check_entregadas_todas"
                                    id="check_entregadas_todas" name="checkboxes[]"
                                    @if ((!old('_token') && $tipo == 'edit' && $ot->check_entregadas_todas == 1) || old('check_entregadas_todas')) checked @endif>
                                <label class="custom-control-label" for="check_entregadas_todas">Todas</label>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" value="check_entregadas_algunas"
                                    id="check_entregadas_algunas" name="checkboxes[]"
                                    @if ((!old('_token') && $tipo == 'edit' && $ot->check_entregadas_algunas == 1) || old('check_entregadas_algunas')) checked @endif>
                                <label class="custom-control-label" for="check_entregadas_algunas">Algunas</label>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group form-row" id="div_cantidad_entregadas_algunas">
                                <label class="col-auto col-form-label"> Cantidad:</label>
                                <input type="number" class="form-control" id="cantidad_entregadas_algunas"
                                    name="cantidad_entregadas_algunas" style="width: 70px;"
                                    @if (!old('_token') && $tipo == 'edit' && !is_null($ot->cantidad_entregadas_algunas)) value="{{ $ot->cantidad_entregadas_algunas }}" disabled="false" @else value="" disabled="true" @endif>
                            </div>
                        </div>
                    </div>
                @endif


                {{-- @if(auth()->user()->role_id == 6)

                @if ($tipo == 'create')
                    <div class="form-group form-row">
                        <div class="col-9">
                            <div id="subida_archivo" class="form-group form-row">
                                <label class="col-auto col-form-label text-right">Archivo: </label>
                                <input type="file" class="input" id="licitacion_file" name="licitacion_file">

                            </div>
                        </div>

                    </div>
                    <div class="form-group form-row">
                         <div class="col-6">
                            {!! armarInputCreateEditOT(
                            'validacion_excel',
                            'Validacion Archivo:',
                            'text',
                            $errors,
                            $ot,
                            'form-control',
                            'readonly',
                            '',
                        ) !!}
                        </div>
                        <div class="col-3">
                            <button type="button" class="btn btn-secondary" onclick="validarArchivo()">Validar
                                Excel</button>
                        </div>
                    </div>
                @endif
                @endif --}}

            </div>
        </div>
    </div>

    <div id="ot-sentido-onda" class="col-6 mb-2">
        <div class="card">
            <div class="card-header">
                Observación
            </div>
            <div class="card-body">
                <div class="form-group form-row">
                    <div class="col-12">
                        <textarea class="{{ $errors->has('observacion') ? 'error' : '' }}" style="resize: none;border-color:#3aaa35"
                            name="observacion" id="observacion" cols="70" rows="3">
@if (old('observacion'))
{{ old('observacion') }}
@elseif(isset($ot->observacion) && !old('_token') && $tipo == 'edit')
{{ $ot->observacion }}
@endif
</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<input type="hidden" id="role_id" name="role_id" value="{{ auth()->user()->role_id }}">
<input type="hidden" id="detalle_id" name="detalle_id" value="">
<input type="hidden" id="muestra_id" name="muestra_id" value="">
<input type="hidden" id="check" name="check" value="">
<input type="hidden" id="tipo" name="tipo" value="{{ $tipo }}">

<!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<script>


document.querySelector('input[type="file"]').addEventListener('change', function () {
    // Restaurar valores predeterminados al cambiar archivo
    $('#validacion_excel').val('');
    // $('#btnGuardarOT').prop('disabled', true);

    // (Opcional) Limpiar mensajes anteriores si tienes algún label visible
    // $('#lbl_validacion').text('');
});

    function validarArchivo() {
        const input = document.querySelector('input[type="file"]');
        const archivo = input.files[0];

        if (!archivo) {
            toastr.warning("Debe seleccionar un archivo para validar.");
            return;
        }

        const formData = new FormData();
        formData.append('archivo', archivo);

        fetch('{{ route('validarExcel') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.validado) {
                    toastr.success("Archivo válido. Puedes guardar la OT.");
                    // $('#lbl_validacion').text('Archivo Validado');
                    $('#validacion_excel').val('Validado');
                } else {
                    data.errores.forEach(error => {
                        toastr.error(error.mensaje);
                    });
                    $('#validacion_excel').val('');
                }
            })
            .catch(err => {
                console.error(err);
                toastr.error("Error al validar el archivo.");
            });
    }
</script>
