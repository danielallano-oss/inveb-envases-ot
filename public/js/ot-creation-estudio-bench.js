$(document).ready(function () {

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
    
    // -------- JERARQUIAS ------------------

    // Al comienzo los select de jerarquia 2 y 3 comienzan desabilitados y luego son habilitados al cambiar la jerarquia 1

    $("#subhierarchy_id,#subsubhierarchy_id,#hierarchy_id").prop("disabled", true);
    // ajax para relacionar jerarquia 2 con la jerarquia 1 seleccionada
    $("#hierarchy_id").change(function () {
        var val = $(this).val();
        return $.ajax({
            type: "GET",
            url: "/getJerarquia2",
            data: "hierarchy_id=" + val +"&jerarquia2=" +$("#jerarquia2").val(),
            success: function (data) {
                data = $.parseHTML(data);
                // if (role == 4) {
                //$("#hierarchy_id").prop("disabled", false);
                $("#subhierarchy_id").prop("disabled", false);
                // }
                $("#subhierarchy_id")
                    .empty()
                    .append(data)
                    .selectpicker("refresh");

                $("#subsubhierarchy_id")
                    .empty()
                    .append(
                        $.parseHTML(
                            '<option value="" disabled selected>Seleccionar Opción</option>'
                        )
                    )
                    .prop("disabled", true)
                    .selectpicker("refresh");
            },
            error: function(e) {
                console.log(e.responseText);
            },
            async:true
        });
    });

    // ajax para relacionar jerarquia 3 con la jerarquia 2 seleccionada
    $("#subhierarchy_id").change(function () {
        var val = $(this).val();
        return $.ajax({
            type: "GET",
            url: "/getJerarquia3",
            data: "subhierarchy_id=" + val +"&jerarquia3=" +$("#jerarquia3").val(),
            success: function (data) {
                data = $.parseHTML(data);
                // if (role == 4) {
                $("#subsubhierarchy_id").prop("disabled", false);
                // }
                $("#subsubhierarchy_id")
                    .empty()
                    .append(data)
                    .selectpicker("refresh");
            },
            error: function(e) {
                console.log(e.responseText);
            },
            async:true
        });
    });

    $("#canal_id").change(function () {
        var val = $(this).val();
        switch (val) {
            case "1":
                $("#hierarchy_id")
                    .val(3)
                    .selectpicker("refresh")
                    .triggerHandler("change");
                break;
            case "2":
                $("#hierarchy_id")
                    .val(5)
                    .selectpicker("refresh")
                    .triggerHandler("change");
                break;
            case "3":
                $("#hierarchy_id")
                    .val(4)
                    .selectpicker("refresh")
                    .triggerHandler("change");
                break;
            case "4":
                $("#hierarchy_id")
                    .val(2)
                    .selectpicker("refresh")
                    .triggerHandler("change");
                break;
            case "5":
                $("#hierarchy_id")
                    .val(1)
                    .selectpicker("refresh")
                    .triggerHandler("change");
                break;
            case "6":
                $("#hierarchy_id")
                    .val(6)
                    .selectpicker("refresh")
                    .triggerHandler("change");
                break;
            default:
                $("#hierarchy_id")
                    .val('')
                    .selectpicker("refresh")
                    .triggerHandler("change");
                break;
        }


    });

    // Popular jerarquias en orden
    const populateHierarchies = async () => {
        await $("#hierarchy_id")
            .val($("#jerarquia1").val())
            .triggerHandler("change");
        await $("#subhierarchy_id")
            .val($("#jerarquia2").val())
            .triggerHandler("change");
        $("#subsubhierarchy_id")
            .val($("#jerarquia3").val())
            .selectpicker("refresh");
    };
    // Si no hay jerarquia es que recien ingreso al formulario por lo tanto no populamos los selects
    // de lo contrario si tiene informacion es que se lleno de algun cambio y debemos llenarlo
    if ($("#jerarquia1").val()) populateHierarchies();

    // DESABILITAR CAMPOS SEGUN ROL
    let role = $("#role_id").val();
    
    const disableAndCleanElements = (elements) => {
        toggleAndCleanElements(elements, true);
    };

    const cleanCheckboxs = () => {
        $(".custom-control-input")
            .prop("disabled", false)
            .prop("checked", false);
        $("#muestra").prop("checked", false).triggerHandler("click");
    };

    const disableHierarchies = () => {
        $("#hierarchy_id,#subhierarchy_id,#subsubhierarchy_id")
            .prop("disabled", true)
            .val("")
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");
    };

    const enableAndCleanElements = (elements) => {
        toggleAndCleanElements(elements, false);
    };

    const toggleAndCleanElements = (elements, state) => {
        $(elements)
            .prop("disabled", state)
            .val("")
            .selectpicker("refresh")
            .closest("div.form-group")
            .removeClass("error");
    };

    const setValue = (val, cad) => {
        $(`#${val}`).prop({ disabled: false, readonly: true }).val(cad[val]);
    };

    $("#form-carga-detalles").on("submit", function (e) {
        //console.log("enviardo csv");
        e.preventDefault();
        // loading gif
        $("#loading").show();
        var html =''
        var formulario = new FormData(this);
        

        return $.ajax({
            type: "POST",
            url: "/cargaDetallesEstudio",
            data: formulario,
            processData: false,
            contentType: false,
            cache: false,
            success: function (data) {
                //var url = $('#archivo_detalles').val();
                $("#loading").hide();
                html=tablaDetalleEstudioBenchMasiva(data.detalles,data.cantidad);
                
                notify("Carga Exitosa de Detalles", "success");
                $("#boton_cerrar_cargar").click();
                $("#detalles_estudio_benchmarking").empty();   
                $("#detalles_estudio_benchmarking").append(html);
                $("#detalle_archivo").prop("disabled",false)
                $("#cantidad_estudio_bench").val(data.cantidad);
                $("#cant_aux").val(data.cantidad);
                $('#archivo_estudio').val(data.archivo);
                
               
            },
            error: function (err) {
                // console.log(err.responseJSON.mensaje);
                notify(err.responseJSON.mensaje, "danger");

                if (err.status == 422) {
                }
            },
        });
    });
    
    
});

$("#check_ficha_simple").click(function () {
    if(this.checked){
        $("#check_ficha_doble").prop( "checked", false );
       
    }        
});

$("#check_ficha_doble").click(function () {
    if(this.checked){
        $("#check_ficha_simple").prop( "checked", false );
       
    }        
});

$("#cantidad_estudio_bench").change(function () {
    var val = $(this).val();
    var html =''
    var val_aux = $("#cant_aux").val();
    
    html=tablaDetalleEstudioBench(val,val_aux);
   
    $("#detalles_estudio_benchmarking").empty();   
    $("#detalles_estudio_benchmarking").append(html);
    $("#cant_aux").val(val);    
        
});

function tablaDetalleEstudioBench(cant,cant_aux) {
    
    var filas='';
   
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

    if(cant_aux==0){

        for(var i = 1; i <= cant; i++){

            filas+='<div class="form-group form-row">';
            //filas+='    <label class="col-auto col-form-label">Ficha Num. '+i+':</label>';
            filas+='    <div class="col-3">';
            filas+='        <input class="form-control" type="text" id="identificacion_estudio_'+i+'" name="identificacion_estudio_'+i+'">';
            filas+='    </div>';
            filas+='    <div class="col-4">';
            filas+='        <input class="form-control" type="text" id="cliente_estudio_'+i+'" name="cliente_estudio_'+i+'">';
            filas+='    </div>';
            filas+='    <div class="col-5">';
            filas+='        <input class="form-control" type="text" id="descripcion_estudio_'+i+'" name="descripcion_estudio_'+i+'">';
            filas+='    </div>';
            filas+='</div>';
            filas+='<br>';
        } 

    }else{

        var identificacion ="";
        var cliente ="";
        var descripcion ="";

        for(var i = 1; i <= cant; i++){

            identificacion  = $('#identificacion_estudio_'+i+'').val();
            cliente         = $('#cliente_estudio_'+i+'').val();
            descripcion     = $('#descripcion_estudio_'+i+'').val();

            filas+='<div class="form-group form-row">';
            //filas+='    <label class="col-auto col-form-label">Ficha Num. '+i+':</label>';
            filas+='    <div class="col-3">';
            if(typeof identificacion == 'undefined'){
                filas+='        <input class="form-control" type="text" id="identificacion_estudio_'+i+'" name="identificacion_estudio_'+i+'" value="">';
            }else{
                filas+='        <input class="form-control" type="text" id="identificacion_estudio_'+i+'" name="identificacion_estudio_'+i+'" value="'+identificacion+'">';
            }
            filas+='    </div>';
            filas+='    <div class="col-4">';
            if(typeof cliente == 'undefined'){
                filas+='        <input class="form-control" type="text" id="cliente_estudio_'+i+'" name="cliente_estudio_'+i+'" value="">';
            }else{
                filas+='        <input class="form-control" type="text" id="cliente_estudio_'+i+'" name="cliente_estudio_'+i+'" value="'+cliente+'">';
            }
            filas+='    </div>';
            filas+='    <div class="col-5">';
            if(typeof descripcion == 'undefined'){
                filas+='        <input class="form-control" type="text" id="descripcion_estudio_'+i+'" name="descripcion_estudio_'+i+'" value="">';
            }else{
                filas+='        <input class="form-control" type="text" id="descripcion_estudio_'+i+'" name="descripcion_estudio_'+i+'" value="'+descripcion+'">';
            }
            filas+='    </div>';
            filas+='</div>';
            filas+='<br>';
        } 
    }
    
    return filas;
    
}

function tablaDetalleEstudioBenchMasiva(array,cant) {
    
    var filas='';

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
    
    for(var i = 1; i <= cant; i++)//see that I removed the $ preceeding the `for` keyword, it should not have been there
    {
        filas+='<div class="form-group form-row">';
        //filas+='    <label class="col-auto col-form-label">Ficha Num. '+i+':</label>';
        filas+='    <div class="col-3">';
        filas+='        <input class="form-control" type="text" id="identificacion_estudio_'+i+'" name="identificacion_estudio_'+i+'" value="'+array[i-1]['identificacion_muestra']+'">';
        filas+='    </div>';
        filas+='    <div class="col-4">';
        filas+='        <input class="form-control" type="text" id="cliente_estudio_'+i+'" name="cliente_estudio_'+i+'" value="'+array[i-1]['cliente']+'">';
        filas+='    </div>';
        filas+='    <div class="col-5">';
        filas+='        <input class="form-control" type="text" id="descripcion_estudio_'+i+'" name="descripcion_estudio_'+i+'" value="'+array[i-1]['descripcion']+'">';
        filas+='    </div>';
        filas+='</div>';
        filas+='<br>';
    } 
    return filas;
    
}





