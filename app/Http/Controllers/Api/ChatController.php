<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Match;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $user = $request->user();
        $match = Match::find($request->match_id);

        $message = Message::create([
            'text' => $request->text,
            'owner_id' => $request->owner_id,
            'chat_id' => $request->chat_id,
        ]);

        $message->players()->syncWithoutDetaching($match->players->pluck('id'));

        return response()->json([
            'success' => true,
            'message' => $message,
            'match' => $match,
            'messages' => $match->chat->messages,
        ]);

    }

    public function myMessages(Request $request)
    {
        $match = Match::find($request->match_id);

        $messages = $match->chat->messages->sortByDesc(function ($message) {
            $message->owner;
            return $message->created_at;
        });

        return response()->json([
            'success' => true,
            'messages' => $messages->values(),
        ]);

    }
}
