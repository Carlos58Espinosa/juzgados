<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('/images/R.png') }}">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

        <!-- Estilos -->
		<link rel="stylesheet" type="text/css" href="{{ asset('/css/estilos.css') }}" >
    </head>
    <body>
    	<br>
    	<br>

		<form method="POST" action="{{ action('AuthController@login') }}">
			@csrf
			<div class="container-fluid" align="center">
				<div class="container">
					 
				    
			        <label for="">Email: <span style="color:red">*</span></label>

			        <br>			        

			        <input style="text-transform: none;" type="email" class="form-control input_nombre" required name="email" value="" required>
			        @error('email')
			          <span class="invalid-feedback" role="alert">
			            <strong>{{ $message }}</strong>
			          </span>
			        @enderror
				      
					<br>

			        <label for="">Password: <span style="color:red">*</span></label>
			        <input style="text-transform: none;" type="password" class="form-control input_nombre" required name="password" value="" required>
			        @error('password')
			          <span class="invalid-feedback" role="alert">
			            <strong>{{ $message }}</strong>
			          </span>
			        @enderror

				    <br>
				    <div>
						<button type="submit" class="btn btn-info">Login</button>
					</div>
				</div>
			</div>
		</form>
		<!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>
