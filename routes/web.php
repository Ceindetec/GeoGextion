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

    Route::get('/asesores', 'AsesorController@listaAsesores')->name('listaacesores');
    Route::get('/gridasesores', 'AsesorController@gridAsesores')->name('gridasesores');
    Route::get('/asesor/crear', 'AsesorController@viewCrearAsesor')->name('asesor.crear');
    Route::post('/asesor/crear', 'AsesorController@crearAsesor')->name('asesor.crearp');
    Route::get('/asesor/editar/{id}', 'AsesorController@viewEditarAsesor')->name('asesor.editar');
    Route::post('/asesor/editar/{id}', 'AsesorController@editarAsesor')->name('asesor.editarp');
    Route::post('/asesor/cambiarestado', 'AsesorController@cambiarEstadoAsesor')->name('asesor.cambiarestado');
    Route::get('exportarasesores','AsesorController@exportar')->name('exportasesor');


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

    Route::get('listaempresas', 'EmpresaController@listaEmpresas')->name('listaEmpresas');
    Route::get('gridaempresas', 'EmpresaController@gridEmpresas')->name('gridaempresas');
    Route::get('empresa/crear', 'EmpresaController@viewCrearEmpresa')->name('empresa.crear');
    Route::post('empresa/crear', 'EmpresaController@crearEmpresa');
    Route::post('empresa/cambiarestado', 'EmpresaController@cambiarEstadoEmpresa')->name('empresa.cambiarestado');
    Route::get('empresa/editar/{id}', 'EmpresaController@viewEditarEmpresa')->name('empresa.editar');
    Route::post('empresa/editar/{id}', 'EmpresaController@editarEmpresa');
    Route::get('exportarempresas','EmpresaController@exportar')->name('exportarempresas');

});



/*inicia Adminstrador*/
Route::group(['middleware' => ['superAdministradorEmpresa','validarEstado']], function () {
    Route::get('listaadministrador', 'AdministradorController@listaAdministradores')->name('listaAdministradores');
    Route::get('gridaadministrador', 'AdministradorController@gridAdministradores')->name('gridaadministradores');
    Route::get('administrador/crear', 'AdministradorController@viewCrearAdministrador')->name('administrador.crear');
    Route::post('administrador/crear', 'AdministradorController@crearAdministrador');
    Route::post('administrador/cambiarestado', 'SupervisorController@cambiarEstadoSupervisor')->name('administrador.cambiarestado');
    Route::get('administrador/editar/{id}', 'SupervisorController@viewEditarSupervisor')->name('administrador.editar');
    Route::post('administrador/editar/{id}', 'SupervisorController@editarSupervisor');
    Route::get('exportaradministradores','AdministradorController@exportar')->name('exportaradmin');
});

/*inicia supervisores*/
Route::group(['middleware' => ['administrador','validarEstado']], function () {
    Route::get('listasupervisores', 'SupervisorController@listaSupervisores')->name('listasupervisores');
    Route::get('gridasupervisores', 'SupervisorController@gridSupervisores')->name('gridasupervisores');
    Route::get('supervisor/crear', 'SupervisorController@viewCrearSupervisor')->name('supervisor.crear');
    Route::post('supervisor/crear', 'SupervisorController@crearSupervisor');
    Route::get('supervisor/editar/{id}', 'SupervisorController@viewEditarSupervisor')->name('supervisor.editar');
    Route::post('supervisor/editar/{id}', 'SupervisorController@editarSupervisor');
    Route::post('supervisor/cambiarestado', 'SupervisorController@cambiarEstadoSupervisor')->name('supervisor.cambiarestado');
    Route::get('supervisor/asociar/{id}', 'SupervisorController@asociarAsesorSupervisor')->name('supervisor.asociar');
    Route::get('supervisor/gridnoasesores/{id}', 'SupervisorController@gridNoAsesores')->name('gridnoasesores');
    Route::get('supervisor/gridsiasesores/{id}', 'SupervisorController@gridSiAsesores')->name('gridsiasesores');
    Route::post('supervisor/agregaasesor', 'SupervisorController@agregaAsesor')->name('supervisor.agregaasesor');
    Route::post('supervisor/quitarasesor', 'SupervisorController@quitarAsesor')->name('supervisor.quitarasesor');
    Route::get('exportarsupervisoresase','SupervisorController@exportar')->name('exportsuperase');
});


/*supervisor transporte */

Route::group(['middleware' => ['administrador','validarEstado']], function () {
    Route::get('listasupervisorestransporte', 'SupervisorTransporteController@listaSupervisoresTransporte')->name('listasupervisorestransporte');
    Route::get('gridasupervisorestrasporte', 'SupervisorTransporteController@gridSupervisoresTrasporte')->name('gridasupervisorestrasporte');
    Route::get('supervisortransportecrear', 'SupervisorTransporteController@viewCrearSupervisor')->name('supervisortransporte.crear');
    Route::post('supervisortransportecrear', 'SupervisorTransporteController@crearSupervisor');
    Route::get('supervisortransporteeditar/{id}', 'SupervisorTransporteController@viewEditarSupervisorTransporte')->name('supervisortransporte.editar');
    Route::post('supervisortransporteeditar/{id}', 'SupervisorTransporteController@editarSupervisorTransporte');

    Route::get('exportarsupervisoresasetrans','SupervisorTransporteController@exportar')->name('exporsupertrans');
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
