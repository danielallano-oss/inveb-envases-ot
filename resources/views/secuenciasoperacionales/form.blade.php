<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          <!-- codigo -->
          {!! armarInputCreateEdit('col', 'codigo', 'Código', 'text',$errors, $secuenciaoperacional, 'form-control', '', '') !!}

          <!-- descripcion -->
          {!! armarInputCreateEdit('col', 'descripcion', 'Descripción', 'text',$errors, $secuenciaoperacional, 'form-control', '', '') !!}

           <!-- nombre corto -->
           {!! armarInputCreateEdit('col', 'nombre_corto', 'Nombre Corto', 'text',$errors, $secuenciaoperacional, 'form-control', '', '') !!}

          <!-- texto_breve -->
          {!! armarSelectArrayCreateEditUser($plantas, 'planta_id', 'Planta' , $errors, $secuenciaoperacional ,'form-control',true,true) !!}

          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.secuencias-operacionales.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($secuenciaoperacional->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>