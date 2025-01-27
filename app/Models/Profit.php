<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profit extends Model
{
    use HasFactory;
    
    protected $table = 'profit';
	
	protected $fillable = ['game', 'sum'];

    protected $hidden = ['created_at', 'updated_at'];
}
