<?php

namespace App\Http\Controllers;

use App\Contracts\LeagueInterface;
use App\Contracts\MatchInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LeagueController extends Controller
{
    public function __construct(private readonly LeagueInterface $league, private readonly MatchInterface $match){}

    /**
     * @param Request $request
     * @return array
     */
    public function fetchLeagueFixtures(Request $request): array
    {
        $weekId = $request->week_id ?? 1;
        $fetchAll = !empty($request->fetch_all);
        $leagues = $this->league->getLeagueFixtures($weekId, $fetchAll);
        $matches = $this->match->getMatch($weekId, $fetchAll);
        $predictions = $this->league->getPrediction($weekId, $fetchAll);
        return [
            'leagues' => $leagues,
            'matches' => $matches,
            'predictions' => $predictions
        ];
    }

    public function getLeagueFixtures(Request $request): Factory|View|Application
    {
        $data = $this->fetchLeagueFixtures($request);
        $leagues = $data['leagues'];
        $matches = $data['matches'];
        $predictions = $data['predictions'];
        return view('leagues.index', compact('leagues', 'matches', 'predictions'));
    }

    public function getNextLeagueFixtures(Request $request): Factory|View|Application
    {
        $data = $this->fetchLeagueFixtures($request);
        $leagues = $data['leagues'];
        $matches = $data['matches'];
        $predictions = $data['predictions'];
        return view('leagues.leagues', compact('leagues', 'matches', 'predictions'));
    }

    public function getAllLeagueFixtures(Request $request, $responses =[])
    {
        $data = $this->fetchLeagueFixtures($request);

        foreach($data['leagues'] as $league){
            $responses[$league->week_id]['leagues'][] = $league;
        }

        foreach($data['matches'] as $match){
            $responses[$match->week_id]['matches'][] = $match;
        }

        foreach($data['predictions'] as $prediction){
            $responses[$prediction->week_id]['predictions'][] = $prediction;
        }


        return view('leagues.fetch-all', compact('responses'));
    }

}
