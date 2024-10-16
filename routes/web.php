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

Route::get('/', function () {
    return view('welcome');
});


Route::resource("plantillas",'PlantillasController');
Route::post("plantillas_pdf","PlantillasController@viewPdf");
Route::resource("configuracion",'ConfiguracionController');
Route::resource("casos",'CasosController');
Route::post("casos_pdf", "CasosController@viewCasosPdf");
Route::post("casos_campos_sensibles","CasosController@getSensitiveData");
Route::post("casos_guardar_campos_sensibles","CasosController@saveSensitiveData");
Route::post("casos_update/{id}","CasosController@update");
Route::resource("agrupacion",'AgrupacionesController');
Route::post("agrupacion_eliminacion","AgrupacionesController@deleteGroupsAndFields");
Route::post("agrupacion_guardar_grupo","AgrupacionesController@addGroup");


