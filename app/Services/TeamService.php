<?php

namespace App\Services;

use App\Contracts\TeamInterface;
use App\Contracts\TeamServiceInterface;

class TeamService implements TeamServiceInterface
{
    public function __construct(private readonly TeamInterface $teamRepository){}
    /**
     * @param $homeScore
     * @param $awayScore
     * @param $home
     * @param $away
     * @return void
     */
    public function calculateScore($homeScore, $awayScore, $home, $away): void
    {
        $this->teamRepository->calculateScore($homeScore, $awayScore, $home, $away);
    }

}