<?php

namespace App\Console\Commands;

use App\Contracts\LeagueServiceInterface;
use Illuminate\Console\Command;

class LeagueFixture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'league {team_num}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command generates the league fixtures for the premier league season';

    public function __construct(private readonly LeagueServiceInterface $service)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $teamNum = $this->argument('team_num');
        if(($teamNum%2) != 0){
            echo sprintf('~~~~~~~ wrong number of team entered. The number should be even (e.g. %d) ~~~~~~~~~~', $teamNum + 1);
            exit();
        }
        if($teamNum > 20){
            echo sprintf('~~~~~~~ wrong number of team entered. The number should be be between %d and %d ~~~~~~~~~~', 2, 20);
            exit();
        }

       $response = $this->service->processLeague($teamNum);
        echo '~~~~~~~ League fixture completed ~~~~~~~~~~'."\n";
    }
}
