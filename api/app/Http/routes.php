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

Route::get('/', function () {
    return view('welcome');
});

/*************** ROTAS ABERTAS ********************/
//Registra um novo usuário
Route::post('register', 'UserController@postRegister');
// Autentica um usuário
Route::post('authenticate', 'JwtAuthenticateController@authenticate');
// Retorna os dados do menu através do QRCode
Route::get('menu/qrcode/{code}', 'MenuController@getQRCode');

/**************** ROTAS ADMINISTRATIVAS ******************/
Route::group(['prefix' => 'admin', 'middleware' => ['ability:admin,*']], function()
{
	// Cria uma nova função (role)
	Route::post('role', 'JwtAuthenticateController@createRole');
	// Cria uma nova permissão
	Route::post('permission', 'JwtAuthenticateController@createPermission');
	// Designa uma função a um usuário
	Route::post('assign-role', 'JwtAuthenticateController@assignRole');
	// Designa uma permissão a uma função (role)
	Route::post('attach-permission', 'JwtAuthenticateController@attachPermission');
	// Verifica as permissões de um usuário
	Route::post('check', 'JwtAuthenticateController@checkRoles');
	// Lista os usuários do sistema
    Route::get('users', 'JwtAuthenticateController@index');
	
});

/****************** ROTAS AUTENTICADAS *****************/
Route::group(['prefix' => 'api', 'middleware' => ['ability:owner|admin|venue-owner,*']], function()
{

	Route::resource('venues', 'VenueController');
	Route::resource('venues.menus', 'MenuController');
	Route::resource('venues.menus.sections', 'SectionController');
	Route::resource('venues.menus.sections.items', 'ItemController');


	//Inclui um novo estabelecimento
	//Route::post('venues', 'VenueController@postCreate');

	//Inclui um novo menu no estabelecimento
	//Route::post('venue/{venue}/menus', 'MenuController@postCreate');	

	//Inclui uma nova seção no menu
	//Route::post('venue/{venue}/menu/{menu}', 'SectionController@postCreate');	

	//Inclui um novo ítem na seção
	//Route::post('venue/{venue}/menu/{menu}/section/{section}', 'ItemController@postCreate');		
	


	// Lista os estabelecimentos
	//Route::get('venues', 'VenueController@index');
	// Retorna os dados de um estabelecimento
	//Route::get('venue/{id}', 'VenueController@getOne');
	// Retorna os menus
	//Route::get('menus', 'MenuController@index');
	// Retorna os dados de um menu
	//Route::get('menu/{id}', 'MenuController@getOne');
});	




