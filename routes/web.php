<?php

use App\Http\Controllers\StoresController;

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

// トップページ
Route::get('/', 'HomeController@index');

// ユーザ新規登録
Route::get('signup', 'Auth\RegisterController@showRegistrationForm')->name('signup');
Route::post('signup', 'Auth\RegisterController@register')->name('signup.post');

//ログイン
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login.post');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');


// ログイン後
Route::group(['middleware' => 'auth'], function () {
  Route::prefix('stores')->group(function () {
  Route::get('create', 'StoresController@create')->name('store.create');
  Route::post('', 'StoresController@store')->name('store.store');
  Route::get('{id}/edit', 'StoresController@edit')->name('store.edit');
  Route::put('{id}', 'StoresController@update')->name('store.update');
  Route::delete('{id}', 'StoresController@destroy')->name('store.delete');
  Route::delete('delete/all', 'StoresController@bulkDelete')->name('stores.bulkDelete');
  });

  // マイページ
  Route::prefix('users')->group(function () {
    Route::get('{id}', 'UsersController@show')->name('user.show');
  });
  Route::delete('{id}', 'UsersController@destroy')->name('user.delete');

});

// 店舗
Route::get('/stores', 'StoresController@index')->name('stores.index');
Route::get('/stores/{id}', 'StoresController@show')->name('stores.show');
