@extends('layouts.index')

@section('content')
<div class="container-fluid">
  <h1 class="page-title">Motivos Rechazos
    <a href="{{ route('reportRechazos') }}" class="btn btn-primary rounded-pill ml-3 px-5">Ir a Entre Fechas</a>
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

  .legend {
    list-style: none;
    display: inline-block;
    text-align: center;
    /* padding-top: 10px; */
    /* width: 100%; */
  }

  .legend li {
    float: left;
    margin-right: 10px;
    font-size: 12px;
    /* display: flex;
    align-items: center; */
  }

  .legend span {
    border: 1px solid #fff;
    float: left;
    width: 30px;
    height: 15px;
    margin: 2px;
    border-radius: 3px;
  }

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
  <form id="filtroReporte" class="filter-form py-1" action="{{ route('reportRechazosPorMesNew') }}" method="get" enctype="multipart/form-data">
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
      <div class="col-2">
        <div class="form-group">
          <label>Creador</label>
          <select name="vendedor_id[]" id="vendedor_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
            {!! optionsSelectObjetfilterMultiple($vendedores,'vendedor_id',['nombre','apellido'],' ') !!}
          </select>
          <input id="user_id" type="text" hidden value="{{(is_null(app('request')->input('vendedor_id')))? auth()->user()->id : null}}">
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

{{-- graficos por cantidad: --}}
<div class="container-report">
  <div class="item-report item-report_sm col-12" style="height: auto;">
    <canvas id="myChart" height="400"></canvas>
  </div>
</div>

<div class="container-report">
  <div class="item-report item-report_sm col-12" style="height: auto;">
    <ul class="legend">
      <li data-toggle="tooltip" data-html="true" title="Falta Muestra Física: <br> No se entrega muestra física como referencia para continuar el desarrollo"><span class="ventas2"></span> Falta Muestra Fisica</li>
      <li data-toggle="tooltip" data-html="true" title="Formato Imagen Inadecuado: <br> No es posible realizar el boceto (ej: imagen pixelada)"><span class="ingenieria2"></span> Formato Imagen Inadecuado</li>
      <li data-toggle="tooltip" data-html="true" title="Información Errónea: <br> Los datos de la OT no concuerdan (Ej: Solicitan BCT y es una caja WA)"><span class="diseno2"></span> Informacion Erronea</li>
      <li data-toggle="tooltip" data-html="true" title="Medida Errónea: Medidas <br> inconsistentes (Ej: mal digitado el número)"><span class="precataloga2"></span> Medida Erronea</li>
      <li data-toggle="tooltip" data-html="true" title="No viable por Restricciones: <br> Por restricciones productivas no es posible fabricar la caja"><span class="cataloga2"></span> No Viable Por Restricciones</li>
      <li data-toggle="tooltip" data-html="true" title="Descripción de Producto: <br> El nombre no corresponde a lo solicitado por el cliente (Ej: pasa una referencia y no incluyen código SAP)"><span class="ventas"></span> Descripción de Producto</li>
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
    var faltaInformacion = @json($faltaInformacion);
    var informacionErronea = @json($informacionErronea);
    var faltaMuestraFisica = @json($faltaMuestraFisica);
    var formatoImagenInadecuado = @json($formatoImagenInadecuado);
    var medidaErronea = @json($medidaErronea);
    var descripcionDeProducto = @json($descripcionDeProducto);
    var planoMalAcotado = @json($planoMalAcotado);
    var errorDeDigitacion = @json($errorDeDigitacion);
    var errorTipoSustrato = @json($errorTipoSustrato);
    var noViablePorRestricciones = @json($noViablePorRestricciones);
    var faltaCadParaCorte = @json($faltaCadParaCorte);
    var faltaOTChileexpress = @json($faltaOTChileexpress);
    var faltaOTLaboratorio = @json($faltaOTLaboratorio);
    // contruir reportes:
    // generar reporte por cantidad:
    generar_reporte_rechazos(mesesSeleccionados, faltaInformacion, informacionErronea, faltaMuestraFisica, formatoImagenInadecuado, medidaErronea, descripcionDeProducto, planoMalAcotado, errorDeDigitacion, errorTipoSustrato, noViablePorRestricciones, faltaCadParaCorte, faltaOTChileexpress, faltaOTLaboratorio);
  });
</script>
@endsection
