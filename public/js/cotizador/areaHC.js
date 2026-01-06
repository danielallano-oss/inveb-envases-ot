$(document).ready(function () {
    // Seleccionar calculo completo
    $("#tipo_calculo").val(1);
    $("#tipo_calculo").change(function () {
        // al cambiar el tipo de calculo limpiamos formulario
        $(
            "#formAreaHC .calculo_inputs .selectpicker,#formAreaHC .calculo_inputs input"
        )
            .prop("disabled", false)
            .prop("readonly", false)
            .val("")
            .selectpicker("refresh");

        // Remover clases de errores de formulario
        $(".error").removeClass("error");

        // Limpiar resultados anteriores
        $("#resultados input").val("");

        var tipo_calculo = $(this).val();

        // Completo
        if (tipo_calculo == 1) {
            // ocultar ect si estuviese visible
            $("#ect_input_container").hide();
            $("#prepicado_ventilacion")
                .prop("disabled", false)
                .prop("readonly", false)
                .val("")
                .selectpicker("refresh");
                
                
$("#formAreaHC #prepicado_ventilacion,#formAreaHC #numero_colores,#formAreaHC #envase_id").parent().parent().parent().show()
$("#formAreaHC #contenido_caja,#formAreaHC #areahc_pallets_apilados,#formAreaHC #cajas_apiladas_por_pallet,#formAreaHC #filas_columnares_por_pallet,#formAreaHC #rmt").parent().parent().show()

                $("#formAreaHC #style_id,#formAreaHC #areahc_product_type_id,#formAreaHC #onda_id,#formAreaHC #process_id").parent().parent().parent().show()
                $("#formAreaHC #interno_largo,#formAreaHC #interno_ancho,#formAreaHC #interno_alto,#formAreaHC #traslape,").parent().parent().show()
        } else if (tipo_calculo == 2) {
            //Area HC

            // Bloquear campos innecesarios para el calculo de area hc
            $(
                "#formAreaHC #rubro_id,#formAreaHC #envase_id,#formAreaHC #contenido_caja,#formAreaHC #areahc_pallets_apilados,#formAreaHC #cajas_apiladas_por_pallet,#formAreaHC #filas_columnares_por_pallet,#formAreaHC #carton_color,#formAreaHC #numero_colores,#formAreaHC #rmt,#formAreaHC #prepicado_ventilacion"
            )
                .prop("disabled", true)
                .prop("readonly", true)
                .val("")
                .selectpicker("refresh");
                
                


                
$("#formAreaHC #prepicado_ventilacion,#formAreaHC #numero_colores,#formAreaHC #envase_id").parent().parent().parent().hide()
                $("#formAreaHC #contenido_caja,#formAreaHC #areahc_pallets_apilados,#formAreaHC #cajas_apiladas_por_pallet,#formAreaHC #filas_columnares_por_pallet,#formAreaHC #rmt").parent().parent().hide()

$("#formAreaHC #onda_id,#formAreaHC #rubro_id,#formAreaHC #carton_color").parent().parent().parent().hide()
                $("#formAreaHC #ect_min_ingresado").parent().parent().hide()

                $("#formAreaHC #style_id,#formAreaHC #areahc_product_type_id,#formAreaHC #onda_id,#formAreaHC #process_id").parent().parent().parent().show()
                $("#formAreaHC #interno_largo,#formAreaHC #interno_ancho,#formAreaHC #interno_alto,#formAreaHC #traslape").parent().parent().show()



            // ocultar ect si estuviese visible
            $("#ect_input_container").hide();
        } else if (tipo_calculo == 3) {
            // Bloquear campos innecesarios para seleccion de carton
            $(
                "#formAreaHC #interno_largo,#formAreaHC #interno_ancho,#formAreaHC #interno_alto,#formAreaHC #style_id,#formAreaHC #traslape,#formAreaHC #areahc_product_type_id,#formAreaHC #process_id,#formAreaHC #envase_id,#formAreaHC #contenido_caja,#formAreaHC #areahc_pallets_apilados,#formAreaHC #cajas_apiladas_por_pallet,#formAreaHC #filas_columnares_por_pallet,#formAreaHC #numero_colores,#formAreaHC #rmt,#formAreaHC #prepicado_ventilacion"
            )
                .prop("disabled", true)
                .prop("readonly", true)
                .val("")
                .selectpicker("refresh");
            ect_input_container;
            $("#ect_input_container").show();

            $("#formAreaHC #prepicado_ventilacion,#formAreaHC #numero_colores,#formAreaHC #envase_id").parent().parent().parent().hide()
            $("#formAreaHC #contenido_caja,#formAreaHC #areahc_pallets_apilados,#formAreaHC #cajas_apiladas_por_pallet,#formAreaHC #filas_columnares_por_pallet,#formAreaHC #rmt").parent().parent().hide()

            
            $("#formAreaHC #style_id,#formAreaHC #areahc_product_type_id,#formAreaHC #onda_id,#formAreaHC #process_id").parent().parent().parent().hide()
            $("#formAreaHC #interno_largo,#formAreaHC #interno_ancho,#formAreaHC #interno_alto,#formAreaHC #traslape").parent().parent().hide()
            
            $("#formAreaHC #onda_id,#formAreaHC #rubro_id,#formAreaHC #carton_color").parent().parent().parent().show()
            $("#formAreaHC #ect_min_ingresado").parent().parent().show()
        }
    });
    // Ajax on click para calcular resultados
    $("#sincronizarAreaHC").click(function (e) {
        e.preventDefault();
        if ($("#carton_color").val()) {
            $("#mensajeCalculoCarton").show();
            $("#texto-calculo-hc").html("Cartón estimado, favor validar este dato con desarrollo");
            
        }
        if ($("#interno_largo").val()) {
            $("#mensajeCalculoAHC").show();
            $("#texto-calculo-hc").html("Área Hoja Corrugada estimada, favor validar este dato con desarrollo");
        }
        $("#modal-confirmacion-calculo-hc").modal("show");
    });
    // Ajax on click para calcular resultados
    $("#confirmarCalculoHC").click(function (e) {
        e.preventDefault();
        $("#modal-calculo-hc").modal("hide");
        $("#modal-confirmacion-calculo-hc").modal("hide");
        if ($("#carton_color").val()) {
            notify(
                "Cartón estimado, favor validar este dato con desarrollo",
                "danger"
            );
            
        }
        if ($("#interno_largo").val()) {
            notify(
                "Área Hoja Corrugada estimada, favor validar este dato con desarrollo",
                "danger"
            );
        }

        if ($("#formAreaHC #interno_largo").val())
            $("#largo").val($("#formAreaHC #interno_largo").val());
        if ($("#formAreaHC #interno_ancho").val())
            $("#ancho").val($("#formAreaHC #interno_ancho").val());
        if ($("#formAreaHC #interno_alto").val())
            $("#alto").val($("#formAreaHC #interno_alto").val());
        if ($("#formAreaHC #areahc_product_type_id").val())
            $("#product_type_id")
                .val($("#formAreaHC #areahc_product_type_id").val())
                .selectpicker("refresh");
        if ($("#formAreaHC #process_id").val())
            $("#process_id")
                .val($("#formAreaHC #process_id").val())
                .selectpicker("refresh");
        if ($("#formAreaHC #rubro_id").val())
            $("#rubro_id")
                .val($("#formAreaHC #rubro_id").val())
                .selectpicker("refresh");
        if ($("#formAreaHC #numero_colores").val())
            $("#numero_colores")
                .val($("#formAreaHC #numero_colores").val())
                .selectpicker("refresh");
        if ($("#formAreaHC #areahc").val())
            $("#area_hc").val($("#formAreaHC #areahc").val());
        if ($("#codigo_carton_id").val())
            $("#carton_id")
                .val($("#codigo_carton_id").val())
                .selectpicker("refresh");
        $("#limpiarAreaHC").click();
        $("#sincronizarAreaHC").hide();
    });

    $("#modal-calculo-hc").on("show.bs.modal", function (e) {
        let btn = $(e.relatedTarget); // e.related here is the element that opened the modal, specifically the row button
        let id = btn.data("id");
        // Si el evento que activa el modal es de calcular area
        if (e.relatedTarget.id == "calculoHC") {
            $("#tipo_calculo").val(3).change();
        } else {
            $("#tipo_calculo").val(2).change();
        }
    });

    // Ajax on click para calcular resultados
    $("#guardarAreaHC").click(function (e) {
        console.log("calcular");
        e.preventDefault();

        // Validamos el formulario
        $("#formAreaHC").valid();
        if (!$("#formAreaHC").valid()) {
            return false;
        }

        var formulario = $("#formAreaHC").serialize();

        var tipo_calculo = $("#tipo_calculo").val();
        return $.ajax({
            type: "POST",
            url: "/cotizador/calcularAreaHC",
            data: formulario,
            success: function (data) {
                console.log(data);
                // Limpiar resultados anteriores
                $("#resultados input").val("");

                // Completo
                if (tipo_calculo == 1) {
                    $("#externo_largo").val(data.externo_largo);
                    $("#externo_alto").val(data.externo_alto);
                    $("#externo_ancho").val(data.externo_ancho);
                    $("#areahc").val(data.areahc.toFixed(3));
                    if (data.rmt == "-") {
                        $("#rmt_resultado").val();
                    } else {
                        $("#rmt_resultado").val(data.rmt.toFixed(2));
                    }
                    if (data.ect_min == "-") {
                        $("#ect_min").val();
                    } else {
                        $("#ect_min").val(data.ect_min.toFixed(2));
                    }

                    // Datos carton seleccionado
                    if (data.carton_seleccionado) {
                        console.log(data.carton_seleccionado);
                        $("#codigo_carton").val(
                            data.carton_seleccionado.codigo
                        );
                        $("#codigo_carton_id").val(data.carton_seleccionado.id);
                        $("#ect_min_carton").val(
                            data.carton_seleccionado.ect_min
                        );
                    } else {
                        $("#codigo_carton_id").val("");
                        $("#codigo_carton").val(
                            "No hay cartón que cumpla requerimientos"
                        );
                        $("#ect_min_carton").val("");
                    }
                } else if (tipo_calculo == 2) {
                    //Area HC
                    $("#externo_largo").val(data.externo_largo);
                    $("#externo_alto").val(data.externo_alto);
                    $("#externo_ancho").val(data.externo_ancho);
                    $("#areahc").val(data.areahc.toFixed(3));
                } else if (tipo_calculo == 3) {
                    //carton
                    // Datos carton seleccionado
                    if (data.carton_seleccionado) {
                        $("#codigo_carton_id").val(data.carton_seleccionado.id);
                        console.log(data.carton_seleccionado);
                        $("#codigo_carton").val(
                            data.carton_seleccionado.codigo
                        );
                        $("#ect_min_carton").val(
                            data.carton_seleccionado.ect_min
                        );
                    } else {
                        $("#codigo_carton_id").val("");
                        $("#codigo_carton").val(
                            "No hay cartón que cumpla requerimientos"
                        );
                        $("#ect_min_carton").val("");
                    }
                }

                $("#sincronizarAreaHC").show();
            },
        });
    });
    // FIN calculo de resultados

    // Limpieza de inputs
    $("#limpiarAreaHC").click(function (e) {
        console.log("limpiar", e);
        e.preventDefault();
        // limpiar inputs
        $(
            "#formAreaHC .calculo_inputs .selectpicker,#formAreaHC .calculo_inputs input"
        )
            .prop("disabled", false)
            .prop("readonly", false)
            .val("")
            .selectpicker("refresh");

        // Remover clases de errores de formulario
        $(".error").removeClass("error");
    });

    // Comportamientos del formulario

    $("#formAreaHC #style_id").change(() => {
        // Al elegir estilos cambiamos el tipo de producto q pueden seleccionar
        if (["2", "3", "4", "12"].includes($("#style_id").val())) {
            const product_type_id = $("#areahc_product_type_id").val();
            // console.log($("#areahc_product_type_id").val());
            $("#areahc_product_type_id")
                .html(
                    '<option value="">Seleccionar...</option><option value="3"> Cajas</option>'
                )
                .selectpicker("refresh");
            $("#areahc_product_type_id").val(product_type_id).selectpicker("refresh");
        } else {
            const product_type_id = $("#areahc_product_type_id").val();
            $("#areahc_product_type_id")
                .html(
                    '<option value="">Seleccionar...</option><option value="4"> Fondo</option><option value="5"> Tapa</option>'
                )
                .selectpicker("refresh");
            $("#areahc_product_type_id").val(product_type_id).selectpicker("refresh");
        }

        // Si es alguno de los siguientes estilos se debe ingresar traslape o gap
        if (
            ["3", "14", "12", "16"].includes($("#formAreaHC #style_id").val())
        ) {
            $("#traslape").prop("readonly", false).val("");
        } else {
            $("#traslape").prop("readonly", true).val("");
        }
    });
});

// CODIGO PARA VALIDAR JERARQUIAS-RUBRO

// Al comienzo los select de jerarquia 2 y 3 comienzan desabilitados y luego son habilitados al cambiar la jerarquia 1

// $("#subhierarchy_id,#subsubhierarchy_id").prop("disabled", true);
// // ajax para relacionar jerarquia 2 con la jerarquia 1 seleccionada
// $("#hierarchy_id").change(function () {
//     var jerarquia_id = $(this).val();
//     // console.log(jerarquia_id);
//     // if (jerarquia_id) {
//     //     $("#rubro_id").val("").prop("disabled", true);
//     // } else {
//     //     $("#rubro_id").prop("disabled", false);
//     // }

//     // // Si tenemos ya un rubro seleccionado
//     var rubro = "";
//     if ($("#rubro_id").val()) {
//         rubro = "&rubro_id=" + $("#rubro_id").val();
//     }
//     // Cargar jerarquia 2
//     return $.ajax({
//         type: "GET",
//         url: "/cotizador/getJerarquia2AreaHC",
//         data: "hierarchy_id=" + jerarquia_id + rubro,
//         success: function (data) {
//             data = $.parseHTML(data);
//             // if (role == 4) {
//             $("#hierarchy_id").prop("disabled", false);
//             $("#subhierarchy_id").prop("disabled", false);
//             // }
//             $("#subhierarchy_id")
//                 .empty()
//                 .append(data)
//                 .selectpicker("refresh");

//             $("#subsubhierarchy_id")
//                 .empty()
//                 .append(
//                     $.parseHTML(
//                         '<option value="" disabled selected>Seleccionar Opción</option>'
//                     )
//                 )
//                 .prop("disabled", true)
//                 .selectpicker("refresh");
//         },
//     });
// });
// $("#rubro_id").change(function () {
//     $("#hierarchy_id").val("").change();
// });

// // ajax para relacionar jerarquia 3 con la jerarquia 2 seleccionada
// $("#subhierarchy_id").change(function () {
//     var val = $(this).val();
//     var rubro = "";
//     if ($("#rubro_id").val()) {
//         rubro = "&rubro_id=" + $("#rubro_id").val();
//     } else {
//     }
//     return $.ajax({
//         type: "GET",
//         url: "/cotizador/getJerarquia3ConRubro",
//         data: "subhierarchy_id=" + val + rubro,
//         success: function (data) {
//             data = $.parseHTML(data);
//             // if (role == 4) {
//             $("#subsubhierarchy_id").prop("disabled", false);
//             // }
//             $("#subsubhierarchy_id")
//                 .empty()
//                 .append(data)
//                 .selectpicker("refresh");
//         },
//     });
// });

// // Segun la jerarquia 3 seleccionada si no hay rubro traemos el rubro correspondiente
// $("#subsubhierarchy_id").change(function () {
//     var val = $(this).val();
//     if ($("#rubro_id").val()) {
//     } else {
//     }
//     return $.ajax({
//         type: "GET",
//         url: "/cotizador/getRubro",
//         data: "subsubhierarchy_id=" + val,
//         success: function (data) {
//             console.log(data);
//             $("#rubro_id").val(data).selectpicker("refresh");
//         },
//     });
// });

// FIN CODIGO JERARQUIA-RUBRO
