<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">
          <!-- Descripcion -->
          {!! armarInputCreateEdit('col', 'descripcion', 'Descripción', 'text',$errors, $subhierarchy, 'form-control', '', '') !!}

          {{-- Role --}} <!-- ($options, $formato, $key, $title , $errors, $objeto ,$class_select) -->
          {!! armarSelectArrayCreateEdit($hierarchies_id, 'col', 'hierarchy_id', 'Jerarquía 1' , $errors, $subhierarchy ,'form-control') !!}
          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.subhierarchies.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($subhierarchy->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>