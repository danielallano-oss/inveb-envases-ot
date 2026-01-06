@extends('layouts.index')

@section('content')
<div class="container-fluid">
  <h1 class="page-title">Indicador Desarrollo&nbsp;<div class="text-right">
      <button id="documentoSubmit" onclick="downloadFile()" title="Documento informativo"class="ml-auto btn btn-light" style="background-color: #ccc;">
        <i class="fas fa-info-circle"></i>
      </button>
      <script>
        function downloadFile() {
          const link = document.createElement('a');
          link.href = '/docs/Info_Reporte_Indicador_Desarrollo.pdf'; // Replace with the actual file path
          link.download = 'Info_Reporte_Indicador_Desarrollo.pdf'; // Replace with the desired file name
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
  <form id="filtroReporte" class="filter-form py-1" action="{{ route('reportDisenoEstructuralySalaMuestra') }}" method="get" enctype="multipart/form-data">
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
            @foreach($years as $yeara)
            <option value="{{$yeara}}" {{ (isset($year) and !is_null($year) and $year==$yeara)? 'selected=selected':'' }}>{{$yeara}}</option>
            @endforeach 
          </select>
        </div>
      </div>
      <div class="col-2">
        <div class="form-group">
          <label>Creador</label>
          <select name="vendedor_id[]" id="vendedor_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
            {!! optionsSelectObjetfilterMultiple($vendedores,'vendedor_id',['nombre','apellido'],' ') !!}
          </select>
          <input id="user_id" type="text" hidden value="{{(is_null(app('request')->input('vendedor_id')))? auth()->user()->id : null}}">
        </div>
      </div>
      <div class="col-2">
        <div class="form-group">
          <label>Área</label>
          <select name="area_id[]" id="area_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
            {!! optionsSelectObjetfilterMultiple($areas,'area_id',['nombre'],' ') !!}
          </select>
        </div>
      </div>
    </div>
    <div class="text-right">
      <button id="filtrarSubmit" class="ml-auto btn btn-primary col-2">Filtrar</button>
    </div>
  </form>
</div>

{{-- Primera fila de graficos --}}
<div class="container-report">
  <div class="item-report item-report_sm col-3" style="height: auto;">
    <canvas id="myChart" height="300"></canvas>
  </div>
  <div class="item-report item-report_sm col-9" style="height: auto;">
    <canvas id="myChart4" height="100"></canvas>
  </div>
  
</div>

{{-- Segunda fila de graficos --}}
<div class="container-report">
  <div class="item-report  col-3" style="height: auto;">
    <canvas id="myChart2" height="300"></canvas>
  </div>
  
  <div class="item-reporte1 ">
    <h5 class="header-report">Dibujante Técnico</h5>
    @if(count($disenador_estructural) == 0)
      <div class="text-center py-2">No se encontraron ots para el mes seleccionado</div>
    @else
      <div class="container-solicitud" style="margin-bottom: 10px;margin-top: 10px;">
        <div class="ot">
          <p style="text-transform: uppercase;font-weight: 900;">Nombre</p>
        </div>
        <div class="dias" style="background-color:#3aaa35;">
          <div class="text-center" title="Cantidad Órdenes de Trabajo" data-toggle="tooltip">OT</div>
        </div>
        <div class="dias" style="background-color:#3aaa35;">
          <div class="text-center" title="Tiempo Total" data-toggle="tooltip">TT</div>
        </div>
        <div class="dias" style="background-color:#3aaa35;">
          <div class="text-center" title="Tiempo Promedio" data-toggle="tooltip">TP</div>
        </div>
      </div>
    @endif
    @foreach($disenador_estructural as $disenador)    
      <div class="container-solicitud">
        <div class="ot">
          <p>{{$disenador->fullname}}</p>
        </div>
        <div class="columna-report">
          <div class="text-center" title="Cantidad Órdenes de Trabajo" data-toggle="tooltip">{{$disenador->total_ots}}</div>
        </div>
        <div class="columna-report">
          <div class="text-center" title="Tiempo Total" data-toggle="tooltip">{{ str_replace(".",",",round($disenador->tiempo_total,1)) }} D</div>
        </div>
        <div class="columna-report">
          <div class="text-center" title="Tiempo Promedio" data-toggle="tooltip">{{ str_replace(".", ",", round($disenador->tiempo_promedio, 1)) }} D</div>
        </div>
      </div>
      <div class="division"></div>
    @endforeach
  </div>

  <div class="item-reporte1 ">
    <h5 class="header-report">Diseñador Gráfico</h5>
    @if(count($disenador_grafico) == 0)
      <div class="text-center py-2">No se encontraron ots para el mes seleccionado</div>
    @else
      <div class="container-solicitud" style="margin-bottom: 10px;margin-top: 10px;">
        <div class="ot">
          <p style="text-transform: uppercase;font-weight: 900;">Nombre</p>
        </div>
        <div class="dias" style="background-color:#3aaa35;">
          <div class="text-center" title="Cantidad Órdenes de Trabajo" data-toggle="tooltip">OT</div>
        </div>
        <div class="dias" style="background-color:#3aaa35;">
          <div class="text-center" title="Tiempo Total" data-toggle="tooltip">TT</div>
        </div>
        <div class="dias" style="background-color:#3aaa35;">
          <div class="text-center" title="Tiempo Promedio" data-toggle="tooltip">TP</div>
        </div>
      </div>
    @endif
    @foreach($disenador_grafico as $disenador)  
      <div class="container-solicitud">
        <div class="ot">
          <p>{{$disenador->fullname}}</p>
        </div>
        <div class="columna-report">
          <div class="text-center" title="Cantidad Órdenes de Trabajo" data-toggle="tooltip">{{$disenador->total_ots}}</div>
        </div>
        <div class="columna-report">
          <div class="text-center" title="Tiempo Total" data-toggle="tooltip">{{ str_replace(".",",",round($disenador->tiempo_total,1)) }} D</div>
        </div>
        <div class="columna-report">
          <div class="text-center" title="Tiempo Promedio" data-toggle="tooltip">{{ str_replace(".", ",", round($disenador->tiempo_promedio, 1)) }} D</div>
        </div>
      </div>
      <div class="division"></div>
    @endforeach
  </div>
{{--
  <div class="item-report  col-3" style="height: auto;">
    <div>
        <h5 class="header-report">OT CON MUESTRAS PENDIENTES DE CORTE</h5>
        <label class="container-number" style="color:#28a745;font-size:50px">
          {{$ot_con_muestras_pendientes_corte}}
        </label>

    </div>
    <div style="margin-top:20px">
        <h5 class="header-report">ID PENDIENTES DE CORTE</h5>
        <label class="container-number" style="color:#28a745;font-size:50px">
         {{$id_muestras_pendientes_corte}}
        </label>

    </div>
    <div style="margin-top:20px">
        <h5 class="header-report">MUESTRAS PENDIENTES DE CORTE</h5>
        <label class="container-number" style="color:#28a745;font-size:50px">
          {{$muestras_pendientes_corte}}
        </label>

    </div>
  </div>
</div>

{{-- Tercera fila de graficos 
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

<div class="container-report-various">
  <div class="item-report item-report_sm col-12" style="height: auto;">
    <div class="row">
      <div class="col-4">
        <canvas id="myChart8" height="300"></canvas>
      </div>
      <div class="col-4">
        <canvas id="myChart9" height="300"></canvas>
      </div>
      <div class="col-4">
        <canvas id="myChart10" height="300"></canvas>
      </div>
    </div>
  </div>
</div>--}}


@endsection
@section('myjsfile')

<!-- chartjs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

<!-- gauge js -->
<!--<script src="{{-- asset('js/gauge.min.js') --}}"></script>-->

<script src="{{ asset('js/reports.js') }}"></script>

<script>
  $(document).ready(function() {

   //Cantidad de OT por Area mes Actual ( GRAFICA -> OT que estan en cada area mes actual )
    var array_cantidad_ot_por_area = @json($array_cantidad_ot_por_area);
    var array_keys_ot_por_area = @json($array_keys_ot_por_area);
  
    //Cantidad historico de OT por Area mes Actual ( GRAFICA -> Nº OT QUE HAN PASADO POR CADA ÁREA del mes actual )
    var array_cantidad_historico_ot_por_area = @json($array_cantidad_historico_ot_por_area);
    var array_keys_historico_ot_por_area = @json($array_keys_historico_ot_por_area);


    //Promedios de tiempos de OT Año anterior ( GRAFICA -> Tiempos OT )
    var promedio_anio_anterior_titulo = @json($promedio_anio_anterior_titulo);
    var promedio_anio_anterior_desarrollo = @json($promedio_anio_anterior_desarrollo);
    var promedio_anio_anterior_muestra = @json($promedio_anio_anterior_muestra);
    var promedio_anio_anterior_diseno = @json($promedio_anio_anterior_diseno);
    var promedio_anio_anterior_catalogacion = @json($promedio_anio_anterior_catalogacion);
    var promedio_anio_anterior_precatalogacion = @json($promedio_anio_anterior_precatalogacion);
    var promedio_mes_actual_anio_anterior_titulo = @json($promedio_mes_actual_anio_anterior_titulo);
    var promedio_mes_actual_anio_anterior_desarrollo = @json($promedio_mes_actual_anio_anterior_desarrollo);
    var promedio_mes_actual_anio_anterior_muestra = @json($promedio_mes_actual_anio_anterior_muestra);
    var promedio_mes_actual_anio_anterior_diseno = @json($promedio_mes_actual_anio_anterior_diseno);
    var promedio_mes_actual_anio_anterior_catalogacion = @json($promedio_mes_actual_anio_anterior_catalogacion);
    var promedio_mes_actual_anio_anterior_precatalogacion = @json($promedio_mes_actual_anio_anterior_precatalogacion);
    var promedio_mes_anterior_al_actual_titulo = @json($promedio_mes_anterior_al_actual_titulo);
    var promedio_mes_anterior_al_actual_desarrollo = @json($promedio_mes_anterior_al_actual_desarrollo);
    var promedio_mes_anterior_al_actual_muestra = @json($promedio_mes_anterior_al_actual_muestra);
    var promedio_mes_anterior_al_actual_diseno = @json($promedio_mes_anterior_al_actual_diseno);
    var promedio_mes_anterior_al_actual_catalogacion = @json($promedio_mes_anterior_al_actual_catalogacion);
    var promedio_mes_anterior_al_actual_precatalogacion = @json($promedio_mes_anterior_al_actual_precatalogacion);
    var promedio_mes_actual_titulo = @json($promedio_mes_actual_titulo);
    var promedio_mes_actual_desarrollo = @json($promedio_mes_actual_desarrollo);
    var promedio_mes_actual_muestra = @json($promedio_mes_actual_muestra);
    var promedio_mes_actual_diseno = @json($promedio_mes_actual_diseno);
    var promedio_mes_actual_catalogacion = @json($promedio_mes_actual_catalogacion);
    var promedio_mes_actual_precatalogacion = @json($promedio_mes_actual_precatalogacion);
    var promedio_anio_actual_titulo = @json($promedio_anio_actual_titulo);
    var promedio_anio_actual_desarrollo = @json($promedio_anio_actual_desarrollo);
    var promedio_anio_actual_muestra = @json($promedio_anio_actual_muestra);
    var promedio_anio_actual_diseno = @json($promedio_anio_actual_diseno);
    var promedio_anio_actual_catalogacion = @json($promedio_anio_actual_catalogacion);
    var promedio_anio_actual_precatalogacion = @json($promedio_anio_actual_precatalogacion);

    //------------******* REPORTES ******----------------
        
    // generar reporte por cantidad:
    generar_reporte_cantidad_ot_por_area_mes_actual(array_cantidad_ot_por_area, array_keys_ot_por_area);

    
    
    // generar reporte por cantidad historico area:
    generar_reporte_cantidad_historico_ot_por_area_mes_actual(array_cantidad_historico_ot_por_area, array_keys_historico_ot_por_area);

    //Generar reporte tiempos de OT Año:
    generar_reporte_tiempos_ot(
        promedio_anio_anterior_titulo,
        promedio_anio_anterior_desarrollo,
        promedio_anio_anterior_muestra,
        promedio_anio_anterior_diseno,
        promedio_anio_anterior_catalogacion,
        promedio_anio_anterior_precatalogacion,
        promedio_mes_actual_anio_anterior_titulo,
        promedio_mes_actual_anio_anterior_desarrollo,
        promedio_mes_actual_anio_anterior_muestra,
        promedio_mes_actual_anio_anterior_diseno,
        promedio_mes_actual_anio_anterior_catalogacion,
        promedio_mes_actual_anio_anterior_precatalogacion,
        promedio_mes_anterior_al_actual_titulo,
        promedio_mes_anterior_al_actual_desarrollo,
        promedio_mes_anterior_al_actual_muestra,
        promedio_mes_anterior_al_actual_diseno,
        promedio_mes_anterior_al_actual_catalogacion,
        promedio_mes_anterior_al_actual_precatalogacion,
        promedio_mes_actual_titulo,
        promedio_mes_actual_desarrollo,
        promedio_mes_actual_muestra,
        promedio_mes_actual_diseno,
        promedio_mes_actual_catalogacion,
        promedio_mes_actual_precatalogacion,
        promedio_anio_actual_titulo,
        promedio_anio_actual_desarrollo,
        promedio_anio_actual_muestra,
        promedio_anio_actual_diseno,
        promedio_anio_actual_catalogacion,
        promedio_anio_actual_precatalogacion
      );

  });
</script>
 
@endsection