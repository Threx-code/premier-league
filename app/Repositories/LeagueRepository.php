<?php

namespace App\Repositories;

use App\Contracts\LeagueInterface;
use App\Contracts\LeagueServiceInterface;
use App\Contracts\MatchServiceInterface;
use App\Contracts\SeasonFixtureServiceInterface;
use App\Contracts\TeamServiceInterface;
use App\Models\League;
use Illuminate\Support\Facades\DB;

class LeagueRepository implements LeagueInterface
{
    /**
     * @param League $league
     */
    public function __construct(private readonly League $league){}

    /**
     * @param $weekId
     * @param $teamId
     * @param $nextTeamId
     * @return mixed
     */
    public function leagueAlreadyCreated($weekId, $teamId, $nextTeamId): mixed
    {
        return $this->league::where([
            'week_id' => $weekId,
            'team_id' => $teamId,
            'next_team_id' => $nextTeamId
        ])->first();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createLeague($data): mixed
    {
        return $this->league::create($data);
    }

    /**
     * @param $weekId
     * @param $fetchAll
     * @return array
     */
    public function getLeagueFixtures($weekId, $fetchAll): array
    {
        $whereClause = !(empty($fetchAll)) ? " IS NOT NULL " : " = {$weekId}";
        $query = /** @lang text */
            "SELECT team1.id AS team_id,
               team1.name AS team_name,
               team2.name AS team_to_play,
               leagues.next_team_id AS team_to_play_id,
               leagues.id as league_id,
               leagues.week_id,
               leagues.played,
               leagues.won,
               leagues.drawn,
               leagues.lost,
               leagues.goals_difference,
               leagues.points,
               leagues.created_at,
               leagues.updated_at,
               weeks.name as current_week
            FROM teams AS team1
            INNER JOIN leagues  ON leagues.team_id = team1.id
            INNER JOIN teams AS team2 ON team2.id = leagues.next_team_id
            INNER JOIN weeks ON weeks.id = leagues.week_id 
            WHERE leagues.week_id {$whereClause}
            ORDER BY leagues.week_id, team1.id ASC;";
        return DB::select($query);


//        return $this->match->select(
//            'matches.id',
//            'matches.played',
//            'matches.week_id',
//            'matches.home_goal',
//            'matches.away_goal',
//            'week_id',
//            'home.name as home_team',
//            'home.logo as home_logo',
//            'away.logo as away_logo',
//            'away.name as away_team')
//            ->join('weeks', 'weeks.id', '=','matches.week_id')
//            ->join('teams as home', 'home.id', '=','matches.home')
//            ->join('teams as away', 'away.id', '=','matches.away')
//            ->orderBy('week_id','ASC')
//            ->get();
    }

    /**
     * @param $weekId
     * @param $fetchAll
     * @return array
     */
    public function getPrediction($weekId, $fetchAll): array
    {
        $whereClause = !(empty($fetchAll)) ? " IS NOT NULL " : " = {$weekId}";
        $query = /** @lang text */
            "
            SELECT
            leagues.team_id,
            leagues.week_id,
            teams.name,
            weeks.name as week_name,
            coalesce(ROUND(SUM(leagues.won + leagues.lost + leagues.goals_difference + leagues.points)::decimal / subq.total_points * 100, 2)) AS prediction
            FROM
            leagues AS leagues
            INNER JOIN teams AS teams ON teams.id = leagues.team_id
            INNER JOIN weeks ON weeks.id = leagues.week_id
            INNER JOIN (
                            SELECT
                                week_id,
                                coalesce(SUM(won + lost + goals_difference + points)) AS total_points
                            FROM leagues
                            WHERE week_id {$whereClause}
                            GROUP BY week_id
                        ) AS subq ON subq.week_id = leagues.week_id
            WHERE leagues.week_id {$whereClause}
            GROUP BY leagues.team_id, leagues.week_id, teams.name, subq.total_points, week_name
            ORDER BY prediction DESC;";

        return DB::select($query);
    }

}