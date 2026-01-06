$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    var form_cotizacion = $("#formCotizacion");
    var cotizacion_id = $("#cotizacion_id");
    // Ajax on click para calcular resultados
    $("#generarPrecotizacion").on("click", function (e) {
        console.log("calcular");
        e.preventDefault();

        // Validamos el formulario
        form_cotizacion.valid();
        if (!form_cotizacion.valid()) {
            return false;
        }

        // loading gif
        $("#loading").show();

        var formulario = form_cotizacion.serializeArray();
        formulario.push({
            name: "detalles",
            value: JSON.stringify(
                window.detalles_cotizaciones.map((a) => a.id)
            ),
            // value: JSON.stringify([
            //     window.detalles_cotizaciones[0],
            //     window.detalles_cotizaciones[1],
            // ]),
        });

        var idCotizacion = cotizacion_id.val() || 0;
        var client_id = $("#client_id").val() || 0;
        return $.ajax({
            type: "POST",
            url: "/cotizador/generarPrecotizacion/" + idCotizacion + "/" + client_id,
            data: formulario,
            success: function (cotizacion) {
                $("#loading").hide(); // hide ajax loader

                window.detalles_cotizaciones = cotizacion.detalles;
                $("#cotizacion_id").val(cotizacion.id);
                $("#titulo-cotizacion").html("Cotización N° " + cotizacion.id);
                notify("Resultados Actualizados", "success");
                contenedorResultados.show();
                renderTablasResultados();

                $("#paso_cuatro").css("background-color", " #7ae091");
            },
        });
    });

    $("#generarPrecotizacionExterno").on("click", function (e) {
        console.log("calcular");
        e.preventDefault();
        // Validamos el formulario
        form_cotizacion.valid();
        if (!form_cotizacion.valid()) {
            return false;
        }

        // loading gif
        $("#loading").show();

        var formulario = form_cotizacion.serializeArray();
        formulario.push({
            name: "detalles",
            value: JSON.stringify(
                window.detalles_cotizaciones.map((a) => a.id)
            ),
            // value: JSON.stringify([
            //     window.detalles_cotizaciones[0],
            //     window.detalles_cotizaciones[1],
            // ]),
        });

        var idCotizacion = cotizacion_id.val() || 0;
        var client_id = $("#client_id").val() || 0;
        return $.ajax({
            type: "POST",
            url: "/cotizador/generarPrecotizacionExterno/" + idCotizacion + "/" + client_id,
            data: formulario,
            success: function (cotizacion) {
                $("#loading").hide(); // hide ajax loader

                window.detalles_cotizaciones = cotizacion.detalles;
                $("#cotizacion_id").val(cotizacion.id);
                $("#titulo-cotizacion").html("Cotización N° " + cotizacion.id);
                notify("Resultados Actualizados", "success");
                contenedorResultados.show();
                renderTablasResultados();

                $("#paso_cuatro").css("background-color", " #7ae091");
            },
        });
    });


    $("#observacion_cliente").focusout(function (e) {
        // ask the user and keep his/her choice
        if ($("#observacion_cliente").val()) {
            $("#observacion-cliente-modal").html(
                '"' + $("#observacion_cliente").val() + '"'
            );
            $("#modal-observacion-cliente").modal("show");
        }
    });
    $("#confirmarObservacion").on("click", function (e) {
        e.preventDefault();
        $("#modal-observacion-cliente").modal("hide");
    });
    $("#eliminarObservacion").on("click", function (e) {
        e.preventDefault();
        $("#modal-observacion-cliente").modal("hide");
        $("#observacion_cliente").val("");
    });
    // Solicitar Aprobacion
    $("#solicitarAprobacion").on("click", function (e) {
        e.preventDefault();
        var formulario = form_cotizacion.serializeArray();
        var idCotizacion = cotizacion_id.val() || 0;
        
        return $.ajax({
            type: "POST",
            url: "/cotizador/solicitar_aprobacion_nuevo/"+ idCotizacion,
           // data: {id:idCotizacion},
            success: function (data) {
                console.log(data);
                var mensaje = data.margen == 0 ? "#aprobado" : "#poraprobar";
                window.location = data.url + mensaje;
            },
        });
    });
    // Enviar correo de cotizacion
    $("#form-envio-pdf").on("submit", function (e) {
        e.preventDefault();
        return $.ajax({
            type: "POST",
            url: "/cotizador/enviar_pdf",
            data: $("#form-envio-pdf").serializeArray(),
            success: function (data) {
                // console.log(data);

                notify("Correo enviado exitosamente", "success");
                $("#modal-enviar-pdf").modal("hide");
                $("#email,#nombre").val("");
            },
        });
    });
    // Duplicar Aprobacion
    $("#duplicarCotizacion").on("click", function (e) {
        e.preventDefault();
        $("#modal-duplicar-cotizacion").modal("show");
    });
    $("#confirmarDuplicarCotizacion").on("click", function (e) {
        e.preventDefault();
        $("#duplicarCotizacionForm").submit();
    });

    // Retomar Cotizacion
    // $("#retomarCotizacion").on("click", function (e) {
    //     e.preventDefault();

    //     var idCotizacion = cotizacion_id.val() || 0;

    //     return $.ajax({
    //         type: "POST",
    //         url: "/cotizador/retomarCotizacion/" + idCotizacion,
    //         // data: formulario,
    //         success: function (data) {
    //             console.log(data);
    //             var mensaje = data.margen == 0 ? "#aprobado" : "#poraprobar";
    //             window.location = data.url + mensaje;
    //         },
    //     });
    // });

    // FIN calculo de resultados
    /*if ($("#pallets_apilados").val() == false) {
        $("#pallets_apilados").selectpicker("val", 2);
    }*/
    if ($("#comision").val() == false) {
        $("#comision").val(0);
    }

    $(".collapse").on("show.bs.collapse", function () {
        console.log("asd");
        $(".collapse.show").each(function () {
            $(this).collapse("hide");
        });
    });

    // $("#client_id").filter(function () {}).length == 0;
    $("#client_id").on("changed.bs.select", function () {
        // console.log($.trim($(this).val()).length == 0);
        if ($.trim($(this).val()).length == 0) {
            $("#paso_uno").css("background-color", " #fff");
        } else {
            $("#paso_uno").css("background-color", " #7ae091");
        }
    });

    $("#moneda_id,#dias_pago").on("changed.bs.select", function () {
        // console.log($.trim($(this).val()).length == 0);
        if (
            $.trim($("#moneda_id").val()).length == 0 ||
            $.trim($("#dias_pago").val()).length == 0
        ) {
            $("#paso_tres").css("background-color", " #fff");
        } else {
            $("#paso_tres").css("background-color", " #7ae091");
        }
    });
    // comision

    // ajax para llenar los contactos del cliente
    //const client_id = $("#client_id");
    // ajax para llenar los contactos del cliente
    const client_id = $("#client_id");
    client_id.on("change", function () {
        if($("#instalacion_cliente_id").val()==''){
            $("#contactos_cliente")
                .empty()
                .selectpicker("refresh");
            $("#nombre_contacto")
                .val('')
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
            $("#email_contacto")
                .val('')
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
            $("#telefono_contacto")
                .val('')
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        }
        
        var val = client_id.val();
        return $.ajax({
            type: "GET",
            url: "/getInstalacionesClienteCotiza",
            data: "client_id=" + val,
            success: function (data) { 
                console.log(data);
                data_select = $.parseHTML(data.html);
                data_clasificacion_select = $.parseHTML(data.html_clasificacion);
                $("#instalacion_cliente")
                    .empty()
                    .append(data_select)
                    .selectpicker("refresh");
                    
                if($("#instalacion_cliente_id").val()!=''){
                    $("#instalacion_cliente")
                        .val($("#instalacion_cliente_id").val())
                        .selectpicker("refresh")
                        .triggerHandler("change");
                }

                $("#clasificacion_cliente")
                    .empty()
                    .append(data_clasificacion_select)
                    .selectpicker("refresh");
                    
                $("#clasificacion_cliente")
                    .val(data.clasificacion)
                    .selectpicker("refresh");

            },
            error: function(e) {
                console.log(e.responseText);
            },
            async:true
        });
      
    })
    .triggerHandler("change");
    /*client_id
        .on("change", function () {
            var val = client_id.val();
            return $.ajax({
                type: "GET",
                url: "/getContactosCliente",
                data: "client_id=" + val,
                success: function (data) {
                    data = $.parseHTML(data);
                    $("#contactos_cliente")
                        .empty()
                        .append(data)
                        .selectpicker("refresh");
                },
            });
        })
        .triggerHandler("change");
    */

        const instalation_id = $("#instalacion_cliente");
        instalation_id.on("change", function () {
            /*$("#nombre_contacto")
                .val('')
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
            $("#email_contacto")
                .val('')
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
            $("#telefono_contacto")
                .val('')
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");*/
                
            var val = instalation_id.val();
            return $.ajax({
                type: "GET",
                url: "/getInformacionInstalacion",
                data: "instalation_id=" + val,
                success: function (data) {
                    contactos = $.parseHTML(data.contactos);
                    console.log(contactos);
                   
                    $("#contactos_cliente")
                        .empty()
                        .append(contactos)
                        .selectpicker("refresh");

                   
                    /*var itemSelect = document.getElementById('contactos_cliente');
                    var index = $(itemSelect).find('[value='+contacto+']').index();
                    console.log(index);*/
                   
                    if($("#instalacion_cliente_id").val()!=''){
                        var index = getIndex($("#nombre_contacto").val());
                        console.log(index);
                        $("#contactos_cliente")
                            .val(index)
                            .selectpicker("refresh");
                       
                    }
                    
                },
                error: function(e) {
                    console.log(e.responseText);
                },
                async:true
            });
        }).triggerHandler("change");
    // ajax para llenar los datos del contacto luego de seleccionarlo
    const contactos_cliente = $("#contactos_cliente");
    const nombre_contacto = $("#nombre_contacto");
    const email_contacto = $("#email_contacto");
    const telefono_contacto = $("#telefono_contacto");
    contactos_cliente.on("change", function () {
       /// alert("change contacto cliente");
        var val = contactos_cliente.val();
        return $.ajax({
            type: "GET",
           // url: "/getDatosContacto",
            //data: "contactos_cliente=" + val + "&client_id=" + client_id.val(),
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
        });
    });
}).triggerHandler("change");

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

function getIndex(value) {
    var selectList = document.getElementById('contactos_cliente');
    var optArray = selectList.children;
    var i = 1;
    var value_search= value.replace(/ /g,'');
    var result=-1;
    for(i=0;i<optArray.length;i++){
        var opt = optArray[i];
        var value_compare=opt.innerText.replace(/ /g,'');
      

        if (value_compare == value_search) result= i-1;
    }
    
        
      
      
    
    return result; // If value isn't exist
  }
