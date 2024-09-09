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
Route::post("casos_banco_datos","CasosController@getDataBank");
Route::post("casos_guardar_banco_datos","CasosController@saveDataBank");
Route::post("casos_update/{id}","CasosController@update");

