<?php

namespace App\Models\Intent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntentOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'intent_id',
        'option',
    ];

    public function intent()
    {
        return $this->belongsTo(Intent::class);
    }

    public function edges()
    {
        return $this->hasOne(Edge::class, 'source_handle');
    }
}
