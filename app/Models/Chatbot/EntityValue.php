<?php

namespace App\Models\Chatbot;

use App\Models\Chatbot\Entity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EntityValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'entitie_id',
        'value',
        'is_selectable'
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}
