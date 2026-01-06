$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-maquilas").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/maquilas/uploading",
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
                $("#btn-cargar-maquilas")
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
                let maquilas_actualizados = data.maquilas_actualizados;
                let maquilas_erroneos = data.maquilas_error;

                let count_maquilas_nuevos = 0;
                let count_maquilas_actualizados = 0;
                let count_maquilas_inactivados = 0;
                let count_maquilas_erroneos = 0;

                let idsMaquilasActualizdos = maquilas_actualizados.map(
                    function (maquila) {
                        count_maquilas_actualizados++;
                        $("#maquila-row-" + maquila.id).css(
                            "background-color",
                            "#faffa8"
                        );
                        return maquila.id;
                    }
                );

                console.log(idsMaquilasActualizdos);

                // $('#listadoMaquilasMasivos').append(listadoMaquilasNuevos);
                listadoMaquilasErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
               
               <td  colspan="8">Servicios de Maquila con Errores</td>
               </tr>`;
                listadoMaquilasErroneos += maquilas_erroneos
                    .map(function (maquila) {
                        count_maquilas_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="2">Linea Excel #${maquila.linea}</td>
               <td colspan="6">${maquila.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (maquilas_erroneos.length > 0) {
                    $("#listadoMaquilasMasivos").append(
                        listadoMaquilasErroneos
                    );
                }

                $("#totalMaquilas").html(
                    +$("#totalMaquilas").html() +
                        count_maquilas_nuevos -
                        count_maquilas_inactivados
                );
                $("#maquilasNuevos").html(count_maquilas_nuevos);
                $("#maquilasActualizados").html(count_maquilas_actualizados);
                $("#maquilasInactivados").html(count_maquilas_inactivados);
                $("#maquilasErroneos").html(count_maquilas_erroneos);
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
        // $("#btn-cargar-maquilas").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-maquilas").prop("disabled", true);
        } else {
            $("#btn-cargar-maquilas").prop("disabled", false);
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
