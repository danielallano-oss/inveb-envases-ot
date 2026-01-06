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

    //////////////////////////////////////////////
    // MASCARAS NUMERICAS

    //Seteamos el valor del recorte adicional para reemplazar los puntos por las comas
    let re = $('#recorte_adicional').val();
    const recorte_adicional_valor = re.replace('.', ',');
    $('#recorte_adicional').val(recorte_adicional_valor)

    var volumenMask = IMask(volumen_venta_anual, thousandsOptions);
    var usdMask = IMask(usd, thousandsOptions);
    var areaProductoMask = IMask(area_producto, fourDecimalsOptions);
    var recorteAdicionalMask = IMask(recorte_adicional, cuatroDecimalsOptions);
    // var recorteAdicionalMask = IMask(recorte_adicional, fourDecimalsOptions);

    const role = $("#role_id").val();

    $("#tipo_solicitud")
        .val($("#tipo_solicitud_ot").val())
        .selectpicker("refresh");

    /*var id_solicitud='';
    if(role== 18){*/
    id_solicitud = $('#tipo_solicitud_ot').val();
    /*}else{
        id_solicitud = $('#tipo_solicitud_2').val();
    }*/
    //const id_solicitud = $('#tipo_solicitud_2').val();
    console.log(`role: ${role} --> id_solicitud: ${id_solicitud}`);
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

    // Popular jerarquias en orden
    const populateHierarchies = async () => {
        await $("#hierarchy_id")
            .val("")
            .triggerHandler("change");
        await $("#subhierarchy_id")
            .val("")
            .triggerHandler("change");
        $("#subsubhierarchy_id")
            .val("")
            .selectpicker("refresh");
        $("#hierarchy_id")
            .val("")
            .selectpicker("refresh");
    };

    populateHierarchies();


    // Si no hay jerarquia es que recien ingreso al formulario por lo tanto no populamos los selects
    // de lo contrario si tiene informacion es que se lleno de algun cambio y debemos llenarlo


    // -------- FIN JERARQUIAS ------------------
    //
    //
    //

    // DESABILITAR CAMPOS SEGUN ROL
    // Area de Venta
    // if (role == 4 || role == 3) {
    //     $(
    //         "#largura_hm,#anchura_hm,#area_producto,#area_interior_perimetro,#rmt,#golpes_largo,#golpes_ancho,#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2,#impresion_1,#impresion_2,#impresion_3,#impresion_4,#impresion_5,#impresion_6,#longitud_pegado,#porcentaje_cera_exterior,#porcentaje_cera_interior,#porcentaje_barniz_interior,#proceso,#pegado_terminacion,#armado,#tipo_sentido_onda,#material_asignado"
    //     ).prop("readonly", true);
    // }
    if (role == 4 || role == 3) {
        $("#largura_hm,#anchura_hm").prop("readonly", true);
    }

    if (role == 3 || role == 4) {
        $(
            "#indicador_facturacion,#largura_hm,#anchura_hm,#area_producto,#recorte_adicional,#bct_min_lb,#bct_min_kg,#golpes_largo,#golpes_ancho,#separacion_golpes_largo,#separacion_golpes_ancho,#cuchillas,#impresion_1,#impresion_2,#impresion_3,#impresion_4,#impresion_5,#impresion_6,#longitud_pegado,#porcentaje_cera_exterior,#porcentaje_cera_interior,#porcentaje_barniz_interior,#tipo_sentido_onda,#material_asignado,#descripcion_material,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
        ).prop("disabled", true);

        //#org_venta_id se elimino disabled 1-10-2024
    }
    // // Area desarrollo
    // if (role == 5 || role == 6) {
    //     $("input,select,textarea").prop("disabled", true);
    //     $(
    //         "#cad_id,#tipo_item_id,#items_set,#veces_item,#carton_id,#style_id,#largura_hm,#anchura_hm,#area_producto,#recubrimiento_id,#rmt,#golpes_largo,#golpes_ancho,#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#pegado,#longitud_pegado,#cera_exterior,#porcentaje_cera_exterior,#cera_interior,#porcentaje_cera_interior,#barniz_interior,#porcentaje_barniz_interior,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#proceso,#pegado_terminacion,#armado,#tipo_sentido_onda"
    //     ).prop("disabled", false);
    //     // habilita el method put y el token csrf
    //     $('input[name="_method"],input[name="_token"]').prop("disabled", false);
    // }
    // // Area catalogacion
    // if (role == 11 || role == 12) {
    //     $("input,select,textarea").prop("disabled", true);
    //     // habilita el method put y el token csrf
    //     $('#material_asignado,input[name="_method"],input[name="_token"]').prop(
    //         "disabled",
    //         false
    //     );
    // }

    let validacion_campos_numero = $("#validacion_campos").val().split(',');

    if (!validacion_campos_numero.includes('0')) {
        notify("Verifique los datos antes de crear el CAD y el Material", "danger");
    }

    if (validacion_campos_numero.includes('2')) {
        console.log('validacion 2');

        if ((parseInt($("#interno_largo").val()) > parseInt($("#externo_largo").val())) || (parseInt($("#interno_ancho").val()) > parseInt($("#externo_ancho").val())) || (parseInt($("#interno_alto").val()) > parseInt($("#externo_alto").val()))) {
            $("#medida-interior-error").html('Las medidas interiores, deben ser menores a las medida exteriores');

            // Ocultamos el mensaje
            setTimeout(function () {
                $("#medida-interior-error").html('');
            }, 15000);
        } else {
            $("#medida-interior-error").html('');
        }

    }

    if (validacion_campos_numero.includes('3')) {
        console.log('validacion 3');

        $("#area-error").html('Los valores de Área HC , Área HM y Área Producto no cumplen las condiciones');

        // Ocultamos el mensaje
        setTimeout(function () {
            $("#area-error").html('');
        }, 15000);

    }

    if (validacion_campos_numero.includes('4')) {
        console.log('validacion 4');

        $("#recorte-error").html('Los valores de Recorte Característico y/o Recorte Adicional, no pueden ser mayor al Área HM');

        // Ocultamos el mensaje
        setTimeout(function () {
            $("#recorte-error").html('');
        }, 15000);

    }

    if (role == 6) { //Rol de Dibujante Técnico (Diseño Estructural) No puede modificar ningun campo de COLOR-CERA-BARNIZ y tampoco el FSC y la planta objetivo
        console.log({ id_solicitud });
        if (id_solicitud == '3') { // Muestra con CAD
            $(` #fsc,#impresion,#percentage_coverage_external,#percentage_coverage_internal,
                #design_type_id,#barniz_uv,#porcentanje_barniz_uv,#complejidad,#color_1_id,#impresion_1,#color_2_id,#impresion_2,
                #color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,
                #indicador_facturacion_diseno_grafico,#coverage_external_id,#coverage_internal_id,#cinta,#planta_id,#carton_color,#carton_id,#numero_colores,
                #cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6`)
                .prop("disabled", true)
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        } else if (id_solicitud == 5) {
            $(` #fsc,#impresion,#percentage_coverage_external,#percentage_coverage_internal,
                #design_type_id,#barniz_uv,#porcentanje_barniz_uv,#complejidad,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,
                #color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,
                #indicador_facturacion_diseno_grafico,#coverage_external_id,#coverage_internal_id,#cinta,#planta_id,#carton_color,#carton_id,
                #cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6`)
                .prop("disabled", true)
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        } else {
            $(`
                #fsc,#impresion,#percentage_coverage_external,#percentage_coverage_internal,
                #design_type_id,#barniz_uv,#porcentanje_barniz_uv,#complejidad,#color_1_id,#impresion_1,#color_2_id,#impresion_2,
                #color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,
                #indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6`)
                .prop("disabled", true)
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        }
    }

    if (role == 8) { //Rol de Diseñador (Diseño Gráfico) No puede modificar el FSC
        if (id_solicitud == '3') {
            $("#fsc,#impresion,#coverage_external_id,#coverage_internal_id,#cinta,#planta_id,#carton_color,#carton_id")
                .prop("disabled", true)
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        } else {
            $("#fsc,#impresion,#coverage_external_id,#coverage_internal_id,#cinta,#planta_id,#carton_color,#carton_id,#numero_colores")
                .prop("disabled", true)
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        }

        $("#design_type_id,#percentage_coverage_internal,#percentage_coverage_external")
            .prop("readonly", false)
            .prop("disabled", false)
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");
    }

    //Funcion que cargara los datos del formulario de Ingresos Principales a lo largo de la vista ( donde van respectivamente )
    function Agregar_datos_de_texto() {

        //-- TIPO ITEM TEXT
        $("#product_type_id_text")
            .prop("disabled", true)
            .val($("#product_type_id").find('option:selected').text().trim());

        //-- Impresión TEXT
        $("#impresion_text")
            .prop("disabled", true)
            .val($("#impresion").find('option:selected').text().trim());

        //-- FSC TEXT
        $("#fsc_text")
            .prop("disabled", true)
            .val($("#fsc").find('option:selected').text().trim());

        //Validacion de Pais con el FSC
        if ($("#fsc").val() == 2) {

            $("#pais_id")
                .prop("disabled", true)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");

        } else {

            $("#pais_id")
                .prop("disabled", false)
                .selectpicker("refresh");
        }

        //-- Cinta TEXT
        $("#cinta_text")
            .prop("disabled", true)
            .val($("#cinta").find('option:selected').text().trim());

        //-- Recubrimiento Interno TEXT
        $("#coverage_internal_id_text")
            .prop("disabled", true)
            .val($("#coverage_internal_id").find('option:selected').text().trim());

        //-- Recubrimiento Externo TEXT
        $("#coverage_external_id_text")
            .prop("disabled", true)
            .val($("#coverage_external_id").find('option:selected').text().trim());

        //-- PLANTA OBJETIVO TEXT
        $("#planta_id_text")
            .prop("disabled", true)
            .val($("#planta_id").find('option:selected').text().trim());

        //-- Color Cartón TEXT
        $("#carton_color_text")
            .prop("disabled", true)
            .val($("#carton_color").find('option:selected').text().trim());

        //-- Cartón TEXT
        $("#carton_id_text")
            .prop("disabled", true)
            .val($("#carton_id").find('option:selected').text().trim());

    }


    //----------- Inicio de Selección de los campos INGRESOS PRINCIPALES para llenar los texto en el resto del formulario
    //alert($("#product_type_id").val());
    //-- TIPO ITEM TEXT
    $("#product_type_id")
        .change(() => {

            if ($("#product_type_id").val() != '') {

                $("#product_type_id_text")
                    .prop("disabled", true)
                    .val($("#product_type_id").find('option:selected').text().trim());

                $("#impresion,#impresion_text")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

            } else {

                $("#product_type_id_text")
                    .prop("disabled", true)
                    .val("");

            }

        });

    //-- Impresión TEXT
    $("#impresion")
        .change(() => {

            if ($("#impresion").val() != '') {

                $("#impresion_text")
                    .prop("disabled", true)
                    .val($("#impresion").find('option:selected').text().trim());

                $("#fsc")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                //Limpia el resto de los campos ( el usuario tendria que volver a llenar todas las opciones de INGRESOS PRINCIPALES)
                $("#cinta,#cinta_text,#coverage_internal_id,#coverage_internal_id_text,#percentage_coverage_internal,#coverage_external_id,#coverage_external_id_text,#percentage_coverage_external,#planta_id,#planta_id_text,#carton_color,#carton_color_text,#carton_id,#carton_id_text")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                //Limpia tambien los datos de barniz
                /* $("#barniz_uv,#porcentanje_barniz_uv")
                     .prop("disabled", false)
                     .val("")
                     .selectpicker("refresh")
                     .closest("div.form-group")
                     .removeClass("error");*/

            } else {

                $("#impresion_text")
                    .prop("disabled", true)
                    .val("");

                //Limpia el resto de los campos ( el usuario tendria que volver a llenar todas las opciones de INGRESOS PRINCIPALES)
                $("#fsc,#cinta,#cinta_text,#coverage_internal_id,#coverage_internal_id_text,#coverage_external_id,#coverage_external_id_text,#planta_id,#planta_id_text,#carton_color,#carton_color_text,#carton_id,#carton_id_text")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

            }

        });

    //-- FSC TEXT
    $("#fsc")
        .change(async () => {

            if ($("#fsc").val() != '') {

                let impresion_id = $("#impresion").val();
                let fsc_id = $("#fsc").val(); //Este valor en la tabla de FSC en realidad es el campo codigo

                $("#fsc_text")
                    .prop("disabled", true)
                    .val($("#fsc").find('option:selected').text().trim());

                const filtro = [
                    {
                        impresion_id: impresion_id,
                        fsc_id: fsc_id,
                        referencia: 'impresion_fsc',
                    }
                ];

                const cint_options = [{ value: 'No', planta_id: '1,2,3' }, { value: 'Si', planta_id: '2,3' }];

                const resultado = await filtroCampos(filtro);
                const clean_options = cint_options.filter(cinta => {
                    const plantas = cinta.planta_id.split(',');

                    if (plantas.some(value => resultado.includes(value))) {
                        return cinta.value;
                    }
                }).map((value, key) => `<option value="${key}">${value.value}</option>`)

                //Comprobamos que clean_options sea mayor de cero (que tenga datos)
                if (!!clean_options.length) {
                    $("#cinta").html(
                        clean_options.toString()
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");

                } else {

                    $("#cinta")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");


                    notify("No se encuantran plantas asociadas", "warning");
                }


                //Limpia el resto de los campos ( el usuario tendria que volver a llenar todas las opciones de INGRESOS PRINCIPALES)
                $("#coverage_internal_id,#coverage_internal_id_text,#percentage_coverage_internal,#coverage_external_id,#coverage_external_id_text,#percentage_coverage_external,#planta_id,#planta_id_text,#carton_color,#carton_color_text,#carton_id,#carton_id_text")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                //Limpia tambien los datos de barniz
                /*$("#barniz_uv,#porcentanje_barniz_uv")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");*/


                //Validacion del pais con el FSC
                if ($("#fsc").val() == 2) {

                    $("#pais_id")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                } else {

                    $("#pais_id")
                        .prop("disabled", false)
                        .selectpicker("refresh");
                }
            }

        });

    //-- Cinta TEXT
    $("#cinta")
        .change(async () => {

            if ($("#cinta").val() != '') {

                let impresion_id = $("#impresion").val();
                let fsc_id = $("#fsc").val();
                let cinta_id = $("#cinta").val();

                $("#cinta_text")
                    .prop("disabled", true)
                    .val($("#cinta").find('option:selected').text().trim());

                const filtro = [
                    {
                        impresion_id: impresion_id,
                        fsc_id: fsc_id,
                        referencia: 'impresion_fsc'
                    },
                    {
                        cinta_id: cinta_id,
                        referencia: 'cinta'
                    }
                ];

                const recubrimiento_interno_opcions = await setRecubrimientoInterno();

                const resultado = await filtroCampos(filtro);
                const clean_options = recubrimiento_interno_opcions.filter(r_interno => {
                    const plantas = r_interno.planta_id.split(',');
                    if (plantas.some(value => resultado.includes(value))) {
                        return r_interno.descripcion;
                    }
                }).map((value) => `<option value="${value.key}">${value.descripcion}</option>`)

                //Comprobamos que clean_options sea mayor de cero (que tenga datos)
                if (!!clean_options.length) {
                    $("#coverage_internal_id").html(
                        clean_options.toString()
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");

                } else {

                    $("#coverage_internal_id")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    notify("No se encuantran plantas asociadas", "warning");
                }

                //Limpia el resto de los campos ( el usuario tendria que volver a llenar todas las opciones de INGRESOS PRINCIPALES)
                $("#coverage_internal_id_text,#percentage_coverage_internal,#coverage_external_id,#coverage_external_id_text,#percentage_coverage_external,#planta_id,#planta_id_text,#carton_color,#carton_color_text,#carton_id,#carton_id_text")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                //Limpia tambien los datos de barniz
                /*$("#barniz_uv,#porcentanje_barniz_uv")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");*/

            }

        });

    //-- Recubrimiento Interno TEXT
    $("#coverage_internal_id")
        .change(async () => {

            if ($("#coverage_internal_id").val() != '') {

                let impresion_id = $("#impresion").val();
                let fsc_id = $("#fsc").val();
                let cinta_id = $("#cinta").val();
                let recubrimiento_interno_id = $("#coverage_internal_id").val();

                $("#coverage_internal_id_text")
                    .prop("disabled", true)
                    .val($("#coverage_internal_id").find('option:selected').text().trim());

                const filtro = [
                    {
                        impresion_id: impresion_id,
                        fsc_id: fsc_id,
                        referencia: 'impresion_fsc'
                    },
                    {
                        cinta_id: cinta_id,
                        referencia: 'cinta'
                    },
                    {
                        recubrimiento_interno_id: recubrimiento_interno_id,
                        referencia: 'recubrimiento_interno'
                    }
                ];

                const opciones = [
                    {
                        impresion_id: impresion_id,
                        referencia: 'impresion_recubrimiento_externo',
                    }
                ];

                const recubrimiento_externo_opcions = await setRecubrimientoExterno(opciones[0]['impresion_id'], opciones[0]['referencia']);

                const resultado = await filtroCampos(filtro);
                const clean_options = recubrimiento_externo_opcions.filter(r_externo => {
                    const plantas = r_externo.planta_id.split(',');
                    if (plantas.some(value => resultado.includes(value))) {//some devuelve true cuando consigue un valor igual
                        return r_externo.descripcion;
                    }
                }).map((value) => `<option value="${value.filtro_2}">${value.descripcion}</option>`)

                //Comprobamos que clean_options sea mayor de cero (que tenga datos)
                if (!!clean_options.length) {
                    $("#coverage_external_id").html(
                        clean_options.toString()
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");

                } else {

                    //Limpia el resto de los campos ( el usuario tendria que volver a llenar todas las opciones de INGRESOS PRINCIPALES)
                    $("#coverage_external_id")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    notify("No se encuantran plantas asociadas", "warning");
                }

                //Limpia el resto de los campos ( el usuario tendria que volver a llenar todas las opciones de INGRESOS PRINCIPALES)
                $("#coverage_external_id_text,#percentage_coverage_external,#planta_id,#planta_id_text,#carton_color,#carton_color_text,#carton_id,#carton_id_text")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                //Limpia tambien los datos de barniz
                /*$("#barniz_uv,#porcentanje_barniz_uv")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");*/

            }

        });

    //-- Recubrimiento Externo TEXT
    $("#coverage_external_id")
        .change(async () => {

            if ($("#coverage_external_id").val() != '') {

                let impresion_id = $("#impresion").val();
                let fsc_id = $("#fsc").val();
                let cinta_id = $("#cinta").val();
                let recubrimiento_interno_id = $("#coverage_internal_id").val();
                let recubrimiento_externo_id = $("#coverage_external_id").val();

                $("#coverage_external_id_text")
                    .prop("disabled", true)
                    .val($("#coverage_external_id").find('option:selected').text().trim());

                const filtro = [
                    {
                        impresion_id: impresion_id,
                        fsc_id: fsc_id,
                        referencia: 'impresion_fsc'
                    },
                    {
                        cinta_id: cinta_id,
                        referencia: 'cinta'
                    },
                    {
                        recubrimiento_interno_id: recubrimiento_interno_id,
                        referencia: 'recubrimiento_interno'
                    },
                    {
                        impresion_id: impresion_id,
                        recubrimiento_externo_id: recubrimiento_externo_id,
                        referencia: 'impresion_recubrimiento_externo',
                    }
                ];

                const planta_objetivo_opcions = await setPlantaObjetivo();

                const resultado = await filtroCampos(filtro);
                const clean_options = planta_objetivo_opcions.filter(planta_objetivo => {
                    if (resultado.includes(String(planta_objetivo.planta_id))) {
                        return planta_objetivo.descripcion;
                    }
                }).map((value) => `<option value="${value.key}">${value.descripcion}</option>`)

                //Comprobamos que clean_options sea mayor de cero (que tenga datos)
                if (!!clean_options.length) {

                    $("#planta_id").html(
                        clean_options.toString()
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");

                } else {

                    $("#planta_id")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    notify("No se encuantran plantas asociadas", "warning");
                }

                //Limpia el resto de los campos ( el usuario tendria que volver a llenar todas las opciones de INGRESOS PRINCIPALES)
                $("#planta_id_text,#carton_color,#carton_color_text,#carton_id,#carton_id_text")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            }
        });

    //-- PLANTA OBJETIVO TEXT
    $("#planta_id")
        .change(async () => {

            if ($("#planta_id").val() != '') {

                let impresion_id = $("#impresion").val();
                let fsc_id = $("#fsc").val();
                let cinta_id = $("#cinta").val();
                let recubrimiento_interno_id = $("#coverage_internal_id").val();
                let recubrimiento_externo_id = $("#coverage_external_id").val();
                let planta_id = $("#planta_id").val();

                $("#planta_id_text")
                    .prop("disabled", true)
                    .val($("#planta_id").find('option:selected').text().trim());

                const filtro = [
                    {
                        impresion_id: impresion_id,
                        fsc_id: fsc_id,
                        referencia: 'impresion_fsc'
                    },
                    {
                        cinta_id: cinta_id,
                        referencia: 'cinta'
                    },
                    {
                        recubrimiento_interno_id: recubrimiento_interno_id,
                        referencia: 'recubrimiento_interno'
                    },
                    {
                        impresion_id: impresion_id,
                        recubrimiento_externo_id: recubrimiento_externo_id,
                        referencia: 'impresion_recubrimiento_externo',
                    }
                ];

                const carton_color_opcions = await setColorCarton();

                const resultado = await filtroCampos(filtro);
                const clean_options = carton_color_opcions.filter(color => {
                    const plantas = color.planta_id.split(',');
                    if (!!color.impresion_id && plantas.some(value => resultado.includes(value)) && color.impresion_id.includes(impresion_id)) {
                        return color.color;
                    }
                })

                //Se tienen que limpiar los valores, ya que si se deja normal, aparecen repetidos los colores ( la idea es que solo salgan dos )
                let colors = [];
                clean_options.forEach(item => {
                    const values = colors.map(v => v.descripcion);
                    if (!values.includes(item.color)) {
                        colors.push({ key: item.color === 'BLANCO' ? 2 : 1, descripcion: item.color });//Se va agregando el color que encuentre
                    }
                })
                colors = colors.map(value => `<option value="${value.key}">${value.descripcion}</option>`)

                //Comprobamos que colors sea mayor de cero (que tenga datos)
                if (!!colors.length) {
                    $("#carton_color").html(
                        colors.toString()
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");

                } else {

                    $("#carton_color")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    notify("No se encuantran cartones asociados", "warning");
                }

                //Limpia el resto de los campos ( el usuario tendria que volver a llenar todas las opciones de INGRESOS PRINCIPALES)
                $("#carton_id,#carton_id_text")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

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
                    $('#check_planta_aux_2').prop('disabled', false);
                }
            }


        });

    //-- Color Cartón TEXT
    $("#carton_color")
        .change(async () => {

            if ($("#carton_color").val() != '') {

                let impresion_id = $("#impresion").val();
                let fsc_id = $("#fsc").val();
                let cinta_id = $("#cinta").val();
                let recubrimiento_interno_id = $("#coverage_internal_id").val();
                let recubrimiento_externo_id = $("#coverage_external_id").val();
                let planta_id = $("#planta_id").val();
                let carton_color = $("#carton_color").val();

                $("#carton_color_text")
                    .prop("disabled", true)
                    .val($("#carton_color").find('option:selected').text().trim());

                const filtro = [
                    {
                        impresion_id: impresion_id,
                        fsc_id: fsc_id,
                        referencia: 'impresion_fsc'
                    },
                    {
                        cinta_id: cinta_id,
                        referencia: 'cinta'
                    },
                    {
                        recubrimiento_interno_id: recubrimiento_interno_id,
                        referencia: 'recubrimiento_interno'
                    },
                    {
                        impresion_id: impresion_id,
                        recubrimiento_externo_id: recubrimiento_externo_id,
                        referencia: 'impresion_recubrimiento_externo',
                    }
                ];

                const carton_color_opcions = await setListaCarton(carton_color, planta_id, impresion_id);

                // const resultado = await filtroCampos(filtro);
                const clean_options = carton_color_opcions.filter(carton => {
                    //const plantas = carton.planta_id.split(',');
                    //if (!!carton.impresion_id && plantas.some( value => resultado.includes(value)) && carton.impresion_id.includes(impresion_id)) {
                    return carton.descripcion;
                    //}
                }).map((value) => `<option value="${value.key}">${value.descripcion}</option>`)

                //Comprobamos que clean_options sea mayor de cero (que tenga datos)
                if (!!clean_options.length) {

                    if (id_solicitud == 1 || id_solicitud == 7) {
                        console.log(role);
                        if (role == 6 || role == 18) {
                            $("#carton_id").html(
                                clean_options.toString()
                            )

                                .prop("disabled", false)
                                .selectpicker("refresh");
                        } else {
                            $("#carton_id").prop('disabled', true).val('').selectpicker("refresh");
                            $('#carton_id_text').val('');
                        }
                    }

                    // $("#carton_id").html(
                    //     clean_options.toString()
                    // )
                    //     .prop("disabled", false)
                    //     .selectpicker("refresh");

                } else {

                    $("#carton_id")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    notify("No se encuantran plantas asociadas", "warning");
                }
            }
        });

    //-- Cartón TEXT
    $("#carton_id")
        .change(() => {

            if ($("#carton_id").val() != '') {

                $("#carton_id_text")
                    .prop("disabled", true)
                    .val($("#carton_id").find('option:selected').text().trim());
            }
        });

    //----------- FIN

    //Consultas para verificar los filtros de Ingresos Principales
    function filtroCampos(datos) {
        return new Promise((resolve) => {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // let resultado;
            $.ajax({
                type: "POST",
                url: "/postVerificacionFiltro",
                data: JSON.stringify(datos),
                contentType: "application/json; charset=utf-8",
                processData: false,
                success: function (data) {
                    resolve(limpiezaPlantas(data));
                },
            });
        })
    }

    //Limpia todas las plantas de los fiktros relacionales para dejar solo las plantas en comun
    function limpiezaPlantas(datos) {

        const plantas = datos['plantas'].flat();
        const limpieza = plantas.filter(i => {
            if (plantas.filter(e => e === i).length >= datos['cantidad_filtro']) { // el 1 es por cada filtro aplicado
                return i;
            }
        })

        return [...new Set(limpieza)];
    }

    //Verificamos las plantas disponibles para el recubrimiento interno
    function setRecubrimientoInterno() {
        return new Promise((resolve) => {
            $.ajax({
                type: "GET",
                url: "/getRecubrimientoInterno",
                success: function (data) {
                    resolve(data)
                },
            });
        })
    }

    //Verificamos las plantas disponibles para el recubrimiento externo
    function setRecubrimientoExterno(impresion, referencia) {
        return new Promise((resolve) => {
            $.ajax({
                type: "GET",
                url: "/getRecubrimientoExterno",
                data: "impresion_id=" + impresion + "&referencia=" + referencia,
                success: function (data) {
                    resolve(data)
                },
            });
        })
    }

    //Verificamos las plantas para mostrar las opciones disponibles segun los select de filtro seleccionados
    function setPlantaObjetivo() {
        return new Promise((resolve) => {
            $.ajax({
                type: "GET",
                url: "/getPlantaObjetivo",
                success: function (data) {
                    resolve(data)
                },
            });
        })
    }

    //Verificamos las opciones de color del carton disponibles para la planta y la impresion seleccionada
    function setColorCarton() {
        return new Promise((resolve) => {
            $.ajax({
                type: "GET",
                url: "/getColorCarton",
                success: function (data) {
                    resolve(data)
                },
            });
        })
    }

    //Verificamos las opciones del carton disponibles para la planta, la impresion y el color del carton seleccionada
    function setListaCarton(color, planta, impresion) {
        return new Promise((resolve) => {
            $.ajax({
                type: "GET",
                url: "/getListaCarton",
                data: "carton_color=" + color + "&planta=" + planta + "&impresion=" + impresion,
                success: function (data) {
                    resolve(data)
                    if ($('#tipo_solicitud').val() == 1 || $('#tipo_solicitud').val() == 7) {
                        if (role == 6 || role == 18) {
                            $('#carton_id').prop('disabled', false).selectpicker('refresh');
                        } else {
                            $('#carton_id').prop('disabled', true).val('').selectpicker('refresh');
                        }
                    }
                },
            });
        })
    }



    // TIPO DE DISEÑO --- Cargar complejidad segun tipo de diseño ( campos habilitados para Jefe de Diseño o el Diseñador )
    const functionDesignType = () => {

        if (role == 4 || role == 6 || role == 7 || role == 8) {
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

    // // Funcion cuando cambia un TIPO DE DISEÑO ---
    const functionDesignTypeChange = () => {
        if (role == 4 || role == 6 || role == 7 || role == 8) {
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
                        $("#design_type_id").prop("disabled", false)

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

    // //IMPRESIÓN -- Validacion de impresion para el numero de colores
    const functionImpresion = () => {

        $("#impresion")
            .change(() => {

                if ($('#impresion').val() === '1') { // 1 => "Offset"

                    if (role === '4') {//para el vendedor

                        $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6")
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

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                } else if ($('#impresion').val() === '2') { //2 => "Flexografía"

                    if (role === '4') {//para el vendedor

                        $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6")
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
                        <option value="4"> 4 </option>`
                    )
                        .prop("disabled", false)
                        .selectpicker("refresh");

                } else if ($('#impresion').val() === '3') { //3 => "Flexografía Alta Gráfica"
                    //alert("alta grafica");
                    if (role === '4') {//para el vendedor

                        $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6")
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

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");


                } else if ($('#impresion').val() === '4') { //4 => "Flexografía Tiro y Retiro"

                    if (role === '4') {//para el vendedor

                        $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6")
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

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                } else if ($('#impresion').val() === '5') { //5 => "Sin Impresión"

                    if (role === '4') {//para el vendedor

                        $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6")
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

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                } else if ($('#impresion').val() === '6' || $('#impresion').val() === '7') { //6 => "Sin Impresión (Sólo OF)", 7 => "Sin Impresión (Trazabilidad Completa)"

                    if (role === '4') {//para el vendedor

                        $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6")
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

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                } else {

                    if (role === '4') {//para el vendedor

                        $("#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6")
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
    //$('#reference_type option[value="0"]').attr("disabled", true);
    //$('#reference_type option[value="1"]').attr("disabled", true);

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


    $("#product_type_id").change(function () {
        let producto_id = ['3', '4', '5', '6', '8', '10', '11', '12', '13', '14', '16', '18', '19', '20', '28', '31', '32', '33', '34']
        let producto = $(this).val();

        if (producto_id.includes(producto)) {
            $("#maquila")
                .prop("disabled", false)
                .val('0')
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


    // RECUBRIMIENTO INTERNO ---- Validacion para liberar los campos
    $("#coverage_internal_id")
        .change(() => {

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


    //RECUBRIMIENTO EXTERNO ---- Validacion para liberar los campos de validacion_color_cera_barniz
    $("#coverage_external_id")
        .change(() => {

            //Se limpian los campos
            // $("#numero_colores")
            //     .prop("disabled", false)
            //     .val("")
            //     .selectpicker("refresh")
            //     .closest("div.form-group")
            //     .removeClass("error");

            if ($("#coverage_external_id").val() == 1) { //No aplica -- se muestra todo de impresión pero el porcentage se deshabilita

                /*$("#barniz_uv")
                    .prop("disabled", true)
                    .val(0)
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
                    <option value="4"> 4 </option>`
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

                /*$("#barniz_uv")
                    .prop("disabled", true)
                    .val(1)
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
                        <option value="4"> 4 </option>`
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

                if ($("#impresion").val() === '2') {
                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>`
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

            } else {//se muestra todo de impresión y se deshabilita el porcentage

                /*$("#barniz_uv")
                    .prop("disabled", false)
                    .val(0)
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
                    <option value="4"> 4 </option>`
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

    //Validacion para el vendedor - color 6 o barniz UV es obligatorio, cuando uno de estos dos tiene datos el otro ya no es obligatorio
    /*$("#color_6_id")
    .change(() => {

        if( $("#color_6_id").val() !== ''){

            /*$("#barniz_uv,#porcentanje_barniz_uv")
                .prop("disabled", false)
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");

        }else if ($("#color_6_id").val() === ''){

            $("#impresion_6")
                .prop("disabled", false)
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        }

    })*/

    //Validacion de BARNIZ UV --- cuando es no se bloquea el % IMPRESIÓN B. UV
    /*$("#barniz_uv")
        .change(() => {

            if( $("#barniz_uv") !== ''){//Validacion para el vendedor - color 6 o barniz UV es obligatorio, cuando uno de estos dos tiene datos el otro ya no es obligatorio

                // $("#color_6_id,#impresion_6")
                //     .prop("disabled", false)
                //     .val("")
                //     .selectpicker("refresh")
                //     .closest("div.form-group")
                //     .removeClass("error");
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

        })*/

    //Maquila ( si maquila es SI activa el campo de servicios de maquila segun el tipo de producto seleccionado)
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
    // .triggerHandler("change");

    // if($("#maquila").val() == ''){
    //     // Maquila por defecto en NO
    //     $("#maquila")
    //         .val("0")
    //         .closest("div.form-group")
    //         .removeClass("error")
    //         .selectpicker("refresh");
    // }

    //Cargo el valor de maquila servicio de la Base de datos segun el id de la OT
    // $("#maquila_servicio_id")
    //     .change(() => {

    //         if($("#maquila_servicio_id").val() != null){
    //             return $.ajax({
    //                 type: "GET",
    //                 url: "/getMaquilaServicio",
    //                 data: "maquila_servicio_id=" + $("#maquila_servicio_id").val(),
    //                 success: function (data) {
    //                     $("#maquila_servicio_id")
    //                         .val(data.id)
    //                         .selectpicker("refresh")
    //                         .triggerHandler("change");
    //                     $("#maquila_servicio_id").prop("disabled", true);
    //                 },
    //             });
    //         }
    // })
    // .triggerHandler("change");

    // Si numero de colores es 0  bloqueamos todos los colores de lo contrario se habilitan acorde al numero seleccionado
    $("#numero_colores")
        .change(() => {
            const numeroColores = $("#numero_colores").val();
            const desabilitarColores = (colores) => {
                $(colores)
                    .prop("disabled", true)
                    .prop('required', false)
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
                    if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
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
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
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
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
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
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
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
                                "#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
                            );
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
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
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
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
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
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
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
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
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
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
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
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
                            desabilitarColores("#color_5_id,#impresion_5,#color_6_id,#impresion_6,#cm2_clisse_color_5,#cm2_clisse_color_6");
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
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
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
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
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
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

                        if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
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

    // si el tipo de producto es "Esquinero (codigo 21)" entonces anchura y ancho interno deben ser 98
    if ($("#product_type_id").val() == 21) {
        $("#anchura_hm,#interno_ancho")
            .prop({ disabled: false, readonly: true })
            .val("98")
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");
        // $("#golpes_largo,#golpes_ancho,#separacion_golpes_ancho,#separacion_golpes_largo")
        $("#golpes_largo,#golpes_ancho,#separacion_golpes_ancho,#separacion_golpes_largo")
            .prop({ disabled: false, readonly: true })
            .val("1")
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");
        /*$("#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2")
            .prop({ disabled: false, readonly: true })
            .val("")
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");*/
    }
    $("#product_type_id").change(() => {
        if ($("#product_type_id").val() != 21) {
            //             anchura_hm
            // interno_ancho
            $(
                "#anchura_hm,#interno_ancho,#golpes_largo,#golpes_ancho,#separacion_golpes_ancho,#separacion_golpes_largo"
            )
                .prop({ disabled: false, readonly: false })
                .val("")
                .closest("div.form-group")
                .removeClass("error");

            // Si es vendedor se debe limpiar pero dejar bloqueado
            if (role == 3 || role == 4) {
                $(
                    "#indicador_facturacion,#anchura_hm,#interno_ancho,#golpes_largo,#golpes_ancho,#separacion_golpes_ancho,#separacion_golpes_largo,#cuchillas"
                )
                    .prop({ readonly: true })
                    .val("")
                    .closest("div.form-group")
                    .removeClass("error");
            }
        } else {
            $("#anchura_hm,#interno_ancho")
                .prop({ disabled: false, readonly: true })
                .val("98")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
            $("#golpes_largo,#golpes_ancho,#separacion_golpes_ancho,#separacion_golpes_largo")
                .prop({ disabled: false, readonly: true })
                .val("1")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
            /* $("#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2")
                 .prop({ disabled: false, readonly: true })
                 .val("")
                 .selectpicker("refresh")
                 .closest("div.form-group")
                 .removeClass("error");*/
        }
    });
    $("#tipo_solicitud")
        .val($("#tipo_solicitud_ot").val())
        .selectpicker("refresh");
    // $("#reference_type").triggerHandler("change");
    // si el tipo de solicitud es distinto a Desarrollo completo  = 1 o arte cad = 5 entonces desabilitamos las referencias
    if (role == 18) {
        if ($("#tipo_solicitud").val() != 1 && $("#tipo_solicitud").val() != 5 && $("#tipo_solicitud").val() != 7) {
            $("#reference_type,#reference_id,#bloqueo_referencia")
                .prop("disabled", false)
                .val("")
                .selectpicker("refresh");
        }
    } else {
        if ($("#tipo_solicitud_2").val() != 1 && $("#tipo_solicitud_2").val() != 5 && $("#tipo_solicitud_2").val() != 7) {
            $("#reference_type,#reference_id,#bloqueo_referencia")
                .prop("disabled", false)
                //.val("") // Se deshabilita por correo enviado por el cliente de fecha 28/03/2024 Asunto: Problemas OT19563
                .selectpicker("refresh");
        }

    }


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
                $("#cantidad").prop("disabled", false);
            }
        })
        .triggerHandler("change");

    // Si el proceso es 4 => "Diecutter-C/Pegado" se bloquean la referencia y el bloqueo referencia
    /*if ($("#process_id").val() == 4) {
        const pegado_terminacion = $("#pegado_terminacion").val();
        // console.log($("#pegado_terminacion").val());
        $("#pegado_terminacion")
            .html(
                '<option value="">Seleccionar...</option><option value="2">Pegado Interno</option><option value="3">Pegado Externo</option>'
            )
            .selectpicker("refresh");
        $("#pegado_terminacion")
            .val(pegado_terminacion)
            .selectpicker("refresh");
    }*/

    // OT: Terminaciones/Pegados: cuando PROCESO= en PEGADO sea automáticamente SI
    $("#process_id")
        .change(() => {
            /*if ($("#process_id").val() == 4) {
                const pegado_terminacion = $("#pegado_terminacion").val();
                // console.log($("#pegado_terminacion").val());
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
            // }

            if (validacion_campos_numero.includes('1') && role == 6 && ($("#process_id").val() == 1 || $("#process_id").val() == 5)) {
                console.log('validacion 1');
                if (role == 18) {
                    if (($("#tipo_solicitud").val() == 1 || $("#tipo_solicitud").val() == 5 || $("#tipo_solicitud").val() == 7) && $("#anchura_hm").val() != '') {
                        $("#rayado-error").html('La suma de los campos Rayado, debe coincidir con el campo Anchura HM')

                        // Ocultamos el mensaje
                        setTimeout(function () {
                            $("#rayado-error").html('');
                        }, 15000);
                    }
                } else {
                    if (($("#tipo_solicitud_2").val() == 1 || $("#tipo_solicitud_2").val() == 5 || $("#tipo_solicitud_2").val() == 7) && $("#anchura_hm").val() != '') {
                        $("#rayado-error").html('La suma de los campos Rayado, debe coincidir con el campo Anchura HM')

                        // Ocultamos el mensaje
                        setTimeout(function () {
                            $("#rayado-error").html('');
                        }, 15000);
                    }
                }


            }
        })
        .triggerHandler("change");



    // Habilita/inabilita la vista del campo de "numero de muestras" segun checklist de "muestra"
    $("#muestra").click(function () {
        if (this.checked) $("#crear_muestra").click();
        $("#container-numero-muetras")[this.checked ? "show" : "hide"]();
        $(".marcas-aprobaciones")[this.checked ? "show" : "hide"]();
    });

    if ($("#muestra").prop("checked")) {
        $("#container-numero-muetras")[
            $("#muestra").prop("checked") ? "show" : "hide"
        ]();
        $(".marcas-aprobaciones")[
            $("#muestra").prop("checked") ? "show" : "hide"
        ]();
    }
    // Si es la edicion debemos bloquear el check de muestra
    //$("#muestra").prop("disabled", true).prop("readonly", true);


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

    //Validacion para el campo pegado
    const functionPegado = () => {

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
            }).triggerHandler("change");

    }


    //CARGA SECUENCIAS OPERACIONALES
    //chargeSelectSecOperacional($("#planta_id").val(), $("#ot_id").val());
    chargeSelectSecOperacionalPlanta($("#ot_id").val());
    chargeSelectSecOperacionalPlantaAux1($("#ot_id").val());
    chargeSelectSecOperacionalPlantaAux2($("#ot_id").val());
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
        getMatriz(val);
        if (!val && role == 6) {
            const elementos = datos_cad
                .map((e) => {
                    return `#${e}`;
                })
                .join(",");
            // enableAndCleanElements(elementos);
            // quitar readonly
            $(elementos)
                .prop("readonly", false)
                .prop("disabled", false)
                .prop("value", "");
            return true;
        }

        if (role == 18) { //Validacion para que pueda editar los valores el rol de super administrador
            const elementos = datos_cad
                .map((e) => {
                    $(`#${e}`)
                        .prop("readonly", false)
                        .prop("disabled", false);
                });
        }

        return $.ajax({
            type: "GET",
            url: "/getCad",
            data: "cad_id=" + val,
            success: function (data) {
                datos_cad.forEach((element) => {
                    if (role == 18) {
                        setValueRole(element, data);
                    } else {
                        setValue(element, data);
                    }
                });
                // Para rayados hacemos una validacion especial
                // Si el CAD viene con datos en los 3 Rayados, se puedan dejar sólo en 0
                // Para ingresar un dato en los rayados, el CAD debe traer los 3 rayados NULL.
                setRayados(data);

            },
        });
    });

    const setValueRole = (val, cad) => {
        $(`#${val}`).prop({ disabled: false, readonly: false }).val(cad[val]);
    };

    const setValue = (val, cad) => {
        $(`#${val}`).prop({ disabled: false, readonly: true }).val(cad[val]);
    };

    const setRayados = (data) => {
        let ot_rayado_c1r1 = $(`#rayado_c1r1`).val();
        let ot_rayado_r1_r2 = $(`#rayado_r1_r2`).val();
        let ot_rayado_r2_c2 = $(`#rayado_r2_c2`).val();
        let rayado_c1r1 = data.rayado_c1r1 ? +data.rayado_c1r1 : null;
        let rayado_r1_r2 = data.rayado_r1_r2 ? +data.rayado_r1_r2 : null;
        let rayado_r2_c2 = data.rayado_r2_c2 ? +data.rayado_r2_c2 : null;

        if (rayado_c1r1 && rayado_r1_r2 && rayado_r2_c2) {
            if (ot_rayado_c1r1 && rayado_c1r1 != ot_rayado_c1r1) {
                rayado_c1r1 = ot_rayado_c1r1;
            }
            if (ot_rayado_r1_r2 && rayado_r1_r2 != ot_rayado_r1_r2) {
                rayado_r1_r2 = ot_rayado_r1_r2;
            }
            if (ot_rayado_r2_c2 && rayado_r2_c2 != ot_rayado_r2_c2) {
                rayado_r2_c2 = ot_rayado_r2_c2;
            }
            // seteamos campos cuando el rol es Dibujante Técnico y tiene una nueva opcion de modificar datos cuando crea el CAD y El Material
            if (!validacion_campos_numero.includes('1') && role == 6) {
                $(`#rayado_c1r1`)
                    //.prop({ disabled: false, readonly: false })
                    .val(rayado_c1r1);
                $(`#rayado_r1_r2`)
                    //.prop({ disabled: false, readonly: false })
                    .val(rayado_r1_r2);
                $(`#rayado_r2_c2`)
                    //.prop({ disabled: false, readonly: false })
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
            }
        } else if (!rayado_c1r1 && !rayado_r1_r2 && !rayado_r2_c2) {
            console.log("TODOS VACIOS");
            // if (ot_rayado_c1r1 && rayado_c1r1 != ot_rayado_c1r1) {
            //     console.log("rayado 1 diferente");
            //     rayado_c1r1 = ot_rayado_c1r1;
            // }
            // if (ot_rayado_r1_r2 && rayado_r1_r2 != ot_rayado_r1_r2) {
            //     rayado_r1_r2 = ot_rayado_r1_r2;
            //     console.log("rayado 2 diferente");
            // }
            // if (ot_rayado_r2_c2 && rayado_r2_c2 != ot_rayado_r2_c2) {
            //     rayado_r2_c2 = ot_rayado_r2_c2;
            //     console.log("rayado 3 diferente");
            // }
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
                // .prop({ disabled: false, readonly: true })
                .val(rayado_c1r1);
            $(`#rayado_r1_r2`)
                //.prop({ disabled: false, readonly: true })
                .val(rayado_r1_r2);
            $(`#rayado_r2_c2`)
                //.prop({ disabled: false, readonly: true })
                .val(rayado_r2_c2);
        }

        if (role == 3 || role == 4) {
            $(`#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2`).prop({
                disabled: false,
                //readonly: true,
            });
        }
    };

    // Este fragmento de codigo habilita para las ots anteriores a la 7000 el campo indicador facturacion para q pueda ser editado
    if (
        $("#ot_id").val() != null &&
        $("#ot_id").val() < 7000 &&
        role != 3 &&
        role != 4
    ) {
        $("#indicador_facturacion")
            .prop("readonly", false)
            .prop("disabled", false)
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");
    }
    var tipo_solicitud = '';
    // Set formulario segun tipo solicitud y rol
    if (role == 18) {
        //alert($("#tipo_solicitud").val());
        tipo_solicitud = $("#tipo_solicitud").val();
    } else {
        tipo_solicitud = $("#tipo_solicitud_2").val();
    }


      if (tipo_solicitud == 1) {
        if (role == 4) {

            $('#trazabilidad').change(function () {
                if (($(this).val() == 2 || $(this).val() == 3) && ($('#impresion').val() == 5)) {
                    $('#numero_colores').val(1).selectpicker('refresh').trigger('change');
                    console.log('entro1');
                } else {
                    $('#numero_colores').val(0).selectpicker('refresh').trigger('change');
                    console.log('no entro1');
                }
            });

        }
    }


    if (tipo_solicitud == 1 || tipo_solicitud == 7) {

        // disableAndCleanElements("#cad_id");
        // area dibujo tecnico
        if (role == 5 || role == 6 || role == 18) {

            // //Validacion de impresion con numeros de colores
            // functionImpresion();

            // Si YA TIENE UN CAD SELECCIONADO LO MOSTRAMOS
            let cad_asignado = $("#cad_asignado").val();
            let material_asignado = $("#material_asignado").val();
            if (cad_asignado) {
                const cad_id = $("#cad_id").val();
                enableCadSelect();
                // Se limpian los cads y se muestra solo el select de cad id y ahora lo seleccionamos dinamicamente para q
                // los campos correspondientes queden readonly
                // if(role == 18 || validacion_campos_numero.includes('0') ){
                if (role == 18) {
                    $("#cad_id").val(cad_id).selectpicker("refresh");
                } else {
                    $("#cad_id").val(cad_id).triggerHandler("change");
                }

                areaProductoMask.updateControl();
                recorteAdicionalMask.updateControl();

                // Si ademas tiene ya el material creado si debemos bloquear select y solo mostrar el cad
                if (material_asignado) {

                    if (role == 18) {
                        //Cuando es rol 18 (superadministrador) puede editar todo
                        $("input").prop("readonly", false);
                        $("select, input:checkbox,#cad").prop("disabled", false);

                        $("#material_asignado,#descripcion_material").prop("readonly", true);
                    } else {
                        // Desactivamos TODO
                        $("input").prop("readonly", true);
                        $("select, input:checkbox,#cad").prop("disabled", true);
                    }

                    if ($("#ot_id").val() != null && $("#ot_id").val() < 3000) {
                        $(
                            "#fsc,#sentido_armado,#cinta,#distancia_cinta_1,#distancia_cinta_2,#distancia_cinta_3,#distancia_cinta_4,#distancia_cinta_5,#distancia_cinta_6,#corte_liner,#tipo_cinta"
                        )
                            .prop("readonly", false)
                            .prop("disabled", false)
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    }
                    // disableCadSelect();
                    // const elementos = datos_cad
                    //     .map((e) => {
                    //         return `#${e}`;
                    //     })
                    //     .join(",");
                    // toggleAndCleanElements(elementos, false);
                }
            } else {
                enableCadSelect();
            }

            //
            // $("#seleccionarCad").click(function (e) {
            //     e.preventDefault();
            //     enableCadSelect();
            // });
            // $("#crearCad").click(function (e) {
            //     e.preventDefault();
            //     disableCadSelect();
            //     const elementos = datos_cad
            //         .map((e) => {
            //             return `#${e}`;
            //         })
            //         .join(",");
            //     toggleAndCleanElements(elementos, false);
            //     // $("#cad").prop("readonly", false);
            // });
        } else {

            // Si YA TIENE UN CAD SELECCIONADO LO MOSTRAMOS
            let cad_asignado = $("#cad_asignado").val();
            if (cad_asignado) {
                const cad_id = $("#cad_id").val();
                enableCadSelect();
                // Se limpian los cads y se muestra solo el select de cad id y ahora lo seleccionamos dinamicamente para q
                // los campos correspondientes queden readonly
                $("#cad_id").val(cad_id).triggerHandler("change");
                areaProductoMask.updateControl();
                recorteAdicionalMask.updateControl();
            } else {
                enableCadSelect();
            }
            // Si no es Dibujante Técnico se bloquean todos los datos del cad
            const elementos = datos_cad
                .map((e) => {
                    return `#${e}`;
                })
                .join(",");
            $(elementos).prop("readonly", true);

            let material_asignado = $("#material_asignado").val();
            // Si tiene material asignado
            if (material_asignado) {
                // Desactivamos TODO
                $("input:not([aria-label='Search'])").prop("readonly", true);
                $("select, input:checkbox").prop("disabled", true);

                // y si es usuario diseñador se permite editar cera pegado barniz
                if (role == 5 || role == 6 || role == 7 || role == 8) {
                    $(
                        "#nombre_contacto,#email_contacto,#telefono_contacto,#numero_colores,#color_1_id,#color_2_id,#color_3_id,#color_4_id,#color_5_id,#color_6_id,#pegado,#impresion_1,#impresion_2,#impresion_3,#impresion_4,#impresion_5,#impresion_6,#longitud_pegado,#porcentaje_cera_exterior,#porcentaje_cera_interior,#porcentaje_barniz_interior,#cm2_clisse_color_1,#cm2_clisse_color_2,#cm2_clisse_color_3,#cm2_clisse_color_4,#cm2_clisse_color_5,#cm2_clisse_color_6"
                    )
                        .prop("readonly", false)
                        .prop("disabled", false)
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                    //$("#fsc").triggerHandler("change");//Se descomenta para mantener los valos de los ingresos principales
                }
                if ($("#ot_id").val() != null && $("#ot_id").val() < 3000) {
                    $(
                        "#fsc,#sentido_armado,#cinta,#distancia_cinta_1,#distancia_cinta_2,#distancia_cinta_3,#distancia_cinta_4,#distancia_cinta_5,#distancia_cinta_6,#corte_liner,#tipo_cinta"
                    )
                        .prop("readonly", false)
                        .prop("disabled", false)
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                }

                // disableCadSelect();
                const elementos = datos_cad
                    .map((e) => {
                        return `#${e}`;
                    })
                    .join(",");
                // toggleAndCleanElements(elementos, false);
            }
        }

        $("#recubrimiento").triggerHandler("change");
        $("#cad").prop("readonly", true);
        $("#material_asignado").prop("readonly", true);
        $("#descripcion_material").prop("readonly", true);

        //Validacion para el campo pegado
        functionPegado();

        //Validacion de impresion con numeros de colores ( se deja ahora pra todos los roles )

        functionImpresion();

        // if(role == 8){
        functionDesignTypeChange();
        // }

        //Agrega los datos del formulario Ingresos principales, en donde va cada campo correspondiente en la vista general
        Agregar_datos_de_texto();

    } else if (tipo_solicitud == 2 || tipo_solicitud == 3) {
        // Primero guardamos el valor del cad seleccionado
        const cad_id = $("#cad_id").val();
        enableCadSelect();
        // Se limpian los cads y se muestra solo el select de cad id y ahora lo seleccionamos dinamicamente para q
        // los campos correspondientes queden readonly
        $("#cad_id").val(cad_id).triggerHandler("change");
        areaProductoMask.updateControl();
        recorteAdicionalMask.updateControl();

        // .selectpicker("refresh")
        // .closest("div.form-group")
        // .removeClass("error");

        //Validacion para el campo pegado
        functionPegado();

        //Validacion de impresion con numeros de colores ( se deja ahora pra todos los roles )
        functionImpresion();

        // if(role == 8){
        functionDesignTypeChange();
        // }

        //Agrega los datos del formulario Ingresos principales, en donde va cada campo correspondiente en la vista general
        Agregar_datos_de_texto();


    } else if (tipo_solicitud == 5) {

        //Validacion para el campo pegado
        functionPegado();

        //Validacion de impresion con numeros de colores ( se deja ahora pra todos los roles )
        functionImpresion();

        // if(role == 8){
        functionDesignTypeChange();
        // }

        //Agrega los datos del formulario Ingresos principales, en donde va cada campo correspondiente en la vista general
        Agregar_datos_de_texto();

        // Primero guardamos el valor del cad seleccionado
        const cad_id = $("#cad_id").val();
        enableCadSelect();
        // Se limpian los cads y se muestra solo el select de cad id y ahora lo seleccionamos dinamicamente para q
        // los campos correspondientes queden readonly
        $("#cad_id").val(cad_id).triggerHandler("change");
        // $("#cad_id,#reference_type,#carton_id,#product_type_id,#style_id").prop(
        //     "disabled",
        //     true
        // );
        //Ajuste rapido 21-01-22
        $("#cad_id,#reference_type,#product_type_id,#style_id").prop(
            "disabled",
            true
        );
        let material_asignado = $("#material_asignado").val();
        if (material_asignado) {
            $("#reference_id").prop("disabled", true);
        }
        // console.log("cad id", cad_id, areaProductoMask);
        areaProductoMask.updateControl();
        recorteAdicionalMask.updateControl();

        // Al seleccionar un material de referencia cargar el cad
        $("#reference_id").change(function () {
            var val = $(this).val();
            return $.ajax({
                type: "GET",
                url: "/getCadByMaterial",
                data: "material_id=" + val,
                success: function (data) {
                    // datos_cad.forEach((element) => {
                    //     setValue(element, data);
                    // });
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


    //Se desabilitan las opcion NO y SI , ya que son reemplazadas solo por los valores activos en la tabla fsc
    $('#fsc option[value="0"]').attr("disabled", true);
    $('#fsc option[value="1"]').attr("disabled", true);



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

    // $("#check_vb_muestra").click(function () {
    //     $("#upload_file_vb_muestra")[this.checked ? "show" : "hide"]();
    //     if (this.checked) {
    //         fileVbMuestra.click();
    //     }
    // });

    // $("#check_vb_boce").click(function () {
    //     $("#upload_file_vb_boce")[this.checked ? "show" : "hide"]();
    //     if (this.checked) {
    //         fileVbBoce.click();
    //     }
    // });

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

    // fileVbMuestra.addEventListener('change', function () {
    //     $('#file_chosen_vb_muestra').attr('data-original-title', this.files[0].name);
    //     $('#file_chosen_vb_muestra').show();
    //     $('#file_chosen_vb_muestra').tooltip();
    // })

    // fileVbBoce.addEventListener('change', function () {
    //     $('#file_chosen_vb_boce').attr('data-original-title', this.files[0].name);
    //     $('#file_chosen_vb_boce').show();
    //     $('#file_chosen_vb_boce').tooltip();
    // })

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
    if (role != 4 && role != 3 && role != 18) {
        $("#restriccion_pallet,#tamano_pallet_type_id,#altura_pallet,#permite_sobresalir_carga")
            .prop("disabled", true)
            .selectpicker("refresh");
    } else {
        $("restriccion_pallet")
            .prop("disabled", false)
            .prop('required', true)
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");
    }
    //Edicion Antecedentes de Desarrollo solo para rol de vendedor
    if (role != 4 && role != 3) {
        $("#check_correo_cliente").prop("disabled", true);
        $("#check_plano_actual").prop("disabled", true);
        $("#check_boceto_actual").prop("disabled", true);
        $("#check_speed").prop("disabled", true);
        $("#check_otro").prop("disabled", true);
        // $("#check_vb_muestra").prop("disabled", true);
        // $("#check_vb_boce").prop("disabled", true);
        $("#check_conservar_si").prop("disabled", true);
        $("#check_conservar_no").prop("disabled", true);
        // $("#check_armado_automatico_si").prop("disabled", true);
        // $("#check_armado_automatico_no").prop("disabled", true);
        $("#check_referencia_de").prop("disabled", true);
        $("check_referencia_dg").prop("disabled", true);
        $("check_envase_primario").prop("disabled", true);
    }


    if ((role == 4 || role == 3 || role == 19 || role == 6 || role == 7 || role == 8 || role == 5) && $('#state_id').val() != 7) {

        // console.log('boceto old')
        if ($('#check_vb_boce').is(':checked')) {
            $('#check_vb_boce').prop('checked', true);
        } else {
            $('#check_vb_boce').prop('disabled', false);

        }

    } else {
        $('#check_vb_boce').prop('disabled', true);

    }

    if ((role == 4 || role == 3 || role == 19 || role == 6 || role == 7 || role == 8 || role == 5) && $('#state_id').val() != 7) {

        if ($('#check_vb_muestra').is(':checked')) {
            $('#check_vb_muestra').prop('checked', true);
        } else {
            $('#check_vb_muestra').prop('disabled', false);
        }

        // $('#check_vb_muestra').prop('disabled', false);
    } else {
        $('#check_vb_muestra').prop('disabled', true);

    }

    //Rol de Diseñador Se Habilita el porcentaje de recubrimiento interno y externo para su edicion
    //con vale de recubrimiento diferente a "No Aplica"
    if (role == 8 || role == 7) {
        if ($("#coverage_external_id").val() != 1) {

            $("#percentage_coverage_external")
                .prop("readonly", false)
                .prop("disabled", false)
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        }
        if ($("#coverage_internal_id").val() != 1) {

            $("#percentage_coverage_internal")
                .prop("readonly", false)
                .prop("disabled", false)
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        }
        //Validacion para hbilitar modificacion campo tipo de diseño para el rol Diseñador
        //si la impresion es de algun tipo de flexografia
        //solicitado en evolutivo 72
        if ($("#impresion").val() == 2 || $("#impresion").val() == 3 || $("#impresion").val() == 4) {
            $("#design_type_id")
                .prop("readonly", false)
                .prop("disabled", false)
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        } else {
            $("#design_type_id")
                .prop("readonly", true)
                .prop("disabled", true)
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        }
    }

    /*$("#barniz_uv")
        .prop("disabled", true)
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



    if (role == 3 || role == 4 || role == 5 || role == 6) {//Validar roles vendedores
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
        if (role == 18) {
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
        $("#pegado_terminacion")
            .prop("disabled", true)
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");
    }

    //Se dehabilitamos todos los campos de la seccion de ingresos principales. Fecha: 28/12/2022
    $("#product_type_id,#impresion,#fsc,#cinta,#coverage_internal_id,#coverage_external_id,#planta_id,#carton_color,#carton_id")
        .prop("disabled", true)
        .selectpicker("refresh")
        .closest("div.form-group")
        .removeClass("error");

    //Se habilitan la opciones de la seccion de ingresos principales que son
    //editables para los roles de diseño estructural segun archivo del cliente . Fecha: 28/12/2022
    //if(role == 5 || role==6 || role==18){
    $("#fsc,#impresion,#product_type_id,#cinta,#coverage_internal_id,#coverage_external_id,#planta_id,#carton_color,#carton_id")
        .prop("disabled", false)
        .selectpicker("refresh")
        .closest("div.form-group")
        .removeClass("error");
    /*// Se deshabilita por correo enviado por el cliente de fecha 28/03/2024 Asunto: Problemas OT19563
    $("#reference_type,#reference_id,#bloqueo_referencia,#indicador_facturacion")
        .prop("disabled", false)
        .val("")
        .selectpicker("refresh");
    */

    if ($("#tipo_solicitud").val() == 5) {
        $('#reference_type option[value="2"]').remove();
    }

    $("#rayado_c1r1").val('');
    $("#rayado_r1_r2").val('');
    $("#rayado_r2_c2").val('');
    $("#veces_item").val('');
    $("#externo_largo").val('');
    $("#externo_ancho").val('');
    $("#externo_alto").val('');
    $("#interno_largo").val('');
    $("#interno_ancho").val('');
    $("#interno_alto").val('');
    $("#area_producto").val('');
    $("#recorte_adicional").val('');
    $("#largura_hm").val('');
    $("#anchura_hm").val('');
    $("#items_set").val('');
    $("#cad").val('');
    $("#cad_id")
        .val('')
        .selectpicker("refresh");
    $("#matriz_id")
        .val('')
        .selectpicker("refresh");
    enableCadSelect();
    Agregar_datos_de_texto();
    // }


    if ($("#instalacion").val() != '') {
        var val = $("#client_id").val();
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

                $("#instalacion_cliente")
                    .val($("#instalacion").val())
                    .selectpicker("refresh")
                    .triggerHandler("change");
            },
            error: function (e) {
                console.log(e.responseText);
            },
            async: true
        });
    } else {
        //alert($("#client_id").val());
        $("#client_id")
            .selectpicker("refresh")
            .triggerHandler("change");

    }

    $('#caracteristicas_adicionales').prop("disabled", true);

    // tipo_solicitud.prop("disabled", true);

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

    /// Evolutivo 24-07
    if (role == 4 || role == 3) {
        if ($('#oc').val() == 1) {
            document.getElementById("subida_archivo_oc").style.display = "block";
        } else {
            document.getElementById("subida_archivo_oc").style.display = "none";
        }
    }
    $("#oc")
        .prop("disabled", false)
        .prop("readonly", false)
        .selectpicker("refresh");
    ///


    $('#planta_id').change(function () {

        // console.log($('#planta_id').val());
        var val = $(this).val();

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

    $('#tipo_matriz_text').prop('disabled', true);

    //Evolutivo 24-11 ajuste 2 - Inicio
    if (role != 3 && role != 4 && role != 18 && role != 19 && role != 5 && role != 7) {
        $('#trazabilidad').prop('disabled', true);
    } else {
        $('#trazabilidad').prop('disabled', false);
    }
    //Evolutivo 24-11 ajuste 2 - Fin
    $('#carton_id').prop('disabled', true).selectpicker("refresh");

    console.log(role);
    console.log(id_solicitud);
    if (id_solicitud == 1 || id_solicitud == 7) {

        if (role == 6 || role == 18) {
            $("#carton_id").prop("disabled", false)
                .selectpicker("refresh");
        } else {
            $("#carton_id").prop('disabled', true).val('').selectpicker("refresh");
            $('#carton_id_text').val('');
        }
    }

});

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


$("#modal-indicaciones-especiales-edit").on("show.bs.modal", function (event) {
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

/// Evolutivo 24-06
$("#button_aplicar_caracteristica").click(function () {
    var result_caracteristicas = "";


    if ($('#check_na').is(':checked')) {

        result_caracteristicas = 'N/A';
    } else {

        //traba anclaje
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

///Solicitud Correo Cliente Asunto "Colocar CAD y modificar el cartón" de Fecha 29-04-2024
$("#reference_type,#reference_id,#bloqueo_referencia,#indicador_facturacion")
    .prop("disabled", false)
    .prop("readonly", false)
    .val("")
    .selectpicker("refresh");

$("#canal_id,#hierarchy_id")
    .val("")
    .selectpicker("refresh")
    .triggerHandler("change");
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


if ($("#role_id").val() == 4 || $("#role_id").val() == 3) {
    if ($("#oc").val() == 1) {
        document.getElementById("subida_archivo_oc").style.display = "block";
    } else {
        document.getElementById("subida_archivo_oc").style.display = "none";
    }
}
/// Evolutivo 24-07 - Fin


//CARGA SECUENCIAS OPERACIONALES
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

function chargeSelectSecOperacionalPlanta(ot_id) {

    $('#sec_ope_ppal_planta_ori_1,#sec_ope_atl_1_planta_ori_1,#sec_ope_atl_2_planta_ori_1,#sec_ope_atl_3_planta_ori_1,#sec_ope_atl_4_planta_ori_1,#sec_ope_atl_5_planta_ori_1,#sec_ope_ppal_planta_ori_2,#sec_ope_atl_1_planta_ori_2,#sec_ope_atl_2_planta_ori_2,#sec_ope_atl_3_planta_ori_2,#sec_ope_atl_4_planta_ori_2,#sec_ope_atl_5_planta_ori_2,#sec_ope_ppal_planta_ori_3,#sec_ope_atl_1_planta_ori_3,#sec_ope_atl_2_planta_ori_3,#sec_ope_atl_3_planta_ori_3,#sec_ope_atl_4_planta_ori_3,#sec_ope_atl_5_planta_ori_3,#sec_ope_ppal_planta_ori_4,#sec_ope_atl_1_planta_ori_4,#sec_ope_atl_2_planta_ori_4,#sec_ope_atl_3_planta_ori_4,#sec_ope_atl_4_planta_ori_4,#sec_ope_atl_5_planta_ori_4,#sec_ope_ppal_planta_ori_5,#sec_ope_atl_1_planta_ori_5,#sec_ope_atl_2_planta_ori_5,#sec_ope_atl_3_planta_ori_5,#sec_ope_atl_4_planta_ori_5,#sec_ope_atl_5_planta_ori_5,#sec_ope_ppal_planta_ori_6,#sec_ope_atl_1_planta_ori_6,#sec_ope_atl_2_planta_ori_6,#sec_ope_atl_3_planta_ori_6,#sec_ope_atl_4_planta_ori_6,#sec_ope_atl_5_planta_ori_6')
        .html("")
        .prop('disabled', false)
        .selectpicker('refresh');

    return $.ajax({
        type: "GET",
        url: "/chargeSelectSecOperacionalPlanta",
        data: "ot_id=" + ot_id,
        success: function (data) {

            if (data.planta === null || typeof data.planta === 'undefined') {
                if ($('#planta_id').val() == 1) {
                    $('#planta_original_sec_ope').val('BUIN').prop('disabled', true);
                    getSecuenciasOperacionalesPlanta(1);
                    $('#sec_ope_planta_orig_id').val(1);


                } else {
                    if ($('#planta_id').val() == 2) {
                        $('#planta_original_sec_ope').val('TILTIL').prop('disabled', true);
                        getSecuenciasOperacionalesPlanta(2);
                        $('#sec_ope_planta_orig_id').val(2);
                    } else {
                        $('#planta_original_sec_ope').val('OSORNO').prop('disabled', true);
                        getSecuenciasOperacionalesPlanta(3);
                        $('#sec_ope_planta_orig_id').val(3);
                    }
                }

            } else {

                /* if(data.cantidad_filas>3){

                     $('#sec_ope_ppal_planta_ori_1,#sec_ope_atl_1_planta_ori_1,#sec_ope_atl_2_planta_ori_1,#sec_ope_ppal_planta_ori_2,#sec_ope_atl_1_planta_ori_2,#sec_ope_atl_2_planta_ori_2,#sec_ope_ppal_planta_ori_3,#sec_ope_atl_1_planta_ori_3,#sec_ope_atl_2_planta_ori_3')
                         .html(data.html)
                         .prop('disabled',false)
                         .selectpicker('refresh');

                     if(data.planta==1){
                         $('#planta_original_sec_ope').val('BUIN').prop('disabled',true);
                         $('#sec_ope_planta_orig_id').val(1);
                     }else{
                         if(data.planta==2){
                             $('#planta_original_sec_ope').val('TILTIL').prop('disabled',true);
                             $('#sec_ope_planta_orig_id').val(2);
                         }else{
                             $('#planta_original_sec_ope').val('OSORNO').prop('disabled',true);
                             $('#sec_ope_planta_orig_id').val(3);
                         }
                     }

                     for (let i = 4; i <= (data.cantidad_filas); i++) {

                         var fila ='';
                         fila +='<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
                         fila +='    <div class="col-4">';
                         fila +='        <div class="form-group form-row">';
                         fila +='            <div class="col">';
                         fila +='                <div id="selector_clon_1_'+i+'"></div>';
                         fila +='            </div>';
                         fila +='        </div>';
                         fila +='    </div>';
                         fila +='    <div class="col-4">';
                         fila +='        <div class="form-group form-row">';
                         fila +='            <div class="col">';
                         fila +='                <div id="selector_clon_2_'+i+'"></div>';
                         fila +='            </div>';
                         fila +='        </div>';
                         fila +='    </div>';
                         fila +='    <div class="col-4">';
                         fila +='        <div class="form-group form-row">';
                         fila +='            <div class="col">';
                         fila +='                <div id="selector_clon_3_'+i+'"></div>';
                         fila +='            </div>';
                         fila +='        </div>';
                         fila +='    </div>';
                         fila +='</div>';
                         fila +='<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
                         fila +='    <div class="col-12">';
                         fila +='        &nbsp;';
                         fila +='    </div>';
                         fila +='</div>';

                         $('#fila_planta_original').append(fila);

                         // Clonar el select picker
                         var $original = $('#sec_ope_ppal_planta_ori_3');
                         var $aux_1 = $('#sec_ope_atl_1_planta_ori_3');
                         var $aux_2 = $('#sec_ope_atl_2_planta_ori_3');
                         // Asegúrate de usar el ID correcto
                         var $clon_org = $original.clone();
                         var $aux_1 = $aux_1.clone();
                         var $aux_2 = $aux_2.clone();

                         $clon_org.attr('id', 'sec_ope_ppal_planta_ori_' + i);
                         $clon_org.attr('name', 'sec_ope_ppal_planta_ori_' + i);
                         $clon_org.removeClass('selectpicker').selectpicker('destroy');
                         $('#selector_clon_1_'+i).append($clon_org);
                         $clon_org.selectpicker();

                         $aux_1.attr('id', 'sec_ope_atl_1_planta_ori_' + i);
                         $aux_1.attr('name', 'sec_ope_atl_1_planta_ori_' + i);
                         $aux_1.removeClass('selectpicker').selectpicker('destroy');
                         $('#selector_clon_2_'+i).append($aux_1);
                         $aux_1.selectpicker();

                         $aux_2.attr('id', 'sec_ope_atl_2_planta_ori_' + i);
                         $aux_2.attr('name', 'sec_ope_atl_2_planta_ori_' + i);
                         $aux_2.removeClass('selectpicker').selectpicker('destroy');
                         $('#selector_clon_3_'+i).append($aux_2);
                         $aux_2.selectpicker();

                     }

                     for (let i = 1; i <= (data.cantidad_filas); i++) {

                         if(!(data.array['fila_' + i] === undefined)){
                             if(!(data.array['fila_' + i]['org'] === undefined)){
                                 $('#sec_ope_ppal_planta_ori_'+i).val(data.array['fila_' + i]['org']).selectpicker('refresh');
                             }
                             if(!(data.array['fila_' + i]['alt1'] === undefined)){
                                 $('#sec_ope_atl_1_planta_ori_'+i).val(data.array['fila_' + i]['alt1']).selectpicker('refresh');
                             }
                             if(!(data.array['fila_' + i]['alt2'] === undefined)){
                                 $('#sec_ope_atl_2_planta_ori_'+i).val(data.array['fila_' + i]['alt2']).selectpicker('refresh');
                             }
                         }
                     }

                 }else{*/
                $('#sec_ope_ppal_planta_ori_1,#sec_ope_atl_1_planta_ori_1,#sec_ope_atl_2_planta_ori_1,#sec_ope_atl_3_planta_ori_1,#sec_ope_atl_4_planta_ori_1,#sec_ope_atl_5_planta_ori_1,#sec_ope_ppal_planta_ori_2,#sec_ope_atl_1_planta_ori_2,#sec_ope_atl_2_planta_ori_2,#sec_ope_atl_3_planta_ori_2,#sec_ope_atl_4_planta_ori_2,#sec_ope_atl_5_planta_ori_2,#sec_ope_ppal_planta_ori_3,#sec_ope_atl_1_planta_ori_3,#sec_ope_atl_2_planta_ori_3,#sec_ope_atl_3_planta_ori_3,#sec_ope_atl_4_planta_ori_3,#sec_ope_atl_5_planta_ori_3,#sec_ope_ppal_planta_ori_4,#sec_ope_atl_1_planta_ori_4,#sec_ope_atl_2_planta_ori_4,#sec_ope_atl_3_planta_ori_4,#sec_ope_atl_4_planta_ori_4,#sec_ope_atl_5_planta_ori_4,#sec_ope_ppal_planta_ori_5,#sec_ope_atl_1_planta_ori_5,#sec_ope_atl_2_planta_ori_5,#sec_ope_atl_3_planta_ori_5,#sec_ope_atl_4_planta_ori_5,#sec_ope_atl_5_planta_ori_5,#sec_ope_ppal_planta_ori_6,#sec_ope_atl_1_planta_ori_6,#sec_ope_atl_2_planta_ori_6,#sec_ope_atl_3_planta_ori_6,#sec_ope_atl_4_planta_ori_6,#sec_ope_atl_5_planta_ori_6')
                    .html(data.html)
                    .prop('disabled', false)
                    .selectpicker('refresh');

                if (data.planta == 1) {
                    $('#planta_original_sec_ope').val('BUIN').prop('disabled', true);
                    $('#sec_ope_planta_orig_id').val(1);
                } else {
                    if (data.planta == 2) {
                        $('#planta_original_sec_ope').val('TILTIL').prop('disabled', true);
                        $('#sec_ope_planta_orig_id').val(2);
                    } else {
                        $('#planta_original_sec_ope').val('OSORNO').prop('disabled', true);
                        $('#sec_ope_planta_orig_id').val(3);
                    }
                }

                /* for (let i = 1; i <= (data.cantidad_filas); i++) {
                     if(!(data.array['fila_' + i] === undefined)){
                         if(!(data.array['fila_' + i]['org'] === undefined)){
                             $('#sec_ope_ppal_planta_ori_'+i).val(data.array['fila_' + i]['org']).selectpicker('refresh');
                         }
                         if(!(data.array['fila_' + i]['alt1'] === undefined)){
                             $('#sec_ope_atl_1_planta_ori_'+i).val(data.array['fila_' + i]['alt1']).selectpicker('refresh');
                         }
                         if(!(data.array['fila_' + i]['alt2'] === undefined)){
                             $('#sec_ope_atl_2_planta_ori_'+i).val(data.array['fila_' + i]['alt2']).selectpicker('refresh');
                         }
                     }
                 }    */

                //  }
            }



            if (data.cantidad_filas == 0 || isNaN(data.cantidad_filas)) {
                $('#sec_ope_planta_orig_filas').val(6);
            } else {
                $('#sec_ope_planta_orig_filas').val(data.cantidad_filas);
            }

            $('#agregar_fila_planta_original').prop('disabled', false);
            //getSecuenciasOperacionalesOt(ot_id);
            $('#check_planta_aux_1').prop('disabled', false);
            $('#check_planta_aux_2').prop('disabled', false);
        },
    });

}

function chargeSelectSecOperacionalPlantaAux1(ot_id) {

    $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3')
        .html("")
        .prop('disabled', true)
        .selectpicker('refresh');

    return $.ajax({
        type: "GET",
        url: "/chargeSelectSecOperacionalPlantaAux1",
        data: "ot_id=" + ot_id,
        success: function (data) {


            if (data.planta === null || typeof data.planta === 'undefined') {
                if ($('#planta_id').val() == 1) {
                    $('#planta_aux_1_sec_ope').val('TILTIL').prop('disabled', true);
                    getSecuenciasOperacionalesPlantaAux1(2);
                    $('#sec_ope_planta_aux_1_id').val(2);
                } else {
                    if ($('#planta_id').val() == 2) {
                        $('#planta_aux_1_sec_ope').val('BUIN').prop('disabled', true);
                        getSecuenciasOperacionalesPlantaAux1(1);
                        $('#sec_ope_planta_aux_1_id').val(1);
                    } else {
                        $('#planta_aux_1_sec_ope').val('BUIN').prop('disabled', true);
                        getSecuenciasOperacionalesPlantaAux1(1);
                        $('#sec_ope_planta_aux_1_id').val(1);
                    }
                }

            } else {

                /*  if(data.cantidad_filas>3){
                      $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3')
                          .html(data.html)
                          .prop('disabled',true)
                          .selectpicker('refresh');

                      if(data.planta==1){
                          $('#planta_aux_1_sec_ope').val('BUIN').prop('disabled',true);
                          $('#sec_ope_planta_aux_1_id').val(1);
                      }else{
                          if(data.planta==2){
                              $('#planta_aux_1_sec_ope').val('TILTIL').prop('disabled',true);
                              $('#sec_ope_planta_aux_1_id').val(2);
                          }else{
                              $('#planta_aux_1_sec_ope').val('OSORNO').prop('disabled',true);
                              $('#sec_ope_planta_aux_1_id').val(3);
                          }
                      }

                      for (let i = 4; i <= (data.cantidad_filas); i++) {
                          var fila ='';
                          var num_fila = i;
                          fila +='<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
                          fila +='    <div class="col-4">';
                          fila +='        <div class="form-group form-row">';
                          fila +='            <div class="col">';
                          fila +='                <div id="selector_aux_1_1_'+num_fila+'"></div>';
                          fila +='            </div>';
                          fila +='        </div>';
                          fila +='    </div>';
                          fila +='    <div class="col-4">';
                          fila +='        <div class="form-group form-row">';
                          fila +='            <div class="col">';
                          fila +='                <div id="selector_aux_1_2_'+num_fila+'"></div>';
                          fila +='            </div>';
                          fila +='        </div>';
                          fila +='    </div>';
                          fila +='    <div class="col-4">';
                          fila +='        <div class="form-group form-row">';
                          fila +='            <div class="col">';
                          fila +='                <div id="selector_aux_1_3_'+num_fila+'"></div>';
                          fila +='            </div>';
                          fila +='        </div>';
                          fila +='    </div>';
                          fila +='</div>';
                          fila +='<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
                          fila +='    <div class="col-12">';
                          fila +='        &nbsp;';
                          fila +='    </div>';
                          fila +='</div>';

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
                          $('#selector_aux_1_1_'+num_fila).append($clon_org);
                          $clon_org.selectpicker();

                          $aux_1.attr('id', 'sec_ope_atl_1_planta_aux_1_' + num_fila);
                          $aux_1.attr('name', 'sec_ope_atl_1_planta_aux_1_' + num_fila);
                          $aux_1.removeClass('selectpicker').selectpicker('destroy');
                          $('#selector_aux_1_2_'+num_fila).append($aux_1);
                          $aux_1.selectpicker();

                          $aux_2.attr('id', 'sec_ope_atl_2_planta_aux_1_' + num_fila);
                          $aux_2.attr('name', 'sec_ope_atl_2_planta_aux_1_' + num_fila);
                          $aux_2.removeClass('selectpicker').selectpicker('destroy');
                          $('#selector_aux_1_3_'+num_fila).append($aux_2);
                          $aux_2.selectpicker();
                      }

                      if(data.habilitado){
                          $('#check_planta_aux_1').prop('disabled',false);
                          $('#check_planta_aux_1').prop('checked', true);
                          $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3')
                              .prop('disabled',false)
                              .selectpicker('refresh');// Marca el checkbox como seleccionado
                          $('#agregar_fila_planta_auxiliar_1').prop('disabled',false);
                      }else{
                          // Aquí puedes desmarcar el checkbox si es necesario
                          $('#check_planta_aux_1').prop('checked', false);
                          $('#agregar_fila_planta_auxiliar_1').prop('disabled',true);
                      }

                      for (let i = 1; i <= (data.cantidad_filas); i++) {
                          var num_fila = i;

                          if(!(data.array['fila_' + num_fila] === undefined)){
                              if(!(data.array['fila_' + num_fila]['org'] === undefined)){
                                  $('#sec_ope_ppal_planta_aux_1_'+num_fila).val(data.array['fila_' + num_fila]['org']).selectpicker('refresh');
                                  if(data.habilitado){
                                      $('#sec_ope_ppal_planta_aux_1_'+num_fila).prop('disabled',false).selectpicker('refresh');
                                  }
                              }
                              if(!(data.array['fila_' + num_fila]['alt1'] === undefined)){
                                  $('#sec_ope_atl_1_planta_aux_1_'+num_fila).val(data.array['fila_' + num_fila]['alt1']).selectpicker('refresh');
                                  if(data.habilitado){
                                      $('#sec_ope_atl_1_planta_aux_1_'+num_fila).prop('disabled',false).selectpicker('refresh');
                                  }
                              }
                              if(!(data.array['fila_' + num_fila]['alt2'] === undefined)){
                                  $('#sec_ope_atl_2_planta_aux_1_'+num_fila).val(data.array['fila_' + num_fila]['alt2']).selectpicker('refresh');
                                  if(data.habilitado){
                                      $('#sec_ope_atl_2_planta_aux_1_'+num_fila).prop('disabled',false).selectpicker('refresh');
                                  }
                              }
                          }
                      }

                  }else{*/


                $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3')
                    .html(data.html)
                    .prop('disabled', true)
                    .selectpicker('refresh');

                if (data.planta == 1) {
                    $('#planta_aux_1_sec_ope').val('BUIN').prop('disabled', true);
                    $('#sec_ope_planta_aux_1_id').val(1);
                } else {
                    if (data.planta == 2) {
                        $('#planta_aux_1_sec_ope').val('TILTIL').prop('disabled', true);
                        $('#sec_ope_planta_aux_1_id').val(2);
                    } else {
                        $('#planta_aux_1_sec_ope').val('OSORNO').prop('disabled', true);
                        $('#sec_ope_planta_aux_1_id').val(3);
                    }
                }

                if (data.habilitado) {
                    $('#check_planta_aux_1').prop('disabled', false);
                    $('#check_planta_aux_1').prop('checked', true);
                    $('#sec_ope_ppal_planta_aux_1_1,#sec_ope_atl_1_planta_aux_1_1,#sec_ope_atl_2_planta_aux_1_1,#sec_ope_ppal_planta_aux_1_2,#sec_ope_atl_1_planta_aux_1_2,#sec_ope_atl_2_planta_aux_1_2,#sec_ope_ppal_planta_aux_1_3,#sec_ope_atl_1_planta_aux_1_3,#sec_ope_atl_2_planta_aux_1_3')
                        .prop('disabled', false)
                        .selectpicker('refresh');// Marca el checkbox como seleccionado
                    $('#agregar_fila_planta_auxiliar_1').prop('disabled', false);
                } else {
                    // Aquí puedes desmarcar el checkbox si es necesario
                    $('#check_planta_aux_1').prop('checked', false);
                    $('#agregar_fila_planta_auxiliar_1').prop('disabled', true);
                }

                /*   for (let i = 1; i <= (data.cantidad_filas); i++) {
                       var num_fila = i;

                       if(!(data.array['fila_' + num_fila] === undefined)){
                           if(!(data.array['fila_' + num_fila]['org'] === undefined)){
                               $('#sec_ope_ppal_planta_aux_1_'+num_fila).val(data.array['fila_' + num_fila]['org']).selectpicker('refresh');
                           }
                           if(!(data.array['fila_' + num_fila]['alt1'] === undefined)){
                               $('#sec_ope_atl_1_planta_aux_1_'+num_fila).val(data.array['fila_' + num_fila]['alt1']).selectpicker('refresh');
                           }
                           if(!(data.array['fila_' + num_fila]['alt2'] === undefined)){
                               $('#sec_ope_atl_2_planta_aux_1_'+num_fila).val(data.array['fila_' + num_fila]['alt2']).selectpicker('refresh');
                           }
                       }
                   }  */
                //  }
            }

            if (data.cantidad_filas == 0 || isNaN(data.cantidad_filas)) {
                $('#sec_ope_planta_aux_1_filas').val(3);
            } else {
                $('#sec_ope_planta_aux_1_filas').val(data.cantidad_filas);
            }
            //getSecuenciasOperacionalesOt(ot_id);
        },
    });

}

function chargeSelectSecOperacionalPlantaAux2(ot_id) {

    $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3')
        .html("")
        .prop('disabled', true)
        .selectpicker('refresh');

    return $.ajax({
        type: "GET",
        url: "/chargeSelectSecOperacionalPlantaAux2",
        data: "ot_id=" + ot_id,
        success: function (data) {

            if (data.planta === null || typeof data.planta === 'undefined') {
                if ($('#planta_id').val() == 1) {
                    $('#planta_aux_2_sec_ope').val('OSORNO').prop('disabled', true);
                    getSecuenciasOperacionalesPlantaAux2(3);
                    $('#sec_ope_planta_aux_2_id').val(3);
                } else {
                    if ($('#planta_id').val() == 2) {
                        $('#planta_aux_2_sec_ope').val('OSORNO').prop('disabled', true);
                        getSecuenciasOperacionalesPlantaAux2(1);
                        $('#sec_ope_planta_aux_2_id').val(1);
                    } else {
                        $('#planta_aux_2_sec_ope').val('TILTIL').prop('disabled', true);
                        getSecuenciasOperacionalesPlantaAux2(2);
                        $('#sec_ope_planta_aux_2_id').val(2);
                    }
                }

            } else {

                /*if(data.cantidad_filas>3){
                    $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3')
                        .html(data.html)
                        .prop('disabled',true)
                        .selectpicker('refresh');

                    if(data.planta==1){
                        $('#planta_aux_2_sec_ope').val('BUIN').prop('disabled',true);
                        $('#sec_ope_planta_aux_2_id').val(1);
                    }else{
                        if(data.planta==2){
                            $('#planta_aux_2_sec_ope').val('TILTIL').prop('disabled',true)   ;
                            $('#sec_ope_planta_aux_2_id').val(2);
                        }else{
                            $('#planta_aux_2_sec_ope').val('OSORNO').prop('disabled',true);
                            $('#sec_ope_planta_aux_2_id').val(3);
                        }
                    }

                    if(data.habilitado){
                        $('#check_planta_aux_2').prop('disabled',false);
                        $('#check_planta_aux_2').prop('checked', true); // Marca el checkbox como seleccionado
                        $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3')
                            .prop('disabled',false)
                            .selectpicker('refresh');
                        $('#agregar_fila_planta_auxiliar_2').prop('disabled',false);
                    }else{
                        // Aquí puedes desmarcar el checkbox si es necesario
                        $('#check_planta_aux_2').prop('checked', false);
                        $('#agregar_fila_planta_auxiliar_2').prop('disabled',true);
                    }

                    for (let i = 4; i <= (data.cantidad_filas); i++) {

                        var num_fila = i;
                        var fila ='';
                        fila +='<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
                        fila +='    <div class="col-4">';
                        fila +='        <div class="form-group form-row">';
                        fila +='            <div class="col">';
                        fila +='                <div id="selector_aux_2_1_'+num_fila+'"></div>';
                        fila +='            </div>';
                        fila +='        </div>';
                        fila +='    </div>';
                        fila +='    <div class="col-4">';
                        fila +='        <div class="form-group form-row">';
                        fila +='            <div class="col">';
                        fila +='                <div id="selector_aux_2_2_'+num_fila+'"></div>';
                        fila +='            </div>';
                        fila +='        </div>';
                        fila +='    </div>';
                        fila +='    <div class="col-4">';
                        fila +='        <div class="form-group form-row">';
                        fila +='            <div class="col">';
                        fila +='                <div id="selector_aux_2_3_'+num_fila+'"></div>';
                        fila +='            </div>';
                        fila +='        </div>';
                        fila +='    </div>';
                        fila +='</div>';
                        fila +='<div class="form-row" style="margin-left: 0px;margin-right: 0px;">';
                        fila +='    <div class="col-12">';
                        fila +='        &nbsp;';
                        fila +='    </div>';
                        fila +='</div>';

                        $('#fila_planta_aux_2').append(fila);

                        // Clonar el select picker
                        var $original = $('#sec_ope_ppal_planta_aux_2_3');
                        var $aux_1 = $('#sec_ope_atl_1_planta_aux_2_3');
                        var $aux_2 = $('#sec_ope_atl_2_planta_aux_2_3');
                        // Asegúrate de usar el ID correcto
                        var $clon_org = $original.clone();
                        var $aux_1 = $aux_1.clone();
                        var $aux_2 = $aux_2.clone();

                        $clon_org.attr('id', 'sec_ope_ppal_planta_aux_2_' + num_fila);
                        $clon_org.attr('name', 'sec_ope_ppal_planta_aux_2_' + num_fila);
                        $clon_org.removeClass('selectpicker').selectpicker('destroy');
                        $('#selector_aux_2_1_'+num_fila).append($clon_org);
                        $clon_org.selectpicker();

                        $aux_1.attr('id', 'sec_ope_atl_1_planta_aux_2_' + num_fila);
                        $aux_1.attr('name', 'sec_ope_atl_1_planta_aux_2_' + num_fila);
                        $aux_1.removeClass('selectpicker').selectpicker('destroy');
                        $('#selector_aux_2_2_'+num_fila).append($aux_1);
                        $aux_1.selectpicker();

                        $aux_2.attr('id', 'sec_ope_atl_2_planta_aux_2_' + num_fila);
                        $aux_2.attr('name', 'sec_ope_atl_2_planta_aux_2_' + num_fila);
                        $aux_2.removeClass('selectpicker').selectpicker('destroy');
                        $('#selector_aux_2_3_'+num_fila).append($aux_2);
                        $aux_2.selectpicker();
                    }

                    for (let i = 1; i <= (data.cantidad_filas); i++) {
                        var num_fila = i;
                        console.log(data.array['fila_' + num_fila]);
                        if(!(data.array['fila_' + num_fila] === undefined)){
                            if(!(data.array['fila_' + num_fila]['org'] === undefined)){
                                $('#sec_ope_ppal_planta_aux_2_'+num_fila).val(data.array['fila_' + num_fila]['org']).selectpicker('refresh');
                                if(data.habilitado){
                                    $('#sec_ope_ppal_planta_aux_2_'+num_fila).prop('disabled',false).selectpicker('refresh');
                                }
                            }
                            if(!(data.array['fila_' + num_fila]['alt1'] === undefined)){
                                $('#sec_ope_atl_1_planta_aux_2_'+num_fila).val(data.array['fila_' + num_fila]['alt1']).selectpicker('refresh');
                                if(data.habilitado){
                                    $('#sec_ope_atl_1_planta_aux_2_'+num_fila).prop('disabled',false).selectpicker('refresh');
                                }
                            }
                            if(!(data.array['fila_' + num_fila]['alt2'] === undefined)){
                                $('#sec_ope_atl_2_planta_aux_2_'+num_fila).val(data.array['fila_' + num_fila]['alt2']).selectpicker('refresh');
                                if(data.habilitado){
                                    $('#sec_ope_atl_2_planta_aux_2_'+num_fila).prop('disabled',false).selectpicker('refresh');
                                }
                            }
                        }
                    }

                }else{*/

                $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3')
                    .html(data.html)
                    .prop('disabled', true)
                    .selectpicker('refresh');

                if (data.planta == 1) {
                    $('#planta_aux_2_sec_ope').val('BUIN').prop('disabled', true);
                    $('#sec_ope_planta_aux_2_id').val(1);
                } else {
                    if (data.planta == 2) {
                        $('#planta_aux_2_sec_ope').val('TILTIL').prop('disabled', true);
                        $('#sec_ope_planta_aux_2_id').val(2);
                    } else {
                        $('#planta_aux_2_sec_ope').val('OSORNO').prop('disabled', true);
                        $('#sec_ope_planta_aux_2_id').val(3);
                    }
                }

                if (data.habilitado) {
                    $('#check_planta_aux_2').prop('disabled', false);
                    $('#check_planta_aux_2').prop('checked', true); // Marca el checkbox como seleccionado
                    $('#sec_ope_ppal_planta_aux_2_1,#sec_ope_atl_1_planta_aux_2_1,#sec_ope_atl_2_planta_aux_2_1,#sec_ope_ppal_planta_aux_2_2,#sec_ope_atl_1_planta_aux_2_2,#sec_ope_atl_2_planta_aux_2_2,#sec_ope_ppal_planta_aux_2_3,#sec_ope_atl_1_planta_aux_2_3,#sec_ope_atl_2_planta_aux_2_3')
                        .prop('disabled', false)
                        .selectpicker('refresh');
                    $('#agregar_fila_planta_auxiliar_2').prop('disabled', false);
                } else {
                    // Aquí puedes desmarcar el checkbox si es necesario
                    $('#check_planta_aux_2').prop('checked', false);
                    $('#agregar_fila_planta_auxiliar_2').prop('disabled', true);
                }

                /*for (let i = 1; i <= (data.cantidad_filas); i++) {
                    var num_fila = i;
                    console.log(data.array['fila_' + num_fila]);
                    if(!(data.array['fila_' + num_fila] === undefined)){
                        if(!(data.array['fila_' + num_fila]['org'] === undefined)){
                            $('#sec_ope_ppal_planta_aux_2_'+num_fila).val(data.array['fila_' + num_fila]['org']).selectpicker('refresh');
                        }
                        if(!(data.array['fila_' + num_fila]['alt1'] === undefined)){
                            $('#sec_ope_atl_1_planta_aux_2_'+num_fila).val(data.array['fila_' + num_fila]['alt1']).selectpicker('refresh');
                        }
                        if(!(data.array['fila_' + num_fila]['alt2'] === undefined)){
                            $('#sec_ope_atl_2_planta_aux_2_'+num_fila).val(data.array['fila_' + num_fila]['alt2']).selectpicker('refresh');
                        }
                    }
                }*/
                //}
            }

            if (data.cantidad_filas == 0 || isNaN(data.cantidad_filas)) {
                $('#sec_ope_planta_aux_2_filas').val(3);
            } else {
                $('#sec_ope_planta_aux_2_filas').val(data.cantidad_filas);
            }
            // getSecuenciasOperacionalesOt(ot_id);
        },
    });

}

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
///

$('#planta_id').change(function () {

    // console.log($('#planta_id').val());
    var val = $(this).val();

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


//CARGA DE MATRICES
function getMatriz(cad_id) {

    // $('#golpes_largo').val('');
    // $('#golpes_ancho').val('');
    // $('#separacion_golpes_largo').val('');
    // $('#separacion_golpes_ancho').val('');

    return $.ajax({
        type: "GET",
        url: "/getMatriz",
        data: "cad_id=" + cad_id,
        success: function (data) {
            console.log('1111' + data);

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


