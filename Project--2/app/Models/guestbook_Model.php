<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class guestbook_Model extends Model
{
    use SoftDeletes;

    protected $table = 'guestbook_entries';

    protected $fillable = ['name', 'rate', 'email', 'message', 'tag', 'image', 'due_date', 'guest_category_id'];
    public function category()
    {
        return $this->belongsTo(GuestCategory::class, 'guest_category_id')
            ->withTrashed();
    }
}
