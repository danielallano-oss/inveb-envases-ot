$(document).ready(function () {
    const rootURL = "/"; // URL relativa para ambiente local
    // Al hacer click para ver modal de contrato
    $(".modalAsignacion").click(function () {
        // debugger;
        // usamos el id almacenado por el boton de ver modal para buscar la data
        var ot_id = $(this).attr("id");
        var role_id = $("#role_id").val();
        console.trace(ot_id);
        // limpiamos el contenido del modal
        $("#modal-asignacion-content").html("");
        // cargamos css loader "loading"
        $("#modal-loader-asignacion").show();
        $.ajaxSetup({
            // añadimos csrf token para ajax
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        // con el id del contrato buscamos todas la informacion a travez de ajax
        $.ajax({
            url: "/modalAsignacion",
            method: "POST",
            data: {
                ot_id,
                role_id,
                // team_id
            },
        })
            .done(function (data) {
                // cuando el ajax este listo y se ejecute correctamente limpia de nuevo el contenido y añade la vista devuelta por el ajax
                $("#modal-asignacion-content").html("");
                $("#modal-asignacion-content").html(data);
                $("#modal-loader-asignacion").hide(); // hide ajax loader
                $("#modal-asignacion").modal("show");
                // inicializamos el datepicker que viene de la vista

                setTimeout(function () {
                    // Inicia el selector multiple
                    $("select[multiple]").selectpicker();
                    // CREAR GESTION
                    $("#asignacion").on("click", function (e) {
                        console.log("clicked asignacion");

                        const id = document.getElementById("ot_id").value;
                        const asignado_id = document.getElementById(
                            "profesional_id"
                        ).value;
                        const payload = {
                            id,
                            asignado_id,
                        };

                        const notify = (
                            msg = "Complete los campos faltantes",
                            type = "danger"
                        ) => {
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
                        console.log(payload);

                        // e.preventDefault();
                        $.ajaxSetup({
                            headers: {
                                "X-CSRF-TOKEN": $(
                                    'meta[name="csrf-token"]'
                                ).attr("content"),
                            },
                        });
                        $.ajax({
                            type: "POST",
                            url: "/asignarOT",
                            data: payload,
                            success: function (data) {
                                console.log(data);
                                if (data != 200) {
                                    notify(
                                        "Error al crear solicitud",
                                        "danger"
                                    );
                                } else {
                                    window.location.replace(
                                        rootURL + "asignacionesConMensaje"
                                    );
                                }
                            },
                        });
                    });
                }, 0); // fin settimeout
            })
            .fail(function () {
                $("#modal-asignacion-content").html(
                    '<i class="glyphicon glyphicon-info-sign"></i> Error al encontrar información, intente nuevamente.'
                );
                $("#modal-loader-asignacion").hide();
            });
    });
});
