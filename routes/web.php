<?php

use \Illuminate\Support\Facades\Route;

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


Route::get('/', function () {
    return view('welcome');
});
Route::group(['middleware' => 'cookie'], function () {
    Route::post('/login', 'UserController@login');
    Route::group(['middleware' => 'loginCheck'],function (){
        Route::get('/user/info', 'UserController@getUserInfo');
        Route::post('/user/avatar', 'UserController@saveAvatar');

        Route::put('/document', 'DocumentController@upload');
        Route::get('/document/{id}', 'DocumentController@documentInfo')->where(["id"=>'[0-9]+']);

        Route::get('/buy/{id}', 'DocumentController@buyDocument')->where(["id"=>'[0-9]+']);
    });
});
