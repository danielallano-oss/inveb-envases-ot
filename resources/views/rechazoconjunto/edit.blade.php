@extends('layouts.index', ['dontnotify' => true])

@section('content')
<h1 class="page-title">Editar Rechazo Conjunto</h1>

<div class="row mb-3">
	<div class="col-12 p-2">
		<form method="POST" action="{{ route('mantenedores.rechazo-conjunto.update', $rechazoconjunto->id) }}">
			@method('PUT')
			@csrf
			@include('rechazoconjunto.form', ['tipo' => "edit",'class' => 'disabled'])
		</form>
	</div>
</div>
@endsection

@section('myjsfile')

<script>
  $(document).ready(function () {
    $('#proceso_id').prop('disabled',true);
    });

</script>
@endsection



