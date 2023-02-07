<?php

namespace App\Http\Controllers;

use App\Models\Box;
use Illuminate\Http\Request;
use App\Models\CardInfo;
use App\Models\Card;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

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

    protected function checkPresentReceived($card, $contactInfo)
    {
        if ($contactInfo->wasChanged('presentReceived')) {
            // уведомление должно прийти тайному санте юзера
            $userSecretSanta = DB::table('boxes_with_people')
                ->where('secret_santa_to_id', $card->user_id)
                ->where('box_id', $card->box_id)
                ->first();
            Notification::create([
                'user_id' => $userSecretSanta->user_id,
                'box_id' => $card->box_id,
                'text' => 'Ваш подопечный получил ваш подарок'
            ]);
            Message::create([
                'writer_id' => $card->user_id,
                'receiver_id' => $userSecretSanta->user_id,
                'card_id' => $card->id,
                'text' => 'Я получил ваш подарок!'
            ]);
        };
    }

    protected function checkPresentSent($card, $contactInfo)
    {
        if ($contactInfo->wasChanged('presentSent')) {
            // уведомление должно прийти подопечному юзера
            $userSecretSanta = DB::table('boxes_with_people')
                ->where('user_id', $card->user_id)
                ->where('box_id', $card->box_id)
                ->first();
            Notification::create([
                'user_id' => $userSecretSanta->secret_santa_to_id,
                'box_id' => $card->box_id,
                'text' => 'Ваш тайный санта отправил вам подарок'
            ]);
            Message::create([
                'writer_id' => $card->user_id,
                'receiver_id' => $userSecretSanta->secret_santa_to_id,
                'card_id' => $card->id,
                'text' => 'Я отправил вам подарок!'
            ]);
        };
    }

    protected function checkContactInfoChange($card, $contactInfo)
    {
        if ($contactInfo->wasChanged('address') || $contactInfo->wasChanged('phone')) {
            // уведомление должно прийти тайному санте юзера
            $userSecretSanta = DB::table('boxes_with_people')
                ->where('secret_santa_to_id', $card->user_id)
                ->where('box_id', $card->box_id)
                ->first();
            Notification::create([
                'user_id' => $userSecretSanta->user_id,
                'box_id' => $card->box_id,
                'text' => 'Обновлена информация о контактах подопечного'
            ]);
            Message::create([
                'writer_id' => $card->user_id,
                'receiver_id' => $userSecretSanta->user_id,
                'card_id' => $card->id,
                'text' => 'Обновлена информация о контактах подопечного!'
            ]);
        };
    }
    protected function checkWishlistChange($card, $contactInfo)
    {
        if ($contactInfo->wasChanged('wishlist')) {
            // уведомление должно прийти тайному санте юзера
            $userSecretSanta = DB::table('boxes_with_people')
                ->where('secret_santa_to_id', $card->user_id)
                ->where('box_id', $card->box_id)
                ->first();
            Notification::create([
                'user_id' => $userSecretSanta->user_id,
                'box_id' => $card->box_id,
                'text' => 'Обновлена информация о вишлисте подопечного'
            ]);
            Message::create([
                'writer_id' => $card->user_id,
                'receiver_id' => $userSecretSanta->user_id,
                'card_id' => $card->id,
                'text' => 'Обновлена информация о вишлисте подопечного!'
            ]);
        };
    }

    public function addAdditionalInfo(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $card = Card::find($credentials['card_id']);
        $card_info_id = $card->card_infos_id;
        $contactInfo = CardInfo::where('id', $card_info_id)->first();
        $contactInfo->fill($credentials);
        $contactInfo->save();
        $this->checkPresentReceived($card, $contactInfo);
        $this->checkPresentSent($card, $contactInfo);
        $this->checkContactInfoChange($card, $contactInfo);
        $this->checkWishlistChange($card, $contactInfo);


        return response()->json(
            [
                'status' => 'success',
                'cardInfo' => $contactInfo
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function delete(Card $card)
    {
        $cardInfo = CardInfo::where('id', $card->card_infos_id);
        $cardInfo->delete();
        $card->delete();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'карточка успешно удалена'
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
