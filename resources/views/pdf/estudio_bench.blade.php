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
    <div>
      <table style="width:100%;" border="0">       
        <tbody style="">
          <tr style="font-weight: bold;">
            <td><img src="{{asset('img/logo-etiqueta.png')}}" width= "120px" heigth= "60px"></td> 
            <td  width= "400px">&nbsp;</td>
            <td  style="width: 120px;">
              <table>
                <tr><td style="font-size:7px;width: 20%;border:1px solid black;">Codigo</td><td style="font-size:6px;width: 80%;border:1px solid black;">BRC-Registro N°207</td></tr>
                <tr><td style="font-size:7px;width: 20%;border:1px solid black;">Revision</td><td style="font-size:8px;width: 80%;border:1px solid black;">02</td></tr>
                <tr><td style="font-size:7px;width: 20%;border:1px solid black;">Fecha</td><td style="font-size:8px;width: 80%;border:1px solid black;">01/06/2023</td></tr>
              </table>
            </td> 
          </tr>
          <tr>
            <td  width= "400px" colspan="3" align="center"><h1> Solicitud Ensayo Laboratorio</h1></td>
          </tr>
          {{--DATOS COMERCIALES--}}          
          <tr>
            <td colspan="2">
              <table>
                <tr>
                  <td colspan="2"><hr class="hr"/></td>
                </tr>
                <tr>
                  <td width="150px"><b>Numero OT:</b></td>
                  <td width="560px">{{$ot->id}}</td>                  
                </tr>
                <tr>
                  <td width="150px"><b>Cliente:</b></td>
                  <td>{{$ot->client->nombreSap}}</td>
                </tr>    
                <tr>
                  <td width="150px"><b>Area:</b></td>
                  <td>Desarrollo de Producto</td>
                </tr>  
                <tr>
                  <td width="150px"><b>Solicitante:</b></td>
                  <td >{{$ot->creador->fullname}}</td>
                </tr>
                <tr>
                  <td width="150px"><b>Fecha de Solicitud:</b></td>
                  <td >{{$ot->fecha_laboratorio}}</td>                  
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
                  <td colspan="5" width="1000px"><b>Detalle información</b></td>
                </tr>
                <tr>
                  <td colspan="8" width="1000px"><hr class="hr"/></td>
                </tr>
                <tr>
                  <td>
                    <table>
                      <tr>
                        <td width="100px"><b>Cantidad: &nbsp;&nbsp;</b>{{$ot->cantidad_estudio_bench}}</td>
                      </tr>
                    </table>    
                  </td>           
                </tr>
                <tr><td colspan="8" width="1000px">&nbsp;</td></tr>
                <tr>
                  <td>
                    <table>
                      <tr>
                        <td><b>Identificación Muestra</b></td>
                        <td><b>Cliente</b></td>
                        <td><b>Descripción</b></td>
                      </tr>
                      <?php 
                        for($i=0;$i<count($detalle_estudio);$i++){
                          $detalle_fila = explode('¡',$detalle_estudio[$i]);
                      ?>
                        <tr>
                          <td>{{$detalle_fila[0]}}</td>
                          <td>{{$detalle_fila[1]}}</td>
                          <td>{{$detalle_fila[2]}}</td>
                        </tr>
                      <?php
                        }                    
                      ?>
                    </table>
                  </td>
                </tr>
                <tr><td colspan="8" width="1000px">&nbsp;</td></tr>
                <tr>
                  <td>
                    <table>
                      <tr>
                        @if($ot->check_estudio_bct==1)                  
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>BCT (lbf)</b></td>
                        @else            
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>BCT (lbf)</b></td>
                        @endif
                        @if($ot->check_estudio_ect==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>ECT (lb/in)</b></td>
                        @else
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>ECT (lb/in)</b></td>
                        @endif
                        @if($ot->check_estudio_bct_humedo==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>BCT en Humedo (lbf)</b></td>                                 
                        @else
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>BCT en Humedo (lbf)</b></td> 
                        @endif 
                      </tr>
                      <tr>
                        @if($ot->check_estudio_flat==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>Flat Crush (lb/in)</b></td>
                        @else
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>Flat Crush (lb/in)</b></td>
                        @endif 
                        @if($ot->check_estudio_humedad==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>Humedad (%)</b></td>
                        @else                  
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>Humedad (%)</b></td>
                        @endif  
                        @if($ot->check_estudio_porosidad_ext==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>Porosidad Exterior Gurley</b></td>
                        @else                 
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>Porosidad Exterior Gurley</b></td>
                        @endif 
                      </tr>
                      <tr>
                        @if($ot->check_estudio_porosidad_int==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>Porosidad Interior Gurley</b></td>
                        @else
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>Porosidad Interior Gurley</b></td>
                        @endif 
                        @if($ot->check_estudio_espesor==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>Espesor (mm)</b></td>
                        @else                  
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>Espesor (mm)</b></td>
                        @endif
                        @if($ot->check_estudio_cera==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>Cera</b></td>
                        @else
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>Cera</b></td>
                        @endif 
                      </tr>
                      <tr>
                        @if($ot->check_estudio_flexion_fondo==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>Flexión de Fondo</b></td>
                        @else               
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>Flexión de Fondo</b></td>
                        @endif 
                        @if($ot->check_estudio_gramaje==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>Gramaje (gr/mt2)</b></td>
                        @else                 
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>Gramaje (gr/mt2)</b></td>
                        @endif
                        @if($ot->check_estudio_composicion_papeles==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>Composición Papeles</b></td>
                        @else                 
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>Composición Papeles</b></td>
                        @endif
                      </tr>
                      <tr>
                        @if($ot->check_estudio_cobb_interno==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>Cobb Interno</b></td>
                        @else                 
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>Cobb Interno</b></td>
                        @endif
                        @if($ot->check_estudio_cobb_externo==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>Cobb Externo:</b></td>
                        @else                 
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>Cobb Externo:</b></td>
                        @endif
                        @if($ot->check_estudio_flexion_4_puntos==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>Flexión 4 Puntos</b></td>
                        @else
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>Flexión 4 Puntos</b></td>
                        @endif
                      </tr>
                      <tr>
                        @if($ot->check_estudio_medidas==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>Medidas</b></td>
                        @else
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>Medidas</b></td>
                        @endif
                        @if($ot->check_estudio_impresion==1)
                          <td width="30px"><input type="checkbox" checked></td>
                          <td width="200px"><b>Impresión</b></td>
                        @else
                          <td width="30px"><input type="checkbox"></td>
                          <td width="200px"><b>Impresión</b></td>
                        @endif 
                        <td colspan="2">&nbsp;</td>
                      <tr>               
                    </table>
                  </td>
                </tr>
            </td>
          </tr>
          <tr><td colspan="6">&nbsp;</td></tr>
          @if(!is_null($ot->observacion))
            <tr>
              <td colspan="2">
                <table>
                  <tr>
                    <td colspan="6"><b>Observaciones</b></td>
                  </tr>
                  <tr>
                    <td colspan="6"><hr class="hr"/></td>
                  </tr>
                  <tr>
                    <td width="720px">{{$ot->observacion}}</td>                  
                  </tr>
                </table>
              </td>
            </tr>  
          @endif        
        </tbody>       
      </table>
    </div>
   </main>
</body>
</html>