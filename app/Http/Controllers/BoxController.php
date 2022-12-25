<?php

namespace App\Http\Controllers;

use App\Models\Box;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BoxController extends Controller
{


    public function create(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $validator = Validator::make($credentials, [
            'title' => 'required|min:3|max:50',
            'isPrivate' => 'sometimes|in:1',
            'email' => 'sometimes|in:1',
        ], [], []);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ])
                ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }


        if ($validator->passes()) {
            $box = new Box();
            $box->fill([
                'title' => $credentials['title'],
                'description' => $credentials['description'],
                'cover' => $credentials['cover'],
                'max_people_in_box' => $credentials['max_people_in_box'],
                'creator_id' => $credentials['creator_id'],
                'isPrivate' => $credentials['isPrivate'],
                'email' => $credentials['email'],
                'cost' => $credentials['cost']
            ]);
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
    }

    public function join(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        //TODO проверить наличие id в БД через валидатор
        $validator = Validator::make($credentials, [], [], []);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ])
                ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        if ($validator->passes()) {

            //Проверка на уникальную пару значений ид коробки и пользователя
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
}
