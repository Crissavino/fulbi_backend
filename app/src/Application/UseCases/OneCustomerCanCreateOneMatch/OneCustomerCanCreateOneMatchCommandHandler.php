<?php


namespace App\src\Application\UseCases\OneCustomerCanCreateOneMatch;


use App\src\Domain\Services\ChatService;
use App\src\Domain\Services\LocationService;
use App\src\Domain\Services\MatchService;
use App\src\Domain\Services\UserService;
use Illuminate\Support\Facades\Log;

class OneCustomerCanCreateOneMatchCommandHandler
{
    const MAX_FREE_MATCHES = 5;
    /**
     * @var LocationService
     */
    private $locationService;
    /**
     * @var ChatService
     */
    private $chatService;
    /**
     * @var MatchService
     */
    private $matchService;
    /**
     * @var UserService
     */
    private $userService;


    /**
     * OneCustomerCanCreateOneMatchCommandHandler constructor.
     * @param LocationService $locationService
     * @param ChatService $chatService
     * @param MatchService $matchService
     * @param UserService $userService
     */
    public function __construct(
        LocationService $locationService,
        ChatService $chatService,
        MatchService $matchService,
        UserService $userService
    )
    {
        $this->locationService = $locationService;
        $this->chatService = $chatService;
        $this->matchService = $matchService;
        $this->userService = $userService;
    }

    public function handle(OneCustomerCanCreateOneMatchCommand $command)
    {
        $parameters = $this->validateParametersFromCommand($command);
        if (!$parameters['success']) {
            return [
                'success' => false,
                'message' => $parameters['message']
            ];
        }
        $when_play = $parameters['when_play'];
        $genre_id = $parameters['genre_id'];
        $type_id = $parameters['type_id'];
        $cost = $parameters['cost'];
        $num_players = $parameters['num_players'];
        $locationData = $parameters['locationData'];
        $userId = $parameters['userId'];
        $user = $parameters['user'];
        if ($user->created_matches >= self::MAX_FREE_MATCHES) {
            return [
                'success' => false,
                'max_free_matches_reached' => true,
                'message' => __('errors.maxMatchesReached'),
            ];
        }

        $location = $this->locationService->create(
            $locationData->lat,
            $locationData->lng,
            $locationData->country,
            $locationData->country_code,
            $locationData->province,
            $locationData->province_code,
            $locationData->city,
            $locationData->place_id,
            $locationData->formatted_address
        );

        $chat = $this->chatService->create();

        $match = $this->matchService->create(
            $location->id,
            $when_play,
            $genre_id,
            $type_id,
            $num_players,
            $cost,
            $chat->id,
            $userId
        );

        $this->userService->addOneCreatedMatch($user);

        return [
            'success' => true,
            'chat' => $chat,
            'match' => $match,
        ];
    }

    private function validateParametersFromCommand(OneCustomerCanCreateOneMatchCommand $command): array
    {

        $whenPlay = $command->getWhenPlay();
        if (!$whenPlay) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }
        $whenPlay = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $whenPlay);

        $genreId = intval($command->getGenreId());
        if (!$genreId) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        $typeId = intval($command->getTypeId());
        if (!$typeId) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        $cost = floatval($command->getCost());
        if (!$cost) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        $numPlayers = intval($command->getNumPlayers());
        if (!$numPlayers) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        $locationData = json_decode($command->getLocationData());
        if (!$locationData) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        $userId = intval($command->getUserId());
        if (!$userId) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        $user = $this->userService->get($userId);
        if (!$user) {
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
            'cost' => $cost,
            'num_players' => $numPlayers,
            'locationData' => $locationData,
            'userId' => $userId,
            'user' => $user,
        ];
    }

}
