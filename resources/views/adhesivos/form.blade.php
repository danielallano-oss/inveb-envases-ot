<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">
          <!-- Planta -->
          {!! armarSelectArrayCreateEditUser($plantas, 'planta_id', 'Planta' , $errors, $adhesivo ,'form-control',true,true) !!}
          
          <!-- Maquina -->
          {!! armarInputCreateEdit('col', 'maquina', 'Maquina', 'text',$errors, $adhesivo, 'form-control', '', '') !!}
          
          <!-- Codigo SAP -->
          {!! armarInputCreateEdit('col', 'codigo', 'Código SAP', 'text',$errors, $adhesivo, 'form-control', '', '') !!}

          <!-- Consumo gr. Ml -->
          {!! armarInputCreateEdit('col', 'consumo', 'Consumo gr. Ml', 'text',$errors, $adhesivo, 'form-control', '', '') !!}

          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.adhesivos.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($adhesivo->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>