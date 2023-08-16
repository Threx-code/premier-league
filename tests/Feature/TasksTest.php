<?php

namespace Tests\Feature;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TasksTest extends TestCase
{
    use RefreshDatabase;

    public function createTask()
    {
        $task = new Task();
        $task->title = fake()->jobTitle();
        $task->description = fake()->paragraph(4);
        $task->task_file = fake()->imageUrl;
        $task->user_id = random_int(1, 1000);
        $task->save();
        return $task;
    }


    public function test_should_fail_when_no_parameter_is_passed_to_store(): void
    {
        $response = $this->postJson(route("all_tasks"), []);
        $response->assertStatus(422)
            ->assertJson([
                "message" => "The title field is required. (and 1 more error)",
                "errors" => [
                    "title"=> [ "The title field is required."],
                    "description" => ["The description field is required."]
                ]
            ]);
    }

    public function test_should_fail_when_no_title_is_provided(): void
    {
        $response = $this->postJson(route("all_tasks"), [
            "description" => fake()->paragraph(2),
            'user_id' => random_int(100, 1000)
        ]);
        $response->assertStatus(422)
            ->assertJson([
                "message" => "The title field is required.",
                "errors" => [
                    "title"=> [ "The title field is required."],
                ]
            ]);
    }

    public function test_should_fail_when_title_provided_already_exist(): void
    {
        $task = $this->createTask();

        $response = $this->postJson(route("all_tasks"), [
            "title" => $task->title,
            "description" => fake()->paragraph(2),
            'user_id' => random_int(100, 1000)
        ]);
        $response->assertStatus(422)
            ->assertJson([
                "message" => "The title has already been taken.",
                "errors" => [
                    "title"=> [ "The title has already been taken."],
                ]
            ]);
    }

    public function test_should_fail_when_no_description_is_provided(): void
    {
        $response = $this->postJson(route("all_tasks"), [
                "title" => fake()->jobTitle(),
            ]);
        $response->assertStatus(422)
            ->assertJson([
                "message" => "The description field is required.",
                "errors" => [
                    "description" => ["The description field is required."]
                ]
            ]);
    }

    public function test_should_pass_when_parameters_provided(): void
    {
        $response = $this->postJson(route("all_tasks"), [
            "title" => fake()->jobTitle(),
            "description" => fake()->paragraph(5),
        ]);
        $response->assertStatus(201);
    }

    public function test_when_no_resource_found_for_task_id()
    {
        $response = $this->json("get", "api/v1/tasks/999999");
        $response->assertStatus(404);
    }

    public function test_when_resource_found_for_task_id()
    {
        $task = $this->createTask();
        $response = $this->json('get', 'api/v1/tasks/'.$task->id );
        $response->assertStatus(200);
    }

    public function test_fetching_all_resources_should_return_empty()
    {
        (new Task())->update([
            'date_deleted' => Carbon::now()
        ]);
        $response = $this->json('get', 'api/v1/tasks' );
        $response->assertStatus(404);
    }

    public function test_fetching_all_resources()
    {
        for($i =0; $i < 5; $i++) {
             $this->createTask();
        }
        $response = $this->json('get', 'api/v1/tasks' );
        $response->assertStatus(200);
    }

    public function test_updating_a_resource()
    {
        $task = $this->createTask();
        $response = $this->putJson('api/v1/tasks/' . $task->id, [
            $task->title => fake()->jobTitle(),
            $task->description => fake()->paragraph(2),
        ] );
        $response->assertStatus(200);
    }

    public function test_deleting_a_resource()
    {
        $task = $this->createTask();
        $response = $this->deleteJson('api/v1/tasks/' . $task->id);
        $response->assertStatus(204);
    }







//
//
//    public function test_making_api_post_request(): void
//    {
//        $task = Task::factory()->create(5);
//        $this->assertDatabaseCount('tasks', 5);
//    }
//
//
//    public function test_should_return_empty_array_where_there_is_no_record()
//    {
//        $this->taskModel()->date_deleted = Carbon::now();
//        $response = $this->getJson(route("all_tasks"), []);
//        $response->assertStatus(200)
//            ->assertJson([]);
//    }
//
//    public function test_should_fail_when_there_is_no_record_for_a_specific_id()
//    {
//
//        $tasks = factory(App\)
//        $this->taskModel();
//        for($i =0; $i < 10; ++$i){
//            $this->taskModel()->title =
//        }
//        $response = $this->getJson(route("all_tasks"), []);
//        $response->assertStatus(200)
//            ->assertJson([]);
//    }
}
