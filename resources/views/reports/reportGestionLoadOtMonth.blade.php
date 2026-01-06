@extends('layouts.index')

@section('content')
<div class="container-fluid">
  <h1 class="page-title">Gestión de Carga OT por Mes</h1>
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

  .legend {
    list-style: none;
    display: inline-block;
    text-align: center;
    /* padding-top: 10px; */
    /* width: 100%; */
    display: flex;
    justify-content: space-evenly;
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
  <form id="filtroReporte" class="filter-form py-1" action="{{ route('reportGestionLoadOtMonth') }}" method="get" enctype="multipart/form-data">
    <div class="form-row">
      <div class="col-2">
        <div class="form-group">
          <label>Mes</label>
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
            @foreach($years as $yearse)
            <option value="{{$yearse}}" {{(isset($year) and !is_null($year) and $year==$yearse)? 'selected=selected':''}}>{{$yearse}}</option>
            @endforeach
          </select>
        </div>
      </div>
      <!-- <div class="col-2">
        <div class="form-group">
          <label>Estado</label>
          <select name="estado_id[]" id="estado_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
            {!! optionsSelectObjetfilterMultiple($estados,'estado_id',['nombre'],' ') !!}
          </select>
        </div>
      </div> -->
      <div class="col-2">
        <div class="form-group">
          <label>Tipo Vendedor </label>
          <select name="tipo_vendedor" id="tipo_vendedor" class="selectpicker form-control form-control-sm" data-live-search="false">
            <option value="1" {{ (isset($tipo_vendedor) and !is_null($tipo_vendedor) and $mes=='1')? 'selected=selected':'' }}>TODOS</option>
            <option value="4" {{ (isset($tipo_vendedor) and !is_null($tipo_vendedor) and $tipo_vendedor=='4')? 'selected=selected':'' }}>Vendedores</option>
            <option value="19" {{ (isset($tipo_vendedor) and !is_null($tipo_vendedor) and $tipo_vendedor=='19')? 'selected=selected':'' }}>Vendedores Externo</option>
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

{{-- graficos por cantidad: --}}
<div class="container-report">
  <div class="item-report item-report_sm col-4" style="height: auto;">
    <canvas id="myChart" height="200"></canvas>
    <ul class="legend">
      <li><span style="background-color: #44a840;color: white;"></span>Todas las Solicitudes</li>
    </ul>
  </div>
  <div class="item-report item-report_sm col-8" style="height: auto;">
    <canvas id="myChart2" height="100"></canvas>
    <ul class="legend">
      <li data-toggle="tooltip" data-html="true" title="Muestra con CAD: <br> Se requiere muestra de algo existente"><span style="background-color: #14880f;color: white;"></span>Muestra Con CAD</li>
      <li data-toggle="tooltip" data-html="true" title="Arte con Material: <br> Desarrollo que empieza de una caja existente que cambia de arte y termina catalogado "><span style="background-color: #44a840;color: white;"></span>Arte Con Material</li>
      <li data-toggle="tooltip" data-html="true" title="Desarrollo Completo: <br> Se genera el material en base a datos entregados por el cliente"><span style="background-color: #6ad766;color: white;"></span>Desarrollo Completo</li>
      <li data-toggle="tooltip" data-html="true" title="Otras Solicitudes Desarrollo"><span style="background-color: #a0f09d;color: white;"></span>Otras Solicitusdes Desarrollo</li>
      <li data-toggle="tooltip" data-html="true" title="Proyecto de Innovación"><span style="background-color: #c6fac3;color: white;"></span>Proyecto Innovación</li>
    </ul>
  </div>
</div>

{{-- graficos por dias: --}}
<div class=" container-report">
  <div class="item-report item-report_sm col-4" style="height: auto;">
    <canvas id="myChart3" height="200"></canvas>
    <ul class="legend">
      <li><span style="background-color: #14880f;color: white;"></span>Todas las Solicitudes</li>
    </ul>
  </div>
  <div class="item-report item-report_sm col-8" style="height: auto;">
    <canvas id="myChart4" height="100"></canvas>
    <ul class="legend">
      <li data-toggle="tooltip" data-html="true" title="Muestra con CAD: <br> Se requiere muestra de algo existente"><span style="background-color: #14880f;color: white;"></span>Muestra Con CAD</li>
      <li data-toggle="tooltip" data-html="true" title="Arte con Material: <br> Desarrollo que empieza de una caja existente que cambia de arte y termina catalogado "><span style="background-color: #44a840;color: white;"></span>Arte Con Material</li>
      <li data-toggle="tooltip" data-html="true" title="Desarrollo Completo: <br> Se genera el material en base a datos entregados por el cliente"><span style="background-color: #6ad766;color: white;"></span>Desarrollo Completo</li>
      <li data-toggle="tooltip" data-html="true" title="Otras Solicitudes Desarrollo"><span style="background-color: #a0f09d;color: white;"></span>Otras Solicitusdes Desarrollo</li>
      <li data-toggle="tooltip" data-html="true" title="Proyecto de Innovación"><span style="background-color: #c6fac3;color: white;"></span>Proyecto Innovación</li>
    </ul>
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
    var muestraSolicitudesTotalesUltimosMeses = @json($muestraSolicitudesTotalesUltimosMeses);
    var desarrolloCompletoSolicitudesTotalesUltimosMeses = @json($desarrolloCompletoSolicitudesTotalesUltimosMeses);
    var arteSolicitudesTotalesUltimosMeses = @json($arteSolicitudesTotalesUltimosMeses);
    var otrasDesarrolloSolicitudesTotalesUltimosMeses = @json($otrasDesarrolloSolicitudesTotalesUltimosMeses);
    var proyectoInnovacionSolicitudesTotalesUltimosMeses = @json($proyectoInnovacionSolicitudesTotalesUltimosMeses);
    // DIAS
    var diasPorSolicitudUltimosMeses = @json($diasPorSolicitudUltimosMeses);
    diasPorSolicitudUltimosMeses = diasPorSolicitudUltimosMeses.map(function(each_element) {
      return Number(each_element.toFixed(1));
    });
    var muestraPromedioDiasTotalesUltimosMeses = @json($muestraPromedioDiasTotalesUltimosMeses);
    var desarrolloCompletoPromedioDiasTotalesUltimosMeses = @json($desarrolloCompletoPromedioDiasTotalesUltimosMeses);
    var artePromedioDiasTotalesUltimosMeses = @json($artePromedioDiasTotalesUltimosMeses);
    var otrasDesarrolloPromedioDiasTotalesUltimosMeses = @json($otrasDesarrolloPromedioDiasTotalesUltimosMeses);
    var proyectoInnovacionPromedioDiasTotalesUltimosMeses = @json($proyectoInnovacionPromedioDiasTotalesUltimosMeses);
    // contruir reportes:
    // generar reporte por cantidad:
    generar_reporte_gestion_carga_ot_por_mesCantidad(mesesSeleccionados, solicitudesTotalesUltimosMeses, muestraSolicitudesTotalesUltimosMeses, desarrolloCompletoSolicitudesTotalesUltimosMeses, arteSolicitudesTotalesUltimosMeses, otrasDesarrolloSolicitudesTotalesUltimosMeses, proyectoInnovacionSolicitudesTotalesUltimosMeses);
    // generar reporte por dias:
    generar_reporte_gestion_carga_ot_por_mesDias(mesesSeleccionados, diasPorSolicitudUltimosMeses, muestraPromedioDiasTotalesUltimosMeses, desarrolloCompletoPromedioDiasTotalesUltimosMeses, artePromedioDiasTotalesUltimosMeses, otrasDesarrolloPromedioDiasTotalesUltimosMeses, proyectoInnovacionPromedioDiasTotalesUltimosMeses);
  });
</script>
@endsection