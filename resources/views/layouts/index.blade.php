<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>{{ config('app.name', 'CMPC') }}</title>
	<meta name="viewport" content="width=1280, initial-scale=1, user-scalable=0, shrink-to-fit=no">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<!-- Favicons -->
	<link rel="apple-touch-icon" sizes="57x57" href="{{ asset('img/favicons/apple-icon-57x57.png') }}">
	<link rel="apple-touch-icon" sizes="60x60" href="{{ asset('img/favicons/apple-icon-60x60.png') }}">
	<link rel="apple-touch-icon" sizes="72x72" href="{{ asset('img/favicons/apple-icon-72x72.png') }}">
	<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/favicons/apple-icon-76x76.png') }}">
	<link rel="apple-touch-icon" sizes="114x114" href="{{ asset('img/favicons/apple-icon-114x114.png') }}">
	<link rel="apple-touch-icon" sizes="120x120" href="{{ asset('img/favicons/apple-icon-120x120.png') }}">
	<link rel="apple-touch-icon" sizes="144x144" href="{{ asset('img/favicons/apple-icon-144x144.png') }}">
	<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('img/favicons/apple-icon-152x152.png') }}">
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicons/apple-icon-180x180.png') }}">
	<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/favicons/android-icon-192x192.png') }}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicons/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('img/favicons/favicon-96x96.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicons/favicon-16x16.png') }}">
	<link rel="manifest" href="{{ asset('img/favicons/manifest.json') }}">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">
	<!-- End Favicons -->

	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<link rel="stylesheet" href="{{ asset('css/style.css') }}">
	<!-- Cambiar el color primario segun sea produccion o local -->
	@if (env('APP_ENV') == "local")
	<script>
		let root = document.documentElement;
		//   root.style.setProperty('--primary', "#3aaa35")
		root.style.setProperty('--primary', "#17a2b9")
	</script>
	@endif
	<link rel="stylesheet" href="{{ asset('css/custom.css') }}">

	<link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.standalone.min.css') }}">

	<link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
</head>

<body class="d-flex flex-column h-100">
	@include('layouts.header')
	<!-- Notificaciones -->
	@if(isset($dontnotify))
	@else @include('layouts.messages')
	@endif
	<!--  -->
	<main class="py-3">
		<div class="container-fluid">
			@yield('content')
		</div>
	</main>
	@include('layouts.footer')


	<script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
	<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
	<script src="{{ asset('js/additional-methods.min.js') }}"></script>
	<script src="{{ asset('js/popper.min.js') }}"></script>
	<script src="{{ asset('js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
	<script src="{{ asset('js/locales/bootstrap-datepicker.es.min.js') }}"></script>
	<script src="{{ asset('js/bootstrap-notify.min.js')}}"></script>
	<script src="{{ asset('js/bootstrap-select.js') }}"></script>
	<script src="{{ asset('js/main.js') }}"></script>
	<script src="{{ asset('js/unpkg.imask.js') }}"></script>
	<script src="{{ asset('js/mascaras-numericas.js') }}"></script>


	@yield('myjsfile')
	@yield('notifyToast')

</body>

</html>