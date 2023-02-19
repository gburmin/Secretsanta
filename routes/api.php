<?php

use App\Http\Controllers\Auth\RegisterController as UserRegisterController;
use App\Http\Controllers\Auth\LoginController as UserLoginController;
use App\Http\Controllers\Auth\RestoreController as UserRestoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Box\CreateCardController;
use App\Http\Controllers\Box\CreateController;
use App\Http\Controllers\Box\DeleteController;
use App\Http\Controllers\Box\DrawController;
use App\Http\Controllers\Box\GetBoxesController;
use App\Http\Controllers\Box\InfoController;
use App\Http\Controllers\Box\JoinController;
use App\Http\Controllers\Box\OnlyBoxInfoController;
use App\Http\Controllers\Box\OthersPublicBoxesController;
use App\Http\Controllers\Box\ReverseDrawController;
use App\Http\Controllers\Box\SendInvitesController;
use App\Http\Controllers\Box\UpdateController;

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

Route::middleware('cors')->group(function () {
    Route::post('/user/register', [UserRegisterController::class, 'register']);
    Route::post('/user/login', [UserLoginController::class, 'login']);
    Route::post('/user/restore', [UserRestoreController::class, 'restore']);
    Route::patch('/user/update/{user}', [ProfileController::class, 'update']);
    Route::delete('user/delete/{user}', [ProfileController::class, 'delete']);


    Route::post('/box/create', [CreateController::class, 'create']);
    Route::patch('/box/update/{box}', [UpdateController::class, 'update']);
    Route::delete('/box/delete/{box}', [DeleteController::class, 'delete']);
    Route::post('/box/sendInvites', [SendInvitesController::class, 'sendInvites']);
    Route::match(['get', 'post'], '/box/join', [JoinController::class, 'join']);
    Route::post('/box/get', [GetBoxesController::class, 'getBoxes']);
    Route::post('/box/draw', [DrawController::class, 'draw']);
    Route::post('/box/reverseDraw', [ReverseDrawController::class, 'reverseDraw']);
    Route::post('/box/info', [InfoController::class, 'info']);
    Route::post('/box/othersPublicBoxes', [OthersPublicBoxesController::class, 'othersPublicBoxes']);
    Route::post('/box/createCard', [CreateCardController::class, 'createCard']);
    Route::post('/box/onlyBoxInfo', [OnlyBoxInfoController::class, 'onlyBoxInfo']);


    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::post('/chat/get', [ChatController::class, 'getAllMessages']);



    Route::patch('/card/update', [CardController::class, 'update']);
    Route::patch('/card/addAdditionalInfo', [CardController::class, 'addAdditionalInfo']);
    Route::delete('card/delete/{card}', [CardController::class, 'delete']);

    Route::get('/user/notifications/{user}', [NotificationController::class, 'show']);
    Route::get('/user/info/{user}', [NotificationController::class, 'userInfo']);
    Route::delete('notification/delete/{notification}', [NotificationController::class, 'delete']);
});
