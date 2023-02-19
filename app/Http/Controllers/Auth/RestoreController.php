<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class RestoreController extends Controller
{
    public function restore(Request $request, User $user)
    {
        $data = $request->getContent(); // получаем body запроса
        $credential = json_decode($data, true); // переводим в ассоциативный массив
        $email = $credential['email'];
        $user = User::where('email', $email)->first();
        $password = Str::random(10);
        $user->fill([
            'password' => Hash::make($password)
        ]);
        $user->save();
        mail($email, 'Восстановление пароля', 'ваш пароль ' . $password, 'From: admin@backsecsanta.com');
        return response()->json([
            'status' => 'success',
            'message' => 'Пароль отправлен на почту ' . $email
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
