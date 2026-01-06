@extends('layouts.index')

@section('content')
<div class="container-fluid">
  <h1 class="page-title">Sala de Muestras&nbsp;<div class="text-right">
      <button id="documentoSubmit" onclick="downloadFile()" title="Documento informativo"class="ml-auto btn btn-light" style="background-color: #ccc;">
        <i class="fas fa-info-circle"></i>
      </button>
      <script>
        function downloadFile() {
          const link = document.createElement('a');
          link.href = '/docs/Info_Reporte_Sala_de_Muestra.pdf'; // Replace with the actual file path
          link.download = 'Info_Reporte_Sala_de_Muestra.pdf'; // Replace with the desired file name
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        }
      </script>
      
    </div></h1>
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
  <form id="filtroReporte" class="filter-form py-1" action="{{ route('reportSalaMuestra') }}" method="get" enctype="multipart/form-data">
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

{{-- Segunda fila de graficos --}}

<div class="container-report-various">
  <div class="item-report item-report_sm col-12" style="height: auto;">
    <div class="row">
      <div class="col-4">
        <h5 class="header-report">OT CON MUESTRAS PENDIENTES DE CORTE</h5>
        <label class="container-number" style="color:#28a745;font-size:50px">
          {{$ot_con_muestras_pendientes_corte}}
        </label>
      </div>
      <div class="col-4">
        <h5 class="header-report">ID PENDIENTES DE CORTE</h5>
        <label class="container-number" style="color:#28a745;font-size:50px">
        {{$id_muestras_pendientes_corte}}
        </label>
      </div>
      <div class="col-4">
        <h5 class="header-report">MUESTRAS PENDIENTES DE CORTE</h5>
        <label class="container-number" style="color:#28a745;font-size:50px">
          {{$muestras_pendientes_corte}}
        </label>
      </div>
    </div>    
  </div>
</div>
<div class="container-report-various">
  <div class="item-report item-report_sm col-12" style="height: auto;">
    <div class="row">
      <div class="col-4">
        <h5 class="header-report">OT CON MUESTRAS PENDIENTES DE ENTREGA</h5>
        <label class="container-number" style="color:#28a745;font-size:50px">
          {{$ot_con_muestras_pendientes_entrega}}
        </label>
      </div>
      <div class="col-4">
        <h5 class="header-report">ID PENDIENTES DE ENTREGA</h5>
        <label class="container-number" style="color:#28a745;font-size:50px">
        {{$id_muestras_pendientes_entrega}}
        </label>
      </div>
      <div class="col-4">
        <h5 class="header-report">MUESTRAS PENDIENTES DE ENTREGA</h5>
        <label class="container-number" style="color:#28a745;font-size:50px">
          {{$muestras_pendientes_entrega}}
        </label>
      </div>
    </div>    
  </div>
</div>

<div class="container-report-various">
  <div class="item-report item-report_sm col-12" style="height: auto;">
    <div class="row">
      <div class="col-4">
        <canvas id="myChart1" height="200"></canvas>
      </div>
      <div class="col-4">
        <canvas id="myChart2" height="200"></canvas>
      </div>
      <div class="col-4">
        <canvas id="myChart3" height="200"></canvas>
      </div>
    </div>
  </div> 
</div>

{{-- Tercera fila de graficos --}}
<div class="container-report-various">
  <div class="item-report item-report_sm col-12" style="height: auto;">
    <div class="row">
      <div class="col-4">    
        <canvas id="myChart5" height="300"></canvas>
      </div>
      <div class="col-4">
        <canvas id="myChart6" height="300"></canvas>
      </div>
      <div class="col-4">
        <canvas id="myChart7" height="300"></canvas>
      </div>
    </div>
  </div>
</div>

@endsection
@section('myjsfile')

<!-- chartjs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

<!-- gauge js -->
<!--<script src="{{-- asset('js/gauge.min.js') --}}"></script>-->

<script src="{{ asset('js/reports.js') }}"></script>

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

   //Promedios de tiempos de OT Año anterior ( GRAFICA -> Tiempos OT )
    var promedio_ot_con_muestras_cortadas_anio_anterior_titulo = @json($promedio_ot_con_muestras_cortadas_anio_anterior_titulo);
    var promedio_ot_con_muestras_cortadas_anio_anterior = @json($promedio_ot_con_muestras_cortadas_anio_anterior);
    var ot_con_muestras_cortadas_mes_actual_anio_anterior_titulo = @json($ot_con_muestras_cortadas_mes_actual_anio_anterior_titulo);
    var ot_con_muestras_cortadas_mes_actual_anio_anterior_cantidad = @json($ot_con_muestras_cortadas_mes_actual_anio_anterior_cantidad);
    var ot_con_muestras_cortadas_mes_anio_actual_titulo = @json($ot_con_muestras_cortadas_mes_anio_actual_titulo);
    var ot_con_muestras_cortadas_mes_anio_actual_cantidad = @json($ot_con_muestras_cortadas_mes_anio_actual_cantidad);
    var promedio_ot_con_muestras_cortadas_anio_actual_titulo = @json($promedio_ot_con_muestras_cortadas_anio_actual_titulo);
    var promedio_ot_con_muestras_cortadas_anio_actual = @json($promedio_ot_con_muestras_cortadas_anio_actual);
    var promedio_id_con_muestras_cortadas_anio_anterior_titulo = @json($promedio_id_con_muestras_cortadas_anio_anterior_titulo);
    var promedio_id_con_muestras_cortadas_anio_anterior = @json($promedio_id_con_muestras_cortadas_anio_anterior);
    var id_con_muestras_cortadas_mes_actual_anio_anterior_titulo = @json($id_con_muestras_cortadas_mes_actual_anio_anterior_titulo);
    var id_con_muestras_cortadas_mes_actual_anio_anterior_cantidad = @json($id_con_muestras_cortadas_mes_actual_anio_anterior_cantidad);
    var id_con_muestras_cortadas_mes_anio_actual_titulo = @json($id_con_muestras_cortadas_mes_anio_actual_titulo);
    var id_con_muestras_cortadas_mes_anio_actual_cantidad = @json($id_con_muestras_cortadas_mes_anio_actual_cantidad);
    var promedio_id_con_muestras_cortadas_anio_actual_titulo = @json($promedio_id_con_muestras_cortadas_anio_actual_titulo);
    var promedio_id_con_muestras_cortadas_anio_actual = @json($promedio_id_con_muestras_cortadas_anio_actual);
    var promedio_muestras_cortadas_anio_anterior_titulo = @json($promedio_muestras_cortadas_anio_anterior_titulo);
    var promedio_muestras_cortadas_anio_anterior = @json($promedio_muestras_cortadas_anio_anterior);
    var muestras_cortadas_mes_actual_anio_anterior_titulo = @json($muestras_cortadas_mes_actual_anio_anterior_titulo);
    var muestras_cortadas_mes_actual_anio_anterior_cantidad = @json($muestras_cortadas_mes_actual_anio_anterior_cantidad);
    var muestras_cortadas_mes_anio_actual_titulo = @json($muestras_cortadas_mes_anio_actual_titulo);
    var muestras_cortadas_mes_anio_actual_cantidad = @json($muestras_cortadas_mes_anio_actual_cantidad);
    var promedio_muestras_cortadas_anio_actual_titulo = @json($promedio_muestras_cortadas_anio_actual_titulo);
    var promedio_muestras_cortadas_anio_actual = @json($promedio_muestras_cortadas_anio_actual);


    generar_reporte_ot_con_muestras(
      promedio_ot_con_muestras_cortadas_anio_anterior_titulo,
      promedio_ot_con_muestras_cortadas_anio_anterior,
      ot_con_muestras_cortadas_mes_actual_anio_anterior_titulo,
      ot_con_muestras_cortadas_mes_actual_anio_anterior_cantidad,
      ot_con_muestras_cortadas_mes_anio_actual_titulo,
      ot_con_muestras_cortadas_mes_anio_actual_cantidad,
      promedio_ot_con_muestras_cortadas_anio_actual_titulo,
      promedio_ot_con_muestras_cortadas_anio_actual,
      promedio_id_con_muestras_cortadas_anio_anterior_titulo,
      promedio_id_con_muestras_cortadas_anio_anterior,
      id_con_muestras_cortadas_mes_actual_anio_anterior_titulo,
      id_con_muestras_cortadas_mes_actual_anio_anterior_cantidad,
      id_con_muestras_cortadas_mes_anio_actual_titulo,
      id_con_muestras_cortadas_mes_anio_actual_cantidad,
      promedio_id_con_muestras_cortadas_anio_actual_titulo,
      promedio_id_con_muestras_cortadas_anio_actual,
      promedio_muestras_cortadas_anio_anterior_titulo,
      promedio_muestras_cortadas_anio_anterior,
      muestras_cortadas_mes_actual_anio_anterior_titulo,
      muestras_cortadas_mes_actual_anio_anterior_cantidad,
      muestras_cortadas_mes_anio_actual_titulo,
      muestras_cortadas_mes_anio_actual_cantidad,
      promedio_muestras_cortadas_anio_actual_titulo,
      promedio_muestras_cortadas_anio_actual
    );

    var cantidad_muestras_puente_alto = @json($cantidad_muestras_puente_alto);
    var cantidad_muestras_osorno = @json($cantidad_muestras_osorno);
    var cantidad_muestras_cortadas_puente_alto = @json($cantidad_muestras_cortadas_puente_alto);
    var cantidad_muestras_osorno_cortadas = @json($cantidad_muestras_osorno_cortadas);
    var cantidad_ot_puente_alto = @json($cantidad_ot_puente_alto);
    var cantidad_ot_osorno = @json($cantidad_ot_osorno);
    var cantidad_muestras_puente_alto = @json($cantidad_muestras_puente_alto);
    var cantidad_muestras_osorno = @json($cantidad_muestras_osorno);
    var cantidad_muestras_cortadas_puente_alto = @json($cantidad_muestras_cortadas_puente_alto);
    var cantidad_muestras_cortadas_otro = @json($cantidad_muestras_cortadas_otro);
    var cantidad_ot_otro = @json($cantidad_ot_otro);
    var cantidad_muestras_otro = @json($cantidad_muestras_otro);

    generar_reporte_muestras_osorno_puentealto(cantidad_muestras_puente_alto,cantidad_muestras_osorno,cantidad_muestras_otro);
    generar_reporte_ot_osorno_puentealto(cantidad_ot_puente_alto,cantidad_ot_osorno,cantidad_ot_otro);
    generar_reporte_cortadas_osorno_puentealto(cantidad_muestras_cortadas_puente_alto,cantidad_muestras_osorno_cortadas,cantidad_muestras_cortadas_otro);
    
  });
</script>
 
@endsection