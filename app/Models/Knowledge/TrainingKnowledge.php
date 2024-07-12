<?php

namespace App\Models\Knowledge;

use App\Models\Chatbot\Chatbot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingKnowledge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chatbot_id',
        'content'
    ];

    public function chatbot()
    {
        return $this->belongsTo(Chatbot::class);
    }
}
