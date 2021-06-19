<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Genre;
use App\Models\Location;
use App\Models\Match;
use App\Models\Type;
use App\Models\User;
use App\src\Infrastructure\Request\CreateOneMatchRequest;
use App\src\Infrastructure\Services\EloquentChatService;
use App\src\Infrastructure\Services\EloquentLocationService;
use App\src\Infrastructure\Services\EloquentMatchService;
use App\src\Infrastructure\Services\EloquentUserService;
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
        $currency_id = $parameters['currency_id'];
        $cost = $parameters['cost'];
        $num_players = $parameters['num_players'];
        $locationData = $parameters['locationData'];
        $user = $parameters['user'];
        if ($user->created_matches >= self::MAX_FREE_MATCHES) {
            return [
                'success' => false,
                'max_free_matches_reached' => true,
                'message' => __('errors.maxMatchesReached'),
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
            $locationData['formatted_address']
        );

        $chat = (new EloquentChatService())->create();

        $match = (new EloquentMatchService())->create(
            $location->id,
            $when_play,
            $genre_id,
            $type_id,
            $num_players,
            $currency_id,
            $cost,
            $chat->id,
            $user->id
        );

        (new EloquentUserService())->addOneCreatedMatch($user);

        return response()->json([
            'success' => true,
            'chat' => $chat,
            'match' => $match,
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
            'currency_id' => $currencyId,
            'cost' => $cost,
            'num_players' => $numPlayers,
            'locationData' => $locationData,
            'user' => $request->user(),
        ];
    }

    public function getMatch($id)
    {

        $match = Match::find($id);
        $match->participants = $match->players()->with(['user'])->get()->pluck('user');
        $match->cost = number_format($match->cost, 2);

        return response()->json([
            'success' => true,
            'match' => $match,
            'location' => Location::find($match->location_id),
            'genre' => Genre::find($match->genre_id),
            'type' => Type::find($match->type_id),
            'currency' => Currency::find($match->currency_id),
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
        $currency_id = $parameters['currency_id'];
        $cost = $parameters['cost'];
        $num_players = $parameters['num_players'];
        $locationData = $parameters['locationData'];
        $user = $parameters['user'];
        $match = $parameters['match'];
        if ($match->owner_id !== $user->id) {
            return [
                'success' => false,
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
            $locationData['formatted_address']
        );

        $match = (new EloquentMatchService())->update(
            $match->id,
            $when_play,
            $genre_id,
            $type_id,
            $num_players,
            $currency_id,
            $cost,
        );

        return [
            'success' => true,
            'match' => $match,
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
            'matches' => $matches,
        ]);

    }

    public function getMatchesOffers(Request $request)
    {
        Log::info($request->header('Authorization'));
        $matches = Match::all();

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
                'matches' => [],
            ]);
        }

        $genre_id = $request->genre_id;
        $matches = $matches->where('genre_id', $genre_id);
        if ($matches->count() === 0) {
            return response()->json([
                'success' => true,
                'matches' => [],
            ]);
        }

        $matchTypes = json_decode($request->types, true);
        $matches = $matches->whereIn('type_id', $matchTypes);
        if ($matches->count() === 0) {
            return response()->json([
                'success' => true,
                'matches' => [],
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

        return response()->json([
            'success' => true,
            'matches' => $matches->values(),
        ]);
    }

    public function joinMatch(Request $request) {

        $user = $request->user();
        $match = Match::find($request->match_id);
        // comprobar genero del partido si es compatible con el genero del jugador
        if ($match->genre_id !== $user->genre->id && $match->genre_id !== self::MIX_GENRE_ID) {
            return response()->json([
                'success' => false,
                'matches' => [],
            ]);
        }
        // relacionar y devolver partidos del jugador
        $match->players()->syncWithoutDetaching($user->player->id);

        $matches = $this->returnAllMatches($request);

        return response()->json([
            'success' => true,
            'matches' => $matches->values(),
        ]);
    }

    public function leaveMatch(Request $request) {

        $player = $request->user()->player;
        $match = Match::find($request->match_id);
        $isInTheMatch = $match->players()->where('player_id', $player->id)->exists();
        if (!$isInTheMatch) {
            return response()->json([
                'success' => false,
            ]);
        }

        $match->players()->wherePivot('match_id', $match->id)->wherePivot('player_id', $player->id)->detach();

        $matches = $this->returnAllMatches($request);

        return response()->json([
            'success' => true,
            'matches' => $matches->values(),
        ]);
    }

    public function getMyMatches(Request $request)
    {
        $matches = $this->returnAllMatches($request);

        return response()->json([
            'success' => true,
            'matches' => $matches->values(),
        ]);
    }

    public function getMyCreatedMatches(Request $request)
    {
        $matches = $this->returnAllMatches($request);

        return response()->json([
            'success' => true,
            'matches' => $matches->values(),
        ]);
    }

    public function sendInvitationToUser(Request $request) {
        $userWhoInvite = $request->user();
        $userToInvite = User::find($request->user_id);
        $matchToInvite = Match::find($request->match_id);
        // comprobar genero del partido si es compatible con el genero del jugador
        if ($matchToInvite->genre_id !== $userToInvite->genre->id && $matchToInvite->genre_id !== self::MIX_GENRE_ID) {
            return response()->json([
                'success' => false,
            ]);
        }

        // TODO enviar notificacion al $userToInvite de $userWhoInvite para unirse al partido

        return response()->json([
            'success' => true,
        ]);
    }

    public function deleteMatch(Request $request) {

        $user = $request->user();
        $match = Match::find($request->match_id);
        $isInTheOwner = $match->owner_id === $user->id;
        if (!$isInTheOwner) {
            return response()->json([
                'success' => false,
            ]);
        }
        $match->players()->wherePivot('match_id', $match->id)->detach();

        $match->delete();

        $matches = $this->returnAllMatches($request);

        return response()->json([
            'success' => true,
            'matches' => $matches->values(),
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
        $matches = $matches->merge($request->user()->player->matches);
        $matches = $matches->sortBy(function ($match) {
            $match->location;
            $match->cost = number_format($match->cost, 2);
            $match->participants = $match->players->map(function ($player) use ($match) {
                return $player->user;
            });
            return $match->when_play;
        });
        return $matches;
    }

}
