<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('/images/R.png') }}">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('admincss/toastr.css') }}">

    <!-- Estilos propios -->
    <link rel="stylesheet" href="{{ asset('/css/estilos.css') }}">
</head>
<body class="bg-light">

<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="card shadow-sm p-4" style="max-width: 420px; width: 100%;">
        <h4 class="text-center mb-4">Iniciar sesión</h4>

        <form method="POST" action="{{ action('AuthController@login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email"
                       required
                       value="{{ old('email') }}">

                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password"
                       required>

                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Button -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    Entrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('adminjs/toastr.js') }}"></script>

<script>
@if(Session::has('message'))
    var type = "{{ Session::get('alert-type', 'info') }}";
    switch(type){
        case 'info': toastr.info("{{ Session::get('message') }}","{{ Session::get('title') }}"); break;
        case 'warning': toastr.warning("{{ Session::get('message') }}","{{ Session::get('title') }}"); break;
        case 'success': toastr.success("{{ Session::get('message') }}","{{ Session::get('title') }}"); break;
        case 'error': toastr.error("{{ Session::get('message') }}","{{ Session::get('title') }}"); break;
    }
@endif
</script>

</body>
</html>
