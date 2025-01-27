<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mines extends Model
{
    use HasFactory;

    protected $table = 'mines';

    protected $fillable = [
        'user_id',
        'bombs',
        'bet',
        'mines',
        'click',
        'onOff',
        'result',
        'step',
        'win',
        'can_open'
      ];
}
