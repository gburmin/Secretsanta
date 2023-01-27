<?php

use App\Http\Controllers\Auth\RegisterController as UserRegisterController;
use App\Http\Controllers\Auth\LoginController as UserLoginController;
use App\Http\Controllers\Auth\RestoreController as UserRestoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CardController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('cors')->post('/user/register', [UserRegisterController::class, 'register']);
Route::middleware('cors')->post('/user/login', [UserLoginController::class, 'login']);
Route::middleware('cors')->post('/user/restore', [UserRestoreController::class, 'restore']);


Route::middleware('cors')->post('/box/create', [BoxController::class, 'create']);
Route::middleware('cors')->patch('/box/update/{box}', [BoxController::class, 'update']);
Route::middleware('cors')->delete('/box/delete/{box}', [BoxController::class, 'delete']);
Route::middleware('cors')->post('/box/sendInvites', [BoxController::class, 'sendInvites']);
Route::middleware('cors')->match(['get', 'post'], '/box/join', [BoxController::class, 'join']);
Route::middleware('cors')->post('/box/get', [BoxController::class, 'getBoxes']);
Route::middleware('cors')->post('/box/draw', [BoxController::class, 'draw']);
Route::middleware('cors')->post('/box/reverseDraw', [BoxController::class, 'reverseDraw']);
Route::middleware('cors')->post('/box/info', [BoxController::class, 'info']);
Route::middleware('cors')->post('/box/othersPublicBoxes', [BoxController::class, 'othersPublicBoxes']);


Route::middleware('cors')->post('/chat/send', [ChatController::class, 'sendMessage']);
Route::middleware('cors')->post('/chat/get', [ChatController::class, 'getAllMessages']);

Route::middleware('cors')->patch('/user/update/{user}', [ProfileController::class, 'update']);
Route::middleware('cors')->delete('user/delete/{user}', [ProfileController::class, 'delete']);

Route::middleware('cors')->patch('/card/update', [CardController::class, 'update']);
