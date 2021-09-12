<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Genre;
use App\Models\Match;
use App\Models\Position;
use App\Models\Sport;
use App\Models\Type;
use App\src\Application\UseCases\OneCustomerCanCreateOneMatch\OneCustomerCanCreateOneMatchCommand;
use App\src\Application\UseCases\OneCustomerCanCreateOneMatch\OneCustomerCanCreateOneMatchCommandHandler;
use App\src\Application\UseCases\OneCustomerCanEditOneMatch\OneCustomerCanEditOneMatchCommand;
use App\src\Application\UseCases\OneCustomerCanEditOneMatch\OneCustomerCanEditOneMatchCommandHandler;
use App\src\Infrastructure\Request\CreateOneMatchRequest;
use App\src\Infrastructure\Request\EditOneMatchRequest;
use App\src\Infrastructure\Services\EloquentChatService;
use App\src\Infrastructure\Services\EloquentLocationService;
use App\src\Infrastructure\Services\EloquentMatchService;
use App\src\Infrastructure\Services\EloquentUserService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function showAll()
    {
        // $matches = Match::where('owner_id', auth()->user()->id)->simplePaginate(8);
        $matches = Match::simplePaginate(8);
        return view('matches.index', [
            'matches' => $matches
        ]);
    }

    public function add(Request $request)
    {
        $apiKey = env('GOOGLE_PLACE_API_KEY', 'AIzaSyCIW7l5SSIkF7JS1LOQAi6Rcsmm_10yJTQ');
        $mapBoxApiKey = env('MAP_BOX_API_KEY', 'pk.eyJ1IjoiY3Jpc3NhdmlubyIsImEiOiJja3JhejN4bHQxMWF2MnpwODA0dGFlemNzIn0.qWasazYjV_FaM7yD6pWAEw');

        $sport = Sport::where('name', 'futbol')->first();
        $types = $sport->types;
        $genres = Genre::all();
        $currencies = Currency::all();
        $user = $request->user();
        $userLocation = $user->player->location;
        $userLat = doubleval($userLocation->lat);
        $userLng = doubleval($userLocation->lng);

        return view('matches.create', [
            'apiKey' => $apiKey,
            'mapBoxApiKey' => $mapBoxApiKey,
            'types' => $types,
            'genres' => $genres,
            'userLat' => $userLat,
            'userLng' => $userLng,
            'currencies' => $currencies
        ]);
    }

    public function store(Request $request)
    {

        $requestResponse = (new CreateOneMatchRequest($request))->__invoke();
        if (!$requestResponse['success']) {
            return response()->json([
                'success' => false,
                'message' => __($requestResponse['message']),
            ]);
        }

        $command = (new OneCustomerCanCreateOneMatchCommand(
            $requestResponse['when_play'],
            $requestResponse['genre_id'],
            $requestResponse['type_id'],
            $requestResponse['is_free_match'],
            $requestResponse['currency_id'],
            $requestResponse['cost'],
            $requestResponse['num_players'],
            $requestResponse['locationData'],
            $requestResponse['description'],
            $requestResponse['userId']
        ));

        $handleResponse = (new OneCustomerCanCreateOneMatchCommandHandler(
            (new EloquentLocationService()),
            (new EloquentChatService()),
            (new EloquentMatchService()),
            (new EloquentUserService())
        ))->handle($command);
        if (!$handleResponse['success']) {
            return redirect()->route('matches.add')->withErrors([
                'error', __('errors.somethingHappened')
            ]);
        }

        return redirect()->route('matches.all')->with('message', __('general.messages.matchCreated'));
    }

    public function edit($id)
    {
        $apiKey = env('GOOGLE_PLACE_API_KEY', 'AIzaSyCIW7l5SSIkF7JS1LOQAi6Rcsmm_10yJTQ');
        $mapBoxApiKey = env('MAP_BOX_API_KEY', 'pk.eyJ1IjoiY3Jpc3NhdmlubyIsImEiOiJja3JhejN4bHQxMWF2MnpwODA0dGFlemNzIn0.qWasazYjV_FaM7yD6pWAEw');

        $match = Match::find($id);
        $sport = Sport::where('name', 'futbol')->first();
        $types = $sport->types;
        $genres = Genre::all();
        $currencies = Currency::all();
        $locationData = $match->location->toJson();

        return view('matches.update', [
            'apiKey' => $apiKey,
            'mapBoxApiKey' => $mapBoxApiKey,
            'types' => $types,
            'genres' => $genres,
            'match' => $match,
            'currencies' => $currencies,
            'locationData' => $locationData,
            'when_play' => Carbon::createFromFormat('Y-m-d H:i:s', $match->when_play)->format('d/m/Y H:i')
        ]);
    }

    public function update(Request $request, $id)
    {
        $requestResponse = (new EditOneMatchRequest($request))->__invoke();
        if (!$requestResponse['success']) {
            return response()->json([
                'success' => false,
                'message' => __($requestResponse['message'])
            ]);
        }

        $command = (new OneCustomerCanEditOneMatchCommand(
            $requestResponse['when_play'],
            $requestResponse['genre_id'],
            $requestResponse['type_id'],
            $requestResponse['is_free_match'],
            $requestResponse['currency_id'],
            $requestResponse['cost'],
            $requestResponse['num_players'],
            $requestResponse['locationData'],
            $requestResponse['userId'],
            $requestResponse['description'],
            $id
        ));

        $handleResponse = (new OneCustomerCanEditOneMatchCommandHandler(
            (new EloquentLocationService()),
            (new EloquentChatService()),
            (new EloquentMatchService()),
            (new EloquentUserService())
        ))->handle($command);
        if (!$handleResponse['success']) {
            return redirect()->route('matches.add')->withErrors([
                'error', __('errors.somethingHappened')
            ]);
        }

        return redirect()->route('matches.all')->with('message', __('general.messages.matchUpdated'));
    }

    public function delete($id)
    {
        $match = Match::find($id);

        $match->players()->detach();

        $match->delete();

        return redirect()->route('matches.all')->with('message', __('general.messages.matchDeleted'));

    }

    public function chat($id)
    {
        $match = Match::find($id);
        $chat = $match->chat;
        $messages = $match->chat->messages()->orderByDesc('created_at')->simplePaginate(10);

        return view('matches.chat', [
            'match' => $match,
            'chat' => $chat,
            'messages' => $messages
        ]);
    }

    public function enrolled($id)
    {
        $match = Match::find($id);
        $players = $match->players()->where('is_confirmed', true)->with(['user'])->where('is_existing_player', 0)->get();

        return view('matches.enrolled', [
            'match' => $match,
            'players' => $players
        ]);
    }
}
