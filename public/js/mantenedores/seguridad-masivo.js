$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-seguridades").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/factores_seguridad/uploading",
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
                $("#btn-cargar-seguridades")
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
                let seguridades_actualizados = data.seguridades_actualizados;
                let seguridades_erroneos = data.seguridades_error;

                let count_seguridades_nuevos = 0;
                let count_seguridades_actualizados = 0;
                let count_seguridades_inactivados = 0;
                let count_seguridades_erroneos = 0;

                let idsSeguridadesActualizdos = seguridades_actualizados.map(
                    function (seguridad) {
                        count_seguridades_actualizados++;
                        $("#seguridad-row-" + seguridad.id).css(
                            "background-color",
                            "#faffa8"
                        );
                        return seguridad.id;
                    }
                );

                console.log(idsSeguridadesActualizdos);

                // $('#listadoSeguridadesMasivos').append(listadoSeguridadesNuevos);
                listadoSeguridadesErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
               
               <td  colspan="4">Factores de Seguridad con Errores</td>
               </tr>`;
                listadoSeguridadesErroneos += seguridades_erroneos
                    .map(function (seguridad) {
                        count_seguridades_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="1">Linea Excel #${seguridad.linea}</td>
               <td colspan="3">${seguridad.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (seguridades_erroneos.length > 0) {
                    $("#listadoSeguridadesMasivos").append(
                        listadoSeguridadesErroneos
                    );
                }

                $("#totalSeguridades").html(
                    +$("#totalSeguridades").html() +
                        count_seguridades_nuevos -
                        count_seguridades_inactivados
                );
                $("#seguridadesNuevos").html(count_seguridades_nuevos);
                $("#seguridadesActualizados").html(
                    count_seguridades_actualizados
                );
                $("#seguridadesInactivados").html(
                    count_seguridades_inactivados
                );
                $("#seguridadesErroneos").html(count_seguridades_erroneos);
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
        // $("#btn-cargar-seguridades").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-seguridades").prop("disabled", true);
        } else {
            $("#btn-cargar-seguridades").prop("disabled", false);
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
