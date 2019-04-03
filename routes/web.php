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

Route::get('/upload/new', 'DocumentController@newUpload');
Route::get('/upload/sort', 'DocumentController@sortUpload');

Route::group(['middleware' => 'cookie'], function () {
    Route::post('/login', 'UserController@login');
    Route::group(['middleware' => 'loginCheck'],function (){ //登录之后允许操作

        Route::get('/user/info', 'UserController@getUserInfo');
        Route::post('/user/avatar', 'UserController@saveAvatar');
        Route::post('/user/nickname', 'UserController@changeNickname');

        Route::get('/user/upload', 'UserController@uploadList');
        Route::get('/user/download', 'UserController@downloadList');
        Route::get('/user/collection', 'UserController@collectionList');

        Route::put('/collection/{id}', 'UserController@addCollection')->where(["id"=>'[0-9]+']);
        Route::delete('/collection/{id}', 'UserController@delCollection')->where(["id"=>'[0-9]+']);

        Route::put('/document', 'DocumentController@upload');
        Route::group(['middleware' => 'ownership'], function () { // 验证所有权
            Route::post('/document/{id}', 'DocumentController@updateDocumentFile');
            Route::post('/document/info/{id}', 'DocumentController@updateDocumentInfo')->where(["id"=>'[0-9]+'])->name('updateDocumentInfo');
            Route::get('/document/info/{id}', 'DocumentController@documentInfo')->where(["id"=>'[0-9]+']);
        });

        Route::get('/buy/{id}', 'DocumentController@buyDocument')->where(["id"=>'[0-9]+']);
        Route::get('/download/{id}', 'DocumentController@downloadDocument')->where(["id"=>'[0-9]+'])->name('download');
    });
});

