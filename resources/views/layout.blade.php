<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#" lang="{{ str_replace('_', '-', app()->getLocale()) }}" itemscope itemtype="http://schema.org/WebPage">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Icons -->
    <link rel="stylesheet" type="text/css" href="{{ asset('admincss/fontawesome5.3.1.css') }} ">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> 

	<?php
		$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$partial_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
	?>

	<title>Turi - Inforela</title>

	<!-- Icon Page -->
	<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('/images/R.png') }}">
	<!--------------------------------.............-------------------------------------------------->

	<!-- Bootstrap CSS-->
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/bootstrap-select.css')}}">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/toastr.css') }} ">
	
	<!-- theme -->
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/theme.css') }} ">

	<!-------------------------------- SUMMERNOTE -------------------------------------------------->
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">   
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<!---------------------------------------------------------------------------------------------->

	<!-- Estilos -->
	<link rel="stylesheet" type="text/css" href="{{ asset('/css/estilos.css') }}" >


	<!-------------------------------- Multiple SELECT -------------------------------------------------->
	<script src="{{ asset('adminjs/jquery3.3.1.js') }}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="{{ asset('adminjs/bootstrap4.1.js') }}"></script>
	<script src="{{ asset('adminjs/bootstrap-select.min.js') }}"></script>
	<!---------------------------------------------------------------------------------------------->

</head>

<body>
	@include('general.general_methods')
	<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">

	<!-- MENU SIDEBAR-->
	<aside class="menu-sidebar d-none d-lg-block">
	    <div class="logo">
	        <a class="logo-a" href="#">
	            <img src="{{asset('/images/R.png')}}" class="logo_turi"/>
	        </a>
	    </div>
	    <div class="menu-sidebar__content js-scrollbar1">
	        <nav class="navbar-sidebar" id="sub-header2">
	            <ul id="menu_list" class="list-unstyled navbar__list">
	            	<li>
						<a id="menu_procedimientos" href="{{action('ConfiguracionController@index')}}"><i class="fas fa-cogs"></i>  Tipo de Procedimientos</a>
					</li>
					<li>
						<a id="menu_plantillas" href="{{action('PlantillasController@index')}}"><i class="far fa-file-alt"></i> Plantillas</a>
					</li>							
					<li>
						<a id="menu_expedientes" href="{{action('CasosController@index')}}"><i class="fas fa-gavel"></i> Expedientes</a>
					</li>
					<li>
						<a id="menu_agrupacion" href="{{action('AgrupacionesController@index')}}"><i class="fas fa-gavel"></i> Agrupación de Valores</a>
					</li>
					@if(auth()->user()->tipo != 'Empleado')
					<li>
						<a id="menu_usuarios" href="{{action('UsuariosController@index')}}"><i class="fas fa-gavel"></i> Usuarios</a>
					</li>
					@endif
					<li>
            			<form id="form_logout" action="{{action('AuthController@logout')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
            			@csrf
							<a href="#" onclick="document.getElementById('form_logout').submit()"><i class="fas fa-gavel"></i> Cerrar Sesión</a>
						</form>
					</li>            		
                </ul>
	        </nav>
	    </div>
	</aside>

	
	<!-- END MENU SIDEBAR-->

	<!-- HEADER DESKTOP-->

    <header class="header-desktop d-none d-lg-block">
    	
    	<div style="height: inherit; width:200px; margin-left: 700px;">
    		<!--<a onclick="changeColorConfiguration(0)" class="btn boton_agregar" style="width: 40px; margin-right: 10px;"><i class="fas fa-sun"></i></a> 
    		<a onclick="changeColorConfiguration(1)" class="btn boton_agregar" style="width: 40px; margin-right: 600px;"><i class="fas fa-moon"></i></a> -->  
    		<a href="#" class="btn" style="background: #405189;color:lightcoral; width: 40px; margin-top: 20px; float: left;"><i class="fas fa-moon"></i></a>
			<div class="form-check form-switch" style="width: 40px; margin-left: 40px; height:inherit;">
			  	<input onchange="changeColorConfiguration(1)" class="form-check-input" type="checkbox" role="switch" id="switch_night_day" style="margin-top: 30px; background-color: lightcoral; fill: lightcoral;">
			</div>
		
    	</div>
        <!--<div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="header-wrap">
                    <div class="header-button">
                        <div class="account-wrap">
                            <div class="account-item clearfix js-item-menu">

                                <div class="image">
									<img src="{{asset('/images/profiles/empty.jpg')}}" alt="" />							
                                </div>

                                <div class="account-dropdown js-dropdown">
                                    <div class="info clearfix">
                                        <div class="image">
                                            <a href="#">
												<img src="{{asset('/images/profiles/empty.jpg')}}" alt="" />
                                            </a>
                                        </div>
                                        <div class="content">
                                            <h5 class="name">
                                                <a href="#"></a>
                                            </h5>
                                            <h5 class="name">
                                                <a href="#"></a>
                                            </h5>
                                            <span class="email"></span>
                                        </div>
                                    </div>			
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    </header> 
	<!-- HEADER DESKTOP-->

    <!-- MAIN CONTENT-->
    <!-- <div id="main-content"> -->     	
    	<input type="hidden" id="modo_color" value="{{session('color')}}">
    	<main id="main-content">
    	@include('general.general_methods')

        @yield('content')
    </main>
    <!-- </div> @livewireScripts -->
     
	<!-- END MAIN CONTENT-->


 	<!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
 	<!-- Bootstrap JS -->
	<script src="{{ asset('adminjs/animsition.min.js') }}"></script>
	<script src="{{ asset('adminjs/perfect-scrollbar.js') }}"></script>
	<script src="{{ asset('adminjs/toastr.js') }}"></script>

	<!-- CKEditor -->
	<script src="{{ asset('js/sweetalert.js') }}"></script>


	<script >
		@if(Session::has('message'))
			var type = "{{ Session::get('alert-type', 'info') }}";
			switch(type){
				case 'info':
					toastr.info("{{ Session::get('message') }}","{{ Session::get('title') }}");
				break;
				case 'warning':
					toastr.warning("{{ Session::get('message') }}","{{ Session::get('title') }}");
				break;

				case 'success':
					toastr.success("{{ Session::get('message') }}","{{ Session::get('title') }}");
				break;

				case 'error':
					toastr.error("{{ Session::get('message') }}","{{ Session::get('title') }}");
				break;
			}
		@endif
	</script>

	<script>
	  $(document).ready(function() {
	      loadColor('index');
	  });
	</script>
	
</body>
</html>
