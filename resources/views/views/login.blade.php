<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Alitas Posadas</title>
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('/images/gallo3.png') }}">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

        <link rel="stylesheet" type="text/css" href="{{ asset('admincss/toastr.css') }} ">

		
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Estilos -->
        <!--link rel="stylesheet" type="text/css" href="{{ asset('/css/estilos.css') }}" -->

		<style>
			html, body {
				height: 100%;
				margin: 0;
				padding: 0;
			}

			.body_login {
				height: 100vh;
				width: 100vw;
				background-image: url(https://alitas.encuentrogeek.com/images/fondo_login-_3_.webp);
				background-repeat: no-repeat;
				background-size: cover;
				background-position: center;
			}
							

			.img_input{
				width: 16px;
			}

			.input-group-text{
				background: #FFF;
				border-right: none;
				border-top-left-radius: 25px;
				border-bottom-left-radius: 25px;
				border: none;
			}

			.input-login{
				border-left: none;
				border-top-right-radius: 25px;
				border-bottom-right-radius: 25px;
				border: none;

			}

			.logos-container
			{
				position: relative;
			}

			.login-logo{
				width: 230px;
				position: absolute;
				top: 0%;
				left: 50%;
				transform: translate(-50%, -50%);
			}

			.login-logo-ia{
				width: 230px;
				position: absolute;
				top: 0%;
				left: -20%;
				transform: translate(-50%, -100%);
				z-index: -1;
			}

			.login-logo-ad {
				width: 230px;
				position: absolute;
				bottom: 0%;
				right: -100%;
				transform: translate(-45%, 170%);
				z-index: -1;
			}

			.login-div{
				background: #d52728;				
				border: 10px solid #ffffff;
				border-radius: 40px;
				box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
				width: fit-content;
				padding: 10px 50px;
				position: relative;
			}

			.login-container{
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				width: max-content;
			}

			.div_email{
				margin-top: 80px;
			}

			.login-button-div{
				width: 80% !important;
				position: absolute;
				bottom: -30px;
				left: 50%;
				transform: translate(-50%, -50%);
			}

			
			.button_login:hover{
				background: #fbbe4b;
			}
			.button_login {
				background-color: #f0a500;
				color: white;
				padding: 10px 20px;
				border: none;
				border-radius: 25px;
				cursor: pointer;
				transition: background 0.2s ease-in-out;
			}

			.button_login:active {
				background: #fbbe4b !important; /* Color cuando se mantiene presionado */
			}


			.input-div{
				box-shadow: 10px 10px 0px 3px rgb(19, 46, 73);
				-webkit-box-shadow: 10px 10px 0px 3px rgb(19, 46, 73);
				-moz-box-shadow: 10px 10px 0px 3px rgb(19, 46, 73);
				border-radius: 25px;
			}

			.div-password{
				margin-bottom: 5rem;
			}
						



			/* Teléfonos pequeños (menor a 576px) */
			@media (max-width: 575.98px) {
			/* Estilos para teléfonos */
				.login-div{
					padding: 10px 20px;
				}
				.login-button {
					width: 90% !important;
				}

				.login-logo-ad{
					right: -72%;
				}
			}

			/* Teléfonos grandes (576px a 767px) */
			@media (min-width: 576px) and (max-width: 767.98px) {
			/* Estilos para móviles grandes / pequeños tablets */
				.login-div{
					padding: 10px 20px;
				}
				.login-button {
					width: 90% !important;
				}

				.login-logo-ad{
					right: -72%;
				}

				

			}

			/* Tablets (768px a 991px) */
			@media (min-width: 768px) and (max-width: 991.98px) {
			/* Estilos para tablets */
			}

			/* Laptops (992px a 1199px) */
			@media (min-width: 992px) and (max-width: 1199.98px) {
			/* Estilos para laptops */
			}

			/* Pantallas grandes (1200px en adelante) */
			@media (min-width: 1200px) {
			/* Estilos para pantallas grandes */
			}

						


			
		</style>

    </head>
    <body class="body_login">
		<main>
			<div class="container py-5">

				<div class="login-container">

					<div class="login-div">

					
					<!--img class="img-fluid mb-3 login-logo-ia" src="{{ asset('images/icono_login.png') }}" alt="Logo">
								<img class="img-fluid mb-3 login-logo-ad" src="{{ asset('images/icono_login.png') }}" alt="Logo"-->

						<form class="form mx-auto" method="POST" action="{{ action('AuthController@login') }}" style="max-width: 400px;">
							@csrf
							<div class="text-center mb-4">
								<!-- Puedes descomentar si usas logo -->
								<img class="img-fluid mb-3 login-logo" src="{{ asset('images/icono_login.png') }}" alt="Logo">
							</div>

							<!-- Campo Email -->
							<div class="mb-4 div_email">
								<div class="input-group input-div">
									<span class="input-group-text">
										<img class="img_input" src="{{ asset('images/falta_email.png') }}" alt="icono">
									</span>
									<input type="email" class="form-control @error('email') is-invalid @enderror input-login"
										placeholder="Email" name="email" required>
								</div>
								@error('email')
								<div class="invalid-feedback d-block">
									<strong>{{ $message }}</strong>
								</div>
								@enderror
							</div>

							<!-- Campo Password -->
							<div class="div-password">
								<div class="input-group input-div">
									<span class="input-group-text">
										<img class="img_input" src="{{ asset('images/falta_email.png') }}" alt="icono">
									</span>
									<input type="password" class="form-control @error('password') is-invalid @enderror input-login"
										placeholder="Password" name="password" required>
								</div>
								@error('password')
								<div class="invalid-feedback d-block">
									<strong>{{ $message }}</strong>
								</div>
								@enderror
							</div>

							<div class="login-button-div input-div">
								<button type="submit" class="btn btn-primary w-100 button_login">Entrar</button>
							</div>
						</form>
					</div>

				</div>


			</div>
		</main>



		<!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <script src="{{ asset('adminjs/animsition.min.js') }}"></script>
		<script src="{{ asset('adminjs/perfect-scrollbar.js') }}"></script>
		<script src="{{ asset('adminjs/toastr.js') }}"></script>


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

    </body>

</html>
