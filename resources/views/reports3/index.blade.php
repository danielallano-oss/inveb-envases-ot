@extends('layouts.index')

@section('content')
<h1 class="page-title">Reporteria</h1>

<div class="container-report">
    <div class="item-report item-report_sm">
        <h5 class="header-report">Titulo</h5>
        <div class="tablero">
            <div class="velocimetro">
                <canvas class="velocimetro__canvas" id="velocimetro1"></canvas>
            </div>
            <div class="velocimetro">
                <canvas class="velocimetro__canvas" id="velocimetro2"></canvas>
            </div>
            <div class="velocimetro">
                <canvas class="velocimetro__canvas" id="velocimetro3"></canvas>
            </div>
        </div>
    </div>
    <div class="item-report item-report_sm">
        <h5 class="header-report">Titulo</h5>
        <div class="tablero">
            <div class="velocimetro">
                <canvas class="velocimetro__canvas" id="velocimetro4"></canvas>
            </div>
            <div class="velocimetro">
                <canvas class="velocimetro__canvas" id="velocimetro5"></canvas>
            </div>
            <div class="velocimetro">
                <canvas class="velocimetro__canvas" id="velocimetro6"></canvas>
            </div>
        </div>
    </div>
</div>
<div class="container-report">

    <div class="item-report">
        <h5 class="header-report">Titulo</h5>
        <div class="tablero">
            <div class="dona">
                <canvas class="dona__canvas" id="solicitudes_vigentes1"></canvas>
            </div>
            <div class="dona">
                <canvas class="dona__canvas" id="solicitudes_vigentes2"></canvas>
            </div>
        </div>
    </div>
    <div class="item-report">
        <h5 class="header-report">Titulo</h5>
        <div class="tablero">
            <div class="dona">addslashes
            </div>
            <div class="dona">asdasd
            </div>
        </div>
    </div>
    <div class="item-report">
        <h5 class="header-report">Titulo</h5>
        <div class="tablero">
            <div class="dona">addslashes
            </div>
            <div class="dona">asdasd
            </div>
        </div>
    </div>
    <div class="item-report">
        <h5 class="header-report">Titulo</h5>
        <div class="tablero">
            <div class="dona">addslashes
            </div>
            <div class="dona">asdasd
            </div>
        </div>
    </div>
</div>


@endsection
@section('myjsfile')
<!-- chartjs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<!-- gauge js -->
<script src="{{ asset('js/gauge.min.js') }}"></script>
<script src="{{ asset('js/reports.js') }}"></script>
@endsection