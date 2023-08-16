<?php

namespace App\Repositories;

use App\Contracts\MatchInterface;
use App\Contracts\MatchServiceInterface;

class MatchRepository implements MatchInterface
{
    public function __construct(private readonly MatchServiceInterface $service){}


    /**
     * @param $weekId
     * @param $fetchAll
     * @return array
     */
    public function getMatch($weekId, $fetchAll): array
    {
        return $this->service->getMatch($weekId, $fetchAll);
    }

}