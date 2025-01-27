<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wheel extends Model
{
    use HasFactory;

    protected $table = 'wheel';

    protected $fillable = ['winner_color', 'price', 'status'];
}
