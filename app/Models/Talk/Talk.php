<?php

namespace App\Models\Talk;

use App\Models\Entity\Entity;
use App\Models\Talk\TalkMessage;
use App\Models\User\ContactInformation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Talk extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chatbot_id',
        'thread_openai_id',
        'thread_deleted',
        'started_at',
        'ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime:Y-m-d H:i:s',
        'ended_at' => 'datetime:Y-m-d H:i:s',
        'thread_deleted' => 'boolean'
    ];

    /**
     * Retrieves the messages associated with the current model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(TalkMessage::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Retrieves the contact information associated with the current model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contactInformation() {
        return $this->hasMany(ContactInformation::class);
    }
}
