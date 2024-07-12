<?php

namespace App\Models\Intent;

use App\Models\Chatbot\Chatbot;
use App\Models\Chatbot\Context;
use App\Models\Intent\IntentCategory;
use App\Models\Intent\IntentResponse;
use Illuminate\Database\Eloquent\Model;
use App\Models\Intent\IntentTrainingPhrase;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Intent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chatbot_id',
        'parent_id',
        'name',
        'description'
    ];

    public function chatbot()
    {
        return $this->belongsTo(Chatbot::class);
    }

    public function category()
    {
        return $this->belongsTo(IntentCategory::class);
    }

    public function trainingPhrases()
    {
        return $this->hasMany(IntentTrainingPhrase::class);
    }

    public function responses()
    {
        return $this->hasMany(IntentResponse::class);
    }

    public function contexts()
    {
        return $this->hasMany(Context::class);
    }

    public function parent()
    {
        return $this->belongsTo(Intent::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Intent::class, 'parent_id');
    }
}
