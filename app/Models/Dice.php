<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dice extends Model
{
    use HasFactory;

    protected $table = 'dice';

    protected $fillable = ['id', 'user_id', 'bet', 'coef', 'type', 'win'];
}
