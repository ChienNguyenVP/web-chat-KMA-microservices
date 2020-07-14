<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $table = 'conversations';

    protected $casts = [
        'users' => 'array',
   ];

    protected $fillable = [
        'users', 'is_encrypt'
    ];

    public function messages()
    {
        return $this->hasMany('App\Message');
    }
}
