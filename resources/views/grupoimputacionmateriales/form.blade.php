<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          {!! armarInputCreateEdit('col', 'codigo', 'Código', 'text',$errors, $grupoimputacionmaterial, 'form-control', '', '') !!}

          {!! armarSelectArrayCreateEditUser($procesos, 'proceso', 'Proceso' , $errors, $grupoimputacionmaterial ,'form-control',true,true) !!}


          {!! armarInputCreateEdit('col', 'familia', 'Familia', 'text',$errors, $grupoimputacionmaterial, 'form-control', '', '') !!}
          {!! armarInputCreateEdit('col', 'material_modelo', 'Material Modelo', 'text',$errors, $grupoimputacionmaterial, 'form-control', '', '') !!}

          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.grupo-imputacion-material.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($grupoimputacionmaterial->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>
