<?php

//use Illuminate\Http\Request;

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
Route::options('user', 'UsersApiController@getOptions', ['middleware'=>'cors']);
Route::options('user/{id}', 'UsersApiController@getOptions', ['middleware'=>'cors']);
Route::resource('user', 'UsersApiController', ['middleware'=>'cors']);

