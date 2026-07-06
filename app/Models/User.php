<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'is_active',
    ];

    public const ROLE_ADMIN = 'admin';
    public const ROLE_PETUGAS_KEPEGAWAIAN = 'petugas_kepegawaian';
    public const ROLE_PIMPINAN = 'pimpinan';

    public const ROLE_LABELS = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_PETUGAS_KEPEGAWAIAN => 'Petugas Kepegawaian',
        self::ROLE_PIMPINAN => 'Pimpinan/Ketua',
    ];

    public const ROLE_PERMISSIONS = [
        self::ROLE_ADMIN => ['*'],
        self::ROLE_PETUGAS_KEPEGAWAIAN => [
            'dashboard.view',
            'employees.manage',
            'training-history.manage',
            'assessments.manage',
            'training-needs.manage',
            'analysis.run',
            'reports.view',
            'master-data.manage',
        ],
        self::ROLE_PIMPINAN => [
            'dashboard.view',
            'employees.view',
            'training-needs.view',
            'training-needs.approve',
            'reports.view',
        ],
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $permissions = $this->permissions ?: (self::ROLE_PERMISSIONS[$this->role] ?? []);

        return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLE_LABELS[$this->role] ?? ucfirst(str_replace('_', ' ', (string) $this->role));
    }
}
