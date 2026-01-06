$(document).ready(function () {
    document.getElementById("loading").style.display = "none";
    // Ajax on click para calcular resultados
    $("#form-carga-variables").on("submit", function (e) {
        console.log("enviando csv");
        e.preventDefault();
        $("#loading").show();

        var formulario = new FormData(this);
        return $.ajax({
            type: "POST",
            url: "/mantenedores/cotizador/variables/uploading",
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
                $("#btn-cargar-variables")
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
                let variables_actualizados = data.variables_actualizados;
                let variables_erroneos = data.variables_error;

                let count_variables_actualizados = 0;
                let count_variables_erroneos = 0;

                let idsVariablesActualizdos = variables_actualizados.map(
                    function (variable) {
                        count_variables_actualizados++;
                        $("#variable-row-" + variable.id).css(
                            "background-color",
                            "#faffa8"
                        );
                        return variable.id;
                    }
                );

                console.log(idsVariablesActualizdos);

                // $('#listadoVariablesMasivos').append(listadoVariablesNuevos);
                let listadoVariablesErroneos = `<tr><td><br/></td></tr><tr class="text-center" style="background-color:#eac7c7;font-weight:bold;">
               
               <td  colspan="2">Variables con Errores</td>
               </tr>`;
                listadoVariablesErroneos += variables_erroneos
                    .map(function (variable) {
                        count_variables_erroneos++;
                        return `<tr style="background-color:#eac7c7;">
               
               <td colspan="1">Linea Excel #${variable.linea}</td>
               <td colspan="1">${variable.motivos}</td>
               
               </tr>`;
                    })
                    .join("");
                if (variables_erroneos.length > 0) {
                    $("#listadoVariablesMasivos").append(
                        listadoVariablesErroneos
                    );
                }

                $("#variablesActualizados").html(count_variables_actualizados);
                $("#variablesErroneos").html(count_variables_erroneos);
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
        // $("#btn-cargar-variables").prop("disabled", false);
    });
    $("#archivo").on("change", function (e) {
        if ($(this).get(0).files.length === 0) {
            console.log("No files selected.");
            $("#btn-cargar-variables").prop("disabled", true);
        } else {
            $("#btn-cargar-variables").prop("disabled", false);
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
