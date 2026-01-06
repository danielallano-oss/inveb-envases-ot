$(document).ready(function () {
    document.getElementById("loading").style.display = "none";

    console.log('aaa');
    // Ajax on click para calcular resultados
    $("#form-carga-materiales").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        console.log('AJAX');
        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/materiales/uploading",
            data: formulario,
            processData: false,
            contentType: false,
            cache: false,
            success: function (data) {

                console.log('SUCCESS AJAX');
                $("#loading").hide(); // hide ajax loader
                // Si se proceso la carga totalmente solo refrescamos
                if (data.url && data.url == "redirect") {
                    window.location.href = window.location.href;
                    return;
                }
                console.log(data);

                // Si la carga es exitosa modificamos el boton de carga parta que sea el de confgirmacion
                $("#btn-cargar-materiales")
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
                let materiales_nuevos = data.materiales;
                let materiales_actualizados = data.materiales_actualizados;
                let materiales_inactivados = data.materiales_inactivados;
                let materiales_erroneos = data.materiales_error;


                let count_materiales_nuevos = 0;
                let count_materiales_actualizados = 0;
                let count_materiales_inactivados = 0;
                let count_materiales_erroneos = 0;

                let idsmaterialesActualizdos = materiales_actualizados.map(function (
                    material
                ) {
                    count_materiales_actualizados++;
                    $("#material-row-" + material.id).css(
                        "background-color",
                        "#faffa8"
                    );
                    return material.id;
                });

                let idsmaterialesInactivados = materiales_inactivados.map(function (
                    material
                ) {
                    count_materiales_inactivados++;
                    $("#material-row-" + material.id).css(
                        "background-color",
                        "#ef5350"
                    );
                    return material.id;
                });

                console.log(idsmaterialesActualizdos);

                // let listadomaterialesNuevos = materiales_nuevos
                //     .map(function (material) {
                //         count_materiales_nuevos++;
                //         let newmaterial = `<tr style="background-color:#b4f3b1">
                // <td></td>
                // <td>${material.codigo}</td>
                // <td>${material.descripcion}</td>
                // <td>${material.numero_colores}</td>
                // <td>${material.gramaje}</td>
                // <td>${material.ect}</td>
                // <td>${material.peso}</td>
                // <td>${material.golpes_largo}</td>
                // <td>${material.golpes_ancho}</td>
                // <td>${material.area_hc}</td>
                // <td>${material.bct_min_lb}</td>
                // <td>${material.bct_min_kg}</td>
                // <td>${material.active == 1 ? "Activo" : "Inactivo"}</td>
                // </tr>`;


                //         if (
                //             material.orden &&
                //             $("#material-row-" + (material.id)) > 0
                //         ) {
                //             $("#material-row-" + (material.id)).after(
                //                 newmaterial
                //             );
                //         } else {
                //             $("#listadomaterialesMasivos").append(newmaterial);
                //         }

                //         return;
                //     })
                //     .join("");

                // $('#listadomaterialesMasivos').append(listadomaterialesNuevos);
                listadomaterialesErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">

               <td  colspan="5">materiales con Errores</td>
               </tr>`;
                listadomaterialesErroneos += materiales_erroneos
                    .map(function (material) {
                        count_materiales_erroneos++;
                        return `<tr style="background-color:#eac7c7;">

               <td colspan="2">Linea Excel #${material.linea}</td>
               <td colspan="3">${material.motivos}</td>

               </tr>`;
                    })
                    .join("");
                if (materiales_erroneos.length > 0) {
                    $("#listadomaterialesMasivos").append(listadomaterialesErroneos);
                }

                $("#totalmateriales").html(
                    +$("#totalmateriales").html() +
                    // count_materiales_nuevos -
                    count_materiales_inactivados
                );

                console.log(data.materiales_actualizados);
                console.log(data.materiales_inactivados);
                console.log(data.materiales_error);
                console.log('-------------------------');
                console.log(count_materiales_actualizados);
                console.log(count_materiales_inactivados);
                console.log(count_materiales_erroneos);
                // $("#materialesNuevos").html(count_materiales_nuevos);
                $("#materialesActualizados").html(count_materiales_actualizados);
                $("#materialesInactivados").html(count_materiales_inactivados);
                $("#materialesErroneos").html(count_materiales_erroneos);

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
        // $("#btn-cargar-materiales").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-materiales").prop("disabled", true);
        } else {
            $("#btn-cargar-materiales").prop("disabled", false);
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
