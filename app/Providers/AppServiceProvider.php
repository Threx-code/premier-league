<?php

namespace App\Providers;

use App\Contracts\LeagueInterface;
use App\Contracts\LeagueServiceInterface;
use App\Contracts\MatchInterface;
use App\Contracts\MatchServiceInterface;
use App\Contracts\SeasonFixtureInterface;
use App\Contracts\SeasonFixtureServiceInterface;
use App\Contracts\TeamInterface;
use App\Contracts\TeamServiceInterface;
use App\Repositories\LeagueRepository;
use App\Repositories\MatchRepository;
use App\Repositories\SeasonFixtureRepository;
use App\Repositories\TeamRepository;
use App\Services\LeagueService;
use App\Services\MatchService;
use App\Services\SeasonFixtureService;
use App\Services\TeamService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SeasonFixtureInterface::class, SeasonFixtureRepository::class);
        $this->app->bind(SeasonFixtureServiceInterface::class, SeasonFixtureService::class);
        $this->app->bind(LeagueInterface::class, LeagueRepository::class);
        $this->app->bind(LeagueServiceInterface::class, LeagueService::class);
        $this->app->bind(MatchInterface::class, MatchRepository::class);
        $this->app->bind(MatchServiceInterface::class, MatchService::class);

        $this->app->bind(TeamInterface::class, TeamRepository::class);
        $this->app->bind(TeamServiceInterface::class, TeamService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();
    }
}
