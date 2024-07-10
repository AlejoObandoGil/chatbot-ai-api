<?php

namespace App\Models\Chatbot;

use App\Models\User\User;
use App\Models\Talk\Talk;
use App\Models\Chatbot\Entity;
use App\Models\Chatbot\Intent;
use App\Models\Chatbot\Knowledge;
use App\Models\Chatbot\ChatbotConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chatbot extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function config()
    {
        return $this->hasOne(ChatbotConfig::class);
    }

    public function intents()
    {
        return $this->hasMany(Intent::class);
    }

    public function knowledge()
    {
        return $this->hasMany(Knowledge::class);
    }

    public function entities()
    {
        return $this->hasMany(Entity::class);
    }

    public function talks()
    {
        return $this->hasMany(Talk::class);
    }
}
