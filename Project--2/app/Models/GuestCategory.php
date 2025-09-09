<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuestCategory extends Model
{
    use SoftDeletes;

    protected $table = 'guest_categories';

    protected $fillable = ['name', 'description'];


    public function entries()
    {
        return $this->hasMany(guestbook_Model::class, 'guest_category_id');
    }
}
