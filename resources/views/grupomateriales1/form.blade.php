<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          <!-- codigo -->
          {!! armarInputCreateEdit('col', 'codigo', 'Código', 'text',$errors, $grupomaterial, 'form-control', '', '') !!}


          {!! armarSelectArrayCreateEditUser($armados, 'armado_id', 'Armado' , $errors, $grupomaterial ,'form-control',true,true) !!}


          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.grupo-materiales-1.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($grupomaterial->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>