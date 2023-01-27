<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller

{
    public   function  update(Request $request, User $user)
    {
        $data = $request->getContent(); // получаем body запроса
        $credential = json_decode($data, true); // переводим в ассоциативный массив
        $user->name = $credential['name'];
        $user->password = Hash::make($credential['newPassword']);
        $user->email = $credential['email'];
        $user->email_notify = $credential['email_notify']; //Поле для поддержки чекбокса "уведомление на эл.почту"

        $result =  $user->save();
        if ($result) {

            return response()->json([
                'status' => 'success',
                'message' => 'Данные изменены!'
            ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {

            return response()->json([
                'status' => 'error',
                'message' => 'Данные  не изменены!'
            ])
                ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }

    public function delete(User $user)
    {
        $user->delete();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'Пользователь удален'
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
