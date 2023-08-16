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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('week_id')->nullable();
            $table->unsignedBigInteger('home_team_id')->nullable()->comment('home team id');
            $table->unsignedBigInteger('away_team_id')->nullable()->comment('away team id');
            $table->integer('home_goal')->default(0);
            $table->integer('away_goal')->default(0);
            $table->boolean('played')->default(0);
            $table->timestamps();
            $table->index(['id', 'week_id', 'home_team_id', 'away_team_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches');
    }
};
