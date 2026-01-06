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

    // VALIDACION DE FORMULARIO
    $("#form-ot")
        .submit(function (e) {
            e.preventDefault();
        })
        .validate({
            // Specify validation rules
            rules: {
                vendedor_id: "required",
                client_id: "required",
                descripcion: {
                    required: true,
                    maxlength: 40,
                },
                tipo_solicitud: "required",
                nombre_contacto: "required",
                email_contacto: { required: true, email: true },
                telefono_contacto: "required telefono",
                canal_id: "required",
                //hierarchy_id: "required",
                /*subhierarchy_id: {
                    required: function () {
                        return $("#hierarchy_id").val() != "";
                    },
                },
                subsubhierarchy_id: {
                    required: function () {
                        return $("#subhierarchy_id").val() != "";
                    },
                },*/
                tipo_producto_estudio : "required",
                cantidad_estudio_bench: "required",
                fecha_maxima_entrega_estudio: "required",
                identificacion_estudio_1:{
                    required: function () {
                        if($('#cantidad_estudio_bench').val()!=''){
                            if($('#cantidad_estudio_bench').val()>=1){
                                return true;
                            }else{
                                return false;
                            }
                         }else{

                            return false;

                        }
                    },
                },
                cliente_estudio_1:{
                    required: function () {
                        if($('#cantidad_estudio_bench').val()!=''){
                            if($('#cantidad_estudio_bench').val()>=1){
                                return true;
                            }else{
                                return false;
                            }
                         }else{

                            return false;

                        }
                    },
                },
                descripcion_estudio_1:{
                    required: function () {
                        if($('#cantidad_estudio_bench').val()!=''){
                            if($('#cantidad_estudio_bench').val()>=1){
                                return true;
                            }else{
                                return false;
                            }
                         }else{

                            return false;

                        }
                    },
                },
                "checkboxes[]":{

                    required: function () {
                        if( $('#check_estudio_bct').prop('checked') ||
                            $('#check_estudio_ect').prop('checked') ||
                            $('#check_estudio_bct_humedo').prop('checked') ||
                            $('#check_estudio_flat').prop('checked') ||
                            $('#check_estudio_humedad').prop('checked') ||
                            $('#check_estudio_porosidad_ext').prop('checked') ||
                            $('#check_estudio_espesor').prop('checked') ||
                            $('#check_estudio_cera').prop('checked') ||
                            $('#check_estudio_porosidad_int').prop('checked') ||
                            $('#check_estudio_flexion_fondo').prop('checked') ||
                            $('#check_estudio_gramaje').prop('checked') ||
                            $('#check_estudio_composicion_papeles').prop('checked') ||
                            $('#check_estudio_cobb_interno').prop('checked') ||
                            $('#check_estudio_cobb_externo').prop('checked') ||
                            $('#check_estudio_flexion_4_puntos').prop('checked') ||
                            $('#check_estudio_medidas').prop('checked') ||
                            $('#check_estudio_impresion').prop('checked')
                            ){
                            $('#div_check_ensayos').removeClass("error")
                            return false;
                        }else{
                            $('#div_check_ensayos').addClass("error")
                            return true;
                        }
                    },
                },
                "check_estudio_bct_value":{
                    required: function () {
                        if($('#check_estudio_bct').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
                    },
                },
                "check_estudio_ect_value":{
                    required: function () {
                        if($('#check_estudio_ect').prop('checked') ){
                            return true;
                         }else{
                            $("#check_estudio_bct_value").prop("disabled", true);
                            return false;
                        }
                    },
                },
                "check_estudio_bct_humedo_value":{
                    required: function () {
                        if($('#check_estudio_bct_humedo').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
                    },
                },
                "check_estudio_flat_value":{
                    required: function () {
                        if($('#check_estudio_flat').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
                    },
                },
                "check_estudio_humedad_value":{
                    required: function () {
                        if($('#check_estudio_humedad').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
                    },
                },
                "check_estudio_porosidad_ext_value":{
                    required: function () {
                        if($('#check_estudio_porosidad_ext').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
                    },
                },
                "check_estudio_espesor_value":{
                    required: function () {
                        if($('#check_estudio_espesor').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
                    },
                },
                "check_estudio_cera_value":{
                    required: function () {
                        if($('#check_estudio_cera').prop('checked') ){
                            $('#div_check_ensayos').addClass("error")
                            return true;
                         }else{
                            $('#div_check_ensayos').removeClass("error")
                            return false;
                        }
                    },
                },
                "check_estudio_porosidad_int_value":{
                    required: function () {
                        if($('#check_estudio_porosidad_int').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
                    },
                },
                "check_estudio_flexion_fondo_value":{
                    required: function () {
                        if($('#check_estudio_flexion_fondo').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
                    },
                },
                "check_estudio_gramaje_value":{
                    required: function () {
                        if($('#check_estudio_gramaje').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
                    },
                },
                "check_estudio_composicion_papeles_value":{
                    required: function () {
                        if($('#check_estudio_composicion_papeles').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
                    },
                },
                "check_estudio_cobb_interno_value":{
                    required: function () {
                        if($('#check_estudio_cobb_interno').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
                    },
                },
                "check_estudio_cobb_externo_value":{
                    required: function () {
                        if($('#check_estudio_cobb_externo').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
                    },
                },
                "check_estudio_flexion_4_puntos_value":{
                    required: function () {
                        if($('#check_estudio_flexion_4_puntos').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
                    },
                },
                "check_estudio_medidas_value":{
                    required: function () {
                        if($('#check_estudio_medidas').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
                    },
                },
                "check_estudio_impresion_value":{
                    required: function () {
                        if($('#check_estudio_impresion').prop('checked') ){
                            return true;
                         }else{
                            return false;
                        }
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
    $("#form-ot").on("change", "select", function (e) {
        // console.log($(this), e);
        $(this).closest("div.form-group").removeClass("error");
        e.stopPropagation();
    });

    // ajax para llenar los contactos del cliente
    const client_id = $("#client_id");
    client_id.on("change", function () {

        var val = client_id.val();
        return $.ajax({
            type: "GET",
            url: "/getInstalacionesCliente",
            data: "client_id=" + val,
            success: function (data) {
                data = $.parseHTML(data);
                $("#instalacion_cliente")
                    .empty()
                    .append(data)
                    .selectpicker("refresh");

                getIndicacionesEspeciales(val);
            },
            error: function(e) {
                console.log(e.responseText);
            },
            async:true
        });

    });

    const instalation_id = $("#instalacion_cliente");
    instalation_id.on("change", function () {

        var val = instalation_id.val();
        return $.ajax({
            type: "GET",
            url: "/getInformacionInstalacion",
            data: "instalation_id=" + val,
            success: function (data) {
                contactos = $.parseHTML(data.contactos);
                $("#contactos_cliente")
                    .empty()
                    .append(contactos)
                    .selectpicker("refresh");
                $("#altura_pallet")
                    .val(data.altura_pallet);
                $("#permite_sobresalir_carga")
                    .val(data.sobresalir_carga)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                $("#tamano_pallet_type_id")
                    .val(data.tipo_pallet)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                $("#bulto_zunchado")
                    .val(data.bulto_zunchado)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                $("#formato_etiqueta")
                    .val(data.formato_etiqueta)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                $("#etiquetas_pallet")
                    .val(data.etiquetas_pallet);
                // $("#termocontraible")
                //     .val(data.termocontraible)
                //     .selectpicker("refresh")
                //     .closest("div.form-group")
                //     .removeClass("error");
                $("#pais_id")
                    .val(data.pais_mercado_destino)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                $("#pallet_qa_id")
                    .val(data.certificado_calidad)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                $("#fsc")
                    .val(data.fsc)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                $("#fsc_instalation")
                    .val(data.fsc);
            },
            error: function(e) {
                console.log(e.responseText);
            },
            async:true
        });
    });

    // ajax para llenar los datos del contacto luego de seleccionarlo
    const contactos_cliente = $("#contactos_cliente");
    const nombre_contacto = $("#nombre_contacto");
    const email_contacto = $("#email_contacto");
    const telefono_contacto = $("#telefono_contacto");
    contactos_cliente.on("change", function () {
        var val = contactos_cliente.val();
        return $.ajax({
            type: "GET",
            url: "/getDatosContactoInstalacion",
            data: "contactos_cliente=" + val + "&instalation_id=" + instalation_id.val(),
            success: function (data) {
                nombre_contacto
                    .val(data.nombre_contacto)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                email_contacto
                    .val(data.email_contacto)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                telefono_contacto
                    .val(data.telefono_contacto)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            },
            error: function(e) {
                console.log(e.responseText);
            },
            async:true
        });
    });
});


