<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          <!-- Nombre -->
          {!! armarInputCreateEdit('col', 'nombre', 'Nombre', 'text',$errors, $canal, 'form-control', '', '') !!}

          <!-- Codigo Estilo Armado -->
          {!! armarInputCreateEdit('col', 'codigo', 'Código SAP', 'text',$errors, $canal, 'form-control', '', '') !!}

          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.canals.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($canal->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>