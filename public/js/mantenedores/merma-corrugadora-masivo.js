$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-mermas-corrugadoras").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();
        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/mermas_corrugadoras/uploading",
            data: formulario,
            processData: false,
            contentType: false,
            cache: false,
            success: function (data) {
                console.log(data);
                $("#loading").hide(); // hide ajax loader
                // Si se proceso la carga totalmente solo refrescamos
                if (data.url && data.url == "redirect") {
                    window.location.href = window.location.href;
                    return;
                }
                // Si la carga es exitosa modificamos el boton de carga parta que sea el de confgirmacion
                $("#btn-cargar-mermasCorrugadoras")
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
                let mermasCorrugadoras_nuevos = data.mermasCorrugadoras;
                let mermasCorrugadoras_actualizados =
                    data.mermasCorrugadoras_actualizados;
                let mermasCorrugadoras_inactivados =
                    data.mermasCorrugadoras_inactivados;
                let mermasCorrugadoras_erroneos = data.mermasCorrugadoras_error;

                let count_mermasCorrugadoras_nuevos = 0;
                let count_mermasCorrugadoras_actualizados = 0;
                let count_mermasCorrugadoras_inactivados = 0;
                let count_mermasCorrugadoras_erroneos = 0;

                let idsMermasCorrugadorasActualizdos =
                    mermasCorrugadoras_actualizados.map(function (merma) {
                        count_mermasCorrugadoras_actualizados++;
                        $("#mermasCorrugadoras-row-" + merma.id).css(
                            "background-color",
                            "#faffa8"
                        );
                        return merma.id;
                    });

                let idsMermasCorrugadorasInactivados =
                    mermasCorrugadoras_inactivados.map(function (merma) {
                        count_mermasCorrugadoras_inactivados++;
                        $("#mermasCorrugadoras-row-" + merma.id).css(
                            "background-color",
                            "#ef5350"
                        );
                        return merma.id;
                    });

                console.log(idsMermasCorrugadorasActualizdos);

                let listadoMermasCorrugadorasNuevos = mermasCorrugadoras_nuevos
                    .map(function (mermasCorrugadoras) {
                        count_mermasCorrugadoras_nuevos++;
                        let newMermaCorrugadora = `<tr style="background-color:#b4f3b1">
                <td></td>
                <td>${mermasCorrugadoras.porcentaje_merma_corrugadora}</td>
                <td>${mermasCorrugadoras.planta_id}</td>
                <td>${mermasCorrugadoras.carton_id}</td>
                </tr>`;
                        console.log(
                            mermasCorrugadoras.orden,
                            mermasCorrugadoras.orden - 1
                        );
                        if (
                            mermasCorrugadoras.orden &&
                            $(
                                "#mermasCorrugadoras-row-" +
                                    (mermasCorrugadoras.orden + 1)
                            ) > 0
                        ) {
                            $(
                                "#mermasCorrugadoras-row-" +
                                    (mermasCorrugadoras.orden + 1)
                            ).after(newMermaCorrugadora);
                        } else {
                            $("#listadoMermasCorrugadorasMasivos").append(
                                newMermaCorrugadora
                            );
                        }

                        return;
                    })
                    .join("");

                // $('#listadoMermasCorrugadorasMasivos').append(listadoMermasCorrugadorasNuevos);
                listadoMermasCorrugadorasErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
               
               <td  colspan="4">Mermas Corrugadoras con Errores</td>
               </tr>`;
                listadoMermasCorrugadorasErroneos += mermasCorrugadoras_erroneos
                    .map(function (merma) {
                        count_mermasCorrugadoras_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="1">Linea Excel #${merma.linea}</td>
               <td colspan="3">${merma.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (mermasCorrugadoras_erroneos.length > 0) {
                    $("#listadoMermasCorrugadorasMasivos").append(
                        listadoMermasCorrugadorasErroneos
                    );
                }

                $("#totalMermasCorrugadoras").html(
                    +$("#totalMermasCorrugadoras").html() +
                        count_mermasCorrugadoras_nuevos -
                        count_mermasCorrugadoras_inactivados
                );
                $("#mermasCorrugadorasNuevos").html(
                    count_mermasCorrugadoras_nuevos
                );
                $("#mermasCorrugadorasActualizados").html(
                    count_mermasCorrugadoras_actualizados
                );
                $("#mermasCorrugadorasInactivados").html(
                    count_mermasCorrugadoras_inactivados
                );
                $("#mermasCorrugadorasErroneos").html(
                    count_mermasCorrugadoras_erroneos
                );
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
        // $("#btn-cargar-mermasCorrugadoras").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-mermasCorrugadoras").prop("disabled", true);
        } else {
            $("#btn-cargar-mermasCorrugadoras").prop("disabled", false);
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
