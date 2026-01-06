<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">
          {{-- Descripción --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
          {!! armarInputCreateEdit('col', 'descripcion', 'Descripción', 'text',$errors, $hierarchy, 'form-control', '', '') !!}

          <div class="col"></div>
          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.hierarchies.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($hierarchy->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>