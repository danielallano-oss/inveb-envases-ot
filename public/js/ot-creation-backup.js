$(document).ready(function() {
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
            "Por favor, ingresa menos de {0} caracteres."
        ),
        minlength: jQuery.validator.format(
            "Por favor, ingresa más {0} caracteres."
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
        )
    });

    // Custom rules
    jQuery.validator.addMethod(
        "exactlength",
        function(value, element, param) {
            return this.optional(element) || value.length == param;
        },
        $.validator.format("Por favor, ingresa exactamente {0} caracteres.")
    );

    $.validator.addMethod(
        "telefono",
        function(value, element) {
            return (
                this.optional(element) ||
                /^(\+56)?(\s?)(0?[92])(\s?)[987654321]\d{7}$/.test(value)
            );
        },
        $.validator.format("Formato de Telefono: +56912345678")
    );

    const desarrolloCompletoOCotizanSinCad = () => {
        if (
            $("#tipo_solicitud").val() == 1 ||
            $("#tipo_solicitud").val() == 4
        ) {
            return true;
        }
        return false;
    };
    const notMuestra = () => {
        if ($("#tipo_solicitud").val() == 3) {
            return false;
        }
        return true;
    };
    const muestra = () => {
        if ($("#tipo_solicitud").val() == 3) {
            return true;
        }
        return false;
    };

    // VALIDACION DE FORMULARIO
    $("#form-ot")
        .submit(function(e) {
            e.preventDefault();
        })
        .validate({
            // Specify validation rules
            rules: {
                client_id: "required",
                descripcion: {
                    required: true,
                    maxlength: 40
                },
                tipo_solicitud: "required",
                // nombre_contacto: "required",
                // email_contacto: { required: true, email: true },
                // telefono_contacto: "required telefono",
                volumen_venta_anual: {
                    required: notMuestra
                },
                usd: {
                    required: notMuestra
                },
                canal_id: "required",
                // hierarchy_id: "required",
                subhierarchy_id: "required",
                subsubhierarchy_id: "required",
                // Solicita
                "checkboxes[]": { required: true },
                numero_muestras: "required",
                // Referencia
                // reference_type: "required",
                // reference_id: "required",
                // bloqueo_referencia: "required",
                cad: "required",
                cad_id: "required",
                product_type_id: "required",
                // items_set: "required",
                // veces_item: "required",
                carton_id: {
                    required: muestra
                },
                carton_color: "required",
                numero_colores: "required",
                // datos para desarrollo
                peso_contenido_caja: {
                    required: desarrolloCompletoOCotizanSinCad
                },
                autosoportante: {
                    required: desarrolloCompletoOCotizanSinCad
                },
                envase_id: {
                    required: desarrolloCompletoOCotizanSinCad
                },
                cajas_altura: {
                    required: desarrolloCompletoOCotizanSinCad
                },
                impresion: {
                    required: desarrolloCompletoOCotizanSinCad
                },
                pallet_sobre_pallet: {
                    required: desarrolloCompletoOCotizanSinCad
                },
                cantidad: {
                    required: desarrolloCompletoOCotizanSinCad
                },
                observacion: {
                    required: true,
                    minlength: 10
                }
            },
            // Specify validation error messages
            messages: {},
            errorClass: "error",
            errorPlacement: function(error, element) {
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
            highlight: function(element, errorClass) {
                $(element)
                    .closest("div.form-group")
                    .addClass("error");
            },
            unhighlight: function(element, errorClass) {
                $(element)
                    .closest("div.form-group")
                    .removeClass("error");
            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid
            submitHandler: function(form) {
                form.submit();
            }
        });

    // FIN VALIDACION

    $("select").change(function() {
        $(this)
            .closest("div.form-group")
            .removeClass("error");
    });

    // -------- JERARQUIAS ------------------

    // Al comienzo los select de jerarquia 2 y 3 comienzan desabilitados y luego son habilitados al cambiar la jerarquia 1

    $("#subhierarchy_id,#subsubhierarchy_id").prop("disabled", true);
    // ajax para relacionar jerarquia 2 con la jerarquia 1 seleccionada
    $("#hierarchy_id").change(function() {
        var val = $(this).val();
        return $.ajax({
            type: "GET",
            url: "/getJerarquia2",
            data: "hierarchy_id=" + val +"&jerarquia2=" +$("#jerarquia2").val(),
            success: function(data) {
                data = $.parseHTML(data);
                if (role == 4) {
                    $("#hierarchy_id").prop("disabled", false);
                    $("#subhierarchy_id").prop("disabled", false);
                }
                $("#subhierarchy_id")
                    .empty()
                    .append(data)
                    .selectpicker("refresh");

                $("#subsubhierarchy_id")
                    .empty()
                    .append(
                        $.parseHTML(
                            '<option value="" disabled selected>Seleccionar Opción</option>'
                        )
                    )
                    .prop("disabled", true)
                    .selectpicker("refresh");
            }
        });
    });

    // ajax para relacionar jerarquia 3 con la jerarquia 2 seleccionada
    $("#subhierarchy_id").change(function() {
        var val = $(this).val();
        return $.ajax({
            type: "GET",
            url: "/getJerarquia3",
            data: "subhierarchy_id=" + val +"&jerarquia3=" +$("#jerarquia3").val(),
            success: function(data) {
                data = $.parseHTML(data);
                if (role == 4) {
                    $("#subsubhierarchy_id").prop("disabled", false);
                }
                $("#subsubhierarchy_id")
                    .empty()
                    .append(data)
                    .selectpicker("refresh");
            }
        });
    });

    // Popular jerarquias en orden
    const populateHierarchies = async () => {
        await $("#hierarchy_id")
            .val($("#jerarquia1").val())
            .triggerHandler("change");
        await $("#subhierarchy_id")
            .val($("#jerarquia2").val())
            .triggerHandler("change");
        $("#subsubhierarchy_id")
            .val($("#jerarquia3").val())
            .selectpicker("refresh");
    };
    // Si no hay jerarquia es que recien ingreso al formulario por lo tanto no populamos los selects
    // de lo contrario si tiene informacion es que se lleno de algun cambio y debemos llenarlo
    if ($("#jerarquia1").val()) populateHierarchies();

    // -------- FIN JERARQUIAS ------------------
    //
    //
    //

    // DESABILITAR CAMPOS SEGUN ROL
    let role = $("#role_id").val();
    // Area de Venta
    if (role == 4 || role == 3) {
        $(
            "#org_venta_id,#largura_hm,#anchura_hm,#area_producto,#rmt,#golpes_largo,#golpes_ancho,#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2,#impresion_1,#impresion_2,#impresion_3,#impresion_4,#impresion_5,#impresion_6,#longitud_pegado,#porcentaje_cera_exterior,#porcentaje_cera_interior,#porcentaje_barniz_interior,#tipo_sentido_onda,#material_asignado,#descripcion_material"
        ).prop("disabled", true);
    }

    // Si el tipo de referencia es 0 => "NO" se bloquean la referencia y el bloqueo referencia
    $("#reference_type")
        .change(() => {
            if ($("#reference_type").val() == 0) {
                $("#reference_id,#bloqueo_referencia")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                // $("#reference_id,#bloqueo_referencia");
            } else {
                $("#reference_id,#bloqueo_referencia")
                    .prop("disabled", false)
                    .selectpicker("refresh");
            }
        })
        .triggerHandler("change");

    // cantidad comienza inabilitado y luego si se selecciona "SI" en pallet este es habilitado
    $("#pallet_sobre_pallet")
        .change(() => {
            if ($("#pallet_sobre_pallet").val() != 1) {
                $("#cantidad")
                    .prop("disabled", true)
                    .val("")
                    .closest("div.form-group")
                    .removeClass("error");
            } else {
                $("#cantidad")
                    .prop("disabled", false)
                    .selectpicker("refresh");
            }
        })
        .triggerHandler("change");

    // Si no se selecciona un carton se puede selccionar un color de carton, si se selecciona un carton desabilitamos color carton
    $("#carton_id")
        .change(() => {
            if ($("#carton_id").val()) {
                $("#carton_color")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            } else {
                $("#carton_color")
                    .prop("disabled", false)
                    .selectpicker("refresh");
            }
        })
        .triggerHandler("change");

    // Habilita/inabilita la vista del campo de "numero de muestras" segun checklist de "muestra"
    $("#muestra").click(function() {
        $("#container-numero-muetras")[this.checked ? "show" : "hide"]();
    });

    // LOGICA DEL FORMULARIO SEGUN TIPO DE SOLICITUD
    //
    //
    //
    //

    // Habilitacion de campos segun tipo de solicitud
    $("#tipo_solicitud")
        .change(function() {
            let tipo_solicitud = $(this).val();
            // Desarrollo Completo
            if (tipo_solicitud == 1) {
                // Bloqueo y limpieza de valores para los siguientes inputs
                disableAndCleanElements("#cad");
                disableCadSelect();

                // Se desbloquean todos los checkbox
                cleanCheckboxs();

                // Desbloqueo y limpieza de valores para los siguientes inputs
                $(
                    "#hierarchy_id,#reference_type,#reference_id,#bloqueo_referencia,#items_set,#veces_item,#carton_id,#style_id,#recubrimiento_id,#rmt,#numero_colores,#color_1_id,#color_2_id,#color_3_id,#color_4_id,#color_5_id,#color_6_id,#pegado,#cera_exterior,#cera_interior,#barniz_interior,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#process_id,#pegado_terminacion,#armado_id,#tipo_sentido_onda,#peso_contenido_caja,#autosoportante,#envase_id,#cajas_altura,#impresion,#pallet_sobre_pallet,#cantidad"
                )
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }
            // Cotiza CAD
            else if (tipo_solicitud == 2) {
                // Se desbloquean todos los checkbox
                cleanCheckboxs();

                enableCadSelect();
                // Desbloqueo y limpieza de valores para los siguientes inputs
                $(
                    "#cad,#carton_id,#numero_colores,#color_1_id,#color_2_id,#color_3_id,#color_4_id,#color_5_id,#color_6_id,#recubrimiento,#pegado,#cera_exterior,#cera_interior,#barniz_interior,#process_id,#pegado_terminacion,#armado_id,#peso_contenido_caja,#autosoportante,#envase_id,#cajas_altura,#impresion,#pallet_sobre_pallet,#cantidad"
                )
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                // Bloqueo y limpieza de valores para los siguientes inputs
                $(
                    "#product_type_id,#reference_type,#reference_id,#bloqueo_referencia,#items_set,#veces_item,#style_id,#rmt,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto"
                )
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                disableHierarchies();
            }

            // Muestra CAD
            else if (tipo_solicitud == 3) {
                enableCadSelect();
                // Bloqueo y limpieza de valores para los siguientes inputs

                // Se bloquean todos los checkbox excepto el de muestra
                $(".custom-control-input:not(#muestra)")
                    .prop("disabled", true)
                    .prop("checked", false);
                // activamos la opcion de muestra dinamicamente
                $("#muestra")
                    .prop("checked", true)
                    .triggerHandler("click");

                // Bloqueo y limpieza de valores para los siguientes inputs
                $(
                    "#product_type_id,#reference_type,#reference_id,#bloqueo_referencia,#style_id,#items_set,#veces_item,#recubrimiento,#rmt,#numero_colores,#color_1_id,#color_2_id,#color_3_id,#color_4_id,#color_5_id,#color_6_id,#pegado,#cera_exterior,#cera_interior,#barniz_interior,#process_id,#pegado_terminacion,#armado_id,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#tipo_sentido_onda,#peso_contenido_caja,#autosoportante,#envase_id,#cajas_altura,#impresion,#pallet_sobre_pallet,#cantidad"
                )
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                // Desbloqueo de inputs
                $("#cad,#carton_id")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                disableHierarchies();
            } // Cotiza sin CAD
            else if (tipo_solicitud == 4) {
                disableCadSelect();
                // Se desbloquean todos los checkbox
                cleanCheckboxs();

                // Desbloqueo y limpieza de valores para los siguientes inputs
                enableAndCleanElements(
                    "#product_type_id,#recubrimiento,#numero_colores,#color_1_id,#color_2_id,#color_3_id,#color_4_id,#color_5_id,#color_6_id,#pegado,#cera_exterior,#cera_interior,#barniz_interior,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#process_id,#pegado_terminacion,#armado_id,#tipo_sentido_onda,#peso_contenido_caja,#autosoportante,#envase_id,#cajas_altura,#impresion,#pallet_sobre_pallet,#cantidad"
                );

                // Bloqueo y limpieza de valores para los siguientes inputs
                disableAndCleanElements(
                    "#cad,#reference_type,#reference_id,#bloqueo_referencia,#items_set,#veces_item,#style_id,#rmt"
                );

                disableHierarchies();
            }
        })
        .triggerHandler("change");

    const cleanCheckboxs = () => {
        $(".custom-control-input")
            .prop("disabled", false)
            .prop("checked", false);
        $("#muestra")
            .prop("checked", false)
            .triggerHandler("click");
    };

    const disableHierarchies = () => {
        $("#hierarchy_id,#subhierarchy_id,#subsubhierarchy_id")
            .prop("disabled", true)
            .val("")
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");
    };

    const disableAndCleanElements = elements => {
        toggleAndCleanElements(elements, true);
    };

    const enableAndCleanElements = elements => {
        toggleAndCleanElements(elements, false);
    };

    const toggleAndCleanElements = (elements, state) => {
        $(elements)
            .prop("disabled", state)
            .val("")
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");
    };

    // Funcionalidades de CAD
    const disableCadSelect = () => {
        $("#cad_input_container").show();
        $("#cad_select_container").hide();
        disableAndCleanElements("#cad_id");
        const elementos = datos_cad
            .map(e => {
                return `#${e}`;
            })
            .join(",");
        disableAndCleanElements(elementos);
        // quitar readonly
        $(elementos).prop("readonly", false);
    };

    const enableCadSelect = () => {
        $("#cad_input_container").hide();
        $("#cad_select_container").show();
        enableAndCleanElements("#cad_id");
    };

    // Segun cad seleccionado se llenan los datos correspondientes al cad

    const datos_cad = [
        "veces_item",
        "externo_largo",
        "externo_ancho",
        "externo_alto",
        "interno_largo",
        "interno_ancho",
        "interno_alto",
        "area_producto",
        "largura_hm",
        "anchura_hm",
        "rayado_c1r1",
        "rayado_r1_r2",
        "rayado_r2_c2"
    ];
    $("#cad_id").change(function() {
        var val = $(this).val();
        console.log(val);
        return $.ajax({
            type: "GET",
            url: "/getCad",
            data: "cad_id=" + val,
            success: function(data) {
                datos_cad.forEach(element => {
                    setValue(element, data);
                });
            }
        });
    });

    const setValue = (val, cad) => {
        $(`#${val}`)
            .prop({ disabled: false, readonly: true })
            .val(cad[val]);
    };
});

// {
//     client_id: "required",
//     descripcion: {
//         required: true,
//         maxlength: 40
//     },
//     tipo_solicitud: "required",
//     // nombre_contacto: "required",
//     // email_contacto: { required: true, email: true },
//     // telefono_contacto: "required telefono",
//     // volumen_venta_anual: "required",
//     // usd: "required",
//     canal_id: "required",
//     // hierarchy_id: "required",
//     subhierarchy_id: "required",
//     subsubhierarchy_id: "required",
//     // Solicita
//     "checkboxes[]": { required: true },
//     numero_muestras: "required",
//     // Referencia
//     reference_type: "required",
//     reference_id: "required",
//     bloqueo_referencia: "required",
//     cad: "required",
//     product_type_id: "required",
//     items_set: "required",
//     veces_item: "required",
//     carton_color: "required",
//     numero_colores: "required",
//     // datos para desarrollo
//     peso_contenido_caja: "required",
//     autosoportante: "required",
//     envase_id: "required",
//     cajas_altura: "required",
//     impresion: "required",
//     pallet_sobre_pallet: "required",
//     cantidad: "required",
//     observacion: {
//         required: true,
//         minlength: 10
//     }
// },
