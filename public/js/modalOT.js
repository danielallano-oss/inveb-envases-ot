$(document).ready(function () {
    const rootURL = "/"; // URL relativa para ambiente local
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
                // console.log(data);
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

    $(".modalVerOtEstudio").click(function () {
        // usamos el id almacenado por el boton de ver modal para buscar la data
        var ot_id = $(this).attr("id");
        // limpiamos el contenido del modal
        $("#modal-ver-ot-estudio-content").html("");
        // cargamos css loader "loading"
        $("#modal-loader-estudio").show();
        $.ajaxSetup({
            // añadimos csrf token para ajax
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        // con el id del contrato buscamos todas la informacion a travez de ajax
        $.ajax({
            url: "/modalOTEstudio",
            method: "POST",
            data: {
                ot_id: ot_id,
            },
        })
            .done(function (data) {
                console.log(data);
                // cuando el ajax este listo y se ejecute correctamente limpia de nuevo el contenido y añade la vista devuelta por el ajax
                $("#modal-ver-ot-estudio-content").html("");
                $("#modal-ver-ot-estudio-content").html(data);
                $("#modal-loader-estudio").hide(); // hide ajax loader
                $("#modal-ver-ot-estudio").modal("show");
                html=tablaDetalleEstudioBenchUpload($('#cantidad_estudio_bench').val(),$('#detalle_estudio_bench').val())
                $("#detalles_estudio_benchmarking").empty();
                $("#detalles_estudio_benchmarking").append(html);
            })
            .fail(function () {
                $("#modal-ver-ot-estudio-content").html(
                    '<i class="glyphicon glyphicon-info-sign"></i> Error al encontrar información, intente nuevamente en unos minutos...'
                );
                $("#modal-loader-estudio").hide();
            });
    });

    $(".modalVerOtLicitacion").click(function () {
        // usamos el id almacenado por el boton de ver modal para buscar la data
        var ot_id = $(this).attr("id");
        // limpiamos el contenido del modal
        $("#modal-ver-ot-licitacion-content").html("");
        // cargamos css loader "loading"
        $("#modal-loader-licitacion").show();
        $.ajaxSetup({
            // añadimos csrf token para ajax
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        // con el id del contrato buscamos todas la informacion a travez de ajax
        $.ajax({
            url: "/modalOTLicitacion",
            method: "POST",
            data: {
                ot_id: ot_id,
            },
        })
            .done(function (data) {
                // console.log(data);
                // cuando el ajax este listo y se ejecute correctamente limpia de nuevo el contenido y añade la vista devuelta por el ajax
                $("#modal-ver-ot-licitacion-content").html("");
                $("#modal-ver-ot-licitacion-content").html(data);
                $("#modal-loader-licitacion").hide(); // hide ajax loader
                $("#modal-ver-ot-licitacion").modal("show");
            })
            .fail(function () {
                $("#modal-ver-ot-content").html(
                    '<i class="glyphicon glyphicon-info-sign"></i> Error al encontrar información, intente nuevamente en unos minutos...'
                );
                $("#modal-loader-licitacion").hide();
            });
    });

    $(".modalVerOtFichaTecnica").click(function () {
        // usamos el id almacenado por el boton de ver modal para buscar la data
        var ot_id = $(this).attr("id");
        // limpiamos el contenido del modal
        $("#modal-ver-ot-ficha-tecnica-content").html("");
        // cargamos css loader "loading"
        $("#modal-loader-ficha-tecnica").show();
        $.ajaxSetup({
            // añadimos csrf token para ajax
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        // con el id del contrato buscamos todas la informacion a travez de ajax
        $.ajax({
            url: "/modalOTFichaTecnica",
            method: "POST",
            data: {
                ot_id: ot_id,
            },
        })
            .done(function (data) {
                // console.log(data);
                // cuando el ajax este listo y se ejecute correctamente limpia de nuevo el contenido y añade la vista devuelta por el ajax
                $("#modal-ver-ot-ficha-tecnica-content").html("");
                $("#modal-ver-ot-ficha-tecnica-content").html(data);
                $("#modal-loader-ficha-tecnica").hide(); // hide ajax loader
                $("#modal-ver-ot-ficha-tecnica").modal("show");
            })
            .fail(function () {
                $("#modal-ver-ot-ficha-tecnica-content").html(
                    '<i class="glyphicon glyphicon-info-sign"></i> Error al encontrar información, intente nuevamente en unos minutos...'
                );
                $("#modal-loader-ficha-tecnica").hide();
            });
    });
});

function tablaDetalleEstudioBenchUpload(cant,data) {
    var filas='';
    var data_filas = data.split('*');

    filas+='<br>';
    filas+='<div class="form-group form-row">';
    filas+='    <div class="col-12">';
    filas+='        <label class="card-header">Detalle Estudio Benchmarking</label>';
    filas+='    </div>';
    filas+='</div>';
    filas+='<br>';
    filas+='<div class="form-group form-row">';
    filas+='    <div class="col-3">';
    filas+='        <label class="card-header">Identificación Muestra</label>';
    filas+='    </div>';
    filas+='    <div class="col-4">';
    filas+='        <label class="card-header">Cliente</label>';
    filas+='    </div>';
    filas+='    <div class="col-5">';
    filas+='        <label class="card-header">Descripción</label>';
    filas+='    </div>';
    filas+='</div>';
    filas+='<br>';

    for(var i = 1; i <= cant; i++){

        var data_filas_detalle = data_filas[i-1].split('¡');

        filas+='<div class="form-group form-row">';
        //filas+='    <label class="col-auto col-form-label">Ficha Num. '+i+':</label>';
        filas+='    <div class="col-3">';
        filas+='        <input class="form-control" type="text" id="identificacion_estudio_'+i+'" name="identificacion_estudio_'+i+'" value="'+data_filas_detalle[0]+'" readonly="true">';
        filas+='    </div>';
        filas+='    <div class="col-4">';
        filas+='        <input class="form-control" type="text" id="cliente_estudio_'+i+'" name="cliente_estudio_'+i+'" value="'+data_filas_detalle[1]+'" readonly="true">';
        filas+='    </div>';
        filas+='    <div class="col-5">';
        filas+='        <input class="form-control" type="text" id="descripcion_estudio_'+i+'" name="descripcion_estudio_'+i+'" value="'+data_filas_detalle[2]+'" readonly="true">';
        filas+='    </div>';
        filas+='</div>';
        filas+='<br>';


    }

    return filas;

}
