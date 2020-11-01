<?php

use Illuminate\Support\Facades\Route;

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
    return view('index');
})->name('home');

Route::view('/login','users.login')->name('login');
Route::post('/logins','LoginController@authenticate')->name('logins');

Route::get('/logout', 'LoginController@logout')->name('logout');

Route::view('/register','users.register')->name('register');
Route::post('/registers','LoginController@register')->name('registers');

Route::view('/forgotPassword', 'users.forgotPassword')->name('forgotPassword');
Route::post('/forgotPasswords','LoginController@forgot')->name('forgotPasswords');

Route::view('/dashboard/{id}','users.dashboard')->name('dashboard');
