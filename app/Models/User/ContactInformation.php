<?php

namespace App\Models\User;

use App\Models\Talk\Talk;
use App\Models\Chatbot\Entity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactInformation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'entity_id',
        'value'
    ];

    public function talk()
    {
        return $this->belongsTo(Talk::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}
