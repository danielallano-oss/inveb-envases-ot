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

    // VALIDACION DE FORMULARIO
    $("#form-codigo-material")
        .submit(function (e) {
            e.preventDefault();
            console.log("submit");
        })
        .validate({
            // Specify validation rules
            rules: {
                sufijo_id: "required",
                prefijo_ot: "required",
                "prefijo[]": {
                    required: function (e) {
                        return false;
                        console.log(e);
                        return (
                            $("#prefijo").val() != "" &&
                            !jQuery.isEmptyObject($("#prefijo").val())
                        );
                    },
                },
                descripcion: "required",
            },
            // Specify validation error messages
            messages: {
                sufijo_id: {
                    required: "Campo obligatorio.",
                },
                prefijo_ot: {
                    required: "Campo obligatorio.",
                },
                "prefijo[]": {
                    required: "Campo obligatorio.",
                },
                descripcion: {
                    required: "Debe ingresar una Descripción",
                },
            },
            errorClass: "error",
            errorPlacement: function (error, element) {
                console.log(element);
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

    $("#prefijo_ot")
        .change(() => {
            // Si esta vacio
            if (jQuery.isEmptyObject($("#prefijo_ot").val())) {
                $("#prefijo")
                    .html(
                        '<option value="EN"> EN</option><option value="ENT"> ENT</option>'
                    )
                    .selectpicker("refresh");
            } else {
                $options = "";
                switch ($("#prefijo_ot").val()) {
                    case "EN":
                        $options = '<option value="ENT"> ENT</option>';
                        break;
                    case "ENT":
                        $options = '<option value="EN"> EN</option>';
                        break;
                    // case "ENR":
                    //     $options =
                    //         '<option value="EN"> EN</option><option value="ENT"> ENT</option>';
                    //     break;
                    default:
                        break;
                }
                $("#prefijo").html($options).selectpicker("refresh");
            }
        })
        .triggerHandler("change");

    $("#prefijo").val("").selectpicker("refresh");

    // $("#prefijo")
    //     .change(() => {
    //         // Si esta vacio
    //         if (jQuery.isEmptyObject($("#prefijo").val())) {
    //             $("#prefijo")
    //                 .selectpicker("refresh")
    //                 .closest("div.form-group")
    //                 .addClass("error");

    //             $("#crearCodigoMaterial").prop("disabled", true);
    //             // $("#reference_id,#bloqueo_referencia");
    //         } else {
    //             $("#crearCodigoMaterial").prop("disabled", false);
    //             // $("#reference_id,#bloqueo_referencia")
    //             //     .prop("disabled", false)
    //             //     .selectpicker("refresh");
    //         }
    //     })
    //     .triggerHandler("change");
});
