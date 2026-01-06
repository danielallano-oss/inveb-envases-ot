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
    border-collapse: separate;
    border-spacing: 0 1px;

    /* border-collapse: collapse; */
    /* border: none; */
    width: 100%;

  }

  td,
  th {
    /* border: 1px solid #000; */
    padding: 3px;
  }

  @page {

    margin: 10px 10px 10px 10px;
    size: 20cm 30cm;



  }


  td {
    font-size: 10px;
    text-transform: uppercase;
  }



</style>

<body>

  <main>
    <div class="table-responsive">
      <table style="width:100%;" border="0">

        <tbody style="">
          <tr style="font-weight: bold;">
            <td><img src="{{asset('img/logo-etiqueta.png')}}" width= "120px" heigth= "60px"></td>
            <td><h1> INFORMACIÓN ARCHIVO DISEÑO  OT: {{$ot->id}} </h1></td>
          </tr>
          {{--DATOS COMERCIALES--}}
          <tr>
            <td colspan="2">
              <table>
                <tr>
                  <td colspan="6"><b>DATOS COMERCIALES</b></td>
                </tr>
                <tr>
                  <td colspan="6"><hr class="hr"/></td>
                </tr>
                <tr>
                  <td width="120px"><b>VENDEDOR:</b></td>
                  <td width="190px">{{$ot->creador->fullname}}</td>
                  <td width="150px">&nbsp;</td>
                  <td>&nbsp;</td>
                  <td width="120px">&nbsp;</td>
                  <td width="70px">&nbsp;</td>
                  {{--<td width="50px"><b>DESCRIPCIÓN:</b></td>
                  <td>{{$ot->descripcion}}</td>--}}
                </tr>
                <tr>
                  <td><b>CLIENTE:</b></td>
                  <td>{{$ot->client->nombreSap}}</td>
                  <td><b>CODIGO CLIENTE:</b></td>
                  <td style="text-align: left;">{{$ot->client->codigo}}</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td><b>MATERIAL ASIGNADO:</b></td>
                  @if(is_null($ot->material_asignado)||$ot->material_asignado=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->material_asignado}}</td>
                  @endif
                  <td><b>DESCRIPCIÓN DE MATERIAL:</b></td>
                  @if(is_null($ot->descripcion_material)||$ot->descripcion_material=='')
                    <td colspan="3" style="text-align: left;">--</td>
                  @else
                    <td colspan="3" style="text-align: left;">{{$ot->descripcion_material}}</td>

                  @endif
                </tr>
              </table>
            </td>
          </tr>
          <tr><td colspan="2">&nbsp;</td></tr>
          {{--CARACTERISTICAS--}}
          <tr>
            <td colspan="2">
              <table>
                <tr>
                  <td colspan="6"><b>CARACTERÍSTICAS</b></td>
                </tr>
                <tr>
                  <td colspan="6"><hr class="hr"/></td>
                </tr>
                <tr>
                  <td width="180px"><b>CAD:</b></td>
                  @if(is_null($ot->cad)||$ot->cad=='')
                    <td style="text-align: left; width: 120px;">--</td>
                  @else
                    <td style="text-align: left; width: 120px;">{{$ot->cad}}</td>
                  @endif
                  <td width="180px"><b>TIPO ITEM:</b></td>
                  @if(is_null($ot->product_type_id)||$ot->product_type_id=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->productType->descripcion}}</td>
                  @endif
                  <td width="110px">&nbsp;</td>
                  <td width="70px">&nbsp;</td>
                </tr>
                {{--<tr>
                  <td><b>ITEMS DEL SET:</b></td>
                  @if(is_null($ot->items_set)||$ot->items_set=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->items_set}} </td>
                  @endif
                  <td><b>VECES ITEM:</b></td>

                  @if(is_null($ot->veces_item)||$ot->veces_item=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;"> {{$ot->veces_item}}</td>
                  @endif
                </tr>--}}
                <tr>
                  <td><b>COLOR CARTÓN:</b></td>
                  @if($ot->carton_color==1)
                    <td style="text-align: left;">Café</td>
                  @else
                    <td style="text-align: left;">Blanco</td>
                  @endif
                  <td><b>ESTILO:</b></td>
                  @if(is_null($ot->style_id)||$ot->style_id=='')
                    <td colspan="3" style="text-align: left;">--</td>
                  @else
                    <td colspan="3" style="text-align: left;">{{$ot->styleType->glosa}}</td>
                  @endif
                </tr>
                <tr>
                  <td><b>LARGURA HM:</b></td>
                  @if(is_null($ot->largura_hm)||$ot->largura_hm=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->largura_hm}}</td>
                  @endif
                  <td><b>ANCHURA HM:</b></td>
                  @if(is_null($ot->anchura_hm)||$ot->anchura_hm=='')
                    <td colspan="3" style="text-align: left;">--</td>
                  @else
                    <td colspan="3" style="text-align: left;">{{$ot->anchura_hm}}</td>
                  @endif
                </tr>
                <tr>
                  <td><b>ECT MIN (LB/PULG):</b></td>
                  @if(is_null($ot->ect)||$ot->ect=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->ect}}</td>
                  @endif
                  <td><b>CINTA:</b></td>
                  @if($ot->cinta==1)
                    <td colspan="3" style="text-align: left;">SI</td>
                  @else
                    <td colspan="3" style="text-align: left;">NO</td>
                  @endif
                </tr>
                <tr>
                  <td><b>FSC:</b></td>
                  @if(is_null($ot->fsc)||$ot->fsc=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->fsc_detalle->descripcion}}</td>
                  @endif
                  <td><b>PAIS/MERCADO DESTINO:</b></td>
                  @if(is_null($ot->pais_id)||$ot->pais_id=='')
                    <td colspan="3" style="text-align: left;">--</td>
                  @else
                    <td colspan="3" style="text-align: left;">{{$ot->pais->name}}</td>
                  @endif
                </tr>
                <tr>
                  <td><b>PLANTA OBJETIVO:</b></td>
                  @if(is_null($ot->planta_id)||$ot->planta_id=='')
                    <td colspan="5" style="text-align: left;">--</td>
                  @else
                    <td colspan="5" style="text-align: left;">{{$ot->planta->nombre}}</td>
                  @endif
                </tr>
                <tr>


                  <td><b>GOLPES AL LARGO:</b></td>
                  @if(is_null($ot->golpes_largo)||$ot->golpes_largo=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->golpes_largo}}</td>
                  @endif
                  <td><b>GOLPES AL ANCHO:</b></td>
                  @if(is_null($ot->golpes_ancho)||$ot->golpes_ancho=='')
                    <td colspan="3" style="text-align: left;">--</td>
                  @else
                    <td colspan="3" style="text-align: left;">{{$ot->golpes_ancho}}</td>
                  @endif
                </tr>
                <tr>
                  <td><b>SEPARACIÓN  GOLPES AL LARGO:</b></td>
                  @if(is_null($ot->separacion_golpes_largo)||$ot->separacion_golpes_largo=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->separacion_golpes_largo}}</td>
                  @endif
                  <td><b>SEPARACIÓN  GOLPES AL ANCHO:</b></td>
                  @if(is_null($ot->separacion_golpes_ancho)||$ot->separacion_golpes_ancho=='')
                    <td colspan="3" style="text-align: left;">--</td>
                  @else
                    <td colspan="3" style="text-align: left;">{{$ot->separacion_golpes_ancho}}</td>
                  @endif
                  <td><b>Cuchillas:</b></td>
                  @if(is_null($ot->cuchillas)||$ot->cuchillas=='')
                    <td colspan="3" style="text-align: left;">--</td>
                  @else
                    <td colspan="3" style="text-align: left;">{{$ot->cuchillas}}</td>
                  @endif
                </tr>
                <tr>
                  <td><b>RAYADO C1/R1 (MM):</b></td>
                  @if(is_null($ot->rayado_c1r1)||$ot->rayado_c1r1=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->rayado_c1r1}}</td>
                  @endif
                  <td><b>RAYADO R1/R2 (MM):</b></td>
                  @if(is_null($ot->rayado_r1_r2)||$ot->rayado_r1_r2=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->rayado_r1_r2}}</td>
                  @endif
                  <td><b>RAYADO R2/C2 (MM):</b></td>
                  @if(is_null($ot->rayado_r2_c2)||$ot->rayado_r2_c2=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->rayado_r2_c2}}</td>
                  @endif
                </tr>
                <tr>
                  <td><b>IMPRESIÓN:</b></td>
                  @if(is_null($ot->impresion)||$ot->impresion=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->impresion_detalle->descripcion}}</td>
                  @endif
                  <td><b>COMPLEJIDAD:</b></td>
                  @if(is_null($ot->complejidad)||$ot->complejidad=='')
                    <td colspan="3" style="text-align: left;">--</td>
                  @else
                    <td colspan="" style="text-align: left;">{{$ot->complejidad}}</td>
                  @endif
                </tr>
                <tr>
                  <td><b>TIPO DISEÑO:</b></td>
                  @if(is_null($ot->design_type_id)||$ot->design_type_id=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td colspan="5" style="text-align: left;">{{$ot->design_type->descripcion}}</td>
                  @endif

                </tr>
                <tr>
                  <td><b>NUMERO COLORES:</b></td>
                  @if(is_null($ot->numero_colores)||$ot->numero_colores=='')
                    <td colspan="5" style="text-align: left;">--</td>
                  @else
                    <td colspan="5" style="text-align: left;">{{$ot->numero_colores}}</td>
                  @endif
                </tr>
                <tr>
                  <td><b>RECUBRIMIENTO INTERNO:</b></td>
                  @if(is_null($ot->coverage_internal_id)||$ot->coverage_internal_id=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->coverage_internal->descripcion}}</td>
                  @endif
                  <td><b>% RECUBRIMIENTO INTERNO:</b></td>
                  @if(is_null($ot->percentage_coverage_internal)||$ot->percentage_coverage_internal=='')
                    <td colspan="3" style="text-align: left;">--</td>
                  @else
                    <td colspan="3" style="text-align: left;">{{$ot->percentage_coverage_internal}}</td>
                  @endif
                </tr>
                <tr>

                  <td><b>RECUBRIMIENTO EXTERNO:</b></td>
                  @if(is_null($ot->coverage_external_id)||$ot->coverage_external_id=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->coverage_external->descripcion}}</td>
                  @endif
                  <td><b>% RECUBRIMIENTO EXTERNO:</b></td>
                  @if(is_null($ot->percentage_coverage_external)||$ot->percentage_coverage_external=='')
                    <td colspan="3" style="text-align: left;">--</td>
                  @else
                    <td colspan="3" style="text-align: left;">{{$ot->percentage_coverage_external}}</td>
                  @endif
                </tr>
                {{--<tr>

                  <td><b>Color 1 (INTERIOR TyR):</b></td>
                  @if(is_null($ot->color_1_id)||$ot->color_1_id=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->color_1->descripcion}}</td>
                  @endif
                </tr>

                <tr>
                  <td><b>% IMPRESIÓN 1:</b></td>
                  @if(is_null($ot->impresion_1)||$ot->impresion_1=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->impresion_1}}</td>
                  @endif
                  <td><b>Color 2:</b></td>
                  @if(is_null($ot->color_2_id)||$ot->color_2_id=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->color_2->descripcion}}</td>
                  @endif
                </tr>
                <tr>
                  <td><b>% IMPRESIÓN 2:</b></td>
                  @if(is_null($ot->impresion_2)||$ot->impresion_2=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->impresion_2}}</td>
                  @endif
                  <td><b>Color 3:</b></td>
                  @if(is_null($ot->color_3_id)||$ot->color_3_id=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->color_3->descripcion}}</td>
                  @endif
                </tr>
                <tr>
                  <td><b>% IMPRESIÓN 3:</b></td>
                  @if(is_null($ot->impresion_3)||$ot->impresion_3=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->impresion_3}}</td>
                  @endif
                  <td><b>Color 4:</b></td>
                  @if(is_null($ot->color_4_id)||$ot->color_4_id=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->color_4->descripcion}}</td>
                  @endif
                </tr>
                <tr>
                  <td><b>% IMPRESIÓN 4:</b></td>
                  @if(is_null($ot->impresion_4)||$ot->impresion_4=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->impresion_4}}</td>
                  @endif
                  <td><b>Color 5:</b></td>
                  @if(is_null($ot->color_5_id)||$ot->color_5_id=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->color_5->descripcion}}</td>
                  @endif
                </tr>
                <tr>
                  <td><b>% IMPRESIÓN 5:</b></td>
                  @if(is_null($ot->impresion_5)||$ot->impresion_5=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->impresion_5}}</td>
                  @endif
                  <td><b>Color 6:</b></td>
                  @if(is_null($ot->color_6_id)||$ot->color_6_id=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->color_6->descripcion}}</td>
                  @endif
                </tr>
                <tr>
                  <td><b>% IMPRESIÓN 6:</b></td>
                  @if(is_null($ot->impresion_6)||$ot->impresion_6=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->impresion_6}}</td>
                  @endif
                </tr>--}}
              </table>
            </td>
          </tr>
          <tr><td colspan="2">&nbsp;</td></tr>
          {{--MEDIDAS INTERIORES--}}
          <tr>
            <td colspan="2">
              <table>
                <tr>
                  <td colspan="6"><b>MEDIDAD INTERIORES</b></td>
                </tr>
                <tr>
                  <td colspan="6"><hr class="hr"/></td>
                </tr>
                <tr>
                  <td width="50px"><b>LARGO(MM):</b></td>
                  @if(is_null($ot->interno_largo)||$ot->interno_largo=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->interno_largo}}</td>
                  @endif
                  <td width="50px"><b>ANCHO(MM):</b></td>
                  @if(is_null($ot->interno_ancho)||$ot->interno_ancho=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->interno_ancho}}</td>
                  @endif
                  <td width="50px"><b>ALTO(MM):</b></td>
                  @if(is_null($ot->interno_alto)||$ot->interno_alto=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->interno_alto}}</td>
                  @endif
                </tr>
              </table>
            </td>
          </tr>
          <tr><td colspan="2">&nbsp;</td></tr>
          {{--MEDIDAS EXTERIORES--}}
          <tr>
            <td colspan="2">
              <table>
                <tr>
                  <td colspan="6"><b>MEDIDAD EXTERIORES</b></td>
                </tr>
                <tr>
                  <td colspan="6"><hr class="hr"/></td>
                </tr>
                <tr>
                  <td width="50px"><b>LARGO(MM):</b></td>
                  @if(is_null($ot->externo_largo)||$ot->externo_largo=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->externo_largo}}</td>
                  @endif
                  <td width="50px"><b>ANCHO(MM):</b></td>
                  @if(is_null($ot->externo_ancho)||$ot->externo_ancho=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->externo_ancho}}</td>
                  @endif
                  <td width="50px"><b>ALTO(MM):</b></td>
                  @if(is_null($ot->externo_alto)||$ot->externo_alto=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->externo_alto}}</td>
                  @endif
                </tr>
              </table>
            </td>
          </tr>
          <tr><td colspan="2">&nbsp;</td></tr>
          {{--TERMINACIONES--}}
          <tr>
            <td colspan="2">
              <table>
                <tr>
                  <td colspan="4"><b>TERMINACIONES</b></td>
                </tr>
                <tr>
                  <td colspan="4"><hr class="hr"/></td>
                </tr>
                <tr>
                  <td width="50px"><b>PROCESO:</b></td>
                  @if(is_null($ot->process_id)||$ot->process_id=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->proceso->descripcion}}</td>
                  @endif
                  <td width="100px"><b>TIPO DE PEGADO:</b></td>
                  @if($ot->pegado_terminacion==0)
                    <td style="text-align: left;">No Aplica</td>
                  @elseif($ot->pegado_terminacion==2)
                    <td style="text-align: left;">Pegado Interno</td>
                  @elseif($ot->pegado_terminacion==3)
                    <td style="text-align: left;">Pegado Externo</td>
                  @elseif($ot->pegado_terminacion==4)
                    <td style="text-align: left;">Pegado 3 Puntos</td>
                  @elseif($ot->pegado_terminacion==5)
                    <td style="text-align: left;">Pegado 4 Puntos</td>
                  @endif
                </tr>
                <tr>
                  <td width="50px"><b>ARMADO:</b></td>
                  @if(is_null($ot->armado_id)||$ot->armado_id=='')
                    <td style="text-align: left;">--</td>
                  @else
                    <td style="text-align: left;">{{$ot->armado->descripcion}}</td>
                  @endif
                  <td width="70px"><b>SENTIDO ARMADO:</b></td>
                  @if($ot->sentido_armado==1)
                    <td style="text-align: left;">No Aplica</td>
                  @elseif($ot->sentido_armado==2)
                    <td style="text-align: left;">Ancho a la Derecha</td>
                  @elseif($ot->sentido_armado==3)
                    <td style="text-align: left;">Ancho a la Izquierda</td>
                  @elseif($ot->sentido_armado==4)
                    <td style="text-align: left;">Largo a la Izquierda</td>
                  @elseif($ot->sentido_armado==5)
                    <td style="text-align: left;">Largo a la Derecha</td>
                  @endif
                </tr>
              </table>
            </td>
          </tr>
          <tr><td colspan="2">&nbsp;</td></tr>

        </tbody>
      </table>
    </div>
   </main>
</body>
</html>
