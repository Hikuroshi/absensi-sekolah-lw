<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(
            Classes::class,
            'class_subject',
            'subject_id',
            'class_id'
        )->withTimestamps();
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'subject_teacher',
            'subject_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Guru pengampu per kelas untuk pelajaran ini
     */
    public function classTeachers()
    {
        return $this->belongsToMany(
            User::class,
            'schedules',
            'subject_id',
            'user_id'
        )->withPivot('class_id')->withTimestamps();
    }

    /**
     * Kelas-kelas di mana pelajaran ini diajarkan beserta guru pengampunya
     */
    public function classesWithTeachers()
    {
        return $this->belongsToMany(
            Classes::class,
            'schedules',
            'subject_id',
            'class_id'
        )->withPivot('user_id')->withTimestamps();
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->when($search, function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
        });
    }

    // Accessors
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucwords(strtolower($value)),
            set: fn ($value) => trim($value),
        );
    }

    protected function code(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? strtoupper($value) : null,
            set: fn ($value) => $value ? strtoupper(trim($value)) : null,
        );
    }

    public function canBeDeleted(): bool
    {
        return !$this->classes()->exists() && !$this->teachers()->exists();
    }
}