<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#" lang="{{ str_replace('_', '-', app()->getLocale()) }}" itemscope itemtype="http://schema.org/WebPage">
<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<!-- color en moviles -->
	<meta name="theme-color" content="#93C144">
	<meta name="apple-mobile-web-app-status-bar-style" content="#93C144">
	<meta name="msaplication-navbutton-color" content="#93C144">

	<?php
		$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$partial_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
	?>

	<meta name="csrf-token" content="{{ csrf_token() }}">

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Turi - Inforela</title>

	<!-- Apple icons -->
	<link rel="apple-touch-icon" sizes="57x57" href="{{ asset('/favicon/apple-icon-57x57.png') }}" />
	<link rel="apple-touch-icon" sizes="60x60" href="{{ asset('/favicon/apple-icon-60x60.png') }}" />
	<link rel="apple-touch-icon" sizes="72x72" href="{{ asset('/favicon/apple-icon-72x72.png') }}" />
	<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('/favicon/apple-icon-76x76.png') }}" />
	<link rel="apple-touch-icon" sizes="114x114" href="{{ asset('/favicon/apple-icon-114x114.png') }}" />
	<link rel="apple-touch-icon" sizes="120x120" href="{{ asset('/favicon/apple-icon-120x120.png') }}" />
	<link rel="apple-touch-icon" sizes="144x144" href="{{ asset('/favicon/apple-icon-144x144.png') }}" />
	<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('/favicon/apple-icon-152x152.png') }}" />
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/favicon/apple-icon-180x180.png') }}" />
	<!-- favicon -->
	<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('/images/R.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/favicon/favicon-16x16.png') }}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/favicon/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('/favicon/favicon-96x96.png') }}">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="{{ asset('/favicon/ms-icon-144x144.png') }}">
	<meta name="theme-color" content="#ffffff">

	<!-- Fontfaces CSS-->
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/fontawesome5.3.1.css') }} ">
	<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@/css/font-awesome.min.css" integrity="sha384-" crossorigin="anonymous">  -->
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/ionicfonts.css') }} ">
	<!-- Bootstrap CSS-->
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/bootstrap4.1.css') }} ">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/flaticon.css') }} ">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/animate.css') }} ">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/bootstrap-select.css')}}">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/datepicker.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/toastr.css') }} ">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/cropper.css') }} ">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/datatables.css') }} ">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/datepicker.css') }}"/>
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/tagsinput.css') }} ">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/animsition.min.css') }} ">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/bootstrap-progressbar.min.css') }} ">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/animate.css') }} ">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/hamburgers.css') }} ">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/slick.css') }} ">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/select2.min.css') }} ">
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/perfect-scrollbar.css') }} ">

	<!-- theme -->
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/theme.css') }} ">
	<!-- styles -->
	<link rel="stylesheet" type="text/css" href="{{ asset('admincss/adminstyles.css') }} ">

	<!-------------------------------- SUMMERNOTE  -------------------------------------------------->
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">   
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<!-- <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet"> -->
	<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
	<!--------------------------------.............-------------------------------------------------->


	<style media="screen">
		a.disabled {
		  pointer-events: none;
		  cursor: default;
		}

		.hidden {
		  display: none;
		}

		.table th, .table td {
			padding: 5px;
		}
		.table td a{
			padding: 0;
		}
		.table-bordered {
		  border: 1px solid #ddd !important;
		}

		input{
			text-transform: uppercase;
		}

		input, textarea, select, .form-control, .form-control:focus, .bootstrap-select > .dropdown-toggle, .bootstrap-select .dropdown-menu {
		  background-color : #efefef;
		}

		select{
			background: #eee;
		}

		.card{
			background: #D0DFE7;
		}

		th{
			background: #B8D9EC;
		}

		td{
			background: #B8D9EC;
		}

		.table-bordered th, .table-bordered td {
	    	border: 1px solid #84B5D2;
		}

		.menu-sidebar__content, .header-desktop, .menu-sidebar .logo {
			background: #84B5D2;
		}

		#sub-header ul li:hover,
		body.home li.home,
		body.contact li.contact { background-color: #fff;}

		#sub-header ul li:hover a,
		body.home li.home a,
		body.contact li.contact a { color: #fff; }

		.navbar-sidebar .navbar__list li.active > a {
			color: green;
		}
		.navbar-sidebar .navbar__list li a:hover {
			color: #FFF;
		}
		.container-fluid{
			background: #D0DFE7;
		}
</style>


<script src="{{ asset('adminjs/jquery3.3.1.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="{{ asset('adminjs/bootstrap4.1.js') }}"></script>
<script src="{{ asset('adminjs/bootstrap-select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('adminjs/tablesorter.js') }}"></script>
<script src="https://unpkg.com/qrious@4.0.2/dist/qrious.js"></script>
</head>

<body class="animsition home">
	<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
    <div class="page-wrapper">

        <!-- HEADER MOBILE-->
		    <header class="header-mobile d-block d-lg-none">
		        <div class="header-mobile__bar">
		            <div class="container-fluid">
		                <div class="header-mobile-inner">
		                    <a class="logo-a" href="#">
		                        <img src="{{asset('/images/R.png')}}" alt="CoolAdmin" class="logo-img" />
		                    </a>
		                    <button class="hamburger hamburger--slider" type="button">
		                        <span class="hamburger-box">
		                            <span class="hamburger-inner"></span>
		                        </span>
		                    </button>

						<div class="header-button">
							<div class="account-wrap">
								<div class="account-item clearfix js-item-menu">
										<div class="image">
												<img src="{{asset('/images/R.png')}}" alt="" />											
										</div>
										<div class="content">
												<a class="js-acc-btn" href="#"></a>
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
												<div class="account-dropdown__footer">
	                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="zmdi zmdi-power"></i>Salir</a>
	                        <form id="logout-form" action="" method="POST" style="display: none;">
	                          @csrf
	                        </form>
	                      </div>
										</div>
								</div>
							</div>
						</div>
		                </div>
		            </div>
		        </div>
		        <nav class="navbar-mobile">
		            <div class="container-fluid" id="sub-header">
		                <ul class="navbar-mobile__list list-unstyled">
							<li>
								<a href="{{action('PlantillasController@index')}}">Plantillas</a>
							</li>
							<li>
								<a href="{{action('ConfiguracionController@index')}}">Configuración de Plantillas</a>
							</li>
							<li>
								<a href="{{action('CasosController@index')}}">Casos</a>
							</li>

		                </ul>
		            </div>
		        </nav>
		    </header>
	    <!-- END HEADER MOBILE-->

	    <!-- MENU SIDEBAR-->
			<aside class="menu-sidebar d-none d-lg-block">
			    <div class="logo">
			        <a class="logo-a" href="#">
			            <img src="{{asset('/images/R.png')}}" class="logo-img" />
			        </a>
			    </div>
			    <div class="menu-sidebar__content js-scrollbar1">
			        <nav class="navbar-sidebar" id="sub-header2">
			            <ul class="list-unstyled navbar__list">
							<li>
								<a href="{{action('PlantillasController@index')}}"><i class="far fa-file-alt"></i>  Plantillas</a>
							</li>
							<li>
								<a href="{{action('ConfiguracionController@index')}}"><i class="fas fa-cogs"></i>Configuración</a>
							</li>
							<li>
								<a href="{{action('CasosController@index')}}"><i class="fas fa-gavel"></i> Casos</a>
							</li>
		                </ul>
			        </nav>
			    </div>
			</aside>
	    <!-- END MENU SIDEBAR-->

	    <!-- PAGE CONTAINER-->
		    <div class="page-container">
			    <!-- HEADER DESKTOP-->

				    <header class="header-desktop d-none d-lg-block">
				        <div class="section__content section__content--p30">
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
				        </div>
				    </header>

				<!-- HEADER DESKTOP-->

		        <!-- MAIN CONTENT-->
		            @yield('content')

		        <!-- END MAIN CONTENT-->
	        </div>
        <!-- END PAGE CONTAINER-->




	<!-- Jquery JS -->

	<!-- Bootstrap JS -->
	<script src="{{ asset('adminjs/toastr.js') }}"></script>
	<script src="{{ asset('adminjs/cropper.js') }}"></script>
	<script src="{{ asset('adminjs/datatables.js') }}"></script>
	<script src="{{ asset('adminjs/datepicker.js') }}"></script>
	<script src="{{ asset('adminjs/tagsinput.js') }}"></script>
	<script src="{{ asset('adminjs/animsition.min.js') }}"></script>
	<script src="{{ asset('adminjs/bootstrap-progressbar.min.js') }}"></script>
	<script src="{{ asset('adminjs/wow.min.js') }}"></script>
	<script src="{{ asset('adminjs/slick.js') }}"></script>
	<script src="{{ asset('adminjs/select2.min.js') }}"></script>
	<script src="{{ asset('adminjs/jquery.waypoints.min.js') }}"></script>
	<script src="{{ asset('adminjs/jquery.counterup.min.js') }}"></script>
	<script src="{{ asset('adminjs/circle-progress.js') }}"></script>
	<script src="{{ asset('adminjs/perfect-scrollbar.js') }}"></script>
	<script src="{{ asset('adminjs/charts.min.js') }}"></script>

	<!-- CKEditor -->
	<script src="{{ asset('js/sweetalert.js') }}"></script>
	<!-- main -->
	<script src="{{ asset('adminjs/main.js') }}"></script>
	<!-- Sweet Alert -->
	<!-- javscripts -->
	<script src="{{ asset('adminjs/adminscripts.js') }}"></script>

	<script >
		$("[data-toggle=tooltip]").tooltip();
	</script>

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
		$('body').on('click','.delete-alert',function(event){
			var url = $(this).attr('data-action');
			var table = $(this).attr('data-table');
			var reload = $(this).attr('data-reload');

			var method = $(this).attr('data-method');
			var message1 = $(this).attr('data-message1');
			var message2 = $(this).attr('data-message2');
		  var message3 = $(this).attr('data-message3');
			var to = $("#token").val();

			Swal.fire({
			  title: '{{__("¿Estás seguro de ELIMINAR?")}}',
			  text: message1,
			  icon: 'warning',
			  showCancelButton: true,
			  confirmButtonColor: '#3085d6',
			  cancelButtonColor: '#d33',
				confirmButtonText: '{{__("Sí")}}',
			  cancelButtonText: '{{__("No")}}'
			}).then((result) => {
			  if (result.isConfirmed) {
					$.ajax({
						type: "POST",
						headers:{"X-CSRF-TOKEN": to},
						url: url,
						cache: false,
						dataType: 'json',
						data: {
                "_token": to,
                "_method": method
            },
						success: function(data) {
							console.log('success');
							$(table).load(" "+table);

							Swal.fire(
							 message2,
							 message3,
							 'success'
						 	);
						},
						error: function(jqXHR, textStatus, errorThrown){

							//$(table).load(" "+table);

							if(jqXHR.status == 422){
								$.parseJSON(jqXHR.responseText);
							}
							else{
								message = '{{__("Oops! there was an error, please try again later.")}}';
							}
							Swal.fire(
							 'Error!',
							 message,
							 'error'
						 	);
						},
					});
			  }
			});
		});
	</script>
	

</body>
</html>
