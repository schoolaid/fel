<?php

namespace Schoolaid\Fel\Enums;

enum CurrencyEnum: string
{
    case QUETZAL = 'GTQ';
    case DOLLAR = 'USD';

    public static function getDefault(): self
    {
        return self::QUETZAL;
    }

    public function getDescription(): string
    {
        return match($this) {
            self::QUETZAL => 'Quetzal',
            self::DOLLAR => 'US Dollar',
        };
    }
}