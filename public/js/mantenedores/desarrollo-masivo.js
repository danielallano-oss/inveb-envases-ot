$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-desarrollos").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/factores_desarrollo/uploading",
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
                $("#btn-cargar-desarrollos")
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
                let desarrollos_actualizados = data.desarrollos_actualizados;
                let desarrollos_erroneos = data.desarrollos_error;

                let count_desarrollos_nuevos = 0;
                let count_desarrollos_actualizados = 0;
                let count_desarrollos_inactivados = 0;
                let count_desarrollos_erroneos = 0;

                let idsDesarrollosActualizdos = desarrollos_actualizados.map(
                    function (desarrollo) {
                        count_desarrollos_actualizados++;
                        $("#desarrollo-row-" + desarrollo.id).css(
                            "background-color",
                            "#faffa8"
                        );
                        return desarrollo.id;
                    }
                );

                console.log(idsDesarrollosActualizdos);

                // $('#listadoDesarrollosMasivos').append(listadoDesarrollosNuevos);
                listadoDesarrollosErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
               
               <td  colspan="9">Factores de Desarrollo con Errores</td>
               </tr>`;
                listadoDesarrollosErroneos += desarrollos_erroneos
                    .map(function (desarrollo) {
                        count_desarrollos_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="2">Linea Excel #${desarrollo.linea}</td>
               <td colspan="7">${desarrollo.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (desarrollos_erroneos.length > 0) {
                    $("#listadoDesarrollosMasivos").append(
                        listadoDesarrollosErroneos
                    );
                }

                $("#totalDesarrollos").html(
                    +$("#totalDesarrollos").html() +
                        count_desarrollos_nuevos -
                        count_desarrollos_inactivados
                );
                $("#desarrollosNuevos").html(count_desarrollos_nuevos);
                $("#desarrollosActualizados").html(
                    count_desarrollos_actualizados
                );
                $("#desarrollosInactivados").html(
                    count_desarrollos_inactivados
                );
                $("#desarrollosErroneos").html(count_desarrollos_erroneos);
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
        // $("#btn-cargar-desarrollos").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-desarrollos").prop("disabled", true);
        } else {
            $("#btn-cargar-desarrollos").prop("disabled", false);
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
