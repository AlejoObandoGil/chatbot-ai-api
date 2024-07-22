<?php

namespace App\Models\Entity;

use App\Models\Entity\Entity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EntityValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'entity_id',
        'value',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}
