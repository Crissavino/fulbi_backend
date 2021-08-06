<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Genre;
use App\Models\Location;
use App\Models\Match;
use App\Models\Message;
use App\Models\Player;
use App\Models\Type;
use App\Models\User;
use App\src\Infrastructure\Request\CreateOneMatchRequest;
use App\src\Infrastructure\Services\EloquentChatService;
use App\src\Infrastructure\Services\EloquentLocationService;
use App\src\Infrastructure\Services\EloquentMatchService;
use App\src\Infrastructure\Services\EloquentUserService;
use App\src\Infrastructure\Services\FcmPushNotificationsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchController extends Controller
{
    const DEFAULT_MATCH_RANGE = 20;
    const MIX_GENRE_ID = 3;
    const MAX_FREE_MATCHES = 5;

    public function store(Request $request)
    {
        $parameters = $this->validateParametersForStore($request);
        if (!$parameters['success']) {
            return [
                'success' => false,
                'message' => $parameters['message']
            ];
        }
        $when_play = $parameters['when_play'];
        $genre_id = $parameters['genre_id'];
        $type_id = $parameters['type_id'];
        $is_free_match = $parameters['is_free_match'];
        $currency_id = $parameters['currency_id'];
        $cost = $parameters['cost'];
        $num_players = $parameters['num_players'];
        $locationData = $parameters['locationData'];
        $user = $parameters['user'];
        if ($user->created_matches >= self::MAX_FREE_MATCHES) {
            return [
                'success' => false,
                'max_free_matches_reached' => true,
                'message' => __('errors.maxMatchesReached')
            ];
        }

        $location = (new EloquentLocationService())->create(
            $locationData['lat'],
            $locationData['lng'],
            $locationData['country'],
            $locationData['country_code'],
            $locationData['province'],
            $locationData['province_code'],
            $locationData['city'],
            $locationData['place_id'],
            $locationData['formatted_address'],
            $locationData['is_by_lat_lng']
        );

        $chat = (new EloquentChatService())->create();

        $match = (new EloquentMatchService())->create(
            $location->id,
            $when_play,
            $genre_id,
            $type_id,
            $num_players,
            $is_free_match,
            $currency_id,
            $cost,
            $chat->id,
            $user->id
        );

        (new EloquentUserService())->addOneCreatedMatch($user);

        $gr_circle_radius = 6371;
        $max_distance = self::DEFAULT_MATCH_RANGE;
        $matchLat = $locationData['lat'];
        $matchLng = $locationData['lng'];
        $distance_select = sprintf(
            "( %d * acos( cos( radians(%s) ) " .
            " * cos( radians( lat ) ) " .
            " * cos( radians( lng ) - radians(%s) ) " .
            " + sin( radians(%s) ) * sin( radians( lat ) ) " .
            " ) " .
            ")",
            $gr_circle_radius,
            $matchLat,
            $matchLng,
            $matchLat
        );
        $locations = Location::select('*')
            ->having(DB::raw($distance_select), '<=', $max_distance)
            ->get();

        $players = Player::where('id', '<>', $user->player->id)->whereIn('location_id', $locations->pluck('id'))->with(['user'])->get();
        $players->map(function ($player) use ($request, $user){
            $userDevicesTokens = $player->user->devices->pluck('token')->toArray();
            if(!empty($userDevicesTokens)) {
                FcmPushNotificationsService::sendMatchCreated(
                    __('notifications.match.created'),
                    [],
                    $userDevicesTokens
                );
            }
        });

        return response()->json([
            'success' => true,
            'chat' => $chat,
            'match' => $match
        ]);
    }

    private function validateParametersForStore(Request $request): array
    {

        $whenPlay = $request->when_play;
        if (!$whenPlay) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }
        $whenPlay = Carbon::createFromFormat('d/m/Y H:i', $whenPlay);

        $genreId = intval($request->genre_id);
        if (!$genreId) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        $typeId = intval($request->type_id);
        if (!$typeId) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        $isFreeMatch = boolval($request->is_free_match);
        $currencyId = null;
        $cost = null;
        if (!$isFreeMatch) {
            $currencyId = intval($request->currency_id);
            if (!$currencyId) {
                return [
                    'success' => false,
                    'message' => __('errors.missingParameter')
                ];
            }

            $cost = doubleval($request->cost);
            if (!$cost) {
                return [
                    'success' => false,
                    'message' => __('errors.missingParameter')
                ];
            }
        }

        $numPlayers = intval($request->num_players);
        if (!$numPlayers) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        $locationData = $request->locationData;
        if (!$locationData) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

         return [
            'success' => true,
            'when_play' => $whenPlay,
            'genre_id' => $genreId,
            'type_id' => $typeId,
            'is_free_match' => $isFreeMatch,
            'currency_id' => $currencyId,
            'cost' => $cost,
            'num_players' => $numPlayers,
            'locationData' => $locationData,
            'user' => $request->user()
        ];
    }

    public function getMatch($id)
    {
        $match = Match::find($id);
        $match->participants = $match->players()->with(['user'])->get()->pluck('user');
        $match->cost = number_format($match->cost, 2);
        $match->is_confirmed = $match->players()->where('player_id', request()->user()->player->id)->where('is_confirmed', true)->exists();
        $match->have_notifications = $match->players()->where('player_id', request()->user()->player->id)->where('have_notifications', true)->exists();

        return response()->json([
            'success' => true,
            'match' => $match,
            'location' => Location::find($match->location_id),
            'genre' => Genre::find($match->genre_id),
            'type' => Type::find($match->type_id),
            'currency' => Currency::find($match->currency_id),
            'players_enrolled' => $match->players()->with(['user'])->where('is_existing_player', 0)->get()->pluck('user')->count()
        ]);
    }

    public function edit(Request $request)
    {
        $parameters = $this->validateParametersForUpdate($request);
        if (!$parameters['success']) {
            return [
                'success' => false,
                'message' => $parameters['message']
            ];
        }
        $when_play = $parameters['when_play'];
        $genre_id = $parameters['genre_id'];
        $type_id = $parameters['type_id'];
        $is_free_match = $parameters['is_free_match'];
        $currency_id = $parameters['currency_id'];
        $cost = $parameters['cost'];
        $num_players = $parameters['num_players'];
        $locationData = $parameters['locationData'];
        $user = $parameters['user'];
        $match = $parameters['match'];
        if ($match->owner_id !== $user->id) {
            return [
                'success' => false
            ];
        }

        (new EloquentLocationService())->update(
            $match->location->id,
            $locationData['lat'],
            $locationData['lng'],
            $locationData['country'],
            $locationData['country_code'],
            $locationData['province'],
            $locationData['province_code'],
            $locationData['city'],
            $locationData['place_id'],
            $locationData['formatted_address'],
            $locationData['is_by_lat_lng']
        );

        $match = (new EloquentMatchService())->update(
            $match->id,
            $when_play,
            $genre_id,
            $type_id,
            $num_players,
            $is_free_match,
            $currency_id,
            $cost
        );

        $whenPlay = Carbon::createFromFormat('Y-m-d H:i:s', $match->when_play);
        $day = strlen((string)$whenPlay->day) === 1 ? '0'.$whenPlay->day : $whenPlay->day;
        $month = strlen((string)$whenPlay->month) === 1 ? '0'.$whenPlay->month : $whenPlay->month;
        $hour = strlen((string)$whenPlay->hour) === 1 ? '0'.$whenPlay->hour : $whenPlay->hour;
        $minutes = strlen((string)$whenPlay->minute) === 1 ? '0'.$whenPlay->minute : $whenPlay->minute;
        $otherPlayers = $match->players()->where('player_id', '<>', $user->player->id)->get();
        $otherPlayers->map(function ($player) use ($request, $user, $match, $day, $month, $hour, $minutes){
            $userDevicesTokens = $player->user->devices->pluck('token')->toArray();
            if(!empty($userDevicesTokens)) {
                FcmPushNotificationsService::sendMatchEdited(
                    __('notifications.match.edited', [
                        'userName' => $user->name,
                        'day' => $day . '/' . $month,
                        'hour' => $hour . ':' . $minutes
                    ]),
                    [
                        'match_id' => $match->id
                    ],
                    $userDevicesTokens
                );

                FcmPushNotificationsService::sendSilence(
                    'silence_match_edited',
                    [
                        'match_id' => $match->id
                    ],
                    $userDevicesTokens
                );
            }
        });

        $message = Message::create([
            'text' => __('notifications.match.chat.edited', [
                'userName' => $user->name
            ]),
            'owner_id' => $user->id,
            'chat_id' => $match->chat->id,
            'type' => 4
        ]);
        $message->players()->syncWithoutDetaching($match->players->pluck('id'));

        $match->players()->where('player_id', '<>', $user->player->id)->update([
            'have_notifications' => true
        ]);

        return [
            'success' => true,
            'match' => $match
        ];
    }

    private function validateParametersForUpdate(Request $request): array
    {
        $matchId = intval($request->match_id);
        if (!$matchId) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }
        $match = (new EloquentMatchService())->get($matchId);

        $whenPlay = $request->when_play;
        if (!$whenPlay) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }
        $whenPlay = Carbon::createFromFormat('d/m/Y H:i', $whenPlay);

        $genreId = intval($request->genre_id);
        if (!$genreId) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        $typeId = intval($request->type_id);
        if (!$typeId) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        $isFreeMatch = boolval($request->is_free_match);
        $currencyId = null;
        $cost = null;
        if(!$isFreeMatch) {
            $currencyId = floatval($request->currency_id);
            if (!$currencyId) {
                return [
                    'success' => false,
                    'message' => __('errors.missingParameter')
                ];
            }

            $cost = doubleval($request->cost);
            if (!$cost) {
                return [
                    'success' => false,
                    'message' => __('errors.missingParameter')
                ];
            }
        }

        $numPlayers = intval($request->num_players);
        if (!$numPlayers) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        $locationData = $request->locationData;
        if (!$locationData) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        return [
            'success' => true,
            'when_play' => $whenPlay,
            'genre_id' => $genreId,
            'type_id' => $typeId,
            'is_free_match' => $isFreeMatch,
            'currency_id' => $currencyId,
            'cost' => $cost,
            'num_players' => $numPlayers,
            'locationData' => $locationData,
            'userId' => $request->user()->id,
            'user' => $request->user(),
            'match' => $match
        ];
    }

    public function getAll(Request $request)
    {
        $matches = Match::all();

        $gr_circle_radius = 6371;
        $max_distance = self::DEFAULT_MATCH_RANGE;
        $userLat = $request->user()->player->location->lat;
        $userLng = $request->user()->player->location->lng;
        $distance_select = sprintf(
            "( %d * acos( cos( radians(%s) ) " .
            " * cos( radians( lat ) ) " .
            " * cos( radians( lng ) - radians(%s) ) " .
            " + sin( radians(%s) ) * sin( radians( lat ) ) " .
            " ) " .
            ")",
            $gr_circle_radius,
            $userLat,
            $userLng,
            $userLat
        );
        $locations = Location::select('*')
            ->having(DB::raw($distance_select), '<=', $max_distance)
            ->get();
        $matches = $matches->whereIn('location_id', $locations->pluck('id'));
        if ($matches->count() === 0) {
            return response([
                'success' => true,
                'matches' => [],
            ]);
        }

        return response()->json([
            'success' => true,
            'matches' => $matches
        ]);

    }

    public function getMatchesOffers(Request $request)
    {
        Log::info($request->header('Authorization'));
        $matches = Match::all();
        $matches = $matches->where('is_closed', false);

        $gr_circle_radius = 6371;
        $max_distance = $request->range;
        $userLat = $request->user()->player->location->lat;
        $userLng = $request->user()->player->location->lng;
        $distance_select = sprintf(
            "( %d * acos( cos( radians(%s) ) " .
            " * cos( radians( lat ) ) " .
            " * cos( radians( lng ) - radians(%s) ) " .
            " + sin( radians(%s) ) * sin( radians( lat ) ) " .
            " ) " .
            ")",
            $gr_circle_radius,
            $userLat,
            $userLng,
            $userLat
        );
        $locations = Location::select('*')
            ->having(DB::raw($distance_select), '<=', $max_distance)
            ->get();
        $matches = $matches->whereIn('location_id', $locations->pluck('id'));
        if ($matches->count() === 0) {
            return response([
                'success' => true,
                'matches' => []
            ]);
        }

        $genre_id = $request->genre_id;
        $matches = $matches->where('genre_id', $genre_id);
        if ($matches->count() === 0) {
            return response()->json([
                'success' => true,
                'matches' => []
            ]);
        }

        $matchTypes = json_decode($request->types, true);
        $matches = $matches->whereIn('type_id', $matchTypes);
        if ($matches->count() === 0) {
            return response()->json([
                'success' => true,
                'matches' => []
            ]);
        }

        $matches = $matches->sortBy(function($match){
            $match->location;
            $match->cost = number_format($match->cost, 2);
            $match->participants = $match->players->map(function ($player) use ($match){
                return $player->user;
            });
            return $match->when_play;
        });

        if ($request->when_play) {
            $whenPlay = Carbon::parse($request->when_play)->toDateTimeString();
            $whenPlay = Carbon::createFromFormat('Y-m-d H:i:s', $whenPlay);
            $matches = $matches->filter(function ($match) use ($whenPlay) {
                return $match->when_play > $whenPlay;
            })->take(10);
        } else {
            $today = Carbon::now();
            $matches = $matches->filter(function ($match) use ($today) {
                return $match->when_play > $today->toDateTimeString();
            })->take(10);
        }

        return response()->json([
            'success' => true,
            'matches' => $matches->values()
        ]);
    }

    public function sendInvitationToUser(Request $request) {
        $userWhoInvite = $request->user();
        $userToInvite = User::find($request->user_id);
        $match = Match::find($request->match_id);
        // comprobar genero del partido si es compatible con el genero del jugador
        if ($match->genre_id !== $userToInvite->genre->id && $match->genre_id !== self::MIX_GENRE_ID) {
            return response()->json([
                'success' => false
            ]);
        }

        $whenPlay = Carbon::createFromFormat('Y-m-d H:i:s', $match->when_play);
        $day = strlen((string)$whenPlay->day) === 1 ? '0'.$whenPlay->day : $whenPlay->day;
        $month = strlen((string)$whenPlay->month) === 1 ? '0'.$whenPlay->month : $whenPlay->month;
        $hour = strlen((string)$whenPlay->hour) === 1 ? '0'.$whenPlay->hour : $whenPlay->hour;
        $minutes = strlen((string)$whenPlay->minute) === 1 ? '0'.$whenPlay->minute : $whenPlay->minute;
        $userDevicesTokens = $userToInvite->devices->pluck('token')->toArray();

        FcmPushNotificationsService::sendMatchInvitation(
            __('notifications.match.invited', [
                'userName' => $userWhoInvite->name,
                'day' => $day . '/' . $month,
                'hour' => $hour . ':' . $minutes
            ]),
            [
                'match_id' => $match->id
            ],
            $userDevicesTokens
        );

        FcmPushNotificationsService::sendSilence(
            'silence_invited_match',
            [],
            $userDevicesTokens
        );

        $match->players()->syncWithoutDetaching($userToInvite->player->id);

        return response()->json([
            'success' => true
        ]);
    }

    public function joinMatchFromInvitationLinkNewUser(Request $request) {
        $userWhoInvite = User::find($request->owner_id);
        $userToInvite = User::find($request->user_id);
        $match = Match::find($request->match_id);
        // comprobar genero del partido si es compatible con el genero del jugador
        if ($match->genre_id !== $userToInvite->genre->id && $match->genre_id !== self::MIX_GENRE_ID) {
            return response()->json([
                'success' => false
            ]);
        }

        $whenPlay = Carbon::createFromFormat('Y-m-d H:i:s', $match->when_play);
        $day = strlen((string)$whenPlay->day) === 1 ? '0'.$whenPlay->day : $whenPlay->day;
        $month = strlen((string)$whenPlay->month) === 1 ? '0'.$whenPlay->month : $whenPlay->month;
        $hour = strlen((string)$whenPlay->hour) === 1 ? '0'.$whenPlay->hour : $whenPlay->hour;
        $minutes = strlen((string)$whenPlay->minute) === 1 ? '0'.$whenPlay->minute : $whenPlay->minute;
        $userDevicesTokens = $userToInvite->devices->pluck('token')->toArray();

        FcmPushNotificationsService::sendMatchInvitation(
            __('notifications.match.invited', [
                'userName' => $userWhoInvite->name,
                'day' => $day . '/' . $month,
                'hour' => $hour . ':' . $minutes
            ]),
            [
                'match_id' => $match->id
            ],
            $userDevicesTokens
        );

        FcmPushNotificationsService::sendSilence(
            'silence_invited_match',
            [],
            $userDevicesTokens
        );

        $match->players()->syncWithoutDetaching($userToInvite->player->id);

        return response()->json([
            'success' => true
        ]);
    }

    public function joinMatchFromInvitationLinkExistingUser(Request $request) {
        $userWhoInvite = User::find($request->owner_id);
        $userToInvite = User::find($request->user_id);
        $match = Match::find($request->match_id);
        // comprobar genero del partido si es compatible con el genero del jugador
        if ($match->genre_id !== $userToInvite->genre->id && $match->genre_id !== self::MIX_GENRE_ID) {
            return response()->json([
                'success' => false
            ]);
        }

        $whenPlay = Carbon::createFromFormat('Y-m-d H:i:s', $match->when_play);
        $day = strlen((string)$whenPlay->day) === 1 ? '0'.$whenPlay->day : $whenPlay->day;
        $month = strlen((string)$whenPlay->month) === 1 ? '0'.$whenPlay->month : $whenPlay->month;
        $hour = strlen((string)$whenPlay->hour) === 1 ? '0'.$whenPlay->hour : $whenPlay->hour;
        $minutes = strlen((string)$whenPlay->minute) === 1 ? '0'.$whenPlay->minute : $whenPlay->minute;
        $userDevicesTokens = $userToInvite->devices->pluck('token')->toArray();

        FcmPushNotificationsService::sendMatchInvitation(
            __('notifications.match.invited', [
                'userName' => $userWhoInvite->name,
                'day' => $day . '/' . $month,
                'hour' => $hour . ':' . $minutes
            ]),
            [
                'match_id' => $match->id
            ],
            $userDevicesTokens
        );

        FcmPushNotificationsService::sendSilence(
            'silence_invited_match',
            [],
            $userDevicesTokens
        );

        $match->players()->syncWithoutDetaching($userToInvite->player->id);
        $match->players()->where('player_id', $userToInvite->player->id)->update([
            'is_existing_player' => true
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function rejectInvitationToMatch(Request $request) {

        $user = $request->user();
        $player = $user->player;
        $match = Match::find($request->match_id);
        $userWhoInvited = User::find($match->owner_id);
        $isInTheMatch = $match->players()->where('player_id', $player->id)->exists();
        if (!$isInTheMatch) {
            return response()->json([
                'success' => false
            ]);
        }

        $match->players()->wherePivot('match_id', $match->id)->wherePivot('player_id', $player->id)->detach();

        $whenPlay = Carbon::createFromFormat('Y-m-d H:i:s', $match->when_play);
        $day = strlen((string)$whenPlay->day) === 1 ? '0'.$whenPlay->day : $whenPlay->day;
        $month = strlen((string)$whenPlay->month) === 1 ? '0'.$whenPlay->month : $whenPlay->month;
        $hour = strlen((string)$whenPlay->hour) === 1 ? '0'.$whenPlay->hour : $whenPlay->hour;
        $minutes = strlen((string)$whenPlay->minute) === 1 ? '0'.$whenPlay->minute : $whenPlay->minute;
        $userDevicesTokens = $userWhoInvited->devices->pluck('token')->toArray();

        FcmPushNotificationsService::sendRejectMatchInvitation(
            __('notifications.match.reject', [
                'userName' => $user->name,
                'day' => $day . '/' . $month,
                'hour' => $hour . ':' . $minutes
            ]),
            [
                'match_id' => $match->id
            ],
            $userDevicesTokens
        );

        $matches = $this->returnAllMatches($request);

        return response()->json([
            'success' => true,
            'matches' => $matches->values()
        ]);
    }

    public function joinMatch(Request $request) {

        $userWhoJoin = $request->user();
        $match = Match::find($request->match_id);
        $userWhoInvited = User::find($match->owner_id);
        // comprobar genero del partido si es compatible con el genero del jugador
        if ($match->genre_id !== $userWhoJoin->genre->id && $match->genre_id !== self::MIX_GENRE_ID) {
            return response()->json([
                'success' => false,
                'matches' => []
            ]);
        }
        // relacionar y devolver partidos del jugador
        $match->players()->syncWithoutDetaching($userWhoJoin->player->id);
        $match->players()->where('player_id', $userWhoJoin->player->id)->update([
            'is_confirmed' => true
        ]);

        $message = Message::create([
            'text' => __('notifications.match.chat.join', [
                'userName' => $userWhoJoin->name
            ]),
            'owner_id' => $userWhoJoin->id,
            'chat_id' => $match->chat->id,
            'type' => 4
        ]);
        $message->players()->syncWithoutDetaching($match->players->pluck('id'));

        $match->players()->where('player_id', '<>', $userWhoJoin->player->id)->update([
            'have_notifications' => true
        ]);

        $whenPlay = Carbon::createFromFormat('Y-m-d H:i:s', $match->when_play);
        $day = strlen((string)$whenPlay->day) === 1 ? '0'.$whenPlay->day : $whenPlay->day;
        $month = strlen((string)$whenPlay->month) === 1 ? '0'.$whenPlay->month : $whenPlay->month;
        $hour = strlen((string)$whenPlay->hour) === 1 ? '0'.$whenPlay->hour : $whenPlay->hour;
        $minutes = strlen((string)$whenPlay->minute) === 1 ? '0'.$whenPlay->minute : $whenPlay->minute;
        $userDevicesTokens = $userWhoInvited->devices->pluck('token')->toArray();

        FcmPushNotificationsService::sendJoinedToMatch(
            __('notifications.match.join', [
                'userName' => $userWhoJoin->name,
                'day' => $day . '/' . $month,
                'hour' => $hour . ':' . $minutes
            ]),
            [
                'match_id' => $match->id
            ],
            $userDevicesTokens
        );

        $otherPlayers = $match->players()->where('player_id', '<>', $userWhoJoin->player->id)->get();
        $otherPlayers->map(function ($player) use ($match){
            $userDevicesTokens = $player->user->devices->pluck('token')->toArray();
            if(!empty($userDevicesTokens)) {
                FcmPushNotificationsService::sendSilence(
                    'silence_join_match',
                    [
                        'match_id' => $match->id
                    ],
                    $userDevicesTokens
                );
            }
        });

        $match->location;
        $match->cost = number_format($match->cost, 2);
        $match->participants = $match->players->map(function ($player) use ($match) {
            return $player->user;
        });

        $matches = $this->returnAllMatches($request);

        return response()->json([
            'success' => true,
            'matches' => $matches->values(),
            'match' => $match
        ]);
    }

    public function leaveMatch(Request $request) {

        $userWhoJoin = $request->user();
        $player = $userWhoJoin->player;
        $match = Match::find($request->match_id);
        $userWhoInvited = User::find($match->owner_id);
        $isInTheMatch = $match->players()->where('player_id', $player->id)->exists();
        if (!$isInTheMatch) {
            return response()->json([
                'success' => false
            ]);
        }

        $match->players()->wherePivot('match_id', $match->id)->wherePivot('player_id', $player->id)->detach();

        Message::create([
            'text' => __('notifications.match.chat.left', [
                'userName' => $userWhoJoin->name
            ]),
            'owner_id' => $userWhoJoin->id,
            'chat_id' => $match->chat->id,
            'type' => 4
        ]);

        $whenPlay = Carbon::createFromFormat('Y-m-d H:i:s', $match->when_play);
        $day = strlen((string)$whenPlay->day) === 1 ? '0'.$whenPlay->day : $whenPlay->day;
        $month = strlen((string)$whenPlay->month) === 1 ? '0'.$whenPlay->month : $whenPlay->month;
        $hour = strlen((string)$whenPlay->hour) === 1 ? '0'.$whenPlay->hour : $whenPlay->hour;
        $minutes = strlen((string)$whenPlay->minute) === 1 ? '0'.$whenPlay->minute : $whenPlay->minute;
        $userDevicesTokens = $userWhoInvited->devices->pluck('token')->toArray();

        FcmPushNotificationsService::sendLeftMatch(
            __('notifications.match.left', [
                'userName' => $userWhoJoin->name,
                'day' => $day . '/' . $month,
                'hour' => $hour . ':' . $minutes
            ]),
            [
                'match_id' => $match->id
            ],
            $userDevicesTokens
        );

        $otherPlayers = $match->players()->where('player_id', '<>', $userWhoJoin->player->id)->get();
        $otherPlayers->map(function ($player) use ($match){
            $userDevicesTokens = $player->user->devices->pluck('token')->toArray();
            if(!empty($userDevicesTokens)) {
                FcmPushNotificationsService::sendSilence(
                    'silence_leave_match',
                    [
                        'match_id' => $match->id
                    ],
                    $userDevicesTokens
                );
            }
        });

        $matches = $this->returnAllMatches($request);

        return response()->json([
            'success' => true,
            'matches' => $matches->values()
        ]);
    }

    public function getMyMatches(Request $request)
    {
        $matches = $this->returnAllMatches($request);
        $today = Carbon::now();
        $matches = $matches->filter(function ($match) use ($today) {
            return $match->when_play > $today->toDateTimeString();
        });
        $matches = $matches->where('is_closed', false);

        return response()->json([
            'success' => true,
            'matches' => $matches->values()
        ]);
    }

    public function getMyCreatedMatches(Request $request)
    {
        $matches = Match::all();
        $today = Carbon::now();
        $matches = $matches->filter(function ($match) use ($today) {
            return $match->when_play > $today->toDateTimeString();
        });
        $matches = $matches->where('owner_id', $request->user()->id);
        $matches = $matches->where('is_closed', false);
        $matches = $matches->sortBy(function ($match) use ($request) {
            $match->location;
            $match->have_notifications = $match->players()->where('player_id', $request->user()->player->id)
                ->where('have_notifications', true)->exists();
            $match->cost = number_format($match->cost, 2);
            $match->participants = $match->players->map(function ($player) use ($match) {
                return $player->user;
            });
            return $match->when_play;
        });

        return response()->json([
            'success' => true,
            'matches' => $matches->values()
        ]);
    }

    public function deleteMatch(Request $request) {

        $user = $request->user();
        $match = Match::find($request->match_id);
        $isInTheOwner = $match->owner_id === $user->id;
        if (!$isInTheOwner) {
            return response()->json([
                'success' => false
            ]);
        }
        $match->players()->wherePivot('match_id', $match->id)->detach();

        $match->delete();

        $matches = $this->returnAllMatches($request);

        return response()->json([
            'success' => true,
            'matches' => $matches->values()
        ]);
    }

    /**
     * @param Request $request
     * @return Match[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function returnAllMatches(Request $request)
    {
        $matches = Match::all();
        $matches = $matches->where('owner_id', $request->user()->id);
        $matches = $matches->where('is_closed', false);
        $matches = $matches->merge($request->user()->player->matches);
        $matches = $matches->sortBy(function ($match) use ($request) {
            $match->location;
            $match->have_notifications = $match->players()->where('player_id', $request->user()->player->id)->where('have_notifications', true)->exists();
            $match->is_confirmed = $match->players()->where('player_id', $request->user()->player->id)->where('is_confirmed', true)->exists();
            $match->cost = number_format($match->cost, 2);
            $match->participants = $match->players->map(function ($player) use ($match) {
                return $player->user;
            });
            return $match->when_play;
        });

        return $matches;
    }

}
