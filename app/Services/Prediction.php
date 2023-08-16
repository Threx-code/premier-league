<?php

namespace App\Services;

class Prediction
{
    public $predictions = array();


    /**
     * @param $team_id
     * @param $is_home
     * @return mixed
     */
    public function getTeamStrenght($team_id, $is_home)
    {
        return $this->teamStrength->where([['team_id','=',$team_id],['is_home','=',$is_home]])->get();
    }

    /**
     * @param $team_id
     * @param $is_home
     * @return int
     */
    public function createStrenght($team_id, $is_home)
    {

        foreach ($this->getTeamStrenght($team_id, $is_home) as $value){
            switch ($value->strength){
                case 'strong':
                    $this->result = rand(4,5);
                    break;
                case 'average':
                    $this->result = rand(2,3);
                    break;
                case 'weak' :
                    $this->result = rand(0,2);
                    break;
            }

            return $this->result;
        }
    }


}