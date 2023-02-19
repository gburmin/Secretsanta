<?php

namespace App\Http\Controllers\Box;

use App\Http\Controllers\Controller;
use App\Models\InvitedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SendInvitesController extends Controller
{
    public function sendInvites(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        foreach ($credentials['emails'] as $email) {
            $InvitedUser = InvitedUser::create(['name' => $email['name'], 'email' => $email['email']]);
            DB::table('boxes_with_people')->insert(['invited_user_id' => $InvitedUser->id, 'box_id' => $email['id']]);
            mail($email['email'], 'Приглашение в коробку для участия в тайном санте', 'Уважаемый ' . $email['name'] . '! Вам выслано приглашения для участия в игре "Тайный Санта". Чтобы подтвердить приглашение, перейдите по ссылке ' . 'https://backsecsanta.alwaysdata.net/api/box/join?email=' . $email['email'] . '&name=' . $email['name']
                . '&id=' . $email['id'] . "&isRedirect=1" . '. Если вы не зарегистрированы на нашем сайте, то переход по ссылке создаст вам аккаунт. После чего, вам придёт повторное письмо, содержащее временный пароль для авторизации на сайте. В дальнейшем, его можно изменить на странице профиля.');
        }
        return response()->json(
            [
                'status' => 'success',
                'message' => 'письма отосланы'
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
