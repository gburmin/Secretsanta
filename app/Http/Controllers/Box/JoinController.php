<?php

namespace App\Http\Controllers\Box;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Card;
use App\Models\CardInfo;
use App\Models\InvitedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;

class JoinController extends Controller
{
    public function join(Request $request)
    {
        // возможно два варианта входящих url: 1)ссылка из письма api+/box/join/?name={name}&email={email}&id={box_id}
        //2) присоединение к публичной коробке api+/box/join/?user_id={user_id}&id={box_id}
        if ($request->user_id) {
            $user = User::find($request->user_id);
        } else {
            $user = User::where('email', $request->email)
                ->first();
        }
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
            InvitedUser::where('email', $user->email)->delete();
            $cardInfo = CardInfo::create([
                'name' => $request->name,
                'email' => $user->email
            ]);
            Card::create([
                'user_id' => $user->id,
                'box_id' => $request->id,
                'card_infos_id' => $cardInfo->id
            ]);
            if ($request->isRedirect) {
                return redirect('https://secret-santa-1.netlify.app/');
            }
            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'вы присоединились к этой коробке'
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
}
