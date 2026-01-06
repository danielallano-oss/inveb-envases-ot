<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//Route::group(['prefix' => 'auth'], function () {
    //APP MOBILE:
  //  Route::post('login', 'AuthController@login'); //LOGIN
    // Route::post('register-app', 'AuthController@registerapp'); //Register
    /*tienen que tener el Authorization*/
   // Route::group(['middleware' => 'auth:api'], function () {
        //LOGIN:------------------------------------------
     //   Route::get('logout', 'AuthController@logout');
        // Route::get('user', 'AuthController@user');
        //PROSPECTOS Y GESTIONES: ----------------------------------------------
      //  Route::prefix('mobile')->group(function () {
            /*Extraer datos: */
        //    Route::get('listar-ordenes-ot', 'ApiMobileController@getOrdenesOt')->name('getOrdenesOt');
        //    Route::post('obtener-detalles-ot', 'ApiMobileController@getDetailsOt')->name('getDetailsOt');
        //    Route::post('obtener-historico-ot', 'ApiMobileController@getHistoryOt')->name('getHistoryOt');
            /*Lista de materiales por cliente para SIAPAC*/
        //    Route::post('listar-materiales-cliente', 'ApiMobileController@postMaterialesCliente')->name('postMaterialesCliente');
        //    Route::post('listar-materiales-jerarquia', 'ApiMobileController@postMaterialesJerarquia')->name('postMaterialesJerarquia');
            /*Eenviar datos al servidor:*/
        //    Route::get('listar-jerarquias', 'ApiMobileController@getJerarquias')->name('getJerarquias');
            /*Eenviar datos al servidor:*/
        //    Route::post('guardar-gestion-ot', 'ApiMobileController@saveGestionOt')->name('saveGestionOt');
        //    Route::post('guardar-respuesta', 'ApiMobileController@saveAnswerOt')->name('saveAnswerOt');
        //    Route::post('actualizar-token-notificacion-vendedor', 'ApiMobileController@updateTokenNotificationSeller')->name('updateTokenNotificationSeller');
        //});
    //});
//});
