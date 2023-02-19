<?php

namespace App\Http\Controllers\Box;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReverseDrawController extends Controller
{
    public function reverseDraw(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        DB::table('boxes_with_people')
            ->where('box_id', $credentials['box_id'])
            ->update(['secret_santa_to_id' => null]);
        $users = DB::table('boxes_with_people')
            ->where('box_id', $credentials['box_id'])
            ->get();
        foreach ($users as $user) {
            $card = Card::where('user_id', $user->user_id)
                ->where('box_id', $credentials['box_id'])
                ->first();
            Message::where('card_id', $card->id)
                ->delete();
        }
        return response()->json(
            [
                'status' => 'success',
                'message' => 'жеребьевка успешно сброшена'
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
