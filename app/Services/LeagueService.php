<?php

namespace App\Services;

use App\Contracts\LeagueInterface;
use App\Contracts\LeagueServiceInterface;
use App\Contracts\MatchServiceInterface;
use App\Contracts\SeasonFixtureServiceInterface;
use App\Contracts\TeamServiceInterface;
use App\Enums\TeamListEnum;
use App\Models\Season;
use Illuminate\Http\Request;

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
     * @param LeagueInterface $leagueRepository
     * @param MatchServiceInterface $matches
     * @param SeasonFixtureServiceInterface $season
     * @param TeamServiceInterface $teamService
     */
    public function __construct(
        private readonly LeagueInterface $leagueRepository,
        private readonly MatchServiceInterface         $matches,
        private readonly SeasonFixtureServiceInterface $season,
        private readonly TeamServiceInterface          $teamService
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

                $leagueAlreadyCreated = $this->leagueRepository->leagueAlreadyCreated(
                    $value['week_id'],
                    $value['team_id'],
                    $value['next_team_id']
                );
                if (!empty($leagueAlreadyCreated)) {
                    continue;
                }
                $home = $this->leagueRepository->createLeague($value);

                $nextTeam = $value['next_team_id'];
                $mainTeam = $value['team_id'];
                $value['team_id'] = $nextTeam;
                $value['next_team_id'] = $mainTeam;

                $leagueAlreadyCreated = $this->leagueRepository->leagueAlreadyCreated(
                    $value['week_id'],
                    $value['team_id'],
                    $value['next_team_id']
                );

                if (!empty($leagueAlreadyCreated)) {
                    continue;
                }

                $away = $this->leagueRepository->createLeague($value);
                $this->teamService->calculateScore( $played->home_goal,  $played->away_goal, $home, $away);
            }
        }
    }


    /**
     * @param Request $request
     * @return array
     */
    public function fetchLeagueFixtures(Request $request): array
    {
        $weekId = $request->week_id ?? 1;
        $fetchAll = !empty($request->fetch_all);
        $leagues = $this->getLeagueFixtures($weekId, $fetchAll);
        $matches = $this->matches->getMatch($weekId, $fetchAll);
        $predictions = $this->getPrediction($weekId, $fetchAll);
        return [
            'leagues' => $leagues,
            'matches' => $matches,
            'predictions' => $predictions
        ];
    }


    /**
     * @param $weekId
     * @param $fetchAll
     * @return mixed
     */
    public function getLeagueFixtures($weekId, $fetchAll): mixed
    {
        return $this->leagueRepository->getLeagueFixtures($weekId, $fetchAll);
    }

    /**
     * @param $weekId
     * @param $fetchAll
     * @return mixed
     */
    public function getPrediction($weekId, $fetchAll): mixed
    {
        return $this->leagueRepository->getPrediction($weekId, $fetchAll);
    }

}