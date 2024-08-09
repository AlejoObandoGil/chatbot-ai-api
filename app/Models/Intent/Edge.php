<?php

namespace App\Models\Intent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Edge extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'source',
        'source_handle',
        'target',
    ];

    /**
     * Get the source intent associated with the edge.
     */
    public function sourceIntent()
    {
        return $this->belongsTo(Intent::class, 'source');
    }

        /**
     * Get the target intent associated with the edge.
     */
    public function sourceHandleOption()
    {
        return $this->belongsTo(IntentOption::class, 'source_handle');
    }


    /**
     * Get the target intent associated with the edge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function targetIntent()
    {
        return $this->belongsTo(Intent::class, 'target');
    }

}
