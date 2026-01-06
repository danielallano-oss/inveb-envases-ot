@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Cliente</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<!-- formulario: -->
		<form method="POST" action="{{ route('mantenedores.clients.update', $client->id) }}">
			@method('PUT')
			@csrf
			@include('clients.form', ['tipo' => "edit",'class' => 'disabled', 'clasificaciones' => $clasificaciones])
		</form>
	</div>
</div>
<input type="hidden" id="client_id" name="client_id" value="{{$client->id}}">
<input type="hidden" id="tipo" name="tipo" value="edit">

<div class="modal fade" id="modal-crear-planta">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header text-center">
				<div class="title"><h3>Registrar Instalación Cliente</h3></div>
			</div>
      
      <div class="modal-body">
        <div class="row">
          <div class="col-3">
            {!! armarInputCreateEdit_2('col', 'nombre_institucion', 'Nombre', 'text',$errors, null, 'form-control', true, false) !!}
          </div>
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado($palletTypes, 'tipo_pallet', 'Tipo Pallet' , $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-3">
            {!! armarInputCreateEdit_2('col','altura_pallet', 'Altura Pallet (mts.)', 'number', $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado([1 => "Si", 0=>"No"], 'sobresalir_carga', 'Permite Sobresalir Carga' , $errors, null ,'form-control',true,false) !!}
          </div>
        </div>

        <div class="row">
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado([1 => "Si", 0=>"No"], 'bulto_zunchado', 'Bulto Zunchado al Pallet' , $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado($palletTagFormat, 'formato_etiqueta', 'Formato Etiqueta Pallet' , $errors, null ,'form-control',true,false) !!}
          </div> 
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado([1 =>"1", 2=>"2", 3 =>"3", 4=>"4"], 'etiquetas_pallet', 'N° Etiqueta por Pallet' , $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado([1 => "Si", 0=>"No"], 'termocontraible', 'Termocontraible' , $errors, null ,'form-control',true,false) !!}
          </div>
        </div>

        <div class="row">
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado($fsc, 'fsc', 'FSC' , $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado($targetMarket, 'pais_mercado_destino', 'Pais Mercado/Destino' , $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado($palletQa, 'certificado_calidad', 'Certificado de Calidad' , $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado(['1'=>'Activo', '0'=>'Inactivo'],'active', 'Estado' , $errors, $client ,'form-control',true,false) !!}
          </div>
        </div>
        
        <br><br>
        <div class="row">
          <div class="col-12">
            <h4 style="color: #666;"><b>Lista de Contactos</b></h4>
            <hr class="hr" />
          </div>
        </div>
       
        <div class="row">
          <div class="col-12">
            <table class="table table-bordered">
              <tr>
                <td width="20%">
                  <div class="row">
                    <div class="col-12">
                      <h5 style="color: #666;"><b>Contacto N° 1</b></h5>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto', 'Nombre', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto', 'Cargo', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto', 'Correo Electrónico', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto', 'Teléfono', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                  </div>
                                
                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto', 'Comuna' , $errors, null ,'form-control',true,true) !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto', 'Dirección:', 'text', $errors, $client, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto', 'Estado' , $errors, $client ,'form-control',true,false) !!}
                    </div>
                  </div>
                </td>
                <td width="20%">
                  <div class="row">
                    <div class="col-12">
                      <h5 style="color: #666;"><b>Contacto N° 2</b></h5>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto_2', 'Nombre', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto_2', 'Cargo', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto_2', 'Correo Electrónico', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto_2', 'Teléfono', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                  </div>
                                
                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto_2', 'Comuna' , $errors, null ,'form-control',true,true) !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto_2', 'Dirección:', 'text', $errors, $client, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto_2', 'Estado' , $errors, $client ,'form-control',true,false) !!}
                    </div>
                  </div>
                </td>
                <td width="20%">
                  <div class="row">
                    <div class="col-12">
                      <h5 style="color: #666;"><b>Contacto N° 3</b></h5>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto_3', 'Nombre', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto_3', 'Cargo', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto_3', 'Correo Electrónico', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto_3', 'Teléfono', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                  </div>
                                
                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto_3', 'Comuna' , $errors, null ,'form-control',true,true) !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto_3', 'Dirección:', 'text', $errors, $client, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto_3', 'Estado' , $errors, $client ,'form-control',true,false) !!}
                    </div>
                  </div>
                </td>
                <td width="20%">
                  <div class="row">
                    <div class="col-12">
                      <h5 style="color: #666;"><b>Contacto N° 4</b></h5>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto_4', 'Nombre', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto_4', 'Cargo', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto_4', 'Correo Electrónico', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto_4', 'Teléfono', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                  </div>
                                
                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto_4', 'Comuna' , $errors, null ,'form-control',true,true) !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto_4', 'Dirección:', 'text', $errors, $client, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto_4', 'Estado' , $errors, $client ,'form-control',true,false) !!}
                    </div>
                  </div>
                </td>
                <td width="20%">
                  <div class="row">
                    <div class="col-12">
                      <h5 style="color: #666;"><b>Contacto N° 5</b></h5>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto_5', 'Nombre', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto_5', 'Cargo', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto_5', 'Correo Electrónico', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto_5', 'Teléfono', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                  </div>
                                
                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto_5', 'Comuna' , $errors, null ,'form-control',true,true) !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto_5', 'Dirección:', 'text', $errors, $client, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto_5', 'Estado' , $errors, $client ,'form-control',true,false) !!}
                    </div>
                  </div>
                </td>
              </tr>
            </table>
          </div>
        </div>       
      </div>

      <div class="modal-footer">
        <div class="text-center">
          <button class="btn btn-light" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-success" type="button" id="button_registrar_instalation">Registrar</button>
        </div>
      </div>
      
		</div>
	</div>
</div>

<div class="modal fade" id="modal-editar-planta">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header text-center">
				<div class="title"><h3>Editar Instalación Cliente</h3></div>
			</div>
      <div class="modal-body">
        <div class="row">
          <div class="col-3">
            {!! armarInputCreateEdit_2('col', 'nombre_institucion_edit', 'Nombre', 'text',$errors, null, 'form-control', true, false) !!}
          </div>          
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado($palletTypes, 'tipo_pallet_edit', 'Tipo Pallet' , $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-3">
            {!! armarInputCreateEdit_2('col','altura_pallet_edit', 'Altura Pallet (mts.)', 'number', $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado([1 => "Si", 0=>"No"], 'sobresalir_carga_edit', 'Permite Sobresalir Carga' , $errors, null ,'form-control',true,false) !!}
          </div>
        </div>
        <div class="row">          
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado([1 => "Si", 0=>"No"], 'bulto_zunchado_edit', 'Bulto Zunchado al Pallet' , $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado($palletTagFormat, 'formato_etiqueta_edit', 'Formato Etiqueta Pallet' , $errors, null ,'form-control',true,false) !!}
          </div> 
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado([1 =>"1", 2=>"2", 3 =>"3", 4=>"4"], 'etiquetas_pallet_edit', 'N° Etiqueta por Pallet' , $errors, null ,'form-control',true,false) !!}
           
          </div>
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado([1 => "Si", 0=>"No"], 'termocontraible_edit', 'Termocontraible' , $errors, null ,'form-control',true,false) !!}
          </div>
        </div>
        <div class="row">
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado($fsc, 'fsc_edit', 'FSC' , $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado($targetMarket, 'pais_mercado_destino_edit', 'Pais Mercado/Destino' , $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado($palletQa, 'certificado_calidad_edit', 'Certificado de Calidad' , $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado(['1'=>'Activo', '0'=>'Inactivo'],'active_edit', 'Estado' , $errors, $client ,'form-control',true,false) !!}
          </div>
        </div>
        <br><br>
        <div class="row">
          <div class="col-12">
            <h4 style="color: #666;"><b>Lista de Contactos</b></h4>
            <hr class="hr" />
          </div>
        </div>
       
        <div class="row">
          <div class="col-12">
            <table class="table table-bordered">
              <tr>
                <td width="20%">
                  <div class="row">
                    <div class="col-12">
                      <h5 style="color: #666;"><b>Contacto N° 1</b></h5>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto_edit', 'Nombre', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto_edit', 'Cargo', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto_edit', 'Correo Electrónico', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto_edit', 'Teléfono', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                  </div>
                                
                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto_edit', 'Comuna' , $errors, null ,'form-control',true,true) !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto_edit', 'Dirección:', 'text', $errors, $client, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto_edit', 'Estado' , $errors, $client ,'form-control',true,false) !!}
                    </div>
                  </div>
                </td>
                <td width="20%">
                  <div class="row">
                    <div class="col-12">
                      <h5 style="color: #666;"><b>Contacto N° 2</b></h5>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto_2_edit', 'Nombre', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto_2_edit', 'Cargo', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto_2_edit', 'Correo Electrónico', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto_2_edit', 'Teléfono', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                  </div>
                                
                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto_2_edit', 'Comuna' , $errors, null ,'form-control',true,true) !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto_2_edit', 'Dirección:', 'text', $errors, $client, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto_2_edit', 'Estado' , $errors, $client ,'form-control',true,false) !!}
                    </div>
                  </div>
                </td>
                <td width="20%">
                  <div class="row">
                    <div class="col-12">
                      <h5 style="color: #666;"><b>Contacto N° 3</b></h5>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto_3_edit', 'Nombre', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto_3_edit', 'Cargo', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto_3_edit', 'Correo Electrónico', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto_3_edit', 'Teléfono', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                  </div>
                                
                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto_3_edit', 'Comuna' , $errors, null ,'form-control',true,true) !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto_3_edit', 'Dirección:', 'text', $errors, $client, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto_3_edit', 'Estado' , $errors, $client ,'form-control',true,false) !!}
                    </div>
                  </div>
                </td>
                <td width="20%">
                  <div class="row">
                    <div class="col-12">
                      <h5 style="color: #666;"><b>Contacto N° 4</b></h5>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto_4_edit', 'Nombre', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto_4_edit', 'Cargo', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto_4_edit', 'Correo Electrónico', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto_4_edit', 'Teléfono', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                  </div>
                                
                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto_4_edit', 'Comuna' , $errors, null ,'form-control',true,true) !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto_4_edit', 'Dirección:', 'text', $errors, $client, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto_4_edit', 'Estado' , $errors, $client ,'form-control',true,false) !!}
                    </div>
                  </div>
                </td>
                <td width="20%">
                  <div class="row">
                    <div class="col-12">
                      <h5 style="color: #666;"><b>Contacto N° 5</b></h5>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto_5_edit', 'Nombre', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto_5_edit', 'Cargo', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto_5_edit', 'Correo Electrónico', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto_5_edit', 'Teléfono', 'text',$errors, $client,'form-control', '', '') !!}
                    </div>
                  </div>
                                
                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto_5_edit', 'Comuna' , $errors, null ,'form-control',true,true) !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto_5_edit', 'Dirección:', 'text', $errors, $client, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto_5_edit', 'Estado' , $errors, $client ,'form-control',true,false) !!}
                    </div>
                  </div>
                </td>
              </tr>
            </table>
          </div>
        </div> 
      </div>
      <div class="modal-footer">
        <div class="text-center">
          <button class="btn btn-light" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-success" type="button" id="button_actualizar_instalation">Actualizar</button>
        </div>
      </div>
      <input type="hidden" id="installation_id" name="installation_id" value="">
    </div>
	</div>
</div>

<div class="modal fade" id="modal-indicacion">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header text-center">
				<div class="title"><h3>Registrar Indicaciones Especiales</h3></div>
			</div>
      
      <div class="modal-body">
        <div class="row">
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado(['Caja' => "Caja", 'Placa'=>"Placa"], 'garantia_ect', 'Garantia ECT' , $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-9">&nbsp;</div>
        </div>
       <div class="row">
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_1', 'Indicación 1', 'text',$errors, null, 'form-control', '', '') !!}
          </div>  
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_2', 'Indicación 2', 'text',$errors, null, 'form-control', '', '') !!}
          </div>        
        </div>
        <div class="row">
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_3', 'Indicación 3', 'text',$errors, null, 'form-control', '', '') !!}
          </div> 
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_4', 'Indicación 4', 'text',$errors, null, 'form-control', '', '') !!}
          </div>         
        </div>
        <div class="row">
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_5', 'Indicación 5', 'text',$errors, null, 'form-control', '', '') !!}
          </div>
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_6', 'Indicación 6', 'text',$errors, null, 'form-control', '', '') !!}
          </div>          
        </div>
        <div class="row">
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_7', 'Indicación 7', 'text',$errors, null, 'form-control', '', '') !!}
          </div>          
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_8', 'Indicación 8', 'text',$errors, null, 'form-control', '', '') !!}
          </div>          
        </div>
        <div class="row">
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_9', 'Indicación 9', 'text',$errors, null, 'form-control', '', '') !!}
          </div>          
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_10', 'Indicación 10', 'text',$errors, null, 'form-control', '', '') !!}
          </div>          
        </div>           
      </div>

      <div class="modal-footer">
        <div class="text-center">
          <button class="btn btn-light" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-success" type="button" id="button_registrar_indicacion">Registrar</button>
        </div>
      </div>
      
		</div>
	</div>
</div>

<div class="modal fade" id="modal-editar-indicacion">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header text-center">
				<div class="title"><h3>Editar Indicaciones Especiales Cliente</h3></div>
			</div>
      
      <div class="modal-body">
        <div class="row">
          <div class="col-3">
            {!! armarSelectArrayCreateEditOTSeparado(['Caja' => "Caja", 'Placa'=>"Placa"], 'garantia_ect_edit', 'Garantia ECT' , $errors, null ,'form-control',true,false) !!}
          </div>
          <div class="col-9">&nbsp;</div>
        </div>
       <div class="row">
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_1_edit', 'Indicación 1', 'text',$errors, null, 'form-control', '', '') !!}
          </div>  
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_2_edit', 'Indicación 2', 'text',$errors, null, 'form-control', '', '') !!}
          </div>        
        </div>
        <div class="row">
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_3_edit', 'Indicación 3', 'text',$errors, null, 'form-control', '', '') !!}
          </div> 
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_4_edit', 'Indicación 4', 'text',$errors, null, 'form-control', '', '') !!}
          </div>         
        </div>
        <div class="row">
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_5_edit', 'Indicación 5', 'text',$errors, null, 'form-control', '', '') !!}
          </div>
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_6_edit', 'Indicación 6', 'text',$errors, null, 'form-control', '', '') !!}
          </div>          
        </div>
        <div class="row">
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_7_edit', 'Indicación 7', 'text',$errors, null, 'form-control', '', '') !!}
          </div>          
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_8_edit', 'Indicación 8', 'text',$errors, null, 'form-control', '', '') !!}
          </div>          
        </div>
        <div class="row">
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_9_edit', 'Indicación 9', 'text',$errors, null, 'form-control', '', '') !!}
          </div>          
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_10_edit', 'Indicación 10', 'text',$errors, null, 'form-control', '', '') !!}
          </div>          
        </div>           
      </div>

      <div class="modal-footer">
        <div class="text-center">
          <button class="btn btn-light" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-success" type="button" id="button_actualizar_indicacion">Editar</button>
        </div>
      </div>
      <input type="hidden" id="indicacion_id" name="indicacion_id" value="">
		</div>
	</div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="informacion">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Informacion</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-success" role="alert">
          <span id='message_info'></span>
        </div>        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

@endsection
@section('myjsfile')
<script>
 	$("#button_registrar_instalation").click(function () {
		var cliente					      = $('#client_id').val();
		var nombre     				    = $('#nombre_institucion').val();
    var tipo_pallet 			    = $('#tipo_pallet').val();
    var altura_pallet 			  = $('#altura_pallet').val();
    var sobresalir_carga  		= $('#sobresalir_carga').val();
    var bulto_zunchado 			  = $('#bulto_zunchado').val();
		var formato_etiqueta 		  = $('#formato_etiqueta').val();
    var etiquetas_pallet 		  = $('#etiquetas_pallet').val();
    var termocontraible  		  = $('#termocontraible').val();
    var fsc 					        = $('#fsc').val();
		var pais_mercado_destino	= $('#pais_mercado_destino').val();
    var certificado_calidad  	= $('#certificado_calidad').val();
    var active 		            = $('#active').val();
    var nombre_contacto				= $('#nombre_contacto').val();
		var cargo_contacto     		= $('#cargo_contacto').val();
    var email_contacto 			  = $('#email_contacto').val();
    var phone_contacto 			  = $('#phone_contacto').val().replace("+", "*");
    var direccion_contacto  	= $('#direccion_contacto').val();
    var comuna_contacto 			= $('#comuna_contacto').val();
		var active_contacto 		  = $('#active_contacto').val();
    var nombre_contacto_2			= $('#nombre_contacto_2').val();
		var cargo_contacto_2     	= $('#cargo_contacto_2').val();
    var email_contacto_2 			= $('#email_contacto_2').val();
    var phone_contacto_2 			= $('#phone_contacto_2').val().replace("+", "*");
    var direccion_contacto_2  = $('#direccion_contacto_2').val();
    var comuna_contacto_2 		= $('#comuna_contacto_2').val();
		var active_contacto_2 		= $('#active_contacto_2').val();
    var nombre_contacto_3			= $('#nombre_contacto_3').val();
		var cargo_contacto_3     	= $('#cargo_contacto_3').val();
    var email_contacto_3 			= $('#email_contacto_3').val();
    var phone_contacto_3 			= $('#phone_contacto_3').val().replace("+", "*");
    var direccion_contacto_3  = $('#direccion_contacto_3').val();
    var comuna_contacto_3 		= $('#comuna_contacto_3').val();
		var active_contacto_3 		= $('#active_contacto_3').val();
    var nombre_contacto_4			= $('#nombre_contacto_4').val();
		var cargo_contacto_4     	= $('#cargo_contacto_4').val();
    var email_contacto_4 			= $('#email_contacto_4').val();
    var phone_contacto_4 			= $('#phone_contacto_4').val().replace("+", "*");
    var direccion_contacto_4  = $('#direccion_contacto_4').val();
    var comuna_contacto_4 		= $('#comuna_contacto_4').val();
		var active_contacto_4 		= $('#active_contacto_4').val();
    var nombre_contacto_5			= $('#nombre_contacto_5').val();
		var cargo_contacto_5     	= $('#cargo_contacto_5').val();
    var email_contacto_5 			= $('#email_contacto_5').val();
    var phone_contacto_5 			= $('#phone_contacto_5').val().replace("+", "*");
    var direccion_contacto_5  = $('#direccion_contacto_5').val();
    var comuna_contacto_5 		= $('#comuna_contacto_5').val();
		var active_contacto_5 		= $('#active_contacto_5').val();
    var tipo 		              = $('#tipo').val();
    return $.ajax({
      type: "GET",
      url: "../store_installation",
      data: "cliente="+cliente+"&nombre="+nombre+"&tipo_pallet="+tipo_pallet+"&altura_pallet="+altura_pallet+"&active="+active+
            "&sobresalir_carga="+sobresalir_carga+"&bulto_zunchado="+bulto_zunchado+"&formato_etiqueta="+formato_etiqueta+
            "&etiquetas_pallet="+etiquetas_pallet+"&termocontraible="+termocontraible+"&fsc="+fsc+"&pais_mercado_destino="+pais_mercado_destino+
            "&certificado_calidad="+certificado_calidad+"&nombre_contacto="+nombre_contacto+"&cargo_contacto="+cargo_contacto+
            "&email_contacto="+email_contacto+"&phone_contacto="+phone_contacto+"&direccion_contacto="+direccion_contacto+
            "&comuna_contacto="+comuna_contacto+"&active_contacto="+active_contacto+"&nombre_contacto_2="+nombre_contacto_2+
            "&cargo_contacto_2="+cargo_contacto_2+"&email_contacto_2="+email_contacto_2+"&phone_contacto_2="+phone_contacto_2+
            "&direccion_contacto_2="+direccion_contacto_2+"&comuna_contacto_2="+comuna_contacto_2+"&active_contacto_2="+active_contacto_2+
            "&nombre_contacto_3="+nombre_contacto_3+"&cargo_contacto_3="+cargo_contacto_3+"&email_contacto_3="+email_contacto_3+
            "&phone_contacto_3="+phone_contacto_3+"&direccion_contacto_3="+direccion_contacto_3+"&comuna_contacto_3="+comuna_contacto_3+
            "&active_contacto_3="+active_contacto_3+"&nombre_contacto_4="+nombre_contacto_4+"&cargo_contacto_4="+cargo_contacto_4+
            "&email_contacto_4="+email_contacto_4+"&phone_contacto_4="+phone_contacto_4+"&direccion_contacto_4="+direccion_contacto_4+
            "&comuna_contacto_4="+comuna_contacto_4+"&active_contacto_4="+active_contacto_4+"&nombre_contacto_5="+nombre_contacto_5+
            "&cargo_contacto_5="+cargo_contacto_5+"&email_contacto_5="+email_contacto_5+"&phone_contacto_5="+phone_contacto_5+
            "&direccion_contacto_5="+direccion_contacto_5+"&comuna_contacto_5="+comuna_contacto_5+"&active_contacto_5="+active_contacto_5+"&tipo="+tipo,
      success: function (data) {
        console.log(data);
  
        $('#nombre_institucion,#altura_pallet,#formato_etiqueta,#etiquetas_pallet,#nombre_contacto,#cargo_contacto,#email_contacto,#phone_contacto,#direccion_contacto,#nombre_contacto_2,#cargo_contacto_2,#email_contacto_2,#phone_contacto_2,#direccion_2_contacto,#nombre_contacto_3,#cargo_contacto_3,#email_contacto_3,#phone_contacto_3,#direccion_3_contacto,#nombre_contacto_3,#cargo_contacto_3,#email_contacto_3,#phone_contacto_3,#direccion_3_contacto,#nombre_contacto_4,#cargo_contacto_4,#email_contacto_4,#phone_contacto_4,#direccion_4_contacto,#nombre_contacto_5,#cargo_contacto_5,#email_contacto_5,#phone_contacto_5,#direccion_5_contacto').val('');
        $('#tipo_pallet,#sobresalir_carga,#bulto_zunchado,#termocontraible,#fsc,#pais_mercado_destino,#certificado_calidad,#comuna_contacto,#active_contacto,#comuna_contacto_2,#active_contacto_2,#comuna_contacto_3,#active_contacto_3,#comuna_contacto_4,#active_contacto_4,#comuna_contacto_5,#active_contacto_5')
          .val('')
          .selectpicker('refresh'); 
        

        $("#modal-crear-planta").modal('hide');
        $("#message_info").text("Registro de Instalación realizada con exito");
        $("#informacion").modal('show');
        $("#client_installations tbody").html('');
        $("#client_installations tbody").html(data);
                    
      },
      error: function(e) {
        console.log(e.responseText);
      },
      async:true
    });
	});
 
  $('#modal-editar-planta').on('show.bs.modal',function(event){
    var button = $(event.relatedTarget) //obtenemos el boton que presionamos
    var instalacion = button.data('editar');
    
    return $.ajax({
      type: "GET",
      url: "../edit_installation",
      data: "instalacion="+instalacion,
      success: function (data) {
        console.log(data);
        $('#nombre_institucion_edit').val(data['nombre']);
        $('#tipo_pallet_edit').val(data['tipo_pallet']).selectpicker('refresh');
        $('#altura_pallet_edit').val(data['altura_pallet']);
        $('#sobresalir_carga_edit').val(data['sobresalir_carga']).selectpicker('refresh');
        $('#bulto_zunchado_edit').val(data['bulto_zunchado']).selectpicker('refresh');
        $('#formato_etiqueta_edit').val(data['formato_etiqueta']).selectpicker('refresh');
        $('#etiquetas_pallet_edit').val(data['etiquetas_pallet']).selectpicker('refresh');
        $('#termocontraible_edit').val(data['termocontraible']).selectpicker('refresh');
        $('#fsc_edit').val(data['fsc']).selectpicker('refresh');
        $('#pais_mercado_destino_edit').val(data['pais_mercado_destino']).selectpicker('refresh');
        $('#certificado_calidad_edit').val(data['certificado_calidad']).selectpicker('refresh');
        $('#active_edit').val(data['active']).selectpicker('refresh');
        $('#nombre_contacto_edit').val(data['nombre_contacto']);
        $('#cargo_contacto_edit').val(data['cargo_contacto']);
        $('#email_contacto_edit').val(data['email_contacto']);
        $('#phone_contacto_edit').val(data['phone_contacto']);
        $('#comuna_contacto_edit').val(data['comuna_contacto']).selectpicker('refresh');
        $('#direccion_contacto_edit').val(data['direccion_contacto']);
        $('#active_contacto_edit').val(data['active_contacto']).selectpicker('refresh');
        $('#nombre_contacto_2_edit').val(data['nombre_contacto_2']);
        $('#cargo_contacto_2_edit').val(data['cargo_contacto_2']);
        $('#email_contacto_2_edit').val(data['email_contacto_2']);
        $('#phone_contacto_2_edit').val(data['phone_contacto_2']);
        $('#comuna_contacto_2_edit').val(data['comuna_contacto_2']).selectpicker('refresh');
        $('#direccion_contacto_2_edit').val(data['direccion_contacto_2']);
        $('#active_contacto_2_edit').val(data['active_contacto_2']).selectpicker('refresh');
        $('#nombre_contacto_3_edit').val(data['nombre_contacto_3']);
        $('#cargo_contacto_3_edit').val(data['cargo_contacto_3']);
        $('#email_contacto_3_edit').val(data['email_contacto_3']);
        $('#phone_contacto_3_edit').val(data['phone_contacto_3']);
        $('#comuna_contacto_3_edit').val(data['comuna_contacto_3']).selectpicker('refresh');
        $('#direccion_contacto_3_edit').val(data['direccion_contacto_3']);
        $('#active_contacto_3_edit').val(data['active_contacto_3']).selectpicker('refresh');
        $('#nombre_contacto_4_edit').val(data['nombre_contacto_4']);
        $('#cargo_contacto_4_edit').val(data['cargo_contacto_4']);
        $('#email_contacto_4_edit').val(data['email_contacto_4']);
        $('#phone_contacto_4_edit').val(data['phone_contacto_4']);
        $('#comuna_contacto_4_edit').val(data['comuna_contacto_4']).selectpicker('refresh');
        $('#direccion_contacto_4_edit').val(data['direccion_contacto_4']);
        $('#active_contacto_4_edit').val(data['active_contacto_4']).selectpicker('refresh');
        $('#nombre_contacto_5_edit').val(data['nombre_contacto_5']);
        $('#cargo_contacto_5_edit').val(data['cargo_contacto_5']);
        $('#email_contacto_5_edit').val(data['email_contacto_5']);
        $('#phone_contacto_5_edit').val(data['phone_contacto_5']);
        $('#comuna_contacto_5_edit').val(data['comuna_contacto_5']).selectpicker('refresh');
        $('#direccion_contacto_5_edit').val(data['direccion_contacto_5']);
        $('#active_contacto_5_edit').val(data['active_contacto_5']).selectpicker('refresh');
        $('#installation_id').val(instalacion);                                    
      },
      error: function(e) {
        console.log(e.responseText);
      },
      async:true
    });
  });
 
  $("#button_actualizar_instalation").click(function () {
    
    var cliente					      = $('#client_id').val();
		var installation  			  = $('#installation_id').val();
		var nombre     				    = $('#nombre_institucion_edit').val();
    var tipo_pallet 			    = $('#tipo_pallet_edit').val();
    var altura_pallet 			  = $('#altura_pallet_edit').val();
    var sobresalir_carga  		= $('#sobresalir_carga_edit').val();
    var bulto_zunchado 			  = $('#bulto_zunchado_edit').val();
		var formato_etiqueta 		  = $('#formato_etiqueta_edit').val();
    var etiquetas_pallet 		  = $('#etiquetas_pallet_edit').val();
    var termocontraible  		  = $('#termocontraible_edit').val();
    var fsc 					        = $('#fsc_edit').val();
		var pais_mercado_destino	= $('#pais_mercado_destino_edit').val();
    var certificado_calidad  	= $('#certificado_calidad_edit').val();
    var active 		            = $('#active_edit').val();
    var nombre_contacto				= $('#nombre_contacto_edit').val();
		var cargo_contacto     		= $('#cargo_contacto_edit').val();
    var email_contacto 			  = $('#email_contacto_edit').val();
    var phone_contacto 			  = $('#phone_contacto_edit').val().replace("+", "*");
    var direccion_contacto  	= $('#direccion_contacto_edit').val();
    var comuna_contacto 			= $('#comuna_contacto_edit').val();
		var active_contacto 		  = $('#active_contacto_edit').val();
    var nombre_contacto_2			= $('#nombre_contacto_2_edit').val();
		var cargo_contacto_2     	= $('#cargo_contacto_2_edit').val();
    var email_contacto_2 			= $('#email_contacto_2_edit').val();
    var phone_contacto_2 			= $('#phone_contacto_2_edit').val().replace("+", "*");
    var direccion_contacto_2  = $('#direccion_contacto_2_edit').val();
    var comuna_contacto_2 		= $('#comuna_contacto_2_edit').val();
		var active_contacto_2 		= $('#active_contacto_2_edit').val();
    var nombre_contacto_3			= $('#nombre_contacto_3_edit').val();
		var cargo_contacto_3     	= $('#cargo_contacto_3_edit').val();
    var email_contacto_3 			= $('#email_contacto_3_edit').val();
    var phone_contacto_3 			= $('#phone_contacto_3_edit').val().replace("+", "*");
    var direccion_contacto_3  = $('#direccion_contacto_3_edit').val();
    var comuna_contacto_3 		= $('#comuna_contacto_3_edit').val();
		var active_contacto_3 		= $('#active_contacto_3_edit').val();
    var nombre_contacto_4			= $('#nombre_contacto_4_edit').val();
		var cargo_contacto_4     	= $('#cargo_contacto_4_edit').val();
    var email_contacto_4 			= $('#email_contacto_4_edit').val();
    var phone_contacto_4 			= $('#phone_contacto_4_edit').val().replace("+", "*");
    var direccion_contacto_4  = $('#direccion_contacto_4_edit').val();
    var comuna_contacto_4 		= $('#comuna_contacto_4_edit').val();
		var active_contacto_4 		= $('#active_contacto_4_edit').val();
    var nombre_contacto_5			= $('#nombre_contacto_5_edit').val();
		var cargo_contacto_5     	= $('#cargo_contacto_5_edit').val();
    var email_contacto_5 			= $('#email_contacto_5_edit').val();
    var phone_contacto_5 			= $('#phone_contacto_5_edit').val().replace("+", "*");
    var direccion_contacto_5  = $('#direccion_contacto_5_edit').val();
    var comuna_contacto_5 		= $('#comuna_contacto_5_edit').val();
		var active_contacto_5 		= $('#active_contacto_5_edit').val();

    return $.ajax({
      type: "GET",
      url: "../update_installation",
      data: "installation="+installation+"&cliente="+cliente+"&nombre="+nombre+"&tipo_pallet="+tipo_pallet+"&altura_pallet="+altura_pallet+"&active="+active+
            "&sobresalir_carga="+sobresalir_carga+"&bulto_zunchado="+bulto_zunchado+"&formato_etiqueta="+formato_etiqueta+
            "&etiquetas_pallet="+etiquetas_pallet+"&termocontraible="+termocontraible+"&fsc="+fsc+"&pais_mercado_destino="+pais_mercado_destino+
            "&certificado_calidad="+certificado_calidad+"&nombre_contacto="+nombre_contacto+"&cargo_contacto="+cargo_contacto+
            "&email_contacto="+email_contacto+"&phone_contacto="+phone_contacto+"&direccion_contacto="+direccion_contacto+
            "&comuna_contacto="+comuna_contacto+"&active_contacto="+active_contacto+"&nombre_contacto_2="+nombre_contacto_2+
            "&cargo_contacto_2="+cargo_contacto_2+"&email_contacto_2="+email_contacto_2+"&phone_contacto_2="+phone_contacto_2+
            "&direccion_contacto_2="+direccion_contacto_2+"&comuna_contacto_2="+comuna_contacto_2+"&active_contacto_2="+active_contacto_2+
            "&nombre_contacto_3="+nombre_contacto_3+"&cargo_contacto_3="+cargo_contacto_3+"&email_contacto_3="+email_contacto_3+
            "&phone_contacto_3="+phone_contacto_3+"&direccion_contacto_3="+direccion_contacto_3+"&comuna_contacto_3="+comuna_contacto_3+
            "&active_contacto_3="+active_contacto_3+"&nombre_contacto_4="+nombre_contacto_4+"&cargo_contacto_4="+cargo_contacto_4+
            "&email_contacto_4="+email_contacto_4+"&phone_contacto_4="+phone_contacto_4+"&direccion_contacto_4="+direccion_contacto_4+
            "&comuna_contacto_4="+comuna_contacto_4+"&active_contacto_4="+active_contacto_4+"&nombre_contacto_5="+nombre_contacto_5+
            "&cargo_contacto_5="+cargo_contacto_5+"&email_contacto_5="+email_contacto_5+"&phone_contacto_5="+phone_contacto_5+
            "&direccion_contacto_5="+direccion_contacto_5+"&comuna_contacto_5="+comuna_contacto_5+"&active_contacto_5="+active_contacto_5,
      success: function (data) {
          
        $('#nombre_institucion_edit,#altura_pallet_edit,#formato_etiqueta_edit,#etiquetas_pallet_edit,#nombre_contacto_edit,#cargo_contacto_edit,#email_contacto_edit,#phone_contacto_edit,#direccion_contacto_edit,#nombre_contacto_2_edit,#cargo_contacto_2_edit,#email_contacto_2_edit,#phone_contacto_2_edit,#direccion_2_contacto_edit,#nombre_contacto_3_edit,#cargo_contacto_3_edit,#email_contacto_3_edit,#phone_contacto_3_edit,#direccion_3_contacto_edit,#nombre_contacto_3_edit,#cargo_contacto_3_edit,#email_contacto_3_edit,#phone_contacto_3_edit,#direccion_3_contacto_edit,#nombre_contacto_4_edit,#cargo_contacto_4_edit,#email_contacto_4_edit,#phone_contacto_4_edit,#direccion_4_contacto_edit,#nombre_contacto_5_edit,#cargo_contacto_5_edit,#email_contacto_5_edit,#phone_contacto_5_edit,#direccion_5_contacto_edit').val('');
        $('#tipo_pallet_edit,#sobresalir_carga_edit,#bulto_zunchado_edit,#termocontraible_edit,#fsc_edit,#pais_mercado_destino_edit,#certificado_calidad_edit,#comuna_contacto_edit,#active_contacto_edit,#comuna_contacto_2_edit,#active_contacto_2_edit,#comuna_contacto_3_edit,#active_contacto_3_edit,#comuna_contacto_4_edit,#active_contacto_4_edit,#comuna_contacto_5_edit,#active_contacto_5_edit')
          .val('')
          .selectpicker('refresh');       

        $("#modal-editar-planta").modal('hide');
        $("#message_info").text("Actualización de Instalación realizada con exito");
        $("#informacion").modal('show');
        $("#client_installations tbody").html('');
        $("#client_installations tbody").html(data);
                    
      },
      error: function(e) {
        console.log(e.responseText);
      },
      async:true
    });
	});

  $('#informacion').on('show.bs.modal', function(){
    var myModal = $(this);
    clearTimeout(myModal.data('hideInterval'));
    myModal.data('hideInterval', setTimeout(function(){
        myModal.modal('hide');
    }, 2500));
  });

  $('#modal-editar-indicacion').on('show.bs.modal',function(event){
    var button = $(event.relatedTarget) //obtenemos el boton que presionamos
    var indicacion = button.data('editar');
    
    return $.ajax({
      type: "GET",
      url: "../edit_indicacion",
      data: "indicacion="+indicacion,
      success: function (data) {
        console.log(data);
        $('#garantia_ect_edit').val(data['garantia_ect']).selectpicker('refresh');
        $('#campo_1_edit').val(data['campo_1']);
        $('#campo_2_edit').val(data['campo_2']);
        $('#campo_3_edit').val(data['campo_3']);
        $('#campo_4_edit').val(data['campo_4']);
        $('#campo_5_edit').val(data['campo_5']);
        $('#campo_6_edit').val(data['campo_6']);
        $('#campo_7_edit').val(data['campo_7']);
        $('#campo_8_edit').val(data['campo_8']);
        $('#campo_9_edit').val(data['campo_9']);
        $('#campo_10_edit').val(data['campo_10']);
        
        $('#indicacion_id').val(indicacion);                                    
      },
      error: function(e) {
        console.log(e.responseText);
      },
      async:true
    });
  });

  $("#button_actualizar_indicacion").click(function () {
    
    var cliente				= $('#client_id').val();
		var garantia_ect  = $('#garantia_ect_edit').val();
    var campo_1 			= $('#campo_1_edit').val();
    var campo_2 			= $('#campo_2_edit').val();
    var campo_3  		  = $('#campo_3_edit').val();
    var campo_4 			= $('#campo_4_edit').val();
		var campo_5 		  = $('#campo_5_edit').val();
    var campo_6 		  = $('#campo_6_edit').val();
    var campo_7  		  = $('#campo_7_edit').val();
    var campo_8 			= $('#campo_8_edit').val();
		var campo_9	      = $('#campo_9_edit').val();
    var campo_10  	  = $('#campo_10_edit').val();
    var indicacion   = $('#indicacion_id').val();
    
    return $.ajax({
      type: "GET",
      url: "../update_indicacion",
      data: "indicacion="+indicacion+"&cliente="+cliente+"&garantia_ect="+garantia_ect+"&campo_1="+campo_1+"&campo_2="+campo_2+"&campo_3="+campo_3+
            "&campo_4="+campo_4+"&campo_5="+campo_5+"&campo_6="+campo_6+"&campo_7="+campo_7+"&campo_8="+campo_8+"&campo_9="+campo_9+"&campo_10="+campo_10,
      success: function (data) {
          
        $('#campo_1_edit,#campo_2_edit,#campo_3_edit,#campo_4_edit,#campo_5_edit,#campo_6_edit,#campo_7_edit,#campo_8_edit,#campo_9_edit,#campo_10_edit').val('');
        $('#garantia_ect_edit')
          .val('')
          .selectpicker('refresh');       

        $("#modal-editar-indicacion").modal('hide');
        $("#message_info").text("Actualización de indicaciones especiales realizada con éxito");
        $("#informacion").modal('show');
        $("#client_indicaciones tbody").html('');
        $("#client_indicaciones tbody").html(data);
                    
      },
      error: function(e) {
        console.log(e.responseText);
      },
      async:true
    });
	});

  $("#button_registrar_indicacion").click(function () {
		var cliente				= $('#client_id').val();
		var garantia_ect  = $('#garantia_ect').val();
    var campo_1 			= $('#campo_1').val();
    var campo_2 			= $('#campo_2').val();
    var campo_3  		  = $('#campo_3').val();
    var campo_4 			= $('#campo_4').val();
		var campo_5 		  = $('#campo_5').val();
    var campo_6 		  = $('#campo_6').val();
    var campo_7  		  = $('#campo_7').val();
    var campo_8 			= $('#campo_8').val();
		var campo_9	      = $('#campo_9').val();
    var campo_10  	  = $('#campo_10').val();
    console.log(cliente,garantia_ect,campo_1,campo_2);
    return $.ajax({
      type: "GET",
      url: "../store_indicacion",
      data: "cliente="+cliente+"&garantia_ect="+garantia_ect+"&campo_1="+campo_1+"&campo_2="+campo_2+"&campo_3="+campo_3+
            "&campo_4="+campo_4+"&campo_5="+campo_5+"&campo_6="+campo_6+"&campo_7="+campo_7+"&campo_8="+campo_8+"&campo_9="+campo_9+"&campo_10="+campo_10,
      success: function (data) {
        console.log(data);
  
        $('#campo_1,#campo_2,#campo_3,#campo_4,#campo_5,#campo_6,#campo_7,#campo_8,#campo_9,#campo_10').val('');
        $('#garantia_ect')
          .val('')
          .selectpicker('refresh'); 
        

        $("#modal-indicacion").modal('hide');
        $("#message_info").text("Registro de indicaciones especiales realizada con exito");
        $("#informacion").modal('show');
        $("#client_indicaciones tbody").html('');
        $("#client_indicaciones tbody").html(data);
                    
      },
      error: function(e) {
        console.log(e.responseText);
      },
      async:true
    });
	});

</script>
@endsection