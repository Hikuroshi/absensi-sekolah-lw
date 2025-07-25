<?php

namespace App\Enums;

enum ClassRole: string
{
    case KETUA_KELAS = 'ketua_kelas';
    case WALI_KELAS = 'wali_kelas';
    case SISWA = 'siswa';

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
            self::KETUA_KELAS => 'Ketua Kelas',
            self::WALI_KELAS => 'Wali Kelas',
            self::SISWA => 'Siswa',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::KETUA_KELAS => 'bg-pink-600/20 text-pink-300',
            self::WALI_KELAS => 'bg-orange-600/20 text-orange-300',
            self::SISWA => 'bg-green-600/20 text-green-300',
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