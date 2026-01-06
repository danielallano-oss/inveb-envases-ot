<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          <!-- Codigo -->
          {!! armarInputCreateEdit('col', 'codigo', 'Código', 'text',$errors, $productType, 'form-control', '', '') !!}
          {!! armarInputCreateEdit('col', 'codigo_sap', 'Código SAP', 'text',$errors, $productType, 'form-control', '', '') !!}

          <!-- Descripcion -->
          {!! armarInputCreateEdit('col', 'descripcion', 'Descripción', 'text',$errors, $productType, 'form-control', '', '') !!}

          <div class="col"></div>
          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.product-types.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($productType->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>
