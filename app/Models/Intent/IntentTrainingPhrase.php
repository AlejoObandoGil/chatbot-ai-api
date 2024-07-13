<?php

namespace App\Models\Intent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IntentTrainingPhrase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'intent_id',
        'phrase',
        'is_learning'
    ];

    public function intent()
    {
        return $this->belongsTo(Intent::class);
    }
}
