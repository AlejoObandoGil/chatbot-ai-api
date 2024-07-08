<?php

namespace App\Models\Chatbot;

use App\Models\Chatbot\Context;
use App\Models\Chatbot\IntentResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Intent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chatbot_id',
        'name',
        'description'
    ];

    public function chatbot()
    {
        return $this->belongsTo(Chatbot::class);
    }

    public function responses()
    {
        return $this->hasMany(IntentResponse::class);
    }

    public function contexts()
    {
        return $this->hasMany(Context::class);
    }
}
