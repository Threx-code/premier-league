<?php

namespace App\Services;

use App\Contracts\MatchServiceInterface;
use App\Helpers\Helper;
use App\Models\League;
use App\Models\Matches;
use App\Models\Season;
use App\Models\Team;
use App\Models\Week;
use Illuminate\Support\Facades\DB;

class MatchService implements MatchServiceInterface
{
    public function __construct(private readonly Matches $matches){
    }

    public function createMatch($data)
    {
        return $this->matches::create($data);
    }

    /**
     * @param $weekId
     * @param $fetchAll
     * @return array
     */
    public function getMatch($weekId, $fetchAll): array
    {
        $whereClause = !(empty($fetchAll)) ? " IS NOT NULL ORDER BY week_id ASC" : " = {$weekId}";
        $query = /** @lang text */
            "SELECT team1.name AS home_team, 
            team2.name AS away_team, 
            matches.id as match_id,
            matches.home_goal,
            matches.away_goal,
            matches.home_team_id,
            matches.away_team_id,
            weeks.id AS week_id,
            weeks.name AS current_week
            FROM teams AS team1
            INNER JOIN matches ON team1.id = matches.home_team_id
            INNER JOIN teams AS team2 ON team2.id = matches.away_team_id
            INNER JOIN weeks ON weeks.id = matches.week_id
            WHERE weeks.id {$whereClause}";
        return DB::select($query);
    }

    public function updateMatch()
    {

    }

    public function getMatchById()
    {

    }

    public function getTeamByWeekId()
    {

    }

}