<?php

// app/Http/Controllers/NotificationController.php
namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\SaveFcmTokenRequest;
use App\Http\Resources\NotificationResource;
use Kreait\Firebase\Factory;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
     public function index(Request $request)
     {
                $user = $request->user();

                $notifications = notification::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                return response()->json([
                    'status' => 'success',
                    'notifications' => NotificationResource::collection($notifications)
                ]);

        }
       public function store(StoreNotificationRequest $request) {

           $notification = Notification::create([
            'user_id' => $request->user()->id,
            ...$request->validated()
        ]);
            return response()->json([
            'message' => 'Notification registered successfully.',
            'status'=>"success",
            'Notification' =>  new NotificationResource($notification)
        ],200);
    }

    public function saveToken(SaveFcmTokenRequest $request)
    {

        $user = $request->user();
        $user->update([
                'fcm_token' => $request->fcm_token
            ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Token saved successfully'
        ]);
    }

   public static function sendFirebaseNotification($fcmToken, $title, $body, $clickAction = null)
{
    try {
        $factory = (new Factory)
            ->withServiceAccount(base_path('storage/app/firebase/ecommerce-a0978-firebase-adminsdk-fbsvc-9f3abee7b4.json'));

        $messaging = $factory->createMessaging();

        $message = [
            'token' => $fcmToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'android' => [
                'notification' => [
                    'click_action' => $clickAction ?? 'FLUTTER_NOTIFICATION_CLICK',
                    'sound' => 'default',
                ],
            ],
            'apns' => [
                'payload' => [
                    'aps' => ['sound' => 'default'],
                ],
            ],
        ];
        $user=User::where('fcm_token',$fcmToken)->first();
            $Notification = notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
            ]);
        $messaging->send($message);


    } catch (\Throwable $e) {
        \Log::error('Firebase notification failed: ' . $e->getMessage());
    }
}

}

