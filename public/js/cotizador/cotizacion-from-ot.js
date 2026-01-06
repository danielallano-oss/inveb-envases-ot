$(document).ready(function () {
    const ot = window.ot;
    if (ot) {

        // Cargar datos Comerciales
        $("#client_id").selectpicker("val", ot.client_id);
        $("#nombre_contacto").val(ot.nombre_contacto);
        $("#email_contacto").val(ot.email_contacto);
        $("#telefono_contacto").val(ot.telefono_contacto);

        // Desplegar y cargar modal detalle
        $("#crear_precotizacion").click();

        $("#work_order_id").val(ot.id);

        // CARACTERISTICAS
        // CORRUGADO
        if (ot.product_type_id != 21) {
            tipo_detalle.selectpicker("val", 1).change();
            defaultValues();
            $("#area_hc").val(ot.area_hc);
            $("#anchura").val(ot.anchura_hm);
            $("#largura").val(ot.largura_hm);

            $("#product_type_id").selectpicker("val", ot.product_type_id);
            if($("#es_provisional").val()==1){
                $("#carton_id").selectpicker("val", $("#carton_original_id").val());
                    
            }else{
                $("#carton_id").selectpicker("val", ot.carton_id);
            }
            
            $("#numero_colores").selectpicker("val", ot.numero_colores);
            if (
                ot.impresion_1 ||
                ot.impresion_2 ||
                ot.impresion_3 ||
                ot.impresion_4 ||
                ot.impresion_5
            ) {
                // console.log(ot.impresion_1 || ot.impresion_2 || ot.impresion_3 || ot.impresion_4 || ot.impresion_5);
                // console.log(Math.max(ot.impresion_1 , ot.impresion_2 , ot.impresion_3 , ot.impresion_4 , ot.impresion_5));
                let impresion = Math.max(
                    ot.impresion_1,
                    ot.impresion_2,
                    ot.impresion_3,
                    ot.impresion_4,
                    ot.impresion_5
                );
                if (
                    ot.impresion &&
                    ot.impresion != 0 &&
                    ot.impresion != 25 &&
                    ot.impresion != 50 &&
                    ot.impresion != 75 &&
                    ot.impresion != 100
                ) {
                    data = `<option value="" disabled selected>Seleccionar Opción</option><option value="${impresion}"> ${impresion}</option><option value="0"> 0</option><option value="2"> 25</option><option value="3"> 50</option><option value="4"> 75</option><option value="5"> 100</option>`;
                } else {
                    data = `<option value="" disabled selected>Seleccionar Opción</option><option value="0"> 0</option><option value="25"> 25</option><option value="50"> 50</option><option value="75"> 75</option><option value="100"> 100</option>`;
                }
                $("#impresion")
                    .empty()
                    .append(data)
                    .selectpicker("refresh")
                    .selectpicker("val", impresion);
                // $("#impresion").val();
            }
            ot.golpes_largo && $("#golpes_largo").val(ot.golpes_largo);
            ot.golpes_ancho && $("#golpes_ancho").val(ot.golpes_ancho);
            $("#process_id").selectpicker("val", ot.process_id);
            ot.porcentaje_cera_interno &&
                $("#porcentaje_cera_interno").val(ot.porcentaje_cera_interno);
            ot.porcentaje_cera_externo &&
                $("#porcentaje_cera_externo").val(ot.porcentaje_cera_externo);
            // ot.impresion && $("#impresion").val(ot.impresion);

            // CAMPOS OPCIONALES

            $("#largo").val(ot.interno_largo);
            $("#ancho").val(ot.interno_ancho);
            $("#alto").val(ot.interno_alto);
            $("#codigo_cliente").val(ot.client.codigo);
            // $("#unidad_medida_bct").selectpicker("val", ot.medida_bct);
            $("#bct_min_lb").val(ot.bct_min_lb);
            $("#bct_min_kg").val(ot.bct_min_kg);

            // SI HAY MATERIAL CARGARLO
            if (ot.material) {
                $("#codigo_material_detalle").val(ot.material.codigo);
                $("#descripcion_material_detalle").val(ot.material.descripcion);
                $("#material_id").val(ot.material.id);
            }
            if (ot.cad_asignado) {
                $("#cad_material_detalle").val(ot.cad_asignado.cad);
                $("#cad_material_id").val(ot.cad_asignado.id);
            }

            // JERARQUIAS
            ot.subsubhierarchy &&
                $("#rubro_id").selectpicker(
                    "val",
                    ot.subsubhierarchy.rubro_id
                ) &&
                populateHierarchies(ot) &&
                $("#hierarchy_id").selectpicker(
                    "val",
                    ot.subsubhierarchy.subhierarchy.hierarchy_id
                );

                  
            //Cargo campo Maquila segun el producto
            if(ot.maquila == null){                   
                $("#maquila").selectpicker("val", '0');
            }else{
                $("#maquila").selectpicker("val", ot.maquila);
                $("#maquila_servicio_id").selectpicker("val", ot.maquila_servicio_id);
        
            }
        
            $("#maquila")
            .change(() => {        
                if ($("#maquila").val() == 0) {                        
                    $("#maquila_servicio_id")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                } else { 

                    if(ot.product_type_id != null){
                        producto_id = ot.product_type_id
                    }else{
                        producto_id = $("#product_type_id").val();
                    }

                    $("#maquila_servicio_id")
                    .prop("disabled", false)
                    .val("")
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");

                }
            })
            .triggerHandler("change");
    
            
            //Producto
            $("#product_type_id").change(function () {
                let producto_id = ['3','4','5','6','8','10','11','12','13','14','16','18','19','20','28','31','32','33','34']
                let producto = $(this).val();
        
                if(producto_id.includes(producto)){
                    $("#maquila")
                        .prop("disabled", false)
                        .val('1')
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");
                        
                        
                        return $.ajax({
                            type: "GET",
                            url: "/cotizador/getServiciosMaquila",
                            data: "tipo_producto_id=" + producto + '&ot_id=' + ot.id,
                            success: function (data) {
                                data = $.parseHTML(data);                                   
                                maquila_servicio_id
                                    .prop("disabled", false)
                                    .empty()
                                    .append(data)
                                    .selectpicker("refresh");
                                    if(ot.maquila_servicio_id){
                                        $("#maquila_servicio_id").selectpicker("val", ot.maquila_servicio_id);
                                    }                                
                            },
                        }); 

                }else{
                    $("#maquila")
                        .prop("disabled", true)
                        .val("0")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");

                    $("#maquila_servicio_id")
                        .prop("disabled", true)
                        .val("")
                        .selectpicker("refresh")
                        .closest("div.form-group")
                        .removeClass("error");       
                }
        


            });

            // Se validan el campo matriz, segun el proceso seleccionado
            $("#process_id")
            .change(() => {
                // procesos DIECUTTER, DICUTTER-C/PEGADO y FLEXO/MATRIZ COMPLET ---- FLEXO/MATRIZ PARCIAL 
                if ($("#process_id").val() === "2" || $("#process_id").val() === "4" || $("#process_id").val() === "10" || $("#process_id").val() === "5") {
                    $("#matriz")
                    .prop("disabled", false)      
                    .selectpicker("val", 1)
                    .selectpicker("refresh");
                }else{
                    $("#matriz")      
                    .prop("disabled", true)             
                    .selectpicker("val", 0)
                    .selectpicker("refresh");
                }                
             
            })
            .triggerHandler("change");

        } else {
            tipo_detalle.selectpicker("val", 2).change();
            // 21 = ESQUINERO

            $("#carton_esquinero_id").selectpicker("val", ot.carton_id);
            $("#numero_colores_esquinero").selectpicker(
                "val",
                ot.numero_colores
            );
            // SI HAY MATERIAL CARGARLO
            if (ot.material) {
                $("#codigo_material_detalle").val(ot.material.codigo);
                $("#descripcion_material_detalle").val(ot.material.descripcion);
                $("#material_id").val(ot.material.id);
            }
            if (ot.cad_asignado) {
                $("#cad_material_detalle").val(ot.cad_asignado.cad);
                $("#cad_material_id").val(ot.cad_asignado.id);
            }
        }
    }
});
