<?php

namespace App\Http\Controllers;

use App\Contracts\LeagueServiceInterface;
use App\Http\Requests\NextLeagueFixtureRequest;
use App\Http\Requests\PlayAllLeagueRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LeagueController extends Controller
{
    public function __construct(private readonly LeagueServiceInterface $leagueService){}

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function getLeagueFixtures(Request $request): Factory|View|Application
    {
        $data = $this->leagueService->fetchLeagueFixtures($request);
        $leagues = $data['leagues'];
        $matches = $data['matches'];
        $predictions = $data['predictions'];
        return view('leagues.index', compact('leagues', 'matches', 'predictions'));
    }

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function getNextLeagueFixtures(NextLeagueFixtureRequest $request): Factory|View|Application
    {
        $data = $this->leagueService->fetchLeagueFixtures($request);
        $leagues = $data['leagues'];
        $matches = $data['matches'];
        $predictions = $data['predictions'];
        return view('leagues.leagues', compact('leagues', 'matches', 'predictions'));
    }

    /**
     * @param Request $request
     * @param array $responses
     * @return Factory|View|Application
     */
    public function getAllLeagueFixtures(PlayAllLeagueRequest $request, array $responses =[]): Factory|View|Application
    {
        $data = $this->leagueService->fetchLeagueFixtures($request);
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
