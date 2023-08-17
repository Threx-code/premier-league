<?php

namespace App\Repositories;

use App\Contracts\LeagueInterface;
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
     * @return mixed
     */
    public function getLeagueFixtures($weekId, $fetchAll): mixed
    {
        $whereRaw = !(empty($fetchAll)) ? "leagues.week_id IS NOT NULL " : "leagues.week_id = {$weekId}";
        return $this->league::select(
            'teams.id AS team_id',
            'teams.name AS team_name',
            'team2.name AS team_to_play',
            'leagues.next_team_id AS team_to_play_id',
            'leagues.id as league_id',
            'leagues.week_id',
            'leagues.played',
            'leagues.won',
            'leagues.drawn',
            'leagues.lost',
            'leagues.goals_difference',
            'leagues.points',
            'leagues.created_at',
            'leagues.updated_at',
            'weeks.name as current_week')
            ->join('teams', 'leagues.team_id', '=', 'teams.id')
            ->join('teams AS team2', 'team2.id', '=', 'leagues.next_team_id')
            ->join('weeks', 'weeks.id', '=', 'leagues.week_id')
            ->whereRaw($whereRaw)
            ->orderBy('leagues.week_id')
            ->orderBy('teams.id')
            ->get();
    }

    /**
     * @param $weekId
     * @param $fetchAll
     * @return mixed
     */
    public function getPrediction($weekId, $fetchAll): mixed
    {
        $whereRaw = !(empty($fetchAll)) ? "leagues.week_id IS NOT NULL " : "leagues.week_id = {$weekId}";
        return $this->league::select(
            'leagues.team_id',
            'leagues.week_id',
            'teams.name',
            'weeks.name as week_name')
            ->selectRaw('COALESCE(ROUND(SUM(leagues.won + leagues.lost + leagues.goals_difference + leagues.points)::decimal / subq.total_points * 100, 2)) AS prediction')
            ->join('teams', 'teams.id', '=', 'leagues.team_id')
            ->join('weeks', 'weeks.id', '=', 'leagues.week_id')
            ->join(DB::raw('(SELECT week_id, COALESCE(SUM(won + lost + goals_difference + points)) AS total_points FROM leagues WHERE '.$whereRaw.' GROUP BY week_id) AS subq'), 'subq.week_id', '=', 'leagues.week_id')
            ->whereRaw($whereRaw)
            ->groupBy('leagues.team_id', 'leagues.week_id', 'teams.name', 'subq.total_points', 'week_name')
            ->orderBy('prediction', 'DESC')
            ->get();
    }

}