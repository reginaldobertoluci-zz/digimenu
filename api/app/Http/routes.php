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


Route::group(['prefix' => 'admin', 'middleware' => ['ability:admin,create-roles']], function()
{
	// Route to create a new role
	Route::post('role', 'JwtAuthenticateController@createRole');
	
});

Route::group(['prefix' => 'admin', 'middleware' => ['ability:admin,create-permissions']], function()
{
	// Route to create a new permission
	Route::post('permission', 'JwtAuthenticateController@createPermission');
});

Route::group(['prefix' => 'admin', 'middleware' => ['ability:admin,assign-roles']], function()
{
	// Route to assign role to user
	Route::post('assign-role', 'JwtAuthenticateController@assignRole');
});	

Route::group(['prefix' => 'admin', 'middleware' => ['ability:admin,attach-permissions']], function(){
	// Route to attache permission to a role
	Route::post('attach-permission', 'JwtAuthenticateController@attachPermission');
});	

Route::group(['prefix' => 'admin', 'middleware' => ['ability:admin,check-permissions']], function(){

	// Route to check user permissions
	Route::post('check', 'JwtAuthenticateController@checkRoles');
});	

// API route group that we need to protect
Route::group(['prefix' => 'api', 'middleware' => ['ability:admin,create-users']], function()
{
    // Protected route
    Route::get('users', 'JwtAuthenticateController@index');
});

// Authentication route
Route::post('authenticate', 'JwtAuthenticateController@authenticate');

Route::get('venues', 'VenueController@index');
Route::get('venue/{id}', 'VenueController@getOne');
Route::get('menus', 'MenuController@index');
Route::get('menu/{id}', 'MenuController@getOne');

