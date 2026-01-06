$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-matrices").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/matrices/uploading",
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
                $("#btn-cargar-matrices")
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
                let matrices_nuevos = data.matrices;
                let matrices_actualizados = data.matrices_actualizados;
                let matrices_inactivados = data.matrices_inactivados;
                let matrices_erroneos = data.matrices_error;

                let count_matrices_nuevos = 0;
                let count_matrices_actualizados = 0;
                let count_matrices_inactivados = 0;
                let count_matrices_erroneos = 0;

                let idsmatricesActualizdos = matrices_actualizados.map(function (
                    matriz
                ) {
                    count_matrices_actualizados++;
                    $("#matriz-row-" + matriz.orden).css(
                        "background-color",
                        "#faffa8"
                    );
                    return matriz.id;
                });

                let idsmatricesInactivados = matrices_inactivados.map(function (
                    matriz
                ) {
                    count_matrices_inactivados++;
                    $("#matriz-row-" + matriz.orden).css(
                        "background-color",
                        "#ef5350"
                    );
                    return matriz.id;
                });

                console.log(idsmatricesActualizdos);

                let listadomatricesNuevos = matrices_nuevos
                    .map(function (matriz) {
                        count_matrices_nuevos++;
                        let newmatriz = `<tr style="background-color:#b4f3b1">
                <td></td>
                <td>${matriz.plano_cad}</td>
                <td>${matriz.material}</td>
                <td>${matriz.texto_breve_material}</td>
                <td>${matriz.largo_matriz}</td>
                <td>${matriz.ancho_matriz}</td>
                <td>${matriz.cantidad_largo_matriz}</td>
                <td>${matriz.cantidad_ancho_matriz}</td>
                <td>${matriz.separacion_largo_matriz}</td>
                <td>${matriz.separacion_ancho_matriz}</td>
                <td>${matriz.tipo_matriz}</td>
                <td>${matriz.total_golpes}</td>
                <td>${matriz.maquina}</td>
                <td>${matriz.active == 1 ? "Activo" : "Inactivo"}</td>
                </tr>`;
                        console.log(matriz.orden, matriz.orden - 1);
                        if (
                            matriz.orden &&
                            $("#matriz-row-" + (matriz.orden + 1)) > 0
                        ) {
                            $("#matriz-row-" + (matriz.orden + 1)).after(
                                newmatriz
                            );
                        } else {
                            $("#listadomatricesMasivos").append(newmatriz);
                        }

                        return;
                    })
                    .join("");

                // $('#listadomatricesMasivos').append(listadomatricesNuevos);
                listadomatricesErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
               
               <td  colspan="5">matrices con Errores</td>
               </tr>`;
                listadomatricesErroneos += matrices_erroneos
                    .map(function (matriz) {
                        count_matrices_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="2">Linea Excel #${matriz.linea}</td>
               <td colspan="3">${matriz.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (matrices_erroneos.length > 0) {
                    $("#listadomatricesMasivos").append(listadomatricesErroneos);
                }

                $("#totalmatrices").html(
                    +$("#totalmatrices").html() +
                    count_matrices_nuevos -
                    count_matrices_inactivados
                );
                $("#matricesNuevos").html(count_matrices_nuevos);
                $("#matricesActualizados").html(count_matrices_actualizados);
                $("#matricesInactivados").html(count_matrices_inactivados);
                $("#matricesErroneos").html(count_matrices_erroneos);
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
        // $("#btn-cargar-matrices").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-matrices").prop("disabled", true);
        } else {
            $("#btn-cargar-matrices").prop("disabled", false);
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
