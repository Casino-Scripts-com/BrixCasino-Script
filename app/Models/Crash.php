<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crash extends Model
{
    use HasFactory;

    protected $table = 'crash';

    protected $fillable = ['multiplier', 'profit', 'status', 'hash'];
}
