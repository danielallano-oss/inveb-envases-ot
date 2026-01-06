@extends('layouts.index')

@section('content')
<div class="container-fluid">
  <h1 class="page-title">Tiempo Primera Muestra</h1>
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
  <form id="filtroReporte" class="filter-form py-1" action="{{ route('reportTiempoPrimeraMuestraNew1') }}" method="get" enctype="multipart/form-data">
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

    <div class="item-report item-report_sm col-12" style="height: auto;">
      <canvas id="myChartAno" height="70"></canvas>
    </div>

</div>

<div class="container-report">
  <div class="item-report  col-5" style="height: auto;">
    <div>
      <canvas id="myChart" height="200"></canvas>
    </div>
  </div>
  <div class="item-report  col-7" style="height: auto;">
    <div>
      <h5 class="header-report">CANTIDAD DE OT CON PRIMERA MUESTRA EN EL PERÍODO</h5>
      <label class="container-number" style="color:#28a745;font-size:50px">
        {{$cantidad_ot}}
      </label>
    </div>
    <div>
      <h5 class="header-report">PROMEDIO DURACIÓN DÍAS DESDE CREACIÓN DE OT</h5>
      <label class="container-number" style="color:#28a745;font-size:50px">
        {{$prom_tiempo_creacion}}
      </label>
    </div>
    <div>
      <h5 class="header-report">PROMEDIO DURACIÓN DÍAS DESDE INGRESO DISEÑO ESTRUCTURAL</h5>
      <label class="container-number" style="color:#28a745;font-size:50px">
        {{$prom_tiempo_DE}}
      </label>
    </div>
  </div>
</div>

<div class="container-report">
  <div class="item-report  col-12" style="height: auto;">
    <h5 class="header-report">Detalle tiempos de OT primera Muestra Lista</h5>
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
            <th scope="col"><center>Fecha/hora  Creación OT</center></th>
            <th scope="col"><center>Fecha/hora  Ingreso Diseño Estructural</center></th>
            <th scope="col"><center>Fecha/hora  Termino Primera Muestra</center></th>
            <th scope="col"><center>Dias desde Creación OT</center></th>
            <th scope="col"><center>Dias desde Ingreso Diseño Estructural</center></th>
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
              <td align="center">{{str_replace(".", ",", round($item[4], 2))}}</td>
              <td align="center">{{str_replace(".", ",", round($item[5], 2))}}</td>
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

  var prom_tiempo_creacion = @json($prom_tiempo_creacion_grafico);
  var prom_tiempo_DE = @json($prom_tiempo_DE_grafico);
  var cantidad_ot = @json($cantidad_ot);
  var prom_tiempo_creacion_ene = @json($prom_tiempo_creacion_ene);
  var prom_tiempo_de_ene = @json($prom_tiempo_de_ene);
  var prom_tiempo_creacion_feb = @json($prom_tiempo_creacion_feb);
  var prom_tiempo_de_feb = @json($prom_tiempo_de_feb);
  var prom_tiempo_creacion_mar = @json($prom_tiempo_creacion_mar);
  var prom_tiempo_de_mar = @json($prom_tiempo_de_mar);
  var prom_tiempo_creacion_abr = @json($prom_tiempo_creacion_abr);
  var prom_tiempo_de_abr = @json($prom_tiempo_de_abr);
  var prom_tiempo_creacion_may = @json($prom_tiempo_creacion_may);
  var prom_tiempo_de_may = @json($prom_tiempo_de_may);
  var prom_tiempo_creacion_jun = @json($prom_tiempo_creacion_jun);
  var prom_tiempo_de_jun = @json($prom_tiempo_de_jun);
  var prom_tiempo_creacion_jul = @json($prom_tiempo_creacion_jul);
  var prom_tiempo_de_jul = @json($prom_tiempo_de_jul);
  var prom_tiempo_creacion_ago = @json($prom_tiempo_creacion_ago);
  var prom_tiempo_de_ago = @json($prom_tiempo_de_ago);
  var prom_tiempo_creacion_sep = @json($prom_tiempo_creacion_sep);
  var prom_tiempo_de_sep = @json($prom_tiempo_de_sep);
  var prom_tiempo_creacion_oct = @json($prom_tiempo_creacion_oct);
  var prom_tiempo_de_oct = @json($prom_tiempo_de_oct);
  var prom_tiempo_creacion_nov = @json($prom_tiempo_creacion_nov);
  var prom_tiempo_de_nov = @json($prom_tiempo_de_nov);
  var prom_tiempo_creacion_dic = @json($prom_tiempo_creacion_dic);
  var prom_tiempo_de_dic = @json($prom_tiempo_de_dic);
  var prom_tiempo_creacion_ano_grafico = @json($prom_tiempo_creacion_ano_grafico);
  var prom_tiempo_DE_ano_grafico = @json($prom_tiempo_DE_ano_grafico);
  var ano_selecionado = @json($yearSeleccionado);

  generar_reporte_tiempo_primera_muestra(prom_tiempo_creacion,prom_tiempo_DE,cantidad_ot);
  generar_reporte_tiempo_primera_muestra_ano( prom_tiempo_creacion_ene,prom_tiempo_de_ene,prom_tiempo_creacion_feb,prom_tiempo_de_feb,
                                              prom_tiempo_creacion_mar,prom_tiempo_de_mar,prom_tiempo_creacion_abr,prom_tiempo_de_abr,
                                              prom_tiempo_creacion_may,prom_tiempo_de_may,prom_tiempo_creacion_jun,prom_tiempo_de_jun,
                                              prom_tiempo_creacion_jul,prom_tiempo_de_jul,prom_tiempo_creacion_ago,prom_tiempo_de_ago,
                                              prom_tiempo_creacion_sep,prom_tiempo_de_sep,prom_tiempo_creacion_oct,prom_tiempo_de_oct,
                                              prom_tiempo_creacion_nov,prom_tiempo_de_nov,prom_tiempo_creacion_dic,prom_tiempo_de_dic,
                                              prom_tiempo_creacion_ano_grafico,prom_tiempo_DE_ano_grafico,ano_selecionado);

</script>

@endsection
