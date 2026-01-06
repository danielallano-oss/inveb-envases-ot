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

    const role = $("#role_id").val();
    const usuarioMuestra = () => {
        if (role != 13 && role != 14) {
            return false;
        }
        return true;
    };
    const usuarioDesarrollo = () => {
        if (role != 5 && role != 6) {
            return false;
        }
        // si no hay ot creada enviar como si no fuera de desarrollo para que funcione como vendedor
        if (!ot) {
            return false;
        }
        return true;
    };
    // VALIDACION DE FORMULARIO
    $("#form-muestra")
        .submit(function (e) {
            e.preventDefault();
        })
        .validate({
            // Specify validation rules
            rules: {
                "destinatarios_id[]": "required",
                cad_id: {
                    required: usuarioDesarrollo,
                },
                cad: {
                    required: function () {
                        return (
                            usuarioDesarrollo() && ot && ot.tipo_solicitud != 4
                        );
                    },
                },
                carton_id: {
                    required: usuarioDesarrollo,
                },
                pegado_id: {
                    required: usuarioDesarrollo,
                },
                sala_corte_vendedor: {
                    required: usuarioDesarrollo,
                },
                sala_corte_diseñador: {
                    required: usuarioDesarrollo,
                },
                sala_corte_laboratorio: {
                    required: usuarioDesarrollo,
                },
                sala_corte_1: {
                    required: usuarioDesarrollo,
                },
                // destino_vendedor: "required",
                // tiempo_unitario: {
                //     required: usuarioMuestra,
                // },
                cantidad_vendedor: {
                    required: function () {
                        return $("#destinatarios_id").val().includes("1");
                    },
                },

                // comentario_vendedor: {
                //     // required: function () {
                //     //     return $("#destinatarios_id").val().includes("1");
                //     // },
                // },
                cantidad_diseñador: {
                    required: function () {
                        return $("#destinatarios_id").val().includes("2");
                    },
                },
                comentario_diseñador: {
                    required: function () {
                        if (role != 14) {
                            return $("#destinatarios_id").val().includes("2");
                        }
                    },
                },
                cantidad_laboratorio: {
                    required: function () {

                        return $("#destinatarios_id").val().includes("3");


                    },
                },
                comentario_laboratorio: {
                    required: function () {
                        if (role != 14) {
                            return $("#destinatarios_id").val().includes("3");
                        }
                    },
                },
                destinatario_1: {
                    required: function () {
                        return (
                            $(".cliente_1").filter(function () {
                                return $(this).val();
                            }).length > 0 ||
                            $("#destinatarios_id").val().includes("4")
                        );
                    },
                },
                comuna_1: {
                    required: function () {
                        return (
                            $(".cliente_1").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                direccion_1: {
                    required: function () {
                        return (
                            $(".cliente_1").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                cantidad_1: {
                    required: function () {
                        return (
                            $(".cliente_1").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                comentario_1: {
                    required: function () {
                        return (
                            $(".cliente_1").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                destinatario_2: {
                    required: function () {
                        return (
                            $(".cliente_2").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                comuna_2: {
                    required: function () {
                        return (
                            $(".cliente_2").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                direccion_2: {
                    required: function () {
                        return (
                            $(".cliente_2").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                cantidad_2: {
                    required: function () {
                        return (
                            $(".cliente_2").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                comentario_2: {
                    required: function () {
                        return (
                            $(".cliente_2").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                destinatario_3: {
                    required: function () {
                        return (
                            $(".cliente_3").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                comuna_3: {
                    required: function () {
                        return (
                            $(".cliente_3").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                direccion_3: {
                    required: function () {
                        return (
                            $(".cliente_3").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                cantidad_3: {
                    required: function () {
                        return (
                            $(".cliente_3").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                comentario_3: {
                    required: function () {
                        return (
                            $(".cliente_3").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                destinatario_4: {
                    required: function () {
                        return (
                            $(".cliente_4").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                comuna_4: {
                    required: function () {
                        return (
                            $(".cliente_4").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                direccion_4: {
                    required: function () {
                        return (
                            $(".cliente_4").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                cantidad_4: {
                    required: function () {
                        return (
                            $(".cliente_4").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
                },
                comentario_4: {
                    required: function () {
                        return (
                            $(".cliente_4").filter(function () {
                                return $(this).val();
                            }).length > 0
                        );
                    },
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
    $("#form-muestra").on("change", "select", function (e) {
        // console.log("delegated select on change remove error");
        $(this).closest("div.form-group").removeClass("error");
        e.stopPropagation();
    });

    // $("select").change(function () {
    //     console.log("select on change remove error");
    //     $(this).closest("div.form-group").removeClass("error");
    // });
});
