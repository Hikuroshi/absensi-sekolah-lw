<?php

namespace App\Enums;

enum SubjectAttendanceStatus: string
{
    case BELUM_LENGKAP = 'belum lengkap';
    case SUDAH_LENGKAP = 'sudah lengkap';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function toArray(): array
    {
        return array_combine(self::names(), self::values());
    }

    public function label(): string
    {
        return match ($this) {
            self::BELUM_LENGKAP => 'Belum Lengkap',
            self::SUDAH_LENGKAP => 'Sudah Lengkap',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $item) => (object) ['value' => $item->value, 'label' => $item->label()],
            self::cases()
        );
    }
}