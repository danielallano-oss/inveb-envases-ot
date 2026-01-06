<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          <!-- RUT -->
          {!! armarInputCreateEdit('col', 'rut', 'Rut', 'text',$errors, $user, 'form-control', $class, '') !!}

          <!-- Nombre -->
          {!! armarInputCreateEdit('col', 'nombre', 'Nombre', 'text',$errors, $user, 'form-control', '', '') !!}

          {{-- last_name --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
          {!! armarInputCreateEdit('col', 'apellido', 'Apellido', 'text',$errors, $user, 'form-control', '', '') !!}

          {{-- Nombre SAP --}}
          {!! armarInputCreateEdit('col', 'nombre_sap', 'Nombre SAP', 'text',$errors, $user, 'form-control', '', '') !!}
        </div>
        <div class="row">
          {{-- Mail --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
          {!! armarInputCreateEdit('col', 'email', 'Mail', 'mail',$errors, $user, 'form-control', '', '') !!}

          {{-- Contraseña --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
          {!! armarInputCreateEdit('col', 'password', 'Contraseña', 'password',$errors, $user, 'form-control', '', '') !!}

          {{-- Repite contraseña --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
          {!! armarInputCreateEdit('col', 'password_confirmation', 'Repite Contraseña', 'password',$errors, $user, 'form-control', '', '') !!}

          {{-- Role --}} <!-- ($options, $formato, $key, $title , $errors, $objeto ,$class_select) -->
          {!! armarSelectArrayCreateEditUser($profiles, 'role_id', 'Rol' , $errors, $user ,'form-control',true,true) !!}
        </div>
        <div class="row">
          <!-- Si es vendedor se puede editar el jefe de este -->
          <div id="jefe-venta" style="display: none; width: 320px;">
            {{-- Role --}} <!-- ($options, $formato, $key, $title , $errors, $objeto ,$class_select) -->
            {!! armarSelectArrayCreateEditUser($jefesVenta, 'jefe_id', 'Jefe' , $errors, $user ,'form-control',true,true) !!}
          </div>
          <div id="sala-corte" style="display: none; width: 320px;">
            {{-- Role --}} <!-- ($options, $formato, $key, $title , $errors, $objeto ,$class_select) -->
            {!! armarSelectArrayCreateEditUser($salas_cortes, 'sala_corte_id', 'Sala Corte' , $errors, $user ,'form-control',true,true) !!}
          </div>         
          <div id="vendedor-cliente" style="display: none; width: 320px;">
            {{-- Role --}} <!-- ($options, $formato, $key, $title , $errors, $objeto ,$class_select) -->
            {{--{!! armarSelectArrayCreateEdit($clientes, 'col', 'cliente_id', 'Cliente' , $errors, $user ,'form-control') !!}--}}
            {!! armarSelectArrayCreateEditUser($clientes, 'cliente_id', 'Cliente' , $errors, $user ,'form-control',true,true) !!}
          </div>
          <div id="vendedor-responsable" style="display: none; width: 320px;">
            {{-- Role --}} <!-- ($options, $formato, $key, $title , $errors, $objeto ,$class_select) -->
            {!! armarSelectArrayCreateEditUser($vendedores, 'responsable_id', 'Responsable' , $errors, $user ,'form-control',true,true) !!}
          </div>  
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 text-right">
  <a href="{{ route('mantenedores.users.list') }}" class="btn btn-light">Cancelar</a>
  <button type="submit" class="btn btn-success">{{ isset($user->id) ? __('Actualizar') : __('Guardar') }}</button>
</div>