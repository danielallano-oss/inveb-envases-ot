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


      {{-- :::::::::::::::::DATOS VENDEDOR::::::::::::::::::: --}}
      <div class="table-responsive">
        <table style="width:100%;" border="0">
          <thead>
            <tr>
              <th style="border: 0px solid #fff;"></th>
              <th style="border: 0px solid #fff;"></th>
              <th style="border: 0px solid #fff;"></th>
            </tr>
          </thead>
          <tbody style="font-size: 11px;">
            <tr>
              <!-- <td style="width: 33%; border: 0px solid #fff;"></td> -->
              <!-- <td style="width: 33%; border: 0px solid #fff;"></td> -->
              <td style="width: 33%; border: 0px solid #fff; text-align: left; font-size: 16px;">
                <b>{{$cotizacion->user->fullname}}</b><br>
                {{$cotizacion->user->role->nombre}} <br>
                @if($cotizacion->user->telefono)
                {{ $cotizacion->user->telefono }} <br>
                @endif
                {{$cotizacion->user->email}}
              </td> 
              <td colspan="2" style="width: 33%; border: 0px solid #fff; text-align: left; font-size: 14px;padding-top:15px">
                Emitir orden de compra a: <br>
                - Envases Impresos Cordillera SpA <br>
                - Rut: 89.201.400-0 <br>
                - Dirección: Casa Matriz: Avda. Eyzaguirre 01098, Puente Alto. <br>
                - Teléfono: (+562) 2444 24 00
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      {{-- :::::::::::::::::ACUERDOS COMERCIALES::::::::::::::::::: --}}
      <br><br>
      <div class="table-responsive">
        <table style="width:100%;" border="0">
          <thead>
            <tr>
              <th colspan="2" style="border: 0px solid #fff; text-align: left; color: #3c3c3c; padding: 8px;"><u><b>Acuerdos comerciales</b></u></th>
            </tr>
          </thead>
          <tbody style="font-size: 16px; color: #3e3e3e;">
            <tr>
              <td style="min-width: 50px; border: 0px solid #fff; text-align: right; padding: 2px 8px;"> - </td>
              <td style="border: 0px solid #fff; text-align: left; font-size: 14px; text-align: left; padding: 2px 8px;">
                La validez de la presente cotización es de 10 días hábiles.
              </td>
            </tr>
            <tr>
              <td style="min-width: 50px; border: 0px solid #fff; text-align: right; padding: 2px 8px;"> - </td>
              <td style="border: 0px solid #fff; text-align: left; font-size: 14px; text-align: left; padding: 2px 8px;">
                Los valores no incluyen IVA.
              </td>
            </tr>
            <tr>
              <td style="min-width: 50px; border: 0px solid #fff; text-align: right; padding: 2px 8px;"> - </td>
              <td style="border: 0px solid #fff; text-align: left; font-size: 14px; text-align: left; padding: 2px 8px;">
                Plazo de pago: <b>{{$cotizacion->dias_pago}} días.</b>
              </td>
            </tr>
             @php $ajuste_precios = false; @endphp
             @foreach($cotizacion->detalles as $detalle)
             @if($detalle->ajuste_precios == 1)
             @php $ajuste_precios = true; @endphp
             @endif
             @endforeach
             @if($ajuste_precios)
             <tr>
               <td style="min-width: 50px; border: 0px solid #fff; text-align: right; padding: 2px 8px;"> - </td>
               <td style="border: 0px solid #fff; text-align: left; font-size: 14px; text-align: left; padding: 2px 8px;">
                 Plazo de pago del IVA: Día 12 del mes siguiente a la emisión factura.
               </td>
             </tr>
             @endif
            <tr>
              <td style="min-width: 50px; border: 0px solid #fff; text-align: right; padding: 2px 8px;"> - </td>
              <td style="border: 0px solid #fff; text-align: left; font-size: 14px; text-align: left; padding: 2px 8px;">
                En caso de que corresponda, el crédito debe ser aprobado por la Gerencia de Finanzas de CMPC.
              </td>
            </tr>
            <tr>
              <td style="min-width: 50px; border: 0px solid #fff; text-align: right; padding: 2px 8px;"> - </td>
              <td style="border: 0px solid #fff; text-align: left; font-size: 14px; text-align: left; padding: 2px 8px;">
                Plazo de entrega: A convenir. En caso de que se trate de un material nuevo, será informada una vez que el material se encuentre aprobado e ingresado al sistema.
              </td>
            </tr>
            <tr>
              <td style="min-width: 50px; border: 0px solid #fff; text-align: right; padding: 2px 8px;"> - </td>
              <td style="border: 0px solid #fff; text-align: left; font-size: 14px; text-align: left; padding: 2px 8px;">
                La orden de compra se entenderá cumplida con un 10% de más o de menos respecto a las cantidades solicitadas. Dentro de esta tolerancia se incluye el despacho de saldos, incluso cuando ello implique el envío de pallets incompletos.
              </td>
            </tr>
            <tr>
              <td style="min-width: 50px; border: 0px solid #fff; text-align: right; padding: 2px 8px;"> - </td>
              <td style="border: 0px solid #fff; text-align: left; font-size: 14px; text-align: left; padding: 2px 8px;">
                La fecha de entrega de la orden de compra se considerará cumplida con una tolerancia de 48 horas previas o posteriores a la fecha estipulada, a menos que se acuerden otras condiciones. La no recepción del material dentro de este plazo implica la aceptación del costo logístico y bodegaje correspondiente.
              </td>
            </tr>
            <tr>
              <td style="min-width: 50px; border: 0px solid #fff; text-align: right; padding: 2px 8px;"> - </td>
              <td style="border: 0px solid #fff; text-align: left; font-size: 14px; text-align: left; padding: 2px 8px;">
                La presentación de los productos se realizará con el paletizado que permita la máxima eficiencia logística y estabilidad propuesta por CMPC, a menos que se acuerde algo diferente.
              </td>
            </tr>
            <tr>
              <td style="vertical-align:top;min-width: 50px; border: 0px solid #fff; text-align: right; padding: 2px 8px;"> - </td>
              <td style="border: 0px solid #fff; text-align: left; font-size: 14px; text-align: left; padding: 2px 8px;">
                Mediante Ord. N° 971, de 16/3/2006, Art. 15° N° 1, del D.L. N° 825, de 1974, de producirse un
                alza en el cambio de la moneda extranjera, en el lapso que medie entre la fecha de la facturación y
                la fecha del pago, ésta se afectará o no con Impuesto al Valor Agregado. De este modo, si existe un
                alza en la variación del tipo de cambio se emitirá Nota de Débito; si, por el contrario, existe una
                baja en la variación del tipo de cambio se emitirá Nota de Crédito. Para ambos DTE solo es
                aplicable el valor del IVA. De igual forma, según oficio de 2010 el N° 1219 señala que, en el caso de
                que ambos contribuyentes lleven su contabilidad en moneda extranjera y pacten y paguen sus
                operaciones en esa misma moneda, no corresponde la emisión de DTE por diferencia de tipo de
                cambio, lo cual se puede acreditar mediante resolución proporcionada por el SII.

              </td>
            </tr>
            @php $devolucion_pallets = false; @endphp
            @foreach($cotizacion->detalles as $detalle)
            @if($detalle->devolucion_pallets == 1)
            @php $devolucion_pallets = true; @endphp
            @endif
            @endforeach
            @if($devolucion_pallets)
            <tr>
              <td style="min-width: 50px; border: 0px solid #fff; text-align: right; padding: 2px 8px;"> - </td>
              <td style="border: 0px solid #fff; text-align: left; font-size: 14px; text-align: left; padding: 2px 8px;">
                Las tarimas utilizadas para la entrega de los productos son de propiedad de CMPC Biopackaging
                Corrugados y deben ser devueltas.
              </td>
            </tr>
            @endif
            @php $ajuste_precios = false; @endphp
            @foreach($cotizacion->detalles as $detalle)
            @if($detalle->ajuste_precios == 1)
            @php $ajuste_precios = true; @endphp
            @endif
            @endforeach
            @if($ajuste_precios)
            <tr>
              <td style="vertical-align:top;min-width: 50px; border: 0px solid #fff; text-align: right; padding: 2px 8px;"> - </td>
              <td style="border: 0px solid #fff; text-align: left; font-size: 14px; text-align: left; padding: 2px 8px;">
                Los precios indicados en la presente cotización podrán ser modificados previo al despacho, en el caso que el indicador de precios de papeles de referencia publicado por Fastmarkets RISI (proveedor de informes de precios de mercado de productos a base de fibra natural) indique una variación superior al 5% (de disminución o incremento) respecto al último valor publicado del mismo indicador previo a la fecha de la presente cotización.
              </td>
            </tr>
            @endif
            <tr>
              <td style="min-width: 50px; border: 0px solid #fff; text-align: right; padding: 2px 8px;"> - </td>
              <td style="border: 0px solid #fff; text-align: left; font-size: 14px; text-align: left; padding: 2px 8px;">
                Cualquier diferencia técnica o condiciones comerciales no especificadas en este documento, deberán ser detalladas en un contrato acordado entre las partes.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>

</html>