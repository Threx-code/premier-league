<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory;

    protected $guarded =[];

    public function weeks()
    {
        return $this->belongsTo(Week::class, 'week_id', 'id');
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'next_team_id', 'id');
    }
}
