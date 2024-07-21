<?php

namespace App\Models\Chatbot;

use App\Models\Intent\Intent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Node extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'position',
        'data',
        'type'
    ];

    protected $casts = [
        'position' => 'json',
        'data' => 'json',
    ];

    /**
     * Retrieves the messages associated with the current model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Intent::class);
    }
}
