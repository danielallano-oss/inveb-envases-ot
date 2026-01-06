$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-consumo-adhesivos").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/consumo_adhesivos/uploading",
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
                $("#btn-cargar-consumo-adhesivos")
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
                let consumo_adhesivos_nuevos = data.consumo_adhesivos;
                let consumo_adhesivos_actualizados =
                    data.consumo_adhesivos_actualizados;
                let consumo_adhesivos_inactivados =
                    data.consumo_adhesivos_inactivados;
                let consumo_adhesivos_erroneos = data.consumo_adhesivos_error;

                let count_consumo_adhesivos_nuevos = 0;
                let count_consumo_adhesivos_actualizados = 0;
                let count_consumo_adhesivos_inactivados = 0;
                let count_consumo_adhesivos_erroneos = 0;

                let idsConsumoAdhesivosActualizdos =
                    consumo_adhesivos_actualizados.map(function (
                        consumoAdhesivo
                    ) {
                        count_consumo_adhesivos_actualizados++;
                        $("#consumoAdhesivo-row-" + consumoAdhesivo.id).css(
                            "background-color",
                            "#faffa8"
                        );
                        return consumoAdhesivo.id;
                    });

                console.log(idsConsumoAdhesivosActualizdos);

                // $('#listadoConsumoAdhesivosMasivos').append(listadoConsumoAdhesivosNuevos);
                listadoConsumoAdhesivosErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
               
               <td  colspan="5">ConsumoAdhesivos con Errores</td>
               </tr>`;
                listadoConsumoAdhesivosErroneos += consumo_adhesivos_erroneos
                    .map(function (consumoAdhesivo) {
                        count_consumo_adhesivos_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="2">Linea Excel #${consumoAdhesivo.linea}</td>
               <td colspan="3">${consumoAdhesivo.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (consumo_adhesivos_erroneos.length > 0) {
                    $("#listadoConsumoAdhesivosMasivos").append(
                        listadoConsumoAdhesivosErroneos
                    );
                }

                $("#totalConsumoAdhesivos").html(
                    +$("#totalConsumoAdhesivos").html()
                );
                $("#ConsumoAdhesivosActualizados").html(
                    count_consumo_adhesivos_actualizados
                );
                $("#ConsumoAdhesivosErroneos").html(
                    count_consumo_adhesivos_erroneos
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
        // $("#btn-cargar-consumo-adhesivos").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-consumo-adhesivos").prop("disabled", true);
        } else {
            $("#btn-cargar-consumo-adhesivos").prop("disabled", false);
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
