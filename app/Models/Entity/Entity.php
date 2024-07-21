<?php

namespace App\Models\Entity;

use App\Models\Intent\Intent;
use App\Models\Chatbot\Chatbot;
use App\Models\User\ContactInformation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chatbot_id',
        'intent_id',
        'name',
        'datatype',
        'save'
    ];

    public function chatbot()
    {
        return $this->belongsTo(Chatbot::class);
    }

    public function intents()
    {
        return $this->hasMany(Intent::class);
    }

    public function contactInformation()
    {
        return $this->hasMany(ContactInformation::class);
    }
}
