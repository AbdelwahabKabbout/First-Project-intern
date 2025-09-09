<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class permission extends Model
{

    protected $table = 'permissions';
    protected $fillable = ['description'];
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permissions')
            ->withTimestamps();
    }
}
