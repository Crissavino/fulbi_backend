<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Match;
use App\Models\Message;
use App\src\Infrastructure\Services\FcmPushNotificationsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
            'type' => 1
        ]);

        $message->players()->syncWithoutDetaching($match->players->pluck('id'));

        $match->players()->where('player_id', '<>', $user->player->id)->update([
            'have_notifications' => true
        ]);

        $whenPlay = Carbon::createFromFormat('Y-m-d H:i:s', $match->when_play);
        $day = strlen((string)$whenPlay->day) === 1 ? '0'.$whenPlay->day : $whenPlay->day;
        $month = strlen((string)$whenPlay->month) === 1 ? '0'.$whenPlay->month : $whenPlay->month;
        $hour = strlen((string)$whenPlay->hour) === 1 ? '0'.$whenPlay->hour : $whenPlay->hour;
        $minutes = strlen((string)$whenPlay->minute) === 1 ? '0'.$whenPlay->minute : $whenPlay->minute;
        $otherPlayers = $match->players()->where('player_id', '<>', $user->player->id)->get();
        $message->owner;
        $otherPlayers->map(function ($player) use ($message, $request, $match, $day, $month, $hour, $minutes){
            $userDevicesTokensEn = [];
            $userDevicesTokensEs = [];
            foreach ($player->user->devices as $device) {
                if ($device->token) {
                    if ($device->language === null) {
                        $userDevicesTokensEn[] = $device->token;
                    } elseif (str_contains($device->language, 'en')) {
                        $userDevicesTokensEn[] = $device->token;
                    } elseif (str_contains($device->language, 'es')) {
                        $userDevicesTokensEs[] = $device->token;
                    } else {
                        $userDevicesTokensEn[] = $device->token;
                    }
                }
            }

            if(!empty($userDevicesTokensEn)) {
                App::setLocale('en');
                FcmPushNotificationsService::sendChatTextMessage(
                    __('notifications.match.chat.newMessage', [
                        'userName' => $request->user()->name,
                        'day' => $day . '/' . $month,
                        'hour' => $hour . ':' . $minutes
                    ]),
                    $message->text,
                    [
                        'chat_id' => $request->chat_id,
                        'match_id' => $match->id
                    ],
                    $userDevicesTokensEn
                );

                FcmPushNotificationsService::sendSilence(
                    'silence_new_chat_message',
                    [
                        'message' => $message,
                        'match_id' => $match->id
                    ],
                    $userDevicesTokensEn
                );
            }

            if(!empty($userDevicesTokensEs)) {
                App::setLocale('es');
                FcmPushNotificationsService::sendChatTextMessage(
                    __('notifications.match.chat.newMessage', [
                        'userName' => $request->user()->name,
                        'day' => $day . '/' . $month,
                        'hour' => $hour . ':' . $minutes
                    ]),
                    $message->text,
                    [
                        'chat_id' => $request->chat_id,
                        'match_id' => $match->id
                    ],
                    $userDevicesTokensEs
                );

                FcmPushNotificationsService::sendSilence(
                    'silence_new_chat_message',
                    [
                        'message' => $message,
                        'match_id' => $match->id
                    ],
                    $userDevicesTokensEs
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => $message,
            'match' => $match,
            'messages' => $match->chat->messages
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

        $this->readChatNotifications($request, $match);

        return response()->json([
            'success' => true,
            'messages' => $messages->values()
        ]);

    }

    public function readChatMessages(Request $request)
    {
        $match = Match::find($request->match_id);
        $player = $request->user()->player;
        $match->players()->where('player_id', $player->id)->update([
            'have_notifications' => false
        ]);

        $userDevicesTokens = $player->user->devices->pluck('token')->toArray();
        FcmPushNotificationsService::sendSilence(
            'silence_chat_notification_read',
            [
                'match_id' => $match->id
            ],
            $userDevicesTokens
        );

        return response()->json([
            'success' => true,
        ]);

    }

    public function readChatNotifications(Request $request, Match $match)
    {
        $player = $request->user()->player;
        $match->players()->where('player_id', $player->id)->update([
            'have_notifications' => false
        ]);

        $userDevicesTokens = $player->user->devices->pluck('token')->toArray();
        FcmPushNotificationsService::sendSilence(
            'silence_chat_notification_read',
            [
                'match_id' => $match->id
            ],
            $userDevicesTokens
        );

    }

}
