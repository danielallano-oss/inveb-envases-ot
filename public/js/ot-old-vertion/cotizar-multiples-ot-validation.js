let object_data = {} //Arreglo de tipo de detalle (Corrugado/esquinero)
let count_checkbox_corrugado = $('input[type=checkbox].checkbox_corrugado').length; //Cuenta los input checkbox para corrugado
let count_checkbox_esquinero = $('input[type=checkbox].checkbox_esquinero').length; //Cuenta los input checkbox para esquinero

function checkMarks() {
    const marked_counts = Object.entries(object_data).length;
    const corrugado_counts_marked = $('input[type=checkbox].checkbox_corrugado:checked').length
    const esquinero_counts_marked = $('input[type=checkbox].checkbox_esquinero:checked').length

    if(count_checkbox_corrugado != corrugado_counts_marked){
        $("#tipo_detalle_corrugado").prop("checked", false);
    }

    if(count_checkbox_esquinero != esquinero_counts_marked){
        $("#tipo_detalle_esquinero").prop("checked", false);
    }

    if (!!marked_counts) {
        $("#cotizar").removeClass('disabled');
        return;
    }
    $("#cotizar").addClass('disabled');
}

   //Funcion para seleccionar el tipo de detalle (Corrugado)
   $(document).on('change', '.checkbox_corrugado', function(element){
        const value = $(this).val();
        const ot_id = $(this).attr('name').split('-')[1];
        const count_checkbox_corrugado_checked = $('input:checkbox.checkbox_corrugado:checked').length;//Contamos la cantidad de input corrugados seleccionados para validar

        //Si esta checked lo agregamos al objeto
        if($(this).is(':checked') === true){
            object_data[ot_id] = value;
            $('input[name=tipo_detalle_list-'+ot_id+'].checkbox_esquinero').prop("checked", false)//Si esta seleccionado el checkbox corrugado, no se puede seleccionar el esquinero
        }else{
            delete object_data[ot_id];
        }

        //Comprobamos la cantidad de OT marcado con tipo de corrugado, si es distinto deshablitamos el check de Seleccionar todos para corrugado
        if(count_checkbox_corrugado != count_checkbox_corrugado_checked){
            $("#tipo_detalle_corrugado").prop("checked", false);
        }

        checkMarks();
   });

    //Funcion para seleccionar el tipo de detalle (Esquinero)
    $(document).on('change', '.checkbox_esquinero', function(element){
        const value = $(this).val();
        const ot_id = $(this).attr('name').split('-')[1];
        const count_checkbox_esquinero_checked = $('input:checkbox.checkbox_esquinero:checked').length;//Contamos la cantidad de input esquinero seleccionados para validar

        //Si esta checked lo agregamos al objeto
        if($(this).is(':checked') === true){
            object_data[ot_id] = value;
            $('input[name=tipo_detalle_list-'+ot_id+'].checkbox_corrugado').prop("checked", false)//Si esta seleccionado el checkbox esquinero, no se puede seleccionar el corrugado
        }else{
            delete object_data[ot_id];
        }

        //Comprobamos la cantidad de OT marcado con tipo de esquinero, si es distinto deshablitamos el check de Seleccionar todos para esquinero
        if(count_checkbox_esquinero != count_checkbox_esquinero_checked){
            $("#tipo_detalle_esquinero").prop("checked", false);
        }

        checkMarks();

    });

   //Funcion para marcar todos con el tipo de detalle corrugado
   $("#tipo_detalle_corrugado").on("click", function() {
       let on_corrugado = $("#tipo_detalle_corrugado").is(':checked');
       const value = $(this).val(); //Captura el valor del checkbox corrugado
       
       if(on_corrugado){
           $('input[type=checkbox].checkbox_corrugado').each(function(element) { //Recorro todos los input individual para setearlos a checked
               $(this).prop("checked", on_corrugado);//Marco como seleccionado el checkbox indididual por ot
               const ot_id = $(this).attr('name').split('-')[1];//Obtengo el id de la ot

               object_data[ot_id] = value;//Los agrego al objecto

               $('input[type=checkbox].checkbox_esquinero').prop("checked", false);
               $("#tipo_detalle_esquinero").prop("checked", false);
            });
            
            // $("#cotizar").removeClass('disabled');
            checkMarks();
        } else {
            $('input[type=checkbox].checkbox_corrugado').each(function(element) {
                const ot_id = $(this).attr('name').split('-')[1];//Obtengo el id de la ot
                if ($(this).is(':checked')) {
                    delete object_data[ot_id];
                }
            });

            checkMarks();
        }
        
       $(".checkbox_corrugado").prop("checked", on_corrugado);
   });

   //Funcion para marcar todos con el tipo de detalle esquinero
   $("#tipo_detalle_esquinero").on("click", function() {
       let on_esquinero = $("#tipo_detalle_esquinero").is(':checked');
       const value = $(this).val(); //Captura el valor del checkbox esquinero
       
       if(on_esquinero){
            $('input[type=checkbox].checkbox_esquinero').each(function(element) {//Recorro todos los input individual para setearlos a checked
                $(this).prop("checked", on_esquinero);//Marco como seleccionado el checkbox indididual por ot
                const ot_id = $(this).attr('name').split('-')[1];//Obtengo el id de la ot

                object_data[ot_id] = value;//Los agrego al objecto

                $('input[type=checkbox].checkbox_corrugado').prop("checked", false);
                $("#tipo_detalle_corrugado").prop("checked", false);
            });

            // $("#cotizar").removeClass('disabled');
            checkMarks();
        } else {
            $('input[type=checkbox].checkbox_esquinero').each(function(element) {
                const ot_id = $(this).attr('name').split('-')[1];//Obtengo el id de la ot
                if ($(this).is(':checked')) {
                    delete object_data[ot_id];
                }
            });
            
            checkMarks();
        }
     
       $(".checkbox_esquinero").prop("checked", this.checked);
   });

   //Enviamos la lista de arreglos al controlador
   $.ajaxSetup({
       headers: {
           "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
       },
   });

   $("#cotizar").on("click", function (e){
       e.preventDefault();
       return $.ajax({
           type: "POST",
           url: "/cotizador/guardarMultiplesOt",
           dataType: "json",
           data: object_data,
           success: function (data) {
               const detalles_id = data.join(',');
               window.location.href = 'cotizador/crear?dids='+detalles_id;
           },
       });
   });

//-------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------
//-PRIMER CODIGO, CUANDO SOLO ERA UN selector, SIN DISTINGUIR ENTRE CORRUGADO Y on_esquinero
//-------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------
   
//Agrero las ot en un arreglo para despues pasarlas al cotizador
//    let arreglo = []; // Arreglo de ID de las OT
//    let count_input = $('input[type=checkbox].checkbox_list').length; //cuenta los input de los ID de las OT

//    $(document).on('click', '.checkbox_list', function(element){
//        const id = $(this).val();
//        const check_input = $(this).is(':checked');

//        if(check_input){//Agregamos una OT al arreglo
//            const value = arreglo.indexOf(parseInt(id));
//            if(value < 0){
//                arreglo.push(parseInt(id));
//            }
//        }else{
//            var indice = arreglo.indexOf(parseInt(id)); // obtenemos el indice
//            arreglo.splice(indice, 1); // 1 es la cantidad de elemento a eliminar
//        }

//        //Validamos si hay alguna OT marcar para habilitar el boton de Cotizar
//        if(arreglo.length > 0){
//            $("#cotizar").removeClass('disabled');
//        }else{
//            $("#cotizar").addClass('disabled');
//        }

//        //Comprobamos la cantidad de OT marcado con todos los checkbox del listado, si es distinto deshablitamos el check de Seleccionar todos
//        if(arreglo.length != count_input){
//            $("#selectall").prop("checked", false);
//        }   
    
//    })

//Funcion para marcar todas las OT
//    $("#selectall").on("click", function() {
//        let on_off = $("#selectall").is(':checked');
//        $(".checkbox_list").prop("checked", this.checked);

//        if(on_off === true){
//            $('input[type=checkbox].checkbox_list:checked').each(function() {
//                const value = arreglo.indexOf(parseInt($(this).val()));
//                if(value < 0){
//                    arreglo.push(parseInt($(this).val()));
//                }
//            });

//            $("#cotizar").removeClass('disabled');
//        }else{
//            arreglo = [];
//            $("#cotizar").addClass('disabled');
//        }     
//    });


