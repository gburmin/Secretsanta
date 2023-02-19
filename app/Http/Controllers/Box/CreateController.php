<?php

namespace App\Http\Controllers\Box;

use App\Http\Controllers\Controller;
use App\Models\Box;
use App\Models\User;
use App\Models\Card;
use App\Models\CardInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreateController extends Controller
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
        if ($box->isPublic) {
            $user = User::find($credentials['creator_id']);
            $cardInfo = CardInfo::create([
                'name' => $user->name,
                'email' => $user->email
            ]);
            Card::create([
                'user_id' => $credentials['creator_id'],
                'box_id' => $box->id,
                'card_infos_id' => $cardInfo->id
            ]);
        }

        return response()->json(
            [
                'status' => 'success',
                'box' => $box
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
