@extends('layouts.index')

@section('content')
<div class="container-fluid">
  <h1 class="page-title">Motivos Rechazos
    <a href="{{ route('reportRechazosPorMes') }}" class="btn btn-primary rounded-pill ml-3 px-5">Ir a Meses</a>
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

  .bg-ventas {
    background-color: #FAA43A;
    color: white;
  }

  .bg-ingenieria {
    background-color: #6e6e6e;
    color: white;
  }

  .bg-diseno {
    background-color: #F17CB0;
    color: white;
  }

  .bg-cataloga {
    background-color: #60BD68;
    color: black;
  }

  .bg-ventas2 {
    background-color: #806939;
    color: white;
  }

  .bg-ingenieria2 {
    background-color: #73e2e6;
    color: white;
  }

  .bg-diseno2 {
    background-color: #5DA5DA;
    color: white;
  }

  .bg-precataloga2 {
    background-color: #DECF3F;
    color: black;
  }

  .bg-cataloga2 {
    background-color: #F15854;
    color: black;
  }

  .bg-cataloga3 {
    background-color: #a668f2;
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
    font-size: 15px;
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

  /* your colors */
  .legend .ventas {
    background-color: #FAA43A;
  }

  .legend .ingenieria {
    background-color: #6e6e6e;
  }

  .legend .diseno {
    background-color: #F17CB0;
  }

  .legend .cataloga {
    background-color: #60BD68;
  }

  .legend .faltaCad {
    background-color: #3f95a6;
  }

  .legend .faltaOT1 {
    background-color: #7b36e3;
  }

  .legend .faltaOT2 {
    background-color: #bd6b1e;
  }

  .legend .ventas2 {
    background-color: #806939;
  }

  .legend .ingenieria2 {
    background-color: #73e2e6;
  }

  .legend .diseno2 {
    background-color: #5DA5DA;
  }

  .legend .precataloga2 {
    background-color: #DECF3F;
  }

  .legend .cataloga2 {
    background-color: #F15854;
  }

  .legend .cataloga3 {
    background-color: #a668f2;
  }
</style>

<div class="container-fluid">
  <form id="filtroReporteRechazo" class="filter-form py-1" action="{{ route('reportRechazosNew1') }}" method="get" enctype="multipart/form-data">
    <div class="form-row">
      <div class="col-1">
        <div class="form-group">
          <label>Desde</label>
          <input class="form-control form-control-sm datepicker" id="datePicker1" name="date_desde" value="{{ (is_null(app('request')->input('date_desde')))? $fromDate : app('request')->input('date_desde') }}" autocomplete="off">
        </div>
      </div>
      <div class="col-1">
        <div class="form-group">
          <label>Hasta</label>
          <input class="form-control form-control-sm datepicker" id="datePicker2" name="date_hasta" value="{{ (is_null(app('request')->input('date_hasta')))? $toDate : app('request')->input('date_hasta') }}" autocomplete="off">
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
      <!--<div class="col-2">
        <div class="form-group">
          <label>Cliente</label>
          <select name="client_id[]" id="client_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
            {!! optionsSelectObjetfilterMultiple($clients,'client_id',['nombre','apellido'],' ') !!}
          </select>
        </div>
      </div> -->
    </div>
    <div class="text-right">
      <button id="exportarSubmit" class="ml-auto btn btn-light col-2" style="background-color: #ccc;">Exportar</button>
      <button id="filtrarSubmit" class="ml-auto btn btn-primary col-2">Filtrar</button>
      <!-- <button id="filtrarSubmit" class="sbtn submit">Buscar</button> -->
      <!-- este inpurt preserva el valor para poder exportar -->
      <input hidden id="exportar" name="exportar" value="">
    </div>
  </form>
</div>


{{-- graficos por dias: --}}
<div class="container-report">
  <div class="item-report item-report_sm col-5" style="height: auto;">
    {{-- Motivo: principal--}}
    <canvas id="myChart" height="300" style="margin-top: 10%;"></canvas>
  </div>
  <div class="item-report item-report_sm col-7" style="height: auto;">
    <div class="container-report col-12">
      <div class="col-4">
        {{-- Motivo: --}}
        <canvas id="myChart1" height="300"></canvas>
      </div>
      <div class="col-4">
        {{-- Motivo: --}}
        <canvas id="myChart10" height="300"></canvas>
      </div>
      <div class="col-4">
        {{-- Motivo: --}}
        <canvas id="myChart11" height="300"></canvas>
      </div>

    </div>

    <div class="container-report col-12">
      <div class="col-4">
        {{-- Motivo: --}}
        <canvas id="myChart2" height="300"></canvas>
      </div>
      <div class="col-4">
        {{-- Motivo: --}}
        <canvas id="myChart3" height="300"></canvas>
      </div>
      <div class="col-4">
        {{-- Motivo: --}}
        <canvas id="myChart4" height="300"></canvas>
      </div>

    </div>

    <div class="container-report col-12">
      <div class="col-4">
        {{-- Motivo: --}}
        <canvas id="myChart5" height="300"></canvas>
      </div>
      <div class="col-4">
        {{-- Motivo: --}}
        <canvas id="myChart6" height="300"></canvas>
      </div>
      <div class="col-4">
        {{-- Motivo: --}}
        <canvas id="myChart7" height="300"></canvas>
      </div>

    </div>
    <div class="container-report col-12">
      <div class="col-4">
        {{-- Motivo: --}}
        <canvas id="myChart8" height="300"></canvas>
      </div>
      <div class="col-4">
        {{-- Motivo: --}}
        <canvas id="myChart9" height="300"></canvas>
      </div>
      <div class="col-4">
      </div>
    </div>
  </div>
</div>

{{-- leyendas: --}}
<div class="container-report">
  <div class="item-report item-report_sm col-12" style="height: auto;">
    <ul class="legend">
      <li data-toggle="tooltip" data-html="true" title="Falta Muestra Física: <br> No se entrega muestra física como referencia para continuar el desarrollo"><span class="ventas2"></span> Falta Muestra Fisica</li>
      <li data-toggle="tooltip" data-html="true" title="Formato Imagen Inadecuado: <br> No es posible realizar el boceto (ej: imagen pixelada)"><span class="ingenieria2"></span> Formato Imagen Inadecuado</li>
      <li data-toggle="tooltip" data-html="true" title="Información Errónea: <br> Los datos de la OT no concuerdan (Ej: Solicitan BCT y es una caja WA)"><span class="diseno2"></span> Informacion Erronea</li>
      <li data-toggle="tooltip" data-html="true" title="Medida Errónea: <br> Medidas inconsistentes (Ej: mal digitado el número)

"><span class="precataloga2"></span> Medida Erronea</li>
      <li data-toggle="tooltip" data-html="true" title="No viable por Restricciones: <br> Por restricciones productivas no es posible fabricar la caja"><span class="cataloga2"></span> No Viable Por Restricciones</li>
      <li data-toggle="tooltip" data-html="true" title="Descripción de Producto: <br> El nombre no corresponde a lo solicitado por el cliente (Ej: pasa una referencia y no incluyen código SAP)"><span class="ventas"></span> Descripción de Producto</li>
      <br>
      <li data-toggle="tooltip" data-html="true" title="Error de Digitacion: <br> sin comentarios (Ej: Cliente mal determinado) "><span class="ingenieria"></span> Error de Digitación</li>
      <li data-toggle="tooltip" data-html="true" title="Error Tipo de Sustrato: <br> Solicitan cartón café y el arte lo adjuntan en blanco pero sin explicar en observaciones "><span class="diseno"></span> Error Tipo Sustrato</li>
      <li data-toggle="tooltip" data-html="true" title="Plano mal Acotado: <br> Distancias no coinciden con lo solicitado (Ej: esto pasa generalmente cuando piden distancia de agujeros y no es lo solicitado)"><span class="cataloga3"></span> Plano Mal Acotado</li>
      <li data-toggle="tooltip" data-html="true" title="Falta de Información: <br> Con los datos ingresados no se puede desarrollar"><span class="cataloga"></span> Falta Informacion </li>

      <li data-toggle="tooltip" data-html="true" title="Falta CAD para corte: <br> Falta CAD para corte"><span class="faltaCad"></span>Falta CAD para corte</li>
      <li data-toggle="tooltip" data-html="true" title="Falta OT Chileexpress: <br> Falta OT Chileexpress"><span class="faltaOT1"></span>Falta OT Chileexpress</li>
      <li data-toggle="tooltip" data-html="true" title="Falta OT Laboratorio: <br> Falta OT Laboratorio "><span class="faltaOT2"></span>Falta OT Laboratorio</li>

    </ul>
  </div>
</div>
<br>
<h5 class="header-report">TOP Usuarios con más Rechazos Recibidos</h5>
<div class="container-report">
  <div class="item-reporte1 ">
    <h5 class="header-report">Vendedores</h5>
    @if(count($responsablesVentas) == 0)
    <div class="text-center py-2">No se encontraron rechazos</div>
    @else
    <div class="container-solicitud" style="margin-bottom: 10px;margin-top: 10px;">
      <div class="ot">
        <p style="    text-transform: uppercase;
    font-weight: 900;">Nombre</p>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Cantidad Rechazos" data-toggle="tooltip">R</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Cantidad Órdenes de Trabajo" data-toggle="tooltip">OT</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Tiempo Total Respuesta Rechazos" data-toggle="tooltip">TT</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Tiempo Promedio Respuesta Rechazos" data-toggle="tooltip">TP</div>
      </div>
    </div>
    @endif
    @foreach($responsablesVentas as $responsable)
    <div class="container-solicitud">
      <div class="ot">
        <p>{{$responsable->fullname}}</p>
      </div>
      <div class="columna-report" style="border-left: 1px solid black;">
        <div class="text-center" title="Cantidad Rechazos" data-toggle="tooltip">{{$responsable->rechazos}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Cantidad Órdenes de Trabajo" data-toggle="tooltip">{{count($responsable->ots)}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Tiempo Total Respuesta Rechazos" data-toggle="tooltip">{{ str_replace(".",",",round($responsable->tiempo,1)) }} D</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Tiempo Promedio Respuesta Rechazos" data-toggle="tooltip">{{ ($responsable->tiempo >0  && $responsable->rechazos > 0) ? str_replace(".",",",round($responsable->tiempo/$responsable->rechazos,1)) : 0 }} D</div>
      </div>
    </div>
    <div class="division"></div>
    @endforeach
  </div>
  <div class="item-reporte1 ">
    <h5 class="header-report">Dibujante Técnico</h5>
    @if(count($responsablesDesarrollo) == 0)
    <div class="text-center py-2">No se encontraron rechazos</div>
    @else
    <div class="container-solicitud" style="margin-bottom: 10px;margin-top: 10px;">
      <div class="ot">
        <p style="    text-transform: uppercase;
    font-weight: 900;">Nombre</p>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Cantidad Rechazos" data-toggle="tooltip">R</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Cantidad Órdenes de Trabajo" data-toggle="tooltip">OT</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Tiempo Total Respuesta Rechazos" data-toggle="tooltip">TT</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Tiempo Promedio Respuesta Rechazos" data-toggle="tooltip">TP</div>
      </div>
    </div>
    @endif
    @foreach($responsablesDesarrollo as $responsable)
    <div class="container-solicitud">
      <div class="ot">
        <p>{{$responsable->fullname}}</p>
      </div>
      <div class="columna-report" style="border-left: 1px solid black;">
        <div class="text-center" title="Cantidad Rechazos" data-toggle="tooltip">{{$responsable->rechazos}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Cantidad Órdenes de Trabajo" data-toggle="tooltip">{{count($responsable->ots)}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Tiempo Total Respuesta Rechazos" data-toggle="tooltip">{{ str_replace(".",",",round($responsable->tiempo,1)) }} D</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Tiempo Promedio Respuesta Rechazos" data-toggle="tooltip">{{ ($responsable->tiempo >0  && $responsable->rechazos > 0) ? str_replace(".",",",round($responsable->tiempo/$responsable->rechazos,1)) : 0 }} D</div>
      </div>
    </div>
    <div class="division"></div>
    @endforeach
  </div>
  <div class="item-reporte1 ">
    <h5 class="header-report">Diseñadores Graficos</h5>
    @if(count($responsablesDiseño) == 0)
    <div class="text-center py-2">No se encontraron rechazos</div>
    @else
    <div class="container-solicitud" style="margin-bottom: 10px;margin-top: 10px;">
      <div class="ot">
        <p style="    text-transform: uppercase;
    font-weight: 900;">Nombre</p>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Cantidad Rechazos" data-toggle="tooltip">R</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Cantidad Órdenes de Trabajo" data-toggle="tooltip">OT</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Tiempo Total Respuesta Rechazos" data-toggle="tooltip">TT</div>
      </div>
      <div class="dias" style="background-color:#3aaa35 ;">
        <div class="text-center" title="Tiempo Promedio Respuesta Rechazos" data-toggle="tooltip">TP</div>
      </div>
    </div>
    @endif
    @foreach($responsablesDiseño as $responsable)
    <div class="container-solicitud">
      <div class="ot">
        <p>{{$responsable->fullname}}</p>
      </div>
      <div class="columna-report" style="border-left: 1px solid black;">
        <div class="text-center" title="Cantidad Rechazos" data-toggle="tooltip">{{$responsable->rechazos}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Cantidad Órdenes de Trabajo" data-toggle="tooltip">{{count($responsable->ots)}}</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Tiempo Total Respuesta Rechazos" data-toggle="tooltip">{{ str_replace(".",",",round($responsable->tiempo,1)) }} D</div>
      </div>
      <div class="columna-report">
        <div class="text-center" title="Tiempo Promedio Respuesta Rechazos" data-toggle="tooltip">{{ ($responsable->tiempo >0  && $responsable->rechazos > 0) ? str_replace(".",",",round($responsable->tiempo/$responsable->rechazos,1)) : 0 }} D</div>
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
    $(document).on('click', '#exportarSubmit', function(e) {
      e.preventDefault();
      document.getElementById('exportar').value = "Si";
      $('#filtroReporteRechazo').submit();
    });
    $(document).on('click', '#filtrarSubmit', function(e) {
      e.preventDefault();
      document.getElementById('exportar').value = "";
      $('#filtroReporteRechazo').submit();
    });

    // contruir reportes:
    // generar reporte por area:

    var motivosCompletos = @json($motivosCompletos);
    var motivosIngenieriaAVentas = @json($motivosIngenieriaAVentas);
    var motivosIngenieriaAMuestras = @json($motivosIngenieriaAMuestras);
    var motivosMuestrasAIngenieria = @json($motivosMuestrasAIngenieria);
    var motivosDiseñoAVentas = @json($motivosDiseñoAVentas);
    var motivosDiseñoAIngenieria = @json($motivosDiseñoAIngenieria);
    var motivosCatalogacionAVentas = @json($motivosCatalogacionAVentas);
    var motivosCatalogacionAIngenieria = @json($motivosCatalogacionAIngenieria);
    var motivosCatalogacionADiseño = @json($motivosCatalogacionADiseño);
    var motivosPrecatalogacionAVentas = @json($motivosPrecatalogacionAVentas);
    var motivosPrecatalogacionAIngenieria = @json($motivosPrecatalogacionAIngenieria);
    var motivosPrecatalogacionADiseño = @json($motivosPrecatalogacionADiseño);
    generar_reporte_motivos_rechazos_por_area_ot_por_mes(motivosCompletos, motivosIngenieriaAVentas, motivosIngenieriaAMuestras, motivosMuestrasAIngenieria, motivosDiseñoAVentas, motivosDiseñoAIngenieria, motivosCatalogacionAVentas, motivosCatalogacionAIngenieria, motivosCatalogacionADiseño, motivosPrecatalogacionAVentas, motivosPrecatalogacionAIngenieria, motivosPrecatalogacionADiseño);

  });
</script>
@endsection
