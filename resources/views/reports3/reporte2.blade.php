@extends('layouts.index')

@section('content')
<h1 class="page-title">Reporte 1</h1>

<form id="filtros" class="py-1" action="{{ route('reporte1New1') }}" method="get" enctype="multipart/form-data">
    <div class="form-row">
        <div class="col-1">
            <div class="form-group">
                <label>Desde</label>
                <input class="form-control form-control-sm datepicker" id="datePicker1" name="date_desde" value="{{ (is_null(app('request')->input('date_desde')))? $fromDate : app('request')->input('date_desde') }}">
            </div>
        </div>
        <div class="col-1">
            <div class="form-group">
                <label>Hasta</label>
                <input class="form-control form-control-sm datepicker" id="datePicker2" name="date_hasta" value="{{ (is_null(app('request')->input('date_hasta')))? $toDate : app('request')->input('date_hasta') }}">
            </div>
        </div>

    </div>
    <div class="text-right">
        <button class="ml-auto btn btn-primary">Filtrar</button>
    </div>
</form>

<h5 class="header-report">TOP 5 SOLICITUDES CON MAYOR DURACIÓN</h5>
<div class="container-reporte1">
    <div class="item-reporte1 ">
        <h5 class="header-report">TODAS</h5>
        @if(count($topOt) == 0)
        <div class="text-center py-2">No se encontraron solicitudes entre rango de fechas</div>
        @endif
        @foreach($topOt as $ot)
        <div class="container-solicitud">
            <div class="dias">
                <div class="text-center">{{$ot->tiempoTotal}}</div>
                <div class="text-center">días</div>
            </div>
            <div class="ot">
                <p>{{$ot->creador->fullName}}</p>
                <p>{{$ot->client->nombre}}</p>
                <p>{{$ot->descripcion}}</p>
            </div>
            <div class="area text-center">
                {{$ot->area->abreviatura}}
            </div>
        </div>
        <div class="division"></div>
        @endforeach

    </div>
    <div class="item-reporte1 ">
        <h5 class="header-report">DESARROLLO COMPLETO</h5>
        @if(count($topDesarrolloCompleto) == 0)
        <div class="text-center py-2">No se encontraron solicitudes entre rango de fechas</div>
        @endif
        @foreach($topDesarrolloCompleto as $ot)
        <div class="container-solicitud">
            <div class="dias">
                <div class="text-center">{{$ot->tiempoTotal}}</div>
                <div class="text-center">días</div>
            </div>
            <div class="ot">
                <p>{{$ot->creador->fullName}}</p>
                <p>{{$ot->client->nombre}}</p>
                <p>{{$ot->descripcion}}</p>
            </div>
            <div class="area text-center">
                {{$ot->area->abreviatura}}
            </div>
        </div>
        <div class="division"></div>
        @endforeach
    </div>
    <div class="item-reporte1 ">
        <h5 class="header-report">OTRAS SOLICITUDES</h5>
        @if(count($topOtrosDesarrollos) == 0)
        <div class="text-center py-2">No se encontraron solicitudes entre rango de fechas</div>
        @endif
        @foreach($topOtrosDesarrollos as $ot)
        <div class="container-solicitud">
            <div class="dias">
                <div class="text-center">{{$ot->tiempoTotal}}</div>
                <div class="text-center">días</div>
            </div>
            <div class="ot">
                <p>{{$ot->creador->fullName}}</p>
                <p>{{$ot->client->nombre}}</p>
                <p>{{$ot->descripcion}}</p>
            </div>
            <div class="area text-center">
                {{$ot->area->abreviatura}}
            </div>
        </div>
        <div class="division"></div>
        @endforeach
    </div>

</div>


@endsection
@section('myjsfile')
<!-- chartjs -->
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script> -->
<!-- gauge js -->
<!-- <script src="{{ asset('js/gauge.min.js') }}"></script> -->
<!-- <script src="{{ asset('js/reports2.js') }}"></script> -->
@endsection
