<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          {{-- denominacion --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
          {!! armarInputCreateEdit('col', 'codigo', 'Código', 'text',$errors, $sector, 'form-control', '', '') !!}
          {!! armarInputCreateEdit('col', 'descripcion', 'Descripcion', 'text',$errors, $sector, 'form-control', '', '') !!}


          {{-- Product type --}}
          {!! armarSelectArrayCreateEditUser($product_types_id, 'product_type_id', 'Tipo de Producto' , $errors, $sector ,'form-control',true,true) !!}

          {{-- {!! armarSelectArrayCreateEdit($product_types_id, 'col', 'product_type_id', 'Tipo de Producto' , $errors, $sector ,'form-control') !!} --}}


          <div class="col"></div>
          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.sectors.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($sector->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>
