<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

Route::get('/user', function()
{
	return View::make('users.index');
});

Route::get('/address/index', function()
{
	return Redirect::action('AddressController@index');
});

Route::resource('/address/index', 'AddressController');
Route::resource('/sales/store', 'SalesController');



Route::post('/sales/addressValidation', function()
{
	$addressMessage = App::make('SalesController')->addressValidation();
	//header("Content-Type: application/json", true);
	return json_encode($addressMessage);
	//return Redirect::action('DemoController@store');
});

/*
Route::get( '/demo/store', array(
    'as' => 'demo.validateAddress',
    'uses' => 'DemoController@validateAddress'
) );
Route::post( '/demo/store', array(
    'as' => 'demo.validateAddress',
    'uses' => 'DemoController@validateAddress'
) );
*/
Route::resource('', 'SalesController');


