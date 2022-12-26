<?php

namespace App\Http\Controllers;

use App\Models\Box;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoxController extends Controller
{


    public function create(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $box = new Box();
        $box->fill($credentials);
        $box->save();
        DB::table('boxes_with_people')->insert([
            'user_id' => $credentials['creator_id'],
            'box_id' => $box->id
        ]);
        return response()->json(
            [
                'status' => 'success',
                'box' => $box
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function join(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        /*Проверка на уникальную пару значений ид коробки и пользователя
        Хотя если пользователь не будет вбивать ид, то проверка не нужна, но я уверен, что будет*/
        if (!DB::table('boxes_with_people')->where('user_id', $credentials['user_id'])->where('box_id',  $credentials['box_id'])->first()) {
            DB::table('boxes_with_people')->insert([
                'user_id' => $credentials['user_id'],
                'box_id' => $credentials['box_id']
            ]);
            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'вы присоединились к коробке'
                ]
            )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        return response()->json(
            [
                'status' => 'error',
                'message' => 'вы уже присоединились к этой коробке'
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
