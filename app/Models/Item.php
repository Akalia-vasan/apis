<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'body', 'due_date', 'attachment', 'reminder', 'status'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

}
