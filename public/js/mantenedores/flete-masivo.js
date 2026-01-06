$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-fletes").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/fletes/uploading",
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
                $("#btn-cargar-fletes")
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
                let fletes_nuevos = data.fletes;
                let fletes_actualizados = data.fletes_actualizados;
                let fletes_inactivados = data.fletes_inactivados;
                let fletes_erroneos = data.fletes_error;

                let count_fletes_nuevos = 0;
                let count_fletes_actualizados = 0;
                let count_fletes_inactivados = 0;
                let count_fletes_erroneos = 0;

                let idsFletesActualizdos = fletes_actualizados.map(function (
                    flete
                ) {
                    count_fletes_actualizados++;
                    $("#flete-row-" + flete.orden).css(
                        "background-color",
                        "#faffa8"
                    );
                    return flete.id;
                });

                let idsFletesInactivados = fletes_inactivados.map(function (
                    flete
                ) {
                    count_fletes_inactivados++;
                    $("#flete-row-" + flete.orden).css(
                        "background-color",
                        "#ef5350"
                    );
                    return flete.id;
                });

                console.log(idsFletesActualizdos);

                let listadoFletesNuevos = fletes_nuevos
                    .map(function (flete) {
                        count_fletes_nuevos++;
                        let newFlete = `<tr style="background-color:#b4f3b1">
                <td></td>
                <td>${flete.ciudad}</td>
                <td>${flete.valor_usd_camion}</td>
                <td>${flete.clp_pallet_osorno}</td>
                <td>${flete.clp_pallet_tiltil}</td>
                <td>${flete.clp_pallet_buin}</td>
                <td>${flete.active == 1 ? "Activo" : "Inactivo"}</td>
                </tr>`;
                        console.log(flete.orden, flete.orden - 1);
                        if (
                            flete.orden &&
                            $("#flete-row-" + (flete.orden + 1)) > 0
                        ) {
                            $("#flete-row-" + (flete.orden + 1)).after(
                                newFlete
                            );
                        } else {
                            $("#listadoFletesMasivos").append(newFlete);
                        }

                        return;
                    })
                    .join("");

                // $('#listadoFletesMasivos').append(listadoFletesNuevos);
                listadoFletesErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
               
               <td  colspan="7">Fletes con Errores</td>
               </tr>`;
                listadoFletesErroneos += fletes_erroneos
                    .map(function (flete) {
                        count_fletes_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="3">Linea Excel #${flete.linea}</td>
               <td colspan="4">${flete.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (fletes_erroneos.length > 0) {
                    $("#listadoFletesMasivos").append(listadoFletesErroneos);
                }

                $("#totalFletes").html(
                    +$("#totalFletes").html() +
                        count_fletes_nuevos -
                        count_fletes_inactivados
                );
                $("#fletesNuevos").html(count_fletes_nuevos);
                $("#fletesActualizados").html(count_fletes_actualizados);
                $("#fletesInactivados").html(count_fletes_inactivados);
                $("#fletesErroneos").html(count_fletes_erroneos);
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
        // $("#btn-cargar-fletes").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-fletes").prop("disabled", true);
        } else {
            $("#btn-cargar-fletes").prop("disabled", false);
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
