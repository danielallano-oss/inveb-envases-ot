@extends('layouts.index', ['dontnotify' => true])


@section('content')
<h1 class="page-title">Crear Cliente</h1>

<div class="row mb-3">
  <div class="col-12 p-2">
    <form id="clientsForm" method="POST" action="{{ route('mantenedores.clients.store') }}">
      @csrf
      @include('clients.form', ['tipo' => "create",'client' => null,'class' => '', 'clasificaciones' => $clasificaciones])
      <input type="hidden" id="codigo_carga" name="codigo_carga" value="{{$codigo_carga}}">
    </form>
  </div>
</div>
<input type="hidden" id="client_id" name="client_id" value="{{$codigo_carga}}">
<input type="hidden" id="tipo" name="tipo" value="create">


<div class="modal fade" id="modal-indicacion">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header text-center">
				<div class="title"><h3>Registrar Instrucción Cliente</h3></div>
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
            {!! armarInputCreateEdit_2('col', 'campo_1', 'Campo Libre 1', 'text',$errors, null, 'form-control', '', '') !!}
          </div>
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_2', 'Campo Libre 2', 'text',$errors, null, 'form-control', '', '') !!}
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_3', 'Campo Libre 3', 'text',$errors, null, 'form-control', '', '') !!}
          </div>
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_4', 'Campo Libre 4', 'text',$errors, null, 'form-control', '', '') !!}
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_5', 'Campo Libre 5', 'text',$errors, null, 'form-control', '', '') !!}
          </div>
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_6', 'Campo Libre 6', 'text',$errors, null, 'form-control', '', '') !!}
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_7', 'Campo Libre 7', 'text',$errors, null, 'form-control', '', '') !!}
          </div>
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_8', 'Campo Libre 8', 'text',$errors, null, 'form-control', '', '') !!}
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_9', 'Campo Libre 9', 'text',$errors, null, 'form-control', '', '') !!}
          </div>
          <div class="col-6">
            {!! armarInputCreateEdit_2('col', 'campo_10', 'Campo Libre 10', 'text',$errors, null, 'form-control', '', '') !!}
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
            {!! armarSelectArrayCreateEditOTSeparado(['1'=>'Activo', '0'=>'Inactivo'],'active', 'Estado' , $errors, null ,'form-control',true,false) !!}
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
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto', 'Nombre', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto', 'Cargo', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto', 'Correo Electrónico', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto', 'Teléfono', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto', 'Comuna' , $errors, null ,'form-control',true,true) !!}

                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto', 'Dirección:', 'text', $errors, null, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto', 'Estado' , $errors, null ,'form-control',true,false) !!}
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
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto_2', 'Nombre', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto_2', 'Cargo', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto_2', 'Correo Electrónico', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto_2', 'Teléfono', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto_2', 'Comuna' , $errors, null ,'form-control',true,true) !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto_2', 'Dirección:', 'text', $errors, null, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto_2', 'Estado' , $errors, null ,'form-control',true,false) !!}
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
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto_3', 'Nombre', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto_3', 'Cargo', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto_3', 'Correo Electrónico', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto_3', 'Teléfono', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto_3', 'Comuna' , $errors, null ,'form-control',true,true) !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto_3', 'Dirección:', 'text', $errors, null, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto_3', 'Estado' , $errors, null ,'form-control',true,false) !!}
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
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto_4', 'Nombre', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto_4', 'Cargo', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto_4', 'Correo Electrónico', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto_4', 'Teléfono', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto_4', 'Comuna' , $errors, null ,'form-control',true,true) !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto_4', 'Dirección:', 'text', $errors, null, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto_4', 'Estado' , $errors, null ,'form-control',true,false) !!}
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
                      {!! armarInputCreateEdit_2('col', 'nombre_contacto_5', 'Nombre', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'cargo_contacto_5', 'Cargo', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'email_contacto_5', 'Correo Electrónico', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col', 'phone_contacto_5', 'Teléfono', 'text',$errors, null,'form-control', '', '') !!}
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditComuna($ciudades, 'comuna_contacto_5', 'Comuna' , $errors, null ,'form-control',true,true) !!}
                    </div>
                    <div class="col-12">
                      {!! armarInputCreateEdit_2('col','direccion_contacto_5', 'Dirección:', 'text', $errors, null, 'form-control', '', '') !!}
                    </div>
                    <div class="col-12">
                      {!! armarSelectArrayCreateEditOTSeparado(['activo'=>'Activo', 'inactivo'=>'Inactivo'],'active_contacto_5', 'Estado' , $errors, null ,'form-control',true,false) !!}
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
  $(document).ready(function() {
    $("#rut").attr("maxlength", "10");
    if ($("input#rut").siblings("span").length <= 1)
      $("#rut").after(
        '<span class="rut_invalid bottom-description text-danger"></span>'
      );

    $("#rut").on("input", function() {
      if ($('#nacional').val() == 1) {
        checkRut(this);
      }
    });

    function checkRut(rut) {
      // Despejar Puntos
      var valor = rut.value.replace(".", "");
      // Despejar Guión
      valor = valor.replace("-", "");

      // Aislar Cuerpo y Dígito Verificador
      cuerpo = valor.slice(0, -1);
      dv = valor.slice(-1).toUpperCase();

      // Formatear RUN
      rut.value = cuerpo + "-" + dv;

      // Si no cumple con el mínimo ej. (n.nnn.nnn)
      if (cuerpo.length < 7) {
        $(".rut_invalid").text("RUT Inválido");
        $('#submitClient').prop('disabled', true);
        /*rut.setCustomValidity("RUT Incompleto");*/

        if (rut.value == "-") rut.value = "";
        return false;
      }

      // Calcular Dígito Verificador
      suma = 0;
      multiplo = 2;

      // Para cada dígito del Cuerpo
      for (i = 1; i <= cuerpo.length; i++) {
        // Obtener su Producto con el Múltiplo Correspondiente
        index = multiplo * valor.charAt(cuerpo.length - i);

        // Sumar al Contador General
        suma = suma + index;

        // Consolidar Múltiplo dentro del rango [2,7]
        if (multiplo < 7) {
          multiplo = multiplo + 1;
        } else {
          multiplo = 2;
        }
      }

      // Calcular Dígito Verificador en base al Módulo 11
      dvEsperado = 11 - (suma % 11);

      // Casos Especiales (0 y K)
      dv = dv == "K" ? 10 : dv;
      dv = dv == 0 ? 11 : dv;

      // Validar que el Cuerpo coincide con su Dígito Verificador
      if (dvEsperado != dv) {
        $(".rut_invalid").text("RUT Inválido");
        $('#submitClient').prop('disabled', true);
        return false;
      }

      // Si todo sale bien, eliminar errores (decretar que es válido)
      $(".rut_invalid").text("");
      $('#submitClient').prop('disabled', false);
    }

    $('#nacional').change(function() {
      if ($('#nacional').val() == 1) {
        $('#rut').val("")
        $("#rut").attr("maxlength", "10");
      } else {
        $('#rut').val("")
        $("#rut").attr("maxlength", "20");
        $(".rut_invalid").text("");

        $('#submitClient').prop('disabled', false);
      }
    })

  });

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
      url: "store_installation",
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

  $('#informacion').on('show.bs.modal', function(){
    var myModal = $(this);
    clearTimeout(myModal.data('hideInterval'));
    myModal.data('hideInterval', setTimeout(function(){
        myModal.modal('hide');
    }, 2500));
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
    var tipo 		      = $('#tipo').val();

    return $.ajax({
      type: "GET",
      url: "store_indicacion",
      data: "cliente="+cliente+"&garantia_ect="+garantia_ect+"&campo_1="+campo_1+"&campo_2="+campo_2+"&campo_3="+campo_3+
            "&campo_4="+campo_4+"&campo_5="+campo_5+"&campo_6="+campo_6+
            "&campo_7="+campo_7+"&campo_8="+campo_8+"&campo_9="+campo_9+"&campo_10="+campo_10+"&tipo="+tipo,
      success: function (data) {
        console.log(data);

        $('#campo_1,#campo_2,#campo_3,#campo_4,#campo_5,#campo_6,#campo_7,#campo_8,#campo_9,#campo_10').val('');
        $('#garantia_ect')
          .val('')
          .selectpicker('refresh');


        $("#modal-indicacion").modal('hide');
        $("#message_info").text("Registro de Instrucciones realizada con exito");
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
