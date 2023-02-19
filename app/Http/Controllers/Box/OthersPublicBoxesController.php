<?php

namespace App\Http\Controllers\Box;

use App\Http\Controllers\Controller;
use App\Models\Box;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OthersPublicBoxesController extends Controller
{
    public function othersPublicBoxes(Request $request)
    {
        $data = $request->getContent();
        $credentials = json_decode($data, true);
        $publicBoxes = Box::where('isPublic', true)->get();
        $allOtherBoxes = [];
        foreach ($publicBoxes as $box) {
            $publicBox = DB::table('boxes_with_people')
                ->where('box_id', $box->id)
                ->where('user_id', $credentials['user_id'])
                ->first();
            if (!$publicBox) {
                $allOtherBoxes[] = Box::find($box->id);
            }
        }

        return response()->json(
            [
                'status' => 'success',
                'allOtherBoxes' =>  $allOtherBoxes
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
