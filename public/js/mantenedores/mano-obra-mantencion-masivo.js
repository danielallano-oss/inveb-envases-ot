$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-mano-obra-mantencion").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/mano_obra_mantencion/uploading",
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
                $("#btn-cargar-mano-obra-mantencion")
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

                let mano_obra_mantencion_nuevos = data.mano_obra_mantencion;
                let mano_obra_mantencion_actualizados = data.mano_obra_mantencion_actualizados;
                let mano_obra_mantencion_inactivados = data.mano_obra_mantencion_inactivados;
                let mano_obra_mantencion_erroneos = data.mano_obra_mantencion_error;

                let count_mano_obra_mantencion_nuevos = 0;
                let count_mano_obra_mantencion_actualizados = 0;
                let count_mano_obra_mantencion_inactivados = 0;
                let count_mano_obra_mantencion_erroneos = 0;

                let idsManoObraMantencionActualizados = mano_obra_mantencion_actualizados.map(function (
                    mano_obra_mantencion
                ) {
                    count_mano_obra_mantencion_actualizados++;
                    $("#manoObraMantencion-row-" + mano_obra_mantencion.id).css(
                        "background-color",
                        "#faffa8"
                    );
                    return mano_obra_mantencion.id;
                });

                let idsManoObraMantencionInactivados = mano_obra_mantencion_inactivados.map(function (
                    mano_obra_mantencion
                ) {
                    count_mano_obra_mantencion_inactivados++;
                    $("#manoObraMantencion-row-" + mano_obra_mantencion.id).css(
                        "background-color",
                        "#ef5350"
                    );
                    return mano_obra_mantencion.id;
                });

                let listadoManoObraMantencionNuevos = mano_obra_mantencion_nuevos
                    .map(function (mano_obra_mantencion) {
                        count_mano_obra_mantencion_nuevos++;
                        let newManoObraMantencion = `<tr style="background-color:#b4f3b1">
                <td></td>
                <td>${mano_obra_mantencion.onda}</td>
                <td>${mano_obra_mantencion.proceso}</td>
                <td>${mano_obra_mantencion.concatenacion}</td>
                <td>${mano_obra_mantencion.costo_buin}</td>
                <td>${mano_obra_mantencion.costo_tiltil}</td>
                <td>${mano_obra_mantencion.costo_osorno}</td>
                </tr>`;
                        console.log(mano_obra_mantencion.id, mano_obra_mantencion.id - 1);
                        if (
                            mano_obra_mantencion.id &&
                            $("#manoObraMantencion-row-" + (mano_obra_mantencion.id + 1)) > 0
                        ) {
                            $("#manoObraMantencion-row-" + (mano_obra_mantencion.id + 1)).after(
                                newManoObraMantencion
                            );
                        } else {
                            $("#listadoManoObraMantencionMasivos").append(newManoObraMantencion);
                        }

                        return;
                    })
                    .join("");

             
                listadoManoObraMantencionErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
               
               <td  colspan="5">Mano Obra Mantencion con Errores</td>
               </tr>`;
               listadoManoObraMantencionErroneos += mano_obra_mantencion_erroneos
                    .map(function (mano_obra_mantencion) {
                        count_mano_obra_mantencion_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="2">Linea Excel #${mano_obra_mantencion.linea}</td>
               <td colspan="3">${mano_obra_mantencion.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (mano_obra_mantencion_erroneos.length > 0) {
                    $("#listadoManoObraMantencionMasivos").append(listadoManoObraMantencionErroneos);
                }

                $("#totalManoObraMantencion").html(
                    +$("#totalManoObraMantencion").html() +
                        count_mano_obra_mantencion_nuevos -
                        count_mano_obra_mantencion_inactivados
                );
                $("#manoObraMantencionNuevos").html(count_mano_obra_mantencion_nuevos);
                $("#manoObraMantencionActualizados").html(count_mano_obra_mantencion_actualizados);
                $("#manoObraMantencionInactivados").html(count_mano_obra_mantencion_inactivados);
                $("#manoObraMantencionErroneos").html(count_mano_obra_mantencion_erroneos);

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
        // $("#btn-cargar-mano-obra-mantencion").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-mano-obra-mantencion").prop("disabled", true);
        } else {
            $("#btn-cargar-mano-obra-mantencion").prop("disabled", false);
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
