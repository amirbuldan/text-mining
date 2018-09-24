<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Twitter extends Model
{
    protected $fillable = [
        'username',
        'user_id',
        'tweet_id',
        'tweet',
        'sentiment'
    ];

    public $timestamps = false;
}
