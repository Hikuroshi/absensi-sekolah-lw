<?php

namespace App\Models;

use App\Enums\SubjectAttendanceStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SubjectAttendanceSummary extends Model
{
    use HasUuids;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected function casts(): array
    {
        return [
            'status' => SubjectAttendanceStatus::class,
        ];
    }

}
