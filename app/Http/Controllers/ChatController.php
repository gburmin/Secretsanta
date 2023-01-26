<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $message = new Message();
        $message->fill($credentials);
        $message->save();
        return response()->json(
            [
                'status' => 'success',
                'message' => $message
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function getAllMessages(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $messages = Message::where('card_id', $credentials['card_id'])
            ->where(function ($query) use ($credentials) {
                $query->where('writer_id', $credentials['first_chatter'])
                    ->orWhere('writer_id', $credentials['second_chatter']);
            })->get();
        return response()->json(
            [
                'status' => 'success',
                'message' => $messages
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
