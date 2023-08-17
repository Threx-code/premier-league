<?php

use App\Http\Controllers\LeagueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::middleware([])->group(function(){
    Route::get('/', [LeagueController::class, 'getLeagueFixtures']);
    Route::prefix('premier/league/')->group(function () {
        Route::post('next-week', [LeagueController::class, 'getNextLeagueFixtures'])->name('premier.league.next-week');
        Route::post('fetch-all', [LeagueController::class, 'getAllLeagueFixtures'])->name('premier.league.fetch-all');
    });
});
