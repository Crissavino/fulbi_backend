<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Match;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $match->players()->where('player_id', '<>', $user->player->id)->update([
            'have_notifications' => true
        ]);

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
        })->take(10);

        if ($request->created_at) {
            $createdAt = Carbon::parse($request->created_at)->toDateTimeString();
            $createdAt = Carbon::createFromFormat('Y-m-d H:i:s', $createdAt);
            $messages = $match->chat->messages->filter(function ($message) use ($createdAt) {
                return $message->created_at < $createdAt;
            });
            $messages = $messages->sortByDesc(function ($message) {
                $message->owner;
                return $message->created_at;
            })->take(10);
        }

        $this->readNotifications($request, $match);

        return response()->json([
            'success' => true,
            'messages' => $messages->values(),
        ]);

    }

    public function readNotifications(Request $request, Match $match)
    {
        $player = $request->user()->player;
        $match->players()->where('player_id', $player->id)->update([
            'have_notifications' => false
        ]);

    }

}
