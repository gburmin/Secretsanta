<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\Box;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function show(User $user)
    {
        $notifications = Notification::select('box_id', 'text', 'created_at')
            ->where('user_id', $user->id)
            ->get();

        foreach ($notifications as $notif) {
            $box = Box::find($notif->box_id);
            $notif->box_title = $box->title;
        }
        return response()->json(
            [
                'status' => 'success',
                'notifications' => $notifications
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function userInfo(User $user)
    {
        $notifications = Notification::select('box_id', 'text', 'created_at')
            ->where('user_id', $user->id)
            ->get();

        foreach ($notifications as $notif) {
            $box = Box::find($notif->box_id);
            $notif->box_title = $box->title;
        }
        return response()->json(
            [
                'status' => 'success',
                'user' => $user,
                'notifications' => $notifications
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
