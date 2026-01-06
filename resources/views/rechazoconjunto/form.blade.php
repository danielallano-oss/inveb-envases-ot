<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

            {!! armarSelectArrayCreateEditUser($procesos, 'proceso_id', 'Proceso' , $errors, $rechazoconjunto ,'form-control',true,true) !!}

          {!! armarInputCreateEdit('col', 'codigo', 'Código', 'text',$errors, $rechazoconjunto, 'form-control', '', '') !!}
          {!! armarInputCreateEdit('col', 'porcentaje_proceso_solo', 'Proceso Solo (%)', 'number',$errors, $rechazoconjunto, 'form-control', '', '') !!}
          {!! armarInputCreateEdit('col', 'porcentaje_proceso_barniz', 'Proceso + Barniz (%)', 'number',$errors, $rechazoconjunto, 'form-control', '', '') !!}
          {!! armarInputCreateEdit('col', 'porcentaje_proceso_maquila', 'Proceso + Maquila (%)', 'number',$errors, $rechazoconjunto, 'form-control', '', '') !!}



          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.rechazo-conjunto.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($rechazoconjunto->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>
