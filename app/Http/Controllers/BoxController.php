<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\User;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;

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
        $card = new Card();
        $card->fill([
            'user_id' => $credentials['creator_id'],
            'box_id' => $box->id
        ]);
        $card->save();
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

    public function delete(Box $box)
    {
        $box->delete();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'коробка успешно удалена'
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    public function sendInvites(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        foreach ($credentials['emails'] as $email) {
            mail($email['email'], 'Приглашение в коробку для участия в тайном санте', 'Уважаемый ' . $email['name'] . '! Вам выслано приглашения для участия в тайном санте.Чтобы принять приглашение, нажмите на ссылку' . 'https://backsecsanta.alwaysdata.net/api/box/join?email=' . $email['email'] . '&name=' . $email['name']
                . '&id=' . $email['id'] . '. Если вы не зарегистрированы на нашем сайте, то переход по ссылке создаст вам аккаунт!');
        }
        return response()->json(
            [
                'status' => 'success',
                'message' => 'письма отосланы'
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function join(Request $request)
    {
        $user = User::where('email', $request->email)
            ->first();
        if (!$user) {
            $user = new User();
            $password = Str::random(10);
            $user->fill([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password)
            ]);
            event(new Registered($user));
            $user->save();
            mail($request->email, 'Ваш новый пароль', 'ваш пароль ' . $password);
        }
        if (!DB::table('boxes_with_people')->where('user_id', $user->id)->where('box_id', $request->id)->first()) {
            DB::table('boxes_with_people')->insert([
                'user_id' => $user->id,
                'box_id' => $request->id
            ]);
            $card = new Card();
            $card->fill([
                'user_id' => $user->id,
                'box_id' => $request->id
            ]);
            $card->save();
            return view('welcome');
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
        $users_id[] = array_shift($users_id);
        foreach ($users as $user) {
            DB::table('boxes_with_people')
                ->where('box_id', $credentials['box_id'])
                ->where('user_id', $user->user_id)
                ->update(['secret_santa_to_id' => array_shift($users_id)]);
        }


        $secret_santas_ward = DB::table('boxes_with_people')
            ->join('users', 'boxes_with_people.secret_santa_to_id', '=', 'users.id')
            ->select(['users.id', 'name', 'email'])
            ->where('boxes_with_people.box_id', $credentials['box_id'])
            ->get();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'жеребьевка успешно проведена',
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
        $box = Box::where('id', $credentials['box_id'])->first();
        return response()->json(
            [
                'status' => 'success',
                'box' => $box,
                'secret_santas' => $secret_santas,
                'secret_santas_ward' => $secret_santas_ward
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
