<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendee extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'attendees';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'meeting_id',
        'user_id',
        'name',
        'division',
        'signature_path',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Disable updated_at since table doesn't have it
     */
    const UPDATED_AT = null;

    /**
     * Get the meeting that this attendee belongs to.
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }

    /**
     * Get the user associated with the attendee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if attendee has signature
     */
    public function hasSignature(): bool
    {
        return !empty($this->signature_path);
    }
}
