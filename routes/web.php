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

Route::group([
    'namespace'=>'Frontend',
    'as' => 'frontend.',
    'middleware' => 'language',
], function () {
    Route::get('/', 'MovieController@index')->name('home');
    Route::get('/filter', 'MovieController@filter')->name('filter');
    Route::get('/catalog/{key}/{slug}', 'MovieController@listMovie')->name('catalog');
    Route::get('/movie/{movie}/{sever?}/{prioritize?}/{video?}', 'MovieController@watchMovie')->name('movie');
    Route::get('/search', 'MovieController@search')->name('search');
    Route::get('/search/{slug}', 'MovieController@searchTag')->name('searchTag');
    Route::get('language/{language}', 'HomeController@language')->name('language');
});
