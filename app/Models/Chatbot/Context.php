<?php

namespace App\Models\Chatbot;

use App\Models\Intent\Intent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Context extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'intent_id',
        'name',
        'lifespan'
    ];

    public function intent()
    {
        return $this->belongsTo(Intent::class);
    }
}
