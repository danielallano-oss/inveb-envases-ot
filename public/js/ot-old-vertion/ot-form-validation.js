$(document).ready(function () {
    //const gramajeMask = IMask(gramaje, oneDecimal);

    console.log('old');
    const ectMask = IMask(ect, twoDecimal);
    const fctMask = IMask(fct, twoDecimal);
    //const flexionAletaMask = IMask(flexion_aleta, twoDecimal);
    //const pesoMask = IMask(peso, twoDecimal);
    //const espesorMask = IMask(espesor, thousandsOptions);
    //const cobbInteriorMask = IMask(cobb_interior, oneDecimal);
    //const cobbExteriorMask = IMask(cobb_exterior, oneDecimal);
    //const incision_rayado = IMask(incision_rayado_longitudinal, thousandsOptions);
    //const incision_rayado_v = IMask(incision_rayado_vertical, thousandsOptions);
    //const separacion_largo = IMask(separacion_golpes_largo, thousandsOptions);
    //const separacion_ancho = IMask(separacion_golpes_ancho, thousandsOptions);
    const separacion_largo = IMask(separacion_golpes_largo, {mask: Number,min: -1000000,max: 1000000,thousandsOptions});
    const separacion_ancho = IMask(separacion_golpes_ancho, {mask: Number,min: -1000000,max: 1000000,thousandsOptions});
    const internoLargo = IMask(interno_largo, thousandsOptions);
    const internoAncho = IMask(interno_ancho, thousandsOptions);
    const internoAlto = IMask(interno_alto, thousandsOptions);
    const externoLargo = IMask(externo_largo, thousandsOptions);
    const externoAncho = IMask(externo_ancho, thousandsOptions);
    const externoAlto = IMask(externo_alto, thousandsOptions);

    // gramajeMask.updateControl();
    // ectMask.updateControl();

    const role = $("#role_id").val();
    const tipo = $("#tipo").val();

    // traduccion mensajes de jquery validator
    jQuery.extend(jQuery.validator.messages, {
        required: "Campo obligatorio.",
        remote: "Por favor, rellena este campo.",
        email: "Por favor, ingresa una dirección de correo válida",
        url: "Por favor, ingresa una URL válida.",
        date: "Por favor, ingresa una fecha válida.",
        dateISO: "Por favor, ingresa una fecha (ISO) válida.",
        number: "Por favor, ingresa un número entero válido.",
        digits: "Por favor, ingresa sólo dígitos.",
        creditcard: "Por favor, ingresa un número de tarjeta válido.",
        equalTo: "Por favor, ingresa el mismo valor de nuevo.",
        accept: "Por favor, ingresa un valor con una extensión aceptada.",
        maxlength: jQuery.validator.format(
            "Por favor, ingresa menos de {0} caracteres."
        ),
        minlength: jQuery.validator.format(
            "Por favor, ingresa más de {0} caracteres."
        ),
        rangelength: jQuery.validator.format(
            "Por favor, ingresa un valor entre {0} y {1} caracteres."
        ),
        range: jQuery.validator.format(
            "Por favor, ingresa un valor entre {0} y {1}."
        ),
        max: jQuery.validator.format(
            "Por favor, ingresa un valor menor o igual a {0}."
        ),
        min: jQuery.validator.format(
            "Por favor, ingresa un valor mayor o igual a {0}."
        ),
    });

    // Custom rules
    jQuery.validator.addMethod(
        "exactlength",
        function (value, element, param) {
            return this.optional(element) || value.length == param;
        },
        $.validator.format("Por favor, ingresa exactamente {0} caracteres.")
    );

    $.validator.addMethod(
        "telefono",
        function (value, element) {
            return (
                this.optional(element) ||
                /^(\+56)?(\s?)(0?[92])(\s?)[987654321]\d{7}$/.test(value)
            );
        },
        $.validator.format("Formato de Telefono: +56912345678")
    );

    const tipo_solicitud = $("#tipo_solicitud");
    const armado_id = $("#armado_id");

    const grupomaterial1 = () => {
        if ( role == 5) {
            return true;
        }
        return false;
    }

    const desarrolloCompletoOCotizanSinCad = () => {
        if (tipo_solicitud.val() == 1 || tipo_solicitud.val() == 4) {
            return true;
        }
        return false;
    };
    const notMuestra = () => {
        if (tipo_solicitud.val() == 3) {
            return false;
        }
        return true;
    };
    const muestra = () => {
        if (tipo_solicitud.val() == 3) {
            return true;
        }
        return false;
    };
    const arteConMaterial = () => {
        if (tipo_solicitud.val() == 5) {
            return true;
        }
        return false;
    };

    // validacionCintaDibujoEstructural

    const validacionCintaDibujoEstructural = () => {
        // console.log($("#cinta").val(), role);
        if ($("#cinta").val() == 1 && (role == 5 || role == 6)) {
            return true;
        }
        return false;
    };

    const validacionPlantaObjetivo = () => {
        //Es obligatorio el campo Planta Objetivo (planta_id) si el ROL de vendedor y Dibujante Técnico (Diseño estructural)
        if ( role == 4 || role == 6 || role == 19 || (role == 8 && tipo == 'create')) {
            return true;
        }
        return false;
    };

    const validacionDesarrolloCompletoOArteConMaterial = () => {
        //Es obligatorio el campo RESTRICCIÓN PALLET (restriccion_pallet) si el tipo de solicitud es Desarrollo Completo o Arte en Material con el ROL de vendedor y Dibujante Técnico (Diseño estructural)
        if ( role == 4 || role == 3 || role == 19 || (role ==8 && tipo == 'create')) {
            return true;
        }
        return false;
    };

    const validacionImpresionBorde = () => {
        //Es obligatorio el campo ImpresionBordesi el ROL de vendedor
        if (role == 4 || role == 3 || role == 5 || role == 6) {

            return true;
        }
        return false;
    };

    const validacionSobreRayado = () => {
        //Es obligatorio el campo SobreRayado si el ROL de vendedor
        if (role == 4 || role == 3 || role == 5 || role == 6) {

            return true;
        }
        return false;
    };

    const validacionOrgVenta = () => {
        //Es obligatorio el campo SobreRayado si el ROL de vendedor
        if (role == 4) {

            return true;
        }
        return false;
    };

    const validacionRestriccionPallet = () => {
        //Es obligatorio el campo RESTRICCIÓN PALLET (restriccion_pallet) si el tipo de solicitud es Desarrollo Completo o Arte en Material con el ROL de vendedor
        if ( $("#restriccion_pallet").val() == 1 && ( role == 4 || role == 3 || role == 19 || (role == 8 && tipo == 'create')) ) {
            return true;
        }else if($("#restriccion_pallet").val() == 1 && role == 6){//Es obligatorio el campo RESTRICCIÓN PALLET (restriccion_pallet) si y el ROL es Dibujante Técnico (Diseño estructural)
            return true;
        }
        return false;
    }

    const validacionMaquila = () => {
        //Es obligatorio el campo Planta Objetivo (planta_id) si el ROL de vendedor y Dibujante Técnico (Diseño estructural)
        if ( role == 5 || role == 6 ) {

            return true;
        }
        return false;
    };

    let validacion_campos_numero = $("#validacion_campos").val().split(',');

    const validacionCrearMaterial = () => {

        if ( validacion_campos_numero.includes('2') && (tipo_solicitud.val() == 1 || tipo_solicitud.val() == 5) && role == 6) {

            if( (parseInt($("#interno_largo").val()) > parseInt($("#externo_largo").val())) || (parseInt($("#interno_ancho").val()) > parseInt($("#externo_ancho").val())) || (parseInt($("#interno_alto").val()) > parseInt($("#externo_alto").val())) ){
                $("#medida-interior-error").html('Las medidas interiores, deben ser menores a las medida exteriores');

                 // Ocultamos el mensaje
                 setTimeout(function() {
                    $("#medida-interior-error").html('');
                },10000);

                $("#prueba_required").val('');
            }else{
                $("#medida-interior-error").html('');

                $("#prueba_required").val('1');//Validacion para retener el formulario
            }

            return true;
        }
        return false;
    }

    const suma_anchura_hm = () => {

        let anchura_hm_ = $("#anchura_hm").val();

        // Se valida primero que el campo anchura hm este con datos
        if( anchura_hm_ != '' ){
            let suma_rayado = parseInt($("#rayado_c1r1").val()) + parseInt($("#rayado_r1_r2").val()) + parseInt($("#rayado_r2_c2").val());

            if(anchura_hm_ == suma_rayado){
                $("#prueba_required").val('1');//Validacion para retener el formulario

                return true;
            }else{
                $("#rayado-error").html('La suma de los campos Rayado, debe coincidir con el campo Anchura HM')

                // Ocultamos el mensaje
                setTimeout(function() {
                    $("#rayado-error").html('');
                },10000);
            }
        }else{
            return false;
        }

    }


    if(validacion_campos_numero.includes('3') || validacion_campos_numero.includes('4') || validacion_campos_numero.includes('0')){

        $("#prueba_required").val('1');
    }

    // const validacionDesarrolloCompletoConRolDiseñadorEstructural = () => {
    //     //Campos obligatorios para tipo de solicitud Desarrollo Completo y con Rol Dibujante Técnico (Diseño estructural)
    //     if ( tipo_solicitud.val() == 1 && role == 6) {
    //         return true;
    //     }
    //     return false;
    // }
    // VALIDACION DE FORMULARIO
    $("#form-ot")
        .submit(function (e) {
            e.preventDefault();
        })
        .validate({
            // Specify validation rules
            rules: {
                vendedor_id: "required",
                client_id: "required",
                descripcion: {
                    required: true,
                    maxlength: 40,
                },
                tipo_solicitud: "required",
                nombre_contacto: "required",
                email_contacto: { required: true, email: true },
                telefono_contacto: "required telefono",
                volumen_venta_anual: {
                    required: notMuestra,
                },
                usd: {
                    required: notMuestra,
                },
                canal_id: "required",
                hierarchy_id: "required",
                subhierarchy_id: {
                    required: function () {
                        return $("#hierarchy_id").val() != "";
                    },
                },
                subsubhierarchy_id: {
                    required: function () {
                        return $("#subhierarchy_id").val() != "";
                    },
                },
                // Solicita
                "checkboxes[]": { required: false },
                //numero_muestras: "required",
                // Referencia
                reference_type: {
                    required: arteConMaterial,
                },
                reference_id: {
                    required: function () {
                        return ( $("#reference_type").val() != '' && $("#reference_type").val() != 2);
                    },
                },
                bloqueo_referencia: {
                    required: function () {
                        return (  $("#reference_type").val() != '' && $("#reference_type").val() != 2 );
                    },
                },
                indicador_facturacion: {
                    required: function () {
                        return (
                            (tipo_solicitud.val() == 5 &&
                                (role == 7 || (role == 8 && tipo != 'create'))) ||
                            (tipo_solicitud.val() != 5 &&
                                (role == 5 || role == 6))
                        );
                    },
                },
                cad: {
                    required: function () {
                        // return role == 2;
                        return false;
                    },
                },
                cad_id: {
                    required: function () {
                        if($('#material_asignado').val()!=''){
                            return true;
                        }else{
                            return false;
                        }
                        // return role != 4;

                    },
                },
                product_type_id: {
                    required: desarrolloCompletoOCotizanSinCad,
                },
                // items_set: "required",
                // veces_item: "required",
                carton_id: {
                    required: muestra,
                },

                org_venta_id: {
                    required: validacionOrgVenta,
                },
                carton_color: {
                    required: function () {
                        return $("#carton_id").val() == "";
                    },
                },
                recubrimiento: {
                    required: true,
                },
                recorte_adicional: {
                    required: function () {
                        return (
                            tipo_solicitud.val() == 1 &&
                            (role == 5 || role == 6)
                        );
                    },
                },
                numero_colores: {
                    required: function () {
                        if(parseInt($("#impresion").val()) == 5 || parseInt($("#impresion").val()) == 6 || parseInt($("#impresion").val()) == 7){
                            return false;
                        }

                        if (
                            (parseInt(tipo_solicitud.val()) == 1 || parseInt(tipo_solicitud.val()) == 5 || parseInt(tipo_solicitud.val()) == 7) &&
                            (parseInt($("#impresion").val()) == 1 || parseInt($("#impresion").val()) == 2)
                        ) {
                            return true
                        }

                        if (parseInt(tipo_solicitud.val()) != 3) {
                            return true;
                        } else {
                            return false;
                        }
                        // return tipo_solicitud.val() != 3;
                    },
                },
                fsc: {
                    required: true,
                },

                impresion_borde: {
                    required: validacionImpresionBorde,
                },

                impresion_sobre_rayado: {
                    required: validacionSobreRayado,
                },
                // fsc_observacion: {
                //     required: function () {
                //         return $("#fsc").val() == 1 && (role == 7 || role == 8);
                //     },
                // },
                cera_exterior: {
                    required: true,
                },
                cera_interior: {
                    required: true,
                },
                barniz_interior: {
                    required: true,
                },
                // process_id: {
                //     required: arteConMaterial,
                // },
                pegado_terminacion: {
                    // required: function () {
                    //     return $("#process_id").val() == 4;
                    // },

                    required: function () {
                        return (
                            (role == 4 || role == 5 || role == 6 || (role == 8 && tipo == 'create'))// Obligatorio para Vendedor
                        );
                    },
                },
                armado_id: {
                    required: grupomaterial1,
                },
                sentido_armado: {
                    required: true,
                    // La logica es la siguiente
                    // Si el campo de ARMADO es obligatorio (es obligatorio cuando tipo_solicitud es 1 o 4), se podria configurar el campo de Sentido de Armado sea obligatorio cuando se seleciona la opción ARMADO PROPIO o ARMADO CLIENTE. Si el campo ARMADO no es obligatorio, el campo de Sentido de armado deberá ser siempre obligatorio.
                    // required: function () {
                    //     if (
                    //         tipo_solicitud.val() == 1 ||
                    //         tipo_solicitud.val() == 4
                    //     ) {
                    //         if (armado_id.val() == 4 || armado_id.val() == 5) {
                    //             return true;
                    //         }
                    //         return false;
                    //     }
                    //     return true;
                    // },
                },
                // datos para desarrollo
                peso_contenido_caja: {
                    required: desarrolloCompletoOCotizanSinCad,
                },
                autosoportante: {
                    required: desarrolloCompletoOCotizanSinCad,
                },
                envase_id: {
                    required: desarrolloCompletoOCotizanSinCad,
                },
                cajas_altura: {
                    required: desarrolloCompletoOCotizanSinCad,
                },
                impresion: {
                    required: true,
                },

                pallet_sobre_pallet: {
                    required: desarrolloCompletoOCotizanSinCad,
                },
                cantidad: {
                    required: function () {
                        return $("#pallet_sobre_pallet").val() == 1;
                    },
                },
                observacion: {
                    required: true,
                    minlength: 10,
                    maxlength: 1000,
                },
                // unidad_medida_bct: {
                //     required: function () {
                //         return $("#rmt").val() != "";
                //     },
                // },
                cinta: {
                    // Solo es requerida si tipo solicitud es desarrollo completo o arete con material
                    required: function () {
                        return (
                            tipo_solicitud.val() == 1 ||
                            tipo_solicitud.val() == 5
                        );
                    },
                },
                // Campoas requeridos solo si cinta = SI y Solo si es Role 5 o 6, Dibujo estructural
                corte_liner: {
                    required: validacionCintaDibujoEstructural,
                },
                tipo_cinta: {
                    required: validacionCintaDibujoEstructural,
                },

                cintas_x_caja: {
                    required: validacionCintaDibujoEstructural,
                },
                distancia_cinta_1: {
                    required: validacionCintaDibujoEstructural,
                },
                distancia_cinta_2: {
                    required: validacionCintaDibujoEstructural,
                },
                distancia_cinta_3: {
                    required: validacionCintaDibujoEstructural,
                },
                distancia_cinta_4: {
                    required: validacionCintaDibujoEstructural,
                },
                distancia_cinta_5: {
                    required: validacionCintaDibujoEstructural,
                },
                distancia_cinta_6: {
                    required: validacionCintaDibujoEstructural,
                },
                pais_id: {
                    // Solo es requerido si el campo FSC () es si
                    required: function () {
                        return $("#fsc").val() == 1;
                    },
                },
                planta_id: {
                    //Es obligatorio el campo Planta Objetivo (planta_id) si el ROL de vendedor y Dibujante Técnico (Diseño estructural)
                    required: validacionPlantaObjetivo,
                },
                sec_operacional_original: {
                    required: function(){

                        if(planta_id != '' && planta_id != 0){
                            return true;
                        }else{
                            return false;
                        }
                    }
                },
                restriccion_pallet: {
                    //Es obligatorio el campo RESTRICCIÓN PALLET (restriccion_pallet) si el tipo de solicitud es Desarrollo Completo o Arte en Material con el ROL de vendedor
                    required: validacionDesarrolloCompletoOArteConMaterial,
                },
                tamano_pallet_type_id: {
                    // Solo es requerido si el campo RESTRICCIÓN PALLET (restriccion_pallet) es si
                    required: validacionRestriccionPallet,
                },
                altura_pallet: {
                     // Solo es requerido si el campo RESTRICCIÓN PALLET (restriccion_pallet) es si
                    required: validacionRestriccionPallet,
                },
                permite_sobresalir_carga: {
                     // Solo es requerido si el campo RESTRICCIÓN PALLET (restriccion_pallet) es si
                    required: validacionRestriccionPallet,
                },
                anchura_hm:{
                    // Solo es requerido si estan vacios y si el tipo de solicitud es Desarrollo completo (1) o Arte con material (5) y el tipo de proceso es
                    //Flexo o Flexo con Matriz Parcial
                     required: function () {
                        if (validacion_campos_numero.includes('1') && role == 6 && (tipo_solicitud.val() == 1 || tipo_solicitud.val() == 5) && ($("#process_id").val() == 1 || $("#process_id").val() == 5)) {
                            if($("#anchura_hm").attr('disabled') || $("#anchura_hm").prop('disabled') || $("#anchura_hm").attr('readonly') || $("#anchura_hm").prop('readonly')){
                                return false;
                            }
                            return true;
                        }
                        return false;
                    },
                },
                rayado_c1r1:{
                    // Solo es requerido si estan vacios y si el tipo de solicitud es Desarrollo completo (1) o Arte con material (5) y el tipo de proceso es
                    //Flexo o Flexo con Matriz Parcial
                     required: function () {
                        if (validacion_campos_numero.includes('1') && role == 6 && (tipo_solicitud.val() == 1 || tipo_solicitud.val() == 5) && ($("#process_id").val() == 1 || $("#process_id").val() == 5)) {
                            if($("#rayado_c1r1").attr('disabled') || $("#rayado_c1r1").prop('disabled') || $("#rayado_c1r1").attr('readonly') || $("#rayado_c1r1").prop('readonly')){
                                return false;
                            }
                            return true;
                        }
                        return false;
                    },
                },
                rayado_r1_r2:{
                    // Solo es requerido si estan vacios y si el tipo de solicitud es Desarrollo completo (1) o Arte con material (5) y el tipo de proceso es
                    //Flexo o Flexo con Matriz Parcial
                     required: function () {
                        if (validacion_campos_numero.includes('1') && role == 6 && (tipo_solicitud.val() == 1 || tipo_solicitud.val() == 5) && ($("#process_id").val() == 1 || $("#process_id").val() == 5)) {
                            if($("#rayado_r1_r2").attr('disabled') || $("#rayado_r1_r2").prop('disabled') || $("#rayado_r1_r2").attr('readonly') || $("#rayado_r1_r2").prop('readonly')){
                                return false;
                            }
                            return true;
                        }
                        return false;
                    },
                },
                rayado_r2_c2:{
                    // Solo es requerido si estan vacios y si el tipo de solicitud es Desarrollo completo (1) o Arte con material (5) y el tipo de proceso es
                    //Flexo o Flexo con Matriz Parcial
                    required: function () {
                        if (validacion_campos_numero.includes('1') && role == 6 && (tipo_solicitud.val() == 1 || tipo_solicitud.val() == 5) && ($("#process_id").val() == 1 || $("#process_id").val() == 5)) {
                            if($("#rayado_r2_c2").attr('disabled') || $("#rayado_r2_c2").prop('disabled') || $("#rayado_r2_c2").attr('readonly') || $("#rayado_r2_c2").prop('readonly')){
                                return false;
                            }
                            suma_anchura_hm();
                            return true;
                        }
                        return false;
                    },
                },
                interno_largo:{
                    // Solo es requerido si estan vacios y si el tipo de solicitud es Desarrollo completo (1) o Arte con material (5)
                    required: validacionCrearMaterial,
                },
                interno_ancho:{
                    // Solo es requerido si estan vacios y si el tipo de solicitud es Desarrollo completo (1) o Arte con material (5)
                    required: validacionCrearMaterial,
                },
                interno_alto:{
                    // Solo es requerido si estan vacios y si el tipo de solicitud es Desarrollo completo (1) o Arte con material (5)
                    required: validacionCrearMaterial,
                },
                externo_largo:{
                    // Solo es requerido si estan vacios y si el tipo de solicitud es Desarrollo completo (1) o Arte con material (5)
                    required: validacionCrearMaterial,
                },
                externo_ancho:{
                    // Solo es requerido si estan vacios y si el tipo de solicitud es Desarrollo completo (1) o Arte con material (5)
                    required: validacionCrearMaterial,
                },
                externo_alto:{
                    // Solo es requerido si estan vacios y si el tipo de solicitud es Desarrollo completo (1) o Arte con material (5)
                    required: validacionCrearMaterial,
                },
                prueba_required :{
                    required: true,
                },
                longitud_pegado:{
                    required: function () {
                        if(parseInt(tipo_solicitud.val()) == 1 || parseInt(tipo_solicitud.val()) == 5) {
                            if(parseInt(role) != 4 && parseInt(role) != 18 && parseInt(role) != 19 && (role == 8 && tipo != 'create')){
                                return $("#pegado_terminacion").val() != '';
                            }else{
                                return false;
                            }
                        } else {
                            return false;
                        }
                    },
                },
                design_type_id:{
                    required: function () {
                        console.log($("#impresion").val());
                        if (parseInt(tipo_solicitud.val()) == 1 || parseInt(tipo_solicitud.val()) == 5 || parseInt(tipo_solicitud.val()) == 7) {
                            return (
                                ($("#impresion").val() == 2)
                            );
                        }
                    },
                },
                coverage_internal_id: {
                    required: function () {
                        return (
                            (role == 4 || role == 8 || role == 19 )// Obligatorio para Vendedor y Diseñador Gráfico
                        );
                    },
                },
                percentage_coverage_internal: {
                    required: function () {
                        if(role == 4 || role == 8 || role == 19){
                            if($("#coverage_internal_id").val() != 1){
                                return true;
                            }else{
                                return false;
                            }
                        }else{
                            return false;
                        }

                    },
                },
                coverage_external_id: {
                    required: function () {
                        return (
                            (role == 4 || role == 8 || role == 19)// Obligatorio para Vendedor y Diseñador Gráfico
                        );
                    },
                },
                percentage_coverage_external: {
                    required: function () {

                        if(role == 4 || role == 8 || role == 19){
                            if($("#coverage_external_id").val() != 1){
                                return true;
                            }else{
                                return false;
                            }
                        }else{
                            return false;
                        }
                    },
                },
                //El color interno, solo sera obligatorio para el rol de Diseñador ( D.G. ) y cuando la impresion sea Tiro y Retiro
                color_interno: {
                    required: function () {

                        if(parseInt(role) === 8 && parseInt($("#impresion").val()) === 4){
                            return true;
                        }

                    },
                },
                 //El % de impresión color interno, solo sera obligatorio para el rol de Diseñador ( D.G. ) y cuando la impresion sea Tiro y Retiro
                impresion_color_interno: {
                    required: function () {

                        if(parseInt(role) === 8 && parseInt($("#impresion").val()) === 4){
                            return true;
                        }else{
                            return false;
                        }

                    },
                },
                // color_6_id:{
                //     required: function () {

                //         if((parseInt(role) === 4 || parseInt(role) === 19) && parseInt($("#impresion").val()) === 3 && $("#barniz_uv").val() === ''){

                //             return true;
                //         }
                //     },
                // },
                // impresion_6:{
                //     required: function () {

                //         if((parseInt(role) === 4 || parseInt(role) === 19 || (role == 8 && tipo == 'create')) && parseInt($("#impresion").val()) === 3){

                //             if(parseInt($("#barniz_uv").val()) === 1 || parseInt($("#barniz_uv").val()) === 0){
                //                 return false
                //             }else{
                //                 return true;
                //             }

                //         }else{
                //             return false
                //         }

                //     },
                // },
                barniz_uv:{
                    required: function () {

                        if(parseInt(role) === 8 && parseInt($("#coverage_external_id").val()) === 4){
                            return true;
                        }else{
                            return false;
                        }
                    },
                },
                porcentanje_barniz_uv:{

                    required: function () {

                        if(parseInt(role) === 8 && parseInt($("#coverage_external_id").val()) === 4){
                            return true;
                        }else{
                            return false;
                        }
                    },
                },
                product_type_developing_id:{

                    required: function () {

                        if(parseInt(role) != 18){
                            return true;
                        }else{
                            return false;
                        }
                    },
                },
                target_market_id:{

                    required: function () {

                        if(parseInt(role) != 18){
                            return true;
                        }else{
                            return false;
                        }
                    },
                },
                maquila: {
                    required: validacionMaquila,
                },
                /*tipo_tabique: {
                    required: function () {
                        return ( ($("#product_type_id").val() == 18 ||
                                  $("#product_type_id").val() == 19 ||
                                  $("#product_type_id").val() == 20) && (role==5 || role==6)
                                ); ///Ajuste Evolutivo 24-06 de fecha 03-04-2024 correo del cliente
                    },
                },
                rayado_desfasado: {
                    required: function () {
                        return ( ($("#product_type_id").val() == 3 ||
                                  $("#product_type_id").val() == 4 ||
                                  $("#product_type_id").val() == 5) && (role==5 || role==6)
                                ); ///Ajuste Evolutivo 24-06 de fecha 03-04-2024 correo del cliente
                    },
                },*/
                oc_file: {
                    required: function () {
                        if(role==3||role==4){
                            if($("#oc").val() ==1 && $("#oc_file_exist").val()==0){
                                return true;
                            }else{
                                return false;
                            }
                        }else{
                            return false;
                        }
                    },
                },
                caracteristicas_adicionales: {
                    required: function () {
                        if(role==5||role==6){
                            $('#caracteristicas_adicionales').prop('readonly', true);
                            return true;
                        }else{
                            return false;
                        }
                    },
                },
                armado_automatico: {

                    required: function () {
                        return (
                            (role == 4)// Obligatorio para Vendedor
                        );
                    },
                },

            },
            // Specify validation error messages
            messages: {},
            errorClass: "error",
            errorPlacement: function (error, element) {
                // si es un select o el error es por campo requerido que no es un checkbox entonces  no mostramos el mensaje de error,
                //   solo se marca en rojo
                if (
                    element.is("select") ||
                    (error.html() == "Campo obligatorio." &&
                        !element.is(":checkbox"))
                ) {
                    return false;
                } else {
                    if (!element.is(":checkbox")) {
                        error.insertAfter(element);
                    } else {
                        error.insertAfter($("#checkbox-card"));
                    }
                }
            },
            highlight: function (element, errorClass) {
                $(element).closest("div.form-group").addClass("error");
            },
            unhighlight: function (element, errorClass) {
                $(element).closest("div.form-group").removeClass("error");
            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid
            submitHandler: function (form) {
                form.submit();
            },
        });

    // FIN VALIDACION

    // Crear event listener en formulario para limpiar errores de selects
    $("#form-ot").on("change", "select", function (e) {
        // console.log($(this), e);
        $(this).closest("div.form-group").removeClass("error");
        e.stopPropagation();
    });
    // $("select").change(function () {
    //     $(this).closest("div.form-group").removeClass("error");
    // });

    // // Funcionalidades compartidas de formulario
    $("#cinta")
        .change(() => {
            if ($("#cinta").val() == 1) {
                $("#ot-distancia-cinta").show();
            } else {
                // ocultar div
                $("#ot-distancia-cinta").hide();
            }
        })
        .triggerHandler("change");

    // $("#fsc")
    //     .change(() => {
    //         if ($("#fsc").val() == 1 && (role == 7 || role == 8)) {
    //             $("#fsc_observacion")
    //                 .prop("disabled", false)
    //                 .prop("readonly", false)
    //                 .selectpicker("refresh")
    //                 .closest("div.form-group")
    //                 .removeClass("error");
    //         } else if ($("#fsc").val() == 1) {
    //             $("#fsc_observacion")
    //                 .prop("disabled", true)
    //                 .prop("readonly", true)
    //                 .selectpicker("refresh")
    //                 .closest("div.form-group")
    //                 .removeClass("error");
    //         } else {
    //             $("#fsc_observacion")
    //                 .prop("disabled", true)
    //                 .prop("readonly", true)
    //                 .val("")
    //                 .selectpicker("refresh")
    //                 .closest("div.form-group")
    //                 .removeClass("error");
    //         }
    //     })
    //     .triggerHandler("change");

    $("#bct_min_lb").on("keyup change", function () {
        if ($(this).val()) {
            $("#bct_min_kg").val(Math.round($("#bct_min_lb").val() * 0.4535));
        } else {
            $("#bct_min_kg").val("");
        }
    });

    // ajax para llenar los contactos del cliente
    const client_id = $("#client_id");
    client_id.on("change", function () {

        var val = client_id.val();
        return $.ajax({
            type: "GET",
            url: "/getInstalacionesCliente",
            data: "client_id=" + val,
            success: function (data) {
                data = $.parseHTML(data);
                console.log('VALIDACIONold');
                $("#instalacion_cliente")
                    .empty()
                    .append(data)
                    .selectpicker("refresh");

                getIndicacionesEspeciales(val);
            },
            error: function(e) {
                console.log(e.responseText);
            },
            async:true
        });

    });

    const instalation_id = $("#instalacion_cliente");
    instalation_id.on("change", function () {

        var val = instalation_id.val();
        return $.ajax({
            type: "GET",
            url: "/getInformacionInstalacion",
            data: "instalation_id=" + val,
            success: function (data) {
                contactos = $.parseHTML(data.contactos);
                $("#contactos_cliente")
                    .empty()
                    .append(contactos)
                    .selectpicker("refresh");
                $("#altura_pallet")
                    .val(data.altura_pallet);
                $("#permite_sobresalir_carga")
                    .val(data.sobresalir_carga)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                $("#tamano_pallet_type_id")
                    .val(data.tipo_pallet)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                $("#bulto_zunchado")
                    .val(data.bulto_zunchado)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                $("#formato_etiqueta")
                    .val(data.formato_etiqueta)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                $("#etiquetas_pallet")
                    .val(data.etiquetas_pallet);
                // $("#termocontraible")
                //     .val(data.termocontraible)
                //     .selectpicker("refresh")
                //     .closest("div.form-group")
                //     .removeClass("error");
                $("#pais_id")
                    .val(data.pais_mercado_destino)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                $("#pallet_qa_id")
                    .val(data.certificado_calidad)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                $("#fsc")
                    .val(data.fsc)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                $("#fsc_instalation")
                    .val(data.fsc);
            },
            error: function(e) {
                console.log(e.responseText);
            },
            async:true
        });
    });

    // ajax para llenar los datos del contacto luego de seleccionarlo
    const contactos_cliente = $("#contactos_cliente");
    const nombre_contacto = $("#nombre_contacto");
    const email_contacto = $("#email_contacto");
    const telefono_contacto = $("#telefono_contacto");
    contactos_cliente.on("change", function () {
        var val = contactos_cliente.val();
        return $.ajax({
            type: "GET",
            url: "/getDatosContactoInstalacion",
            data: "contactos_cliente=" + val + "&instalation_id=" + instalation_id.val(),
            success: function (data) {
                nombre_contacto
                    .val(data.nombre_contacto)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                email_contacto
                    .val(data.email_contacto)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
                telefono_contacto
                    .val(data.telefono_contacto)
                    .selectpicker("refresh")
                    .closest("div.form-group")
                    .removeClass("error");
            },
            error: function(e) {
                console.log(e.responseText);
            },
            async:true
        });
    });
});

function getIndicacionesEspeciales(cliente) {
    $("#client_indicaciones_view tbody").html('');
    return $.ajax({
        type: "GET",
        url: "/getIndicacionesEspeciales",
        data: "client_id=" + cliente,
        success: function (data) {
            console.log(data);
            if(data){

                $('#seccion_indicaciones_especiales').show();
                $("#client_indicaciones_view tbody").html('');
                $("#client_indicaciones_view tbody").html(data);
                $('#indicaciones-especiales').modal('show');

            }else{
                $('#seccion_indicaciones_especiales').hide();
                $("#client_indicaciones_view tbody").html('');
                $('#indicaciones-especiales').modal('hide');
            }


        },
        error: function(e) {
            console.log(e.responseText);
        },
        async:true
    });
}

