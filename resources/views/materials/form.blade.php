<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row mb-3">

          <!-- Código -->
          {!! armarInputCreateEdit('col', 'codigo', 'Código', 'text',$errors, $material, 'form-control', '', '') !!}


          <!-- Descripción -->
          {!! armarInputCreateEdit('col', 'descripcion', 'Descripción', 'text',$errors, $material, 'form-control', '', '') !!}

          {!! armarInputCreateEdit('col', 'numero_colores', 'Número de Colores', 'text',$errors, $material, 'form-control', '', '') !!}


          <!-- Largo Interno -->
          {!! armarInputCreateEdit('col', 'gramaje', 'Gramaje', 'text',$errors, $material, 'form-control', '', '') !!}

          <!-- Ancho Interno -->
          {!! armarInputCreateEdit('col', 'ect', 'ECT', 'text',$errors, $material, 'form-control', '', '') !!}

        </div>
        <div class="row  mb-3">
          <!-- Alto Interno -->
          {!! armarInputCreateEdit('col', 'golpes_largo', 'Golpes Largo', 'text',$errors, $material, 'form-control', '', '') !!}


          <!-- Largo Externo -->
          {!! armarInputCreateEdit('col', 'golpes_ancho', 'Golpes Ancho', 'text',$errors, $material, 'form-control', '', '') !!}

          <!-- Ancho Externo -->
          {!! armarInputCreateEdit('col', 'area_hc', 'Área HC', 'text',$errors, $material, 'form-control', '', '') !!}

          <!-- Alto Externo -->
          {!! armarInputCreateEdit('col', 'bct_min_lb', 'BCT Min LB', 'text',$errors, $material, 'form-control', '', '') !!}

          {!! armarInputCreateEdit('col', 'bct_min_kg', 'BCT Min KG.', 'text',$errors, $material, 'form-control', '', '') !!}

        </div>
        <div class="row  mb-3">
          <!-- Largura -->

          <!-- Anchura -->
          {{-- {!! armarInputCreateEdit('col', 'anchura', 'Anchura', 'text',$errors, $material, 'form-control', '', '') !!} --}}

          <!-- distancia_corte1_rayado1 -->
          {{-- {!! armarInputCreateEdit('col', 'distancia_corte1_rayado1', 'Distancia Corte 1 a Rayado 1', 'text',$errors, $material, 'form-control', '', '') !!} --}}

          <!-- distancia_rayado1_rayado2 -->
          {{-- {!! armarInputCreateEdit('col', 'distancia_rayado1_rayado2', 'Distancia Rayado 1 a Rayado 2', 'text',$errors, $material, 'form-control', '', '') !!} --}}
        </div>
        <div class="row  mb-3">

            {!! armarInputCreateEdit('col', 'peso', 'Peso', 'text',$errors, $material, 'form-control', '', '') !!}

            {!! armarSelectArrayCreateEdit($cads, 'col', 'cad_id', 'CAD' , $errors, $material ,'form-control') !!}

            {!! armarSelectArrayCreateEdit($cartons, 'col', 'carton_id', 'Carton' , $errors, $material ,'form-control') !!}
            {!! armarSelectArrayCreateEdit($productTypes, 'col', 'product_type_id', 'Tipo de Producto' , $errors, $material ,'form-control') !!}
            {!! armarSelectArrayCreateEdit($styles, 'col', 'style_id', 'Estilo' , $errors, $material ,'form-control') !!}


          <!-- distancia_rayado2_corte2 -->
          {{-- {!! armarInputCreateEdit('col', 'distancia_rayado2_corte2', 'Distancia Rayado 2 a Corte 2', 'text',$errors, $material, 'form-control', '', '') !!} --}}


          <!-- numero_colores -->
          {{-- {!! armarInputCreateEdit('col', 'numero_colores', 'Número de Colores', 'text',$errors, $material, 'form-control', '', '') !!} --}}


          <!-- plano_cad -->
          {{-- {!! armarInputCreateEdit('col', 'plano_cad', 'Plano Cad', 'text',$errors, $material, 'form-control', '', '') !!} --}}


          <!-- carton -->
          {{-- {!! armarSelectArrayCreateEdit($cartons, 'col', 'carton_id', 'Carton' , $errors, $material ,'form-control') !!} --}}
        </div>
        <div class="row">
          <!-- product types -->

          <!-- style -->

          <!-- rayado -->
          {!! armarSelectArrayCreateEdit($vendedores, 'col', 'vendedor_id', 'Vendedor' , $errors, $material ,'form-control') !!}

          <!-- client -->
          {!! armarSelectArrayCreateEdit($clients, 'col', 'client_id', 'Cliente' , $errors, $material ,'form-control') !!}

          {!! armarSelectArrayCreateEdit($estados, 'col', 'active', 'Estado' , $errors, $material ,'form-control') !!}

        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.materials.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($material->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>
