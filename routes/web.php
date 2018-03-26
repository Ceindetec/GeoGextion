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


//DB::listen(function ($query){
//    echo "<pre>{$query->sql}</pre>";
//});

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
//Password reset routes
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');


Route::get('perfil/usuario', 'PerfilUsuarioController@perfilUsuario')->name('perfil');
Route::post('perfil/usuario', 'PerfilUsuarioController@actulizarPerfil');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('datatable_es', 'IndiomasController@espanol')->name('datatable_es');

//Auth::routes();

Route::group(['middleware' => ['supervisor','validarEstado']], function () {
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
    Route::get('exportarexcel', 'HomeController@exportarExcel')->name('exportarexcel');

});


/*inicia superAdminstrador*/
Route::group(['middleware' => ['superAdmin','validarEstado']], function () {
    Route::get('listaempresas', 'HomeController@listaEmpresas')->name('listaEmpresas');
    Route::get('gridaempresas', 'HomeController@gridEmpresas')->name('gridaempresas');
    Route::get('empresa/crear', 'HomeController@viewCrearEmpresa')->name('empresa.crear');
    Route::post('empresa/crear', 'HomeController@crearEmpresa');
    Route::post('empresa/cambiarestado', 'HomeController@cambiarEstadoEmpresa')->name('empresa.cambiarestado');
    Route::get('empresa/editar/{id}', 'HomeController@viewEditarEmpresa')->name('empresa.editar');
    Route::post('empresa/editar/{id}', 'HomeController@editarEmpresa');
});



/*inicia Adminstrador*/
Route::group(['middleware' => ['superAdministradorEmpresa','validarEstado']], function () {
    Route::get('listaadministrador', 'HomeController@listaAdministradores')->name('listaAdministradores');
    Route::get('gridaadministrador', 'HomeController@gridAdministradores')->name('gridaadministradores');
    Route::get('administrador/crear', 'HomeController@viewCrearAdministrador')->name('administrador.crear');
    Route::post('administrador/crear', 'HomeController@crearAdministrador');
    Route::post('administrador/cambiarestado', 'HomeController@cambiarEstadoSupervisor')->name('administrador.cambiarestado');
    Route::get('administrador/editar/{id}', 'HomeController@viewEditarSupervisor')->name('administrador.editar');
    Route::post('administrador/editar/{id}', 'HomeController@editarSupervisor');
});

/*inicia supervisores*/
Route::group(['middleware' => ['administrador','validarEstado']], function () {
    Route::get('listasupervisores', 'HomeController@listaSupervisores')->name('listasupervisores');
    Route::get('gridasupervisores', 'HomeController@gridSupervisores')->name('gridasupervisores');
    Route::get('supervisor/crear', 'HomeController@viewCrearSupervisor')->name('supervisor.crear');
    Route::post('supervisor/crear', 'HomeController@crearSupervisor');
    Route::get('supervisor/editar/{id}', 'HomeController@viewEditarSupervisor')->name('supervisor.editar');
    Route::post('supervisor/editar/{id}', 'HomeController@editarSupervisor');
    Route::post('supervisor/cambiarestado', 'HomeController@cambiarEstadoSupervisor')->name('supervisor.cambiarestado');
    Route::get('supervisor/asociar/{id}', 'HomeController@asociarAsesorSupervisor')->name('supervisor.asociar');
    Route::get('supervisor/gridnoasesores/{id}', 'HomeController@gridNoAsesores')->name('gridnoasesores');
    Route::get('supervisor/gridsiasesores/{id}', 'HomeController@gridSiAsesores')->name('gridsiasesores');
    Route::post('supervisor/agregaasesor', 'HomeController@agregaAsesor')->name('supervisor.agregaasesor');
    Route::post('supervisor/quitarasesor', 'HomeController@quitarAsesor')->name('supervisor.quitarasesor');
});

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

Route::get('insert', 'HomeController@insertlocoMedallo');
