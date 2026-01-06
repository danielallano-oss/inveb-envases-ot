@extends('layouts.index')

@section('content')
<div class="container-fluid">
  <h1 class="page-title">OTs Activas Por Usuario</h1>
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

  .legenda {
    height: 100%;
    background-color: white;
    border-radius: 10px;
    box-shadow: 1px 1px 60px 1px rgba(0, 0, 0, 0.2);
    padding: 10px;

  }

  .legenda-item {
    padding: 5px;
  }

  .legenda span {
    border: 1px solid #fff;
    float: left;
    width: 35px;
    height: 15px;
    margin: 2px;
    /* border-radius: 20px; */
  }

  .item-report {
    padding: 2px 15px;
  }
</style>

<div class="container-fluid">
  <form id="filtroReporte" class="filter-form py-1" action="{{ route('reportActiveOtsPerAreaNew1') }}" method="get" enctype="multipart/form-data">
    <div class="form-row">

      <div class="col-2">
        <div class="form-group">
          <label>Área</label>
          <select name="area_id[]" id="area_id" class="form-control form-control-sm" data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
            {!! optionsSelectObjetfilterMultiple($areas,'area_id',['nombre'],' ') !!}
          </select>
        </div>
      </div>
      <div class="col-2">
        <div class="form-group">
          <label>Usuarios</label>
          <select name="user_id[]" id="user_id" class="form-control form-control-sm" multiple data-live-search="true" title="Selecciona..." data-selected-text-format="count > 1">
            {!! optionsSelectObjetfilterMultiple($users,'user_id',['fullname',],' ') !!}
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
<div class="row">
  <div class="col-10">
    {{-- graficos cantidad solicitudes asignadas: --}}
    <div class="container-report">
      <div class="item-report item-report_sm col-3" style="height: auto;">
        <canvas id="myChart" height="200"></canvas>
      </div>
      <div class="item-report item-report_sm col-9" style="height: auto;">
        <canvas id="myChart2" height="70"></canvas>
      </div>
    </div>

    {{-- graficos cantidad solicitudes asignadas en area: --}}
    <div class="container-report">
      <div class="item-report item-report_sm col-3" style="height: auto;">
        <canvas id="myChart3" height="200"></canvas>
      </div>
      <div class="item-report item-report_sm col-9" style="height: auto;">
        <canvas id="myChart4" height="70"></canvas>
      </div>
    </div>

    {{-- graficos tiempos promedios solicitudes asignadas: --}}
    <div class="container-report">
      <div class="item-report item-report_sm col-3" style="height: auto;">
        <canvas id="myChart9" height="200"></canvas>
      </div>
      <div class="item-report item-report_sm col-9" style="height: auto;">
        <canvas id="myChart10" height="70"></canvas>
      </div>
    </div>
    {{-- graficos tiempos promedios solicitudes asignadas en area: --}}
    <div class="container-report">
      <div class="item-report item-report_sm col-3" style="height: auto;">
        <canvas id="myChart11" height="200"></canvas>
      </div>
      <div class="item-report item-report_sm col-9" style="height: auto;">
        <canvas id="myChart12" height="70"></canvas>
      </div>
    </div>
    {{-- graficos tiempos solicitudes asignadas: --}}
    <div class="container-report">
      <div class="item-report item-report_sm col-3" style="height: auto;">
        <canvas id="myChart5" height="200"></canvas>
      </div>
      <div class="item-report item-report_sm col-9" style="height: auto;">
        <canvas id="myChart6" height="70"></canvas>
      </div>
    </div>
    {{-- graficos tiempos solicitudes asignadas en area: --}}
    <div class="container-report">
      <div class="item-report item-report_sm col-3" style="height: auto;">
        <canvas id="myChart7" height="200"></canvas>
      </div>
      <div class="item-report item-report_sm col-9" style="height: auto;">
        <canvas id="myChart8" height="70"></canvas>
      </div>
    </div>

  </div>
  <div class="col-2" style="padding: 0px;
    margin-left: -7px;">
    <div class="legenda">
      <h5 style="margin: 15px 0px;text-align: center; ">Leyenda</h5>
      @foreach($responsablesArea as $responsable)
      <div class="legenda-item"><span style="background-color: {{$colores[$count]}};"></span>{{$responsable->fullname}} </div>
      @php $count++; @endphp
      @if($count >= 10)
      @php $count=0; @endphp
      @endif
      @endforeach
    </div>
  </div>
</div>

<input hidden id="area_actual" name="area_actual" value="{{$area_actual}}">
<input hidden id="users_actual" name="users_actual" value="">
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

    var responsablesArea = @json($responsablesArea);
    console.log(responsablesArea);
    var colores = ["#806939", "#73e2e6", "#5DA5DA", "#DECF3F", "#FAA43A", "#6e6e6e", "#F17CB0", "#a668f2", "#60BD68", "#F15854"]
    // responsables es un objeto asi q iteramos en el para armar el dataset del reporte
    var otsAsignadasPorUsuario = [];
    var otsAsignadasEnAreaPorUsuario = [];
    var tiempoOtsAsignadasPorUsuario = [];
    var tiempoOtsAsignadasEnAreaPorUsuario = [];
    var tiempoPromedioOtsAsignadasPorUsuario = [];
    var tiempoPromedioOtsAsignadasEnAreaPorUsuario = [];
    var count = 0;
    for (const responsable in responsablesArea) {
      if (responsablesArea.hasOwnProperty(responsable)) {
        const user = responsablesArea[responsable];
        otsAsignadasPorUsuario.push({
          label: user.fullname,
          backgroundColor: colores[count],
          borderColor: colores[count],
          data: [user.ots_asignadas],
        }, )
        otsAsignadasEnAreaPorUsuario.push({
          label: user.fullname,
          backgroundColor: colores[count],
          borderColor: colores[count],
          data: [user.ots_asignadas_en_area],
        }, )
        tiempoOtsAsignadasPorUsuario.push({
          label: user.fullname,
          backgroundColor: colores[count],
          borderColor: colores[count],
          data: [user.tiempo_ots_asignadas],
        }, )
        tiempoOtsAsignadasEnAreaPorUsuario.push({
          label: user.fullname,
          backgroundColor: colores[count],
          borderColor: colores[count],
          data: [user.tiempo_ots_asignadas_en_area],
        }, )

        tiempoPromedioOtsAsignadasPorUsuario.push({
          label: user.fullname,
          backgroundColor: colores[count],
          borderColor: colores[count],
          data: [user.tiempo_promedio_ots_asignadas],
        }, )
        tiempoPromedioOtsAsignadasEnAreaPorUsuario.push({
          label: user.fullname,
          backgroundColor: colores[count],
          borderColor: colores[count],
          data: [user.tiempo_promedio_ots_asignadas_en_area],
        }, )
        count++;
        if (count >= 10) {
          count = 0;
        }
      }
    }
    console.log(otsAsignadasPorUsuario);


    // generar reporte por cantidad de solicitudes asignadas:
    var totalSolicitudesAsignadasAlArea = @json($totalSolicitudesAsignadasAlArea);

    // contruir reportes:
    generar_reporte_ots_activas_por_area_cantidad(totalSolicitudesAsignadasAlArea, otsAsignadasPorUsuario);


    // generar reporte por solicitudes en area actual:

    var totalSolicitudesEnArea = @json($totalSolicitudesEnArea);
    generar_reporte_ots_activas_por_area_y_usuario(totalSolicitudesEnArea, otsAsignadasEnAreaPorUsuario);

    // generar reporte tiempos por solicitudes asignadas

    var tiempoSolicitudesAsignadasAlArea = @json($tiempoSolicitudesAsignadasAlArea);
    generar_reporte_ots_activas_tiempos_solicitudes_asignadas(tiempoSolicitudesAsignadasAlArea, tiempoOtsAsignadasPorUsuario);

    // generar reporte tiempos por solicitudes asignadas por area
    var tiempoSolicitudesEnArea = @json($tiempoSolicitudesEnArea);
    generar_reporte_ots_activas_tiempos_solicitudes_asignadas_en_area(tiempoSolicitudesEnArea, tiempoOtsAsignadasEnAreaPorUsuario);


    // generar reporte tiempos promedios por solicitudes asignadas

    var tiempoPromedioSolicitudesAsignadasAlArea = @json($tiempoPromedioSolicitudesAsignadasAlArea);
    generar_reporte_ots_activas_tiempos_promedio_solicitudes_asignadas(tiempoPromedioSolicitudesAsignadasAlArea, tiempoPromedioOtsAsignadasPorUsuario);

    // generar reporte tiempos promedios por solicitudes asignadas por area
    var tiempoPromedioSolicitudesEnArea = @json($tiempoPromedioSolicitudesEnArea);
    generar_reporte_ots_activas_tiempos_promedio_solicitudes_asignadas_en_area(tiempoPromedioSolicitudesEnArea, tiempoPromedioOtsAsignadasEnAreaPorUsuario);

    // Cargar usuarios segun area seleccionada
    $("#area_id").change(function() {
      var val = $(this).val();
      return $.ajax({
        type: "GET",
        url: "/getUsersByArea",
        data: "area_id=" + val,
        success: function(data) {
          data = $.parseHTML(data);
          // console.log(data);
          $("#user_id")
            .empty()
            .append(data)
            .selectpicker("refresh");

          $('#user_id option').attr("selected", "selected");
          $('#user_id').selectpicker('refresh');

        },
      });
    });

    //  $("#area_id").val($("#area_actual").val()).selectpicker("refresh").triggerHandler("change");

    // var usuarios = @json($users_actual);
    // $('#user_id').val(usuarios).selectpicker('refresh');

    $.when($("#area_id").val($("#area_actual").val()).selectpicker("refresh").triggerHandler("change")).then(function() {
      var usuarios = @json($users_actual);
      if (usuarios.length > 0) {
        // console.log(usuarios);
        $('#user_id').val(usuarios).selectpicker('refresh');
      }
    });

  });
</script>
@endsection
