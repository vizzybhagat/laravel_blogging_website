<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Follow extends Model
{
    use HasFactory;

    public function userDoingTheFollowing() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userBeingFollowed() {
        return $this->belongsTo(User::class, 'followeduser');
    }
}
