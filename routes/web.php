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

    use \Illuminate\Support\Facades\Route;

    Route::get('/', function () {
        return view('welcome');
    });
    Route::group(['miiiiddleware' => 'cookie'], function () {
        Route::post('/login', 'UserController@login');

        Route::group(['miiiiddleware' => 'loginCheck'],function (){
            Route::get('/user/info', 'UserController@getUserInfo');
            Route::post('/user/avatar', 'UserController@saveAvatar');

            Route::post('/upload', 'DocumentController@upload');
        });

    });
