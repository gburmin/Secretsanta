<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\User;
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

    public function join(Request $request, User $user)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'такой пользователь не зарегистрирован'
                ]
            )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        /*Проверка на уникальную пару значений ид коробки и пользователя*/
        if (!DB::table('boxes_with_people')->where('user_id', $user->id)->where('box_id',  $credentials['box_id'])->first()) {
            DB::table('boxes_with_people')->insert([
                'user_id' => $user->id,
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

    public function getBoxes(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $publicBoxes = Box::where('isPublic', true)->get();
        $privateBoxes = DB::table('boxes_with_people')
            ->join('boxes', 'boxes_with_people.box_id', '=', 'boxes.id')
            ->where('boxes_with_people.user_id', $credentials['id'])
            ->where('isPublic', false)
            ->get();
        return response()->json(
            [
                'status' => 'success',
                'publicBoxes' => $publicBoxes,
                'privateBoxes' => $privateBoxes
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
