<?php

namespace App\Models\Chatbot;

use App\Models\User\ContactInformation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chatbot_id',
        'name',
        'type',
        'save'
    ];

    public function chatbot()
    {
        return $this->belongsTo(Chatbot::class);
    }

    public function contactInformation()
    {
        return $this->hasMany(ContactInformation::class);
    }
}
