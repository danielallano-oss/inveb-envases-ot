{{-- HEAD --}}
<header>
  <div id="title" style="">
    <div class="table-responsive">
      <table style="width:100%;">
        <tbody>
          <tr>
            <td><img src="{{ asset('img/favicon-cmpc.png') }}" style="max-height:70px;"></td>
            <td class="font_div" style="font-size: 25px;" align="center"> <b>COTIZACIÓN</b> <br> <span style="font-size: 14px;">CMPC Biopackaging Corrugados</span></td>
            <td class="font_div" style="font-size: 15px;" align="left">
              Cotización Nº <b>{{$cotizacion->id}}V{{$cotizacion->version_number}}</b>
              <br>
              Fecha: <b>{{$cotizacion->created_at->format('d/m/y')}}</b>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</header>