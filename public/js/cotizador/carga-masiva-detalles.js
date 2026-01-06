$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    var formCargaMasiva = $("#form-carga-masiva");
    var cotizacion_id = $("#cotizacion_id");
    // Ajax on click para calcular resultados
    $("#form-carga-masiva").on("submit", function (e) {
        //console.log("enviardo csv");
        e.preventDefault();
        // loading gif
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/cotizador/cargaMasivaDetalles",
            data: formulario,
            processData: false,
            contentType: false,
            cache: false,
            success: function (data) {
                //console.log(data);
                $("#loading").hide(); // hide ajax loader
                notify("Carga Exitosa de Detalles", "success");
                listarResultadosCargaMasiva(data.detalles);
                listarErroresCargaMasiva(data.detallesInvalidos);
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

var tablaDetallesCargados = $("#detalles-carga tbody");
var tablaDetallesInvalidos = $("#detalles-invalidos-carga tbody");
function listarResultadosCargaMasiva(detalles) {
    // debugger;
    // console.log(detalles);
    var detallesID = [];
    var listadoDetalles = detalles
        .map(function (detalle) {
            detallesID.push(detalle.id);
            // console.log("creado detalle " + detalle.id);
            let tipo_detalle = detalle.tipo_detalle_id;
            let area_hc, carton, proceso;
            switch (tipo_detalle) { 
                case 1:
                    area_hc = redondeo(detalle.area_hc);
                    carton = detalle.carton.codigo;
                    proceso = detalle.proceso.descripcion;
                    pegado =
                        detalle.pegado_terminacion != null
                            ? { 0: "NO", 1: "SI" }[detalle.pegado_terminacion]
                            : "";
                    golpes_ancho = detalle.golpes_ancho;
                    golpes_largo = detalle.golpes_largo;
                    impresion = detalle.impresion + "%";
                    porcentaje_cera =
                        detalle.porcentaje_cera_interno +
                        detalle.porcentaje_cera_externo +
                        "%";
                    break;
                case 2:
                    area_hc = "";
                    carton = detalle.carton_esquinero.codigo;
                    proceso = "";
                    pegado = "";
                    golpes_ancho = "";
                    golpes_largo = "";
                    impresion = "";
                    porcentaje_cera = "";
                    break;
                default:
                    break;
            }

            // <td>${pegado}</td>
            return `<tr>
                <td>${
                    detalle.tipo_detalle_id
                        ? {
                              1: "Corrug.",
                              2: "Esq.",
                              3: "Offset",
                              4: "Pulpa",
                          }[detalle.tipo_detalle_id]
                        : ""
                }</td>
                <td>${separadorMiles(detalle.cantidad)}</td>
                <td>${area_hc}</td>
                <td>${carton}</td>
                <td>${detalle.product_type.descripcion}</td>
                <td>${proceso}</td>
                <td>${golpes_ancho}</td>
                <td>${golpes_largo}</td>
                
                <td>${detalle.numero_colores}</td>
                <td>${impresion}</td>
                <td>${porcentaje_cera}</td>
                
                <td>${
                    detalle.matriz != null
                        ? { 0: "NO", 1: "SI" }[detalle.matriz]
                        : ""
                }</td>
                <td>${
                    detalle.clisse != null
                        ? { 0: "NO", 1: "SI" }[detalle.clisse]
                        : ""
                }</td>
                <td>${
                    detalle.royalty != null
                        ? { 0: "NO", 1: "SI" }[detalle.royalty]
                        : ""
                }</td>
                <td>${
                    detalle.maquila != null
                        ? { 0: "NO", 1: "SI" }[detalle.maquila]
                        : ""
                }</td>
                <td>${
                    detalle.armado_automatico != null
                        ? { 0: "NO", 1: "SI" }[detalle.armado_automatico]
                        : ""
                }</td>
                </tr>`;
        })
        .join("");
    tablaDetallesCargados.html(listadoDetalles);
    $("#resultados-carga-masiva").show();
    $("#total-detalles-carga").html(detalles.length);

    if (detalles.length > 0) {
        window.detallesID = detallesID;
        window.detallesCargaMasiva = detalles;
    }
}

function listarErroresCargaMasiva(detalles) {
    // debugger;
    // console.log(detalles);
    var listadoDetalles = detalles
        .map(function (detalle) {
            return `<tr>
               
                <td>${detalle.linea}</td>
                <td>${detalle.motivos}</td>
                
                </tr>`;
        })
        .join("");
    tablaDetallesInvalidos.html(listadoDetalles);
    $("#total-detalles-invalidos-carga").html(detalles.length);
}

var cotizacion_id = $("#cotizacion_id");
$("#sincronizarDetalles").on("click", function (e) {
    e.preventDefault();

    if (window.detallesCargaMasiva.length < 1) {
        notify("No hay detalles para sincronizar", "warning");
        return;
    }
    window.detalles_cotizaciones.push(...window.detallesCargaMasiva);
    renderTable();

    notify(
        "Detalles Sincronizados, Recuerde actualizar la cotizacion",
        "success"
    );
    limpiarCargaMasiva();
    toggleResultados();
    $("#modal-carga-masiva-detalles").modal("toggle");
});

function limpiarCargaMasiva() {
    $("#archivo").val("");
    $("#resultados-carga-masiva").hide();
}
