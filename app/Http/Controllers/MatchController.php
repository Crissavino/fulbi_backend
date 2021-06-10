<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Match;
use App\Models\Sport;
use App\src\Application\UseCases\OneCustomerCanCreateOneMatch\OneCustomerCanCreateOneMatchCommand;
use App\src\Application\UseCases\OneCustomerCanCreateOneMatch\OneCustomerCanCreateOneMatchCommandHandler;
use App\src\Infrastructure\Request\StoreOneMatchRequest;
use App\src\Infrastructure\Services\EloquentChatService;
use App\src\Infrastructure\Services\EloquentLocationService;
use App\src\Infrastructure\Services\EloquentMatchService;
use App\src\Infrastructure\Services\EloquentUserService;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function showAll()
    {
        $matches = Match::all();
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

        return view('matches.create', [
            'apiKey' => $apiKey,
            'types' => $types,
            'genres' => $genres,
        ]);
    }

    public function store(Request $request)
    {

        $requestResponse = (new StoreOneMatchRequest($request))->__invoke();
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
}
