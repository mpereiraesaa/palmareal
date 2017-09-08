<!DOCTYPE html>
<html lang="en">
<head>
	<title>Palma Real | Panel de Administracion - @yield('title')</title>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="author" content="Christian Aguilar" />
	<meta name="copyright" content="Palma Real"/>
	<meta name="keywords" content="Inmuebles en venta, Inmuebles en Alquiler, Inmobiliaria, Venta, Compra, Alquiler, Chile, Santiago, Precios bajos, economicos, calidad, condominios, arriendos" />
	<meta name="description" content="@yield('meta-description')" />
	<!-- Favicon -->
	<link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}"/>
	<link rel="icon" href="{{ asset('images/favicon-16x16.png') }}" sizes="16x16" type="image/png">
	<link rel="icon" href="{{ asset('images/favicon.ico') }}" sizes="32x32 48x48" type="image/vnd.microsoft.icon">
	<link rel="icon" href="{{ asset('images/favicon-apple.png') }}" sizes="57x57" type="image/png">	
	<!-- Styles -->
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.6 -->
	<link rel="stylesheet" href="{{ asset('adminlte/bootstrap/css/bootstrap.min.css') }}">
	<!-- Theme style -->
	<link rel="stylesheet" href="{{ asset('adminlte/dist/css/AdminLTE.min.css') }}">
	<!-- Theme style custom user -->
	<link rel="stylesheet" href="{{ asset('adminlte/dist/css/custom.css') }}">
	<!-- AdminLTE Skins. Choose a skin from the css/skins
	     folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="{{ asset('adminlte/dist/css/skins/_all-skins.min.css') }}">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
 	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
 	<!--[if lt IE 9]>
 	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
 	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
 	 <![endif]-->
 	 @yield('styles')
</head>
<body class="hold-transition skin-blue fixed sidebar-mini">
	<div class="wrapper">
		@include('layouts.admin.header')
		@include('layouts.admin.nav')
		<main class="content-wrapper" role="main">
		@include('flash::message')
		<!-- Content Header (Page header) -->
		    <header class="content-header">
		        <h1>
		            @yield('title')
		            <small>Panel de Control</small>
		        </h1>
		    </header>

		    <!-- Main content -->
		    <section class="content">
				{{-- @include('flash::message') --}}
				@yield('content')
			</section>
		</main>
	</div>
	<!-- ./wrapper -->
	@yield('modals')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD9KS1GeaKAQk7LCDqAJclffYKE_izcBFk&libraries=places"></script>
<!-- jQuery 2.2.3 -->
<script src="{{ asset('adminlte/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{ asset('adminlte/bootstrap/js/bootstrap.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('adminlte/plugins/fastclick/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('adminlte/dist/js/app.min.js') }}"></script>
<!-- SlimScroll 1.3.0 -->
<script src="{{ asset('adminlte/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/locationpicker.jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/functions.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/maps.js') }}"></script>
@yield('scripts')
</body>
</html>
