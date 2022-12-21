<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;


class RegisterController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }


    public function register(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $arr = json_decode($data, true); // переводим в ассоциативный массив
        $validator = Validator::make($arr, [
            'name' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'required|min:3'
        ], [], []);

        // Check validation failure
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        // Check validation success
        if ($validator->passes()) {
            $user = new User();

            $user->fill([
                'name' => $arr['name'],
                'email' => $arr['email'],
                'password' => Hash::make(($arr['password']))
            ]);
            event(new Registered($user));
            $user->save(); // сохраняем в таблицу
            $token = Auth::login($user); // логинимся
            // Storage::disk('local')->put('example.txt', $token); // для тестов
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
        }
    }
}
