$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-papeles").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/papeles/uploading",
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
                $("#btn-cargar-papeles")
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
                let papeles_nuevos = data.papeles;
                let papeles_actualizados = data.papeles_actualizados;
                let papeles_inactivados = data.papeles_inactivados;
                let papeles_erroneos = data.papeles_error;

                let count_papeles_nuevos = 0;
                let count_papeles_actualizados = 0;
                let count_papeles_inactivados = 0;
                let count_papeles_erroneos = 0;

                let idsPapelesActualizdos = papeles_actualizados.map(function (
                    papel
                ) {
                    count_papeles_actualizados++;
                    $("#papel-row-" + papel.orden).css(
                        "background-color",
                        "#faffa8"
                    );
                    return papel.id;
                });

                let idsPapelesInactivados = papeles_inactivados.map(function (
                    papel
                ) {
                    count_papeles_inactivados++;
                    $("#papel-row-" + papel.orden).css(
                        "background-color",
                        "#ef5350"
                    );
                    return papel.id;
                });

                console.log(idsPapelesActualizdos);

                let listadoPapelesNuevos = papeles_nuevos
                    .map(function (papel) {
                        count_papeles_nuevos++;
                        let newPapel = `<tr style="background-color:#b4f3b1">
                <td></td>
                <td>${papel.codigo}</td>
                <td>${papel.gramaje}</td>
                <td>${papel.precio}</td>
                <td>${papel.active == 1 ? "Activo" : "Inactivo"}</td>
                </tr>`;
                        console.log(papel.orden, papel.orden - 1);
                        if (
                            papel.orden &&
                            $("#papel-row-" + (papel.orden + 1)) > 0
                        ) {
                            $("#papel-row-" + (papel.orden + 1)).after(
                                newPapel
                            );
                        } else {
                            $("#listadoPapelesMasivos").append(newPapel);
                        }

                        return;
                    })
                    .join("");

                // $('#listadoPapelesMasivos').append(listadoPapelesNuevos);
                listadoPapelesErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
               
               <td  colspan="5">Papeles con Errores</td>
               </tr>`;
                listadoPapelesErroneos += papeles_erroneos
                    .map(function (papel) {
                        count_papeles_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="2">Linea Excel #${papel.linea}</td>
               <td colspan="3">${papel.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (papeles_erroneos.length > 0) {
                    $("#listadoPapelesMasivos").append(listadoPapelesErroneos);
                }

                $("#totalPapeles").html(
                    +$("#totalPapeles").html() +
                        count_papeles_nuevos -
                        count_papeles_inactivados
                );
                $("#papelesNuevos").html(count_papeles_nuevos);
                $("#papelesActualizados").html(count_papeles_actualizados);
                $("#papelesInactivados").html(count_papeles_inactivados);
                $("#papelesErroneos").html(count_papeles_erroneos);
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
            $("#btn-cargar-papeles").prop("disabled", true);
        } else {
            $("#btn-cargar-papeles").prop("disabled", false);
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
