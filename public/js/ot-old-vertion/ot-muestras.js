$(document).ready(function () { });

const destinatarios = $("#destinatarios_id");

destinatarios.on("change", function () {
    // console.log("destinatarios", destinatarios.val());

    // Si va a vendedor
    if (destinatarios.val().includes("1")) {
        $(".muestra-vendedor").show();
    } else {
        $(".muestra-vendedor").hide();
    }

    // Si va a diseñador
    if (destinatarios.val().includes("2")) {
        $(".muestra-diseñador").show();
    } else {
        $(".muestra-diseñador").hide();
    }

    // Si va a laboratorio
    if (destinatarios.val().includes("3")) {
        $(".muestra-laboratorio").show();
    } else {
        $(".muestra-laboratorio").hide();
    }

    // Si va a clientes
    if (destinatarios.val().includes("4")) {
        $(".muestra-clientes").show();
    } else {
        $(".muestra-clientes").hide();
    }
});

const role = $("#role_id").val();

$("#modal-muestras").on("show.bs.modal", function (e) {
    // prevent datepicker from firing bootstrap modal "show.bs.modal"
    if (e.namespace === "bs.modal") {
        // Your code here
        console.log(e.relatedTarget.id);
        let btn = $(e.relatedTarget); // e.related here is the element that opened the modal, specifically the row button
        let id = btn.data("id"); // this is how you get the of any `data` attribute of an element
        let muestra_id = $("#muestra_id");
        destinatarios.val("").selectpicker("refresh").change();
        // Si el evento que activa el modal es el de crear cotizacion
        if (e.relatedTarget.id == "crear_muestra") {
            // Limpiar modal si al abrir el modal para crear un detalle habia anteriormente una edicion no terminada de otro detalle,
            // console.log(muestra_id.val(), id);
            limpiarFormMuestra();
            // Dejar fijo "Retira Vendedor" para comentario de vendedor
            $("#comentario_vendedor")
                .val("Retira Vendedor")
                .prop("disabled", true)
                .prop("readonly", true);
            if (muestra_id.val() != "" && muestra_id.val() != null) {
                muestra_id.val("");
                $("#titulo-form-muestra").html("Crear Muestra");
            }
            let client_id = ot ? ot.client_id : $("#client_id").val();
            //let instalacion_cliente = ot ? ot.instalacion_cliente : $("#instalacion_cliente").val();
            // LLenamos los datos para los selectores de clientes si tiene
            var instalacion_cliente = "";
            if (ot) {
                if (ot.instalacion_cliente != null) {
                    instalacion_cliente = ot.instalacion_cliente;
                } else {
                    instalacion_cliente = $("#instalacion_cliente").val();
                }
            } else {
                instalacion_cliente = $("#instalacion_cliente").val();
            }
            $.ajax({
                type: "GET",
                url: "/getContactosCliente",
                data: "client_id=" + client_id + "&instalacion_cliente=" + instalacion_cliente,
                success: function (data) {
                    data = $.parseHTML(data);
                    $(
                        "#contactos_cliente_1,#contactos_cliente_2,#contactos_cliente_3,#contactos_cliente_4"
                    )
                        .empty()
                        .append(data)
                        .selectpicker("refresh");

                    $(".select-contactos-clientes").show();
                    $(".contactos-clientes").show();
                },
            });
            return;
        }

        // De lo contrario tomtamos el id para cargar el detalle al formulario de edicion
        setFormMuestra(id);
        $(".select-contactos-clientes").hide();
        $(".contactos-clientes").hide();
        $("#titulo-form-muestra").html("Editar Muestra ID " + id);
        muestra_id.val(id);
        // Dejar fijo "Retira Vendedor" para comentario de vendedor
        $("#comentario_vendedor")
            .val("Retira Vendedor")
            .prop("disabled", true)
            .prop("readonly", true);
    }
});
// Si al estar creando OT se trata de crear una muestra y luego se cierra el modal sin guardar debemos desmarcar la muestra
$("#modal-muestras").on("hide.bs.modal", function (e) {
    // if (!ot) {
    if ($("#muestra_id").val() == "") {
        $("#muestra").prop("checked", false);
        $("#container-numero-muetras").hide();
    } else {
        $("#muestra").prop("disabled", true).prop("readonly", true);
    }
    // }
});

$("#form-muestra").on(
    "change",
    "#contactos_cliente_1,#contactos_cliente_2,#contactos_cliente_3,#contactos_cliente_4",
    function (e) {
        let numero_contacto = e.currentTarget.id;
        numero_contacto = numero_contacto.substr(numero_contacto.length - 1);
        console.log(
            "current target id",
            numero_contacto.substr(numero_contacto.length - 1)
        );
        // ajax para llenar los datos del contacto luego de seleccionarlo
        const contactos_cliente = $("#contactos_cliente_" + numero_contacto);
        const destinatario = $("#destinatario_" + numero_contacto);
        const comuna = $("#comuna_" + numero_contacto);
        const direccion = $("#direccion_" + numero_contacto);
        // contactos_cliente.on("change", function () {
        var val = contactos_cliente.val();
        let client_id = ot ? ot.client_id : $("#client_id").val();
        //let instalacion_cliente = ot ? ot.instalacion_cliente : $("#instalacion_cliente").val();
        var instalacion_cliente = "";
        if (ot) {
            if (ot.instalacion_cliente != null) {
                instalacion_cliente = ot.instalacion_cliente;
            } else {
                instalacion_cliente = $("#instalacion_cliente").val();
            }
        } else {
            instalacion_cliente = $("#instalacion_cliente").val();
        }
        return $.ajax({
            type: "GET",
            url: "/getDatosContactoInstalacion",
            data: "contactos_cliente=" + val + "&instalation_id=" + instalacion_cliente,
            success: function (data) {
                console.log(data);
                destinatario
                    .val(data.nombre_contacto)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                comuna
                    .val(data.comuna_contacto)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                direccion
                    .val(data.direccion_contacto)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            },
        });
        // });
    }
);

function limpiarFormMuestra() {
    $("#guardarMuestra").show();
    $("#destinatarios_id")
        .prop("disabled", false)
        .prop("readonly", false)
        .val("")
        .selectpicker("refresh")
        .change();
    // limpiar inputs
    $("#form-muestra  .selectpicker,#form-muestra  input:not([type=hidden])")
        .prop("disabled", false)
        .prop("readonly", false)
        .val("")
        .selectpicker("refresh");

    // Remover clases de errores de formulario
    $(".error").removeClass("error");

    // Al crear no se pueden ingresar comentarios asi que deben bloquearse
    $("#form-muestra  #tiempo_unitario,.fecha_corte")
        .prop("disabled", true)
        .prop("readonly", true);

    $("#form-muestra #cad_id").val($("#cad_id").val()).selectpicker("refresh");
    // Si la ot tiene carton precargarlog
    if (ot && ot.carton_id) {
        $("#form-muestra #carton_id").val(ot.carton_id).selectpicker("refresh");
    }
    if (
        (ot && (ot.tipo_solicitud == 2 || ot.tipo_solicitud == 3)) ||
        $("#tipo_solicitud_id").val() == 2 ||
        $("#tipo_solicitud_id").val() == 3
    ) {
        $("#form-muestra #cad_id")
            .prop("readonly", true)
            .prop("disabled", true);
    }

    if (
        (ot && (ot.tipo_solicitud == 1 || ot.tipo_solicitud == 4)) ||
        $("#tipo_solicitud_id").val() == 1 ||
        $("#tipo_solicitud_id").val() == 4 ||
        $("#tipo_solicitud_id").val() == 7
    ) {
        disableCadMuestraSelect();
    } else {
        enableCadMuestraSelect();
    }

    // Si es vendedor solo visualiza los datos
    if (role == 3 || role == 4 || role == 19) {
        $(
            "#form-muestra  #cad,#form-muestra #cad_id,#form-muestra #carton_id,#form-muestra #carton_muestra_id,#form-muestra #pegado_id"
        )
            .prop("disabled", true)
            .prop("readonly", true)
            .selectpicker("refresh");
    }

    // ocultar botones para descargar pdfs
    $(".pdf-muestra").hide();
    $(".direccion-envio-cliente ").hide();
}

function setFormMuestra(muestra_id) {
    let muestra;
    $.ajax({
        type: "GET",
        url: "/getMuestra",
        data: { muestra_id },
        success: function (muestraDB) {
            muestra = muestraDB;
            console.log(muestra);
            destinatarios
                .val(muestra.destinatarios)
                .selectpicker("refresh")
                .change();
            // console.log(muestra);
            $.each(muestra, function (name, val) {
                // console.log(name, val);
                var $el = $('[name="' + name + '"]');
                var type = $el.attr("type");
                // console.log($el, type);
                switch (type) {
                    case "checkbox":
                        console.log("dentro de checkbox", $el, type);
                        if (muestra[name] == 1) {
                            $el.attr("checked", "checked");
                        }
                        break;
                    case "radio":
                        $el.filter('[value="' + val + '"]').attr(
                            "checked",
                            "checked"
                        );
                        break;
                    default:
                        if ($el.is("select")) {
                            // console.log($el, val);
                            $el.val(val).selectpicker("refresh");
                        } else {
                            $el.val(val);
                        }
                }
            });

            // Setear ids para rechazo y/o aprobacion
            $("#terminarMuestraID").val(muestra.id);
            $("#rechazarMuestraID").val(muestra.id);

            $(".pdf-muestra").show();
            $("#link_pdf_muestra").attr(
                "href",
                "https://envases-ot.inveb.cl/generar_etiqueta_muestra_pdf?download=pdf" +
                // "https://test.envases-ot.inveb.cl/generar_etiqueta_muestra_pdf?download=pdf" +

                "&id=" +
                muestra.id +
                "&tipo=producto"
            );
            $("#link_pdf_cliente").attr(
                "href",
                "https://envases-ot.inveb.cl/generar_etiqueta_muestra_pdf?download=pdf" +
                // "https://test.envases-ot.inveb.cl/generar_etiqueta_muestra_pdf?download=pdf" +

                "&id=" +
                muestra.id +
                "&tipo=cliente"
            );

            $("#destinatarios_id")
                .prop("disabled", true)
                .prop("readonly", true)
                .val(muestra.destinatarios_id)
                .selectpicker("refresh")
                .change();

            if (
                (ot && (ot.tipo_solicitud == 1 || ot.tipo_solicitud == 4 || ot.tipo_solicitud == 7)) ||
                $("#tipo_solicitud_id").val() == 1 ||
                $("#tipo_solicitud_id").val() == 4 ||
                $("#tipo_solicitud_id").val() == 7
            ) {
                disableCadMuestraSelect();
            } else {
                enableCadMuestraSelect();
            }

            if (muestra.tiempo_unitario) {
                var fecha = new Date(muestra.tiempo_unitario);
                fecha.setDate(fecha.getDate() + 1);
                let h = `${fecha.getHours()}`.padStart(2, "0");
                let m = `${fecha.getMinutes()}`.padStart(2, "0");
                let tiempo_unitario = h + ":" + m;
                console.log("tiempo_unitario", tiempo_unitario);
                // Setear la fecha en el formato correcto
                $("#tiempo_unitario")
                    .data("DateTimePicker")
                    .date(tiempo_unitario); // or
            }
            // SETEAR FECHAS DE CORTE
            if (muestra.fecha_corte_vendedor) {
                var fecha = new Date(muestra.fecha_corte_vendedor);
                fecha.setDate(fecha.getDate() + 1);
                // Setear la fecha en el formato correcto
                $("fecha_corte_vendedor").datepicker("setDate", fecha); // or
            }
            if (muestra.fecha_corte_diseñador) {
                var fecha = new Date(muestra.fecha_corte_diseñador);
                fecha.setDate(fecha.getDate() + 1);
                // Setear la fecha en el formato correcto
                $("fecha_corte_diseñador").datepicker("setDate", fecha); // or
            }
            if (muestra.fecha_corte_laboratorio) {
                var fecha = new Date(muestra.fecha_corte_laboratorio);
                fecha.setDate(fecha.getDate() + 1);
                // Setear la fecha en el formato correcto
                $("fecha_corte_laboratorio").datepicker("setDate", fecha); // or
            }
            if (muestra.fecha_corte_1) {
                var fecha = new Date(muestra.fecha_corte_1);
                fecha.setDate(fecha.getDate() + 1);
                // Setear la fecha en el formato correcto
                $("fecha_corte_1").datepicker("setDate", fecha); // or
            }
            if (muestra.fecha_corte_2) {
                var fecha = new Date(muestra.fecha_corte_2);
                fecha.setDate(fecha.getDate() + 1);
                // Setear la fecha en el formato correcto
                $("fecha_corte_2").datepicker("setDate", fecha); // or
            }
            if (muestra.fecha_corte_3) {
                var fecha = new Date(muestra.fecha_corte_3);
                fecha.setDate(fecha.getDate() + 1);
                // Setear la fecha en el formato correcto
                $("fecha_corte_3").datepicker("setDate", fecha); // or
            }
            if (muestra.fecha_corte_4) {
                var fecha = new Date(muestra.fecha_corte_4);
                fecha.setDate(fecha.getDate() + 1);
                // Setear la fecha en el formato correcto
                $("fecha_corte_4").datepicker("setDate", fecha); // or
            }

            // Setear numeros de envio si es chileexpress
            if (muestra.comentario_1 == "0") {
                $("#contenedorNumeroEnvio1").show();
            }
            if (muestra.comentario_2 == "0") {
                $("#contenedorNumeroEnvio2").show();
            }
            if (muestra.comentario_3 == "0") {
                $("#contenedorNumeroEnvio3").show();
            }
            if (muestra.comentario_4 == "0") {
                $("#contenedorNumeroEnvio4").show();
            }

            $("#terminarMuestra").hide();
            $("#rechazarMuestra").hide();
            $("#direccion-envio-cliente ").hide();
            // Si es tecnico de muestra
            if (role == 13 || role == 14) {
                // Mientras este en sala de muestra no se puede modificar lo siguiente

                $(
                    // "#form-muestra #cad,#form-muestra #cad_id"
                    "#form-muestra input[type=text], #form-muestra select"
                )
                    .prop("disabled", true)
                    .prop("readonly", true)
                    .selectpicker("refresh");

                $(
                    "#form-muestra #tiempo_unitario,#form-muestra #carton_muestra_id,#form-muestra #cantidad_vendendor,#form-muestra #cantidad_diseñador,#form-muestra #cantidad_laboratorio, #form-muestra #cantidad_1,#cantidad_2,#cantidad_3,#cantidad_4"
                )
                    .prop("disabled", false)
                    .prop("readonly", false)
                    .selectpicker("refresh");
                if (muestra.estado == 1) {
                    $("#rechazarMuestra").show();

                    switch (muestra.destinatarios_id[0]) {
                        case "1":
                            if (muestra.check_fecha_corte_vendedor) {
                                $("#terminarMuestra").show();
                            }
                            break;
                        case "2":
                            if (muestra.check_fecha_corte_diseñador) {
                                $("#terminarMuestra").show();
                            }
                            break;
                        case "3":
                            if (muestra.check_fecha_corte_laboratorio) {
                                $("#terminarMuestra").show();
                            }
                            break;
                        case "4":
                            if (
                                muestra.check_fecha_corte_1 ||
                                muestra.check_fecha_corte_2 ||
                                muestra.check_fecha_corte_3 ||
                                muestra.check_fecha_corte_4
                            ) {
                                $("#terminarMuestra").show();

                                $("#direccion-envio-cliente").show();
                            }
                            break;

                        default:
                            break;
                    }
                }
            }
            // Si es vendedor solo visualiza los datos
            if (role == 3 || role == 4 || role == 19) {
                $(
                    "#form-muestra  #cad,#form-muestra #cad_id,#form-muestra #carton_id,#form-muestra #carton_muestra_id,#form-muestra #pegado_id,#tiempo_unitario,.fecha_corte"
                )
                    .prop("disabled", true)
                    .prop("readonly", true)
                    .selectpicker("refresh");

                // Si es tenico de muestras
            } else if (role == 5 || role == 6) {
                console.log(role, "role");
                // $(
                //     "#form-muestra  .selectpicker,#form-muestra  input:not([type=hidden]):not(.comentario)"
                // )
                //     .prop("disabled", true)
                //     .prop("readonly", true)
                //     .selectpicker("refresh");

                $("#form-muestra #tiempo_unitario,.fecha_corte")
                    .prop("disabled", true)
                    .prop("readonly", true)
                    .selectpicker("refresh");
            }
            $("#guardarMuestra").show();
            if (muestra.estado == 3) {
                $("#guardarMuestra").hide();
            }
        },
    });
}

$("#modal-eliminar-muestra").on("show.bs.modal", function (e) {
    $("#botonEliminarMuestra").data("id", $(e.relatedTarget).data("id"));
});

$("#botonEliminarMuestra").on("click", function (e) {
    let muestra_id = $(this).data("id");

    window.location.href = "/eliminar-muestra/" + muestra_id;
});

$("#check_fecha_corte_diseñador").on("click", function (e) {
    $("#check_fecha_corte_diseñador").val('on');
});
$("#check_fecha_corte_laboratorio").on("click", function (e) {
    $("#check_fecha_corte_laboratorio").val('on');
});
$("#check_fecha_corte_vendedor").on("click", function (e) {
    $("#check_fecha_corte_vendedor").val('on');
});
$("#check_fecha_corte_1").on("click", function (e) {
    $("#check_fecha_corte_1").val('on');
});
$("#check_fecha_corte_2").on("click", function (e) {
    $("#check_fecha_corte_2").val('on');
});
$("#check_fecha_corte_3").on("click", function (e) {
    $("#check_fecha_corte_3").val('on');
});
$("#check_fecha_corte_4").on("click", function (e) {
    $("#check_fecha_corte_4").val('on');
});


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

// Funcionalidades de CAD
const disableCadMuestraSelect = () => {
    $("#cad_input_container_muestra").show();
    $("#cad_select_container_muestra").hide();
};

const enableCadMuestraSelect = () => {
    $("#cad_input_container_muestra").hide();
    $("#cad_select_container_muestra").show();
};

// Ajax on click para crear muestra
$("#guardarMuestraVendedor").on("click", function (e) {
    e.preventDefault();
    let formMuestra = $("#form-muestra");

    // Validamos el formulario
    formMuestra.valid();
    if (!formMuestra.valid()) {
        return false;
    }
    var formulario = formMuestra.serialize();
    return $.ajax({
        type: "POST",
        url: "/crear-muestra",
        data: formulario,
        success: function (data) {
            let muestra = data[0];
            let muestras_id = data[1];
            console.log(data);
            let numero_muestras = 0;
            // Si va a vendedora
            if (destinatarios.val().includes("1")) {
                console.log("retira ventas", numero_muestras);
                numero_muestras += parseInt(
                    muestra.cantidad_vendedor ? muestra.cantidad_vendedor : 0
                );
            }

            // Si va a diseñador
            if (destinatarios.val().includes("2")) {
                console.log("retira diseñador", numero_muestras);
                numero_muestras += parseInt(
                    muestra.cantidad_diseñador ? muestra.cantidad_diseñador : 0
                );
            }

            // Si va a laboratorio
            if (destinatarios.val().includes("3")) {
                console.log("retira laboratorio", numero_muestras);
                numero_muestras += parseInt(
                    muestra.cantidad_laboratorio
                        ? muestra.cantidad_laboratorio
                        : 0
                );
            }
            // Si va a clientes
            if (destinatarios.val().includes("4")) {
                console.log("retira clientes", numero_muestras);
                numero_muestras +=
                    parseInt(muestra.cantidad_1 ? muestra.cantidad_1 : 0) +
                    parseInt(muestra.cantidad_2 ? muestra.cantidad_2 : 0) +
                    parseInt(muestra.cantidad_3 ? muestra.cantidad_3 : 0) +
                    parseInt(muestra.cantidad_4 ? muestra.cantidad_4 : 0);
            }

            $("#container-numero-muetras #numero_muestras")
                .val(numero_muestras)
                .prop("disabled", true)
                .prop("readonly", true);
            $("#form-ot #muestra_id").val(JSON.stringify(muestras_id));
            notify("Muestra Guardada Exitosamente", "success");

            $("#modal-muestras").modal("toggle");
            // Limpiar resultados anteriores
            // $("#resultados input").val("");
        },
    });
});

// $.ajaxSetup({
//     headers: {
//         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
//     },
// });
// Ajax on click para Rechazar muestra
// $("#rechazarMuestra").on("click", function (e) {
//     e.preventDefault();
//     console.log("RECHAZO MUESTRA", $(this).attr("data-id"));
//     // return false;
//     let id = $(this).attr("data-id");
//     return $.ajax({
//         type: "POST",
//         url: "/rechazarMuestra/" + id,
//         success: function (data) {
//             console.log(data);
//         },
//     });
// });

// Ajax on click para Terminar muestra
$("#terminarMuestra").on("click", function (e) {
    e.preventDefault();
    console.log("TERMINAR MUESTRA", $(this).attr("data-id"));
    // return false;
    let id = $(this).attr("data-id");
    return $.ajax({
        type: "POST",
        url: "/terminarMuestra",
        data: id,
        success: function (data) {
            console.log(data);
        },
    });
});

$("#comentario_1").on("change", function (e) {
    if ($(this).val() == "0") {
        $("#contenedorNumeroEnvio1").show();
    } else {
        $("#contenedorNumeroEnvio1").hide();
    }
});

$("#comentario_2").on("change", function (e) {
    if ($(this).val() == "0") {
        $("#contenedorNumeroEnvio2").show();
    } else {
        $("#contenedorNumeroEnvio2").hide();
    }
});
$("#comentario_3").on("change", function (e) {
    if ($(this).val() == "0") {
        $("#contenedorNumeroEnvio3").show();
    } else {
        $("#contenedorNumeroEnvio3").hide();
    }
});
$("#comentario_4").on("change", function (e) {
    if ($(this).val() == "0") {
        $("#contenedorNumeroEnvio4").show();
    } else {
        $("#contenedorNumeroEnvio4").hide();
    }
});
