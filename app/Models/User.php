<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;

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
        'id_number',
        'phone',
        'address',
        'is_active',
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
        ];
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah ketua kelas
     */
    public function isKetuaKelas()
    {
        return $this->role === 'ketua_kelas';
    }

    /**
     * Cek apakah user adalah guru
     */
    public function isGuru()
    {
        return $this->role === 'guru';
    }

    /**
     * Cek apakah user adalah siswa
     */
    public function isSiswa()
    {
        return $this->role === 'siswa';
    }

    /**
     * Relasi user dengan kelas (banyak ke banyak, dengan pivot role dan is_active)
     */
    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_members', 'user_id', 'class_id')
            ->withPivot('role', 'is_active')
            ->withTimestamps();
    }

    /**
     * Helper: kelas aktif yang sedang ditempati/diajar/dipimpin user sesuai role
     */
    public function activeClass(string $role = null)
    {
        $query = $this->classes()->wherePivot('is_active', true);
        if ($role) {
            $query->wherePivot('role', $role);
        }
        return $query->first();
    }

    /**
     * Helper: semua riwayat kelas user sesuai role (jika ingin)
     */
    public function classHistory(string $role = null)
    {
        $query = $this->classes();
        if ($role) {
            $query->wherePivot('role', $role);
        }
        return $query->get();
    }

    /**
     * Relasi user (guru) dengan pelajaran yang diampu
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'user_id', 'subject_id');
    }

    /**
     * Kelas-mata pelajaran yang diampu oleh guru ini
     */
    public function teachingAssignments()
    {
        return $this->belongsToMany(
            Classes::class,
            'schedules',
            'user_id',
            'class_id'
        )->withPivot('subject_id')->withTimestamps();
    }

    /**
     * Pelajaran yang diampu oleh guru ini di kelas tertentu
     */
    public function teachingSubjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'schedules',
            'user_id',
            'subject_id'
        )->withPivot('class_id')->withTimestamps();
    }

    public function getRoleLabelAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->role));
    }

    public function getIdNumberLabelAttribute()
    {
        return match($this->role) {
            'guru' => 'NIP',
            'siswa', 'ketua_kelas' => 'NIS',
            'admin' => 'Nomor',
            default => 'Nomor'
        };
    }

    /**
     * Scope untuk pencarian user
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->when($search, function ($query) use ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('id_number', 'like', "%$search%")
                  ->orWhere('role', 'like', "%$search%")
                  ->orWhereHas('classes', function($classQuery) use ($search) {
                      $classQuery->where('name', 'like', "%$search%");
                  });
            });
        });
    }

    /**
     * Scope untuk filter berdasarkan role
     */
    public function scopeFilterByRole(Builder $query, string $role): Builder
    {
        return $query->when($role, function ($query) use ($role) {
            $query->where('role', $role);
        });
    }
}
