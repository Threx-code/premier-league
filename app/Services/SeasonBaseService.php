<?php

namespace App\Services;

use App\Contracts\SeasonBaseInterface;
use App\Models\Season;
use App\Models\Team;
use App\Models\Week;
use Carbon\Carbon;

class SeasonBaseService implements SeasonBaseInterface
{
    /**
     * @param Week $week
     * @param Season $season
     * @param Team $team
     */
    public function __construct(
        private readonly Week $week,
        private readonly Season $season,
        private readonly Team $team,
    ){
    }

    public function generateLeagueSeason(): mixed
    {
        $seasonYear = Carbon::now()->subYears(1)->format('Y') . '-' . Carbon::now()->format('y');
        $seasonCount = $this->season::count();
        $season = $this->season::where([
            'season_year' => $seasonYear
        ])->first();
        return !empty($season) ? $season :
            $this->season::create([
                'name' => 'Season ' . ++$seasonCount,
                'season_year' => $seasonYear,
                'finished' => false
            ]);
    }


    /**
     * @param $week
     * @param $seasonId
     * @return mixed
     */
    public function weekGenerator($week, $seasonId): mixed
    {
        $teamExist = $this->week::where('name', mb_strtolower($week))->first();
        return !empty($teamExist) ? $teamExist :
            $this->week::create([
                'name' => mb_strtolower($week),
                'season_id' => $seasonId
            ]);
    }

    /**
     * @param $teams
     * @return array
     */
    public function createTeam($teams): array
    {
        $response = [];
        foreach($teams as $team){
            $teamExist = $this->team::where('name', mb_strtolower($team))->first();
            if($teamExist){
                $response[$team] = $teamExist->id;
                continue;
            }
            $response[$team] = $this->team::create([
                'name' => mb_strtolower($team)
            ])->id;
        }

        return $response;
    }

}