<?php

namespace App\Models\Talk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TalkMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chatbot_talk_id',
        'sender',
        'message'
    ];

    public function talk()
    {
        return $this->belongsTo(Talk::class);
    }
}
