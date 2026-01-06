@extends('layouts.index')

@section('content')
<div class="container-fluid">
  <h1 class="page-title">Indicadores Sala de Muestras</h1>
</div>

<style>
  .filter-form {
    background-color: #FFF;
    position: relative;
    padding: 15px;
    padding-top: 15px;
    padding-bottom: 15px;
    border-radius: 10px;
    -webkit-box-shadow: 0px 3px 5px 1px #aaa;
    box-shadow: 0px 3px 5px 1px #aaa;
    margin-bottom: 30px;
    -webkit-transition: all ease 0.5s;
    -o-transition: all ease 0.5s;
    transition: all ease 0.5s;
  }

  .container-report {
    height: 100%;
    margin-top:15px;
  }

  .horizontal-scroll {
    border-radius: 0px;
    overflow-x: auto;
  }

  .container-muestras {
      background-color: #dadada;
      border: 1px solid #727272;
  }

  .container-information {
    display: flex;
    flex-direction: column;
  }

  .container-pendient{
    display: flex;
    flex-direction: row;
    justify-content: space-around;
  }

  .container-result{
    display: flex;
    justify-content: center;
  }

  .container-number{
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    font-size:40px;
  }

  .legend {
    list-style: none;
    display: inline-block;
    text-align: center;
    /* padding-top: 10px; */
    /* width: 100%; */
    display: flex;
    justify-content: center;
    align-items: stretch;
  }

  .legend li {
    float: left;
    margin-right: 10px;
    font-size: 12px;
    display: flex;
    align-items: center;
  }

  .legend span {
    border: 1px solid #fff;
    float: left;
    width: 30px;
    height: 15px;
    margin: 2px;
    border-radius: 3px;
  }

</style>
<div class="container-fluid">
  <form id="filtroReporte" class="filter-form py-1" action="{{ route('reportIndicadorSalaMuestraNew1') }}" method="get" enctype="multipart/form-data">
    <div class="form-row">
      <div class="col-2">
        <div class="form-group">
          <label>Mes</label>
          <!-- <select name="mes" id="mes" class="selectpicker form-control form-control-sm" data-live-search="false"></select> -->
          <select name="mes" id="mes" class="selectpicker form-control form-control-sm" data-live-search="false">
            <option value="1" {{ (isset($mes) and !is_null($mes) and $mes=='1')? 'selected=selected':'' }}>ENERO</option>
            <option value="2" {{ (isset($mes) and !is_null($mes) and $mes=='2')? 'selected=selected':'' }}>FEBRERO</option>
            <option value="3" {{ (isset($mes) and !is_null($mes) and $mes=='3')? 'selected=selected':'' }}>MARZO</option>
            <option value="4" {{ (isset($mes) and !is_null($mes) and $mes=='4')? 'selected=selected':'' }}>ABRIL</option>
            <option value="5" {{ (isset($mes) and !is_null($mes) and $mes=='5')? 'selected=selected':'' }}>MAYO</option>
            <option value="6" {{ (isset($mes) and !is_null($mes) and $mes=='6')? 'selected=selected':'' }}>JUNIO</option>
            <option value="7" {{ (isset($mes) and !is_null($mes) and $mes=='7')? 'selected=selected':'' }}>JULIO</option>
            <option value="8" {{ (isset($mes) and !is_null($mes) and $mes=='8')? 'selected=selected':'' }}>AGOSTO</option>
            <option value="9" {{ (isset($mes) and !is_null($mes) and $mes=='9')? 'selected=selected':'' }}>SEPTIEMBRE</option>
            <option value="10" {{ (isset($mes) and !is_null($mes) and $mes=='10')? 'selected=selected':'' }}>OCTUBRE</option>
            <option value="11" {{ (isset($mes) and !is_null($mes) and $mes=='11')? 'selected=selected':'' }}>NOVIEMBRE</option>
            <option value="12" {{ (isset($mes) and !is_null($mes) and $mes=='12')? 'selected=selected':'' }}>DICIEMBRE</option>
          </select>
        </div>
      </div>
      <div class="col-2">
        <div class="form-group">
          <label>Año</label>
          <select name="year[]" id="year" class="selectpicker form-control form-control-sm" data-live-search="false">
            @foreach($years as $year)
            <option value="{{$year}}">{{$year}}</option>
            @endforeach
          </select>
        </div>
      </div>

    </div>
    <div class="text-right">
      <button id="filtrarSubmit" class="ml-auto btn btn-primary col-2">Filtrar</button>
    </div>
  </form>
</div>

{{-- Cantidad de muestras gestionadas --}}
<div  class="filter-form py-1">
    <h5 class="header-report">Muestras Gestionadas {{$mesSeleccionado}} {{$yearSeleccionado}}</h5>
        <div class="container-report">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="item-report item-report_sm container-muestras" style="height:auto;">
                            <h6 class="header-report"><label style="font-size:18px;color:#14880f;">MUESTRAS</label></h6>
                            <div class="container-information">
                                <div class="container-pendient">
                                    <div>
                                        <p style="text-transform:uppercase;font-size:10px;">Pendiente Corte</p>
                                        <label class="container-number" style="color:#12580f">
                                            {{$muestrasPendientesCorte}}
                                        </label>

                                    </div>
                                    <div>
                                        <p style="text-transform:uppercase;font-size:10px;">Pendiente Término</p>
                                        <label class="container-number" style="color:#28a745;">
                                            {{$muestrasPendientesTermino}}
                                        </label>

                                    </div>

                                </div>
                                <div class="container-result">
                                    <div>
                                        <p style="text-transform:uppercase;font-size:10px;">Terminadas</p>
                                        <label class="container-number" style="color:#28a745;">
                                          {{$muestrasTerminadas}}
                                        </label>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="item-report item-report_sm container-muestras" style="height:auto;">
                            <h6 class="header-report"><label style="font-size:18px;color:#14880f;">OT CON MUESTRAS</label></h6>
                            <div class="container-information">
                                <div class="container-pendient">
                                    <div>
                                        <p style="text-transform:uppercase;font-size:10px;">Pendiente Corte</p>
                                        <label class="container-number" style="color:#12580f">
                                            {{$muestrasPendientesCortePorOt}}
                                        </label>

                                    </div>
                                    <div>
                                        <p style="text-transform:uppercase;font-size:10px;">Pendiente Término</p>
                                        <label class="container-number" style="color:#28a745;">
                                           {{$muestrasPendientesTerminoPorOt}}
                                        </label>

                                    </div>

                                </div>
                                <div class="container-result">
                                    <div>
                                        <p style="text-transform:uppercase;font-size:10px;">Terminadas</p>
                                        <label class="container-number" style="color:#28a745;">
                                            {{$muestrasTerminadasPorOt}}
                                        </label>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

{{-- graficos por muestras: --}}
<div class="container-report">
  <div class="item-report item-report_sm col-12" style="height:auto;">
    <canvas id="myChart" height="100"></canvas>
    <ul class="legend">
      <li data-toggle="tooltip" data-html="true" title="Cantidad De Muestras"><span style="background-color: #22951d;color: white;"></span>Cantidad De Muestras</li>
      <li data-toggle="tooltip" data-html="true" title="OT Con Muestras"><span style="background-color: #6ad766;color: red;"></span>OT Con Muestras</li>
    </ul>
  </div>
</div>



{{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> --}}{{--
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> --}}


@endsection
@section('myjsfile')
<!-- chartjs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<!-- gauge js -->
<script src="{{ asset('js/gauge.min.js') }}"></script>
<script src="{{ asset('js/reports2.js') }}"></script>

 <script>
  $(document).ready(function() {
    // Funcionabilidad para filtrar
    $(document).on('click', '#filtrarSubmit', function(e) {
      e.preventDefault();
      $('#filtroReporte').submit();
    });

    // const MESES = [
    //   {'id': 1, 'mes': 'ENEsdsdsdRO'},
    //   {'id': 2, 'mes': 'FEBRERO'},
    //   {'id': 3, 'mes': 'MARZO'},
    //   {'id': 4, 'mes': 'ABRIL'},
    //   {'id': 5, 'mes': 'MAYO'},
    //   {'id': 6, 'mes': 'JUNIO'},
    //   {'id': 7, 'mes': 'JULIO'},
    //   {'id': 8, 'mes': 'AGOSTO'},
    //   {'id': 9, 'mes': 'SEPTIEMBRE'},
    //   {'id': 10, 'mes': 'OCTUBRE'},
    //   {'id': 11, 'mes': 'NOVIEMBRE'},
    //   {'id': 12, 'mes': 'DICIEMBRE'},
    // ];

    // for (const mes of MESES) {
    //   $("select#mes").append(new Option(mes.mes, mes.id));
    // }
    // Seteo el mes seleccionado o el mes actual por defecto
    // let mesSeleccionado = {{ $mes }}
    // $("select#mes").val(mesSeleccionado);


    // Detecta cambio de año y recorre nuevamente los meses
    // $('select#year').on('change', function(e) {
    //   console.log('Cambio de mes')
    //   const value = this.value;
    //   const dateNow = new Date();
    //   const year = dateNow.getFullYear();

    //   const month = dateNow.getMonth();
    //   $(document).find('select#mes').empty();
    //   if (parseInt(value) === year) {
    //     console.log('es el anio actual')
    //     for (const mes of MESES) {
    //       console.log({mes})
    //       if (parseInt(mes.id) <= parseInt(month)) {
    //         console.log('asdjnkasdnkasjd')
    //         $(document).find("select#mes").append(new Option(mes.mes, mes.id));
    //       } else {
    //         return;
    //       }
    //     }
    //   }
    // })


    //Nombre de meses para grafica
    var nombreMesesSeleccionados = @json($nombreMesesSeleccionados);

    // Cantidad de Suma
    var muestrasTerminadas       = @json($muestrasTerminadas);
    var muestrasTerminadasPorOt  = @json($muestrasTerminadasPorOt);

    //Muestras terminadas de grafica
    var muestrasTerminadasGrafica = @json($muestrasTerminadasGrafica);

    // contruir reportes:
    // generar reporte de muestra:
    generar_reporte_indicadores_sala_muestra(nombreMesesSeleccionados,muestrasTerminadas,muestrasTerminadasPorOt,muestrasTerminadasGrafica);



  });
</script>
@endsection
