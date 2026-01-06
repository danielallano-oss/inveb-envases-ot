$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-cartones").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/cartones-esquineros/uploading",
            data: formulario,
            processData: false,
            contentType: false,
            cache: false,
            success: function (data) {
                // Si se proceso la carga totalmente solo refrescamos
                if (data.url && data.url == "redirect") {
                    window.location.href = window.location.href;
                    return;
                }
                console.log(data);
                $("#loading").hide(); // hide ajax loader

                // Si la carga es exitosa modificamos el boton de carga parta que sea el de confgirmacion
                $("#btn-cargar-cartones")
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
                let cartones_nuevos = data.cartones;
                let cartones_actualizados = data.cartones_actualizados;
                let cartones_inactivados = data.cartones_inactivados;
                let cartones_erroneos = data.cartones_error;

                let count_cartones_nuevos = 0;
                let count_cartones_actualizados = 0;
                let count_cartones_inactivados = 0;
                let count_cartones_erroneos = 0;

                let idsCartonesActualizdos = cartones_actualizados.map(
                    function (carton) {
                        count_cartones_actualizados++;
                        $("#carton-row-" + carton.orden).css(
                            "background-color",
                            "#faffa8"
                        );
                        return carton.id;
                    }
                );

                let idsCartonesInactivados = cartones_inactivados.map(function (
                    carton
                ) {
                    count_cartones_inactivados++;
                    $("#carton-row-" + carton.orden).css(
                        "background-color",
                        "#ef5350"
                    );
                    return carton.id;
                });

                console.log(idsCartonesActualizdos);

                let listadoCartonesNuevos = cartones_nuevos
                    .map(function (carton) {
                        count_cartones_nuevos++;
                        let newCarton = `<tr style="background-color:#b4f3b1">
                <td></td>
                <td>${carton.codigo}</td>
                <td>${
                    carton.codigo_papel_1 != '0' && carton.codigo_papel_1 != ""
                        ? carton.codigo_papel_1
                        : ""
                }</td>
                <td>${
                    carton.codigo_papel_2 != '0' && carton.codigo_papel_2 != ""
                        ? carton.codigo_papel_2
                        : ""
                }</td>
                <td>${
                    carton.codigo_papel_3 != '0' && carton.codigo_papel_3 != ""
                        ? carton.codigo_papel_3
                        : ""
                }</td>
                <td>${
                    carton.codigo_papel_4 != '0' && carton.codigo_papel_4 != ""
                        ? carton.codigo_papel_4
                        : ""
                }</td>
                <td>${
                    carton.codigo_papel_5 != '0' && carton.codigo_papel_5 != ""
                        ? carton.codigo_papel_5
                        : ""
                }</td>
                <td>${carton.resistencia}</td>
                <td>${carton.ancho_esquinero}</td>
                <td>${carton.active == 1 ? "Activo" : "Inactivo"}</td>
                </tr>`;
                        // console.log(carton.orden, carton.orden - 1);
                        // if (carton.orden) {
                        //     $("#carton-row-" + (carton.orden + 1)).after(
                        //         newCarton
                        //     );
                        // } else {
                        //     $("#listadoCartonesMasivos").append("newCarton");
                        // }

                        if (
                            carton.orden &&
                            $("#carton-row-" + (carton.orden + 1)) > 0
                        ) {
                            $("#carton-row-" + (carton.orden + 1)).after(
                                newCarton
                            );
                        } else {
                            $("#listadoCartonesMasivos").append(newCarton);
                        }

                        return;
                    })
                    .join("");

                // $('#listadoCartonesMasivos').append(listadoCartonesNuevos);
                listadoCartonesErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
               
               <td  colspan="10">Cartones con Errores</td>
               </tr>`;
                listadoCartonesErroneos += cartones_erroneos
                    .map(function (carton) {
                        count_cartones_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="3">Linea Excel #${carton.linea}</td>
               <td colspan="7">${carton.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (cartones_erroneos.length > 0) {
                    $("#listadoCartonesMasivos").append(
                        listadoCartonesErroneos
                    );
                }

                $("#totalCartones").html(
                    +$("#totalCartones").html() +
                        count_cartones_nuevos -
                        count_cartones_inactivados
                );
                $("#cartonesNuevos").html(count_cartones_nuevos);
                $("#cartonesActualizados").html(count_cartones_actualizados);
                $("#cartonesInactivados").html(count_cartones_inactivados);
                $("#cartonesErroneos").html(count_cartones_erroneos);
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
        // $("#btn-cargar-cartones").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-cartones").prop("disabled", true);
        } else {
            $("#btn-cargar-cartones").prop("disabled", false);
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
