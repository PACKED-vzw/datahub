<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/* Route::get('/', function () {
    return view('welcome');
}); */

Route::post('/record', 'Record@index');
Route::get('/record/{uuid}', 'Record@record');
Route::get('/search/{facet}/{term}', 'Record@collection');

Route::get('/collection', 'Collection@index');
Route::post('/collection', 'Collection@postCollection');
Route::get('/collection/{id}', 'Collection@getCollection');
Route::delete('/collection/{id}', 'Collection@deleteCollection');
Route::put('/collection/{id}', 'Collection@updateCollection');


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
