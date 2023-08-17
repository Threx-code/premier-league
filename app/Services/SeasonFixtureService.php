<?php

namespace App\Services;

use App\Contracts\SeasonFixtureInterface;
use App\Contracts\SeasonFixtureServiceInterface;

class SeasonFixtureService implements SeasonFixtureServiceInterface
{
    /**
     * @param SeasonFixtureInterface $seasonFixtureRepository
     */
    public function __construct(private readonly SeasonFixtureInterface $seasonFixtureRepository){}

    /**
     * @return mixed
     */
    public function generateLeagueSeason(): mixed
    {
        return $this->seasonFixtureRepository->generateLeagueSeason();
    }

    /**
     * @param $week
     * @param $seasonId
     * @return mixed
     */
    public function weekGenerator($week, $seasonId): mixed
    {
        return $this->seasonFixtureRepository->weekGenerator($week, $seasonId);
    }

    /**
     * @param $teams
     * @return array
     */
    public function createTeam($teams): array
    {
        return $this->seasonFixtureRepository->createTeam($teams);
    }

}