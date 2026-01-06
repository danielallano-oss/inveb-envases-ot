@extends('layouts.index')

@section('content')
<div class="container-fluid">
  <h1 class="page-title">Gestión de OT Activas</h1>
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

  .col-2-5 {
    flex: 0 0 18.7%;
    max-width: 18.7%;
  }

  .table thead th,
  .table td {
    border: 0px #fff solid;
    padding: .01rem;
    color: #686f75;
  }

  .progress-bar {
    line-height: 1;
    font-size: 11px;
  }

  .container-report {
    height: 100%;
  }

  .bg-ventas {
    background-color: #0c7108;
    color: white;
  }

  .bg-ingenieria {
    background-color: #1877d6;
    color: white;
  }

  .bg-diseno {
    background-color: #b030a6;
    color: white;
  }

  .bg-precataloga {
    background-color: #b56316;
    color: black;
  }

  .bg-cataloga {
    background-color: #02b597;
    color: black;
  }

  /* basic positioning */
  .legend {
    list-style: none;
    display: inline-block;
    text-align: center;
    padding-top: 10px;
  }

  .legend li {
    float: left;
    margin-right: 10px;
  }

  .legend span {
    border: 1px solid #fff;
    float: left;
    width: 20px;
    height: 20px;
    margin: 2px;
    border-radius: 20px;
  }

  /* your colors */
  .legend .ventas {
    background-color: #0c7108;
  }

  .legend .ingenieria {
    background-color: #1877d6;
  }

  .legend .diseno {
    background-color: #b030a6;
  }

  .legend .precataloga {
    background-color: #b56316;
  }

  .legend .cataloga {
    background-color: #02b597;
  }


  .horizontal-scroll {
    border-radius: 0px;
    overflow-x: auto;
  }
</style>

<div class="container-fluid">
  <form id="filtroReporte" class="filter-form py-1" action="{{ route('reportGestionsActiveNew') }}" method="get" enctype="multipart/form-data">
    <div class="form-row">
      <div class="col-1">
        <div class="form-group">
          <label>Desde</label>
          <input class="form-control form-control-sm datepicker" id="datePicker1" name="date_desde" value="{{ (is_null(app('request')->input('date_desde')))? null : app('request')->input('date_desde') }}" autocomplete="off">
        </div>
      </div>
      <div class="col-1">
        <div class="form-group">
          <label>Hasta</label>
          <input class="form-control form-control-sm datepicker" id="datePicker2" name="date_hasta" value="{{ (is_null(app('request')->input('date_hasta')))? null : app('request')->input('date_hasta') }}" autocomplete="off">
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
          <label>Canal</label>
          <select name="canal_id[]" id="canal_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
            {!! optionsSelectObjetfilterMultiple($canals,'canal_id',['nombre','apellido'],' ') !!}
          </select>
        </div>
      </div>
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


{{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> --}}{{--
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> --}}


{{-- paneles por tipo de solicitud: --}}
<div class="container-report horizontal-scroll">
  {{-- Ventas: --}}
  <div class="item-report item-report_sm col-2-5" style="height: auto; padding: 3px;border: solid 1px #979393;">
    <div class="container-report" style="background-color: #eee;border-radius: 8px;">
      <div class="container" style="padding-right: 3px;padding-left: 3px;">
        <table border="0" class="table">
          <thead>
            <tr>
              <th class="text-center">Ventas</th>
            </tr>
          </thead>
          <tbody style="font-size: 10px;">
            <tr>
              <td>Solicitudes Totales: <b>{{$solicitudesTotales}}</b></td>
            </tr>
            <tr>
              <td>Porcentaje en Ventas: <b>{{$porcentajeSolicitudesPorArea[1]}}</b></td>
            </tr>
            <tr>
              <td>Solicitudes en Ventas: <b>{{$solicitudesPorArea[1]}}</b></td>
            </tr>
          </tbody>
        </table>
        {{-- Item Desarrollo Completo: --}}
        <div class="progress" style="height: 30px;">
          @php $totalDesarrollosEnVenta = 0; @endphp
          @foreach($desarrollosPorArea[1] as $semaforo)
          @php $totalDesarrollosEnVenta += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[1][0]}}<br>{{$desarrollosPorAreaDias[1][0]}}D" class="progress-bar bg-success" role="progressbar" @if($desarrollosPorArea[1][0]> 0) style="width:{{$desarrollosPorArea[1][0] * 100 / $totalDesarrollosEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[1][0]}}<br>{{$desarrollosPorAreaDias[1][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[1][1]}}<br>{{$desarrollosPorAreaDias[1][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($desarrollosPorArea[1][1]> 0) style="width:{{$desarrollosPorArea[1][1] * 100 / $totalDesarrollosEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[1][1]}}<br>{{$desarrollosPorAreaDias[1][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[1][2]}}<br>{{$desarrollosPorAreaDias[1][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($desarrollosPorArea[1][2]> 0) style="width:{{$desarrollosPorArea[1][2] * 100 / $totalDesarrollosEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[1][2]}}<br>{{$desarrollosPorAreaDias[1][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Desarrollo Completo: <b>{{$totalDesarrolloPorArea[1]}}</b></p>

        {{-- Item Arte con Material : --}}
        <div class="progress" style="height: 30px;">
          @php $totalArtesEnVenta = 0; @endphp
          @foreach($artesPorArea[1] as $semaforo)
          @php $totalArtesEnVenta += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[1][0]}}<br>{{$artesPorAreaDias[1][0]}}D" class="progress-bar bg-success" role="progressbar" @if($artesPorArea[1][0]> 0) style="width:{{$artesPorArea[1][0] * 100 / $totalArtesEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[1][0]}}<br>{{$artesPorAreaDias[1][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[1][1]}}<br>{{$artesPorAreaDias[1][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($artesPorArea[1][1]> 0) style="width:{{$artesPorArea[1][1] * 100 / $totalArtesEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[1][1]}}<br>{{$artesPorAreaDias[1][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[1][2]}}<br>{{$artesPorAreaDias[1][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($artesPorArea[1][2]> 0) style="width:{{$artesPorArea[1][2] * 100 / $totalArtesEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[1][2]}}<br>{{$artesPorAreaDias[1][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Arte con Material: <b>{{$totalArtePorArea[1]}}</b></p>

        {{-- Item Cotizan con Cad: --}}
        <div class="progress" style="height: 30px;">
          @php $totalCotizaConCadEnVenta = 0; @endphp
          @foreach($cotizaConCadPorArea[1] as $semaforo)
          @php $totalCotizaConCadEnVenta += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaConCadPorArea[1][0]}}<br>{{$cotizaConCadPorAreaDias[1][0]}}D" class="progress-bar bg-success" role="progressbar" @if($cotizaConCadPorArea[1][0]> 0) style="width:{{$cotizaConCadPorArea[1][0] * 100 / $totalCotizaConCadEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaConCadPorArea[1][0]}}<br>{{$cotizaConCadPorAreaDias[1][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaConCadPorArea[1][1]}}<br>{{$cotizaConCadPorAreaDias[1][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($cotizaConCadPorArea[1][1]> 0) style="width:{{$cotizaConCadPorArea[1][1] * 100 / $totalCotizaConCadEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaConCadPorArea[1][1]}}<br>{{$cotizaConCadPorAreaDias[1][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaConCadPorArea[1][2]}}<br>{{$cotizaConCadPorAreaDias[1][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($cotizaConCadPorArea[1][2]> 0) style="width:{{$cotizaConCadPorArea[1][2] * 100 / $totalCotizaConCadEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaConCadPorArea[1][2]}}<br>{{$cotizaConCadPorAreaDias[1][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Cotiza con Cad: <b>{{$totalCotizaConCadPorArea[1]}}</b></p>

        {{-- Item Cotizan Sin Cad: --}}
        <div class="progress" style="height: 30px;">
          @php $totalCotizaSinCadEnVenta = 0; @endphp
          @foreach($cotizaSinCadPorArea[1] as $semaforo)
          @php $totalCotizaSinCadEnVenta += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaSinCadPorArea[1][0]}}<br>{{$cotizaSinCadPorAreaDias[1][0]}}D" class="progress-bar bg-success" role="progressbar" @if($cotizaSinCadPorArea[1][0]> 0) style="width:{{$cotizaSinCadPorArea[1][0] * 100 / $totalCotizaSinCadEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaSinCadPorArea[1][0]}}<br>{{$cotizaSinCadPorAreaDias[1][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaSinCadPorArea[1][1]}}<br>{{$cotizaSinCadPorAreaDias[1][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($cotizaSinCadPorArea[1][1]> 0) style="width:{{$cotizaSinCadPorArea[1][1] * 100 / $totalCotizaSinCadEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaSinCadPorArea[1][1]}}<br>{{$cotizaSinCadPorAreaDias[1][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaSinCadPorArea[1][2]}}<br>{{$cotizaSinCadPorAreaDias[1][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($cotizaSinCadPorArea[1][2]> 0) style="width:{{$cotizaSinCadPorArea[1][2] * 100 / $totalCotizaSinCadEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaSinCadPorArea[1][2]}}<br>{{$cotizaSinCadPorAreaDias[1][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Cotiza Sin Cad: <b>{{$totalCotizaSinCadPorArea[1]}}</b></p>

        {{-- Item Muestra Con Cad: --}}
        <div class="progress" style="height: 30px;">
          @php $totalMuestraConCadEnVenta = 0; @endphp
          @foreach($muestraConCadPorArea[1] as $semaforo)
          @php $totalMuestraConCadEnVenta += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$muestraConCadPorArea[1][0]}}<br>{{$muestraConCadPorAreaDias[1][0]}}D" class="progress-bar bg-success" role="progressbar" @if($muestraConCadPorArea[1][0]> 0) style="width:{{$muestraConCadPorArea[1][0] * 100 / $totalMuestraConCadEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$muestraConCadPorArea[1][0]}}<br>{{$muestraConCadPorAreaDias[1][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$muestraConCadPorArea[1][1]}}<br>{{$muestraConCadPorAreaDias[1][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($muestraConCadPorArea[1][1]> 0) style="width:{{$muestraConCadPorArea[1][1] * 100 / $totalMuestraConCadEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$muestraConCadPorArea[1][1]}}<br>{{$muestraConCadPorAreaDias[1][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$muestraConCadPorArea[1][2]}}<br>{{$muestraConCadPorAreaDias[1][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($muestraConCadPorArea[1][2]> 0) style="width:{{$muestraConCadPorArea[1][2] * 100 / $totalMuestraConCadEnVenta}}%;" @else style="width:0%;display:none;" @endif>{{$muestraConCadPorArea[1][2]}}<br>{{$muestraConCadPorAreaDias[1][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Muestra Con Cad: <b>{{$totalMuestraConCadPorArea[1]}}</b></p>

      </div>
    </div>
  </div>

  {{-- Diseño Estructural: --}}
  <div class="item-report item-report_sm col-2-5" style="height: auto; padding: 3px;border: solid 1px #979393;">
    <div class="container-report" style="background-color: #eee;border-radius: 8px;">
      <div class="container" style="padding-right: 3px;padding-left: 3px;">
        <table border="0" class="table">
          <thead>
            <tr>
              <th class="text-center">Diseño Estructural</th>
            </tr>
          </thead>
          <tbody style="font-size: 10px;">
            <tr>
              <td>Solicitudes Totales: <b>{{$solicitudesTotales}}</b></td>
            </tr>
            <tr>
              <td>Porcentaje en Diseño Estructural: <b>{{$porcentajeSolicitudesPorArea[2]}}</b></td>
            </tr>
            <tr>
              <td>Solicitudes en Diseño Estructural: <b>{{$solicitudesPorArea[2]}}</b></td>
            </tr>
          </tbody>
        </table>
        {{-- Item Desarrollo Completo: --}}
        <div class="progress" style="height: 30px;">
          @php $totalDesarrollosEnDesarrollo = 0; @endphp
          @foreach($desarrollosPorArea[2] as $semaforo)
          @php $totalDesarrollosEnDesarrollo += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[2][0]}}<br>{{$desarrollosPorAreaDias[2][0]}}D" class="progress-bar bg-success" role="progressbar" @if($desarrollosPorArea[2][0]> 0) style="width:{{$desarrollosPorArea[2][0] * 100 / $totalDesarrollosEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[2][0]}}<br>{{$desarrollosPorAreaDias[2][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[2][1]}}<br>{{$desarrollosPorAreaDias[2][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($desarrollosPorArea[2][1]> 0) style="width:{{$desarrollosPorArea[2][1] * 100 / $totalDesarrollosEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[2][1]}}<br>{{$desarrollosPorAreaDias[2][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[2][2]}}<br>{{$desarrollosPorAreaDias[2][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($desarrollosPorArea[2][2]> 0) style="width:{{$desarrollosPorArea[2][2] * 100 / $totalDesarrollosEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[2][2]}}<br>{{$desarrollosPorAreaDias[2][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Desarrollo Completo: <b>{{$totalDesarrolloPorArea[2]}}</b></p>

        <!-- Arte con Material -->
        {{-- Item Arte con Material: --}}
        <div class="progress" style="height: 30px;">
          @php $totalArtesEnDesarrollo = 0; @endphp
          @foreach($artesPorArea[2] as $semaforo)
          @php $totalArtesEnDesarrollo += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[2][0]}}<br>{{$artesPorAreaDias[2][0]}}D" class="progress-bar bg-success" role="progressbar" @if($artesPorArea[2][0]> 0) style="width:{{$artesPorArea[2][0] * 100 / $totalArtesEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[2][0]}}<br>{{$artesPorAreaDias[2][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[2][1]}}<br>{{$artesPorAreaDias[2][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($artesPorArea[2][1]> 0) style="width:{{$artesPorArea[2][1] * 100 / $totalArtesEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[2][1]}}<br>{{$artesPorAreaDias[2][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[2][2]}}<br>{{$artesPorAreaDias[2][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($artesPorArea[2][2]> 0) style="width:{{$artesPorArea[2][2] * 100 / $totalArtesEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[2][2]}}<br>{{$artesPorAreaDias[2][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Arte con Material: <b>{{$totalArtePorArea[2]}}</b></p>


        {{-- Item Cotiza con Cad: --}}
        <div class="progress" style="height: 30px;">
          @php $totalCotizaConCadEnDesarrollo = 0; @endphp
          @foreach($cotizaConCadPorArea[2] as $semaforo)
          @php $totalCotizaConCadEnDesarrollo += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaConCadPorArea[2][0]}}<br>{{$cotizaConCadPorAreaDias[2][0]}}D" class="progress-bar bg-success" role="progressbar" @if($cotizaConCadPorArea[2][0]> 0) style="width:{{$cotizaConCadPorArea[2][0] * 100 / $totalCotizaConCadEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaConCadPorArea[2][0]}}<br>{{$cotizaConCadPorAreaDias[2][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaConCadPorArea[2][1]}}<br>{{$cotizaConCadPorAreaDias[2][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($cotizaConCadPorArea[2][1]> 0) style="width:{{$cotizaConCadPorArea[2][1] * 100 / $totalCotizaConCadEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaConCadPorArea[2][1]}}<br>{{$cotizaConCadPorAreaDias[2][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaConCadPorArea[2][2]}}<br>{{$cotizaConCadPorAreaDias[2][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($cotizaConCadPorArea[2][2]> 0) style="width:{{$cotizaConCadPorArea[2][2] * 100 / $totalCotizaConCadEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaConCadPorArea[2][2]}}<br>{{$cotizaConCadPorAreaDias[2][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Cotiza con Cad: <b>{{$totalCotizaConCadPorArea[2]}}</b></p>

        {{-- Item Cotiza Sin Cad: --}}
        <div class="progress" style="height: 30px;">
          @php $totalCotizaSinCadEnDesarrollo = 0; @endphp
          @foreach($cotizaSinCadPorArea[2] as $semaforo)
          @php $totalCotizaSinCadEnDesarrollo += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaSinCadPorArea[2][0]}}<br>{{$cotizaSinCadPorAreaDias[2][0]}}D" class="progress-bar bg-success" role="progressbar" @if($cotizaSinCadPorArea[2][0]> 0) style="width:{{$cotizaSinCadPorArea[2][0] * 100 / $totalCotizaSinCadEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaSinCadPorArea[2][0]}}<br>{{$cotizaSinCadPorAreaDias[2][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaSinCadPorArea[2][1]}}<br>{{$cotizaSinCadPorAreaDias[2][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($cotizaSinCadPorArea[2][1]> 0) style="width:{{$cotizaSinCadPorArea[2][1] * 100 / $totalCotizaSinCadEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaSinCadPorArea[2][1]}}<br>{{$cotizaSinCadPorAreaDias[2][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaSinCadPorArea[2][2]}}<br>{{$cotizaSinCadPorAreaDias[2][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($cotizaSinCadPorArea[2][2]> 0) style="width:{{$cotizaSinCadPorArea[2][2] * 100 / $totalCotizaSinCadEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaSinCadPorArea[2][2]}}<br>{{$cotizaSinCadPorAreaDias[2][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Cotiza Sin Cad: <b>{{$totalCotizaSinCadPorArea[2]}}</b></p>

        {{-- Item Muestra Con Cad: --}}
        <div class="progress" style="height: 30px;">
          @php $totalMuestraConCadEnDesarrollo = 0; @endphp
          @foreach($muestraConCadPorArea[2] as $semaforo)
          @php $totalMuestraConCadEnDesarrollo += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$muestraConCadPorArea[2][0]}}<br>{{$muestraConCadPorAreaDias[2][0]}}D" class="progress-bar bg-success" role="progressbar" @if($muestraConCadPorArea[2][0]> 0) style="width:{{$muestraConCadPorArea[2][0] * 100 / $totalMuestraConCadEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$muestraConCadPorArea[2][0]}}<br>{{$muestraConCadPorAreaDias[2][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$muestraConCadPorArea[2][1]}}<br>{{$muestraConCadPorAreaDias[2][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($muestraConCadPorArea[2][1]> 0) style="width:{{$muestraConCadPorArea[2][1] * 100 / $totalMuestraConCadEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$muestraConCadPorArea[2][1]}}<br>{{$muestraConCadPorAreaDias[2][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$muestraConCadPorArea[2][2]}}<br>{{$muestraConCadPorAreaDias[2][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($muestraConCadPorArea[2][2]> 0) style="width:{{$muestraConCadPorArea[2][2] * 100 / $totalMuestraConCadEnDesarrollo}}%;" @else style="width:0%;display:none;" @endif>{{$muestraConCadPorArea[2][2]}}<br>{{$muestraConCadPorAreaDias[2][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Muestra Con Cad: <b>{{$totalMuestraConCadPorArea[2]}}</b></p>

      </div>
    </div>
  </div>



  {{-- Sala de Muestras: --}}
  <div class="item-report item-report_sm col-2-5" style="height: auto; padding: 3px;border: solid 1px #979393;">
    <div class="container-report" style="background-color: #eee;border-radius: 8px;">
      <div class="container" style="padding-right: 3px;padding-left: 3px;">
        <table border="0" class="table">
          <thead>
            <tr>
              <th class="text-center">Sala de Muestras</th>
            </tr>
          </thead>
          <tbody style="font-size: 10px;">
            <tr>
              <td>Solicitudes Totales: <b>{{$solicitudesTotales}}</b></td>
            </tr>
            <tr>
              <td>Porcentaje en Muestras: <b>{{$porcentajeSolicitudesPorArea[6]}}</b></td>
            </tr>
            <tr>
              <td>Solicitudes en Muestras: <b>{{$solicitudesPorArea[6]}}</b></td>
            </tr>
          </tbody>
        </table>
        {{-- Item Sala Muestras : --}}
        <div class="progress" style="height: 30px;">
          @php $totalDesarrollosEnMuestras = 0; @endphp
          @foreach($desarrollosPorArea[6] as $semaforo)
          @php $totalDesarrollosEnMuestras += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[6][0]}}<br>{{$desarrollosPorAreaDias[6][0]}}D" class="progress-bar bg-success" role="progressbar" @if($desarrollosPorArea[6][0]> 0) style="width:{{$desarrollosPorArea[6][0] * 100 / $totalDesarrollosEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[6][0]}}<br>{{$desarrollosPorAreaDias[6][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[6][1]}}<br>{{$desarrollosPorAreaDias[6][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($desarrollosPorArea[6][1]> 0) style="width:{{$desarrollosPorArea[6][1] * 100 / $totalDesarrollosEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[6][1]}}<br>{{$desarrollosPorAreaDias[6][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[6][2]}}<br>{{$desarrollosPorAreaDias[6][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($desarrollosPorArea[6][2]> 0) style="width:{{$desarrollosPorArea[6][2] * 100 / $totalDesarrollosEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[6][2]}}<br>{{$desarrollosPorAreaDias[6][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Desarrollo Completo: <b>{{$totalDesarrolloPorArea[6]}}</b></p>

        <!-- Arte con Material -->
        {{-- Item Arte con Material: --}}
        <div class="progress" style="height: 30px;">
          @php $totalArtesEnMuestras = 0; @endphp
          @foreach($artesPorArea[6] as $semaforo)
          @php $totalArtesEnMuestras += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[6][0]}}<br>{{$artesPorAreaDias[6][0]}}D" class="progress-bar bg-success" role="progressbar" @if($artesPorArea[6][0]> 0) style="width:{{$artesPorArea[6][0] * 100 / $totalArtesEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[6][0]}}<br>{{$artesPorAreaDias[6][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[6][1]}}<br>{{$artesPorAreaDias[6][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($artesPorArea[6][1]> 0) style="width:{{$artesPorArea[6][1] * 100 / $totalArtesEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[6][1]}}<br>{{$artesPorAreaDias[6][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[6][2]}}<br>{{$artesPorAreaDias[6][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($artesPorArea[6][2]> 0) style="width:{{$artesPorArea[6][2] * 100 / $totalArtesEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[6][2]}}<br>{{$artesPorAreaDias[6][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Arte con Material: <b>{{$totalArtePorArea[6]}}</b></p>


        {{-- Item Cotiza con Cad: --}}
        <div class="progress" style="height: 30px;">
          @php $totalCotizaConCadEnMuestras = 0; @endphp
          @foreach($cotizaConCadPorArea[6] as $semaforo)
          @php $totalCotizaConCadEnMuestras += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaConCadPorArea[6][0]}}<br>{{$cotizaConCadPorAreaDias[6][0]}}D" class="progress-bar bg-success" role="progressbar" @if($cotizaConCadPorArea[6][0]> 0) style="width:{{$cotizaConCadPorArea[6][0] * 100 / $totalCotizaConCadEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaConCadPorArea[6][0]}}<br>{{$cotizaConCadPorAreaDias[6][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaConCadPorArea[6][1]}}<br>{{$cotizaConCadPorAreaDias[6][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($cotizaConCadPorArea[6][1]> 0) style="width:{{$cotizaConCadPorArea[6][1] * 100 / $totalCotizaConCadEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaConCadPorArea[6][1]}}<br>{{$cotizaConCadPorAreaDias[6][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaConCadPorArea[6][2]}}<br>{{$cotizaConCadPorAreaDias[6][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($cotizaConCadPorArea[6][2]> 0) style="width:{{$cotizaConCadPorArea[6][2] * 100 / $totalCotizaConCadEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaConCadPorArea[6][2]}}<br>{{$cotizaConCadPorAreaDias[6][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Cotiza con Cad: <b>{{$totalCotizaConCadPorArea[6]}}</b></p>

        {{-- Item Cotiza Sin Cad: --}}
        <div class="progress" style="height: 30px;">
          @php $totalCotizaSinCadEnMuestras = 0; @endphp
          @foreach($cotizaSinCadPorArea[6] as $semaforo)
          @php $totalCotizaSinCadEnMuestras += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaSinCadPorArea[6][0]}}<br>{{$cotizaSinCadPorAreaDias[6][0]}}D" class="progress-bar bg-success" role="progressbar" @if($cotizaSinCadPorArea[6][0]> 0) style="width:{{$cotizaSinCadPorArea[6][0] * 100 / $totalCotizaSinCadEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaSinCadPorArea[6][0]}}<br>{{$cotizaSinCadPorAreaDias[6][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaSinCadPorArea[6][1]}}<br>{{$cotizaSinCadPorAreaDias[6][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($cotizaSinCadPorArea[6][1]> 0) style="width:{{$cotizaSinCadPorArea[6][1] * 100 / $totalCotizaSinCadEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaSinCadPorArea[6][1]}}<br>{{$cotizaSinCadPorAreaDias[6][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaSinCadPorArea[6][2]}}<br>{{$cotizaSinCadPorAreaDias[6][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($cotizaSinCadPorArea[6][2]> 0) style="width:{{$cotizaSinCadPorArea[6][2] * 100 / $totalCotizaSinCadEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaSinCadPorArea[6][2]}}<br>{{$cotizaSinCadPorAreaDias[6][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Cotiza Sin Cad: <b>{{$totalCotizaSinCadPorArea[6]}}</b></p>

        {{-- Item Muestra Con Cad: --}}
        <div class="progress" style="height: 30px;">
          @php $totalMuestraConCadEnMuestras = 0; @endphp
          @foreach($muestraConCadPorArea[6] as $semaforo)
          @php $totalMuestraConCadEnMuestras += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$muestraConCadPorArea[6][0]}}<br>{{$muestraConCadPorAreaDias[6][0]}}D" class="progress-bar bg-success" role="progressbar" @if($muestraConCadPorArea[6][0]> 0) style="width:{{$muestraConCadPorArea[6][0] * 100 / $totalMuestraConCadEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$muestraConCadPorArea[6][0]}}<br>{{$muestraConCadPorAreaDias[6][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$muestraConCadPorArea[6][1]}}<br>{{$muestraConCadPorAreaDias[6][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($muestraConCadPorArea[6][1]> 0) style="width:{{$muestraConCadPorArea[6][1] * 100 / $totalMuestraConCadEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$muestraConCadPorArea[6][1]}}<br>{{$muestraConCadPorAreaDias[6][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$muestraConCadPorArea[6][2]}}<br>{{$muestraConCadPorAreaDias[6][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($muestraConCadPorArea[6][2]> 0) style="width:{{$muestraConCadPorArea[6][2] * 100 / $totalMuestraConCadEnMuestras}}%;" @else style="width:0%;display:none;" @endif>{{$muestraConCadPorArea[6][2]}}<br>{{$muestraConCadPorAreaDias[6][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Muestra Con Cad: <b>{{$totalMuestraConCadPorArea[6]}}</b></p>

      </div>
    </div>
  </div>



  {{-- Diseño Gráfico: --}}
  <div class="item-report item-report_sm col-2-5" style="height: auto; padding: 3px;border: solid 1px #979393;">
    <div class="container-report" style="background-color: #eee;border-radius: 8px;">
      <div class="container" style="padding-right: 3px;padding-left: 3px;">
        <table border="0" class="table">
          <thead>
            <tr>
              <th class="text-center">Diseño Gráfico</th>
            </tr>
          </thead>
          <tbody style="font-size: 10px;">
            <tr>
              <td>Solicitudes Totales: <b>{{$solicitudesTotales}}</b></td>
            </tr>
            <tr>
              <td>Porcentaje en Diseños Gráfico: <b>{{$porcentajeSolicitudesPorArea[3]}}</b></td>
            </tr>
            <tr>
              <td>Solicitudes en D. Gráficos: <b>{{$solicitudesPorArea[3]}}</b></td>
            </tr>
          </tbody>
        </table>
        {{-- Item Desarrollo Completo: --}}
        <div class="progress" style="height: 30px;">
          @php $totalDesarrollosEnDiseño = 0; @endphp
          @foreach($desarrollosPorArea[3] as $semaforo)
          @php $totalDesarrollosEnDiseño += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[3][0]}}<br>{{$desarrollosPorAreaDias[3][0]}}D" class="progress-bar bg-success" role="progressbar" @if($desarrollosPorArea[3][0]> 0) style="width:{{$desarrollosPorArea[3][0] * 100 / $totalDesarrollosEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[3][0]}}<br>{{$desarrollosPorAreaDias[3][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[3][1]}}<br>{{$desarrollosPorAreaDias[3][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($desarrollosPorArea[3][1]> 0) style="width:{{$desarrollosPorArea[3][1] * 100 / $totalDesarrollosEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[3][1]}}<br>{{$desarrollosPorAreaDias[3][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[3][2]}}<br>{{$desarrollosPorAreaDias[3][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($desarrollosPorArea[3][2]> 0) style="width:{{$desarrollosPorArea[3][2] * 100 / $totalDesarrollosEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[3][2]}}<br>{{$desarrollosPorAreaDias[3][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Desarrollo Completo: <b>{{$totalDesarrolloPorArea[3]}}</b></p>

        {{-- Item Arte con Material: --}}
        <div class="progress" style="height: 30px;">
          @php $totalArtesEnDiseño = 0; @endphp
          @foreach($artesPorArea[3] as $semaforo)
          @php $totalArtesEnDiseño += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[3][0]}}<br>{{$artesPorAreaDias[3][0]}}D" class="progress-bar bg-success" role="progressbar" @if($artesPorArea[3][0]> 0) style="width:{{$artesPorArea[3][0] * 100 / $totalArtesEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[3][0]}}<br>{{$artesPorAreaDias[3][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[3][1]}}<br>{{$artesPorAreaDias[3][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($artesPorArea[3][1]> 0) style="width:{{$artesPorArea[3][1] * 100 / $totalArtesEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[3][1]}}<br>{{$artesPorAreaDias[3][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[3][2]}}<br>{{$artesPorAreaDias[3][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($artesPorArea[3][2]> 0) style="width:{{$artesPorArea[3][2] * 100 / $totalArtesEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[3][2]}}<br>{{$artesPorAreaDias[3][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Arte con Material: <b>{{$totalArtePorArea[3]}}</b></p>

        {{-- Item Cotizan con Cad: --}}
        <div class="progress" style="height: 30px;">
          @php $totalCotizaConCadEnDiseño = 0; @endphp
          @foreach($cotizaConCadPorArea[3] as $semaforo)
          @php $totalCotizaConCadEnDiseño += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaConCadPorArea[3][0]}}<br>{{$cotizaConCadPorAreaDias[3][0]}}D" class="progress-bar bg-success" role="progressbar" @if($cotizaConCadPorArea[3][0]> 0) style="width:{{$cotizaConCadPorArea[3][0] * 100 / $totalCotizaConCadEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaConCadPorArea[3][0]}}<br>{{$cotizaConCadPorAreaDias[3][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaConCadPorArea[3][1]}}<br>{{$cotizaConCadPorAreaDias[3][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($cotizaConCadPorArea[3][1]> 0) style="width:{{$cotizaConCadPorArea[3][1] * 100 / $totalCotizaConCadEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaConCadPorArea[3][1]}}<br>{{$cotizaConCadPorAreaDias[3][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaConCadPorArea[3][2]}}<br>{{$cotizaConCadPorAreaDias[3][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($cotizaConCadPorArea[3][2]> 0) style="width:{{$cotizaConCadPorArea[3][2] * 100 / $totalCotizaConCadEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaConCadPorArea[3][2]}}<br>{{$cotizaConCadPorAreaDias[3][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Cotiza con Cad: <b>{{$totalCotizaConCadPorArea[3]}}</b></p>

        {{-- Item Cotizan Sin Cad: --}}
        <div class="progress" style="height: 30px;">
          @php $totalCotizaSinCadEnDiseño = 0; @endphp
          @foreach($cotizaSinCadPorArea[3] as $semaforo)
          @php $totalCotizaSinCadEnDiseño += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaSinCadPorArea[3][0]}}<br>{{$cotizaSinCadPorAreaDias[3][0]}}D" class="progress-bar bg-success" role="progressbar" @if($cotizaSinCadPorArea[3][0]> 0) style="width:{{$cotizaSinCadPorArea[3][0] * 100 / $totalCotizaSinCadEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaSinCadPorArea[3][0]}}<br>{{$cotizaSinCadPorAreaDias[3][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaSinCadPorArea[3][1]}}<br>{{$cotizaSinCadPorAreaDias[3][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($cotizaSinCadPorArea[3][1]> 0) style="width:{{$cotizaSinCadPorArea[3][1] * 100 / $totalCotizaSinCadEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaSinCadPorArea[3][1]}}<br>{{$cotizaSinCadPorAreaDias[3][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$cotizaSinCadPorArea[3][2]}}<br>{{$cotizaSinCadPorAreaDias[3][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($cotizaSinCadPorArea[3][2]> 0) style="width:{{$cotizaSinCadPorArea[3][2] * 100 / $totalCotizaSinCadEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$cotizaSinCadPorArea[3][2]}}<br>{{$cotizaSinCadPorAreaDias[3][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Cotiza Sin Cad: <b>{{$totalCotizaSinCadPorArea[3]}}</b></p>

        {{-- Item Muestra Con Cad: --}}
        <div class="progress" style="height: 30px;">
          @php $totalMuestraConCadEnDiseño = 0; @endphp
          @foreach($muestraConCadPorArea[3] as $semaforo)
          @php $totalMuestraConCadEnDiseño += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$muestraConCadPorArea[3][0]}}<br>{{$muestraConCadPorAreaDias[3][0]}}D" class="progress-bar bg-success" role="progressbar" @if($muestraConCadPorArea[3][0]> 0) style="width:{{$muestraConCadPorArea[3][0] * 100 / $totalMuestraConCadEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$muestraConCadPorArea[3][0]}}<br>{{$muestraConCadPorAreaDias[3][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$muestraConCadPorArea[3][1]}}<br>{{$muestraConCadPorAreaDias[3][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($muestraConCadPorArea[3][1]> 0) style="width:{{$muestraConCadPorArea[3][1] * 100 / $totalMuestraConCadEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$muestraConCadPorArea[3][1]}}<br>{{$muestraConCadPorAreaDias[3][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$muestraConCadPorArea[3][2]}}<br>{{$muestraConCadPorAreaDias[3][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($muestraConCadPorArea[3][2]> 0) style="width:{{$muestraConCadPorArea[3][2] * 100 / $totalMuestraConCadEnDiseño}}%;" @else style="width:0%;display:none;" @endif>{{$muestraConCadPorArea[3][2]}}<br>{{$muestraConCadPorAreaDias[3][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Muestra Con Cad: <b>{{$totalMuestraConCadPorArea[3]}}</b></p>

      </div>
    </div>
  </div>
  {{-- Pre Catalogación: --}}
  <div class="item-report item-report_sm col-2-5" style="height: auto; padding: 3px;border: solid 1px #979393;">
    <div class="container-report" style="background-color: #eee;border-radius: 8px;">
      <div class="container" style="padding-right: 3px;padding-left: 3px;">
        <table border="0" class="table">
          <thead>
            <tr>
              <th class="text-center">Precatalogación</th>
            </tr>
          </thead>
          <tbody style="font-size: 10px;">
            <tr>
              <td>Solicitudes Totales: <b>{{$solicitudesTotales}}</b></td>
            </tr>
            <tr>
              <td>Porcentaje en Precatalogación: <b>{{$porcentajeSolicitudesPorArea[5]}}</b></td>
            </tr>
            <tr>
              <td>Solicitudes en Precatalogación: <b>{{$solicitudesPorArea[5]}}</b></td>
            </tr>
          </tbody>
        </table>
        {{-- Item Desarrollo Completo: --}}
        <div class="progress" style="height: 30px;">
          @php $totalDesarrollosEnPrecatalogacion = 0; @endphp
          @foreach($desarrollosPorArea[5] as $semaforo)
          @php $totalDesarrollosEnPrecatalogacion += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[5][0]}}<br>{{$desarrollosPorAreaDias[5][0]}}D" class="progress-bar bg-success" role="progressbar" @if($desarrollosPorArea[5][0]> 0) style="width:{{$desarrollosPorArea[5][0] * 100 / $totalDesarrollosEnPrecatalogacion}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[5][0]}}<br>{{$desarrollosPorAreaDias[5][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[5][1]}}<br>{{$desarrollosPorAreaDias[5][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($desarrollosPorArea[5][1]> 0) style="width:{{$desarrollosPorArea[5][1] * 100 / $totalDesarrollosEnPrecatalogacion}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[5][1]}}<br>{{$desarrollosPorAreaDias[5][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[5][2]}}<br>{{$desarrollosPorAreaDias[5][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($desarrollosPorArea[5][2]> 0) style="width:{{$desarrollosPorArea[5][2] * 100 / $totalDesarrollosEnPrecatalogacion}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[5][2]}}<br>{{$desarrollosPorAreaDias[5][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Desarrollo Completo: <b>{{$totalDesarrolloPorArea[5]}}</b></p>
        {{-- Item Arte con Material: --}}
        <div class="progress" style="height: 30px;">
          @php $totalArtesEnPrecatalogacion = 0; @endphp
          @foreach($artesPorArea[5] as $semaforo)
          @php $totalArtesEnPrecatalogacion += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[5][0]}}<br>{{$artesPorAreaDias[5][0]}}D" class="progress-bar bg-success" role="progressbar" @if($artesPorArea[5][0]> 0) style="width:{{$artesPorArea[5][0] * 100 / $totalArtesEnPrecatalogacion}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[5][0]}}<br>{{$artesPorAreaDias[5][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[5][1]}}<br>{{$artesPorAreaDias[5][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($artesPorArea[5][1]> 0) style="width:{{$artesPorArea[5][1] * 100 / $totalArtesEnPrecatalogacion}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[5][1]}}<br>{{$artesPorAreaDias[5][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[5][2]}}<br>{{$artesPorAreaDias[5][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($artesPorArea[5][2]> 0) style="width:{{$artesPorArea[5][2] * 100 / $totalArtesEnPrecatalogacion}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[5][2]}}<br>{{$artesPorAreaDias[5][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Arte con Material: <b>{{$totalArtePorArea[5]}}</b></p>

      </div>
    </div>
  </div>
  {{-- Catalogación: --}}
  <div class="item-report item-report_sm col-2-5" style="height: auto; padding: 3px;border: solid 1px #979393;">
    <div class="container-report" style="background-color: #eee;border-radius: 8px;">
      <div class="container" style="padding-right: 3px;padding-left: 3px;">
        <table border="0" class="table">
          <thead>
            <tr>
              <th class="text-center">Catalogación</th>
            </tr>
          </thead>
          <tbody style="font-size: 10px;">
            <tr>
              <td>Solicitudes Totales: <b>{{$solicitudesTotales}}</b></td>
            </tr>
            <tr>
              <td>Porcentaje en Catalogación: <b>{{$porcentajeSolicitudesPorArea[4]}}</b></td>
            </tr>
            <tr>
              <td>Solicitudes en Catalogación: <b>{{$solicitudesPorArea[4]}}</b></td>
            </tr>
          </tbody>
        </table>
        {{-- Item Desarrollo Completo: --}}
        <div class="progress" style="height: 30px;">
          @php $totalDesarrollosEnCatalogacion = 0; @endphp
          @foreach($desarrollosPorArea[4] as $semaforo)
          @php $totalDesarrollosEnCatalogacion += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[4][0]}}<br>{{$desarrollosPorAreaDias[4][0]}}D" class="progress-bar bg-success" role="progressbar" @if($desarrollosPorArea[4][0]> 0) style="width:{{$desarrollosPorArea[4][0] * 100 / $totalDesarrollosEnCatalogacion}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[4][0]}}<br>{{$desarrollosPorAreaDias[4][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[4][1]}}<br>{{$desarrollosPorAreaDias[4][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($desarrollosPorArea[4][1]> 0) style="width:{{$desarrollosPorArea[4][1] * 100 / $totalDesarrollosEnCatalogacion}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[4][1]}}<br>{{$desarrollosPorAreaDias[4][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$desarrollosPorArea[4][2]}}<br>{{$desarrollosPorAreaDias[4][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($desarrollosPorArea[4][2]> 0) style="width:{{$desarrollosPorArea[4][2] * 100 / $totalDesarrollosEnCatalogacion}}%;" @else style="width:0%;display:none;" @endif>{{$desarrollosPorArea[4][2]}}<br>{{$desarrollosPorAreaDias[4][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Desarrollo Completo: <b>{{$totalDesarrolloPorArea[4]}}</b></p>
        {{-- Item Arte con Material: --}}
        <div class="progress" style="height: 30px;">
          @php $totalArtesEnCatalogacion = 0; @endphp
          @foreach($artesPorArea[4] as $semaforo)
          @php $totalArtesEnCatalogacion += $semaforo@endphp
          @endforeach
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[4][0]}}<br>{{$artesPorAreaDias[4][0]}}D" class="progress-bar bg-success" role="progressbar" @if($artesPorArea[4][0]> 0) style="width:{{$artesPorArea[4][0] * 100 / $totalArtesEnCatalogacion}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[4][0]}}<br>{{$artesPorAreaDias[4][0]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[4][1]}}<br>{{$artesPorAreaDias[4][1]}}D" class="progress-bar bg-warning" role="progressbar" @if($artesPorArea[4][1]> 0) style="width:{{$artesPorArea[4][1] * 100 / $totalArtesEnCatalogacion}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[4][1]}}<br>{{$artesPorAreaDias[4][1]}}D</div>
          <div data-toggle="tooltip" data-html="true" title="{{$artesPorArea[4][2]}}<br>{{$artesPorAreaDias[4][2]}}D" class="progress-bar bg-danger" role="progressbar" @if($artesPorArea[4][2]> 0) style="width:{{$artesPorArea[4][2] * 100 / $totalArtesEnCatalogacion}}%;" @else style="width:0%;display:none;" @endif>{{$artesPorArea[4][2]}}<br>{{$artesPorAreaDias[4][2]}}D</div>
        </div>
        <p class="text-center" style="font-size: 11px;"> Arte con Material: <b>{{$totalArtePorArea[4]}}</b></p>

      </div>
    </div>
  </div>

</div>

<?php /*
{{-- panel completo por dias: --}}
<div class="container-report">
  <div class="item-report item-report_sm col-12" style="height: auto;">
    <table border="0" class="table" style="margin-bottom: 0px;padding:5px 15px">
      <tbody style="font-size: 15px;">
        {{-- Desarrollo Completo: --}}
        <tr class="row">
          @php $totalDesarrollos = 0; @endphp
          @foreach($solicitudesPorTipoSolicitudPorArea[1] as $semaforo)
          @php $totalDesarrollos += $semaforo @endphp
          @endforeach
          @php $totalDesarrollosDias = 0; @endphp
          @foreach($solicitudesPorTipoSolicitudPorAreaDias[1] as $key => $semaforo)
          @php $totalDesarrollosDias += $semaforo * $solicitudesPorTipoSolicitudPorArea[1][$key] @endphp
          @endforeach
          <td class="col-2 text-right" style="vertical-align: middle;margin-top: 10px;">Desarrollo Completo: <b>{{$totalDesarrollos}} - @if($totalDesarrollos > 0){{number_format_unlimited_precision(round($totalDesarrollosDias / $totalDesarrollos,1))}}@else 0 @endif</b></td>
          <td class="col-10 text-left" style="padding: 0px 10px;">
            <div class="progress" style="height: 35px; margin-top: 5px; margin-bottom: 5px;">
              <div class="progress-bar bg-ventas" data-toggle="tooltip" data-html="true" title="Ventas <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[1][1])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[1][1]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[1][1] * 100 / $totalDesarrollos}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[1][1]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[1][1])}}D</div>
              <div class="progress-bar bg-ingenieria" data-toggle="tooltip" data-html="true" title="Diseño Estructural <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[1][2])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[1][2]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[1][2] * 100 / $totalDesarrollos}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[1][2]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[1][2])}}D</div>
              <div class="progress-bar bg-diseno" data-toggle="tooltip" data-html="true" title="Diseño Grafico <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[1][3])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[1][3]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[1][3] * 100 / $totalDesarrollos}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[1][3]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[1][3])}}D</div>
              <div class="progress-bar bg-precataloga" data-toggle="tooltip" data-html="true" title="Precatalogación <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[1][5])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[1][5]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[1][5] * 100 / $totalDesarrollos}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[1][5]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[1][5])}}D</div>
              <div class="progress-bar bg-cataloga" data-toggle="tooltip" data-html="true" title="Catalogación <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[1][4])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[1][4]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[1][4] * 100 / $totalDesarrollos}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[1][4]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[1][4])}}D</div>
            </div>
          </td>
        </tr>
        {{-- Arte con Material: --}}
        <tr class="row">
          @php $totalArte = 0; @endphp
          @foreach($solicitudesPorTipoSolicitudPorArea[5] as $semaforo)
          @php $totalArte += $semaforo @endphp
          @endforeach
          @php $totalArteDias = 0; @endphp
          @foreach($solicitudesPorTipoSolicitudPorAreaDias[5] as $key => $semaforo)
          @php $totalArteDias += $semaforo * $solicitudesPorTipoSolicitudPorArea[5][$key] @endphp
          @endforeach
          <td class="col-2 text-right" style="vertical-align: middle;margin-top: 10px;">Arte con Material: <b>{{$totalArte}} - @if($totalArte > 0){{number_format_unlimited_precision(round($totalArteDias / $totalArte,1))}}@else 0 @endif</b></td>
          <td class="col-10 text-left" style="padding: 0px 10px;">
            <div class="progress" style="height: 35px; margin-top: 5px; margin-bottom: 5px;">
              <div class="progress-bar bg-ventas" data-toggle="tooltip" data-html="true" title="Ventas <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[5][1])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[5][1]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[5][1] * 100 / $totalArte}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[5][1]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[5][1])}}D</div>
              <div class="progress-bar bg-ingenieria" data-toggle="tooltip" data-html="true" title="Diseño Estructural <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[5][2])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[5][2]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[5][2] * 100 / $totalArte}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[5][2]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[5][2])}}D</div>
              <div class="progress-bar bg-diseno" data-toggle="tooltip" data-html="true" title="Diseño Grafico <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[5][3])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[5][3]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[5][3] * 100 / $totalArte}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[5][3]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[5][3])}}D</div>
              <div class="progress-bar bg-precataloga" data-toggle="tooltip" data-html="true" title="Precatalogación <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[5][5])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[5][5]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[5][5] * 100 / $totalArte}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[5][5]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[5][5])}}D</div>
              <div class="progress-bar bg-cataloga" data-toggle="tooltip" data-html="true" title="Catalogación <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[5][4])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[5][4]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[5][4] * 100 / $totalArte}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[5][4]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[5][4])}}D</div>
            </div>
          </td>
        </tr>
        {{-- Cotiza Con Cad: --}}
        <tr class="row">
          @php $totalCotizaConCad = 0; @endphp
          @foreach($solicitudesPorTipoSolicitudPorArea[2] as $semaforo)
          @php $totalCotizaConCad += $semaforo @endphp
          @endforeach
          @php $totalCotizaConCadDias = 0; @endphp
          @foreach($solicitudesPorTipoSolicitudPorAreaDias[2] as $key => $semaforo)
          @php $totalCotizaConCadDias += $semaforo * $solicitudesPorTipoSolicitudPorArea[2][$key] @endphp
          @endforeach
          <td class="col-2 text-right" style="vertical-align: middle;margin-top: 10px;">Cotiza Con Cad: <b>{{$totalCotizaConCad}} - {{($totalCotizaConCad > 0) ? number_format_unlimited_precision(round($totalCotizaConCadDias / $totalCotizaConCad,1)): 0}}</b></td>
          <td class="col-10 text-left" style="padding: 0px 10px;">
            <div class="progress" style="height: 35px; margin-top: 5px; margin-bottom: 5px;">
              <div class="progress-bar bg-ventas" data-toggle="tooltip" data-html="true" title="Ventas  <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[2][1])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[2][1]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[2][1] * 100 / $totalCotizaConCad}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[2][1]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[2][1])}}D</div>
              <div class="progress-bar bg-ingenieria" data-toggle="tooltip" data-html="true" title="Diseño Estructural  <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[2][2])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[2][2]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[2][2] * 100 / $totalCotizaConCad}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[2][2]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[2][2])}}D</div>
              <div class="progress-bar bg-diseno" data-toggle="tooltip" data-html="true" title="Diseño Grafico  <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[2][3])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[2][3]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[2][3] * 100 / $totalCotizaConCad}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[2][3]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[2][3])}}D</div>
            </div>
          </td>
        </tr>
        {{-- Cotiza Sin Cad: --}}
        <tr class="row">
          @php $totalCotizaSinCad = 0; @endphp
          @foreach($solicitudesPorTipoSolicitudPorArea[4] as $semaforo)
          @php $totalCotizaSinCad += $semaforo @endphp
          @endforeach
          @php $totalCotizaSinCadDias = 0; @endphp
          @foreach($solicitudesPorTipoSolicitudPorAreaDias[4] as $key => $semaforo)
          @php $totalCotizaSinCadDias += $semaforo * $solicitudesPorTipoSolicitudPorArea[4][$key] @endphp
          @endforeach
          <td class="col-2 text-right" style="vertical-align: middle;margin-top: 10px;">Cotiza Sin Cad: <b>{{$totalCotizaSinCad}} - {{($totalCotizaSinCad > 0) ? number_format_unlimited_precision(round($totalCotizaSinCadDias / $totalCotizaSinCad,1)): 0}}</b></td>
          <td class="col-10 text-left" style="padding: 0px 10px;">
            <div class="progress" style="height: 35px; margin-top: 5px; margin-bottom: 5px;">
              <div class="progress-bar bg-ventas" data-toggle="tooltip" data-html="true" title="Ventas  <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[4][1])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[4][1]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[4][1] * 100 / $totalCotizaSinCad}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[4][1]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[4][1])}}D</div>
              <div class="progress-bar bg-ingenieria" data-toggle="tooltip" data-html="true" title="Diseño Estructural  <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[4][2])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[4][2]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[4][2] * 100 / $totalCotizaSinCad}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[4][2]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[4][2])}}D</div>
              <div class="progress-bar bg-diseno" data-toggle="tooltip" data-html="true" title="Diseño Grafico  <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[4][3])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[4][3]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[4][3] * 100 / $totalCotizaSinCad}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[4][3]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[4][3])}}D</div>
            </div>
          </td>
        </tr>
        {{-- Muestra Con Cad: --}}
        <tr class="row">
          @php $totalMuestraConCad = 0; @endphp
          @foreach($solicitudesPorTipoSolicitudPorArea[3] as $semaforo)
          @php $totalMuestraConCad += $semaforo @endphp
          @endforeach
          @php $totalMuestraConCadDias = 0; @endphp
          @foreach($solicitudesPorTipoSolicitudPorAreaDias[3] as $key => $semaforo)
          @php $totalMuestraConCadDias += $semaforo * $solicitudesPorTipoSolicitudPorArea[3][$key] @endphp
          @endforeach
          <td class="col-2 text-right" style="vertical-align: middle;margin-top: 10px;">Muestra Con Cad: <b>{{$totalMuestraConCad}} - {{($totalMuestraConCad > 0) ? number_format_unlimited_precision(round($totalMuestraConCadDias / $totalMuestraConCad,1)): 0}}</b></td>
          <td class="col-10 text-left" style="padding: 0px 10px;">
            <div class="progress" style="height: 35px; margin-top: 5px; margin-bottom: 5px;">
              <div class="progress-bar bg-ventas" data-toggle="tooltip" data-html="true" title="Ventas  <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[3][1])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[3][1]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[3][1] * 100 / $totalMuestraConCad}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[3][1]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[3][1])}}D</div>
              <div class="progress-bar bg-ingenieria" data-toggle="tooltip" data-html="true" title="Diseño Estructural  <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[3][2])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[3][2]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[3][2] * 100 / $totalMuestraConCad}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[3][2]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[3][2])}}D</div>
              <div class="progress-bar bg-diseno" data-toggle="tooltip" data-html="true" title="Diseño Grafico  <br> {{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[3][3])}}D" role="progressbar" @if($solicitudesPorTipoSolicitudPorArea[3][3]> 0) style="width:{{$solicitudesPorTipoSolicitudPorArea[3][3] * 100 / $totalMuestraConCad}}%;" @else style="width:0%;display:none;" @endif>{{$solicitudesPorTipoSolicitudPorArea[3][3]}}<br>{{number_format_unlimited_precision($solicitudesPorTipoSolicitudPorAreaDias[3][3])}}D</div>
            </div>
          </td>
        </tr>
        {{-- leyenda: --}}
        <tr class="row">
          <td class="text-center" colspan="2" style="    margin: 0 auto;">
            <ul class="legend">
              <li><span class="ventas"></span> Ventas</li>
              <li><span class="ingenieria"></span> Diseño Estructural</li>
              <li><span class="diseno"></span> Diseño Gráfico</li>
              <li><span class="precataloga"></span> Precatalogación</li>
              <li><span class="cataloga"></span> Catalogacion</li>
            </ul>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
  */ ?>

{{-- graficos por dias: --}}
<div class="container-report">
  <div class="item-report item-report_sm col-6" style="height: auto;">
    <canvas id="myChart3" height="150"></canvas>
  </div>
  <div class="item-report item-report_sm col-6" style="height: auto;">
    <canvas id="myChart4" height="150"></canvas>
  </div>
</div>


<div class="container-report">
  <div class="item-report " style="height:auto;z-index: 1;">
    <h5 class="header-report">Top Gestiones Activas Vendedores </h5>
    @if(count($top_responsables) == 0)
    <div class="text-center py-2">No se encontraron ots para el mes seleccionado</div>
    @else
    <div class="container-solicitud" style="margin-bottom: 10px;margin-top: 10px;">
      <div class="ot">
        <p style="    text-transform: uppercase;
    font-weight: 900;">Nombre</p>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Cantidad Gestiones Activas" data-toggle="tooltip">G</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Tiempo Promedio desde que está en Ventas" data-toggle="tooltip">TPV</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Tiempo Promedio Total OT" data-toggle="tooltip">TPT</div>
      </div>
    </div>
    @endif
    @foreach($top_responsables as $responsable)
    <div class="container-solicitud">
      <div class="ot">
        <p>{{$responsable->fullname}}</p>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Cantidad Gestiones Activas" data-toggle="tooltip">{{$responsable->total_ots}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Tiempo Promedio desde que está en Ventas" data-toggle="tooltip">{{str_replace(".", ",", $responsable->tiempo_promedio_venta)}} D</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Tiempo Promedio Total OT" data-toggle="tooltip">{{str_replace(".", ",", $responsable->tiempo_promedio_total)}} D</div>
      </div>
    </div>
    <div class="division"></div>
    @endforeach
  </div>
  <div class="item-report item-report_sm col-8" style="height: auto;z-index: 1">
    <div class="container-report row">
      <div class="col-12 text-center mb-4">
        <ul class="legend">
          <li data-toggle="tooltip" data-html="true" title="Proceso de Ventas: <br> Acción pendiente del vendedor, Ejemplo prueba industrial, liberación de crédito, reunión con cliente, etc"><span style="background-color: #60BD68;"></span>Proceso de Ventas</li>
          <li data-toggle="tooltip" data-html="true" title="Consulta Cliente: <br> Algún dato que debe confirmar el cliente para seguir con el desarrollo"><span style="background-color: #5DA5DA;"></span> Consulta Cliente</li>
          <li data-toggle="tooltip" data-html="true" title="Rechazada: <br> Devolución de una OT a ventas por otras áreas por no conformidad"><span style="background-color: #F15854;"></span> Rechazada</li>
          <li data-toggle="tooltip" data-html="true" title="Espera OC:  <br> Espera Orden de compra del cliente"><span style="background-color: #FAA43A;"></span> Espera de OC</li>
          <li data-toggle="tooltip" data-html="true" title="Definicion Cliente: <br> Desarrollo en su etapa final, falta definición del cliente (fecha de compra)"><span style="background-color: #806939;"></span> Falta definición del Cliente</li>
          <li data-toggle="tooltip" data-html="true" title="V°B° Cliente: <br> Visto bueno del cliente. Ejemplo el ok del arte por marketing"><span style="background-color: #73e2e6;"></span> Visto Bueno Cliente</li>
        </ul>
      </div>
      @foreach($responsables as $responsable)
      <div class="col-3 mb-2">
        <canvas id="myChartVendedor{{$responsable->id}}" height="300"></canvas>
      </div>
      @endforeach
    </div>
  </div>
</div>


@endsection
@section('myjsfile')
<!-- chartjs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<!-- gauge js -->
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

    var estados = [
      ["Proceso de", "Ventas"],
      ["Consulta", "Cliente"],
      ["Rechazada"],
      ["Espera de", "OC"],
      ["Falta", "Definición", "del Cliente"],
      ["Visto", "Bueno", "Cliente"]
    ];
    var cantidadPorEstado = @json($cantidadPorEstado);
    var tiempoPromedioPorEstado = @json($tiempoPromedioPorEstado);
    generar_reporte_secundario_gestiones_activas(estados, cantidadPorEstado, tiempoPromedioPorEstado);

    var responsables = @json($responsables);
    generar_reporte_estados_por_vendedor(estados, responsables);
  });
</script>
@endsection
