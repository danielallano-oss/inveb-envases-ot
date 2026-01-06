@extends('layouts.app')


@section('body')
<div class="content">
  <div class="container-fluid">
    <div class="row">

      <div class="col-xs-12 col-md-10 col-sm-offset-1">
        <div class="row">
          <div class="col-xs-12 col-sm-12">
            <div class="pageTitle">Carga masiva de Clientes</div>
          </div>
        </div>
        <div class="normalForm">
          <!-- formulario: -->
          <form method="POST" enctype="multipart/form-data" action="{{ route('mantenedores.clients.uploading') }}">
            @csrf
            <div class="col-xs-12 col-md-12">
              <div class="row">
                <div class="col-xs-12 col-sm-6 pull-left">
                  <!-- <label for="archivo">Seleccionar CSV a cargar</label> -->
                  <input type="file" class="file" name="archivo" id="archivo" required />
                </div>
                <div class="col-xs-12 col-sm-3 "><a class="sbtn cancel" href="{{ route('mantenedores.clients.list') }}">Volver</a></div>
                <div class="col-xs-12 col-sm-3 pull-right">
                  <input type="submit" class="sbtn submit" />
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
      </div>
    </div>
    @if(isset($clients_ingresados))
    <div class="row" style="margin-top:50px">
      <div class="col-md-10 col-md-offset-1">
        <div class="col-md-4">
          <div class="infoBox">
            <div class="title">Clientes Ingresados
              <div class="badge infoInverse right">{{count($clients_ingresados)}}</div>
            </div>
            <div class="content height200">
              <table class="table">
                <thead>
                  <tr>
                    <th>Lineas</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>@foreach($clients_ingresados as $i => $client)
                      @if($i == 0) {{$client->linea}}
                      @else {{', '.$client->linea }}
                      @endif
                      @endforeach
                    </td>

                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="infoBox">
            <div class="title">Clientes Duplicados
              <div class="badge infoInverse right">{{count($clients_duplicados)}}</div>
            </div>
            <div class="content height200">
              <table class="table">
                <thead>
                  <tr>
                    <th>Lineas</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>@foreach($clients_duplicados as $i => $client)
                      @if($i == 0) {{$client->linea}}
                      @else {{', '.$client->linea }}
                      @endif
                      @endforeach
                    </td>

                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="infoBox">
            <div class="title">Clientes Invalidos
              <div class="badge infoInverse right">{{count($clients_error)}}</div>
            </div>
            <div class="content height200">
              <table class="table">
                <thead>
                  <tr>
                    <th>Lineas</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>@foreach($clients_error as $i => $client)
                      @if($i == 0) {{$client}}
                      @else {{', '.$client }}
                      @endif
                      @endforeach
                    </td>

                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>

@endsection