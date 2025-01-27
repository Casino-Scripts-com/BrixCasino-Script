<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocodes extends Model
{
    use HasFactory;

    protected $table = 'promocodes';

    protected $fillable = ['type', 'status', 'sum', 'activate', 'activate_limit', 'name'];
}
