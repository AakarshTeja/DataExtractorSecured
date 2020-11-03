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

Route::match(['get','post'],'/forgotPassword', function(){
    return view('users.forgotPassword');
})->name('forgotPassword');
Route::post('/forgotPasswords','LoginController@forgotPassword')->name('forgotPasswords');

Route::match(['get', 'post'],'/resetPassword',function(){
    $request=request();
        if(!$request->hasValidSignature()){
            abort(403);
        }
    return view('users.resetPassword');
})->name('resetPassword')->middleware('signed');
Route::post('/resetPasswords','LoginController@resetPassword')->name('resetPasswords');


Route::view('/dashboard/{id}','users.dashboard')->name('dashboard');

Route::match(['get','post'],'/support', function(){
    return view('support');
})->name('support');

Route::post('/extracts','ExtractController@extract')->name('extracts');