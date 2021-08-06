<?php


namespace App\src\Infrastructure\Request;


use Illuminate\Http\Request;

class CreateOneMatchRequest
{
    /**
     * @var Request
     */
    private $request;

    /**
     * CreateOneMatchRequest constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function __invoke()
    {
        if (!$this->request->when_play) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        if (!$this->request->genre_id) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        if (!$this->request->type_id) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        if (!$this->request->cost) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        if (!$this->request->currency_id) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        if (!$this->request->num_players) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        if (!$this->request->locationData) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        if (!$this->request->userId) {
            return [
                'success' => false,
                'message' => __('errors.missingParameter')
            ];
        }

        return [
            'success' => true,
            'when_play' => $this->request->when_play,
            'genre_id' => $this->request->genre_id,
            'type_id' => $this->request->type_id,
            'currency_id' => $this->request->currency_id,
            'cost' => $this->request->cost,
            'num_players' => $this->request->num_players,
            'locationData' => $this->request->locationData,
            'userId' => $this->request->userId,
        ];
    }
}
