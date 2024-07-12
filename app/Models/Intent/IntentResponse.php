<?php

namespace App\Models\Intent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntentResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'intent_id',
        'response'
    ];

    public function intent()
    {
        return $this->belongsTo(Intent::class);
    }
}
