<?php

namespace App\Models\Chatbot;

use App\Models\Chatbot\Chatbot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Knowledge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chatbot_id',
        'content',
        'is_learning',
        'link'
    ];

    public function chatbot()
    {
        return $this->belongsTo(Chatbot::class);
    }
}
