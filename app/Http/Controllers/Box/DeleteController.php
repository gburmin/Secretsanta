<?php

namespace App\Http\Controllers\Box;

use App\Http\Controllers\Controller;
use App\Models\Box;

class DeleteController extends Controller
{
    public function delete(Box $box)
    {
        $box->delete();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'коробка успешно удалена'
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
