<?php

namespace App\Http\Controllers\Box;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Card;
use App\Models\CardInfo;
use Illuminate\Http\Request;

class CreateCardController extends Controller
{
    public function createCard(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $user = User::find($credentials['user_id']);
        $cardInfo = CardInfo::create([
            'name' => $user->name,
            'email' => $user->email,
            'image' => $credentials['image']
        ]);
        $card = Card::create([
            'user_id' => $credentials['user_id'],
            'box_id' => $credentials['box_id'],
            'card_infos_id' => $cardInfo->id
        ]);
        return response()->json(
            [
                'status' => 'success',
                'card' => $card,
                'cardInfo' => $cardInfo
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
