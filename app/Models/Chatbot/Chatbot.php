<?php

namespace App\Models\Chatbot;

use App\Models\Talk\Talk;
use App\Models\User\User;
use App\Models\Intent\Edge;
use App\Models\Entity\Entity;
use App\Models\Intent\Intent;
use App\Models\Chatbot\Knowledge;
use App\Models\Chatbot\ChatbotConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chatbot extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'description',
        'type',
        'enabled',
        'assistant_openai_id',
        'temperature',
        'max_tokens',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Define the relationship between the Chatbot model and the ChatbotConfig model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function config()
    {
        return $this->hasOne(ChatbotConfig::class);
    }

    /**
     * Define the relationship between the Chatbot model and the Intent model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function intents()
    {
        return $this->hasMany(Intent::class);
    }

    /**
     * Define the relationship between the Chatbot model and the Edge model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function edges()
    {
        return $this->hasMany(Edge::class);
    }

    /**
     * Define the relationship between the Chatbot model and the Knowledge model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function knowledges()
    {
        return $this->HasMany(Knowledge::class);
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
