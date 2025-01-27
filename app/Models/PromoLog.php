<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoLog extends Model
{
    use HasFactory;

    protected $table = 'promo_log';

    protected $fillable = ['user_id', 'sum', 'name'];
}
