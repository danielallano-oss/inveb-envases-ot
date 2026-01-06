@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Contraseña</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<!-- formulario: -->
		<form method="POST" action="{{ route('actualizarContraseña', Auth()->user()->id) }}">
			@method('PUT')
			@csrf
			<div class="form-row">
				<div class="col-12">
					<div class="card h-100">
						<div class="card-header"></div>
						<div class="card-body">
							<div class="row">

								{{-- Contraseña --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
								{!! armarInputCreateEdit('col', 'password', 'Contraseña', 'password',$errors, $user, 'form-control', '', '') !!}

								{{-- Repite contraseña --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
								{!! armarInputCreateEdit('col', 'password_confirmation', 'Repite Contraseña', 'password',$errors, $user, 'form-control', '', '') !!}

								<div class="col"></div>
								<div class="col"></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="mt-3 text-right">
				<a href="{{ route('mantenedores.users.list') }}" class="btn btn-light">Cancelar</a>
				<button type="submit" class="btn btn-success">{{ isset($user->id) ? __('Actualizar') : __('Guardar') }}</button>
			</div>
		</form>
	</div>
</div>
@endsection