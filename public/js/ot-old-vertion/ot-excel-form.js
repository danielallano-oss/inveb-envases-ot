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

});
