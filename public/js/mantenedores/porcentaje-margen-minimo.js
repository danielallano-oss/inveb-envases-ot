$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-porcentajes-margenes").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/porcentajes_margenes_minimos/uploading",
            data: formulario,
            processData: false,
            contentType: false,
            cache: false,
            success: function (data) {
                $("#loading").hide(); // hide ajax loader
                // Si se proceso la carga totalmente solo refrescamos
                if (data.url && data.url == "redirect") {
                    window.location.href = window.location.href;
                    return;
                }
                console.log(data);

                // Si la carga es exitosa modificamos el boton de carga parta que sea el de confgirmacion
                $("#btn-cargar-porcentajes-margenes")
                    .html("Confirmar Carga")
                    .addClass("btn-lg");
                $("#proceso").val("cargaCompleta");

                notify("Procesamiento de datos Exitoso", "success");
                $("#container-boton-carga")
                    .removeClass("col-2")
                    .addClass("col-12 text-center");
                $("#container-file-carga").hide();
                $("#reload").show();
                $("#important-message").show();
                // #faffa8 amarillo
                let porcentajes_margenes_nuevos = data.porcentajes_margenes;
                let porcentajes_margenes_actualizados = data.porcentajes_margenes_actualizados;
                let porcentajes_margenes_erroneos = data.porcentajes_margenes_error;

                let count_porcentajes_margenes_nuevos = 0;
                let count_porcentajes_margenes_actualizados = 0;
                //let count_porcentajes_margenes_inactivados = 0;
                let count_porcentajes_margenes_erroneos = 0;

                let idsPorcentajesMargenesActualizdos = porcentajes_margenes_actualizados.map(function (
                    margen
                ) {
                    count_porcentajes_margenes_actualizados++;
                    $("#porcentaje-margen-row-" + margen.id).css(
                        "background-color",
                        "#faffa8"
                    );
                    return margen.id;
                });               

                console.log(idsPorcentajesMargenesActualizdos);

                let listadoPorcentajesMargenesNuevos = porcentajes_margenes_nuevos.map(function (margen) {
                    count_porcentajes_margenes_nuevos++;
                    let newPorcentajeMargen = `<tr style="background-color:#b4f3b1">
                    <td></td>
                    <td>${margen.rubro.descripcion}</td>
                    <td>${margen.clasificacion.name}</td>
                    <td>${margen.bruto_esperado}</td>
                    <td>${margen.servir_esperado}</td>
                    <td>${margen.ebitda_esperado}</td>
                    </tr>`;
                        console.log(margen.id, margen.id - 1);
                        if (
                            margen.id &&
                            $("#porcentaje-margen-row-" + (margen.id + 1)) > 0
                        ) {
                            $("#porcentaje-margen-row-" + (margen.id + 1)).after(
                                newMargen
                            );
                        } else {
                            $("#listadoPorcentajesMargenesMinimosMasivos").append(newMargen);
                        }
                        return;
                })
                    .join("");
                console.log(listadoPorcentajesMargenesNuevos);
                // $('#listadoPapelesMasivos').append(listadoPapelesNuevos);
                listadoPorcentajesMargenesErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
                
                
               <td  colspan="5">Margenes con Errores</td>
               </tr>`;
                listadoPorcentajesMargenesErroneos += porcentajes_margenes_erroneos
                    .map(function (margen) {
                        count_porcentajes_margenes_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="2">Linea Excel #${margen.linea}</td>
               <td colspan="3">${margen.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (porcentajes_margenes_erroneos.length > 0) {
                    $("#listadoPorcentajesMargenesMinimosMasivos").append(listadoPorcentajesMargenesErroneos);
                }
                console.log(count_porcentajes_margenes_nuevos);
                $("#totalPorcentajesMargenes").html(
                    +$("#totalPorcentajesMargenes").html() +
                        count_porcentajes_margenes_nuevos
                );
                $("#porcentajesMargenesNuevos").html(count_porcentajes_margenes_nuevos);
                $("#porcentajesMargenesActualizados").html(count_porcentajes_margenes_actualizados);
                //$("#margenesInactivados").html(count_porcentajes_margenes_inactivados);
                $("#porcentajesMargenesErroneos").html(count_porcentajes_margenes_erroneos);
            },
            error: function (err) {
                // console.log(err.responseJSON.mensaje);
                // notify(err.responseJSON.mensaje, "danger");

                if (err.status == 422) {
                }
            },
        });
    });
    // FIN calculo de resultados

    $("#archivo").on("click", function (e) {
        $(this).change();
        if (
            !confirm(
                "IMPORTANTE: Recuerde que se recomienda utilizar la ultima version del archivo antes de editarlo"
            )
        ) {
            e.preventDefault();
        }
        // $("#btn-cargar-papeles").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-porcentajes-margenes").prop("disabled", true);
        } else {
            $("#btn-cargar-porcentajes-margenes").prop("disabled", false);
        }
    });
});

// button "GO TO TOP"
//Get the button:
mybutton = document.getElementById("myBtn");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
    scrollFunction();
};

function scrollFunction() {
    if (
        document.body.scrollTop > 240 ||
        document.documentElement.scrollTop > 240
    ) {
        mybutton.style.display = "block";
        document.getElementById("legenda").style.display = "block";
    } else {
        mybutton.style.display = "none";
        document.getElementById("legenda").style.display = "none";
    }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}

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
