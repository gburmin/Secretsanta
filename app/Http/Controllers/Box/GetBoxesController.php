<?php

namespace App\Http\Controllers\Box;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetBoxesController extends Controller
{
    public function getBoxes(Request $request)
    {
        $data = $request->getContent(); // получаем body запроса
        $credentials  = json_decode($data, true); // переводим в ассоциативный массив
        $publicBoxes = DB::table('boxes_with_people')
            ->join('boxes', 'boxes_with_people.box_id', '=', 'boxes.id')
            ->where('boxes_with_people.user_id', $credentials['id'])
            ->where('isPublic', true)
            ->get();
        $privateBoxes = DB::table('boxes_with_people')
            ->join('boxes', 'boxes_with_people.box_id', '=', 'boxes.id')
            ->where('boxes_with_people.user_id', $credentials['id'])
            ->where('isPublic', false)
            ->get();
        return response()->json(
            [
                'status' => 'success',
                'publicBoxes' => $publicBoxes,
                'privateBoxes' => $privateBoxes
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
