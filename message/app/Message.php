<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';

    protected $fillable = [
        'sender_id', 'receiver_id', 'content', 'conversation_id', 'is_encrypt', 'file_id', 'read'
    ];
    public function post()
    {
        return $this->belongsTo('App\Conversation');
    }
}
