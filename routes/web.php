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

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\User;
// Route::get('/auth/azure', function () {
//     return Socialite::driver('microsoft')
//         ->scopes(['openid', 'email', 'profile'])
//         ->redirect();
// });

// Route::get('/auth/azure/callback', function () {
//     $azureUser = Socialite::driver('microsoft')->stateless()->user();

//     $user = \App\Models\User::firstOrCreate(
//         ['email' => $azureUser->getEmail()],
//         [
//             'name' => $azureUser->getName() ?? 'Sin Nombre',
//             'email_verified_at' => now(),
//             'password' => bcrypt(Str::random(32)),
//         ]
//     );

//     Auth::login($user);

//     return redirect('/home'); // o json si estás haciendo login para API
// });




Auth::routes();


Route::get('/login2', function () {
    return view('auth.login2'); // tu vista del formulario
});

// Route::post('/loginazure', 'Auth\LoginController@loginAzure')->name('loginazure');
// Route::get('/auth/azure/callback', 'Auth\LoginController@azureCallback');

Route::post('/loginAzure', 'Auth\LoginController@loginAzure')->name('loginAzure');
Route::get('/auth/azure/callback', 'Auth\LoginController@azureCallback')->name('azure.callback');

// Route::get('login2', 'Auth\LoginController@redirectToAzure');

// Route::get('/auth/azure/callback', function () {
//     $azureUser = Socialite::driver('microsoft')->stateless()->user();

//     // dd($azureUser);

//     // Obtén el email
//     $email = $azureUser->getEmail();

//     // Busca usuario en tu sistema
//     $user = User::where('email', $email)->where('active', 1)->first();


//     if ($user) {
//         Auth::login($user);
//         return redirect('/home');
//     } else {
//         return redirect('/login')->with('error', 'El usuario no existe en el sistema.');
//     }
// });

Route::post('login', 'Auth\LoginController@login');

//Rutas para cambio de contraseña antes de Login
Route::get('resetPassword', 'Auth\LoginController@resetPassword')->name('resetPassword');
Route::post('resetPasswordStore', 'Auth\LoginController@resetPasswordStore')->name('resetPasswordStore');
Route::get('resetPasswordLogin', 'Auth\LoginController@resetPasswordLogin')->name('resetPasswordLogin');
Route::get('recoveryPassword', 'Auth\LoginController@recoveryPassword')->name('recoveryPassword');
Route::post('recoveryEmail', 'Auth\LoginController@recoveryEmail')->name('recoveryEmail');
Route::get('resetPasswordRecovery', 'Auth\LoginController@resetPasswordRecovery')->name('resetPasswordRecovery');
Route::post('resetPasswordRecoveryStore', 'Auth\LoginController@resetPasswordRecoveryStore')->name('resetPasswordRecoveryStore');



Route::group(["middleware" => "auth"], function () {
    Route::post('/procesaPDF', 'PdfController@procesarArchivo')->name('procesaPDF');

    Route::get('/', 'WorkOrderController@index')->name('home');
    Route::get('', 'WorkOrderController@index')->name('home');
    // Ordenes de trabajo
    Route::get('/home', 'WorkOrderController@index')->name('home');
    Route::get('/home2', 'WorkOrderController@index2')->name('home2');
    Route::get('/ordenes-trabajo', 'WorkOrderController@index')->name('Ots');
    Route::get('/ordenes-trabajo2', 'WorkOrderController@index2')->name('Ots2');
    Route::get('/crear-ot', 'WorkOrderController@create')->name('nuevaOt')->middleware('role:Administrador,Jefe de Ventas,Vendedor,Dibujante Técnico,Jefe de Diseño Estructural,Vendedor Externo,Jefe de Diseño Gráfico,Diseñador');
    Route::get('/crear-ot-old', 'WorkOrderOldController@create')->name('nuevaOtOld')->middleware('role:Administrador,Jefe de Ventas,Vendedor,Dibujante Técnico,Jefe de Diseño Estructural,Vendedor Externo,Jefe de Diseño Gráfico,Diseñador');
    Route::get('/select-ot', 'WorkOrderController@select')->name('selectOt')->middleware('role:Administrador,Jefe de Ventas,Vendedor,Dibujante Técnico,Jefe de Diseño Estructural,Vendedor Externo,Jefe de Diseño Gráfico,Diseñador');
    Route::post('/guardar', 'WorkOrderController@store')->name('storeOt')->middleware('role:Administrador,Jefe de Ventas,Vendedor,Dibujante Técnico,Jefe de Diseño Estructural,Vendedor Externo,Jefe de Diseño Gráfico,Diseñador');
    Route::get('/edit-ot/{id}', 'WorkOrderController@edit')->name('editOt');
    Route::get('/edit-ot-old/{id}', 'WorkOrderOldController@edit')->name('editOtOld');
    Route::get('/edit-description-ot/{id}/{type_edit}', 'WorkOrderController@editDescriptionOt')->name('editDescriptionOt');
    Route::put('/actualizar-ot/{id}', 'WorkOrderController@update')->name('updateOt');
    Route::put('/actualizar-descripcion-ot/{id}', 'WorkOrderController@updateDescripcion')->name('updateDescriptionOt');
    Route::post('/modalOT', 'WorkOrderController@modalOT')->name('modalOT');
    Route::post('/modalOTEstudio', 'WorkOrderController@modalOTEstudio')->name('modalOTEstudio');
    Route::post('/modalOTLicitacion', 'WorkOrderController@modalOTLicitacion')->name('modalOTLicitacion');
    Route::post('/modalOTFichaTecnica', 'WorkOrderController@modalOTFichaTecnica')->name('modalOTFichaTecnica');
    // Nuevas OT Area de Desarrollo
    Route::get('/crear-licitacion', 'WorkOrderController@createLicitacion')->name('nuevaOtLicitacion')->middleware('role:Administrador,Jefe de Ventas,Vendedor,Dibujante Técnico,Jefe de Diseño Estructural,Vendedor Externo');
    Route::get('/crear-ficha-tecnica', 'WorkOrderController@createFichaTecnica')->name('nuevaOtFichaTecnica')->middleware('role:Administrador,Jefe de Ventas,Vendedor,Dibujante Técnico,Jefe de Diseño Estructural,Vendedor Externo');
    Route::get('/crear-estudio-benchmarking', 'WorkOrderController@createEstudioBenchmarking')->name('nuevaOtEstudioBenchmarking')->middleware('role:Administrador,Jefe de Ventas,Vendedor,Dibujante Técnico,Jefe de Diseño Estructural,Vendedor Externo');
    Route::get('/edit-ot-licitacion/{id}', 'WorkOrderController@editOtLicitacion')->name('editOtLicitacion');
    Route::get('/edit-ot-ficha/{id}', 'WorkOrderController@editOtFicha')->name('editOtFicha');
    Route::get('/edit-ot-estudio-bench/{id}', 'WorkOrderController@editOtEstudioBench')->name('editOtEstudioBench');
    Route::get('/generar-pdf-estudio-bench/{id}', 'WorkOrderController@generarPdfEstudioBench')->name('generarPdfEstudioBench');
    Route::post('/cargaDetallesEstudio', 'WorkOrderController@cargaDetallesEstudio')->name('carga_detalles_estudio');
    //Cotizar multiples OT
    Route::get('/cotizar-multiples-ot', 'WorkOrderController@cotizarMultiplesOt')->name('cotizarMultiplesOt');
    Route::post('/nuevaCotizacion', 'WorkOrderController@nuevaCotizacion')->name('nuevaCotizacion');

    // Creacion de sufijo y prefijo codigo de material
    Route::put('/crear-codigo-material/{idOt}', 'WorkOrderController@createCodigoMaterial')->name('createCodigoMaterial');
    // Duplicacion de ot
    Route::get('/duplicar/{idOt}', 'WorkOrderController@duplicate')->name('duplicateOt');
    Route::get('/duplicar-old/{idOt}', 'WorkOrderOldController@duplicate')->name('duplicateOtOld');
    // Creacion de material y cad de ot
    Route::put('/crear-cad-material/{idOt}', 'WorkOrderController@createCadMaterial')->name('createCadMaterial');
    // Validar CAD
    Route::get('/cad', 'MaterialController@findCad')->name('findCad');
    Route::get('/material', 'MaterialController@findMaterial')->name('findMaterial');
    // ficha excel
    Route::get('/crear-ot-excel/{id}', 'WorkOrderExcelController@create')->name('nuevaOtExcel'); //->middleware('role:Administrador,Jefe de Ventas,Vendedor')
    Route::post('/guardar-excel/{id}', 'WorkOrderExcelController@store')->name('storeOtExcel'); //->middleware('role:Administrador,Jefe de Ventas,Vendedor')
    Route::get('/descargar-reporte-excel/{id}', 'WorkOrderExcelController@descargarReporteExcel')->name('descargarReporte'); //->middleware('role:Administrador,Jefe de Ventas,Vendedor')
    Route::get('/descargar-excel-sap/{id}', 'WorkOrderExcelController@descargarExcelSap')->name('descargarExcelSap'); //->middleware('role:Administrador,Jefe de Ventas,Vendedor')
    Route::get('/descargar-excel-sap-semielaborado/{id}', 'WorkOrderExcelController@descargarExcelSapSemielaborado')->name('descargarExcelSapSemielaborado'); //->middleware('role:Administrador,Jefe de Ventas,Vendedor')
    // Route::get('/edit-ot-excel/{id}', 'WorkOrderExcelController@edit')->name('editOtExcel');
    // Route::put('/actualizar-ot-excel/{id}', 'WorkOrderExcelController@update')->name('updateOtExcel');


    // Notificaciones OT
    Route::get('/notificaciones', 'NotificationController@index')->name('notificacionesOT');


    Route::put('inactivarNotificacion/{id}', 'NotificationController@inactivarNotificacion')->name('inactivarNotificacion');
    // Aprobacion OT
    Route::get('/listadoAprobacion', 'WorkOrderController@listadoAprobacion')->name('listadoAprobacion')->middleware('role:Administrador,Jefe de Ventas,Jefe de Diseño Estructural,Super Administrador');

    Route::put('aprobarOt/{id}', 'WorkOrderController@aprobarOt')->name('aprobarOt');
    Route::put('rechazarOt/{id}', 'WorkOrderController@rechazarOt')->name('rechazarOt');

    // Reporteria
    Route::get('/reportes', 'ReportController@index')->name('reporteria');
    Route::get('/reporte1', 'ReportController@reporte1')->name('reporte1');
    Route::get('/reporte2', 'ReportController@reporte2')->name('reporte2');
    Route::get('/reporte-gestion-carga-ot-mes', 'ReportController@reportGestionLoadOtMonth')->name('reportGestionLoadOtMonth');
    Route::get('/reporte-conversion-ot', 'ReportController@reportCompletedOt')->name('reportCompletedOt');
    Route::get('/reporte-conversion-ot-entre-fechas', 'ReportController@reportCompletedOtEntreFechas')->name('reportCompletedOtEntreFechas');
    Route::get('/reporte-gestion-ot-activos', 'ReportController@reportGestionOtActives')->name('reportGestionsActive');
    Route::get('/reporte-tiempos-por-area-ot-mes', 'ReportController@reportTimeByAreaOtMonth')->name('reportTimeByAreaOtMonth');
    Route::get('/reporte-motivos-rechazos-mes', 'ReportController@reportReasonsRejectionMonth')->name('reportRechazos');
    Route::get('/reporte-rechazos-mes', 'ReportController@reportRechazosPorMes')->name('reportRechazosPorMes');
    Route::get('/reporte-ot-activas-por-area', 'ReportController@reportActiveOtsPerArea')->name('reportActiveOtsPerArea');
    Route::get('/reporte-anulaciones', 'ReportController@reportAnulaciones')->name('reportAnulaciones');
    Route::get('/reporte-muestras', 'ReportController@reportMuestras')->name('reportMuestras');
    Route::get('/reporte-indicador-sala-muestras', 'ReportController@reportIndicadorSalaMuestra')->name('reportIndicadorSalaMuestra');
    Route::get('/reporte-diseno-estructutal-sala-muestra', 'ReportController@reportDisenoEstructuralySalaMuestra')->name('reportDisenoEstructuralySalaMuestra');
    Route::get('/reporte-sala-muestra', 'ReportController@reportSalaMuestra')->name('reportSalaMuestra');
    Route::get('/reporte-tiempo-primera-muestra', 'ReportController@reportTiempoPrimeraMuestra')->name('reportTiempoPrimeraMuestra');
    Route::get('/reporte-tiempo-disenador-externo', 'ReportController@reportTiempoDisenadorExterno')->name('reportTiempoDisenadorExterno');
    Route::get('/reporte-tiempo-disenador-externo-ajuste', 'ReportController@reportTiempoDisenadorExternoAjuste')->name('reportTiempoDisenadorExternoAjuste');


    //reportes 2

    Route::get('/reportesNew', 'Report2Controller@index')->name('reporteriaNew');
    Route::get('/reporte1New', 'Report2Controller@reporte1')->name('reporte1New');
    Route::get('/reporte2New', 'Report2Controller@reporte2')->name('reporte2New');
    Route::get('/reporteNew-gestion-carga-ot-mes', 'Report2Controller@reportGestionLoadOtMonth')->name('reportGestionLoadOtMonthNew');
    Route::get('/reporteNew-conversion-ot', 'Report2Controller@reportCompletedOt')->name('reportCompletedOtNew');
    Route::get('/reporteNew-conversion-ot-entre-fechas', 'Report2Controller@reportCompletedOtEntreFechas')->name('reportCompletedOtEntreFechasNew');
    Route::get('/reporteNew-gestion-ot-activos', 'Report2Controller@reportGestionOtActives')->name('reportGestionsActiveNew');
    Route::get('/reporteNew-tiempos-por-area-ot-mes', 'Report2Controller@reportTimeByAreaOtMonth')->name('reportTimeByAreaOtMonthNew');
    Route::get('/reporteNew-motivos-rechazos-mes', 'Report2Controller@reportReasonsRejectionMonth')->name('reportRechazosNew');
    Route::get('/reporteNew-rechazos-mes', 'Report2Controller@reportRechazosPorMes')->name('reportRechazosPorMesNew');
    Route::get('/reporteNew-ot-activas-por-area', 'Report2Controller@reportActiveOtsPerArea')->name('reportActiveOtsPerAreaNew');
    Route::get('/reporteNew-anulaciones', 'Report2Controller@reportAnulaciones')->name('reportAnulacionesNew');
    Route::get('/reporteNew-muestras', 'Report2Controller@reportMuestras')->name('reportMuestrasNew');
    Route::get('/reporteNew-indicador-sala-muestras', 'Report2Controller@reportIndicadorSalaMuestra')->name('reportIndicadorSalaMuestraNew');
    Route::get('/reporteNew-diseno-estructutal-sala-muestra', 'Report2Controller@reportDisenoEstructuralySalaMuestra')->name('reportDisenoEstructuralySalaMuestraNew');
    Route::get('/reporteNew-sala-muestra', 'Report2Controller@reportSalaMuestra')->name('reportSalaMuestraNew');
    Route::get('/reporteNew-tiempo-primera-muestra', 'Report2Controller@reportTiempoPrimeraMuestra')->name('reportTiempoPrimeraMuestraNew');
    Route::get('/reporteNew-tiempo-disenador-externo', 'Report2Controller@reportTiempoDisenadorExterno')->name('reportTiempoDisenadorExternoNew');


        //reportes 3

    Route::get('/reportesNew1', 'Report3Controller@index')->name('reporteriaNew1');
    Route::get('/reporte1New1', 'Report3Controller@reporte1')->name('reporte1New1');
    Route::get('/reporte2New1', 'Report3Controller@reporte2')->name('reporte2New1');
    Route::get('/reporteNew-gestion-carga-ot-mes1', 'Report3Controller@reportGestionLoadOtMonth')->name('reportGestionLoadOtMonthNew1');
    Route::get('/reporteNew-conversion-ot1', 'Report3Controller@reportCompletedOt')->name('reportCompletedOtNew1');
    Route::get('/reporteNew-conversion-ot-entre-fechas1', 'Report3Controller@reportCompletedOtEntreFechas')->name('reportCompletedOtEntreFechasNew1');
    Route::get('/reporteNew-gestion-ot-activos1', 'Report3Controller@reportGestionOtActives')->name('reportGestionsActiveNew1');
    Route::get('/reporteNew-tiempos-por-area-ot-mes1', 'Report3Controller@reportTimeByAreaOtMonth')->name('reportTimeByAreaOtMonthNew1');
    Route::get('/reporteNew-motivos-rechazos-mes1', 'Report3Controller@reportReasonsRejectionMonth')->name('reportRechazosNew1');
    Route::get('/reporteNew-rechazos-mes1', 'Report3Controller@reportRechazosPorMes')->name('reportRechazosPorMesNew1');
    Route::get('/reporteNew-ot-activas-por-area1', 'Report3Controller@reportActiveOtsPerArea')->name('reportActiveOtsPerAreaNew1');
    Route::get('/reporteNew-anulaciones1', 'Report3Controller@reportAnulaciones')->name('reportAnulacionesNew1');
    Route::get('/reporteNew-muestras1', 'Report3Controller@reportMuestras')->name('reportMuestrasNew1');
    Route::get('/reporteNew-indicador-sala-muestras1', 'Report3Controller@reportIndicadorSalaMuestra')->name('reportIndicadorSalaMuestraNew1');
    Route::get('/reporteNew-diseno-estructutal-sala-muestra1', 'Report3Controller@reportDisenoEstructuralySalaMuestra')->name('reportDisenoEstructuralySalaMuestraNew1');
    Route::get('/reporteNew-sala-muestra1', 'Report3Controller@reportSalaMuestra')->name('reportSalaMuestraNew1');
    Route::get('/reporteNew-tiempo-primera-muestra1', 'Report3Controller@reportTiempoPrimeraMuestra')->name('reportTiempoPrimeraMuestraNew1');
    Route::get('/reporteNew-tiempo-disenador-externo1', 'Report3Controller@reportTiempoDisenadorExterno')->name('reportTiempoDisenadorExternoNew1');

    // Gestiones
    Route::get('/gestionarOt/{id}', 'ManagementController@gestionarOt')->name('gestionarOt');
    Route::get('/reactivarOt/{id}', 'ManagementController@reactivarOt')->name('reactivarOt');
    Route::get('/detalleLogOt/{id}', 'ManagementController@detalleLogOt')->name('detalleLogOt');
    Route::get('/descargar-detalle-log-excel/{id}', 'ManagementController@descargarDetalleLogExcel')->name('descargarDetalleLogExcel');
    Route::post('/crear-gestion/{id}', 'ManagementController@store')->name('crear-gestion');
    Route::post('/leer-pdf', 'ManagementController@read_pdf')->name('leer-pdf');
    Route::post('/guardar-pdf', 'ManagementController@store_pdf')->name('guardar-pdf');
    Route::post('/validar-carton-lector-pdf', 'ManagementController@validar_carton')->name('validar-carton-lector-pdf');
    Route::post('/respuesta/{id}', 'ManagementController@storeRespuesta')->name('respuesta');
    Route::get('/detalleMckee', 'ManagementController@detalleMckee')->name('detalleMckee');
    Route::get('/generar_diseño_pdf', 'ManagementController@generar_diseño_pdf')->name("generar_diseño_pdf");
    Route::get('/obtener-proveedor-externo-diseño', 'ManagementController@obtenerProveedorExternoDiseño')->name("obtener-proveedor-externo-diseño");
    Route::post('/leer-boceto-pdf', 'ManagementController@obtenerDatosPdf')->name('leer-boceto-pdf');
    Route::post('/guardar-boceto-pdf', 'ManagementController@store_boceto_pdf')->name('guardar-boceto-pdf');

    // MUESTRAS DE OT
    // Route::post('/crear-muestra', 'MuestraController@store')->name('crear-muestra');
    Route::post('/crear-muestra', 'MuestraController@store')->name('crear-muestra');
    Route::get('/eliminar-muestra/{id}', 'MuestraController@delete')->name('eliminar-muestra');

    Route::get('/retomarOt/{id}', 'ManagementController@retomarOt')->name('retomarOt');

    //Route::post('/rechazarMuestra', 'MuestraController@rechazarMuestra')->name('rechazarMuestra');
    Route::get('/rechazarMuestra/{id}', 'MuestraController@rechazarMuestra')->name('rechazarMuestra');
    Route::post('/terminarMuestra', 'MuestraController@terminarMuestra')->name('terminarMuestra');
    Route::post('/anularMuestra', 'MuestraController@anularMuestra')->name('anularMuestra');
    Route::post('/devolverMuestra', 'MuestraController@devolverMuestra')->name('devolverMuestra');
    Route::get('/getMuestrasOt/{id}', 'MuestraController@getMuestrasOt')->name('getMuestrasOt');

    Route::put('muestraPrioritaria/{id}', 'MuestraController@muestraPrioritaria')->name('muestraPrioritaria');
    Route::put('muestraNoPrioritaria/{id}', 'MuestraController@muestraNoPrioritaria')->name('muestraNoPrioritaria');

    // generar pdf etiqueta de info producto de muestra
    Route::get('generar_etiqueta_muestra_pdf', 'MuestraController@generar_etiqueta_muestra_pdf')->name("generar_etiqueta_muestra_pdf");
    Route::get('generar_etiqueta_cliente_pdf', 'MuestraController@generar_etiqueta_cliente_pdf')->name("generar_etiqueta_cliente_pdf");
    Route::get('visualizar_muestra_html', 'MuestraController@visualizar_muestra_html')->name("visualizar_muestra_html");

    // Modulo de asignaciones
    Route::get('/asignaciones', 'UserWorkOrderController@index')->name('asignaciones');
    Route::post('/modalAsignacion', 'UserWorkOrderController@modalAsignacion')->name('modalAsignacion');
    Route::post('/asignarOT', 'UserWorkOrderController@asignarOT')->name('asignarOT');
    Route::get('/asignacionesConMensaje', 'UserWorkOrderController@asignacionesConMensaje')->name('asignacionesConMensaje');


    // Admin
    // Loggear cualquier usuario desde admin
    Route::get('log-user/{id}', 'UserController@logearUsuario')->name('logearUsuario')->middleware('role:Administrador');

    // URLS AJAX
    Route::get('/getJerarquia2', 'SubhierarchyController@getJerarquia2')->name('getJerarquia2');
    Route::get('/getJerarquia3', 'SubsubhierarchyController@getJerarquia3')->name('getJerarquia3');
    Route::get('/getCad', 'WorkOrderController@getCad')->name('getCad');
    Route::get('/getCadByMaterial', 'WorkOrderController@getCadByMaterial')->name('getCadByMaterial');
    Route::get('/getCarton', 'WorkOrderController@getCarton')->name('getCarton');
    Route::get('/getDesignType', 'WorkOrderController@getDesignType')->name('getDesignType');
    Route::get('/getCartonColor', 'WorkOrderController@getCartonColor')->name('getCartonColor');
    Route::post('/postVerificacionFiltro', 'WorkOrderController@postVerificacionFiltro')->name('postVerificacionFiltro');
    Route::get('/getRecubrimientoInterno', 'WorkOrderController@getRecubrimientoInterno')->name('getRecubrimientoInterno');
    Route::get('/getRecubrimientoExterno', 'WorkOrderController@getRecubrimientoExterno')->name('getRecubrimientoExterno');
    Route::get('/getPlantaObjetivo', 'WorkOrderController@getPlantaObjetivo')->name('getPlantaObjetivo');
    Route::get('/getColorCarton', 'WorkOrderController@getColorCarton')->name('getColorCarton');
    Route::get('/getListaCarton', 'WorkOrderController@getListaCarton')->name('getListaCarton');
    Route::get('/getListaCartonEdit', 'WorkOrderController@getListaCartonEdit')->name('getListaCartonEdit');
    Route::get('/getUsersByArea', 'UserController@getUsersByArea')->name('getUsersByArea');
    Route::get('/getContactosCliente', 'ClientController@getContactosCliente')->name('getContactosCliente');
    Route::get('/getDatosContacto', 'ClientController@getDatosContacto')->name('getDatosContacto');
    Route::get('/getMuestra', 'MuestraController@getMuestra')->name('getMuestra');
    Route::get('/getMaquilaServicio', 'WorkOrderController@getMaquilaServicio')->name('getMaquilaServicio');
    Route::get('/getListaCartonOffset', 'WorkOrderController@getListaCartonOffset')->name('getListaCartonOffset');
    Route::get('/getCartonMuestra', 'MuestraController@getCartonMuestra')->name('getCartonMuestra');
    Route::get('/getInstalacionesCliente', 'ClientController@getInstalacionesCliente')->name('getInstalacionesCliente');
    Route::get('/getInformacionInstalacion', 'ClientController@getInformacionInstalacion')->name('getInformacionInstalacion');
    Route::get('/getDatosContactoInstalacion', 'ClientController@getDatosContactoInstalacion')->name('getDatosContactoInstalacion');
    Route::get('/getIndicacionesEspeciales', 'ClientController@getIndicacionesEspeciales')->name('getIndicacionesEspeciales');
    Route::get('/getTiposVendedores', 'UserController@getTiposVendedores')->name('getTiposVendedores');
    Route::get('/getSecuenciasOperacionales', 'WorkOrderController@getSecuenciasOperacionales')->name('getSecuenciasOperacionales');
    Route::get('/getSecuenciasOperacionalesOt', 'WorkOrderController@getSecuenciasOperacionalesOt')->name('getSecuenciasOperacionalesOt');
    Route::get('/getMatriz', 'WorkOrderController@getMatriz')->name('getMatriz');
    Route::get('/getMatrizData', 'WorkOrderController@getMatrizData')->name('getMatrizData');
    Route::get('/getOtData', 'WorkOrderController@getOtData')->name('getOtData');
    Route::get('/searchMatrizCad', 'WorkOrderController@searchMatrizCad')->name('searchMatrizCad');
    Route::get('/getSecuenciasOperacionalesPlanta', 'WorkOrderController@getSecuenciasOperacionalesPlanta')->name('getSecuenciasOperacionalesPlanta');
    Route::get('/chargeSelectSecOperacionalPlanta', 'WorkOrderController@chargeSelectSecOperacionalPlanta')->name('chargeSelectSecOperacionalPlanta');
    Route::get('/chargeSelectSecOperacionalPlantaAux1', 'WorkOrderController@chargeSelectSecOperacionalPlantaAux1')->name('chargeSelectSecOperacionalPlantaAux1');
    Route::get('/chargeSelectSecOperacionalPlantaAux2', 'WorkOrderController@chargeSelectSecOperacionalPlantaAux2')->name('chargeSelectSecOperacionalPlantaAux2');
    Route::get('/getInstalacionesClienteCotiza', 'ClientController@getInstalacionesClienteCotiza')->name('getInstalacionesClienteCotiza');

    Route::post('/validarExcel', 'WorkOrderController@validarExcel')->name('validarExcel');
    Route::post('/guardar-muestra-masiva', 'WorkOrderController@importarMuestrasDesdeExcel')->name('guardar-muestra-masiva');

    // Mantenedores
    /*CLIENTES*/
    Route::prefix('mantenedores/clients/')->name('mantenedores.clients.')->group(function () {
        Route::get('list', 'ClientController@index')->name('list');
        Route::get('create', 'ClientController@create')->name('create');
        Route::post('guardar', 'ClientController@store')->name('store');
        Route::get('editar/{id}', 'ClientController@edit')->name('edit');
        Route::put('actualizar/{id}', 'ClientController@update')->name('update');
        Route::put('activar/{id}', 'ClientController@active')->name('active');
        Route::put('inactivar/{id}', 'ClientController@inactive')->name('inactive');
        Route::get('store_installation', 'ClientController@store_installation')->name('store_installation');
        Route::get('edit_installation', 'ClientController@edit_installation')->name('edit_installation');
        Route::get('update_installation', 'ClientController@update_installation')->name('update_installation');
        Route::get('store_indicacion', 'ClientController@store_indicacion')->name('store_indicacion');
        Route::get('edit_indicacion', 'ClientController@edit_indicacion')->name('edit_indicacion');
        Route::get('update_indicacion', 'ClientController@update_indicacion')->name('update_indicacion');
        // Route::get('massive', 'ClientController@cargaClientsForm')->name('massive');
        // Route::post('uploading', 'ClientController@importClients')->name('uploading');
    });

    // Cambio contraseña de usuario

    Route::get('editarContraseña/{id}', 'UserController@editarContraseña')->name('editarContraseña');
    Route::put('actualizarContraseña/{id}', 'UserController@actualizarContraseña')->name('actualizarContraseña');
    Route::group(["middleware" => "role:Administrador,Super Administrador"], function () {
        // ----------
        /*USUARIOS*/
        Route::prefix('mantenedores/users/')->name('mantenedores.users.')->group(function () {
            Route::get('list', 'UserController@index')->name('list');
            Route::get('create', 'UserController@create')->name('create');
            Route::post('guardar', 'UserController@store')->name('store');
            Route::get('editar/{id}', 'UserController@edit')->name('edit');
            Route::put('actualizar/{id}', 'UserController@update')->name('update');
            Route::put('activar/{id}', 'UserController@active')->name('active');
            Route::put('inactivar/{id}', 'UserController@inactive')->name('inactive');
            Route::get('masive', 'UserController@cargaUsersForm')->name('masive');
            Route::post('uploading', 'UserController@importUsers')->name('uploading');
        });

        /*SECTORES*/
        Route::prefix('mantenedores/sectors/')->name('mantenedores.sectors.')->group(function () {
            Route::get('list', 'SectorController@index')->name('list');
            Route::get('create', 'SectorController@create')->name('create');
            Route::post('guardar', 'SectorController@store')->name('store');
            Route::get('editar/{id}', 'SectorController@edit')->name('edit');
            Route::put('actualizar/{id}', 'SectorController@update')->name('update');
            Route::put('activar/{id}', 'SectorController@active')->name('active');
            Route::put('inactivar/{id}', 'SectorController@inactive')->name('inactive');
        });

        /*HIERARCHY*/
        Route::prefix('mantenedores/hierarchies/')->name('mantenedores.hierarchies.')->group(function () {
            Route::get('list', 'HierarchyController@index')->name('list');
            Route::get('create', 'HierarchyController@create')->name('create');
            Route::post('guardar', 'HierarchyController@store')->name('store');
            Route::get('editar/{id}', 'HierarchyController@edit')->name('edit');
            Route::put('actualizar/{id}', 'HierarchyController@update')->name('update');
            Route::put('activar/{id}', 'HierarchyController@active')->name('active');
            Route::put('inactivar/{id}', 'HierarchyController@inactive')->name('inactive');
        });

        /*SUBHIERARCHY*/
        Route::prefix('mantenedores/subhierarchies/')->name('mantenedores.subhierarchies.')->group(function () {
            Route::get('list', 'SubhierarchyController@index')->name('list');
            Route::get('create', 'SubhierarchyController@create')->name('create');
            Route::post('guardar', 'SubhierarchyController@store')->name('store');
            Route::get('editar/{id}', 'SubhierarchyController@edit')->name('edit');
            Route::put('actualizar/{id}', 'SubhierarchyController@update')->name('update');
            Route::put('activar/{id}', 'SubhierarchyController@active')->name('active');
            Route::put('inactivar/{id}', 'SubhierarchyController@inactive')->name('inactive');
        });

        /*SUBSUBHIERARCHY (dont judge the naming)*/
        Route::prefix('mantenedores/subsubhierarchies/')->name('mantenedores.subsubhierarchies.')->group(function () {
            Route::get('list', 'SubsubhierarchyController@index')->name('list');
            Route::get('create', 'SubsubhierarchyController@create')->name('create');
            Route::post('guardar', 'SubsubhierarchyController@store')->name('store');
            Route::get('editar/{id}', 'SubsubhierarchyController@edit')->name('edit');
            Route::put('actualizar/{id}', 'SubsubhierarchyController@update')->name('update');
            Route::put('activar/{id}', 'SubsubhierarchyController@active')->name('active');
            Route::put('inactivar/{id}', 'SubsubhierarchyController@inactive')->name('inactive');
        });

        /*PRODUCT_TYPES*/
        Route::prefix('mantenedores/product-types/')->name('mantenedores.product-types.')->group(function () {
            Route::get('list', 'ProductTypeController@index')->name('list');
            Route::get('create', 'ProductTypeController@create')->name('create');
            Route::post('guardar', 'ProductTypeController@store')->name('store');
            Route::get('editar/{id}', 'ProductTypeController@edit')->name('edit');
            Route::put('actualizar/{id}', 'ProductTypeController@update')->name('update');
            Route::put('activar/{id}', 'ProductTypeController@active')->name('active');
            Route::put('inactivar/{id}', 'ProductTypeController@inactive')->name('inactive');
        });

        /*PALLET_TYPES*/
        Route::prefix('mantenedores/pallet-types/')->name('mantenedores.pallet-types.')->group(function () {
            Route::get('list', 'PalletTypeController@index')->name('list');
            Route::get('create', 'PalletTypeController@create')->name('create');
            Route::post('guardar', 'PalletTypeController@store')->name('store');
            Route::get('editar/{id}', 'PalletTypeController@edit')->name('edit');
            Route::put('actualizar/{id}', 'PalletTypeController@update')->name('update');
            Route::put('activar/{id}', 'PalletTypeController@active')->name('active');
            Route::put('inactivar/{id}', 'PalletTypeController@inactive')->name('inactive');
        });

        /*STYLES*/
        Route::prefix('mantenedores/styles/')->name('mantenedores.styles.')->group(function () {
            Route::get('list', 'StyleController@index')->name('list');
            Route::get('create', 'StyleController@create')->name('create');
            Route::post('guardar', 'StyleController@store')->name('store');
            Route::get('editar/{id}', 'StyleController@edit')->name('edit');
            Route::put('actualizar/{id}', 'StyleController@update')->name('update');
            Route::put('activar/{id}', 'StyleController@active')->name('active');
            Route::put('inactivar/{id}', 'StyleController@inactive')->name('inactive');
        });

        /*CARTONES*/
        Route::prefix('mantenedores/cartons/')->name('mantenedores.cartons.')->group(function () {
            Route::get('list', 'CartonController@index')->name('list');
            Route::get('create', 'CartonController@create')->name('create');
            Route::post('guardar', 'CartonController@store')->name('store');
            Route::get('editar/{id}', 'CartonController@edit')->name('edit');
            Route::put('actualizar/{id}', 'CartonController@update')->name('update');
            Route::put('activar/{id}', 'CartonController@active')->name('active');
            Route::put('inactivar/{id}', 'CartonController@inactive')->name('inactive');
        });

        /*COLORS*/
        Route::prefix('mantenedores/colors/')->name('mantenedores.colors.')->group(function () {
            Route::get('list', 'ColorController@index')->name('list');
            Route::get('create', 'ColorController@create')->name('create');
            Route::post('guardar', 'ColorController@store')->name('store');
            Route::get('editar/{id}', 'ColorController@edit')->name('edit');
            Route::put('actualizar/{id}', 'ColorController@update')->name('update');
            Route::put('activar/{id}', 'ColorController@active')->name('active');
            Route::put('inactivar/{id}', 'ColorController@inactive')->name('inactive');
        });

        /*SECUENCIAS OPERACIONALES*/
        Route::prefix('mantenedores/secuencias-operacionales/')->name('mantenedores.secuencias-operacionales.')->group(function () {
            Route::get('list', 'SecuenciaOperacionalController@index')->name('list');
            Route::get('create', 'SecuenciaOperacionalController@create')->name('create');
            Route::post('guardar', 'SecuenciaOperacionalController@store')->name('store');
            Route::get('editar/{id}', 'SecuenciaOperacionalController@edit')->name('edit');
            Route::put('actualizar/{id}', 'SecuenciaOperacionalController@update')->name('update');
            Route::put('activar/{id}', 'SecuenciaOperacionalController@active')->name('active');
            Route::put('inactivar/{id}', 'SecuenciaOperacionalController@inactive')->name('inactive');
        });

        /*ALMACENES*/
        Route::prefix('mantenedores/almacenes/')->name('mantenedores.almacenes.')->group(function () {
            Route::get('list', 'AlmacenController@index')->name('list');
            Route::get('create', 'AlmacenController@create')->name('create');
            Route::post('guardar', 'AlmacenController@store')->name('store');
            Route::get('editar/{id}', 'AlmacenController@edit')->name('edit');
            Route::put('actualizar/{id}', 'AlmacenController@update')->name('update');
            Route::put('activar/{id}', 'AlmacenController@active')->name('active');
            Route::put('inactivar/{id}', 'AlmacenController@inactive')->name('inactive');
        });

        /*CANTIDAD BASE*/
        // Route::prefix('mantenedores/cantidad-base/')->name('mantenedores.cantidad-base.')->group(function () {
        //     Route::get('list', 'CantidadBaseController@index')->name('list');
        //     Route::get('create', 'CantidadBaseController@create')->name('create');
        //     Route::post('guardar', 'CantidadBaseController@store')->name('store');
        //     Route::get('editar/{id}', 'CantidadBaseController@edit')->name('edit');
        //     Route::put('actualizar/{id}', 'CantidadBaseController@update')->name('update');
        //     Route::put('activar/{id}', 'CantidadBaseController@active')->name('active');
        //     Route::put('inactivar/{id}', 'CantidadBaseController@inactive')->name('inactive');
        // });

        /*TIPOS CINTAS*/
        Route::prefix('mantenedores/tipos-cintas/')->name('mantenedores.tipos-cintas.')->group(function () {
            Route::get('list', 'TipoCintaController@index')->name('list');
            Route::get('create', 'TipoCintaController@create')->name('create');
            Route::post('guardar', 'TipoCintaController@store')->name('store');
            Route::get('editar/{id}', 'TipoCintaController@edit')->name('edit');
            Route::put('actualizar/{id}', 'TipoCintaController@update')->name('update');
            Route::put('activar/{id}', 'TipoCintaController@active')->name('active');
            Route::put('inactivar/{id}', 'TipoCintaController@inactive')->name('inactive');
        });

        /*RECHAZO CONJUNTO*/
        Route::prefix('mantenedores/rechazo-conjunto/')->name('mantenedores.rechazo-conjunto.')->group(function () {
            Route::get('list', 'RechazoConjuntoController@index')->name('list');
            Route::get('create', 'RechazoConjuntoController@create')->name('create');
            Route::post('guardar', 'RechazoConjuntoController@store')->name('store');
            Route::get('editar/{id}', 'RechazoConjuntoController@edit')->name('edit');
            Route::put('actualizar/{id}', 'RechazoConjuntoController@update')->name('update');
            Route::put('activar/{id}', 'RechazoConjuntoController@active')->name('active');
            Route::put('inactivar/{id}', 'RechazoConjuntoController@inactive')->name('inactive');
        });

        /*GRUPO IMPUTACION MATERIALES*/
        Route::prefix('mantenedores/grupo-imputacion-material/')->name('mantenedores.grupo-imputacion-material.')->group(function () {
            Route::get('list', 'GrupoImputacionMaterialController@index')->name('list');
            Route::get('create', 'GrupoImputacionMaterialController@create')->name('create');
            Route::post('guardar', 'GrupoImputacionMaterialController@store')->name('store');
            Route::get('editar/{id}', 'GrupoImputacionMaterialController@edit')->name('edit');
            Route::put('actualizar/{id}', 'GrupoImputacionMaterialController@update')->name('update');
            Route::put('activar/{id}', 'GrupoImputacionMaterialController@active')->name('active');
            Route::put('inactivar/{id}', 'GrupoImputacionMaterialController@inactive')->name('inactive');
        });


        /*ORGANIZACION VENTA*/
        Route::prefix('mantenedores/organizacion-venta/')->name('mantenedores.organizacion-venta.')->group(function () {
            Route::get('list', 'OrganizacionVentaController@index')->name('list');
            Route::get('create', 'OrganizacionVentaController@create')->name('create');
            Route::post('guardar', 'OrganizacionVentaController@store')->name('store');
            Route::get('editar/{id}', 'OrganizacionVentaController@edit')->name('edit');
            Route::put('actualizar/{id}', 'OrganizacionVentaController@update')->name('update');
            Route::put('activar/{id}', 'OrganizacionVentaController@active')->name('active');
            Route::put('inactivar/{id}', 'OrganizacionVentaController@inactive')->name('inactive');
        });

        /*TIEMPO TRATAMIENTO*/
        Route::prefix('mantenedores/tiempo-tratamiento/')->name('mantenedores.tiempo-tratamiento.')->group(function () {
            Route::get('list', 'TiempoTratamientoController@index')->name('list');
            Route::get('create', 'TiempoTratamientoController@create')->name('create');
            Route::post('guardar', 'TiempoTratamientoController@store')->name('store');
            Route::get('editar/{id}', 'TiempoTratamientoController@edit')->name('edit');
            Route::put('actualizar/{id}', 'TiempoTratamientoController@update')->name('update');
            Route::put('activar/{id}', 'TiempoTratamientoController@active')->name('active');
            Route::put('inactivar/{id}', 'TiempoTratamientoController@inactive')->name('inactive');
        });

        /*GRUPO MATERIALES 1*/
        Route::prefix('mantenedores/grupo-materiales-1/')->name('mantenedores.grupo-materiales-1.')->group(function () {
            Route::get('list', 'GrupoMateriales1Controller@index')->name('list');
            Route::get('create', 'GrupoMateriales1Controller@create')->name('create');
            Route::post('guardar', 'GrupoMateriales1Controller@store')->name('store');
            Route::get('editar/{id}', 'GrupoMateriales1Controller@edit')->name('edit');
            Route::put('actualizar/{id}', 'GrupoMateriales1Controller@update')->name('update');
            Route::put('activar/{id}', 'GrupoMateriales1Controller@active')->name('active');
            Route::put('inactivar/{id}', 'GrupoMateriales1Controller@inactive')->name('inactive');
        });

        /*GRUPO MATERIALES 2*/
        Route::prefix('mantenedores/grupo-materiales-2/')->name('mantenedores.grupo-materiales-2.')->group(function () {
            Route::get('list', 'GrupoMateriales2Controller@index')->name('list');
            Route::get('create', 'GrupoMateriales2Controller@create')->name('create');
            Route::post('guardar', 'GrupoMateriales2Controller@store')->name('store');
            Route::get('editar/{id}', 'GrupoMateriales2Controller@edit')->name('edit');
            Route::put('actualizar/{id}', 'GrupoMateriales2Controller@update')->name('update');
            Route::put('activar/{id}', 'GrupoMateriales2Controller@active')->name('active');
            Route::put('inactivar/{id}', 'GrupoMateriales2Controller@inactive')->name('inactive');
        });

        // MATRICES
        Route::prefix('mantenedores/matrices')->name('mantenedores.matrices.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaMatricesForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importMatrices')->name('uploading');
            Route::get('descargar_excel_matrices', 'MantenedorController@descargar_excel_matrices')->name("descargar_excel_matrices");
        });

        // MATERIALES
        Route::prefix('mantenedores/materiales')->name('mantenedores.materiales.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaMaterialesForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importMateriales')->name('uploading');
            Route::get('descargar_excel_materiales', 'MantenedorController@descargar_excel_materiales')->name("descargar_excel_materiales");
        });

        /*GRUPO PLANTAS*/
        Route::prefix('mantenedores/grupo-plantas/')->name('mantenedores.grupo-plantas.')->group(function () {
            Route::get('list', 'GrupoPlantasController@index')->name('list');
            Route::get('create', 'GrupoPlantasController@create')->name('create');
            Route::post('guardar', 'GrupoPlantasController@store')->name('store');
            Route::get('editar/{id}', 'GrupoPlantasController@edit')->name('edit');
            Route::put('actualizar/{id}', 'GrupoPlantasController@update')->name('update');
            Route::put('activar/{id}', 'GrupoPlantasController@active')->name('active');
            Route::put('inactivar/{id}', 'GrupoPlantasController@inactive')->name('inactive');
        });


        /*MATERIALS*/
        // Route::prefix('mantenedores/materials/')->name('mantenedores.materials.')->group(function () {
        //     Route::get('list', 'MaterialController@index')->name('list');
        //     Route::get('create', 'MaterialController@create')->name('create');
        //     Route::post('guardar', 'MaterialController@store')->name('store');
        //     Route::get('editar/{id}', 'MaterialController@edit')->name('edit');
        //     Route::put('actualizar/{id}', 'MaterialController@update')->name('update');
        //     Route::put('activar/{id}', 'MaterialController@active')->name('active');
        //     Route::put('inactivar/{id}', 'MaterialController@inactive')->name('inactive');
        // });

        /*CANALS*/
        Route::prefix('mantenedores/canals/')->name('mantenedores.canals.')->group(function () {
            Route::get('list', 'CanalController@index')->name('list');
            Route::get('create', 'CanalController@create')->name('create');
            Route::post('guardar', 'CanalController@store')->name('store');
            Route::get('editar/{id}', 'CanalController@edit')->name('edit');
            Route::put('actualizar/{id}', 'CanalController@update')->name('update');
            Route::put('activar/{id}', 'CanalController@active')->name('active');
            Route::put('inactivar/{id}', 'CanalController@inactive')->name('inactive');
        });

        /*ADHESIVOS*/
        Route::prefix('mantenedores/adhesivos/')->name('mantenedores.adhesivos.')->group(function () {
            Route::get('list', 'AdhesivoController@index')->name('list');
            Route::get('create', 'AdhesivoController@create')->name('create');
            Route::post('guardar', 'AdhesivoController@store')->name('store');
            Route::get('editar/{id}', 'AdhesivoController@edit')->name('edit');
            Route::put('actualizar/{id}', 'AdhesivoController@update')->name('update');
            Route::put('activar/{id}', 'AdhesivoController@active')->name('active');
            Route::put('inactivar/{id}', 'AdhesivoController@inactive')->name('inactive');
        });

        // MANTENEDORES COTIZADOR

        // CARTONES CORRUGADOS
        Route::prefix('mantenedores/cotizador/cartons')->name('mantenedores.cotizador.cartons.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaCartonsForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importCartons')->name('uploading');
            Route::get('descargar_excel_cartones_corrugados', 'MantenedorController@descargar_excel_cartones_corrugados')->name("descargar_excel_cartones_corrugados");
        });

        // CARTONES ESQUINEROS
        Route::prefix('mantenedores/cotizador/cartones-esquineros')->name('mantenedores.cotizador.cartones-esquineros.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaCartonesEsquinerosForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importCartonesEsquineros')->name('uploading');
            Route::get('descargar_excel_cartones_esquineros', 'MantenedorController@descargar_excel_cartones_esquineros')->name("descargar_excel_cartones_esquineros");
        });

        // PAPELES
        Route::prefix('mantenedores/cotizador/papeles')->name('mantenedores.cotizador.papeles.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaPapelesForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importPapeles')->name('uploading');
            Route::get('descargar_excel_papeles', 'MantenedorController@descargar_excel_papeles')->name("descargar_excel_papeles");
        });

        // FLETES
        Route::prefix('mantenedores/cotizador/fletes')->name('mantenedores.cotizador.fletes.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaFletesForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importFletes')->name('uploading');
            Route::get('descargar_excel_fletes', 'MantenedorController@descargar_excel_fletes')->name("descargar_excel_fletes");
        });

        // MERMAS CORRUGADORAS
        Route::prefix('mantenedores/cotizador/mermas_corrugadoras')->name('mantenedores.cotizador.mermas-corrugadoras.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaMermasCorrugadorasForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importMermasCorrugadoras')->name('uploading');
            Route::get('descargar_excel_mermas_corrugadoras', 'MantenedorController@descargar_excel_mermas_corrugadoras')->name("descargar_excel_mermas_corrugadoras");
        });

        // MERMAS CONVERTIDORAS
        Route::prefix('mantenedores/cotizador/mermas_convertidoras')->name('mantenedores.cotizador.mermas-convertidoras.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaMermasConvertidorasForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importMermasConvertidoras')->name('uploading');
            Route::get('descargar_excel_mermas_convertidoras', 'MantenedorController@descargar_excel_mermas_convertidoras')->name("descargar_excel_mermas_convertidoras");
        });

        // DETALLE PALETIZADO
        Route::prefix('mantenedores/cotizador/paletizados')->name('mantenedores.cotizador.paletizados.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaPaletizadosForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importPaletizados')->name('uploading');
            Route::get('descargar_excel_paletizados', 'MantenedorController@descargar_excel_paletizados')->name("descargar_excel_paletizados");
        });

        // DETALLES INSUMOS
        Route::prefix('mantenedores/cotizador/insumos_paletizados')->name('mantenedores.cotizador.insumos-paletizados.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaInsumosPaletizadosForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importInsumosPaletizados')->name('uploading');
            Route::get('descargar_excel_insumos_paletizados', 'MantenedorController@descargar_excel_insumos_paletizados')->name("descargar_excel_insumos_paletizados");
        });

        // TARIFARIO DE MARGENES
        Route::prefix('mantenedores/cotizador/tarifarios')->name('mantenedores.cotizador.tarifarios.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaTarifariosForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importTarifarios')->name('uploading');
            Route::get('descargar_excel_tarifarios', 'MantenedorController@descargar_excel_tarifarios')->name("descargar_excel_tarifarios");
        });

        // CONSUMOS ADHESIVOS
        Route::prefix('mantenedores/cotizador/consumo_adhesivos')->name('mantenedores.cotizador.consumo-adhesivos.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaConsumoAdhesivoForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importConsumoAdhesivo')->name('uploading');
            Route::get('descargar_excel_consumo_adhesivos', 'MantenedorController@descargar_excel_consumo_adhesivos')->name("descargar_excel_consumo_adhesivos");
        });

        // CONSUMOS ADHESIVOS PEGADOS
        Route::prefix('mantenedores/cotizador/consumo_adhesivos_pegados')->name('mantenedores.cotizador.consumo-adhesivos-pegados.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaConsumoAdhesivoPegadoForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importConsumoAdhesivoPegado')->name('uploading');
            Route::get('descargar_excel_consumo_adhesivos_pegados', 'MantenedorController@descargar_excel_consumo_adhesivos_pegados')->name("descargar_excel_consumo_adhesivos_pegados");
        });

        // CONSUMOS Energia
        Route::prefix('mantenedores/cotizador/consumo_energia')->name('mantenedores.cotizador.consumo-energia.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaConsumoEnergiaForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importConsumoEnergia')->name('uploading');
            Route::get('descargar_excel_consumo_energia', 'MantenedorController@descargar_excel_consumo_energia')->name("descargar_excel_consumo_energia");
        });

        // FACTORES SEGURIDAD
        Route::prefix('mantenedores/cotizador/factores_seguridad')->name('mantenedores.cotizador.factores-seguridad.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaFactoresSeguridadForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importFactoresSeguridad')->name('uploading');
            Route::get('descargar_excel_factores_seguridad', 'MantenedorController@descargar_excel_factores_seguridad')->name("descargar_excel_factores_seguridad");
        });

        // FACTORES ONDA
        Route::prefix('mantenedores/cotizador/factores_onda')->name('mantenedores.cotizador.factores-onda.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaFactoresOndaForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importFactoresOnda')->name('uploading');
            Route::get('descargar_excel_factores_onda', 'MantenedorController@descargar_excel_factores_onda')->name("descargar_excel_factores_onda");
        });

        // FACTORES Desarrollo
        Route::prefix('mantenedores/cotizador/factores_desarrollo')->name('mantenedores.cotizador.factores-desarrollo.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaFactoresDesarrolloForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importFactoresDesarrollo')->name('uploading');
            Route::get('descargar_excel_factores_desarrollo', 'MantenedorController@descargar_excel_factores_desarrollo')->name("descargar_excel_factores_desarrollo");
        });

        // MAQUILAS
        Route::prefix('mantenedores/cotizador/maquilas')->name('mantenedores.cotizador.maquilas.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaMaquilasForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importMaquilas')->name('uploading');
            Route::get('descargar_excel_maquilas', 'MantenedorController@descargar_excel_maquilas')->name("descargar_excel_maquilas");
        });

        // ONDAS
        Route::prefix('mantenedores/cotizador/ondas')->name('mantenedores.cotizador.ondas.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaOndasForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importOndas')->name('uploading');
            Route::get('descargar_excel_ondas', 'MantenedorController@descargar_excel_ondas')->name("descargar_excel_ondas");
        });

        // PLANTAS
        Route::prefix('mantenedores/cotizador/plantas')->name('mantenedores.cotizador.plantas.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaPlantasForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importPlantas')->name('uploading');
            Route::get('descargar_excel_plantas', 'MantenedorController@descargar_excel_plantas')->name("descargar_excel_plantas");
        });

        // VARIABLES
        Route::prefix('mantenedores/cotizador/variables')->name('mantenedores.cotizador.variables.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaVariablesForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importVariables')->name('uploading');
            Route::get('descargar_excel_variables', 'MantenedorController@descargar_excel_variables')->name("descargar_excel_variables");
        });

        // MARGEN MINIMO
        Route::prefix('mantenedores/cotizador/margenes_minimos')->name('mantenedores.cotizador.margenes-minimos.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaMargenesMinimosForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importMargenesMinimos')->name('uploading');
            Route::get('descargar_excel_margenes', 'MantenedorController@descargar_excel_margenes')->name("descargar_excel_margenes");
        });

        //MATERIALS
        Route::prefix('mantenedores/materials/')->name('mantenedores.materials.')->group(function () {
            Route::get('list', 'MaterialController@index')->name('list');
            Route::get('create', 'MaterialController@create')->name('create');
            Route::post('guardar', 'MaterialController@store')->name('store');
            Route::get('editar/{id}', 'MaterialController@edit')->name('edit');
            Route::put('actualizar/{id}', 'MaterialController@update')->name('update');
            Route::put('activar/{id}', 'MaterialController@active')->name('active');
            Route::put('inactivar/{id}', 'MaterialController@inactive')->name('inactive');
        });

        //CEBES
        Route::prefix('mantenedores/cebes/')->name('mantenedores.cebes.')->group(function () {
            Route::get('list', 'CeBeController@index')->name('list');
            Route::get('create', 'CeBeController@create')->name('create');
            Route::post('guardar', 'CeBeController@store')->name('store');
            Route::get('editar/{id}', 'CeBeController@edit')->name('edit');
            Route::put('actualizar/{id}', 'CeBeController@update')->name('update');
            Route::put('activar/{id}', 'CeBeController@active')->name('active');
            Route::put('inactivar/{id}', 'CeBeController@inactive')->name('inactive');
        });

        /*CLASIFICACION CLIENTE*/
        Route::prefix('mantenedores/clasificaciones_clientes/')->name('mantenedores.clasificaciones_clientes.')->group(function () {
            Route::get('list', 'ClasificacionClienteController@index')->name('list');
            Route::get('create', 'ClasificacionClienteController@create')->name('create');
            Route::post('guardar', 'ClasificacionClienteController@store')->name('store');
            Route::get('editar/{id}', 'ClasificacionClienteController@edit')->name('edit');
            Route::put('actualizar/{id}', 'ClasificacionClienteController@update')->name('update');
            Route::put('activar/{id}', 'ClasificacionClienteController@active')->name('active');
            Route::put('inactivar/{id}', 'ClasificacionClienteController@inactive')->name('inactive');
        });

        // PORCENTAJE MARGEN MINIMO
        Route::prefix('mantenedores/cotizador/porcentajes_margenes_minimos')->name('mantenedores.cotizador.porcentajes-margenes-minimos.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaPorcentajesMargenesMinimosForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importPorcentajesMargenesMinimos')->name('uploading');
            Route::get('descargar_excel_porcentaje_margenes', 'MantenedorController@descargar_excel_porcentajes_margenes')->name("descargar_excel_porcentajes_margenes");
        });

        // MANO DE OBRA MANTENCION
        Route::prefix('mantenedores/cotizador/mano_obra_mantencion')->name('mantenedores.cotizador.mano-obra-mantencion.')->group(function () {
            Route::get('masive', 'MantenedorController@cargaManoObraMantencionForm')->name('masive');
            Route::post('uploading', 'MantenedorController@importManoObraMantencion')->name('uploading');
            Route::get('descargar_excel_mano_obra_mantencion', 'MantenedorController@descargar_excel_mano_obra_mantencion')->name("descargar_excel_mano_obra_mantencion");
        });
    });


    Route::get('/excel_muestras_pendientes', 'CartonController@excel_muestras_pendientes')->name("excel_muestras_pendientes");
    // test route

    Route::get('/test', 'UserController@test')->name('test');


    // RUTAS DE COTIZADOR ENVASES

    Route::prefix('cotizador/')->name('cotizador.')->group(function () {
        Route::get('crear_areahc', 'AreahcController@create')->name('crear_areahc');

        // Cotizaciones
        Route::get('crear', 'CotizacionController@create')->name('crear_cotizacion');
        Route::get('edit/{id}', 'CotizacionController@create')->name('editar_cotizacion');
        Route::get('index', 'CotizacionController@index')->name('index_cotizacion');
        Route::get('index_externo', 'CotizacionController@index_externo')->name('index_cotizacion_externo');
        Route::get('crear_externo', 'CotizacionController@create_externo')->name('crear_cotizacion_externo');
        Route::get('edit_externo/{id}', 'CotizacionController@create_externo')->name('editar_cotizacion_externo');
        Route::get('aprobar_externo/{id}', 'CotizacionController@aprobar_externo')->name('aprobar_cotizacion_externo');
        Route::get('edit_externo_aprobacion/{id}', 'CotizacionController@create_externo_aprobacion')->name('editar_cotizacion_externo_aprobacion');

        // Generar pdf cotizacion
        Route::get('generar_pdf', 'CotizacionController@generar_pdf')->name("generar_pdf");
        Route::post('enviar_pdf', 'CotizacionController@enviar_pdf')->name("enviar_pdf");
        // Generar Descargable detalle de costos
        Route::get('detalle_costos', 'CotizacionController@detalle_costos')->name("detalle_costos");
        Route::get('detalles_corrugados', 'CotizacionController@detalles_corrugados')->name("detalles_corrugados");
        Route::get('detalles_esquineros', 'CotizacionController@detalles_esquineros')->name("detalles_esquineros");

        Route::get('ayuda', 'CotizacionController@ayuda')->name('ayuda');

        Route::get('aprobaciones', 'CotizacionApprovalController@aprobaciones')->name('aprobaciones');
        Route::post('/gestionar-cotizacion/{id}', 'CotizacionApprovalController@gestionarAprobacionCotizacion')->name('gestionar-cotizacion');
        Route::post('/versionarCotizacion/{id}', 'CotizacionController@versionarCotizacion')->name('versionarCotizacion');
        Route::post('/duplicarCotizacion/{id}', 'CotizacionController@duplicarCotizacion')->name('duplicarCotizacion');
        Route::post('/retomarCotizacion/{id}', 'CotizacionController@retomarCotizacion')->name('retomarCotizacion');
        Route::post('/retomarCotizacionExterno/{id}', 'CotizacionController@retomarCotizacionExterno')->name('retomarCotizacionExterno');
        Route::post('/gestionar-cotizacion-externo/{id}', 'CotizacionApprovalController@gestionarAprobacionCotizacionExterno')->name('gestionar-cotizacion-externo');
        Route::post('/editarCotizacionExterno/{id}', 'CotizacionController@editarCotizacionExterno')->name('editarCotizacionExterno');
        // Cotizar ot
        Route::get('/cotizarOt/{id}', 'CotizacionController@cotizarOt')->name('cotizar-ot');
        // detalle a ot
        Route::post('/detalleAOt', 'WorkOrderController@detalleAOt')->name('detalleAOt');

        // URLS AJAX
        Route::post('/calcularAreaHC', 'AreahcController@store')->name('store');
        Route::post('/calcularDetalleCotizacion', 'CotizacionController@calcularDetalleCotizacion')->name('calcular_cotizacion');
        Route::post('/guardarDetalleCotizacion/{id}', 'DetalleCotizacionController@store')->name('guardar_detalle_cotizacion');
        Route::post('/editarDetalleCotizacion', 'DetalleCotizacionController@update')->name('editar_detalle_cotizacion');
        Route::post('/editarMargenCotizacion', 'DetalleCotizacionController@editarMargenCotizacion')->name('editar_margen_cotizacion');
        Route::post('/eliminarDetalleCotizacion', 'DetalleCotizacionController@delete')->name('eliminar_detalle_cotizacion');
        Route::post('/cargaMasivaDetalles', 'DetalleCotizacionController@cargaMasivaDetalles')->name('carga_masiva_detalles');
        Route::post('/cargaMateriales', 'CotizacionController@cargaMateriales')->name('carga_materiales');
        Route::post('/generarPrecotizacion/{id}/{client_id}', 'CotizacionController@generarPrecotizacion')->name('generar_precotizacion');
        Route::post('/solicitarAprobacion/{id}', 'CotizacionController@solicitarAprobacion')->name('solicitar_aprobacion');
        Route::post('/sincronizarDetalles/{id}', 'CotizacionController@sincronizarDetalles')->name('sincronizar_detalles');
        Route::post('/detalleCotizacionGanado', 'DetalleCotizacionController@detalleCotizacionGanado')->name('detalle_cotizacion_ganado');
        Route::post('/detalleCotizacionPerdido', 'DetalleCotizacionController@detalleCotizacionPerdido')->name('detalle_cotizacion_perdido');
        Route::post('/solicitarAprobacionExterno/{id}', 'CotizacionController@solicitarAprobacionExterno')->name('solicitar_aprobacion_externo');
        Route::post('/generarPrecotizacionExterno/{id}/{client_id}', 'CotizacionController@generarPrecotizacionExterno')->name('generar_precotizacion_externo');


        Route::get('/getServiciosMaquila', 'DetalleCotizacionController@getServiciosMaquila')->name('getServiciosMaquila');
        Route::post('/guardarMultiplesOt', 'DetalleCotizacionController@guardarMultiplesOt')->name('guardarMultiplesOt');
        Route::post('/obtieneDatos', 'DetalleCotizacionController@obtieneDatos')->name('obtieneDatos');
        Route::post('/cartonAltaGrafica', 'DetalleCotizacionController@cartonAltaGrafica')->name('cartonAltaGrafica');
        Route::post('/cartonGenerico', 'DetalleCotizacionController@cartonGenerico')->name('cartonGenerico');



        Route::get('/getJerarquia2AreaHC', 'SubhierarchyController@getJerarquia2AreaHC')->name('getJerarquia2AreaHC');
        Route::get('/getJerarquia3ConRubro', 'SubsubhierarchyController@getJerarquia3ConRubro')->name('getJerarquia3ConRubro');
        // obtener rubro segun jerarquia 3
        Route::get('/getRubro', 'SubsubhierarchyController@getRubro')->name('getRubro');
        Route::post('/solicitar_aprobacion_nuevo/{id}', 'CotizacionController@solicitarAprobacionNew')->name('solicitar_aprobacion_nuevo');
    });
});
