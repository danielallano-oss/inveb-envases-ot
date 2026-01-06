<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          {!! armarInputCreateEdit('col', 'tiempo_buin', 'Buin', 'number',$errors, $tiempotratamiento, 'form-control', '', '') !!}
          {!! armarInputCreateEdit('col', 'tiempo_tiltil', 'Tiltil', 'number',$errors, $tiempotratamiento, 'form-control', '', '') !!}
          {!! armarInputCreateEdit('col', 'tiempo_osorno', 'Osorno', 'number',$errors, $tiempotratamiento, 'form-control', '', '') !!}
          {!! armarInputCreateEdit('col', 'tiempo_buin_powerply', 'Buin Powerply', 'number',$errors, $tiempotratamiento, 'form-control', '', '') !!}
          {!! armarInputCreateEdit('col', 'tiempo_buin_cc_doble', 'Buin CC Doble', 'number',$errors, $tiempotratamiento, 'form-control', '', '') !!}
          
          {!! armarSelectArrayCreateEditUser($procesos, 'proceso_id', 'Proceso' , $errors, $tiempotratamiento ,'form-control',true,true) !!}


          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.tiempo-tratamiento.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($rechazoconjunto->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>