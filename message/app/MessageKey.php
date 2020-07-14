<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageKey extends Model
{
    protected $table = 'message_keys';

    protected $fillable = [
        'contact_id', 'conversation_id', 'message_key_hash'
    ];
}
