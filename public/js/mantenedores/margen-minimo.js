$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-margenes").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/margenes_minimos/uploading",
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
                $("#btn-cargar-margenes")
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
                let margenes_nuevos = data.margenes;
                let margenes_actualizados = data.margenes_actualizados;
                let margenes_erroneos = data.margenes_error;

                let count_margenes_nuevos = 0;
                let count_margenes_actualizados = 0;
                //let count_margenes_inactivados = 0;
                let count_margenes_erroneos = 0;

                let idsMargenesActualizdos = margenes_actualizados.map(function (
                    margen
                ) {
                    count_margenes_actualizados++;
                    $("#margen-row-" + margen.id).css(
                        "background-color",
                        "#faffa8"
                    );
                    return margen.id;
                });

               

                console.log(idsMargenesActualizdos);

                let listadoMargenesNuevos = margenes_nuevos.map(function (margen) {
                    count_margenes_nuevos++;
                    let newMargen = `<tr style="background-color:#b4f3b1">
                    <td></td>
                    <td>${margen.mercado_descripcion}</td>
                    <td>${margen.rubro_descripcion}</td>
                    <td>${margen.cluster}</td>
                    <td>${margen.minimo}</td>
                    </tr>`;
                        console.log(margen.id, margen.id - 1);
                        if (
                            margen.id &&
                            $("#margen-row-" + (margen.id + 1)) > 0
                        ) {
                            $("#margen-row-" + (margen.id + 1)).after(
                                newMargen
                            );
                        } else {
                            $("#listadoMargenesMinimosMasivos").append(newMargen);
                        }

                        return;
                })
                    .join("");
                console.log(listadoMargenesNuevos);
                // $('#listadoPapelesMasivos').append(listadoPapelesNuevos);
                listadoMargenesErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
                
                
               <td  colspan="5">Margenes con Errores</td>
               </tr>`;
                listadoMargenesErroneos += margenes_erroneos
                    .map(function (margen) {
                        count_margenes_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="2">Linea Excel #${margen.linea}</td>
               <td colspan="3">${margen.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (margenes_erroneos.length > 0) {
                    $("#listadoMargenesMinimosMasivos").append(listadoMargenesErroneos);
                }
                console.log(count_margenes_nuevos);
                $("#totalMargenes").html(
                    +$("#totalMargenes").html() +
                        count_margenes_nuevos
                );
                $("#margenesNuevos").html(count_margenes_nuevos);
                $("#margenesActualizados").html(count_margenes_actualizados);
                //$("#margenesInactivados").html(count_margenes_inactivados);
                $("#margenesErroneos").html(count_margenes_erroneos);
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
            $("#btn-cargar-margenes").prop("disabled", true);
        } else {
            $("#btn-cargar-margenes").prop("disabled", false);
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
