<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Card;
use App\Models\User;
use App\Notifications\GiftNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //логинимся
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }


    public function cardNotification($id)
        //получаем карточку по переданному идентификатору
    {
        $card = Card::query()->where('id', '=', $id)->first();

        //проверяем есть ли такая карточка
        if ($card != NULL) {

            //получаем связанные с карточкой данные коробки для получения информации о статусе жеребьевки
            $box = $card->box();
            // dd($box);

            //получаем пользователя связанного с коробкой для проверки изменений пользовательских данных
            $user = $card->user();



            //сообщение об отправленном подарке
            if ($card->gift_sent)
                $gift_sent_result = ['status' => 'success',
                    'message' => 'Подарок отправлен'];
            else
                $gift_sent_result = ['status' => 'error',
                    'message' => 'Подарок не отправлен'];
            // сообщение о полученом подарке
            if ($card->gift_received)

                $gift_received_result = ['status' => 'success',
                    'message' => 'Подарок получен'];
            else
                $gift_received_result = ['status' => 'error',
                    'message' => 'Подарок не получен'];

            //сообщение о статусе жеребьевки
            if ($box->draw_done)
                $draw_status_result = (['status' => 'success',
                    'message' => 'Жеребьевка проведена']);
            else
                $draw_status_result = (['status' => 'error',
                    'message' => 'Жеребьевка не проведена']);

            //проверка на изменение списка желаний
            if (!$card->wasChanged('wish_list')) {
                $wish_list_result = ['status' => 'success',
                    'message' => 'текст списка желаний не поменялся'];
            } else
                $wish_list_result = ['status' => 'error',
                    'message' => 'Список желаний изменился'];
            //проверка на изменение контактов
            if (!$user->wasChanged()) {
                $user_changes_result = ['status' => 'success',
                    'message' => 'Данные пользователя не менялись'];
            } else
                $user_changes_result = ['status' => 'error',
                    'message' => 'Данные пользователя изменены'];

            return response()->json([
                'gift_received' => $gift_received_result,
                'gift_sent' => $gift_sent_result,
                'draw_done' => $draw_status_result,
                'wish_list' => $wish_list_result,
                'users_credentials_changes' => $user_changes_result
            ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            return response()->json(['status' => 'error',
                'message' => 'Зарегистрируйте карточку'])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

    }


}
