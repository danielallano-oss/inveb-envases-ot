$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-paletizados").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();

        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/paletizados/uploading",
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
                $("#btn-cargar-paletizados")
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
                let paletizados_actualizados = data.paletizados_actualizados;
                let paletizados_erroneos = data.paletizados_error;

                let count_paletizados_actualizados = 0;
                let count_paletizados_erroneos = 0;

                let idsPaletizadosActualizdos = paletizados_actualizados.map(
                    function (paletizado) {
                        count_paletizados_actualizados++;
                        $("#paletizado-row-" + paletizado.id).css(
                            "background-color",
                            "#faffa8"
                        );
                        return paletizado.id;
                    }
                );

                console.log(idsPaletizadosActualizdos);

                // $('#listadoPaletizadosMasivos').append(listadoPaletizadosNuevos);
                listadoPaletizadosErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
               
               <td  colspan="14">Paletizados con Errores</td>
               </tr>`;
                listadoPaletizadosErroneos += paletizados_erroneos
                    .map(function (paletizado) {
                        count_paletizados_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="3">Linea Excel #${paletizado.linea}</td>
               <td colspan="11">${paletizado.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (paletizados_erroneos.length > 0) {
                    $("#listadoPaletizadosMasivos").append(
                        listadoPaletizadosErroneos
                    );
                }

                $("#totalPaletizados").html(
                    +$("#totalPaletizados").html() +
                        count_paletizados_nuevos -
                        count_paletizados_inactivados
                );
                $("#paletizadosNuevos").html(count_paletizados_nuevos);
                $("#paletizadosActualizados").html(
                    count_paletizados_actualizados
                );
                $("#paletizadosInactivados").html(
                    count_paletizados_inactivados
                );
                $("#paletizadosErroneos").html(count_paletizados_erroneos);
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
        // $("#btn-cargar-paletizados").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-paletizados").prop("disabled", true);
        } else {
            $("#btn-cargar-paletizados").prop("disabled", false);
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
