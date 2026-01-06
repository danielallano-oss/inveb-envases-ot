$(document).ready(function () {

    const notify = (msg = "Complete los campos faltantes", type = "danger") => {
        $.notify(
            {
                message: `<p  class="text-center">${msg}</p> `,
            },
            {
                type,
                animate: {
                    enter: "animated bounceInDown",
                    exit: "animated bounceOutUp",
                },
                // delay: 500000,
                placement: {
                    from: "top",
                    align: "center",
                },
                z_index: 999999,
            }
        );
    };

    // traduccion mensajes de jquery validator
    jQuery.extend(jQuery.validator.messages, {
        required: "Campo obligatorio.",
        remote: "Por favor, rellena este campo.",
        email: "Por favor, ingresa una dirección de correo válida",
        extension: "Por favor, subir solo archivos con una extensión valida",
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
    $.validator.addMethod(
        "filesize",
        function (value, element, param) {
            return this.optional(element) || element.files[0].size <= param;
        },
        "Peso máximo de archivo: 20MB"
    );

    // VALIDACION DE FORMULARIO
    $("#crear-gestion")
        .submit(function (e) {
            e.preventDefault();
            $('#click_diseño_pdf').removeAttr("disabled");
        })
        .validate({

            // Specify validation rules
            rules: {
                management_type_id: "required",
                work_space_id: "required",
                state_id: "required",
                motive_id: "required",
                // titulo: "required",
                observacion: {
                    required: function () {
                        if ($("#tipo_solicitud_ot").val() == 6) {

                            return false;

                        } else {
                            if ($("#management_type_id").val() == 10 || $("#management_type_id").val() == 9) {

                                return false;

                            } else {

                                return true;
                            }

                        }
                    },
                    minlength: function () {
                        if ($("#tipo_solicitud_ot").val() == 6) {

                            return false;
                        } else {
                            return 10;
                        }
                    },

                    //required: true,
                    //minlength: 10,
                    maxlength: 1000,
                },
                "files[]": {
                    // los archivos solo son requeridos cuando el tipo de gestion es de Archivo
                    required: function () {
                        if ($("#role_id").val() == 6 && $("#file_pdf").val() != '') {//No sera requerido cuando se suba un archivo para el lector de PDF
                            return false;
                        } else {
                            if (($("#management_type_id").val() == 3)) {
                                return true;
                            } else {
                                return false;
                            }

                        }
                    },
                    filesize: 20000000,
                    // extension: "doc|pdf"
                },
                click_diseño_pdf: {

                    required: function () {

                        if ($("#management_type_id").val() == 9 && $("#envio_disenador_externo").val() == 'N') {
                            $('#click_diseño_pdf').prop("disabled", true);
                            return true;
                        } else {
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

    $("select").change(function () {
        $(this).closest("div.form-group").removeClass("error");
    });

    const role_id = $("#role_id").val();

    // Si el tipo de gestion es cambio de estado y el estado es "rechazada" se puede seleccionar un area y motivo
    $("#state_id")
        .change(function () {
            if ($("#state_id").val() == 12) {
                $("#work_space_id,#motive_id")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh");
                // $("#motivo").removeClass("d-none");

                const selectObject = document
                    .getElementById("work_space_id")
                    .getElementsByTagName("option");

                // ingeniero y jefe desarrollo
                if (role_id == 5 || role_id == 6) {
                    selectObject[3].disabled = true;
                    selectObject[4].disabled = true;
                    selectObject[5].disabled = true;
                }
                //diseñador y jefe diseño
                else if (role_id == 7 || role_id == 8) {
                    selectObject[4].disabled = true;
                    selectObject[5].disabled = true;
                    selectObject[6].disabled = true;
                } //catalogador y jefe catalogador
                else if (role_id == 9 || role_id == 10) {
                    selectObject[5].disabled = true;
                    selectObject[6].disabled = true;
                } // jefe muesztra y tecnico muestras
                else if (role_id == 13 || role_id == 14) {
                    // selectObject[2].disabled = true;
                    selectObject[4].disabled = true;
                    selectObject[5].disabled = true;
                    selectObject[6].disabled = true;
                }

                $("#work_space_id").selectpicker("refresh");
            } else {
                $("#work_space_id,#motive_id")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh");
                $("#motivo").addClass("d-none");
                $("#muestra").addClass("d-none");
            }
            //llena el tooltip de informacion de acuerdo al estado seleccionado en el gestion
            if ($("#state_id").val() == 2) {
                $('#info_state').attr('data-original-title', "Avanza al area de diseño estructural");
                $('#info_state').show();
                $('#info_state').tooltip();
            } else if ($("#state_id").val() == 5) {
                $('#info_state').attr('data-original-title', "Avanza al area de diseño gráfico");
                $('#info_state').show();
                $('#info_state').tooltip();
            } else if ($("#state_id").val() == 6) {
                $('#info_state').attr('data-original-title', "Avanza al area de precatalogación");
                $('#info_state').show();
                $('#info_state').tooltip();
            } else if ($("#state_id").val() == 7) {
                $('#info_state').attr('data-original-title', "Avanza al area de catalogación");
                $('#info_state').show();
                $('#info_state').tooltip();
            } else if ($("#state_id").val() == 9) {
                $('#info_state').attr('data-original-title', "Perdida estado de culminacion de la OT");
                $('#info_state').show();
                $('#info_state').tooltip();
            } else if ($("#state_id").val() == 10) {
                $('#info_state').attr('data-original-title', "Registra gestión de consultas del cliente con respecto a la OT");
                $('#info_state').show();
                $('#info_state').tooltip();
            } else if ($("#state_id").val() == 11) {
                $('#info_state').attr('data-original-title', "Registra la anulacion de la OT y no permite activar nuevamente. Se debe generar una nueva OT");
                $('#info_state').show();
                $('#info_state').tooltip();
            } else if ($("#state_id").val() == 14) {
                $('#info_state').attr('data-original-title', "Registra gestion de espera por la orden de Compra");
                $('#info_state').show();
                $('#info_state').tooltip();
            } else if ($("#state_id").val() == 15) {
                $('#info_state').attr('data-original-title', "Registra falta de definicion del cliente con respecto a la OT");
                $('#info_state').show();
                $('#info_state').tooltip();
            } else if ($("#state_id").val() == 16) {
                $('#info_state').attr('data-original-title', "Registra el visto bueno de la OT por parte del cliente");
                $('#info_state').show();
                $('#info_state').tooltip();
            } else if ($("#state_id").val() == 20) {
                $('#info_state').attr('data-original-title', "Se detiene el tiempo de proceso de la OT hasta cambiar el estado nuevamente");
                $('#info_state').show();
                $('#info_state').tooltip();
            } else if ($("#state_id").val() == 21) {
                $('#info_state').attr('data-original-title', "Sigue contando tiempo dentro del area de Ventas");
                $('#info_state').show();
                $('#info_state').tooltip();
            }

            if ($("#state_id").val() == 18) {
                $("#observacion").val("Muestras Listas");
            }

        })
        .triggerHandler("change");

    $("#work_space_id").change(function () {
        // Si el estado es rechazada
        if ($("#state_id").val() == 12) {
            $("#motive_id")
                .prop("disabled", false)
                .val("")
                .selectpicker("refresh");
            $("#motivo").removeClass("d-none");
            $("#muestra").addClass("d-none");
            const motiveOptions = setMotives();
            $("#motive_id").html(motiveOptions);
            $("#motive_id").selectpicker("refresh");
        }
    });

    $("#management_type_id")
        .change(function () {
            $("#state_id").triggerHandler("change");
            let tipo_gestion = $(this).val();
            // Cambio de Estado
            if (tipo_gestion == 1) {
                $("#proveedor").addClass("d-none");
                // Bloqueo y limpieza de valores para los siguientes inputs
                $("#work_space_id")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                // Desbloqueo y limpieza de valores para los siguientes inputs
                $("#state_id")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                $("#files").closest("div.form-group").removeClass("error");

                $("#file_pdf").closest("div.form-group").removeClass("error");

                document.getElementById("subida_archivo").style.display = "block";
                document.getElementById("subida_archivo_pdf").style.display = "none";
                document.getElementById("subida_archivo_diseño_pdf").style.display = "none";
                document.getElementById("generar_archivo").style.display = "none";
                document.getElementById("subida_archivo_boceto_pdf").style.display = "none";
            }
            // Consulta
            else if (tipo_gestion == 2) {

                // Activar cualquier area que haya podido ser inactivada por un rechazo
                const selectOptions = document
                    .getElementById("work_space_id")
                    .getElementsByTagName("option");
                console.log(selectOptions);
                // selectOptionsArr = [...selectOptions];
                selectOptionsArr = [].slice.call(selectOptions);
                // console.log(selectOptionsArr);
                selectOptionsArr.map(function (ele) {
                    return (ele.disabled = false);
                });

                // Ocultar Motivo si llegase a estar en display
                $("#motivo").addClass("d-none");
                $("#muestra").removeClass("d-none");
                $("#proveedor").addClass("d-none");
                // Bloqueo y limpieza de valores para los siguientes inputs
                $("#state_id,#motive_id")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                // Desbloqueo y limpieza de valores para los siguientes inputs
                $("#work_space_id")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                $("#files").closest("div.form-group").removeClass("error");

                $("#file_pdf").closest("div.form-group").removeClass("error");

                document.getElementById("subida_archivo").style.display = "block";
                document.getElementById("subida_archivo_pdf").style.display = "none";
                document.getElementById("subida_archivo_diseño_pdf").style.display = "none";
                document.getElementById("generar_archivo").style.display = "none";
                document.getElementById("subida_archivo_boceto_pdf").style.display = "none";
            }
            //  Archivo
            else if (tipo_gestion == 3) {

                // Ocultar Motivo si llegase a estar en display
                $("#motivo").addClass("d-none");
                $("#muestra").addClass("d-none");
                $("#proveedor").addClass("d-none");
                // Bloqueo y limpieza de valores para los siguientes inputs
                $("#state_id,#work_space_id,#motive_id")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                document.getElementById("subida_archivo").style.display = "block";
                document.getElementById("subida_archivo_pdf").style.display = "none";
                document.getElementById("subida_archivo_diseño_pdf").style.display = "none";
                document.getElementById("generar_archivo").style.display = "none";
                document.getElementById("subida_archivo_boceto_pdf").style.display = "none";

            }//  Archivo PDF
            else if (tipo_gestion == 6) {

                // Ocultar Motivo si llegase a estar en display
                $("#motivo").addClass("d-none");
                $("#muestra").addClass("d-none");
                $("#proveedor").addClass("d-none");
                // Bloqueo y limpieza de valores para los siguientes inputs
                $("#state_id,#work_space_id,#motive_id")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                document.getElementById("subida_archivo").style.display = "none";
                document.getElementById("subida_archivo_pdf").style.display = "block";
                document.getElementById("subida_archivo_diseño_pdf").style.display = "none";
                document.getElementById("generar_archivo").style.display = "none";
                document.getElementById("subida_archivo_boceto_pdf").style.display = "none";

            } else if (tipo_gestion == 8) {
                $("#motivo").addClass("d-none");
                $("#muestra").removeClass("d-none");
                $("#proveedor").addClass("d-none");
                $("#gestion-form #state_id")
                    .val('')
                    .prop("disabled", true)
                    .selectpicker("refresh");
                $("#gestion-form #work_space_id")
                    .val('')
                    .prop("disabled", false)
                    .selectpicker("refresh");

                document.getElementById("subida_archivo_diseño_pdf").style.display = "none";
                document.getElementById("generar_archivo").style.display = "none";
                document.getElementById("subida_archivo_boceto_pdf").style.display = "none";

            } else if (tipo_gestion == 9) {

                // Ocultar Motivo si llegase a estar en display
                $("#proveedor").removeClass("d-none");
                $("#muestra").addClass("d-none");

                // Bloqueo y limpieza de valores para los siguientes inputs
                $("#state_id,#work_space_id,#motivo_id")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                $("#proveedor_id")
                    .prop("disabled", false)
                    .prop("required", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                if ($("#envio_disenador_externo").val() == 'N') {
                    document.getElementById("generar_archivo").style.display = "block";
                } else {
                    document.getElementById("generar_archivo").style.display = "none";
                }


                //document.getElementById("generar_archivo").style.display = "block";
                document.getElementById("subida_archivo_diseño_pdf").style.display = "none";
                document.getElementById("subida_archivo_pdf").style.display = "none";
                document.getElementById("subida_archivo").style.display = "none";
                document.getElementById("subida_archivo_boceto_pdf").style.display = "none";

                $("#observacion").val("Se envía a diseño Externo.");

            } else if (tipo_gestion == 10) {

                // Ocultar Motivo si llegase a estar en display
                $("#proveedor").removeClass("d-none");
                $("#muestra").addClass("d-none");

                // Bloqueo y limpieza de valores para los siguientes inputs
                $("#state_id,#work_space_id,#motivo_id")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                $("#proveedor_id")
                    .prop("disabled", false)
                    .prop("required", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");


                document.getElementById("generar_archivo").style.display = "none";
                document.getElementById("subida_archivo_diseño_pdf").style.display = "block";
                document.getElementById("subida_archivo_pdf").style.display = "none";
                document.getElementById("subida_archivo").style.display = "none";
                document.getElementById("subida_archivo_boceto_pdf").style.display = "none";

                getProveedorExterno();

                $("#observacion").val("Se recepciona diseño Externo.");

            }//  Archivo Boceto Clisse PDF
            else if (tipo_gestion == 11) {

                // Ocultar Motivo si llegase a estar en display
                $("#motivo").addClass("d-none");
                $("#muestra").addClass("d-none");
                $("#proveedor").addClass("d-none");
                // Bloqueo y limpieza de valores para los siguientes inputs
                $("#state_id,#work_space_id,#motive_id")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                document.getElementById("subida_archivo").style.display = "none";
                document.getElementById("subida_archivo_pdf").style.display = "none";
                document.getElementById("subida_archivo_diseño_pdf").style.display = "none";
                document.getElementById("generar_archivo").style.display = "none";
                document.getElementById("subida_archivo_boceto_pdf").style.display = "block";

            }
        })
        .triggerHandler("change");

    //Verifica que ya este un archivo de pdf cargado para mostrar los datos en el modal
    $("#file_pdf")
        .change(function () {

            if ($("#file_pdf").val() != '') {
                $('#modal-datos-pdf').modal('toggle');

                let formData = new FormData();
                // Capturamos el archivo para leer los datos
                formData.append('file_pdf', $('input#file_pdf')[0].files[0]);

                // console.log({ formData })
                // console.log($('input#file_pdf')[0].files[0])
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "/leer-pdf",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        // console.log('Enviado al backend')
                        // console.log(response)

                        let data = response.data;

                        $.ajax({
                            type: "POST",
                            url: "/validar-carton-lector-pdf",//Buscamos que el carton exista en la BD
                            data: "codigo_carton=" + data.carton,
                            success: function (data_response) {

                                $("#ot_id").prop("disabled", true).val(data.ot);
                                $("#carton").val(data_response.carton_data);
                                $("#interno_largo").val(data.medidas_interiores[0]);
                                $("#interno_ancho").val(data.medidas_interiores[1]);
                                $("#interno_alto").val(data.medidas_interiores[2]);
                                $("#externo_largo").val(data.medidas_exteriores[0]);
                                $("#externo_ancho").val(data.medidas_exteriores[1]);
                                $("#externo_alto").val(data.medidas_exteriores[2]);
                                $("#largura_hm").val(data.largura);
                                $("#anchura_hm").val(data.anchura);
                                $("#golpes_largo").val(data.golpes_largo);
                                $("#golpes_ancho").val(data.golpes_ancho);
                                $("#area_producto").val(data.area_producto);
                                $("#recorte_adicional").val(data.area_agujeros);
                                $("#process").val(data.proceso);
                                $("#maquila").val(data.maquila);

                            }
                        });

                    }
                });
            }

        });

    //Se guardan los datos PDF del modal
    $("#guardarDatosPDF").on("click", function (e) {

        let formData = new FormData();
        // Capturamos el archivo para leer los datos
        formData.append('file_pdf', $('input#file_pdf')[0].files[0]);
        formData.append('otID', $('input#otID').val());
        formData.append('ot_id', $('input#ot_id').val());
        formData.append('carton', $("#carton").val());
        formData.append('largura_hm', $("#largura_hm").val());
        formData.append('anchura_hm', $("#anchura_hm").val());
        formData.append('area_producto', $("#area_producto").val());
        formData.append('recorte_adicional', $("#recorte_adicional").val());
        formData.append('golpes_largo', $("#golpes_largo").val());
        formData.append('golpes_ancho', $("#golpes_ancho").val());
        formData.append('interno_largo', $("#interno_largo").val());
        formData.append('interno_ancho', $("#interno_ancho").val());
        formData.append('interno_alto', $("#interno_alto").val());
        formData.append('externo_largo', $("#externo_largo").val());
        formData.append('externo_ancho', $("#externo_ancho").val());
        formData.append('externo_alto', $("#externo_alto").val());
        formData.append('process', $("#process").val());
        formData.append('maquila', $("#maquila").val());

        // console.log({ formData })
        // console.log($('input#file_pdf')[0].files[0])

        e.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "/guardar-pdf",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {

                if (data == true) {
                    notify("Datos De OT Actualizados Correctamente", "success");
                    $('#modal-datos-pdf').modal('toggle');
                } else {
                    notify("La OT ingresada en el formulario es errónea", "danger");
                    // $('#modal-datos-pdf').modal('toggle');
                }

            },
        });

    });

    //Manejo click sobre generar diseño pdf
    $("#link_pdf_muestra_envio").on("click", function (e) {
        document.getElementById("click_diseño_pdf").checked = true;
        //$("#click_diseño_pdf").attr('readonly', 'readonly');
    });


    // "Falta Información", "Información Erronea", "Falta Muestra Física", "Formato Imagen Inadecuado", "Medida Erronea", "Descripción de Producto", "Plano mal Acotado", "Error de Digitación", "Error tipo Sustrato", "No viable por restricciones"
    // [1 => "Falta Información", 2 => "Información Erronea", 3 => "Falta Muestra Física", 4 => "Formato Imagen Inadecuado", 5 => "Medida Erronea", 6 => "Descripción de Producto", 7 => "Plano mal Acotado", 8 => "Error de Digitación", 9 => "Error tipo Sustrato", 10 => "No viable por restricciones", 11 => "Falta CAD para corte", 12 => "Falta OT Chileexpress", 13 => "Falta OT Laboratorio"]
    const setMotives = function () {
        let options = [];
        let src;

        //diseñador tecnico y jefe desarrollo
        if (role_id == 5 || role_id == 6) {
            // si esta rechazando al area de venta
            if ($("#work_space_id").val() == 1) {
                src = [
                    { id: "", txt: "Seleccionar..." },
                    { id: 1, txt: "Falta Información" },
                    { id: 2, txt: "Información Erronea" },
                    { id: 3, txt: "Falta Muestra Física" },
                    { id: 10, txt: "No viable por restricciones" },
                ];
            } else if ($("#work_space_id").val() == 6) {
                // si esta rechazando al area de muestra
                src = [
                    { id: "", txt: "Seleccionar..." },
                    { id: 1, txt: "Falta Información" },
                    { id: 2, txt: "Información Erronea" },
                ];
            }
        } //diseñador y jefe diseño
        else if (role_id == 7 || role_id == 8) {
            // si esta rechazando al area de venta
            if ($("#work_space_id").val() == 1) {
                src = [
                    { id: "", txt: "Seleccionar..." },
                    { id: 1, txt: "Falta Información" },
                    { id: 2, txt: "Información Erronea" },
                    { id: 3, txt: "Falta Muestra Física" },
                    { id: 4, txt: "Formato Imagen Inadecuado" },
                    { id: 10, txt: "No viable por restricciones" },
                ];
            } else if ($("#work_space_id").val() == 2) {
                // si esta rechazando al area de desarrollo
                src = [
                    { id: "", txt: "Seleccionar..." },
                    { id: 2, txt: "Información Erronea" },
                    { id: 5, txt: "Medida Erronea" },
                    { id: 10, txt: "No viable por restricciones" },
                ];
            }
        } //catalogador y jefe catalogador
        else if (role_id == 9 || role_id == 10) {
            // si esta rechazando al area de venta
            if ($("#work_space_id").val() == 1) {
                src = [
                    { id: "", txt: "Seleccionar..." },
                    { id: 1, txt: "Falta Información" },
                    { id: 2, txt: "Información Erronea" },
                    { id: 6, txt: "Descripción de Producto" },
                ];
            } else if ($("#work_space_id").val() == 2) {
                // si esta rechazando al area de desarrollo
                src = [
                    { id: "", txt: "Seleccionar..." },
                    { id: 1, txt: "Falta Información" },
                    { id: 5, txt: "Medida Erronea" },
                    { id: 7, txt: "Plano mal Acotado" },
                    { id: 8, txt: "Error de Digitación" },
                ];
            } else if ($("#work_space_id").val() == 3) {
                // si esta rechazando al area de diseño
                src = [
                    { id: "", txt: "Seleccionar..." },
                    { id: 2, txt: "Información Erronea" },
                    { id: 9, txt: "Error tipo Sustrato" },
                ];
            }
        } // tecnico de muestra o jefe de muestras
        else if (role_id == 13 || role_id == 14) {
            src = [
                { id: "", txt: "Seleccionar..." },
                { id: 1, txt: "Falta Información" },
                { id: 2, txt: "Información Erronea" },
                { id: 11, txt: "Falta CAD para corte" },
                { id: 12, txt: "Falta OT Chileexpress" },
                { id: 13, txt: "Falta OT Laboratorio" },
            ];
        }
        src.forEach(function (item) {
            let option =
                "<option value=" + item.id + ">" + item.txt + "</option>";
            options.push(option);
        });

        return options;
    };

    if ($("#cambio_rechazo_devolucion").val() == 4) {
        $("#gestion_actividad").click();
        $("#gestion_muestra").click();
        $("#gestion-form #management_type_id")
            .prop("disabled", true)
            .val(1)
            .selectpicker("refresh");
        $("#gestion-form #state_id")
            .prop("disabled", true)
            .val(12)
            .selectpicker("refresh");
        $("#gestion-form #work_space_id")
            .prop("disabled", true)
            .val(2)
            .selectpicker("refresh");
        $("#motive_id")
            .prop("disabled", false)
            .val("")
            .selectpicker("refresh");

        $("#motivo").removeClass("d-none");
        const motiveOptions = setMotives();
        $("#motive_id").html(motiveOptions);
        $("#motive_id").selectpicker("refresh");


    }

    $("#modal-indicaciones-especiales").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget)
        var cliente = button.data('editar');

        $("#client_indicaciones_view tbody").html('');
        return $.ajax({
            type: "GET",
            url: "/getIndicacionesEspeciales",
            data: "client_id=" + cliente,
            success: function (data) {
                console.log(data);
                if (data) {
                    $("#client_indicaciones_view tbody").html('');
                    $("#client_indicaciones_view tbody").html(data);
                    $('#indicaciones-especiales').modal('show');

                } else {
                    $('#seccion_indicaciones_especiales').hide();
                    $("#client_indicaciones_view tbody").html('');

                }


            },
            error: function (e) {
                console.log(e.responseText);
            },
            async: true
        });
    });

    //Verifica que ya este un archivo de pdf cargado para mostrar los datos en el modal
    $("#file_boceto_pdf")
        .change(function () {

            if ($("#file_boceto_pdf").val() != '') {
                $('#modal-datos-boceto-pdf').modal('toggle');

                //mostrar div loading del modal-datos-boceto-pdf
                $('#modal-datos-boceto-pdf #loading').prop('style', 'display: block');
                $('#modal-datos-boceto-pdf #form-datos').prop('style', 'display: none');
                //Limpiar todos los imputs del form del modal
                $('#modal-datos-boceto-pdf input').val('');
                let formData = new FormData();
                // Capturamos el archivo para leer los datos
                formData.append('file_boceto_pdf', $('input#file_boceto_pdf')[0].files[0]);

                // console.log({ formData })
                // console.log($('input#file_pdf')[0].files[0])
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "/leer-boceto-pdf",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        // console.log('Enviado al backend')
                        // console.log(response)
                        //mostrar div loading del modal-datos-boceto-pdf
                        $('#modal-datos-boceto-pdf #loading').prop('style', 'display: none');
                        $('#modal-datos-boceto-pdf #form-datos').prop('style', 'display: block');
                        let data_clisse_cm2 = response.data_clisse_cm2;
                        let data_colores_id = response.data_colores_id;
                        let data_colores_desc = response.data_colores_desc;
                        let data_porcentaje = response.data_porcentaje;
                        let cant_colores = response.cant_colores;
                        console.log(data_clisse_cm2);
                        console.log(data_colores_id);
                        console.log(data_colores_desc);
                        console.log(data_porcentaje);
                        console.log(cant_colores);
                        $('#cant_colores_pdf').val(cant_colores);
                        //recorrer los arreglos para llenar los campos con for
                        var y = 1
                        var total = 0;
                        for (let i = 0; i < data_clisse_cm2.length; i++) {
                            $("#color_" + y).val(data_colores_desc[i]);
                            $("#color_" + y).prop("readonly", true).prop("disabled", true);
                            $("#color_" + y + "_value").val(data_colores_id[i]);
                            $("#cm2_clisse_color_" + y).val(data_clisse_cm2[i]);
                            $("#cm2_clisse_color_" + y + "_value").val(data_clisse_cm2[i]);
                            $("#impresion_" + y).val(data_porcentaje[i]);
                            $("#impresion_" + y + "_value").val(data_porcentaje[i]);
                            y++;

                            //si no es null data_clisse_cm2 sumar tipo entero y acumularlo en una variable llamada total
                            if (data_clisse_cm2[i] !== null) {
                                total = (total || 0) + parseFloat(data_clisse_cm2[i]);
                            }
                        }

                        $("#total_cm2_clisse").val(total);
                        $("#total_cm2_clisse_value").val(total);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error al leer el boceto PDF:", error);
                        notify("Error al leer el boceto PDF", "danger");
                        $('#modal-datos-boceto-pdf').modal('toggle');
                    }
                });
            }

        });

    //Se guardan los datos PDF del modal
    $("#guardarDatosBocetoPDF").on("click", function (e) {

        e.preventDefault();

        let cant_colores_pdf = $('#cant_colores_pdf').val();
        let cant_colores_ot = document.getElementById("cant_colores_ot").getAttribute("data-val");

        let formData = new FormData();
        if (cant_colores_pdf != cant_colores_ot) {
            notify("Cantidad colores de archivo (.pdf) no coincide con cantidad colores OT (" + cant_colores_ot + "), favor revisar la OT antes de avanzar.", "danger");
        } else {


            // Capturamos el archivo para leer los datos
            formData.append('file_boceto_pdf', $('input#file_boceto_pdf')[0].files[0]);
            formData.append('otID', $('#otID').val());
            formData.append('color_1_value', $('#color_1_value').val());
            formData.append('color_2_value', $("#color_2_value").val());
            formData.append('color_3_value', $("#color_3_value").val());
            formData.append('color_4_value', $("#color_4_value").val());
            formData.append('color_5_value', $("#color_5_value").val());
            formData.append('color_6_value', $("#color_6_value").val());
            formData.append('color_7_value', $("#color_7_value").val());

            formData.append('impresion_1_value', $('#impresion_1_value').val());
            formData.append('impresion_2_value', $("#impresion_2_value").val());
            formData.append('impresion_3_value', $("#impresion_3_value").val());
            formData.append('impresion_4_value', $("#impresion_4_value").val());
            formData.append('impresion_5_value', $("#impresion_5_value").val());
            formData.append('impresion_6_value', $("#impresion_6_value").val());
            formData.append('impresion_7_value', $("#impresion_7_value").val());

            formData.append('cm2_clisse_color_1_value', $("#cm2_clisse_color_1_value").val());
            formData.append('cm2_clisse_color_2_value', $("#cm2_clisse_color_2_value").val());
            formData.append('cm2_clisse_color_3_value', $("#cm2_clisse_color_3_value").val());
            formData.append('cm2_clisse_color_4_value', $("#cm2_clisse_color_4_value").val());
            formData.append('cm2_clisse_color_5_value', $("#cm2_clisse_color_5_value").val());
            formData.append('cm2_clisse_color_6_value', $("#cm2_clisse_color_6_value").val());
            formData.append('cm2_clisse_color_7_value', $("#cm2_clisse_color_7_value").val());
            formData.append('total_cm2_clisse_value', $("#total_cm2_clisse_value").val());


            // console.log({ formData })
            // console.log($('input#file_pdf')[0].files[0])

            e.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "/guardar-boceto-pdf",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {

                    if (data == true) {
                        notify("Datos De OT Actualizados Correctamente", "success");
                        $('#modal-datos-boceto-pdf').modal('toggle');
                    } else {
                        notify("La OT ingresada en el formulario es errónea", "danger");
                        // $('#modal-datos-pdf').modal('toggle');
                    }

                },
            });

        }
    });

});

function getProveedorExterno() {

    var ot = $("#otID").val();

    $.ajax({
        type: "GET",
        url: "/obtener-proveedor-externo-diseño",
        data: "ot=" + ot,
        success: function (data) {
            console.log(data.proveedor_id);

            $("#proveedor_id")
                .val(data.proveedor_id)
                .selectpicker("refresh");

            $("#proveedor_id")
                .prop({ disabled: true, readonly: true });
        }
    });
}


