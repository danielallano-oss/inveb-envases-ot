$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-ondas").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/factores_onda/uploading",
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
                $("#btn-cargar-ondas")
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
                let ondas_actualizados = data.ondas_actualizados;
                let ondas_erroneos = data.ondas_error;

                let count_ondas_nuevos = 0;
                let count_ondas_actualizados = 0;
                let count_ondas_inactivados = 0;
                let count_ondas_erroneos = 0;

                let idsOndasActualizdos = ondas_actualizados.map(function (
                    onda
                ) {
                    count_ondas_actualizados++;
                    $("#onda-row-" + onda.id).css(
                        "background-color",
                        "#faffa8"
                    );
                    return onda.id;
                });

                console.log(idsOndasActualizdos);

                // $('#listadoOndasMasivos').append(listadoOndasNuevos);
                listadoOndasErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
               
               <td  colspan="4">Factores de Onda con Errores</td>
               </tr>`;
                listadoOndasErroneos += ondas_erroneos
                    .map(function (onda) {
                        count_ondas_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="1">Linea Excel #${onda.linea}</td>
               <td colspan="3">${onda.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (ondas_erroneos.length > 0) {
                    $("#listadoOndasMasivos").append(listadoOndasErroneos);
                }

                $("#totalOndas").html(
                    +$("#totalOndas").html() +
                        count_ondas_nuevos -
                        count_ondas_inactivados
                );
                $("#ondasNuevos").html(count_ondas_nuevos);
                $("#ondasActualizados").html(count_ondas_actualizados);
                $("#ondasInactivados").html(count_ondas_inactivados);
                $("#ondasErroneos").html(count_ondas_erroneos);
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
        // $("#btn-cargar-ondas").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-ondas").prop("disabled", true);
        } else {
            $("#btn-cargar-ondas").prop("disabled", false);
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
