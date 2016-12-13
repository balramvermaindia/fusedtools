<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');
Route::get('test', 'TestController@index');

Route::auth();

Route::get('/home', 'HomeController@index');
Route::get('/test', 'TestController@getAllTags');
Route::get('/refresh_token','RefreshTokensController@refreshISTokens');

Route::group([ 'prefix' => 'manage-accounts', 'middleware' => ['auth']], function () {
	Route::get('/', 'ManageAccountsController@listUsersAccount');
	Route::get('/add', 'ManageAccountsController@addUsersAccount');
	Route::get('/save', 'ManageAccountsController@saveUsersAccount');
	Route::post('/change_status', 'ManageAccountsController@changeStatusOfAccount');
	Route::post('/delete', 'ManageAccountsController@deleteAccount');
});

Route::group([ 'middleware' => ['auth']], function () {
	Route::get('import-step1/{parm?}','ImportCSVController@importStep1');
	Route::any('import-step2','ImportCSVController@importStep2');
	Route::any('import-step3','ImportCSVController@importStep3');
	Route::any('import-step4','ImportCSVController@importStep4');
	Route::any('import-step5','ImportCSVController@importStep5');
});

Route::get('curl_import','InfusionSoftController@index');
