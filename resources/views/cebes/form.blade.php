<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          <!-- codigo -->

          {!! armarSelectArrayCreateEditUser($plantas, 'planta_id', 'Planta' , $errors, $cebe ,'form-control',true,true,) !!}
          {!! armarInputCreateEditCustomCol('col', 'tipo', 'Tipo', 'text',$errors, $cebe, 'form-control', '', '','-2') !!}
          {!! armarSelectArrayCreateEditUser($mercados, 'hierearchie_id', 'Mercado' , $errors, $cebe ,'form-control',true,true) !!}
          {!! armarInputCreateEditCustomCol('col', 'cebe', 'CeBe', 'text',$errors, $cebe, 'form-control', '', '','-2') !!}
          {!! armarInputCreateEditCustomCol('col', 'nombre_cebe', 'Nombre Cebe', 'text',$errors, $cebe, 'form-control', '', '','-2') !!}
          {!! armarInputCreateEditCustomCol('col', 'grupo_gastos_generales', 'Grupo Gastos Gral.', 'text',$errors, $cebe, 'form-control', '', '','-3') !!}




          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.cebes.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($cebe->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>
