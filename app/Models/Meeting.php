<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'meetings';

    /**
     * The primary key type.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'title',
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
     * Get the attendees for this meeting.
     */
    public function attendees(): HasMany
    {
        return $this->hasMany(Attendee::class, 'meeting_id');
    }

    /**
     * Get attendee count
     */
    public function getAttendeeCountAttribute(): int
    {
        return $this->attendees()->count();
    }
}
