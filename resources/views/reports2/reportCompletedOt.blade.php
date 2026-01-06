@extends('layouts.index')

@section('content')
<div class="container-fluid">
  <h1 class="page-title">Ratio Conversión de OT Por Meses
    <a href="{{ route('reportCompletedOtEntreFechas') }}" class="btn btn-primary rounded-pill ml-3 px-5">Ir a Entre Fechas</a>
  </h1>
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
</style>

<div class="container-fluid">
  <form id="filtroReporte" class="filter-form py-1" action="{{ route('reportCompletedOtNew') }}" method="get" enctype="multipart/form-data">
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
            @foreach($years as $year)
            <option value="{{$year}}">{{$year}}</option>
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
          <label>Tipo Vendedor</label>
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
  <div class="item-report item-report_sm col-6" style="height: auto;">
    <canvas id="myChart" height="150"></canvas>
  </div>
  <div class="item-report item-report_sm col-6" style="height: auto;">
    <canvas id="myChart2" height="150"></canvas>
  </div>
</div>
<br><br>
<h5 class="header-report">TOP RATIOS DE CONVERSIÓN</h5>
<div class="container-reporte1">
  <div class="item-reporte1 ">
    <h5 class="header-report">Vendedores Mayor Ratio</h5>
    @if(count($creadoresPositivos) == 0)
    <div class="text-center py-2">No se encontraron solicitudes entre rango de fechas</div>
    @else
    <div class="container-solicitud" style="margin-bottom: 10px;margin-top: 10px;padding: 0px 5px;">
      <div class="ot">
        <p style="    text-transform: uppercase;
    font-weight: 900;">Nombre Vendedor</p>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Solicitudes Creadas" data-toggle="tooltip">C</div>
      </div>
      <div class="dias" style="background-color:#a0f09d ;color:black">
        <div class="text-center" title="Solicitudes Terminadas" data-toggle="tooltip">T</div>
      </div>
      <div class="dias">
        <div class="text-center" title="Ratio de Conversión" data-toggle="tooltip">%</div>
      </div>

    </div>
    @endif
    @foreach($creadoresPositivos as $vendedor)
    <div class="container-solicitud" style="padding: 0px 5px;">
      <div class="ot">
        <p>{{$vendedor->fullName}}</p>
      </div>
      <div class="columna-report" style="border-left: 1px solid black;">
        <div class="text-center" title="Solicitudes Creadas" data-toggle="tooltip">{{$vendedor->desarrollosCreados}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Solicitudes Terminadas" data-toggle="tooltip">{{$vendedor->desarrollosTerminados}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Ratio de Conversión" data-toggle="tooltip">{{$vendedor->ratio_conversion}}%</div>
      </div>

    </div>
    <div class="division"></div>
    @endforeach
    <br>
    <!-- MENOR RATIO -->
    <h5 class="header-report">Vendedores Menor Ratio</h5>
    @if(count($creadoresNegativos) == 0)
    <div class="text-center py-2">No se encontraron solicitudes entre rango de fechas</div>
    @else
    <div class="container-solicitud" style="margin-bottom: 10px;margin-top: 10px;padding: 0px 5px;">

      <div class="ot">
        <p style="    text-transform: uppercase;
    font-weight: 900;">Nombre Vendedor</p>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Solicitudes Creadas" data-toggle="tooltip">C</div>
      </div>
      <div class="dias" style="background-color:#a0f09d ;color:black">
        <div class="text-center" title="Solicitudes Terminadas" data-toggle="tooltip">T</div>
      </div>
      <div class="dias">
        <div class="text-center" title="Ratio de Conversión" data-toggle="tooltip">%</div>
      </div>
    </div>
    @endif
    @foreach($creadoresNegativos as $vendedor)
    <div class="container-solicitud" style="padding: 0px 5px;">
      <div class="ot">
        <p>{{$vendedor->fullName}}</p>
      </div>
      <div class="columna-report" style="border-left: 1px solid black;">
        <div class="text-center" title="Solicitudes Creadas" data-toggle="tooltip">{{$vendedor->desarrollosCreados}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Solicitudes Terminadas" data-toggle="tooltip">{{$vendedor->desarrollosTerminados}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Ratio de Conversión" data-toggle="tooltip">{{$vendedor->ratio_conversion}}%</div>
      </div>
    </div>
    <div class="division"></div>
    @endforeach
  </div>
  <div class="item-reporte1 ">
    <h5 class="header-report">Clientes Mayor Ratio</h5>
    @if(count($clientesMayorRatio) == 0)
    <div class="text-center py-2">No se encontraron solicitudes entre rango de fechas</div>
    @else
    <div class="container-solicitud" style="margin-bottom: 10px;margin-top: 10px;">
      <div class="ot">
        <p style="    text-transform: uppercase;
    font-weight: 900;">Nombre Cliente</p>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Solicitudes Creadas" data-toggle="tooltip">C</div>
      </div>
      <div class="dias" style="background-color:#a0f09d ;color:black">
        <div class="text-center" title="Solicitudes Terminadas" data-toggle="tooltip">T</div>
      </div>
      <div class="dias">
        <div class="text-center" title="Ratio de Conversión" data-toggle="tooltip">%</div>
      </div>
    </div>
    @endif
    @foreach($clientesMayorRatio as $cliente)
    <div class="container-solicitud">
      <div class="ot">
        <p>{{$cliente->nombre}}</p>
      </div>
      <div class="columna-report" style="border-left: 1px solid black;">
        <div class="text-center" title="Solicitudes Creadas" data-toggle="tooltip">{{$cliente->desarrollosCreados}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Solicitudes Terminadas" data-toggle="tooltip">{{$cliente->desarrollosTerminados}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Ratio de Conversión" data-toggle="tooltip">{{$cliente->ratio_conversion}}%</div>
      </div>
    </div>
    <div class="division"></div>
    @endforeach


  </div>

  <div class="item-reporte1 ">
    <h5 class="header-report">Clientes Mayor Solicitudes</h5>
    @if(count($clientesMayorDesarrollos) == 0)
    <div class="text-center py-2">No se encontraron solicitudes entre rango de fechas</div>
    @else
    <div class="container-solicitud" style="margin-bottom: 10px;margin-top: 10px;">
      <div class="ot">
        <p style="    text-transform: uppercase;
    font-weight: 900;">Nombre Cliente</p>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Solicitudes Creadas" data-toggle="tooltip">C</div>
      </div>
      <div class="dias" style="background-color:#a0f09d ;color:black">
        <div class="text-center" title="Solicitudes Terminadas" data-toggle="tooltip">T</div>
      </div>
      <div class="dias">
        <div class="text-center" title="Ratio de Conversión" data-toggle="tooltip">%</div>
      </div>
    </div>
    @endif
    @foreach($clientesMayorDesarrollos as $cliente)
    <div class="container-solicitud">
      <div class="ot">
        <p>{{$cliente->nombre}}</p>
      </div>
      <div class="columna-report" style="border-left: 1px solid black;">
        <div class="text-center" title="Solicitudes Creadas" data-toggle="tooltip">{{$cliente->desarrollosCreados}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Solicitudes Terminadas" data-toggle="tooltip">{{$cliente->desarrollosTerminados}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Ratio de Conversión" data-toggle="tooltip">{{$cliente->ratio_conversion}}%</div>
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
<script src="{{ asset('js/reports2.js') }}"></script>

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
    var desarrolloCompletoSolicitudesTotalesUltimosMeses = @json($desarrolloCompletoSolicitudesTotalesUltimosMeses);
    var desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje = @json($desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje);

    var artesCreadosUltimosMeses = @json($artesCreadosUltimosMeses);
    var artesTerminadosUltimosMeses = @json($artesTerminadosUltimosMeses);
    var artesTerminadosUltimosMesesPorcentaje = @json($artesTerminadosUltimosMesesPorcentaje);

    var totalMaterialesCreadosUltimosMeses = @json($totalMaterialesCreadosUltimosMeses);
    var totalMaterialesCreadosUltimosMesesPorcentaje = @json($totalMaterialesCreadosUltimosMesesPorcentaje);

    // contruir reportes:
    // generar reporte por cantidad:
    generar_reporte_ots_completadas(mesesSeleccionados, solicitudesTotalesUltimosMeses, desarrolloCompletoSolicitudesTotalesUltimosMeses, desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje, artesCreadosUltimosMeses, artesTerminadosUltimosMeses, artesTerminadosUltimosMesesPorcentaje,totalMaterialesCreadosUltimosMeses, totalMaterialesCreadosUltimosMesesPorcentaje);
  });
</script>
@endsection
