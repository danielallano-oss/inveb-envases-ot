<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          <!-- descripcion -->
          {!! armarInputCreateEdit('col', 'name', 'Descripción', 'text',$errors, $clasificacion_cliente, 'form-control', '', '') !!}
          <div class="col"></div>
          <div class="col"></div>
          
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.clasificaciones_clientes.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($clasificacion_cliente->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>