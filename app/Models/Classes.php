<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $name
 * @property string $academic_year
 * @property int|null $wali_kelas_id
 * @property int|null $ketua_kelas_id
 * @property bool $is_active
 */
class Classes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'academic_year',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'class_members',
            'class_id',
            'user_id'
        )->withPivot('role', 'is_active');
    }

    // Helper: ambil wali kelas dari class_members (role = 'guru')
    public function waliKelas()
    {
        return $this->members()->wherePivot('role', 'guru')->first();
    }

    // Helper: ambil ketua kelas dari class_members (role = 'ketua_kelas')
    public function ketuaKelas()
    {
        return $this->members()->wherePivot('role', 'ketua_kelas')->wherePivot('is_active', true)->first();
    }

    /**
     * Guru pengampu per pelajaran di kelas ini
     */
    public function subjectTeachers()
    {
        return $this->belongsToMany(
            User::class,
            'schedules',
            'class_id',
            'user_id'
        )->withPivot('subject_id')->withTimestamps();
    }

    /**
     * Pelajaran yang diajar di kelas ini beserta guru pengampunya
     */
    public function subjectsWithTeachers()
    {
        return $this->belongsToMany(
            Subject::class,
            'schedules',
            'class_id',
            'subject_id'
        )->withPivot('user_id')->withTimestamps();
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'schedules', 'class_id', 'subject_id')->distinct();
    }

    public function schedules()
    {
        return $this->hasMany(\App\Models\Schedule::class, 'class_id');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('academic_year', 'like', "%$search%")
                ; // Hapus filter waliKelas & ketuaKelas karena bukan relasi Eloquent
            });
        });
    }
}
