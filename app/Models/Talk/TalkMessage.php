<?php

namespace App\Models\Talk;

use App\Models\Intent\Intent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TalkMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chatbot_talk_id',
        'intent_id',
        'sender',
        'message'
    ];

    public function talk()
    {
        return $this->belongsTo(Talk::class);
    }

    public function intent()
    {
        return $this->belongsTo(Intent::class);
    }
}
