function configuracionInicialFormOT() {
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    // MASCARAS NUMERICAS
    const volumenMask = IMask(volumen_venta_anual, thousandsOptions);
    const usdMask = IMask(usd, thousandsOptions);
    var areaProductoMask = IMask(area_producto, fourDecimalsOptions);
    var recorteAdicionalMask = IMask(recorte_adicional, cuatroDecimalsOptions);

    // -------- JERARQUIAS ------------------

    // Al comienzo los select de jerarquia 2 y 3 comienzan desabilitados y luego son habilitados al cambiar la jerarquia 1

    $("#subhierarchy_id,#subsubhierarchy_id").prop("disabled", true);
    // ajax para relacionar jerarquia 2 con la jerarquia 1 seleccionada
    $("#hierarchy_id").change(function () {
        var val = $(this).val();
        return $.ajax({
            type: "GET",
            url: "/getJerarquia2",
            data: "hierarchy_id=" + val +"&jerarquia2=" +$("#jerarquia2").val(),
            success: function (data) {
                data = $.parseHTML(data);
                // if (role == 4) {
                $("#hierarchy_id").prop("disabled", false);
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
            data: "subhierarchy_id=" + val +"&jerarquia3=" +$("#jerarquia3").val(),
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

    // -------- FIN JERARQUIAS ------------------
    //
    //
    //

    // DESABILITAR CAMPOS SEGUN ROL
    let role = $("#role_id").val();
    // Area de Venta
    if (role == 3 || role == 4 || role == 5 || role == 6) {
        $(
            "#indicador_facturacion,#largura_hm,#anchura_hm,#area_producto,#recorte_adicional,#bct_min_lb,#bct_min_kg,#golpes_largo,#golpes_ancho,#separacion_golpes_largo,#separacion_golpes_ancho,#cuchillas,#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2,#impresion_1,#impresion_2,#impresion_3,#impresion_4,#impresion_5,#impresion_6,#longitud_pegado,#porcentaje_cera_exterior,#porcentaje_cera_interior,#porcentaje_barniz_interior,#tipo_sentido_onda,#material_asignado,#descripcion_material"
        ).prop("disabled", true);
    }

        // TIPO DE DISEÑO --- Cargar complejidad segun tipo de diseño ( campos habilitados para Jefe de Diseño o el Diseñador )
        const functionDesignType = () => {

            if (role == 4 || role == 6 || role == 7 || role == 8){

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

                } else if($("#design_type_id").val() == ""){

                    $("#complejidad").val("").prop("disabled", true)
                    $(document).find("#indicador_facturacion_diseno_grafico").val("").prop("disabled", true)

                }else {
                    $("#complejidad").prop("disabled", true)
                    $("#indicador_facturacion_diseno_grafico").prop("disabled", true)
                }

            }else{

                $("#design_type_id").prop("disabled", true)
                $("#complejidad").prop("disabled", true)
                $("#indicador_facturacion_diseno_grafico").prop("disabled", true)

            }
        }

        // // Funcion cuando cambia un TIPO DE DISEÑO ---
        const functionDesignTypeChange = () => {
            if (role == 4 || role == 6 || role == 7 || role == 8){

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

                        } else if($("#design_type_id").val() == ""){

                            $("#complejidad").val("").prop("disabled", true)
                            $(document).find("#indicador_facturacion_diseno_grafico").val("").prop("disabled", true)


                        }else {

                            $("#complejidad").prop("disabled", true)
                            $("#indicador_facturacion_diseno_grafico").prop("disabled", true)
                        }
                    })
                    .triggerHandler("change");
            }else{

                $("#design_type_id").prop("disabled", true)
                $("#complejidad").prop("disabled", true)
                $("#indicador_facturacion_diseno_grafico").prop("disabled", true)

            }
        }

        // //IMPRESIÓN -- Validacion de impresion para el numero de colores
        const functionImpresion = () => {

            $("#impresion")
                .change(() => {

                    if($('#impresion').val() === '1'){ // 1 => "Offset"

                        // if($("#planta_id").val()  == '' || $("#planta_id").val()  != 3){// Si la planta esta vacia se queda vacia y si era Osorno se queda Osorno

                        //     $("#planta_id")
                        //         .prop("disabled", false)
                        //         .val("")
                        //         .selectpicker("refresh")
                        //         .closest("div.form-group")
                        //         .removeClass("error");
                        // }
                        if(role === '4'){//para el vendedor

                            // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                            // $("#impresion,#coverage_external_id,#coverage_internal_id")
                            //     .prop("disabled", false)
                            //     .val("")
                            //     .selectpicker("refresh")
                            //     .closest("div.form-group")
                            //     .removeClass("error");


                            $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico")
                                .prop("disabled", true)
                                .val("")
                                .selectpicker("refresh")
                                .closest("div.form-group")
                                .removeClass("error");
                        }else{

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

                    }else if($('#impresion').val() === '2'){ //2 => "Flexografía"

                        // if($("#planta_id").val()  == '' || $("#planta_id").val()  != 3){// Si la planta esta vacia se queda vacia y si era Osorno se queda Osorno

                        //     $("#planta_id")
                        //         .prop("disabled", false)
                        //         .val("")
                        //         .selectpicker("refresh")
                        //         .closest("div.form-group")
                        //         .removeClass("error");
                        // }
                        if(role === '4'){//para el vendedor

                            // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                            // $("#impresion,#coverage_external_id,#coverage_internal_id")
                            //     .prop("disabled", false)
                            //     .val("")
                            //     .selectpicker("refresh")
                            //     .closest("div.form-group")
                            //     .removeClass("error");


                            $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico")
                                .prop("disabled", true)
                                .val("")
                                .selectpicker("refresh")
                                .closest("div.form-group")
                                .removeClass("error");
                        }else{

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

                    }else if($('#impresion').val() === '3'){ //3 => "Flexografía Alta Gráfica"

                        // if($("#planta_id").val()  == '' || $("#planta_id").val()  != 1){// Si la planta esta vacia se selecciona automaticamente la planta Buin

                        //     $("#planta_id")
                        //         .prop("disabled", true)
                        //         .val("1")
                        //         .selectpicker("refresh")
                        //         .closest("div.form-group")
                        //         .removeClass("error");
                        // }
                        if(role === '4'){//para el vendedor

                            // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                            // $("#impresion,#coverage_external_id,#coverage_internal_id")
                            //     .prop("disabled", false)
                            //     .val("")
                            //     .selectpicker("refresh")
                            //     .closest("div.form-group")
                            //     .removeClass("error");


                            $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico")
                                .prop("disabled", true)
                                .val("")
                                .selectpicker("refresh")
                                .closest("div.form-group")
                                .removeClass("error");
                        }else{

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


                    }else if($('#impresion').val() === '4'){ //4 => "Flexografía Tiro y Retiro"

                        // if($("#planta_id").val()  == '' || $("#planta_id").val()  != 2){// Si la planta esta vacia se selecciona automaticamente la planta Tiltil

                        //     $("#planta_id")
                        //         .prop("disabled", true)
                        //         .val("2")
                        //         .selectpicker("refresh")
                        //         .closest("div.form-group")
                        //         .removeClass("error");
                        // }
                        if(role === '4'){//para el vendedor

                            // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                            // $("#impresion,#coverage_external_id,#coverage_internal_id")
                            //     .prop("disabled", false)
                            //     .val("")
                            //     .selectpicker("refresh")
                            //     .closest("div.form-group")
                            //     .removeClass("error");


                            $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico")
                                .prop("disabled", true)
                                .val("")
                                .selectpicker("refresh")
                                .closest("div.form-group")
                                .removeClass("error");
                        }else{

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
                            <option value="5"> 5 </option>`
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

                    }else if($('#impresion').val() === '5'){ //5 => "Sin Impresión"

                        // if($("#planta_id").val()  == '' || $("#planta_id").val()  != 3 ){// Si la planta esta vacia se queda vacia y si era Osorno se queda Osorno

                        //     $("#planta_id")
                        //         .prop("disabled", false)
                        //         .val("")
                        //         .selectpicker("refresh")
                        //         .closest("div.form-group")
                        //         .removeClass("error");
                        // }
                        if(role === '4'){//para el vendedor

                            // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                            // $("#impresion,#coverage_external_id,#coverage_internal_id")
                            //     .prop("disabled", false)
                            //     .val("")
                            //     .selectpicker("refresh")
                            //     .closest("div.form-group")
                            //     .removeClass("error");


                            $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico")
                                .prop("disabled", true)
                                .val("")
                                .selectpicker("refresh")
                                .closest("div.form-group")
                                .removeClass("error");
                        }else{

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

                    }else if($('#impresion').val() === '6' || $('#impresion').val() === '7'){ //6 => "Sin Impresión (Sólo OF)", 7 => "Sin Impresión (Trazabilidad Completa)"

                        // if($("#planta_id").val()  == '' || $("#planta_id").val()  != 3 ){// Si la planta esta vacia se queda vacia y si era Osorno se queda Osorno

                        //     $("#planta_id")
                        //         .prop("disabled", false)
                        //         .val("")
                        //         .selectpicker("refresh")
                        //         .closest("div.form-group")
                        //         .removeClass("error");
                        // }
                        if(role === '4'){//para el vendedor

                            // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                            // $("#impresion,#coverage_external_id,#coverage_internal_id")
                            //     .prop("disabled", false)
                            //     .val("")
                            //     .selectpicker("refresh")
                            //     .closest("div.form-group")
                            //     .removeClass("error");


                            $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico")
                                .prop("disabled", true)
                                .val("")
                                .selectpicker("refresh")
                                .closest("div.form-group")
                                .removeClass("error");
                        }else{

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

                    }else{

                        // if($("#planta_id").val()  == '' || $("#planta_id").val()  != 3 ){// Si la planta esta vacia se queda vacia y si era Osorno se queda Osorno

                        //     $("#planta_id")
                        //         .prop("disabled", false)
                        //         .val("")
                        //         .selectpicker("refresh")
                        //         .closest("div.form-group")
                        //         .removeClass("error");
                        // }
                        if(role === '4'){//para el vendedor

                            // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                            // $("#impresion,#coverage_external_id,#coverage_internal_id")
                            //     .prop("disabled", false)
                            //     .val("")
                            //     .selectpicker("refresh")
                            //     .closest("div.form-group")
                            //     .removeClass("error");


                            $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico")
                                .prop("disabled", true)
                                .val("")
                                .selectpicker("refresh")
                                .closest("div.form-group")
                                .removeClass("error");
                        }else{

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
                    }

                })
                // .triggerHandler("change");
        }


    //Se desabilitan las opcion NO y SI , ya que son reemplazadas solo por los valores activos en la tabla reference_types
    $('#reference_type option[value="0"]').attr("disabled", true);
    $('#reference_type option[value="1"]').attr("disabled", true);

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
    // $('#recubrimiento option[value="1"]').attr("disabled", true);

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


    // Si numero de colores es 0  bloqueamos todos los colores de lo contrario se habilitan acorde al numero seleccionado
    $("#numero_colores")
        .change(() => {
            const numeroColores = $("#numero_colores").val();
            const desabilitarColores = (colores) => {
                $(colores)
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            };
            if (numeroColores === "0" || numeroColores === "") {
                desabilitarColores(
                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5"
                );
            } else {
                switch (numeroColores) {
                    case "1":
                        desabilitarColores(
                            "#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5"
                        );
                        $("#color_1_id,#impresion_1")
                            .prop("disabled", false)
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                        break;
                    case "2":
                        desabilitarColores(
                            "#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5"
                        );
                        $("#color_1_id,#impresion_1,#color_2_id,#impresion_2")
                            .prop("disabled", false)
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                        break;
                    case "3":
                        desabilitarColores(
                            "#color_4_id,#impresion_4,#color_5_id,#impresion_5"
                        );
                        $(
                            "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3"
                        )
                            .prop("disabled", false)
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                        break;
                    case "4":
                        desabilitarColores("#color_5_id,#impresion_5");
                        $(
                            "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4"
                        )
                            .prop("disabled", false)
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                        break;
                    case "5":
                        // desabilitarColores();
                        $(
                            "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5"
                        )
                            .prop("disabled", false)
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                        break;

                    default:
                        break;
                }
            }
        })
        .triggerHandler("change");

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
        let producto_id = ['3','4','5','6','8','10','11','12','13','14','16','18','19','20','28','31','32','33','34']
        let producto = $(this).val();

        if(producto_id.includes(producto)){
            $("#maquila")
                .prop("disabled", false)
                .val('')
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        }else{
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

           if(carton_color != '' && carton_color == 1){//Si esta uno seleccionado y es color Café no se muestra Alta Gráfica en impresión

               if($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() == 4){//Se valida porque el color Café si muestra el tiro y retiro pero el recubrimiento externo no

                   $("#impresion").html(
                       `<option value="">Seleccionar...</option>
                       <option value="2">Flexografía</option>
                       <option value="5">Sin Impresión</option>
                       <option value="6">Sin Impresión (Sólo OF)</option>
                       <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                   )
                   .selectpicker("refresh");

               }else{

                   $("#impresion").html(
                       `<option value="">Seleccionar...</option>
                       <option value="2">Flexografía</option>
                       <option value="4">Flexografía Tiro y Retiro</option>
                       <option value="5">Sin Impresión</option>
                       <option value="6">Sin Impresión (Sólo OF)</option>
                       <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                   )
                   .selectpicker("refresh");
               }

           }else{

               if($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() == 4){//Se valida porque el color Blanco si muestra el tiro y alta grafica

                   $("#impresion").html(
                       `<option value="">Seleccionar...</option>
                       <option value="2">Flexografía</option>
                       <option value="3">Flexografía Alta Gráfica</option>
                       <option value="5">Sin Impresión</option>
                       <option value="6">Sin Impresión (Sólo OF)</option>
                       <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                   )
                   .selectpicker("refresh");

               }else{

                   $("#impresion").html(
                       `<option value="">Seleccionar...</option>
                       <option value="2">Flexografía</option>
                       <option value="3">Flexografía Alta Gráfica</option>
                       <option value="4">Flexografía Tiro y Retiro</option>
                       <option value="5">Sin Impresión</option>
                       <option value="6">Sin Impresión (Sólo OF)</option>
                       <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                   )
                   .selectpicker("refresh");
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
                           chargeSelectSecOperacional(data.planta_id);


                           //cuando sea seleccionado el color blanco y algunos de estos tres cartones es que puede mostrar impresion  Alta Gráfica
                           if($("#carton_color").val() == 2 && (data.codigo == 'EN50G' || data.codigo == 'EN80G' || data.codigo == 'EN50EL')){

                               if($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() == 4){//Barniz UV --- No muestra tiro y retiro

                                   $("#impresion").html(
                                       `<option value="">Seleccionar...</option>
                                                       <option value="2">Flexografía</option>
                                       <option value="3">Flexografía Alta Gráfica</option>
                                       <option value="5">Sin Impresión</option>
                                       <option value="6">Sin Impresión (Sólo OF)</option>
                                       <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                                   )
                                   .selectpicker("refresh");

                               }else{

                                   $("#impresion").html(
                                       `<option value="">Seleccionar...</option>
                                                       <option value="2">Flexografía</option>
                                       <option value="3">Flexografía Alta Gráfica</option>
                                       <option value="4">Flexografía Tiro y Retiro</option>
                                       <option value="5">Sin Impresión</option>
                                       <option value="6">Sin Impresión (Sólo OF)</option>
                                       <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                                   )
                                   .selectpicker("refresh");
                               }

                           }else{

                               if($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() == 4){//Barniz UV --- No muestra tiro y retiro

                                   $("#impresion").html(
                                       `<option value="">Seleccionar...</option>
                                                       <option value="2">Flexografía</option>
                                       <option value="5">Sin Impresión</option>
                                       <option value="6">Sin Impresión (Sólo OF)</option>
                                       <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                                   )
                                   .selectpicker("refresh");

                               }else{

                                   $("#impresion").html(
                                       `<option value="">Seleccionar...</option>
                                                       <option value="2">Flexografía</option>
                                       <option value="4">Flexografía Tiro y Retiro</option>
                                       <option value="5">Sin Impresión</option>
                                       <option value="6">Sin Impresión (Sólo OF)</option>
                                       <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                                   )
                                   .selectpicker("refresh");
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

    // --------- PLANTA OBJETIVO -----------
    $("#planta_id")
    .change(() => {

        if ($("#planta_id").val() == '1') {// Buin

            if(role === '4'){//para el vendedor

                // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                $("#coverage_external_id,#coverage_internal_id")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");


                $("#impresion,#percentage_coverage_internal,#percentage_coverage_external,#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }else{

                //se limpian solo estos campos para los otros roles
                $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }

            functionImpresion();

            $("#impresion").html(
                `<option value="">Seleccionar...</option>
                 value="2">Flexografía</option>
                <option value="3">Flexografía Alta Gráfica</option>
                <option value="5">Sin Impresión</option>
                <option value="6">Sin Impresión (Sólo OF)</option>
                <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
            )
            .selectpicker("refresh");

        }else if ($("#planta_id").val() == '2'){// Til til

            functionImpresion();

            if(role === '4'){//para el vendedor

                // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                $("#coverage_external_id,#coverage_internal_id")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");


                $("#impresion,#percentage_coverage_internal,#percentage_coverage_external,#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }else{

                //se limpian solo estos campos para los otros roles
                $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }

            if($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() == 4){

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    <option value="2">Flexografía</option>
                    <option value="5">Sin Impresión</option>
                    <option value="6">Sin Impresión (Sólo OF)</option>
                    <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                )
                .selectpicker("refresh");

            }else{

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    <option value="2">Flexografía</option>
                    <option value="4">Flexografía Tiro y Retiro</option>
                    <option value="5">Sin Impresión</option>
                    <option value="6">Sin Impresión (Sólo OF)</option>
                    <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                )
                .selectpicker("refresh");
            }

        }else if ($("#planta_id").val() == '3'){// Osorno

            functionImpresion();

            if(role === '4'){//para el vendedor

                // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                $("#coverage_external_id,#coverage_internal_id")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");


                $("#impresion,#percentage_coverage_internal,#percentage_coverage_external,#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }else{

                //se limpian solo estos campos para los otros roles
                $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }

            $("#impresion").html(
                `<option value="">Seleccionar...</option>
                <option value="2">Flexografía</option>
                <option value="5">Sin Impresión</option>
                <option value="6">Sin Impresión (Sólo OF)</option>
                <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
            )
            .selectpicker("refresh");

        }else{

            if($("#coverage_external_id").val() != '' && $("#coverage_external_id").val() == 4){

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    tion value="2">Flexografía</option>
                    <option value="3">Flexografía Alta Gráfica</option>
                    <option value="5">Sin Impresión</option>
                    <option value="6">Sin Impresión (Sólo OF)</option>
                    <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                )
                .selectpicker("refresh");

                $("#design_type_id,#complejidad,#indicador_facturacion_diseno_grafico")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

            }else{

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    tion value="2">Flexografía</option>
                    <option value="3">Flexografía Alta Gráfica</option>
                    <option value="4">Flexografía Tiro y Retiro</option>
                    <option value="5">Sin Impresión</option>
                    <option value="6">Sin Impresión (Sólo OF)</option>
                    <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                )
                .selectpicker("refresh");

                if(role === '4'){//para el vendedor

                    // cuando cambia la planta, se limpian todos los campos de COLOR-CERA-BARNIZ
                    $("#coverage_external_id,#coverage_internal_id")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");


                    $("#impresion,#percentage_coverage_internal,#percentage_coverage_external,#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                }else{

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

        // RECUBRIMIENTO INTERNO ---- Validacion para liberar los campos
        $("#coverage_internal_id")
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

            functionImpresion();

            if($("#coverage_internal_id").val() == 1){
                $("#percentage_coverage_internal")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }else{
                $("#percentage_coverage_internal")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }

        })



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

        if($("#coverage_external_id").val() == 1){ //No aplica -- se muestra todo de impresión pero el porcentage se deshabilita

            if ($("#planta_id").val() == '1') {//Buin

                if($("#carton_color").val() == 1){//Café

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                         <option value="2">Flexografía</option>
                        <option value="5">Sin Impresión</option>
                        <option value="6">Sin Impresión (Sólo OF)</option>
                        <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                    )
                    .selectpicker("refresh");

                }else if($("#carton_color").val() == 2){//Blanco

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                         <option value="2">Flexografía</option>
                        <option value="5">Sin Impresión</option>
                        <option value="6">Sin Impresión (Sólo OF)</option>
                        <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                    )
                    .selectpicker("refresh");

                    // <option value="3">Flexografía Alta Gráfica</option>
                }else{

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                         <option value="2">Flexografía</option>
                        <option value="5">Sin Impresión</option>
                        <option value="6">Sin Impresión (Sólo OF)</option>
                        <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                    )
                    .selectpicker("refresh");

                    // <option value="3">Flexografía Alta Gráfica</option>
                }

            }else if ($("#planta_id").val() == '2'){// Til til

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    <option value="2">Flexografía</option>
                    <option value="4">Flexografía Tiro y Retiro</option>
                    <option value="5">Sin Impresión</option>
                    <option value="6">Sin Impresión (Sólo OF)</option>
                    <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                )
                .selectpicker("refresh");

            }else if ($("#planta_id").val() == '3'){// Osorno

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    <option value="2">Flexografía</option>
                    <option value="5">Sin Impresión</option>
                    <option value="6">Sin Impresión (Sólo OF)</option>
                    <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                )
                .selectpicker("refresh");

            }else{

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    tion value="2">Flexografía</option>
                    <option value="4">Flexografía Tiro y Retiro</option>
                    <option value="5">Sin Impresión</option>
                    <option value="6">Sin Impresión (Sólo OF)</option>
                    <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                )
                .selectpicker("refresh");
                // <option value="3">Flexografía Alta Gráfica</option>

            }

            $("#barniz_uv")
                .prop("disabled", false)
                .val("0")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");

            $("#barniz_uv").trigger("change");

            $("#percentage_coverage_external")
                .prop("disabled", true)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");

        }else if ($("#coverage_external_id").val() == 4){//Barniz UV -- no puede tener impresión de tiro y retiro

            if ($("#planta_id").val() == '1') {//Buin

                if($("#carton_color").val() == 1){//Café

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                         <option value="2">Flexografía</option>
                        <option value="5">Sin Impresión</option>
                        <option value="6">Sin Impresión (Sólo OF)</option>
                        <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                    )
                    .selectpicker("refresh");

                //Para poder seleccionar el barniz UV se debe validar primero las planta, el color carton y el codigo del carton
                }else if($("#carton_color").val() == 2 && ($("#carton_id").find('option:selected').text().trim() == 'EN50G' || $("#carton_id").find('option:selected').text().trim() == 'EN80G' || $("#carton_id").find('option:selected').text().trim() == 'EN50EL')){// Si el color es blanco

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                         <option value="2">Flexografía</option>
                        <option value="3">Flexografía Alta Gráfica</option>
                        <option value="5">Sin Impresión</option>
                        <option value="6">Sin Impresión (Sólo OF)</option>
                        <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                    )
                    .selectpicker("refresh");

                    $("#impresion")
                        .val("3")
                        .selectpicker("refresh");

                    $("#impresion").trigger("change");

                    $("#barniz_uv")
                        .prop("disabled", false)
                        .val("1")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    $("#barniz_uv").trigger("change");

                }else{

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                         <option value="2">Flexografía</option>
                        <option value="5">Sin Impresión</option>
                        <option value="6">Sin Impresión (Sólo OF)</option>
                        <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                    )
                    .selectpicker("refresh");
                    // <option value="3">Flexografía Alta Gráfica</option>
                }

            }else if ($("#planta_id").val() == '2'){// Til til

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    <option value="2">Flexografía</option>
                    <option value="4">Flexografía Tiro y Retiro</option>
                    <option value="5">Sin Impresión</option>
                    <option value="6">Sin Impresión (Sólo OF)</option>
                    <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                )
                .selectpicker("refresh");

                //Barniz UV automaticamente es NO
                $("#barniz_uv")
                    .prop("disabled", false)
                    .val("0")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                $("#barniz_uv").trigger("change");

            }else if ($("#planta_id").val() == '3'){// Osorno

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    <option value="2">Flexografía</option>
                    <option value="5">Sin Impresión</option>
                    <option value="6">Sin Impresión (Sólo OF)</option>
                    <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                )
                .selectpicker("refresh");

                //Barniz UV automaticamente es NO
                $("#barniz_uv")
                    .prop("disabled", false)
                    .val("0")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                $("#barniz_uv").trigger("change");

            }else{

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    tion value="2">Flexografía</option>
                    <option value="4">Flexografía Tiro y Retiro</option>
                    <option value="5">Sin Impresión</option>
                    <option value="6">Sin Impresión (Sólo OF)</option>
                    <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                )
                .selectpicker("refresh");
                // <option value="3">Flexografía Alta Gráfica</option>

                //Para poder seleccionar el barniz UV se debe validar primero las planta, el color carton y el codigo del carton
                if($("#carton_color").val() == 2 && ($("#carton_id").find('option:selected').text().trim() == 'EN50G' || $("#carton_id").find('option:selected').text().trim() == 'EN80G' || $("#carton_id").find('option:selected').text().trim() == 'EN50EL')){// Si el color es blanco

                    $("#impresion").html(
                        `<option value="">Seleccionar...</option>
                         <option value="2">Flexografía</option>
                        <option value="3">Flexografía Alta Gráfica</option>
                        <option value="4">Flexografía Tiro y Retiro</option>
                        <option value="5">Sin Impresión</option>
                        <option value="6">Sin Impresión (Sólo OF)</option>
                        <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                    )
                    .selectpicker("refresh");

                    $("#impresion")
                        .val("3")
                        .selectpicker("refresh");

                    $("#impresion").trigger("change");

                    $("#barniz_uv")
                        .prop("disabled", false)
                        .val("1")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    $("#barniz_uv").trigger("change");

                }else{

                    $("#barniz_uv")
                        .prop("disabled", false)
                        .val("0")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    $("#barniz_uv").trigger("change");
                }

            }

            $("#percentage_coverage_external")
                .prop("disabled", false)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");


        }else if( $("#coverage_external_id").val() == ''){// cuando esta vacio, se limpian los datos

            $("#barniz_uv")
                .prop("disabled", false)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");

            $("#barniz_uv").trigger("change");

            $("#percentage_coverage_external")
                .prop("disabled", false)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");

        }else{//se muestra todo de impresión y se deshabilita el porcentage

            if ($("#planta_id").val() == '1') {// Buin

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    tion value="2">Flexografía</option>
                    <option value="5">Sin Impresión</option>
                    <option value="6">Sin Impresión (Sólo OF)</option>
                    <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                )
                .selectpicker("refresh");
                // <option value="3">Flexografía Alta Gráfica</option>


            }else if ($("#planta_id").val() == '2'){// Til til

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    <option value="2">Flexografía</option>
                    <option value="4">Flexografía Tiro y Retiro</option>
                    <option value="5">Sin Impresión</option>
                    <option value="6">Sin Impresión (Sólo OF)</option>
                    <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                )
                .selectpicker("refresh");

            }else if ($("#planta_id").val() == '3'){// Osorno

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    <option value="2">Flexografía</option>
                    <option value="5">Sin Impresión</option>
                    <option value="6">Sin Impresión (Sólo OF)</option>
                    <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                )
                .selectpicker("refresh");

            }else{

                $("#impresion").html(
                    `<option value="">Seleccionar...</option>
                    tion value="2">Flexografía</option>
                    <option value="4">Flexografía Tiro y Retiro</option>
                    <option value="5">Sin Impresión</option>
                    <option value="6">Sin Impresión (Sólo OF)</option>
                    <option value="7">Sin Impresión (Trazabilidad Completa)</option>`
                )
                .selectpicker("refresh");
                // <option value="3">Flexografía Alta Gráfica</option>
            }

            $("#barniz_uv")
                .prop("disabled", false)
                .val("0")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");

            $("#barniz_uv").trigger("change");

            $("#percentage_coverage_external")
                .prop("disabled", false)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");

        }

    })

        //Validacion para el vendedor - color 6 o barniz UV es obligatorio, cuando uno de estos dos tiene datos el otro ya no es obligatorio
        $("#color_6_id")
        .change(() => {

            if( $("#color_6_id").val() !== ''){

                $("#barniz_uv,#porcentanje_barniz_uv")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

            }else if ($("#color_6_id").val() === ''){

                $("#impresion_6")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }

        })

        //Validacion de BARNIZ UV --- cuando es no se bloquea el % IMPRESIÓN B. UV
        $("#barniz_uv")
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

    //Se desabilitan las opcion NO y SI , ya que son reemplazadas solo por los valores activos en la tabla fsc
    $('#fsc option[value="0"]').attr("disabled", true);
    $('#fsc option[value="1"]').attr("disabled", true);

    // Pais de referencia (pais_id) inabilitado cuando la opcion es NO y Sin FSC
    $("#fsc")
    .change(() => {

        let planta_id = $("#planta_id").val();

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

            if(planta_id != ''){

                $("#planta_id")
                    .val(planta_id)
                    .selectpicker("refresh");

            }


        }else if($("#fsc").val() != 2 && $("#fsc").val() != ''){

            //Planta BUIN no tiene FSC
            $("#planta_id").html(
                `<option value="">Seleccionar...</option>
                <option value="2">TIL TIL</option>
                <option value="3">OSORNO</option>`
            )
            .selectpicker("refresh");

            if(planta_id != '' && planta_id != 1){

                $("#planta_id")
                    .val(planta_id)
                    .selectpicker("refresh");

            }

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

            if(planta_id != ''){

                $("#planta_id")
                    .val(planta_id)
                    .selectpicker("refresh");

            }

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
            $("#tamano_pallet_type_id,#altura_pallet,#permite_sobresalir_carga")
                .prop("disabled", true)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        } else {
            $("#tamano_pallet_type_id,#altura_pallet,#permite_sobresalir_carga")
                .prop("disabled", false)
                .selectpicker("refresh");
        }
    })
    .triggerHandler("change");
    //Validacion Restriccion de Paletizado solo para rol de vendedor
    if(role!=4 && role!=3){
        $("#restriccion_pallet")
            .prop("disabled", true)
            .val("")
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");
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
                $("#pegado_terminacion")
                    .html(
                        '<option value="">Seleccionar...</option><option value="2">Pegado Interno</option><option value="3">Pegado Externo</option>'
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
            //}
        })
        .triggerHandler("change");
    // LOGICA DEL FORMULARIO SEGUN TIPO DE SOLICITUD
    //
    //
    //
    //

    // Habilitacion de campos segun tipo de solicitud
    $("#tipo_solicitud").change(function () {
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
                "#hierarchy_id,#reference_id,#bloqueo_referencia,#cinta,#items_set,#veces_item,#carton_id,#style_id,#recubrimiento_id,#numero_colores,#color_1_id,#color_2_id,#color_3_id,#color_4_id,#color_5_id,#color_6_id,#pegado,#cera_exterior,#cera_interior,#barniz_interior,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#process_id,#pegado_terminacion,#armado_id,#tipo_sentido_onda,#peso_contenido_caja,#autosoportante,#envase_id,#cajas_altura,#impresion,#pallet_sobre_pallet,#cantidad"
            )
                .prop("disabled", false)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");

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
        }
        // Cotiza CAD
        else if (tipo_solicitud == 2) {
            // Se desbloquean todos los checkbox
            cleanCheckboxs();

            enableCadSelect();
            // Desbloqueo y limpieza de valores para los siguientes inputs
            $(
                "#hierarchy_id,#cad,#carton_id,#numero_colores,#color_1_id,#color_2_id,#color_3_id,#color_4_id,#color_5_id,#color_6_id,#recubrimiento,#pegado,#cera_exterior,#cera_interior,#barniz_interior,#process_id,#pegado_terminacion,#armado_id,#peso_contenido_caja,#autosoportante,#envase_id,#cajas_altura,#impresion,#pallet_sobre_pallet,#cantidad"
            )
                .prop("disabled", false)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");

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
            $(".custom-control-input:not(#muestra)")
                .prop("disabled", true)
                .prop("checked", false);
            // activamos la opcion de muestra dinamicamente
            $("#muestra").prop("checked", true).triggerHandler("click");

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
            $("#hierarchy_id,#cad,#carton_id")
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
        } // Cotiza sin CAD
        else if (tipo_solicitud == 4) {
            disableCadSelect();
            // Se desbloquean todos los checkbox
            cleanCheckboxs();

            // Desbloqueo y limpieza de valores para los siguientes inputs
            enableAndCleanElements(
                "#hierarchy_id,#product_type_id,#recubrimiento,#numero_colores,#color_1_id,#color_2_id,#color_3_id,#color_4_id,#color_5_id,#color_6_id,#pegado,#cera_exterior,#cera_interior,#barniz_interior,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#process_id,#pegado_terminacion,#armado_id,#tipo_sentido_onda,#peso_contenido_caja,#autosoportante,#envase_id,#cajas_altura,#impresion,#pallet_sobre_pallet,#cantidad"
            );

            // Bloqueo y limpieza de valores para los siguientes inputs
            disableAndCleanElements(
                "#cad,#reference_type,#reference_id,#bloqueo_referencia,#cinta,#items_set,#veces_item,#style_id,#bct_min_lb,#bct_min_kg"
            );

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

            // Desbloqueo y limpieza de valores para los siguientes inputs
            enableAndCleanElements(
                "#hierarchy_id,#product_type_id,#reference_type,#cinta,#recubrimiento,#numero_colores,#color_1_id,#color_2_id,#color_3_id,#color_4_id,#color_5_id,#color_6_id,#pegado,#cera_exterior,#cera_interior,#barniz_interior,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#process_id,#pegado_terminacion,#peso_contenido_caja,#autosoportante,#envase_id,#cajas_altura,#impresion,#pallet_sobre_pallet,#cantidad"
            );

            // Bloqueo y limpieza de valores para los siguientes inputs
            disableAndCleanElements(
                "#style_id"
                // ,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#process_id,#pegado_terminacion,#armado_id,#impresion
            );

            // Marcar tipo de referencia = sin referencia
            $("#reference_type")
                .prop("disabled", false)
                .val("2")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");

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
                            .triggerHandler("change");
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
    });
    // .triggerHandler("change");

    //Cuando se crea la OT los campos de separación Golpes al Ancho y Largo se establecen en 0
    $("#separacion_golpes_largo,#separacion_golpes_ancho,#cuchillas")
        .prop("disabled", false)
        .val("0")
        .selectpicker("refresh")
        .closest("div.form-group")
        .removeClass("error");


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

    const disableAndCleanElements = (elements) => {
        toggleAndCleanElements(elements, true);
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
                    setValue(element, data);
                });

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
            $(`#rayado_c1r1`).prop({
                disabled: false,
                readonly: false,
            });
            $(`#rayado_r1_r2`).prop({
                disabled: false,
                readonly: false,
            });
            $(`#rayado_r2_c2`).prop({
                disabled: false,
                readonly: false,
            });
            $("#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2").off("change");
        } else {
            $(`#rayado_c1r1`)
                .prop({ disabled: false, readonly: true })
                .val(rayado_c1r1);
            $(`#rayado_r1_r2`)
                .prop({ disabled: false, readonly: true })
                .val(rayado_r1_r2);
            $(`#rayado_r2_c2`)
                .prop({ disabled: false, readonly: true })
                .val(rayado_r2_c2);
        }
        if (role == 3 || role == 4) {
            $(`#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2`).prop({
                disabled: false,
                readonly: true,
            });
        }
    };
}

// Popular jerarquias en orden
const populateHierarchies = async (detalle) => {
    await $("#hierarchy_id")
        .val(detalle.subsubhierarchy.subhierarchy.hierarchy_id)
        .triggerHandler("change");
    await $("#subhierarchy_id")
        .val(detalle.subsubhierarchy.subhierarchy_id)
        .triggerHandler("change");
    $("#subsubhierarchy_id")
        .val(detalle.subsubhierarchy_id)
        .selectpicker("refresh");
};

function setCheckboxs() {
    var ot = window.ot;
    if (ot.analisis) {
        $("#analisis").prop("checked", true);
    }
    if (ot.plano) {
        $("#plano").prop("checked", true);
    }
    if (ot.prueba_industrial) {
        $("#prueba_industrial").prop("checked", true);
    }
    if (ot.datos_cotizar) {
        $("#datos_cotizar").prop("checked", true);
    }
    if (ot.boceto) {
        $("#boceto").prop("checked", true);
    }
    if (ot.nuevo_material) {
        $("#nuevo_material").prop("checked", true);
    }
    if (ot.muestra) {
        $("#muestra").prop("checked", true).triggerHandler("click");

        $("#numero_muestras").val(ot.numero_muestras);
    }
}

function cargarDatos() {
    // En base a los datos de la OT a duplicar y el tipo de solicitud que sea llenaremos los datos pertinentes del formulario
    var detalle = window.detalleCotizacion;
    var cotizacion = window.cotizacion;
    console.log("detalle", detalle, "cotizacion", cotizacion);

    $("#tipo_solicitud")
        .selectpicker("val", window.tipo_solicitud)
        .change()
        .prop("disabled", true);

    $("#client_id").selectpicker("val", cotizacion.client_id);

    // Datos compartidos
    $("#descripcion").val(
        detalle.descripcion_material_detalle
            ? detalle.descripcion_material_detalle
            : ""
    );
    $("#nombre_contacto").val(cotizacion.nombre_contacto);
    $("#email_contacto").val(cotizacion.email_contacto);
    $("#telefono_contacto").val(cotizacion.telefono_contacto);

    // Si no hay jerarquia es que recien ingreso al formulario por lo tanto no populamos los selects
    // de lo contrario si tiene informacion es que se lleno de algun cambio y debemos llenarlo
    if (detalle.subsubhierarchy_id) populateHierarchies(detalle);

    if (window.tipo_solicitud != 3) {
        $("#numero_colores")
            .selectpicker("val", detalle.numero_colores)
            .change();

        $("#process_id").selectpicker("val", detalle.process_id).change();
    }

    $("#carton_id").selectpicker("val", detalle.carton_id).change();
    $("#detalle_id").val(detalle.id);

    // Datos segun tipo de solicitud
    switch (window.tipo_solicitud) {
        // Desarrollo completo
        case "1":
            if (detalle.material_id) {
                // $("#reference_type").selectpicker("val", 1).change();
                $("#reference_id")
                    .selectpicker("val", detalle.material_id)
                    .change();
            }
            if (detalle.cad_material_id) {
                $("#cad_id")
                    .selectpicker("val", detalle.cad_material_id)
                    .change();
            }
            if (detalle.bct_min_lb) {
                $("#bct_min_lb").val(detalle.bct_min_lb);
            }
            if (detalle.bct_min_kg) {
                $("#bct_min_kg").val(detalle.bct_min_kg);
            }
            $("#product_type_id")
                .selectpicker("val", detalle.product_type_id)
                .change();
            if(detalle.maquila != ''){
                $("#maquila")
                    .selectpicker("val", detalle.maquila)
                    .change();
            }
            if(detalle.maquila_servicio_id != ''){
                $("#maquila_servicio_id")
                    .selectpicker("val", detalle.maquila_servicio_id)
                    .change();
            }
            if (detalle.golpes_ancho) {
                $("#golpes_ancho").val(detalle.golpes_ancho);
            }
            if (detalle.golpes_largo) {
                $("#golpes_largo").val(detalle.golpes_largo);
            }
            break;
        // Cotiza con CAD
        case "2":
            $("#cad_id").selectpicker("val", detalle.cad_material_id).change();
            break;
        // Muestra con CAD
        case "3":
            break;
        // Cotiza sin CAD
        case "4":
            break;
        //Arte con Material
        case "5":
            if (detalle.material_id) {
                $("#reference_id")
                    .selectpicker("val", detalle.material_id)
                    .change();
            }
            if (detalle.cad_material_id) {
                $("#cad_id")
                    .selectpicker("val", detalle.cad_material_id)
                    .change();
            }
            if (detalle.bct) {
                $("#bct").val(detalle.bct);
            }
            if (detalle.unidad_medida_bct) {
                $("#unidad_medida_bct")
                    .selectpicker("val", detalle.unidad_medida_bct)
                    .change();
            }
            if(detalle.maquila != ''){
                $("#maquila")
                    .selectpicker("val", detalle.maquila)
                    .change();
            }
            if(detalle.maquila_servicio_id != ''){
                $("#maquila_servicio_id")
                    .selectpicker("val", detalle.maquila_servicio_id)
                    .change();
            }
            if (detalle.golpes_ancho) {
                $("#golpes_ancho").val(detalle.golpes_ancho);
            }
            if (detalle.golpes_largo) {
                $("#golpes_largo").val(detalle.golpes_largo);
            }
            break;
        default:
            break;
    }
}

function chargeSelectSecOperacional(planta_id){


    return $.ajax({
        type: "GET",
        url: "/getSecuenciasOperacionales",
        data: "planta_id=" + planta_id,
        success: function (data) {

            $('.form-sec-operacional').html(data);

            $('#sec_operacional_principal').selectpicker("refresh");
            $('#sec_operacional_1').selectpicker("refresh");
            $('#sec_operacional_2').selectpicker("refresh");
        },
    });

}

$(document).ready(function () {
    var deferred = configuracionInicialFormOT();
    $.when(deferred).then(cargarDatos());
});
