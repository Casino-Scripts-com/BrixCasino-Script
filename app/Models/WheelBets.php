<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WheelBets extends Model
{
    use HasFactory;

    protected $table = 'wheel_bets';

    protected $fillable = ['user_id', 'game_id', 'price', 'color', 'win', 'balance', 'win_sum'];
}
