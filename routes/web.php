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

Route::get('/upload/new/{page}', 'DocumentController@newUpload')->where(["page"=>'[0-9]+']);
Route::get('/upload/sort/{page}', 'DocumentController@sortUpload')->where(["page"=>'[0-9]+']);
Route::get('/document/search/{page}', 'DocumentController@search')->where(["page"=>'[0-9]+']);

<<<<<<< HEAD
Route::get('/document/view/{id}/{page}', 'DocumentController@getJpg')
    ->where(["id"=>'[0-9]+'])->where(["page"=>'[0-9]+']);

Route::get('/swf', 'DocumentController@swf');

=======
>>>>>>> ac160807170520091ad647049f8e571ed0233018
Route::group(['middleware' => 'cookie'], function () {
    Route::post('/login', 'UserController@login');
    Route::group(['middleware' => 'loginCheck'],function (){ //登录之后允许操作

        Route::get('/user/info', 'UserController@getUserInfo');
        Route::post('/user/avatar', 'UserController@saveAvatar');
        Route::post('/user/nickname', 'UserController@changeNickname');

        Route::get('/user/upload/{page}', 'UserController@uploadList')->where(["page"=>'[0-9]+']);
        Route::get('/user/download/{page}', 'UserController@downloadList')->where(["page"=>'[0-9]+']);
        Route::get('/user/collection/{page}', 'UserController@collectionList')->where(["page"=>'[0-9]+']);

        Route::put('/collection/{id}', 'UserController@addCollection')->where(["id"=>'[0-9]+']);
        Route::delete('/collection/{id}', 'UserController@delCollection')->where(["id"=>'[0-9]+']);

        Route::put('/document', 'DocumentController@upload');
        Route::get('/document/info/{id}', 'DocumentController@documentInfo')->where(["id"=>'[0-9]+']);
        Route::group(['middleware' => 'ownership'], function () { // 验证所有权
            Route::post('/document/{id}', 'DocumentController@updateDocumentFile');
            Route::post('/document/info/{id}', 'DocumentController@updateDocumentInfo')->where(["id"=>'[0-9]+'])->name('updateDocumentInfo');
            Route::delete('/document/{id}', 'DocumentController@delDocument')->where(["id"=>'[0-9]+']);
        });

        Route::get('/buy/{id}', 'DocumentController@buyDocument')->where(["id"=>'[0-9]+']);
        Route::get('/download/{id}', 'DocumentController@downloadDocument')->where(["id"=>'[0-9]+'])->name('download');
    });
});
<<<<<<< HEAD
=======

>>>>>>> ac160807170520091ad647049f8e571ed0233018
