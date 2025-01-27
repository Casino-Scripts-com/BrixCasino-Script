<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jackpot extends Model
{
    protected $table = 'jackpot';

    protected $fillable = ['game_id', 'winner_id', 'winner_chance', 'winner_ticket', 'winner_sum', 'winner_username', 'winner_avatar', 'hash', 'price', 'status'];

    public static function getBank($room) {
        $game = self::orderBy('id', 'desc')->first();
        if(is_null($game)) return 0;
        return $game->price;
    }

    public function users() {
        return self::join('jackpot_bets', 'jackpot.game_id', '=', 'jackpot_bets.game_id')
            ->join('users', 'jackpot_bets.user_id', '=', 'users.id')
            ->where('jackpot.game_id', $this->game_id)
            ->groupBy('users.id')
            ->select('users.*')
            ->get();
    }

    public function winner() {
        return $this->belongsTo('App\Models\User');
    }

    public function bets() {
        return $this->hasMany('App\Models\JackpotBets');
    }
}
