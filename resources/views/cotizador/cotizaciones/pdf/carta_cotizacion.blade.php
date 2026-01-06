<html>
<style>
  @media print {
    a[href]:after {
      content: none !important;
    }
  }

  body {
    font-family: arial, sans-serif;
  }

  .sectionTitle {
    /*text-transform: uppercase;*/
    font-size: 30px;
    margin-top: 20px;
    margin-bottom: 30px;
    border-bottom: 2px solid #54a51c;
  }

  .subTitle {
    /*text-transform: uppercase;*/
    font-size: 12;
    margin-top: 5px;
    margin-bottom: 5px;
    font-weight: bold;
  }

  .table-responsive {
    min-height: .01%;
    /* overflow-x: auto; */
  }

  .font_div {
    position: relative;
    color: black !important;
  }

  table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
  }

  td,
  th {
    border: 1px solid #000;
    text-align: center;
    padding: 3px;
  }

  @page {
    size: a4 portrait;
    margin: 100px 15px;
  }



  header {
    position: sticky;
    top: -90px;
    left: 0px;
    right: 0px;
    height: 50px;
  }

  footer {
    position: absolute;
    bottom: -90px;
    left: 0px;
    right: 0px;
    height: 50px;
  }
</style>

<body>
  {{-- ################### HEAD ################### --}}
  <header>
    <div id="title" style="">
        <div class="table-responsive">
        <table style="width:100%;">
            <tbody>
            <tr>
              <td><img src="{{ asset('img/logo.png') }}" style="max-height:70px;width:110px;"></td>
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


  {{-- ################### FOOTER ################### --}}
  <footer>
    <div id="title" style=" text-align: center; color: #2d2c2c;">
        <p>CMPC Biopackaging Corrugados <br> Dirección Av. Eyzaguirre 01098, Puente Alto, Santiago</p>
    </div>
  </footer>


  {{-- ################### BODY ################### --}}

  @php
  $descripcion = false;
  $codigo_cliente = false;
  $tipo_medida = false;
  $largo = false;
  $ancho = false;
  $alto = false;
  $cad_material_detalle = false;
  $bct = false;

  @endphp
  @foreach($cotizacion->detalles as $detalle)
  @if($detalle->descripcion_material_detalle != "" && isset($detalle->descripcion_material_detalle))
  @php $descripcion = true; @endphp
  @endif
  @if($detalle->codigo_cliente != "" && isset($detalle->codigo_cliente))
  @php $codigo_cliente = true; @endphp
  @endif
  @if($detalle->tipo_medida != "" && isset($detalle->tipo_medida))
    @php $tipo_medida = true; @endphp
    @if($detalle->tipo_medida==1)
      @php $desc_medida = 'Medidas Internas'; @endphp
    @else
      @php $desc_medida = 'Medidas Externas'; @endphp
    @endif
    
  @endif
  @if($detalle->largo != "" && isset($detalle->largo))
  @php $largo = true; @endphp
  @endif
  @if($detalle->ancho != "" && isset($detalle->ancho))
  @php $ancho = true; @endphp
  @endif
  @if($detalle->alto != "" && isset($detalle->alto))
  @php $alto = true; @endphp
  @endif
  @if($detalle->cad_material_detalle != "" && isset($detalle->cad_material_detalle))
  @php $cad_material_detalle = true; @endphp
  @endif
  @if($detalle->bct_min_lb != "" && isset($detalle->bct_min_lb))
  @php $bct = true; @endphp
  @endif
  @endforeach
  <main>
    <div id="pdf" style="position:relative;">

      {{-- Cliente: --}}
      <div class="subTitle" style="">
        @if($cotizacion->check_nombre_contacto)
        Estimado/a {{$cotizacion->nombre_contacto}}
        <br>{{$cotizacion->client->nombre}}

        @else
        Estimado/a {{$cotizacion->client->nombre}}

        @endif
        <!-- <br> -->
        <!-- [Razón Social Empresa] -->
      </div>

      <p style="font-size: 11px;">Por intermedio de la presente y según lo solicitado, tenemos el agrado de presentar nuestra cotización para el abastecimiento de los siguientes materiales:</p>
      @php $contiene_corrugado = false; @endphp
      @foreach($cotizacion->detalles as $detalle)
      @if($detalle->tipo_detalle_id == 1)
      @php $contiene_corrugado = true; @endphp
      @endif
      @endforeach

      <!-- Solo mostramos esquineros si es que hay alguno -->
      @if($contiene_corrugado)

      {{-- :::::::::::::::CAJAS DE CORRUGADO::::::::::::::: --}}
      <div class="subTitle" style="">
        CAJAS DE CARTÓN CORRUGADO:
      </div>
      <div class="table-responsive">
        <table style="width:100%;" border="1">
          <thead style="font-size: 8px;">
            <tr style="color:gray; border: 2px solid #8c8c8c;">
              
              <th colspan="3"></th>
              <th colspan="3">{{$desc_medida}}(mm)</th>
              <th colspan="10"></th>
              <th colspan="2">Servicios Incluidos</th>
            </tr>
            <tr style="background: #00b82e;color: white; border: 2px solid #8c8c8c;">
              <th style="min-width: 180px; text-align: left;">Descripción</th>
              <th>Cod. Int. Cliente</th>
              <th>Tipo Ítem</th>
              <th>Lar.</th>
              <th>Anc.</th>
              <th>Alt.</th>
              <th>Tipo Onda</th>
              <th>Color Liner Exterior</th>
              <th>Num. Colores</th>
              <th>Tipo Impresion</th>
              <th>CAD</th>
              <th>OT</th>
              <th>ECT</th>
              <th>BCT MIN (LB)</th>
              <th>Volumen Negociado</th>
              <th>Precio Unitario ({{[1=>"USD",2=>"CLP"][$cotizacion->moneda_id]}})</th>
              <th>Barniz</th>
              <th>Destino</th>
            </tr>
          </thead>
          <tbody style="font-size: 9px; border: 2px solid #8c8c8c;">

            @foreach($cotizacion->detalles as $detalle)
            @if($detalle->tipo_detalle_id != 1)
            @php continue; @endphp 
            @endif
            <tr>
              <td style=" text-align: left; font-size:9px;">{{$detalle->descripcion_material_detalle}}</td>
              <td>{{$detalle->codigo_cliente}}</td>
              <td>{{$detalle->productType->descripcion}}</td>
              <td>{{$detalle->largo}}</td>
              <td>{{$detalle->ancho}}</td>
              <td>{{$detalle->alto}}</td>
              <td>{{$detalle->carton->onda_1}}{{($detalle->carton->onda_2 != "0" && $detalle->carton->onda_2 != $detalle->carton->onda_1) ? $detalle->carton->onda_2 : null}}</td>
              <td>{{$detalle->carton->color_tapa_exterior}}</td>
              <td>{{$detalle->numero_colores}}</td>
              @if(is_null($detalle->print_type_id))
                <td></td>
              @else
                @if($detalle->print_type_id == 1 || $detalle->print_type_id == 2 || $detalle->print_type_id == 4) 
                  <td>Normal</td>
                @else
                  @if($detalle->print_type_id == 3 || $detalle->print_type_id == 5)
                    <td>Alta Grafica</td>
                  @else
                    <td></td>
                  @endif
                @endif
              @endif
              <td>{{$detalle->cad_material_detalle}}</td>
              <td>{{$detalle->work_order_id}}</td>
              <td>{{$detalle->carton->ect_min}}</td>
              <td>{{($detalle->bct_min_lb) ? $detalle->bct_min_lb : 'SI'}} </td>
              <td>{{$detalle->cantidad}}</td>
              @if ($cotizacion->moneda_id==1)
                @if(isset($detalle->precios->precio_final))
                  <td><b>{{number_format_unlimited_precision_cotizacion($detalle->precios->precio_final["usd_caja"],',','.',3)}}</b></td>
                @else
                  <td><b>{{number_format_unlimited_precision_cotizacion($detalle->precios->precio_total["usd_caja"],',','.',3)}}</b></td>
                @endif
              @else
                @if(isset($detalle->precios->precio_final))
                  <td><b>{{number_format_unlimited_precision_cotizacion($detalle->precios->precio_final["clp_caja"],',','.',0)}}</b></td>
                @else
                  <td><b>{{number_format_unlimited_precision_cotizacion($detalle->precios->precio_total["clp_caja"],',','.',0)}}</b></td>
                @endif
              @endif
              
              @if($detalle->porcentaje_cera_interno + $detalle->porcentaje_cera_externo > 0){{--Cotizaciones Antes del Evolutivo 24-01--}}
                <td>SI</td>
              @else
                @if(!is_null($detalle->barniz_type_id)){{--Nuevas Cotizaciones Despues Evolutivo 24-01--}}
                  <td>SI</td>
                @else
                  <td>NO</td>
                @endif  
              @endif            
              
              <td>{{$detalle->flete->ciudad}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <br>
     
      
      @endif

      {{-- :::::::::::::::::ESQUINEROS::::::::::::::::::: --}}

      @php $contiene_esquinero = false; @endphp
      @foreach($cotizacion->detalles as $detalle)
      @if($detalle->tipo_detalle_id == 2)
      @php $contiene_esquinero = true; @endphp
      @endif
      @endforeach

      <!-- Solo mostramos esquineros si es que hay alguno -->
      @if($contiene_esquinero)
      {{-- :::::::::::::::salto de pagina:::::::::::::::: --}}
      @if($contiene_corrugado)
      <div style="page-break-after: always"></div>
      @endif


      <div class="subTitle" style="">
        ESQUINEROS:
      </div>
      <div class="table-responsive">
        <table style="width:100%;" border="1">
          <thead style="font-size: 10px;">
            {{-- <tr style="color:gray; border: 2px solid #8c8c8c;">
                <th colspan="10"></th>
              </tr> --}}
            <tr style="background: #00b82e;color: white; border: 2px solid #8c8c8c;">
              <th>Descripción</th>
              <!-- <th>Color Exterior</th> -->
              <th>Resistencia</th>
              <th>Impresión</th>
              <!-- <th>Volumen Negociado </th> -->
              <th>Precio Unitario ({{[1=>"USD",2=>"CLP"][$cotizacion->moneda_id]}})</th>
              <th>Flete</th>            
            </tr>
          </thead>
          <tbody style="font-size: 10px; border: 2px solid #8c8c8c;">
            @foreach($cotizacion->detalles as $detalle)
            @if($detalle->tipo_detalle_id != 2)
            @php continue; @endphp
            @endif
            <tr>
              <td style=" text-align: left; font-size:10px;">{{$detalle->descripcion_material_detalle}}</td>
              <td>{{$detalle->carton_esquinero->resistencia}}</td>
              <td>{{($detalle->numero_colores >= 1) ? "SI" : "NO"}}</td>
              @if ($cotizacion->moneda_id==1)
                @if(isset($detalle->precios->precio_final))
                  <td><b>{{number_format_unlimited_precision(str_replace(',', '.',$detalle->precios->precio_final["usd_caja"]),',','.',3)}}</b></td>
                @else
                  <td><b>{{number_format_unlimited_precision(str_replace(',', '.',$detalle->precios->precio_total["usd_caja"]),',','.',3)}}</b></td>
                @endif
              @else
                @if(isset($detalle->precios->precio_final))
                  <td><b>{{number_format_unlimited_precision(str_replace(',', '.',$detalle->precios->precio_final["clp_caja"]),',','.',0)}}</b></td>
                @else
                  <td><b>{{number_format_unlimited_precision(str_replace(',', '.',$detalle->precios->precio_total["clp_caja"]),',','.',0)}}</b></td>
                @endif
              @endif
              <td>{{$detalle->flete->ciudad}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <br>
      
      <br>
      @endif
      {{-- Observaciones: --}}
      <div class="subTitle" style="font-weight: normal;font-size:14px">
        <p>Observaciones:</p>
        <div style="width: 100%; border-bottom: 1px solid #000; margin-bottom: 26px;">

          {{$cotizacion->observacion_cliente}}
        </div>
        <div style="width: 100%; border-bottom: 1px solid #000; margin-bottom: 26px;"></div>
        <div style="width: 100%; border-bottom: 1px solid #000; margin-bottom: 26px;"></div>
        <div style="width: 100%; border-bottom: 1px solid #000; margin-bottom: 26px;"></div>
      </div>

    </div>
  </main>
</body>

</html>