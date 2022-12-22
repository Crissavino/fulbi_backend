<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
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

//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/', [HomeController::class, 'changeLang'])->name('changeLang');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::post('/home', [HomeController::class, 'changeLang'])->name('changeLangHome');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::post('/terms', [HomeController::class, 'changeLangTerms'])->name('changeLangTerms');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::post('/privacy', [HomeController::class, 'changeLangPrivacy'])->name('changeLangPrivacy');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::group(['middleware' => 'auth'], function () {
	Route::get('table-list', function () {
		return view('pages.table_list');
	})->name('table');

	Route::get('typography', function () {
		return view('pages.typography');
	})->name('typography');

	Route::get('icons', function () {
		return view('pages.icons');
	})->name('icons');

	Route::get('map', function () {
		return view('pages.map');
	})->name('map');

	Route::get('notifications', function () {
		return view('pages.notifications');
	})->name('notifications');

	Route::get('rtl-support', function () {
		return view('pages.language');
	})->name('language');

	Route::get('upgrade', function () {
		return view('pages.upgrade');
	})->name('upgrade');
});

Route::group(['middleware' => 'auth'], function () {
	Route::resource('user', 'UserController', ['except' => ['show']]);
    Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);

    Route::get('matches', ['as' => 'matches.all', 'uses' => 'MatchController@showAll']);
    Route::get('matches/create', ['as' => 'matches.add', 'uses' => 'MatchController@add']);
    Route::post('matches/add', ['as' => 'matches.store', 'uses' => 'MatchController@store']);
    Route::get('matches/edit/{id}', ['as' => 'matches.edit', 'uses' => 'MatchController@edit']);
    Route::post('matches/edit/{id}', ['as' => 'matches.update', 'uses' => 'MatchController@update']);
    Route::delete('matches/delete/{id}', ['as' => 'matches.delete', 'uses' => 'MatchController@delete']);
    Route::get('match/chat/{id}', ['as' => 'matches.chat', 'uses' => 'MatchController@chat']);
    Route::get('match/enrolled/{id}', ['as' => 'matches.enrolled', 'uses' => 'MatchController@enrolled']);

    Route::get('players', ['as' => 'players.all', 'uses' => 'PlayerController@showAll']);
    Route::get('player/edit/{id}', ['as' => 'players.edit', 'uses' => 'PlayerController@edit']);
    Route::post('player/edit/{id}', ['as' => 'players.update', 'uses' => 'PlayerController@update']);
    Route::get('all-match-players', ['as' => 'players.allMatchPlayers', 'uses' => 'PlayerController@showAllMatchPlayers']);

    Route::get('organization', ['as' => 'organization.all', 'uses' => 'OrganizationController@showAll']);

});

Route::get('/user/recover-password/{encryptedId}',[AuthController::class, 'showRecoverPassword'])->name('recover-password');
Route::post('/user/recover-password/{encryptedId}',[AuthController::class, 'recoverPassword'])->name('recover-password-post');


