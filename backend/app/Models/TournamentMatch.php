<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentMatch extends Model
{
    protected $fillable = [
        'tournament_id',
        't1_name',
        't2_name',
        't1_score',
        't2_score',
        'round',
        'status'
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}
