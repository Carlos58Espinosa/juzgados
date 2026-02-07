<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Turi - Inforela</title>

<!-- Favicon -->
<link rel="icon" type="image/png" href="{{ asset('/images/R.png') }}">

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- FullCalendar -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">

<!-- Toastr -->
<link rel="stylesheet" href="{{ asset('admincss/toastr.css') }}">

<!-- Theme admin -->
<link rel="stylesheet" href="{{ asset('admincss/theme.css') }}">

<!-- Tu CSS -->
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<!-- jQuery (solo una vez) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Summernote Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

</head>

<body>

@include('general.general_methods')
<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">

<!-- SIDEBAR -->
<aside class="menu-sidebar d-none d-lg-block">
    <div class="logo">
        <a href="#">
            <img src="{{asset('/images/R.png')}}" class="logo_turi"/>
        </a>
    </div>

    <div class="menu-sidebar__content js-scrollbar1">
        <nav class="navbar-sidebar">
            <ul id="menu_list" class="list-unstyled navbar__list">

                <li><a href="{{action('ConfiguracionController@index')}}"><i class="fas fa-cogs"></i> Tipo de Procedimientos</a></li>
                <li><a href="{{action('PlantillasController@index')}}"><i class="far fa-file-alt"></i> Plantillas</a></li>
                <li><a href="{{action('CasosController@index')}}"><i class="fas fa-gavel"></i> Expedientes</a></li>
                <li><a href="{{action('AgrupacionesController@index')}}"><i class="fas fa-gavel"></i> Agrupación de Valores</a></li>
                <li><a href="{{action('CalendarioController@index')}}"><i class="fas fa-gavel"></i> Calendario</a></li>

                @if(auth()->user()->tipo != 'Empleado')
                <li><a href="{{action('UsuariosController@index')}}"><i class="fas fa-gavel"></i> Usuarios</a></li>
                @endif

                <li>
                    <form id="form_logout" action="{{action('AuthController@logout')}}" method="POST">
                        @csrf
                        <a href="#" onclick="document.getElementById('form_logout').submit()">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </form>
                </li>

            </ul>
        </nav>
    </div>
</aside>

<!-- HEADER -->
<header class="header-desktop d-none d-lg-block">
    <div class="d-flex justify-content-end align-items-center p-3">

        <i class="fas fa-moon me-2" style="color:lightcoral;"></i>

        <div class="form-check form-switch">
            <input onchange="changeColorConfiguration(1)"
                   class="form-check-input"
                   type="checkbox"
                   id="switch_night_day"
                   style="background-color: lightcoral;">
        </div>

    </div>
</header>

<!-- MAIN -->
<input type="hidden" id="modo_color" value="{{session('color')}}">

<main id="main-content">
    @yield('content')
</main>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Admin scripts -->
<script src="{{ asset('adminjs/toastr.js') }}"></script>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Toastr messages -->
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

<script>
$(document).ready(function() {
    loadColor('index');
});
</script>

</body>
</html>
