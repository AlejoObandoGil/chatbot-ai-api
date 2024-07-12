<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatbotTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'template_id',
        'type',
        'name',
        'description',
        'enabled',
        'script_embed'
    ];
}
