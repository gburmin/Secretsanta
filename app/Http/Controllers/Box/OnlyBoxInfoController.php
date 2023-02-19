<?php

namespace App\Http\Controllers\Box;

use App\Http\Controllers\Controller;
use App\Models\Box;
use Illuminate\Http\Request;

class OnlyBoxInfoController extends Controller
{
    public function onlyBoxInfo(Request $request)
    {
        $data = $request->getContent();
        $credentials = json_decode($data, true);
        $box = Box::find($credentials['box_id']);
        return response()->json(
            [
                'status' => 'success',
                'box' =>  $box
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
