<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          {!! armarInputCreateEdit('col', 'cantidad_buin', 'Cantidad Buin', 'number',$errors, $cantidadbase, 'form-control', '', '') !!}
          {!! armarInputCreateEdit('col', 'cantidad_tiltil', 'Cantidad Tiltil', 'number',$errors, $cantidadbase, 'form-control', '', '') !!}
          {!! armarInputCreateEdit('col', 'cantidad_osorno', 'Cantidad Osorno', 'number',$errors, $cantidadbase, 'form-control', '', '') !!}
          
          {!! armarSelectArrayCreateEditUser($procesos, 'proceso_id', 'Proceso' , $errors, $cantidadbase ,'form-control',true,true) !!}


          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.cantidad-base.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($cantidadbase->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>