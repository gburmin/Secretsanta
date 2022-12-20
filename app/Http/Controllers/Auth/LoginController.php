<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class LoginController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $arr = json_decode($data, true); // переводим в ассоциативный массив
        $validator = Validator::make($arr, [
            'email' => 'required|email',
            'password' => 'required|min:3'
        ], [], []);

        if ($validator->fails()) {
            return response()->json($validator->errors())
                ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        if ($validator->passes()) {
            if (Auth::attempt($arr)) {
                return response()->json(['status' => 'success', Auth::user()])
                    ->header('Location', '/')
                    ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }

            return response()->json(['status' => 'error'])
                ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }
}
