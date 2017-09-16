<?php

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

Route::get('datatable_es', 'IndiomasController@espanol')->name('datatable_es');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/asesores', 'HomeController@listaAsesores')->name('listaacesores');
Route::get('/gridasesores', 'HomeController@gridAsesores')->name('gridasesores');
Route::get('/asesor/crear', 'HomeController@viewCrearAsesor')->name('asesor.crear');
Route::post('/asesor/crear', 'HomeController@crearAsesor')->name('asesor.crearp');
Route::get('/asesor/editar/{id}', 'HomeController@viewEditarAsesor')->name('asesor.editar');
Route::post('/asesor/editar/{id}', 'HomeController@editarAsesor')->name('asesor.editarp');

