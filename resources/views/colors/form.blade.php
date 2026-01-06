<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          <!-- codigo -->
          {!! armarInputCreateEdit('col', 'codigo', 'Código', 'text',$errors, $color, 'form-control', '', '') !!}

          <!-- descripcion -->
          {!! armarInputCreateEdit('col', 'descripcion', 'Descripción', 'text',$errors, $color, 'form-control', '', '') !!}

          <!-- texto_breve -->
          {!! armarInputCreateEdit('col', 'texto_breve', 'Texto Breve', 'text',$errors, $color, 'form-control', '', '') !!}

          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.colors.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($color->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>