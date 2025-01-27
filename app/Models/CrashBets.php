<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrashBets extends Model
{
    use HasFactory;

    protected $table = 'crash_bets';

    protected $fillable = ['user_id', 'round_id', 'price', 'withdraw', 'won', 'status'];
}
