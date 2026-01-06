@extends('layouts.index')

@section('content')
<div class="container-fluid">
  <h1 class="page-title">Muestras Pendientes</h1>
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
  <form id="filtroReporte" class="filter-form py-1" action="{{ route('reportMuestrasNew1') }}" method="get" enctype="multipart/form-data">
    <div class="form-row">
      <div class="col-2">
        <div class="form-group">
          <label>Desde</label>
          <input class="form-control form-control-sm datepicker" id="datePicker1" name="date_desde" value="{{ (is_null(app('request')->input('date_desde')))? $fromDate : app('request')->input('date_desde') }}" autocomplete="off">
        </div>
      </div>
      <div class="col-2">
        <div class="form-group">
          <label>Hasta</label>
          <input class="form-control form-control-sm datepicker" id="datePicker2" name="date_hasta" value="{{ (is_null(app('request')->input('date_hasta')))? $toDate  : app('request')->input('date_hasta') }}" autocomplete="off">
        </div>
      </div>
    </div>
    <div class="text-left">
      <button id="exportarSubmit" class="ml-auto btn btn-light col-2" style="    background-color: #ccc;">Exportar</button>
      <!-- <button id="filtrarSubmit" class="ml-auto btn btn-primary col-2">Filtrar</button> -->
      <!-- <button id="filtrarSubmit" class="sbtn submit">Buscar</button> -->
      <!-- este inpurt preserva el valor para poder exportar -->
      <input hidden id="exportar" name="exportar" value="">
    </div>
  </form>
</div>


{{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> --}}{{--
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> --}}


@endsection
@section('myjsfile')

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
  });
</script>
@endsection
