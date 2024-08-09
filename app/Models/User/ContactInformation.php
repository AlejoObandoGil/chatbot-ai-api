<?php

namespace App\Models\User;

use App\Models\Talk\Talk;
use App\Models\Entity\Entity;
use App\Models\Intent\Intent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactInformation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'intent_id',
        'talk_id',
        'status',
        'value'
    ];

    public function talk()
    {
        return $this->belongsTo(Talk::class);
    }

    /**
     * Retrieves the related Intent model for this object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function intent()
    {
        return $this->belongsTo(Intent::class);
    }
}
