<?php

namespace App\Services;

use App\Contracts\MatchInterface;
use App\Contracts\MatchServiceInterface;

class MatchService implements MatchServiceInterface
{
    public function __construct(private readonly MatchInterface $matchRepository){}

    /**
     * @param $data
     * @return mixed
     */
    public function createMatch($data): mixed
    {
        return $this->matchRepository->createMatch($data);
    }

    /**
     * @param $weekId
     * @param $fetchAll
     * @return array
     */
    public function getMatch($weekId, $fetchAll): array
    {
        return $this->matchRepository->getMatch($weekId, $fetchAll);
    }



}