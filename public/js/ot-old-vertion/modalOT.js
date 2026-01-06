$(document).ready(function () {
    const rootURL = "http://test.envases-ot.inveb.cl/";
    // Al hacer click para ver modal de ot
    $(".modalVerOt").click(function () {
        // usamos el id almacenado por el boton de ver modal para buscar la data
        var ot_id = $(this).attr("id");
        // limpiamos el contenido del modal
        $("#modal-ver-ot-content").html("");
        // cargamos css loader "loading"
        $("#modal-loader").show();
        $.ajaxSetup({
            // añadimos csrf token para ajax
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        // con el id del contrato buscamos todas la informacion a travez de ajax
        $.ajax({
            url: "/modalOT",
            method: "POST",
            data: {
                ot_id: ot_id,
            },
        })
            .done(function (data) {
                console.log(data);
                // cuando el ajax este listo y se ejecute correctamente limpia de nuevo el contenido y añade la vista devuelta por el ajax
                $("#modal-ver-ot-content").html("");
                $("#modal-ver-ot-content").html(data);
                $("#modal-loader").hide(); // hide ajax loader
                $("#modal-ver-ot").modal("show");
            })
            .fail(function () {
                $("#modal-ver-ot-content").html(
                    '<i class="glyphicon glyphicon-info-sign"></i> Error al encontrar información, intente nuevamente en unos minutos...'
                );
                $("#modal-loader").hide();
            });
    });
});
