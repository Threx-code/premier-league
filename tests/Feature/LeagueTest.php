<?php

namespace Tests\Feature;

use App\Models\League;
use App\Models\Matches;
use App\Models\Team;
use App\Models\Week;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeagueTest extends TestCase
{
    public function team()
    {
        return Team::all()->random(1)->first();
    }

    public function getMatches($weekId)
    {
        return Matches::where('week_id', $weekId)->first();
    }

    public function leagues($weekId)
    {
        return League::where('week_id', $weekId)->first();
    }

    public function week($weekId)
    {
        return Week::where('id', $weekId)->first();
    }
    const PREFIX = 'premier/league/';
    public function test_application_return_a_successful_response()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_if_view_contains_team_name()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee($this->team()->name);
    }

    public function test_if_view_contains_team_week()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee($this->week(1)->name);
    }

    public function test_next_week_endpoint_should_failed_when_wrong_method_is_passed()
    {
        $response = $this->get(route('premier.league.next-week'));
        $response->assertStatus(405);
    }


    public function test_next_week_endpoint_should_failed_response_when_no_parameter_is_passed()
    {
        $response = $this->postJson(route('premier.league.next-week'), []);
        $response->assertUnprocessable();
        $response->assertJson([
            "message" => "The week id field is required.",
            "errors" => [
                "week_id" => [
                    "The week id field is required."
                ]
            ]
        ]);
    }

    public function test_next_week_endpoint_should_failed_response_when_week_id_does_not_exist()
    {
        $response = $this->postJson(route('premier.league.next-week'), [
            'week_id' => League::count() + 999
        ]);
        $response->assertUnprocessable();
        $response->assertJson([
            "message" => "The selected week id is invalid.",
            "errors" => [
                "week_id" => [
                    "The selected week id is invalid."
                ]
            ]
        ]);
    }

    public function test_next_week_endpoint_return_a_successful_response_when_right_parameter_is_passed()
    {
        $response = $this->post(route('premier.league.next-week'), [
            'week_id' => 2
        ]);
        $response->assertStatus(200);
    }

    public function test_play_all_endpoint_should_failed_when_wrong_method_is_passed()
    {
        $response = $this->get(route('premier.league.fetch-all'));
        $response->assertStatus(405);
    }

    public function test_play_all_endpoint_should_failed_response_when_no_parameter_is_passed()
    {
        $response = $this->postJson(route('premier.league.fetch-all'), []);
        $response->assertUnprocessable();
        $response->assertJson([
            "message" => "The fetch all field is required.",
            "errors" => [
                "fetch_all" => [
                    "The fetch all field is required."
                ]
            ]
        ]);
    }

    public function test_play_all_endpoint_return_a_successful_response_when_right_parameter_is_passed()
    {
        $response = $this->post(route('premier.league.fetch-all'), [
            'fetch_all' => true
        ]);
        $response->assertStatus(200);
    }



}
