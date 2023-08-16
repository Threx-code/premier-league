<?php

namespace App\Services;

class TeamService
{
    /**
     * @param $homeScore
     * @param $awayScore
     * @param $home
     * @param $away
     * @return void
     */
    public function calculateScore($homeScore, $awayScore, $home, $away): void
    {
        if($homeScore > $awayScore){
            $this->goalsAndPoints($home, $awayScore, $homeScore, $away);
        }elseif($awayScore > $homeScore){
            $this->goalsAndPoints($away, $awayScore, $homeScore, $home);
        }else{
            $home->drawn += 1;
            $away->drawn += 1;
            $home->points += 1;
            $away->points += 1;
        }

        $home->played  = 1;
        $away->played = 1;
        $home->save();
        $away->save();
    }

    /**
     * @param $away
     * @param $awayScore
     * @param $homeScore
     * @param $home
     * @return void
     */
    public function goalsAndPoints($away, $awayScore, $homeScore, $home): void
    {
        $away->won += 1;
        $away->points += 3;
        $away->drawn += 0;
        $home->lost += 1;
        $home->drawn += 0;
        $away->goals_difference = abs($homeScore - $awayScore);
        $home->goals_difference = abs($homeScore - $awayScore);
    }

}