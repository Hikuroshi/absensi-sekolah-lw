<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case KETUA_KELAS = 'ketua_kelas';
    case GURU = 'guru';
    case WALI_KELAS = 'wali_kelas';
    case SISWA = 'siswa';

    public static function values(array $except = []): array
    {
        $cases = self::cases();

        if (!empty($except)) {
            $cases = array_filter($cases, fn($case) => !in_array($case->value, $except));
        }

        return array_column($cases, 'value');
    }

    public static function names(array $except = []): array
    {
        $cases = self::cases();

        if (!empty($except)) {
            $cases = array_filter($cases, fn($case) => !in_array($case->value, $except));
        }

        return array_column($cases, 'name');
    }

    public static function toArray(array $except = []): array
    {
        return array_combine(self::names($except), self::values($except));
    }

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::KETUA_KELAS => 'Ketua Kelas',
            self::GURU => 'Guru',
            self::WALI_KELAS => 'Wali Kelas',
            self::SISWA => 'Siswa',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::ADMIN => 'bg-purple-600/20 text-purple-300',
            self::KETUA_KELAS => 'bg-pink-600/20 text-pink-300',
            self::GURU => 'bg-blue-600/20 text-blue-300',
            self::WALI_KELAS => 'bg-orange-600/20 text-orange-300',
            self::SISWA => 'bg-green-600/20 text-green-300',
        };
    }

    public static function options(array $except = []): array
    {
        $cases = self::cases();

        if (!empty($except)) {
            $cases = array_filter($cases, fn($case) => !in_array($case->value, $except));
        }

        return array_map(
            fn(self $item) => (object) [
                'value' => $item->value,
                'label' => $item->label(),
                'class' => $item->badgeClasses(),
            ],
            $cases
        );
    }
}