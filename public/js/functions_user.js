//funcioens javascript:
$(document).ready(function(){

  $('#rut').attr('maxlength','10');
  if($('input#rut').siblings('span').length==0)
    $('#rut').after('<span class="rut_invalid bottom-description text-danger"></span>');

  $('#rut').on('input',function(){
    checkRut(this);
  });

});

$("#role_id").on("change",function (){
  
  if($("#role_id").val() == 4){
    $("#jefe-venta").show();
    $("#sala-corte").hide();
    $("#vendedor-cliente").hide();
    $("#vendedor-responsable").hide();
  }else{
    $("#jefe-venta").hide();
   
    if($("#role_id").val() == 14){
      $("#sala-corte").show();
      $("#vendedor-cliente").hide();
      $("#vendedor-responsable").hide();
    }else{
      $("#sala-corte").hide();
      if($("#role_id").val() == 19){
        $("#vendedor-cliente").show();
        $("#vendedor-responsable").show();
        $("#jefe-venta").hide();
       
      }else{
        $("#vendedor-cliente").hide();
        $("#vendedor-responsable").hide();
        $("#sala-corte").hide();
        $("#jefe-venta").hide();
      }
    }
    
  }
 

}).triggerHandler("change");






/*funciones locales:*/

function checkRut(rut) {
    // Despejar Puntos
    var valor = rut.value.replace('.','');
    // Despejar Guión
    valor = valor.replace('-','');
    
    // Aislar Cuerpo y Dígito Verificador
    cuerpo = valor.slice(0,-1);
    dv = valor.slice(-1).toUpperCase();
    
    // Formatear RUN
    rut.value = cuerpo + '-'+ dv
    
    // Si no cumple con el mínimo ej. (n.nnn.nnn)
    if(cuerpo.length < 7) { 
      $('.rut_invalid').text("RUT Inválido"); 
      /*rut.setCustomValidity("RUT Incompleto");*/ 
      if(rut.value=='-') rut.value = '';
      return false;
    }
    
    // Calcular Dígito Verificador
    suma = 0;
    multiplo = 2;
    
    // Para cada dígito del Cuerpo
    for(i=1;i<=cuerpo.length;i++) {
    
        // Obtener su Producto con el Múltiplo Correspondiente
        index = multiplo * valor.charAt(cuerpo.length - i);
        
        // Sumar al Contador General
        suma = suma + index;
        
        // Consolidar Múltiplo dentro del rango [2,7]
        if(multiplo < 7) { multiplo = multiplo + 1; } else { multiplo = 2; }
  
    }
    
    // Calcular Dígito Verificador en base al Módulo 11
    dvEsperado = 11 - (suma % 11);
    
    // Casos Especiales (0 y K)
    dv = (dv == 'K')?10:dv;
    dv = (dv == 0)?11:dv;
    
    // Validar que el Cuerpo coincide con su Dígito Verificador
    if(dvEsperado != dv) { 
      $('.rut_invalid').text("RUT Inválido");
      // rut.setCustomValidity("RUT Inválido"); 
      return false; 
    }

    // Si todo sale bien, eliminar errores (decretar que es válido)
    $('.rut_invalid').text('');
}