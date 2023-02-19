<?php

namespace App\Http\Controllers\Box;

use App\Http\Controllers\Controller;
use App\Models\Box;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function update(Request $request, Box $box)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $box->fill($credentials);
        $box->save();
        return response()->json(
            [
                'status' => 'success',
                'box' => $box
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
