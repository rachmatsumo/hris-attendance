<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFcmToken extends Model
{
    protected $tablle = 'user_fcm_tokens';

    protected $fillable = [
        'user_id',
        'fcm_token',
        'device',
    ];
}
