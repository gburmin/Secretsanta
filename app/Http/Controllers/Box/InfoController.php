<?php

namespace App\Http\Controllers\Box;

use App\Http\Controllers\Controller;
use App\Models\Box;
use App\Models\Card;
use App\Models\CardInfo;
use App\Models\InvitedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfoController extends Controller
{
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
        foreach ($secret_santas_ward as $ward) {
            $card = Card::join('card_infos', 'cards.card_infos_id', '=', 'card_infos.id')
                ->select('cards.id', 'user_id', 'box_id', 'name', 'email', 'phone', 'wishlist', 'address')
                ->where('box_id', $credentials['box_id'])
                ->where('user_id', $ward->id)
                ->first();
            $ward->name = $card->name;
            $ward->email = $card->email;
            $ward->phone = $card->phone;
            $ward->wishlist = $card->wishlist;
            $ward->address = $card->address;
        }

        $box = Box::where('id', $credentials['box_id'])->first();
        $card = Card::join('card_infos', 'cards.card_infos_id', '=', 'card_infos.id')
            ->select('cards.id', 'user_id', 'box_id', 'name', 'email', 'image', 'phone', 'wishlist', 'address', 'presentSent', 'presentReceived')
            ->where('box_id', $credentials['box_id'])
            ->where('user_id', $credentials['user_id'])
            ->first();
        foreach ($secret_santas as $santa) {
            $cardInfoId = Card::select('id', 'card_infos_id')
                ->where('user_id', $santa->id)
                ->where('box_id', $credentials['box_id'])
                ->first();
            if ($cardInfoId) {
                $cardInfo = CardInfo::find($cardInfoId->card_infos_id);
                $santa->card_id = $cardInfoId->id;
                $santa->name = $cardInfo->name;
                $santa->email = $cardInfo->email;
                $santa->image = $cardInfo->image;
            }
        }
        $invitedUsers = InvitedUser::select('invited_users.name', 'invited_users.email')
            ->join('boxes_with_people', 'invited_users.id', '=', 'boxes_with_people.invited_user_id')
            ->where('boxes_with_people.box_id', $credentials['box_id'])->get();

        if ($secret_santas[0]->secret_santa_to_id) {
            foreach ($secret_santas as $santa) {
                // блок с добавлением телефона, статусом доставки/отправки подарков
                $cardInfoId = Card::select('card_infos_id')
                    ->where('user_id', $santa->id)
                    ->where('box_id', $credentials['box_id'])
                    ->first();
                $cardInfo = CardInfo::find($cardInfoId->card_infos_id);
                $santa->phone = $cardInfo->phone;
                $santa->presentSent = $cardInfo->presentSent;
                $santa->presentReceived = $cardInfo->presentReceived;
                // блок с получением имени подопечного
                $cardInfoId = Card::select('card_infos_id')
                    ->where('user_id', $santa->secret_santa_to_id)
                    ->where('box_id', $credentials['box_id'])
                    ->first();
                $cardInfo = CardInfo::find($cardInfoId->card_infos_id);
                $santa->ward_name = $cardInfo->name;
                // блок с получением id и имени тайного санты пользователя
                $userSecretSanta = DB::table('boxes_with_people')
                    ->where('secret_santa_to_id', $santa->id)
                    ->where('box_id', $credentials['box_id'])
                    ->first();
                $cardInfoId = Card::select('card_infos_id')
                    ->where('user_id', $userSecretSanta->user_id)
                    ->where('box_id', $credentials['box_id'])
                    ->first();
                $cardInfo = CardInfo::find($cardInfoId->card_infos_id);
                $santa->your_secret_santa_id = $userSecretSanta->user_id;
                $santa->your_secret_santa_name = $cardInfo->name;
            }
        }

        return response()->json(
            [
                'status' => 'success',
                'box' => $box,
                'secret_santas' => $secret_santas,
                'secret_santas_ward' => $secret_santas_ward,
                'card' => $card,
                'invitedUsers' => $invitedUsers,
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
