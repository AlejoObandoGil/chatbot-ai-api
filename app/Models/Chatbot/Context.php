<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
