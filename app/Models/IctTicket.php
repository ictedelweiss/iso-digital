<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class IctTicket extends Model
{
    protected $table = 'ict_tickets';

    protected $fillable = [
        'user_id',
        'ticket_number',
        'subject',
        'category',
        'priority',
        'description',
        'attachment',
        'status',
        'assigned_to',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method untuk auto-generate ticket number dan track status changes
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = static::generateTicketNumber();
            }
        });

        static::updating(function ($ticket) {
            // Auto-set resolved_at ketika status berubah menjadi Resolved
            if ($ticket->isDirty('status') && $ticket->status === 'Resolved' && is_null($ticket->resolved_at)) {
                $ticket->resolved_at = now();
            }

            // Clear resolved_at jika status kembali ke Open atau In Progress
            if ($ticket->isDirty('status') && in_array($ticket->status, ['Open', 'In Progress'])) {
                $ticket->resolved_at = null;
            }
        });
    }

    /**
     * Generate nomor tiket otomatis: TKT-YYYYMM-XXXXX
     */
    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT-' . date('Ym') . '-';

        $lastTicket = static::where('ticket_number', 'LIKE', $prefix . '%')
            ->orderBy('ticket_number', 'desc')
            ->first();

        if (!$lastTicket) {
            return $prefix . '00001';
        }

        $lastNumber = (int)substr($lastTicket->ticket_number, -5);
        return $prefix . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Relasi: Pelapor (user yang membuat tiket)
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class , 'user_id');
    }

    /**
     * Relasi: Teknisi yang ditugaskan
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class , 'assigned_to');
    }

    /**
     * Hitung durasi penyelesaian dalam jam
     */
    public function getResolutionHoursAttribute(): ?float
    {
        if (!$this->resolved_at) {
            return null;
        }

        return round($this->created_at->diffInMinutes($this->resolved_at) / 60, 1);
    }

    /**
     * Cek apakah tiket melanggar SLA
     * High/Critical: > 24 jam
     * Medium/Low: > 48 jam
     */
    public function getIsSlaBreach(): bool
    {
        if (in_array($this->status, ['Resolved', 'Closed'])) {
            return false;
        }

        $hoursOpen = $this->created_at->diffInHours(now());

        if (in_array($this->priority, ['High', 'Critical'])) {
            return $hoursOpen > 24;
        }

        return $hoursOpen > 48;
    }

    /**
     * Get SLA limit in hours based on priority
     */
    public function getSlaLimitHoursAttribute(): int
    {
        return in_array($this->priority, ['High', 'Critical']) ? 24 : 48;
    }

    /**
     * Get hours elapsed since ticket creation
     */
    public function getHoursOpenAttribute(): float
    {
        $endTime = $this->resolved_at ?? now();
        return round($this->created_at->diffInMinutes($endTime) / 60, 1);
    }
}