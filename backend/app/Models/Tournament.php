<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'description',
        'start_date',
        'fee',
        'max_teams',
        'registered_teams',
        'status',
        'prize_pool',
        'format',
        'organizer',
        'banner_url'
    ];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'tournament_user')->withPivot('team_name')->withTimestamps();
    }

    public function matches()
    {
        return $this->hasMany(TournamentMatch::class);
    }
}
