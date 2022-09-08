<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	@section('header')
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	@show

	<title>@yield('title', 'Expert Teachers Application')</title>

	<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
	
	<div id="app">
		<div class="wrapper">
			@include('admin.layouts.navbar')
			@include('admin.layouts.main_sidebar')
			<!-- .content-wrapper -->
			@yield('contents')
			<!-- /.content-wrapper -->
		</div>
	
		{{-- <div id="app">
			<div class="main-container">
				@yield('contents')
			</div>
		</div> --}}
	
		<footer class="main-footer">
			<strong>Telif hakkı &copy; 2022 <a href="https://oygm.meb.gov.tr">OYGM</a>.</strong>
				Bütün Hakları saklıdır.
			<div class="float-right d-none d-sm-inline-block">
				<b>Versiyon</b> 1.0.0
			</div>
		</footer>
	
		<!-- Control Sidebar -->
		<aside class="control-sidebar control-sidebar-dark">
			<!-- Control sidebar content goes here -->
		</aside>
		<!-- /.control-sidebar -->
	</div>
	
	<script src="{{ asset('js/mark.min.js') }}" charset="UTF-8"></script>
	<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>