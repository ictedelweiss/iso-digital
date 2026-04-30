<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DivisionCoordinator extends Model
{
    protected $fillable = [
        'department',
        'user_id',
        'coordinator_name',
        'coordinator_email',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
