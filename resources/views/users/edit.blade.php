@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar usuario</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<!-- formulario: -->
		<form method="POST" action="{{ route('mantenedores.users.update', $user->id) }}">
			@method('PUT')
			@csrf
			@include('users.form', ['tipo' => "edit",'class' => 'disabled','profiles'=> $profiles,'jefesVenta'=>$jefesVenta])
		</form>
	</div>
</div>


@endsection
@section('myjsfile')

<script type="text/javascript" src="{{ asset('/js/functions_user.js') }}"></script>
<script>
	$("#role_id").on("change", function() {
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
</script>
@endsection