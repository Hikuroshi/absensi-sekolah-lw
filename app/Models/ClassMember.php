<?php

namespace App\Models;

use App\Enums\ClassRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClassMember extends Pivot
{
    use HasUuids, HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function casts(): array
    {
        return [
            'role' => ClassRole::class,
        ];
    }

    public function scopeSearch($query, $search)
    {
        $query->when($search, function ($query) use ($search) {
            $query->whereAny(['role'], 'like', '%' . $search . '%')
                ->orWhereHas('classroom', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
        });
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
