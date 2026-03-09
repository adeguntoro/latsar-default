<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordHistory extends Model
{
    protected $fillable = [
        'user_id',
        'password_hash',
        'changed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
