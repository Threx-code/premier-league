<?php

namespace App\Providers;

use App\Contracts\LeagueInterface;
use App\Contracts\LeagueServiceInterface;
use App\Contracts\MatchInterface;
use App\Contracts\MatchServiceInterface;
use App\Contracts\SeasonBaseInterface;
use App\Repositories\LeagueRepository;
use App\Repositories\MatchRepository;
use App\Services\LeagueService;
use App\Services\MatchService;
use App\Services\SeasonBaseService;
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
        $this->app->bind(SeasonBaseInterface::class, SeasonBaseService::class);
        $this->app->bind(LeagueInterface::class, LeagueRepository::class);
        $this->app->bind(LeagueServiceInterface::class, LeagueService::class);
        $this->app->bind(MatchInterface::class, MatchRepository::class);
        $this->app->bind(MatchServiceInterface::class, MatchService::class);
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
