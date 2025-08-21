<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Kreait\Firebase\Factory;

class NotificationController extends Controller
{
    public function index()
    {
        return response()->make('
            <!DOCTYPE html>
            <html>
            <head>
                <title>Send Notification</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <body class="p-4">
                <div class="container">
                    <h2>Kirim Notifikasi</h2>
                    <form method="POST" action="/send-notification">
                        <input type="hidden" name="_token" value="'.csrf_token().'">
                        <div class="mb-3">
                            <label>User ID</label>
                            <input type="number" name="user_id" class="form-control" value="2" required>
                        </div>
                        <div class="mb-3">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" value="Test Notification" required>
                        </div>
                        <div class="mb-3">
                            <label>Image Url (Opsional)</label>
                            <input type="text" name="image" value="'.asset('upload/avatar/default.png').'" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Avatar</label>
                            <input type="text" name="avatar" value="'.asset('upload/avatar/thumbnails/default.png').'" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Body</label>
                            <textarea name="body" class="form-control" required>Ini adalah body uji coba notifikasi</textarea>
                        </div>
                        <button class="btn btn-primary">Send</button>
                    </form>
                </div>
            </body>
            </html>
        ');
    }

    public static function sendToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title'   => 'required|string',
            'body'    => 'required|string',
            'image'   => 'nullable|url', // opsional
            'avatar'   => 'nullable|url', // opsional
        ]);

        $user = User::with('fcmTokens')->findOrFail($request->user_id);
        $tokens = $user->fcmTokens()->pluck('fcm_token')->toArray(); 

        if (count($tokens) === 0) {
            return response()->json(['message' => 'User has no FCM tokens'], 404);
        }
 
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/private/busogi-ee864-40d3e6e45a91.json'));

        $messaging = $factory->createMessaging();

        $message = [
            'data' => [
                'title' => $request->title,
                'body'  => $request->body,
                'icon'  => $request->avatar ?? null,
                'image' => $request->image ?? null,
            ],
        ]; 
 
        $sendReport = $messaging->sendMulticast($message, $tokens);

        return response()->json([
            'success' => $sendReport->successes()->count(),
            'failure' => $sendReport->failures()->count(),
        ]);
    }
}
