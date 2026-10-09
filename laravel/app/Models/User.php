<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const STUDENT = 'STUDENT';
    public const STAFF = 'STAFF';

    protected $fillable = [
        'name', 'email', 'password', 'role', 'staff_role', 'staff_tracks',
        'recovery_email', 'phone', 'grade', 'streak', 'points',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'staff_tracks' => 'array',
        ];
    }

    public function simRecords(): HasMany
    {
        return $this->hasMany(SimRecord::class)->latest('taken_on')->latest('id');
    }

    public function isStudent(): bool
    {
        return $this->role === self::STUDENT;
    }

    public function isStaff(): bool
    {
        return $this->role === self::STAFF;
    }

    /** SUPER_ADMIN | TEACHER — drives the RBAC matrix */
    public function staffRole(): string
    {
        return $this->staff_role ?: 'SUPER_ADMIN';
    }

    /** the three demo records every new student starts with (DEFAULT_PORTAL) */
    public function seedDefaultPortal(): void
    {
        foreach ([
            ['محاكي القدرات — تجربة تجريبية ١', 88, 22, 25, '2026-09-18'],
            ['محاكي التحصيلي — تجربة تجريبية ٢', 92, 23, 25, '2026-09-21'],
            ['قدرات — تجميعة المستوى المتقدم', 96, 24, 25, '2026-09-24'],
        ] as [$label, $score, $correct, $total, $date]) {
            $this->simRecords()->create(compact('label', 'score', 'correct', 'total') + ['taken_on' => $date]);
        }
    }
}
