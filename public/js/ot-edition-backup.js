$(document).ready(function() {
    const role = $("#role_id").val();

    // Al comienzo los select de jerarquia 2 y 3 comienzan desabilitados y luego son habilitados al cambiar la jerarquia 1

    $("#subhierarchy_id,#subsubhierarchy_id").prop("disabled", true);
    // ajax para relacionar jerarquia 2 con la jerarquia 1 seleccionada
    $("#hierarchy_id").change(function() {
        var val = $(this).val();
        return $.ajax({
            type: "GET",
            url: "../../getJerarquia2",
            data: "hierarchy_id=" + val +"&jerarquia2=" +$("#jerarquia2").val(),
            success: function(data) {
                data = $.parseHTML(data);
                $("#subhierarchy_id")
                    .empty()
                    .append(data);
                if (role == 4) {
                    $("#hierarchy_id").prop("disabled", false);
                    $("#subhierarchy_id").prop("disabled", false);
                }
                $("#subsubhierarchy_id")
                    .empty()
                    .append(
                        $.parseHTML(
                            '<option value="" disabled selected>Seleccionar Opción</option>'
                        )
                    )
                    .prop("disabled", true);
            }
        });
    });

    // ajax para relacionar jerarquia 3 con la jerarquia 2 seleccionada
    $("#subhierarchy_id").change(function() {
        var val = $(this).val();
        return $.ajax({
            type: "GET",
            url: "../../getJerarquia3",
            data: "subhierarchy_id=" + val +"&jerarquia3=" +$("#jerarquia3").val(),
            success: function(data) {
                data = $.parseHTML(data);
                $("#subsubhierarchy_id")
                    .empty()
                    .append(data);
                if (role == 4) {
                    $("#subsubhierarchy_id").prop("disabled", false);
                }
            }
        });
    });

    // Popular jerarquias en orden
    const populateHierarchies = async () => {
        await $("#hierarchy_id")
            .val($("#jerarquia1").val())
            .triggerHandler("change");
        await $("#subhierarchy_id")
            .val($("#jerarquia2").val())
            .triggerHandler("change");
        $("#subsubhierarchy_id").val($("#jerarquia3").val());
    };
    populateHierarchies();

    // DESABILITAR CAMPOS SEGUN ROL
    // Area de Venta
    if (role == 4 || role == 3) {
        $(
            "#largura_hm,#anchura_hm,#area_producto,#area_interior_perimetro,#rmt,#golpes_largo,#golpes_ancho,#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2,#impresion_1,#impresion_2,#impresion_3,#impresion_4,#impresion_5,#impresion_6,#longitud_pegado,#porcentaje_cera_exterior,#porcentaje_cera_interior,#porcentaje_barniz_interior,#proceso,#pegado_terminacion,#armado,#tipo_sentido_onda,#material_asignado"
        ).prop("disabled", true);
    }
    // Area desarrollo
    if (role == 5 || role == 6) {
        $("input,select,textarea").prop("disabled", true);
        $(
            "#cad_id,#tipo_item_id,#items_set,#veces_item,#carton_id,#style_id,#largura_hm,#anchura_hm,#area_producto,#recubrimiento_id,#rmt,#golpes_largo,#golpes_ancho,#rayado_c1r1,#rayado_r1_r2,#rayado_r2_c2,#color_1_id,#impresion_1,#color_2_id,#impresion_2,#color_3_id,#impresion_3,#color_4_id,#impresion_4,#color_5_id,#impresion_5,#color_6_id,#impresion_6,#pegado,#longitud_pegado,#cera_exterior,#porcentaje_cera_exterior,#cera_interior,#porcentaje_cera_interior,#barniz_interior,#porcentaje_barniz_interior,#interno_largo,#interno_ancho,#interno_alto,#externo_largo,#externo_ancho,#externo_alto,#proceso,#pegado_terminacion,#armado,#tipo_sentido_onda"
        ).prop("disabled", false);
        // habilita el method put y el token csrf
        $('input[name="_method"],input[name="_token"]').prop("disabled", false);
    }
    // Area catalogacion
    if (role == 11 || role == 12) {
        $("input,select,textarea").prop("disabled", true);
        // habilita el method put y el token csrf
        $('#material_asignado,input[name="_method"],input[name="_token"]').prop(
            "disabled",
            false
        );
    }

    // Si el tipo de referencia es 0 => "SIN REFERENCIA" se bloquean la referencia y el bloqueo referencia

    $("#reference_type_id").change(() => {
        if ($("#reference_type_id").val() == 0) {
            $("#reference_id,#bloqueo_referencia").prop("disabled", true);
            $("#reference_id,#bloqueo_referencia").val("");
        } else {
            if (role == 4) {
                $("#reference_id,#bloqueo_referencia").prop("disabled", false);
            }
        }
    });
    $("#reference_type_id").change();
    // cantidad comiensa inabilitado y luego si se selecciona "SI" en pallet este es habilitado
    $("#pallet_sobre_pallet")
        .change(() => {
            if ($("#pallet_sobre_pallet").val() != 1) {
                $("#cantidad").prop("disabled", true);
                $("#cantidad").val("");
            } else {
                if (role == 4) {
                    $("#cantidad").prop("disabled", false);
                }
            }
        })
        .triggerHandler("change");
});
