<?php


namespace App\src\Application\UseCases\OneCustomerCanEditOneMatch;


use App\src\Domain\Services\ChatService;
use App\src\Domain\Services\LocationService;
use App\src\Domain\Services\MatchService;
use App\src\Domain\Services\UserService;
use Carbon\Carbon;

class OneCustomerCanEditOneMatchCommandHandler
{
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
     * OneCustomerCanEditOneMatchCommandHandler constructor.
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

    public function handle(OneCustomerCanEditOneMatchCommand $command)
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
        $currency_id = $parameters['currency_id'];
        $cost = $parameters['cost'];
        $num_players = $parameters['num_players'];
        $locationData = $parameters['locationData'];
        $match = $parameters['match'];

        if ($match->location->formatted_address !== $locationData && !is_string($locationData)) {
            $this->locationService->update(
                $match->location->id,
                $locationData->lat,
                $locationData->lng,
                $locationData->country,
                $locationData->country_code,
                $locationData->province,
                $locationData->province_code,
                $locationData->city,
                $locationData->place_id,
                $locationData->formatted_address,
                false
            );
        }

        $match = $this->matchService->update(
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

    private function validateParametersFromCommand(OneCustomerCanEditOneMatchCommand $command): array
    {
        $matchId = intval($command->getMatchId());
        if (!$matchId) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }
        $match = $this->matchService->get($matchId);

        $whenPlay = $command->getWhenPlay();
        if (!$whenPlay) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }
        $whenPlay = Carbon::createFromFormat('d/m/Y H:i', $whenPlay);

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

        $currencyId = floatval($command->getCurrencyId());
        if (!$currencyId) {
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

        $locationData = json_decode($command->getLocationData()) ? json_decode($command->getLocationData()) : $command->getLocationData();
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
            'currency_id' => $currencyId,
            'cost' => $cost,
            'num_players' => $numPlayers,
            'locationData' => $locationData,
            'userId' => $userId,
            'user' => $user,
            'match' => $match
        ];
    }

}
