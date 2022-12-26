<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;


class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credential = json_decode($data, true); // переводим в ассоциативный массив

        $user = new User();

        $user->fill([
            'name' => $credential['name'],
            'email' => $credential['email'],
            'password' => Hash::make(($credential['password']))
        ]);
        event(new Registered($user));
        $user->save(); // сохраняем в таблицу
        $token = Auth::login($user); // логинимся
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
