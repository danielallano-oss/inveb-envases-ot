<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          <!-- codigo -->
          {!! armarInputCreateEdit('col', 'codigo', 'Código', 'text',$errors, $organizacionventa, 'form-control', '', '') !!}

          <!-- descripcion -->
          {!! armarInputCreateEdit('col', 'descripcion', 'Descripción', 'text',$errors, $organizacionventa, 'form-control', '', '') !!}

    

          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.organizacion-venta.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($organizacionventa->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>