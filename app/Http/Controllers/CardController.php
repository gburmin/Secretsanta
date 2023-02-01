<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CardInfo;
use App\Models\Card;

class CardController extends Controller
{
    public function update(Request $request)

    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $card_info = CardInfo::select('card_infos.name', 'card_infos.email', 'card_infos.image')
            ->join('cards', 'cards.card_infos_id', '=', 'card_infos.id')
            ->where('box_id', $credentials['box_id'])
            ->where('user_id', $credentials['user_id'])
            ->update([
                'card_infos.name' => $credentials['name'],
                'card_infos.email' => $credentials['email'],
                'card_infos.image' => $credentials['image']
            ]);
        $card_info = CardInfo::select('card_infos.name', 'card_infos.email', 'card_infos.image')
            ->join('cards', 'cards.card_infos_id', '=', 'card_infos.id')
            ->where('box_id', $credentials['box_id'])
            ->where('user_id', $credentials['user_id'])
            ->first();
        return response()->json(
            [
                'status' => 'success',
                'card_info' => $card_info
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function addContactInfo(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $card = Card::find($credentials['card_id']);
        $card_info_id = $card->card_infos_id;
        $contactInfo = CardInfo::where('id', $card_info_id)->first();
        $contactInfo->fill($credentials);
        $contactInfo->save();
        return response()->json(
            [
                'status' => 'success',
                'cardInfo' => $contactInfo
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
