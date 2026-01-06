<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          <!-- codigo -->
          {!! armarInputCreateEdit('col', 'codigo', 'Código', 'text',$errors, $almacen, 'form-control', '', '') !!}

          <!-- descripcion -->
          {!! armarInputCreateEdit('col', 'denominacion', 'Denominación', 'text',$errors, $almacen, 'form-control', '', '') !!}

          <!-- centro -->
          {!! armarInputCreateEdit('col', 'centro', 'Centro', 'text',$errors, $almacen, 'form-control', '', '') !!}



          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.almacenes.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($almacen->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>