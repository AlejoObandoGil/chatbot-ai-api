<?php

namespace App\Models\Talk;

use App\Models\Chatbot\Entity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Talk extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chatbot_id',
        'user_id',
        'started_at',
        'ended_at'
    ];

    public function talk()
    {
        return $this->belongsTo(Talk::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}
