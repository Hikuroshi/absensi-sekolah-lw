<?php

namespace App\Enums;

enum AttendanceType: string
{
    case HADIR = 'hadir';
    case IZIN = 'izin';
    case SAKIT = 'sakit';
    case ALPA = 'alpa';
    case PKL = 'pkl';

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
            self::HADIR => 'Hadir',
            self::IZIN => 'Izin',
            self::SAKIT => 'Sakit',
            self::ALPA => 'Alpa',
            self::PKL => 'Praktek Kerja Lapangan',
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