<?php


namespace App\src\Application\UseCases\OneCustomerCanCreateOneMatch;


class OneCustomerCanCreateOneMatchCommand
{
    private $when_play;
    private $genre_id;
    private $type_id;
    private $cost;
    private $num_players;
    private $locationData;
    private $userId;
    private $currency_id;
    private $description;
    private $is_free_match;

    /**
     * OneCustomerCanCreateOneMatchCommand constructor.
     * @param $when_play
     * @param $genre_id
     * @param $type_id
     * @param $is_free_match
     * @param $currency_id
     * @param $cost
     * @param $num_players
     * @param $locationData
     * @param $description
     * @param $userId
     */
    public function __construct(
        $when_play,
        $genre_id,
        $type_id,
        $is_free_match,
        $currency_id,
        $cost,
        $num_players,
        $locationData,
        $description,
        $userId
    )
    {
        $this->when_play = $when_play;
        $this->genre_id = $genre_id;
        $this->type_id = $type_id;
        $this->cost = $cost;
        $this->num_players = $num_players;
        $this->locationData = $locationData;
        $this->userId = $userId;
        $this->currency_id = $currency_id;
        $this->description = $description;
        $this->is_free_match = $is_free_match;
    }

    /**
     * @return mixed
     */
    public function getIsFreeMatch()
    {
        return $this->is_free_match;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getCurrencyId()
    {
        return $this->currency_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return mixed
     */
    public function getWhenPlay()
    {
        return $this->when_play;
    }

    /**
     * @return mixed
     */
    public function getGenreId()
    {
        return $this->genre_id;
    }

    /**
     * @return mixed
     */
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @return mixed
     */
    public function getNumPlayers()
    {
        return $this->num_players;
    }

    /**
     * @return mixed
     */
    public function getLocationData()
    {
        return $this->locationData;
    }
}
