<?php

namespace App\Models;

use App\Enums\AttendanceType;
use App\Enums\SessionType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasUuids;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function casts(): array
    {
        return [
            'type' => SessionType::class,
            'status' => AttendanceType::class,
        ];
    }

    public function summary()
    {
        return $this->belongsTo(StudentAttendanceSummary::class, 'summary_id');
    }

    public function classMember()
    {
        return $this->belongsTo(ClassMember::class, 'class_member_id');
    }
}
