$(document).ready(function () {
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    // MASCARAS NUMERICAS
    const volumenMask = IMask(volumen_venta_anual, thousandsOptions);
    var cintasXcaja = IMask(cintas_x_caja, thousandsOptions);
    const usdMask = IMask(usd, thousandsOptions);
    var areaProductoMask = IMask(area_producto, fourDecimalsOptions);
    var recorteAdicionalMask = IMask(recorte_adicional, cuatroDecimalsOptions);
    // -------- JERARQUIAS ------------------
    // const tipo = $('#tipo').val();

    // console.log('TIPO' + tipo);
    // Al comienzo los select de jerarquia 2 y 3 comienzan desabilitados y luego son habilitados al cambiar la jerarquia 1

    $("#subhierarchy_id,#subsubhierarchy_id,#hierarchy_id").prop("disabled", true);
    // ajax para relacionar jerarquia 2 con la jerarquia 1 seleccionada
    $("#hierarchy_id").change(function () {
        var val = $(this).val();
        return $.ajax({
            type: "GET",
            url: "/getJerarquia2",
            data: "hierarchy_id=" + val + "&jerarquia2=" + $("#jerarquia2").val(),
            success: function (data) {
                data = $.parseHTML(data);
                // if (role == 4) {
                //$("#hierarchy_id").prop("disabled", false);
                $("#subhierarchy_id").prop("disabled", false);
                // }
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
            },
        });
    });

    // ajax para relacionar jerarquia 3 con la jerarquia 2 seleccionada
    $("#subhierarchy_id").change(function () {
        var val = $(this).val();
        return $.ajax({
            type: "GET",
            url: "/getJerarquia3",
            data: "subhierarchy_id=" + val + "&jerarquia3=" + $("#jerarquia3").val(),
            success: function (data) {
                data = $.parseHTML(data);
                // if (role == 4) {
                $("#subsubhierarchy_id").prop("disabled", false);
                // }
                $("#subsubhierarchy_id")
                    .empty()
                    .append(data)
                    .selectpicker("refresh");
            },
        });
    });

    // $("#canal_id").change(function () {
    //     var val = $(this).val();
    //     switch (val) {
    //         case "1":
    //             $("#hierarchy_id")
    //                 .val(3)
    //                 .selectpicker("refresh")
    //                 .triggerHandler("change");
    //             break;
    //         case "2":
    //             $("#hierarchy_id")
    //                 .val(5)
    //                 .selectpicker("refresh")
    //                 .triggerHandler("change");
    //             break;
    //         case "3":
    //             $("#hierarchy_id")
    //                 .val(4)
    //                 .selectpicker("refresh")
    //                 .triggerHandler("change");
    //             break;
    //         case "4":
    //             $("#hierarchy_id")
    //                 .val(2)
    //                 .selectpicker("refresh")
    //                 .triggerHandler("change");
    //             break;
    //         case "5":
    //             $("#hierarchy_id")
    //                 .val(1)
    //                 .selectpicker("refresh")
    //                 .triggerHandler("change");
    //             break;
    //         default:
    //             $("#hierarchy_id")
    //                 .val('')
    //                 .selectpicker("refresh")
    //                 .triggerHandler("change");
    //             break;
    //     }


    // });

    $("#canal_id").change(function () {
        var val = $(this).val();
        switch (val) {
            case "1":
                $("#hierarchy_id")
                    .val(3)
                    .selectpicker("refresh")
                    .triggerHandler("change");
                break;
            case "2":
                $("#hierarchy_id")
                    .val(5)
                    .selectpicker("refresh")
                    .triggerHandler("change");
                break;
            case "3":
                $("#hierarchy_id")
                    .val(4)
                    .selectpicker("refresh")
                    .triggerHandler("change");
                break;
            case "4":
                $("#hierarchy_id")
                    .val(2)
                    .selectpicker("refresh")
                    .triggerHandler("change");
                break;
            case "5":
                $("#hierarchy_id")
                    .val(1)
                    .selectpicker("refresh")
                    .triggerHandler("change");
                break;
            case "6":
                $("#hierarchy_id")
                    .val(6)
                    .selectpicker("refresh")
                    .triggerHandler("change");
                break;
            default:
                $("#hierarchy_id")
                    .val('')
                    .selectpicker("refresh")
                    .triggerHandler("change");
                break;
        }


    });

    //Se bloquean todos los campos COLOR-CERA-BARNIZ hasta que seleccionen algun tipo de recubrimiento
    const validacion_color_cera_barniz = () => {

        if ($("#coverage_internal_id").val() == '' || $("#coverage_external_id").val() == '') {
            $("#impresion,#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6,#cm2_clisse_color_7"
            ).prop("disabled", true)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        }

    }

    // TIPO DE DISEÑO --- Cargar complejidad segun tipo de diseño ( campos habilitados para Jefe de Diseño o el Diseñador )
    const functionDesignType = () => {

        if (role == 4 || role == 6 || role == 7 || role == 8 || role == 19 || (role == 8 && tipo == 'create')) {

            if ($("#design_type_id").val() != '') {

                var design_type_id = $("#design_type_id").val();
                return $.ajax({
                    type: "GET",
                    url: "/getDesignType",
                    data: "design_type_id=" + design_type_id,
                    success: function (data) {
                        $("#complejidad").val(data.complejidad).prop("disabled", true)
                        $("#indicador_facturacion_diseno_grafico").val(data.indicador_facturacion_diseno_grafico).prop("disabled", true)
                    },
                });

            } else if ($("#design_type_id").val() == "") {

                $("#complejidad").val("").prop("disabled", true)
                $(document).find("#indicador_facturacion_diseno_grafico").val("").prop("disabled", true)

            } else {

                $("#complejidad").prop("disabled", true)
                $("#indicador_facturacion_diseno_grafico").prop("disabled", true)
            }

        } else {

            $("#design_type_id").prop("disabled", true)
            $("#complejidad").prop("disabled", true)
            $("#indicador_facturacion_diseno_grafico").prop("disabled", true)

        }
    }

    // Funcion cuando cambia un TIPO DE DISEÑO ---
    const functionDesignTypeChange = () => {

        if (role == 4 || role == 6 || role == 7 || role == 8 || role == 19 || (role == 8 && tipo == 'create')) {

            $("#design_type_id")
                .change(() => {

                    if ($("#design_type_id").val() != '') {

                        var design_type_id = $("#design_type_id").val();
                        return $.ajax({
                            type: "GET",
                            url: "/getDesignType",
                            data: "design_type_id=" + design_type_id,
                            success: function (data) {
                                $("#complejidad").val(data.complejidad).prop("disabled", true)
                                $("#indicador_facturacion_diseno_grafico").val(data.indicador_facturacion_diseno_grafico).prop("disabled", true)
                            },
                        });

                    } else if ($("#design_type_id").val() == "") {

                        $("#complejidad").val("").prop("disabled", true)
                        $(document).find("#indicador_facturacion_diseno_grafico").val("").prop("disabled", true)


                    } else {

                        $("#complejidad").prop("disabled", true)
                        $("#indicador_facturacion_diseno_grafico").prop("disabled", true)
                    }
                })
                .triggerHandler("change");
        } else {

            $("#design_type_id").prop("disabled", true)
            $("#complejidad").prop("disabled", true)
            $("#indicador_facturacion_diseno_grafico").prop("disabled", true)

        }
    }


    //IMPRESIÓN -- Validacion de impresion para el numero de colores
    const functionImpresion = () => {

        $("#impresion")
            .change(() => {

                if ($("#impresion").val() === '1') { // 1 => "Offset"

                    //                     if($("#planta_id").val()  == '' || $("#planta_id").val()  != 3){// Si la planta esta vacia se queda vacia y si era Osorno se queda Osorno

                    //                         $("$("#planta_id").val()")
                    //                             .prop("disabled", false)
                    //                             .val("")
                    //                             .selectpicker("refresh")
                    //                             .closest("div.form-group")
                    //                             .removeClass("error");
                    //                     }
                    if (role === '4' || role === '19') {//para el vendedor

                        // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                        // $("#impresion,#coverage_external_id,#coverage_internal_id")
                        //     .prop("disabled", false)
                        //     .val("")
                        //     .selectpicker("refresh")
                        //     .closest("div.form-group")
                        //     .removeClass("error");


                        $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6,#cm2_clisse_color_7")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    } else {

                        //se limpian solo estos campos para los otros roles
                        $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    }

                    $("#design_type_id").html(
                        `<option value="">Seleccionar...</option>
                        <option value="1"> Sin impresión o solo cambio de cartón de una referencia (conservando color de papel tapa exterior y tipo de onda del cartón). </option>
                        <option value="2"> Diseño de 1 a 4 colores con antecedentes gráficos editables en texto y reubicación o incorporación de elementos sin vectorizar. Adaptación como mejora productiva e EEII. </option>
                        <option value="3"> Idem tipo 2, pero con vectorización adicional de figuras y/o solicitud de estires de tinta. </option>
                        <option value="4"> Adaptación a flexo cartón desde otro sistma de impresión, propuesta reduccción de colores y/o trabajo con prueba de color digital. Propuestas de diseño nuevo o inédito. Manipulación de imágenes fotográficas. Certificación FSC. </option>
                        <option value="5"> Cajas offset o alta gráfica. </option>
                        <option value="6"> Cajas tiro y retiro. </option>`
                    )
                        .selectpicker("refresh");

                    $("#design_type_id")
                        .val(5)
                        .prop("disabled", true)
                        .selectpicker("refresh");

                    functionDesignType();

                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                        <option value="1"> 1 </option>
                        <option value="2"> 2 </option>
                        <option value="3"> 3 </option>
                        <option value="4"> 4 </option>
                        <option value="5"> 5 </option>
                        <option value="6"> 6 </option>`
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");

                    // if ($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() != 4){// Si el recubrimiento externo es Barniz UV se selecciona automatico si y no entra en esta condicion
                    //     $("#barniz_uv").html(
                    //         `<option value="">Seleccionar...</option>
                    //         <option value="1"> Si </option>
                    //         <option value="0"> No </option>`
                    //     )
                    //     .prop("disabled", false)
                    //     .selectpicker("refresh");

                    //     $("#barniz_uv,#porcentanje_barniz_uv")
                    //         .prop("disabled", false)
                    //         .val("")
                    //         .selectpicker("refresh")
                    //         .closest("div.form-group")
                    //         .removeClass("error");
                    // }

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    // getListaCartonOffset(1);

                } else if ($("#impresion").val() === '2') { //2 => "Flexografía"

                    //                     if($("#planta_id").val()  == '' || $("#planta_id").val()  != 3){// Si la planta esta vacia se queda vacia y si era Osorno se queda Osorno
                    //                         $("$("#planta_id").val()")
                    //                             .prop("disabled", false)
                    //                             .val("")
                    //                             .selectpicker("refresh")
                    //                             .closest("div.form-group")
                    //                             .removeClass("error");
                    //                     }

                    if (role === '4' || role === '19') {//para el vendedor

                        // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                        // $("#impresion,#coverage_external_id,#coverage_internal_id")
                        //     .prop("disabled", false)
                        //     .val("")
                        //     .selectpicker("refresh")
                        //     .closest("div.form-group")
                        //     .removeClass("error");


                        $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6,#cm2_clisse_color_7")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    } else {

                        //se limpian solo estos campos para los otros roles
                        $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    }

                    $("#design_type_id").html(
                        `<option value="">Seleccionar...</option>
                        <option value="2"> Diseño de 1 a 4 colores con antecedentes gráficos editables en texto y reubicación o incorporación de elementos sin vectorizar. Adaptación como mejora productiva e EEII. </option>
                        <option value="3"> Idem tipo 2, pero con vectorización adicional de figuras y/o solicitud de estires de tinta. </option>
                        <option value="4"> Adaptación a flexo cartón desde otro sistma de impresión, propuesta reduccción de colores y/o trabajo con prueba de color digital. Propuestas de diseño nuevo o inédito. Manipulación de imágenes fotográficas. Certificación FSC. </option>`
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");

                    functionDesignTypeChange();

                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                        <option value="1"> 1 </option>
                        <option value="2"> 2 </option>
                        <option value="3"> 3 </option>
                        <option value="4"> 4 </option>
                        <option value="5"> 5 </option>
                        <option value="6"> 6 </option>`
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");

                    // if ($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() != 4){// Si el recubrimiento externo es Barniz UV se selecciona automatico si y no entra en esta condicion

                    //     $("#barniz_uv").html(
                    //         `<option value="">Seleccionar...</option>
                    //         <option value="1"> Si </option>
                    //         <option value="0"> No </option>`
                    //     )
                    //     .prop("disabled", false)
                    //     .selectpicker("refresh");

                    //     $("#barniz_uv,#porcentanje_barniz_uv")
                    //         .prop("disabled", false)
                    //         .val("")
                    //         .selectpicker("refresh")
                    //         .closest("div.form-group")
                    //         .removeClass("error");
                    // }
                    //getListaCartonOffset(2);
                } else if ($("#impresion").val() === '3') { //3 => "Flexografía Alta Gráfica"
                    //                     if($("#planta_id").val()  == '' || $("#planta_id").val()  != 1){// Si la planta esta vacia se selecciona automaticamente la planta Buin

                    //                         $("$("#planta_id").val()")
                    //                             .prop("disabled", true)
                    //                             .val("1")
                    //                             .selectpicker("refresh")
                    //                             .closest("div.form-group")
                    //                             .removeClass("error");
                    //                     }

                    if (role === '4' || role === '19') {//para el vendedor

                        // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                        // $("#impresion,#coverage_external_id,#coverage_internal_id")
                        //     .prop("disabled", false)
                        //     .val("")
                        //     .selectpicker("refresh")
                        //     .closest("div.form-group")
                        //     .removeClass("error");


                        $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6,#cm2_clisse_color_7")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    } else {

                        //se limpian solo estos campos para los otros roles
                        $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    }

                    $("#design_type_id").html(
                        `<option value="">Seleccionar...</option>
                        <option value="1"> Sin impresión o solo cambio de cartón de una referencia (conservando color de papel tapa exterior y tipo de onda del cartón). </option>
                        <option value="2"> Diseño de 1 a 4 colores con antecedentes gráficos editables en texto y reubicación o incorporación de elementos sin vectorizar. Adaptación como mejora productiva e EEII. </option>
                        <option value="3"> Idem tipo 2, pero con vectorización adicional de figuras y/o solicitud de estires de tinta. </option>
                        <option value="4"> Adaptación a flexo cartón desde otro sistma de impresión, propuesta reduccción de colores y/o trabajo con prueba de color digital. Propuestas de diseño nuevo o inédito. Manipulación de imágenes fotográficas. Certificación FSC. </option>
                        <option value="5"> Cajas offset o alta gráfica. </option>
                        <option value="6"> Cajas tiro y retiro. </option>`
                    )
                        .selectpicker("refresh");

                    $("#design_type_id")
                        .val(5)
                        .prop("disabled", true)
                        .selectpicker("refresh");

                    functionDesignType();

                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                        <option value="1"> 1 </option>
                        <option value="2"> 2 </option>
                        <option value="3"> 3 </option>
                        <option value="4"> 4 </option>
                        <option value="5"> 5 </option>
                        <option value="6"> 6 </option>
                        <option value="7"> 7 </option>`
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");

                    // if ($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() != 4){// Si el recubrimiento externo es Barniz UV se selecciona automatico si y no entra en esta condicion
                    //     $("#barniz_uv").html(
                    //         `<option value="">Seleccionar...</option>
                    //         <option value="1"> Si </option>`
                    //     )
                    //     .prop("disabled", false)
                    //     .selectpicker("refresh");

                    //     $("#barniz_uv,#porcentanje_barniz_uv")
                    //         .prop("disabled", false)
                    //         .val("")
                    //         .selectpicker("refresh")
                    //         .closest("div.form-group")
                    //         .removeClass("error");
                    // }

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    //  getListaCartonOffset(3);
                } else if ($("#impresion").val() === '4') { //4 => "Flexografía Tiro y Retiro"

                    //                     if($("#planta_id").val()  == '' || $("#planta_id").val()  != 2){// Si la planta esta vacia se selecciona automaticamente la planta Tiltil

                    //                         $("$("#planta_id").val()")
                    //                             .prop("disabled", true)
                    //                             .val("2")
                    //                             .selectpicker("refresh")
                    //                             .closest("div.form-group")
                    //                             .removeClass("error");
                    //                     }

                    if (role === '4' || role === '19') {//para el vendedor

                        // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                        // $("#impresion,#coverage_external_id,#coverage_internal_id")
                        //     .prop("disabled", false)
                        //     .val("")
                        //     .selectpicker("refresh")
                        //     .closest("div.form-group")
                        //     .removeClass("error");


                        $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6,#cm2_clisse_color_7")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    } else {

                        //se limpian solo estos campos para los otros roles
                        $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    }

                    $("#design_type_id").html(
                        `<option value="">Seleccionar...</option>
                        <option value="1"> Sin impresión o solo cambio de cartón de una referencia (conservando color de papel tapa exterior y tipo de onda del cartón). </option>
                        <option value="2"> Diseño de 1 a 4 colores con antecedentes gráficos editables en texto y reubicación o incorporación de elementos sin vectorizar. Adaptación como mejora productiva e EEII. </option>
                        <option value="3"> Idem tipo 2, pero con vectorización adicional de figuras y/o solicitud de estires de tinta. </option>
                        <option value="4"> Adaptación a flexo cartón desde otro sistma de impresión, propuesta reduccción de colores y/o trabajo con prueba de color digital. Propuestas de diseño nuevo o inédito. Manipulación de imágenes fotográficas. Certificación FSC. </option>
                        <option value="5" selected="selected"> Cajas offset o alta gráfica. </option>
                        <option value="6"> Cajas tiro y retiro. </option>`
                    )
                        .selectpicker("refresh");

                    $("#design_type_id")
                        .val(6)
                        .prop("disabled", true)
                        .selectpicker("refresh");

                    functionDesignType();

                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                        <option value="1"> 1 </option>
                        <option value="2"> 2 </option>
                        <option value="3"> 3 </option>
                        <option value="4"> 4 </option>
                        <option value="5"> 5 </option>
                        <option value="6"> 6 </option>`
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");

                    // if ($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() != 4){// Si el recubrimiento externo es Barniz UV se selecciona automatico si y no entra en esta condicion
                    //     $("#barniz_uv").html(
                    //         `<option value="">Seleccionar...</option>
                    //         <option value="1"> Si </option>
                    //         <option value="0"> No </option>`
                    //     )
                    //     .prop("disabled", false)
                    //     .selectpicker("refresh");

                    //     $("#barniz_uv,#porcentanje_barniz_uv")
                    //         .prop("disabled", false)
                    //         .val("")
                    //         .selectpicker("refresh")
                    //         .closest("div.form-group")
                    //         .removeClass("error");
                    // }

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    //     getListaCartonOffset(2);
                } else if ($("#impresion").val() === '5') { //5 => "Sin Impresión"

                    //                     if($("#planta_id").val()  == '' || $("#planta_id").val()  != 3 ){// Si la planta esta vacia se queda vacia y si era Osorno se queda Osorno

                    //                         // $("$("#planta_id").val()")
                    //                         //     .prop("disabled", false)
                    //                         //     .val("")
                    //                         //     .selectpicker("refresh")
                    //                         //     .closest("div.form-group")
                    //                         //     .removeClass("error");

                    //                         $("#planta_id").trigger("change");
                    //                     }

                    if (role === '4' || role === '19') {//para el vendedor

                        // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                        // $("#impresion,#coverage_external_id,#coverage_internal_id")
                        //     .prop("disabled", false)
                        //     .val("")
                        //     .selectpicker("refresh")
                        //     .closest("div.form-group")
                        //     .removeClass("error");


                        $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6,#cm2_clisse_color_7")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    } else {

                        //se limpian solo estos campos para los otros roles
                        $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    }

                    $("#design_type_id").html(
                        `<option value="">Seleccionar...</option>
                        <option value="1"> Sin impresión o solo cambio de cartón de una referencia (conservando color de papel tapa exterior y tipo de onda del cartón). </option>
                        <option value="2"> Diseño de 1 a 4 colores con antecedentes gráficos editables en texto y reubicación o incorporación de elementos sin vectorizar. Adaptación como mejora productiva e EEII. </option>
                        <option value="3"> Idem tipo 2, pero con vectorización adicional de figuras y/o solicitud de estires de tinta. </option>
                        <option value="4"> Adaptación a flexo cartón desde otro sistma de impresión, propuesta reduccción de colores y/o trabajo con prueba de color digital. Propuestas de diseño nuevo o inédito. Manipulación de imágenes fotográficas. Certificación FSC. </option>
                        <option value="5" selected="selected"> Cajas offset o alta gráfica. </option>
                        <option value="6"> Cajas tiro y retiro. </option>`
                    )
                        .selectpicker("refresh");

                    $("#design_type_id")
                        .val(1)
                        .prop("disabled", true)
                        .selectpicker("refresh");

                    functionDesignType();

                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                        <option value="0"> 0 </option>
                        <option value="1"> 1 </option>
                        <option value="2"> 2 </option>
                        <option value="3"> 3 </option>
                        <option value="4"> 4 </option>
                        <option value="5"> 5 </option>
                        <option value="6"> 6 </option>`
                    )
                        .selectpicker("refresh");

                    $("#numero_colores")
                        .prop("disabled", true)
                        .val("0")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    $("#numero_colores").trigger("change");

                    // if ($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() != 4){// Si el recubrimiento externo es Barniz UV se selecciona automatico si y no entra en esta condicion
                    //     $("#barniz_uv").html(
                    //         `<option value="">Seleccionar...</option>
                    //         <option value="1"> Si </option>
                    //         <option value="0"> No </option>`
                    //     )
                    //     .prop("disabled", false)
                    //     .selectpicker("refresh");

                    //     $("#barniz_uv,#porcentanje_barniz_uv")
                    //         .prop("disabled", false)
                    //         .val("")
                    //         .selectpicker("refresh")
                    //         .closest("div.form-group")
                    //         .removeClass("error");
                    // }

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    // getListaCartonOffset(2);
                } else if ($("#impresion").val() === '6' || $("#impresion").val() === '7') { //6 => "Sin Impresión (Sólo OF)", 7 => "Sin Impresión (Trazabilidad Completa)"

                    //                     if($("#planta_id").val()  == '' || $("#planta_id").val()  != 3 ){// Si la planta esta vacia se queda vacia y si era Osorno se queda Osorno

                    //                         $("$("#planta_id").val()")
                    //                             .prop("disabled", false)
                    //                             .val("")
                    //                             .selectpicker("refresh")
                    //                             .closest("div.form-group")
                    //                             .removeClass("error");
                    //                     }

                    if (role === '4' || role === '19') {//para el vendedor

                        // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                        // $("#impresion,#coverage_external_id,#coverage_internal_id")
                        //     .prop("disabled", false)
                        //     .val("")
                        //     .selectpicker("refresh")
                        //     .closest("div.form-group")
                        //     .removeClass("error");


                        $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6,#cm2_clisse_color_7")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    } else {

                        //se limpian solo estos campos para los otros roles
                        $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    }

                    $("#design_type_id").html(
                        `<option value="">Seleccionar...</option>
                        <option value="1"> Sin impresión o solo cambio de cartón de una referencia (conservando color de papel tapa exterior y tipo de onda del cartón). </option>
                        <option value="2"> Diseño de 1 a 4 colores con antecedentes gráficos editables en texto y reubicación o incorporación de elementos sin vectorizar. Adaptación como mejora productiva e EEII. </option>
                        <option value="3"> Idem tipo 2, pero con vectorización adicional de figuras y/o solicitud de estires de tinta. </option>
                        <option value="4"> Adaptación a flexo cartón desde otro sistma de impresión, propuesta reduccción de colores y/o trabajo con prueba de color digital. Propuestas de diseño nuevo o inédito. Manipulación de imágenes fotográficas. Certificación FSC. </option>
                        <option value="5" selected="selected"> Cajas offset o alta gráfica. </option>
                        <option value="6"> Cajas tiro y retiro. </option>`
                    )
                        .selectpicker("refresh");

                    $("#design_type_id")
                        .val("")
                        .prop("disabled", false)
                        .selectpicker("refresh");

                    functionDesignTypeChange();

                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                        <option value="0"> 0 </option>
                        <option value="1"> 1 </option>
                        <option value="2"> 2 </option>
                        <option value="3"> 3 </option>
                        <option value="4"> 4 </option>
                        <option value="5"> 5 </option>
                        <option value="6"> 6 </option>`
                    )
                        .selectpicker("refresh");

                    $("#numero_colores")
                        .prop("disabled", true)
                        .val("0")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    $("#numero_colores").trigger("change");

                    // if ($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() != 4){// Si el recubrimiento externo es Barniz UV se selecciona automatico si y no entra en esta condicion
                    //     $("#barniz_uv").html(
                    //         `<option value="">Seleccionar...</option>
                    //         <option value="1"> Si </option>
                    //         <option value="0"> No </option>`
                    //     )
                    //     .prop("disabled", false)
                    //     .selectpicker("refresh");

                    //     $("#barniz_uv,#porcentanje_barniz_uv")
                    //         .prop("disabled", false)
                    //         .val("")
                    //         .selectpicker("refresh")
                    //         .closest("div.form-group")
                    //         .removeClass("error");
                    // }

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                    //  getListaCartonOffset(2);
                } else {

                    //                 if(planta_id  == '' || planta_id  != 3 ){// Si la planta esta vacia se queda vacia y si era Osorno se queda Osorno

                    //                     // $("$("#planta_id").val()")
                    //                     //     .prop("disabled", false)
                    //                     //     .val("")
                    //                     //     .selectpicker("refresh")
                    //                     //     .closest("div.form-group")
                    //                     //     .removeClass("error");

                    //                     $("$("#planta_id").val()").trigger("change");
                    //                 }

                    if (role === '4' || role === '19') {//para el vendedor

                        // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                        // $("#impresion,#coverage_external_id,#coverage_internal_id")
                        //     .prop("disabled", false)
                        //     .val("")
                        //     .selectpicker("refresh")
                        //     .closest("div.form-group")
                        //     .removeClass("error");


                        $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6,#cm2_clisse_color_7")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    } else {

                        //se limpian solo estos campos para los otros roles
                        $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    }

                    $("#design_type_id").html(
                        `<option value="">Seleccionar...</option>
                        <option value="1"> Sin impresión o solo cambio de cartón de una referencia (conservando color de papel tapa exterior y tipo de onda del cartón). </option>
                        <option value="2"> Diseño de 1 a 4 colores con antecedentes gráficos editables en texto y reubicación o incorporación de elementos sin vectorizar. Adaptación como mejora productiva e EEII. </option>
                        <option value="3"> Idem tipo 2, pero con vectorización adicional de figuras y/o solicitud de estires de tinta. </option>
                        <option value="4"> Adaptación a flexo cartón desde otro sistma de impresión, propuesta reduccción de colores y/o trabajo con prueba de color digital. Propuestas de diseño nuevo o inédito. Manipulación de imágenes fotográficas. Certificación FSC. </option>
                        <option value="5"> Cajas offset o alta gráfica. </option>
                        <option value="6"> Cajas tiro y retiro. </option>`
                    )
                        .selectpicker("refresh");

                    $("#design_type_id")
                        .val("")
                        .prop("disabled", false)
                        .selectpicker("refresh");

                    functionDesignType();

                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                        <option value="0"> 0 </option>
                        <option value="1"> 1 </option>
                        <option value="2"> 2 </option>
                        <option value="3"> 3 </option>
                        <option value="4"> 4 </option>
                        <option value="5"> 5 </option>
                        <option value="6"> 6 </option>`
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");

                    // if ($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() != 4){// Si el recubrimiento externo es Barniz UV se selecciona automatico si y no entra en esta condicion
                    //     $("#barniz_uv").html(
                    //         `<option value="">Seleccionar...</option>
                    //         <option value="1"> Si </option>
                    //         <option value="0"> No </option>`
                    //     )
                    //     .prop("disabled", false)
                    //     .selectpicker("refresh");

                    //     $("#barniz_uv,#porcentanje_barniz_uv")
                    //         .prop("disabled", false)
                    //         .val("")
                    //         .selectpicker("refresh")
                    //         .closest("div.form-group")
                    //         .removeClass("error");
                    // }

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                    // getListaCartonOffset(2);
                }

            })
            .triggerHandler("change");
    }


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
    if (role == 3 || role == 4 || role == 19 || (role == 8 && tipo == 'create')) {
        $(
            "#indicador_facturacion,#largura_hm,#anchura_hm,#area_producto,#recorte_adicional,#bct_min_lb,#bct_min_kg,#golpes_largo,#golpes_ancho,#separacion_golpes_largo,#separacion_golpes_ancho,#cuchillas,#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2,#impresion_1,#impresion_2,#impresion_3,#impresion_4,#impresion_5,#impresion_6,#longitud_pegado,#porcentaje_cera_exterior,#porcentaje_cera_interior,#porcentaje_barniz_interior,#tipo_sentido_onda,#material_asignado,#descripcion_material,#tipo_matriz_text,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6,#cm2_clisse_color_7"
        ).prop("disabled", true);
    }

    // Si el tipo de referencia es 0 => "NO" se bloquean la referencia y el bloqueo referencia
    // Ahora el valor de 2 es : 'Sin referencia ' equivale al NO anterior
    $("#reference_type")
        .change(() => {
            if ($("#reference_type").val() == 2) {
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

    // ------------- RECUBRIMIENTO ----------
    // $('#recubrimiento option[value="1"]').remove();// se elimina el 1 que es Cera para que no aparezca en el listado de crear

    // Se validan los campos de cera y barniz segun el recubrimiento
    // $("#recubrimiento")
    //     .change(() => {
    //         if ($("#recubrimiento").val() === "0") {//No aplica
    //             $("#cera_exterior,#cera_interior,#barniz_interior")
    //                 .prop("disabled", true)
    //                 .val("0")
    //                 .selectpicker("refresh")
    //                 .closest("div.form-group")
    //                 .removeClass("error");
    //         }else if($("#recubrimiento").val() === "2"){//Barniz interior
    //             $("#barniz_interior")
    //             .prop("disabled", true)
    //             .val("1")
    //             .selectpicker("refresh")
    //             .closest("div.form-group")
    //             .removeClass("error");

    //             $("#cera_exterior")
    //             .prop("disabled", true)
    //             .val("0")
    //             .selectpicker("refresh")
    //             .closest("div.form-group")
    //             .removeClass("error");

    //             $("#cera_interior")
    //             .prop("disabled", true)
    //             .val("0")
    //             .selectpicker("refresh")
    //             .closest("div.form-group")
    //             .removeClass("error");

    //         }else if($("#recubrimiento").val() === "3"){//Cera interior
    //             $("#barniz_interior")
    //             .prop("disabled", true)
    //             .val("0")
    //             .selectpicker("refresh")
    //             .closest("div.form-group")
    //             .removeClass("error");

    //             $("#cera_exterior")
    //             .prop("disabled", true)
    //             .val("0")
    //             .selectpicker("refresh")
    //             .closest("div.form-group")
    //             .removeClass("error");

    //             $("#cera_interior")
    //             .prop("disabled", true)
    //             .val("1")
    //             .selectpicker("refresh")
    //             .closest("div.form-group")
    //             .removeClass("error");

    //         }else if($("#recubrimiento").val() === "4"){//Cera Exterior
    //             $("#barniz_interior")
    //             .prop("disabled", true)
    //             .val("0")
    //             .selectpicker("refresh")
    //             .closest("div.form-group")
    //             .removeClass("error");

    //             $("#cera_exterior")
    //             .prop("disabled", true)
    //             .val("1")
    //             .selectpicker("refresh")
    //             .closest("div.form-group")
    //             .removeClass("error");

    //             $("#cera_interior")
    //             .prop("disabled", true)
    //             .val("0")
    //             .selectpicker("refresh")
    //             .closest("div.form-group")
    //             .removeClass("error");

    //         }else if($("#recubrimiento").val() === "5"){//Cera Ambas caras
    //             $("#barniz_interior")
    //             .prop("disabled", true)
    //             .val("0")
    //             .selectpicker("refresh")
    //             .closest("div.form-group")
    //             .removeClass("error");

    //             $("#cera_exterior")
    //             .prop("disabled", true)
    //             .val("1")
    //             .selectpicker("refresh")
    //             .closest("div.form-group")
    //             .removeClass("error");

    //             $("#cera_interior")
    //             .prop("disabled", true)
    //             .val("1")
    //             .selectpicker("refresh")
    //             .closest("div.form-group")
    //             .removeClass("error");

    //         }else {
    //             $("#cera_exterior,#cera_interior,#barniz_interior")
    //                 .prop("disabled", false)
    //                 .val("")
    //                 .selectpicker("refresh")
    //                 .closest("div.form-group")
    //                 .removeClass("error");
    //         }
    //     })
    //     .triggerHandler("change");


    // -------------- Planta Objetivo -----------
    $("#planta_id")
        .change(() => {

            if ($("#planta_id").val() == '1') {// Buin

                if (role === '4' || role === '19') {//para el vendedor

                    // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                    $("#coverage_external_id,#coverage_internal_id")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");


                    $("#impresion,#percentage_coverage_internal,#percentage_coverage_external,#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6,#cm2_clisse_color_7")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                } else {

                    //se limpian solo estos campos para los otros roles
                    $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                }

                //Color blanco
                if (
                    $("#carton_color").val() == 2 && (
                        $("#carton_id").find('option:selected').text().trim() == 'EN50G' ||
                        $("#carton_id").find('option:selected').text().trim() == 'EN80G' ||
                        $("#carton_id").find('option:selected').text().trim() == 'EN50EL' ||
                        $("#carton_id").find('option:selected').text().trim() == 'EN44AGB' ||
                        $("#carton_id").find('option:selected').text().trim() == 'EN80AGBE' ||
                        $("#carton_id").find('option:selected').text().trim() == 'EN50AGE' ||
                        $("#carton_id").find('option:selected').text().trim() == 'EN86AGCE' ||
                        $("#carton_id").find('option:selected').text().trim() == 'EN118AGCB'
                    )) {
                    if (role == 19) {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                            <option value="2">Flexografía</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>`
                        )
                            .selectpicker("refresh");

                    } else {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                            <option value="2">Flexografía</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>`
                        )
                            .selectpicker("refresh");
                    }


                } else if ($("#carton_color").val() == 1) {//Café

                    if (role == 19) {
                        // $("#impresion").html(
                        //     `<option value="">Seleccionar...</option>
                        //     <option value="2">Flexografía</option>
                        //     <option value="5">Sin Impresión</option>
                        //
                        //     `
                        // )
                        //     .selectpicker("refresh");

                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                            <option value="2">Flexografía</option>
                            <option value="5">Sin Impresión</option>`
                        )
                            .selectpicker("refresh");

                    } else {
                        // $("#impresion").html(
                        //     `<option value="">Seleccionar...</option>
                        //     <option value="2">Flexografía</option>
                        //     <option value="5">Sin Impresión</option>
                        //
                        //     `
                        // )
                        //     .selectpicker("refresh");

                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                            <option value="2">Flexografía</option>
                            <option value="5">Sin Impresión</option>`
                        )
                            .selectpicker("refresh");
                    }

                }

            } else if ($("#planta_id").val() == '2') {// Til til

                if (role === '4' || role === '19') {//para el vendedor

                    // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                    $("#coverage_external_id,#coverage_internal_id")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");


                    $("#impresion,#percentage_coverage_internal,#percentage_coverage_external,#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6,#cm2_clisse_color_7")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                } else {

                    //se limpian solo estos campos para los otros roles
                    $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                }

                if ($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() == 4) {

                    // $("#impresion").html(
                    //     `<option value="">Seleccionar...</option>
                    //     <option value="3">Flexografía Alta Gráfica</option>
                    //     <option value="5">Sin Impresión</option>
                    //
                    //     `
                    // )
                    //     .selectpicker("refresh");

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                        <option value="3">Flexografía Alta Gráfica</option>
                        <option value="5">Sin Impresión</option>`
                    )
                        .selectpicker("refresh");

                } else {

                    // $("#impresion").html(
                    //     `<option value="">Seleccionar...</option>
                    //     <option value="2">Flexografía</option>
                    //     <option value="4">Flexografía Tiro y Retiro</option>
                    //     <option value="5">Sin Impresión</option>
                    //
                    //     `
                    // )
                    //     .selectpicker("refresh");

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                            <option value="2">Flexografía</option>
                            <option value="4">Flexografía Tiro y Retiro</option>
                            <option value="5">Sin Impresión</option>`
                    )
                        .selectpicker("refresh");
                }

            } else if ($("#planta_id").val() == '3') {// Osorno

                if (role === '4' || role === '19') {//para el vendedor

                    // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                    $("#coverage_external_id,#coverage_internal_id")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");


                    $("#impresion,#percentage_coverage_internal,#percentage_coverage_external,#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6,#cm2_clisse_color_7")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                } else {

                    //se limpian solo estos campos para los otros roles
                    $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                }

                // $("#impresion").html(
                //     `<option value="">Seleccionar...</option>
                //     <option value="2">Flexografía</option>
                //     <option value="5">Sin Impresión</option>
                //
                //     `
                // )
                //     .selectpicker("refresh");

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    <option value="2">Flexografía</option>
                    <option value="5">Sin Impresión</option>`
                )
                    .selectpicker("refresh");

            } else {

                if ($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() == 4) {

                    if (role == 19) {
                        // $("#impresion").html(
                        //     `<option value="">Seleccionar...</option>
                        //     <option value="3">Flexografía Alta Gráfica</option>
                        //     <option value="5">Sin Impresión</option>
                        //
                        //     `
                        // )
                        //     .selectpicker("refresh");
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>`
                        )
                            .selectpicker("refresh");

                    } else {
                        // $("#impresion").html(
                        //     `<option value="">Seleccionar...</option>
                        //     <option value="3">Flexografía Alta Gráfica</option>
                        //     <option value="5">Sin Impresión</option>
                        //
                        //     `
                        // )
                        //     .selectpicker("refresh");

                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                                <option value="3">Flexografía Alta Gráfica</option>
                                <option value="5">Sin Impresión</option>`
                        )
                            .selectpicker("refresh");
                    }

                    $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                } else {
                    if (role == 19) {
                        // $("#impresion").html(
                        //     `<option value="">Seleccionar...</option>
                        //     <option value="2">Flexografía</option>
                        //     <option value="4">Flexografía Tiro y Retiro</option>
                        //     <option value="5">Sin Impresión</option>
                        //
                        //     `
                        // )
                        //     .selectpicker("refresh");

                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                                <option value="2">Flexografía</option>
                                <option value="4">Flexografía Tiro y Retiro</option>
                                <option value="5">Sin Impresión</option>`
                        )
                            .selectpicker("refresh");
                    } else {
                        // $("#impresion").html(
                        //     `<option value="">Seleccionar...</option>
                        //     <option value="2">Flexografía</option>
                        //     <option value="4">Flexografía Tiro y Retiro</option>
                        //     <option value="5">Sin Impresión</option>
                        //
                        //     `
                        // )
                        //     .selectpicker("refresh");

                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                            <option value="2">Flexografía</option>
                            <option value="4">Flexografía Tiro y Retiro</option>
                            <option value="5">Sin Impresión</option>`
                        )
                            .selectpicker("refresh");
                    }


                    if (role === '4' || role === '19') {//para el vendedor

                        // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                        $("#coverage_external_id,#coverage_internal_id")
                            .prop("disabled", false)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");


                        $("#impresion,#percentage_coverage_internal,#percentage_coverage_external,#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6,#cm2_clisse_color_7")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    } else {

                        //se limpian solo estos campos para los otros roles
                        $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    }
                }


            }
        })


    // RECUBRIMIENTO INTERNO ---- Validacion para liberar los campos de validacion_color_cera_barniz
    $("#coverage_internal_id")
        .change(() => {

            //Se limpian los campos
            // $("#impresion,#numero_colores")
            //     .prop("disabled", false)
            //     .val("")
            //     .selectpicker("refresh")
            //     .closest("div.form-group")
            //     .removeClass("error");

            // $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
            //     .prop("disabled", true)
            //     .val("")
            //     .selectpicker("refresh")
            //     .closest("div.form-group")
            //     .removeClass("error");

            functionImpresion();

            if ($("#coverage_internal_id").val() == 1) {
                $("#percentage_coverage_internal")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            } else {
                $("#percentage_coverage_internal")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }

        })
        .triggerHandler("change");


    //RECUBRIMIENTO EXTERNO ---- Validacion para liberar los campos de validacion_color_cera_barniz
    $("#coverage_external_id")
        .change(() => {

            //Se limpian los campos
            $("#impresion,#numero_colores")
                .prop("disabled", false)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");

            $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                .prop("disabled", true)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");

            // functionImpresion();

            if ($("#coverage_external_id").val() == 1) { //No aplica -- se muestra todo de impresión pero el porcentage se deshabilita

                if ($("#planta_id").val() == '1') {//Buin

                    if ($("#carton_color").val() == 1) {//Café
                        if (role == 19) {
                            // $("#impresion").html(
                            //     `<option value="">Seleccionar...</option>
                            // <option value="2">Flexografía</option>
                            // <option value="5">Sin Impresión</option>
                            //
                            // `
                            // )
                            //     .selectpicker("refresh");

                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                                <option value="2">Flexografía</option>
                                <option value="5">Sin Impresión</option>`
                            )
                                .selectpicker("refresh");
                        } else {
                            // $("#impresion").html(
                            //     `<option value="">Seleccionar...</option>
                            // <option value="2">Flexografía</option>
                            // <option value="5">Sin Impresión</option>
                            //
                            // `
                            // )
                            //     .selectpicker("refresh");

                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                                <option value="2">Flexografía</option>
                                <option value="5">Sin Impresión</option>`
                            )
                                .selectpicker("refresh");
                        }


                    } else if ($("#carton_color").val() == 2) {//Blanco
                        if (role == 19) {
                            // $("#impresion").html(
                            //     `<option value="">Seleccionar...</option>
                            // <option value="2">Flexografía</option>
                            // <option value="5">Sin Impresión</option>
                            //
                            // `
                            // )
                            //     .selectpicker("refresh");

                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                                <option value="2">Flexografía</option>
                                <option value="5">Sin Impresión</option>`
                            )
                                .selectpicker("refresh");
                        } else {
                            // $("#impresion").html(
                            //     `<option value="">Seleccionar...</option>
                            // <option value="2">Flexografía</option>
                            // <option value="5">Sin Impresión</option>
                            //
                            // `
                            // )
                            //     .selectpicker("refresh");

                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="2">Flexografía</option>
                            <option value="5">Sin Impresión</option>`
                            )
                                .selectpicker("refresh");
                        }


                        // <option value="3">Flexografía Alta Gráfica</option>
                    } else {
                        if (role == 19) {
                            // $("#impresion").html(
                            //     `<option value="">Seleccionar...</option>
                            // <option value="2">Flexografía</option>
                            // <option value="5">Sin Impresión</option>
                            //
                            // `
                            // )
                            //     .selectpicker("refresh");
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="2">Flexografía</option>
                            <option value="5">Sin Impresión</option>`
                            )
                                .selectpicker("refresh");
                        } else {
                            // $("#impresion").html(
                            //     `<option value="">Seleccionar...</option>
                            // <option value="2">Flexografía</option>
                            // <option value="5">Sin Impresión</option>
                            //
                            // `
                            // )
                            //     .selectpicker("refresh");
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="2">Flexografía</option>
                            <option value="5">Sin Impresión</option>`
                            )
                                .selectpicker("refresh");
                        }


                        // <option value="3">Flexografía Alta Gráfica</option>
                    }

                } else if ($("#planta_id").val() == '2') {// Til til

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                    <option value="2">Flexografía</option>
                    <option value="4">Flexografía Tiro y Retiro</option>
                    <option value="5">Sin Impresión</option>

                    `
                    )
                        .selectpicker("refresh");

                } else if ($("#planta_id").val() == '3') {// Osorno

                    // $("#impresion").html(
                    //     `<option value="">Seleccionar...</option>
                    // <option value="2">Flexografía</option>
                    // <option value="5">Sin Impresión</option>
                    //
                    // `
                    // )
                    //     .selectpicker("refresh");

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                    <option value="2">Flexografía</option>
                    <option value="5">Sin Impresión</option>`
                    )
                        .selectpicker("refresh");

                } else {
                    if (role == 19) {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="2">Flexografía</option>
                        <option value="4">Flexografía Tiro y Retiro</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    } else {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="2">Flexografía</option>
                        <option value="4">Flexografía Tiro y Retiro</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                        // <option value="3">Flexografía Alta Gráfica</option>
                    }



                }

                /*$("#barniz_uv")
                    .prop("disabled", false)
                    .val("0")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                $("#barniz_uv").trigger("change");*/

                $("#percentage_coverage_external")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                if ($("#impresion").val() === '2') {
                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>
                    <option value="5"> 5 </option>
                    <option value="6"> 6 </option>`
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");
                } else {
                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>
                    <option value="5"> 5 </option>
                    <option value="6"> 6 </option>`
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");
                }

            } else if ($("#coverage_external_id").val() == 3) {//Barniz acuoso -- no puede tener impresión de tiro y retiro

                if ($("#planta_id").val() == '1') {//Buin

                    if ($("#carton_color").val() == 1) {//Café
                        if (role == 19) {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        } else {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        }


                        //Para poder seleccionar el barniz UV se debe validar primero las planta, el color carton y el codigo del carton
                    } else if (
                        $("#carton_color").val() == 2 && (
                            $("#carton_id").find('option:selected').text().trim() == 'EN50G' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN80G' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN50EL' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN44AGB' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN80AGBE' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN50AGE' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN86AGCE' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN118AGCB'
                        )) {// Si el color es blanco

                        if (role == 19) {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        } else {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        }


                        $("#impresion")
                            .val("3")
                            .selectpicker("refresh");

                        $("#impresion").trigger("change");

                        /*$("#barniz_uv")
                            .prop("disabled", false)
                            .val("1")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");

                        $("#barniz_uv").trigger("change");*/

                    } else {
                        if (role == 19) {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        } else {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        }

                        // <option value="3">Flexografía Alta Gráfica</option>

                        /*$("#barniz_uv")
                             .prop("disabled", false)
                             .val("0")
                             .selectpicker("refresh")
                             .closest("div.form-group")
                             .removeClass("error");

                         $("#barniz_uv").trigger("change");*/
                    }

                } else if ($("#planta_id").val() == '2') {// Til til

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                    <option value="3">Flexografía Alta Gráfica</option>
                    <option value="5">Sin Impresión</option>

                    `
                    )
                        .selectpicker("refresh");

                    //Barniz UV automaticamente es NO
                    /*$("#barniz_uv")
                        .prop("disabled", false)
                        .val("0")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    $("#barniz_uv").trigger("change");*/

                } else if ($("#planta_id").val() == '3') {// Osorno

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                    <option value="3">Flexografía Alta Gráfica</option>
                    <option value="5">Sin Impresión</option>

                    `
                    )
                        .selectpicker("refresh");

                    //Barniz UV automaticamente es NO
                    /*$("#barniz_uv")
                        .prop("disabled", false)
                        .val("0")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    $("#barniz_uv").trigger("change");*/

                } else {
                    if (role == 19) {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="3">Flexografía Alta Gráfica</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    } else {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="3">Flexografía Alta Gráfica</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    }

                    // <option value="3">Flexografía Alta Gráfica</option>

                    //Para poder seleccionar el barniz UV se debe validar primero las planta, el color carton y el codigo del carton
                    if (
                        $("#carton_color").val() == 2 && (
                            $("#carton_id").find('option:selected').text().trim() == 'EN50G' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN80G' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN50EL' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN44AGB' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN80AGBE' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN50AGE' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN86AGCE' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN118AGCB'
                        )) {// Si el color es blanco

                        if (role == 19) {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        } else {
                            if (role == 19) {
                                $("#impresion").html(
                                    `<option value="">Seleccionar...</option>
                                <option value="3">Flexografía Alta Gráfica</option>
                                <option value="5">Sin Impresión</option>

                                `
                                )
                                    .selectpicker("refresh");
                            } else {
                                $("#impresion").html(
                                    `<option value="">Seleccionar...</option>
                                <option value="3">Flexografía Alta Gráfica</option>
                                <option value="5">Sin Impresión</option>

                                `
                                )
                                    .selectpicker("refresh");
                            }

                        }


                        $("#impresion")
                            .val("3")
                            .selectpicker("refresh");

                        $("#impresion").trigger("change");

                        /*$("#barniz_uv")
                            .prop("disabled", false)
                            .val("1")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");

                        $("#barniz_uv").trigger("change");*/

                    } else {

                        /*$("#barniz_uv")
                            .prop("disabled", false)
                            .val("0")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");

                        $("#barniz_uv").trigger("change");*/
                    }

                }

                $("#percentage_coverage_external")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                if ($("#impresion").val() === '2') {
                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>
                    <option value="5"> 5 </option>
                    <option value="6"> 6 </option>`
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");
                } else {
                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>
                    <option value="5"> 5 </option>
                    <option value="6"> 6 </option>`
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");
                }

            } else if ($("#coverage_external_id").val() == 4) {//Barniz UV -- no puede tener impresión de tiro y retiro

                if ($("#planta_id").val() == '1') {//Buin

                    if ($("#carton_color").val() == 1) {//Café
                        if (role == 19) {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        } else {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        }


                        //Para poder seleccionar el barniz UV se debe validar primero las planta, el color carton y el codigo del carton
                    } else if (
                        $("#carton_color").val() == 2 && (
                            $("#carton_id").find('option:selected').text().trim() == 'EN50G' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN80G' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN50EL' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN44AGB' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN80AGBE' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN50AGE' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN86AGCE' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN118AGCB'
                        )) {// Si el color es blanco
                        if (role == 19) {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        } else {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        }


                        $("#impresion")
                            .val("3")
                            .selectpicker("refresh");

                        $("#impresion").trigger("change");

                        /*$("#barniz_uv")
                            .prop("disabled", false)
                            .val("1")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");

                        $("#barniz_uv").trigger("change");*/

                    } else {
                        if (role == 19) {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        } else {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        }

                        // <option value="3">Flexografía Alta Gráfica</option>

                        /*$("#barniz_uv")
                             .prop("disabled", false)
                             .val("0")
                             .selectpicker("refresh")
                             .closest("div.form-group")
                             .removeClass("error");

                         $("#barniz_uv").trigger("change");*/
                    }

                } else if ($("#planta_id").val() == '2') {// Til til

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                    <option value="3">Flexografía Alta Gráfica</option>
                    <option value="5">Sin Impresión</option>

                    `
                    )
                        .selectpicker("refresh");

                    //Barniz UV automaticamente es NO
                    /*$("#barniz_uv")
                        .prop("disabled", false)
                        .val("0")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    $("#barniz_uv").trigger("change");*/

                } else if ($("#planta_id").val() == '3') {// Osorno

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                    <option value="3">Flexografía Alta Gráfica</option>
                    <option value="5">Sin Impresión</option>

                    `
                    )
                        .selectpicker("refresh");

                    //Barniz UV automaticamente es NO
                    /*$("#barniz_uv")
                        .prop("disabled", false)
                        .val("0")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    $("#barniz_uv").trigger("change");*/

                } else {
                    if (role == 19) {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="3">Flexografía Alta Gráfica</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    } else {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="3">Flexografía Alta Gráfica</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    }

                    // <option value="3">Flexografía Alta Gráfica</option>

                    //Para poder seleccionar el barniz UV se debe validar primero las planta, el color carton y el codigo del carton
                    if (
                        $("#carton_color").val() == 2 && (
                            $("#carton_id").find('option:selected').text().trim() == 'EN50G' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN80G' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN50EL' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN44AGB' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN80AGBE' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN50AGE' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN86AGCE' ||
                            $("#carton_id").find('option:selected').text().trim() == 'EN118AGCB'
                        )) {// Si el color es blanco
                        if (role == 19) {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        } else {
                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>

                            `
                            )
                                .selectpicker("refresh");
                        }


                        $("#impresion")
                            .val("3")
                            .selectpicker("refresh");

                        $("#impresion").trigger("change");

                        /*$("#barniz_uv")
                            .prop("disabled", false)
                            .val("1")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");

                        $("#barniz_uv").trigger("change");*/

                    } else {

                        /*$("#barniz_uv")
                            .prop("disabled", false)
                            .val("0")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");

                        $("#barniz_uv").trigger("change");*/
                    }

                }

                $("#percentage_coverage_external")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                if ($("#impresion").val() == 3 || $("#impresion").val() == 1) {
                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>
                    <option value="5"> 5 </option>
                    <option value="6"> 6 </option>
                    <option value="7"> 7 </option>`
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");
                } else {

                    if ($("#impresion").val() === '2') {
                        $("#numero_colores").html(
                            `<option value="">Seleccionar...</option>
                        <option value="1"> 1 </option>
                        <option value="2"> 2 </option>
                        <option value="3"> 3 </option>
                        <option value="4"> 4 </option>
                        <option value="5"> 5 </option>
                        <option value="6"> 6 </option>`
                        )
                            .prop("disabled", false)
                            .selectpicker("refresh");
                    } else {
                        $("#numero_colores").html(
                            `<option value="">Seleccionar...</option>
                        <option value="1"> 1 </option>
                        <option value="2"> 2 </option>
                        <option value="3"> 3 </option>
                        <option value="4"> 4 </option>
                        <option value="5"> 5 </option>
                        <option value="6"> 6 </option>`
                        )
                            .prop("disabled", false)
                            .selectpicker("refresh");
                    }
                }

            } else if ($("#coverage_external_id").val() == '') {// cuando esta vacio, se limpian los datos

                /*$("#barniz_uv")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                $("#barniz_uv").trigger("change");*/

                $("#percentage_coverage_external")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

            } else {//se muestra todo de impresión y se deshabilita el porcentage

                if ($("#planta_id").val() == '1') {// Buin
                    if (role == 19) {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="2">Flexografía</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    } else {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="2">Flexografía</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    }

                    // <option value="3">Flexografía Alta Gráfica</option>


                } else if ($("#planta_id").val() == '2') {// Til til

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                    <option value="2">Flexografía</option>
                    <option value="4">Flexografía Tiro y Retiro</option>
                    <option value="5">Sin Impresión</option>

                    `
                    )
                        .selectpicker("refresh");

                } else if ($("#planta_id").val() == '3') {// Osorno

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                    <option value="2">Flexografía</option>
                    <option value="5">Sin Impresión</option>

                    `
                    )
                        .selectpicker("refresh");

                } else {
                    if (role == 19) {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="2">Flexografía</option>
                        <option value="4">Flexografía Tiro y Retiro</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    } else {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="2">Flexografía</option>
                        <option value="4">Flexografía Tiro y Retiro</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    }

                    // <option value="3">Flexografía Alta Gráfica</option>
                }

                /*$("#barniz_uv")
                    .prop("disabled", false)
                    .val("0")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                $("#barniz_uv").trigger("change");*/

                $("#percentage_coverage_external")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                if ($("#impresion").val() === '2') {
                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>
                    <option value="5"> 5 </option>
                    <option value="6"> 6 </option>`
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");
                } else {
                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>
                    <option value="5"> 5 </option>
                    <option value="6"> 6 </option>`
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");
                }

            }

        })
        .triggerHandler("change");



    // Si numero de colores es 0  bloqueamos todos los colores de lo contrario se habilitan acorde al numero seleccionado
    $("#numero_colores")
        .change(() => {
            const numeroColores = $("#numero_colores").val();
            const desabilitarColores = (colores) => {
                $(colores)
                    .prop("disabled", true)
                    .prop("required", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            };
            if (numeroColores === "0" || numeroColores === "") {
                if ($("#coverage_external_id").val() != 4) {
                    desabilitarColores(
                        "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
                    );
                } else {
                    desabilitarColores(
                        "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
                    );
                    if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18 && role != 19 && !(role == 8 && tipo == 'create')) {
                        $("#barniz_uv,#porcentanje_barniz_uv")
                            .prop("disabled", false)
                            .prop('required', true)
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    } else {
                        $("#barniz_uv,#porcentanje_barniz_uv")
                            .prop("disabled", false)
                            .prop('required', false)
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    }
                }
            } else {
                switch (numeroColores) {
                    case "1":
                        //Validacion de impresion Flexografía Tiro y Retiro con respecto al Evolutico 72
                        //permitir que uno de los colores siempre sea color interno el ppal.
                        if ($("#coverage_external_id").val() != 4) {
                            desabilitarColores(
                                "#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
                            );
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18 && role != 19 && !(role == 8 && tipo == 'create')) {
                                $("#color_1_id,#impresion_1,#cm2_clisse_color_1")
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            } else {
                                $("#color_1_id,#impresion_1,#cm2_clisse_color_1")
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        } else {
                            desabilitarColores(
                                "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
                            );
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18 && role != 19 && !(role == 8 && tipo == 'create')) {
                                $("#barniz_uv,#porcentanje_barniz_uv")
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            } else {
                                $("#barniz_uv,#porcentanje_barniz_uv")
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }

                        }
                        break;
                    case "2":
                        if ($("#coverage_external_id").val() != 4) {
                            desabilitarColores(
                                "#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
                            );
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18 && role != 19 && !(role == 8 && tipo == 'create')) {
                                $("#color_1_id,#impresion_1,#color_2_id,#impresion_2,#cm2_clisse_color_1,#cm2_clisse_color_2")
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            } else {
                                $("#color_1_id,#impresion_1,#color_2_id,#impresion_2,#cm2_clisse_color_1,#cm2_clisse_color_2")
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        } else {
                            desabilitarColores(
                                "#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
                            );
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18 && role != 19 && !(role == 8 && tipo == 'create')) {
                                $("#color_1_id,#impresion_1,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_1")
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            } else {
                                $("#color_1_id,#impresion_1,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_1")
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        }
                        break;
                    case "3":
                        if ($("#coverage_external_id").val() != 4) {
                            desabilitarColores(
                                "#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
                            );
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18 && role != 19 && !(role == 8 && tipo == 'create')) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            } else {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3"
                                )
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        } else {
                            desabilitarColores(
                                "#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
                            );
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18 && role != 19 && !(role == 8 && tipo == 'create')) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_1,#cm2_clisse_color_2"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            } else {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_1,#cm2_clisse_color_2"
                                )
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        }
                        break;
                    case "4":
                        if ($("#coverage_external_id").val() != 4) {
                            desabilitarColores("#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_5,#cm2_clisse_color_6");
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18 && role != 19 && !(role == 8 && tipo == 'create')) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            } else {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4"
                                )
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        } else {
                            desabilitarColores("#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6");
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18 && role != 19 && !(role == 8 && tipo == 'create')) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            } else {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3"
                                )
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        }
                        break;
                    case "5":
                        if ($("#coverage_external_id").val() != 4) {
                            desabilitarColores("#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_6");
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18 && role != 19 && !(role == 8 && tipo == 'create')) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            } else {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5"
                                )
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        } else {
                            desabilitarColores("#color_5_id,#impresion_5,#color_6_id,#impresion_6");
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18 && role != 19 && !(role == 8 && tipo == 'create')) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            } else {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4"
                                )
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        }
                        break;
                    case "6":
                        if ($("#coverage_external_id").val() != 4) {
                            desabilitarColores("#barniz_uv,#porcentanje_barniz_uv");
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18 && role != 19 && !(role == 8 && tipo == 'create')) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            } else {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
                                )
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        } else {
                            desabilitarColores("#color_6_id,#impresion_6,#cm2_clisse_color_6");
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18 && role != 19 && !(role == 8 && tipo == 'create')) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            } else {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5"
                                )
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        }
                        break;
                    case "7":

                        if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18 && role != 19 && !(role == 8 && tipo == 'create')) {
                            $(
                                "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
                            )
                                .prop("disabled", false)
                                .prop('required', true)
                                .selectpicker("refresh")
                                .closest("div.form-group")
                                .removeClass("error");
                        } else {
                            $(
                                "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
                            )
                                .prop("disabled", false)
                                .prop('required', false)
                                .selectpicker("refresh")
                                .closest("div.form-group")
                                .removeClass("error");
                        }

                        break;
                    default:
                        break;
                }
            }
        })
        .triggerHandler("change");

    //Validacion para el vendedor - color 6 o barniz UV es obligatorio, cuando uno de estos dos tiene datos el otro ya no es obligatorio
    $("#color_6_id")
        .change(() => {

            if ($("#color_6_id").val() !== '') {

                /*$("#barniz_uv,#porcentanje_barniz_uv")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");*/

            } else if ($("#color_6_id").val() === '') {

                $("#impresion_6")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }

        })
        .triggerHandler("change");

    //Validacion de BARNIZ UV --- cuando es no se bloquea el % IMPRESIÓN B. UV
    /*$("#barniz_uv")
        .change(() => {

            if( $("#barniz_uv") !== ''){//Validacion para el vendedor - color 6 o barniz UV es obligatorio, cuando uno de estos dos tiene datos el otro ya no es obligatorio

                $("#color_6_id,#impresion_6")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }

            if($("#barniz_uv").val() == 1){

                $("#porcentanje_barniz_uv")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }else{

                $("#porcentanje_barniz_uv")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }

        })
        .triggerHandler("change");*/


    // si el tipo de producto es "Esquinero (codigo 21)" entonces anchura y ancho interno deben ser 98
    // $("#product_type_id").change(() => {
    //     if ($("#product_type_id").val() != 21) {
    //         //             anchura_hm
    //         // interno_ancho
    //         $("#anchura_hm,#interno_ancho,#golpes_largo,#golpes_ancho")
    //             .prop("disabled", false)
    //             .val("")
    //             .closest("div.form-group")
    //             .removeClass("error");
    //     } else {
    //         $("#anchura_hm,#interno_ancho")
    //             .prop({ disabled: false, readonly: true })
    //             .val("98")
    //             .selectpicker("refresh")
    //             .closest("div.form-group")
    //             .removeClass("error");

    //         $("#golpes_largo,#golpes_ancho")
    //             .prop({ disabled: false, readonly: true })
    //             .val("1")
    //             .selectpicker("refresh")
    //             .closest("div.form-group")
    //             .removeClass("error");
    //     }
    // });
    // .triggerHandler("change");

    //Si cambia el producto, vacio los campos de maquila y maquila servicio para volver a cargar los valores, segun el producto
    $("#product_type_id").change(function () {
        let producto_id = ['3', '4', '5', '6', '8', '10', '11', '12', '13', '14', '16', '18', '19', '20', '28', '31', '32', '33', '34']
        let producto = $(this).val();

        if (producto_id.includes(producto)) {
            $("#maquila")
                .prop("disabled", false)
                .val('')
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        } else {
            $("#maquila")
                .prop("disabled", true)
                .val("0")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        }

        $("#maquila_servicio_id")
            .prop("disabled", true)
            .val("")
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");
    });

    //Maquila ( si maquila es SI activa el campo de servicios de maquila)
    $("#maquila")
        .change(() => {
            if ($("#maquila").val() == 0) {
                $("#maquila_servicio_id")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            } else {

                return $.ajax({
                    type: "GET",
                    url: "/cotizador/getServiciosMaquila",
                    data: "tipo_producto_id=" + $("#product_type_id").val(),
                    success: function (data) {
                        data = $.parseHTML(data);
                        $("#maquila_servicio_id")
                            .prop("disabled", false)
                            .empty()
                            .append(data)
                            .selectpicker("refresh");

                    },
                });

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
                $("#cantidad").prop("disabled", false).selectpicker("refresh");
            }
        })
        .triggerHandler("change");

    // seleccionamos el color del carton para listar todos los cartones segun el color, sino mostramos todos los cartones
    $("#carton_color")
        .change(() => {
            var carton_color = $("#carton_color").val();

            if (carton_color != '' && carton_color == 1) {//Si esta uno seleccionado y es color Café no se muestra Alta Gráfica en impresión

                if ($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() == 4) {//Se valida porque el color Café si muestra el tiro y retiro pero el recubrimiento externo no
                    if (role == 19) {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="2">Flexografía</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    } else {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="2">Flexografía</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    }


                } else {
                    if (role == 19) {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="2">Flexografía</option>
                        <option value="4">Flexografía Tiro y Retiro</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    } else {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="2">Flexografía</option>
                        <option value="4">Flexografía Tiro y Retiro</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    }

                }

            } else {

                if ($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() == 4) {//Se valida porque el color Blanco si muestra el tiro y alta grafica
                    if (role == 19) {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="2">Flexografía</option>
                        <option value="3">Flexografía Alta Gráfica</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    } else {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="2">Flexografía</option>
                        <option value="3">Flexografía Alta Gráfica</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    }


                } else {
                    if (role == 19) {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="2">Flexografía</option>
                        <option value="3">Flexografía Alta Gráfica</option>
                        <option value="4">Flexografía Tiro y Retiro</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    } else {
                        $("#impresion").html(
                            `<option value="">Seleccionar...</option>
                        <option value="2">Flexografía</option>
                        <option value="3">Flexografía Alta Gráfica</option>
                        <option value="4">Flexografía Tiro y Retiro</option>
                        <option value="5">Sin Impresión</option>

                        `
                        )
                            .selectpicker("refresh");
                    }

                }

            }

            return $.ajax({
                type: "GET",
                url: "/getCartonColor",
                data: "carton_color=" + carton_color,
                success: function (data) {
                    data = $.parseHTML(data);
                    $("#carton_id")
                        .empty()
                        .append(data)
                        .selectpicker("refresh");
                },
            });
        })
        .triggerHandler("change");
    // Si no se selecciona un carton se puede selccionar un color de carton, si se selecciona un carton desabilitamos color carton
    $("#carton_id")
        .change(() => {
            if ($("#carton_id").val()) {

                var carton_id = $("#carton_id").val();

                return $.ajax({
                    type: "GET",
                    url: "/getCarton",
                    data: "carton_id=" + carton_id,
                    success: function (data) {
                        $("#liner_exterior").val(data.liner_exterior);
                        $("#carton_color").selectpicker(
                            "val",
                            data.color_tapa_exterior == "CAFE" ? 1 : 2
                        );

                        //Enviamos los id de las palntas para cargar el listado, segun el carton
                        setPlanta(data.planta_id);

                        //cuando sea seleccionado el color blanco y algunos de estos tres cartones es que puede mostrar impresion  Alta Gráfica
                        if (data.alta_grafica == 1) {

                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>

                            <option value="3">Flexografía Alta Gráfica</option>
                            `
                            )
                                .selectpicker("refresh");
                        } else if (
                            $("#carton_color").val() == 2 && (
                                data.codigo == 'EN50G' ||
                                data.codigo == 'EN80G' ||
                                data.codigo == 'EN50EL' ||
                                data.codigo == 'EN44AGB' ||
                                data.codigo == 'EN80AGBE' ||
                                data.codigo == 'EN50AGE' ||
                                data.codigo == 'EN86AGCE' ||
                                data.codigo == 'EN118AGCB'
                            )) {

                            if ($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() == 4) {//Barniz UV --- No muestra tiro y retiro

                                /*$("#impresion").html(
                                    `<option value="">Seleccionar...</option>
                                    <option value="2">Flexografía</option>
                                    <option value="3">Flexografía Alta Gráfica</option>
                                    <option value="5">Sin Impresión</option>

                                    `
                                )
                                .selectpicker("refresh");*/

                            } else {

                                /*$("#impresion").html(
                                    `<option value="">Seleccionar...</option>
                                    <option value="2">Flexografía</option>
                                    <option value="3">Flexografía Alta Gráfica</option>
                                    <option value="4">Flexografía Tiro y Retiro</option>
                                    <option value="5">Sin Impresión</option>

                                    `
                                )
                                .selectpicker("refresh");*/
                            }

                        } else {

                            // $("#impresion").html(
                            //     `<option value="">Seleccionar...</option>
                            // <option value="2">Flexografía</option>
                            // <option value="3">Flexografía Alta Gráfica</option>
                            // <option value="5">Sin Impresión</option>
                            //
                            // `
                            // ).selectpicker("refresh");

                            $("#impresion").html(
                                `<option value="">Seleccionar...</option>
                            <option value="2">Flexografía</option>
                            <option value="3">Flexografía Alta Gráfica</option>
                            <option value="5">Sin Impresión</option>`
                            ).selectpicker("refresh");

                            if ($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() == 4) {//Barniz UV --- No muestra tiro y retiro

                                /*$("#impresion").html(
                                    `<option value="">Seleccionar...</option>
                                    <option value="2">Flexografía</option>
                                    <option value="5">Sin Impresión</option>

                                    `
                                )
                                .selectpicker("refresh");*/

                            } else {

                                /*$("#impresion").html(
                                    `<option value="">Seleccionar...</option>
                                    <option value="2">Flexografía</option>
                                    <option value="4">Flexografía Tiro y Retiro</option>
                                    <option value="5">Sin Impresión</option>

                                    `
                                )
                                .selectpicker("refresh");*/
                            }

                        }

                    },
                });
            } else {
                $("#carton_color")
                    .prop("disabled", false)
                    .selectpicker("refresh");
                $("#liner_exterior").val("");
            }

        })
        .triggerHandler("change");

    //Enviamos los id de las palntas para cargar el listado, segun el carton
    const setPlanta = (val) => {

        return $.ajax({
            type: "GET",
            url: "/getPlantaCarton",
            data: "planta_id=" + val,
            success: function (data) {

                $("#planta_id")
                    .empty()
                    .append(data)
                    .selectpicker("refresh");
            },
        });

    }

    // Habilita/inabilita la vista del campo de "numero de muestras" segun checklist de "muestra"
    $("#muestra").click(function () {
        if (this.checked) $("#crear_muestra").click();
        $("#container-numero-muetras")[this.checked ? "show" : "hide"]();
    });

    // Si el proceso es 4 => "Diecutter-C/Pegado" se bloquean la referencia y el bloqueo referencia
    // OT: Terminaciones/Pegados: cuando PROCESO= en PEGADO sea automáticamente SI
    $("#process_id")
        .change(() => {
            /*if ($("#process_id").val() == 4) {
                const pegado_terminacion = $("#pegado_terminacion").val();
                // console.log($("#pegado_terminacion").val());
                $("#pegado_terminacion")
                    .html(
                        '<option value="">Seleccionar...</option><option value="2"> Pegado Interno</option><option value="3">Pegado Externo</option>'
                    )
                    .selectpicker("refresh");
                $("#pegado_terminacion")
                    .val(pegado_terminacion)
                    .selectpicker("refresh");
            } else {*/
            const pegado_terminacion = $("#pegado_terminacion").val();
            $("#pegado_terminacion")
                .html(
                    `<option value="">Seleccionar...</option>
                            <option value="0"> No Aplica</option>
                            <option value="2"> Pegado Interno</option>
                            <option value="3"> Pegado Externo</option>
                            <option value="4"> Pegado 3 Puntos</option>
                            <option value="5"> Pegado 4 Puntos</option>`
                )
                .selectpicker("refresh");
            $("#pegado_terminacion")
                .val(pegado_terminacion)
                .selectpicker("refresh");
            // }

            // if($("#process_id").val() == 1 || $("#process_id").val() == 5){

            //     if(($("#tipo_solicitud").val() == 1 || $("#tipo_solicitud").val() == 5) && $("#anchura_hm").val() != ''){
            //         let suma = suma_anchura_hm();
            //         if(suma){
            //             console.log('soy suma true');
            //         }else{
            //             $("#rayado-error").html('La suma de los campos Rayado, debe coincidir con el campo Anchura HM')

            //             // Ocultamos el mensaje
            //             setTimeout(function() {
            //                 $("#rayado-error").html('');
            //             },5000);
            //         }
            //     }

            // }
        })
        .triggerHandler("change");

    // LOGICA DEL FORMULARIO SEGUN TIPO DE SOLICITUD
    //
    //
    //
    //

    const disableAndCleanElements = (elements) => {
        toggleAndCleanElements(elements, true);
    };

    //Validacion para el campo pegado
    const functionPegado = () => {

        if ($("#tipo_solicitud").val() == 1 || $("#tipo_solicitud").val() == 5) {

            $("#pegado")
                .prop("disabled", true)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");

            $("#pegado_terminacion")
                .change(() => {
                    if ($("#pegado_terminacion").val() === "0") {

                        $("#pegado")
                            .prop("disabled", true)
                            .val("0")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    } else if ($("#pegado_terminacion").val() !== '') {
                        $("#pegado")
                            .prop("disabled", true)
                            .val("1")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    } else {
                        $("#pegado")
                            .prop("disabled", true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    }
                })
                .triggerHandler("change");

        }

    }

    const cleanCheckboxs = () => {
        $(".custom-control-input")
            .prop("disabled", false)
            .prop("checked", false);
        $("#muestra").prop("checked", false).triggerHandler("click");
    };

    const disableHierarchies = () => {
        $("#hierarchy_id,#subhierarchy_id,#subsubhierarchy_id")
            .prop("disabled", true)
            .val("")
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");
    };


    const enableAndCleanElements = (elements) => {
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
            .map((e) => {
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



    // Habilitacion de campos segun tipo de solicitud
    $("#tipo_solicitud")
        .change(function () {
            let tipo_solicitud = $(this).val();
            // Desarrollo Completo
            if (tipo_solicitud == 1) {
                // Bloqueo y limpieza de valores para los siguientes inputs
                disableAndCleanElements("#cad");
                // disableCadSelect();

                enableCadSelect();

                // Se desbloquean todos los checkbox
                cleanCheckboxs();

                // Desbloqueo y limpieza de valores para los siguientes inputs
                $(
                    "#reference_id,#bloqueo_referencia,#cinta,#items_set,#veces_item,#carton_id,#style_id,#recubrimiento_id,#numero_colores,#color_1_id,#color_2_id,#color_3_id,#color_4_id,#color_5_id,#color_6_id,#cera_exterior,#cera_interior,#barniz_interior,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#process_id,#pegado_terminacion,#armado_id,#tipo_sentido_onda,#peso_contenido_caja,#autosoportante,#envase_id,#cajas_altura,#impresion,#pallet_sobre_pallet,#cantidad, #matriz_id"
                )
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                // agregamos la opcion value = 2 "Sin Referencia":
                $('#reference_type option[value="2"]').remove();
                $('#reference_type option[value=""]').remove();
                $('#reference_type').prepend('<option value="">Seleccionar...</option><option value="2"> Sin referencia</option>');
                $('#reference_type').selectpicker("refresh")

                $("#reference_type")
                    .prop("disabled", false)
                    .val("2")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                $("#maquila")
                    .prop("disabled", false)
                    .val("0")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                //Validacion para el campo pegado
                functionPegado();

                //Primero se deben cumplir estas nuevas condiciones
                validacion_color_cera_barniz();

                if (role == 6) {

                    //Validacion de impresion con numeros de colores
                    functionImpresion();
                }

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

                // agregamos la opcion value = 2 "Sin Referencia":
                $('#reference_type option[value="2"]').remove();
                $('#reference_type option[value=""]').remove();
                $('#reference_type').prepend('<option value="">Seleccionar...</option><option value="2"> Sin referencia</option>');
                $('#reference_type').selectpicker("refresh")
                // Bloqueo y limpieza de valores para los siguientes inputs
                $(
                    "#product_type_id,#reference_type,#reference_id,#bloqueo_referencia,#cinta,#items_set,#veces_item,#style_id,#bct_min_lb,#bct_min_kg,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto"
                )
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                // Si se desplego anteriormente contenedor de cintas se debe ocultar
                $("#ot-distancia-cinta").hide();

                $("#maquila")
                    .prop("disabled", false)
                    .val("0")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }

            // Muestra CAD
            else if (tipo_solicitud == 3) {

                enableCadSelect();
                // Bloqueo y limpieza de valores para los siguientes inputs

                // Se bloquean todos los checkbox excepto el de muestra
                $(".custom-control-input:not(#muestra,#check_correo_cliente,#check_plano_actual,#check_boceto_actual,#check_speed,#check_otro,#check_vb_muestra,#check_vb_boce,#check_referencia_de,#check_referencia_dg,#check_envase_primario,#check_conservar_si,#check_conservar_no)")
                    .prop("disabled", true)
                    .prop("checked", false);
                // activamos la opcion de muestra dinamicamente
                $("#muestra").prop("checked", true).triggerHandler("click");

                $("#check_correo_cliente,#check_plano_actual,#check_boceto_actual,#check_speed,#check_otro,#check_vb_muestra,#check_vb_boce,#check_referencia_de,#check_referencia_dg,#check_envase_primario,#check_conservar_si,#check_conservar_no")
                    .prop("disabled", false);

                // agregamos la opcion value = 2 "Sin Referencia":
                $('#reference_type option[value="2"]').remove();
                $('#reference_type option[value=""]').remove();
                $('#reference_type').prepend('<option value="">Seleccionar...</option><option value="2"> Sin referencia</option>');
                $('#reference_type').selectpicker("refresh")

                // Bloqueo y limpieza de valores para los siguientes inputs
                $(
                    "#product_type_id,#reference_type,#reference_id,#bloqueo_referencia,#cinta,#style_id,#items_set,#veces_item,#bct_min_lb,#bct_min_kg,#numero_colores,#color_1_id,#color_2_id,#color_3_id,#color_4_id,#color_5_id,#color_6_id,#pegado,#process_id,#pegado_terminacion,#armado_id,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#tipo_sentido_onda,#peso_contenido_caja,#autosoportante,#envase_id,#cajas_altura,#impresion,#pallet_sobre_pallet,#cantidad"
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
                // Si se desplego anteriormente contenedor de cintas se debe ocultar
                $("#ot-distancia-cinta").hide();
                // bloquear modal de muestras el carton
                $("#form-muestra  #carton_id")
                    .prop("disabled", true)
                    .prop("readonly", true)
                    .selectpicker("refresh");

                $("#maquila")
                    .prop("disabled", false)
                    .val("0")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                // $('#col-referencia1').show();
                // $('#col-referencia2').remove();


            } // Cotiza sin CAD
            else if (tipo_solicitud == 4) {
                disableCadSelect();
                // Se desbloquean todos los checkbox
                cleanCheckboxs();

                // Desbloqueo y limpieza de valores para los siguientes inputs
                enableAndCleanElements(
                    "#product_type_id,#recubrimiento,#numero_colores,#color_1_id,#color_2_id,#color_3_id,#color_4_id,#color_5_id,#color_6_id,#pegado,#cera_exterior,#cera_interior,#barniz_interior,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#process_id,#pegado_terminacion,#armado_id,#tipo_sentido_onda,#peso_contenido_caja,#autosoportante,#envase_id,#cajas_altura,#impresion,#pallet_sobre_pallet,#cantidad"
                );

                // agregamos la opcion value = 2 "Sin Referencia":
                $('#reference_type option[value="2"]').remove();
                $('#reference_type option[value=""]').remove();
                $('#reference_type').prepend('<option value="">Seleccionar...</option><option value="2"> Sin referencia</option>');
                $('#reference_type').selectpicker("refresh")

                // Bloqueo y limpieza de valores para los siguientes inputs
                disableAndCleanElements(
                    "#cad,#reference_type,#reference_id,#bloqueo_referencia,#cinta,#items_set,#veces_item,#style_id,#bct_min_lb,#bct_min_kg, #matriz_id"
                );
                // Si se desplego anteriormente contenedor de cintas se debe ocultar
                $("#ot-distancia-cinta").hide();

                $("#maquila")
                    .prop("disabled", false)
                    .val("0")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

            } //Arte con Material
            else if (tipo_solicitud == 5) {
                enableCadSelect();
                // Se desbloquean todos los checkbox
                cleanCheckboxs();

                //Primero se deben cumplir estas nuevas condiciones
                validacion_color_cera_barniz();

                //Validacion para el campo pegado
                functionPegado();

                // $('#col-referencia1').remove();
                // $('#col-referencia2').show();

                if (role == 6) {

                    //Validacion de impresion con numeros de colores
                    functionImpresion();
                }

                // Desbloqueo y limpieza de valores para los siguientes inputs
                enableAndCleanElements(
                    "#product_type_id,#reference_type,#cinta,#recubrimiento,#numero_colores,#color_1_id,#color_2_id,#color_3_id,#color_4_id,#color_5_id,#color_6_id,#cera_exterior,#cera_interior,#barniz_interior,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#process_id,#pegado_terminacion,#peso_contenido_caja,#autosoportante,#envase_id,#cajas_altura,#impresion,#pallet_sobre_pallet,#cantidad"
                );

                // Bloqueo y limpieza de valores para los siguientes inputs
                disableAndCleanElements(
                    "#style_id"
                    // ,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#process_id,#pegado_terminacion,#armado_id,#impresion
                );

                // Habilitamos el Select de referencia_type
                $("#reference_type")
                    .prop("disabled", false)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                //Eliminamos la opcion de "Sin Referencia" para tipo solicitud Arte con material
                $('#reference_type option[value="2"]').remove();
                $('#reference_type').selectpicker("refresh")

                $("#reference_id,#bloqueo_referencia")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                $("#maquila")
                    .prop("disabled", false)
                    .val("0")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                // Al seleccionar un material de referencia cargar el cad
                $("#reference_id").change(function () {
                    var val = $(this).val();
                    return $.ajax({
                        type: "GET",
                        url: "/getCadByMaterial",
                        data: "material_id=" + val,
                        success: function (data) {
                            // Cargamos cad asociado y desabilitamos
                            $("#cad_id")
                                .val(data.cad_id)
                                .selectpicker("refresh")
                                .triggerHandler("change");
                            $("#cad_id").prop("disabled", true);
                            // Cargamos carton asociado y desabilitamos
                            $("#carton_id")
                                .val(data.carton_id)
                                .selectpicker("refresh")
                                .triggerHandler("change")

                            //Validamos que cuando la solicitud sea arte con material y la referencia no traiga carton asociado, muestre un mensaje y se oculta boton de guardar
                            if ($("#tipo_solicitud").val() == 5 && data.carton_id == 0) {
                                notify("El material de referencia no tiene carton asociado", "danger");
                                $("#ot-submit").addClass('invisible')
                            } else {
                                $("#ot-submit").removeClass('invisible')
                            }

                            $("#carton_id").prop("disabled", true);
                            // Cargamos tipo item asociado y desabilitamos
                            $("#product_type_id")
                                .val(data.product_type_id)
                                .selectpicker("refresh")
                                .triggerHandler("change");
                            $("#product_type_id").prop("disabled", true);
                            // Cargamos Estilo asociado y desabilitamos
                            $("#style_id")
                                .val(data.style_id)
                                .selectpicker("refresh")
                                .triggerHandler("change");
                            $("#style_id").prop("disabled", true);
                        },
                    });
                });
            }
        })
        .triggerHandler("change");

    //Cuando se crea la OT los campos de separación Golpes al Ancho y Largo se establecen en 0
    $("#separacion_golpes_largo,#separacion_golpes_ancho")
        .prop("disabled", false)
        .val("0")
        .selectpicker("refresh")
        .closest("div.form-group")
        .removeClass("error");

    // Pais de referencia (pais_id) inabilitado cuando la opcion es NO y Sin FSC
    $("#fsc")
        .change(() => {

            if ($("#fsc").val() == 2) {
                $("#pais_id")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                $("#planta_id").html(
                    `<option value="">Seleccionar...</option>
                <option value="1">BUIN</option>
                <option value="2">TIL TIL</option>
                <option value="3">OSORNO</option>`
                )
                    .selectpicker("refresh");


            } else if ($("#fsc").val() != 2 && $("#fsc").val() != '') {

                //Planta BUIN no tiene FSC
                $("#planta_id").html(
                    `<option value="">Seleccionar...</option>
                <option value="2">TIL TIL</option>
                <option value="3">OSORNO</option>`
                )
                    .selectpicker("refresh");

                $("#pais_id")
                    .prop("disabled", false)
                    .selectpicker("refresh");
            } else {

                $("#planta_id").html(
                    `<option value="">Seleccionar...</option>
            <option value="1">BUIN</option>
            <option value="2">TIL TIL</option>
            <option value="3">OSORNO</option>`
                )
                    .selectpicker("refresh");

                $("#pais_id")
                    .prop("disabled", false)
                    .selectpicker("refresh");
            }


        })
        .triggerHandler("change");

    // Tamaño Pallet (tamano_pallet_type_id) inabilitado y luego si se selecciona "SI" en Restricción Paletizado este es habilitado
    $("#restriccion_pallet")
        .change(() => {
            if ($("#restriccion_pallet").val() == 0) {
                $("#tamano_pallet_type_id,#altura_pallet,#permite_sobresalir_carga,#bulto_zunchado,#formato_etiqueta,#etiquetas_pallet")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                // #termocontraible
            } else {
                $("#tamano_pallet_type_id,#altura_pallet,#permite_sobresalir_carga,#bulto_zunchado,#formato_etiqueta,#etiquetas_pallet")
                    .prop("disabled", false)
                    .selectpicker("refresh");
                // #termocontraible
            }
        })
        .triggerHandler("change");


    // Si proceso es Flexo o Flexo con Matriz parcial, la SUMA de rayados deben coincidir con Anchura HM ( todos datos obligatorios) Para todo tipo de ítem y estilo
    // const suma_anchura_hm = () => {

    //     let anchura_hm_ = $("#anchura_hm").val();

    //     // Se valida primero que el campo anchura hm este con datos
    //     if(anchura_hm_ != ''){
    //         let suma_rayado =  parseInt($("#rayado_c1r1").val()) + parseInt($("#rayado_r1_r2").val()) + parseInt($("#rayado_r2_c2").val());

    //         if(anchura_hm_ == suma_rayado){
    //             return true;
    //         }else{
    //             return false;
    //         }
    //     }else{
    //         return false;
    //     }

    // }


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
        "recorte_adicional",
        "largura_hm",
        "anchura_hm",
    ];
    $("#cad_id").change(function () {
        var val = $(this).val();
        return $.ajax({
            type: "GET",
            url: "/getCad",
            data: "cad_id=" + val,
            success: function (data) {
                datos_cad.forEach((element) => {
                    // console.log(element, data);
                    setValue(element, data);
                });
                getMatriz(val);

                // Para rayados hacemos una validacion especial
                // Si el CAD viene con datos en los 3 Rayados, se puedan dejar sólo en 0
                // Para ingresar un dato en los rayados, el CAD debe traer los 3 rayados NULL.
                setRayados(data);

                if (val == "" && $("#tipo_solicitud").val() == 1) {
                    $(
                        "#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto"
                    ).prop("readonly", false);
                }
            },
        });
    });

    const setValue = (val, cad) => {
        $(`#${val}`).prop({ disabled: false, readonly: true }).val(cad[val]);
    };

    const setRayados = (data) => {
        //         "rayado_c1r1",
        // "rayado_r1_r2",
        // "rayado_r2_c2",
        let rayado_c1r1 = data.rayado_c1r1 ? +data.rayado_c1r1 : null;
        let rayado_r1_r2 = data.rayado_r1_r2 ? +data.rayado_r1_r2 : null;
        let rayado_r2_c2 = data.rayado_r2_c2 ? +data.rayado_r2_c2 : null;

        if (rayado_c1r1 && rayado_r1_r2 && rayado_r2_c2) {
            console.log("TODOS LLENOS");
            // seteamos campos
            $(`#rayado_c1r1`)
                .prop({ disabled: false, readonly: false })
                .val(rayado_c1r1);
            $(`#rayado_r1_r2`)
                .prop({ disabled: false, readonly: false })
                .val(rayado_r1_r2);
            $(`#rayado_r2_c2`)
                .prop({ disabled: false, readonly: false })
                .val(rayado_r2_c2);
            // agregamos listener para validar que solo puedan editar el valor a 0 o el valor inicial
            $(`#rayado_c1r1`).on("change", () => {
                if (
                    $("#rayado_c1r1").val() === "0" ||
                    $("#rayado_c1r1").val() == rayado_c1r1
                ) {
                } else {
                    $("#rayado_c1r1").val(rayado_c1r1);
                }
            });
            $(`#rayado_r1_r2`).on("change", () => {
                if (
                    $("#rayado_r1_r2").val() === "0" ||
                    $("#rayado_r1_r2").val() == rayado_r1_r2
                ) {
                } else {
                    $("#rayado_r1_r2").val(rayado_r1_r2);
                }
            });
            $(`#rayado_r2_c2`).on("change", () => {
                if (
                    $("#rayado_r2_c2").val() === "0" ||
                    $("#rayado_r2_c2").val() == rayado_r2_c2
                ) {
                } else {
                    $("#rayado_r2_c2").val(rayado_r2_c2);
                }
            });
        } else if (!rayado_c1r1 && !rayado_r1_r2 && !rayado_r2_c2) {
            console.log("TODOS VACIOS");
            $(`#rayado_c1r1`).val('').prop({
                disabled: false,
                readonly: false,
            });
            $(`#rayado_r1_r2`).val('').prop({
                disabled: false,
                readonly: false,
            });
            $(`#rayado_r2_c2`).val('').prop({
                disabled: false,
                readonly: false,
            });
            $("#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2").off("change");
        } else {
            $(`#rayado_c1r1`)
                //.prop({ disabled: false, readonly: true })
                .val(rayado_c1r1);
            $(`#rayado_r1_r2`)
                //.prop({ disabled: false, readonly: true })
                .val(rayado_r1_r2);
            $(`#rayado_r2_c2`)
                //.prop({ disabled: false, readonly: true })
                .val(rayado_r2_c2);
        }
        if (role == 3 || role == 4 || role == 19 || (role == 8 && tipo == 'create')) {
            $(`#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2`).prop({
                disabled: false,
                readonly: true,
            });
        }
    };

    // Habilita/inabilita la vista del campo de "numero de muestras" segun checklist de "muestra"
    var fileCorreoCliente = document.getElementById("file_check_correo_cliente");
    var filePlanoActual = document.getElementById("file_check_plano_actual");
    var fileBocetoActual = document.getElementById("file_check_boceto_actual");
    var fileSpeed = document.getElementById("file_check_speed");
    var fileOtro = document.getElementById("file_check_otro");
    var fileVbMuestra = document.getElementById("file_check_vb_muestra");
    var fileVbBoce = document.getElementById("file_check_vb_boce");

    $("#check_correo_cliente").click(function () {
        $("#upload_file_correo")[this.checked ? "show" : "hide"]();
        if (this.checked) {
            fileCorreoCliente.click();
        }
    });
    $("#check_plano_actual").click(function () {
        $("#upload_file_plano")[this.checked ? "show" : "hide"]();
        if (this.checked) {
            filePlanoActual.click();
        }
    });
    $("#check_boceto_actual").click(function () {
        $("#upload_file_boceto")[this.checked ? "show" : "hide"]();
        if (this.checked) {
            fileBocetoActual.click();
        }
    });
    $("#check_speed").click(function () {
        $("#upload_file_speed")[this.checked ? "show" : "hide"]();
        if (this.checked) {
            fileSpeed.click();
        }
    });
    $("#check_otro").click(function () {
        $("#upload_file_otro")[this.checked ? "show" : "hide"]();
        if (this.checked) {
            fileOtro.click();
        }
    });

    let fileOpenedMuestra = false


    $("#check_vb_muestra").click(function () {
        // $("#upload_file_vb_muestra")[this.checked ? "show" : "hide"]();
        // if (this.checked) {
        //     fileVbMuestra.click();
        // }

        $("#upload_file_vb_muestra")[this.checked ? "show" : "hide"]();

        if (this.checked) {

            $('#upload_file_vb_muestra').show();

            fileVbMuestra.value = ''; // Restablece el valor
            fileOpenedMuestra = true;

            // Escucha la pérdida de foco (cuando se cierra el selector de archivos)
            // $(window).on('focus', () => {
            //     setTimeout(() => {
            //         if (fileOpenedMuestra) {
            //             $('#check_vb_muestra').prop('checked', false);
            //             console.log('Selección cancelada');
            //             // $('#file_chosen_vb_muestra').html('');
            //             fileOpenedMuestra = false;

            //             $('#upload_file_vb_muestra').hide();
            //         }
            //         $(window).off('focus'); // Desactiva el evento
            //     }, 200);
            // });
            $(window).on('focus', () => {
                setTimeout(() => {
                    // Si no hay archivo seleccionado
                    if (fileOpenedMuestra && (!fileVbMuestra.files || fileVbMuestra.files.length === 0)) {
                        $("#check_vb_muestra").prop('checked', false);
                        console.log("selección cancelada");
                        // $("#file_chosen_vb_muestra").html('');
                        fileOpenedMuestra = false;
                        $("#upload_file_vb_muestra").hide();
                    }

                    $(window).off("focus");
                }, 1000);
            });

            fileVbMuestra.click(); // Abre el explorador de archivos
        }
    });

    let fileOpened = false

    $("#check_vb_boce").click(function () {
        // $("#upload_file_vb_boce")[this.checked ? "show" : "hide"]();
        // if (this.checked) {
        //     fileVbBoce.click();
        // }

        $("#upload_file_vb_boce")[this.checked ? "show" : "hide"]();

        if (this.checked) {

            $('#upload_file_vb_boce').show();
            fileVbBoce.value = ''; // Restablece el valor
            fileOpened = true;

            // Escucha la pérdida de foco (cuando se cierra el selector de archivos)
            // $(window).on('focus', () => {
            //     setTimeout(() => {
            //         if (fileOpened) {
            //             $('#check_vb_boce').prop('checked', false);
            //             console.log('Selección cancelada');
            //             $('#upload_file_vb_boce').hide();

            //             // $('#file_chosen_vb_boce').html('');
            //             fileOpened = false;
            //         }
            //         $(window).off('focus'); // Desactiva el evento
            //     }, 200);
            // });
            $(window).on('focus', () => {
                setTimeout(() => {
                    // Si no hay archivo seleccionado
                    if (fileOpened && (!fileVbBoce.files || fileVbBoce.files.length === 0)) {
                        $("#check_vb_boce").prop('checked', false);
                        console.log("selección cancelada");
                        // $("#file_chosen_vb_muestra").html('');
                        fileOpened = false;
                        $("#upload_file_vb_boce").hide();
                    }

                    $(window).off("focus");
                }, 1000);
            });

            fileVbBoce.click(); // Abre el explorador de archivos
        }
    });

    $("#check_conservar_si").click(function () {
        if (this.checked) {
            $("#check_conservar_no").prop("checked", false);
        }
    });
    $("#check_conservar_no").click(function () {
        if (this.checked) {
            $("#check_conservar_si").prop("checked", false);
        }
    });

    $("#check_armado_automatico_si").click(function () {
        if (this.checked) {
            $("#check_armado_automatico_no").prop("checked", false);
        }
    });
    $("#check_armado_automatico_no").click(function () {
        if (this.checked) {
            $("#check_armado_automatico_si").prop("checked", false);
        }
    });
    fileCorreoCliente.addEventListener('change', function () {
        $('#file_chosen_correo').attr('data-original-title', this.files[0].name);
        $('#file_chosen_correo').show();
        $('#file_chosen_correo').tooltip();
    })

    filePlanoActual.addEventListener('change', function () {
        $('#file_chosen_plano').attr('data-original-title', this.files[0].name);
        $('#file_chosen_plano').show();
        $('#file_chosen_plano').tooltip();
    })

    fileBocetoActual.addEventListener('change', function () {
        $('#file_chosen_boceto').attr('data-original-title', this.files[0].name);
        $('#file_chosen_boceto').show();
        $('#file_chosen_boceto').tooltip();
    })

    fileSpeed.addEventListener('change', function () {
        $('#file_chosen_speed').attr('data-original-title', this.files[0].name);
        $('#file_chosen_speed').show();
        $('#file_chosen_speed').tooltip();
    })

    fileOtro.addEventListener('change', function () {
        $('#file_chosen_otro').attr('data-original-title', this.files[0].name);
        $('#file_chosen_otro').show();
        $('#file_chosen_otro').tooltip();
    })

    fileVbMuestra.addEventListener('change', function () {
        // $('#file_chosen_vb_muestra').attr('data-original-title', this.files[0].name);
        // $('#file_chosen_vb_muestra').show();
        // $('#file_chosen_vb_muestra').tooltip();

        fileOpenedMuestra = false; // Si se selecciona archivo, no se desmarca
        if (this.files.length > 0) {
            $('#file_chosen_vb_muestra')
                .attr('data-original-title', this.files[0].name)
                .show()
                .tooltip();

            $('#check_vb_muestra').prop('disabled', true);
            console.log('Archivo seleccionado muestra:', this.files[0].name);
        } else {
            $('#file_chosen_vb_muestra')
                .attr('data-original-title', '')
                .hide()
        }
    })

    fileVbBoce.addEventListener('change', function () {
        // $('#file_chosen_vb_boce').attr('data-original-title', this.files[0].name);
        // $('#file_chosen_vb_boce').show();
        // $('#file_chosen_vb_boce').tooltip();

        fileOpened = false; // Si se selecciona archivo, no se desmarca
        if (this.files.length > 0) {
            $('#file_chosen_vb_boce')
                .attr('data-original-title', this.files[0].name)
                .show()
                .tooltip();

            $('#check_vb_boce').prop('disabled', true);
            console.log('Archivo seleccionado boceto:', this.files[0].name);
        } else {
            $('#file_chosen_vb_boce')
                .attr('data-original-title', '')
                .hide();
        }
    })

    //Validacion Restriccion de Paletizado solo para rol de vendedor
    //Validacion Restriccion de Paletizado solo para rol de vendedor
    if (role != 4 && role != 3 && role != 19 && role != 18) {
        $("#restriccion_pallet,#tamano_pallet_type_id,#altura_pallet,#permite_sobresalir_carga")
            .prop("disabled", true)
            .selectpicker("refresh");
    }

    if (role == 8 && $("#tipo").val() == 'create') {
        $("#restriccion_pallet,#tamano_pallet_type_id,#altura_pallet,#permite_sobresalir_carga")
            .prop("disabled", false)
            .selectpicker("refresh");
    } else {
        $("#restriccion_pallet,#tamano_pallet_type_id,#altura_pallet,#permite_sobresalir_carga")
            .prop("disabled", true)
            .selectpicker("refresh");
    }

    /*$("#barniz_uv")
         .prop("disabled", true)
         .val("")
         .selectpicker("refresh")
         .closest("div.form-group")
         .removeClass("error");*/


    // RECUBRIMIENTO EXTERNO BARNIZ UV
    $("#percentage_coverage_external")
        .change(() => {

            if ($("#coverage_external_id").val() == 4) {
                $("#porcentanje_barniz_uv")
                    .prop("disabled", true)
                    .val($("#percentage_coverage_external").val());

            } else {
                $("#porcentanje_barniz_uv")
                    .prop("disabled", true)
                    .val("");
            }

        })
        .triggerHandler("change");

    if (role == 3 || role == 4 || role == 5 || role == 6 || role == 19 || (role == 8 && tipo == 'create')) {//Validar roles vendedores
        //Manejo de Select Seccion DATOS PARA DESARROLLO
        $("#product_type_developing_id")
            .change(() => {

                var val = $("#product_type_developing_id").val();

                if (val == 1) {

                    $("#food_type_id,#expected_use_id,#recycled_use_id")
                        .prop("disabled", true)
                        .prop('required', false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    $("#class_substance_packed_id,#transportation_way_id")
                        .prop("disabled", false)
                        .prop('required', true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                } else {
                    if (val == 3) {

                        $("#class_substance_packed_id,#transportation_way_id")
                            .prop("disabled", true)
                            .prop('required', false)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");

                        $("#food_type_id,#expected_use_id,#recycled_use_id")
                            .prop("disabled", false)
                            .prop('required', true)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    } else {

                        $("#food_type_id,#expected_use_id,#recycled_use_id,#class_substance_packed_id,#transportation_way_id")
                            .prop("disabled", true)
                            .prop('required', false)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    }
                }
            })
            .triggerHandler("change");
    } else {
        if (role === 18) {
            //Manejo de Select Seccion DATOS PARA DESARROLLO
            $("#product_type_developing_id")
                .change(() => {

                    var val = $("#product_type_developing_id").val();
                    if (val == 1) {

                        $("#food_type_id,#expected_use_id,#recycled_use_id")
                            .prop("disabled", true)
                            .prop('required', false)
                            .val("")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");

                        $("#class_substance_packed_id,#transportation_way_id")
                            .prop("disabled", false)
                            .prop('required', false)
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    } else {
                        if (val == 3) {

                            $("#class_substance_packed_id,#transportation_way_id")
                                .prop("disabled", true)
                                .prop('required', false)
                                .val("")
                                .selectpicker("refresh")
                                .closest("div.form-group")
                                .removeClass("error");

                            $("#food_type_id,#expected_use_id,#recycled_use_id")
                                .prop("disabled", false)
                                .prop('required', false)
                                .selectpicker("refresh")
                                .closest("div.form-group")
                                .removeClass("error");
                        } else {

                            $("#food_type_id,#expected_use_id,#recycled_use_id,#class_substance_packed_id,#transportation_way_id")
                                .prop("disabled", true)
                                .prop('required', false)
                                .val("")
                                .selectpicker("refresh")
                                .closest("div.form-group")
                                .removeClass("error");
                        }
                    }
                })
                .triggerHandler("change");
        }
    }


    //Bloqueo de Pegado para rol de diseñador grafico
    if (role == 8) {
        if (tipo == 'create') {
            $("#pegado_terminacion")
                .prop("disabled", false)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        } else {
            $("#pegado_terminacion")
                .prop("disabled", true)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        }

    }

    //Vendedor Externo se asigna cliente edipac por defecto
    if (role == 19) {
        $("#client_id")
            .prop("disabled", false)
            .prop("readonly", true)
            .val(8)
            .selectpicker("refresh")
            .triggerHandler("change");

        $("#org_venta_id")
            .prop("disabled", true)
            .prop("readonly", true)
            .val(1)
            .selectpicker("refresh")
            .triggerHandler("change");

        $('#impresion option[value="1"]').remove();
    }

    if ($("#tipo_solicitud").val() == 5) {
        $("#carton_id,#carton_color")
            .prop("disabled", true)
            .selectpicker("refresh");
    }

    if ($("#tipo_solicitud").val() == 1 || $("#tipo_solicitud").val() == 5 || $("#tipo_solicitud").val() == 7) {

        $('.col-termocontraible').show();
    } else {
        $('.col-termocontraible').hide();
    }

    //bloqueo check muestras y boceto
    if ((role == 4 || role == 3 || role == 19 || role == 6 || role == 7 || role == 8 || role == 5) && $('#state_id').val() != 7 ) {

        console.log('boceto old')


        $('#check_vb_boce').prop('disabled', false);
    } else {
        $('#check_vb_boce').prop('disabled', true);

    }

    if ((role == 4 || role == 3 || role == 19 || role == 6 || role == 7 || role == 8 || role == 5) && $('#state_id').val() != 7) {

        console.log('muestra old');
        $('#check_vb_muestra').prop('disabled', false);
    } else {
        $('#check_vb_muestra').prop('disabled', true);

    }

    $('#caracteristicas_adicionales')
        .prop("disabled", true)

    ///Ajuste Evolutivo 24-06 de fecha 03-04-2024 correo del cliente
    /*if(role !=3 && role !=4 && role !=5 && role !=6 && role !=18){
        $('#tipo_tabique,#rayado_desfasado')
            .prop("disabled",true);
    }*/
    if (role != 3 && role != 4 && role != 5 && role != 6 && role != 7 && role != 8 && role != 18) {
        $('#impresion_borde,#impresion_sobre_rayado')
            .prop("disabled", true);
    }
    ///
    ///Ajuste Evolutivo 24-06 de fecha 24-04-2024 correo del cliente
    if (role == 5 || role == 6) {
        $('#golpes_largo,#golpes_ancho,#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2,#process_id')
            .prop("disabled", false)
            .prop("readonly", false)
            .selectpicker("refresh");

    }
    ///
    $('#sec_operacional_principal').prop('disabled', true).selectpicker('refresh');
    $('#sec_operacional_1').prop('disabled', true).selectpicker('refresh');
    $('#sec_operacional_2').prop('disabled', true).selectpicker('refresh');
    $('#matriz_id').prop('disabled', true).selectpicker('refresh');


    // $('#sec_ope_ppal_planta_ori_1').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_1_planta_ori_1').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_2_planta_ori_1').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_3_planta_ori_1').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_4_planta_ori_1').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_5_planta_ori_1').prop('disabled',true).selectpicker('refresh');

    // $('#sec_ope_ppal_planta_ori_2').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_1_planta_ori_2').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_2_planta_ori_2').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_3_planta_ori_2').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_4_planta_ori_2').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_5_planta_ori_2').prop('disabled',true).selectpicker('refresh');

    // $('#sec_ope_ppal_planta_ori_3').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_1_planta_ori_3').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_2_planta_ori_3').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_3_planta_ori_3').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_4_planta_ori_3').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_5_planta_ori_3').prop('disabled',true).selectpicker('refresh');

    // $('#sec_ope_ppal_planta_ori_4').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_1_planta_ori_4').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_2_planta_ori_4').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_3_planta_ori_4').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_4_planta_ori_4').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_5_planta_ori_4').prop('disabled',true).selectpicker('refresh');

    // $('#sec_ope_ppal_planta_ori_5').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_1_planta_ori_5').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_2_planta_ori_5').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_3_planta_ori_5').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_4_planta_ori_5').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_5_planta_ori_5').prop('disabled',true).selectpicker('refresh');

    // $('#sec_ope_ppal_planta_ori_6').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_1_planta_ori_6').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_2_planta_ori_6').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_3_planta_ori_6').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_4_planta_ori_6').prop('disabled',true).selectpicker('refresh');
    // $('#sec_ope_atl_5_planta_ori_6').prop('disabled',true).selectpicker('refresh');

    $('#sec_ope_ppal_planta_ori_1,#sec_ope_atl_1_planta_ori_1,#sec_ope_atl_2_planta_ori_1,#sec_ope_atl_3_planta_ori_1,#sec_ope_atl_4_planta_ori_1,#sec_ope_atl_5_planta_ori_1,#sec_ope_ppal_planta_ori_2,#sec_ope_atl_1_planta_ori_2,#sec_ope_atl_2_planta_ori_2,#sec_ope_atl_3_planta_ori_2,#sec_ope_atl_4_planta_ori_2,#sec_ope_atl_5_planta_ori_2,#sec_ope_ppal_planta_ori_3,#sec_ope_atl_1_planta_ori_3,#sec_ope_atl_2_planta_ori_3,#sec_ope_atl_3_planta_ori_3,#sec_ope_atl_4_planta_ori_3,#sec_ope_atl_5_planta_ori_3,#sec_ope_ppal_planta_ori_4,#sec_ope_atl_1_planta_ori_4,#sec_ope_atl_2_planta_ori_4,#sec_ope_atl_3_planta_ori_4,#sec_ope_atl_4_planta_ori_4,#sec_ope_atl_5_planta_ori_4,#sec_ope_ppal_planta_ori_5,#sec_ope_atl_1_planta_ori_5,#sec_ope_atl_2_planta_ori_5,#sec_ope_atl_3_planta_ori_5,#sec_ope_atl_4_planta_ori_5,#sec_ope_atl_5_planta_ori_5,#sec_ope_ppal_planta_ori_6,#sec_ope_atl_1_planta_ori_6,#sec_ope_atl_2_planta_ori_6,#sec_ope_atl_3_planta_ori_6,#sec_ope_atl_4_planta_ori_6,#sec_ope_atl_5_planta_ori_6')
        .prop('disabled', true)
        .selectpicker('refresh');
    $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3,#agregar_fila_planta_auxiliar_1')
        .prop('disabled', true)
        .selectpicker('refresh');
    $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3,#agregar_fila_planta_auxiliar_2')
        .prop('disabled', true)
        .selectpicker('refresh');
    $('#check_planta_aux_1').prop('disabled', true);
    $('#check_planta_aux_2').prop('disabled', true);
    $("#planta_original_sec_ope").prop("disabled", true);
    $("#planta_auxiliar_1_sec_ope").prop("disabled", true);
    $("#planta_auxiliar_2_sec_ope").prop("disabled", true);

    //Evolutivo 24-11 ajuste 2 - Inicio
    if (role != 3 && role != 4 && role != 18 && role != 19 && role != 5 && role != 7) {
        $('#trazabilidad').prop('disabled', true);
    } else {
        $('#trazabilidad').prop('disabled', false);
    }
    //Evolutivo 24-11 ajuste 2 - Fin
});

function getMatriz(cad_id) {
    $('#golpes_largo').val('');
    $('#golpes_ancho').val('');
    $('#separacion_golpes_largo').val('');
    $('#separacion_golpes_ancho').val('');
    $('#cuchillas').val('');
    $('#tipo_matriz_text').val('');
    return $.ajax({
        type: "GET",
        url: "/getMatriz",
        data: "cad_id=" + cad_id,
        success: function (data) {
            $('.matriz_select_container').html(data);
            $('#matriz_id').prop('disabled', false).val("").selectpicker("refresh");
            $('#matriz_id').change(function () {
                var val = $(this).val();

                console.log(val);
                return $.ajax({
                    type: "GET",
                    url: "/getMatrizData",
                    data: "matriz_id=" + val,
                    success: function (data) {
                        console.log(data);
                        $('#golpes_largo').val(data.cantidad_largo_matriz);
                        $('#golpes_ancho').val(data.cantidad_ancho_matriz);
                        $('#separacion_golpes_largo').val(data.separacion_largo_matriz);
                        $('#separacion_golpes_ancho').val(data.separacion_ancho_matriz);
                        $('#cuchillas').val(data.cuchillas);
                        $('#tipo_matriz_text').val(data.tipo_matriz);
                        if (data.tipo_matriz == 'Completa') {
                            $('#rayado_c1r1').val('');
                            $('#rayado_r1_r2').val('');
                            $('#rayado_r2_c2').val('');
                        }
                    },
                });
            });
        },
    });
}

///Formula Mckee
$("#button_formula_mckee").click(function () {
    if ($("#carton_id").val() != '') {
        $('#carton_id_mckee')
            .val($("#carton_id").val())
            .selectpicker('refresh');
    } else {
        $('#carton_id_mckee')
            .prop("disabled", true)
            .val('')
            .selectpicker('refresh');
    }
    $('#ancho_mckee,#alto_mckee,#perimetro_mckee,#ect_mckee,#espesor_mckee,#bct_kilos_mckee,#bct_lib_mckee')
        .prop("disabled", true)
        .val('');
    $('#largo_mckee')
        .val('')
        .focus();

    $("#seccion_combinabilidad").removeClass("show");

    $('#hc_combinabilidad,#formato_optimo,#numero_cortes,#perdida_minima,#perdida_minima_mm,#1750_combinabilidad,#1830_combinabilidad,#1900_combinabilidad,#1950_combinabilidad,#2040_combinabilidad,#2180_combinabilidad,#2250_combinabilidad,#2350_combinabilidad,#2450_combinabilidad,#2500_combinabilidad')
        .prop("disabled", true)
        .val('');
    $('#carton_id_combinabilidad')
        .val('')
        .selectpicker('refresh');

    $('#button_aplicar_mckee').prop("disabled", true);
});

$('#largo_mckee').change(function () {
    $('#ancho_mckee')
        .prop("disabled", false)
        .val('')
        .focus();
    $('#alto_mckee,#perimetro_mckee,#ect_mckee,#espesor_mckee,#bct_kilos_mckee,#bct_lib_mckee')
        .prop("disabled", true)
        .val('');
    if ($("#carton_id_mckee").val() == '') {
        $('#carton_id_mckee')
            .prop("disabled", true)
            .val('')
            .selectpicker('refresh');
    }
});

$("#largo_mckee").keypress(function (e) {
    if (e.which == 13) {
        $("#largo_mckee").trigger("change");
        return false;
    }
});

$('#ancho_mckee').change(function () {
    $('#alto_mckee')
        .prop("disabled", false)
        .val('')
        .focus();
    $('#perimetro_mckee,#ect_mckee,#espesor_mckee,#bct_kilos_mckee,#bct_lib_mckee')
        .prop("disabled", true)
        .val('');
    if ($("#carton_id_mckee").val() == '') {
        $('#carton_id_mckee')
            .prop("disabled", true)
            .val('')
            .selectpicker('refresh');
    }
});

$("#ancho_mckee").keypress(function (e) {
    if (e.which == 13) {
        $("#ancho_mckee").trigger("change");
        return false;
    }
});

$('#alto_mckee').change(function () {

    var largo = $('#largo_mckee').val();
    var ancho = $('#ancho_mckee').val();
    var perimetro = (parseInt(largo) + parseInt(ancho)) * 2;
    $('#perimetro_mckee')
        .prop("disabled", true)
        .val(perimetro);

    if ($("#carton_id_mckee").val() != '') {
        $('#carton_id_mckee')
            .prop("disabled", false)
            .focus();
        $("#carton_id_mckee").trigger("change");
    } else {
        $('#carton_id_mckee')
            .prop("disabled", false)
            .val('')
            .selectpicker('refresh')
            .focus();
    }

});

$("#alto_mckee").keypress(function (e) {
    if (e.which == 13) {
        $("#alto_mckee").trigger("change");
        return false;
    }
});

$('#carton_id_mckee').change(function () {
    var val = $(this).val();
    if (val == $('#carton_id').val()) {
        var ect = parseInt(0);
        var espesor = parseFloat(0);
        var bct_kilos = parseFloat(0);
        var bct_lb = parseFloat(0);
        var perimetro = $('#perimetro_mckee').val();

        return $.ajax({
            type: "GET",
            url: "/getCarton",
            data: "carton_id=" + val,
            success: function (data) {
                if (data.ect_min != null) {
                    ect = parseInt(data.ect_min);
                }

                if (data.espesor != null) {
                    espesor = parseFloat(data.espesor);
                }

                bct_kilos = (parseFloat(0.325) * ect * (Math.pow((espesor - parseFloat(0.2)), parseFloat(0.508)))) * (Math.pow((perimetro / parseInt(10)), parseFloat(0.492)));
                bct_lb = bct_kilos / parseFloat(0.454);

                $('#ect_mckee').prop("disabled", true).val(ect);
                $('#espesor_mckee').prop("disabled", true).val(espesor);
                $('#bct_kilos_mckee').prop("disabled", true).val(bct_kilos.toFixed(0));
                $('#bct_lib_mckee').prop("disabled", true).val(bct_lb.toFixed(0));
                $('#button_aplicar_mckee').prop("disabled", false);

            },
            error: function (e) {
                console.log(e.responseText);
            },
            async: true
        });
    } else {
        alert("El cartón es diferente al de la Orden de Trabajo. Debe seleccionar el mismo para generar el cálculo");
        $('#button_aplicar_mckee').prop("disabled", true);
        $('#perimetro_mckee,#ect_mckee,#espesor_mckee,#bct_kilos_mckee,#bct_lib_mckee')
            .prop("disabled", true)
            .val('');

    }
});

$("#button_aplicar_mckee").click(function () {

    var bct_kilos = $('#bct_kilos_mckee').val();
    var bct_lb = $('#bct_lib_mckee').val();
    var ect = $('#ect_mckee').val();
    var espesor = $('#espesor_mckee').val();
    var carton = $('#carton_id_mckee').val();
    var perimetro = $('#perimetro_mckee').val();
    var alto = $('#alto_mckee').val();
    var ancho = $('#ancho_mckee').val();
    var largo = $('#largo_mckee').val();
    var d = new Date();

    const dt = new Date();
    const padL = (nr, len = 2, chr = `0`) => `${nr}`.padStart(2, chr);

    var fecha = `${padL(dt.getDate())}-${padL(dt.getMonth() + 1)}-${dt.getFullYear()}
                ${padL(dt.getHours())}:${padL(dt.getMinutes())}:${padL(dt.getSeconds())}`

    $('#bct_min_kg').val(bct_kilos);
    $('#bct_kilos_mckee_value').val(bct_kilos);
    $('#bct_min_lb').val(bct_lb);
    $('#bct_lib_mckee_value').val(bct_lb);
    $('#ect_mckee_value').val(ect);
    $('#espesor_mckee_value').val(espesor);
    $('#carton_id_mckee_value').val(carton);
    $('#perimetro_mckee_value').val(perimetro);
    $('#alto_mckee_value').val(alto);
    $('#ancho_mckee_value').val(ancho);
    $('#largo_mckee_value').val(largo);
    $('#aplicar_mckee_value').val(1);
    $('#fecha_mckee_value').val(fecha);

});
///Fin Formula Mckee

///Formula Combinabilidad Analisis Anchura
$("#button_formula_combinabilidad").click(function () {

    if ($("#carton_id").val() != '') {
        $('#formato_optimo,#numero_cortes,#perdida_minima,#perdida_minima_mm,#1750_combinabilidad,#1830_combinabilidad,#1900_combinabilidad,#1950_combinabilidad,#2040_combinabilidad,#2180_combinabilidad,#2250_combinabilidad,#2350_combinabilidad,#2450_combinabilidad,#2500_combinabilidad')
            .prop("disabled", true)
            .val('');
        $('#carton_id_combinabilidad')
            .val($("#carton_id").val())
            .selectpicker('refresh');

    } else {
        $('#hc_combinabilidad,#formato_optimo,#numero_cortes,#perdida_minima,#perdida_minima_mm,#1750_combinabilidad,#1830_combinabilidad,#1900_combinabilidad,#1950_combinabilidad,#2040_combinabilidad,#2180_combinabilidad,#2250_combinabilidad,#2350_combinabilidad,#2450_combinabilidad,#2500_combinabilidad')
            .prop("disabled", true)
            .val('');
    }


    $("#seccion_formula_mckee").removeClass("show");
});

$('#carton_id_combinabilidad').change(function () {
    var val = $(this).val();

    if (val == $('#carton_id').val()) {
        $('#carton_combinabilidad_select').removeClass("error");
        $('#hc_combinabilidad')
            .prop("disabled", false)
            .val('');
    } else {
        alert("El cartón es diferente al de la Orden de Trabajo. Debe seleccionar el mismo para generar el cálculo");
        $('#hc_combinabilidad,#formato_optimo,#numero_cortes,#perdida_minima,#perdida_minima_mm,#1750_combinabilidad,#1830_combinabilidad,#1900_combinabilidad,#1950_combinabilidad,#2040_combinabilidad,#2180_combinabilidad,#2250_combinabilidad,#2350_combinabilidad,#2450_combinabilidad,#2500_combinabilidad')
            .prop("disabled", true)
            .val('');

    }

});

$('#hc_combinabilidad').change(function () {

    $('#formato_optimo,#numero_cortes,#perdida_minima,#perdida_minima_mm,#1750_combinabilidad,#1830_combinabilidad,#1900_combinabilidad,#1950_combinabilidad,#2040_combinabilidad,#2180_combinabilidad,#2250_combinabilidad,#2350_combinabilidad,#2450_combinabilidad,#2500_combinabilidad')
        .prop("disabled", true)
        .val('');

    var hc = $(this).val();
    var val = $('#carton_id_combinabilidad').val();
    var combinabilidad = '';
    var result = parseInt(0);
    var result_mm = parseInt(0);
    var min = parseInt(101);
    var min_mm = parseInt(1001);
    var formato_optimo = '';
    var numero_cortes = parseInt(0);

    return $.ajax({
        type: "GET",
        url: "/getCarton",
        data: "carton_id=" + val,

        success: function (data) {

            combinabilidad = data.combinabilidad.split(',');

            for (i = 0; i < combinabilidad.length; i++) {

                result = (1 - (Math.trunc(Math.trunc(parseInt(combinabilidad[i]) - 30) / parseFloat(hc)) * (parseFloat(hc) / parseInt(combinabilidad[i])))) * 100;
                $('#' + combinabilidad[i] + '_combinabilidad').val(result.toFixed(0) + "%");

                if (min > result) {
                    min = result.toFixed(0);
                    formato_optimo = combinabilidad[i];
                }

                result_mm = (parseInt(combinabilidad[i]) - 30) - (Math.trunc((parseInt(combinabilidad[i]) - 30) / parseFloat(hc))) * parseFloat(hc);

                if (min_mm > result_mm) {
                    min_mm = result_mm.toFixed(0);
                }
            }

            numero_cortes = parseInt(formato_optimo) / parseFloat(hc);

            $('#perdida_minima').val(min + "%");
            $('#perdida_minima_mm').val(min_mm);
            $('#formato_optimo').val(formato_optimo);
            $('#numero_cortes').val(numero_cortes.toFixed(0));

        },
        error: function (e) {
            console.log(e.responseText);
        },
        async: true
    });
});

$("#hc_combinabilidad").keypress(function (e) {
    if (e.which == 13) {
        $("#hc_combinabilidad").trigger("change");
        return false;
    }
});
///Fin Formula Analisis Anchura

function getListaCartonOffset(impresion) {

    return $.ajax({
        type: "GET",
        url: "/getListaCartonOffset",
        data: "impresion=" + impresion,
        success: function (data) {
            //data = $.parseHTML(data);
            $("#carton_id")
                .prop("disabled", false)
                .empty()
                .append(data)
                .selectpicker("refresh");


            /*$("#carton_id")
                .val(carton_val)
                .selectpicker("refresh"); */
        },
    });
}

/// Evolutivo 24-06
$("#button_aplicar_caracteristica").click(function () {
    var result_caracteristicas = "";
    if ($('#check_na').is(':checked')) {
        result_caracteristicas = 'N/A';
    } else {
        if ($('#check_a').is(':checked')) {
            result_caracteristicas += 'A';
        }

        //Ceja Pegado Extendida
        if ($('#check_c').is(':checked')) {
            result_caracteristicas += 'C';
        }

        //Pegado Exterior
        if ($('#check_e').is(':checked')) {
            result_caracteristicas += 'E';
        }

        //Hibrida
        if ($('#check_h').is(':checked')) {
            result_caracteristicas += 'H';
        }

        //Cabezal o lateral inclinado
        if ($('#check_i').is(':checked')) {
            result_caracteristicas += 'I';
        }

        //Cajas doble Lateral
        if ($('#check_l').is(':checked')) {
            result_caracteristicas += 'L';
        }

        //Nervio refuerso pegado o autoarmable
        if ($('#check_n').is(':checked')) {
            result_caracteristicas += 'N';
        }

        //Esquinero
        if ($('#check_q').is(':checked')) {
            result_caracteristicas += 'Q';
        }

        //RRP
        if ($('#check_r').is(':checked')) {
            result_caracteristicas += 'R';
        }

        //Troquel adicional
        if ($('#check_t').is(':checked')) {
            result_caracteristicas += 'T';
        }

        //Rayados desplazados
        if ($('#check_y').is(':checked')) {
            result_caracteristicas += 'Y';
        }

        //Pieza adicional
        if ($('#check_x').is(':checked')) {
            result_caracteristicas += 'X';
        }

        if ($('#check_p').is(':checked')) {
            result_caracteristicas += 'P';
        }
    }

    $('#caracteristicas_adicionales')
        .val(result_caracteristicas);

    $('#button_cerrar_caracteristica').click();


});

$("#button_cerrar_caracteristica").click(function () {
    $('#check_a,#check_c,#check_e,#check_h,#check_i,#check_l,#check_n,#check_q,#check_r,#check_t,#check_y,#check_x,#check_p')
        .prop("checked", false);
});

$("#modal-carac-adicional").on("show.bs.modal", function (e) {
    var valor = $('#caracteristicas_adicionales').val();
    if (valor == 'N/A') {
        $('#check_na')
            .prop("checked", true);

        $('#check_a,#check_c,#check_e,#check_h,#check_i,#check_l,#check_n,#check_q,#check_r,#check_t,#check_y,#check_x,#check_p')
            .prop("checked", false)
            .prop("disabled", true);
    } else {
        if (valor.indexOf('A') > -1) {
            $('#check_a')
                .prop("checked", true);
        }

        if (valor.indexOf('C') > -1) {
            $('#check_c')
                .prop("checked", true);
        }

        if (valor.indexOf('E') > -1) {
            $('#check_e')
                .prop("checked", true);
        }

        if (valor.indexOf('H') > -1) {
            $('#check_h')
                .prop("checked", true);
        }

        if (valor.indexOf('I') > -1) {
            $('#check_i')
                .prop("checked", true);
        }

        if (valor.indexOf('L') > -1) {
            $('#check_l')
                .prop("checked", true);
        }

        if (valor.indexOf('N') > -1) {
            $('#check_n')
                .prop("checked", true);
        }

        if (valor.indexOf('Q') > -1) {
            $('#check_q')
                .prop("checked", true);
        }

        if (valor.indexOf('R') > -1) {
            $('#check_r')
                .prop("checked", true);
        }

        if (valor.indexOf('T') > -1) {
            $('#check_t')
                .prop("checked", true);
        }

        if (valor.indexOf('Y') > -1) {
            $('#check_y')
                .prop("checked", true);
        }

        if (valor.indexOf('X') > -1) {
            $('#check_x')
                .prop("checked", true);
        }
        if (valor.indexOf('P') > -1) {
            $('#check_p')
                .prop("checked", true);
        }
    }
});

$("#check_na").click(function () {
    if (this.checked) {
        $('#check_a,#check_c,#check_e,#check_h,#check_i,#check_l,#check_n,#check_q,#check_r,#check_t,#check_y,#check_x,#check_p')
            .prop("checked", false)
            .prop("disabled", true);
    } else {
        $('#check_a,#check_c,#check_e,#check_h,#check_i,#check_l,#check_n,#check_q,#check_r,#check_t,#check_y,#check_x,#check_p')
            .prop("disabled", false);
    }
});
///

/// Evolutivo 24-07
$("#oc").change(function () {
    var val = $(this).val();
    if ($("#role_id").val() == 4 || $("#role_id").val() == 3) {
        if (val == 1) {
            document.getElementById("subida_archivo_oc").style.display = "block";
        } else {
            document.getElementById("subida_archivo_oc").style.display = "none";
        }
    }
});
///

$('#planta_id').change(function () {

    // console.log($('#planta_id').val());
    var val = $(this).val();

    if ($("#planta_id").val() == 1) {//Buin
        $("#planta_original_sec_ope")
            .prop("disabled", true)
            .val('Buin');
        $("#planta_aux_1_sec_ope")
            .prop("disabled", true)
            .val('TilTil');
        $("#planta_aux_2_sec_ope")
            .prop("disabled", true)
            .val('Osorno');
        getSecuenciasOperacionalesPlanta(1);
        getSecuenciasOperacionalesPlantaAux1(2);
        getSecuenciasOperacionalesPlantaAux2(3);
        $('#sec_ope_planta_orig_id').val(1);
        $('#sec_ope_planta_aux_1_id').val(2);
        $('#sec_ope_planta_aux_2_id').val(3);
        $('#check_planta_aux_1').prop('disabled', false);
        $('#check_planta_aux_2').prop('disabled', false);
        $('#termocontraible').prop('disabled', true);
        $('#termocontraible').val(0).selectpicker("refresh");

    }

    if ($("#planta_id").val() == 2) {//TilTil
        $("#planta_original_sec_ope")
            .prop("disabled", true)
            .val('TilTil');
        $("#planta_aux_1_sec_ope")
            .prop("disabled", true)
            .val('Buin');
        $("#planta_aux_2_sec_ope")
            .prop("disabled", true)
            .val('Osorno');
        getSecuenciasOperacionalesPlanta(2);
        getSecuenciasOperacionalesPlantaAux1(1);
        getSecuenciasOperacionalesPlantaAux2(3);
        $('#sec_ope_planta_orig_id').val(2);
        $('#sec_ope_planta_aux_1_id').val(1);
        $('#sec_ope_planta_aux_2_id').val(3);
        $('#check_planta_aux_1').prop('disabled', false);
        $('#check_planta_aux_2').prop('disabled', false);
        $('#termocontraible').prop('disabled', false);
        $('#termocontraible').selectpicker("refresh");

    }

    if ($("#planta_id").val() == 3) {//Osorno
        $("#planta_original_sec_ope")
            .prop("disabled", true)
            .val('Osorno');
        $("#planta_aux_1_sec_ope")
            .prop("disabled", true)
            .val('Buin');
        $("#planta_aux_2_sec_ope")
            .prop("disabled", true)
            .val('TilTil');
        getSecuenciasOperacionalesPlanta(3);
        getSecuenciasOperacionalesPlantaAux1(1);
        getSecuenciasOperacionalesPlantaAux2(2);
        $('#sec_ope_planta_orig_id').val(3);
        $('#sec_ope_planta_aux_1_id').val(1);
        $('#sec_ope_planta_aux_2_id').val(2);
        $('#check_planta_aux_1').prop('disabled', false);
        $('#termocontraible').prop('disabled', true);
        $('#termocontraible').val(0).selectpicker("refresh");
    }


    return $.ajax({
        type: "GET",
        url: "/getSecuenciasOperacionales",
        data: "planta_id=" + val,
        success: function (data) {

            $('.form-sec-operacional').html(data);

            $('#sec_operacional_principal').selectpicker("refresh");
            $('#sec_operacional_1').selectpicker("refresh");
            $('#sec_operacional_2').selectpicker("refresh");
        },
    });

});



// AGREGAR SECUENCIA OPERCIONAL

// function selectPlanta() {
//     console.log('prueba');
// }

// Se sehabilitan los selectores de secuencias operacionales


function getSecuenciasOperacionalesPlanta(planta_id) {

    $('#sec_ope_ppal_planta_ori_1,#sec_ope_atl_1_planta_ori_1,#sec_ope_atl_2_planta_ori_1,#sec_ope_atl_3_planta_ori_1,#sec_ope_atl_4_planta_ori_1,#sec_ope_atl_5_planta_ori_1,#sec_ope_ppal_planta_ori_2,#sec_ope_atl_1_planta_ori_2,#sec_ope_atl_2_planta_ori_2,#sec_ope_atl_3_planta_ori_2,#sec_ope_atl_4_planta_ori_2,#sec_ope_atl_5_planta_ori_2,#sec_ope_ppal_planta_ori_3,#sec_ope_atl_1_planta_ori_3,#sec_ope_atl_2_planta_ori_3,#sec_ope_atl_3_planta_ori_3,#sec_ope_atl_4_planta_ori_3,#sec_ope_atl_5_planta_ori_3,#sec_ope_ppal_planta_ori_4,#sec_ope_atl_1_planta_ori_4,#sec_ope_atl_2_planta_ori_4,#sec_ope_atl_3_planta_ori_4,#sec_ope_atl_4_planta_ori_4,#sec_ope_atl_5_planta_ori_4,#sec_ope_ppal_planta_ori_5,#sec_ope_atl_1_planta_ori_5,#sec_ope_atl_2_planta_ori_5,#sec_ope_atl_3_planta_ori_5,#sec_ope_atl_4_planta_ori_5,#sec_ope_atl_5_planta_ori_5,#sec_ope_ppal_planta_ori_6,#sec_ope_atl_1_planta_ori_6,#sec_ope_atl_2_planta_ori_6,#sec_ope_atl_3_planta_ori_6,#sec_ope_atl_4_planta_ori_6,#sec_ope_atl_5_planta_ori_6')
        .html("")
        .prop('disabled', false)
        .selectpicker('refresh');
    $('#agregar_fila_planta_original').prop('disabled', false);
    return $.ajax({
        type: "GET",
        url: "/getSecuenciasOperacionalesPlanta",
        data: "planta_id=" + planta_id,
        success: function (data) {
            console.log(data);
            $('#sec_ope_ppal_planta_ori_1,#sec_ope_atl_1_planta_ori_1,#sec_ope_atl_2_planta_ori_1,#sec_ope_atl_3_planta_ori_1,#sec_ope_atl_4_planta_ori_1,#sec_ope_atl_5_planta_ori_1,#sec_ope_ppal_planta_ori_2,#sec_ope_atl_1_planta_ori_2,#sec_ope_atl_2_planta_ori_2,#sec_ope_atl_3_planta_ori_2,#sec_ope_atl_4_planta_ori_2,#sec_ope_atl_5_planta_ori_2,#sec_ope_ppal_planta_ori_3,#sec_ope_atl_1_planta_ori_3,#sec_ope_atl_2_planta_ori_3,#sec_ope_atl_3_planta_ori_3,#sec_ope_atl_4_planta_ori_3,#sec_ope_atl_5_planta_ori_3,#sec_ope_ppal_planta_ori_4,#sec_ope_atl_1_planta_ori_4,#sec_ope_atl_2_planta_ori_4,#sec_ope_atl_3_planta_ori_4,#sec_ope_atl_4_planta_ori_4,#sec_ope_atl_5_planta_ori_4,#sec_ope_ppal_planta_ori_5,#sec_ope_atl_1_planta_ori_5,#sec_ope_atl_2_planta_ori_5,#sec_ope_atl_3_planta_ori_5,#sec_ope_atl_4_planta_ori_5,#sec_ope_atl_5_planta_ori_5,#sec_ope_ppal_planta_ori_6,#sec_ope_atl_1_planta_ori_6,#sec_ope_atl_2_planta_ori_6,#sec_ope_atl_3_planta_ori_6,#sec_ope_atl_4_planta_ori_6,#sec_ope_atl_5_planta_ori_6')
                .html(data)
                .prop('disabled', false)
                .selectpicker('refresh');
        },
    });
}

function getSecuenciasOperacionalesPlantaAux1(planta_id) {

    $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3')
        .html("")
        .prop('disabled', true)
        .selectpicker('refresh');

    // console.log($('#planta_id').val());

    return $.ajax({
        type: "GET",
        url: "/getSecuenciasOperacionalesPlanta",
        data: "planta_id=" + planta_id,
        success: function (data) {
            $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3')
                .html(data)
                .prop('disabled', true)
                .selectpicker('refresh');
        },
    });



}

function getSecuenciasOperacionalesPlantaAux2(planta_id) {

    $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3')
        .html("")
        .prop('disabled', true)
        .selectpicker('refresh');

    // console.log($('#planta_id').val());

    return $.ajax({
        type: "GET",
        url: "/getSecuenciasOperacionalesPlanta",
        data: "planta_id=" + planta_id,
        success: function (data) {
            $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3')
                .html(data)
                .prop('disabled', true)
                .selectpicker('refresh');
        },
    });



}

//Manejo de los check de las Plantas de la Secuencia Operacional
$("#check_planta_aux_1").click(function () {
    var cantidad_filas = $('#sec_ope_planta_aux_1_filas').val();
    if (this.checked) {

        for (let i = 1; i <= (cantidad_filas); i++) {

            $('#sec_ope_ppal_planta_aux_1_' + i)
                .prop('disabled', false)
                .selectpicker('refresh');

            $('#sec_ope_atl_1_planta_aux_1_' + i)
                .prop('disabled', false)
                .selectpicker('refresh');

            $('#sec_ope_atl_2_planta_aux_1_' + i)
                .prop('disabled', false)
                .selectpicker('refresh');
        }

        $('#agregar_fila_planta_auxiliar_1').prop('disabled', false);

    } else {
        for (let i = 1; i <= (cantidad_filas); i++) {

            $('#sec_ope_ppal_planta_aux_1_' + i)
                .prop('disabled', true)
                .selectpicker('refresh');

            $('#sec_ope_atl_1_planta_aux_1_' + i)
                .prop('disabled', true)
                .selectpicker('refresh');

            $('#sec_ope_atl_2_planta_aux_1_' + i)
                .prop('disabled', true)
                .selectpicker('refresh');
        }

        $('#agregar_fila_planta_auxiliar_1').prop('disabled', true);
    }
});

$("#check_planta_aux_2").click(function () {
    var cantidad_filas = $('#sec_ope_planta_aux_2_filas').val();
    if (this.checked) {
        for (let i = 1; i <= (cantidad_filas); i++) {

            $('#sec_ope_ppal_planta_aux_2_' + i)
                .prop('disabled', false)
                .selectpicker('refresh');

            $('#sec_ope_atl_1_planta_aux_2_' + i)
                .prop('disabled', false)
                .selectpicker('refresh');

            $('#sec_ope_atl_2_planta_aux_2_' + i)
                .prop('disabled', false)
                .selectpicker('refresh');
        }

        $('#agregar_fila_planta_auxiliar_2').prop('disabled', false);
    } else {
        for (let i = 1; i <= (cantidad_filas); i++) {

            $('#sec_ope_ppal_planta_aux_2_' + i)
                .prop('disabled', true)
                .selectpicker('refresh');

            $('#sec_ope_atl_1_planta_aux_2_' + i)
                .prop('disabled', true)
                .selectpicker('refresh');

            $('#sec_ope_atl_2_planta_aux_2_' + i)
                .prop('disabled', true)
                .selectpicker('refresh');
        }

        $('#agregar_fila_planta_auxiliar_2').prop('disabled', true);
    }
});

///Funcionas para agregar filas de las secuencias operacionales
$("#agregar_fila_planta_original").click(function () {

    // Clonar el select picker
    var $original = $('#sec_ope_ppal_planta_ori_3');
    var $aux_1 = $('#sec_ope_atl_1_planta_ori_3');
    var $aux_2 = $('#sec_ope_atl_2_planta_ori_3');
    // Asegúrate de usar el ID correcto
    var $clon_org = $original.clone();
    var $aux_1 = $aux_1.clone();
    var $aux_2 = $aux_2.clone();

    // Actualizar atributos para evitar conflictos
    let num_fila = parseInt($('#sec_ope_planta_orig_filas').val());
    num_fila = num_fila + 1; // Ejemplo de ID único

    var fila = '';
    fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_clon_1_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_clon_2_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_clon_3_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '</div>';
    fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
    fila += '    <div class="col-12">';
    fila += '        &nbsp;';
    fila += '    </div>';
    fila += '</div>';

    $('#fila_planta_original').append(fila);

    $clon_org.attr('id', 'sec_ope_ppal_planta_ori_' + num_fila);
    $clon_org.attr('name', 'sec_ope_ppal_planta_ori_' + num_fila);
    $clon_org.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_clon_1_' + num_fila).append($clon_org);
    $clon_org.selectpicker();

    $aux_1.attr('id', 'sec_ope_atl_1_planta_ori_' + num_fila);
    $aux_1.attr('name', 'sec_ope_atl_1_planta_ori_' + num_fila);
    $aux_1.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_clon_2_' + num_fila).append($aux_1);
    $aux_1.selectpicker();

    $aux_2.attr('id', 'sec_ope_atl_2_planta_ori_' + num_fila);
    $aux_2.attr('name', 'sec_ope_atl_2_planta_ori_' + num_fila);
    $aux_2.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_clon_3_' + num_fila).append($aux_2);
    $aux_2.selectpicker();

    $('#sec_ope_planta_orig_filas').val(num_fila);

});

$("#agregar_fila_planta_auxiliar_1").click(function () {

    // Actualizar atributos para evitar conflictos
    let num_fila = parseInt($('#sec_ope_planta_aux_1_filas').val());
    num_fila = num_fila + 1; // Ejemplo de ID único

    var fila = '';
    fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_aux_1_1_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_aux_1_2_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_aux_1_3_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '</div>';
    fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
    fila += '    <div class="col-12">';
    fila += '        &nbsp;';
    fila += '    </div>';
    fila += '</div>';

    $('#fila_planta_aux_1').append(fila);

    // Clonar el select picker
    var $original = $('#sec_ope_ppal_planta_aux_1_3');
    var $aux_1 = $('#sec_ope_atl_1_planta_aux_1_3');
    var $aux_2 = $('#sec_ope_atl_2_planta_aux_1_3');
    // Asegúrate de usar el ID correcto
    var $clon_org = $original.clone();
    var $aux_1 = $aux_1.clone();
    var $aux_2 = $aux_2.clone();

    $clon_org.attr('id', 'sec_ope_ppal_planta_aux_1_' + num_fila);
    $clon_org.attr('name', 'sec_ope_ppal_planta_aux_1_' + num_fila);
    $clon_org.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_aux_1_1_' + num_fila).append($clon_org);
    $clon_org.selectpicker();

    $aux_1.attr('id', 'sec_ope_atl_1_planta_aux_1_' + num_fila);
    $aux_1.attr('name', 'sec_ope_atl_1_planta_aux_1_' + num_fila);
    $aux_1.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_aux_1_2_' + num_fila).append($aux_1);
    $aux_1.selectpicker();

    $aux_2.attr('id', 'sec_ope_atl_2_planta_aux_1_' + num_fila);
    $aux_2.attr('name', 'sec_ope_atl_2_planta_aux_1_' + num_fila);
    $aux_2.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_aux_1_3_' + num_fila).append($aux_2);
    $aux_2.selectpicker();

    $('#sec_ope_planta_aux_1_filas').val(num_fila);

});

$("#agregar_fila_planta_auxiliar_2").click(function () {

    // Clonar el select picker
    var $original = $('#sec_ope_ppal_planta_aux_2_3');
    var $aux_1 = $('#sec_ope_atl_1_planta_aux_2_3');
    var $aux_2 = $('#sec_ope_atl_2_planta_aux_2_3');
    // Asegúrate de usar el ID correcto
    var $clon_org = $original.clone();
    var $aux_1 = $aux_1.clone();
    var $aux_2 = $aux_2.clone();

    // Actualizar atributos para evitar conflictos
    let num_fila = parseInt($('#sec_ope_planta_aux_2_filas').val());
    num_fila = num_fila + 1; // Ejemplo de ID único

    var fila = '';
    fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_aux_2_1_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_aux_2_2_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '    <div class="col-4">';
    fila += '        <div class="form-group form-row">';
    fila += '            <div class="col">';
    fila += '                <div id="selector_aux_2_3_' + num_fila + '"></div>';
    fila += '            </div>';
    fila += '        </div>';
    fila += '    </div>';
    fila += '</div>';
    fila += '<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
    fila += '    <div class="col-12">';
    fila += '        &nbsp;';
    fila += '    </div>';
    fila += '</div>';

    $('#fila_planta_aux_2').append(fila);

    $clon_org.attr('id', 'sec_ope_ppal_planta_aux_2_' + num_fila);
    $clon_org.attr('name', 'sec_ope_ppal_planta_aux_2_' + num_fila);
    $clon_org.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_aux_2_1_' + num_fila).append($clon_org);
    $clon_org.selectpicker();

    $aux_1.attr('id', 'sec_ope_atl_1_planta_aux_2_' + num_fila);
    $aux_1.attr('name', 'sec_ope_atl_1_planta_aux_2_' + num_fila);
    $aux_1.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_aux_2_2_' + num_fila).append($aux_1);
    $aux_1.selectpicker();

    $aux_2.attr('id', 'sec_ope_atl_2_planta_aux_2_' + num_fila);
    $aux_2.attr('name', 'sec_ope_atl_2_planta_aux_2_' + num_fila);
    $aux_2.removeClass('selectpicker').selectpicker('destroy');
    $('#selector_aux_2_3_' + num_fila).append($aux_2);
    $aux_2.selectpicker();

    $('#sec_ope_planta_aux_2_filas').val(num_fila);

});


// BUSQUEDA DE MATRIZ POR CAD
function searchMatrizCad() {

    var cad = $('#cad_id').val();
    //obtener valor del texto del selector

    var cad_text = $('#cad_id option:selected').text();

    return $.ajax({
        type: "GET",
        url: "/searchMatrizCad",
        data: "cad=" + cad,
        success: function (data) {

            var html = '';

            if (data && data.length > 0) {

                $.each(data, function (key, dat) {

                    html += '<tr>'
                    html += '<td>' + dat.plano_cad + '</td>';
                    html += '<td>' + dat.material + '</td>';
                    html += '<td>' + dat.texto_breve_material + '</td>';
                    html += '<td>' + dat.largo_matriz + '</td>';
                    html += '<td>' + dat.ancho_matriz + '</td>';
                    html += '<td>' + dat.cantidad_largo_matriz + '</td>';
                    html += '<td>' + dat.cantidad_ancho_matriz + '</td>';
                    html += '<td>' + dat.separacion_largo_matriz + '</td>';
                    html += '<td>' + dat.separacion_ancho_matriz + '</td>';
                    html += '<td>' + dat.tipo_matriz + '</td>';
                    html += '<td>' + dat.total_golpes + '</td>';
                    if (dat.maquina === null) {
                        html += '<td>&nbsp;</td>'
                    } else {
                        html += '<td>' + dat.maquina + '</td>'
                    }

                    if (dat.active == 1) {
                        html += '<td>Activo</td>'
                    } else {
                        html += '<td>Inactivo</td>'
                    }
                    html += '</tr>';

                });
            } else {
                html += '<tr><td colspan="12">Sin resultados. CAD: ' + cad_text + ' NO posee matriz asociada</td></tr>';
            }

            $('#body_matriz_table_view').html(html);
        },
    });

}

////Evolutivo 24-11 version 2 lectura boceto pdf campos clisse cm2 - Inicio
$("#total_cm2_clisse").prop("disabled", true).prop("readonly", true);

$("#cm2_clisse_color_1").change(function () {

    var total_antiguo = parseFloat($("#total_cm2_clisse_value").val());
    var valor_antiguo = parseFloat($("#cm2_clisse_color_1_value").val());
    var valor_nuevo = 0;
    var total_nuevo = total_antiguo - valor_antiguo;
    var result = 0;

    if ($("#cm2_clisse_color_1").val()) {
        valor_nuevo = parseFloat($("#cm2_clisse_color_1").val());
    } else {
        valor_nuevo = 0;
    }

    result = total_nuevo + valor_nuevo;
    if (result == 0) {
        $("#total_cm2_clisse").val('');
    } else {
        $("#total_cm2_clisse").val(result);
    }
    $("#total_cm2_clisse_value").val(result);
    $("#cm2_clisse_color_1_value").val(valor_nuevo)
});

$("#cm2_clisse_color_2").change(function () {

    var total_antiguo = parseFloat($("#total_cm2_clisse_value").val());
    var valor_antiguo = parseFloat($("#cm2_clisse_color_2_value").val());
    var valor_nuevo = 0;
    var total_nuevo = total_antiguo - valor_antiguo;
    var result = 0;

    if ($("#cm2_clisse_color_2").val()) {
        valor_nuevo = parseFloat($("#cm2_clisse_color_2").val());
    } else {
        valor_nuevo = 0;
    }

    result = total_nuevo + valor_nuevo;
    if (result == 0) {
        $("#total_cm2_clisse").val('');
    } else {
        $("#total_cm2_clisse").val(result);
    }
    $("#total_cm2_clisse_value").val(result);
    $("#cm2_clisse_color_2_value").val(valor_nuevo)
});

$("#cm2_clisse_color_3").change(function () {

    var total_antiguo = parseFloat($("#total_cm2_clisse_value").val());
    var valor_antiguo = parseFloat($("#cm2_clisse_color_3_value").val());
    var valor_nuevo = 0;
    var total_nuevo = total_antiguo - valor_antiguo;
    var result = 0;

    if ($("#cm2_clisse_color_3").val()) {
        valor_nuevo = parseFloat($("#cm2_clisse_color_3").val());
    } else {
        valor_nuevo = 0;
    }

    result = total_nuevo + valor_nuevo;
    if (result == 0) {
        $("#total_cm2_clisse").val('');
    } else {
        $("#total_cm2_clisse").val(result);
    }
    $("#total_cm2_clisse_value").val(result);
    $("#cm2_clisse_color_3_value").val(valor_nuevo)
});

$("#cm2_clisse_color_4").change(function () {

    var total_antiguo = parseFloat($("#total_cm2_clisse_value").val());
    var valor_antiguo = parseFloat($("#cm2_clisse_color_4_value").val());
    var valor_nuevo = 0;
    var total_nuevo = total_antiguo - valor_antiguo;
    var result = 0;

    if ($("#cm2_clisse_color_4").val()) {
        valor_nuevo = parseFloat($("#cm2_clisse_color_4").val());
    } else {
        valor_nuevo = 0;
    }

    result = total_nuevo + valor_nuevo;
    if (result == 0) {
        $("#total_cm2_clisse").val('');
    } else {
        $("#total_cm2_clisse").val(result);
    }
    $("#total_cm2_clisse_value").val(result);
    $("#cm2_clisse_color_4_value").val(valor_nuevo)
});

$("#cm2_clisse_color_5").change(function () {

    var total_antiguo = parseFloat($("#total_cm2_clisse_value").val());
    var valor_antiguo = parseFloat($("#cm2_clisse_color_5_value").val());
    var valor_nuevo = 0;
    var total_nuevo = total_antiguo - valor_antiguo;
    var result = 0;

    if ($("#cm2_clisse_color_5").val()) {
        valor_nuevo = parseFloat($("#cm2_clisse_color_5").val());
    } else {
        valor_nuevo = 0;
    }

    result = total_nuevo + valor_nuevo;
    if (result == 0) {
        $("#total_cm2_clisse").val('');
    } else {
        $("#total_cm2_clisse").val(result);
    }
    $("#total_cm2_clisse_value").val(result);
    $("#cm2_clisse_color_5_value").val(valor_nuevo)
});

$("#cm2_clisse_color_6").change(function () {

    var total_antiguo = parseFloat($("#total_cm2_clisse_value").val());
    var valor_antiguo = parseFloat($("#cm2_clisse_color_6_value").val());
    var valor_nuevo = 0;
    var total_nuevo = total_antiguo - valor_antiguo;
    var result = 0;

    if ($("#cm2_clisse_color_6").val()) {
        valor_nuevo = parseFloat($("#cm2_clisse_color_6").val());
    } else {
        valor_nuevo = 0;
    }

    result = total_nuevo + valor_nuevo;
    if (result == 0) {
        $("#total_cm2_clisse").val('');
    } else {
        $("#total_cm2_clisse").val(result);
    }
    $("#total_cm2_clisse_value").val(result);
    $("#cm2_clisse_color_6_value").val(valor_nuevo)
});

$("#cm2_clisse_color_7").change(function () {

    var total_antiguo = parseFloat($("#total_cm2_clisse_value").val());
    var valor_antiguo = parseFloat($("#cm2_clisse_color_7_value").val());
    var valor_nuevo = 0;
    var total_nuevo = total_antiguo - valor_antiguo;
    var result = 0;

    if ($("#cm2_clisse_color_7").val()) {
        valor_nuevo = parseFloat($("#cm2_clisse_color_7").val());
    } else {
        valor_nuevo = 0;
    }

    result = total_nuevo + valor_nuevo;
    if (result == 0) {
        $("#total_cm2_clisse").val('');
    } else {
        $("#total_cm2_clisse").val(result);
    }
    $("#total_cm2_clisse_value").val(result);
    $("#cm2_clisse_color_7_value").val(valor_nuevo)
});
////Evolutivo 24-11 version 2 lectura boceto pdf campos clisse cm2 - Fin
