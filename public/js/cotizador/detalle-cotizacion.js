$(document).ready(function () {
     
    //console.log('Cargando');
   
    defaultValues();
    if (window.detalles_cotizaciones.length > 0) {
        $(function() {
            $('#generarPrecotizacion').trigger('click');
        });

        renderTable();
        toggleResultados();
        contenedorResultados.show();
        renderTablasResultados();

    }
    
    let guardarDetalleCotizacion = $("#guardarDetalleCotizacion");
    let tipo_detalle = $("#tipo_detalle_id");
    // Seleccionar calculo completo
    tipo_detalle.val(1);
    tipo_detalle.on("change", function () {
        // al cambiar el tipo de detalle limpiamos formulario
        // limpiarFormDetalleCotizacion();
        // Limpiar multidestinos
        $("#newRow").html("");

        var tipo_detalle_id = $(this).val();

        var fragmento_formulario_corrugado = $(
            ".fragmento_formulario_corrugado"
        );
        var fragmento_formulario_esquinero = $(
            ".fragmento_formulario_esquinero"
        );
        // Corrugado
        if (tipo_detalle_id == 1) {
            fragmento_formulario_corrugado.show();
            fragmento_formulario_esquinero.hide();
            if (proceso.val() == 7 || proceso.val() == 9) {
                inputs_offset.show();
            }
            $("#encabezado_tipo_detalle").html("Corrugado");
        } else if (tipo_detalle_id == 2) {
            fragmento_formulario_corrugado.hide();
            fragmento_formulario_esquinero.show();
            inputs_offset.hide();
            $("#encabezado_tipo_detalle").html("Esquinero");
        } else if (tipo_detalle_id == 3) {
        }
    });
    // Ajax on click para calcular resultados
    guardarDetalleCotizacion.on("click", function (e) {
       
        $("#pallets_apilados").prop("disabled",false).selectpicker("refresh");
        $("#maquila,#print_type_id,#numero_colores,#barniz,#barniz_type_id").prop("disabled",false).selectpicker("refresh");
        e.preventDefault();
        let formDetalleCotizacion = $("#form-detalle-cotizacion");
        $(".cantidad-multidestino").each(function () {
            $(formDetalleCotizacion).rules("add", {
                required: true,
            });
        });
        // VALIDAR ANTES DE GUARDAR SI HAY CLISSE IMPRESION DEBE SER MAYOR A 1
        if ($("#clisse").val() == 1) {
            $("#impresion").rules("remove", "min");
            $("#impresion").rules("add", {
                min: 1,
            });
        } else {
            $("#impresion").rules("remove", "min");
            $("#impresion").rules("add", {
                min: 0,
            });
        }
        // Validamos el formulario
        formDetalleCotizacion.valid();
        if (!formDetalleCotizacion.valid()) {
            return false;
        }

        // Antes de serializar habilitamos el formulario para enviar el tipo de detalle
        tipo_detalle.prop("disabled", false).prop("readonly", false);
        var formulario = formDetalleCotizacion.serialize();
        var url;
        if (guardarDetalleCotizacion.hasClass("creacion")) {
           
            url = "/cotizador/guardarDetalleCotizacion/0";
        } else if (guardarDetalleCotizacion.hasClass("edicion")) {
           
            url =
                "/cotizador/guardarDetalleCotizacion/" +
                $("#detalle_cotizacion_id").val();
        }

        // var tipo_calculo = $("#tipo_calculo").val();
        return $.ajax({
            type: "POST",
            url: url,
            data: formulario,
            success: function (data) {
                // Si se esta creando solo pusheamos el nuevo detalle, si se actualiza uno recibimos todos los detalles para actualizarlos
                if (guardarDetalleCotizacion.hasClass("creacion")) {
                    var detalle = data;
                    // Si es un objeto solo pusheamos 1 valor de lo contrario con el spread operator (...) logramos pushear todos los detalles si es un array
                    if (!Array.isArray(data)) {
                        window.detalles_cotizaciones.push(detalle);
                    } else {
                        window.detalles_cotizaciones.push(...detalle);
                    }

                    if(detalle.carton != null){

                        let desperdicio_papel_save = parseFloat(detalle.carton.desperdicio).toFixed(1);
    
                        if(desperdicio_papel_save >= 8){
    
                            notify("El desperdicio del papel para la cotización"+' '+data.id +' '+"es mayor al 8%","warning");
    
                        }
                    }

                    notify("Detalle Creado con exito", "success");
                    toggleResultados();
                } else if (guardarDetalleCotizacion.hasClass("edicion")) {
                    // var detalle = data;

                    // Eliminamos del listado de detalles el actual para luego incluirlo actualizado o con los multiples agregados
                    var indice_detalle = window.detalles_cotizaciones.findIndex(
                        (detalle) =>
                            detalle.id == $("#detalle_cotizacion_id").val()
                    );

                    // window.detalles_cotizaciones.pop(indice_detalle);
                    // debugger;
                    if (!Array.isArray(data)) {
                        window.detalles_cotizaciones[indice_detalle] = data;

                        if(data.carton != null){

                            let desperdicio_papel = parseFloat(data.carton.desperdicio).toFixed(1);

                            if(desperdicio_papel >= 8){

                                notify("El desperdicio del papel para la cotización"+' '+data.id +' '+"es mayor al 8%","warning");

                            }

                        }

                    } else {
                        var detalle_actualizar_index = data.findIndex(
                            (detalle) =>
                                detalle.id == $("#detalle_cotizacion_id").val()
                        );
                        let detalle_actualizar = data.splice(
                            detalle_actualizar_index,
                            1
                        )[0];
                        window.detalles_cotizaciones[indice_detalle] =
                            detalle_actualizar;
                        // if (data.length <= 1) {
                        //     window.detalles_cotizaciones.push(data);
                        // } else {
                        window.detalles_cotizaciones.push(...data);
                        // }
                    }
                    // var detalle_id = detalle.id;
                    // console.log(detalle_id);
                    // var indice_detalle = window.detalles_cotizaciones.findIndex(
                    //     (detalle) => detalle.id === detalle_id
                    // );
                    // window.detalles_cotizaciones[indice_detalle] = detalle;
                    notify("Detalle Actualizado con exito", "success");
                }
                renderTable();
                limpiarFormDetalleCotizacion();
                $("#modal-detalle-cotizacion").modal("toggle");
                // Limpiar resultados anteriores
                // $("#resultados input").val("");
            },
        });

        
    });
    // FIN calculo de resultados

    // Limpieza de inputs
    $("#limpiarDetalleCotizacion").click(function (e) {
        e.preventDefault();
        limpiarFormDetalleCotizacion();
    });

    /*if ($("#pallet").val() == 1) {
        $("#tipo_pallet").show();
    }else{
        $("#tipo_pallet").hide();
        $("#pallet_type_id")
            .selectpicker("val", "")
            .selectpicker("refresh");
    }*/
});
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
// Comportamientos del formulario
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
const tipo_producto = $("#product_type_id");
const maquila = $("#maquila");
const maquila_servicio_id = $("#maquila_servicio_id");
const detalle_maquila_servicio_id = $("#detalle_maquila_servicio_id");
const armado_automatico = $("#armado_automatico");
const armado_usd_caja = $("#armado_usd_caja");
const proceso = $("#process_id");
const print_type = $("#print_type_id");
const numero_colores = $("#numero_colores");
const numero_colores_esquinero = $("#numero_colores_esquinero");
const matriz = $("#matriz");
const clisse = $("#clisse"); 
const clisse_esquinero = $("#clisse_esquinero");
const cinta_desgarro = $("#cinta_desgarro");
const porcentaje_cera_interno = $("#porcentaje_cera_interno");
const porcentaje_cera_externo = $("#porcentaje_cera_externo");
const coverage = $("#coverage");
const coverage_type = $("#coverage_type_id");
cinta_desgarro.on("change", function () {
    if (cinta_desgarro.val() == 1) {
        $("#largura").prop("disabled", false).prop("readonly", false);
    }
});
/*
print_type.on("change", function(){

    if( print_type.val() != ''){

        if (print_type.val() == 2){//Delantera (0-5 colores) + Trasera (1 color)
            /*proceso
                .val(2)
                .selectpicker("refresh")
                .change();

            //Como selecciono la opcion Delantera (0-5 colores) + Trasera (1 color) quiere decir, que solo puede elegir 6 colores

            if($("#coverage_type_id").val() == 1){//Quiere decir que tiene seleccionado barniz y se debe restar un color

                $("#numero_colores")
                .html(
                        `<option value="">Seleccionar...</option>
                        <option value="0"> 0</option>
                        <option value="1"> 1</option>
                        <option value="2"> 2</option>
                        <option value="3"> 3</option>
                        <option value="4"> 4</option>
                        <option value="5"> 5</option>`
                )
                .selectpicker("refresh");

            }else{

                $("#numero_colores")
                .html(
                        `<option value="">Seleccionar...</option>
                        <option value="0"> 0</option>
                        <option value="1"> 1</option>
                        <option value="2"> 2</option>
                        <option value="3"> 3</option>
                        <option value="4"> 4</option>
                        <option value="5"> 5</option>
                        <option value="6"> 6</option>`
                )
                .selectpicker("refresh");   
            }


        }else if(print_type.val() == 3){//Alta gráfica (0-6 colores) + Barniz

            if($("#coverage_type_id").val() == 4){//Barniz Acuso ( reemplaza un color )

                $("#numero_colores")
                .html(
                        `<option value="">Seleccionar...</option>
                        <option value="0"> 0</option>
                        <option value="1"> 1</option>
                        <option value="2"> 2</option>
                        <option value="3"> 3</option>
                        <option value="4"> 4</option>
                        <option value="5"> 5</option>`
                )
                .selectpicker("refresh");

            }else if($("#coverage_type_id").val() == 5){//Barniz UV es un color diferente

                $("#numero_colores")
                .html(
                        `<option value="">Seleccionar...</option>
                        <option value="0"> 0</option>
                        <option value="1"> 1</option>
                        <option value="2"> 2</option>
                        <option value="3"> 3</option>
                        <option value="4"> 4</option>
                        <option value="5"> 5</option>
                        <option value="6"> 6</option>`
                )
                .selectpicker("refresh");   

            }else{

                $("#numero_colores")
                .html(
                        `<option value="">Seleccionar...</option>
                        <option value="0"> 0</option>
                        <option value="1"> 1</option>
                        <option value="2"> 2</option>
                        <option value="3"> 3</option>
                        <option value="4"> 4</option>
                        <option value="5"> 5</option>
                        <option value="6"> 6</option>`
                )
                .selectpicker("refresh");   
            }


        }else{//Solo delantera (0-8 colores, incluyendo barniz)
            /*proceso
                .val("")
                .selectpicker("refresh");

            if($("#coverage_type_id").val() == 1){//Quiere decir que tiene seleccionado barniz y se debe restar un color

                $("#numero_colores")
                .html(
                        `<option value="">Seleccionar...</option>
                        <option value="0"> 0</option>
                        <option value="1"> 1</option>
                        <option value="2"> 2</option>
                        <option value="3"> 3</option>
                        <option value="4"> 4</option>
                        <option value="5"> 5</option>
                        <option value="6"> 6</option>
                        <option value="7"> 7</option>`
                )
                .selectpicker("refresh");

            }else{

                $("#numero_colores")
                .html(
                        `<option value="">Seleccionar...</option>
                        <option value="0"> 0</option>
                        <option value="1"> 1</option>
                        <option value="2"> 2</option>
                        <option value="3"> 3</option>
                        <option value="4"> 4</option>
                        <option value="5"> 5</option>
                        <option value="6"> 6</option>
                        <option value="7"> 7</option>
                        <option value="8"> 8</option>`
                )
                .selectpicker("refresh");   
            }
        }
    }else{

        $("#numero_colores")
        .html(
                `<option value="">Seleccionar...</option>
                <option value="0"> 0</option>
                <option value="1"> 1</option>
                <option value="2"> 2</option>
                <option value="3"> 3</option>
                <option value="4"> 4</option>
                <option value="5"> 5</option>
                <option value="6"> 6</option>
                <option value="7"> 7</option>
                <option value="8"> 8</option>`
        )
        .selectpicker("refresh");

    }
});*/
/*
$("#coverage_type_id").on("change", function(){

    if($("#coverage_type_id").val() == 1){//Si esta seleccionado barniz

        if (print_type.val() == 2){//Delantera (0-5 colores) + Trasera (1 color)

            $("#numero_colores")
            .html(
                    `<option value="">Seleccionar...</option>
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                    <option value="3"> 3</option>
                    <option value="4"> 4</option>
                    <option value="5"> 5</option>`
            )
            .selectpicker("refresh");

        }else if(print_type.val() == 1){//Solo delantera (0-8 colores, incluyendo barniz)

            $("#numero_colores")
            .html(
                    `<option value="">Seleccionar...</option>
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                    <option value="3"> 3</option>
                    <option value="4"> 4</option>
                    <option value="5"> 5</option>
                    <option value="6"> 6</option>
                    <option value="7"> 7</option>`
            )
            .selectpicker("refresh");   

        }
        $("#porcentaje_cera_interno,#porcentaje_cera_externo").prop("disabled", false);
    }else if($("#coverage_type_id").val() == 4){//Barniz acuoso 

        if (print_type.val() == 3){//Alta gráfica (0-6 colores) + Barniz
            
            $("#numero_colores")
            .html(
                    `<option value="">Seleccionar...</option>
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                    <option value="3"> 3</option>
                    <option value="4"> 4</option>
                    <option value="5"> 5</option>`
            )
            .selectpicker("refresh");

        }
        $("#porcentaje_cera_interno,#porcentaje_cera_externo").prop("disabled", false);
    }else if($("#coverage_type_id").val() == 5){//Barniz UV

        if (print_type.val() == 3){//Alta gráfica (0-6 colores) + Barniz
            
            $("#numero_colores")
            .html(
                    `<option value="">Seleccionar...</option>
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                    <option value="3"> 3</option>
                    <option value="4"> 4</option>
                    <option value="5"> 5</option>
                    <option value="6"> 6</option>`
            )
            .selectpicker("refresh");
            
        }

    }else if($("#coverage_type_id").val() == 2){

        if (print_type.val() == 2){//Delantera (0-5 colores) + Trasera (1 color)

            $("#numero_colores")
            .html(
                    `<option value="">Seleccionar...</option>
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                    <option value="3"> 3</option>
                    <option value="4"> 4</option>
                    <option value="5"> 5</option>
                    <option value="6"> 6</option>`
            )
            .selectpicker("refresh");

        }else if(print_type.val() == 1){//Solo delantera (0-8 colores, incluyendo barniz)

            $("#numero_colores")
            .html(
                    `<option value="">Seleccionar...</option>
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                    <option value="3"> 3</option>
                    <option value="4"> 4</option>
                    <option value="5"> 5</option>
                    <option value="6"> 6</option>
                    <option value="7"> 7</option>
                    <option value="8"> 8</option>`
            )
            .selectpicker("refresh");   

        }
        $("#porcentaje_cera_interno,#porcentaje_cera_externo").prop("disabled", false);
    }else{
        $("#porcentaje_cera_interno,#porcentaje_cera_externo").val(0);
        $("#porcentaje_cera_interno,#porcentaje_cera_externo").prop("disabled", true);
    }

});

coverage.on("change", function () {
    if (coverage.val() == 1) {
        $("#coverage_type_id,#porcentaje_cera_interno,#porcentaje_cera_externo")
        .prop("disabled", false)
        .prop("readonly", false)
        .selectpicker("refresh");

        if (print_type.val() == 2){//Delantera (0-5 colores) + Trasera (1 color)

            $("#numero_colores")
            .html(
                    `<option value="">Seleccionar...</option>
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                    <option value="3"> 3</option>
                    <option value="4"> 4</option>
                    <option value="5"> 5</option>`
            )
            .selectpicker("refresh");

        }else if(print_type.val() == 1){//Solo delantera (0-8 colores, incluyendo barniz)

            $("#numero_colores")
            .html(
                    `<option value="">Seleccionar...</option>
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                    <option value="3"> 3</option>
                    <option value="4"> 4</option>
                    <option value="5"> 5</option>
                    <option value="6"> 6</option>
                    <option value="7"> 7</option>`
            )
            .selectpicker("refresh");   

        }


    }else{
        $("#coverage_type_id,#porcentaje_cera_interno,#porcentaje_cera_externo")
        .prop("disabled", true)
        .prop("readonly", true)
        .val("")
        .selectpicker("refresh");    
        /*
        if (print_type.val() == 2){//Delantera (0-5 colores) + Trasera (1 color)

            $("#numero_colores")
            .html(
                    `<option value="">Seleccionar...</option>
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                    <option value="3"> 3</option>
                    <option value="4"> 4</option>
                    <option value="5"> 5</option>
                    <option value="6"> 6</option>`
            )
            .selectpicker("refresh");

        }else if(print_type.val() == 1){//Solo delantera (0-8 colores, incluyendo barniz)

            $("#numero_colores")
            .html(
                    `<option value="">Seleccionar...</option>
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                    <option value="3"> 3</option>
                    <option value="4"> 4</option>
                    <option value="5"> 5</option>
                    <option value="6"> 6</option>
                    <option value="7"> 7</option>
                    <option value="8"> 8</option>`
            )
            .selectpicker("refresh");   

        }
    }
});
*/

matriz.on("change", function () {
    if (matriz.val() == 1) {
        $("#largura").prop("disabled", false).prop("readonly", false);
        $("#anchura").prop("disabled", false).prop("readonly", false);
        $("#cuchillos_gomas").prop("disabled", false).prop("readonly", false);
    }else{
        $("#cuchillos_gomas").prop("disabled", true).prop("readonly", true).val(0);
    }
});
/*maquila.on("change", function () {
    if (maquila.val() == 1) {
        tipo_producto.triggerHandler("change");
        maquila_servicio_id
            .prop("disabled", false)
            .prop("readonly", false)
            .selectpicker("refresh");
    } else {
        maquila_servicio_id
            .prop("disabled", true)
            .prop("readonly", true)
            .val("")
            .selectpicker("refresh");
        detalle_maquila_servicio_id
            .prop("disabled", true)
            .prop("readonly", true)
            .val("")
            .selectpicker("refresh");
    }
});*/
// maquila_servicio_id.on("change", function () {
//     // console.log("maquila_servicio_id.val()", maquila_servicio_id.val(),maquilaServicios);
//     if (maquila_servicio_id.val()) {
//         // tipo_producto.triggerHandler("change");
//         let servicio = maquilaServicios[maquila_servicio_id.val()];
//         let detalle_maquila = "";
//         if (servicio.desgajado)
//             detalle_maquila +=
//                 '<option value="Desgajado" selected="selected"> Desgajado</option>';
//         if (servicio.ensamblado)
//             detalle_maquila +=
//                 '<option value="Ensamblado" selected="selected"> Ensamblado</option>';
//         if (servicio.pegado)
//             detalle_maquila +=
//                 '<option value="Pegado" selected="selected"> Pegado</option>';
//         if (servicio.flejado)
//             detalle_maquila +=
//                 '<option value="Flejado" selected="selected"> Flejado</option>';
//         if (servicio.palletizado)
//             detalle_maquila +=
//                 '<option value="Palletizado" selected="selected"> Palletizado</option>';
//         if (servicio.empaquetado)
//             detalle_maquila +=
//                 '<option value="Empaquetado" selected="selected"> Empaquetado</option>';

//         detalle_maquila_servicio_id
//             .prop("disabled", false)
//             .prop("readonly", false)
//             .html(detalle_maquila)
//             .selectpicker("refresh");
//     } else {
//         detalle_maquila_servicio_id
//             .prop("disabled", true)
//             .prop("readonly", true)
//             .html("")
//             .selectpicker("refresh");
//     }
// });
tipo_producto.on("change", function () {
    var val = tipo_producto.val();
  
    // Si es cabezal obligado lleva maquila
    /* if (val == 8 && !maquila.prop("disabled")) {    
        maquila
            .prop("disabled", true)
            .prop("readonly", true)
            .val(1)
            .selectpicker("refresh")
            .change();

        return $.ajax({
            type: "GET",
            url: "/cotizador/getServiciosMaquila",
            data: "tipo_producto_id=" + val,
            success: function (data) {
                data = $.parseHTML(data);
                maquila_servicio_id
                    .empty()
                    .append(data)
                    // .val()
                    .selectpicker("refresh");
                // maquila_servicio_id.val(servicio_id).selectpicker("refresh");
            },
        });
    }*/

    if (val == 35) {
        maquila
            .prop("disabled", true)
            .prop("readonly", true)
            .val(1)
            .selectpicker("refresh");
    }else{
        maquila
            .prop("disabled", true)
            .prop("readonly", true)
            .val(0)
            .selectpicker("refresh");
    }

    //Evolutivo 25-01
    if(val == 18){//tipo producto set tabiques
        $("#ensamblado").prop("disabled", false).prop("readonly", false).selectpicker("refresh").triggerHandler("change");
        //$("#desgajado_cabezal").prop("disabled", true).prop("readonly", true).val('').selectpicker("refresh");
        
    }else{
        $("#ensamblado").prop("disabled", true).prop("readonly", true).val('').selectpicker("refresh").triggerHandler("change");
        //$("#desgajado_cabezal").prop("disabled", true).prop("readonly", true).val('').selectpicker("refresh");
    }

    //Evolutivo 25-01
    if(val == 8){//tipo producto set cabezal

         $("#desgajado_cabezal")
                .html('<option value="">Seleccionar...</option> <option value="1">SI</option> <option value="0">NO</option>')
                .prop("disabled", false)
                .prop("readonly", false)
                .val('')
                .selectpicker("refresh");
                
    }else{
        //validar si el val es vacio
        if(val == ''){
            $("#desgajado_cabezal").prop("disabled", true).prop("readonly", true).val('').selectpicker("refresh");
        }else{
            $("#desgajado_cabezal")
                .html('<option value="0">NO</option>')
                .prop("disabled", false)
                .prop("readonly", false)
                .selectpicker("refresh");
            
            $("#desgajado_cabezal").val(0).selectpicker("refresh");
        }
        
    }

    // Si al cambiar el tipo de producto maquila es = SI se actualiza los servicios a seleccionar
    /*if (maquila.val() == 1) {
        return $.ajax({
            type: "GET",
            url: "/cotizador/getServiciosMaquila",
            data: "tipo_producto_id=" + val,
            success: function (data) {
                data = $.parseHTML(data);
                maquila_servicio_id
                    .empty()
                    .append(data)
                    // .val()
                    .selectpicker("refresh");
                detalle_maquila_servicio_id.html("").selectpicker("refresh");

                // maquila_servicio_id.val(servicio_id).selectpicker("refresh");
            },
        });
    } else {
        return false;
    }*/
});

armado_automatico.on("change", function () {
    if (armado_automatico.val() == 1) {
        armado_usd_caja
            .prop("disabled", false)
            .prop("readonly", false)
            .selectpicker("refresh");
    } else {
        armado_usd_caja
            .prop("disabled", true)
            .prop("readonly", true)
            .selectpicker("refresh");
    }
});

const inputs_offset = $("#inputs_offset");
const carton = $("#carton_id");
//const tipo_tinta = $("#tipo_tinta");
proceso.on("change", function () {
   var  coverage_type_aux= $("#coverage_type_id").val();
    // Si el proceso es OFFSET se debe desplegar inputs de offset
    if (proceso.val() == 7 || proceso.val() == 9) {
        //tipo_tinta.hide();

        $("#ink_type_id")
            .selectpicker("val", "")
            .selectpicker("refresh");

        inputs_offset.show();

        /*if (carton.val() != "" && !cartones_offset.includes(+carton.val())) {
            // alert("Debe seleccionar un carton correspondiente a Offset");

            notify(
                "Debe seleccionar un carton correspondiente a Offset",
                "warning"
            );
            carton.val("").selectpicker("refresh");
        }*/

        /*print_type
            .prop("disabled", false)
            .prop("readonly", false)
            .selectpicker("val", "")
            .selectpicker("refresh");
*/
        cinta_desgarro
            .prop("disabled", true)
            .prop("readonly", true)
            .selectpicker("val", 0)
            .selectpicker("refresh");

        /*coverage
            .prop("disabled", true)
            .prop("readonly", true)
            .selectpicker("val", "")
            .selectpicker("refresh");*/

        /*$("#coverage_type_id")
            .html(
                `<option value="">Seleccionar...</option>
                <option value="1"> UV </option>
                <option value="2"> Acuoso </option>
                <option value="3"> Hidrorepelente </option>`
            )
            .selectpicker("refresh");*/

        $("#coverage_type_id")
            .prop("disabled", false)
            .prop("readonly", false)
            .selectpicker("val", coverage_type_aux)
            .selectpicker("refresh");
 /*
        return $.ajax({
            type: "POST",
            url: "/cotizador/cartonGenerico",
            success: function (data) {
                
                data = $.parseHTML(data);
                if(typeof global_id_detalle === "undefined" ){
                    
                    carton
                        .empty() 
                        .append(data)
                        .selectpicker("refresh");
                }else{
                    let detalle_aux = window.detalles_cotizaciones.find(
                        (detalle_aux) => detalle_aux.id === global_id_detalle
                    );
                    carton
                    .empty()
                    .append(data)
                    .val(detalle_aux.carton.id)
                    .selectpicker("refresh");

                    $("#carton_id")
                        .val(detalle_aux.carton.id)
                        .selectpicker("refresh");
                }
                
            },
        });*/


    }else if(proceso.val() == 11 || proceso.val() == 12){//Si el proceso es DIECUTTER - ALTA GRÁFICA o DIECUTTER -C/PEGADO ALTA GRÁFICA, aparece el input de Tipo de tinta
        
        //tipo_tinta.show();

        inputs_offset.hide();

        //La impresion debe ser Alta gráfica (0-6 colores) + Barniz
        /*print_type
            .prop("disabled", false)
            .prop("readonly", false)
            .selectpicker("val", 3)
            .selectpicker("refresh");*/

        // print_type.trigger("change");

        //Alta gráfica no tiene cinta desgarro
        cinta_desgarro
            .prop("disabled", true)
            .prop("readonly", true)
           // .selectpicker("val", 0)
            .selectpicker("refresh");

        
        //Los tipos de cobertura para Alta gráfica solo son estos:
        /*$("#coverage_type_id")
            .html(
                `<option value="">Seleccionar...</option>
                <option value="1"> UV </option>
                <option value="2"> Acuoso </option>
                <option value="3"> Hidrorepelente </option>`
            )
            .selectpicker("refresh");*/

        $("#coverage_type_id")
            .prop("disabled", false)
            .prop("readonly", false)
            .selectpicker("val", coverage_type_aux)
            .selectpicker("refresh");

        /*$("#coverage_type_id")
            .prop("disabled", false)
            .prop("readonly", false)
            .selectpicker("val", 5)
            .selectpicker("refresh");*/
        //alert($("#coverage_type_id").val());
        
            /*coverage
                .prop("disabled", true)
                .prop("readonly", true)
                .selectpicker("val", 1)
                .selectpicker("refresh");*/
        
            

        
        

        //Se ejecuta el trigger para que actualice el campo de número de colores
        $("#coverage_type_id").trigger("change");
        /*
        const carton_aux=carton.val();
        //alert(carton_aux);
        if (!cartones_alta_grafica.includes(+carton_aux)) {
       // if(!cartones_alta_grafica.includes(+carton.val())){
            notify("Debe seleccionar un carton correspondiente de Alta Grafica","warning");
            return $.ajax({
                type: "POST",
                url: "/cotizador/cartonAltaGrafica",
                success: function (data) {
                    data = $.parseHTML(data);
                    carton
                        .empty()
                        .append(data)
                        .selectpicker("refresh");
                },
            });
                      
        }else{
            return $.ajax({
                type: "POST",
                url: "/cotizador/cartonAltaGrafica",
                success: function (data) {
                    data = $.parseHTML(data);
                    carton
                        .empty()
                        .append(data)
                        .selectpicker("refresh");

                    carton
                        .selectpicker("val", carton_aux)
                        .selectpicker("refresh");
                    
                },
            });
            
        }*/
           

            
       // }

    }else {
      //  tipo_tinta.hide();

        $("#ink_type_id")
            .selectpicker("val", "")
            .selectpicker("refresh");

        inputs_offset.hide();
        /*
        print_type
             .prop("disabled", false)
             .prop("readonly", false)
             .selectpicker("val", "")
             .selectpicker("refresh");*/


        cinta_desgarro
            .prop("disabled", false)
            .prop("readonly", false)
            .selectpicker("refresh");

        /*coverage
            .prop("disabled", true)
            .prop("readonly", true)
            .selectpicker("val", "")
            .selectpicker("refresh");*/
       
        //let();
        /*$("#coverage_type_id")
            .html(
                `<option value="">Seleccionar...</option>
                <option value="1"> UV </option>
                <option value="2"> Acuoso </option>
                <option value="3"> Hidrorepelente </option>`
            )
            .selectpicker("refresh");*/

        $("#coverage_type_id")
            .prop("disabled", false)
            .prop("readonly", false)
            .selectpicker("val", coverage_type_aux)
            .selectpicker("refresh");
        /*
        return $.ajax({
            type: "POST",
            url: "/cotizador/cartonGenerico",
            success: function (data) {
                
                data = $.parseHTML(data);
                if(typeof global_id_detalle === "undefined" ){
                    
                    carton
                        .empty()
                        .append(data)
                        .selectpicker("refresh");
                }else{
                    
                    let detalle_aux = window.detalles_cotizaciones.find(
                        (detalle_aux) => detalle_aux.id === global_id_detalle
                    );
                    carton
                        .empty()
                        .append(data)
                        .selectpicker("refresh")
                       
                    $("#carton_id")
                        .val(detalle_aux.carton.id)
                        .selectpicker("refresh");
                }
                
                
            },
        });*/
    }

    //Procesos con pegado
    if (proceso.val() == 4 || proceso.val() == 12){
        $("#pegado_id")
            .prop("disabled",false)
            .prop("readonly",false)
            .selectpicker("refresh");
    }else{
        $("#pegado_id")
        .prop("disabled",true)
        .prop("readonly",true)
        .selectpicker("refresh");
    }

    //cinta desgarro
    if (proceso.val() == 11 || proceso.val() == 12){ //ALta grafica
        $("#cinta_desgarro")
            .prop("disabled",true)
            .prop("readonly",true)
            .val(0)
            .selectpicker("refresh");
    }else{
        $("#cinta_desgarro")
            .prop("disabled",false)
            .prop("readonly",false)
           // .val("")
            .selectpicker("refresh");
    }
    
});

carton.on("change", function () {
  
    // Si el proceso es OFFSET se debe desplegar inputs de offset
    if (proceso.val() == 7 || proceso.val() == 9) {
        if (carton.val() != "" && !cartones_offset.includes(+carton.val())) {
            // alert("Debe seleccionar un carton correspondiente a Offset");

            notify(
                "Debe seleccionar un carton correspondiente a Offset",
                "warning"
            );
            carton.val("").selectpicker("refresh");
        }
    } else {
        inputs_offset.hide();
    }

    // Si el carton es una excepcion debemos bloquear largura y anchura
    if (["62", "67", "72", "77", "84", "90", "92", "95"].indexOf(carton.val()) >= 0) {
        if (cinta_desgarro.val() == 1) {
            $("#anchura").val("").prop("disabled", true);
            $("#largura").prop("disabled", false).prop("readonly", false);
        }else{
            $("#anchura,#largura").val("").prop("disabled", true);
        }
    } else {
        $("#anchura,#largura").prop("disabled", false);
    }

    if(cartones_alta_grafica.includes(+carton.val())){
        var  process_id_aux=$("#process_id").val();
        $("#process_id")
            .html( `<option value="11">DIECUTTER - ALTA GRAFICA </option>
                    <option value="12">DIECUTTER -C/PEGADO ALTA GRAFICA </option>`)
            .selectpicker("refresh");

            $("#process_id")
                .val(process_id_aux)
                .selectpicker("refresh");
    }else{
        var  process_id_aux=$("#process_id").val();

        $("#process_id")
            .html( `<option value="2">DIECUTTER</option>
                    <option value="4">DIECUTTER-C/PEGADO</option>
                    <option value="1">FLEXO</option>
                    <option value="10">FLEXO/MATRIZ COMPLET</option>
                    <option value="5">FLEXO/MATRIZ PARCIAL</option>
                    <option value="7">OFFSET</option>
                    <option value="9">OFFSET-C/PEGADO</option>
                    <option value="11">DIECUTTER - ALTA GRAFICA </option>
                    <option value="12">DIECUTTER -C/PEGADO ALTA GRAFICA </option>
                    <option value="3">S/PROCESO</option>
                    `)           
            .selectpicker("refresh");

            $("#process_id")
                .val(process_id_aux)
                .selectpicker("refresh");
    }
});
/*
$("#ensamblado").on("change", function () {
    var val = $("#ensamblado").val();
  
    if((tipo_producto).val()==18){
        if(val == 1){//Opcion SI
            $("#desgajado_cabezal")
                .html('<option value="1">SI</option>')
                .prop("disabled", false)
                .prop("readonly", false)
                .selectpicker("refresh");
            
            $("#desgajado_cabezal").val(1).selectpicker("refresh");
        }else{
            $("#desgajado_cabezal")
                .html('<option value="">Seleccionar...</option> <option value="1">SI</option> <option value="0">NO</option>')
                .prop("disabled", false)
                .prop("readonly", false)
                .val('')
                .selectpicker("refresh");
        }
    }else{
        $("#desgajado_cabezal")
            .html('<option value="">Seleccionar...</option>')
            .prop("disabled", true)
            .prop("readonly", true)
            .val('')
            .selectpicker("refresh");
    }    

});*/

numero_colores_esquinero.on("change", function () {
    // si numero de colores = 0 no lleva clisse bloquear "NO"
    if (
        numero_colores_esquinero.val() &&
        numero_colores_esquinero.val() != "0"
    ) {
        clisse_esquinero
            .prop("disabled", false)
            .prop("readonly", false)
            .selectpicker("refresh");
    } else {
        clisse_esquinero
            .prop("disabled", true)
            .prop("readonly", true)
            .selectpicker("val", 0)
            .selectpicker("refresh");
    }
});

porcentaje_cera_interno.add(porcentaje_cera_externo).on("change", function () {
    // si numero de colores = 0 no lleva clisse bloquear "NO"
    if (
        porcentaje_cera_interno.val() <= 0 &&
        porcentaje_cera_interno.val() <= 0
    ) {
        cinta_desgarro
            .prop("disabled", false)
            .prop("readonly", false)
            .selectpicker("refresh");
    } else {
        cinta_desgarro
            .prop("disabled", true)
            .prop("readonly", true)
           // .selectpicker("val", 0)
            .selectpicker("refresh");
    }
});

$("#bct_min_lb").on("keyup change", function () {
    if ($(this).val()) {
        $("#bct_min_kg").val(Math.round($("#bct_min_lb").val() * 0.4535));
    } else {
        $("#bct_min_kg").val("");
    }
});

if (window.impresion) {
    data = `<option value="" disabled selected>Seleccionar Opción</option><option value="${window.impresion}"> ${window.impresion}</option><option value="0"> 0</option><option value="2"> 25</option><option value="3"> 50</option><option value="4"> 75</option><option value="5"> 100</option>`;
    $("#impresion")
        .empty()
        .append(data)
        .val(window.impresion)
        .selectpicker("refresh");
    window.impresion = null;
}

$("#listado-resultados-detalle").on(
    "change",
    ".selectpicker-plantas",
    function (e) {
        const planta_id = $("#" + e.currentTarget.id).val();
        const detalle_id = e.currentTarget.id.replace("planta_detalle_", "");

        return $.ajax({
            type: "POST",
            url: "/cotizador/editarDetalleCotizacion",
            data: "detalle_id=" + detalle_id + "&planta_id=" + planta_id,
            success: function (data) {
                var detalle = data;
                var detalle_id = detalle.id;
                var indice_detalle = window.detalles_cotizaciones.findIndex(
                    (detalle) => detalle.id === detalle_id
                );
                window.detalles_cotizaciones[indice_detalle] = detalle;

                notify("Resultados Actualizados", "success");
                renderTablasResultados();
                resaltarFila(detalle_id);
            },
        });
    }
);

$("#listado-resultados-detalle").on(
    {
        change: function (e) {
            actualizarDetalle(e);
        },
        keypress: function (e) {
            if (e.keyCode == 13 && e.target.type !== "submit") {
                e.preventDefault();
                return actualizarDetalle(e);
            }
        },
    },
    ".margen-detalle"
);

$("#listado-resultados-detalle").on(
    {
        change: function (e) {
            calcularMargenInverso(e);
        },
        keypress: function (e) {
            if (e.keyCode == 13 && e.target.type !== "submit") {
                e.preventDefault();
                return calcularMargenInverso(e);
            }
        },
    },
    ".calcular-margen"
);

$(document)
    .on("focusin", ".autofill-value", function () {
        $(this).data("val", $(this).val());
    })
    .on("change", ".autofill-value", function () {
        var prev = $(this).data("val");
        var current = $(this).val();
        if (current == "") {
            // console.log("current = ''");
            $(this).val($(this).data("val"));
        }
    });

$(document).on(
    {
        keyup: function (e) {
            // $(this).val(formatMoney($(this).val(),null,",","."));
            $(this).val(
                $(this)
                    .val()
                    .replace(/[^0-9\.\,]/g, "")
            );
            $(this).val($(this).val().toLocaleString("es"));
            if (
                (e.which != 46 || $(this).val().indexOf(".") != -1) &&
                (e.which < 48 ||
                    e.which > 57 ||
                    e.whitch === 188 ||
                    e.which === 110)
            ) {
                e.preventDefault();
            }
        },
    },
    ".calcular-margen"
);

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
// FUNCIONES LOCALES
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
function resaltarFila($id) {
    $(".row_detalle_" + $id).addClass("actualizado");
    setTimeout(function () {
        $(".row_detalle_" + $id).removeClass("actualizado");
    }, 1000);
}

function actualizarDetalle(e) {
    const margen = $("#" + e.currentTarget.id).val();
    const detalle_id = e.currentTarget.id.replace("margen_detalle_", "");
   
    return $.ajax({
        type: "POST",
        url: "/cotizador/editarDetalleCotizacion",
        data: "detalle_id=" + detalle_id + "&margen=" + margen,
        success: function (data) {
            var detalle = data;
            var detalle_id = detalle.id;
            var indice_detalle = window.detalles_cotizaciones.findIndex(
                (detalle) => detalle.id === detalle_id
            );
            window.detalles_cotizaciones[indice_detalle] = detalle;

            notify("Resultados Actualizados", "success");
            renderTablasResultados();
            resaltarFila(detalle_id);
        },
    });
}

function calcularMargenInverso(e) {
    const monto = $("#" + e.currentTarget.id).val();
    const id = e.currentTarget.id;
    let variable = "usd_mm2";
    if (e.currentTarget.id.startsWith("precio_total_usd_mm2")) {
        variable = "usd_mm2";
    } else if (e.currentTarget.id.startsWith("precio_total_usd_caja")) {
        variable = "usd_caja";
    } else if (e.currentTarget.id.startsWith("precio_total_clp_caja")) {
        variable = "clp_caja";
    }
    const detalle_id = e.currentTarget.id.replace(
        "precio_total_" + variable + "_",
        ""
        );

    AjaxCalcularMargenInverso(id,detalle_id, monto, variable);
}

function AjaxCalcularMargenInverso(id,detalle_id, monto, variable, contador = 0,detener = false) {

    contador++;
    if(contador>3){detener=true;}
    return $.ajax({
        type: "POST",
        url: "/cotizador/editarMargenCotizacion",
        data:
            "detalle_id=" +
            detalle_id +
            "&monto=" +
            monto +
            "&variable=" +
            variable,
        success: function (data) {
            var detalle = data;
            var detalle_id = detalle.id;
            var indice_detalle = window.detalles_cotizaciones.findIndex(
                (detalle) => detalle.id === detalle_id
            );
            window.detalles_cotizaciones[indice_detalle] = detalle;

            if(detener==false){
                AjaxCalcularMargenInverso(id,detalle_id, monto, variable,contador, detener);
            }else{
                notify("Resultados Actualizados", "success");
                renderTablasResultados();
                resaltarFila(detalle_id);
            }
            
        },
    });
}

function activarListeners(tipo_formulario = "creacion") {
    // Restablecer listeners
    // if(tipo_formulario=="creacion"){

    // }else{

    // }
    maquila.triggerHandler("change");
    tipo_producto.triggerHandler("change");
    armado_automatico.triggerHandler("change");
    proceso.triggerHandler("change");
}

let tipo_detalle = $("#tipo_detalle_id");

// Al abrir modal se evalua si es para un nuevo detalle o para editar uno anterior
var global_id_detalle=0;
$("#modal-detalle-cotizacion").on("show.bs.modal", function (e) {
    
    
    let btn = $(e.relatedTarget); // e.related here is the element that opened the modal, specifically the row button
    let id = btn.data("id"); // this is how you get the of any `data` attribute of an element
    let detalle_cotizacion_id = $("#detalle_cotizacion_id");
    global_id_detalle=id;//asignamos detalle actual a variable global;
    // al abrir modal siempre limpiar multidestinos
    $("#newRow").html("");
   
   

    // Si el evento que activa el modal es el de crear cotizacion
    if (e.relatedTarget.id == "crear_precotizacion") {
        // Limpiar modal si al abrir el modal para crear un detalle habia anteriormente una edicion no terminada de otro detalle,

        if (
            detalle_cotizacion_id.val() != "" &&
            detalle_cotizacion_id.val() != null
        ) {
            limpiarFormDetalleCotizacion();
            detalle_cotizacion_id.val("");
            $("#divCargaMaterial").show();
            $("#titulo-form-detalle").html("Crear Detalle");

            $("#guardarDetalleCotizacion")
                .addClass("creacion")
                .removeClass("edicion");
            // debugger
            // Setear formulario a corrugado
            tipo_detalle
                .prop("disabled", false)
                .prop("readonly", false)
                .val(1)
                .selectpicker("refresh")
                .change();
        }
        // debugger
        tipo_detalle.val(1).change();
        activarListeners();
        // Mostrar calculos hc

        $(".calculo-hc-div").addClass("col-8").removeClass("col-12");
        $(".calculo-hc-boton").show();
        return;
    }

    $("#guardarDetalleCotizacion").removeClass("creacion").addClass("edicion");
    // De lo contrario tomtamos el id para cargar el detalle al formulario de edicion
    setFormDetalleCotizacion(id);
    $("#titulo-form-detalle").html("Editar Detalle ID " + id);
    detalle_cotizacion_id.val(id);
    $("#divCargaMaterial").hide();
    // activarListeners();

    // Ocultar calculos hc
    $(".calculo-hc-div").addClass("col-12").removeClass("col-8");
    $(".calculo-hc-boton").hide();
});

function setFormDetalleCotizacion(detalle_id) {
    let detalle = window.detalles_cotizaciones.find(
        (detalle) => detalle.id === detalle_id
    );
    switch (detalle.tipo_detalle_id) {
        case 1:
            tipo_detalle.val(1).change();
            populate_corrugado(detalle);

            break;
        case 2:
            tipo_detalle.val(2).change();
            populate_esquinero(detalle);
            break;
        default:
            break;
    }

    tipo_detalle.prop("disabled", true).prop("readonly", true);
    return detalle;
}

async function populate_corrugado(detalle) {
    
    $.each(detalle, function (name, val) {
        // console.log(name, val);
        var $el = $('[name="' + name + '"]');
        var type = $el.attr("type");
        switch (type) {
            case "checkbox":
                $el.attr("checked", "checked");
                break;
            case "radio":
                $el.filter('[value="' + val + '"]').attr("checked", "checked");
                break;
            default:
                if ($el.is("select")) {
                    impresion;
                    
                    //console.log($el, val);
                    $el.val(val).selectpicker("refresh");
                } else {
                    $el.val(val);
                }
        }
    });
    await maquila.triggerHandler("change");
    await tipo_producto.triggerHandler("change");

    await armado_automatico.triggerHandler("change");
    if (detalle.maquila == 1) {
        maquila_servicio_id
            .prop("disabled", false)
            .prop("readonly", false)
            .val(detalle.maquila_servicio_id)
            .selectpicker("refresh")
            .change();
        // Si hay detalle seleccionamos el especifico de lo contrario seleccionamos todos
        if (detalle.detalle_maquila_servicio_id) {
            detalle_maquila_servicio_id
                .prop("disabled", false)
                .prop("readonly", false)
                .val(detalle.detalle_maquila_servicio_id)
                .selectpicker("refresh");
        } else {
            detalle_maquila_servicio_id
                .prop("disabled", false)
                .prop("readonly", false)
                .selectpicker("selectAll")
                .selectpicker("refresh");
        }
    }
    if (detalle.armado_automatico == 1) {
        armado_usd_caja
            .prop("disabled", false)
            .prop("readonly", false)
            .val(detalle.armado_usd_caja);
    }
    proceso.triggerHandler("change");
    carton.triggerHandler("change");
    numero_colores.triggerHandler("change");
    // Solo si tiene subsubjerarquia cargamos el arbol completo
    if (detalle.subsubhierarchy_id) {
        // console.log(detalle);
        populateHierarchies(detalle);
    }
    // console.log(detalle.impresion);
    if (
        detalle.impresion &&
        detalle.impresion != 0 &&
        detalle.impresion != 25 &&
        detalle.impresion != 50 &&
        detalle.impresion != 75 &&
        detalle.impresion != 100
    ) {
        data = `<option value="" disabled selected>Seleccionar Opción</option><option value="${detalle.impresion}"> ${detalle.impresion}</option><option value="0"> 0</option><option value="25"> 25</option><option value="50"> 50</option><option value="75"> 75</option><option value="100"> 100</option>`;
    } else {
        data = `<option value="" disabled selected>Seleccionar Opción</option><option value="0"> 0</option><option value="25"> 25</option><option value="50"> 50</option><option value="75"> 75</option><option value="100"> 100</option>`;
    }
    $("#impresion").empty().append(data).selectpicker("refresh");
    $("#impresion").selectpicker("val", detalle.impresion).change();
    $("#bct_min_kg").prop("readonly", true);
    $("#barniz").val(detalle.barniz).selectpicker("refresh");
    $("#barniz_type_id").val(detalle.barniz_type_id).selectpicker("refresh");
    // console.log(impresion        $("#impresion").val());
    if(detalle.carton.provisional==1){
       $("#carton_id").selectpicker("val", detalle.carton.carton_original_id);
    }

    if(detalle.printing_machine_id==5){ // Dong Fang
                          
        $("#numero_colores").html(
            `<option value="">Seleccionar...</option>
            <option value="0"> 0</option>
            <option value="1"> 1</option>
            <option value="2"> 2</option>
            <option value="3"> 3</option>
            <option value="4"> 4</option>
            <option value="5"> 5</option>`
        )
        .selectpicker("refresh");                   
       
        $("#print_type_id")
            .val(4)
            .prop("readonly",true)
            .selectpicker("refresh");
    }

    $("#numero_colores").val(detalle.numero_colores).selectpicker("refresh");

    if(detalle.barniz==1){
        $("#barniz").val(detalle.barniz).selectpicker("refresh");
        $("#barniz_type_id").val(detalle.barniz_type_id).selectpicker("refresh");
    }
}

function populate_esquinero(detalle) {
    // console.log(detalle, "detalle");
    $("#largo_esquinero").val(detalle.largo_esquinero);
    $("#carton_esquinero_id")
        .val(detalle.carton_esquinero_id)
        .selectpicker("refresh");
    $("#cantidad_esquinero").val(detalle.cantidad);
    $("#numero_colores_esquinero")
        .val(detalle.numero_colores)
        .selectpicker("refresh");
    $("#funda_esquinero").val(detalle.funda_esquinero).selectpicker("refresh");
    $("#tipo_destino_esquinero")
        .val(detalle.tipo_destino_esquinero)
        .selectpicker("refresh");
    $("#tipo_camion_esquinero")
        .val(detalle.tipo_camion_esquinero)
        .selectpicker("refresh");
    $("#clisse_esquinero").val(detalle.clisse).selectpicker("refresh");
    $("#maquila_esquinero").val(detalle.maquila).selectpicker("refresh");
    $("#ciudad_id").val(detalle.ciudad_id).selectpicker("refresh");

    $("#codigo_material_detalle").val(detalle.codigo_material_detalle);
    
    $("#descripcion_material_detalle").val(
        detalle.descripcion_material_detalle
    );
    $("#cad_material_detalle").val(detalle.cad_material_detalle);
    numero_colores_esquinero.triggerHandler("change");
}

// Popular jerarquias en orden
const populateHierarchies = async (detalle) => {
    await $("#hierarchy_id")
        .val(detalle.subsubhierarchy.subhierarchy.hierarchy_id)
        .triggerHandler("change");
    await $("#subhierarchy_id")
        .val(detalle.subsubhierarchy.subhierarchy_id)
        .triggerHandler("change");
    $("#subsubhierarchy_id")
        .val(detalle.subsubhierarchy_id)
        .selectpicker("refresh");
};

function defaultValues() {
    // Agregar valores por defectos
    // Inputs
   
    $("#golpes_largo,#golpes_ancho").val(1);
    $("#porcentaje_cera_interno,#porcentaje_cera_externo").val(0);
   // $("#porcentaje_cera_interno,#porcentaje_cera_externo,#coverage_type_id").prop("disabled", true);
    $("#tipo_camion_esquinero").selectpicker("val", 1);
    
    data = `<option value="" disabled selected>Seleccionar Opción</option><option value="0"> 0</option><option value="25"> 25</option><option value="50"> 50</option><option value="75"> 75</option><option value="100"> 100</option>`;
    $("#impresion").empty().append(data).selectpicker("refresh");

    // Selects
    $("#devolucion_pallets,#tipo_medida,#stretch_film").selectpicker("val", 1);
    $("#pallet").selectpicker("val", 2);
    $("#ajuste_precios,#clisse,#armado_automatico,#royalty,#funda,#impresion").selectpicker("val", 0);
    // if(!window.impresion){
    $("#desgajado_cabezal").prop("disabled", true).prop("readonly", true).selectpicker("refresh")
    $("#ensamblado").prop("disabled", true).prop("readonly", true).selectpicker("refresh").triggerHandler("change");
    $("#maquila").prop("disabled", true).prop("readonly", true).val(0).selectpicker("refresh");
  
    // }
}
function limpiarFormDetalleCotizacion() {
    // limpiar inputs
    $(
        "#form-detalle-cotizacion .input_detalle_cotizacion .selectpicker,#form-detalle-cotizacion .input_detalle_cotizacion input"
    )
        .prop("disabled", false)
        .prop("readonly", false)
        .val("")
        .selectpicker("refresh");

    // Remover clases de errores de formulario
    $(".error").removeClass("error");

    // Agregar valores por defectos
    defaultValues();
}

var tableContainer = $("#listado-detalles tbody");
var tableResultadosDetalle = $("#listado-resultados-detalle tbody");
var tableCostosDetalle = $("#listado-costos-detalle tbody");
var tableCostosServicios = $("#listado-costos-servicios-detalle tbody");
var tableNuevosDetalles = $("#listado-nuevos-detalle tbody");
function renderTable() {
    
    // console.log(window.deta);
    if (window.detalles_cotizaciones.length > 0) {
        $("#paso_dos").css("background-color", " #7ae091");
    } else {
        $("#paso_dos").css("background-color", " #fff");
    }
    // debugger;
    var listadoDetalles = window.detalles_cotizaciones
        .map(function (detalle, index) {
            // console.log("creado detalle " + detalle.id);
            // console.log(detalle);
            let tipo_detalle = detalle.tipo_detalle_id;
            let area_hc, carton, proceso;

            

            switch (tipo_detalle) {
                case 1:
                    area_hc = redondeo(detalle.area_hc);
                    if(detalle.carton.provisional==1){
                        carton = detalle.carton.carton_original;
                    }else{
                        carton = detalle.carton.codigo;
                    }                    
                    proceso = detalle.proceso.descripcion; 
                    pegado =
                        detalle.pegado_terminacion != null
                            ? { 0: "NO", 1: "SI" }[detalle.pegado_terminacion]
                            : "";
                    golpes_ancho = detalle.golpes_ancho;
                    golpes_largo = detalle.golpes_largo;
                    impresion = detalle.impresion ? detalle.impresion + "%" : 0;
                    porcentaje_cera =
                        detalle.porcentaje_cera_interno +
                        detalle.porcentaje_cera_externo +
                        "%";
                    break;
                case 2:
                    area_hc = "";
                    carton = detalle.carton_esquinero.codigo;
                    proceso = "";
                    pegado = "";
                    golpes_ancho = "";
                    golpes_largo = "";
                    impresion = "";
                    porcentaje_cera = "";
                    break;
                default:
                    break;
            }
            let acciones = `
            <a href="#" class=" modalVerDetalle" data-id="${detalle.id}" data-toggle="modal" data-target="#modal-detalle-cotizacion">
            <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
            </a>
            <a id="eliminar-detalle-cotizacion" data-toggle="modal" data-target="#modal-eliminar-detalle" href="#" class=" modalVerDetalle" data-id="${detalle.id}" ><div class="material-icons md-14" data-toggle="tooltip" title="Eliminar">delete</div></a>
            `;

            if (
                detalle.cotizacion &&
                (detalle.cotizacion.estado_id != 1 ||
                    window.user.id != detalle.cotizacion.user_id)
            ) {
                acciones = `
                <a href="#" class=" modalVerDetalle" data-id="${detalle.id}" data-toggle="modal" data-target="#modal-detalle-cotizacion">
                <div class="material-icons md-14" data-toggle="tooltip" title="Ver">search</div>
                </a>
                `;
            }
            if (
                detalle.cotizacion &&
                window.user.id == detalle.cotizacion.user_id
            ) {
                acciones += `<a href="#" class="" data-id="${detalle.id}" data-toggle="modal" data-target="#modal-detalle-a-ot"><div class="material-icons md-14" data-toggle="tooltip" title="Enviar a Órden de Trabajo">note_alt</div> </a>`;
            }

            // Verficar estado de no tenerlo mostrar opciones de ganada o perdida
            let estado = "--";

            if (detalle.cotizacion && detalle.cotizacion.estado_id == 3) {
                if (!detalle.estado) {
                    estado = `<a class="marcar-ganado-detalle-cotizacion"  href="#"  data-id="${detalle.id}" ><div class="material-icons md-14" data-toggle="tooltip" title="Marcar Ganado">thumb_up</div></a>
                    <a class="marcar-perdido-detalle-cotizacion"  href="#"  data-id="${detalle.id}" ><div style="color:red" class="material-icons md-14" data-toggle="tooltip" title="Marcar Perdido">thumb_down</div></a>`;
                } else if (detalle.estado == 1) {
                    estado = `<a class="marcar-ganado-detalle-cotizacion"  href="#"  data-id="${detalle.id}" ><div style="" class="material-icons md-14" data-toggle="tooltip" title="Marcar Ganado" >thumb_up</div></a>
                    <a class="marcar-perdido-detalle-cotizacion"  href="#"  data-id="${detalle.id}" ><div style="opacity: 0.15;color:red" class="material-icons md-14" data-toggle="tooltip" title="Marcar Perdido">thumb_down</div></a>`;
                } else if (detalle.estado == 2) {
                    estado = `<a class="marcar-ganado-detalle-cotizacion"  href="#"  data-id="${detalle.id}" ><div style="opacity: 0.15;" class="material-icons md-14" data-toggle="tooltip" title="Marcar Ganado" >thumb_up</div></a>
                    <a class="marcar-perdido-detalle-cotizacion"  href="#"  data-id="${detalle.id}" ><div style="color:red" class="material-icons md-14" data-toggle="tooltip" title="Marcar Perdido">thumb_down</div></a>`;
                }
            }

            // <td>${pegado}</td>
            return `<tr>
            <td>${index + 1}</td>
            <td class="text-truncate" title="${
                detalle.descripcion_material_detalle
                    ? detalle.descripcion_material_detalle
                    : ""
            }" data-toggle="tooltip">${detalle.descripcion_material_detalle ? detalle.descripcion_material_detalle : ""}</td>
            <td >${
                detalle.cad_material_detalle ? detalle.cad_material_detalle : ""
            }</td>
                <td>${
                    detalle.tipo_detalle_id
                        ? {
                              1: "Corrug.",
                              2: "Esq.",
                              3: "Offset",
                              4: "Pulpa",
                          }[detalle.tipo_detalle_id]
                        : ""
                }</td>
                <td>${separadorMiles(detalle.cantidad)}</td>
                <td>${area_hc}</td>
                <td>${carton}</td>
                <td>${detalle.product_type.descripcion}</td>
                <td>${proceso}</td>
                
                <td>${detalle.numero_colores}</td>
                <td>${impresion}</td>
                <td>${porcentaje_cera}</td>
                
                <td>${
                    detalle.matriz != null
                        ? { 0: "NO", 1: "SI" }[detalle.matriz]
                        : ""
                }</td>
                <td>${
                    detalle.clisse != null
                        ? { 0: "NO", 1: "SI" }[detalle.clisse]
                        : ""
                }</td>
                <td>${
                    detalle.royalty != null
                        ? { 0: "NO", 1: "SI" }[detalle.royalty]
                        : ""
                }</td>
                <td>${
                    detalle.maquila != null
                        ? { 0: "NO", 1: "SI" }[detalle.maquila]
                        : ""
                }</td>
                <td>${
                    detalle.armado_automatico != null
                        ? { 0: "NO", 1: "SI" }[detalle.armado_automatico]
                        : ""
                }</td>
                <td style="color:#28a745">
                <a target="_blank" href="/gestionarOt/${
                    detalle.work_order_id
                }">
                ${detalle.work_order_id != null ? detalle.work_order_id : ""}
                </a>
                </td>
                <td id="marcas-detalle-${detalle.id}">${estado}</td>
                <td>${acciones}</td>
                </tr>`;
        })
        .join("");
    tableContainer.html(listadoDetalles);
}

var selectpickerPlantas = $(".selectpicker-plantas");
var botonSolicitarAprobacion = $("#solicitarAprobacion");
function renderTablasResultados() {
    // debugger;
    var listadoResultadosDetalle, listadoCostosDetalle, listadoCostosServicios, listadoNuevosDetalles;
    var montoTotalCotizacion = 0;
    var margen_sugerido_total = 0;
    var margen_ingresado_total = 0;
    var margen_papeles_total = 0;

    //Nuevos totales ponderados por la cantidad
    let totalCantidad = 0;
    let totalMargenUSDMm2 = 0;

    let classes;
    let errores = 0;
    let precio_mm2 = '';
    let margen_valor = '';
    let precio_un_valor = '';
    let array_margen_usd_mm2 = [];
    let array_margen_minimo_usd_mm2 = [];
    let array_margen_papeles_usd_mm2 = [];
    let array_precio_usd_mm2 = [];
    let array_precio_usd_ton = [];
    let array_precio_usd_un = [];
    let array_precio_un = [];
    let array_suma_mm2 = [];
    let array_suma_ton = [];
    let array_suma_cantidad = [];
    
    window.detalles_cotizaciones.map(function (detalle, index) {
        
        //console.log("creado detalle " + detalle.id);
        margen_sugerido_total += detalle.margen_sugerido;
        margen_papeles_total += detalle.margen_papeles;
        // console.log(parseFloat(detalle.margen));
        margen_ingresado_total += parseFloat(detalle.margen);
        let tipo_detalle = detalle.tipo_detalle_id;
        let area_hc, carton, proceso;
        if (
            detalle.cotizacion &&
            detalle.cotizacion.estado_id == 1 &&
            window.user.id == detalle.cotizacion.user_id
        ) {
            margen = `<input class="text-center margen-detalle"style="width:80%" type="text" id="margen_detalle_${
                detalle.id
            }" maxlength="10" name="margen_detalle_${
                detalle.id
            }" value="${separadorMiles(
                parseFloat(detalle.margen).toFixed(1)
            )}">`;
            if(!detalle.precios.precio_final){  
                precio_total_usd_mm2 = `<input class="text-center calcular-margen"style="width:80%" type="text" id="precio_total_usd_mm2_${
                    detalle.id
                }" maxlength="10" name="precio_total_usd_mm2_${
                    detalle.id
                }" value="${separadorMiles(
                    parseFloat(detalle.precios.total.usd_mm2).toFixed(1)
                )}">`;
            }else{
                precio_total_usd_mm2 = `<input class="text-center calcular-margen"style="width:80%" type="text" id="precio_total_usd_mm2_${
                    detalle.id
                }" maxlength="10" name="precio_total_usd_mm2_${
                    detalle.id
                }" value="${separadorMiles(
                    parseFloat(detalle.precios.precio_final.usd_mm2).toFixed(1)
                )}">`;
            }
           
            if(!detalle.precios.precio_final){  
                precio_total_usd_caja = `<input class="text-center calcular-margen"style="width:80%" type="text" id="precio_total_usd_caja_${
                    detalle.id
                }" maxlength="10" name="precio_total_usd_caja_${
                    detalle.id
                }" value="${redondeo(detalle.precios.precio_total.usd_caja)}">`;
            }else{
                precio_total_usd_caja = `<input class="text-center calcular-margen"style="width:80%" type="text" id="precio_total_usd_caja_${
                    detalle.id
                }" maxlength="10" name="precio_total_usd_caja_${
                    detalle.id
                }" value="${redondeo(detalle.precios.precio_final.usd_caja)}">`;
            }
            
            if(!detalle.precios.precio_final){  
                precio_total_clp_caja = `<input class="text-center calcular-margen"style="width:80%" type="text" id="precio_total_clp_caja_${
                    detalle.id
                }" maxlength="10" name="precio_total_clp_caja_${
                    detalle.id
                }" value="${separadorMiles(
                    parseFloat(detalle.precios.precio_total.clp_caja).toFixed(1)
                )}">`;
            }else{
                precio_total_clp_caja = `<input class="text-center calcular-margen"style="width:80%" type="text" id="precio_total_clp_caja_${
                    detalle.id
                }" maxlength="10" name="precio_total_clp_caja_${
                    detalle.id
                }" value="${separadorMiles(
                    parseFloat(detalle.precios.precio_final.clp_caja).toFixed(1)
                )}">`;
            }
         

            precio_mm2 = $(`input#precio_total_usd_mm2_${detalle.id}`).val();
            margen_valor = $(`input#margen_detalle_${detalle.id}`).val();
            precio_un_valor = $(`input#precio_total_clp_caja_${detalle.id}`).val();


        } else {
            margen = detalle.margen;

            if(!detalle.precios.precio_final){
                precio_total_usd_mm2 = separadorMiles(
                    parseFloat(detalle.precios.precio_total.usd_mm2).toFixed(1)
                );
            }else{
                precio_total_usd_mm2 = separadorMiles(
                    parseFloat(detalle.precios.precio_final.usd_mm2).toFixed(1)
                );
            }   

            if(!detalle.precios.precio_final){   
                precio_total_usd_caja = redondeo(
                    detalle.precios.precio_total.usd_caja
                );
            }else{
                precio_total_usd_caja = redondeo(
                    detalle.precios.precio_final.usd_caja
                );
            }     

            if(!detalle.precios.precio_final){  
                precio_total_clp_caja = separadorMiles(
                    parseFloat(detalle.precios.precio_total.clp_caja).toFixed(1)
                );
            }else{
                precio_total_clp_caja = separadorMiles(
                    parseFloat(detalle.precios.precio_final.clp_caja).toFixed(1)
                );
            }
            

            precio_mm2 = precio_total_usd_mm2;
            margen_valor = detalle.margen;
            precio_un_valor = precio_total_clp_caja;
        }
        // console.log(detalle.cinta_desgarro);
        // console.log(detalle.print_type_id);
        switch (tipo_detalle) {

            case 1:
                area_hc = redondeo(detalle.area_hc);
                carton = detalle.carton.codigo;
                proceso = detalle.proceso.descripcion;
                pegado =
                    detalle.pegado_terminacion != null
                        ? { 0: "NO", 1: "SI" }[detalle.pegado_terminacion]
                        : "";
                golpes_ancho = detalle.golpes_ancho;
                golpes_largo = detalle.golpes_largo;
                impresion = detalle.impresion ? detalle.impresion + "%" : null;
                porcentaje_cera =
                    detalle.porcentaje_cera_interno +
                    detalle.porcentaje_cera_externo +
                    "%";
                if (
                    detalle.process_id != 7 &&
                    detalle.process_id != 9 &&
                    detalle.porcentaje_cera_interno +
                        detalle.porcentaje_cera_externo <=
                        0 &&
                    detalle.cotizacion &&
                    detalle.cotizacion.estado_id == 1 &&
                    window.user.id == detalle.cotizacion.user_id
                ) {
                    //Se habilita la planta Osorno para cuando cinta desgarro sea Si
                    if( detalle.cinta_desgarro != 1 && detalle.print_type_id != 2){  
                      
                        // if(detalle.print_type_id == 5 ){
                        if(detalle.printing_machine_id == 4 || detalle.printing_machine_id == 5 || detalle.product_type_id == 18 || detalle.product_type_id == 36 || detalle.product_type_id == 35 || detalle.product_type_id == 8){

                            //Solo la impresion alta gráfica esta disponible para la planta buin
                            planta = `
                                <select name="planta_detalle_${
                                    detalle.id
                                }" id="planta_detalle_${
                                    detalle.id
                                }" class="selectpicker-plantas">
                                <option value="1" ${
                                    detalle.planta_id == 1 ? 'selected="selected"' : null
                                }>Buin</option>
                                </select>`;


                        }else{

                            if(detalle.printing_machine_id == 3){
                                //Solo la impresion interna esta disponible para la planta til til
                                planta = `
                                <select name="planta_detalle_${
                                    detalle.id
                                }" id="planta_detalle_${
                                    detalle.id
                                }" class="selectpicker-plantas">
                                <option value="2" ${
                                    detalle.planta_id == 1 ? 'selected="selected"' : null
                                }>Til Til</option>
                                </select>`;

                            }else{

                                planta = `
                                    <select name="planta_detalle_${
                                        detalle.id
                                    }" id="planta_detalle_${
                                        detalle.id
                                    }" class="selectpicker-plantas">
                                    <option value="1" ${
                                        detalle.planta_id == 1 ? 'selected="selected"' : null
                                    } >Buin</option>
                                    <option value="2" ${
                                        detalle.planta_id == 2 ? 'selected="selected"' : null
                                    }>Til Til</option>
                                    <option value="3" ${
                                        detalle.planta_id == 3 ? 'selected="selected"' : null
                                    }>Osorno</option>
                                    </select>`;
                            }
                        }

                    }else if( detalle.printing_machine_id == 4 || detalle.printing_machine_id == 5 || detalle.product_type_id == 18 || detalle.product_type_id == 36 || detalle.product_type_id == 35 || detalle.product_type_id == 8){//else if( detalle.print_type_id == 2 ){
                        
                        //Solo la impresion alta gráfica esta disponible para la planta buin
                        planta = `
                        <select name="planta_detalle_${
                            detalle.id
                        }" id="planta_detalle_${
                            detalle.id
                        }" class="selectpicker-plantas">
                        <option value="1" ${
                            detalle.planta_id == 1 ? 'selected="selected"' : null
                        }>Buin</option>
                        </select>`;

                    }else{
                        
                        if(detalle.printing_machine_id == 3){
                            //Solo la impresion interna esta disponible para la planta til til
                            planta = `
                            <select name="planta_detalle_${
                                detalle.id
                            }" id="planta_detalle_${
                                detalle.id
                            }" class="selectpicker-plantas">
                            <option value="2" ${
                                detalle.planta_id == 1 ? 'selected="selected"' : null
                            }>Til Til</option>
                            </select>`;

                        }else{

                            planta = `
                                <select name="planta_detalle_${
                                    detalle.id
                                }" id="planta_detalle_${
                                    detalle.id
                                }" class="selectpicker-plantas">
                                <option value="1" ${
                                    detalle.planta_id == 1 ? 'selected="selected"' : null
                                } >Buin</option>
                                <option value="2" ${
                                    detalle.planta_id == 2 ? 'selected="selected"' : null
                                }>Til Til</option>
                                <option value="3" ${
                                    detalle.planta_id == 3 ? 'selected="selected"' : null
                                }>Osorno</option>
                                </select>`;
                        }
                    }

                    
                } else planta = detalle.planta.nombre;
                break;
            case 2:
                area_hc = "";
                carton = detalle.carton_esquinero.codigo;
                proceso = "";
                pegado = "";
                golpes_ancho = "";
                golpes_largo = "";
                impresion = "";
                porcentaje_cera = "";
                planta = detalle.planta.nombre;
                break;
            default:
                break;
        }

        //Tabla resumen de resultados
        if(!detalle.precios.precio_final){
            listadoResultadosDetalle += `<tr class="row_detalle_${detalle.id}">
            <td>${index + 1}</td>
            <td class="text-truncate" title="${
                detalle.descripcion_material_detalle
                    ? detalle.descripcion_material_detalle
                    : ""
            }" data-toggle="tooltip">${
                detalle.descripcion_material_detalle
                    ? detalle.descripcion_material_detalle
                    : ""
            }</td>
            <td >${
                detalle.cad_material_detalle ? detalle.cad_material_detalle : ""
            }</td>
                <td>${planta}</td>
                    <td>${
                        detalle.tipo_detalle_id
                            ? {
                                  1: "Corrug.",
                                  2: "Esq.",
                                  3: "Offset",
                                  4: "Pulpa",
                              }[detalle.tipo_detalle_id]
                            : ""
                    }</td>
                    <td>${detalle.product_type.descripcion}</td>
                    <td>${carton}</td>
                    
                    <td>${detalle.flete.ciudad}</td>
                    <td>${detalle.margen_papeles}</td>
                    <td>${margen}</td>
                    <td>${detalle.margen_sugerido}</td>
                    <td>${precio_total_usd_mm2}</td>
                    <td>${separadorMiles(
                        parseFloat(detalle.precios.precio_total.usd_ton).toFixed(1)
                    )}</td>
                    <td>${precio_total_usd_caja}</td>
                    <td>${precio_total_clp_caja}</td>
                    <td>${separadorMiles(detalle.cantidad)}</td>
                    <td>${redondeo(
                        (detalle.precios.precio_total.usd_caja * detalle.cantidad) /
                            1000
                    )}</td>
                    </tr>`;
        }else{
            listadoResultadosDetalle += `<tr class="row_detalle_${detalle.id}">
            <td>${index + 1}</td>
            <td class="text-truncate" title="${
                detalle.descripcion_material_detalle
                    ? detalle.descripcion_material_detalle
                    : ""
            }" data-toggle="tooltip">${
                detalle.descripcion_material_detalle
                    ? detalle.descripcion_material_detalle
                    : ""
            }</td>
            <td >${
                detalle.cad_material_detalle ? detalle.cad_material_detalle : ""
            }</td>
                <td>${planta}</td>
                    <td>${
                        detalle.tipo_detalle_id
                            ? {
                                  1: "Corrug.",
                                  2: "Esq.",
                                  3: "Offset",
                                  4: "Pulpa",
                              }[detalle.tipo_detalle_id]
                            : ""
                    }</td>
                    <td>${detalle.product_type.descripcion}</td>
                    <td>${carton}</td>
                    
                    <td>${detalle.flete.ciudad}</td>
                    <td>${detalle.margen_papeles}</td>
                    <td>${margen}</td>
                    <td>${detalle.margen_sugerido}</td>
                    <td>${precio_total_usd_mm2}</td>
                    <td>${separadorMiles(
                        parseFloat(detalle.precios.precio_final.usd_ton).toFixed(1)
                    )}</td>
                    <td>${precio_total_usd_caja}</td>
                    <td>${precio_total_clp_caja}</td>
                    <td>${separadorMiles(detalle.cantidad)}</td>
                    <td>${redondeo(
                        (detalle.precios.precio_final.usd_caja * detalle.cantidad) /
                            1000
                    )}</td>
                    </tr>`;
        }
       
        if (detalle.precios.costo_flete.usd_mm2 <= 0 && detalle.flete.ciudad !== 'RETIRO EN PLANTA') {
            classes = "error-costeo";
            errores++;
        } else {
            classes = "";
        }

        //Se calcula primero el precio por 1000
        var precio_caja = 0;
        if(!detalle.precios.precio_final){
            precio_caja = redondeo((detalle.precios.precio_total.usd_caja * detalle.cantidad) / 1000);
        }else{
            precio_caja = redondeo((detalle.precios.precio_final.usd_caja * detalle.cantidad) / 1000);
        }
       
        let precio_total_musd = precio_caja.replace(/\./g,'').replace(/,/g, '.') * 1000;
        
        //Datos para calcular el valor mm2 para el calculo final
        let precio_usd_mm2 = precio_mm2 ? precio_mm2.replace(/\./g,'').replace(/,/g, '.') : '';
        let valor_mm2 = redondeo(precio_total_musd / precio_usd_mm2);
        let parseValorMm2 = parseFloat(valor_mm2);
        if(valor_mm2 !=0){
            
            parseValorMm2 = parseFloat(valor_mm2.replace(/,/g, '.'));
        }
        
        
        //Datos para calcular el valor ton para el calculo final 
        var precio_ton=0;
        if(!detalle.precios.precio_final){
            precio_ton = separadorMiles(parseFloat(detalle.precios.precio_total.usd_ton).toFixed(1));
        }else{
            precio_ton = separadorMiles(parseFloat(detalle.precios.precio_final.usd_ton).toFixed(1));
        }
        
        let precio_usd_ton = precio_ton ? precio_ton.replace(/\./g,'').replace(/,/g, '.') : ''; //Se quita el punto y despues se remplaza la coma por punto
        let valor_ton = redondeo(precio_total_musd / precio_usd_ton);
        
        let parseValorTon = parseFloat(valor_ton);
        if(valor_ton !=0){
            
            parseValorTon = parseFloat(valor_ton.replace(/,/g, '.'));
        }        

        let margen_usd_mm2 = margen_valor ? parseFloat(margen_valor.replace(/\./g,'').replace(/,/g, '.')) : '';

        let margen_minimo_usd_mm2 = detalle.margen_sugerido;
        let margen_papeles_usd_mm2 = detalle.margen_papeles;

        var precio_usd_un_valor = '';
        if(!detalle.precios.precio_final){
            precio_usd_un_valor = redondeo(detalle.precios.precio_total.usd_caja);
        }else{
            precio_usd_un_valor = redondeo(detalle.precios.precio_final.usd_caja);
        }
        
        let precio_usd_un = precio_usd_un_valor ? parseFloat(precio_usd_un_valor.replace(/\./g,'').replace(/,/g, '.')) : '';

        let cantidad_valor = separadorMiles(detalle.cantidad);
        let cantidad_detalle = cantidad_valor ? parseFloat(cantidad_valor.replace(/\./g,'').replace(/,/g, '.')) : '';

        let precio_un = precio_un_valor ? parseFloat(precio_un_valor.replace(/\./g,'').replace(/,/g, '.')) : '';

        //--------------Llena los arreglos
        array_margen_usd_mm2.push(parseFloat(margen_usd_mm2) * parseValorMm2);
        array_margen_minimo_usd_mm2.push(margen_minimo_usd_mm2 * parseValorMm2);
        array_margen_papeles_usd_mm2.push(margen_papeles_usd_mm2 * parseValorMm2);
        array_precio_usd_mm2.push(parseFloat(precio_usd_mm2) * parseValorMm2);
        array_precio_usd_ton.push(parseFloat(precio_usd_ton) * parseValorTon);
        array_precio_usd_un.push(parseFloat(precio_usd_un) * cantidad_detalle);
        array_precio_un.push(parseFloat(precio_un) * cantidad_detalle);

        array_suma_mm2.push(parseValorMm2);
        array_suma_ton.push(parseValorTon);
        array_suma_cantidad.push(cantidad_detalle)

        var costo_fijo_total = 0
        if(!detalle.precios.costo_fijo_total){
            costo_fijo_total=0;
        }else{
            costo_fijo_total = detalle.precios.costo_fijo_total.usd_mm2;
        }

        var costo_servir_sin_flete = 0
        if(!detalle.precios.costo_servir_sin_flete){
            costo_servir_sin_flete=0;
        }else{
            costo_servir_sin_flete = detalle.precios.costo_servir_sin_flete.usd_mm2;
        }

        var costo_administrativos = 0
        if(!detalle.precios.costo_administrativos){
            costo_administrativos=0;
        }else{
            costo_administrativos = detalle.precios.costo_administrativos.usd_mm2;
        }

        //Tabla de listado costo detalle
        listadoCostosDetalle += `<tr class="row_detalle_${detalle.id} " >
        <td>${index + 1}</td>
        <td class="text-truncate" title="${
            detalle.descripcion_material_detalle
                ? detalle.descripcion_material_detalle
                : ""
        }" data-toggle="tooltip">${
            detalle.descripcion_material_detalle
                ? detalle.descripcion_material_detalle
                : ""
        }</td>
        <td >${
            detalle.cad_material_detalle ? detalle.cad_material_detalle : ""
        }</td>
                    <td>${
                        detalle.tipo_detalle_id
                            ? {
                                  1: "Corrug.",
                                  2: "Esq.",
                                  3: "Offset",
                                  4: "Pulpa",
                              }[detalle.tipo_detalle_id]
                            : ""
                    }</td>
                    <td>${detalle.product_type.descripcion}</td>
                    <td>${carton}</td>
                    <td>${redondeo(detalle.precios.costo_directo.usd_mm2)}</td>
                    <td>${redondeo(
                        detalle.precios.costo_indirecto.usd_mm2
                    )}</td>
                    <td>${redondeo(detalle.precios.costo_gvv.usd_mm2)}</td>
                    <td>${redondeo(costo_fijo_total + costo_servir_sin_flete + costo_administrativos)}</td>
                    <td>${redondeo(detalle.precios.costo_total.usd_mm2)}</td>
                    </tr>`;
        
        //Tabla de listado costo servicios
        listadoCostosServicios += `<tr class="${classes} row_detalle_${
            detalle.id
        }" ${
            classes
                ? "data-toggle='tooltip' title='Este Detalle contiene un flete invalido, por favor verificar'"
                : null
        }>
        <td>${index + 1}</td>
        <td class="text-truncate" title="${
            detalle.descripcion_material_detalle
                ? detalle.descripcion_material_detalle
                : ""
        }" data-toggle="tooltip">${
            detalle.descripcion_material_detalle
                ? detalle.descripcion_material_detalle
                : ""
        }</td>
        <td >${
            detalle.cad_material_detalle ? detalle.cad_material_detalle : ""
        }</td>
                    <td>${
                        detalle.tipo_detalle_id
                            ? {
                                  1: "Corrug.",
                                  2: "Esq.",
                                  3: "Offset",
                                  4: "Pulpa",
                              }[detalle.tipo_detalle_id]
                            : ""
                    }</td>
                    <td>${detalle.product_type.descripcion}</td>
                    <td>${carton}</td>
                    <td>${redondeo(detalle.precios.costo_maquila.usd_mm2)}</td>
                    <td>${
                        detalle.tipo_detalle_id == 1
                            ? redondeo(detalle.precios.costo_armado.usd_mm2)
                            : "N/A"
                    }</td>
                    <td>${redondeo(detalle.precios.costo_clisses.usd_mm2)}</td>
                    <td>${
                        detalle.tipo_detalle_id == 1
                            ? redondeo(detalle.precios.costo_matriz.usd_mm2)
                            : "N/A"
                    }</td>
                    <td>${
                        detalle.tipo_detalle_id == 1
                            ? redondeo(detalle.precios.costo_mano_de_obra.usd_mm2)
                            : "N/A"
                    }</td>
                    <td>${redondeo(detalle.precios.costo_flete.usd_mm2)}</td>
                    </tr>`;
        

        if(window.user.role_id==15 || window.user.role_id==2){
             //Tabla de listado nuevos detalles
             if(!detalle.precios.precio_final){
                listadoNuevosDetalles += `<tr class="row_detalle_${detalle.id} " >
                <td>
                    ${index + 1}
                </td>
                <td class="text-truncate" title="${
                    detalle.descripcion_material_detalle
                        ? detalle.descripcion_material_detalle
                        : ""}
                    " data-toggle="tooltip">${
                        detalle.descripcion_material_detalle
                        ? detalle.descripcion_material_detalle
                        : ""}
                </td>
                <td>
                    ${detalle.cad_material_detalle ? detalle.cad_material_detalle : ""}
                </td>
                <td>${
                    detalle.tipo_detalle_id
                        ? {
                            1: "Corrug.",
                            2: "Esq.",
                            3: "Offset",
                            4: "Pulpa",
                        }[detalle.tipo_detalle_id]
                        : ""
                    }
                </td>
                <td>
                    ${detalle.product_type.descripcion}
                </td>
                <td>
                    ${carton}
                </td>
                <td>
                    ${parseFloat(detalle.margen) - parseFloat(detalle.margen_sugerido)}
                </td>
                <td>
                    ${redondeo(parseFloat(costo_fijo_total) + 
                               parseFloat(detalle.margen) + 
                               parseFloat(costo_servir_sin_flete) + 
                               parseFloat(costo_administrativos))}
                </td>
                <td>
                    ${parseFloat(detalle.margen) + 
                      parseFloat(costo_servir_sin_flete) + 
                      parseFloat(costo_administrativos)}
                </td>
                <td>
                    ${redondeo(((parseFloat(detalle.margen) + 
                                 parseFloat(costo_servir_sin_flete)+ 
                                 parseFloat(costo_administrativos)) / 
                                 (parseFloat(detalle.precios.precio_total.usd_mm2) + 
                                 (parseFloat(detalle.margen) - parseFloat(detalle.margen_sugerido))))*100)}
                </td>
                <td>
                    ${parseFloat(detalle.margen) + parseFloat(costo_administrativos)}
                </td>
                <td>
                    ${redondeo(((parseFloat(detalle.margen) + 
                                 parseFloat(costo_administrativos)) / 
                                 (parseFloat(detalle.precios.precio_total.usd_mm2) + 
                                 (parseFloat(detalle.margen) - parseFloat(detalle.margen_sugerido))))*100)}
                </td>
                <td>
                    ${parseFloat(detalle.margen)}
                </td>
                <td>${redondeo(parseFloat(detalle.margen)/(parseFloat(detalle.precios.precio_total.usd_mm2))*100)}
                </td>
                </tr>`;
             }else{
                listadoNuevosDetalles += `<tr class="row_detalle_${detalle.id} " >
                <td>
                    ${index + 1}
                </td>
                <td class="text-truncate" title="${
                    detalle.descripcion_material_detalle
                        ? detalle.descripcion_material_detalle
                        : ""}
                    " data-toggle="tooltip">${
                        detalle.descripcion_material_detalle
                        ? detalle.descripcion_material_detalle
                        : ""}
                </td>
                <td>
                    ${detalle.cad_material_detalle ? detalle.cad_material_detalle : ""}
                </td>
                <td>${
                    detalle.tipo_detalle_id
                        ? {
                            1: "Corrug.",
                            2: "Esq.",
                            3: "Offset",
                            4: "Pulpa",
                        }[detalle.tipo_detalle_id]
                        : ""
                    }
                </td>
                <td>
                    ${detalle.product_type.descripcion}
                </td>
                <td>
                    ${carton}
                </td>
                <td>
                    ${parseFloat(detalle.margen) - parseFloat(detalle.margen_sugerido)}
                </td>
                <td>
                    ${redondeo(parseFloat(costo_fijo_total) + 
                               parseFloat(detalle.margen) + 
                               parseFloat(costo_servir_sin_flete) + 
                               parseFloat(costo_administrativos))}
                </td>
                <td>
                    ${parseFloat(detalle.margen) + 
                      parseFloat(costo_servir_sin_flete) + 
                      parseFloat(costo_administrativos)}
                </td>
                <td>
                    ${redondeo(((parseFloat(detalle.margen) + 
                                 parseFloat(costo_servir_sin_flete)+ 
                                 parseFloat(costo_administrativos)) / 
                                 (parseFloat(detalle.precios.precio_final.usd_mm2) + 
                                 (parseFloat(detalle.margen) - parseFloat(detalle.margen_sugerido))))*100)}
                </td>
                <td>
                    ${parseFloat(detalle.margen) + parseFloat(costo_administrativos)}
                </td>
                <td>
                    ${redondeo(((parseFloat(detalle.margen) + 
                                 parseFloat(costo_administrativos)) / 
                                 (parseFloat(detalle.precios.precio_final.usd_mm2) + 
                                 (parseFloat(detalle.margen) - parseFloat(detalle.margen_sugerido))))*100)}
                </td>
                <td>
                    ${parseFloat(detalle.margen)}
                </td>
                <td>${redondeo(parseFloat(detalle.margen)/(parseFloat(detalle.precios.precio_final.usd_mm2))*100)}
                </td>
                </tr>`;
             }
            
           
        }else{
            //Tabla de listado nuevos detalles
            if(!detalle.precios.precio_final){
                listadoNuevosDetalles += `<tr class="row_detalle_${detalle.id} " >
                <td>
                    ${index + 1}
                </td>
                <td class="text-truncate" title="${
                    detalle.descripcion_material_detalle
                        ? detalle.descripcion_material_detalle
                        : ""}
                    " data-toggle="tooltip">${
                        detalle.descripcion_material_detalle
                        ? detalle.descripcion_material_detalle
                        : ""}
                </td>
                <td>
                    ${detalle.cad_material_detalle ? detalle.cad_material_detalle : ""}
                </td>
                <td>${
                    detalle.tipo_detalle_id
                        ? {
                            1: "Corrug.",
                            2: "Esq.",
                            3: "Offset",
                            4: "Pulpa",
                        }[detalle.tipo_detalle_id]
                        : ""
                    }
                </td>
                <td>
                    ${detalle.product_type.descripcion}
                </td>
                <td>
                    ${carton}
                </td>
                <td>
                    ${redondeo(parseFloat(costo_fijo_total) + 
                            parseFloat(detalle.margen) + 
                            parseFloat(costo_servir_sin_flete) + 
                            parseFloat(costo_administrativos))}
                </td>
                <td>
                    ${parseFloat(detalle.margen) + 
                        parseFloat(costo_servir_sin_flete) + 
                        parseFloat(costo_administrativos)}
                </td>
                <td>
                    ${parseFloat(detalle.margen) + parseFloat(costo_administrativos)}
                </td>
                
                <td>${redondeo(parseFloat(detalle.margen)/(parseFloat(detalle.precios.precio_total.usd_mm2))*100)
                    }
                </td>
                </tr>`;
            }else{
                listadoNuevosDetalles += `<tr class="row_detalle_${detalle.id} " >
                <td>
                    ${index + 1}
                </td>
                <td class="text-truncate" title="${
                    detalle.descripcion_material_detalle
                        ? detalle.descripcion_material_detalle
                        : ""}
                    " data-toggle="tooltip">${
                        detalle.descripcion_material_detalle
                        ? detalle.descripcion_material_detalle
                        : ""}
                </td>
                <td>
                    ${detalle.cad_material_detalle ? detalle.cad_material_detalle : ""}
                </td>
                <td>${
                    detalle.tipo_detalle_id
                        ? {
                            1: "Corrug.",
                            2: "Esq.",
                            3: "Offset",
                            4: "Pulpa",
                        }[detalle.tipo_detalle_id]
                        : ""
                    }
                </td>
                <td>
                    ${detalle.product_type.descripcion}
                </td>
                <td>
                    ${carton}
                </td>
                <td>
                    ${redondeo(parseFloat(costo_fijo_total) + 
                            parseFloat(detalle.margen) + 
                            parseFloat(costo_servir_sin_flete) + 
                            parseFloat(costo_administrativos))}
                </td>
                <td>
                    ${parseFloat(detalle.margen) + 
                        parseFloat(costo_servir_sin_flete) + 
                        parseFloat(costo_administrativos)}
                </td>
                <td>
                    ${parseFloat(detalle.margen) + parseFloat(costo_administrativos)}
                </td>
                
                <td>${redondeo(parseFloat(detalle.margen)/(parseFloat(detalle.precios.precio_final.usd_mm2))*100)
                    }
                </td>
                </tr>`;
            }
            
        }        
            
       

        // totalMargenUSDMm2 += (margen_usd_ton * valor_mm2 ) / valor_mm2;
            
        totalCantidad += detalle.cantidad;
        if(!detalle.precios.precio_final){
            montoTotalCotizacion += detalle.precios.precio_total.usd_caja * detalle.cantidad;
        }else{
            montoTotalCotizacion += detalle.precios.precio_final.usd_caja * detalle.cantidad;
        }
        return;
    });

    //--- Total calculo suma de MM2 y TON
    const reducer = (previousValue, currentValue) => previousValue + currentValue;
    const total_suma_mm2 = array_suma_mm2.reduce(reducer);
    const total_suma_ton = array_suma_ton.reduce(reducer);
    const total_suma_cantidad = array_suma_cantidad.reduce(reducer);
    
    //---------------------Total de margen USD/MM2
    const total_calculo_margen_usd_mm2 = array_margen_usd_mm2.reduce(reducer);
    const valor_margen_usd_mm2 = (total_calculo_margen_usd_mm2 / total_suma_mm2).toFixed(2);

    //---------------------Total de margen minimo USD/MM2
    const total_calculo_margen_minimo_usd_mm2 = array_margen_minimo_usd_mm2.reduce(reducer);
    const valor_margen_minimo_usd_mm2 = (total_calculo_margen_minimo_usd_mm2 / total_suma_mm2).toFixed(1);

    //---------------------Total de margen minimo USD/MM2
    const total_calculo_margen_papeles_usd_mm2 = array_margen_papeles_usd_mm2.reduce(reducer);
    const valor_margen_papeles_usd_mm2 = (total_calculo_margen_papeles_usd_mm2 / total_suma_mm2).toFixed(1);

     //---------------------Total de precio USD/MM2
     const total_calculo_precio_usd_mm2 = array_precio_usd_mm2.reduce(reducer);
     const valor_precio_usd_mm2 = (total_calculo_precio_usd_mm2 / total_suma_mm2).toFixed(1);

    //---------------------Total de precio USD/TON
    const total_calculo_precio_usd_ton = array_precio_usd_ton.reduce(reducer);
    const valor_precio_usd_ton = (total_calculo_precio_usd_ton / total_suma_ton).toFixed(1);
     
    //---------------------Total de precio USD/UN
    const total_calculo_precio_usd_un = array_precio_usd_un.reduce(reducer);
    const valor_precio_usd_un = (total_calculo_precio_usd_un / total_suma_cantidad).toFixed(3);
     
    //---------------------Total de precio $/UN
    const total_calculo_precio_un = array_precio_un.reduce(reducer);
    const valor_precio_un = (total_calculo_precio_un / total_suma_cantidad).toFixed(1);

    listadoResultadosDetalle += `
    <tr>
        <td colspan="8" class="text-right" style="font-size:17px;font-weight:bold;padding-right: 20px;">Monto Total Cotización (MUSD): </td>
        <td style="color: #025902;font-size:13px">${separadorMiles(valor_margen_papeles_usd_mm2)}</td>
        <td style="color: #025902;font-size:13px">${separadorMiles(valor_margen_usd_mm2)}</td>
        <td style="color: #025902;font-size:13px">${separadorMiles(valor_margen_minimo_usd_mm2)}</td>
        <td style="color: #025902;font-size:13px">${separadorMiles(valor_precio_usd_mm2)}</td>
        <td style="color: #025902;font-size:13px">${separadorMiles(valor_precio_usd_ton)}</td>
        <td style="color: #025902;font-size:13px">${separadorMiles(valor_precio_usd_un)}</td>
        <td style="color: #025902;font-size:13px">${separadorMiles(valor_precio_un)}</td>
        <td style="color: #025902;font-size:13px">${separadorMiles(totalCantidad)}</td>
        <td style="font-weight:bold;font-size:17px;"> ${redondeo(montoTotalCotizacion / 1000)}</td>
    </tr>`;
    tableResultadosDetalle.html(listadoResultadosDetalle);
    tableCostosDetalle.html(listadoCostosDetalle);
    tableCostosServicios.html(listadoCostosServicios);
    tableNuevosDetalles.html(listadoNuevosDetalles);
    console.log("listo");

    // Si el usuario es jefe de venta entonces puede sobrepasarse hasta un 10% y ser aprobado
    if (margen_sugerido_total <= margen_ingresado_total) {
        botonSolicitarAprobacion
            .html("Finalizar Cotizacion")
            .addClass("btn-success")
            .removeClass("btn-warning");
    } else {
        botonSolicitarAprobacion
            .html("Solicitar Aprobación")
            .removeClass("btn-success")
            .addClass("btn-warning");
    }

    if (errores > 0) {
        botonSolicitarAprobacion.prop("disabled", true);
        botonSolicitarAprobacion.attr("data-toggle", "tooltip");
        botonSolicitarAprobacion.attr(
            "title",
            "Esta Cotización contiene uno o mas fletes invalidos, por favor verificar"
        );
        // ="tooltip" title=""
    } else {
        botonSolicitarAprobacion.prop("disabled", false);
        botonSolicitarAprobacion.attr("data-toggle", "tooltip");
        botonSolicitarAprobacion.attr("title", "");
    }
    $("#monto-total-modal").html(redondeo(montoTotalCotizacion / 1000));
    // Inicializar selectores
    //**$(".selectpicker-plantas").selectpicker("refresh");
}

Number.prototype.toFixedNoRound = function (precision = 3) {
    const factor = Math.pow(10, precision);
    return Math.floor(this * factor) / factor;
}
const redondeo = (value) => separadorMiles(parseFloat(value).toFixedNoRound());

var separadorMiles = function (num) {
    if (num) {
        num = num.toString().split(".");
        var coma = num[1];
        num = num[0];
        num = num
            .toString()
            .split("")
            .reverse()
            .join("")
            .replace(/(?=\d*\.?)(\d{3})/g, "$1.");
        num = num.split("").reverse().join("").replace(/^[\.]/, "");

        if (coma && coma != "") {
            num = num + "," + coma;
        }
    }
    return num;
};
const botonGenerarPrecotizacion = $("#generarPrecotizacion");
const contenedorResultados = $("#contenedor-resultados");
const toggleResultados = () => {
    if (window.detalles_cotizaciones.length < 1) {
        botonGenerarPrecotizacion.prop("disabled", true);
        contenedorResultados.hide();
    } else {
        // contenedorResultados.show();
        botonGenerarPrecotizacion.prop("disabled", false);
    }
    return;
};


$("#generarPrecotizacion").on("click", function (e) {
   
    e.preventDefault();
    var form_cotizacion = $("#formCotizacion");
    var cotizacion_id = $("#cotizacion_id");
    // Validamos el formulario
    form_cotizacion.valid();
    if (!form_cotizacion.valid()) {
        return false;
    }

    // loading gif
    $("#loading").show();

    var formulario = form_cotizacion.serializeArray();
    formulario.push({
        name: "detalles",
        value: JSON.stringify(
            window.detalles_cotizaciones.map((a) => a.id)
        ),
        // value: JSON.stringify([
        //     window.detalles_cotizaciones[0],
        //     window.detalles_cotizaciones[1],
        // ]),
    });

    var idCotizacion = cotizacion_id.val() || 0;
    var client_id = $("#client_id").val() || 0;
    return $.ajax({
        type: "POST",
        url: "/cotizador/generarPrecotizacion/" + idCotizacion + "/" + client_id,
        data: formulario,
        success: function (cotizacion) {
            $("#loading").hide(); // hide ajax loader

            window.detalles_cotizaciones = cotizacion.detalles;
            $("#cotizacion_id").val(cotizacion.id);
            $("#titulo-cotizacion").html("Cotización N° " + cotizacion.id);
            notify("Resultados Actualizados", "success");
            contenedorResultados.show();
            renderTablasResultados();

            $("#paso_cuatro").css("background-color", " #7ae091");
        },
    });
});

//
// function updateData(newData) {
//     newData.forEach(function (item) {
//         data.push(item);
//     });
// }

// CODIGO PARA VALIDAR JERARQUIAS-RUBRO

// Al comienzo los select de jerarquia 2 y 3 comienzan desabilitados y luego son habilitados al cambiar la jerarquia 1

$("#subhierarchy_id,#subsubhierarchy_id").prop("disabled", true);
// ajax para relacionar jerarquia 2 con la jerarquia 1 seleccionada
$("#hierarchy_id").change(function () {
    var jerarquia_id = $(this).val();
    // if (jerarquia_id) {
    //     $("#rubro_id").val("").prop("disabled", true);
    // } else {
    //     $("#rubro_id").prop("disabled", false);
    // }

    // // Si tenemos ya un rubro seleccionado
    var rubro = "";
    if ($("#rubro_id").val()) {
        rubro = "&rubro_id=" + $("#rubro_id").val();
    }
    // Cargar jerarquia 2
    return $.ajax({
        type: "GET",
        url: "/cotizador/getJerarquia2AreaHC",
        data: "hierarchy_id=" + jerarquia_id + rubro,
        success: function (data) {
            data = $.parseHTML(data);
            // if (role == 4) {
            $("#hierarchy_id").prop("disabled", false);
            $("#subhierarchy_id").prop("disabled", false);
            // }
            $("#subhierarchy_id").empty().append(data).selectpicker("refresh");

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
    });
});
$("#rubro_id").change(function () {
    $("#hierarchy_id").val("").change();
});

// ajax para relacionar jerarquia 3 con la jerarquia 2 seleccionada
$("#subhierarchy_id").change(function () {
    var val = $(this).val();
    var rubro = "";
    if ($("#rubro_id").val()) {
        rubro = "&rubro_id=" + $("#rubro_id").val();
    } else {
    }
    return $.ajax({
        type: "GET",
        url: "/cotizador/getJerarquia3ConRubro",
        data: "subhierarchy_id=" + val + rubro,
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
    });
});

// Segun la jerarquia 3 seleccionada si no hay rubro traemos el rubro correspondiente
$("#subsubhierarchy_id").change(function () {
    var val = $(this).val();
    if ($("#rubro_id").val()) {
    } else {
    }
    return $.ajax({
        type: "GET",
        url: "/cotizador/getRubro",
        data: "subsubhierarchy_id=" + val,
        success: function (data) {
            $("#rubro_id").val(data).selectpicker("refresh");
        },
    });
});

// FIN CODIGO JERARQUIA-RUBRO

// CODIGO PARA AGREGAR DESTINOS
let destinosCounter = 0;
$("#agregarDestino").click(function (e) {
    e.preventDefault();
    let detalle = $("#tipo_detalle_id").val();

    destinosCounter++;

    // Solo se incluyen los pallets apilados si es corrugado
    if (detalle == 1) {
        pallets = `<div class=" col-3">
        <div class="form-group form-row ">
            <label class="col-auto col-form-label">Pallets Apilados</label>
            <div class="col" id="pallets${destinosCounter}"> </div>
        </div>
    </div>`;
    } else {
        pallets = "";
    }
    var html = `
    <div class="nuevoDestino row">
        <div class=" col-3">
            <div class="form-group form-row ">
                <label class="col-auto col-form-label">Lugar de Destino</label>
                <div class="col" id="destino${destinosCounter}"> </div>
            </div>
        </div>
        ${pallets}
        <div class="col-3">
            <div class="form-group form-row ">
              <label class="col-auto col-form-label">Cantidad (UN):</label>
              <div class="col">
                <input type="number" id="cantidad${destinosCounter}" name="cantidad${destinosCounter}" value="" class="form-control cantidad-multidestino" min="1" placeholder="">
                
              </div>
            </div>
        </div>
        <div class="input-group-append" style="display:block">
            <button  type="button" class="btn btn-danger removeRow">Eliminar</button>
            </div>
    </div>`;

    $("#newRow").append(html);
    $("select#ciudad_id")
        .clone()
        .attr("id", "ciudad_id" + destinosCounter)
        .attr("name", "ciudad_id" + destinosCounter)
        .attr("class", "cantidad-multidestino")
        .appendTo("#destino" + destinosCounter);

    if (detalle == 1) {
        $("select#pallets_apilados")
            .clone()
            .attr("id", "pallets_apilados" + destinosCounter)
            .attr("name", "pallets_apilados" + destinosCounter)
            .attr("class", "cantidad-multidestino")
            .appendTo("#pallets" + destinosCounter);
    }
    $("#ciudad_id" + destinosCounter).selectpicker();
    $("#pallets_apilados" + destinosCounter).selectpicker();

    // let formDetalleCotizacion = $("#form-detalle-cotizacion");
    $(".cantidad-multidestino").each(function () {
        $(this).rules("add", "required");
    });
});

// remove row
$(document).on("click", ".removeRow", function () {
    $(this).closest(".nuevoDestino").remove();
});

$("#tipo_detalle_id")
    .parent()
    .parent()
    .parent()
    .find("label")
    .css("color", "black");

$(document).on("click", ".marcar-ganado-detalle-cotizacion", function (e) {
    e.preventDefault();
    let detalle_id = $(this).data("id");
    return $.ajax({
        type: "POST",
        url: "/cotizador/detalleCotizacionGanado",
        data: "detalle_id=" + detalle_id,
        success: function (data) {
            notify("Detalle marcado como Ganado", "success");
            $("#marcas-detalle-" + detalle_id).html(
                `<a class="marcar-ganado-detalle-cotizacion"  href="#"  data-id="${detalle_id}" ><div style="" class="material-icons md-14" data-toggle="tooltip" title="Marcar Ganado" >thumb_up</div></a>
                <a class="marcar-perdido-detalle-cotizacion"  href="#"  data-id="${detalle_id}" ><div style="opacity: 0.15;color:red" class="material-icons md-14" data-toggle="tooltip" title="Marcar Perdido">thumb_down</div></a>`
            );
        },
    });
});
$(document).on("click", ".marcar-perdido-detalle-cotizacion", function (e) {
    e.preventDefault();
    let detalle_id = $(this).data("id");
    return $.ajax({
        type: "POST",
        url: "/cotizador/detalleCotizacionPerdido",
        data: "detalle_id=" + detalle_id,
        success: function (data) {
            notify("Detalle marcado como Perdido", "success");
            $("#marcas-detalle-" + detalle_id).html(
                `<a class="marcar-ganado-detalle-cotizacion"  href="#"  data-id="${detalle_id}" ><div style="opacity: 0.15;" class="material-icons md-14" data-toggle="tooltip" title="Marcar Ganado" >thumb_up</div></a>
                <a class="marcar-perdido-detalle-cotizacion"  href="#"  data-id="${detalle_id}" ><div style="color:red" class="material-icons md-14" data-toggle="tooltip" title="Marcar Perdido">thumb_down</div></a>`
            );
        },
    });
});
/*
$("#pallet").on("change", function () {
    if ($("#pallet").val() == 1) {
        $("#tipo_pallet").show();
    }else{
        $("#tipo_pallet").hide();
        $("#pallet_type_id")
            .selectpicker("val", "")
            .selectpicker("refresh");
    }
});*/

$("#tipo_medida").on("change", function () {
    if ($("#tipo_medida").val() == 1) {
        $("#ancho").val($("#interno_ancho_med").val());
        $("#alto").val($("#interno_alto_med").val());
        $("#largo").val($("#interno_largo_med").val());
    }else{
        $("#ancho").val($("#externo_ancho_med").val());
        $("#alto").val($("#externo_alto_med").val());
        $("#largo").val($("#externo_largo_med").val());
    }
});

$("#printing_machine_id").on("change", function () {
    //Sin Impresion
    if($("#printing_machine_id").val()==1){
        $("#numero_colores,#print_type_id")
            .prop("disabled",true)
            .prop("readonly",true)
            .val("")
            .selectpicker("refresh");
    }else{
        $("#numero_colores")
            .prop("disabled",false)
            .selectpicker("refresh");
        //Maquina impresora normal
        if($("#printing_machine_id").val()==2){
            $("#numero_colores").html(
                    `<option value="">Seleccionar...</option>
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                    <option value="3"> 3</option>
                    <option value="4"> 4</option>`
            )
            .selectpicker("refresh");

            $("#print_type_id")
                .val(4)
                .prop("readonly",true)
                .selectpicker("refresh");
            
            $("#barniz_type_id")
                .html('<option value="3">Hidrorepelente</option>')
                .selectpicker("refresh");
        }else{

            if($("#printing_machine_id").val()==5){ // Dong Fang
                          
                $("#numero_colores").html(
                    `<option value="">Seleccionar...</option>
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                    <option value="3"> 3</option>
                    <option value="4"> 4</option>
                    <option value="5"> 5</option>`
                )
                .selectpicker("refresh");                   
               
                $("#print_type_id")
                    .val(4)
                    .prop("readonly",true)
                    .selectpicker("refresh");
                
                
            }else{
                //Maquina impresora interna / Alta grafica 
            
                $("#numero_colores").html(
                    `<option value="">Seleccionar...</option>
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                    <option value="3"> 3</option>
                    <option value="4"> 4</option>
                    <option value="5"> 5</option>
                    <option value="6"> 6</option>`
                )
                .selectpicker("refresh");   
                
                //Maquina Impresora Alta Grafica
                if($("#printing_machine_id").val()==4){
                    $("#print_type_id")
                        .prop("disabled",false)
                        .prop("readonly",false)
                        .selectpicker("refresh");
                }else{
                    $("#print_type_id")
                        .val(4)
                        .prop("readonly",true)
                        .selectpicker("refresh");
                }
                //Maquina Impresora Interna
                if($("#printing_machine_id").val()==3){
                    $("#barniz_type_id")
                        .html('<option value="3">Hidrorepelente</option>')
                        .selectpicker("refresh");
                }
            }    
        }
    }     

    if($("#printing_machine_id").val()==1){//Sin Impresion

        $("#process_id")
            .html(`<option value="">Seleccionar...</option>
                    <option value="3">S/PROCESO</option>
                `)
        .selectpicker("refresh");

    }else if($("#printing_machine_id").val()==2){//Normal

        $("#process_id")
            .html( `<option value="2">DIECUTTER</option>
                    <option value="4">DIECUTTER-C/PEGADO</option>
                    <option value="1">FLEXO</option>
                    <option value="10">FLEXO/MATRIZ COMPLET</option>
                    <option value="5">FLEXO/MATRIZ PARCIAL</option>
                `)           
            .selectpicker("refresh");

    }else if($("#printing_machine_id").val()==3){//Interna

        $("#process_id")
            .html( `<option value="1">FLEXO</option>
                    <option value="10">FLEXO/MATRIZ COMPLET</option>
                    <option value="5">FLEXO/MATRIZ PARCIAL</option>
                `)           
            .selectpicker("refresh");

    }else if($("#printing_machine_id").val()==4){//Alta Grafica

        if($("#print_type_id").val()==4){//Impresion Normal
            $("#process_id")
                .html( `<option value="2">DIECUTTER</option>
                        <option value="4">DIECUTTER-C/PEGADO</option>
                    `)           
                .selectpicker("refresh");
        }else{ //Impresion Alta Grafica
            $("#process_id")
                .html( `<option value="11">DIECUTTER - ALTA GRAFICA </option>
                        <option value="12">DIECUTTER -C/PEGADO ALTA GRAFICA </option>
                    `)           
                .selectpicker("refresh");
        }
       

    }else if($("#printing_machine_id").val()==5){//Dong Fang 1224 (Buin)

        $("#process_id")
        .html( `<option value="1">FLEXO</option>
                <option value="10">FLEXO/MATRIZ COMPLET</option>
                <option value="5">FLEXO/MATRIZ PARCIAL</option>
            `)           
        .selectpicker("refresh");
    }
});

$("#numero_colores,#print_type_id,#pegado_id,#armado_usd_caja,#cuchillos_gomas")
    .prop("disabled",true)
    .prop("readonly",true)
    .selectpicker("refresh");

numero_colores.on("change", function () {
   
    //Maquina impresora Normal    
    if($("#printing_machine_id").val()==2){
        if(numero_colores.val()==4){
            $("#barniz,#barniz_type_id")
                .val(0)
                .prop("disabled", true)
                .prop("readonly", true)
                .selectpicker("refresh");
          
        }else{
            $("#barniz,#barniz_type_id")
                .val("")
                .prop("disabled", false)
                .prop("readonly", false)
                .selectpicker("refresh");            
        }         
    }

    //Maquina impresora Interna    
    if($("#printing_machine_id").val()==3){
        if(numero_colores.val()==6){
            $("#barniz,#barniz_type_id")
                .val(0)
                .prop("disabled", true)
                .prop("readonly", true)
                .selectpicker("refresh");          
        }else{
            $("#barniz,#barniz_type_id")
                .val("")
                .prop("disabled", false)
                .prop("readonly", false)
                .selectpicker("refresh");            
        }
    }

    //Maquina impresora Dong Fang
    if($("#printing_machine_id").val()==5){
        if(numero_colores.val()==5){
            $("#barniz,#barniz_type_id")
                .val(0)
                .prop("disabled", true)
                .prop("readonly", true)
                .selectpicker("refresh");          
        }else{
            $("#barniz,#barniz_type_id")
                .val("")
                .prop("disabled", false)
                .prop("readonly", false)
                .selectpicker("refresh");            
        }
    }    
});

print_type.on("change", function () {
    //Impresion alta grafica
    var barniz = $("#barniz").val();
    var tipo_barniz = $("#barniz_type_id").val();
    if(print_type.val()==5){
        //Maquina Alta Grafica
        if($("#printing_machine_id").val()==4){
            $("#barniz")
                .val(1)
                .prop("disabled", true)
                .prop("readonly", true)
                .selectpicker("refresh")
                .trigger("change");
            $("#barniz_type_id")
                .html(`<option value="1"> UV</option>
                       <option value="2"> Acuoso</option>`)
                .selectpicker("refresh");

            $("#barniz_type_id")
                .val(barniz_type_id)
                .selectpicker("refresh");
            
            $("#process_id")
                .html( `<option value="11">DIECUTTER - ALTA GRAFICA </option>
                        <option value="12">DIECUTTER -C/PEGADO ALTA GRAFICA </option>
                    `)           
                .selectpicker("refresh");

        }else{
          
            $("#barniz")
                //.val("")
                .val(barniz)
                .prop("disabled", false)
                .prop("readonly", false)
                .selectpicker("refresh");
            $("#barniz_type_id")
                .html(`<option value="1"> UV</option>
                       <option value="2"> Acuoso</option>
                       <option value="3">Hidrorepelente</option>`)
                .selectpicker("refresh");
            $("#barniz_type_id")
                .val(barniz_type_id)
                .selectpicker("refresh");
        }
    }else{
        //Impresion normal
        if(print_type.val()==4){
           
            //Maquina Alta Grafica
            if($("#printing_machine_id").val()==4){
                $("#barniz_type_id")
                    .html(`<option value="3">Hidrorepelente</option>`)
                    .selectpicker("refresh");
                $("#barniz_type_id")
                    .val(barniz_type_id)
                    .selectpicker("refresh");
                
                $("#process_id")
                    .html( `<option value="2">DIECUTTER</option>
                            <option value="4">DIECUTTER-C/PEGADO</option>
                        `)           
                    .selectpicker("refresh");
            }else{
                $("#barniz_type_id")
                    .html(`<option value="1"> UV</option>
                        <option value="2"> Acuoso</option>
                        <option value="3">Hidrorepelente</option>`)
                    .selectpicker("refresh");
                $("#barniz_type_id")
                    .val(barniz_type_id)
                    .selectpicker("refresh");
            }
            $("#barniz")
                .val("")
                //.val(barniz)
                .prop("disabled", false)
                .prop("readonly", false)
                .selectpicker("refresh");
        }
    }   
    
    

   
});

$("#barniz").on("change", function () {
    var num_colores=$("#numero_colores").val();
    if($("#barniz").val()==1){
        if($("#printing_machine_id").val()==4){
            $("#numero_colores")
                .html(`<option value="">Seleccionar...</option>
                        <option value="0"> 0</option>
                        <option value="1"> 1</option>
                        <option value="2"> 2</option>
                        <option value="3"> 3</option>
                        <option value="4"> 4</option>
                        <option value="5"> 5</option>
                        <option value="6"> 6</option>`
                    )
                    .selectpicker("refresh");
            if(num_colores==6){
                $("#numero_colores")
                    .val(6)
                    .selectpicker("refresh");
            }else{
                $("#numero_colores")
                    .val(num_colores)
                    .selectpicker("refresh");
            }
        }
        $("#barniz_type_id")
            .prop("disabled", false)
            .prop("readonly", false)
            .selectpicker("refresh");
    }else{
        $("#barniz_type_id")
            .val(0)
            .prop("disabled", true)
            .prop("readonly", true)
            .selectpicker("refresh");
    }
    
});

//Altura de pallet
$("#pallet_height_id").on("change", function () {
    var val= $(this).val();
    if(val==''){
        $("#pallets_apilados")
            .prop("disabled",false)
            .prop("readonly",false)
            .val("")
            .selectpicker("refresh");
        
        $("#pallets_apilados_val").val("");
    }else{
        if(val==1){
            $("#pallets_apilados")
                .prop("disabled",true)
                .prop("readonly",true)
                .val(2)
                .selectpicker("refresh");

            $("#pallets_apilados_val").val(2);

        }else{
            $("#pallets_apilados")
                .prop("disabled",true)
                .prop("readonly",true)
                .val(1)
                .selectpicker("refresh");

            $("#pallets_apilados_val").val(1);
        }       
    }    
});

//Destino para agente de exportacion
/*$("#ciudad_id").on("change", function () {

    if($("#ciudad_id").val()==331 || $("#ciudad_id").val()==332){
        $("#agente_exportacion")
            .prop("disabled",false)
            .prop("readonly",false);
            
    }else{
        $("#agente_exportacion")
            .prop("disabled",true)
            .prop("readonly",true);
    }
    
});*/


