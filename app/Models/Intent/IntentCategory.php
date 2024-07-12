<?php

namespace App\Models\Intent;

use App\Models\Intent\Intent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IntentCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function intent()
    {
        return $this->HasMany(Intent::class);
    }
}
