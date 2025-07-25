<?php

namespace App\Enums;

enum SessionType: string
{
    case MASUK = 'masuk';
    case KELUAR = 'keluar';

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
            self::MASUK => 'Masuk',
            self::KELUAR => 'Keluar',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $item) => (object) ['value' => $item->value, 'label' => $item->label()],
            self::cases()
        );
    }

    public function opposite(): self
    {
        return match ($this) {
            self::MASUK => self::KELUAR,
            self::KELUAR => self::MASUK,
        };
    }
}