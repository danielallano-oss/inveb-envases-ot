$(document).ready(function () {
    var formCargaMasiva = $("#form-carga-masiva");
    var cotizacion_id = $("#cotizacion_id");
    // Ajax on click para calcular resultados
    $("#form-carga-material").on("submit", function (e) {
        console.log("enviardo csv");
        e.preventDefault();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/cotizador/cargaMateriales",
            data: formulario,
            processData: false,
            contentType: false,
            cache: false,
            success: function (data) {
                console.log(data);
                notify("Carga Exitosa de Materiales", "success");
                listarResultadosMaterialesCargados(data.materiales);
                // $("#cotizacion_id").val(cotizacion.id);
            },
            error: function (err) {
                // console.log(err.responseJSON.mensaje);
                notify(err.responseJSON.mensaje, "danger");

                if (err.status == 422) {
                }
            },
        });
    });
    // FIN calculo de resultados
});

var tablaMaterialesCargados = $("#materiales-carga tbody");
function listarResultadosMaterialesCargados(materiales) {
    // debugger;
    // console.log(detalles);
    var materialesID = [];
    var listadoMateriales = materiales
        .map(function (material) {
            materialesID.push(material.id);
            // console.log("creado material " + material.id);
            // let tipo_material = material.tipo_material_id;
            // let area_hc, carton, proceso;
            // switch (tipo_material) {
            //     case 1:
            //         area_hc = redondeo(material.area_hc);
            //         carton = material.carton.codigo;
            //         proceso = material.proceso.descripcion;
            //         pegado =
            //             material.pegado_terminacion != null
            //                 ? { 0: "NO", 1: "SI" }[material.pegado_terminacion]
            //                 : "";
            //         golpes_ancho = material.golpes_ancho;
            //         golpes_largo = material.golpes_largo;
            //         impresion = material.impresion + "%";
            //         porcentaje_cera =
            //             material.porcentaje_cera_interno +
            //             material.porcentaje_cera_externo +
            //             "%";
            //         break;
            //     case 2:
            //         area_hc = "";
            //         carton = material.carton_esquinero.codigo;
            //         proceso = "";
            //         pegado = "";
            //         golpes_ancho = "";
            //         golpes_largo = "";
            //         impresion = "";
            //         porcentaje_cera = "";
            //         break;
            //     default:
            //         break;
            // }

            // <td>${pegado}</td>
            return `<tr>
                <td>${material.descripcion}</td>
                <td>${material.codigo}</td>
                <td>${material.carton ? material.carton.codigo : ""}</td>
                <td>${material.cad.cad}</td>
                <td>${material.style ? material.style.glosa : ""}</td>
                <td>${
                    material.product_type
                        ? material.product_type.descripcion
                        : ""
                }</td>
                <td>
                <a href="#" class="sincronizarMaterial" data-id="${
                    material.id
                }" >
                <div class="material-icons md-14" data-toggle="tooltip" title="Guardar">save</div>
                </a>
                </td>
                </tr>`;
        })
        .join("");
    tablaMaterialesCargados.html(listadoMateriales);
    $("#resultados-carga-materiales").show();
    $("#total-materiales-carga").html(materiales.length);

    if (materiales.length > 0) {
        window.materialesID = materialesID;
        window.materialesCargados = materiales;
    }
}

var cotizacion_id = $("#cotizacion_id");
$(document).on("click", ".sincronizarMaterial", function (e) {
    e.preventDefault();

    // console.log($(this).attr("data-id"));
    let material_id = $(this).attr("data-id");
    // Segun el id seleccionado encontramos el material a cargar en el formulario de detalle
    var indice_material = window.materialesCargados.findIndex(
        (material) => material.id == material_id
    );
    let material = window.materialesCargados[indice_material];
    cargarMaterialADetalle(material);
    notify("Material Sincronizados", "success");
    limpiarCargaMateriales();
    // toggleResultados();
    $("#modal-carga-material").modal("toggle");
});

function limpiarCargaMateriales() {
    $("#codigo_material").val("");
    $("#descripcion_material").val("");
    $("#cad").val("");
    $("#style_id").selectpicker("val", "");

    $("#resultados-carga-materiales").hide();
}

function cargarMaterialADetalle(material) {
    console.log(material);
    if (material.carton_id) {
        $("#carton_id").val(material.carton_id).selectpicker("refresh");
    }
    if (material.product_type_id) {
        $("#product_type_id")
            .val(material.product_type_id)
            .selectpicker("refresh");
    }
    $("#numero_colores").val(material.numero_colores).selectpicker("refresh");
    $("#codigo_material_detalle").val(material.codigo);
    $("#descripcion_material_detalle").val(material.descripcion);
    $("#material_id").val(material.id);
    $("#cad_material_detalle").val(material.cad.cad);
    $("#cad_material_id").val(material.cad.id);
    // $("#area_hc").val(parseFloat().toFixed(3));
    $("#anchura").val(material.cad.anchura_hm);
    $("#largura").val(material.cad.largura_hm);
    $("#golpes_largo").val(material.golpes_largo);
    $("#golpes_ancho").val(material.golpes_ancho);
    $("#bct_min_lb").val(material.bct_min_lb);
    $("#bct_min_kg").val(material.bct_min_kg).selectpicker("refresh");
    if ($("#tipo_medida").val() != 2) {
        $("#ancho").val(material.cad.interno_ancho);
        $("#alto").val(material.cad.interno_alto);
        $("#largo").val(material.cad.interno_largo);
    } else {
        $("#ancho").val(material.cad.externo_ancho);
        $("#alto").val(material.cad.externo_alto);
        $("#largo").val(material.cad.externo_largo);
    } 
    $("#interno_ancho_med").val(material.cad.interno_ancho);
    $("#interno_alto_med").val(material.cad.interno_alto);
    $("#interno_largo_med").val(material.cad.interno_largo);
    $("#externo_ancho_med").val(material.cad.externo_ancho);
    $("#externo_alto_med").val(material.cad.externo_alto);
    $("#externo_largo_med").val(material.cad.externo_largo); 
    // FORMULA DE AREA HC
    if (material && material.area_hc) {
        $("#area_hc").val(parseFloat(material.area_hc).toFixed(3));
        console.log("areahc", areahc);
    }
}
