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

        @if ($tipo == 'producto')
            border-spacing: 0 1px;
        @else
            border-spacing: 0 0.7em;
        @endif
        /* border-collapse: collapse; */
        /* border: none; */
        width: 100%;

    }

    td,
    th {
        /* border: 1px solid #000; */
        text-align: center;
        padding: 3px;
    }

    @page {
        @if ($tipo == 'producto')
            /* size: a4 portrait; */
            size: 10cm 10cm;
            margin: 5px 5px -20px 10px;
            /* margin: 20px 10px 20px 30px; */
        @else
            /* size: 10cm 10cm; */
            size: a4 portrait;
            margin: 50px;
        @endif
    }

    /*
  header {
    position: fixed;
    top: -90px;
    left: 0px;
    right: 0px;
    height: 50px;
  }

  footer {
    position: fixed;
    bottom: -90px;
    left: 0px;
    right: 0px;
    height: 50px;
  } */

    td {
        font-size: 7px;
        text-transform: uppercase;
    }

    tr {
        /* padding-bottom: 0px; */
    }
</style>

<body>
    <!--<div id="watermark">
      <img src="{{ asset('img/logo-cmpc.gif') }}" style="position:absolute; opacity:0.3; top:100px; left:70px; width:250px; height:100px" alt="Marca de Agua CMPC" width="70%" height="30%">
  </div> -->
    <main>
        <div class="table-responsive">
            <table style="width:100%;" border="0">
                @if ($tipo == 'producto')
                    <tbody style="">
                        <tr style="font-weight: bold;">
                            <td colspan="2" style="width: 1%;"><img src="{{ asset('img/logo-etiqueta.png') }}"
                                    width= "120px" heigth= "60px"></td>
                            <td colspan="2" style="width: 52%;text-align:center;font-size:15px">OT
                                {{ $muestra->work_order_id }}&nbsp; CAD {{ $muestra->cad }}</td>

                            <td style="width: 47%;">
                                <table>
                                    @if ($muestra->muestra_excel == 1)
                                        <tr>
                                            <td style="font-size:7px;width: 20%;border:1px solid black;">Codigo</td>
                                            <td style="font-size:6px;width: 80%;border:1px solid black;">
                                                {{ $muestra->codigo_cliente }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:7px;width: 20%;border:1px solid black;">Revision</td>
                                            <td style="font-size:8px;width: 80%;border:1px solid black;">02</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:7px;width: 20%;border:1px solid black;">Fecha</td>
                                            <td style="font-size:8px;width: 80%;border:1px solid black;">01/06/2023</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td style="font-size:7px;width: 20%;border:1px solid black;">Codigo</td>
                                            <td style="font-size:6px;width: 80%;border:1px solid black;">BRC-Registro
                                                N°207</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:7px;width: 20%;border:1px solid black;">Revision</td>
                                            <td style="font-size:8px;width: 80%;border:1px solid black;">02</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:7px;width: 20%;border:1px solid black;">Fecha</td>
                                            <td style="font-size:8px;width: 80%;border:1px solid black;">01/06/2023</td>
                                        </tr>
                                    @endif

                                </table>
                            </td>
                        </tr>
                        <tr style="font-weight: lighter;">
                            <td style="width: 50%;text-align:left;font-size:9px"><b>solicitante</b></td>
                            <td colspan="4" style="width: 100%;text-align:left;font-size:9px">
                                {{ $muestra->ot->client->nombre }}</td>

                        </tr>
                        @if ($muestra->muestra_excel == 1)
                            <tr style="font-weight: lighter;">
                                <td style="width: 50%;text-align:left;font-size:9px"><b>descripcion</b></td>
                                <td colspan="4" style="width: 100%;text-align:left;font-size:9px">
                                    {{ $muestra->descripcion_muestra }}</td>

                            </tr>
                        @else
                            <tr style="font-weight: lighter;">
                                <td style="width: 50%;text-align:left;font-size:9px"><b>descripcion</b></td>
                                <td colspan="4" style="width: 100%;text-align:left;font-size:9px">
                                    {{ $muestra->ot->descripcion }}</td>

                            </tr>
                        @endif

                        <tr style="font-weight: lighter;">
                            <td style=" width: 50%;text-align:left;font-size:9px"><b>diseñador</b></td>
                            <td colspan="3" style="width: 40%;text-align:left;font-size:9px">
                                {{ $muestra->ot->ingenieroAsignado ? $muestra->ot->ingenieroAsignado->user->fullname : null }}
                            </td>
                            @if ($muestra->muestra_excel == 1)
                                <td style="width: 10%;text-align:right;margin-right:-10px;font-size:15px"><b>ID
                                        {{ $muestra->ot_id_excel }}</b></td>
                            @else
                                <td style="width: 10%;text-align:right;margin-right:-10px;font-size:15px"><b>ID
                                        {{ $muestra->id }}</b></td>
                            @endif

                        </tr>
                        <tr style="font-weight: lighter;">
                            <td style=" width: 50%;text-align:left;font-size:9px"><b>medidas</b></td>
                            <td style=" width: 10%;text-align:left;font-size:9px"><b>interiores</b></td>
                            <td style=" width: 20%;"></td>
                            <td style=" width: 10%;text-align:left;font-size:9px"><b>exteriores</b></td>
                            <td style="width: 10%;text-align:right;margin-right:-10px;font-size:11.5px"><b>Fecha
                                    {{ $muestra->created_at->format('d/m/Y') }} </b></td>



                        </tr>
                        @if ($muestra->muestra_excel == 1)
                            <tr style="font-weight: lighter;">
                                <td style=" width: 50%;text-align:left;font-size:9px"><b>Largo</b></td>
                                <td style="font-weight:bold;font-size: 16px; width: 45%;text-align:right;">
                                    {{ isset($muestra->largo_int) ? $muestra->largo_int : null }}</td>
                                <td style=" width: 30%;text-align:left;">[mm]</td>
                                <td style="font-weight:bold;font-size: 16px; width: 45%;text-align:right;">
                                    {{ isset($muestra->largo_ext) ? $muestra->largo_ext : null }}</td>
                                <td style=" width: 30%;text-align:left;">[mm]</td>

                            </tr>
                            <tr style="font-weight: lighter;">
                                <td style=" width: 50%;text-align:left;font-size:9px"><b>Ancho</b></td>
                                <td style="font-weight:bold;font-size: 16px; width: 45%;text-align:right;">
                                    {{ isset($muestra->ancho_int) ? $muestra->ancho_int : null }}</td>
                                <td style=" width: 30%;text-align:left;">[mm]</td>
                                <td style="font-weight:bold;font-size: 16px; width: 45%;text-align:right;">
                                    {{ isset($muestra->ancho_ext) ? $muestra->ancho_ext : null }}</td>
                                <td style=" width: 30%;text-align:left;">[mm]</td>
                            </tr>
                            <tr style="font-weight: lighter;">
                                <td style=" width: 50%;text-align:left;font-size:9px"><b>Alto</b></td>
                                <td style="font-weight:bold;font-size: 16px;; width: 45%;text-align:right;">
                                    {{ isset($muestra->alto_int) ? $muestra->alto_int : null }}</td>
                                <td style=" width: 30%;text-align:left;">[mm]</td>
                                <td style="font-weight:bold;font-size: 16px; width: 45%;text-align:right;">
                                    {{ isset($muestra->alto_ext) ? $muestra->alto_ext : null }}</td>
                                <td style=" width: 30%;text-align:left;">[mm]</td>
                            </tr>
                        @else
                            <tr style="font-weight: lighter;">
                                <td style=" width: 50%;text-align:left;font-size:9px"><b>Largo</b></td>
                                <td style="font-weight:bold;font-size: 16px; width: 45%;text-align:right;">
                                    {{ isset($muestra->ot->interno_largo) ? $muestra->ot->interno_largo : null }}</td>
                                <td style=" width: 30%;text-align:left;">[mm]</td>
                                <td style="font-weight:bold;font-size: 16px; width: 45%;text-align:right;">
                                    {{ isset($muestra->ot->externo_largo) ? $muestra->ot->externo_largo : null }}</td>
                                <td style=" width: 30%;text-align:left;">[mm]</td>

                            </tr>
                            <tr style="font-weight: lighter;">
                                <td style=" width: 50%;text-align:left;font-size:9px"><b>Ancho</b></td>
                                <td style="font-weight:bold;font-size: 16px; width: 45%;text-align:right;">
                                    {{ isset($muestra->ot->interno_ancho) ? $muestra->ot->interno_ancho : null }}</td>
                                <td style=" width: 30%;text-align:left;">[mm]</td>
                                <td style="font-weight:bold;font-size: 16px; width: 45%;text-align:right;">
                                    {{ isset($muestra->ot->externo_ancho) ? $muestra->ot->externo_ancho : null }}</td>
                                <td style=" width: 30%;text-align:left;">[mm]</td>
                            </tr>
                            <tr style="font-weight: lighter;">
                                <td style=" width: 50%;text-align:left;font-size:9px"><b>Alto</b></td>
                                <td style="font-weight:bold;font-size: 16px;; width: 45%;text-align:right;">
                                    {{ isset($muestra->ot->interno_alto) ? $muestra->ot->interno_alto : null }}</td>
                                <td style=" width: 30%;text-align:left;">[mm]</td>
                                <td style="font-weight:bold;font-size: 16px; width: 45%;text-align:right;">
                                    {{ isset($muestra->ot->externo_alto) ? $muestra->ot->externo_alto : null }}</td>
                                <td style=" width: 30%;text-align:left;">[mm]</td>
                            </tr>
                        @endif

                        <tr style="font-weight: lighter">


                            <td style=" width: 50%;text-align:left;font-size:9px"><b>Tipo Onda</b></td>
                            <td style="font-weight:bold; width: 45%;text-align:right;font-size:16px">
                                {{ isset($muestra->carton) ? $muestra->carton->onda : null }}</td>
                            <td style=" width: 30%;text-align:left;"></td>

                            <td colspan="2" style=" width: 50%;text-align:left;font-size:9px"><b>Color
                                    Externo</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span
                                    style="font-weight:bold; width: 45%;text-align:right;font-size:16px;margin-right:-10px;">{{ isset($muestra->carton) && isset($muestra->carton->color_tapa_exterior) ? $muestra->carton->color_tapa_exterior : null }}</span>
                            </td>
                        </tr>
                        <td style=" width: 35%;text-align:left;font-size:9px"><b>Conformidad</b></td>
                        </tr>
                        <tr>
                            <td><br></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <span style="border: 1px solid black;width:100%;display:block"></span>
                            </td>
                            <td>
                                <span style="border: 1px solid black;width:100%;display:block"></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="width:100%; text-align:center;font-size:9px"><b>Nombre</b></td>
                            <td style="width:100%; text-align:center;font-size:9px"><b>Firma</b></td>
                        </tr>
                        <tr style="font-weight: lighter;">
                            <td colspan="8">
                                <p style=" text-align:justify; text-transform: none;font-size:6.5px">
                                    MUESTRA CONFECCIONADA EN MESA DE CORTE A PARTIR DE UNA PLACA DE CARTÓN. VÁLIDA SÓLO
                                    PARA VERIFICACIÓN Y APROBACIÓN DE ASPECTO (TIPO DE ONDA Y COLOR EXTERNO),
                                    FORMA/ESTILO Y DIMENSIONES.<b> NO RESULTA REPRESENTATIVA EN TÉRMINOS DE GRAMAJE,
                                        RESISTENCIA (ej. ECT, BCT) Y/O FUNCIONALIDAD (ej. ARMADO AUTOMÁTICO)</b>. NO
                                    SOBRESCRIBIR LOS DATOS DE LA ETIQUETA Y TOMAR UNA FOTO DE BUENA CALIDAD. CASO
                                    CONTRARIO NO SE CONSIDERARÁ VÁLIDA LA CONFORMIDAD (V°B°).
                                </p>
                            </td>
                        </tr>
                    </tbody>
                @else
                    <tbody style="">
                        <tr style="">
                            <td colspan="5" style="width: 1%; text-align:right;"><img
                                    src="{{ asset('img/logo-etiqueta.png') }}" width= "200px" heigth= "200px"></td>
                        </tr>
                        <tr style="">
                            <td colspan="5"
                                style="font-weight: lighter; width: 66%;text-align:left;font-size:50px">
                                CLIENTE: {{ $muestra->ot->client->nombre }}</td>
                        </tr>
                        <tr style="">
                            <td colspan="5"
                                style="font-weight: lighter; width: 66%;text-align:left;font-size:50px">
                                Nombre:</td>
                        </tr>
                        <tr style="">
                            <td colspan="5" style="width: 66%;text-align:left;font-size:50px">
                                {{ $muestra->destinatario_1 ? $muestra->destinatario_1 : null }}</td>
                        </tr>

                        <tr style="">

                            <td colspan="5"
                                style="font-weight: lighter; width: 66%;text-align:left;font-size:50px">
                                Dirección:</td>
                        </tr>
                        <tr style="">

                            <td colspan="5" style="width: 66%;text-align:left;font-size:50px">
                                {{ isset($muestra->direccion_1) ? $muestra->direccion_1 : null }}</td>
                        </tr>

                        <tr style="">

                            <td colspan="5"
                                style="font-weight: lighter; width: 66%;text-align:left;font-size:50px">Comuna:</td>
                        </tr>
                        <tr style="">
                            <td colspan="5" style="width: 66%;text-align:left;font-size:50px">
                                {{ isset($muestra->ciudad_asignada) ? $muestra->ciudad_asignada->ciudad : null }}
                            </td>
                        </tr>
                        <tr>

                            <td><br></td>
                        </tr>
                        <tr style="">
                            <td colspan="5" style="width: 66%;text-align:left;font-size:50px"> OT:
                                {{ $muestra->work_order_id }}</td>
                        </tr>
                    </tbody>
                @endif
            </table>
        </div>
    </main>
</body>

</html>
