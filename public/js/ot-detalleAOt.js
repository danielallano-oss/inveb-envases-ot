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
    const usdMask = IMask(usd, thousandsOptions);
    var areaProductoMask = IMask(area_producto, fourDecimalsOptions);
    var recorteAdicionalMask = IMask(recorte_adicional, cuatroDecimalsOptions);
    // -------- JERARQUIAS ------------------

    // Al comienzo los select de jerarquia 2 y 3 comienzan desabilitados y luego son habilitados al cambiar la jerarquia 1

    $("#subhierarchy_id,#subsubhierarchy_id,#hierarchy_id").prop("disabled", true);
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
            error: function(e) {
                console.log(e.responseText);
            },
            async:true
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
            error: function(e) {
                console.log(e.responseText);
            },
            async:true
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

        if($("#coverage_internal_id").val() == '' || $("#coverage_external_id").val() == ''){
            $("#impresion,#design_type_id,#complejidad,#numero_colores,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#color_interno,#impresion_color_interno,#indicador_facturacion_diseno_grafico"
            ).prop("disabled", true)
            .val("")
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");
        }

    }

    //Se bloquean todos los campos del formulario Ingresos Principales hasta que seleccione el tipo de solicitud
    const validacion_ingresos_principales = () => {

        $("#impresion,#fsc,#cinta,#coverage_internal_id,#coverage_external_id,#planta_id,#carton_color,#carton_id")
        .prop("disabled", true)
        .val("")
        .selectpicker("refresh")
        .closest("div.form-group")
        .removeClass("error");

        //----------- Inicio de Selección de los campos INGRESOS PRINCIPALES para llenar los texto en el resto del formulario

        //-- TIPO ITEM TEXT
        $("#product_type_id")
        .change(() => {

            if($("#product_type_id").val() != ''){

                $("#product_type_id_text")
                    .prop("disabled", true)
                    .val($("#product_type_id").find('option:selected').text().trim());

                $("#impresion,#impresion_text")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

            }else{

                $("#product_type_id_text")
                    .prop("disabled", true)
                    .val("");

            }
        });

        //-- Impresión TEXT
        $("#impresion")
        .change(() => {

            if($("#impresion").val() != ''){

                $("#impresion_text")
                    .prop("disabled", true)
                    .val($("#impresion").find('option:selected').text().trim());

                if($("#fsc_instalation").val()!=''){
                    $("#fsc")
                        .prop("disabled", false)
                        .val($("#fsc_instalation").val())
                        .selectpicker("refresh")
                        .triggerHandler("change")
                        .closest("div.form-group")
                        .removeClass("error");
                }else{
                    $("#fsc")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                }


                //Limpia el resto de los campos ( el usuario tendria que volver a llenar todas las opciones de INGRESOS PRINCIPALES)
                $("#cinta,#cinta_text,#coverage_internal_id,#coverage_internal_id_text,#coverage_external_id,#coverage_external_id_text,#planta_id,#planta_id_text,#carton_color,#carton_color_text,#carton_id,#carton_id_text")
                    .prop("disabled", true)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                /*Limpia tambien los datos de barniz
                $("#barniz_uv,#porcentanje_barniz_uv")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");*/
            }else{

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

            if($("#fsc").val() != ''){

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

                const cint_options = [{value: 'No', planta_id: '1,2,3'}, {value: 'Si', planta_id: '2,3'}];

                const resultado = await filtroCampos(filtro);
                const clean_options = cint_options.filter( cinta => {
                    const plantas = cinta.planta_id.split(',');

                    if (plantas.some( value => resultado.includes(value))) {
                        return cinta.value;
                    }
                }).map( (value, key) => `<option value="${key}">${value.value}</option>`)

                //Comprobamos que clean_options sea mayor de cero (que tenga datos)
                if (!!clean_options.length) {
                    $("#cinta").html(
                        clean_options.toString()
                    )
                    .prop("disabled", false)
                    .selectpicker("refresh");

                    //Limpia el resto de los campos ( el usuario tendria que volver a llenar todas las opciones de INGRESOS PRINCIPALES)
                    $("#coverage_internal_id,#coverage_internal_id_text,#coverage_external_id,#coverage_external_id_text,#planta_id,#planta_id_text,#carton_color,#carton_color_text,#carton_id,#carton_id_text")
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

                }else{

                    $("#cinta")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    notify("No se encuantran plantas asociadas", "warning");
                }
            }

        });

        //-- Cinta TEXT
        $("#cinta")
        .change(async () => {

            if($("#cinta").val() != ''){
                let impresion_id = $("#impresion").val();
                let fsc_id = $("#fsc").val();
                let cinta_id = $("#cinta").val();
                let tipo_solicitud = $("#tipo_solicitud").val();
                let role = $("#role_id").val();

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
                const clean_options = recubrimiento_interno_opcions.filter( r_interno => {
                    const plantas = r_interno.planta_id.split(',');
                    if (plantas.some( value => resultado.includes(value))) {
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

                    //Limpia el resto de los campos ( el usuario tendria que volver a llenar todas las opciones de INGRESOS PRINCIPALES)
                    $("#coverage_external_id,#coverage_external_id_text,#planta_id,#planta_id_text,#carton_color,#carton_color_text,#carton_id,#carton_id_text")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    /*Limpia tambien los datos de barniz
                    $("#barniz_uv,#porcentanje_barniz_uv")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");*/

                    // Se habilita planta ya que los recubrimientos serán opcionales si el requerimiento es MuestraConCad y Rol Vendedor
                    if (tipo_solicitud == 3 && role == 4) {
                        const planta_objetivo_opcions = await setPlantaObjetivo();
                        const clean_options_plantas = planta_objetivo_opcions.filter( planta_objetivo => {
                            if (resultado.includes(String(planta_objetivo.planta_id))) {
                                return planta_objetivo.descripcion;
                            }
                        }).map((value) => `<option value="${value.key}">${value.descripcion}</option>`)
                        if (!!clean_options_plantas) {
                            $("#planta_id").html(
                                clean_options_plantas.toString()
                            )
                            .prop("disabled", false)
                            .selectpicker("refresh");
                        } else {
                            $("#planta_id")
                                .prop("disabled", false)
                                .val("")
                                .selectpicker("refresh")
                                .closest("div.form-group")
                                .removeClass("error");

                            notify("No se encuantran plantas asociadas", "warning");
                        }

                    }

                }else{

                    $("#coverage_internal_id")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    notify("No se encuantran plantas asociadas", "warning");
                }
            }

        });

        //-- Recubrimiento Interno TEXT
        $("#coverage_internal_id")
        .change(async () => {

            if($("#coverage_internal_id").val() != ''){

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
                const clean_options = recubrimiento_externo_opcions.filter( r_externo => {
                    const plantas = r_externo.planta_id.split(',');
                    if (plantas.some( value => resultado.includes(value))) {//some devuelve true cuando consigue un valor igual
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

                    //Limpia el resto de los campos ( el usuario tendria que volver a llenar todas las opciones de INGRESOS PRINCIPALES)
                    $("#planta_id,#planta_id_text,#carton_color,#carton_color_text,#carton_id,#carton_id_text")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    /*Limpia tambien los datos de barniz
                    $("#barniz_uv,#porcentanje_barniz_uv")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");*/

                }else{

                    $("#coverage_external_id")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    notify("No se encuantran plantas asociadas", "warning");
                }
            }

        });

        //-- Recubrimiento Externo TEXT
        $("#coverage_external_id")
        .change(async () => {

            if($("#coverage_external_id").val() != ''){

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

                const clean_options = planta_objetivo_opcions.filter( planta_objetivo => {
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

                    //Limpia el resto de los campos ( el usuario tendria que volver a llenar todas las opciones de INGRESOS PRINCIPALES)
                    $("#carton_color,#carton_color_text,#carton_id,#carton_id_text")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                }else{

                    $("#planta_id")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    notify("No se encuantran plantas asociadas", "warning");
                }
            }
        });

        //-- PLANTA OBJETIVO TEXT
        $("#planta_id")
        .change(async () => {

            if($("#planta_id").val() != ''){

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
                const clean_options = carton_color_opcions.filter( color => {
                    const plantas = color.planta_id.split(',');
                    if (!!color.impresion_id && plantas.some( value => resultado.includes(value)) && color.impresion_id.includes(impresion_id)) {
                        return color.color;
                    }
                })

                //Se tienen que limpiar los valores, ya que si se deja normal, aparecen repetidos los colores ( la idea es que solo salgan dos )
                let colors = [];
                clean_options.forEach(item => {
                    const values = colors.map(v => v.descripcion);
                    if (!values.includes(item.color)) {
                        colors.push({key: item.color === 'BLANCO' ? 2 : 1, descripcion: item.color});//Se va agregando el color que encuentre
                    }
                })
                colors = colors.map( value => `<option value="${value.key}">${value.descripcion}</option>`)

                //Comprobamos que colors sea mayor de cero (que tenga datos)
                if (!!colors.length) {
                    $("#carton_color").html(
                        colors.toString()
                    )
                    .prop("disabled", false)
                    .selectpicker("refresh");

                    //Limpia el resto de los campos ( el usuario tendria que volver a llenar todas las opciones de INGRESOS PRINCIPALES)
                    $("#carton_id,#carton_id_text")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                }else{

                    $("#carton_color")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    notify("No se encuantran cartones asociados", "warning");
                }
            }
        });

        //-- Color Cartón TEXT
        $("#carton_color")
        .change(async () => {

            if($("#carton_color").val() != ''){

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

                const carton_color_opcions = await setListaCarton(carton_color,planta_id,impresion_id);
                //const resultado = await filtroCampos(filtro);
                const clean_options = carton_color_opcions.filter( carton => {
                    //const plantas = carton.planta_id.split(',');
                    //if (!!carton.impresion_id && plantas.some( value => resultado.includes(value)) && carton.impresion_id.includes(impresion_id)) {
                        return carton.descripcion;
                    //}
                }).map((value) => `<option value="${value.key}">${value.descripcion}</option>`)

                //Comprobamos que clean_options sea mayor de cero (que tenga datos)
                if (!!clean_options.length) {
                    $("#carton_id,#carton_id_combinabilidad,#carton_id_mckee").html(
                        clean_options.toString()
                    )

                    .prop("disabled", false)
                    .selectpicker("refresh");

                }else{

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

            if($("#carton_id").val() != ''){

                $("#carton_id_text")
                    .prop("disabled", true)
                    .val($("#carton_id").find('option:selected').text().trim());
                $("#carton_id_combinabilidad")
                    .val($("#carton_id").val())
                    .selectpicker('refresh')
                    .triggerHandler("change");


            }
        });

        //----------- FIN
    }

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
                error: function(e) {
                    console.log(e.responseText);
                },
                async:true
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
                error: function(e) {
                    console.log(e.responseText);
                },
                async:true
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
                error: function(e) {
                    console.log(e.responseText);
                },
                async:true
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
                error: function(e) {
                    console.log(e.responseText);
                },
                async:true
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
                error: function(e) {
                    console.log(e.responseText);
                },
                async:true
            });
        })
    }

    //Verificamos las opciones del carton disponibles para la planta, la impresion y el color del carton seleccionada
    function setListaCarton(color,planta,impresion) {

        return new Promise((resolve) => {

            $.ajax({
                type: "GET",
                url: "/getListaCarton",
                data: "carton_color=" + color + "&planta=" + planta +"&impresion=" + impresion,
                success: function (data) {
                    console.log(data);
                    resolve(data)
                },
                error: function(e) {
                    console.log(e.responseText);
                },
                async:true
            });
        })
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
                    error: function(e) {
                        console.log(e.responseText);
                    },
                    async:true
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

    // Funcion cuando cambia un TIPO DE DISEÑO ---
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
                            error: function(e) {
                                console.log(e.responseText);
                            },
                            async:true
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


    //IMPRESIÓN -- Validacion de impresion para el numero de colores
    const functionImpresion = () => {

        $("#impresion")
            .change(() => {

                if($("#impresion").val() === '1'){ // 1 => "Offset"


                    if(role === '4'){//para el vendedor

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

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                }else if($("#impresion").val() === '2'){ //2 => "Flexografía"


                    if(role === '4'){//para el vendedor


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

                }else if($("#impresion").val() === '3'){ //3 => "Flexografía Alta Gráfica"


                    if(role === '4'){//para el vendedor

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

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                }else if($("#impresion").val() === '4'){ //4 => "Flexografía Tiro y Retiro"

                    if(role === '4'){//para el vendedor

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

                }else if($("#impresion").val() === '5'){ //5 => "Sin Impresión"

                    if(role === '4'){//para el vendedor

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

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                }else if($("#impresion").val() === '6' || $("#impresion").val() === '7'){ //6 => "Sin Impresión (Sólo OF)", 7 => "Sin Impresión (Trazabilidad Completa)"

                    if(role === '4'){//para el vendedor

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
                }else{

                    if(role === '4'){//para el vendedor

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

                    $("#color_interno,#impresion_color_interno")
                        .prop("disabled", false)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
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
    if (role == 3 || role == 4) {
        $(
            "#indicador_facturacion,#largura_hm,#anchura_hm,#area_producto,#recorte_adicional,#bct_min_lb,#bct_min_kg,#golpes_largo,#golpes_ancho,#separacion_golpes_largo,#separacion_golpes_ancho,#cuchillas,#impresion_1,#impresion_2,#impresion_3,#impresion_4,#impresion_5,#impresion_6,#longitud_pegado,#porcentaje_cera_exterior,#porcentaje_cera_interior,#porcentaje_barniz_interior,#tipo_sentido_onda,#material_asignado,#descripcion_material"
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


    // RECUBRIMIENTO INTERNO ----
    $("#coverage_internal_id")
        .change(() => {

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
        .triggerHandler("change");


    //RECUBRIMIENTO EXTERNO ----
    $("#coverage_external_id")
    .change(() => {

        //Se limpian los campos
        // $("#numero_colores")
        //     .prop("disabled", false)
        //     .val("")
        //     .selectpicker("refresh")
        //     .closest("div.form-group")
        //     .removeClass("error");

        if($("#coverage_external_id").val() == 1){ //No aplica -- se muestra todo de impresión pero el porcentage se deshabilita

            /*$("#barniz_uv")
                .prop("disabled", true)
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
            if($("#impresion").val() === '2'){
                $("#numero_colores").html(
                    `<option value="">Seleccionar...</option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>`
                )
                .prop("disabled", false)
                .selectpicker("refresh");
            }else{
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


        }else if ($("#coverage_external_id").val() == 4){//Barniz UV -- no puede tener impresión de tiro y retiro

            /*$("#barniz_uv")
                .prop("disabled", true)
                .val("1")
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

            if( $("#impresion").val()==3 || $("#impresion").val()==1){
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
            }else{

                if($("#impresion").val() === '2'){
                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                        <option value="1"> 1 </option>
                        <option value="2"> 2 </option>
                        <option value="3"> 3 </option>
                        <option value="4"> 4 </option>`
                    )
                    .prop("disabled", false)
                    .selectpicker("refresh");
                }else{
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



        }else if( $("#coverage_external_id").val() == ''){// cuando esta vacio, se limpian los datos

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

                if($("#impresion").val() === '2'){
                    $("#numero_colores").html(
                        `<option value="">Seleccionar...</option>
                        <option value="1"> 1 </option>
                        <option value="2"> 2 </option>
                        <option value="3"> 3 </option>
                        <option value="4"> 4 </option>`
                    )
                    .prop("disabled", false)
                    .selectpicker("refresh");
                }else{
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

        }else{

            /*$("#barniz_uv")
                .prop("disabled", true)
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

            if($("#impresion").val() === '2'){
                $("#numero_colores").html(
                    `<option value="">Seleccionar...</option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>`
                )
                .prop("disabled", false)
                .selectpicker("refresh");
            }else{
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
                    .prop('required', false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            };
            if (numeroColores === "0" || numeroColores === "") {
                if($("#coverage_external_id").val()!=4){
                    desabilitarColores(
                        "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv"
                    );
                }else{
                    desabilitarColores(
                        "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6"
                    );
                    if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
                        $("#barniz_uv,#porcentanje_barniz_uv")
                            .prop("disabled", false)
                            .prop('required', true)
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    }else{
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
                        if($("#coverage_external_id").val()!=4){
                            desabilitarColores(
                                "#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv"
                            );
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
                                $("#color_1_id,#impresion_1")
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }else{
                                $("#color_1_id,#impresion_1")
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        }else{
                            desabilitarColores(
                                "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6"
                            );
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
                                $("#barniz_uv,#porcentanje_barniz_uv")
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }else{
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
                        if($("#coverage_external_id").val()!=4){
                            desabilitarColores(
                                "#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv"
                            );
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
                                $("#color_1_id,#impresion_1,#color_2_id,#impresion_2")
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }else{
                                $("#color_1_id,#impresion_1,#color_2_id,#impresion_2")
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        }else{
                            desabilitarColores(
                                "#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6"
                            );
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
                                $("#color_1_id,#impresion_1,#barniz_uv,#porcentanje_barniz_uv")
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }else{
                                $("#color_1_id,#impresion_1,#barniz_uv,#porcentanje_barniz_uv")
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        }

                        break;
                    case "3":
                        if($("#coverage_external_id").val()!=4){
                            desabilitarColores(
                                "#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv"
                            );
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }else{
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3"
                                )
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        }else{
                            desabilitarColores(
                                "#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6"
                            );
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#barniz_uv,#porcentanje_barniz_uv"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                                }else{
                                    $(
                                        "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#barniz_uv,#porcentanje_barniz_uv"
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
                        if($("#coverage_external_id").val()!=4){
                            desabilitarColores("#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv");
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }else{
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4"
                                )
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        }else{
                            desabilitarColores("#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6");
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#barniz_uv,#porcentanje_barniz_uv"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }else{
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#barniz_uv,#porcentanje_barniz_uv"
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

                        if($("#coverage_external_id").val()!=4){
                            desabilitarColores("#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv");
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }else{
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5"
                                )
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        }else{
                            desabilitarColores("#color_5_id,#impresion_5,#color_6_id,#impresion_6");
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#barniz_uv,#porcentanje_barniz_uv"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }else{
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#barniz_uv,#porcentanje_barniz_uv"
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
                        if($("#coverage_external_id").val()!=4){
                            desabilitarColores("#barniz_uv,#porcentanje_barniz_uv");
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }else{
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6"
                                )
                                    .prop("disabled", false)
                                    .prop('required', false)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }
                        }else{
                            desabilitarColores("#color_6_id,#impresion_6");
                            if (role != 3 && role != 4 && role != 5 && role != 6 && role != 18) {
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#barniz_uv,#porcentanje_barniz_uv"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }else{
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#barniz_uv,#porcentanje_barniz_uv"
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
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv"
                                )
                                    .prop("disabled", false)
                                    .prop('required', true)
                                    .selectpicker("refresh")
                                    .closest("div.form-group")
                                    .removeClass("error");
                            }else{
                                $(
                                    "#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#barniz_uv,#porcentanje_barniz_uv"
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

            if( $("#color_6_id").val() !== ''){

                /*$("#barniz_uv,#porcentanje_barniz_uv")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");*/

            }else if ($("#color_6_id").val() === ''){

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
                error: function(e) {
                    console.log(e.responseText);
                },
                async:true
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
    // $("#carton_color")
    //     .change(() => {
    //         var carton_color = $("#carton_color").val();

    //         return $.ajax({
    //             type: "GET",
    //             url: "/getCartonColor",
    //             data: "carton_color=" + carton_color,
    //             success: function (data) {
    //                 data = $.parseHTML(data);
    //                 $("#carton_id")
    //                     .empty()
    //                     .append(data)
    //                     .selectpicker("refresh");
    //             },
    //                error: function(e) {
    //                    console.log(e.responseText);
    //                },
    //                  async:true
    //         });
    // })
    // .triggerHandler("change");

    // Si no se selecciona un carton se puede selccionar un color de carton, si se selecciona un carton desabilitamos color carton
    // $("#carton_id")
    //     .change(() => {
    //         if ($("#carton_id").val()) {

    //             var carton_id = $("#carton_id").val();

    //             return $.ajax({
    //                 type: "GET",
    //                 url: "/getCarton",
    //                 data: "carton_id=" + carton_id,
    //                 success: function (data) {
    //                     $("#liner_exterior").val(data.liner_exterior);
    //                     $("#carton_color").selectpicker(
    //                         "val",
    //                         data.color_tapa_exterior == "CAFE" ? 1 : 2
    //                     );
    //                 },
    //             });
    //         } else {
    //             $("#carton_color")
    //                 .prop("disabled", false)
    //                 .selectpicker("refresh");
    //             $("#liner_exterior").val("");
    //         }

    //     })
    //     .triggerHandler("change");


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
            //}

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

        if($("#tipo_solicitud").val() == 1 || $("#tipo_solicitud").val() == 5 || $("#tipo_solicitud").val() == 7){

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
                    }else if ($("#pegado_terminacion").val() !== ''){
                        $("#pegado")
                            .prop("disabled", true)
                            .val("1")
                            .selectpicker("refresh")
                            .closest("div.form-group")
                            .removeClass("error");
                    }else{
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
           // alert(tipo_solicitud);
            if (tipo_solicitud == 1 || tipo_solicitud == 7) {
                $("#ot-tipo-solicitud").show();
                $("#ot-solicita").show();
                $("#ot-datos-cliente").show();
                $("#ot-caracteristicas").show();
                $("#ot-ingresos-principales").show();
                $("#ot-colores").show();
                $("#ot-medidas-interiores").show();
                $("#ot-medidas-exteriores").show();
                $("#ot-terminaciones").show();
                $("#ot-material").show();
                $("#ot-desarrollo").show();
                $("#ot-sentido-onda").show();

                // Bloqueo y limpieza de valores para los siguientes inputs
                disableAndCleanElements("#cad");
                // disableCadSelect();

                enableCadSelect();

                // Se desbloquean todos los checkbox
                cleanCheckboxs();

                // Desbloqueo y limpieza de valores para los siguientes inputs
                $(
                    "#reference_id,#bloqueo_referencia,#cinta,#items_set,#veces_item,#style_id,#recubrimiento_id,#numero_colores,#color_1_id,#color_2_id,#color_3_id,#color_4_id,#color_5_id,#color_6_id,#cera_exterior,#cera_interior,#barniz_interior,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#process_id,#pegado_terminacion,#armado_id,#tipo_sentido_onda,#peso_contenido_caja,#autosoportante,#envase_id,#cajas_altura,#pallet_sobre_pallet,#cantidad"
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

                // //Primero se deben cumplir estas nuevas condiciones
                // validacion_color_cera_barniz();

                //Se bloquean todos los campos de ingresos principales, menos el tipo de item, para poder comenzar a llenar uno por uno
                validacion_ingresos_principales();

                // if(role == 6){

                    //Validacion de impresion con numeros de colores
                    functionImpresion();
                // }

            }

        })
        .triggerHandler("change");

    //Cuando se crea la OT los campos de separación Golpes al Ancho y Largo se establecen en 0
    // $("#separacion_golpes_largo,#separacion_golpes_ancho")
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

        }else if($("#fsc").val() != 2 && $("#fsc").val() != ''){

            $("#pais_id")
                .prop("disabled", false)
                .selectpicker("refresh");
        } else {

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
            $("#tamano_pallet_type_id,#altura_pallet,#permite_sobresalir_carga,#bulto_zunchado,#formato_etiqueta,#etiquetas_pallet,#termocontraible")
                .prop("disabled", true)
                .val("")
                .selectpicker("refresh")
                .closest("div.form-group")
                .removeClass("error");
        } else {
            $("#tamano_pallet_type_id,#altura_pallet,#permite_sobresalir_carga,#bulto_zunchado,#formato_etiqueta,#etiquetas_pallet,#termocontraible")
                .prop("disabled", false)
                .selectpicker("refresh");
        }
    })
    .triggerHandler("change");


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

                // Para rayados hacemos una validacion especial
                // Si el CAD viene con datos en los 3 Rayados, se puedan dejar sólo en 0
                // Para ingresar un dato en los rayados, el CAD debe traer los 3 rayados NULL.
                setRayados(data);

                if (val == "" && ($("#tipo_solicitud").val() == 1 || $("#tipo_solicitud").val() == 7)) {
                    $(
                        "#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto"
                    ).prop("readonly", false);
                }
            },
            error: function(e) {
                console.log(e.responseText);
            },
            async:true
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
        /*if (role == 3 || role == 4) {
            $(`#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2`).prop({
                disabled: false,
                readonly: true,
            });
        }*/
    };

    var fileCorreoCliente=document.getElementById("file_check_correo_cliente");
    var filePlanoActual=document.getElementById("file_check_plano_actual");
    var fileBocetoActual=document.getElementById("file_check_boceto_actual");
    var fileSpeed=document.getElementById("file_check_speed");
    var fileOtro=document.getElementById("file_check_otro");
    var fileVbMuestra = document.getElementById("file_check_vb_muestra");
    var fileVbBoce = document.getElementById("file_check_vb_boce");
    // Habilita/inabilita la vista del campo de "numero de muestras" segun checklist de "muestra"
    $("#check_correo_cliente").click(function () {
        $("#upload_file_correo")[this.checked ? "show" : "hide"]();
        if(this.checked){
            fileCorreoCliente.click();
        }

    });
    $("#check_plano_actual").click(function () {
        $("#upload_file_plano")[this.checked ? "show" : "hide"]();
        if(this.checked){
            filePlanoActual.click();
        }
    });
    $("#check_boceto_actual").click(function () {
        $("#upload_file_boceto")[this.checked ? "show" : "hide"]();
        if(this.checked){
            fileBocetoActual.click();
        }
    });
    $("#check_speed").click(function () {
        $("#upload_file_speed")[this.checked ? "show" : "hide"]();
        if(this.checked){
            fileSpeed.click();
        }
    });
    $("#check_otro").click(function () {
        $("#upload_file_otro")[this.checked ? "show" : "hide"]();
        if(this.checked){
            fileOtro.click();
        }
    });

    $("#check_vb_muestra").click(function () {
        $("#upload_file_vb_muestra")[this.checked ? "show" : "hide"]();
        if (this.checked) {
            fileVbMuestra.click();
        }
    });

    $("#check_vb_boce").click(function () {
        $("#upload_file_vb_boce")[this.checked ? "show" : "hide"]();
        if (this.checked) {
            fileVbBoce.click();
        }
    });

    $("#check_conservar_si").click(function () {
        if(this.checked){
            $("#check_conservar_no").prop( "checked", false );
        }
    });
    $("#check_conservar_no").click(function () {
        if(this.checked){
            $("#check_conservar_si").prop( "checked", false );
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

    fileCorreoCliente.addEventListener('change', function(){
        $('#file_chosen_correo').attr('data-original-title', this.files[0].name);
        $('#file_chosen_correo').show();
        $('#file_chosen_correo').tooltip();
    })

    filePlanoActual.addEventListener('change', function(){
        $('#file_chosen_plano').attr('data-original-title', this.files[0].name);
        $('#file_chosen_plano').show();
        $('#file_chosen_plano').tooltip();
    })

    fileBocetoActual.addEventListener('change', function(){
        $('#file_chosen_boceto').attr('data-original-title', this.files[0].name);
        $('#file_chosen_boceto').show();
        $('#file_chosen_boceto').tooltip();
    })

    fileSpeed.addEventListener('change', function(){
        $('#file_chosen_speed').attr('data-original-title', this.files[0].name);
        $('#file_chosen_speed').show();
        $('#file_chosen_speed').tooltip();
    })

    fileOtro.addEventListener('change', function(){
        $('#file_chosen_otro').attr('data-original-title', this.files[0].name);
        $('#file_chosen_otro').show();
        $('#file_chosen_otro').tooltip();
    })

    fileVbMuestra.addEventListener('change', function () {
        $('#file_chosen_vb_muestra').attr('data-original-title', this.files[0].name);
        $('#file_chosen_vb_muestra').show();
        $('#file_chosen_vb_muestra').tooltip();
    })

    fileVbBoce.addEventListener('change', function () {
        $('#file_chosen_vb_boce').attr('data-original-title', this.files[0].name);
        $('#file_chosen_vb_boce').show();
        $('#file_chosen_vb_boce').tooltip();
    })

    //Validacion Restriccion de Paletizado solo para rol de vendedor
    if(role!=4 && role!=3  && role != 18){
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

          if($("#coverage_external_id").val() == 4){
              $("#porcentanje_barniz_uv")
                  .prop("disabled", true)
                  .val($("#percentage_coverage_external").val());

          }else{
              $("#porcentanje_barniz_uv")
                  .prop("disabled", true)
                  .val("");
          }

      })
      .triggerHandler("change");

    if(role == 3 || role == 4 || role == 5 || role == 6){//Validar roles vendedores
        //Manejo de Select Seccion DATOS PARA DESARROLLO
        $("#product_type_developing_id")
        .change(() => {

            var val = $("#product_type_developing_id").val();

            if(val==1){

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
            }else{
                if(val==3){

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
                }else{

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
    }else{
        if(role === 18){
            //Manejo de Select Seccion DATOS PARA DESARROLLO
            $("#product_type_developing_id")
            .change(() => {

                var val = $("#product_type_developing_id").val();
                if(val==1){

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
                }else{
                    if(val==3){

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
                    }else{

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


    /*$("#barniz_uv,#porcentanje_barniz_uv")
        .prop("disabled", true)
        .val("")
        .selectpicker("refresh")
        .closest("div.form-group")
        .removeClass("error");*/

    //Permitir edicion campos de rayado
    $("#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2")
        .prop("disabled", false);

    //Bloqueo de Pegado para rol de diseñador grafico
    if(role == 8){
        $("#pegado_terminacion")
        .prop("disabled", true)
        .val("")
        .selectpicker("refresh")
        .closest("div.form-group")
        .removeClass("error");
    }


    cargarDatos();


});

///Formula Mckee
$("#button_formula_mckee").click(function () {
    if($("#carton_id").val() != ''){
        $('#carton_id_mckee')
            .val($("#carton_id").val())
            .selectpicker('refresh');
    }else{
        $('#carton_id_mckee')
            .prop("disabled",true)
            .val('')
            .selectpicker('refresh');
    }
    $('#ancho_mckee,#alto_mckee,#perimetro_mckee,#ect_mckee,#espesor_mckee,#bct_kilos_mckee,#bct_lib_mckee')
        .prop("disabled",true)
        .val('');
    $('#largo_mckee')
        .val('')
        .focus();

    $("#seccion_combinabilidad").removeClass("show");

    $('#hc_combinabilidad,#formato_optimo,#numero_cortes,#perdida_minima,#perdida_minima_mm,#1750_combinabilidad,#1830_combinabilidad,#1900_combinabilidad,#1950_combinabilidad,#2040_combinabilidad,#2180_combinabilidad,#2250_combinabilidad,#2350_combinabilidad,#2450_combinabilidad,#2500_combinabilidad')
        .prop("disabled",true)
        .val('');
    $('#carton_id_combinabilidad')
        .val('')
        .selectpicker('refresh');

    $('#button_aplicar_mckee').prop("disabled",true);
});

$('#largo_mckee').change(function () {
    $('#ancho_mckee')
        .prop("disabled",false)
        .val('')
        .focus();
    $('#alto_mckee,#perimetro_mckee,#ect_mckee,#espesor_mckee,#bct_kilos_mckee,#bct_lib_mckee')
        .prop("disabled",true)
        .val('');
    if($("#carton_id_mckee").val() == ''){
        $('#carton_id_mckee')
            .prop("disabled",true)
            .val('')
            .selectpicker('refresh');
    }
});

$("#largo_mckee").keypress(function(e) {
    if (e.which == 13) {
        $("#largo_mckee" ).trigger("change");
        return false;
    }
});

$('#ancho_mckee').change(function () {
    $('#alto_mckee')
        .prop("disabled",false)
        .val('')
        .focus();
    $('#perimetro_mckee,#ect_mckee,#espesor_mckee,#bct_kilos_mckee,#bct_lib_mckee')
        .prop("disabled",true)
        .val('');
    if($("#carton_id_mckee").val() == ''){
        $('#carton_id_mckee')
            .prop("disabled",true)
            .val('')
            .selectpicker('refresh');
    }
});

$("#ancho_mckee").keypress(function(e) {
    if (e.which == 13) {
        $("#ancho_mckee" ).trigger("change");
        return false;
    }
});

$('#alto_mckee').change(function () {

    var largo = $('#largo_mckee').val();
    var ancho = $('#ancho_mckee').val();
    var perimetro=(parseInt(largo)+parseInt(ancho))*2;
    $('#perimetro_mckee')
        .prop("disabled",true)
        .val(perimetro);

    if($("#carton_id_mckee").val() != ''){
        $('#carton_id_mckee')
        .prop("disabled",false)
        .focus();
        $("#carton_id_mckee" ).trigger("change");
    }else{
        $('#carton_id_mckee')
        .prop("disabled",false)
        .val('')
        .selectpicker('refresh')
        .focus();
    }

});

$("#alto_mckee").keypress(function(e) {
    if (e.which == 13) {
        $("#alto_mckee" ).trigger("change");
        return false;
    }
});

$('#carton_id_mckee').change(function () {
    var val = $(this).val();
    if(val==$('#carton_id').val()){
        var ect     = parseInt(0);
        var espesor = parseFloat(0);
        var bct_kilos = parseFloat(0);
        var bct_lb  = parseFloat(0);
        var perimetro =  $('#perimetro_mckee').val();

        return $.ajax({
            type: "GET",
            url: "/getCarton",
            data: "carton_id=" + val,
            success: function (data) {
                if(data.ect_min!=null){
                    ect = parseInt(data.ect_min);
                }

                if(data.espesor!=null){
                    espesor = parseFloat(data.espesor);
                }

                bct_kilos =(parseFloat(0.325)*ect*(Math.pow((espesor-parseFloat(0.2)),parseFloat(0.508))))*(Math.pow((perimetro/parseInt(10)),parseFloat(0.492)));
                bct_lb  = bct_kilos/parseFloat(0.454);

                $('#ect_mckee').prop("disabled",true).val(ect);
                $('#espesor_mckee').prop("disabled",true).val(espesor);
                $('#bct_kilos_mckee').prop("disabled",true).val(bct_kilos.toFixed(0));
                $('#bct_lib_mckee').prop("disabled",true).val(bct_lb.toFixed(0));
                $('#button_aplicar_mckee').prop("disabled",false);

            },
            error: function(e) {
                console.log(e.responseText);
            },
            async:true
        });
    }else{
        alert("El cartón es diferente al de la Orden de Trabajo. Debe seleccionar el mismo para generar el cálculo");
        $('#button_aplicar_mckee').prop("disabled",true);
        $('#perimetro_mckee,#ect_mckee,#espesor_mckee,#bct_kilos_mckee,#bct_lib_mckee')
        .prop("disabled",true)
        .val('');

    }
});

$("#button_aplicar_mckee").click(function () {

    var bct_kilos   = $('#bct_kilos_mckee').val();
    var bct_lb      = $('#bct_lib_mckee').val();
    var ect         = $('#ect_mckee').val();
    var espesor     = $('#espesor_mckee').val();
    var carton      = $('#carton_id_mckee').val();
    var perimetro   = $('#perimetro_mckee').val();
    var alto        = $('#alto_mckee').val();
    var ancho       = $('#ancho_mckee').val();
    var largo       = $('#largo_mckee').val();
    var d           = new Date();

    const dt = new Date();
    const padL = (nr, len = 2, chr = `0`) => `${nr}`.padStart(2, chr);

    var fecha= `${padL(dt.getDate())}-${padL(dt.getMonth()+1)}-${dt.getFullYear()}
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

    if($("#carton_id").val() != ''){
        $('#formato_optimo,#numero_cortes,#perdida_minima,#perdida_minima_mm,#1750_combinabilidad,#1830_combinabilidad,#1900_combinabilidad,#1950_combinabilidad,#2040_combinabilidad,#2180_combinabilidad,#2250_combinabilidad,#2350_combinabilidad,#2450_combinabilidad,#2500_combinabilidad')
            .prop("disabled",true)
            .val('');
        $('#carton_id_combinabilidad')
            .val($("#carton_id").val())
            .selectpicker('refresh');

    }else{
        $('#hc_combinabilidad,#formato_optimo,#numero_cortes,#perdida_minima,#perdida_minima_mm,#1750_combinabilidad,#1830_combinabilidad,#1900_combinabilidad,#1950_combinabilidad,#2040_combinabilidad,#2180_combinabilidad,#2250_combinabilidad,#2350_combinabilidad,#2450_combinabilidad,#2500_combinabilidad')
            .prop("disabled",true)
            .val('');
    }


    $("#seccion_formula_mckee").removeClass("show");
});

$('#carton_id_combinabilidad').change(function () {
    var val = $(this).val();

    if(val==$('#carton_id').val()){
        $('#carton_combinabilidad_select').removeClass("error");
        $('#hc_combinabilidad')
        .prop("disabled",false)
        .val('');
    }else{
        alert("El cartón es diferente al de la Orden de Trabajo. Debe seleccionar el mismo para generar el cálculo");
        $('#hc_combinabilidad,#formato_optimo,#numero_cortes,#perdida_minima,#perdida_minima_mm,#1750_combinabilidad,#1830_combinabilidad,#1900_combinabilidad,#1950_combinabilidad,#2040_combinabilidad,#2180_combinabilidad,#2250_combinabilidad,#2350_combinabilidad,#2450_combinabilidad,#2500_combinabilidad')
            .prop("disabled",true)
            .val('');

    }

});

$('#hc_combinabilidad').change(function () {

    $('#formato_optimo,#numero_cortes,#perdida_minima,#perdida_minima_mm,#1750_combinabilidad,#1830_combinabilidad,#1900_combinabilidad,#1950_combinabilidad,#2040_combinabilidad,#2180_combinabilidad,#2250_combinabilidad,#2350_combinabilidad,#2450_combinabilidad,#2500_combinabilidad')
        .prop("disabled",true)
        .val('');

    var hc              = $(this).val();
    var val             = $('#carton_id_combinabilidad').val();
    var combinabilidad  = '';
    var result          = parseInt(0);
    var result_mm       = parseInt(0);
    var min             = parseInt(101);
    var min_mm          = parseInt(1001);
    var formato_optimo  = '';
    var numero_cortes   = parseInt(0);

    return $.ajax({
        type: "GET",
        url: "/getCarton",
        data: "carton_id=" + val,

        success: function (data) {

            combinabilidad = data.combinabilidad.split(',');

            for (i = 0; i < combinabilidad.length; i++) {

                result=(1-(Math.trunc(Math.trunc(parseInt(combinabilidad[i])-30)/parseFloat(hc))*(parseFloat(hc)/parseInt(combinabilidad[i]))))*100;
                $('#'+combinabilidad[i]+'_combinabilidad').val(result.toFixed(0)+"%");

                if(min>result){
                    min=result.toFixed(0);
                    formato_optimo=combinabilidad[i];
                }

                result_mm=(parseInt(combinabilidad[i])-30)-(Math.trunc((parseInt(combinabilidad[i])-30)/parseFloat(hc)))*parseFloat(hc);

                if(min_mm>result_mm){
                    min_mm=result_mm.toFixed(0);
                }
            }

            numero_cortes=parseInt(formato_optimo)/parseFloat(hc);

            $('#perdida_minima').val(min+"%");
            $('#perdida_minima_mm').val(min_mm);
            $('#formato_optimo').val(formato_optimo);
            $('#numero_cortes').val(numero_cortes.toFixed(0));

        },
        error: function(e) {
            console.log(e.responseText);
        },
        async:true
    });
});

$("#hc_combinabilidad").keypress(function(e) {
    if (e.which == 13) {
        $("#hc_combinabilidad" ).trigger("change");
        return false;
    }
});
///Fin Formula Analisis Anchura

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


