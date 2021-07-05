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
        dd(env('FCM_KEY'));

        $matches = Match::where('owner_id', auth()->user()->id)->simplePaginate(8);
        return view('matches.index', [
            'matches' => $matches
        ]);
    }

    public function add()
    {
        $apiKey = env('GOOGLE_PLACE_API_KEY', 'AIzaSyCIW7l5SSIkF7JS1LOQAi6Rcsmm_10yJTQ');

        $sport = Sport::where('name', 'futbol')->first();
        $types = $sport->types;
        $genres = Genre::all();
        $currencies = Currency::all();

        return view('matches.create', [
            'apiKey' => $apiKey,
            'types' => $types,
            'genres' => $genres,
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
            $requestResponse['currency_id'],
            $requestResponse['cost'],
            $requestResponse['num_players'],
            $requestResponse['locationData'],
            $requestResponse['userId'],
        ));

        $handleResponse = (new OneCustomerCanCreateOneMatchCommandHandler(
            (new EloquentLocationService()),
            (new EloquentChatService()),
            (new EloquentMatchService()),
            (new EloquentUserService()),
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

        $match = Match::find($id);
        $sport = Sport::where('name', 'futbol')->first();
        $types = $sport->types;
        $genres = Genre::all();
        $currencies = Currency::all();

        return view('matches.update', [
            'apiKey' => $apiKey,
            'types' => $types,
            'genres' => $genres,
            'match' => $match,
            'currencies' => $currencies,
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
            $requestResponse['currency_id'],
            $requestResponse['cost'],
            $requestResponse['num_players'],
            $requestResponse['locationData'],
            $requestResponse['userId'],
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
        $players = $match->players;

        return view('matches.enrolled', [
            'match' => $match,
            'players' => $players
        ]);
    }
}
