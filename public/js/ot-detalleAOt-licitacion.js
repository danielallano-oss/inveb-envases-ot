$(document).ready(function () {
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    // MASCARAS NUMERICAS
    /*const volumenMask = IMask(volumen_venta_anual, thousandsOptions);
    const usdMask = IMask(usd, thousandsOptions);
    var areaProductoMask = IMask(area_producto, fourDecimalsOptions);
    var recorteAdicionalMask = IMask(recorte_adicional, cuatroDecimalsOptions);*/
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


    const disableAndCleanElements = (elements) => {
        toggleAndCleanElements(elements, true);
    };

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



    cargarDatos();


});

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

}


