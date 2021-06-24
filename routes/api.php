<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/existEmail',[AuthController::class, 'existEmail']);
Route::post('/logout',[AuthController::class, 'logout']);
Route::post('/complete-user-profile',[AuthController::class, 'completeUserProfile'])->middleware('auth:sanctum');
//Auth

//Match
Route::get('/match/{id}',[MatchController::class, 'getMatch'])->middleware('auth:sanctum');
Route::post('/match/create',[MatchController::class, 'store'])->middleware('auth:sanctum');
Route::post('/match/edit',[MatchController::class, 'edit'])->middleware('auth:sanctum');
Route::post('/match/delete',[MatchController::class, 'deleteMatch'])->middleware('auth:sanctum');
Route::post('/get-matches-offers',[MatchController::class, 'getMatchesOffers'])->middleware('auth:sanctum');
Route::get('/matches/get-my-matches',[MatchController::class, 'getMyMatches'])->middleware('auth:sanctum');
Route::post('/join-match',[MatchController::class, 'joinMatch'])->middleware('auth:sanctum');
Route::post('/leave-match',[MatchController::class, 'leaveMatch'])->middleware('auth:sanctum');
Route::get('/get-my-created-matches',[MatchController::class, 'getMyCreatedMatches'])->middleware('auth:sanctum');
Route::post('/send-invitation-to-user',[MatchController::class, 'sendInvitationToUser'])->middleware('auth:sanctum');
//Match

// Chat
Route::post('/chat/send-message',[ChatController::class, 'sendMessage'])->middleware('auth:sanctum');
Route::post('/chat/my-messages',[ChatController::class, 'myMessages'])->middleware('auth:sanctum');
// Chat

//User
Route::post('/edit-user-positions',[UserController::class, 'editUserPositions'])->middleware('auth:sanctum');
Route::post('/edit-user-location',[UserController::class, 'editUserLocation'])->middleware('auth:sanctum');
Route::post('/get-users-offers',[UserController::class, 'getUserOffers'])->middleware('auth:sanctum');
Route::post('/get-user-data',[UserController::class, 'getUserData'])->middleware('auth:sanctum');
Route::post('/user/change-nickname',[UserController::class, 'changeNickname'])->middleware('auth:sanctum');
Route::post('/user/change-password',[UserController::class, 'changePassword'])->middleware('auth:sanctum');
Route::post('/user/update-profile-picture',[UserController::class, 'updateProfileImage']);
//Route::post('/user/update-profile-picture',[UserController::class, 'updateProfileImage'])->middleware('authToken');
//User

