<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class log extends Model
{
    protected $table = 'logs';
    protected $fillable = ['action', 'details'];
}
