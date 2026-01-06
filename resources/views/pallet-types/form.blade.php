<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">


          <!-- Codigo -->
          {!! armarInputCreateEditCustomColValue('col', 'codigo', 'Código', 'text',$errors, $palletType, 'form-control', '', '','-2','') !!}
          <!-- Descripcion -->
          {!! armarInputCreateEditCustomColValue('col', 'descripcion', 'Descripción', 'text',$errors, $palletType, 'form-control', '', '','-2','') !!}

           {!! armarInputCreateEditCustomColValue('col', 'largo', 'Largo', 'number',$errors, $palletType, 'form-control', '', '','-2',0) !!}
            {!! armarInputCreateEditCustomColValue('col', 'ancho', 'Ancho', 'number',$errors, $palletType, 'form-control', '', '','-2',0) !!}

            <div class="col-12">

            </div>

            {!! armarSelectArrayCreateEditCusmtomColValue(['chico'=>'Chico','mediano'=>'Mediano','grande'=>'Grande'], 'size_pallet_expedicion_26', 'Tamaño Pallet Expedición 26' , $errors, $palletType ,'form-control',true,true,'-2','chico') !!}

            {!! armarInputCreateEditCustomColValue('col', 'cant_pallet_expedicion_26', 'Cant. Pallet Expedición 26', 'number',$errors, $palletType, 'form-control', '', '','-2',0) !!}

            {!! armarSelectArrayCreateEditCusmtomColValue(['chico'=>'Chico','mediano'=>'Mediano','grande'=>'Grande'], 'size_pallet_expedicion_27', 'Tamaño Pallet Expedición 27' , $errors, $palletType ,'form-control',true,true,'-2','chico') !!}

            {!! armarInputCreateEditCustomColValue('col', 'cant_pallet_expedicion_27', 'Cant. Pallet Expedición 27', 'number',$errors, $palletType, 'form-control', '', '','-2',0) !!}

            {!! armarSelectArrayCreateEditCusmtomColValue(['chico'=>'Chico','mediano'=>'Mediano','grande'=>'Grande'], 'size_pallet_expedicion_28', 'Tamaño Pallet Expedición 28' , $errors, $palletType ,'form-control',true,true,'-2','chico') !!}

            {!! armarInputCreateEditCustomColValue('col', 'cant_pallet_expedicion_28', 'Cant. Pallet Expedición 28', 'number',$errors, $palletType, 'form-control', '', '','-2',0) !!}

            {!! armarSelectArrayCreateEditCusmtomColValue(['chico'=>'Chico','mediano'=>'Mediano','grande'=>'Grande'], 'size_pallet_expedicion_29', 'Tamaño Pallet Expedición 29' , $errors, $palletType ,'form-control',true,true,'-2','chico') !!}

            {!! armarInputCreateEditCustomColValue('col', 'cant_pallet_expedicion_29', 'Cant. Pallet Expedición 29', 'number',$errors, $palletType, 'form-control', '', '','-2',0) !!}


            {!! armarSelectArrayCreateEditCusmtomColValue(['chico'=>'Chico','mediano'=>'Mediano','grande'=>'Grande'], 'size_pallet_expedicion_30', 'Tamaño Pallet Expedición 30' , $errors, $palletType ,'form-control',true,true,'-2','mediano') !!}

            {!! armarInputCreateEditCustomColValue('col', 'cant_pallet_expedicion_30', 'Cant. Pallet Expedición 30', 'number',$errors, $palletType, 'form-control', '', '','-2',0) !!}

            {!! armarSelectArrayCreateEditCusmtomColValue(['chico'=>'Chico','mediano'=>'Mediano','grande'=>'Grande'], 'size_pallet_expedicion_36', 'Tamaño Pallet Expedición 36' , $errors, $palletType ,'form-control',true,true,'-2','mediano') !!}

            {!! armarInputCreateEditCustomColValue('col', 'cant_pallet_expedicion_36', 'Cant. Pallet Expedición 36', 'number',$errors, $palletType, 'form-control', '', '','-2',0) !!}


            {!! armarSelectArrayCreateEditCusmtomColValue(['chico'=>'Chico','mediano'=>'Mediano','grande'=>'Grande'], 'size_pallet_expedicion_40', 'Tamaño Pallet Expedición 40' , $errors, $palletType ,'form-control',true,true,'-2','grande') !!}

            {!! armarInputCreateEditCustomColValue('col', 'cant_pallet_expedicion_40', 'Cant. Pallet Expedición 40', 'number',$errors, $palletType, 'form-control', '', '','-2',0) !!}


            {!! armarSelectArrayCreateEditCusmtomColValue(['chico'=>'Chico','mediano'=>'Mediano','grande'=>'Grande'], 'size_pallet_expedicion_41', 'Tamaño Pallet Expedición 41' , $errors, $palletType ,'form-control',true,true,'-2','grande') !!}

            {!! armarInputCreateEditCustomColValue('col', 'cant_pallet_expedicion_41', 'Cant. Pallet Expedición 41', 'number',$errors, $palletType, 'form-control', '', '','-2',0) !!}

            {!! armarSelectArrayCreateEditCusmtomColValue(['chico'=>'Chico','mediano'=>'Mediano','grande'=>'Grande'], 'size_pallet_expedicion_42', 'Tamaño Pallet Expedición 42' , $errors, $palletType ,'form-control',true,true,'-2','grande') !!}

            {!! armarInputCreateEditCustomColValue('col', 'cant_pallet_expedicion_42', 'Cant. Pallet Expedición 42', 'number',$errors, $palletType, 'form-control', '', '','-2',0) !!}

            {!! armarSelectArrayCreateEditCusmtomColValue(['chico'=>'Chico','mediano'=>'Mediano','grande'=>'Grande'], 'size_pallet_expedicion_43', 'Tamaño Pallet Expedición 43' , $errors, $palletType ,'form-control',true,true,'-2','grande') !!}

            {!! armarInputCreateEditCustomColValue('col', 'cant_pallet_expedicion_43', 'Cant. Pallet Expedición 43', 'number',$errors, $palletType, 'form-control', '', '','-2',0) !!}


          <div class="col"></div>
          <div class="col"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.pallet-types.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($palletType->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>
