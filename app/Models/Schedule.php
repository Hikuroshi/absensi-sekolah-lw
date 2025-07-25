<?php

namespace App\Models;

use App\Enums\Days;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasUuids, HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected function casts(): array
    {
        return [
            'day' => Days::class,
        ];
    }

    protected function startTime(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('H:i'),
            set: fn($value) => Carbon::createFromFormat('H:i', $value),
        );
    }

    protected function endTime(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('H:i'),
            set: fn($value) => Carbon::createFromFormat('H:i', $value),
        );
    }

    public function scopeSearch($query, $search)
    {
        $query->when($search, function ($query) use ($search) {
            $query->whereAny(['day', 'start_time', 'end_time'], 'like', '%' . $search . '%')
                ->orWhereHas('classroom', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('subject', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('teacher', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
        });
    }

    public function getIsSubjectAttendedTodayAttribute()
    {
        return SubjectAttendanceSummary::where('schedule_id', $this->id)
            ->whereToday('created_at')
            ->exists();
    }

    public function getIsTeacherAttendedTodayAttribute()
    {
        return TeacherAttendance::where('schedule_id', $this->id)
            ->whereToday('created_at')
            ->exists();
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
