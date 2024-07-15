<?php

namespace App\Models\Intent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntentTransition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_intent_id',
        'child_intent_id',
    ];

    public function parentIntent()
    {
        return $this->belongsTo(Intent::class, 'parent_intent_id');
    }

    public function childIntent()
    {
        return $this->belongsTo(Intent::class, 'child_intent_id');
    }

    public function option()
    {
        return $this->belongsTo(IntentOption::class);
    }
}
