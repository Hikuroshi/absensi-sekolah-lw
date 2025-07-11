<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'user_id',
        'type',
        'is_active'
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
