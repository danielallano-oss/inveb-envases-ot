<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          <!-- descripcion -->
          {!! armarInputCreateEdit('col', 'descripcion', 'Descripción', 'text',$errors, $tipocinta, 'form-control', '', '') !!}
          {!! armarInputCreateEdit('col', 'codigo', 'Código', 'text',$errors, $tipocinta, 'form-control', '', '') !!}




          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.tipos-cintas.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($almacen->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>
