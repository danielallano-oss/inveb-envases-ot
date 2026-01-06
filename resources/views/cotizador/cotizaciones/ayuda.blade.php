@extends('layouts.index')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
<style>
  .card-text {
    font-size: 16px !important;
    font-weight: bold !important;
  }
</style>
<!-- Titulo -->
<h1 class="page-title">Sección de Ayuda
</h1>
<div class="container">
  <div class="card-deck mb-4 video-deck">
    <div class="card">

      <a data-fancybox="" data-width="640" data-height="360" data-small-btn="true" href="{{ asset('videos/cotizacion a OT.mp4')}}"><img class="card-img-top img-fluid" src="https://techrev.me/wp-content/uploads/2019/09/cropped-how-to-make-tutorial-videos-1600x768.jpg"></a>

      <div class="card-body">
        <p class="card-text">
          ¿Cómo pasar de un detalle de una Cotización a una OT ?
        </p>
      </div>
    </div>

    <div class="card">
      <a data-fancybox="" data-width="640" data-height="360" data-small-btn="true" href="{{ asset('videos/OT a Cotizacion.mp4')}}"><img class="card-img-top img-fluid" src="https://techrev.me/wp-content/uploads/2019/09/cropped-how-to-make-tutorial-videos-1600x768.jpg"></a>

      <div class="card-body">
        <p class="card-text">
          ¿Cómo pasar de una OT a una Cotización?
        </p>
      </div>
    </div>
    <div class="card">
      <a data-fancybox="" data-width="640" data-height="360" data-small-btn="true" href="{{ asset('videos/crear Cotizacion.mp4')}}"><img class="card-img-top img-fluid" src="https://techrev.me/wp-content/uploads/2019/09/cropped-how-to-make-tutorial-videos-1600x768.jpg"></a>

      <div class="card-body">
        <p class="card-text">
          ¿Cómo crear una nueva Cotización rapidamente?
        </p>
      </div>
    </div>
    <div class="card">
      <a data-fancybox="" data-width="640" data-height="360" data-small-btn="true" href="{{ asset('videos/Corrugado.mp4')}}"><img class="card-img-top img-fluid" src="https://techrev.me/wp-content/uploads/2019/09/cropped-how-to-make-tutorial-videos-1600x768.jpg"></a>

      <div class="card-body">
        <p class="card-text">
          ¿Cómo agregar detalle corrugado?
        </p>
      </div>
    </div>

  </div>
  <div class="card-deck mb-4 video-deck">
    <div class="card">

      <a data-fancybox="" data-width="640" data-height="360" data-small-btn="true" href="{{ asset('videos/Esquinero.mp4')}}"><img class="card-img-top img-fluid" src="https://techrev.me/wp-content/uploads/2019/09/cropped-how-to-make-tutorial-videos-1600x768.jpg"></a>

      <div class="card-body">
        <p class="card-text">
          ¿Cómo agregar detalle esquinero?
        </p>
      </div>
    </div>

    <div class="card">
      <a data-fancybox="" data-width="640" data-height="360" data-small-btn="true" href="{{ asset('videos/AreaHC.mp4')}}"><img class="card-img-top img-fluid" src="https://techrev.me/wp-content/uploads/2019/09/cropped-how-to-make-tutorial-videos-1600x768.jpg"></a>

      <div class="card-body">
        <p class="card-text">
          ¿Cómo calcular un area HC?
        </p>
      </div>
    </div>
    <div class="card">
      <a data-fancybox="" data-width="640" data-height="360" data-small-btn="true" href="{{ asset('videos/CartonHC.mp4')}}"><img class="card-img-top img-fluid" src="https://techrev.me/wp-content/uploads/2019/09/cropped-how-to-make-tutorial-videos-1600x768.jpg"></a>

      <div class="card-body">
        <p class="card-text">
          ¿Cómo estimar un cartón?
        </p>
      </div>
    </div>
    <div class="card">
      <a data-fancybox="" data-width="640" data-height="360" data-small-btn="true" href="{{ asset('videos/Versionar.mp4')}}"><img class="card-img-top img-fluid" src="https://techrev.me/wp-content/uploads/2019/09/cropped-how-to-make-tutorial-videos-1600x768.jpg"></a>

      <div class="card-body">
        <p class="card-text">
          ¿Cómo versionar una cotización? (Editar Cotización activa)
        </p>
      </div>
    </div>

  </div>
  <div class="card-deck mb-4 video-deck">
    <div class="card">

      <a data-fancybox="" data-width="640" data-height="360" data-small-btn="true" href="{{ asset('videos/Multidestino.mp4')}}"><img class="card-img-top img-fluid" src="https://techrev.me/wp-content/uploads/2019/09/cropped-how-to-make-tutorial-videos-1600x768.jpg"></a>

      <div class="card-body">
        <p class="card-text">
          ¿Cómo Agregar un Multidestino?
        </p>
      </div>
    </div>

    <div class="card">
      <a data-fancybox="" data-width="640" data-height="360" data-small-btn="true" href="{{ asset('videos/cargaMasiva.mp4')}}"><img class="card-img-top img-fluid" src="https://techrev.me/wp-content/uploads/2019/09/cropped-how-to-make-tutorial-videos-1600x768.jpg"></a>

      <div class="card-body">
        <p class="card-text">
          ¿Cómo cargar una cotizacíon masiva? (Carga de Archivo)
        </p>
      </div>
    </div>
    <div class="card">
      <a data-fancybox="" data-width="640" data-height="360" data-small-btn="true" href="{{ asset('videos/Duplicar.mp4')}}"><img class="card-img-top img-fluid" src="https://techrev.me/wp-content/uploads/2019/09/cropped-how-to-make-tutorial-videos-1600x768.jpg"></a>

      <div class="card-body">
        <p class="card-text">
          ¿Cómo duplicar cotización? (Cotización independiente)
        </p>
      </div>
    </div>
    <div class="card">
      <a data-fancybox="" data-width="640" data-height="360" data-small-btn="true" href="{{ asset('videos/DescargarPDF.mp4')}}"><img class="card-img-top img-fluid" src="https://techrev.me/wp-content/uploads/2019/09/cropped-how-to-make-tutorial-videos-1600x768.jpg"></a>

      <div class="card-body">
        <p class="card-text">
          ¿Cómo imprimir cotización? (Carta Tipo)
        </p>
      </div>
    </div>

  </div>
  <!-- <div class="card-deck mb-4 video-deck">
    <div class="card">

      <a data-fancybox="" data-width="640" data-height="360" data-small-btn="true" href="{{ asset('videos/AreaHC.mp4')}}"><img class="card-img-top img-fluid" src="https://techrev.me/wp-content/uploads/2019/09/cropped-how-to-make-tutorial-videos-1600x768.jpg"></a>

      <div class="card-body">
        <p class="card-text">
          ¿Cómo buscar un material en una Cotización?
        </p>
      </div>
    </div>


  </div> -->

</div>
@endsection
@section('myjsfile')

<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>


<script>
  $(document).on("click", '[data-toggle="lightbox"]', function(event) {
    event.preventDefault();
    $(this).ekkoLightbox();
  });
  const notify = (msg = "Complete los campos faltantes", type = "danger") => {
    $.notify({
      message: `<p  class="text-center">${msg}</p> `,
    }, {
      type,
      animate: {
        enter: "animated bounceInDown",
        exit: "animated bounceOutUp",
      },
      // delay: 500000,
      placement: {
        from: "top",
        align: "center",
      },
      z_index: 999999,
    });
  };
</script>
@endsection