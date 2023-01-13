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

    public function join(Request $request, User $user)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $user = User::where('email', $credentials['email'])
            ->where('name', $credentials['name'])
            ->first();
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



    public function draw(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $users =  DB::table('boxes_with_people')
            ->select('user_id')
            ->where('box_id', $credentials['box_id'])
            ->get();
        foreach ($users as $user) {
            $users_id[] = $user->user_id;
        }


        foreach ($users as $user) {
            $secret_santa = DB::table('boxes_with_people')
                ->select('secret_santa_to_id')
                ->where('box_id', $credentials['box_id'])
                ->where('user_id', $user->user_id)->first();
            while (is_null($secret_santa->secret_santa_to_id)) {
                $rand = array_rand($users_id);
                if ($user->user_id !== $users_id[$rand]) {
                    DB::table('boxes_with_people')
                        ->where('box_id', $credentials['box_id'])
                        ->where('user_id', $user->user_id)
                        ->update(['secret_santa_to_id' => $users_id[$rand]]);

                    $secret_santa = DB::table('boxes_with_people')
                        ->select('secret_santa_to_id')
                        ->where('box_id', $credentials['box_id'])
                        ->where('user_id', $user->user_id)->first();
                    unset($users_id[$rand]);
                }
            }
        }
        $secret_santas_ward = DB::table('boxes_with_people')
            ->join('users', 'boxes_with_people.secret_santa_to_id', '=', 'users.id')
            ->select(['users.id', 'name', 'email'])
            ->where('boxes_with_people.box_id', $credentials['box_id'])
            ->get();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'жеребьевка проведена',
                'secret_santas_ward' => $secret_santas_ward
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function reverseDraw(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        DB::table('boxes_with_people')
            ->where('box_id', $credentials['box_id'])
            ->update(['secret_santa_to_id' => null]);
        return response()->json(
            [
                'status' => 'success',
                'message' => 'жеребьевка успешно сброшена'
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }


    public function info(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $secret_santas = DB::table('boxes_with_people')
            ->join('users', 'boxes_with_people.user_id', '=', 'users.id')
            ->select(['users.id', 'secret_santa_to_id', 'name', 'email'])
            ->where('boxes_with_people.box_id', $credentials['box_id'])
            ->get();
        $secret_santas_ward = DB::table('boxes_with_people')
            ->join('users', 'boxes_with_people.secret_santa_to_id', '=', 'users.id')
            ->select(['users.id', 'name', 'email'])
            ->where('boxes_with_people.box_id', $credentials['box_id'])
            ->get();
        return response()->json(
            [
                'status' => 'success',
                'secret_santas' => $secret_santas,
                'secret_santas_ward' => $secret_santas_ward
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
