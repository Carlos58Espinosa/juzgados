<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () { return view('login'); });
Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/principal', function () { return view('layout');});
    Route::resource("plantillas",'PlantillasController');
    Route::get("plantillas_pdf","PlantillasController@viewPdf");
    Route::post("plantillas_clonar","PlantillasController@clone");
    Route::resource("configuracion",'ConfiguracionController');
    Route::post("configuracion_clonar","ConfiguracionController@clone");
    Route::resource("casos",'CasosController');
    Route::get("casos_pdf", "CasosController@viewCasosPdf");
    Route::post("casos_campos_sensibles","CasosController@getSensitiveData");
    Route::post("casos_guardar_campos_sensibles","CasosController@saveSensitiveData");
    Route::post("casos_update/{id}","CasosController@update");
    Route::resource("agrupacion",'AgrupacionesController');
    Route::post("agrupacion_eliminacion","AgrupacionesController@deleteGroupsAndFields");
    Route::post("agrupacion_guardar_grupo","AgrupacionesController@addGroup");
    Route::resource('usuarios', 'UsuariosController');
    Route::post('activate_user', 'UsuariosController@activateUser');
    Route::post('usuarios_color_config', 'UsuariosController@changeColorConfig');
    Route::post('casos/obtener_formato', 'CasosController@getFormat');
    Route::post('casos/guardar_logo', 'CasosController@saveLogo');
    Route::post('casos/guardar_formato', 'CasosController@saveFormat');
});




