<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     * Maps to existing 'admins' table
     */
    protected $table = 'admins';

    /**
     * Disable updated_at since table doesn't have it
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username',
        'password_hash',
        'role',
        'access_permissions',
        'ms_id',
        'ms_email',
        'display_name',
        'division',
        'signature_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'access_permissions' => 'array',
    ];

    /**
     * Get the password attribute name for authentication.
     * This maps Laravel's expected 'password' to our 'password_hash' column
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    /**
     * Get the email for login
     * Uses ms_email if available, otherwise generates from username
     */
    public function getEmailAttribute(): string
    {
        return $this->ms_email ?: $this->username . '@edelweiss.sch.id';
    }

    /**
     * Get the column name for the "email" unique identifier.
     * This tells Laravel Auth to use ms_email instead of email
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->ms_email;
    }

    /**
     * Scope to find user by credentials for Filament login
     */
    public function scopeFindForFilament($query, string $email)
    {
        return $query->where('ms_email', $email)
            ->orWhere('username', $email)
            ->orWhere('username', explode('@', $email)[0]);
    }

    /**
     * Get the name for Filament display
     */
    public function getNameAttribute(): string
    {
        return $this->display_name ?: $this->username;
    }

    /**
     * Can access Filament panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // All admins can access
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Available access permissions that can be assigned by admin.
     */
    public static function permissionOptions(): array
    {
        return [
            'documents' => 'Documents',
            'attendance' => 'Attendance',
            'ict_helpdesk' => 'ICT Helpdesk',
            'asset_management' => 'Asset Management',
            'hrd_management' => 'HRD Management',
            'user_management' => 'User Management',
        ];
    }

    /**
     * Determine whether the user can access a specific feature area.
     * If no explicit permissions have been set yet, preserve legacy behavior.
     */
    public function hasAccessTo(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $permissions = $this->access_permissions;

        if ($permissions === null) {
            return true;
        }

        return in_array($permission, $permissions, true);
    }

    /**
     * Find user by email (for login)
     */
    public static function findByEmail(string $email): ?self
    {
        return static::where('ms_email', $email)
            ->orWhere('username', explode('@', $email)[0])
            ->first();
    }

    /**
     * Get the user's purchase requisitions
     */
    public function purchaseRequisitions(): HasMany
    {
        return $this->hasMany(PurchaseRequisition::class , 'created_by');
    }

    /**
     * Get the user's leave requests
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class , 'created_by');
    }

    /**
     * Get the user's handover forms
     */
    public function handoverForms(): HasMany
    {
        return $this->hasMany(HandoverForm::class , 'created_by');
    }

    /**
     * Get the user's ICT helpdesk tickets
     */
    public function ictTickets(): HasMany
    {
        return $this->hasMany(IctTicket::class , 'user_id');
    }
}
