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
    return redirect()->route('login');
});

Route::get('datatable_es', 'IndiomasController@espanol')->name('datatable_es');

//Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/asesores', 'HomeController@listaAsesores')->name('listaacesores');
Route::get('/gridasesores', 'HomeController@gridAsesores')->name('gridasesores');
Route::get('/asesor/crear', 'HomeController@viewCrearAsesor')->name('asesor.crear');
Route::post('/asesor/crear', 'HomeController@crearAsesor')->name('asesor.crearp');
Route::get('/asesor/editar/{id}', 'HomeController@viewEditarAsesor')->name('asesor.editar');
Route::post('/asesor/editar/{id}', 'HomeController@editarAsesor')->name('asesor.editarp');
Route::post('/asesor/cambiarestado', 'HomeController@cambiarEstadoAsesor')->name('asesor.cambiarestado');
Route::get('geoposicionfinal', 'HomeController@geoPosicionfinal')->name('geoposicionfinal');
Route::get('ubicarasesor', 'HomeController@ubicarasesor')->name('ubicarasesor');
Route::get('rutaasesor', 'HomeController@rutaasesor')->name('rutaasesor');
Route::get('updatemarketgeneral', 'HomeController@updatemarketgeneral')->name('updatemarketgeneral');


Route::get('consulta', 'HomeController@consulta')->name('consulta');
Route::get('resultadoconsulta', 'HomeController@resultadoConsulta')->name('resultadoconsulta');
Route::get('modalpunto', 'HomeController@modalPunto')->name('modalpunto');
Route::get('exportarpdf', 'HomeController@exportarPdf')->name('exportarpdf');


Route::get('perfil/usuario', 'PerfilUsuarioController@perfilUsuario')->name('perfil');
Route::post('perfil/usuario', 'PerfilUsuarioController@actulizarPerfil');




Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
//Password reset routes
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');


Route::get('mapas', function(){
    $config = array();
    $config['center'] = 'auto';
    $config['onboundschanged'] = 'if (!centreGot) {
            var mapCentre = map.getCenter();
            marker_0.setOptions({
                position: new google.maps.LatLng(mapCentre.lat(), mapCentre.lng())
            });
        }
        centreGot = true;';
    app('map')->initialize($config);
    $direccion = app('map')->get_address_from_lat_long('4.1359736','-73.6073006');
    dd($direccion);

});