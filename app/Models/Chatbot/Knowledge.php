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
        'document',
        'file_openai_id',
        'vector_store_openai_id',
        'file_vector_openai_id',
        'content_file_openai_id',
        'is_learning',
        'link'
    ];

    public function chatbot()
    {
        return $this->belongsTo(Chatbot::class);
    }
}
