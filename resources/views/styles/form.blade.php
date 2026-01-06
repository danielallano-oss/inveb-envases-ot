<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">


          <!-- Codigo -->
          {!! armarInputCreateEdit('col', 'codigo', 'Código', 'text',$errors, $style, 'form-control', '', '') !!}

          <!-- Descripcion -->
          {!! armarInputCreateEdit('col', 'glosa', 'Glosa', 'text',$errors, $style, 'form-control', '', '') !!}

          <!-- Codigo Estilo Armado -->
          {!! armarInputCreateEdit('col', 'codigo_armado', 'Código Estilo Armado', 'text',$errors, $style, 'form-control', '', '') !!}

           <!-- Grupo Materiales -->
           {!! armarInputCreateEdit('col', 'grupo_materiales', 'Grupo Materiales', 'text',$errors, $style, 'form-control', '', '') !!}

          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.styles.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($style->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>