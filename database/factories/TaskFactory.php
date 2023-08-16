<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition()
    {
        return [

        ];
    }
}

//$data[$key]['id'] = $task->id;
//$data[$key]['title'] = ucwords($task->title);
//$data[$key]['task_file'] = $task->task_file;
//$data[$key]['description'] = $task->description;
//$data[$key]['created_by_id'] = $task->user_id;
//$data[$key]['completed'] = $task->completed;
//$data[$key]['date_deleted'] = $task->date_deleted;
//$data[$key]['created_at'] = Carbon::parse($task->created_at)->format('Y-m-d');
//$data[$key]['updated_at'] = Carbon::parse($task->updated_at)->format('Y-m-d');
