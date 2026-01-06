@extends('layouts.index')

@section('content')
<div class="container-fluid">
  <h1 class="page-title">Tiempos de Área de OT por Mes</h1>
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

  .item-reporte1 {
    width: 40%;
    margin: 3px;
    z-index: 1;
  }
</style>

<div class="container-fluid">
  <form id="filtroReporte" class="filter-form py-1" action="{{ route('reportTimeByAreaOtMonth') }}" method="get" enctype="multipart/form-data">
    <div class="form-row">
      <div class="col-2">
        <div class="form-group">
          <label>Mes</label>
          <select name="mes" id="mes" class="selectpicker form-control form-control-sm" data-live-search="false">
            <option value="1" {{ (isset($mes) and !is_null($mes) and $mes=='01')? 'selected=selected':'' }}>ENERO</option>
            <option value="2" {{ (isset($mes) and !is_null($mes) and $mes=='02')? 'selected=selected':'' }}>FEBRERO</option>
            <option value="3" {{ (isset($mes) and !is_null($mes) and $mes=='03')? 'selected=selected':'' }}>MARZO</option>
            <option value="4" {{ (isset($mes) and !is_null($mes) and $mes=='04')? 'selected=selected':'' }}>ABRIL</option>
            <option value="5" {{ (isset($mes) and !is_null($mes) and $mes=='05')? 'selected=selected':'' }}>MAYO</option>
            <option value="6" {{ (isset($mes) and !is_null($mes) and $mes=='06')? 'selected=selected':'' }}>JUNIO</option>
            <option value="7" {{ (isset($mes) and !is_null($mes) and $mes=='07')? 'selected=selected':'' }}>JULIO</option>
            <option value="8" {{ (isset($mes) and !is_null($mes) and $mes=='08')? 'selected=selected':'' }}>AGOSTO</option>
            <option value="9" {{ (isset($mes) and !is_null($mes) and $mes=='09')? 'selected=selected':'' }}>SEPTIEMBRE</option>
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
      <div class="col-2">
        <div class="form-group">
          <label>Creador</label>
          <select name="vendedor_id[]" id="vendedor_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
            {!! optionsSelectObjetfilterMultiple($vendedores,'vendedor_id',['nombre','apellido'],' ') !!}
          </select>
          <input id="user_id" type="text" hidden value="{{(is_null(app('request')->input('vendedor_id')))? auth()->user()->id : null}}">
        </div>
      </div>
      <!-- <div class="col-2">
        <div class="form-group">
          <label>Cliente</label>
          <select name="client_id[]" id="client_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
            {!! optionsSelectObjetfilterMultiple($clients,'client_id',['nombre','apellido'],' ') !!}
          </select>
        </div>
      </div> -->
    </div>
    <div class="text-right">
      <button id="exportarSubmit" class="ml-auto btn btn-light col-2" style="    background-color: #ccc;">Exportar</button>
      <button id="filtrarSubmit" class="ml-auto btn btn-primary col-2">Filtrar</button>
      <!-- <button id="filtrarSubmit" class="sbtn submit">Buscar</button> -->
      <!-- este inpurt preserva el valor para poder exportar -->
      <input hidden id="exportar" name="exportar" value="">
    </div>
  </form>
</div>


{{-- graficos por dias: --}}
<div class="container-report">
  <div class="item-report item-report_sm col-3" style="height: auto;">
    <canvas id="myChart3" height="300"></canvas>
  </div>
  <div class="item-report item-report_sm col-9" style="height: auto;">
    <canvas id="myChart4" height="100"></canvas>
  </div>
</div>

{{-- graficos por cantidad: --}}
<div class="container-report">
  <div class="item-report  col-3" style="height: auto;">
    <canvas id="myChart" height="300"></canvas>
  </div>
  <div class="item-reporte1 ">
    <h5 class="header-report">Vendedores</h5>
    @if(count($creadores) == 0)
    <div class="text-center py-2">No se encontraron ots para el mes seleccionado</div>
    @else
    <div class="container-solicitud" style="margin-bottom: 10px;margin-top: 10px;">
      <div class="ot">
        <p style="    text-transform: uppercase;
    font-weight: 900;">Nombre</p>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Cantidad Órdenes de Trabajo" data-toggle="tooltip">OT</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Tiempo Total" data-toggle="tooltip">TT</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Tiempo Promedio" data-toggle="tooltip">TP</div>
      </div>
    </div>
    @endif
    @foreach($creadores as $responsable)
    <div class="container-solicitud">
      <div class="ot">
        <p>{{$responsable->fullname}}</p>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Cantidad Órdenes de Trabajo" data-toggle="tooltip">{{$responsable->total_ots}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Tiempo Total" data-toggle="tooltip">{{ str_replace(".",",",round($responsable->tiempo_total,1)) }} D</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Tiempo Promedio" data-toggle="tooltip">{{ str_replace(".", ",", round($responsable->tiempo_promedio, 1)) }} D</div>
      </div>
    </div>
    <div class="division"></div>
    @endforeach
  </div>
  <div class="item-reporte1 ">
    <h5 class="header-report">Clientes</h5>
    @if(count($clientes) == 0)
    <div class="text-center py-2">No se encontraron ots para el mes seleccionado</div>
    @else
    <div class="container-solicitud" style="margin-bottom: 10px;margin-top: 10px;">
      <div class="ot">
        <p style="    text-transform: uppercase;
    font-weight: 900;">Nombre</p>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Cantidad Órdenes de Trabajo" data-toggle="tooltip">OT</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Tiempo Total" data-toggle="tooltip">TT</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Tiempo Promedio" data-toggle="tooltip">TP</div>
      </div>
    </div>
    @endif
    @foreach($clientes as $responsable)
    <div class="container-solicitud">
      <div class="ot">
        <p>{{$responsable->nombre}}</p>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Cantidad Órdenes de Trabajo" data-toggle="tooltip">{{$responsable->total_ots}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Tiempo Total" data-toggle="tooltip">{{ str_replace(".",",",round($responsable->tiempo_total,1)) }} D</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Tiempo Promedio" data-toggle="tooltip">{{ str_replace(".", ",", round($responsable->tiempo_promedio, 1)) }} D</div>
      </div>
    </div>
    <div class="division"></div>
    @endforeach
  </div>
</div>


@endsection
@section('myjsfile')
<!-- chartjs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<!-- gauge js -->
<script src="{{ asset('js/gauge.min.js') }}"></script>
<script src="{{ asset('js/reports.js') }}"></script>

<script>
  $(document).ready(function() {
    // Funcionabilidad para filtrar o exportar
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


    var mesesSeleccionados = @json($nombreMesesSeleccionados);

    // Cantidad
    var solicitudesTotalesUltimosMeses = @json($solicitudesTotalesUltimosMeses);
    // DIAS
    var diasPorSolicitudUltimosMeses = @json($diasPorSolicitudUltimosMeses);
    diasPorSolicitudUltimosMeses = diasPorSolicitudUltimosMeses.map(function(each_element) {
      return Number(each_element.toFixed(1));
    });
    var ventaPromedioDiasUltimosMeses = @json($ventaPromedioDiasUltimosMeses);
    var desarrolloPromedioDiasTotalesUltimosMeses = @json($desarrolloPromedioDiasTotalesUltimosMeses);
    var muestrasPromedioDiasTotalesUltimosMeses = @json($muestrasPromedioDiasTotalesUltimosMeses);
    var diseñoPromedioDiasTotalesUltimosMeses = @json($diseñoPromedioDiasTotalesUltimosMeses);
    var catalogacionPromedioDiasTotalesUltimosMeses = @json($catalogacionPromedioDiasTotalesUltimosMeses);
    var precatalogacionPromedioDiasTotalesUltimosMeses = @json($precatalogacionPromedioDiasTotalesUltimosMeses);
    // contruir reportes:
    // generar reporte por dias:
    generar_reporte_tiempos_por_area_ot_por_mesDias(mesesSeleccionados, diasPorSolicitudUltimosMeses, ventaPromedioDiasUltimosMeses, desarrolloPromedioDiasTotalesUltimosMeses, muestrasPromedioDiasTotalesUltimosMeses, diseñoPromedioDiasTotalesUltimosMeses, catalogacionPromedioDiasTotalesUltimosMeses, precatalogacionPromedioDiasTotalesUltimosMeses);
    // generar reporte por cantidad:
    generar_reporte_tiempos_por_area_ot_por_mesCantidad(mesesSeleccionados, solicitudesTotalesUltimosMeses);
  });
</script>
@endsection