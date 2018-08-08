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


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('datatable_es', 'IndiomasController@espanol')->name('datatable_es');


Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
//Password reset routes
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');


Route::group(['middleware' => ['auth','validarEstado']], function () {




    Route::get('perfil/usuario', 'PerfilUsuarioController@perfilUsuario')->name('perfil');
    Route::post('perfil/usuario', 'PerfilUsuarioController@actulizarPerfil');

    /*/////////RUTAS UBICAR ASESORES MAPA////////////*/
    Route::get('geoposicionfinal', 'HomeController@geoPosicionfinal')->name('geoposicionfinal');
    Route::get('ubicarasesor', 'HomeController@ubicarasesor')->name('ubicarasesor');
    Route::get('rutaasesor', 'HomeController@rutaasesor')->name('rutaasesor');
    Route::get('updatemarketgeneral', 'HomeController@updatemarketgeneral')->name('updatemarketgeneral');

    Route::get('/home', 'HomeController@index')->name('home')->middleware('nohome'); //TODO mirar como mejorar esto


    /*//////////// RUTAS SUPERADMIN DEL SISTEMA //////////////////*/
    Route::group(['middleware' => ['superAdmin']], function () {
        Route::get('listaempresas', 'EmpresaController@listaEmpresas')->name('listaEmpresas');
        Route::get('gridaempresas', 'EmpresaController@gridEmpresas')->name('gridaempresas');
        Route::get('empresa/crear', 'EmpresaController@viewCrearEmpresa')->name('empresa.crear');
        Route::post('empresa/crear', 'EmpresaController@crearEmpresa');
        Route::post('empresa/cambiarestado', 'EmpresaController@cambiarEstadoEmpresa')->name('empresa.cambiarestado');
        Route::get('empresa/editar/{id}', 'EmpresaController@viewEditarEmpresa')->name('empresa.editar');
        Route::post('empresa/editar/{id}', 'EmpresaController@editarEmpresa');
        Route::get('exportarempresas','EmpresaController@exportar')->name('exportarempresas');
    });

    /*///////// RUTAS SUPERADMIN EMPRESAS ////////////*/
    Route::group(['middleware' => ['superAdministradorEmpresa']], function () {


        Route::get('listaadministrador', 'AdministradorController@listaAdministradores')->name('listaAdministradores');
        Route::get('gridaadministrador', 'AdministradorController@gridAdministradores')->name('gridaadministradores');
        Route::get('administrador/crear', 'AdministradorController@viewCrearAdministrador')->name('administrador.crear');
        Route::post('administrador/crear', 'AdministradorController@crearAdministrador');
        Route::post('administrador/cambiarestado', 'SupervisorController@cambiarEstadoSupervisor')->name('administrador.cambiarestado');
        Route::get('administrador/editar/{id}', 'SupervisorController@viewEditarSupervisor')->name('administrador.editar');
        Route::post('administrador/editar/{id}', 'SupervisorController@editarSupervisor');
        Route::get('exportaradministradores','AdministradorController@exportar')->name('exportaradmin');

        Route::get('configuracion','ConfiguracionController@index')->name('configuracion');
        Route::post('configuracion','ConfiguracionController@guardar');
    });


    //SAE AE
    Route::group(['middleware' => ['administrador']], function () {


        /*/////// SUPERVISOR TRANSPORTE /////////*/
        Route::get('listasupervisorestransporte', 'SupervisorTransporteController@listaSupervisoresTransporte')->name('listasupervisorestransporte');
        Route::get('gridasupervisorestrasporte', 'SupervisorTransporteController@gridSupervisoresTrasporte')->name('gridasupervisorestrasporte');
        Route::get('supervisortransportecrear', 'SupervisorTransporteController@viewCrearSupervisor')->name('supervisortransporte.crear');
        Route::post('supervisortransportecrear', 'SupervisorTransporteController@crearSupervisor');
        Route::get('supervisortransporteeditar/{id}', 'SupervisorTransporteController@viewEditarSupervisorTransporte')->name('supervisortransporte.editar');
        Route::post('supervisortransporteeditar/{id}', 'SupervisorTransporteController@editarSupervisorTransporte');
        Route::get('exportarsupervisoresasetrans', 'SupervisorTransporteController@exportar')->name('exporsupertrans');

        /*/////// SUPERVISOR SUPERVISORES ASESORES /////////*/
        Route::get('listasupervisores', 'SupervisorController@listaSupervisores')->name('listasupervisores');
        Route::get('gridasupervisores', 'SupervisorController@gridSupervisores')->name('gridasupervisores');
        Route::get('supervisor/crear', 'SupervisorController@viewCrearSupervisor')->name('supervisor.crear');
        Route::post('supervisor/crear', 'SupervisorController@crearSupervisor');
        Route::get('supervisor/editar/{id}', 'SupervisorController@viewEditarSupervisor')->name('supervisor.editar');
        Route::post('supervisor/editar/{id}', 'SupervisorController@editarSupervisor');
        Route::post('supervisor/cambiarestado', 'SupervisorController@cambiarEstadoSupervisor')->name('supervisor.cambiarestado');
        Route::get('exportarsupervisoresase', 'SupervisorController@exportar')->name('exportsuperase');

        /*////////////////////RUTAS PARA ASIGNAR ASESOR AL SUPERVISOR ///////////////////*/
        Route::get('supervisor/asociar/{id}', 'SupervisorController@asociarAsesorSupervisor')->name('supervisor.asociar');
        Route::get('supervisor/gridnoasesores/{id}', 'SupervisorController@gridNoAsesores')->name('gridnoasesores');
        Route::get('supervisor/gridsiasesores/{id}', 'SupervisorController@gridSiAsesores')->name('gridsiasesores');
        Route::post('supervisor/agregaasesor', 'SupervisorController@agregaAsesor')->name('supervisor.agregaasesor');
        Route::post('supervisor/quitarasesor', 'SupervisorController@quitarAsesor')->name('supervisor.quitarasesor');

        /*////////////////////RUTAS PARA ASIGNAR TRANSPORTADOR AL SUPERVISOR ///////////////////*/
        Route::get('supervisortransporte/asociar/{id}', 'SupervisorTransporteController@asociarTrasportadorSupervisor')->name('supervisortransporte.asociar');
        Route::get('supervisor/gridnoasesorestrans/{id}', 'SupervisorTransporteController@gridNoTransportador')->name('gridnoasesorestrans');
        Route::get('supervisor/gridsiasesorestrans/{id}', 'SupervisorTransporteController@gridSiTransportador')->name('gridsiasesorestrans');
        Route::post('supervisor/agregatransportador', 'SupervisorTransporteController@agregaTransportador')->name('supervisor.agregatransportador');
        Route::post('supervisor/quitartransportador', 'SupervisorTransporteController@quitarTransportador')->name('supervisor.quitartransportador');



    });


    //SAE AE SPA
    /*/////////RUTAS ASESORES////////////*/
    Route::group(['middleware' => ['supervisorAsesor']], function () {



        Route::get('/asesores', 'AsesorController@listaAsesores')->name('listaacesores');
        Route::get('/gridasesores', 'AsesorController@gridAsesores')->name('gridasesores');
        Route::get('/asesor/crear', 'AsesorController@viewCrearAsesor')->name('asesor.crear');
        Route::post('/asesor/crear', 'AsesorController@crearAsesor')->name('asesor.crearp');
        Route::get('/asesor/editar/{id}', 'AsesorController@viewEditarAsesor')->name('asesor.editar');
        Route::post('/asesor/editar/{id}', 'AsesorController@editarAsesor')->name('asesor.editarp');
        Route::post('/asesor/cambiarestado', 'AsesorController@cambiarEstadoAsesor')->name('asesor.cambiarestado');
        Route::get('exportarasesores', 'AsesorController@exportar')->name('exportasesor');
    });


    //SAE AE SPT
    Route::group(['middleware' => ['supervisorTrasnporte']], function () {


        /*/////////RUTAS TRASNSPORTADORES////////////*/
        Route::get('listartransportadores', 'TransporteController@listarTrasportadores')->name('listatrasportador');
        Route::get('gridtransportadores', 'TransporteController@gridTransportadores')->name('gridtransportadores');
        Route::get('creartransportador', 'TransporteController@viewCrearTransportador')->name('transportadores.crear');
        Route::get('transportadoreditar/{id}', 'TransporteController@viewEditarTransportador')->name('transportadores.editar');
        Route::post('transportadoreditar/{id}', 'TransporteController@editarTransportador');
        Route::post('creartransportador', 'TransporteController@crearTransportador');
        Route::get('exportatrasportadores', 'TransporteController@exportarTransportadores')->name('exportatrasportadores');

        /*/////// RUTA DE LOS VEHICULOS ///////*/
        Route::get('listarvehiculo', 'VehiculoController@listarVehiculos')->name('listarvehiculo');
        Route::get('crearvehiculo', 'VehiculoController@viewCrearVehiculo')->name('vehiculo.crear');
        Route::post('crearvehiculo', 'VehiculoController@crearVehiculo');
        Route::get('gridvehiculos', 'VehiculoController@gridVehiculos')->name('gridvehiculos');
        Route::get('editarvehiculo/{id}', 'VehiculoController@viewEditarVehiculo')->name('vehiculo.editar');
        Route::post('editarvehiculo/{id}', 'VehiculoController@editarVehiculo');
        Route::post('cambiarestadovehiculo', 'VehiculoController@cambiarEstado')->name('vehiculo.estado');
        Route::get('exportatrvehiculos', 'VehiculoController@exportarVehiculos')->name('exportatrvehiculos');

    });



    /*/////// RUTAS REPORTES /////////*/
    Route::get('consulta', 'HomeController@consulta')->name('consulta');
    Route::get('resultadoconsulta', 'HomeController@resultadoConsulta')->name('resultadoconsulta');
    Route::get('modalpunto', 'HomeController@modalPunto')->name('modalpunto');
    Route::get('exportarpdf', 'HomeController@exportarPdf')->name('exportarpdf');
    Route::get('exportarexcel', 'HomeController@exportarExcel')->name('exportarexcel');


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
