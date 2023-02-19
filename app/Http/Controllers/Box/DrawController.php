<?php

namespace App\Http\Controllers\Box;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DrawController extends Controller
{
    public function draw(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $users =  DB::table('boxes_with_people')
            ->select('user_id')
            ->where('box_id', $credentials['box_id'])
            ->get();
        foreach ($users as $user) {
            $card = Card::where('box_id', $credentials['box_id'])
                ->where('user_id', $user->user_id)
                ->first();
            if ($card) {
                $users_id[] = $user->user_id;
            }
        }
        $users_id[] = array_shift($users_id);
        DB::table('boxes_with_people')
            ->where('box_id', $credentials['box_id'])
            ->whereNotNull('invited_user_id')
            ->delete();
        foreach ($users as $user) {
            // уведомления о проведении жеребьевки
            Notification::create([
                'user_id' => $user->user_id,
                'box_id' => $credentials['box_id'],
                'text' => 'Жеребьевка проведена!'
            ]);

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
}
