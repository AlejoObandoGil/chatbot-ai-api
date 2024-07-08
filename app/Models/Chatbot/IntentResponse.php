<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntentResponse extends Model
{
    use HasFactory, SoftDeletes;

    public function intent()
    {
        return $this->belongsTo(Intent::class);
    }
}
