<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasUuids, HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function scopeSearch($query, $search)
    {
        $query->when($search, function ($query) use ($search) {
            $query->whereAny(['name', 'year'], 'like', '%' . $search . '%');
        });
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'class_member')->withPivot('role')->using(ClassMember::class);
    }
}
