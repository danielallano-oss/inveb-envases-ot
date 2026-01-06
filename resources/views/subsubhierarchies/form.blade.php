<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">
          <!-- Descripcion -->
          {!! armarInputCreateEdit('col', 'descripcion', 'Descripción', 'text',$errors, $subsubhierarchy, 'form-control', '', '') !!}

          {{-- Subhierarchy --}} <!-- ($options, $formato, $key, $title , $errors, $objeto ,$class_select) -->
          {!! armarSelectArrayCreateEdit($subhierarchies_id, 'col', 'subhierarchy_id', 'Jerarquía 2' , $errors, $subsubhierarchy ,'form-control') !!}

          <!-- Jerarquia sap -->
          {!! armarInputCreateEdit('col', 'jerarquia_sap', 'Jerarquía SAP', 'text',$errors, $subsubhierarchy, 'form-control', '', '') !!}

        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.subsubhierarchies.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($subsubhierarchy->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>