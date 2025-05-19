<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Alitas Posadas</title>

        <!-- Para Android Chrome -->
        <meta name="theme-color" content="#D02A2A">

        <!-- Para iOS Safari -->
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <!-- Icon Page -->
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('/favicon/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('/favicon/apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('/favicon/apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('/favicon/apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('/favicon/apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('/favicon/apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('/favicon/apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('/favicon/apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/favicon/apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('/favicon/android-icon-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('/favicon/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('/favicon/manifest.json') }}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{ asset('/favicon/ms-icon-144x144.png') }}">
        <meta name="theme-color" content="#ffffff">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

       
        <!-- theme -->
        <!--link rel="stylesheet" type="text/css" href="{{ asset('admincss/theme.css') }} "-->

        <!-- Fonts 
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet"> -->


        <!-- Bootstrap Select CSS -->
        <!--link rel="stylesheet" type="text/css" href="{{ asset('admincss/bootstrap-select.css')}}"-->

        <!-- Toastr CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('admincss/toastr.css') }} ">

        
        <!-- Estilos -->
        <!--link rel="stylesheet" type="text/css" href="{{ asset('/css/estilos.css') }}" -->

        <!------------------ Multiple SELECT ------------------------------------>

        <!--link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.8.1/css/bootstrap-select.css"-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <link rel="stylesheet" type="text/css" href="{{ asset('/css/estilos2.css') }}">

        
        @yield('styles')



       
    </head>
    <body>
        <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">

        <!-- MENU SIDEBAR -->
        <div class="menu-sidebar">
            <div class="menu-sidebar__content js-scrollbar1">

                <button class="btn  position-fixed top-0 end-0 m-3 z-20 d-md-none iconMenu" id="closeMenu">
                <i class="fa-solid fa-xmark"></i>
                </button>



                <img class="logo_menu" src="{{ asset('images/logo_menu.png') }}">
                <nav class="navbar-sidebar" id="sub-header2">
                    <!-- <ul id="menu_list" class="list-unstyled navbar__list"> -->
                    <ul class="ul_menu">
                        @if( Auth::user()->tipo == 'Administrador')
                        <li class="opcion_menu {{ Str::contains(Request::path(), 'productos') ? 'active' : '' }}">
                            <a id="menu_productos" href="{{action('ProductoController@index')}}"><img src="{{ asset('images/boneless.png') }}"> Productos</a>
                        </li>
                        @endif                    
                        <li class="opcion_menu {{ Str::contains(Request::path(), 'inventario') ? 'active' : '' }}">                        
                            <a id="menu_inventario" href="{{action('InventarioController@index')}}"><img src="{{ asset('images/boneless.png') }}"> Inventario</a>
                        </li> 
                        @if( Auth::user()->tipo != 'Administrador')
                        <li class="opcion_menu {{ Str::contains(Request::path(), 'comandas') ? 'active' : '' }}">
                            <a id="menu_comandas" href="{{action('ComandaController@index')}}"><img src="{{ asset('images/comandas.png') }}"> Comandas</a>
                        </li>  
                        @endif                         
                        <li class="opcion_menu {{ Str::contains(Request::path(), 'ventas') ? 'active' : '' }}">
                            <a id="menu_ventas" href="{{action('VentasController@index')}}"><img src="{{ asset('images/ventas.png') }}"> Ventas</a>
                        </li>  
                        <li class="opcion_menu opcion_cerrar_sesion">
                            <form id="form_logout" action="{{action('AuthController@logout')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data"  class="text-center">
                            @csrf
                                <a href="#" class="cerrar" onclick="document.getElementById('form_logout').submit()">C E R R A R  <span>S E S I Ó N</span></a>
                            </form>
                        </li> 
                                                        
                    </ul>
                </nav>
            </div>
        </div>

    
        <!-- END MENU SIDEBAR-->

        <!-- HEADER DESKTOP-->

        <header class="header-desktop  d-none d-sm-none d-md-block">
            
            <div class="div_header text-center">   
                @if(session('sucursal_id') == "16")
                <div> 
                    <img src="{{ asset('images/usuario_container_land.png') }}" alt="" />  
                </div> 
                @else                
                    <img src="{{ asset('images/usuario_administrador.png') }}" alt="" />
                    <!--div class="div_header_sucursal td_radius_right" id="div_header_sucursal" >
                        <select class="" onchange="seleccionSucursal(this.value)" required>
                            <option value="15">Casa</option>
                            <option value="16">Container Land</option>
                        </select> 
                    </div -->
                @endif
            </div>        
        </header> 

        <header class="header-movile  d-block d-sm-block d-md-none">
            
            <div class="div_header text-center">   
                @if(session('sucursal_id') == "16")
                <div> 
                    <img src="{{ asset('images/usuario_container_land.png') }}" alt="" />  
                </div> 
                @else                
                    <img src="{{ asset('images/usuario_administrador.png') }}" alt="" />
                    <!--div class="div_header_sucursal td_radius_right" id="div_header_sucursal" >
                        <select class="" onchange="seleccionSucursal(this.value)" required>
                            <option value="15">Casa</option>
                            <option value="16">Container Land</option>
                        </select> 
                    </div -->
                @endif

                <button class="btn  position-fixed top-0 start-0 m-3 z-5 d-md-none iconMenu" id="toggleMenu">
                <i class="fa-solid fa-bars"></i>
                </button>

            </div>        
        </header> 


        <!-- HEADER DESKTOP-->

        <!-- MAIN CONTENT-->
        <div class="main" id="main-content">
            @yield('content')
        </div>


        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

        
        <!-- Highcharts -->
        <script src="https://code.highcharts.com/highcharts.js"></script>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <!-- Toastr JS-->
        <script src="{{ asset('adminjs/toastr.js') }}"></script>

        <!-- Bootstrap Select JS -->
        <!--script src="{{ asset('adminjs/bootstrap-select.min.js') }}"></script-->

        <!--script src="{{ asset('adminjs/jquery3.3.1.js') }}"></script-->

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

        <!--script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.8.1/js/bootstrap-select.js"></script-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

        <!--script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.0.4/popper.js"></script>

        <script>
            function selectedMenu(id){
                document.getElementById(id).style.color = "white";
            }

            document.querySelectorAll('.opcion_menu:not(.opcion_cerrar_sesion)').forEach(function(li) {
                li.addEventListener('click', function(e) {
                    const link = li.querySelector('a');
                    if (link) {
                        link.click();
                    }
                });
            });


            document.getElementById('toggleMenu').addEventListener('click', function () {
                document.querySelector('.menu-sidebar').classList.toggle('active');
            });

            document.getElementById('closeMenu').addEventListener('click', function () {
                document.querySelector('.menu-sidebar').classList.toggle('active');
            });

        </script>

        @yield('scripts')

    </body>
</html>
