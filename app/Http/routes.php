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
Route::get('/books', 'BookController@get');
Route::get('/books/upload', 'BookController@upload');
Route::any('/books/flow', 'BookController@flow');
Route::get('/covers/{slug}.jpg', 'BookController@cover');
Route::get('/auth/login', 'Auth\AuthController@login');
Route::post('/auth/login', 'Auth\AuthController@authenticate');
Route::get('/auth/logout', 'Auth\AuthController@logout');

