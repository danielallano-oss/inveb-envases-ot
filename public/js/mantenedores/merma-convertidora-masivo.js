$(document).ready(function () {
    // window.onload = function() {
    document.getElementById("loading").style.display = "none";
    // }
    // Ajax on click para calcular resultados
    $("#form-carga-mermas-convertidoras").on("submit", function (e) {
        // cargamos css loader "loading"

        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/mermas_convertidoras/uploading",
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
                $("#btn-cargar-mermasConvertidoras")
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
                let mermasConvertidoras_nuevos = data.mermasConvertidoras;
                let mermasConvertidoras_actualizados =
                    data.mermasConvertidoras_actualizados;
                let mermasConvertidoras_inactivados =
                    data.mermasConvertidoras_inactivados;
                let mermasConvertidoras_erroneos =
                    data.mermasConvertidoras_error;

                let count_mermasConvertidoras_nuevos = 0;
                let count_mermasConvertidoras_actualizados = 0;
                let count_mermasConvertidoras_inactivados = 0;
                let count_mermasConvertidoras_erroneos = 0;

                let idsMermasConvertidorasActualizdos =
                    mermasConvertidoras_actualizados.map(function (merma) {
                        count_mermasConvertidoras_actualizados++;
                        $("#mermasConvertidoras-row-" + merma.id).css(
                            "background-color",
                            "#faffa8"
                        );
                        return merma.id;
                    });

                let idsMermasConvertidorasInactivados =
                    mermasConvertidoras_inactivados.map(function (merma) {
                        count_mermasConvertidoras_inactivados++;
                        $("#mermasConvertidoras-row-" + merma.id).css(
                            "background-color",
                            "#ef5350"
                        );
                        return merma.id;
                    });

                console.log(idsMermasConvertidorasActualizdos);

                let listadoMermasConvertidorasNuevos =
                    mermasConvertidoras_nuevos
                        .map(function (mermasConvertidoras) {
                            count_mermasConvertidoras_nuevos++;
                            let newMermaCorrugadora = `<tr style="background-color:#b4f3b1">
                <td></td>
                <td>${mermasConvertidoras.porcentaje_merma_convertidora}</td>
                <td>${mermasConvertidoras.planta}</td>
                <td>${mermasConvertidoras.proceso}</td>
                <td>${mermasConvertidoras.rubro}</td>
                </tr>`;
                            console.log(
                                mermasConvertidoras.orden,
                                mermasConvertidoras.orden - 1
                            );
                            if (
                                mermasConvertidoras.orden &&
                                $(
                                    "#mermasConvertidoras-row-" +
                                        (mermasConvertidoras.orden + 1)
                                ) > 0
                            ) {
                                $(
                                    "#mermasConvertidoras-row-" +
                                        (mermasConvertidoras.orden + 1)
                                ).after(newMermaCorrugadora);
                            } else {
                                $("#listadoMermasConvertidorasMasivos").append(
                                    newMermaCorrugadora
                                );
                            }

                            return;
                        })
                        .join("");

                // $('#listadoMermasConvertidorasMasivos').append(listadoMermasConvertidorasNuevos);
                listadoMermasConvertidorasErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
               
               <td  colspan="5">Mermas Convertidoras con Errores</td>
               </tr>`;
                listadoMermasConvertidorasErroneos +=
                    mermasConvertidoras_erroneos
                        .map(function (merma) {
                            count_mermasConvertidoras_erroneos++;
                            return `<tr style="background-color:#eac7c7;">
               
               <td colspan="1">Linea Excel #${merma.linea}</td>
               <td colspan="4">${merma.motivos}</td>
               
               </tr>`;
                        })
                        .join("");
                if (mermasConvertidoras_erroneos.length > 0) {
                    $("#listadoMermasConvertidorasMasivos").append(
                        listadoMermasConvertidorasErroneos
                    );
                }

                $("#totalMermasConvertidoras").html(
                    +$("#totalMermasConvertidoras").html() +
                        count_mermasConvertidoras_nuevos -
                        count_mermasConvertidoras_inactivados
                );
                $("#mermasConvertidorasNuevos").html(
                    count_mermasConvertidoras_nuevos
                );
                $("#mermasConvertidorasActualizados").html(
                    count_mermasConvertidoras_actualizados
                );
                $("#mermasConvertidorasInactivados").html(
                    count_mermasConvertidoras_inactivados
                );
                $("#mermasConvertidorasErroneos").html(
                    count_mermasConvertidoras_erroneos
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
        // $("#btn-cargar-mermasConvertidoras").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-mermasConvertidoras").prop("disabled", true);
        } else {
            $("#btn-cargar-mermasConvertidoras").prop("disabled", false);
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
