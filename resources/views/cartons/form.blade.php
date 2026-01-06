<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">


          <!-- Codigo -->
          {!! armarInputCreateEdit('col', 'codigo', 'Código', 'text',$errors, $carton, 'form-control', '', '') !!}

          <!-- Descripción -->
          {!! armarInputCreateEdit('col', 'descripcion', 'Descripción', 'text',$errors, $carton, 'form-control', '', '') !!}

          <!-- onda -->
          {!! armarInputCreateEdit('col', 'onda', 'Onda', 'text',$errors, $carton, 'form-control', '', '') !!}

          <!-- peso -->
          {!! armarInputCreateEdit('col', 'peso', 'Peso Bruto (g)', 'text',$errors, $carton, 'form-control', '', '') !!}


        </div>
        <div class="row">

          <!-- volumen -->
          {!! armarInputCreateEdit('col', 'volumen', 'Volumen (cms 3)', 'text',$errors, $carton, 'form-control', '', '') !!}

          <!-- espesor -->
          {!! armarInputCreateEdit('col', 'espesor', 'Espesor (mm)', 'text',$errors, $carton, 'form-control', '', '') !!}

          <!-- color -->
          {!! armarInputCreateEdit('col', 'color', 'Color', 'text',$errors, $carton, 'form-control', '', '') !!}

          <!-- tipo de Cartón -->
          {!! armarInputCreateEdit('col', 'tipo', 'Tipo de Cartón', 'text',$errors, $carton, 'form-control', '', '') !!}

        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.cartons.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($carton->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>