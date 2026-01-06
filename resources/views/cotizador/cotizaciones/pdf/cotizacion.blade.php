<html>
<body>

      {{-- :::::::::::::::::ACUERDOS COMERCIALES::::::::::::::::::: --}}
      @include('cotizador.cotizaciones.pdf.carta_cotizacion',$cotizacion)


      {{-- :::::::::::::::salto de pagina:::::::::::::::: --}}
      <div style="page-break-after: always"></div>

      {{-- :::::::::::::::::ACUERDOS COMERCIALES::::::::::::::::::: --}}
      @include('cotizador.cotizaciones.pdf.acuerdo_comercial',$cotizacion)
 
      {{-- :::::::::::::::salto de pagina:::::::::::::::: --}}
      <div style="page-break-after: always"></div>

      {{-- :::::::::::::::::DATOS FICHA TECNICA::::::::::::::::::: --}}
      @include('cotizador.cotizaciones.pdf.ficha_tecnica')

</body>
</html>