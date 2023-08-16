<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('next_team_id');
            $table->unsignedBigInteger('week_id');
            $table->boolean('played');
            $table->integer('won');
            $table->integer('drawn');
            $table->integer('lost');
            $table->integer('goals_difference');
            $table->integer('points');
            $table->timestamps();
            $table->index(['id', 'team_id', 'next_team_id', 'week_id', 'played', 'won', 'drawn', 'lost', 'won', 'points']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leagues');
    }
};
