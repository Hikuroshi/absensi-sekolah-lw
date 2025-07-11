<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'subject_id',
        'user_id',
        'session_type',
        'status',
        'checked_at',
        'note'
    ];

    protected function casts(): array
    {
        return [
            'checked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function isHadir(): bool
    {
        return $this->status === 'hadir';
    }

    public function isIzin(): bool
    {
        return $this->status === 'izin';
    }

    public function isSakit(): bool
    {
        return $this->status === 'sakit';
    }

    public function isAlpa(): bool
    {
        return $this->status === 'alpa';
    }
}
