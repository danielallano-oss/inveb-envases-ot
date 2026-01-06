$(document).ready(function () {

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

    $('#cantidad_entregadas_algunas').prop({disabled: true});

    // console.log('muestras111');
      // Habilita/inabilita la vista del campo de "numero de muestras" segun checklist de "muestra"
    //   $("#muestra").click(function () {
    //     console.log('aaaaaa');
    //     $("#container-numero-muetras")[this.checked ? "show" : "hide"]();
    //     $(".marcas-aprobaciones")[this.checked ? "show" : "hide"]();
    // });
      // Habilita/inabilita la vista del campo de "numero de muestras" segun checklist de "muestra"
      $("#muestra").click(function () {
        if (this.checked) $("#crear_muestra").click();
        $("#container-numero-muetras")[this.checked ? "show" : "hide"]();
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
    // $("#muestra").prop("disabled", true).prop("readonly", true);


    // const cleanCheckboxs = () => {
    //     $(".custom-control-input")
    //         .prop("disabled", false)
    //         .prop("checked", false);
    //     $("#muestra").prop("checked", false).triggerHandler("click");
    // };

});

$("#check_entregadas_todas").click(function () {
    if(this.checked){
        $("#check_entregadas_algunas").prop( "checked", false );
        $('#cantidad_entregadas_algunas').prop({disabled: true});
        $('#cantidad_entregadas_algunas').val('');
        $('#div_cantidad_muestras_entregadas').removeClass("error")
    }else{
        $('#div_cantidad_muestras_entregadas').removeClass("error")
    }
});

$("#check_entregadas_algunas").click(function () {
    if(this.checked){
        $("#check_entregadas_todas").prop( "checked", false );
        $('#cantidad_entregadas_algunas').prop({disabled: false});
        $('#div_cantidad_muestras_entregadas').removeClass("error")
    }else{
        $('#cantidad_entregadas_algunas').prop({disabled: true});
        $('#cantidad_entregadas_algunas').val('');
        $('#div_cantidad_entregadas_algunas').removeClass("error");
    }
});

$("#cantidad_item_licitacion").change(function () {
    var val = $(this).val();
    $("#cantidad_entregadas_algunas").attr({
        "max" : val,        // substitute your own
        "min" : 1          // values (or variables) here
    });
});





