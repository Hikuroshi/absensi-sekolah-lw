<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasUuids, HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function scopeSearch($query, $search)
    {
        $query->when($search, function ($query) use ($search) {
            $query->whereAny(['name', 'code', 'description'], 'like', '%' . $search . '%');
        });
    }
}
