<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          <!-- codigo -->

          {!! armarSelectArrayCreateEditUser($plantas, 'planta_id', 'Planta' , $errors, $grupoplanta ,'form-control',true,true) !!}

          {!! armarInputCreateEdit('col', 'centro', 'Centro', 'text',$errors, $grupoplanta, 'form-control', '', '') !!}
          {!! armarInputCreateEdit('col', 'num_almacen', 'N° Almacen', 'text',$errors, $grupoplanta, 'form-control', '', '') !!}
          {{-- {!! armarInputCreateEdit('col', 'cebe', 'CeBe', 'text',$errors, $grupoplanta, 'form-control', '', '') !!} --}}




          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.grupo-plantas.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($grupoplanta->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>
