<?php

namespace App\Services;

use App\Contracts\LeagueServiceInterface;
use App\Contracts\MatchServiceInterface;
use App\Contracts\SeasonBaseInterface;
use App\Enums\TeamListEnum;
use App\Models\League;
use App\Models\Season;
use Illuminate\Support\Facades\DB;

class LeagueService implements LeagueServiceInterface
{
    /**
     * @var array
     */
    private array $teamsArray;

    /**
     * @var array
     */
    private array $teamIds;

    /**
     * @var Season
     */
    private Season $seasonData;

    /**
     * @param League $league
     * @param MatchServiceInterface $matches
     * @param SeasonBaseInterface $season
     * @param TeamService $teamService
     */
    public function __construct(
        private readonly League $league,
        private readonly MatchServiceInterface $matches,
        private readonly SeasonBaseInterface $season,
        private readonly TeamService $teamService
    ){}

    /**
     * @param $numberOfTeam
     * @return void
     */
    public function processLeague($numberOfTeam): void
    {
        $this->seasonData = $this->season->generateLeagueSeason();
        $this->teamsArray = array_slice(TeamListEnum::PREMIER_LEAGUE_TEAMS, 0, $numberOfTeam);
        $this->teamIds = $this->season->createTeam($this->teamsArray);
        $this->firstRounds();
        $this->secondRounds();
    }

    /**
     * @param string $round
     * @return array
     */
    private function generateLeague(string $round='first'): array
    {
        $teams = $this->teamsArray;
        $data = [];
        $length = sizeof($teams);
        $lastHalf = $length-1;

        $isHome=true;
        for ($t=0; $t <$length-1 ; $t++) {
            for ($i=0; $i < $length/2; $i++) {
                if($i==0){
                    if($isHome){
                        $data[] = $this->formatLeagues($round, $teams, $i,$lastHalf);
                        $isHome=false;
                    }else{
                        $data[] = $this->formatLeagues($round, $teams, $i,$lastHalf);
                        $isHome=true;
                    }
                }else{
                    if($i%2==0){
                        $data[] = $this->formatLeagues($round, $teams, $i,$lastHalf );
                    }else{
                        $data[] = $this->formatLeagues($round, $teams, $i,$lastHalf);
                    }
                }
            }
            array_splice( $teams, 1, 0, $teams[$length-1]);
            array_pop($teams);
        }

        return array_chunk($data, count($teams) / 2);
    }

    /**
     * @param $round
     * @param $teams
     * @param $i
     * @param $lastHalf
     * @return array
     */
    private function formatLeagues($round, $teams, $i, $lastHalf ): array
    {
        $teamIds = $this->teamIds;
        return ($round == 'first') ?
            [
                'played' => 1,
                'week_id' => 0,
                'team_id' => $teamIds[$teams[$i]],
                'next_team_id' => $teamIds[$teams[$lastHalf-$i]],
            ]:

            [
                'played' => 1,
                'week_id' => 0,
                'team_id' => $teamIds[$teams[$lastHalf-$i]],
                'next_team_id' => $teamIds[$teams[$i]],
            ];
    }

    /**
     * @return void
     */
    private function firstRounds(): void
    {
        $result = [];
        $firstRound = $this->generateLeague();
        foreach($firstRound as $key => $value){
            $result['week '. ++$key] = $value;
        }
        $this->insertMatches($result);
    }

    /**
     * @return void
     */
    private function secondRounds(): void
    {
        $result = [];
        $secondRound = $this->generateLeague('second');
        foreach($secondRound as $key => $value){
            $result['week '. count($secondRound) + (++$key)] = $value;
        }

        $this->insertMatches($result);
    }

    /**
     * @param $result
     * @return void
     */
    private function insertMatches($result): void
    {
        foreach ($result as $key => $values) {
            $weekIds[$key] = $this->season->weekGenerator($key, $this->seasonData->id)->id;
            foreach ($values as $value) {
                $value['week_id'] = $weekIds[$key];

                $played = $this->matches->createMatch([
                    'week_id' => $value['week_id'],
                    'home_team_id' => $value['team_id'],
                    'away_team_id' => $value['next_team_id'],
                    'home_goal' => rand(0, 5),
                    'away_goal' => rand(0, 5),
                    'played' => true
                ]);

                $leagueAlreadyCreated = $this->leagueAlreadyCreated($value['week_id'], $value['team_id'], $value['next_team_id']);
                if (!empty($leagueAlreadyCreated)) {
                    continue;
                }
                $home = $this->league::create($value);

                $nextTeam = $value['next_team_id'];
                $mainTeam = $value['team_id'];
                $value['team_id'] = $nextTeam;
                $value['next_team_id'] = $mainTeam;

                $leagueAlreadyCreated = $this->leagueAlreadyCreated($value['week_id'], $value['team_id'], $value['next_team_id']);

                if (!empty($leagueAlreadyCreated)) {
                    continue;
                }

                $away = $this->league::create($value);

                $this->teamService->calculateScore( $played->home_goal,  $played->away_goal, $home, $away);
            }
        }
    }

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