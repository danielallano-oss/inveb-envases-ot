$(document).ready(function () {
    const role = $("#role_id").val();

    // traduccion mensajes de jquery validator
    jQuery.extend(jQuery.validator.messages, {
        required: "Campo obligatorio.",
        remote: "Por favor, rellena este campo.",
        email: "Por favor, ingresa una dirección de correo válida",
        url: "Por favor, ingresa una URL válida.",
        date: "Por favor, ingresa una fecha válida.",
        dateISO: "Por favor, ingresa una fecha (ISO) válida.",
        number: "Por favor, ingresa un número entero válido.",
        digits: "Por favor, ingresa sólo dígitos.",
        creditcard: "Por favor, ingresa un número de tarjeta válido.",
        equalTo: "Por favor, ingresa el mismo valor de nuevo.",
        accept: "Por favor, ingresa un valor con una extensión aceptada.",
        maxlength: jQuery.validator.format(
            "Por favor, no escribas más de {0} caracteres."
        ),
        minlength: jQuery.validator.format(
            "Por favor, no escribas menos de {0} caracteres."
        ),
        rangelength: jQuery.validator.format(
            "Por favor, ingresa un valor entre {0} y {1} caracteres."
        ),
        range: jQuery.validator.format(
            "Por favor, ingresa un valor entre {0} y {1}."
        ),
        max: jQuery.validator.format(
            "Por favor, ingresa un valor menor o igual a {0}."
        ),
        min: jQuery.validator.format(
            "Por favor, ingresa un valor mayor o igual a {0}."
        ),
    });

    // Custom rules
    jQuery.validator.addMethod(
        "exactlength",
        function (value, element, param) {
            return this.optional(element) || value.length == param;
        },
        $.validator.format("Por favor, ingresa exactamente {0} caracteres.")
    );
    $.validator.addMethod(
        "telefono",
        function (value, element) {
            return (
                this.optional(element) ||
                /^(\+56)?(\s?)(0?[92])(\s?)[987654321]\d{7}$/.test(value)
            );
        },
        $.validator.format("Formato de Telefono: +56912345678")
    );

    var reglas = {};
    if (role == 9 || role == 10) {
        reglas = {
            pallet_type_id: "required",
            pallet_treatment: "required",
            cajas_por_pallet: "required",
            placas_por_pallet: "required",
            pallet_patron_id: "required",
            patron_zuncho: "required",
            pallet_protection_id: "required",
            pallet_box_quantity_id: "required",
            patron_zuncho_paquete: "required",
            patron_zuncho_bulto: "required",
            paquetes_por_unitizado: "required",
            unitizado_por_pallet: "required",
            pallet_tag_format_id: "required",
            numero_etiquetas: "required",
            pallet_qa_id: "required",
            unidad_medida_bct: "required",
            tipo_camion: "required",
            restriccion_especial: "required",
            horario_recepcion: "required",
            codigo_producto_cliente: "required",
            uso_programa_z: "required",
            etiquetas_dsc: "required",
            orientacion_placa: "required",
            precut_type_id: "required",
            rayado_type_id: "required",
            additional_characteristics_type_id: "required"
        };
    }

    // VALIDACION DE FORMULARIO
    $("#form-excel")
        .submit(function (e) {
            e.preventDefault();
        })
        .validate({
            // Specify validation rules
            rules: reglas,
            // Specify validation error messages
            messages: {},
            errorClass: "error",
            errorPlacement: function (error, element) {
                // si es un select o el error es por campo requerido que no es un checkbox entonces  no mostramos el mensaje de error,
                //   solo se marca en rojo
                if (
                    element.is("select") ||
                    (error.html() == "Campo obligatorio." &&
                        !element.is(":checkbox"))
                ) {
                    return false;
                } else {
                    if (!element.is(":checkbox")) {
                        error.insertAfter(element);
                    } else {
                        error.insertAfter($("#checkbox-card"));
                    }
                }
            },
            highlight: function (element, errorClass) {
                $(element).closest("div.form-group").addClass("error");
            },
            unhighlight: function (element, errorClass) {
                $(element).closest("div.form-group").removeClass("error");
            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid
            submitHandler: function (form) {
                form.submit();
            },
        });

    // FIN VALIDACION

    // Al cambiar un select limpiar errores
    $("select").change(function () {
        $(this).closest("div.form-group").removeClass("error");
    });

    // DESABILITAR CAMPOS SEGUN ROL
    // let role = $("#role_id").val();
    // Area de Venta
    // if (role == 4 || role == 3) {
    //     $(
    //         "#org_venta_id,#largura_hm,#anchura_hm,#area_producto,#area_interior_perimetro,#rmt,#golpes_largo,#golpes_ancho,#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2,#impresion_1,#impresion_2,#impresion_3,#impresion_4,#impresion_5,#impresion_6,#longitud_pegado,#porcentaje_cera_exterior,#porcentaje_cera_interior,#porcentaje_barniz_interior,#tipo_sentido_onda,#material_asignado,#descripcion_material"
    //     ).prop("disabled", true);
    // }

    chargeSelectSecOperacionalPlanta($("#ot_id").val());
    chargeSelectSecOperacionalPlantaAux1($("#ot_id").val());
    chargeSelectSecOperacionalPlantaAux2($("#ot_id").val());
});

function chargeSelectSecOperacionalPlanta(ot_id) {

    $('#sec_ope_ppal_planta_ori_1,#sec_ope_atl_1_planta_ori_1,#sec_ope_atl_2_planta_ori_1,#sec_ope_atl_3_planta_ori_1,#sec_ope_atl_4_planta_ori_1,#sec_ope_atl_5_planta_ori_1,#sec_ope_ppal_planta_ori_2,#sec_ope_atl_1_planta_ori_2,#sec_ope_atl_2_planta_ori_2,#sec_ope_atl_3_planta_ori_2,#sec_ope_atl_4_planta_ori_2,#sec_ope_atl_5_planta_ori_2,#sec_ope_ppal_planta_ori_3,#sec_ope_atl_1_planta_ori_3,#sec_ope_atl_2_planta_ori_3,#sec_ope_atl_3_planta_ori_3,#sec_ope_atl_4_planta_ori_3,#sec_ope_atl_5_planta_ori_3,#sec_ope_ppal_planta_ori_4,#sec_ope_atl_1_planta_ori_4,#sec_ope_atl_2_planta_ori_4,#sec_ope_atl_3_planta_ori_4,#sec_ope_atl_4_planta_ori_4,#sec_ope_atl_5_planta_ori_4,#sec_ope_ppal_planta_ori_5,#sec_ope_atl_1_planta_ori_5,#sec_ope_atl_2_planta_ori_5,#sec_ope_atl_3_planta_ori_5,#sec_ope_atl_4_planta_ori_5,#sec_ope_atl_5_planta_ori_5,#sec_ope_ppal_planta_ori_6,#sec_ope_atl_1_planta_ori_6,#sec_ope_atl_2_planta_ori_6,#sec_ope_atl_3_planta_ori_6,#sec_ope_atl_4_planta_ori_6,#sec_ope_atl_5_planta_ori_6')
        .html("")
        .prop('disabled', false)
        .selectpicker('refresh');

    return $.ajax({
        type: "GET",
        url: "/chargeSelectSecOperacionalPlanta",
        data: "ot_id=" + ot_id,
        success: function (data) {

            if (data.planta === null || typeof data.planta === 'undefined') {
                if ($('#planta_id').val() == 1) {
                    $('#planta_original_sec_ope').val('BUIN').prop('disabled', true);
                    getSecuenciasOperacionalesPlanta(1);
                    $('#sec_ope_planta_orig_id').val(1);


                } else {
                    if ($('#planta_id').val() == 2) {
                        $('#planta_original_sec_ope').val('TILTIL').prop('disabled', true);
                        getSecuenciasOperacionalesPlanta(2);
                        $('#sec_ope_planta_orig_id').val(2);
                    } else {
                        $('#planta_original_sec_ope').val('OSORNO').prop('disabled', true);
                        getSecuenciasOperacionalesPlanta(3);
                        $('#sec_ope_planta_orig_id').val(3);
                    }
                }

            } else {

                if (data.cantidad_filas > 6) {

                    $('#sec_ope_ppal_planta_ori_1,#sec_ope_atl_1_planta_ori_1,#sec_ope_atl_2_planta_ori_1,#sec_ope_atl_3_planta_ori_1,#sec_ope_atl_4_planta_ori_1,#sec_ope_atl_5_planta_ori_1,#sec_ope_ppal_planta_ori_2,#sec_ope_atl_1_planta_ori_2,#sec_ope_atl_2_planta_ori_2,#sec_ope_atl_3_planta_ori_2,#sec_ope_atl_4_planta_ori_2,#sec_ope_atl_5_planta_ori_2,#sec_ope_ppal_planta_ori_3,#sec_ope_atl_1_planta_ori_3,#sec_ope_atl_2_planta_ori_3,#sec_ope_atl_3_planta_ori_3,#sec_ope_atl_4_planta_ori_3,#sec_ope_atl_5_planta_ori_3,#sec_ope_ppal_planta_ori_4,#sec_ope_atl_1_planta_ori_4,#sec_ope_atl_2_planta_ori_4,#sec_ope_atl_3_planta_ori_4,#sec_ope_atl_4_planta_ori_4,#sec_ope_atl_5_planta_ori_4,#sec_ope_ppal_planta_ori_5,#sec_ope_atl_1_planta_ori_5,#sec_ope_atl_2_planta_ori_5,#sec_ope_atl_3_planta_ori_5,#sec_ope_atl_4_planta_ori_5,#sec_ope_atl_5_planta_ori_5,#sec_ope_ppal_planta_ori_6,#sec_ope_atl_1_planta_ori_6,#sec_ope_atl_2_planta_ori_6,#sec_ope_atl_3_planta_ori_6,#sec_ope_atl_4_planta_ori_6,#sec_ope_atl_5_planta_ori_6')
                        .html(data.html)
                        .prop('disabled', false)
                        .selectpicker('refresh');

                    if (data.planta == 1) {
                        $('#planta_original_sec_ope').val('BUIN').prop('disabled', true);
                        $('#sec_ope_planta_orig_id').val(1);
                    } else {
                        if (data.planta == 2) {
                            $('#planta_original_sec_ope').val('TILTIL').prop('disabled', true);
                            $('#sec_ope_planta_orig_id').val(2);
                        } else {
                            $('#planta_original_sec_ope').val('OSORNO').prop('disabled', true);
                            $('#sec_ope_planta_orig_id').val(3);
                        }
                    }

                    for (let i = 7; i <= (data.cantidad_filas); i++) {

                        var fila = '';
                        fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
                        fila += '    <div class="col-4">';
                        fila += '        <div class="form-group form-row">';
                        fila += '            <div class="col">';
                        fila += '                <div id="selector_clon_1_' + i + '"></div>';
                        fila += '            </div>';
                        fila += '        </div>';
                        fila += '    </div>';
                        fila += '    <div class="col-4">';
                        fila += '        <div class="form-group form-row">';
                        fila += '            <div class="col">';
                        fila += '                <div id="selector_clon_2_' + i + '"></div>';
                        fila += '            </div>';
                        fila += '        </div>';
                        fila += '    </div>';
                        fila += '    <div class="col-4">';
                        fila += '        <div class="form-group form-row">';
                        fila += '            <div class="col">';
                        fila += '                <div id="selector_clon_3_' + i + '"></div>';
                        fila += '            </div>';
                        fila += '        </div>';
                        fila += '    </div>';
                        fila += '</div>';
                        fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
                        fila += '    <div class="col-12">';
                        fila += '        &nbsp;';
                        fila += '    </div>';
                        fila += '</div>';

                        $('#fila_planta_original').append(fila);

                        // Clonar el select picker
                        var $original = $('#sec_ope_ppal_planta_ori_3');
                        var $aux_1 = $('#sec_ope_atl_1_planta_ori_3');
                        var $aux_2 = $('#sec_ope_atl_2_planta_ori_3');
                        // Asegúrate de usar el ID correcto
                        var $clon_org = $original.clone();
                        var $aux_1 = $aux_1.clone();
                        var $aux_2 = $aux_2.clone();

                        $clon_org.attr('id', 'sec_ope_ppal_planta_ori_' + i);
                        $clon_org.attr('name', 'sec_ope_ppal_planta_ori_' + i);
                        $clon_org.removeClass('selectpicker').selectpicker('destroy');
                        $('#selector_clon_1_' + i).append($clon_org);
                        $clon_org.selectpicker();

                        $aux_1.attr('id', 'sec_ope_atl_1_planta_ori_' + i);
                        $aux_1.attr('name', 'sec_ope_atl_1_planta_ori_' + i);
                        $aux_1.removeClass('selectpicker').selectpicker('destroy');
                        $('#selector_clon_2_' + i).append($aux_1);
                        $aux_1.selectpicker();

                        $aux_2.attr('id', 'sec_ope_atl_2_planta_ori_' + i);
                        $aux_2.attr('name', 'sec_ope_atl_2_planta_ori_' + i);
                        $aux_2.removeClass('selectpicker').selectpicker('destroy');
                        $('#selector_clon_3_' + i).append($aux_2);
                        $aux_2.selectpicker();

                    }

                    for (let i = 1; i <= (data.cantidad_filas); i++) {

                        if (!(data.array['fila_' + i] === undefined)) {
                            if (!(data.array['fila_' + i]['org'] === undefined)) {
                                $('#sec_ope_ppal_planta_ori_' + i).val(data.array['fila_' + i]['org']).selectpicker('refresh');
                            }
                            if (!(data.array['fila_' + i]['alt1'] === undefined)) {
                                $('#sec_ope_atl_1_planta_ori_' + i).val(data.array['fila_' + i]['alt1']).selectpicker('refresh');
                            }
                            if (!(data.array['fila_' + i]['alt2'] === undefined)) {
                                $('#sec_ope_atl_2_planta_ori_' + i).val(data.array['fila_' + i]['alt2']).selectpicker('refresh');
                            }
                            if (!(data.array['fila_' + i]['alt3'] === undefined)) {
                                $('#sec_ope_atl_3_planta_ori_' + i).val(data.array['fila_' + i]['alt3']).selectpicker('refresh');
                            }
                            if (!(data.array['fila_' + i]['alt4'] === undefined)) {
                                $('#sec_ope_atl_4_planta_ori_' + i).val(data.array['fila_' + i]['alt4']).selectpicker('refresh');
                            }
                            if (!(data.array['fila_' + i]['alt5'] === undefined)) {
                                $('#sec_ope_atl_5_planta_ori_' + i).val(data.array['fila_' + i]['alt5']).selectpicker('refresh');
                            }
                        }
                    }

                } else {
                    $('#sec_ope_ppal_planta_ori_1,#sec_ope_atl_1_planta_ori_1,#sec_ope_atl_2_planta_ori_1,#sec_ope_atl_3_planta_ori_1,#sec_ope_atl_4_planta_ori_1,#sec_ope_atl_5_planta_ori_1,#sec_ope_ppal_planta_ori_2,#sec_ope_atl_1_planta_ori_2,#sec_ope_atl_2_planta_ori_2,#sec_ope_atl_3_planta_ori_2,#sec_ope_atl_4_planta_ori_2,#sec_ope_atl_5_planta_ori_2,#sec_ope_ppal_planta_ori_3,#sec_ope_atl_1_planta_ori_3,#sec_ope_atl_2_planta_ori_3,#sec_ope_atl_3_planta_ori_3,#sec_ope_atl_4_planta_ori_3,#sec_ope_atl_5_planta_ori_3,#sec_ope_ppal_planta_ori_4,#sec_ope_atl_1_planta_ori_4,#sec_ope_atl_2_planta_ori_4,#sec_ope_atl_3_planta_ori_4,#sec_ope_atl_4_planta_ori_4,#sec_ope_atl_5_planta_ori_4,#sec_ope_ppal_planta_ori_5,#sec_ope_atl_1_planta_ori_5,#sec_ope_atl_2_planta_ori_5,#sec_ope_atl_3_planta_ori_5,#sec_ope_atl_4_planta_ori_5,#sec_ope_atl_5_planta_ori_5,#sec_ope_ppal_planta_ori_6,#sec_ope_atl_1_planta_ori_6,#sec_ope_atl_2_planta_ori_6,#sec_ope_atl_3_planta_ori_6,#sec_ope_atl_4_planta_ori_6,#sec_ope_atl_5_planta_ori_6')
                        .html(data.html)
                        .prop('disabled', false)
                        .selectpicker('refresh');

                    if (data.planta == 1) {
                        $('#planta_original_sec_ope').val('BUIN').prop('disabled', true);
                        $('#sec_ope_planta_orig_id').val(1);
                    } else {
                        if (data.planta == 2) {
                            $('#planta_original_sec_ope').val('TILTIL').prop('disabled', true);
                            $('#sec_ope_planta_orig_id').val(2);
                        } else {
                            $('#planta_original_sec_ope').val('OSORNO').prop('disabled', true);
                            $('#sec_ope_planta_orig_id').val(3);
                        }
                    }

                    for (let i = 1; i <= (data.cantidad_filas); i++) {
                        if (!(data.array['fila_' + i] === undefined)) {
                            if (!(data.array['fila_' + i]['org'] === undefined)) {
                                $('#sec_ope_ppal_planta_ori_' + i).val(data.array['fila_' + i]['org']).selectpicker('refresh');
                            }
                            if (!(data.array['fila_' + i]['alt1'] === undefined)) {
                                $('#sec_ope_atl_1_planta_ori_' + i).val(data.array['fila_' + i]['alt1']).selectpicker('refresh');
                            }
                            if (!(data.array['fila_' + i]['alt2'] === undefined)) {
                                $('#sec_ope_atl_2_planta_ori_' + i).val(data.array['fila_' + i]['alt2']).selectpicker('refresh');
                            }
                            if (!(data.array['fila_' + i]['alt3'] === undefined)) {
                                $('#sec_ope_atl_3_planta_ori_' + i).val(data.array['fila_' + i]['alt3']).selectpicker('refresh');
                            }
                            if (!(data.array['fila_' + i]['alt4'] === undefined)) {
                                $('#sec_ope_atl_4_planta_ori_' + i).val(data.array['fila_' + i]['alt4']).selectpicker('refresh');
                            }
                            if (!(data.array['fila_' + i]['alt5'] === undefined)) {
                                $('#sec_ope_atl_5_planta_ori_' + i).val(data.array['fila_' + i]['alt5']).selectpicker('refresh');
                            }
                        }
                    }

                }
            }



            if (data.cantidad_filas == 0 || isNaN(data.cantidad_filas)) {
                $('#sec_ope_planta_orig_filas').val(6);
            } else {
                $('#sec_ope_planta_orig_filas').val(data.cantidad_filas);
            }

            $('#agregar_fila_planta_original').prop('disabled', false);
            //getSecuenciasOperacionalesOt(ot_id);
        },
    });

}

function chargeSelectSecOperacionalPlantaAux1(ot_id) {

    $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3')
        .html("")
        .prop('disabled', true)
        .selectpicker('refresh');

    return $.ajax({
        type: "GET",
        url: "/chargeSelectSecOperacionalPlantaAux1",
        data: "ot_id=" + ot_id,
        success: function (data) {


            if (data.planta === null || typeof data.planta === 'undefined') {
                if ($('#planta_id').val() == 1) {
                    $('#planta_aux_1_sec_ope').val('TILTIL').prop('disabled', true);
                    getSecuenciasOperacionalesPlantaAux1(2);
                    $('#sec_ope_planta_aux_1_id').val(2);
                } else {
                    if ($('#planta_id').val() == 2) {
                        $('#planta_aux_1_sec_ope').val('BUIN').prop('disabled', true);
                        getSecuenciasOperacionalesPlantaAux1(1);
                        $('#sec_ope_planta_aux_1_id').val(1);
                    } else {
                        $('#planta_aux_1_sec_ope').val('BUIN').prop('disabled', true);
                        getSecuenciasOperacionalesPlantaAux1(1);
                        $('#sec_ope_planta_aux_1_id').val(1);
                    }
                }

            } else {

                if (data.cantidad_filas > 6) {
                    $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3')
                        .html(data.html)
                        .prop('disabled', true)
                        .selectpicker('refresh');

                    if (data.planta == 1) {
                        $('#planta_aux_1_sec_ope').val('BUIN').prop('disabled', true);
                        $('#sec_ope_planta_aux_1_id').val(1);
                    } else {
                        if (data.planta == 2) {
                            $('#planta_aux_1_sec_ope').val('TILTIL').prop('disabled', true);
                            $('#sec_ope_planta_aux_1_id').val(2);
                        } else {
                            $('#planta_aux_1_sec_ope').val('OSORNO').prop('disabled', true);
                            $('#sec_ope_planta_aux_1_id').val(3);
                        }
                    }

                    for (let i = 4; i <= (data.cantidad_filas); i++) {
                        var fila = '';
                        var num_fila = i;
                        fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
                        fila += '    <div class="col-4">';
                        fila += '        <div class="form-group form-row">';
                        fila += '            <div class="col">';
                        fila += '                <div id="selector_aux_1_1_' + num_fila + '"></div>';
                        fila += '            </div>';
                        fila += '        </div>';
                        fila += '    </div>';
                        fila += '    <div class="col-4">';
                        fila += '        <div class="form-group form-row">';
                        fila += '            <div class="col">';
                        fila += '                <div id="selector_aux_1_2_' + num_fila + '"></div>';
                        fila += '            </div>';
                        fila += '        </div>';
                        fila += '    </div>';
                        fila += '    <div class="col-4">';
                        fila += '        <div class="form-group form-row">';
                        fila += '            <div class="col">';
                        fila += '                <div id="selector_aux_1_3_' + num_fila + '"></div>';
                        fila += '            </div>';
                        fila += '        </div>';
                        fila += '    </div>';
                        fila += '</div>';
                        fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
                        fila += '    <div class="col-12">';
                        fila += '        &nbsp;';
                        fila += '    </div>';
                        fila += '</div>';

                        $('#fila_planta_aux_1').append(fila);

                        // Clonar el select picker
                        var $original = $('#sec_ope_ppal_planta_aux_1_3');
                        var $aux_1 = $('#sec_ope_atl_1_planta_aux_1_3');
                        var $aux_2 = $('#sec_ope_atl_2_planta_aux_1_3');
                        // Asegúrate de usar el ID correcto
                        var $clon_org = $original.clone();
                        var $aux_1 = $aux_1.clone();
                        var $aux_2 = $aux_2.clone();

                        $clon_org.attr('id', 'sec_ope_ppal_planta_aux_1_' + num_fila);
                        $clon_org.attr('name', 'sec_ope_ppal_planta_aux_1_' + num_fila);
                        $clon_org.removeClass('selectpicker').selectpicker('destroy');
                        $('#selector_aux_1_1_' + num_fila).append($clon_org);
                        $clon_org.selectpicker();

                        $aux_1.attr('id', 'sec_ope_atl_1_planta_aux_1_' + num_fila);
                        $aux_1.attr('name', 'sec_ope_atl_1_planta_aux_1_' + num_fila);
                        $aux_1.removeClass('selectpicker').selectpicker('destroy');
                        $('#selector_aux_1_2_' + num_fila).append($aux_1);
                        $aux_1.selectpicker();

                        $aux_2.attr('id', 'sec_ope_atl_2_planta_aux_1_' + num_fila);
                        $aux_2.attr('name', 'sec_ope_atl_2_planta_aux_1_' + num_fila);
                        $aux_2.removeClass('selectpicker').selectpicker('destroy');
                        $('#selector_aux_1_3_' + num_fila).append($aux_2);
                        $aux_2.selectpicker();
                    }

                    if (data.habilitado) {
                        $('#check_planta_aux_1').prop('disabled', false);
                        $('#check_planta_aux_1').prop('checked', true);
                        $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3')
                            .prop('disabled', false)
                            .selectpicker('refresh');// Marca el checkbox como seleccionado
                        $('#agregar_fila_planta_auxiliar_1').prop('disabled', false);
                    } else {
                        // Aquí puedes desmarcar el checkbox si es necesario
                        $('#check_planta_aux_1').prop('checked', false);
                        $('#agregar_fila_planta_auxiliar_1').prop('disabled', true);
                    }

                    for (let i = 1; i <= (data.cantidad_filas); i++) {
                        var num_fila = i;

                        if (!(data.array['fila_' + num_fila] === undefined)) {
                            if (!(data.array['fila_' + num_fila]['org'] === undefined)) {
                                $('#sec_ope_ppal_planta_aux_1_' + num_fila).val(data.array['fila_' + num_fila]['org']).selectpicker('refresh');
                                if (data.habilitado) {
                                    $('#sec_ope_ppal_planta_aux_1_' + num_fila).prop('disabled', false).selectpicker('refresh');
                                }
                            }
                            if (!(data.array['fila_' + num_fila]['alt1'] === undefined)) {
                                $('#sec_ope_atl_1_planta_aux_1_' + num_fila).val(data.array['fila_' + num_fila]['alt1']).selectpicker('refresh');
                                if (data.habilitado) {
                                    $('#sec_ope_atl_1_planta_aux_1_' + num_fila).prop('disabled', false).selectpicker('refresh');
                                }
                            }
                            if (!(data.array['fila_' + num_fila]['alt2'] === undefined)) {
                                $('#sec_ope_atl_2_planta_aux_1_' + num_fila).val(data.array['fila_' + num_fila]['alt2']).selectpicker('refresh');
                                if (data.habilitado) {
                                    $('#sec_ope_atl_2_planta_aux_1_' + num_fila).prop('disabled', false).selectpicker('refresh');
                                }
                            }
                        }
                    }

                } else {


                    $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3')
                        .html(data.html)
                        .prop('disabled', true)
                        .selectpicker('refresh');

                    if (data.planta == 1) {
                        $('#planta_aux_1_sec_ope').val('BUIN').prop('disabled', true);
                        $('#sec_ope_planta_aux_1_id').val(1);
                    } else {
                        if (data.planta == 2) {
                            $('#planta_aux_1_sec_ope').val('TILTIL').prop('disabled', true);
                            $('#sec_ope_planta_aux_1_id').val(2);
                        } else {
                            $('#planta_aux_1_sec_ope').val('OSORNO').prop('disabled', true);
                            $('#sec_ope_planta_aux_1_id').val(3);
                        }
                    }

                    if (data.habilitado) {
                        $('#check_planta_aux_1').prop('disabled', false);
                        $('#check_planta_aux_1').prop('checked', true);
                        $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3')
                            .prop('disabled', false)
                            .selectpicker('refresh');// Marca el checkbox como seleccionado
                        $('#agregar_fila_planta_auxiliar_1').prop('disabled', false);
                    } else {
                        // Aquí puedes desmarcar el checkbox si es necesario
                        $('#check_planta_aux_1').prop('checked', false);
                        $('#agregar_fila_planta_auxiliar_1').prop('disabled', true);
                    }

                    for (let i = 1; i <= (data.cantidad_filas); i++) {
                        var num_fila = i;

                        if (!(data.array['fila_' + num_fila] === undefined)) {
                            if (!(data.array['fila_' + num_fila]['org'] === undefined)) {
                                $('#sec_ope_ppal_planta_aux_1_' + num_fila).val(data.array['fila_' + num_fila]['org']).selectpicker('refresh');
                            }
                            if (!(data.array['fila_' + num_fila]['alt1'] === undefined)) {
                                $('#sec_ope_atl_1_planta_aux_1_' + num_fila).val(data.array['fila_' + num_fila]['alt1']).selectpicker('refresh');
                            }
                            if (!(data.array['fila_' + num_fila]['alt2'] === undefined)) {
                                $('#sec_ope_atl_2_planta_aux_1_' + num_fila).val(data.array['fila_' + num_fila]['alt2']).selectpicker('refresh');
                            }
                        }
                    }
                }
            }

            if (data.cantidad_filas == 0 || isNaN(data.cantidad_filas)) {
                $('#sec_ope_planta_aux_1_filas').val(3);
            } else {
                $('#sec_ope_planta_aux_1_filas').val(data.cantidad_filas);
            }
            //getSecuenciasOperacionalesOt(ot_id);
        },
    });

}

function chargeSelectSecOperacionalPlantaAux2(ot_id) {

    $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3')
        .html("")
        .prop('disabled', true)
        .selectpicker('refresh');

    return $.ajax({
        type: "GET",
        url: "/chargeSelectSecOperacionalPlantaAux2",
        data: "ot_id=" + ot_id,
        success: function (data) {

            if (data.planta === null || typeof data.planta === 'undefined') {
                if ($('#planta_id').val() == 1) {
                    $('#planta_aux_2_sec_ope').val('OSORNO').prop('disabled', true);
                    getSecuenciasOperacionalesPlantaAux2(3);
                    $('#sec_ope_planta_aux_2_id').val(3);
                } else {
                    if ($('#planta_id').val() == 2) {
                        $('#planta_aux_2_sec_ope').val('OSORNO').prop('disabled', true);
                        getSecuenciasOperacionalesPlantaAux2(1);
                        $('#sec_ope_planta_aux_2_id').val(1);
                    } else {
                        $('#planta_aux_2_sec_ope').val('TILTIL').prop('disabled', true);
                        getSecuenciasOperacionalesPlantaAux2(2);
                        $('#sec_ope_planta_aux_2_id').val(2);
                    }
                }

            } else {

                if (data.cantidad_filas > 6) {
                    $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3')
                        .html(data.html)
                        .prop('disabled', true)
                        .selectpicker('refresh');

                    if (data.planta == 1) {
                        $('#planta_aux_2_sec_ope').val('BUIN').prop('disabled', true);
                        $('#sec_ope_planta_aux_2_id').val(1);
                    } else {
                        if (data.planta == 2) {
                            $('#planta_aux_2_sec_ope').val('TILTIL').prop('disabled', true);
                            $('#sec_ope_planta_aux_2_id').val(2);
                        } else {
                            $('#planta_aux_2_sec_ope').val('OSORNO').prop('disabled', true);
                            $('#sec_ope_planta_aux_2_id').val(3);
                        }
                    }

                    if (data.habilitado) {
                        $('#check_planta_aux_2').prop('disabled', false);
                        $('#check_planta_aux_2').prop('checked', true); // Marca el checkbox como seleccionado
                        $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3')
                            .prop('disabled', false)
                            .selectpicker('refresh');
                        $('#agregar_fila_planta_auxiliar_2').prop('disabled', false);
                    } else {
                        // Aquí puedes desmarcar el checkbox si es necesario
                        $('#check_planta_aux_2').prop('checked', false);
                        $('#agregar_fila_planta_auxiliar_2').prop('disabled', true);
                    }

                    for (let i = 4; i <= (data.cantidad_filas); i++) {

                        var num_fila = i;
                        var fila = '';
                        fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
                        fila += '    <div class="col-4">';
                        fila += '        <div class="form-group form-row">';
                        fila += '            <div class="col">';
                        fila += '                <div id="selector_aux_2_1_' + num_fila + '"></div>';
                        fila += '            </div>';
                        fila += '        </div>';
                        fila += '    </div>';
                        fila += '    <div class="col-4">';
                        fila += '        <div class="form-group form-row">';
                        fila += '            <div class="col">';
                        fila += '                <div id="selector_aux_2_2_' + num_fila + '"></div>';
                        fila += '            </div>';
                        fila += '        </div>';
                        fila += '    </div>';
                        fila += '    <div class="col-4">';
                        fila += '        <div class="form-group form-row">';
                        fila += '            <div class="col">';
                        fila += '                <div id="selector_aux_2_3_' + num_fila + '"></div>';
                        fila += '            </div>';
                        fila += '        </div>';
                        fila += '    </div>';
                        fila += '</div>';
                        fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
                        fila += '    <div class="col-12">';
                        fila += '        &nbsp;';
                        fila += '    </div>';
                        fila += '</div>';

                        $('#fila_planta_aux_2').append(fila);

                        // Clonar el select picker
                        var $original = $('#sec_ope_ppal_planta_aux_2_3');
                        var $aux_1 = $('#sec_ope_atl_1_planta_aux_2_3');
                        var $aux_2 = $('#sec_ope_atl_2_planta_aux_2_3');
                        // Asegúrate de usar el ID correcto
                        var $clon_org = $original.clone();
                        var $aux_1 = $aux_1.clone();
                        var $aux_2 = $aux_2.clone();

                        $clon_org.attr('id', 'sec_ope_ppal_planta_aux_2_' + num_fila);
                        $clon_org.attr('name', 'sec_ope_ppal_planta_aux_2_' + num_fila);
                        $clon_org.removeClass('selectpicker').selectpicker('destroy');
                        $('#selector_aux_2_1_' + num_fila).append($clon_org);
                        $clon_org.selectpicker();

                        $aux_1.attr('id', 'sec_ope_atl_1_planta_aux_2_' + num_fila);
                        $aux_1.attr('name', 'sec_ope_atl_1_planta_aux_2_' + num_fila);
                        $aux_1.removeClass('selectpicker').selectpicker('destroy');
                        $('#selector_aux_2_2_' + num_fila).append($aux_1);
                        $aux_1.selectpicker();

                        $aux_2.attr('id', 'sec_ope_atl_2_planta_aux_2_' + num_fila);
                        $aux_2.attr('name', 'sec_ope_atl_2_planta_aux_2_' + num_fila);
                        $aux_2.removeClass('selectpicker').selectpicker('destroy');
                        $('#selector_aux_2_3_' + num_fila).append($aux_2);
                        $aux_2.selectpicker();
                    }

                    for (let i = 1; i <= (data.cantidad_filas); i++) {
                        var num_fila = i;
                        console.log(data.array['fila_' + num_fila]);
                        if (!(data.array['fila_' + num_fila] === undefined)) {
                            if (!(data.array['fila_' + num_fila]['org'] === undefined)) {
                                $('#sec_ope_ppal_planta_aux_2_' + num_fila).val(data.array['fila_' + num_fila]['org']).selectpicker('refresh');
                                if (data.habilitado) {
                                    $('#sec_ope_ppal_planta_aux_2_' + num_fila).prop('disabled', false).selectpicker('refresh');
                                }
                            }
                            if (!(data.array['fila_' + num_fila]['alt1'] === undefined)) {
                                $('#sec_ope_atl_1_planta_aux_2_' + num_fila).val(data.array['fila_' + num_fila]['alt1']).selectpicker('refresh');
                                if (data.habilitado) {
                                    $('#sec_ope_atl_1_planta_aux_2_' + num_fila).prop('disabled', false).selectpicker('refresh');
                                }
                            }
                            if (!(data.array['fila_' + num_fila]['alt2'] === undefined)) {
                                $('#sec_ope_atl_2_planta_aux_2_' + num_fila).val(data.array['fila_' + num_fila]['alt2']).selectpicker('refresh');
                                if (data.habilitado) {
                                    $('#sec_ope_atl_2_planta_aux_2_' + num_fila).prop('disabled', false).selectpicker('refresh');
                                }
                            }
                        }
                    }

                } else {

                    $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3')
                        .html(data.html)
                        .prop('disabled', true)
                        .selectpicker('refresh');

                    if (data.planta == 1) {
                        $('#planta_aux_2_sec_ope').val('BUIN').prop('disabled', true);
                        $('#sec_ope_planta_aux_2_id').val(1);
                    } else {
                        if (data.planta == 2) {
                            $('#planta_aux_2_sec_ope').val('TILTIL').prop('disabled', true);
                            $('#sec_ope_planta_aux_2_id').val(2);
                        } else {
                            $('#planta_aux_2_sec_ope').val('OSORNO').prop('disabled', true);
                            $('#sec_ope_planta_aux_2_id').val(3);
                        }
                    }

                    if (data.habilitado) {
                        $('#check_planta_aux_2').prop('disabled', false);
                        $('#check_planta_aux_2').prop('checked', true); // Marca el checkbox como seleccionado
                        $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3')
                            .prop('disabled', false)
                            .selectpicker('refresh');
                        $('#agregar_fila_planta_auxiliar_2').prop('disabled', false);
                    } else {
                        // Aquí puedes desmarcar el checkbox si es necesario
                        $('#check_planta_aux_2').prop('checked', false);
                        $('#agregar_fila_planta_auxiliar_2').prop('disabled', true);
                    }

                    for (let i = 1; i <= (data.cantidad_filas); i++) {
                        var num_fila = i;
                        console.log(data.array['fila_' + num_fila]);
                        if (!(data.array['fila_' + num_fila] === undefined)) {
                            if (!(data.array['fila_' + num_fila]['org'] === undefined)) {
                                $('#sec_ope_ppal_planta_aux_2_' + num_fila).val(data.array['fila_' + num_fila]['org']).selectpicker('refresh');
                            }
                            if (!(data.array['fila_' + num_fila]['alt1'] === undefined)) {
                                $('#sec_ope_atl_1_planta_aux_2_' + num_fila).val(data.array['fila_' + num_fila]['alt1']).selectpicker('refresh');
                            }
                            if (!(data.array['fila_' + num_fila]['alt2'] === undefined)) {
                                $('#sec_ope_atl_2_planta_aux_2_' + num_fila).val(data.array['fila_' + num_fila]['alt2']).selectpicker('refresh');
                            }
                        }
                    }
                }
            }

            if (data.cantidad_filas == 0 || isNaN(data.cantidad_filas)) {
                $('#sec_ope_planta_aux_2_filas').val(3);
            } else {
                $('#sec_ope_planta_aux_2_filas').val(data.cantidad_filas);
            }
            // getSecuenciasOperacionalesOt(ot_id);
        },
    });

}

///Funcionas para agregar filas de las secuencias operacionales
$("#agregar_fila_planta_original").click(function () {

    // Clonar el select picker
    var $original = $('#sec_ope_ppal_planta_ori_3');
    var $aux_1 = $('#sec_ope_atl_1_planta_ori_3');
    var $aux_2 = $('#sec_ope_atl_2_planta_ori_3');
    // Asegúrate de usar el ID correcto
    var $clon_org = $original.clone();
    var $aux_1 = $aux_1.clone();
    var $aux_2 = $aux_2.clone();

    // Actualizar atributos para evitar conflictos
    let num_fila = parseInt($('#sec_ope_planta_orig_filas').val());
    num_fila = num_fila + 1; // Ejemplo de ID único

    var fila = '';
    fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_clon_1_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_clon_2_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_clon_3_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '</div>';
    fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
    fila += '    <div class="col-12">';
    fila += '        &nbsp;';
    fila += '    </div>';
    fila += '</div>';

    $('#fila_planta_original').append(fila);

    $clon_org.attr('id', 'sec_ope_ppal_planta_ori_' + num_fila);
    $clon_org.attr('name', 'sec_ope_ppal_planta_ori_' + num_fila);
    $clon_org.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_clon_1_' + num_fila).append($clon_org);
    $clon_org.selectpicker();

    $aux_1.attr('id', 'sec_ope_atl_1_planta_ori_' + num_fila);
    $aux_1.attr('name', 'sec_ope_atl_1_planta_ori_' + num_fila);
    $aux_1.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_clon_2_' + num_fila).append($aux_1);
    $aux_1.selectpicker();

    $aux_2.attr('id', 'sec_ope_atl_2_planta_ori_' + num_fila);
    $aux_2.attr('name', 'sec_ope_atl_2_planta_ori_' + num_fila);
    $aux_2.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_clon_3_' + num_fila).append($aux_2);
    $aux_2.selectpicker();

    $('#sec_ope_planta_orig_filas').val(num_fila);

});

$("#agregar_fila_planta_auxiliar_1").click(function () {

    // Actualizar atributos para evitar conflictos
    let num_fila = parseInt($('#sec_ope_planta_aux_1_filas').val());
    num_fila = num_fila + 1; // Ejemplo de ID único

    var fila = '';
    fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_aux_1_1_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_aux_1_2_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_aux_1_3_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '</div>';
    fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
    fila += '    <div class="col-12">';
    fila += '        &nbsp;';
    fila += '    </div>';
    fila += '</div>';

    $('#fila_planta_aux_1').append(fila);

    // Clonar el select picker
    var $original = $('#sec_ope_ppal_planta_aux_1_3');
    var $aux_1 = $('#sec_ope_atl_1_planta_aux_1_3');
    var $aux_2 = $('#sec_ope_atl_2_planta_aux_1_3');
    // Asegúrate de usar el ID correcto
    var $clon_org = $original.clone();
    var $aux_1 = $aux_1.clone();
    var $aux_2 = $aux_2.clone();

    $clon_org.attr('id', 'sec_ope_ppal_planta_aux_1_' + num_fila);
    $clon_org.attr('name', 'sec_ope_ppal_planta_aux_1_' + num_fila);
    $clon_org.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_aux_1_1_' + num_fila).append($clon_org);
    $clon_org.selectpicker();

    $aux_1.attr('id', 'sec_ope_atl_1_planta_aux_1_' + num_fila);
    $aux_1.attr('name', 'sec_ope_atl_1_planta_aux_1_' + num_fila);
    $aux_1.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_aux_1_2_' + num_fila).append($aux_1);
    $aux_1.selectpicker();

    $aux_2.attr('id', 'sec_ope_atl_2_planta_aux_1_' + num_fila);
    $aux_2.attr('name', 'sec_ope_atl_2_planta_aux_1_' + num_fila);
    $aux_2.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_aux_1_3_' + num_fila).append($aux_2);
    $aux_2.selectpicker();

    $('#sec_ope_planta_aux_1_filas').val(num_fila);

});

$("#agregar_fila_planta_auxiliar_2").click(function () {

    // Clonar el select picker
    var $original = $('#sec_ope_ppal_planta_aux_2_3');
    var $aux_1 = $('#sec_ope_atl_1_planta_aux_2_3');
    var $aux_2 = $('#sec_ope_atl_2_planta_aux_2_3');
    // Asegúrate de usar el ID correcto
    var $clon_org = $original.clone();
    var $aux_1 = $aux_1.clone();
    var $aux_2 = $aux_2.clone();

    // Actualizar atributos para evitar conflictos
    let num_fila = parseInt($('#sec_ope_planta_aux_2_filas').val());
    num_fila = num_fila + 1; // Ejemplo de ID único

    var fila = '';
    fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_aux_2_1_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_aux_2_2_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_aux_2_3_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '</div>';
    fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
    fila += '    <div class="col-12">';
    fila += '        &nbsp;';
    fila += '    </div>';
    fila += '</div>';

    $('#fila_planta_aux_2').append(fila);

    $clon_org.attr('id', 'sec_ope_ppal_planta_aux_2_' + num_fila);
    $clon_org.attr('name', 'sec_ope_ppal_planta_aux_2_' + num_fila);
    $clon_org.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_aux_2_1_' + num_fila).append($clon_org);
    $clon_org.selectpicker();

    $aux_1.attr('id', 'sec_ope_atl_1_planta_aux_2_' + num_fila);
    $aux_1.attr('name', 'sec_ope_atl_1_planta_aux_2_' + num_fila);
    $aux_1.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_aux_2_2_' + num_fila).append($aux_1);
    $aux_1.selectpicker();

    $aux_2.attr('id', 'sec_ope_atl_2_planta_aux_2_' + num_fila);
    $aux_2.attr('name', 'sec_ope_atl_2_planta_aux_2_' + num_fila);
    $aux_2.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_aux_2_3_' + num_fila).append($aux_2);
    $aux_2.selectpicker();

    $('#sec_ope_planta_aux_2_filas').val(num_fila);

});

function getSecuenciasOperacionalesPlanta(planta_id) {

    $('#sec_ope_ppal_planta_ori_1,#sec_ope_atl_1_planta_ori_1,#sec_ope_atl_2_planta_ori_1,#sec_ope_atl_3_planta_ori_1,#sec_ope_atl_4_planta_ori_1,#sec_ope_atl_5_planta_ori_1,#sec_ope_ppal_planta_ori_2,#sec_ope_atl_1_planta_ori_2,#sec_ope_atl_2_planta_ori_2,#sec_ope_atl_3_planta_ori_2,#sec_ope_atl_4_planta_ori_2,#sec_ope_atl_5_planta_ori_2,#sec_ope_ppal_planta_ori_3,#sec_ope_atl_1_planta_ori_3,#sec_ope_atl_2_planta_ori_3,#sec_ope_atl_3_planta_ori_3,#sec_ope_atl_4_planta_ori_3,#sec_ope_atl_5_planta_ori_3,#sec_ope_ppal_planta_ori_4,#sec_ope_atl_1_planta_ori_4,#sec_ope_atl_2_planta_ori_4,#sec_ope_atl_3_planta_ori_4,#sec_ope_atl_4_planta_ori_4,#sec_ope_atl_5_planta_ori_4,#sec_ope_ppal_planta_ori_5,#sec_ope_atl_1_planta_ori_5,#sec_ope_atl_2_planta_ori_5,#sec_ope_atl_3_planta_ori_5,#sec_ope_atl_4_planta_ori_5,#sec_ope_atl_5_planta_ori_5,#sec_ope_ppal_planta_ori_6,#sec_ope_atl_1_planta_ori_6,#sec_ope_atl_2_planta_ori_6,#sec_ope_atl_3_planta_ori_6,#sec_ope_atl_4_planta_ori_6,#sec_ope_atl_5_planta_ori_6')
        .html("")
        .prop('disabled', false)
        .selectpicker('refresh');
    $('#agregar_fila_planta_original').prop('disabled', false);
    return $.ajax({
        type: "GET",
        url: "/getSecuenciasOperacionalesPlanta",
        data: "planta_id=" + planta_id,
        success: function (data) {
            console.log(data);
            $('#sec_ope_ppal_planta_ori_1,#sec_ope_atl_1_planta_ori_1,#sec_ope_atl_2_planta_ori_1,#sec_ope_atl_3_planta_ori_1,#sec_ope_atl_4_planta_ori_1,#sec_ope_atl_5_planta_ori_1,#sec_ope_ppal_planta_ori_2,#sec_ope_atl_1_planta_ori_2,#sec_ope_atl_2_planta_ori_2,#sec_ope_atl_3_planta_ori_2,#sec_ope_atl_4_planta_ori_2,#sec_ope_atl_5_planta_ori_2,#sec_ope_ppal_planta_ori_3,#sec_ope_atl_1_planta_ori_3,#sec_ope_atl_2_planta_ori_3,#sec_ope_atl_3_planta_ori_3,#sec_ope_atl_4_planta_ori_3,#sec_ope_atl_5_planta_ori_3,#sec_ope_ppal_planta_ori_4,#sec_ope_atl_1_planta_ori_4,#sec_ope_atl_2_planta_ori_4,#sec_ope_atl_3_planta_ori_4,#sec_ope_atl_4_planta_ori_4,#sec_ope_atl_5_planta_ori_4,#sec_ope_ppal_planta_ori_5,#sec_ope_atl_1_planta_ori_5,#sec_ope_atl_2_planta_ori_5,#sec_ope_atl_3_planta_ori_5,#sec_ope_atl_4_planta_ori_5,#sec_ope_atl_5_planta_ori_5,#sec_ope_ppal_planta_ori_6,#sec_ope_atl_1_planta_ori_6,#sec_ope_atl_2_planta_ori_6,#sec_ope_atl_3_planta_ori_6,#sec_ope_atl_4_planta_ori_6,#sec_ope_atl_5_planta_ori_6')
                .html(data)
                .prop('disabled', false)
                .selectpicker('refresh');
        },
    });
}

function getSecuenciasOperacionalesPlantaAux1(planta_id) {

    $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3')
        .html("")
        .prop('disabled', true)
        .selectpicker('refresh');

    // console.log($('#planta_id').val());

    return $.ajax({
        type: "GET",
        url: "/getSecuenciasOperacionalesPlanta",
        data: "planta_id=" + planta_id,
        success: function (data) {
            $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3')
                .html(data)
                .prop('disabled', true)
                .selectpicker('refresh');
        },
    });



}

function getSecuenciasOperacionalesPlantaAux2(planta_id) {

    $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3')
        .html("")
        .prop('disabled', true)
        .selectpicker('refresh');

    // console.log($('#planta_id').val());

    return $.ajax({
        type: "GET",
        url: "/getSecuenciasOperacionalesPlanta",
        data: "planta_id=" + planta_id,
        success: function (data) {
            $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3')
                .html(data)
                .prop('disabled', true)
                .selectpicker('refresh');
        },
    });



}

//Manejo de los check de las Plantas de la Secuencia Operacional
$("#check_planta_aux_1").click(function () {
    var cantidad_filas = $('#sec_ope_planta_aux_1_filas').val();
    if (this.checked) {

        for (let i = 1; i <= (cantidad_filas); i++) {

            $('#sec_ope_ppal_planta_aux_1_' + i)
                .prop('disabled', false)
                .selectpicker('refresh');

            $('#sec_ope_atl_1_planta_aux_1_' + i)
                .prop('disabled', false)
                .selectpicker('refresh');

            $('#sec_ope_atl_2_planta_aux_1_' + i)
                .prop('disabled', false)
                .selectpicker('refresh');
        }

        $('#agregar_fila_planta_auxiliar_1').prop('disabled', false);

    } else {
        for (let i = 1; i <= (cantidad_filas); i++) {

            $('#sec_ope_ppal_planta_aux_1_' + i)
                .prop('disabled', true)
                .selectpicker('refresh');

            $('#sec_ope_atl_1_planta_aux_1_' + i)
                .prop('disabled', true)
                .selectpicker('refresh');

            $('#sec_ope_atl_2_planta_aux_1_' + i)
                .prop('disabled', true)
                .selectpicker('refresh');
        }

        $('#agregar_fila_planta_auxiliar_1').prop('disabled', true);
    }
});

$("#check_planta_aux_2").click(function () {
    var cantidad_filas = $('#sec_ope_planta_aux_2_filas').val();
    if (this.checked) {
        for (let i = 1; i <= (cantidad_filas); i++) {

            $('#sec_ope_ppal_planta_aux_2_' + i)
                .prop('disabled', false)
                .selectpicker('refresh');

            $('#sec_ope_atl_1_planta_aux_2_' + i)
                .prop('disabled', false)
                .selectpicker('refresh');

            $('#sec_ope_atl_2_planta_aux_2_' + i)
                .prop('disabled', false)
                .selectpicker('refresh');
        }

        $('#agregar_fila_planta_auxiliar_2').prop('disabled', false);
    } else {
        for (let i = 1; i <= (cantidad_filas); i++) {

            $('#sec_ope_ppal_planta_aux_2_' + i)
                .prop('disabled', true)
                .selectpicker('refresh');

            $('#sec_ope_atl_1_planta_aux_2_' + i)
                .prop('disabled', true)
                .selectpicker('refresh');

            $('#sec_ope_atl_2_planta_aux_2_' + i)
                .prop('disabled', true)
                .selectpicker('refresh');
        }

        $('#agregar_fila_planta_auxiliar_2').prop('disabled', true);
    }
});
