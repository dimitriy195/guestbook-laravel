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

Auth::routes();

Route::get('/', 'GuestbookController@index');
Route::post('/guestbook/store', 'GuestbookController@store')->middleware('can:create,' . \App\GuestbookMessage::class);
Route::post('/guestbook/update/{message}', 'GuestbookController@update')->middleware('can:update,message');
Route::post('/guestbook/validate', 'GuestbookController@validateData')->middleware('auth');
