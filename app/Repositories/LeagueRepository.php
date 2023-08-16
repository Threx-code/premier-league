<?php

namespace App\Repositories;

use App\Contracts\LeagueInterface;
use App\Contracts\LeagueServiceInterface;

class LeagueRepository implements LeagueInterface
{
    public function __construct(private readonly LeagueServiceInterface $service){}

    /**
     * @param $numberOfTeam
     * @return void
     */
    public function processLeague($numberOfTeam): void
    {
        $this->service->processLeague($numberOfTeam);
    }

    /**
     * @param $weekId
     * @param $fetchAll
     * @return array
     */
    public function getLeagueFixtures($weekId, $fetchAll): array
    {
        return $this->service->getLeagueFixtures($weekId, $fetchAll);
    }

    /**
     * @param $weekId
     * @param $fetchAll
     * @return array
     */
    public function getPrediction($weekId, $fetchAll): array
    {
        return $this->service->getPrediction($weekId, $fetchAll);
    }

}