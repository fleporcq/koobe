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
Route::post('/books', 'BookController@get');
Route::get('/books/upload', 'BookController@upload');
Route::any('/books/flow', 'BookController@flow');
Route::get('/covers/{slug}.jpg', 'BookController@cover');
Route::get('/epubs/{slug}.epub', 'BookController@download');
Route::get('/auth/login', 'Auth\AuthController@login');
Route::post('/auth/login', 'Auth\AuthController@authenticate');
Route::get('/auth/logout', 'Auth\AuthController@logout');
Route::get('/notifications', 'NotificationController@index');
Route::get('/notifications/all', 'NotificationController@all');
Route::get('/notification/delete', 'NotificationController@delete');

