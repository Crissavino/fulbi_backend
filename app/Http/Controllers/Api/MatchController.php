<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Device;
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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchController extends Controller
{
    const DEFAULT_MATCH_RANGE = 20;
    const MIX_GENRE_ID = 3;
    const MAX_FREE_MATCHES = 5;
    const MAX_FREE_MATCHES_BY_WEEK = 3;

    public function store(Request $request)
    {
        $parameters = $this->validateParametersForStore($request);
        if (!$parameters['success']) {
            return [
                'success' => false,
                'message' => $parameters['message']
            ];
        }
        $genre_id = $parameters['genre_id'];
        $type_id = $parameters['type_id'];
        $is_free_match = $parameters['is_free_match'];
        $currency_id = $parameters['currency_id'];
        $cost = $parameters['cost'];
        $num_players = $parameters['num_players'];
        $locationData = $parameters['locationData'];
        $user = $parameters['user'];
        if (!$user->premium) {
            if ($user->matches_created >= self::MAX_FREE_MATCHES) {
                return [
                    'success' => false,
                    'max_free_matches_reached' => true,
                    'message' => __('errors.maxMatchesReached')
                ];
            }

            // tengo q hacer esto porque me cambia el valor de when_play
            $matchesInThatWeek = $this->countMatchesThatWeek($user, $parameters['when_play']);
            if ($matchesInThatWeek > self::MAX_FREE_MATCHES_BY_WEEK) {
                return [
                    'success' => false,
                    'max_free_matches_by_week_reached' => true,
                    'message' => __('errors.maxMatchesByWeekReached')
                ];
            }
        }
//        $locationData = json_decode($locationData, true);

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

        $when_play = Carbon::createFromFormat('d/m/Y H:i', $request->when_play);

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
        $players->map(function ($player) use ($request, $user, $match){
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
                FcmPushNotificationsService::sendMatchCreated(
                    __('notifications.match.created'),
                    [],
                    $userDevicesTokensEn
                );

                FcmPushNotificationsService::sendSilence(
                    'silence_match_created',
                    [
                        'match_id' => $match->id
                    ],
                    $userDevicesTokensEn
                );
            }

            if(!empty($userDevicesTokensEs)) {
                App::setLocale('es');
                FcmPushNotificationsService::sendMatchCreated(
                    __('notifications.match.created'),
                    [],
                    $userDevicesTokensEs
                );

                FcmPushNotificationsService::sendSilence(
                    'silence_match_created',
                    [
                        'match_id' => $match->id
                    ],
                    $userDevicesTokensEs
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
        $currencyId = intval($request->currency_id);
        if (!$currencyId) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        $cost = doubleval($request->cost);
        if (!$cost && !$isFreeMatch) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
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
        $match->participants = $match->players()->where('is_confirmed', true)->with(['user'])->get()->pluck('user');
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

        if (!$user->premium) {
            $matchesInThatWeek = $this->countMatchesThatWeek($user, $parameters['when_play']);
            if ($matchesInThatWeek > self::MAX_FREE_MATCHES_BY_WEEK) {
                return [
                    'success' => false,
                    'max_free_matches_by_week_reached' => true,
                    'message' => __('errors.maxMatchesByWeekReached')
                ];
            }
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

        $when_play = Carbon::createFromFormat('d/m/Y H:i', $request->when_play);
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
                FcmPushNotificationsService::sendMatchEdited(
                    __('notifications.match.edited', [
                        'userName' => $user->name,
                        'day' => $day . '/' . $month,
                        'hour' => $hour . ':' . $minutes
                    ]),
                    [
                        'match_id' => $match->id
                    ],
                    $userDevicesTokensEn
                );

                FcmPushNotificationsService::sendSilence(
                    'silence_match_edited',
                    [
                        'match_id' => $match->id
                    ],
                    $userDevicesTokensEn
                );
            }

            if(!empty($userDevicesTokensEs)) {
                App::setLocale('es');
                FcmPushNotificationsService::sendMatchEdited(
                    __('notifications.match.edited', [
                        'userName' => $user->name,
                        'day' => $day . '/' . $month,
                        'hour' => $hour . ':' . $minutes
                    ]),
                    [
                        'match_id' => $match->id
                    ],
                    $userDevicesTokensEs
                );

                FcmPushNotificationsService::sendSilence(
                    'silence_match_edited',
                    [
                        'match_id' => $match->id
                    ],
                    $userDevicesTokensEs
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
        $currencyId = floatval($request->currency_id);
        if (!$currencyId) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        $cost = doubleval($request->cost);
        if (!$cost && !$isFreeMatch) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
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
            $match->participants = $match->players()->where('is_confirmed', true)->with(['user'])->get()->pluck('user');
            return $match->when_play;
        });

        $today = Carbon::now();
        $matches = $matches->filter(function ($match) use ($today) {
            return $match->when_play > $today->toDateTimeString();
        });

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

        $userDevicesTokensEn = [];
        $userDevicesTokensEs = [];
        foreach ($userToInvite->devices as $device) {
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

        if (!empty($userDevicesTokensEn)) {
            App::setLocale('en');
            FcmPushNotificationsService::sendMatchInvitation(
                __('notifications.match.invited', [
                    'userName' => $userWhoInvite->name,
                    'day' => $day . '/' . $month,
                    'hour' => $hour . ':' . $minutes
                ]),
                [
                    'match_id' => $match->id
                ],
                $userDevicesTokensEn
            );

            FcmPushNotificationsService::sendSilence(
                'silence_invited_match',
                [],
                $userDevicesTokensEn
            );
        }
        if (!empty($userDevicesTokensEs)) {
            App::setLocale('es');
            FcmPushNotificationsService::sendMatchInvitation(
                __('notifications.match.invited', [
                    'userName' => $userWhoInvite->name,
                    'day' => $day . '/' . $month,
                    'hour' => $hour . ':' . $minutes
                ]),
                [
                    'match_id' => $match->id
                ],
                $userDevicesTokensEs
            );

            FcmPushNotificationsService::sendSilence(
                'silence_invited_match',
                [],
                $userDevicesTokensEs
            );
        }

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
        $userDevicesTokensEn = [];
        $userDevicesTokensEs = [];
        foreach ($userToInvite->devices as $device) {
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
        if (!empty($userDevicesTokensEn)) {
            App::setLocale('en');
            FcmPushNotificationsService::sendMatchInvitation(
                __('notifications.match.invited', [
                    'userName' => $userWhoInvite->name,
                    'day' => $day . '/' . $month,
                    'hour' => $hour . ':' . $minutes
                ]),
                [
                    'match_id' => $match->id
                ],
                $userDevicesTokensEn
            );

            FcmPushNotificationsService::sendSilence(
                'silence_invited_match',
                [],
                $userDevicesTokensEn
            );
        }
        if (!empty($userDevicesTokensEs)) {
            App::setLocale('es');
            FcmPushNotificationsService::sendMatchInvitation(
                __('notifications.match.invited', [
                    'userName' => $userWhoInvite->name,
                    'day' => $day . '/' . $month,
                    'hour' => $hour . ':' . $minutes
                ]),
                [
                    'match_id' => $match->id
                ],
                $userDevicesTokensEs
            );

            FcmPushNotificationsService::sendSilence(
                'silence_invited_match',
                [],
                $userDevicesTokensEs
            );
        }

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

        $userDevicesTokensEn = [];
        $userDevicesTokensEs = [];
        foreach ($userToInvite->devices as $device) {
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

        if (!empty($userDevicesTokensEn)) {
            App::setLocale('en');
            FcmPushNotificationsService::sendMatchInvitation(
                __('notifications.match.invited', [
                    'userName' => $userWhoInvite->name,
                    'day' => $day . '/' . $month,
                    'hour' => $hour . ':' . $minutes
                ]),
                [
                    'match_id' => $match->id
                ],
                $userDevicesTokensEn
            );

            FcmPushNotificationsService::sendSilence(
                'silence_invited_match',
                [],
                $userDevicesTokensEn
            );
        }
        if (!empty($userDevicesTokensEs)) {
            App::setLocale('es');
            FcmPushNotificationsService::sendMatchInvitation(
                __('notifications.match.invited', [
                    'userName' => $userWhoInvite->name,
                    'day' => $day . '/' . $month,
                    'hour' => $hour . ':' . $minutes
                ]),
                [
                    'match_id' => $match->id
                ],
                $userDevicesTokensEs
            );

            FcmPushNotificationsService::sendSilence(
                'silence_invited_match',
                [],
                $userDevicesTokensEs
            );
        }

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

        $userDevicesTokensEn = [];
        $userDevicesTokensEs = [];
        foreach ($userWhoInvited->devices as $device) {
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
            FcmPushNotificationsService::sendRejectMatchInvitation(
                __('notifications.match.reject', [
                    'userName' => $user->name,
                    'day' => $day . '/' . $month,
                    'hour' => $hour . ':' . $minutes
                ]),
                [
                    'match_id' => $match->id
                ],
                $userDevicesTokensEn
            );
        }
        if (!empty($userDevicesTokensEs)) {
            App::setLocale('es');
            FcmPushNotificationsService::sendRejectMatchInvitation(
                __('notifications.match.reject', [
                    'userName' => $user->name,
                    'day' => $day . '/' . $month,
                    'hour' => $hour . ':' . $minutes
                ]),
                [
                    'match_id' => $match->id
                ],
                $userDevicesTokensEs
            );
        }

        $matches = $this->returnAllMatches($request);
        $today = Carbon::now();
        $matches = $matches->filter(function ($match) use ($today) {
            return $match->when_play > $today->toDateTimeString();
        });

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

        $userDevicesTokensEn = [];
        $userDevicesTokensEs = [];
        foreach ($userWhoInvited->devices as $device) {
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
        if (!empty($userDevicesTokensEn)) {
            App::setLocale('en');
            FcmPushNotificationsService::sendJoinedToMatch(
                __('notifications.match.join', [
                    'userName' => $userWhoJoin->name,
                    'day' => $day . '/' . $month,
                    'hour' => $hour . ':' . $minutes
                ]),
                [
                    'match_id' => $match->id
                ],
                $userDevicesTokensEn
            );
        }
        if (!empty($userDevicesTokensEs)) {
            App::setLocale('es');
            FcmPushNotificationsService::sendJoinedToMatch(
                __('notifications.match.join', [
                    'userName' => $userWhoJoin->name,
                    'day' => $day . '/' . $month,
                    'hour' => $hour . ':' . $minutes
                ]),
                [
                    'match_id' => $match->id
                ],
                $userDevicesTokensEs
            );
        }

        $otherPlayers = $match->players()->where('player_id', '<>', $userWhoJoin->player->id)->get();
        $otherPlayers->map(function ($player) use ($match){
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
                FcmPushNotificationsService::sendSilence(
                    'silence_join_match',
                    [
                        'match_id' => $match->id
                    ],
                    $userDevicesTokensEn
                );
            }

            if(!empty($userDevicesTokensEs)) {
                App::setLocale('es');
                FcmPushNotificationsService::sendSilence(
                    'silence_join_match',
                    [
                        'match_id' => $match->id
                    ],
                    $userDevicesTokensEs
                );
            }
        });

        $match->location;
        $match->cost = number_format($match->cost, 2);
        $match->participants = $match->players()->where('is_confirmed', true)->with(['user'])->get()->pluck('user');

        $matches = $this->returnAllMatches($request);
        $today = Carbon::now();
        $matches = $matches->filter(function ($match) use ($today) {
            return $match->when_play > $today->toDateTimeString();
        });

        return response()->json([
            'success' => true,
            'matches' => $matches->values(),
            'match' => $match
        ]);
    }

    public function leaveMatch(Request $request) {

        $userWhoLeave = $request->user();
        $player = $userWhoLeave->player;
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
                'userName' => $userWhoLeave->name
            ]),
            'owner_id' => $userWhoLeave->id,
            'chat_id' => $match->chat->id,
            'type' => 4
        ]);

        $whenPlay = Carbon::createFromFormat('Y-m-d H:i:s', $match->when_play);
        $day = strlen((string)$whenPlay->day) === 1 ? '0'.$whenPlay->day : $whenPlay->day;
        $month = strlen((string)$whenPlay->month) === 1 ? '0'.$whenPlay->month : $whenPlay->month;
        $hour = strlen((string)$whenPlay->hour) === 1 ? '0'.$whenPlay->hour : $whenPlay->hour;
        $minutes = strlen((string)$whenPlay->minute) === 1 ? '0'.$whenPlay->minute : $whenPlay->minute;
        $userDevicesTokensEn = [];
        $userDevicesTokensEs = [];
        foreach ($userWhoInvited->devices as $device) {
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
        if (!empty($userDevicesTokensEn)) {
            App::setLocale('en');
            FcmPushNotificationsService::sendLeftMatch(
                __('notifications.match.left', [
                    'userName' => $userWhoLeave->name,
                    'day' => $day . '/' . $month,
                    'hour' => $hour . ':' . $minutes
                ]),
                [
                    'match_id' => $match->id
                ],
                $userDevicesTokensEn
            );
        }
        if (!empty($userDevicesTokensEs)) {
            App::setLocale('es');
            FcmPushNotificationsService::sendLeftMatch(
                __('notifications.match.left', [
                    'userName' => $userWhoLeave->name,
                    'day' => $day . '/' . $month,
                    'hour' => $hour . ':' . $minutes
                ]),
                [
                    'match_id' => $match->id
                ],
                $userDevicesTokensEs
            );
        }

        $otherPlayers = $match->players()->where('player_id', '<>', $userWhoLeave->player->id)->get();
        $otherPlayers->map(function ($player) use ($match){
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
                FcmPushNotificationsService::sendSilence(
                    'silence_leave_match',
                    [
                        'match_id' => $match->id
                    ],
                    $userDevicesTokensEn
                );
            }

            if(!empty($userDevicesTokensEs)) {
                App::setLocale('es');
                FcmPushNotificationsService::sendSilence(
                    'silence_leave_match',
                    [
                        'match_id' => $match->id
                    ],
                    $userDevicesTokensEs
                );
            }
        });

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

    public function expelFromMatch(Request $request)
    {
        $userWhoExpel = $request->user();
        $userToExpel = User::find($request->user_to_expel);
        $playerToExpel = $userToExpel->player;
        $match = Match::find($request->match_id);
        if ($userWhoExpel->id ==! $match->owner_id) {
            return response()->json([
                'success' => false
            ]);
        }

        $match->players()->wherePivot('match_id', $match->id)->wherePivot('player_id', $playerToExpel->id)->detach();

        Message::create([
            'text' => __('notifications.match.chat.expelled', [
                'userName' => $userToExpel->name
            ]),
            'owner_id' => $userWhoExpel->id,
            'chat_id' => $match->chat->id,
            'type' => 4
        ]);

        $otherPlayers = $match->players()->where('player_id', '<>', $userWhoExpel->player->id)->get();
        $otherPlayers->map(function ($player) use ($match, $userToExpel){
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
                if ($player->user->id == $userToExpel->id) {
                    FcmPushNotificationsService::sendSilence(
                        'silence_im_expelled',
                        [
                            'match_id' => $match->id
                        ],
                        $userDevicesTokensEn
                    );
                } else {
                    FcmPushNotificationsService::sendSilence(
                        'silence_player_expelled',
                        [
                            'match_id' => $match->id
                        ],
                        $userDevicesTokensEn
                    );
                }
            }

            if(!empty($userDevicesTokensEs)) {
                App::setLocale('es');
                if ($player->user->id == $userToExpel->id) {
                    FcmPushNotificationsService::sendSilence(
                        'silence_im_expelled',
                        [
                            'match_id' => $match->id
                        ],
                        $userDevicesTokensEn
                    );
                } else {
                    FcmPushNotificationsService::sendSilence(
                        'silence_player_expelled',
                        [
                            'match_id' => $match->id
                        ],
                        $userDevicesTokensEs
                    );
                }
            }
        });

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
            $match->participants = $match->players()->where('is_confirmed', true)->with(['user'])->get()->pluck('user');
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

        $gr_circle_radius = 6371;
        $max_distance = self::DEFAULT_MATCH_RANGE;
        $matchLat = $match->location->lat;
        $matchLng = $match->location->lng;
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
        $players->map(function ($player) use ($request, $user, $match){
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

            FcmPushNotificationsService::sendSilence(
                'silence_deleted_match',
                [
                    'match_id' => $match->id
                ],
                $userDevicesTokensEn
            );

            FcmPushNotificationsService::sendSilence(
                'silence_deleted_match',
                [
                    'match_id' => $match->id
                ],
                $userDevicesTokensEs
            );
        });

        $match->delete();

        $matches = $this->returnAllMatches($request);
        $today = Carbon::now();
        $matches = $matches->filter(function ($match) use ($today) {
            return $match->when_play > $today->toDateTimeString();
        });
        $matches = $matches->where('is_closed', false);

        return response()->json([
            'success' => true,
            'deletedId' => $request->match_id,
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
            $match->participants = $match->players()->where('is_confirmed', true)->with(['user'])->get()->pluck('user');
            return $match->when_play;
        });

        return $matches;
    }

    /**
     * @param $user
     * @param Carbon $when_play
     * @return mixed
     */
    protected function countMatchesThatWeek($user, Carbon $when_play)
    {
        $weekStartDate = $when_play->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = $when_play->endOfWeek()->format('Y-m-d H:i');
        return $user->player->matches->whereBetween('when_play', [$weekStartDate, $weekEndDate])->count();
    }

}
