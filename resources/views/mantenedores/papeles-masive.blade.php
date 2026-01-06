@extends('layouts.index')
@section('content')
<style>
  .table td {
    padding: 0rem;
  }

  .card-counter {
    box-shadow: 2px 2px 10px #DADADA;
    margin: 5px;
    padding: 10px 50px;
    background-color: #fff;
    height: 86px;
    border-radius: 5px;
    transition: .3s linear all;
  }

  .card-counter:hover {
    box-shadow: 4px 4px 20px #DADADA;
    transition: .3s linear all;
  }

  .card-counter.primary {
    background-color: #4491e4;
    color: #FFF;
  }

  .card-counter.danger {
    background-color: #ef5350;
    color: #FFF;
  }

  .card-counter.success {
    background-color: #66bb6a;
    color: #FFF;
  }

  .card-counter.info {
    background-color: #26c6da;
    color: #FFF;
  }

  .card-counter.warning {
    background-color: #faffa8;
    color: #000;
  }

  .card-counter i {
    font-size: 5em;
    opacity: 0.2;
  }

  .card-counter .count-numbers {
    position: absolute;
    right: 35px;
    top: 20px;
    font-size: 32px;
    display: block;
  }

  .card-counter .count-name {
    position: absolute;
    right: 35px;
    top: 65px;
    font-style: italic;
    text-transform: capitalize;
    opacity: 0.7;
    display: block;
    font-size: 18px;
  }

  /* LEGENDA
   */
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
  .legend .ventas2 {
    background-color: #FAA43A;
  }

  /* BACK TO TOP */
  #myBtn {
    display: none;
    /* Hidden by default */
    position: fixed;
    /* Fixed/sticky position */
    bottom: 60px;
    /* Place the button at the bottom of the page */
    right: 30px;
    /* Place the button 30px from the right */
    z-index: 99;
    /* Make sure it does not overlap */
    border: none;
    /* Remove borders */
    outline: none;
    /* Remove outline */
    background-color: #218838;
    /* Set a background color */
    color: white;
    /* Text color */
    cursor: pointer;
    /* Add a mouse pointer on hover */
    padding: 15px;
    /* Some padding */
    border-radius: 50px;
    /* Rounded corners */
    font-size: 16px;
    /* Increase font size */

    width: auto;
    height: 50px;

  }

  #myBtn:hover {
    background-color: #555;
    /* Add a dark-grey background on hover */
  }

  /* LOGO DEL MANTENEDOR */
  #logo-mantenedor {
    display: block;
    position: absolute;
    top: 5%;
    right: 19.5%;
    z-index: 99;
    color: #218838;
    padding: 8px;
    font-size: 95px;
  }

  #loading {
    width: 100%;
    height: 100%;
    top: 0px;
    left: 0px;
    position: fixed;
    display: block;
    z-index: 99;
    background-color: rgba(0, 0, 0, 0.15);
  }

  .loader {
    position: absolute;
    top: 40%;
    left: 45%;
    z-index: 100;
  }
</style>
<div class="row">
  <div class="col-12">
    <div class="page-title" style="display:inline">Carga masiva de Papeles

    </div>
    <!-- <a class="btn btn-success btn-sm" data-attribute="link" href="/files/Carga Masiva Corrugados.xlsx" download title="Descargar">Archivo Ejemplo Papeles</a> -->
    <a class="btn btn-info flot-right" href="{{ route('mantenedores.cotizador.papeles.descargar_excel_papeles') }}" title="Descargar Listado Papeles">Descargar Listado Papeles
      <div class="material-icons md-14" data-toggle="tooltip" title="Descargar Listado Papeles" style="color:white;">download</div> </a>
    </a>
  </div>
</div>
<br>
<div class="normalForm">
  <!-- formulario: -->
  <form id="form-carga-papeles" method="POST" enctype="multipart/form-data" action="{{ route('mantenedores.cotizador.papeles.uploading') }}">
    @csrf
    <div class="col-xs-12 col-md-12">
      <div class="row">
        <div id="container-boton-carga" class="col-2 row text-center">

          <div class="col-12">
            <button id="btn-cargar-papeles" disabled type="submit" class="btn btn-success ">Procesar Papeles</button>
          </div>
          <div class="col-12 my-1" style="display: none;" id="reload">
            <a href="javascript:window.location.href=window.location.href" class="btn btn-light " style="background-color:#dde1e4 ;">Revertir</a>
          </div>

          <div id="important-message" style="display:none" class="col-12 my-2">
            <h6 style="color:red;font-weight:bold">* Recuerda confirmar la informacion procesada para finalizar la Carga</h6>
          </div>
        </div>
        <div id="container-file-carga" class="col-10">
          <!-- <label for="archivo">Seleccionar CSV a cargar</label> -->
          <input type="file" class="file" name="archivo" id="archivo" required />
          <input type="hidden" name="proceso" id="proceso" value="" />
        </div>

      </div>
    </div>
    @if ($errors->any())
    <div class="alert alert-danger">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

  </form>
</div>

<br>
<br>
<div class="row ">
  <div class="col">
    <div class="card-counter primary">
      <div class="material-icons md-70" data-toggle="tooltip" style="color:white;font-size:50px">functions</div>
      <span id="totalPapeles" class="count-numbers">{{$papeles->count()}}</span>
      <span class="count-name">Total Papeles</span>
    </div>
  </div>
  <div class="col">
    <div class="card-counter success">
      <div class="material-icons md-70" data-toggle="tooltip" style="color:white;font-size:50px">add</div>
      <span id="papelesNuevos" class="count-numbers">0</span>
      <span class="count-name">Papeles Nuevos</span>
    </div>
  </div>
  <div class="col">
    <div class="card-counter warning">
      <div class="material-icons md-70" data-toggle="tooltip" style="color:white;font-size:50px;color:#000">edit</div>
      <span id="papelesActualizados" class="count-numbers">0</span>
      <span class="count-name">Papeles Actualizados</span>
    </div>
  </div>
  <div class="col">
    <div class="card-counter danger">
      <div class="material-icons md-70" data-toggle="tooltip" style="color:white;font-size:50px">clear</div>
      <span id="papelesInactivados" class="count-numbers">0</span>
      <span class="count-name">Papeles Inactivados</span>
    </div>
  </div>
  <div class="col">
    <div class="card-counter " style="background-color: #eac7c7;">
      <div class="material-icons md-70" data-toggle="tooltip" style="color:white;font-size:50px">error</div>
      <span id="PapelesErroneos" class="count-numbers">0</span>
      <span class="count-name">Errores de Carga</span>
    </div>
  </div>



</div>
<div id="legenda" class="row sticky-top" style="background-color: #f2f4f5;display:none">
  <div class="col-12 ">
    <div class="" style="margin: 5px;;background-color: #f2f4f5;height: 40px;border-radius: 5px;">
      <ul class="legend" style="display: flex;justify-content:space-around">
        <li data-toggle="tooltip" data-html="true" title="Total Papeles"><span style="background-color:#4491e4"></span> Total Papeles</li>
        <li data-toggle="tooltip" data-html="true" title="Papeles Nuevos"><span style="background-color:#66bb6a"></span> Papeles Nuevos</li>
        <li data-toggle="tooltip" data-html="true" title="Papeles Actualizados"><span style="background-color:#faffa8"></span> Papeles Actualizados</li>
        <li data-toggle="tooltip" data-html="true" title="Papeles Inactivados"><span style="background-color:#ef5350"></span> Papeles Inactivados</li>
        <li data-toggle="tooltip" data-html="true" title="Errores de Carga"><span style="background-color:#eac7c7"></span> Errores de Carga</li>

      </ul>
    </div>
  </div>
</div>
<!-- Tabla / Listado -->
<div class="container-table mt-3 bg-white border px-2">
  <table id="listadoPapelesMasivos" class="table table-status table-hover text-center">
    <thead>
      <tr>
        <th width="100px">ID</th>
        <th>Código</th>
        <th>Gramaje</th>
        <th>Precio</th>
        <th>Mc Usd Ton</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody>
      @foreach($papeles as $papel)
      <tr id="papel-row-{{ $papel->orden }}" class="{{ $papel->active == 1 ? '' : 'text-muted' }}">
        <td>{{ $papel->id }}</td>
        <!-- el codigo 99 es especial de emplacado por eso mostrar distinto -->
        <td>{{ ($papel->codigo != 99) ?$papel->codigo: "99 (Cartulina 175 gr)" }}</td>
        <td>{{ $papel->gramaje }}</td>
        <td>{{ $papel->precio }}</td>
        <td>{{ $papel->mc_usd_ton }}</td>
        <td>
          @if($papel->active == 1) Activo
          @else Inactivo
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<button onclick="topFunction()" id="myBtn" title="Go to top">
  <div class="material-icons md-18" data-toggle="tooltip" title="Descargar Listado Papeles" style="color:white;">arrow_upward</div> </a>
</button>

<div id="logo-mantenedor">
  <i class="fas fa-map"></i>
</div>
<!-- Loading  -->
<div id="loading">
  <div id="modal-loader" class="loader">Loading...</div>
</div>
@endsection


@section('myjsfile')

<script src="{{ asset('js/mantenedores/papel-masivo.js') }}"></script>
@endsection