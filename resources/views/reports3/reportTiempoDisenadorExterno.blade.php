@extends('layouts.index')

@section('content')
<div class="container-fluid">
  <h1 class="page-title">Diseñador Externo</h1>
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

  .container-report-various {
    height: 100%;
    margin-top:15px;
    display: flex;
    flex-direction: row;
  }

  .container-number{
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    font-size:40px;
  }
</style>

{{-- Filtro de búsqueda --}}
<div class="container-fluid">
  <form id="filtroReporte" class="filter-form py-1" action="{{ route('reportTiempoDisenadorExternoNew1') }}" method="get" enctype="multipart/form-data">
    <div class="form-row">
      <div class="col-2">
        <div class="form-group">
          <label>Mes</label>
          <select name="mes" id="mes" class="selectpicker form-control form-control-sm" data-live-search="false">
            <option value="01" {{ (isset($mes) and !is_null($mes) and $mes=='1')? 'selected=selected':'' }}>ENERO</option>
            <option value="02" {{ (isset($mes) and !is_null($mes) and $mes=='2')? 'selected=selected':'' }}>FEBRERO</option>
            <option value="03" {{ (isset($mes) and !is_null($mes) and $mes=='3')? 'selected=selected':'' }}>MARZO</option>
            <option value="04" {{ (isset($mes) and !is_null($mes) and $mes=='4')? 'selected=selected':'' }}>ABRIL</option>
            <option value="05" {{ (isset($mes) and !is_null($mes) and $mes=='5')? 'selected=selected':'' }}>MAYO</option>
            <option value="06" {{ (isset($mes) and !is_null($mes) and $mes=='6')? 'selected=selected':'' }}>JUNIO</option>
            <option value="07" {{ (isset($mes) and !is_null($mes) and $mes=='7')? 'selected=selected':'' }}>JULIO</option>
            <option value="08" {{ (isset($mes) and !is_null($mes) and $mes=='8')? 'selected=selected':'' }}>AGOSTO</option>
            <option value="09" {{ (isset($mes) and !is_null($mes) and $mes=='9')? 'selected=selected':'' }}>SEPTIEMBRE</option>
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
            <option value="{{$year}}" {{(isset($yearSeleccionado) and !is_null($yearSeleccionado) and $yearSeleccionado==$year)? 'selected=selected':''}} >{{$year}}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="text-right">
      <button id="exportarSubmit" class="ml-auto btn btn-light col-2" style="    background-color: #ccc;">Exportar</button>
      <button id="filtrarSubmit" class="ml-auto btn btn-primary col-2">Filtrar</button>
      <input hidden id="exportar" name="exportar" value="">
    </div>
  </form>
</div>

<div class="container-report">
  <div class="item-report  col-3" style="height: auto;">
    <div>
      <canvas id="myChart4" height="300"></canvas>
    </div>
  </div>
  <div class="item-report  col-3" style="height: auto;">
    <div>
      <canvas id="myChart1" height="300"></canvas>
    </div>
  </div>
  <div class="item-report  col-3" style="height: auto;">
    <div>
      <canvas id="myChart2" height="300"></canvas>
    </div>
  </div>
  <div class="item-report  col-3" style="height: auto;">
    <div>
      <canvas id="myChart3" height="300"></canvas>
    </div>
  </div>
</div>

<div class="container-report">
  <div class="item-report  col-12" style="height: auto;">
    <h5 class="header-report">Detalle tiempos OT por Diseñador Externo</h5>
    <br>
    <table class="table">
      @if(count($indicador_result) == 0)
        <thead>
          <tr>
            <th colspan="5"><center>No se encontraron ots para el mes seleccionado</center></th>
          </tr>
        </thead>
      @else
        <thead>
          <tr>
            <th scope="col"><center>#</center></th>
            <th scope="col"><center>OT</center></th>
            <th scope="col"><center>Diseñador CMPC</center></th>
            <th scope="col"><center>Fecha Ingreso Diseño Grafico</center></th>
            <th scope="col"><center>Diseñador Externo</center></th>
            <th scope="col"><center>Fecha Envio Diseñador Externo</center></th>
            <th scope="col"><center>Fecha Entrega Diseñador Externo</center></th>
            <th scope="col"><center>Fecha Diseño a Precatalogar</center></th>
            <th scope="col"><center>Tiempo respuesta Diseñador Externo</center></th>
            <th scope="col"><center>Tiempo respuesta Diseñador Grafico</center></th>
          </tr>
        </thead>
        <tbody>
          @php
            $num=1;
          @endphp
          @foreach($indicador_result as $indicador)
            @php
              $item=array();
              $item= explode('*',$indicador);
            @endphp
            <tr>
              <td align="center">{{$num}}</td>
              <td align="center"><a href="{{route('gestionarOt', $item[0])}}" target="_blank">{{$item[0]}}</a></td>
              <td align="center">{{$item[1]}}</td>
              <td align="center">{{$item[2]}}</td>
              <td align="center">{{$item[3]}}</td>
              <td align="center">{{$item[4]}}</td>
              <td align="center">{{$item[5]}}</td>
              <td align="center">{{$item[6]}}</td>
              <td align="center">{{str_replace(".", ",", round($item[7], 2))}}</td>
              <td align="center">{{str_replace(".", ",", round($item[8], 2))}}</td>
            </tr>
            @php
              $num++;
            @endphp
          @endforeach
        </tbody>
      @endif
    </table>
  </div>
</div>

@endsection

@section('myjsfile')

<!-- chartjs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

<script src="{{ asset('js/reports2.js') }}"></script>

<script>
  $(document).ready(function() {

    $(document).on('click', '#exportarSubmit', function(e) {
      e.preventDefault();
      document.getElementById('exportar').value = "Si";
      $('#filtroReporte').submit();
    });
    $(document).on('click', '#filtrarSubmit', function(e) {
      e.preventDefault();
      document.getElementById('exportar').value = "";
      $('#filtroReporte').submit();
    });

  });

  var cantidad_envio_prinflex = @json($cantidad_envio_prinflex);
  var cantidad_envio_graphicbox = @json($cantidad_envio_graphicbox);
  var cantidad_envio_flexoclean = @json($cantidad_envio_flexoclean);
  var cantidad_envio_artfactory = @json($cantidad_envio_artfactory);

  var cantidad_enviadas_prinflex = @json($cantidad_enviadas_prinflex);
  var cantidad_enviadas_graphicbox = @json($cantidad_enviadas_graphicbox);
  var cantidad_enviadas_flexoclean = @json($cantidad_enviadas_flexoclean);
  var cantidad_enviadas_artfactory = @json($cantidad_enviadas_artfactory);

  var cantidad_pendiente_prinflex = @json($cantidad_pendiente_prinflex);
  var cantidad_pendiente_graphicbox = @json($cantidad_pendiente_graphicbox);
  var cantidad_pendiente_flexoclean = @json($cantidad_pendiente_flexoclean);
  var cantidad_pendiente_artfactory = @json($cantidad_pendiente_artfactory);

  var prom_tiempo_duracion_prinflex = @json($prom_tiempo_duracion_prinflex);
  var prom_tiempo_duracion_graphicbox = @json($prom_tiempo_duracion_graphicbox);
  var prom_tiempo_duracion_flexoclean = @json($prom_tiempo_duracion_flexoclean);
  var prom_tiempo_duracion_artfactory = @json($prom_tiempo_duracion_artfactory);

  var cantidad_recepcion_prinflex   = @json($cantidad_recepcion_prinflex);
  var cantidad_recepcion_graphicbox = @json($cantidad_recepcion_graphicbox);
  var cantidad_recepcion_flexoclean = @json($cantidad_recepcion_flexoclean);
  var cantidad_recepcion_artfactory = @json($cantidad_recepcion_artfactory);


  generar_reporte_cantidad_enviadas_diseno( cantidad_enviadas_prinflex,cantidad_enviadas_graphicbox,
                                            cantidad_enviadas_flexoclean,cantidad_enviadas_artfactory);

  generar_reporte_cantidad_ot_pendiente_diseno( cantidad_pendiente_prinflex,cantidad_pendiente_graphicbox,
                                                cantidad_pendiente_flexoclean,cantidad_pendiente_artfactory);

  generar_reporte_cantidad_ot_tiempo_diseno(prom_tiempo_duracion_prinflex,prom_tiempo_duracion_graphicbox,
                                            prom_tiempo_duracion_flexoclean,prom_tiempo_duracion_artfactory);

  generar_reporte_cantidad_ot_recepcion_diseno( cantidad_recepcion_prinflex,cantidad_recepcion_graphicbox,
                                                cantidad_recepcion_flexoclean,cantidad_recepcion_artfactory);

</script>

@endsection
