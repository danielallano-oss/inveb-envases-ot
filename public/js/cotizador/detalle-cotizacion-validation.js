$(document).ready(function () {
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
            "Por favor, ingresa más de {0} caracteres."
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
    const tipo_detalle_id = $("#tipo_detalle_id");
    const corrugado = () => {
      
        if (tipo_detalle_id.val() == 1) {
            if (["62", "67", "72", "77", "84", "90", "92", "95"].indexOf($("#carton_id").val()) >= 0){
                return false;
            }else{
                return true;
            }
        }
        return false;
    };
    const larguraHm = () => {
        if (tipo_detalle_id.val() == 1) {
            if (["62", "67", "72", "77", "84", "90", "92", "95"].indexOf($("#carton_id").val()) >= 0){
                if ($("#cinta_desgarro").val() == 1) {
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
        }
        return false;
    };
    const esquinero = () => {
        if (tipo_detalle_id.val() == 2) {
            return true;
        }
        return false;
    };

    const offset = () => {
        if (proceso.val() == 7 || proceso.val() == 9) {
            return true;
        }
        return false;
    };
    // VALIDACION DE FORMULARIO
    $("#form-detalle-cotizacion")
        .submit(function (e) {
            e.preventDefault();
        })
        .validate({
            // Specify validation rules
            rules: {
                tipo_detalle_id: "required",
                cantidad: {
                    required: true,
                    digits: true,
                },
                /////////// Validaciones de Formulario Corrugado//////////

                // style_id: "required",
                product_type_id: {
                    required: corrugado,
                },
                process_id: {
                    required: corrugado,
                },
                rubro_id: {
                    required: corrugado,
                },
                prepicado_ventilacion: {
                    required: corrugado,
                },
                porcentaje_cera: {
                    required: corrugado,
                },
                area_hc: {
                    required: corrugado,
                },
                anchura: {
                    required: corrugado,
                },
                largura: {
                   
                   required: larguraHm,
                 //   required: corrugado,
                },
                carton_id: {
                    required: corrugado,
                },
                numero_colores: {
                    required: corrugado,
                },
                porcentaje_cera_interno: {
                    digits: true,
                    required: corrugado,
                },
                porcentaje_cera_externo: {
                    digits: true,
                    required: corrugado,
                },
                impresion: {
                    digits: true,
                    required: corrugado,
                },
                golpes_largo: {
                    digits: true,
                    required: corrugado,
                },
                golpes_ancho: {
                    digits: true,
                    required: corrugado,
                },
                // pegado_terminacion: {
                //     required: corrugado,
                // },
                cinta_desgarro: {
                    required: corrugado,
                },
                pallet: {
                    required: corrugado,
                },
                zuncho: {
                    required: corrugado,
                },
                funda: {
                    required: corrugado,
                },
                stretch_film: {
                    required: corrugado,
                },
                armado_automatico: {
                    required: corrugado,
                },
                armado_usd_caja: {
                    required: () => {
                        if (
                            tipo_detalle_id.val() == 1 &&
                            $("#armado_automatico").val() == 1
                        ) {
                            return true;
                        }
                        return false;
                    },
                },
                maquila: {
                    required: corrugado,
                },
                clisse: {
                    required: corrugado,
                },
                matriz: {
                    required: corrugado,
                },
                royalty: {
                    required: corrugado,
                },
                maquila_servicio_id: {
                    required: () => {
                        if (
                            tipo_detalle_id.val() == 1 &&
                            $("#maquila").val() == 1
                        ) {
                            return true;
                        }
                        return false;
                    },
                },
                "detalle_maquila_servicio_id[]": {
                    required: () => {
                        if (
                            $("#maquila").val() == 1
                        ) {
                            return true;
                        }
                        return false;
                    },
                },

                ancho_pliego_cartulina: {
                    required: offset,
                },
                largo_pliego_cartulina: {
                    required: offset,
                },
                precio_pliego_cartulina: {
                    required: offset,
                },
                precio_impresion_pliego: {
                    required: offset,
                },
                gp_emplacado: {
                    required: offset,
                },

                /////////// Validaciones de Formulario Esquinero//////////
                largo_esquinero: {
                    required: esquinero,
                },
                carton_esquinero_id: {
                    required: esquinero,
                },
                cantidad_esquinero: {
                    digits: true,
                    required: esquinero,
                },
                numero_colores_esquinero: {
                    required: esquinero,
                },
                funda_esquinero: {
                    required: esquinero,
                },
                tipo_destino_esquinero: {
                    required: esquinero,
                },
                tipo_camion_esquinero: {
                    required: esquinero,
                },
                clisse_esquinero: {
                    required: esquinero,
                },
                maquila_esquinero: {
                    required: esquinero,
                },
                // hierarchy_id: "",
                subhierarchy_id: {
                    required: function () {
                        return $("#hierarchy_id").val() != "";
                    },
                },
                subsubhierarchy_id: {
                    required: function () {
                        return $("#subhierarchy_id").val() != "";
                    },
                },
                ciudad_id: "required",
                pallets_apilados: {
                    required: esquinero,
                },
            },
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

    // Crear event listener en formulario para limpiar errores de selects
    $("#form-detalle-cotizacion").on("change", "select", function (e) {
        // console.log("delegated select on change remove error");
        $(this).closest("div.form-group").removeClass("error");
        e.stopPropagation();
    });

    // $("select").change(function () {
    //     console.log("select on change remove error");
    //     $(this).closest("div.form-group").removeClass("error");
    // });
});
