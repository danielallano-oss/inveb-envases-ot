@extends('layouts.index', ['dontnotify' => true])

@section('content')
    <h1 class="page-title">Ingreso Nueva Orden de Trabajo</h1>
    <div class="row mb-3">
        <div class="col-12">
            <section id="ficha" class="py-3">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $mensaje)
                                <li>{{ $mensaje }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form
                    onsubmit="$(this).find('#tipo_solicitud,#ajuste_area_desarrollo,#cad_id,#reference_type,#carton_id,#carton_color,#product_type_id,#style_id,#muestra,#numero_muestras,#design_type_id,#complejidad,#numero_colores,#indicador_facturacion_diseno_grafico,#org_venta_id,#caracteristicas_adicionales, #golpes_largo, #golpes_ancho, #separacion_golpes_largo,#separacion_golpes_ancho,#cuchillas').prop('disabled', false)"
                    id="form-ot" method="POST" action="{{ route('storeOt') }}" enctype="multipart/form-data">
                    <div id="ot-select-solicitud" class="col-12 mb-3">
                        <div class="card h-100">
                            <div class="card-header">1.- Seleccione el Tipo de Solicitud</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        {!! armarSelectArrayCreateEditOT(
                                            $tipos_solicitud,
                                            'tipo_solicitud_select_ppal',
                                            'Tipo de solicitud:',
                                            $errors,
                                            null,
                                            'form-control',
                                            true,
                                            false,
                                        ) !!}
                                    </div>
                                    <div class="col-3">
                                        <button onclick="location.reload()" class="btn btn-success" type="button">Reiniciar
                                            Solicitud</button>
                                    </div>
                                    <div class="col-5">
                                        <div class="col-12">
                                            <div id="select_sub_tipo" style="display:none;">
                                                {!! armarSelectArrayCreateEditOT(
                                                    $ajustes_area_desarrollo,
                                                    'ajuste_area_desarrollo_select_ppal',
                                                    'Tipo de Ajuste Area Desarrollo:',
                                                    $errors,
                                                    null,
                                                    'form-control',
                                                    true,
                                                    false,
                                                ) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="ot-view" class="col-12 mb-3">
                    </div>
                </form>
            </section>
        </div>
    </div>

    <style>
        #loading {
            width: 100%;
            height: 100%;
            top: 0px;
            left: 0px;
            position: fixed;
            display: block;
            z-index: 99;
            background-color: rgba(0, 0, 0, 0.15);
        }

        .loader {
            position: absolute;
            top: 40%;
            left: 45%;
            z-index: 100;

        }
    </style>
    <!-- Loading  -->
    <div id="loading">
        <div id="modal-loader" class="loader">Loading...</div>
    </div>
@endsection
@section('myjsfile')
    <script>
        window.onload = function() {
            document.getElementById("loading").style.display = "none"
        }
    </script>
    <!-- Solo hay ot si es una duplicacion y no creacion -->

    <script>
        $('#tipo_solicitud_select_ppal').on('change', function() {

            var select = this.value;

            if (select == 6) {
                $("#select_sub_tipo").show();
            } else {
                $("#select_sub_tipo").hide();
                $('#loading').show();
                var urlajax = '/crear-ot'; //select == 1
                if (select == 3 || select == 5) {
                    urlajax = '/crear-ot-old';
                }

                $.ajax({
                    type: 'get',
                    url: urlajax,
                    data: {},
                    success: function(data) {

                        //obtenemos la vista con las datos del formulario
                        $('#ot-view').html(data);

                        //refrescar los selectores
                        $("#ot-view .selectpicker")
                            .prop("disabled", false)
                            .prop("readonly", false)
                            .val("")
                            .selectpicker("refresh");

                        //Se oculta el icono de loading
                        $('#loading').hide();

                        //seteamos el tipo de solicitud seleccionado e inhaibilitamos
                        $("#ot-view #tipo_solicitud").val($("#tipo_solicitud_select_ppal").val())
                            .trigger("change");
                        $("#ot-view #tipo_solicitud")
                            .prop("disabled", true)
                            .prop("readonly", true)
                            .selectpicker("refresh");

                        //inhaibilitamos selector principal
                        $("#tipo_solicitud_select_ppal")
                            .prop("disabled", true)
                            .prop("readonly", true)
                            .val(select)
                            .selectpicker("refresh");

                    },
                    error: function(e) {
                        console.log(e.responseText);
                        $('#loading').hide();
                    },
                    async: true
                });
            }
        });

        $('#ajuste_area_desarrollo_select_ppal').on('change', function() {

            var select = this.value;
            var select_tipo = $('#tipo_solicitud_select_ppal').val();
            $('#loading').show();
            if (select == 1) {
                urlajax = '/crear-licitacion';
            } else {
                if (select == 2) {
                    urlajax = '/crear-ficha-tecnica';
                } else {
                    urlajax = '/crear-estudio-benchmarking';
                }
            }

            $.ajax({
                type: 'get',
                url: urlajax,
                data: {},
                success: function(data) {

                    //obtenemos la vista con las datos del formulario
                    $('#ot-view').html(data);

                    //refrescar los selectores
                    $("#ot-view .selectpicker")
                        .prop("disabled", false)
                        .prop("readonly", false)
                        .val("")
                        .selectpicker("refresh");

                    //Se oculta el icono de loading
                    $('#loading').hide();

                    //seteamos el tipo de solicitud seleccionado e inhaibilitamos
                    $("#ot-view #tipo_solicitud").val($("#tipo_solicitud_select_ppal").val()).trigger(
                        "change");
                    $("#ot-view #tipo_solicitud")
                        .prop("disabled", true)
                        .prop("readonly", true)
                        .selectpicker("refresh");

                    $("#ot-view #ajuste_area_desarrollo").val($("#ajuste_area_desarrollo_select_ppal")
                        .val()).trigger("change");
                    $("#ot-view #ajuste_area_desarrollo")
                        .prop("disabled", true)
                        .prop("readonly", true)
                        .selectpicker("refresh");

                    //inhaibilitamos selector principal
                    $("#tipo_solicitud_select_ppal")
                        .prop("disabled", true)
                        .prop("readonly", true)
                        .val(select_tipo)
                        .selectpicker("refresh");

                    $("#ajuste_area_desarrollo_select_ppal")
                        .prop("disabled", true)
                        .prop("readonly", true)
                        .val(select)
                        .selectpicker("refresh");

                },
                error: function(e) {
                    console.log(e.responseText);
                    $('#loading').hide();
                },
                async: true
            });


        });
    </script>



    <!-- ////////////////SCRIPT DE MUESTRAS Y GESTIONES DE MUESTRAS -->


    <!-- //////////////// FIN FIN FIN SCRIPT DE MUESTRAS Y GESTIONES DE MUESTRAS -->
@endsection
