<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JackpotBets extends Model
{
    protected $table = 'jackpot_bets';

    protected $fillable = ['game_id', 'user_id', 'sum', 'from', 'to', 'color'];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function game() {
        return $this->belongsTo('App\Models\Jackpot');
    }
}
